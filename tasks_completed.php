<?php  
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


    include_once('config.php');
    $pageTitle = 'Ofuz :: '._('Tasks Completed');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_task = new Task($GLOBALS['conx']);
    $do_task_completed = new Task();
    $do_task_category = new TaskCategory();
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
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Tasks'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Add a task'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
            <div class="tundra">
            <?php
                $do_task_add = new Task();
                $do_task_add->getTaskAddForm();
             ?>
            </div>
            </div>
        </div>
        <div class="left_menu_footer"></div>
    </td><td class="layout_rcolumn">
        <div class="mainheader pad20">
                                <span class="headline14"><?php echo _('Your completed tasks');?></span>
                                <?php
								if (is_object($GLOBALS['cfg_submenu_placement']['tasks'] ) ) {
									echo  $GLOBALS['cfg_submenu_placement']['tasks']->getMenu();
								}
								?>
         </div>
        <div class="contentfull">
            <?php
                $do_task->getDistinctCompletedTaskDates();
                if ($do_task->getNumRows()) {
                    while($do_task->next()) {
             ?>
        <div class="tasks">
            <div class="headline10"><?php echo $do_task->formated_date; ?></div>
                <?php 
                    $do_task_completed->getAllCompletedTasks($do_task->formated_date);
                    while ($do_task_completed->next()) {
                 ?>
                <span id="t<?php echo $do_task->idtask; ?>" class="task_item">
                    <span class="task_category">
                    <?php 
                        //echo $do_task_category->getTaskCategoryName($do_task_completed->category);
                        echo $do_task_completed->task_category;
                    ?>
                    </span>
                    <span class="task_desc"><a href="#"><?php echo $do_task_completed->task_description; ?></a></span>
                </span><br />
                <?php } ?>
            </div>
            <?php } } ?>
            <div class="dottedline"></div>
            <?php $footer_note = 'dropboxtask'; include_once('includes/footer_notes.php'); ?>
        </div>
    </td></tr></table>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
