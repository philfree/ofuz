<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class TaskDropBoxBlock extends BaseBlock{
    public $short_description = 'Add project task dropbox code';
    public $long_description = 'Create the dropbox code for the project task.';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Task Drop Box'));
	  $this->setContent($this->generateDropBoxBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/ProjectTask.class.php
      */

      function generateDropBoxBlock(){

	    $output = '';

	    if($_SESSION['do_project_task']->drop_box_code){
	      $output .= _('Use the following Email Id to create a note :').'<br />';
	      $emailid = $_SESSION['do_project_task']->getDropBoxEmail();
	      $output .= '<a href = "mailto:'.$emailid.'">'.$emailid.'</a>';
	    }else{
	      $e_gen_dropboxid = new Event("do_project_task->eventGenerateDropBoxIdTask");
	      $e_gen_dropboxid->addParam("goto", "task.php");
	      $output .= '<br />'._('No drop box code is generated');
	      $output .= '<br />'._('Generate one by').' '.$e_gen_dropboxid->getLink(_('clicking here'));
	    }

	    return $output;

      }

      

      
}

?>
