<?php
// Copyright 2001 - 2012 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// Licensed under the LGPL 2.0 
// For licensing details, reuse, modification and distribution see license.txt
  /**
   * Base Object 
   * @see BaseObject
   * @package RadriaCore
   */

  /**
   * Base Object Class
   *
   * Its the original object of all.
   * It handle basic attributes and methods common to all objects.
   * Basic and simple error handling
   * This class will need to be fully rewriten soon.
   *
   * @package RadriaCore
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007   
   * @version 4.3.0
   * @access public
   *
   */
#namespace radriacore;

class BaseObject {
        var $objErrorId = 0;
        var $objErrorDesc  = "";
        var $objErrorFile = "radria_errors.log" ;
        var $objLogRunFile = "radria_run.log";
        var $objLogFilesPath = "./";
        var $objLogErrors = true ;
        var $objLogRun = true;
        var $objDisplayErrors = true ;
        var $objDisplayRunLog = false ;
        public $persistence_time_to_live = 0;
        // Name of the object when saved in the session.
        private $object_name = '';
        protected $is_persistent = false;
 
        /**   Flag to tel if the object should be freeed
        * @var bool $private_free
        * @access private
        */
        private $private_free = 0;

        function __construct() {
           if (defined("RADRIA_LOG_ERROR")) {
               $this->objLogErrors = RADRIA_LOG_ERROR;
           }
           if (defined("RADRIA_DISPLAY_ERROR")) {
                $this->objDisplayErrors = RADRIA_DISPLAY_ERROR;
           }
           if (defined("RADRIA_LOG_RUNLOG")) {
                $this->objLogRun = RADRIA_LOG_RUNLOG;
           }
           if (defined("RADRIA_DISPLAY_RUNLOG")) {
               $this->objDisplayRunLog = RADRIA_DISPLAY_RUNLOG;
           }
        }


        /** 
         *  Return the last error message from RADRIA or PHP
         **/
    
        function getErrorMessage() {
            if (strlen($this->objErrorDesc) >0) {
                return $this->objErrorDesc ;
            } 
        }
        
       /**
        *  Return the last error message from PHP only
        **/
    
        function getPHPError() {
            global $php_errormsg ;   
            if (strlen($php_errormsg)> 0) {
                return $php_errormsg ;
            } else {
                return 0;
            }
        }
        
       /**
        *  Check is there is an error already set
        **/
            
        function isError() {
	   if (!empty($this->objErrorDesc)) {
                return true ;
            } else {
                return false ;
            }
        }
        
        /**
            *  Set an Error to be logged in and eventualy displayed.
            *  The error message when logged will be formated with the date, time, uri and referer
            *  The error will be written in the default pas_error.log file.
            *  A PAS error will be thrown.
            **/
    
        function setError($message, $id=0) {
            global $php_errormsg ;
            $requesturi = $_SERVER["REQUEST_URI"] ;
            $referer = $_SERVER["REFERER"] ;
            $this->objErrorDesc = $message ;
            $this->objErrorId = $id ;
            if ($this->objLogFilesPath.$this->objLogErrors) {
                if (!file_exists($this->objLogFilesPath.$this->objErrorFile)) {
                    $fp = @fopen($this->objLogFilesPath.$this->objErrorFile, "w") ;
                    @fwrite($fp, "#PAS error logs \n") ;
                    @fclose($fp) ;
                }
                if(is_writable($this->objLogFilesPath.$this->objErrorFile)) {
                    $logm = "\n".date("Y/m/d - H:m:i") ;
                    $logm.= " - (".get_class($this) ;
                    $logm.= ") - ".$message." uri : ".$requesturi." referrer : ".$referer."\n" ;
                    //error_log($logm, 3, $this->objLogFilesPath.$this->objErrorFile) ;
                    $this->writeLog($logm, $this->objLogFilesPath.$this->objErrorFile);
                }
            }
            if ($this->objDisplayErrors) {
                echo "<font color=red>(".get_class($this).") : ".$message."</font>" ;
            }
            $php_errormsg = "" ; // will that clean the global one, anyway its not currently used
        }
        
       /**
        * Set a message in the run log.
        * Messaged are not formated so you need to add your hown \n (carriage return)
        *
        **/
    
        function setLog($message, $doLog=true) {
            if ($this->objLogRun && $doLog) {
                if (!file_exists($this->objLogFilesPath.$this->objLogRunFile)) {
                    $fp = fopen($this->objLogFilesPath.$this->objLogRunFile, "w") ;
                    fwrite($fp, "#PAS Run logs \n") ;
                    fclose($fp) ;
                }
              //  echo $this->objLogFilesPath.$this->objLogRunFile;
                if(is_writable($this->objLogFilesPath.$this->objLogRunFile)) {
                    //$logm = date("Y/m/d - H:m:i") ;
                    $logm = $message ;
                    //error_log($logm, 3, $this->objLogFilesPath.$this->objLogRunFile) ;

                    $this->writeLog($logm, $this->objLogFilesPath.$this->objLogRunFile);
                }
            }
            if ($this->objDisplayRunLog) {
                echo nl2br($message) ;
            }
        }
        
        
        /**
        * setLogArray(Array $log_array)
        */
        function setLogArray(Array $log_array) {
			$this->setLog(print_r($log_array, true));
			/**
            foreach ($log_array as $key => $value) { 
				if (is_array($value)) { 
					$this->setLogArray($value); 
				} else {
					$this->setLog("\n ".$key." = ".$value) ; 
				}
            }**/
            
        }
        
        /**
        * setLogObject(Object $log_object)
        * Log an object
        */
        function setLogObject($log_object) { 
            $this->setLog(print_r($log_object, true));
        }
        
       /**
        * Private class to write to the log file.
        * @private
        **/
    
        function writeLog($message, $file) {
            $fp = fopen($file, "a") ;
            fwrite($fp, $message) ;
            fclose($fp) ;
        }
    


        /**
        * save give persistance feature to the object by
        * saving it in the session. It also use a destination string
        * to tel when this object must die.
        * This feature is use to create a persistant object through the 
        * current session.
        * The destination tels when the object will release his parameters
        * as global vars and when he will die. (this feature will soon be deprecate)
        *
        * The includes/globalvar.inc.php is managing the garbage collection with 
        * globalevents and garbagevent arrays
        *
        * @param String $objectname  name of the object in the session.
        * @param String $destination string used for globalevents
        * @access public
        * @see Event
        */
        function save($objectname, $destination="", $ttl=1400) {
            $destination = trim($destination);
            if (empty($destination)) {
                $destination = basename($_SERVER['PHP_SELF']);
                if (strstr("?",$destination)) {
                    list($destination, $querystring) = explode("?", $destination) ;
                }
            } elseif ($destination == "never") {
				$destination = "theimpossiblepagename.blah";
				$ttl = 100000000;
			}
            $this->persistence_time_to_live = time() + $ttl;

            if (empty($destination)) {
               $this->setError("Save method couldn't find a destination for ".$objectname.", ".get_class($this)." not saved in session");
               return false;
            } else {
                $_SESSION[$objectname] = $this;
                $GLOBALS[$objectname] = $this;
                $_SESSION['globalevents'][$objectname] = $destination ;
                $_SESSION['garbagevent'][$objectname] = 0;
                $this->setObjectName($objectname);
                $this->setLog("\n (BaseObject:>".get_class($this).") saved object ".$objectname." in the session");
                $this->is_persistent = true;
                return true;
            }
        }
        /**
         * sessionPersistent is an Alias for save(..)
         * @see save((
         */
        function sessionPersistent($objectname, $destination="", $ttl=1400) {
            $this->save($objectname, $destination, $ttl);
        }
        /**
        * Keep a live
        * Checks if the persistant object has reached is time to live limit
        *
        * @return boolean true or false
        */
        function keepALive() {
            $curent_time = time();
            if ($curent_time > $this->persistence_time_to_live) {
                $this->setLog("\n (BaseObject:>".get_class($this).") Session object: ".$this->getObjectName()." keep a live expired, should die now");
                return false;
            } 
            $this->setLog("\n (BaseObject:>".get_class($this).") Session object:".$this->getObjectName()." is a live");
            return true;
        }

        /**
        * Free the object from the current session and take it out
        * of the globalevents
        *
        * @param String $objectname  name of the object in the session.
        * @access public
        * @see Event
        */
        function free($objectname='') {
            if (empty($objectname)) { $objectname = $this->getObjectName(); }
            $_SESSION['globalevents'][$objectname] = "0" ;
            //$_SESSION['garbagevents'][$objectname] = $_SESSION[$objectname] ;
            $this->setLog("\n(BaseObject:>".get_class($this).") Session Persistent object ".$objectname." Destroyed");
            $this->private_free = 0;
            $this->is_persistent = false;
            $_SESSION[$objectname] = '';
            unset($_SESSION[$objectname]);
        }
        
        /**
        *  Set to free the object on the next page.
        *  This is require so the variables of the event are still
        *  load when reload the page.
        *
        * @access public
        * @see Event, isFree()
        */
        function setFree($key="") {
            $this->private_free = 1 ;
            $this->setLog("\n(BaseObject:".get_class($this).") Session Persistent object ".$this->getObjectName()." Set for destruction");
        }
        
        /**
        * Check if the object is ready to be freed.
        *
        * @return bool $free with the status of the object.
        * @access public
        * @see Event, setFree()
        */
        function isFree() {
            return $this->private_free ;
        }

       /** 
        * Set path for the error and log files
        **/
        function setLogFilesPath($path) {
            $this->objLogFilesPath = $path ;
        }
    
       /**
        * Set the file name for the error log
        **/
        function setLogErrorFile($filename) {
            $this->objErrorFile = $filename ;
        }
    
       /**
        * Set the file name for the run log file
        **/
        function setLogRunFile($filename) {
            $this->objLogRunFile = $filename ;
        }
    
       /**
        * Set the loggin of errors on
        * setLogErrors(true) will turn error loggin on
        */
        function setLogErrors($bool) {
            $this->objLogErrors = $bool ;
        }
    
       /** 
        * Set the run log on
        * setLogRun(true) will turn on the writting 
        * of logs in the runlog file.
        * if its "false" all the setLog() will be ignored
        **/
        function setLogRun($bool) {
            $this->objLogRun = $bool;
        }
        function getLogRun() {
            return $this->objLogRun;
        }
    
       /**
        * Set the display of errors
        * setDisplayErrors(true) will turn on the display of error messages
        **/
        function setDisplayErrors($bool) {
            $this->objDisplayErrors = $bool ;
        }
    
       /**
        * Set the display of logs in web pages.
        * setDisplayRunLog(true) will display all the logs when set.
        **/
        function setDisplayRunLog($bool) {
            $this->objDisplayRunLog = $bool ;
        }
       /**
        *  Set the name of this object instance
        *  Mainly used for logs and session manipulation
        *
        *  @param string with the object name
        */
        public function setObjectName($objectname) {
            $this->object_name = $objectname;
        }

       /**
        *  get the name of this object instance
        *  Mainly used for logs and session manipulation
        *
        *  @return string with the object name
        */
        public function getObjectName() {
            if ($this->isPersistent()) {
                return $this->object_name;
            } else {
                return get_class($this);                
            }
        }
        
       /**
        * isPersistent
        * return true is this instance is stored in the
        * session
        * @return boolean true or false
        */
        public function isPersistent() {
            return $this->is_persistent;
        }


    }
?>
