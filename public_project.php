<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Project';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
   
    include_once('includes/header.inc.php');

    if (isset($_GET['idproject'])) {
        $idproject = $_GET['idproject'];
    }
    $project_access = false;
    $project_operation_access = false;
    $do_project = new Project();
    
    if(!is_object($_SESSION['do_list_project_task'])){
        $do_project_task = new ProjectTask();
        $do_project_task->sessionPersistent("do_list_project_task", "projects.php", OFUZ_TTL);
    }
    //check the access

    

    if($do_project->isPublicProject($idproject) === true){
            $project_access = true;
            $project_operation_access = false;
        }
    
    if($project_access){
        $do_project->getId($idproject);
        $do_project->sessionPersistent("do_project", "project.php", OFUZ_TTL);
    
        $project_details = $do_project->getProjectDetails($idproject);
     
    }
?>
<script type="text/javascript">
    //<![CDATA[
	
	
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
    <div class="layout_header">
        <div class="layout_logo">
            <a href="/index.php"><img src="/images/ofuz_logo.jpg" width="188" height="90" alt="" /></a>
        </div>
    </div>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$project_access){
             echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo 'unauthorized';
              echo '</div></div><br /><br />';
              exit;
              
             
          }
    ?>
   
    <table class="layout_columns"><tr>
    
    
    
    
    <td class="layout_rcolumn">
	    <?php
            $msg = new Message(); 
			if ($msg->getMessageFromContext("project tasks")) {
				//echo $msg->displayMessage();
			}
        ?> 
        <div class="mainheader">
            <div class="project_detail_name">
                <span class="headline14"><?php echo $project_details['name']; ?></span>
                <span class="project_edit">
                  
                </span>
            </div>
        </div>
                 
         </div>
        <div id="project_ctlbar" style="display: none;">
            
            
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
                  echo $_SESSION['do_list_project_task']->viewProjectTasks('Public');
                }

             ?>
             </div>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php //include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
