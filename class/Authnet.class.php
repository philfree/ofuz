<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

/**
  * @author SQLFusion's Dream Team <info@sqlfusion.com>
  * @package OfuzCore
  * @license GNU Affero General Public License
  * @version 0.6
  * @date 2010-09-04
  * @since 0.6
  */

class Authnet extends BaseObject {

  //private $login = "3f7Mk5TVbm";
  //private $transkey = "7hT7aK85766rD9ep";
  private $login ;
  private $transkey;
  private $approved = false;
  private $declined = false;
  private $error = true;

  private $params = array();
  private $results = array();

  private $fields;
  private $response;

  private $test;

  public function __construct($test = false, $arr_user_info,$login,$transkey,$inv_info_arr,$curr_code="") {
      $this->login = $login;
      $this->transkey = $transkey;
      $this->test = trim($test);
      if ($this->test) {		  
	  $this->params['x_test_request'] =  "TRUE";
          $this->url = "https://test.authorize.net/gateway/transact.dll";
      } else {
          $this->url = "https://secure.authorize.net/gateway/transact.dll";
	  $this->params['x_test_request'] =  "FALSE";
      }

	  //$this->url = "https://secure.authorize.net/gateway/transact.dll";
      //if($curr_code == ""){$curr_code = 'USD';}
      $this->params['x_delim_data'] = "TRUE";
      $this->params['x_delim_char'] = "|";
      $this->params['x_relay_response'] = "FALSE";
      $this->params['x_url'] = "FALSE";
      $this->params['x_version'] = "3.1";
      $this->params['x_method'] = "CC";
      $this->params['x_type'] = "AUTH_CAPTURE";
      $this->params['x_login'] = $this->login;
      $this->params['x_tran_key'] = $this->transkey;

      $this->params['x_first_name'] = $arr_user_info['firstname'];
      $this->params['x_last_name'] = $arr_user_info['lastname'];
      $this->params['x_company'] = $arr_user_info['company'];
      $this->params['x_address'] = $arr_user_info['address'];
      $this->params['x_city'] = $arr_user_info['city'];
      $this->params['x_state'] = $arr_user_info['state'];
      $this->params['x_zip'] = $arr_user_info['zip'];
      $this->params['x_country'] = $arr_user_info['country'];
      $this->params['x_phone'] = $arr_user_info['phone'];
      // Invoice Num and description is sent 
      $this->params['x_description'] = $inv_info_arr['description'];
      $this->params['x_invoice_num'] = $inv_info_arr['inv_num'];

      // Don't let Authnet to send email to the customer Ofuz sends email just customize email template ofuz_inv_payment_confirmation
      //$this->params['x_email'] = $arr_user_info['email'];
     // $this->params['x_currency_code'] = $curr_code;
     // print_r($this->params);exit;
	 //$this->setLogRun(true);
	 //$this->setLogArray($this->params);
  }

  public function transaction($cardnum, $expiration, $amount, $cvv = "", $invoice = "", $tax = "") {
      $this->params['x_card_num'] = trim($cardnum);
      $this->params['x_exp_date'] = trim($expiration);
      $this->params['x_amount'] = trim($amount);
      $this->params['x_po_num'] = trim($invoice);
      $this->params['x_tax'] = trim($tax);
      $this->params['x_card_code'] = trim($cvv);
  }

  public function process($retries = 3) {
      $this->prepareParameters();
      $ch = curl_init($this->url);
      $count = 0;
      while ($count < $retries) {
          curl_setopt($ch, CURLOPT_HEADER, 0);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, rtrim($this->fields, "& "));
          $this->response = curl_exec($ch);
          $this->parseResults();
          if ($this->getResultResponseFull() == "Approved") {
              $this->approved = true;
              $this->declined = false;
              $this->error = false;
              break;
          } else if ($this->getResultResponseFull() == "Declined") {
              $this->approved = false;
              $this->declined = true;
              $this->error = false;
              break;
          }
          $count++;
      }
      curl_close($ch);
  }

  private function prepareParameters() {
      foreach($this->params as $key => $value) {
          $this->fields .= "$key=" . urlencode($value) . "&";
      }
  }

  private function parseResults() {
      $this->results = explode("|", $this->response);
  }

  public function setParameter($param, $value) {
      $param = trim($param);
      $value = trim($value);
      $this->params[$param] = $value;
  }

  public function getResultResponse() {
      return $this->results[0];
  }

  public function getResultResponseFull() {
      $response = array("", "Approved", "Declined", "Error");
      return $response[$this->results[0]];
  }

  public function isApproved() {
      return $this->approved;
  }

  public function isDeclined() {
      return $this->declined;
  }

  public function isError() {
      return $this->error;
  }

  public function getResponseText() {
      return $this->results[3];
  }

  public function getResponseCode(){
      return $this->results[2];
  }

  public function getAuthCode() {
      return $this->results[4];
  }

  public function getAVSResponse() {
      return $this->results[5];
  }

  public function getTransactionID() {
      return $this->results[6];
  }

  public function validateCreditCard($cc_number, $payment_type, $cvv, $expire_year, $expire_month,$check_cvv = true){
      $cc_number = trim($cc_number);
      $cc_length = strlen($cc_number);
      $cvv = trim($cvv);
      $cvv_length = strlen($cvv);
      $cc_msg = "";

      /**
       * Credit Card length Validation
       */
       //it will be on live since test credit card number does not work with this
      if($payment_type == 'AmericanExpress'){
         if($cc_length != 15){
            $cc_msg = "Invalid American Express Card Number.The Card Number should be of 15 digits.You have entered '{$cc_length}' digits.Please re-enter.";
         }
      }
      if($payment_type == 'Visa'){
         if($cc_length != 16){
            $cc_msg = "Invalid Visa Card Number.The Card Number should be of 16 digits.You have entered '{$cc_length}' digits.Please re-enter.";
         }
      }
      if($payment_type == 'MasterCard'){
         if($cc_length != 16){
            $cc_msg = "Invalid Master Card Number.The Card Number should be of 16 digits.You have entered '{$cc_length}' digits.Please re-enter.";
         }
      }

      /**
       * CVV code length Validation
       */
      if($cc_msg == ""){
          /* it will be on live since test credit card number does not work with this
          if($payment_type=='AmericanExpress'){
            if($cvv_length != 4){
                $cc_msg = "Invalid American Express Card CID code.The CID code should be of 4 digits.You have entered only '{$cvv_length}' digits.Please re-enter.";
            }
          }
          if($payment_type=='Visa' && $check_cvv){
            if($cvv_length != 3){
                $cc_msg = "Invalid Visa Card CVC2 code.The CVC2 code should be of 3 digits.You have entered only '{$cvv_length}' digits.Please re-enter.";
            }
          }
          if($payment_type=='MasterCard' && $check_cvv){
            if($cvv_length != 3){
                $cc_msg = "Invalid Master Card CVV2 code.The CVV2 code should be of 3 digits.You have entered only '{$cvv_length}' digits.Please re-enter.";
            }
          }*/
      }

      /**
       * Credit Card Expiration Date Validation
       */
      if($cc_msg == ""){
          $current_month = date("m");
          $current_year = date("y");
          if ($expire_year < $current_year){
              // Invalid date
              $cc_msg = _('You have selected Invalid date.');
          } else{
              // Check if the same year,
              // if so, make sure month is current or later
              if ($expire_year == $current_year){
                  if ($expire_month < $current_month){
                      // Invalid date
                      $cc_msg = _("You have selected Invalid date.");
                  } else{
                      // Valid date
                  }
              }
          }
      }

      return $cc_msg;
  }

}

