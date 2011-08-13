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

class OfuzApiClientBase {

	var $postParams = array();
   	var $params = Array();
	var $api_url = "http://www.ofuz.net/api.php";
   	var $response_object;
	var $return_object = false;
		
   	 /*Constructor Function*/
   	function OfuzApiClientBase($key='', $format='') {
		if (!empty($key)) 
		$this->key = $key;
		if (!empty($format)) {
			$this->format = $format;
		} else {
			$this->format = "php";
		}
   	 }
	 function setAuth($key) {
        	$this->key = $key;
     }
	 function getKey() { 
		 return $this->key;
	 }
	 function setMethod($method) { 
		 $this->method = $method;
	 }
	 function setFormat($format) {
		 $this->format = $format;
	 }
	 function getFormat() {
		 return $this->format;
	 }
		
	function setObject($bool) {
		$this->return_object = $bool;
	}
	function getObject() {
		return $this->return_object;
	}
	function requestQuery() {
		$request = '';
		foreach ($this->params as $name => $value) {
			$request .= $name."=".urlencode($value)."&";
		}
		$request = ereg_replace("\&$", "", $request);
		if (!empty($request)) {
			return $request;
		} else {
			return false;
        }
		
    }
	
	function getResponse() {
		return $this->response_object;
	}
	function setResponse($response) {
		$this->response_object = $response;
	}
	
	/**
     	* Submit the Request to the API
     	*
   	* @return array with the API response
     	*/
    
   	 function submit() {
        $result = file_get_contents($this->api_url."?".$this->requestQuery());
                //echo $this->api_url."?".$this->requestQuery();exit();
		if ($this->getObject()) {
			if ($this->format == "json") {
				$this->setResponse(json_decode($result));
			} elseif($this->format == "php") {
				$this->setResponse(unserialize($result));
		    } 
            if (strlen($this->getResponse()->msg) > 1 && $this->getResponse()->stat == 'fail') { 
				return false; 
			} else {
				return true;
			} 
		} else {
			return  $result;
		}
	}

	/**
	* return request parameter value
	* @param string parameter name
	*/
	function getParam($var) {
		if (@array_key_exists($var, $this->params)) {
		return $this->params[$var];
		} else {
		return false;
		}
	}
	
	/**
	* set a parameter value
	* @param string parameter name
	* @param string parameter value
	*/
	function setParam($var, $value) {
		$this->params[$var] = $value;
	}
	
	/**
	* __get magic method to return the request param value
	*
	* @param field name of the field
	* @return value of the field
	*/
	function __get($field) {
		return $this->getParam($field);
	}
	
	/**
	* __set magic method to set the value of a field.
	* 
	*  This magic method is used here to assign a value to the fields array.
	*  making the value available to the object for further manipulation.
	*  @param field name of fields from the table structure
	*  @param value value of the field.
	*
	*/
	
	function __set($field, $value) {
		$this->setParam($field, $value);
	}
	
    function clearRequest() {
		$key = $this->getKey();
		$format = $this->getFormat();
		$this->params = Array();
		$this->setAuth($key);
		$this->setFormat($format);
		$this->response = null;
		$this->method = null;
	}
	
}

?>
