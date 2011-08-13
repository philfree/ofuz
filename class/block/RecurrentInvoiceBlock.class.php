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

class RecurrentInvoiceBlock extends BaseBlock{
  public $short_description = 'Recurrent Invoice block';
  public $long_description = 'Option to set an invoice as';
    
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
          }
          $this->setTitle(_('Recurrent Invoice'));
          $this->setContent($this->generateRecurrentInvoiceContent());
          $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generateRecurrentInvoiceContent(){
	    $output = '';
	    $do_rec_inv = new RecurrentInvoice();
        $do_rec_inv->getRecurrentInvoiceDetail($_SESSION['do_invoice']->idinvoice);
        if($do_rec_inv->getNumRows()) {
            $do_rec_inv->fetch();
            if($do_rec_inv->recurrence > 1) {
              $output .= $rec_inv_text = _('This invoice repeats every').$do_rec_inv->recurrence." ".$do_rec_inv->recurrencetype."s.";
            } else {
              $output .= $rec_inv_text = _('This invoice repeats')." <i>"._('every')." ". $do_rec_inv->recurrencetype."</i>.";
            }
            $output .= '<br />';
            $next_rec_inv_date = date("l F d, Y", strtotime($do_rec_inv->nextdate));
            $output .= _('The next invoice will be generated on ') . '<i>'. $next_rec_inv_date .'.</i>';
        } else { $this->setIsActive(false); }
        return $output;
      }

}

?>
