<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 * A cron job script for sending the autoresponders.
 * Will get the autoresponders with the tags and will send the email to the contacts associated with the tag_name
 * @see class/Autoresponder.class.php
 */

include_once('config.php');

$AutoResponder = new AutoResponder();
$AutoResponder->getAutoresponders();

if($AutoResponder->getNumRows()){ 
    while($AutoResponder->next()){ 
        $User = new User(); 
        $User->getId($AutoResponder->iduser);
        $User->sessionPersistent('do_User', 'contacts.php', OFUZ_TTL);
        $email_template = new EmailTemplate();
        $email_template->bodyhtml = nl2br(stripslashes($AutoResponder->bodyhtml));
        $email_template->subject = $AutoResponder->subject;
        $email_template->senderemail = $User->email;
        $email_template->sendername = $User->firstname.' '.$User->lastname;
        $resp_name = $AutoResponder->resp_name;
        $Contact = new Contact();
        $Contact->getContactsForAutoResponder($AutoResponder->iduser,$AutoResponder->tag_name,$AutoResponder->num_days_to_send);
        
        if($Contact->getNumRows()){
            while($Contact->next()){
                $values = array("idcontact"=>$Contact->idcontact,"firstname"=>$Contact->firstname,"lastname"=>$Contact->lastname,"position"=>$Contact->position,"company"=>$Contact->company,"idtag"=>$Contact->idtag,"resp_name"=>$resp_name,"flag"=>"unsubscribe_autoresponder");
                $Contact->sendMessage($email_template,$values);
                echo '<br /> Email Sent to :'.$Contact->email_address;
            }
        }
        $Contact->free();
    }
   $AutoResponder->free();
   
   
}
