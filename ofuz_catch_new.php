<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Page to list all the invoices
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzPage
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.4
     */


include_once('config.php');


//mb_convert_encoding($str, "UTF-8",
//fwrite($fp, "\n\n text/plain content:".$final_message_content);


$code_found = false;
$addnote = false;
$addtask = false;
$addprojectnote = false;
$add_project_task = false;
$attachments_extracted = false ;

$OfuzEmailFetcher = new OfuzEmailFetcher();
$do_user = new User();
$do_contact = new Contact();
$do_contact_note = new ContactNotes();
$do_contact_email = new ContactEmail();
$do_project = new Project();
$do_project_discuss = new ProjectDiscuss();
$do_project_task = new ProjectTask();
$do_task = new Task();
$do_activity = new Activity();

// Take any one as per convinience 
$OfuzEmailFetcher->fetchEmailText('http://dev.ofuz.net/files/ofuz_catch.log');
//$OfuzEmailFetcher->fetchEmailRow($rowEmail);
//$OfuzEmailFetcher->fetchEmailStream($stream);


$to = $OfuzEmailFetcher->getToEmail();
$from = $OfuzEmailFetcher->getFromEmail();
$bcc = $OfuzEmailFetcher->getBCCEmail();
$cc = $OfuzEmailFetcher->getCCEmail(); 
$msg_text = $OfuzEmailFetcher->getMessageBody('text');
$msg_html = $OfuzEmailFetcher->getMessageBody('html');
$email_sub = $OfuzEmailFetcher->getHeader('subject');


if(strlen($msg_text) < 1 ){
    $final_message_content = $msg_html;
}else{ $final_message_content = $msg_text; }
$final_message_content = strip_tags($final_message_content);
//$final_message_content = nl2br($final_message_content);
//$final_message_content = preg_replace('/(<br[^>]*>\s*){2,}/', '<br/>', $final_message_content);

$final_message_content = preg_replace('/\n{2,}/', "\n", $final_message_content);
$final_message_content = preg_replace('/(<br[^>]*>\s*){2,}/', "\n", $final_message_content);

function cmp($a, $b){
    if ($a['filesize'] == $b['filesize']) {
        return 0;
    }
    return ($a['filesize'] > $b['filesize']) ? -1 : 1;

}




$email_marged_array = array_merge($to,$bcc);
$all_emails_merged = array_merge($to,$cc,$bcc);

$from_name = $OfuzEmailFetcher->getEmailDisplay($from[0]);
$from_address = $OfuzEmailFetcher->getEmailAddress($from[0]);




/*print_r($email_marged_array);
echo '<br />';
print_r($email_marged_array);
*/

/*
From the marge array for To/BCC get the email ids and
out in a seperate id and get the array length
*/
if(is_array($all_emails_merged)){
  foreach($all_emails_merged as $email_add){
    $display_array[] = $OfuzEmailFetcher->getEmailAddress($email_add);
    $email_array[] = $OfuzEmailFetcher->getEmailDisplay($email_add);
  }
  $len_to_emailarr = count($email_array);
}
/*
From the above email array  $to_emailarr check if there is an email id like
addnote-123@ofuz.net and get the drop box code from that.
*/
for($i=0 ;$i<$len_to_emailarr;$i++){
    $email_code_split = split("@", $email_array[$i]);
    $email_code = $email_code_split[0];
    $email_code_domain = $email_code_split[1];
    if($email_code_domain == $GLOBALS['email_domain']){ 
      if(preg_match("/addnote-/",$email_code,$matches)) { 
        $code_split = split("-",$email_code);
        $drop_box_code_note = $code_split[1];
        $code_found = true;
        $addnote = true;
        break;
      }
    }
}


/*
From the above generated array with email addresses check if there is an email id
like addtask-123@ofuz.net and then get the drop box code for that.
*/
for($i=0 ;$i<$len_to_emailarr;$i++){
    $email_code_split_task = split("@", $email_array[$i]);
    $email_code_task = $email_code_split_task[0];
    $email_code_domain = $email_code_split_task[1];
    if($email_code_domain == $GLOBALS['email_domain']){
      if(preg_match("/addtask-/",$email_code_task,$matches)) {
        $code_split = split("-",$email_code_task);
        $drop_box_code_task = $code_split[1];
        $code_found = true;
        $addtask = true; 
        break;
      }
    }
}


/*
  For project task drop box same email array is used as in the 
  task $email_marged_array_task
*/
for($i=0 ;$i<$len_to_emailarr;$i++){
    $email_code_split_proj_task = split("@", $email_array[$i]);
    $email_code_proj_task = $email_code_split_proj_task[0];
    $email_code_proj_domain = $email_code_split_proj_task[1];
    if($email_code_proj_domain == $GLOBALS['email_domain']){
      if(preg_match("/task-/",$email_code_proj_task,$matches)) {
        $code_split = split("-",$email_code_proj_task);
        $drop_box_code_proj_task = $code_split[1];
        $addprojectnote = true;
        break;
      }
    }
}


// If the email is PHP generated while adding a task discussion and sending emails
if(preg_match("/x-ofuz-emailer/",$OfuzEmailFetcher->getHeaderString(),$matches)) {
   $addprojectnote = false;
}


/*
From the above generated array with email addresses check if there is an email id
like newtask-5@ofuz.net and then get the drop box code for that.
*/
for($i=0 ;$i<$len_to_emailarr;$i++){
    $email_code_split_proj_task = split("@", $email_array[$i]);
    $email_code_proj_task = $email_code_split_proj_task[0];
    $email_code_proj_domain = $email_code_split_proj_task[1];
    if($email_code_proj_domain == $GLOBALS['email_domain']){
      if(preg_match("/newtask-/",$email_code_proj_task,$matches)) {
        $code_split_proj_task = split("-",$email_code_proj_task);
        $drop_box_code_proj = $code_split_proj_task[1];
        $add_project_task = true;
        break;
      }
    }
}


/** 
  * Adding Contact Note when the drop box code is for add note
  * Check if the email id is related to the user contact and if yes just add the note
  * if not found any contact related to the email id then add a new contact first and then add the note
  * While creating new Contact should ignore creating contact from addnote-1@ofuz.net
*/
if($addnote === true){ 
    // Replaced this with final message content to only extract plain/text
    $parse_content = $final_message_content;
    $iduser = $do_user->getUserFromDropBox($drop_box_code_note); 
    if($iduser !== false ){
        // Retrieve the attachment only if its a valid email to be extracted.
        if($attachments_extracted === false){
            $attachment = $OfuzEmailFetcher->saveAttachments('/var/www/dev.ofuz.net/files/');// Change this file path to your file saving path for Contact Notes and Project discussion
            $attachments_extracted = true ;
        }
        foreach($email_marged_array as $finalres){ 
          if(!preg_match("/addnote-/",$OfuzEmailFetcher->getEmailAddress($finalres),$matches) && !preg_match("/addtask-/",$OfuzEmailFetcher->getEmailAddress($finalres),$matches)) {
              $contact_email = $OfuzEmailFetcher->getEmailAddress($finalres);
              $idcontact = $do_contact->getContactIdByEmail($contact_email);
              if($idcontact === false ){
                  $name_email = $OfuzEmailFetcher->getEmailDisplay($finalres);
                  $regexp = "/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";
                  if($name_email != ''){
                        $name = $name_email;
                  }else{
                        $name = $contact_email;
                  }
              
                  $do_contact->addNew();
                  $do_contact->firstname = $name;
                  $do_contact->iduser = $iduser;
                  $do_contact->add();
                  $idcontact = $do_contact->getInsertId();
                  
                  $do_contact_email->addNew();
                  $do_contact_email->idcontact = $idcontact;
                  $do_contact_email->email_address = $contact_email;
                  $do_contact_email->email_type = 'Work';
                  $do_contact_email->add();
                  
                  $do_contact->getId($idcontact);
                  $do_contact_view = new ContactView();
                  $do_contact_view->setUser($iduser);
                  $do_contact_view->addFromContact($do_contact);

                  $do_activity->addNew();
                  $do_activity->idcontact = $idcontact;
                  $do_activity->when = date("Y-m-d H:i:s");
                  $do_activity->add();    
              }
              
              // For now add multiple notes for multiple attachment
              if(is_array($attachment) && count($attachment)> 0){
                  $attachment_count = 0;
                  foreach($attachment as $attachment){
                      $attachment_count++;
                      $do_contact_note->addNew();
                      $do_contact_note->iduser = $iduser;
                      $do_contact_note->idcontact = $idcontact;
                      if($attachment_count == 1)
                          $do_contact_note->note = $parse_content ;
                      else
                          $do_contact_note->note = 'Attachment' ;
                      $do_contact_note->date_added = date("Y-m-d");
                      $do_contact_note->document = $attachment['filename'] ;
                      $do_contact_note->add();
                  }
              }else{
                  $do_contact_note->addNew();
                  $do_contact_note->iduser = $iduser;
                  $do_contact_note->idcontact = $idcontact;
                  $do_contact_note->note = $parse_content ;
                  $do_contact_note->date_added = date("Y-m-d");
                  $do_contact_note->add();
              }
         }
      }
   }
}
  
// Drop Box code is for add task          
if($addtask === true){
 $iduser = $do_user->getUserFromDropBox($drop_box_code_note);
  if($iduser !== false ){
      $cont_array = split("due:",$email_sub);
      $parse_content = $cont_array[0];
      $dateformat1 = "/due:([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
      $dateformat2 = "/due:([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/";
      $dateformat3 = "/due:([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/";
      $dateformat4 = "/due:([0-9]{2})-([0-9]{2})-([0-9]{4})$/";
    
      $category = 3; //Email
      if(preg_match("/due:tomorrow/",$email_sub,$matches) || preg_match("/due:Tomorrow/",$email_sub,$matches)) {
          $formated_date = date("Y-m-d",strtotime("+1 day"));
      }elseif(preg_match("/due:this week/",$email_sub,$matches) || preg_match("/due:This Week/",$email_sub,$matches)){
          $formated_date = date("Y-m-d",strtotime("next Friday"));
      }elseif(preg_match("/due:next week/",$email_sub,$matches) || preg_match("/due:Next Week/",$email_sub,$matches)){
        $formated_date = date("Y-m-d",strtotime("next Friday",strtotime("+1 week"))); 
      }elseif(preg_match("/due:Later/",$email_sub,$matches) || preg_match("/due:later/",$email_sub,$matches)){
        $formated_date = '0000-00-00';
      }elseif(preg_match($dateformat1,$email_sub,$matches)){
        if(preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})$/",$email_sub,$matches1)){
          $date_parsed = $matches1[0];
          $formated_date = date("Y-m-d",strtotime($date_parsed));
        }
      }elseif(preg_match($dateformat2,$email_sub,$matches)){
        if(preg_match("/([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/",$email_sub,$matches1)){
          $date_parsed = $matches1[0];
          $formated_date = date("Y-m-d",strtotime($date_parsed));
        }
      }elseif(preg_match($dateformat3,$email_sub,$matches)){
        if(preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/",$email_sub,$matches1)){
          $date_parsed = $matches1[0];
          $formated_date = date("Y-m-d",strtotime($date_parsed));
        }
      }elseif(preg_match($dateformat4,$email_sub,$matches)){
        if(preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{4})$/",$email_sub,$matches1)){
          $date_parsed = $matches1[0];
          $formated_date = date("Y-m-d",strtotime($date_parsed));
        }
      }else{
        $formated_date = date("Y-m-d");
      }
    
      $today = date("Y-m-d");
      $difference = strtotime($formated_date) - strtotime($today);
      $date_diff = round((($difference/60)/60)/24,0);
      if($date_diff < 0 ){
          $return_string = 'Today';
      }elseif($date_diff == 0 ){
          $return_string = 'Today';
      }elseif($date_diff == 1){
          $return_string = 'Tomorrow';
      }elseif($date_diff >=2 && $date_diff < 8){
          $return_string = 'This week';
      }elseif($date_diff >7 && $date_diff < 15){
          $return_string = 'Next week';
      }elseif($date_diff > 15){
          $return_string = 'Later';
      }
      $due_date = $return_string;
      $is_sp_date_set = "Yes";
      $do_task->addNew();
      $do_task->category = $category;
      $do_task->due_date = $due_date;
      $do_task->due_date_dateformat = $formated_date;
      $do_task->iduser = $iduser;
      $do_task->is_sp_date_set = $is_sp_date_set;
      $do_task->status = 'open';
      $do_task->task_description = $parse_content;
      $do_task->add();
   }
}



/**
  * If the dropbox code is set to add the project note
  * Check if the sender is associated with the project task and if yes add the discussion for that project task
  * If the user is not associated with the project task then check if the project is a public project and if yes
  * add the discussion by specifying the drop_box_sender
*/    
if($addprojectnote === true){
   $parse_content = ereg_replace("^\>", "", $final_message_content);
   $do_project_task->getTaskDetailByDropBoxCode($drop_box_code_proj_task);
   //echo $do_project_task->idproject_task; echo $do_project_task->idproject;
   if($do_project_task->getNumRows() > 0 ){
        if($attachments_extracted === false){
            $attachment = $OfuzEmailFetcher->saveAttachments('/var/www/dev.ofuz.net/files/');// Change this file path to your file saving path for Contact Notes and Project discussion
            $attachments_extracted = true ;
        }
        $do_project->getId($do_project_task->idproject);
        $do_user->getUserDataByEmail($from_address);
        if($do_user->getNumRows() > 0 ){ 
            if(is_array($attachment) && count($attachment)> 0){
                $attachment_count = 0;
                foreach($attachment as $attachment){
                    $attachment_count++;
                    $do_project_discuss->addNew();
                    $do_project_discuss->idproject_task = $do_project_task->idproject_task;
                    $do_project_discuss->iduser = $do_user->iduser;
                    if($attachment_count == 1)
                        $do_project_discuss->discuss = $parse_content;
                    else
                        $do_project_discuss->discuss = 'Attachment';

                    $do_project_discuss->document = $attachment['filename'] ;
                    $do_project_discuss->date_added = date("Y-m-d");
                    $do_project_discuss->hours_work = 0.00;
                    $do_project_discuss->add();
                }
            }else{ 
                $do_project_discuss->addNew();
                $do_project_discuss->idproject_task = $do_project_task->idproject_task;
                $do_project_discuss->iduser = $do_user->iduser;
                $do_project_discuss->discuss = $parse_content;
                $do_project_discuss->hours_work = 0.00;
                $do_project_discuss->date_added = date("Y-m-d");
                $do_project_discuss->add();
            }
        }else{
            if($do_project->is_public == 1){
                if($from_name == ''){$from_name = $from_address;}
                if(is_array($attachment) && count($attachment)> 0){
                    $attachment_count = 0;
                    foreach($attachment as $attachment){
                        $attachment_count++;
                        $do_project_discuss->addNew();
                        $do_project_discuss->idproject_task = $do_project_task->idproject_task;
                        $do_project_discuss->drop_box_sender = $from_name;
                        if($attachment_count == 1)
                            $do_project_discuss->discuss = $parse_content;
                        else
                            $do_project_discuss->discuss = 'Attachment';

                        $do_project_discuss->date_added = date("Y-m-d");
                        $do_project_discuss->hours_work = 0.00;
                        $do_project_discuss->document = $attachment['filename'] ;
                        $do_project_discuss->add();
                    }
                }else{
                    $do_project_discuss->addNew();
                    $do_project_discuss->idproject_task = $do_project_task->idproject_task;
                    $do_project_discuss->drop_box_sender = $from_name;
                    $do_project_discuss->discuss = $parse_content;
                    $do_project_discuss->date_added = date("Y-m-d");
                    $do_project_discuss->hours_work = 0.00;
                    $do_project_discuss->add();
                }
            }
        }
    } 
}
    

if($add_project_task === true){
      $allow_db_operation = false;
      $allow_without_project_worker =  false;
      $do_user->getUserDataByEmail($from_address);
      $do_project->getId($drop_box_code_proj);
      $project_owner = $do_project->iduser;
  
      if($do_user->getNumRows() > 0 ){
          $user_array = $do_project->getAllUserFromProjectRel($drop_box_code_proj) ; 
          if( $user_array === false ){
              $user_array = array();
              $user_array[] = $do_project->iduser;  
          }

          $user_found_success = false ;
          foreach($user_array as $id){
            if($id == $do_user->iduser){
                $user_found_success = true ;
                break;
            }
          }
          if($user_found_success === true ) {
            $allow_db_operation = true;
            $iduser = $do_user->iduser ;
          }elseif($do_project->is_public == 1){
            $allow_db_operation = true;
            $allow_without_project_worker = true;
            $from_note = $from_name.'<br /> '.$from_address._(' has the following note : ').'<br />';
            $iduser = $project_owner;
          }
      }else{
          if($do_project->is_public == 1){
            $allow_db_operation = true;
            $allow_without_project_worker = true;
            $from_note = $from_name.'<br /> '.$from_address._(' has the following note : ').'<br />';
            $iduser = $project_owner;
          }
      }

      if($allow_db_operation === true ){
            if($attachments_extracted === false){
                $attachment = $OfuzEmailFetcher->saveAttachments('/var/www/dev.ofuz.net/files/');// Change this file path to your file saving path for Contact Notes and Project discussion
                $attachments_extracted = true ;
            }
            $task_description = $email_sub;
            $due_date = 'Today';
            $task_category = 'Email';
            $due_date_dateformat = date('Y-m-d');
            $do_task->addNew();
            $do_task->task_description = $task_description;
            $do_task->due_date = $due_date;
            $do_task->task_category = $task_category;
            $do_task->iduser = $iduser;
            $do_task->due_date_dateformat = $due_date_dateformat;
            $do_task->add();
            $idtask = $do_task->getInsertId();

            $do_project_task->addNew();
            $do_project_task->idtask = $idtask;
            $do_project_task->idproject = $drop_box_code_proj;
            $do_project_task->add();
            $idproject_task = $do_project_task->getInsertId();
            
            if(is_array($attachment) && count($attachment)> 0){
                $attachment_count = 0;
                foreach($attachment as $attachment){
                    $attachment_count++;
                    $do_project_discuss->addNew();
                    $do_project_discuss->idproject_task = $idproject_task;
                    $do_project_discuss->iduser = $iduser;
                    if($attachment_count == 1 )
                        $do_project_discuss->discuss = $from_note.$parse_content;
                    else
                        $do_project_discuss->discuss = 'Attachment';   
                    $do_project_discuss->date_added = date("Y-m-d");
                    $do_project_discuss->document = $attachment['filename'] ;
                    $do_project_discuss->add();
                }
            }else{
                $do_project_discuss->addNew();
                $do_project_discuss->idproject_task = $idproject_task;
                $do_project_discuss->iduser = $iduser;
                $do_project_discuss->discuss = $from_note.$parse_content;
                $do_project_discuss->date_added = date("Y-m-d");
                $do_project_discuss->document = $attachment['filename'] ;
                $do_project_discuss->add();
            }
          
       }
}



?>

