<?php 
include_once("class/OfuzApiClient.class.php");
$api_key = '4a974e2d0d18d5257f064fd33972390e';// replace this with your API key
$do_ofuz = new OfuzApiClient();
$iduser = $do_ofuz->setAuth($api_key);

$do_ofuz->format = "json";// json,xml,php
//include_once('config.php');

/*if(!empty($_POST)){

    $id_invoice = $_POST["id"];
    $inv_num = $_POST["num"];
    $amt_paid = $_POST["amt_paid"];
    $chk_sum = $_POST["c"];
    $pay_type = $_POST["pay_type"];
    $ref_num = $_POST["ref_num"];
    
    if(md5($inv_num.$amt_paid.$api_key) == $chk_sum){
        $body = "\r\n".'id = '.$id_invoice;
        $body .= "\r\n".'num = '.$inv_num;
        $body .= "\r\n".'amt_paid = '.$amt_paid;
        $body .= "\r\n".'pay_type = '.$pay_type;
        $body .= "\r\n".'ref_num = '.$ref_num;

        //$to      = 'philippe@sqlfusion.com';
        $to      = 'abhik@sqlfusion.com';
        $subject = 'Curl Post For Invoice';
        $message = $body;
        $headers = 'From: info@sqlfusion.com' . "\r\n" .
            'Reply-To: info@sqlfusion.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
        
        mail($to, $subject, $message, $headers);
    }

}
*/
/*
 Example How to add a contact
***********************************************************************
/

// First OR Last Name is required rest is optional
/*$do_ofuz->firstname = 'First name';
$do_ofuz->lastname = 'last name';
$do_ofuz->position = 'PHP Developer';
$do_ofuz->company = 'Sqlfusion';
$do_ofuz->phone_work = '1111111';
$do_ofuz->phone_home = '2222222';
$do_ofuz->mobile_number = '3333333';
$do_ofuz->fax_number = '4444444';
$do_ofuz->phone_other = '5555555';
$do_ofuz->email_work = 'abhik@work.com';
$do_ofuz->email_home = 'abhik@home.com';
$do_ofuz->email_other = 'abhik@other.com';
$do_ofuz->company_website = 'http://www.sqlfusion.com';
$do_ofuz->personal_website = 'http://www.abhik.in';
$do_ofuz->blog_url = 'http://www.abhik.in/blog';
$do_ofuz->twitter_profile_url = 'http://www.twitter.com';
$do_ofuz->linkedin_profile_url = 'http://www.linkedin.com';
$do_ofuz->facebook_profile_url = 'http://www.facebook.com';
$tags = 'API,Test contact'; // Comma seperated tags
$do_ofuz->tags = $tags;
$response = $do_ofuz->add_contact();
*/

/*
How to search
******************************************************************************
*/
// Atleast one search criteria is needed
/*$do_ofuz->firstname = 'Yanai';
$do_ofuz->lastname = "ARFI";
$do_ofuz->email = 'jobphp@boxtelecom.com';
$response = $do_ofuz->search_contact();
*/

/*
How to add tag
******************************************************************************
*/
/*
$do_ofuz->idreference = 3631; // Id of the contact required
$do_ofuz->tags = 'room-mate,buddy';//comma seperated tags
$do_ofuz->reference = 'contact'; // optional only contacts can be tagged now.
$response = $do_ofuz->add_tag();
*/

/*
How to delete tag
******************************************************************************
*/
/*
$do_ofuz->idreference = 3631;
$do_ofuz->tags = 'room-mate,buddy';//comma seperated tags
$do_ofuz->reference = 'contact'; // optional only contacts tag can be deleted now.
$response = $do_ofuz->delete_tag();
*/

/*
How to add note for a contact
******************************************************************************
*/
/*
$do_ofuz->idcontact = 5929; // Contact ID required 
$do_ofuz->note = 'Lets add a note';//Note to be added required Max 200 character
$response = $do_ofuz->add_note();
*/

/*
How to add task with or without a contact
******************************************************************************
*/
/*
$do_ofuz->idcontact = 3631; // Optional, if no contact then task will be added as self task
$do_ofuz->task_description = 'Testing some task';//required, the task description
$do_ofuz->due_date = '2009-06-23'; // optional default today. Formal should be Y-m-d
$do_ofuz->task_category = 'Call';// Optional default is Email
$response = $do_ofuz->add_task();
*/

/*
How to get all contacts
******************************************************************************
*/
/*
$response = $do_ofuz->get_contacts();
*/

/*
  How to add Invoice
*******************************************************************************
*/

/*$do_ofuz->idcontact = 3652;// Required
$do_ofuz->type = 'Invoice'; // Possible values Quote,Invoice
$do_ofuz->due_date = date('Y-m-d');// Format Should be yyyy-mm-dd
$do_ofuz->invoice_term = 'Upon Receipt';
$do_ofuz->invoice_note = 'Thanks for the business';
$do_ofuz->description = 'Invoice Creation with a call back url';
$do_ofuz->discount = '10'; // Should be as 10,10.55,0.50,.5 
$do_ofuz->callback_url = 'http://dev.ofuz.net/api_test.php';
$response = $do_ofuz->add_invoice();

*/

/*
    How to Add InvoiceLine
**********************************************************************************
*/

/*$do_ofuz->idinvoice = 35; // Required and should belong to user
$do_ofuz->description = 'Algorithm For the Invoice';
$do_ofuz->price = '100.00'; // Should be as  100.59 , 100 , 0.78, .78 , 2987 etc
$do_ofuz->qty = '2.0' ; // should be as 5 , 5.0 -- 3.4, .5 etc are not allowed
$do_ofuz->item = 'Add Payment';
$response = $do_ofuz->add_invoice_line();
*/


/*
    How to set Invoice as Recurrent
************************************************************************************
*/
/*
$do_ofuz->idinvoice = 35; // Required and should belong to user
$do_ofuz->recurrence = 1; // Required and must be a number
$do_ofuz->recurrencetype = 'Month'; // Possible values are "Day","Week","Month","Year"
$do_ofuz->callback_url = 'http://dev.ofuz.com/api_test1.php';
$response = $do_ofuz->add_recurrent();
*/

//If the format is php then use the following code to display
//$response = unserialize($response);
/*print_r ($response);
if ($response['stat'] == "fail") {
    echo "<b>Error: (".$response['code'].") ".$response['msg']."</b>";
} 
if ($response['stat'] == "ok") {
    echo "<b>Success: (".$response['code'].") ".$response['msg']."</b>";
}*/


//If the format is json then use the following code to display
//$response = json_decode($response);
print_r($response);


//If the out put is xml then need to parse the xml 
//to generate the output
//echo $response;

?>