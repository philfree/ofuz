<?php
    /**
     * Import expenses
     */
    include_once("config.php");

    include("includes/header.inc.php");
?>
<h2>Import expenses manualy</h2>
<div class="info_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div>



<?php

   // mysql> desc expense;
    // +-------------+--------------+------+-----+------------+----------------+
    // | Field       | Type         | Null | Key | Default    | Extra          |
    // +-------------+--------------+------+-----+------------+----------------+
    // | idexpense   | int(10)      |      | PRI | NULL       | auto_increment |
    // | num         | int(10)      |      |     | 0          |                |
    // | suplier     | int(10)      |      | MUL | 0          |                |
    // | description | varchar(200) |      |     |            |                |
    // | daterecieve | date         |      |     | 0000-00-00 |                |
    // | amount      | float(10,2)  |      |     | 0.00       |                |
    // | Taxes       | float(10,2)  |      |     | 0.00       |                |
    // | discount    | float(10,2)  |      |     | 0.00       |                |
    // | datepayed   | date         |      |     | 0000-00-00 |                |
    // | type        | varchar(20)  |      |     |            |                |
    // | checknum    | varchar(25)  |      |     |            |                |
    // +-------------+--------------+------+-----+------------+----------------+

    // mysql> desc expense_import;
    // +------------------+--------------+------+-----+------------+----------------+
    // | Field            | Type         | Null | Key | Default    | Extra          |
    // +------------------+--------------+------+-----+------------+----------------+
    // | idexpense_import | int(10)      |      | PRI | NULL       | auto_increment |
    // | category         | varchar(60)  |      |     |            |                |
    // | debit_date       | date         |      |     | 0000-00-00 |                |
    // | description      | varchar(250) |      |     |            |                |
    // | payment_method   | varchar(60)  |      |     |            |                |
    // | amount           | float(10,2)  |      |     | 0.00       |                |
    // +------------------+--------------+------+-----+------------+----------------+


    $do_import_expense = new DataObject($GLOBALS['conx']);
    $do_import_expense->setTable("expense_import");

    $do_import_expense->query("select * from expense_import order by debit_date desc limit 1");
    if ($do_import_expense->getNumRows() > 0) {
    $do_expense = new DataObject($GLOBALS['conx']);
    $do_expense->setTable("expense");
    $do_expense->num = $do_import_expense->idexpense_import;
    $do_expense->description = $do_import_expense->description;
    $do_expense->datepayed = $do_import_expense->debit_date;
    $do_expense->daterecieve = $do_import_expense->debit_date;
    $do_expense->amount = $do_import_expense->amount;
    $do_expense->prepareForm();
    $do_expense->form->event->addEvent("accounting.import_expense_manualy", 1010);
    $do_expense->form->event->addParam("idexpense_import", $do_import_expense->idexpense_import);
    //$do_expense->form->setSubmit("");

    $e_skip = new Event("accounting.import_expense_manualy");
    $e_skip->addParam("idexpense_import", $do_import_expense->idexpense_import);
    $e_skip->addParam("doSave", "yes");
    $e_skip->addParam("insertid", 1);
    echo $e_skip->getLink("Do not import this expense");

    $do_expense->form();
    } else {
        echo "No more expenses to import.";
    }




?>


<?php
    include("includes/footer.inc.php");
?>