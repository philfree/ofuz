<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Drop Box Task';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory();
    $do_contact_task = new Contact();
?>
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

    function showDateOpt(){
        document.getElementById('due_sp_date').style.display = "block";
        document.getElementById('when_due').style.display = "none";
        document.getElementById('sp_date_selected').value = "Yes";
    }
     function hideDateOpt(){
        document.getElementById('due_sp_date').style.display = "none";
        document.getElementById('when_due').style.display = "block";
        document.getElementById('sp_date_selected').value = "";
    }
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Tasks'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
<div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
                $do_ofuz_ui = new OfuzUserInterface();
                $do_ofuz_ui->generateLeftMenuSettings('Drop Box Task');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Drop Box Task Setup'); ?></div>
        <div class="contentfull">
         <div class="messageshadow">
            <div class="messages">
            <?php
                $msg = new Message();
                echo $msg->getMessage('drop box task');
            ?>
            </div>
        </div>
        <div class="instruction_copy_past">
         <?php 
                 if($_SESSION['do_User']->drop_box_code){
                    ///echo _('Use the following Email Id to create a Task :').'<br />';
                    $emailid = 'addtask-'.$_SESSION['do_User']->drop_box_code.'@ofuz.net';
                    echo '<a href = "mailto:'.$emailid.'">'.$emailid.'</a>';
                 }else{

                    $e_gen_dropboxid = new Event("do_User->eventGenerateDropBoxId");
                    $e_gen_dropboxid->addParam("goto", "drop_box_note.php");

                    echo '<br />Dear '.$_SESSION['do_User']->firstname. ',';
                    echo '<br />'._('You do not have a drop box email id yet');
                    echo '<br />'._('Generate one by clicking ').$e_gen_dropboxid->getLink("here");
                 }
             ?>
         </div>
        </div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>