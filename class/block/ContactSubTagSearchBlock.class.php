<?php
/**COPYRIGHTS**/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

/**
  * A ContactSubTagSearchBlock
  * contacts.php has persistent session object as $_SESSION['do_Contacts']
  * with the current list of contacts displayed. 
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ContactSubTagSearchBlock extends BaseBlock{
  public $short_description = 'Contacts subtag search block';
  public $long_description = 'Search contacts by the subtags';
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */
      function processBlock(){
	  $this->setTitle(_('Narrow your search'));
	  $this->setContent($this->generateTagList());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generateTagList(){
	    $output = '';    
	    $SearchTags = $_SESSION['do_Contacts']->getSearchTags();
        if (!empty($SearchTags)) {
          $do_subtag_list = new Tag();
          $UserSubTagList = trim($do_subtag_list->getUserSubTagList('contact'));
          if (!empty($UserSubTagList)) {
              $output = $UserSubTagList;
          }
        } else { $this->setIsActive(false); }

	    return $output;
      }
   
}

?>