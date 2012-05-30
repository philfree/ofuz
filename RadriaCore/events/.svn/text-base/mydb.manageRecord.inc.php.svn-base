<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt

  /**
   *  Event Manage records
   *
   * Generic record management.
   * To add, edit, delete records from a table.
   * Used by the EventRecord object to reroot the action to the
   * correct form.
   *
   * <br>- param String formpage name of the page containing the form
   * <br>- param String goto page to display after executing the form by default formrecordedit.php
   * <br>- param String table name of the table where the records will be added/edited or deleted
   * <br>- param String primarykey part of the sqlstatement required to query the updated or deleted record.
   * <br> global $globalevents, $garbagevents
   *
   * @note this EventAction is technicaly not needed, at the next refactor of the EventRecord it should be Deprecate
   *  
   * @package RadriaEvents
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007
   * @version 3.9   
   */
  global $strNoActionsFound, $strMissingArgument, $strConfirm, $strYes, $strNo;
  
  if (!isset($strNoActionsFound)) {
      $strNoActionsFound = "No Action found" ;
  }
  if (!isset($strMissingArgument)) {
      $strMissingArgument = "Error coudn't add the record, missing arguments" ;
  }
  if (!isset($strConfirm)) {
      $strConfirm = "Are you sure you want to delete the record" ;
  }
  if (!isset($strYes)) {
      $strYes = "Yes" ;
  }
  if (!isset($strNo)) {
      $strNo = "No" ;
  }

  //$table = $this->getParam("table");
  $primary_key_var = $this->getParam("primary_key_var");
  $primary_key_value = $this->getParam($primary_key_var);
  $submitbutton = $this->getParam("submitbutton");

if ($submitbutton != "Cancel") {
  global $PHP_SELF ;
  $this->setLogRun(false);
    if (defined("RADRIA_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(RADRIA_LOG_RUN_MYDB_EVENTS);
    }
  $this->setLog("\n\n Start mydb.manageRecord ".date("Y/m/d H:i:s"));
  $disp = new Display($formpage) ;
  $disp->addParam("mydb_num", $mydb_num) ;

  if (strlen($errorpage)>0) {
    $dispError = new Display($errorpage) ;
  } else {
    $dispError = new Display($this->getMessagePage()) ;
  }

  if ($eventaction == "Add") {
    if(strlen($goto)>0 && strlen($table)>0 && strlen($formpage) > 0) {
      if (ereg("\?", $goto)) { $sep = "&"; } else { $sep="?";}
      $urlnext = $goto ;
      $goto = urlencode($urlnext) ;
      $disp->addParam("table", $table) ;
      $disp->addParam("addrecord", "yes") ;
      $disp->addParam("goto", $goto) ;
	  //$disp->save("FormRecordEditData", $goto);
      $this->setUrlNext($disp->getUrl()) ;
    } else {
      $dispError->addParam("message", $strMissingArgument) ;
      $this->setDisplayNext($dispError) ;
    }
  } elseif($eventaction == "Edit") {
    if(strlen($goto)>0 && strlen($formpage)>0 && (strlen($primarykey)>0 || !empty($primary_key_var))) {
      $urlnext = $goto ;
      $goto = urlencode($urlnext) ;
      $disp->addParam("table", $table) ;
      $disp->addParam("primarykey", stripslashes($primarykey)) ;
      $disp->addParam("goto", $goto) ;
      $disp->addParam("primary_key_var", $primary_key_var);
      $disp->addParam($primary_key_var, $primary_key_value);
	  //$disp->save("FormRecordEditData", $goto);
      $this->setDisplayNext($disp) ;
    } else {
      $dispError->addParam("message", $strMissingArgument) ;
      $this->setDisplayNext($dispError) ;
    }
  } elseif($eventaction == "Delete") {
  $this->setLog("\n".date("Ymd")." - manageRecordEvent: delete from ".$table." where ".$primarykey." Confirm:".$deleteconfirm);
    if ($submityes == $strYes || $deleteconfirm == "no") {
      $qdelete = new sqlQuery($dbc) ;
      $primarykey = stripslashes($primarykey) ;
      $qdelete->query("delete from $table where $primarykey") ;
      if ($deleteconfirm != "no") {
        $goto = base64_decode($goto) ;
      }
      $disp->setPage($goto) ;
      $disp->addParam("mydb_num", $mydb_num) ;
      $this->setDisplayNext($disp) ;
    } elseif ($submitno == $strNo) {
      $goto = base64_decode($goto) ;
      $disp->setPage($goto) ;
      $disp->addParam("mydb_num", $mydb_num) ;
      $this->setDisplayNext($disp) ;
    } else {
      // built confirm message
      $goto = $goto ;
      $goto = base64_encode($goto) ;
      $message = $strConfirm."<br><form action=\"$PHP_SELF\" method=\"POST\">" ;
      $message .= "<input type=\"hidden\" name=\"mydb_events[]\" value=\"mydb.manageRecord\">" ;
      $message .= "<input type=\"hidden\" name=\"eventaction\" value=\"Delete\"> ";
      $message .= "<input type=\"hidden\" name=\"goto\" value=\"$goto\"> ";
      $message .= "<input type=\"hidden\" name=\"primarykey\" value=\"".stripslashes($primarykey)."\">" ;
      $message .= "<input type=\"hidden\" name=\"mydb_num\" value=\"".$mydb_num."\">" ;
      $message .= "<input type=\"hidden\" name=\"table\" value=\"$table\"> ";
      $message .= "<input type=\"submit\" name=\"submityes\" value=\"$strYes\"> " ;
      $message .= "<input type=\"submit\" name=\"submitno\" value=\"$strNo\"> " ;
      //$urlgo = $this->getMessagePage()."?message=".urlencode($message) ;
      $disp->addParam("message", $message) ;
      $disp->setPage($this->getMessagePage()) ;
      $this->setUrlNext($disp->getUrl()) ;
    }
  } else {
    $dispError->addParam("message", $strMissingArgument) ;
    $this->setDisplayNext($dispError) ;
  }
  //$disp->save("displaymydbManageRecord", $disp->getPage()) ;
  
}
?>