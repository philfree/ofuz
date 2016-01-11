<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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
    $do_contact_task = new Contact();
    $do_invoice = new Invoice();
    $do_invoice->sessionPersistent("InvoiceEditSave", "index.php", OFUZ_TTL);
    //$InvLine  = new InvoiceLine();
    //$InvLine->sessionPersistent("InvoiceLine", "index.php", OFUZ_TTL);
    if (isset($_GET['id'])) {
       $_SESSION['InvoiceEditSave']->getId((int)$_GET['id']);
    }
    $pageTitle = _('Edit Invoice # ').  $_SESSION['InvoiceEditSave']->num .' :: Ofuz';
    include_once('includes/header.inc.php');

	$inv_status = $_SESSION['InvoiceEditSave']->status;

    if($_SESSION['do_invoice']->isInvoiceOwner($_SESSION['InvoiceEditSave']->idinvoice,$_SESSION['do_User']->iduser)){
        $invoice_access = true;
    }
    $user_settings = $_SESSION['do_User']->getChildUserSettings();    
    if($user_settings->getNumRows()){
        while($user_settings->next()){
            if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
                $currency =  explode("-",$user_settings->setting_value) ;
                $_SESSION['InvoiceEditSave']->currency_iso_code = $currency[0];
                $_SESSION['InvoiceEditSave']->currency_sign = $currency[1];
                $_SESSION['InvoiceEditSave']->setCurrencyDisplay() ;
                $_SESSION['InvoiceEditSave']->getCurrencyPostion() ;
            }
        }
    }
    $RecInv = new RecurrentInvoice();
    $RecInv->sessionPersistent("RecurrentInvoice", "index.php", OFUZ_TTL);

?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
  reCalculateAmount();
});

function fnHighlight(area) {
	var div=$("#cw"+area);
        div.css("background-color", "#ffffdd");
}
function fnNoHighlight(area) {
	var div=$("#cw"+area);
        div.css("background-color", "#ffffff");
}
function setContactForCoworker(){
  $("#do_contact_sharing__eventShareContactsMultiple").submit();
}

function getSuggestion(){
    var text_input = $("#text_input").val();
    $.ajax({
        type: "GET",
<?php
$e_suggestion = new Event("Contact->eventAjaxGetSuggestion");
$e_suggestion->setEventControler("ajax_evctl.php");
$e_suggestion->setSecure(false);
?>
        url: "<?php echo $e_suggestion->getUrl(); ?>",
        data: "text="+text_input,
        success: function(suggestion){
            if(suggestion == 'No'){
                $("#suggestion_area").slideUp(100);
            }else{
                $("#suggestion_area").html(suggestion);
                if ($("#suggestion_area").is(":hidden")) {
                    $("#suggestion_area").slideDown(200);
                }
            }
        }
    });
}

function setSuggestion(text,value){
    $("#idcontact").val(value);
    $("#text_input").val(text);
    $("#suggestion_area").slideUp(100);
    $.ajax({
        type: "GET",
    <?php
      $e_address = new Event("Contact->eventAjaxGetInvoiceAddress");
      $e_address->setEventControler("ajax_evctl.php");
      $e_address->setSecure(false);
    ?>

     url: "<?php echo $e_address->getUrl(); ?>",
        data: "idcontact="+value,
        success: function(address){
           $("#invoice_address").val(address); 
        }
    });
}

function getItemSuggestion(text_id,select_id){
    var text_val = document.getElementById(text_id).value;
    $.ajax({
        type: "GET",
<?php
$e_suggestion_item = new Event("InvoiceEditSave->eventAjaxItemSuggestion");
$e_suggestion_item->setEventControler("ajax_evctl.php");
$e_suggestion_item->setSecure(false);
?>
        url: "<?php echo $e_suggestion_item->getUrl(); ?>",
        data: "text="+text_val,
        success: function(item_suggestion){
            if(item_suggestion == 'No'){
                $("#"+select_id).slideUp(100);
            }else{
                $("#"+select_id).html(item_suggestion);
                if ($("#"+select_id).is(":hidden")) {
                    $("#"+select_id).slideDown(200);
                }
            }
        }
    });
}

function setItemSuggestion(text,id_text,select_id){
    var w = document.getElementById(select_id).selectedIndex;
    var text_selected = document.getElementById(select_id).options[w].text;
    document.getElementById(id_text).value = text_selected;
    $("#"+select_id).slideUp(100);
}

function formatNumber(val,iso_code){
    switch(iso_code){
        case 'Euro' :
                  return formatNumberInternationalize(val,",","  ")+'<?php echo $_SESSION['InvoiceEditSave']->currency_sign; ?>';
                  break;
        case 'BRL' :
                  return '<?php echo $_SESSION['InvoiceEditSave']->currency_sign; ?>' +formatNumberInternationalize(val,",",".") ;
                  break;
        default:
                return '<?php echo $_SESSION['InvoiceEditSave']->currency_sign; ?>'+formatNumberInternationalize(val,".",",");
                break;   
    }
}

function formatNumberInternationalize(val,decimal_var,seperator_var){
    Num = "" + eval(val);
    dec = Num.indexOf(".");
    end = ((dec > -1) ? "" + Num.substring(dec,Num.length) : ".00");
    Num = "" + parseInt(Num);
    var temp1 = "";
    var temp2 = "";
    if (end.length == 2) end += "0";
    if (end.length == 1) end += "00";
    if (end == "") end += decimal_var+"00";
    var count = 0;
    Num = "" + eval(Num);
    var count = 0;
	for (var k = Num.length-1; k >= 0; k--) {
		var oneChar = Num.charAt(k);
			if (count == 3) {
				temp1 += seperator_var;
				temp1 += oneChar;
				count = 1;
				continue;
			}
			else {
				temp1 += oneChar;
				count ++;
			}
	}
	for (var k = temp1.length-1; k >= 0; k--) {
		var oneChar = temp1.charAt(k);
		temp2 += oneChar;
	}
	temp2 = temp2 + end.replace(/./,decimal_var);
	return temp2;
}

function formatNumberZero(iso_code){
    switch(iso_code){
        case "Euro" :
              return '0,00';
              break;
         case "BRL" :
              return '0,00';
              break;
        default : 
              return '0.00';
              break;
    }
}

function checkNumber(val){
 var str = val;
 var retval = true;
 for (var i = 0; i < str.length; i++) {
    var ch = str.substring(i, i + 1)
    if ((ch < "0" || "9" < ch) && ch != '.') {
        retval =  false;
        break;
     }
  }
  return retval;
}

/**
* computeTotal() has been depricated 
*/

function computeTotal(fldprice,fldqty,fldtot,fldtax,fldline_tot_without_tax){
  reCalculateAmount(); 
}



function reCalculateAmount(){
    var inv_line_num ;
    var line_amt;
    var line_qty;
    var line_discount;
    var line_tax;
    var line_taxed_amt;
    var line_sub_total;
    var line_total;
    var total_line_taxed_amount;
    var total_invoice_taxed_amt;
    var discount = document.getElementById('discount').value;
    var tax = document.getElementById('tax').value;    
    $("input[name^='mfields[invoiceline]']input[name$='[price]']").each(
        function(){
            inv_line_num = $(this).attr("name").match(/([0-9]+)/);
            line_amt = $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][price]']").val();
            line_qty = $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][qty]']").val();
            line_tax = $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][line_tax]']").val();
            
            line_sub_total = parseFloat(line_qty*line_amt);
            $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][line_sub_total]']").val(line_sub_total);
            if(discount.length != 0 && checkNumber(discount)){
              line_discount = parseFloat(line_sub_total * discount/100);
              line_total = parseFloat(line_sub_total - line_discount );
              $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][total]']").val(line_total); 
              $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][discounted_amount]']").val(line_discount); 
            }else{
                line_total = line_sub_total;
                $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][total]']").val(line_total);
                $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][discounted_amount]']").val('0.00'); 
            }
            if(line_tax.length != 0 && checkNumber(line_tax)){
                line_taxed_amt =  parseFloat(line_total * line_tax/100);
                line_total = parseFloat(line_total + line_taxed_amt);
                $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][total]']").val(line_total); 
                $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][taxed_amount]']").val(line_taxed_amt); 
            }else{
                $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][total]']").val(line_total); 
                $("input[name='mfields[invoiceline]["+inv_line_num[0]+"][taxed_amount]']").val('0.00'); 
            }
            
        }
    );

    // This will be trigrred when we choose to add another 
    $("input[name^='mfields[invoiceline_new]']input[name$='[price]']").each(
        function(){
            inv_line_num = $(this).attr("name").match(/([0-9]+)/);
            line_amt = $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][price]']").val();
            line_qty = $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][qty]']").val();
            line_tax = $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][line_tax]']").val();
            line_sub_total = parseFloat(line_qty*line_amt);
            $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][line_sub_total]']").val(line_sub_total);
            if(discount.length != 0 && checkNumber(discount)){
               line_discount = parseFloat(line_sub_total * discount/100);
               line_total = parseFloat(line_sub_total - line_discount );
               $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][total]']").val(line_total); 
               $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][discounted_amount]']").val(line_discount); 
            }else{
                line_total = line_sub_total;
                $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][total]']").val(line_total);
                $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][discounted_amount]']").val('0.00'); 
            }
            if(line_tax.length != 0 && checkNumber(line_tax)){
                line_taxed_amt =  parseFloat(line_total * line_tax/100);
                line_total = parseFloat(line_total + line_taxed_amt);
                $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][total]']").val(line_total); 
                $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][taxed_amount]']").val(line_taxed_amt); 
            }else{
                $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][total]']").val(line_total); 
                $("input[name='mfields[invoiceline_new]["+inv_line_num[0]+"][taxed_amount]']").val('0.00'); 
            }
            
        }
    );


     var total_invoice_amount_discounted = 0;
     $("input[name$=[discounted_amount]]").each(function(){
              total_invoice_amount_discounted += parseFloat($(this).val());
     });
      
    var sub_total = 0;
    $("input[name$=[line_sub_total]]").each(function(){
        sub_total += parseFloat($(this).val());
    });
    
    var grandtotal = 0;
    $("input[name$=[total]]").each(function(){
        grandtotal += parseFloat($(this).val());
    });       
    var total_line_taxed_amount = 0;
    $("input[name$=[taxed_amount]]").each(function(){
        total_line_taxed_amount += parseFloat($(this).val());
    }); 

    total_invoice_taxed_amt = total_line_taxed_amount;
    if(tax.length != 0 && checkNumber(tax)){
            var tax_amt = 0;  
            var 
            tax_amt = parseFloat(grandtotal * tax/100 );
            //total_invoice_taxed_amt = parseFloat(total_invoice_taxed_amt + tax_amt);
            grandtotal = grandtotal + tax_amt;
            document.getElementById('txt_tax_other').innerHTML =formatNumber(tax_amt.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
     }
    document.getElementById('txt_discount').innerHTML =formatNumber(total_invoice_amount_discounted.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');

    document.getElementById('txt_tax').innerHTML =formatNumber(total_invoice_taxed_amt.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');

    document.getElementById('sub_total').innerHTML = formatNumber(sub_total.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
    document.getElementById('tot_balance').innerHTML = formatNumber(grandtotal.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');

}

function setRecurent(){
  if($("#setRec").attr("checked")){
    $("#rec_section").show("slow");  
  }else{
    $("#rec_section").hide("slow");
  }
  
}

function unsetRecurent(){
  $("#rec_section").hide("slow");
}

function fnAddAnother(){
    $("#addOneMore").html("Add another");
}

function showTax(){
$("#tax_entry").slideToggle("slow");
}

$(document).ready(function() {
     $("input[name='contact']").attr("autocomplete", "off");
     $("input[name^='mfield']").attr("autocomplete", "off");
});
//]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php
$thistab = _('Invoices'); include_once('includes/ofuz_navtabs.php');
$do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs();

if(!$invoice_access){
    $msg = new Message(); 
    echo '<div class="messageshadow_unauthorized">';
    echo '<div class="messages_unauthorized">';
    echo $msg->getMessage("unauthorized_invoice_access");
    echo '</div></div><br /><br />';
    exit;
}

// Main Invoice Form
$f_invoiceForm = new Event("InvoiceEditSave->eventValuesFromForm");
$f_invoiceForm->setLevel(1999);
//$f_invoiceForm->addEventAction("InvoiceEditSave->checkEmptyDueDate", 1999);
$f_invoiceForm->addEventAction("InvoiceEditSave->eventUpdateInvoice", 2000);
$f_invoiceForm->addEventAction("InvoiceLine->eventSaveInvoiceLine", 2200);
$f_invoiceForm->addEventAction("InvoiceEditSave->eventCheckContactChanged", 2201);     
$f_invoiceForm->addEventAction("InvoiceEditSave->eventSetInvoiceCalculation", 2210);
$f_invoiceForm->addParam("id",$_SESSION['InvoiceEditSave']->idinvoice);
$f_invoiceForm->addParam("contact",$_SESSION['InvoiceEditSave']->idcontact);
$f_invoiceForm->addParam("goto", "invoice.php");
echo $f_invoiceForm->getFormHeader();
echo $f_invoiceForm->getFormEvent();
$discount = $_SESSION['InvoiceEditSave']->discount;
$tax = $_SESSION['InvoiceEditSave']->tax;
$idcontact = $_SESSION['InvoiceEditSave']->idcontact;
$due_date =  $_SESSION['InvoiceEditSave']->due_date;
$contact_name_comp = $do_contact->getContact_Company_ForInvoice($idcontact,$_SESSION['InvoiceEditSave']->idcompany);
$_SESSION['InvoiceEditSave']->setFields("invoice_edit"); 
$_SESSION['InvoiceEditSave']->setApplyRegistry(true, "Form");
?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Recurrent Invoice?'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
            <?php
                // Recurrent Part
                $idrecurrentinvoice = $_SESSION['RecurrentInvoice']->checkIfInvoiceIsInRecurrent((int)$_GET['id']) ;
                if($idrecurrentinvoice){
                    $_SESSION['RecurrentInvoice']->getId($idrecurrentinvoice);
                    $checked = 'CHECKED';
                    $frequency = $_SESSION['RecurrentInvoice']->recurrencetype;
                    $recurrence = $_SESSION['RecurrentInvoice']->recurrence;
                    $div_style = 'style="display:block;"';
                }else{
                    $checked = '';
                    $frequency = '';
                    $recurrence = 1;
                    $div_style = 'style="display:none;"';
                }
                echo '<input type="checkbox" name="setRec" id="setRec" value= "Yes" ',$checked,' onclick="setRecurent();" />',
                     '&nbsp;&nbsp;','<span class="text12"><b>',_('Set this invoice as recurrent'),'</b></span><br /><br />',
                     '<div id="rec_section" class="text12" ',$div_style,'>',
                     _('Repeat this invoice every: <br />'),'<div class="spacerblock_5"></div>',
                     '<input type="text" name="recurrent" id="recurrent" size="2" value="',$recurrence,'">&nbsp;',
                     $_SESSION['RecurrentInvoice']->generateRecurrentFrequencyCombo($frequency),'<br /><br /><br />',
                     _('(like every 2 Months or every 3 Days)'),'</div><br />';
               // Recurrent Part ends here
            ?>                
            </div>
        </div>
        <div class="left_menu_footer"></div><br /><br />
    </td><td class="layout_rcolumn">
        <div class="contentfull">
            <div class="text32"><b><?php echo _('Edit Invoice'),' ',$_SESSION['do_invoice']->num; ?>:</b> <?php $company_name = $do_company->getCompanyName($_SESSION['do_invoice']->idcompany); echo (empty($company_name)?$do_contact->getContactName($_SESSION['do_invoice']->idcontact):$company_name); ?></div>
            <div class="spacerblock_20"></div>
            <div class="invoice_edit">
                <?php
                    if( $_SESSION['do_invoice']->inv_logo == ''){
                        echo '<a href="/settings_invoice.php">Upload Logo</a>';
                    }else{
                        echo '<img src="/files/'.$_SESSION['do_invoice']->inv_logo.'" alt="" /><br />';
                    }
                ?>
                <div class="spacerblock_20"></div>
                <table class="layout_columns">
					<tr>
						<td colspan="3">
						<?php if($inv_status != "Quote") { ?>
						Status:<br /><?php echo $_SESSION['InvoiceEditSave']->status; ?><br /><br />
						<?php } ?>
						</td>
					</tr>
					<tr>
                    <td>
                        <?php echo _('Contact/Company'); ?>:<br /><input type="text" size="35" name="contact" id="text_input" onkeyup="getSuggestion()" value="<?php echo $contact_name_comp; ?>" />
                        <input type="hidden" name="fields[idcontact]" id="idcontact" value="<?php echo $idcontact; ?>" />
                        <div class="suggestion_area_cont"><select id="suggestion_area" size="5" onchange="setSuggestion(this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)" style="display:none;"></select></div><br />
                        <div id="cont_area"><?php echo _('Address'); ?>:<br /><?php echo $_SESSION['InvoiceEditSave']->invoice_address; ?></div> 
                    </td>
                    <td class="layout_col20">&nbsp;</td>
                    <td>
                        <table class="invoice_edit_summary">
                            <tr>
                                <td class="layout_col120"><?php echo _('Date Created'); ?>:</td><td><?php echo $_SESSION['InvoiceEditSave']->datecreated; ?></td>
                            </tr>
                            <tr>
                                <td class="layout_col120"><?php echo _('Due Date'); ?>:</td><td><?php echo $_SESSION['InvoiceEditSave']->due_date; ?></td>
                            </tr>
                            <tr>
                                <td class="layout_col120"><?php echo _('Terms'); ?>:</td><td><?php echo $_SESSION['InvoiceEditSave']->invoice_term; ?></td>
                            </tr>
                        </table>
                    </td>
                </tr></table>
                <div class="spacerblock_20"></div>
                <?php echo _('Description:');?><br /><?php echo $_SESSION['InvoiceEditSave']->description; ?><br /><br />
                <?php echo _('Discount (%) :');?><br /><input type="text" name="fields[discount]" id="discount" size="4" onchange="reCalculateAmount();" value="<?php echo $discount; ?>" />
                <?php 
                      if($_SESSION['InvoiceEditSave']->tax != ''){
                          $tax_style = 'style="display:block;"';
                      }else{ $tax_style = 'style="display:none;"'; }
                ?>
                 <br /><br /><a href="#" onclick="showTax(); return false;"><?php echo _('Add Tax (%)');?></a> <br />
                <div id = "tax_entry" <?php echo $tax_style;?> >
                <input type="text" name="fields[tax]" id="tax" size="4" onchange="reCalculateAmount();" value="<?php echo $tax; ?>" />
                </div>
                <div class="spacerblock_20"></div>
                <table id="invoice_list" class="invoice_edit_list">
                    <tr>
                        <th class="layout_col180 left_text">&nbsp; &nbsp; <?php echo _('Item'); ?></th>
                        <th class="left_text">&nbsp;</th>
                        <th class="layout_col80 center_text"><?php echo _('Qty.'); ?></th>
                        <th class="layout_col100 center_text"><?php echo _('Price'); ?></th>
                        <th class="layout_col80 left_text"><?php echo _('Tax (%)'); ?></th>
                        <th class="layout_col100 center_text"><?php echo _('Total'); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php
                        $InvLine = $_SESSION['InvoiceEditSave']->getChildinvoiceline();
                        echo $InvLine->formInvoiceMultiEntry();
                    ?>
                <div class="invoice_edit_totals">
                    <table class="invoice_edit_totals_table" onclick="reCalculateAmount();">
                        <tr>
                            <td><?php echo _('Subtotal'); ?></td>
                            <td id="sub_total" class="right_text"><?php echo $_SESSION['InvoiceEditSave']->setNumberFormatZero(); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo _('Discount'); ?></td>
                            <td id="txt_discount" class="right_text"><?php echo $_SESSION['InvoiceEditSave']->setNumberFormatZero(); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo _('Tax'); ?></td>
                            <td id="txt_tax" class="right_text"><?php echo $_SESSION['InvoiceEditSave']->setNumberFormatZero(); ?></td>
                        </tr>
                         <tr>
                            <td><?php echo _('Tax 2'); ?></td>
                            <td id="txt_tax_other" class="right_text"><?php echo $_SESSION['InvoiceEditSave']->setNumberFormatZero(); ?></td>
                        </tr>
                        <tr>
                            <td style="background-color: #ffffdd;"><?php echo _('Balance'); ?></td>
                            <td id="tot_balance" class="right_text" style="background-color: #ffffdd;"><?php echo $_SESSION['InvoiceEditSave']->setNumberFormatZero(); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="spacerblock_20"></div>
                Note:<br /><?php echo $_SESSION['InvoiceEditSave']->invoice_note; ?>
            </div>
            <div class="spacerblock_20"></div>
            <div class="section20" style="text-align: center"><input type="submit" value="Save" /></div>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
    </form>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
