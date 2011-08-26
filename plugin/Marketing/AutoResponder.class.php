<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

   /** 
    * AutoResponder 
    * 
    * This will store the autoresponder messages created by the users
    * if the user decide to keep it it will save it in the db for reuse if not it dies with the session.
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package OfuzCore
    * @license ##License##
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
    */
    
class AutoResponder extends DataObject {
    public $table = "autoresponder";
    protected $primary_key = "idautoresponder";

    
   function eventCheckEmptyFields(Eventcontroler $evtcl){
        $fields = $evtcl->fields;
        if($fields['name'] == ''){
              $evtcl->doSave = 'no';
              $_SESSION['in_page_message'] = _("Please provide one name for the autoresponder");
        }elseif($fields['tag_name'] == ''){
              $evtcl->doSave = 'no';
              $_SESSION['in_page_message'] = _("Please Select One Tag");
        }
        if($evtcl->doSave == 'no'){
            $evtcl->goto = 'settings_auto_responder.php';
            $dispError = new Display("settings_auto_responder.php");
            $dispError->addParam("e", 'yes');
            $evtcl->setDisplayNext($dispError);
        }
   }

   function eventAjaxGetEmailTemplateText(Eventcontroler $evtcl){
        if($evtcl->temlid != ''){
           $do_user_email_tmpl = new EmailTemplateUser();
           $do_user_email_tmpl->getId($evtcl->temlid);
           $evtcl->addOutputValue($do_user_email_tmpl->bodyhtml);   
        }
   }

    function getUserSavedAutoResponders(){
        $this->query("select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser);
    }

    function eventDelAutoResponder(Eventcontroler $evtcl){
        if($evtcl->id != ''){
           $this->getId($evtcl->id);
           $emails =  $this->getChildAutoResponderEmail();
           if($emails->getNumRows()){
                while($emails->next()){
                    $emails->delete();
                }
           }
           $this->delete();
        }    
   }

   function isOwner($id){
        $q = new sqlQuery($this->getDbCon());
        $q->query("Select * from ".$this->table." where idautoresponder = ".$id." AND iduser = ".$_SESSION['do_User']->iduser);
        if($q->getNumRows()){
          return true;
        }else{
          return false;
        }
   }


    function getAutoresponders(){ 
        $qry = " Select autoresponder.idautoresponder,autoresponder.iduser,autoresponder.tag_name,autoresponder.name AS resp_name,
                 autoresponder_email.subject,autoresponder_email.name,autoresponder_email.bodyhtml,autoresponder_email.num_days_to_send 
                 from autoresponder 
                 Inner Join autoresponder_email on autoresponder_email.idautoresponder = autoresponder.idautoresponder
               ";
       // echo $qry;exit;
        $this->query($qry);
    }
}

?>
