<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
// @author sarvesh kulkarni sarvesh@htmlfusion.com

/**
 * A cron job script for sending user worklog By Email
 */

include_once('config.php');

 $admin_emails = array(1 =>'sarveshsk43@gmail.com',2=>'saru.111kulkarni@gmail.com');
 $message_sent = 0;
 $text ="";

 $proj_discuss = new ProjectDiscuss();
 
 //select all the  user from project discuss table for the day
 $proj_discuss->query('select DISTINCT(pd.iduser) as iduser , usr.email from project_discuss pd inner join user usr on usr.iduser=pd.iduser WHERE DATEDIFF(NOW(), date_added) <= 7;');
 
 //check if any of the user enterd worklog for the day
 if($proj_discuss->getNumRows()>0) {

    //declare all instances of classes used
     $do_all_project = new Project();
     $do_project = new Project();
     //Get details of the project and notes they entered
     $do_all_project->query("SELECT pd.iduser,pr.name,ptsk.idproject,usr.firstname,tsk.idtask,tsk.task_description,pd.date_added,pd.idproject_task,pd.discuss,pd.document,pd.hours_work from project_discuss pd 
                                                                                                  inner join task tsk 
                                                                                                  inner join user usr 
                                                                                                  inner join project_task ptsk   
                                                                                                  inner join project pr 
                                                                                                              on ptsk.idtask =tsk.idtask and
                                                                                                                 pd.iduser =usr.iduser and
                                                                                                                 pd.idproject_task=ptsk.idproject_task  and 
                                                                                                                 pr.idproject= ptsk.idproject                    
            
            where date_added <= curdate() and date_added >= DATE_SUB(curdate(),INTERVAL 2 day) order by(pd.date_added)");

           
           $last_task = 0;
           $last_desc = 0;
           $last_date = 0;
           
           
           
            //$text .= '<br/><span > Worklog For the Day :'.date('jS F Y').'</span>';
              while($do_all_project->fetch()){
              
                     $project_id =  $do_all_project->getdata('idproject');
                     $do_project->query('select name from project where idproject='.$project_id);
                     $do_project->fetch();
                     $name_prj = $do_project->getData('name');
                     
                     $_SESSION['adm_project_discuss_idtask'] = $project_id;
                     
                     
                     $project_name =  $do_all_project->getdata('name');
                     $first_name   =  $do_all_project->getdata('firstname');
                     $task_desc    =  $do_all_project->getdata('task_description');
                     $discuss_text =  $do_all_project->getdata('discuss');
                     $hours_work   =  $do_all_project->getdata('hours_work');
                     $idtask       =  $do_all_project->getdata('idproject_task');
                     $document     =  $do_all_project->getdata('document');
                     $date_added   =  $do_all_project->getdata('date_added');
                     $iduser   =  $do_all_project->getdata('iduser');
                     $_SESSION['adm_project_task_discuss'] = $idtask;
                     $_SESSION['adm_project_date_added'] = $date_added;
                     
                     
                     
                    if ($last_date !=  $_SESSION['adm_project_date_added']) {
                        $date = date_create_from_format('Y-m-d',$date_added);
                        $text.='<br/><u><h2> Worklog For  :  '.date_format($date, 'j-M-Y').'</h2></u>';
                        
                        
                       /* $proj_time = new ProjectDiscuss();
                        $proj_time->query('SELECT project_discuss.discuss,project_discuss.date_added,document,
                              project_discuss.iduser,sum( project_discuss.hours_work ) AS total_hrs,
                              project_task.idproject, project_task.idtask FROM project_task
                              left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                              where DATE(project_discuss.date_added) = CURDATE() AND project_discuss.iduser = '.$iduser.' '.'group by project_discuss.iduser');
                              
                        $user_id = $proj_time->getData('iduser');
                        $us_obj = new User();       
                        $user_name = $us_obj->getFullName($user_id);
                        //$text .= '<h2><u><b>'.$user_name.'</b>'.'&nbsp;&nbsp; Worklog </u></h2>';
                        $text .= '<u><h2> '.$user_name.'</h2></u>';
                        //$text .= '<h3><u>Your Notes For '.date('jS F Y').'</u></h3>';
                        //$text .= '<h3>by: <b>'.$user_name.'</b>'.'</h3>';
                        $text .= "<h3>Total Hours Entered : ".$proj_time->getData('total_hrs')."</h3>";  */    
                    }                        	
                        
                    
                    
                     
                    if ($last_task !=  $_SESSION['adm_project_discuss_idtask']) {
                    $text.=' <div>';
                    $text.='<b><span ><a href='.$_SERVER[HTTP_HOST].'/Project/'.$project_id.'>'.$name_prj.'</a></span></b>';  
                    $text.= '<div>';
                    }                    		
                    if ($last_desc !=  $_SESSION['adm_project_task_discuss']) {
                        $text.='<br/><span ><a href='.$_SERVER[HTTP_HOST].'/Task/'.$idtask.'>'.$task_desc.'</a></span>';
                    }                    		
                    
                	$text.='<br /><i>';
                    $text.= _('Note By ').$first_name;
                    $text.= '<br />';
                    $text.=_('Time Worked').' : <b>'.$hours_work.' '._('hrs') .'</b>';
                    $text.='<br /></i>';
                    $text.= nl2br($discuss_text.'<br />');
                    if($document!= ''){
                        $file_url = "/files/".$document;
                        $file = '<a href="'.$_SERVER[HTTP_HOST].$file_url.'" target="_blank">'.$document.'</a>';
                        $text.='<br /> <b>'._('Attachment').'</b> : '.$file;
                        }
                    $text.='<br /><br />';
                    $text.='<div class="dottedline"></div>';
                    $text.='</div></div>';
                    
                    $last_task = $_SESSION['adm_project_discuss_idtask'];
                    $last_desc = $_SESSION['adm_project_task_discuss'];
                    $last_date = $_SESSION['adm_project_date_added'];
                    
                    
            }
        
   
   
        // echo $text.'<br />';   
        
        // send mails to the ofuz users with their respective worklog
        
         foreach ($admin_emails as $email){
            
                  $do_template = new EmailTemplate();
                  $do_template->senderemail = "support@sqlfusion.com";
                  $do_template->sendername = "Ofuz";
                  $do_template->subject = "Worklog Summay Of 2 days :";
                  $do_template->bodytext = $text;
                  $do_template->bodyhtml = $do_template->bodytext;
                  
                  //echo $text.'<br />';
                  $values=Array();
                  //Use for sending email here for general users
                          $emailer = new Radria_Emailer('UTF-8');
                          $emailer->setEmailTemplate($do_template);
                          $emailer->mergeArray($values);//required even if there is nothig to merge
                        
                             $emailer->addTo($email);
                             $message_sent =  (int)$emailer->send();
                             //$emailer->cleanup();
                                //$text = '';
                             }
                          
   
   
   $text = '';
   
   
   
   
   
   
   

   
   
   
   
   
   
   
   
    /* Worklog summary for the past week*/
    
    
    //declare all instances of classes used
     $do_all_project = new Project();
     $do_project = new Project();
     //Get details of the project and notes they entered
     $do_all_project->query("SELECT pr.name,ptsk.idproject,usr.firstname,tsk.idtask,tsk.task_description,pd.date_added,pd.idproject_task,pd.discuss,pd.document,pd.hours_work from project_discuss pd 
                                                                                                  inner join task tsk 
                                                                                                  inner join user usr 
                                                                                                  inner join project_task ptsk   
                                                                                                  inner join project pr 
                                                                                                              on ptsk.idtask =tsk.idtask and
                                                                                                                 pd.iduser =usr.iduser and
                                                                                                                 pd.idproject_task=ptsk.idproject_task  and 
                                                                                                                 pr.idproject= ptsk.idproject                    
            
            where date_added <= curdate() and date_added >= DATE_SUB(curdate(),INTERVAL 7 day) order by(pd.date_added)");

           
           $last_task = 0;
           $last_desc = 0;
           $last_date = 0;
           
            //$text .= '<br/><span > Worklog For the Day :'.date('jS F Y').'</span>';
              while($do_all_project->fetch()){
              
                     $project_id =  $do_all_project->getdata('idproject');
                     $do_project->query('select name from project where idproject='.$project_id);
                     $do_project->fetch();
                     $name_prj = $do_project->getData('name');
                     
                     $_SESSION['adm_project_discuss_idtask'] = $project_id;
                     
                     
                     $project_name =  $do_all_project->getdata('name');
                     $first_name   =  $do_all_project->getdata('firstname');
                     $task_desc    =  $do_all_project->getdata('task_description');
                     $discuss_text =  $do_all_project->getdata('discuss');
                     $hours_work   =  $do_all_project->getdata('hours_work');
                     $idtask       =  $do_all_project->getdata('idproject_task');
                     $document     =  $do_all_project->getdata('document');
                     $date_added   =  $do_all_project->getdata('date_added');
                     $_SESSION['adm_project_task_discuss'] = $idtask;
                     $_SESSION['adm_project_date_added'] = $date_added;
                    
                     
                    if ($last_date !=  $_SESSION['adm_project_date_added']) {
                        $date = date_create_from_format('Y-m-d',$date_added);
                        $text.='<br/><u><h2> Worklog For  :  '.date_format($date, 'j-M-Y').'</h2></u>';
                    }                            
                    
                     
                    if ($last_task !=  $_SESSION['adm_project_discuss_idtask']) {
                    $text.=' <div>';
                    $text.='<b><span ><a href='.$_SERVER[HTTP_HOST].'/Project/'.$project_id.'>'.$name_prj.'</a></span></b>';  
                    $text.= '<div>';
                    }                    		
                    if ($last_desc !=  $_SESSION['adm_project_task_discuss']) {
                        $text.='<br/><span ><a href='.$_SERVER[HTTP_HOST].'/Task/'.$idtask.'>'.$task_desc.'</a></span>';
                    }                    		
                    
                	$text.='<br /><i>';
                    $text.= _('Note By ').$first_name;
                    $text.= '<br />';
                    $text.=_('Time Worked').' : <b>'.$hours_work.' '._('hrs') .'</b>';
                    $text.='<br /></i>';
                    $text.= nl2br($discuss_text.'<br />');
                    if($document!= ''){
                        $file_url = "/files/".$document;
                        $file = '<a href="'.$_SERVER[HTTP_HOST].$file_url.'" target="_blank">'.$document.'</a>';
                        $text.='<br /> <b>'._('Attachment').'</b> : '.$file;
                        }
                    $text.='<br /><br />';
                    $text.='<div class="dottedline"></div>';
                    $text.='</div></div>';
                    
                    $last_task = $_SESSION['adm_project_discuss_idtask'];
                    $last_desc =  $_SESSION['adm_project_task_discuss'];
                    $last_date = $_SESSION['adm_project_date_added'];
            }
        
        
   
   
   
           // echo $text.'<br />';   
        
        // send mails to the ofuz users with their respective worklog
        
        
        
                  $do_template = new EmailTemplate();
                  $do_template->senderemail = "support@sqlfusion.com";
                  $do_template->sendername = "Ofuz";
                  $do_template->subject = "Worklog Summary Of past Week :";
                  $do_template->bodytext = $text;
                  $do_template->bodyhtml = $do_template->bodytext;
                  
                  //echo $text.'<br />';
                  $values=Array();
                  //Use for sending email here for general users
                          $emailer = new Radria_Emailer('UTF-8');
                          $emailer->setEmailTemplate($do_template);
                          $emailer->mergeArray($values);//required even if there is nothig to merge
                          $emailer->addTo($email);
                          foreach ($admin_emails as $email){
                                 $emailer->addTo($email);
                                 $message_sent =  (int)$emailer->send();
                                 //$emailer->cleanup();
                                //$text = '';
                             }
   
}else{
    echo 'No Worklog For the Day <b>'.date('jS F Y').'</b>';
}
  
 /*if($message_sent==1) 
 {
     echo "Notification Sent Successfully";
 }*/
  
 
