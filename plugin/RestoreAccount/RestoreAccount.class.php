<?php
set_time_limit(3600); //1 hr
/**
  * RestoreAccount
  * Upload and import an XML account information file.
  * setTitle() will set the Block Title
  * setContent() will set the content
  * displayBlock() call will display the block
  * @author 
  */

class RestoreAccount extends TabSetting{
    
    
  function __construct() {
    $this->setPlugInName("RestoreAccount");
    $this->setTabName("Import Account");
    $this->setTitle(_('Import Account'));
    $this->setPages(array("import_account"));
    $this->setDefaultPage('import_account');      
  }
  /**
    * processTab() , This method must be added  
    * Must extent BaseTab
  */
  function processTab(){
    $this->displayTab();
  }

  function eventImportAccount(EventControler $evtcl) {
    $msg = "";

    $uploaded_file = $_FILES['fields']['name']['import_account'];
    $target_path = 'files/' . $uploaded_file;

    if(!move_uploaded_file($_FILES['fields']['tmp_name']['import_account'], $target_path)) {
	  $msg = "There was an error uploading the file, please try again!";

    } else  {
      chmod($target_path, 0755);
      if(file_exists($target_path)){
	//$xml = simplexml_load_file($_SERVER['DOCUMENT_ROOT']."/".$target_path);
	$str_xml = file_get_contents($_SERVER['DOCUMENT_ROOT']."/".$target_path);
	$str_xml = preg_replace('/[^(\x20-\x7F)]*/','', $str_xml);
	$xml = simplexml_load_string($str_xml);
echo'<pre>';print_r($xml);echo'</pre>';die();
	if($xml !== FALSE){

	  $c_cnt = count($xml->contact);
	  if($c_cnt) {
	    for($i = 0; $i < $c_cnt; $i++) {
	      $do_contact = new Contact();
	      $contact = $xml->contact[$i];
	      $do_contact->firstname = $contact->firstname;
	      $do_contact->lastname = $contact->lastname;
	      $do_contact->position = $contact->position;
	      $do_contact->company = $contact->company;
	      $do_contact->idcompany = $contact->idcompany;
	      $do_contact->iduser = $_SESSION['do_User']->iduser;
	      $do_contact->picture = $contact->picture;
	      $do_contact->summary = $contact->summary;
	      $do_contact->birthday = $contact->birthday;
	      $do_contact->portal_code = $contact->portal_code;
	      $do_contact->fb_userid = $contact->fb_userid;
	      $do_contact->tw_user_id = $contact->tw_user_id;
	      $do_contact->email_optout = $contact->email_optout;


    
	      $do_contact->add();
	      $lastInsertedContId = $do_contact->getPrimaryKeyValue();


/**
  *Contact Address	
  */

			  $ca_cnt = count($contact->contact_address);
			    if($ca_cnt) {
			      for($ca_cnt_i = 0; $ca_cnt_i < $ca_cnt; $ca_cnt_i++) {
				$do_contact_address = new ContactAddress();
				$contact_address = $contact->contact_address[$ca_cnt_i];
				$do_contact_address->city = $contact_address->city;
				$do_contact_address->country = $contact_address->country;
				$do_contact_address->state = $contact_address->state;
				$do_contact_address->street = $contact_address->street;
				$do_contact_address->zipcode = $contact_address->zipcode;
				$do_contact_address->idcontact = $lastInsertedContId;
				$do_contact_address->address = $contact_address->address;
				$do_contact_address->address_type = $contact_address->address_type;
				$do_contact_address->add();
				$do_contact_address->free();
			      }
			    }
/**
  *Contact Email	
  */	      
			  $ce_cnt = count($contact->contact_email);
			  if($ce_cnt) {
			    for($ce_cnt_i = 0; $ce_cnt_i < $ce_cnt; $ce_cnt_i++) {
			      $do_contact_email = new ContactEmail();
			      $contact_email = $contact->contact_email[$ce_cnt_i];
			      $do_contact_email->idcontact = $lastInsertedContId;
			      $do_contact_email->email_address = $contact_email->email_address;
			      $do_contact_email->email_type = $contact_email->email_type;
			      $do_contact_email->email_isdefault = $contact_email->email_isdefault;
			      $do_contact_email->add();
			      $do_contact_email->free();
			    }
			  }
/**
  *Contact Phone 
  */
			  $cp_cnt = count($contact->contact_phone);
			  if($cp_cnt) {
			    for($cp_cnt_i = 0; $cp_cnt_i < $cp_cnt; $cp_cnt_i++) {
			      $do_contact_phone = new ContactPhone();
			      $contact_phone = $contact->contact_phone[$cp_cnt_i];
			      $do_contact_phone->phone_number = $contact_phone->phone_number;
			      $do_contact_phone->phone_type = $contact_phone->phone_type;
			      $do_contact_phone->idcontact = $lastInsertedContId;
			      $do_contact_phone->add();
			      $do_contact_phone->free();
			    }
			  }
/**
  *Contact Note	
  */
			 $cn_cnt = count($contact->contact_note);
			 if($cn_cnt) {
			  for($cn_cnt_i = 0; $cn_cnt_i < $cn_cnt; $cn_cnt_i++) {
			    $do_contact_note = new ContactNotes();
			    $contact_note = $contact->contact_note[$cn_cnt_i];
			    $do_contact_note->idcontact = $lastInsertedContId;
			    $do_contact_note->note = $contact_note->note;
			    $do_contact_note->date_added = $contact_note->date_added;
			    $do_contact_note->document = $contact_note->document;
			    $do_contact_note->idcompany = $contact_note->idcompany;
			    $do_contact_note->iduser = $_SESSION['do_User']->iduser;
			    $do_contact_note->priority = $contact_note->priority;
			    $do_contact_note->send_email = $contact_note->send_email;
			    $do_contact_note->hours_work = $contact_note->hours_work;
			    $do_contact_note->note_visibility = $contact_note->note_visibility;
			    $do_contact_note->type = $contact_note->type;
			    $do_contact_note->add();
			    $do_contact_note->free();
			  }
			}
/**
  *Contact Tag	
  */
			      $ctag_cnt = count($contact->contact_tag);
			      if($ctag_cnt) {
				for($ctag_cnt_i = 0; $ctag_cnt_i < $ctag_cnt; $ctag_cnt_i++) {
				  $do_tag = new Tag();
				  $contact_tag = $contact->contact_tag[$ctag_cnt_i];
				  $do_tag->tag_name = $contact_tag->tag_name;
				  $do_tag->iduser = $_SESSION['do_User']->iduser;
				  $do_tag->reference_type = $contact_tag->reference_type;
				  $do_tag->idreference = $lastInsertedContId;
				  $do_tag->date_added = $contact_tag->date_added;
				  $do_tag->add();
				  $do_tag->free();
				}
			      }
/**
  *Contact tasks which are not associated with Project	
  */
			      //Contact tasks which are not associated with Project
			      $ctwop_cnt = count($contact->contact_task_without_project);
			      if($ctwop_cnt) {
				for($ctwop_cnt_i = 0; $ctwop_cnt_i < $ctwop_cnt; $ctwop_cnt_i++) {
				  $do_task = new Task();
				  $contact_task_wo_p = $contact->contact_task_without_project[$ctwop_cnt_i];
				  $do_task->task_description = $contact_task_wo_p->task_description;
				  $do_task->due_date = $contact_task_wo_p->due_date;
				  $do_task->category = $contact_task_wo_p->category;
				  $do_task->iduser = $_SESSION['do_User']->iduser;
				  $do_task->due_date_dateformat = $contact_task_wo_p->due_date_dateformat;
				  $do_task->status = $contact_task_wo_p->status;
				  $do_task->date_completed = $contact_task_wo_p->date_completed;
				  $do_task->idcontact = $lastInsertedContId;
				  $do_task->from_note = $contact_task_wo_p->from_note;
				  $do_task->is_sp_date_set = $contact_task_wo_p->is_sp_date_set;
				  $do_task->task_category = $contact_task_wo_p->task_category;
				  $do_task->add();
				  $do_task->free();
				}
			      }
/**
  *Contact tasks which are associated with Project	
  */
				
				$arr_prj = array();
				$cont_task_with_prj_cnt = count($contact->contact_task_with_project);
				if($cont_task_with_prj_cnt) { 
				  for($i = 0; $i < $cont_task_with_prj_cnt; $i++) {
				    $do_project = new Project();
				    $project = $contact->contact_task_with_project[$i];

				    $do_project->iduser = $_SESSION['do_User']->iduser;
				    $do_project->name = $project->name;
				    $do_project->end_date_dateformat = $project->end_date_dateformat;
				    $do_project->idcompany = $project->idcompany;
				    $do_project->status = $project->status;
				    $do_project->effort_estimated_hrs = $project->effort_estimated_hrs;
				    $do_project->is_public = $project->is_public;

				    $do_project->add();

				    $lastInsertedPrjId = $do_project->getPrimaryKeyValue();

				    //$arr_prj[$lastInsertedPrjId] = $project->idproject;

				   $pt_cnt = count($project->project_task);
				    if($pt_cnt) {				
				      for($pt_cnt_i = 0; $pt_cnt_i < $pt_cnt; $pt_cnt_i++) {
					$do_task = new Task();
					$project_task = $project->project_task[$pt_cnt_i];

	      
					$do_task->task_description = $project_task->task_description;
					$do_task->due_date = $project_task->due_date;
					$do_task->category = $project_task->category;
					$do_task->iduser = $_SESSION['do_User']->iduser;
					$do_task->due_date_dateformat = $project_task->due_date_dateformat;
					$do_task->status = $project_task->status;
					$do_task->date_completed = $project_task->date_completed;
					$do_task->idcontact = $lastInsertedContId;
					$do_task->from_note = $project_task->from_note;
					$do_task->is_sp_date_set = $project_task->is_sp_date_set;
					$do_task->task_category = $project_task->task_category;

					$do_task->add();

					$lastInsertedTaskId = $do_task->getPrimaryKeyValue();

					$do_project_task = new ProjectTask();
					$do_project_task->idtask = $lastInsertedTaskId;
					$do_project_task->idproject = $lastInsertedPrjId;
					$do_project_task->progress = $project_task->progress;
					$do_project_task->drop_box_code = $project_task->drop_box_code;
					$do_project_task->priority = $project_task->priority;
					$do_project_task->hrs_work_expected = $project_task->hrs_work_expected;

					$do_project_task->add();

					$lastInsertedPrjTaskId = $do_project_task->getPrimaryKeyValue();


					$pd_cnt = count($project_task->project_discuss);
					if($pd_cnt) {
					  for($i = 0; $i < $pd_cnt; $i++) {
					    $do_project_discuss = new ProjectDiscuss();
					    $project_discuss = $project_task->project_discuss[$i];

					    $do_project_discuss->idproject_task = $lastInsertedPrjTaskId;
					    $do_project_discuss->idtask = $lastInsertedTaskId;
					    $do_project_discuss->idproject = $lastInsertedPrjId;
					    $do_project_discuss->discuss = $project_discuss->discuss;
					    $do_project_discuss->date_added = $project_discuss->date_added;
					    $do_project_discuss->document = $project_discuss->document;
					    $do_project_discuss->iduser = $_SESSION['do_User']->iduser;
					    $do_project_discuss->drop_box_sender = $project_discuss->drop_box_sender;
					    $do_project_discuss->priority = $project_discuss->priority;
					    $do_project_discuss->hours_work = $project_discuss->hours_work;
					    $do_project_discuss->discuss_edit_access = $project_discuss->discuss_edit_access;
					    $do_project_discuss->type = $project_discuss->type;

					    $do_project_discuss->add();
					    $do_project_discuss->free();
					  }
					}
					$do_project_task->free();
					$do_task->free();
				      }
				    }

				    $do_project->free();
				  }

				}


/**
  *Invoice import
  */

$msg_inv = "";
$inv_cnt = count($contact->invoice);

if($inv_cnt) {
  for($inv_cnt_i = 0; $inv_cnt_i < $inv_cnt; $inv_cnt_i++) {
    $do_invoice = new Invoice();
    $invoice = $contact->invoice[$inv_cnt_i];

    $do_invoice->num = $invoice->num;
    $do_invoice->iduser = $_SESSION['do_User']->iduser;
    $do_invoice->description = $invoice->description;
    $do_invoice->amount = $invoice->amount;
    $do_invoice->datepaid = $invoice->datepaid;
    $do_invoice->datecreated = $invoice->datecreated;
    $do_invoice->status = $invoice->status;
    $do_invoice->discount = $invoice->discount;
    $do_invoice->idcontact = $lastInsertedContId;
    $do_invoice->due_date = $invoice->due_date;
    $do_invoice->invoice_address = $invoice->invoice_address;
    $do_invoice->invoice_term = $invoice->invoice_term;
    $do_invoice->invoice_note = $invoice->invoice_note;
    $do_invoice->sub_total = $invoice->sub_total;
    $do_invoice->net_total = $invoice->net_total;
    $do_invoice->amt_due = $invoice->amt_due;
    $do_invoice->idcompany = $invoice->idcompany;
    $do_invoice->tax = $invoice->tax;
    $do_invoice->set_delete = $invoice->set_delete;
    $do_invoice->total_discounted_amt = $invoice->total_discounted_amt;
    $do_invoice->total_taxed_amount = $invoice->total_taxed_amount;

    $do_invoice->add();

    $lastInsertedInvoiceId = $do_invoice->getPrimaryKeyValue();

    $invline_cnt = count($invoice->invoiceline);
    if($invline_cnt) {
      for($invline_cnt_i = 0; $invline_cnt_i < $invline_cnt; $invline_cnt_i++) {
        $do_invoiceline = new InvoiceLine();
        $invoiceline = $invoice->invoiceline[$invline_cnt_i];

        $do_invoiceline->idinvoice = $lastInsertedInvoiceId;
        $do_invoiceline->description = $invoiceline->description;
        $do_invoiceline->price = $invoiceline->price;
        $do_invoiceline->qty = $invoiceline->qty;
        $do_invoiceline->total = $invoiceline->total;
        $do_invoiceline->item = $invoiceline->item;
        $do_invoiceline->line_tax = $invoiceline->line_tax;
        $do_invoiceline->discounted_amount = $invoiceline->discounted_amount;
        $do_invoiceline->taxed_amount = $invoiceline->taxed_amount;

        $do_invoiceline->add();
        $do_invoiceline->free();
      }
    } //invoiceline import ends

    // recurrentinvoice

    $recinv_cnt = count($invoice->recurrentinvoice);
    if($recinv_cnt) {
      for($recinv_cnt_i = 0; $recinv_cnt_i < $recinv_cnt; $recinv_cnt_i++) {
        $do_recurrentinvoice = new RecurrentInvoice();
        $recurrentinvoice = $invoice->recurrentinvoice[$recinv_cnt_i];

        $do_recurrentinvoice->iduser = $_SESSION['do_User']->iduser;
        $do_recurrentinvoice->idinvoice = $lastInsertedInvoiceId;
        $do_recurrentinvoice->nextdate = $recurrentinvoice->nextdate;
        $do_recurrentinvoice->recurrence = $recurrentinvoice->recurrence;
        $do_recurrentinvoice->recurrencetype = $recurrentinvoice->recurrencetype;

        $do_recurrentinvoice->add();
        $do_recurrentinvoice->free();
      }
    } //recurrentinvoice import ends

    // Payment Log import

    $paymentlog_cnt = count($invoice->paymentlog);
    if($paymentlog_cnt) {
      for($paymentlog_cnt_i = 0; $paymentlog_cnt_i < $paymentlog_cnt; $paymentlog_cnt_i++) {
        $do_paymentlog = new PaymentLog();
        $paymentlog = $invoice->paymentlog[$paymentlog_cnt_i];

        $do_paymentlog->timestamp = $paymentlog->timestamp;
        $do_paymentlog->idinvoice = $lastInsertedInvoiceId;
        $do_paymentlog->amount = $paymentlog->amount;
        $do_paymentlog->payment_type = $paymentlog->payment_type;
        $do_paymentlog->ref_num = $paymentlog->ref_num;
        $do_paymentlog->date_added = $paymentlog->date_added;

        $do_paymentlog->add();

        $lastInsertedPaymentLogId = $do_paymentlog->getPrimaryKeyValue();

        //payment_invoice : Payment Invoice import

        $paymentinv_cnt = count($paymentlog->payment_invoice);
        if($paymentinv_cnt) {
          for($paymentinv_cnt_i = 0; $paymentinv_cnt_i < $paymentinv_cnt; $paymentinv_cnt_i++) {
            $do_payment_invoice = new PaymentInvoice();
            $paymentinvoice = $paymentlog->payment_invoice[$paymentinv_cnt_i];

            $do_payment_invoice->idpayment = $lastInsertedPaymentLogId;
            $do_payment_invoice->idinvoice = $lastInsertedInvoiceId;
            $do_payment_invoice->amount = $paymentinvoice->amount;

            $do_payment_invoice->add();
            $do_payment_invoice->free();
          }
        } // payment_invoice import ends

        //paymentlog_extra_amount import

        $paymentlog_ext_amt_cnt = count($paymentlog->paymentlog_extra_amount);
        if($paymentlog_ext_amt_cnt) {
          for($paymentlog_ext_amt_cnt_i = 0; $paymentlog_ext_amt_cnt_i < $paymentlog_ext_amt_cnt; $paymentlog_ext_amt_cnt_i++) {

            $paymentlog_extra_amount = $paymentlog->paymentlog_extra_amount[$paymentlog_ext_amt_cnt_i];

            $q = new sqlQuery($GLOBALS['conx']);
            $query = "INSERT INTO paymentlog_extra_amount (`idpaymentlog`,`extra_amt`,`iduser`)
            VALUES (".$lastInsertedPaymentLogId.",".$paymentlog_extra_amount->extra_amt.",".$_SESSION['do_User']->iduser.")
            ";
            $q->query($query);
            $q->free();
          }
        } // paymentlog_extra_amount import ends

        $do_paymentlog->free();
      }
    } //Payment Log import ends
    $msg_inv = ", Invoices";
    $do_invoice->free();
  }
} // Invoice import ends
/************************************************************************************************************************/
$do_contact->free();

} 
	    $msg = "Your Contacts".$msg_inv;
}

/**
  *Company insert
 */


$compani_id = array();
$lastInsertedCompani_id = array();

$companies_cnt = count($xml->companies);
if($companies_cnt){
  for($i=0;$i<$companies_cnt;$i++){
      $do_company = new Company();
      $company=$xml->companies[$i];

      array_push($compani_id,"$company->idcompany");
     // $do_company->idcompany=$company->idcompany;
      $do_company->name=$company->name;
      $do_company->iduser=$_SESSION['do_User']->iduser;
    
      $do_company->add();
      array_push($lastInsertedCompani_id,$do_company->getPrimaryKeyValue());
      $do_company->free();
    }
}


	  //tasks which are neither associated with Contact nor with project
	  $task_wop_cnt = count($xml->task_without_project);
	  if($task_wop_cnt) {
	    for($i = 0; $i < $task_wop_cnt; $i++) {
	      $do_task = new Task();
	      $task_wop = $xml->task_without_project[$i];
	      $do_task->task_description = $task_wop->task_description;
	      $do_task->due_date = $task_wop->due_date;
	      $do_task->category = $task_wop->category;
	      $do_task->iduser = $_SESSION['do_User']->iduser;
	      $do_task->due_date_dateformat = $task_wop->due_date_dateformat;
	      $do_task->status = $task_wop->status;
	      $do_task->date_completed = $task_wop->date_completed;
	      $do_task->idcontact = $task_wop->idcontact; //it would be 0 since not associated with contact.
	      $do_task->from_note = $task_wop->from_note;
	      $do_task->is_sp_date_set = $task_wop->is_sp_date_set;
	      $do_task->task_category = $task_wop->task_category;
	      $do_task->add();
	      $do_task->free();
	    }
	    $msg .= ", Tasks";
	  }







	  //tasks which are associated with Project
	  $prj_cnt = count($xml->project);

	  if($prj_cnt) {
	    for($i = 0; $i < $prj_cnt; $i++) {
	      $do_project = new Project();
	      $project = $xml->project[$i];


	      $do_project->iduser = $_SESSION['do_User']->iduser;
	      $do_project->name = $project->name;
	      $do_project->end_date_dateformat = $project->end_date_dateformat;
	      $do_project->idcompany = $project->idcompany;
	      $do_project->status = $project->status;
	      $do_project->effort_estimated_hrs = $project->effort_estimated_hrs;
	      $do_project->is_public = $project->is_public;

	      $do_project->add();

	      $lastInsertedPrjId = $do_project->getPrimaryKeyValue();

	      $pt_cnt = count($project->project_task);
	      if($pt_cnt) {
		
        for($pt_cnt_i = 0; $pt_cnt_i < $pt_cnt; $pt_cnt_i++) {
          $do_task = new Task();
          $project_task = $project->project_task[$pt_cnt_i];

          $do_task->task_description = $project_task->task_description;
          $do_task->due_date = $project_task->due_date;
          $do_task->category = $project_task->category;
          $do_task->iduser = $_SESSION['do_User']->iduser;
          $do_task->due_date_dateformat = $project_task->due_date_dateformat;
          $do_task->status = $project_task->status;
          $do_task->date_completed = $project_task->date_completed;
          $do_task->idcontact = $project_task->idcontact;
          $do_task->from_note = $project_task->from_note;
          $do_task->is_sp_date_set = $project_task->is_sp_date_set;
          $do_task->task_category = $project_task->task_category;
          $do_task->add();

          $lastInsertedTskId = $do_task->getPrimaryKeyValue();


	  $q = new sqlQuery($GLOBALS['conx']);
	      if($project_task->progress==''){
		    $project_task_progress=0;
		}else{
		    $project_task_progress=$project_task->progress;
		}


	  $sql="INSERT INTO 
			project_task (idtask, idproject, progress,drop_box_code,priority,hrs_work_expected) 
			VALUES ({$lastInsertedTskId},{$lastInsertedPrjId},{$project_task_progress},{$project_task->drop_box_code},{$project_task->priority},{$project_task->hrs_work_expected})";
echo $sql; echo "<br>";
	  $q->query($sql);
	  $lastInsertedPrjTaskId = $q->getInsertId('project_task', 'idproject_task');




          $pd_cnt = count($project_task->project_discuss);
          if($pd_cnt) {
             for($pd_cnt_i = 0; $pd_cnt_i < $pd_cnt; $pd_cnt_i++) {
              $do_project_discuss = new ProjectDiscuss();
              $project_discuss = $project_task->project_discuss[$pd_cnt_i];

              $do_project_discuss->idproject_task = $lastInsertedPrjTaskId;
              $do_project_discuss->idtask = $lastInsertedTskId;
              $do_project_discuss->idproject = $lastInsertedPrjId;
              $do_project_discuss->discuss = $project_discuss->discuss;
              $do_project_discuss->date_added = $project_discuss->date_added;
              $do_project_discuss->document = $project_discuss->document;
              $do_project_discuss->iduser = $_SESSION['do_User']->iduser;
              $do_project_discuss->drop_box_sender = $project_discuss->drop_box_sender;
              $do_project_discuss->priority = $project_discuss->priority;
              $do_project_discuss->hours_work = $project_discuss->hours_work;
              $do_project_discuss->discuss_edit_access = $project_discuss->discuss_edit_access;
              $do_project_discuss->type = $project_discuss->type;

              $do_project_discuss->add();
              $do_project_discuss->free();
            }
          }
        // $do_project_task->free();
          $do_task->free();
        }
	      }

	      $do_project->free();
	    }

	
	  $compani_id_cnt = count($compani_id);
	   if($compani_id_cnt){
	      $j=0;
		foreach($compani_id as $cmp_id){
		  $q = new sqlQuery($GLOBALS['conx']);	

		  $sql="UPDATE  contact SET idcompany ={$lastInsertedCompani_id[$j]}  WHERE  iduser={$_SESSION['do_User']->iduser} AND idcompany ={$cmp_id}";
		  $q->query($sql);

		  $sql1="UPDATE  invoice SET idcompany ={$lastInsertedCompani_id[$j]}  WHERE  iduser={$_SESSION['do_User']->iduser} AND idcompany ={$cmp_id}";
		  $q->query($sql1);

		  $sql2="UPDATE  project SET idcompany ={$lastInsertedCompani_id[$j]}  WHERE  iduser={$_SESSION['do_User']->iduser} AND idcompany ={$cmp_id}";
		  $q->query($sql2);

		  $q->free();
		  $j++;
		 }
	    }
    
	    $do_create_usrtbl=new ContactView();
	    $do_create_usrtbl->rebuildContactUserTable($_SESSION['do_User']->iduser);
              

	    $msg .= " and Projects have been imported successfully.";
	  }


	} else {
	  $msg = "Sorry! The data could not be imported.";
	}
      } else{
	$msg = "Sorry! Could not find the uploaded file.";
      }
    }

    $_SESSION['in_page_message'] = $msg;



  }

  function object2array($object) {
    if (is_object($object)) {
      foreach ($object as $key => $value) {
      $array[$key] = $value;
      }
    }else {
      $array = $object;
    }
    return $array;
  }

}

?>
