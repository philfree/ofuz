<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

   /** 
    * MailMerge Class
    * Used to Merge contact and email template.
    * Extend Emailer for its merge capabilities.
    *
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @access public
    */

class MailMerge extends Emailer {

    private $contacts = Array();

    /**
     * eventSetUsers()
     * Grab the users ids from the contacts multi select and 
     * store the object in the session
     */
    function eventSetUsers(EventControler $event_controler) {
        if (strlen($event_controler->tags)==0) {
            $this->setLog("\n Not tags Adding users to the MailMerge");
            $contacts = $event_controler->getParam("ck");
            if (is_array($contacts)) {
               $this->contacts = $contacts;
            }
        }
        $_SESSION['MailMerge_Contacts']= $this; 
    }
}

?>