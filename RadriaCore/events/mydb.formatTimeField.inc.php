<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
 /**   
  * Event Mydb.formatTimeSQLField
  *
  * Format the time field.
  * <br>- param array fields 
  * <br>- param string timefieldname
  * <br>Option :
  * <br>- param string errorpage page to display the errors
  * 
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.0
  */
  global $strWrongTimeFormat;
  if (!isset($strWrongTimeFormat)) {
    $strWrongTimeFormat = "Wrong time format should be : HH:MM:SS with hours < 24, minutes < 60 and seconds < 60";
  }
  
  $nbrtime = count($timefieldname) ;
  if ($nbrtime>0) {
    for ($i=0; $i<$nbrtime; $i++) {
      $tmptimefieldname = $timefieldname[$i] ;
      list($hour, $min, $sec) = explode(":",$fields[$tmptimefieldname]) ; 
      if ($hour > 24 || $min > 60 || $sec > 60) {
        $message = $strWrongTimeFormat;
        $this->updateParam("doSave", "no") ;
        if (strlen($errorpage)>0) {
           $dispError = new Display($errorpage) ;
         } else {
           $dispError = new Display($this->getMessagePage()) ;
        }
        $dispError->addParam("message", $message) ;
        $this->setDisplayNext($dispError);
      }
    }
  }

?>