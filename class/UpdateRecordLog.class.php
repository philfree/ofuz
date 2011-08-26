<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  
  /**
   * Class UpdateRecordLog
   * Log a timestamp each time a element is updated. (mainly contacts).
   * 
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @package OfuzCore
   * @license GNU Affero General Public License
   * @version 0.7
   * @date 2011-05-13
   * @since 0.1
   */


class UpdateRecordLog extends DataObject {
    public $table = "updated_date_log";
    public $primary_key = "idupdated_date_log";
    
    
    function getId($tablename, $primary_key_value) {
        $this->query("SELECT * FROM `".$this->getTable()."` WHERE 
                         `tablename`='".$this->quote($tablename)."' AND 
                         `primarykeyvalue`='".$this->quote($primary_key_value)."'");
        return $this->getPrimaryKeyValue();
    }
    /**
     * eventListenUpdateRecord
     * This event check in the eventcontroler variables if it 
     * can detect an update in a database table. If yes then
     * it logs it by guessing the table name and primarykeyvalue.
     */
    function eventListenUpdateRecord(EventControler $event_controler) {
        foreach($event_controler->mydb_events as $eventaction) {
            if (eregi(".*update$", $eventaction)) {
                list ($object_name, $method) = explode("->", $eventaction);
                if (is_object($_SESSION[$object_name])) {
                    $this->setLastUpdate($_SESSION[$object_name]->getTable(),
                                         $_SESSION[$object_name]->getPrimaryKeyValue());
        
                }
            }
        }
    }
    
    /**
     * setLastUpdate()
     * set the last update date from a table specific record primary key value
     */
    
    function setLastUpdate($tablename, $primary_key_value) {
        $this->getId($tablename, $primary_key_value);
        if ($this->getId($tablename, $primary_key_value) > 0) {
            $this->updatedate = date("Y-m-d H:i:s");
            $this->update();
        } else {
            $this->tablename = $tablename;
            $this->primarykeyvalue = $primary_key_value;
            $this->add();
        }
        
    }
    
    /**
     * Return the last update date from an object.
     * @param mix dataobject or tablename
     * @param optional primarykey value.
     * @see lastUpdate
     */
    
    function getLastUpdate($tablename, $primary_key_value=0) {
        if (is_object($tablename)) {
            $this->getId($tablename->getTable(), $tablename->getPrimaryKeyValue());
        } else  {
            $this->getId($tablename, $primary_key_value);
        }
        return $this->updatedate;
    }
       
    /**
     * lastUpdate
     * @see getLastUpdate
     */
     
     function lastUpdate($tablename, $primary_key_value=0) {
        $q = new sqlQuery($GLOBALS['conx']);
         
        if (is_object($tablename)) {
            $actual_tablename = $tablename->getTable();
            $primary_key_value = $tablename->getPrimaryKeyValue();   
            
        } else { $actual_tablename = $tablename; }
         
         $q->query("SELECT updatedate FROM `".$this->getTable()."` WHERE 
                         `tablename`='".$this->quote($actual_tablename)."' AND 
                         `primarykeyvalue`='".$this->quote($primary_key_value)."'");
         if ($q->getNumRows() > 0) {
             $q->fetch();
             return $q->getData("updatedate");
         } else { return false; } 
         $q->free();
     }
    
}


?>
