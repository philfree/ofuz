<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Invoices';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    //include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    $invoice_access = true;

        if(!empty($_POST)){
            $status = $_POST["payment_status"];    
            $ref_num = $_POST["txn_id"];
            $amt_paid = $_POST["mc_gross"];
            $id  = $_POST["item_number"]; 
	    

            if (!is_object($_SESSION['do_invoice'])) {
                $do_invoice = new Invoice();
                $do_invoice->sessionPersistent("do_invoice", "index.php", OFUZ_TTL);
            }
            $_SESSION['do_invoice']->getId($id);
            if($status == "Completed"){
                $do_pay_log = new PaymentLog();
                $do_pay_log->addPaymentLog($ref_num,"Paypal",$_SESSION['do_invoice']->idinvoice,$amt_paid);
                $idpayment_log = $do_pay_log->getPrimaryKeyValue();
                $do_payment_inv = new PaymentInvoice();
                $do_payment_inv->addPaymentInvoice($idpayment_log,$_SESSION['do_invoice']->idinvoice,$amt_paid);
                $_SESSION['do_invoice']->updatePayment($amt_paid);


               // if(!$do_pay_log->isTransRefExists($ref_num,$_SESSION['do_invoice']->idinvoice,"Paypal")){
                   // $do_pay_log->addPaymentLog($ref_num,"Paypal",$_SESSION['do_invoice']->idinvoice,$amt_paid);
                   // $_SESSION['do_invoice']->updatePayment($amt_paid);
                     $_SESSION['do_invoice']->sendPaymentApprovedEmail($amt_paid,"Paypal",$ref_num);
                      $_SESSION['do_invoice']->sendPaymentApprovedEmail($amt_paid,"Paypal",$ref_num,true);// True for sending the ofuz user
                      /*
                         Lets check if the invoice has an call back URL and process that
                      */
                      $do_inv_callback = new InvoiceCallback();
                      $do_inv_callback->processCallBack($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->num,$amt_paid,$_SESSION['do_invoice']->iduser,"ok","Paypal",$ref_num);
                //}
                $_SESSION['in_page_message'] = _("This transaction has been approved.");
            }else{
                $do_inv_callback = new InvoiceCallback();
                $do_inv_callback->processCallBack($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->num,$amt_paid,$_SESSION['do_invoice']->iduser,"fail","Paypal","",$status);
                $_SESSION['in_page_message'] = _("The transaction has been declined.'{$status}'");
            }
        }

?>