<?php
    /**
     * Accounting import expenses from wellsfargo
     *  
     */

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


class ExpenseImport extends DataObject {
    
    public $table = "expense_import";
    protected $primary_key = "idexpense_import";
    var $import_file = "importcsv/expenses_2010.csv";

    function __construct(sqlConnect $conx = null) {
        if (!empty($conx)) { $conx = $GLOBALS['conx']; }
        if (defined("OFUZ_LOG_PLUGIN_EXPENSE")) {
            $this->setLogRun(OFUZ_LOG_PLUGIN_EXPENSE);
        }
        parent::__construct($conx);

    }
    private function format_sql_date($sheet_date) {
        list($month, $day, $year) = explode("/",$sheet_date);
        return "20".$year."-".$month."-".$day;
    }
    private function money2float($money_string) {
        $money_float = str_replace("$", "", $money_string);
        $money_float = str_replace(",", "", $money_float);
        return $money_float;
    }

    public function getUserAll($iduser=0) {
		if ($iduser==0) { $iduser = $_SESSION['do_User']->iduser ; }
        $this->query("SELECT * FROM ".$this->getTable()." WHERE iduser=".$iduser);   
	}

    public function eventImportCSV(EventControler $evctl) {

		$this->setLog(true);
		$this->setLog("\n Importing: expenses 2010");
		$goto = $evctl->getParam("goto");
		$import_file = $evctl->getParam("import_file");
		if (empty($import_file)) {
			$import_file = $this->import_file ;
		}
        $import_file = $GLOBALS['cfg_plugin_expense_path'].$import_file;
        $this->setLog("\n With File: ".$import_file);
	//   amount  Float Number (Float)   
	//   category  One Line text   
	//   debit_date  Date (SQL)   
	//   description  One Line text   
	//   idexpense_import  Simple Number (Integer)   
	//   payment_method  One Line text

		if (file_exists($import_file)) {
			$fp = fopen($import_file, "r");
			$i=0;
			$total = 0;
			while (($fields = fgetcsv($fp))) {
				$i++; 
				$this->newRecord();
				$this->category = $fields[0];
				$this->debit_date = $this->format_sql_date($fields[1]);
				$this->description = $fields[3];
				$this->payment_method = $fields[4];
				$this->amount = $this->money2float($fields[5]);
				$this->iduser = $_SESSION['do_User']->iduser;
				$this->add();
				$total += $this->money2float($fields[5]);
				$this->setLog("\n insert with: ".$this->getSqlQuery());
			}
		}
		$this->setLog("\n imported a total of: ".$total);

		$disp = new Display($goto);
		$disp->addParam("message", "Import completed");
		$evctl->setDisplayNext($disp);

    }

   /** 
    * eventApplyFilter
    * This event check process all the imported expenses and based on their description
    * categorize them and set the proper suplier.
    * Then it insert it into Expense table and delete it from the ExpenseImport.
    */

 	public function eventApplyFilters(EventControler $evctl) {

		$this->getUserAll(); 
        $do_expense = new Expense();

		while($this->next()) {
		    $account = 0;
		    $suplier = 0;
		    if ($this->category == "Airlines / Transportation") {       $account = 16;  }
		    if ($this->category == "ATM Withdrawals") {                 $account = 44;  }
		    if ($this->category == "Auto / Gas") {                      $account = 5;   }
		    if ($this->category == "Building Supply / Wholesale") {     $account = 13;  }
		    if ($this->category == "Entertainment") {                   $account = 21;   }
		    if ($this->category == "Groceries") {                       $account = 47;  }
		    if ($this->category == "Insurance / Financial Services") {  $account = 23;  }
		    if ($this->category == "Office Supply / Stationery") {      $account = 15;  }
		    if ($this->category == "Postage / Delivery") {              $account = 33; }
		    if ($this->category == "Restaurants") {                     $account = 4; }
		    if ($this->category == "Utilities / Telecom") {             $account = 14; }

		    if (preg_match("/DIRECTNIC COM/", $this->description)) {    $account = 49;  $suplier = 32; }
		    if (preg_match("/GANDI/i", $this->description)) {           $account = 49;  $suplier = 48; }        
		    if (preg_match("/THAWTE INC/i", $this->description)) {      $account = 49;  $suplier = 47; }
		    if (preg_match("/GEOTRUST/i", $this->description)) {        $account = 49;  $suplier = 27; }
		    if (preg_match("/BANKCARD/i", $this->description)) {        $account = 17;  $suplier = 39; }
            if (preg_match("/FXOL FGN/i", $this->description)) {        $account = 17;  $suplier = 39; }
			if (preg_match("/MEMBERSHIP FEE/i", $this->description)) {  $account = 17;  $suplier = 39; }
		    if (preg_match("/ADP TX/i", $this->description)) {          $account = 26;  $suplier = 30; }
		    if (preg_match("/Arctern/i", $this->description)) {         $account = 42;  $suplier = 8;  }
            if (preg_match("/INR AT/i", $this->description)) {			$account = 42;  $suplier = 71; } 
			if (preg_match("/WORD LIONS/i", $this->description)) {		$account = 9;   $suplier = 73; }
		    if (preg_match("/NETFLIX/i", $this->description)) {         $account = 23;  $suplier = 18; }
		    if (preg_match("/BLUE SHIELD/i", $this->description)) {     $account = 43;  $suplier = 4;  }
		    if (preg_match("/KAIZER/i", $this->description)) {     		$account = 43;  $suplier = 51; }    
		    if (preg_match("/NET2EZ/i", $this->description)) {          $account = 3;   $suplier = 19; }       
		    if (preg_match("/FRY\'S/i", $this->description)) {          $account = 13;  $suplier = 28; }
		    if (preg_match("/YAHOO SEARCH/i", $this->description)) {    $account = 10;  $suplier = 37; }
		    if (preg_match("/CISLO/i", $this->description)) {           $account = 11;  $suplier = 2;  }
		    if (preg_match("/VERIZON/i", $this->description)) {         $account = 14;  $suplier = 38; }                
		    if (preg_match("/CINGULAR/i", $this->description) 
			 || preg_match("/at\&t/i", $this->description)
             || preg_match("/IPHONE/i", $this->description)) {          $account = 14;  $suplier = 7;  } 
		    if (preg_match("/CALLCENTRIC/i", $this->description)) {     $account = 3;   $suplier = 107;}  
		    if (preg_match("/SIPPHONE/i", $this->description) 
             || preg_match("/GIZMO/i", $this->description)) {        	$account = 14;  $suplier = 26; } 
			if (preg_match("/ADWS/i", $this->description)) {    		$account = 10; $suplier = 26;  } 
		    if (preg_match("/AMZN PMTS/i", $this->description)
			 || preg_match("/Amazon Payments/i", $this->description)) { $account = 3;  $suplier = 11;  }      
		    if (preg_match("/OFFICE DEPOT/i", $this->description)) {    $account = 15; $suplier = 52;  } 
 		    if (preg_match("/OFFICE MAX/i", $this->description)) {    	$account = 15; $suplier = 72;  } 
		    if (preg_match("/STAPLES/i", $this->description)) {         $account = 15; $suplier = 13;  }  
		    if (preg_match("/WEBEX/i", $this->description)) {           $account = 50; $suplier = 40;  } 
		    if (preg_match("/PAYCYCLE/i", $this->description)) {        $account = 11; $suplier = 16;  }
            if (preg_match("/INTUIT/i", $this->description)) {        	$account = 50; $suplier = 16;  }
		    if (preg_match("/EXPERTPAY/i", $this->description)) {       $account = 46; }  
		    if (preg_match("/PAYROLL/i", $this->description)) {         $account = 46; }  
		    if (preg_match("/USATAXPYMT/i", $this->description)) {      $account = 26; $suplier = 30;  }  
		    if (preg_match("/Tax/i", $this->description)) {             $account = 8;  $suplier = 30;  }  
		    if (preg_match("/GRAND CASINO/i", $this->description)) {    $account = 4;  $suplier = 113; } 
		    if (preg_match("/IN-N-OUT/i", $this->description)) {        $account = 4;  $suplier = 14;  }        		   
            if (preg_match("/STARBUCKS/i", $this->description)) {       $account = 4;  $suplier = 14;  }
		    if (preg_match("/SAFARIBOOKSONL/i", $this->description)) {  $account = 21; $suplier = 67;  }
		    if (preg_match("/BEST BUY/i", $this->description)) {        $account = 13; $suplier = 5;   }
		    if (preg_match("/LAW OFFICES/i", $this->description)) {     $account = 11; $suplier = 68;  }
		    if (preg_match("/CA BOE/i", $this->description)) {          $account = 8; $suplier = 53;   }
		    if (preg_match("/TRAFFICSWARM/i", $this->description)) {    $account = 50; $suplier = 69;  }	
		    if (preg_match("/DELL/i", $this->description)) {		    $account = 13; $suplier = 34;  }
			if (preg_match("/T\-MOBILE/i", $this->description)) {		$account = 14; $suplier = 45;  }   
			if (preg_match("/Amazon Prime/i", $this->description)) {	$account = 23; $suplier = 10;  } 
			if (preg_match("/FRONTIER/i", $this->description)) {		$account = 16; $suplier = 49;  } 
			if (preg_match("/YOGAGLO/i", $this->description)) {			$account = 50; $suplier = 50;  } 
            if (preg_match("/SNCF/i", $this->description)) {			$account = 47; $suplier = 70;  } 
            if (preg_match("/ITUNES/i", $this->description)) {			$account = 12; $suplier = 9;   } 

		        
		    if ($account != 0) {
		        $do_expense->newRecord();
		        $do_expense->num = $this->idexpense_import;
		        $do_expense->description = $this->description;
		        $do_expense->date_paid = $this->debit_date;
		        $do_expense->date_receive = $this->debit_date;
		        $do_expense->amount = $this->amount;
                $do_expense->iduser = $_SESSION['do_User']->iduser;
		        $do_expense->idledger_account = $account;
		        if ($suplier !=0) {
		            $do_expense->idsuplier = $suplier;
		        }
		        $do_expense->add();
		        $this->delete();
		    }

		}

		$disp = new Display($evctl->goto);
		$disp->addParam("message", "Import completed");
		$evctl->setDisplayNext($disp);
	}
}
?>
