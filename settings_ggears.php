<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Google Gears';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
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
		$GLOBALS['thistabsetting'] = 'Google Gears';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Google Gears Setup'); ?></div>
        <div class="contentfull">
        <?php 
            $UserSettings = new UserSettings();
			$e_set_ggear =  new Event("UserSettings->eventSetSetting");
			$e_set_ggear->addParam("goto",$_SERVER['PHP_SELF']);
			$e_set_ggear->addParam("setting_name", "google_gears");
			
			echo   '<div class="messageshadow">';
			echo     '<div class="messages">';
			$msg = new Message();  
		   
			if($UserSettings->getSetting("google_gears") == 'Yes'){
				echo $msg->getMessage('google_gears');
				echo '<br />';
				$e_set_ggear->addParam("setting_value", "No");
				echo $e_set_ggear->getLink(_('Turn Off'));
			}else{
				echo $msg->getMessage('google_gears');
				echo '<br />';
				$e_set_ggear->addParam("setting_value", "Yes");
				echo $e_set_ggear->getLink(_('Turn On'));
			}
			echo '</div></div>';
			echo '<br />';

         ?>
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
