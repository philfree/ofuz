<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
    * Ofuz Blocks and Tabs
    * This will load all the classes related to the blocks
    * From the core and plug-ins.
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package OfuzCore
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-03
    * @since 0.6
    */
  
    
    // Core Block positioning 
    $GLOBALS['cfg_block_placement'] = Array(
                                  "invoice" => 
                                    Array("PaymentLogBlock",
                                          "RecurrentInvoiceBlock"),
                                          "invoices" =>
                                    Array("InvoicesMonthlyGraphBlock",
                                          "InvoicesYTDBlock"),
                                 "contact" => 
                                    Array("ContactDetailBlock", 
                                          "ContactInvoiceBlock",
                                          "ContactAddTaskBlock",
                                          "ContactShareCoworkerBlock",
                                         // "ContactShareFileNoteBlock",
                                          "ContactTasksBlock"),
                                  "contacts" =>
                                    Array("ContactSubTagSearchBlock",
                                          "ContactTagSearchBlock"),
                                  "index" =>
                                    Array("DashboardTodaysTask",
                                          "DashboardMessageBlock"
                                        ),
                                  "tasks" =>
                                    Array("TasksAddTaskBlock"),
                                  "task" =>
                                    Array("TaskProgressBlock",
                                          "TaskOwnerBlock",
                                          "TaskDropBoxBlock"
                                          ),
                                  "projects" =>
                                    Array("ProjectsAddProjectBlock"),
                                  "project" =>
                                    Array("ProjectAddProjectTaskBlock",
                                          "ProjectAddCoworkerBlock",
                                          "ProjectAddTaskDropboxBlock",
                                          "ProjectDiscussionEmailAlertBlock"
                                        ),
                                   "co_workers"=>
                                      Array("CoworkerSendInvitationEmail",
                                            "CoworkerSearch",
                                            "CoworkerListInvitations",
                                            ),
                                    "timesheet"=>
                                       Array("TimesheetBlockCoWorker")
                          ) ;



        // Core plugin names
    $GLOBALS['core_plugin_names'] = Array(
                                          'ContactAddTaskBlock',
                                          'ContactDetailBlock',
                                          'ContactInvoiceBlock',
                                          'ContactShareCoworkerBlock',
                                          'ContactShareFileNoteBlock',
                                          'ContactSubTagSearchBlock',
                                          'ContactTagSearchBlock',
                                          'ContactTasksBlock',
                                          'CoworkerListInvitations',
                                          'CoworkerSearch',
                                          'CoworkerSendInvitationEmail',
                                          'DashboardMessageBlock',
                                          'DashboardTodaysTask',
                                          'InvoicesMonthlyGraphBlock',
                                          'InvoicesYTDBlock',
                                          'NotesAndDiscussionBlock',
                                          'PaymentLogBlock.class',
                                          'ProjectAddCoworkerBlock',
                                          'ProjectAddProjectTaskBlock',    
                                          'ProjectAddTaskDropboxBlock',
                                          'ProjectDiscussionEmailAlertBlock',
                                          'ProjectsAddProjectBlock',
                                          'RecurrentInvoiceBlock',
                                          'TaskDropBoxBlock',
                                          'TaskOwnerBlock',
                                          'TaskProgressBlock',
                                          'TasksAddTaskBlock',
                                          'TimesheetBlockCoWorker'
                                          
                          ) ;  
 
   // Core Tabs 
   $GLOBALS['cfg_tab_placement'] = new ArrayIterator();

   //Dashboard
   $GLOBALS['cfg_tab_placement']->append(new Tab(""));
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName(_('Dashboard'))
                                ->setPages(Array (
                                      "index",
                                      "daily_notes",
                                      "timesheet"))
                                ->setDefaultPage("index");
                                
   //Contact      
   $GLOBALS['cfg_tab_placement']->append(new Tab(""));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName(_('Contacts'))
                                ->setPages(Array (
                                    "contacts",
                                    "contact",
                                    "contact_edit",
                                    "contact_share_settings"))
                                ->setDefaultPage("contacts");
   //Tasks
   $GLOBALS['cfg_tab_placement']->append(new Tab(""));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName(_('Tasks'))
                                ->setPages(Array ("tasks"))
                                ->setDefaultPage("tasks");
   //Projects
   $GLOBALS['cfg_tab_placement']->append(new Tab(""));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName(_('Projects'))
                                ->setPages(Array (
                                    "projects",
                                    "project",
                                    "prject_done"
                                  ))
                                ->setDefaultPage("projects");
   //Invoices
   $GLOBALS['cfg_tab_placement']->append(new Tab(""));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName(_('Invoices'))
                                ->setPages(Array (
                                    "invoices",
                                    "invoice"))
                                ->setDefaultPage("invoices");

  

   $GLOBALS['core_tab_name'] = Array(
                                      'Dashboard',
                                      'Contacts',
                                      'Tasks',
                                      'Projects',
                                      'Invoices'
                                    );


    $GLOBALS['core_setting_tab_name'] = Array(
                                      'My Information',
                                      'My Profile',
                                      'Web Forms',
                                      'Email Templates',
                                      'Auto Responder',
                                      'Sync Contacts',
                                      'API Key',
                                      'Email Stream',
                                      'Discussion Email Alert',
                                      'Invoice Settings',
                                      'Cancel Account',
                                      'Export',
                                      'My Account Backup'                                     
                                    );


   // Core Settings Tabs 
   $GLOBALS['cfg_setting_tab_placement'] = new ArrayIterator();

   //My Information
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("My Information"))
                                        ->setPages(Array ("settings_info"))
                                        ->setDefaultPage("settings_info");
   //My Profile
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("My Profile"))
                                        ->setPages(Array ("settings_myinfo"))
                                        ->setDefaultPage("settings_myinfo");

   //Web Forms
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Web Forms"))
                                        ->setPages(Array ("settings_wf"))
                                        ->setDefaultPage("settings_wf");

   //Email Templates
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Email Templates"))
                                        ->setPages(Array ("settings_email_templ"))
                                        ->setDefaultPage("settings_email_templ");

   //Auto Responder
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Auto Responder"))
                                        ->setPages(Array ("settings_auto_responder"))
                                        ->setDefaultPage("settings_auto_responder");

   //Sync
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Sync Contacts"))
                                        ->setPages(Array ("sync"))
                                        ->setDefaultPage("sync");

   //Twitter Setup
   /** Hiding is as its part of the Sync Contact tab.
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Twitter Setup"))
                                        ->setPages(Array ("settings_twitter"))
                                        ->setDefaultPage("settings_twitter");
   **/
   //API Key
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("API Key"))
                                        ->setPages(Array ("api_key"))
                                        ->setDefaultPage("api_key");

   //Email Stream
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Email Stream"))
                                        ->setPages(Array ("email_stream"))
                                        ->setDefaultPage("email_stream");

   //Task Discussion Email Alert
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Discussion Email Alert"))
                                        ->setPages(Array ("settings_discussion_alert"))
                                        ->setDefaultPage("settings_discussion_alert");

   //Google Gears
   /** Started to move this into a plug-in
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Offline Support"))
                                        ->setPages(Array ("settings_ggears"))
                                        ->setDefaultPage("settings_ggears");
   */
   
   //Invoice Settings
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Invoice Settings"))
                                        ->setPages(Array ("settings_invoice"))
                                        ->setDefaultPage("settings_invoice");

   //Cancel Account
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Cancel Account"))
                                        ->setPages(Array ("cancel_account"))
                                        ->setDefaultPage("cancel_account");

   //Export
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Export"))
                                        ->setPages(Array ("settings_export"))
                                        ->setDefaultPage("settings_export");

   //Backup
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("My Account Backup"))
                                        ->setPages(Array ("settings_export_user_ofuz_ac"))
                                        ->setDefaultPage("settings_export_user_ofuz_ac");



   // For plug-in without pages
   $GLOBALS['cfg_plugin_page'] = new ArrayIterator();
   $GLOBALS['cfg_plugin_page']->append(new PlugIn(""));
  //Plugin
   /*$GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Plugins"))
                                        ->setPages(Array ("settings_plugin"))
                                        ->setDefaultPage("settings_plugin");*/
                   
    // Added to include Plugins
    $d3 = dir($cfg_project_directory."class/block/");
    while($entry = $d3->read()) {
        if (preg_match("/\.class\.php$/i", $entry) && !preg_match("/^\./", $entry)) {
            $config_files_core_plugin[] = $entry;
        }
    }
    $d3->close();

    if (is_array($config_files_core_plugin)) {
        sort($config_files_core_plugin) ;
        foreach($config_files_core_plugin as $config_file_core_plugin) {
          include_once($cfg_project_directory."class/block/".$config_file_core_plugin);       
        }  
    }

    $d3 = dir($cfg_project_directory."plugin/");
    while($entry = $d3->read()) {
        if (preg_match("/\.conf\.inc\.php$/i", $entry) && !preg_match("/^\./", $entry)) {
            $config_files_plugin[] = $entry;
        }
    }
    $d3->close();

    if (is_array($config_files_plugin)) {
        sort($config_files_plugin) ;
		foreach($config_files_plugin as $config_file_plugin) {
		  include_once($cfg_project_directory."plugin/".$config_file_plugin);       
		}  
    }

?>
