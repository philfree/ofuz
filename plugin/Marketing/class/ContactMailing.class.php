<?php 
// Copyright 2008 - 2012 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html 
 **/ 

    /**
      * ContactMailing class
      * Using the Contact and DataObject
      * 
      * Method to reuse:
      * sendMessage() its a very simple and easy way to send messages to contacts.
      * 
      *
      * @author SQLFusion's Dream Team 2 <info@sqlfusion.com>
      * @package Marketing
      * @license GNU Affero General Public License
      * @version 0.6
      * @date 2010-09-03
      * @since 0.1
      */
      
class ContactMailing extends Contact {      
 
     function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_PLUGIN_MARKETING);
		}

		$this->setLog("\n ContactMailing Object instantiated");
    }
      
    /**
     * eventGetForMailMerge
     * Get the list of contact for mailmerge
     * load in the current contact object the list of
     * selected contacts from the contacts.php
     * Then redirect to the SendMessage page.
     * 
     * In here we are doing something not very elegant but to grab the proper
     * information from the Contacts object we linked it with this new Object.
     * @Note this assignment may need to be revisited in the future.
     */
    function eventGetForMailMerge(EventControler $event_controler) {
        $idcontacts = $event_controler->getParam("ck");
        $sql_in = "(";
        foreach($idcontacts as $idcontact) {
            $sql_in .= $idcontact.",";
        }
        $sql_in = substr($sql_in, 0, -1);
        $sql_in .= ")";
        $tag_search = $_SESSION['do_Contacts']->getSearchTags();
        if(is_array($tag_search) && count($tag_search > 0)){
              $_SESSION['searched_tags'] = $tag_search ;
        }
        $this->clearSearch();
        $this->setSqlQuery("SELECT * FROM contact WHERE idcontact in ".$sql_in);
        $this->sessionPersistent("do_ContactMailing", "contacts.php", 36000);
        $event_controler->goto = $GLOBALS['cfg_plugin_mkt_path']."SendMessage";
        //$event_controler->setDisplayNext(new Display($GLOBALS['cfg_plugin_mkt_path']."SendMessage/"));
    }

     /** 
      *  eventSendMessage
      *  This will send a message to one or multiple users.
	  *  @todo Problem with the getBodyText(true) it returns some bad encoding and no newline, could also be the problem in the template it self...
      */

     function eventSendMessage(EventControler $event_controler) { 
        $this->setLog("\n eventSendMessage (".date("Y/m/d H:i:s").")");
        if ($event_controler->send == _('Send Mailing')) {
            $email_template = $_SESSION['do_message'];
            $email_template->setApplyFieldFormating(false);
            $this->setLog("\n EmailTemplate id: ".$email_template->getPrimarykeyValue());
            //$this->setLogObject($email_template);
            $this->setLog("\n SQL:".$this->getSqlQuery());
            $this->query();
            $this->setLog("\n ".$this->getNumRows()." contacts to send the message");

            while ($this->next()) {	
                set_time_limit(500);
                if($event_controler->unsubtag == 'on'){ // set unsubscribe auto-responder list 
                    $values_contact = $this->getValues();
                    $tag_values = array("flag"=>"unsubscribe_autoresponder","idtag"=>$event_controler->idtag);
                    $values = array_merge($values_contact,$tag_values);
                    $message = $this->sendMessage($email_template, $values);
                }else{
                    $message = $this->sendMessage($email_template, $this->getValues());
                }
                if ($this->last_message_sent) {	
                    //$do_contact_notes->iduser = $_SESSION['do_User']->iduser;
                    $do_contact_notes = new ContactNotes();
                    $do_contact_notes->iduser = $_SESSION['do_User']->iduser;
                    //$do_contact_notes->note = '<b>'.$message->getSubject().'</b><br/>'.$message->getBodyText(true);
                    //->getTemplateBodyHtml();
                    $note_text = preg_replace('/^(.*?)<hr>.*$/','$1',str_replace("\n",'',$message->getBodyHtml(true)));
                    $do_contact_notes->note = '<b>'.$message->getSubject().'</b><br/>'.$note_text;
                    $do_contact_notes->date_added = date("Y-m-d");	 
                    $do_contact_notes->idcontact = $this->getPrimaryKeyValue();
                    $do_contact_notes->add();


                    /*
                     * Recording the messages sent by User
                     */
                    $msg_con = new sqlQuery($this->getDbCon());
                    $sql_msg_check = "SELECT * FROM `message_usage` WHERE `iduser` = ".$_SESSION['do_User']->iduser." AND `date_sent` = '".date("Y-m-d")."'";                    
                    $msg_con->query($sql_msg_check);
                    if($msg_con->getNumRows()) {
                      $msg_con->fetch();
                      $sql_con_update = new sqlQuery($this->getDbCon());
                      $sql_msg_update = "UPDATE `message_usage` SET `num_msg_sent` = num_msg_sent+1 WHERE `idmessage_usage` = ".$msg_con->getData("idmessage_usage");
                      $sql_con_update->query($sql_msg_update);
                    } else {
                      $sql_con_ins = new sqlQuery($this->getDbCon());
                      $sql_msg_ins = "INSERT INTO `message_usage`(iduser,date_sent,num_msg_sent) VALUES(".$_SESSION['do_User']->iduser.",'".date("Y-m-d")."',1)";
                      $sql_con_ins->query($sql_msg_ins);
                    }
                    
                    
                    
                }		
            }
        } else { $event_controler->goto = "contacts.php"; }
        $this->clearSearch();
        $this->free();
     }
  
     /** 
      *   eventSendNote
      *   This event is triggered when adding a note in a contact 
	  *   It will send a copy of the note the contact.
      */

     function eventSendNote(EventControler $event_controler) {
        $send_note = $event_controler->fields['send_email'];
        $this->setLog("eventSendNote starting (".date("Y/m/d H:i:s").")");
        $this->setLog("do we send a message:".$send_note);
        if ($send_note == "y") {
          $template = new EmailTemplate();
          $template->setSubject("{Ofuz} Message: ".substr($event_controler->fields['note'], 0, 70)."...")
            ->setMessage($event_controler->fields['note']);
          $this->sendMessage($template);
        }
     }  
  
     /**
      *  sendMessage
      *  This abstract the message sending so we use a general function
      *  that will send email or facebook or twitter based on the user
      *  preferences and settings.
      *  its possible to generate an EmailTemplate on the fly with no records in the DB
      *  Here is an exemple:
      *  <code php>
      *  $do_template = new EmailTemplate();
      *  $do_template->senderemail = "philippe@sqlfusion.com";
      *  $do_template->sendername = "Philippe Lewicki";
      *  $do_template->subject = "This is an example";
      *  $do_template->bodytext = "This is the content of the sample message";
      *  $do_template->bodyhtml = nl2br($do_template->bodytext);
      *  </code>
      * 
      *  An other example more OO / stylish
      *  <code php>
      *  $do_template = new EmailTemplate();
      *  $do_template->setFrom("phil@sqlfusion.com", "Philippe Lewicki")
      *              ->setSubject("This is an example")
      *              ->setMessage("This is the content of the sample message");
      *  </code>
      *  setFrom() is optional, if not provided it takes the do_User data
      *  
      *  Then send the message with:  $contact->sendMessage($do_template);
      * 
      *  If you used a saved EmailTemplate like
      *  $do_template = new EmailTemplate("my template in email template table");
      *  and want the sender to be the currently signed in user, make sure the senderemail field
      *  is empty.
      * 
      *  @param $message an EmailTemplate object.
      *  @param $values an array with values to merge, optional.
      *  
      */
     function sendMessage($template, $values=Array()) {
        if (!is_object($template)) { return false; }
        if (empty($values)) { $values = $this->getValues(); }
        $this->last_message_sent = false;
        $do_contact_email = $this->getChildContactEmail();
        $email_to = $do_contact_email->getDefaultEmail();
        $this->setLog("\n Sending message to:".$email_to);   
        $contact_link = '<a href="/Contact/'.$this->idcontact.'">'.$this->firstname.' '.$this->lastname.'</a>';     
        if (strlen($email_to) > 4) { 
            if ($this->email_optout != 'y') {
                $emailer = new Ofuz_Emailer('UTF-8');
                if (strlen($template->senderemail) == 0) {
                    $template->setFrom($_SESSION['do_User']->email,  $_SESSION['do_User']->getFullName());				
                }
                $emailer->setEmailTemplate($template);
                $emailer->mergeArrayWithFooter($values);
                $emailer->addTo($email_to);
                $this->last_message_sent = true;
                return $emailer->send();
            } else {
                $_SESSION['in_page_message'] .= _("<br>".$contact_link." has opt-out and will not receive this email");
            }
        } elseif (strlen($this->tw_user_id) > 0) {
            // send direct message using twitter api.
            try{
                $do_twitter = new OfuzTwitter();
                $tw_config = $do_twitter->getTwitterConfig();
                $serialized_token = $do_twitter->getAccessToken();
                $token = unserialize($serialized_token);
                $ofuz_twitter = new Ofuz_Service_Twitter($token->getParam('user_id'), $tw_config, $token);
                $followers = $ofuz_twitter->userFollowers(false);
                if (is_object($followers) && count($followers->user) > 0) {
                    foreach ($followers->user as $follower) {
                        if ($follower->id == $this->tw_user_id) {
                            $merge = new MergeString();
                            $message = $merge->withArray($template->bodytext, $values);
                            $ofuz_twitter->directMessageNew($this->tw_user_id, $message);
                            $do_contact_notes = new ContactNotes();
                            $do_contact_notes->iduser = $_SESSION['do_User']->iduser;
                            $do_contact_notes->note = $message;
                            $do_contact_notes->date_added = date('Y-m-d');	 
                            $do_contact_notes->idcontact = $this->idcontact;
                            $do_contact_notes->add();
                            return true;
                        }
                    }
                }
                $_SESSION['in_page_message'] .= _("<br>Notification can not be sent to ".$contact_link);
            }catch(Exception $e){
                $_SESSION['in_page_message'] .= _("<br>Notification can not be sent to ".$contact_link);
            }
        } elseif ($this->fb_userid && $_SESSION["do_User"]->global_fb_connected) {
            // send message using facebook api.
            include_once 'facebook_client/facebook.php';
            include_once 'class/OfuzFacebook.class.php';
            $facebook = new Facebook(FACEBOOK_API_KEY, FACEBOOK_APP_SECRET);
            try{
                $msg = _(' has sent the following message using ofuz').'<br />';
                $msg .= '<b>'.$template->subject.'</b><br/>'.$template->bodyhtml;
                $facebook->api_client->notifications_send($this->fb_userid, $msg, 'user_to_user');
                //$this->last_message_sent = true;
            }catch(Exception $e){
                $_SESSION['in_page_message'] .= _("<br>Notification can not be sent to ".$contact_link);
            }
        } else {
            $_SESSION['in_page_message'] .= _("<br>".$contact_link." doesn't have a valid email address, facebook account, or twitter id.");
        }
        
        if ($this->last_message_sent) {
            return true;
        } else { 
            return false; 
        }		 
    }
    
    
    
}
