<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


    /**
	 * tasks.php
	 * Display the tasks of a user group by time.
	 * It uses the object: Task
	 * Copyright 2001 - 2010 All rights reserved SQLFusion LLC, info@sqlfusion.com 
	 * Authors: Philippe Lewicki, Abhik Chakraborty, Jay Link, Ravi Rokkam  
	 */

    $pageTitle = 'Ofuz :: Tasks';
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
	<?php include_once('includes/ofuz_js.inc.php'); ?>
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

    function showAllTasksLater(){

        $.ajax({
            type: "GET",
            <?php
            $e_task_later = new Event("Task->eventAjaxGetAllTasksLater");
            $e_task_later->setEventControler("ajax_evctl.php");
            $e_task_later->setSecure(false);
            ?>
            url: "<?php echo $e_task_later->getUrl(); ?>",
            data: "",
            success: function(tasks_later){
                $("#tasks_later")[0].innerHTML = tasks_later;
				$("#tasks_options").hide();
            }
        });

    }

    function showAllTasksThisMonth(){

        $.ajax({
            type: "GET",
            <?php
            $e_task_thismonth = new Event("Task->eventAjaxGetAllTasksThisMonth");
            $e_task_thismonth->setEventControler("ajax_evctl.php");
            $e_task_thismonth->setSecure(false);
            ?>
            url: "<?php echo $e_task_thismonth->getUrl(); ?>",
            data: "",
            success: function(tasks_this_month){
                $("#tasks_thismonth")[0].innerHTML = tasks_this_month;
				$("#tasks_options_this_month").hide();
            }
        });

    }

    function showAllTasksOverdue(){

        $.ajax({
            type: "GET",
            <?php
            $e_task_overdue = new Event("Task->eventAjaxGetAllTasksOverdue");
            $e_task_overdue->setEventControler("ajax_evctl.php");
            $e_task_overdue->setSecure(false);
            ?>
            url: "<?php echo $e_task_overdue->getUrl(); ?>",
            data: "",
            success: function(tasks_overdue){
                $("#tasks_overdue")[0].innerHTML = tasks_overdue;
				$("#tasks_options_overdue").hide();
            }
        });

    }


    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Tasks'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
     <table class="layout_columns">
      <tr>
	<td class="layout_lcolumn">
	  <?php
	  include_once('plugin_block.php');
	  ?>
	</td>
	<td class="layout_rcolumn">
	  <?php
            $msg = new Message(); 
	    if ($msg->getMessageFromContext("tasks")) {
		    echo $msg->displayMessage();
	    }
          ?>
        <!--<div class="tasktop">-->
        
         <div class="mainheader pad20">
            <span class="headline14">Your tasks</span>
            <?php
                  if($_GET['t'] == 't'){$_SESSION['remember_task_on'] = '';}
                  if($_GET['t']=='p' || $_SESSION['remember_task_on']== 'Project'){
                        $link_html = '<a href=/tasks.php?t=t>'._('Tasks By Date').'</a><span class="headerlinksI">|</span>';
                  }else{
                        $link_html = '<a href=/tasks.php?t=p>'._('Task By Projects').'</a><span class="headerlinksI">|</span>';
                  }
            ?>
            <span class="headerlinks"><?php echo $link_html;?><?php echo _('Upcoming');?><span class="headerlinksI">|</span><a href="tasks_completed.php"><?php echo _('Completed');?></a></span>
            <!--<span class="headerlinksI">|</span><a href="ofuz_demo_tasks3.php">Assigned</a></span>//-->
        </div>
        <div class="contentfull">
          <?php
           if($_GET['t']=='p' || $_SESSION['remember_task_on']== 'Project'){  // Starts the first if
                    $_SESSION['remember_task_on'] = 'Project';
                    $do_task->getAllTaskProjectRelated();
                    $last_project = 0;
                    echo '<div class="tasks">';
                    while($do_task->next()){
                          if($last_project != $do_task->idproject ){
                              //echo '<br />';
                              echo '<div ><br /><b><a href="/Project/'.$do_task->idproject.'">'.$do_task->name.'</a></b></div>';
                          }
                          $last_project = $do_task->idproject;
                          echo '<br /><span class="task_item">&nbsp;&nbsp;';
                          if ($do_task->task_category != '') { 
                              echo  '<span class="task_category">'.$do_task->task_category.'</span>&nbsp;'; 
                          }
                          echo '<span class="task_desc"><a href="/Task/'.$do_task->idproject_task.'">'.$do_task->task_description.'</a>';
                          echo '</span></span>';
                          //echo '<div></div>';
                          //echo '<span></span>';
                    }
                    echo '</div>';
            }else{
                  $_SESSION['remember_task_on'] = '';
            ?>
            

            <?php
				$num_tasks_overdue = $do_task->getNumAllTasksOverdue();
                $do_task->getAllTasksOverdue();
				$num_tasks_overdue_limit = $do_task->getNumRows();
                if ($do_task->getNumRows()) {
             ?>
            <div class="tasks">
                <div class="headline10" style="color: #ff0000;"><?php echo _('Overdue');?></div>
				<div id="tasks_overdue"><?php echo $do_task->viewTasks(); ?></div>
			<?php if($num_tasks_overdue > 10) { ?>
				<div id="tasks_options_overdue"><a href="#" onclick="showAllTasksOverdue(); return false;"><?php echo _('More...');?></a></div>
			<?php } ?>
            </div>
            <?php  
                }
                $do_task->getAllTasksToday();
                if ($do_task->getNumRows()) {
             ?>
            <div class="tasks_today">
                <div class="headline10">Today</div>
                <?php echo $do_task->viewTasks(); ?>
            </div>
            <?php 
                }
                $do_task->getAllTasksTomorrow();
                if ($do_task->getNumRows()) {
             ?>
            <div class="tasks">
                <div class="headline10">Tomorrow</div>
                <?php echo $do_task->viewTasks(); ?>
            </div>
            <?php
                }
                $do_task->getAllTasksThisWeek();
                if ($do_task->getNumRows()) {
             ?>
            <div class="tasks">
                <div class="headline10">This week</div>
                <?php echo $do_task->viewTasks(); ?>
            </div>
            <?php 
                }
                $do_task->getAllTasksNextWeek();
                if ($do_task->getNumRows()) {
             ?>
            <div class="tasks">
                <div class="headline10">Next week</div>
                <?php echo $do_task->viewTasks(); ?>
            </div>
            <?php 
                }
				$num_tasks_this_month = $do_task->getNumAllTasksThisMonth();
                $do_task->getAllTasksThisMonth();
				$num_tasks_this_month_limit = $do_task->getNumRows();
                if ($do_task->getNumRows()) {
			?>
            <div class="tasks">
                <div class="headline10"><?php echo _('This Month');?></div>
                <div id="tasks_thismonth"><?php echo $do_task->viewTasks(); ?></div>
            </div>
				<?php if($num_tasks_this_month > 20) { ?>
				<div id="tasks_options_this_month"><a href="#" onclick="showAllTasksThisMonth(); return false;"><?php echo _('More...')?></a></div>
				<?php } ?>
            <?php 
                }

				$num_tasks_later = $do_task->getNumAllTasksLater();
                $do_task->getAllTasksLater();
				$num_twenty_tasks = $do_task->getNumRows();
                if ($do_task->getNumRows()) {
             ?>
            <div class="tasks">
                <div class="headline10">Later</div>
                <div id="tasks_later"><?php echo $do_task->viewTasks(); ?></div>
            </div>
				<?php if($num_tasks_later > 20) { ?>
				<div id="tasks_options"><a href="#" onclick="showAllTasksLater(); return false;"><?php echo _('More...');?></a></div>
				<?php } ?>
            <?php } 
            }// Ends the First If
          ?>
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