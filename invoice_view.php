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

    if (is_object($_SESSION['do_User'])) {
	  try {
	    if (!$_SESSION['do_User']->iduser) {
		unset($_SESSION['do_User']);
		header("Location: ".$GLOBALS['cfg_ofuz_site_http_base'].$_SERVER['REQUEST_URI']);
	    }
	  } catch (Exception $e) { 
		unset($_SESSION['do_User']);
		header("Location: ".$GLOBALS['cfg_ofuz_site_http_base'].$_SERVER['REQUEST_URI']);
	  }
	}

    include_once('includes/header_secure.inc.php');

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
        if($_SESSION['do_invoice']->isDeleted($idinvoice)){ $invoice_access = false; }
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
            if($user_settings->setting_name == 'inv_date_format' &&  $user_settings->setting_value != ''){
                $_SESSION['do_invoice']->inv_dd_format = $user_settings->setting_value;
	    }
        }
    }
    
?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php include_once('includes/ofuz_navtabs_invoice.php'); ?>
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
<?php
$do_inv_payment_log = new PaymentLog();
$do_inv_payment_log->getPaymentLog($_SESSION['do_invoice']->idinvoice);
$num_pay = $do_inv_payment_log->getNumRows();
if($num_pay) {
?>
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Payments'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
            <?php
                //Payment Log Starts Here

                    while($do_inv_payment_log->next()){
                        echo '<span class="text12"><span class="text_lightblue">',$do_inv_payment_log->ref_num,'</span> <span class="sep1">|</span> <b>',$_SESSION['do_invoice']->viewAmount($do_inv_payment_log->amount),'</b><br />',
                        '<span class="text_darkgray">Received: ',date('F j, Y',$do_inv_payment_log->timestamp),'</span></span>',
                        '<div class="invoiceline12"></div>';
                    }

                // Payment Log Ends here
             ?>
            </div>
        </div>
        <div class="left_menu_footer"></div><br /><br />
<?php } ?>

<!-- Show other due invoices if any. -->
<?php
$do_other_due_inv = new Invoice();
$do_other_due_inv->getDueInvoicesForContact($idcontact);
if($do_other_due_inv->getNumRows()) {
?>
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Other invoice due'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
				<table width="100%">
				<?php
					$do_user_rel = new UserRelations();
					while($do_other_due_inv->next()) {
						$invoice_url = "";
						$invoice_url = $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_user_rel->encrypt($do_other_due_inv->idinvoice).'/'.$do_user_rel->encrypt($do_other_due_inv->idcontact);

						$due_inv_link = $do_other_due_inv->num." - ".$do_other_due_inv->datecreated." - ".$_SESSION['do_invoice']->viewAmount($do_other_due_inv->amt_due);

						echo '<a href="'.$invoice_url.'">'.$due_inv_link.'</a>';
						echo '<br />';

					}
				?>
				</table>
            </div>
        </div>
        <div class="left_menu_footer"></div><br /><br />
<?php
}
?>

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
             ?>
            <table class="layout_columns"><tr>
                <td>
                    <span class="text12"><b><?php echo _('Date Created'); ?>:</b> <?php echo $_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->datecreated);; ?></span>
                </td>
                <?php
                    if($_SESSION['do_invoice']->status == 'Paid'){
                        $statuscolor = '#31a660';
                    }elseif($_SESSION['do_invoice']->status == 'Partial'){
                        $statuscolor = '#ea8484';
                    }elseif($_SESSION['do_invoice']->status == 'Sent'){
                        $statuscolor = '#677cdf';
                    } else{  
                        $statuscolor = '#000000';
                    }
                  ?>
                <td class="right_text"><span class="text12"><b><?php echo _('Invoice Status'); ?>:</b>&nbsp; &nbsp; <span style="color: #ffffff; background-color: <?php echo $statuscolor; ?>; font-weight: bold; padding: 1px 20px;"><?php echo strtoupper($_SESSION['do_invoice']->status); ?></span></td>
            </tr></table>
            <div class="spacerblock_20"></div>
            <div class="invoice_view">
                <div class="invoice_view_head text14">
                    &nbsp; &nbsp;
                    <?php if($_SESSION['do_invoice']->status != 'Quote') { ?>
                        <?php if($_SESSION['do_invoice']->paypal_business_email != '' ){ ?>
                            <a href="/invoice_pay_paypal.php"><?php echo _('Pay With Paypal');?></a>&nbsp;<span class="sep3">|</span>&nbsp;
                        <?php } ?>
                        <?php if($_SESSION['do_invoice']->authnet_login != '' && $_SESSION['do_invoice']->authnet_merchant_id != ''){?>
                            <a href="/invoice_pay_auth.php"><?php echo _('Pay with Credit card'); ?></a>&nbsp;<span class="sep3">|</span>&nbsp;
                        <?php } ?>
                    <?php }else{
                        $e_approve_quote = new Event("do_invoice->eventChangeQuoteToInvoice") ;
                        $e_approve_quote->addParam('id',$_SESSION['do_invoice']->idinvoice);
                        $e_approve_quote->addParam('iduser',$_SESSION['do_invoice']->iduser);
                        $e_approve_quote->addParam('contact',$_SESSION['do_invoice']->idcontact);
                        $e_approve_quote->addParam('approve',"Yes");
                        $e_approve_quote->addParam("goto",$_SERVER['PHP_SELF']);
                        echo $e_approve_quote->getLink('Approve').'&nbsp;<span class="sep3">|</span>&nbsp;';
                    }
                    ?>
                    <a href="#" onclick="Popup=window.open('/invoice_print.php','Popup','toolbar=no,location=no,status=no,menubar=yes,scrollbars=yes,resizable=no, width=900,height=800,left=30,top=23'); return false;">
                    <?php echo _('Print');?></a>
					| &nbsp;<a href="/invoice_pdf.php" ><?php echo _('Download');?></a>
                </div>
                <div class="invoice_view_body">
                    <?php
                        if( $_SESSION['do_invoice']->inv_logo != ''){
                            echo '<img src="/files/'.$_SESSION['do_invoice']->inv_logo.'" alt="" /><br />';
                        }
                    ?>
                    <div class="spacerblock_20"></div>
                    <table class="layout_columns"><tr>
                        <td class="layout_col10">&nbsp;</td>
                        <td class="layout_col180">
                            <span class="text12"><span class="text_gray"><b><?php echo _('FROM'); ?>:</b></span><br />
                            <span class="text_darkgray"><?php echo $_SESSION['do_invoice']->Only1brFirstLineBlack($_SESSION['do_invoice']->getInvoiceSenderAddr($do_user_detail)); ?></span></span>
                        </td>
                        <td class="layout_col20">&nbsp;</td>
                        <td class="layout_col180">
                            <span class="text12"><span class="text_gray"><b><?php echo _('TO'); ?>:</b></span><br />
                            <span class="text_darkgray"><?php
                                echo $_SESSION['do_invoice']->Only1brFirstLineBlack($_SESSION['do_invoice']->invoice_address);
                            ?></span></span>
                        </td>
                        <td class="layout_col20">&nbsp;</td>
                        <td>
                            <table class="invoice_view_summary">
                                <tr>
                                    <td class="layout_col10">&nbsp;</td>
                                    <td class="layout_col100"><span class="text_darkgray"><b><?php echo _('Invoice'); ?> #</b></td>
                                    <td><?php echo $_SESSION['do_invoice']->num; ?></td>
                                </tr>
                                <tr>
                                    <td class="layout_col10">&nbsp;</td>
                                    <td class="layout_col100"><span class="text_darkgray"><b><?php echo _('Due Date'); ?></b></td>
                                    <td><?php echo $_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->due_date); ?></td>
                                </tr>
                                <tr>
                                    <td class="layout_col10">&nbsp;</td>
                                    <td class="layout_col100"><span class="text_darkgray"><b><?php echo _('Amount Due'); ?></b></td>
                                    <td><?php echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due); ?></td>
                                </tr>
                                <tr>
                                    <td class="layout_col10">&nbsp;</td>
                                    <td class="layout_col100"><span class="text_darkgray"><b><?php echo _('Terms'); ?></b></td>
                                    <td><?php echo $_SESSION['do_invoice']->invoice_term; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr></table>
                    <div class="spacerblock_20"></div>
                    <div class="invoice_description">
                      <?php echo nl2br($_SESSION['do_invoice']->description); ?>
                     </div>
                    <div class="spacerblock_20"></div>
                    <table class="invoice_view_list">
                        <tr>
                            <th class="layout_col10">&nbsp;</th>
                            <th class="layout_col180 left_text"><?php echo _('Item'); ?></th>
                            <th class="left_text">&nbsp;</th>
                            <th class="layout_col80 center_text"><?php echo _('Qty.'); ?></th>
                            <th class="layout_col100 center_text"><?php echo _('Price'); ?></th>
                            <?php
                                    $show_tax_amout = false ; 
                                    $line_tax_amt = $_SESSION['do_invoice']->getTotalLineTax($_SESSION['do_invoice']->idinvoice);
                                    if($line_tax_amt !== false){
                                    $show_tax_amout = true;
                              ?>
                            <th class="layout_col80 center_text"><?php echo _('Tax(%)'); ?></th>
                            <?php } ?>
                            <th class="layout_col100 center_text"><?php echo _('Total'); ?></th>
                        </tr>
                    <?php
                         $do_invoice_line = $_SESSION['do_invoice']->getChildinvoiceline();
                         $price = 0;
                         while($do_invoice_line->next()){ 
                    ?>
                        <tr>
                            <td class="layout_col10">&nbsp;</td>
                            <td class="left_text"><?php echo $do_invoice_line->item; ?></td>
                            <td class="left_text">&nbsp;</td>
                            <td class="center_text"><?php echo $do_invoice_line->qty; ?></td>
                            <td class="center_text"><?php echo $_SESSION['do_invoice']->viewAmount($do_invoice_line->price); ?></td>
                            <?php if($show_tax_amout === true ){ ?>
                            <td class="center_text"><?php echo $do_invoice_line->line_tax; ?></td>
                            <?php } ?>
                            <td class="center_text">
                             <?php 
                                  $line_sub_tot = floatval($do_invoice_line->qty*$do_invoice_line->price);
                                  //echo $line_sub_tot;
                                  echo $_SESSION['do_invoice']->viewAmount($line_sub_tot);
                            ?></td>
                        </tr>
                        <tr class="invoice_view_list_desc">
                            <td colspan="5">
                                &nbsp; &nbsp;<?php echo nl2br($do_invoice_line->description); ?>
                            </td>
                            <td colspan="2">&nbsp;</td>
                        </tr>
                    <?php } ?>
                    </table>
                    <div class="invoice_view_totals">
                        <table class="invoice_view_totals_table">
                            <tr class="invoice_view_totals_del">
                                <td><?php echo _('Subtotal'); ?></td>
                                <td class="right_text"><?php echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->sub_total); ?></td>
                            </tr>
                            <?php if($_SESSION['do_invoice']->discount != ''){ ?>
                            <tr class="invoice_view_totals_del">
                                <td><?php echo _('Discount'),' ',$_SESSION['do_invoice']->discount.'% :'; ?></td>
                                <td class="right_text"><?php echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_discounted_amt); ?></td>
                            </tr>
                            <?php } ?>
                             <?php 
                                if($_SESSION['do_invoice']->total_taxed_amount > 0 ){
                            ?>
                             <tr class="invoice_view_totals_del">
                                <td><?php echo _('Tax'); ?></td>
                                <td class="right_text"><?php echo $_SESSION['do_invoice']->viewAmount($line_tax_amt);//echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_taxed_amount); ?></td>
                            </tr>
                            <?php if($_SESSION['do_invoice']->tax > 0 ){ 
                                    $other_tax = $_SESSION['do_invoice']->total_taxed_amount - $line_tax_amt;
                            ?>
                              <tr class="invoice_view_totals_del">
                                  <td><?php echo _('Tax2'),'  ',$_SESSION['do_invoice']->tax ,'% :'; ?></td>
                                  <td class="right_text"><?php echo $_SESSION['do_invoice']->viewAmount($other_tax);//echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_taxed_amount); ?></td>
                              </tr>
                              <?php } ?>
                            <?php } ?>
                            <tr class="invoice_view_totals_del">
                                <td><?php echo _('Total'); ?></td>
                                <td class="right_text"><b><?php echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->net_total); ?></b></td>
                            </tr>
                            <tr class="invoice_view_totals_del">
                                <td><?php echo _('Amount Paid'); ?></td>
                                <td class="right_text"><?php echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amount); ?></td>
                            </tr>
                            <tr>
                                <td><div class="spacerblock_10"></div><span class="text18 text_darkgray text_bold"><?php echo _('Balance Due'); ?></span></td>
                                <td class="right_text"><div class="spacerblock_10"></div><span class="text18 text_bold"><?php echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due); ?></span></td>
                            </tr>
                        </table>
                    </div>
                    <br />
                    <div class="spacerblock_40"></div>
                    <span class="text12"><i><?php echo _('Note'); ?>:</i> &nbsp;<?php echo $_SESSION['do_invoice']->invoice_note; ?></span>
                </div>
                <div class="center_text text12">
                    <?php echo _('Invoice generated by ');?><a href="http://www.ofuz.com">Ofuz</a>
                </div>
                <div class="spacerblock_5"></div>
            </div>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
</body>
</html>
