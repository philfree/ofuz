<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Web Forms';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
    $do_userform = new WebFormUser();
    $wb_access = true;
    $do_userform->sessionPersistent('do_userform', 'contacts.php', OFUZ_TTL);
    if (isset($_GET['edit'])) {
      $idwebformuser = $_GET['edit'] ;
      if(!$_SESSION['do_userform']->isWebFormOwner($idwebformuser)){
          $wb_access = false;
      }
    }

?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    	$("div[id^=webform]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
    });
//]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Projects'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$wb_access){
              $msg = new Message(); 
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo $msg->getMessage("unauthorized weform access");
              echo '</div></div><br /><br />';
              exit;
          }

          if($_SESSION['in_page_message'] != ''){
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo htmlentities($_SESSION['in_page_message']);
                  $_SESSION['in_page_message'] = '';
                  echo '</div></div><br /><br />';
          }
    ?> 
    <table class="layout_columns"><tr><td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
		$GLOBALS['thistabsetting'] = 'Web Forms';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
        <?php 
            $_SESSION['do_userform']->getUsersWebForms();
            if($_SESSION['do_userform']->getNumRows()){
                $count = 0;
        ?>
        <br /><br />
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('My Web Forms'); ?>:</div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
               <?php 
                    while($_SESSION['do_userform']->next()){
                        $e_remove_wf =  new Event("do_userform->eventDeleteWebForm");
                        $e_remove_wf->addParam('id',$_SESSION['do_userform']->idwebformuser);
                        $e_remove_wf->addParam("goto",$_SERVER['PHP_SELF']);
                        $count++;
                        echo '<div id="webform', $count, '" class="co_worker_item co_worker_desc">'; 
                        echo '<div style="position: relative;">';
                        echo '<a href="settings_wf.php?edit='.$_SESSION['do_userform']->idwebformuser.'">'.$_SESSION['do_userform']->title.'</a>';
                        $img_del = '<img src="/images/delete.gif" width="14px" height="14px" alt="" />';
                        echo '<div width="15px" id="trashcan', $count, '" class="deletenote" style="right:0;">'.$e_remove_wf->getLink($img_del).'</div>';
                        echo '</div></div>';
                    }
               ?>
            </div>
        </div>
        <div class="left_menu_footer"></div>
       <?php } ?> 
        
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Web Form Builder'); ?></div>
        <div class="contentfull">
        <div class="messageshadow">
            <div class="messages">
            <?php
				$msg = new Message(); 
				echo $msg->getMessage('web form creator instruction');
            ?>
            </div>
        </div><br />
        <?php
	         $_SESSION['setting_mode'] = 'Yes';
                 if (isset($_GET['edit']) && $_GET['edit'] !=''){
                    $_SESSION['do_userform']->getId($_GET['edit']);
                    $do_userform->iduser = $_SESSION['do_User']->iduser;
                    $do_userform->setRegistry("webformuser");
                    $do_userform->newUpdateForm('do_userform');
                    $do_userform->form->addEventAction('do_userform->eventDeleteWebFormFields', 2100);
                    $do_userform->form->addEventAction('do_userform->eventAddFormFields', 2103);
                    $do_userform->form->goto = 'contact_web_form_url.php';
		    echo $do_userform->form->getFormHeader();
		    echo $do_userform->form->getFormEvent();
		 
		    echo '<b>'._('Form Title').':</b>'. $do_userform->title;
		 
		    echo '<br/><b>'._('Form Description').'</b><br/>'.$do_userform->description;
                    $do_webform = new WebFormField();
		    $do_webform->getAll('display_order');
                    echo $do_webform->displayFieldsOnWebFormEdit($_GET['edit']);
		    echo '<br/><b>'._('Tags for those contacts').':</b></b> '.$do_userform->tags;
		    echo '<br/><b>'._('Web address to take the user after submitting the form').':</b> '.$do_userform->urlnext;
		    echo '<br/><b>'._('Receive an email alert when someone submit the form').':</b> '.$do_userform->email_alert;
		    echo '<div align="right">'.$do_userform->form->getFormFooter(_('Create')).'</div>';
                    ?>
                    <div><?php 
		            $_SESSION['do_userform']->setApplyRegistry(false);
		            echo _('The url for the web form '). '<b>'.$_SESSION['do_userform']->title.'</b>'._(' is ').
		                $GLOBALS['cfg_ofuz_site_http_base'].'form/'.
						$_SESSION['do_userform']->getPrimaryKeyValue(); 
						?>
		</div>
		<?php echo _('Embed code to insert in your blog or web site'); ?>
		<div>
		<textarea rows="2" cols="100"><script type="text/javascript" src="<?php echo    $GLOBALS['cfg_ofuz_site_http_base'].'js_form.php?fid='.$_SESSION['do_userform']->getPrimaryKeyValue(); ?>"></script>
                </textarea>
		</div>
                    <?php

                  }else{
                      $do_userform->iduser = $_SESSION['do_User']->iduser;
                      $do_userform->newAddForm('do_userform');
                      $do_userform->form->addEventAction('do_userform->eventAddFormFields', 2103);
                      $do_userform->form->addEventAction('do_userform->eventCheckEmptyFields', 700);
                      $do_userform->form->goto = 'contact_web_form_url.php';
                      echo $do_userform->form->getFormHeader();
                      echo $do_userform->form->getFormEvent();
                      
                      echo '<b>'._('Form Title').':</b>'. $do_userform->title;
                      
                      echo '<br/><b>'._('Form Description').'</b><br/>'.$do_userform->description;
                      $do_webform = new WebFormField();
                      $do_webform->getAll('display_order');
                      echo $do_webform->displayFields();
                      echo '<br/><b>'._('Tags for those contacts').':</b></b> '.$do_userform->tags;
                      echo '<br/><b>'._('Web address to take the user after submitting the form').':</b> '.$do_userform->urlnext;
                      echo '<br/><b>'._('Receive an email alert when someone submit the form').':</b> '.$do_userform->email_alert;
                      echo '<div align="right">'.$do_userform->form->getFormFooter(_('Create')).'</div>';
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