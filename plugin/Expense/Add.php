<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Add an Expense
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzPage
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.4
     */

 ?>
<div class="banner50 pad020 text16 fuscia_text"><?php echo _('Add an Expense'); ?></div>
<?php 
  $do_expense = new Expense();
  $do_expense->setFields(new Fields("expense", $cfg_plugin_expense_path));
  $do_expense->prepareForm($cfg_plugin_expense_uri."All");
  $do_expense->form();
?>
