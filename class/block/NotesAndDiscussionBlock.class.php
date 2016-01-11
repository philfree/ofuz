<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class NotesAndDiscussionBlock extends BaseBlock{
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Notes & Discussion'));
	  $this->setContent($this->generateNotesAndDiscussionBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
      */

      function generateNotesAndDiscussionBlock(){

	    $output = '';
	    $output .= "Coming Soon...";
	    return $output;

      }

      

      
}

?>
