<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Edit contact';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
// PS need a better way to handle that type of message:
    if (!is_object($_SESSION['ContactEditSave']) && !isset($_GET['id'])) {
        echo "Missing idcontact please click back and try again";
        exit;
    }
    include_once('includes/i_header.inc.php');
?>
<?php $thistab = 'Contacts'; include_once('i_ofuz_navtabs.php'); ?>
<div class="mobile_main">
   <div class="mainheader">
       <div class="mobile_head_pad5">
           <h1>Edit Contact</h1>
       </div>
   </div>
   <div class="mobile_head_pad5">

<?php
  if (!is_object($_SESSION['ContactEditSave'])) {
    $ContactEdit  = new Contact($GLOBALS['conx']);
    $ContactEdit->sessionPersistent("ContactEditSave", "index.php", 120);
  }
  if (isset($_GET['id'])) {
     $_SESSION['ContactEditSave']->getId((int)$_GET['id']);
  }
  $e_fullContact = new Event("ContactEditSave->eventValuesFromForm");
  $e_fullContact->setLevel(1999);
  $e_fullContact->addEventAction("ContactEditSave->update", 2000);
  $e_fullContact->addEventAction("ContactPhone->eventSavePhones", 2001);
  $e_fullContact->addEventAction("ContactEmail->eventSaveEmails", 2002);
  $e_fullContact->addEventAction("ContactInstantMessage->eventSaveIM", 2003);
  $e_fullContact->addEventAction("ContactAddress->eventSaveContactAddress", 2004);
  $e_fullContact->addEventAction("ContactWebsite->eventSaveWebsites", 2005);
  $e_fullContact->addEventAction("ContactRssFeed->eventSaveRssFeed", 2006);
  $e_fullContact->addEventAction("mydb.gotoPage", 2333);
  $e_fullContact->addParam("goto", "i_contact.php");
  echo $e_fullContact->getFormHeader();
  echo $e_fullContact->getFormEvent();
  
  $_SESSION['ContactEditSave']->setRegistry("i_ofuz_add_contact");
  $_SESSION['ContactEditSave']->setApplyRegistry(true, "Form");
 
?>

            <div>
                First name<br />
                <?php echo $_SESSION['ContactEditSave']->firstname; ?>
            </div>

            <div>
                Last name<br />
            <?php   echo $_SESSION['ContactEditSave']->lastname; ?>
            </div>
                
            <div>
                Title<br />
            <?php  echo $_SESSION['ContactEditSave']->position; ?>
            </div>
            <div>
            Company<br />
            <?php echo $_SESSION['ContactEditSave']->company; ?>
            </div>
            <div class="dashedline"></div>

<?
  echo "<h3>Phone</h3>";
  $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
  echo $ContactPhone->formMultiEntry();

  echo "<h3>Email</h3>";
  $ContactEmail  = $_SESSION['ContactEditSave']->getChildContactEmail();
  echo $ContactEmail->formMultiEntry();
  //echo "<b>".$ContactEmail->hasData();
  //echo count($ContactEmail->values);
  //echo $ContactEmail->getNumRows();
  echo "</b>";

  echo "<h3>IM</h3>";
  $ContactInstantMessage = $_SESSION['ContactEditSave']->getChildContactInstantMessage();
  echo $ContactInstantMessage->formMultiEntry();

  echo "<h3>Contact Address</h3>";
  $ContactAddress = $_SESSION['ContactEditSave']->getChildContactAddress();
  echo $ContactAddress->formMultiEntry();

  echo "<h3>Website</h3>";
  $ContactWebsite = $_SESSION['ContactEditSave']->getChildContactWebsite();
  echo $ContactWebsite->formMultiEntry();

  echo "<h3>RSS Feed Import</h3>";
  $ContactRssFeed = $_SESSION['ContactEditSave']->getChildContactRssFeed();
  echo $ContactRssFeed->formMultiEntry();


  echo "<br><br><div align=\"right\">";
  echo $e_fullContact->getFormFooter("Save");
  echo "</div>";
  
?><hr />
<?php 
$_SESSION['ContactEditSave']->setApplyRegistry(false);
//echo $_SESSION['ContactEditSave']->idcontact; 
//echo "<br>key:".$_SESSION['ContactEditSave']->getPrimaryKeyValue();
?>
       <div class="bottompad40"></div>
    </div>
<?php include_once('i_ofuz_logout.php'); ?>
</div>
</body>
</html>
