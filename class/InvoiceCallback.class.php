<?php 
    /** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
      // Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
    /** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * InvoiceCallback class
     * Using the DataObject
     */

class InvoiceCallback extends DataObject {
    
    public $table = "invoice_callback";
    protected $primary_key = "idinvoice_callback";

    private $report = Array (
    );

    private $savedquery = Array (
    );

    /*
      This method is called when an Invoice is added via API to add the Call Back URL
      if for an invoice id call back URL is found then it will return the callback_url
      else it will add the callback_url and returns true.
    */
    function addCallBackUrl($idinvoice,$url="",$next_url=""){
        $callback = $this->isCallBackUrlPresent($idinvoice);
        if(!$callback){
          $this->addNew();
          $this->idinvoice = $idinvoice;
          $this->callback_url = $url;
          $this->next_url = $next_url;
          $this->add();
          return true;
        }else{
          $this->getId($callback);
          return $this->callback_url;
        }
    }

    /*
      Method to check if the invoice  is having a call back url or not
      @param : idinvoice
    */    
    function isCallBackUrlPresent($idinvoice){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table." where idinvoice = ".$idinvoice);
        if($q->getNumRows()){
           $q->fetch();
           return $q->getData("idinvoice_callback");
        }else{
           return false;
        }
    }

    function isNextUrl($idinvoice){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table." where idinvoice = ".$idinvoice);
        if($q->getNumRows()){
           $q->fetch(); 
           $next_url = $q->getData("next_url");
           if($next_url !='' && !empty($next_url) ){
                return $next_url;
           }else{ return false ; }
        }else{
           return false;
        }
    }

    /*
        Method to update the callback Url details.  
        While a new invoice is created which is recurrent
        then the idinvoice is updated with the new one.
    */
    function updateCallBack($idinvoice_old,$idinvoice_new){
        $id = $this->isCallBackUrlPresent($idinvoice_old);
        if($id){
            $this->getId($id);
            $this->idinvoice = $idinvoice_new;
            $this->update();
        }
    }

    /*
        Method to process the call back URL. Post the data in the call back URl
        The Data Posted now as :
        id : idinvoice
        num : invoice number
        amt_paid : Total Amount paid
        payment_status : ok for success,fail for failure
        failure_reason : Reason for failure
        c : check sum md5(invoice number.amount paid.api key of the user)
        pay_type : Authnet,Paypal,Manual
        ref_num : the reference number/transaction id returned from payment gateway or some manual data while adding manual payment 
    */
    function processCallBack($idinvoice,$invoice_num,$amt_paid,$iduser,$payment_status,$pay_type="",$ref_num="",$failure_reason=""){
          $do_user_api = new User();
          $do_user_api->getId($iduser);
          $api_key = $do_user_api->api_key;
          $check_sum = md5($invoice_num.$amt_paid.$api_key);
          $ret_data = $this->isCallBackUrlPresent($idinvoice);
          if($ret_data){
                $this->getId($ret_data);
                $call_back_url = $this->callback_url;
                $Curl_Session = curl_init($call_back_url);
                curl_setopt ($Curl_Session, CURLOPT_POST, 1);
                curl_setopt ($Curl_Session, CURLOPT_POSTFIELDS, "id=$idinvoice&num=$invoice_num&amt_paid=$amt_paid&c=$check_sum&pay_type=$pay_type&pay_status=$payment_status&ref_num=$ref_num&reason=$failure_reason");
                curl_setopt ($Curl_Session, CURLOPT_FOLLOWLOCATION, 1);
                curl_exec ($Curl_Session);
                curl_close ($Curl_Session);
          }
        
    }
}
?>
