<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


  /**
   * Extra config, load config settings from packages.
   * Needed t
   * o load classes before session started.
   **/
    $cfg_local_pasdir = '../radria/RadriaCore/';
    include_once($cfg_local_pasdir."class/BaseObject.class.php") ;
    include_once($cfg_local_pasdir."class/".$cfg_local_db."/sqlConnect.class.php") ;
    include_once($cfg_local_pasdir."class/".$cfg_local_db."/sqlQuery.class.php") ;
    include_once($cfg_local_pasdir."class/Registry.class.php") ;
    include_once($cfg_local_pasdir."class/sqlSavedQuery.class.php") ;
    include_once($cfg_local_pasdir."class/Report.class.php") ;
    include_once($cfg_local_pasdir."class/libReport.php") ;
    include_once($cfg_local_pasdir."class/EventControler.class.php") ;
    include_once($cfg_local_pasdir."class/Display.class.php") ;
    include_once($cfg_local_pasdir."class/Event.class.php") ;
    include_once($cfg_local_pasdir."class/ReportForm.class.php") ;
    include_once($cfg_local_pasdir."class/ReportTable.class.php") ;

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
    }
    foreach($config_files as $config_file) {
        include_once($config_file);
        //echo "\n<br>".$config_file;
    }    
    
       // Block positioning 

  define("RADRIA_EVENT_ABSOLUTE_PATH", true);

//  Log errors in the pas_errro.log file:
  define("RADRIA_LOG_ERROR", true);
//  Display errors in generated web pages:
  define("RADRIA_DISPLAY_ERROR", true);
//  Log general message/debug log in the pas_run.log:
  define("RADRIA_LOG_RUNLOG", true);
//  Display message/debug log in generated web pages:
  define("RADRIA_DISPLAY_RUNLOG", true);

    
//  To log only specific classes set the value to true
  define("RADRIA_LOG_RUN_DISPLAY", false);
  define("RADRIA_LOG_RUN_RECORDEVENT", false);
  define("RADRIA_LOG_RUN_EVENT", false);
  define("RADRIA_LOG_RUN_EVENTCONTROLER", false);
  define("RADRIA_LOG_RUN_REGISTRY", false);
  define("RADRIA_LOG_RUN_REGISTRYFIELD", false);
  define("RADRIA_LOG_RUN_REPORT", false);
  define("RADRIA_LOG_RUN_REPORTFORM", false);
  define("RADRIA_LOG_RUN_REPORTTABLE", false);
  define("RADRIA_LOG_RUN_SQLSAVEDQUERY", false);
  define("RADRIA_LOG_RUN_SQLQUERY", false);
  define("RADRIA_LOG_RUN_DATAOBJECT", false);
  define("RADRIA_LOG_RUN_MYDB_EVENTS", false);
  
?>
