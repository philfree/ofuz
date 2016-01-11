<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * setting tabs class
     * Using the DataObject
     */
   
class OfuzUserInterface extends BaseObject {
    
    function generateLeftMenuSettings($page_name=""){
        if($page_name == 'My Information'){
            echo '<div class="settingstabon"><a href="/settings_info.php">'._('My Information').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_info.php">'._('My Information').'</a></div>';
        }
        if($page_name == 'My Profile'){
            echo '<div class="settingstabon"><a href="/settings_myinfo.php">'._('My Profile').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_myinfo.php">'._('My Profile').'</a></div>';
        }
    	if($page_name == 'Web Forms'){
            echo '<div class="settingstabon"><a href="/settings_wf.php">'._('Web Forms').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_wf.php">'._('Web Forms').'</a></div>';
        }
    	if($page_name == 'Email Templates'){
            echo '<div class="settingstabon"><a href="/settings_email_templ.php">'._('Email Templates').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_email_templ.php">'._('Email Templates').'</a></div>';
        }
        if($page_name == 'Auto Responder'){
            echo '<div class="settingstabon"><a href="/settings_auto_responder.php">'._('Auto Responder').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_auto_responder.php">'._('Auto Responder').'</a></div>';
        }
    	if($page_name == 'Sync'){
            echo '<div class="settingstabon"><a href="/sync.php">'._('Sync').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/sync.php">'._('Sync').'</a></div>';
        }
    	if($page_name == 'Twitter Setup'){
            echo '<div class="settingstabon"><a href="/settings_twitter.php">'._('Twitter Setup').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_twitter.php">'._('Twitter Setup').'</a></div>';
        }
    	//if($page_name == 'Notes Settings'){ echo '<div class="settingstabon"><a href="/settings_notes.php">Notes Settings</a></div>'; } else { echo '<div class="settingstab"><a href="/settings_notes.php">Notes Settings</a></div>'; }
        if($page_name == 'API Key'){
            echo '<div class="settingstabon"><a href="/api_key.php">'._('API Key').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/api_key.php">'._('API Key').'</a></div>';
        }
        if($page_name == 'Email Stream'){
            echo '<div class="settingstabon"><a href="/email_stream.php">'._('Email Stream').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/email_stream.php">'._('Email Stream').'</a></div>';
        }
        //if($page_name == 'Drop Box Task'){ echo '<div class="settingstabon"><a href="/drop_box_task.php">Drop Box Task</a></div>'; } else { echo '<div class="settingstab"><a href="/drop_box_task.php">Drop Box Task</a></div>'; }
        if($page_name == 'Discussion Email Alert'){
            echo '<div class="settingstabon"><a href="/settings_discussion_alert.php">'._('Task Discussion Email Alert').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_discussion_alert.php">'._('Task Discussion Email Alert').'</a></div>';
        }
        if($page_name == 'Google Gears'){
            echo '<div class="settingstabon"><a href="/settings_ggears.php">'._('Google Gears').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_ggears.php">'._('Google Gears').'</a></div>';
        }
        if($page_name == 'Invoice'){
            echo '<div class="settingstabon"><a href="/settings_invoice.php">'._('Invoice Settings').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_invoice.php">'._('Invoice Settings').'</a></div>';
        }
        if($page_name == 'Cancel Account'){
            echo '<div class="settingstabon"><a href="/cancel_account.php">'._('Cancel Account').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/cancel_account.php">'._('Cancel Account').'</a></div>';
        }
        if($page_name == 'Export'){
            echo '<div class="settingstabon"><a href="/settings_export.php">'._('Export').'</a></div>';
        } else {
            echo '<div class="settingstab"><a href="/settings_export.php">'._('Export').'</a></div>';
        }
	// Load the plugin settings
        //$this->getPluginSettings();

    }

    /**
      * Loading the plugin settings
    */
    /*public function getPluginSettings(){
	global $cfg_setting_tab_placement;
	    if(is_array($cfg_setting_tab_placement) && count($cfg_setting_tab_placement) > 0 ){
            foreach($cfg_setting_tab_placement as  $val ){   
              if (class_exists($val)) {
                $do_tab = new $val();
                if($do_tab->isActive() === true ){
                    $do_tab->processTab();
                }
              }
                  
            }
        }
    }*/

}	
?>
