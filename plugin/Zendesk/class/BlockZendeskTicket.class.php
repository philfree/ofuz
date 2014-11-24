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
        $this->setTitle("Zend Desk Ticket");
        $content = $this->getZendBlockConent();
        if(!empty($content)){
			$this->setContent($content);
			$this->displayBlock();
		}
      }
      
      function getZendBlockConent(){
		  
		 // echo $_GET['idprojecttask'];
		  
		  $do_project_task = new ProjectTask();
		  $idtask = $do_project_task->getTaskId($_GET['idprojecttask']);
		 // echo $idtask;
		  $data = $do_project_task->getProjectTaskDetailsByTaskId($idtask);
		  //echo $data->getData('idproject');
		  $idproject = $data->getData('idproject');
		  //$idproject = $data['idproject'];
		  //echo '-'.$idproject;
		  $do_zend = new Zendesk();
		  $ticket = $do_zend->getZendTicketId($_SESSION['do_User']->iduser,$_GET['idprojecttask']);
		  $ticket_id = $ticket['ticket'];
		  if(!empty($ticket_id)){
			  $idzendesk_task_ticket_releation = $ticket['zendesk_task_ticket_releation'];
			  
			  $output .= '<div class="co_worker_item"><div class="co_worker_desc">' ;
              $output .= '<div id="invite" class="co_worker_item co_worker_desc">'; 
              $output .= '<div style="position: relative;">';
			  $output .= '<b>Ticket ID: '.$ticket_id.'</b>';
				  $e_remove_invitation =  new Event("Zendesk->eventRemoveZendTicket");
				  $e_remove_invitation->addParam('idzendesk_task_ticket_releation',$idzendesk_task_ticket_releation);
				  $e_remove_invitation->addParam("goto",$_SERVER['PHP_SELF']);
			  
				 $img_del = '<img src="/images/delete.gif" width="14px" height="14px" alt="" />';
                  $output .= '<div width="15px" id="trashcan" class="deletenote" style="right:0;">'.$e_remove_invitation->getLink($img_del).'</div>';
                  $output .= '</div></div>';
                  $output .= '</div></div>';
		  }
		  
		  if($do_zend->zendeskProjectUserRelation($_SESSION['do_User']->iduser,$idproject)){
				
				$ticket_id = $do_zend->getZendTicketId($_SESSION['do_User']->iduser,$_GET['idprojecttask']);
				
			    $output .= '<a href="#" onclick="showZBox();return false;">'._('Add/Update Zendesk Ticket ID').'</a>';
				$output .= '<div id="task_zbox" style="display:none;">'; 
				$e_zticket = new Event("Zendesk->eventAddZendTicket");
				//$e_zticket->setLevel(160);
				$e_zticket->addParam("idproject_task", $_GET['idprojecttask']);
				$e_zticket->addParam("iduser", $_SESSION['do_User']->iduser);
				$e_zticket->addParam("goto", "Task/".$_SESSION['do_project_task']->idproject_task);
				$output .= $e_zticket->getFormHeader();
				$output .= $e_zticket->getFormEvent();
				$output .= '<input type="text" name="z_ticket_id" id = "z_ticket_id" value= '.$ticket_id.'>';
				$output .='<input value="'._('Add Zend Ticket').'" type="submit">';
				$output .= $e_zticket->getFormFooter();
				$output .= '<br /><br /><a href="#" onclick="hideZbox(); return false;">'._('Hide').'</a>';
				
				$output .= '</div>';
			  
		  }
		  
		  return $output;
	  }
}

?>
