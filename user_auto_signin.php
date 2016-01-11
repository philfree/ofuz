<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

	/** 
	 *  user auto signin
	 *  use session variable set during the payment process 
	 *  to automatically sign in the new user.
	 */
  include("config.php");
  if ($_SESSION['autologin_userid'] > 5 && ($_SESSION['autologin_paid']) ) { 
	  $do_User = new User();
	  $do_User->getId($_SESSION['autologin_userid']); 
	  $do_User->setSessionVariable(); 
	  $do_login_audit = new LoginAudit();
	  $do_login_audit->do_login_audit();
	  
	  $do_contact = $do_User->getChildContact();
	  if ($do_contact->getNumRows() > 1) { 
		  header("Location: /");
	  } else {
		  header("Location: /import_contacts.php");
	  }
	  
  } else { header("Location: /user_login.php"); }
