<?php
// Copyrights 2008 - 2012 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


    /**
	 * task.php
	 * Display the task discussion of a project 
	 * It uses the object: Task, Project, ProjectTask, NoteDraft, WorkFeedProjectDiscuss, Message, Feedback, Breadcrumb
	 * Copyright 2001 - 2012 All rights reserved SQLFusion LLC, info@sqlfusion.com 
	 */
	
    //$pageTitle = 'Ofuz :: Project Task';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    //include_once('includes/header.inc.php');

	if(is_object($_SESSION['ProjectDiscussCount'])) {
		$_SESSION['ProjectDiscussCount']->sql_qry_start = 0;
	}

    if (isset($_GET['idprojecttask'])) {
        $idproject_task = (int)$_GET['idprojecttask'];
        $do_project_task = new ProjectTask();
        $do_project_task->getProjectTaskDetails( $idproject_task );
        $do_project = $do_project_task->getParentProject();
        if ($_SESSION['do_project']->idproject != $do_project_task->idproject) {
            $do_project->sessionPersistent("do_project", "project.php", OFUZ_TTL);
        }
    } elseif (is_object($_SESSION["eDetail_ProjectTask"])) {
        $idproject_task = $_SESSION["eDetail_ProjectTask"]->getparam("idproject_task");
        $do_project_task = new ProjectTask();
        $do_project_task->getProjectTaskDetails( $idproject_task );
        $do_project = $do_project_task->getParentProject();
        if ($_SESSION['do_project']->idproject != $do_project_task->idproject) {
            $do_project->sessionPersistent("do_project", "project.php", OFUZ_TTL);
        }
    } elseif(is_object($_SESSION['do_project_task'])) {
        $idproject_task = $_SESSION['do_project_task']->idproject_task;
        $do_project_task = new ProjectTask();
        $do_project_task->getProjectTaskDetails( $idproject_task );
    }

    $do_task_name = new ProjectTask();
    $task_name = $do_task_name->getTaskName($idproject_task);
    $pageTitle = 'Ofuz :: '.$task_name;

    include_once('includes/header.inc.php');

    $_SESSION['do_project']->setBreadcrumb();
	
    //check if the user can access the task or not
    $task_access = false;
    $task_operation_access = true;
    if(!empty($idproject_task)){
        if($do_project_task->isProjectTaskReletedToUser($idproject_task)){$task_access = true;}
    }
    /*if($task_access === false){
        if($do_project_task->isPublicAccess($idproject_task)){
            $task_access = true;
            $task_operation_access = false;
        }
    }*/
    //if($task_access){
        $do_project_task->sessionPersistent('do_project_task', 'task.php', OFUZ_TTL);
        $WorkFeedProjectDiscuss = new WorkFeedProjectDiscuss();
        $WorkFeedProjectTask = new WorkFeedProjectTask();

		$do_count_discussion = $_SESSION['do_project_task']->getChildProjectDiscuss("ORDER BY priority DESC, date_added DESC,idproject_discuss DESC");
		$count_discussion = $do_count_discussion->getNumRows();

//         $do_discuss = $_SESSION['do_project_task']->getChildProjectDiscuss("ORDER BY priority DESC, date_added DESC,idproject_discuss DESC  limit 0,50");
        $ProjectDiscuss = new ProjectDiscuss();
        $ProjectDiscuss->sessionPersistent('ProjectDiscussEditSave', 'project.php', OFUZ_TTL);
        //$_SESSION['ProjectDiscussEditSave']->idproject_task = $_SESSION['do_project_task']->idproject_task;
         $_SESSION['ProjectDiscussEditSave']->idproject_task = $idproject_task;

        $do_discuss = $_SESSION['do_project_task']->getChildProjectDiscuss("ORDER BY priority DESC, date_added DESC,idproject_discuss DESC  limit {$_SESSION['ProjectDiscussEditSave']->sql_qry_start},{$_SESSION['ProjectDiscussEditSave']->sql_view_limit}");
        $return_page = 'Task/'.$idproject_task;
		//$ProjectDiscuss->prj_discussion_count = $count_discussion;

		if(!is_object($_SESSION['ProjectDiscussCount'])) {
			$ProjectDiscuss->sessionPersistent('ProjectDiscussCount', 'project.php', OFUZ_TTL);
			$_SESSION['ProjectDiscussCount']->prj_discussion_count = $count_discussion;
			//$_SESSION['ProjectDiscussCount']->sql_qry_start = 0;
		}
    //}

    if(!is_object($_SESSION['do_note_draft'])) {
        include_once('class/NoteDraft.class.php');
        $do_note_draft = new NoteDraft();
        $do_note_draft->sessionPersistent("do_note_draft","project.php",OFUZ_TTL);
    }
	
	$DiscussNoteExpend  = new ProjectDiscuss($GLOBALS['conx']);
    $DiscussNoteExpend->sessionPersistent("DiscussNoteExpend", "contacts.php", OFUZ_TTL);

?>
<script type="text/javascript">
//<![CDATA[
<?php include_once('includes/ofuz_js.inc.php'); ?>
function fnEditTask(){
    $("#ptask_ctlbar").slideToggle("fast");
}
function showOpt(){
    $("#more_options").hide(0);
    $("#discuss_options").show("fast");
}

$(document).ready(function(){
  $("#discuss").click(function(){
    $("#more_options").hide(0);
    $("#discuss_options").show("fast");
  });

});





function showFullNote(idproject_discuss){
    $.ajax({
        type: "GET",
<?php
$e_ProjectDiscuss = new Event("DiscussNoteExpend->eventAjaxProjectTaskDiscussion");
$e_ProjectDiscuss->setEventControler("ajax_evctl.php");
$e_ProjectDiscuss->setSecure(false);
?>
        url: "<?php echo $e_ProjectDiscuss->getUrl(); ?>",
        data: "idnote="+idproject_discuss,
        success: function(notetext){
            $("#notepreview"+idproject_discuss)[0].innerHTML = notetext;
        }
    });
}
function showCoWorkers(){
    $("#task_co_worker").show("fast");
}
function hideCoWorkers(){
    $("#task_co_worker").hide("fast");
}
var ProgressValue = false;
function fnSetProgress(progress){
    if (ProgressValue===false){window.setTimeout(fnUpdateProgress,1000);}
    ProgressValue=progress;
}
function fnUpdateProgress(){
    $.ajax({
        type: "GET",
<?php
$e_UpdateProgress = new Event("ProjectTask->eventAjaxUpdateProgress");
$e_UpdateProgress->setEventControler("ajax_evctl.php");
$e_UpdateProgress->setSecure(false);
?>
        url: "<?php echo $e_UpdateProgress->getUrl(); ?>",
        data: "idproject_task=<?php echo $idproject_task; ?>&progress="+ProgressValue,
        success: function(result){
            ProgressValue=false;
        }
    });
}
$(document).ready(function() {
    $("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
    //fnSetProgress(arguments[0]);
});

function fnSaveDraft(){
  var discuss_text = document.getElementById('discuss').value;

  $.ajax({
        type: "GET",
        <?php
        $e_NoteDraft = new Event("do_note_draft->eventAddUpdateDraft");
        $e_NoteDraft->setEventControler("ajax_evctl.php");
        $e_NoteDraft->setSecure(false);
        ?>
        url: "<?php echo $e_NoteDraft->getUrl(); ?>",
        data: "text="+escape(discuss_text)+"&id_type=project_discuss&id=<?php echo $idproject_task; ?>",
        success: function(result){
        }
    });
}
function fnSaveDraftOnClick(){
  var discuss_text = document.getElementById('discuss').value;
  $.ajax({
        type: "GET",
        <?php
        $e_NoteDraft = new Event("do_note_draft->eventAddUpdateDraft");
        $e_NoteDraft->setEventControler("ajax_evctl.php");
        $e_NoteDraft->setSecure(false);
        ?>
        url: "<?php echo $e_NoteDraft->getUrl(); ?>",
        data: "text="+escape(discuss_text)+"&id_type=project_discuss&id=<?php echo $idproject_task; ?>",
        success: function(result){
            $("#draft_saved").show("fast");
            window.setInterval("reloadPage()", 4000);
        }
    });
}
function reloadPage(){
   window.location = '/task.php';
}
function copyDraft(){
  var textcontent = document.getElementById('draft_hidden').value ;
  document.getElementById('discuss').value =  textcontent; 
   $("#unsaved_draft").hide("slow");
  
}
function discardDraft(id){
    $.ajax({
        type: "GET",
        <?php
        $e_NoteDraft = new Event("do_note_draft->eventDeleteDraft");
        $e_NoteDraft->setEventControler("ajax_evctl.php");
        $e_NoteDraft->setSecure(false);
        ?>
        url: "<?php echo $e_NoteDraft->getUrl(); ?>",
        data: "id="+id,
        success: function(result){
             $("#unsaved_draft").hide("slow");
        }
    });
}

// Function to generate the Ajax edit form with expandable text area
var openform;
var hiddendiv;
function fnEditNote(div_id,idnote){
    if ($("#e"+openform).length > 0) fnCancelEdit(hiddendiv,openform);
        openform = idnote;
        hiddendiv = div_id
    $.ajax({
        type: "GET",
<?php
$e_edit_form = new Event("ProjectDiscussEditSave->eventAjaxGetEditForm");
$e_edit_form->setEventControler("ajax_evctl.php");
$e_edit_form->setSecure(false);
?>
        url: "<?php echo $e_edit_form->getUrl(); ?>",
        data: "id="+idnote+"&curdiv="+div_id,
        success: function(edit_form){
            $("#"+div_id).hide(0);
            $("#e"+idnote)[0].innerHTML = edit_form;
            $("#e"+idnote).show(0);
            $("#note_edit").expandable();
            $("#note_edit").focus();
        }
    });
}

function fnCancelEdit(hiddendiv,openform) {
    $.ajax({
        type: "GET",
		<?php
		$e_reg = new Event("ProjectDiscussEditSave->eventAjaxSetRegFalse");
		$e_reg->setEventControler("ajax_evctl.php");
		$e_reg->setSecure(false);
		?>
        url: "<?php echo $e_reg->getUrl(); ?>",
        success: function(edit_form){
        }
    });
	$("#e"+openform).hide(0);
	$("#e"+openform)[0].innerHTML = "";
	$("#"+hiddendiv).show(0);
};

function fnEditNoteMoreOpts(){
  $("#edit_note_more").hide("slow");
  $("#edit_note_more_opts").show(0);
}

<?php if ($do_project_task->status=='open') { ?>
window.setInterval("fnSaveDraft()", 30000);
<?php } ?>
//]]>
</script>

<script type="text/javascript">
function autoLoadPrjDiscussion() {
		$('div#last_prj_loader').html('<img src="/images/loader1.gif">');
		$.ajax({
			type: "GET",
		<?php
		$e_discussion = new Event("ProjectDiscussEditSave->autoLoadPrjDiscussionOnScrollDown");
		$e_discussion->setEventControler("ajax_evctl.php");
		$e_discussion->setSecure(false);
		?>
			url: "<?php echo $e_discussion->getUrl(); ?>",
			//data: "dob="+dob,
			success: function(data){
					$(".message_box:last").after(data);
					$('div#last_prj_loader').empty();
			}
		});
		
}
<?php if ($count_discussion > $do_discuss->getNumRows()) { ?>
$(document).ready(function() {
   $(window).scroll(function(){
      if ($(window).scrollTop() == $(document).height() - $(window).height()){
          autoLoadPrjDiscussion();
      }
   });
});
<?php } ?>
</script>

<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Projects'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$task_access){
              $msg = new Message(); 
              //echo '<div class="messageshadow_unauthorized">';
              //echo '<div class="messages_unauthorized">';
              $msg->getMessage("unauthorized task access");
			  echo $msg->displayMessage();
              //echo '</div></div><br /><br />';
              exit;
          }
    ?> 
    <table class="layout_columns"><tr><td class="layout_lcolumn">
    <?php
        //For public access block the following
        if($task_operation_access === true){
	  include_once('plugin_block.php');
	} // Public access hide ends here
    ?>    

    <?php
          if(!is_object($_SESSION['UserSettings'])) {
              $do_user_settings = new UserSettings();
              $do_user_settings->sessionPersistent("UserSettings", "logout.php", OFUZ_TTL);
          }
          $data = $_SESSION['UserSettings']->getSettingValue("task_discussion_alert");
          $global_discussion_email_on = 'Yes';
          if(!$data){
              $global_discussion_email_on = 'Yes';
          }else{
              if(is_array($data)){
                  if($data["setting_value"] == 'Yes'){
                      $global_discussion_email_on = 'Yes';
                  }else{ $global_discussion_email_on = 'No'; }
              }
          }
          $_SESSION['UserSettings']->global_task_discussion_alert = $global_discussion_email_on;
          // Task level email alert is not required in this version
         
    ?>
    
    </td><td class="layout_rcolumn">
	    <?php
            $msg = new Message(); 
			if ($msg->getMessageFromContext("task discussion")) {
				echo $msg->displayMessage();
			}
        ?> 
         <div class="mainheader pad20">
                <span class="page_title"><?php echo $_SESSION['do_project_task']->task_category.": ".$_SESSION['do_project_task']->task_description; ?></span>
                <?php
                if (is_object($GLOBALS['cfg_submenu_placement']['task'] ) ) {
                	echo  $GLOBALS['cfg_submenu_placement']['task']->getMenu();
                }
                ?>
        </div>
               
        <div id="ptask_ctlbar" style="display: none;">
            <?php
                $do_project_list = new Project();
                $do_project_list->getAllProjects();
                $SelectOptions = $do_project_list->getProjectsSelectOptions($_SESSION['do_project_task']->idproject);
                $e_ProjectTask = new Event('ProjectTask->eventUpdateProjectTask');
                $e_ProjectTask->setLevel(1999);
                $e_ProjectTask->addEventAction('WorkFeedProjectTask->eventAddFeed', 2002);
                $e_ProjectTask->addEventAction('mydb.gotoPage', 2010);
                $e_ProjectTask->addParam('goto', 'Task/'.$idproject_task);
                $e_ProjectTask->addParam('task_event_type', 'update_task');
                echo $e_ProjectTask->getFormHeader();
                echo $e_ProjectTask->getFormEvent();

                $do_project_list->idproject = $_SESSION['do_project_task']->idproject;
                $task_category_arr = $do_project_list->getDistinctTaskCategoryForProjectUnionUser();
            ?>
            Task name: &nbsp; <input type="text" name="task_description" value="<?php echo htmlentities($_SESSION['do_project_task']->task_description); ?>" style="width:400px;" /><br /><br />
            Project: &nbsp; <select name="idproject"><?php echo $SelectOptions; ?></select><br /><br />
            <input type="hidden" name="idproject_task" value="<?php echo $_SESSION['do_project_task']->idproject_task; ?>" />
            <input type="hidden" name="idtask" value="<?php echo $_SESSION['do_project_task']->idtask; ?>" />
            <?php
                   if($task_category_arr && is_array($task_category_arr)){
                          include_once("includes/dojo.dijit.combobox.js.inc.php");
                           $fval_select ='';
                          echo _('Task Category :').$fval_select.'<select name ="task_category" id = "task_category" 
                               class=""  dojoType="dijit.form.ComboBox"
	                       autocomplete="false"
	                       value=""
	                       >';
                          echo '<option value = "" > </option>';
                          foreach($task_category_arr as $category){
                            $select = '';
                            if($_SESSION['do_project_task']->task_category == $category['category']){ $select = "Selected"; }
                            echo '<option value = "'.$category['category'].'" '.$select.'>'.$category['category'].'</option>';
                          }
                      }
                      echo '</select><br /><br />';
                      $date_text_box_name = "dijit.form.DateTextBox";
                      include_once("includes/dojo.dijit.datetextbox.js.inc.php");
					  if($_SESSION['do_project_task']->due_date_dateformat == "0000-00-00") {
					  	$due_date = "";
					  } else {
					  	$due_date = $_SESSION['do_project_task']->due_date_dateformat;
					  }
                      $fval .= '<input class="'.$field_class.'" name="due_date" 
                        id = "due_date"
                        value="'.$due_date.'"
	                dojoType ="'.$date_text_box_name.'" />';
                     echo _('Due Date : '). $fval.'<br /><br />';
                     echo _('Hours of work expected :').'<input type = "text" name = "hrs_work_expected" id ="hrs_work_expected" value = '.$_SESSION['do_project_task']->hrs_work_expected.'><br /><br />';
                     $do_task = new Task();
                     $do_project_owner = new Project();
                     //if($do_project_owner->isProjectOwner($_SESSION['do_project_task']->idproject) || $do_task->isTasKOwner($_SESSION['do_project_task']->idtask)){// Only task owner or the project owner
                          $open_checked  = '';
                          $closed_checked  = '';
                          if($_SESSION['do_project_task']->status == 'closed'){
                              $closed_checked = 'Checked';
                          }else{
                              $open_checked = 'Checked';
                          }
                          echo _('Task Status').'&nbsp;&nbsp;<input type = "radio" name = "status" id="status" value = "open" '.$open_checked.'>'._('Open').'&nbsp;&nbsp;<input type = "radio" name = "status" id="status" value = "closed" '.$closed_checked.'>'._('Closed').'<br /><br /> ';
                      //}else{
                        //  echo '<input type="hidden" name = "status" value ="'.$_SESSION['do_project_task']->status.'" >';
                      //}
            ?>
            <div class="div_right"><input type="submit" value="<?php echo _('Save');?>" /> &nbsp;<?php echo _('or');?>&nbsp; <a href="#" onclick="fnEditTask(); return false;"><?php echo _('cancel');?></a></div>
            </form>
        </div>
        <div class="contentfull">
        <?php
          if($do_project_task->status=='open'){
            if($_SESSION["do_project"]->status != 'closed'){
        ?>
            <div class="headline_fuscia"><?php echo _('Get into the discussion:');?></div>
            <?php
                  $draft = $_SESSION['do_note_draft']->getDraft($idproject_task,"project_discuss");
                  if($draft && is_array($draft)){
                    $text = $draft["note_content"];
                    //$added_time = date("l, F j, g:i A ", $draft["timestamp"]);
                    $added_time = OfuzUtilsi18n::formatDateLong(date("Y-m-d H:i:s",$draft["timestamp"]),true);
                    echo '<div id="unsaved_draft" style="display:block;"><div class="messageshadow"><div class="messages">';
                    echo '<b>'._('You have an unsaved note draft on : ').$added_time.'</b><br />';
                    echo $text;
                    echo '<br />';
                    echo '<a href="#" onclick = "copyDraft();return false;">'._('Copy this').'</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick = "discardDraft('.$draft["idnote_draft"].'); return false;">'._('Discard this').'</a>';
                  ?>
                   <input type="hidden" id="draft_hidden" name="draft_hidden" value="<?php echo $text; ?>">
                 <?php   echo '</div></div></div>';
                  }
                }
            ?>
              <div id="draft_saved" style="display:none">
                  <div class="messageshadow">
                    <div class="messages">
                      <?php
                            echo _('Note is saved as a draft');
                      ?>
                    </div>
                  </div>
              </div>  
            <div class="percent95">
            <?php              
            if($_SESSION["do_project"]->status != 'closed'){
      
                /**
                     Discussion Add form starts here 
                */
              if($task_operation_access === true){
                $_SESSION['ProjectDiscussEditSave']->setLogRun(true);
                $e_addProjectDiscuss = $_SESSION['ProjectDiscussEditSave']->newForm('ProjectDiscussEditSave->eventAdd');
                $e_addProjectDiscuss->setLevel(123);
                $e_addProjectDiscuss->setGotFile(true);
                $e_addProjectDiscuss->addEventAction('mydb.gotoPage', 90);
                //$e_addProjectDiscuss->addEventAction('ProjectDiscussEditSave->eventFormatDiscussInsert', 119);
                $e_addProjectDiscuss->addEventAction('ProjectDiscussEditSave->eventHTMLCleanUp', 119);
                $e_addProjectDiscuss->addEventAction('ProjectDiscussEditSave->eventSendDiscussMessageByEmail', 131);
                $e_addProjectDiscuss->addEventAction('WorkFeedProjectDiscuss->eventAddFeed', 140);
                $e_addProjectDiscuss->addParam('goto', $return_page);
                $e_addProjectDiscuss->addParam('errPage', 'Task/'.$idproject_task);
                $discussFields = new FieldsForm('ofuz_add_project_discuss');
                echo $e_addProjectDiscuss->getFormHeader();
                echo $e_addProjectDiscuss->getFormEvent();
                // display the note text field:
                echo $discussFields->discuss ;
                //hidden fields
                echo $discussFields->idproject_task;
                echo $discussFields->iduser;      
                echo $discussFields->type;// Hidden Type, default :: Note   
                if($draft && is_array($draft)){
                   //echo '<input type="hidden" name="idnote_draft" value="'.$draft["idnote_draft"].'">';
                }
                ?>
                
                
                <!--<span id="more_options"><a href="#"><?php //echo _('More Options'); ?></a>-->
                  <!--<a href="#" onclick="showOpt(); return false;"><?php //echo _('More Options'); ?></a>-->
                <!--</span>-->
                <div class="div_right">
                    <div id="discuss_options" style="display:none;">
                        <?php echo _('Hours Worked').': '.$discussFields->hours_work; ?>  
                        <br/>  
                        <?php echo _('Attach a file').': '.$discussFields->document; ?> 
                        <br/>
                        <?php echo _('When this happened').': '.$discussFields->date_added; ?><br />
                        <?php echo _('Who can edit ? ') ?>
                        <input type="radio" name="fields[discuss_edit_access]" value="user" checked> <?php echo _('Just me');?> &nbsp;&nbsp;
                        <input type="radio" name="fields[discuss_edit_access]" value="user coworker"><?php echo _('My Co-Workers and I');?>
                        <br /><a href="#" onclick="fnSaveDraftOnClick();return false;"><?php echo _('Save As Draft')?></a><br />
                    </div>
                </div>
                <div class="div_right"><input type="submit" name="submitaction" value="<?php echo _('Add this notes');?>" />
                
                </div>
                </form>
            </div>
             
             <?php
              }
          }else {              
              if($do_project_task->status =='closed'){
              ?>
                <div class="headline_fuscia"><?php echo _('This project is closed'); ?></div>
              <?php
              }else{
          ?>                
              <div class="headline_fuscia"><?php echo _('This project is closed'); ?></div>
          <?php
              }
          }
              /** Discussion Adding Section ends */

            // Deleted Note section
            $deleted_note = $_SESSION['ProjectDiscussEditSave']->getNotesDataFromDeleted($idproject_task,"project_discuss","ProjectDiscuss");
            if(is_array($deleted_note) && count($deleted_note) > 0 ){
                echo $_SESSION['ProjectDiscussEditSave']->viewDeletedNote($deleted_note,"ProjectDiscuss"); 
            } // Ends here
        ?>
            <?php } else{                  
                  if($_SESSION["do_project"]->status == 'closed'){                    
                  ?>
                     <div class="headline_fuscia"><?php echo _('The Project and task is closed'); ?></div>
                  <?php
                  }else{
                  ?>
                <div class="headline_fuscia"><?php echo _('This task is closed'); ?></div>
            <?php }
                 }
                $do_discuss->sessionPersistent('ProjectDiscussEditSave', "project.php", OFUZ_TTL);
                if ($do_discuss->getNumRows()) {
                    echo '<div class="spacerblock_24"></div>', "\n";
                    echo '<div class="headline_fuscia">', $_SESSION['do_project_task']->task_description, ' '._('discussion').'</div>', "\n";
                    $item_count = 0;
                    while ($do_discuss->next()) {
                        $file = '';
                        $preview_item = '';
                        if($do_discuss->document != ''){
                            $doc_name = $do_discuss->document;
                            //$doc_name = str_replace("  ","%20%20",$do_discuss->document);
                            //$doc_name = str_replace(" ","%20",$doc_name);
                            $file_url = "/files/".$doc_name;
                            //$file_url = '/files/'.$do_discuss->document;
                            $file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_discuss->document.'</a>';
                               //$file = '<br /><a href="/files_download.php?e=ProjectTask&id='.$idproject_task.'&file='.$do_discuss->document.'" target="_blank">'.$do_discuss->document.'</a>';
                        }
                        
                        $item_text = $do_discuss->formatDiscussionItemDisplay($do_discuss->discuss, 500);
                        //if (substr_count($item_text, '<br />') > 4) {
                        //	$preview_item = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$item_text)).' ';
                        //} else if (strlen($item_text) > 500) {
                        //    $preview_item = substr($item_text, 0, 500);
                        //}
                        if($do_discuss->iduser){
                          $added_by = $_SESSION['do_User']->getFullName($do_discuss->iduser);
                          $do_contact = new Contact();
                          $do_contact->getUserContacts($do_discuss->iduser);                          
                            if($do_contact->getNumRows()){
                              //while($do_contact->next()){   
                              if($do_contact->picture!=''){
                                 $contact_picture="/dbimage/thumbnail/".$do_contact->picture; 
                               }else{
                                 $contact_picture='/images/empty_avatar.gif';
                               }  
                              $contact_id = $do_contact->idcontact;
                              //}
                          }else{
                            $contact_picture='/images/empty_avatar.gif';
                          }

                        }else{
                          $added_by = $do_discuss->drop_box_sender;
                          $contact_picture='/images/empty_avatar.gif';
                        }

                        $e_gen_dropboxid = new Event('do_project_task->eventGenerateDropBoxIdTask');
                        $e_PrioritySort = new Event('ProjectDiscuss->eventPrioritySortNotes');
                        $e_PrioritySort->addParam('goto', 'Task/'.$idproject_task);
                        $e_PrioritySort->addParam('idnote', $do_discuss->idproject_discuss);
                        $star_title = ($do_discuss->priority > 0) ? 'Unstar this note' : 'Star this note to move it on top';
                        $star_img_url = '<img src="/images/'.($do_discuss->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="'._($star_title).'" />';
                        if (is_object($_SESSION['ProjectDiscussEditSave'])) {
                            $e_discuss_del = new Event('ProjectDiscussEditSave->eventTempDelNoteById');
                        }
                        $e_discuss_del->addParam('goto', 'Task/'.$idproject_task);
                        $e_discuss_del->addParam('id', $do_discuss->idproject_discuss);
                        $e_discuss_del->addParam('context', 'ProjectDiscuss');
                        $del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
                        echo '<div id="notetext',$do_discuss->idproject_discuss,'" class="vpad10">';
                        echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/discussion.png" class="note_icon" width="16" height="16" alt='._('Task Discussion').'" />';                        
                        if($task_operation_access === true){
                          //echo $e_PrioritySort->getLink($star_img_url, ' title="'._('Star this note to move it on top1').'"');
                          echo $e_PrioritySort->getLink($star_img_url, ' title="'._($star_title).'"');
                        }

                        list($yyyy,$mm,$dd) = split("-",$do_discuss->date_added);
                        /*if($yyyy < date('Y')) {
                          echo '<b>'.date('l, F j Y', strtotime($do_discuss->date_added)).'</b>&nbsp;('._('Added By :').'&nbsp;'.$added_by.')</div>'; 
                        } else {
                          echo '<b>'.date('l, F j', strtotime($do_discuss->date_added)).'</b>&nbsp;('._('Added By :').'&nbsp;'.$added_by.')</div>'; 
                        }*/
                        $date_added_note =  OfuzUtilsi18n::formatDateLong($do_discuss->date_added);   
                        
                        echo '<div id="item_title"> '.$date_added_note.'</b>&nbsp;('._('Added By :').'&nbsp;<i><strong>'.$added_by.'</strong></i>)</div></div>'; 
                        echo "<br>";
                        echo '<a href="/Contact/'.$contact_id.'"> <img width="34" height="34"alt="" src='.$contact_picture.' > </a>';
                        if($task_operation_access === true){
                          echo '<div id="trashcan', $item_count++, '" class="deletenote" style="right:0;">'.'<a href="#"  onclick="fnEditNote(\'notetext'.$do_discuss->idproject_discuss.'\','.$do_discuss->idproject_discuss.');return false;">'._('edit').'</a>&nbsp;|&nbsp;'.$e_discuss_del->getLink($del_img_url, ' title="'._('Delete this note').'"').'</div>';
                        }
                        echo '</div>';
                        if ($do_discuss->is_truncated) {
                            echo '<div id="item_text"><div id="notepreview',$do_discuss->idproject_discuss,'">',$item_text,'…<br/><br/><a href="#" id="more_item_text" onclick="showFullNote(',$do_discuss->idproject_discuss,'); return false;">'._('<strong>read more…</strong>').'</a><br /></div></div>';
                        } else {
                            echo "<div id='item_text'>".$item_text."</div>";
                        }
                        //echo '<div id="e'.$do_discuss->idproject_discuss.'" style="display: none;" class="note_edit_box"></div>';
                        echo $do_discuss->formatDocumentLink("ProjectTask").'</div>
                        <div id="e'.$do_discuss->idproject_discuss.'" style="display: none;" class="note_edit_box"></div>
                        <div id="'.$do_discuss->idproject_discuss.'" class="message_box"></div>';
                        
                    }
					
                }
            ?>
			<div id="last_prj_loader"></div>
            <div class="dottedline"></div>
            <div class="section20">
                &nbsp;
            </div>
            <div class="solidline"></div>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
