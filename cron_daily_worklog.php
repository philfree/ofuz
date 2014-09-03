<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
// @author sarvesh kulkarni sarvesh@htmlfusion.com

/**
 * A cron job script for sending user worklog By Email
 */

include_once('config.php');

 $message_sent = false;
 //declare all instances of classes used
 $do_all_project = new Project();
 $do_project = new Project();
 $do_all_project->query("SELECT pr.name,ptsk.idproject,usr.firstname,tsk.idtask,tsk.task_description,pd.discuss,pd.document,pd.hours_work from project_discuss pd 
                                                                                      inner join task tsk 
                                                                                      inner join user usr 
                                                                                      inner join project_task ptsk   
                                                                                      inner join project pr 
                                                                                                  on ptsk.idtask =tsk.idtask and
                                                                                                     pd.iduser =usr.iduser and
                                                                                                     pd.idproject_task=ptsk.idproject_task  and 
                                                                                                     pr.idproject= ptsk.idproject                    

where DATE(date_added)=CURDATE()");

  $text ="Projects";
if($do_all_project->getNumRows()>1) {
      while($do_all_project->fetch()){
      
             $project_id =  $do_all_project->getdata('idproject');
             $do_project->query('select name from project where idproject='.$project_id);
             $do_project->fetch();
             $name_prj = $do_project->getData('name');
             $last_id=$project_id;
             
             
             $project_name =  $do_all_project->getdata('name');
             $first_name   =  $do_all_project->getdata('firstname');
             $task_desc    =  $do_all_project->getdata('task_description');
             $discuss_text =  $do_all_project->getdata('discuss');
             $hours_work   =  $do_all_project->getdata('hours_work');
             $idtask       =  $do_all_project->getdata('idtask');
             $document     =  $do_all_project->getdata('document');
             
             
            $text.=' <div>';
            $text.='<b><span ><a href='.$_SERVER[HTTP_HOST].'/Project/'.$project_id.'>'.$name_prj.'</a></span></b>';  
            $text.= '<div>';
            				
        	$text.='<br/><span ><a href='.$_SERVER[HTTP_HOST].'/Task/'.$idtask.'>'.$task_desc.'</a></span>';
        	$text.='<br /><i>';
            $text.= _('Note By ').$first_name;
            $text.= '<br />';
            $text.=_('Time Worked').' : '.$hours_work.' '._('hrs') ;
            $text.='<br /></i>';
            $text.= nl2br($discuss_text.'<br />');
            if($document!= ''){
                $file_url = "/files/".$document;
                $file = '<a href="'.$_SERVER[HTTP_HOST].$file_url.'" target="_blank">'.$document.'</a>';
                $text.='<br /> '._('Attachment').' : '.$file;
                }
            $text.='<br />';
            $text.='<div class="dottedline"></div>';
            $text.='</div></div>';
            
          }
          echo $text;
          $do_template = new EmailTemplate();
          $do_template->senderemail = "support@sqlfusion.com";
          $do_template->sendername = "Ofuz";
          $do_template->subject = "Worklog Reminder :".date('Y-m-d');
          $do_template->bodytext = $text;
          $do_template->bodyhtml = $do_template->bodytext;
          
          
          $values=Array();
          //Use for sending email here for general users
                  $emailer = new Radria_Emailer('UTF-8');
                  $emailer->setEmailTemplate($do_template);
                  $emailer->mergeArray($values);//required even if there is nothig to merge
                  $emailer->addTo('email@gmail.com');
                  $emailer->send();
                  $emailer->cleanup();
                  $message_sent = true;
}      
  
 if($message_sent==true) 
 {
     echo "Notification Sent Successfully";
 }
  
  