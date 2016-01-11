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
    if (!is_object($_SESSION['do_invoice_list'])) {
        $do_invoice_list = new Invoice();
        $do_invoice_list->sessionPersistent("do_invoice_list", "index.php", OFUZ_TTL);
        
    }
//echo $_SESSION['do_invoice_list']->getSqlQuery();
    $user_settings = $_SESSION['do_User']->getChildUserSettings();    
    if($user_settings->getNumRows()){
        while($user_settings->next()){
            if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                $currency =  explode("-",$user_settings->setting_value) ;
                $_SESSION['do_invoice_list']->currency_iso_code = $currency[0];
                $_SESSION['do_invoice_list']->currency_sign = $currency[1];
                //$_SESSION['do_invoice_list']->currency = $_SESSION['do_invoice_list']->currecy_sign ;
                $_SESSION['do_invoice_list']->setCurrencyDisplay() ;
                $_SESSION['do_invoice_list']->getCurrencyPostion() ;
            }
        }
    }

    $do_payment_inv = new PaymentInvoice();
    
?>
<script type="text/javascript">
//<![CDATA[

<?php include_once('includes/ofuz_js.inc.php'); ?>


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
        

        </div>

        
        <div class="contentfull">
           
                <?php 
                if($_SESSION['in_page_message'] != ''){
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo htmlentities($_SESSION['in_page_message']);
                  if($_SESSION['in_page_message_inv_amt_more'] == 'Yes'){
                      $e_pay_mul = new Event("do_invoice->eventAddMultiPayment");
                      $e_pay_mul->addParam('id',$_SESSION['do_invoice']->idinvoice);
                      $e_pay_mul->addParam('contact',$_SESSION['do_invoice']->idcontact);
                      $e_pay_mul->addParam('amt', $_SESSION['payment_amt']);
                      $e_pay_mul->addParam('ref_num', $_SESSION['ref_num']);
                      $e_pay_mul->addParam("goto",$_SERVER['PHP_SELF']);
                      echo '<br />'.$e_pay_mul->getLink('Yes');
                      $e_pay_cancel = new Event("do_invoice->eventCancelMultiPayment");
                      echo '&nbsp;&nbsp;&nbsp;&nbsp;'.$e_pay_cancel->getLink('Cancel');
                      $_SESSION['in_page_message_inv_amt_more'] = '';
                  }elseif($_SESSION['in_page_message_inv_mul_pay_del'] == 'Yes'){
                      if(isset($_SESSION["in_page_message_inv_idpaymentlog"]) && $_SESSION["in_page_message_inv_idpaymentlog"] != '' ){
                            $do_paymentlog = new Paymentlog();
                            $do_paymentlog->getMulInvoicesForPayment((int)$_SESSION["in_page_message_inv_idpaymentlog"]);
                            $do_invoice_del = new Invoice() ;
                            $do_contact = new Contact();
                            echo '<div style="width:700px;margin-left:0px;margin-top:5px;height:30px;text-align:left;position: relative; color:#FFA500;font-size:14px;">';
                            echo '<div style="width:100px;float:left;"><b>'._('Number').'</b></div>';
                            echo '<div style="width:250px;float:left;"><b>'._('Contact').'</b></div>';
                            echo '<div style="width:100px;float:left;"><b>'._('Due Date').'</b></div>';
                            echo '<div style="width:100px;float:left;"><b>'._('Total').'</b></div>';
                            echo '<div style="width:100px;float:left;"><b>'._('Due').'</b></div>';
                            echo '</div>';
                            echo '<div style="width:700px;margin-left:0px;margin-top:5px;height:30px;text-align:left;position: relative;font-size:12px;">';
                            while($do_paymentlog->next()){
                                $do_invoice_del->getId($do_paymentlog->idinvoice);
                                echo  '<div style="width:100px;float:left;">'.$do_invoice_del->num.' </div> ';
                                $contact = $do_contact->getContact_Company_ForInvoice($do_invoice_del->idcontact,$do_invoice_del->idcompany);
                                if($contact == ''){ $contact = '--'; }
                                echo  ' <div style="width:250px;float:left;">'.$contact.'</div>';
                                echo  '<div style="width:100px;float:left;">'.$do_invoice_del->getInvFormattedDate($do_invoice_del->due_date).'</div>';
                                echo  '<div style="width:100px;float:left;">'.$do_invoice_del->viewAmount($do_invoice_del->net_total).'</div>';
                                echo  '<div style="width:100px;float:left;">'.$do_invoice_del->viewAmount($do_invoice_del->amt_due).'</div>';
                            }
                            echo '</div>';
                            echo '<br /><br />';
                            $e_del_payment = new Event("PaymentLog->eventDeletePaymentLog");
                            $e_del_payment->addParam("id",(int)$_SESSION["in_page_message_inv_idpaymentlog"]);
                            $e_del_payment->addParam("del_mul_confirm","Yes");
                            echo $e_del_payment->getLink(_('delete'));
                            echo '&nbsp;&nbsp;&nbsp;&nbsp;';
                            echo '<a href="/Invoice/'.$_SESSION['do_invoice']->idinvoice.'">cancel</a>';
                      }
                  }
                  $_SESSION['in_page_message'] = '';
                  echo '</div></div><br /><br />';
              }
            ?>
            
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
