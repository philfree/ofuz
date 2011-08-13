<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class TaskProgressBlock extends BaseBlock{
      public $short_description = 'Task Progress block';
      public $long_description = 'Manage the progress of the task';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Progress'));
	  $this->setContent($this->generateProgressBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
       * @see class/ProjectTask.class.php
       * @see class/OfuzHorizontalSlider.class.php
      */

      function generateProgressBlock(){

	    $output = '';
	    $output .= '<form method="get" action="'.$_SERVER['PHP_SELF'].'">';

	    $do_project_task_progress = new ProjectTask();
	    $do_project_task_progress->getId($_SESSION['do_project_task']->idproject_task);
	    $do_project_task_progress->sessionPersistent('do_project_task_progress', 'project.php', OFUZ_TTL);
	    $_SESSION['do_project_task_progress']->setFields(new Fields());
	    $_SESSION['do_project_task_progress']->fields->addField(new OfuzHorizontalSlider("progress"));
	    $_SESSION['do_project_task_progress']->setApplyRegistry(true, "Form");
	    $output .= $_SESSION['do_project_task_progress']->progress;

	    $output .= '</form>';

	    return $output;

      }

      

      
}

?>
