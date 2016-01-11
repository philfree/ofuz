<?php

/**
  * A PaymentLogBlock plugin class
  * contact.php has persistent session object as $_SESSION['ContactEditSave']
  * So for contact related data can be retrieve from this object
  * This is set a block on the left side of contact.php with contact details
  * Little complex than what we have on the other test Example Weather Object 
  * It also has 2 extra params in setContent() i.e. url_path and url_name
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class PaymentLogBlock extends BaseBlock{

    public $short_description = 'Payment log block for an invoice';
    public $long_description = 'Shows the last payment details for the invoice';

    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */
      function processBlock(){
          if ($_SESSION['do_invoice']->status == 'Quote') { 
            $this->setIsActive(false); 
          } else { 
            $this->setIsActive(true);
            $this->setTitle(_('Payments Log'));
            $this->setContent($this->generatePaymentLogDetails());
          }
          $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generatePaymentLogDetails(){
        $output = '';
        $do_company = new Company();
        
        
        
        $output .= '<div class="center_elem center_text">';
        $button_payment = new DynamicButton();
        $output .= $button_payment->CreateButton('#', 'receive payment', '', 'addPayment();return false;', 'dyn_button_receive_payment', 'width:138px;margin:0 auto;');
        $output .= '</div><div class="spacerblock_20"></div>'."\n";
        if($_SESSION['extra_amt'] != '' ){ 
          $cancel_payment = new Event("do_invoice->eventCancelMultiPayment");
          $cancel_payment->setLevel(10);
          $cancel_link = $cancel_payment->getLink(_('Cancel'));
          $msg = '<div style="margin-left:0px;">';
          $msg .= '<div class="messages_unauthorized">';
          $msg .= _('Add payment ').$_SESSION['do_invoice']->viewAmount($_SESSION['extra_amt'])._(' with the Note: ').'"'.$_SESSION['ref_num'].'"'._(' or with the amount you want.').'  &nbsp;'.$cancel_link;
          $msg .='</div></div>';

          $output .= "\n".'<div id="add_payment" style="display:block;">';
          $output .= $msg;
        }else{ 
            /*$do_payment_invoice = new PaymentInvoice();
            if($do_payment_invoice->getExtraAmoutNotPaid() !== false ){
                $msg = '<div style="margin-left:0px;">';
                $msg .= '<div class="messages_unauthorized">';
                $msg .= _('You have some extra amount not yet applied to invoices, do you want to apply them now ? ');
                $e_apply_extra_amt = new Event("do_invoice->eventSetApplyExtraAmount");
                $apply_link = $e_apply_extra_amt->getLink(_('apply'));
                $msg .='<br />'.$apply_link;
                $msg .='</div></div>';  
                $output .= $msg ;
                $output .= "\n".'<div id="add_payment" style="display:block;">';
            }else{
                $output .= "\n".'<div id="add_payment" style="display:none;">';
            }*/
            $output .= "\n".'<div id="add_payment" style="display:none;">';
        }

        

        
        $e_add_pay = new Event("do_invoice->eventAddPayment");
        $e_add_pay->setLevel(20);
        $e_add_pay->addParam("goto", $_SERVER['PHP_SELF']);
        $e_add_pay->addParam('id',$_SESSION['do_invoice']->idinvoice);
        $output .= $e_add_pay->getFormHeader();
        $output .= $e_add_pay->getFormEvent();

        

        if($_SESSION['extra_amt'] != ''){
           $output .= _('Amount :').' <br /><input type="Text" name="payment_amt" id="payment_amt" value="'.$_SESSION['extra_amt'].'"><br />';
        }else{
          $output .= _('Amount :').' <br /><input type="Text" name="payment_amt" id="payment_amt" value="'.$_SESSION['do_invoice']->amt_due.'"><br />';
          $output .= _('Note :').' <br /><input type="Text" name="payment_ref_num" id="payment_ref_num" value=""><br />';
        }
        $output .= '<input type="submit" name="psubmit" value="'._('Add Payment').'">';
        $output .= '</form>';

        $output .='        <br /><a href="#" onclick="hidePayment();return false;">'._('No, I\'ll add later').'</a><br /><br />';
        $output .= "\n".'</div>';
        $do_inv_payment_log = new PaymentLog();
        $do_inv_payment_log->getPaymentLog($_SESSION['do_invoice']->idinvoice);
        while($do_inv_payment_log->next()){
            $count = 0;
            $e_del_log = new Event("PaymentLog->eventDeletePaymentLog");
            $e_del_log->addParam("goto",$_SERVER['PHP_SELF']);
            $e_del_log->addParam("id",$do_inv_payment_log->idpaymentlog);
            $e_del_log->addParam("amt",$do_inv_payment_log->amount);

            $output .= "\n". '<div id="templt'. $count. '" class="co_worker_item co_worker_desc">'; 
            $output .= "\n". '<div style="position: relative;">';  
            $output .= "\n". '<span class="text12"><span class="text_lightblue">'.$do_inv_payment_log->ref_num.'</span> <span class="sep1">|</span> <b>'.$_SESSION['do_invoice']->viewAmount($do_inv_payment_log->amount).'</b><br />'.
            '<b>'.(isset($_SESSION['do_invoice']->idcompany)?$do_company->getCompanyName($_SESSION['do_invoice']->idcompany):$do_contact->getContactName($_SESSION['do_invoice']->idcontact)).'</b><br />'.
            '<span class="text_darkgray">Received: '.date('F j, Y',$do_inv_payment_log->timestamp).'</span></span>'
            ;
            $img_del = '<img class="delete_icon_tag" border="0" width="14px" height="14px" src="/images/delete.gif">';
            $output .= "\n". '<div width="15px" id="trashcan'. $count. '" class="deletenote" style="right:0;">'.$e_del_log->getLink($img_del, ' title="'._('Remove').'"').'</div>';
            $output .= "\n". '</div></div>';
            $output .= "\n". '<div class="invoiceline12"></div>';
            $count++;
        }

        $output .= "\n".'</div>';
        return $output;
      }
}

?>
