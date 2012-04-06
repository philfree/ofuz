<?php 

// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');

    /*include_once("class/Feed.class.php");*/
    if (isset($_GET['id'])) {
        $idcontact = $_GET['id']; 
    } elseif (is_object($_SESSION["eDetail_contact"])) {
        $idcontact = $_SESSION["eDetail_contact"]->getparam("idcontact");
    } elseif($do_contact->idcontact){
       $idcontact = $do_contact->idcontact;
    } elseif(is_object($_SESSION['ContactEditSave'])) {
        $idcontact = $_SESSION['ContactEditSave']->idcontact;
    }
    
  $do_user = new User();
  $current_idcontact = $do_user->getContactId($_SESSION["do_User"]->iduser);

  /*if($current_idcontact != $idcontact){
    if(isset($_SESSION['edit_from_page'])) {
        unset($_SESSION['edit_from_page']);
    }
  }else{*/
    $_SESSION['edit_from_page'] = 'Contact/'.$idcontact;
  //}

    /* Contacts Note sharing Object 
    */
    if(!is_object($_SESSION['do_cont'])){
      $do_cont = new Contact();
      $do_cont->sessionPersistent("do_cont", "index.php", OFUZ_TTL);
      $_SESSION['do_cont']->idcontact = $idcontact;
    }else{ $_SESSION['do_cont']->idcontact = $idcontact; }
    
    $_SESSION['do_cont']->idcontact = $idcontact;
    $contact_access = false;
    $do_contact = new Contact($GLOBALS['conx']);
    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_task_category = new TaskCategory();
    $do_contact_task = new Contact();
    if($do_contact->isContactRelatedToUser($idcontact)){
      $contact_access = true;
      $do_contact->getContactDetails($idcontact);
      $do_contact->setBreadcrumb();
    // Set this contact in the session so it can be edited on the contact_edit.php page.
      $do_contact->sessionPersistent("ContactEditSave", "contact.php", OFUZ_TTL);
    }else{
      $contact_access = false;
    }
	// For the ajax expend
    $ContactNoteExpend  = new ContactNotes($GLOBALS['conx']);
    $ContactNoteExpend->sessionPersistent("ContactNoteExpend", "contacts.php", OFUZ_TTL);
	
if(!is_object($_SESSION['do_note_draft'])) {
        $do_note_draft = new NoteDraft();
        $do_note_draft->sessionPersistent("do_note_draft","project.php",OFUZ_TTL);
    }

	/**
	 * resetting the sql query limit start whenever this page is loaded
	 */
	if(is_object($_SESSION['ContactNotes'])) {
		$_SESSION['ContactNotes']->sql_qry_start = 0;
	}
    $pageTitle = $_SESSION['do_cont']->getContactFullName().' :: Ofuz';
    include_once('includes/header.inc.php');
?>

<script type="text/javascript">
//<![CDATA[

<?php include_once('includes/ofuz_js.inc.php'); ?>
	
    var openform;
    var EventKey = 0;
    var NextTag = 0;
    function fnEditTask(task){
        if ($("#e"+openform).length > 0) fnCancelEdit(openform);
        openform = task;
        $.ajax({
            type: "GET",
        <?php
        $e_editForm = new Event("Task->eventAjaxEditTaskFormRHS");
        $e_editForm->setEventControler("ajax_evctl.php");
        $e_editForm->setSecure(false);
        ?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "id="+task,
            success: function(html){
            	$("#t"+task).hide(0);
            	$("#e"+task)[0].innerHTML = html;
                $("#e"+task).show(0);
            }
        });
    };
    function fnCancelEdit(task){
        $("#e"+task).hide(0);
        $("#e"+task)[0].innerHTML = "";
        $("#t"+task).show(0);
    };
    function fnTaskComplete(task){
        $.ajax({
            type: "GET",
        <?php
        $e_editForm = new Event("Task->eventAjaxTaskComplete");
        $e_editForm->setEventControler("ajax_evctl.php");
        $e_editForm->setSecure(false);
        ?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "id="+task,
            success: function(){
            	$("#t"+task).css("text-decoration", "line-through");
            	$("#t"+task).fadeOut("slow", function() {
            	    $("#e"+task).remove();
            	    $("#b"+task).remove();
                });
            }
        });
    };
    function showOpt(){
        $("#more_options").hide(0);
        $("#notes_options").show("fast");
    }



    $(document).ready(function(){
      $("#note").click(function(){
        $("#more_options").hide(0);
        $("#notes_options").show("fast");
      });
    });

  
    function showDateOpt(){
        $("#due_sp_date").show(0);
        $("#when_due").hide(0);
        document.getElementById('sp_date_selected').value = "Yes";
    }
    function hideDateOpt(){
        $("#due_sp_date").hide(0);
        $("#when_due").show(0);
        document.getElementById('sp_date_selected').value = "";
    }

    function showTagOpt(){
        $("#cont_tag_add").show(0);
        $("span[id^=delContactTag]").show(0);
        $("#EditTags").hide(0);
    }
    function hideTagOpt(){
        $("#cont_tag_add").hide(0);
        $("span[id^=delContactTag]").hide(0);
        $("#EditTags").show(0);
    }
    function showFullNote(idnote){
        $.ajax({
            type: "GET",
            <?php
            $e_ContactNote = new Event("ContactNoteExpend->eventAjaxGetContactNote");
            $e_ContactNote->setEventControler("ajax_evctl.php");
            $e_ContactNote->setSecure(false);
            ?>
            url: "<?php echo $e_ContactNote->getUrl(); ?>",
            data: "idnote="+idnote,
            success: function(notetext){
                $("#notepreview"+idnote)[0].innerHTML = notetext;
            }
        });
    }
    function addTask(){
    	$("#addatask").show("fast");
    	$("#addatask_button").hide("fast");
    }
    function addTaskFollowUp(){
        $("#follow_up_task").slideToggle("slow");
    }

    function fnAddTags(){
        var tags = ($("input[name='fields[tag_name]']").val()).split(",");
        $("input[name='fields[tag_name]']").val("");
        var i, len;
        for (i = 0, len = tags.length; i < len; i++) {
            fnAddTag(tags[i]);
        }
    }
    function fnAddTag(tag_name){
       var re = /.+/;
       if(tag_name.length != 0 && tag_name !=null && tag_name !="" && tag_name.match(re)){
        $.ajax({
            type: "GET",
            <?php
            $e_addTagAjax = new Event("Tag->eventAjaxAddTagAssociation");
            $e_addTagAjax->addEventAction("ContactView->eventAddTag");
            $e_addTagAjax->setEventControler("ajax_evctl.php");
            $e_addTagAjax->setSecure(false);
            ?>
            url: "<?php echo $e_addTagAjax->getUrl(); ?>",
            data: "tag_name="+escape(tag_name)+"&idcontact=<?php echo $idcontact; ?>",
            success: function(idtag){
            	if ($("#TagList")[0].innerHTML=="") {
            	    $("#TagList").append("Tags: ");
            	} else {
            	    $("#TagList").append(", ");
            	}
                $("#TagList").append('<span id="Tag'+NextTag+'"><a href="eventcontroler.php?mydb_events[100]=do_Contacts-%3EeventSearchByTag&goto=contacts.php&search_tag_name='+escape(tag_name)+'&mydb_eventkey='+EventKey+'">'+tag_name+'</a><span id="delContactTag'+NextTag+'">&nbsp;<a href="#" onclick="fnDeleteTag('+idtag+',\'Tag'+NextTag+'\'); return false;"><img src="/images/delete.gif" width="14" height="14" alt="Delete this tag" /></a></span></span>');
                NextTag++;
            }
        });
      }
    }
    function fnDeleteTag(idtag,iddom){
        $.ajax({
            type: "GET",
            <?php
              $e_delTagAjax = new Event("Tag->eventAjaxDeleteTagById");
              $e_delTagAjax->addEventAction("ContactView->eventDeleteTag");				
              $e_delTagAjax->setEventControler("ajax_evctl.php");
              $e_delTagAjax->setSecure(false);
            ?>
            url: "<?php echo $e_delTagAjax->getUrl(); ?>",
            data: "idtag_delete="+idtag+"&idcontact=<?php echo $idcontact; ?>",
            success: function(){
                $("#"+iddom).remove();
            }
        });
    }
    function autoFetchToggle(feed, idcontact_website){
    	if (feed.className.substring(0,9) == "feedcheck") return;
    	feed.src = "/images/wait16g.gif";
        $.ajax({
            type: "GET",
            <?php
            $e_Feed = new Event("ContactWebsite->eventAjaxToggleAutoFetch");
            $e_Feed->setEventControler("ajax_evctl.php");
            $e_Feed->setSecure(false);
            ?>

            url: "<?php echo $e_Feed->getUrl(); ?>",
            data: "idcontact_website="+idcontact_website,
            success: function(icon){
                feed.src = icon;
            }
        });
    }
    $(document).ready(function() {
        $("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
        $.ajax({
            type: "GET",
            <?php
            $e_EventKey = new Event("EventKey->eventAjaxGetEventKey");
            $e_EventKey->setEventControler("ajax_evctl.php");
            $e_EventKey->setSecure(false);
            ?>
            url: "<?php echo $e_EventKey->getUrl(); ?>",
            success: function(key){
                EventKey = key;
            }
        });
        $("input[class^=feedcheck]").each(function(){
        	var feed = this;
            $.ajax({
                type: "GET",
                <?php
                $e_FeedCheck = new Event("Feed->eventAjaxNumOfFeedsInWebPage");
                $e_FeedCheck->setEventControler("ajax_evctl.php");
                $e_FeedCheck->setSecure(false);
                ?>
                url: "<?php echo $e_FeedCheck->getUrl(); ?>",
                data: "idcontact_website="+feed.getAttribute("id").substring(4),
                success: function(count){
                    if (count == 0 || isNaN(count)) {
                        $(feed).remove();
                    } else if (feed.className == "feedcheckYes") {
                        feed.src = "/images/feed-icon-12x12-green.png";
                    } else {
                        feed.src = "/images/feed-icon-12x12-orange.gif";
                    }
                    feed.setAttribute("class", "feedverified");
                    feed.setAttribute("className", "feedverified");
                }
            });
        });
    });

function fnSaveDraft(){
  var discuss_text = document.getElementById('note').value;
  //alert(discuss_text);
  $.ajax({
        type: "GET",
        <?php
        $e_NoteDraft = new Event("do_note_draft->eventAddUpdateDraft");
        $e_NoteDraft->setEventControler("ajax_evctl.php");
        $e_NoteDraft->setSecure(false);
        ?>
        url: "<?php echo $e_NoteDraft->getUrl(); ?>",
        data: "text="+escape(discuss_text)+"&id_type=contact_note&id=<?php echo $idcontact; ?>",
        success: function(result){
        }
    });
}

function fnSaveDraftOnClick(){
  var discuss_text = document.getElementById('note').value;
  $.ajax({
        type: "GET",
        <?php
        $e_NoteDraft = new Event("do_note_draft->eventAddUpdateDraft");
        $e_NoteDraft->setEventControler("ajax_evctl.php");
        $e_NoteDraft->setSecure(false);
        ?>
        url: "<?php echo $e_NoteDraft->getUrl(); ?>",
        data: "text="+escape(discuss_text)+"&id_type=contact_note&id=<?php echo $idcontact; ?>",
        success: function(result){
            $("#draft_saved").show("fast");
            window.setInterval("reloadPage()", 4000);
        }
    });
}
function reloadPage(){
   window.location = '/contact.php';
}
function copyDraft(){
  var textcontent =   document.getElementById('draft_hidden').value ;
  document.getElementById('note').value =  textcontent; 
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
$e_edit_form = new Event("ContactNoteEditSave->eventAjaxGetEditForm");
$e_edit_form->addParam("idcontact",$idcontact);
$e_edit_form->setEventControler("ajax_evctl.php");
$e_edit_form->setSecure(false);
?>
        url: "<?php echo $e_edit_form->getUrl(); ?>",
        data: "idnote="+idnote+"&curdiv="+div_id,
        success: function(edit_form){
            //$("#"+div_id)[0].innerHTML = edit_form;
            //$("#note_edit").expandable();
            //$("#note_edit").focus();
            $("#"+div_id).hide(0);
            $("#e"+idnote)[0].innerHTML = edit_form;
            $("#e"+idnote).show(0);
            $("#note_edit").expandable();
            $("#note_edit").focus();
        }
    });
}

function fnCancelEdit(hiddendiv,openform) {
        $("#e"+openform).hide(0);
        $("#e"+openform)[0].innerHTML = "";
        $("#"+hiddendiv).show(0);
    };

function fnEditNoteMoreOpts(){
  $("#edit_note_more").hide("slow");
  $("#edit_note_more_opts").show(0);
  $("#edit_note_more_opts_vis").show(0);
}

window.setInterval("fnSaveDraft()", 30000);

function showProjectList(){
	$("#cp_prj_list").show("slow");
}

function populateTasks() {
	var idproject = document.getElementById('cpy_prjs').value;

    $.ajax({
        type: "GET",
        <?php
        $e_prj = new Event("ProjectTask->eventGetProjectTasks");
        $e_prj->setEventControler("ajax_evctl.php");
        $e_prj->setSecure(false);
        ?>
        url: "<?php echo $e_prj->getUrl(); ?>",
        data: "idproject="+idproject,
        success: function(result){
             $("#cp_prj_tasks")[0].innerHTML = result;
        }
    });
}

function autoLoadContactNotes() {
		$('div#last_note_loader').html('<img src="/images/loader1.gif">');
		$.ajax({
			type: "GET",
		<?php
		$e_notes = new Event("ContactNotes->autoLoadNotesOnScrollDown");
		$e_notes->setEventControler("ajax_evctl.php");
		$e_notes->setSecure(false);
		?>
			url: "<?php echo $e_notes->getUrl(); ?>",
			//data: "dob="+dob,
			success: function(data){
					$(".message_box:last").after(data);
					$('div#last_note_loader').empty();
			}
		});
		
}

$(document).ready(function()
{
$(window).scroll(function(){
if ($(window).scrollTop() == $(document).height() - $(window).height()){
autoLoadContactNotes();
}
});
});
//]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $GLOBALS['thistab'] = _('Contacts'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$contact_access){
              $msg = new Message(); 
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo $msg->getMessage("unauthorized contact access");
              echo '</div></div><br /><br />';
              exit;
          }
    ?>
    <table class="layout_columns"><tr><td class="layout_lcolumn">

<?php
$GLOBALS['page_name'] = 'contact';
include_once('plugin_block.php');
?>
</td><td class="layout_rcolumn"><div class="min660">
	     <?php
            $msg = new Message(); 
			if ($msg->getMessageFromContext("contact")) {
				echo $msg->displayMessage();
			}
        ?>
        <div class="contact_info">
            <div class="contact_photo_frame">
                <div class="contact_photo" style="overflow:hidden;">
                    <?php
                          if($do_contact->picture == '' || empty($do_contact->picture)){
                    ?>
                    <a href="/contact_edit.php"><img src="/images/empty_avatar.gif" width="100" height="100" alt="" /></a>
                    <?php }else{
                    ?>
                    <img src="<?php echo $do_contact->getContactPicture();?>"  height="100%" alt="" />
                    <?php } ?>
                </div>
            </div>
            <div class="contact_detail">
				<div id="contact_detail_sub1">
	                <div class="contact_detail_name"><?php echo $do_contact->firstname, ' ', $do_contact->lastname; ?></div>
	                <div class="contact_detail_company">
	                            <?php
	                                $e_detail_com = new Event("mydb.gotoPage");
	                                $e_detail_com->addParam("goto", "company.php");
	                                $e_detail_com->addParam("idcompany",$do_contact->idcompany);
	                                $e_detail_com->addParam("tablename", "company");
	                                $e_detail_com->requestSave("eDetail_company", "contact.php");
	                                $dis = '<a href =" '.$e_detail_com->getUrl().'">'.$do_contact->company.'</a>';
	                                //echo $do_contact->position,' ', _('at'), ' ', $dis;
                                    if($do_contact->position != ''){
                                       echo $do_contact->position,' ', _('at'), ' ', $dis;
                                    }else{ echo $dis ; }
	                             ?>
	                </div>
	                <div><?php
	                                 $idtags = $do_contact->getTags();
	                                 $do_tag = new Tag();
	                                 $e_tag_search = new Event("do_Contacts->eventSearchByTag");
	                                 $e_tag_search->addParam("goto", "contacts.php");
	                                 if (is_array($idtags)) {
	                                     echo '<span id="TagList">Tags: ';
	                                     $dTagId = 0;
	                                     foreach ($idtags as $idtag) {
	                                         $do_tag->getId($idtag);
	                                         $e_tag_search->addParam("search_tag_name", $do_tag->tag_name);
	                                         echo '<span id="Tag',$dTagId,'">',($dTagId > 0 ? ', ' : ''),'<a href="'.$e_tag_search->getUrl().'">'.$do_tag->tag_name.'</a>';
	                                         echo '<span id="delContactTag',$dTagId,'" style="display:none;">&nbsp;<a href="#" onclick="fnDeleteTag(',$do_tag->idtag,',\'Tag',$dTagId,'\'); return false;"><img src="/images/delete.gif" width="14" height="14" alt="Delete this tag" /></a></span></span>';
	                                         $dTagId++;
	                                     }
	                              ?></span><br /><br />
	                            <script type="text/javascript">
	                            //<![CDATA[
	                            NextTag = <?php echo $dTagId ?>;
	                            //]]>
	                            </script>
	                            <?php } else { ?>
	                                <span id="TagList"></span><br />
	                            <?php } ?><form method="get" action="contact.php" onsubmit="fnAddTags(); return false;">
	                        <span id="EditTags"><img src="/images/edit_tags_icon.gif" class="edit_tags_icon" width="16" height="16" alt="" /><a href="#" onclick="showTagOpt(); return false;"><?php echo (isset($idtags) && is_array($idtags) ? 'Edit' : 'Add'); ?> tags</a></span><br />
	                        <div id="cont_tag_add" style="display: none;">
	                            <!--<input type="text" name="tags" />&nbsp;<input type="button" name="tagsadd" value="Add" onclick="fnAddTags();" />-->
								<?php
									$do_suggest_tag = new Tag();
									$do_suggest_tag->setFields("auto_suggest_tag"); 
									$do_suggest_tag->setApplyRegistry(true, "Form");
									echo $do_suggest_tag->tag_name;
								?>
								<input type="button" name="tagsadd" value="<?php echo _('Add')?>" onclick="fnAddTags();" />
								&nbsp;<a href="#" onclick="hideTagOpt(); return false;">Done</a>
	                        </div>
	                    </form>
	                </div>
				</div>
			</div>
			<div class="contact_invite">
				<?php
					/*$e_detail_invite_cw = new Event("mydb.gotoPage");
					$e_detail_invite_cw->addParam("goto", "invite_as_cw.php");
					$e_detail_invite_cw->addParam("idcontact",$do_contact->idcontact);
					$e_detail_invite_cw->addParam("firstname", $do_contact->firstname);
					$e_detail_invite_cw->addParam("lastname", $do_contact->lastname);
					$e_detail_invite_cw->addParam("email", $do_contact->email);
					$e_detail_invite_cw->addParam("tablename", "contact");
					$e_detail_invite_cw->requestSave("eDetail_invite_cw", "contact.php");*/
					$button_invite = new DynamicButton();
					//echo $button_invite->CreateButton($e_detail_invite_cw->getUrl(), _('invite as Co-Worker'));
     echo $button_invite->CreateButton('/invite_as_cw.php', _('invite as Co-Worker'));
				?>
			</div>
        </div>
        <div class="grayline2"></div>
        <?php
               if($_SESSION['in_page_message'] == 'follow_up_task'){
                  echo '<br />';
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo _('Its a good time to set the next date when to contact').' '.$do_contact->firstname.' '._(' with a ').' '.'<a href="#" onclick="addTaskFollowUp();return false;">'._('Follow UP task').'</a>';
                  $_SESSION['in_page_message'] = '';
                  echo '<div id="follow_up_task" style="display:none;">';
                  $do_task_add = new Task();
                  echo $do_task_add->getTaskAddContactRelatedForm();
                  echo '</div>';
                  echo '</div></div>';
              }
        ?>
        
        <div class="headline_fuscia">Add a Note About <?php echo $do_contact->firstname; ?></div>
        <?php
                  $draft = $_SESSION['do_note_draft']->getDraft($idcontact,"contact_note");
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
            $ContactNotes  = new ContactNotes($GLOBALS['conx']);
            $ProjectDiscuss = new ProjectDiscuss();
            $WorkFeedContactNote = new WorkFeedContactNote();
            $ContactNotes->sessionPersistent("ContactNoteEditSave", "contacts.php", OFUZ_TTL);
            $_SESSION['ContactNoteEditSave']->idcontact = $idcontact;
			
            //$return_page = $_SERVER['PHP_SELF'];
            $return_page = '/Contact/'.$idcontact;
            $e_addContactNote = $_SESSION['ContactNoteEditSave']->newForm("ContactNoteEditSave->eventAdd");
            $e_addContactNote->setLevel(123);
            $e_addContactNote->setGotFile(true);
            $e_addContactNote->addEventAction("mydb.gotoPage", 390);
            //$e_addContactNote->addEventAction("ContactNoteEditSave->eventFormatNoteInsert", 119);
            $e_addContactNote->addEventAction("ContactNoteEditSave->eventHTMLCleanUp", 119);
            $e_addContactNote->addEventAction("ContactNoteEditSave->eventSetFollowUpTaskReminder", 300);
            $e_addContactNote->addEventAction("ContactEditSave->eventSendNote", 238);
            $e_addContactNote->addEventAction("ContactNoteEditSave->eventDelNoteDrafts", 239); // make sure to have this to delete the drafts
            $e_addContactNote->addEventAction("WorkFeedContactNote->eventAddFeed", 245);
            $e_addContactNote->addEventAction("ProjectDiscuss->eventCopyContactNoteToPrjDiscuss",240);
            $e_addContactNote->addParam("goto", $return_page);
            $e_addContactNote->addParam("errPage", $return_page);
            $noteFields = new FieldsForm("ofuz_add_contact_note");

            // form headers:
            echo $e_addContactNote->getFormHeader();
            echo $e_addContactNote->getFormEvent();

            // display the note text field:
            //echo $noteFields->idcontact;
            //echo $noteFields->date_added;
            echo $noteFields->note;
            echo $noteFields->iduser;
         ?>
        <span id="more_options"><a href="#" onclick="showOpt(); return false;">&#9654;<?php echo _(' more options');?></a></span>
        <div class="div_right">
            <div id="notes_options" style="display:none;">
				<div style="position:relative;float:left;width:40%;text-align: left;">
				<?php echo _('Who can view this note:'); ?><br />
			<?php if ($GLOBALS['cfg_plugin_contact_file_upload_url']) { ?>
				<input type="radio" name="fields[note_visibility]" value="user"> <?php echo _('Just me');?> <br />
				<input type="radio" name="fields[note_visibility]" value="user coworker"> <?php echo _('My Co-Workers and I');?> <br />
				<input type="radio" name="fields[note_visibility]" value="user contact"> <?php printf(_("%s %s and I"), $do_contact->firstname,$do_contact->lastname); ?><br />
				<input type="radio" name="fields[note_visibility]" value="user coworker contact" checked> <?php printf(_("Me, Co-Workers and %s %s"), $do_contact->firstname, $do_contact->lastname);?>	
			<?php } else { ?>
                <input type="radio" name="fields[note_visibility]" value="user"> <?php echo _('Just me');?> <br />
                <input type="radio" name="fields[note_visibility]" value="user coworker" checked> <?php echo _('My Co-Workers and I');?> <br />
			<?php } ?>
				</div>
				<div style="position:relative;float:left;width:60%;text-align: right;">
								<?php echo _('Attach a file:'); ?> <?php echo $noteFields->document; ?> 
								<br/>
								<?php echo _('When this happened:');?> <?php echo $noteFields->date_added; ?>
								<br />
								<?php echo _('Hours Worked:');?> <?php echo $noteFields->hours_work;?>
							<?php if ($_SESSION['ContactEditSave']->email_optout != 'y') { ?>	<br/>
								<?php echo _('Send a copy to this contact: ');?><?php echo $noteFields->send_email; ?>
							<?php } ?>
								<br /><a href="#" onclick="fnSaveDraftOnClick();return false;"><?php echo _('Save As Draft')?></a><br />
				</div>
            </div>
        </div>
        <div class="div_right">
            <?php 
                  echo $noteFields->type; // Hidden Note type and default is 'Note'
                  echo $e_addContactNote->getFormFooter(_('Add this note')); 
                  echo '</form>'; // closing the form so as other forms can be operative
            ?>
            
        </div>
        <?php
            // Deleted Note
            
            $deleted_note = $do_notes->getNotesDataFromDeleted($idcontact,"contact");
            if(is_array($deleted_note) && count($deleted_note) > 0 ){
				  $do_cn = new ContactNotes();
                  echo $do_cn->viewDeletedNote($deleted_note,"ContactNote"); 
            }
        ?>
        <?php
		
        $do_notes->getContactNotes($idcontact);
        $do_notes->sessionPersistent("ContactNotes", "contacts.php", OFUZ_TTL);
        // Make sure to have the same HTML in autoLoadNotesOnScrollDown() method as below
        if ($do_notes->getNumRows()) {
            echo '<div class="headline_fuscia">';
            printf(_("%s's Notes:"), $do_contact->firstname );
            echo '</div>'. "\n";
            $note_count = 0;
            while ($do_notes->next()) {
				if($do_notes->iduser != $_SESSION['do_User']->iduser) {
					if($do_notes->note_visibility == 'user coworker' || $do_notes->note_visibility == 'user coworker contact') {
						$file = '';
						$preview_note = '';
						if($do_notes->document != ''){
							$doc_name = $do_notes->document;
							$doc_name = str_replace("  ","%20%20",$do_notes->document);
							$doc_name = str_replace(" ","%20",$doc_name);
							
							$file_url = "/files/".$doc_name;
							$file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_notes->document.'</a>';
						}
					// Format the note text
						$note_text = $do_notes->formatNoteDisplayShort();
						//if (substr_count($note_text, '<br />') > 4) {
						//	$preview_note = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$note_text)).' ';
						//} else if (strlen($note_text) > 500) {
						//	$preview_note = substr($note_text, 0, 500).' ';
						//}
						// Formating note ends here
		
						$added_by = $_SESSION['do_User']->getFullName($do_notes->iduser);
						// Priority sort
						$e_PrioritySort = new Event('ContactNotes->eventPrioritySortNotes');
						$e_PrioritySort->addParam('goto', 'contact.php');
						$e_PrioritySort->addParam('idnote', $do_notes->idcontact_note);
						// Priority sort Ends here
      $star_title = ($do_notes->priority > 0) ? 'Unstar this note' : 'Star this note to move it on top';
						$star_img_url = '<img src="/images/'.($do_notes->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="'._($star_title).'" />';
		
						// Generate the delete event
						if (is_object($_SESSION["ContactNotes"])) {
							$e_note_del = new Event("ContactNotes->eventTempDelNoteById");
						}
						$e_note_del->addParam("goto", "contact.php");
						$e_note_del->addParam("id", $do_notes->idcontact_note);
						$e_note_del->addParam("context", "ContactNote");
						$del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
						//delete event ends here
		
/*
       $do_contact = new Contact();
       $do_contact->getUserContacts($do_notes->iduser);
        if($do_contact->getNumRows()){
          while($do_contact->next()){   
            $contact_picture="/dbimage/".$do_contact->picture; 
            $contact_id = $do_contact->idcontact;
          }
        }*/
  


						// Start displaying the note
						echo '<div id="notetext', $do_notes->idcontact_note, '" class="vpad10">';
						echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/note_icon.gif" class="note_icon" width="16" height="16" alt="" />',$e_PrioritySort->getLink($star_img_url, ' title="'._($star_title).'"');
						/*list($yyyy,$mm,$dd) = split("-",$do_notes->date_added);
						if($yyyy < date('Y')) {
						  $added_by = date('l, F j Y', strtotime($do_notes->date_added));
						} else {
						  $added_by = date('l, F j', strtotime($do_notes->date_added)); 
						}*/
                        $added_by = OfuzUtilsi18n::formatDateLong($do_notes->date_added);
						echo '<b>'.$added_by.'</b>&nbsp;(Added By :&nbsp;'.$do_notes->getNoteOwnerFullName().')</div>
						<div id="trashcan', $note_count, '" class="deletenote" style="right:0;">'.'<a href="#"  onclick="fnEditNote(\'notetext'.$do_notes->idcontact_note.'\','.$do_notes->idcontact_note.');return false;">edit</a>&nbsp;|&nbsp;'.$e_note_del->getLink($del_img_url, ' title="'._('Delete this note').'"').'</div></div>';
						if ($do_notes->is_truncated) {
							echo '<div id="notepreview',$do_notes->idcontact_note,'">',$note_text,'<br /><a href="#" onclick="showFullNote(',$do_notes->idcontact_note,'); return false;" >',_('more ...'),'</a><br /></div>';
						} else {
							echo $note_text;
						}
						$note_count++;
						
						echo $do_notes->formatDocumentLink().'</div>
						<div id="e'.$do_notes->idcontact_note.'" style="display: none;" class="note_edit_box"></div>
						<div id="'.$do_notes->idcontact_note.'" class="message_box"></div>';
					}
				} else {
					$file = '';
					$preview_note = '';
					if($do_notes->document != ''){
						$doc_name = $do_notes->document;
						$doc_name = str_replace("  ","%20%20",$do_notes->document);
						$doc_name = str_replace(" ","%20",$doc_name);
						
						$file_url = "/files/".$doc_name;
						$file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_notes->document.'</a>';
					}
				// Format the note text
					$note_text = $do_notes->formatNoteDisplayShort();
					//if (substr_count($note_text, '<br />') > 4) {
					//	$preview_note = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$note_text)).' ';
					//} else if (strlen($note_text) > 500) {
					//	$preview_note = substr($note_text, 0, 500).' ';
					//}
					// Formating note ends here
	
     $added_by = $_SESSION['do_User']->getFullName($do_notes->iduser);
					// Priority sort
					$e_PrioritySort = new Event('ContactNotes->eventPrioritySortNotes');
					$e_PrioritySort->addParam('goto', 'contact.php');
					$e_PrioritySort->addParam('idnote', $do_notes->idcontact_note);
					// Priority sort Ends here
     $star_title = ($do_notes->priority > 0) ? 'Unstar this note' : 'Star this note to move it on top';
					$star_img_url = '<img src="/images/'.($do_notes->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="'._($star_title).'" />';
	
					// Generate the delete event
					if (is_object($_SESSION["ContactNotes"])) {
						$e_note_del = new Event("ContactNotes->eventTempDelNoteById");
					}
					$e_note_del->addParam("goto", "contact.php");
					$e_note_del->addParam("id", $do_notes->idcontact_note);
					$e_note_del->addParam("context", "ContactNote");
					$del_img_url = _('delete ').'<img src="/images/delete.gif" width="14px" height="14px" alt="" />';
					//delete event ends here
  
      $do_contact = new Contact();
       //$do_contact->getUserContacts($do_notes->iduser);
       $do_contact -> getContactPictureDetails($do_notes->iduser);
        if($do_contact->getNumRows()){
          //while($do_contact->next()){   
            if($do_contact->picture!=''){
              $contact_picture="/dbimage/thumbnail/".$do_contact->picture;   
            }else{
              $contact_picture="/images/empty_avatar.gif";         
            }            
            $contact_id = $do_contact->idcontact;
          //}
        }
    
      // Start displaying the note
      echo '<div id="notetext', $do_notes->idcontact_note, '" class="vpad10">';      
      echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/note_icon.gif" class="note_icon" width="16" height="16" alt="" />',$e_PrioritySort->getLink($star_img_url, ' title="'._($star_title).'"');
					/*list($yyyy,$mm,$dd) = split("-",$do_notes->date_added);
					if($yyyy < date('Y')) {
					  $added_by = date('l, F j Y', strtotime($do_notes->date_added));
					} else {
					  $added_by = date('l, F j', strtotime($do_notes->date_added)); 

					}*/
                    $added_by = OfuzUtilsi18n::formatDateLong($do_notes->date_added);

					echo '<b>'.$added_by.'</b>&nbsp;('._('Added By :').'&nbsp;'.$do_notes->getNoteOwnerFullName().')</div>
					<div id="trashcan', $note_count, '" class="deletenote" style="right:0;">'.'<a href="#"  onclick="fnEditNote(\'notetext'.$do_notes->idcontact_note.'\','.$do_notes->idcontact_note.');return false;">'._('edit').'</a>&nbsp;|&nbsp;'.$e_note_del->getLink($del_img_url, ' title="'._('Delete this note').'"').'</div></div>';
     echo "<br>";
      echo '<a href="/Contact/'.$contact_id.'"> <img width="34" height="34"alt="" src='.$contact_picture.' class="note_icon"> </a>';
   
					if ($do_notes->is_truncated) {
						echo '<div id="notepreview',$do_notes->idcontact_note,'">',$note_text,'<br /><a href="#" onclick="showFullNote(',$do_notes->idcontact_note,'); return false;" >',_('more ...'),'</a><br /></div>';
					} else {
      //echo "<div style='padding:0px 0px 0px 0px;'>";
          echo "&nbsp;&nbsp;&nbsp;&nbsp;".$note_text;
      //echo "</div>";
					}
					$note_count++;
					
					echo $do_notes->formatDocumentLink().'</div>
					<div id="e'.$do_notes->idcontact_note.'" style="display: none;" class="note_edit_box"></div>
					<div id="'.$do_notes->idcontact_note.'" class="message_box"></div>';
				}
               
                //Display ends here
            }
		$_SESSION['ContactNotes']->getContactNotesCount($idcontact);
        }
        ?>
    
        </div>
        <div id="last_note_loader"></div>
        <div class="spacerblock_20"></div>
        <div class="dottedline"></div>
        <?php $footer_note = 'contact'; include_once('includes/footer_notes.php'); ?>
        <div class="solidline"></div>
    </div></td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
