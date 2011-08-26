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
    //$do_auto_responder = new AutoResponder();
    
    if (isset($_GET['id']) && $_GET['id'] !=''){
        $do_auto_responder_detail = new AutoResponder();
        $do_auto_responder_detail->getId($_GET['id']);
        $do_auto_responder_detail->sessionPersistent('do_auto_responder_detail', 'contacts.php', OFUZ_TTL);
        $user_email_templ  = new EmailTemplateUser();
        if(!$_SESSION['do_auto_responder_detail']->isOwner($_GET['id'])){
            $access = false;
        }
    }else{ $access = false; }

?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    	$("div[id^=autores_email]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
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
$e_email_temp = new Event("do_auto_responder_detail->eventAjaxGetEmailTemplateText");
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
              echo $msg->getMessage("unauthorized_autoresponder_access");
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
			  $email_temp = $_SESSION['do_auto_responder_detail']->getChildAutoResponderEmail();
			  if($email_temp->getNumRows() < 1) {
				echo _('Auto Responder '). $_SESSION['do_auto_responder_detail']->name. _(' - email template add form') ; 
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				echo '<a href = "#" onclick ="addAutoResponderEmail();return false;">'._('Add a new email').'</a>';
			  } else {
				//echo _('Auto Responder Email Templates for - '). $_SESSION['do_auto_responder_detail']->name; 
				echo _('Auto Responder '). $_SESSION['do_auto_responder_detail']->name. _(' - Email templates') ; 
				echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				echo '<a href = "#" onclick ="addAutoResponderEmail();return false;">'._('Add a new email').'</a>';
			  }
          ?>
        </div>
        
        <div class="text14 pad020">
        <?php
            //echo '>> <a href="/settings_auto_responder.php">'._('Auto Responder').'</a>';
        ?>
        </div>
       
          <?php
                              $msg = new Message(); 
                              if ($msg->getMessageFromContext("autoresponder email instruction")) {
				    echo $msg->displayMessage();
			         }
          ?>
           
        <div id="ptask_ctlbar_1" style = "display:none;">
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
              $do_auto_responder_email = new AutoResponderEmail();
              
              $do_auto_responder_email->newAddForm();
              $do_auto_responder_email->form->addEventAction('do_auto_responder_email->eventCheckEmptyFields', 700);
              $do_auto_responder_email->setRegistry('autoresponder_email_add');
              $do_auto_responder_email->form->goto = $_PHP['SELF'];
              echo $do_auto_responder_email->form->getFormHeader();
              echo $do_auto_responder_email->form->getFormEvent();
              $do_auto_responder_email->idautoresponder = $_SESSION['do_auto_responder_detail']->idautoresponder;
              echo  $do_auto_responder_email->idautoresponder;
              //echo '<b>'._('Name').':</b>'. $do_auto_responder_email->name;
              echo '<b>'._('Subject').':</b>'. $do_auto_responder_email->subject;
              echo '<br/><br/><b>'._('Send it in').' </b>'. $do_auto_responder_email->num_days_to_send." <b>"._('day(s)')."</b>";
              echo '<br/><br/><b>'._('Message').':</b>'. $do_auto_responder_email->bodyhtml;
              echo '<div align="right">'.$do_auto_responder_email->form->getFormFooter(_('Create')).'</div>';
         ?>
        </div>
        <?php
                echo '<div class="solidline"></div>';
                $Emails = $_SESSION['do_auto_responder_detail']->getChildAutoResponderEmail();
                
                if($Emails->getNumRows()){
                      $item_count = 0;
                      $del_auto_responder_email = new Event('AutoResponderEmail->eventDelAutoResponderEmail');
                      $del_auto_responder_email->addParam('goto', 'settings_auto_responder_email.php');
                      while($Emails->next()){
                             $item_count++;
                             echo '<div class="contacts" id="autores_email">';
                             echo '<div class="contacts_desc">';
                             echo '<span class="contacts_name">';
                             echo '<a href="/settings_auto_responder_email_edit.php?id='.$Emails->idautoresponder_email .'">'.$Emails->subject.'</a>';
                             echo '</span>';
                             echo '</div>';
                             
                             $del_auto_responder_email->addParam('id', $Emails->idautoresponder_email);
                             $del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';

                              echo '<div id="trashcan', $item_count, '" align="right" style="margin-left:400px;display:none;position:absolute;">';

							  //echo $del_auto_responder_email->getLink($del_img_url, ' title="'._('Delete this Auto Responder Email').'"  onclick="if (!confirm(\'Do you really want to delete?\')) return false;"').'</span>';
							  echo $del_auto_responder_email->getLink($del_img_url, ' title="'._('Delete this Auto Responder Email').'"  onclick="return confirm(\''.('Do you really want to delete?').'\')"').'</span>';

                             echo '</div>';
                             echo '<div class="spacerblock_2"></div>';
                             echo '<div class="solidline"></div>';
                      }   
                }else{
                    echo '<div style="margin-left:0px;">';
                    echo '<div class="messages_unauthorized">';
                    echo '<b>'._('You do not have any auto responder Email Templates for '.$_SESSION['do_auto_responder_detail']->name.'. To add click ').'<a href = "#" onclick ="addAutoResponderEmail();return false;">'._('here').'</a></b>';
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
