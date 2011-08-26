<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * unsubscribe particular auto responder for a contact. 
   */
   
	include_once("config.php");
	include_once("class/Emailer.class.php");
	$background_color = 'white';
	include_once('includes/header.inc.php');
        
	$idcontact = (int)$_GET['idc'];
	$iduser = (int)trim($_GET['idu']);
	$idtag = (int)trim($_GET['idt']);
   
	$do_contact = new Contact();
	$do_contact->getId($idcontact);
	$firstname = $do_contact->firstname;
	$lastname = $do_contact->lastname;
	$email_address = $do_contact->getDefaultEmailId($idcontact);
	$message = '';
	$do_tag = new Tag();
	if($do_tag->isTagValidTagId($idtag) === true ){
	      $do_tag->getId($idtag);
	      $tag_name = $do_tag->tag_name;

	      $do_user = new User();
	      $do_user->getId($iduser);
	      $user_idcontact = $do_user->idcontact;

	      $q_auto_resp = new sqlQuery($GLOBALS['conx']);
	      /*$sql_auto_resp = "SELECT ar.name,arem.subject FROM autoresponder AS ar 
			    INNER JOIN autoresponder_email AS arem ON ar.idautoresponder = arem.idautoresponder
						WHERE ar.iduser = {$iduser} AND ar.tag_name = '{$tag_name}'";*/
	      $sql_auto_resp = "SELECT `name` FROM autoresponder
        WHERE iduser = {$iduser} AND tag_name = '{$tag_name}'";
	      $q_auto_resp->query($sql_auto_resp);
	      $responder = "";
	      $resp_email_subj = "";
	      if($q_auto_resp->getNumRows()) {
		      $q_auto_resp->fetch();
		      $responder = $q_auto_resp->getData("name");
		      //$resp_email_subj = $q_auto_resp->getData("subject");
	      }else{
            $message = 'already_unsub_from_list' ;
	      }

	      $do_cont_note = new ContactNotes();
	      $do_cont_note->addNew();
	      $do_cont_note->idcontact = $idcontact;
	      $do_cont_note->note =  $firstname." ".$lastname." has unsubscribed from the autoresponder series called ".$responder;
	      $do_cont_note->date_added = date("Y-m-d");
	      $do_cont_note->iduser = $iduser;
	      $do_cont_note->add();

	      $do_workfeed_uns = new WorkFeedContactUnsubscibeEmails();
       $do_workfeed_uns->addUnsubscribeEmailWorkfeed($do_cont_note,$responder) ;

	      $q = new sqlQuery($GLOBALS['conx']);
	      $sql = "DELETE FROM `tag` WHERE iduser={$iduser} AND idreference={$idcontact} AND idtag={$idtag}";
	      $q->query($sql);
	
	      $do_contact_view = new ContactView();
	      $do_contact_view->setUser($iduser);
	      $do_contact_view->deleteTag($do_tag->tag_name,$idcontact);

	      //$message = $firstname." ".$lastname.' '._('you have successfully unsubscribed from the autoresponder series called').' '.$responder;
          $message = 'unsub_list_message';
	      $data = array("firstname"=>$firstname,"lastname"=>$lastname,"responder"=>$responder);
       $do_contact->sendMessage(new EmailTemplate("unsubscribe_auto_responder"),$data);
	  }else{
	      $message = 'already_unsub_from_list' ;
	  }
	//header("Location: http://ofuz.com");
	//exit();
?>
<div class="loginbg1">
    <!--<div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>-->
    <div style = "width:500px;margin: 0 auto; padding: 10px; text-align:left;">
        <?php
	    if($message != ''){
		  $msg = new Message();
          $msg->setData(array("firstname"=>$firstname,"lastname"=>$lastname,"responder"=>$responder));
          $msg->getMessage($message);
		  $msg->displayMessage();
          
	    }
	?>
    </div>
    <div style = "width:500px;margin: 0 auto; padding: 10px; text-align:left;">
        <?php echo '<b>'._('Powered By').'</b> '.'<a href="http://www.ofuz.com"><img style="vertical-align: top;" src = "http://www.ofuz.com/images/ofuz_title_small.png" ></a>';?>
    </div>
</div>
