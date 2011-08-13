<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ProjectAddTaskDropboxBlock extends BaseBlock{
    
  public $short_description = 'Add project dropbox block';
  public $long_description = 'Create the dropbox code for the project';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Add Task Dropbox'));
	  $this->setContent($this->generateAddTaskDropboxBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
      */

      function generateAddTaskDropboxBlock(){

	    $output = '';
	    $output .= _('Use the following drop box to add a task for this project.');
	    $output .= '<br />';
	    $email_drop_box = 'newtask-'.$_SESSION["do_project"]->idproject.'@ofuz.net';
	    $output .= '<a href = "mailto:'.$email_drop_box.'">'.$email_drop_box.'</a>';
	    return $output;

      }

      

      
}

?>
