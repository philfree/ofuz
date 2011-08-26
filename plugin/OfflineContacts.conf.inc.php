<?php
// Copyright 2008-2011 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/
  /**
   * Offline contacts plugin
   * This is a configuration file for the Sample plugin.
   * Its load class and set hooks 
   *
   * @package OfflineContacts
   * @author SQLFusion Dream team <phil@sqlfusion.com>
   * @license ##License##
   * @versnion 0.1
   * @date 2011-01-14  
   */

   // We include here our Block Object
  // include_once("plugin/SamplePlugIn/BlockSample.class.php");

   // Hookfor the block object
  // $GLOBALS['cfg_block_placement']['TabContentSample'][] = "BlockSample";

                                  
   // Hook to display the tab Settings.
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("OfflineContacts"));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName("Offline Contacts")
                                        ->setTitle("Offline support for Ofuz Contacts")
                                        ->setPages(Array ("setting"))
                                        ->setDefaultPage("setting");




?>
