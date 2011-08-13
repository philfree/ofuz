<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class InvoicesMonthlyGraphBlock extends BaseBlock{

    public $short_description = 'Invoice Monthly Graph block';
    public $long_description = 'Shows the monthly graph for the invoice';
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	  if($_SESSION['do_invoice_list']->filter_month != ''){
	    if($_SESSION['do_invoice_list']->filter_month == '') {
		    //$_SESSION['do_invoice_list']->filter_month = date("m");
	    }
	    $month_name = date( 'F', mktime(0, 0, 0, $_SESSION['do_invoice_list']->filter_month) );
	    if($_SESSION['do_invoice_list']->filter_year == '' || $_SESSION['do_invoice_list']->filter_year == date("Y"))
		  $title = _('This ').$month_name;
	    else
		  $title = $month_name.' '.$_SESSION['do_invoice_list']->filter_year;

	    $this->setTitle(_($title));
	    $this->setContent($this->generateMonthlyGraphBlock());
	    $this->displayBlock();
	  }
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
       * @see class/Invoice.class.php
      */

      function generateMonthlyGraphBlock(){

	    $output = '';

	    $quote_total = $_SESSION['do_invoice_list']->getTotalQuotesForTheMonth();
	    $invoice_total = $_SESSION['do_invoice_list']->getTotalInvoiceForTheMonth();
	    $invoice_month_sent =  $_SESSION['do_invoice_list']->getTotalSentForTheMonth();
	    $paid_total = $_SESSION['do_invoice_list']->getTotalPaidForTheMonth();
	    $pastdue_total = $_SESSION['do_invoice_list']->getTotalPastDueForTheMonth();
	    
								    
	    $output .= '<table>';
	    if($quote_total) {
		    $output .= '<tr><td style="text-align:left;">'._('Quotes').':</td><td style="text-align:right;">'.$_SESSION['do_invoice_list']->viewAmount($quote_total).'</td></tr>';
	    }
	    if($invoice_total) {
		    $output .= '<tr><td style="text-align:left;">'._('Invoiced').':</td><td style="text-align:right;">'.$_SESSION['do_invoice_list']->viewAmount($invoice_total).'</td></tr>';
	    }
	    if($invoice_month_sent) {
		    $output .= '<tr><td style="text-align:left;">'._('Pending Payment').':</td><td style="text-align:right;">'.$_SESSION['do_invoice_list']->viewAmount($invoice_month_sent).'</td></tr>';
	    }
	    if($paid_total) {
		    $output .= '<tr><td style="text-align:left;">'._('Paid').':</td><td style="text-align:right;">'.$_SESSION['do_invoice_list']->viewAmount($paid_total).'</td></tr>';
	    }
	    if($pastdue_total) {					
		    $output .= '<tr><td style="text-align:left;">'._('Past Due').':</td><td style="text-align:right;">'.$_SESSION['do_invoice_list']->viewAmount($pastdue_total).'</td></tr>';
	    }

	    $output .= '</table>';

	    return $output;

      }

}

?>
