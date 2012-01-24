<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
/** New help page **/

    $pageTitle = 'Ofuz :: Help Support';
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
<?php $thistab = _('Help'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns">
     <tr>
      <td>
        <?php    include_once('plugin_block.php'); ?>
      </td>
      
      <td class="layout_rcolumn">
         <div class="mainheader">
            <div class="pad20">
                <span class="page_title"><?php echo _('Help &amp; Support'); ?></span>
                <?php
                // Menues are defined in includes/x_ofuz_hooks_plugin.conf.inc.php
                if (is_object($GLOBALS['cfg_submenu_placement']['help']) ) {
                	echo  $GLOBALS['cfg_submenu_placement']['help']->getMenu();
                }
                ?>
            </div>
        </div>
        <div class="solidline"></div>
        <div class="spacerblock_40"></div>
        
        <div class="contentfull">
        
        Video tutorials <br>
        
        Usefull blog posts <br>
        
        Forum (mainly for developers).
       
       
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
