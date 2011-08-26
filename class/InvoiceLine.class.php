<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
* InvoiceLine class
* Using the DataObject
* 
+---------------+--------------+------+-----+---------+----------------+
| Field         | Type         | Null | Key | Default | Extra          |
+---------------+--------------+------+-----+---------+----------------+
| idinvoiceline | int(10)      | NO   | PRI | NULL    | auto_increment | 
| idinvoice     | int(14)      | NO   |     |         |                | 
| description   | mediumtext   | NO   |     |         |                | 
| price         | float(10,2)  | NO   |     |         |                | 
| qty           | float(10,2)  | NO   |     |         |                | 
| total         | float(10,2)  | NO   |     |         |                | 
| item          | varchar(200) | NO   |     |         |                | 
+---------------+--------------+------+-----+---------+----------------+

*/

class InvoiceLine extends DataObject {

    public $table = "invoiceline";
    protected $primary_key = "idinvoiceline";
    protected $prefix = "InvoiceLine";  // Should be the same as the class name 

    function formInvoiceMultiEntry() {
        $invoice_form = '';
        $InvoiceLineCount = 0;
        while ($this->next()) {
            $InvoiceLineCount = $this->getPrimaryKeyValue();
            $invoice_form .= '<tr id="'.$this->getInvoiceLinePrefix().$this->getPrimaryKeyValue().'" onclick="reCalculateAmount();">';
            $invoice_form .= '<td colspan="2"><input type="text" style="width: 90%" size="20" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][item]" value="'.$this->item.'" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][item]" onkeyup=\'getItemSuggestion("mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][item]","suggestion_area_item'.$this->getPrimaryKeyValue().'")\' autocomplete="off" />';

            $invoice_form .= '<select id="suggestion_area_item'.$this->getPrimaryKeyValue().'" size="5" style="border: solid 1px #b3b3b3; position: absolute; z-index: 2;display:none;" onchange=\'setItemSuggestion("this.options[this.selectedIndex].text","mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][item]","suggestion_area_item'.$this->getPrimaryKeyValue().'")\' ></select>';

            // Description below the item, but on the same row
            //$invoice_form .= '<br />'._('Description').'<br /><textarea cols="10" rows="2" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]" style="width:90%;">'.$this->description.'</textarea>';
            
            $invoice_form .= "\n".'<script type="text/javascript">
                     $(document).ready(function() {
                         $(document.getElementById("mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]")).expandable();
                     });
                      </script>
                     '."\n";

            $invoice_form .= '<input type="hidden" size="10" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][total]" value="'.$this->total.'" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][total]" readonly="readonly" />
            <input type="hidden" size="10" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][discounted_amount]" value="'.$this->discounted_amount.'" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][discounted_amount]" readonly="readonly" />
            <input type="hidden" size="10" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][taxed_amount]" value="'.$this->taxed_amount.'" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][taxed_amount]" readonly="readonly" /></td>';

            /*
            // Description in column 2
            $invoice_form .= '<td><textarea cols="10" rows="2" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]" style="width:90%;">'.$this->description.'</textarea>';
            $invoice_form .= "\n".'<script type="text/javascript">
                     $(document).ready(function() {
                         $(document.getElementById("mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]")).expandable();
                     });
                      </script></td>
                     '."\n";
            */

            $invoice_form .= '<td class="center_text"><input type="text" size="5" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][qty]" value="'.$this->qty.'" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][qty]" onchange=\'computeTotal()\' /></td>';

            $invoice_form .= '<td><input type="text" size="10" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][price]" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][price]" value="'.$this->price.'" onchange=\'computeTotal()\' /></td>';

            $invoice_form .= '<td><input type="text" size="4" class="center_text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][line_tax]" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][line_tax]" value="'.$this->line_tax.'" onchange=\'computeTotal()\' /></td>';

            //$invoice_form .= '<td><input type="text" size="10" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][total]" value="'.$this->total.'" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][total]" readonly="readonly" /></td>';

            $invoice_form .= '<td><input type="text" size="10" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][line_sub_total]" value="'.$this->line_sub_total.'" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][line_sub_total]" readonly="readonly" /></td>';


            $e_delForm = new Event($this->getInvoiceLinePrefix()."->eventAjaxInvoiceLineDelete");
            $e_delForm->addParam("id", $this->getPrimaryKeyValue());
            $e_delForm->setSecure(false);
            $e_delForm->setEventControler("ajax_evctl.php");

            $invoice_form .= '<td><a href="#" id="delete'.$this->getInvoiceLinePrefix().$this->getPrimaryKeyValue().'" title="'._('Delete this item').'"><img src="/images/delete.gif" alt="" /></a>';
            $invoice_form .= '
                <script type="text/javascript">
                    $("#delete'.$this->getInvoiceLinePrefix().$this->getPrimaryKeyValue().'").click(
                        function () {
                            $.get("'.$e_delForm->getUrl().'");
                            $("#'.$this->getInvoiceLinePrefix().$this->getPrimaryKeyValue().'").hide(1000).remove();
                            $("#'.$this->getInvoiceLinePrefix().$this->getPrimaryKeyValue().'_dscr").hide(1000).remove();
                            return false;
                        }); 
                </script>';

            $invoice_form .= '</td></tr>';
            // Description on a new row
            $invoice_form .= '<tr id="'.$this->getInvoiceLinePrefix().$this->getPrimaryKeyValue().'_dscr" class="invoice_edit_list_desc"><td colspan="5">&nbsp; &nbsp; <span class="gray_text">'._('Item Description').'</span><br /><textarea cols="10" rows="2" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]" id="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]" style="width:90%;">'.$this->description.'</textarea>';
            $invoice_form .= "\n".'<script type="text/javascript">
                     $(document).ready(function() {
                         $(document.getElementById("mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][description]")).expandable();
                     });
                      </script></td><td colspan="2">&nbsp;</td></tr>'."\n";
        }
        $invoice_form .= '</table>';
        $invoice_form .= '<a href="#" id="addOneMore">'._('Add another').'</a>';

        $e_addform = new Event($this->getInvoiceLinePrefix()."->eventAjaxInvoiceFormEntry");
        $e_addform->setSecure(false);
        $e_addform->setEventControler("ajax_evctl.php");
        $invoice_form .= '
                <script type="text/javascript">
                    var InvoiceLineCount = '.++$InvoiceLineCount.';
                    $("#addOneMore").click(function(){
                        $.get("'.$e_addform->getUrl().'&count="+InvoiceLineCount++, function(data){$("#invoice_list").append(data);});
                        return false;
                    }); 
                </script>'."\n";
        return $invoice_form;
    }

    function getNewFormFields($new_invoice_line_count) {
        $invoice_form = '<tr id="'.$this->getInvoiceLinePrefix().$new_invoice_line_count.'" onclick="reCalculateAmount();">';
        $invoice_form .= '<td colspan="2"><input type="text" style="width: 90%" size="20" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][item]" value="" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][item]" onkeyup=\'getItemSuggestion("mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][item]","suggestion_area_item'.$new_invoice_line_count.'")\' autocomplete="off" />';

        $invoice_form .= '<select id="suggestion_area_item'.$new_invoice_line_count.'" size="5" style="border: solid 1px #b3b3b3; position: absolute; z-index: 2;display:none;" onchange=\'setItemSuggestion("this.options[this.selectedIndex].text","mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][item]","suggestion_area_item'.$new_invoice_line_count.'")\' ></select>';

        /*
        // Description below the item, but on the same row
        $invoice_form .= '<br />'._('Description').'<br /><textarea cols="20" rows="2" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]" style="width:90%;"></textarea>';
        $invoice_form .= '<script type="text/javascript">
                     $(document).ready(function() {
                         $(document.getElementById("mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]")).expandable();
                     });
                      </script>
                     '."\n";
        */

        $invoice_form .= '<input type="hidden" size="10" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][total]" value="" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][total]" readonly="readonly" />
        <input type="hidden" size="10" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][total_with_discount]" value="" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][total_with_discount]" readonly="readonly" />
        <input type="hidden" size="10" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][discounted_amount]" value="" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][discounted_amount]" readonly="readonly" />
        <input type="hidden" size="10" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][taxed_amount]" value="" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][taxed_amount]" readonly="readonly" /></td>';

        /*
        // Description in column 2
        $invoice_form .= '<td><textarea cols="10" rows="2" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]" style="width:90%;"></textarea>';
        $invoice_form .= '<script type="text/javascript">
                     $(document).ready(function() {
                         $(document.getElementById("mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]")).expandable();
                     });
                      </script></td>
                     '."\n";
        */

        
        $invoice_form .= '<td class="center_text"><input type="text" size="5" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][qty]" value="" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][qty]" onchange=\'computeTotal()\' /></td>';

        $invoice_form .= '<td><input type="text" size="10" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][price]" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][price]" value="" onchange=\'computeTotal()\' /></td>';

        $invoice_form .= '<td ><input type="text" size="4" class="center_text" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][line_tax]" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][line_tax]" value="" onchange=\'computeTotal()\' /></td>';

        //$invoice_form .= '<td><input type="hidden" size="10" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][total]" value="" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][total]" readonly="readonly" /></td>';


        $invoice_form .= '<td><input type="text" size="10" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][line_sub_total]" value="" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][line_sub_total]" readonly="readonly" /></td>';

        $e_delForm = new Event($this->getInvoiceLinePrefix()."->eventAjaxInvoiceLineDelete");
        $e_delForm->addParam("id", $this->getPrimaryKeyValue());
        $e_delForm->setSecure(false);
        $e_delForm->setEventControler("ajax_evctl.php");

        $invoice_form .= '<td><a href="#" id="delete'.$this->getInvoiceLinePrefix().$new_invoice_line_count.'" title="'._('Delete this item').'"><img src="/images/delete.gif" alt="" /></a>';
        $invoice_form .= '
            <script type="text/javascript">
                $("#delete'.$this->getInvoiceLinePrefix().$new_invoice_line_count.'").click(
                    function () {
                        $.get("'.$e_delForm->getUrl().'");
                        $("#'.$this->getInvoiceLinePrefix().$new_invoice_line_count.'").hide(1000).empty();
                        $("#'.$this->getInvoiceLinePrefix().$new_invoice_line_count.'_dscr").hide(1000).empty();
                        return false;
                    }); 
            </script>';
        $invoice_form .= '</td></tr>';
        // Description on a new row
        $invoice_form .= '<tr id="'.$this->getInvoiceLinePrefix().$new_invoice_line_count.'_dscr" class="invoice_edit_list_desc"><td colspan="5">&nbsp; &nbsp; <span class="gray_text">'._('Item Description').'</span><br /><textarea cols="20" rows="2" name="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]" id="mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]" style="width:90%;"></textarea>';
        $invoice_form .= '<script type="text/javascript">
                     $(document).ready(function() {
                         $(document.getElementById("mfields['.$this->getTable().'_new]['.$new_invoice_line_count.'][description]")).expandable();
                     });
                      </script></td><td colspan="2">&nbsp;</td></tr>'."\n";

        return $invoice_form;
    }

    function eventSaveInvoiceLine(EventControler $evctl)  {
        $mfields = $evctl->mfields;
        //print_r($mfields);exit;
        $this->setLog("\n ".$this->getInvoiceLinePrefix().": Saving multiple addresses");
        $this->idinvoice = $_SESSION['InvoiceEditSave']->idinvoice;
        if (is_array($mfields['invoiceline'])) { 
            foreach($mfields['invoiceline'] as $primary_key_value=>$fields) {
                $this->getId($primary_key_value);
                $this->item = $fields['item'];
                $this->price = $fields['price'];
                $this->qty = $fields['qty'];
                $this->line_tax = $fields['line_tax'];
                $this->total = $fields['total'];
                $this->description = $fields['description'];
                $this->taxed_amount = $fields['taxed_amount'];
                $this->discounted_amount = $fields['discounted_amount'];
                //$this->setPrimaryKeyValue($primary_key_value);
                //$this->setLog("\n ".$this->getInvoiceLinePrefix() .": Updating Invoiceline:".$this->invoiceline);
                $this->update();
            }
            $go_to = "/Invoice/".$primary_key_value;
        }
        if (is_array($mfields['invoiceline_new'])) {
            foreach($mfields['invoiceline_new'] as $key=>$fields) {
                $this->addNew();
                $this->idinvoice = $_SESSION['InvoiceEditSave']->getPrimaryKeyValue();
                $this->item = $fields['item'];
                $this->price = $fields['price'];
                $this->qty = $fields['qty'];
                $this->line_tax = $fields['line_tax'];
                $this->total = $fields['total'];
                $this->description = $fields['description'];
                $this->taxed_amount = $fields['taxed_amount'];
                $this->discounted_amount = $fields['discounted_amount'];
                //$this->setLog("\n Invoice Line: Adding Price:".$this->idinvoice." ".$fields['price']." Qty:".$this->qty.", for Invoice:".$this->idinvoice);
                if (strlen($this->price) > 0) {
                    $this->add();
                }
            }
            $go_to = "/Invoice/".$this->idinvoice;
        }
        if($evctl->goto != ""){
            // $evctl->setDisplayNext(new Display($go_to));
        }  
    }

    /**
     * this method is called by the ajax_evctl.php as an event action
     * and returns a new form entry to add a row to the invoiceline table.
     * 
     * @param EventControler
     */
    function eventAjaxInvoiceFormEntry(EventControler $evctl) {
        $invoice_form = $this->getNewFormFields($evctl->count);
        $evctl->addOutputValue($invoice_form);
    }

    function eventAjaxInvoiceLineDelete(EventControler $evctl) {
   	    $this->delete($evctl->id);
    }

    function getInvoiceLinePrefix() {
        return $this->prefix;
    }
}
?>
