<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

   /** 
    * EmailTemplateUsers
    * 
    * This will store the emails messages created by the users
    * if the user decide to keep it it will save it in the db for reuse if not it dies with the session.
    */
    
class EmailTemplateUser extends EmailTemplate {
    public $table = "emailtemplate_user";
    protected $primary_key = "idemailtemplate_user";

   function eventPrepareSaving(EventControler $event_controler) { 

      $fields = $event_controler->fields;
      $this->bodytext = nl2br(strip_tags($fields['bodyhtml'])) ;
      $this->bodyhtml = $fields['bodyhtml'];
      if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() == 1) {
          $this->subject = stripslashes($fields['subject']);
      } else {
          $this->subject = $fields['subject'];
      }
      $this->sendername = $_SESSION['do_User']->getFullName();
      $this->senderemail = $_SESSION['do_User']->email;
      $this->iduser = $_SESSION['do_User']->iduser;
	  
      $fields['bodytext'] = nl2br(strip_tags($fields['bodyhtml'])) ;
      $fields['sendername'] = $_SESSION['do_User']->getFullName();
      $fields['senderemail'] = $_SESSION['do_User']->email;
      $fields['iduser'] = $_SESSION['do_User']->iduser;

      $event_controler->fields = $fields;
   }

   function eventDelete(EventControler $event_controler) {
         if ($this->getPrimaryKeyValue() > 0) {
            $this->delete();   
			$this->free();
        }
    }

    function getUserSavedEmailTemplates(){
        $this->query("select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser );
    }

    function isTemplateOwner($id){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table." where ".$this->primary_key ." = ".$id. " AND iduser = ".$_SESSION['do_User']->iduser);
        if($q->getNumRows()){
            return true;
        }else{
            return false;
        }
    }

     function eventDeleteUserEmailTmpl(EventControler $evtcl){
            $this->getId($evtcl->id);
            $this->delete();
            $this->free();
        }

}

?>
