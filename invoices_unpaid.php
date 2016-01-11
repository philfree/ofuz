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
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
    if (!is_object($_SESSION['do_invoice_list_up'])) {
        $do_invoice_list = new Invoice();
        $do_invoice_list->sessionPersistent("do_invoice_list_up", "index.php", OFUZ_TTL);
        
    }
//echo $_SESSION['do_invoice_list']->getSqlQuery();
    $user_settings = $_SESSION['do_User']->getChildUserSettings();    
    if($user_settings->getNumRows()){
        while($user_settings->next()){
            if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                $currency =  explode("-",$user_settings->setting_value) ;
                $_SESSION['do_invoice_list_up']->currency_iso_code = $currency[0];
                $_SESSION['do_invoice_list_up']->currency_sign = $currency[1];
                //$_SESSION['do_invoice_list']->currency = $_SESSION['do_invoice_list']->currecy_sign ;
                $_SESSION['do_invoice_list_up']->setCurrencyDisplay() ;
                $_SESSION['do_invoice_list_up']->getCurrencyPostion() ;
            }
        }
    }

    $do_payment_inv = new PaymentInvoice();
    
?>
<script type="text/javascript">
//<![CDATA[

<?php include_once('includes/ofuz_js.inc.php'); ?>

$(document).ready(function() {
    $(".invoice_list_row").hover(
        function (){$(this).css('background-color','#edf6f7');},
        function (){$(this).css('background-color','#ffffff');}
    );
});

function hideTotals(){
    $(".layout_lcolumn").hide(0);
    $("#totals_txt").show(0);
}

function showTotals(){
    $(".layout_lcolumn").show(0);
    $("#totals_txt").hide(0);
}

function showExtraAmt(){
  $("#extra_amt").slideToggle("slow");
}

function closePastDue(){
    $.ajax({
        type: "GET",
<?php
$e_hide_past_due = new Event("do_invoice_list->eventClosePastDue");
$e_hide_past_due->setEventControler("ajax_evctl.php");
$e_hide_past_due->setSecure(false);
?>
        url: "<?php echo $e_hide_past_due->getUrl(); ?>",
        data: "a=1",
        success: function(hide_inv){ 
            $("#past_due_invoices").hide("fast");
            $("#past_due_txt").show("fast");
            $("#inv_msgs").show("fast");
        }
    });
}

function showPastDue(){
    $.ajax({
        type: "GET",
<?php
$e_show_past_due = new Event("do_invoice_list->eventShowPastDue");
$e_show_past_due->setEventControler("ajax_evctl.php");
$e_show_past_due->setSecure(false);
?>
        url: "<?php echo $e_show_past_due->getUrl(); ?>",
        data: "a=1",
        success: function(html_data){ 
           $("#past_due_txt").hide("fast");
           $("#inv_msgs").hide("fast");
           $("#show_past_due")[0].innerHTML = html_data;
        }
    });
}

function sendPastDueRemainder(){
    $("#email_msg").show("fast");
    $("#inv_msgs").hide("fast");
    
    $.ajax({
        type: "GET",
<?php
$e_show_past_due = new Event("do_invoice_list->eventSendPastDueRemainderEmail");
$e_show_past_due->setEventControler("ajax_evctl.php");
$e_show_past_due->setSecure(false);
?>
        url: "<?php echo $e_show_past_due->getUrl(); ?>",
        data: "a=1",
        success: function(html_data){ 
           //$("#past_due_txt").hide("fast");
           $("#email_msg")[0].innerHTML = html_data;
        }
    });
}

function setContactForCoworker(){
  $("#do_contact_sharing__eventShareContactsMultiple").submit();
}
//]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Invoices'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_rcolumn">
        <div id="email_msg" style="display:none">
            <div style="margin-left:0px;" class="messages_unauthorized">
		<?php echo _('Sending Emails......');?>
            </div>
        </div>
         <?php
                if($_SESSION['extra_amt'] != ''){
                           echo '<br />';
                           echo '<div style="margin-left:0px;">';
                           echo '<div class="messages_unauthorized">';
                           $cancel_extra_amt_pay = new Event("PaymentInvoice->eventCancelExtraAmtPay");
                           $cancel_link = $cancel_extra_amt_pay->getLink(_('Cancel'));
                           echo _('You can apply the amount ').$_SESSION['do_invoice_list']->viewAmount($_SESSION['extra_amt'])._(' for the Note'). ' "'.$_SESSION['ref_num'].'" '._('to any of the following invoices.').'&nbsp;&nbsp;'.$cancel_link;
                           echo '</div></div><br />';
                      }
          ?>
            <table class="invoice_list">
                <tr>
                    <th class="invoice_list_12pct center_text"><?php echo _('Invoice #');?></th>
                    <th class="invoice_list_40pct left_text"><?php echo _('Client');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('Due Date');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('Total Paid');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('Total Due');?></th>
                    <th class="invoice_list_12pct center_text">Status</th>
                </tr>
                <?php
                                  //echo $_SESSION['do_invoice_list']->getSqlQuery()
                  $_SESSION['do_invoice_list_up']->getAllUnpaidInvoices();
                  if($_SESSION['do_invoice_list_up']->getNumRows() > 0 ){
                    $do_contact = new Contact();
                    while($_SESSION['do_invoice_list_up']->next()){
                      $currentpage = $_SERVER['PHP_SELF'];
                      $e_detail = new Event("mydb.gotoPage");
                      $e_detail->addParam("goto", "invoice.php");
                      $e_detail->addParam("idinvoice",$_SESSION['do_invoice_list_up']->idinvoice);
                      $e_detail->addParam("tablename", "invoice");
                      $e_detail->requestSave("eDetail_invoice", $currentpage);
                      //echo $_SESSION['do_invoice']->idcompany;
                      if($_SESSION['do_invoice_list_up']->discount){
                        $dis = $_SESSION['do_invoice_list_up']->discount;
                      }else{$dis = "";}
                     // $invoice_cal_data = $_SESSION['do_invoice']->getInvoiceCalculations($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->amount,$dis);

                      if($_SESSION['do_invoice_list_up']->status == 'Paid'){
                          $statuscolor = "#009900";
                      }elseif($_SESSION['do_invoice_list_up']->status == 'Partial'){
                          $statuscolor = "#EA8484";
                      }elseif($_SESSION['do_invoice_list_up']->status == 'Sent'){
                          $statuscolor = "#677CDF";
                      }else{  
                          $statuscolor ="#000000";
                      }
                ?>
                <tr class="invoice_list_row" onclick="window.location.href='<?php echo "/Invoice/".$_SESSION['do_invoice_list_up']->idinvoice?>'">
                    <td class="center_text"><?php echo $_SESSION['do_invoice_list_up']->num; ?></td>
                    <td class="left_text"><?php echo $do_contact->getContact_Company_ForInvoice($_SESSION['do_invoice_list_up']->idcontact,$_SESSION['do_invoice_list_up']->idcompany); ?></td>
                    <td class="center_text"><?php echo $_SESSION['do_invoice_list_up']->due_date; ?></td>
                    <td class="center_text"><?php echo $_SESSION['do_invoice_list_up']->viewAmount($_SESSION['do_invoice_list_up']->amount); ?></td>
                    <td class="center_text"><?php echo $_SESSION['do_invoice_list_up']->viewAmount($_SESSION['do_invoice_list_up']->net_total); ?></td>
                    <td class="center_text" style="color: <?php echo $statuscolor; ?>"><?php echo $_SESSION['do_invoice_list_up']->status; ?></td>
                </tr>
                <?php } } ?>
            </table>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
