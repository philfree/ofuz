<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

   /**
    * The goal of this class is to provide a mecanism
    * general to all our API.
    * It will process the message passed and the values
    * output in 3 differents formats:
    * xml, json, php serialized.
    *
    * Copyright 2002 - 2007 SQLFusion LLC
    * @author Abhik Chakraborty abhik@sqlfusion.com, Philippe phil@sqlfusion.com
    * @version 0.1
    * Base class for the Ofuz REST API
    * 
    *
    */

class OfuzApiBase {

    var $output_type;/* php, json, xml - default is "xml" */
    var $message = '';
    var $message_code = 0;
    var $message_stat; // ok or fail (for errors)
    var $return_value; //PHP Array that countains the values that need to be returned
    var $output_string = ''; //String that containt the output.
    var $request_param = Array();


    function OfuzApiBase($output_type="xml", $values=Array()){
      $this->setOutputFormat($output_type);
      $this->setValues($values);
      $this->setRequestParam($_REQUEST);
   }

    /**
	 * Check if the key is valid and get the user 
	 * associated with it
	 * @param string api_key 
	 * @return iduser or false
	 */
    function checkKey($api_key){
        $do_api_user = new User();
        $iduser = $do_api_user->validateAPIKey($api_key);
        if($iduser){
          $this->iduser = $iduser;
          return $this->iduser;
        }else{
          $this->setMessage("501","The supplied API key is not valid");
		  return false;
        }
    }

    /**
     * Get the message
     * Return a message string if set otherwize
     * return false.
     * @return mix string or boolean
     */
    function getMessage(){
        if(strlen($this->message) > 0){
            return $this->message;
        } else {
            return false;
        }
    }

    /**
     * Set the message.
     * Takes the message string as
     * parameter and the code and assigns to the $message
         * variable to have the error number and message.
     * This is used to display the error message.
     * @param integer message_code
     * @param string message
     * @param string ok or fail (optional)
     */
    function setMessage($code,$msg,$stat="fail"){
        $this->message = $msg;
        $this->message_code = $code;
        $this->message_stat = $stat;
    }

    /**
     * Return the message code and false if none are set.
     * @return integer message_code
     */
    function getMessageCode()  {
        if (isset($this->message_code)) {
          return $this->message_code;
        } else {
          return false;
        }
    }

    /**
     * setOutputFormat
     * Setting the output format.
     * If not set at the constructor
     * this will set the format in wich the PHP
     * array return_value need to be formated
     * before its sent back
     * on the response.
     * @param string output_type (xml, json, php)
     */
    function setOutputFormat($output_type){
        if ($output_type == "xml" || $output_type == "php" || $output_type == "json") {
            $this->output_type = $output_type;
            return true;
        } else { return false; }
    }

    /**
     * getOutputFormat()
     * Return the current output.
     * @return string output like: xml, json, php
     */
    function getOutputFormat() {
       return $this->output_type;
    }

    /**
     * set values
     * Set the values to return
     * as a PHP array with keys as var name.
     *
     * @param array values
     */
    function setValues($value) {
            $this->return_value = $value;
    }

    /**
     * get values
     * return the php array with the
     * value to return
     * @return Array
     */
    function getValues() {
        return $this->return_value;
    }
    
    /**
     * Set request param array.
     * Set the REST request param in 
     * the class array: request_param
     * By default its the $_REQUEST array
     * 
     * @note this will overwrite all value already set from the request.
     * @param array paramerts array with key as var name and content as value.
     */
    function setRequestParam($params) {
        $this->request_param = $params;
    }
    
    /**
     * return request parameter value
     * @param string parameter name
     */
    function getParam($var) {
        if (array_key_exists($var, $this->request_param)) {
          return $this->request_param[$var];
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
        $this->request_param[$var] = $value;
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
    
    
    /**
     * Get output string
     * Return the content of the output
     */
    function getOutputString() {
        return $this->output_string;
    }

    /**
     * Set output string
     * String that contains the output
     * @param string output content.
     */
     function setOutputString($output) {
        $this->output_string = $output;
     }

    /**  
     * OutputValues()
     * could be renamed:
     * responseValues or formatValues()....
     * It take all the values in the array
     * and format them based on the defined
     * output format so they can be sent
     *  as a response,
     * @return string with output values
     */
    function OutputValues(){
        if ($this->getOutputFormat() == "xml"){
            header("Content-type: text/xml");
            $xmlTmpXml  = "<?xml version=\"1.0\" encoding=\"utf-8\" ";
            $xmlTmpXml .="<rsp stat=\"ok\">";
            foreach ($this->getValues() as $var_name => $var_value) {
                $xmlTmpXml .="\n   <".$var_name.">".$var_value."</".$var_name.">";
            }
            $xmlTmpXml .= "</rsp>";
            $this->setOutputString($xmlTmpXml);
        }
        if($this->getOutputFormat() == "json"){
            $this->setOutputString(json_encode($this->getValues()));
        }
        if($this->getOutputFormat() == "php"){
            $this->setOutputString(serialize($this->getValues()));
        }
        return $this->getOutputString();
    }

   /**
    *  Output Message
    *  This method format the message string
    *  and its code with the define output format
    *  so it can be return as the response.
    *  @param stats 2 string values: ok or fail for errors
    */
    function OutputMessage(){
        if ($this->getOutputFormat() == "xml"){
            header("Content-type: text/xml");
            $outPut  = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>";
            $outPut .="<rsp stat=\"".$this->message_stat."\">";
            $outPut .="<err code=\"".$this->getMessageCode()."\" msg=\"".$this->getMessage()."\" />";
            $outPut .="</rsp>";
            $this->setOutputString($outPut);
        }
        if($this->getOutputFormat() == "json"){
            $outputArr = array('msg'=> $this->getMessage(),
                               'code' => $this->getMessageCode(),
                               'stat' => $this->message_stat );
            $this->setOutputString(json_encode($outputArr));
        }
        if($this->getOutputFormat() == "php"){
            $outputArr = array('msg'=> $this->getMessage(),
                               'code' => $this->getMessageCode(),
                               'stat' => $this->message_stat );
            $this->setOutputString(serialize($outputArr));
        }
        return $this->getOutputString();
    }

}


?>
