<?php
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
   * sql Save Query Object Object
   * @see sqlSavedQuery
   * @package RadriaCore
   */
  /**
  *  Object sqlSavedQuery to manage persistant queries
  *
  *  Based on sqlQuery is store and restore queries from the Database.
  * They are prepared to be used like an sqlQuery object.
  *
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2007
  * @version 4.0
  * @package RadriaCore
  * @access public
  */

class sqlSavedQuery extends sqlQuery {
  /**  name of the sqlSavedQuery object when serialized in database.
   * @var int $id uniq id.
   */
  var $name ;
  /**  Where statement of the query
   * @var string $qwhere
   */
  var $qwhere ;
  /**  table where persistant sqlSavedQuery are stored.
   * @var string $tbl_query
   */
  var $tbl_query = "savedquery";
  /**  Name of the query
   * @var string $name
   */
  var $qname ;
  /**  SQL statement of the query before replacement of the global var..
   * @var string $query SQL statment
   */
  var $query ;
   /**  Flag to tel if the query is ready to be executed. Mean when all the attributes are defined.
   * @var boolean $queryReady  is query ready to be executedSQL statetment
   */
  var $queryReady = true ;

  /**
   * Constructor sqlSavedQuery
   * Get the query from the database built the SQL query from the global vars.
   *
   * @param object sqlConnect $dbc
   * @param int $id unique id of the sqlSavedQuery to be reactivate from database.
   * @access public
   */

  function sqlSavedQuery($dbc, $name="") {
    //$this->setDisplayErrors(true);
    $this->dbCon = $dbc ;
    if (!is_resource($this->dbCon->getDbConId())) {
        $this->setError("SQLConnect object is not an open database connexion");
    }
    if (defined("RADRIA_LOG_RUN_SQLSAVEDQUERY")) {
        $this->setLogRun(RADRIA_LOG_RUN_SQLSAVEDQUERY);
    }
    if (!empty($name)) {
        if ($this->dbCon->getUseDatabase()) {
            $this->name = $name ;
            $qGetQuery = new sqlQuery($dbc) ;
            $r = $qGetQuery->query("select * from $this->tbl_query where qname='$this->name'") ;
            $qGetQuery->getNumRows() ;
            $infoquery = $qGetQuery->fetch() ;
            $this->qname = $infoquery->qname ;
            $this->sql_query = $infoquery->query ;
            $this->query = $infoquery->query ;
            $this->sql_order = $infoquery->qorder ;
            $this->pos = $infoquery->qpos ;
            $this->table = explode(":", $infoquery->tablenames)  ;
            $qGetQuery->free() ;
        }  else {
            include_once($this->dbCon->getBaseDirectory()."class/XMLBaseLoad.class.php") ;
            include_once($this->dbCon->getBaseDirectory()."class/XMLFlatDataLoad.class.php") ;
            $regfilename1 = $this->dbCon->getBaseDirectory()."/".$this->tbl_query."/".$name.".sq.xml" ;
            $regfilename2 = $this->dbCon->getProjectDirectory()."/".$this->tbl_query."/".$name.".sq.xml" ;
            if (file_exists($regfilename1)) {
                $xmlSQ = new XMLFlatDataLoad() ;
                $xmlSQ->init($regfilename1) ;
            } elseif(file_exists($regfilename2)) {
                $xmlSQ = new XMLFlatDataLoad() ;
                $xmlSQ->init($regfilename2) ;
            }
            if (is_object($xmlSQ)) {
                $xmlSQ->parse() ;
                $this->qname = $xmlSQ->finaldata["QNAME"] ;
            //  $this->sql_query = $xmlSQ->finaldata["QUERY"] ;
                $this->query = $xmlSQ->finaldata["QUERY"]  ;
                $this->sql_order = $xmlSQ->finaldata["QORDER"] ;
                $this->pos = $xmlSQ->finaldata["QPOS"] ;
                $this->table = explode(":", trim($xmlSQ->finaldata["TABLENAMES"]))  ;
            }
        }
        $this->prepareQuery();
        // Not sure this should stay here.
        if (strlen($this->qwhere) > 0 ) {
            if (eregi("where", $this->sql_query)) {
                $this->sql_query .= " AND ".$this->qwhere;
            } else {
                $this->sql_query .= " WHERE ".$this->qwhere;
            }
        }
    }
    return true ;
  }

  /**
   *  setQueryName
   */
 //   function setName($name) {
 //       $this->qname = $name;
 //   }
    function setPosition($pos) {
        $this->pos = $pos;
    }
    function setTable($table) {
        $this->table = $table;
    }
    function setOrder($order) {
        $this->sql_order = $order;
    }

  /**
   * setQuery
   * Manualy set a query if none where set previously.
   *
   */
    function setQuery($sql_query, $table="") {
        $this->query = $sql_query;
        $this->sql_query = $sql_query;
        if (!empty($table)) {
            $this->table = $table;
        }
    }

  /**
   * Prepare query
   * Prepare the query and load the params or variables.
   */

  function prepareQuery($params=Array()) {
    if (count($this->table) == 1) {
      $this->table = $this->table[0] ;
    }
    $this->sql_query = $this->getInitialQuery();
    if (empty($this->sql_query)) {
        $this->setLog("\n prepareQuery did not found a sql query to prepare");
        return false;
    } else {
        $this->queryReady = true;
        $this->setLog("\n looking for variables in:".$this->sql_query);
        while (ereg('\[([^\[]*)\]', $this->sql_query, $matches)) {
            $field = $matches[1] ;
            $this->setLog("\n Found field:".$field."\n");
            if (ereg(":", $field)) {
                $a_paramdefaultvar = explode(":", $field);
                if (function_exists($a_paramdefaultvar[0])) {
                    $fieldvalue = $a_paramdefaultvar[0]($a_paramdefaultvar);
                    $this->setLog("runned function: ".$a_paramdefaultvar[0]);
                }
            } elseif (ereg(";", $field)) {
                $a_paramdefaultvar = explode(";", $field);
                if (function_exists($a_paramdefaultvar[0])) {
                    $fieldvalue = $a_paramdefaultvar[0]($a_paramdefaultvar);
                    $this->setLog("runned function: ".$a_paramdefaultvar[0]);
                }
            } elseif (strrchr($field, ".") !== false) {
                list ($table_name, $field_name) = explode(".", $field);
                $a_paramdefaultvar = Array( "getparam", "eDetail_".$table_name, $field_name); 
                $fieldvalue = getsavedparam($a_paramdefaultvar);
                $this->setLog("runned function: getparam from a table_name.field_name ");
            } elseif(strpos($field, "->") !== false) {
                list ($object_name, $variable_name) = explode("->", $field);
                $variable_name = str_replace("()", "", $variable_name);
                if (is_object($_SESSION[$object_name])) {
                    if (method_exists($_SESSION[$object_name], $variable_name)) {
                        $fieldvalue = $_SESSION[$object_name]->{$variable_name}();
                        $this->setLog("runned function: Get Param values from object ".$object_name."->".$variable_name."=".$fieldvalue." in the session ");
                    } else {
                        $fieldvalue = $_SESSION[$object_name]->{$variable_name};
                        $this->setLog("runned function: Get Param values from object ".$object_name."->".$variable_name."=".$fieldvalue." in the session ");
                    }
                }
                
            } else {
                if (array_key_exists($field, $params)) {
                    $fieldvalue = $params[$field] ;
                    $this->setLog("\n params is :".$field."=".$fieldvalue);
                } elseif (!empty($_SESSION[$field])) {
                    $fieldvalue = $_SESSION[$field] ;
                    $this->setLog("\n From session fieldname: ".$field." - fieldvalue: ".$fieldvalue);
                } elseif (!empty($GLOBALS[$field])) {
                    $fieldvalue = $GLOBALS[$field] ;
                    $this->setLog("\n From global fieldname: ".$field." - fieldvalue: ".$fieldvalue);
                }
            }

            if (!isset($fieldvalue)) { $this->queryReady = false; }
            $this->sql_query = eregi_replace('\['.$field.'\]', strval($fieldvalue), $this->sql_query) ;
            unset($fieldvalue);

        }
        $this->setLog("\n Prepared SQL Query :".$this->sql_query."---");
        return true;
    }

  }

  /**
   * Return the original query string before its replacement with the global vars.
   * @return String $query SQL Query string
   */
  function getInitialQuery() {
    return $this->query ;
  }
  /**
   * Check if the query is ready to be executed.
   * @return boolean $queryReady
   */
  function getQueryReady() {
    return $this->queryReady ;
  }


  /**
   *  serializeToXML
   *
   * This method serialize the current sqlquery to an XML file.
   * It will take the query at its current state and save all its informations
   * in a <name>.sq.xml file in the savedquery directory.
   *
   * @param string $saved_query contains the name of the report to serialize.
   */
  function serializeToXML($saved_query, $table="") {

    if (empty($table)) { $table = $this->table; }

    if (empty($table) && is_resource($this->getResultSet())) {
         $table = $this->squery->getTableName();
    }
   if (empty($table)) {
        $this->setError("Needs a tablename to generate the XML file this is for backward compatibility. Pass the table name as second parameter or run the query before call the method serializeToXML");
        return false;
    }

    $data['qname'] = $this->qname = stripslashes($saved_query);
    $data['tablenames'] = $table;
    $data['qpos'] = $this->pos;
    $data['qorder'] =  $this->sql_order;
    $data['query'] = $this->query;
    $data['idsavedquery'] = "1";
    $dbc = $this->getDbCon();
    $filename = $dbc->getProjectDirectory()."savedquery/".$data['qname'].".sq.xml" ;
    include_once($dbc->getBaseDirectory()."class/XMLBaseLoad.class.php") ;
    include_once($dbc->getBaseDirectory()."class/XMLFlatDataLoad.class.php") ;
    $xmlWriter = new XMLFlatDataLoad() ;

    $xmlWriter->arrayToXML($data, $filename, "savedquery")  ;
    return true;
  }

}
?>