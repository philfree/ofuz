<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/    
    
    $do_user_email_teml = new EmailTemplateUser("blank");
    $emtpl_access = true;
    $do_user_email_teml->sessionPersistent('do_user_email_teml', 'contacts.php', OFUZ_TTL);
    if (isset($plugin_item_value)) {
      $idtemplate = $plugin_item_value ;
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
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Email Templates'); ?></div>
            <?php
				$msg = new Message(); 
				//echo $msg->getMessage('web form creator instruction');
            ?>
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
                 if (isset($plugin_item_value)){
                    $_SESSION['do_user_email_teml']->getId($plugin_item_value);
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

        <div class="solidline"></div>
