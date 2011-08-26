<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

   /** 
    * AutoResponder 
    * 
    * This will store the autoresponder messages created by the users
    * if the user decide to keep it it will save it in the db for reuse if not it dies with the session.
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package OfuzCore
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
    */
    
class AutoResponderEmail extends DataObject {
    public $table = "autoresponder_email";
    protected $primary_key = "idautoresponder_email";

    
   function eventCheckEmptyFields(Eventcontroler $evtcl){
        $fields = $evtcl->fields;
   }

   function eventChangeGoto(Eventcontroler $evtcl){
          $fields = $evtcl->fields;
          $dispError = new Display("settings_auto_responder_email.php");
          $dispError->addParam("id", $fields["idautoresponder"]);
          $evtcl->setDisplayNext($dispError);
   }

   function eventAjaxGetEmailTemplateText(Eventcontroler $evtcl){
        if($evtcl->temlid != ''){
           $do_user_email_tmpl = new EmailTemplateUser();
           $do_user_email_tmpl->getId($evtcl->temlid);
           $evtcl->addOutputValue($do_user_email_tmpl->bodyhtml);   
        }
   }

   function eventDelAutoResponderEmail(Eventcontroler $evtcl){
        if($evtcl->id != ''){
           $this->getId($evtcl->id);
           $this->delete(); 
        }
   } 

    
}

?>
