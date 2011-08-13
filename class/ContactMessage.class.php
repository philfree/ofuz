<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com


     /**
      * Class ContactMessage
      * To personalize the messages for client portal by the User for contacts.
      * table: contact_portal_message 
      *
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzContactPortal
      * @license ##License##
      * @version 0.6.2
      * @date 2010-11-13
      * @since 0.6.2
      */


class ContactMessage extends DataObject {
    public $table = "contact_portal_message";
    public $primary_key = "idcontact_portal_message";


    function __construct(sqlConnect $conx=NULL, $table_name="") {
        parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_MESSAGE);
		}
    }

	public function eventSetPersonalizedMessage(EventControler $evtcl) {

		if($this->checkIfMessageExists($evtcl->idcontact)){
			$sql = "UPDATE {$this->table}
					SET
					`message` = '{$evtcl->per_msg}'
					WHERE
					idcontact = {$evtcl->idcontact} AND iduser = {$_SESSION['do_User']->iduser}
				   ";
		} else {
			$sql = "INSERT INTO {$this->table}
					(`idcontact`,`iduser`,`message`)
					VALUES
					({$evtcl->idcontact},{$_SESSION['do_User']->iduser},'{$evtcl->per_msg}')
				";
		}
 
		$this->query($sql);

	}

	public function checkIfMessageExists($idcontact) {
		$sql = "SELECT *
				FROM {$this->table}
				WHERE
				idcontact = {$idcontact} AND iduser = {$_SESSION['do_User']->iduser}
			   ";
		$this->query($sql);
		if($this->getNumRows()) {
			return true;
		} else {
			return false;
		}
	}

	public function getPersonalizedMessage($idcontact,$iduser) {

		$sql = "SELECT *
				FROM {$this->table} 
				WHERE
				idcontact = {$idcontact} AND iduser = {$iduser}";
		$this->query($sql);
		if($this->getNumRows()) {
			return $this->getData("message");
		} else {
			return false;
		}

	}

	public function displayPersonalizedMessage($personalized_message) {

		echo '<div id="per_message" class="messageshadow">';
		echo '     <div class="messages" style="position:relative">';
		echo "      ".$personalized_message;
		echo '     </div>';
		echo '</div>';
		
	}

}


?>
