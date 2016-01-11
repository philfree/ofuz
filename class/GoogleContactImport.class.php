<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

set_time_limit(18000); //5 hrs
/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';
/**
 * @see class_ContactsAuth
 * @author Ravi
 */
//Zend_Loader::loadClass('Zend_Http_Response');
include_once 'ContactsAuth.php';
class GoogleContactImport extends DataObject
{
    public $id_user;
    public $uEmail = "";
    public $err_message = "";
    public $status_code_desc = "";

    private $client = "";


    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(false);
       if (RADRIA_LOG_RUN_OFUZ) {
           $this->setLogRun(RADRIA_LOG_RUN_OFUZ);
       }
    }

    /**
     * User authentication.
     * This processes without storing SESSION TOKEN in table.
     * @return void
     * @see class_ContactsAuth
     */

    function processAuth() 
    {
        global $_SESSION, $_GET;

        if (!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
                $_SESSION["uEmail"] = $this->uEmail; //is used to retrieve user contacts
                Zend_ContactsAuth::requestUserLogin('Please login to your Google Account.');
        }
        else {
                $client = Zend_ContactsAuth::getAuthSubHttpClient();
                $this->client = $client;
        }
    }

    /**
     * retrieves all the contacts of an user associated with provided emailid.
     * 'g_o' = 'Google to Ofuz'
     * @return string (Status code Description)
     * @see class : ContactsAuth,Gdata
     */

    function retrieveUserContacts()
    {

        global $_SESSION, $_GET;
        $client = $this->client;

        // Create a Gdata object using the authenticated Http Client
        $gdata = new Zend_Gdata($client);
		$useremailid = urlencode($_SESSION["uEmail"]);
        //$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/'.$_SESSION["uEmail"].'%40gmail.com/full');
		$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/'.$useremailid.'/full');
        $query->setMaxResults(1000);

        /*
         * checks if already synchronized, if yes, then gets the last sync date.
         * and contacts are retrieved after last updated date.
         */

        $syncronized = $this->isSyncronized('g_o');
        if($syncronized){
            $sync_date = $this->getLastSyncDate('g_o');
            $updated_min = $this->formatDateForGoogle($sync_date);
	    /*
         *Sets the minimum updated date to fetch contacts after last updated date
         */
            $query->setUpdatedMin($updated_min);
        }
        try{
                $feed = $gdata->getFeed($query);
                $contacts_flag = false;
                $this->setLog("\n---------google Feed ------\n".$feed."\n-----------end feed-----\n");
                foreach ($feed as $entry) {
                    $data = $this->pushContactIntoArray($entry);
                    if($data){
                        $contacts[] = $data;
                        $contacts_flag = true;
                    }
                }
        
                if($syncronized){
                    if($contacts_flag){
                        $this->setLog("\n Update Contact:".$contacts['title']);
                        $this->updateContact($contacts);
                    }
                } else{
                    if($contacts_flag){
                        $this->setLog("\n Adding Contact:".$contacts['title']);
                        $this->insertContact($contacts);
                    }
                }
        } catch (Exception $e){
                $status_code = $gdata->getHttpClient()->getLastResponse()->getStatus();
                $this->status_code_desc = $this->getStatusDescription($status_code);
        }
    }

    /**
     * Checks if it has already been synchronized for particular User for particular mode.
     * 'g_o' = 'Google to Ofuz', 'o_g' = 'Ofuz to Google'
     * @param string  'g_o' or 'o_g'
     * @return boolean
     */
    function isSyncronized($mode){
        $q_user = new sqlQuery($this->getDbCon());
        $q_user->query("SELECT * FROM google_contact WHERE iduser = ".$this->id_user." AND mode='".$mode."'");

        if($q_user->getNumRows())
        {
            $q_user->free();
            return true;
        } else{
            $q_user->free();
            return false;
        }
    }

    /**
     * making an array of contacts (returned by feed)
     * @param xml : Contact feed entry (contact details returned by feed)
     * @return array/false : contact details(if exists) else false
     */
    function pushContactIntoArray($entry){

        if($entry){

            if (trim($entry->title) != ""){

                $data = array();
    
                //getting self link (<link rel="self"...../>) from entry (used to retrieve single contact)
                foreach($entry->link as $link)
                {
                    if($link->rel == 'self')
                    {
                            $data['link_self'] = $link->href;
                    }
                    if($link->rel == 'edit')
                    {
                            $data['link_edit'] = $link->href;
                    }
                    if($link->rel == 'http://schemas.google.com/contacts/2008/rel#photo')
                    {
                            $data['link_photo'] = $link->href;
                    }
                }		
                // Get contact name
                if ($entry->title != "") {
                    $data['title'] = $entry->title;
                } else {
                    $data['title'] = "Untitled Contact";
                }	
            
                $data['id'] = $entry->id;
                $data['updated'] = $entry->updated;          
                $data['content'] = $entry->content;
    
                    // retrieving emails
                $extensionElements = $entry->getExtensionElements();

                foreach ($extensionElements as $extensionElement) {
                    //emails
                    if ($extensionElement->rootNamespaceURI ==
                    "http://schemas.google.com/g/2005"
                    && $extensionElement->rootElement == "email") {
                        $attributes = $extensionElement->getExtensionAttributes();
                        if (array_key_exists('address', $attributes)) {
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#home")
                            {
                                    $data['em_home'] = $attributes['address']['value'];
                            }
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#work")
                            {
                                    $data['em_work'] = $attributes['address']['value'];
                            }
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#other")
                            {
                                    $data['em_other'] = $attributes['address']['value'];
                            }					
                        }
                    }
                    //Phone
                    if ($extensionElement->rootNamespaceURI ==
                    "http://schemas.google.com/g/2005"
                    && $extensionElement->rootElement == "phoneNumber") {
                        /*$ph = $extensionElement->text ;
                        echo "<li>Home:" . $ph . "</li>\n";*/
                        $attributes = $extensionElement->getExtensionAttributes();
                        if (array_key_exists('rel', $attributes)) {
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#mobile")
                            {
                                    $data['ph_mobile'] = $extensionElement->text;
                            }
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#home")
                            {
                                    $data['ph_home'] = $extensionElement->text;
                            }
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#work")
                            {
                                    $data['ph_work'] = $extensionElement->text;
                            }
    /*
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#home_fax")
                            {
                                    $data['ph_home_fax'] = $extensionElement->text;
                            }
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#other")
                            {
                                    $data['ph_other'] = $extensionElement->text;
                            }
    */
                        }
                    }
                    //address
                    if ($extensionElement->rootNamespaceURI ==
                    "http://schemas.google.com/g/2005"
                    && $extensionElement->rootElement == "postalAddress") {
                        /*$ph = $extensionElement->text ;
                        echo "<li>Home:" . $ph . "</li>\n";*/
                        $attributes = $extensionElement->getExtensionAttributes();
                        if (array_key_exists('rel', $attributes)) {
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#home")
                            {
                                    $data['address_home'] = $extensionElement->text;
                            }
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#other")
                            {
                                    $data['address_other'] = $extensionElement->text;
                            }
                            if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#work")
                            {
                                    $data['address_work'] = $extensionElement->text;
                            }
                        }
                    }
                    //organization
                    if ($extensionElement->rootNamespaceURI ==
                    "http://schemas.google.com/g/2005"
                    && $extensionElement->rootElement == "organization") {
                        $subElements = $extensionElement->getExtensionElements();
                        foreach ($subElements as $subElement) {
                            if ($subElement->rootElement == "orgName") {
                                $data['org_name'] = $subElement->text;
                            }
                            if ($subElement->rootElement == "orgTitle") {
                                $data['org_title'] = $subElement->text;
                            }
                        }
                    }
    
    /*
                    //IM
                    if ($extensionElement->rootNamespaceURI ==
                    "http://schemas.google.com/g/2005"
                    && $extensionElement->rootElement == "im") {
                        $attributes = $extensionElement->getExtensionAttributes();
                        if (array_key_exists('address', $attributes)) {
                            if($attributes['protocol']['value'] == "http://schemas.google.com/g/2005#GOOGLE_TALK")
                            {
                                    $data['im_gt'] = $attributes['address']['value'];
                            }
                            if($attributes['protocol']['value'] == "http://schemas.google.com/g/2005#AIM")
                            {
                                    $data['im_aim'] = $attributes['address']['value'];
                            }
                            if($attributes['protocol']['value'] == "http://schemas.google.com/g/2005#YAHOO")
                            {
                                    $data['im_y'] = $attributes['address']['value'];
                            }
                            if($attributes['protocol']['value'] == "http://schemas.google.com/g/2005#MSN")
                            {
                                    $data['im_msn'] = $attributes['address']['value'];
                            }
                            if($attributes['protocol']['value'] == "http://schemas.google.com/g/2005#ICQ")
                            {
                                    $data['im_icq'] = $attributes['address']['value'];
                            }	
                            if($attributes['protocol']['value'] == "http://schemas.google.com/g/2005#JABBER")
                            {
                                    $data['im_jb'] = $attributes['address']['value'];
                            }	
                        }
                    }
    */
    
                }
                return $data;
            } else{
                return false;
            }

        }
    }

    /**
     * inserting a contact into ofuz Database.
     * @param array : contact details
     * @return void
     */
    function insertContact($contact){
        if($contact){
            $email_msg = "The following contacts could not be imported since email addresses <br />";
            $email_msg .= "for these Contacts already exist in your Contacts : <br />";
            $flag = false;
            $flag1 = false;
            foreach($contact as $entry){
                //check if contact is present with same email address for a particular User
                $emailExists = $this->checkContactWithSameEmail($entry);
                if($emailExists){
                    $this->status_code_desc .= $entry['title'].",";
                    $flag1 = true;
                }
                if($emailExists == false){
                    $this->insertContactEntry($entry);
                    $flag = true;
                }
            }
            if($flag == true){
                $q_ins_contact = new sqlQuery($this->getDbCon());
                $last_updated = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - 60);

                //'g_o' is mode which says 'google to ofuz'
                if($this->isSyncronized('g_o')){
                    $sql_ins = "UPDATE google_contact SET last_updated = '".$last_updated."'
                                WHERE iduser=".$this->id_user." AND mode='g_o'";
                } else{
                          $sql_ins = "INSERT INTO
                                      google_contact(iduser,last_updated,mode)
                                      VALUES(".$this->id_user.",'".$last_updated."','g_o')
                                    ";                      
                }
                $q_ins_contact->query($sql_ins);
            }
            if($flag1){
                $this->status_code_desc = $email_msg.$this->status_code_desc;
            }
        }
    }
	
	/**
	 * checking if the company already exists for a particular User.
	 * @param string : organization/company name
	 * @return string/boolean : if exists then company name else false
	 */

    function checkCompanyExists($org_name){
        $q_company = new sqlQuery($this->getDbCon());
        $sql_sel = "SELECT *
                    FROM company
                    WHERE iduser = ".$this->id_user." AND 
                    name = '".$org_name."'
                   ";

        $q_company->query($sql_sel);

        if($q_company->getNumRows()){
            $q_company->fetch();
            return $q_company->getData("idcompany");
        } else{
                  return false;
        }
    }

	/**
	 * inserting a contact entry into ofuz Database.
	 * @param array : contact details
	 * @return void
	 * @see class : Contact, Company
	 */

    function insertContactEntry($entry){
        $id_company = $this->checkCompanyExists($entry['org_name']);

        if($id_company){

            $new_contact = new Contact();
            $new_contact->company = $entry['org_name'];
            $new_contact->firstname = $entry['title'];
            $new_contact->idcompany = $id_company;
            $new_contact->iduser = $this->id_user;
            $new_contact->position = $entry['org_title'];
            $new_contact->add();
            $lastInsertedContId = $new_contact->getPrimaryKeyValue();

        } else{
                  $new_company = new Company();
                  $new_company->name = $entry['org_name'];
                  $new_company->iduser = $this->id_user;
                  $new_company->add();

                  $new_company_id = $new_company->getPrimaryKeyValue();

                  $new_contact = new Contact();
                  $new_contact->company = $entry['org_name'];
                  $new_contact->firstname = $entry['title'];
                  $new_contact->idcompany = $new_company_id;
                  $new_contact->iduser = $this->id_user;
                  $new_contact->position = $entry['org_title'];
                  $new_contact->add();
                  $lastInsertedContId = $new_contact->getPrimaryKeyValue();
        }

        if($entry['ph_mobile']){
            $new_contact->addPhone($entry['ph_mobile'],'Mobile');
        }
        if($entry['ph_home']){
            $new_contact->addPhone($entry['ph_home'],'Home');
        }
        if($entry['ph_work']){
            $new_contact->addPhone($entry['ph_work'],'Work');
        }
        if($entry['address_home']){
            $new_contact->addAddress($entry['address_home'],'Home');
        }
        if($entry['address_other']){
            $new_contact->addAddress($entry['address_other'],'Other');
        }
        if($entry['address_work']){
            $new_contact->addAddress($entry['address_work'],'Work');
        }
        if($entry['em_other']){
            $new_contact->addEmail($entry['em_other'],'Other');
        }
        if($entry['em_home']){
            $new_contact->addEmail($entry['em_home'],'Home');
        }
        if($entry['em_work']){
            $new_contact->addEmail($entry['em_work'],'Work');
        }

        $q_ins_contact = new sqlQuery($this->getDbCon());
        $sql_ins = "INSERT INTO
                    google_contact_info(idcontact,iduser,entry_id,entry_link_edit,entry_link_self)
                    VALUES(".$lastInsertedContId.",".$this->id_user.",'".$entry['id']."','"
                    .$entry['link_edit']."','".$entry['link_self']."')
                  ";
        $q_ins_contact->query($sql_ins);
    }

	/**
	 * Updates a contact entry in ofuz Database.
	 * @param array,int,int : contact details, contact id, company id
	 * @return void
	 * @see class : Company, Contact
	 */

    function updateContactEntry($entry,$id_contact,$id_company=null){
        if(!$id_company){
            $new_company = new Company();
            $new_company->name = $entry['org_name'];
            $new_company->iduser = $this->id_user;
            $new_company->add();
    
            $id_company = $new_company->getPrimaryKeyValue();
        }

        $contact = new Contact();
        $contact->getId($id_contact); // Primarykey id of the record to update
        $contact->company = $entry['org_name'];
        //do not update name while importing since Gmail has only one text box for the name
        //$contact->firstname = $entry['title'];
        $contact->idcompany = $id_company;
        //$contact->iduser = $this->id_user;
        $contact->position = $entry['org_title'];
        $contact->update();

        $q_contact = new sqlQuery($this->getDbCon());

        $sql_sel = "SELECT * FROM contact_address WHERE idcontact = ".$id_contact;
        $q_contact->query($sql_sel);

        if($q_contact->getNumRows()){           
           while($q_contact->fetch()){

              if($q_contact->getData("address_type") == "Home"){
                  $contact->updateAddress($entry['address_home'],'Home',$q_contact->getData("idcontact_address"));
              }
              if($q_contact->getData("address_type") == "Other"){
                  $contact->updateAddress($entry['address_other'],'Other',$q_contact->getData("idcontact_address"));
              }
              if($q_contact->getData("address_type") == "Work"){
                  $contact->updateAddress($entry['address_work'],'Work',$q_contact->getData("idcontact_address"));
              }
            }
        }

        $sql_sel = "SELECT * FROM contact_email WHERE idcontact = ".$id_contact;
        $q_contact->query($sql_sel);

        if($q_contact->getNumRows()){           
           while($q_contact->fetch()){

              if($q_contact->getData("email_type") == "Home"){
                  $contact->updateEmail($entry['em_home'],'Home',$q_contact->getData("idcontact_email"));
              }
              if($q_contact->getData("email_type") == "Other"){
                  $contact->updateEmail($entry['em_other'],'Other',$q_contact->getData("idcontact_email"));
              }
              if($q_contact->getData("email_type") == "Work"){
                  $contact->updateEmail($entry['em_work'],'Work',$q_contact->getData("idcontact_email"));
              }
            }
        }

        $sql_sel = "SELECT * FROM contact_phone WHERE idcontact = ".$id_contact;
        $q_contact->query($sql_sel);

        if($q_contact->getNumRows()){           
           while($q_contact->fetch()){

              if($q_contact->getData("phone_type") == "Home"){
                  $contact->updatePhone($entry['ph_home'],'Home',$q_contact->getData("idcontact_phone"));
              }
              if($q_contact->getData("phone_type") == "Mobile"){
                  $contact->updatePhone($entry['ph_mobile'],'Mobile',$q_contact->getData("idcontact_phone"));
              }
              if($q_contact->getData("phone_type") == "Work"){
                  $contact->updatePhone($entry['ph_work'],'Work',$q_contact->getData("idcontact_phone"));
              }
            }
        }

    }

	/**
	 * checking if contact entry already exists in google_contact_info table.
     * when we import contact/s from Gmail to Ofuz, we keep contact details in this table to 
	 * keep track if the particular contact is already retrieved from Gmail.
	 *
	 * @param string : contact entry id (each contact retrieved from Gmail has unique entry id)
	 * @return int/boolean : if true then returns contact id else false.
	 */

    function entryExists($entry_id){
        $q_entry_id = new sqlQuery($this->getDbCon());
        $sql_sel = "SELECT entry_id,idcontact
                    FROM google_contact_info
                    WHERE entry_id='".$entry_id."' AND
                    iduser=".$this->id_user."
                   ";
        $q_entry_id->query($sql_sel);

        if($q_entry_id->getNumRows())
        {
            $q_entry_id->fetch();
            $idcontact = $q_entry_id->getData("idcontact");
            return $idcontact;
        } else{
                  return false;
        }
    }

	/**
	 * updating a contact entry in ofuz Database.
	 * @param array : contact details
	 * @return void
	 */

	function updateContact($contact){
		if($contact){
			$email_msg = "The following contacts could not be imported since email addresses <br />";
			$email_msg .= "for these Contacts already exist in your Contacts : <br />";
			$flag = false;
			$flag1 = false;
			//$q_ins_contact = new sqlQuery($this->getDbCon());
			foreach($contact as $entry){
				$id_contact = $this->entryExists($entry['id']);
				if($id_contact){
					//check if contact is present with same email address for a particular User
					$emailExists = $this->checkContactWithSameEmail($entry, $id_contact);
					if($emailExists){
						$this->status_code_desc .= $entry['title'].",";
						$flag1 = true;
					}
					if($emailExists == false){
						$id_company = $this->checkCompanyExists($entry['org_name']);
						
						if($id_company){
							$this->updateContactEntry($entry,$id_contact,$id_company);
						} else{
									$this->updateContactEntry($entry,$id_contact);
						}             
					}       
				} else{
							//check if contact is present with same email address for a particular User
							$emailExists = $this->checkContactWithSameEmail($entry);
							if($emailExists){
								$this->status_code_desc .= $entry['title'].",";
								$flag1 = true;
							}
							if($emailExists == false){
								$this->insertContactEntry($entry);
							}
				}
				$flag = true;
			}
			if($flag == true){
			$last_updated = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - 60);

			$q_ins_contact = new sqlQuery($this->getDbCon());
			$sql_update = "UPDATE google_contact
						SET last_updated='".$last_updated."'
						WHERE iduser = ".$this->id_user." AND mode='g_o'
						";
			$q_ins_contact->query($sql_update);
			}

			if($flag1){
				$this->status_code_desc = $email_msg.$this->status_code_desc;
			}
		}
	}

	/**
	 * retrieves the date when the last synchronization happened.
	 * @param string : 'g_o' or 'o_g'
	 * @return date
	 */

	function getLastSyncDate($mode){
		$q_updated_date = new sqlQuery($this->getDbCon());
		$sql_sel = "SELECT last_updated
					FROM google_contact
					WHERE iduser=".$this->id_user." AND
					mode = '".$mode."'
					";
		$q_updated_date->query($sql_sel);

		$q_updated_date->fetch();
		$updated_date = $q_updated_date->getData("last_updated");
		return $updated_date;
	}

/*
	function displayContact(){

		$query = "SELECT * 
					FROM company,contact
					WHERE company.iduser=".$this->id_user." AND contact.idcompany=company.idcompany";

		$this->query($query);

		if($this->getNumRows()){
			while($this->next()){
				$data = array();
				$data['firstname'] = $this->firstname;
				$data['position'] = $this->position;
				$data['company'] = $this->name;
				$contacts[] = $data;
			}
			return $contacts;
		}
	}
*/
		/**
		 * creating/adding a contact in user's Gmail account.
		 * @param array : contact details
		 * @return void : sets the status
		 * @see class : Gdata
		 */

        function createContactInGmail($contact)
        {
                $this->setLog("\n---------createContactInGmail ------\n".$contact."\n-----------end-----\n");
                global $_SESSION, $_GET;
                $client = $this->client;

                $xml = "<?xml version='1.0'?>";
                $xml .= "<atom:entry xmlns:atom='http://www.w3.org/2005/Atom'
                xmlns:gd='http://schemas.google.com/g/2005' Content-Type='application/atom+xml'>";
                $xml .= "<atom:category scheme='http://schemas.google.com/g/2005#kind'
                term='http://schemas.google.com/contact/2008#contact' />";
if($contact['name']){
                $xml .= "<atom:title type='text'>".htmlentities($contact['name'], ENT_QUOTES)."</atom:title>";
}
                //$xml .= "<atom:content type='text'>content</atom:content>";
if($contact['email_Work']){
                $xml .= "<gd:email rel='http://schemas.google.com/g/2005#work' address='".$contact['email_Work']."' />";
}
if($contact['email_Home']){
                $xml .= "<gd:email rel='http://schemas.google.com/g/2005#home' address='".$contact['email_Home']."' />";
}
        
if($contact['email_Other']){
                $xml .= "<gd:email rel='http://schemas.google.com/g/2005#other' address='".$contact['email_Other']."' />";
}        
if($contact['phone_Work']){
                $xml .= "<gd:phoneNumber rel='http://schemas.google.com/g/2005#work' primary='true'>".$contact['phone_Work']."</gd:phoneNumber>";
}        
if($contact['phone_Home']){
                $xml .= "<gd:phoneNumber rel='http://schemas.google.com/g/2005#home'>".$contact['phone_Home']."</gd:phoneNumber>";
}

if($contact['phone_Mobile']){
                $xml .= "<gd:phoneNumber rel='http://schemas.google.com/g/2005#mobile'>".$contact['phone_Mobile']."</gd:phoneNumber>";
}
if($contact['addr_Work']){
                $xml .= "<gd:postalAddress rel='http://schemas.google.com/g/2005#work' primary='true'>".htmlentities($contact['addr_Work'], ENT_QUOTES)."</gd:postalAddress>";
}
if($contact['addr_Home']){
                $xml .= "<gd:postalAddress rel='http://schemas.google.com/g/2005#home' >".htmlentities($contact['addr_Home'], ENT_QUOTES)."</gd:postalAddress>";
}
if($contact['addr_Other']){
                $xml .= "<gd:postalAddress rel='http://schemas.google.com/g/2005#other' >".htmlentities($contact['addr_Other'], ENT_QUOTES)."</gd:postalAddress>";
} 
if($contact['company']){
                $xml .= "<gd:organization rel='http://schemas.google.com/g/2005#other'>";
                $xml .= "<gd:orgName>".htmlentities($contact['company'], ENT_QUOTES)."</gd:orgName>";
    if($contact['position']){
                $xml .= "<gd:orgTitle>".htmlentities($contact['position'], ENT_QUOTES)."</gd:orgTitle>";
    }
                $xml .= "</gd:organization>";
}
                $xml .= "</atom:entry>";

				$useremailid = urlencode($_SESSION["uEmail"]);

                //$url = 'http://www.google.com/m8/feeds/contacts/'.$_SESSION["uEmail"].'%40gmail.com/full';
				$url = 'http://www.google.com/m8/feeds/contacts/'.$useremailid.'/full';

                $gdata = new Zend_Gdata($client);
                try{
                        $retVal = $gdata->post($xml,$url);
                        //$retVal = $gdata->post($xml,$url);
                        if($retVal)
                        {
                                $q_email = new sqlQuery($this->getDbCon());
                                
                                if($contact['email_Work']){
                                    $sql_email_ins = "INSERT INTO 
                                                      temp_gmail_emails(email_address)
                                                      VALUES('".$contact['email_Work']."')
                                                    ";
                                    $q_email->query($sql_email_ins);
                                }
                                if($contact['email_Home']){
                                    $sql_email_ins = "INSERT INTO 
                                                      temp_gmail_emails(email_address)
                                                      VALUES('".$contact['email_Home']."')
                                                    ";
                                    $q_email->query($sql_email_ins);
                                }
                                if($contact['email_Other']){
                                    $sql_email_ins = "INSERT INTO 
                                                      temp_gmail_emails(email_address)
                                                      VALUES('".$contact['email_Other']."')
                                                    ";
                                    $q_email->query($sql_email_ins);
                                }
                                $q_email->free();
                        }
                }  catch (Exception $e){
                            $status_code = $gdata->getHttpClient()->getLastResponse()->getStatus();
                            $this->status_code_desc = $this->getStatusDescription($status_code);
                }
        }

		/**
		 * Updates user's contact in Gmail while exporting from Ofuz to Gmail.
		 * @param array, array : $row (existing contact in Gmail), $entry (contact details to be updated)
		 * @return void : sets the status
		 */

        function updateOfuzContactInGmail($row, $entry){

			global $_SESSION, $_GET;
			$client = $this->client;

			$gdata = new Zend_Gdata($client);

			$xml = "<?xml version='1.0' encoding='UTF-8' ?>";
			$xml .= "<entry xmlns='http://www.w3.org/2005/Atom' xmlns:gd='http://schemas.google.com/g/2005'>";
			$xml .= "<id>".$row['id']."</id>";
			//$xml .= "<updated>".$this->updated."</updated>";
			$xml .= "<category scheme='http://schemas.google.com/g/2005#kind' term='http://schemas.google.com/contact/2008#contact' />";
			$xml .= "<title type='text'>".htmlentities($entry['name'], ENT_QUOTES)."</title>";
			// $xml .= "<content type='text'>".$this->content."</content>";
			$xml .= "<link rel='self' type='application/atom+xml' href='".$row['link_self']."' />";
			$xml .= "<link rel='edit' type='application/atom+xml' href='".$row['link_edit']."' />";
			if($entry['email_Home'])
			{
					$xml .= '<gd:email rel="http://schemas.google.com/g/2005#home" address="'.$entry['email_Home'].'"></gd:email>';
			}
			if($entry['email_Work'])
			{
					$xml .= '<gd:email rel="http://schemas.google.com/g/2005#work" address="'.$entry['email_Work'].'"></gd:email>';
			}
			if($entry['email_Other'])
			{
					$xml .= '<gd:email rel="http://schemas.google.com/g/2005#other" address="'.$entry['email_Other'].'"></gd:email>';
			}
			if($entry['phone_Mobile'] != "")
			{
			$xml .= "<gd:phoneNumber rel='http://schemas.google.com/g/2005#mobile' >".$entry['phone_Mobile']."</gd:phoneNumber>";
			}
			if($entry['phone_Home'] != "")
			{
			$xml .= "<gd:phoneNumber rel='http://schemas.google.com/g/2005#home' >".$entry['phone_Home']."</gd:phoneNumber>";
			}
			if($entry['phone_Work'] != "")
			{
			$xml .= "<gd:phoneNumber rel='http://schemas.google.com/g/2005#work' >".$entry['phone_Work']."</gd:phoneNumber>";
			}
			if($entry['addr_Work']){
			$xml .= "<gd:postalAddress rel='http://schemas.google.com/g/2005#work' primary='true'>".htmlentities($entry['addr_Work'], ENT_QUOTES)."</gd:postalAddress>";
			}
			if($entry['addr_Home']){
			$xml .= "<gd:postalAddress rel='http://schemas.google.com/g/2005#home' >".htmlentities($entry['addr_Home'], ENT_QUOTES)."</gd:postalAddress>";
			}
			if($entry['addr_Other']){
			$xml .= "<gd:postalAddress rel='http://schemas.google.com/g/2005#other' >".htmlentities($entry['addr_Other'], ENT_QUOTES)."</gd:postalAddress>";
			}
			if($entry['company']){
			$xml .= "<gd:organization rel='http://schemas.google.com/g/2005#other'>";
			$xml .= "<gd:orgName>".htmlentities($entry['company'], ENT_QUOTES)."</gd:orgName>";
				if($entry['position']){
					$xml .= "<gd:orgTitle>".htmlentities($entry['position'], ENT_QUOTES)."</gd:orgTitle>";
				}
			$xml .= "</gd:organization>";
			}
			$xml .= "</entry>";

			try{
					$retVal = $gdata->put($xml,$row['link_edit']);

					if($retVal)
					{
							return true;
					}
			} catch (Exception $e){
					$status_code = $gdata->getHttpClient()->getLastResponse()->getStatus();
					$this->status_code_desc = $this->getStatusDescription($status_code);
			}

        }

		/**
		 * retrieves all the contacts which are recently added to Gmail from OFUZ
		 * @return array : contacts
		 */

        function getRecentlyAddedContactsFromOfuz()
        {
            global $_SESSION, $_GET;
            $client = $this->client;
    		$useremailid = urlencode($_SESSION["uEmail"]);
            // Create a Gdata object using the authenticated Http Client
            $gdata = new Zend_Gdata($client);
            //$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/'.$_SESSION["uEmail"].'%40gmail.com/full');
			$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/'.$useremailid.'/full');
            $query->setMaxResults(1000);
    
            $date = date ("Y-m-d H:i:s");
            list($dt,$time) = split(' ',$date);
            $updated_min = $dt."T".$time;
    
            $query->setUpdatedMin($updated_min); //to fetch contacts after last updated date
            try{
                    $feed = $gdata->getFeed($query);
                    //print_r($feed);exit;
                    foreach ($feed as $entry) {
                        $data = $this->pushContactIntoArray($entry);
                        $contacts[] = $data;
                    }
                    return $contacts;
            } catch (Exception $e){
                $status_code = $gdata->getHttpClient()->getLastResponse()->getStatus();
                $this->status_code_desc = $this->getStatusDescription($status_code);
              }

        }

		/**
		 * exporting(add/update) ofuz contacts to Gmail
		 * @return void
		 */

        function syncOfuzToGmail(){
                $temp_gmail_emails_table_flag = false; // temp_gmail_emails table is empty.
                $ofuz_contacts = $this->getOfuzContactsNotInGmail();
                //$contacts_not_in_gmail = $ofuz_contacts;
                $q_ins_contact = new sqlQuery($this->getDbCon());
                if($ofuz_contacts){
                    $this->storeGmailEmailsTemprorily(); //store Gmail emails in temp table
                    $temp_gmail_emails_table_flag = true; // temp_gmail_emails_table is not empty.
                    foreach($ofuz_contacts as $entry){
                        $emailExists = $this->checkEmailExistsInTempTable($entry);
                        $this->setLog("\n check email exists: idcontact".$entry['idcontact']);
                        if($emailExists){

                       }else{

                            $this->createContactInGmail($entry);
                            $this->setLog("\n---------after creating contact ------\n".$entry."\n-----------end-----\n");
                            $gmail_contacts = $this->getRecentlyAddedContactsFromOfuz();    
                            //$contacts_not_in_gmail = $this->getOfuzContactsNotInGmail();        
                            $this->setLog("\n getRecentlyAddedContactsFromOfuz: idcontact".$gmail_contacts);
                            if($gmail_contacts){
                                foreach($gmail_contacts as $ent){

                                    if(trim($entry['name'])==trim($ent['title'])){
                                        $sql_sel = "SELECT idcontact FROM google_contact_info WHERE entry_id = '".$ent['id']."'";
                                        $q_ins_contact->query($sql_sel);
                                        if($q_ins_contact->getNumRows() < 1){
                                            $sql_ins = "INSERT INTO
                                                        google_contact_info(idcontact,iduser,entry_id,entry_link_edit,entry_link_self)
                                                        VALUES(".$entry['idcontact'].",".$this->id_user.",'".$ent['id']."','"
                                                        .$ent['link_edit']."','".$ent['link_self']."')
                                                      ";
                                            $q_ins_contact->query($sql_ins);
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $q_ins_contact->free();
//exit();
                //updating OFUZ contacts in Gmail
                $contacts = $this->getOfuzContactsAfterSyncDate();
                //print_r($contacts);exit();
                if($contacts){
                    if(!$temp_gmail_emails_table_flag){
                        $this->storeGmailEmailsTemprorily(); //store Gmail emails in temp table
                    }

                    foreach($contacts  as $entry){
						/*if($entry['entry_link_self']) {
							$contact = $this->retreiveSingleContact($entry['entry_link_self']);
							//print_r($contact);exit();
							if($contact){
								foreach($contact as $row){
									$this->updateOfuzContactInGmail($row,$entry);
								}
							}
						}*/
						if($entry['entry_link_self']) {
							//$emailExists = $this->checkEmailExistsInTempTable($entry, $entry['idcontact']);
							$emailExists = $this->checkEmailExistsInTempTable($entry);
							if($emailExists){
								//409 CONFLIC
								//This Email id already exists in your Gmail Contacts.
								//Gmail can not have more than one contact with the same Email id.
							} else{
								$contact = $this->retreiveSingleContact($entry['entry_link_self']); 
								if($contact){
									foreach($contact as $row){
									$this->updateOfuzContactInGmail($row,$entry);
									}
								}
							}
						}
                    }
                }

				$this->addUpdateSyncDate();

                //empty temp_gmail_emails table
                $q_del_contact_sync = new sqlQuery($this->getDbCon());
                $sql_del = "DELETE FROM temp_gmail_emails";
                $q_del_contact_sync->query($sql_del);
        }

		/**
		 * retrieves all the contacts from Ofuz after the last synchronization date for a 
		 * particular User.
		 *
		 * @return array : contacts
	  	 */

        function getOfuzContactsAfterSyncDate(){
                $sync_date = $this->getLastSyncDate('o_g');
                $sql_sel = "SELECT c.*,gci.* 
                            FROM contact c 
                            LEFT JOIN company co ON c.idcompany = co.idcompany 
                            LEFT JOIN google_contact_info gci ON c.idcontact = gci.idcontact 
                            LEFT JOIN updated_date_log udl ON c.idcontact = udl.primarykeyvalue
                            WHERE c.iduser=".$this->id_user." AND gci.idcontact IS NOT NULL AND 
                            udl.tablename = 'contact' AND udl.updatedate > '".$sync_date."'
                           ";

                $this->query($sql_sel);

                if($this->getNumRows()){

                    $Contact = new Contact();
                    $Contact->sessionPersistent("ContactEditSave", "index.php", OFUZ_TTL);

                    while($this->next()){

                        $Contact->getId($this->idcontact);

                        $data = array();
                        $data['name'] = $this->firstname." ".$this->lastname;
                        if($this->position != "" || $this->position != NULL){
                                                $data['position'] = $this->position;
                        } else{
                              $data['position'] = "Position";
                        }
                        
                        if($this->company != "" || $this->company != NULL){
                                                $data['company'] = $this->company;
                        } else{
                              $data['company'] = "Company";
                        }
                        $data['idcontact'] = $this->idcontact;
  
                        $data['entry_id'] = $this->entry_id;
                        $data['entry_link_self'] = $this->entry_link_self;
                        $data['entry_link_edit'] = $this->entry_link_edit;                        
                            
                        $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
                        while($ContactPhone->next()){
                            if($ContactPhone->phone_type == "Home"){
                                $data['phone_Home'] = $ContactPhone->phone_number;
                            }
                            if($ContactPhone->phone_type == "Mobile"){
                                $data['phone_Mobile'] = $ContactPhone->phone_number;
                            }
                            if($ContactPhone->phone_type == "Work"){
                                $data['phone_Work'] = $ContactPhone->phone_number;
                            }
                        }

                        $ContactEmail = $_SESSION['ContactEditSave']->getChildContactEmail();
                        while($ContactEmail->next()){
                            if($ContactEmail->email_type == "Home"){
                                $data['email_Home'] = $ContactEmail->email_address;
                            }
                            if($ContactEmail->email_type == "Other"){
                                $data['email_Other'] = $ContactEmail->email_address;
                            }
                            if($ContactEmail->email_type == "Work"){
                                $data['email_Work'] = $ContactEmail->email_address;
                            }
                        }

                        $ContactAddr = $_SESSION['ContactEditSave']->getChildContactAddress();
                        while($ContactAddr->next()){
                            if($ContactAddr->address_type == "Home"){
                                $data['addr_Home'] = $ContactAddr->address;
                            }
                            if($ContactAddr->address_type == "Other"){
                                $data['addr_Other'] = $ContactAddr->address;
                            }
                            if($ContactAddr->address_type == "Work"){
                                $data['addr_Work'] = $ContactAddr->address;
                            }
                        }

                        $contacts[] = $data;
                    }
                    $this->free();
                    return  $contacts;
                 }
        }

		/**
		 * retrieves a single contact from Ofuz Database based on the contact entry id.
		 * contact entry id is a unique id associated with Gmail Contact.
		 *
		 * @param string : contact entry id
		 * @return array : contact details
		 */

        function getOfuzSingleContactOnEntyid($entry_id){

                $sql_sel = "SELECT c.*,gci.*
                            FROM contact c 
                            LEFT JOIN company co ON c.idcompany = co.idcompany 
                            LEFT JOIN google_contact_info gci ON c.idcontact = gci.idcontact 
                            WHERE c.iduser=".$this->id_user." AND gci.entry_id='".$entry_id."'
                            AND gci.idcontact IS NOT NULL
                          ";
                $this->query($sql_sel);

                  if($this->getNumRows()){

                    $Contact = new Contact();
                    $Contact->sessionPersistent("ContactEditSave", "index.php", OFUZ_TTL);

                    while($this->next()){

                        $Contact->getId($this->idcontact);

                        $data = array();
                        $data['name'] = $this->firstname;
                        $data['position'] = $this->position;
                        $data['company'] = $this->company;
                        $data['idcontact'] = $this->idcontact;
  
                        $data['entry_id'] = $this->entry_id;
                        $data['entry_link_self'] = $this->entry_link_self;
                        $data['entry_link_edit'] = $this->entry_link_edit;                        
                            
                        $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
                        while($ContactPhone->next()){
                            if($ContactPhone->phone_type == "Home"){
                                $data['phone_Home'] = $ContactPhone->phone_number;
                            }
                            if($ContactPhone->phone_type == "Mobile"){
                                $data['phone_Mobile'] = $ContactPhone->phone_number;
                            }
                            if($ContactPhone->phone_type == "Work"){
                                $data['phone_Work'] = $ContactPhone->phone_number;
                            }
                        }

                        $ContactEmail = $_SESSION['ContactEditSave']->getChildContactEmail();
                        while($ContactEmail->next()){
                            if($ContactEmail->email_type == "Home"){
                                $data['email_Home'] = $ContactEmail->email_address;
                            }
                            if($ContactEmail->email_type == "Other"){
                                $data['email_Other'] = $ContactEmail->email_address;
                            }
                            if($ContactEmail->email_type == "Work"){
                                $data['email_Work'] = $ContactEmail->email_address;
                            }
                        }

                        $ContactAddr = $_SESSION['ContactEditSave']->getChildContactAddress();
                        while($ContactAddr->next()){
                            if($ContactAddr->address_type == "Home"){
                                $data['addr_Home'] = $ContactAddr->address;
                            }
                            if($ContactAddr->address_type == "Other"){
                                $data['addr_Other'] = $ContactAddr->address;
                            }
                            if($ContactAddr->address_type == "Work"){
                                $data['addr_Work'] = $ContactAddr->address;
                            }
                        }

                        $contacts[] = $data;
                    }
                    return  $contacts;
                 }
        }

		/**
		 * retrieves all the contacts from Ofuz Database which are not yet exported (to Gmail contacts).
		 * @return array : contact details
		 */

        function getOfuzContactsNotInGmail(){
                $sql_sel = "SELECT c.* 
                            FROM contact c 
                            LEFT JOIN company co ON c.idcompany = co.idcompany 
                            LEFT JOIN contact_email ce ON ce.idcontact = c.idcontact
                            LEFT JOIN google_contact_info gci ON c.idcontact = gci.idcontact 
                            WHERE c.iduser=".$this->id_user."
                            AND ce.email_address != ''
                            AND gci.idcontact IS NULL
                            GROUP BY c.idcontact
                          ";

                $this->query($sql_sel);

                  if($this->getNumRows()){

                    $Contact = new Contact();
                    $Contact->sessionPersistent("ContactEditSave", "index.php", OFUZ_TTL);

                    while($this->next()){

                        //$Contact->getId($this->idcontact);
                        $_SESSION['ContactEditSave']->getId($this->idcontact);

                        $data = array();
                        $data['name'] = $this->firstname." ".$this->lastname;

                        if($this->company != "" || $this->company != NULL){
                              $data['company'] = $this->company;
                        } else{
                              $data['company'] = "Company";
                        }
                        
                        if($this->position != "" || $this->position != NULL){
                              $data['position'] = $this->position;
                        } else{
                              $data['position'] = "Position";
                        }

                        //$data['company'] = $this->company;
                        $data['idcontact'] = $this->idcontact;

                        $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
                        while($ContactPhone->next()){
                            if($ContactPhone->phone_type == "Home"){
                                $data['phone_Home'] = $ContactPhone->phone_number;
                            }
                            if($ContactPhone->phone_type == "Mobile"){
                                $data['phone_Mobile'] = $ContactPhone->phone_number;
                            }
                            if($ContactPhone->phone_type == "Work"){
                                $data['phone_Work'] = $ContactPhone->phone_number;
                            }
                        }

                        $ContactEmail = $_SESSION['ContactEditSave']->getChildContactEmail();
                        while($ContactEmail->next()){
                            if($ContactEmail->email_type == "Home"){
                                $data['email_Home'] = $ContactEmail->email_address;
                            }
                            if($ContactEmail->email_type == "Other"){
                                $data['email_Other'] = $ContactEmail->email_address;
                            }
                            if($ContactEmail->email_type == "Work"){
                                $data['email_Work'] = $ContactEmail->email_address;
                            }
                        }

                        $ContactAddr = $_SESSION['ContactEditSave']->getChildContactAddress();
                        while($ContactAddr->next()){
                            if($ContactAddr->address_type == "Home"){
                                $data['addr_Home'] = $ContactAddr->address;
                            }
                            if($ContactAddr->address_type == "Other"){
                                $data['addr_Other'] = $ContactAddr->address;
                            }
                            if($ContactAddr->address_type == "Work"){
                                $data['addr_Work'] = $ContactAddr->address;
                            }
                        }

                        $contacts[] = $data;
                    }
                    //frees connexion object
                    $this->free();
                    return  $contacts;
                 }
        }

		/**
		 * retrieves single contact from Gmail associated with a User.
		 *
		 * @param string : contact self link
		 */

        function retreiveSingleContact($link_self){
                global $_SESSION, $_GET;
                $client = $this->client;
                //This is to fetch single contact which is to be updated.
                // Create a Gdata object using the authenticated Http Client
                $gdata = new Zend_Gdata($client);
                try{	
                        $entry = $gdata->getEntry($link_self);

                        $data = $this->pushContactIntoArray($entry);
                        $contacts[] = $data;
                        return $contacts;
                } catch (Exception $e){
                      $status_code = $gdata->getHttpClient()->getLastResponse()->getStatus();
                      $this->status_code_desc = $this->getStatusDescription($status_code);   
                }
        }

		/**
		 * retrieves email ids for all the contacts from Gmail and stores in Ofuz DB temprorily.
		 *
		 * @return void
		 * @see Gdata
		 */

        function storeGmailEmailsTemprorily()
        {
            global $_SESSION, $_GET;
            $client = $this->client;
			$useremailid = urlencode($_SESSION["uEmail"]);
            // Create a Gdata object using the authenticated Http Client
            $gdata = new Zend_Gdata($client);
            //$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/'.$_SESSION["uEmail"].'%40gmail.com/full');

			$query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/'.$useremailid.'/full');
            $query->setMaxResults(1000);
            try{
                $feed = $gdata->getFeed($query);

                if($feed){
                    $q_email = new sqlQuery($this->getDbCon());
                    foreach ($feed as $entry) {
                        // retrieving emails
                        $extensionElements = $entry->getExtensionElements();
                    
                        foreach ($extensionElements as $extensionElement) {
                            //emails
                            if ($extensionElement->rootNamespaceURI ==
                            "http://schemas.google.com/g/2005"
                            && $extensionElement->rootElement == "email") {
                                $attributes = $extensionElement->getExtensionAttributes();
                                if (array_key_exists('address', $attributes)) {
                                    if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#home")
                                    {
                                            //$data['em_home'] = $attributes['address']['value'];
                                            $sql_email_ins = "INSERT INTO 
                                                              temp_gmail_emails(email_address)
                                                              VALUES('".$attributes['address']['value']."')
                                                            ";
                                            $q_email->query($sql_email_ins);
                                    }
                                    if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#work")
                                    {
                                            $sql_email_ins = "INSERT INTO 
                                                              temp_gmail_emails(email_address)
                                                              VALUES('".$attributes['address']['value']."')
                                                            ";
                                            $q_email->query($sql_email_ins);
                                    }
                                    if($attributes['rel']['value'] == "http://schemas.google.com/g/2005#other")
                                    {
                                            $sql_email_ins = "INSERT INTO 
                                                              temp_gmail_emails(email_address)
                                                              VALUES('".$attributes['address']['value']."')
                                                            ";
                                            $q_email->query($sql_email_ins);
                                    }					
                                }
                            }
                        }
                    }
                    $q_email->free();
                }

            } catch (Exception $e){
                $status_code = $gdata->getHttpClient()->getLastResponse()->getStatus();
                $this->status_code_desc = $this->getStatusDescription($status_code);
            }
        }

        /**
		 * checks if the imported contact's email id already exists in temp table in Ofuz DB.
		 *
		 * @param array : contact
		 * @return boolean
		 */

		function checkEmailExistsInTempTable($entry){
            $str = "";
            $exclude_contact = "";
            //this for updating contact where this contact should not be considered.
            /*if($idcontact){
                //$exclude_contact = "AND idcontact NOT IN (".$idcontact.")";
				$exclude_contact = "AND email_address NOT IN (".$str.")";
            } */

            if($entry['email_Home'])
            {
                $str .= "'".$entry['email_Home']."',";
            }
            if($entry['email_Other'])
            {
                $str .= "'".$entry['email_Other']."',";
            }
            if($entry['email_Work'])
            {
                $str .= "'".$entry['email_Work']."'";
            }
			
            /*if($idcontact){
                //$exclude_contact = "AND idcontact NOT IN (".$idcontact.")";
				$exclude_contact = "AND email_address NOT IN (".$str.")";
            }*/

            if($str != ""){
                //removing ',' if it exists as last character
                $len = strlen($str);
                $val = $str[$len-1] == ',' ? true : false;
                if($val){
                    $str = substr($str, 0, -1);
                }

                $q_email = new sqlQuery($this->getDbCon());
                $sql_check_email = "SELECT *
                                    FROM temp_gmail_emails
                                    WHERE
                                    email_address IN (".$str.")
                                  ";

                $q_email->query($sql_check_email);
                if($q_email->getNumRows() > 0)
                {
                    $q_email->free();
                    return true;
                } else{
                          $q_email->free();
                          return false;
                }
            }

            return false;
        }
/*
        function dispOfuzContactsNotSync(){
                $ofuz_contacts = $this->getOfuzContactsNotInGmail();
                return $ofuz_contacts;
        }
*/

		/**
		 * Formatting sync date according to the Gmail feed.
		 * This date is sent to Gmail to retrieve contacts after this date.
		 *
		 * @param date
		 * @return date
		 */

        function formatDateForGoogle($sync_date){
            list($dt,$time) = split(' ',$sync_date);
            $updated_min = $dt."T".$time;
            return $updated_min;
        }

		/**
		 * checking if the contact already exists with same email id in Ofuz Database.
		 *
		 * @param array, int : contact details, Contact id (to be checked).
		 * @return boolean
		 */

        function checkContactWithSameEmail($entry, $id_contact=null){            
            $q_email = new sqlQuery($this->getDbCon());
            if($id_contact){
                $q_id_con = " AND ce.idcontact NOT IN(".$id_contact.")";
            }
            if($entry['em_other']){
                $sql_check_email = "SELECT c.idcontact
                                    FROM contact c
                                    LEFT JOIN contact_email ce ON c.idcontact = ce.idcontact
                                    WHERE c.iduser=".$this->id_user." AND ce.email_address='".$entry['em_other']."'
                                    ".$q_id_con;

                $q_email->query($sql_check_email);
                if($q_email->getNumRows() > 0){
                    return true;
                } else{
                    return false;
                }
            }
            if($entry['em_home']){
                $sql_check_email = "SELECT c.idcontact
                                    FROM contact c
                                    LEFT JOIN contact_email ce ON c.idcontact = ce.idcontact
                                    WHERE c.iduser=".$this->id_user." AND ce.email_address='".$entry['em_home']."'
                                    ".$q_id_con;
                $q_email->query($sql_check_email);
                if($q_email->getNumRows() > 0){
                    return true;
                } else{
                    return false;
                }
            }
            if($entry['em_work']){
                $sql_check_email = "SELECT c.idcontact
                                    FROM contact c
                                    LEFT JOIN contact_email ce ON c.idcontact = ce.idcontact
                                    WHERE c.iduser=".$this->id_user." AND ce.email_address='".$entry['em_work']."'
                                    ".$q_id_con;
                $q_email->query($sql_check_email);
                if($q_email->getNumRows() > 0){
                    return true;
                } else{
                    return false;
                }
            }
        }

		/**
		 * Status code and description table
		 * NOT IN USE.
		 */

        function getStatusCodeTable(){
        $statusCodeTable = "
        <HTML>
        <HEAD>
        <TITLE>Status Code</TITLE>
        </HEAD>
        <BODY>
            <TABLE WIDTH='500' ALIGN='CENTER'>
                <TR><TD colspan='2' ALIGN='RIGHT'><a href='sync.php'>Back to Sync</a></TD></TR>
            </TABLE>
            <TABLE WIDTH='500' ALIGN='CENTER' BGCOLOR='#99BEE3'>
                <TR><TD colspan='2' BGCOLOR='#5396DA'> Client Error 4xx</TD></TR>
                <TR><TD>400</TD><TD>Bad Request</TD></TR>
                <TR><TD>401</TD><TD>Unauthorized</TD></TR>
                <TR><TD>402</TD><TD>Payment Required</TD></TR>
                <TR><TD>403</TD><TD>Forbidden</TD></TR>
                <TR><TD>404</TD><TD>Not Found</TD></TR>
                <TR><TD>405</TD><TD>Method Not Allowed</TD></TR>
                <TR><TD>406</TD><TD>Not Acceptable</TD></TR>
                <TR><TD>407</TD><TD>Proxy Authentication Required</TD></TR>
                <TR><TD>408</TD><TD>Request Timeout</TD></TR>
                <TR><TD>409</TD><TD>Conflict</TD></TR>
                <TR><TD>410</TD><TD>Gone</TD></TR>
                <TR><TD>411</TD><TD>Length Required</TD></TR>
                <TR><TD>412</TD><TD>Precondition Failed</TD></TR>
                <TR><TD>413</TD><TD>Request Entity Too Large</TD></TR>
                <TR><TD>414</TD><TD>Request-URI Too Long</TD></TR>
                <TR><TD>415</TD><TD>Unsupported Media Type</TD></TR>
                <TR><TD>416</TD><TD>Requested Range Not Satisfiable</TD></TR>
                <TR><TD>417</TD><TD>Expectation Failed</TD></TR>
        
                <TR><TD colspan='2' BGCOLOR='#5396DA'> Server Error 5xx</TD></TR>
                <TR><TD>500</TD><TD>Internal Server Error</TD></TR>
                <TR><TD>501</TD><TD>Not Implemented</TD></TR>
                <TR><TD>502</TD><TD>Bad Gateway</TD></TR>
                <TR><TD>503</TD><TD>Service Unavailable</TD></TR>
                <TR><TD>504</TD><TD>Gateway Timeout</TD></TR>
                <TR><TD>505</TD><TD>HTTP Version Not Supported</TD></TR>
                <TR><TD>509</TD><TD>Bandwidth Limit Exceeded</TD></TR>
            </TABLE>
        </BODY>
        </HTML>
                ";
        return $statusCodeTable;
        }


		/**
		 * retrieves the Description of the Status depending on the status code.
		 *
		 * @param int : status code
		 * @return string : status description
		 */

        function getStatusDescription($status_code){
            $messages = array(
                    // Informational 1xx
                    100 => 'Continue',
                    101 => 'Switching Protocols',
            
                    // Success 2xx
                    200 => 'OK',
                    201 => 'Created',
                    202 => 'Accepted',
                    203 => 'Non-Authoritative Information',
                    204 => 'No Content',
                    205 => 'Reset Content',
                    206 => 'Partial Content',
                    207 => 'Your Gmail Contacts have been imported successfully.',
                    208 => 'Your Ofuz Contacts have been exported successfully.',
            
                    // Redirection 3xx
                    300 => 'Multiple Choices',
                    301 => 'Moved Permanently',
                    302 => 'Found',  // 1.1
                    303 => 'See Other',
                    304 => 'Not Modified',
                    305 => 'Use Proxy',
                    // 306 is deprecated but reserved
                    307 => 'Temporary Redirect',
            
                    // Client Error 4xx
                    400 => 'Bad Request',
                    401 => 'Unauthorized',
                    402 => 'Payment Required',
                    403 => 'The email address is not correct.',
                    404 => 'Not Found',
                    405 => 'Method Not Allowed',
                    406 => 'Not Acceptable',
                    407 => 'Proxy Authentication Required',
                    408 => 'Request Timeout',
                    409 => 'Conflict',
                    410 => 'Gone',
                    411 => 'Length Required',
                    412 => 'Precondition Failed',
                    413 => 'Request Entity Too Large',
                    414 => 'Request-URI Too Long',
                    415 => 'Unsupported Media Type',
                    416 => 'Requested Range Not Satisfiable',
                    417 => 'Expectation Failed',
            
                    // Server Error 5xx
                    500 => 'Internal Server Error',
                    501 => 'Not Implemented',
                    502 => 'Bad Gateway',
                    503 => 'Service Unavailable',
                    504 => 'Gateway Timeout',
                    505 => 'HTTP Version Not Supported',
                    509 => 'Bandwidth Limit Exceeded'
                );
            return $messages[$status_code];
        }
/*
    function retrieveContactsPhotos()
    {

        global $_SESSION, $_GET;
        $client = $this->client;

        // Create a Gdata object using the authenticated Http Client
        $gdata = new Zend_Gdata($client);

        $query = new Zend_Gdata_Query('http://www.google.com/m8/feeds/contacts/'.$_SESSION["uEmail"].'%40gmail.com/full');
        $query->setMaxResults(1000);
        //parameters: tableName, iduser : checking for User
        //$ret_value = $this->checkUserExistence("company", $this->id_user);
        $syncronized = $this->isSyncronized('g_o');    //mode 'g_o' says 'google to ofuz'
        if($syncronized){
            $sync_date = $this->getLastSyncDate('g_o');
            $updated_min = $this->formatDateForGoogle($sync_date);
            $query->setUpdatedMin($updated_min); //to fetch contacts after last updated date
        }
        $feed = $gdata->getFeed($query);
        $this->setLog("\n---------google Feed ------\n".$feed."\n-----------end feed-----\n");        

        foreach ($feed as $entry) {
            $data = $this->pushContactIntoArray($entry);
            $contacts[] = $data;
        }
        if($contacts){
            foreach($contacts as $entry){
                if($entry['link_photo']){
                   $this->retreivePhoto($entry['link_photo']);
                }
            }
        }   
    }

    function retreivePhoto($link_photo){
        global $_SESSION, $_GET;
        $client = $this->client;

        // Create a Gdata object using the authenticated Http Client
        $gdata = new Zend_Gdata($client);

        //$query = new Zend_Gdata_Query($link_photo);
        // $query->setMaxResults(1000);
        $feed = $gdata->get($link_photo);
        print_r($feed);exit();
        foreach ($feed as $entry) {

        }

    }
*/

	/**
	 * adding/updating the synchronization date in Ofuz Database as soon as any sync(import/export) happens.
	 *
	 * @return void
	 */

	function addUpdateSyncDate() {

		$q_ins_contact_sync = new sqlQuery($this->getDbCon());
		//insert/update sync date for OFUZ
		$syncronized = $this->isSyncronized('o_g');   // mode 'o_g' says 'ofuz to google'
		$last_updated = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")) - 60*5);

		if($syncronized){
			$sql_ins = "UPDATE google_contact SET last_updated = '".$last_updated."'
						WHERE iduser=".$this->id_user." AND mode='o_g'";
		} else{
				$sql_ins = "INSERT INTO
							google_contact(iduser,last_updated,mode)
							VALUES(".$this->id_user.",'".$last_updated."','o_g')
						";
		}
		$q_ins_contact_sync->query($sql_ins);
		$q_ins_contact_sync->free();

	}

}//end of class
