<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class DashboardTodaysTask extends BaseBlock{
    public $short_description = 'Task for the day block';
    public $long_description = 'Lists all the tasks for the day';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Today\'s Tasks'), '/tasks.php', _('All Tasks'));
	  $this->setContent($this->generateTodaysTasksDisplay());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * @return string: HTML output, when there is at least one task for today.
       * @return void : if there is not task for today.
       * @see class/Task.class.php
      */

      function generateTodaysTasksDisplay(){

	  $output = '';
	  $do_task = new Task();
	  $do_task->getAllTasksToday();
	  if ($do_task->getNumRows()) {
	    $output .= '<div class="task_today">';
	    while ($do_task->next()) {
	      $output .= "\n".'<div id="t'.$do_task->idtask.'" class="task_item">';
       $output .="<table><tr><td>";
	      $output .= '<input type="checkbox" name="c'.$do_task->idtask.'" class="task_checkbox" onclick="fnTaskComplete(\''.$do_task->idtask.'\')" /></td>';
	      $output .= '<td><span class="task_desc">'.$do_task->task_category.' '.$do_task->task_description.'.</span></td></tr></table></div>';
	    }
	    $output .= '</div>';
	    return $output;
	  } else {
	    $this->setIsActive(false);
	  }

      }

} //end of class

?>