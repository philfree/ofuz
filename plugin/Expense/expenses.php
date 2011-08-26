<?php
    /**
     * View the expenses
     */
    include_once("config.php");
    include("includes/header.inc.php");
?>
<a href="import_expenses.php">Import expenses</a>
<?php
if (empty($_SESSION['expense_view_year'])) {
    $_SESSION['expense_view_year'] = date("Y");
}
if (empty($_SESSION['expense_view_month'])) {
    $_SESSION['expense_view_month'] = date("m");
}

$f_expense_filter = new ReportForm($GLOBALS['conx']);
$f_expense_filter->setNoData(true);  // Flag to turn off search for database table data (update or add record)

    $reg_filter = new Registry($GLOBALS['conx']);
        $field_month = new strFBFieldTypeListBoxSmall("expense_view_month");
        $field_month->listvalues = "01:02:03:04:05:06:07:08:09:10:11:12";
        $field_month->listlabels = "January:February:March:April:May:June:July:August:September:October:November:December";
        $field_month->label = "Month";
        $field_month->default = "[expense_view_month]";
    $reg_filter->addField($field_month);
    
        $field_year = new strFBFieldTypeListBoxSmall("expense_view_year");
        $field_year->listvalues = "2003:2006:2007:2008:2009";
        $field_year->listlabels = "2003:2006:2007:2008:2009";
        $field_year->label = "Year";
        $field_year->default = "[expense_view_month]"; 
    $reg_filter->addField($field_year);


$f_expense_filter->setRegistry($reg_filter);
$f_expense_filter->setValue("expense_view_month", $_SESSION['expense_view_month']);
$f_expense_filter->setValue("expense_view_year", $_SESSION['expense_view_year']);
// echo $f_expense_filter->getTable();
$f_expense_filter->setDefault();
$f_expense_filter->setEvent("accounting.expenses_filter_view_set_values");
$f_expense_filter->setNoData(true);
$f_expense_filter->execute();


?>

<?php 
    $do_expense = new Expense($GLOBALS['conx']);
    $do_expense->setViewTemplate("search_report");
    $do_expense->setSavedQuery("expenses_search_display");
    $do_expense->setRegistry("expense_view");
    //$do_expense->get_expenses_search_display();
   // $do_expense->prepareView();
   // echo $do_expense->view->getNoData();
   // $do_expense->view->setMaxRows(20);
   // $do_expense->view->setQuery();

    $do_expense->view();
?>

<?php
    include("includes/footer.inc.php");
?>