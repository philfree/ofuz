<?php

/**
  * A ContactInvoiceBlock plugin class
  * It display the status of invoices for that contact and link to the list of invoices.
  * it users the persistant do_cont object which is the current instance of the Contact displayed. 
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ContactInvoiceBlock extends BaseBlock{
      public $short_description = 'Contact Invoice Block';
      public $long_description = 'List the invoice history related to the contact in the contact detail page';
    
      /**
        * processBlock() , This method must be added  
        * Required to set the Block Title and The Block Content Followed by displayBlock()
        * Must extent BaseBlock
      */
      function processBlock(){
          $this->setTitle( _('Invoices'));
          $this->setContent($this->generateContactInvoiceContent());
          $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generateContactInvoiceContent(){
	    $output = '';
        if (!is_object($_SESSION['do_invoice_list'])) { 
            $do_invoice_list = new Invoice();
            $do_invoice_list->sessionPersistent("do_invoice_list", "index.php", OFUZ_TTL);          
        }
        if ($_SESSION['do_invoice_list']->hasInvoicesForEntity($_SESSION['do_cont']->idcontact,'Contact')) {
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
          
          $e_filter_inv  = new Event("do_invoice_list->eventFilterInvoice");
          $e_filter_inv->addParam("type","Contact");
          $e_filter_inv->addParam("idcontact",$_SESSION['do_cont']->idcontact);
          $e_filter_inv->addParam("goto", "invoices.php"); 
          $e_filter_inv->setLevel(10);
                
                
          $output .= '<table width="100%">' ;
          $output .= $_SESSION['do_invoice_list']->getInvoiceTotals($e_filter_inv,$_SESSION['do_cont']->idcontact);
          $output .= '</table>';
        } else {
          $this->setIsActive(false);
        }
        return $output;
      }
}

?>
