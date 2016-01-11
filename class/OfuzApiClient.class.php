<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
 * This will basically process the user request.
 * Checks for user authentication and adds data
 * to the database if there is no data error
 * occur.
* Copyright 2002 - 2007 SQLFusion LLC
* @author Abhik Chakraborty      info@sqlfusion.com
* @version 0.1
* Base class for the Ofuz REST API
* 
*
*/

include_once("class/OfuzApiClientBase.class.php");
class OfuzApiClient extends OfuzApiClientBase{

	var $api_url = "";
	//var $api_url = "http://abhik:3atBr3ad@dev.ofuz.net/api.php";
		
   	 /*Constructor Function*/
   	function OfuzApiClient($key='', $format='') {
		parent::OfuzApiClientBase($key, $format);
		$this->api_url = OFUZ_NET."/api.php";
   	 }

	/**
     	* Alias to submit()
     	*/
    	function add_contact() {
                $this->method = "add_contact";
        	return $this->submit();
    	}

        function search_contact(){
            $this->method = "search_contact";
            return $this->submit();
        }

        /**
		 * Method: get_contact_id
		 * request requires 
		 * - firstname
		 * - lastname
		 * - email
		 *  Sample code
		 *  $api_call = new OfuzApiClient("326939597dc3e8e698d0618c4764ba7b", "json");
			$api_call->setObject(true);
	        $api_call->firstname = $_SESSION['do_User']->firstname;
	        $api_call->lastname = $_SESSION['do_User']->lastname;
	        $api_call->email = $_SESSION['do_User']->email;
	        $api_call->get_contact_id();	 
		 * 
		 * @return a structure with Array & object like that:
		 * 	 
		 * 
		   Array ( 
		       [0] => stdClass Object (
						  [msg] => Contact Found
						  [stat] => ok
						  [code] => 601 )
               [1] => Array  (
						  [0] => stdClass Object (
								   [idcontact] => 12685
								   [firstname] => Philippe
								   [lastname] => Lewicki
								   [company] => SQLF1
								   [position] => 
								   [email] => philippe@sqlfusion.com)
						 )
                 )
		  * 
		  * i
		  */

        function get_contact_id(){
            $this->method = "get_contact_id";
            return $this->submit();
        }

         function get_contacts(){
            $this->method = "get_contacts";
            return $this->submit();
        }
	
        function add_tag(){
            $this->method = "add_tag";
            return $this->submit();
        }

        function delete_tag(){
            $this->method = "delete_tag";
            return $this->submit();
        }
        function add_note(){
            $this->method = "add_note";
            return $this->submit();
        }
        function add_task(){
            $this->method = "add_task";
            return $this->submit();
        }

        // Invoice related methods 
        function add_invoice(){
            $this->method = "add_invoice";
            return $this->submit();
        }        

        function add_invoice_line(){
            $this->method = "add_invoice_line";
            return $this->submit();
        }

        function add_recurrent(){
            $this->method = "add_recurrent";
            return $this->submit();
        }
		
		/**
		 * Stop recurrent on an invoice
		 * requires 
		 *   - idinvoice
		 */
		
		function stop_recurrent() {		
			$this->method = "stop_recurrent";
			return $this->submit();
		}
		function get_inv_amt_due(){
            $this->method = "get_inv_amt_due";
        	return $this->submit();
        }
		
		/**
		 * Method get_contact_subscription
		 * return the details of an invoice with recurrent subscription for a contact
		 * requires 
		 *  - idcontact
		 * return something like:
		 * stdClass Object
			(
				[next_charge_date] => 2010-02-18
				[recurrence] => 1
				[recurrence_frequency] => Month
				[number] => 25
				[description] => Descrp
				[amount] => 0.00
				[date_created] => 2010-01-18
				[date_due] => 2010-01-18
				[amount_due] => 24.00
				[line_item] => Array
					(
						[0] => stdClass Object
							(
								[item] => Ofuz24
								[description] => Monthly Subscription
								[price] => 24.00
								[qty] => 1.00
							)

					)

			)
           */
			
		function get_contact_subscription() {
			$this->setMethod("get_contact_subscription");
			return $this->submit();
		}
        
	
}

?>
