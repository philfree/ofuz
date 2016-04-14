<?php
// Copyright 2008-2011 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/
  /**
   * TimesheetsPlugIn configuration
   * This is a configuration file for the Sample plugin.
   * Its load class and set hooks 
   *
   * @package Timesheets Plugin
   * @author varaprasad ch <varaprasad@htmlfusion.com>
   * @license ##License##
   * @version 0.1
   * @date 2016-03-09  
   */

  $plugins_info['TimesheetsPlugIn'] = 
                    Array ('name' => 'Timesheets Add-On',
                           'description' => 'This add marketing capabilities with Email sending, web forms and auto-responders','version' => '0.0.5',
                           'status' => 'devel',
                           'tabs' => Array ('Timesheets'),
                           'blocks' => Array('BlockTimesheets')
                           );   
                            
    // blocks
    include_once("plugin/TimesheetsPlugIn/class/BlockTimesheets.class.php");
    include_once("plugin/TimesheetsPlugIn/class/TimesheetsPlugIn.class.php");
    
    //define('OFUZ_LOG_RUN_PLUGIN_MARKETING', true); 

   $cfg_sample_plugin_path = "/Tab/TimesheetsPlugIn/";
  
   // Hook for the block object
   $GLOBALS['cfg_block_placement']['TimesheetsPage'][] = "BlockTimesheets";
      
   $plugin_timesheet_menu = new SubMenu();
   $plugin_sample_menu->addMenuItem("page 1", $cfg_sample_plugin_path."TabContentTimesheet")
                      ->addMenuItem("page 2", $cfg_sample_plugin_path."TabContentTimesheet2");
                      
   // Hook to display the Tab (they are real page just without the .php)   
   $GLOBALS['cfg_tab_placement']->append(new Tab("TimesheetsPlugIn"));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName("Timesheets")
                                ->setMessage("welcome_timesheet")
                                ->setPages(Array (
                                              "TimesheetsPage"
                                            ))
                                ->setMenu($plugin_timesheet_menu)
                                ->setDefaultPage("TimesheetsPage");
                                   
  $GLOBALS['cfg_submenu_placement']['SendMessage'] = '';
  $GLOBALS['cfg_submenu_placement']['SaveTemplate'] = '';

?>