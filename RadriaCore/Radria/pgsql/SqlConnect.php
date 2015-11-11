<?php
namespace RadriaCore\Radria\pgsql;
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
    *  Abstract connexion to a PostgreSQL Database.
    *
    *  Abstract connexion to a Database.  This on is for PostgreSQL database
    *  Create a connexion object that can be reuse in the PHP script
    *  The connexion obejct is used in all the MyDB Classes.
    *  The backup synchronisation feature is used to make a backup off all the
    *  change in the database. (insert, update, alter, create....). Its very uselfull if
    *  you want to synchronise with a main server after working in local.
    *
    * @copyright  SQLFusion 2001 - 2004
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @version 3.0.5
    * @package RadriaCorePGSQL
    * @access public
    */

Class SqlConnect {
   /**  Hostname where the database is hosted
   * @var String $hostname
   * @access private
   */
  var $hostname = "localhost"  ;

    /**  Login to acces the database
   * @var String $login
   * @access private
   */
  var $login = "lewicki" ;

  /**  Password to acces the database
   * @var String $password
   * @access private
   */
  var $password = "lewicki" ;

  /**  Database Name
   * @var String $db
   */
  var $db ;

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
   * @access public
   */
   var $baseDir;

   /** Project Directory for files
   * @var String baseDir
   * @default "." curent directory
   * @access public
   */
   var $projectDir  ="./" ;
  /**
   *    Constructor sqlConnect.
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
   *  Open a connection to the database.
   *
   *  Its use to connect to a PostgreSQL database.
   *  This is the prefered methode compare to startp().
   *  The database need to be set before executing this method.
   *
   *  @param string $login    Username to access the database
   *  @param  string $password    Password to access the database
   *  @return connextionid $linkidentifier
   *  @see startp()
   */
  function start($login="", $password="")  {
      if (strlen($login) > 0) {
        $this->login = $login ;
      }
      if  (strlen($password) > 0) {
        $this->password = $password ;
    }
    if (strlen($this->hostname) > 0 && strlen($this->db)>0 && strlen($this->login)>0 && strlen($this->password)>0) {
      $linkidentifier = pg_connect("host=$this->hostname dbname=$this->db user=$this->login password=$this->password") ;
    } elseif(strlen($this->hostname) > 0 && strlen($this->db)>0 && strlen($this->login)>0) {
      $linkidentifier = pg_connect("host=$this->hostname dbname=$this->db user=$this->login") ;
    } elseif(strlen($this->hostname) > 0 && strlen($this->db)>0) {
      $linkidentifier = pg_connect("host=$this->hostname dbname=$this->db user=postgres") ;
    } elseif(strlen($this->db) > 0) {
      $linkidentifier = pg_connect("dbname=$this->db") ;
    }
    $this->id = $linkidentifier;
    return $linkidentifier ;
  }

    /**
   *  Open a persistant connection to the database.
   *
   *  Its use to connect to a PostgreSQL database using persistant connexion.
   *  Becarefull with the use of persistante connexion if you dont close them you
   *  can overload the ressource of your system with unused connexion.
   *  Some version of PostgreSQL are known for not closing properly connexions.
   *
   *  @param string login $login    Username to access the database
   *  @param  string password $password    Password to access the database
   *  @return connextionid $linkidentifier
   *  @see start()
   */
  function startp($login="", $password="")  {
      if (strlen($login) > 0 && strlen($password) > 0) {
      $this->login = $login ;
      $this->password = $password ;
    }
    if (strlen($this->hostname) > 0 && strlen($this->db)>0 && strlen($this->login)>0 && strlen($this->password)>0) {
      $linkidentifier = pg_pconnect("host=$this->hostname dbname=$this->db user=$this->login password=$this->password") ;
    } elseif(strlen($this->hostname) > 0 && strlen($this->db)>0 && strlen($this->login)>0) {
      $linkidentifier = pg_pconnect("host=$this->hostname dbname=$this->db user=$this->login") ;
    } elseif(strlen($this->hostname) > 0 && strlen($this->db)>0) {
      $linkidentifier = pg_pconnect("host=$this->hostname dbname=$this->db") ;
    } elseif(strlen($this->db) > 0) {
      $linkidentifier = pg_pconnect("dbname=$this->db") ;
    }  
    $this->id = $linkidentifier ;
    return $linkidentifier ;
  }

  /**
   *  Close Database connexion.
   */
  function stop() {
   pg_close($this->id) ;
  }

  function setHostname($hostname) {
    $this->hostname = $hostname ;
  }

  function getHostname() {
    return $this->hostname ;
  }

  function setDatabase($db) {
    $this->db = $db ;
  }

  function getDatabase() {
    return $this->db ;
  }

  function setTable($table) {
    $this->table = $table ;
  }

  function getTable() {
    return $this->table ;
  }

  function getDbConId() {
    return $this->id ;
  }

  function setBackupSync($bool = true) {
    $this->backupSync = $bool ;
  }

  function getBackupSync() {
    return $this->backupSync  ;
  }

  function getTableBackupSync() {
    return $this->tbl_backupSync ;
  }

  function getUseDatabase(){
    return $useDatabase ;
  }

  function setUseDatabase($bool= false) {
    $this->useDatabase = $bool ;
  }

  function getBaseDirectory() {
    return $this->baseDir ;
  }

  function setBaseDirectory($dirname) {
    if(ereg("/$", $dirname)) {
     $this->baseDir = $dirname ;
    } else {
     $this->baseDir = $dirname."/" ;
    }
  }

  function getProjectDirectory() {
    return $this->projectDir ;
  }

  function setProjectDirectory($dirname) {
    if(ereg("/$", $dirname)) {
      $this->projectDir = $dirname ;
    } else {
      $this->projectDir = $dirname."/" ;
    }
  }
} /* End class sqlConnect */

