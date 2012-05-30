<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
    *  Abstract Database query
    *
    *  It is used to abstract query  and other Database access.
    *  
    *  For Postgres/MySQL compatibility
    *  The table required a SERIAL type to stay compatible with MySQL AUTOINCREMENT
    *  PostgreSQL supports a SERIAL data type.
    *  It auto-creates a sequence and index on the column. For example, this:
    *  <code>
    *  CREATE TABLE person (
    *    idperson   SERIAL,
    *   name TEXT
    *   );
    * is automatically translated into this:
    *   CREATE SEQUENCE person_idperson_seq;
    *   CREATE TABLE person (
    *       idperson   INT4 NOT NULL DEFAULT nextval('person_idperson_seq'),
    *       name TEXT
    *    );
    *   CREATE UNIQUE INDEX person_idperson_key ON person ( idperson );
    *   </code>
    *   See the create_sequence manual page for more information about sequences.
    *   You can also use each row's OID field as a unique value. However, if you
    *   need to dump and reload the database, you need to use pg_dump's -o option
    *   or COPY WITH OIDS option to preserve the OIDs.
    *
    *
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion 2001 - 2004    
    * @version 3.0.3
    * @package RadriaCorePGSQL
    * @access public
    */

Class sqlQuery extends BaseObject {

  /**  Name of the table used in the query, it can be an array for multiple tables
   * @var String $table
   */
  var $table = "";

  /**  SQL query string  that is going to be executed.
   * @var String $sql_query
   */
  var $sql_query ;

   /**  Order sequence of the SQL Query, exemple :
   * @var string $sql_order
   */
  var $sql_order ;

  /**  Position of the record, for the limit sequence of the SQL Query
   * @var integer $pos
   */
  var $pos = 0 ;

  /**  Number maximum of rows to display, for the limit sequence of the SQL Query
   * @var string $max_rows
   */
  var $max_rows ;

  /**  Number of rows return by the execution of the SQL Query
   * @var integer $num_rows
   */
  var $num_rows;

    /**  Server number, deprecate var was use for compatibility with phpmyadmin
   * @var String $server
   * @deprecated
   */
  var $server ="0";

     /**  Result set of the executed SQL Query
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
  var $dbCon ;

    /**  Position of the record in the result set.
   * @var integer $cursor
   */
  var $cursor ;

   /**  MetaData about the SQL Query, table name, fields , not used yet.
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
   * Constructor, create a new instance of a sqlQuery
   * @access public
   */
//  function sqlQuery() {
//    return true ;
//  }

    /**
   * Constructor, create a new instance of a sqlQuery
   * @param object sqlConnect $dbCon
   * @access public
   */
  function sqlQuery($dbCon=0) {
    //$this->objDisplayErrors = false ; 
    if (defined("RADRIA_LOG_RUN_SQLQUERY")) {
        $this->setLogRun(RADRIA_LOG_RUN_SQLQUERY);
    }
    $this->dbCon = $dbCon ;
    if (!is_resource($this->dbCon->id)) {
      $this->setError("Query Error: No open or valid connexion has been provide to execute the query. ") ;
      return false;
    } else {
      return true ;
    }
  }

    /**
   * Return the numbers of row for the executed SQLQuery
   * @param ResultSet $result
   * @return integer num_rows
   * @access public
   */
  function getNumRows($result = 0) {
    if (is_resource($result)) {
      $this->num_rows = pg_numrows($result) ;
    } elseif(is_resource($this->result)) {
      $this->num_rows = pg_numrows($this->result) ;
    } else {
      $this->num_rows = 0;
    }
    return $this->num_rows ;
   
  }

   /**
   * Execute an sqlQuery
   *
   * Execute a query the query string and database connexion object need
   * to be passe has parameters or be previously define
   *
   * @param sting $sql   String with the SQL Query
   * @param object sqlConnect $dbCon   Connexion object if not previously define in the contructor
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
//    if ($this->sql_query == "") {
//      if (is_array($this->table)) {
//        reset($this->table) ;
//        $this->sql_query = "select * from " ;
//        while (list($key, $table) = each($this->table)) {
//          $this->sql_query .= $table."," ;
//        }
//        $this->sql_query = ereg_replace(",$", "", $this->sql_query) ;
//      } else {
//        $this->sql_query= "select * from $this->table" ;
//      }
//    }
    if (empty($this->sql_query)) {
        $this->setError("No query to execute");
        return false;
    }
    if ($this->max_rows) {
      if (!$this->pos) { $this->pos = 0 ; }
     // $qpos = " limit ".$this->pos.", ".$this->max_rows ; PL oposite of MySQL
      $qpos = " limit ".$this->max_rows." offset ".$this->pos ;
    } else {
      if (!$this->pos) { $this->pos = "" ; }
      $qpos = $this->pos;
    }
    // convert quote from mysql queries
    // This will need to be removed as it may generate significant problem is queries uses backquotes
    // in their content
    $this->sql_query=str_replace('`','"',$this->sql_query);

    $rquery = pg_query($this->dbCon->id,"$this->sql_query $this->sql_order $qpos");
    if (!is_resource($rquery)) {
        $sqlerror = pg_last_error() ;
        if ($sqlerror) {
          $this->setError("<b>SQL Query Error :</b>".$sqlerror." ($this->sql_query $this->sql_order $qpos)") ;
        }
    } else {
    //    if (!$this->max_rows) {  PL 20020920 commented out to make it work on the fetch()
            $this->num_rows = @pg_numrows($rquery) ;
     //   }
    }

    //$this->insert_id =  pg_getlastoid($rquery) ;
    $this->result = $rquery ;
    $this->cursor = 0 ;
     if ($this->dbCon->getBackupSync()) {
        if (eregi("^alter", $this->sql_query)
         || eregi("^create", $this->sql_query)
         || eregi("^drop", $this->sql_query)) {
            if ($this->dbCon->getUseDatabase()) {
                $qInsSync = "insert into ".$this->dbCon->getTableBackupSync()." ( actiontime, sqlstatement, dbname) values ( '".time()."', '".addslashes($this->sql_query)."', '".$this->dbCon->db."') " ;
                $rquery = pg_query($this->dbCon->db,$qInsSync, $this->dbCon->id);
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
                $qInsSync = "insert into ".$this->dbCon->getTableBackupSync()." ( actiontime, sqlstatement, dbname) values ( '".time()."', '".addslashes("$this->sql_query $this->sql_order $qpos")."', '".$this->dbCon->db."') " ;
                $rquery = pg_query($this->dbCon->db,$qInsSync, $this->dbCon->id);
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
   * If param field is not provide it is guess with "id"+$tablename
   * if table param is not provide it search for $this->table.
   * if not enough information is found to create the sequence name
   * then it return 0.
   *
   * @param string $table  name of the table with the sequence
   * @param string $field name of the primary key used for the sequence
   *
   * @return integer insert_id
   */
  function getInsertId($table="",  $field="") {
    if(strlen($table) > 0) {
        if (strlen($field) == "") {
                    $field = "id".$table ;
         }
    } elseif (strlen($this->table) > 0) {
        $table = $this->table ;
        $field =  "id".$table ;
    }
    //$table = substr( $table, 0, 13) ;
    //$field = substr($field, 0, 13) ;
    if (strlen($table) > 0 && strlen($field) > 0) {
        $sequence = $table."_".$field."_seq" ;
        $rquery = pg_query($this->dbCon->id, "SELECT currval('".$sequence."')") ;
        $this->insert_id =   pg_Result($rquery, 0, 0) ;
        return $this->insert_id ;
    } else {
        return 0;
    }
  }

  /**
   * Catch and return an error string from the last Error
   * @return string Error description
   * @access public
   */
  function getError() {
    if (is_resource($this->getDbCon())) {
      return pg_errormessage($this->getDbCon());
    } else {
      return 0 ;
    }
  }

  /**
   *  return the content data of a record from a result set
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
    if ($this->cursor < $this->num_rows) {
      if ($result>0) {
        $rowobject = pg_fetch_object($result, $this->cursor) ;
      } elseif ($this->result>0) {
        $rowobject = pg_fetch_object($this->result, $this->cursor) ;
      }
    } else {
      $rowobject = 0 ;
    }
    $this->cursor++ ;
    $this->data = $rowobject ;
  return $rowobject ;
  }

  /**
   *  Return the content data of a record from a result set
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
    if ($this->cursor < $this->num_rows) {
      if (is_resource($result)) {
        $rowarray = pg_fetch_array($result, $this->cursor) ;
      } elseif (is_resource($this->result)) {
        $rowarray = pg_fetch_array($this->result, $this->cursor) ;
      }
    } else {
      $rowarray = 0 ;
    }
    $this->cursor++ ;
    $this->data = $rowarray ;
    return $rowarray ;
  }

  /**
   *  Return all the fields of a table, Not Implemented yet. Will return nothing.
   *
   * It also populate the metadata array attribute will all the informations about the
   * table fields. Type, Key, Extra, Null
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
    //$field = Array();
    if (is_array($atable)) {
      reset($atable) ;
      $numfields = 0 ;
      while(list($key, $table) = each($atable)) {
         $q_getfield = "SELECT a.attname, t.typname, a.attlen, a.atttypmod, a.attnotnull, a.atthasdef, a.attnum FROM pg_class c, pg_attribute a, pg_type t WHERE c.relname = '".$table."' AND a.attnum > 0 AND a.attrelid = c.oid AND a.atttypid = t.oid ORDER BY a.attnum";
        $table_def = pg_query($this->dbCon->id, $q_getfield) ;
        for ($i=0; $i< pg_numrows($table_def); $i++) {
          $fieldname = pg_result($table_def, $i, 0);
          $field[$numfields] = pg_result($table_def, $i, 0);
          $fieldname = pg_result($table_def, $i, 0);
          $this->metadata[$table][$fieldname]["Type"] = pg_result($table_def, $i, 1);
          $numfields++ ;
        }
      }
    } else {
        $q_getfield = "SELECT a.attname, t.typname, a.attlen, a.atttypmod, a.attnotnull, a.atthasdef, a.attnum FROM pg_class c, pg_attribute a, pg_type t WHERE c.relname = '".$atable."' AND a.attnum > 0 AND a.attrelid = c.oid AND a.atttypid = t.oid ORDER BY a.attnum";
        $table_def = pg_query($this->dbCon->id, $q_getfield) ;
        for ($i=0; $i< pg_numrows($table_def); $i++) {
          $fieldname = pg_result($table_def, $i, 0);
          $field[$numfields] = pg_result($table_def, $i, 0);
          $fieldname = pg_result($table_def, $i, 0);
          $this->metadata[$fieldname]["Type"] = pg_result($table_def, $i, 1);
          $numfields++ ;
        }
    }
    reset($field) ;
    return $field ;

//             $this->metadata[$fieldname]["Null"] = $row_table_def["Null"];
//             $this->metadata[$fieldname]["Key"] = $row_table_def["Key"];
//             $this->metadata[$fieldname]["Default"] = $row_table_def["Default"];
//             $this->metadata[$fieldname]["Extra"] = $row_table_def["Extra"];

  }

   /**
   *  Return all the fields from a query
   *
   * @param string $result Name of the Table
   * @return array $field  All the fields name
   */
  function getQueryField($result="") {
   return false ;
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
    if (!is_resource($dbc)) {
      $dbc = $this->getDbCon() ;
    }
    $q_getTables = "
      SELECT c.relname as \"Name\", 'table'::text as \"Type\", u.usename as \"Owner\" 
      FROM pg_class c, pg_user u 
      WHERE c.relowner = u.usesysid 
      AND c.relkind = 'r' 
      AND not exists (select 1 from pg_views where viewname = c.relname) 
      AND c.relname !~ '^pg_' 
      UNION 
          SELECT c.relname as \"Name\", 'table'::text as \"Type\", NULL as \"Owner\"
          FROM pg_class c 
          WHERE c.relkind = 'r'
          AND not exists (select 1 from pg_views where viewname = c.relname)
          AND not exists (select 1 from pg_user where usesysid = c.relowner)
          AND c.relname !~ '^pg_'
      ORDER BY \"Name\"
          ";

    $result = pg_query($dbc->id, $q_getTables) ;
    $this->result = $result ;
    $this->cursor = 0 ;
    $this->num_rows = pg_numrows($result) ;
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
    if (!is_resource($dbc)) {
      $dbc = $this->getDbCon() ;
    }
    $q_getDatabase = "
       SELECT pg_database.datname as \"Database\",  pg_user.usename as \"Owner\", pg_encoding_to_char(pg_database.encoding) as \"Encoding\" 
       FROM pg_database, pg_user
       WHERE pg_database.datdba = pg_user.usesysid
       UNION
       SELECT pg_database.datname as \"Database\", NULL as \"Owner\", pg_encoding_to_char(pg_database.encoding) as \"Encoding\"
       FROM pg_database
       WHERE pg_database.datdba NOT IN (SELECT usesysid FROM pg_user)
       ORDER BY \"Database\"
";
    $result = pg_query($dbc->id, $q_getDatabase);
    $this->result = $result ;
    $this->cursor = 0 ;
    $this->num_rows = pg_numrows($result) ;
    return $result ;
  }

   /**
   * Try to create a new database
   *
   * @param string name new database name
   * @return true if succed false if not
   * @access public
   */
  function createDatabase($name) {
    $b_success = pg_query("CREATE DATABASE $name");
    return $b_success;
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
        $tablename = pg_result ($this->result, $this->cursor, 0);
      } else {
        $tablename = pg_result ($tableList, $this->cursor, 0);
      }
      $this->cursor++ ;
      return $tablename ;
    }
  }

  /**
   * Return the name of the Table from the executed SQL Query.
   * Dont know how to do that with postgresql
   *
   * @param ResultSet $result
   * @return string $table Table name
   * @access public
   */
  function getTableName($result=0) {
    return false ;
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
   * Set the default table name for the query.
   * Some object needs the table name of query reruning the query parser is 
   * very cpu intensive so we store the table name in the table attribute.
   * To set multiple tables separate them with comas.
   * 
   * @param mixte $table string with table name separated with comas or array of table names. 
   * @see getTable()
   */  
  function setTable($table) {
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
   * Set the maximum number of rows for a query.
   * This will add a limit clause to the SQL Statement on its excecution.
   * 
   * @param integer $rows maximum number of rows to display.
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
   *  Destructor, clear the ResultSet and  other related attributes
   *
   * @param ResultSet $result
   * @access public
   */
  function free($result = 0) {
    parent::free();
    if ($result>0) {
      pg_freeresult($result) ;
    } elseif ($this->id>0) {
      pg_freeresult($this->id) ;
    }
    $this->sql_query ="";
    $this->sql_order ="" ;
    $this->pos = 0 ;
    $this->max_rows = 0 ;
    $this->num_rows = 0;
  }
} /* End class sqlQuery */

