<?php 
// Copyright 2012 SQLFusion LLC           info@sqlfusion.com

   /**
    * User Edit
	*
    * @package RadriaSampleSite
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2012
    * @version 1.0
	*/

  $pageTitle = "Users :: Edit" ;
  $Author = "SQLFusion";
  $Keywords = "Keywords for search engine";
  $Description = "Description for search engine";
  $background_color = "white";
  $background_image = "";
  
  include_once("config.php") ;
  include("includes/header.inc.php") ;

  // we are loading data in the do_user we need the do_user and _SESSION['do_user'] to be the 
  // same object. 
  if (!isset($_SESSION['do_user'])) {
	  $do_user = new User();
	  $do_user->sessionPersistent('do_user', 'index.php');
  } else {
	  $do_user = $_SESSION['do_user'];
  }
  
  if ($_GET['id']) {
	$do_user->getId((int)$_GET['id']);  
	$do_user->newUpdateform();
  } 
  
  // Load the fields descriptions
  $do_user->initFields();
  
  // Set additional parameters for eventactions
  $do_user->form->addParam('goto', 'users.php');
  $do_user->form->addParam('errorpage', 'message.php');
  
  echo $do_user->displayFormHeader();
?>

Firstname: <?php echo $do_user->firstname; ?><br>
Lastname: <?php echo $do_user->lastname; ?><br>
Email:<?php echo $do_user->email; ?><br>
Password: <?php echo $do_user->password; ?><br>
Status: <?php echo $do_user->status; ?><br>

<?php

 echo $do_user->displayFormFooter("Submit");
 
 include("includes/footer.inc.php");
 ?>
