<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Task Categories';
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
    function fnEditTaskCategory(task) {
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("TaskCategory->eventAjaxEditTaskCategoryForm");
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
        $("#t"+task).show(0);
    };
    function fnDeleteTaskCategory(task) {
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("TaskCategory->eventAjaxTaskCategoryDelete");
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
    //]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Tasks'; include_once('includes/ofuz_navtabs.php'); ?>
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
                $do_task_category_add = new TaskCategory();
                $do_task_category_add->getTaskCategoryAddForm();
             ?>
            </div>
            </div>
        </div>
        <div class="left_menu_footer"></div>
    </td><td class="layout_rcolumn">
        <div class="tasktop">
            <span class="headline11"><?php echo _('Your Task Categories');?></span>
            <span class="headerlinks"><?php echo _('Upcoming')?><span class="headerlinksI">|</span><a href="/tasks_completed.php"><?php echo _('Completed')?></a></span>
        </div>
        <div class="contentfull">
            <div class="task">
                <span id="t" class="task_item">
                  <?php
                        $do_task_category->getUsersTaskCategory();
                        if($do_task_category->getNumRows()){
                          while($do_task_category->next()){ ?>
                              <span id="t<?php echo $do_task_category->idtask_category; ?>" class="task_item">
                              <input type="checkbox" name="c<?php echo $do_task_category->idtask_category; ?>" class="task_checkbox" onclick="fnDeleteTaskCategory('<?php echo $do_task_category->idtask_category; ?>')" />
                              <span class="task_desc"><a href="#" onclick="fnEditTaskCategory('<?php echo  $do_task_category->idtask_category; ?>')"><?php echo $do_task_category->name; ?></a>
                             </span>
                              </span>
                              <div id="e<?php echo $do_task_category->idtask_category; ?>" style="display: none;"></div>
                              <span id="b<?php echo $do_task_category->idtask_category; ?>"><br /></span>
                          <?php }
                        }else{
                        }
                  ?>
                </span>
            </div>
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