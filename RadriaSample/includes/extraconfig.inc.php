<?php 
// Copyright 2001 - 2007 SQLFusion LLC           info@sqlfusion.com

  /**
   * Extra config, load config settings from packages.
   * Needed to load classes before session started.
   **/

    include_once($cfg_local_pasdir."class/BaseObject.class.php") ;
    include_once($cfg_local_pasdir."class/".$cfg_local_db."/sqlConnect.class.php") ;
    include_once($cfg_local_pasdir."class/".$cfg_local_db."/sqlQuery.class.php") ;
    include_once($cfg_local_pasdir."class/Fields.class.php") ;
    include_once($cfg_local_pasdir."class/FieldType.class.php") ;  
    include_once($cfg_local_pasdir."class/EventControler.class.php") ;
    include_once($cfg_local_pasdir."class/Display.class.php") ;
    include_once($cfg_local_pasdir."class/Event.class.php") ;

    if (substr(phpversion(),0,1) > 4) {
        include_once($cfg_local_pasdir."class/RadriaException.class.php") ;
        include_once($cfg_local_pasdir."class/DataObject.class.php") ;
        include_once($cfg_local_pasdir."class/FieldsForm.class.php");
    }
    
    $d = dir($cfg_project_directory."includes/");
    while($entry = $d->read()) {
        if (preg_match("/\.conf\.inc\.php$/i", $entry) && !preg_match("/^\./", $entry)) {
            $config_files[] = $entry;
        }
    }
    $d->close();

    if (is_array($config_files)) {
        sort($config_files) ;
    
        foreach($config_files as $config_file) {
            include_once($config_file);
            //echo "\n<br>".$config_file;
        }    
    }

  // By default all path are relative. If true it will set all path to absolute
  define("RADRIA_EVENT_ABSOLUTE_PATH", false);

//  Log errors in the pas_errro.log file:
  define("RADRIA_LOG_ERROR", true);
//  Display errors in generated web pages:
  define("RADRIA_DISPLAY_ERROR", false);
//  Log general message/debug log in the pas_run.log:
  define("RADRIA_LOG_RUNLOG", true);
//  Display message/debug log in generated web pages:
  define("RADRIA_DISPLAY_RUNLOG", false);

    
//  To log only specific classes set the value to true
  define("RADRIA_LOG_RUN_DISPLAY", false);
  define("RADRIA_LOG_RUN_EVENT", true);
  define("RADRIA_LOG_RUN_EVENTCONTROLER", true);
  define("RADRIA_LOG_RUN_REGISTRY", false);
  define("RADRIA_LOG_RUN_REGISTRYFIELD", true);
  define("RADRIA_LOG_RUN_SQLQUERY", false);
  define("RADRIA_LOG_RUN_DATAOBJECT", false);
  define("RADRIA_LOG_RUN_MYDB_EVENTS", false);
  
?>
