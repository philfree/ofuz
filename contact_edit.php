<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');

    // PS need a better way to handle that type of message:
    if (!is_object($_SESSION['ContactEditSave']) && !isset($_GET['id'])) {
        echo "Missing idcontact please click back and try again";
        exit;
    } 
    // need to test this is a security danger:
    //if ($_SESSION['ContactEditSave']->idcontact != $_SESSION['portal_idcontact']) {
    include_once('includes/ofuz_check_access.script.inc.php');
    $pageTitle = _('Edit: ').$_SESSION['ContactEditSave']->getContactFullName().' :: Ofuz';
    include_once('includes/header.inc.php');
    //} else {
    //   include_once('includes/header.inc.php');
    //   include_once('includes/ofuz_portal_header.inc.php');
    //}
?>
<script type="text/javascript">
    //<![CDATA[
    //]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Contacts'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        &nbsp;
    </td><td class="layout_rcolumn">
        <div class="mainheader">
            <div class="pad20">
                <span class="headline14"><?php echo _('Edit Contact');?></span>
            </div>
        </div>
        <div class="contentfull">
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
  $e_fullContact->setGotFile(true);
  $e_fullContact->addEventAction("ContactEditSave->update", 2000);
  $e_fullContact->addEventAction("ContactPhone->eventSavePhones", 2001);
  $e_fullContact->addEventAction("ContactEmail->eventSaveEmails", 2002);
  $e_fullContact->addEventAction("ContactInstantMessage->eventSaveIM", 2003);
  $e_fullContact->addEventAction("ContactAddress->eventSaveContactAddress", 2004);
  $e_fullContact->addEventAction("ContactWebsite->eventSaveWebsites", 2005);
  //$e_fullContact->addEventAction("ContactRssFeed->eventSaveRssFeed", 2006);
  $e_fullContact->addEventAction("ContactEditSave->eventUpdateWebView", 2030);
  $e_fullContact->addEventAction("mydb.gotoPage", 2333);

  if(isset($_SESSION['edit_from_page'])) {
		$e_fullContact->addParam("goto", $_SESSION['edit_from_page'] );
		unset($_SESSION['edit_from_page']);

  }else{
		$e_fullContact->addParam("goto", "contact.php");
  }

  echo $e_fullContact->getFormHeader();
  echo $e_fullContact->getFormEvent();
  
  $_SESSION['ContactEditSave']->setFields("contact");
  //if ($_SESSION['ContactEditSave']->idcontact == $_SESSION['portal_idcontact']) {
  //  $_SESSION['ContactEditSave']->setApplyRegistry(true, "Disp");
  //} else {
    $_SESSION['ContactEditSave']->setApplyRegistry(true, "Form");
  //}
 
?>
                 <table class="tplain">
                    <tr>
                      <td>
                        <div class="in280x20">
                          <?php echo _('First name');?><br />
                          <?php echo $_SESSION['ContactEditSave']->firstname; ?>
                        </div>
                      </td>
                      <td>
                        <div class="in280x20">
                          <?php echo _('Last name');?><br />
                        <?php   echo $_SESSION['ContactEditSave']->lastname; ?>
                        </div>
                      </td>
                    </tr>
                  </table>
                  <div class="in290x18">
                    <?php echo _('Title'); ?><br />
                   <?php  echo $_SESSION['ContactEditSave']->position; ?>
                  </div>
                  <div class="in290x18">
                    <?php echo _('Company');?><br />
                    <?php echo $_SESSION['ContactEditSave']->company; ?>
 
                   </div>
                   <div class="in290x18">
                    <?php echo _('Picture');?><br />
                   <?php  echo $_SESSION['ContactEditSave']->picture; ?>
                  </div>
                   <div class="dashedline"></div>

<?php
  echo "<h3>"._('Phone')."</h3>";
  $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
  echo $ContactPhone->formMultiEntry();

  echo "<h3>"._('Email')."</h3>";
  $ContactEmail  = $_SESSION['ContactEditSave']->getChildContactEmail();
  echo $ContactEmail->formMultiEntry();
  //echo "<b>".$ContactEmail->hasData();
  //echo count($ContactEmail->values);
  //echo $ContactEmail->getNumRows();
  echo "</b>";

  echo "<h3>"._('IM')."</h3>";
  $ContactInstantMessage = $_SESSION['ContactEditSave']->getChildContactInstantMessage();
  echo $ContactInstantMessage->formMultiEntry();

  echo "<h3>"._("Website")."</h3>";
  $ContactWebsite = $_SESSION['ContactEditSave']->getChildContactWebsite();
  echo $ContactWebsite->formMultiEntry();

  echo "<h3>"._('Contact Address')."</h3>";
  $ContactAddress = $_SESSION['ContactEditSave']->getChildContactAddress();
  echo $ContactAddress->formMultiEntry();

  // As the feeds are directly in the website now we do not need it.
  //echo "<h3>RSS Feed Import</h3>";
  //$ContactRssFeed = $_SESSION['ContactEditSave']->getChildContactRssFeed();
  //echo $ContactRssFeed->formMultiEntry();


  echo "<br><br>";
  echo $e_fullContact->getFormFooter("Save");
  
?><hr />
<?php 
$_SESSION['ContactEditSave']->setApplyRegistry(false);
//echo $_SESSION['ContactEditSave']->idcontact; 
//echo "<br>key:".$_SESSION['ContactEditSave']->getPrimaryKeyValue();
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
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
