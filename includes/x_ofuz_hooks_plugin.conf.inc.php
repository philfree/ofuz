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
    * @version 1.0
    * @date 2012-01-07
    * @since 0.6
    */
  
    
    // Core Block positioning 
    // The index of the array is the page script name (without .php) or plugin name. The in an array add all the block class names to display in that page.
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


    // Core Block positioning 
    // The key is the page script name without .php or the plugIn name
    $GLOBALS['cfg_plugin_eventmultiple_placement'] = 
                              Array(
                                  'contacts' => 
                                    Array( 
                                       Array('name' => 'Merge In One',
                                             'confirm' => 'Are you sure you want to merge the selected contacts?',
                                             'event' => '',
                                             'action' => 'merge_automated.php',
                                             'plugin' => 'Core'
                                             )
                                          )
                                    );
    // The key is the page script name without .php or the plugIn name                                         
    $GLOBALS['cfg_submenu_placement']['index'] = new SubMenu();
    $GLOBALS['cfg_submenu_placement']['index']->addMenuItem(_('Work Feed'), "index.php")
										      ->addMenuItem(_('Notes & Discussion'), "daily_notes.php");
	//										  ->addMenuItem(_('Timesheet'), "timesheet.php");   
    $GLOBALS['cfg_submenu_placement']['daily_notes'] = new SubMenu();
    $GLOBALS['cfg_submenu_placement']['daily_notes']->addMenuItem(_('Work Feed'), "index.php")
										      ->addMenuItem(_('Notes & Discussion'), "daily_notes.php");
	//										  ->addMenuItem(_('Timesheet'), "timesheet.php");   
	/**										  
	$GLOBALS['cfg_submenu_placement']['timesheet'] = new SubMenu();
    $GLOBALS['cfg_submenu_placement']['timesheet']->addMenuItem(_('Work Feed'), "index.php")
										      ->addMenuItem(_('Notes & Discussion'), "daily_notes.php")
											  ->addMenuItem(_('Timesheet'), "timesheet.php");  	
	*/	
	$GLOBALS['cfg_submenu_placement']['timesheet'] = new SubMenu();
	$GLOBALS['cfg_submenu_placement']['timesheet']->addMenuItem(_('Open Projects'), "projects.php")
	                                              ->addMenuItem(_('Closed Projects'), "projects_closed.php")
												  ->addMenuItem(_('Timesheet'), "timesheet.php");                           
    $GLOBALS['cfg_submenu_placement']['tasks'] = new SubMenu();
    $GLOBALS['cfg_submenu_placement']['tasks']->addMenuItem(_('Task By Projects'), "tasks_by_project.php")
											  ->addMenuItem(_('Task By Date'), "tasks.php")
                                              ->addMenuItem(_('Completed'), "tasks_completed.php") ;
    $GLOBALS['cfg_submenu_placement']['settings_plugin'] = new SubMenu();
    $GLOBALS['cfg_submenu_placement']['settings_plugin']->addMenuItem(_('Add-On'), "enable_plugin.php")
											  ->addMenuItem(_('Detail List'), "settings_plugin.php"); 

	$GLOBALS['cfg_submenu_placement']['projects_closed'] = new SubMenu();										  
    $GLOBALS['cfg_submenu_placement']['projects_closed']->addMenuItem(_('Open Projects'), "projects.php");
    $GLOBALS['cfg_submenu_placement']['projects'] = new SubMenu();	
    $GLOBALS['cfg_submenu_placement']['projects']->addMenuItem(_('Closed Projects'), "projects_closed.php")
												->addMenuItem(_('Timesheet'), "timesheet.php");   
 									
 	$GLOBALS['cfg_submenu_placement']['project'] = new SubMenu();		
 	$GLOBALS['cfg_submenu_placement']['project']->addMenuItemJSCallback(_('Edit'), "fnEditProject()")
												->addMenuItemJSCallback(_('Filter'), "fnFilterProject()");
 	// Project tasks:
  	$GLOBALS['cfg_submenu_placement']['task'] = new SubMenu();		
 	$GLOBALS['cfg_submenu_placement']['task']->addMenuItemJSCallback(_('Edit'), "fnEditTask()");												  
											     
	$GLOBALS['cfg_submenu_placement']['help'] = ''; // No sub menu for now. 										                                          
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
                                          'PaymentLogBlock',
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

     // Is this still in use ?
    $GLOBALS['core_setting_tab_name'] = Array(
                                      'My Information',
                                      'My Profile',
                                      'Sync Contacts',
                                      'API Key',
                                      'Email Stream',                                    
                                      'Invoice Settings',
                                      'Add On',
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
  /** moving those to the marketing plugin
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
**/
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

   //Email Stream
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Email Stream"))
                                        ->setPages(Array ("email_stream"))
                                        ->setDefaultPage("email_stream");

   /** temporary suspending this one as its a confusing setting
    *  This only releate to project so should be better integrated within projects.
   //Task Discussion Email Alert
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Discussion Email Alert"))
                                        ->setPages(Array ("settings_discussion_alert"))
                                        ->setDefaultPage("settings_discussion_alert");
    */
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
                                        
 
   //Enable Add-on
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Add On"))
                                        ->setPages(Array ("enable_plugin", "settings_plugin"))
                                        ->setDefaultPage("enable_plugin");
                                                                               
                                        
   //API Key
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting(""));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("API Key"))
                                        ->setPages(Array ("api_key"))
                                        ->setDefaultPage("api_key");
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

   // Include plug-in Configuration files
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
