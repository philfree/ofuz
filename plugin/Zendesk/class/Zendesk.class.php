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
		$q->query("Select iduser_zendesk,zend_email,zend_api,zend_url from ".$this->table. "
					  where iduser = ".$iduser."");
		if($q->getNumRows() >= 1){
			$data = array();
			$i = 0;
			 while($q->fetch()){
				  $data["zend_email"][$i] = $q->getData("zend_email");
				  $data["zend_api"][$i] = $q->getData("zend_api");   
				  $data["zend_url"][$i] = $q->getData("zend_url");   
				  $data["iduser_zendesk"][$i] = $q->getData("iduser_zendesk");   
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
				$sql = "INSERT INTO user_zendesk(`iduser`,`zend_email`,`zend_api`,`zend_url`)
				  VALUES(".$evtcl->iduser.",'".$evtcl->zend_email."','".$evtcl->zend_api."','".$evtcl->zend_url."')";
				$q->query($sql);
				
				$_SESSION['msg'] = "New zendesk details has been added successfully.";
			} else {
				$_SESSION['msg'] = 'Provided is information is not correct. Zendesk cannot able to autenticate you. Please try with proper data';
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
	
}

?>
