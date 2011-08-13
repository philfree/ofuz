<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

   include_once('config.php');
    error_reporting(E_WARNING | E_ERROR);
    
    if (empty($_REQUEST['method'])) {
        echo "no method selected";
    }

    /*
        API method for adding a contact
    */
    if ($_REQUEST['method'] == "add_contact") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
        $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->add_contact()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("540", "Error adding Contact Information");
			}
		}
	}
        if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }
 
    /*
        API method for searching contact
    */
    if ($_REQUEST['method'] == "search_contact") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
        $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->search_contact()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("641", "Error Searching Contact Information");
			}
		}
	}
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }

    /*
        API method for searching contact and returning the idcontact
    */
    if ($_REQUEST['method'] == "get_contact_id") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
        $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->get_contact_id()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("641", "Error Searching Contact Information");
			}
		}
	}
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }

    /*
        API method for getting all the contacts
    */
    if ($_REQUEST['method'] == "get_contacts") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
        $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->get_contacts()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("641", "Error getting Contact Information");
			}
		}
	}
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }
    
    /*
        API method for adding tags
    */
    if ($_REQUEST['method'] == "add_tag") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
         $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->add_tag()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("650", "Error Adding Tags");
			}
		}
	} 
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }

    /*
        API method for deleting tags 
    */
    if ($_REQUEST['method'] == "delete_tag") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
         $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->delete_tag()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("651", "Error Deleting Tags");
			}
		}
	} 
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }

    /*
        API method for adding note
    */
    if ($_REQUEST['method'] == "add_note") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
         $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->add_note()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("633", "Error Adding Note");
			}
		}
	} 
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }

    /*
        API method for adding task
    */
    if ($_REQUEST['method'] == "add_task") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
         $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->add_task()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("643", "Error Adding Task");
			}
		}
	} 
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }

    /*
        API method for adding invoice
    */
    if ($_REQUEST['method'] == "add_invoice") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
        $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->add_invoice()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("701", "Error Adding Invoice");
			}
		}
	} 
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }

    /*
        API method for adding the Invoice Line
    */
    
     if ($_REQUEST['method'] == "add_invoice_line") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
        $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->add_invoice_line()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("711", "Error Adding Invoice Line");
			}
		}
	} 
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }


     /*
        API method for adding the Invoice As Recurrent
    */
    
     if ($_REQUEST['method'] == "add_recurrent") {
        $api_key = $_REQUEST['key'];
        $format = $_REQUEST['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
        $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->add_recurrent()){
                        $message_set = false;
			echo $ofuz_call_methods->OutputValues();
		}else{
			if($ofuz_call_methods->OutputMessage() == ''){
				$ofuz_call_methods->setMessage("711", "Error Adding Invoice As Recurrebt");
			}
		}
	} 
         if($message_set){ echo $ofuz_call_methods->OutputMessage(); }
    }

	echo api_method_call("get_contact_subscription", $_REQUEST, "711", "Error getting the contact subscription");
    echo api_method_call("stop_recurrent", $_REQUEST, "711", "Error in stopping invoice recurrence");
	
	function api_method_call($method, $request, $default_error_code, $default_error_message) {
     if ($request['method'] == $method) {
        $api_key = $request['key'];
        $format = $request['format'];
        if (empty($format)) {$format = "xml"; }
        $ofuz_call_methods = new OfuzApiMethods($format);
        $message_set = true;
        if($ofuz_call_methods->checkKey($api_key)){
       		if($ofuz_call_methods->{$method}()){
				$message_set = false; 
				return $ofuz_call_methods->OutputValues();
			}else{
				if($ofuz_call_methods->OutputMessage() == ''){
					$ofuz_call_methods->setMessage($default_error_code, $default_error_message);
				}
			}
		} 
			if($message_set){ return $ofuz_call_methods->OutputMessage(); }
		}			
	}
	

// test
//http://abhik:3atBr3ad@dev.ofuz.net/api.php?method=Ofuz.add.Contact&format=php&key=cad453ca9ec37e07eb766f798423037d 
 ?>
