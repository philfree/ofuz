<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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
    <table class="layout_columns">
     <tr>
      <td>
        <div class="layout_lcolumn settingsbg">
          <div class="settingsbar">
       	 		<div class="spacerblock_16"></div>
            	<?php
                	$GLOBALS['thistabsetting'] = 'My Information';
                	include_once('includes/setting_tabs.php');
                	//$do_ofuz_ui = new OfuzUserInterface();
                	//$do_ofuz_ui->generateLeftMenuSettings('My Information');
             	?>
        	<div class="settingsbottom"></div>
          </div>
        </div>
      </td>
      
      <td class="layout_rcolumn">
		  
		<div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('My Information'); ?></div>
        <div class="contentfull">
        
        <?php
              if ($_GET['message']) {
                  $message = new Message();
                  if($_GET['message'] == 'reg_duplicate_email'){
                        $message->getMessage("reg_duplicate_email");
                  }else{
                        $message->setContent($_GET['message']);
                  }
                  $message->displayMessage();
                
              }
              $f_user = new User();
              $f_user->getId($_SESSION['do_User']->iduser);

              $f_regForm = $f_user->prepareSavedForm("OfuzUserInfoUpdateForm");
              $f_regForm->setFormEvent("do_User->eventUpdateUserInfo") ;
              $f_regForm->addEventAction("do_User->eventValidationOnUpdate", 10) ;
              $f_regForm->addParam("errPage",'settings_info.php');
              $f_regForm->setForm();
              $f_regForm->execute();
              
              

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
