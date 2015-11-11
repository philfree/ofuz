<?php
namespace RadriaCore\Radria;
// Copyright 2001 - 2012 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
   * Event
   * This file contains the Event and RecordEvent class.
   *
   * @see Event, RecordEvent
   * @package RadriaCore
   */
  /**
   * Event Class
   * Used to manage event calls true forms or links. Built the url to call an event.
   * Insert the hidden field in a form to call and proceed and event.
   * Based on display it can use persistance thrue sessions
   * By default the Event object send one event but you can add sub events
   * using the addEvent method, The differents events will then share the
   * sames parameters.
   * @package RadriaCore2
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2012   
   * @version 5.0.0
   * @access public
   */

   use RadriaCore\Radria\Display;
class Event extends Display {

  /**  name of the event
   * @var String $name
   */
  var $name ;

  /**  action for this event
   * complexe events sometime uses an action parameter to manage differents states of the event.
   * When action is not required its better to use sub events.
   * @var String $action
   */
  var $action ;

   /**  level of the event
   * When multiples events in a request are sent at the same time to the
    * eventcontroler there execution will be ordered by there level from 1->10000.
   * @var int $level
   */
  var $level = "";

  /**  URL of an event controler that will execute the event
   * @var String $tbl_event
   */
  var $eventcontroler = "eventcontroler.php" ;

  /**  file flag. true if there is a file field in the form.
   * @var boolean $file
   */
  var $file = false ;

  /**  list of variable that will be saved with the instance of the object in the session
   * @var array $paramtosave
   */
  var $paramstosave ;

  /**  Set the event to send is parameters in a more secure way. It take more processing time, but it will protect
   *    your site from people guessing process by looking at variables in GET and POST.
   * @var bool $secure
   */
  var $secure = true;

  /**  It is the key send as parameter to reload the parameters associates to it.
   *   It is originaly emtpy and generated on its first call
   * @var string $securekey
   */
  var $securekey = "";
  
  /**  Its will hide the securekey.
   *   Only needed for Events composed of multiple events to avoid key conflict.
   * @var boolean $hide_key
   */
  var $hide_key = false;

   /**  This is an array with the list of variable not to hide when secure mode is on.
   * @var array $do_not_hide
   */ 
  var $do_not_hide;
  
    /**  This is a string with the name of the target window or frame.
   * @var string $target  used to set target with getformheader. Doesnt apply to getlink
   */  
  var $target = "";
  
   /** Time to live 
    * 
    * @var int $time_to_live number of second additional this event has before its params are deleted.
    */
   var $time_to_live = 0;
 
   /** Referer
    * 
    * @var string $pas_event_referer record the current page so the eventcontroler can use it as a referer
    */
   var $pas_event_referer = "";

   /** DOM unique id
    * 
    * @var string $domid record the domid for this event if set as link or form.
    */
   var $domid = "";

   // just testing something (compliance vs ajax)
   public $amp_encoded = false;
 
   // Base web path
   public $base_web_path = "";

  /**
   * Constructor, create the event object with name and action
   * parameters.
   * The goto param is preset to the location where the event is created.
   * The goto param is used in the events to define the url to call
   * after executing the event.
   * The event key is set in the constructor so a valid event key can be displayed even 
   * if the event is manualy set to not secure
   *
   * @param String $name name of the event
   * @param String $action action for this event
   * @global $PHP_SELF, $QUERY_STRING
   * @constant RADRIA_EVENT_SECURE to set the event to secure or none secure mode
   * @access public
   */
  function __construct($name="", $action="") {
    global $PHP_SELF, $QUERY_STRING ;
      parent::__construct();
    if (defined("RADRIA_LOG_RUN_EVENT")) {
        $this->setLogRun(RADRIA_LOG_RUN_EVENT);
    }
    $this->setName($name) ;
    $this->setAction($action) ;
    $this->setLevel(100) ;    
    if (defined("RADRIA_EVENT_SECURE")) {
        $this->setSecure(RADRIA_EVENT_SECURE);
    } else {
        define("RADRIA_EVENT_SECURE", true);
        $this->setSecure(true);
    }
    if (defined("RADRIA_EVENT_CONTROLER")) {
        $this->setEventControler(RADRIA_EVENT_CONTROLER);
    }
    if (!defined("RADRIA_EVENT_ABSOLUTE_PATH")) { define("RADRIA_EVENT_ABSOLUTE_PATH", false); }

    if (RADRIA_EVENT_ABSOLUTE_PATH) {
       $this->base_web_path = "/"; 
    } else {
       $this->base_web_path = "";
    }

    if (RADRIA_EVENT_SECURE) {
        if ($_SERVER["HTTPS"] == "on") { $http = "https://"; } else { $http = "http://"; } 
        $this->addParam("event_referer",  $http.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
    }
  }

  function getAmp() {
    if ($this->amp_encoded) {
      return "&amp;";
    } else {
      return "&";
    }
   
  }

  /**
   * getUrl return a welformed URL in a string with eventcontroler url
   * and all the parameters ready to be sent to the eventcontroler.
   * @access public
   */
  function getUrl() {
    $url = $this->base_web_path.$this->eventcontroler."?mydb_events[".$this->level."]=".urlencode($this->name) ;
    if (!empty($this->action)) {
      $url .=$this->getAmp()."eventaction=".urlencode($this->action) ;
    }
    $key = $this->getSecureKey() ;
    if (RADRIA_EVENT_SECURE) {
        $_SESSION['mydb_paramkeys'][$key] = Array();
        $this->addParam("event_timestamp", $this->getTimeToLive() + time());
    }
    if ($this->getSecure()) {
        global $mydb_paramkeys;
        $newparams = Array();
      //  $this->addParam("mydb_eventkey", $key, "no_secure_hidden"); 
        // $this->getSecureKey() ; avoid double generation of the key.
        if (is_array($this->params)) {
            foreach($this->params as $varname=>$varvalue) {
                if (eregi("mydb_events", $varname) || $this->do_not_hide[$varname]){
                    $url .= $this->getAmp().$varname."=".urlencode($varvalue) ;
                } else {
                    $newparams[$varname] = $varvalue;
                }
            }
        }
        $_SESSION['mydb_paramkeys'][$key] = $newparams ;
        //session_register("mydb_paramkeys") ;
        $url .= $this->getAmp()."mydb_events[0]=EventControler->eventLoadParams".$this->getAmp()."mydb_eventkey=".urlencode($key);
    } else {
        if (is_array($this->params)) {
           $this->addParam("mydb_eventkey", $key);
           reset($this->params) ;
            while(list($varname, $varvalue) = each($this->params)) {
                $$varname = $varvalue;
                if (is_array($$varname)) {
                    foreach($$varname as $key => $value) {
                        if (is_array($value)) {
                            foreach($value as $key2 => $value2) {
                                if(!is_array($value2)) {
                                $url .= $this->getAmp().$varname."[".$key."][".$key2."]=".urlencode($value2);
                                } else {
                                    $this->setError("The param is an array with more than 2 dimentions. \$arrayname[][][] or more. Make sure its only 2 dimenssions max.");
                                }
                            }
                        } else {
                            $url .= $this->getAmp().$varname."[".$key."]=".urlencode($value) ;
                        }
                    }
                } else {
                  $url .=$this->getAmp().$varname."=".urlencode($varvalue) ;
                }
            }
        }
        /*
        if (is_array($this->paramstosave)) {
            reset($this->paramstosave) ;
            while(list($key, $varname) = each($this->paramstosave)) {
                $url .="&paramstosave[]=".urlencode($varname) ;
            }
        }
        */
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
     if (strlen($this->getTarget()) > 0) {
        $target = " target=\"".$this->getTarget()."\"";
     } else { $target = ""; }
     $link = "<a id=\"".$this->getJsName()."\" href=\"".$this->getUrl()."\"".$properties.$target.">".$linklabel."</a>";
     return $link;
  }
  /**
   * getFornHeader return a string with the header of the form for that event.
   *
   * @access public
   */
  function getFormHeader() {
    $target = "";
    if ($this->getTarget() != "") { $target = " target=\"".$this->getTarget()."\""; }
    if ($this->file) {
      $out = "\n<form id=\"".$this->getJsName()."\" name=\"".$this->getDomId()."\" action=\"".$this->base_web_path.$this->eventcontroler."\" method=\"post\" enctype=\"multipart/form-data\"".$target.">" ;
 //         $out .= "<INPUT TYPE=\"hidden\" NAME=\"MAX_FILE_SIZE\" VALUE = \"1000000\">";
    } else {
      $out = "\n<form id=\"".$this->getJsName()."\" name=\"".$this->getDomId()."\" action=\"".$this->base_web_path.$this->eventcontroler."\" method=\"post\"".$target.">" ;
    }
    return $out ;
  }

  /**
   * getFornHeader return a string with the events hidden fields required
   * for the eventcontroler and all the parameters of the event or events.
   *
   * @access public
   */
  function getFormEvent() {
    $out = "\n  <input id=\"".$this->getDomId()."_mydb_events_".$this->level."_\" type=\"hidden\" name=\"mydb_events[".$this->level."]\" value=\"".$this->name."\"/>" ;
    if (!empty($this->action)) {
        $out .= "\n  <input type=\"hidden\" name=\"eventaction\" value=\"".$this->action."\"/>" ;
    }
    $key = $this->getSecureKey() ;
    $_SESSION['mydb_paramkeys'][$key] = Array();
    if ($this->getSecure()) {
        global $mydb_paramkeys;
        $newparams = Array();
        $this->addParam("event_timestamp", $this->getTimeToLive() + time());
        $this->addParam("mydb_eventkey", $key, "no_secure_hidden"); 
        //$key = $this->getSecureKey() ;
        if (is_array($this->params)) {
            foreach($this->params as $varname=>$varvalue) {
                if (eregi("mydb_events", $varname) || $this->do_not_hide[$varname]){
                    if (is_array($varvalue)) {
                        foreach($varvalue as $key => $value) {
                            $out .="\n  <input id=\"".$this->getDomId()."_".$this->getJavascriptSafeName($varname)."_".$this->getJavascriptSafeName($key)."_\" type=\"hidden\" name=\"".$varname."[".$key."]\" value=\"".$value."\">" ;
                        }
                    } else {
                        $out .=  "\n <input id=\"".$this->getDomId()."_".$this->getJavascriptSafeName($varname)."\" type=\"hidden\" name=\"".$varname."\" value=\"".$varvalue."\">" ;
                    }
                } else {
                    $newparams[$varname] = $varvalue;
                }
            }
        }
        $_SESSION['mydb_paramkeys'][$key] = $newparams ;
        //session_register("mydb_paramkeys") ;
        $out .= "\n <input id=\"".$this->getDomId()."_mydb_loadParamsFromSession\" type=\"hidden\" name=\"mydb_events[0]\" value=\"mydb.loadParamsFromSession\"/>" ;
//      $out .="\n  <input type=\"hidden\" name=\"mydb_eventkey\" value=\"".$key."\"/>" ;
    } else {
        if (is_array($this->params)) {
            if(!$this->getHideKey()) {
               $this->addParam("mydb_eventkey", $key);
            }
            reset($this->params) ;
            while(list($varname, $varvalue) = each($this->params)) {
                $$varname = $varvalue;
                if (is_array($$varname)) {
                    foreach($$varname as $key => $value) {
                        $out .="\n  <input id=\"".$this->getDomId()."_".$this->getJavascriptSafeName($varname)."_".$this->getJavascriptSafeName($key)."_\" type=\"hidden\" name=\"".$varname."[".$key."]\" value=\"".$value."\"/>" ;
                    }
                } else {
                   $out .="\n  <input id=\"".$this->getDomId()."_".$this->getJavascriptSafeName($varname)."\" type=\"hidden\" name=\"".$varname."\" value=\"".$varvalue."\"/>" ;
                }

            }
        }
        /*
        if (is_array($this->paramstosave)) {
            reset($this->paramstosave) ;
            while(list($key, $varname) = each($this->paramstosave)) {
                $out .="\n  <INPUT TYPE=\"HIDDEN\" NAME=\"paramstosave[]\" VALUE=\"".$varname."\">" ;
            }
        }
        */
    }
    return $out ;
  }
  
  /** 
   *   getEvent
   * Simplified version of the getEventForm()
   * but this one only adds one event and its param. Its adding eventActions with its param to an existing form.
   */
    
  function getEvent() {
	$this->delParam("event_referer"); // removing it because its added in this class constructor.
	
	$out = "\n  <input id=\"".$this->getDomId()."_mydb_events_".$this->level."_\" type=\"hidden\" name=\"mydb_events[".$this->level."]\" value=\"".$this->name."\"/>" ;
	while(list($varname, $varvalue) = each($this->params)) {
		$out .="\n  <input id=\"".$this->getDomId()."_".$this->getJavascriptSafeName($varname)."\" type=\"hidden\" name=\"".$varname."\" value=\"".$varvalue."\"/>" ;		   
	}	
	return $out;  
  }

  /**
   * getFormFooter return a string with the footer of the form for that event.
   * 
   * @param string submitvalue label/value for the submit button
   * @return string HTML submit button tag.
   * @access public
   */
  function getFormFooter($submitvalue="") {
    $out = "";
    if (!empty($submitvalue)) {
   //   $submitvalue = "Submit" ;
    $out .= "\n  <input type=\"submit\" name=\"submitaction\" value=\"".$submitvalue."\"/>" ;
    }
    $out .= "\n</form>" ;
    return $out ;
  }

  /**
   * Set an action variable
   * this was used to separet one eventAction in multiple events.
   * @deprecate Its better to set params to interact with event actions.
   * @param string $action name of the action to execute. 
   */
  
  function setAction($action) {
    $this->action = $action ;
  }
  
  /**
   * Set the name of the Main Event action
   * @param string Event name
   * @param level Event level of execution. 
   */
  function setName($name, $level=0) {
    $this->name = $name;
    if ($level) {
      $this->level = $level;
    }
  }
   /**
   * Return the Event name
   * @return string Event name
   */ 
  function getName() {
    return $this->name ;
  }

  /**
   * getJavascriptSafeName()
   * Convert a string to a usable javascript name.
   * @param string name
   * @return string javascript safe.
   */
   function getJavascriptSafeName($var_name) {
        $js_var_name = '';
        $js_not_friendly_char = Array(" ", ".", "-", "*", "+", "|", "&", "'", "\"", ">", "[", "]");
        $js_var_name = str_replace($js_not_friendly_char,"_", $var_name);
        return $js_var_name;
   }

   /**
   * Return a javascript friendly conversion of the event name
   * The optional param is just to be used if the event name is not
   * already set. 
   * Note: It will not set the event name.
   * @param string Event name
   * @return string JS friendly Event name
   */ 
  function getJsName($name="") {
        $jsname = '';
        //$js_not_friendly_char = Array(" ", ".", "-", "*", "+", "|", "&", "'", "\"", ">");
        if (empty($name)) {
            $name = $this->getDomId();
        }
        if (empty($name)) {
            $name = $this->getName();
        }
       //$jsname = str_replace($js_not_friendly_char,"_", $name);
       $jsname = $this->getJavascriptSafeName($name);
       if (isset($GLOBALS['eventdomid_'.$jsname])) {
          $this->setDomId($jsname."_".$GLOBALS['eventdomid_'.$jsname]++);
       } else {
          $GLOBALS['eventdomid_'.$jsname] = 1;
          $this->setDomId($jsname);
       }
       return $this->getDomId(); 
  }
   /**
   * depricate use getJsName()
   */
    function getFormName($name="") {
       return $this->getJsName($name);
    }

   /**
    * Get DOM ID
    * This return the DOM id generated by getJsName()
    * 
    * @return string of the dom id generated by getJSName
    */
    function getDomId() {
       return $this->domid;
    }

    /**
     * Set the domid
     * This method should not be used in general unless
     * the dom id of the event html element is already known 
     * its better to use getJsName() to generate a uniq dom id.
     * @param domid uniq dom id for the form or link id.
     */

    function setDomId($domid) {
       $this->domid = $domid;
    }

  /**
   * Save the global variable of the current display in the event param.
   * The display offen requires global vars from event object.
   * If the curent event want to call the exact same display it must save
   * current global vars used.
   *
   * @param String $objectname  name of the object in the session.
   * @param String $destination string used for globalevents
   * @access public
   * @see Event
   */
  function addPageVars($pglobalevents="") {
    if ($pglobalevents == "") {
      global $globalevents ;
    }  else {
      $globalevents = $pglobalevents ;
    }
    while (list($key, $value)= each($globalevents)) {
    //echo $key ;
    global $$key ;
      if (is_object($$key) && ($$key->isFree())) {
        $tmp_params = $$key->getParams() ;
        while(list($name, $value) = each($tmp_params)) {
          if (!eregi("mydb_events", $name)) {
            $this->params[$name] = $value ;
          }
        }
      }
    }
  }
  
   /**
   * Request a persistance
   * This create the object but doesn't register it in the globalevents array
   * but in garbagevents instead.
   * It will also add the webide.registerGlobalEvent event that will get the object
   * name and assign it to globalevent, then the object will leave until it reach its target.
   * Before calling requestSave you need to addParamToSave or just addParam for all the params
   * you want that event to keep persistant.
   *
   * @param String $objectname  name of the object in the session.
   * @param String $destination string used for globalevents
   * @param integer $level level of the execution for the requestSaveEventName
   * @access public
   * @see Event
   */
  function requestSave($objectname, $destination="", $level=20) {
    if (is_array($this->params)) {
      reset($this->params) ;
      while(list($varname, $varvalue) = each($this->params)) {
        $this->paramstosave[] = $varname ;
      }
    }
    $this->addParam("paramstosave", $this->paramstosave) ;
    $this->addEvent("mydb.registerGlobalEvent", $level) ;
    $valueSaveObject[$destination] = $objectname;
    $this->addParam("requestSaveObject", $valueSaveObject) ;
    //$this->addParam("requestSaveEventName", $this->getName()); 
    $this->setLog("\n Event: Request to be saved as:".$objectname." until:".$destination);
  }
  
  /**
   * addEventAction add event action to this events.
   * The sub events should be ordered by level for the execution order
   * they will share the same parameters has the other events.
   * Sub event can't have actions .
   * Each event needs to have a unique level.
   *
   * @param String $name name of the event
   * @param String $level for this event
   */

  function addEventAction($name, $level=0) {
    $this->addEvent($name, $level);
  }

  function addEvent($name, $level=0) {
    if ($level) {
      $varname = "mydb_events[".$level."]" ;
    } else {
      $varname = "mydb_events[10]" ;
    }
      $this->params[$varname] = $name ;
  }  

  /**
   * setGotFile to tail the event that there is a file field in the form
   * @access public
   */
  function setGotFile() {
    $this->file = true ;
  }
  
  /**
   * addParam Overwrite the default addParam from display to add the options
   * The first option is "no_secure_hidden" to show the param even if event 
   * secure mode is active.
   *
   * @param String $varname name of the param to add, it will be the name of the variable.
   * @param String $varvalue value of that param and future variable.
   * @param String $option default "" name of the option to apply to that param.
   */
  function addParam($varname, $varvalue, $option="") {
    $this->params[$varname] = $varvalue ;
    if ($option=="no_secure_hidden") {
        $this->do_not_hide[$varname] = 1;
    }
    return $this;
  }
  
  /**
   * delParam remove a parameters
   * 
   * @param String $varname name of the param to remove
   */
  function delParam($varname) {
	 unset($this->params[$varname]); 
  }
  
  /**
   * add a param to be saved.
   * When the getURL() or getFormEvent() are run a list of param are set to be saved in the
   * session when a requestSave as been issues.
   * Thie method allow you to add variable that will be saved without having to preset them with
   * addParam(). This method is required if you are in secure mode for param set after the requestSave 
   * call or comming from user input.
   *
   * @access private
   * @param string $varname name of the variable to be saved with the event object.
   * @see requestSave()
   */
  function addParamToSave($varname) {
    $this->paramstosave[] = $varname ;
  }
  
  /** Set execution level of the main Event action
   * The main event action is the event name set on the object construction
   * @param integer $level level of execution.
   */
  function setLevel($level=10) {
    $this->level = $level ;
    return $this;
  }
  
  function getLevel() {
    return $this->level ;
  }
  /**
   *  Set the url of the eventcontroler
   *  The event controler is a php files with an eventcontroler object.
   *  The eventcontroler object process events with their params and event actions.
   *  By default the file is eventcontroler.php
   *  @param string url of the eventcontroler
   */
  function setEventControler($url) {
    $this->eventcontroler = $url;
  }

  /**
   *  return the current event controler
   *  @return string url of the eventcontroler
   *  @see setEventControler()
   */
  function getEventControler() {
    return $this->eventcontroler ;
  }

  /**
   * check is the event is in secure mod or not.
   * @return boolean
   */
  function getSecure() {
    return $this->secure;
  }
  
  /**
   * Set the event in secure or unsecure more.
   * The event in secure mode will not display its params in URLs or Forms hidden fields
   * instead it will save in the session all the params and they will be retrieved by 
   * the event controler when the event is executed.
   * @param boolean $bool false or true
   */
  function setSecure($bool) {
    $this->secure = $bool ;
    return $this;
  }
  
  /**
   * Generate a random secure key to register the events params in the session
   * @access private
   * @return string secure md5 rand key.
   */
  function getSecureKey() {
    $this->securekey = md5(uniqid("MYDBPARAMKEY")) ;
    return $this->securekey;
  }
  
  /** 
   * Set Hide the secure key
   * @access public
   * @param boolean
   * @return void
   */
  function setHideKey($bool) {
    $this->hide_key = $bool;
    return $this;
  }
  
  /** 
   * Get Hide key to display or not the key.
   * @access public
   * @return boolean 
   */
  function getHideKey() {
    return $this->hide_key;
  }
  
  /**
   * Set a window name or frame name for the html link or html to target
   * @access public
   * @param string target name
   */
  function setTarget($target) {
      $this->target = $target;
  }
  /**
   * Return the currently set target.
   * @access public
   * @return string target name
   * @see setTarget()
   */
  function getTarget() {
      return $this->target;
  }
  /**
   * Set the number of second this event can live.
   * When in secured mode all the params are stored in the server side session
   * A general time to live is set by default before the params of the events 
   * are removed from the session.
   * This method will let you over write the default time to live to make it
   * shorter or longueur.
   * 
   * @access public
   * @param string target name
   */
  function setTimeToLive($timeout) {
    $this->time_to_live = $timeout;
  }
  /** 
   * Return the current number of second for the ttl of the event.
   * @return integer number of second the event's params will live in the session.
   * @see setTimeToLive()
   */
  function getTimeToLive() {
    return $this->time_to_live;
  }

}
