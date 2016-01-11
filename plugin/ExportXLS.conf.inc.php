<?php
// Copyright 2008-2011 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/
  /**
   * SamplePlugIn configuration
   * This is a configuration file for the Sample plugin.
   * Its load class and set hooks 
   *
   * @package SamplePlugIn
   * @author Philippe Lewicki <phil@sqlfusion.com>
   * @license ##License##
   * @version 0.1
   * @date 2010-09-04  
   */

   // We include here our objects
   //require_once "plugin/ExportXLS/php_writeexcel-0.3.0/class.writeexcel_workbook.inc.php";
   //require_once "plugin/ExportXLS/php_writeexcel-0.3.0/class.writeexcel_worksheet.inc.php";

   $vv= "plugin/ExportXLS/php_writeexcel-0.3.0/";
   require_once("$vv"."class.writeexcel_workbook.inc.php");
   require_once("$vv"."class.writeexcel_worksheet.inc.php");

   include_once("plugin/ExportXLS/Export.class.php");

   // Hook to display the tab Settings.
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("ExportXLS"));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName("Export to Excel")
                                        ->setTitle("Export your contacts to Excel")
                                        ->setPages(Array ("settings_export"))
                                        ->setDefaultPage("settings_export");

?>