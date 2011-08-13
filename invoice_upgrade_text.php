<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

	include_once("config.php");
	    include_once("class/OfuzApiClientBase.class.php");
        include_once("class/OfuzApiClient.class.php");
		include_once("class/OfuzApiClientPrivate.class.php");
		
		$idinvoice = 101;
		$user_id = 173;
		
        //$api_key = '4a974e2d0d18d5257f064fd33972390e';// replace this with your API key
        $do_ofuz = new OfuzApiClientPrivate(OFUZ_API_KEY, "json");     
		$do_ofuz->setObject(true);
		// Stop recurrence on the current invoice and transfer the payment information
       // if($evctl->idinvoice > 0 || $evctl->current_plan != 'free') {
		//	$do_ofuz->idinvoice = $idinvoice;
		//	$do_ofuz->stop_recurrent();
		//	echo "<Br>".$do_ofuz->requestQuery()."</Br>";
		//	echo "\n Found existing invoice Stoping recurence:\n";
		//	print_r($do_ofuz);
		//	$do_ofuz->clearRequest();
	//	} 
		
	/**	
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
		echo "Add Invoice with API response:";
		print_r($response);
   
		if($response->stat == "ok" && $response->code == "710"){
			$invoice_url = $response->invoice_url;
			$payment_url = $response->payment_url;
	*/	
			// Set the invoice as recurrent
			$do_ofuz->idinvoice =$idinvoice;
			$do_ofuz->recurrence = 1;
			$do_ofuz->recurrencetype = 'Month';
			$do_ofuz->callback_url = '';
		    $do_ofuz->next_url = '';
			$do_ofuz->add_recurrent();
			echo $do_ofuz->requestQuery();
			echo "\n set recurence:\n" ;
			print_r($do_ofuz);
			//$dethis->setLogObject($do_ofuz->getResponse());
	  
			//Set The RegistrationInvoiceLog
			//$do_ofuz->idinvoice =$response->idinvoice;
			//$do_ofuz->reg_iduser = $user_id;
			//$do_ofuz->set_reg_user_invoice();
			//echo "\n associate user with invoice in reg_invoice_log:\n";
			//print_r($do_ofuz);
			
/**
			// Adding the invoice line
			//$do_ofuz->idinvoice = $response->idinvoice;
			$do_ofuz->price= $inv_line_price;
			$do_ofuz->qty = $inv_line_qty;
			$do_ofuz->description = $inv_line_desc;
			$do_ofuz->item = $inv_line_item;
			$do_ofuz->add_invoice_line();
			echo "\n Add the invoice line item:\n";
			print_r($do_ofuz->getResponse());
			//return $invoice_url;
		
			echo "\nNext:".$payment_url;
		} else {
			echo "\n index.php";
		}
		**/
		
?>
