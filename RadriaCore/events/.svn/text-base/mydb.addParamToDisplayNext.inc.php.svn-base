<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
   /**
    * mydb.addParamToDisplayNext.inc.php
    *
    * This event take the current display next and add
    * All the param to it.
    * A display object need to have already been created by a previous object
    * New 3.9 version will now save the param in the session variable $_SESSION['previous_event_params']
    * It was breaking to much older scripts. Added a param to choose between session and url get.
    * 
    *
    * @param  use_session string 'yes' to use sessions instead of the url get.
    * @note the url get will break in IE if the URI is to long. It returns a DNS error.
    * @package RadriaEvents   
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2001-2007
    * @version 4.0
    */
    
    $this->setLogRun(false);
    if (defined("RADRIA_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(RADRIA_LOG_RUN_MYDB_EVENTS);
    }
    $this->setLog("\n\nEvent mydb.addParamToDisplayNext start :".date("Y-m-d H:i:s"));
    $use_session = $this->getParam("use_session");

    if ($use_session == "no") {
        $d_cur = $this->getDisplayNext();
        if (is_object($d_cur)) {
            foreach($this->params as $key=>$value) {
                if ($d_cur->getParam($key) == "") {
                    if (!empty($value) && !empty($key)) { 
                        if ($key != "mydb_events"
                        && $key != "globalevents"
                        && $key != "garbagevents"
                        && !is_object($value)) {
                            $d_cur->addParam($key, $value);
                            $this->setLog("\nParamtodisplay : $key = $value");
                        }
                    }
                }
            }
            $this->setDisplayNext($d_cur);
        } else {
            $this->setLog("\nEvent ParamToDisplay : no display object found");
        }
    } else {
        $previous_event_params = Array();
        foreach($this->params as $key=>$value) {
            if ($key != "mydb_events"
                && $key != "globalevents"
                && $key != "garbagevents"
                && !is_object($value)) {
                    $previous_event_params[$key] = $value;
                    $this->setLog("\nParamtodisplay : $key = $value");
            }
        }
        $_SESSION['previous_event_params'] = $previous_event_params;
        $d_cur = $this->getDisplayNext();
        if (is_object($d_cur)) {
            $d_cur->addParam("reload_fields", "Yes");
            $this->setDisplayNext($d_cur);
        }
    }

    $this->setLog("\nEvent mydb.addParamToDisplayNext stop");
    $this->setLogRun(false);
?>