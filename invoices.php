<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Page to list all the invoices
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzPage
     * @license GNU Affero General Public License
     * @version 0.6
     * @date 2010-09-06
     * @since 0.4
     */

    include_once('config.php');
    $pageTitle = _('Invoices').' :: Ofuz ';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
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
            if($user_settings->setting_name == 'inv_date_format' &&  $user_settings->setting_value != ''){
                $_SESSION['do_invoice_list']->inv_dd_format = $user_settings->setting_value;
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

    $.ajax({
        type: "GET",
	<?php
	$e_hide = new Event("do_invoice_list->eventHideTotal");
	$e_hide->setEventControler("ajax_evctl.php");
	$e_hide->setSecure(false);
	?>
        url: "<?php echo $e_hide->getUrl(); ?>",
        success: function(hide_inv){ 
        }
    });
}

function showTotals(){
    $(".layout_lcolumn").show(0);
    $("#totals_txt").hide(0);
    $.ajax({
        type: "GET",
	<?php
	$e_show = new Event("do_invoice_list->eventShowTotal");
	$e_show->setEventControler("ajax_evctl.php");
	$e_show->setSecure(false);
	?>
        url: "<?php echo $e_show->getUrl(); ?>",
        success: function(hide_inv){ 
        }
    });
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
<?php $thistab = _('Invoices'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
	<?php
		if($_SESSION["hide_total"]) {
			$hide_total = $_SESSION["hide_total"];
		} else {
			$hide_total = 'display:block';
		}
	?>
    <table class="layout_columns"><tr><td class="layout_lcolumn" style="<?php echo $hide_total; ?>">
        <?php include_once('plugin_block.php');?>
        <br /><br />
        <div class="pad020"><a href="#" onclick="hideTotals();return false;"><?php echo _('( hide totals )');?></a></div>
    </td><td class="layout_rcolumn">
        <div id="email_msg" style="display:none">
            <div style="margin-left:0px;" class="messages_unauthorized marginright">
                <?php echo _('Sending Emails......');?>
            </div>
        </div>
        <div class="banner40 pad020 text34">         
<?php
    echo _('Invoices');
    $do_inv_limit = new UserPlan();
    $button_add_invoice = new DynamicButton();
    if ($do_inv_limit->canUserAddInvoice()) {
        echo '<div class="right_20_top_0">',$button_add_invoice->CreateButton('/invoice_add.php', _('create new invoice'), '', '', 'dyn_button_add_new_invoice', 'width:150px;'),'</div>';
    } else {
        echo '<div class="right_20_top_0">',$button_add_invoice->CreateButton('/upgrade_your_account.php?msg='.$_SESSION['do_User']->plan.'_i', _('create new invoice'), '', '', 'dyn_button_add_new_invoice', 'width:150px;'),'</div>';
    }
?>
        </div>

        <div id="inv_msgs">
		    <?php
                   if($_SESSION['inv_past_due_hide'] == 'Yes'){
                        $do_invoice_check = new Invoice();
                        $msg = new Message(); 
                              if($do_invoice_check->hasInvoices()){
                                      if ($msg->getMessageFromContext("invoice list")) {
                                              echo $msg->displayMessage();
                                      }
                              } else { 
                                      $msg->getMessage("invoice first time");
                                      echo $msg->displayMessage();
                              }
                              $do_invoice_check->free();
                    }
            ?>
        </div>
        <div id="show_past_due"></div>
              <?php
                      if($_SESSION['inv_past_due_hide'] != 'Yes' && $_SESSION['do_invoice_list']->from_invoice_page === true){
                          //echo '<br /><br />';
                          echo $_SESSION['do_invoice_list']->getInvoicesPastDue();
                      }
                      if($do_payment_inv->getExtraAmoutNotPaid() !== false && $_SESSION['extra_amt'] == ''){
                           echo '<br />';
                           echo '<div style="margin-left:0px;">';
                           echo '<div class="messages_unauthorized marginright">';
                           echo _('You have payments which are not yet applied to invoices.').' <a href="#" onclick="showExtraAmt();return false;">'._('Click here.').'</a>';
                           echo '<div id="extra_amt" style="display:none;">';
                           $do_payment_inv->displayExtraAmtNotPaid();
                           echo '</div>';
                           echo '</div></div><br />'; 
                      }
                    
               ?>

        <div class="contentfull">
           <div class="banner60_mid text14">
              <?php
                    if($_SESSION['do_invoice_list']->from_invoice_page === true){
              ?>
              <!--Invoice Menu -->
               <span class="fuscia_text text16"><?php echo _('Showing:'); ?></span> &nbsp; &nbsp;
               <?php
                    $e_filter_inv  = new Event("do_invoice_list->eventFilterInvoice");
                    $e_filter_inv->setLevel(10);
                    
                      
                    // All
                    //$e_filter_inv->addParam("status","None");
                    //$e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                    //echo $e_filter_inv->getLink("All");
                    //echo '&nbsp; &nbsp; ';
                    // Quote
                    $e_filter_inv->addParam("status","Quote");
                    $e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                    echo $e_filter_inv->getLink(_('Quote'));
                    echo '&nbsp; &nbsp; ';
                    // New
                    $e_filter_inv->addParam("status","New");
                    $e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                    echo $e_filter_inv->getLink(_('New'));
                    echo '&nbsp; &nbsp; ';
                    
                    $e_filter_inv->addParam("status","Sent");
                    $e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                    echo $e_filter_inv->getLink(_('Sent'));
                    echo '&nbsp; &nbsp; ';
                    $e_filter_inv->addParam("status","Partial");
                    $e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                    echo $e_filter_inv->getLink(_('Partial'));
                    echo '&nbsp; &nbsp; ';
                    $e_filter_inv->addParam("status","Paid");
                    $e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                    echo $e_filter_inv->getLink(_('Paid'));
                    if($_SESSION['do_invoice_list']->filter_inv_status_val !=''){
                        echo '&nbsp; &nbsp; ';
                        $e_filter_inv->addParam("status","None");
                        $e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                        echo $e_filter_inv->getLink(_('All'));
                        echo '&nbsp;&nbsp;';
                    } 
                  echo '&nbsp; &nbsp; ';
                  $e_filter_inv->addParam("type","date");
                  $e_filter_inv->addParam("goto", $_PHP['SELF']); 
                  
                  echo '<form id="setFilterInvMonth" name="setFilterInvMonth" method="post" action="/eventcontroler.php" style="display:inline;">';
                  echo $e_filter_inv->getFormEvent();
                  echo $_SESSION['do_invoice_list']->getYearDropDownFilter();
                  echo '&nbsp; &nbsp; ';
                  echo $_SESSION['do_invoice_list']->getMonthDropDownFilter();
                  echo '</form>';
               ?>
               <?php
                   }else{
                       $e_filter_inv  = new Event("do_invoice_list->eventUnsetFilterInvoice");
                       $e_filter_inv->setLevel(10);
                       $e_filter_inv->addParam("goto",$_SERVER['PHP_SELF']);
                       echo $e_filter_inv->getLink(_("View all invoices"));
                       echo '&nbsp; &nbsp; ';
                  }
               ?>
                <div class="right_20_top_0">

                <?php
					if($_SESSION["show_total"]) {
						$show_total = $_SESSION["show_total"];
					} else {
						$show_total = 'display:none;';
					}
                    //if($_SESSION['inv_past_due_hide'] == 'Yes'){
                        echo '<span id="totals_txt" style="'.$show_total.'"><a href="#" onclick="showTotals();return false;">'._('show totals').'</a></span>';   
                    //}
                ?>
                &nbsp; &nbsp;
                <?php
                    if($_SESSION['inv_past_due_hide'] == 'Yes'){
                        echo '<span id="past_due_txt"><a href="#" onclick="showPastDue();return false;">'._('show past due').'</a></span>';   
                    }
                ?>
                </div>
            </div>
            <table class="invoice_list">
                <tr>
                    <th class="invoice_list_12pct center_text"><?php echo _('Invoice #');?></th>
                    <th class="invoice_list_40pct left_text"><?php echo _('Client');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('Due Date');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('Total Paid');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('Total Due');?></th>
                    <th class="invoice_list_12pct center_text"><?php echo _('Status');?></th>
                </tr>
                <?php
				  $do_rec_inv = new RecurrentInvoice();
                                  //echo $_SESSION['do_invoice_list']->getSqlQuery();
				  if(!$_SESSION['do_invoice_list']->filter_set){
							$_SESSION['do_invoice_list']->getAllInvoice();
				   }else{
							$_SESSION['do_invoice_list']->query($_SESSION['do_invoice_list']->getSqlQuery());
				   }               
                  if($_SESSION['do_invoice_list']->getNumRows() > 0 ){
                    $do_contact = new Contact();
                    while($_SESSION['do_invoice_list']->next()){
                      $currentpage = $_SERVER['PHP_SELF'];
                      $e_detail = new Event("mydb.gotoPage");
                      $e_detail->addParam("goto", "invoice.php");
                      $e_detail->addParam("idinvoice",$_SESSION['do_invoice_list']->idinvoice);
                      $e_detail->addParam("tablename", "invoice");
                      $e_detail->requestSave("eDetail_invoice", $currentpage);
                      //echo $_SESSION['do_invoice']->idcompany;
                      if($_SESSION['do_invoice_list']->discount){
                        $dis = $_SESSION['do_invoice_list']->discount;
                      }else{$dis = "";}
                     // $invoice_cal_data = $_SESSION['do_invoice']->getInvoiceCalculations($_SESSION['do_invoice']->idinvoice,$_SESSION['do_invoice']->amount,$dis);

                      if($_SESSION['do_invoice_list']->status == 'Paid'){
                          $statuscolor = "#009900";
                      }elseif($_SESSION['do_invoice_list']->status == 'Partial'){
                          $statuscolor = "#EA8484";
                      }elseif($_SESSION['do_invoice_list']->status == 'Sent'){
                          $statuscolor = "#677CDF";
                      }else{  
                          $statuscolor ="#000000";
                      }
					  $is_recurrent = $do_rec_inv->checkIfInvoiceIsInRecurrent($_SESSION['do_invoice_list']->idinvoice);
					  if($is_recurrent) {
					  	$rec_inv_icon = '<img src="images/recurrent_invoice.png" title="Recurrent Invoice" alt="Recurrent Invoice" class="invoice_list_recurrent" />';
					  } else {
					  	$rec_inv_icon = '';
					  }
                ?>
                <tr class="invoice_list_row" onclick="window.location.href='<?php echo "/Invoice/".$_SESSION['do_invoice_list']->idinvoice ?>'" title="<?php echo str_replace('"', "'", $_SESSION['do_invoice_list']->description); ?>">
                    <td class="center_text"><?php echo $_SESSION['do_invoice_list']->num." ".$rec_inv_icon; ?></td>
                    <td class="left_text"><?php echo $do_contact->getContact_Company_ForInvoice($_SESSION['do_invoice_list']->idcontact,$_SESSION['do_invoice_list']->idcompany); ?></td>
                    <td class="center_text">
		      <?php
			$inv_formatted_date = $_SESSION['do_invoice_list']->getInvFormattedDate($_SESSION['do_invoice_list']->due_date);
			echo $inv_formatted_date;
		      ?>
                    </td>
                    <td class="center_text"><?php echo $_SESSION['do_invoice_list']->viewAmount($_SESSION['do_invoice_list']->amount); ?></td>
                    <td class="center_text"><?php echo $_SESSION['do_invoice_list']->viewAmount($_SESSION['do_invoice_list']->net_total); ?></td>
                    <td class="center_text" style="color: <?php echo $statuscolor; ?>"><?php echo $_SESSION['do_invoice_list']->status; ?></td>
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
