<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * UserPlan class
     * Using the DataObject
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzOnline
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.5
     */

class UserPlan extends DataObject {
    public $table = "";
    protected $primary_key = "";

	function getUserPlanDetails($plan) {
		
	}

	/**
	  * Check if user is allowed to add invoices 
      * The limit is currently a number of invoices per month
	  * @return boolean
	  */
	function canUserAddInvoice() {
			return true;
	}
    /** 
     * check if the user can add more contact
     * based on his plan and plan configuration.
     * @return boolean if the user can add more contacts.
     */

	function canUserAddContact() {
		return true;
	}
	
    /**
     * Check if the user can add more project
     * based on his plan and number of existing projects.
     * @return boolean if it can or not.
     */

	function canUserAddProject() {
			return true;
	}


    /**
      * Specific method to add user to PHIL
    */
    function eventAddUserToPhilContact(EventControler $evtcl){
    }

    /**
      * Specific method to add user to PHIL
    */
    function addUserToPhilContact($firstname,$lastname,$company,$email){
        }

	/**
	 * eventUpgrade
	 */

   function eventUpgrade(EventControler $evctl) {
			$evctl->setUrlNext("index.php");      
    }

}
?>
