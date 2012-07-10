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
                 $GLOBALS['thistabsetting'] = 'My Profile';
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
  <?php
  $do_user_profile = new UserProfile();
  $profile_information=$do_user_profile->getProfileInformation($_SESSION['do_User']->iduser);
  if(empty($profile_information)){?>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('My Job Profile Information'); ?></div>
  <?php 
    }else{ ?>
      <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Update My Job Profile Information'); ?></div>
    <?php } ?>
        <div class="contentfull">
        
    

<?php
  
  if(empty($profile_information)){
      $profileAdd  = new UserProfile($GLOBALS['conx']);
      $profileAdd->sessionPersistent("ProfileEditSave", "index.php", 3600);
      $profileAdd->newAddForm("ProfileEditSave");
      $profileAdd->form->addParam("goto", "settings_myinfo.php");
      $profileAdd->form->setGotFile(true);
      echo $profileAdd->form->getFormHeader();
      echo $profileAdd->form->getFormEvent();
      
  }else{   
      if (is_object($_SESSION['ProfileEditSave'])) {
		$ProfileEdit = $_SESSION['ProfileEditSave'];  
		$ProfileEdit->getProfileInformation((int)$_SESSION['do_User']->iduser);	
      } else {
        $ProfileEdit  = new UserProfile($GLOBALS['conx']);
        $ProfileEdit->sessionPersistent("ProfileEditSave", "index.php", 120);
        $ProfileEdit->getProfileInformation((int)$_SESSION['do_User']->iduser);		
	  }
       
      $ProfileEdit->newUpdateForm('ProfileEditSave');   
      $ProfileEdit->setRegistry("user_profile");  
	  $ProfileEdit->form->addParam("goto", "settings_myinfo.php");
	  $ProfileEdit->form->setGotFile(true);
      echo $ProfileEdit->form->getFormHeader();
      echo $ProfileEdit->form->getFormEvent();

  }
?>
    <?php echo $_SESSION['ProfileEditSave']->iduser  ; ?>          
  <table class="tplain">
      <tr>
        <td>
          <div class="in280x20">
            <b>
            <?php echo _('Logo');?><br />
            </b>
            <?php echo $_SESSION['ProfileEditSave']->logo  ; ?>
          </div>
        </td>
        </tr>      
       <tr>
        <td>
        <br>
          <div class="in280x20">
            <b><?php echo _('Job Type');?></b><br /><br />
          <?php   echo $_SESSION['ProfileEditSave']->job_type ; ?>
          </div>
        </td>
      </tr>
   </table>
   
    </br>
    <div class="in290x18">
     <b> <?php echo _('Job Description'); ?></b><br />
      <?php  echo $_SESSION['ProfileEditSave']->job_description ; ?>
    </div>
                  
   <div class="dashedline"></div>
<?php
  echo "<br><br>";
  echo $_SESSION['ProfileEditSave']->form->getFormFooter("Save");
  $_SESSION['ProfileEditSave']->setApplyRegistry(false);

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
