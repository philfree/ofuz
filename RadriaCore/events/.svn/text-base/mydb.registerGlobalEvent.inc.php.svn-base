<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt

  /**
   *  Event Registration in globalEvent
   *
   *  It just transfert the Event object from garbage to global.
   *  This event is always executed with other events so he doesn't have
   *  setUrlNext().
   *
   * <br>- param String $requestSaveObject[] name of the objects to set in globalevents.
   * <br>- global $globalevents, $garbagevents
   *
   * @package RadriaEvents
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007
   * @version 3.9   
   */

 // global $globalevents, $garbagevents ;

  $globalevents = $_SESSION['globalevents'];
  $garbagevents = $_SESSION['garbagevents'];

  $this->setLogRun(true);
  if (defined("RADRIA_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(RADRIA_LOG_RUN_MYDB_EVENTS);
  }
  $this->setLog("\n Start event mydb.registryGlobalEvent ".date("Y-M-d H:i:s"));
  
  $requestSaveObject = $this->getParam("requestSaveObject"); 
  if (is_array($requestSaveObject)) {
      reset($requestSaveObject) ;
      while (list($key, $lobjectname) = each($requestSaveObject)) {
        $this->setLog("\n Object to save: ".$lobjectname);
        //$$lobjectname = new Event($this->getParam("requestSaveEventName")) ;
        $$lobjectname = new Display($key) ;
        $globalevents[$lobjectname] = $key;
        $garbagevents[$lobjectname] = 0 ;
        if (is_array($paramstosave)) { 
        reset($paramstosave) ;
            while (list($key, $varname) = each($paramstosave)) {
                $this->setLog("\n Param: ".$varname." = ".$$varname);
                if (is_array($$varname)) { 
                  $$lobjectname->editParam($varname, $$varname) ;
                  $this->setLog(" is an array"); 
                } else {
                   $$lobjectname->editParam($varname, stripslashes($$varname)) ;
                }                
            }
        } else { 
            $this->setError(" No Param to be saved for object ".$lobjectname." add a ->addParam before calling ->requestSave"); 
        }
        $this->setLog("\n Do Save for object ".$lobjectname." die on page ".$globalevents[$lobjectname]);
        $$lobjectname->save($lobjectname, $globalevents[$lobjectname]) ;
      }
      //session_register("globalevents") ;
      //session_register("garbagevents") ;
      $_SESSION['globalevents'] = $globalevents;
      $_SESSION['garbagevents'] = $garbagevents;
  } else {
      $this->setError("requestSavedObject is not an Array") ;
  }
?>