<?php
namespace RadriaCore\Radria\odbc;
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
   /**
    *  Abstract Database query in MyDB for ODBC.
    *
    *  It is used to abstract query  and other Database access.
    *  Curently PostgreSQL, MySQL and ODBC are supported.
    *  For all the MyDB application there is basic requirement in the
    *  table structure. All the table must have an integer not null with autoincrement and primary key with
    *  the following name id<tableName> and type.
    *  To keep the compatibility with postgreSQL the insert queries must include the list of fields prior to the values.
    *  Exemple : 
    *  <code>
    *  insert into table1 (field1, field2, field3) values ( 'valuefield1', 'valuefield2', 'valuefield3')
    *  </code>
    *  Avoid using : 
    *  <code>
    *    insert into table1 ('', 'valuefield1', ''. valuedield3), the '' are not inserting the default value in postgreSQL
    *  </code>
    *  but an empty string.
    *
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @version 3.0.0
    * @package RadriaCoreODBC
    * @access public
    */

use RadriaCore\Radria\BaseObject;
    
Class SqlQuery extends BaseObject {
  /**  Name of the table used in the query, it can be an array for multiple tables.
   * @var String $table
   */
  var $table = "";

  /**  SQL query string  that is going to be executed.
   * @var String $sql_query
   */
  var $sql_query ;

  /**  Order sequence of the SQL Query, exemple.
   * @var string $sql_order
   */
  var $sql_order ;

  /**  Position of the record, for the limit sequence of the SQL Query.
   * @var integer $pos
   */
  var $pos = 0 ;

  /**  Number maximum of rows to display, for the limit sequence of the SQL Query.
   * @var string $max_rows
   */
  var $max_rows ;

  /**  Number of rows return by the execution of the SQL Query.
   * @var integer $num_rows
   */
  var $num_rows;

  /**  Server number, deprecate var was use for compatibility with phpmyadmin .
   * @var String $server
   * @deprecated
   */
  var $server ="0";

   /**  Result set of the executed SQL Query.
   * @var ResultSet $result
   * @private
   */
  var $result ;

   /**  Unique id of the last inserted record.
   * @var integer $insert_id
   * @private
   */
  var $insert_id ;

   /**  Database connexion object used to execute the query.
   * @var sqlConnect $dbCon
   */
  var $dbCon ;          /* dbConection object associate with this query */

   /**  Position of the record in the result set.
   * @var integer $cursor
   */
  var $cursor ;

   /**  MetaData about the SQL Query, table name, fields.
   * @var array $metadata
   * @access public
   */
  var $metadata ;

   /**  Data from the restult of a fetch or fetchArray
   * @var mixte $data
   * @access public
   */
  var $data ;
    /**
   * Constructor, create a new instance of a sqlQuery.
   * @access public
   */
//  function sqlQuery() {
//    $this->objDisplayErrors = false ;
//    return true ;
//  }

    /**
   * Constructor, create a new instance of a sqlQuery with database connection
   * and query string.
   * @param object sqlConnect $dbCon
   * @param string $sql   String with the SQL Query.
   * @access public
   */
  function sqlQuery($dbCon=0, $sql="") {
    $this->objDisplayErrors = false ;
    if (defined("RADRIA_LOG_RUN_SQLQUERY")) {
        $this->setLogRun(RADRIA_LOG_RUN_SQLQUERY);
    }
    if ($dbCon <> 0) {
      $this->dbCon = $dbCon ;
    }
    if (!is_resource($this->dbCon->id)) {
      $this->setError("Query Error: No open or valid connexion has been provide to execute the query. ") ;
      return false;
    }
    if (strlen($sql)>0) {
      $this->sql_query = $sql ;
    }
    return true;
  }

    /**
   * Return the numbers of row for the executed SQLQuery.
   * @param ResultSet $result
   * @return integer num_rows
   * @access public
   */
  function getNumRows($result = 0) {
  //  if (!((isset($this->num_rows) || ($this->num_rows > 0)))) {
        if (is_resource($result)) {
          $this->num_rows = odbc_num_rows($result) ;
        } elseif(is_resource($this->result)) {
          $this->num_rows = odbc_num_rows($this->result) ;
        } else {
          $this->num_rows = 0 ;
        }       
   // }
    return $this->num_rows ;
  }

  /**
   * Execute an sqlQuery.
   *
   * Execute a query the query string and database connexion object need
   * to be passe has parameters or be previously define.
   *
   * @param string $sql   String with the SQL Query.
   * @param object sqlConnect $dbCon   Connexion object if not previously define in the contructor.
   * @return ResultSet $rquery
   * @access public
   */
  function query($sql = "", $dbCon =0) {
    if ($dbCon != 0) {
      $this->dbCon = $dbCon ;
    }
    if (strlen($sql)>0) {
      $this->sql_query = trim($sql) ;
    }
    if (!is_resource($this->dbCon->id)) {
      $this->setError("Query Error: No open or valid connexion has been provide to execute the query: ".$this->sql_query) ;
    }
    if ($this->sql_query == "") {
      if (is_array($this->table)) {
        reset($this->table) ;
        $this->sql_query = "select * from " ;
        while (list($key, $table) = each($this->table)) {
          $this->sql_query .= $table."," ;
        }
        $this->sql_query = ereg_replace(",$", "", $this->sql_query) ;
      } else {
        $this->sql_query= "select * from $this->table" ;
      }
    }
    if ($this->max_rows) {
      if (!$this->pos) { $this->pos = 0 ; }
      $qpos = " limit ".$this->pos.", ".$this->max_rows ;
    } else {
      if (!$this->pos) { $this->pos = "" ; }
      $qpos = $this->pos;
    }
    if ($this->dbCon->getAllwaysSelectDb()) {
      $this->dbCon->setDatabase($this->dbCon->getDatabase()) ;
    }
    $rquery = odbc_exec($this->dbCon->id, "$this->sql_query $this->sql_order $qpos");
    if (!is_resource($rquery)) {
        $sqlerror = odbc_errormsg() ;
        if ($sqlerror) {
          $this->setError("<b>SQL Query Error :</b>".$sqlerror." ($this->sql_query $this->sql_order $qpos)") ;
        }
    }
    if (!$this->max_rows) {
      $this->num_rows = @odbc_num_rows($rquery) ;
    }
    $this->insert_id = 0;
    $this->result = $rquery ;
    $this->cursor = 0 ;
    if ($this->dbCon->getBackupSync()) {
        if (eregi("^alter", $this->sql_query)
         || eregi("^create", $this->sql_query)
         || eregi("^drop", $this->sql_query)) {
            if ($this->dbCon->getUseDatabase()) {
                $qInsSync = "insert into ".$this->dbCon->getTableBackupSync()." ( actiontime, sqlstatement, dbname) values ( '".time()."', '".addslashes($this->sql_query)."', '".$this->dbCon->db."') " ;
                $rquery = odbc_exec($this->dbCon->id, $qInsSync);
            } else {
                $file = $this->dbCon->getProjectDirectory()."/".$this->dbCon->getTableBackupSync().".struct.sql" ;
                $fp = fopen($file, "a") ;
                $syncquery = $this->sql_query.";\n" ;
                fwrite($fp, $syncquery, strlen($syncquery)) ;
                fclose($fp) ;
            }
          }
        if (eregi("^insert", $this->sql_query)
         || eregi("^update", $this->sql_query)
         || eregi("^delete", $this->sql_query)) {
            if ($this->dbCon->getUseDatabase()) {
                $qInsSync = "insert into ".$this->dbCon->getTableBackupSync()." ( actiontime, sqlstatement, dbname) values ( '".time()."', '".addslashes($this->sql_query)."', '".$this->dbCon->db."') " ;
                $rquery = odbc_exec($this->dbCon->id, $qInsSync);
            } else {
                $file = $this->dbCon->getProjectDirectory()."/".$this->dbCon->getTableBackupSync().".data.sql" ;
                $fp = fopen($file, "a") ;
                $syncquery = $this->sql_query.";\n" ;
                fwrite($fp, $syncquery, strlen($syncquery)) ;
                fclose($fp) ;
            }
          }
    }
    return $rquery ;
  }

  /**
   * Return uniq id from the last insert,
   *
   * This is difficult to support because their is no odbc function to get_last_insert_id
   * Then each Database has its own way to support it. 
   * The default is with "@@IDENTITY", used on MSSQL server. 
   *
   *
   * @param string $table  name of the table with the sequence
   * @param string $field name of the primary key used for the sequence
   *
   * @return integer insert_id
   */
  function getInsertId($table="", $field="") {
     if (strlen($table) > 0) { $this->setTable($table); }
     if (strlen($this->getTable())>0 && strlen($field) > 0) {
        //$result = odbc_exec("SELECT @@IDENTITY");  this could work for sql server but didn't try
        $result = odbc_exec($this->dbCon->id, "SELECT MAX(".$field.") AS inserid FROM ".$table);
        $insert_id = odbc_result($result, 1);
        if (!($this->insert_id > 0)) {
            $this->insert_id = $insert_id[0] ;
        }
        return $this->insert_id ;
    } else {
        $this->setError("sqlQuery, ODBC, getInsertID requires param \$table and param \$field");
        return 0;
    }
  }

  /**
   * Catch and return an error string from the last Error.
   * @return string Error description
   * @access public
   */
  function getError() {
    return odbc_errormsg() ;
  }

  /**
   *  return the content data of a record from a result set.
   *
   *  Return the data of a record in the form of an object where all fields are vars.
   *  It use the result set of a previously executed query.
   *  It move the cursor of the ResultSet to the next record
   *
   * @param ResultSet $result
   * @return object $rowobject
   * @see fetchArray()
   */
  function fetch($result = 0) {
    if( function_exists("odbc_fetch_object") ) {
        if ($result>0) {
            $rowobject = odbc_fetch_object($result, $this->cursor) ;
        } elseif ($this->result>0) {
            $rowobject = odbc_fetch_object($this->result, $this->cursor) ;
        }
    } else {
        if ($result>0) {
            $res = $result;
        } elseif ($this->result>0) {
            $res = $this->result; 
        }
        $a_data = array();
        $tmp_obj = false;
        if( odbc_fetch_into($res, &$a_data) ) {
            foreach( $a_data as $key=>$value ) {
                $fkey = odbc_field_name($res, $key+1);
                $tmp_obj->$fkey = trim($value);
            }
        }
       $rowobject = $tmp_obj;
    }

    $this->cursor++ ;
    $this->data = $rowobject ;
  return $rowobject ;
  }

  /**
   *  Return the content data of a record from a result set.
   *
   *  Return the data of a record in the form of an Array where all fields are keys.
   *  It use the result set of a previously executed query.
   *  It move the cursor of the ResultSet to the next record
   *
   * @param ResultSet $result
   * @return array $rowarray
   * @see fetch()
   */
  function fetchArray($result = 0) {
    if ($result>0) {
      $rowarray = odbc_fetch_array($result, $this->cursor) ;
    } elseif ($this->result>0) {
      $rowarray = odbc_fetch_array($this->result) ;
    }
    $this->cursor++ ;
    $this->data = $rowarray ; 
    return $rowarray ;
  }

  /**
   *  Return all the fields of a table.
   *
   * It also populate the metadata array attribute will all the informations about the
   * table fields. Type, Key, Extra, Null
   * TABLE_QUALIFIER    
      TABLE_SCHEM
      TABLE_NAME
      COLUMN_NAME
      DATA_TYPE
      TYPE_NAME
      PRECISION
      LENGTH
      SCALE
      RADIX
      NULLABLE
      REMARKS
   *
   * @param string $table Name of the Table
   * @return array $field  All the fields name
   */
  function getTableField($table="") {
    if (is_array($table)) {
      $atable = $table ;
    } elseif (strlen($table) > 0) {
      $atable = $table ;
    } else {
      $atable = $this->table ;
    }
    if (is_array($atable)) {
      reset($atable) ;
      $numfields = 0 ;
            
      while(list($key, $table) = each($atable)) {
        //$table_def = mysql_query("SHOW FIELDS FROM $table", );
        $table_def = odbc_columns($this->dbCon->id, $this->dbCon->getDatabase(), "%", $table);
        for ($i=0;$i<odbc_num_rows($table_def);$i++) {
          $row_table_def = odbc_fetch_array($table_def);
          $field[$numfields] = $row_table_def["COLUMN_NAME"];
          $fieldname = $row_table_def["COLUMN_NAME"];
          $this->metadata[$table][$fieldname]["Type"] = $row_table_def["TYPE_NAME"];
          $this->metadata[$table][$fieldname]["Null"] = $row_table_def["NULLABLE"];
          $this->metadata[$table][$fieldname]["Key"] = $row_table_def["SCALE"];  //not sure of that one.
          $this->metadata[$table][$fieldname]["Extra"] = $row_table_def["REMARKS"];
          $numfields++ ;
        }
      }
    } else {
      $table_def = odbc_columns($this->dbCon->id, $this->dbCon->getDatabase(), "%", $atable);
      for ($i=0;$i<odbc_num_rows($table_def);$i++) {
        $row_table_def = odbc_fetch_array($table_def);
        $field[$i] = $row_table_def["COLUMN_NAME"];
        $fieldname = $row_table_def["COLUMN_NAME"];
        $this->metadata[$table][$fieldname]["Type"] = $row_table_def["TYPE_NAME"];
        $this->metadata[$table][$fieldname]["Null"] = $row_table_def["NULLABLE"];
        $this->metadata[$table][$fieldname]["Key"] = $row_table_def["SCALE"];  //not sure of that one.
        $this->metadata[$table][$fieldname]["Extra"] = $row_table_def["REMARKS"];
      }
    }
    reset($field) ;
    return $field ;
  }

 /**
   *  Return all the fields from a query
   *
   * @param string $result Name of the Table
   * @return array $field  All the fields name
   */
  function getQueryField($result="") {
    if ($result == "") {
      $result = $this->result ;
    }
    $numfield = odbc_num_fields($result) ;
    for ($i=0; $i < $numfield; $i++) {
      $field[$i] = odbc_field_name($result, $i) ;
  }
   return $field ;
}
  /**
   * Return a ResultSet with all the table names from the database of the query.
   *
   * @param object sqlConnect $dbc
   * @return ResultSet $result  ResultSet with all the tables names
   * @access public
   * @see fetchTableName()
   */
  function getTables($dbc=0) {
    if ($dbc == 0) {
      $dbc = $this->getDbCon() ;
    }
    $result = odbc_tables ($dbc->id);
    $this->result = $result ;
    $this->cursor = 0 ;
    $this->num_rows = odbc_num_rows($result) ;
    return $result ;
  }

  /**
   * Return a ResultSet with all the databases from the connexion.
   *
   * @param object sqlConnect $dbc
   * @return ResultSet $result  ResultSet with all the tables names
   * @access public
   * @see fetchTableName()
   */
  function getDatabases($dbc=0) {
  /*
    if ($dbc == 0) {
      $dbc = $this->getDbCon() ;
    }
    $result = mysql_list_dbs($dbc->id);
    $this->result = $result ;
    $this->cursor = 0 ;
    $this->num_rows = mysql_num_rows($result) ;
    return $result ;
    */
    $this->setError("sqlQuery, ODBC : getDatabases not suported");
    return 0;
  }
  /**
   * Try to create a new database
   *
   * @param string name new database name
   * @return true if succed false if not
   * @access public
   */
  function createDatabase($name) {
    //$b_success = mysql_create_db($name);
    //return $b_success;
    $this->setError("sqlQuery, ODBC : createDatabase not supported");
    return 0;
  }

  /**
   *  Fetch a table name from the result set created by getTables.
   *
   *  Fetch the ResultSet from getTables and increment the cursor to the
   * next record
   *
   *  @param ResultSet $tableList
   *  @return string $tablename
   *  @access public
   *  @see getTables()
   */
  function fetchTableName($tableList=0) {
    if ($this->cursor >= $this->num_rows) {
      return 0 ;
    } else {
      if($tableList==0) {
        $tablename = mysql_tablename ($this->result, $this->cursor);
      } else {
        $tablename = mysql_tablename ($tableList, $this->cursor);
      }
      $this->cursor++ ;
      return $tablename ;
    }
  }
  
  /** 
   * Set the database connexion object (sqlConnect).
   *
   * @param sqlConnect $dbConid database connexion.
   */  
  function setDbCon($dbConid) {
    $this->dbCon = $dbConid ;
  }
  
  /** 
   * Return the database connexion object (sqlConnect).
   *
   * @return sqlConnect database connexion
   */  
  function getDbCon() {
    return $this->dbCon ;
  }
  /**
   * Return the name of the Table from the executed SQL Query.
   *
   * @param ResultSet $result
   * @return string $table Table name
   * @access public
   */
  function getTableName($result=0) {
  /*
    if (is_resource($this->result)) {
        $table = mysql_field_table($this->result,0);
    } else {
        $this->setError("Can't get the table name the result set is not a ressource") ;
    }
    return $table ;
    */
    $this->setError("sqlQuery, ODBC : get table name not yet supported");
    return 0;
  }
  
  /**
   * Set the default table name for the query.
   * Some object needs the table name of query reruning the query parser is 
   * very cpu intensive so we store the table name in the table attribute.
   * To set multiple tables separate them with comas.
   * 
   * @param mixte $table string with table name separated with comas or array of table names. 
   * @see getTable()
   */  
  function setTable($table) {
    if (strrpos($table, ",")) {
		$table = explode(",",$table);
	}	  
    $this->table = $table;
  }
  
  /** 
   * Return the table(s) of the query.
   * The table return can be a string with the uniq table name or 
   * an array or table names.
   * 
   * @return mixte array of tables or string with table name.
   */  
  function getTable() {
    return $this->table ;
  }
  
  /**
   * Return the sql statement of the query.
   * 
   * @return string with the sql statement of the query.
   */  
  function getSqlQuery() {
    return $this->sql_query ;
  }
  
  /** 
   * Set the SQL statement for the query.
   * @param string $query with SQL statement. 
   * @see query()
   */ 
  function setSqlQuery($query) {
    $this->sql_query = $query ;
  }
  

  /** 
   * Return the result ressourse of the query
   * When query() is called the query is executed and the result ressource can 
   * be returned.
   * The fetch() and getData() method used the internal ressource attribute.
   *
   * @return ressource of the query ressource.
   * @see query()
   */   
  function getResultSet() {
    return $this->result ;
  }
  /** 
   * setResultSet
   * Set the result of a query the this object.
   *
   * @param resource ResultSet of a query
   * @see query(), getResultSet()
   */
  function setResultSet($resultSet) {
      if (is_resource($resultSet)) {
          $this->result = $resultSet;
      }
  }  
  /** 
   * Set the SQL statement for the query.
   * @param string $query with SQL statement. 
   * @see query()
   */  
  function setMaxRows($rows) {
    $this->max_rows = $rows;
  }

  /**
   * setCursor set the position of the cursor in the current result set.
   * Once the query is executed, the $pos will move the cursor to that position.
   * @param integer $pos position of the next row to seek.
   **/
  function setCursor($pos) {
    $this->cursor = $pos ; 
  }

  /** 
   * getCursor return the current position of the cursor.
   **/
  function getCursor() {
      return $this->cursor;
  }

  /**
   * Return the value of a field.
   * From an executed sql Query and fetch row this will return the 
   * value of a field from the current read row.
   * query() and fetch() method need to be executed before data can be returned.
   * @param string $fieldname name of the field to get the query value from.
   * @see query(), fetch(), getD()
   */  
  function getData($fieldname) {
    return $this->getD($fieldname) ;
  }
  
  /**
   * Return the value of a field.
   * Shorter version of getData() method.
   *
   * @param string $fieldname name of the field to get the query value from.
   * @see getData()
   */  
  function getD($fieldname) {
    if (is_object($this->data)) {
        return $this->data->{$fieldname} ;
    } elseif (is_array($this->data)) {
        return $this->data[$fieldname] ;
    } else {
        return false ;
    }
  }
   /**
   * Escape a string from bad injections.
   * Need to be apply to all values comming from POST/GET.
   *
   * @param string string to escape
   * @return string escaped.
   */
    function escapeString($value) {
        if (get_magic_quotes_gpc()) {
                $value = stripslashes($value);
        }
        return addslashes($value);
    } 
  /**
   *  Destructor, clear the ResultSet and  other related attributes.
   *
   * @param ResultSet $result
   * @access public
   */
  function free($result = 0) {
    if ($result>0) {
      odbc_free_result($result) ;
    } elseif ($this->id>0) {
      odbc_free_result($this->id) ;
    }
    $this->sql_query ="";
    $this->sql_order ="" ;
    $this->pos = 0 ;
    $this->max_rows = 0 ;
    $this->num_rows = 0;
  }
} /* End class sqlQuery */



/* function not working in version 4.1 of PHP    
 */
 
 if  (function_exists('odbc_fetch_array'))
  return;
  
 function odbc_fetch_array($result, $rownumber=-1) {
  if (PHP_VERSION > "4.1") {
    if ($rownumber < 0) {
     odbc_fetch_into($result, &$rs);
    } else {
     odbc_fetch_into($result, &$rs, $rownumber);
    }
  } else {
   odbc_fetch_into($result, $rownumber, &$rs);
  }
  foreach ($rs as $key => $value) {
    $rs_assoc[odbc_field_name($result, $key+1)] = $value;
  }
  return $rs_assoc;
}

?>
