<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


  /**
   * Global events to manage Event specific global vars.
   * This is where the Event saved in session are managed and killed.
   *
   * @package PASSiteTemplate
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @version 3.9
   */

  if (!is_array($_SESSION['globalevents'])) {
    $globalevents['Init'] = "0" ;
    $_SESSION['globalevents'] =  $globalevents;
    //session_register("globalevents") ;
  } else {
    while (list($key, $value)= each($_SESSION['globalevents'])) { 
//echo "\n ".$key."->".$value;
      if (strlen($value) > 0 && $value != "0") {
   //     if (strlen($value) > 0) {
        if((eregi($value, $_SERVER['PHP_SELF'])) && (is_object($_SESSION[$key]))) {
            if (is_subclass_of($_SESSION[$key], "Display") || (get_class($_SESSION[$key]) == "Display")) {
                $params = $_SESSION[$key]->getParams() ;
                if (is_array($params)) {
                    while(list($name, $value) = each($params)) {
                        $$name = $value ;
                    }
                }
            }
            $_SESSION[$key]->setFree($key) ;
            //$_SESSION[$key]->setLog("\n set free:".$key);
        } elseif(is_object($_SESSION[$key]) && ($_SESSION[$key]->isFree())) {
            $_SESSION[$key]->free($key) ;
        } elseif(is_object($_SESSION[$key])) {
            if (!$_SESSION[$key]->keepALive()) { $_SESSION[$key]->free($key);  };
        }
      } else { 
        $_SESSION['globalevents'][$key] = "0"; 
        if (array_key_exists($key, $_SESSION)) {
            if (is_object($_SESSION[$key]) && is_subclass_of($_SESSION[$key],"BaseObject")) {
               $_SESSION[$key]->setError("No end of life page (".$value."). A bug in the Global events. This event (".$key.") will be killed now."); 
               $_SESSION[$key]->free($key);
            } else { unset($_SESSION[$key]); }
        }
      }
    }
    if (!is_array($_SESSION['garbagevents'])) {
      $garbagevents['init'] = 0 ;
      $_SESSION["garbagevents"] = $garbagevents ;
    }
    while (list($key, $value)= each($_SESSION['garbagevents'])) {
        if (array_key_exists("mydb_evens", $_REQUEST)) {
          if(($value) && ($_REQUEST['mydb_events'][20] != "mydb.registerGlobalEvent" ) && (is_object($_SESSION[$key]))) {
             $okey = $_SESSION[$key];
             $okey->free($key) ;
             unset($_SESSION[$key]);
          }
        }
    }
  }
?>
