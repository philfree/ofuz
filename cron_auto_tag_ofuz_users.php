<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

	/**
	 * Auto tag for users who are in Phil's contact list
	 *
	 */

  set_time_limit(3600); 

  include_once("config.php");

  $do_user_loginstatus = new UserInternalMarketing();
  $do_user = new UserInternalMarketing();
  $do_user->getUsersFromPhilsContacts();
  while($do_user->next()) {
    $do_user_loginstatus->setActiveInactiveTag($do_user->iduser, $do_user->idcontact);
  }
?>
