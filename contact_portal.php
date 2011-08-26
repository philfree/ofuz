<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 

    $pageTitle = 'Ofuz :: Contact Portal';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    //include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    if ($_GET['pc']) {
        $obj = new Contact();
        $idcontact = $obj->getContactIdByPortal($_GET['pc']);
        if (!$idcontact) { echo "The shared access as been stopped."; exit;  }
        $obj->sessionPersistent("do_contact", "index.php", OFUZ_TTL);
    } elseif (isset($_SESSION['portal_idcontact'])) {
        $obj = new Contact();
        $idcontact = $_SESSION['portal_idcontact'];
        $obj->getContactDetails($idcontact);
        $obj->sessionPersistent("do_contact", "index.php", OFUZ_TTL);
    }
	//print_r($_SESSION['do_contact']);
    if (!is_object($_SESSION['do_contact'])) {
      exit;
      //$do_contact = new Contact();
      //$do_contact->sessionPersistent("do_contact", "index.php", 36000);
    }
    $ContactNoteExpend  = new ContactNotes($GLOBALS['conx']);
    $ContactNoteExpend->sessionPersistent("ContactNoteExpend", "contacts.php", OFUZ_TTL);

    
    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_user = $_SESSION['do_contact']->getParentUser();
    $do_user->sessionPersistent("portalUser", "contact_portal.php", OFUZ_TTL_SHORT);
	
    $_SESSION['portal_idcontact'] = $idcontact;

	if($_SESSION['do_User']->iduser !='' && !empty($_SESSION['do_User']->iduser)){
		$iduser_for_feed = $_SESSION['do_User']->iduser;
		$added_by_contact = 'No'; 
	}else{ 
		$iduser_for_feed =  $_SESSION['do_contact']->iduser; 
		$added_by_contact = 'Yes';
	}
	//echo $iduser_for_feed;
?>
<script type="text/javascript">
    //<![CDATA[
    function showOpt(){
        $("#more_options").hide(0);
        $("#notes_options").show("fast");
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
    $(document).ready(function() {
    	$("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
    });
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
    <div class="layout_header">
        <div class="layout_logo">
            <a href="/index.php"><img src="/images/ofuz_logo.jpg" width="188" height="90" alt="" /></a>
        </div>
    </div>
    <div class="contact_portal_name"><?php echo $_SESSION['do_contact']->firstname, ' ', $_SESSION['do_contact']->lastname; ?></div>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Welcome to the ').$do_user->company._(' portal'); ?></span>
        </div>
    </div>
    <div class="contentfull">
	    <div class="vpad10">
            <?php 
				$contact_message = new ContactMessage();
				$personalized_message = $contact_message->getPersonalizedMessage($idcontact,$iduser_for_feed);
				if($personalized_message) {
					echo $contact_message->displayPersonalizedMessage($personalized_message);
				} else {
					$msg = new Message();
					$msg->setData(Array("user_firstname" => $do_user->firstname, 
										"user_lastname" => $do_user->lastname,
										"user_company" => $do_user->company));
					$msg->getMessage("welcome client portal");
					$msg->setCanClose("no");
					echo $msg->displayMessage();
				}
            ?>
	    </div>
        <div class="grayline2"></div>
        <div class="headline_fuscia"><?php echo _('Add a Note or File'); ?></div>
        <div class="percent95">
        <?php
            $ContactNotes  = new ContactNotes($GLOBALS['conx']);
            $ContactNotes->sessionPersistent("ContactNoteEditSave", "contacts.php", 300);
            $_SESSION['ContactNoteEditSave']->idcontact = $idcontact;
	        //$_SESSION['ContactNoteEditSave']->iduser =  $do_user->iduser; 		
            $e_addContactNote = $_SESSION['ContactNoteEditSave']->newForm("ContactNoteEditSave->eventAdd");
            $e_addContactNote->setLevel(123);
            $e_addContactNote->setGotFile(true);
            $e_addContactNote->addEventAction("mydb.gotoPage", 90);
            //$e_addContactNote->addEventAction("ContactNoteEditSave->eventFormatNoteInsert", 119);
			$e_addContactNote->addEventAction("ContactNoteEditSave->eventHTMLCleanUp", 119);
			$e_addContactNote->addEventAction("portalUser->eventSendPortalAlert", 238);
			$e_addContactNote->addEventAction("WorkFeedContactNotePortal->eventAddFeed", 245);
			$e_addContactNote->addParam("iduser_for_feed",$iduser_for_feed);
			$e_addContactNote->addParam("added_by_cont",$added_by_contact);
			
            $e_addContactNote->addParam('goto', $_SERVER['PHP_SELF']);
            $e_addContactNote->addParam('errPage', $_SERVER['PHP_SELF']);
            //$noteFields = new FieldsForm("ofuz_add_contact_note");
			if($_SESSION['do_User']->iduser != ''  && !empty($_SESSION['do_User']->iduser)) {
				$noteFields = new FieldsForm("ofuz_add_contact_note_portal_signin_user");
			} else {
				$noteFields = new FieldsForm("ofuz_add_contact_note_portal");
			}
			//$noteFields = new FieldsForm("ofuz_add_contact_note_portal");
            $noteFields->setValues(Array('iduser' => '', 'date_added'=>date("Y-m-d")));

            // form headers:
            echo $e_addContactNote->getFormHeader();
            echo $e_addContactNote->getFormEvent();

            // display the note text field:
            //echo $noteFields->idcontact;
            //echo $noteFields->date_added;
			
            echo $noteFields->note;
			if($_SESSION['do_User']->iduser != ''  && !empty($_SESSION['do_User']->iduser)) {
            	echo $noteFields->iduser;
			}
			echo _('Attach a file: ').$noteFields->document; 
			
         ?>
       <!-- <span id="more_options"><a href="#" onclick="showOpt(); return false;">More Options</a></span>-->
        <div class="div_right">
            <div id="notes_options" style="display:none;">
                <br/>
                <?php echo _('When this happened: ');?><?php echo $noteFields->date_added; ?>

            </div>
        </div>
        <div class="div_right">
			<input type="hidden" name="fields[note_visibility]" value="user coworker contact" />
            <?php echo $e_addContactNote->getFormFooter(_('Add this note')); ?>
        </div>
        <?php
        $do_notes->getContactNotes($_SESSION['do_contact']->idcontact);
        if ($do_notes->getNumRows()) {
            echo '<div class="headline_fuscia">', $do_contact->firstname, '\'s',_(' Notes:'),'</div>', "\n";
            $note_count = 0;
            while ($do_notes->next()) {

				if($_SESSION['do_User']->iduser !='' && !empty($_SESSION['do_User']->iduser)){
					if($do_notes->iduser != $_SESSION['do_User']->iduser) {
						if($do_notes->note_visibility == 'user coworker' || $do_notes->note_visibility == 'user coworker contact') {
							$file = '';
							$preview_note = '';
							if($do_notes->document != ''){
								$doc_name = $do_notes->document;
								$doc_name = str_replace("  ","%20%20",$do_notes->document);
								$doc_name = str_replace(" ","%20",$doc_name);
								//$file_url = "/files/".$do_notes->document;
								$file_url = "/files/".$doc_name;
								$file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_notes->document.'</a>';
							}
							//$note_text = $do_notes->note;
							$note_text = $do_notes->formatNoteDisplayFull();
							if (substr_count($note_text, '<br />') > 4) {
								$preview_note = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$note_text)).' ...';
							}
							// else if (strlen($note_text) > 500) {
							//    $preview_note = substr($note_text, 0, 500).' ...';
							//}
							//$added_by = $do_user->getFullName($do_notes->iduser);
							$e_PrioritySort = new Event('ContactNotes->eventPrioritySortNotes');
							$e_PrioritySort->addParam('goto', $_SERVER['PHP_SELF']);
							$e_PrioritySort->addParam('idnote', $do_notes->idcontact_note);
							$star_img_url = '<img src="/images/'.($do_notes->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="" />';
							//if (is_object($_SESSION["ContactNotesEditSave"])) {
							//    $e_note_del = new Event("ContactNotes->eventdelContactNoteById");
							//}
							//$e_note_del->addParam("goto", $_SERVER['PHP_SELF']);
							//$e_note_del->addParam("id", $do_notes->idcontact_note);
							//$del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
							echo '<div id="notetext', $note_count, '" class="vpad10">';
							echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/note_icon.gif" class="note_icon" width="16" height="16" alt="" />',$e_PrioritySort->getLink($star_img_url);
							/*list($yyyy,$mm,$dd) = split("-",$do_notes->date_added);
							if($yyyy < date('Y')) {
							  $added_by = date('l, F j Y', strtotime($do_notes->date_added));
							} else {
							  $added_by = date('l, F j', strtotime($do_notes->date_added)); 
							}*/
                            $added_by = OfuzUtilsi18n::formatDateLong($do_notes->date_added);
							echo '<b>'.$added_by.'</b>&nbsp;('._('Added By :').'&nbsp;'.$do_notes->getNoteOwnerFullName().')</div> </div>';
							//if ($preview_note != '') {
							//    echo '<div id="notepreview',$do_notes->idcontact_note,'">',nl2br(stripslashes($preview_note)),'<br /><a href="#" onclick="showFullNote(',$do_notes->idcontact_note,'); return false;" />more ...</a><br /></div>';
							//} else {
								echo $note_text;
							//}
							echo $do_notes->formatDocumentLink().'</div>';
						}
					} else {
							$file = '';
							$preview_note = '';
							if($do_notes->document != ''){
								$doc_name = $do_notes->document;
								$doc_name = str_replace("  ","%20%20",$do_notes->document);
								$doc_name = str_replace(" ","%20",$doc_name);
								//$file_url = "/files/".$do_notes->document;
								$file_url = "/files/".$doc_name;
								$file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_notes->document.'</a>';
							}
							//$note_text = $do_notes->note;
							$note_text = $do_notes->formatNoteDisplayFull();
							if (substr_count($note_text, '<br />') > 4) {
								$preview_note = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$note_text)).' ...';
							}
							// else if (strlen($note_text) > 500) {
							//    $preview_note = substr($note_text, 0, 500).' ...';
							//}
							//$added_by = $do_user->getFullName($do_notes->iduser);
							$e_PrioritySort = new Event('ContactNotes->eventPrioritySortNotes');
							$e_PrioritySort->addParam('goto', $_SERVER['PHP_SELF']);
							$e_PrioritySort->addParam('idnote', $do_notes->idcontact_note);
							$star_img_url = '<img src="/images/'.($do_notes->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="" />';
							//if (is_object($_SESSION["ContactNotesEditSave"])) {
							//    $e_note_del = new Event("ContactNotes->eventdelContactNoteById");
							//}
							//$e_note_del->addParam("goto", $_SERVER['PHP_SELF']);
							//$e_note_del->addParam("id", $do_notes->idcontact_note);
							//$del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
							echo '<div id="notetext', $note_count, '" class="vpad10">';
							echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/note_icon.gif" class="note_icon" width="16" height="16" alt="" />',$e_PrioritySort->getLink($star_img_url);
							/*list($yyyy,$mm,$dd) = split("-",$do_notes->date_added);
							if($yyyy < date('Y')) {
							  $added_by = date('l, F j Y', strtotime($do_notes->date_added));
							} else {
							  $added_by = date('l, F j', strtotime($do_notes->date_added)); 
							}*/
                            $added_by = OfuzUtilsi18n::formatDateLong($do_notes->date_added);
							echo '<b>'.$added_by.'</b>&nbsp;('._('Added By :').'&nbsp;'.$do_notes->getNoteOwnerFullName().')</div> </div>';
							//if ($preview_note != '') {
							//    echo '<div id="notepreview',$do_notes->idcontact_note,'">',nl2br(stripslashes($preview_note)),'<br /><a href="#" onclick="showFullNote(',$do_notes->idcontact_note,'); return false;" />more ...</a><br /></div>';
							//} else {
								echo $note_text;
							//}
							echo $do_notes->formatDocumentLink().'</div>';
					}

				} else {
					if($do_notes->note_visibility == 'user contact' || $do_notes->note_visibility == 'user coworker contact') {
						$file = '';
						$preview_note = '';
						if($do_notes->document != ''){
							$doc_name = $do_notes->document;
							$doc_name = str_replace("  ","%20%20",$do_notes->document);
							$doc_name = str_replace(" ","%20",$doc_name);
							//$file_url = "/files/".$do_notes->document;
							$file_url = "/files/".$doc_name;
							$file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_notes->document.'</a>';
						}
						//$note_text = $do_notes->note;
						$note_text = $do_notes->formatNoteDisplay();
						if (substr_count($note_text, '<br />') > 4) {
							$preview_note = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$note_text)).' ...';
						}
						// else if (strlen($note_text) > 500) {
						//    $preview_note = substr($note_text, 0, 500).' ...';
						//}
						//$added_by = $do_user->getFullName($do_notes->iduser);
						$e_PrioritySort = new Event('ContactNotes->eventPrioritySortNotes');
						$e_PrioritySort->addParam('goto', $_SERVER['PHP_SELF']);
						$e_PrioritySort->addParam('idnote', $do_notes->idcontact_note);
						$star_img_url = '<img src="/images/'.($do_notes->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="" />';
						//if (is_object($_SESSION["ContactNotesEditSave"])) {
						//    $e_note_del = new Event("ContactNotes->eventdelContactNoteById");
						//}
						//$e_note_del->addParam("goto", $_SERVER['PHP_SELF']);
						//$e_note_del->addParam("id", $do_notes->idcontact_note);
						//$del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
						echo '<div id="notetext', $note_count, '" class="vpad10">';
						echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/note_icon.gif" class="note_icon" width="16" height="16" alt="" />',$e_PrioritySort->getLink($star_img_url);
						list($yyyy,$mm,$dd) = split("-",$do_notes->date_added);
						if($yyyy < date('Y')) {
						  $added_by = date('l, F j Y', strtotime($do_notes->date_added));
						} else {
						  $added_by = date('l, F j', strtotime($do_notes->date_added)); 
						}
						echo '<b>'.$added_by.'</b>&nbsp;('._('Added By').' :&nbsp;'.$do_notes->getNoteOwnerFullName().')</div> </div>';
						//if ($preview_note != '') {
						//    echo '<div id="notepreview',$do_notes->idcontact_note,'">',nl2br(stripslashes($preview_note)),'<br /><a href="#" onclick="showFullNote(',$do_notes->idcontact_note,'); return false;" />more ...</a><br /></div>';
						//} else {
							echo $note_text;
						//}
						echo $do_notes->formatDocumentLink().'</div>';
					}
				}

            }
        }
        ?>
        <div class="dottedline"></div>
    </div>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
