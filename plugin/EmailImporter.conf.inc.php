<?php
// Copyright 2008-2011 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/
  /**
   * EmailImporter configuration
   * This is a configuration file for the email contacts importer.
   * It loads class and set hooks 
   *
   * @package EmailImporter
   * @author Ravi Rokkam <ravi@sqlfusion.com>
   * @license ##License##
   * @version 0.1
   * @date 2011-11-18  
   */

   include_once("plugin/EmailImporter/OfuzEmailImporter.class.php");
   require_once("plugin/EmailImporter/openinviter.php");

   // Hook to display the tab Settings.
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("EmailImporter"));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName("Email Contacts Import")
                                        ->setTitle("Email Contacts Import")
                                        ->setPages(Array ("import_email_contacts"))
                                        ->setDefaultPage("import_email_contacts");

                                                                            
?>
