<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * Class NewRecordLog
   * Log a timestamp each time a element is updated. (mainly contacts).
   * 
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @package OfuzCore
   * @license ##License##
   * @version 0.6
   * @date 2010-09-03
   * @since 0.1
   */


class NewRecordLog extends DataObject {
    public $table = "created_date_log";
    public $primary_key = "idcreated_date_log";
    
    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(false);
    }    
    /**
     * eventListenNewRecord
     * This event check in the eventcontroler variables if it 
     * can detect an update in a database table. If yes then
     * it logs it by guessing the table name and primarykeyvalue.
     */
    function eventListenNewRecord(EventControler $event_controler) {
        $this->setLog("\n listenNewRecords:");
        foreach($event_controler->mydb_events as $eventaction) {
            $this->setLog("\n checking event action:".$eventaction);
            if (eregi(".*add$", $eventaction)) {
                list ($object_name, $method) = explode("->", $eventaction);
                if (is_object($_SESSION[$object_name])) {
                    $this->setCreateDate($_SESSION[$object_name]->getTable(),
                                         $event_controler->insertid);
        
                } else {
                    $do = new $object_name();
                    $this->setCreateDate($do->getTable(), $event_controler->insertid);
                }
            }
        }
    }
    
    /**
     * setLastUpdate()
     * set the last update date from a table specific record primary key value
     */
    
    function setCreateDate($tablename, $primary_key_value) {
            $this->table_name = $tablename;
            $this->id = $primary_key_value;
            $this->created_date = date("Y-m-d H:i:s");
            $this->add();
    }
    
    /**
     * getCreateDate
     * Return the create timestamp for a specific table, primary key value.
     * @param mix String with tablename value or DataObject 
     * @param String optional primary key value
     * @return mysql timestamp.
     */
    
    function getCreateDate($tablename, $primary_key_value=0) {
        if (is_object($tablename)) {
            $actual_tablename = $tablename->getTable();
            $primary_key_value = $tablename->getPrimaryKeyValue();   
            
        } else { $actual_tablename = $tablename; }
        
        
        $this->query("SELECT * FROM `".$this->getTable()."` WHERE 
                   `table_name`='".$this->quote($actual_tablename)."' AND 
                   `id`='".$this->quote($primary_key_value)."'");
        return $this->created_date;
    }
    
    /**
     * createDate
     * static version of getCreateDate
     * @see getCreateDate
     */
     
     function createDate($tablename, $primary_key_value=0) {
        $q = new sqlQuery($GLOBALS['conx']);
        if (is_object($tablename)) {
            $actual_tablename = $tablename->getTable();
            $primary_key_value = $tablename->getPrimaryKeyValue();   
            
        } else { $actual_tablename = $tablename; }
        
        $q->query("SELECT created_date FROM `".$this->getTable()."` WHERE 
                   `table_name`='".$q->quote($actual_tablename)."' AND 
                   `id`='".$q->quote($primary_key_value)."'");
        if ($q->getNumRows() > 0){
            return $q->getData("created_date");
        } else {
            return false;
        }
        $q->free();
     }
}


?>
