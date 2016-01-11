<?php
    /** 
     * Event set expenses view
     */

    $fields = $this->getParam("fields");

    $_SESSION['expense_view_month'] = $fields['expense_view_month'];
    $_SESSION['expense_view_year'] = $fields['expense_view_year'];

    $this->setUrlNext("expenses.php");

?>