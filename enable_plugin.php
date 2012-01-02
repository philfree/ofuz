<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Enable Add-On';
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
                $GLOBALS['thistabsetting'] = 'Plugins';
                include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Add-On'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Add-On Settings'); ?></div>
        <div class="contentfull">
        <?php
                if($_SESSION['in_page_message'] != ''){
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo htmlentities($_SESSION['in_page_message']);
                  $_SESSION['in_page_message'] = '';
                  echo '</div></div><br /><br />';
              }
          ?>


        <?php 

      /*
        echo "<pre>";
        print_r($plugins_info);
        //print_r($plugins_info['Marketing']);
        echo "</pre>";
         print_r($plugins_info['Marketing']['tabs']); 
        foreach($plugins_info as $plugin){
             
        }*/
        
          echo '<b>'._('Add-On').':</b><br/><br/>';
          $do_plugin_enable = new PluginEnable();
          $do_dynamic_button = new DynamicButton();


          echo '<table width="100%">';
          $plugin_name=array_keys($plugins_info);    
          foreach($plugin_name as $pl_name){
          echo '<tr height="30px;">';
          echo '<td colspan=1 width="30%">';                            
          echo $pl_name;
          echo '</td>';
          echo '<td colspan=2 valign="left" width="50%">';
          
          /*
          print_r($plugins_info[$pl_name]);*/ 
          //echo $plugins_info[$pl_name]['tabs'];
          echo '</td>';
          echo '<td width=12%>'; 
          
      
  
          $idplugin_enable = $do_plugin_enable->isAddOnEnabled($plugins_info[$pl_name]);
      
          
    
          if($idplugin_enable ===false){
            $button = $do_dynamic_button->CreateButton('',_('Enable'));
            $e_enable = new Event('PluginEnable->eventEnableAddOn');
            $e_enable->addParam('goto', $_SERVER['PHP_SELF']);
            $e_enable->addParam('tabs', $plugins_info[$pl_name]['tabs']);
            $e_enable->addParam('settings', $plugins_info[$pl_name]['settings']);          
            $e_enable->addParam('blocks', $plugins_info[$pl_name]['blocks']);
            echo $e_enable->getLink(_('Enable'));
          }else{
            $button = $do_dynamic_button->CreateButton('',_('Disable'));
            $e_disable = new Event('PluginEnable->eventDisableAddOn');
            $e_disable->addParam('goto', $_SERVER['PHP_SELF']);
            $e_disable->addParam('idplugin_enable', $idplugin_enable);            
            echo $e_disable->getLink(_('Disable'));
          }
          echo '</td>';
          echo '</tr>';
          echo '<tr>';
          echo '<td colspan=4>';
          echo '<div class="dashedline"></div>'; 
          echo '</td>';
          echo '</tr>';
        }
        echo '</table>';               
            
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
