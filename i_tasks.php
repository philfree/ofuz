<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Tasks';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/i_header.inc.php');
    //include_once('class/Task.class.php');
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory();
    $do_contact_task = new Contact();
?>
<?php $thistab = 'Tasks'; include_once('includes/i_ofuz_navtabs.php'); ?>
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
                $("#e"+task).slideDown('normal');
            }
        });
    };
    function fnCancelEdit(task) {
        $("#e"+task).slideUp('fast');
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
<!--
    <table class="main">
        <tr>
            <td class="main_right">-->
<div  style="position:absolute; top:2px; right:25px; text-align:right;">
     <a href="" onclick="$('#add_task_form').slideDown('slow'); return false;"><img src ="images/i_add.png"></a>
</div>
<div class="main mobile_main">
               <!-- <div class="tasktop">
                    <span class="headline11">Your tasks</span>
                    <span class="headerlinks">Upcoming<span class="headerlinksI">|</span><a href="i_tasks_completed.php">Completed</a></span>
                </div>-->
<div id="add_task_form" style="display:none; " class="mainheader">
   <div class="mobile_head_pad5">
                    <?php
                        $do_task_add = new Task();
                        $do_task_add->getiTaskAddForm();

                    ?>
   </div>    
</div>
           <div class="mobile_head_pad5">
                    <?php 
                        $do_task->getAllTasksOverdue();
                        if ($do_task->getNumRows()) {
                     ?>
                    <div class="task">
                        <div class="headline10" style="color: #ff0000;">Overdue</div>
                        <?php
                            echo $do_task->viewTasks();                         
                        ?>
                    </div>
                    <?php  
                        }
                        $do_task->getAllTasksToday();
                        if ($do_task->getNumRows()) {
                    ?>
                    <div class="task_today">
                        <div class="headline10">Today</div>
                        <?php echo $do_task->viewTasks(); ?>
                    </div>
                    <?php 
                        }
                        $do_task->getAllTasksTomorrow();
                        if ($do_task->getNumRows()) {                            
                    ?>
                    <div class="task">
                        <div class="headline10">Tomorrow</div>
                        <?php echo $do_task->viewTasks(); ?>
                    </div>
                    <?php
                        }
                        $do_task->getAllTasksThisWeek();
                        if ($do_task->getNumRows()) {
                    ?>
                    <div class="task">
                        <div class="headline10">This week</div>
                        <?php echo $do_task->viewTasks(); ?>
                    </div>
                    <?php 
                        }
                        $do_task->getAllTasksNextWeek();
                        if ($do_task->getNumRows()) {
                    ?>
                    <div class="task">
                        <div class="headline10">Next week</div>
                        <?php echo $do_task->viewTasks(); ?>
                    </div>
                    <?php 
                        }
                        $do_task->getAllTasksLater();
                        if ($do_task->getNumRows()) {    
                    ?>
                    <div class="task">
                        <div class="headline10">Later</div>
                        <?php echo $do_task->viewTasks(); ?>
                    </div>
                    <?php } ?>
                    <div class="dottedline"></div>
                    <?php $footer_note = 'dropboxtask'; include_once('includes/footer_notes.php'); ?>
                    <div class="bottompad40"></div>
                </div>
<!--
            </td>
        </tr>
    </table>-->
<?php include_once('i_ofuz_logout.php'); ?>
</div>
</body>
</html>
