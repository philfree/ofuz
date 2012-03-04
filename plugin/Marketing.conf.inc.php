<?php
// Copyright 2008-2012 SQLFusion LLC           info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html 
 **/
  /*
   * Marketing configuration
   * This is a configuration file for the Marketing plugin.
   * It loads class and sets hooks 
   *
   * @package EmailMarketing
   * @author Philippe Lewicki <phil@sqlfusion.com>
   * @license ##License##
   * @version 0.1
   * @date 2010-11-08
   */
   
   // Plug-in Definition
   // status: devel, alpha, beta, rc, stable
   $plugins_info['Marketing'] = 
                    Array ('name' => 'Marketing Add-On',
                           'description' => 'This add marketing capabilities with Email sending, web forms and auto-responders','version' => '0.0.5',
                           'status' => 'devel',
                           'tabs' => Array ('Marketing'),
                           'blocks' => Array('BlockMarketing', 'BlockWebFormList', 'BlockEmailTemplateList')
                           );   
                            
   // Classes
    include_once("plugin/Marketing/class/ContactMailing.class.php");
    include_once("plugin/Marketing/class/AutoResponder.class.php");
    include_once("plugin/Marketing/class/AutoResponderEmail.class.php");
    //include_once("plugin/Marketing/WebForm.class.php");    
    include_once("plugin/Marketing/class/WebFormField.class.php");   
    include_once("plugin/Marketing/class/WebFormUserField.class.php");    
    include_once("plugin/Marketing/class/WebFormUser.class.php");
    // blocks
    include_once("plugin/Marketing/class/BlockMarketing.class.php");
    include_once("plugin/Marketing/class/BlockWebFormList.class.php");    
    include_once("plugin/Marketing/class/BlockEmailTemplateList.class.php");
    
    define('OFUZ_LOG_RUN_PLUGIN_MARKETING', true); 

   $cfg_plugin_mkt_path = "/Tab/Marketing/";
	
   // Hook for the block object
   $GLOBALS['cfg_block_placement']['AutoResponderEmailEdit'][] = "BlockMarketing";
   $GLOBALS['cfg_block_placement']['WebForm'][] = "BlockWebFormList";
   $GLOBALS['cfg_block_placement']['MEmailTemplate'][] = "BlockEmailTemplateList";
   
   $GLOBALS['cfg_plugin_eventmultiple_placement']['contacts'][] = 

                                        Array('name'=> 'Send a Message',
                                             'confirm' => ' ',
                                             'event' => 'ContactMailing->eventGetForMailMerge',
                                             'action' => ' ',
                                             'plugin' => 'Marketing');

                                    //    Array('name'=> 'Send a Message ',
                                   //          'confirm' => '',
                                    //         'event' => 'ContactMailing->eventGetForMailMerge',
                                    //         'action' => '');
                   
  
   $plugin_marketing_menu = new SubMenu();
   $plugin_marketing_menu->addMenuItem("WebForms", "/Tab/Marketing/WebForm")
                         ->addMenuItem("Auto Responders", "/Tab/Marketing/AutoResponder")
                         ->addMenuItem("Email Template", "/Tab/Marketing/MEmailTemplate");
  

   // Hook to display the Tab (they are real page just without the .php)   
   $GLOBALS['cfg_tab_placement']->append(new Tab("Marketing"));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName("Marketing")
                                ->setMessage("welcome_marketing")
                                ->setPages(Array (
                                              "TabContentMarketing",
                                              "WebForm",
                                              "WebFormUrl",
                                              "MEmailTemplate",
                                              "settings_auto_responder_add",
                                              "settings_auto_responder",
                                              "AutoResponder",
                                              "AutoResponderEmail",
                                              "AutoResponderEmailEdit",
                                              "settings_auto_responder_edit",
                                              "SendMessage",
                                              "SaveTemplate"
                                            ))
                                ->setMenu($plugin_marketing_menu)
                                ->setDefaultPage("MEmailTemplate");
                                   
  // Use this array to customize the display of the menu in the pages.
  // $GLOBALS['cfg_submenu_placement']['TabContentMarketing'] = $plugin_marketing_menu;
  // $GLOBALS['cfg_submenu_placement']['AutoResponder'] = $plugin_marketing_menu;
  // $GLOBALS['cfg_submenu_placement']['AutoResponderEmail'] = $plugin_marketing_menu;
  // $GLOBALS['cfg_submenu_placement']['AutoResponderEmailEdit'] = $plugin_marketing_menu;
  // $GLOBALS['cfg_submenu_placement']['WebForm'] = $plugin_marketing_menu;
  // $GLOBALS['cfg_submenu_placement']['WebFormURL'] = $plugin_marketing_menu;   
  // $GLOBALS['cfg_submenu_placement']['MEmailTemplate'] = $plugin_marketing_menu;   
  $GLOBALS['cfg_submenu_placement']['SendMessage'] = '';
  $GLOBALS['cfg_submenu_placement']['SaveTemplate'] = '';

?>
