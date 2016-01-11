<?php 
    /**
     * Load expenses in the from import_expense to the expenses table
     */

    $goto = $this->getParam("goto");



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

    $do_import_expense = new DataObject($this->getDbCon());
    $do_import_expense->setTable("expense_import");
    $do_import_expense->getAll();

    $do_expense = new DataObject($this->getDbCon()) ;
    $do_expense->setTable("expense");


    while($do_import_expense->next()) {
        $category = 0;
        $suplier = 0;
        if ($do_import_expense->category == "Airlines / Transportation") {
            $category = 10;
        }
        if ($do_import_expense->category == "ATM Withdrawals") {
            $category = 46;
        }
        if ($do_import_expense->category == "Auto / Gas") {
            $category = 8;
        }
        if ($do_import_expense->category == "Building Supply / Wholesale") {
            $category = 25;
        }
//        if ($do_import_expense->category == "Building Supply / Wholesale") {
//            $category = 25;
//        }
        if ($do_import_expense->category == "Entertainment") {
            $category = 9;
        }
        if ($do_import_expense->category == "Groceries") {
            $category = 11;
        }
        if ($do_import_expense->category == "Insurance / Financial Services") {
            $category = 23;
        }
        if ($do_import_expense->category == "Office Supply / Stationery") {
            $category = 11;
        }
        if ($do_import_expense->category == "Postage / Delivery") {
            $category = 26;
        }
        if ($do_import_expense->category == "Restaurants") {
            $category = 43;
        }
        if ($do_import_expense->category == "Utilities / Telecom") {
            $category = 12;
        }

        if (eregi("MARCO TABINI", $do_import_expense->description)) {
            $category = 19;
            $suplier = 90;
        }
        if (eregi("DIRECTNIC COM", $do_import_expense->description)) {
            $category = 2;
            $suplier = 32;
        }
        if (eregi("GANDI", $do_import_expense->description)) {
            $category = 2;
        }        

        if (eregi("THAWTE INC", $do_import_expense->description)) {
            $category = 2;
            $suplier = 91;
        }
        if (eregi("GEOTRUST", $do_import_expense->description)) {
            $category = 2;
            $suplier = 96;
        }

        if (eregi("EFAX", $do_import_expense->description)) {
            $category = 2;
        }
        if (eregi("BANKCARD", $do_import_expense->description)) {
            $category = 3;
            $suplier = 3;
        }
        if (eregi("ADP TX", $do_import_expense->description)) {
            $category = 35;
        }
        if (eregi("Arctern", $do_import_expense->description)) {
            $category = 27;
            $suplier = 92;
        }
        if (eregi("NETFLIX", $do_import_expense->description)) {
            $category = 19;
            $suplier = 77;
        }
        if (eregi("BLUE SHIELD", $do_import_expense->description)) {
            $category = 47;
            $suplier = 93;
        }
        if (eregi("NAVISITE", $do_import_expense->description)) {
            $category = 2;
            $suplier = 94;
        }
         if (eregi("NET2EZ", $do_import_expense->description)) {
            $category = 2;
            $suplier = 120;
        }       
        if (eregi("ADP", $do_import_expense->description)) {
            $category = 27;
            $suplier = 95;
        }
        if (eregi("FRY'S", $do_import_expense->description)) {
            $category = 13;
            $suplier = 13;
        }
        if (eregi("OVERTURE", $do_import_expense->description)) {
            $category = 16;
            $suplier = 97;
        }
        if (eregi("YAHOO SEARCH", $do_import_expense->description)) {
            $category = 16;
            $suplier = 97;
        }
        if (eregi("CISLO", $do_import_expense->description)) {
            $category = 15;
            $suplier = 85;
        }
        //if (eregi("BANKCARD", $do_import_expense->description)) {
        //    $category = 3;
        //    $suplier = 3;
        //}
        if (eregi("VERIZON", $do_import_expense->description)) {
            $category = 12;
            $suplier = 56;
        }                
        if (eregi("CINGULAR", $do_import_expense->description) || eregi("IPHONE", $do_import_expense->description)) {
            $category = 12;
            $suplier = 57;
        } 

        if (eregi("CALLCENTRIC", $do_import_expense->description)) {
            $category = 12;
            $suplier = 107;
        }  
        if (eregi("SIPPHONE", $do_import_expense->description)) {
            $category = 12;
            $suplier = 108;
        }  
        if (eregi("AMZN PMTS", $do_import_expense->description)) {
            $category = 2;
            $suplier = 110;
        }        
        if (eregi("OFFICE DEPOT", $do_import_expense->description)) {
            $category = 12;
            $suplier = 36;
        }  
        if (eregi("STAPLES", $do_import_expense->description)) {
            $category = 12;
            $suplier = 27;
        }  
        if (eregi("WEBEX", $do_import_expense->description)) {
            $category = 52;
            $suplier = 112;
        } 
        if (eregi("PAYCYCLE", $do_import_expense->description)) {
            $category = 52;
            $suplier = 111;
        }
        if (eregi("EXPERTPAY", $do_import_expense->description)) {
            $category = 44;
            //$suplier = 111;
        }  
        if (eregi("PAYROLL", $do_import_expense->description)) {
            $category = 44;
            //$suplier = 111;
        }  
        if (eregi("USATAXPYMT", $do_import_expense->description)) {
            $category = 35;
            //$suplier = 111;
        }  
        if (eregi("Tax", $do_import_expense->description)) {
            $category = 35;
            //$suplier = 111;
        }  
        if (eregi("BOBA LOCA", $do_import_expense->description)) {
            $category = 9;
            $suplier = 113;
        } 
         if (eregi("IN-N-OUT", $do_import_expense->description)) {
            $category = 9;
            $suplier = 14;
        }        
        
        if (eregi("SAXBYS", $do_import_expense->description)) {
            $category = 9;
            $suplier = 113;
        } 
        if (eregi("SAFARIBOOKSONL", $do_import_expense->description)) {
            $category = 19;
            $suplier = 114;
        }

        if (eregi("BEST BUY", $do_import_expense->description)) {
            $category = 13;
            $suplier = 65;
        }

        if (eregi("LAW OFFICES", $do_import_expense->description)) {
            $category = 15;
            //$suplier = 65;
        }
        if (eregi("CA BOE", $do_import_expense->description)) {        
            $category = 35;
            $suplier = 53;
		}
        if (eregi("TRAFFICSWARM", $do_import_expense->description)) {		
		    $category = 16;
		}
		
        if (eregi("DELL", $do_import_expense->description)) {		
		    $category = 13;
		    $suplier = 73;
		}
		if (eregi("T-MOBILE", $do_import_expense->description)) {		
		    $category = 12;
		    $category = 119;
		}               
            
        if ($category != 0) {
            $do_expense->newRecord();
            $do_expense->num = $do_import_expense->idexpense_import;
            $do_expense->description = $do_import_expense->description;
            $do_expense->datepayed = $do_import_expense->debit_date;
            $do_expense->daterecieve = $do_import_expense->debit_date;
            $do_expense->amount = $do_import_expense->amount;
            $do_expense->type = $category;
            if ($suplier !=0) {
                $do_expense->suplier = $suplier;
            }
            $do_expense->add();
            $do_import_expense->delete();
        }

    }

    $disp = new Display($goto);
    $disp->addParam("message", "Import completed");
    $this->setDisplayNext($disp);

?>
