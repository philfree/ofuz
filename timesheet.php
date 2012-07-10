<?php
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Timesheet';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');

   if(!is_object($_SESSION['adm_project_report'])){
        $do_adm_project = new Project();
        $do_adm_project->sessionPersistent("adm_project_report", "index.php", OFUZ_TTL);
     }
     $_SESSION['adm_project_report']->getAll();
     
     $do_adm_project_task = new ProjectTask();
     $do_adm_task = new Task();
     if(!is_object($_SESSION['adm_project_report_discuss'])){
        $do_adm_project_discuss = new ProjectDiscuss();
        $do_adm_project_discuss->sessionPersistent("adm_project_report_discuss", "index.php", OFUZ_TTL);
     }
     
?>

 
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Projects'); 
// $_SESSION['dashboard_link'] = "timesheet";
include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns">
      <tr>
	<td class="layout_lcolumn">
	  <?php include_once('plugin_block.php'); ?>
	</td>
	<td class="layout_rcolumn">
		<div class="min660">
		  <?php $msg = new Message(); 
			  $msg->getMessageFromContext("timesheet");
              echo $msg->displayMessage();
		?>
	        <div class="mainheader">
            <div class="pad20">
                <span class="headline14"><?php echo _('Timesheet'); ?></span>
                <?php
                if (is_object($GLOBALS['cfg_submenu_placement']['timesheet'] ) ) {
                	echo  $GLOBALS['cfg_submenu_placement']['timesheet']->getMenu();
                }
                ?>
            </div>
        </div>		
	<div class="sub_action_menu">

<?php  
	          
                $e_filter = new Event("adm_project_report_discuss->eventSetMonth");
                $e_filter->setLevel(501);
                $e_filter->addParam("goto", $_PHP['SELF']);
				?>
          <form id="setFilterProjHrReport" name="setFilterProjHrReport" method="post" action="/eventcontroler.php">
          <?php echo $e_filter->getFormEvent();
                echo $_SESSION['adm_project_report_discuss']->getYearDropDown();
                echo '&nbsp;&nbsp;';
                echo $_SESSION['adm_project_report_discuss']->getMonthDropDown();
                echo '&nbsp;&nbsp;';
                echo $_SESSION['adm_project_report_discuss']->getWeekDropDowns();  
          ?>
          </form>
          </div>
          <br/><div class="dottedline"></div>     
		  <div class="headline_fuscia"><?php echo _('Projects'); ?></div>
		<?php 				
     $do_prj_discuss = new ProjectDiscuss();

     while($_SESSION['adm_project_report']->next()){ 
        if($_SESSION['adm_project_report']->isProjectOwner($_SESSION['adm_project_report']->idproject) || $_SESSION['adm_project_report']->isProjectCoWorker($_SESSION['adm_project_report']->idproject)){ 
            if($_SESSION['adm_project_report_discuss']->isAnyDiscussOnProjectMonth($_SESSION['adm_project_report']->idproject)){
                echo '<b><span class="project_name"><a href="/timesheet_worklogs.php?p='.$_SESSION['adm_project_report']->idproject.'">'.$_SESSION['adm_project_report']->name.'</a></span></b><br />';
                echo 'Total hours for this project :<b>'.$_SESSION['adm_project_report_discuss']->getTotalHoursEnteredMonthly("",$_SESSION['adm_project_report']->idproject).' hrs</b> <br /><br />';
                /*
				$users_participating = $_SESSION['adm_project_report']->getAllUserFromProjectRel();
                if($users_participating && is_array($users_participating)){
                   echo '<br />';
                   echo _('Users Participating in this project').'<br />';
                   foreach($users_participating as $users_participating){
                      echo '<i>'.$_SESSION['do_User']->getFullName($users_participating).'</i><br />';
                   }
                }
				*/

				$do_prj_discuss->report_month = $_SESSION['adm_project_report_discuss']->report_month;
				$do_prj_discuss->report_year = $_SESSION['adm_project_report_discuss']->report_year;
				$do_prj_discuss->week_start_date = $_SESSION['adm_project_report_discuss']->week_start_date;
				$do_prj_discuss->week_end_date = $_SESSION['adm_project_report_discuss']->week_end_date;

				$do_prj_discuss->getTotalHoursPerTaskMonthly($_SESSION['adm_project_report']->idproject);

				echo '<i>'._('Per Tasks:').'</i><br/>';
				while($do_prj_discuss->next()) {
					if ($do_prj_discuss->tot_task_hrs>0) {
						echo '<b>'.$do_prj_discuss->tot_task_hrs." "._('hrs').'</b> on '.$do_prj_discuss->task_description.'<br />';
					}
				}
		/*$_SESSION['adm_project_report_discuss']->getTotalHoursPerTaskMonthly($_SESSION['adm_project_report']->idproject);

				echo '<i>'._('Per Tasks:').'</i><br/>';
				while($_SESSION['adm_project_report_discuss']->next()) {
					if ($_SESSION['adm_project_report_discuss']->tot_task_hrs>0) {
						echo '<b>'.$_SESSION['adm_project_report_discuss']->tot_task_hrs." "._('hrs').'</b> on '.$_SESSION['adm_project_report_discuss']->task_description.'<br />';
					}
				}*/
		    
		echo '<br /><i>'._('Per Participants:').'</i><br/>';
                $_SESSION['adm_project_report_discuss']->getMonthlyWorkDonePerProjectTask($_SESSION['adm_project_report']->idproject);
                $_SESSION['adm_project_report_discuss']->query($_SESSION['adm_project_report_discuss']->getSqlQuery());

                while($_SESSION['adm_project_report_discuss']->next()){

                    echo '<div >';
                    echo '<div>';
                    //echo '<b>Task Name :'.$do_adm_task->getTaskDetail($_SESSION['adm_project_report_discuss']->idtask).'</b>';

                    //echo '<br />';
                    //echo 'date : '.$_SESSION['adm_project_report_discuss']->date_added.'<br />' ;
					echo '<b>'. $_SESSION['adm_project_report_discuss']->getTotalHoursEnteredMonthly($_SESSION['adm_project_report_discuss']->iduser,$_SESSION['adm_project_report']->idproject).' hrs</b>' ;
					echo '&nbsp;&nbsp;';
                    echo ' by '.$_SESSION['do_User']->getFullName($_SESSION['adm_project_report_discuss']->iduser);
                    //echo _('For the current month total work done : ').$_SESSION['adm_project_report_discuss']->getTotalHoursEnteredMonthly($_SESSION['adm_project_report_discuss']->iduser,"") ;
                    //echo '<br />';
                    echo '</div></div>';
					//echo '<div class="dottedline"></div>';
                }
              echo '<div class="dottedline"></div>
                        <div class="section20">';
				//echo '<div class="section20">';
            }
        }
     }
	 echo '<div class="headline_fuscia">'._('Contacts').'</div>';

	 $do_adm_contacts = new ContactNotes();
	 $do_adm_contact_notes = new ContactNotes();
	 $do_contact = new Contact();
	 $do_adm_contacts->getUserContactsFromNotesMonthly($_SESSION['adm_project_report_discuss']->report_year,$_SESSION['adm_project_report_discuss']->report_month);

	 while($do_adm_contacts->next()) {
		if($do_contact->isContactRelatedToUser($do_adm_contacts->idcontact)) {
			//$monthly_hours = $do_adm_contact_notes->getUserContactNotesMonthlyHours($_SESSION['adm_project_report_discuss']->report_year,$_SESSION['adm_project_report_discuss']->report_month, $do_adm_contacts->idcontact);
			//if($monthly_hours > '0.00') {
				echo '<b>'.$do_adm_contacts->monthly_hours.' '._('hrs').'</b> '._(' spent with ').' <span class="contacts_name"><a href="/Contact/'.$do_adm_contacts->idcontact.'">'.$do_adm_contacts->cname.' </a></span> ';
				echo '<br />';
			//}
			
		}
	 }
	echo '<br />';
     
?>
    <div class="dottedline"></div>
    </div></td></tr></table>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
