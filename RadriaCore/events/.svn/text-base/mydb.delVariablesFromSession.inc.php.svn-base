<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
    /**
     * mydb.delVariablesFromSession
     * This is event delete variables from the session
     *
     * <br>- param mixte a_del_from_session can be a string or an array and contain the 
     *                          variable(s) that you want to delete from the session
     * <br>- param string goto name of the page to display next.
     *
     * @package RadriaEvents
     * @author Philippe Lewicki  <phil@sqlfusion.com>
     * @copyright  SQLFusion LLC 2001-2004
     * @version 3.0	 
     */

     if (is_array($a_del_from_session)) {
        foreach($a_del_from_session as $varname => $value) {
            global $$varname;
            session_unregister($varname, $value);
        }
     } else {
         global $$a_del_from_session;
         session_unregister($a_del_from_session);
     }

     if (strlen($goto)>0) {
        $disp = new Display($goto);
        $this->setDisplayNext($disp);
     }
?>