<?php
// Copyright 2010 SQLFusion LLC  info@sqlfusion.com
// All rights reserved
/**COPYRIGHTS**/
/**
  * An Marketing Block plugin class
  * The class must extends the BaseBlock
  * setTitle() will set the Block Title
  * setContent() will set the content
  * displayBlock() call will display the block
  * isActive() is set to true by default so to inactivate the block uncomment the method isActive();
  * @package Marketing
  * @author Philippe Lewicki <phil@sqlfusion.com>
  * @license ##License##
  * @version 0.1
  * @date 2010-11-08
  */


class BlockZendeskTicket extends BaseBlock{
      public $short_description = 'Marketing Block';
      public $long_description = 'Marketing Block';
    
       /**
        * processBlock() , This method must be added  
        * Required to set the Block Title and The Block Content Followed by displayBlock()
        * Must extent BaseBlock
        */
      function processBlock(){
        $this->setTitle("Zend Desk Ticket111");
        $content = $this->getZendBlockConent();
        if(!empty($content)){
			$this->setContent($content);
			$this->displayBlock();
		}
      }
      
      function getZendBlockConent(){
		  
		  //echo $_GET['idprojecttask'];
		  
		  $do_project_task = new ProjectTask();
		  $idtask = $do_project_task->getTaskId($_GET['idprojecttask']);
		  
		  $data = $do_project_task->getProjectTaskDetails($idtask);
		  $idproject = $data['idproject'];
		  
		  $do_zend = new Zendesk();
		  if($do_zend->zendeskProjectUserRelation($_SESSION['do_User']->iduser,$idproject)){
			  
				$output .= '<a href="#" onclick="showZBox();return false;">'._('Add Zendesk Ticket ID').'</a>';
				$output .= '<div id="task_zbox" style="display:none;">'; 
				$e_zticket = new Event("do_zend->eventAddZendTicket");
				$e_zticket->setLevel(100);
				$e_zticket->addParam("idtask", $idtask);
				$e_zticket->addParam("goto", "Task/".$_SESSION['do_project_task']->idproject_task);
				$output .= $e_zticket->getFormHeader();
				$output .= $e_zticket->getFormEvent();
				$output .= '<input type="text" name="z_ticket_id" id = "z_ticket_id">';
				$output .='<input value="'._('Add Zend Ticket').'" type="submit">';
				$output .= '<br /><br /><a href="#" onclick="hideZbox(); return false;">'._('Hide').'</a>';
				$output .= $e_zticket->getFormFooter();
				$output .= '</div>';
			  
		  }
		  
		  return $output;
	  }
}

?>
