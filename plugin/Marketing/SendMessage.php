<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2012 all rights reserved, SQLFusion LLC, info@sqlfusion.com

    $do_message = new EmailTemplateUser("blank");
    $do_message->sessionPersistent("do_message", "contacts.php", OFUZ_TTL);

    if(!is_object($_SESSION['do_message_draft'])) {
        $do_note_draft = new MessageDraft();
        $do_note_draft->sessionPersistent("do_message_draft","index.php",OFUZ_TTL);
    }
?>

<script type="text/javascript">
//<![CDATA[
function fnInsertMergeField(merge){
    if (merge.selectedIndex > 0) {
        var mergefield = merge[merge.selectedIndex].value;
        var textarea = dijit.byId("editor_bodyhtml");
        textarea.attr("value", textarea.attr("value")+mergefield);
        merge.selectedIndex = 0;
    }
}

// Method for draft autosave
function fnSaveDraft(){
  //var email_text = document.getElementById('hidden_field_bodyhtml').value;
  var email_text = dijit.byId("editor_bodyhtml").attr("value");
  var email_sub = $("input[name='fields[subject]']").val();
  $.ajax({
        type: "GET",
        <?php
        $e_MessageDraft = new Event("do_message_draft->eventAddUpdateDraft");
        $e_MessageDraft->setEventControler("ajax_evctl.php");
        $e_MessageDraft->setSecure(false);
        ?>
        url: "<?php echo $e_MessageDraft->getUrl(); ?>",
        data: "text="+escape(email_text)+"&type=contact_email&sub="+escape(email_sub),
        success: function(result){
        }
    });
}

// IF user wants to save the content as draft manually
function fnSaveDraftOnClick(){
  var email_text = dijit.byId("editor_bodyhtml").attr("value");
  var email_sub = $("input[name='fields[subject]']").val();
  $.ajax({
        type: "GET",
        <?php
        $e_MessageDraft = new Event("do_message_draft->eventAddUpdateDraft");
        $e_MessageDraft->setEventControler("ajax_evctl.php");
        $e_MessageDraft->setSecure(false);
        ?>
        url: "<?php echo $e_MessageDraft->getUrl(); ?>",
        data: "text="+escape(email_text)+"&type=contact_email&sub="+escape(email_sub),
        success: function(result){
            $("#draft_saved").show("fast");
            window.setInterval("relodPage()", 4000);
        }
    });
}

function relodPage(){
   window.location = '/contact_sendemail.php';
}

// Copy the saved draft to the editor
function copyDraft(){
  var textcontent =   document.getElementById('draft_hidden').value ;
  var msg_sub = document.getElementById('draft_subject_hidden').value ;
  var textarea = dijit.byId("editor_bodyhtml");
  textarea.attr("value", textcontent);
  $("input[name='fields[subject]']").val(msg_sub) ;
   $("#unsaved_draft").hide("slow");
  
}
// Discard the saved draft
function discardDraft(id){
    $.ajax({
        type: "GET",
        <?php
        $e_MessageDraft = new Event("do_message_draft->eventDeleteDraft");
        $e_MessageDraft->setEventControler("ajax_evctl.php");
        $e_MessageDraft->setSecure(false);
        ?>
        url: "<?php echo $e_MessageDraft->getUrl(); ?>",
        data: "id="+id,
        success: function(result){
             $("#unsaved_draft").hide("slow");
        }
    });
}
// Run the save draft method in every 30 seconds
window.setInterval("fnSaveDraft()", 30000);

$(document).ready(function() {;
    //$("#editor_bodyhtml_iframe").css("height","80px");
});
//]]>
</script>
<?php echo _('Select an email template'); 
        echo '<form id="select_email_template"><select name="idtemplate" onchange=\'$("#select_email_template").submit();\'><option value="0"></option><option value="0">'._('New Template').'</option>';
	    $template_list = new EmailTemplateUser();
		$template_list->query('SELECT * FROM '.$template_list->getTable().' WHERE iduser='.$_SESSION['do_User']->iduser.' AND name <> \'\'');
		while ($template_list->next()) {
			echo '<option value="'.$template_list->getPrimaryKeyValue().'">'.$template_list->name.'</option>';
		}
		
		?>
		</select>
		</form><a href=""><?php echo _('Manage your templates');?></a><br />
		<?php echo _('Insert a merge field'); ?>
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

    

    // Draft handling part
    $draft = $_SESSION['do_message_draft']->getDraft();
    if($draft && is_array($draft)){
      $text = $draft["message_content"];
      $sub = $draft["message_subject"];
      $added_time = date("l, F j, g:i A ", $draft["timestamp"]);
      echo '<div id="unsaved_draft" style="display:block;"><div class="messageshadow"><div class="messages">';
      echo '<b>'._('You have an unsaved note draft on : ').$added_time.'</b><br />';
      //echo str_replace("<br />","\r\n",$text);
      echo $text;
      echo '<br />';
      echo '<a href="#" onclick = "copyDraft();return false;">'._('Copy this').'</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick = "discardDraft('.$draft["idmessage_draft"].'); return false;">'._('Discard this').'</a>';
    ?>
      <input type="hidden" id="draft_hidden" name="draft_hidden" value="<?php echo $text; ?>">
      <input type="hidden" id="draft_subject_hidden" name="draft_subject_hidden" value="<?php echo $sub; ?>">
    <?php   echo '</div></div></div>';
    }
    ?>
    <div id="draft_saved" style="display:none">
        <div class="messageshadow">
          <div class="messages">
            <?php
                  echo _('Content is saved as a draft');
            ?>
          </div>
        </div>
    </div>  
    <?php
    // ends Here
    if ($_GET['idtemplate']) {
          $do_message->getId( (int)$_GET['idtemplate'] );
		//  $do_message->newUpdateForm(); 
    } else {
		// $do_message->newAddForm();
	}
    $do_message->newForm('do_message->eventPrepareSaving');
    //$do_message->setFields($do_message->getTable());
    $do_message->form->setLevel(920);
    $do_message->form->addEventAction('do_message_draft->eventDeleteSavedDraft', 900);//added to delete the draft
    //$do_message->form->addEventAction('do_message->eventPrepareSaving', 920);
    $do_message->form->addEventAction('do_ContactMailing->eventSendMessage', 2340);
    $do_message->form->addEventAction('mydb.gotoPage', 2430);
    $do_message->form->goto = $GLOBALS['cfg_plugin_mkt_path'].'SaveTemplate';
    $do_message->form->type = 'contact_email';//Message Draft Type
    $do_message->iduser = $_SESSION['do_User']->iduser;
    $do_message->setFields("emailtemplate_user");
    $do_message->fields->subject->size = 80;
       // $do_message->fields->subject->id = 'message_subject';
    echo $do_message->form->getFormHeader();
    echo $do_message->form->getFormEvent();
    $do_message->setApplyFieldFormating(true, "Form");
    echo '<div>'._("Subject: ").$do_message->subject.'</div><br />'; 
    echo '<div>'.$do_message->bodyhtml.'</div>';

    echo '<br />';
    $tag_search = $_SESSION['searched_tags'];
    if(is_array($tag_search) && count($tag_search) > 0 ){
        $do_tag = new Tag();
        echo '<input type ="checkbox" name = "unsubtag" id="unsubtag">&nbsp;Let the user un-subscribe the ';
        echo '<select name ="idtag" id="idtag">';
        foreach($tag_search as $tagnames){
          $do_tag->getTagByName($tagnames);
          $tagid= $do_tag->idtag ;
          echo '<option value='.$tagid.'>'.$tagnames.'</option>';
        }
        echo '</select> list';
        echo '<br />';
    }
	
        ?>
         
		  <br /><a href="#" onclick="fnSaveDraftOnClick();return false;"><?php echo _('Save As Draft')?></a><br />
        <?php
      
	$do_message->setApplyFieldFormating(false);
	
	echo '<div align="right"><input type="submit" name="send" value="'._('Send Mailing').'"></div></form>';
	
//	if ($_GET['idtemplate']) {
//		echo '<br/><div align="right">'._('Or').' '.$do_message->name.' '.$do_message->form->getFormFooter(_('Update template')).'</div>';
//	} else {
//		echo '<br/><div align="right">'._('Or').' '.$do_message->name.' '.$do_message->form->getFormFooter(_('Save as template')).'</div>';
//	}
?> 
