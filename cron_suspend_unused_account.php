<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

	/**
	 * Auto tag for users who are in Phil's contact list
	 *
	 */

  set_time_limit(3600); 

  include_once("config.php");

  $do_user = new User();
  $do_suspend_user = new User();
  $do_user->getUserNotLoggedInWithinPeriod(60);
  if($do_user->getNumRows() > 0 ){
      while($do_user->next()){
            if($do_user->plan != 'free') continue;
            $do_suspend_user->suspendUser($do_user->iduser);
            echo 'Account suspended user :: '.$do_user->iduser.'<br />';
            if($do_user->iduser == 102 || $do_user->iduser == 19){
              // Send Email To user
              $email_template = new EmailTemplate("suspend_unused_account");
              $email_data = array("firstname"=>$do_user->firstname,
                                  "lastname"=>$do_user->lastname
                                 );
              $emailer = new Radria_Emailer();
              $emailer->setEmailTemplate($email_template);
              $emailer->mergeArray($email_data);
              $emailer->addTo($do_user->email);
              $emailer->send();
              echo 'Email Sent to :: '.$do_user->email.'<br />';
            }
      }
  }

?>
