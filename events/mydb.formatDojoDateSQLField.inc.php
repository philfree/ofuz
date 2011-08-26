<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

 $nbrdate = count($datedojofieldname) ;
  if ($nbrdate>0) {
    for ($i=0; $i<$nbrdate; $i++) {
      $tmpdatefieldname = $datedojofieldname[$i] ; 
      //$tmpdateformat = $dformat[$i]; 
      $fieldname = $fieldnamefordate[$i];
      /*if($tmpdateformat == 'dd-MM-y'){
        $tmp_datefieldname = explode("/", $tmpdatefieldname) ;
        $day =  $tmp_datefieldname[0];
        $mon =  $tmp_datefieldname[1];
        $year = $tmp_datefieldname[2];
        $updated_date = $year."-".$mon."-".$day;
        
      }else{
         $updated_date = date('Y-m-d', strtotime($tmpdatefieldname));
      }*/
      $fields[$fieldname] = $tmpdatefieldname;
      //print_r($fields);exit;
    }
     $this->updateParam("fields", $fields) ;
  }
 
?>