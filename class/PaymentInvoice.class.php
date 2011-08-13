<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * PaymentInvoice class
     * Using the DataObject
     * This is to maintain the relation between the payment and the idinvoice
     */

class PaymentInvoice extends DataObject {
    
    public $table = "payment_invoice";
    protected $primary_key = "idpayment_invoice";
    
    /*
        Adding the Payment Invoice
    */
    function addPaymentInvoice($idpaylog,$idinvoice,$amt){  
      $this->addNew();
      $this->amount = $amt;
      $this->idpayment = $idpaylog;
      $this->idinvoice = $idinvoice;
      $this->add();
      $this->processPaymentExtraAmt($idpaylog);
    }


    /*
        Method processing the extra amount for an invoice.
    */
    function processPaymentExtraAmt($idpaylog){
        $q = new sqlQuery($this->getDbCon());
        $do_paymentlog = new PaymentLog();
        $do_paymentlog->getId($idpaylog);
        $inv_log_amt = $do_paymentlog->amount;
        $qry = "Select sum(payment_invoice.amount ) as amount,payment_invoice.idpayment
                from payment_invoice 
                Where payment_invoice.idpayment = ".$idpaylog." GROUP BY payment_invoice.idpayment "; 
        //echo $qry.'<br />';
        $q->query($qry);
        if($q->getNumRows()){
            $q->fetch();
            $total_added_amt = $q->getData("amount");
            $diff = $inv_log_amt - $total_added_amt;
            if($diff == 0 || $diff == 0.00){
                $this->updatePaymentExtraAmount($idpaylog,$diff,"delete");
            }elseif( $diff > 0 ){
                $this->updatePaymentExtraAmount($idpaylog,$diff,"update");
            }
        }   
    }

    /*
        Add/edit/delete the extra amount for a payment
    */
    function updatePaymentExtraAmount($idpaylog,$extra_amt,$mode){
        $q = new sqlQuery($this->getDbCon());
        $q_update = new sqlQuery($this->getDbCon());
        $q->query("select * from paymentlog_extra_amount where idpaymentlog = ".$idpaylog);
        if($q->getNumRows()){
              $q->fetch();
              $idpaymentlog_extra_amount = $q->getData("idpaymentlog_extra_amount");
              switch($mode){
                  case 'update' :
                        $query = "update paymentlog_extra_amount set extra_amt = ".$extra_amt." 
                                  where idpaymentlog_extra_amount= ".$idpaymentlog_extra_amount;
                        break;
                  case 'delete' :
                        $query = "delete from paymentlog_extra_amount where 
                                  idpaymentlog_extra_amount= ".$idpaymentlog_extra_amount." Limit 1";
                        break;
              }
        }else{
            switch($mode){
                  case 'update' :
                        $query = "Insert Into paymentlog_extra_amount 
                                  (`idpaymentlog`,`extra_amt`,`iduser`)
                                  values (".$idpaylog.",".$extra_amt.",".$_SESSION['do_User']->iduser.")
                                  ";
                              break;
            }
        }
        $q_update->query($query);  
        //echo $query.'<br />';exit;
    }
  
    /*
        Method to ge the Extra Amount for a payment 
    */
    function getExtraAmoutNotPaid(){
        $this->query("Select paymentlog.idpaymentlog,paymentlog.ref_num,paymentlog_extra_amount.extra_amt,
                      paymentlog_extra_amount.iduser
                      from paymentlog 
                      Inner join paymentlog_extra_amount on paymentlog_extra_amount.idpaymentlog = paymentlog.idpaymentlog
                      where paymentlog_extra_amount.iduser =".$_SESSION['do_User']->iduser );
         
        if($this->getNumRows()){
            $this->getValues();
            return true ;
        }else{
            return false ;
        }
    }

    function displayExtraAmtNotPaid(){
          $e_select_extraamt_not_paid = new Event($this->getObjectName()."->eventSelectExtraAmount");
          while($this->next()){
                $e_select_extraamt_not_paid->addParam("idpaymentlog",$this->idpaymentlog);
                $e_select_extraamt_not_paid->addParam("extra_amt",$this->extra_amt);
                $e_select_extraamt_not_paid->addParam("ref_num",$this->ref_num);
                $select_link = $e_select_extraamt_not_paid->getLink("Select");
                echo '<div style="font-size:14px;">';
                echo _('Note:').$this->ref_num.'&nbsp;&nbsp;&nbsp;'._('Amount Not Added : ').$this->extra_amt.'&nbsp;&nbsp;'.$select_link;
                echo '</div>';
          }
    }

    function eventSelectExtraAmount(EventControler $evtcl){ 
        $_SESSION['extra_amt'] = $evtcl->extra_amt;
        $_SESSION['ref_num'] = $evtcl->ref_num;
        $_SESSION['last_paylogid'] =$evtcl->idpaymentlog;
        //$evtcl->addParam("status","Overdue");
        //$evtcl->addParam("goto","invoices.php");
        //$evtcl->addParam("type","User");
        //$_SESSION['do_invoice_list']->eventFilterInvoice($evtcl);
        $evtcl->setDisplayNext(new Display("invoices_unpaid.php"));
    }

    function eventCancelExtraAmtPay(EventControler $evtcl){
        $_SESSION['extra_amt'] = '';
        $_SESSION['ref_num'] = '';
        $_SESSION['last_paylogid'] ='';
        $evtcl->addParam("goto","invoices.php");
        $_SESSION['do_invoice_list']->eventUnsetFilterInvoice($evtcl);
    }

    function getInvDetails($idpayment) {
      $sql = "SELECT * FROM {$this->table} WHERE idpayment = {$idpayment}";
      $this->query($sql);
    }
    
}
?>
