<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * EventKey class
     * Extends Event
     * @author Jay Link info@sqlfusion.com
     */

class EventKey extends Event {

    function eventAjaxGetEventKey(EventControler $event_controler) {
        $e_ajax = new Event();
        $key = $e_ajax->getSecureKey();
        $event_controler->addOutputValue($key);
    }

}
?>