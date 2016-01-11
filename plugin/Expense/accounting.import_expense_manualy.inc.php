<?php
    /**
     *  import expense manualy
     *  Check if the expense is inserted 
     *  and delete the entry in the import_export
     */

    $insertid = $this->getParam("insertid");
    $doSave = $this->getParam("doSave");
    $idexpense_import = $this->getParam("idexpense_import");
    $this->setLogRun(true);
    $this->setLog("\n Delete expense from expense_import, doSave:".$doSave." - ".$insertid);
    if ($doSave == "yes" && $insertid > 0) {
        $do_import_expense = new DataObject($this->getDbCon());
        $do_import_expense->setLogRun(true);
        $do_import_expense->setTable("expense_import");
        $do_import_expense->getId($idexpense_import);
        $do_import_expense->delete();
    }

    $this->setLogRun(false);
?>