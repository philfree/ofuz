<?php 
// Copyright 2012 SQLFusion LLC           info@sqlfusion.com

   /**
    * User Add
	*
    * @package RadriaSampleSite
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2012
    * @version 1.0
	*/

  $pageTitle = "Users :: Invoice :: Add" ;
  $Author = "SQLFusion";
  $Keywords = "Keywords for search engine";
  $Description = "Description for search engine";
  $background_color = "white";
  $background_image = "";
  
  include_once("config.php") ;
  include("includes/header.inc.php") ;

  $do_invoice = new Invoice();
  if (!isset($_SESSION['do_invoice'])) {
	  $do_invoice->sessionPersistent('do_invoice', 'user_detail.php');
  }
  $do_invoice->iduser = $_SESSION['do_user']->iduser;
  // after newaddForm() all call to: do_invoice->variable will output HTML form.
  $do_invoice->newaddForm();
  $do_invoice->initFields();
  $do_invoice->form->addEventAction('do_invoice->eventSetInvoiceNumber', 771);
  $do_invoice->form->addParam('goto', 'user_detail.php');
  $do_invoice->form->addParam('errorpage', 'message.php');
  
  echo $do_invoice->displayFormHeader();
  echo $do_invoice->iduser;
?>

Number: <?php echo $do_invoice->num; ?><br>
Description: <?php echo $do_invoice->description; ?><br>
Amount:<?php echo $do_invoice->amount; ?><br>
Date Created: <?php echo $do_invoice->datecreated; ?><br>
Status: <?php echo $do_invoice->status; ?><br>

<?php

 echo $do_invoice->displayFormFooter("Submit");
 
 include("includes/footer.inc.php");
 ?>
