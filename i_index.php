<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    $do_notes->sessionPersistent("ContactNotesEditSave", "index.php", 3600);
  
?>
<?php $thistab = 'Welcome'; include_once('i_ofuz_navtabs.php'); ?>
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
</script>
<div class="content">
    <table class="main">
        <tr>
            <td class="i_main_right" >
                <div class="mainheader">
                    <div class="pad20">
                        <span class="headline14">Welcome</span>
                    </div>
                </div>
                <div class="contentfull">
                    Welcome back <?php 
                       // print_r($_SESSION['do_User']);
                         echo $_SESSION['do_User']->firstname; 
                        
                        $do_task->getAllTasksOverdue();
                        if ($do_task->getNumRows()) {
                    ?>
                    <div class="task">
                        <div class="headline10" style="color: #ff0000;">Your Overdue taks</div>
                        <?php
                            while ($do_task->next()) {
                                $category = $do_task_category->getTaskCategoryName($do_task->category);
                        ?>
                        <span id="t<?php echo $do_task->idtask; ?>" class="task_item">
                            <input type="checkbox" name="c<?php echo $do_task->idtask; ?>" class="task_checkbox" onclick="fnTaskComplete('<?php echo $do_task->idtask; ?>')" />
                            <?php if ($category != '') { ?><span class="task_category"><?php echo $category; ?></span><?php } ?>
                            <span class="task_desc"><a href="#" onclick="fnEditTask('<?php echo $do_task->idtask; ?>'); return false;"><?php echo $do_task->task_description; ?></a>
                            <?php 
                                if ($do_task->idcontact) {
                                    if($do_task->from_note){
                                      echo $do_contact_task->getContactNameTaskRelated($do_task->idcontact);
                                    }else{
                                      echo $do_contact_task->getContactNameContactRelatedTask($do_task->idcontact);
                                    }
                                }
                            ?>
                            </span>
                        </span>
                        <div id="e<?php echo $do_task->idtask; ?>" style="display: none;"></div>
                        <span id="b<?php echo $do_task->idtask; ?>"><br /></span>
                        <?php } ?>
                        </div>
                    <?php  
                       }
                        $do_task->getAllTasksToday();
                        if ($do_task->getNumRows()) {
                    ?>
                    <div class="task_today">
                        <div class="headline10">Your Today's task</div>
                        <?php
                            while ($do_task->next()) {
                                $category = $do_task_category->getTaskCategoryName($do_task->category);
                        ?>
                        <span id="t<?php echo $do_task->idtask; ?>" class="task_item">
                            <input type="checkbox" name="c<?php echo $do_task->idtask; ?>" class="task_checkbox" onclick="fnTaskComplete('<?php echo $do_task->idtask; ?>')" />
                            <?php if ($category != '') { ?><span class="task_category"><?php echo $category; ?></span><?php } ?>
                            <span class="task_desc"><a href="#" onclick="fnEditTask('<?php echo $do_task->idtask; ?>'); return false;"><?php echo $do_task->task_description; ?></a>
                            <?php 
                                if ($do_task->idcontact) {
                                    if($do_task->from_note){
                                      echo $do_contact_task->getContactNameTaskRelated($do_task->idcontact);
                                    }else{
                                      echo $do_contact_task->getContactNameContactRelatedTask($do_task->idcontact);
                                    }
                                }
                            ?>
                            </span>
                        </span>
                        <div id="e<?php echo $do_task->idtask; ?>" style="display: none;"></div>
                        <span id="b<?php echo $do_task->idtask; ?>"><br /></span>
                        <?php } ?>
                    </div>
                    <?php 
                        } ?>
                    <div class="dottedline"></div>
                    <?php $footer_note = 'emailstream'; include_once('includes/footer_notes.php'); ?>

                    <!-- Add ofuz to Browser Search option begins -->
                    
                    <script src="browser_search/browser_detect.js" type="text/javascript"></script>
                    
                    <div id="xyz" style='text-align:center;cursor:pointer'>
                    <script src="browser_search/browser_functions.js" type="text/javascript"></script>
                    </div>
                    
                    <script src="browser_search/urchin.js" type="text/javascript"></script>
                    <script type="text/javascript">
                            _uacct = "UA-58643-5";
                            urchinTracker();
                    </script>
                    <!-- Add ofuz to Browser Search option ends -->
                    <div class="bottompad40"></div>
                </div>
            </td>
        </tr>
    </table>
</div>
 

</body>
</html>