<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class GitRepoAdd extends BaseBlock{
      public $short_description = 'Add Git-Repository block';
      public $long_description = 'Opensource version of git-repository add via projects';
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Add a Git-Repository'));
	  $this->setContent($this->generateAddGitRepositoryBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * 
      */

      function generateAddCoWorkerBlock(){

	    $output = '';
	    $output .= '<div class="tundra">';
	    if(isset($_GET["message"])){
		//$output .= htmlentities($_GET["message"]).'<br />';
	    }
	    $output .= $_SESSION['do_coworker']->generateFromAddCoWorkerOS();
	    $output .= '</div>';
	    return $output;
      }
}

