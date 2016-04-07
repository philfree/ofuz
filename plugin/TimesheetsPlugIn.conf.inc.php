<?php
// Copyright 2008-2011 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/
  /**
   * TimesheetPlugIn configuration
   * This is a configuration file for the Sample plugin.
   * Its load class and set hooks 
   *
   * @package TimesheetPlugIn
   * @author varaprasad <varaprasad@htmlfusion.com>
   * @license ##License##
   * @version 0.1
   * @date 2016-03-20  
   */

/**
   tab enable disable is with setTabName("TimesheetsPlugIn")
   setting menu is with setTabName("Timesheets Plugin Setting");
   block with TabContentTimesheet

**/

// Plug-in Definition
// status : devel, alpha, beta, rc, stable
   $plugins_info['TimesheetsPlugIn'] = 
                    Array ('name' => 'Timesheets Plugin',
                           'description' => 'This is a sample description',
                           'version' => '0.0.1',
                           'status' => 'beta',
                           'tabs' => Array ('TimesheetsPlugIn'),
                           'settings' => Array('Timesheets Plugin Setting'),                          
                           'blocks' => Array('BlockTimesheets')
                           );                          

  // We include here our Block Object
  include_once("plugin/TimesheetsPlugIn/class/BlockTimesheets.class.php");
  include_once("plugin/TimesheetsPlugIn/class/TimesheetsPlugIn.class.php");
   

   $cfg_sample_plugin_path = "/Tab/TimesheetsPlugIn/";

   // Hook for the block object
   $GLOBALS['cfg_block_placement']['TabContentTimesheet'][] = "BlockTimesheets";

   $plugin_timesheet_menu = new SubMenu();
   $plugin_timesheet_menu->addMenuItem("page 1", $cfg_sample_plugin_path."TabContentTimesheets")
                      ->addMenuItem("page 2", $cfg_sample_plugin_path."TabContentTimesheets2");
         
   // Hook to display the Tab (they are real page just without the .php)  
   $GLOBALS['cfg_tab_placement']->append(new Tab("TimesheetsPlugIn"));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTitle("You are on the Timesheets Plugin")
                                ->setPages(Array (
                                              "TabContentTimesheets",
                                              "TabContentTimesheet2"))
                                ->setMenu($plugin_timesheet_menu)
                                ->setMessage('welcome_timesheet_plugin')
                                ->setDefaultPage("TabContentTimesheets");
                                   
   // Hook to display this plug-in Configuration in the tab Settings.
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("TimesheetsPlugIn"));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName("Timesheets Setting")
                                        ->setTitle("Timesheets Setting or configuration sample")
                                        ->setPages(Array ("SettingContentTimesheet"))
                                        ->setDefaultPage("SettingContentTimesheet");


   // Register the SamplePage, its just a plug-in Page not linked to a Tab or SettingTab.
   $GLOBALS['cfg_plugin_page']->append(new PlugIn("TimesheetsPlugIn"));
   $GLOBALS['cfg_plugin_page']->next();
   $GLOBALS['cfg_plugin_page']->current()
                              ->setPages(Array ("TimesheetsPage"));

?>