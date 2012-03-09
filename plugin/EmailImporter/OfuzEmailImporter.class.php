<?php
/**
  * OfuzOpenInviter class 
  */
      
  class OfuzEmailImporter extends DataObject {
	
	function eventGetContacts(EventControler $evtcl) {
		$plugType = 'email';		
		$provider_box = $evtcl->provider_box;
		$email_box = $evtcl->email_box;
		$password_box = $evtcl->password_box;
		$msg = "";
		$contacts = "";
		$ok = false;

		if (empty($email_box))
			$msg="Email missing !";			
		if (empty($password_box))
			$msg="Password missing !";
		if (empty($provider_box))
			$msg="Provider missing !";

		if ($msg=="") {
			$inviter=new OpenInviter();
			$inviter->startPlugin($provider_box);
			$internal=$inviter->getInternalError();

			if ($internal)
				$msg=$internal;
			elseif (!$inviter->login($email_box,$password_box))
			{
				$internal=$inviter->getInternalError();			
				$msg=($internal?$internal:"Login failed. Please check the email and password you have provided and try again later !");
			}
			elseif (false===$contacts=$inviter->getMyContacts())
				$msg="Unable to get contacts !";
			else
			{
				$msg = "Contacts imported successfully.";
				$ok = true;
				//$_POST['oi_session_id']=$inviter->plugin->getSessionID();
			}
		}
		if($ok) {			
			/*print_r($contacts);
			exit();*/
			foreach ($contacts as $email=>$name) {
				$do_contact = new Contact();
				$do_contact->firstname = $name;
				$do_contact->iduser = $_SESSION['do_User']->iduser;
				$do_contact->add();
				$lastInsertedContId = $do_contact->getPrimaryKeyValue();
				$do_contact->addEmail($email,'Home');
				$do_contact->free();
				
				//contact view
				$do_cv = new ContactView();
				$do_cv->idcontact = $lastInsertedContId;
				$do_cv->firstname = $name;
				$do_cv->email_address = $email;
				$do_cv->add();
				$do_cv->free();
			}
		}
		
		$_SESSION['in_page_message'] = $msg;
		
	}

  }
?>
