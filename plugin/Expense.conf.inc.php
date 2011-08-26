<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/
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

   $cfg_plugin_expense_uri = "/Tab/Expense/";
   $cfg_plugin_expense_path = "plugin/Expense/";
   define('OFUZ_LOG_PLUGIN_EXPENSE', true);

   // Classes
    include_once($cfg_plugin_expense_path."class/Expense.class.php");
    include_once($cfg_plugin_expense_path."class/ExpenseImport.class.php");
    include_once($cfg_plugin_expense_path."class/BlockTmpExpenseMenu.class.php");
	
   // Hook for the block object
   $GLOBALS['cfg_block_placement']['All'][] = "BlockTmpExpenseMenu";
 //  $GLOBALS['cfg_block_placement']['WebForm'][] = "BlockWebFormList";
 //  $GLOBALS['cfg_block_placement']['MEmailTemplate'][] = "BlockEmailTemplateList";
   
   $plugin_expense_menu = new SubMenu();
   $plugin_expense_menu->addMenuItem("All", $cfg_plugin_expense_uri."All")
                       ->addMenuItem("Add New Expense", $cfg_plugin_expense_uri."Add")
                         ->addMenuItem("Import", "/Tab/Expense/Import")
                         ->addMenuItem("Profit & Loss", "/Tab/Expense/ProfitAndLoss");
  

   // Hook to display the Tab (they are real page just without the .php)   

   $GLOBALS['cfg_tab_placement']->append(new Tab("Expense"));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                                ->setTabName("Expenses")
                                ->setMessage("welcome_expenses")
                                ->setPages(Array (
                                              "All",
                                              "Add",
                                              "Import",
                                              "ImportManual"
                                            ))
                                ->setMenu($plugin_expense_menu)
                                ->setDefaultPage("All");
                                  

?>
