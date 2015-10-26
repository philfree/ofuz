<?php
namespace RadriaCore\Radria;
// Copyright 2001 - 2012 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// Licensed under the LGPL V3 
// For licensing, reuse, modification and distribution see license.txt
  /**
   * Data Object
   * @see DataObject
   * @package RadriaCore
   */
   /**
    *  Data Object for Radria
    *
    *  Extends the sqlQuery to allow a more object oriented approch to the
    *  database data.
    *  This make extensive use of the magic method: __call() 
    *  To load data, generate view, forms and run sql query on the fly.
    *  In general this class is extended and not used directly.
    *  For example:
    *  class Products extends DataObject {
    *     public $table = "product";
    *     protected $primary_key = "idproduct";
    *    
    *  }
    *
    * @author Philippe Lewicki  phil at sqlfusion.com
    * @version 5.0
    * @package RadriaCore2
    * @access abstract
    */

use RadriaCore\Radria\mysql\SqlQuery;
use RadriaCore\Radria\mysql\SqlConnect;

Class DataObject extends SqlQuery {
    
    public $dbCon;
    protected $squery;
    public $table = '';
    protected $primary_key = '';
    public $form;
    public $view;
    protected $values = Array();
    public $reg;
    public $fields;
    private $report_name;
    private $report_template = "";
    private $reportform_template = "";
    private $applyreg = false;
    private $reg_context = "Disp";
    private $form_ready = false;
    private $view_ready = false;
    private $default_order = '';
    public $auto_increment_key = true;
     
    /**
     * Default constructor
     * To be called from child class first with parent::__construct($conx)
     * with a valid database connection object.
     *
     * @param sqlConnect Active Database connection.
     * @return true on success.
     */
    
    function __construct(SqlConnect $conx=NULL, $table_name="") {
        if (is_null($conx)) { $conx = $GLOBALS['conx']; }
        parent::__construct($conx);
        if (defined("RADRIA_LOG_RUN_DATAOBJECT")) {
            $this->setLogRun(RADRIA_LOG_RUN_DATAOBJECT);
        }
        //$this->dbCon = $conx;
        if (!is_resource($this->dbCon->id)) {
            $this->setError("DataObject Error: No open or valid connexion has been provide to execute the query. ") ;
            return false;
        }
        if (!empty($table_name)) {
            $this->setTable($table_name);
        }

        return true;
        
    }

    /**
     * query()  Execute an SQLQuery
     * 
     * Overwrite the query method to add an initial fetch() if the query 
     * returns only one row.
     * Used the setCursor to reset its position to the first record in case
     * a next() method would be executed afterward.
     *
     * @access public
     * @return database result set ressource
     */
    public function query($sql = "", $dbCon =0) {
        $this->setLog($sql);
        parent::query($sql, $dbCon);
        // This sounds good but could be counter intuitive
        // Improved it by reseting the cursor to zero
        // That would make the first call to next() fetch the same data.
        //if ($this->getNumRows() == 1) {
        if ($this->getNumRows() > 0) {
             $result = $this->next();
             $this->setCursor(0);
             return $result;
        }
        
    }

    /**
     * next() fetch the next record of that object result set
     * 
     * Use the result set to assign all the fields and values
     * To the object.
     * if the result set reach the ends it return false.
     *
     * @access public
     * @return Array with fields as key containing their values
     */

    public function next() {
        //try {
	if (is_resource($this->getResultSet())) {
          return $this->values = $this->fetchArray();
	} else { return false ; } 
//	catch (RadriaException $e) {
//	   $this->setLog("\n DataObject Exception in next() : ".$e->getMessage());
//	} catch (Exception $e) {
//	   $this->setError("\n DataObject Exception in next()");
//	}

    }

    /**
     * previous() fetch the previous that object result set
     * 
     * Like next() but move backward in the result set.
     * 
     * @access public
     * @return Array with fields as key containing their values
     * @see next()
     */

    public function previous() {
        $this->setCursor($this->getCursor()-1);
       return $this->values = $this->fetchArray();
    }

    /**
     * first() return the first row of the result set
     * 
     * The position of the cursor is now the first row
     * 
     * @access public
     * @return Array with fields as key containing their values
     * @see next()
     */
    public function first() {
        if (is_resource($this->getResultSet())) {
            $this->setCursor(0);
            $this->values = $this->fetchArray();
            $this->setCursor(0);
            return  $this->values;
        } else {
            return Array();
        }
    }

    /**
     * last() return the last row of the result set
     * 
     * The position of the cursor is now the last row
     * 
     * @access public
     * @return Array with fields as key containing their values
     * @see next()
     */
    public function last() {
        $this->setCursor($this->getNumRows()-1);
        return $this->values = $this->fetchArray();
    }

    /**
     * __set magic method to set the value of a field.
     * 
     *  This magic method is used here to assign a value to the fields array.
     *  making the value available to the object for further manipulation.
     *  Example: $do_movie = new Movie; $do_movie->title = "Lord of the Ring";
     *
     *  @param field name of fields from the table structure
     *  @param value value of the field.
     *
     */

    public function __set($field, $value) {
        $this->values[$field] = $value;
    }

    /**
     * __get magic method to return the value of a field
     *
     *  By default this method will enable the display of the value from a field of 
     *  the currently fetch database record.
     *  $do_movie->title will return $do_movie->values['title'].
     *  If the ApplyRegistry is set to True then the currently loaded registry will
     *  be apply to that field.
     *  An example can be found at:
     *  http://radria.sqlfusion.com/documentation/core:by_example_php5#custom_forms
     *
     * @param field name of the field
     * @return value of the field
     */
    public function __get($field) {
        $value = "";
        if (isset($this->values[$field]) || $this->hasData()) {
            if (strlen($this->values[$field]) > 0) {
                $value = $this->values[$field] ;
            } elseif(is_resource($this->getResultSet())) {
                $value = $this->getData($field);
            }
            if ($this->applyreg) {
                if (is_object($this->fields)) {
                    $value = $this->fields->apply($this->reg_context, $field, $this->values[$field]);
                } else {
                    throw new RadriaException("Can't apply reg on field:".$field.". No registry available");
                }
            }
          
        } elseif ($this->applyreg) {
			    if (is_object($this->fields)) {
                    $value = $this->fields->apply($this->reg_context, $field, "");
                } else {
                    throw new RadriaException("The field doesn't exists or doesn't has values. Can't apply reg on field:".$field.". No registry available");
                }
		} else {	
            throw new RadriaException("Field:".$field." not available or doesn't exists");
        }
		return $value;
    }

    public function __isset($field) {
       return isset($this->values[$field]);
    }
    
    public function __unset($field){
       unset($this->values[$field]);
    }
    
    /**
     * __call magic method to generate DataObject or load data in the object.
     *
     *  This is where the magic happened.
     *  
     *  Those magic methods display saved view, form or saved query as well a manage relations.
     *  ->get_name_of_saved_query will load and run an SQLfusion from the savedquery/ folder.
     *  ->view_name_of_a_serialized_report will load and display a view from the report/ folder.
     *  ->form_name_of_a_serialized_form will load and display a form from the form/ folder.
     *  Relation:
     *  ->GetChildNameOfChildDataObject ($BlogEntries->GetChildComments()) will return an instance of
     *  the child object with a dataset containing only the child entries of the current Object value.
     *  An example is at: http://radria.sqlfusion.com/documentation/core:by_example_php5#relations
     *  ->GetParent... works the same why but look on the Parent relation and will return one record. 
     *
     * @param method name of the method called
     * @param params array of parameters used on the method call.
     */ 
    public function __call($method, $params) {                    
        try {
            $this->setLog("\n DataObject __call for method:".$method);
            if (method_exists($this,$method)) { return; }
            $method_found = false;
            
            // lets use the method as an ordering.
            if (ereg("^getChild(.*)$", $method, $match)) {
                $this->setLog("\n DataObject __call for child:".$match[1]); 
                $tablename = strtolower($match[1]);
                $order_limit = $params[0];
                $class_name = $match[1];
                    $this->setLog("\n DataObject:".$tablename." Doesn exist checking if the table exists");
                    $q_tables = new sqlQuery($this->getDbCon()) ;
                    $q_tables->getTables();
                    $table_found = false;
                    if (class_exists($class_name)) {
                        $this->setLog("\n DataObject:".$class_name." exist instanciating it with child values");
                        $new_data_object = new $class_name($this->getDbCon());
                        if (strlen($new_data_object->getTable()) > 0) {
                            $tablename = $new_data_object->getTable();
                        }
                        $new_data_object->{$this->getPrimaryKey()} = $this->getPrimaryKeyValue();
                        $new_data_object->query("select * from ".$tablename." where ".$this->getPrimaryKey()."=".$this->getPrimaryKeyValue()." ".$order_limit);
                        $method_found = true;
                        return $new_data_object;  
                    } else {
                        $this->setLog("\n DataObject:".$class_name." Doesn't exist checking if the table ".$tablename." exists");
                        while($tables = $q_tables->fetchArray()) {
                            if ($tablename == $tables[0]) 
                                $table_found = true; 
                        }
                        if ($table_found) {
                            $this->setLog("\nDataObject Table Found creating new data object:".$tablename);
                            
                            $new_data_object = new DataObject($this->getDbCon());
                            $new_data_object->setTable($tablename);
                            $new_data_object->setPrimaryKey("id".$tablename);
                            $new_data_object->{$this->getPrimaryKey()} = $this->getData($this->getPrimaryKey());
                            $new_data_object->query("select * from ".$tablename." where ".$this->getPrimaryKey()."=".$this->getData($this->getPrimaryKey()));
                            $method_found = true;
                            //$this->{$tablename} = $new_data_object;
                            return $new_data_object; ;                      
                        } else {
                            throw new RadriaException("Table:".$tablename." Not Found Could not create an Object");
                        }
                    }
                    
            }
            if (ereg("^getParent(.*)$", $method, $match)) {
                $this->setLog("\n DataObject __call for parent:".$match[1]); 
                $tablename = strtolower($match[1]);

                if (class_exists($tablename) && is_subclass_of($tablename, "DataObject")) {
                        $this->setLog("\n DataObject class for".$tablename." exists will instanciate it");
                        $do = new $tablename($this->getDbCon());
                        //$do->query("select * from ".$tablename." where ".$do->getPrimaryKey()."=".$this->getData($do->getPrimaryKey()));
                        $do->getId($this->{$do->getPrimaryKey()});   
						$method_found = true;						                  
						return $do;
						
                } else { 
                    $this->setLog("\n DataObject class for ".$tablename." Doesn exist checking if the table exists");
                    $q_tables = new sqlQuery($this->getDbCon()) ;
                    $q_tables->getTables();
                    $table_found = false;
                    while($tables = $q_tables->fetchArray()) {
                        if ($tablename == $tables[0]) 
                            $table_found = true; 
                    }
                    if ($table_found) {
                        $this->setLog("\n Creating new data object:".$tablename);
                         
                        $new_data_object = new DataObject($this->getDbCon());
                        $new_data_object->setTable($tablename);
                        $new_data_object->setPrimaryKey("id".$tablename);
                        $new_data_object->query("select * from ".$tablename." where ".$new_data_object->getPrimaryKey()."=".$this->getData($new_data_object->getPrimaryKey()));
                        $method_found = true;
                        //$this->{$tablename} = $new_data_object;
                        return $new_data_object; ;                      
                    } else {
                        throw new RadriaException("Table:".$tablename." Not Found");
                    }
                    
                }
            }

            if (!$method_found) {
                //
                throw new RadriaException("Method:".$method." Not Found");
                die("Method:".$method." Not Found");
            } 
            return false;
        } catch (RadriaException $e) {
            $this->setError("\n DataObject Exception: ".$e->getMessage());
            throw new RadriaException($e->getMessage()." when calling method:".$method);
        } catch (Exception $e) {
            trigger_error("Method".$method." Not found", E_USER_ERROR);
        }
    }

    /**
     * __wakeup()
     * This magic method will 
     * reconnect the object to the database once its 
     * unserialized from the session.
     *
     */
    public function __wakeup() {
        $this->dbCon->start();
    }

    /**
     * getAll();
     *
     * By default load a result set with all data from the table.
     * an $orderby and $where optional parameter are available
     * they allow to add a WHERE and ORDER BY to the query.
     * An Array can also be passed with 2 keys: ordreby and where 
     *
     * @param mix order by part of an sql statement
     * @param string where part on an sql statement
     * @return result set with the result of the query.
     */



    function getAll($orderby="", $where="") {
        if (is_array($orderby)) {
            $a_orderby = $orderby;
            $where = $a_orderby['where'];
            $orderby = $a_orderby['orderby'];
        }
        if (!empty($this->default_order)) {
           $orderby = $this->default_order;
        }
        if (empty($orderby)) { 
            $sql_orderby = "";
        } else {
            $sql_orderby = " ORDER BY ".$orderby;
        }
        if (empty($where)) {
            $sql_where = "";
        } else {
            $sql_where = " where ".$where;
        }
       return $this->query("SELECT * FROM ".$this->getTable().$sql_where.$sql_orderby);
        
    }

    /**
     *  getId()
     *  Fetch a record using the primakey value passed as a parameter.
     *  @param mix primary key value.
     *  @return true if the query as fetch data.
     */

    function getId($primary_key_value='') {
        if (empty($primary_key_value)) {
            $primary_key_value = $this->getPrimaryKeyValue();
        } else { 
			$this->setPrimaryKeyValue($primary_key_value);
		}
        if (is_numeric($primary_key_value)) {
            $this->query("select * from `".$this->getTable()."` where `".$this->getPrimaryKey()."` = ".$primary_key_value);
        } else {           
            $this->query("select * from `".$this->getTable()."` where `".$this->getPrimaryKey()."` = '".$primary_key_value."'");
        }
		if ($this->hasData()) { return true; } else { return false; }
    }


   /**
    * newForm create a generic form
    * for ->form to be use as an Event object
    * this is great to create custom form
    * without report templates.
    * @param eventAction an event to process the form
    */

    public function newForm($eventAction) {
      $this->form = new Event($eventAction);
	  //$this->setApplyFieldFormating(true, "Form");
	  $this->form->addParam("event_action_object", $this->getObjectName());
      return $this->form;
    }

    /**
     * newAddForm
     * To create a custom form to add a record in the database
     * Based on a logic of Form methods that used only the Event Object.
     * That doesn't require a form template or saved form.
	 * It will require a param goto. 
     * @param session_object_name optional, if you have the object in the session give its name here. 
     * @see newUpdateForm()
     */
    public function newAddForm($session_object_name='') {
      if (empty($session_object_name)) {
        if ((strlen($this->getObjectName()) > 0) && ($this->is_persistent)) {
            $session_object_name = $this->getObjectName();
        } else {
            $session_object_name = get_class($this);
        }
      }
      $this->newForm($session_object_name.'->eventAdd'); 
	  $this->setFields($this->getTable());
      $this->form->setLevel(1004);
      //$this->form->addEventAction($session_object_name.'->eventFieldFormatingOff', 92);
	  $this->form->addEventAction('Display::eventGotoPage', 9);
      $this->form->table = $this->getTable();
	  $this->setFieldsFormating(true, 'Form');
    }
 

     /**
      * newUpdateForm
      * like the newAddForm to edit a record of that object.
      * @param session_object_name give the name of that object in the session
      * @see newAddForm()
      */
    public function newUpdateForm($session_object_name='') {
      if (empty($session_object_name)) {
        if ((strlen($this->getObjectName()) > 0) && ($this->is_persistent)) {
            $session_object_name = $this->getObjectName();
        } else {
            $session_object_name = get_class($this);
        }
      }
      $this->newForm($session_object_name.'->eventUpdate'); 
      $this->form->setLevel(1004);
      //$this->form->addEventAction($session_object_name.'->eventValuesFromForm', 543);
	  $this->form->addEventAction('Display::eventGotoPage', 9);
      $this->form->table = $this->getTable();
	  $this->setFieldsFormating(true, 'Form');
    }   
 
     /**
	 * displayFormHeader()
	 * return the HTML code for the form 
	 * and related events actions
	 * @return html form tags
	 */
    function displayFormHeader() {
		$html_out = $this->form->getFormHeader();
		$html_out .= $this->form->getFormEvent();
		return $html_out;
	}
 
 
    /**
	 * displayFormFooter()
	 * Return HTML to close the form 
	 * Needed to turn off field formating
	 * @param submit label
	 */
	function displayFormFooter($submitvalue="") {
		$html_out = "";
		if (!empty($submitvalue)) {
   //   $submitvalue = "Submit" ;
			$html_out .= "\n  <input class=\"formfooter\" type=\"submit\" name=\"submitaction\" value=\"".$submitvalue."\"/>" ;
		}
		$html_out .= "\n</form>" ;
		$this->setFieldsFormating(false);
		return $html_out ;
	}

    /**
     * Not sure those 2 functions are needed
     * They are needed if the dataobject class in not abstract.
     * If it should be abstract then this could be set
     * on the new classes.
     */
    function setTable($tablename='') {
        if (!empty($tablename)) {
            $this->table = $tablename;
            return true;
        } else {
            $this->setLog("DataObject->setTable: Called with no tablename");
            return false;
        }
    }

    /**
     * getTable 
     * return the name of table associated with this dataobject.
     * @return string table name or false is not tables are set.
     */
    function getTable() {
        if (!empty($this->table)) {
            return $this->table;
        } else {
            $this->setLog("DataObject->getTable: no table name to return, returning false");
            return false;
        }
    }

    /**
     * Set the primary key field name for the table associate to the dataobject.
     * Should be set as a variable when extending the DataObject.
     * @param string database table primarykey
     */
    function setPrimaryKey($primary_key) {
        $this->primary_key = $primary_key ;
    }

    /**
     * return the primary key field of the table associated with the dataobject.
     * @return string database table primarykey field name.
     */
    function getPrimaryKey() {
        if (!empty($this->primary_key)) {
            return $this->primary_key ;
        } else {
            $this->getTableField();
            foreach($this->metadata as $fieldname => $fielddata) {
                    if ($fielddata['Key'] == "PRI") {
                        $this->primary_key = $fieldname;
                        continue;
                    }
            }
            if (!empty($this->primary_key)) {
                return $this->primary_key ;
            } else {
                $this->setPrimaryKey("id".$this->getTable());
                return "id".$this->getTable();
            } 
        }
    }
    
    /**
     * this would set a value for the primary key.
     * Use to point to a specific record in the database.
     * @param string value that will be set to the primarykey variable.
     * @see setId()
     */
    function setPrimaryKeyValue($primary_key_value) {
        $primary_key = $this->getPrimaryKey();
        $this->values[$primary_key] = $primary_key_value;
    }

    /**
     * same as setPrimaryKeyValue
     * @see setPrimaryKeyValue()
     */
    function setId($id_value) {
        $this->setPrimaryKeyValue($id_value);
    }

    /**
     * Returrn the value of the currently set primary variable.
     * @return mix value of the primary key variable.
     */
    function getPrimaryKeyValue() {
        if (isset($this->values[$this->getPrimaryKey()])) {
            return $this->values[$this->getPrimaryKey()];
        } else { return false; }
    }

    
    /** 
     * Set Fields for this object
     * Will instantiate a new Registry object
     * @param mixte string Registry $regname Registry name or Registry object
     * @param sqlConnect connexion object to use to load that query.
     */
    function setFields($xml_obj_fields="", $extracon=0) {
        if (empty($xml_obj_fields)) { $xml_obj_fields = $this->getTable(); }
        if (is_object($xml_obj_fields)) {
            $this->fields = $xml_obj_fields;
        } else {
            if (is_resource($extracon)) {
                $this->fields = new Fields($xml_obj_fields, $extracon);
            } else {
                $this->fields = new Fields($xml_obj_fields, $this->getDbCon());
            }
        }
    }

    /**
     * Get fields 
     * Return an array of FieldType objects
     * 
     * @see setFields()
     */

    function getFields() {
        return $this->fields;
    }
    /**
     * Set Fields formating
     * This enable disable the FieldType context generation of HTML arround the data fields.
     * 
     * @param boolean $bool true or false
     * @param string $context Disp or Form
	 * @see getFieldFormating()
     */
    
    function setFieldsFormating($bool, $context='Disp') {
		$this->applyreg = $bool ;
        $this->reg_context = $context;
	}

    /**
     * getFieldsFormating()
     * return a boolean that tel if the object is currenlty
     * applying the FieldType HTML generation to the fields values 
     * 
     * @return boolean 
     * @see setFieldFormating()
     */
    
	function getFieldsFormating() {
		return $this->applyreg ;	
	}
	
    /**
     * getApplyFieldsContext()
     * return a string that tels the current registry context.
     * @return string 
     * @see getApplyRegistry(), setApplyRegistry()
     */
    function getFieldsContext() {
        return $this->reg_context ;
    }
    /**
     * This one is deprecate use addNew() instead.
     * @see addNew();
     */
    function newRecord() {
        $this->addNew();
    } 

    /**
     * addNew()
     * Reset the values, variables and resultset of the object.
     * 
     * @see newRecord();
     */
    function addNew() {
        $this->values = Array();
        $this->result = null;
    }

    /** 
    * Set values is used to add values / variable of the object
    * This will overwrite all values previously set.
    * @param array $values array of values with key as fields: $values[fieldname]=$fieldvalue
    */
    function setValues($values) {
        $this->values = $values ; 
    }


    /**
    * get Values return the current values
    * It returns an array with the current values that have been set.
    * Values have format $values[varname] = values;
    *
    * @return Array array of values
    */
    function getValues() {
        return $this->values;
    } 
  
    /**
     * Testing with this. not sure it should stay...
     * Its loading in the object the fields variable its 
     * to be used as an EventAction by an Event object.
     *
     * @param EventControler eventcontroler object.
     */
    function eventValuesFromForm(EventControler $evctl) {
		$this->setFieldsFormating(false);
        $fields = $evctl->getParam("fields");
        if ($this->getTable() == '') { 
           $this->setLog("(".get_class($this).") setting tablename with:".$evctl->getParam("table")); 
           $this->setTable($evctl->getParam("table")); 
       }
        $this->setLog("\n values from form:");
        foreach ($fields as $key=>$value) {
            $this->values[$key] = $value;
            $this->setLog("\n ".$key." = ".$value);
        }
        //$this->setValues($evctl->getParam("fields"));
    }

    /**
     * eventAdd 
     * to trigger the add method from the eventControler.
     * It also check if doSave is == yes and set the insertid.
     */

    function eventAdd(EventControler $event_controler) {
		$this->setFieldsFormating(false);
		$this->eventValuesFromForm($event_controler);
        if ($event_controler->doSave == "yes") {           
            $this->add();
            $event_controler->insertid = $this->getInsertId($this->getTable(), $this->getPrimaryKey());
        }
    }

    /**
     * Add a record to the table.
     * And set the primary key so next time its an update
     * http://radria.sqlfusion.com/documentation/core:by_example_php5#add_delete_edit
     * @return boolean
     */

    function add($mix=null) {
        var_dump('adding');
        $qGetFields = new sqlQuery($this->getDbCon()) ;
        $qGetFields->setTable($this->getTable()) ;
        $tableFields = $qGetFields->getTableField() ;
        $this->setLog("\n in ".$this->getObjectName()." (DataObject)::Add()");
        $reg = new Fields();
        $reg->registryFromTable($this->getTable());

        $fields = $this->getValues();
        $this->setLogArray($fields);
        $table = $this->getTable();

        $fieldlist = "";
        $valuelist = "";
        if ($GLOBALS['cfg_local_db'] == "mysql") {

            while (list($key, $fieldname) = each($tableFields)) {
				$this->setLog("\n ".$fieldname."=".$fields[$fieldname].";");
                if (strlen($fields[$fieldname])>0) {
                    if (get_magic_quotes_gpc()) {
                        $fields[$fieldname] = stripslashes($fields[$fieldname]);
                    }
                    $fieldname = str_replace("`", "", $fieldname);
                    $fieldlist .= "`$fieldname`, ";
                    if ($fields[$fieldname] == "null") { 
                        $val = $fields[$fieldname]; 
                    } else {
                        if (function_exists("mysql_real_escape_string")) {
                            $val = "'".mysql_real_escape_string($fields[$fieldname])."'";
                        } else {
                         // } elseif (is_numeric($fields[$fieldname])) {
                //     $val = $fields[$fieldname];                    $val = "'".addslashes($fields[$fieldname])."'";
                        }
                    }
                    $valuelist .= "$val, ";
                }
            }
            $table = str_replace("`", "", $table);
            $fieldlist = ereg_replace(', $', '', $fieldlist);
            $valuelist = ereg_replace(', $', '', $valuelist);
            $query = "INSERT INTO $table ($fieldlist) VALUES ($valuelist)";

        } elseif ($GLOBALS['cfg_local_db'] == "pgsql") {

            while (list($key, $fieldname) = each($tableFields)) {
                    if (strlen($fields[$fieldname])>0) {
                        $this->setLog("\n For $key / $fieldname /  ");
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
                        if ($no_database_type) {

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
            $query = "INSERT INTO '$table' ($fieldlist) VALUES ($valuelist)";

        }
        $this->setLog("\n Running query:\n".$query);
        $result = $this->query($query) ;
        if (is_object($mix)) {
            if (strtolower(get_class($mix)) ==  "eventcontroler") {
                $mix->insertid = $this->getInsertId($this->getTable(), $this->getPrimaryKey());
            }
        }
		if ($this->auto_increment_key) {
			$this->setPrimaryKeyValue($this->getInsertId($this->getTable(), $this->getPrimaryKey()));
		}
        return $result;

    }


    /**
     * eventUpdate
     * to trigger the update method from the eventControler.
     * It also check if doSave is == yes 
     */

    function eventUpdate(EventControler $event_controler) {
		$this->setFieldsFormating(false);
		$this->setLog("\n doSave:".$event_controler->doSave);
        if ($event_controler->doSave == "yes") {
            $this->eventValuesFromForm($event_controler);
            $this->update();
        }
    }

    /**
     * Update a record in the table associate with the databoject.
     * The currently fetch will be the one updated.
     * This method requires a ->getId() or a valide fetch record.
     * http://radria.sqlfusion.com/documentation/core:by_example_php5#add_delete_edit
     */

    function update() {

        $this->setLog("\n DataObject, (".$this->getTable().") update()"); 

        //$urlerror = $this->getMessagePage() ;
        $valuelist = '';
        $query = '';
        $dbc = $this->getDbCon();
        $table = $this->getTable();
        $primary_key_var = $this->getPrimaryKey();
        $primary_key_value = $this->getPrimaryKeyValue();
        $this->setLog("\n primary key:".$primary_key_var." Value:".$primary_key_value);
        if (empty($primary_key_value)) {  
           $primary_key_value = 0; 
           $this->setLog("\n call on update with empty primary key value");
        }
        if (empty($GLOBALS['cfg_local_db'])) { $GLOBALS['cfg_local_db'] = "mysql"; }
        if (empty($primary_key_var)) { $this->setError("Call on update without primarykey"); return false; }
        if (is_object($this->fields)) {
            $reg = $this->fields;
        } else {
            $reg = new Fields();
            $reg->registryFromTable($table);
        }
        if ($GLOBALS['cfg_local_db'] == "mysql") {
            $primary_key_var = mysql_real_escape_string($primary_key_var);
            $primary_key_value = mysql_real_escape_string($primary_key_value);
            $fields = $this->getValues();
            while (list($key, $val) = each($fields)) {
                    if (get_magic_quotes_gpc()) {
                        $val = stripslashes($val);
                    }
                    $this->setLog("\n For $key / $val ");
                    if (is_object($reg->fields[$key])) {
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
                        if ((!empty($val) || $val == 0) 
                              && ($reg->fields[$key]->getRdata("databasetype") == "integer")
                            ) {
                            $val = (int)$val;
                        }
                        if ((!empty($val) || $val == 0)
                              && ($reg->fields[$key]->getRdata("databasetype") == "float")
                            ) {
                            $val = (float)$val;
                        }
                        if (!empty($val) || $val == 0) {
                           $valuelist .= "`$key` = $val, ";
                        }
                        $this->setLog(" add:`$key` = $val, ");
                    } else {
                        if($val != "null") $val = "'".mysql_real_escape_string($val)."'";
                        $valuelist .= "`$key` = $val, ";
                        $this->setLog(" add:`$key` = $val, ");
                    }
                    } 
            }
            $valuelist = ereg_replace(', $', '', $valuelist);
            
            if (!empty($primary_key_var)) {
				if (is_object($reg->fields[$primary_key_var])) {
					if ($reg->fields[$primary_key_var]->getRdata("databasetype") == "integer") {
						   $query = "UPDATE `$table` SET $valuelist WHERE $primary_key_var = $primary_key_value";
					} else {
						 $query = "UPDATE `$table` SET $valuelist WHERE $primary_key_var = '$primary_key_value'";
					}
				} else {
					 $query = "UPDATE `$table` SET $valuelist WHERE $primary_key_var = '$primary_key_value'";
				}
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
            } 

        } 
        $this->setLog("\n Message:".$setMessage);
        $this->setLog("\n Running query:\n".$query);
        $sql_query = $query;
        $qSaveData = new sqlQuery($dbc) ;
        $result = $qSaveData->query($query) ;
        return $result;

    }

    /**
     * eventDelete 
     * to trigger the delete method from the eventControler.
     * This is a serious risk, we may want to make it more secure.
	 * Not sure how, so for now here it is...
     */

  //  function eventDelete(EventControler $event_controler) {
   //      if ($this->getPrimaryKeyValue() > 0) {
    //        $this->delete();   
  //      }
  //  }

    /**
     * delete a record in the table associated with the databoject.
     * If the id parameter is not set then the current record id is used.
     * http://radria.sqlfusion.com/documentation/core:by_example_php5#add_delete_edit
     *
     * @param int primary key value of the record to delete.
     */

    function delete($id='') {
        if (empty($id)) {
            $id = $this->getPrimaryKeyValue();
        }
        if (!empty($id)) {		
			if (is_integer($id)) { 
				$q_del = new sqlQuery($this->getDbCon()) ; 
				$q_del->query("delete from ".$this->getTable()." where ".$this->getPrimaryKey()." = ".$id);
			} else {
				$q_del = new sqlQuery($this->getDbCon()) ; 
				$q_del->query("delete from ".$this->getTable()." where ".$this->getPrimaryKey()." = '".mysql_real_escape_string ( $id )."'");				
			}
        }
    }

    /**
     * This method check if the databoject current record as data.
     * Either the result set of the values array.
     * @return boolean true or false
     */

    function hasData() {
      $has_data = false;
      if (is_resource($this->result)) { 
        if ($this->getNumRows() > 0) {
            $has_data =  true;
        }
      }
      if (count($this->values) > 0) {
        $has_data = true;
      }
      return $has_data;
    }

    //function getInsertId() {
    //    return $this->insert_id;
    //}
    }
?>
