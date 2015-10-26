<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
 /**   Event Mydb.fieldToArray
  *
  * Move variable like fields_fieldname into the $fields array.
  * if the fields with $fields[fieldnam] exits it will be overighten by 
  * the new value.    
  * <br>- param string fields_{fieldsnames} 
  *
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.0  
  */

    while(list($key, $value) = each($fields)) {

        if (isset(${"fields_".$key})) {
           $newfields[$key] = ${"fields_".$key} ;
        } else {
            $newfields[$key] = $value  ;
        }
    }

    if (is_array($datefieldname)) {
//       $datefields[] = $datefieldname ;
       foreach($datefieldname as $name) {
 	  $datefields[] = $name;
       }
    }
    if (is_array($datesqlfieldname)) {
       foreach($datesqlfieldname as $name) {
           $datefields[] = $name;
       }
//       $datefields[] = $datesqlfieldname ;
    }
    if (is_array($datefields)) {
        foreach($datefields as $fname) {
            if (isset(${"datefieldday_".$fname})) {
                $newdatefieldday[$fname] = ${"datefieldday_".$fname};
            }
            if (isset(${"datefieldmonth_".$fname})) {
                $newdatefieldmonth[$fname] = ${"datefieldmonth_".$fname};
            }
            if (isset(${"datefieldyear_".$fname})) {
                $newdatefieldyear[$fname] = ${"datefieldyear_".$fname};
            }
        }
        $this->addParam("datefieldday", $newdatefieldday);
        $this->addParam("datefieldmonth", $newdatefieldmonth);
        $this->addParam("datefieldyear", $newdatefieldyear);
    }
    $fields = $newfields ;
    $this->updateParam("fields", $newfields) ;

?>