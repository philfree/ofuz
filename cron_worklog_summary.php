<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
// @author sarvesh kulkarni sarvesh@htmlfusion.com

/**
 * A cron job script for sending user worklog By Email
 */

include_once('config.php');

 $admin_emails = array(1 =>'sarveshsk43@gmail.com',2=>'saru.111kulkarni@gmail.com');
 $type = array(1=>'Today',2=>'PreviousDay',3=>'LastWeek');
 $message_sent = 0;
 $text ="";

 $proj_discuss = new ProjectDiscuss();
 $pd_discuss = new ProjectDiscuss();
 
 foreach ($type as $tp){
 //select all the  user from project discuss table for the day
 $proj_discuss->query('select DISTINCT(pd.iduser) as iduser,usr.firstname, usr.email from project_discuss pd inner join user usr on usr.iduser=pd.iduser WHERE DATEDIFF(NOW(), date_added) <= 7;');
 
 //check if any of the user enterd worklog for the day
 if($proj_discuss->getNumRows()>0) {
       
           echo "<h2>Total Hours Entered by workers for $tp </h2>";
         while($proj_discuss->fetch()){

            $iduser = $proj_discuss->getData('iduser');
            $name = $proj_discuss->getData('firstname');
                
                $total_hrs = $pd_discuss->getTotalHoursEnteredByIndividual($iduser,$tp);
                
                echo  $name.'<br/>';
                echo '<b>'.$total_hrs.'</b>  Hrs'.'<br/><hr>';
        }
     }
 }
  
 /*if($message_sent==1) 
 {
     echo "Notification Sent Successfully";
 }*/
  
 
$conx = new sqlConnect("root", "s4rvPHP1000") ;