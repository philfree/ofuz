<?php

/**
  * A ContactCompletedBlock
  * This is set a block on the left side of contact.php with contact details
  * @author SQLFusion's Dream Team <info@sqlfusion.com>
  * @package OfuzCore
  * @license GNU Affero General Public License
  * @version 1.2
  * @date 2016-01-15
  * @since 1.2
  */

class ContactCompletedBlock extends BaseBlock{
    
    public $short_description = 'Contact completed block';
    public $long_description = 'Shows the detail of the task completed for the contact';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */
      function processBlock(){
    	  $this->setTitle(_('Latest 10 Completed Tasks'));
    	  $this->setContent($this->generateTaskDetails());
    	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generateTaskDetails(){
    	   $do_contact = new Contact();
    	   $idcontact = $_SESSION['ContactEditSave']->idcontact;
           $completed_task .= $do_contact->getTaskCompletedDetails($idcontact);
           return $completed_task;
      }

     
}

?>