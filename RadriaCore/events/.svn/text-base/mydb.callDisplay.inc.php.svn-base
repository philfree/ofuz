<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
   *  This event is used to call and display reports.
   *  DEPRECATED
   * <br>- param int $mydb_num its the number of the report that need to be displayed.
   * <br>- param String $goto to tel in what page call the report.
   * <br>- param bool $setdistributed (true or false) tell the the application is distributed. (no passing parameters in urls).
   *
   * @package RadriaEvents
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2004
   * @version 3.0
   * @deprecated
   */

//  global $infoDisplay ;
//echo "--".$doSave."--" ;
  global $displayTableOrder, $globalevents, $manageData ;
  session_unregister("displayTableOrder") ;
  session_unregister("manageData") ;
  $globalevents[displayTableOrder] = 0;
  $globalevents[manageData] = 0 ;
  session_register("globalevents") ;

if ($doSave != "no") {
  $infoDisplay = new Display() ;
  $infoDisplay->addParam("mydb_num", $mydb_num) ;
  if ($displaytype == "form") {
    $infoDisplay->addParam("goto", $gotonext) ;
   // $infoDisplay->addParam("toto", "toto") ;
   if (strlen($empty)>0) {
     $infoDisplay->addParam("empty", $empty) ;
   } else {
     if (strlen($table) > 0) {
       $infoDisplay->addParam("table", $table) ;
       $infoDisplay->addParam("id".$table, ${"id".$table}) ;
     }
   }
    $goto = "displayform.php" ;
  }
  if (strlen($goto) > 0) {
    $infoDisplay->setPage($goto);
    $this->setDisplayNext($infoDisplay) ;
    $infoDisplay->save("infoDisplay", basename($goto)) ;
  } else {
    $infoDisplay->setPage("displayreport.php");
    $this->setDisplayNext($infoDisplay) ;
    $infoDisplay->save("infoDisplay", "displayreport.php") ;
  }
}
//echo $this->getUrlNext() ;
?>