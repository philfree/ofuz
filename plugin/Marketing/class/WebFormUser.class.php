<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
    /**
     * WebFormField class
     * Using the DataObject
     */
   
class WebFormUser extends DataObject {
    
    public $table = "webformuser";
    protected $primary_key = "idwebformuser";

    function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_WEBFORM);
		}
    }    
	
    function eventCheckEmptyFields(Eventcontroler $evtcl){
            $field_selected = $evtcl->field_selected;

            if(empty($field_selected)){ 
                  $_SESSION['in_page_message'] = _("Please select some labels");
                  $evtcl->doSave = 'no';
                  $evtcl->goto = 'settings_wf.php';
                  $dispError = new Display("settings_wf.php");
                  $dispError->addParam("e", 'yes');
                  $evtcl->setDisplayNext($dispError);
            }
    }

	function eventAddFormFields(EventControler $event_controler) {
		$idwebformuser = $this->getPrimaryKeyValue();
		$field_selected = $event_controler->field_selected;
		$field_label = $event_controler->field_label;
		$field_size = $event_controler->field_size;
		$field_required = $event_controler->field_required;
		if(!empty($field_selected)){ 
                    foreach ($field_selected as $field_name=>$checked) {
                            if ($checked == "Yes") {
                                    $do_userfields = new WebFormUserField();
                                    $do_userfields->idwebformuser = $idwebformuser;
                                    $do_userfields->name = $field_name;
                                    $do_userfields->label = $field_label[$field_name];
                                    $do_userfields->size = $field_size[$field_name];
                                    $do_userfields->required = $field_required[$field_name];
                                    $do_userfields->add();
                            }
                    }	
                }	
	}
	
	/**
	 *  displayWebForm
	 *  Display the webform within OFuz.net so we can use all the ofuz / radria field type
	 */
	function displayWebFormFields() {
		$out = '<table>';

		$do_webform_userfields = new WebFormUserField();
		$do_webform_userfields->query("SELECT wfu.name, wfu.label, wfu.required, wfu.size, wff.field_type 
		                                 FROM webformfields as wff, webformuserfield as wfu 
										 WHERE wff.name=wfu.name
										   AND wfu.idwebformuser = ".$this->getPrimaryKeyValue()."
										 ORDER BY wff.display_order");
		$field_format = new Fields();
		while ($do_webform_userfields->next()) {
			$field_format->addField($do_webform_userfields->name, $do_webform_userfields->field_type);
			if ($do_webform_userfields->size) { 
				 if ($do_webform_userfields->field_type == 'FieldTypeText') {
				    $field_format->fields[$do_webform_userfields->name]->textarea = $do_webform_userfields->size;	 	 
				 } else {
					$field_format->fields[$do_webform_userfields->name]->size = $do_webform_userfields->size;
				 }
		    }
		}
		//print_r($field_format);
		$do_webform_userfields->first();		
		//$do_webform_userfields->newForm();
		//$do_webform_userfields->setFields($field_format);
		//$do_webform_userfields->setApplyFieldFormating(true);
		
		while ($do_webform_userfields->next()) {			
			$out .= "\n".'<tr><td class="webform_row_label">'.$do_webform_userfields->label.
			'</td><td class="webform_row_field">'.$field_format->applyRegToForm($do_webform_userfields->name, '').'</td></tr>';
		}
		$out .=	'</table>';
		return $out;
	}
	
	/**
	 *   displayWebFormEmbed
	 *  This will generate a webform for the javascript embed
	 *  We will need to limit to html standard fieldtype (no dojo/jquery)
	 */
	
	function displayWebFormEmbed() {
		
	}
	
	
	/**
	 * saveWebForm()
	 * this will check if contact exists with firname, lastname, name and email 
	 * If exist add to it, if doesn't exists create a new one.
	 */
	
	function eventAddContact(EventControler $event_controler) {
	
	
		//$fields = $_REQUEST['fields']; 
		$fields = $event_controler->fields;
		
		$this->setLog("\n eventAddContact, creating new contact from form, using ".count($fields)." fields. (".date("Y/m/d H:i:s").")");
		//$dropcode = $_POST['dropcode'];
		
		if(isset($this->iduser)){
		$do_contact = new Contact();
		$do_contact->iduser = $this->iduser;
		$do_contact->add();
		$this->setLog("\n new contact:".$do_contact->idcontact." for user:".$this->iduser);
		} else {
		$do_contact = new Contact();
		$do_contact->addNew();
		$do_contact->iduser = $event_controler->uid;
		
		$do_contact->add();  //print_r($fields);exit();
		$this->setLog("\n new contact:".$do_contact->idcontact." for user:".$event_controler->uid);	
		}	
		
		foreach ($fields as $field_name => $field_value) {
    $this->setLog("\n Processing field:".$field_name." with value:".$field_value);
    if(isset($this->idwebformuser)){
      $do_webform_fields = new WebFormUserField();
      $do_webform_fields->query("SELECT wfu.name, wff.class as class_name, wff.variable, wff.variable_type, wfu.required 
		                                FROM webformfields as wff, webformuserfield as wfu 
                                  WHERE wff.name=wfu.name
                                  AND wfu.name = '".$field_name."'
                                  AND wfu.idwebformuser= ".$this->idwebformuser);
    } else {
      $do_webform_fields = new WebFormUserField();
      $do_webform_fields->query("SELECT wfu.name, wff.class as class_name, wff.variable, wff.variable_type, wfu.required 
                                  FROM webformfields as wff, webformuserfield as wfu 
                                  WHERE wff.name=wfu.name
                                  AND wfu.name = '".$field_name."'
                                  AND wfu.idwebformuser= ".$event_controler->fid);
    }
    $this->setLog("\n Field information class:".$do_webform_fields->class_name." Variable:".$do_webform_fields->variable); 
    $this->setLog("\n rows:".$do_webform_fields->getNumRows());      
    if ($do_webform_fields->getNumRows() == 1) {
      if ($do_webform_fields->class_name == "Contact") {
        $this->setLog("\n     Updating contact");
        $do_contact->{$do_webform_fields->variable} = $field_value;
        $do_contact->update();
      } else {
        $update = false;
        if (is_object(${'sub_'.$do_webform_fields->class_name})) {
          if (${'sub_'.$do_webform_fields->class_name}->getType() == $do_webform_fields->variable_type) {
            $update = true;
          }
        }
        if ($update) {
          $this->setLog("\n     Updating class:".$do_webform_fields->class_name);
          $obj = ${'sub_'.$do_webform_fields->class_name};
          $obj->{$do_webform_fields->variable} = $field_value;
          $obj->update();
        } else {
          $class_name = $do_webform_fields->class_name;
          ${'sub_'.$class_name} = new $class_name();
          $obj = ${'sub_'.$class_name};
          $obj->{$do_webform_fields->variable} = $field_value;
          if (method_exists($obj, "setType") && strlen($do_webform_fields->variable_type) > 0) {
            $obj->setType($do_webform_fields->variable_type);
          }
          $obj->idcontact = $do_contact->getPrimaryKeyValue();
          $obj->iduser = $event_controler->uid ;
          $obj->add();
        }
      }
    }	
  }
		if(isset($this->iduser)){
		$contact_view = new ContactView() ;
		$contact_view->setUser($this->iduser);
		$contact_view->addFromContact($do_contact);
		} else {
		$contact_view = new ContactView() ;
		$contact_view->setUser($event_controler->uid);
		$contact_view->addFromContact($do_contact);
		}
		
		if(isset($this->tags)){
			$tags = explode(",", $this->tags);
		
			foreach ($tags as $tag) {
				$tag = trim($tag);
				$do_tag = new Tag();
				$do_tag->addNew(); 
				$do_tag->addTagAssociation($do_contact->getPrimaryKeyValue(), $tag, "contact", $this->iduser);
				$contact_view->addTag($tag);
			}
			if (strlen($this->urlnext) > 0) {
				$event_controler->setUrlNext($this->urlnext);
			} else {
				$event_controler->setUrlNext($GLOBALS['cfg_ofuz_site_http_base'].'web_form_thankyou.php');
			}
			$event_controler->addParam("do_contact", $do_contact);
		} else {
			$sql = "SELECT * FROM {$this->table} WHERE idwebformuser=$event_controler->fid";
			$this->query($sql);
			while($this->fetch()){
			$tags = $this->getData("tags");
			$urlnext = $this->getData("urlnext");
			}
			
			$tags = explode(",", $tags);
		
			foreach ($tags as $tag) {
				$tag = trim($tag);
				$do_tag = new Tag();
				$do_tag->addNew(); 
				$do_tag->addTagAssociation($do_contact->getPrimaryKeyValue(), $tag, "contact", $this->iduser);
				$contact_view->addTag($tag);
			}
			if (strlen($urlnext) > 0) {
				$event_controler->setUrlNext($urlnext);
			} else {
				
				
				$url = $GLOBALS['cfg_ofuz_site_http_base'].'web_form_thankyou.php';
				//$event_controler->setUrlNext($url);
				//header("location:$url");
				$err_disp = new Display($url);
				$event_controler->setDisplayNext($err_disp);
				$event_controler->doForward();
			}
			//$event_controler->addParam("do_contact", $do_contact);
		}
	}
	
	function eventSendEmailAlert(EventControler $event_controler) {
		if($this->email_alert == 'y') {
			$fields = $event_controler->fields;
			$do_contact = $event_controler->do_contact;
			$fields_content_html = '';
			$fields_content_text = '';
			foreach ($fields as $field_name => $field_value) {
				if (strlen($field_value) > 0 && strlen($field_name) > 0) { 
					$fields_content_html .= '<br>'.$field_name.': '.$field_value ;
					$fields_content_text .= "\n".$field_name.": ".$field_value;
				}
			}
			$email_values['fields_content_html'] = $fields_content_html;
			$email_values['fields_content_text'] = $fields_content_text;
			$email_values['firstname'] = $do_contact->firstname;
			$email_values['webformname'] = $this->title;
			$email_values['contact_url'] = $GLOBALS['cfg_ofuz_site_http_base'].'Contact/'.$do_contact->idcontact;
			$email_template = new EmailTemplate("web form email alert");
			$do_user = new User();
			$do_user->getId($this->iduser);
			$do_contact_email = $do_contact->getChildContactEmail();
			$contact_email = $do_contact_email->getDefaultEmail();
			if (strlen($contact_email) < 3) { $contact_email = $GLOBALS['cfg_ofuz_email_support'];  }
			$this->setLog("\n Sending webform alert to: ".$do_user->getFullName()." with ".$do_user->email." for new contact:".$do_contact->firstname);
			$email_template->setFrom($contact_email, $do_contact->firstname." ".$do_contact->lastname );
			
			$do_user->sendMessage($email_template, $email_values);
			
		}
		
	}

        function getUsersWebForms(){
            $this->query("select * from ".$this->table ." where iduser = ".$_SESSION['do_User']->iduser);
        }

        function eventDeleteWebForm(EventControler $evtcl){
            $this->getId($evtcl->id);
            $this->delete();
        }
        
        function isWebFormOwner($id){
            $q = new sqlQuery($this->getDbCon());
            $q->query("select * from ".$this->table." where ".$this->primary_key."= ".$id." AND iduser = ".$_SESSION['do_User']->iduser );
            if($q->getNumRows()){
                return true;
            }else{
                return false;
            }
        }

        function eventDeleteWebFormFields(EventControler $evtcl) {
		$idwebformuser = $this->getPrimaryKeyValue();
		$q = new sqlQuery($this->getDbCon());
                if($idwebformuser){
                  $q->query("delete from webformuserfield where idwebformuser = ".$idwebformuser);  
                }		
	}
	
	
	
	/**
	 * saveWebForm()
	 * this will check if contact exists with firname, lastname, name and email 
	 * If exist add to it, if doesn't exists create a new one.
	 */
	
	function posteventAddContact($fid,$fields,$nxturl,$uid,$tags) {
	//echo 'hh';die();
	
		//$fields = $_REQUEST['fields']; 
		//$fields = $event_controler->fields;
		
		$this->setLog("\n eventAddContact, creating new contact from form, using ".count($fields)." fields. (".date("Y/m/d H:i:s").")");
		//$dropcode = $_POST['dropcode'];
		
		$do_contact = new Contact();
		$do_contact->iduser = $uid;
		$do_contact->add();
		$this->setLog("\n new contact:".$do_contact->idcontact." for user:".$uid);
		
		foreach ($fields as $field_name => $field_value) {
			$this->setLog("\n Processing field:".$field_name." with value:".$field_value);
			$do_webform_fields = new WebFormUserField();
			$do_webform_fields->query("SELECT wfu.name, wff.class as class_name, wff.variable, wff.variable_type, wfu.required 
		                                   FROM webformfields as wff, webformuserfield as wfu 
										   WHERE wff.name=wfu.name
										     AND wfu.name = '".$field_name."'
											 AND wfu.idwebformuser= ".$fid);
	        $this->setLog("\n Field information class:".$do_webform_fields->class_name." Variable:".$do_webform_fields->variable); 
			$this->setLog("\n rows:".$do_webform_fields->getNumRows() );      
		    if ($do_webform_fields->getNumRows() == 1) {
				if ($do_webform_fields->class_name == "Contact") {
					$this->setLog("\n     Updating contact");
					$do_contact->{$do_webform_fields->variable} = $field_value;
                    $do_contact->update();
				} else {
					$update = false;
					if (is_object(${'sub_'.$do_webform_fields->class_name})) {
						if (${'sub_'.$do_webform_fields->class_name}->getType() == $do_webform_fields->variable_type) {
							$update = true;
						}
					}
					if ($update) {
						$this->setLog("\n     Updating class:".$do_webform_fields->class_name);
						$obj = ${'sub_'.$do_webform_fields->class_name};
						$obj->{$do_webform_fields->variable} = $field_value;
						$obj->update();
					} else {
						$class_name = $do_webform_fields->class_name;
						${'sub_'.$class_name} = new $class_name();
						$obj = ${'sub_'.$class_name};
						$obj->{$do_webform_fields->variable} = $field_value;
						if (method_exists($obj, "setType") && strlen($do_webform_fields->variable_type) > 0) {
							$obj->setType($do_webform_fields->variable_type);
						}
						$obj->idcontact = $do_contact->getPrimaryKeyValue();
						$obj->add();
					}
				}
			}	
		}
		$contact_view = new ContactView() ;
		$contact_view->setUser($uid);
		$contact_view->addFromContact($do_contact);
		
		$tags = explode(",", $tags);
		
		foreach ($tags as $tag) {
			$tag = trim($tag);
			$do_tag = new Tag();
			$do_tag->addNew(); 
			$do_tag->addTagAssociation($do_contact->getPrimaryKeyValue(), $tag, "contact", $fid);
			$contact_view->addTag($tag);
        }
		if (strlen($nexturl) > 0) {
			//$event_controler->setUrlNext($this->urlnext);
			header("location:$nxturl");
		} else {
			//$event_controler->setUrlNext($GLOBALS['cfg_ofuz_site_http_base'].'web_form_thankyou.php');
			$url = $GLOBALS['cfg_ofuz_site_http_base'].'web_form_thankyou.php';
			header("location:$url");
		}
		//$event_controler->addParam("do_contact", $do_contact);
	}
	
}	
?>
