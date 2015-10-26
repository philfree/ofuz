<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
   /**
     * mydb.addVAriablesToSession
     * This is event set variables in the session
     * <br>
     * <br>- param mixte a_add_to_session can be a string or an array and contain the 
     *                          variable(s) that you want to set in the session
     * <br>- param string goto name of the page to display next.    
     *
     * @package RadriaEvents  
     * @author Philippe Lewicki  <phil@sqlfusion.com>
     * @copyright  SQLFusion LLC 2001-2007
     * @version 4.0
     */

     $a_add_to_session = $this->getParam("a_add_to_session");
     $this->setLogRun(false);
     if (defined("RADRIA_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(RADRIA_LOG_RUN_MYDB_EVENTS);
     }
     $this->setLog("\nCount:".count($a_add_to_session));
     if (is_array($a_add_to_session)) {
        foreach($a_add_to_session as $varname => $value) {
            //global $$varname;
            //$$varname = $value;
            $this->setLog("\n".$varname." = ".$value);
            $_SESSION[$varname] = $value;
        }
     } else {
         $this->setLog("add session to session : ".$a_add_to_session." => ".$this->getParam($a_add_to_session));
         $_SESSION[$a_add_to_session] = $this->getParam($a_add_to_session);
     }

     if (strlen($goto)>0) {
        $disp = new Display($goto);
        $this->setDisplayNext($disp);
     }
?>