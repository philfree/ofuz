<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

class DiscussionEmailSetting extends DataObject {

    public $table = 'discussion_email_setting';
    protected $primary_key = 'iddiscussion_email_setting';

    /*
      Function to check the discussion alert for the task for that user.
      If the user has not set anything bydefault it will be set to 'Yes'
      If user wants to set as No user can.
      So this method checks if user has an entry in this table and will 
      data or false.
    */
    function isDiscussionAlertSet($id,$setting_level,$iduser =""){
      if($iduser == ""){ $iduser = $_SESSION['do_User']->iduser ; }
      $q = new sqlQuery($this->getDbCon());
      $q->query("Select iddiscussion_email_setting,discussion_email_alert from ".$this->table." 
                 Where iduser = ".$iduser." AND id = ".$id ." AND setting_level = '".$setting_level."'"
                );
      if($q->getNumRows()){ 
          $data = array();
          while($q->fetch()){
            $data["iddiscussion_email_setting"] = $q->getData("iddiscussion_email_setting");
            $data["discussion_email_alert"] = $q->getData("discussion_email_alert");
          }
          
          return $data;
      }else{
        return false;
      }
    }

    /*
      Event method to set the task discussion email alert on for a 
      specific task when after it is set off
    */
    function eventSetOnDiscussionAlert(EventControler $evtcl) { 
        $this->getId($evtcl->id);
        $this->delete();
    }

    /*
      Event method to set the task discussion email alert off for a 
      specific task when after it is set on
    */
    function eventSetOffDiscussionAlert(EventControler $evtcl) { 
        $this->iduser = $_SESSION['do_User']->iduser;
        $this->id = $evtcl->id;
        $this->setting_level = $evtcl->setting_level;
        $this->discussion_email_alert = 'No';
        $this->add();
    }
}
?>