<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Daily Work Log';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');
  
  $idproject = $_GET['p']; 
  
?>

 
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Dashboard'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11">Work Logs </span>
        </div>
    </div>
    <div class="contentfull">
        
<?php  

     
     echo '<div class="dottedline"></div>
                    <div class="section20">';
    $do_billing_proj = new Project();
    $do_adm_task = new Task();
    $do_billing_proj_discuss = new ProjectDiscuss();
    $do_billing_proj_discuss->report_month = $_SESSION['adm_project_report_discuss']->report_month;
    $do_billing_proj_discuss->report_year = $_SESSION['adm_project_report_discuss']->report_year;
    $do_billing_proj_discuss->week_start_date = $_SESSION['adm_project_report_discuss']->week_start_date;
    $do_billing_proj_discuss->week_end_date = $_SESSION['adm_project_report_discuss']->week_end_date;

    $project_name = $do_billing_proj->getProjectName($idproject);
    
        if($do_billing_proj->isProjectOwner($idproject) || $do_billing_proj->isProjectCoWorker($idproject)){
                echo '<b><span class="headline_fuscia">Project Name : '.$project_name.'</span></b>&nbsp;&nbsp;&nbsp;<a href="timesheet.php">back</a><br />';
           $do_billing_proj_discuss->getDistinctTaskForProjectWithDiscussion($idproject,$_SESSION['adm_project_report_discuss']->report_month);
           while($do_billing_proj_discuss->next()){
                echo '<div class="contacts" >';
                echo '<div>';
                echo '<b>Task Name :'.$do_adm_task->getTaskDetail($do_billing_proj_discuss->idtask).'</b>';
                echo '<br />';
                echo '<br />';
                $_SESSION['adm_project_report_discuss']->getMonthlyBillableHoursWithDiscussion($idproject,$do_billing_proj_discuss->idtask);
                $_SESSION['adm_project_report_discuss']->query($_SESSION['adm_project_report_discuss']->getSqlQuery());
                while($_SESSION['adm_project_report_discuss']->next()){
                    echo '<i>Added By  '.$_SESSION['do_User']->getFullName($_SESSION['adm_project_report_discuss']->iduser).' on :<b> '.$_SESSION['adm_project_report_discuss']->date_added.'</b> </i>';
                    echo '<br />';
                    echo '<b><i>Time Worked : '.$_SESSION['adm_project_report_discuss']->hours_work.' hrs</i></b>' ;
                    echo '<br />';
                    echo  nl2br($_SESSION['adm_project_report_discuss']->discuss);
                    if($_SESSION['adm_project_report_discuss']->document != ''){
                      $file_url = "/files/".$_SESSION['adm_project_report_discuss']->document;
                      $file = '<a href="'.$file_url.'" target="_blank">'.$_SESSION['adm_project_report_discuss']->document.'</a>';
                      echo '<br /> Attachment : '.$file;
                    }
                    echo '<br />';
                    echo '<br />';
                }
                echo '</div></div>';
             
          }
           echo '<div class="dottedline"></div>
                        <div class="section20">';
        }else{
            echo _('You are trying to access a project which you are not realted to. Please check the URL');
        }
     

?>
<div class="spacerblock_80"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>