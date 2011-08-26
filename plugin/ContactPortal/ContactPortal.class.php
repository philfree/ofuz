<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/ 

    /**
      * ContactPortal class
      * Extend the Contact class to manage the client portal.
      * 
      *
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzCore
      * @license ##License##
      * @version 0.6.2
      * @date 2010-11-13
      * @since 0.6.2
      */
   
class ContactPortal extends Contact {

    function eventGenerateSharedUrl(EventControler $evtcl) {
        $msg = "";
        $dispMsg = new Display($evtcl->goto) ;
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT portal_code FROM contact WHERE idcontact = ".$this->idcontact. " AND portal_code != ''");
        
        if($q->getNumRows()){
            while($q->fetch()){
                $portal_code = $q->getData("portal_code");
              }
        } else{
                $portal_code = rand(0, pow(10,5));
                $q->query("UPDATE contact SET portal_code = '".$portal_code."' WHERE idcontact =".$this->idcontact);
        }
        
        $share_url = $GLOBALS['cfg_ofuz_site_http_base'].'cp/'.$portal_code;
        $this->sendMailToShareNotesFiles($share_url);
        $_SESSION['in_page_message'] = 'url portal initiated';
        $evtcl->setDisplayNext($dispMsg) ;
    }
    
    //sends a mail to the contact with unique Url link
    function sendMailToShareNotesFiles($share_url){ 
    
        $this->sendMessage(new EmailTemplate("contact share notes url"), 
                     Array ('firstname' => $this->firstname,
                            'user_fullname' => $_SESSION['do_User']->getFullName(),
                            'contact_portal_url' => $share_url
                           )
                     ) ;        
    }

    //checks if unique Url link already generated
    function checkIfNotesShared($idcontact=''){
        if (empty($idcontact)) { $idcontact = $this->idcontact; }
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT portal_code FROM contact WHERE idcontact=".$idcontact." AND portal_code != ''");

        if($q->getNumRows()){
            while($q->fetch()){
                $portal_code = $q->getData("portal_code");
                return $portal_code;
              }
        } else{
                return null;
        }
    }

    //generates new unique Url link to Share the Notes & Files
    function eventGenerateNewSharedUrl(EventControler $evtcl){
        $portal_code = rand(0, pow(10,5));
        $q = new sqlQuery($this->getDbCon());        
        $q->query("UPDATE contact SET portal_code = '".$portal_code."' WHERE idcontact =".$this->idcontact);
        
        $share_url = $GLOBALS['cfg_ofuz_site_http_base'].'cp/'.$portal_code;        
        $this->sendMailToShareNotesFiles($share_url);
        
        $_SESSION['in_page_message'] = 'url portal regenerated';
        $dispMsg = new Display($evtcl->goto) ;
        $evtcl->setDisplayNext($dispMsg) ;    
    }
    
    //stops sharing the Notes & Files
    function eventStopSharingNotes(EventControler $evtcl){
        $dispMsg = new Display($evtcl->goto) ;
        $q = new sqlQuery($this->getDbCon());
        $q->query("UPDATE contact SET portal_code = '' WHERE idcontact =".$this->idcontact);
        $_SESSION['in_page_message'] = 'url portal stoped';
        $evtcl->setDisplayNext($dispMsg) ;
    }



}


?>
