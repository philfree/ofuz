<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Email Templates';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
    
    
    //$user_email_templ  = new EmailTemplateUser();
    if (isset($_GET['id']) && $_GET['id'] !=''){
        $do_auto_responder = new AutoResponder();
        $do_auto_responder->getId($_GET['id']);
        $do_auto_responder->sessionPersistent('do_auto_responder', 'contacts.php', OFUZ_TTL);
        $user_email_templ  = new EmailTemplateUser();
         if(!$do_auto_responder->isOwner($_GET['id'])){
            $access = false;
        }
    }else{ $access = false; }
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    	$("div[id^=templt]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
    });


//]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php

          if($access === false){
              $msg = new Message(); 
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo $msg->getMessage("unauthorized email template access");
              echo '<br /><a href="/settings_email_templ.php">Go Back</a>';
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
                $do_ofuz_ui = new OfuzUserInterface();
                $do_ofuz_ui->generateLeftMenuSettings('Auto Responder');
             ?>
        <div class="settingsbottom"></div></div>
        
        
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text">
          <?php 
              echo _('Auto Responder - ').$_SESSION['do_auto_responder']->name; 
          ?>
        </div>

        <div class="text14 pad020">
        <?php
            //echo '>> <a href="/settings_auto_responder.php">'._('Auto Responder').'</a>';
        ?>
        </div>
		<?php
          $msg = new Message(); 
          if ($msg->getMessageFromContext("add edit autoresponder")) {
			echo $msg->displayMessage();
		  }
		?>
		<div class="spacerblock_16"></div>
        <!--<br /><br />-->
        <div class="contentfull">
        <!--<div class="messageshadow">
            <div class="messages">
            <?php
				$msg = new Message(); 
				//echo $msg->getMessage('web form creator instruction');
            ?>
            </div>
        </div><br />-->
        <div id="ptask_ctlbar_1" style = "display:block;">
         <?php
              
              $_SESSION['do_auto_responder']->newUpdateForm();
              $_SESSION['do_auto_responder']->form->addEventAction('do_auto_responder->eventCheckEmptyFields', 700);
              $_SESSION['do_auto_responder']->setRegistry('autoresponder_add');
              $_SESSION['do_auto_responder']->form->goto = 'settings_auto_responder.php';
              echo $_SESSION['do_auto_responder']->form->getFormHeader();
              echo $_SESSION['do_auto_responder']->form->getFormEvent();
              $_SESSION['do_auto_responder']->iduser = $_SESSION['do_User']->iduser;
              echo $_SESSION['do_auto_responder']->iduser;
              echo '<b>'._('Auto Responder Name').':</b>'. $_SESSION['do_auto_responder']->name;
              echo '<br/><br/><b>'._('Tag Name').':</b>'. $_SESSION['do_auto_responder']->tag_name;
              echo '<div align="right">';
              echo $_SESSION['do_auto_responder']->form->getFormFooter(_('Update'));
              echo '&nbsp;&nbsp;<a href= "/settings_auto_responder.php"><input type="button" value="Cancel"></a>';
              echo '</div>';
         ?>
        </div>
        
        </div>
        
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
