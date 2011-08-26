<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /***
     *  Display a form to Add / Edit records in a table.
     *  Parameters :
     * - param string $primarykey in the form of primarykeyvarname=primarykeyvaluetoedit (required only if editing)
     * - param string $table name of the table to add/edit
     * - param string $goto name or URL of the next page to display.
     * - param string $addrecord to display an empty form : $addrecord=yes
	 * 
	 * @see ReportForm
     * @package PASSiteTemplate
     * @author Philippe Lewicki  <phil@sqlfusion.com>
     * @copyright  SQLFusion LLC 2001-2004
     * @version 3.0	 
     */

  include_once("config.php") ;
  if ($addrecord == "yes") {
    $pageTitle = "Add record in ".$table ;
  } else {
    $pageTitle = "Edit Record from table ".$table." where ".$primarykey ;
  }
  include("includes/header.inc.php") ;

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
  }
  $form = new ReportForm($conx) ;
  $form->setUrlNext(urldecode($goto)) ;
  $form->setEventControl($cfg_eventcontroler) ;
  if (!empty($registryname)) {
    $form->setRegistry($registryname);
  }

  if ($primarykey != "") {
    $form->squery = new sqlQuery($conx) ;
    $primarykey = stripslashes($primarykey) ;
    $form->squery->query("select * from `".$table."` where ".$primarykey) ;
    $form->setLogRun(true);
    $form->setDefault($table) ;
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
  include("includes/footer.inc.php") ;
?>