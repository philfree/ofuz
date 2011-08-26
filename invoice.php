<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzPage
      * @license GNU Affero General Public License
      * @version 0.6
      * @date 2010-09-04
      * @since 0.4
      */

    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    $invoice_access = false;
    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    //$do_contact_task = new Contact();
	if (isset($_GET['idinvoice'])) {
		$idinvoice = (int)$_GET['idinvoice']; 
	} else {
		$idinvoice =  $_SESSION["eDetail_invoice"]->getparam("idinvoice");
	}
    if (!is_object($_SESSION['do_invoice'])) {
        $do_invoice = new Invoice();
        $do_invoice->sessionPersistent("do_invoice", "index.php", OFUZ_TTL);
    }
    $do_company = new Company();
    $_SESSION['do_invoice']->getId($idinvoice);
    //$_SESSION['do_invoice']->setBreadcrumb();
    if($_SESSION['do_invoice']->discount){
      $dis = $_SESSION['do_invoice']->discount;
    }else{$dis = "";}
    
    if($_SESSION['do_invoice']->isInvoiceOwner($idinvoice,$_SESSION['do_User']->iduser)){
        $invoice_access = true;
    }
    $pageTitle = 'Ofuz :: '._('Invoices').' # '.$_SESSION['do_invoice']->num;
    include_once('includes/header.inc.php');
    // Probably we can do this is some other way !!!
    $user_settings = $_SESSION['do_User']->getChildUserSettings();    
    if($user_settings->getNumRows()){
        while($user_settings->next()){
            if($user_settings->setting_name == 'invoice_logo' &&  $user_settings->setting_value != ''){
                $_SESSION['do_invoice']->inv_logo =  $user_settings->setting_value ;
            }
            if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                $currency =  explode("-",$user_settings->setting_value) ;
                $_SESSION['do_invoice']->currency_iso_code =  $currency[0];
                $_SESSION['do_invoice']->currency_sign = $currency[1];
                $_SESSION['do_invoice']->setCurrencyDisplay() ;
                $_SESSION['do_invoice']->getCurrencyPostion() ;
            }
            if($user_settings->setting_name == 'inv_date_format' &&  $user_settings->setting_value != ''){
                $_SESSION['do_invoice']->inv_dd_format = $user_settings->setting_value;
	    }
        }
    }
    //echo $_SESSION['extra_amt_idpaylog'];
?>
<script type="text/javascript">
//<![CDATA[
function addPayment(){
    	$("#add_payment").show("slow");
    }

function hidePayment(){
    $("#add_payment").hide("slow");
}

function showInvURL(){
  if ($("#inv_copyurl_box").is(":hidden")) {
            $("#inv_copyurl_box").hide(0);
            $("#inv_copyurl_box").slideDown("fast");
        } else {
            $("#inv_copyurl_box").slideUp("fast");
        }
  //
}

function showPastDue(id,idcontact){
    $.ajax({
        type: "GET",
<?php
$e_show_past_due = new Event("do_invoice_list->eventAddMultiPayment");
$e_show_past_due->setEventControler("ajax_evctl.php");
$e_show_past_due->setSecure(false);
?>
        url: "<?php echo $e_show_past_due->getUrl(); ?>",
        data: "id="+id+"&idcontact="+idcontact,
        success: function(html_data){ 
           //$("#past_due_txt").hide("fast");
           //$("#inv_msgs").hide("fast");
           $("#messages_unauthorized")[0].innerHTML = html_data;
        }
    });
}

function selectInvUrl(){
  document.getElementById('copy_link_txt').select();
}

$(document).ready(function() {
    	$("div[id^=templt]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
    });

//]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Invoices'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>

<?php 
 if(!$invoice_access){
    $msg = new Message(); 
    echo '<div class="messageshadow_unauthorized">';
    echo '<div class="messages_unauthorized">';
    echo $msg->getMessage("unauthorized_invoice_access");
    echo '</div></div><br /><br />';
    exit;
}
?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        <?php 
            if(0) {
           // if($_SESSION['do_invoice']->status != 'Quote'){
        ?>
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Payments Log'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
                <div class="center_elem center_text">
<?php
// <a href="#" onclick="addPayment();return false;"><img src="/images/receive_payment.png" width="131" height="24" alt="" /></a>
$button_payment = new DynamicButton();
echo $button_payment->CreateButton('#', _('receive payment'), '', 'addPayment();return false;', 'dyn_button_receive_payment');
?>
                </div>
                <div class="spacerblock_20"></div>
                <?php if($_SESSION['extra_amt'] != '' ){ 
                      $cancel_payment = new Event("do_invoice->eventCancelMultiPayment");
                      $cancel_payment->setLevel(10);
                      $cancel_link = $cancel_payment->getLink(_('Cancel'));
                      $msg = '<div style="margin-left:0px;">';
                      $msg .= '<div class="messages_unauthorized">';
                      $msg .= _('Add payment ').$_SESSION['do_invoice']->viewAmount($_SESSION['extra_amt'])._(' with the Note: ').'"'.$_SESSION['ref_num'].'"'._(' or with the amount you want.').'  &nbsp;'.$cancel_link;
                      $msg .='</div></div>';
                ?>
                <div id="add_payment" style="display:block;">
                <?php }else{ ?>
                  <div id="add_payment" style="display:none;">
                <?php }?>
                <?php
                    echo $msg;
                    $e_add_pay = new Event("do_invoice->eventAddPayment");
                    $e_add_pay->setLevel(20);
                    $e_add_pay->addParam("goto", $_SERVER['PHP_SELF']);
                    $e_add_pay->addParam('id',$_SESSION['do_invoice']->idinvoice);
                    echo $e_add_pay->getFormHeader();
                    echo $e_add_pay->getFormEvent();
                    if($_SESSION['extra_amt'] != ''){
                        echo _('Amount :').' <br /><input type="Text" name="payment_amt" id="payment_amt" value="'.$_SESSION['extra_amt'].'"><br />';
                    }else{
                      echo _('Amount :').' <br /><input type="Text" name="payment_amt" id="payment_amt" value="0.00"><br />';
                      echo _('Note :').' <br /><input type="Text" name="payment_ref_num" id="payment_ref_num" value=""><br />';
                    }
                    echo '<input type="submit" name="psubmit" value="'._('Add Payment').'">';
                    echo '</form>';
                ?>
                <br /><a href="#" onclick="hidePayment();return false;"><?php echo _('No, I\'ll add later');?></a><br /><br />
                </div>
                <?php
                      $do_inv_payment_log = new PaymentLog();
                      $do_inv_payment_log->getPaymentLog($_SESSION['do_invoice']->idinvoice);
                      while($do_inv_payment_log->next()){
                          $count = 0;
                          $e_del_log = new Event("PaymentLog->eventDeletePaymentLog");
                          $e_del_log->addParam("goto",$_SERVER['PHP_SELF']);
                          $e_del_log->addParam("id",$do_inv_payment_log->idpaymentlog);
                          $e_del_log->addParam("amt",$do_inv_payment_log->amount);

                          echo '<div id="templt', $count, '" class="co_worker_item co_worker_desc">'; 
                          echo '<div style="position: relative;">';  
                          echo '<span class="text12"><span class="text_lightblue">',$do_inv_payment_log->ref_num,'</span> <span class="sep1">|</span> <b>',$_SESSION['do_invoice']->viewAmount($do_inv_payment_log->amount),'</b><br />',
                          '<b>',(isset($_SESSION['do_invoice']->idcompany)?$do_company->getCompanyName($_SESSION['do_invoice']->idcompany):$do_contact->getContactName($_SESSION['do_invoice']->idcontact)),'</b><br />',
                          '<span class="text_darkgray">Received: ',date('F j, Y',$do_inv_payment_log->timestamp),'</span></span>'
                          ;
                          $img_del = '<img class="delete_icon_tag" border="0" width="14px" height="14px" src="/images/delete.gif">';
                          echo '<div width="15px" id="trashcan', $count, '" class="deletenote" style="right:0;">'.$e_del_log->getLink($img_del, ' title="'._('Remove').'"').'</div>';
                          echo '</div></div>';
                          echo '<div class="invoiceline12"></div>';
                          $count++;
                      }
                ?>
            </div>
        </div>
        <div class="left_menu_footer"></div><br /><br />
               <?php }?>
               
<?php 
$GLOBALS['page_name'] = "invoice";
include_once('plugin_block.php');?>


    </td><td class="layout_rcolumn">
        <div class="contentfull">
            <?php
                if($_SESSION['in_page_message'] != ''){
		    $msg = new Message();
                    
		    if($_SESSION['in_page_message'] == 'invoice_client_email_not_found' ) {
		      $msg->getMessage($_SESSION['in_page_message']);
	              $msg->displayMessage();
		      echo '<div style="margin-left:0px;">';
		      echo '<div class="messages_unauthorized">';
		      $e_set_email = new Event("Contact->eventSetEmailId");
		      $e_set_email->addParam('id',$_SESSION['do_invoice']->idinvoice);
		      $e_set_email->addParam('contact',$_SESSION['do_invoice']->idcontact);
		      $e_set_email->addParam("goto",$_SERVER['PHP_SELF']);
		      $e_set_email->addEventAction("do_invoice->eventSendInvoiceByEmail",105);
		      echo $e_set_email->getFormHeader();
		      echo $e_set_email->getFormEvent();
		      echo '<br />'._("Please enter the Email address : ");
		      echo '<input type="text" name="emailid" value="" >';
		      echo $e_set_email->getFormFooter("Submit");
		      echo '</div></div><br /><br />';
		    } else{
			$msg->setData(Array("contact_email" => $_SESSION['in_page_message_data']['contact_email']));
			$msg->getMessage($_SESSION['in_page_message']);
			$msg->displayMessage();
		    }
		    
                    $_SESSION['in_page_message'] = '';
		    $_SESSION['in_page_message_data']['contact_email'] = '';

                }
             ?>
             <div id="show_past_due">
                <?php 
                    if($_SESSION['show_past_due'] == 'Yes'){ 
                       // echo $_SESSION['do_invoice']->getInvoicesPastDue($_SESSION['do_invoice']->idcontact,$_SESSION['do_invoice']->idcompany);
                        $_SESSION['show_past_due'] = '';
                    }
                 ?>
             </div>
             <table class="layout_columns"><tr>
                 <td><span class="text32">
		    <b>
		      <?php
			if($_SESSION['do_invoice']->status == "Quote") {
			  echo _(strtoupper($_SESSION['do_invoice']->status)),' ',$_SESSION['do_invoice']->num; 
			} else {
			  echo _('Invoice'),' ',$_SESSION['do_invoice']->num; 
			}
		      ?>
		    :</b> 
		    <?php $company_name = $do_company->getCompanyName($_SESSION['do_invoice']->idcompany); echo (empty($company_name)?$do_contact->getContactName($_SESSION['do_invoice']->idcontact):$company_name); ?></span></td>
                 <td class="right_text"><div class="spacerblock_10"></div>
<?php
$button_add_invoice = new DynamicButton();
$do_inv_limit = new UserPlan();
if ($do_inv_limit->canUserAddInvoice()) {
    echo $button_add_invoice->CreateButton('/invoice_add.php', _('create new invoice'), '', '', 'dyn_button_add_new_invoice', 'width:150px;float:right;');
} else {
    echo $button_add_invoice->CreateButton('/upgrade_your_account.php?msg='.$_SESSION['do_User']->plan.'_i', _('create new invoice'), '', '', 'dyn_button_add_new_invoice', 'width:150px;float:right;');
}
?>
				</td>
             </tr></table>
             <div class="invoiceline16"></div>
             <table class="layout_columns"><tr>
                 <td>
                     <span class="text12"><b><?php echo _('Date Created'); ?>:</b> <?php echo $_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->datecreated); ?></span>
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
                 <div class="invoice_view_head">
                     &nbsp; &nbsp; <span class="text16 text_fuscia"><b><?php _('INVOICE:');?></b></span> &nbsp;
                     <span class="text12">
                         <!--Invoice Menu -->
                         <?php
                           if($_SESSION['do_invoice']->status == 'Quote'){
                               $e_conv_quote_to_inv = new Event("do_invoice->eventChangeQuoteToInvoice");
                               $e_conv_quote_to_inv->addParam('id',$_SESSION['do_invoice']->idinvoice);
                               $e_conv_quote_to_inv->addParam('contact',$_SESSION['do_invoice']->idcontact);
                               $e_conv_quote_to_inv->addParam("goto",$_SERVER['PHP_SELF']);
                               echo '<b>'.$e_conv_quote_to_inv->getLink(_('Convert to Invoice')).'</b>'.' | ';
                           }
                           $e_send_inv =  new Event("do_invoice->eventSendInvoiceByEmail");
                           $e_send_inv->addParam('id',$_SESSION['do_invoice']->idinvoice);
                           $e_send_inv->addParam('contact',$_SESSION['do_invoice']->idcontact);
                           $e_send_inv->addParam("goto",$_SERVER['PHP_SELF']);

                           if($_SESSION['do_invoice']->status == 'Quote')
                              echo $e_send_inv->getLink(_('Send Quote By Email')).' | ';
                           else
                              echo $e_send_inv->getLink(_('Send Invoice By Email')).' | ';
                         ?>
                         <a href="/invoice_edit.php?id=<?php echo $_SESSION['do_invoice']->idinvoice;?>"><?php echo _('Edit');?></a>&nbsp;|&nbsp;

                          <?php 
                                $e_del_inv = new Event("do_invoice->eventDeleteInvoice");
                                $e_del_inv->addParam('id',$_SESSION['do_invoice']->idinvoice);
                                $e_del_inv->addParam("goto","invoices.php");
                                $e_del_inv->addParam("status",$_SESSION['do_invoice']->status);
                                if($_SESSION['do_invoice']->status == 'Quote')
                                      echo $e_del_inv->getLink(_('Delete'),'OnClick="return confirm(\''._('Are you sure you want to Delete?\'').')"').' | ';
                                elseif($_SESSION['do_invoice']->status == 'New' || $_SESSION['do_invoice']->status == 'Sent')
                                      echo $e_del_inv->getLink(_('Cancel'),'OnClick="return confirm(\''._('Are you sure you want to Cancel?\'').')"').' | ';
                          ?>

                         <a href="#" onclick="Popup=window.open('/invoice_print.php','Popup','toolbar=no,location=no,status=no,menubar=yes,scrollbars=yes,resizable=no, width=900,height=800,left=30,top=23'); return false;">
                         <?php echo _('Print');?></a> 
                         &nbsp;|&nbsp;<a href="#" onclick="showInvURL();return false;"><?php echo _('Client Link');?></a>
                         <?php
                             $do_user_rel = new UserRelations();
                             $invoice_url = $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
                         ?>
                        | <a href="/invoice_pdf.php" ><?php echo _('Generate pdf');?></a>
                     </span>
                     <div id="inv_copyurl_box">
                         <input type="text" id="copy_link_txt" name="copy_link_txt" size="40" value="<?php echo $invoice_url;?>" onfocus="selectInvUrl()" onclick="selectInvUrl()">
                     </div>
                 </div>
                 <div class="invoice_view_body">
                     <?php
                         if( $_SESSION['do_invoice']->inv_logo == ''){
                             echo '<a href="/settings_invoice.php">'._('Upload Logo').'</a>';
                         }else{
                             echo '<img src="/files/'.$_SESSION['do_invoice']->inv_logo.'" alt="" /><br />';
                         }
                     ?>
                     <div class="spacerblock_20"></div>
                     <table class="layout_columns"><tr>
                         <td class="layout_col10">&nbsp;</td>
                         <td class="layout_col180">
                             <span class="text12"><span class="text_gray"><b><?php echo _('FROM'); ?>:</b></span><br />
                             <span class="text_darkgray"><?php 
							 echo $_SESSION['do_invoice']->Only1brFirstLineBlack($_SESSION['do_invoice']->getInvoiceSenderAddr($_SESSION['do_User'])); 
							 ?></span></span>
                         </td>
                         <td class="layout_col20">&nbsp;</td>
                         <td class="layout_col180">
                             <span class="text12"><span class="text_gray"><b><?php echo _('TO'); ?>:</b></span><br />
                             <span class="text_darkgray"><?php
                                 echo $_SESSION['do_invoice']->Only1brFirstLineBlack($_SESSION['do_invoice']->invoice_address),
                                     '<br /><br /><a href="/Contact/'.$_SESSION['do_invoice']->idcontact.'">'._('view customer details').'</a>';
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
                                  echo $_SESSION['do_invoice']->viewAmount($line_sub_tot); ?>
                              </td>
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
                        <table class="invoice_view_totals_table" >
                            <tr class="invoice_view_totals_del">
                                <td><?php echo _('Subtotal'); ?></td>
                                <td class="right_text"><?php echo $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->sub_total); ?></td>
                            </tr>
                            <?php if($_SESSION['do_invoice']->discount != ''){ 
                            ?>
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
                     <!--<div class="spacerblock_20"></div>
                     <div class="right_text text18 text_darkgray text_bold"><?php echo _('Amount Due'),' &nbsp; &nbsp;',$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due); ?></div>-->
                     <div class="spacerblock_40"></div>
                     <span class="text12"><i><?php echo _('Note'); ?>:</i> &nbsp;<?php echo $_SESSION['do_invoice']->invoice_note; ?></span>
                 </div>
             </div>
             <div class="spacerblock_40"></div>
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
