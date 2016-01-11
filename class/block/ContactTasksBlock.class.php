<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ContactTasksBlock extends BaseBlock{
      public $short_description = 'Contact task block';
      public $long_description = 'Shows the task related to the contact';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */
      function processBlock(){
	  $this->setTitle(_('Contact Tasks'));
	  $this->setContent($this->generateContactTasksDisplay());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generateContactTasksDisplay(){
	  $output = '';
	  $idcontact = $_SESSION['ContactEditSave']->idcontact ;
	  $show_tasks_box = false;
	  $ContactRelatedOverdueTask = '';
	  $do_task_show = new Task();
	  $do_task_show->getContactRelatedOverdueTask($idcontact);
	  if ($do_task_show->getNumRows()) {
	      $ContactRelatedOverdueTask = $do_task_show->viewContactsTasks();
	      $show_tasks_box = true;
	  }
	  $ContactRelatedTodayTask = '';
	  $do_task_show->getContactRelatedTodayTask($idcontact);
	  if ($do_task_show->getNumRows()) {
	      $ContactRelatedTodayTask = $do_task_show->viewContactsTasks();
	      $show_tasks_box = true;
	  }
	  $ContactRelatedTomorrowTask = '';
	  $do_task_show->getContactRelatedTomorrowTask($idcontact);
	  if ($do_task_show->getNumRows()) {
	      $ContactRelatedTomorrowTask = $do_task_show->viewContactsTasks();
	      $show_tasks_box = true;
	  }
	  $ContactRelatedThisWeekTask = '';
	  $do_task_show->getContactRelatedThisWeekTask($idcontact);
	  if ($do_task_show->getNumRows()) {
	      $ContactRelatedThisWeekTask = $do_task_show->viewContactsTasks();
	      $show_tasks_box = true;
	  }
	  $ContactRelatedNextWeekTasks = '';
	  $do_task_show->getContactRelatedNextWeekTasks($idcontact);
	  if ($do_task_show->getNumRows()) {
	      $ContactRelatedNextWeekTasks = $do_task_show->viewContactsTasks();
	      $show_tasks_box = true;
	  }
	  $ContactRelatedLaterTasks = '';
	  $do_task_show->getContactRelatedLaterTasks($idcontact);
	  if ($do_task_show->getNumRows()) {
	      $ContactRelatedLaterTasks = $do_task_show->viewContactsTasks();
	      $show_tasks_box = true;
	  }   
	  if ($show_tasks_box === true) { 
      $output.= '<b>'._('Tasks Related to this Contact').'</b><br />';
      if ($ContactRelatedOverdueTask != '') { 
        $output.= '<div class="headline10" style="color: #ff0000;">Overdue</div>';
        $output.=  $ContactRelatedOverdueTask; 
      } 
      if ($ContactRelatedTodayTask != '') { 
        $output.= '<div class="headline10">Today</div>';
        $output.= $ContactRelatedTodayTask; 
      } 
      if ($ContactRelatedTomorrowTask != '') { 
        $output.= '<div class="headline10">Tomorrow</div>';
        $output.= $ContactRelatedTomorrowTask; 
      } 
      if ($ContactRelatedThisWeekTask != '') {
        $output.= '<div class="headline10">This week</div>';
        $output.= $ContactRelatedThisWeekTask; 
      } 
      if ($ContactRelatedNextWeekTasks != '') { 
        $output.= '<div class="headline10">Next week</div>';
        $output.= $ContactRelatedNextWeekTasks; 
      } 
      if ($ContactRelatedLaterTasks != '') { 
        $output.=  '<div class="headline10">Later</div>';
        $output.= $ContactRelatedLaterTasks; 
      } 
      return $output;
	  }else{ 
	      $this->setIsActive(false);
	  }
		  
      }

     
}

?>
