<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
/*
 * File:
 *      VBook.php
 *
 * Project:
 *      vCard PHP <http://vcardphp.sourceforge.net>
 *
 * Author:
 *      Ravi Rokkam <ravi@sqlfusion.com>
 *
 */

require("class/VCard.class.php");


class VBook extends DataObject {

	private $iduser;
	public $errMsg = "";
	private $contact;
	private $imported = false;

    function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_CONTACT);
		}
		$this->iduser = $_SESSION['do_User']->iduser;
    }

	function eventVCardImport(EventControler $evtcl) {

        $msg = "";
		$goto = $evtcl->getParam("goto");
		$uploaded_file = $_FILES['fields']['name']['contact_vcard'];

		if($uploaded_file) {
	
			$target_path = 'files/' . $uploaded_file;
	
			if(!move_uploaded_file($_FILES['fields']['tmp_name']['contact_vcard'], $target_path)) {
				$msg = "There was an error uploading the file, please try again!";
			} else {

				chmod($target_path, 0755);
		
				$_SESSION['vcard_file'] = $uploaded_file;
				$_SESSION['import_tag'] = trim($evtcl->fields['import_tag']);
		
				$retVal = $this->print_vcard_address_book($target_path,'ofuz vCard Import','','','');
				if($retVal) {
					$msg = "Your vCard contacts have been imported successfully.";
				} else {
					$msg = $this->errMsg;
				}
			}

		} else {

			$msg = "Please select a file to be imported.";

		}

		$disp = new Display($goto);
		$disp->addParam("msg", $msg);
		if($evtcl->getParam("fromReg") == 'Yes'){//if importing while registration
		$_SESSION["page_from"] = 'reg';
		}
		$evtcl->setDisplayNext($disp);

	}

	function findexts ($filename){

		$filename = strtolower($filename) ;
		$exts = split("[/\\.]", $filename) ;
		$n = count($exts)-1;
		$exts = $exts[$n];
		return $exts;

	}

	function print_vcard_address_book($file, $title, $style, $cat, $hide)
	{
		if ($title) {
			$title = stripcslashes($title);
		} else if ($file) {
			$title = $file;
		} else {
			$title = "vCard - A vCard Address Book";
		}
		
		if (!$style) {
			$style = "includes/vcard_style.css";
		}
	
		//echo "<link href='$style' type='text/css' rel='stylesheet'>\n";
	
		//echo "</head>\n<body>\n";
		//echo "<h1>$title</h1>\n";
/*	
		if (!$file) {
			exit('Required $file parameter not specified.');
		}
*/
	
		$lines = file($file);
		if (!$lines) {
			$this->errMsg .= "Can't read the vCard file: $file";
			return false;
		}

		$cards = $this->parse_vcards($lines);
		$this->print_vcards($cards, $cat, $hide);
		if(!$this->imported) {
			$this->errMsg .= "Sorry! Your Contacts could not be imported.";
		}
		return $this->imported;
		//echo "</body>\n</html>\n";
	}
	
	/**
	* Prints a set of vCards in two columns. The $categories_to_display is a
	* comma delimited list of categories.
	*/   
	function print_vcards(&$cards, $categories_to_display, $hide)
	{
		$this->contact = array();
		$all_categories = $this->get_vcard_categories($cards);
	
		if (!$categories_to_display) {
			$categories_to_display = array('All');
		} else if ($categories_to_display == '*') {
			$categories_to_display = $all_categories;
		} else {
			$categories_to_display = explode(',', $categories_to_display);
		}
	
		if ($hide) {
			$hide = explode(',', $hide);
		} else {
			$hide = array();
		}
	

		//echo join(', ', $categories_to_display);

	
		foreach ($cards as $card_name => $card) {	
			if (!$card->inCategories($categories_to_display)) {
				continue;
			}

			//echo "<p class='name'><strong>$card_name</strong>";
			// Add the categories (if present) after the name.
			$property = $card->getProperty('CATEGORIES');
			if ($property) {
				// Replace each comma by a comma and a space.
				$categories = $property->getComponents(',');
				$categories = join(',', $categories);
				//echo "&nbsp;($categories)";
				$this->contact['CATEGORIES'] = $categories;
			}

			$this->print_vcard($card, $hide);
			$this->insertContact();
			//print_r($this->contact);
		}

	}
	
	/**
	* Prints the vCard as HTML.
	*/
	function print_vcard($card, $hide)
	{
		$names = array('FN', 'TITLE', 'ORG', 'TEL', 'EMAIL', 'URL', 'ADR', 'BDAY', 'NOTE', 'PHOTO');
	
		$row = 0;
	
		foreach ($names as $name) {
			if (in_array_case($name, $hide)) {
				continue;
			}
			$properties = $card->getProperties($name);
			if ($properties) {
				foreach ($properties as $property) {
					$show = true;
					$types = $property->params['TYPE'];
					if ($types) {
						foreach ($types as $type) {
							if (in_array_case($type, $hide)) {
								$show = false;
								break;
							}
						}
					}
					if ($show) {
						$class = ($row++ % 2 == 0) ? "property-even" : "property-odd";
						$this->print_vcard_property($property, $class, $hide);
					}
				}
			}
		}
	}
	
	/**
	* Prints a VCardProperty as HTML.
	*/
	function print_vcard_property($property, $class, $hide)
	{		
		$name = $property->name;
		$value = $property->value;
		switch ($name) {
			case 'FN':
				$components = $property->getComponents();
				$lines = array();
				foreach ($components as $component) {
					if ($component) {
						$lines[] = $component;
					}
				}
				$fn = join("\n", $lines);
				$this->contact['FULLNAME'] = $fn;
				break;
			case 'TITLE':
				$this->contact['TITLE'] = $value;
			case 'ADR':
				$adr = $property->getComponents();
				$lines = array();
				for ($i = 0; $i < 3; $i++) {
					if ($adr[$i]) {
						$lines[] = $adr[$i];
					}
				}
				$city_state_zip = array();
				for ($i = 3; $i < 6; $i++) {
					if ($adr[$i]) {
						$city_state_zip[] = $adr[$i];
					}
				}
				if ($city_state_zip) {
					// Separate the city, state, and zip with spaces and add
					// it as the last line.
					$lines[] = join("&nbsp;", $city_state_zip);
				}
				// Add the country.
				if ($adr[6]) {
					$lines[] = $adr[6];
				}
				$addr = join("\n", $lines);
				$types = $property->params['TYPE'];
				if ($types) {
					foreach($types as $type) {
						if($type == 'WORK') {
							$this->contact['ADR_WORK'] = $addr;
						} elseif($type == 'HOME') {
							$this->contact['ADR_HOME'] = $addr;
						} else {
							$this->contact['ADR_OTHER'] = $addr;
						}
					}
				}
				break;
			case 'EMAIL':
				//$html = "<a href='mailto:$value'>$value</a>";
				$types = $property->params['TYPE'];
				if ($types) {
					foreach($types as $type) {
						if($type == 'WORK') {
							$this->contact['EMAIL_WORK'] = $value;
						} elseif($type == 'HOME') {
							$this->contact['EMAIL_HOME'] = $value;
						} elseif($type == 'INTERNET') {
							$this->contact['EMAIL_OTHER'] = $value;
						} elseif($type == 'PREF') {
							$this->contact['EMAIL_OTHER'] = $value;
						} else {
							$this->contact['EMAIL_OTHER'] = $value;
						}
					}
				}
				break;
			case 'TEL':
				$components = $property->getComponents();
				$lines = array();
				foreach ($components as $component) {
					if ($component) {
						$lines[] = $component;
					}
				}
				$value = join("\n", $lines);
				$types = $property->params['TYPE'];
				if ($types) {
					foreach($types as $type) {
						if($type == 'WORK') {
							$this->contact['TELL_WORK'] = $value;
						} elseif($type == 'HOME') {
							$this->contact['TELL_HOME'] = $value;
						} elseif($type == 'FAX') { 
							$this->contact['TELL_FAX'] = $value;
						} elseif($type == 'CELL') { 
							$this->contact['TELL_CELL'] = $value;
						} else {
							$this->contact['TELL_OTHER'] = $value;
						}
					}
				}
				break;
			case 'PHOTO':
				$components = $property->getComponents();
				$lines = array();
				foreach ($components as $component) {
					if ($component) {
						$lines[] = $component;
					}
				}
				$value = join("\n", $lines);
				$types = $property->params['VALUE'];
				if ($types) {
					foreach($types as $type) {
						if($type == 'URI') {
							$this->contact['PHOTO'] = $value;
						} else{
							$this->contact['PHOTO'] = 'testphoto';
						}
					}
				}
				break;
			case 'URL':
				//$html = "<a href='$value' target='_base'>$value</a>";
				$this->contact['URL'] = $value;
				break;
			case 'BDAY':
				//$html = "Birthdate: $value";
				$this->contact['BDAY'] = $value;
				break;
			case 'ORG':
				$this->contact['ORG'] = $value;
				//$html = $value;
				break;
			default:
				$components = $property->getComponents();
				$lines = array();
				foreach ($components as $component) {
					if ($component) {
						$lines[] = $component;
					}
				}
				$html = join("\n", $lines);
				break;
		}
/*
		echo "<p class='$class'>\n";
		echo nl2br(stripcslashes($html));
		$types = $property->params['TYPE'];
		if ($types) {
			$type = join(", ", $types);
			echo " (" . ucwords(strtolower($type)) . ")";
		}
		echo "\n</p>\n";
*/

		//$this->insertContact();
	}
	
	function get_vcard_categories(&$cards)
	{
		$unfiled = false;   // set if there is at least one unfiled card
		$result = array();
		foreach ($cards as $card_name => $card) {
			$properties = $card->getProperties('CATEGORIES');
			if ($properties) {
				foreach ($properties as $property) {
					$categories = $property->getComponents(',');
					foreach ($categories as $category) {
						if (!in_array($category, $result)) {
							$result[] = $category;
						}
					}
				}
			} else {
				$unfiled = true;
			}
		}
		if ($unfiled && !in_array('Unfiled', $result)) {
			$result[] = 'Unfiled';
		}
		return $result;
	}
	
	/**
	* Parses a set of cards from one or more lines. The cards are sorted by
	* the N (name) property value. There is no return value. If two cards
	* have the same key, then the last card parsed is stored in the array.
	*/
	function parse_vcards(&$lines)
	{
		$cards = array();
		$card = new VCard();
		while ($card->parse($lines)) {
			$property = $card->getProperty('N');
			if (!$property) {
				return "";
			}
			$n = $property->getComponents();
			$tmp = array();
			if ($n[3]) $tmp[] = $n[3];      // Mr.
			if ($n[1]) $tmp[] = $n[1];      // John
			if ($n[2]) $tmp[] = $n[2];      // Quinlan
			if ($n[4]) $tmp[] = $n[4];      // Esq.
			$ret = array();
			if ($n[0]) $ret[] = $n[0];
			$tmp = join(" ", $tmp);
			if ($tmp) $ret[] = $tmp;
			$key = join(", ", $ret);
			$cards[$key] = $card;
			// MDH: Create new VCard to prevent overwriting previous one (PHP5)
			$card = new VCard();
		}
		ksort($cards);
		return $cards;
	}

    function insertContact() {

		if($this->contact['ORG']) {
        	$id_company = $this->checkCompanyExists($this->contact['ORG']);
			$id_company_flag = ($id_company) ? true : false;
			$flag = true;
		} else {
			$flag = true;
			$id_company = 0;
			$id_company_flag = true;
		}
	
		if($flag) {

			if($id_company_flag){
					
				$new_contact = new Contact();
				$new_contact->firstname = $this->contact['FULLNAME'];
				$new_contact->position = $this->contact['TITLE'];
				if($this->contact['ORG']) {
					$company = $this->contact['ORG'];
				} else {
					$company = "";
				}
				$new_contact->company = $company;
				$new_contact->idcompany = $id_company;
				$new_contact->iduser = $this->iduser;
				$new_contact->birthday = $this->contact['BDAY'];
				$new_contact->picture = $this->contact['PHOTO'];
				$new_contact->add();
				$lastInsertedContId = $new_contact->getPrimaryKeyValue();

	
			} else{

				$new_company = new Company();
				$new_company->name = $this->contact['ORG'];
				$new_company->iduser = $this->iduser;
				$new_company->add();
	
				$new_company_id = $new_company->getPrimaryKeyValue();
	
				$new_contact = new Contact();
				$new_contact->firstname = $this->contact['FULLNAME'];
				$new_contact->position = $this->contact['TITLE'];
				$new_contact->company = $this->contact['ORG'];
				$new_contact->idcompany = $new_company_id;
				$new_contact->iduser = $this->iduser;
				$new_contact->birthday = $this->contact['BDAY'];
				$new_contact->picture = $this->contact['PHOTO'];
				$new_contact->add();
				$lastInsertedContId = $new_contact->getPrimaryKeyValue();
				

			}

			if($this->contact['TELL_WORK']){
				$new_contact->addPhone($this->contact['TELL_WORK'],'Work');
			}
			if($this->contact['TELL_HOME']){
				$new_contact->addPhone($this->contact['TELL_HOME'],'Home');
			}
			if($this->contact['TELL_FAX']){
				$new_contact->addPhone($this->contact['TELL_FAX'],'Fax');
			}
			if($this->contact['TELL_CELL']){
				$new_contact->addPhone($this->contact['TELL_CELL'],'Mobile');
			}
			if($this->contact['TELL_OTHER']){
				$new_contact->addPhone($this->contact['TELL_OTHER'],'Other');
			}
			if($this->contact['ADR_WORK']){
				$new_contact->addAddress($this->contact['ADR_WORK'],'Work');
			}
			if($this->contact['ADR_HOME']){
				$new_contact->addAddress($this->contact['ADR_HOME'],'Home');
			}
			if($this->contact['ADR_OTHER']){
				$new_contact->addAddress($this->contact['ADR_OTHER'],'Other');
			}
			if($this->contact['EMAIL_WORK']){
				$new_contact->addEmail($this->contact['EMAIL_WORK'],'Work');
			}
			if($this->contact['EMAIL_HOME']){
				$new_contact->addEmail($this->contact['EMAIL_HOME'],'Home');
			}
			if($this->contact['EMAIL_OTHER']){
				$new_contact->addEmail($this->contact['EMAIL_OTHER'],'Other');
			}

			$do_tag = new Tag();

			if($this->contact['CATEGORIES']) {		
				$contact_tag = explode(",", $this->contact['CATEGORIES']);
				$arr_import_tags = explode(",", $_SESSION['import_tag']);
				foreach($arr_import_tags as $imp_tag) {
					array_push($contact_tag, $imp_tag);
				}

				foreach($contact_tag as $tag) {
					$tag = trim($tag);
					$do_tag->addTagAssociation($lastInsertedContId,$tag,"contact",$this->iduser);
				}
			} else {
					$arr_import_tags = explode(",", $_SESSION['import_tag']);
					foreach($arr_import_tags as $tag) {
						$tag = trim($tag);
						$do_tag->addTagAssociation($lastInsertedContId,$tag,"contact",$this->iduser);
					}
			}
			$do_cont_view =  new ContactView();
			$do_cont_view->addFromContact($new_contact);
   $do_cont_view->updateFromContact($new_contact);// Added the method call updateFromContact() so that the child data is updated just after insert
   $do_cont_view->addTag($_SESSION['import_tag'],$new_contact->idcontact);// Update the contact view for tags.
			$this->imported = true;
		}

    }

    function checkCompanyExists($org_name){
        $q_company = new sqlQuery($this->getDbCon());
        $sql_sel = "SELECT *
                    FROM company
                    WHERE iduser = ".$this->iduser." AND 
                    name = '".$org_name."'
                   ";

        $q_company->query($sql_sel);

        if($q_company->getNumRows()){
			while($q_company->fetch()) {
            	$idcompany = $q_company->getData("idcompany");
			}
			return $idcompany;
        } else{
                  return false;
        }
    }
	
}
?>