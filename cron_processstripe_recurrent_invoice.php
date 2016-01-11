<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

	/**
	 * Process the Stripe Payment for the recurrent invoices
	 * on due date
  * Recurrent Invoices which are paid monthly online are porcessed via this script
  * Will check the user stripe details and will process the invoice payment if its the due date.
  * @see class/RecurrentInvoice.class.php
  * @see class/Stripe.class.php
	 */
	 
	include_once('config.php');
	include_once('class/Stripe.class.php');
	include_once('class/stripe-lib/Stripe.php');
	set_time_limit(3600); 	 
		//echo '<pre>';print_r($_SESSION);echo '</pre>';
		$do_recurrent = new RecurrentInvoice();
        $do_invoice = new Invoice();
        $do_inv_line = new InvoiceLine();
        $do_contact = new Contact();
        $do_recurrent->getRecInvoiceForStripeProcess();
       
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
						$payment_mode = false;$payment_selection='';
                        if($user_settings->setting_name == 'invoice_logo' &&  $user_settings->setting_value != ''){
                            $_SESSION['do_invoice']->inv_logo =  $user_settings->setting_value ;
                        }
                        if($user_settings->setting_name == 'stripe_api_key' &&  $user_settings->setting_value != ''){
                            $_SESSION['do_invoice']->stripe_api_key =  $user_settings->setting_value ;
                        }
                        if($user_settings->setting_name == 'stripe_publish_key' &&  $user_settings->setting_value != ''){
                            $_SESSION['do_invoice']->stripe_publish_key =  $user_settings->setting_value ;
                        }
                        
                        if($user_settings->setting_name == 'payment_selection' &&  $user_settings->setting_value != ''){
							$_SESSION['do_invoice']->payment_selection = $user_settings->setting_value;
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
                
                if(isset($_SESSION['do_invoice']->payment_selection)){							
					if($_SESSION['do_invoice']->payment_selection == 'stripe.com'){
						if((!empty($_SESSION['do_invoice']->stripe_api_key)) && (!empty($_SESSION['do_invoice']->stripe_publish_key))){
								$payment_mode =  true;
						}	
					}
                } else {
					if((!empty($_SESSION['do_invoice']->stripe_api_key)) && (!empty($_SESSION['do_invoice']->stripe_publish_key))){
							$payment_mode =  true;
					}
				}
                
                
                if($payment_mode == true){echo $_SESSION['do_invoice']->idcontact;
					$stripe_customer_id = $_SESSION['do_invoice']->getStripeCustomerId($_SESSION['do_invoice']->iduser,$_SESSION['do_invoice']->idcontact);
					
					if(!empty($stripe_customer_id)){
						
						$total = $do_recurrent->net_total*100;
						
						$payment = new StripeGateWay(false, $_SESSION['do_invoice']->stripe_api_key);
						$result = $payment->ChargeExsistingCustomer($stripe_customer_id,$total);
						
						if($result['success'] == '1'){          
						  
						  $total = $total/100;          
						  
						  $do_pay_log = new PaymentLog();
						  $do_pay_log->addPaymentLog($result['response']['id'],"Stripe",$_SESSION['do_invoice']->idinvoice,$total);
						  $idpayment_log = $do_pay_log->getPrimaryKeyValue();
						  $do_payment_inv = new PaymentInvoice();
						  $do_payment_inv->addPaymentInvoice($idpayment_log,$_SESSION['do_invoice']->idinvoice,$total);
						  
						  
						  //$this->sendPaymentApprovedEmail($total,"Stripe.com",$transactionID);// Sending to customer
						  //$this->sendPaymentApprovedEmail($total,"Stripe.com",$transactionID,true); // Sending to user
							  $inv_qry = new sqlQuery($conx);
							  $date_paid = date("Y-m-d");
							  $status = 'Paid';
							  $invoice_note = 'Thanks for the business';
							  $sub_total  = $total;
							  $net_total = $total;
							  $amt_due = '0.00';
							  $sql_update_invoice = "UPDATE invoice set
														  amount = '$sub_total',
														  sub_total = '$sub_total',
														  net_total = '$net_total', 
														  status = '$status',
														  amt_due = '$amt_due',
														  datepaid  = '$date_paid'
												where idinvoice = ".$_SESSION['do_invoice']->idinvoice;echo $sql_update_invoice;
							  $inv_qry->query($sql_update_invoice);
							  $inv_qry->free();
							  
							$do_user_data = new User();
							$do_user_data->getId($_SESSION['do_invoice']->iduser);
					  
							$customer_name = $do_user_data->firstname.' '.$do_user_data->lastname;
							$customer_email = $do_user_data->email;
							$signature = $do_user_data->company.'<br />'.$do_user_data->firstname.' '.$do_user_data->lastname;


							$docustomer_invoice = new Invoice();
							$paid_memebership_amount = $docustomer_invoice->viewAmount($total);
							$docustomer_invoice->getId($_SESSION['do_invoice']->idinvoice);
							
							$email_data = Array('name' => $customer_name,
												'company'=>$do_user_data->company,
												'description'=>$docustomer_invoice->desc,
												'signature'=>$signature,
												'amount'=>$paid_memebership_amount,
												'num'=>$docustomer_invoice->num,
												'refnum'=>$result['response']['id'],
												'paytype'=>'Stripe', 
												'username'=>$do_user_data->firstname,
												'invoice_num' =>$docustomer_invoice->num
											  );
                  
						  //Notify User by email about his payment
						  if(!empty($customer_email)){          
							$emailer = new Radria_Emailer();
							$email_template = new EmailTemplate("ofuz_inv_payment_confirmation");
							$email_template->setSenderName($customer_name);
							$email_template->setSenderEmail($customer_email);
							$email_template->free();    
							$emailer->setEmailTemplate($email_template);
							$emailer->mergeArray($email_data);     
							
							$emailer->addTo($customer_email);print_r($emailer);
							//$emailer->send();
							
						  }

						  //Email for admin
						 
						  $doemail_template_adm = new EmailTemplate("ofuz_inv_payment_confirmation_adm");   
						  $doemail_template_adm->setSenderName('Admin');
						  $doemail_template_adm->setSenderEmail($admin_email);
						  $emailer2 = new Radria_Emailer();
						  $emailer2->setEmailTemplate($doemail_template_adm);
						  $emailer2->mergeArray($email_data);
						  $emailer2->addTo($admin_email); print_r($emailer);
						  //$emailer->send();

							
								   
						}
						
					}
                }
			  
            }
            $do_invoice->free();
        }
        
	 
?>
