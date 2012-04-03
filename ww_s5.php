<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Setup Public Profile';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');
     
?>
<style type="text/css">
#simplemodal-overlay {background-color:#000;}
#simplemodal-container {background-color:#333; height:auto; border:8px solid #444; padding:12px;}
</style>

<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns">
  <tr>
    <td class="layout_lmargin"></td>
    <td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Setup Public Profile');?></span>
        </div>
    </div>
    <div class="contentfull">        
      <div class="messageshadow">
	<div class="messages" style="font-size:1.8em;">Ofuz Getting started wizard</div>
      </div>

      <div align="center">
      <p id="pYourFirstProject" style="font-size:1.4em;">Setup Public Profile</p>

	<div id="setup_public_profile">
	  <div class="spacerblock_20"></div>
<table width="450">
  <tr>
    <td>
<?php
  if (!is_object($_SESSION['ContactEditSave'])) {
    $ContactEdit  = new Contact($GLOBALS['conx']);
    $ContactEdit->sessionPersistent("ContactEditSave", "index.php", 120);
  }
  if (isset($_SESSION['do_User']->idcontact)) {
     $_SESSION['ContactEditSave']->getId((int)$_SESSION['do_User']->idcontact);
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
  $e_fullContact->addEventAction("ContactEditSave->eventUpdateWebView", 2030);
  $e_fullContact->addEventAction("mydb.gotoPage", 2333);

  $e_fullContact->addParam("goto", "index.php");
  echo $e_fullContact->getFormHeader();
  echo $e_fullContact->getFormEvent();
  
  $_SESSION['ContactEditSave']->setFields("contact");

  $_SESSION['ContactEditSave']->setApplyRegistry(true, "Form");

?>

<div class="in280x20">
<?php echo _('First name');?><br />
<?php echo $_SESSION['ContactEditSave']->firstname; ?>
</div>

<div class="in280x20">
<?php echo _('Last name');?><br />
<?php   echo $_SESSION['ContactEditSave']->lastname; ?>
</div>

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

<?php
  echo "<h3>"._('Phone')."</h3>";
  $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
  echo $ContactPhone->formMultiEntry();

  echo "<h3>"._('Email')."</h3>";
  $ContactEmail  = $_SESSION['ContactEditSave']->getChildContactEmail();
  echo $ContactEmail->formMultiEntry();
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

  echo "<br><br>";
  //echo $e_fullContact->getFormFooter("Save");
  
?>
<?php 
$_SESSION['ContactEditSave']->setApplyRegistry(false);
?>
    </td>
  </tr>
</table>

	</div>
      <div class="spacerblock_40"></div>
      <div>
	<a id="next" href="javascript:;"><input type="image" src="/images/next.jpg" border="0" /></a> <br />
	<a href="index.php" title="">Skip >></a>
      </div>
      <div class="spacerblock_80"></div>

      <div class="layout_footer"></div>

     </div>
</form>
</td>
<td class="layout_rmargin"></td>
</tr></table>
</body>
</html>