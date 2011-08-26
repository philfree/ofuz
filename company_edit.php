<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Edit company';
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
    include_once('includes/header.inc.php');
?>
<script type="text/javascript">
    //<![CDATA[
    //]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        &nbsp;
    </td><td class="layout_rcolumn">
        <div class="mainheader">
            <div class="pad20">
                <h1><?php echo _('Edit Company'); ?></h1>
            </div>
        </div>
        <div class="contentfull">
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
  $e_fullContact->addParam("goto", "company.php");
  echo $e_fullContact->getFormHeader();
  echo $e_fullContact->getFormEvent();
  
  $_SESSION['CompanyEditSave']->setRegistry("company");
  $_SESSION['CompanyEditSave']->setApplyRegistry(true, "Form");
?>
                <table class="tplain">
                    <tr>
                      <td>
                        <div class="in280x20">
                            <?php echo $_SESSION['CompanyEditSave']->name; ?>
                         </div>
                      </td>
                     </tr>
                  </table>
<div class="dashedline"></div>

<?php 

  echo "<h3>"._('Phone')."</h3>";
  $CompanyPhone = $_SESSION['CompanyEditSave']->getChildCompanyPhone();
  echo $CompanyPhone->formMultiEntry();

  echo "<h3>"._('Email')."</h3>";
  $CompanyEmail= $_SESSION['CompanyEditSave']->getChildCompanyEmail();
  echo $CompanyEmail->formMultiEntry();


  echo "<h3>"._('Contact Address')."</h3>";
  $ContactAddress = $_SESSION['CompanyEditSave']->getChildCompanyAddress();
  echo $ContactAddress->formMultiEntry();

  echo "<h3>"._('Website')."</h3>";
  $CompanyWebsite = $_SESSION['CompanyEditSave']->getChildCompanyWebsite();
  echo $CompanyWebsite->formMultiEntry();

  echo "<br><br>";
  echo $e_fullContact->getFormFooter("Save");
  
?><hr>
<?php 
$_SESSION['CompanyEditSave']->setApplyRegistry(false);
?>
                    <div class="section20">
                        <!-- <input type="submit" value="Add this company" />-->
                    </div>
                    <?php //echo $do_contact->getFormFooter(); ?>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>