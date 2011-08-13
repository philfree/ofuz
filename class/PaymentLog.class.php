<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

class PaymentLog extends DataObject {

    public $table = 'paymentlog';
    protected $primary_key = 'idpaymentlog';


    /**
      * Add Payment Log method
      * @param string $ref_num
      * @param string $pay_type
      * @param integer $idinvoice
      * @param double $amt
    */
    function addPaymentLog($ref_num,$pay_type,$idinvoice,$amt){
        $this->amount = $amt;
        $this->idinvoice = $idinvoice;
        $this->payment_type = $pay_type;
        $this->ref_num = $ref_num;
        $this->timestamp = time();
        $this->date_added = date("Y-m-d H:i:s");
        $this->add();
    }

    /**
      *  Check if its a duplicate entry for a ref num. 
      *  Needed for the Paypal as the Returned data is 
      *  a post value from Paypal and using f5 may create 
      *  a duplicate entry.
      *  @param string $ref_num
      *  @param integer $idinvoice
      *  @param string $pay_type
    */
    function isTransRefExists($ref_num,$idinvoice,$pay_type){
        $q = new sqlQuery($this->getDbCon());
        $q->query("Select * from ".$this->table. " Where ref_num = '".$ref_num."' AND
                   idinvoice = ".$idinvoice." AND payment_type = '".$pay_type."'
                  ");
        if($q->getNumRows()){
            return true;
        }else{
            return false;
        }
    }

    /**
      * Function to get the payment Log for an invoice
      * @param integer $idinvoice
    */
    function getPaymentLog($idinvoice){
      //$this->query("select * from ".$this->table. " where idinvoice = ".$idinvoice." order by ".$this->primary_key. " desc");
      $qry = "Select sum(payment_invoice.amount ) as amount,payment_invoice.idpayment_invoice,
              paymentlog.timestamp,paymentlog.ref_num,paymentlog.idpaymentlog from paymentlog 
              Inner Join payment_invoice on payment_invoice.idpayment = paymentlog.idpaymentlog
              Where payment_invoice.idinvoice = ".$idinvoice." GROUP BY payment_invoice.idpayment_invoice "; 
       $this->query($qry);       
    }


    

    /**
      * Function to delete the invoice payment
      * For single payment and single invoice it will delete the data straight away
      * If the payment is attached to multiple invoices then it will first show the alert to the user and 
      * if user wants to delete the payment then it will delete the payment from the attached invoices
      * @param object $evtcl
    */
    function eventDeletePaymentLog(EventControler $evtcl){
      if($evtcl->id){
            $q = new sqlQuery($this->getDbCon());
            $q1 = new sqlQuery($this->getDbCon());
            $sql = "SELECT COUNT(payment_invoice.idpayment) AS num_inv 
              FROM payment_invoice INNER JOIN paymentlog 
              ON paymentlog.idpaymentlog = payment_invoice.idpayment
              WHERE paymentlog.idpaymentlog = {$evtcl->id}
                    ";
            $q->query($sql);
            if($q->getNumRows()) {
                $q->fetch();
                $num_inv = $q->getData("num_inv");

                if($num_inv == 1) {

                  $do_inv = new Invoice();
                  $this->getId($evtcl->id);
                  $idinvoice = $this->idinvoice;

                  $sql_del_paymentlog = "DELETE FROM {$this->table} WHERE idpaymentlog = '{$evtcl->id}'";
                  $sql_del_paymentinv = "DELETE FROM payment_invoice WHERE idpayment = '{$evtcl->id}'";
                  $sql_del_ext_amt = "DELETE FROM paymentlog_extra_amount WHERE idpaymentlog = '{$evtcl->id}'";

                  $q->query($sql_del_paymentlog);
                  $q->query($sql_del_paymentinv);
                  $q->query($sql_del_ext_amt);

                  $do_inv->deletePaymentFromInvoice($idinvoice,$evtcl->amt);

                  $_SESSION['in_page_message'] = _("Payment has been deducted from the invoice.");

                } 
                if($num_inv > 1) { 
                    $do_inv = new Invoice();
                    if($evtcl->del_mul_confirm != 'Yes'){
                        $_SESSION['in_page_message'] = _("This Payment is shared with multiple invoices.");
                        $_SESSION['in_page_message_inv_mul_pay_del'] = 'Yes';
                        $_SESSION['in_page_message_inv_idpaymentlog'] = (int)$evtcl->id ;
                        $evtcl->setDisplayNext(new Display("invoice_alert.php"));
                    }else{
                          $sql = "SELECT payment_invoice.* 
                                FROM payment_invoice 
                                INNER JOIN paymentlog 
                                ON paymentlog.idpaymentlog = payment_invoice.idpayment
                                WHERE paymentlog.idpaymentlog = {$evtcl->id}
                          ";
                          $q1->query($sql);
                          if($q1->getNumRows() > 0 ){
                              while($q1->fetch()){
                                      $idinvoice = $q1->getData("idinvoice");
                                      $idpayment = $q1->getData("idpayment");
                                      $amt = $q1->getData("amount");
                                      $do_inv->deletePaymentFromInvoice($idinvoice,$amt);   
                              }
                              
                              $sql_del_paymentlog = "DELETE FROM {$this->table} WHERE idpaymentlog = '{$idpayment}'";
                              $sql_del_paymentinv = "DELETE FROM payment_invoice WHERE idpayment = '{$idpayment}'";
                              $sql_del_ext_amt = "DELETE FROM paymentlog_extra_amount WHERE idpaymentlog = '{$idpayment}'";

                              $q1->query($sql_del_paymentlog);
                              $q1->query($sql_del_paymentinv);
                              $q1->query($sql_del_ext_amt);
                              $evtcl->setDisplayNext(new Display("/Invoice/".$_SESSION['do_invoice']->idinvoice));
                          }
                    }
                }
            }
        }
      
    }


   /**
     * Function to get the multiple invoices from the idpaymentlog
     * @param integer idpaymentlog
     * scope when we try to delete the payment for an invoice where the payment is attached with 
     * multiple invoices then it will list the invoices for that payment for user confirmation to 
     * delete
     * @see PaymentLog :: eventDeletePaymentLog()
   */ 

    function getMulInvoicesForPayment($idpaymentlog){
        $qry = "SELECT payment_invoice.*
                          FROM payment_invoice 
                          INNER JOIN paymentlog ON paymentlog.idpaymentlog = payment_invoice.idpayment
                          WHERE paymentlog.idpaymentlog = ".(int)$idpaymentlog;
        $this->query($qry);
    }

    
    /**
      * Function to get the paymentlog details
      * @param integer $idinvoice
    */
    function getPaymentLogDetails($idinvoice) {
      $sql = "SELECT *
              FROM {$this->table}
              WHERE idinvoice = {$idinvoice}
            ";
      $this->query($sql);
    }

    /**
      * Function to get the paymentlog extra amount details
      * @param integer $idpaymentlog
    */
    function getPaymentLogExtraAmountDetails($idpaymentlog) {
      $sql = "SELECT *
              FROM `paymentlog_extra_amount`
              WHERE idpaymentlog = {$idpaymentlog}
            ";
      $this->query($sql);
    }
}
?>