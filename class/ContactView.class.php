<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


    /**
     * ContactView class
     * Using the DataObject
     * @author Philippe Lewicki, SQLFusion LLC, info@sqlfusion.com
	 * 
+---------------+--------------+------+-----+---------------------+-------+
| Field         | Type         | Null | Key | Default             | Extra |
+---------------+--------------+------+-----+---------------------+-------+
| idcontact     | int(10)      | NO   |     | 0                   |       | 
| firstname     | varchar(50)  | NO   | MUL |                     |       | 
| lastname      | varchar(60)  | NO   | MUL |                     |       | 
| company       | varchar(70)  | NO   | MUL |                     |       | 
| idcompany     | int(10)      | NO   |     |                     |       | 
| position      | varchar(60)  | NO   |     |                     |       | 
| picture       | varchar(200) | NO   |     |                     |       | 
| email_address | varchar(180) | YES  |     | NULL                |       | 
| phone_number  | varchar(30)  | YES  |     | NULL                |       | 
| tags          | varchar(250) | NO   | MUL |                     |       | 
| last_activity | timestamp    | NO   | MUL | 0000-00-00 00:00:00 |       | 
| last_update   | timestamp    | NO   |     | 0000-00-00 00:00:00 |       | 
| first_created | timestamp    | NO   |     | 0000-00-00 00:00:00 |       | 
+---------------+--------------+------+-----+---------------------+-------+
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzCore
      * @license ##License##
      * @version 0.7
      * @date 2011-05-13
      * @since 0.1
     */

class ContactView extends DataObject {
    
    public $table = "";
    protected $primary_key = "idcontact";
    public $sql_view_name;
	public $iduser;

    function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_CONTACT);
		}
		if (isset($_SESSION['do_User'])) {
				$this->table = "userid".$_SESSION['do_User']->iduser."_contact";
				$this->sql_view_name = $this->table;
				$this->iduser = $_SESSION['do_User']->iduser;
		}		
    } 
	
	public function setUser($iduser) {
		$this->table = "userid".$iduser."_contact";
		$this->sql_view_name = $this->table;
		$this->iduser = $iduser;
	}
	
	public function rebuildContactUserTable($iduser=0) {
		if (empty($iduser)) {
            $iduser = $this->iduser;
        }
		$this->setUser($iduser);
		
		
		//$this->sql_view_name = "userid".$iduser."_contact";
       
		$this->query("DROP VIEW IF EXISTS ".$this->sql_view_name);
		$this->query("DROP TABLE IF EXISTS ".$this->sql_view_name);

		$this->query("CREATE TABLE `".$this->sql_view_name."` (
			`idcontact` int(10) NOT NULL default '0',
			`firstname` varchar(50) NOT NULL,
			`lastname` varchar(60) NOT NULL,
			`company` varchar(70) NOT NULL,
			`idcompany` int(10) NOT NULL,
			`position` varchar(60) NOT NULL,
			`picture` varchar(200) NOT NULL,
			`email_address` varchar(180),
			`phone_number` varchar(30),
			`tags` varchar(250) NOT NULL,
			`last_activity` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
			`last_update` timestamp NOT NULL default '0000-00-00 00:00:00',
			`first_created` timestamp NOT NULL default '0000-00-00 00:00:00',
			KEY `firstname` (`firstname`),
			KEY `lastname` (`lastname`),
			KEY `company` (`company`), 
			KEY `tags` (`tags`), 
			KEY `last_activity` (`last_activity`)) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		//echo $this->getSqlQuery();
		//		exit;
		
		$contacts = new Contact();
		$contacts->getUserContacts($this->iduser);
		$this->setLog("\n importing Contacts:".$contacts->getNumRows());
		while($contacts->next()) {
		    $this->setLog("\n name:".$contacts->firstname);
			$this->rebuildAddContact($contacts);
		}
		/**
		$contacts->query("SELECT contact.idcontact as idcontact,         
                                contact.firstname as firstname,         
                                contact.lastname as lastname,        
                                contact.company as company,   
                                contact.idcompany as idcompany,     
                                contact.position as position,        
                                contact.picture as picture                                                         
                            FROM contact,		
							     contact_sharing							  
                            WHERE `contact_sharing`.`idcoworker` = ".$this->iduser."
                            AND contact.idcontact = contact_sharing.idcontact");
        while($contacts->next) {
			$this->rebuildAddContact($contacts);
		}
		**/
		/**
		$this->query("SELECT contact.idcontact as idcontact,         
                                contact.firstname as firstname,         
                                contact.lastname as lastname,        
                                contact.company as company,   
                                contact.idcompany as idcompany,     
                                contact.position as position,
                                contact.picture as picture,
                                contact_email.email_address as email_address, 
                                contact_phone.phone_number,
								GROUP_CONCAT(DISTINCT tag.tag_name ORDER BY tag.tag_name SEPARATOR ',') as tags,
								activity.when,
								created_date_log.created_date,
								updated_date_log.updatedate
                            FROM  contact LEFT JOIN contact_email ON contact.idcontact = contact_email.idcontact 
                                          LEFT JOIN contact_phone ON contact.idcontact = contact_phone.idcontact 
										  LEFT JOIN tag ON (contact.idcontact = tag.idreference AND reference_type='contact' AND tag.iduser=".$iduser.") 
										  LEFT JOIN activity ON (contact.idcontact = activity.idcontact)
										  LEFT JOIN created_date_log ON (created_date_log.id = contact.idcontact AND created_date_log.table_name='contact')
										  LEFT JOIN updated_date_log ON (updated_date_log.primarykeyvalue = contact.idcontact AND updated_date_log.tablename='contact')
                            WHERE contact.iduser = ".$iduser."
                            GROUP BY contact.idcontact
							
							UNION
							
							SELECT contact.idcontact as idcontact,         
                                contact.firstname as firstname,         
                                contact.lastname as lastname,        
                                contact.company as company,   
                                contact.idcompany as idcompany,     
                                contact.position as position,        
                                contact.picture as picture,
                                contact_email.email_address as email_address, 
                                contact_phone.phone_number,         
                                GROUP_CONCAT(DISTINCT tag.tag_name ORDER BY tag.tag_name SEPARATOR ',') as tags,
								activity.when,
								created_date_log.created_date,
								updated_date_log.updatedate 
                            FROM contact        LEFT JOIN tag ON (contact.idcontact = tag.idreference AND reference_type='contact' AND tag.iduser=".$iduser."),		
							     contact_sharing LEFT JOIN contact_email ON contact_sharing.idcontact = contact_email.idcontact 
                                                 LEFT JOIN contact_phone ON contact_sharing.idcontact = contact_phone.idcontact 
												 LEFT JOIN activity ON (contact_sharing.idcontact = activity.idcontact)
												 LEFT JOIN created_date_log ON (created_date_log.id = contact_sharing.idcontact AND created_date_log.table_name='contact')
												 LEFT JOIN updated_date_log ON (updated_date_log.primarykeyvalue = contact_sharing.idcontact AND updated_date_log.tablename='contact')							  
                            WHERE `contact_sharing`.`idcoworker` = ".$iduser."
                            AND contact.idcontact = contact_sharing.idcontact
			    GROUP BY contact.idcontact
							");
			$this->setLog("\n---------\n Users contact: ".$this->getSqlQuery()."\n----------\n");
			$q_insert = new sqlQuery($this->getDbCon());
			while($this->next()) {
				$q_insert->query("INSERT INTO ".$this->sql_view_name." 
				                  VALUES (".$this->idcontact.", 
										  '".addslashes($this->firstname)."',
										  '".addslashes($this->lastname)."',
										  '".addslashes($this->company)."',
										   ".$this->idcompany.",
										  '".addslashes($this->position)."',
										  '".$this->picture."',
										  '".$this->email_address."',
										  '".$this->phone_number."',
										  '".addslashes($this->tags)."',
										  '".$this->when."',
										  '".$this->created_date."',
										  '".$this->updatedate."')");		
			    $this->setLog("\n ".$q_insert->getSqlQuery());		
			}

			//$this->setLog("\n Create table contact:".$this->getSqlQuery());
			*/
			
			$_SESSION['refresh_contacts'] = false;
			$_SESSION['contact_view_name'] = $this->sql_view_name;
	}
	
	private function rebuildAddContact(Contact $contact) {
		$this->idcontact = $contact->idcontact;
		$this->firstname = $contact->firstname;
		$this->lastname = $contact->lastname;
		$this->company = $contact->company;
		$this->idcompany = $contat->idcompany;
		$this->position = $contact->position;
		$this->picture = $contact->picture;
		$emails = $contact->getChildContactEmail();
        $this->email_address = $emails->getDefaultEmail();
        $phone = $contact->getChildContactPhone();
        $this->phone_number = $phone->phone_number;
		$do_tags = new Tag();
		$do_tags->getUserContactTags($this->iduser, $contact->idcontact);
		while($do_tags->next()) {
			$tags .= trim($do_tags->tag_name).",";
		}
		$this->tags = substr($tags, 0,-1);
		$activity = $contact->getChildActivity();
		$this->last_activity = $activity->when;
		//$this->last_update = UpdateRecordLog::lastUpdate($contact);
		//$this->first_created = NewRecordLog::createDate($contact);
		$this->last_update = $contact->getLastUpdate();
		$this->first_created = $contact->getCreateDate();
		$this->add();
	}
	
	public function updateFromContact(Contact $contact) {
				
		$do_email = $contact->getChildContactEmail();
		$default_email = $do_email->getDefaultEmail();
		$do_phone = $contact->getChildContactPhone();
		//$do_phone->next();
		
		$this->query("
			UPDATE " . $this->getTable() . " SET 
			       firstname = '".$contact->firstname."',
				   lastname = '".$contact->lastname."',
				   company = '".$contact->company."', 
				   idcompany = '".$contact->idcompany."',
				   position = '".$contact->position."',
				   picture = '".$contact->picture."',
				   email_address = '".$default_email."',
				   phone_number = '".$do_phone->phone_number."',
				   last_update = now()
			 WHERE idcontact = ".$contact->getPrimaryKeyValue()."
		");	
		$this->setLog("\n Contact View Update: ".$this->getSqlQuery());
		//$this->free();		
		
	}
	
	public function addFromContact(Contact $contact) {
		$this->query("
			INSERT INTO " . $this->getTable() . " 
			    (idcontact, firstname, lastname, company, idcompany, position, picture, last_activity, last_update, first_created) VALUES( 
			     ".$contact->idcontact.", '".$contact->firstname."', '".$contact->lastname."', '".$contact->company."', '".$contact->idcompany."', '".$contact->position."', '".$contact->picture."',
				  now(), now(), now())");	

		$this->setLog("\n ContactView Insert: ".$this->getSqlQuery());
		$this->getId($contact->idcontact);
		//$q_insert->free();	
	}

	public function deleteFromContact($idcontact) {
		$sql = "DELETE
				FROM ".$this->getTable()."
				WHERE
				idcontact = {$idcontact}
			   ";
		$this->query($sql);
	}

	/**
	 * addTag
	 * Add a tag to a contact view
	 * @param tag name required
	 * @param idcontact optional if the contact is already loaded.
	 */
	public function addTag($new_tag_name, $idcontact=0) {
		if (empty($idcontact)) { $idcontact = $this->idcontact; }
		$a_tag_name = explode(",", $new_tag_name);
		$this->getId($idcontact);
		$a_tags = explode(",", $this->tags);
		foreach ($a_tag_name as $tag_name) {
			$tag_name = trim($tag_name);
			$exists = false;
			foreach ($a_tags as $tag) {
				if (trim($tag) == $tag_name) {
					$exists = true;
				} 
			}
			if (!$exists) { 
				if (empty($this->tags)) {
					$this->tags = $tag_name;
				} else {
					$this->tags .= ','.$tag_name; 
				}
			}
		}
		//echo $this->tags;
		$this->last_activity = date("Y-m-d H:i:s");
		$this->update();		
	}

	/**
	 * eventAddTag
	 * Event to add a tag
	 * @see addTag
	 */
	
	public function eventAddTag(EventControler $event_controler) {
		$this->addTag($event_controler->tag_name, $event_controler->idcontact);
	}

	/**
	 * DeleteTag
	 * Delete a tag from the contactview->tags.
	 * @param tag_name string with the name of the tag
	 * @param idcontact optional contact id 
	 * @see deleteTagById(), eventDeleteTag()
	 */
	public function deleteTag($tag_name, $idcontact=0) {
		if (empty($idcontact)) { $idcontact = $this->idcontact; }
			$this->getId($idcontact);
			$a_tags = explode(",", $this->tags);
			$new_tags = '';
			foreach ($a_tags as $tag) {
				if ($tag != $tag_name) {
					$new_tags .= ','.$tag;
				}
			}
			$new_tags = substr($new_tags, 1);
			$this->tags = $new_tags;
			$this->setLog("\n ContactView Tag Delete ".$tag_name." tag left are: ".$this->tags);
			$this->last_activity = date("Y-m-d H:i:s");
			$this->update();
	}
	
	/**
	 * deleteTagById 
	 * Delete a tag using the tag primary key.
	 * Used in the ajax delete in the contact.php
	 * it fetch the tag name and call the deleteTab()
	 * @param idtag 
	 * @param idcontact
	 * @see deleteTab(), eventDeleteTag()
	 */
	public function deleteTagById($idtag, $idcontact=0) {
		if (empty($idcontact)) { $idcontact = $this->idcontact; }
		$tag_to_delete = new Tag();
		if ($tag_to_delete->getId($idtag)) {
			$this->deleteTag($tag_to_delete->tag_name, $idcontact);			
		} else {
			$this->setLog("\n ContactView Tag delete: No tag found to delete");
		}		
	}

	/**
	 * eventDeleteTag
	 * Event to delete a tag, just a wrapper for the eventcontroler.
	 * @see deleteTag
	 */
	
	public function eventDeleteTag(EventControler $event_controler) {
		$this->deleteTagById($event_controler->idtag_delete, $event_controler->idcontact);
	}

	public function eventRebuildContactUserTable(EventControler $evtcl) {
		$this->rebuildContactUserTable($evtcl->iduser);
	}

	public function isContactViewEmpty() {
		$sql = "SELECT *
				FROM `{$this->table}`
			   ";
		$this->query($sql);
		if($this->getNumRows()) {
			return false;
		} else {
			return true;
		}
	}
	
}
