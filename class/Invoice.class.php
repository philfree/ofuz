<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
    /**
     * Invoice class
     * Using the DataObject
     * In the Invoice table the 'amount' is the amount paid for that invoice not the
     * actual invoice amount. The sub_total is the total of line amount
     * and the net_total is sum(lineamout) (+/-) discount/tax

     *Different Invoice status : Quote,New,Sent, Partial and Paid
      Variables
	 <pre>
		+-----------------+-------------+------+-----+---------+----------------+
		| Field           | Type        | Null | Key | Default | Extra          |
		+-----------------+-------------+------+-----+---------+----------------+
		| idinvoice       | int(10)     | NO   | PRI | NULL    | auto_increment | 
		| num             | int(14)     | NO   |     |         |                | 
		| iduser          | int(14)     | NO   |     |         |                | 
		| description     | mediumtext  | NO   |     |         |                | 
		| amount          | float(10,2) | NO   |     |         |                | 
		| datepaid        | date        | NO   |     |         |                | 
		| datecreated     | date        | NO   |     |         |                | 
		| status          | varchar(50) | NO   |     |         |                | 
		| discount        | varchar(10) | NO   |     |         |                | 
		| idcontact       | int(14)     | NO   |     |         |                | 
		| due_date        | date        | NO   |     |         |                | 
		| invoice_address | mediumtext  | NO   |     |         |                | 
		| invoice_term    | mediumtext  | NO   |     |         |                | 
		| invoice_note    | mediumtext  | NO   |     |         |                | 
		| sub_total       | float(14,2) | NO   |     |         |                | 
		| net_total       | float(14,2) | NO   |     |         |                | 
		| amt_due         | float(14,2) | NO   |     |         |                | 
		| idcompany       | int(14)     | NO   |     |         |                | 
		| tax             | varchar(10) | NO   |     |         |                | 
		+-----------------+-------------+------+-----+---------+----------------+
     </pre>
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license ##License##
     * @version 0.6
     * @date 2010-09-03
     * @since 0.4
     */

class Invoice extends DataObject {
    
    public $table = "invoice";
    protected $primary_key = "idinvoice";
    public $invoice_num = "1";
    public $filter_set = false;
    //  $from_invoice_page is set to true by default. When an invoice related filter is triggered from any other places then this variable is set to false so as to hide some other section in the listing page.
    public $from_invoice_page = true; 
    public $filter_inv_status_val ;
    public $filter_inv_mon_val ;
    public $filter_month = '';
    public $filter_year = '';
    public $max_len_invoice_num = 10;
    public $inv_dd_format = "Y-m-d";
    public $inv_logo = '';
    public $authnet_login = '';
    public $authnet_merchant_id = ''; 
    public $paypal_business_email = '';
    // Currency Related 
    public $currency_sign = '$';
    public $currency_iso_code = ''; // Can be USD,CAD,GBP not used currently
    public $currency  = '$'; 
    public $currency_position  = 'l'; // default currency potion is left for euro its right
    public $user_edit_amount = True;

    private $report = Array ();

   // public $currency = array("USD")
    private $savedquery = Array (
      "AddInvoiceForm" => "coworker_add_form"
    );

    /**
      * Function getting all the invoice for the user
      * sets the query object
    */
    function getAllInvoice(){
         if($this->filter_month != ''){
              $date = $this->formatSearchMonth($this->filter_month);
         }else{
              $date = date("Y");
         }
         
         $this->query("select * from invoice where iduser = ".$_SESSION['do_User']->iduser.
                      " AND status <> 'Cancel' AND datecreated like '%".$date."%' order by ".$this->primary_key." desc"
                      );
    }
	
    /**
      * Checks if the there are invoice available for the user
      * @returns boolean 
    */
    function hasInvoices() {
            $this->query("select idinvoice from invoice where iduser= ".$_SESSION['do_User']->iduser);
            if ($this->getNumRows() > 0) { return true; } else { return false; }	
    }

    /**
      * Check if the invoice is set as delete in the databse
      * @param integer $idinvoice
      * @returns boolean
    */
    function isDeleted($idinvoice){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select status from ".$this->table." where idinvoice = ".$idinvoice);
        $q->fetch();
        if($q->getData("status") == 'Cancel'){
            return true;
        }else{ return false ; }
    }


    /**
      * Event method delete invoice
      * @param object $evtcl 
    */
    function eventDeleteInvoice(EventControler $evtcl){
        $status = $evtcl->status;
        $idinvoice = $evtcl->id;
        if($idinvoice && $status !=''){
                // Delete the RecurrentInvoice First
                $this->getId($idinvoice);
                $do_rec_invoice = $this->getChildRecurrentInvoice();
                while ($do_rec_invoice->next()) {
                    $do_rec_invoice->delete();
                }
                $q = new sqlQuery($this->getDbCon());
                switch($status){
                      case 'Quote' : $qry = "delete from ".$this->table." where idinvoice = ".$idinvoice." limit 1";
                                    break;
                      case 'New' : $qry = "Update ".$this->table." set status = 'Cancel' 
                                           where idinvoice = ".$idinvoice." limit 1";
                                    break;

                      case 'Sent' : $qry = "Update ".$this->table." set status = 'Cancel' 
                                           where idinvoice = ".$idinvoice." limit 1";
                                    break;
                }
                $q->query($qry);
                $evtcl->setDisplayNext(new Display($evtcl->goto));
        }
          
        
        
    }

    /**
      * Method to check if the user has permission to see the invoice
      * @param integer $idinvoice 
      * @param integer $iduser 
      * @return boolean
    */
    function isInvoiceOwner($idinvoice='',$iduser=""){
        if(empty($iduser)){ $iduser = $_SESSION['do_User']->iduser ; }
        if(empty($idinvoice)) { $idinvoice = $this->idinvoice; } 
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table." where idinvoice = ".$idinvoice." AND iduser = ".$iduser." AND status <> 'Cancel'");
        if($q->getNumRows()){
            return true;
        }else{
            return false;
        }
    }

    /**
      * Method to get the Invoices Past due
      * @param integer $idcontact
      * @param integer $idcompany
      * @return  $html
    */
    function getInvoicesPastDue($idcontact = "",$idcompany=""){
        $q_past_due = new sqlQuery($this->getDbCon());
        $contact_related = false;
        
        if($multiple_add === true){
            $qry_mul_add_inv_exclude_idinvoice = " AND idinvocie <> ".$_SESSION['do_invoice']->idinvoice ;
        }
        if($idcontact == ""){
            $q_past_due->query("Select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser.
                  " AND due_date < '".date("Y-m-d")."' 
                    AND status <> 'Quote'
                    AND status <> 'Paid'
                    AND status <> 'Cancel'
                    "
                  );
        }else{
            $q_past_due->query("Select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser.
                  " AND due_date < '".date("Y-m-d")."'
                    AND status <> 'Quote'
                    AND status <> 'Paid'
                    AND (idinvoice = ".$idcontact." or idcompany = ".$idcompany.")
                    AND status <> 'Cancel'
                    "
                  );
            if($q_past_due->getNumRows() < 1){
                $q_past_due->query("Select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser.
                  " AND due_date < '".date("Y-m-d")."' 
                    AND status <> 'Quote'
                    AND status <> 'Paid'
                    AND status <> 'Cancel'
                    "
                  );
            }
            $contact_related = true;
        } 
        
        if($q_past_due->getNumRows()){
            $do_contact_past_due = new Contact();
            $html = '';
            $html .='<br /><br />';
            $html .= '<div style="background-color:#ffffcc; padding: 6px 20px; color: #3f312b; font-size: 11pt; background-color: #ffffcc; border: solid 2px #f6bfbc; margin-right: 20px;" id="past_due_invoices" >';
    
           
            if(!$contact_related){ 
                  $html .= '<div align="left" style="float:left;"><b>'._('Past due invoices').'</b></div>';
                  $html .= '<div align="right"><a href="#" onclick = "sendPastDueRemainder();return false;">'._('send email reminder').'</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="closePastDue();return false;">'._('close').'<img src="/images/delete.gif"></a></div>';
            }else{
                 $e_pay_cancel = new Event("do_invoice->eventCancelMultiPayment");
                 $no = $e_pay_cancel->getLink('No');
                 $html .= '<div align="left" style="float:left;"><b>'._('Would you like add payment from the remaining '.$this->viewAmount($_SESSION['extra_amt'])).' in one of the following invoices? &nbsp;'.$no.'</b></div>';
                 $html .= '<div ><br /></div>'; 
            }
            $html .='<div style="width:700px;margin-left:0px;margin-top:5px;height:30px;text-align:left;position: relative; color:#FFA500;font-size:14px;">';
            $html .= '<div style="width:100px;float:left;"><b>'._('Number').'</b></div>';
            $html .= '<div style="width:250px;float:left;"><b>'._('Contact').'</b></div>';
            $html .= '<div style="width:100px;float:left;"><b>'._('Due Date').'</b></div>';
            $html .= '<div style="width:100px;float:left;"><b>'._('Total').'</b></div>';
            $html .= '<div style="width:100px;float:left;"><b>'._('Due').'</b></div>';
            $html .= '</div>';
            while($q_past_due->fetch()){ 
              $html .='<div class="invoices_past_due" id="cw'.$q_past_due->getData("idinvoice").'"  href="javascript:void(0)" onclick="window.location.href=\'/Invoice/'.$q_past_due->getData("idinvoice").'\'">';
              $html .= '<div class="invoices_main">';
              $html .= '<div style="width:100px;float:left;">
                                 '.$q_past_due->getData("num").' 
                         </div> ';

              $contact = $do_contact_past_due->getContact_Company_ForInvoice($q_past_due->getData("idcontact"),$q_past_due->getData("idcompany"));
              if($contact == ''){ $contact = '--'; }
              $html .= ' <div style="width:250px;float:left;">
                                  '.$contact.
                                  '
                             </div>';
              $html .= '<div style="width:100px;float:left;">'.$this->getInvFormattedDate($q_past_due->getData("due_date")).'</div>';
              $html .= '<div style="width:100px;float:left;">'.$this->viewAmount($q_past_due->getData("net_total")).'</div>';
              $html .= '<div style="width:100px;float:left;">'.$this->viewAmount($q_past_due->getData("amt_due")).'</div>';
              $html .= '</div></div>
                      <div class="spacerblock_2"></div>
                      ';
              
            }
            $html .= '<div class="spacerblock_2"></div></div>';  
        }
        
        return $html;
    }

    

    /**
      * Event method to set the session value for past due show hide.
      * This method is called to hide the past due invoices.
      * @param $evtcl -- Object
    */
    function eventClosePastDue(EventControler $evtcl){
        $_SESSION['inv_past_due_hide'] = 'Yes';
        $evtcl->addOutputValue('Yes');
    }

    /**
      * Event method to set the session value so that the 
      * past invoices can be seen on the invoices.php file
      * @param $evtcl -- Object
    */
    function eventShowPastDue(EventControler $evtcl){
        $_SESSION['inv_past_due_hide'] = '';
        $html = $this->getInvoicesPastDue();
        $evtcl->addOutputValue($html);
    }


    /**
      * Event method to send the reminder email to the customers on 
      * past due invoices. 
      * Uses Email template
      * @param $evtcl -- Object
    */
    function eventSendPastDueRemainderEmail(EventControler $evtcl){
        $q = new sqlQuery($this->getDbCon());
        $q->query("Select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser.
                  " AND due_date < '".date("Y-m-d")."' 
                    AND status <> 'Quote'
                    AND status <> 'Paid'
                    AND status <> 'Cancel'
                    "
                  );

        if($q->getNumRows()){
            $do_user_rel = new UserRelations();
            $do_contact = new Contact();
            $do_contact_email = new ContactEmail();
            $msg = '';
            while($q->fetch()){ // Fetching past due invoices
                // Get Contact related data
                $do_contact->getId($q->getData("idcontact"));
                $contact_name = $do_contact->firstname.' '.$do_contact->lastname;
                /*if($do_contact->email_address != '') {
                    $contact_email = $do_contact->email_address;
                } else { 
                    $ContactEmail = $do_contact->getChildContactEmail();
                    if($ContactEmail->getNumRows()) {
                          $contact_email = $ContactEmail->email_address;
                    }
                }*/
                  $do_contact_email = $do_contact->getChildContactEmail();
                  $contact_email = $do_contact_email->getDefaultEmail();


               // Contact related data ends here
                if($contact_email){
                    $total_due = $q->getData("amt_due");
                    $short_desc = substr($q->getData("description"), 0, 70);
                    $total_due = $this->viewAmount($total_due);
                    $email_template = new EmailTemplate("ofuz_past_due_invoice_notification");
                    $email_template->setSenderName($_SESSION['do_User']->getFullName());
                    $email_template->setSenderEmail($_SESSION['do_User']->email);
                    $signature = $_SESSION['do_User']->company.'<br />'.$_SESSION['do_User']->getFullName();
                    $invoice_url = $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_user_rel->encrypt($q->getData("idinvoice")).'/'.$do_user_rel->encrypt($q->getData("idcontact"));
                    $email_data = Array('name' => $contact_name,
                                                'sender_name'=>$_SESSION['do_User']->getFullName(),
                                                'company'=>$_SESSION['do_User']->company,
                                                'invoice_description'=>$q->getData("description"),
                                                'invoice_url'=>$invoice_url,
                                                'signature'=>$signature,
                                                'num'=>$q->getData("num"),
                                                'invoice_short_description'=>$short_desc,
                                                'due_date'=>$q->getData("due_date"),
                                                'amt_due'=>$total_due
                                                );
                    $emailer = new Radria_Emailer();
                    $emailer->setEmailTemplate($email_template);
                    $emailer->mergeArray($email_data);
                    $emailer->addTo($contact_email);
                    $emailer->send();
                    $msg .= '<br />'._("Successfully email sent to: ".$contact_email); 
                }else { 
                    $msg .= _("Unable to send email to ".$contact_name." as no email id found");
                }
            }// Past due invoice ends here
            $html = '<div style="margin-left:0px;">';
            $html .='<div class="messages_unauthorized">';
            $html .= $msg;
            $html .= '</div></div>';
            $evtcl->addOutputValue($html);
        }
    }
    
    
    /*
      Event method to get the invoice based on invoice filters
    */
  /*  function eventFilterInvoiceByStatus(EventControler $evtcl){
        
        $select = "select * from invoice";
        $where = " Where iduser = ".$_SESSION['do_User']->iduser;
        $order_by = " order by ".$this->primary_key." desc";
       
        if($evtcl->status != 'None'){
            $where.=" AND status = '".$evtcl->status."'";
            $qry = $select.$where.$order_by;
            $this->filter_set = true;
            $this->filter_inv_status_val = $evtcl->status;
            $this->setSqlQuery($qry);
        }else{
            $this->filter_set = false;
            $this->filter_inv_status_val = "";
        }
        
    }*/

     function getAllUnpaidInvoices(){
          $select = "select * from invoice";
          $where = " Where iduser = ".$_SESSION['do_User']->iduser." AND status <> 'Cancel' ";
          $where .= " AND status <> 'Paid' AND status <> 'Quote' ";
          $order_by = " order by ".$this->primary_key." desc";
          $qry = $select.$where.$order_by;
          $this->query($qry);
          $this->getValues();
      }


    /**
      * Event method to get the invoice based on invoice filters
      * if filter_set = true then the invoices.php will get the query set in the method
      * else will call getAllInvoice()
      * If the Month dropdown is seleted for filter then check if already one status is set before in the 
      * var filter_inv_status_val
      * @param object $evtcl 
    */

    function eventFilterInvoice(EventControler $evtcl){
        $this->setSqlQuery("");
        $select = "select * from invoice";
        $where = " Where iduser = ".$_SESSION['do_User']->iduser." AND status <> 'Cancel' ";
        $order_by = " order by ".$this->primary_key." desc";
        if($evtcl->filter_for_year != ''){$this->filter_year = $evtcl->filter_for_year;}
        
        if($evtcl->type == "date"){ 
            $this->filter_set = true;
            if($this->filter_inv_status_val != ''){
                $where.=" AND status = '".$this->filter_inv_status_val."'";
            }
            $where.= " AND datecreated like '%".$this->formatSearchMonth($evtcl->filter_for_mon)."%'";
            $qry = $select.$where.$order_by;
            $this->setSqlQuery($qry);
            $this->filter_month = $evtcl->filter_for_mon;
        }elseif($evtcl->type == 'Contact' || $evtcl->type == 'Company'){ 
            if($evtcl->type == 'Contact')
                $where.= " AND idcontact = ".$evtcl->idcontact;
            else
                $where.= " AND idcompany = ".$evtcl->idcompany;

            $this->from_invoice_page = false;
            $this->filter_set = true;
            switch($evtcl->status){
                case "Paid" : 
                            $where.=" AND ( status = 'Paid' OR status = 'Partial' ) ";
                            break;
                case "Quote" :
                            $where.=" AND  status = 'Quote'";
                            break;
                case "Invoice" : 
                            $where.=" AND ( status = 'New' OR status = 'Sent' )";
                            break;
                case "Overdue" : 
                            $where.=" AND due_date < '".date("Y-m-d")."' 
                                    AND status <> 'Quote'
                                    AND status <> 'Paid'
                                    ";
                            break;
              }
              $qry = $select.$where.$order_by;
              $this->setSqlQuery($qry);
            
        }elseif($evtcl->type == "User"){
              $this->from_invoice_page = false;
              $this->filter_set = true;
              switch($evtcl->status){
                case "Paid" : 
                            $where.=" AND ( status = 'Paid' OR status = 'Partial' ) ";
                            break;
                case "Quote" :
                            $where.=" AND  status = 'Quote'";
                            break;
                case "New" : 
                            $where.=" AND  status = 'New' ";
                            break;
                case "Sent" : 
                            $where.=" AND  status = 'Sent' ";
                            break;
                case "Overdue" : 
                            $where.=" AND due_date < '".date("Y-m-d")."' 
                                    AND status <> 'Quote'
                                    AND status <> 'Paid'
                                    ";
                            break;
			    case "Invoiced" :
					        $where.=" AND status <> 'Quote' ";
							break;
              }
              $qry = $select.$where.$order_by;
              $this->setSqlQuery($qry);
        }else{
              //if($this->filter_month == ''){$this->filter_month = date("m");}
              $where.= " AND datecreated like '%".$this->formatSearchMonth($this->filter_month)."%'";
              if($evtcl->status != 'None'){
                $where.=" AND status = '".$evtcl->status."'";
                $this->filter_set = true;
                $this->filter_inv_status_val = $evtcl->status;
              }else{
                $this->filter_inv_status_val = "";
              }
              $qry = $select.$where.$order_by;
              
              $this->setSqlQuery($qry);
        }

       $evtcl->setDisplayNext(new Display($evtcl->goto));
    }
    

    /**
      * Unset the filter and set to default
      * @param object $evtcl
    */
    function eventUnsetFilterInvoice(EventControler $evtcl){
        $this->setSqlQuery("");
        $this->filter_set = false;
        $this->from_invoice_page = true;
        $evtcl->setDisplayNext(new Display($evtcl->goto));
    }

    /**
      * Setting the Breadcrumb for the Invoice
    */
    function setBreadcrumb() {
        $do_breadcrumb = new Breadcrumb();
        $do_breadcrumb->type = "Invoice";
        if (is_object($_SESSION['do_User'])) {
            $do_breadcrumb->iduser = $_SESSION['do_User']->iduser;
        }
        $do_breadcrumb->id = $this->idinvoice;
        $do_breadcrumb->add();
    }

    /**
      *  Event Method to check empty invoice date. If the due_date is empty
      *  set this to the current date and update the $fields array
      *  @param object $evtcl
    */
    function checkEmptyDueDate(EventControler $evtcl){
        //echo $evtcl->fields["due_date"];
        if($evtcl->fields["due_date"] == ''){
              $fields["due_date"] = date('Y-m-d');
              $evtcl->updateParam("fields", $fields) ;
        }
    }

    /**
      * Custom add method to add an invoice. Needs a contact for each invocie else 
      * do not add the invoice
      * @param object $evtcl
    */

     function addInvoice(EventControler $evtcl){
        $_SESSION['in_page_message'] = '';
        $rec_value = $evtcl->recurrent;
        $add_invoice = false;
        $do_contact = new Contact();
        $concat_name_in_address = '';
        if($evtcl->fields["due_date"] == ''){	
              $this->due_date = date('Y-m-d');
        }else{ $this->due_date = $evtcl->fields["due_date"];  }
        
        if($evtcl->idcontact){
            $idcontact = $evtcl->idcontact;
            $add_invoice = true;
            $do_contact->getId($idcontact);
        }else{
            $cont_name = $evtcl->contact;
            if($cont_name != ''){
                $do_Contact_View = new ContactView();
                $do_contact->addNew();
                $do_contact->firstname = $cont_name;
                $do_contact->iduser = $_SESSION['do_User']->iduser;
                $do_contact->add();
                $idcontact = $do_contact->getPrimaryKeyValue(); 
                $do_Contact_View->setUser($_SESSION['do_User']->iduser);
                $do_contact->getId($idcontact);
                $concat_name_in_address = $do_contact->firstname;
                $do_Contact_View->addFromContact($do_contact);
                $do_Contact_View->updateFromContact($do_contact);
                if($idcontact){ 
                    $add_invoice = true;
                    $do_cont_addr = new ContactAddress();
                    $do_cont_addr->addNew();
                    $do_cont_addr->idcontact = $idcontact;
                    $do_cont_addr->address = $evtcl->fields["invoice_address"];
                    $do_cont_addr->address_type = 'Work';
                    if (strlen($evtcl->fields["invoice_address"]) > 0) $do_cont_addr->add();
                }
            }
        }

        if($add_invoice){
          // Keep the idcontact in case there is an idcompany
          if($do_contact->idcompany != '' && !empty($do_contact->idcompany)){$this->idcompany = $do_contact->idcompany; }
          //$this->num = $this->getUniqueInvoiceNum();
          $this->num = time();
          $this->iduser = $_SESSION['do_User']->iduser;
          $this->idterms = $evtcl->fields["idterms"];
          $this->description = $evtcl->fields["description"];
          $this->discount = $evtcl->fields["discount"];
          $this->tax = $evtcl->fields["tax"];
          $this->datecreated = date("Y-m-d");
          $this->idcontact = $idcontact;
          $this->status = 'Quote';
          $this->invoice_note = $evtcl->fields["invoice_note"];
          $this->invoice_term = $evtcl->fields["invoice_term"];
          if($concat_name_in_address == ''){
              $this->invoice_address = $evtcl->fields["invoice_address"];
          }else{  
              $this->invoice_address = $concat_name_in_address.'<br />'.$evtcl->fields["invoice_address"];
          }
          
          $this->add();
          $this->idinvoice = $this->getPrimaryKeyValue();
          // If the user set to have the ivoice added as recurrent Invoice then add
          if($evtcl->setRec == 'Yes'){
             if(!empty($rec_value) && preg_match('/^[0-9]+$/',$rec_value) && $rec_value != 0 ){
                // Add to recurrent
                $_SESSION['RecurrentInvoice']->addRecurrentInvoice($this->getPrimaryKeyValue(),$evtcl->recurrent,$evtcl->frequency,date("Y-m-d"));
             }else{
                $_SESSION['in_page_message'] = _("Recurrent Value is not correct.To set as Recurrent edit the invoice and enter numeric value");
             }  
          }

        }else { 
              $_SESSION['in_page_message'] = _("Invoice could be created. Possible reason Contact Missing."); 
              $evtcl->setDisplayNext(new Display('invoices.php'));
        }
    }

     /**
       * Invoice Update method that will just check the due_date  and set to 
       * current date if the value is set as null in the form. 
       * do update with the method update()
       * @param object $evtcl
    */
     function eventUpdateInvoice(EventControler $evtcl){
        if($evtcl->fields["due_date"] == ''){
              $this->due_date = date('Y-m-d');
        }
        $this->update();
        
        // Check for recurrent
        if($evtcl->setRec == 'Yes'){
             $rec_value = $evtcl->recurrent;
             if(!empty($rec_value) && preg_match('/^[0-9]+$/',$rec_value) && $rec_value != 0 ){
                // Add to recurrent
                $_SESSION['RecurrentInvoice']->updateRecurrentInvoice($this->idinvoice,$evtcl->recurrent,$evtcl->frequency,$evtcl->fields["datecreated"]);
             }else{
                $_SESSION['in_page_message'] = _("Recurrent Value is not correct.To set as Recurrent edit the invoice and enter numeric value");
             }
        }else{
            // Remove Recurent
            $_SESSION['RecurrentInvoice']->deleteRecurrentInvoice($this->idinvoice);
        }  
    
    }

    

    /*function setInvoiceCalculations($idinvoice = ""){
        if($idinvoice == ""){ $idinvoice = $this->idinvoice; }
        $q = new sqlQuery($this->getDbCon());
        $q->query("select sum(total) as amt_total from invoiceline where idinvoice = ".$idinvoice.
                  " group by idinvoice"
                  );
        $q->fetch();
        $total = $q->getData("amt_total");
        $this->getId($idinvoice);
        $this->setApplyRegistry(false, "Form");
        $amount = $this->amount;
        $net_total_with_tax_dis = $total;
        if($this->discount){
            $dis_amt = $net_total_with_tax_dis*$this->discount/100;
            $net_total = $net_total_with_tax_dis - $dis_amt;
        }else{
            $dis_amt = 0;
            $net_total = $net_total_with_tax_dis;
        }
        if($this->tax != ''){
            $tax_amt = $net_total*$this->tax/100;
            $net_total = $net_total + $tax_amt;
        }
      
        $amt_due = floatval($net_total) - floatval($amount);

		if($amt_due == 0.00 || $amt_due == 0) {
			$paid_date = ", datepaid = '".date("Y-m-d")."' ";
		} else {
			$paid_date = "";
		}

         $q_update = new sqlQuery($this->getDbCon());
         $q_update->query("update ".$this->table." set
                            sub_total = '$total',
                            net_total = '$net_total',
                            amt_due = '$amt_due'
							".$paid_date."
							where idinvoice = ".$idinvoice
                          ); 
    }*/

     /**
       * Function to set all the invoice related calculated data in invoice
       * @param integer $idinvoice 
    */
  
    function setInvoiceCalculations($idinvoice = ""){
            if($idinvoice == ""){ $idinvoice = $this->idinvoice; }
            $total = 0;
            $total_discount = 0 ;
            $total_tax = 0 ;
            $net_total = 0;
            $line_total = 0;
            $this->getId($idinvoice);
            $this->setApplyRegistry(false, "Form");
            $amount = $this->amount;
            $q = new sqlQuery($this->getDbCon());
            $q->query("Select * from invoiceline where idinvoice = ".$idinvoice);
            if($q->getNumRows()){
                while($q->fetch()){
                    $line_total += floatval($q->getData("price"))*floatval($q->getData("qty"));
                    $total += $q->getData("total");
                    $total_discount += $q->getData("discounted_amount");
                    $total_tax += $q->getData("taxed_amount");
                }
            }
            $net_total = $total;
            if($this->tax != ''){
                  $tax_amt = $net_total*$this->tax/100;
                  $net_total = $net_total + $tax_amt;
                  $total_tax +=$tax_amt;
            }

            $amt_due = floatval($net_total) - floatval($amount);
            if($amt_due == 0.00 || $amt_due == 0) {
                $paid_date = ", datepaid = '".date("Y-m-d")."' ";
            } else {
                    $paid_date = "";
            }
            $q_update = new sqlQuery($this->getDbCon());
            $q_update->query("update ".$this->table." set
                            sub_total = '$line_total',
                            net_total = '$net_total',
                            total_discounted_amt = '$total_discount',
                            total_taxed_amount = '$total_tax',
                            amt_due = '$amt_due'
                            ".$paid_date."
                            where idinvoice = ".$idinvoice
                          ); 
    }


    /**
      * Method getting total line tax for the invoice
      * @param integer $idinvocie
      * @returns boolean if no amount added else line_taxed_total 
    */
    function getTotalLineTax($idinvoice){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select sum(taxed_amount) as line_taxed_total from invoiceline where idinvoice = ".$idinvoice.
                  " group by idinvoice"
                  );
        $q->fetch();
        if($q->getData("line_taxed_total") > 0 ){
              return $q->getData("line_taxed_total");
        }else{
              return false ;
        }
    }

    /** 
      * Temp method to fix the invoices
      * Not a part of the Application 0.6 V anymore
    */
    function InvoiceFix($idinvoice){
        $total = 0;
        $total_discount = 0 ;
        $total_tax = 0 ;
        $net_total = 0;
        $line_total = 0;
        $this->getId($idinvoice);
        $this->setApplyRegistry(false, "Form");
        $amount = $this->amount;
        $q_line = new sqlQuery($this->getDbCon());
        $q_line_upd = new sqlQuery($this->getDbCon());
        $q_line->query("Select * from invoiceline where idinvoice = ".$idinvoice);
        if($q_line->getNumRows()){
            while($q_line->fetch()){            
                  $line_total += floatval($q_line->getData("price"))*floatval($q_line->getData("qty"));
                  $line_sub_tot = floatval($q_line->getData("price"))*floatval($q_line->getData("qty"));
                  if($this->discount){
                      $discount = $line_sub_tot*$this->discount/100;
                      $tot = $line_sub_tot - $discount;
                      $total_discount += $discount;
                  }else{
                      $discount = 0;
                      $tot = $line_sub_tot ; 
                  }
                  if($q_line->getData("line_tax") != '' || $q_line->getData("line_tax") != 0 || $q_line->getData("line_tax") != 0.00){
                            $taxed_amt = $tot*$q_line->getData("line_tax")/100;
                            $tot = $tot + $taxed_amt ;  
                            $total_tax += $taxed_amt;
                 }
                 $total += $tot;
                 $q_line_upd->query("update invoiceline set total = '$tot',taxed_amount='$taxed_amt',
                                    discounted_amount='$discount' 
                                    where idinvoiceline = ".$q_line->getData("idinvoiceline"));
            }
          
            $net_total = $total;
            if($this->tax != ''){
                  $tax_amt = $net_total*$this->tax/100;
                  $net_total = $net_total + $tax_amt;
                  $total_tax +=$tax_amt;
            }
            $amt_due = floatval($net_total) - floatval($amount);
            $q_update = new sqlQuery($this->getDbCon());
            echo "<br />Query : "."update ".$this->table." set
                                sub_total = '$line_total',
                                net_total = '$net_total',
                                total_discounted_amt = '$total_discount',
                                total_taxed_amount = '$total_tax',
                                amt_due = '$amt_due'
                                where idinvoice = ".$idinvoice;
            $q_update->query("update ".$this->table." set
                                sub_total = '$line_total',
                                net_total = '$net_total',
                                total_discounted_amt = '$total_discount',
                                total_taxed_amount = '$total_tax',
                                amt_due = '$amt_due'
                                where idinvoice = ".$idinvoice
                              ); 

        }
        

    }

    
    /**
      *  Event Method for the Invoice calculations.
      * @param object $evtcl
    */
    function eventSetInvoiceCalculation(EventControler $evtcl){
        if($evtcl->id){ // While updating Invoice we are sending the idinvoice as an hidden field
            $this->setInvoiceCalculations($evtcl->id);
            $go_to = "/Invoice/".$evtcl->id;
        }else{  // While adding a new Invoice get the last insterted id and then do the calculation
            $this->setInvoiceCalculations($_SESSION['InvoiceEditSave']->getPrimaryKeyValue()); 
            $go_to = "/Invoice/".$_SESSION['InvoiceEditSave']->getPrimaryKeyValue();
        }
        $evtcl->setDisplayNext(new Display($go_to));
    }
    
    /**
      * This method is executed while an ivoice is edited.
      * The purpose of the method is since every newly created invoice now
      * will have a idcontact and if user wants to change some contact/company
      * user should be able to do so.So while editing it will check if the value from the
      * invoice table is not same as that is set hidden after a contact is changed from 
      * autosuggest then update the idcompany to the new contact's company on invoice
      * table
      * @param object $evtcl
    */
    function eventCheckContactChanged(EventControler $evtcl){
        if($evtcl->fields["idcontact"]!= $evtcl->contact){
            $do_contact = new Contact();
            $do_contact->getId($evtcl->fields["idcontact"]);
            if($do_contact->idcompany){
                $q = new sqlQuery($this->getDbCon());
                $q->query("update ".$this->table. " 
                  set idcompany = ".$do_contact->idcompany." Where ".$this->primary_key." =".$evtcl->id);
            }  
        }
    }

    /**
      * Function to generate Unique invoice num for each user in sequence
      * @param integer $iduser
      * @return integer $num  // This could be changed to a character padding as well by minor changes
    */

    function getUniqueInvoiceNum($iduser = ""){
        if($iduser == ""){$iduser = $_SESSION['do_User']->iduser;}
        $q = new sqlQuery($this->getDbCon());
        $q->query("select max(num) as num from ".$this->table." where iduser = ".$iduser." AND status <> 'Quote'");
        if($q->getNumRows()){
          $q->fetch();
          $num = $q->getData("num");
          $num = $num + 1;
        }else{
          $num = $this->invoice_num;
        }
        //echo $num;exit;
        //echo $num;exit;
        return $num;
    }

    /**
      * padding leading 0's in the invoice number
      * This method is deprecated as the invoice number will just have the 
      * invoice number without leading 0s
      * @param integer $num
      * @return $num
    */

    function setInvoiceFormat($num){
      $len_num = strlen($num);
      if( $len_num <  $this->max_len_invoice_num ){
          $num = str_pad($num, $this->max_len_invoice_num, "0", STR_PAD_LEFT);
      }
      return $num;
    }

     /**
       * Generate the Add Quote Form
       * @param string  $nextPage
       *** Not a part of V 0.6 ***
     */
     function generateAddQuoteForm($nextPage){
        $errPage = $nextPage;
        $this->setRegistry("ofuz_add_quote");
        $f_quoteForm = $this->prepareSavedForm("ofuz_add_quote");
        $f_quoteForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
       // $f_taskForm->addEventAction($this->getObjectName()."->eventAddInvoiceLine", 1010);
        $f_quoteForm->setAddRecord();
        //$f_quoteForm->setUrlNext($nextPage);
        $f_quoteForm->addParam("goto", $nextPage);
        $f_quoteForm->setForm();
        $f_quoteForm->execute();
    }

     /** 
      * Function to generate the Ajax suggestion for the invoice
      * line item.
      * Return the HTML containing the items
      * @param object $evtcl
    */

    function eventAjaxItemSuggestion(EventControler $evtcl){
      $html = '';
      if(strlen($evtcl->text) > 0){
          $q = new sqlQuery($this->getDbCon());
          $q->query("SELECT distinct item 
                FROM invoiceline
                LEFT JOIN invoice ON invoiceline.idinvoice = invoice.idinvoice
                WHERE invoice.iduser =".$_SESSION['do_User']->iduser." AND item like '".$evtcl->text."%'
                Order by item");
          ;
          if($q->getNumRows()){
              while($q->fetch()){
                  $html .= '<option value="0" style="padding: 8px; background-color: #f7f7fa; border-bottom: solid 1px #dddddd;">'.$q->getData("item").'</option>';
              }
           $evtcl->addOutputValue($html);
          }else{  $evtcl->addOutputValue('No'); }
      }else{  $evtcl->addOutputValue('No'); }
    }

    /**
      * Function to get the total line amount
      * @param integer $idinvoice 
      * @param float $dis 
    */
    function getTotalLineAmount($idinvoice,$dis=""){
         $q = new sqlQuery($this->getDbCon());
         $q->query("select sum(total) as amt_total from invoiceline where idinvoice = ".$idinvoice.
                  " group by idinvoice"
                  );
         $q->fetch();
         $total = $q->getData("amt_total");
         if($dis){
            $dis_amt = $total*$dis/100;
            $total = $total- $dis_amt;
         } 
        return $total;
    }

    /**
        Function to get the total dues 
        *** Method is depricated with getDueAmount() ***
    */
    function getTotalDue($idinvoice,$amount,$dis=""){
        $tot_line_amt = $this->getTotalLineAmount($idinvoice,$dis);
        return $tot_line_amt-$amount;
    }

    /**
      * To get the due amout of the invoice
      * @param integer $idinvoice
      * @return amt_due
    */
    function getDueAmount($idinvoice){
        $data = array();
        $q = new sqlQuery($this->getDbCon());
        $q->query("select amt_due from ".$this->table. " Where idinvoice = ".$idinvoice);
        $q->fetch();
        return $q->getData("amt_due");
        
    }

    /**
      * Function to get the total discount
      * @param integer $idinvoice 
      * @param float $dis
    */
    function getDiscountedAmt($idinvoice,$dis=""){
          if($dis){
                $q = new sqlQuery($this->getDbCon());
                $q->query("select sum(total) as amt_total from invoiceline where idinvoice = ".$idinvoice.
                  " group by idinvoice"
                  );
                $q->fetch();
                $total = $q->getData("amt_total");
                $dis_amt = $total*$dis/100;
                return $dis_amt;
          }else{ return 0; }
    }

    /**
      * Function to get all the invoice related calculated data and return as
      * an aray.
      * @param integer $idinvoice
      * @param float $amount
      * @param float $dis
    */
    function getInvoiceCalculations($idinvoice,$amount,$dis=""){
        $data = array();
        $q = new sqlQuery($this->getDbCon());
        $q->query("select sum(total) as amt_total from invoiceline where idinvoice = ".$idinvoice.
                  " group by idinvoice"
                  );
        $q->fetch();
        $total = $q->getData("amt_total");
        $data["line_total"] = $total;
        if($dis){
            $dis_amt = $total*$dis/100;
            $data["discounted_amt"] = $dis_amt;
            $data["total_after_discount"] = $total - $dis_amt;
        }else{
            $data["discounted_amt"] = 0;
            $data["total_after_discount"] = $total ;
        }
        $data["total_due_amt"] = $data["total_after_discount"] - $amount;

        return $data;
    }

    

    /**
      * Function to send the invoice to the contact with the email.
      * @param object $evtcl 
    */

    function eventSendInvoiceByEmail(EventControler $evtcl){ 
       // echo $this->status;exit;
        $idinvoice = $evtcl->id;
        $idcontact = $evtcl->contact;
//echo $idinvoice.":".$idcontact;exit();
		$this->sendInvoiceByEmail($idinvoice,$idcontact,$_SESSION['do_User']->iduser);
        /*$do_contact = new Contact();
        $do_user_rel = new UserRelations();
        $do_contact->getId($idcontact);
        $contact_name = $do_contact->firstname.' '.$do_contact->lastname;
        if($do_contact->email_address != '') {
            $contact_email = $do_contact->email_address;
        } else { 
            $ContactEmail = $do_contact->getChildContactEmail();
            if($ContactEmail->getNumRows()) {
                  $contact_email = $ContactEmail->email_address;
            }
        }
        if($this->discount) {
          $dis = $this->discount;
        } else {
          $dis = "";
        }
        if($contact_email){
            $total_due = $this->amt_due;
            $total_due = $this->viewAmount($total_due);
            $email_template = new EmailTemplate("ofuz_send_invoice");
            $email_template->setSenderName($_SESSION['do_User']->getFullName());
            $email_template->setSenderEmail($_SESSION['do_User']->email);
            $signature = $_SESSION['do_User']->company.'<br />'.$_SESSION['do_User']->getFullName();
            $invoice_url = $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_user_rel->encrypt($idinvoice).'/'.$do_user_rel->encrypt($idcontact);
            $email_data = Array('name' => $contact_name,
                                        'sender'=>$_SESSION['do_User']->getFullName(),
                                        'company'=>$_SESSION['do_User']->company,
                                        'description'=>$this->description,
                                        'invoice_url'=>$invoice_url,
                                        'signature'=>$signature,
                                        'amount'=>$total_due
                                        );
    
            $emailer = new Radria_Emailer();
            $emailer->setEmailTemplate($email_template);
            $emailer->mergeArray($email_data);
            $emailer->addTo($contact_email);
            $emailer->send(); 
            if($this->status != 'Quote' && $this->status != 'Paid'){
                   $this->status = 'Sent';
                   $this->update();
            }
            $_SESSION['in_page_message'] = _("Invoice is sent to ".$contact_email);
         }else { 
            $_SESSION['in_page_message'] = _("Unable to send invocie as no email id found");
         }*/
    }


    /**
      * Method to send the email
      * @param integer $idinvoice 
      * @param integer $idcontact
      * @param integer $iduser 
      * @param boolean $recurrent 
    */

    function sendInvoiceByEmail($idinvoice,$idcontact,$iduser,$recurrent = false){
        $do_contact = new Contact();
        $do_user_rel = new UserRelations();
        $this->getId($idinvoice);
        if($recurrent === true ){
          $this->sessionPersistent("do_invoice", "index.php", OFUZ_TTL);
        }
        $do_contact->getId($idcontact);
        if(!$do_contact->hasData()) return ;
        $contact_name = $do_contact->firstname.' '.$do_contact->lastname;
        $do_contact_email = $do_contact->getChildContactEmail();
        $contact_email = $do_contact_email->getDefaultEmail();
        $do_user_detail = new User();
        $do_user_detail->getId($iduser);
        if($contact_email){ 
            $total_due = $this->amt_due;
            $total_due = $this->viewAmount($total_due);
             if($recurrent){
                $do_rec_invoice = new RecurrentInvoice();
                $idrec = $do_rec_invoice->checkIfInvoiceIsInRecurrent($idinvoice);
                if($idrec){
                    $email_template = new EmailTemplate("ofuz_send_recurrent_invoice");
                    $do_rec_invoice->getId($idrec);
                    $next_due_date = $do_rec_invoice->nextdate;
                    $recurrence = $do_rec_invoice->recurrence;
                    $recurrence_type = $do_rec_invoice->recurrencetype;
                }else{
                    $email_template = new EmailTemplate("ofuz_send_invoice");
                }
            }else{
                 $email_template = new EmailTemplate("ofuz_send_invoice");
            }

            if($this->status == 'Quote'){
                $email_template = new EmailTemplate("ofuz_send_quote");
            }
            $email_template->setSenderName($do_user_detail->getFullName());
            $email_template->setSenderEmail($do_user_detail->email);
            $signature = $do_user_detail->company.'<br />'.$do_user_detail->getFullName();
            $description= $this->description;
           
           
            $invoice_url = $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_user_rel->encrypt($idinvoice).'/'.$do_user_rel->encrypt($idcontact);
            $email_data = Array('name' => $contact_name,
                                        'sender'=>$do_user_detail->getFullName(),
                                        'company'=>$do_user_detail->company,
                                        'description'=>$description,
                                        'invoice_url'=>$invoice_url,
                                        'num'=>$this->num,
                                        'signature'=>$signature,
                                        'amount'=>$total_due,
                                        'recurrence'=>$recurrence,
                                        'recurrence_type'=>$recurrence_type,
                                        'next_due_date'=>$next_due_date
                                        );
    //echo "<pre>";print_r($email_data); echo"</pre>";die();
            $emailer = new Radria_Emailer();
            $emailer->setEmailTemplate($email_template);
            $emailer->mergeArray($email_data);
            $emailer->addTo($contact_email);

            //attachment starts
            // Some bug in the PDF part it does not send the correct PDF as it says due amt is 0. Happens for the cron to send recurrent inv
            //echo '<br />Calling Method generatePDFInvoice().....ID INVOICE :: '.$idinvoice.'<br />';
            // This is fixed
            $this->generatePDFInvoice($invoice_url);
            
            $fpdf_file_name = $this->getEncryptedFileName("pdf");
            $pdfFilePath = "invoice_pdf/$fpdf_file_name";
            $pdfFile = file_get_contents($pdfFilePath);
            
            $at = $emailer->createAttachment($pdfFile);
            $at->type = 'image/pdf';
            $at->disposition = Zend_Mime::DISPOSITION_INLINE;
            $at->encoding = Zend_Mime::ENCODING_BASE64;
            $at->filename = $fpdf_file_name; 
            //attachment ends

            $emailer->send(); 

            if($this->status == 'New'){
                $this->status = 'Sent';
                $this->update();
            }
            
            if($recurrent){
                $q = new sqlQuery($this->getDbCon());
                $q->query("update invoice set status = 'Sent' where idinvoice = ".$idinvoice." Limit 1");
                $q->free();
            }
            $_SESSION['in_page_message'] = "client_invoice_sent";
            $_SESSION['in_page_message_data']['contact_email'] = $contact_email;
         }else { 
            $_SESSION['in_page_message'] = "invoice_client_email_not_found";
         }
         $do_user_detail->free();
	 

    }

    /**
      * Creates an encrypted file name with idinvoice and idcontact.
      * @param string $filetype 
      * @return string $fpdf_file_name 
    */
    function getEncryptedFileName($filetype) {
    $do_user_rel = new UserRelations(); 
    $fpdf_file_name = $do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'_'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
	    $fpdf_file_name = $fpdf_file_name.".".$filetype;
	    return $fpdf_file_name;
    }

    /**
      * Method generating the PDF for an invoice 
      * Invoice Object must be a persistent.
    */
    public function generatePDFInvoice($inv_url) {

      //echo '<br />Calling method generatePDFInvoice with Ivoice URL :: '.$inv_url.'<br />' ;
      global $invoice_url;
      $invoice_url = $inv_url;

	    //define('FPDF_FONTPATH','font/')
	    include_once('html2fpdf-3.0.2b/html2fpdf.php');
	    include_once('html2fpdf-3.0.2b/fpdf_ofuz.php');
	    //echo '<br /> Checking The Session Obj <br />';
	    //print_r($_SESSION['do_invoice']);
	    $do_notes = new ContactNotes($GLOBALS['conx']);
	    $do_contact = new Contact($GLOBALS['conx']);
	    $do_company = new Company($GLOBALS['conx']);
	    $do_task = new Task($GLOBALS['conx']);
	    $do_task_category = new TaskCategory($GLOBALS['conx']);
	    $do_contact_task = new Contact();
	    $invoice_access = true;

	    if($_SESSION['do_invoice']->discount){
	    $dis = $_SESSION['do_invoice']->discount;
	    }else{$dis = "";}

	    $do_user_detail = new User();
	    $do_user_detail->getId($_SESSION['do_invoice']->iduser);
	    $user_settings = $do_user_detail->getChildUserSettings();    
	    if($user_settings->getNumRows()){
		    while($user_settings->next()){
			    if($user_settings->setting_name == 'invoice_logo' &&  $user_settings->setting_value != ''){
				    $_SESSION['do_invoice']->inv_logo =  $user_settings->setting_value ;
			    }
			    if($user_settings->setting_name == 'currency' &&  $user_settings->setting_value != ''){
				    $currency =  explode("-",$user_settings->setting_value) ;
				    $_SESSION['do_invoice']->currency_iso_code = $currency[0];
				    $_SESSION['do_invoice']->currency_sign = $currency[1];
				    $_SESSION['do_invoice']->setCurrencyDisplay() ;
				    $_SESSION['do_invoice']->getCurrencyPostion() ;
			    }
			    if($user_settings->setting_name == 'inv_date_format' &&  $user_settings->setting_value != ''){
				$_SESSION['do_invoice']->inv_dd_format = $user_settings->setting_value;
			    }
		    }
	    }

	    $pdf=new ofuzFPDF();
	    $pdf->AddPage();
	    
	    $do_user_rel = new UserRelations();
	    $fpdf_file_name = $do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'_'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
	    $fpdf_file_name = $fpdf_file_name.".pdf";
	    
	    $fpdf_file_path = "invoice_pdf/";
	    
	    $body1 = '';
	    
	    
	    if($_SESSION['do_invoice']->status == 'Paid'){
		    $color = "#009900";
	    }elseif($_SESSION['do_invoice']->status == 'Partial'){
		    $color = "#EA8484";
	    }elseif($_SESSION['do_invoice']->status == 'Sent'){
		    $color = "#677CDF";
	    }
	    else{  
		    $color ="#000000";
	    }
	    
	    
	    if( $_SESSION['do_invoice']->inv_logo == ''){
		    //$logo = 'files/sqllogo_small.jpg';
		    //$pdf->InitLogo($logo);
	    }else{
      $path = 'files/'.$_SESSION['do_invoice']->inv_logo;
      $filename = strtolower($_SESSION['do_invoice']->inv_logo) ; 
      $exts = split("[/\\.]", $filename) ; 
      $n = count($exts)-1; 
      $exts = $exts[$n];
      if(file_exists($path) && $exts!="gif")
        $logo = 'files/'.$_SESSION['do_invoice']->inv_logo;
		    //$pdf->InitLogo($logo);
	    }
	    
	    //$pdf->ln(8);
	    
	    if($_SESSION['do_invoice']->status == 'Quote'){
				    $inv .= _('Q U O T E');
	    }else{
				    $inv .= _('I N V O I C E');
	    }
	    $current_date = date("m-d-Y");
	    
	    $body1 .= '<table width="700px" cellspacing="0" cellpadding="0">';
	    $body1 .= '<tr><td>';
	    $body1 .= '<img src="'.$logo.'" />';
	    $body1 .= '</td></tr>';
	    $body1 .= '</table>';
	    $body1 .= '<table width="700px" cellspacing="0" cellpadding="0">';
	    $body1 .= '<tr><td align="left">'.$current_date.'</td><td align="right">'.$inv.'</td></tr>';
	    $body1 .= '<tr><td colspan="2">&nbsp;</td></tr>';
	    $body1 .= '<tr><td valign="top" width="200px">From : ';
	    $body1 .= '<br />'.$_SESSION['do_invoice']->Only1br($_SESSION['do_invoice']->getInvoiceSenderAddr($do_user_detail));
	    $body1 .= '</td>';
	    $body1 .= '<td valign="top" width="500px">To : ';
	    $body1 .= '<br />'.$_SESSION['do_invoice']->Only1br($_SESSION['do_invoice']->invoice_address);
	    $body1 .= '</td>';
	    $body1 .= '</tr>';
	    $body1 .= '</table>';
	    $body1 .= '<table width="700px">';
	    $body1 .= '<tr><td width="550px" align="right">';
	    
	    if($_SESSION['do_invoice']->status == 'Quote'){
		    $body1 .= _('Quote #');
	    }else{
		    $body1 .= _('Invoice #');
	    }
	    $body1 .= '</td>';
	    
	    $body1 .= '<td width="150px" align="right">'.$_SESSION['do_invoice']->num.'</td></tr>';
	    
	    $body1 .= '<tr>';
	    
	    $body1 .= '<td width="550px" align="right">'._('Date Created').'</td>';
	    
	    $body1 .= '<td width="150px" align="right">'.$_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->datecreated).'</td></tr>';
	    
	    $body1 .= '<tr>';
	    $body1 .= '<td width="550px" align="right">'._('Due Date').'</td>';
	    $body1 .= '<td width="150px" align="right">'.$_SESSION['do_invoice']->getInvFormattedDate($_SESSION['do_invoice']->due_date).'</td></tr>';
	    $body1 .= '<tr>';
	    $body1 .= '<td width="550px" align="right">'._('Amount Due').'</td>';
	    $body1 .= '<td width="150px" align="right">';
	    $body1 .= $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due);
	    $body1 .= '</td></tr>';
	    $body1 .= '<tr>';
	    $body1 .= '<td width="550px" align="right">'._('Terms').'</td>';
	    $body1 .= '<td width="150px" align="right">'.$_SESSION['do_invoice']->invoice_term.'</td>';
	    $body1 .= '</tr>';
	    $body1 .= '</table>';
	    
	    $body1 .= '<table width="700"><tr><td>';
	    $body1 .= nl2br($_SESSION['do_invoice']->description);
	    $body1 .= '</td></tr>';
	    $body1 .= '</table>';
	    
	    $body1 .= '<table width="700" border="1">';
	    $body1 .= '<tr>';
	    $body1 .= '<td width="200" align="center"><b>'._('Item').'</b></td>';
	    //$body1 .= '<td width="150" align="center"><b>Description</b></td>';
	    $body1 .= '<td width="125" align="center"><b>'._('Price').'</b></td>';
	    $body1 .= '<td width="125" align="center"><b>'._('Quantity').'</b></td>';
	    $show_tax_amout = false ; 
     $line_tax_amt = $_SESSION['do_invoice']->getTotalLineTax($_SESSION['do_invoice']->idinvoice);
	    if($line_tax_amt !== false){
		    $show_tax_amout = true;
		    $body1 .= '<td width="125" align="center"><b>'._('Tax').'(%)</b></td>';
	    }
	    
	    $body1 .= '<td width="125" align="center"><b>'._('Total').'</b></td>';
	    $body1 .= '</tr>';
	    
	    $do_invoice_line = $_SESSION['do_invoice']->getChildinvoiceline();
	    $price = 0;
	    while($do_invoice_line->next()){ 
	    
		    $body1 .= '<tr><td width="200">'.$do_invoice_line->item.'</td>';
		    //$body1 .= '<td width="100">'.nl2br($do_invoice_line->description).'</td>';
		    $body1 .= '<td width="125" align="right">';
		    $body1 .= $_SESSION['do_invoice']->viewAmount($do_invoice_line->price);
		    $body1 .= '</td>';
		    $body1 .= '<td width="125" align="right">';
		    $body1 .= $do_invoice_line->qty;
		    $body1 .= '</td>';
		    if($show_tax_amout === true ){
			    $body1 .= '<td width="125" align="right">';
			    $body1 .= $do_invoice_line->line_tax;
			    $body1 .= '</td>';
		    }
		    $line_sub_tot = floatval($do_invoice_line->qty*$do_invoice_line->price);
		    $body1 .= '<td width="125" align="right">';
		    $body1 .= $_SESSION['do_invoice']->viewAmount($line_sub_tot);
		    $body1 .= '</td></tr>';
		    if($do_invoice_line->description) {
			    $body1 .= '<tr><td colspan="5">'.nl2br($do_invoice_line->description).'</td></tr>';
		    }
	    } 
	    $body1 .= '</table>';
	    
	    $body1 .= '<table width="700px">';
	    $body1 .= '<tr><td colspan="2">&nbsp;</td></tr>';
	    $body1 .= '<tr><td width="550px" align="right"><b>'.('Subtotal').':</b></td>';
	    $body1 .= '<td width="150px" align="right"><b>'.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->sub_total).'</b></td></tr>';
	    
	    if($_SESSION['do_invoice']->discount != ''){
		    $body1 .= '<tr><td width="550px" align="right">'._('Discount').' -'.$_SESSION['do_invoice']->discount.'% :</td>';
		    $body1 .= '<td width="150px" align="right">';
		    $body1 .=  $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_discounted_amt);
		    $body1 .= '</td></tr>';
	    } 
	    if($_SESSION['do_invoice']->total_taxed_amount > 0 ){
		    $body1 .= '<tr><td width="550px" align="right">'._('Tax').' + :</td>';
		    $body1 .= '<td width="150px" align="right">';
		    //$body1 .=  $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->total_taxed_amount);
      $body1 .=  $_SESSION['do_invoice']->viewAmount($line_tax_amt);
		    $body1 .= '</td></tr>';
      if($_SESSION['do_invoice']->tax > 0 ){ 
          $other_tax = $_SESSION['do_invoice']->total_taxed_amount - $line_tax_amt;
          $body1 .= '<tr><td width="550px" align="right">'._('Tax2').'  '.$_SESSION['do_invoice']->tax.'% :</td>';
          $body1 .= '<td width="150px" align="right">';
          $body1 .=  $_SESSION['do_invoice']->viewAmount($other_tax);
          $body1 .= '</td></tr>';
      }
	    }
	    $body1 .= '<tr><td width="550px" align="right"><b>'._('Total').' :</b></td><td width="150px" align="right">';
	    $body1 .= '<b>'. $_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->net_total).'</b>';
	    $body1 .= '</td></tr>';
	    $body1 .= '<tr><td width="550px" align="right"><b>'._('Amount Paid').':</b></td><td width="150px" align="right">';
	    $body1 .= '<b> '.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amount).'</b>';
	    $body1 .= '</td></tr><tr><td width="550px" align="right"><b>'._('Balance Due').' :</b></td>';
	    $body1 .= '<td width="150px" align="right"><b>'.$_SESSION['do_invoice']->viewAmount($_SESSION['do_invoice']->amt_due).'</b>';
	    $body1 .= '</td></tr>';
	    $body1 .= '</table>';
	    
	    $pdf->WriteHTML($body1);
	    $pdf->Output($fpdf_file_path.$fpdf_file_name);
	    unset($pdf);
    }


    /**
      * Function adding payment for the invocie.
      * @param object $evtcl 
    */

    function eventAddPayment(EventControler $evtcl){ 
	// Replace the , ' ' $ from the amount 
        $evtcl->payment_amt = preg_replace('/[\$,\' \']/', '', $evtcl->payment_amt);
        if(preg_match('/^[0-9]+$/',$evtcl->payment_amt) || preg_match('/^[0-9]+\.[0-9]*$/',$evtcl->payment_amt)){
	   
	    //$evtcl->payment_amt = (float)$evtcl->payment_amt;
	    //echo $evtcl->payment_amt ;exit;
            if($this->discount) {
              $dis = $this->discount;
            } else {
              $dis = "";
            }
            //$inv_cal = $this->getInvoiceCalculations($this->idinvoice,$this->amount,$dis);
            if($evtcl->payment_amt == '0.00'){
              $_SESSION['in_page_message'] = _("Invalid amount has been entered");
            }elseif(round($evtcl->payment_amt,2) > round($this->amt_due,2) ){
                $_SESSION['in_page_message'] = _('The payment received is greater than the invoice amount, do you want to apply '.$this->viewAmount($this->amt_due).' to the this invoice and the rest of the amount on other invoices ? ');
                $_SESSION['in_page_message_inv_amt_more'] = 'Yes';
                $_SESSION['payment_amt'] = $evtcl->payment_amt;
                $_SESSION['ref_num'] = $evtcl->payment_ref_num;
                $evtcl->setDisplayNext(new Display("invoice_alert.php"));
            }else{
                $this->amount =round(round($this->amount,2)+round($evtcl->payment_amt,2),2);
                if(round($evtcl->payment_amt) == round($this->amt_due) ){
                    $this->status = 'Paid';
					
                }else{
                    $this->status = 'Partial';
                }
                $this->update();
                $this->setInvoiceCalculations(); // after the payment is added set the invoice calculation also
                $_SESSION['in_page_message'] = _("Payment has been added");
                // If the user is paying for the same payment to multiple invoices
                if($_SESSION['last_paylogid'] != '' && $_SESSION['extra_amt'] != ''){
                      $idpayment_log = $_SESSION['last_paylogid'];
                }else{
                     $do_pay_log = new PaymentLog();
                     $do_pay_log->addPaymentLog($evtcl->payment_ref_num,"Manual",$this->idinvoice,$evtcl->payment_amt);
                     $idpayment_log = $do_pay_log->getPrimaryKeyValue();
                }
                $do_payment_inv = new PaymentInvoice();
                $do_payment_inv->addPaymentInvoice($idpayment_log,$this->idinvoice,$evtcl->payment_amt);
                
                $_SESSION['show_past_due'] = '';
                if($_SESSION['extra_amt'] != ''){
                	$extra_amt = $_SESSION['extra_amt'] - $evtcl->payment_amt;
                }
                if($extra_amt == 0 || $extra_amt == 0.00 || $extra_amt < 0 ){
                    $_SESSION['extra_amt'] = '';
                }else{    
                    $_SESSION['extra_amt'] = $extra_amt;
                }
  
                
                 /*
                  Lets check if the invoice has an call back URL and process that
                 */
                //$do_inv_callback = new InvoiceCallback();
                //$do_inv_callback->processCallBack($this->idinvoice,$this->num,$evtcl->payment_amt,$_SESSION['do_User']->iduser,"Manual",$evtcl->payment_ref_num);
            }
        }else{  
           $_SESSION['in_page_message'] = _("Invalid amount has been entered");
        }
    }

    /**
      * Method deleting payment for an invoice
      * @param integer  $idinvoice 
      * @param float $amt 
    */
    function deletePaymentFromInvoice($idinvoice,$amt){
      $this->getId($idinvoice);
      $this->amount = $this->amount - $amt;
      if($this->amount == '0.00' || $this->amount == '0'){
        $this->status = 'New';
      }else{
          $this->status = 'Partial';
      }
      $this->update();
      $this->setInvoiceCalculations($idinvoice);
    }

    /**
      * Event Method Adding Multiple Payment
      * @param object $evtcl
    */
    function eventAddMultiPayment(EventControler $evtcl){ 
        $idinvoice = $evtcl->id;
        $idcontact = $evtcl->idcontact;
        $amt = $evtcl->amt;
        $ref_num = $evtcl->ref_num;
        $this->addMultiplePayment($idinvoice,$idcontact,$amt,$ref_num);
        $evtcl->setDisplayNext(new Display("invoices_unpaid.php"));
    }

    /**
      * Event Method Cancel Multi Payment for a session
      * @param object $evtcl
    */
    function eventCancelMultiPayment(EventControler $evtcl){ 
          $_SESSION['show_past_due'] = '';
          $_SESSION['extra_amt'] = '';
          $_SESSION['last_paylogid'] = '';
          $_SESSION['ref_num'] = '';
          $evtcl->setDisplayNext(new Display("invoices.php"));
    }    


    /**
      * Event method to set the extra amount to the invoice
      * @param object $evtcl
    */
    function eventSetApplyExtraAmount(EventControler $evtcl){
         $do_payment_invoice = new PaymentInvoice();
         if($do_payment_invoice->getExtraAmoutNotPaid() !== false ){
                $_SESSION['extra_amt'] = $do_payment_invoice->extra_amt ;
                $_SESSION['ref_num'] = $do_payment_invoice->ref_num ;
         }
    }

    /**
      * Method add multiple payment
      * @param integer $idinvoice
      * @param integer $idcontact
      * @param float $amt
      * @param string $ref_num 
    */
    function addMultiplePayment($idinvoice,$idcontact,$amt,$ref_num){
          $this->getId($idinvoice);
          $due_amt = $this->amt_due;
          $extra_amt = $amt -$due_amt; //echo $extra_amt;exit;
          $this->amount = round(round($this->amount,2)+round($this->amt_due,2),2);
          //$this->amount = $due_amt;
          $this->status = 'Paid';
          $this->update();  
          $this->setInvoiceCalculations($idinvoice);
          if($_SESSION['last_paylogid'] != ''){
              $idpayment_log = $_SESSION['last_paylogid'];
          }else{
              $do_pay_log = new PaymentLog();
              $do_pay_log->addPaymentLog($ref_num,"Manual",$idinvoice,$amt);
              $idpayment_log = $do_pay_log->getPrimaryKeyValue();
          }
          $do_payment_inv = new PaymentInvoice();
          $do_payment_inv->addPaymentInvoice($idpayment_log,$idinvoice,$due_amt);
            /*
            Lets check if the invoice has an call back URL and process that
            */
          $do_inv_callback = new InvoiceCallback();
          $do_inv_callback->processCallBack($idinvoice,$this->num,$due_amt,$_SESSION['do_User']->iduser,"Manual",$ref_num);
          $_SESSION['show_past_due'] = 'Yes';
          $_SESSION['extra_amt'] = $extra_amt;
          $_SESSION['last_paylogid'] = $idpayment_log;
          
    }

    /**
      * Function to update the Payment
      * Method is called when a payment is done VIA CC
      * @param float $total
    */
    function updatePayment($total){
        if($this->discount) {
          $dis = $this->discount;
        } else {
          $dis = "";
        }
        //$inv_cal = $this->getInvoiceCalculations($this->idinvoice,$this->amount,$dis);
        if($evtcl->payment_amt == '0.00'){
          $_SESSION['in_page_message'] = _("Invalid amount has been entered");
        }elseif(round($evtcl->payment_amt,2) > round($this->amt_due,2) ){
          $_SESSION['in_page_message'] = _("Amount is greater than the total due");
        }else{
            $this->amount =round(round($this->amount,2)+round($total,2),2);
            if(round($total) == round($this->amt_due) ){
                $this->status = 'Paid';
            }else{
                $this->status = 'Partial';
            }
            $this->update();  
            $this->setInvoiceCalculations();
        }
    }
    
    /**
      * Event Method changing a quote into Invoice
      * @param object $evtcl 
    */
    // Change the Quote to Invoice
    function eventChangeQuoteToInvoice(EventControler $evtcl){ 
        $idinvoice = $evtcl->id;
                
        if($evtcl->approve == 'Yes'){ // When client approves the Quote
            // When client approves the quote we can send some email
            //$this->num = $this->getUniqueInvoiceNum($this->iduser);
            $inv_num = $this->getUniqueInvoiceNum($evtcl->iduser);
            
            
        }else{ 
            //$this->num = $this->getUniqueInvoiceNum();
            $inv_num = $this->getUniqueInvoiceNum();  
        }
        $q = new sqlQuery($this->getDbCon());
        $q->query("Update invoice set status = 'New', num = '".$inv_num."' where idinvoice = ".$idinvoice);
        if($evtcl->approve == 'Yes'){
            $this->sendInvoiceApproveReject($evtcl->iduser,$idinvoice,"Accept");
        }        

    }

    /**
      * Method to generate the Authorized.NET payment for to receive the CC info.
      * Chnaged the method to get the specified Payment Form based on the Payment selection on invoice setting current option is (Authnet,Stripe)
      * @param from either Authnet or Stripe
      * @param float $due
      * @return string $Authnet_form 
    */
    function prepareAuthnetForm($form="Authnet",$due){
        if($form === "Authnet"){
		$Authnet_form = '<table>';
        if ($this->user_edit_amount !== False) {
            $Authnet_form .= '
                                  <tr>
                                      <td><B>'._('Total Amount').':</B></td>
                                      <td><input type="text" name="tot_amt" MAXLENGTH=16 value = "'.(float)$due.'"></td>
                                      <td></td>
                                  </tr>';
            } 
            $Authnet_form .= '					
                                  <tr>
                                      <td><B>'._('Please Select Your Payment Method').':</B></td>
                                      <td>
                                          <select name="payment_type">
                                              <option value="AmericanExpress">'._('American Express').'</option>
                                              <option value="MasterCard">'._('Master Card').'</option>
                                              <option value="Visa">'._('Visa').'</option>
                                          </select>
                                      </td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td><B>'._('Credit Card Number').':</B></td>
                                      <td><input type="text" name="cc" MAXLENGTH=16></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td><B>'._('CVV number').':</B></td>
                                      <td><input type="text" name="cvv"></td>
                                      <td>
                                          <a href="http://www.sti.nasa.gov/cvv.html" target="_blank">&#160;What is CVV?</a>
                                      </td>
                                  </tr>
                                  <tr>
                                      <td><B>'._('Card Expiration Month - Year').':</B></td>
                                      <td>
                                          <select name="expire_month">
                                              <option value="01">'._('January').'</option>
                                              <option value="02">'._('February').'</option>
                                              <option value="03">'._('March').'</option>
                                              <option value="04">'._('April').'</option>
                                              <option value="05">'._('May').'</option>
                                              <option value="06">'._('June').'</option>
                                              <option value="07">'._('July').'</option>
                                              <option value="08">'._('August').'</option>
                                              <option value="09">'._('September').'</option>
                                              <option value="10">'._('October').'</option>
                                              <option value="11">'._('November').'</option>
                                              <option value="12">'._('December').'</option>
                                          </select>

                                          <select name="expire_year">';

                                          // Set the year to be the current year up to ten years from now
                                          for ($i = date("Y"); $i < date("Y") + 10; $i++) {
                                              
                                              $Authnet_form .= "<option value=\"" . substr($i, -2) . "\">" . $i . "</option>";
                                          }

                                     $Authnet_form .= '</select>
                                      </td>
                                      <td></td>
                                  </tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                  </tr>
                                  </tr>
                                   <tr>
                                      <td></td>
                                      <td><input type="submit" value ="'._('Submit').'"></td>
                                      <td></td>
                                  </tr>
                                </table>
                              ';
                         $htmlform = $Authnet_form;     
                      } elseif($form === "stripe"){
						$stripe_form = '<table>';
						  if ($this->user_edit_amount !== False) { 
						$stripe_form .= '
                                  <tr>
                                      <td><B>'._('Total Amount').':</B></td>
                                      <td><input type="text" name="tot_amt" MAXLENGTH=16 value = "'.(float)$due.'"></td>
                                      <td></td>
                                  </tr>';
						} 
						 $stripe_form .= '<tr  class="form-row">
										  <td><label for="name" class="stripeLabel"><B>'._('Your Name').':</B></label></td>
										  <td><input type="text" name="name" class="required" /></td>
										  </tr>            
					
										<tr class="form-row">
											<td><label for="email"><B>E-mail Address</B></label></td>
											<td><input type="text" name="email" class="required" /></td>
										</tr>            
					
										<tr class="form-row">
											<td><label><B>Card Number</B></label></td>
											<td><input type="text" maxlength="20" autocomplete="off" class="card-number stripe-sensitive required" /></td>
										</tr>
										
										<tr class="form-row">
											<td><label><B>CVC</B></label></td>
											<td><input type="text" maxlength="4" autocomplete="off" class="card-cvc stripe-sensitive required" /></td>
										</tr>
							
										<tr class="form-row">
											<td><label><B>Expiration</B></label></td>
											<td class="expiry-wrapper">
												<select class="card-expiry-month stripe-sensitive required">
												</select>
												<script type="text/javascript">
													var select = $(".card-expiry-month"),
														month = new Date().getMonth() + 1;
													for (var i = 1; i <= 12; i++) {
														select.append($("<option value=\'"+i+"\' "+(month === i ? "selected" : "")+">"+i+"</option>"))
													}
												</script>
															<span> / </span>
															<select class="card-expiry-year stripe-sensitive required"></select>
															<script type="text/javascript">
															var select = $(".card-expiry-year"),
															year = new Date().getFullYear();
															for (var i = 0; i < 12; i++) {
																select.append($("<option value=\'"+(i + year)+"\' "+(i === 0 ? "selected" : "")+">"+(i + year)+"</option>"))
															}
												</script>
											</td>
										</tr>
										<tr><td colspan="2"><button type="submit" name="submit-button">Submit</button></td></tr></table>'; 
            $htmlform = $stripe_form;
			}    
        return $htmlform;
    }

    /**
      *  Method to generate the PAYPAL paynow button.
      *  Sandbox Mode Gateway : https://www.sandbox.paypal.com/us/cgi-bin/webscr 
      *  Live Gateway :  https://www.paypal.com/cgi-bin/webscr
      *  @param float $due 
      *  @param integer $invnum 
      *  @param string $description 
      *  @return string $paypal_form 
        
    */
    function preparePaypalForm($due,$invnum,$description){
        $bus_email = $_SESSION['do_invoice']->paypal_business_email;
        $do_user_rel = new UserRelations(); 
        $invoice_url = $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idinvoice).'/'.$do_user_rel->encrypt($_SESSION['do_invoice']->idcontact);
        $paypal_form = '<table>
                           <form id="Paypal" name="Paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post" onsubmit="return isAmountValid();">
                                  <tr>
                                      <td><B>'._('Total Amount').':</B></td>
                                      <td><input type="text" name="amount" id="amount" MAXLENGTH=16 value = "'.(float)$due.'">
                                      </td>
                                      <td>
                                          <input type="hidden" name="item_name" value="'.$description.'" />
                                          <input type="hidden" name="quantity" value="1" />
                                          <input type="hidden" name="item_number" value="'.$_SESSION['do_invoice']->idinvoice.'" />
                                           <input type="hidden" name="notify_url" value="'.$GLOBALS['cfg_ofuz_site_https_base'].'invoice_paypal_notify.php" />
                                          <input type="hidden" name="return" value="'.$invoice_url.'" />
                                          <input type="hidden" name="cancel_return" value="'.$invoice_url.'" />
                                      </td>
                                  </tr>
                                  <tr>
                                      <td>
                                        <input type="hidden" name="cmd" value="_xclick" />
                                        <input type="hidden" name="business" value="'.$bus_email.'" />
                                      </td>
                                      <td>
                                      <input type="hidden" name="currency_code" value="'.$_SESSION['do_invoice']->currency_iso_code.'">
                                      <input type="hidden" name="upload" value="1" />
                                      <input type="hidden" name="custom" value="'.$invnum.'" /> 
                                      </td>
                                      <td></td>
                                  </tr> 
                                
                                   <tr>
                                      <td><input type="hidden" name="due_amnt" id="due_amnt" value="'.$due.'" /></td>
                                      <td><input type="submit" value ="'._('Submit').'"></td>
                                      <td></td>
                                  </tr></form>
                                </table>
                              ';
                    return $paypal_form;
        
    }


     /**
      * Event Method for Authnet Payment Process
      * test with credit card 4111111111111111
      * American Express Card Test : 378282246310005
      * @param object $evtcl 
     */
    function eventProcessAuthNetPayment(Eventcontroler $evtcl){

      $flag = true;

      if($evtcl->tot_amt > $_SESSION['do_invoice']->amt_due) {
      $_SESSION['in_page_message'] = _("The Total Amount entered is greater than the invoice amount. Please re-enter.");
      $flag = false;
      }

      if($flag) {
      // $this->Authnet_message = "";
      include_once('class/Authnet.class.php');
      $payment_type = $evtcl->payment_type;
      $cc_number = $evtcl->cc;
      $cvv = trim($evtcl->cvv);
      $expire_year = $evtcl->expire_year;
      $expire_month = $evtcl->expire_month;
      $expiration = $evtcl->expire_month.$evtcl->expire_year;
      if ($evtcl->tot_amt > 0) {
        $total = $evtcl->tot_amt; 
      } else {
        $total = $_SESSION['do_invoice']->amt_due;
      }       
      $idinvoice = $_SESSION['do_invoice']->idinvoice;
      $idcontact = $_SESSION['do_invoice']->idcontact;
      $goto = $evtcl->goto;
      $do_contact = new Contact();
      $arr_user_info = $do_contact->getContactInfo_For_Invoice($idcontact);
      $auth_login = $_SESSION['do_invoice']->authnet_login;
      $inv_info_arr = array();
      $inv_info_arr['description'] = $_SESSION['do_invoice']->description;
      $inv_info_arr['inv_num'] = $_SESSION['do_invoice']->num;
      $auth_merchant_id = $_SESSION['do_invoice']->authnet_merchant_id;
      /* @param true = test mode
      @param false = non test mode i.e live  
      */
      $payment = new Authnet(false, $arr_user_info,$auth_login,$auth_merchant_id,$inv_info_arr);
      $cc_msg = $payment->validateCreditCard($cc_number, $payment_type, $cvv, $expire_year, $expire_month);
      if($cc_msg == ""){
        $invoice = uniqid('ofuz_', true);
        $payment->transaction($cc_number, $expiration, $total, $cvv, $invoice);
        $payment->process();
        if ($payment -> isApproved()){
            $reason = $payment -> getResponseText();
            // Display a printable receipt
            $_SESSION['in_page_message'] = _("This transaction has been approved. Thank you for your payment");
            $transactionID = $payment->getTransactionID();
            $do_pay_log = new PaymentLog();
            $do_pay_log->addPaymentLog($transactionID,"AuthNet",$_SESSION['do_invoice']->idinvoice,$total);
                                                $idpayment_log = $do_pay_log->getPrimaryKeyValue();
                                                $do_payment_inv = new PaymentInvoice();
                                                $do_payment_inv->addPaymentInvoice($idpayment_log,$_SESSION['do_invoice']->idinvoice,$total);
            $this->updatePayment($total);
            $this->sendPaymentApprovedEmail($total,"Authorized.net",$transactionID);// Sending to customer
            $this->sendPaymentApprovedEmail($total,"Authorized.net",$transactionID,true); // Sending to user
            /*
              Lets check if the invoice has an call back URL and process that
            */
            $do_inv_callback = new InvoiceCallback();
            $do_User = $_SESSION['do_invoice']->getParentUser();
            $do_inv_callback->processCallBack($this->idinvoice,$this->num,$total,$do_User->iduser,"ok","AuthNet",$transactionID);
            
            /*
            Check if there is a next URL if so then set goto variable to that URL. Userful if we want the user to go to someother page
            Than in the goto page. This is triggered only when full payment is done.
            */
            $next_url = $do_inv_callback->isNextUrl($this->idinvoice);
            //if($next_url !== false){
            //     $due_amt = $this->getDueAmount($this->idinvoice);
            //     if($due_amt == '0.00'){
              $goto = $next_url; 
            //     }
            //}
            $_SESSION['autologin_paid'] = True;
            // Add the CC info in the RecurrentInvoiceCC
            if($evtcl->is_rec !=0 && $evtcl->is_cc == 0 ){
              $RecurrentInvoiceCC = new RecurrentInvoiceCC();
              $RecurrentInvoiceCC->add_cc_info($cc_number,$expire_year,$expire_month,$evtcl->payment_type,$evtcl->is_rec);
            }
        }else if ($payment -> isDeclined()){
            $reason = $payment -> getResponseText();
            $do_inv_callback = new InvoiceCallback();
            $do_inv_callback->processCallBack($this->idinvoice,$this->num,$total,$_SESSION['do_User']->iduser,"fail","AuthNet","",$reason);
            $goto = $evtcl->error_page;
            // As for another form of payment
            $_SESSION['in_page_message'] = _("The transaction has been declined.'{$reason}'");
        } else {
            $reason = $payment -> getResponseText();
            //$reason .= $payment->getResponseCode();
            $do_inv_callback = new InvoiceCallback();
            $do_inv_callback->processCallBack($this->idinvoice,$this->num,$total,$_SESSION['do_User']->iduser,"fail","AuthNet","",$reason);
            $goto = $evtcl->error_page;				
            // Ask the merchant to call us
            $_SESSION['in_page_message'] = _("The transaction failed.'{$reason}'");
        }
      }else{
        $_SESSION['in_page_message'] = $cc_msg;
      }
    }
      $evtcl->setDisplayNext(new Display($goto));

 }




  /**
      * Event Method for Stripe Payment Process
      * test with credit card 4242424242424242
      * @param object $evtcl 
     */
    function eventProcessStripePayment(Eventcontroler $evtcl){
      $flag = true;

      if($evtcl->tot_amt > $_SESSION['do_invoice']->amt_due) {
        $_SESSION['in_page_message'] = _("The Total Amount entered is greater than the invoice amount. Please re-enter.");
        $flag = false;
      }

      if($flag) {      
        include_once('class/Stripe.class.php');
        include_once("stripe-lib/Stripe.php");
        $token = $evtcl->stripeToken;
        $name = $evtcl->name;
        $email = $evtcl->email;
        $description = $name;
        $srtipecustomer_id = $evtcl->stripecustomer_id;
      
        if ($evtcl->tot_amt > 0) {
          $total = $evtcl->tot_amt; 
        } else {
          $total = $_SESSION['do_invoice']->amt_due;
        }       
      
        //Amount need to conver to cents 
        $total = $total*100;
      
        $idinvoice = $_SESSION['do_invoice']->idinvoice;
        $idcontact = $_SESSION['do_invoice']->idcontact;
        $goto = $evtcl->goto;
        $error_page = $evtcl->error_page;
        $updateStripecustomer = $evtcl->updateStripecustomer;
        $do_contact = new Contact();
        $arr_user_info = $do_contact->getContactInfo_For_Invoice($idcontact);
        
        $inv_info_arr = array();
        $inv_info_arr['description'] = $_SESSION['do_invoice']->description;
        $inv_info_arr['inv_num'] = $_SESSION['do_invoice']->num;
        $stripe_api_key = $evtcl->stripe_api_key;
      
      
        $payment = new StripeGateWay(false, $stripe_api_key);
      
        if(empty($srtipecustomer_id)){
          $result = $payment->CreateCustomer($token,$name,$total,$email,$description);
        }else{
          if($updateStripecustomer === 'Yes'){
            $result = $payment->UpdateExistingCustomer($srtipecustomer_id,$token,$name,$total,$email="",$description=""); 
          }else{
            $result = $payment->ChargeExsistingCustomer($srtipecustomer_id,$total);
          }
        }

        if($result['success'] == '1'){          
          //set the amout back to $ value 
          $total = $total/100;          
          
          //echo $result['customer_id'];die();
				
          //Add the customer id in to stripe details class
          if(isset($result['customer_id'])){
            $this->saveStripeCustomerId($_SESSION['do_invoice']->iduser,$_SESSION['do_invoice']->idcontact,$result['customer_id']);
          }
				
          // Display a printable receipt
          $_SESSION['in_page_message'] = _("This transaction has been approved. Thank you for your payment");
          
          $do_pay_log = new PaymentLog();
          $do_pay_log->addPaymentLog($result['response']['id'],"Stripe",$_SESSION['do_invoice']->idinvoice,$total);
                                              $idpayment_log = $do_pay_log->getPrimaryKeyValue();
                                              $do_payment_inv = new PaymentInvoice();
                                              $do_payment_inv->addPaymentInvoice($idpayment_log,$_SESSION['do_invoice']->idinvoice,$total);
          $this->updatePayment($total);
          //$this->sendPaymentApprovedEmail($total,"Stripe.com",$transactionID);// Sending to customer
          //$this->sendPaymentApprovedEmail($total,"Stripe.com",$transactionID,true); // Sending to user

          if(isset($_SESSION["upgrade"])){      
               $do_user = new User();               
               $date = date('Y-m-d');
               $do_user->query("update user set plan='paid', regdate = '{$date}' where iduser=".$_SESSION['do_User']->iduser);                              
                
                $do_recurrentInvoice = new RecurrentInvoice();
                $do_recurrentInvoice->addRecurrentInvoice($_SESSION['do_invoice']->idinvoice,'1','Month',date("Y-m-d"),$_SESSION['do_User']->iduser);

                $do_ccdetails = new CcDetails();
                $do_ccdetails->iduser = $_SESSION['do_User']->iduser;
                $do_ccdetails->token = $result['customer_id'];
                $do_ccdetails->type = 'Stripe';
                $do_ccdetails->add();

                $goto = 'index.php';  
                unset($_SESSION['upgrade']);
          }else{     
            /*
              Lets check if the invoice has an call back URL and process that
            */
            $do_inv_callback = new InvoiceCallback();
            $do_User = $_SESSION['do_invoice']->getParentUser();
            $do_inv_callback->processCallBack($this->idinvoice,$this->num,$total,$do_User->iduser,"ok","Stripe",$transactionID);
            
            /*
            Check if there is a next URL if so then set goto variable to that URL. Userful if we want the user to go to someother page
            Than in the goto page. This is triggered only when full payment is done.
            */
            $next_url = $do_inv_callback->isNextUrl($this->idinvoice);
            
          }
            //$goto = $next_url;             
            $_SESSION['autologin_paid'] = True;
            // Add the CC info in the RecurrentInvoiceCC
            if($evtcl->is_rec !=0 && $evtcl->is_cc == 0 ){
              $RecurrentInvoiceCC = new RecurrentInvoiceCC();
              $RecurrentInvoiceCC->add_cc_info($cc_number,$expire_year,$expire_month,$evtcl->payment_type,$evtcl->is_rec);
          }        
        }else{
          $rr = json_decode($result,true);//echo'<pre>';print_r($rr);echo'</pre>';die();
          $r = $rr['error']['message']; 
          $error_code = $rr['error']['code'];

          if(($error_code == 'invalid_expiry_month') || ($error_code == 'invalid_expiry_year') || ($error_code == 'expired_card') || ($error_code == 'missing')){
            $goto = $error_page;
            $_SESSION['updatecustomer'] = 'Yes';		
          }

          $_SESSION['in_page_message'] = $r;
        }
      }
      
      $disp_next = new Display($goto);

      if(isset($_SESSION['upgrade'])){
          $msg = "Thank You . Your payment has been apporved and now you are paid user.";
          $disp_next->addParam("message", $msg);
      }
       
      $evtcl->setDisplayNext($disp_next);
  }



	/**
	 *  Function to add the stripe customer id in to the stripe details table
	 * @param iduser
	 * @param idcontact
	 * @param stripe created customer id
	*/
	
	function saveStripeCustomerId($iduser,$idcontact,$customer_id){
		
		$q = new sqlQuery($this->getDbCon()); 
        $q->query("Insert into stripe_details (iduser,idcontact,stripe_token,createdate) values('$iduser','$idcontact','$customer_id',now())");
		
	}
		
	
	/**
	 * Function to check and get the stripe customer id if present in stripe details table
	 * @param iduser
	 * @param idcontact
	 */
	 function getStripeCustomerId($iduser,$idcontact){
		 //echo "SELECT stripe_token FROM stripe_details WHERE idcontact ='$idcontact' and iduser = '$iduser'";die();
		$q = new sqlQuery($this->getDbCon()); 
        $q->query("SELECT stripe_token FROM stripe_details WHERE idcontact ='$idcontact' and iduser = '$iduser'");
        $q->fetch();
        $stripe_token = $q->getData("stripe_token");
        
        return $stripe_token;
	 }
	 
    /**
      * Send Payment confirmation email to the customer
      * @param float $amt
      * @param string $pay_type
      * @param string $ref
      * @param boolean $to_user
    */
    function sendPaymentApprovedEmail($amt,$pay_type,$ref,$to_user = false){
	
        $idinvoice = $this->idinvoice;
        $idcontact = $this->idcontact;
	
		$do_contact_cust = new Contact();
		$do_contact_cust->getId($_SESSION['do_invoice']->idcontact);
		$customer_name = $do_contact_cust->firstname.' '.$do_contact_cust->lastname;
			
        $do_contact = new Contact();
        $do_contact->getId($idcontact);
        $contact_name = $do_contact->firstname.' '.$do_contact->lastname;
        if($do_contact->email_address != '') {
            $contact_email = $do_contact->email_address;
        } else { 
            $ContactEmail = $do_contact->getChildContactEmail();
            $contact_email = $ContactEmail->getDefaultEmail();
            //if($ContactEmail->getNumRows()) {
            //      $contact_email = $ContactEmail->email_address;
            //}
        }
        $do_user_data = new User();
        $do_user_data->getId($_SESSION['do_invoice']->iduser);
        if($contact_email){
            $total = $amt;
            $total = $this->viewAmount($total);
            if($to_user === false ){ // Setting email template for the contact 
                $email_template = new EmailTemplate("ofuz_inv_payment_confirmation");  
                $email_template->setSenderName($do_user_data->firstname.' '.$do_user_data->lastname);
                $email_template->setSenderEmail($do_user_data->email);
            }else{ // Setting email template for the user 
                $email_template = new EmailTemplate("ofuz_inv_payment_confirmation_adm");   
                $email_template->setSenderName($contact_name);
                $email_template->setSenderEmail($contact_email);
            }
            //$email_template->setSubject($do_user_data->company." Payment Confirmation for Invoice: ".$this->num);
            
            $signature = $do_user_data->company.'<br />'.$do_user_data->firstname.' '.$do_user_data->lastname;
            $email_data = Array('name' => $customer_name,
                                        'company'=>$do_user_data->company,
                                        'description'=>$_SESSION['do_invoice']->desc,
                                        'signature'=>$signature,
                                        'amount'=>$total,
                                         'num'=>$_SESSION['do_invoice']->num,
                                         'refnum'=>$ref,
                                         'paytype'=>$pay_type, 
                                          'username'=>$do_user_data->firstname,
                                          'invoice_num' => $_SESSION['do_invoice']->num
                                        );
    //echo"<pre>";print_r($email_data);echo"</pre>";die();
            $emailer = new Radria_Emailer();
            $emailer->setEmailTemplate($email_template);
            $emailer->mergeArray($email_data);
            if($to_user === false ){ // Sending to Contact 
                    $emailer->addTo($contact_email);
            }else{ // Sending to User 
                    $emailer->addTo($do_user_data->email);
            }
            $emailer->send(); 
            //$_SESSION['in_page_message'] = _("Invoice is sent to ".$contact_email);
         }else { 
            //$_SESSION['in_page_message'] = _("Unable to send invocie as no email id found");
         } 
    }

    /**
      * Event Method sending Invoice Approve/Reject Email
      * @param object $evtcl
    */
    function eventSendInvoiceApproveReject(Eventcontroler $evtcl){
          $this->sendInvoiceApproveReject($evtcl->iduser,$evtcl->idinvoice,$evtcl->status);
    }

    /**
      * Method to send Send Invoice Apporove/Reject Email to the Invoice Owner
      * @param integer $iduser
      * @param integer $idinvoice
      * @param string $status
      * @param string $rejected_reason

    */
    function sendInvoiceApproveReject($iduser,$idinvoice,$status,$rejected_reason=""){
        $do_inv = new Invoice();
        $do_inv_user = new User();
        $do_contact = new Contact();

        $do_inv->getId($idinvoice);       
        $do_inv_user->getId($iduser);
        $do_contact->getId($do_inv->idcontact);

        $total_due = $do_inv->amt_due;
        $inv_num = $do_inv->num;
        $inv_desc = $do_inv->description;
        $inv_address = nl2br($this->Only1brFirstLineBlack($do_inv->invoice_address));

        $contact_name = $do_contact->firstname.' '.$do_contact->lastname;
        if($do_contact->email_address != '') {
            $contact_email = $do_contact->email_address;
        } else { 
            $ContactEmail = $do_contact->getChildContactEmail();
            if($ContactEmail->getNumRows()) {
                  $contact_email = $ContactEmail->email_address;
            }
        }
        if($contact_email == ''){$contact_email = 'info@sqlfusion.com' ;}
        
        if($status == "Accept"){
            $email_template = new EmailTemplate("ofuz_inv_accept_confirmation");
        }
        $do_user_rel = new UserRelations();
        $invoice_url = $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_user_rel->encrypt($idinvoice).'/'.$do_user_rel->encrypt($do_inv->idcontact);
        //$email_template->setSubject(_("Invoice #").$inv_num.' '.$inv_desc." "._("has been accepted by")." ".$contact_name);
        $email_template->setSenderName($contact_name);
        $email_template->setSenderEmail($contact_email);
        $signature = $inv_address;
        $email_data = Array('name' => $contact_name,
                                    'contact'=>$contact_name,
                                    'description'=>$inv_desc,
                                    'signature'=>$signature,
                                    'amount'=>$total_due,
                                    'num'=>$inv_num,
                                    'invoice_url'=>$invoice_url,
                                    'address'=>$inv_address
                                    );

        $emailer = new Radria_Emailer();
        $emailer->setEmailTemplate($email_template);
        $emailer->mergeArray($email_data);
        $emailer->addTo($do_inv_user->email);
        $emailer->send(); 
        
    }

    // DOMPDF does not work with complex HTML :(
    function eventGeneratePDFInvoice(EventControler $evtcl){ 
      include_once("dompdf/dompdf_config.inc.php");
      $html = file_get_contents("invoice_print.php");
      $pdffilename = "Invoice_".$this->num.".pdf";
      $dompdf = new DOMPDF();
      $dompdf->load_html($html);
      $dompdf->render();
      $dompdf->stream($pdffilename);
    }

    /**
      * Generate the Month Dropdown for the Invoice filter
      * @return $html -- STRING
    */
    function getMonthDropDownFilter(){
        //if($this->filter_month == ''){ $this->filter_month = date("m"); }
        $html = '<select name ="filter_for_mon" id = "filter_for_mon" class="" onChange=\'$("#setFilterInvMonth").submit();\' style="align:center;"">';
        $html .='<option value = "" '.$this->getMonthFilter().'>'._('All').'</option>';
        $html .='<option value = "01" '.$this->getMonthFilter("01").'>'._('January').'</option>';
        $html .='<option value = "02" '.$this->getMonthFilter("02").'>'._('February').'</option>';
        $html .='<option value = "03" '.$this->getMonthFilter("03").'>'._('March').'</option>';
        $html .='<option value = "04" '.$this->getMonthFilter("04").'>'._('April').'</option>';
        $html .='<option value = "05" '.$this->getMonthFilter("05").'>'._('May').'</option>';
        $html .='<option value = "06" '.$this->getMonthFilter("06").'>'._('June').'</option>';
        $html .='<option value = "07" '.$this->getMonthFilter("07").'>'._('July').'</option>';
        $html .='<option value = "08" '.$this->getMonthFilter("08").'>'._('August').'</option>';
        $html .='<option value = "09" '.$this->getMonthFilter("09").'>'._('September').'</option>';
        $html .='<option value = "10" '.$this->getMonthFilter("10").'>'._('October').'</option>';
        $html .='<option value = "11" '.$this->getMonthFilter("11").'>'._('November').'</option>';
        $html .='<option value = "12" '.$this->getMonthFilter("12").'>'._('December').'</option>';
        $html .= '</select>';
        return $html;
    }

    /**
      * Generate Year Drop Down Filter
      * @return $html -- STRING 
    */
    function getYearDropDownFilter(){ 
        if($this->filter_year == ''){ $this->filter_year = date("Y"); }
        $html = '<select name ="filter_for_year" id = "filter_for_year" class="" onChange=\'$("#setFilterInvMonth").submit();\' style="align:center;"">';
        $html .='<option value = "'.date("Y").'" '.$this->getYearFilter(date("Y")).'>'.date("Y").'</option>';
        $html .='<option value = "'.date("Y",strtotime("-1 year",strtotime(date("Y")))).'" '.$this->getYearFilter(date("Y",strtotime("-1 year",strtotime(date("Y"))))).'>'.date("Y",strtotime("-1 year",strtotime(date("Y")))).'</option>';
        $html .='<option value = "'.date("Y",strtotime("-2 year",strtotime(date("Y")))).'" '.$this->getYearFilter(date("Y",strtotime("-2 year",strtotime(date("Y"))))).'>'.date("Y",strtotime("-2 year",strtotime(date("Y")))).'</option>';
        //$html .='<option value = "'.date("Y",strtotime("-3 year",strtotime(date("Y")))).'" '.$this->getYearFilter(date("Y",strtotime("-3 year",strtotime(date("Y"))))).'>'.date("Y",strtotime("-3 year",strtotime(date("Y")))).'</option>';
        //$html .='<option value = "'.date("Y",strtotime("-4 year",strtotime(date("Y")))).'" '.$this->getYearFilter(date("Y",strtotime("-4 year",strtotime(date("Y"))))).'>'.date("Y",strtotime("-4 year",strtotime(date("Y")))).'</option>';
        //$html .='<option value = "'.date("Y",strtotime("-5 year",strtotime(date("Y")))).'" '.$this->getYearFilter(date("Y",strtotime("-5 year",strtotime(date("Y"))))).'>'.date("Y",strtotime("-5 year",strtotime(date("Y")))).'</option>';
        $html .= '</select>';
        return $html;
    }
    
    /**
      * Set the selected month
      * @param string $selected
    */
    function getMonthFilter($selected="") {
        if ($selected == $this->filter_month) {
            return " selected";
        } else {
            return $this->filter_month;
        }
    }

    /**
      * Method to get year filter
      * @param string $selected
    */
    function getYearFilter($selected=""){
         if ($selected == $this->filter_year) {
            return " selected";
        } else {
            return $this->filter_year;
        }
    }
    /**
      * Format the filtered month for the query
      * @return "formated month" -- STRING
    */
    function formatSearchMonth($month){
        if($this->filter_year != '')
          return $this->filter_year.'-'.$month; 
        else
          return date("Y").'-'.$month; 
    }


    /**
      * Get the posion of the currency right for euro left others
      * It will set the member variable currency_position with the value
    */
    function getCurrencyPostion(){
        if($this->currency_iso_code == 'Euro'){
              $this->currency_position = 'r';
        }else{
             $this->currency_position = 'l';
        }
    }

    /**
      * Set the currency signto the public var currency
      * This version has just the currecny symbol not the ISO code
    */
    function setCurrencyDisplay(){
        $this->currency = $this->currency_sign;// For now only symbol no ISO code
    }


    /**
      * Method to view an amount after determining the posion and then 
      * setting the number format
      * @param float $amt
    */
    function viewAmount($amt){
        if($this->currency_position == 'r'){
              return $this->setNumberFormat($amt).' '.$this->currency;
        }else{
              return $this->currency.' '.$this->setNumberFormat($amt);
        }
    }

    /**
      * Automatic conversion of the numbers depending on the currency selected
      * @param float $amt
      * @return $amt with formated 
    */
    function setNumberFormat($amt){ 
        switch($this->currency_iso_code){
              case "Euro" : 
                          return number_format($amt,2, ', ', ' ' );
                          break;
              case "BRL" :
                         return number_format($amt,2, ', ', '. ' );
                          break;
              case "ZAR" :
                         return number_format($amt,2, '. ', ' ' );
                          break;
              default : 
                          return number_format($amt,2, '. ', ',' );
                          break;
        }
    }

    /**
      * Formating 0's
    */
    function setNumberFormatZero(){ 
        switch($this->currency_iso_code){
              case "Euro" : 
                          return '0,00 '.$this->currency;
                          break;
              case "BRL" :
                          return $this->currency.' 0,00';
                          break;
              default : 
                          return $this->currency.' 0.00';
                          break;
        }
    }

    /**
      * Function to get the Invoice sender address.
      * First look at the User object if an idcontact is available
      * If so try to get the address info from that idcontact using Contact Object.
      * If no idcontact then get the address info from the user object.
      * @param object $do_user
    */

    function getInvoiceSenderAddr(User $do_user = null){
        $html = '';
        if (is_null($do_user)) { $do_user == $_SESSION['do_User']; }

        if($do_user->idcontact != 0 && !empty($do_user->idcontact)){
            $q = new sqlQuery($this->getDbCon()); 
            $q->query("select address from contact_address where idcontact = ".$do_user->idcontact);
            if($q->getNumRows()){
                $q->fetch();
                if($do_user->company !=''){$html .= $do_user->company.'<br />';}
                $html .= $do_user->firstname.' '.$do_user->lastname.'<br />';
                $html .= $q->getData("address");
                $contact = $do_user->getParentContact();
                //$contact->getId($idcontact);
                $emails = $contact->getChildContactEmail();
                $default_email = $emails->getDefaultEmail();
                if (strlen($default_email) > 0) {
                  $html .= '<br /><a href="mailto:'.$default_email.'">'.$default_email.'</a><br />';
                }
                $phones = $contact->getChildContactPhone();
                if (strlen($phones->phone_number) > 0) {
                    $html .= '<a href="tel:'.$phones->phone_number.'">'.$phones->phone_number.'</a><br/>';
                }
            }
        }
        if($html == ''){
              if($do_user->company !=''){$html .= $do_user->company;}
              $html .= '<br />'.$do_user->firstname.' '.$do_user->lastname;
              if($do_user->address1 !=''){$html .= '<br />'.$do_user->address1;}
              if($do_user->address2 !=''){$html .= '<br />'.$do_user->address2;}
              if($do_user->city !=''){$html .= '<br />'.$do_user->city;}
              if($do_user->state !=''){$html .= ', '.$do_user->state;}
              if($do_user->zip !=''){$html .= ' '.$do_user->zip;}
              if($do_user->country !=''){$html .= '<br />'.$do_user->country;}			
              if($do_user->email !=''){$html .= '<br /><a href="mailto:'.$do_user->email.'">'.$do_user->email.'</a>';}
              if($do_user->phone !=''){$html .= '<br /><a href="tel:'.$do_user->phone.'">'.$do_user->phone.'</a>';}
        }
        return $html;
    }

    /**
      * Get Total num of invoices for a user
      * @param integer $iduser
      * @return total num of invoices
    */
    function getTotalNumInvoicesForUser($iduser) {
	    $q = new sqlQuery($this->getDbCon());
	    $sql = "SELECT COUNT(idinvoice) AS total_invoices
			    FROM `{$this->table}` 
			    WHERE `iduser` = {$iduser}
			    AND status <> 'Cancel'
			";
	    $q->query($sql);
	    if($q->getNumRows()) {
		    $q->fetch();
		    return $q->getData("total_invoices");
	    } else {
		    return "0";
	    }
    }

    /**
      * Utility method to strip to only one BR
      * @param $string -- STRING
    */
    function Only1br($string) {
	    return preg_replace('/(\r\n)+|(\n|\r)+/', '<br />', $string);
    }

    
    function Only1brFirstLineBlack($string) {
	    return preg_replace('/^(.*?)<br \/>(.*)$/', '<span class="text_black"><b>$1</b></span><br />$2', preg_replace('/(\r\n)+|(\n|\r)+/', '<br />', trim($string)));
    }


                
    /**
      * get the total Quotes for the month
      * @return total amt of quotes if found else boolean
    */

    function getTotalQuotesForTheMonth() {
	    //Quote,New,Sent, Partial and Paid
	    $q = new sqlQuery($this->getDbCon());
	    $year_cond = '';
	    if($this->filter_year !=''){$year_cond = " AND YEAR(datecreated) ='{$this->filter_year}'";}
	    $sql = "SELECT SUM( net_total ) as quote_total
			    FROM `invoice`
			    WHERE iduser = {$_SESSION['do_User']->iduser}
			    AND status = 'Quote'
			    AND MONTH(datecreated) = '{$this->filter_month}'
			    AND status <> 'Cancel'
			".$year_cond;

	    $q->query($sql);

	    if($q->getNumRows()) {
		    $q->fetch();
		    return $q->getData('quote_total');
	    } else {
		    return false;
	    }

    }

    /**
      * get the total Invoices for the month
      * @return total amt of Invoices if found else boolean
    */


    function getTotalInvoiceForTheMonth() {
	    //Quote,Invoice,Sent, Partial and Paid
//'Invoice' Partial and Paid Sent
	    $year_cond = '';
	    if($this->filter_year !=''){$year_cond = " AND YEAR(datecreated) ='{$this->filter_year}'";}
	    $q = new sqlQuery($this->getDbCon());
	    $sql = "SELECT SUM( net_total ) as invoice_total
			    FROM `invoice`
			    WHERE iduser = {$_SESSION['do_User']->iduser}
			    AND (status = 'New' OR status = 'Paid' OR status = 'Partial' OR status = 'Sent')
			    AND MONTH(datecreated) = '{$this->filter_month}'
			    AND status <> 'Cancel'
			".$year_cond;

	    $q->query($sql);

	    if($q->getNumRows()) {
		    $q->fetch();
		    return $q->getData('invoice_total');
	    } else {
		    return false;
	    }

	}

	/**
	  * get the total Invoices Sent for the month
	  * @return total amt of Invoices Sent if found else boolean
	*/

	function getTotalSentForTheMonth() {
		//Quote,Invoice,Sent, Partial and Paid
//'Invoice' Partial and Paid Sent
    $year_cond = '';
    if($this->filter_year !=''){$year_cond = " AND YEAR(datecreated) ='{$this->filter_year}'";}
    $q = new sqlQuery($this->getDbCon());
    $sql = "SELECT SUM( net_total ) as invoice_total
      FROM `invoice`
      WHERE iduser = {$_SESSION['do_User']->iduser}
      AND status = 'Sent'
      AND MONTH(datecreated) = '{$this->filter_month}'
                                  AND status <> 'Cancel'
        ".$year_cond;

    $q->query($sql);

    if($q->getNumRows()) {
      $q->fetch();
      return $q->getData('invoice_total');
    } else {
      return false;
    }

	}

	/**
	  * get the total Invoices Paid for the month
	  * @return total amt of Invoices Paid if found else boolean
	*/
	
	function getTotalPaidForTheMonth() {
		//Quote,Invoice,Sent, Partial and Paid
    $year_cond = '';
    if($this->filter_year !=''){$year_cond = " AND YEAR(datecreated) ='{$this->filter_year}'";}
    $q = new sqlQuery($this->getDbCon());
    $sql = "SELECT SUM( net_total ) as paid_total
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND (status = 'Paid' OR status = 'Partial')
				AND MONTH(datecreated) = '{$this->filter_month}'
    AND status <> 'Cancel'  ".$year_cond;

		$q->query($sql);

		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData('paid_total');
		} else {
			return false;
		}

	}

	/**
	  * get the total Invoices Past due for the month
	  * @return total amt of Invoices Past due if found else boolean
	*/

	function getTotalPastDueForTheMonth() {
		//Quote,Invoice,Sent, Partial and Paid
      $year_cond = '';
      if($this->filter_year !=''){$year_cond = " AND YEAR(datecreated) ='{$this->filter_year}'";}
      $today = date("Y-m-d");
      $q = new sqlQuery($this->getDbCon());
      $sql = "SELECT SUM( amt_due ) as past_due_total
        FROM `invoice`
        WHERE iduser = {$_SESSION['do_User']->iduser}
        AND status <> 'Quote'
        AND due_date < '{$today}'
        AND MONTH(datecreated) = '{$this->filter_month}'
                                    AND status <> 'Cancel'
          ".$year_cond;

      $q->query($sql);

      if($q->getNumRows()) {
        $q->fetch();
        return $q->getData('past_due_total');
      } else {
        return false;
      }

	}

	/**
	  * get the total Quotes Year To Date
	  * @return total amt of Quotes if found else boolean
	*/

	function getTotalQuotesYTD() {
		//$ytd_start = date("Y")."-01-01";
        $ytd_start = $this->formatSearchMonth("01-01");
        $ytd_end = $this->formatSearchMonth("12-31");
		//$ytd_end = date("Y-m-d");
        $q = new sqlQuery($this->getDbCon());
        $sql = "SELECT SUM( net_total ) as quote_total
          FROM `invoice`
          WHERE iduser = {$_SESSION['do_User']->iduser}
          AND status = 'Quote'
          AND datecreated BETWEEN '{$ytd_start}' AND '{$ytd_end}'
                                      AND status <> 'Cancel'
            ";

        $q->query($sql);

        if($q->getNumRows()) {
            $q->fetch();
            return $q->getData('quote_total');
        } else {
            return false;
        }

	}

	/**
	  * get the total Quotes 
	  * @return total amt of Quotes if found else boolean
	*/

  function getTotalQuotes() {

      $today = date("Y-m-d");
      $q = new sqlQuery($this->getDbCon());
      $sql = "SELECT SUM( net_total ) as quote_total
        FROM `invoice`
        WHERE iduser = {$_SESSION['do_User']->iduser}
        AND status = 'Quote'
                                    AND status <> 'Cancel'
          ";

      $q->query($sql);

      if($q->getNumRows()) {
          $q->fetch();
          return $q->getData('quote_total');
      } else {
          return false;
      }

	}


	/**
	  * get the total Invoices Year To Date
	  * @return total amt of Invoices if found else boolean
	*/
	function getTotalInvoiceYTD() {
		//$ytd_start = date("Y")."-01-01";
        $ytd_start = $this->formatSearchMonth("01-01");
		//$today = date("Y-m-d");
        $ytd_end = $this->formatSearchMonth("12-31");
        $q = new sqlQuery($this->getDbCon());
        $sql = "SELECT SUM( net_total ) as invoice_total
          FROM `invoice`
          WHERE iduser = {$_SESSION['do_User']->iduser}
          AND (status = 'New' OR status = 'Paid' OR status = 'Partial' OR status = 'Sent')
          AND datecreated BETWEEN '{$ytd_start}' AND '{$ytd_end}'
                                      AND status <> 'Cancel'
            ";

        $q->query($sql);

        if($q->getNumRows()) {
          $q->fetch();
          return $q->getData('invoice_total');
        } else {
          return false;
        }

	}

	/**
	  * get the total Invoices
	  * @return total amt of Invoices if found else boolean
	*/

  function getTotalInvoice() {
		
      $today = date("Y-m-d");
      $q = new sqlQuery($this->getDbCon());
      $sql = "SELECT SUM( net_total ) as invoice_total
              FROM `invoice`
              WHERE iduser = {$_SESSION['do_User']->iduser}
              AND (status = 'New' OR status = 'Paid' OR status = 'Partial' OR status = 'Sent')
                                          AND status <> 'Cancel'
                ";

      $q->query($sql);

      if($q->getNumRows()) {
          $q->fetch();
          return $q->getData('invoice_total');
      } else {
          return false;
      }

	}


	/**
	  * get the total Invoices Sent Year To Date
	  * @return total amt of Invoices Sent if found else boolean
	*/

	function getTotalSentYTD() {
		//$ytd_start = date("Y")."-01-01";
        $ytd_start = $this->formatSearchMonth("01-01");
        $ytd_end = $this->formatSearchMonth("12-31");
        //$today = date("Y-m-d");
        $q = new sqlQuery($this->getDbCon());
        $sql = "SELECT SUM( net_total ) as invoice_total
        FROM `invoice`
        WHERE iduser = {$_SESSION['do_User']->iduser}
        AND (status = 'Sent')
        AND datecreated BETWEEN '{$ytd_start}' AND '{$ytd_end}'
                                    AND status <> 'Cancel'
          ";

        $q->query($sql);

        if($q->getNumRows()) {
          $q->fetch();
          return $q->getData('invoice_total');
        } else {
          return false;
        }

	}


	/**
	  * get the total Invoices Sent
	  * @return total amt of Invoices Sent if found else boolean
	*/

   function getTotalSent() {
      $q = new sqlQuery($this->getDbCon());
      $sql = "SELECT SUM( net_total ) as invoice_total
        FROM `invoice`
        WHERE iduser = {$_SESSION['do_User']->iduser}
        AND (status = 'Sent')
        AND status <> 'Cancel'
          ";

      $q->query($sql);

      if($q->getNumRows()) {
        $q->fetch();
        return $q->getData('invoice_total');
      } else {
        return false;
      }

	}
	
	/**
	  * get the total Invoices Paid Year To Date
	  * @return total amt of Invoices Paid if found else boolean
	*/


	function getTotalPaidYTD() {
		$ytd_start = $this->formatSearchMonth("01-01");
		$ytd_end = $this->formatSearchMonth("12-31");
		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT SUM( net_total ) as paid_total
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND (status = 'Paid' OR status = 'Partial')
				AND datecreated BETWEEN '{$ytd_start}' AND '{$ytd_end}'
                                AND status <> 'Cancel'
			   ";

		$q->query($sql);

		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData('paid_total');
		} else {
			return false;
		}

	}

	/**
	  * get the total Invoices Paid
	  * @return total amt of Invoices Paid if found else boolean
	*/

        function getTotalPaid() {
		
		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT SUM( net_total ) as paid_total
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND (status = 'Paid' OR status = 'Partial')
				AND status <> 'Cancel'
			   ";

		$q->query($sql);

		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData('paid_total');
		} else {
			return false;
		}
	}

	/**
	  * get the total Invoices Past Due Year To Date
	  * @return total amt of Invoices Past Due if found else boolean
	*/

	function getTotalPastDueYTD() {		
        $ytd_start = $this->formatSearchMonth("01-01");
		$ytd_end = $this->formatSearchMonth("12-31");
		$today = date("Y-m-d");
		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT SUM( amt_due ) as past_due_total
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND status <> 'Quote'
				AND due_date < '{$today}'
				AND datecreated BETWEEN '{$ytd_start}' AND '{$ytd_end}'
                                AND status <> 'Cancel'
			   ";

		$q->query($sql);

		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData('past_due_total');
		} else {
			return false;
		}

	}

	/**
	  * get the total Invoices Past Due 
	  * @return total amt of Invoices Past Due if found else boolean
	*/

        function getTotalPastDue() {
		
		$today = date("Y-m-d");
		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT SUM( amt_due ) as past_due_total
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND status <> 'Quote'
				AND due_date < '{$today}'
                                AND status <> 'Cancel'
				
			   ";

		$q->query($sql);

		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData('past_due_total');
		} else {
			return false;
		}

	}


	/**
	  * get the total Contact Quotes 
	  * @param $idcontact -- INT
	  * @param $year -- STRING
	  * @return $sql
	*/
	function getTotalContactQuotesYTD($idcontact,$year) {
                		
		//$q = new sqlQuery($this->getDbCon());

                /*$sql = "SELECT SUM( net_total ) as quote_total
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcontact = {$idcontact}
				AND status = 'Quote'
				AND datecreated BETWEEN '{$ytd_start}' AND '{$ytd_end}'
			   ";*/

                $sql = "SELECT SUM( net_total ) as invoice_total,DATE_FORMAT( datecreated,'%Y') as year_created
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcontact = {$idcontact}
				AND status = 'Quote'
                                AND status <> 'Cancel'
                                AND datecreated like '%".$year."%'
                                Group By year_created
                                Order By year_created
                        ";
                return $sql;

                //echo $sql;
		/*$q->query($sql);

		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData('quote_total');
		} else {
			return false;
		}*/

	}

        
	/**
	  * get the total Contact Invoices 
	  * @param integer $idcontact
	  * @param string $year
	  * @return $sql
	*/
	function getTotalContactInvoiceYTD($idcontact,$year) {
		
                $sql = "SELECT SUM( net_total ) as invoice_total,DATE_FORMAT( datecreated,'%Y') as year_created
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcontact = {$idcontact}
				AND (status = 'New' OR status = 'Sent') 
                                AND status <> 'Cancel'
                                AND datecreated like '%".$year."%'
                                Group By year_created
                                Order By year_created
                        ";
                return $sql;
		//return $q->query($sql);

		/*if($q->getNumRows()) {
			$q->fetch();
			return $q->getData('invoice_total');
		} else {
			return false;
		}*/

	}


	/**
	  * get the total Contact Invoices Paid
	  * @param integer $idcontact 
	  * @param string $year
	  * @return $sql
	*/

	function getTotalContactPaidYTD($idcontact,$year) {
		
                $sql = "SELECT SUM( net_total ) as invoice_total,DATE_FORMAT( datecreated,'%Y') as year_created
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcontact = {$idcontact}
				AND (status = 'Paid' OR status = 'Partial')
                                AND status <> 'Cancel'
                                AND datecreated like '%".$year."%'
                                Group By year_created
                                Order By year_created
                        ";
                return $sql;

	}


	/**
	  * get the total Contact Invoices Past Due
	  * @param integer $idcontact
	  * @param string $year
	  * @return $sql
	*/

	function getTotalContactPastDueYTD($idcontact,$year) {
		$today = date("Y-m-d");
		

                 $sql = "SELECT SUM( net_total ) as invoice_total,DATE_FORMAT( datecreated,'%Y') as year_created,due_date
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcontact = {$idcontact}
				AND status <> 'Quote'
				AND status <> 'Paid'
				AND due_date < '{$today}'
				AND status <> 'Cancel'
                                AND datecreated like '%".$year."%'
				Group By year_created
				Order By year_created
                        ";
                //echo $sql;
                return $sql;

	}


	/**
	  * get the total Company Invoices Paid
	  * @param integer $idcompany
	  * @param string $year
	  * @return $sql
	*/

	function getTotalCompanyQuotesYTD($idcompany,$year) {
		

                $sql = "SELECT SUM( net_total ) as invoice_total,DATE_FORMAT( datecreated,'%Y') as year_created,due_date
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcompany = {$idcompany}
				AND status = 'Quote'
                                AND status <> 'Cancel'
                                AND datecreated like '%".$year."%'
                                Group By year_created
                                Order By year_created
                        ";
                //echo $sql;
                return $sql;

	}


	/**
	  * get the total Company Invoices Year To Date
	  * @param integer  $idcompany
	  * @param string $year
	  * @return $sql
	*/

	function getTotalCompanyInvoiceYTD($idcompany,$year) {
		
                $sql = "SELECT SUM( net_total ) as invoice_total,DATE_FORMAT( datecreated,'%Y') as year_created,due_date
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcompany = {$idcompany}
				AND ( status = 'New' OR status = 'Sent')
                                AND status <> 'Cancel'
                                AND datecreated like '%".$year."%'
                                Group By year_created
                                Order By year_created
                        ";
                //echo $sql;
                return $sql;

	}


	/**
	  * get the total Company Invoices Paid Year To Date
	  * @param integer $idcompany
	  * @param string $year
	  * @return $sql
	*/

	function getTotalCompanyPaidYTD($idcompany,$year) {

                $sql = "SELECT SUM( net_total ) as invoice_total,DATE_FORMAT( datecreated,'%Y') as year_created,due_date
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcompany = {$idcompany}
				AND (status = 'Paid' OR status = 'Partial')
                                AND status <> 'Cancel'
                                AND datecreated like '%".$year."%'
                                Group By year_created
                                Order By year_created
                        ";
                //echo $sql;
                return $sql;

	}


	/**
	  * get the total Company Invoices Past Due Year To Date
	  * @param integer $idcompany
	  * @param string $year
	  * @return $sql
	*/

	function getTotalCompanyPastDueYTD($idcompany,$year) {
		$today = date("Y-m-d");
		//$q = new sqlQuery($this->getDbCon());

                $sql = "SELECT SUM( net_total ) as invoice_total,DATE_FORMAT( datecreated,'%Y') as year_created,due_date
				FROM `invoice`
				WHERE iduser = {$_SESSION['do_User']->iduser}
				AND idcompany = {$idcompany}
				AND status <> 'Quote'
				AND status <> 'Paid'
				AND due_date < '{$today}'
                                AND status <> 'Cancel'
                                AND datecreated like '%".$year."%'
                                Group By year_created
                                Order By year_created
                        ";
                //echo $sql;
                return $sql;

	}


	/**
	  * check if the there is an invoice for the suplied entity
	  * @param integer $id
	  * @param string $entity
	  * @return Num of record if found else return Boolean
	*/
        function hasInvoicesForEntity($id,$entity){
          $q = new sqlQuery($this->getDbCon());
          switch($entity){
              case 'Contact' : $qry = "select * from ".$this->table." where idcontact = ".$id.
                                      " AND (status = 'Quote' OR status = 'Paid' OR status ='Partial' OR
                                        status = 'Send' OR status = 'Sent' ) AND status <> 'Cancel'
                                      " ;
                               break;

              case 'Company' : $qry = "select * from ".$this->table." where idcompany = ".$id.
                                      " AND (status = 'Quote' OR status = 'Paid' OR status ='Partial' OR
                                        status = 'Send' OR status = 'Sent' ) AND status <> 'Cancel'
                                      " ;
                               break;
          }
          $q->query($qry);
          if($q->getNumRows()){
              return true;
          }else{ return false; }
        }


        /**
          * Display methods to display the invoice totals on the left side of contact and Company page
          * @param object $obj 
          * @param string $status
          * @param string $year
          * @param integer $idcontact
          * @param integer $idcompany
          * @return string $html_data
        */
        function displayInvoiceTotals($obj,$status,$year,$idcontact=0,$idcompany=0){
            $q = new sqlQuery($this->getDbCon());
            switch($status){
                  case "Quote" :
                                 if($idcontact != 0 ){ $rs = $this->getTotalContactQuotesYTD($idcontact,$year); }
                                 if($idcompany != 0 ){ $rs = $this->getTotalCompanyQuotesYTD($idcompany,$year); }
                                 $obj->addParam("status","Quote");
                                 break;
                  case "Invoice" :
                                 
                                 if($idcontact != 0 ){ $rs = $this->getTotalContactInvoiceYTD($idcontact,$year); }
                                 if($idcompany != 0 ){ $rs = $this->getTotalCompanyInvoiceYTD($idcompany,$year); }
                                 $obj->addParam("status","Invoice");
                                 break;
                  case "Paid" : 
                                 if($idcontact != 0 ){ $rs = $this->getTotalContactPaidYTD($idcontact,$year); }
                                 if($idcompany != 0 ){ $rs = $this->getTotalCompanyPaidYTD($idcompany,$year); }
                                 $obj->addParam("status","Paid");
                                 break;
                  case "Past Due" : 
                                 if($idcontact != 0 ){ $rs = $this->getTotalContactPastDueYTD($idcontact,$year); }
                                 if($idcompany != 0 ){ $rs = $this->getTotalCompanyPastDueYTD($idcompany,$year); }
                                 $obj->addParam("status","Overdue");
                                 break;
                                 
            }      
            $q->query($rs);
            if($q->getNumRows()){
              
                $html_data = '<tr><td>&nbsp;</td>';
                $html_data .='<td style="text-align:right;">';
                $html_data .= '<table width="100%">';
                while($q->fetch()){
                    $html_data .='<tr><td style="text-align:left;">'.$this->displayStatus($status).' :</td><td style="text-align:right;margin-right:10px;"> '.$obj->getLink($this->viewAmount($q->getData("invoice_total"))).'</td></tr>';
                }
                $html_data .='</table></td></tr>';
                return $html_data;
            }
        }
        
        
        /**
         * Display status
         * The status are hard coded in the database using the english language
         * In order to display those status in the proper language 
         * when need to have them going through the Gettext function. 
         * We can't change the values in the database to the proper langage as it will break
         * all the queries.
         * I did set this method as static so it can be called without instanciating the class.
         * @param string of the status current status as in db
         * @return string of the translated status
         */
        
        
        static function displayStatus($status) {
          $display_status = $status;
          switch($status) {
            case 'Quote' :
                $display_status = _('Quote');
                break;
            case 'Sent' :
                $display_status = _('Sent');
                break;
            case 'Invoice' :
                $display_status = _('Invoice');
                break;
            case 'Paid' : 
                $display_status = _('Paid');
                break;
            case 'Past Due' :
                $display_status = _('Past Due');
                break;
          }
          return $display_status;
        }
        
        
        /**
          * Get the Invoice Totals on on the status and then display with displayInvoiceTotals() with the Year
          * @param object $obj
          * @param integer $idcontact
          * @param integer $idcompany
          * @return $html_data
	  
        */
        function getInvoiceTotals($obj,$idcontact=0,$idcompany=0){
            $html_data = '';
            $q = new sqlQuery($this->getDbCon());
            if($idcontact != 0 ){
                  $q->query("select distinct(DATE_FORMAT( datecreated,'%Y')) as year_created from invoice where idcontact = ".$idcontact." AND status <> 'Cancel'");
            }
            else if($idcompany != 0 ){
              $q->query("select distinct(DATE_FORMAT( datecreated,'%Y')) as year_created from invoice where idcompany = ".$idcompany." AND status <> 'Cancel'");
            }
            //echo $q->getNumRows();
            if($q->getNumRows()){
              while($q->fetch()){
                if($q->getNumRows() == 1  && $q->getData("year_created") == date("Y")){
                      $html_data .= $this->displayInvoiceTotals($obj,"Quote",$q->getData("year_created"),$idcontact,$idcompany);
                      $html_data .= $this->displayInvoiceTotals($obj,"Invoice",$q->getData("year_created"),$idcontact,$idcompany);
                      $html_data .= $this->displayInvoiceTotals($obj,"Paid",$q->getData("year_created"),$idcontact,$idcompany);
                      $html_data .= $this->displayInvoiceTotals($obj,"Overdue",$q->getData("year_created"),$idcontact,$idcompany);
                }else{
                      $html_data .= '<tr><td  VALIGN="middle"><b>'.$q->getData("year_created").'</b></td></tr>';
                      $html_data .= $this->displayInvoiceTotals($obj,"Quote",$q->getData("year_created"),$idcontact,$idcompany);
                      $html_data .= $this->displayInvoiceTotals($obj,"Invoice",$q->getData("year_created"),$idcontact,$idcompany);
                      $html_data .= $this->displayInvoiceTotals($obj,"Paid",$q->getData("year_created"),$idcontact,$idcompany);
                      $html_data .= $this->displayInvoiceTotals($obj,"Overdue",$q->getData("year_created"),$idcontact,$idcompany);
                }
              }
            }
            return $html_data;
        }        



	/**
	  * Get Due Invoices for a contact
	  * @param integer $idcontact contact primarykey id
	  * Sets the Query in the Object
	*/
	//gets all the due invoices except the being viwed one for contact.
	function getDueInvoicesForContact($idcontact){

		$sql = "SELECT *
				FROM `{$this->table}`
				WHERE `idcontact` = {$idcontact}
                AND `due_date` < '".date("Y-m-d")."' AND `status` <> 'Quote' AND `status` <> 'Paid' AND `status` <> 'Cancel'
				AND idinvoice NOT IN ({$_SESSION['do_invoice']->idinvoice})
               ";

		$this->query($sql);

	}

	/**
	  * Event Method to hide Total 
	  * @param object $evtcl
	*/
	function eventHideTotal(EventControler $evtcl){
		$_SESSION["hide_total"] = 'display:none;';
		$_SESSION["show_total"] = 'display:inline;';
	}

	/**
	  * Event Method to Show Total 
	  * @param object $evtcl
	*/
	function eventShowTotal(EventControler $evtcl){
		$_SESSION["show_total"] = 'display:none;';
		$_SESSION["hide_total"] = 'display:inline;';
	}
	
  /**
   * Formatting the date as per the user setting
   * @param string $date
   * @return $formatted_date
   */
  function getInvFormattedDate($date) {
    $formatted_date = date($this->inv_dd_format,strtotime($date));
    return $formatted_date;
  }

  /**
   * This fetches invoice details for a particular Contact for loggedin User
   * @param inteher  $idcontact
   * @return query object
   */
  function getContactInvoiceDetails($idcontact) {
    $sql = "SELECT *
            FROM {$this->table}
            WHERE iduser = {$_SESSION['do_User']->iduser} AND idcontact = {$idcontact}
           ";
    $this->query($sql);
  }



/**
   * This fetches invoice details for a particular Contact for particular User [Inactive Users].
   * @param inteher  $idcontact
   * @return query object
   */
  function getContactInvoiceDetailsWithUser($idcontact,$iduser) {
    $sql = "SELECT *
            FROM {$this->table}
            WHERE iduser = {$iduser} AND idcontact = {$idcontact}
           ";
    $this->query($sql);
  }
  
  
  
/**
  * This fetches invoice details for a particular Contact for particular User [Inactive Users] with status as invoice generated for Plan upragde 21-01-2012
  * @param inteher  $idcontact
  * @return query object
  */
 function getContactInvoiceDetailsForPlanUpgrade($idcontact,$iduser) {
   $idinvoice = 0;
   $sql = "SELECT *
           FROM {$this->table}
           WHERE iduser = {$iduser} AND idcontact = {$idcontact} AND status = 'Invoice' AND description = 'Invoice for plan upgrade'
          ";

   $this->query($sql);
   
   if($this->getNumRows()){
	   while($this->fetch()){
		   $idinvoice = $this->getData("idinvoice");
		}
	 }
     return $idinvoice;
 }


  function getUserStripeDetails($iduser=''){
      if($iduser != ''){      
        $sql = "SELECT setting_name,setting_value FROM user
            INNER JOIN  user_settings on  user_settings.iduser = user.iduser where user.iduser = '{$iduser}'";
      }/*else{
        $sql = "SELECT setting_name,setting_value,user.iduser FROM user
                INNER JOIN  user_settings on  user_settings.iduser = user.iduser where isadmin  = 1 ";
      } */
    $this->query($sql);
    if($this->getNumRows() > 0){
        while($this->fetch()){
          $stripeDetails[$this->getData('setting_name')] = $this->getData('setting_value');
          $stripeDetails['iduser'] = $this->getData('iduser');
        }
      return $stripeDetails;    
    }else{
      return 0;
    }
  }

}
?>
