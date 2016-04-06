<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
// @author sarvesh kulkarni sarvesh@htmlfusion.com

/**
 * A cron job script for sending user worklog By Email
 */

include_once('config.php');

$admin_emails = array(
    1 =>'philippe@htmlfusion.com',
    2=>'elsa@htmlfusion.com',
    3=>'jared@htmlfusion.com', 
    4=>'rafai@htmlfusion.com',
    5=>'jesse@htmlfusion.com',
    6=>'evan@htmlfusion.com',
    7=>'pedro@htmlfusion.com',
    8=>'binh@htmlfusion.com'
  );
 $type = array(1=>'Today',2=>'PreviousDay',3=>'LastWeek');
 $message_sent = 0;
 $text ="";

 $proj_discuss = new ProjectDiscuss();
 $pd_discuss = new ProjectDiscuss();
 
 $text.= "<h2> <u>Worklog Summary </u></h2>";
 
 foreach ($type as $tp){
 //select all the  user from project discuss table for the day
 $proj_discuss->query('select DISTINCT(pd.iduser) as iduser,usr.firstname, usr.email from project_discuss pd inner join user usr on usr.iduser=pd.iduser WHERE DATEDIFF(NOW(), date_added) <= 7;');
 
 //check if any of the user enterd worklog for the day
 if($proj_discuss->getNumRows()>0) {
       
           $text.= "<h3>Total Hours Entered   $tp </h3>";
           $text.= "<h4><u> Per Participants:</h4></u>"; 
         while($proj_discuss->fetch()){

            $iduser = $proj_discuss->getData('iduser');
            $name = $proj_discuss->getData('firstname');
                
                $total_hrs = $pd_discuss->getTotalHoursEnteredByIndividual($iduser,$tp);
                //echo 'sa'.(int)$total_hrs.'<br/>';
                $total_hours = $total_hrs['total_hrs'] + $total_hrs['tot_work'];
                //echo $total_hrs['total_hrs'];
                if($total_hrs!=''){$text.= '<b>'.$total_hours.'</b>  Hrs By '. $name.'<br/>';}else{$text.= '<b> 0:00</b>  Hrs  By '.$name.'.<br/>';}

        }

     }
 }
 
 //echo $text."<br>";
 //echo $email;
 //die();
 
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
                             $emailer->cleanup();
                                //$text = '';
                             }
                          
  
 /*if($message_sent==1) 
 {
     echo "Notification Sent Successfully";
 }*/
  
 
