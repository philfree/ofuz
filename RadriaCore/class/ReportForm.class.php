<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
   * Report Form Object 
   * @see ReportForm
   * @package RadriaCore
   */
   
  /**
  *  Object reportForm display a Form
  *
  *  ReportForm use a template stored in the report table form directory 
  *  and apply the registry and table record associate to it.
  *  
  *  This version doesn't support multiple tables yet.
  *
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2007
  * @version 4.0.0
  * @package RadriaCore
  * @access public
  */

class ReportForm extends Report{
  /**  unused in version 2 will be deleted  The event level is now in the event object.
   * @var int $eventlevel
   * @deprecated
   */
  var $eventlevel ;
  /**  Event controler URL
   * @var String $eventcontrol
   * @see setEventControler()
   */
  var $eventcontrol = "eventcontroler.php" ;
  /**  String with the URL of the next URL
   * @var String $urlnext
   */
  var $urlnext;
  /**  flag and string with the primary key from the where statement
   *  used to tel if its an update or a  new record.
   * @var String $primarykey
   */
  var $primarykey = "1" ;
  /** Primary key var is the name of the primary key.
    * @var string $primarykey_var
    */
  var $primarykey_var;
  /**  String with the name of the table that contains the reportForm objects.
   * @var String $primarykey value 0 = new record and 1 update.
   */
  var $tbl_report = "form";
  /**  Event object for that form, manage events, levels and parameters associate with this form.
   * @var Event $event
   */
  var $event ;

  /** Name of the report template to load instead of generating the default one.
   *  @var string $report_template   name of the report template to load.
   **/
  var $report_template = "";

  /** Submit button label
  * @var string $submit_label label of the submit button
  */
  var $submit_label = "";
  
  /** Flag to check if footer needs to be set again
   */
  var $addSubmit = true;
  
  /** Flag custom_event to tel its a custom event that will process the form data
   */
  var $custom_event = false;

   /** Constructor call super() and instanciate the event object.
   *  It uses a constant called "PAS_DEFAULT_FORM_TEMPLATE" to set the
   *  form template to use when the setDefault() methode is called.
   *
   * @param object sqlConnect $dbc
   * @param int $id unique id of the report to be reactivate from instanciations.
   * @param sqlConnect $extracon extra connextion used to get from registry information from an other database.
   * @access public
   * @see Report
    */

  function ReportForm($dbc, $id="", $extracon=0) {
    $this->setContext("Form");
    if (defined("RADRIA_DEFAULT_FORM_TEMPLATE")) {
        $this->report_template = RADRIA_DEFAULT_FORM_TEMPLATE;
    }
    $this->Report($dbc, $id, $extracon) ;
    $this->event = new Event() ;
    if (defined("RADRIA_LOG_RUN_REPORTFORM")) {
        $this->setLogRun(RADRIA_LOG_RUN_REPORTFORM);
    }
    $this->setLog("\n Start Report form object");
  }

  /**
   *  setQuery built the query or get it from the associated saved query
   *  
   *  This method will execute the query.
   *  If the $table param and primary key value is set a default query
   *  will be set and executed.
   *
   * @param String $table Name of the table to generate form from.
   * @param String $primary_key_value  value of the primary key to select the values for the form
   */

  function setQuery($table="", $primary_key_value="", $primary_key_var="") {
    if (!empty($table)) {
      $this->squery = new sqlQuery($this->getDbCon()) ;
      $this->squery->setTable($table) ; 
      if (empty($primary_key_var)) { $primary_key_var = $this->getPrimaryKeyVar(); }
      if (empty($primary_key_var)) { $primary_key_var = "id".$table; }
      if (!empty($primary_key_value)) {
          $this->squery->query("SELECT * FROM ".$table." WHERE ".$primary_key_var."= ".$primary_key_value." LIMIT 1,1") ;
      }
      if (empty($primary_key_var) && empty($primary_key_value) && (!empty($_SESSION['id'.$table]))) {
          $this->squery->query("SELECT * FROM ".$table." WHERE id".$table."= ".$_SESSION['id'.$table]." LIMIT 1,1") ;
      }
      $this->setPrimaryKey($primary_key_var);
    }
    if ( strtolower(get_class($this->squery)) == "sqlsavedquery"  && empty($table)) {
        if($this->squery->getQueryReady() ) {
                    $this->squery->query() ;
        }
    } else {
        $this->squery->query() ;
    }
    if (is_resource($this->squery->result)) {
        return true;
    } else {
        return false;
    }
  }

  /**
   * Get Table fetch try to auto detect the table name
   * private method
   * @return string $tablename or false
   */

    function guessTable() {
        $table = "";
        if(is_object($this->squery)) {
            if (is_resource($this->squery->result) && ($this->squery->getTableName() !== FALSE)) {
                $this->squery->setTable($this->squery->getTableName()) ;
                $table = $this->squery->getTable() ;          
            } else {
                $table = $this->squery->getTable() ;
            }
        }
        if (empty($table)) {
            return false;
        } else {
            return $table;
        }
    }


  /**
   * setDefault load default value and display the form using the default template if
   * there is none or if its a new object not yet serialized.
   * If no default template are set it will try to read the table catalog to get the list of the fields name and their type and generate a default report.
   *
   * @param String $table Name of the table to generate form from.
   * @param string $report_template Name of the report template to use.
   */

  function setDefault($table="", $report_template="") {
    $dbc = $this->getDbCon() ;
    if (empty($table)) {
        $table = $this->table;
    } else {
       $this->table = $table;
    }
    if (strlen($report_template)>0) {
        $this->report_template = $report_template;
    }
    // Deprecate, now setQuery() should be called instead.
    if (!is_resource($this->squery->result)) {
      if (!empty($table)) {
        $this->squery = new sqlQuery($this->getDbCon()) ;
        $this->squery->setTable($table) ;
        if (!empty($_SESSION['id'.$table])) {
            $this->squery->query("SELECT * FROM ".$table." WHERE id".$table."= ".$_SESSION['id'.$table]." LIMIT 1,1") ;
        } else {
            $this->setNoData(true);
        }
      }
    } 
    if (empty($this->table) && !$this->getNoData()) {
        $table = $this->guessTable();
        $this->table = $table;
    }
    // Replaced by guessTable
//     if(is_object($this->squery)) {
//       if (is_resource($this->squery->result) && ($this->squery->getTableName() !== FALSE)) {
//           $this->squery->setTable($this->squery->getTableName()) ;
//           $table = $this->squery->getTable() ;          
//       } elseif (empty($table)) {
//           $table = $this->squery->getTable() ;
//       } else {
//           $this->squery->setTable($table) ;
//           $table = $this->squery->getTable() ;
//       }
//     }
    $this->setLog("\ntable:".$table);
    $this->setLog("\nthis table:".$this->table); 
    if (is_object($this->reg)) { $this->setLog("\nRegistry loaded pre auto detect:".count($this->reg->fields)); }

    if ((!is_object($this->reg) || count($this->reg->fields)==0)) {
      if (!empty($table) && file_exists($dbc->getProjectDirectory()."/registry/".$table.".reg.xml")) {
        $this->reg = new Registry($this->getDbCon(), $table) ;
      } elseif (is_resource($this->squery->result)) {
        $this->reg = new Registry($this->getDbCon()) ;
        if (!$this->reg->registryFromQueryResult($this->squery)) {
            $this->reg->registryFromTable($table);
            $this->setLog("\n Load default registry using table name with an existing query");
        } else { $this->setLog("\n Load from a result query"); }
      } elseif(!empty($table)) {
        $this->reg = new Registry($this->getDbCon(), $table) ;
        $this->setLog("\n Load default registry using table name");
      }
    }

    if (is_object($this->reg)) { $this->setLog("\nRegistry loaded:".count($this->reg->fields)); }

    if (strlen($this->report_template) > 0) {
        $tpl_fields = Array("tablename", "formpage", "eventcontroler", "fieldname", "fieldlabel", "primarykey", "submit_label");
        $values["primarykey"] = "id".$table;
        $values["tablename"] = $table;
        $values["eventcontroler"] = $this->getEventControl();
        if (strlen( $this->getSubmitLabel()) > 0 ) {
            $values["submit_label"] = $this->getSubmitLabel();
        } else {
            $values["submit_label"] = "Submit";
        }

        $conx = $this->getDbCon();
        $rvalues = Array();
        $rfields = Array();
        $order = 0;
        foreach($this->reg->fields as $regfields) {
            if ($regfields->getRData("ordering") && $this->applyreg) {
                $rfields[$regfields->getRData("ordering")] = $regfields;
                if ($order < $regfields->getRData("ordering")) {
                    $order = $regfields->getRData("ordering") + 1;
                } else {
                    $order++;
                }
            } else {
                $rfields[++$order] = $regfields;
            }
        }
        ksort($rfields);
        foreach($rfields as $regfields) {
            if ($this->applyreg) {
            //if (!$regfields->getRData("hidden")) {  // causes problems in template forms with fields using hidden vlues. PhL 03/02/2005
                $rvalues["fieldname"] = $regfields->getFieldName();
                if ($regfields->getRData('readonly') || $regfields->getRData("hidden"))  {
                    $rvalues["fieldlabel"] = "";
                } else {
                    if (strlen($regfields->getRData("label")) > 0)  {
                        $rvalues["fieldlabel"] = $regfields->getRData("label");
                    } else {
                        $rvalues["fieldlabel"] = $regfields->getFieldName();                    
                    }
                }
                $row_values[] = $rvalues;
            //}
            } else {
                $rvalues["fieldname"] = $regfields->getFieldName();
                $rvalues["fieldlabel"] = $regfields->getFieldName(); 
                $row_values[] = $rvalues;
            }
        }
        if (file_exists($conx->getProjectDirectory()."report/".$this->report_template.".header.tpl.report.xml")) {
            $r_deflt_header = new Report($conx, $this->report_template.".header.tpl");
            //$r_deflt_header->setField($tpl_fields);
            $r_deflt_header->setValues($values);
            $r_deflt_header->setNoData();
            $r_deflt_header->setRowValues($row_values);
            $this->setHeader($r_deflt_header->doReport());
        }
        if (file_exists($conx->getProjectDirectory()."report/".$this->report_template.".row.tpl.report.xml")) {
            $r_deflt_row = new Report($conx, $this->report_template.".row.tpl");
            $r_deflt_row->setValues($values);
            $r_deflt_row->setRowValues($row_values);
            $this->setRow( $r_deflt_row->doReport());
        }
       if (file_exists($conx->getProjectDirectory()."report/".$this->report_template.".footer.tpl.report.xml")) {
            $r_deflt_footer = new Report($conx, $this->report_template.".footer.tpl");
            $r_deflt_footer->setValues($values);
            $r_deflt_footer->setRowValues($row_values);
            $this->setFooter($r_deflt_footer->doReport());
        }
        //  $this->setLog("\n------ Default generated Header ------\n".$this->getHeader()) ;
        //  $this->setLog("\n------ Default generated Row ------\n".$this->getRow()) ;
        //  $this->setLog("\n------ Default generated Footer ------\n".$this->getFooter()) ;


    } else {
        $this->setQuery($table);
        $this->setHeader("<TABLE class=\"tableform\">");
        $tablefields = $this->squery->getTableField() ;
        while(list($key, $value) = each($tablefields)) {
            $row .= "\n<TR>\n  <TD class=\"tdformlabel\">" ;
            if (is_object($this->reg)) {
                if (is_object($this->reg->fields[$value])) {
                    if (!($this->reg->fields[$value]->getRData("hidden") || $this->reg->fields[$value]->getRData("readonly")) && strlen($this->reg->fields[$value]->getRData("label")) > 0) { $row .= $this->reg->fields[$value]->getRData("label") ; }
                    else { $row .= "" ; }
                }
            }
            $row .= "</TD>" ;
            $row .= "\n  <TD class=\"tabletdformfield\">[".$value."]</TD>" ;
            $row .= "\n</TR>" ;
        }
        $footer = "\n <TR> \n  <TD colspan=\"2\" align=\"right\"><INPUT TYPE=\"submit\"></TD>\n </TR>\n</TABLE>" ;
        $this->setRow($row) ;
        $this->setFooter($footer) ;
    }
    $this->field = $this->getField($this->getRow()) ;
  }

 /**
   *  Set Primary key and set default update / add events.
   * 
   *  Set the default event as forms for update or add record using
   *  mydb.updateRecord and mydb.addRecord Events.
   *  IF you need to add events to your form add them before calling this methods.
   *  
   *  This method will also try to detect the primary key to 
   *  use in the where statement to update the record.
   *  The primary key is in the form keyfieldname='record_id_to_edit'
   *
   *  This method needs a saved query or a table name. If your reportForm doesn't have
   *  a saved query (in the xml file or using ->setSavedQuery()) you need to set
   *  the table name using ->setTable($tablename) that will receive the data.
   *
   * @see setPrimaryKey(), setAddRecord(), setTable(), mydb.updateRecord, mydb.addRecord
   */

  function setForm() {
    //print gettype($this->squery)."<BR>\n";
    //print "this->squery: ".$this->squery."<BR>\n";
   ## Seems to have very serious backward compatiblity issue.
   if (($this->event->getName() != "") && ($this->custom_event)) {
    //if (0) {
      $header = $this->event->getFormHeader()  ;
      $header .=  $this->event->getFormEvent() ;
      $this->header = $header.$this->header;
      if (!empty($this->submit_label) && $this->getAddSubmit()) {
          $this->footer .= "<div style=\"text-align:right\">".$this->event->getFormFooter($this->getSubmitLabel())."</div>" ;
      } else {
            $this->footer .= $this->event->getFormFooter() ;
      }
      $this->setAddSubmit(false);
    } else {
        // Looks deprecate but very much in use....
        if (is_object($this->squery) && !($this->getNoData())) {
        if ($this->primarykey == "1") {
            if (ereg("where", $this->squery->sql_query)) {
            list($trash, $primarykey) = explode("where", $this->squery->sql_query) ;
            $this->primarykey = $primarykey;
            }
            if (ereg("WHERE", $this->squery->sql_query)) {
            list($trash, $primarykey) = explode("WHERE", $this->squery->sql_query) ;
            $this->primarykey = $primarykey;
            }
            if (ereg("group", $this->primarykey)) {
            list($primarykey, $trash) = explode("group", $this->primarykey) ;
            $this->primarykey = $primarykey;
            }
            if (ereg("GROUP", $this->primarykey)) {
            list( $primarykey, $trash) = explode("GROUP", $this->primarykey) ;
            $this->primarykey = $primarykey;
            }
        }
        if (strlen($this->table)==0) {
            $table = $this->squery->getTable() ;
        } else {
            $table = $this->table ;
        }
        }
        if ($this->getUrlNext() == "") {
            global $PHP_SELF ;
            $goto = $PHP_SELF ;
        } else {
            $goto = $this->getUrlNext() ;
        }
        if (empty($table)) {
            $table = $this->table;
        } 
        if (empty($table)) {
            $table = $this->guessTable();
        }
    
        $this->event->setEventControler($this->getEventControl()) ;
        if ($this->primarykey && $this->squery && $this->squery->getNumRows()>0) {
            $primarykey = trim($this->primarykey) ;
            $this->event->setName("mydb.updateRecord") ;
            //$this->reg->setFormName(str_replace(".","_", $this->event->getName())) ;
            $this->event->setAction("update") ;
            $this->event->setLevel(1000) ;
            $this->event->addParam("table", $table) ;
            $this->event->addParam("goto", $goto) ;
            $this->event->addParam("primarykey_var", $this->getPrimaryKeyVar());
            $this->event->addParam("primarykey", $primarykey) ;
            $this->event->setGotFile() ;
        } elseif(!empty($this->table)) {
            $this->event->setName("mydb.addRecord") ;
            //$this->reg->setFormName(str_replace(".","_", $this->event->getName())) ;
            $this->event->setAction("add") ;
            $this->event->setLevel(1000) ;
            $this->event->addParam("table", $this->table) ;
            $this->event->addParam("goto", $goto) ;
            $this->event->setGotFile() ;
        } else { $this->setError("Events could not be set. You need a query or to set a table to the form"); }
        
        $this->reg->setFormName(str_replace(".","_", $this->event->getName())) ;
        $header = $this->event->getFormHeader() ;
        $header .= $this->event->getFormEvent() ;
        $this->header = $header.$this->header ;
        //$this->footer = $this->footer."</FORM>" ;
        if ($this->getAddSubmit()) {
            if (!empty($this->submit_label)) { 
                $this->footer .= $this->event->getFormFooter($this->getSubmitLabel()) ;
            } else {
                $this->footer .= $this->event->getFormFooter() ;
            }
        }
        $this->setAddSubmit(false);

    }
    
  }

  /**
   * Overwrite default or set events of the form.
   * Usefull for custom event for the form without usingdefault mydb.updateRecord or mydb.addRecord
   * For backward compatibility we check if the level is numerical in case it is a depracate eventaction.
   * If its not numerical we set the event action.
   * @param string $eventname Name of the event
   * @param string $level level of execution for the event
   * @note Renamed it to setFormEvent() and left the deprecat setEvent Until we fix back ward compatibility.
   */


  function setFormEvent($eventname, $level=0) {
      if (is_object($eventname)) {
        $this->event = $eventname;
      } else {
        $this->event->setName($eventname) ;
        if (is_numeric($level)) {
            if ($level > 0) {
                $this->event->setLevel($level);
            }
        } else {
           $this->event->setAction($level) ;
        }
      }
      $this->custom_event = true;
  }

  /** Deprecate, setFormEvent() folowed by setForm() should be used instead **/
  function setEvent($eventname, $eventaction="") {      
      $this->event->setName($eventname) ;
      $this->event->setAction($eventaction) ;
      $header = $this->event->getFormHeader()  ;
      $header .=  $this->event->getFormEvent() ;
      $this->header = $header.$this->header;
      if (!empty($this->submit_label) && $this->getAddSubmit()) {
          $this->footer .= "<div style=\"text-align:right\">".$this->event->getFormFooter($this->getSubmitLabel())."</div>" ;
      } else {
            $this->footer .= $this->event->getFormFooter() ;
      }      
  }


  /**
   * AddEventAction
   * add an event action to the form.
   * It will leave the default add/update event but allow to add a custom event.
   * @param string $eventname Name of the event
   * @param string $level level of execution for the event
   */

  function addEventAction($eventname, $level) {
      $this->event->addEventAction($eventname, $level);
  }

  /**
   * addParam will add hidden variables to the form.
   * It just transfert the param to the event object that will
   * Add them as hidden field or save them is the session 
   * @param string $param_name Name of the param or variable
   * @param string $param_value string content of the parameter
   */

  function addParam($param_name, $param_value) {
      $this->event->addParam($param_name, $param_value);
  }

  /**
   * Overide the original doReport method to include the
   * primarykey management and update or new data for the form.
   *
   * @access public
   * @return String $htmloutput report applyed
   */
  function doReport() {
    $htmloutput = "";
    $htmloutput .= $this->reportfusion($this->values, $this->getHeader()) ;
    //while($record = $this->squery->fetchArray() ) {
    if ($this->nodata) {
       $htmloutput .= $this->reportfusion($this->values, $this->getRow()) ;
    } else {
      if ($this->primarykey && is_resource($this->squery->getResultSet())) {
        $record = $this->squery->fetchArray() ;
        $htmloutput .= $this->reportfusion($record, $this->getRow()) ;
      } else {
        $htmloutput .= $this->reportfusion($this->values, $this->getRow()) ;
      }
    }
    $htmloutput .= $this->reportfusion($this->values, $this->getFooter()) ;
    $htmloutput = str_replace("\[", "[", $htmloutput) ;
    $htmloutput = str_replace("\]", "]", $htmloutput) ;
    return $htmloutput ;
  }

  /**
   *  Overwrite the default event controler.
   *  By default the event controler is eventcontroler.php
   *  To trigger events using a different eventcontroler page or script
   *  you can set the custom event controler.
   *
   *  @param string $page name of the eventcontroler php page.
   */
  
  function setEventControl($page) {
    $this->eventcontrol = $page ;
  }
  function setEventControler($page) {
      $this->setEventControl($page);
  }
  function getEventControl() {
    return $this->eventcontrol ;
  }
  function getEventControler() {
      return $this->getEventControl();
  }
  function getUrlNext() {
    return $this->urlnext ;
  }
  
  /**
   * Set the next url to goto after submit.
   * This will set the goto param in the form event.
   * The goto param is a generic param for events to set 
   * the next display 
   * 
   * @param string $url name of the next page to display after the submit
   */
  
  function setUrlNext($url) {
    $this->urlnext = $url ;
  }
  /*
  function getEventLevel($eventname) {
    return $this->eventlevel[$eventname] ;
  }
  */
  
  /**
   *  Set the Primary key to use to update records.
   *  
   *  If set to 0 it will add a record to the table.
   *  The primary key format is a string with 'primary_key_column_name='record_id_value_to_edit'
   *  it will be added to the where close of the update sql statement.
   *  The content of the $primarykey string need to be a sql compatible condition of a where close.
   * 
   *  @param string $primarykey string containing the update where close statement
   */
  
  function setPrimaryKey($primarykey) {
     return $this->primarykey = $primarykey ;
  }

  function setPrimaryKeyVar($primary_key_var) {
     $this->primarykey_var = $primary_key_var ; 
  }
  function getPrimaryKeyVar() {
     return $this->primarykey_var; 
  }
  /**
   *  Set the form to empty and ready to add record.
   *
   *  This method is called when you want the reportForm to add a record to the
   *  database instead of updating it.
   * 
   *  @return $this->primarykey current value which is at this point 0
   **/
  function setAddRecord() {
    return $this->primarykey = 0 ;
  }

  /**
   *  return The submit label.
   *  
   * @return string submit_label
   * @see setSubmitLabel
   */
  function getSubmitLabel() {
    return $this->submit_label;
  }
  
  /**
   *  Set the submit label.
   *  Set the label for the submit button
   *  by default its empty and is the up to the browser and os decision
   * 
   * @param string submitLabel  label of the submit button
   */
  function setSubmitLabel($submitLabel) {
    $this->submit_label = $submitLabel;
  }
  
  function setAddSubmit($bool) {
    $this->addSubmit = $bool;
  }
  function getAddSubmit() {
    return $this->addSubmit;
  }
  
} /* end class report */

?>
