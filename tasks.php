<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


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


   /* $do_list_project_task = new ProjectTask();                    
    $do_list_project_task->viewProjectTasks();*/


    if(!is_object($_SESSION['do_list_project_task'])){
        $do_project_task = new ProjectTask();
        $do_project_task->sessionPersistent("do_list_project_task", "projects.php", OFUZ_TTL);
    }



?>
	<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
<style type="text/css">
	.ui-effects-transfer { border: 2px groove #B13283; }
</style>

<script type="text/javascript">
    //<![CDATA[
<?php include_once('includes/ofuz_js.inc.php'); ?>
    
    var allowHighlight = true;
    function fnHighlight(area,color,change_to) {
        if (allowHighlight == false) return;
        var ck=$("#ck"+area);
        var div=$("#pt_"+area);

        var ctlbar=$("#contacts_ctlbar");
        var count_checkbox_checked = 0 ;       
        var selected_tasks= new Array();

        $("input:checkbox").each(function(){
                if(this.checked == true){                  
                   selected_tasks[count_checkbox_checked] = $(this).val();                
                   count_checkbox_checked++ ;                   
                  }               
            });
  
        if(count_checkbox_checked!=0){
            if (ck.is(":checked")){
                div.css("background-color", change_to);
                    if(ctlbar.is(":hidden"))ctlbar.slideDown("fast");
                        $.ajax({
                            type: "GET",
                            <?php
                              $e_getOwners = new Event("ProjectTask->eventRenderChangeTaskOwnerList");
                              $e_getOwners->setEventControler("ajax_evctl.php");
                              $e_getOwners->setSecure(false);
                            ?>
                            url: "<?php echo $e_getOwners->getUrl(); ?>",
                            data: "idproject="+selected_tasks,
                            success: function(result){
                                    $("#co_workers").html(result);
                                    }
                              });
            }else{
                 div.css("background-color", color);
                   if(count_checkbox_checked!=0){
                      if(ctlbar.is(":hidden"))ctlbar.slideDown("fast");
                           $.ajax({
                              type: "GET",
                               <?php
                               $e_getOwners = new Event("ProjectTask->eventRenderChangeTaskOwnerList");
                               $e_getOwners->setEventControler("ajax_evctl.php");
                               $e_getOwners->setSecure(false);
                               ?>
                               url: "<?php echo $e_getOwners->getUrl(); ?>",
                               data: "idproject="+selected_tasks,
                               success: function(result){
                                         $("#co_workers").html(result);
                                         }
                            });
                      }
                 }
       }else{
          div.css("background-color", color);
          ctlbar.slideUp("fast");
          $("#co_workers").html('');
       }

  }


        
        //ck.attr("checked",(ck.is(":checked")?"":"unchecked"));

      /*  if (ck.is(":checked")){
          $("input:checkbox").each(function(){
                if(this.checked == true){              
                   selected_tasks[count_checkbox_checked] = $(this).val();                 
                   count_checkbox_checked++ ;            
                }
                
            });


          alert("first if");
            //div.css("background-color", "#ffffdd");
            div.css("background-color", change_to);
            if(count_checkbox_checked!=0){
                if(ctlbar.is(":hidden"))ctlbar.slideDown("fast");
                alert(count_checkbox_checked);
                  $.ajax({
                          type: "GET",
                        <?php
                          $e_getOwners = new Event("ProjectTask->eventRenderChangeTaskOwnerList");
                          $e_getOwners->setEventControler("ajax_evctl.php");
                          $e_getOwners->setSecure(false);
                        ?>
                          url: "<?php echo $e_getOwners->getUrl(); ?>",
                          data: "idproject="+selected_tasks,
                          success: function(result){
                          $("#co_workers").html(result);
                    }
                  });
            }
        } else {
            //div.css("background-color", "#ffffff");
            div.css("background-color", color);
          alert("esle");
            //if($("input:checked").length==0)ctlbar.slideUp("fast");
            var all_checked = true;
           
           

            var selected_tasks= new Array();
            var count_checkbox_checked = 0 ;
            $("input:checkbox").each(function(){
                if(this.checked == true){ 
                   selected_tasks[count_checkbox_checked] = $(this).val();
                   count_checkbox_checked++ ;
                  }
            });

         





            if(count_checkbox_checked == 0){
                all_checked = false;
            }     



            if(all_checked == false ) { 
                $('#co_workers').html('');
                ctlbar.slideUp("fast");
                 
            }
        }
    }*/

    function closeTaskMul(){
      if (confirm("<?php echo _('Are you sure you want to Close the selected Task ?');?>")) {
          $("#do_list_project_task__eventChangeOwnerMultiple_mydb_events_100_").attr("value", "do_list_project_task->eventCloseTaskMultiple");
          $("#do_list_project_task__eventChangeOwnerMultiple").submit();
      }
    }

    function changeDueDateMul(){
        if (confirm("<?php echo _('Are you sure you want to change due date of selected task ?');?>")) {           
            $("#do_list_project_task__eventChangeOwnerMultiple_mydb_events_100_").attr("value", "do_list_project_task->eventChangeDueDateMultiple");
            $("#do_list_project_task__eventChangeOwnerMultiple").submit();
        }
    }

    function fnSelAll() {
         var count_checkbox_checked = 0 ;
         var selected_tasks= new Array();
        $("input:checkbox").each(function(){
            this.checked=true;
            if(this.checked == true){                                 
                  selected_tasks[count_checkbox_checked] = jQuery(this).val();
                  count_checkbox_checked++ ;   
                }
        });
        $.ajax({
          type: "GET",
        <?php
            $e_getOwners = new Event("ProjectTask->eventRenderChangeTaskOwnerList");
            $e_getOwners->setEventControler("ajax_evctl.php");
            $e_getOwners->setSecure(false);
            ?>
        url: "<?php echo $e_getOwners->getUrl(); ?>",
        data: "idproject="+selected_tasks,
        success: function(result){
            $("#co_workers").html(result);
        }
    });

        $("li.ddtasks").css("background-color", "#ffffdd");
        $("li.ddtasks_today").css("background-color", "#b8eaaa");
        $("li.ddtasks_overdue").css("background-color", "#ffe9ad");
    }
    function fnSelNone() {
        $("input:checkbox").each(function(){this.checked=false;});
        $('#co_workers').html('');
        $("li.ddtasks").css("background-color", "#ffffff");
        $("li.ddtasks_today").css("background-color", "#b8eacc");
        $("li.ddtasks_overdue").css("background-color", "#ffe9ce");
        $("#contacts_ctlbar").slideUp("fast");
    }

    function fnEditProject(){
        $("#project_ctlbar").slideToggle("fast");
    }
    function fnFilterProject(){
        $("#project_filter").slideToggle("fast");
    }
    function fnViewTask(idproject_task) {
        document.location.href = "/task.php?idprojecttask="+idproject_task;
    }
    function showDateOpt(){
        $("#due_sp_date").show(0);
        $("#when_due").hide(0);
        document.getElementById('sp_date_selected').value = "Yes";
    }
    function hideDateOpt(){
        $("#due_sp_date").hide(0);
        $("#when_due").show(0);
        document.getElementById('sp_date_selected').value = "";
    }
      

<?php
$e_PrioritySort = new Event("Task->eventAjaxPrioritySort");
$e_PrioritySort->setEventControler("ajax_evctl.php");
$e_PrioritySort->setSecure(false);
$strPrioritySortURL = $e_PrioritySort->getUrl();
?>
    

    $(document).ready(function() {
	var dropped_idtask; //global var
	$("#tasks_list_overdue").sortable({
		connectWith: ['#tasks_list_today', '#tasks_list_tomorrow', '#tasks_list_thisweek', '#tasks_list_nextweek', '#tasks_list_thismonth', '#tasks_list_later'],
		opacity: 0.4,
		receive: function(event, ui) {
			//Run this code whenever an item is dragged and dropped into this list
			var item= ui.item.attr('id');
                        var arr_dropped_idtask = item.split('_');	
			dropped_idtask = arr_dropped_idtask[1];
			var selectedEffect = 'transfer';
			var options = {};
			if(selectedEffect == 'transfer'){ options = { to: "#tasks_list_overdue", className: 'ui-effects-transfer' }; }
			$("#"+item).effect(selectedEffect,options,500,callback(item));
			var position_dropped_element = ui.item.prevAll().length;
		},
		update:function(){
		  var priorities=$("#tasks_list_overdue").sortable("serialize");
		  $.get("<?php echo $strPrioritySortURL; ?>&"+priorities+"&due_date=overdue&dropped_idtask="+dropped_idtask);
		}
	}),
	$("#tasks_list_today").sortable({
		connectWith: ['#tasks_list_overdue', '#tasks_list_tomorrow', '#tasks_list_thisweek', '#tasks_list_nextweek', '#tasks_list_thismonth', '#tasks_list_later'],
		opacity: 0.4,
		receive: function(event, ui) {
			//Run this code whenever an item is dragged and dropped into this list
			var item= ui.item.attr('id');
                        var arr_dropped_idtask = item.split('_');	
			dropped_idtask = arr_dropped_idtask[1];
			var selectedEffect = 'transfer';
			var options = {};
			if(selectedEffect == 'transfer'){ options = { to: "#tasks_list_today", className: 'ui-effects-transfer' }; }
			$("#"+item).effect(selectedEffect,options,500,callback(item));
			var position_dropped_element = ui.item.prevAll().length;
		},
		update:function(){
		  var priorities=$("#tasks_list_today").sortable("serialize");
		  $.get("<?php echo $strPrioritySortURL; ?>&"+priorities+"&due_date=today&dropped_idtask="+dropped_idtask);
		}
	}),
	$("#tasks_list_tomorrow").sortable({
		connectWith: ['#tasks_list_overdue', '#tasks_list_today', '#tasks_list_thisweek', '#tasks_list_nextweek', '#tasks_list_thismonth', '#tasks_list_later'],
		opacity: 0.4,
		receive: function(event, ui) {
			//Run this code whenever an item is dragged and dropped into this list
			var item= ui.item.attr('id');
                        var arr_dropped_idtask = item.split('_');	
			dropped_idtask = arr_dropped_idtask[1];
			var selectedEffect = 'transfer';
			var options = {};
			if(selectedEffect == 'transfer'){ options = { to: "#tasks_list_tomorrow", className: 'ui-effects-transfer' }; }
			$("#"+item).effect(selectedEffect,options,500,callback(item));
			var position_dropped_element = ui.item.prevAll().length;
		},
		update:function(){
		  var priorities=$("#tasks_list_tomorrow").sortable("serialize");
		  $.get("<?php echo $strPrioritySortURL; ?>&"+priorities+"&due_date=tomorrow&dropped_idtask="+dropped_idtask);
		}
	}),
	$("#tasks_list_thisweek").sortable({
		connectWith: ['#tasks_list_overdue', '#tasks_list_today', '#tasks_list_tomorrow', '#tasks_list_nextweek', '#tasks_list_thismonth', '#tasks_list_later'],
		opacity: 0.4,
		receive: function(event, ui) {
			//Run this code whenever an item is dragged and dropped into this list
			var item= ui.item.attr('id');
                        var arr_dropped_idtask = item.split('_');	
			dropped_idtask = arr_dropped_idtask[1];
			var selectedEffect = 'transfer';
			var options = {};
			if(selectedEffect == 'transfer'){ options = { to: "#tasks_list_thisweek", className: 'ui-effects-transfer' }; }
			$("#"+item).effect(selectedEffect,options,500,callback(item));
			var position_dropped_element = ui.item.prevAll().length;
		},
		update:function(){
		  var priorities=$("#tasks_list_thisweek").sortable("serialize");
		  $.get("<?php echo $strPrioritySortURL; ?>&"+priorities+"&due_date=thisweek&dropped_idtask="+dropped_idtask);
		}
	}),
	$("#tasks_list_nextweek").sortable({
		connectWith: ['#tasks_list_overdue', '#tasks_list_today', '#tasks_list_tomorrow', '#tasks_list_thisweek', '#tasks_list_thismonth', '#tasks_list_later'],
		opacity: 0.4,
		receive: function(event, ui) {
			//Run this code whenever an item is dragged and dropped into this list
			var item= ui.item.attr('id');
                        var arr_dropped_idtask = item.split('_');	
			dropped_idtask = arr_dropped_idtask[1];
			var selectedEffect = 'transfer';
			var options = {};
			if(selectedEffect == 'transfer'){ options = { to: "#tasks_list_nextweek", className: 'ui-effects-transfer' }; }
			$("#"+item).effect(selectedEffect,options,500,callback(item));
			var position_dropped_element = ui.item.prevAll().length;
		},
		update:function(){
		  var priorities=$("#tasks_list_nextweek").sortable("serialize");
		  $.get("<?php echo $strPrioritySortURL; ?>&"+priorities+"&due_date=nextweek&dropped_idtask="+dropped_idtask);
		}
	}),
	$("#tasks_list_thismonth").sortable({
		connectWith: ['#tasks_list_overdue', '#tasks_list_today', '#tasks_list_tomorrow', '#tasks_list_thisweek', '#tasks_list_nextweek', '#tasks_list_later'],
		opacity: 0.4,
		receive: function(event, ui) {
			//Run this code whenever an item is dragged and dropped into this list
			var item= ui.item.attr('id');
                        var arr_dropped_idtask = item.split('_');	
			dropped_idtask = arr_dropped_idtask[1];
			var selectedEffect = 'transfer';
			var options = {};
			if(selectedEffect == 'transfer'){ options = { to: "#tasks_list_thismonth", className: 'ui-effects-transfer' }; }
			$("#"+item).effect(selectedEffect,options,500,callback(item));
			var position_dropped_element = ui.item.prevAll().length;
		},
		update:function(){
		  var priorities=$("#tasks_list_thismonth").sortable("serialize");
		  $.get("<?php echo $strPrioritySortURL; ?>&"+priorities+"&due_date=thismonth&dropped_idtask="+dropped_idtask);
		}
	}),
	$("#tasks_list_later").sortable({
		connectWith: ['#tasks_list_overdue', '#tasks_list_today', '#tasks_list_tomorrow', '#tasks_list_thisweek', '#tasks_list_nextweek', '#tasks_list_thismonth'],
		opacity: 0.4,
		receive: function(event, ui) {
			//Run this code whenever an item is dragged and dropped into this list
			var item= ui.item.attr('id');
                        var arr_dropped_idtask = item.split('_');	
			dropped_idtask = arr_dropped_idtask[1];
			var selectedEffect = 'transfer';
			var options = {};
			if(selectedEffect == 'transfer'){ options = { to: "#tasks_list_later", className: 'ui-effects-transfer' }; }
			$("#"+item).effect(selectedEffect,options,500,callback(item));
			var position_dropped_element = ui.item.prevAll().length;
		},
		update:function(){
		  var priorities=$("#tasks_list_later").sortable("serialize");
		  $.get("<?php echo $strPrioritySortURL; ?>&"+priorities+"&due_date=later&dropped_idtask="+dropped_idtask);
		}
	}).disableSelection();

	function callback(item){
			setTimeout(function(){
				$("#"+item+":hidden").removeAttr('style').hide().fadeIn();
				
			}, 1000);
	};

    });

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
  <table class="layout_columns">
      <tr><td class="layout_lmargin"></td>
          <td>
              <div class="layout_content">
                    <?php $thistab = _('Tasks'); include_once('includes/ofuz_navtabs.php'); ?>
                    <?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
                  <div class="grayline1"></div>
                  <div class="spacerblock_20"></div>
                   <table class="layout_columns">
                      <tr>
                        <td class="layout_lcolumn">
                            <?php include_once('plugin_block.php');?>
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
                                <span class="headline14">Your tasks</span><!--<div id="info">testing message place</div>-->
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


                            <?php 
                            $do_proj_task_operation = new ProjectTask();
                            $e_set_close = new Event("do_list_project_task->eventChangeOwnerMultiple");
                            $e_set_close->addEventAction("mydb.gotoPage", 304);
                            $e_set_close->addParam("goto", "tasks.php");
                            echo $e_set_close->getFormHeader();
                            echo $e_set_close->getFormEvent();
                              ?>



                            <div id="contacts_ctlbar" style="display: none;">
                              <div id ="co_workers"></div>
                                <?php 
                                  // $_SESSION['do_project']->getAllProjects();
                                    //echo '<select name="project_id">'.$_SESSION['do_project']->getProjectsSelectOptions($_SESSION['do_project_task']->idproject).'</select>';
                                    //echo '<input type="button" onclick = "changeProjMul();return false;" value="'._('Assign Task To').'">';
                                    echo (' or ').' '._('change due date')._(':');
                                    // OO style using FieldsForm object to generate a field. The same thing we have on task.php to generate due_date but using JS and HTML
                                    $field_due_date_mul = new DijitDateTextBox("due_date_mul");
                                    $field_due_date_mul->datetype = 'dd-MM-y';
                                    //$field_due_date_mul->name = 'due_date_mul';
                                    $form_fields = new FieldsForm();
                                    $form_fields->addField($field_due_date_mul);
                                    echo $form_fields->due_date_mul ;
                                    // Ends Here 
                                    echo '<input type="button" onclick = "changeDueDateMul();return false;" value="'._('Change Due Date').'">';
                                    //echo '<br/>',_(' or '),'<span class="redlink"><a href="#" onclick="moveTasks(0);return false;">'._('Promote them to the top').'</a></span>';        
                                    echo '<br/>',_(' or '),'<span class="redlink"><a href="#" onclick="closeTaskMul();return false;">'._('Close the Selected Task').'</a></span>';        
                                ?>
                              <div class="spacerblock_10"></div>
                              <span class="sasnlinks">( <span class="bluelink"><a href="#" onclick="fnSelAll(); return false;"><?php echo _('select all'); ?></a></span> | <span class="bluelink"><a href="#" onclick="fnSelNone(); return false;"><?php echo _('select none');?></a></span> )</span>
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
        </div>

<!--Over Due Tasks-->

            <?php
                $num_tasks_overdue = $do_task->getNumAllTasksOverdue();
                $do_task->getAllTasksOverdue();
                $num_tasks_overdue_limit = $do_task->getNumRows();
                if ($do_task->getNumRows()) {
             ?>
		<div class="tasks">

		  <div class="headline10" style="color: #ff0000;"><?php echo _('Overdue');?></div>
		  <div class="contentfull">

		    <div class="ddtasks">
		      <div id="tasks_overdue">
			<?php  echo $do_task->viewTaskList('overdue'); //echo $do_task->viewTasks(); ?>
		      </div>
		      <?php if($num_tasks_overdue > 10) { ?>
			<div id="tasks_options_overdue"><a href="#" onclick="showAllTasksOverdue(); return false;"><?php echo _('More...');?></a></div>
		      <?php } ?>
		    </div>
		  </div>
		</div>
	    <?php  
	      }
	      /*$do_task->getAllTasksToday();
	      if ($do_task->getNumRows()) {*/
	      ?>



<?php
$do_task->getAllTasksToday();
//$do_task->query($do_task->getSqlQuery());
if($do_task->getNumRows()){
?>
			
	      <div class="tasks_today"> 
		<div class="headline10">Today</div>
		  <div class="contentfull">
		    <div class="ddtasks">
		    <?php //echo $do_task->viewTasks(); ?>
		    <?php   echo $do_task->viewTaskList('today'); ?>
		    </div>
		  </div>
		</div>
	      <!--</div>-->
<?php                 
}
?>
<?php
$do_task->getAllTasksTomorrow();
//$do_task->query($do_task->getSqlQuery());
if($do_task->getNumRows()){
?>      
             
	    <div class="tasks">                  
	      <div class="headline10">Tomorrow</div>
		<div class="contentfull">
		  <div class="ddtasks">
		    <div class = "task_tomorrow">
		    <?php            
		    echo $do_task->viewTaskList('tomorrow');
		    ?>
		    <?php //echo $do_task->viewTasks(); ?>
		    </div>
		  </div>
		</div>
	      </div>

<?php
}
/*
$do_task->getAllTasksThisWeek();
if ($do_task->getNumRows()) {*/

echo $do_task->getAllTasksThisWeek();
//$do_task->query($do_task->getSqlQuery());
if($do_task->getNumRows()){
?>
<div class="tasks">
  <div class="headline10">This week</div>
  <div class="contentfull">
    <div class="ddtasks">
    <?php echo $do_task->viewTaskList('thisweek');?>
    <?php //echo $do_task->viewTasks(); ?>
    </div>
  </div>
</div>
<?php 
}

$do_task->getAllTasksNextWeek();
//$do_task->query($do_task->getSqlQuery());
if($do_task->getNumRows()){
?>                         
<div class="tasks">
  <div class="headline10">Next week</div>
  <div class="contentfull">
    <div class="ddtasks">          
    <?php echo $do_task->viewTaskList('nextweek');
    ?>
    <?php //echo $do_task->viewTasks(); ?>
    </div>
  </div>
</div>
<?php 
}
$num_tasks_this_month = $do_task->getNumAllTasksThisMonth();
$do_task->getAllTasksThisMonth();
//$do_task->query($do_task->getSqlQuery());
$num_tasks_this_month_limit = $do_task->getNumRows();
if ($do_task->getNumRows()) {
?>
<div class="tasks">
  <div class="headline10"><?php echo _('This Month');?></div>
  <div class="contentfull">
   <div class="ddtasks">
     <div id="tasks_thismonth">      
      <?php echo $do_task->viewTaskList('thismonth');//echo $do_task->viewTasks(); ?>
      </div>                         
    </div>
  </div>
</div>
<?php if($num_tasks_this_month > 20) { ?>
<div id="tasks_options_this_month"><a href="#" onclick="showAllTasksThisMonth(); return false;"><?php echo _('More...')?></a></div>
<?php } ?>
<?php 
}

$num_tasks_later = $do_task->getNumAllTasksLater();
$do_task->getAllTasksLater();
//$do_task->query($do_task->getSqlQuery());
$num_twenty_tasks = $do_task->getNumRows();
if ($do_task->getNumRows()) {
?>
<div class="tasks">
  <div class="headline10">Later</div>              
  <div class="contentfull">
    <div class="ddtasks">
      <div id="tasks_later"><?php   echo $do_task->viewTaskList('later'); //echo $do_task->viewTasks(); ?></div>
    </div>
  </div>
</div>
<?php if($num_tasks_later > 20) { ?>
<div id="tasks_options"><a href="#" onclick="showAllTasksLater(); return false;"><?php echo _('More...');?></a></div>
<?php } ?>
<?php } 
}// Ends the else condition
?>

            <div class="dottedline"></div>
            <?php $footer_note = 'dropboxtask'; include_once('includes/footer_notes.php'); ?>
        </div>
       </div>
      </div>
    </td></tr></table>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>


