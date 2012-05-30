<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
   * Report Table Object 
   * @see ReportTable
   * @package RadriaCore
   */
  /**
  *  Object reportTable display a resultset of data in a HTML table
  *
  *  ReportTable extends Report and add methods to auto generate the 
  *  report HTML or XML templates.
  *  You dont need a serialized report in the report directory.
  *  Just with a table name or a sqlQuery you can display the data.
  * 
  *  Since V3 you can load "report templates" they are predefine templates that
  *  will generate the report based on a registry file.
  * 
  *  ReportTable us a template and a result set of data
  *  the registry and table record associate to it.
  *
  *  <b>Example:</b><br>
  *  <code>
  *  <?php
  *      $rt_client = new ReportTable($conx);
  *      $rt_client->setRegistry(“limited_client”);
  *      $rt_client->setSavedQuery(“all.limited.clients”);
  *      $rt_client->setDefault(“”, “default_report”);
  *      $rt_client->setQuery();
  *      $rt_client->execute();
  *  ?>
  *  </code>
  *  This example display the data from the sqlSavedQuery all.limited.clients using 
  *  the "limited_client" registry and report template "default_report".
  *  PS: dont forget to run setQuery() or no query will be executed.
  *
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2007 
  * @version 4.0.0
  * @package RadriaCore
  * @access public
  */

class ReportTable extends Report {
   /**  Event controler URL that process all the events.
   * @var String $eventcontrol
   */
   var $eventcontrol = "eventcontroler.php" ;

   /** position of the record in the query, usefull for the navbarr
    * @var int $recordpos position in num
    */
   var $recordpos =0;
   /** field on which the order need to be done.
    * @var String $orderfield field name
    */
   var $orderfield ;
  /**  Direction on which the order must be done.
    * @var String $orderdir order direction ASC or DESC
    */
   var $orderdir ;

  /** report template
   *  Name of the report containing the template to generate the
   *  default values. the templates are in
   *  @var string report_template template name to generate default report.
   **/
  var $report_template = "";
  /** URL of the page that have a ReportForm object to add or edit records. Use to set the default report.
   *  Used for the default value.    Un used any more.
   *  @var String $formpage url of the page with formpage.
   */
  var $formpage = "form_page.hide.php";
  
  /** Primary key var
    * name of the primary key variable, used by RecordEvent object in report templates
    * to know what primary key to use to update/delete records.
    */
  var $primary_key_var = "";  
  
  /**  Defaults strings for the default table values.
   *  @var String $strPrevious  default value
   *  @access public
   */
  var $strPrevious = "-Previous-";
 /**  Defaults strings for the default table values.
   *  @var String $strStart  default value
   *  @access public
   */
  var $strStart = "First-";
  /**  Defaults strings for the default table values.
   *  @var String $strEnd  default value
   *  @access public
   */
  var $strEnd = "-End";
  /**  Defaults strings for the default table values.
   *  @var String $strNext  default value
   *  @access public
   */
  var $strNext = "-Next-" ;
 /**  Defaults strings for the default table values.
   *  @var String $strUpdate  default value
   *  @access public
   */
  var $strUpdate = "Update" ;
  /**  Defaults strings for the default table values.
   *  @var String $strDelete  default value
   *  @access public
   */
  var $strDelete = "Delete" ;
 /**  Defaults strings for the default table values.
   *  @var String $strAddRecord  default value
   *  @access public
   */
  var $strAddRecord = "Add Record";

  /**
   * Constructor, create a new instance of a ReportTable object.
   * Call super constructor and then add global $recordpos, $orderdir and $orderfield.
   * @global  $recordpos, $orderfield, $orderdir
   * @param object sqlConnect $dbc
   * @param int $id unique id of the report to be reactivate from instanciations.
   * @param sqlConnect $extracon extra connextion used to get from registry information from an other database.
   * @access public
   */
  function ReportTable($dbc, $id="", $extracon=0) {
    //global $recordpos, $orderfield, $orderdir ;
    if (defined("RADRIA_DEFAULT_REPORT_TEMPLATE")) {
        $this->setReportTemplate(RADRIA_DEFAULT_REPORT_TEMPLATE);
    }
    if (defined("RADRIA_EVENT_CONTROLER")) {
        $this->setEventControl(RADRIA_EVENT_CONTROLER);
    }      
    $this->Report($dbc, $id, $extracon) ;
    // This doesn't seems to work with register global off
//     $this->recordpos = $recordpos ;
//     $this->orderfield = $orderfield ;
//     $this->orderdir = $orderdir ;
    if (isset($_GET['recordpos'])) {
        $this->recordpos = (int)$_GET['recordpos'] ;
    } else {
        $this->recordpos = '';
    }
    if (isset($_GET['orderfield'])) {
        $this->orderfield = str_replace(" ", "", $_GET['orderfield']) ;
        if (isset($_GET['orderdir'])) {
            if (eregi("asc", $_GET['orderdir']) || eregi("desc", $_GET['orderdir'])) {
                $this->orderdir = $_GET['orderdir'] ;
            }
        }
    }

    if (defined("RADRIA_LOG_RUN_REPORTTABLE")) {
        $this->setLogRun(RADRIA_LOG_RUN_REPORTTABLE);
    }
    $this->setLog("\n----\n recordpos:".$this->recordpos." \n orderfield: ".$this->orderfield." \n orderdir:".$this->orderdir."\n-----\n");
  }   

  /**
   *  setQuery built the query or get it from the associated saved query
   *  It also sets the orders and recordpostion attribute when browsing the table.
   *  
   *  This method will execute the query.
   *  If the $table param is set a default query that select all the records of that table
   *  will be set and executed.
   *
   * @param String $table Name of the table to generate form from.
   */
  function setQuery($table="") {
    if (empty($table)) {
        $table = $this->table;
    } elseif (empty($this->table)) { $this->table = $table; }
    if(!(strtolower(get_class($this->squery)) == "sqlquery" || strtolower(get_class($this->squery)) == "sqlsavedquery") && !empty($table)) {
        $this->squery = new sqlQuery($this->getDbCon()) ;
        $this->squery->table = $table ; 
        $this->squery->query("SELECT * FROM ".$table);
    }
    if (strlen($this->orderfield) > 0) {
      $this->squery->sql_order = " order by ".$this->orderfield ;
      if (strlen($this->orderdir) > 0) {
        $this->squery->sql_order .= " ".$this->orderdir ;
      }
    }
    if (strlen($this->recordpos) > 0) {
      $this->squery->pos = $this->recordpos ;
    }
    if ($this->max_rows>0) {
      $this->squery->max_rows = $this->max_rows ;
    }
    $this->setLog("\n Report setQuery: Get Class:".get_class($this->squery));
    if (strtolower(get_class($this->squery)) == "sqlquery" || strtolower(get_class($this->squery)) == "sqlsavedquery") {
    //    $this->squery = new sqlQuery($this->getDbCon()) ;
 //      $this->squery->table = $table ; 
  //      $this->squery->query("SELECT * FROM ".$table);
        $this->squery->query() ;

    } else { $this->setError(" No query object to execute"); }
  }

  /**
   * setDefault()
   * Load a default dynamic template for the report.
   *
   * two parameters, 
   * $table  will be used to build a query and to load the registry associated with that table name.
   * $report_template will load and merge the registry information with that report template
   *
   * 
   *
   * You can set a report_template prefix that will look for the report template files :
   * prefix.row.tpl.report.xml, prefix.header.tpl.report.xml, prefix.footer.tpl.report.xml
   * each files will parse with each fields set in the registry object.
   * 
   * if only a table name is provide the registry is optional and the fields will be taken from
   * the result of the query.
   *
   * For version 3.9 changed the get default to not run setQuery() and try to load the registry 
   * From the name of the table provided, or query result set.
   *
   * @author Philippe Lewicki
   * @version 3.9
   * @param String $table Name of the table to generate form from.
   * @param String $report_template prefix for the 3 report templates files 
   * 
   * @return boolean true on success false if an error occured.
   */

  function setDefaultTemplate($report_template="", $table="") {
     $this->setDefault( $table, $report_template);
  }

  function setDefault($table="", $report_template="") { 
    $this->setLog("\n setDefault start");
    global $PHP_SELF, $QUERY_STRING ;  // deprecate should be removed
    $dbc = $this->getDbCon() ;
    //$this->setQuery($table) ;   // PhL 20040123 This may break a lot of stuff 
                                  // PhL 20040125 moved in the no template section.
    if (strlen($report_template)>0) {
        $this->setReportTemplate($report_template);
    } 
    if (empty($table)) { $table = $this->table; } elseif (empty($this->table)) { $this->table = $table; }
    if (empty($table) && is_object($this->squery)) { $table = $this->squery->getTable(); }
//     Commendted on version 3.9 and moved
//     if ( (strlen($this->getReportTemplate()) > 0) && is_object($this->squery)) {
//        if (is_resource($this->squery->getResultSet())) {
//          $table = $this->squery->getTableName();
//        } elseif (strlen($this->squery->getTable())>0) {
//          $table = $this->squery->getTable();
//        }
//     } else { 
//         $this->setQuery($table) ; 
//     }

   // deprecate need to be totaly removed. PHL 20060518
//     if (!$this->getNoData()) {
//         if ( (strlen($this->getReportTemplate()) > 0) && is_object($this->squery)) {
//             if (!is_resource($this->squery->getResultSet()) && !empty($table)) {
//                     $this->setQuery($table) ; 
//             }
//         }
//    }

    $this->setLog("\n Table = ".$table);
    $empty_reg = false;
    if (!is_object($this->reg)) {
        $empty_reg = true;
    } else { 
        if (count($this->reg->fields)==0) {
           $empty_reg = true;
        }
    }
        
    if ($empty_reg) {
       if (strlen($this->squery->qname)>0 && file_exists("registry/savedquery.".$this->squery->qname.".reg.xml")) {
         $this->reg = new Registry($this->getDbCon(), "savedquery.".$this->squery->qname) ;
       } elseif (strlen($table)>0) {
         $this->reg = new Registry($this->getDbCon(), $table) ;
       } elseif (is_object($this->squery) && (strlen($this->getReportTemplate()) > 0)) {
            if (is_resource($this->squery->getResultSet())) {
                $this->reg = new Registry($this->getDbCon()) ;
                $this->reg->registryFromQueryResult($this->squery);
            } elseif (strlen($this->squery->getTable())>0) {  
                $table = $this->squery->getTable();
                $this->reg = new Registry($this->getDbCon(), $table) ;
            }
       }
    }
    
    if (count($this->reg->fields)==0) {
        $this->setError("setDefault: Could not load a registry. Needs a executed query or a table name");
        return false;
    }

    $this->setLog("\n Registry = ".count($this->reg->fields));
    //if ($this->eventcontrol == "") {
    //  $this->setEventControl($_SERVER['PHP_SELF']) ;
    //}

    $a_self = explode("/", $_SERVER['PHP_SELF']);
    $self = $a_self[count($a_self)-1] ;
    if (strlen($_SERVER['QUERY_STRING']) > 0) {
      $self = $self."?".$_SERVER['QUERY_STRING'] ;
    }
    if (strlen($this->getReportTemplate()) > 0) {
    
      //$tpl_fields = Array("tablename", "formpage", "eventcontroler", "fieldname", "fieldlabel", "primarykey");
      if (strlen($this->getPrimaryKeyVar()) > 0)  {
          $values["primarykey"] = $this->getPrimaryKeyVar();
      } else { 
          $values["primarykey"] = "id".$table;
      }
      $values["tablename"] = $table;
      $values["formpage"] = $this->getFormPage();
      $values["eventcontroler"] = $this->getEventControl();
      $this->addValues($values);
      $conx = $this->getDbCon();
      $r_deflt_header = new Report($conx, $this->getReportTemplate().".header.tpl");
      //$r_deflt_header->setField($tpl_fields);
      $r_deflt_header->setValues($this->getValues());
      $r_deflt_header->setNoData();
      $rvalues = Array();
      $rfields = Array();
      $row_values = Array();
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
      $this->setLog("\n ".count($rfields)." fields");
      ksort($rfields);
      foreach($rfields as $regfields) {
        if ($this->applyreg) {
            if (!$regfields->getRData("hidden")) {
                $rvalues["fieldname"] = $regfields->getFieldName();
                if (strlen($regfields->getRData("label")) > 0)  {
                    $rvalues["fieldlabel"] = $regfields->getRData("label");
                } else {
                    $rvalues["fieldlabel"] = $regfields->getFieldName();
                }
                if (strlen($regfields->getRData("dispalign")) > 0)  {
                    $rvalues["aligncel"] = $regfields->getRData("dispalign");
                } else {
                    $rvalues["aligncel"] = "left";
                }           
                $row_values[] = $rvalues;
            }
        } else {
           $rvalues["fieldname"] = $regfields->getFieldName();
           $rvalues["fieldlabel"] = $regfields->getFieldName(); 
           $row_values[] = $rvalues;
        }
      }
      $this->setLog("\n row_values = ".count($row_values));
      $r_deflt_header->setRowValues($row_values);
      $this->setHeader($r_deflt_header->doReport());
      $r_deflt_row = new Report($conx, $this->report_template.".row.tpl");
      $r_deflt_row->setValues($this->getValues());
      $r_deflt_row->setRowValues($row_values);
      $this->setRow( $r_deflt_row->doReport());
      $r_deflt_footer = new Report($conx, $this->report_template.".footer.tpl");
      $r_deflt_footer->setValues($this->getValues());
      $r_deflt_footer->setRowValues($row_values);
      $this->setFooter($r_deflt_footer->doReport());
      $this->field = $this->getField($this->getRow());
    //  $this->setLog("\n------ Default generated Header ------\n".$this->getHeader()) ;
    //  $this->setLog("\n------ Default generated Row ------\n".$this->getRow()) ;
    //  $this->setLog("\n------ Default generated Footer ------\n".$this->getFooter()) ;


    } else {
        $this->setQuery($table) ; 
        if ($table=="") {
            $table = $this->squery->getTableName() ;
          if (strlen($table) == 0) {
            $table = $this->squery->getTable() ;
          }
          $this->squery->table = $table ;
        }        
        if (is_resource($this->squery->getResultSet())) {
            if ( $this->squery->getQueryField() ) {
                $tablefields = $this->squery->getQueryField() ;
            } else {
                $tablefields = $this->squery->getTableField() ;
            }
        }
        $header = "<?php  global \$orderdir, \$orderfield, \$recordpos, \$mydb_num  ;" ;
        $header .= "\n \$revent = new RecordEvent(\"$table\") ;" ;
        $header .= "\n \$revent->setFormPage(\"".$this->getFormPage()."\");";
        $header .= "\n \$oevent = new Event(\"mydb.tableorder\") ;" ;
        $header .= "\n// \$revent->addParam(\"goto\", \"displayreport.php\") ; " ;
        $header .= "\n// \$oevent->addParam(\"goto\", \"displayreport.php\") ; " ;
        $header .= "\n// \$revent->addParam(\"mydb_num\", \$mydb_num) ;" ;
        $header .= "\n \$revent->addParam(\"goto\", \"$self\") ; " ;
        $header .= "\n  \$revent->addParam(\"deleteconfirm\", \"no\"); ";
        $header .= "\n \$oevent->addParam(\"goto\", \"$self\") ; " ;
        $header .= "\n \$oevent->addParam(\"orderfield\", \$orderfield) ;" ;
        $header .= "\n \$oevent->addParam(\"orderdir\", \$orderdir) ; " ;
        $header .= "\n \$oevent->addParam(\"recordpos\", \$recordpos) ; " ;
        $header .= "\n \$oevent->addParam(\"mydb_num\", \$mydb_num) ; " ;
        $header .= "\n \$revent->setEventControler(\"".$this->getEventControl()."\"); ?>" ;
        $header .= "\n<a href=\"<?php echo \$revent->getUrlAdd()?>\">".$this->strAddRecord."</a>" ;
        $header .= "\n<TABLE class=\"tableform\">" ;
        $header .= "\n <TR>" ;
        while(list($key, $value) = each($tablefields)) {
        $header .= "\n  <TD class=\"tabletdheader\">" ;
        $header .= "\n<?php  if (\$orderfield == \"$value\" && \$orderdir==\"ASC\") {\n  \$neworderdir = \"DESC\" ;\n} else {\n\$neworderdir = \"ASC\";\n} " ;
        $header .=  "\$oevent->editParam(\"recordpos\", 0) ; \n\$oevent->editParam(\"orderfield\", \"$value\") ; \n\$oevent->editParam(\"orderdir\", \$neworderdir) ;?>" ;
        $header .= "<a href=\"<?php echo \$oevent->getUrl();?>\">" ;
        if (strlen($this->reg->label["$value"]) > 0) {
            $header .= $this->reg->label["$value"] ;
        } else {
            $header .= $value ;
        }
        $header .= "</a></TD>" ;
        }
        $header .= "\n </TR>" ;
        $this->setHeader($header) ;
        reset($tablefields) ;
        $row = "<TR bgcolor=\"<?php  if (\$bgcolor==\"#C7F5E7\") { \$bgcolor=\"#D0F5C5\"; } else { \$bgcolor=\"#C7F5E7\";} echo \$bgcolor;?>\">"  ;
        while(list($key, $value) = each($tablefields)) {
        $row .= "\n  <TD class=\"tabletdformfield\">[".$value."]</TD>" ;
        }
        $row .= "\n  <TD><a href=\"<?php echo \$revent->getUrlEdit(\"[noreg:id".$table.":]\");?>\">".$this->strUpdate."</a></TD>" ;
        $row .= "\n  <TD><a href=\"<?php echo \$revent->getUrlDelete(\"[noreg:id".$table.":]\") ;?>\" onclick=\"return confirm('Are you sure you want to delete that record');\">".$this->strDelete."</a></TD>" ;
        $row .= "\n</TR>" ;
        $this->setRow($row) ;
        $footer = "</TABLE>" ;
        $totalrows = $this->getTotalRows($table) ;
        if ($this->max_rows > 0 &&  $totalrows > $this->max_rows) {
        //$url = $self."?orderfield=".$this->orderfield."&orderdir=".$this->orderdir ;
        $footer .= "\n<?php  " ;
        $footer .= "\n\$oevent->editParam(\"orderdir\", \$orderdir) ; " ;
        $footer .= "\n\$oevent->editParam(\"orderfield\", \$orderfield) ; " ;
        $footer .= "\n\$oevent->editParam(\"recordpos\", 0) ; " ;
        $footer .= "\n?> " ;
        $footer .= "\n<a href=\"<?php echo \$oevent->getUrl()?>\">".$this->strStart."</A>   " ;
        $footer .= "\n<?php   " ;
        $footer .= "\n  \$prevnum = \$recordpos - \$this->max_rows ; " ;
        $footer .= "\n  \$totalrows = \$this->getTotalRows(\$this->squery->table) ;  " ;
        $footer .= "\n  if(\$prevnum < 0) { \$prevnum = 0 ; } ;  " ;
        $footer .= "\n  \$oevent->editParam(\"recordpos\", \$prevnum) ; " ;
        $footer .= "\n?> " ;
        $footer .= "\n<a href=\"<?php echo \$oevent->getUrl()?>\">".$this->strPrevious."</a> " ;
        $footer .= "\n<?php   " ;
        $footer .= "\n  \$nextnum = \$recordpos + \$this->max_rows ; " ;
        $footer .= "\n  if(\$nextnum >= \$totalrows) { " ;
        $footer .= "\n    \$nextnum = \$totalrows - \$this->max_rows; " ;
        $footer .= "\n  } " ;
        $footer .= "\n  \$oevent->editParam(\"recordpos\", \$nextnum) ; " ;
        $footer .= "\n?> " ;
        $footer .= "\n<a href=\"<?php echo \$oevent->getUrl()?>\">".$this->strNext."</a> " ;
        $footer .= "\n<?php  " ;
        $footer .= "\n  \$lastnum = \$totalrows - \$this->max_rows ; " ;
        $footer .= "\n  \$oevent->editParam(\"recordpos\", \$lastnum) ;  " ;
        $footer .= "\n?>  " ;
        $footer .= "\n<a href=\"<?php echo \$oevent->getUrl();?>\">".$this->strEnd."</A>  " ;
    }
        //echo $footer ;
        $this->setFooter($footer) ;
        $this->field = $this->getField($this->getRow()) ;
    }
    $this->setLog("\n setDefault done");
    return true;
  }

  function setReportTemplate($templateprefix) {
      $this->report_template = $templateprefix;
  }
  function getReportTemplate() {
    return $this->report_template;  
  }
  
  function setEventControl($page) {
    $this->eventcontrol = $page ;
  }
  function getEventControl() {
    return $this->eventcontrol ;
  }
  /**
   * Set the page that will generate the Add or Update form.
   * Some report templates display update and Add links to modify the content of the
   * table. 
   * To auto generate the form a formrecordedit page is needed.
   * @param string $page page name that contains code similare to formrecordedit.php
   */
  function setFormPage($page) {
    $this->formpage = $page;
  }
  /**
   * Return the current form page.
   * @return string $page page name that contains code similare to formrecordedit.php
   * @see setFormPage()
   */  
  function getFormPage() {
    return $this->formpage ;
  }
  
  /**
   * setPrimaryKeyVar()
   * Its the variable name of the Primary of the table.
   * This is needed by the recordEvent in the report templates to set 
   * The primary key to use for editing and deleting records.
   * @param string $primarykeyvar name of the primary key variable.
   */
  
  function setPrimaryKeyVar($primary_key_var) {
      $this->primary_key_var = $primary_key_var;
  }
  
  /**
   * getPrimaryKeyVar()
   * Return the currently set primary key variable name
   * @return string Primary key var name
   */
  function getPrimaryKeyVar() {
      return $this->primary_key_var;
  }
  
} /* end class ReportTable */


?>
