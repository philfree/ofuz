<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt

 /**   Event Mydb.formatDateSQLField
  *
  * Format the date field to a SQLDate format.
  * get value from 3 fields, minimum check on consistancy and 
  * update the fiedls array with a valide SQLDate
  *
  * <br>- param array $fields contains all the values of the fields
  * <br>- param array datesqlfieldname contains all the names of fields that are date fields
  * <br>- param array datefieldyear indexed on fields name contains year value
  * <br>- param array datefieldmonth indexed on fields name contains month values
  * <br>- param array datefieldday indexed on fields name contains day value
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.0  
  */


  $nbrdate = count($datesqlfieldname) ;
  if ($nbrdate>0) {
    for ($i=0; $i<$nbrdate; $i++) {
      $tmpdatefieldname = $datesqlfieldname[$i] ;
      if (($datefieldyear[$tmpdatefieldname]>0) && ($datefieldmonth[$tmpdatefieldname]>0) && ($datefieldmonth[$tmpdatefieldname]<13) && ($datefieldday[$tmpdatefieldname]<32) && ($datefieldday[$tmpdatefieldname]>0)) {
        $fields[$tmpdatefieldname] = $datefieldyear[$tmpdatefieldname]."-".$datefieldmonth[$tmpdatefieldname]."-".$datefieldday[$tmpdatefieldname];
      } else {
        $fields[$tmpdatefieldname] = "";
      }
    }
  }
  $this->updateParam("fields", $fields) ;

?>