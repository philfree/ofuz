<?php 
/**COPYRIGHTS**/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

    /**
    * WorkFeedContactNote class
    * Copyright 2001 - 2008 SQLFusion LLC, Author: Philippe Lewicki, Abhik Chakraborty ,Jay Link info@sqlfusion.com 
    *
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package WorkFeed
    * @license ##License##
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
   */

class WorkFeedProjectDiscuss extends WorkFeedItem {
    private $idproject_task;
    private $discuss;
    private $iduser; 
	private $idproject_discuss;
	private $project_name;
	private $idproject;
	private $user_full_name;
	private $task_description;
	private $more = false;
	
    function display() {
        $do_proj_task_feed = new ProjectTask();
        $do_proj_feed = new Project();
        if($do_proj_task_feed->isProjectTaskReletedToUser($this->idproject_task)){
            //$idproject = $do_proj_task_feed->getProjectForTask($this->idproject_task);
            $do_proj_task_feed->getProjectTaskDetails($this->idproject_task);
            $html .= '<br />';
            $html .= '<div style="width:25px;float:left;">';
            $html .= '<img src="/images/discussion.png" width="16" height="16" alt="" />';
            $html .= '</div>';
            $html .= '<div style="text-align:middle;">';
            $html .= '<b>'.$this->user_full_name.'</b>'.' '.
                      _('has added a note on discussion').' '.'<a href ="/Task/'.$this->idproject_task.'">'
                      .$this->task_description.'</a>';
            $html .= ' '._('in project ').' '. ' <a href="/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';
            $html .= '<div id="discusspreview'.$this->idproject_discuss.'">';
            $html .= stripslashes($this->discuss);
			//$html .= htmlentities($this->discuss);
			if ($this->more) {
				$html .='<a onclick="showFullProjDiscuss('.$this->idproject_discuss.'); return false;" href="#">'._('more...').'</a>';
			}
			$html .='</div>';
            $html .= '</div>';
            $html .= '<div style = "color: #666666;font-size: 8pt; margin-left:25px;">';
           // $html .= date('l, F j,  g:i a', $this->date_added);
	    $html .= OfuzUtilsi18n::formatDateLong(date("Y-m-d H:i:s",$this->date_added),true);
	    //$html .= '  '.$this->date_added;
	    
	    
            $html .= '</div>';
            $html .='<br />';
            $html .= '<div class="dottedline"></div>';
			$html .= '<div id="'.$this->idworkfeed.'" class="message_box"></div>';
        }
        
        return $html;
    }
    /**
	 *  Add the note to the workfeed
	 *  Select all other Co-Worker on the project and push the note to them.
	 *  Every Co-Worker in the project get the feed except the one posting the note.
	 *  @param EventControler object
	 */
    function eventAddFeed(EventControler $evtcl) {
		//print_r($_SESSION['ProjectDiscussEditSave']);exit;
        $this->idproject_task = $_SESSION['ProjectDiscussEditSave']->idproject_task;
        $this->discuss = $_SESSION['ProjectDiscussEditSave']->discuss;
        $this->iduser = $_SESSION['ProjectDiscussEditSave']->iduser;		
		$this->idproject_discuss = $_SESSION['ProjectDiscussEditSave']->idproject_discuss;
		$this->idproject = $_SESSION['do_project_task']->idproject;
		$do_project = new Project();
		$do_project->getId($this->idproject);
		$this->project_name = $do_project->getProjectName();
		$user = new User();
		$user->getId($this->iduser);
		$this->user_full_name  = $user->getFullName();
		$do_proj_task_feed = new ProjectTask();
		$do_proj_task_feed->getProjectTaskDetails($this->idproject_task);
		$this->task_description = $do_proj_task_feed->task_description;
		
		if(strlen($this->discuss) > 200 ){ 
			 $this->discuss = substr($this->discuss, 0, 200);
			 $this->more = True;
		} else { $this->more = False; }
		
		$do_project_sharing = new ProjectSharing();
		$project_users = $do_project_sharing->getCoWorkersAsArray($do_project);		
		$project_users[] = $do_project->getProjectOwner();
		$users = Array();
		foreach ($project_users as $project_user) {
			if ($_SESSION['do_User']->iduser != $project_user) {
				$users[] = $project_user;
			}
		}
        $this->addFeed($users);
    }
}
