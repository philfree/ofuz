<?php

/**
  * A DashboardMessageBlock
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class DashboardMessageBlock extends BaseMessageBlock{
    public $short_description = 'Message Block on Dashboard';
    public $long_description = 'Displays message related to the user on the Dashboard';
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */
      function processBlock(){
          $this->setContent($this->generateDashboardMsgContent());
          $this->displayBlock();
      }

      function generateDashboardMsgContent() {
	    $do_msg = new Message();
            $do_msg->setData(Array("user_firstname" => $_SESSION["do_User"]->firstname));
	    //$message = $do_msg->getMessage("db_msgblock1");
            $message = $do_msg->getMessageFromContext("db_side-info");
	    if($message) {
	       return $message;
	    } else {
	      $this->setIsActive(false);
	    }
	   
      }
}

?>
