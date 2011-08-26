<?php
    /**
     * Import expenses
     */

?>
<h2 class="headline">Import expenses</h2>
<div class="info_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div>
<?php 
    $e_import_csv = new Event("ExpenseImport->eventImportCSV");
    $e_import_csv->addParam("goto", $GLOBALS['cfg_plugin_expense_uri']."Import");
    $e_import_csv->addParam("import_file", "importcsv/expenses_2010.csv");
    echo $e_import_csv->getLink("Import Expenses cvs file");
?> 
<br/><br/>
<?php 
    $e_process_filters = new Event("ExpenseImport->eventApplyFilters");
    $e_process_filters->addParam("goto", $GLOBALS['cfg_plugin_expense_uri']."Import");
    echo $e_process_filters->getLink("Load Expenses");
?>
<br/><br/>


<a href="<?php echo $GLOBALS['cfg_plugin_expense_uri']; ?>ImportManual">Manualy import expenses</a>
<br/>
<br/>
<?php
    $do_import_expense = new ExpenseImport();
    $do_import_expense->view();
?>

