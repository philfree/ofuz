<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt

  /**
   * Event Controler Object
   * @see EventControler
   * @package RadriaCore
   */

 /**
  * Check incoming events and distribute them
  *
  * This object listen to incoming events and based on the mydb_events. all the events names are stored in an array,
  * the array is passed to the lisenevent method that will include the file based on the event name.
  * 
  * @package RadriaCore
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2007  
  * @version 4.0.0
  * @access public
  */
class EventControler extends BaseObject {

  /**  Name of the table to store the events
   * @var String $tbl_event
   */
  var $tbl_event = "mydb_event" ;

  /**  object for instances, database connexion
   * @var object sqlConnect $dbc
   */
  var $dbc ;

  /**  directory where all the eveent include files are
   */
  var $eventdir = "events/" ;

  /**  Event path, Array that holds the different path where event are stored
   */
  var $event_path = Array("./");

  /**  path to mydb application for default envents.
   */
  var $mydbpath = "../mydb/" ;

  /**  array with all the parameters, params[varname, varvalue]
   * @var array $params
   */
  var $params ;

  /**  array with all the session variables, params[varname, varvalue]
   * @var array $params
   */
  var $sessionparams ;

  /** default url to go after executing the events
   */
  var $urlNext ;

  /** default url to go after executing the events
   */
  var $dispNext ;

  /** name of the page that display messages
  */
  var $messagepage="message.php" ;

  /** display the global vars in the url
  */
  var $urlglobalvars = true ;

  /** Execute events request coming from the same domain has eventcontroler
  */
  var $checkreferer = true ;

  /** List all the
  */
  var $mydb_events;

  /** Secure param flag
  */
  var $secure_param = true;

  /** OutputValue
   *  String culmulated with values from all eventAction to be output
   *  once all eventaction are executed.
   */
  private $output_value = '';
  
  /** Unique url
   *  All redirected url are always unique.
   */
   private $unique_url = false;

  /** headers
   * Headers to be sent to the output
   * in case of a none redirect call
   */
   private $headers = Array();

  /**
   * Constructor, create a new instance of an event controler
   * @param object sqlConnect $dbc
   * @access public
   */
  function EventControler($dbc=0) {
        parent::__construct();
        $this->dbc = $dbc ;
        if (defined("RADRIA_LOG_RUN_EVENTCONTROLER")) {
            $this->setLogRun(RADRIA_LOG_RUN_EVENTCONTROLER);
        }
        $this->setLog("\n\n Instanciate Event Controler Object ".date("Y/m/d H:i:s"));
        if (!defined("RADRIA_EVENT_SECURE")) {
            define("RADRIA_EVENT_SECURE", true);
        } 
        $this->setSecure(RADRIA_EVENT_SECURE);
  }

  /**
   * Execute an event with the associate parameters
   *
   * To execute an event it will look for a file based on the name of the event by adding ",inc.php" at the end of the name.
   * The method will look inside the default directory events from mydb directory and local application directory.
   * If it founds no methodes it will set the nexturl to the message page with an error message.
   *
   * @param string $eventname
   * @param array $params
   * @access private
   */
  function Execute($eventname, $params=0) {
    if (is_array($params)) {
      $this->params = $params;
    }
    reset($this->params) ;
    while (list($key, $val) = each($this->params)) {
      $$key = $val ;
    }
    $this->setLog("\n looking for Event:".$eventname);
    if (!empty($eventname)) {
        if (file_exists($this->mydbpath.$this->eventdir.$eventname.".inc.php")) {
          include($this->mydbpath.$this->eventdir.$eventname.".inc.php") ;
        } else {
            $tmp_errormessage = "";
            $event_found = false;
            foreach ($this->event_path as $event_path) {
                $tmp_errormessage = ", ".$event_path.$this->eventdir;
                if (file_exists($event_path.$this->eventdir.$eventname.".inc.php")) {
                    $event_found = true;
                    include($event_path.$this->eventdir.$eventname.".inc.php") ;
                }
            }
            if (strpos($eventname, "->")>0) {
                list ($object_name, $method) = explode("->", $eventname);
                if (strpos($object_name, ":") === false) {
					$this->setLog("\n found object method: ".$object_name." -> ".$method);
					if (is_object($_SESSION[$object_name])) {
						$this->setLog("\nObject is in the session");
						if (method_exists($_SESSION[$object_name], $method)) {
							$event_found = true;
							$_SESSION[$object_name]->{$method}($this);
						}
					} elseif (class_exists($object_name)) {
						$this->setLog("\n Object class exists ".$object_name);
						$event_object = new $object_name($this->getDbCon());
						if (method_exists($event_object, $method)) {
							$event_object->{$method}($this);
							$event_found = true;
						}
					}
				} else {
					    list($object_name, $instance_param) = split(":", $object_name);
					    $this->setLog("\n Object with instantiation param: $object_name ($instance_param) ");
					    if (isset($event_action_object) && is_object($_SESSION[$event_action_object]->fields->{$instance_param})) {
							$this->setLog("\n $event_action_object Databoject with FieldType:".$object_name. "field ".$instance_param." event:".$method);
							if(method_exists($_SESSION[$event_action_object]->fields->{$instance_param}, $method)) {
								$this->setLog("Found!!");
								$_SESSION[$event_action_object]->fields->{$instance_param}->{$method}($this);
								$event_found = true;
							}
						}elseif (get_parent_class($object_name) == "FieldType") {
							$this->setLog("\n FieldType Object class ".$object_name);
							$event_object = new $object_name($instance_param);
							if (method_exists($event_object, $method)) {
								$event_object->{$method}($this);
								$event_found = true;
							}						
						}
					
				}
            }
            if (strpos($eventname, "::")>0) {
                list ($object_name, $method) = explode("::", $eventname);
                
                if (class_exists($object_name)) {
                    $this->setLog("\n Object class exists ".$object_name);
                    //Until static reference are possible (PHP 5.3)
                    $event_object = new $object_name();
                    if (method_exists($event_object, $method)) {
                        $this->setLog("\n With Static method ".$method);
                        $event_object->{$method}($this);
                        $event_found = true;
                    }
                }
            }
            if (!$event_found) {
                $event_errormessage = "Event ".$eventname." not found in :".$this->mydbpath.$this->eventdir.", ".$tmp_errormessage ;
            }
        }
    } else {
      $event_errormessage = "An event must be specified" ;
    }
    if (!empty($event_errormessage)) {
	  $this->setLog("\n".$event_errormessage);
      $this->setError($event_errormessage);
      //$this->setUrlNext($this->getMessagePage()."?message=".urlencode($event_errormessage)) ;
    }
  }

  /**
   * check the incomming events and send them to the Execute method
   *
   * Get the mydb_event array, order them on there execution order, delete duplicate and send them to the Execute method
   *
   * @param array $mydb_events
   * @access public
   */
   function lisenEvents($mydb_events="")  {
     $this->listenEvents($mydb_events) ;
   }
   function listenEvents($mydb_events="") {
        global $mydb_key, $cfg_notrefererequestkey ;
        $mydb_paramkeys= $_SESSION['mydb_paramkeys']; 
        $mydb_eventkey= $_REQUEST['mydb_eventkey']; 
        if (is_array($mydb_paramkeys) && !empty($mydb_eventkey)) {
            if (is_array($mydb_paramkeys[$mydb_eventkey])) {
                 $mydb_key = $mydb_paramkeys[$mydb_eventkey]['mydb_key'];
            } elseif ($this->getSecure()) {
                $this->setLog("EventControler: Secure Events turn on, Invalid event");
                $this->setUrlNext($this->getReferer());
                //$this->doForward() ;
            }
        } elseif ($this->getSecure()) {
            $this->setLog("EventControler: Secure Events turn on, missing event key and session params");
            $this->setUrlNext($this->getReferer());
            //$this->doForward() ;
        }
        if ($mydb_events=="") {  $mydb_events == $GLOBALS['mydb_events']; }
        $this->mydb_events = $mydb_events ;
        if ($this->getCheckReferer()) {
            if (($this->getReferer() == $this->getURI()) || ($mydb_key == $cfg_notrefererequestkey))  {
                if (is_array($this->mydb_events)) {
                    $mydb_events = array_unique($this->mydb_events) ;
                    ksort($this->mydb_events) ;
                    if (is_array($this->mydb_events)) {
                        foreach($this->mydb_events as $eventname) {
                            $this->setLog("\n\n Executing Event : ".$eventname) ;
                            $this->Execute($eventname);
                        }
                    }
                }
            } else {
                $this->setError("EventControler Error : URI didn't match REFERER or Wrong mydb_key has been sent") ;
            }
        } else {
            if (is_array($this->mydb_events)) {
                $mydb_events = array_unique($this->mydb_events) ;
                ksort($this->mydb_events) ;
                if (is_array($this->mydb_events)) {
                    foreach($this->mydb_events as $eventname) {
                        $this->setLog("\n\n Executing Event : ".$eventname) ;
                        $this->Execute($eventname);
                    }
                }
            }
        }
    }

	function eventLoadParams(EventControler $event_controler) {
		  $disperr = new Display($this->getMessagePage()) ;

		  $mydb_paramkeys = $_SESSION["mydb_paramkeys"];
		  $mydb_eventkey = $event_controler->mydb_eventkey;

		  $this->setLog("\n Event Key: ".$mydb_eventkey );

		  if (isset($GLOBALS['cfg_event_param_garbage_time_out'])) {
			$cfg_event_param_garbage_time_out = $GLOBALS['cfg_event_param_garbage_time_out'];
		  } else {
			$cfg_event_param_garbage_time_out = 600;
		  }
		  if (isset($GLOBALS['cfg_event_param_garbage_interval'])) {
			$cfg_event_param_garbage_interval = $GLOBALS['cfg_event_param_garbage_interval'];
		  } else {
			$cfg_event_param_garbage_interval = 120;
		  } 

		  if (!isset($_SESSION['pas_event_garbage_timeout'])) {
			 $_SESSION['pas_event_garbage_timeout'] = time();
		  }
		  $this->setLog("\n current Time is :".date("H:i:s", time()));
		  $this->setLog("\n timeout set to: ".date("H:i:s", $_SESSION['pas_event_garbage_timeout']));
		  if (is_array($mydb_paramkeys) && !empty($mydb_eventkey)) {
			  if (is_array($mydb_paramkeys[$mydb_eventkey])) {
				foreach($mydb_paramkeys[$mydb_eventkey] as $varname=>$varvalue) {
					$GLOBALS[$varname] = $varvalue;
					$event_controler->addParam($varname, $varvalue) ;
					$this->setLog("\n Restoring vars :".$varname."=".$varvalue);
				}
			}
		   // $this->setLog("\n event referer:".$GLOBALS['pas_event_referer']);
			unset($_SESSION['mydb_paramkeys'][$mydb_eventkey]);
			//session_register("mydb_paramkeys") ;

			$this->setLog("\n\n-".$_SESSION['pas_event_garbage_timeout']+$cfg_event_param_garbage_interval." < ".time());
			if ($_SESSION['pas_event_garbage_timeout']+$cfg_event_param_garbage_interval < time()) {
				$old_paramkeys = $_SESSION['mydb_paramkeys'];
				$this->setLog("\n Running events param garbage collector");
				foreach ($old_paramkeys as $key => $a_params) {
					if ($a_params['pas_event_timestamp']+$cfg_event_param_garbage_time_out > time()) {
						$new_paramkeys[$key] = $a_params;
						$this->setLog("\n Keep event:".$key." at:".date("Y/m/d - H:i:s", $a_params['pas_event_timestamp']));
					}
				}
				$_SESSION['mydb_paramkeys'] = $new_paramkeys;
				$_SESSION['pas_event_garbage_timeout'] = time();
			}

		  } else {
				$disperr->addParam("message", "This event requires mydb_paramkeys from the session and mydb_eventkey as parameter") ;
				$event_controler->setDisplayNext($disperr) ;
		  }
		  $this->setLogRun($logrun);
		
	}


    function getReferer($url="") {
        //global $HTTP_SERVER_VARS ;
        $mydb_paramkeys= $_SESSION['mydb_paramkeys']; 
        $mydb_eventkey= $_REQUEST['mydb_eventkey']; 
        if (empty($url)) {
            if (!empty($mydb_paramkeys[$mydb_eventkey]["event_referer"])) {
                $url = $mydb_paramkeys[$mydb_eventkey]["event_referer"];
            } else {
                $url = $_SERVER["HTTP_REFERER"];
            }
        }
        list ($url, $querystring) = explode("?", $url) ;
        $urlreferer = explode("/", $url) ;
        $num = count($urlreferer) ;
        for ($i=0; $i<$num-1; $i++) {
            $referer .= $urlreferer[$i]."/" ;
        }
        return $referer ;
    }

    function getURI($url="") {
     //   global $HTTP_SERVER_VARS ;
        if (empty($url)) {
            $url =  "http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;
        }
        list ($url, $querystring) = explode("?", $url) ;
        $urluri = explode("/", $url) ;
        $num = count($urluri) ;
        for ($i=0; $i<$num-1; $i++) {
            $uri .= $urluri[$i]."/" ;
        }
        return $uri ;
    }

  /**
   * Process to the next page based on the $urlNext
   * @access public
   */
  function doForward() {
    global $globalevents;
    if (strlen($this->getUrlNext()) == 0) {
        if (strlen($this->getParam("event_referer") > 0)) {
            $this->setUrlNext($this->getParam("event_referer"));
        } elseif (strlen($_SERVER['HTTP_REFERER']) > 0) {
            $this->setUrlNext($_SERVER['HTTP_REFERER']);
        } else {
            $disp = new Display($this->getMessagePage());
            $disp->addParam("message", "No redirection set for this event. Please click back and try again");
        }
    }
    if ($this->getUniqueUrl()) {
        if (ereg("uniqid", $this->getUrlNext())) {
            $this->urlNext = ereg_replace("uniqid=.*", "uniqid=".uniqid(rand()), $this->getUrlNext()) ;
        } elseif (ereg("\?", $this->getUrlNext())) {
            $this->urlNext .= "&uniqid=".uniqid(rand()) ;
        }  else {
            $this->urlNext .= "?uniqid=".uniqid(rand()) ;
        }
    }
    $this->setLog("\n Forward to URL:".$this->getUrlNext());
    header("Location: ".$this->getUrlNext()) ;
    exit ;
  }
  /**
   * setUniqueUrl 
   * when the eventcontroler redirect the best way to kill the potential cache
   * is to make the browser believe its a new url.
   * so we add a param to the url with a random number.
   */
  function setUniqueUrl($bool_value=true) {
     $this->unique_url = $bool_value;
  }

  /**
   * getUniqueUrl() 
   * @see setUniqueUrl
   * @return boolean true of false if unique url is turned on 
   */
  function getUniqueUrl() {
    return $this->unique_url;
  }

  /**
   * Return the value to be output
   * This is incompatible with the doForward().
   *
   */

  function doOutput() {
    return $this->output_value;
  }
  
  /**
   * sendHeader() 
   * Send headers from the header array
   * @note we will try to allow multiple headers.
   */
   function sendHeader() {
        if (!empty($this->headers)) {
		    foreach($this->headers as $header) {
				header($header, false);
			}		
	    }
   }
  
  /**
   * addHeader() 
   * add header string to the list of headers to send
   */
   function addHeader($header_string) {
	   $this->headers[] = $header_string;
  
   }
  /**
   * Add a parameter to the event execution
   *
   * The var name must be the variable name inside a string. This methode is used on the initialisation of the event controler
   * or inside an event to save a variable that will be used by other events.
   * The varvalue is optional
   *
   * @param string $varname
   * @param string $varvalue
   * @access public
   * @see updateParam(), addallvars()
   */
  function addParam($varname, $varvalue="" ) {
    if(!empty($varvalue)) {
      $this->params[$varname] = $varvalue ;
    } else {
      global $$varname ;
      $this->params[$varname] = $$varname ;
    }
  }

  /**
   * Add a session var to the event execution
   *
   * The var name must be the variable name inside a string. This methode is used on the initialisation of the event controler
   * or inside an event to save a variable that will be used by other events.
   * The varvalue is optional
   *
   * @param string $varname
   * @param string $varvalue
   * @access public
   * @see addParam(), addallvars()
   */

 function addSession($varname, $varvalue="" ) {
  // global $HTTP_SESSION_VARS;
    if(!empty($varvalue)) {
      $this->sessionparams[$varname] = $varvalue ;
    } else {
      $varvalue = $_SESSION[$varname] ;
      $this->sessionparams[$varname] = $varvalue ;
    }
  }
  /**
   *  Update a parameter
   *
   * This methode is mainly used inside events to modify the value of a parameter
   * that is sent to other events.
   * All parameters are required for now because the global on $varname doesn't work well.
   *
   * @param string $varname
   * @param string $varnewvalue
   * @access public
   * @see addParam(), addallvars()
   */
  function updateParam($varname, $varnewvalue="") {
    if(!empty($varnewvalue)) {
      $this->params[$varname] = $varnewvalue ;
    } else {
      global $$varname ;
      $this->params[$varname] = $$varname ;
    }
  }
  function editParam($varname, $varnewvalue="") {
    $this->updateParam($varname, $varnewvalue="") ;
  }
  function setParam($varname, $varnewvalue="") {
    $this->updateParam($varname, $varnewvalue="") ;
  }

  function updateSession($varname, $varnewvalue="") {
    $this->addSession($varname, $varnewvalue);
  }

  function editSession($varname, $varnewvalue="") {
    $this->addSession($varname, $varnewvalue);
  }

  function getParam($varname) {
    return $this->params[$varname] ;
  }
  function getParams() {
    return $this->params;
  }
  function getSession($varname) {
    return $this->sessionparams["$varname"] ;
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

  function getIsParam($varname) {
    if(!empty($varname) && is_array($this->params)) {
        if(array_key_exists($varname, $this->params)) {
            return true;
        } else {
            return false;
        }
    }
  }
  function getIsSession($varname) {
      if(!empty($varname) && is_array($this->sessionparams)) {
   //   echo "<br>".$varname."->".$this->sessionparams[$varname];
        if(array_key_exists($varname, $this->sessionparams)) {
            return true;
        } else {
            return false;
        }
      }
  }
  /**
   *  Insert all the PHP environement variables in the $params
   * @access private
   * @see addParam(), updateParam()
   */
  function addallvars() {
    //global $HTTP_GET_VARS, $HTTP_POST_VARS, $HTTP_ENV_VARS, $HTTP_COOKIE_VARS, $HTTP_SESSION_VARS ;
    while (list($name, $value) = each($_POST)) {
       if (!$this->getIsParam($name)) {
         $this->addParam($name, $value) ;
       }
    }
    while (list($name, $value) = each($_GET)) {
      if (!$this->getIsParam($name)) {
        $this->addParam($name, $value) ;
      }
    }
    if (is_array($_SESSION)) {
      while (list($name, $value) = each($_SESSION)) {
        if (!$this->getIsSession($name)) {
          $this->addSession($name, $value);
        } 
      }
    }
    if (is_array($_COOKIE)) {
      while (list($name, $value) = each($_COOKIE)) {
        if (!$this->getIsParam($name)) {
          $this->addParam($name, $value);
        }
      }
    }
  }

  /** Set the path to MyDB
   * @param string $path
   */
  function setMydbPath($path) {
    $this->mydbpath = $path ;
  }

  /**  Return the path of MyDB
   * @return string
   */
  function getMydbPath() {
    return $this->mydbpath ;
  }

  /** Set the Local Event Directory
   * @param string $path
   */
  function setEventDir($path) {
    $this->eventdir = $path ;
  }
  
  /** Return the local envent directory
   * @return string $eventdir
   */
  function getEventDir() {
    return $this->eventdir ;
  }
  
  /** Set the next url to go after events execution
   * @change 20081014-PhL: added NULL to dispNext
   * @param string $url
   */
  function setUrlNext($url) {
    $this->urlNext = $url ;
    $this->dispNext = NULL;
  }
  
  /** Return the next url
   * @return string $urlNext
   */
  function getUrlNext() {
    if (is_object($this->dispNext)) {
      if (strtolower(get_class($this->dispNext)) == "display" || strtolower(get_class($this->dispNext)) == "event") {
         $this->urlNext = $this->dispNext->getUrl() ;
      }
    }
    return $this->urlNext ;
  }

  /** Set the next url to go after events execution
   * @param string $url
   */
  function setDisplayNext($disp) {
    if (strtolower(get_class($disp)) == "display" || strtolower(get_class($disp)) == "event") {
       $this->dispNext = $disp ;
    } else {
      $this->setError("<b>Event Controler Error</b> The display object assign to setDisplayNext is not a Display or Event object") ;
    }
  }
  
  /** Return the next url
   * @return string $urlNext
   */
  function getDisplayNext() {
    return $this->dispNext ;
  }
  
  /** Set the message Page, default page that display messages
   * @param string $page
   * @return boolean true
   */
  function setMessagePage($page) {
    $this->messagepage = $page ;
    return true ;
  }
  
  /** AddOutputValue
   *  Cummulate string to be outputed.
   */
   function addOutputValue($value) {
      $this->output_value .= $value;
   }

  /** Set the display of global vars of the request page in the URL
   *  @param boolean $bool
   */
   function setUrlGolbalvars($bool) {
     $this->urlglobalvars = $bool ;
   }

  /** Set the check refer that will refuse all event request not comming from local host
   *  unless the key =  cfg_notrefererequestkey
   *  @param boolean $bool
   */
    function setCheckReferer($bool) {
        $this->checkreferer = $bool ;
    }
    
    function getCheckReferer() {
        return $this->checkreferer  ;
    }
    
  /** Return the page to display message, used in events to built urlNext
   * @return string $messagepage
   */
  function getMessagePage() {
    return $this->messagepage ;
  }

  /** Return Database connexion object
   * @return object sqlConnect $dbc
   */
  function getDbCon() {
    return $this->dbc ;
  }

  /** Add event path
   *  this add a directory with event to be search by the event controler
   *  @param string $event_path
   */

  function addEventPath($event_path) { 
    $this->event_path[] = $event_path; 
  }

  function setSecure($bool) {
    $this->secure_param = $bool;
  }
  function getSecure() {
    return $this->secure_param;
  }

}
?>
