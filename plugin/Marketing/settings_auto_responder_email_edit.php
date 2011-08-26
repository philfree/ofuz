<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Email Templates';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
    $do_auto_responder = new AutoResponder();
    //$do_auto_responder->sessionPersistent('do_auto_responder', 'contacts.php', OFUZ_TTL);
    //$user_email_templ  = new EmailTemplateUser();
    $access = true;
    if (isset($_GET['id']) && $_GET['id'] !=''){
        $do_auto_responder_email = new AutoResponderEmail();
        $do_auto_responder_email->getId($_GET['id']);
        $do_auto_responder_email->sessionPersistent('do_auto_responder_email', 'contacts.php', OFUZ_TTL);
        $user_email_templ  = new EmailTemplateUser();
        $do_auto_responder = new AutoResponder();
        $idautoresponder = $_SESSION['do_auto_responder_email']->idautoresponder;
        if(!$do_auto_responder->isOwner($_SESSION['do_auto_responder_email']->idautoresponder)){ $access = false; }
        $do_auto_responder->getId($_SESSION['do_auto_responder_email']->idautoresponder);
    }else{
        $access = false;
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

function getEmailTemplateText(template){
    var select_id = template[template.selectedIndex].value;
    $.ajax({
        type: "GET",
<?php
$e_email_temp = new Event("do_auto_responder->eventAjaxGetEmailTemplateText");
$e_email_temp->setEventControler("ajax_evctl.php");
$e_email_temp->setSecure(false);
?>
        url: "<?php echo $e_email_temp->getUrl(); ?>",
        data: "temlid="+select_id,
        success: function(template_text){ 
           var textarea = dijit.byId("editor_bodyhtml");
           textarea.attr("value", template_text); 
        }
    });
}
function addAutoResponderEmail(){
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
          if($access === false){
              $msg = new Message(); 
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo $msg->getMessage("unauthorized_autoresponder_emailtemplate_access");
              echo '<br /><a href="/settings_auto_responder.php">Go Back</a>';
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
              //echo _('Auto Responder Email Templates -').$_SESSION['do_auto_responder_email']->name; 
			  echo _('Auto Responder '). '<a href="/settings_auto_responder_email.php?id='.$_SESSION['do_auto_responder_detail']->idautoresponder.'">'.$_SESSION['do_auto_responder_detail']->name.'</a>'. _(' - email template edit '). $_SESSION['do_auto_responder_email']->subject ; 

          ?>
        </div>
        <div class="text14 pad020">
        <?php
            //echo '>> <a href="/settings_auto_responder.php">'._(' Auto Responder').'</a>'.' >> <a href="/settings_auto_responder_email.php?id='.$idautoresponder.'">'.$do_auto_responder->name.'</a>';
        ?>
        </div>
		<?php
          $msg = new Message(); 
          if ($msg->getMessageFromContext("edit autoresponder email template")) {
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
           <div align="right">
		<select name="email_template" onchange="getEmailTemplateText(this)">
                    <?php
                        $user_email_templ->getUserSavedEmailTemplates();
                        echo '<option value="0">'._('Select Email Template').'</option>';
                        if($user_email_templ->getNumRows()){
                            while($user_email_templ->next()){
                                echo '<option value = '.$user_email_templ->idemailtemplate_user.'>'.$user_email_templ->name.'</option>';
                            }
                        }
                    ?>
		</select>
        </div>
         <?php
              
              
              $_SESSION['do_auto_responder_email']->newUpdateForm();
              $_SESSION['do_auto_responder_email']->form->addEventAction('do_auto_responder_email->eventCheckEmptyFields', 700);
              $_SESSION['do_auto_responder_email']->form->addEventAction('do_auto_responder_email->eventChangeGoto', 680);
              $_SESSION['do_auto_responder_email']->setRegistry('autoresponder_email_add');
              $_SESSION['do_auto_responder_email']->form->goto = 'settings_auto_responder_email.php?id='.$_SESSION['do_auto_responder_email']->idautoresponder;
              echo $_SESSION['do_auto_responder_email']->form->getFormHeader();
              echo $_SESSION['do_auto_responder_email']->form->getFormEvent();
              //$_SESSION['do_auto_responder_email']->idautoresponder = $_SESSION['do_auto_responder_detail']->idautoresponder;
              echo  $_SESSION['do_auto_responder_email']->idautoresponder;
              //echo '<b>'._('Name').':</b>'. $_SESSION['do_auto_responder_email']->name;
              echo '<b>'._('Subject').':</b>'. $_SESSION['do_auto_responder_email']->subject;
              echo '<br/><br/><b>'._('Send it in').' </b>'. $_SESSION['do_auto_responder_email']->num_days_to_send." <b>"._('day(s)')."</b>";
              //echo '<br/><br/><b>'._('Number Of Days to Send').':</b>'. $_SESSION['do_auto_responder_email']->num_days_to_send;
              echo '<br/><br/><b>'._('Message').':</b>'. $_SESSION['do_auto_responder_email']->bodyhtml;
              echo '<div align="right">';
              echo $do_auto_responder_email->form->getFormFooter(_('Update'));
               echo '&nbsp;&nbsp;<a href="/settings_auto_responder_email.php?id='.$idautoresponder.'"><input type="button" value="'._('Cancel').'"></a>';
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
