<?php  
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
 /**
   *
   * @package OfuzCore
   * @author Philippe Lewicki <phil@sqlfusion.com>
   * @license GNU Affero General Public License
   * @version 0.6
   * @date 2010-09-04  
   */

    include_once('config.php');
  
        
    if(!isset($_GET['plugin']) && !isset($_GET['content'])){
	echo _('Parameter missing !!!');exit;
    }


    $do_plugin_enable = new PluginEnable();
        if($GLOBALS['cfg_tab_placement']->count() > 0 ){
               foreach($GLOBALS['cfg_tab_placement'] as  $tab ){   
                  if (is_object($tab)) {
                    $idplugin_enabled = $do_plugin_enable->isEnabled($_GET['plugin']);
                    if($idplugin_enabled==0){
                      echo _('Plugin Disabled !!!');exit;        
                    }  
                  }
                }
        }



    $plugin_name = $_GET['plugin'];
    $plugin_page_name = $_GET['content'];
    if (isset($_GET['item_value'])) { 
       $plugin_item_value = $_GET['item_value'];
    }
    
    $GLOBALS['cfg_tab_placement']->rewind();
    foreach($GLOBALS['cfg_tab_placement'] as  $tab_plugin ){  
        if (is_object($tab_plugin )) {  
          if ($tab_plugin->getPlugInName() == $plugin_name) { $plugin = $tab_plugin; continue; }
        }
    }
    if (!is_object($plugin) || !$plugin->setCurrentPage($plugin_page_name)) {
      $GLOBALS['cfg_plugin_page']->rewind();
      foreach($GLOBALS['cfg_plugin_page'] as  $page_plugin ){  
          if (is_object($page_plugin )) {  
            if ($page_plugin->getPlugInName() == $plugin_name) { $plugin = $page_plugin; continue; }
          }
      }      
    }
    if (!is_object($plugin)) { echo _('-Plug-in content page not defined, exiting now'); exit; }
    if (!$plugin->setCurrentPage($plugin_page_name)) {
      echo _('-Plug-in curent page not defined, exiting now');
      exit;
    }
    $pageTitle = $plugin->getPlugInName().' :: Ofuz';
    $Author = 'SQLFusion LLC';
    $Keywords = '';
    $Description = '';
    $background_color = 'white'; 
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

?>
<!-- loading the Ofuz JS -->
<script type="text/javascript">
    //<![CDATA[
	<?php include_once('includes/ofuz_js.inc.php'); ?>
    //]]>
</script>

<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns">
<tr>
  <td class="layout_lmargin"></td>
  <td>
      <div class="layout_content">
	  <?php 
		  // This is where the plugin tab is loaded as well as the default tabs
		  include_once('includes/ofuz_navtabs.php'); 
	  ?>

	  <?php 
	      // Load the Breadcrumb
	      $do_breadcrumb = new Breadcrumb(); 
	      $do_breadcrumb->getBreadcrumbs(); 
	  ?>
	<div class="grayline1"></div>
	<div class="spacerblock_20"></div>
        
	<table class="layout_columns">
	<tr>
	    <td class="layout_lcolumn">
	    <?php
	    // Load the plugin block 
	    $GLOBALS['page_name'] = $plugin->getCurrentPage();
	    include_once('plugin_block.php');
	    
	    ?>

       </td>
       <td class="layout_rcolumn">
         <!-- <div class="min660"> -->
         <?php if (strlen($plugin->getMessageKey()) > 0 ) { ?>
         <div class="pad20">
         <?php
            $msg = new Message();   
	    if ($msg->getMessage($plugin->getMessageKey())) {
	        $msg->displayMessage() ;
	    }  	
         ?>
         </div>
        <?php } ?>
        <?php if (strlen($plugin->getTitle()) > 0) { ?>
        <div class="mainheader">
            <div class="pad20">
                <span class="headline14"><?php echo $plugin->getTitle(); ?></span>
                <?php
                if (is_object($GLOBALS['cfg_submenu_placement'][$plugin->getCurrentPage()]) ) {
                	echo  $GLOBALS['cfg_submenu_placement'][$plugin->getCurrentPage()]->getMenu();
                }
                ?>
            </div>
        </div>
        <?php } ?>
          <div class="spacerblock_20"></div>        
         <div class="contentfull">
           <!-- Place holder for the plugin tab page -->
	    <?php

		//$plugin_file = 'plugin/'.$plugin.'/'.$plugin_page_name ;
		if(file_exists($plugin->getCurrentPageFilePath())){
		    include_once($plugin->getCurrentPageFilePath());
		}else{
		    echo _('Plugin Content Not Found !!');
		}
	     ?>        
	   <div class="spacerblock_20"></div>
           <div class="dottedline"></div>
         </div>
    </td></tr></table>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>