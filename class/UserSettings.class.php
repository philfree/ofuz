<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
 * 
 *   UserSettings
 *   
 *   variables:
	+-----------------+--------------+------+-----+---------+----------------+
	| Field           | Type         | Null | Key | Default | Extra          |
	+-----------------+--------------+------+-----+---------+----------------+
	| iduser_settings | int(10)      | NO   | PRI | NULL    | auto_increment |
	| setting_name    | varchar(100) | NO   |     | NULL    |                |
	| setting_value   | varchar(100) | NO   |     | NULL    |                |
	| iduser          | int(14)      | NO   |     | NULL    |                |
	+-----------------+--------------+------+-----+---------+----------------+

    List Of setting names and possible vales 
    
    1. task_discussion_alert : Yes/No Yes-Email Alert globally on / No- Email alert off
    2. google_gears : Yes/No  Yes-Gears On / No- Gears Off
    3. invoice_logo : Logo file name
    4. authnet_merchant_id : Authorized.Net marchant Id
    5. authnet_login : Authorized.Net Login
    6. paypal_business_email : Paypal Business email
    7. currency : currency value
    * 
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzCore
      * @license GNU Affero General Public License
      * @version 0.6
      * @date 2011-01-14
      * @since 0.3
*/
class UserSettings extends DataObject {

    public $table = 'user_settings';
    protected $primary_key = 'iduser_settings';

    public $global_task_discussion_alert;

    /*
      Method to get the setting value on setting name
      Returns array with data on success else returns
      false
    */
    function getSettingValue($setting_name,$iduser = ""){
      if($iduser == "" ){ $iduser = $_SESSION['do_User']->iduser; }
      $q = new sqlQuery($this->getDbCon());
      $q->query("Select iduser_settings,setting_value from ".$this->table. "
                  where iduser = ".$iduser." AND setting_name = '".$setting_name."'
                ");
     
      if($q->getNumRows()){
           $data = array();
           while($q->fetch()){
              $data["iduser_settings"] = $q->getData("iduser_settings");
              $data["setting_value"] = $q->getData("setting_value");   
           }
           return $data;
      }else{
          return false;
      }
    }

    /**
     *  Generic settings methods
     */
    /**
     *  setSetting
     *  Set Setting will add a setting if it doesn't already exists and
     *  update its value if already exist.
     *  @param string setting_name name of the setting.
     *  @param mix setting_value value for that setting
     */
    public function setSetting($setting_name, $setting_value) {
      if ($this->getSetting($setting_name) !== false) {
        $this->setting_value = $setting_value;
        $this->update();			
      } else {
        $this->iduser = $_SESSION['do_User']->iduser;
        $this->setting_name = $setting_name;
        $this->setting_value = $setting_value;
        $this->add();
      }
	} 
	
	/**
	 *  eventSetSetting
	 *  eventaction to set a setting.
	 */
	public function eventSetSetting(EventControler $evctl) {
		$this->setSetting($evctl->setting_name, $evctl->setting_value);
	}
	
	
	/**
	 *  deleteSetting 
	 *  Delete an existing setting, return false if the setting is not found.
	 */
	public function deleteSetting($setting_name) {
		if ($this->getSetting($setting_name) !== false) {
			$this->delete();
			return true;
		} else {
			return false;
		}
	}
	
	/**  
	 *  getSetting
	 *  Get the value of a setting
	 *  @param setting_name
	 *  @return setting_value return false if no value.
	 */
	public function getSetting($setting_name) {
		$this->iduser = $_SESSION['do_User']->iduser;
		$this->query("SELECT * FROM ".$this->getTable()." WHERE iduser = ".$this->iduser." AND setting_name = '".$this->quote($setting_name)."'");
		if ($this->getNumRows()) { 
			return $this->setting_value;
		} else {
			return false;
		}
	}


    /*
      Seeting the global discusssion alert email to No
    */
    function eventSetOffDiscussionAlert(EventControler $evtcl) { 
        $this->iduser = $_SESSION['do_User']->iduser;
        $this->setting_name = 'task_discussion_alert';
        $this->setting_value = 'No';
        $this->add();
    }

     /*
          Setting the discussion alert to on/off
          if set to No then user should not receive discussion email
          if set to Yes then user should receive email
     */
     function eventSetOnOffDiscussionAlert(EventControler $evtcl) { 
        if($evtcl->setting_value == 'Yes'){
            $setting_value = 'No'; 
        }else{
            $setting_value = 'Yes';
        }
        $q = new sqlQuery($this->getDbCon());
        $q->query("update ".$this->table." set setting_value = '".$setting_value. "' Where ".$this->primary_key." = ".$evtcl->id. " Limit 1");
    }

   /*** deprecate moved to a plugIn
    function eventSetOffGoogleGears(EventControler $evtcl) { 
        $this->iduser = $_SESSION['do_User']->iduser;
        $this->setting_name = 'google_gears';
        $this->setting_value = 'No';
        $this->add();
    }
    
     function eventSetOnOffGoogleGears(EventControler $evtcl) { 
        if($evtcl->setting_value == 'Yes'){
            $setting_value = 'No'; 
        }else{
            $setting_value = 'Yes';
        }
        $q = new sqlQuery($this->getDbCon());
        $q->query("update ".$this->table." set setting_value = '".$setting_value. "' Where ".$this->primary_key." = ".$evtcl->id. " Limit 1");
    }
***/

    /*
      Adding Authorized.NET setting values i.e. Transaction key and Login
    */
    function eventAddAuthNetDetail(EventControler $evtcl) { 
        if($evtcl->auth_login == '' || $evtcl->auth_merchant_id == ''){
             $_SESSION['in_page_message'] = _("Please Enter Login and Transacton Key for Authorized.net");
        }else{
            $this->addNew();
            $this->setting_name = 'authnet_login';
            $this->setting_value = $evtcl->auth_login;
            $this->iduser = $_SESSION['do_User']->iduser;
            $this->add();
            $this->addNew();
            $this->setting_name = 'authnet_merchant_id';
            $this->setting_value = $evtcl->auth_merchant_id;
            $this->iduser = $_SESSION['do_User']->iduser;
            $this->add();
        }
    }

    /*
      Update Authorized.NET setting data
    */
    function eventUpdateAuthNetDetail(EventControler $evtcl) { 
        $this->getId($evtcl->id_authlogin);
        $this->setting_value = $evtcl->auth_login;
        $this->update();

        $this->getId($evtcl->id_authmerchant);
        $this->setting_value = $evtcl->auth_merchant_id;
        $this->update();
        $_SESSION['in_page_message'] = _("Data Updated");  
      
    }

    /*
        Deleting Authorized.NET setting data
    */
    function eventDelAuthNetDetail(EventControler $evtcl) { 
        $q = new sqlQuery($this->getDbCon());
        $qry = "delete from ".$this->table. " 
                  Where ".$this->primary_key. " IN (".$evtcl->id_authlogin.",".$evtcl->id_authmerchant.") LIMIT 2 ";

        $q->query($qry);
        $_SESSION['in_page_message'] = _("Data Deleted");
    }

    /*
        Adding Paypal Business email
    */
    function eventAddPaypalDetail(EventControler $evtcl) { 
        if($evtcl->paypal_b_email == ''){
            $_SESSION['in_page_message'] = _("Please Enter Paypal Business Email");
        }else{  
            $this->addNew();
            $this->setting_name = 'paypal_business_email';
            $this->setting_value = $evtcl->paypal_b_email ; 
            $this->iduser = $_SESSION['do_User']->iduser;
            $this->add();
        }
    }


    /*
        Update Paypal business email
    */
    function eventUpdatePaypalDetail(EventControler $evtcl) { 
        $this->getId($evtcl->id_paypal_business);
        $this->setting_value = $evtcl->paypal_b_email;
        $this->update();
        $_SESSION['in_page_message'] = _("Data Updated");  
    }

    /*
      Delete Paypal business Email
    */
    function eventDelPayPalDetail(EventControler $evtcl) { 
         $this->getId($evtcl->id_paypal_business);
         $this->delete();
         $_SESSION['in_page_message'] = _("Data Deleted"); 
    }

	/*
        Adding Stripe API Key
    */
    function eventAddStripeDetail(EventControler $evtcl) { 
         if($evtcl->stripe_api_key == '' || $evtcl->stripe_publish_key == ''){
             $_SESSION['in_page_message'] = _("Please Enter API and Publish Key for Stripe.com");
        }else{
            $this->addNew();
            $this->setting_name = 'stripe_api_key';
            $this->setting_value = $evtcl->stripe_api_key;
            $this->iduser = $_SESSION['do_User']->iduser;
            $this->add();
            $this->addNew();
            $this->setting_name = 'stripe_publish_key';
            $this->setting_value = $evtcl->stripe_publish_key;
            $this->iduser = $_SESSION['do_User']->iduser;
            $this->add();
    }
}


    /*
        Update Stripe API details
    */
    function eventUpdateStripeDetails(EventControler $evtcl) { 
        $this->getId($evtcl->id_stripe_api_key);
        $this->setting_value = $evtcl->stripe_api_key;
        $this->update();

        $this->getId($evtcl->id_stripe_publish_key);
        $this->setting_value = $evtcl->stripe_publish_key;
        $this->update();
        $_SESSION['in_page_message'] = _("Data Updated");  
    }

	/*
      Delete Paypal business Email
    */
    function eventDelStripeDetail(EventControler $evtcl) { 
         $q = new sqlQuery($this->getDbCon());
        $qry = "delete from ".$this->table. " 
                  Where ".$this->primary_key. " IN (".$evtcl->id_stripe_api_key.",".$evtcl->id_stripe_publish_key.") LIMIT 2 ";

        $q->query($qry);
        $_SESSION['in_page_message'] = _("Data Deleted");
    }
	

	/*
	 * Add Payment selection details
	*/
	function eventAddPaymentSelection(EventControler $evtcl) {
		if($evtcl->payment_selection == ''){
			$_SESSION['in_page_message'] = _("Please select any one of the payment gateway which you would like to have in payment process");
		} else {
			
			$this->addNew();
			$this->setting_name = 'payment_selection';
			$this->setting_value = $evtcl->payment_selection;
			$this->iduser = $_SESSION['do_User']->iduser;
			$this->add();
		}
	}
	
	/*
	 * Update Payment Selection details
	*/
	function eventUpdatePaymentSelection(EventControler $evtcl) {
		$this->getId($evtcl->id_payment_selection);
        $this->setting_value = $evtcl->payment_selection;
        $this->update();
        $_SESSION['in_page_message'] = _("Data Updated");  
	}
	
	/*
	 *  Delete Payment Selection details
	*/ 
	function eventDelPaymentSelection(EventControler $evtcl) {
        $this->getId($evtcl->id_payment_selection);
        $this->delete();
        $_SESSION['in_page_message'] = _("Data Deleted"); 
	}
	
	
    
		
    /*
        Listing the Currency symbols
    */
    function listCurrencies($selected = ""){
          $html = '';
          $html .= '<select name = "currency" id="">';

          $html .= '<option value = "AUD-$" '.$this->checkSelectedCurrency("AUD-$",$selected).'>'._('Australian dollar').' (AUD-$)</option>';

          $html .= '<option value = "BRL-R$" '.$this->checkSelectedCurrency("BRL-R$",$selected).'>'._('Brazilian real').' (BRL-R$)</option>';

          $html .= '<option value = "GBP-&amp;pound;" '.$this->checkSelectedCurrency("GBP-&pound;",$selected).'>'._('British pound').' (GBP-&pound)</option>';

          $html .= '<option value = "CAD-$" '.$this->checkSelectedCurrency("CAD-$",$selected).'>'._('Canadian dollar').' (CAD-$)</option>';

          $html .= '<option value = "Euro-&amp;euro;" '.$this->checkSelectedCurrency('Euro-&euro;',$selected).'>'._('Euro').'-&euro;</option>';

          $html .= '<option value = "HKD-$" '.$this->checkSelectedCurrency("HKD-$",$selected).'>'._('Hong Kong dollar').' (HKD-$)</option>';
          
          $html .= '<option value = "INR-&#2352;" '.$this->checkSelectedCurrency("INR-&#2352;",$selected).'>'._('Indian rupee').' (INR-&#2352;)</option>';
          
          $html .= '<option value = "JPY-&amp;yen;" '.$this->checkSelectedCurrency("JPY-&yen;",$selected).'>'._('Japanese yen').' (JPY-&yen)</option>';
    
          $html .= '<option value = "MUR-Rs" '.$this->checkSelectedCurrency("MUR-Rs",$selected).'>'._('Mauritian Rupees').' (MUR-Rs)</option>';

          $html .= '<option value = "NZD-$" '.$this->checkSelectedCurrency("NZD-$",$selected).'>'._('New Zealand dollar').' (NZD-$)</option>';

          $html .= '<option value = "ZAR-R" '.$this->checkSelectedCurrency("ZAR-R",$selected).'>'._('South African rand').' (ZAR-R)</option>';

          $html .= '<option value = "CHF-Fr" '.$this->checkSelectedCurrency("CHF-Fr",$selected).'>'._('Swiss franc').' (CHF-Fr)</option>';

          $html .= '<option value = "USD-$" '.$this->checkSelectedCurrency("USD-$",$selected).'>'._('United States dollar').' (USD-$)</option>';

          $html .='</select><br />';
          return $html;
    }
    
    /*
       List different date format
    */
    function listInvDateFormat($selected = ""){
          $html = '';
          $html .= '<select name = "inv_dt_format" id="">';

          $html .= '<option value = "Y-m-d" '.$this->checkSelectedInvDateFormat("Y-m-d",$selected).'>YYYY-MM-DD</option>';

          $html .= '<option value = "d/m/Y" '.$this->checkSelectedInvDateFormat("d/m/Y",$selected).'>DD/MM/YYYY</option>';

          $html .= '<option value = "m/d/Y" '.$this->checkSelectedInvDateFormat("m/d/Y",$selected).'>MM/DD/YYYY</option>';

          $html .='</select><br />';
          return $html;
    }

    function checkSelectedCurrency($curr,$sel){
        //echo $curr;
        //echo ' ---- ';
        //echo $sel;
        if($sel == $curr){
             //echo 'here';
             return  'selected="selected"';; 
        }
    }

    function checkSelectedInvDateFormat($dt_format,$sel){
        if($sel == $dt_format){
             return  'selected="selected"';; 
        }
    }

    /*
      Updating Currency Settings
    */
    function eventUpdateCurrency(EventControler $evtcl) { 
        //echo $evtcl->currency;exit;
        $this->getId($evtcl->id_currency);
        //$this->setting_value = str_replace("&","&amp;",$evtcl->currency);
        $this->setting_value = $evtcl->currency;
        //$this->setting_value = 'Euro-&euro;';
        //$q = new sqlQuery($this->getDbCon());
        //$q->query("update ".$this->table." set setting_value = '".$evtcl->currency."' where iduser_settings = ".$evtcl->id_currency);
        $this->update();
        
        $_SESSION['in_page_message'] = _("Data Updated");  
    }

    /*
      Adding Currency Settings
    */
    function eventAddCurrency(EventControler $evtcl) {  
        $this->addNew();
        $this->setting_name = 'currency';
        $this->setting_value = $evtcl->currency ; 
        $this->iduser = $_SESSION['do_User']->iduser;
        $this->add();
        
    }

    /*
      Setting up the Date Format
    */
    function eventAddInvDateFormat(EventControler $evtcl) {  
        $this->addNew();
        $this->setting_name = 'inv_date_format';
        $this->setting_value = $evtcl->inv_dt_format ; 
        $this->iduser = $_SESSION['do_User']->iduser;
        $this->add();
        
    }

    /*
      Updating the Date Format
    */
    function eventUpdateInvDateFormat(EventControler $evtcl) { 
        $this->getId($evtcl->id_inv_date_format);
        $this->setting_value = $evtcl->inv_dt_format;
        $this->update();
        
        $_SESSION['in_page_message'] = _("Data Updated");  
    }


    /**
      * Event method to check the invoice logo extension
      * GIF is not supported by the html2pdf for PDF generation so if user 
      * try to upload gif file give them a message
    */
    function eventCheckInvLogoExtension(EventControler $evtcl) { 
       // print_r($_FILES);
        $filename = $_FILES['userfile']['name'][0] ;
        if(is_array($_FILES['userfile']['type'])){
            $file_type = strtolower($_FILES['userfile']['type'][0]) ;
        }
        $filename = strtolower($filename) ; 
        $exts = split("[/\\.]", $filename) ; 
        $n = count($exts)-1; 
        $exts = $exts[$n]; 
        if($exts == "gif" || $file_type == "image/gif"){
          $evtcl->updateparam("doSave", "no") ;
          unset($_FILES);
          $_SESSION['in_page_message'] = _("GIF files for logo is not supported");  
          $evtcl->setDisplayNext(new Display("settings_invoice.php"));
        }

    }
     
}
?>
