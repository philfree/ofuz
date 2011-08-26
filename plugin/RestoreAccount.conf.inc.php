<?php
// Copyright 2008 SQLFusion LLC           info@sqlfusion.com
  /**
   * Config for the plugin PluginSettingImportAccount
   *
   */
   // include plugin_name/class_file

   include_once("RestoreAccount/RestoreAccount.class.php");
//   $cfg_setting_tab_placement[] = "RestoreAccount";
   
      // Hook to display the tab Settings.
      
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("RestoreAccount"));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Restore My Account"))
                                        ->setTitle(_("Restore a full Ofuz account from backup"))
                                        ->setPages(Array ("import_account"))
                                        ->setDefaultPage("import_account");


?>