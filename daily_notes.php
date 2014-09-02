<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: '._('Notes & Discussion');
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');
     
?>

 
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Dashboard');  
 $_SESSION['dashboard_link'] = "daily_notes";
include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns">
      <tr>
	<td class="layout_lcolumn">
	  <?php include_once('plugin_block.php'); ?>
	</td>
	<td class="layout_rcolumn"><div class="min660">
		    <?php $msg = new Message(); 
			  $msg->getMessageFromContext("daily notes");
              echo $msg->displayMessage();
		?>
        <div class="mainheader">
            <div class="pad20">
                <span class="headline14"><?php echo _('Daily Notes & Discussion');?></span>
                <?php
                if (is_object($GLOBALS['cfg_submenu_placement']['daily_notes'] ) ) {
                	echo  $GLOBALS['cfg_submenu_placement']['daily_notes']->getMenu();
                }
                ?>
            </div>
        </div>
     <div class="sub_action_menu">
<?php  

     if(!is_object($_SESSION['adm_project'])){
        $do_adm_project = new Project();
        $do_adm_project->sessionPersistent("adm_project", "index.php", OFUZ_TTL);
     }
     $_SESSION['adm_project']->getAll();
     
     $do_adm_project_task = new ProjectTask();
     $do_adm_task = new Task();
     if(!is_object($_SESSION['adm_project_discuss'])){
        $do_adm_project_discuss = new ProjectDiscuss();
        $do_adm_project_discuss->sessionPersistent("adm_project_discuss", "index.php", OFUZ_TTL);
     }
     if ($_SESSION['adm_project_discuss']->report_date == ''){
        $_SESSION['adm_project_discuss']->report_date = date('Y-m-d');
     }
      
     $e_setPredate = new Event("adm_project_discuss->eventSetPreviousDate");
     $e_setPredate->addParam('date_report',$_SESSION['adm_project_discuss']->report_date);
     $e_setPredate->addParam('goto',$_SERVER['PHP_SELF']);
     
     $e_setNextdate = new Event("adm_project_discuss->eventSetNextDate");
     $e_setNextdate->addParam('date_report',$_SESSION['adm_project_discuss']->report_date);
     $e_setNextdate->addParam('goto',$_SERVER['PHP_SELF']);
     
     $e_selectDate = new Event("adm_project_discuss->eventSetDateSelected");
     $e_selectDate->addParam('goto',$_SERVER['PHP_SELF']);
     

     echo $e_setPredate->getLink(_("Previous Day"))."&nbsp;&nbsp;&nbsp;".$e_setNextdate->getLink(_("Next Day"));
     if($_SESSION['adm_project_discuss']->report_date != date('Y-m-d')){
          $e_setdatetoday = new Event("adm_project_discuss->eventSetDateToday");
          $e_setdatetoday->addParam('goto',$_SERVER['PHP_SELF']);
          echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$e_setdatetoday->getLink(_("Today"));
     }
      
    if(!$_SESSION['adm_project_discuss']->time_spent_on_task){
        $e_sethrstrue = new Event("adm_project_discuss->eventSetHoursWorkedTrue");
        $e_sethrstrue->addParam('goto',$_SERVER['PHP_SELF']);
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$e_sethrstrue->getLink(_("Notes With Hours"));
    }else{
        $e_sethrsfalse = new Event("adm_project_discuss->eventSetHoursWorkedFalse");
        $e_sethrsfalse->addParam('goto',$_SERVER['PHP_SELF']);
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$e_sethrsfalse->getLink(_("All Task"));
    }

     echo '<br /><br />'.strftime("%c", strtotime($_SESSION['adm_project_discuss']->report_date)).'<br />';
     if($_SESSION['adm_project_discuss']->set_user_search){
        echo '<br /><b>'.$_SESSION['adm_project_discuss']->getTotalHoursEntered().' hrs entered by '.$_SESSION['do_User']->getFullName($_SESSION['adm_project_discuss']->for_user).'</b>';
        echo '<br />You may not see some task description if you are not the owner or Co-Worker';

        $e_user_search_false = new Event("adm_project_discuss->eventSetUserSearchFalse");
        $e_user_search_false->addParam('goto',$_SERVER['PHP_SELF']); 
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$e_user_search_false->getLink(_("All Users"));
     }
     echo '</div><div class="dottedline"></div>                 
            <div class="headline_fuscia">'._('Projects').'</div>
			';
    $discussFields = new FieldsForm('ofuz_add_project_discuss');    
    echo _('Jump to Day ').': '.$discussFields->date_select."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$e_selectDate->getLink(_("Go"))."<br />";          
     while($_SESSION['adm_project']->next()){
        if($_SESSION['adm_project']->isProjectOwner($_SESSION['adm_project']->idproject) || $_SESSION['adm_project']->isProjectCoWorker($_SESSION['adm_project']->idproject)){
            if($_SESSION['adm_project_discuss']->isAnyDiscussForProject($_SESSION['adm_project']->idproject)){
				echo' <div class="section20">';
                echo '<b> <span class="project_name"><a href="/Project/'.$_SESSION['adm_project']->idproject.'">'.$_SESSION['adm_project']->name.'</a></span></b><br />';
                $_SESSION['adm_project_discuss']->getDailyWorkDonePerProjectTask($_SESSION['adm_project']->idproject);
                $_SESSION['adm_project_discuss']->query($_SESSION['adm_project_discuss']->getSqlQuery());
				$last_task = 0;
                while($_SESSION['adm_project_discuss']->next()){

                    $e_user_search = new Event("adm_project_discuss->eventSetUserSearchTrue");
                    $e_user_search->addParam("iduser",$_SESSION['adm_project_discuss']->iduser);
                    $e_user_search->addParam('goto',$_SERVER['PHP_SELF']); 

                    //echo '<div class="contacts" >';
                    echo '<div>';
					if ($last_task !=  $_SESSION['adm_project_discuss']->idtask) {
						echo '<br/><span class="ptask_name"><a href="/Task/'.$_SESSION['adm_project_discuss']->idproject_task.'">'.$do_adm_task->getTaskDetail($_SESSION['adm_project_discuss']->idtask).'</a></span>';
					}
                    echo '<br /><i>';
                    echo _('Note By ').$e_user_search->getLink($_SESSION['do_User']->getFullName($_SESSION['adm_project_discuss']->iduser));
                    if ($_SESSION['adm_project_discuss']->hours_work > 0) {
						echo '<br />';
						echo _('Time Worked').' : '.$_SESSION['adm_project_discuss']->hours_work.' '._('hrs') ;
					}
                    echo '<br /></i>';
                    echo  nl2br($_SESSION['adm_project_discuss']->discuss);
                    if($_SESSION['adm_project_discuss']->document != ''){
                      $file_url = "/files/".$_SESSION['adm_project_discuss']->document;
                      $file = '<a href="'.$file_url.'" target="_blank">'.$_SESSION['adm_project_discuss']->document.'</a>';
                      echo '<br /> '._('Attachment').' : '.$file;
                    }
					//echo '<br /><span style="color: rgb(102, 102, 102); font-size: 8pt;">'.date('l, F j', strtotime($_SESSION['adm_project_discuss']->date_added)).'</span>';
                    //echo '</div></div>';
					echo '<div class="dottedline"></div>';
					$last_task = $_SESSION['adm_project_discuss']->idtask;
                }
             // echo '<div class="dottedline"></div>
              //          <div class="section20">';
			  echo '</div></div>';
            }
        }
     }
/** lets hide this for now we will move it to its own page.
	 $do_adm_contacts = new ContactNotes();
	 $do_adm_contact_notes = new ContactNotes();
	 $do_contact = new Contact();
	 $do_adm_contacts->getUserContactsFromNotesDaily($_SESSION['adm_project_discuss']->report_date);
	 echo '<div class="headline_fuscia">'._('Contacts').'</div>';
	 while($do_adm_contacts->next()) {
		if($do_contact->isContactRelatedToUser($do_adm_contacts->idcontact)) {
			$do_adm_contact_notes->getUserContactNotesOnDate($_SESSION['adm_project_discuss']->report_date, $do_adm_contacts->idcontact);
			//echo '<b> <span class="headline_fuscia">'.$do_adm_contacts->cname.'\'s notes</span></b><br />';
			
			echo '<b> <span class="contacts_name"><a href="Contact/'.$do_adm_contacts->idcontact.'">'.$do_adm_contacts->cname.'</a></span></b><br />';
			while($do_adm_contact_notes->next()) {
				    $html_notes = "";
					$html_notes .= '<div class="contacts" >';
					$html_notes .= $do_adm_contact_notes->note;
					if($do_adm_contact_notes->hours_work > 0) {
						$html_notes .=  '<br />'._('Time Recorded').': '.$do_adm_contact_notes->hours_work.' '._('hrs');
					}
					$html_notes .=  '</div>';
					//$html_notes .=  '<div class="dottedline"></div>';
					echo $html_notes;
			}
		}
	 }
 **/   
     
?>
<script type="text/javascript">
 $(document).ready(function(){
 //on load assign date
 var dts = "<?php echo $_SESSION['adm_project_discuss']->report_date;?>" ;
 $('#date_select').val(dts);
 
 
 $("#adm_project_discuss__eventSetDateSelected").click(function(){
 var dt = $('#date_select').val();
 document.cookie='dts='+dt;
  })
 
 });

</script>
    <div class="dottedline"></div>
    </div></td></tr></table>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
