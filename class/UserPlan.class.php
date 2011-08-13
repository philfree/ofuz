<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * UserPlan class
     * Using the DataObject
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzOnline
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.5
     */

class UserPlan extends dataObject {
    public $table = "user_plan";
    protected $primary_key = "iduser_plan";

	function getUserPlanDetails($plan) {

		$sql = "SELECT * 
				FROM `{$this->table}`
				WHERE plan = '{$plan}'
			   ";
		$this->query($sql);
		
	}

	/**
	  * Check if user is allowed to add invoices 
      * The limit is currently a number of invoices per month
	  * @return boolean
	  */
	function canUserAddInvoice() {

		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT COUNT(idinvoice) AS total_invoices
				FROM `{$this->table}` 
				WHERE `iduser` = {$_SESSION['do_User']->iduser}
				AND MONTH(datecreated) = '{$_SESSION['do_invoice_list']->filter_month}'
			   ";

		$q->query($sql);

		if($q->getNumRows()) {
			$q->fetch();
			$total_inv = $q->getData("total_invoices");
			$this->getUserPlanDetails($_SESSION['do_User']->plan);
			if($this->getNumRows()) {
				while($this->next()) {
					if($this->getData("invoices") == "unlimited") {
						return true;
					} else {
							if($total_inv < $this->getData("invoices")) {
								return true;
							} else {
								return false;
							}
					}
				}
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
    /** 
     * check if the user can add more contact
     * based on his plan and plan configuration.
     * @return boolean if the user can add more contacts.
     */

	function canUserAddContact() {
		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT COUNT(idcontact) AS total_contacts 
				FROM `{$this->table}` 
				WHERE `iduser` = {$_SESSION['do_User']->iduser}
			   ";
		$q->query($sql);
		if($q->getNumRows()) {

			$q->fetch();
			$total_contacts = $q->getData("total_contacts");
			$do_up = new UserPlan();
			$this->getUserPlanDetails($_SESSION['do_User']->plan);
			if($this->getNumRows()) {
				while($this->next()) {
					if($this->getData("contacts") == "unlimited") {
						return true;
					} else {
							if($total_contacts < $this->getData("contacts")) {
								return true;
							} else {
								return false;
							}
					}
				}
			} else {
				return false;
			}

		} else {
			return true;
		}
	}
	
    /**
     * Check if the user can add more project
     * based on his plan and number of existing projects.
     * @return boolean if it can or not.
     */

	function canUserAddProject() {

		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT COUNT(idproject) AS total_projects 
				FROM `{$this->table}` 
				WHERE `iduser` = {$_SESSION['do_User']->iduser}
			   ";
		$q->query($sql);

		if($q->getNumRows()) {

			$q->fetch();
			$total_prjs = $q->getData("total_projects");
			$do_up = new UserPlan();
			$this->getUserPlanDetails($_SESSION['do_User']->plan);
			if($this->getNumRows()) {
				while($this->next()) {
					if($this->getData("projects") == "unlimited") {
						return true;
					} else {
							if($total_prjs < $this->getData("projects")) {
								return true;
							} else {
								return false;
							}
					}
				}
			} else {
				return false;
			}

		} else {
			return true;
		}

	}


    /**
      * Specific method to add user to PHIL
      * NOT a part of OPEN SOURCE RELEASE 
    */
    function eventAddUserToPhilContact(EventControler $evtcl){
	    if($evtcl->validation_fail != 'Yes'){
		include_once("class/OfuzApiClient.class.php"); 		
		$do_ofuz = new OfuzApiClient();		
		$iduser = $do_ofuz->setAuth(OFUZ_API_KEY);
		$do_ofuz->firstname = $evtcl->fields["firstname"];
		$do_ofuz->lastname = $evtcl->fields["lastname"];
		if($evtcl->fields["company"]) {
			$do_ofuz->company = $evtcl->fields["company"];
		}
		$do_ofuz->email_home = $evtcl->fields["email"];
		$tags = 'beta3,new_user'; // Comma seperated tags
		$do_ofuz->tags = $tags;
		$response = $do_ofuz->add_contact();
	    }

    }

    /**
      * Specific method to add user to PHIL
      * NOT a part of OPEN SOURCE RELEASE 
    */
    function addUserToPhilContact($firstname,$lastname,$company,$email){
              include_once("class/OfuzApiClient.class.php"); 

              $do_ofuz = new OfuzApiClient();		
              $iduser = $do_ofuz->setAuth(OFUZ_API_KEY);
              $do_ofuz->firstname = $firstname;
              $do_ofuz->lastname = $lastname;
              if($company !="") {
                      $do_ofuz->company = $company;
              }
              $do_ofuz->email_home = $email;
              $tags = 'beta3,new_user'; // Comma seperated tags
              $do_ofuz->tags = $tags;
              $response = $do_ofuz->add_contact();
        }

	/**
	 * eventUpgrade
	 * called from the upgrade_plan.php
	 * Manage the upgrade of the users from free to 24 and 24 to 99.
	 * if user already paid is payment information are transfered to the 
	 * new plan and a new invoice is created.
	 * If upgrade from a free he will be entering payment information and charged.
	 */

   function eventUpgrade(EventControler $evctl) {
	 
	    include_once("class/OfuzApiClientBase.class.php");
        include_once("class/OfuzApiClient.class.php");
		include_once("class/OfuzApiClientPrivate.class.php");
        $do_ofuz = new OfuzApiClientPrivate(OFUZ_API_KEY, "json");     
		$do_ofuz->setObject(true);
		// Stop recurrence on the current invoice and transfer the payment information
        if($evctl->idinvoice > 0 || $evctl->current_plan != 'free') {
			$do_ofuz->idinvoice = $evctl->idinvoice;
			$do_ofuz->stop_recurrent();
			$this->setLog("\n Found existing invoice Stoping recurence:\n");
			$this->setLogObject($do_ofuz);
			$do_ofuz->clearRequest();
		} 
			
		switch($evctl->plan){
			  case  "24" :
						  $inv_term = 'Upon Receipt';
						  $inv_note = 'Thanks';
						  $inv_desc = 'Ofuz subscription';
						  $inv_line_qty = '1';
						  $inv_line_price = '24.00';
						  $inv_line_desc = 'Monthly Subscription';
						  $inv_line_item = 'Ofuz24';  
						  break;
			  case "99" :
						  $inv_term = 'Upon Receipt';
						  $inv_note = 'Thanks';
						  $inv_desc = 'Ofuz subscription';
						  $inv_line_qty = '1';
						  $inv_line_price = '99.00';
						  $inv_line_desc = 'Monthly Subscription';
						  $inv_line_item = 'Ofuz99';  
						  break;
		}
				
		// Add the invoice data first
		$do_ofuz->idcontact = $evctl->idcontact;
		$do_ofuz->invoice_term = $inv_term;
		$do_ofuz->invoice_note= $inv_note;
		$do_ofuz->description = $inv_desc;
		//$do_ofuz->iduser = $iduser;
		$do_ofuz->type = "New";
		$do_ofuz->due_date = date("Y-m-d");
		$do_ofuz->callback_url = OFUZ_COM.'/reg_payment_callback.php';
		$do_ofuz->next_url = OFUZ_NET.'/upgrade_thank_you.php';
		$this->setLog("\n submitting invoice to API:\n");
		$this->setLogObject($do_ofuz);
		$do_ofuz->add_invoice();
		$response = $do_ofuz->getResponse();
		$this->setLog("\n Add Invoice with API response:\n");
		$this->setLogObject($response);
   
		if($response->stat == "ok" && $response->code == "710"){
			$invoice_url = $response->invoice_url;
			$payment_url = $response->payment_url;
		
			// Set the invoice as recurrent
			$do_ofuz->idinvoice =$response->idinvoice;
			$do_ofuz->recurrence = 1;
			$do_ofuz->recurrencetype = 'Month';
			$do_ofuz->callback_url = '';
   		        $do_ofuz->next_url = '';
			$do_ofuz->add_recurrent();
			$this->setLog("\n set recurence:\n") ;
			$this->setLogObject($do_ofuz);
			//$this->setLogObject($do_ofuz->getResponse());
	  
			//Set The RegistrationInvoiceLog
			//$do_ofuz->idinvoice =$response->idinvoice;
			$do_ofuz->reg_iduser = $evctl->user_id;
			$do_ofuz->set_reg_user_invoice();
			$this->setLog("\n associate user with invoice in reg_invoice_log:\n");
			$this->setLogObject($do_ofuz);

			// Adding the invoice line
			$do_ofuz->idinvoice = $response->idinvoice;
			$do_ofuz->price= $inv_line_price;
			$do_ofuz->qty = $inv_line_qty;
			$do_ofuz->description = $inv_line_desc;
			$do_ofuz->item = $inv_line_item;
			$do_ofuz->add_invoice_line();
			$this->setLog("\n Add the invoice line item:\n");
			$this->setLogObject($do_ofuz->getResponse());
			//return $invoice_url;
		
			$evctl->setUrlNext($payment_url);
		} else {
			$evctl->setUrlNext("index.php");
		}
        
    }

}
?>
