<?php

require_once('config.php');
include_once("class/OfuzApiMethods.class.php");
include_once("class/OfuzApiClient.class.php");


require_once('class/User.class.php');
require_once('class/Stripe.class.php');
require_once("class/stripe-lib/Stripe.php");

$api_key = 'f1976041736ccb95fbb322e1e5c07cbf';// replace this with your API key

$membership_amount = 24.00; 

 
  set_time_limit(3600);   

  $do_user = new User();
  $adm_iduser=$do_user->validateAPIKey($api_key);

  if($adm_iduser === false){
    echo "Supplied api key is wrong"; 
    exit();
  }


  $today = date('Y-m-d');
  echo 'Monthly billing for Paid users ', $today, "\n\n";


  $do_invoice1 = new Invoice();
  $stripe_details  = $do_invoice1->getUserStripeDetails($adm_iduser);  
  if($stripe_details !== false){
    $stripe_api_key = $stripe_details['stripe_api_key'];  
    $admin_email = $stripe_details['email'];
  }else{
    echo "Stripe api key is Missing"; 
    exit();
  }
  

  $read_qry = new sqlQuery($conx);
  $sql_get_user = "SELECT * FROM recurrentinvoice r INNER JOIN user u ON r.iduser = u.iduser WHERE (u.plan = 'paid') AND r.nextdate = '".$today."'";
  $read_qry->query($sql_get_user);

  if($read_qry->getNumrows() > 0){ 
     while($read_qry->fetch()){
      $idcontact = $read_qry->getData('idcontact');
      $iduser = $read_qry->getData('iduser');
      //check for invoice already generated but not paid, if yes redirect to payment page
      if(!empty($idcontact)){
        $do_invoice = new Invoice();
        $idinvoice = $do_invoice->getContactInvoiceDetailsForPlanUpgrade($idcontact,$adm_iduser);        
        if($idinvoice == '0'){
          $do_ofuz = new OfuzApiClient($api_key,'php');
          $do_ofuz->firstname = $read_qry->getData('firstname');
          $do_ofuz->lastname = $read_qry->getData('lastname');
          $response = $do_ofuz->search_contact();
          $response = unserialize($response);


          if($response[stat] == 'fail'){
              $do_ofuz->firstname = $read_qry->getData('firstname');
              $do_ofuz->lastname = $read_qry->getData('lastname'); 
              $do_ofuz->position = '';
              $do_ofuz->company = '';
              $do_ofuz->phone_work = '';
              $do_ofuz->phone_home = '';
              $do_ofuz->mobile_number = '';
              $do_ofuz->fax_number = '';
              $do_ofuz->phone_other = '';
              $do_ofuz->email_work = '';
              $do_ofuz->email_home = '';
              $do_ofuz->email_other = '';
              $do_ofuz->company_website = '';
              $do_ofuz->personal_website = '';
              $do_ofuz->blog_url = '';
              $do_ofuz->twitter_profile_url = '';
              $do_ofuz->linkedin_profile_url = '';
              $do_ofuz->facebook_profile_url = '';
              $tags = 'API,Upgrade Plan'; // Comma seperated tags
              $do_ofuz->tags = $tags;
              $response = $do_ofuz->add_contact();
              $response = unserialize($response);
             

              if(!empty($response[idcontact])){  
                $user = new User(); 
                $user->getId($iduser);
                $user->idcontact = $response[idcontact];
                $user->update();
                $idcontact = $response[idcontact];
              }
            } 
            

            $do_ofuz = new OfuzApiMethods();
            $do_ofuz->iduser = $adm_iduser;
            $do_ofuz->idcontact = $idcontact;// Required
            $do_ofuz->type = 'Invoice'; // Possible values Quote,Invoice
            $do_ofuz->due_date = date('Y-m-d');// Format Should be yyyy-mm-dd
            $do_ofuz->invoice_term = 'Upon Receipt';
            $do_ofuz->invoice_note = 'Thanks for the business';
            $do_ofuz->description = 'User Membership Recurrent Billing';
            $do_ofuz->discount = ''; // Should be as 10,10.55,0.50,.5 
            $do_ofuz->amt_due = '24.00';
            $do_ofuz->sub_total='24.00';
            $do_ofuz->net_total='24.00';           
            $idinvoice = $do_ofuz->cron_billing_add_invoice($iduser);
            
        }
      }

      $read_qry2 = new sqlQuery($conx);
      $sql_get_ccdetails = "SELECT token  from cc_details where iduser = '{$iduser}'";
      $read_qry2->query($sql_get_ccdetails);
      
      if($read_qry2->getNumrows() > 0){
        $read_qry2->fetch();
        $user_tokenid = $read_qry2->getData('token');
      }else{
        $user_tokenid = '';
      }

   

      if(!empty($user_tokenid)){
        $name = $read_qry->getData('name');
        $email = $read_qry->getData('email');
        $description = $name;
        
        $srtipecustomer_id = $user_tokenid;           
        //Amount need to conver to cents 
        $total_amount = $membership_amount*100;             

        $payment = new StripeGateWay(false, $stripe_api_key);      
        if(!empty($srtipecustomer_id)){      
          $result = $payment->ChargeExsistingCustomer($srtipecustomer_id,$total_amount);
        }
         

        if($result['success'] == '1'){
          //set the amout back to $ value 
          $re_membership_amount = $total_amount/100;                 
      
          //Add the customer id in to stripe details class
          if(isset($result['customer_id'])){ 
            $do_invoice->saveStripeCustomerId($adm_iduser,$read_qry->getData('idcontact'),$result['customer_id']);
          }                   
            
          $do_pay_log = new PaymentLog();
          $do_pay_log->addPaymentLog($result['response']['id'],"Stripe",$idinvoice,$total);
          $idpayment_log = $do_pay_log->getPrimaryKeyValue();
          $do_payment_inv = new PaymentInvoice();
          $do_payment_inv->addPaymentInvoice($idpayment_log,$idinvoice,$total);

          $date_paid = $today;
          $status = 'Paid';
          $invoice_note = 'Thanks for the business';
          $sub_total  = $re_membership_amount;
          $net_total = $re_membership_amount;
          $amt_due = '0.00';
          $sql_update_invoice = "UPDATE invoice set 
                                      sub_total = '$sub_total',
                                      net_total = '$net_total', 
                                      status = '$status',
                                      amt_due = '$amt_due',
                                      datepaid  = '$date_paid'
                            where idinvoice = ".$idinvoice;
          $do_invoice->query($sql_update_invoice );
      
        $do_user_data = new User();
        $do_user_data->getId($iduser);
  
        $customer_name = $do_user_data->firstname.' '.$do_user_data->lastname;
        $customer_email = $do_user_data->email;
        $signature = $do_user_data->company.'<br />'.$do_user_data->firstname.' '.$do_user_data->lastname;


        $docustomer_invoice = new Invoice();
        $paid_memebership_amount = $docustomer_invoice->viewAmount($re_membership_amount);
        $docustomer_invoice->getId($idinvoice);
        
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

              
          $do_user = new User();               
          $date = date('Y-m-d');
          $do_user->query("update user set plan='paid' where iduser=".$iduser);                              

          $do_recurrentInvoice = new RecurrentInvoice();
          $do_recurrentInvoice->addRecurrentInvoice($idinvoice,'1','Month',date("Y-m-d"),$iduser);                 
          echo "Paid";
        }else{
          $do_user = new User();                          
          $do_user->query("update user set status='suspend'  where iduser=".$iduser);  
          echo "Not paid declined";
        }

        $do_pay_log->free();
        $do_payment_inv->free();
        $do_invoice1->free();
        $do_invoice->free();
        $do_user->free();
        $do_recurrentInvoice->free();
        $payment->free();
        $read_qry2->free();
      }
    }
  }else{
    echo "<br/> No users for this date" ;
  }
?>