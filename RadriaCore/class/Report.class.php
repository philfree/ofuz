<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
   * Report Object 
   * @see Report
   * @package RadriaCore
   */
   
 /**
  *  Object report used to display a set or record from the database.
  *
  *  Report use a template stored in the report table or XML file and load the
  *  data from the savedquery associate to it.
  *  It then apply the registry for each of the fields found in the report.  
  *  setApplyRegistry to turn on and off of the calls to registry classes.
  *
  *  To save and instance of your report object you can use serializeToXML this 
  *  will create an XML file in the report directory that you can reload as needed.
  *  <b>Example:<b><br>
  *  Load a report called "display client list" and execute it.
  *  <code>
  *  <?php
  *       $r_client = new Report($conx, “display client list”);
  *       $r_client->execute();
  *  ?>
  *  </code>
  *  report/display client list.report.xml is an XML file of a previously serializedToXML report
  *  or just created by hand or a IDE tool.
  * 
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2007 
  * @version 4.0.0
  * @package RadriaCore
  * @access public
  */

class Report extends BaseObject {
   /**  Uniq id of the report object when serialized in database.
   * @var int $id uniq id.
   */
  var $id;
   /**  Array with all the fields that are used in the Report
   * @var Array $field contain all the field name.
   */
  var $field = Array();
   /**  String with the header HTML template
   * @var String $header HTML template.
   */
  var $header ;
   /**  String with the row HTML template
   * @var String $row HTML template.
   */
  var $rowdeljen;
   /**  String with the footer HTML template
   * @var String $footer HTML template.
   */
  var $footer ;
   /**  Registry object for the table for that report.
   * @var Registry $reg Registry object.
   */
  var $reg ;
   /**  SavedQuery object with the query associate with this report.
   * @var sqlSavedQuery $squery Savedquery object.
   */
  var $squery ;
   /**  Database connexion object.
   * @var sqlConnect $dbCon Database connexion.
   */
  var $dbCon ;
   /**  Table name where the report will apply.
   * @var String $table Table name.
   */
  var $table ;
   /**  Table  name where the report objects are stored. (serialized)
   * @var String $tbl_report  report table name.
   */
  var $tbl_report = "report";
   /**  flag Apply the form with no data
   * @var boolean $nodata to apply the form with no data.
   */
  var $nodata = false;

    /** Array with the values to merge in header, row, footer when there is no records.
   * @var array $values to apply to the report with no data.
   */
   var $values = array ();

    /** Array with the values of the data in the xml file, key = field name in capital letters
   * @var array $xmldata key on the field name contain all the original data from the xml file.
   */
   var $xmldata;

   /** Flag to set on and off the application of the registry on a report
   */
   var $applyreg = true ;

   /** string that describe on which context to apply it for the registry
   */
   var $context = "";

   /** description string that contains text documentation on that report
   */
   var $description = "";
   
   /** Number of record per rows. not used in this version.
    * @var int $recprow
    */
   var $recprow = 0 ;
   /** Numbers of rows to display
    * @var int $max_rows limit value in the query 0 = infinite
    */
   var $max_rows = 0 ;  
   
  /**
   * Constructor, create a new instance of a Report object.
   * It creates an empty report object if only $dbc parameter is given.
   * if the id parameter is pass it will load the Report from the $dbc database.
   * if the extracon is pass it will load the Report from the $dbc database and load the registry from $extracon.
   * Set the default contect to "Disp", in case no context has been previously set.
   * 
   * @param object sqlConnect $dbc
   * @param int $id unique id of the report to be reactivate from database.
   * @param sqlConnect $extracon extra connextion used to get from registry information from an other database.
   * @access public
   */
  function Report($dbc, $id="", $extracon=0) {
    if (defined("RADRIA_LOG_RUN_REPORT")) {
            $this->setLogRun(RADRIA_LOG_RUN_REPORT);
    }
    $this->dbCon = $dbc ;
    if ($this->getContext() == "") {
        $this->setContext("Disp");
    }
    if (strtolower(get_class($dbc)) != "sqlconnect") {
        $this->setError("<b>Missing Argument</b> : Database Connexion is not an sqlConnect object. : new Report(\$sqlconnectobject, \"report name\")") ;
    }
   if (!$extracon) { $extracon = $dbc; } ;
    if (!empty($id)) {
      $this->id = $id ;
      if ($this->dbCon->getUseDatabase()) {
        if (!is_resource($this->dbCon->getDbConId())) {
            $this->setError("<b>Database Connexion Error : The connexion object as lost is link to the database. Check the login and password and make sure your sqlConnect (\$conx) is started") ;
        }
        $qGetReport = new sqlQuery ;
        $qGetReport->query("select * from $this->tbl_report where name='$id'", $this->dbCon) ;
        $oreport = $qGetReport->fetch() ;
        if (empty($this->header)) { $this->setHeader($oreport->header); } //DS//
        if (empty($this->row)) { $this->setRow($oreport->row) ; } //DS//
        if (empty($this->footer)) { $this->setFooter($oreport->footer); } //DS//
        if ($this->recprow == 0) { $this->setRecPerRow($oreport->recprow); }
        if ($this->max_rows == 0) { $this->setMaxRows($oreport->numrow); }
        if ($oreport->idquery != "0") {
          $this->squery = new sqlSavedQuery($dbc, $oreport->idquery);
          $this->table =  $this->squery->getTable() ;
          if ($oreport->numrow) { $this->squery->max_rows = $this->max_rows; }
          if ($this->squery->getQueryReady()) {
            $this->squery->setDbCon($extracon) ;
            $this->squery->query() ;
          }
          $this->reg = new Registry($extracon, $this->table) ;
        } else { $this->nodata = true; }
        return $oreport ;
      } else {
       // if (!class_exists("xmlbaseload")) {
            include_once($this->dbCon->getBaseDirectory()."class/XMLBaseLoad.class.php") ;
            include_once($this->dbCon->getBaseDirectory()."class/XMLFlatDataLoad.class.php") ;
      //  }
        $filename1 = $this->dbCon->getBaseDirectory()."/".$this->tbl_report."/".$id.".".$this->tbl_report.".xml" ;
        $filename2 = $this->dbCon->getProjectDirectory()."/".$this->tbl_report."/".$id.".".$this->tbl_report.".xml" ;
        if (file_exists($filename1)) {
          $xmlSQ = new XMLFlatDataLoad() ;
          $xmlSQ->init($filename1) ;
        }elseif(file_exists($filename2)) {
          $xmlR = new XMLFlatDataLoad() ;
          $xmlR->init($filename2) ;
          //echo $filename2 ;
        } else {
            $this->setError("Missing file : Couldn't find the report file. Check the report name") ;
        }
        if (is_object($xmlR)) {
          $xmlR->parse() ;
          $this->xmldata = $xmlR->finaldata ;
          $this->description = $xmlR->finaldata["DESCR"]; 
          $this->setHeader($xmlR->finaldata["HEADER"]) ;
          $this->setFooter($xmlR->finaldata["FOOTER"]) ;
          $this->setRow($xmlR->finaldata["ROW"])  ;
          $this->setRecPerRow($xmlR->finaldata["RECPROW"]) ;
          $this->setMaxRows($xmlR->finaldata["NUMROW"]) ;
       //   if ($xmlR->finaldata["IDQUERY"] != "0" || strlen($xmlR->finaldata["IDQUERY"]) > 0) {
          if ($xmlR->finaldata["IDQUERY"] != "0") {
            if (!is_resource($this->dbCon->getDbConId())) {
                $this->setError("<b>Database Connexion Error : The connexion object as lost is link to the database. Check the login and password and make sure your sqlConnect (\$conx) is started") ;
            }
            $this->squery = new sqlSavedQuery($this->dbCon, $xmlR->finaldata["IDQUERY"]);
            $this->table =  $this->squery->getTable() ;
            if ($this->max_rows) { $this->squery->max_rows = $this->max_rows; }
            if ($this->squery->getQueryReady()) {
              $this->squery->setDbCon($extracon) ;
              $this->squery->query() ;
            }
            $this->reg = new Registry($extracon, $this->table) ;
          } else { $this->nodata = true; $this->reg = new Registry($extracon) ; }
        }
      }
      if (is_array($this->getField($this->row))) {
        $this->field = array_merge($this->getField($this->row), $this->field);
      }
      if (is_array($this->getField($this->getHeader()))) {
        $this->field = array_merge($this->getField($this->getHeader()),$this->field);
      }
      if (is_array($this->getField($this->getFooter()))) { 
        $this->field = array_merge($this->getField($this->getFooter()), $this->field) ;
      }
    } else {
      $this->setHeader("");
      $this->setRow("");
      $this->setFooter("");
      $this->recprow = 0;
      $this->max_rows = 0 ;

      return true ;
    }
  }

  /**
   * Load the field in the field attribute from the HTML template.
   * get Table Field could be used instead but it will not get the
   * extra fields and multiple tables fields
   * @param String $template HTML template (row, header, footer) where there is fields to be used
   * @access public
   * @return Array $fields indexed on the field name.
   */
  function getField($template) {
    $template = str_replace("\[", "{&{", $template);
    $template = str_replace("\]", "}#}", $template);
   // while (ereg('\[([^\[]*)\]', $template, $fieldmatches)) {
    while (preg_match('/\[([^\[]*)\]/', $template, $fieldmatches)) {   
      $fields[] = $fieldmatches[1];
      $template = str_replace($fieldmatches[0], "", $template) ;
    }    
    return $fields ;
  }

  
  /**
   * Merge the HMTL templates row with the registry.
   * its uses the field attribute and registry object to fields by there data for this record.
   * It also call the functions from libReport and execute them.
   *
   * @param Array $row indexed by field name and contains fields value for this row
   * @param String $newrow row HTML template where field will be replaced by values.
   * @access public
   * @return String $newrow with fields replaced by there value..
   */
  function reportfusion($row, $newrow) {
    if (!(is_array($row))) {
      $row = array(1=>"", 2=>"") ;
    }
    $dbc = $this->getDbCon() ;
    $nbrfield = count($this->field) ;
    for($i=0; $i<$nbrfield; $i++) {
      $field = $this->field[$i] ;
      $reportdata = explode(":", $field) ;
      $nbrdata = count($reportdata);
      if ($nbrdata == 1) {
        if ($this->applyreg) {
          $replacedata = $this->reg->apply($this->context, $reportdata[0],  $row[$reportdata[0]]) ;
        } else {
          $replacedata = $row[$reportdata[0]] ;
        }
        $replacedata = str_replace("]", "\]", $replacedata) ;
        $replacedata = str_replace("[", "\[", $replacedata) ;
        $newrow = str_replace('['.$reportdata[0].']', $replacedata , $newrow) ;
            //echo "<br><br>".$reportdata[0].": contenue : <br>||".htmlentities($newrow)."||" ;
      } elseif ($nbrdata == 2) {
        list ($namefield, $r) = explode(":", $field) ;
        if ($this->applyreg) {
          $replacedata = $this->reg->apply($this->context, $namefield, $row[$r][$namefield]) ;
        } else {
          $replacedata = $row[$r][$namefield] ;
        }
        $newrow = str_replace('['.$field.']', $replacedata , $newrow) ;
      } else {
        //    echo "fonction: ".$field."<br>" ;
        //    echo "- ".$reportdata[0]($reportdata, $row, $dbc) ;
        $newrow = str_replace('['.$field.']',  $reportdata[0]($reportdata, $row, $dbc), $newrow) ;
      }
    }
    //$newrow = stripslashes($newrow)  ;
  // $newrow = str_replace("\[", "[", $newrow) ;
 //  $newrow = str_replace("\]", "]", $newrow) ;
    return $newrow ;
  }

  /**
   * Execute the Report for each of the HTML template.
   * returns a string with report gererated from data and registry.
   *
   * @access public
   * @return String $htmloutput report applyed
   */
  function doReport() {
    $this->setlog("\n doReport() start");
    $htmloutput = "";
    $htmloutput .= $this->reportfusion($this->values, $this->getHeader()) ;
    if ($this->nodata) {
      if (is_array($this->row_values)) {
        $this->setLog("\n nodata with row_values array");
        reset($this->row_values);
        if($this->recprow > 1) {
            $this->setLog("\n this->recprow:".$this->recprow);
            $numrec = 1 ;
            $dispnumrows = 1;
            $multiplrecord = array() ;
            $anewfield = array()  ;
            $stilrecords = 1 ;
            $this->setLog("\n stilrecords:".$stilrecords." dispnumrows:".$dispnumrows);
            while($stilrecords && (($this->max_rows > $dispnumrows) || (!$this->max_rows))) {
                for ($r=0 ; $r<$this->recprow; $r++) {
                    $record[$numrec] = current($this->row_values);
                    $numrec++ ;
                    $dispnumrows++;
                    if(  next($this->row_values) ) {
                        $stilrecords = 1 ;
                    } else {
                        $stilrecords = 0 ;
                    }
                }
                $htmloutput .= $this->reportfusion($record, $this->getRow()) ;
                $numrec = 1 ;
            }
        } else {
            foreach($this->row_values as $values) {
                if (is_array($values)) {
                    $htmloutput .= $this->reportfusion($values, $this->getRow()) ;
                }
            }
        }
      } else {
        $htmloutput .= $this->reportfusion($this->values, $this->getRow()) ;
      }
    } elseif($this->recprow) {
      $numrec = 1 ;
      $dispnumrows = 1;
      $multiplrecord = array() ;
      $anewfield = array()  ;
      $stilrecords = 1 ;
      while($stilrecords && (($this->max_rows > $dispnumrows) || (!$this->max_rows))) {
        for ($r=0 ; $r<$this->recprow; $r++) {
          if( $record[$numrec] = $this->squery->fetchArray() ) {
            $stilrecords = 1 ;
          } else {
            $stilrecords = 0 ;
          }
          $numrec++ ;
          $dispnumrows++;
        }
        $htmloutput .= $this->reportfusion($record, $this->getRow()) ;
        $numrec = 1 ;
      }    
    } else {
      while($record = $this->squery->fetchArray() ) {
        if (is_array($record)) {     
          $htmloutput .= $this->reportfusion($record, $this->getRow()) ;
        };
      }
    }
    $htmloutput .= $this->reportfusion($this->values, $this->getFooter()) ;
    $htmloutput = str_replace("\[", "[", $htmloutput) ;
    $htmloutput = str_replace("\]", "]", $htmloutput) ;
    return $htmloutput ;    
  }

  /**
   *  Execute the report and echo it to the output.
   * it doesn't execut the php scripte in the report.
   *
   * @access public
   * @return String $htmloutput report applyed
   */
  function display() {
    echo $this->doReport() ;
  }
  /**
   *  Execute the report, execute all php code and echo it to the output
   *
   * @access public
   */
  function execute() {
      eval("?>".$this->doReport()."<?php " ) ;
  }
  
  /** 
   * Execute the report and return a String without displaying anything
   *
   * @access public
   * @return String HTML or XML  with all the php executed
   */
  function executeToString() {
      ob_start() ;
      $this->execute();
      $htmloutput = ob_get_contents();
      ob_end_clean();
      return $htmloutput;
  }
  
  
  /**
   * Return the databse connexion of the report
   * 
   * @access public
   * @return sqlConnect object with current database connexion object
   */
  
  function getDbCon() {
    return $this->dbCon ;
  }

  
 /**
  * Return the total number of rows for the current query.
  * It skips the Order and Limit close set in the sqlSavedQuery
  *
  * @access public
  * @return integer with the number of rows
  */
  function getTotalRows() {
    $qtotal = new sqlQuery($this->getdbCon()) ;
    $qtotal->query($this->squery->sql_query) ;
    $totalrows = $qtotal->getNumRows() ;
    $qtotal->free() ;
    return $totalrows ;
  }

  
  /** 
   *  serializeToXML
   *
   * This method serialize the current report to an XML file.
   * It will take the report at its current state and save all its informations
   * in a report.xml file in the report directory.
   * This will record, registry name, sqlSavedQuery name and generate the HTML from the result of the report template execution.
   * 
   * @param string $reportname contains the name of the report to serialize.
   */
  
  function serializeToXML($reportname) {
    $data['name'] = stripslashes($reportname) ;
    if ($this->GetContext() == "Form") {
        $report_type = "form";
    } else {
        $report_type = "report";
    }
    $dbc = $this->getDbCon();
    $filename = $dbc->getProjectDirectory().$report_type."/".$data['name'].".".$report_type.".xml" ;
    include_once($dbc->getBaseDirectory()."class/XMLBaseLoad.class.php") ;
    include_once($dbc->getBaseDirectory()."class/XMLFlatDataLoad.class.php") ;
    $xmlWriter = new XMLFlatDataLoad() ;
    if (!empty($this->id)) {
    $data['idreport'] =  $this->id ;
    } else { $this->id = 555; }
    $data['descr'] = $this->description ;
    $data['numrow'] = $this->getMaxRows();
    if (is_object($this->squery)) {
        $data['idquery'] = $this->squery->qname ;
    } else {
        $data['idquery'] = 0;
    }
    $data['header'] =  $this->getHeader() ;
    $data['row'] =  $this->getRow() ;
    $data['footer'] = $this->getFooter() ;
    $data['recprow'] = $this->getRecPerRow() ;
    $xmlWriter->arrayToXML($data, $filename, $report_type)  ;
      
  }
  
  
  /**
   *  setHeader
   *  Set the header, by default it concatenate the headers
   *  to Reset the header just call the method with no arguments
   *  Header contant HTML or XML tags mixts with PHP and database fields name [fieldname]
   * 
   *  @param string $header with the content of the header to set.
   */

  function setHeader($header="") {
    if ($header=="") {
      $this->header = "";
    } elseif (!empty($this->header)) {
      $this->header .= $header ;
    } else {
      $this->header = $header ;
    }
  }
  
  /**
   * Return the header of that report.
   *
   * @return string with the content of the header
   */
  function getHeader() {
    return $this->header ;
  }
  /**
   *  setRow
   *  Set the row, this set the row HTML or XML template of the report.
   *  The row template will be parsed and executed for each records from the 
   *  query associate with the report. 
   *  It doesn't concatenate like with SetHeader. 
   *  Row contant HTML or XML tags mixts with PHP and database fields name [fieldname]
   * 
   *  @param string $row with the content of the row to be set.
   *  @see getRow(), setHeader()
   */ 
  function setRow($row) {
     $this->row = $row ;
  }
  /**
   *  getRow
   *  Return the current content of the report's row.
   *  The row template will be parsed and executed for each records from the 
   *  query associate with the report. 
   *  Row contant HTML or XML tags mixts with PHP and database fields name [fieldname]
   * 
   *  @return string $row with the content of the row.
   *  @see setRow()
   */   
  function getRow() {
    return $this->row ;
  }
  /**
   *  Get Records Per Row
   *  Return the number of Records per row previously set.
   *  Look at setRecPerRow() for more details.
   *
   *  @return integer $rows number of records per row
   *  @see setRecPerRow()
   */       
  function getRecPerRow() {
      return $this->recprow;
  }
  /**
   *  Set Records Per Row
   *  Its possible to set multiples records for a row.
   *  If you set it to 3 records per row, then the report Row template will be
   *  apply for 3 rows at the time.
   *  Row contant HTML or XML tags mixts with PHP.
   *  The database fields names needs a special notation: [fieldname:recordnum]
   *  recordnum start with 1. to the number of records per row set.<br>
   *  Example code for the Row:
   *  <code>
   *  <tr><td>[thumbnail_picture:1]</td><td>[thumbnail_picture:2]</td></tr>
   *  </code>
   *  Will display 2 records per rows.
   *  The benefits its the uses of TABLE like tags in HTML and XML structure where
   *  it make the template more readable if we have multiple records per rows. 
   * 
   *  @param integer $rows number of records to use in each row
   *  @see setRow()
   */     
  function setRecPerRow($rows) {
      $this->recprow = $rows;
  }
  
  /** 
   * Get the Maximum number or rows to display per page.
   * This is in Report Class for historical reason but should realy be in ReportTable Class.
   * It returns the number of rows from the sqlQuery results to show per page.
   * @return integer max_rows the maximum number of rows per page.
   * @see setMaxRows()
   */    
  function getMaxRows() {
   return $this->max_rows;
  }
  
  /** 
   * Set the Maximum number or rows to display per page.
   * This parameter will allow you to limit the number of records display perpages.
   * If used in a ReportTable navigation links will be automaticaly generated to let
   * you browse through the different pages.<br> 
   * This is in Report Class for historical reason but should realy be in ReportTable Class.<br>
   * It will set the max Number of rows to the Report Class and if the squery object is a 
   * sqlSavedQuery or a sqlQuery then set it to them to.
   * 
   * @param integer $rows the maximum number of rows per page.
   * @see getMaxRows()
   */
  function setMaxRows($rows) {
      $this->max_rows = $rows;
      if (is_object($this->squery)) {
          if (get_class($this->squery) == "sqlsavedquery" || (get_class($this->squery) == "sqlquery")) {
              $this->squery->setMaxRows($rows);
          }
      }
  }
  
  /** 
   *  setFooter
   *  Set the footer, by default it concatenate the headers
   *  to Reset the header just call the method with no arguments
   * 
   *  @param string $footer contain the footer of the report
   */
  function setFooter($footer="") {
    if ($footer=="") {
      $this->footer = "";
    } elseif (!empty($this->footer)) {
      $this->footer .= $footer ;
    } else {
      $this->footer = $footer ;
    }
  }
  function getFooter() {
    return $this->footer ;
  }
  function setNoData($bool=1) {
    $this->nodata = $bool;
  }
  function getNoData() {
    return $this->nodata ;
  }
  
  /** 
   * set a query to report.
   * Will overwrite the default saved query if one is set.
   * 
   * @param mixte string sqlSavedquery $squeryname name of the sqlSavedQuery or an sqlSavedQuery object
   * @param sqlConnect connexion object to use to load that query.
   */
  function setSavedQuery($squeryname, $extracon=0) {
      if (is_object($squeryname)) {
          $this->squery = $squeryname;
      } else {
          if (is_resource($extracon)) {
              $this->squery = new sqlSavedQuery($extracon, $squeryname);
          } else {
              $this->squery = new sqlSavedQuery($this->getDbCon(), $squeryname);
          }
          if (!$this->getNoData()) { $this->setNoData(false); }
          //  $this->squery->query();  tempting but no.          
      }
      $this->table =  $this->squery->getTable() ;
  }
  
  /**
   * Return value of field from the XML report file.
   * 
   * @param string $fieldname  name of the field to retrieve information from
   */
  
  function getXMLData($fieldname) {
    $fieldname = strtoupper($fieldname) ;
    return $this->xmldata[$fieldname] ;
  }
  
  /** 
   * Set a registry for that report
   * Will instantiate a new Registry object
   * @param mixte string Registry $regname Registry name or Registry object
   * @param sqlConnect connexion object to use to load that query.
   */
  function setRegistry($regname, $extrcon=0) {
      if (is_object($regname)) {
          $this->reg = $regname;
      } else {
          if (is_resource($extracon)) {
              $this->reg = new Registry($extracon, $regname);
          } else {
              $this->reg = new Registry($this->getDbCon(), $regname);
          }
      }
  }
  
  /**
   *  Alias to prepare the new nomenclature and class/method names
   * @see setRegistry()
   */
  function setFields($regname, $extrcon=0) {
      $this->setRegistry($regname, $extrcon);
  }
  
  /**
   * Set the report to apply the registry or not.
   * if set to false the report will not apply the registry and just display the
   * plain content of the database.
   * @param boolean $bool true or false
   */
  function setApplyRegistry($bool) {
    $this->applyreg = $bool ;
  }
  
  /** 
   * Set values is used to add values / content to a report that as not sqlSavedQuery set.
   * Its is used only for one row of data.
   * Values are parsed in the header, row and footer.
   * @param array $values array of values with key as fields: $values[fieldname]=$fieldvalue
   */
  function setValues($values) {
    $this->values = $values ; 
  }
  
    /**
     * Add an additional array of values to existing values
     * @param Array value to add format: fieldname => value
     * @access public
     */ 
  function addValues($values) {
    // Experiment to find a way to pass params to the report template.
    //$this->values = array_merge($this->values, $values) ;
    $this->values = array_merge($values, $this->values) ;
  }
  
 /**
  * Add or edit a value to the current list of values
  */
  function setValue($valuekey, $value) {
      $this->values[$valuekey] = $value;  
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
   * setRowValues will set values for multiple rows of data.
   * @param array $row_values is a multidimentional array $row_values[row][fieldname]=fieldvalue;
   */
  function setRowValues($row_values) {
    $this->row_values = $row_values;
  }

  /** 
   * setField for the report
   * Used to overwrite the default fields returned by getField()
   * @param array $fields that contains all the feilds name.
   */
  function setField($fields) {
    $this->field = $fields;
  }
  
  /**
   * Set context change the context on which the registry will be applyed for that Report
   * @param string $context name of the context, usualy : Disp or Form
   */
  function setContext($context) {
      $this->context = $context;
  }
  function getContext() {
      return $this->context;
  }
  
  /**
   * Set Table used to set the table of a report for ReportForm and ReportTable 
   * When the table name cannot be found.
   * @param $tablename name of the table
   */
  function setTable($tablename) {
      $this->table = $tablename;
  }
  
  /**
   * Get table return the name of the table for that report
   * @return return the name of the table associate with the report.
   */
  function getTable() {
      return $this->table;
  }
  
} /* end class report */

?>
