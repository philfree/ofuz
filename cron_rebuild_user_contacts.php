<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

	/**
	 * Rebuild the user contacts table
	 * User contacts are accessed from the tables by userid<iduser>_contact
  * The contact table is updated for all the contact related operation and also the user contact table is getting updated on
  * add/edit/delete operation of contact, tag etc.
  * To make it more reliable the following script is used. This is rebuilt all the user contact tables.
  * This script is intensive as the volume of contact and user increases.
  * Wise to set the script once in a week. Also we can ignore rebuiling of user contact tables who have not logged in in certain period by passing the parameter
  * in getUserLoggedInWithinPeriod() default is 7 days
  * @see class/Contact.class.php
  * @see class/ContactView.class.php
  *
	 */
	 
	include_once("config.php");
	$contact_view = new ContactView(); 
	
	set_time_limit(36000); 	 
	
	$user = new User();
	$user->getUserLoggedInWithinPeriod();

	while ($user->next()) {
     // if($user->iduser == 20 ){
         echo "\n<br>User id:".$user->iduser." Name:".$user->firstname." ".$user->lastname;
         $contact_view->setUser($user->iduser);
         $contact_view->rebuildContactUserTable();
    //  }
	}
	 
?>
