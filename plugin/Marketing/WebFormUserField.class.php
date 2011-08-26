<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * WebFormUserField class
     * Using the DataObject
     */
   
class WebFormUserField extends DataObject {
    
    public $table = "webformuserfield";
    protected $primary_key = "idwebformuserfield";

    function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_WEBFORM);
		}
    }    

    function getFieldsByWebFormUser($id){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from ".$this->table. " where idwebformuser = ".$id);
      $data = array();
      $field_arr = array();
      while($q->fetch()){
           $field_arr["name"] = $q->getData("name");
           $field_arr["size"] = $q->getData("size");
           $field_arr["label"] = $q->getData("label");
           $data[] = $field_arr;
      }
      return $data;
   }
}	
?>
