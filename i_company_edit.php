<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Edit contact';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
// PS need a better way to handle that type of message:
    if (!is_object($_SESSION['CompanyEditSave']) && !isset($_GET['id'])) {
        echo "Missing idcompany please click back and try again";
        exit;
    }
    include_once('includes/i_header.inc.php');
?>
<?php $thistab = 'Company'; include_once('i_ofuz_navtabs.php'); ?>
<div class="mobile_main">
   <div class="mainheader">
       <div class="mobile_head_pad5">
                    <h1>Edit Company</h1>
       </div>
    </div>
    <div class="mobile_head_pad5">
<?php
  if (!is_object($_SESSION['CompanyEditSave'])) {
    $CompanyEdit  = new Company($GLOBALS['conx']);
    $CompanyEdit->sessionPersistent("CompanyEditSave", "index.php", 120);
  }
  $_SESSION['CompanyEditSave']->getId((int)$_GET['id']);
  $e_fullContact = new Event("CompanyEditSave->eventValuesFromForm");
  $e_fullContact->setLevel(1999);
  $e_fullContact->addEventAction("CompanyEditSave->eventCheckDuplicateCompanyInUpdate", 1000);
  $e_fullContact->addEventAction("CompanyEditSave->eventUpdate", 2000);
  $e_fullContact->addEventAction("CompanyPhone->eventSavePhones", 2001);
  $e_fullContact->addEventAction("CompanyEmail->eventSaveEmails", 2002);
  //$e_fullContact->addEventAction("ContactInstantMessage->eventSaveIM", 2003);
  $e_fullContact->addEventAction("CompanyAddress->eventSaveContactAddress", 2003);
  $e_fullContact->addEventAction("CompanyWebsite->eventSaveWebsites", 2004);
  $e_fullContact->addEventAction("mydb.gotoPage", 900);
  $e_fullContact->addParam("goto", "i_company.php");
  echo $e_fullContact->getFormHeader();
  echo $e_fullContact->getFormEvent();
  
  $_SESSION['CompanyEditSave']->setRegistry("company");
  $_SESSION['CompanyEditSave']->setApplyRegistry(true, "Form");
?>

                        <div >
                            <?php echo $_SESSION['CompanyEditSave']->name; ?>
                         </div>
<div class="dashedline"></div>

<?php 

  echo "<h3>Phone</h3>";
  $CompanyPhone = $_SESSION['CompanyEditSave']->getChildCompanyPhone();
  echo $CompanyPhone->formMultiEntry();

  echo "<h3>Email</h3>";
  $CompanyEmail= $_SESSION['CompanyEditSave']->getChildCompanyEmail();
  echo $CompanyEmail->formMultiEntry();


  echo "<h3>Contact Address</h3>";
  $ContactAddress = $_SESSION['CompanyEditSave']->getChildCompanyAddress();
  echo $ContactAddress->formMultiEntry();

  echo "<h3>Website</h3>";
  $CompanyWebsite = $_SESSION['CompanyEditSave']->getChildCompanyWebsite();
  echo $CompanyWebsite->formMultiEntry();

  echo "<br><br><div align=\"right\">";
  echo $e_fullContact->getFormFooter("Save");
  echo "</div>";  

?><hr>
<?php 
$_SESSION['CompanyEditSave']->setApplyRegistry(false);
?>
       </div>
      <div class="bottompad40"></div>

<?php include_once('i_ofuz_logout.php'); ?>
</div>
</body>
</html>