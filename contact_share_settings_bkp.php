<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Share Notes and Files';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    //$do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    //$do_company = new Company($GLOBALS['conx']);
    //$do_task = new Task($GLOBALS['conx']);
    //$do_task_category = new TaskCategory($GLOBALS['conx']);
    //$do_contact_task = new Contact();

    if (is_object($_SESSION["eDetail_contact"])) {
        $idcontact = $_SESSION["eDetail_contact"]->getparam("idcontact");
    } elseif (isset($_GET['id'])) {
        $idcontact = $_GET['id'];
    } elseif($do_contact->idcontact){
       $idcontact = $do_contact->idcontact;
    } elseif(is_object($_SESSION['ContactEditSave'])) {
        $idcontact = $_SESSION['ContactEditSave']->idcontact;
    }
    $do_contact->getContactDetails($idcontact);

    if (!is_object($_SESSION['do_sharenotes'])) {
      $do_contact_notes = new ContactNotes();
      $do_contact_notes->sessionPersistent("do_sharenotes", "index.php", 36000);
    }
//echo $_SESSION['do_sharenotes'];
?>
<?php $thistab = 'Welcome'; include_once('ofuz_navtabs.php'); ?>
<div class="content">
    <script type="text/javascript">
    //<![CDATA[
    var openform;
    function fnEditTask(task) {
        if ($("#e"+openform).length > 0) fnCancelEdit(openform);
        openform = task;
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("Task->eventAjaxEditTaskForm");
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
    function fnCancelEdit(task) {
        $("#e"+task).hide(0);
        $("#e"+task)[0].innerHTML = "";
        $("#t"+task).show(0);
    };
    function fnTaskComplete(task) {
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
    $(document).ready(function() {
    	$("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
    });
    //]]>
</script>
<div class="content">
    <table class="main">
        <tr>
            <td class="main_left">
                <!--<div class="col_pad_25">
                   <div class="sidebox1a"></div>
                </div>-->
            </td>
            <td class="main_right">
                <div class="mainheader">
                    <div class="pad20">
                        <span class="headline14">Share files and Notes</span>
                    </div>
                </div>
                <div class="contentfull">
                    Description comes here.......
                    <div class="dottedline"></div>
                          <?php
                          //checks if already Notes Shared (Url link is generated)
                          $portal_code = $_SESSION['do_sharenotes']->checkIfNotesShared($idcontact);
                          //generating an Event to create an unique URL link to Share Notes & Files
                          $e_generate_url = new Event("do_sharenotes->generateUrlToShareNotes");
                          $e_generate_url->addParam("idcontact",$idcontact);
                          $e_generate_url->addParam("goto", $_SERVER['PHP_SELF']);
                          //generating an Event to create an new unique URL link to Share Notes & Files
                          $e_generate_new_url = new Event("do_sharenotes->generateNewUrlToShareNotes");
                          $e_generate_new_url->addParam("idcontact", $idcontact);
                          $e_generate_new_url->addParam("goto", $_SERVER['PHP_SELF']);
                          //generating an Event to stop sharing the Notes & Files
                          $e_stop_sharing_notes = new Event("do_sharenotes->stopSharingNotes");
                          $e_stop_sharing_notes->addParam("idcontact", $idcontact);
                          $e_stop_sharing_notes->addParam("goto", $_SERVER['PHP_SELF']);

                          if($portal_code){
                          ?>
                    <div class="section">

                       <div>You have already shared Notes with <?php echo "<b>".$do_contact->firstname." ".$do_contact->lastname."</b>"; ?></div>
                       <div id="share_file_notes_url">   <b>contact portal</b> : 
                          <?php echo "<a href='http://dev2.sqlfusion.com/ofuz3/contact_portal.php?pc=".$portal_code."'>http://dev2.sqlfusion.com/ofuz3/contact_portal.php?pc=".$portal_code."</a>"; ?>
                       </div>
                      <div id="share_file_notes_generate">
                          <?php echo $e_generate_url->getLink("resend access to my contact"); ?> : <?php echo $e_generate_new_url->getLink("Generate a new access"); ?> : <?php echo $e_stop_sharing_notes->getLink("Stop sharing"); ?>
                      </div>
                    </div>
                          <?php
                          } else{
                          ?>
                    <div class="section">
                        <?php
                            /*$e_generateUrl = new Event("do_sharenotes->generateUrlToShareNotes");
                            $e_generateUrl->addParam("idcontact",$idcontact);
                            $e_generateUrl->addParam("goto", $_SERVER['PHP_SELF']);*/

                        ?>
                       <div id="sharenotes" class="text8">
                          <?php echo $e_generate_url->getLink("Share Notes & Files with ")."<b>".$do_contact->firstname." ".$do_contact->lastname."</b>"; ?>
                       </div>
                    </div>
                            <?php } ?>
                    <div id="share_file_notes_msg"><?php if($_GET['msg']){ echo htmlentities($_GET['msg']);} ?></div>
                    <div class="bottompad40"></div>
                </div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>