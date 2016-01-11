<?php
    /**
     * Accounting import expenses from wellsfargo
     * 
     */

    $this->setLog(true);
    $this->setLog("\n Importing: expenses 2008");
    $goto = $this->getParam("goto");
    $import_file = $this->getParam("import_file");
    if (empty($import_file)) {
        $import_file = "import/expenses_2009.csv";
    }
    $do_expense_import = new DataObject($this->getDbCon());
    $do_expense_import->setTable("expense_import");

//   amount  Float Number (Float)   
//   category  One Line text   
//   debit_date  Date (SQL)   
//   description  One Line text   
//   idexpense_import  Simple Number (Integer)   
//   payment_method  One Line text

    function format_sql_date($sheet_date) {
        list($month, $day, $year) = explode("/",$sheet_date);
        return "20".$year."-".$month."-".$day;
    }
    function money2float($money_string) {
        $money_float = str_replace("$", "", $money_string);
        $money_float = str_replace(",", "", $money_float);
        return $money_float;
    }

    if (file_exists($import_file)) {
        $fp = fopen($import_file, "r");
        $i=0;
        $total = 0;
        while (($fields = fgetcsv($fp))) {
            $i++; 
            $do_expense_import->newRecord();
            $do_expense_import->category = $fields[0];
            $do_expense_import->debit_date = format_sql_date($fields[1]);
            $do_expense_import->description = $fields[3];
            $do_expense_import->payment_method = $fields[4];
            $do_expense_import->amount = money2float($fields[5]);
            $do_expense_import->add();
            $total += money2float($fields[5]);
            $this->setLog("\n insert with: ".$do_expense_import->getSqlQuery());
        }
    }
    $this->setLog("\n imported a total of: ".$total);

    $disp = new Display($goto);
    $disp->addParam("message", "Import completed");
    $this->setDisplayNext($disp);

?>
