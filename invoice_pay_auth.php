<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Invoices';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    //include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header_secure.inc.php');

    //$do_notes = new ContactNotes($GLOBALS['conx']);
    //$do_contact = new Contact($GLOBALS['conx']);
    //$do_company = new Company($GLOBALS['conx']);
    //$do_task = new Task($GLOBALS['conx']);
    //$do_task_category = new TaskCategory($GLOBALS['conx']);
    //$do_contact_task = new Contact();
    $invoice_access = true;
    
    if($invoice_access){
        if (!is_object($_SESSION['do_invoice'])) {
            //$do_invoice = new Invoice();
            //$do_invoice->sessionPersistent("do_invoice", "index.php", OFUZ_TTL);
            echo _('Your page session has been expired. Please go back to the Invoice page and try again.');
            exit();
        }
        $do_company = new Company();
        
    }
    $RecurrentInvoice = new RecurrentInvoice();
    $id_rec = $RecurrentInvoice->checkIfInvoiceIsInRecurrent($_SESSION['do_invoice']->idinvoice);// Check if recurrent
    if($id_rec){
         $is_recurrent = $id_rec;
         $RecurrentInvoiceCC = new RecurrentInvoiceCC();
         $id_rec_cc = $RecurrentInvoiceCC->has_cc_info($id_rec);//Check if having CC in DB
         if(!$id_rec_cc){ $id_rec_cc = 0; }else{ $id_rec_cc = 1; }//idrecurrentinvoice
    }else{ $is_recurrent = 0; $id_rec_cc = 0; }

   
?>
<script type="text/javascript">
//<![CDATA[

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
    <!--<div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Invoice'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
            <a href="#">Pay By Paypal</a> <br />
            <a href="#">Pay By Authorized.Net</a> <br />
             </div>
        </div>
        <div class="left_menu_footer"></div><br /><br />-->
    </td><td class="layout_rcolumn">
        <div class="contentfull">
           <?php
              if($_SESSION['in_page_message'] != ''){
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo htmlentities($_SESSION['in_page_message']);
                  $_SESSION['in_page_message'] = '';
                  echo '</div></div><br /><br />';
              }            

             echo nl2br($_SESSION['do_invoice']->invoice_address);
             //echo '<br />'. _('Total due :').'<b>$'. number_format($invoice_cal_data["total_due_amt"],2, '.', ',' ).'</b>';
             echo '<br />'. _('Total due :').'<b>'. $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due).'</b>';
             echo '<br /><br />';
             $do_user_rel = new UserRelations();
             $invoice_url = $GLOBALS['cfg_ofuz_site_http_base'].'inv/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
             $do_payment = new Event("do_invoice->eventProcessAuthNetPayment");
             $do_payment->addParam("is_rec", $is_recurrent);
             $do_payment->addParam("is_cc", $id_rec_cc); // send 0 if not in recurrent else send 1
             $do_payment->addParam("goto", $invoice_url); // send 0 if no CC else send 1
			 $do_payment->addParam("error_page", "invoice_pay_auth.php");
             echo $do_payment->getFormHeader();
             echo $do_payment->getFormEvent();
             echo $_SESSION['do_invoice']->prepareAuthnetForm($_SESSION['do_invoice']->amt_due);//sending the amout 
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
