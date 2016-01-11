<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 * This will basically process the user request.
 * Checks for user authentication and adds data
 * to the database if there is no data error
 * occur.
* Copyright 2002 - 2007 SQLFusion LLC
* @author Abhik Chakraborty      info@sqlfusion.com
* @version 0.1
* Base class for the Ofuz REST API
* 
*
*/

class OfuzApiClientPrivate extends OfuzApiClient{

	var $postParams = array();
   	var $params = Array();
	var $api_url = "";
	//var $api_url = "http://abhik:3atBr3ad@dev.ofuz.net/api_private.php";
   	var $response;
		
   	 /*Constructor Function*/
   	function OfuzApiClientPrivate($key="", $format='') {
          parent::OfuzApiClient($key, $format);
		  $this->api_url = OFUZ_NET."/api_private.php";
   	 }

		
	// Ofuz user releated (not public api)
	function add_user() {
			$this->method = "add_user";
		return $this->submit();
	}
	function delete_user() {
			$this->method = "delete_user";
		return $this->submit();
	}
	
	/**
	 * set_user_active
	 * requires the iduser and set that user as active
	 */
	function set_user_active() {
			$this->method = "set_user_active";
		return $this->submit();
	}
	function set_reg_user_invoice() {
			$this->method = "set_reg_user_invoice";
		return $this->submit();
	}
	/**
	 * get_reg_user_invoice
	 * requires idinvoice and returns the Ofuz iduser
	 * associated with that invoice
	 */ 
	
	function get_reg_user_invoice() {
			$this->method = "get_reg_user_invoice";
		return $this->submit();
	}
	
}

?>
