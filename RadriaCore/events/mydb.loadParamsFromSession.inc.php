<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
/**   
  * Event Mydb.loadParamsFromSession
  *
  * This restore to the event controler the varibles of events stored 
  * in the session.
  * <br>- param string fields_{fieldsnames}
  *
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.0  
  */

use RadriaCore\Radria\Display;

  $disperr = new Display($this->getMessagePage()) ;

  $mydb_paramkeys = $_SESSION["mydb_paramkeys"];
  $mydb_eventkey = $_REQUEST["mydb_eventkey"];
  $logrun = $this->getLogRun();
  if (defined("RADRIA_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(RADRIA_LOG_RUN_MYDB_EVENTS);
  }
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
            $this->addParam($varname, $varvalue) ;
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
        $this->setDisplayNext($disperr) ;
  }
  $this->setLogRun($logrun);
?>