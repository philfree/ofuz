<?php
/* * * * *
* This is script to update the user after the payment made for trail user. 
* This is curl post for this script
* @param Idinvoice. 
* * * * * */
ob_start();
include_once("config.php");

//amt_paid=$amt_paid&c=$check_sum&pay_type=$pay_type&pay_status=$payment_status

$invoice_id = $_POST['id'];
$payment_status = $_POST['pay_status'];

$invoice_id = '6';

$invoice = new Invoice();
$invoice->getId($invoice_id);
$iduser = $invoice->iduser;            
            


$date = date('Y-m-d');

$do_user = new User();
$do_user->getId($iduser);
$do_user->plan = 'Paid';
$do_user->regdate = $date;
$do_user->update();

if (!is_object($_SESSION['do_User'])) {
	
	if ($_SESSION['upgrade'] === true) {
		unset($_SESSION['upgrade']);
	}
}

?>
