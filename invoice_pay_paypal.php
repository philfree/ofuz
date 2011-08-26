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
    include_once('includes/header_secure.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    $invoice_access = true;

    //echo $req;
    if($invoice_access){
        if (!is_object($_SESSION['do_invoice'])) {
            echo _('Your page session has been expired. Please go back to the Invoice page and try again.');
            exit();
        }
       // if(!empty($_POST)){
         //   $status = $_POST["payment_status"];    
           // $ref_num = $_POST["txn_id"];
            //$amt_paid = $_POST["mc_gross"];
            //if($status == "Completed"){
              //  $do_pay_log = new PaymentLog();
               // if(!$do_pay_log->isTransRefExists($ref_num,$_SESSION['do_invoice']->idinvoice,"Paypal")){
                 //   $do_pay_log->addPaymentLog($ref_num,"Paypal",$_SESSION['do_invoice']->idinvoice,$amt_paid);
                   // $_SESSION['do_invoice']->updatePayment($amt_paid);
                    // $_SESSION['do_invoice']->sendPaymentApprovedEmail($amt_paid,"Paypal",$ref_num);
               // }
               // $_SESSION['in_page_message'] = _("This transaction has been approved.");
           // }else{
             //   $_SESSION['in_page_message'] = _("The transaction has been declined.'{$status}'");
          //  }
      //  }      
        $do_company = new Company();
        if($_SESSION['do_invoice']->discount){
          $dis = $_SESSION['do_invoice']->discount;
        }else{$dis = "";}
       // $invoice_cal_data = $_SESSION['do_invoice']->getInvoiceCalculations($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->amount,$dis);
    }

?>
<script type="text/javascript">
//<![CDATA[
function isAmountValid() {
	var total_amt = document.getElementById('amount').value;
	var due_amt = document.getElementById('due_amnt').value;
	if(parseFloat(total_amt) > parseFloat(due_amt)) {
		document.getElementById('msg_unauth').innerHTML = '<div class="messages_unauthorized">The Total Amount entered is greater than the invoice amount. Please re-enter.</div>';
		document.getElementById('msg_unauth').style.display = 'block';
		return false;
	}
}
//]]>
</script>
<?php //$do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php include_once('includes/ofuz_navtabs_invoice.php'); ?>
<?php //$do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$invoice_access){
              $msg = new Message(); 
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo $msg->getMessage("wrong_invoice_url");
              echo '</div></div><br /><br />';
              exit;
          }
    ?>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
    </td><td class="layout_rcolumn">
        <div class="contentfull">
			<div style="margin-left:0px;display:none;" id="msg_unauth"></div>
           <?php
              if($_SESSION['in_page_message'] != ''){
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo htmlentities($_SESSION['in_page_message']);
					//echo 'test test test';
                  $_SESSION['in_page_message'] = '';
                  echo '</div></div><br /><br />';
              }            

             echo nl2br($_SESSION['do_invoice']->invoice_address);
             echo '<br />'. _('Total due :').'<b>'.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due).'</b>';
             echo '<br /><br />';
             echo $_SESSION['do_invoice']->preparePaypalForm($_SESSION['do_invoice']->amt_due,$_SESSION['do_invoice']->num,$_SESSION['do_invoice']->description);
          ?>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php// include_once('includes/ofuz_facebook.php'); ?>
<?php //include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>