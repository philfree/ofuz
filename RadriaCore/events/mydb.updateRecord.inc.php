<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt

 /**   
  *  Event Mydb.udateRecord
  *
  * Record the data from a Form.
  * All the variables recieved must be in the following format
  * $fields[fieldname] = Value
  * $doSave is a Inter event parameter. Any event executed before mydb.updateRecord
  * can set $doSave to no to stop the data from being saved in the database.
  * <br>- param array fields All the fields and there values
  * <br>- param string $primarykey contains the sql where statement to select the field to update.
  * <br>- param string table name of the table where to update
  * <br>- param string setmessage allow to customize the message var sent to the urlnext page.
  *
  * @note this is slowly moving to the DataObject class, but it will stay until we drop PHP4 support
  *
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.9
  */
/*
$strInsertError = "Erreur lors de la mise a jour de l'enregistrement ";
$strUpdateOk = "L'enregistrement a ete mit a jour" ;
 */
 
 global $strInsertError, $strUpdateOk, $strCancel, $strUpdateCancel;
 if (!isset($strInsertError)) {
     $strInsertError = "An error occured while updating the record";
 }
 if (!isset($strUpdateOk)) { 
     $strUpdateOk = "The record has been updated" ;
 }
 if (!isset($strCancel)) {
     $strCancel = "Cancel";
 }
 if (!isset($strUpdateCancel)) {
     $strUpdateCancel = "The Update of the record as been canceled"; 
 }
 
 $primarykey = $this->getParam("primarykey");
 $primarykeyvar = $this->getParam("primarykeyvar");
 $primary_key_var = $this->getParam("primary_key_var");
 if (!empty($primarykeyvar)) { $primary_key_var = $primarykeyvar; }
 $primary_key_value = $this->getParam($primary_key_var);
 $table = $this->getParam("table");
 $fields = $this->getParam("fields");

 $this->setLogRun(false);
    if (defined("PAS_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(PAS_LOG_RUN_MYDB_EVENTS);
    }
 $this->setLog("\n mydb_Update record start".date("Y-m-d H:i:s"));

if (!function_exists("mysql_real_escape_string")) {
    function mysql_real_escape_string($string_value) {
        return addslashes($string_value);
    }
}

 $disp = new Display($goto) ;
if ($submitbutton != $strCancel) {
        reset($fields) ;

        //echo $doSave ;
        if ($doSave == "yes") {
        $primarykey = stripslashes($primarykey) ;
        $urlerror = $this->getMessagePage() ;
        $valuelist = '';

        $reg = new Registry($dbc);
        $reg->registryFromTable($table);
        if ($GLOBALS['cfg_local_db'] == "mysql") {
            while (list($key, $val) = each($fields)) {
                    if (get_magic_quotes_gpc()) {
                        $val = stripslashes($val);
                    }
                    $this->setLog("\n For $key / $val ");
                    if (strlen($reg->fields[$key]->getRdata("databasetype"))>0) {
                        $this->setLog(" type:".$reg->fields[$key]->getRdata("databasetype"));
                        if ($reg->fields[$key]->getRdata("databasetype") == "varchar"
                         || $reg->fields[$key]->getRdata("databasetype") == "text"
                        ) { $val = "'".mysql_real_escape_string($val)."'";}
                        if (!empty($val) 
                              && ($reg->fields[$key]->getRdata("databasetype") == "time"
                                  || $reg->fields[$key]->getRdata("databasetype") == "date")
                            ) {
                            $val = "'".mysql_real_escape_string($val)."'";
                        }
                        if (!empty($val) 
                              && ($reg->fields[$key]->getRdata("databasetype") == "integer")
                            ) {
                            $val = (int)$val;
                        }
                        if (!empty($val) 
                              && ($reg->fields[$key]->getRdata("databasetype") == "float")
                            ) {
                            $val = (float)$val;
                        }
                        if (!empty($val)) {
                           $valuelist .= "`$key` = $val, ";
                        }
                        $this->setLog(" add:`$key` = $val, ");
                    } else {
                        if($val != "null") $val = "'".mysql_real_escape_string($val)."'";
                        $valuelist .= "`$key` = $val, ";
                        $this->setLog(" add:`$key` = $val, ");
                    }
            }
            $valuelist = ereg_replace(', $', '', $valuelist);
            
            if (!empty($primary_key_var)) {
                if ($reg->fields[$primary_key_var]->getRdata("databasetype") == "integer") {
                   if (!empty($primary_key_value)) { 
                       $query = "UPDATE `$table` SET $valuelist WHERE $primary_key_var = $primary_key_value";
                   } else {
                       $query = "UPDATE `$table` SET $valuelist WHERE $primary_key_var = 0";
                   }
                }
            } else { 
                $query = "UPDATE `$table` SET $valuelist WHERE $primarykey";
            }

        } elseif ($GLOBALS['cfg_local_db'] == "pgsql") {
            while (list($key, $val) = each($fields)) {
                $this->setLog("\n For $key / $val ");
                if (in_array($key, $reg->fields)) {
                    if (strlen($reg->fields[$key]->getRdata("databasetype"))>0) {
                        $this->setLog(" type:".$reg->fields[$key]->getRdata("databasetype"));
                        if ($reg->fields[$key]->getRdata("databasetype") == "varchar"
                        || $reg->fields[$key]->getRdata("databasetype") == "text"
                        ) { $val = "'$val'";}
                        if (!empty($val) 
                            && ($reg->fields[$key]->getRdata("databasetype") == "time"
                                || $reg->fields[$key]->getRdata("databasetype") == "date")
                            ) {
                            $val = "'$val'";
                        }
                        if (!empty($val)) {
                        $valuelist .= "\"$key\" = $val, ";
                        }
                        $this->setLog(" add:\"$key\" = $val, ");
                    } else {
                        if($val != "null") $val = "'$val'";
                        $valuelist .= "\"$key\" = $val, ";
                        $this->setLog(" add:\"$key\" = $val, ");
                    }
                } else {
                    $setMessage = "Some fields doesn't have a registry entry and may break the update";
                    if($val != "null") $val = "'$val'";
                    $valuelist .= "\"$key\" = $val, ";
                    $this->setLog(" add:\"$key\" = $val, ");
                }
            }
            $valuelist = ereg_replace(', $', '', $valuelist);
            
            if (!empty($primary_key_var)) {
                if ($reg->fields[$primary_key_var]->getRdata("databasetype") == "integer") {
                   if (!empty($primary_key_value)) { 
                       $query = "UPDATE \"$table\" SET $valuelist WHERE $primary_key_var = $primary_key_value";
                   } else {
                       $query = "UPDATE \"$table\" SET $valuelist WHERE $primary_key_var = 0";
                   }
                }
            } else { 
                $query = "UPDATE \"$table\" SET $valuelist WHERE $primarykey";
            }

        } else {
            while (list($key, $val) = each($fields)) {
                $this->setLog("\n For $key / $var ");
                if($val != "null") $val = "'$val'";
                $valuelist .= "`$key` = $val, ";
                $this->setLog(" add:`$key` = $val, ");
            }
            $valuelist = ereg_replace(', $', '', $valuelist);
            $query = "UPDATE `$table` SET $valuelist WHERE $primarykey";
        }
        $this->setLog("\n Running query:\n".$query);
        $sql_query = $query;
        $qSaveData = new sqlQuery($dbc) ;
        $result = $qSaveData->query($query) ;
        //  $uniqid = $qSaveData->getInsertId() ; PL 20030516 not used
        if (!$result) {
                $error = $qSaveData->getError();
                $this->addParam("recordupdated", "no");
                $disp->setPage($urlerror) ;
                $disp->addParam("message", $strInsertError.$error) ;
                $this->setDisplayNext($disp);
        } else {
                $disp->setPage(urldecode($goto)) ;
                if (strlen($setmessage) > 0) {
                $strUpdateOk = $setmessage;
                }
                $this->addParam("recordupdated", "yes");
                $disp->addParam("message", $strUpdateOk) ;
        //    $disp->addParam("updateid", $uniqid) ; ; PL 20030516 not used could be replace with value of primary key
                $disp->addParam("update", "yes") ;
                $this->setDisplayNext($disp) ;
        }
        if(!empty($_SERVER['PHP_SELF'])) {
            $disp->save("displayUpdateRecord",  $_SERVER['PHP_SELF']) ;
        }
        }
} else {
        $disp->setPage(urldecode($goto)) ;
        $disp->addParam("message", $strUpdateCancel) ;
        $disp->addParam("update", "no") ;
        $this->setDisplayNext($disp) ;
}
?>