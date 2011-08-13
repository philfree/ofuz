<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/   
   
class OfuzCancelAccount extends DataObject {
    

    function eventCancelAccount(EventControler $evtcl){
        //echo 'No cancellation after registration....';
        //echo '<br /> and for the reson that u have given <br />'.$evtcl->fields["reason"].'<br /> no way, no cancellation ';
        //exit;
        if($evtcl->fields["reason"] != '' ){
            $this->deleteContactRelated();
            $this->deleteUserContactNote();
            $this->deleteUserContactSharing();
            $this->deleteUserEmailTemplate();
            $this->deleteInvoiceRelated();
            $this->deleteMessage();
            $this->deleteProjectRelated();
            $this->deleteTask();
            $this->deletewebformuser();
            $this->deleteworkfeed();
            $this->deletenotedraft();
            $this->deletetwitteraccount();
            $this->deletetbreadcrumb();
            $this->deleteUserRel();
            $this->deleteUserSettings();
            $this->deleteUserTags();
            $this->dropContactView();
            $email_template = new EmailTemplate("ofuz_cancel_account_notification");
            $email_template->setSenderName($_SESSION['do_User']->getFullName());
            $email_template->setSenderEmail($_SESSION['do_User']->email);
            $email_data = Array('name' => $_SESSION['do_User']->getFullName(),
                                                
                                                'email'=>$_SESSION['do_User']->email,
                                                'reason'=>$evtcl->fields["reason"]
                                                );
                    $emailer = new Radria_Emailer();
                    $emailer->setEmailTemplate($email_template);
                    $emailer->mergeArray($email_data);
                    $emailer->addTo("philippe@sqlfusion.com");
                    //$emailer->addTo("abhik@sqlfusion.com");
                    $emailer->send();

            $this->deleteUser();
        }else{
            $_SESSION['in_page_message'] = _("Please Provide a reason before cancelling the account");
             $dispError = new Display('cancel_account.php') ;
             $dispError->addParam("m", 'e') ;
             $evtcl->setDisplayNext($dispError) ;
        }
        
        
    }


    function deleteContactRelated($iduser=""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("select idcontact from contact where iduser = ".$iduser);
        if($q->getNumRows()){
            while($q->fetch()){
              $q_del = new sqlQuery($this->getDbCon());
              $q_del->query("DELETE FROM `activity` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->query("DELETE FROM `contact_address` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->query("DELETE FROM `ccontact_email` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->query("DELETE FROM `contact_instant_message` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->query("DELETE FROM `contact_phone` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->query("DELETE FROM `contact_portal_message` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->query("DELETE FROM `contact_rss_feed` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->query("DELETE FROM `contact_website` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->query("DELETE FROM `contact` WHERE idcontact = ".$q->getData("idcontact"));
              $q_del->free();
            }  
        }
    }

    function deleteUserContactNote($iduser=""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from contact_note where iduser = ".$iduser);
    }

    function deleteUserContactSharing($iduser=""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from contact_sharing where iduser = ".$iduser);
    }

    function deleteUserEmailTemplate($iduser=""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from emailtemplate_user where iduser = ".$iduser);
    }

    function deleteInvoiceRelated($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("select idinvoice from invoice where iduser = ".$iduser);
        if($q->getNumRows()){
            $do_del_inv = new Invoice();
            while($q->fetch()){
              $do_del_inv->getId($q->getData("idinvoice"));
              //Del Invoice Lines
              $inv_lines = $do_del_inv->getChildInvoiceLine();
              while($inv_lines->next()){
                   $inv_lines->delete(); 
              }
              //Del Recurrence Invoice
              $rec_inv = $do_del_inv->getChildRecurrentInvoice();
              $del_rec_inv_cc = new sqlQuery($this->getDbCon());
              while($rec_inv->next()){
                   // Del Recurrent Invoice CC 
                   $del_rec_inv_cc->query("delete from recurrent_invoice_cc 
                                           where idrecurrentinvoice = ".$rec_inv->idrecurrentinvoice);
                   $rec_inv->delete(); 
              }
              //Delete Payment Log
              $pay_log =  $do_del_inv->getChildPaymentLog();
              while($pay_log->next()){
                  $pay_log->delete();
              }
              // Delete Invoice
              $do_del_inv->delete();
            }
        }
    }

    function deleteMessage($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from message_user where iduser = ".$iduser);
        $q->query("delete from message_draft where iduser = ".$iduser);
    }


    function deleteProjectRelated($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("select idproject from project where iduser =".$iduser);
        if($q->getNumRows()){ 
            $q_del = new sqlQuery($this->getDbCon());
            $q_proj_task = new sqlQuery($this->getDbCon());
            $q_task = new sqlQuery($this->getDbCon());  
            //$do_del_proj = new Project();
            while($q->fetch()){
                $q_del->query("delete from project where idproject = ".$q->getData("idproject"));
                $q_del->query("delete from project_sharing where idproject = ".$q->getData("idproject"));
                $q_proj_task->query("select * from project_task where idproject = ".$q->getData("idproject"));
                if($q_proj_task->getNumRows()){
                    while($q_proj_task->fetch()){
                        $q_del->query("delete from task where idtask = ".$q_proj_task->getData("idtask"));

                        $q_del->query("delete from project_task 
                                      where idproject_task = ".$q_proj_task->getData("idproject_task"));

                        $q_del->query("delete from project_discuss 
                                      where idproject_task = ".$q_proj_task->getData("idproject_task") );
                    }
                }  
            }
        }
        $q->query("delete from project_sharing where idcoworker = ".$iduser);
    }

    function deleteTask($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q_del = new sqlQuery($this->getDbCon());
        $q->query("SELECT task.idtask,project_task.idproject_task
                  FROM task
                  LEFT JOIN project_task ON project_task.idtask = task.idtask
                  WHERE task.iduser =" .$iduser."
                  AND project_task.idproject_task IS NULL" );

        if($q->getNumRows()){
            while($q->fetch()){ 
                //echo "idtsk: ".$q->getData("idtask");
                //echo "delete from task where idtask = ".$q->getData("idtask").'<br />';
                $q_del->query("delete from task where idtask = ".$q->getData("idtask"));
            }
        }
    }

    function deleteUserSettings($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from user_settings where iduser = ".$iduser);
    }

    function deletewebformuser($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
          $q = new sqlQuery($this->getDbCon());
          $q->query("delete from webformuser where iduser = ".$iduser);
    }

    function deleteworkfeed($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
          $q = new sqlQuery($this->getDbCon());
          $q->query("delete from workfeed where iduser = ".$iduser);
    }

    function deletenotedraft($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
          $q = new sqlQuery($this->getDbCon());
          $q->query("delete from note_draft where iduser = ".$iduser);
    }

    function deletetwitteraccount($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
          $q = new sqlQuery($this->getDbCon());
          $q->query("delete from twitter_account where iduser = ".$iduser);
    }

    function deletetbreadcrumb($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
          $q = new sqlQuery($this->getDbCon());
          $q->query("delete from breadcrumb where iduser = ".$iduser);
    }
    
    function deleteUserRel($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from user_relations where iduser = ".$iduser);
        $q->query("delete from user_relations where idcoworker = ".$iduser);
    }

    function deleteUserTags($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from tag where iduser = ".$iduser);
    }

    function dropContactView($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $conatc_view_name = 'userid'.$iduser.'_contact';
        $q = new sqlQuery($this->getDbCon());
        //echo "drop table ".$conatc_view_name;
        $q->query("drop table ".$conatc_view_name);
    }

    function deleteUser($iduser = ""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from user where iduser = ".$iduser." limit 1");
    }

  /**
   * This deletes entire User Account from Ofuz.
   * @param int : $iduser
   * @return void
   */
  function deleteUserAccount($iduser) {
    $this->deleteContactRelated($iduser);
    $this->deleteUserContactNote($iduser);
    $this->deleteUserContactSharing($iduser);
    $this->deleteUserEmailTemplate($iduser);
    $this->deleteInvoiceRelated($iduser);
    $this->deleteMessage($iduser);
    $this->deleteProjectRelated($iduser);
    $this->deleteTask($iduser);
    $this->deletewebformuser($iduser);
    $this->deleteworkfeed($iduser);
    $this->deletenotedraft($iduser);
    $this->deletetwitteraccount($iduser);
    $this->deletetbreadcrumb($iduser);
    $this->deleteUserRel($iduser);
    $this->deleteUserSettings($iduser);
    $this->deleteUserTags($iduser);
    $this->dropContactView($iduser);
    $this->deleteUser($iduser);
  }
}	
?>
