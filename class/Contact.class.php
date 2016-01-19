<?php 
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 


    /**
      * Contact class
      * Using the DataObject
      * 
      * Method to reuse:
      * sendMessage() its a very simple and easy way to send messages to contacts.
      * 
      * Most of the Methods and Events related to the Contact in Ofuz are defined here.
      *
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzCore
      * @license GNU Affero General Public License
      * @version 0.6
      * @date 2010-09-03
      * @since 0.1
      */
   
class Contact extends DataObject {
    
    public $table = "contact";
    protected $primary_key = "idcontact";
    
    private $report = Array (
      "list_contacts","i_list_contacts"
      );
    private $savedquery = Array (
      "all_contacts"
    );
    
    public $set_unshare = false;// V 0.3 will be depricated in V 0.4
    public $unshare_co_worker = "";// V 0.3 will be depricated in V 0.4
    public $contact_count = 0;
    public $search_keyword = "";
    public $filter = "";
    private $search_tags = Array();
    public $sql_view_name = "";

    public $sql_view_order = "last_activity DESC";
    public $sql_view_limit = 50;
	public $sql_qry_start = 0;
	public $sql_qry_end = 50;
    private $last_message_send = false;
	



    function __construct(sqlConnect $conx=NULL, $table_name="",$iduser = '') {
      parent::__construct($conx, $table_name);
      if (RADRIA_LOG_RUN_OFUZ) {
        $this->setLogRun(OFUZ_LOG_RUN_CONTACT);
      }

      if(empty($iduser)){
        $iduser = $_SESSION['do_User']->iduser;
      }

      if (isset($_SESSION['contact_view_name'])) {
        $this->sql_view_name = $_SESSION['contact_view_name'];
      }else{
        if (isset($_SESSION['do_User'])) {
          $this->sql_view_name = "userid".$iduser."_contact";
        }
      }
        $this->setLog("\n Contact Object instantiated");
      } 
	
    /**
     * getSqlViewName()
     * This is the name of the user's contact view
     * @return string name of the sql view
     */

    function getSqlViewName() {
        return $this->sql_view_name;
    }
	
    function setSqlViewName($iduser = ""){
        if($iduser == ""){ 
              $iduser = $_SESSION['do_User']->iduser; 
        }
        $this->sql_view_name = "userid".$iduser."_contact";
    }

	function isTmpContactNeedRefresh() {
		return true;
		if (strlen($this->getSqlViewName()) > 0) {
			if ($_SESSION['refresh_contacts'] === true) {
				return true;
			} else { return false; }
		} else { return true; }
	}
	
    /**
     * Set the company id for the contact
     * and create a new one if doesn't exists.
     * @param object EventControler
     */
     
    function eventSetCompany(EventControler $event_controler) {
		
        $fields = $event_controler->getParam('fields');
        $newCompany = new Company($this->getDbCon());
        $newCompany->query("select * from company where name='".trim($fields['company'])."' and iduser = ".$_SESSION['do_User']->iduser);
        if (!$newCompany->hasData()) {
        $newCompany->name = trim($fields['company']);
        $newCompany->iduser = $_SESSION['do_User']->iduser;
        $newCompany->add();
        $newCompanyid = $newCompany->getInsertId($this->getTable(), $this->getPrimaryKey());
        } else {
        $newCompanyid = $newCompany->idcompany;
        }
        $fields['idcompany'] = $newCompanyid;
        //print_r($fields);exit;
        $this->setLog("\n Company id:".$fields['idcompany']." for ".$fields['company']);
        $event_controler->updateParam("fields", $fields);
        return true;
    }

    function addPhone($number, $type){
        $new_phone = new ContactPhone();
    	$new_phone->idcontact = $this->idcontact;
    	$new_phone->phone_number = $number;
    	$new_phone->phone_type = $type;
    	$new_phone->add();
    }

    function addAddress($address, $type, $city=null, $state=null, $street=null, $zip=null, $country=null){
        $new_address = new ContactAddress();
    	$new_address->idcontact = $this->idcontact;
    	$new_address->city = $city;
    	$new_address->state = $state;
    	$new_address->street = $street;
    	$new_address->zipcode = $zip;
    	$new_address->country = $country;
    	$new_address->address = $address;
    	$new_address->address_type = $type;
    	$new_address->add();
    }

    function addEmail($email, $type){
        $new_email = new ContactEmail();
    	$new_email->idcontact = $this->idcontact;
    	$new_email->email_address = $email;
    	$new_email->email_type = $type; 
    	$new_email->add();
    }

    function addWebsite($website, $type){
        $new_website = new ContactWebsite();
        $new_website->idcontact = $this->idcontact;
        $new_website->website = $website;
        $new_website->website_type = $type; 
        $new_website->add();
    }

    function addIM($im, $type, $username){
        $new_im = new ContactInstantMessage();
        $new_im->idcontact = $this->idcontact;
        $new_im->im_options = $im;
        $new_im->im_type = $type; 
        $new_im->im_username = $username;
        $new_im->add();
    }

    /**
     * getContactDetails()
     * Get the contacts for a define contact id.
     * Right now same as getId() but in the future we will link it
     * to the auth_contact to check if the users is authorize to view 
     * that contact.
     * @param int contact primary  key value.
     */

    function getContactDetails($idcontact) {
        //$this->query("select * from contact where idcontact = " . $idcontact);
        $this->getId($idcontact);
    }

    /**
     * getContactIdByPortal
     * Return the idcontact from the portal_code generated for that contact.
     * @param integer portal_code
     * @return idcontact (contact primary key)
     */

    function getContactIdByPortal($portal_code){        
        //$q = new sqlQuery($this->getDbCon());
        $this->query("select * from contact where portal_code ='".$this->quote($portal_code)."'");
        if($this->next()){
            $idcontact = $this->idcontact;
            return $idcontact;
        }else{
            return false;
        }
    }

    /** 
     * getcontactbyUsername
     * Load the contact in the dataobject using the Username of that contact
     * @param string username
     */

    function getContactbyUsername($username) {
        $do_user = new User();
        $do_user->query("select idcontact from ".$do_user->getTable()." where  username='".$this->quote($username)."'");
        if(isset($do_user->idcontact) && !empty($do_user->idcontact)){
          $this->getId($do_user->idcontact);
        }else{ return false ; }
    }


    /**
      * getContactName
      * Return the full name of the contact.
      * 
      * @param idcontact
      * @return full name
      */
    function getContactName($idcontact=0){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select firstname,lastname from contact where idcontact =".$idcontact);
        $q->fetch();
        $fname = $q->getData("firstname");
        $lname = $q->getData("lastname");
        return $fname.' '.$lname;
    }

    /**
      * getContactPicture
      * Return the picture of associated with the contact.
      * I've seen a lot of display code everywhere but assume that this
      * method is only use for display so moved the facebook display check here.
      * 
      * @param idcontact
      * @return url of the picture
      */
    function getContactPicture($idcontact = ""){   
        
        if($idcontact != "") { 
            $q = new sqlQuery($this->getDbCon());
            //$idcontact = $this->idcontact; 
            $q->query("select picture from contact where idcontact = ".$idcontact);
            $q->fetch();
            $picture = $q->getData("picture");
        } else {
            $picture = $this->picture;
        }
        if($picture == '' || empty($picture)){ 
            $image_url = "/images/empty_avatar.gif";
        } else {
            if(preg_match("/http/",$picture,$matches)){
                $image_url = $picture;
            }else{
                $image_url = "/dbimage/".$picture;
            }
        }

        return $image_url;
    }

    function updateAddress($address, $type, $idcontact_address, $city=null, $state=null, $street=null, $zip=null, $country=null){
        $ca_address = new ContactAddress();
        $ca_address->getId($idcontact_address); // Primarykey id of the record to update
        $new_address->city = $city;
        $new_address->state = $state;
        $new_address->street = $street;
        $new_address->zipcode = $zip;
        $new_address->country = $country;
        $ca_address->address = $address;
        $ca_address->address_type = $type;
        $ca_address->update();
    }

    function updatePhone($number, $type, $idcontact_phone){
        $cp_phone = new ContactPhone();
        $cp_phone->getId($idcontact_phone); // Primarykey id of the record to update
        $cp_phone->phone_number = $number;
        $cp_phone->phone_type = $type;
        $cp_phone->update();
    }

    function updateWebsite($website, $type, $idcontact_website){
        $cw_website = new ContactWebsite();
        $cw_website->getId($idcontact_website); // Primarykey id of the record to update
        $cw_website->website = $website;
        $cw_website->website_type = $type;
        $cw_website->update();
    }

    function updateIM($im, $type, $username, $idcontact_instant_message){
        $cim_im = new ContactInstantMessage();
        $cim_im->getId($idcontact_instant_message); // Primarykey id of the record to update
        $cim_im->im_options = $im;
        $cim_im->im_type = $type;
        $cim_im->im_username = $username;
        $cim_im->update();
    }

    function updateEmail($email, $type, $idcontact_email){
        $ce_email = new ContactEmail();
        $ce_email->getId($idcontact_email); // Primarykey id of the record to update
        $ce_email->email_address = $email;
        $ce_email->email_type = $type;
        $ce_email->update();
    }

    function addNewContact($idusers,$firstname,$lastname,$idcompany,$company,$position){
      $this->idusers = $idusers;
      $this->firstname = $firstname;
      $this->lastname = $lastname;
      $this->idcompany = $idcompany;
      $this->company = $company;
      $this->position = $position;
      $this->add();
    }
    /**
     * Method to generate the link to contact detail page
     * when a task is realted to any contact
    */
    function getContactNameTaskRelated($idcontact = 0){
      if (!empty($idcontact)) {
        $q = new sqlQuery($this->getDbCon());
        $q->query("select firstname,lastname from contact where idcontact =".$idcontact);
         //echo "select firstname,lastname from contact where idcontact =".$idcontact;
        $q->fetch();
        $fname = $q->getData("firstname");
        $lname = $q->getData("lastname");
      } else {
         $this->getId($this->idcontact);
        $fname = $this->firstname;
        $lname = $this->lastname;
      }
      $fullname = $fname.' '.$lname;
      $output = '&nbsp;<span style="background-color: gray;">(Re: Note about <a style="color:orange;" href = "'.$this->getContactUrl().'">'.$fullname.'</a>)</span>';
      return $output;
    }

    /**
     * getContactNameContactRelatedTask()
     * This is called from the task->viewTask method to display a link
     * to the contact detail attached to a task.
     * Note: the parameter is now deprecate and will be doing nothing in future releases use getId($idcontact) instead;
     * @return string with html link to a contact.
     * @see getContactUrl()
     */

     function getContactNameContactRelatedTask($idcontact=0){
      if (!empty($idcontact)) {
        $q = new sqlQuery($this->getDbCon());
        $q->query("select firstname,lastname from contact where idcontact =".$idcontact);
        // echo "select firstname,lastname from contact where idcontact =".$idcontact." and from_note = 0";
        $q->fetch();
        $fname = $q->getData("firstname");
        $lname = $q->getData("lastname");
      } else {
        $this->getId($this->idcontact);
        $fname = $this->firstname;
        $lname = $this->lastname;
      }
      $fullname = $fname.' '.$lname;
      $output = '&nbsp;<span style="background-color: gray;">(<a style="color:orange;" href = "'.$this->getContactUrl($idcontact).'">'.$fullname.'</a>)</span>';
      return $output;
    }
    /**
     * getContactUrl()
     * Return an url that will link to the contact detail page.
     * if in mobile site display the mobile version
     * @return string url to the contact detail.
     */
    function getContactUrl($idcontact=0) {
      if (empty($idcontact)) { $idcontact = $this->idcontact; }
      if ($_SESSION['do_User']->is_mobile) {
         $contact_detail_page = "i_contact.php";
      } else {
         $contact_detail_page = "contact.php";
      }
      /*$e_detail = new Event("mydb.gotoPage");
      $e_detail->addParam("goto", $contact_detail_page);
      $e_detail->addParam("idcontact",$idcontact);
      $e_detail->addParam("tablename", "contact");
      $e_detail->requestSave("eDetail_contact", "contacts.php");
      return $e_detail->getUrl();*/
      return '/Contact/'.$idcontact;
    }

    function getCompanyRelatedContacts($idcompany){
          include_once("class/ContactNotes.class.php");
          $do_con_notes = new ContactNotes();
          $idowner = $do_con_notes->getCompanyOwner($idcompany);
          //if($idowner == $_SESSION['do_User']->iduser ){
              $this->query("select contact.idcontact as idcontact ,contact.firstname as firstname,
                            contact.lastname,contact_email.email_address as email_address, contact_phone.phone_number
                            as phone_number,contact_website.website as website
                            FROM contact 
                            LEFT JOIN contact_email ON contact.idcontact = contact_email.idcontact 
                            LEFT JOIN contact_phone ON contact.idcontact = contact_phone.idcontact
                            LEFT JOIN contact_website ON contact.idcontact = contact_website.idcontact
                            where 
                            contact. idcompany = ".$idcompany.
                            " GROUP BY contact.idcontact");
    }

    function isContactRelatedToUser($idcontact,$iduser=""){  
        if($iduser == ""){ $iduser = $_SESSION['do_User']->iduser; }
        if($this->isContactOwner($idcontact,$iduser)){
          $retval = true ;
        }else{ 
            $q1 = new sqlQuery($this->getDbCon());
            $q1->query("select * from contact_sharing where idcoworker =  ".$iduser." AND idcontact = ".$idcontact);
            if ($q1->getNumRows()){  
                $retval = true ;
            }else{
                $retval = false ;
            }
        }

        return $retval;
    }

    function isContactOwner($idcontact,$iduser=""){
         if($iduser == ""){ $iduser = $_SESSION['do_User']->iduser; }
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from contact where iduser =  ".$iduser." AND idcontact = ".$idcontact);
        if ($q->getNumRows()){ 
            return true;
        }else{
            return false;
        }
    }
    /**
     * getLastUpdate()
     * Method that returns the last time that Contact was updated.
     * @return string with a date (sql format)
     */

    function getLastUpdate() {
        return UpdateRecordLog::lastUpdate($this);
    }

    /** 
     * getCreateDate()
     * Return the date when this contact got created.
     * @return string with a date (sql format)
     */

    function getCreateDate() {
        return NewRecordLog::createDate($this);
    }

    /**
     * Overload the add() method to add created date in the NewRecordLog
     */

    function add() {
      parent::add();
      if ($this->getPrimaryKeyValue() > 0) {
        $rlog = new NewRecordLog();
        $rlog->setCreateDate($this->getTable(), $this->getPrimaryKeyValue());
        $this->setActivity("new");
      }
	  $_SESSION['refresh_contacts'] = true;
    }
    /**
     * Overload the update() method to add last update date in UpdateRecordLog
     */
    function update() {
      parent::update();
      if ($this->getPrimaryKeyValue() > 0) {
        $rlog = new UpdateRecordLog();
        $rlog->setLastUpdate($this->getTable(), $this->getPrimaryKeyValue());
        // moved to eventUpdateWebView();
        //$q = new sqlQuery($GLOBALS['conx']);
        //$q->query("UPDATE ".$this->getSqlViewName()." SET last_update=now() WHERE idcontact=".$this->getPrimaryKeyValue());
        //$q->free();
        $this->setActivity();
      }
      $_SESSION['refresh_contacts'] = true;
      
    }
    
    
    /**
      * Update the Contact view table with Phone and email.
      * eventUpdateContactView()
      */
    function eventUpdateWebView(EventControler $evtc) {
        $contact_view = new ContactView();
        $contact_view->updateFromContact($this);
    }
    
    /**
      * Update the Contact view table with Phone and email.
      * eventUpdateContactView()
      */
    function eventAddWebView(EventControler $evtc) {
        $contact_view = new ContactView();
        $contact_view->addFromContact($this);	
    }	
    
    /** 
      * Overload delete to delete all dependent and child data
      */
    
     function delete() {
         $do_contact_address = $this->getChildContactAddress();
         while ($do_contact_address->next()) {
             $do_contact_address->delete();
         }
         $do_contact_email = $this->getChildContactEmail();
         while ($do_contact_email->next()) {
             $do_contact_email->delete();
         }
         $do_contact_instant_message = $this->getChildContactInstantMessage();
         while ($do_contact_instant_message->next()) {
             $do_contact_instant_message->delete();            
         }
         $do_contact_phone = $this->getChildContactPhone();
         while ($do_contact_phone->next()) {
             $do_contact_phone->delete();
         }
         
	     $tags = $this->getTags();
         if(is_array($tags)){ // added this array check as if there is no tag then it was showing some warning
            $do_tag = new Tag();
            foreach($tags as $idtags) {
              $do_tag->delTagById($idtags);
            } 
            $do_tag->free();
         }
         $do_contact_notes = $this->getChildContactNotes();
         while ($do_contact_notes->next()) {
             $do_contact_notes->delete();
         }
         $do_contact_activity = $this->getChildActivity();
         $do_contact_activity->delete();

         $do_contact_rel = $this->getChildContactSharing();
         while ($do_contact_rel->next()) {
             $do_contact_rel->delete();
         }
		 $do_contact_website = $this->getChildContactWebsite();
		 while ($do_contact_website->next()) {
			$do_contact_website->delete();
		 }
         // Need to check if the company will not be orphane once this contact is deleted.
         // if yes delete the company as well.
         // FIXME

         $q_del = new sqlQuery($this->getDbCon());
		 // Delete the record in the web view, this may need to be moved to a seperate event.
		 $q_del->query("DELETE FROM ".$this->getSqlViewName()." WHERE idcontact=".$this->idcontact);
         
         //parent::delete();
         // Need to check if the current user is authorized to delete the contact
         // Can't use this here as we have a multiple record to delete in the current result set.

         $q_del->query("DELETE FROM contact WHERE iduser=".$_SESSION['do_User']->iduser." AND idcontact=".$this->idcontact);

        $q_del->free();  
        $_SESSION['refresh_contacts'] = true; 
     }
    

    /**
     * Instance an activity object and updated the time stamp or 
     * if the flag="new" insert a new entry (only for new record)
     */
    function setActivity($flag="") {
        if (!empty($this->idcontact)) {
            $do_activity  = new Activity();
            $do_activity->idcontact = $this->idcontact;
            if ($flag == "new") {
                $do_activity->add();
            } else {
                $do_activity->update();
            }
            $q = new sqlQuery($GLOBALS['conx']);
            $q->query("UPDATE ".$this->getSqlViewName()." SET last_activity=now() WHERE idcontact=".$this->idcontact);
                    
            $q->free();
        }
    }

    function setBreadcrumb() {
        $do_breadcrumb = new Breadcrumb();
        $do_breadcrumb->type = "Contact";
        if (is_object($_SESSION['do_User'])) {
          $do_breadcrumb->iduser = $_SESSION['do_User']->iduser;
        }
        $do_breadcrumb->id = $this->idcontact;
        $do_breadcrumb->add();
    }

    /** 
     * clearSearch
     * This method remove all search keywords, filters or tags
     * 
     */
    function clearSearch() {
      $this->filter = "";
      $this->search_tags = Array();
      $this->search_keyword = "";
      $this->set_unshare = false; // V 0.3
      $this->unshare_co_worker = "";
    }

    /** 
     * eventClearSearch
     * This method remove all search keywords, filters or tags
     * 
     */
    function eventClearSearch(EventControler $evtcl) {
      $this->clearSearch();
      $this->query("SELECT * FROM ".$this->getSqlViewName()." ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit);
    }

    /**
     * eventSetSearch
     * Set the keywords in object and create an SQL query
     * with the keywords. Set the sqlquery for use in the contacts.php page.
     * @param EventControler event controler object to get params and set DisplayNext.
     */

    function eventSetSearch(EventControler $event_controler) {
        if(is_object($_SESSION['do_Contacts'])) {
            $_SESSION['do_Contacts']->sql_qry_start = 0;
        }
        $this->clearSearch();
        $this->search_keyword = $event_controler->contacts_search;
        //$this->search_tags = Array();
        // The contact refresh will happen on the background.
        //if ($this->isTmpContactNeedRefresh() === true) {
        //    $this->createViewUserContact();
        //}
        $this->setSqlQuery("SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany,contact.position as position,contact.picture as picture,contact.email_address as email_address, contact.phone_number, contact.tags
        FROM ".$this->getSqlViewName()." as contact
        WHERE  
          ( contact.firstname LIKE '%".$this->search_keyword."%'
             OR contact.lastname LIKE '%".$this->search_keyword."%'
             OR contact.company LIKE '%".$this->search_keyword."%')
        ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit);
	
        $event_controler->setDisplayNext(new Display($event_controler->goto));
    }

    function getContact_Company_ForInvoice($idcontact,$idcompany = ""){ 

        $q = new sqlQuery($this->getDbCon()); 
        if(empty($idcompany) || $idcompany == ""){
              
              $q->query("Select contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname  
              from ". $this->getSqlViewName()." as contact
              where contact.idcontact = ".$idcontact);
        }else{
            $q->query("Select contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,company.idcompany as idcompany ,company.name as company 
            from ". $this->getSqlViewName()." as contact,company
            where contact.idcontact = ".$idcontact. " AND company.idcompany = ".$idcompany);
          
        }
        $html = '';
        while($q->fetch()){
           $html .= $q->getData("firstname").' '.$q->getData("lastname");
           if($idcompany!= "" || !empty($idcompany)){
              if($q->getData("company") != ''){
                  $html .='('.$q->getData("company").')';
              }
            }
	    break;
        }
        return $html;
    }
    
    /**
      Generating the text suggestion for the contact.
      Takes the string and searches for the match in the database for
      contact for that user. 
      --- Looks in the view and set the query
    */
    function getContactSuggestionSearch($str) {

        $this->setSqlQuery("SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,contact.company as company
        FROM ".$this->getSqlViewName()." as contact 
        WHERE   
           ( contact.firstname LIKE '".$str."%'
             OR contact.lastname LIKE '".$str."%'
             OR contact.company LIKE '".$str."%')
        GROUP BY contact.idcontact order by contact.firstname");
    }
    
    /**
      Event method for the Ajax text suggestion for contact.
      Takes the text as an ajax request and the query is set
      in the method getContactSuggestionSearch($str)
      If result is found then returns the HTML data with the 
      matching contacts
    */

    function eventAjaxGetSuggestion(EventControler $evtcl){
        $text = $evtcl->text;
        if(strlen($text) > 0 ){
            $this->getContactSuggestionSearch($text);
            $html = '';
            $this->query($this->getSqlQuery());
            if($this->getNumRows()){
            	$html = '';
                while($this->next()){
                    $comp = (strlen($this->company)>3) ? ' ('.$this->company.')' : '';
                    $html .= '<option value="'.$this->idcontact.'" class="suggestion_area_option">'.$this->firstname.' '.$this->lastname.$comp.'</option>';
                }
                $evtcl->addOutputValue($html);
            }else{$evtcl->addOutputValue('No');}
        }else{$evtcl->addOutputValue('No');}
    }

     /**
        Event method to get address for the contact.
        Used in the invoice address for a selected contact.
        Returns the HTML data with the address detail.
     */
     function eventAjaxGetInvoiceAddress(EventControler $evtcl){
        $idcontact = $evtcl->idcontact;
        $html = '';
        if($idcontact){
            $html = $this->ajaxGetInvoiceAddress($idcontact);
        }
         $evtcl->addOutputValue($html);
    }
    
    function ajaxGetInvoiceAddress($idcontact){
        $html = '';
        $q_comp = new sqlQuery($this->getDbCon());
            $qry_comp = "Select contact.company as company,contact.idcontact as idcontact,
                         contact.idcompany as idcompany,contact.firstname as firstname,
                         contact.lastname as lastname,contact_address.address from ".$this->getSqlViewName()."
                         as contact 
                         LEFT JOIN contact_address on contact.idcontact = contact_address.idcontact
                         where contact.idcontact = ".$idcontact.";
                        " ;
            $q_comp->query($qry_comp);
            $contact_address = false ;
            while($q_comp->fetch()){
                    $fname = $q_comp->getData("firstname");
                    $lname = $q_comp->getData("lastname");
                    $idcompany = $q_comp->getData("idcompany");
                    $company = $q_comp->getData("company");
                    if($company !=''){
                        $html .= $company.' '."\r\n";
                    }
                    $html .= $fname.' '.$lname.' '."\r\n";
                 if( $q_comp->getData("address") != '' ){
                    $html .= $q_comp->getData("address").' '."\r\n";
                    $contact_address = true;
                 }
            }
            if($idcompany && $contact_address === false){
                $q_comp_add = new sqlQuery($this->getDbCon());
                $comp_add = "select * from company_address where idcompany = ".$idcompany." Limit 1";
                $q_comp_add->query($comp_add);
                if($q_comp_add->getNumRows()){
                    while($q_comp_add->fetch()){  
                      $html .=  $q_comp_add->getData("address").' '."\r\n";
                      $html .=  $q_comp_add->getData("zipcode").' '."\r\n";
                      $html .=  $q_comp_add->getData("street").' '."\r\n";
                      $html .=  $q_comp_add->getData("city").' '."\r\n";
                    }
                }else{
                    $q_con_add = new sqlQuery($this->getDbCon());
                    $con_add = "select * from contact_address where idcontact = ".$idcontact." Limit 1";
                    $q_con_add->query($con_add);
                    if($q_con_add->getNumRows()){
                        while($q_con_add->fetch()){  
                            $html .=  $q_con_add->getData("address").' '."\r\n";
                            $html .=  $q_con_add->getData("zipcode").' '."\r\n";
                            $html .=  $q_con_add->getData("street").' '."\r\n";
                            $html .=  $q_con_add->getData("city").' '."\r\n";
                        }
                    }   
                }
            }
          return $html;            
    }
      
    /**
      * Function to get the Contact info and emaiid for the invoice
      * Method is used while calling the authnet API with User Info
      */
     function getContactInfo_For_Invoice($idcontact){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select firstname,lastname,company from contact where idcontact = ".$idcontact);
        while($q->fetch()){
          $fname = $q->getData("firstname");
          $lname = $q->getData("lastname");
          $company = $q->getData("company");
        }
        $q_email = new sqlQuery($this->getDbCon());
        $q_email->query("select email_address from contact_email where idcontact = ".$idcontact);
        while($q_email->fetch()){
            $email = $q_email->getData("email_address");  
        }
        $arr_cont_info = array();
        $arr_cont_info['firstname'] = $fname;
        $arr_cont_info['lastname'] = $lname;
        $arr_cont_info['company'] = $company;
        $arr_cont_info['email'] = $email;
        $arr_user_info['description'] = 'Test Description';
        return $arr_cont_info;
     }

    /**
     * eventSearchByTag()
     * Set the contacts query to filter contacts by selected tags.
     * tag size: in this version the size is only for the first level all sub tag search
     * are ignored and will not affect the tag size.
     */

     function eventSearchByTag(EventControler $event_controler) {

        $clear_search = 0;
        if (strlen($event_controler->search_add_tag_name)>0) {
            if (!in_array($event_controler->search_tag_name, $this->search_tags)) {
                $this->search_tags[] = $event_controler->search_add_tag_name;
            }
        } else { $clear_search++;  }
        if (strlen($event_controler->search_tag_name)>0) {
            $this->search_tags = Array( $event_controler->search_tag_name );
			$tagClick = new Tag();
			$tagClick->getTagByName($this->search_tags[0]);
			$tagClick->addTagClick();
			$tagClick->calculateTagSize();
			$tagClick->free();
        } else { $clear_search++; }
        if (strlen($event_controler->search_remove_tag_name)>0) {
            $cur_tags = $this->getSearchTags();
            $this->search_tags = Array();
            foreach ($cur_tags as $cur_tag) {
                if ($cur_tag != $event_controler->search_remove_tag_name) {
                    $this->search_tags[] = $cur_tag;
                }
            }            
        } else { $clear_search++; }
        if ($clear_search == 3) { $this->clearSearch(); }
        if (!empty($this->search_tags)) {
          $sql_search = "SELECT contact.idcontact as idcontact, contact.firstname as firstname,contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany, contact.position as position, contact.picture as picture, contact.email_address as email_address, contact.phone_number, contact.tags as tags 
              FROM ".$this->getSqlViewName()." as contact 
              WHERE ";
            $first = true;
            foreach ($this->search_tags as $search_tag) {
                if ($first) { $first = false ; } else { $sql_search .= " AND "; }
                $sql_search .= " tags like '%".$search_tag."%'";
            }
            $sql_search .= " ORDER BY ".$this->sql_view_order;
            $this->setLog("\n tag search with SQLQuery:".$sql_search);
        } else {
             $sql_search = "SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,contact.company as company,contact.position as position, contact.picture as picture,contact.email_address as email_address, contact.phone_number, contact.tags as tags
                  FROM ".$this->getSqlViewName()." as contact ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_view_limit;
        }
        $_SESSION['last_tag_refresh'] = 0;
        $this->setSqlQuery($sql_search);
        $event_controler->setDisplayNext(new Display($event_controler->goto));
     }
     
    /**
     * eventSetFilter
     * Set a query to display contact based on selected filters.
     */

    function eventSetFilter(EventControler $event_controler) {
        if(is_object($_SESSION['do_Contacts'])) {
            $_SESSION['do_Contacts']->sql_qry_start = 0;
        }

       $this->clearSearch();
       $filter = $event_controler->filter;

       $sql_search = "SELECT contact.idcontact as idcontact, contact.firstname as firstname, contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany, contact.position as position, contact.email_address as email_address, contact.phone_number,contact.picture as picture, contact.tags as tags FROM ".$this->getSqlViewName()." AS contact";

       $sql_search_count = "SELECT contact.idcontact as idcontact, contact.firstname as firstname, contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany, contact.position as position, contact.email_address as email_address, contact.phone_number,contact.picture as picture, contact.tags as tags FROM ".$this->getSqlViewName()." AS contact";

       if (!empty($filter)) {
        switch ($filter) {
            case "add":
                $sql_search_count .= " ORDER BY first_created DESC";
                $sql_search .= " ORDER BY first_created DESC LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
            break;
            case "modify":
                $sql_search_count .= " ORDER BY last_update DESC";
                $sql_search .= " ORDER BY last_update DESC LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
            break;
            case "view";
                $sql_search_count .= ", `breadcrumb` AS b WHERE b.`type`='".$this->getTable()."' and b.`id`=contact.idcontact AND b.iduser=".$_SESSION['do_User']->iduser."
                            GROUP BY contact.idcontact ORDER BY b.`when`";
                $sql_search .= ", `breadcrumb` AS b WHERE b.`type`='".$this->getTable()."' and b.`id`=contact.idcontact AND b.iduser=".$_SESSION['do_User']->iduser."
                            GROUP BY contact.idcontact ORDER BY b.`when` DESC LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit; 
            break;
            case "active";
                $sql_search_count .= " ORDER BY last_activity DESC";
                $sql_search .= " ORDER BY last_activity DESC LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
            break;
            case "alpha";
                $sql_search_count .= " ORDER BY lastname, firstname";
                $sql_search .= " ORDER BY lastname, firstname LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
            break;
            default:
                $sql_search_count .= " ORDER BY ".$this->sql_view_order;
                $sql_search .= " ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
        }

        $this->filter = $filter;
		//This is to set the total count of the contacts with the filter.
		$this->query($sql_search_count);
		$this->contact_count = $this->getNumRows();

        $this->setSqlQuery($sql_search);
		
       } else {
             $sql_search .= " ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
             $this->setSqlQuery($sql_search);
       }

    }
  
  
  
    function getContactCount(){
        if(!$this->contact_count){
         // if ($this->isTmpContactNeedRefresh() === true) {
         //       $this->createViewUserContact();
         // }
          $q = new sqlQuery($this->getDbCon());
          $q->query("select * from ".$this->getSqlViewName());
		  $this->contact_count = $q->getNumRows();	
          return $q->getNumRows();
        }else{ return $this->contact_count; }
        
    }
    /**
      * Event to seacrh the contacts with respect to as Co-Worker
    */
    function eventFilterContactAsCoWorker(EventControler $evtcl) {
        $this->clearSearch();
        $idcontacts = $evtcl->ids;
        $setShare = $evtcl->setShare;
        if($idcontacts !="" || !empty($idcontacts)){
            if($setShare == "No"){
                $this->set_unshare = true;
            }else{ $this->set_unshare = false; }
            $this->unshare_co_worker = $evtcl->coworker;
        }
        
        $this->setSqlQuery("SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany,contact.position as position,contact.picture as picture,contact.email_address as email_address, contact.phone_number
        FROM ".$this->getSqlViewName()." as contact
        WHERE contact.idcontact IN (".$idcontacts.") 
        GROUP BY contact.idcontact
        ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_view_limit);
        $evtcl->setDisplayNext(new Display($evtcl->goto));
    }

     function eventUnShareMultiple(EventControler $evtcl) {
        $idcoworker = $evtcl->co_worker_id;
        $contacts = $evtcl->getParam("ck");
        if (is_array($contacts)) {
            $do_unshare = new ContactSharing();
            foreach ($contacts as $idcontact) {
                $do_unshare->unshareContact($idcontact,$idcoworker);
            }
        }
        $idcontacts = $do_unshare->getSharedContacts($idcoworker);
        if(is_array($idcontacts)){
            $idcontacts = implode(",",$idcontacts);
        }else{$this->set_unshare = false;}
        $this->setSqlQuery("SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany,contact.position as position,contact.picture as picture,contact.email_address as email_address, contact.phone_number
        FROM ".$this->getSqlViewName()." as contact
        WHERE contact.idcontact IN (".$idcontacts.") 
        GROUP BY contact.idcontact
        ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_view_limit);
     }

    /**
     * eventDeleteMultiple
     * this method delete contacts using the "ck" array from the contact list.
     * Currently also adding the userid in the where but this will need to 
     * be changed as multiple users may have the right to deleted contacts.
     * @param EventControler $event_controler Object with the event data
     */
    
    function eventDeleteMultiple(EventControler $event_controler) {
	$error_message = "";
        if (strlen($event_controler->tags)==0) {
            $this->setLog("\n Not tags deleting contacts");
            $contacts = $event_controler->getParam("ck");
            if (is_array($contacts)) {
                $error_delete = false;                
                $do_deleting_contact = new Contact();
                $logged_in_useridcontact = $_SESSION['do_User']->idcontact;
				$count_contact = 0;
                foreach ($contacts as $idcontact) {
					$count_contact++;
					//Delete only 50 contacts at a time.
					if($count_contact <= 50) {
						$this->setLog("\n deleting contact:".$idcontact);                        
						$do_deleting_contact->getId($idcontact);
						if($this->isContactOwner($idcontact)){ // if owner then only delete
							if($idcontact != $logged_in_useridcontact){
								$do_deleting_contact->delete();
							}else{
								$error_message = 'You can not delete your own contact.<br />';
								$error_delete = true;
							}
						}else{
						$error_message = "The following Contacts can not be deleted as these are shared by some of your Co-Workers.<br />";
						$error_message .= '<b><i>'.$this->getContactFullName($idcontact).'</i></b><br />';
						$error_delete = true;
						}
					}
                }
                $do_deleting_contact->free();
                // When deleting contact by uisng search by tag and if all the contacts are deleted then we must set the query as empty
                $this->query($this->getSqlQuery());
                if($this->getNumRows() == 0 ){ 
                    $this->clearSearch();
                    $this->setSqlQuery("") ; 
                }
            }
        }
        if($error_delete){
            //error.php can be used for some other erros as well.
            $event_controler->goto = 'error.php';
            $error_message .= '<br /><a href="/contacts.php">Back</a><br />';
            $_SESSION['errorMessage'] = $error_message;
        }
    }
    
    /**
      Method to delete tags from multiple contacts
      Reset the query to NULL if no more tag found by the same, otherwise searching by tag and 
      then deleting will result error due to the query in the session
    */

    function eventDeleteMultipleContactsTag(EventControler $evtcl){
        $tagname = $evtcl->delTagMul;
        $contacts = $evtcl->getParam("ck");
        if($tagname != ''){
            $do_tag = new Tag();
            $contact_view = new ContactView();
            foreach($contacts as $idcontact){
                $contact_view->getId($idcontact);
                $contact_view->deleteTag($tagname);
                $idtag = $do_tag->isTagExistsForReferer($tagname,$idcontact);
                $do_tag->delTagById($idtag);
            }
            $q = new sqlQuery($this->getDbCon());
            $q->query("select * from tag where tag_name = '".trim($tagname)."' AND iduser = ".$_SESSION['do_User']->iduser);
            if(!$q->getNumRows()){
                $this->setSqlQuery("") ;
            }
                $contact_view->free();
            // When deleting contact by uisng search by tag and if all the contacts are deleted then we must set the query as empty
            $this->query($this->getSqlQuery());
            if($this->getNumRows() == 0 ){ 
                $this->clearSearch();
                $this->setSqlQuery("") ; 
            }
        }
        $_SESSION['tag_refresh_now'] = true;
    }

    /**
     * eventAddTagMultiple
     * Triggered form the contacts.php page when assigning multiple tags 
     * to a single contact.
     */
     
    function eventAddTagMultiple(EventControler $event_controler) {
        $this->eventSetFilter($event_controler);
        $tags = explode(",", $event_controler->tags);
        $contacts = $event_controler->getParam("ck");
        if (is_array($tags) && is_array($contacts)) {
            $do_tag = new Tag();
            $contact_view = new ContactView();
            foreach ($contacts as $idcontact) {
                $contact_view->getId($idcontact);
                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    $do_tag->addTagAssociationShared($idcontact,$tag,'contact',$_SESSION['iduser']->iduser);
                    $do_tag->addNew();
                    $do_tag->addTagAssociation($idcontact, $tag, "contact");
                    $contact_view->addTag($tag);
                }
            }
        }
        $_SESSION['tag_refresh_now'] = true;
    }


    /**
     * eventGetForMailMerge
     * Get the list of contact for mailmerge
     * load in the current contact object the list of
     * selected contacts from the contacts.php
     * Then redirect to the contact_sendemail.php.
     */
    function eventGetForMailMerge(EventControler $event_controler) {

        $idcontacts = $event_controler->getParam("ck");
        $sql_in = "(";
        foreach($idcontacts as $idcontact) {
            $sql_in .= $idcontact.",";
        }
        $sql_in = substr($sql_in, 0, -1);
        $sql_in .= ")";
        $tag_search = $this->getSearchTags();
        if(is_array($tag_search) && count($tag_search > 0)){
              $_SESSION['searched_tags'] = $tag_search ;
        }

        $this->clearSearch();
        $this->setSqlQuery("SELECT * FROM contact WHERE idcontact in ".$sql_in);
        $event_controler->goto = "contact_sendemail.php";
        $event_controler->setDisplayNext(new Display("contact_sendemail.php"));
    }

     /** 
      *  eventSendMessage
      *  This will send a message to one or multiple users.
	  *  @todo Problem with the getBodyText(true) it returns some bad encoding and no newline, could also be the problem in the template it self...
      */

     function eventSendMessage(EventControler $event_controler) {      
        $this->setLog("\n eventSendMessage (".date("Y/m/d H:i:s").")");
        if ($event_controler->send == _('Send Mailing')) {
            $email_template = $_SESSION['do_message'];
            $email_template->setApplyFieldFormating(false);
            $this->setLog("\n EmailTemplate id: ".$email_template->getPrimarykeyValue());
            $this->setLog("\n SQL:".$this->getSqlQuery());
            $this->query();
            $this->setLog("\n ".$this->getNumRows()." contacts to send the message");

            while ($this->next()) {	
                set_time_limit(500);
                if($event_controler->unsubtag == 'on'){ // set unsubscribe auto-responder list 
                    $values_contact = $this->getValues();
                    $tag_values = array("flag"=>"unsubscribe_autoresponder","idtag"=>$event_controler->idtag);
                    $values = array_merge($values_contact,$tag_values);
                    $message = $this->sendMessage($email_template, $values);echo "++++++++++++++++++_";exit;
                }else{
                    $message = $this->sendMessage($email_template, $this->getValues());
                }
                if ($this->last_message_sent) {	
                    //$do_contact_notes->iduser = $_SESSION['do_User']->iduser;
                    $do_contact_notes = new ContactNotes();
                    $do_contact_notes->iduser = $_SESSION['do_User']->iduser;
                    //$do_contact_notes->note = '<b>'.$message->getSubject().'</b><br/>'.$message->getBodyText(true);
                    //->getTemplateBodyHtml();
                    $note_text = preg_replace('/^(.*?)<hr>.*$/','$1',str_replace("\n",'',$message->getBodyHtml(true)));
                    $do_contact_notes->note = '<b>'.$message->getSubject().'</b><br/>'.$note_text;
                    $do_contact_notes->date_added = date("Y-m-d");	 
                    $do_contact_notes->idcontact = $this->getPrimaryKeyValue();
                    $do_contact_notes->add();


                    /*
                     * Recording the messages sent by User
                     */
                    $msg_con = new sqlQuery($this->getDbCon());
                    $sql_msg_check = "SELECT * FROM `message_usage` WHERE `iduser` = ".$_SESSION['do_User']->iduser." AND `date_sent` = '".date("Y-m-d")."'";                    
                    $msg_con->query($sql_msg_check);
                    if($msg_con->getNumRows()) {
                      $msg_con->fetch();
                      $sql_con_update = new sqlQuery($this->getDbCon());
                      $sql_msg_update = "UPDATE `message_usage` SET `num_msg_sent` = num_msg_sent+1 WHERE `idmessage_usage` = ".$msg_con->getData("idmessage_usage");
                      $sql_con_update->query($sql_msg_update);
                    } else {
                      $sql_con_ins = new sqlQuery($this->getDbCon());
                      $sql_msg_ins = "INSERT INTO `message_usage`(iduser,date_sent,num_msg_sent) VALUES(".$_SESSION['do_User']->iduser.",'".date("Y-m-d")."',1)";
                      $sql_con_ins->query($sql_msg_ins);
                    }
                    
                    
                    
                }		
            }
        } else { $event_controler->goto = "contacts.php"; }
        $this->clearSearch();
        $this->free();
     }
  
     /** 
      *   eventSendNote
      *   This event is triggered when adding a note in a contact 
	  *   It will send a copy of the note the contact.
      */

     function eventSendNote(EventControler $event_controler) {
        $send_note = $event_controler->fields['send_email'];
        $this->setLog("eventSendNote starting (".date("Y/m/d H:i:s").")");
        $this->setLog("do we send a message:".$send_note);
        if ($send_note == "y") {
          $template = new EmailTemplate();
          $template->setSubject("{Ofuz} Message: ".substr($event_controler->fields['note'], 0, 70)."...")
            ->setMessage($event_controler->fields['note']);
          $this->sendMessage($template);
        }
     }  
  
     /**
      *  sendMessage
      *  This abstract the message sending so we use a general function
      *  that will send email or facebook or twitter based on the user
      *  preferences and settings.
      *  its possible to generate an EmailTemplate on the fly with no records in the DB
      *  Here is an exemple:
      *  <code php>
      *  $do_template = new EmailTemplate();
      *  $do_template->senderemail = "philippe@sqlfusion.com";
      *  $do_template->sendername = "Philippe Lewicki";
      *  $do_template->subject = "This is an example";
      *  $do_template->bodytext = "This is the content of the sample message";
      *  $do_template->bodyhtml = nl2br($do_template->bodytext);
      *  </code>
      * 
      *  An other example more OO / stylish
      *  <code php>
      *  $do_template = new EmailTemplate();
      *  $do_template->setFrom("phil@sqlfusion.com", "Philippe Lewicki")
      *              ->setSubject("This is an example")
      *              ->setMessage("This is the content of the sample message");
      *  </code>
      *  setFrom() is optional, if not provided it takes the do_User data
      *  
      *  Then send the message with:  $contact->sendMessage($do_template);
      * 
      *  If you used a saved EmailTemplate like
      *  $do_template = new EmailTemplate("my template in email template table");
      *  and want the sender to be the currently signed in user, make sure the senderemail field
      *  is empty.
      * 
      *  @param $message an EmailTemplate object.
      *  @param $values an array with values to merge, optional.
      *  
      */
     function sendMessage($template, $values=Array()) {   
        if (!is_object($template)) { return false; }
                if (empty($values)) { $values = $this->getValues(); }
                $this->last_message_sent = false;
        $do_contact_email = $this->getChildContactEmail();
        $email_to = $do_contact_email->getDefaultEmail();
        $this->setLog("\n Sending message to:".$email_to);   
        $contact_link = '<a href="/Contact/'.$this->idcontact.'">'.$this->firstname.' '.$this->lastname.'</a>';   

        $do_ofuzBeanstalkd = new OfuzBeanstalkd();
        $do_ofuzBeanstalkd ->addToQueue($mail_data,'jobqueue_send_mail',$_SESSION['do_User']->iduser);
          echo 'Job Queue requested';
          exit;

       if (strlen($email_to) > 4) { 
          if($this->email_optout !='y'){
        
 /*    
   if (strlen($email_to) > 4) { 
            if ($this->email_optout != 'y') {
                $emailer = new Ofuz_Emailer('UTF-8');
                if (strlen($template->senderemail) == 0) {
                    $template->setFrom($_SESSION['do_User']->email,  $_SESSION['do_User']->getFullName());				
                }
                $emailer->setEmailTemplate($template);
                $emailer->mergeArrayWithFooter($values);
                $emailer->addTo($email_to);
                $this->last_message_sent = true;
                echo "<pre>";
                print_r($emailer);
                echo "</pre>";
                exit;
                return $emailer->send();*/

            } else {
                $_SESSION['in_page_message'] .= _("<br>".$contact_link." has opt-out and will not receive this email");
            }
        } elseif (strlen($this->tw_user_id) > 0) {
            // send direct message using twitter api.
            try{
                $do_twitter = new OfuzTwitter();
                $tw_config = $do_twitter->getTwitterConfig();
                $serialized_token = $do_twitter->getAccessToken();
                $token = unserialize($serialized_token);
                $ofuz_twitter = new Ofuz_Service_Twitter($token->getParam('user_id'), $tw_config, $token);
                $followers = $ofuz_twitter->userFollowers(false);
                if (is_object($followers) && count($followers->user) > 0) {
                    foreach ($followers->user as $follower) {
                        if ($follower->id == $this->tw_user_id) {
                            $merge = new MergeString();
                            $message = $merge->withArray($template->bodytext, $values);
                            $ofuz_twitter->directMessageNew($this->tw_user_id, $message);
                            $do_contact_notes = new ContactNotes();
                            $do_contact_notes->iduser = $_SESSION['do_User']->iduser;
                            $do_contact_notes->note = $message;
                            $do_contact_notes->date_added = date('Y-m-d');	 
                            $do_contact_notes->idcontact = $this->idcontact;
                            $do_contact_notes->add();
                            return true;
                        }
                    }
                }
                $_SESSION['in_page_message'] .= _("<br>Notification can not be sent to ".$contact_link);
            }catch(Exception $e){
                $_SESSION['in_page_message'] .= _("<br>Notification can not be sent to ".$contact_link);
            }
        } elseif ($this->fb_userid && $_SESSION["do_User"]->global_fb_connected) {
            // send message using facebook api.
            include_once 'facebook_client/facebook.php';
            include_once 'class/OfuzFacebook.class.php';
            $facebook = new Facebook(FACEBOOK_API_KEY, FACEBOOK_APP_SECRET);
            try{
                $msg = _(' has sent the following message using ofuz').'<br />';
                $msg .= '<b>'.$template->subject.'</b><br/>'.$template->bodyhtml;
                $facebook->api_client->notifications_send($this->fb_userid, $msg, 'user_to_user');
                //$this->last_message_sent = true;
            }catch(Exception $e){
                $_SESSION['in_page_message'] .= _("<br>Notification can not be sent to ".$contact_link);
            }
        } else {
            $_SESSION['in_page_message'] .= _("<br>".$contact_link." doesn't have a valid email address, facebook account, or twitter id.");
        }
        
        if ($this->last_message_sent) {
            return true;
        } else { 
            return false; 
        }		 
    }

  
    function eventdelContactTagById(EventControler $event_controler){
      $do_tag =new Tag();
      $idtag = $event_controler->getParam("id");
      $do_tag->delTagById($idtag);
    }

    /**
     *  getFilter()
     *  return the filter currently set.
     */
    function getFilter($selected="") {
        if ($selected == $this->filter) {
            return " selected";
        } else {
            return $this->filter;
        }
    }

    /**
     * getTags()
     * return an array with the list of tags associated with this user.
     * return tags primarykey
     */
    function getTags() {
        $q_tags = new sqlQuery($this->getDbCon());
        $q_tags->query("SELECT idtag FROM tag WHERE idreference=".$this->idcontact." AND  iduser=".$_SESSION['do_User']->iduser." AND reference_type='".$this->getTableName()."' ORDER BY tag_name");
       // $q_tags->query("SELECT idtag FROM tag WHERE idreference=".$this->idcontact." AND reference_type='".$this->getTableName()."' ORDER BY tag_name");
        while ($q_tags->fetch()) {
           $a_tag[] = $q_tags->getData("idtag");
        }
        return $a_tag;
    }
   
    /**
     * getSearchTags
     * Return an array with all the tags currently norrowing the search
     */
    function getSearchTags() {
        return $this->search_tags;
    }

    function formatTextDisplay($text){
      /**
        $text = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $text); //phone number replace
        $text = preg_replace("/([0-9]{5})([0-9]{4})?/", "$1-$2", $text); //zip code replace
     */
      $ret = ereg_replace("[a-zA-Z]+://([.]?[a-zA-Z0-9_/-])*", "<a href=\"\\0\" target = \"_blank\">\\0</a>", $text);
      $ret = ereg_replace("(^| )(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a href=\"http://\\2\" target = \"_blank\">\\2</a>", $ret);
      $ret = preg_replace("/([\w\.]+)(@)([\S\.]+)\b/i","<a href=\"mailto:$1@$3\">$1@$3</a>",$ret);
      return ($ret) ;
    }

    function formatTextDisplayWithStyle($text){
      /**
        $text = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $text); //phone number replace
        $text = preg_replace("/([0-9]{5})([0-9]{4})?/", "$1-$2", $text); //zip code replace
     */
      $ret = ereg_replace("[a-zA-Z]+://([.]?[a-zA-Z0-9_/-])*", "<a style =\"color:orange;\" href=\"\\0\" target = \"_blank\">\\0</a>", $text);
      $ret = ereg_replace("(^| )(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a style =\"color:orange;\" href=\"http://\\2\" target = \"_blank\">\\2</a>", $ret);
      $ret = preg_replace("/([\w\.]+)(@)([\S\.]+)\b/i","<a style =\"color:orange;\" href=\"mailto:$1@$3\">$1@$3</a>",$ret);
      return ($ret) ;
    }

    function getContactFullName($idcontact=0){
       if (empty($idcontact)) { $idcontact = $this->idcontact; }
       $q = new sqlQuery($this->getDbCon());
       $q->query("select firstname,lastname from contact where idcontact = ".$idcontact) ;
       //echo "select firstname.lastname from contact where idcontact = ".$idcontact;
       while($q->fetch()){
          $fname = $q->getData("firstname");
          $lname = $q->getData("lastname");
       }
       return $fname.' '.$lname;
    }

    function eventMergeContacts(EventControler $evtcl){
        $contact_ids = $evtcl->getParam("contact_ids");
        $idcontact = $evtcl->getParam("cont_id");
        $idcontact_phone = $evtcl->getParam("cont_phone");
        $idcontact_email = $evtcl->getParam("cont_email");
        $contact_instant_message = $evtcl->getParam("cont_im");
        $idcontact_website = $evtcl->getParam("cont_website");
        $idcontact_address = $evtcl->getParam("cont_addr");
        $idcontact_rss_feed = $evtcl->getParam("cont_rss");
        $del_cont_ids = array();
       /* if(count($idcontact)){
            foreach($contact_ids as $mergeid){
                if($mergeid != $idcontact){
                    $del_cont_ids[] = $mergeid             
                }
            }
            if(is_array($idcontact_phone)){
                
            }
            
        }else{
        }*/
    }
    
    function mergeContactRelated($cont_array){
        $q = new sqlQuery($this->getDbCon());
        $q_master = new sqlQuery($this->getDbCon());
        $q_rel = new sqlQuery($this->getDbCon());
        $id_to_keep = $cont_array[0];//take the first record from the contact array to keep
        $merged_fname = '';
        $merged_lname = '';
        $merged_comp = '';
        $merged_pos = '';
        $q_master->query("select firstname,lastname,company,position from contact where idcontact = ".$id_to_keep);
        $q_master->fetch();
        $master_firstname = ucwords($q_master->getData("firstname"));
        $master_lastname = ucwords($q_master->getData("lastname"));
        $master_comp = $q_master->getData("company");
        $master_pos = $q_master->getData("position");

        $merged_fname = $master_firstname;
        $merged_lname = $master_lastname;
        $merged_comp = $master_comp;
        $merged_pos = $master_pos;
        foreach($cont_array as  $contact){
            if($contact != $id_to_keep){
                $q_rel->query("select firstname,lastname,company,position from contact where idcontact = ".$contact);
                $q_rel->fetch();
                if($master_firstname != $q_rel->getData("firstname")){
                  $merged_fname = $merged_fname.' '.$q_rel->getData("firstname");
                }
                if($master_lastname != $q_rel->getData("lastname")){
                  $merged_lname = $merged_lname.' '.$q_rel->getData("lastname");
                }
                if($master_comp != $q_rel->getData("company")){
                  $merged_comp = $merged_comp.' '.$q_rel->getData("company");
                }
                if($master_pos != $q_rel->getData("position")){
                  $merged_pos = $merged_pos.' '.$q_rel->getData("position");
                }
            }
        }
        $q->query("update contact set firstname = '".$merged_fname."',lastname = '".$merged_lname."',
                  company ='".$merged_comp."',position='".$merged_pos."' where idcontact = ".$id_to_keep);
    }

    /**
      * @param $cont_array -- Array with idcontact   
    */
    function mergeContactTags($cont_array){
        $q = new sqlQuery($this->getDbCon());
        $id_to_keep = $cont_array[0];//take the first record from the contact array to keep
        $do_tag = new Tag();
        $tag_names = $do_tag->getAllTagNamesForReferer($id_to_keep);//get the tag names for the contact to keep
        if($tag_names && is_array($tag_names)){
            foreach($tag_names as $tag_name){ //run through the tag names
                foreach($cont_array as $contact){ // run through the contact
                    if($contact != $id_to_keep){
                        // if the same tag present for contact to be deleted delete that tag first
                        $idtag = $do_tag->isTagExistsForReferer($tag_name,$contact);
                        if($idtag){
                           $q->query("delete from tag where idtag = ".$idtag." Limit 1");
                        }
                    }
                }
            }
        }

        // Start merging the tags to the contact to keep
        foreach($cont_array as $contact){
          if($contact != $id_to_keep){
            $q->query("update tag set idreference =".$id_to_keep." where idreference=".$contact);
          }
        }
    }
    
    /**
      *  The below function is to merge the contacts and related information
      *  Need some better algorithm for that so as to avoid the repeated code
      *  FIXME
      * @param $cont_array -- Array with idcontact   
    */
    function MergeContactsAutomated($cont_array){
        if(is_array($cont_array)){
            $this->mergeContactTags($cont_array);
            $this->mergeContactRelated($cont_array);
            $id_to_keep = $cont_array[0];//take the first record from the contact array to keep
            $do_deleting_contact = new Contact();
            $do_check_duplicate = new Contact();
            $do_cont_sharing = new ContactSharing();
            $facebook_id_updated = false;
            $facebook_image_updated = false;
            $twitter_id_updated = false;
            foreach($cont_array as $contact_id_del){
                if($contact_id_del != $id_to_keep){

                     // facebook Related keep the image if facebook image is available from the merged
                     $q = new sqlQuery($this->getDbCon());
                     $q_master = new sqlQuery($this->getDbCon());
                     $q_rel = new sqlQuery($this->getDbCon());
                     if(!$facebook_image_updated){
                        $q_master->query("select picture from contact where idcontact = ".$id_to_keep);
                        $q_master->fetch();
                        $picture = $q_master->getData("picture");
                         if(!preg_match("/facebook.com/",$picture,$matches)){
                            $q_rel->query("select picture from contact where idcontact = ".$contact_id_del);
                            $q_rel->fetch();
                            $r_picture =$q_rel->getData("picture");
                              
                             if(preg_match("/facebook.com/",$r_picture,$matches)){
                                $q->query("update contact set picture = '".$r_picture."' where idcontact = ".$id_to_keep);
                                $facebook_image_updated = true;
                            }
                          }else{$facebook_image_updated = true;}
                        } 
                     // Facebook Related keep track of facebook id of contact
                     $q = new sqlQuery($this->getDbCon());
                     $q_master = new sqlQuery($this->getDbCon());
                     $q_rel = new sqlQuery($this->getDbCon());
                     if(!$facebook_id_updated){
                         $q_master->query("select fb_userid from contact where idcontact = ".$id_to_keep);
                         $q_master->fetch();
                         $fb_uid = $q_master->getData("fb_userid");
                         if($fb_uid == 0 || empty($fb_uid) || $fb_uid == ''){
                             $q_rel->query("select fb_userid from contact where idcontact = ".$contact_id_del);
                             $q_rel->fetch();
                             $fb_userid =$q_rel->getData("fb_userid");

                             if($fb_userid != 0 && !empty($fb_userid) && $fb_userid != ''){
                                 $q->query("update contact set fb_userid = ".$fb_userid." where idcontact = ".$id_to_keep);
                                 $facebook_id_updated = true;
                             }
                         }else{$facebook_id_updated = true;}
                     }
                     // Twitter Related :: keep track of twitter id of the contact
                     $q = new sqlQuery($this->getDbCon());
                     $q_master = new sqlQuery($this->getDbCon());
                     $q_rel = new sqlQuery($this->getDbCon());
                     if(!$twitter_id_updated){
                         $q_master->query("select tw_user_id from contact where idcontact = ".$id_to_keep);
                         $q_master->fetch();
                         $tw_uid = $q_master->getData('tw_user_id');
                         if(empty($tw_uid) || $tw_uid == ''){
                             $q_rel->query("select tw_user_id from contact where idcontact = ".$contact_id_del);
                             $q_rel->fetch();
                             $tw_user_id = $q_rel->getData('tw_user_id');

                             if(!empty($tw_user_id) && $tw_user_id != ''){
                                 $q->query("update contact set tw_user_id = ".$tw_user_id." where idcontact = ".$id_to_keep);
                                 $twitter_id_updated = true;
                             }
                         }else{$twitter_id_updated = true;}
                     }
                    //update the ContactPhone with $id_to_keep
                      $q = new sqlQuery($this->getDbCon());
                      $q_master = new sqlQuery($this->getDbCon());
                      $q_rel = new sqlQuery($this->getDbCon());
                      $q_master->query("select * from contact_phone where idcontact = ".$id_to_keep);
                      if($q_master->getNumRows()> 0 ){
                          $update_ids_array = array();
                          
                          $q_rel->query("select * from contact_phone where idcontact = ".$contact_id_del);
                          if($q_rel->getNumRows()){
                              while($q_rel->fetch()){
                                   $found = true;
                                  while($q_master->fetch()){
                                      if( ($q_rel->getData("phone_number")== $q_master->getData("phone_number")) && ($q_rel->getData("phone_type") == $q_master->getData("phone_type")) ){
                                          $found = true;
                                          break;
                                      }else{
                                          $found = false;
                                      }
                                  }
                                  if(!$found){
                                      $q->query("update contact_phone set idcontact = ".$id_to_keep. " where idcontact_phone = ".$q_rel->getData("idcontact_phone"));
                                  }
                              }
                          }
                      }else{
                            $q->query("update contact_phone set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                      }
                      
                    //Update the ContactEmail with $id_to_keep
                      $q = new sqlQuery($this->getDbCon());
                      $q_master = new sqlQuery($this->getDbCon());
                      $q_rel = new sqlQuery($this->getDbCon());
                      $q_master->query("select * from contact_email where idcontact = ".$id_to_keep);
                      if($q_master->getNumRows() > 0 ){
                          $update_ids_array = array();
                          $q_rel->query("select * from contact_email where idcontact = ".$contact_id_del);
                          if($q_rel->getNumRows()){
                              $count = 0;
                              while($q_rel->fetch()){
                                  $found = true;
                                  while($q_master->fetch()){
                                      $count++;
                                      if( ($q_rel->getData("email_address") == $q_master->getData("email_address")) && ($q_rel->getData("email_type") == $q_master->getData("email_type")) ){
                                          $found = true;
                                          break;
                                      }else{
                                          $found = false;
                                      }
                                  }
                                  if(!$found){
                                      $q->query("update contact_email set idcontact = ".$id_to_keep. " where idcontact_email = ".$q_rel->getData("idcontact_email"));
                                  }
                              }
                          }
                      }else{
                            $q->query("update contact_email set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                      }
                    //Update the ContactInstantMessage with $id_to_keep
                      $q = new sqlQuery($this->getDbCon());
                      $q_master = new sqlQuery($this->getDbCon());
                      $q_rel = new sqlQuery($this->getDbCon());
                      $q_master->query("select * from contact_instant_message where idcontact = ".$id_to_keep);
                      if($q_master->getNumRows()> 0 ){
                          $update_ids_array = array();
                          
                          $q_rel->query("select * from contact_instant_message where idcontact = ".$contact_id_del);
                          if($q_rel->getNumRows()){
                              while($q_rel->fetch()){
                                  $found = true;
                                  while($q_master->fetch()){
                                      if( ($q_rel->getData("im_options") == $q_master->getData("im_options")) && ($q_rel->getData("im_type") == $q_master->getData("im_type")) && ($q_rel->getData("im_username") == $q_master->getData("im_username"))){
                                           $found = true;
                                           break;
                                      }else{
                                          $found = false;
                                      }
                                  }
                                  if(!$found){
                                      $q->query("update contact_instant_message set idcontact = ".$id_to_keep. " where idcontact_instant_message = ".$q_rel->getData("idcontact_instant_message"));
                                  }
                              }
                          }
                      }else{
                            $q->query("update contact_instant_message set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                      }

                    //Update the ContactWebsite with $id_to_keep
                      $q = new sqlQuery($this->getDbCon());
                      $q_master = new sqlQuery($this->getDbCon());
                      $q_rel = new sqlQuery($this->getDbCon());
                      $q_master->query("select * from contact_website where idcontact = ".$id_to_keep);
                      if($q_master->getNumRows()> 0 ){
                          $update_ids_array = array();
                          
                          $q_rel->query("select * from contact_website where idcontact = ".$contact_id_del);
                          if($q_rel->getNumRows()){
                              while($q_rel->fetch()){
                                  $found = true;
                                  while($q_master->fetch()){
                                      if( ($q_rel->getData("website") == $q_master->getData("website")) && ($q_rel->getData("website_type") == $q_master->getData("website_type")) ){
                                          $found = true;
                                          break;
                                      }else{
                                          $found = false;
                                      }
                                  }
                                  if(!$found){
                                      $q->query("update contact_website set idcontact = ".$id_to_keep. " where idcontact_website = ".$q_rel->getData("idcontact_website"));
                                  }
                              }
                          }
                      }else{
                            $q->query("update contact_website set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                      }

                    //Update the ContactAddress with $id_to_keep
                      $q = new sqlQuery($this->getDbCon());
                      $q_master = new sqlQuery($this->getDbCon());
                      $q_rel = new sqlQuery($this->getDbCon());
                      $q_master->query("select * from contact_address where idcontact = ".$id_to_keep);
                      if($q_master->getNumRows()> 0 ){
                          $update_ids_array = array();
                          
                          $q_rel->query("select * from contact_address where idcontact = ".$contact_id_del);
                          if($q_rel->getNumRows()){
                              while($q_rel->fetch()){
                                  $found = true;
                                  while($q_master->fetch()){
                                      if( ($q_rel->getData("address") == $q_master->getData("address")) && ($q_rel->getData("address_type") == $q_master->getData("address_type")) && ($q_rel->getData("city") == $q_master->getData("city")) && ($q_rel->getData("country") == $q_master->getData("country")) && ($q_rel->getData("state") == $q_master->getData("state")) && ($q_rel->getData("street") == $q_master->getData("street")) && ($q_rel->getData("zipcode") == $q_master->getData("zipcode")) ){
                                          $found = true;
                                          break;
                                      }else{
                                          $found = false;
                                      }
                                  }
                                  if(!$found){
                                      $q->query("update contact_address set idcontact = ".$id_to_keep. " where idcontact_address = ".$q_rel->getData("idcontact_address"));
                                  }
                              }
                          }
                      }else{
                            $q->query("update contact_address set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                      }

                    //Update the ContactRssFeed with $id_to_keep
                      $q = new sqlQuery($this->getDbCon());
                      $q_master = new sqlQuery($this->getDbCon());
                      $q_rel = new sqlQuery($this->getDbCon());
                      $q_master->query("select * from contact_rss_feed where idcontact = ".$id_to_keep);
                      if($q_master->getNumRows()> 0 ){
                          $update_ids_array = array();
                          
                          $q_rel->query("select * from contact_rss_feed where idcontact = ".$contact_id_del);
                          if($q_rel->getNumRows()){
                              while($q_rel->fetch()){
                                  $found = true;
                                  while($q_master->fetch()){
                                      if( ($q_rel->getData("feed_type") == $q_master->getData("feed_type")) && ($q_rel->getData("rss_feed_url") == $q_master->getData("rss_feed_url")) && ($q_rel->getData("username") == $q_master->getData("username")) ){
                                           $found = true;
                                           break;
                                      }else{
                                          $found = false;
                                      }
                                  }
                                  if(!$found){
                                      $q->query("update contact_rss_feed set idcontact = ".$id_to_keep. " where idcontact_rss_feed = ".$q_rel->getData("idcontact_rss_feed"));
                                  }
                              }
                          }
                      }else{
                            $q->query("update contact_rss_feed set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                      }
                      $q = new sqlQuery($this->getDbCon());
                    //Update the ContactNotes with $id_to_keep
                      $q->query("update contact_note set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                    //Update the Contacttasks with $id_to_keep
                      $q->query("update task set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                    //update the invoice with #id_to_keep and idcompany
                      $idcompany = $this->getIdCompanyForContact($id_to_keep);
                      $inv_addr = $this->ajaxGetInvoiceAddress($id_to_keep);
                      $q->query("update invoice set idcontact = ".$id_to_keep. ",idcompany = {$idcompany},invoice_address='{$inv_addr}' where idcontact = ".$contact_id_del);
                    //Update the google_contact_info with $id_to_keep
                      //$q->query("update google_contact_info set idcontact = ".$id_to_keep. " where idcontact = ".$contact_id_del);
                     // Update the shared contact
                     $do_cont_sharing->resetContactSharingOnMerging($id_to_keep,$contact_id_del);
                     // delete contact
                     $do_deleting_contact->getId($contact_id_del);
                     $do_deleting_contact->delete();
                }
            }// For each ends here
            $do_deleting_contact->free();//exit;
            //$rlog = new UpdateRecordLog();
            //$rlog->setLastUpdate("contact", $id_to_keep);
            //$this->setActivity();
            return $id_to_keep;
        }else{
            return false;
        } 
    }


    function getIdUser($idcontact){
        $this->getId($idcontact);
        return $this->iduser;
    }

    function getDbFieldNames(){
        /*
            contact,contact_address,contact_email,contact_phone,contact_website
        */
        $fields_arr = array('First Name'=>'firstname', 'Last Name'=>'lastname','Position'=>'position',
                            'Company'=>'company', 'Summary'=>'summary', 'Birthday'=>'birthday', 
                            'Address Home'=>'address_hm', 'Address Other'=>'address_ot', 'Address Work'=>'address_wk',
                            'City'=>'city', 'State'=>'state', 'Street'=>'street', 'Zipcode'=>'zipcode',
                            'Country'=>'country', 'Email Home'=>'email_hm', 'Email Work'=>'email_wk', 
                            'Email Other'=>'email_ot', 'Phone Home'=>'phone_number_hm', 'Phone Work'=>'phone_number_wk',
                            'Phone Other'=>'phone_number_ot', 'Phone Mobile'=>'phone_number_mb', 'Phone Fax'=>'phone_number_fx', 'Website Company'=>'website_comp', 'Website Blog'=>'website_blog','Website Facebook'=>'website_fb', 'Website LinkedIn'=> 'website_linkedin','Website Other'=>'website_ot', 'Website Personal'=>'website_personal', 'Website Twitter'=>'website_twitter', 'AIM Work'=>'im_aim_wk', 'AIM Personal'=>'im_aim_per', 'AIM Other'=>'im_aim_ot', 'MSN Work'=>'im_msn_wk', 'MSN Personal'=>'im_msn_per', 'MSN Other'=>'im_msn_ot', 'ICQ Work'=>'im_icq_wk', 'ICQ Personal'=>'im_icq_per', 'ICQ Other'=>'im_icq_ot', 'Jabber Work'=>'im_jabber_wk', 'Jabber Personal'=>'im_jabber_per', 'Jabber Other'=>'im_jabber_ot', 'Yahoo Work'=>'im_yahoo_wk', 'Yahoo Personal'=>'im_yahoo_per', 'Yahoo Other'=>'im_yahoo_ot', 'Skype Work'=>'im_skype_wk', 'Skype Personal'=>'im_skype_per', 'Skype Other'=>'im_skype_ot', 'Google Talk Work'=>'im_gt_wk', 'Google Talk Personal'=>'im_gt_per', 'Google Talk Other'=>'im_gt_ot', 'Note'=>'note'
                           );

        return $fields_arr;
    }

    /**
     * Function to import the facebook contact to ofuz.
     * Since Facebook does provide limited information on friends so just the basic
     * information will be imported and also keep track of the Facebook user id so that 
     * for the first time it will just add and on next import it will update.
     * @param array $fb_friend_id 
     * @param integer $iduser
    */

    function importFacebookFriends($friends_data,$iduser=''){
        $do_tag = new Tag();
        $do_company = new Company();
        $do_cont_view =  new ContactView();
        if($iduser == '' ){
            $iduser = $_SESSION['do_User']->iduser ;
        }else{
            $do_cont_view->setUser($iduser);
        }
        $tag_list_names = '';
        $frnd_fb_uid = $friends_data["fb_uid"];
        $idcontact = $this->isFbFriendInContact($frnd_fb_uid);
        $fname = $friends_data["name"]["first_name"];
        $lname = $friends_data["name"]["last_name"];
        $work =  $friends_data["work"];
        $work_detail = $work[0];
        $work_history = @$work_detail["work_history"];
        $profile_url = @$friends_data["profile_url"][0]["profile_url"];
        $profile_pic = @$friends_data["pic_with_logo"][0]["pic_with_logo"];
        if(is_array($work_history)){
          $company =  $work_history[0]["company_name"];
          $position =  $work_history[0]["position"];
          $desc = $work_history[0]["description"];
        }
        $list_name = $friends_data['listname'];
        $this->firstname = $fname;
        $this->lastname = $lname;
        $this->fb_userid = $frnd_fb_uid;
        $this->iduser = $iduser;
        if($idcontact){
          //update the data
           $this->checkFbProfileUrlOnUpdate($idcontact,$profile_url);
           $this->updateFbProfilePic($idcontact,$profile_pic);
           $do_tag->addTagAssociation($idcontact,"Facebook","contact",$iduser);
           if(is_array($list_name) && count($list_name) > 0 ){
              $tag_list_names = implode(",",$list_name);
              foreach($list_name as $list_name){
                  $do_tag->addTagAssociation($idcontact,$list_name,"contact",$iduser);
              }
           }
           $this->getId($idcontact);
           if($company != '' && !empty($company)){
              if($position !=''){$this->position =$position; }else{$this->position ='';}
              $q = new sqlQuery($this->getDbCon());
              $q->query("select idcompany from company where name = '".trim($company)."' AND iduser = ".$iduser );
              if($q->getNumRows()){
                $q->fetch();
                $idcompany = $q->getData("idcompany");
                $this->idcompany = $idcompany;
                $this->company = trim($company);
                $this->update();
                $idcontact = $this->getPrimaryKeyValue();
              }else{
                $do_company->name = trim($company);
                $do_company->iduser = $iduser;
                $do_company->add();
                $idcompany = $do_company->getPrimaryKeyValue();
                $this->idcompany = $idcompany;
                $this->company = trim($company);
                $this->update();
              }
           }else{
                //$iduser = $_SESSION['do_User']->iduser;
                $q = new sqlQuery($this->getDbCon());
                $q_upd = "UPDATE contact set firstname = '".$fname."',lastname = '".$lname."',fb_userid = ".$frnd_fb_uid.",position = '".$position."' where idcontact = ".$idcontact;
                $q->query($q_upd);
           }
           $this->getId($idcontact);
           $do_cont_view->updateFromContact($this);// Added the method call updateFromContact() so that the child data is updated 
           $do_cont_view->addTag('Facebook',$this->idcontact);// Update the contact view for tags.
           $do_cont_view->addTag($tag_list_names,$this->idcontact);// Update the contact view for tags.
        }else{
           // new entry
           $do_company = new Company();
           $do_cont_website = new ContactWebsite();
           if($company != '' && !empty($company)){
              if($position !=''){$this->position =$position; }else{$this->position ='';}
              $q = new sqlQuery($this->getDbCon());
              $q->query("select idcompany from company where name = '".trim($company)."' AND iduser = ".$iduser );
              if($q->getNumRows()){
                $q->fetch();
                $idcompany = $q->getData("idcompany");
                $this->idcompany = $idcompany;
                $this->company = trim($company);
                $this->add();
                $idcontact = $this->getPrimaryKeyValue();
                $do_cont_website->idcontact = $idcontact;
                $do_cont_website->website = $profile_url;
                $do_cont_website->website_type = 'Facebook';
                $do_cont_website->add();
                $this->updateFbProfilePic($idcontact,$profile_pic);
                $do_tag->addTagAssociation($idcontact,"Facebook","contact",$iduser);
                
                if(is_array($list_name) && count($list_name) > 0 ){
                  $tag_list_names = implode(",",$list_name);
                  foreach($list_name as $list_name){
                      $do_tag->addTagAssociation($idcontact,$list_name,"contact",$iduser);
                  }
                }
              }else{
                $do_company->name = trim($company);
                $do_company->iduser = $iduser;
                $do_company->add();
                $idcompany = $do_company->getPrimaryKeyValue();
                $this->idcompany = $idcompany;
                $this->company = trim($company);
                $this->add();
                $idcontact = $this->getPrimaryKeyValue();
                $do_cont_website->idcontact = $idcontact;
                $do_cont_website->website = $profile_url;
                $do_cont_website->website_type = 'Facebook';
                $do_cont_website->add();
                $this->updateFbProfilePic($idcontact,$profile_pic);
                $do_tag->addTagAssociation($idcontact,"Facebook","contact",$iduser);
                if(is_array($list_name) && count($list_name) > 0 ){
                  $tag_list_names = implode(",",$list_name);
                  foreach($list_name as $list_name){
                      $do_tag->addTagAssociation($idcontact,$list_name,"contact",$iduser);
                  }
                }
              }
              
           }else{
                //$iduser = $_SESSION['do_User']->iduser;
                $q = new sqlQuery($this->getDbCon());
                $q_ins = "INSERT into contact (firstname,lastname,fb_userid,iduser,position) values(
                          '$fname','$lname',$frnd_fb_uid,$iduser,'$position')";
                $q->query($q_ins);
                $idcontact = $q->getInsertId();
                $q_website = new sqlQuery($this->getDbCon());
                $q_website->query("INSERT into contact_website (idcontact,website,website_type) VALUES
                                   ($idcontact,'$profile_url','Facebook')");
                $this->updateFbProfilePic($idcontact,$profile_pic);
                $do_tag->addTagAssociation($idcontact,"Facebook","contact",$iduser);
                if(is_array($list_name) && count($list_name) > 0 ){
                  $tag_list_names = implode(",",$list_name);
                  foreach($list_name as $list_name){
                      $do_tag->addTagAssociation($idcontact,$list_name,"contact",$iduser);
                  }
                }
           }
           $this->getId($idcontact);
           $do_cont_view->addFromContact($this);
           $do_cont_view->updateFromContact($this);// Added the method call updateFromContact() so that the child data is updated just after insert
           $do_cont_view->addTag('Facebook',$this->idcontact);// Update the contact view for tags.
           $do_cont_view->addTag($tag_list_names,$this->idcontact);// Update the contact view for tags.
           
        }
        $do_tag->free();

    }

    

    function isFbFriendInContact($fb_friend_id){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select idcontact from contact where fb_userid = ".$fb_friend_id." AND iduser = ".$_SESSION['do_User']->iduser);
        if($q->getNumRows()){
            $q->fetch();
            $idcontact = $q->getData("idcontact");
            return $idcontact;
        }else{
            return false;
        }
    }

    function checkFbProfileUrlOnUpdate($idcontact,$profile_url){
        $do_contact_website = new ContactWebsite();
        $q = new sqlQuery($this->getDbCon());
        $q->query("select idcontact_website from contact_website where idcontact = ".$idcontact." AND website_type = 'Facebook' AND website = '".$profile_url."'");
        if($q->getNumRows() == 0 ){
          $do_contact_website->idcontact = $idcontact;
          $do_contact_website->website = $profile_url;
          $do_contact_website->website_type = 'Facebook';
          $do_contact_website->add();
        }else{
            $q->fetch();
            $idcontact_website = $q->getData("idcontact_website");
            $do_contact_website->getId($idcontact_website);
            $do_contact_website->website = $profile_url;
            $do_contact_website->update();
        }
    }
    
    function updateFbProfilePic($idcontact,$url){
      $q = new sqlQuery($this->getDbCon());
      $q->query("update contact set picture = '".$url."' where idcontact = ".$idcontact);
    }

    /**
      API usage method for checking duplicate Contact
	  * checkDuplicateContact is more explicite, renaming and deprecating duplicateContact.
	  * Requires a Contact object with firstname and lastname set.
      @param iduser
    */
	 function duplicateContact($iduser,$email_work,$email_home,$email_other){ 
		 $this->checkDuplicateContact($iduser,$email_work,$email_home,$email_other);
	 }     
	     
     function checkDuplicateContact($iduser,$email_work,$email_home='',$email_other=''){ 
        if($this->firstname!= '' && $this->lastname != ''){
            /**
			 * if($email_work != '' || $email_home != '' || $email_other != ''){
                $email_clause = '';
                if($email_work != ''){ 
                    $email_work_found = true;
                    $email_clause = " AND( (contact_email.email_address = '".$email_work."' AND email_type='Work') " ; 
                }else{ $email_work_found = false ; }
                if($email_home != ''){
                    $email_home_found = true;  
                    if($email_work_found){
                        $email_clause .= " OR (contact_email.email_address = '".$email_home."' AND email_type='Home') " ; 
                    }else{
                        $email_clause = " AND( (contact_email.email_address ='".$email_home."' AND email_type='Home') " ; 
                    }
                }else{ $email_home_found = false; }
                if($email_other != ''){
                    if($email_work_found || $email_work_found){
                        $email_clause .= " OR (contact_email.email_address = '".$email_other."' AND email_type='Other') " ; 
                    }else{
                        $email_clause .= " AND( (contact_email.email_address = '".$email_other."' AND email_type='Other') " ; 
                    }
                }
                if($email_clause != ''){$email_clause .= " )";}
                $q = new sqlQuery($this->getDbCon());
                $qry = "SELECT contact.firstname,contact.lastname,contact.idcontact from contact inner join contact_email ON contact.idcontact = contact_email.idcontact
                        where 
                        contact.firstname ='".$this->firstname."' OR contact.lastname ='".$this->lastname."'
                        AND contact.iduser = ".$iduser.$email_clause ;
                 //echo 'query: '.$qry;exit;
                $q->query($qry);
                if($q->getNumRows()){
                    //return true;
                    $q->fetch();
                    return $q->getData("idcontact");
                }else{
                    return false;
                }
                //echo $qry;exit;
              }
				**/
				
			$q = new sqlQuery($this->getDbCon());
			$q->query("SELECT idcontact FROM contact, contact_email WHERE 
												contact.idcontact = contact_email.idcontact
											AND	contact.firstname ='".$this->firstname."' 
											AND	contact.lastname ='".$this->lastname."'
											AND contact.iduser = ".$iduser."
											AND contact_email.email_address='".$email_work."'");
			if ($q->getNumRows() > 0) { 
				$q->fetch();
				return $q->getData("idcontact");
				
			} elseif(!empty($email_home)) {
				$q->query("SELECT idcontact FROM contact, contact_email WHERE 
												contact.idcontact = contact_email.idcontact
											AND	contact.firstname ='".$this->firstname."' 
											AND	contact.lastname ='".$this->lastname."'
											AND contact.iduser = ".$iduser."
											AND contact_email.email_address='".$email_home."'");
			}
			if ($q->getNumRows() > 0) { 
				$q->fetch();
				return $q->getData("idcontact");
			} elseif(!empty($email_other)) {
				$q->query("SELECT idcontact FROM contact, contact_email WHERE 
												contact.idcontact = contact_email.idcontact
											AND	contact.firstname ='".$this->firstname."' 
											AND	contact.lastname ='".$this->lastname."'
											AND contact.iduser = ".$iduser."
											AND contact_email.email_address='".$email_other."'");
			}
			if ($q->getNumRows() > 0) {
				$q->fetch();
				return $q->getData("idcontact");
			} else {
				return false;
			}				
           		
        }//echo 'here';exit;
    }

    /**
      API usage method for contact search
      @param firstname,lastname,email
    */
     function apiSearchContact($iduser,$firstname = "",$lastname = "",$email=""){
        $where_clause = " Where contact.iduser = ".$iduser;
        $join_clause = '';
        $query = ''; 
        $query = "select contact.idcontact,
                  contact.firstname,
                  contact.lastname,
                  contact.iduser,
                  contact.company,
                  contact_email.email_address as email_address,
                  contact.position from ".$this->table ;
        if($firstname != ''){
            $where_clause .= " AND ( (contact.firstname like '%".$firstname."%') ";
            $fname_searched = true;
        }else {$fname_searched = false;}
        if($lastname !=''){
            $lname_searched  = true ;
            if($fname_searched){
	     	$where_clause .= " OR (contact.lastname like '%".$lastname."%') ";
            }else{
        	$where_clause .= " AND( (contact.lastname like '%".$lastname."%') ";
            }
        }else{ $lname_searched  = false ;}
        $join_clause = " left join contact_email ON contact.idcontact = contact_email.idcontact ";
        if($email != ''){ 
            if($fname_searched || $lname_searched){
                $where_clause .= " OR ( email_address like '%".$email."%') ";
            }else{
                $where_clause .= " AND( ( email_address like '%".$email."%') ";
            }
        }
        $where_clause .= " ) GROUP BY contact.idcontact ORDER BY contact.firstname";
        
        $query = $query.$join_clause.$where_clause;
	//echo $query;
        $q = new sqlQuery($this->getDbCon());
        $q->query($query);
        $out_put_array = array();
        $status = array();
        $status["msg"] = "Contact Found";
        $status["stat"] = "ok";
        $status["code"] = "601";
        $rowRes = array();
        $out_put_array[] = $status;
        $row_array = array();
        if($q->getNumRows()){
            while($q->fetch()){
                $rowRes["idcontact"]  = $q->getData("idcontact");
                $rowRes["firstname"]  = $q->getData("firstname");
                $rowRes["lastname"]  = $q->getData("lastname");
                $rowRes["company"]  = $q->getData("company");
                $rowRes["position"]  = $q->getData("position");
                $rowRes["email"]  = $q->getData("email_address");
                $row_array[] = $rowRes;
            }
            $out_put_array[] = $row_array;
            //print_r ($out_put_array);exit;
            return $out_put_array;
			
        }else{
            return false;
        }
        

     }

     function apiGetContactId($iduser,$firstname = "",$lastname = "",$email=""){
        $where_clause = " Where contact.iduser = ".$iduser;
        $join_clause = '';
        $query = ''; 
        $query = "select contact.idcontact,
                  contact.firstname,
                  contact.lastname,
                  contact.iduser,
                  contact.company,
                  contact_email.email_address as email_address,
                  contact.position from ".$this->table ;
        if($firstname != ''){
        $where_clause .= " AND ( (contact.firstname  = '".$firstname."') ";
                $fname_searched = true;
        }else {$fname_searched = false;}
        if($lastname !=''){
            $lname_searched  = true ;
            if($fname_searched){
	      		$where_clause .= " AND (contact.lastname = '".$lastname."') ";
            }else{
	      	$where_clause .= " AND( (contact.lastname = '".$lastname."') ";
            }
        }else{ $lname_searched  = false ;}
        $join_clause = " left join contact_email ON contact.idcontact = contact_email.idcontact ";
        if($email != ''){ 
            if($fname_searched || $lname_searched){
	      $where_clause .= " AND ( email_address = '".$email."') ";
            }else{
	       $where_clause .= " AND( ( email_address = '".$email."') ";
            }
        }
        $where_clause .= " ) GROUP BY contact.idcontact ORDER BY contact.firstname";
        
        $query = $query.$join_clause.$where_clause;
	//echo $query;
        $q = new sqlQuery($this->getDbCon());
        $q->query($query);
        $out_put_array = array();
		
        $status = array();
        $status["msg"] = "Contact Found";
        $status["stat"] = "ok";
        $status["code"] = "601";
        $rowRes = array();
        $out_put_array[] = $status;
        $row_array = array();
        if($q->getNumRows()){
            while($q->fetch()){
                $rowRes["idcontact"]  = $q->getData("idcontact");
                $rowRes["firstname"]  = $q->getData("firstname");
                $rowRes["lastname"]  = $q->getData("lastname");
                $rowRes["company"]  = $q->getData("company");
                $rowRes["position"]  = $q->getData("position");
                $rowRes["email"]  = $q->getData("email_address");
                $row_array[] = $rowRes;
            }
            $out_put_array[] = $row_array;
            //print_r ($out_put_array);exit;			
            return $out_put_array;
        }else{
            return false;
        }
		
     }

      //This function returns all the contacts for a particular User
      function getContactsForAPI($iduser) {

        $query = "SELECT c.*,ce.email_address,cp.phone_number,cw.website AS company_website
            FROM {$this->table} AS c
            LEFT JOIN contact_email AS ce ON ce.idcontact = c.idcontact
            LEFT JOIN contact_phone AS cp ON cp.idcontact = c.idcontact
            LEFT JOIN company_website AS cw ON cw.idcompany = c.idcompany
            WHERE
                        c.iduser = {$iduser}
            GROUP BY c.idcontact ORDER BY c.firstname
          ";
              $q = new sqlQuery($this->getDbCon());
              $q->query($query);
              $out_put_array = array();
              $status = array();
              $status["msg"] = "Contact Found";
              $status["stat"] = "ok";
              $status["code"] = "601";
              $out_put_array[] = $status;
              $row_array = array();
              $do_tags = new Tag();
              if($q->getNumRows()){
                  while($q->fetch()){
                        $default_email_id = $this->getDefaultEmailId($q->getData("idcontact"));
                        if($default_email_id) {
                          $email_id = $default_email_id;
                        } else {
                          $email_id = $q->getData("email_address");
                        }
                        $rowRes = array();
                        $rowRes["idcontact"]  = $q->getData("idcontact");
                        $rowRes["firstname"]  = $q->getData("firstname");
                        $rowRes["lastname"]  = $q->getData("lastname");
                        $rowRes["company"]  = $q->getData("company");
                        $rowRes["position"]  = $q->getData("position");
                        $rowRes["email_address"]  = $email_id;
                        $rowRes["phone_number"]  = $q->getData("phone_number");
                        $rowRes["company_website"]  = $q->getData("company_website");
                        $rowRes["contact_photo"] = $this->getContactPicture($q->getData("idcontact"));
                        $rowRes["idcompany"]  = $q->getData("idcompany");
                        $arr_tagname = array();
                        $arr_tagname = $do_tags->getAllTagNamesForReferer($rowRes["idcontact"]);
                        $str_tagname = '';
                        if($arr_tagname) {
                            foreach($arr_tagname as $tagname) {
                              $str_tagname .= ($str_tagname == '') ? $tagname : ','.$tagname;
                            }
                        }
                        $rowRes["tag_name"]  = $str_tagname;
                        $row_array[] = $rowRes;
                  }
                  $out_put_array[] = $row_array;
                  //print_r ($out_put_array);
        //exit;
                  return $out_put_array;

              }else{
                  return false;
              }

      }
        /**
      * getDefaultEmailId
      * DEPRECATE
      *
      * Use the following syntax instead
      * $emails = $contact->getChildContactEmail();
      * $default_email = $emails->getDefaultEmail();
      *
      * The function below will return false if there are no emails set as default.
      * @deprecate
      */
    function getDefaultEmailId($idcontact) {
      $q = new sqlQuery($this->getDbCon());
      $sql = "SELECT email_address
          FROM contact_email
        WHERE
        idcontact = {$idcontact} AND
        email_isdefault = 'y'
          ";
      $q->query($sql);
      if($q->getNumRows()) {
		$q->fetch();
      return $q->getData("email_address");
      } else {
		return false;
      }
    }

    function getTotalNumContactsForUser($iduser) {
      $q = new sqlQuery($this->getDbCon());
      $sql = "SELECT COUNT(idcontact) AS total_contacts 
        FROM `{$this->table}` 
        WHERE `iduser` = {$iduser}
          ";
      $q->query($sql);
      if($q->getNumRows()) {
      $q->fetch();
      return $q->getData("total_contacts");
      } else {
      return "0";
      }
    }

    function autoLoadContactsOnScrollDown(EventControler $event_controler) {

      /*echo 'searchkey: '.$event_controler->searchkey;
      echo 'filter: '.$event_controler->filter;*/
      $OfuzList = new OfuzList($this);
                    $OfuzList->setMultiSelect(true);

      if(trim($event_controler->searchkey)) {
        
      if($this->contact_count >= $this->sql_qry_start)	{

        $this->sql_qry_start = $this->sql_qry_start + $this->sql_view_limit;

        $this->clearSearch();
        $this->search_keyword = $event_controler->searchkey;

        $this->setSqlQuery("SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany,contact.position as position,contact.picture as picture,contact.email_address as email_address, contact.phone_number, contact.tags as tags
        FROM ".$this->getSqlViewName()." as contact
        WHERE  
        ( contact.firstname LIKE '%".$this->search_keyword."%'
        OR contact.lastname LIKE '%".$this->search_keyword."%'
        OR contact.company LIKE '%".$this->search_keyword."%')
        GROUP BY contact.idcontact
        ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit);
        $this->query();
      
        //$this->view_list_contacts();
        $OfuzList->displayList();

      }

      } elseif($event_controler->filter) {

      if($this->contact_count >= $this->sql_qry_start)	{

        $this->sql_qry_start = $this->sql_qry_start + $this->sql_view_limit;

        $this->clearSearch();
        $filter = $event_controler->filter;
        $sql_search = "SELECT contact.idcontact as idcontact, contact.firstname as firstname, contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany, contact.position as position, contact.email_address as email_address, contact.phone_number,contact.picture as picture,contact.tags as tags FROM ".$this->getSqlViewName()." AS contact";
        if (!empty($filter)) {
                switch ($filter) {
          case "add":
            $sql_search .= " ORDER BY first_created DESC LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
          break;
          case "modify":
            $sql_search .= " ORDER BY last_update DESC LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
          break;
          case "view":
            $sql_search .= ", `breadcrumb` AS b WHERE b.`type`='".$this->getTable()."' and b.`id`=contact.idcontact AND b.iduser=".$_SESSION['do_User']->iduser."
              GROUP BY contact.idcontact ORDER BY b.`when` DESC LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit; 
          break;
          case "active":
            $sql_search .= " ORDER BY last_activity DESC LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
          break;
          case "alpha":
            $sql_search .= " ORDER BY lastname, firstname LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
          break;
          default:
            $sql_search .= " ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.",".$this->sql_view_limit;
          }
        
        $this->filter = $filter;
        $this->setSqlQuery($sql_search);
        } else {
          $sql_search .= " ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.','.$this->sql_view_limit;
          $this->setSqlQuery($sql_search);
        }
    
        $this->query();
        //$this->view_list_contacts();
        $OfuzList->displayList();
        //echo $this->getSqlQuery();

      }

      } else {

      if($this->contact_count >= $this->sql_qry_start)	{

        $this->sql_qry_start = $this->sql_qry_start + $this->sql_view_limit;

        $this->clearSearch();

        $sql_search = "SELECT contact.idcontact as idcontact, contact.firstname as firstname, contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany, contact.position as position, contact.email_address as email_address, contact.phone_number,contact.picture as picture,contact.tags as tags FROM ".$this->getSqlViewName()." AS contact";

        $sql_search .= " ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.','.$this->sql_view_limit;
        $this->setSqlQuery($sql_search);

        $this->query();
        //$this->view_list_contacts();
        $OfuzList->displayList();
        //echo $this->getSqlQuery();

      }

      }
      
    }

    function eventAutoLoadContactsOnSelectAll(EventControler $evtcl) {

      //if($_SESSION['do_Contacts']->contact_count >= $_SESSION['do_Contacts']->sql_qry_start)	{

      //$_SESSION['do_Contacts']->sql_qry_start = $_SESSION['do_Contacts']->sql_qry_start + $this->sql_view_limit;
          if (empty($this->search_tags)) {
        if($this->contact_count >= $this->sql_qry_start)	{

        $this->sql_qry_start = $this->sql_qry_start + $this->sql_view_limit;
          
        $contacts_left = $this->contact_count - $this->sql_qry_start;
          /**
        $sql = "SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,
          contact.company as company,contact.idcompany as idcompany, contact.position as position,
          contact_email.email_address as email_address, contact_phone.phone_number, contact_phone.phone_type,
          a.`when` as activity_when
          FROM activity as a, contact 
          LEFT JOIN contact_email ON contact.idcontact = contact_email.idcontact 
          LEFT JOIN contact_phone ON contact.idcontact = contact_phone.idcontact
          WHERE contact.iduser = {$_SESSION['do_User']->iduser}
          AND a.idcontact=contact.idcontact
          GROUP BY contact.idcontact 
          UNION
          SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,contact.company as company,contact.idcompany as idcompany, contact.position as position,contact_email.email_address as email_address, contact_phone.phone_number, contact_phone.phone_type,  a.`when` as activity_when
          FROM activity as a, contact_sharing, contact
          LEFT JOIN contact_email ON contact.idcontact = contact_email.idcontact
          LEFT JOIN contact_phone ON contact.idcontact = contact_phone.idcontact
          WHERE contact_sharing.idcoworker = {$_SESSION['do_User']->iduser}
          AND contact.idcontact = contact_sharing.idcontact
          AND a.idcontact=contact.idcontact
          GROUP BY contact.idcontact		
          ORDER BY activity_when DESC, lastname
          LIMIT {$_SESSION['do_Contacts']->sql_qry_start},{$contacts_left}
          ";
          */
        $sql_search = "SELECT contact.idcontact as idcontact,contact.firstname as firstname,contact.lastname as lastname,contact.company as company,contact.position as position, contact.picture as picture,contact.email_address as email_address, contact.phone_number, contact.tags as tags FROM ".$this->getSqlViewName()." as contact ORDER BY ".$this->sql_view_order." LIMIT ".$this->sql_qry_start.", ".$contacts_left;

        $this->sql_qry_start = $this->contact_count + 1;	

        echo '<script type="text/javascript">
          $("input:checkbox").each(function(){this.checked=true;});
          $("div.contacts").css("background-color", "#ffffdd");
          </script>';

        //$this->setSqlQuery($sql);
        $this->setSqlQuery($sql_search);
        $this->query();
        $this->view_list_contacts();

      }
      }

    }

    function getContactEmailsForDojo() {
      $sql = "SELECT email_address
        FROM contact_email
        WHERE
        idcontact = {$_SESSION['do_cont']->idcontact}
      ";

      $this->query($sql);
    }
	/**
	 * load all the contacts of a user from the ContactView table
	 * @see getUserContacts
	 **/
  function getAllContactsForAUser() {
    $table = "userid".$_SESSION['do_User']->iduser."_contact";
    $sql = "SELECT *
      FROM {$table}
      ORDER BY firstname ASC
    ";
    $this->query($sql);
  }

    /**
        Fuction to get the contacts for auto-responder email depending on the tag 
        associated
        @param iduser
        @param tag_name
        @param num_days_to_send
    */
    function getContactsForAutoResponder($iduser,$tag_name,$num_days_to_send){
        
        $qry = "Select contact.idcontact,contact.firstname,contact.lastname,
                contact_email.email_address,tag.tag_name,tag.date_added,tag.idtag
                from contact
                Inner Join contact_email on contact_email.idcontact = contact.idcontact
                Inner Join tag on tag.idreference = contact.idcontact
                where tag.iduser = ".$iduser. "
                AND tag.tag_name = '".$tag_name."'
                AND DATEDIFF(CURDATE(), tag.date_added ) = ".$num_days_to_send
                ;
        //echo $qry;exit;
        $this->query($qry);
    }

    function getIdCompanyForContact($idcontact) {
      $sql = "SELECT idcompany FROM contact WHERE iduser = {$_SESSION['do_User']->iduser} AND idcontact = {$idcontact}";
      $this->query($sql);
      if($this->getNumRows()) {
      $this->fetch();
      return $this->getData("idcompany");
      }
    }

    function eventSetEmailId(EventControler $evtcl) {
      $new_email = new ContactEmail();
      $new_email->idcontact = $evtcl->contact;
      $new_email->email_address = $evtcl->emailid;
      $new_email->email_type = 'Work'; 
      $new_email->add();
    }

    /**
      * This fetches all the contacts for a User.
      * from the main contact table.
      * @return query object
      * @see getAllContactsForAUser
      */
    function getUserContacts($iduser) {
      $sql = "SELECT * FROM {$this->table} WHERE iduser = {$iduser}";
      $this->query($sql);
    }


    /**
      * Function to get the contact id by email
      * @param integer $iduser
      * @param string $email
    */

    function getContactIdByEmail($email,$iduser=""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser ;
        $q = new sqlQuery($this->getDbCon());
        $q->query("select contact.idcontact as idcontact from contact
                            left join contact_email
                            on contact.idcontact = contact_email.idcontact
                            where contact_email.email_address = '".$email."' AND contact.iduser = ".$iduser);

        
        if($q->getNumRows() > 0 ){
            $q->fetch();
            return $q->getData("idcontact");
        }else{ return false ; }
    }

    /**
      * Share Contact with Co-Workers of the auto-shared teams
      * 
      */
    function eventAddContactToTeam(EventControler $evtc) {
        $contact_team = new ContactTeam();
        $contact_team->eventAddContactToTeamCW($this);	
    }	


  function getContactPictureDetails($iduser=''){
    if($iduser!=''){
    $sql="SELECT contact.idcontact,contact.picture
          FROM contact
          INNER JOIN user ON user.idcontact = contact.idcontact
          WHERE user.iduser =".$iduser;
    $this->query($sql);
    }
  }
  
  /**
    * Function to get task completed description for the contact 
    * param int contact primary  key value.
    *
   */ 
  function getTaskCompletedDetails($idcontact){
      $q = new sqlQuery($this->getDbCon());
      $sql = "SELECT task_description FROM task T INNER JOIN contact C ON T.iduser = C.iduser where C.idcontact='".$idcontact."' and T.status='closed' LIMIT 10";
      $q->query($sql);
      if($q->getNumRows()) {
		while($q->fetch()){
         $task_description .= $q->getData("task_description");
         $task_description .='<br>';
      }
        return $task_description;
      } else {
		return false;
      }
      
  }

}
?>
