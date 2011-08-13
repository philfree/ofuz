<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

	/**
	 * Create the recurrent invoices depeding on the recurrent date.
	 * Will get the recurrent invoices for the date where the invoice set as recurrent and will create an invoice with the same data
  * setting the invice due date as today.
  * Will also process the invoice call back object to set the call back url for the online payament for the created invoice
  * @see class/Invoice.class.php
  * @see class/RecurrentInvoice.class.php
  * @see class/InvoiceLine.class.php
  * @see class/InvoiceCallback
	 */
	 
	include_once("config.php");
	
	set_time_limit(3600); 	 
	
	$do_recurrent = new RecurrentInvoice();
        $do_invoice = new Invoice();
        $do_inv_line = new InvoiceLine();
        
        $do_recurrent->getRecurrentInvoiceForTheDay();
        if($do_recurrent->getNumRows()){
            while($do_recurrent->next()){
                // Create a new Invoice First
             // if($do_recurrent->idinvoice == '52'  ){ //Testing method by harcoding the idinvoice
             if($do_recurrent->iduser == 15){
                
                $do_invoice->addNew();
                $do_invoice->num = $do_invoice->getUniqueInvoiceNum($do_recurrent->iduser);
                $do_invoice->iduser = $do_recurrent->iduser;
                $do_invoice->description = $do_recurrent->description;
                $do_invoice->status = 'Sent';
                $do_invoice->discount = $do_recurrent->discount;
                $do_invoice->invoice_term = $do_recurrent->invoice_term;
                $do_invoice->invoice_address = $do_recurrent->invoice_address;
                $do_invoice->idcompany = $do_recurrent->idcompany;
                $do_invoice->idcontact = $do_recurrent->idcontact;
                $do_invoice->datecreated = $do_recurrent->nextdate;
                $do_invoice->invoice_note = $do_recurrent->invoice_note;
                $do_invoice->due_date = date("Y-m-d");
                $do_invoice->sub_total = $do_recurrent->sub_total;
                $do_invoice->net_total = $do_recurrent->net_total;
                $do_invoice->add();
                // Add the Invoice Line
                $idinvoice  = $do_invoice->getPrimaryKeyValue();
                echo '<br />New Invoice Created :: '.$idinvoice.'<br />';
                $do_inv_add = new Invoice();
                $do_inv_add->getId($do_recurrent->idinvoice);
                $inv_line = $do_inv_add->getChildinvoiceline();
                while($inv_line->next()){
                    $do_inv_line->addNew();
                    $do_inv_line->idinvoice = $idinvoice;
                    $do_inv_line->description = $inv_line->description;
                    $do_inv_line->price = $inv_line->price;
                    $do_inv_line->qty = $inv_line->qty;
                    $do_inv_line->total = $inv_line->total;  
                    $do_inv_line->item = $inv_line->item;  
                    $do_inv_line->add();
                }
                $do_invoice->setInvoiceCalculations($idinvoice);// Call this method for other calculations

                //update callback
                $do_inv_callbak = new InvoiceCallback();
                $do_inv_callbak->updateCallBack($do_recurrent->idcontact,$idinvoice);
                
                // Process RegistrationInvoiceLog will update only if the invoice is from registration Part of Ofuz.com
                $do_RegistrationInvoiceLog = new RegistrationInvoiceLog();
                // @params old invoiceid,new invoice id and the iduser
                $do_RegistrationInvoiceLog->process_reg_invoice_log($do_recurrent->idinvoice,$idinvoice,$do_recurrent->iduser);
    
                // Update Recurrent
                $do_recurrent_update = new RecurrentInvoice();
                $next_date = $do_recurrent_update->getNextDate($do_recurrent->recurrence,$do_recurrent->recurrencetype,$do_recurrent->nextdate);
                $do_recurrent_update->getId($do_recurrent->idrecurrentinvoice);
                $do_recurrent_update->nextdate =  $next_date;
                $do_recurrent_update->idinvoice =  $idinvoice;
                $do_recurrent_update->update();
                $do_recurrent_update->free();
                //Sending Email to the customer
                //echo 'Calling method sendInvoiceByEmail()..........<br />';
                $do_inv_add->sendInvoiceByEmail($idinvoice,$do_inv_add->idcontact,$do_inv_add->iduser,true);
                $do_inv_add->free();
            }
          }
        }
	 
?>
