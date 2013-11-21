<?php
 /** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
    // Copyright 2008 - 2010 all rights reserved, SQLFusion LLC,  info@sqlfusion.com
 /** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 

set_time_limit(3600); //1 hr

  /**
   * Class OfuzExportXML
   * To take ofuz account backup
   * User can export the online account
   * 
   *  @package Ofuz
   *  @Author Ravi Rokkam <ravi@sqlfusion.com>
   */


class OfuzExportXML extends DataObject {

  function eventExportUserContacts(EventControler $evtcl) {
    $do_contact = new Contact();
    $do_contact->getUserContacts($_SESSION['do_User']->iduser);


    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
    $xml .= "<contacts>\n";



/**
  *Contact Exporting
 */

   while($do_contact->next()) {

      $xml .= " <contact>\n";

     $xml .= "  <idcontact><![CDATA[".$do_contact->getData("idcontact")."]]></idcontact>\n";
      $xml .= "  <firstname><![CDATA[".$do_contact->getData("firstname")."]]></firstname>\n";
      $xml .= "  <lastname><![CDATA[".$do_contact->getData("lastname")."]]></lastname>\n";
      $xml .= "  <position><![CDATA[".$do_contact->getData("position")."]]></position>\n";
      $xml .= "  <company><![CDATA[".$do_contact->getData("company")."]]></company>\n";
      $xml .= "  <idcompany><![CDATA[".$do_contact->getData("idcompany")."]]></idcompany>\n";
      $xml .= "  <iduser><![CDATA[".$do_contact->getData("iduser")."]]></iduser>\n";
      $xml .= "  <picture><![CDATA[".$do_contact->getData("picture")."]]></picture>\n";
      $xml .= "  <summary><![CDATA[".$do_contact->getData("summary")."]]></summary>\n";
      $xml .= "  <birthday><![CDATA[".$do_contact->getData("birthday")."]]></birthday>\n";
      $xml .= "  <portal_code><![CDATA[".$do_contact->getData("portal_code")."]]></portal_code>\n";
      $xml .= "  <fb_userid><![CDATA[".$do_contact->getData("fb_userid")."]]></fb_userid>\n";
      $xml .= "  <tw_user_id><![CDATA[".$do_contact->getData("tw_user_id")."]]></tw_user_id>\n";
      $xml .= "  <email_optout><![CDATA[".$do_contact->getData("email_optout")."]]></email_optout>\n\n";

      $contact_address = $do_contact->getChildContactAddress();


     while($contact_address->next()) {
        $xml .= "  <contact_address>\n";
        $xml .= "   <idcontact_address><![CDATA[".$contact_address->idcontact_address."]]></idcontact_address>\n";
        $xml .= "   <city><![CDATA[".$contact_address->city."]]></city>\n";
        $xml .= "   <country><![CDATA[".$contact_address->country."]]></country>\n";
        $xml .= "   <state><![CDATA[".$contact_address->state."]]></state>\n";
        $xml .= "   <street><![CDATA[".$contact_address->street."]]></street>\n";
        $xml .= "   <zipcode><![CDATA[".$contact_address->zipcode."]]></zipcode>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_address->idcontact."]]></idcontact>\n";
        $xml .= "   <address><![CDATA[".$contact_address->address."]]></address>\n";
        $xml .= "   <address_type><![CDATA[".$contact_address->address_type."]]></address_type>\n";
        $xml .= "  </contact_address>\n";
      }
      $contact_address->free();

      $contact_email = $do_contact->getChildContactEmail();

      while($contact_email->next()) {
        $xml .= "  <contact_email>\n";
        $xml .= "   <idcontact_email><![CDATA[".$contact_email->idcontact_email."]]></idcontact_email>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_email->idcontact."]]></idcontact>\n";
        $xml .= "   <email_address><![CDATA[".$contact_email->email_address."]]></email_address>\n";
        $xml .= "   <email_type><![CDATA[".$contact_email->email_type."]]></email_type>\n";
        $xml .= "   <email_isdefault><![CDATA[".$contact_email->email_isdefault."]]></email_isdefault>\n";
        $xml .= "  </contact_email>\n";
      }
      $contact_email->free();

      $contact_phone = $do_contact->getChildContactPhone();

      while($contact_phone->next()) {
        $xml .= "  <contact_phone>\n";
        $xml .= "   <idcontact_phone><![CDATA[".$contact_phone->idcontact_phone."]]></idcontact_phone>\n";
        $xml .= "   <phone_number><![CDATA[".$contact_phone->phone_number."]]></phone_number>\n";
        $xml .= "   <phone_type><![CDATA[".$contact_phone->phone_type."]]></phone_type>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_phone->idcontact."]]></idcontact>\n";
        $xml .= "  </contact_phone>\n";
      }
      $contact_phone->free();

      $contact_note = $do_contact->getChildContactNotes();

      while($contact_note->next()) {
        $xml .= "  <contact_note>\n";
        $xml .= "   <idcontact_note><![CDATA[".$contact_note->idcontact_note."]]></idcontact_note>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_note->idcontact."]]></idcontact>\n";
        $xml .= "   <note><![CDATA[".$contact_note->note."]]></note>\n";
        $xml .= "   <date_added><![CDATA[".$contact_note->date_added."]]></date_added>\n";
        $xml .= "   <document><![CDATA[".$contact_note->document."]]></document>\n";
        $xml .= "   <idcompany><![CDATA[".$contact_note->idcompany."]]></idcompany>\n";
        $xml .= "   <iduser><![CDATA[".$contact_note->iduser."]]></iduser>\n";
        $xml .= "   <priority><![CDATA[".$contact_note->priority."]]></priority>\n";
        $xml .= "   <send_email><![CDATA[".$contact_note->send_email."]]></send_email>\n";
        $xml .= "   <hours_work><![CDATA[".$contact_note->hours_work."]]></hours_work>\n";
        $xml .= "   <note_visibility><![CDATA[".$contact_note->note_visibility."]]></note_visibility>\n";
        $xml .= "   <type><![CDATA[".$contact_note->type."]]></type>\n";
        $xml .= "  </contact_note>\n";
      }
      $contact_note->free();

      $do_tag = new Tag();
      $do_tag->getUserContactTags($_SESSION['do_User']->iduser,$do_contact->getData("idcontact"));
      $do_tag->getValues();
      while($do_tag->next()) {
        $xml .= "  <contact_tag>\n";
        $xml .= "   <idtag><![CDATA[".$do_tag->idtag."]]></idtag>\n";
        $xml .= "   <tag_name><![CDATA[".$do_tag->tag_name."]]></tag_name>\n";
        $xml .= "   <iduser><![CDATA[".$do_tag->iduser."]]></iduser>\n";
        $xml .= "   <reference_type><![CDATA[".$do_tag->reference_type."]]></reference_type>\n";
        $xml .= "   <idreference><![CDATA[".$do_tag->idreference."]]></idreference>\n";
        $xml .= "   <date_added><![CDATA[".$do_tag->date_added."]]></date_added>\n";
        $xml .= "  </contact_tag>\n"; 
      }
     $do_tag->free();


      $do_task = new Task();
      $do_task->getContactTaskWithoutProject($do_contact->getData("idcontact"));
      while($do_task->next()) {
        $xml .= "  <contact_task_without_project>\n";
        $xml .= "   <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "   <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "   <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "   <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "   <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "   <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "   <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "   <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "   <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "   <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "   <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "   <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n";
        $xml .= "  </contact_task_without_project>\n";
      }
      $do_task->free();



/**
  * For all the contact tasks which are associated with Project.
 */

			  $do_task = new Task();
			  $do_task->getContactTasksAssociatedWithProject($do_contact->getData("idcontact"));
			  while($do_task->next()) {
			    $do_project = new Project();
			    $do_project->getId($do_task->idproject);

				  $xml .= "  <contact_task_with_project>\n";
				  $xml .= "   <idproject><![CDATA[".$do_project->idproject."]]></idproject>\n";
				  $xml .= "   <iduser><![CDATA[".$do_project->iduser."]]></iduser>\n";
				  $xml .= "   <name><![CDATA[".$do_project->name."]]></name>\n";
				  $xml .= "   <end_date_dateformat><![CDATA[".$do_project->end_date_dateformat."]]></end_date_dateformat>\n";
				  $xml .= "   <idcompany><![CDATA[".$do_project->idcompany."]]></idcompany>\n";
				  $xml .= "   <status><![CDATA[".$do_project->status."]]></status>\n";
				  $xml .= "   <effort_estimated_hrs><![CDATA[".$do_project->effort_estimated_hrs."]]></effort_estimated_hrs>\n";
				  $xml .= "   <is_public><![CDATA[".$do_project->is_public."]]></is_public>\n\n";

				  $xml .= "   <project_task>\n";
				  $xml .= "    <idproject_task><![CDATA[".$do_task->getData("idproject_task")."]]></idproject_task>\n";
				  $xml .= "    <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
				  $xml .= "    <idproject><![CDATA[".$do_task->getData("idproject")."]]></idproject>\n";
				  $xml .= "    <progress><![CDATA[".$do_task->getData("progress")."]]></progress>\n";
				  $xml .= "    <drop_box_code><![CDATA[".$do_task->getData("drop_box_code")."]]></drop_box_code>\n";
				  $xml .= "    <priority><![CDATA[".$do_task->getData("priority")."]]></priority>\n";
				  $xml .= "    <hrs_work_expected><![CDATA[".$do_task->getData("hrs_work_expected")."]]></hrs_work_expected>\n";

				  $xml .= "    <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
				  $xml .= "    <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
				  $xml .= "    <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
				  $xml .= "    <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
				  $xml .= "    <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
				  $xml .= "    <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
				  $xml .= "    <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
				  $xml .= "    <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
				  $xml .= "    <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
				  $xml .= "    <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
				  $xml .= "    <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n\n";

			    $do_prj_discuss = new ProjectDiscuss();
			    $do_prj_discuss->getProjectTaskDiscussions($do_task->getData("idproject_task"));
			    while($do_prj_discuss->next()) {

				  $xml .= "    <project_discuss>\n";
				  $xml .= "     <idproject_discuss><![CDATA[".$do_prj_discuss->getData("idproject_discuss")."]]></idproject_discuss>\n";
				  $xml .= "     <idproject_task><![CDATA[".$do_prj_discuss->getData("idproject_task")."]]></idproject_task>\n";
				  $xml .= "     <idtask><![CDATA[".$do_prj_discuss->getData("idtask")."]]></idtask>\n";
				  $xml .= "     <idproject><![CDATA[".$do_prj_discuss->getData("idproject")."]]></idproject>\n";
				  $xml .= "     <discuss><![CDATA[".$do_prj_discuss->getData("discuss")."]]></discuss>\n";
				  $xml .= "     <date_added><![CDATA[".$do_prj_discuss->getData("date_added")."]]></date_added>\n";
				  $xml .= "     <document><![CDATA[".$do_prj_discuss->getData("document")."]]></document>\n";
				  $xml .= "     <iduser><![CDATA[".$do_prj_discuss->getData("iduser")."]]></iduser>\n";
				  $xml .= "     <drop_box_sender><![CDATA[".$do_prj_discuss->getData("drop_box_sender")."]]></drop_box_sender>\n";
				  $xml .= "     <priority><![CDATA[".$do_prj_discuss->getData("priority")."]]></priority>\n";
				  $xml .= "     <hours_work><![CDATA[".$do_prj_discuss->getData("hours_work")."]]></hours_work>\n";
				  $xml .= "     <discuss_edit_access><![CDATA[".$do_prj_discuss->getData("discuss_edit_access")."]]></discuss_edit_access>\n";
				  $xml .= "     <type><![CDATA[".$do_prj_discuss->getData("type")."]]></type>\n";
				  $xml .= "    </project_discuss>\n";
			    }
			    $do_prj_discuss->free();

			    $xml .= "   </project_task>\n";

			    $xml .= "  </contact_task_with_project>\n";

			    $do_project->free();
			  }
			  $do_task->free();



/**
  * Invoice
 */
			$do_invoice = new Invoice();
			$do_invoice->getContactInvoiceDetails($do_contact->getData("idcontact"));
			while($do_invoice->next()) {
  
			  $xml .= "  <invoice>\n";
			  $xml .= "   <idinvoice><![CDATA[".$do_invoice->getData("idinvoice")."]]></idinvoice>\n";
			  $xml .= "   <num><![CDATA[".$do_invoice->getData("num")."]]></num>\n";
			  $xml .= "   <iduser><![CDATA[".$do_invoice->getData("iduser")."]]></iduser>\n";
			  $xml .= "   <description><![CDATA[".$do_invoice->getData("description")."]]></description>\n";
			  $xml .= "   <amount><![CDATA[".$do_invoice->getData("amount")."]]></amount>\n";
			  $xml .= "   <datepaid><![CDATA[".$do_invoice->getData("datepaid")."]]></datepaid>\n";
			  $xml .= "   <datecreated><![CDATA[".$do_invoice->getData("datecreated")."]]></datecreated>\n";
			  $xml .= "   <status><![CDATA[".$do_invoice->getData("status")."]]></status>\n";
			  $xml .= "   <discount><![CDATA[".$do_invoice->getData("discount")."]]></discount>\n";
			  $xml .= "   <idcontact><![CDATA[".$do_invoice->getData("idcontact")."]]></idcontact>\n";
			  $xml .= "   <due_date><![CDATA[".$do_invoice->getData("due_date")."]]></due_date>\n";
			  $xml .= "   <invoice_address><![CDATA[".$do_invoice->getData("invoice_address")."]]></invoice_address>\n";
			  $xml .= "   <invoice_term><![CDATA[".$do_invoice->getData("invoice_term")."]]></invoice_term>\n";
			  $xml .= "   <invoice_note><![CDATA[".$do_invoice->getData("invoice_note")."]]></invoice_note>\n";
			  $xml .= "   <sub_total><![CDATA[".$do_invoice->getData("sub_total")."]]></sub_total>\n";
			  $xml .= "   <net_total><![CDATA[".$do_invoice->getData("net_total")."]]></net_total>\n";
			  $xml .= "   <amt_due><![CDATA[".$do_invoice->getData("amt_due")."]]></amt_due>\n";
			  $xml .= "   <idcompany><![CDATA[".$do_invoice->getData("idcompany")."]]></idcompany>\n";
			  $xml .= "   <tax><![CDATA[".$do_invoice->getData("tax")."]]></tax>\n";
			  $xml .= "   <set_delete><![CDATA[".$do_invoice->getData("set_delete")."]]></set_delete>\n";
			  $xml .= "   <total_discounted_amt><![CDATA[".$do_invoice->getData("total_discounted_amt")."]]></total_discounted_amt>\n";
			  $xml .= "   <total_taxed_amount><![CDATA[".$do_invoice->getData("total_taxed_amount")."]]></total_taxed_amount>\n";

			  $invoice_line = $do_invoice->getChildInvoiceLine();
			  while($invoice_line->next()) {

			    $xml .= "   <invoiceline>\n";
			    $xml .= "    <idinvoiceline><![CDATA[".$invoice_line->idinvoiceline."]]></idinvoiceline>\n";
			    $xml .= "    <idinvoice><![CDATA[".$invoice_line->idinvoice."]]></idinvoice>\n";
			    $xml .= "    <description><![CDATA[".$invoice_line->description."]]></description>\n";
			    $xml .= "    <price><![CDATA[".$invoice_line->price."]]></price>\n";
			    $xml .= "    <qty><![CDATA[".$invoice_line->qty."]]></qty>\n";
			    $xml .= "    <total><![CDATA[".$invoice_line->total."]]></total>\n";
			    $xml .= "    <item><![CDATA[".$invoice_line->item."]]></item>\n";
			    $xml .= "    <line_tax><![CDATA[".$invoice_line->line_tax."]]></line_tax>\n";
			    $xml .= "    <discounted_amount><![CDATA[".$invoice_line->discounted_amount."]]></discounted_amount>\n";
			    $xml .= "    <taxed_amount><![CDATA[".$invoice_line->taxed_amount."]]></taxed_amount>\n";
			    $xml .= "   </invoiceline>\n";
			  }
			  $invoice_line->free();

			  $do_recurrent_invoice = new RecurrentInvoice();
			  $do_recurrent_invoice->getRecurrentInvoiceDetail($do_invoice->getData("idinvoice"));
			  while($do_recurrent_invoice->next()) {
			    $xml .= "   <recurrentinvoice>\n";
			    $xml .= "    <idrecurrentinvoice><![CDATA[".$do_recurrent_invoice->getData("idrecurrentinvoice")."]]></idrecurrentinvoice>\n";
			    $xml .= "    <iduser><![CDATA[".$do_recurrent_invoice->getData("iduser")."]]></iduser>\n";
			    $xml .= "    <idinvoice><![CDATA[".$do_recurrent_invoice->getData("idinvoice")."]]></idinvoice>\n";
			    $xml .= "    <nextdate><![CDATA[".$do_recurrent_invoice->getData("nextdate")."]]></nextdate>\n";
			    $xml .= "    <recurrence><![CDATA[".$do_recurrent_invoice->getData("recurrence")."]]></recurrence>\n";
			    $xml .= "    <recurrencetype><![CDATA[".$do_recurrent_invoice->getData("recurrencetype")."]]></recurrencetype>\n";
			    $xml .= "   </recurrentinvoice>\n";
			  }
			  $do_recurrent_invoice->free();

			  $do_paymentlog = new PaymentLog();
			  $do_paymentlog->getPaymentLogDetails($do_invoice->getData("idinvoice"));
			  while($do_paymentlog->next()) {
			    $xml .= "   <paymentlog>\n";
			    $xml .= "    <idpaymentlog ><![CDATA[".$do_paymentlog->getData("idpaymentlog")."]]></idpaymentlog >\n";
			    $xml .= "    <timestamp><![CDATA[".$do_paymentlog->getData("timestamp")."]]></timestamp>\n";
			    $xml .= "    <idinvoice><![CDATA[".$do_paymentlog->getData("idinvoice")."]]></idinvoice>\n";
			    $xml .= "    <amount><![CDATA[".$do_paymentlog->getData("amount")."]]></amount>\n";
			    $xml .= "    <payment_type><![CDATA[".$do_paymentlog->getData("payment_type")."]]></payment_type>\n";
			    $xml .= "    <ref_num><![CDATA[".$do_paymentlog->getData("ref_num")."]]></ref_num>\n";
			    $xml .= "    <date_added><![CDATA[".$do_paymentlog->getData("date_added")."]]></date_added>\n";

			    $do_payment_invoice = new PaymentInvoice();
			    $do_payment_invoice->getInvDetails($do_paymentlog->getData("idpaymentlog"));
			    while($do_payment_invoice->next()) {
			      $xml .= "    <payment_invoice>\n";
			      $xml .= "     <idpayment_invoice><![CDATA[".$do_payment_invoice->getData("idpayment_invoice")."]]></idpayment_invoice>\n";
			      $xml .= "     <idpayment><![CDATA[".$do_payment_invoice->getData("idpayment")."]]></idpayment>\n";
			      $xml .= "     <idinvoice><![CDATA[".$do_payment_invoice->getData("idinvoice")."]]></idinvoice>\n";
			      $xml .= "     <amount><![CDATA[".$do_payment_invoice->getData("amount")."]]></amount>\n";
			      $xml .= "    </payment_invoice>\n";
			    }
			    $do_payment_invoice->free();

			    $do_paymentlog_extra_amount = new PaymentLog();
			    $do_paymentlog_extra_amount->getPaymentLogExtraAmountDetails($do_paymentlog->getData("idpaymentlog"));
			    while($do_paymentlog_extra_amount->next()) {
			      $xml .= "    <paymentlog_extra_amount>\n";
			      $xml .= "     <idpaymentlog_extra_amount><![CDATA[".$do_paymentlog_extra_amount->getData("idpaymentlog_extra_amount")."]]></idpaymentlog_extra_amount>\n";
			      $xml .= "     <idpaymentlog><![CDATA[".$do_paymentlog_extra_amount->getData("idpaymentlog")."]]></idpaymentlog>\n";
			      $xml .= "     <extra_amt><![CDATA[".$do_paymentlog_extra_amount->getData("extra_amt")."]]></extra_amt>\n";
			      $xml .= "     <iduser><![CDATA[".$do_paymentlog_extra_amount->getData("iduser")."]]></iduser>\n";
			      $xml .= "    </paymentlog_extra_amount>\n";
			    }
			    $do_paymentlog_extra_amount->free();

			    $xml .= "   </paymentlog>\n";

			  }
			  $do_paymentlog->free();

			  $xml .= "  </invoice>\n";
			}
			$do_invoice->free();

			$xml .= " </contact>\n";

		      }

    $do_task = new Task();
    $do_task->getTasksWithoutProject();
    while($do_task->next()) {
      $xml .= " <task_without_project>\n";
      $xml .= "  <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
      $xml .= "  <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
      $xml .= "  <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
      $xml .= "  <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
      $xml .= "  <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
      $xml .= "  <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
      $xml .= "  <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
      $xml .= "  <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
      $xml .= "  <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
      $xml .= "  <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
      $xml .= "  <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
      $xml .= "  <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n";
      $xml .= " </task_without_project>\n";
    }
    $do_task->free();



/**
  *Company Exporting
  */
	$do_company = new Company();
	$do_company->getAllCompanies($_SESSION['do_User']->iduser);

	while($do_company->next()){
	 $xml .= " <companies>\n";
	 $xml .= " <idcompany><![CDATA[".$do_company->getData("idcompany")."]]></idcompany>\n";
 	 $xml .= " <name><![CDATA[".$do_company->getData("name")."]]></name>\n";
	 $xml .= " </companies>\n";
    }
      $do_company->free();





    $do_prj = new Project();
    $do_prj->getUserProjects();
    while($do_prj->next()) {
      $xml .= " <project>\n";
      $xml .= "  <idproject><![CDATA[".$do_prj->getData("idproject")."]]></idproject>\n";
      $xml .= "  <iduser><![CDATA[".$do_prj->getData("iduser")."]]></iduser>\n";
      $xml .= "  <name><![CDATA[".$do_prj->getData("name")."]]></name>\n";
      $xml .= "  <end_date_dateformat><![CDATA[".$do_prj->getData("end_date_dateformat")."]]></end_date_dateformat>\n";
      $xml .= "  <idcompany><![CDATA[".$do_prj->getData("idcompany")."]]></idcompany>\n";
      $xml .= "  <status><![CDATA[".$do_prj->getData("status")."]]></status>\n";
      $xml .= "  <effort_estimated_hrs><![CDATA[".$do_prj->getData("effort_estimated_hrs")."]]></effort_estimated_hrs>\n";
      $xml .= "  <is_public><![CDATA[".$do_prj->getData("is_public")."]]></is_public>\n\n";

      $do_task = new Task();
      $do_task->getTasksAssociatedWithProject($do_prj->getData("idproject"));
      while($do_task->next()) {
        $xml .= "   <project_task>\n";
        $xml .= "    <idproject_task><![CDATA[".$do_task->getData("idproject_task")."]]></idproject_task>\n";
        $xml .= "    <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "    <idproject><![CDATA[".$do_task->getData("idproject")."]]></idproject>\n";
        $xml .= "    <progress><![CDATA[".$do_task->getData("progress")."]]></progress>\n";
        $xml .= "    <drop_box_code><![CDATA[".$do_task->getData("drop_box_code")."]]></drop_box_code>\n";
        $xml .= "    <priority><![CDATA[".$do_task->getData("priority")."]]></priority>\n";
        $xml .= "    <hrs_work_expected><![CDATA[".$do_task->getData("hrs_work_expected")."]]></hrs_work_expected>\n";

        $xml .= "    <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "    <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "    <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "    <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "    <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "    <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "    <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "    <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "    <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "    <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "    <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n\n";

        $do_prj_discuss = new ProjectDiscuss();
        $do_prj_discuss->getProjectTaskDiscussions($do_task->getData("idproject_task"));
        while($do_prj_discuss->next()) {
          $xml .= "    <project_discuss>\n";
          $xml .= "     <idproject_discuss><![CDATA[".$do_prj_discuss->getData("idproject_discuss")."]]></idproject_discuss>\n";
          $xml .= "     <idproject_task><![CDATA[".$do_prj_discuss->getData("idproject_task")."]]></idproject_task>\n";
          $xml .= "     <idtask><![CDATA[".$do_prj_discuss->getData("idtask")."]]></idtask>\n";
          $xml .= "     <idproject><![CDATA[".$do_prj_discuss->getData("idproject")."]]></idproject>\n";
          $xml .= "     <discuss><![CDATA[".$do_prj_discuss->getData("discuss")."]]></discuss>\n";
          $xml .= "     <date_added><![CDATA[".$do_prj_discuss->getData("date_added")."]]></date_added>\n";
          $xml .= "     <document><![CDATA[".$do_prj_discuss->getData("document")."]]></document>\n";
          $xml .= "     <iduser><![CDATA[".$do_prj_discuss->getData("iduser")."]]></iduser>\n";
          $xml .= "     <drop_box_sender><![CDATA[".$do_prj_discuss->getData("drop_box_sender")."]]></drop_box_sender>\n";
          $xml .= "     <priority><![CDATA[".$do_prj_discuss->getData("priority")."]]></priority>\n";
          $xml .= "     <hours_work><![CDATA[".$do_prj_discuss->getData("hours_work")."]]></hours_work>\n";
          $xml .= "     <discuss_edit_access><![CDATA[".$do_prj_discuss->getData("discuss_edit_access")."]]></discuss_edit_access>\n";
          $xml .= "     <type><![CDATA[".$do_prj_discuss->getData("type")."]]></type>\n";
          $xml .= "    </project_discuss>\n";
       }
       $do_prj_discuss->free();
       $xml .= "   </project_task>\n";
      }
      $do_task->free();
      $xml .= " </project>\n";
    }
    $do_prj->free();

    $xml .= "</contacts>\n";

    $xml_file = "xml_export/".$_SESSION['do_User']->iduser."_contacts.xml";
    $handle_xml = fopen($xml_file, "w+");
    if (fwrite($handle_xml, $xml) === FALSE) {
      $_SESSION['in_page_message'] = "ofuz_export_xml_failure";
    } else {
      $_SESSION['in_page_message'] = "ofuz_export_xml_success";
    }

    fclose($handle_xml);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($xml_file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($xml_file));
    ob_clean();
    flush();
    readfile($xml_file);

  }

  /**
   * Exports an User's account.
   * @param int : $iduser
   * @return void
   */
  public function exportUserAccount($iduser) {

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
    $do_user = new User();
    $do_user->getId($iduser);

    $xml .= "<user>\n";
    $xml .= " <iduser><![CDATA[".$do_user->iduser."]]></iduser>\n";
    $xml .= " <firstname><![CDATA[".$do_user->firstname."]]></firstname>\n";
    $xml .= " <middlename><![CDATA[".$do_user->middlename."]]></middlename>\n";
    $xml .= " <lastname><![CDATA[".$do_user->lastname."]]></lastname>\n";
    $xml .= " <email><![CDATA[".$do_user->email."]]></email>\n";
    $xml .= " <phone><![CDATA[".$do_user->phone."]]></phone>\n";
    $xml .= " <company><![CDATA[".$do_user->company."]]></company>\n";
    $xml .= " <position><![CDATA[".$do_user->position."]]></position>\n";
    $xml .= " <address1><![CDATA[".$do_user->address1."]]></address1>\n";
    $xml .= " <address2><![CDATA[".$do_user->address2."]]></address2>\n";
    $xml .= " <city><![CDATA[".$do_user->city."]]></city>\n";
    $xml .= " <zip><![CDATA[".$do_user->zip."]]></zip>\n";
    $xml .= " <state><![CDATA[".$do_user->state."]]></state>\n";
    $xml .= " <country><![CDATA[".$do_user->country."]]></country>\n";
    $xml .= " <username><![CDATA[".$do_user->username."]]></username>\n";
    $xml .= " <password><![CDATA[".$do_user->password."]]></password>\n";
    $xml .= " <isadmin><![CDATA[".$do_user->isadmin."]]></isadmin>\n";
    $xml .= " <regdate><![CDATA[".$do_user->regdate."]]></regdate>\n";
    $xml .= " <openid><![CDATA[".$do_user->openid."]]></openid>\n";
    $xml .= " <last_login><![CDATA[".$do_user->last_login."]]></last_login>\n";
    $xml .= " <drop_box_code><![CDATA[".$do_user->drop_box_code."]]></drop_box_code>\n";
    $xml .= " <idcontact><![CDATA[".$do_user->idcontact."]]></idcontact>\n";
    $xml .= " <fb_user_id><![CDATA[".$do_user->fb_user_id."]]></fb_user_id>\n";
    $xml .= " <api_key><![CDATA[".$do_user->api_key."]]></api_key>\n";
    $xml .= " <plan><![CDATA[".$do_user->plan."]]></plan>\n";
    $xml .= " <status><![CDATA[".$do_user->status."]]></status>\n";
    $xml .= " <google_openid_identity><![CDATA[".$do_user->google_openid_identity."]]></google_openid_identity>\n";
    $xml .= "</user>\n";

    $do_contact = new Contact();
    $do_contact->getUserContacts($iduser);

    $xml .= "<contacts>\n";

    while($do_contact->next()) {

      $xml .= " <contact>\n";

      $xml .= "  <idcontact><![CDATA[".$do_contact->getData("idcontact")."]]></idcontact>\n";
      $xml .= "  <firstname><![CDATA[".$do_contact->getData("firstname")."]]></firstname>\n";
      $xml .= "  <lastname><![CDATA[".$do_contact->getData("lastname")."]]></lastname>\n";
      $xml .= "  <position><![CDATA[".$do_contact->getData("position")."]]></position>\n";
      $xml .= "  <company><![CDATA[".$do_contact->getData("company")."]]></company>\n";
      $xml .= "  <idcompany><![CDATA[".$do_contact->getData("idcompany")."]]></idcompany>\n";
      $xml .= "  <iduser><![CDATA[".$do_contact->getData("iduser")."]]></iduser>\n";
      $xml .= "  <picture><![CDATA[".$do_contact->getData("picture")."]]></picture>\n";
      $xml .= "  <summary><![CDATA[".$do_contact->getData("summary")."]]></summary>\n";
      $xml .= "  <birthday><![CDATA[".$do_contact->getData("birthday")."]]></birthday>\n";
      $xml .= "  <portal_code><![CDATA[".$do_contact->getData("portal_code")."]]></portal_code>\n";
      $xml .= "  <fb_userid><![CDATA[".$do_contact->getData("fb_userid")."]]></fb_userid>\n";
      $xml .= "  <tw_user_id><![CDATA[".$do_contact->getData("tw_user_id")."]]></tw_user_id>\n";
      $xml .= "  <email_optout><![CDATA[".$do_contact->getData("email_optout")."]]></email_optout>\n\n";

      $contact_address = $do_contact->getChildContactAddress();

      while($contact_address->next()) {
        $xml .= "  <contact_address>\n";
        $xml .= "   <idcontact_address><![CDATA[".$contact_address->idcontact_address."]]></idcontact_address>\n";
        $xml .= "   <city><![CDATA[".$contact_address->city."]]></city>\n";
        $xml .= "   <country><![CDATA[".$contact_address->country."]]></country>\n";
        $xml .= "   <state><![CDATA[".$contact_address->state."]]></state>\n";
        $xml .= "   <street><![CDATA[".$contact_address->street."]]></street>\n";
        $xml .= "   <zipcode><![CDATA[".$contact_address->zipcode."]]></zipcode>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_address->idcontact."]]></idcontact>\n";
        $xml .= "   <address><![CDATA[".$contact_address->address."]]></address>\n";
        $xml .= "   <address_type><![CDATA[".$contact_address->address_type."]]></address_type>\n";
        $xml .= "  </contact_address>\n";
      }

      $contact_email = $do_contact->getChildContactEmail();

      while($contact_email->next()) {
        $xml .= "  <contact_email>\n";
        $xml .= "   <idcontact_email><![CDATA[".$contact_email->idcontact_email."]]></idcontact_email>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_email->idcontact."]]></idcontact>\n";
        $xml .= "   <email_address><![CDATA[".$contact_email->email_address."]]></email_address>\n";
        $xml .= "   <email_type><![CDATA[".$contact_email->email_type."]]></email_type>\n";
        $xml .= "   <email_isdefault><![CDATA[".$contact_email->email_isdefault."]]></email_isdefault>\n";
        $xml .= "  </contact_email>\n";
      }

      $contact_phone = $do_contact->getChildContactPhone();

      while($contact_phone->next()) {
        $xml .= "  <contact_phone>\n";
        $xml .= "   <idcontact_phone><![CDATA[".$contact_phone->idcontact_phone."]]></idcontact_phone>\n";
        $xml .= "   <phone_number><![CDATA[".$contact_phone->phone_number."]]></phone_number>\n";
        $xml .= "   <phone_type><![CDATA[".$contact_phone->phone_type."]]></phone_type>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_phone->email_type."]]></idcontact>\n";
        $xml .= "  </contact_phone>\n";
      }

      $contact_note = $do_contact->getChildContactNotes();

      while($contact_note->next()) {
        $xml .= "  <contact_note>\n";
        $xml .= "   <idcontact_note><![CDATA[".$contact_note->idcontact_note."]]></idcontact_note>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_note->idcontact."]]></idcontact>\n";
        $xml .= "   <note><![CDATA[".$contact_note->note."]]></note>\n";
        $xml .= "   <date_added><![CDATA[".$contact_note->date_added."]]></date_added>\n";
        $xml .= "   <document><![CDATA[".$contact_note->document."]]></document>\n";
        $xml .= "   <idcompany><![CDATA[".$contact_note->idcompany."]]></idcompany>\n";
        $xml .= "   <iduser><![CDATA[".$contact_note->iduser."]]></iduser>\n";
        $xml .= "   <priority><![CDATA[".$contact_note->priority."]]></priority>\n";
        $xml .= "   <send_email><![CDATA[".$contact_note->send_email."]]></send_email>\n";
        $xml .= "   <hours_work><![CDATA[".$contact_note->hours_work."]]></hours_work>\n";
        $xml .= "   <note_visibility><![CDATA[".$contact_note->note_visibility."]]></note_visibility>\n";
        $xml .= "  </contact_note>\n";
      }

      $do_task = new Task();
      $do_task->getContactTaskWithoutProject($do_contact->getData("idcontact"));
      while($do_task->next()) {
        $xml .= "  <contact_task>\n";
        $xml .= "   <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "   <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "   <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "   <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "   <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "   <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "   <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "   <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "   <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "   <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "   <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "   <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n";
        $xml .= "  </contact_task>\n";
      }
      $do_task->free();

/**
 * For all the contact tasks which are associated with Project.
 */

      $do_task = new Task();
      $do_task->getContactTasksAssociatedWithProject($do_contact->getData("idcontact"));
      while($do_task->next()) {
        $do_project = new Project();
        $do_project->getId($do_task->idproject);

        $xml .= "  <contact_task_with_project>\n";
        $xml .= "   <idproject><![CDATA[".$do_project->idproject."]]></idproject>\n";
        $xml .= "   <iduser><![CDATA[".$do_project->iduser."]]></iduser>\n";
        $xml .= "   <name><![CDATA[".$do_project->name."]]></name>\n";
        $xml .= "   <end_date_dateformat><![CDATA[".$do_project->end_date_dateformat."]]></end_date_dateformat>\n";
        $xml .= "   <idcompany><![CDATA[".$do_project->idcompany."]]></idcompany>\n";
        $xml .= "   <status><![CDATA[".$do_project->status."]]></status>\n";
        $xml .= "   <effort_estimated_hrs><![CDATA[".$do_project->effort_estimated_hrs."]]></effort_estimated_hrs>\n";
        $xml .= "   <is_public><![CDATA[".$do_project->is_public."]]></is_public>\n\n";

        $xml .= "   <project_task>\n";
        $xml .= "    <idproject_task><![CDATA[".$do_task->getData("idproject_task")."]]></idproject_task>\n";
        $xml .= "    <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "    <idproject><![CDATA[".$do_task->getData("idproject")."]]></idproject>\n";
        $xml .= "    <progress><![CDATA[".$do_task->getData("progress")."]]></progress>\n";
        $xml .= "    <drop_box_code><![CDATA[".$do_task->getData("drop_box_code")."]]></drop_box_code>\n";
        $xml .= "    <priority><![CDATA[".$do_task->getData("priority")."]]></priority>\n";
        $xml .= "    <hrs_work_expected><![CDATA[".$do_task->getData("hrs_work_expected")."]]></hrs_work_expected>\n";

        $xml .= "    <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "    <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "    <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "    <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "    <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "    <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "    <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "    <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "    <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "    <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "    <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n\n";

        $do_prj_discuss = new ProjectDiscuss();
        $do_prj_discuss->getProjectTaskDiscussions($do_task->getData("idproject_task"));
        while($do_prj_discuss->next()) {
          $xml .= "    <project_discuss>\n";
          $xml .= "     <idproject_discuss><![CDATA[".$do_prj_discuss->getData("idproject_discuss")."]]></idproject_discuss>\n";
          $xml .= "     <idproject_task><![CDATA[".$do_prj_discuss->getData("idproject_task")."]]></idproject_task>\n";
          $xml .= "     <idtask><![CDATA[".$do_prj_discuss->getData("idtask")."]]></idtask>\n";
          $xml .= "     <idproject><![CDATA[".$do_prj_discuss->getData("idproject")."]]></idproject>\n";
          $xml .= "     <discuss><![CDATA[".$do_prj_discuss->getData("discuss")."]]></discuss>\n";
          $xml .= "     <date_added><![CDATA[".$do_prj_discuss->getData("date_added")."]]></date_added>\n";
          $xml .= "     <document><![CDATA[".$do_prj_discuss->getData("document")."]]></document>\n";
          $xml .= "     <iduser><![CDATA[".$do_prj_discuss->getData("iduser")."]]></iduser>\n";
          $xml .= "     <drop_box_sender><![CDATA[".$do_prj_discuss->getData("drop_box_sender")."]]></drop_box_sender>\n";
          $xml .= "     <priority><![CDATA[".$do_prj_discuss->getData("priority")."]]></priority>\n";
          $xml .= "     <hours_work><![CDATA[".$do_prj_discuss->getData("hours_work")."]]></hours_work>\n";
          $xml .= "     <discuss_edit_access><![CDATA[".$do_prj_discuss->getData("discuss_edit_access")."]]></discuss_edit_access>\n";
          $xml .= "    </project_discuss>\n";
        }
        $do_prj_discuss->free();

        $xml .= "   </project_task>\n";

        $xml .= "  </contact_task_with_project>\n";

        $do_project->free();
      }
      $do_task->free();

      $do_invoice = new Invoice();
      $do_invoice->getContactInvoiceDetails($do_contact->getData("idcontact"));
      while($do_invoice->next()) {
        $xml .= "  <invoice>\n";
        $xml .= "   <idinvoice><![CDATA[".$do_invoice->getData("idinvoice")."]]></idinvoice>\n";
        $xml .= "   <num><![CDATA[".$do_invoice->getData("num")."]]></num>\n";
        $xml .= "   <iduser><![CDATA[".$do_invoice->getData("iduser")."]]></iduser>\n";
        $xml .= "   <description><![CDATA[".$do_invoice->getData("description")."]]></description>\n";
        $xml .= "   <amount><![CDATA[".$do_invoice->getData("amount")."]]></amount>\n";
        $xml .= "   <datepaid><![CDATA[".$do_invoice->getData("datepaid")."]]></datepaid>\n";
        $xml .= "   <datecreated><![CDATA[".$do_invoice->getData("datecreated")."]]></datecreated>\n";
        $xml .= "   <status><![CDATA[".$do_invoice->getData("status")."]]></status>\n";
        $xml .= "   <discount><![CDATA[".$do_invoice->getData("discount")."]]></discount>\n";
        $xml .= "   <idcontact><![CDATA[".$do_invoice->getData("idcontact")."]]></idcontact>\n";
        $xml .= "   <due_date><![CDATA[".$do_invoice->getData("due_date")."]]></due_date>\n";
        $xml .= "   <invoice_address><![CDATA[".$do_invoice->getData("invoice_address")."]]></invoice_address>\n";
        $xml .= "   <invoice_term><![CDATA[".$do_invoice->getData("invoice_term")."]]></invoice_term>\n";
        $xml .= "   <invoice_note><![CDATA[".$do_invoice->getData("invoice_note")."]]></invoice_note>\n";
        $xml .= "   <sub_total><![CDATA[".$do_invoice->getData("sub_total")."]]></sub_total>\n";
        $xml .= "   <net_total><![CDATA[".$do_invoice->getData("net_total")."]]></net_total>\n";
        $xml .= "   <amt_due><![CDATA[".$do_invoice->getData("amt_due")."]]></amt_due>\n";
        $xml .= "   <idcompany><![CDATA[".$do_invoice->getData("idcompany")."]]></idcompany>\n";
        $xml .= "   <tax><![CDATA[".$do_invoice->getData("tax")."]]></tax>\n";
        $xml .= "   <set_delete><![CDATA[".$do_invoice->getData("set_delete")."]]></set_delete>\n";
        $xml .= "   <total_discounted_amt><![CDATA[".$do_invoice->getData("total_discounted_amt")."]]></total_discounted_amt>\n";
        $xml .= "   <total_taxed_amount><![CDATA[".$do_invoice->getData("total_taxed_amount")."]]></total_taxed_amount>\n";

        $invoice_line = $do_invoice->getChildInvoiceLine();
        while($invoice_line->next()) {
          $xml .= "   <invoiceline>\n";
          $xml .= "    <idinvoiceline><![CDATA[".$invoice_line->idinvoiceline."]]></idinvoiceline>\n";
          $xml .= "    <idinvoice><![CDATA[".$invoice_line->idinvoice."]]></idinvoice>\n";
          $xml .= "    <description><![CDATA[".$invoice_line->description."]]></description>\n";
          $xml .= "    <price><![CDATA[".$invoice_line->price."]]></price>\n";
          $xml .= "    <qty><![CDATA[".$invoice_line->qty."]]></qty>\n";
          $xml .= "    <total><![CDATA[".$invoice_line->total."]]></total>\n";
          $xml .= "    <item><![CDATA[".$invoice_line->item."]]></item>\n";
          $xml .= "    <line_tax><![CDATA[".$invoice_line->line_tax."]]></line_tax>\n";
          $xml .= "    <discounted_amount><![CDATA[".$invoice_line->discounted_amount."]]></discounted_amount>\n";
          $xml .= "    <taxed_amount><![CDATA[".$invoice_line->taxed_amount."]]></taxed_amount>\n";
          $xml .= "   </invoiceline>\n";
        }
        $invoice_line->free();

        $do_recurrent_invoice = new RecurrentInvoice();
        $do_recurrent_invoice->getRecurrentInvoiceDetail($do_invoice->getData("idinvoice"));
        while($do_recurrent_invoice->next()) {
          $xml .= "   <recurrentinvoice>\n";
          $xml .= "    <idrecurrentinvoice><![CDATA[".$do_recurrent_invoice->getData("idrecurrentinvoice")."]]></idrecurrentinvoice>\n";
          $xml .= "    <iduser><![CDATA[".$do_recurrent_invoice->getData("iduser")."]]></iduser>\n";
          $xml .= "    <idinvoice><![CDATA[".$do_recurrent_invoice->getData("idinvoice")."]]></idinvoice>\n";
          $xml .= "    <nextdate><![CDATA[".$do_recurrent_invoice->getData("nextdate")."]]></nextdate>\n";
          $xml .= "    <recurrence><![CDATA[".$do_recurrent_invoice->getData("recurrence")."]]></recurrence>\n";
          $xml .= "    <recurrencetype><![CDATA[".$do_recurrent_invoice->getData("recurrencetype")."]]></recurrencetype>\n";
          $xml .= "   </recurrentinvoice>\n";
        }
        $do_recurrent_invoice->free();

        $do_paymentlog = new PaymentLog();
        $do_paymentlog->getPaymentLogDetails($do_invoice->getData("idinvoice"));
        while($do_paymentlog->next()) {
          $xml .= "   <paymentlog>\n";
          $xml .= "    <idpaymentlog ><![CDATA[".$do_paymentlog->getData("idpaymentlog")."]]></idpaymentlog >\n";
          $xml .= "    <timestamp><![CDATA[".$do_paymentlog->getData("timestamp")."]]></timestamp>\n";
          $xml .= "    <idinvoice><![CDATA[".$do_paymentlog->getData("idinvoice")."]]></idinvoice>\n";
          $xml .= "    <amount><![CDATA[".$do_paymentlog->getData("amount")."]]></amount>\n";
          $xml .= "    <payment_type><![CDATA[".$do_paymentlog->getData("payment_type")."]]></payment_type>\n";
          $xml .= "    <ref_num><![CDATA[".$do_paymentlog->getData("ref_num")."]]></ref_num>\n";
          $xml .= "    <date_added><![CDATA[".$do_paymentlog->getData("date_added")."]]></date_added>\n";

          $do_payment_invoice = new PaymentInvoice();
          $do_payment_invoice->getInvDetails($do_paymentlog->getData("idpaymentlog"));
          while($do_payment_invoice->next()) {
            $xml .= "    <payment_invoice>\n";
            $xml .= "     <idpayment_invoice><![CDATA[".$do_payment_invoice->getData("idpayment_invoice")."]]></idpayment_invoice>\n";
            $xml .= "     <idpayment><![CDATA[".$do_payment_invoice->getData("idpayment")."]]></idpayment>\n";
            $xml .= "     <idinvoice><![CDATA[".$do_payment_invoice->getData("idinvoice")."]]></idinvoice>\n";
            $xml .= "     <amount><![CDATA[".$do_payment_invoice->getData("amount")."]]></amount>\n";
            $xml .= "    </payment_invoice>\n";
          }
          $do_payment_invoice->free();

          $do_paymentlog_extra_amount = new PaymentLog();
          $do_paymentlog_extra_amount->getPaymentLogExtraAmountDetails($do_paymentlog->getData("idpaymentlog"));
          while($do_paymentlog_extra_amount->next()) {
            $xml .= "    <paymentlog_extra_amount>\n";
            $xml .= "     <idpaymentlog_extra_amount><![CDATA[".$do_paymentlog_extra_amount->getData("idpaymentlog_extra_amount")."]]></idpaymentlog_extra_amount>\n";
            $xml .= "     <idpaymentlog><![CDATA[".$do_paymentlog_extra_amount->getData("idpaymentlog")."]]></idpaymentlog>\n";
            $xml .= "     <extra_amt><![CDATA[".$do_paymentlog_extra_amount->getData("extra_amt")."]]></extra_amt>\n";
            $xml .= "     <iduser><![CDATA[".$do_paymentlog_extra_amount->getData("iduser")."]]></iduser>\n";
            $xml .= "    </paymentlog_extra_amount>\n";
          }
          $do_paymentlog_extra_amount->free();

          $xml .= "   </paymentlog>\n";

        }
        $do_paymentlog->free();

        $xml .= "  </invoice>\n";
      }
      $do_invoice->free();

      $do_tag = new Tag();
      $do_tag->getUserContactTags($iduser,$do_contact->getData("idcontact"));
      while($do_tag->next()) {
        $xml .= "  <tag>\n";
        $xml .= "   <idtag><![CDATA[".$do_tag->idtag."]]></idtag>\n";
        $xml .= "   <tag_name><![CDATA[".$do_tag->tag_name."]]></tag_name>\n";
        $xml .= "   <iduser><![CDATA[".$do_tag->iduser."]]></iduser>\n";
        $xml .= "   <reference_type><![CDATA[".$do_tag->reference_type."]]></reference_type>\n";
        $xml .= "   <idreference><![CDATA[".$do_tag->idreference."]]></idreference>\n";
        $xml .= "   <date_added><![CDATA[".$do_tag->date_added."]]></date_added>\n";
        $xml .= "  </tag>\n";
      }
     $do_tag->free();
 
      $xml .= " </contact>\n";

    }

    $xml .= "</contacts>\n";

    $xml_file = XML_EXPORT."adm_".$iduser."_account_bkp.xml";
    $handle_xml = fopen($xml_file, "w+");
    if (fwrite($handle_xml, $xml) === FALSE) {
      //$_SESSION['in_page_message'] = "ofuz_export_xml_failure";
      echo 'could not write'; exit();
    } else {
      $_SESSION['in_page_message'] = "ofuz_export_xml_success";
    }

    fclose($handle_xml);

  }




/**
   * Back up Inactive users data from all the fields and Delete Inactive User's account.
   * This function is used to generate the xml back up file for the user information from the DB
   * This function is differnt from exportUserAccount as we have added more table to the backup
   * And we have function to delete the table once back up is done. And the function exportUserAccount  
   * was not disturbed as this was already in use. 
   * @param int : $iduser
   * @return void
   */
  public function exportUserAccountandDelete($iduser) {

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\n";
    $do_user = new User();
    $do_user->getId($iduser);

    $xml .= "<user>\n";
    $xml .= " <iduser><![CDATA[".$do_user->iduser."]]></iduser>\n";
    $xml .= " <firstname><![CDATA[".$do_user->firstname."]]></firstname>\n";
    $xml .= " <middlename><![CDATA[".$do_user->middlename."]]></middlename>\n";
    $xml .= " <lastname><![CDATA[".$do_user->lastname."]]></lastname>\n";
    $xml .= " <email><![CDATA[".$do_user->email."]]></email>\n";
    $xml .= " <phone><![CDATA[".$do_user->phone."]]></phone>\n";
    $xml .= " <company><![CDATA[".$do_user->company."]]></company>\n";
    $xml .= " <position><![CDATA[".$do_user->position."]]></position>\n";
    $xml .= " <address1><![CDATA[".$do_user->address1."]]></address1>\n";
    $xml .= " <address2><![CDATA[".$do_user->address2."]]></address2>\n";
    $xml .= " <city><![CDATA[".$do_user->city."]]></city>\n";
    $xml .= " <zip><![CDATA[".$do_user->zip."]]></zip>\n";
    $xml .= " <state><![CDATA[".$do_user->state."]]></state>\n";
    $xml .= " <country><![CDATA[".$do_user->country."]]></country>\n";
    $xml .= " <username><![CDATA[".$do_user->username."]]></username>\n";
    $xml .= " <password><![CDATA[".$do_user->password."]]></password>\n";
    $xml .= " <isadmin><![CDATA[".$do_user->isadmin."]]></isadmin>\n";
    $xml .= " <regdate><![CDATA[".$do_user->regdate."]]></regdate>\n";
    $xml .= " <openid><![CDATA[".$do_user->openid."]]></openid>\n";
    $xml .= " <last_login><![CDATA[".$do_user->last_login."]]></last_login>\n";
    $xml .= " <drop_box_code><![CDATA[".$do_user->drop_box_code."]]></drop_box_code>\n";
    $xml .= " <idcontact><![CDATA[".$do_user->idcontact."]]></idcontact>\n";
    $xml .= " <fb_user_id><![CDATA[".$do_user->fb_user_id."]]></fb_user_id>\n";
    $xml .= " <api_key><![CDATA[".$do_user->api_key."]]></api_key>\n";
    $xml .= " <plan><![CDATA[".$do_user->plan."]]></plan>\n";
    $xml .= " <status><![CDATA[".$do_user->status."]]></status>\n";
    $xml .= " <google_openid_identity><![CDATA[".$do_user->google_openid_identity."]]></google_openid_identity>\n";
    $xml .= "</user>\n";

	$i=1;
	//$tbl_name = "userid".$iduser."_contact";
    $do_contact = new Contact(NULL,NULL,$iduser);
    $do_contact->getUserContacts($iduser);
	$nums = $do_contact->GetNumRows();

	if($nums >= 1){
    $xml .= "<contacts>\n";

    while($do_contact->next()) {

      $xml .= " <contact>\n";

      $xml .= "  <idcontact><![CDATA[".$do_contact->getData("idcontact")."]]></idcontact>\n";
      $xml .= "  <firstname><![CDATA[".$do_contact->getData("firstname")."]]></firstname>\n";
      $xml .= "  <lastname><![CDATA[".$do_contact->getData("lastname")."]]></lastname>\n";
      $xml .= "  <position><![CDATA[".$do_contact->getData("position")."]]></position>\n";
      $xml .= "  <company><![CDATA[".$do_contact->getData("company")."]]></company>\n";
      $xml .= "  <idcompany><![CDATA[".$do_contact->getData("idcompany")."]]></idcompany>\n";
      $xml .= "  <iduser><![CDATA[".$do_contact->getData("iduser")."]]></iduser>\n";
      $xml .= "  <picture><![CDATA[".$do_contact->getData("picture")."]]></picture>\n";
      $xml .= "  <summary><![CDATA[".$do_contact->getData("summary")."]]></summary>\n";
      $xml .= "  <birthday><![CDATA[".$do_contact->getData("birthday")."]]></birthday>\n";
      $xml .= "  <portal_code><![CDATA[".$do_contact->getData("portal_code")."]]></portal_code>\n";
      $xml .= "  <fb_userid><![CDATA[".$do_contact->getData("fb_userid")."]]></fb_userid>\n";
      $xml .= "  <tw_user_id><![CDATA[".$do_contact->getData("tw_user_id")."]]></tw_user_id>\n";
      $xml .= "  <email_optout><![CDATA[".$do_contact->getData("email_optout")."]]></email_optout>\n\n";

      $contact_address = $do_contact->getChildContactAddress();

      while($contact_address->next()) {
        $xml .= "  <contact_address>\n";
        $xml .= "   <idcontact_address><![CDATA[".$contact_address->idcontact_address."]]></idcontact_address>\n";
        $xml .= "   <city><![CDATA[".$contact_address->city."]]></city>\n";
        $xml .= "   <country><![CDATA[".$contact_address->country."]]></country>\n";
        $xml .= "   <state><![CDATA[".$contact_address->state."]]></state>\n";
        $xml .= "   <street><![CDATA[".$contact_address->street."]]></street>\n";
        $xml .= "   <zipcode><![CDATA[".$contact_address->zipcode."]]></zipcode>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_address->idcontact."]]></idcontact>\n";
        $xml .= "   <address><![CDATA[".$contact_address->address."]]></address>\n";
        $xml .= "   <address_type><![CDATA[".$contact_address->address_type."]]></address_type>\n";
        $xml .= "  </contact_address>\n";
      }

      $contact_email = $do_contact->getChildContactEmail();

      while($contact_email->next()) {
        $xml .= "  <contact_email>\n";
        $xml .= "   <idcontact_email><![CDATA[".$contact_email->idcontact_email."]]></idcontact_email>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_email->idcontact."]]></idcontact>\n";
        $xml .= "   <email_address><![CDATA[".$contact_email->email_address."]]></email_address>\n";
        $xml .= "   <email_type><![CDATA[".$contact_email->email_type."]]></email_type>\n";
        $xml .= "   <email_isdefault><![CDATA[".$contact_email->email_isdefault."]]></email_isdefault>\n";
        $xml .= "  </contact_email>\n";
      }

      $contact_phone = $do_contact->getChildContactPhone();

      while($contact_phone->next()) {
        $xml .= "  <contact_phone>\n";
        $xml .= "   <idcontact_phone><![CDATA[".$contact_phone->idcontact_phone."]]></idcontact_phone>\n";
        $xml .= "   <phone_number><![CDATA[".$contact_phone->phone_number."]]></phone_number>\n";
        $xml .= "   <phone_type><![CDATA[".$contact_phone->phone_type."]]></phone_type>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_phone->email_type."]]></idcontact>\n";
        $xml .= "  </contact_phone>\n";
      }

      $contact_note = $do_contact->getChildContactNotes();

      while($contact_note->next()) {
        $xml .= "  <contact_note>\n";
        $xml .= "   <idcontact_note><![CDATA[".$contact_note->idcontact_note."]]></idcontact_note>\n";
        $xml .= "   <idcontact><![CDATA[".$contact_note->idcontact."]]></idcontact>\n";
        $xml .= "   <note><![CDATA[".$contact_note->note."]]></note>\n";
        $xml .= "   <date_added><![CDATA[".$contact_note->date_added."]]></date_added>\n";
        $xml .= "   <document><![CDATA[".$contact_note->document."]]></document>\n";
        $xml .= "   <idcompany><![CDATA[".$contact_note->idcompany."]]></idcompany>\n";
        $xml .= "   <iduser><![CDATA[".$contact_note->iduser."]]></iduser>\n";
        $xml .= "   <priority><![CDATA[".$contact_note->priority."]]></priority>\n";
        $xml .= "   <send_email><![CDATA[".$contact_note->send_email."]]></send_email>\n";
        $xml .= "   <hours_work><![CDATA[".$contact_note->hours_work."]]></hours_work>\n";
        $xml .= "   <note_visibility><![CDATA[".$contact_note->note_visibility."]]></note_visibility>\n";
        $xml .= "  </contact_note>\n";
      }
	 
	  if($i == '1') {
      $do_task = new Task();
      $do_task->getContactTaskWithoutProjectAndWithUser($iduser);
      while($do_task->next()) {
        $xml .= "  <contact_task>\n";
        $xml .= "   <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "   <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "   <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "   <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "   <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "   <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "   <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "   <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "   <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "   <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "   <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "   <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n";
        $xml .= "  </contact_task>\n";
      }
      $do_task->free();
      }
	  
	  $do_task = new Task();
      $do_task->getContactTaskWithoutProjectAndWithContact($do_contact->getData("idcontact"));
      while($do_task->next()) {
        $xml .= "  <contact_task>\n";
        $xml .= "   <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "   <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "   <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "   <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "   <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "   <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "   <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "   <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "   <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "   <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "   <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "   <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n";
        $xml .= "  </contact_task>\n";
      }
      $do_task->free();
/**
 * For all the contact tasks which are associated with Project.
 */

      $do_task = new Task();
      //$do_task->getContactTasksAssociatedWithProject($do_contact->getData("idcontact"));
       $do_task->getContactTasksAssociatedWithProjectWithContact($do_contact->getData("idcontact"));
      while($do_task->next()) {
        $do_project = new Project();
        $do_project->getId($do_task->idproject);

        $xml .= "  <contact_task_with_project>\n";
        $xml .= "   <idproject><![CDATA[".$do_project->idproject."]]></idproject>\n";
        $xml .= "   <iduser><![CDATA[".$do_project->iduser."]]></iduser>\n";
        $xml .= "   <name><![CDATA[".$do_project->name."]]></name>\n";
        $xml .= "   <end_date_dateformat><![CDATA[".$do_project->end_date_dateformat."]]></end_date_dateformat>\n";
        $xml .= "   <idcompany><![CDATA[".$do_project->idcompany."]]></idcompany>\n";
        $xml .= "   <status><![CDATA[".$do_project->status."]]></status>\n";
        $xml .= "   <effort_estimated_hrs><![CDATA[".$do_project->effort_estimated_hrs."]]></effort_estimated_hrs>\n";
        $xml .= "   <is_public><![CDATA[".$do_project->is_public."]]></is_public>\n\n";

        $xml .= "   <project_task>\n";
        $xml .= "    <idproject_task><![CDATA[".$do_task->getData("idproject_task")."]]></idproject_task>\n";
        $xml .= "    <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "    <idproject><![CDATA[".$do_task->getData("idproject")."]]></idproject>\n";
        $xml .= "    <progress><![CDATA[".$do_task->getData("progress")."]]></progress>\n";
        $xml .= "    <drop_box_code><![CDATA[".$do_task->getData("drop_box_code")."]]></drop_box_code>\n";
        $xml .= "    <priority><![CDATA[".$do_task->getData("priority")."]]></priority>\n";
        $xml .= "    <hrs_work_expected><![CDATA[".$do_task->getData("hrs_work_expected")."]]></hrs_work_expected>\n";

        $xml .= "    <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "    <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "    <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "    <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "    <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "    <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "    <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "    <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "    <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "    <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "    <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n\n";

        $do_prj_discuss = new ProjectDiscuss();
        $do_prj_discuss->getProjectTaskDiscussions($do_task->getData("idproject_task"));
        while($do_prj_discuss->next()) {
          $xml .= "    <project_discuss>\n";
          $xml .= "     <idproject_discuss><![CDATA[".$do_prj_discuss->getData("idproject_discuss")."]]></idproject_discuss>\n";
          $xml .= "     <idproject_task><![CDATA[".$do_prj_discuss->getData("idproject_task")."]]></idproject_task>\n";
          $xml .= "     <idtask><![CDATA[".$do_prj_discuss->getData("idtask")."]]></idtask>\n";
          $xml .= "     <idproject><![CDATA[".$do_prj_discuss->getData("idproject")."]]></idproject>\n";
          $xml .= "     <discuss><![CDATA[".$do_prj_discuss->getData("discuss")."]]></discuss>\n";
          $xml .= "     <date_added><![CDATA[".$do_prj_discuss->getData("date_added")."]]></date_added>\n";
          $xml .= "     <document><![CDATA[".$do_prj_discuss->getData("document")."]]></document>\n";
          $xml .= "     <iduser><![CDATA[".$do_prj_discuss->getData("iduser")."]]></iduser>\n";
          $xml .= "     <drop_box_sender><![CDATA[".$do_prj_discuss->getData("drop_box_sender")."]]></drop_box_sender>\n";
          $xml .= "     <priority><![CDATA[".$do_prj_discuss->getData("priority")."]]></priority>\n";
          $xml .= "     <hours_work><![CDATA[".$do_prj_discuss->getData("hours_work")."]]></hours_work>\n";
          $xml .= "     <discuss_edit_access><![CDATA[".$do_prj_discuss->getData("discuss_edit_access")."]]></discuss_edit_access>\n";
          $xml .= "    </project_discuss>\n";
        }
        $do_prj_discuss->free();

        $xml .= "   </project_task>\n";

        $xml .= "  </contact_task_with_project>\n";

        $do_project->free();
      }
      $do_task->free();
	  
	   if($i == '1') {
	   $do_task = new Task();
       $do_task->getContactTasksAssociatedWithProjectWithUser($iduser);
       while($do_task->next()) {
        $do_project = new Project();
        $do_project->getId($do_task->idproject);

        $xml .= "  <contact_task_with_project>\n";
        $xml .= "   <idproject><![CDATA[".$do_project->idproject."]]></idproject>\n";
        $xml .= "   <iduser><![CDATA[".$do_project->iduser."]]></iduser>\n";
        $xml .= "   <name><![CDATA[".$do_project->name."]]></name>\n";
        $xml .= "   <end_date_dateformat><![CDATA[".$do_project->end_date_dateformat."]]></end_date_dateformat>\n";
        $xml .= "   <idcompany><![CDATA[".$do_project->idcompany."]]></idcompany>\n";
        $xml .= "   <status><![CDATA[".$do_project->status."]]></status>\n";
        $xml .= "   <effort_estimated_hrs><![CDATA[".$do_project->effort_estimated_hrs."]]></effort_estimated_hrs>\n";
        $xml .= "   <is_public><![CDATA[".$do_project->is_public."]]></is_public>\n\n";

        $xml .= "   <project_task>\n";
        $xml .= "    <idproject_task><![CDATA[".$do_task->getData("idproject_task")."]]></idproject_task>\n";
        $xml .= "    <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "    <idproject><![CDATA[".$do_task->getData("idproject")."]]></idproject>\n";
        $xml .= "    <progress><![CDATA[".$do_task->getData("progress")."]]></progress>\n";
        $xml .= "    <drop_box_code><![CDATA[".$do_task->getData("drop_box_code")."]]></drop_box_code>\n";
        $xml .= "    <priority><![CDATA[".$do_task->getData("priority")."]]></priority>\n";
        $xml .= "    <hrs_work_expected><![CDATA[".$do_task->getData("hrs_work_expected")."]]></hrs_work_expected>\n";

        $xml .= "    <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "    <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "    <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "    <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "    <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "    <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "    <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "    <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "    <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "    <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "    <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n\n";

        $do_prj_discuss = new ProjectDiscuss();
        $do_prj_discuss->getProjectTaskDiscussions($do_task->getData("idproject_task"));
        while($do_prj_discuss->next()) {
          $xml .= "    <project_discuss>\n";
          $xml .= "     <idproject_discuss><![CDATA[".$do_prj_discuss->getData("idproject_discuss")."]]></idproject_discuss>\n";
          $xml .= "     <idproject_task><![CDATA[".$do_prj_discuss->getData("idproject_task")."]]></idproject_task>\n";
          $xml .= "     <idtask><![CDATA[".$do_prj_discuss->getData("idtask")."]]></idtask>\n";
          $xml .= "     <idproject><![CDATA[".$do_prj_discuss->getData("idproject")."]]></idproject>\n";
          $xml .= "     <discuss><![CDATA[".$do_prj_discuss->getData("discuss")."]]></discuss>\n";
          $xml .= "     <date_added><![CDATA[".$do_prj_discuss->getData("date_added")."]]></date_added>\n";
          $xml .= "     <document><![CDATA[".$do_prj_discuss->getData("document")."]]></document>\n";
          $xml .= "     <iduser><![CDATA[".$do_prj_discuss->getData("iduser")."]]></iduser>\n";
          $xml .= "     <drop_box_sender><![CDATA[".$do_prj_discuss->getData("drop_box_sender")."]]></drop_box_sender>\n";
          $xml .= "     <priority><![CDATA[".$do_prj_discuss->getData("priority")."]]></priority>\n";
          $xml .= "     <hours_work><![CDATA[".$do_prj_discuss->getData("hours_work")."]]></hours_work>\n";
          $xml .= "     <discuss_edit_access><![CDATA[".$do_prj_discuss->getData("discuss_edit_access")."]]></discuss_edit_access>\n";
          $xml .= "    </project_discuss>\n";
        }
        $do_prj_discuss->free();

        $xml .= "   </project_task>\n";

        $xml .= "  </contact_task_with_project>\n";

        $do_project->free();
      }
      $do_task->free();
      }
	
	  
      $do_invoice = new Invoice();
      $do_invoice->getContactInvoiceDetailsWithUser($do_contact->getData("idcontact"),$do_user->iduser);
      while($do_invoice->next()) {
        $xml .= "  <invoice>\n";
        $xml .= "   <idinvoice><![CDATA[".$do_invoice->getData("idinvoice")."]]></idinvoice>\n";
        $xml .= "   <num><![CDATA[".$do_invoice->getData("num")."]]></num>\n";
        $xml .= "   <iduser><![CDATA[".$do_invoice->getData("iduser")."]]></iduser>\n";
        $xml .= "   <description><![CDATA[".$do_invoice->getData("description")."]]></description>\n";
        $xml .= "   <amount><![CDATA[".$do_invoice->getData("amount")."]]></amount>\n";
        $xml .= "   <datepaid><![CDATA[".$do_invoice->getData("datepaid")."]]></datepaid>\n";
        $xml .= "   <datecreated><![CDATA[".$do_invoice->getData("datecreated")."]]></datecreated>\n";
        $xml .= "   <status><![CDATA[".$do_invoice->getData("status")."]]></status>\n";
        $xml .= "   <discount><![CDATA[".$do_invoice->getData("discount")."]]></discount>\n";
        $xml .= "   <idcontact><![CDATA[".$do_invoice->getData("idcontact")."]]></idcontact>\n";
        $xml .= "   <due_date><![CDATA[".$do_invoice->getData("due_date")."]]></due_date>\n";
        $xml .= "   <invoice_address><![CDATA[".$do_invoice->getData("invoice_address")."]]></invoice_address>\n";
        $xml .= "   <invoice_term><![CDATA[".$do_invoice->getData("invoice_term")."]]></invoice_term>\n";
        $xml .= "   <invoice_note><![CDATA[".$do_invoice->getData("invoice_note")."]]></invoice_note>\n";
        $xml .= "   <sub_total><![CDATA[".$do_invoice->getData("sub_total")."]]></sub_total>\n";
        $xml .= "   <net_total><![CDATA[".$do_invoice->getData("net_total")."]]></net_total>\n";
        $xml .= "   <amt_due><![CDATA[".$do_invoice->getData("amt_due")."]]></amt_due>\n";
        $xml .= "   <idcompany><![CDATA[".$do_invoice->getData("idcompany")."]]></idcompany>\n";
        $xml .= "   <tax><![CDATA[".$do_invoice->getData("tax")."]]></tax>\n";
        $xml .= "   <set_delete><![CDATA[".$do_invoice->getData("set_delete")."]]></set_delete>\n";
        $xml .= "   <total_discounted_amt><![CDATA[".$do_invoice->getData("total_discounted_amt")."]]></total_discounted_amt>\n";
        $xml .= "   <total_taxed_amount><![CDATA[".$do_invoice->getData("total_taxed_amount")."]]></total_taxed_amount>\n";

        $invoice_line = $do_invoice->getChildInvoiceLine();
        while($invoice_line->next()) {
          $xml .= "   <invoiceline>\n";
          $xml .= "    <idinvoiceline><![CDATA[".$invoice_line->idinvoiceline."]]></idinvoiceline>\n";
          $xml .= "    <idinvoice><![CDATA[".$invoice_line->idinvoice."]]></idinvoice>\n";
          $xml .= "    <description><![CDATA[".$invoice_line->description."]]></description>\n";
          $xml .= "    <price><![CDATA[".$invoice_line->price."]]></price>\n";
          $xml .= "    <qty><![CDATA[".$invoice_line->qty."]]></qty>\n";
          $xml .= "    <total><![CDATA[".$invoice_line->total."]]></total>\n";
          $xml .= "    <item><![CDATA[".$invoice_line->item."]]></item>\n";
          $xml .= "    <line_tax><![CDATA[".$invoice_line->line_tax."]]></line_tax>\n";
          $xml .= "    <discounted_amount><![CDATA[".$invoice_line->discounted_amount."]]></discounted_amount>\n";
          $xml .= "    <taxed_amount><![CDATA[".$invoice_line->taxed_amount."]]></taxed_amount>\n";
          $xml .= "   </invoiceline>\n";
        }
        $invoice_line->free();

        $do_recurrent_invoice = new RecurrentInvoice();
        $do_recurrent_invoice->getRecurrentInvoiceDetail($do_invoice->getData("idinvoice"));
        while($do_recurrent_invoice->next()) {
          $xml .= "   <recurrentinvoice>\n";
          $xml .= "    <idrecurrentinvoice><![CDATA[".$do_recurrent_invoice->getData("idrecurrentinvoice")."]]></idrecurrentinvoice>\n";
          $xml .= "    <iduser><![CDATA[".$do_recurrent_invoice->getData("iduser")."]]></iduser>\n";
          $xml .= "    <idinvoice><![CDATA[".$do_recurrent_invoice->getData("idinvoice")."]]></idinvoice>\n";
          $xml .= "    <nextdate><![CDATA[".$do_recurrent_invoice->getData("nextdate")."]]></nextdate>\n";
          $xml .= "    <recurrence><![CDATA[".$do_recurrent_invoice->getData("recurrence")."]]></recurrence>\n";
          $xml .= "    <recurrencetype><![CDATA[".$do_recurrent_invoice->getData("recurrencetype")."]]></recurrencetype>\n";
          $xml .= "   </recurrentinvoice>\n";
        }
        $do_recurrent_invoice->free();

        $do_paymentlog = new PaymentLog();
        $do_paymentlog->getPaymentLogDetails($do_invoice->getData("idinvoice"));
        while($do_paymentlog->next()) {
          $xml .= "   <paymentlog>\n";
          $xml .= "    <idpaymentlog ><![CDATA[".$do_paymentlog->getData("idpaymentlog")."]]></idpaymentlog >\n";
          $xml .= "    <timestamp><![CDATA[".$do_paymentlog->getData("timestamp")."]]></timestamp>\n";
          $xml .= "    <idinvoice><![CDATA[".$do_paymentlog->getData("idinvoice")."]]></idinvoice>\n";
          $xml .= "    <amount><![CDATA[".$do_paymentlog->getData("amount")."]]></amount>\n";
          $xml .= "    <payment_type><![CDATA[".$do_paymentlog->getData("payment_type")."]]></payment_type>\n";
          $xml .= "    <ref_num><![CDATA[".$do_paymentlog->getData("ref_num")."]]></ref_num>\n";
          $xml .= "    <date_added><![CDATA[".$do_paymentlog->getData("date_added")."]]></date_added>\n";

          $do_payment_invoice = new PaymentInvoice();
          $do_payment_invoice->getInvDetails($do_paymentlog->getData("idpaymentlog"));
          while($do_payment_invoice->next()) {
            $xml .= "    <payment_invoice>\n";
            $xml .= "     <idpayment_invoice><![CDATA[".$do_payment_invoice->getData("idpayment_invoice")."]]></idpayment_invoice>\n";
            $xml .= "     <idpayment><![CDATA[".$do_payment_invoice->getData("idpayment")."]]></idpayment>\n";
            $xml .= "     <idinvoice><![CDATA[".$do_payment_invoice->getData("idinvoice")."]]></idinvoice>\n";
            $xml .= "     <amount><![CDATA[".$do_payment_invoice->getData("amount")."]]></amount>\n";
            $xml .= "    </payment_invoice>\n";
          }
          $do_payment_invoice->free();

          $do_paymentlog_extra_amount = new PaymentLog();
          $do_paymentlog_extra_amount->getPaymentLogExtraAmountDetails($do_paymentlog->getData("idpaymentlog"));
          while($do_paymentlog_extra_amount->next()) {
            $xml .= "    <paymentlog_extra_amount>\n";
            $xml .= "     <idpaymentlog_extra_amount><![CDATA[".$do_paymentlog_extra_amount->getData("idpaymentlog_extra_amount")."]]></idpaymentlog_extra_amount>\n";
            $xml .= "     <idpaymentlog><![CDATA[".$do_paymentlog_extra_amount->getData("idpaymentlog")."]]></idpaymentlog>\n";
            $xml .= "     <extra_amt><![CDATA[".$do_paymentlog_extra_amount->getData("extra_amt")."]]></extra_amt>\n";
            $xml .= "     <iduser><![CDATA[".$do_paymentlog_extra_amount->getData("iduser")."]]></iduser>\n";
            $xml .= "    </paymentlog_extra_amount>\n";
          }
          $do_paymentlog_extra_amount->free();

          $xml .= "   </paymentlog>\n";

        }
        $do_paymentlog->free();

        $xml .= "  </invoice>\n";
      }
      $do_invoice->free();

      $do_tag = new Tag();
      $do_tag->getUserContactTags($iduser,$do_contact->getData("idcontact"));
      while($do_tag->next()) {
        $xml .= "  <tag>\n";
        $xml .= "   <idtag><![CDATA[".$do_tag->idtag."]]></idtag>\n";
        $xml .= "   <tag_name><![CDATA[".$do_tag->tag_name."]]></tag_name>\n";
        $xml .= "   <iduser><![CDATA[".$do_tag->iduser."]]></iduser>\n";
        $xml .= "   <reference_type><![CDATA[".$do_tag->reference_type."]]></reference_type>\n";
        $xml .= "   <idreference><![CDATA[".$do_tag->idreference."]]></idreference>\n";
        $xml .= "   <date_added><![CDATA[".$do_tag->date_added."]]></date_added>\n";
        $xml .= "  </tag>\n";
      }
     $do_tag->free();
 
      $xml .= " </contact>\n";
		$i++;
    } //end line
	$xml .= "</contacts>\n";
   }else { // if no contact 
	 
	 $do_task = new Task();
       $do_task->getContactTasksAssociatedWithProjectWithUser($iduser);
       while($do_task->next()) {
        $do_project = new Project();
        $do_project->getId($do_task->idproject);

        $xml .= "  <project>\n";
        $xml .= "   <idproject><![CDATA[".$do_project->idproject."]]></idproject>\n";
        $xml .= "   <iduser><![CDATA[".$do_project->iduser."]]></iduser>\n";
        $xml .= "   <name><![CDATA[".$do_project->name."]]></name>\n";
        $xml .= "   <end_date_dateformat><![CDATA[".$do_project->end_date_dateformat."]]></end_date_dateformat>\n";
        $xml .= "   <idcompany><![CDATA[".$do_project->idcompany."]]></idcompany>\n";
        $xml .= "   <status><![CDATA[".$do_project->status."]]></status>\n";
        $xml .= "   <effort_estimated_hrs><![CDATA[".$do_project->effort_estimated_hrs."]]></effort_estimated_hrs>\n";
        $xml .= "   <is_public><![CDATA[".$do_project->is_public."]]></is_public>\n\n";

        $xml .= "   <project_task>\n";
        $xml .= "    <idproject_task><![CDATA[".$do_task->getData("idproject_task")."]]></idproject_task>\n";
        $xml .= "    <idtask><![CDATA[".$do_task->getData("idtask")."]]></idtask>\n";
        $xml .= "    <idproject><![CDATA[".$do_task->getData("idproject")."]]></idproject>\n";
        $xml .= "    <progress><![CDATA[".$do_task->getData("progress")."]]></progress>\n";
        $xml .= "    <drop_box_code><![CDATA[".$do_task->getData("drop_box_code")."]]></drop_box_code>\n";
        $xml .= "    <priority><![CDATA[".$do_task->getData("priority")."]]></priority>\n";
        $xml .= "    <hrs_work_expected><![CDATA[".$do_task->getData("hrs_work_expected")."]]></hrs_work_expected>\n";

        $xml .= "    <task_description><![CDATA[".$do_task->getData("task_description")."]]></task_description>\n";
        $xml .= "    <due_date><![CDATA[".$do_task->getData("due_date")."]]></due_date>\n";
        $xml .= "    <category><![CDATA[".$do_task->getData("category")."]]></category>\n";
        $xml .= "    <iduser><![CDATA[".$do_task->getData("iduser")."]]></iduser>\n";
        $xml .= "    <due_date_dateformat><![CDATA[".$do_task->getData("due_date_dateformat")."]]></due_date_dateformat>\n";
        $xml .= "    <status><![CDATA[".$do_task->getData("status")."]]></status>\n";
        $xml .= "    <date_completed><![CDATA[".$do_task->getData("date_completed")."]]></date_completed>\n";
        $xml .= "    <idcontact><![CDATA[".$do_task->getData("idcontact")."]]></idcontact>\n";
        $xml .= "    <from_note><![CDATA[".$do_task->getData("from_note")."]]></from_note>\n";
        $xml .= "    <is_sp_date_set><![CDATA[".$do_task->getData("is_sp_date_set")."]]></is_sp_date_set>\n";
        $xml .= "    <task_category><![CDATA[".$do_task->getData("task_category")."]]></task_category>\n\n";

        $do_prj_discuss = new ProjectDiscuss();
        $do_prj_discuss->getProjectTaskDiscussions($do_task->getData("idproject_task"));
        while($do_prj_discuss->next()) {
          $xml .= "    <project_discuss>\n";
          $xml .= "     <idproject_discuss><![CDATA[".$do_prj_discuss->getData("idproject_discuss")."]]></idproject_discuss>\n";
          $xml .= "     <idproject_task><![CDATA[".$do_prj_discuss->getData("idproject_task")."]]></idproject_task>\n";
          $xml .= "     <idtask><![CDATA[".$do_prj_discuss->getData("idtask")."]]></idtask>\n";
          $xml .= "     <idproject><![CDATA[".$do_prj_discuss->getData("idproject")."]]></idproject>\n";
          $xml .= "     <discuss><![CDATA[".$do_prj_discuss->getData("discuss")."]]></discuss>\n";
          $xml .= "     <date_added><![CDATA[".$do_prj_discuss->getData("date_added")."]]></date_added>\n";
          $xml .= "     <document><![CDATA[".$do_prj_discuss->getData("document")."]]></document>\n";
          $xml .= "     <iduser><![CDATA[".$do_prj_discuss->getData("iduser")."]]></iduser>\n";
          $xml .= "     <drop_box_sender><![CDATA[".$do_prj_discuss->getData("drop_box_sender")."]]></drop_box_sender>\n";
          $xml .= "     <priority><![CDATA[".$do_prj_discuss->getData("priority")."]]></priority>\n";
          $xml .= "     <hours_work><![CDATA[".$do_prj_discuss->getData("hours_work")."]]></hours_work>\n";
          $xml .= "     <discuss_edit_access><![CDATA[".$do_prj_discuss->getData("discuss_edit_access")."]]></discuss_edit_access>\n";
          $xml .= "    </project_discuss>\n";
        }
        $do_prj_discuss->free();

        $xml .= "   </project_task>\n";

        $xml .= "  </project>\n";

        $do_project->free();
      }
      $do_task->free();
      
      $do_task = new Task();
      $do_task->getProjectsWithNoTask($iduser);
      while($do_task->next()){
		 $do_project = new Project();
        $do_project->getId($do_task->idproject);

        $xml .= "  <project>\n";
        $xml .= "   <idproject><![CDATA[".$do_project->idproject."]]></idproject>\n";
        $xml .= "   <iduser><![CDATA[".$do_project->iduser."]]></iduser>\n";
        $xml .= "   <name><![CDATA[".$do_project->name."]]></name>\n";
        $xml .= "   <end_date_dateformat><![CDATA[".$do_project->end_date_dateformat."]]></end_date_dateformat>\n";
        $xml .= "   <idcompany><![CDATA[".$do_project->idcompany."]]></idcompany>\n";
        $xml .= "   <status><![CDATA[".$do_project->status."]]></status>\n";
        $xml .= "   <effort_estimated_hrs><![CDATA[".$do_project->effort_estimated_hrs."]]></effort_estimated_hrs>\n";
        $xml .= "   <is_public><![CDATA[".$do_project->is_public."]]></is_public>\n";
        $xml .= "  </project>\n";
	  }
	  $do_task->free();
	
   }
   
   $do_login_audit = new LoginAudit();
   $do_login_audit->getLoginAuditDetails($iduser);
   
   while($do_login_audit->next()){
	   
	   $xml .= "  <login_audit>\n";
       $xml .= "   <idlogin_audit><![CDATA[".$do_login_audit->idlogin_audit."]]></idlogin_audit>\n";
       $xml .= "   <iduser><![CDATA[".$do_login_audit->iduser."]]></iduser>\n";
       $xml .= "   <last_login><![CDATA[".$do_login_audit->last_login."]]></last_login>\n";
       $xml .= "   <ip_address><![CDATA[".$do_login_audit->ip_address."]]></ip_address>\n";
       $xml .= "   <login_type><![CDATA[".$do_login_audit->login_type."]]></login_type>\n";
       $xml .= "  </login_audit>\n";
	   
   }
   $do_login_audit->free();
   
	if(!is_dir(XML_EXPORT)){
        mkdir(XML_EXPORT);         
        } 
    $xml_file = XML_EXPORT."adm_".$iduser."_inactiveuser_bkp.xml";
    $handle_xml = fopen($xml_file, "w+");
    if (fwrite($handle_xml, $xml) === FALSE) {
      //$_SESSION['in_page_message'] = "ofuz_export_xml_failure";
      echo 'could not write'; exit();
    } else {
      $_SESSION['in_page_message'] = "ofuz_export_xml_success";
    }
	
    fclose($handle_xml);
    //Below method deletes user information
    $deleteuser = new OfuzCancelAccount();
	$deleteuser->deleteUserAccount($iduser);
	
	$do_login_audit = new LoginAudit();
	$id = $do_login_audit->getLastLogin($iduser);
	$do_login_audit->getId($id);
	$do_login_audit->delete();
	$do_login_audit->free();
	

  }



}

