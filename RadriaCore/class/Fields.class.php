<?php
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
 /**
  * Registry
  * This files contain the Registry class and all the core regitryfield class
  * @see RegistryFieldBase, Registry
  * @package RadriaCore
  */



 /**
  *  Registry Object Apply load and apply the registry rules.
  *
  *  The Registry is load from a .reg.xml file in registry/ directory.
  *  for each field element in the .reg.xml file a RegistryField object
  *  is created based on its fieldtype (rdata).
  *  It can then be apply for Form context or Display context.
  *  Each RegistryField object contains a default_form and default_disp
  *  method that generate HTML code for both context.
  *  From the .reg.xml files all the rdata values are accessible
  *  with the getRdata().
  *  And they can also be methods of a RegistryField object.
  *  This is to simplify the extention of the RegistryField object..
  *  The Registry constructor requires a connexion and a prefix of a .reg.xml file.
  *  To apply the registy to a field call the apply method with context, field name and field value.
  *
  *  Alot of the properties currently there are deprecate and should not be use.
  *
  * @package RadriaCore
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @version 4.0.0
  * @access public
  */

#namespace radriacore;

Class Fields  extends BaseObject {
    /**  Name of the folder where serialized fields are.
     * @var String $tbl_registry
     */
    var $tbl_registry = "registry" ;
    
    /**  Name of the table on which the registry need to be apply
     * @var String $table
     */
    var $table ;

    /** fields array containing all the fields objects
     * @var Array $fields contains fields object
     */
    var $fields = Array() ;

    var $field ;
    /**  Reg Info for Options type, typicaly list box, list or choices.
     * @var Array $optiontype index on field of the table
     */
   
    var $fieldnumber = 0;
    var $totalnumfields = 0;
    var $formname ;

    /** Flag to include the javascript function only once for the date fields
    * @var boolean true or false
    */
    var $datejsinclude = true;

    /**  The database connexion where the regtable and table to apply the registry are.
    * @var sqlConnect $dbc database connexion
    */

    var $dbc ;

    /** Project dir to replace the dbc requirement
     */
    var $project_path = ".";

    /** Radria base path
     * This should not be used at all
     */
    var $radria_core_path = "../RadriaCore/";
    
    /**
     * Constructor
     * Constructor, create a new instance of a registry for a table. Load all the registry info in the Registry object.
     * To make it available to apply the registry in the different contexts.
     * @param object sqlConnect $dbc  Database connexion
     * @param String $table name of the table where to apply the registry.
     * @access public
     *
     * Constructor now is going to load the reqistry XML or table
     * for each field get its type (if not defined use default char)
     * create a new instance of the associated field type object and store it into an array
     * indexed by the field name.
     * Built a [rtype]=rdata array and pass it to the constructor
     *
     */
    function __construct($table='', $dbc='') {
      
      if (is_resource($table)) { $dbc = $table; $table = '';} // temporary hack for backward compatibility. (th4bc)
	  if ($dbc=='') { $dbc = $GLOBALS['conx']; }
      if ($dbc != "") {
        $this->dbc = $dbc ;
        $this->project_path = $this->dbc->getProjectDirectory();
      } 
 
      if (!empty($GLOBALS['cfg_local_pasdir'])) {
          $this->radria_core_path = $GLOBALS['cfg_local_pasdir'];
      }
      if (defined("RADRIA_LOG_RUN_FIELDS")) {
            $this->setLogRun(RADRIA_LOG_RUN_FIELDS);
      }
      $this->setLog("\n \n Fields object constructor: ".$table);
            
      
      if(strlen(trim($table)) > 0) {
		  	$table = trim($table);
            $this->table = $table ;
            $this->setLog("\n loading from registry: ".$table);
            if (strpos($table, ",") === false) {
                if (file_exists($this->project_path."/".$this->tbl_registry."/".$this->table.".reg.xml")) {
                    $this->setLog(" \n loading from xml registry:".$this->table);
                    $this->registryFromXML($table);
                } else {
                    $this->setLog(" \n loading from Table registry:".$this->table);
                    $this->registryFromTable($table);
                }
            } else {
                $this->setLog("\n Multiple registries detected");
                $a_tables = explode(",", $table);
                foreach($a_tables as $reg_table) {
                    $reg_table = trim($reg_table);
                    if (file_exists($this->project_path."/".$this->tbl_registry."/".$reg_table.".reg.xml")) {
                        $this->setLog(" \n loading from xml registry:".$reg_table);
                        $this->registryFromXML($reg_table);
                    } else {
                        $this->registryFromTable($reg_table);
                        $this->setLog(" \n loading from Table registry:".$reg_table);
                    }
                }
            }
      }
    }

 /**
  * Load registry from xml file
  * Method to load the registry from a file.
  * Was originaly from the constructor, but moved it to allow
  * other method to populate the registry.
  *
  * @param String registry_name name of the registry in xml, it will look into registry/ folder and add .reg.xml
  * @see registrytoXML()
  */
    function registryFromXML($table='') {
        if (!empty($table)) {
            $this->table = $table;
        }
        //$dbc = $this->dbc;
        include_once($this->radria_core_path."class/XMLBaseLoad.class.php") ;
        include_once($this->radria_core_path."class/XMLRegistryLoad.class.php") ;
        $regfilename1 = $this->radria_core_path.$this->tbl_registry."/".$this->table.".reg.xml" ;
        $regfilename2 = $this->project_path."/".$this->tbl_registry."/".$this->table.".reg.xml" ;
        if (file_exists($regfilename1)) {
            $xmlReg = new XMLRegistryLoad() ;
            $xmlReg->init($regfilename1) ;
        } elseif(file_exists($regfilename2)) {
            $xmlReg = new XMLRegistryLoad() ;
            $xmlReg->init($regfilename2) ;
        }
        if (is_object($xmlReg)) {
            $xmlReg->parse() ;
            $aReg = $xmlReg->finaldata ;
            $xmlReg->free() ;
            if (is_array($aReg)) {
                reset($aReg)  ;
                while (list ($rfield, $aFieldtype) = each($aReg)) {
                    $this->totalnumfields++ ;
                    $this->setLog("\n load reg for ".$rfield);
                    $a_rtype = Array();
                    $fieldtype = "";
                    while (list ($rtype, $rdata) = each($aFieldtype)) {
                        //if ($this->{$rtype}[$rfield] == "") {
                            //$this->{$rtype}[$rfield] = $rdata ;
                            if ($rtype == "fieldtype") {
                                $fieldtype = $rdata;
                                $this->setLog("\n field type : ".$fieldtype);
                            } else {
                                $a_rtype[$rtype]=$rdata ;
                                $this->setLog("\n rtype :".$rtype." - data ".$rdata);
                            }
                        //}
                    }
                    $this->addField($rfield, $fieldtype, $a_rtype);
                    if (is_object($this->fields[$rfield])) {
                        $this->setLog("\n Field loaded (".count($a_rtype)."): ".$rfield);
                    }
                }
            }
        }
        $this->setLog("\n - Registry for ".$table." loaded");
    }

  /**
   * Load Registry from table description
   * This method will load the registry by discovering the different
   * fields type from the table.
   * @param String table_name name of the table
   * @see registryFromXML()
   */

    function registryFromTable($table_name) {
		if (is_object($this->dbc)) {
			$qTable = new sqlQuery($this->dbc);
		} else {
			$qTable = new sqlQuery($GLOBALS['conx']) ;
		}
        $fields = $qTable->getTableField($table_name) ;
        if (is_array($fields)) {
        $this->setRegistryFromQueryMetadata($fields, $qTable);
        } else { $this->setError(" Registry From Table: no field founds for this table: ".$table_name);}
    }

  /**
   * Load Registry from a Query Result set
   * This method will load the registry by discovering the different
   * fields type from the result of a query.
   * @param String table_name name of the table
   * @see registryFromXML()
   */

    function registryFromQueryResult($sqlQuery) {
        if (is_object($sqlQuery)) {
            if (is_resource($sqlQuery->getResultSet())) {
                $fields = $sqlQuery->getQueryField();
                if ($fields !== FALSE) {
                    $this->setRegistryFromQueryMetadata($fields, $sqlQuery);
                    return true;
                } else {
                    $this->setError("registry From Query Result: not supported for your database");
                    return false;
                }
            } else {
                $this->setError("registryFromQueryResult: No Result Set from query, make sure the query is executed first");
                return false;
            }
        } else {
            $this->setError("registryFromQueryResult: A sqlQuery object is required ");
            return false;
        }
    }

  /**
   * Set default registry field from field types.
   * This method is private used by registryFromQueryResult and registryFromTable
   * @param String table_name name of the table
   * @see registryFromXML()
   */

    function setRegistryFromQueryMetadata($fields, $qTable) {
        if (is_array($fields) && is_object($qTable)) {
        while (list($key, $fieldname) = each($fields)) {
            $this->setLog("\n loading field:".$fieldname." Type:".$qTable->metadata[$fieldname]["Type"]);
            if (preg_match("/int/i", $qTable->metadata[$fieldname]["Type"])
                ) {
                $this->addField($fieldname, "FieldTypeInt");
                $this->fields[$fieldname]->addRData("textline", "5:10");
                $this->fields[$fieldname]->addRData("databasetype", "integer");
            } elseif (preg_match("/float/i", $qTable->metadata[$fieldname]["Type"])
                    || preg_match("/numeric/i", $qTable->metadata[$fieldname]["Type"])
                    || preg_match("/double/i", $qTable->metadata[$fieldname]["Type"])
                    || preg_match("/real/i", $qTable->metadata[$fieldname]["Type"])
                      ) {
                $this->addField($fieldname, "FieldTypeFloat") ;
                $this->fields[$fieldname]->addRData("databasetype", "float");
            } elseif (preg_match("/text/i", $qTable->metadata[$fieldname]["Type"])
                   || preg_match("/blob/i",$qTable->metadata[$fieldname]["Type"])
                     ) {
                $this->addField($fieldname, "FieldTypeText") ;
                $this->fields[$fieldname]->addRData("databasetype", "text");
                $this->fields[$fieldname]->addRData("textarea", "40:10");
            } elseif (preg_match("/date/i", $qTable->metadata[$fieldname]["Type"])) {
                $this->addField($fieldname, "FieldTypeDateSQL") ;
                $this->fields[$fieldname]->addRData("datesql", "m/d/Y:today:0:0");
                $this->fields[$fieldname]->addRData("databasetype", "date");
            } elseif (preg_match("/time/i", $qTable->metadata[$fieldname]["Type"])) {
                $this->addField($fieldname, "FieldTypeTimeSQL") ;
                $this->fields[$fieldname]->addRData("timef", "now:0");
                $this->fields[$fieldname]->addRData("databasetype", "time");
            } else {
                $this->addField($fieldname, "FieldTypeChar") ;
                $this->fields[$fieldname]->addRData("databasetype", "varchar");
            }
        }
       } else {
            $this->setError("Registry From Query Metadata, invalid parameters, this is a private method used by registry from query and registry from table method");
            return false; }
    }


  /**
   * Apply the registry on a Display Context.  For a field and a value.
   * This requires that the call is done from a Report object.
   * Temporary methode will be deprecate when Report objects will use
   * the context and directly the apply() method
   *
   * @param String $fname Name of the field
   * @param String $fval Value of the field.
   * @access public
   * @deprecate Used for backward compatibility with old Reports class
   * @see apply()
   */
  function applyRegistry($fname, $fval) {
   return $this->apply("Disp", $fname, $fval) ;
  }

  /**
   * Apply the registry on a Form Context.  For a field and a value.
   * This requires that the call is done from a Report object.
   *
   * @param String $fname Name of the field
   * @param String $fval Value of the field.
   * @access public
   * @see apply()
   * @deprecate Used for backward compatibility with old ReportForm class.
   */
  function applyRegToForm($fname, $fval) {
   return $this->apply("Form", $fname, $fval) ;
  }

  /**
   * Apply the registry.  For a field and a value.
   *
   * @param String $context string of the context to apply reg
   * @param String $fname Name of the field
   * @param String $fval Value of the field.
   * @access public
   */

  function apply($context, $fname, $fval) {
    if (is_object($this->fields[$fname])) {
        $val = $this->fields[$fname]->process($context, $fval);
    } else {
        $field = new FieldTypeChar($fname);
        $val = $field->process($context, $fval);
    }
    //$this->setLogRun(false);
    $this->setLog("\n field name ".$fname." - context ".$context);
    $this->setLog("\n processed value :\n\n".$val);
    return $val;
  }

  /**
   * add a field to the registry object.
   * Add a new field to the current registry object.
   *
   * @param string $field_name  Name of the field need to match with the form.
   * @param string $field_type  Field Type this needs to be the class name of a registry field type
   * @param array $a_rtype  Array with all the rdata for that field type. $a_rtype[$rtype_name] = $rdata_value
   * @param sqlConnect $dbc Open Database connection object.
   * @access public
   */


  function addField($field_name, $field_type="", $a_rtype=0, $dbc=0) {
    if (is_object($field_name)) {
        $this->fields[$field_name->getFieldName()] = $field_name;
        $this->fields[$field_name->getFieldName()]->setRegistryName($this->table);
    } else {
        //if ($dbc == 0) { $dbc = $this->dbc; }
        if (!empty($field_type) && class_exists($field_type)) {
            $this->fields[$field_name] = new $field_type($field_name, $a_rtype, $dbc);
        } else {
            $this->fields[$field_name] = new strFBFieldTypeChar($field_name, $a_rtype, $dbc);
            $field_type = "strFBFieldTypeChar";
        }
        $rd_fieldtype = $this->fields[$field_name]->getRData("fieldtype");
        if (empty($rd_fieldtype)) {
            $this->fields[$field_name]->setRData("fieldtype", $field_type);
        }
        $this->fields[$field_name]->setRegistryName($this->table);
    }
	return $this;
    
  }

    /** 
     * Set Field object with the magic __set
     * 
     * @param string $data_value
     */
    function __set($field_name, $FieldTypeObject) {
        $this->fields[$field_name] = $FieldTypeObject;
    }

    /** 
     * Get Field Object with the magic __get 
     * 
     * @param string $data_value
     */
    function __get($field_name) {
        return $this->fields[$field_name];
    }

  /** regToXML and RegistryToXML
   * Save the current reg object in an XML file.
   * This method will serialize the current registry object to an XML file.
   * This requires that the call is done from a Report object.
   * If the file is in the main PAS registry directory it will overwrite it
   * if not it will overwrite or create a new one in the project registry folded.
   *
   * @param string $name registry filename.
   * @access public
   */
  function regToXML($name="") {
    if (empty($name)) {
        $name = $this->table;
    }
    foreach (array_keys($this->fields) as $key) {
        $byField[$key] = $this->fields[$key]->getRDatas();
    }
    /* Deprecate can't work in PAS 3.0 was working for MyDB 2.2
    $regVars =  get_object_vars($this)  ;
    while(list ($rtype, $value) = each($regVars)) {
      if (is_array($value)) {
        while(list ($rfield, $rdata) = each($value)) {
         if(strlen($rdata)>0) {
            $byField[$rfield][$rtype] = $rdata ;
          }
        }
      }
    }
    */
    $xmlData = "";
    if (!is_array($byField)) { $byField = Array(); }
    while(list ($rfield, $value) = each($byField)) {
        $xmlData .= "\n  <rfield name=\"".$rfield."\">" ;
      while(list ($rtype, $rdata) = each($value)) {
         $xmlData .=  "\n    <rdata type=\"".$rtype."\">".$rdata."</rdata>" ;
      }
        $xmlData .= "\n  </rfield>" ;
    }
    $regfilename1 = $this->radria_core_path.$this->tbl_registry."/".$name.".reg.xml" ;
    $regfilename2 = $this->project_path."/".$this->tbl_registry."/".$name.".reg.xml" ;
    if (file_exists($regfilename1)) {
      $fp = fopen($regfilename1, "w") ;
    } else {
      $fp = fopen($regfilename2, "w") ;
    }
    $header = "<?xml version=\"1.0\"?>\n<registry>" ;
    $footer = "\n</registry>" ;
    $xmlFile = $header.$xmlData.$footer ;
    fwrite($fp, $xmlFile) ;
      fclose($fp) ;
  }
  function registryToXML($name="") {
    $this->regToXML($name);
  }
  function serializeToXML($name="") {
    $this->regToXML($name);
  }

  /**
   * setFormName set a name for the form context
   *
   * In the form context some fields type needs the name of the form to generate some javascripts code.
   * It will also set the form name in all the RegistryFields object of that Registry.
   *
   * @param string $formname Name of the form for the form context
   * @see getFormName()
   */

  function setFormName($formname) {
    $this->formname = $formname;
    //print "Registry->setFormName(): $formname<BR>\n";
    foreach (array_keys($this->fields) as $key) {
        //print "this->fields[$key]->setFormName()<BR>\n";
        $this->fields[$key]->setFormName($formname);
    }
  }

  function getFormName() {
    return $this->formname;
  }

}


/**
 * Fields
 * in the new nomenclature project
 * Renaming Registry with Fields
 * This is extremely experimental, 
 * Only use with big caution as it may change anytime.
 */

/**
class Fields extends Registry {
	
	function __construct($table='', $dbc='') {
		if (is_resource($table)) { $dbc = $table; } // temporary hack for backward compatibility. (th4bc)
		if ($dbc=='') { $dbc = $GLOBALS['conx']; }
		parent::Registry($dbc,$table);
	}
};
**/
?>
