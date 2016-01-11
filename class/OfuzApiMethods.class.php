<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

   /**
    * Execute all the API methods
    *
    * Copyright 2002 - 2007 SQLFusion LLC
    * @author Abhik Chakraborty abhik@sqlfusion.com, Philippe phil@sqlfusion.com
    * @version 0.1
    * 
    * 
    *
    */
include_once('config.php');
include_once('class/OfuzApiBase.class.php');
class OfuzApiMethods extends OfuzApiBase {

    /*Constructor Function*/
    function OfuzApiMethods($output_type="json", $values=Array()){
              parent::OfuzApiBase($output_type, $values);
      }

    
    function add_contact(){
       $do_api_contact = new Contact();
       $do_Contact_View = new ContactView();
       $do_api_contact->addNew();
       $do_api_contact->firstname = $this->firstname;
       $do_api_contact->lastname = $this->lastname;
       $do_api_contact->position = $this->position;
       
       $do_api_contact->iduser = $this->iduser;
       if($this->tags !=''){
          $tags = explode(",",$this->tags);
       }
       if($this->firstname == "" && $this->lastname == ""){
           $this->setMessage("610","First Name OR Last Name is Required");
	   return false;
       }elseif(!$this->iduser){
           $this->setMessage("502","The User Session is expired");
	   return false; 
       }elseif($idcontact =  $do_api_contact->duplicateContact($this->iduser,$this->email_work,$this->email_home,$this->email_other)){
           $this->setMessage("613","The Contact is duplicated. Contact ID:  ".$idcontact);
	   return false; 
       }else{
            if($this->company != ""){
                $do_api_contact->company = $this->company;
                $do_api_company = new Company();
                $idcompany = $do_api_company->isDuplicateCompany($this->company,$this->iduser);
                if(!$idcompany){ 
                    $do_api_company->addNew();
                    $do_api_company->iduser = $this->iduser;
                    $do_api_company->name = trim($this->company);
                    $do_api_company->add();
                    $this->idcompany = $do_api_company->getPrimaryKeyValue();
                }else{
                    $this->idcompany = $idcompany;
                }
              $do_api_contact->idcompany = $this->idcompany;
            }
            
            $do_api_contact->add();
            $this->idcontact = $do_api_contact->getPrimaryKeyValue(); 
            $do_api_contact->idcontact = $this->idcontact;
            //child data starts here
      
            // Phones
            if($this->phone_work != ""){ $do_api_contact->addPhone($this->phone_work,"Work"); } 
            if($this->phone_home != ""){ $do_api_contact->addPhone($this->phone_home,"Home"); }
            if($this->mobile_number != ""){ $do_api_contact->addPhone($this->mobile_number,"Mobile"); }
            if($this->fax_number != ""){ $do_api_contact->addPhone($this->fax_number,"Fax"); }
            if($this->phone_other != ""){ $do_api_contact->addPhone($this->phone_other,"Other"); }
            
            //emails 
            if($this->email_work !=""){ $do_api_contact->addEmail($this->email_work,"Work"); } 
            if($this->email_home !=""){ $do_api_contact->addEmail($this->email_home,"Home"); } 
            if($this->email_other !=""){ $do_api_contact->addEmail($this->email_other,"Other"); }       
      
            //Website
            if($this->company_website !=""){ $do_api_contact->addWebsite($this->company_website,"Company"); } 
            if($this->personal_website !=""){ $do_api_contact->addWebsite($this->personal_website,"Personal"); } 
            if($this->blog_url !=""){ $do_api_contact->addWebsite($this->blog_url,"Blog"); } 
            if($this->twitter_profile_url !=""){ $do_api_contact->addWebsite($this->twitter_profile_url,"Twitter"); } 
            if($this->linkedin_profile_url !=""){ $do_api_contact->addWebsite($this->linkedin_profile_url,"LinkedIn"); } 
            if($this->facebook_profile_url !=""){ $do_api_contact->addWebsite($this->facebook_profile_url,"Facebook"); } 
            // API V.02 will have IM and Address

            //Add tags if any
            if(is_array($tags)){
                $do_api_tags = new Tag();
                foreach($tags as $tag){
                    $do_api_tags->addNew();
                    $do_api_tags->addTagAssociation($this->idcontact, $tag, "contact",$this->iduser);
                }
            }
            // Ok here the last thing that needs to be done so that the contact should also on the table 
            $do_Contact_View = new ContactView();
            $do_Contact_View->setUser($this->iduser);
            $do_api_contact->getId($this->idcontact);
            $do_Contact_View->addFromContact($do_api_contact);
			
            $do_Contact_View->updateFromContact($do_api_contact);
			
            if($this->tags !=''){
                $do_Contact_View->addTag($this->tags,$this->idcontact);
            }
            
            $this->setValues(Array("msg" => "Contact Added", "stat" => "ok", "code" => "600", "idcontact" => $this->idcontact));
            return true;
      }

    }

    function search_contact(){
        if($this->firstname =='' && $this->lastname == '' && $this->email == ''){
           $this->setMessage("611","No search criteria found");
	   return false;
        }else{
           $do_api_contact = new Contact();
           $res = $do_api_contact->apiSearchContact($this->iduser,$this->firstname,$this->lastname,$this->email);
           if($res){
              $this->setValues($res);
              return true;
           }else{
              $this->setMessage("612","No Contact found with the searched criteria supplied");
              return false;
           }
        }
    }

    function get_contact_id(){
        if($this->firstname =='' && $this->lastname == '' && $this->email == ''){
           $this->setMessage("611","No search criteria found");
	   return false;
        }else{
           $do_api_contact = new Contact();
           $res = $do_api_contact->apiGetContactId($this->iduser,$this->firstname,$this->lastname,$this->email);
           if($res){
              $this->setValues($res);
              return true;
           }else{
              $this->setMessage("612","No Contact found with the searched criteria supplied");
              return false;
           }
        }
    }
    
    function get_contacts() {
        $do_api_contact = new Contact();
        $res = $do_api_contact->getContactsForAPI($this->iduser);
        if($res){
              $this->setValues($res);
              return true;
           }else{
              $this->setMessage("612","No Contacts found");
              return false;
           }
    }
    
    function add_tag(){ 
          $do_api_contact = new Contact();
          if(!$this->idreference || $this->idreference == ''){
              $this->setMessage("621","Reference Id Missing"); 
	      return false;
          }elseif( $this->tags == '' ){
              $this->setMessage("622","Tag missing");
	      return false;
          }elseif(!$do_api_contact->isContactRelatedToUser($this->idreference,$this->iduser)){ 
              $this->setMessage("615","Contact does not belong to you nor shared by your Co-Worker");
	      return false;
          }else{
              $do_api_tags = new Tag();
              $tags = explode(",",$this->tags);
              foreach($tags as $tag){
                    $do_api_tags->addNew();
                    $do_api_tags->addTagAssociation($this->idreference, $tag, $this->reference,$this->iduser);
               }
              $this->setValues(Array("msg" => "Tags Added", "stat" => "ok", "code" => "620"));
              return true;
          }
    }

    function delete_tag(){
      $do_api_contact = new Contact();
      if(!$this->idreference || $this->idreference == ''){
              $this->setMessage("621","Reference Id Missing"); 
	      return false;
       }elseif( $this->tags == '' ){
              $this->setMessage("622","Tag missing");
	      return false;
       }elseif(!$do_api_contact->isContactRelatedToUser($this->idreference,$this->iduser)){
              $this->setMessage("615","Contact does not belong to you nor shared by your Co-Worker");
	      return false;
       }else{
         $do_api_tags = new Tag();
         $tags = explode(",",$this->tags);
         foreach($tags as $tag){
            if($idtag = $do_api_tags->isTagExistsForReferer($tag,$this->idreference,$this->iduser,$this->reference)){
                $do_api_tags->delTagById($idtag);
            }
         }
         $this->setValues(Array("msg" => "Tags Deleted", "stat" => "ok", "code" => "626"));
         return true; 
       }
    }

    function add_note(){
        $max_note_length = 200 ;
      /* 
           May be need to limit the note text as in 
           RFC 2068 states:

          Servers should be cautious about depending on URI lengths above 255 bytes, because some older client or proxy implementations may not properly support these lengths.

          The spec for URL length does not dictate a minimum or maximum URL length, but implementation varies by browser. On Windows: Opera supports ~4050 characters, IE 4.0+ supports exactly 2083 characters, Netscape 3 -> 4.78 support up to 8192 characters before causing errors on shut-down, and Netscape 6 supports ~2000 before causing errors on start-up.  
      */
      $do_api_contact = new Contact();
      if(!$this->idcontact || $this->idcontact == ''){
          $this->setMessage("621","Contact Id Missing"); 
          return false;
       }elseif($this->note == ''){
          $this->setMessage("631","Note Missing"); 
	  return false;
       }elseif(strlen($this->note) >$max_note_length){
          $msg = 'Note lenght is exceding the limit allowed lenght is '.$max_note_length. ' characters'.
          $this->setMessage("632",$msg); 
	  return false;
       }elseif(!$do_api_contact->isContactRelatedToUser($this->idcontact,$this->iduser)){
          $this->setMessage("615","Contact does not belong to you nor shared by your Co-Worker");
	  return false;
       }else{
          $do_api_contact_notes = new ContactNotes();
          $do_api_contact_notes->addNew();
          $do_api_contact_notes->note = $this->note;
          $do_api_contact_notes->iduser = $this->iduser;
          $do_api_contact_notes->idcontact = $this->idcontact;
          $do_api_contact_notes->date_added = date("Y-m-d");
          $do_api_contact_notes->note_visibility = $this->note_visibility;
          $do_api_contact_notes->add();
          $this->setValues(Array("msg" => "Note Added", "stat" => "ok", "code" => "636"));
          return true; 
       }
    }

    function add_task(){
        $do_api_task = new Task();
        $do_api_contact = new Contact();
        $add_task = true;
        if($this->task_description == ''){ 
             $this->setMessage("644","Empty Task Description"); 
             $add_task = false;
             return false;
        }elseif( ($this->idcontact || $this->idcontact != '') && !$do_api_contact->isContactRelatedToUser($this->idcontact,$this->iduser)){
            $this->setMessage("615","Contact does not belong to you nor shared by your Co-Worker");
            $add_task = false;
	    return false;
        }else{
              if($this->due_date != ''){
                    if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $this->due_date, $parts)){
                        if(checkdate($parts[2],$parts[3],$parts[1])){
                            $difference = strtotime($this->due_date) - strtotime($today);
                            $date_diff = round((($difference/60)/60)/24,0);
                            if($date_diff < 0 ){
                              $due_date_dateformat = date("Y-m-d");
                              $due_date_str = 'Today';
                            }else{
                              $due_date_dateformat = $this->due_date;
                              $due_date_str  = $do_api_task->convertDateToString($due_date_dateformat);
                            }
                        }else{
                            $this->setMessage("641","Invalid date Format should be Y-m-d(2000-12-31)"); 
                            $add_task = false;
                            return false;
                        }
                    }else{
                        $this->setMessage("642","Invalid date"); 
                        $add_task = false;
                        return false;
                    }
              }
              if($add_task){
                    if($this->due_date == ''){
                        $due_date_dateformat = date("Y-m-d");
                        $due_date_str = 'Today';
                    }
                    if($this->task_category == ''){ $this->task_category = 'Email'; }
                    if(!$this->idcontact || $this->idcontact == ''){ $this->idcontact = 0 ; }
                    $do_api_task->task_description = $this->task_description;
                    $do_api_task->due_date = $due_date_str;
                    $do_api_task->due_date_dateformat = $due_date_dateformat;
                    $do_api_task->idcontact = $this->idcontact;
                    $do_api_task->iduser = $this->iduser;
                    $do_api_task->status = 'open';
                    $do_api_task->task_category = $this->task_category;
                    $do_api_task->add();
                    $this->setValues(Array("msg" => "Task Added", "stat" => "ok", "code" => "645"));
                    return true; 
              }
        }       
    }

    /*
        Method adding invoice 
    */
	
	/** Different than the one in MethodsPrivate seems like OfuzAPiMethodPrivate is more tested so using 
	 * the OfuzApiMethodPrivates and commenting those
    function add_invoice(){
        $add_call_back_url = false ; 
        $do_contact_invoice = new Contact();
        $do_api_invoice = new Invoice();
        if(!$do_contact_invoice->isContactOwner($this->idcontact,$this->iduser)){
             $this->setMessage("616","Contact does not belong to you");
	     return false;
        }elseif($this->type !='Quote' && $this->type !='Invoice'){
             $this->setMessage("701","Type can be either Quote or Invoice");
	     return false;
        }elseif(!preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $this->due_date, $parts)){
             $this->setMessage("641","Invalid date Format should be Y-m-d(2000-12-31)");
	     return false;
        }elseif(!preg_match("/^[0-9]+$/",$this->discount,$parts)
                && !preg_match("/^[0-9]+\.[0-9]*$/",$this->discount,$parts) 
                && !preg_match("/^\.[0-9]*$/",$this->discount,$parts)){
              $this->setMessage("702","Invalid Discount. (Correct Format : 10,10.55,0.50,.5)");
	      return false;
        }elseif($this->callback_url != '' && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->callback_url)){
            $this->setMessage("722","Invalid Call Back URL");
	    return false;
        }else{
            //$do_contact_invoice->sql_view_name = "userid".$this->iduser."_contact"; // Setting the sql_view_name
            if($this->callback_url != '' && preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->callback_url)){
                $add_call_back_url = true ; 
            }
            if($this->type == 'Invoice'){ $this->type = 'New'; }
            $do_api_invoice->addNew();
            $do_api_invoice->idcontact = $this->idcontact ;
            $do_api_invoice->iduser = $this->iduser ;
            $do_api_invoice->status = $this->type ; // could be status
            $do_api_invoice->num = $do_api_invoice->getUniqueInvoiceNum($this->iduser) ;
            $do_api_invoice->datecreated = date("Y-m-d");
            $do_api_invoice->due_date = $this->due_date;
            $do_contact_invoice->setSqlViewName($this->iduser);//  set the sqlview name
            $do_api_invoice->invoice_address = $do_contact_invoice->ajaxGetInvoiceAddress($this->idcontact);
            $do_api_invoice->invoice_term = $this->invoice_term;
            $do_api_invoice->invoice_note = $this->invoice_note;
            $do_api_invoice->description = $this->description;
            $do_contact_invoice->getId($this->idcontact);
            $do_api_invoice->idcompany = $do_contact_invoice->idcompany;
            $do_api_invoice->discount = $this->discount;
            $do_api_invoice->add();
            $inserted_id = $do_api_invoice->getPrimaryKeyValue();
            if($add_call_back_url){
                $do_api_inv_call_back = new InvoiceCallback();
                $callback = $do_api_inv_call_back->addCallBackUrl($inserted_id,$this->callback_url);
            }
            $do_api_user_rel = new UserRelations();
            $inv_url =  $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_api_user_rel->encrypt($inserted_id).'/'.$do_api_user_rel->encrypt($this->idcontact);
            if($callback !== true){
                $this->setValues(Array("msg" => "Invoice Added-Call back URL exists", "stat" => "ok", "code" => "710","idinvoice"=>$inserted_id, "invoice_url"=>$inv_url,"callback_url"=>$callback));
            }else{          
                $this->setValues(Array("msg" => "Invoice Added", "stat" => "ok", "code" => "710","idinvoice"=>$inserted_id, "invoice_url"=>$inv_url));
            }
            return true;
         } 
    }
    */
    /*
        Method adding the invoice_line
    */
	/** Commenting as duplicate from MethodsPrivate 
    function add_invoice_line(){
        
        $do_api_inv_line = new InvoiceLine();
        $do_api_invoice = new Invoice();
        if(!$do_api_invoice->isInvoiceOwner($this->idinvoice,$this->iduser)){
            $this->setMessage("711","Invoice does not belong to you");
	    return false;
        }elseif(!preg_match("/^[0-9]+$/",$this->price,$parts)
                && !preg_match("/^[0-9]+\.[0-9]*$/",$this->price,$parts) 
                && !preg_match("/^\.[0-9]*$/",$this->price,$parts)){
             $this->setMessage("703","Invalid Amount. (Correct Format : 100.59 , 100 , 0.78, .78 , 2987 )");
	     return false; 
        }elseif(!preg_match("/^[0-9]+$/",$this->qty,$parts)
                && !preg_match("/^[0-9]+\.[0]*$/",$this->qty,$parts) 
                ){
             $this->setMessage("704","Invalid Quantity. (Correct Format : 5 , 5.0 )");
	     return false; 
        }else{
            $do_api_inv_line->addNew();
            $do_api_inv_line->idinvoice = $this->idinvoice;
            $do_api_inv_line->description = $this->description;
            $do_api_inv_line->price = $this->price;
            $do_api_inv_line->qty = $this->qty;
            $do_api_inv_line->item = $this->item;
            $do_api_inv_line->total = floatval($this->price) * floatval($this->qty);
            $do_api_inv_line->add();
            $do_api_invoice->setInvoiceCalculations($this->idinvoice);
            $this->setValues(Array("msg" => "Invoice Line Added for the idinvoice".$this->idinvoice, "stat" => "ok", "code" => "720","idinvoice"=>$inserted_id));
            return true; 
        }
    }
    **/
    /*
      Method to make an invocie as Recurrent
    */
    /** Commenting as duplicate from OfuzApiMethodsPrivate
    function add_recurrent(){
        $do_api_invoice = new Invoice();
        $do_api_rec_invocie = new RecurrentInvoice();
        if(!$do_api_invoice->isInvoiceOwner($this->idinvoice,$this->iduser)){
            $this->setMessage("711","Invoice does not belong to you");
	    return false;
        }elseif(!in_array($this->recurrencetype,$do_api_rec_invocie->frequencyComboArray)){
            $this->setMessage("731","Invalid Invoice Recurrent Type");
	    return false;  
        }elseif(!preg_match("/^[0-9]*$/",$this->recurrence,$parts) || $this->recurrence == 0){
            $this->setMessage("732","Invalid Invoice Recurrent Value");
	    return false;  
        }elseif($this->callback_url != '' && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->callback_url)){
            $this->setMessage("722","Invalid Call Back URL");
	    return false;
        }elseif($do_api_rec_invocie->checkIfInvoiceIsInRecurrent($this->idinvoice)){
            $this->setMessage("733","Invoice is already set to recurrent idinvoice :".$this->idinvoice);
            return false;
        }else{
            //add Recurrent Here
            if($this->callback_url != '' && preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->callback_url)){
                $add_call_back_url = true ; 
            }
             if($add_call_back_url){
                $do_api_inv_call_back = new InvoiceCallback();
                $callback = $do_api_inv_call_back->addCallBackUrl($this->idinvoice,$this->callback_url);
            }
            $do_api_invoice->getId($this->idinvoice);
            $do_api_rec_invocie->addRecurrentInvoice($this->idinvoice,$this->recurrence,$this->recurrencetype,$do_api_invoice->datecreated,$this->iduser);
            
            if($callback !== true){
                $this->setValues(Array("msg" => "Invoice has been set as recurrent for-Call back URL exists for idinvoice : ".$this->idinvoice, "stat" => "ok", "code" => "730","callback_url"=>$callback));
            }else{
                $this->setValues(Array("msg" => "Invoice has been set as recurrent for ".$this->idinvoice, "stat" => "ok", "code" => "730"));
            }
            return true; 
        }
    }
	*/
	

    /*
        Method adding invoice 
    */
    function add_invoice(){
        $add_call_back_url = false ; 
        $do_contact_invoice = new Contact();
        $do_api_invoice = new Invoice();
        if(!$do_contact_invoice->isContactOwner($this->idcontact,$this->iduser)){
             $this->setMessage("616","Contact does not belong to you");
	     return false;
        }elseif($this->type !='Quote' && $this->type !='New'){
             $this->setMessage("701","Type can be either Quote or Invoice");
	     return false;
        }elseif(!preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $this->due_date, $parts)){
             $this->setMessage("641","Invalid date Format should be Y-m-d(2000-12-31)");
	     return false;
        }/*elseif($this->discount!= '0' && $this->discount!= '' && !empty($this->discount) && !preg_match("/^[0-9]+$/",$this->discount,$parts)
                && !preg_match("/^[0-9]+\.[0-9]*$/",$this->discount,$parts) 
                && !preg_match("/^\.[0-9]*$/",$this->discount,$parts)){
              $this->setMessage("702","Invalid Discount. (Correct Format : 10,10.55,0.50,.5)");
	      return false;
        }*/elseif($this->callback_url != '' && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->callback_url)){
            $this->setMessage("722","Invalid Call Back URL");
	    return false;
        }elseif($this->next_url != '' && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->next_url)){
            $this->setMessage("723","Invalid Next URL");
	    return false;
        }else{
            //$do_contact_invoice->sql_view_name = "userid".$this->iduser."_contact"; // Setting the sql_view_name
            if($this->callback_url != '' || $this->next_url != ''){
                $add_call_back_url = true ; 
            }
            //if($this->type == 'Invoice'){ $this->type = 'New'; }
            $do_api_invoice->addNew();
            $do_contact_invoice->setSqlViewName($this->iduser);//  set the sqlview name
            $do_api_invoice->idcontact = $this->idcontact ;
            $do_api_invoice->iduser = $this->iduser ;
            $do_api_invoice->status = $this->type ; // could be status
            $do_api_invoice->num = $do_api_invoice->getUniqueInvoiceNum($this->iduser) ;
            $do_api_invoice->datecreated = date("Y-m-d");
            $do_api_invoice->due_date = $this->due_date;
            $do_api_invoice->invoice_address = $do_contact_invoice->ajaxGetInvoiceAddress($this->idcontact);
            $do_api_invoice->invoice_term = $this->invoice_term;
            $do_api_invoice->invoice_note = $this->invoice_note;
            $do_api_invoice->description = $this->description;
            $do_contact_invoice->getId($this->idcontact);
            $do_api_invoice->idcompany = $do_contact_invoice->idcompany;
            $do_api_invoice->discount = $this->discount;
            $do_api_invoice->add();
            $idinvoice = $do_api_invoice->getPrimaryKeyValue();
            if($add_call_back_url){
                $do_api_inv_call_back = new InvoiceCallback();
                $callback = $do_api_inv_call_back->addCallBackUrl($idinvoice,$this->callback_url,$this->next_url);
            }
            $do_api_user_rel = new UserRelations();
            $inv_url =  $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_api_user_rel->encrypt($idinvoice).'/'.$do_api_user_rel->encrypt($this->idcontact);
			$pay_url =  $GLOBALS['cfg_ofuz_site_https_base'].'pay/'.$do_api_user_rel->encrypt($idinvoice).'/'.$do_api_user_rel->encrypt($this->idcontact);
            if($callback === true){
                $this->setValues(Array("msg" => "Invoice Added- with Call back URL", 
				                       "stat" => "ok", 
									   "code" => "710",
									   "idinvoice"=>$idinvoice, 
									   "invoice_url"=>$inv_url,
									   "payment_url"=>$pay_url,
									   "callback_url"=>$callback));
            }else{          
                $this->setValues(Array("msg" => "Invoice Added", 
				                       "stat" => "ok", 
									   "code" => "710",
									   "idinvoice"=>$idinvoice, 
									   "invoice_url"=>$inv_url,
									   "payment_url"=>$pay_url));
            }
            return true;
         } 
    }

    /*
        Method adding the invoice_line
    */
    function add_invoice_line(){
        
        $do_api_inv_line = new InvoiceLine();
        $do_api_invoice = new Invoice();
        if(!$do_api_invoice->isInvoiceOwner($this->idinvoice,$this->iduser)){
            $this->setMessage("711","Invoice does not belong to you");
	    return false;
        }elseif(!preg_match("/^[0-9]+$/",$this->price,$parts)
                && !preg_match("/^[0-9]+\.[0-9]*$/",$this->price,$parts) 
                && !preg_match("/^\.[0-9]*$/",$this->price,$parts)){
             $this->setMessage("703","Invalid Amount. (Correct Format : 100.59 , 100 , 0.78, .78 , 2987 )");
	     return false; 
        }elseif(!preg_match("/^[0-9]+$/",$this->qty,$parts)
                && !preg_match("/^[0-9]+\.[0]*$/",$this->qty,$parts) 
                ){
             $this->setMessage("704","Invalid Quantity. (Correct Format : 5 , 5.0 )");
	     return false; 
        }else{
            $do_api_inv_line->addNew();
            $do_api_inv_line->idinvoice = $this->idinvoice;
            $do_api_inv_line->description = $this->description;
            $do_api_inv_line->price = $this->price;
            $do_api_inv_line->qty = $this->qty;
            $do_api_inv_line->item = $this->item;
            $do_api_inv_line->total = floatval($this->price) * floatval($this->qty);
            $do_api_inv_line->add();
            $do_api_invoice->setInvoiceCalculations($this->idinvoice);
            $this->setValues(Array("msg" => "Invoice Line Added for the idinvoice".$this->idinvoice, "stat" => "ok", "code" => "720","idinvoice"=>$inserted_id));
            return true; 
        }
    }

    /*
      Method to make an invocie as Recurrent
    */
    
    function add_recurrent(){
        $do_api_invoice = new Invoice();
        $do_api_rec_invoice = new RecurrentInvoice();
        if(!$do_api_invoice->isInvoiceOwner($this->idinvoice,$this->iduser)){
            $this->setMessage("711","Invoice does not belong to you");
	    return false;
        }elseif(!in_array($this->recurrencetype,$do_api_rec_invoice->frequencyComboArray)){
            $this->setMessage("731","Invalid Invoice Recurrent Type");
	    return false;  
        }elseif(!preg_match("/^[0-9]*$/",$this->recurrence,$parts) || $this->recurrence == 0){
            $this->setMessage("732","Invalid Invoice Recurrent Value");
	    return false;  
        }elseif($this->callback_url != '' && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->callback_url)){
            $this->setMessage("722","Invalid Call Back URL");
	    return false;
        }elseif($this->next_url != '' && !preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->next_url)){
            $this->setMessage("723","Invalid Next URL");
	    return false;
        }elseif($do_api_rec_invoice->checkIfInvoiceIsInRecurrent($this->idinvoice)){
            $this->setMessage("733","Invoice is already set to recurrent idinvoice :".$this->idinvoice);
            return false;
        }else{
            //add Recurrent Here
            if($this->callback_url != '' || $this->next_url != '' ){
                $add_call_back_url = true ; 
            }
             if($add_call_back_url){
                $do_api_inv_call_back = new InvoiceCallback();
                $callback = $do_api_inv_call_back->addCallBackUrl($this->idinvoice,$this->callback_url,$this->next_url);
            }
            $do_api_invoice->getId($this->idinvoice);
            $do_api_rec_invoice->addRecurrentInvoice($this->idinvoice,$this->recurrence,$this->recurrencetype,$do_api_invoice->datecreated,$this->iduser);
            
            if($callback !== true){
                $this->setValues(Array("msg" => "Invoice has been set as recurrent for-Call back URL exists for idinvoice : ".$this->idinvoice, "stat" => "ok", "code" => "730","callback_url"=>$callback));
            }else{
                $this->setValues(Array("msg" => "Invoice has been set as recurrent for ".$this->idinvoice, "stat" => "ok", "code" => "730"));
            }
            return true; 
        }
    }
    	
    /**
	 * Delete recurence form an invoice
	 */
	function stop_recurrent() {
        $do_api_invoice = new Invoice();
        $do_api_rec_invoice = new RecurrentInvoice();
        if(!$do_api_invoice->isInvoiceOwner($this->idinvoice,$this->iduser)){
            $this->setMessage("711","Invoice does not belong to you");
			return false;
	    } elseif($do_api_rec_invoice->checkIfInvoiceIsInRecurrent($this->idinvoice)){
			if ($do_api_rec_invoice->deleteRecurrentInvoice($this->idinvoice)) {
				    $this->setMessage("750","Recurence for invoice ".$this->idinvoice." as been removed", "ok");
					return True;
			} else {
				$this->setMessage("733","Invoice:".$this->idinvoice." Doesn't has any recurrence", "fail");
				return false;
			}
        }else{		
			$this->setMessage("733","Invoice:".$this->idinvoice." Doesn't has any recurrence", "fail");
            return false;
		}
	}
	
    /*
        Function to get the due amount for an invoice
    */
    function get_inv_amt_due(){
        $do_api_invoice = new Invoice();
		$do_api_invoice->getId($this->idinvoice);
		if ($do_api_invoice->isInvoiceOwner($this->idinvoice, $this->iduser)) { 
			$due_amt = $do_api_invoice->getDueAmount($this->idinvoice);
			$this->setValues(Array("due_amount"=>$due_amt, "idinvoice"=>$this->idinvoice));
			return true; 
		} else {
			$this->setMessage("711","Invoice does not belong to you".$this->idinvoice."_".$this->iduser, 'fail');
			return false;
		}
    } 
	 
	/* 
	 * function get contact subscriptions
	 * Return the detail of the current subscription
	 * invoice and invoice line.
	 */
	function get_contact_subscription() {
		$do_invoice = new Invoice();
		if ($this->idcontact) {
			$do_invoice->query("SELECT * FROM ".$do_invoice->getTable()." WHERE iduser=".$this->iduser." AND idcontact=".$this->idcontact." ORDER BY datecreated DESC LIMIT 1");
		} elseif($this->idcompany) {
			$do_invoice->query("SELECT * FROM ".$do_invoice->getTable()." WHERE iduser=".$this->iduser." AND idcompany=".$this->idcompany." ORDER BY datecreated DESC LIMIT 1");			
		} else {
			$this->setMessage("404", "A contact or a Company id is required", 'fail');
			return false;
		}
		//$do_invoice->fetch();
		if ($do_invoice->getNumRows() == 1) {
			$do_rec_invoice = $do_invoice->getChildRecurrentInvoice();
			if ($do_rec_invoice->hasData()) {
				$do_invoice_line = $do_invoice->getChildInvoiceLine();
				$i=0;
				while($do_invoice_line->next()) {
					$invoice_line[$i] = Array ('item' => $do_invoice_line->item, 
					                         'description' => $do_invoice_line->description,
					                         'price' => $do_invoice_line->price,
										     'qty' => $do_invoice_line->qty);
					$i++;
				}
				$this->setValues ( Array (
				             'next_charge_date' => $do_rec_invoice->nextdate,
							 'recurrence' => $do_rec_invoice->recurrence,
							 'recurrence_frequency' => $do_rec_invoice->recurrencetype,
							 'idinvoice' => $do_rec_invoice->idinvoice,
							 'number' => $do_invoice->num,
							 'description' => $do_invoice->description,
							 'amount' => $do_invoice->amount,
							 'date_created' => $do_invoice->datecreated,
							 'date_due' => $do_invoice->due_date,
							 'amount_due' => $do_invoice->amt_due,
							 'line_item' => $invoice_line
								) );
				return true;
			} else {
				$this->setMessage("734","Invoice does not have a recurrence or is not a subscription", 'fail');
				return false;			
			}
		} else {
			$this->setMessage("800","No Invoice found", 'fail');
			return false;				
		}
	}
	
	/*method add invoice for upgrade 21-01-2012*/
	
	function upgrade_plan_add_invoice(){ 
        $add_call_back_url = false ; 
        $do_contact_invoice = new Contact();
        $do_api_invoice = new Invoice();
        
		$do_api_invoice->addNew();
		$do_contact_invoice->setSqlViewName($_SESSION["do_User"]->iduser);//  set the sqlview name
		$do_api_invoice->idcontact = $this->idcontact ;
		$do_api_invoice->iduser = $this->iduser ;
		$do_api_invoice->status = $this->type ; // could be status
		$do_api_invoice->num = $do_api_invoice->getUniqueInvoiceNum($this->iduser) ;
		$do_api_invoice->datecreated = date("Y-m-d");
		$do_api_invoice->due_date = $this->due_date;
		$do_api_invoice->invoice_address = $do_contact_invoice->ajaxGetInvoiceAddress($this->idcontact);
		$do_api_invoice->invoice_term = $this->invoice_term;
		$do_api_invoice->invoice_note = $this->invoice_note;
		$do_api_invoice->description = $this->description;
		$do_contact_invoice->getId($this->idcontact);
		$do_api_invoice->idcompany = $do_contact_invoice->idcompany;
		$do_api_invoice->amt_due = $this->amt_due;
		$do_api_invoice->sub_total = $this->sub_total;
		$do_api_invoice->net_total = $this->net_total;
		$do_api_invoice->discount = $this->discount;
		$do_api_invoice->add();
		$idinvoice = $do_api_invoice->getPrimaryKeyValue();

  /** 
    * Will Add an entry to the Invoice line table 
    * */
  if($_SESSION["upgrade"]){
    $do_invoice_line = new InvoiceLine();
    $do_invoice_line ->idinvoice  = $idinvoice;
    $do_invoice_line->item = 'User Plan Upgrade';
    $do_invoice_line->price = $this->amt_due;
    $do_invoice_line->qty = 1;
    $do_invoice_line->total = $this->amt_due;
    $do_invoice_line->description = $this->description;
    $do_invoice_line->add();
  }

  //add to table invoice call back for trail users
  $url = $this->url;$next_url=$this->next_url;
		
		$invoice_call_back = new InvoiceCallback();
		$invoice_call_back->addCallBackUrl($idinvoice,$url,$next_url);
		
  /*if($add_call_back_url){
      $do_api_inv_call_back = new InvoiceCallback();
      $callback = $do_api_inv_call_back->addCallBackUrl($idinvoice,$this->callback_url,$this->next_url);
  }*/
  $do_api_user_rel = new UserRelations();
      //$inv_url =  $GLOBALS['cfg_ofuz_site_https_base'].'inv/'.$do_api_user_rel->encrypt($idinvoice).'/'.$do_api_user_rel->encrypt($this->idcontact);
			$pay_url =  $GLOBALS['cfg_ofuz_site_https_base'].'pay/'.$do_api_user_rel->encrypt($idinvoice).'/'.$do_api_user_rel->encrypt($this->idcontact);
   //$inv_url =  'http://ofuz.localhost/inv/'.$do_api_user_rel->encrypt($idinvoice).'/'.$do_api_user_rel->encrypt($this->idcontact);
   //$pay_url =  'http://ofuz.localhost/pay/'.$do_api_user_rel->encrypt($idinvoice).'/'.$do_api_user_rel->encrypt($this->idcontact);
  return $pay_url;          
  }








  /**
   * Invoice generation for paid customers for recurrent billing 
   * @param INT $iduser
   * @return INT $idinvoice
   * @see corn_billing.php
   **/
  function cron_billing_add_invoice($iduser){         
    $do_contact_invoice = new Contact(NULL,'',$iduser);
    $do_api_invoice = new Invoice();
       
    $do_api_invoice->addNew(); 
    $do_contact_invoice->setSqlViewName($iduser);//  set the sqlview name
    $do_api_invoice->idcontact = $this->idcontact ;
    $do_api_invoice->iduser = $this->iduser ;
    $do_api_invoice->status = $this->type ; // could be status
    $do_api_invoice->num = $do_api_invoice->getUniqueInvoiceNum($this->iduser) ;
    $do_api_invoice->datecreated = date("Y-m-d");
    $do_api_invoice->due_date = $this->due_date;
    $do_api_invoice->invoice_address = $do_contact_invoice->ajaxGetInvoiceAddress($this->idcontact);
    $do_api_invoice->invoice_term = $this->invoice_term;
    $do_api_invoice->invoice_note = $this->invoice_note;
    $do_api_invoice->description = $this->description;
    $do_contact_invoice->getId($this->idcontact);
    $do_api_invoice->idcompany = $do_contact_invoice->idcompany;
    $do_api_invoice->amt_due = $this->amt_due;
    $do_api_invoice->sub_total = $this->sub_total;
    $do_api_invoice->net_total = $this->net_total;
    $do_api_invoice->discount = $this->discount;
    $do_api_invoice->add();
    $idinvoice = $do_api_invoice->getPrimaryKeyValue();

    /** 
      * Will Add an entry to the Invoice line table 
      **/  
    $do_invoice_line = new InvoiceLine();
    $do_invoice_line ->idinvoice  = $idinvoice;
    $do_invoice_line->item = 'User Plan Upgrade';
    $do_invoice_line->price = $this->amt_due;
    $do_invoice_line->qty = 1;
    $do_invoice_line->total = $this->amt_due;
    $do_invoice_line->description = $this->description;
    $do_invoice_line->add();
    return $idinvoice;
  }



























	
	
}
