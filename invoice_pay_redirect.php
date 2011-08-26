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
   // include_once('includes/header_secure.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    $invoice_access = true;
    if(empty($_GET["idinv"]) || empty($_GET["idcon"])){
        $invoice_access = false;
    }else{
        $do_user_rel = new UserRelations();
        $idinvoice = $do_user_rel->decrypt($_GET["idinv"]);
        $idcontact = $do_user_rel->decrypt($_GET["idcon"]);
    }
    if($invoice_access){
        if (!is_object($_SESSION['do_invoice'])) {
            $do_invoice = new Invoice();
            $do_invoice->sessionPersistent("do_invoice", "index.php", OFUZ_TTL);
        }
        $do_company = new Company();
        $_SESSION['do_invoice']->getId($idinvoice);
        if($idcontact != $_SESSION['do_invoice']->idcontact){ $invoice_access = false; }
        if($_SESSION['do_invoice']->discount){
          $dis = $_SESSION['do_invoice']->discount;
        }else{$dis = "";}
       // $invoice_cal_data = $_SESSION['do_invoice']->getInvoiceCalculations($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->amount,$dis);
    }

    //$do_user_detail = new User();
    //$do_user_detail->getId($_SESSION['do_invoice']->iduser);
	$do_user_detail = $_SESSION['do_invoice']->getParentUser();
	
    $user_settings = $do_user_detail->getChildUserSettings();    
    if($user_settings->getNumRows()){
        while($user_settings->next()){
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
            if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                $currency =  explode("-",$user_settings->setting_value) ;
                $_SESSION['do_invoice']->currency_iso_code = $currency[0];
                $_SESSION['do_invoice']->currency_sign = $currency[1];
                $_SESSION['do_invoice']->setCurrencyDisplay() ;
                $_SESSION['do_invoice']->getCurrencyPostion() ;
            }
        }
    }
	$_SESSION['do_invoice']->user_edit_amount = false;
    
	
	$reg_user = new RegistrationInvoiceLog();
	$reg_user_id = $reg_user->getUserIdRegistered($idinvoice,$do_user_detail->iduser);
	if($reg_user_id !== False) { $_SESSION['autologin_userid'] = $reg_user_id; } 
	
	if ($invoice_access) {
		header("Location: /invoice_pay_auth.php");
	} else { echo "Oups"; }
	exit;
	// will delete everything bellow once sure I do not need anything.
