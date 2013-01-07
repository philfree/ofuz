<?php 
ob_start();
include_once("config.php");
include_once("class/OfuzApiMethods.class.php");
include_once("class/OfuzApiClient.class.php");

$api_key = 'f1976041736ccb95fbb322e1e5c07cbf';// replace this with your API key




$user = new User();
$iduser=$user->validateAPIKey($api_key);
$idcontact=$_SESSION['do_User']->idcontact;


//check for invoice already generated but not paid, if yes redirect to payment page
if(!empty($idcontact)){
$invoice = new Invoice();
$idinvoice = $invoice->getContactInvoiceDetailsForPlanUpgrade($idcontact,$iduser);
//echo "idinvoice: ".$idinvoice;exit();
if($idinvoice != '0'){
	$do_api_user_rel = new UserRelations();
    //$pay_url =  'http://ofuz.localhost/pay/'.$do_api_user_rel->encrypt($idinvoice).'/'.$do_api_user_rel->encrypt($idcontact);
    $pay_url =  $GLOBALS['cfg_ofuz_site_https_base'].'pay/'.$do_api_user_rel->encrypt($idinvoice).'/'.$do_api_user_rel->encrypt($idcontact);
    @header("location:$pay_url");
	exit();
}

}


$do_ofuz = new OfuzApiClient();
$iduser = $do_ofuz->setAuth($api_key);
$do_ofuz->format = "php";// json,xml,php

$do_ofuz->firstname = $_SESSION['do_User']->firstname;
$do_ofuz->lastname = $_SESSION['do_User']->lastname;
$response = $do_ofuz->search_contact();

$response = unserialize($response);
//echo'<pre>';print_r($response);echo'</pre>';




 if($response[stat] == 'fail'){
	$do_ofuz->firstname = $_SESSION['do_User']->firstname;
	$do_ofuz->lastname = $_SESSION['do_User']->lastname;
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
	  $user->getId($_SESSION['do_User']->iduser);
	  $user->idcontact = $response[idcontact];
	  $user->update();
	  $idcontact = $response[idcontact];
	}
}






$do_ofuz = new OfuzApiMethods();
$do_ofuz->iduser = $iduser;
$do_ofuz->idcontact = $idcontact;// Required
$do_ofuz->type = 'Invoice'; // Possible values Quote,Invoice
$do_ofuz->due_date = date('Y-m-d');// Format Should be yyyy-mm-dd
$do_ofuz->invoice_term = 'Upon Receipt';
$do_ofuz->invoice_note = 'Thanks for the business';
$do_ofuz->description = 'Invoice for plan upgrade';
$do_ofuz->discount = ''; // Should be as 10,10.55,0.50,.5 
$do_ofuz->amt_due = '24.00';
$do_ofuz->sub_total='24.00';
$do_ofuz->net_total='24.00';
//$do_ofuz->callback_url = 'api_upgrade_invoice.php';
$do_ofuz->url = "http://".$_SERVER['HTTP_HOST']."/"."plan_upgrade.php";
$do_ofuz->next_url = "http://".$_SERVER['HTTP_HOST']."/"."index.php";
$response = $do_ofuz->upgrade_plan_add_invoice();

@header("location:$response");

?>
