<?php
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Block object to display an Add task button and then Form
     * calls the method: Task->getTaskAddContactRelatedForm()
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.6
     * @see OfuzCore.Task#getTaskAddContactRelatedForm()
     */
 

class ContactAddTaskBlock extends BaseBlock{
    public $short_description = 'Add task for contact block';
    public $long_description = 'Add task from the contact detail page related to that contact';
    
      /**
    	* processBlock() , This method must be added  
    	* Required to set the Block Title and The Block Content Followed by displayBlock()
    	* Must extent BaseBlock
        */
      function processBlock(){
	  $this->setTitle(_('Add a Task'));
	  $this->setContent($this->generateAddContactTaskForm());
	  $this->setButtonOnClickDisplayBlock(_("add new task"),"addatask","#","addTask()","addatask_button");
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generateAddContactTaskForm(){
          $output = '';
          if ($_SESSION['in_page_message']!= 'follow_up_task' ){
                $do_task_add = new Task();
                $output .=$do_task_add->getTaskAddContactRelatedForm();
          
          }else{
                $this->setIsActive(false);
          }
                return $output;
      }      
}

?>