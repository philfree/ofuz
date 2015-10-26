<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
   
   /**   Event Mydb.FormatMultiSelectField
    *
    * Format the select input field from a multiselect_form function.
    * uses the ext tablename, primary key field name, value and foreign key
    * to update the content of the externale table.
    * we assume the mprimarykey to old only values greater than zero.
    *
    * <br>- param array multiselectfield contains the name of all the external table fields.
    * <br>- param array mprimarykey name of the external table where to insert the values
    * <br>- param array $$fieldname value of the primary key
    * <br>- param array mforeignkey name of the foreign key that link external table and the data table
	*
    * @package RadriaEvents
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2001-2007
    * @version 3.0	
    */


    $this->setLogRun(false);
    if (defined("RADRIA_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(RADRIA_LOG_RUN_MYDB_EVENTS);
    }
    $this->setLog("\n\n --- Start mydb.formatMultiSelectField at ".date("Y/m/d H:i:s"));
    $mprimarykey = $this->getParam("mprimarykey");
    $mforeignkey = $this->getParam("mforeignkey");

if ($doSave == "yes") {

    if (is_array($multiselectfield)) {
        $this->setLog("\n multiselect field Array found with ".count($multiselectfield)." records");
        foreach($multiselectfield as $ext_table_name) {
            $fieldname = $mprimarykey[$ext_table_name];
            if (strlen($this->getParam($fieldname)) > 0) {
                $fieldvalue = $this->getParam($fieldname);
            } else {
                $fieldvalue = $this->getParam("insertid") ;
                $this->setLog("\n insert id :".$fieldvalue);
            }

            $this->setLog("\n processing field: ".$ext_table_name);
            $q_del = new sqlQuery($this->getDbCon());
            $q_del->query("delete from ".$ext_table_name." where ".$fieldname."='".$fieldvalue."'");
            $q_del->free();

            $q_ins_new = new sqlQuery($this->getDbCon());
            if (is_array($multiselectvalues[$ext_table_name])) {
                $this->setLog("\n multiselectvalues field Array found with ".count($multiselectvalues[$ext_table_name])." records");
                foreach($multiselectvalues[$ext_table_name] as $value) {
                    $q_ins_new->query("insert into ".$ext_table_name." ($fieldname, ".$mforeignkey[$ext_table_name].") values ('".$fieldvalue."', '".$value."')");
                    $this->setLog("\n".$q_ins_new->getSqlQuery());
                }
            }
            $q_ins_new->free();
        }
    }

}
    $this->setLog("\n --- End mydb.formatMultiSelectField ");
    $this->setLogRun(false);
?>