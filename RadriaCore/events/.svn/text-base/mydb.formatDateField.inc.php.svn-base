<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
 
 /**   Event Mydb.formatDateField
  *
  * Format the date field to a unix timestamp.
  *
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.0
  */

   
  $nbrdate = count($datefieldname) ; 	 
  if ($nbrdate>0) {
    for ($i=0; $i<$nbrdate; $i++) {
      $tmpdatefieldname = $datefieldname[$i] ;
      if ($datefieldhour[$tmpdatefieldname] > 0 || $datefieldminute[$tmpdatefieldname] > 0 || $datefieldsecond[$tmpdatefieldname] > 0 ) {
        $fields[$tmpdatefieldname] =  mktime($datefieldhour[$tmpdatefieldname],$datefieldminute[$tmpdatefieldname],$datefieldsecond[$tmpdatefieldname],$datefieldmonth[$tmpdatefieldname], $datefieldday[$tmpdatefieldname], $datefieldyear[$tmpdatefieldname]) ;
      } else {
        $fields[$tmpdatefieldname] =  mktime(0,0,0,$datefieldmonth[$tmpdatefieldname], $datefieldday[$tmpdatefieldname], $datefieldyear[$tmpdatefieldname]) ;
      }
    }
  }
  $this->updateParam("fields", $fields) ;

?>