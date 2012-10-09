<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

   /**
    * @author SQLFusion
    * @package WorkFeed
    * @license GNU Affero General Public License
    * @version 0.7
    * @date 2012-07-13
   */

class WorkFeedKanbanBlockReasonProjectDiscuss extends WorkFeedItem {
    private $idproject_task;
    private $discuss;
    private $iduser; 
    private $idproject_discuss;
    private $project_name;
    private $idproject;
    private $user_full_name;
    private $task_description;
    private $more = false;
    private $user_picture;
    private $contact_id;
    private $block_unblock_flag;
	
    function display() {
        $do_proj_task_feed = new ProjectTask();
        $do_proj_feed = new Project();
        $do_user = new User();
        if($do_proj_task_feed->isProjectTaskReletedToUser($this->idproject_task)){

            $do_proj_task_feed->getProjectTaskDetails($this->idproject_task);
            $html .= '<br />';

            if($this->user_picture!=''){
                $thumb_name = $_SERVER['DOCUMENT_ROOT'].'/dbimage/thumbnail/'.$this->user_picture;
                if(file_exists($thumb_name)) {
		  $user_pic="/dbimage/thumbnail/".$this->user_picture;
                } else {
		  $user_pic="/images/empty_avatar.gif";
                }              
            }else{
               $user_pic="/images/empty_avatar.gif";         
            }
	    $block_text = ($this->block_unblock_flag == "Block") ? "<b>blocked</b>" : "<b>unblocked</b>" ;
            $user_name = $do_user->getUserNameByIdUser($this->iduser);                
            $html .='<div style="width:50px;float:left;">';                       
            $html .='<a href="/profile/'.$user_name.'"> <img width="34" height="34"alt="" src='.$user_pic.' > </a>';           
            $html .='</div>';                 
            $html .= '<div style="text-align:middle;"> <table width=95% border=0><tr><td>';
            $html .= '<b>'.ucfirst($this->user_full_name).'</b>'.' '.
                      _('has '.$block_text.' the task ').' '.'<a href ="/Task/'.$this->idproject_task.'">'
                      .$this->task_description.'</a>';
            $html .= ' '._('in project ').' '. ' <a href="/Project/'.$this->idproject.'"><i>'.$this->project_name.'</i></a>';            
            $html .= '&nbsp; <img src="/images/discussion.png" width="16" height="16" alt="" />';
            $html .= '<div id="discusspreview'.$this->idproject_discuss.'">';
            $html .= stripslashes($this->discuss);
	    if ($this->more) {
		    $html .='<a onclick="showFullProjDiscuss('.$this->idproject_discuss.'); return false;" href="#">'._('more...').'</a>';
	    }
	    $html .='</div>';
            $html .= '</td></tr></table></div>';
            $html .= '<div style = "color: #666666;font-size: 8pt; margin-left:50px;">';
	    $html .= OfuzUtilsi18n::formatDateLong(date("Y-m-d H:i:s",$this->date_added),true);   
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
      $prefix_note = ($evtcl->block_unblock_flag == "Block") ? "<b>Task Block </b>" : "<b>Task Unblock </b>" ;
      $this->idproject_task = $evtcl->ofuz_idprojecttask;
      $this->discuss = $prefix_note.$evtcl->block_unblock_reason;
      $this->iduser = $_SESSION['do_User']->iduser;		
      $this->idproject_discuss = $evtcl->idproject_discuss;
      $this->idproject = $_SESSION['do_project_task']->idproject;
      $this->block_unblock_flag = $evtcl->block_unblock_flag;

      $do_project = new Project();
      $do_project->getId($this->idproject);
      $this->project_name = $do_project->getProjectName();
      $user = new User();
      $user->getId($this->iduser);

      $this->user_full_name  = $user->getFullName();
 
      $do_contact = new Contact();
      $do_contact->getContactPictureDetails($this->iduser);
      if($do_contact->getNumRows()){
	while($do_contact->next()){
	  $this->user_picture = $do_contact->picture;
	  $this->contact_id = $do_contact->idcontact;
	}
      }

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
