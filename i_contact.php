<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Contact detail';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/i_header.inc.php');

    if (is_object($_SESSION["eDetail_contact"])) {
        $idcontact = $_SESSION["eDetail_contact"]->getparam("idcontact");
    } elseif (isset($_GET['id'])) {
        $idcontact = $_GET['id'];
    } elseif($do_contact->idcontact){
       $idcontact = $do_contact->idcontact;
    } elseif(is_object($_SESSION['ContactEditSave'])) {
        $idcontact = $_SESSION['ContactEditSave']->idcontact;
    }
 
    $do_contact = new Contact($GLOBALS['conx']);
    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_task_category = new TaskCategory();
    $do_contact_task = new Contact();


    $do_contact->getContactDetails($idcontact);
    // This is not needed because the query() method of the DataObject does that.
    //if ($do_contact->getNumRows()) {
    //    $do_contact->next();
    //}
    $do_contact->setActivity();
    // Set this contact in the session so it can be edited on the contact_edit.php page.
    $do_contact->sessionPersistent("ContactEditSave", "contact.php", 3600);

?>
<?php $thistab = 'Contacts'; include_once('i_ofuz_navtabs.php'); ?>

<script type="text/javascript">
    //<![CDATA[
    var EventKey = 0;
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
    function fnAddTags(){
        var tags = ($("input[name='tags']").val()).split(",");
        $("input[name='tags']").val("");
        var i, len;
        for (i = 0, len = tags.length; i < len; i++) {
            fnAddTag(tags[i]);
        }
    }
    function fnAddTag(tag_name){
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("Tag->eventAjaxAddTagAssociation");
$e_editForm->setEventControler("ajax_evctl.php");
$e_editForm->setSecure(false);
?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "tag_name="+escape(tag_name)+"&idcontact=<?php echo $idcontact; ?>",
            success: function(idtag){
                $("#TagList").append('<span id="Tag'+nexttag+'"><a href="eventcontroler.php?mydb_events[100]=do_Contacts-%3EeventSearchByTag&goto=contacts.php&search_tag_name='+escape(tag_name)+'&mydb_eventkey='+EventKey+'">'+tag_name+'</a><span id="delContactTag'+nexttag+'">&nbsp;<a href="#" onclick="fnDeleteTag('+idtag+',\'Tag'+nexttag+'\'); return false;"><img src="images/delentry.gif" width="14" height="14" alt="Delete this tag" /></a></span>, </span>');
                nexttag++;
            }
        });
    }
    function fnDeleteTag(idtag,iddom){
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("Tag->eventAjaxDeleteTagById");
$e_editForm->setEventControler("ajax_evctl.php");
$e_editForm->setSecure(false);
?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "idtag_delete="+idtag,
            success: function(){
                $("#"+iddom).remove();
            }
        });
    }
    $(document).ready(function() {
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
    	$("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
    });
    //]]>
</script>

<div class="mobile_main main">
  <div class="mainheader">
            <div class="mobile_head_pad5">
                <? //echo $do_contact->idcompany;
                  $e_detail_com = new Event("mydb.gotoPage");
                  if ($_SESSION['do_User']->is_mobile) {
                    $e_detail_com->addParam("goto", "i_company.php");
                  } else {
                    $e_detail_com->addParam("goto", "company.php");
                  }
                  $e_detail_com->addParam("idcompany",$do_contact->idcompany);
                  $e_detail_com->addParam("tablename", "company");
                 // $e_detail_com->requestSave("eDetail_company", $currentpage);
                  $dis = '<a href =" '.$e_detail_com->getUrl().'">'.$do_contact->company.'</a>';
                ?>
                <span class="headline14"><?php echo $do_contact->firstname, ' ', $do_contact->lastname; ?></span>
                <br/><span><?php if (strlen($do_contact->position) > 0) { echo $do_contact->position; ?> at <?php } echo $dis; ?></span>
                                    <br/><?php 
                                        $idtags = $do_contact->getTags();
                                        $do_tag = new Tag();
                                        $e_tag_search = new Event("do_Contacts->eventSearchByTag");
                                        $e_tag_search->addParam("goto", "i_contacts.php");
                                        if (is_array($idtags)) {
                                            echo '<span id="TagList">Tags: ';
                                            $dTagId = 0;
                                            foreach ($idtags as $idtag) {
                                                $do_tag->getId($idtag);
                                                $e_tag_search->addParam("search_tag_name", $do_tag->tag_name);
                                                echo '<span id="Tag',$dTagId,'"><a href="'.$e_tag_search->getUrl().'">'.$do_tag->tag_name.'</a>';
                                                echo '<span id="delContactTag',$dTagId,'" style="display:none;">&nbsp;<a href="#" onclick="fnDeleteTag(',$do_tag->idtag,',\'Tag',$dTagId,'\'); return false;"><img src="images/delentry.gif" width="14" height="14" alt="Delete this tag" /></a></span>, </span>';
                                                $dTagId++;
                                            }
                                          ?></span><br />
                                          <script type="text/javascript">
                                          //<![CDATA[
                                          var nexttag = <?php echo $dTagId ?>;
                                          //]]>
                                          </script>
                                          <form method="get" action="i_contact.php" onsubmit="fnAddTags(); return false;">
                                            <span id="EditTags"><a href="#" onclick="showTagOpt(); return false;" style="color: #666666;"><?php echo (is_array($idtags) ? 'Edit' : 'Add'); ?> tags</a></span><br />
                                            <div id="cont_tag_add" style="display: none;">
                                              <input type="text" name="tags" />&nbsp;<input type="button" name="tagsadd" value="Add" onclick="fnAddTags();" />&nbsp;<a href="#" onclick="hideTagOpt(); return false;">Done</a>
                                            </div>
                                          </form>
                                  <?php }else { 
                                            if (is_object($_SESSION["eDetail_contact"])) {
                                                  $e_tag = new Event("do_Contacts->eventAddTagMultiple");
                                              } elseif(is_object($_SESSION['ContactEditSave'])) {
                                                  $e_tag = new Event("ContactEditSave->eventAddTagMultiple");
                                              }
                                            $e_tag->addEventAction("mydb.gotoPage", 304);
                                            $e_tag->addParam("goto", "i_contact.php");
                                            echo $e_tag->getFormHeader();
                                            echo $e_tag->getFormEvent();
                                      ?>
                                      <a href="#" onclick="showTagOpt()" style="color: #666666;">Add tags</a>
                                          <div id="cont_tag_add" style="display: none;">
                                              <input type="hidden" name="ck[]" value = "<?php echo $idcontact;?>" />
                                              <input type="text" name="tags" />&nbsp;<input type="submit" name="tagsadd" value="Add"/>&nbsp;<a href="#" onclick="hideTagOpt()">Hide</a>
                                           </div>
                                          </form>
                                  <?php } ?>
                                </div>
                </div>
                    <div class="mobile_head_pad5">
                    <?php 
                            // echo '<b>Contact Information:</b><br />';
                             $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
                              if($ContactPhone->getNumRows()){
                              echo "<b>Phone</b><br />";
                              
                              while($ContactPhone->next()){
                                  echo $ContactPhone->phone_type;
                                  echo ': '.$ContactPhone->phone_number;
                                  echo '<br />';
                              }
                            }


                            $ContactEmail = $_SESSION['ContactEditSave']->getChildContactEmail();
                            if($ContactEmail->getNumRows()){
                              echo "<b>Email</b><br />";
                              
                              while($ContactEmail->next()){
                                  echo $ContactEmail->email_type;
                                  //echo ':<a href = "mailto:'.$ContactEmail->email_address.'"> '.$ContactEmail->email_address.'</a>';
                                  echo ':'.$_SESSION['ContactEditSave']->formatTextDisplay($ContactEmail->email_address);
                                  echo '<br />';
                              }
                            }
                             
                             $ContactInstantMessage = $_SESSION['ContactEditSave']->getChildContactInstantMessage();
                             if($ContactInstantMessage->getNumRows()){
                              echo "<b>IM</b><br />";
                              
                              while($ContactInstantMessage->next()){
                                  echo $ContactInstantMessage->im_options;
                                  echo ': '.$ContactInstantMessage->im_type;
                                  echo ': '.$ContactInstantMessage->im_username;
                                  echo '<br />';
                               }
                              }

                             $ContactWebsite = $_SESSION['ContactEditSave']->getChildContactWebsite();
                             if($ContactWebsite->getNumRows()){
                              echo "<b>Website</b><br />";
                              
                               while($ContactWebsite->next()){
                                  echo  $ContactWebsite->website_type;
                                  echo ':'.$_SESSION['ContactEditSave']->formatTextDisplay($ContactWebsite->website);
                                  echo '<br />';
                               }
                              }
                              
                         ?></div>
 <?php $mobile_local_bottom_nav_links = '<div align="right" style="right:3px;" class="navtab"><div class="navtab_text"><a href="i_contact_edit.php">Edit</a></div></div>'; 
 include_once('i_ofuz_logout.php'); ?>
</div>
</body>
</html>