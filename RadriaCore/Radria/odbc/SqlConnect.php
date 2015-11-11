<?php
namespace RadriaCore\Radria\odbc;
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
   /**
    *   Abstract connexion to a ODBC Database.
    *
    *  This on is for any ODBC database
    *  Create a connexion object that can be reuse in the PHP script
    *  The connexion object is used in all the MyDB Classes.
    *  The backup synchronisation feature is used to make a backup off all the
    *  change in the database. (insert, update, alter, create....). Its very uselfull if
    *  you want to synchronise with a main server after working in local.
    *
    *  This is using the unifed odbc functions from PHP, meaning it will work with any 
    *  ODBC sources plus natively access the following databases : 
    *  Adabas D, IBM DB2, iODBC, Solid, and Sybase SQL Anywhere.
    *
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @version 3.0.0
    * @package RadriaCoreODBC
    * @access public
    */
use RadriaCore\Radria\BaseObject;

Class SqlConnect extends BaseObject {

  /**  Hostname where the database is hosted
   * @var String $hostname
   * @access private
   */
  var $hostname = "localhost"  ;

  /**  Login to acces the database
   * @var String $login
   * @access private
   */
  var $login = "" ;

  /**  Password to acces the database
   * @var String $password
   * @access private
   */
  var $password = "" ;

   /**  DSN Name
   * @var String $db
   */
  var $db = "";

  /**  Table Name
   * @var String $table
   */
  var $table = "";

  /**  Database connexion identifier
  * @var databaseidentifier $id
  * @access private
  */
  var $id;

   /** Backup all queries for synchronisation
    * @var bool $backupSync
    * @access private
    */
   var $backupSync = false ;

    /** Table backup Sync
    * @var String $tbl_backupSync
    * @access public
    */
   var $tbl_backupSync = "backupsync" ;

   /** Use Database or text files
    * @var boolean $useDatabase
    * @access private
    */
   var $useDatabase = false ;

   /** Base directory for files
   * @var String baseDir
   * @default "." curent directory
   * @access public
   */
   var $baseDir  ="../mydb2/" ;

   /** Project Directory for files
   * @var String baseDir
   * @default "." curent directory
   * @access public
   */
   var $projectDir  ="./" ;
   
   /** AllwaysSelectDb flag to tel sqlQuery if he need to
   * select the database before each query.
   * @var booloean
   * @default "." curent directory
   * @access public
   */
   var $allwaysselectdb = false;

  /**
   *    Constructor sqlConnect
   *
   *    If provide with parameters set the login and password
   *
   *  @param string login $login    Username to access the database
   *  @param  string password $password    Password to access the database
   */
  function sqlConnect($login="", $password="") {
    $this->login = $login ;
    $this->password = $password ;
  }

  /**
   *  Method start()
   *
   *  Its use to connect to a ODBC database.
   *  This is the prefered methode compare to startp().
   *  The login and password are not required.
   *  DSN must be set as the database before calling this function.
   *
   *  @param string login $login    Username to access the database
   *  @param  string password $password    Password to access the database
   *  @return connextionid $linkidentifier
   * @see startp()
   */
  function start($login="", $password="")  {
    if (strlen($login) > 0 && strlen($password) > 0) {
      $this->login = $login ;
      $this->password = $password ;
    }
    if (strlen($this->login) > 0 && strlen($this->password) > 0) {
      if (strlen($this->db)>0) {
        $linkidentifier = odbc_connect($this->db, $this->login, $this->password) ;
        if (is_resource($linkidentifier)) {
          $this->id = $linkidentifier ;
        } else { 
          $this->setError("<b>Database Connect Error</b> : Couldn't connect to the database Wrong login and password") ;
        }
      } else {
        $this->setError("<b>Database Select Error</b> : No DSN/Database Provide SetDatabase before starting the connexion");
      }
    } else {
      if (strlen($this->db)>0) {
        $linkidentifier = odbc_connect($this->db, "", "") ;
        if (is_resource($linkidentifier)) {
          $this->id = $linkidentifier ;
        } else { 
          $this->setError("<b>Database Connect Error</b> : Couldn't connect to the database probably a login is required to connect to the database, please provide a login and password") ;
        }
      }
    }
    return $linkidentifier ;
  }

   /**
   *  Method startp()
   *
   *  Its use to connect to a ODBC database using persistant connexion.
   *  Becarefull with the use of persistante connexion if you dont close them you
   *  can overload the ressource of your system with unused connexion.
   *
   *  @param string login $login    Username to access the database
   *  @param  string password $password    Password to access the database
   *  @return connectionid $linkidentifier
   *  @see start()
   */
  function startp($login="", $password="")  {
    if (strlen($login) > 0 && strlen($password) > 0) {
      $this->login = $login ;
      $this->password = $password ;
    }
    if (strlen($this->login) > 0 && strlen($this->password) > 0) {
      if (strlen($this->db)>0) {
        $linkidentifier = odbc_pconnect($this->db, $this->login, $this->password) ;
        if (is_resource($linkidentifier)) {
          $this->id = $linkidentifier ;
        } else { $this->setError("<b>Database Error</b> : Couldn't connect to the database") ;}
      } else {  $this->setError("<b>Database Select Error</b> : No DSN/Database provide SetDatabase before starting the connexion");}
    } else { $this->setError("<b>Database Connect Error</b> : A login and password are required to connect to the database") ; }
    return $linkidentifier ;
  }

  /**
   *    Method stop
   *    Close Database connexion
   */
  function stop() {
    odbc_close($this->id) ;
  }
  /**
   *  Set the hostname for the connexion
   */
  function setHostname($hostname) {
    $this->hostname = $hostname ;
  }
  /**
   *  return the hostname of the connexion
   */
  function getHostname() {
    return $this->hostname ;
  }
  /**
   *  Set the DSN name of the connexion
   */
  function setDatabase($db) {
    $this->db = $db ;
  //  if(is_resource($this->id))  {
  //    mysql_select_db($this->db, $this->id) ;
  //  }
  }
  /**
   *  Return the DSN name of the connexion
   */
  function getDatabase() {
    return $this->db ;
  }
  /**
   *  Set a default table (deprecate)
   */
  function setTable($table) {
    $this->table = $table ;
  }
  /**
   *  Return the name of the default table (deprecate)
   */
  function getTable() {
    return $this->table ;
  }
  /**
   *  Return connexion ressource id
   */
  function getDbConId() {
    return $this->id ;
  }
  /**
   *  Set to true of false the Backup synchronisation.
		* 	 If set to true, all the queries using this connexion will be saved in 2 files:
		* 
		* backupsync.struct.sql for all queries that generate structural changes to the database. (CREATE, DROP, ALTER)
		* backupsync.data.sql for all the queries that modify the data content of the 
		* database.
		* @param boolean bool 
   */
  function setBackupSync($bool = true) {
    $this->backupSync = $bool ;
  }
  /**
   *  Return the value of the backupSync flag

   * @return bool value of the backupsync flag
   */
  function getBackupSync() {
    return $this->backupSync  ;
  }
  /**
   *  Return the name of the table use for the backupsync 
* 	 (Deprecate)
* 	 @return string name of the table used for backupsync
   */
  function getTableBackupSync() {
    return $this->tbl_backupSync ;
  }
  /**
   *  Return true the value of the backupSync flag
* 	 @return boolean true if there is connexion to the database, false otherwise.
   */
  function is_connected() {
    if (is_resource($this->id)) {
      return true ;
    } else {
      return false ; 
    }
  }
  /**
   *  return if the database instead of xml file is used or not.
* 	 @return boolean true if the database is used, false otherwise
   */
  function getUseDatabase(){
    return $this->useDatabase ;
  }
	  /**
   *  Set the use of the database to true or false.
* 	 By default mydb elements like : reports, forms, registry are stored using xml files.
* For speed and sclalable reason database tables can be used instead.
*
   */
  function setUseDatabase($bool= true) {
    $this->useDatabase = $bool ;
  }
  /**
   *  Return the path of the directory where the main MyDB library is stored.
* 	 @return string with path of the MyDB library
   */
  function getBaseDirectory() {
    return $this->baseDir ;
  }
  /**
   *  Set the path where the MyDB library is stored.
   */
  function setBaseDirectory($dirname) {
    if(ereg("/$", $dirname)) {
     $this->baseDir = $dirname ;
    } else {
     $this->baseDir = $dirname."/" ;
    }
  }
  /**
   *  Return the path of the project using this connexion is stored.
* 	 @return string with path of project using this connexion
   */
  function getProjectDirectory() {
    return $this->projectDir ;
  }
  /**
   *  Set the path of the project using this connexion is stored
   */
  function setProjectDirectory($dirname) {
    if(ereg("/$", $dirname)) {
      $this->projectDir = $dirname ;
    } else {
      $this->projectDir = $dirname."/" ;
    }
  }
  /**
   *  allways_select_db, is a flag for the sqlQuery object
   *  To select the database before each query
   *  This flag fix a weid behavior from mysql/php.
   *  When you use multiple sqlConnect objects to access different
   *  databases and use the same user name, then the mysql_select_db
   *  function dosen't work properly anymore. The select database is
   *  apply to all the connexion with the same username.
   *
   *  So for that case this flag will tel sqlConnect to do a mysql_select_db
   *  before each query
   **/

   function setAllwaysSelectDb($bool) {
     $this->allwaysselectdb = $bool;
   }
   function getAllwaysSelectDb() {
     return $this->allwaysselectdb ;
   }


} /** End class sqlConnect */
?>
