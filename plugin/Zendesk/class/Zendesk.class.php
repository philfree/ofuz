<?php

class Zendesk extends DataObject {
	
	public $table = 'user_zendesk';
    protected $primary_key = 'iduser_zendesk';



	function curlWrap($url, $json, $action, $ZDURL, $ZDUSER, $ZDAPIKEY)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt($ch, CURLOPT_URL, $ZDURL.$url);
		curl_setopt($ch, CURLOPT_USERPWD, $ZDUSER."/token:".$ZDAPIKEY);
		switch($action){
			case "POST":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				break;
			case "GET":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
				break;
			case "PUT":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
				break;
			case "DELETE":
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
				break;
			default:
				break;
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		$output = curl_exec($ch);
		//echo 'Curl output: ' . curl_error($ch);
		curl_close($ch);
		$decoded = json_decode($output);
		return $decoded;
	}
	/**
	 * Function Zendesk Details for user
	 * @param iduser
	 * @return query object
	 **/

	 function GetUserZendeskDetails($iduser){
		 
		$q = new sqlQuery($this->getDbCon());
		$q->query("Select iduser_zendesk,zend_email,zend_api,zend_url,idproject from ".$this->table. "
					  where iduser = ".$iduser."");
		if($q->getNumRows() >= 1){
			$data = array();
			$i = 0;
			 while($q->fetch()){
				  $data["zend_email"][$i] = $q->getData("zend_email");
				  $data["zend_api"][$i] = $q->getData("zend_api");   
				  $data["zend_url"][$i] = $q->getData("zend_url");   
				  $data["iduser_zendesk"][$i] = $q->getData("iduser_zendesk");   
				  $data["idproject"][$i] = $q->getData("idproject");   
				  $i++;
			   }
			   return $data;
		  }else{
			  return false;
		  }
	 }
	 
	 /**
	 * FUnction to add zend  the user
	 * @event object
	 * @return msg
	**/
	function eventAddZend(EventControler $evtcl){
		//$msg = '';
		
		$goto = $evtcl->goto;
		//check if the below is already exist
		  if(!$this->checkIfZendeskExist($evtcl->iduser,$evtcl->zend_email,$evtcl->zend_api,$evtcl->zend_url)) {	
			
			$data = $this->curlWrap("/users/me.json", null, "GET", $evtcl->zend_url, $evtcl->zend_email, $evtcl->zend_api);
			//echo $data->{'user'}->{'verified'};

			if($data->{'user'}->{'verified'}){
				$q = new sqlQuery($this->getDbCon());
				$sql = "INSERT INTO user_zendesk(`iduser`,`zend_email`,`zend_api`,`zend_url`,`idproject`)
				  VALUES(".$evtcl->iduser.",'".$evtcl->zend_email."','".$evtcl->zend_api."','".$evtcl->zend_url."','".$evtcl->idproject."')";
				$q->query($sql);
				
				$_SESSION['msg'] = "New zendesk details has been added successfully.";
			} else {
				$_SESSION['msg'] = 'Provided is information is not correct. Zendesk cannot able to authenticate you. Please try with proper data';
			}
		}else{
			$_SESSION['msg'] = "Duplicate Entry, Zendesk details Already Exist Please check your details provided to avoid the duplicates.";
		}	
		//echo $msg;	
		$evtcl->setDisplayNext(new Display($goto));
	}
	
	
	/**
	 * function to check zenddesk details already exist 
	 * @param iduser 
	 * @param zend_email
	 * @param zend_api
	 * @param zend_url
	**/ 
	function checkIfZendeskExist($iduser,$zend_email,$zend_api,$zend_url){
		
		$q = new sqlQuery($this->getDbCon());
		$q->query("Select * from ".$this->table. "
					  where iduser = '".$iduser."' and zend_email = '".$zend_email."' and zend_url = '".$zend_url."' and zend_api = '".$zend_api."'");
		if($q->getNumRows() >= 1){
			return true;
		} else {
			return false;
		}		
	}
	
	
	/**
	 * function eventunlinkZend
	 * functiont to unlink zendesk details 
	 * @param eventcontroller 
	 **/
	 function eventunlinkZend(EventControler $evtcl){
		 $goto = $evtcl->goto;
		 $iduser_zendesk = $evtcl->iduser_zendesk;
		 
		 if($iduser_zendesk){
			 
			 $q = new sqlQuery($this->getDbCon());
			 $sql = "delete from ".$this->table." where iduser_zendesk = ".$iduser_zendesk." limit 1";
			 $q->query($sql);
			 
			 $_SESSION['msg'] = "unlinked zendesk from Ofuz.";
		 }
		 $evtcl->setDisplayNext(new Display($goto));
	 }
	 
	 /**
	  * function to list the projects for zendesk
	  * @param iduser
	  **/
	  function getProjectList($iduser){
		  
		   $q = new sqlQuery($this->getDbCon());
		   $sql = "select idproject,name from project where iduser = ".$iduser." and status='open'";
		   $q->query($sql);
		   
		   while($q->fetch()){
			   
			   $idproject = $q->getData("idproject");
			   $project_name = $q->getData("name");
			   
			   $html .= '<option value='.$idproject.'>'.$project_name.'</option>';
		   }
		   return $html;
	  }
	  
	  /**
	   * function to check if user with project id has zendapi
	   * zendeskProjectUserRelation()
	   * @param iduser 
	   * @Param idproject
	   * @see BlockZendeskTicket
	   * @return boolean
	   **/
	   function zendeskProjectUserRelation($iduser,$idproject){
		   
		   $q = new sqlQuery($this->getDbCon());
		   $sql = "Select * from ".$this->table. "
					  where iduser = '".$iduser."' and idproject = '".$idproject."'";//echo $sql;
		   $q->query($sql);
		   if($q->getNumRows() >= 1){
			   return true;
		   } else {
				return false;
			}
		   
	   }
	   
	   /**
	    * function to add zendesk ticket to task id
	    * eventAddZendTicket
	    * @param eventcontroller 
	    * @see zendeask_api
	    **/
	    function eventAddZendTicket(EventControler $evtcl){ 
		 $goto = $evtcl->goto;
		 $idproject_task = $evtcl->idproject_task;
		 $z_ticket_id = $evtcl->z_ticket_id;
		 $iduser = $evtcl->iduser;
		 if($z_ticket_id){
			 
			 $pre = new sqlQuery($this->getDbCon());
			 $pre_sql = "select idzendesk_task_ticket_releation from zendesk_task_ticket_releation where iduser = '".$iduser."' and idproject_task = '".$idproject_task."' order by idzendesk_task_ticket_releation desc limit 1";
			 $pre->query($pre_sql);
			 
			 if($pre->getNumRows() >= 1){
				 
				 while($pre->fetch()){
					 
					 $idzendesk_task_ticket_releation = $pre->getData("idzendesk_task_ticket_releation");
				 }
				 $u = new sqlQuery($this->getDbCon());
				 $u_sql = "update zendesk_task_ticket_releation set ticket = '".$z_ticket_id."',idproject_task = '".$idproject_task."' where idzendesk_task_ticket_releation = '".$idzendesk_task_ticket_releation."' limit 1";
				 $u->query($u_sql);
				 $_SESSION['msg'] = "Updated Zendesk Ticket.";
			 } else {
			 
				$q = new sqlQuery($this->getDbCon());
				$sql = "Insert into  zendesk_task_ticket_releation (ticket,iduser,idproject_task) values ('".$z_ticket_id."','".$iduser."','".$idproject_task."')";
				$q->query($sql);
				$_SESSION['msg'] = "Added zendesk Ticket.";
				
			}		 
			 
		 } else {
			 $_SESSION['msg'] = "Please enter valid zendesk ticket ID";
		 }
		 $evtcl->setDisplayNext(new Display($goto));
			
		}
		
		/**
		 * function get Zend Ticket ID
		 * getZendTicketId
		 * @param iduser int
		 * @param idproject_task
		 * @return ticket
		 **/
		 function getZendTicketId($iduser,$idproject_task){
			 
			 $q = new sqlQuery($this->getDbCon());
			 $sql = "select idzendesk_task_ticket_releation,ticket from zendesk_task_ticket_releation where iduser='".$iduser."' and idproject_task = '".$idproject_task."' order by idzendesk_task_ticket_releation desc limit 1";
			 $q->query($sql);
			 
			 if($q->getNumRows() >= 1){
				 $ticket = array();
				 while($q->fetch()){					 
					 $ticket['ticket'] = $q->getData('ticket');					 
					 $ticket['idzendesk_task_ticket_releation'] = $q->getData('idzendesk_task_ticket_releation');
				 }
			 }
			 
			 return $ticket;
		 }
	
}

?>
