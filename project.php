<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Project';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    if (isset($_GET['idproject'])) {
        $idproject = (int)$_GET['idproject'];
    } elseif (is_object($_SESSION["eDetail_Project"])) {
        $idproject = $_SESSION["eDetail_Project"]->getparam("idproject");
    } elseif($do_project->idproject){
       $idproject = $do_project->idproject;
    } elseif(is_object($_SESSION['do_project'])) {
        $idproject = $_SESSION['do_project']->idproject;
    }
    $project_access = false;
    $project_operation_access = true;
    $do_project = new Project();
    
    if(!is_object($_SESSION['do_list_project_task'])){
        $do_project_task = new ProjectTask();
        $do_project_task->sessionPersistent("do_list_project_task", "projects.php", OFUZ_TTL);
    }
    //check the access

    if(!empty($idproject)){
          if($do_project->isProjectOwner($idproject)){
                $project_access = true;
          }else{
              if($do_project->isProjectCoWorker($idproject)){
                $project_access = true;
              }else{
                $project_access = false;
              }
          }
    }
   
    // If the project is not accessible by user check if the project is a public project or not
    /*if($project_access === false){ 
        if($do_project->isPublicProject($idproject) === true){
            $project_access = true;
            $project_operation_access = false;
        }
    }*/
    
    if($project_access){
        $WorkFeedProjectTask = new WorkFeedProjectTask();
        $do_project->getId($idproject);
        $do_project->sessionPersistent("do_project", "index.php", OFUZ_TTL);
    
        $project_details = $_SESSION["do_project"]->getProjectDetails($idproject);
        $do_project->setBreadcrumb();
    }
?>
<script type="text/javascript">
    //<![CDATA[
	
	<?php include_once('includes/ofuz_js.inc.php'); ?>

    var allowHighlight = true;
    function fnHighlight(area,color,change_to) {
        if (allowHighlight == false) return;
        var ck=$("#ck"+area);
        var div=$("#pt_"+area);
        var ctlbar=$("#contacts_ctlbar");
        ck.attr("checked",(ck.is(":checked")?"":"checked"));
        if (ck.is(":checked")) {
            //div.css("background-color", "#ffffdd");
            div.css("background-color", change_to);
            if(ctlbar.is(":hidden"))ctlbar.slideDown("fast");
        } else {
            //div.css("background-color", "#ffffff");
            div.css("background-color", color);
            //if($("input:checked").length==0)ctlbar.slideUp("fast");
            var all_checked = true;
            var count_checkbox_checked = 0 ;
            $("input:checkbox").each(function(){
                if(this.checked == true){
                  count_checkbox_checked++ ;
                }
            });
            if(count_checkbox_checked == 0){
                all_checked = false;
            }
            if(all_checked == false ) ctlbar.slideUp("fast");
        }
    }

    function closeTaskMul(){
      if (confirm("<?php echo _('Are you sure you want to Close the selected Task ?');?>")) {
          $("#do_list_project_task__eventChangeOwnerMultiple_mydb_events_100_").attr("value", "do_list_project_task->eventCloseTaskMultiple");
          $("#do_list_project_task__eventChangeOwnerMultiple").submit();
      }
    }

    function changeProjMul(){
        if (confirm("<?php echo _('Are you sure you want to change the selected task to a different project ?');?>")) {
            $("#do_list_project_task__eventChangeOwnerMultiple_mydb_events_100_").attr("value", "do_list_project_task->eventChangeProjectForTaskMultiple");
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
        $("input:checkbox").each(function(){
            this.checked=true;
        });
        $("li.ddtasks").css("background-color", "#ffffdd");
        $("li.ddtasks_today").css("background-color", "#b8eaaa");
        $("li.ddtasks_overdue").css("background-color", "#ffe9ad");
    }
    function fnSelNone() {
        $("input:checkbox").each(function(){this.checked=false;});
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
$e_PrioritySort = new Event("ProjectTask->eventAjaxPrioritySort");
$e_PrioritySort->setEventControler("ajax_evctl.php");
$e_PrioritySort->setSecure(false);
$strPrioritySortURL = $e_PrioritySort->getUrl();
?>
    function moveTasks(TorB) {
        var priorities="",checked="",unchecked="";
        var priorities=$("#project_tasks").sortable("toArray");
        $("input:checkbox").each(function(){
            if(this.checked){
                checked+=(checked=="")?"":"&";
                checked+="pt[]="+$(this).parents("li").attr("id").substr(3);
            }else{
                unchecked+=(unchecked=="")?"":"&";
                unchecked+="pt[]="+$(this).parents("li").attr("id").substr(3);
            }
        });
        if(TorB==1){
            priorities=unchecked+(unchecked==""?checked:"&"+checked);
        }else{
            priorities=checked+(unchecked==""?"":"&"+unchecked);
        }
        $.get("<?php echo $strPrioritySortURL; ?>&"+priorities, function(){window.location.reload();});
    }

    $(document).ready(function() {
        $("div[id^=templt]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
        $("#project_tasks").sortable({axis:"y",handle:".ptask_handle",helper:"clone",
            update:function(){
            var priorities=$("#project_tasks").sortable("serialize");
            $.get("<?php echo $strPrioritySortURL; ?>&"+priorities);}
        });
    });
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Projects'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$project_access){
              $msg = new Message(); 
			  $msg->getMessage("unauthorized project access");
			  $msg->displayMessage();
              
              exit;
          }
    ?>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
     <?php
      // If the project is a public project then hide the following
      if($project_operation_access === true){

	// Login the Block Plugins here //
	include_once('plugin_block.php');
    }// Public project hide part ends here
    ?>
    </td><td class="layout_rcolumn">
	    <?php
            $msg = new Message(); 
			if ($msg->getMessageFromContext("project tasks")) {
				echo $msg->displayMessage();
			}
        ?> 
        <div class="mainheader">
            <div class="project_detail_name">
                <span class="headline14"><?php echo $project_details['name']; ?></span>
                <span class="project_edit">
                  <?php 
                      if($project_operation_access === true){
                  ?>
                    <a href="#" onclick="fnEditProject(); return false;">edit</a>
                  <?php } ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="fnFilterProject(); return false;">filter</a>
                </span>
            </div>
        </div>
          <?php
              if($_SESSION['do_list_project_task']->project_id_searched != $idproject ){
                                $_SESSION['do_list_project_task']->clearSearch();
              }
              if($_SESSION['do_list_project_task']->set_search){
                  echo '<div id="project_filter" style="display: block;">';
              }else{
                  echo '<div id="project_filter" style="display: none;">';
              }
          ?>
         
          <?php 

              $e_proj_filter_worker = new Event("do_list_project_task->eventFilterProjectTask");
              $e_proj_filter_worker->setLevel(501);
              $e_proj_filter_worker->addParam("goto", $_SERVER['PHP_SELF']);
              $e_proj_filter_worker->addParam("idproject", $idproject);
              echo '<form id="setFilterProjTask" name="setFilterProjTask" method="post" action="/eventcontroler.php">';
              echo $e_proj_filter_worker->getFormEvent();
              $project_workers = $_SESSION["do_project"]->getProjectCoWorkers();
                            
              echo '<select name ="proj_workers" id = "proj_workers" class="" onChange=\'$("#setFilterProjTask").submit();\' style="align:center;"">';
              echo '<option value = "0" >'. _('-- All Participants --').'</option>';
              foreach($project_workers as $workers){
                  $id = $workers['idcoworker'];
                  echo '<option value = "'.$workers['idcoworker'].'" '.$_SESSION['do_list_project_task']->getUserFilter($id).'>'.$workers['firstname'].' '.$workers['lastname'].'</option>';
              }
              echo '</select>';
              $project_task_category = $_SESSION["do_project"]->getDistinctTaskCategoryForProject();
              if($project_task_category && is_array($project_task_category)){
                  echo '<select name ="proj_task_cat" id = "proj_task_cat" class="" onChange=\'$("#setFilterProjTask").submit();\' >';
                  echo '<option value = "" >'. _('-- All Task Categories --').'</option>';
                  foreach($project_task_category as $category){
                    echo '<option value = "'.$category['category'].'" '.$_SESSION['do_list_project_task']->getCategoryFilter($category['category']).'>'.$category['category'].'</option>';
                  }
              }
              echo '</select>';
              echo '</form>';
        ?>
         </div>
        <div id="project_ctlbar" style="display: none;">
            <?php
                // Create a new persistent object do_project_edit and do the edit operation
                $do_project_edit = new Project();
                $do_project_edit->sessionPersistent("do_project_edit", "index.php", OFUZ_TTL);
                 $_SESSION['do_project_edit']->getId((int)$idproject);
                //$e_ProjectEdit = new Event('Project->eventUpdateProject');
                $e_ProjectEdit = new Event('do_project_edit->eventUpdateProject');
                $e_ProjectEdit->setLevel(1999);
                $e_ProjectEdit->addEventAction('mydb.gotoPage', 2000);
                $e_ProjectEdit->addParam('goto', 'Project/'.$idproject);
                echo $e_ProjectEdit->getFormHeader();
                echo $e_ProjectEdit->getFormEvent();
                $_SESSION['do_project_edit']->setFields("project"); 
                $_SESSION['do_project_edit']->setApplyRegistry(true, "Form");
            ?>
            <!--Project name: &nbsp; <input type="text" name="name" value="<?php echo $project_details['name']; ?>" style="width:400px;" /><br /><br />
            Effort estimated in hours : &nbsp; <input type="text" name="effort_estimated_hrs" value="<?php echo $project_details['effort_estimated_hrs']; ?>" style="width:40px;" /><br /><br />-->
             <?php echo _('Project name').':&nbsp;'.$_SESSION['do_project_edit']->name ?><br /><br />
             <?php echo _('Effort estimated in hours').':&nbsp;'.$_SESSION['do_project_edit']->effort_estimated_hrs ?><br /><br />
             <?php echo _('Company').':&nbsp;'.$_SESSION['do_project_edit']->idcompany ?><br /><br />

            <?php 
                  
                  if($do_project->isProjectOwner($project_details['idproject'])){ // only project owner can perform open/close
                     $open_checked  = '';
                     $closed_checked  = '';
                     // Project is open/closed
                     if($project_details['status'] == 'closed'){
                        $closed_checked = 'Checked';
                     }else{
                        $open_checked = 'Checked';
                     }

                     //Project is public private
                     $public_checked = '';
                     $private_checked = '';
                     if($project_details['is_public'] == 1)
                        $public_checked = 'Checked';
                     else 
                        $private_checked = 'Checked';
                  
                     echo _('Project Status').'&nbsp;&nbsp;<input type = "radio" name = "status" id="status" value = "open" '.$open_checked.'>'._('Open').'&nbsp;&nbsp;<input type = "radio" name = "status" id="status" value = "closed" '.$closed_checked.'>'._('Closed').'<br /><br /> ';
                     echo _('Project accesibility').'&nbsp;&nbsp;<input type = "radio" name = "is_public"  value = "1" '.$public_checked.'>'._('Public').'&nbsp;&nbsp;<input type = "radio" name = "is_public" value = "0" '.$private_checked.'>'._('Private').'<br /><br /> ';
                    
                  }else{
                      echo '<input type="hidden" name = "status" value ="'.$project_details['status'].'" >';
                  }
            ?>
            <input type="hidden" name="idproject" value="<?php echo $idproject; ?>" />
            <input type="submit" value="<?php echo _('Save');?>" /> &nbsp;or&nbsp; <a href="#" onclick="fnEditProject(); return false;"><?php echo _('cancel');?></a>
            </form>
        </div>
	    <?php 
		$do_proj_task_operation = new ProjectTask();
		$e_set_close = new Event("do_list_project_task->eventChangeOwnerMultiple");
		$e_set_close->addEventAction("mydb.gotoPage", 304);
		$e_set_close->addParam("goto", "Project/".$idproject);
		echo $e_set_close->getFormHeader();
		echo $e_set_close->getFormEvent();
	    ?>
	<div id="contacts_ctlbar" style="display: none;">
    <?php 
        echo '<b>'._('With the selected Task(s) you can:').'</b><br/>';
        echo _('Change the task owner')._(':');
        echo $do_proj_task_operation->renderChangeTaskOwnerList($idproject);
        echo '<br/>'._(' or ').' '._('move them to another project')._(':');
        $_SESSION['do_project']->getAllProjects();
        echo '<select name="project_id">'.$_SESSION['do_project']->getProjectsSelectOptions($_SESSION['do_project_task']->idproject).'</select>';
        echo '<input type="button" onclick = "changeProjMul();return false;" value="'._('Move').'">';
        echo '<br/>'._(' or ').' '._('change due date')._(':');
        // OO style using FieldsForm object to generate a field. The same thing we have on task.php to generate due_date but using JS and HTML
        $field_due_date_mul = new DijitDateTextBox("due_date_mul");
        $field_due_date_mul->datetype = 'dd-MM-y';
        //$field_due_date_mul->name = 'due_date_mul';
        $form_fields = new FieldsForm();
        $form_fields->addField($field_due_date_mul);
        echo $form_fields->due_date_mul ;
        // Ends Here 
        echo '<input type="button" onclick = "changeDueDateMul();return false;" value="'._('Change Due Date').'">';
        echo '<br/>',_(' or '),'<span class="redlink"><a href="#" onclick="moveTasks(0);return false;">'._('Promote them to the top').'</a></span>';
        echo '<br/>',_(' or '),'<span class="redlink"><a href="#" onclick="moveTasks(1);return false;">'._('Drop them to the bottom').'</a></span>';
        echo '<br/>',_(' or '),'<span class="redlink"><a href="#" onclick="closeTaskMul();return false;">'._('Close them all').'</a></span>';
    ?>
        <div class="spacerblock_10"></div>
	    <span class="sasnlinks">( <span class="bluelink"><a href="#" onclick="fnSelAll(); return false;"><?php echo _('select all'); ?></a></span> | <span class="bluelink"><a href="#" onclick="fnSelNone(); return false;"><?php echo _('select none');?></a></span> )</span>
	</div>

        <div class="contentfull">
            <div class="ddtasks">

            <?php
                if($_SESSION['do_list_project_task']->getSqlQuery() =='' || $_SESSION['do_list_project_task']->project_id_searched != $idproject ){
                    $_SESSION['do_list_project_task']->clearSearch();
                    $_SESSION['do_list_project_task']->getAllProjectTasks($idproject);
                 }
                $_SESSION['do_list_project_task']->query($_SESSION['do_list_project_task']->getSqlQuery());
                if($_SESSION['do_list_project_task']->getNumRows()){
                  echo $_SESSION['do_list_project_task']->viewProjectTasks();
                }

             ?>
             </div>
        </div></form>

    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
