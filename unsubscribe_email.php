<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * unsubscribe a user 
   */
   
   include_once("config.php");
   
   $idcontact = (int)$_GET['idc'];
   $iduser = (int)trim($_GET['idu']);
   
   $do_contact = new Contact();
   //$do_contact->getId($idcontact);
   $do_user = new User();
   $do_user->getId($iduser);
   $do_user->sessionPersistent('user_unsub', 'unsubscribe_message.php', TTL_OFUZ);
   $do_contact->query("SELECT * 
                            FROM  contact 
                            WHERE contact.idcontact=".$idcontact." AND contact.iduser = ".$iduser);
   if ($do_contact->getNumRows() == 0) {
      $do_contact->query("SELECT contact.*
                            FROM contact, contact_sharing 
                            WHERE `contact_sharing`.`idcoworker` = ".$iduser."
                            AND contact.idcontact = contact_sharing.idcontact
							AND contact.idcontact=".$idcontact);
   }
   if ($do_contact->getNumRows() == 1 && $do_user->getNumRows() == 1) {
	   $do_contact->email_optout = 'y';
	   $do_contact->update();
    
    $do_workfeed_uns = new WorkFeedContactUnsubscibeEmails();
    $do_workfeed_uns->addUnsubscribeEmailWorkfeed($do_contact) ;

   }
   //$do_contact->sessionPersistent('do_contact', 'unsubscribe_messaage.php', OFUZ_TTL);
   header("Location: /unsubscribe_message.php");
   exit();
?>
