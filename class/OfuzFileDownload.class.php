<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

Class OfuzFileDownload extends DataObject {
    
    public function checkFileAccessSecurity($filename){
        $return = false;
        /*
          Check if the request is from the Contact Portal and then do the operation
        */
        if($_SESSION['portal_idcontact'] !='' ){
          $do_cnt_note = new ContactNotes();
          if($do_cnt_note->isDocumentForContact($_SESSION['portal_idcontact'],$filename)){        
              $return =  true;
          }
        }elseif($_SESSION['do_User']->iduser !=''){ // We have userid set then the request is from a loggedin user
              $q_project_discuss = new sqlQuery($this->getDbCon()); 
              $q_project_discuss->query("select idproject_task from project_discuss where document = '".$filename."'");
              // Check if the file is in project_discuss
              if($q_project_discuss->getNumRows()){
                  $q_project_discuss->fetch();
                  $do_proj_task = new ProjectTask();
                  if($do_proj_task->isProjectTaskReletedToUser($q_project_discuss->getData("idproject_task"),$_SESSION['do_User']->iduser)){
                        $return = true;
                  }
              }else{ // Not in project Discuss then check in contact_note
                  $q_cnt_note = new ContactNotes();
                  $q_cnt_note->query("select idcontact from contact_note where document = '".$filename."'");
                  if($q_cnt_note->getNumRows()){
                      $q_cnt_note->fetch();
                      $do_cont = new Contact();
                      if($do_cont->isContactRelatedToUser($q_cnt_note->getData("idcontact"))){
                          $return = true;
                      } 
                  }  
              }
        }
      return $return;
    }
}
?>