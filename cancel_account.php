<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: My Information';
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
		$GLOBALS['thistabsetting'] = 'Cancel Account';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Cancel Account'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Please give us the reason for Cancellation'); ?></div>
        <div class="contentfull">
        <?php 
            if($_SESSION['in_page_message'] != ''){
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo htmlentities($_SESSION['in_page_message']);
                  $_SESSION['in_page_message'] = '';
                  echo '</div></div><br /><br />';
            }else{
                $msg = new Message(); 
                $msg->getMessage("Account Cacel");
                 echo '<div class="percent95">',$msg->displayMessage(),'</div>';
            }
            //$f_user = new User();
            //$f_user->getId($_SESSION['do_User']->iduser);
            //$f_user->form();
            $CancelAccount = new OfuzCancelAccount();
            //$CancelAccount->setRegistry('ofuz_cancel_account')
            $e_cancel_account = new Event("OfuzCancelAccount->eventCancelAccount");
            $e_cancel_account->setLevel(20);
            $e_cancel_account->addEventAction("do_User->eventLogout",50);
            echo $e_cancel_account->getFormHeader();
            echo $e_cancel_account->getFormEvent();
            $CancelAccount->setFields("ofuz_cancel_account"); 
            $CancelAccount->setApplyRegistry(true, "Form");
            echo '<br />'.$CancelAccount->reason;
            echo '<br /><input type="submit" value="'._('Yes Cancel and delete all my information').'" />';

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