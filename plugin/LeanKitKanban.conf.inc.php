<?php
// Copyright 2008-2011 SQLFusion LLC info@sqlfusion.com
/**COPYRIGHTS**/
  /**
* LeanKitKanban configuration
* This is a configuration file for the LeanKitKanban API integration.
* It loads class and set hooks
*
* @package LeanKitKanban
* @author SQLFusion
* @license ##License##
* @version 0.1
* @date 06-26-2012
*/


// Plug-in Definition
// status : devel, alpha, beta, rc, stable
$plugins_info['LeanKitKanban'] = 
		Array ('name' => 'Ofuz LeanKit Kanban Integration',
			'description' => 'This Plug-in integrates LeanKit Kanban API with Ofuz.',
			'version' => '0.0.1',
			'status' => 'beta',
			//'tabs' => Array ('LeanKitKanban'),
			'settings' => Array('LeanKit Kanban Authentication'),                          
			'blocks' => Array('BlockLeanKitKanbanTaskDiscussion')
			);  

include_once("plugin/LeanKitKanban/class/LeanKitKanban.class.php");
include_once("plugin/LeanKitKanban/class/OfuzLeanKitKanban.class.php");
include_once("plugin/LeanKitKanban/class/BlockLeanKitKanbanTaskDiscussion.class.php");


// Hook for the block object
$GLOBALS['cfg_block_placement']['task'][] = "BlockLeanKitKanbanTaskDiscussion";

// Hook to display this plug-in Configuration in the tab Settings.
$GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("LeanKitKanban"));
$GLOBALS['cfg_setting_tab_placement']->next();
$GLOBALS['cfg_setting_tab_placement']->current()
				    ->setTabName("LeanKit Kanban Authentication")
				    ->setTitle("Set up your LeanKit Kanban login Credentials:")
				    ->setPages(Array ("leankit_kanban_authentication"))
				    ->setDefaultPage("leankit_kanban_authentication");
                                                                            
?>