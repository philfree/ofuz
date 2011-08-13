<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class TasksAddTaskBlock extends BaseBlock{
    public $short_description = 'Task add block';
    public $long_description = 'Add task block in the task page';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Add a Task'));
	  $this->setContent($this->generateAddTaskBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/Task.class.php
      */

      function generateAddTaskBlock(){

	    $output = '';
	    $output .= '<div class="tundra">';
	    $do_task_add = new Task();
	    $output .= $do_task_add->getTaskAddForm();
	    $output .= '</div>';
	    return $output;

      }

      

      
}

?>
