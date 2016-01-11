<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class InvoicesYTDBlock extends BaseBlock{
    
  public $short_description = 'Invoice Year to date block';
  public $long_description = 'Shows the invoice history year to date';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	    if($_SESSION['do_invoice_list']->filter_year == '' || $_SESSION['do_invoice_list']->filter_year == date("Y"))
		      $title =  _('Invoices Year to date ');
            else
		      $title =  _('Totals for').' '.$_SESSION['do_invoice_list']->filter_year;

	    $this->setTitle(_($title));
	    $this->setContent($this->generateInvoicesYTDBlock());
	    $this->displayBlock();

      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
       * @see class/Invoice.class.php
      */

      function generateInvoicesYTDBlock(){

	    $output = '';

	    $output .= '<div class="spacerblock_20"></div>';

	    $quote_ytd_total = $_SESSION['do_invoice_list']->getTotalQuotesYTD();
	    $invoice_ytd_total = $_SESSION['do_invoice_list']->getTotalInvoiceYTD();
	    $invoice_sent = $_SESSION['do_invoice_list']->getTotalSentYTD();
	    $paid_ytd_total = $_SESSION['do_invoice_list']->getTotalPaidYTD();
	    $pastdue_ytd_total = $_SESSION['do_invoice_list']->getTotalPastDueYTD();
	    
	    
	    $e_filter_inv  = new Event("do_invoice_list->eventFilterInvoice");
	    $e_filter_inv->addParam("type","User");
	    $e_filter_inv->addParam("goto", "invoices.php"); 
	    $e_filter_inv->setLevel(10);
	    $output .= '<table width="100%">';
	    if($quote_ytd_total) {
		    $e_filter_inv->addParam("status","Quote");
		    $output .= '<tr><td style="text-align:left;">Quotes:</td><td style="text-align:right;">'.$e_filter_inv->getLink($_SESSION['do_invoice_list']->viewAmount($quote_ytd_total)).'</td></tr>';
	    }
	    if($invoice_ytd_total) {	
		    $e_filter_inv->addParam("status","Invoiced");			
		    $output .= '<tr><td style="text-align:left;">Invoiced:</td><td style="text-align:right;">'.$e_filter_inv->getLink($_SESSION['do_invoice_list']->viewAmount($invoice_ytd_total)).'</td></tr>';
	    }
	    if($invoice_sent) {			
		    $e_filter_inv->addParam("status","Sent");	
		    $output .= '<tr><td style="text-align:left;">Pending Payment:</td><td style="text-align:right;">'.$e_filter_inv->getLink($_SESSION['do_invoice_list']->viewAmount($invoice_sent)).'</td></tr>';
	    }
	    if($paid_ytd_total) {
		    $e_filter_inv->addParam("status","Paid");
		    $output .= '<tr><td style="text-align:left;">Paid:</td><td style="text-align:right;">'.$e_filter_inv->getLink($_SESSION['do_invoice_list']->viewAmount($paid_ytd_total)).'</td></tr>';
	    }
	    if($pastdue_ytd_total) {
		    $e_filter_inv->addParam("status","Overdue");
		    $output .= '<tr><td style="text-align:left;">Past Due:</td><td style="text-align:right;">'.$e_filter_inv->getLink($_SESSION['do_invoice_list']->viewAmount($pastdue_ytd_total)).'</td></tr>';
	    }
	    $output .= '</table>';
	    return $output;

      }

}

?>
