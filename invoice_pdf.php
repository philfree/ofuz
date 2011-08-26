<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    include_once('config.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    $invoice_access = true;
    if($_SESSION['do_invoice']->discount){
      $dis = $_SESSION['do_invoice']->discount;
    }else{$dis = "";}
   
    $do_user_detail = new User();
    $do_user_detail->getId($_SESSION['do_invoice']->iduser);
    $user_settings = $do_user_detail->getChildUserSettings();    
    if($user_settings->getNumRows()){
        while($user_settings->next()){
            if($user_settings->setting_name == 'invoice_logo' &&  $user_settings->setting_value != ''){
                $_SESSION['do_invoice']->inv_logo =  $user_settings->setting_value ;
            }
             if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                $currency =  explode("-",$user_settings->setting_value) ;
                $_SESSION['do_invoice']->currency_iso_code = $currency[0];
                $_SESSION['do_invoice']->currency_sign = $currency[1];
                $_SESSION['do_invoice']->setCurrencyDisplay() ;
                $_SESSION['do_invoice']->getCurrencyPostion() ;
            }
            if($user_settings->setting_name == 'inv_date_format' &&  $user_settings->setting_value != ''){
                $_SESSION['do_invoice']->inv_dd_format = $user_settings->setting_value;
	    }
        }
    }
global $invoice_url;
$do_user_rel = new UserRelations();
$fpdf_file_name = $do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'_'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
$invoice_url = $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
$fpdf_file_name = $fpdf_file_name.".pdf";

$fpdf_file_path = "invoice_pdf/";

require('html2fpdf-3.0.2b/html2fpdf.php');
require('html2fpdf-3.0.2b/fpdf_ofuz.php');
//$pdf=new HTML2FPDF();
$pdf=new ofuzFPDF();
$pdf->AddPage();

$body1 = '';


if($_SESSION['do_invoice']->status == 'Paid'){
	$color = "#009900";
}elseif($_SESSION['do_invoice']->status == 'Partial'){
	$color = "#EA8484";
}elseif($_SESSION['do_invoice']->status == 'Sent'){
	$color = "#677CDF";
}
else{  
	$color ="#000000";
}


if( $_SESSION['do_invoice']->inv_logo == ''){
	//$logo = 'files/sqllogo_small.jpg';
 	//$pdf->InitLogo($logo);
}else{

	$logo = 'files/'.$_SESSION['do_invoice']->inv_logo;
 	//$pdf->InitLogo($logo);
}

//$pdf->ln(8);

if($_SESSION['do_invoice']->status == 'Quote'){
			$inv .= _('Q U O T E');
}else{
			$inv .= _('I N V O I C E');
}
$current_date = date("m-d-Y");

$body1 .= '<table width="700px" cellspacing="0" cellpadding="0">';
$body1 .= '<tr><td>';
$body1 .= '<img src="'.$logo.'" />';
$body1 .= '</td></tr>';
$body1 .= '</table>';
$body1 .= '<table width="700px" cellspacing="0" cellpadding="0">';
$body1 .= '<tr><td align="left">'.$current_date.'</td><td align="right">'.$inv.'</td></tr>';
$body1 .= '<tr><td colspan="2">&nbsp;</td></tr>';
$body1 .= '<tr><td valign="top" width="200px">From : ';
$body1 .= '<br />'.$_SESSION['do_invoice']->Only1br($_SESSION['do_invoice']->getInvoiceSenderAddr($do_user_detail));
$body1 .= '</td>';
$body1 .= '<td valign="top" width="500px">To : ';
$body1 .= '<br />'.$_SESSION['do_invoice']->Only1br($_SESSION['do_invoice']->invoice_address);
$body1 .= '</td>';
$body1 .= '</tr>';
$body1 .= '</table>';
$body1 .= '<table width="700px">';
$body1 .= '<tr><td width="550px" align="right">';

if($_SESSION['do_invoice']->status == 'Quote'){
	$body1 .= _('Quote #');
}else{
	$body1 .= _('Invoice #');
}
$body1 .= '</td>';

$body1 .= '<td width="150px" align="right">'.$_SESSION['do_invoice']->num.'</td></tr>';

$body1 .= '<tr>';

$body1 .= '<td width="550px" align="right">'._('Date Created').'</td>';

$body1 .= '<td width="150px" align="right">'.$_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->datecreated).'</td></tr>';

$body1 .= '<tr>';
$body1 .= '<td width="550px" align="right">'._('Due Date').'</td>';
$body1 .= '<td width="150px" align="right">'.$_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->due_date).'</td></tr>';
$body1 .= '<tr>';
$body1 .= '<td width="550px" align="right">'._('Amount Due').'</td>';
$body1 .= '<td width="150px" align="right">';
$body1 .= $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due);
$body1 .= '</td></tr>';
$body1 .= '<tr>';
$body1 .= '<td width="550px" align="right">'._('Terms').'</td>';
$body1 .= '<td width="150px" align="right">'.$_SESSION['do_invoice']->invoice_term.'</td>';
$body1 .= '</tr>';
$body1 .= '</table>';

$body1 .= '<table width="700"><tr><td>';
$body1 .= nl2br($_SESSION['do_invoice']->description);
$body1 .= '</td></tr>';
$body1 .= '</table>';

$body1 .= '<table width="700" border="1">';
$body1 .= '<tr>';
$body1 .= '<td width="200" align="center"><b>'._('Item').'</b></td>';
//$body1 .= '<td width="150" align="center"><b>Description</b></td>';
$body1 .= '<td width="125" align="center"><b>'._('Price').'</b></td>';
$body1 .= '<td width="125" align="center"><b>'._('Quantity').'</b></td>';
$show_tax_amout = false ; 
$line_tax_amt = $_SESSION['do_invoice']->getTotalLineTax($_SESSION['do_invoice']->idinvoice);
if( $line_tax_amt !== false ){
	$show_tax_amout = true;
	$body1 .= '<td width="125" align="center"><b>'._('Tax(%)').'</b></td>';
}

$body1 .= '<td width="125" align="center"><b>'._('Total').'</b></td>';
$body1 .= '</tr>';

$do_invoice_line = $_SESSION['do_invoice']->getChildinvoiceline();
$price = 0;
while($do_invoice_line->next()){ 

	$body1 .= '<tr><td width="200">'.$do_invoice_line->item.'</td>';
	//$body1 .= '<td width="100">'.nl2br($do_invoice_line->description).'</td>';
	$body1 .= '<td width="125" align="right">';
	$body1 .= $_SESSION['do_invoice']->viewAmount($do_invoice_line->price);
	$body1 .= '</td>';
	$body1 .= '<td width="125" align="right">';
	$body1 .= $do_invoice_line->qty;
	$body1 .= '</td>';
	if($show_tax_amout === true ){
		$body1 .= '<td width="125" align="right">';
		$body1 .= $do_invoice_line->line_tax;
		$body1 .= '</td>';
	}
	$line_sub_tot = floatval($do_invoice_line->qty*$do_invoice_line->price);
	$body1 .= '<td width="125" align="right">';
	$body1 .= $_SESSION['do_invoice']->viewAmount($line_sub_tot);
	$body1 .= '</td></tr>';
	if($do_invoice_line->description) {
		$body1 .= '<tr><td colspan="5">'.nl2br($do_invoice_line->description).'</td></tr>';
	}
} 
$body1 .= '</table>';

$body1 .= '<table width="700px">';
$body1 .= '<tr><td colspan="2">&nbsp;</td></tr>';
$body1 .= '<tr><td width="550px" align="right"><b>'._('Subtotal:').'</b></td>';
$body1 .= '<td width="150px" align="right"><b>'.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->sub_total).'</b></td></tr>';

if($_SESSION['do_invoice']->discount != ''){
	$body1 .= '<tr><td width="550px" align="right">Discount -'.$_SESSION['do_invoice']->discount.'% :</td>';
	$body1 .= '<td width="150px" align="right">';
	$body1 .=  $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_discounted_amt);
	$body1 .= '</td></tr>';
} 
if($_SESSION['do_invoice']->total_taxed_amount > 0 ){
	$body1 .= '<tr><td width="550px" align="right">'._('Tax + :').'</td>';
	$body1 .= '<td width="150px" align="right">';
	//$body1 .=  $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_taxed_amount);
 $body1 .=  $_SESSION['do_invoice']->viewAmount($line_tax_amt);
	$body1 .= '</td></tr>';
 if($_SESSION['do_invoice']->tax > 0 ){ 
    $other_tax = $_SESSION['do_invoice']->total_taxed_amount - $line_tax_amt;
    $body1 .= '<tr><td width="550px" align="right">'._('Tax2').'  '.$_SESSION['do_invoice']->tax.'% :</td>';
    $body1 .= '<td width="150px" align="right">';
    $body1 .=  $_SESSION['do_invoice']->viewAmount($other_tax);
    $body1 .= '</td></tr>';
 }
}
$body1 .= '<tr><td width="550px" align="right"><b>'._('Total :').'</b></td><td width="150px" align="right">';
$body1 .= '<b>'. $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->net_total).'</b>';
$body1 .= '</td></tr>';
$body1 .= '<tr><td width="550px" align="right"><b>'._('Amount Paid :').'</b></td><td width="150px" align="right">';
$body1 .= '<b> '.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amount).'</b>';
$body1 .= '</td></tr><tr><td width="550px" align="right"><b>'._('Balance Due :').'</b></td>';
$body1 .= '<td width="150px" align="right"><b>'.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due).'</b>';
$body1 .= '</td></tr>';
$body1 .= '</table>';

$pdf->WriteHTML($body1);
$pdf->Output($fpdf_file_path.$fpdf_file_name);

header("Location: ".$fpdf_file_path.$fpdf_file_name);
?>