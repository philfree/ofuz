<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Tasks';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/header.inc.php');
    //include_once('class/Task.class.php');
    $do_task = new Task($GLOBALS['conx']);
    $do_task_completed = new Task();
    $do_task_category = new TaskCategory();
?>
<?php $thistab = 'Tasks'; include_once('i_ofuz_navtabs.php'); ?>
<div class="content">
    <table class="main">
        <tr>
            <td class="main_right">
                <div class="content730">
                    <?php
                        $do_task->getDistinctCompletedTaskDates();
                        if ($do_task->getNumRows()) {
                            while($do_task->next()) {
                    ?>
                        <div class="task">
                        <div class="headline10"><?php echo $do_task->formated_date; ?></div>
                        <?php 
                            $do_task_completed->getAllCompletedTasks($do_task->formated_date);
                            while ($do_task_completed->next()) {
                        ?>
                            <span id="t<?php echo $do_task->idtask; ?>" class="task_item">
                                <span class="task_category"><?php echo $do_task_category->getTaskCategoryName($do_task_completed->category);?></span>
                                <span class="task_desc"><a href="#"><?php echo $do_task_completed->task_description; ?></a></span>
                            </span><br />
                        <?php } ?>
                        </div>
                    <?php } } ?>
                    <div class="dottedline"></div>
                    <?php $footer_note = 'dropboxtask'; include_once('includes/footer_notes.php'); ?>
                    <div class="bottompad40"></div>
                </div>
            </td>
        </tr>
    </table>
<?php include_once('i_ofuz_logout.php'); ?>
</div>
</body>
</html>