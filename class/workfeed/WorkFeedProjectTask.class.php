<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
    /**
    * WorkFeedProjectDiscuss class
    * Copyright 2001 - 2008 SQLFusion LLC, Author: Philippe Lewicki, Abhik Chakraborty ,Jay Link info@sqlfusion.com 
    *
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package WorkFeed
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
   */

class WorkFeedProjectTask extends WorkFeedItem {
    private $idproject_task;
    private $idtask;
    private $iduser; 
    private $idproject; 
    private $task_description;
    private $task_event_type ;
    private $progress;
    private $task_name;
    private $due_date;
    private $task_category;
	private $project_name;
	private $user_full_name;
	
    function display() {
        //$do_proj_task_feed = new ProjectTask();
        
       // if($do_proj_task_feed->isProjectTaskReletedToUser($this->idproject_task)){
            $type = $this->task_event_type ;
            $html = '<br />';
            $html .= '<div style="width:50px;float:left;">';
            $html .= '<img src="/images/note_icon.gif" width="16" height="16" alt="" />';
            $html .= '</div>';
            $html .= '<div style="text-align:middle;">';
            switch($type){
                case 'change_task_owner' :
                    $html .= _('Ownership of the task').' ';
                    $html .= '<a href = "/Task/'.$this->idproject_task.'">'.$this->task_description.'</a>';
                    $html .= ' '._('on project').' '. '<a href= "/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
                    $html .= '<br />'.' '._('has been changed to').' '.'<b>'.$this->user_full_name.'</b>';
                    break;
                case 'close_task' :
                      $html .= '<a href = "/Task/'.$this->idproject_task.'">'.$this->task_description.'</a>';
                      $html .= ' '._('on project').' '.'<a href= "/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
                      $html .= '<br />'.' '._('has been closed by ').'<b>'.$this->user_full_name.'</b>';
                      break;
                    break;
                case 'open_task' :
                      $html .= '<a href = "/Task/'.$this->idproject_task.'">'.$this->task_description.'</a>';
                      $html .= _('on project').' '.'<a href= "/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
                      $html .= '<br />'.' '._('has been re-opened by').' '.'<b>'.$this->user_full_name.'</b>';
                      break;

                case 'task_progress' :
                      $html .= '<a href = "/Task/'.$this->idproject_task.'">'.$this->task_description.'</a>';
                      $html .= ' '._('on project').' '.'<a href= "/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
                      $html .= '<br />'.' '._('has been set to ').' '.'<b> '.$this->progress.' '.'%'.' '._('by').' '. $this->user_full_name.'</b>';
                      break;
                case 'task_name_change' :
                      $html .= $this->task_description.' '._('on project').' '.'<a href= "/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
                      $html .='<br />'.' '._('is renamed as ').' '.'<a href = "/Task/'.$this->idproject_task.'">'.$this->task_name.'</a>';
                      $html .=_('by').'<b>'.$this->user_full_name.'</b>';
                      break;
                case 'task_category_changed';
                      $html .= _('Category of ').' '.'<a href = "/Task/'.$this->idproject_task.'">'.$this->task_description.'</a>';
                      $html .= ' '._('on project').' '.'<a href= "/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
                      $html .= '<br />'.' '._('has been changed to').' '.'<i>'.$this->task_category.'</i><b> '.' '._('by').' '. $this->user_full_name.'</b>';
                      break;

                case 'task_due_date_changed';
                      
                      $html .= _('Due date of').' '.'<a href = "/Task/'.$this->idproject_task.'">'.$this->task_description.'</a>';
                      $html .= ' '._('on project').' '.'<a href= "/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
                      $html .= '<br />'.' '._('has been changed to').' '.'<i>'.$this->due_date.'</i><b>'.' '._('by').' '. $this->user_full_name.'</b>';
                      break;
                  case 'new_task_add' :
                    $html .= _('A new task').' ';
                    $html .= '<a href = "/Task/'.$this->idproject_task.'">'.$this->task_description.'</a>';
                    $html .= ' '._('on project').' '.'<a href= "/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
                    $html .= '<br />'.' '._('has been added by').' '.'<b>'.$this->user_full_name.'</b>';
                    break;
             }
            $html .= '</div>';
            $html .= '<div style = "color: #666666;font-size: 8pt; margin-left:50px;">';
            //$html .= date('l, F j,  g:i a', $this->date_added);
            $html .= OfuzUtilsi18n::formatDateLong(date("Y-m-d H:i:s",$this->date_added),true);
            $html .= '</div>';
            $html .='<br />';
            $html .= '<div class="dottedline"></div>';
			$html .= '<div id="'.$this->idworkfeed.'" class="message_box"></div>';
        //}
       
        return $html;
    }

    function eventAddFeed(EventControler $evtcl) {
        $type = $evtcl->task_event_type;
		
		$do_project = new Project();
		$do_project->getId($_SESSION['do_project_task']->idproject);
		$this->project_name = $do_project->getProjectName();
		$do_project_sharing = new ProjectSharing();
		$project_users = $do_project_sharing->getCoWorkersAsArray($do_project);		
		$project_users[] = $do_project->getProjectOwner();
		$users = Array();
		foreach ($project_users as $project_user) {
			if ($_SESSION['do_User']->iduser != $project_user) {
				$users[] = $project_user;
			}
		}
		$this->user_full_name = $_SESSION['do_User']->getFullName();				
        switch($type){
            case 'change_task_owner' :
                  $fields = $evtcl->fields;
                  $idcoworker =  $fields["co_worker"];
                  $this->iduser = $idcoworker;
				  $do_user = new User();
				  $do_user->getId($idcoworker);
				  $this->user_full_name = $do_user->getFullName();
                  $this->idproject_task = $_SESSION['do_project_task']->idproject_task;
                  $this->idtask = $_SESSION['do_project_task']->idtask;
                  $this->idproject = $_SESSION['do_project_task']->idproject;
                  $this->task_description = $_SESSION['do_project_task']->task_description;
                  $this->task_event_type = $type;
                  $this->addFeed($users);
                  break;
            case 'update_task' :
                  $status = $evtcl->status;
                  $this->iduser = $_SESSION['do_User']->iduser;
                  $this->idproject_task = $_SESSION['do_project_task']->idproject_task;
                  $this->idtask = $_SESSION['do_project_task']->idtask;
                  $this->idproject = $_SESSION['do_project_task']->idproject;
                  $this->task_description = $_SESSION['do_project_task']->task_description;
                  if($evtcl->task_description != $_SESSION['do_project_task']->task_description){
                      $this->task_event_type = 'task_name_change';
                      $this->task_name = $evtcl->task_description;
                      $this->addFeed($users);
                  }
                  if($evtcl->status != $_SESSION['do_project_task']->status){
                        if($status == 'open'){
                              $this->task_event_type = 'open_task';
                        }else{ $this->task_event_type = 'close_task'; }
                      $this->addFeed($users);
                  }
                  if($evtcl->task_category !=  $_SESSION['do_project_task']->task_category){
                     $this->task_event_type = 'task_category_changed';
                     $this->task_category = $evtcl->task_category;
                     $this->addFeed($users);
                  }
                  if($evtcl->due_date != $_SESSION['do_project_task']->due_date){
                     $this->task_event_type = 'task_due_date_changed';
                     $this->due_date = $evtcl->due_date; 
                     $this->addFeed($users);
                  }

                  break;
             case 'task_progress' :
                  $this->iduser = $_SESSION['do_User']->iduser;
                  $this->idproject_task = $_SESSION['do_project_task']->idproject_task;
                  $this->idtask = $_SESSION['do_project_task']->idtask;
                  $this->idproject = $_SESSION['do_project_task']->idproject;
                  $this->progress = $evtcl->progress;
                  $this->task_description = $_SESSION['do_project_task']->task_description;
                  $this->task_event_type = $type;
                  $this->addFeed($users);
                  break;
            case 'new_task_add' :
                  $this->iduser = $_SESSION['do_User']->iduser;
                  $this->idproject_task = $_SESSION['do_add_project_task']->idproject_task;
                  $this->idtask = $_SESSION['do_add_project_task']->idtask;
                  $this->idproject = $_SESSION['do_add_project_task']->idproject;
                  $this->progress = $evtcl->progress;
                  $this->task_description = $_SESSION['do_add_project_task']->task_description;
                  $this->task_event_type = $type;
                  $this->addFeed($users);
                  break;
        }     
    }
    
     /**
	 *  Add the Project note to the wrokfeed from the drop box mail. 
	 *  Will do the similar steps as it does for adding new project note 
	 *  @param User id, Projecttask id, Project id, task id, task descrpition
	 */
    function eventaddFeedFromDropbox($iduser,$idproject_task,$idproject,$idtask,$task_description){
		
		$do_project = new Project();
		$do_project->getId($idproject);
		$this->project_name = $do_project->getProjectName($idproject);
		$do_project_sharing = new ProjectSharing();
		$project_users = $do_project_sharing->getCoWorkersAsArray($do_project);		
		$project_users[] = $do_project->getProjectOwner();
		$users = Array();
		foreach ($project_users as $project_user) {
			if ($iduser != $project_user) {
				$users[] = $project_user;
			}
		}
		$do_user = new User();
		$this->user_full_name = $do_user->getFullName($iduser);
		
    	$this->iduser = $iduser;
	    $this->idproject_task = $idproject_task;
	    $this->idtask = $idtask;
	    $this->idproject = $idproject;
	    $this->task_description = $task_description;
	    $this->task_event_type = 'new_task_add';
	    $this->addFeed($users);
		
	}
    
}
