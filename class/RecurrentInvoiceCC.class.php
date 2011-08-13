<?php 
    /**COPYRIGHTS**/ 
      // Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
    /**COPYRIGHTS**/

    /**
     * RecurrentInvoice class
     * Using the DataObject
     */

class RecurrentInvoiceCC extends DataObject {
    
    public $table = "recurrent_invoice_cc";
    protected $primary_key = "idrecurrent_invoice_cc";
    
    protected $md5_str = 'E$le_ey#';

   function CCEncrypt($num) {
        $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
        $key = substr(md5($this->md5_str), 0, mcrypt_enc_get_key_size($td));
        mcrypt_generic_init($td, $key, $iv);
        $en_num = mcrypt_generic($td, $num);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $iv.$en_num;
    }
    function CCDecrypt($iv_num) {
        $td = mcrypt_module_open('rijndael-256', '', 'ofb', '');
        $key = substr(md5($this->md5_str), 0, mcrypt_enc_get_key_size($td));
        $iv_size = mcrypt_enc_get_iv_size($td);
        $iv = substr($iv_num, 0, $iv_size);
        $en_num = substr($iv_num, $iv_size);
        mcrypt_generic_init($td, $key, $iv);
        $num = mdecrypt_generic($td, rtrim($en_num, "\0"));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $num;
    }

    function add_cc_info($cc_number,$expire_year,$expire_month,$cc_type,$idrecurrentinvoice){
        
         $enc_cc_num = $this->CCEncrypt($cc_number);
         //$enc_cvv = $this->CCEncrypt($cvv);
         $this->idrecurrentinvoice = $idrecurrentinvoice;
         $this->cc_num = addslashes($enc_cc_num);
         $this->cc_exp_mon = $expire_month;
         $this->cc_exp_year = $expire_year;
         $this->cc_type  = $cc_type;
         
         $this->add();  
    }

    function delete_cc_info(){
    } 

    function has_cc_info($idrecurrent){
        $q = new sqlQuery($this->getDbCon());
        //echo "select * from ".$this->table." where idrecurrentinvoice = ".$idrecurrent;
        $q->query("select * from ".$this->table." where idrecurrentinvoice = ".$idrecurrent);
        if($q->getNumRows()){
            $q->fetch();
            return $q->getData("idrecurrent_invoice_cc");
        }else{
            return false;
        }
    }

}
?>
