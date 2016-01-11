<?php 
    /**
     * Project class
     * Using the DataObject
     */


class Expense extends DataObject {
    
    public $table = "expense";
    protected $primary_key = "idexpense";

    public $currency_sign = '$'; 
    public $currency_iso_code = ''; // Can be USD,CAD,GBP not used currently   
    public $currency  = '$'; 
    public $currency_position  = 'l'; // default currency potion is left for euro its right
    public $inv_dd_format = "Y-m-d";
    public $filter_set = false;
    public $filter_month = '';
    public $filter_year = '';



    function __construct(sqlConnect $conx = null) {
        if (!empty($conx)) { $conx = $GLOBALS['conx']; }
        if (defined("OFUZ_LOG_PLUGIN_EXPENSE")) {
            $this->setLogRun(OFUZ_LOG_PLUGIN_EXPENSE);
        }
        parent::__construct($conx);

    }


    /**
      * Get the posion of the currency right for euro left others
      * It will set the member variable currency_position with the value
    */
    function getCurrencyPostion(){
        if($this->currency_iso_code == 'Euro'){
              $this->currency_position = 'r';
        }else{
             $this->currency_position = 'l';
        }
    }

    /**
      * Set the currency signto the public var currency
      * This version has just the currecny symbol not the ISO code
    */
    function setCurrencyDisplay(){
        $this->currency = $this->currency_sign;// For now only symbol no ISO code
    }


    /**
      * Method to view an amount after determining the posion and then 
      * setting the number format
      * @param $amt -- FLOAT
    */
    function viewAmount($amt){
        if($this->currency_position == 'r'){
              return $this->setNumberFormat($amt).' '.$this->currency;
        }else{
              return $this->currency.' '.$this->setNumberFormat($amt);
        }
    }

    /**
      * Automatic conversion of the numbers depending on the currency selected
      * @param $amt -- FLOAT
      * @return $amt with formated 
    */
    function setNumberFormat($amt){ 
        switch($this->currency_iso_code){
              case "Euro" : 
                          return number_format($amt,2, ', ', ' ' );
                          break;
              case "BRL" :
                         return number_format($amt,2, ', ', '. ' );
                          break;
              case "ZAR" :
                         return number_format($amt,2, '. ', ' ' );
                          break;
              default : 
                          return number_format($amt,2, '. ', ',' );
                          break;
        }
    }

    /**
      * Formating 0's
    */
    function setNumberFormatZero(){ 
        switch($this->currency_iso_code){
              case "Euro" : 
                          return '0,00 '.$this->currency;
                          break;
              case "BRL" :
                          return $this->currency.' 0,00';
                          break;
              default : 
                          return $this->currency.' 0.00';
                          break;
        }
    }


  /**
   * Formatting the date as per the user setting
   * @param $date -- String
   * @return $formatted_date -- String
   */
  function getFormattedDate($date) {
    $formatted_date = date($this->inv_dd_format,strtotime($date));
    return $formatted_date;
  }

    /**
      * Format the filtered month for the query
      * @return "formated month" -- STRING
    */
    function formatSearchMonth($month){
        if($this->filter_year != '')
          return $this->filter_year.'-'.$month; 
        else
          return date("Y").'-'.$month; 
    }

   /**
      * Event method to get the invoice based on invoice filters
      * if filter_set = true then the invoices.php will get the query set in the method
      * else will call getAllExpense()
      * If the Month dropdown is seleted for filter then check if already one status is set before in the 
      * var filter_inv_status_val
      * @param $evtcl -- Object  
    */

    function eventFilterExpense(EventControler $evtcl){
        $this->setSqlQuery("");
        $select = "select * from expense";
        $where = " where iduser = ".$_SESSION['do_User']->iduser." ";
        $order_by = " order by ".$this->primary_key." desc";

        if($evtcl->filter_for_year != ''){ $this->filter_year = $evtcl->filter_for_year;}
        
        if($evtcl->type == "date"){ 
            $this->filter_set = true;
            $where.= " AND datecreated like '%".$this->formatSearchMonth($evtcl->filter_for_mon)."%'";
            $qry = $select.$where.$order_by;
            $this->setSqlQuery($qry);
            $this->filter_month = $evtcl->filter_for_mon;
        }else{
              //if($this->filter_month == ''){$this->filter_month = date("m");}
              $where.= " AND datecreated like '%".$this->formatSearchMonth($this->filter_month)."%'";
              if($evtcl->status != 'None'){
                $where.=" AND status = '".$evtcl->status."'";
                $this->filter_set = true;
                $this->filter_inv_status_val = $evtcl->status;
              }else{
                $this->filter_inv_status_val = "";
              }
              $qry = $select.$where.$order_by;
              
              $this->setSqlQuery($qry);
        }

       $evtcl->setDisplayNext(new Display($evtcl->goto));
    }
    

    /**
      * Unset the filter and set to default
      * @param $evtcl -- Object  
    */
    function eventUnsetFilterInvoice(EventControler $evtcl){
        $this->setSqlQuery("");
        $this->filter_set = false;
        $this->from_invoice_page = true;
        $evtcl->setDisplayNext(new Display($evtcl->goto));
    }


    /**
      * Function getting all the Expenses for the user
      * sets the query object
    */
    function getAllExpense(){
         if($this->filter_month != ''){
              $date = $this->formatSearchMonth($this->filter_month);
         }else{
              $date = date("Y")-1;
         }        
         $this->query("select * from expense where iduser = ".$_SESSION['do_User']->iduser.
                      " AND date_paid like '%".$date."%' order by ".$this->primary_key." desc"
                      );
    }
 

  
}
?>
