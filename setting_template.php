<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Twitter Setup';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
    if(!isset($_GET['plugin']) && !isset($_GET['content'])){
	echo _('Parameter missing !!!');exit;
    }

    $plugin_name = $_GET['plugin'];
    if (!class_exists($plugin_name)) {
          echo _('Plug-in class not found exiting');
          exit;
    }
    $plugin_setting_name = $_GET['setting'];
    if (isset($_GET['item_value'])) { 
      $plugin_item_value = $_GET['item_value'];
    }
    
    $plugin = new $plugin_name();
    
    if (!$plugin->setCurrentPage($plugin_setting_name)) {
      echo _('Plug-in setting content not defined, exiting now');
      exit;
    }
?>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>

    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr>
    <td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
		$GLOBALS['thistabsetting'] = $plugin_name;
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
 
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
		
        <div class="banner50 pad020 text16 fuscia_text"><?php echo $plugin->getTitle(); ?></div>
        <div class="contentfull">
        <?php
           // Load the setting plugin
// 		$plugin_file_setting = 'plugin/'.$plugin.'/'.$plugin_setting_name ;
// 		if(file_exists($plugin_file_setting)){
// 		    require($plugin_file_setting);
// 		}else{
// 		    echo _('Plugin Not Found !!');
// 		}
		if(file_exists($plugin->getCurrentPageFilePath())){
            include_once($plugin->getCurrentPageFilePath());
        }else{
            echo _('Plugin Setting Content Not Found !!');
        }
         ?>
        </div>
        <div class="spacerblock_20"></div>
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
