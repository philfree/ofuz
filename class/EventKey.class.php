<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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