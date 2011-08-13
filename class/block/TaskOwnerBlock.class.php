<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class TaskOwnerBlock extends BaseBlock{
    public $short_description = 'Task Owner block in the project task page';
    public $long_description = 'Shows the currect task owner and the option to change the task owner.';
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Task Owner'));
	  $this->setContent($this->generateTaskOwnerBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/ProjectTask.class.php
      */

      function generateTaskOwnerBlock(){

	    $output = '';

	    $output .= '<b>'.$_SESSION['do_project_task']->getTaskOwnerName().'</b>';
	    $output .= '<br /><br />'; 
	    $output .= '<a href="#" onclick="showCoWorkers();return false;">'._('Change the task owner').'</a>';
	    $output .= '<div id="task_co_worker" style="display:none;">'; 
	    $e_change_task_owner = new Event("do_project_task->eventChangeTaskOwner");
	    $e_change_task_owner->setLevel(100);
	    $e_change_task_owner->addEventAction('WorkFeedProjectTask->eventAddFeed', 140);
	    $e_change_task_owner->addParam('task_event_type','change_task_owner');
	    $e_change_task_owner->addParam("idtask", $_SESSION["do_project_task"]->idtask);
	    $e_change_task_owner->addParam("goto", "Task/".$_SESSION['do_project_task']->idproject_task);
	    $output .= $e_change_task_owner->getFormHeader();
	    $output .= $e_change_task_owner->getFormEvent();
	    $output .= $_SESSION['do_project_task']->renderChangeTaskOwnerList();
	    $output .= '<br /><br /><a href="#" onclick="hideCoWorkers(); return false;">'._('Hide').'</a>';
	    $output .= $e_change_task_owner->getFormFooter();
	    $output .= '</div>';

	    return $output;

      }

      

      
}

?>
