<?php 
    /**COPYRIGHTS**/ 
    // Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
    /**COPYRIGHTS**/

    /**
     * RegistrationInvoiceLog class
     * Using the DataObject
     */

class RegistrationInvoiceLog extends DataObject {
    
    public $table = "reg_invoice_log";
    protected $primary_key = "idreg_invoice_log";
    

    /*
        Method to get the id of the user registered.
        @param 
        iduser : invocie owner
        idinvoice : Invoice ID
    */
    public function getUserIdRegistered($idinvoice,$iduser){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table." Where idinvoice = ".$idinvoice. " AND iduser = ".$iduser);
        if($q->getNumRows()){
            $q->fetch();
            return $q->getData("reg_iduser");
        }else{
            return false ;
        }
    }


    /*
      Add the log 
      @param
      idinvoice : invoice id
      iduser : invoice owner
      reg_iduser : id of the user registered
    */
    public function add_reg_invoice_log($idinvoice,$iduser,$reg_iduser){
          $this->addNew();
          $this->idinvoice = $idinvoice;
          $this->iduser = $iduser;
          $this->reg_iduser = $reg_iduser;
          $this->add();
    }


    /*
       Update the Log
       @param
       idinvoice_new : id of the newly updated invoice. Usually in case when the invoice is a recurrent
       idinvoice_old : id of the old invoice
       iduser : invoice owner 
    */
    function update_reg_invoice_log($idinvoice_new,$idinvoice_old,$iduser){
      $q = new sqlQuery($this->getDbCon());
      $q->query("update idinvoice set idinvoice =".$idinvoice_new." Where idinvoice = ".$idinvoice_old." AND iduser = ".$iduser);
    }


    /*
        Process the Log only if the invoice belongs to a newly registered user
        @param
        idinvoice_new : id of the newly updated invoice. Usually in case when the invoice is a recurrent
        idinvoice_old : id of the old invoice
        iduser : invoice owner
    */
    function process_reg_invoice_log($idinvoice_new,$idinvoice_old,$iduser){
        if($this->getUserIdRegistered($idinvoice_old,$iduser))
              $this->update_reg_invoice_log($idinvoice_new,$idinvoice_old,$iduser);
    }
}
?>
