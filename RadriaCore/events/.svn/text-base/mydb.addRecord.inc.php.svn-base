<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt

/**   Event Mydb.addRecord
  *
  * Record the data from a Form.
  * All the variables recieved must be in the following format
  * <br>$fields[fieldname] = Value
  * <br>$doSave is a Inter event parameter. Any event executed before mydb.addRecord
  * can set $doSave to no to stop the data from being saved in the database.
  * Every event executed after this one can check if the record has been inserted by looking at the
  * inter event param: recordinserted (yes/no).
  * <br>- param array fields All the fields and there values
  * <br>- param string table name of the table where to insert
  * <br>- param string setmessage allow to customize the message var sent to the urlnext page.
  * Optional :
  * <br>- param string errorpage page where to display the error message
  *
  * @note this is slowly moving to the DataObject class, but it will stay until we drop PHP4 support
  *
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2007
  * @version 3.9  
  */
/*
$strInsertError = "Erreur lors de l'insertion de l'enregistrement ";
$strInsertOk = "L'enregistrement a ete inserer " ;
 */
global $strInsertError, $strInsertOk, $strAddCancel, $strCancel;
if (!isset($strInsertError)) {
    $strInsertError = "Error while inserting the record ";
}
if (!isset($strInsertOk)) {
    $strInsertOk = "The record has been inserted " ;
}
if (!isset($strAddCancel)) {
$strAddCancel = "The insertion of the record as been canceled"; 
}
if (!isset($strCancel)) {
$strCancel = "Cancel";
}

if (strlen($errorpage)>0) {
    $urlerror = $errorpage;
} else {
    $urlerror = $this->getMessagePage() ;
}
 $this->setLogRun(false);
    if (defined("RADRIA_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(RADRIA_LOG_RUN_MYDB_EVENTS);
    }
 $this->setLog("\n mydb_add record start".date("Y-m-d H:i:s"));

$disp = new Display($goto) ;
if ($submitbutton != $strCancel)  {
        if ($doSave == "yes") {    
        $table = $this->getParam("table");
        $fieldlist = '';
        $valuelist = '';
        $qGetFields = new sqlQuery($this->dbc) ;
        $qGetFields->setTable($table) ;
        $tableFields = $qGetFields->getTableField() ;

        $reg = new Registry($dbc);
        $reg->registryFromTable($table);

        if ($GLOBALS['cfg_local_db'] == "mysql") {

            while (list($key, $fieldname) = each($tableFields)) {
                if (strlen($fields[$fieldname])>0) {
                    if (get_magic_quotes_gpc()) {
                        $fields[$fieldname] = stripslashes($fields[$fieldname]);
                    }
                    $fieldname = str_replace("`", "", $fieldname);
                    $fieldlist .= "`$fieldname`, ";
                    if ($fields[$fieldname] == "null") { 
                        $val = $fields[$fieldname]; 
                // } elseif (is_numeric($fields[$fieldname])) {
                //     $val = $fields[$fieldname]; 
                    } else {
                        if (function_exists("mysql_real_escape_string")) {
                            $val = "'".mysql_real_escape_string($fields[$fieldname])."'";
                        } else {
                            $val = "'".addslashes($fields[$fieldname])."'";
                        }
                    }
                    $valuelist .= "$val, ";
                }
            }
            $table = str_replace("`", "", $table);
            $fieldlist = ereg_replace(', $', '', $fieldlist);
            $valuelist = ereg_replace(', $', '', $valuelist);
            $query = "INSERT INTO `$table` ($fieldlist) VALUES ($valuelist)";

        } elseif ($GLOBALS['cfg_local_db'] == "pgsql") {

            while (list($key, $fieldname) = each($tableFields)) {
                    if (strlen($fields[$fieldname])>0) {
                        $this->setLog("\n For $key / $fieldname / $var ");
                        $no_database_type = true;
                        if (is_object($reg->fields[$fieldname])) {
                            if (strlen($reg->fields[$fieldname]->getRdata("databasetype"))>0) {

                                $this->setLog(" type:".$reg->fields[$fieldname]->getRdata("databasetype"));
                                if ($reg->fields[$fieldname]->getRdata("databasetype") == "varchar"
                                || $reg->fields[$fieldname]->getRdata("databasetype") == "text"
                                ) { 
                                    $fieldlist .= "\"$fieldname\", ";
                                    $valuelist .= "'".$fields[$fieldname]."', ";
                                } elseif($reg->fields[$fieldname]->getRdata("databasetype") == "time"
                                      || $reg->fields[$fieldname]->getRdata("databasetype") == "date") {
                                    if (!empty($fields[$fieldname])) {
                                        $fieldlist .= "\"$fieldname\", ";
                                        $valuelist .= "'".$fields[$fieldname]."', ";
                                    }
                                } else {
                                    if (!empty($fields[$fieldname])) {
                                        $fieldlist .= "\"$fieldname\", ";
                                        $valuelist .= $fields[$fieldname].", ";
                                    }
                                }

                                $this->setLog(" add:\"$fieldname\" = ".$fields[$fieldname].", ");
                                $no_database_type = false;
                            }
                        } 
                        if ($no_databasetype) {

                            $fieldlist .= "\"$fieldname\", ";
                            if ($fields[$fieldname] == "null") { 
                                $val = $fields[$fieldname]; 
                        // } elseif (is_numeric($fields[$fieldname])) {
                        //     $val = $fields[$fieldname]; 
                            } else {
                                $val = "'$fields[$fieldname]'";
                            }
                            $valuelist .= "$val, ";
                            $this->setLog(" add:\"$fieldname\" = $val, ");
                        }
                    }
            }
            $fieldlist = ereg_replace(', $', '', $fieldlist);
            $valuelist = ereg_replace(', $', '', $valuelist);
            $query = "INSERT INTO `$table` ($fieldlist) VALUES ($valuelist)";

        } else {

            while (list($key, $fieldname) = each($tableFields)) {
                    if (strlen($fields[$fieldname])>0) {
                            $fieldlist .= "`$fieldname`, ";
                            if ($fields[$fieldname] == "null") { 
                                $val = $fields[$fieldname]; 
                        // } elseif (is_numeric($fields[$fieldname])) {
                        //     $val = $fields[$fieldname]; 
                            } else {
                                $val = "'$fields[$fieldname]'";
                            }
                            $valuelist .= "$val, ";
                    }
            }
            $fieldlist = ereg_replace(', $', '', $fieldlist);
            $valuelist = ereg_replace(', $', '', $valuelist);
            $query = "INSERT INTO `$table` ($fieldlist) VALUES ($valuelist)";

        }
        $this->setLog("\n Running query:\n".$query);
        $message = urlencode($strInsertOk) ;
        $sql_query = $query;
        $qSaveData = new sqlQuery($this->dbc) ;
        $result = $qSaveData->query($query) ;
        $uniqid = $qSaveData->getInsertId($table, "id".$table) ;
        $this->addParam("insertid", $uniqid);
        if (!$result) {
                $error = $qSaveData->getError();
                $this->addParam("recordinserted", "no");
                $disp->setPage($urlerror) ;
                $disp->addParam("message",$strInsertError.$error) ;
        } else {
                $disp->setPage($goto) ;
                if (strlen($setmessage) > 0) {
                    $strInsertOk = $setmessage;
                }
                $this->addParam("recordinserted", "yes");
                $disp->addParam("message",$strInsertOk) ;
                $disp->addParam("insertid", $uniqid) ;
                $disp->addParam("updage", "no") ;
        }
        $this->setDisplayNext($disp) ;
        }
        if(!empty($_SERVER['PHP_SELF'])) {
            $disp->save("displayAddRecord", $_SERVER['PHP_SELF']) ;
        }
} else {
        $disp->setPage(urldecode($goto)) ;
        $disp->addParam("message", $strAddCancel) ;
        $disp->addParam("update", "no") ;
        $this->setDisplayNext($disp) ;
        
}
?>