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
    $do_user_email_teml = new EmailTemplateUser("blank");
    $emtpl_access = true;
    $do_user_email_teml->sessionPersistent('do_user_email_teml', 'contacts.php', OFUZ_TTL);
    if (isset($_GET['edit'])) {
      $idtemplate = $_GET['edit'] ;
      if(!$_SESSION['do_user_email_teml']->isTemplateOwner($idtemplate)){
          $emtpl_access = false;
      }
    }

?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    	$("div[id^=templt]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
    });

function fnInsertMergeField(merge){
    if (merge.selectedIndex > 0) {
        var mergefield = merge[merge.selectedIndex].value;
        var textarea = dijit.byId("editor_bodyhtml");
        textarea.attr("value", textarea.attr("value")+mergefield);
        merge.selectedIndex = 0;
    }
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
          if(!$emtpl_access){
              $msg = new Message(); 
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo $msg->getMessage("unauthorized email template access");
              echo '<br /><a href="/settings_email_templ.php">'._('Go Back').'</a>';
              echo '</div></div><br /><br />';
              exit;
          }
    ?> 
    <table class="layout_columns"><tr><td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
		$GLOBALS['thistabsetting'] = 'Email Templates';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
        <?php 
            $_SESSION['do_user_email_teml']->getUserSavedEmailTemplates();
            if($_SESSION['do_user_email_teml']->getNumRows()){
                $count = 0;
        ?>
        <br /><br />
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('My Email Templates'); ?>:</div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
               <?php 
                    while($_SESSION['do_user_email_teml']->next()){
                        $e_remove_etml =  new Event("do_user_email_teml->eventDeleteUserEmailTmpl");
                        $e_remove_etml->addParam('id',$_SESSION['do_user_email_teml']->idemailtemplate_user);
                        $e_remove_etml->addParam("goto",$_SERVER['PHP_SELF']);
                        $count++;
                        echo '<div id="templt', $count, '" class="co_worker_item co_worker_desc">'; 
                        echo '<div style="position: relative;">';
                        echo '<a href="/settings_email_templ.php?edit='.$_SESSION['do_user_email_teml']->idemailtemplate_user.'">'.$_SESSION['do_user_email_teml']->name.'</a>';
                        $img_del = '<img src="/images/delete.gif" width="14px" height="14px" alt="Delete" />';
                        echo '<div width="15px" id="trashcan', $count, '" class="deletenote" style="right:0;">'.$e_remove_etml->getLink($img_del, ' title="'._('Delete').'"').'</div>';
                        echo '</div></div>';
                    }
               ?>
            </div>
        </div>
        <div class="left_menu_footer"></div><br /><br />
       <?php } ?> 
        
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Email Templates'); ?></div>
        <div class="contentfull">
        <div class="messageshadow">
            <div class="messages">
            <?php
				$msg = new Message(); 
				//echo $msg->getMessage('web form creator instruction');
            ?>
            </div>
        </div><br />
        <div align="right">
            <form id="select_merge_field">
		<select name="merge_fields" onchange="fnInsertMergeField(this)">
		    <option value="0"><?php echo _('merge field');?></option>
		    <option value="[firstname]"><?php echo _('First Name');?></option>
		    <option value="[lastname]"><?php echo _('Last Name');?></option>
		    <option value="[position]"><?php echo _('Position');?></option>
		    <option value="[company]"><?php echo _('Company');?></option>
		</select>
		</form>		
        </div>
        <?php
	         //$_SESSION['setting_mode'] = 'Yes';
                 if (isset($_GET['edit']) && $_GET['edit'] !=''){
                    $_SESSION['do_user_email_teml']->getId($_GET['edit']);
                    $do_user_email_teml->iduser = $_SESSION['do_User']->iduser;
                    $do_user_email_teml->setRegistry("emailtemplate_user");
                    $do_user_email_teml->newUpdateForm();
                    $do_user_email_teml->form->goto = $_PHP['SELF'];
                    echo $do_user_email_teml->form->getFormHeader();
                    echo $do_user_email_teml->form->getFormEvent();
                    echo '<b>'._('Template Name').':</b>'. $do_user_email_teml->name;
                    echo '<br/><b>'._('Subject').':</b>'. $do_user_email_teml->subject;
                    echo '<br/><b>'._('Message').'</b><br/>'.$do_user_email_teml->bodyhtml;
                    echo '<div align="right">'.$do_user_email_teml->form->getFormFooter(_('Update')).'</div>';
                  }else{
                      $do_user_email_teml->iduser = $_SESSION['do_User']->iduser;
                      $do_user_email_teml->newAddForm();
                      $do_user_email_teml->form->goto = $_PHP['SELF'];
                      echo $do_user_email_teml->form->getFormHeader();
                      echo $do_user_email_teml->form->getFormEvent();
                      echo '<b>'._('Template Name').':</b>'. $do_user_email_teml->name;
                      echo '<br/><b>'._('Subject').':</b>'. $do_user_email_teml->subject;
                      echo '<br/><b>'._('Message').'</b><br/>'.$do_user_email_teml->bodyhtml;
                      echo '<div align="right">'.$do_user_email_teml->form->getFormFooter(_('Create')).'</div>';
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
