<?php
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
   * Display Object
   * @see Display
   * @package RadriaCore
   */
  /**
   * Display Class
   *
   * It manage parameters from an internal array
   * and built a full url with the getUrl method.
   *
   * @package RadriaCore   
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007   
   * @version 3.0.0
   * @access public
   */

#namespace radriacore;

class Display extends BaseObject {
    
  /**  Array that stores all the parameters, the index is the parameter name and
   *    the value is the value of the parameter.
   * @var Array $params
   * @access private
   */
  var $params ;

  /**  String with the full URL of the page that will recieve the parameters
   * @var String $page
   * @access private
   */
  var $page ;

  /**
   * Constructor, create a new instance of an Display
   * @param String $page its the full URL of the page where the parameters need to be sent.
   * @access public
   */
  function Display($page="") {
    $this->page = $page ;
    if (defined("RADRIA_LOG_RUN_DISPLAY")) {
        $this->setLogRun(RADRIA_LOG_RUN_DISPLAY);
    }
  }

  /**
   * getUrl return a welformed URL in a string with parameter page
   * and all the parameters ready to be send has get.
   * @access public
   * @return string with partial URL
   * @see getLink()
   */
  function getUrl() {
    if (ereg("\?", $this->page)) {
      list($this->page, $query) = explode("?", $this->page) ;
    }
    $url = "" ;
    if (is_array($this->params)) {
      reset($this->params) ;
      while(list($varname, $varvalue) = each($this->params)) {
        $$varname = $varvalue;
        if (is_array($$varname)) {
            foreach ($$varname as $key => $value) {
                if (is_array($value)) {
                    foreach($value as $key2 => $value2) {
                        if(!is_array($value2)) {
                        $url .= "&".$varname."[".$key."][".$key2."]=".urlencode($value2);
                        } else {
                            $this->setError("The param is an array with more than 2 dimentions. \$arrayname[][][] or more. Make sure its only 2 dimenssions max.");
                        }
                    }
                } else {
                    $url .= "&".$varname."[".$key."]=".urlencode($value) ;
                }
            }
        } else {
            $url .="&".$varname."=".urlencode($varvalue) ;
        }
      }
    }
    if (empty($url) ) {
        $url = $this->page;
    } else { 
        $url = $this->page."?x=1".$url ;
    }
    return $url ;
  }
  /**
   * getLink return an html link for that display.
   *
   * @param string $linklabel label to display for that link
   * @param string class name of the style for that link
   * @access public
   * @return string with an HTML link
   * @see getUrl()
   */
  function getLink($linklabel, $properties="") {
     $link = "<a href=\"".$this->getUrl()."\"".$properties.">".$linklabel."</a>";
     return $link;
  }
   
  /** 
   * setPage set the page name of the display
   * 
   * @param string $pagename name of the page.
   */
  function setPage($pagename) {
    $this->page = $pagename ;
  }
  
  function getPage() {
    return $this->page ;
  }
  
  /**
   * addParam to the display
   * The varname and its value will be added to the params property array.
   * They will be added to the url on the getUrl()
   * @param string $varname name of the variable
   * @param string $varvalue value or content of the variable
   * @see getUrl(), getParam()
   */
  function addParam($varname, $varvalue) {
    $this->params[$varname] = $varvalue ;
  }
  
  /**
   * get on Param from the display
   * Return the value of the requested param.
   * @param string $varname variable name.
   * @return string param value.
   * @see addParam()
   */
  function getParam($varname) {
    return $this->params[$varname] ;
  }
  
  /**
   * Get all params for the display
   * Return an array with all the params previously set in that display
   * @return array with all the params with the variable name as key.
   * @see addParam(), getParam()
   */
  function getParams() {
    return $this->params ;
  }
   /**
    * __set magic method to set the value of a field.
    * 
    *  This magic method is used here to assign param to the event object 
    *  Storing them in the param array
    *  @param field name of fields from the table structure
    *  @param value value of the field.
    *
    */
    
    function __set($var, $value) {
        $this->params[$var] = $value;
    }
    
   /**
    * __get magic method to set the value of a field.
    * 
    *  This magic method is used here to retrieve a param from the event object 
    *  Taking it from the param array
    *  @param field name of fields from the table structure
    *  @param value value of the field.
    *
    */
    
    function __get($var) {
        return $this->params[$var];
    }    
  /**
   * edit param to modify a param of the display
   * Work exactly like addParam, currently redondant.
   * Was set in the original design interface.
   * @param string $varname name for the variable
   * @param string $varvalue value of the variable.
   * @see addParam(), getParam(), getParams()
   */
  function editParam($varname, $varvalue) {
    $this->params[$varname] = $varvalue ;
  }
  
   /**
     * This event set the next display to be the page set in 
     * the goto parameter.
     *
     * @package RadriaEvents
     * @author Philippe Lewicki  <phil@sqlfusion.com>
     * @copyright  SQLFusion LLC 2001-2004
     * @version 3.0
     */
  function eventGotoPage(EventControler $evctl) {
		$goto = $evctl->getParam("goto");
		$curdisp = $evctl->getDisplayNext();
		if (is_object($curdisp)) {
		$curdisp->setPage($goto);
		$evctl->setDisplayNext($curdisp);
		} elseif (strlen($goto) > 0) {
		$nextpage = new Display($goto);
		$evctl->setDisplayNext($nextpage);
		}
	}
  /**
   * message simply display the message
   * from in page message in the session
   */
  function message() {
	  $message = '';
	  if(isset($_SESSION["in_page_message"]) && $_SESSION["in_page_message"] !=''){
		$message = $_SESSION["in_page_message"];
		$_SESSION["in_page_message"] = '';
	  }
	  return $message;
  }
}

?>
