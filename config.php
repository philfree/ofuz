<?php
// Copyright 2001 - 2007 SQLFusion LLC           info@sqlfusion.com
  /**
   * MySQL Main configuration page
   *
   * Include that file in all the files that will uses PAS objects.
   *
   * @package RadriaSiteTemplate
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007
   * @version 4.0
   */


  if (!isset($GLOBALS['cfg_full_path'])) { $GLOBALS['cfg_full_path'] = ''; }
  set_include_path(get_include_path() . PATH_SEPARATOR . $GLOBALS['cfg_full_path']);
  $cfg_project_directory = $GLOBALS['cfg_full_path'];
  $cfg_local_pasdir = $GLOBALS['cfg_full_path'].'../RadriaCore/'; // path to Radria from your app usualy its : '../pas/'
  $cfg_local_db = 'mysql';
  $cfg_eventcontroler = 'eventcontroler.php';
  //define("RADRIA_EVENT_CONTROLER", $cfg_eventcontroler);
  
  $cfg_lang = 'us';
  // For compatibility with mydb
  $cfg_local_mydbdir = $cfg_local_pasdir;
  
  // diseable secure events, will show all the parameters of forms and links.
  //define("RADRIA_EVENT_SECURE", false);
  define("RADRIA_DEFAULT_REPORT_TEMPLATE", "default_report");
  define("RADRIA_DEFAULT_FORM_TEMPLATE", "default_form");
  define("RADRIA_LOCAL_DB", $cfg_local_db);

  //  Change the default events parameters times out
  //  $cfg_event_param_garbage_time_out = 3600;
  //  $cfg_event_param_garbage_interval = 3400;

  // Change this key. This is the key that authorized event execution coming from not local domain.
  $cfg_notrefererequestkey = "@refererkey" ;

  //Radria anonymous usage statistics:
  $cfg_radria_stat_usage = true;

  // Turn off errors display on production site
  // or when using the pas pagebuilder
  //error_reporting(0);
  error_reporting(E_ERROR | E_WARNING | E_PARSE);

  if (file_exists($GLOBALS['cfg_full_path'].'includes/extraconfig.inc.php')) {
      include_once($GLOBALS['cfg_full_path'].'includes/extraconfig.inc.php');
  } 

  $cfg_web_path =  dirname($_SERVER['PHP_SELF']);

  if (!ereg("/$", $cfg_web_path)) {
     $cfg_web_path .= "/";
  }

  session_set_cookie_params(0, $cfg_web_path);
  session_start() ;

  //include("includes/lang_".$cfg_lang.".inc.php") ;
  //$_SESSION["cfg_lang"] = $cfg_lang ;
  // Database connexions :

  $conx = new sqlConnect("ofuz", "d3v5") ;
  $conx->setHostname("localhost") ;
  $conx->setDatabase("ofuz") ;
  // Directory where pas is located
  $conx->setBaseDirectory($cfg_local_pasdir) ;
  // Directory where the project is located unless your config.php file is outside your project tree is should be "./"
  $conx->setProjectDirectory($cfg_full_path."./") ;
  $conx->start() ;

  include("includes/globalvar.inc.php") ;

  if (file_exists("includes/extraconfig_postdb.inc.php")) {
        include_once("includes/extraconfig_postdb.inc.php") ;
  };
?>
