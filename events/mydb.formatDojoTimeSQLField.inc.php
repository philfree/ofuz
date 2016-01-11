<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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