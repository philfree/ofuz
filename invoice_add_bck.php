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
    $do_contact->sessionPersistent("Contact", "index.php", OFUZ_TTL);
    $do_invoice = new Invoice();
    $do_invoice->sessionPersistent("InvoiceEditSave", "index.php", OFUZ_TTL);
    $InvLine  = new InvoiceLine();
    $InvLine->sessionPersistent("InvoiceLine", "index.php", OFUZ_TTL);
    $RecInv = new RecurrentInvoice();
    $RecInv->sessionPersistent("RecurrentInvoice", "index.php", OFUZ_TTL);
    
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

?>
<script type="text/javascript">
//<![CDATA[
 dojo.require("dijit.form.Textarea");
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
                  //return euroCentenRenderer(val)+ '<?php echo $_SESSION['InvoiceEditSave']->currency_sign; ?>';
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
function computeTotal(fldprice,fldqty,fldtot){
 var price ;
 var qty;
 var tot;
 price = document.getElementById(fldprice).value;
 qty = document.getElementById(fldqty).value;
 if(price.length != 0 && qty.length != 0 && price!="" && qty != ""){
   if(!checkNumber(price) || !checkNumber(qty) ){
      document.getElementById(fldprice).value =formatNumberZero('<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
      document.getElementById(fldqty).value = formatNumberZero('<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
      document.getElementById(fldtot).value = formatNumberZero('<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
      document.getElementById('txt_discount').innerHTML = formatNumberZero('<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
   }else{
        price = parseFloat(price);
        qty = parseFloat(qty);
        tot = (price * qty).toFixed(2);
        document.getElementById(fldprice).value = price.toFixed(2);
        document.getElementById(fldqty).value = qty.toFixed(2);
        document.getElementById(fldtot).value = tot;

        var grandtotal = 0;
        $("input[name$=[total]]").each(function(){
            grandtotal += parseFloat($(this).val());
        });
        var discount = document.getElementById('discount').value;
        var tax = document.getElementById('tax').value;
        var grandtotal_with_tax = 0;
        grandtotal_with_tax = grandtotal;
        
        if(discount.length != 0 && checkNumber(discount)){
            var discount_amt = 0 ;
            var tot_with_discount = 0;
            discount_amt = parseFloat(grandtotal_with_tax * discount/100 );
            document.getElementById('txt_discount').innerHTML =formatNumber(discount_amt.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
            tot_with_discount = grandtotal_with_tax - discount_amt;
            document.getElementById('tot_balance').innerHTML = formatNumber(tot_with_discount.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
            

        }else{
           var tot_with_discount = 0;
           tot_with_discount = grandtotal_with_tax;
            document.getElementById('tot_balance').innerHTML = formatNumber(tot_with_discount.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
        }

        if(tax.length != 0 && checkNumber(tax)){
            var tax_amt = 0;  
            tax_amt = parseFloat(tot_with_discount * tax/100 );
            grandtotal_with_tax = tot_with_discount + tax_amt;
            document.getElementById('txt_tax').innerHTML =formatNumber(tax_amt.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
            
        }


        document.getElementById('sub_total').innerHTML = formatNumber(grandtotal.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
         document.getElementById('tot_balance').innerHTML = formatNumber(grandtotal_with_tax.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
    }
 }else{ 
  document.getElementById(fldtot).value = formatNumberZero('<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
  document.getElementById('txt_discount').innerHTML =formatNumberZero('<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
 }
 
}

function reCalculateAmount(){
        var grandtotal = 0;
        $("input[name$=[total]]").each(function(){
            grandtotal += parseFloat($(this).val());
        });
        var discount = document.getElementById('discount').value;
        var tax = document.getElementById('tax').value;
        var grandtotal_with_tax = 0;
        grandtotal_with_tax = grandtotal;
         

        if(discount.length != 0 && checkNumber(discount) && !isNaN(grandtotal)){
            var discount_amt = 0 ;
            var tot_with_discount = 0;
            discount_amt = parseFloat(grandtotal_with_tax * discount/100 );
            document.getElementById('txt_discount').innerHTML = formatNumber(discount_amt.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');    
            tot_with_discount = grandtotal_with_tax - discount_amt;
            document.getElementById('tot_balance').innerHTML = formatNumber(tot_with_discount.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');    
            document.getElementById('sub_total').innerHTML = formatNumber(grandtotal.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
        }else if(!isNaN(grandtotal)){
            var discount_amt = 0 ;
            var tot_with_discount = 0;
            tot_with_discount = grandtotal_with_tax;
            document.getElementById('txt_discount').innerHTML = formatNumberZero('<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');   
            document.getElementById('tot_balance').innerHTML = formatNumber(tot_with_discount.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
            document.getElementById('sub_total').innerHTML = formatNumber(grandtotal.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
        }

        if(tax.length != 0 && checkNumber(tax)){
            var tax_amt = 0;  
            tax_amt = parseFloat(tot_with_discount * tax/100 );
            grandtotal_with_tax = tot_with_discount + tax_amt;
            document.getElementById('txt_tax').innerHTML =formatNumber(tax_amt.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
             document.getElementById('tot_balance').innerHTML = formatNumber(grandtotal_with_tax.toFixed(2),'<?php echo $_SESSION['InvoiceEditSave']->currency_iso_code ; ?>');
        }
}

function setRecurent(){
  if($("#setRec").attr("checked")){
    $("#rec_section").show("slow");  
  }else{
    $("#rec_section").hide("slow");
  }
  
}


function showTax(){
$("#tax_entry").slideToggle("slow");
}


function unsetRecurent(){
  $("#rec_section").hide("slow");
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
$thistab = 'Invoices'; include_once('includes/ofuz_navtabs.php');
$do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs();

// Main Invoice Form
$f_invoiceForm = new Event("InvoiceEditSave->addInvoice");
$f_invoiceForm->setLevel(2000);
$f_invoiceForm->addEventAction("InvoiceLine->eventSaveInvoiceline", 2200);
$f_invoiceForm->addEventAction("InvoiceEditSave->eventSetInvoiceCalculation", 2210);
$f_invoiceForm->addParam("goto", "invoice.php");
echo $f_invoiceForm->getFormHeader();
echo $f_invoiceForm->getFormEvent();
$_SESSION['InvoiceEditSave']->setFields("invoice_add"); 
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
                echo '<input type="checkbox" name="setRec" id="setRec" value= "Yes" onclick="setRecurent();" />',
                     '&nbsp;&nbsp;','<span class="text12"><b>',_('Set this invoice as recurrent'),'</b></span><br /><br />',
                     '<div id="rec_section" class="text12" style="display:none;">',
                     _('Repeat this invoice every: <br />'),'<div class="spacerblock_5"></div>',
                     '<input type="text" name="recurrent" id="recurrent" size="2" value="1">&nbsp;',
                     $_SESSION['RecurrentInvoice']->generateRecurrentFrequencyCombo(),'<br /><br /><br />',
                     _('(like every 2 Months or every 3 Days)'),'</div><br />';
               // Recurrent Part ends here
            ?>                
            </div>
        </div>
        <div class="left_menu_footer"></div><br /><br />
    </td><td class="layout_rcolumn">
        <div class="contentfull">
            <div class="text32"><b><?php echo _('Add Invoice'); ?></b></div>
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
                <table class="layout_columns"><tr>
                    <td>
                        <?php echo _('Contact/Company'); ?>:<br /><input type="text" size="35" name="contact" id="text_input" onkeyup="getSuggestion()" value="" />
                        <input type="hidden" name="idcontact" id="idcontact" value="" />
                        <div class="suggestion_area_cont"><select id="suggestion_area" size="5" onchange="setSuggestion(this.options[this.selectedIndex].text,this.options[this.selectedIndex].value)" style="display:none;"></select><br />
                        <div id="cont_area"><?php echo _('Address'); ?>:<br /><?php echo $_SESSION['InvoiceEditSave']->invoice_address; ?></div> 
                    </td>
                    <td class="layout_col20">&nbsp;</td>
                    <td>
                        <table class="invoice_edit_summary">
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
                Description:<br /><?php echo $_SESSION['InvoiceEditSave']->description; ?><br /><br />
                Discount (%) :<br /><input type="text" name="fields[discount]" id="discount" size="4" onchange="reCalculateAmount();" value="<?php echo $discount; ?>" />
                <br /><br /><a href="#" onclick="showTax(); return false;">Add Tax (%)</a> <br />
                <div id = "tax_entry" style="display:none;">
                <input type="text" name="fields[tax]" id="tax" size="4" onchange="reCalculateAmount();" value="<?php echo $tax; ?>" />
                </div>
                <div class="spacerblock_20"></div>
                <table id="invoice_list" class="invoice_edit_list">
                    <tr>
                        <th class="layout_col180 left_text">&nbsp; &nbsp; <?php echo _('Item'); ?></th>
                        <th class="left_text"><?php echo _('Description'); ?></th>
                        <th class="layout_col80 center_text"><?php echo _('Qty.'); ?></th>
                        <th class="layout_col100 center_text"><?php echo _('Price'); ?></th>
                        <th class="layout_col100 center_text"><?php echo _('Total'); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php echo $_SESSION['InvoiceLine']->getNewFormFields(1); ?>
                </table>
                <a href="#" id="addOneMore">Add another</a>
                <?php
                $e_addform = new Event($_SESSION['InvoiceLine']->getInvoiceLinePrefix()."->eventAjaxInvoiceFormEntry");
                $e_addform->setSecure(false);
                $e_addform->setEventControler("ajax_evctl.php");
                ?>
                <script type="text/javascript">
                    var InvoiceLineCount = 2;
                    $("#addOneMore").click(function(){
                        $.get("<?php echo $e_addform->getUrl(); ?>&count="+InvoiceLineCount++, function(data){$("#invoice_list").append(data);});
                        return false;
                    }); 
                </script>
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