<?php
	
	/**
	 * delete inactive users.php
	 * Get the inactive user take their information backup to an xml file and delete the user data from the database.
	 * It uses the object: Task
	 * Copyright 2001 - 2010 All rights reserved SQLFusion LLC, info@sqlfusion.com 
	 */
	
    $pageTitle = 'Ofuz :: Delete Inactive Users';
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
		$GLOBALS['thistabsetting'] = 'Delete Inactive Users';
		include_once('includes/setting_tabs.php');
        ?>
        <div class="settingsbottom"></div></div>
        </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Delete Inactive Users'); ?></div>
        <div class="contentfull">
        <?php
		$du = new user();
		$y=$du->eventDeleteInactiveUsers();
		echo $y;
		?>
		</div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php //include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
