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



/**
   tab enable disable is with setTabName("SamplePlugIn")
   setting menu is with setTabName("Sample Plugin Setting");
   block with TabContentSample

**/



// Plug-in Definition
// status : devel, alpha, beta, rc, stable
   $plugins_info['SamplePlugin'] = 
                    Array ('name' => 'Sample Plugin',
                           'description' => 'This is a sample description',
                           'version' => '0.0.1',
                           'status' => 'beta',
                           'tabs' => Array ('SamplePlugin'),
                           'settings' => Array('SamplePlugin'),
                           'plugins' => Array('SamplePlugin'),
                           'blocks' => Array('BlockSample')
                           );                          

   // We include here our Block Object
   include_once("plugin/SamplePlugIn/BlockSample.class.php");

   $cfg_sample_plugin_path = "/Tab/SamplePlugIn/";

   // Hook for the block object
   $GLOBALS['cfg_block_placement']['TabContentSample'][] = "BlockSample";

   $plugin_sample_menu = new SubMenu();
   $plugin_sample_menu->addMenuItem("page 1", $cfg_sample_plugin_path."TabContentSample")
                      ->addMenuItem("page 2", $cfg_sample_plugin_path."TabContentSamplePage2");
         
   // Hook to display the Tab (they are real page just without the .php)  
   $GLOBALS['cfg_tab_placement']->append(new Tab("SamplePlugIn"));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName("SamplePlugIn")
                                ->setTitle("You are on the Sample Plugin")
                                ->setPages(Array (
                                              "TabContentSample",
                                              "TabContentSamplePage2"))
                                ->setMenu($plugin_sample_menu)
                                ->setMessage('welcome_sample_plugin')
                                ->setDefaultPage("TabContentSample");
                                   
   // Hook to display this plug-in Configuration in the tab Settings.
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("SamplePlugIn"));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName("Sample Plugin Setting")
                                        ->setTitle("Sample Plug In Setting or configuration sample")
                                        ->setPages(Array ("SettingContentSample"))
                                        ->setDefaultPage("SettingContentSample");


   // Register the SamplePage, its just a plug-in Page not linked to a Tab or SettingTab.
   $GLOBALS['cfg_plugin_page']->append(new PlugIn("SamplePlugIn"));
   $GLOBALS['cfg_plugin_page']->next();
   $GLOBALS['cfg_plugin_page']->current()
                              ->setPages(Array ("SamplePage"));


  //By default the menu will display in all pages of your plug-in
  //To set specify which menu display on which page use this array
  // $GLOBALS['cfg_submenu_placement']['TabContentSample'] = $plugin_sample_menu;
  // $GLOBALS['cfg_submenu_placement']['TabContentSamplePage2'] = $plugin_sample_menu;


?>
