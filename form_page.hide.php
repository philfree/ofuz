<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * Default Template web pages 
   */
  $pageTitle = "Form Page";
  $Author = "PAS Pagebuilder";
  $Keywords = "PAS Pagebuilder SQLFusion Web authoring tool";
  $Description = "The best way to built rich web sites";
  $background_color = "white";
  $background_image = "none";
  include_once("config.php");
  include_once("includes/header.inc.php");
?>
<DIV id="DRAG_script_Script" style="top:107px;left:150px;height:387px;width:533px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;filename:includes/blank_script.script.inc.php;" --><?php
  echo "<div class=\"error_message\">".htmlentities(stripslashes($_GET['message']))."</div>";

  $registryname = "";
  $primary_key_value = "";
  $primary_key_var = "";
  $goto = $_GET['goto'];
  $addrecord = $_GET['addrecord'];
  $manageData = $_SESSION['manageData'];
  if (is_object($manageData)) {  
    if (strlen($manageData->getParam("goto")>0)) {
      $goto = $manageData->getParam("goto");
    }
    if (strlen($manageData->getParam("primarykey")) > 2) {
      $primarykey = $manageData->getParam("primarykey");
    }
    $registryname = $manageData->getParam("registryname");
    $savedquery = $manageData->getParam("savedquery");
    if (strlen($manageData->getParam("table"))>0) {
      $table = $manageData->getParam("table");
    }
    $primary_key_var = $manageData->getParam("primarykeyvar");
    if (!empty($primarykeyvar)) {
        $primary_key_value = $manageData->getParam("primary_key_var");
    } else {
        $primary_key_value = $manageData->getParam("id".$table);
    }
  

 if (!empty($table) || !empty($savedquery)) {

  $form = new ReportForm($conx) ;
  $form->setUrlNext(urldecode($goto)) ;
  $form->setEventControl($cfg_eventcontroler) ;
  if (!empty($registryname)) {
    $form->setRegistry($registryname);
  }

  if ($primarykey != "") {
    $form->squery = new sqlQuery($conx) ;
    $primarykey = stripslashes($primarykey) ;
    $form->squery->query("select * from ".$table." where ".$primarykey) ;
    $form->setLogRun(true);
    $form->setDefault($table) ;
  } elseif(!empty($savedquery)) {
     $form->setSavedQuery($savedquery);
     $form->setQuery();
     $form->setDefault();
  } else {
    $form->setQuery($table, $primary_key_value, $primary_key_var);
    $form->setDefault($table) ;
  }

  if ($addrecord == "yes") {
    $form->setAddRecord() ;
  }

  $form->event->addEvent("mydb.addParamToDisplayNext",500);
  $form->event->addParam("errorpage", $_SERVER['PHP_SELF']);

  $form->setForm() ;
  $form->execute() ;

 } else { ?>
<div class="error_message">Missing parameters to display the from</div>
<?php
  }
 } else { ?>
<div class="error_message">Missing parameters to display the from (manageData)</div>
<?php
 }
?>
</DIV>



  </body>
</html>