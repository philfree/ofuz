<?php 
// Copyright 2012 SQLFusion LLC           info@sqlfusion.com

   /**
    * User Details
	*
    * @package RadriaSampleSite
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2012
    * @version 1.0
	*/

  $pageTitle = "Users :: Details" ;
  $Author = "SQLFusion";
  $Keywords = "Keywords for search engine";
  $Description = "Description for search engine";
  $background_color = "white";
  $background_image = "";
  
  include_once("config.php") ;
  include("includes/header.inc.php") ;

  if (!isset($_SESSION['do_user'])) {
	  $do_user = new User();
	  $do_user->sessionPersistent('do_user', 'index.php');
  } else {
	  $do_user = $_SESSION['do_user'];
  }
  
  if ($_GET['id']) {
	$do_user->getId((int)$_GET['id']);  
  } 
  

?>
<div><a href="users.php">Return to the list of users</a></div>
<div><a href="user_edit.php?id=<?php echo $do_user->iduser; ?>">Edit User: <?php echo $do_user->firstname; ?></a><br>
<?php
  $do_user->initFields();
  // override the default email fieldtype from FieldTypeLogin to FieldTypeEmail
  $do_user->fields->addField(new FieldTypeEmail('email'));
  
  $do_user->setFieldsFormating(true);
  echo $do_user->firstname; echo "<br>";
  echo $do_user->lastname; echo "<br>";
  echo $do_user->email; echo "<br>";
  echo $do_user->status; echo "<br>";
  echo $do_user->regdate; echo "<br>";
  $do_user->setFieldsFormating(false);
?>
</div>
<div><a href="invoice_add.php">Add Invoice</a></div>
<?php 
  $do_invoice = $do_user->getChildInvoice();
// Load the fields type and turn on formating
  $do_invoice->initFields();
  $do_invoice->setFieldsFormating(true);
  while ($do_invoice->next()) {
  	  echo $do_invoice->idinvoice." | ".$do_invoice->num." | ".$do_invoice->description." - ".$do_invoice->amount;
	  echo "\n<br>";
  }
  $do_invoice->setFieldsFormating(false);
  
 include("includes/footer.inc.php");
 ?>
