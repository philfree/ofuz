<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Task Discussion Email Alert';
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
		$GLOBALS['thistabsetting'] = 'Task Discussion Email Alert';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Discussion Email Alert Setup'); ?></div>
        <div class="contentfull">
        <?php 
            $UserSettings = new UserSettings();
            $data = $UserSettings->getSettingValue("task_discussion_alert");
            //if($_SESSION['do_User']->discussion_email_alert !='No'){
            if(!$data){
                $e_set_email_alert =  new Event("UserSettings->eventSetOffDiscussionAlert");
                $e_set_email_alert->addParam("goto",$_SERVER['PHP_SELF']);

                echo   '<div class="messageshadow">';
                echo     '<div class="messages">';
                $msg = new Message(); 
                echo $msg->getMessage('global_discussion_email_on');
                echo '<br />';
                echo $e_set_email_alert->getLink(_('Turn Off'));
                echo '</div></div>';
                echo '<br />';
            }else{
                if(is_array($data)){
                    $e_set_email_alert =  new Event("UserSettings->eventSetOnOffDiscussionAlert");
                    $e_set_email_alert->addParam("goto",$_SERVER['PHP_SELF']);
                    $e_set_email_alert->addParam("setting_value",$data["setting_value"]);
                    $e_set_email_alert->addParam("id",$data["iduser_settings"]);
                    //echo  '<div class="contentfull">';
                    echo   '<div class="messageshadow">';
                    echo     '<div class="messages">';
                    $msg = new Message(); 
                   
                    if($data["setting_value"] == 'No'){
                        echo $msg->getMessage('global_discussion_email_off');
                        echo '<br />';
                        echo $e_set_email_alert->getLink('Turn On');
                    }else{
                        echo $msg->getMessage('global_discussion_email_on');
                        echo '<br />';
                        echo $e_set_email_alert->getLink('Turn Off');
                    }
                    echo '</div></div>';
                    echo '<br />';
                }
            }
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