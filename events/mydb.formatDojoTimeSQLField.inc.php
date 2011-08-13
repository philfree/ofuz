<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

 $nbrdate = count($timedojofieldname) ;
  if ($nbrdate>0) {
    for ($i=0; $i<$nbrdate; $i++) {
      $tmptimefieldname = $timedojofieldname[$i] ;
      $fieldname = $fieldnamefortime[$i];
      $updated_time = str_replace("T","",$tmptimefieldname);
      $fields[$fieldname] = $updated_time;
      //print_r($fields);exit;
    }
     $this->updateParam("fields", $fields) ;
  }
 
?>