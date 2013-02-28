<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

	/**
	 * Process the CC on Authnet for the recurrent invoices
	 * on due date
  * Recurrent Invoices which are paid monthly online are porcessed via this script
  * Will retrieve the encrypted CC info and will process the invoice payment if its the due date.
  * @see class/RecurrentInvoice.class.php
  * @see class/RecurrentInvoiceCC.class.php
	 */
	 
	include_once('config.php');
	include_once('class/Authnet.class.php');
	set_time_limit(3600); 	 
	
	$do_recurrent = new RecurrentInvoice();
        $do_invoice = new Invoice();
        $do_inv_line = new InvoiceLine();
        $do_contact = new Contact();
        $do_recurrent_cc = new RecurrentInvoiceCC();
        $do_recurrent->getRecInvoiceForCCProcess();
       
        if($do_recurrent->getNumRows()){
            while($do_recurrent->next()){
                $do_user_detail = new User();
                $do_invoice = new Invoice();
                $do_invoice->getId($do_recurrent->idinvoice);
                $do_invoice->sessionPersistent("do_invoice", "index.php", OFUZ_TTL);
                $do_user_detail->getId($_SESSION['do_invoice']->iduser);
                $user_settings = $do_user_detail->getChildUserSettings();    
                if($user_settings->getNumRows()){// Get the setting data for the user who has created the invoice
                    while($user_settings->next()){
						$payment_mode = false;
                        if($user_settings->setting_name == 'invoice_logo' &&  $user_settings->setting_value != ''){
                            $_SESSION['do_invoice']->inv_logo =  $user_settings->setting_value ;
                        }
                        if($user_settings->setting_name == 'authnet_login' &&  $user_settings->setting_value != ''){
                            $_SESSION['do_invoice']->authnet_login =  $user_settings->setting_value ;
                        }
                        if($user_settings->setting_name == 'authnet_merchant_id' &&  $user_settings->setting_value != ''){
                            $_SESSION['do_invoice']->authnet_merchant_id =  $user_settings->setting_value ;
                        }
                        if($user_settings->setting_name == 'paypal_business_email' &&  $user_settings->setting_value != ''){
                            $_SESSION['do_invoice']->paypal_business_email =  $user_settings->setting_value ;
                        } 
                        if($user_settings->setting_name == 'payment_selection' &&  $user_settings->setting_value != ''){
							if($user_settings->setting_value == 'authorized.net'){
								$payment_mode =  true;
							}
                        }
                        
                        if(empty($payment_mode)){
							if((!empty($_SESSION['do_invoice']->authnet_login)) && (!empty($_SESSION['do_invoice']->authnet_merchant_id))){
								$payment_mode =  true;
							}	
						}
                        if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                            $currency =  explode("-",$user_settings->setting_value) ;
                            $_SESSION['do_invoice']->currency_iso_code = $currency[0];
                            $_SESSION['do_invoice']->currency_sign = $currency[1];
                            $_SESSION['do_invoice']->setCurrencyDisplay() ;
                            $_SESSION['do_invoice']->getCurrencyPostion() ;
                        }
                    }
                }// User setting data ends here
                
                if($payment_mode == true){
                $do_user_detail->free();
                $arr_user_info = $do_contact->getContactInfo_For_Invoice($do_recurrent->idcontact);
                $inv_info_arr = array();
                $inv_info_arr['description'] = $_SESSION['do_invoice']->description;
                $inv_info_arr['inv_num'] = $_SESSION['do_invoice']->num;
                $cc_number = $do_recurrent_cc->CCDecrypt($do_recurrent->cc_num);
                $payment_type = $do_recurrent->cc_type;
                $expire_year =  $do_recurrent->cc_exp_year;
                $expire_month = $do_recurrent->cc_exp_mon;
                /* @param true = test mode
                  @param false = non test mode i.e live  
                */
                $payment = new Authnet(false, $arr_user_info,$_SESSION['do_invoice']->authnet_login,$_SESSION['do_invoice']->authnet_merchant_id,$inv_info_arr);
                $cc_msg = $payment->validateCreditCard($cc_number, $payment_type,"",$expire_year, $expire_month,false);
               // echo '<br />'.$cc_msg;
                if($cc_msg == ""){
                      $invoice = uniqid('ofuz_', true);
                      $expiration = $expire_month.$expire_year;
                      $payment->transaction($cc_number, $expiration, $do_recurrent->net_total, "", $do_recurrent->idinvoice);
                      $payment->process();  
                      if ($payment -> isApproved()){
                            $transactionID = $payment->getTransactionID();
                            echo '<br />Payment Processed for invoice ID::: '.$do_recurrent->idinvoice.' Transaction ID ::: '.$transactionID.'<br />';
                            $do_pay_log = new PaymentLog();
                            $do_pay_log->addPaymentLog($transactionID,"AuthNet",$do_recurrent->idinvoice,$do_recurrent->net_total);
                            $idpayment_log = $do_pay_log->getPrimaryKeyValue();
                            $do_payment_inv = new PaymentInvoice();
                            $do_payment_inv->addPaymentInvoice($idpayment_log,$_SESSION['do_invoice']->idinvoice,$do_recurrent->net_total);
                            $_SESSION['do_invoice']->updatePayment($do_recurrent->net_total);
                            $_SESSION['do_invoice']->sendPaymentApprovedEmail($do_recurrent->net_total,"Authorized.net",$transactionID);
                            
                            $do_inv_callback = new InvoiceCallback();
                            $do_inv_callback->processCallBack($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->num,$do_recurrent->net_total,$_SESSION['do_invoice']->iduser,"AuthNet",$transactionID);
                            $do_inv_callback->free();
                      }elseif($payment -> isDeclined()){
                          $reason = $payment -> getResponseText();
                          $do_inv_callback = new InvoiceCallback();
                          $do_inv_callback->processCallBack($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->num,$do_recurrent->net_total,$_SESSION['do_invoice']->iduser,"fail","AuthNet","",$reason);
                      }else{
                          $reason = $payment -> getResponseText();
                          $do_inv_callback = new InvoiceCallback();
                          $do_inv_callback->processCallBack($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->num,$do_recurrent->net_total,$_SESSION['do_invoice']->iduser,"fail","AuthNet","",$reason);
                      }
                }
			  }
            }
            $do_invoice->free();
        }
        
	 
?>
