<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2012 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Account Backup';
    $Author = 'SQLFusion LLC';
    $Keywords = '';
    $Description = '';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
?>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
		$GLOBALS['thistabsetting'] = 'My Account Backup';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('My Account Backup'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php //echo _('Export'); ?></div>
        <div class="contentfull">
        <?php 
	  echo   '<div class="messageshadow">';
	  echo     '<div class="messages">';
	  $msg = new Message(); 
	  echo $msg->getMessage('ofuz_export_xml');
	  echo '</div></div>';
	  echo '<br />';
         ?>
		<div>
			<?php
			$e_export_contacts =  new Event("OfuzExportAccount->eventExportUserContacts");
			$e_export_contacts->addParam("goto",$_SERVER['PHP_SELF']);
			echo $e_export_contacts->getLink(_('Export My Contacts'));

			if($_SESSION['in_page_message']) {
			  echo " ".$msg->getMessage($_SESSION['in_page_message']);
			}
			unset($_SESSION['in_page_message']);
			?>
		</div>

		<div class="spacerblock_20"></div>
        </div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
