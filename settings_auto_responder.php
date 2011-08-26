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
    $do_auto_responder = new AutoResponder();
    $do_auto_responder->sessionPersistent('do_auto_responder', 'contacts.php', OFUZ_TTL);
    //$user_email_templ  = new EmailTemplateUser();

?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    	$("div[id^=autores]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
    });


function addAutoResponder(){
     $("#ptask_ctlbar_1").slideToggle("slow");
}
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
		$GLOBALS['thistabsetting'] = 'Auto Responder';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
        
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text">
          <?php 
              echo _('Auto Responder'); 
              echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
              echo '<a href = "#" onclick ="addAutoResponder();return false;">'._('Create New').'</a>';
          ?>
        </div>
        
            <?php
				$msg = new Message(); 
				//echo $msg->getMessageFromContext('autoresponder instruction');
                                if ($msg->getMessageFromContext("autoresponder instruction")) {
				    echo $msg->displayMessage();
			         }
            ?>
           
        <div id="ptask_ctlbar_1" style = "display:none;">
         <?php
              
              $_SESSION['do_auto_responder']->newAddForm();
              $_SESSION['do_auto_responder']->form->addEventAction('do_auto_responder->eventCheckEmptyFields', 700);
              $_SESSION['do_auto_responder']->setRegistry('autoresponder_add');
              $_SESSION['do_auto_responder']->form->goto = $_PHP['SELF'];
              echo $_SESSION['do_auto_responder']->form->getFormHeader();
              echo $_SESSION['do_auto_responder']->form->getFormEvent();
              $_SESSION['do_auto_responder']->iduser = $_SESSION['do_User']->iduser;
              echo $_SESSION['do_auto_responder']->iduser;
              echo '<b>'._('Auto Responder Name').':</b>'. $_SESSION['do_auto_responder']->name;
              echo '<br/><br/><b>'._('Tag Name').':</b>'. $_SESSION['do_auto_responder']->tag_name;
              echo '<div align="right">'.$_SESSION['do_auto_responder']->form->getFormFooter(_('Create')).'</div>';
         ?>
        </div>
        <?php
                echo '<div class="solidline"></div>';
                $_SESSION['do_auto_responder']->setApplyRegistry(false,"Form");
                $_SESSION['do_auto_responder']->getUserSavedAutoResponders();
                if($_SESSION['do_auto_responder']->getNumRows()){
                      $del_auto_responder = new Event('do_auto_responder->eventDelAutoResponder');
                      $del_auto_responder->addParam('goto', 'settings_auto_responder.php');
                      $item_count = 0;
                      while($do_auto_responder->next()){
                             $item_count++;
                             echo '<div class="contacts"  id="autores'.$item_count.'">';
                             echo '<div class="contacts_desc">';
                             echo '<span class="contacts_name" >';
                             echo '<a href="/settings_auto_responder_email.php?id='.$_SESSION['do_auto_responder']->idautoresponder.'">'.$_SESSION['do_auto_responder']->name.'</a>';
							 echo " (".$_SESSION['do_auto_responder']->tag_name.")";
                             echo '</span>';
                             echo '</div>';
                             $del_auto_responder->addParam('id', $_SESSION['do_auto_responder']->idautoresponder);
                             $del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';

                             echo '<div id="trashcan'.$item_count.'" align="right" style="margin-left:400px;display:none;position:absolute;">'.'<a href="/settings_auto_responder_edit.php?id='.$_SESSION['do_auto_responder']->idautoresponder.'">edit &nbsp;&nbsp;|&nbsp;&nbsp;'.$del_auto_responder->getLink($del_img_url, ' title="'._('Delete this Auto Responder').'" onclick="if (!confirm(\''._('Do you really want to delete?').'\')) return false;"').'</div>';

                             //echo '<span align="right" style="margin-left:100px;"><a href="/settings_auto_responder_edit.php?id='.$_SESSION['do_auto_responder']->idautoresponder.'">edit &nbsp;&nbsp;|&nbsp;&nbsp;'.$del_auto_responder->getLink($del_img_url, ' title="'._('Delete this Auto Responder').'"').'</span>';
                             
                             echo '</div>';
                             echo '<div class="spacerblock_2"></div>';
                             echo '<div class="solidline"></div>';
                      }   
                }else{
                    echo '<div style="margin-left:0px;">';
                    echo '<div class="messages_unauthorized">';
                    echo '<b>'._('You do not have any auto responder. To add click ').'<a href = "#" onclick ="addAutoResponder();return false;">'._('here').'</a></b>';
                    echo '</div></div>';
                }
	         
	 ?>
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
