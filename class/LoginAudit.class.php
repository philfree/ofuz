<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * LoginAudit class
     * Using the DataObject
     */

class LoginAudit extends DataObject {
    
    public $table = "login_audit";
    protected $primary_key = "idlogin_audit";


    /**
      * Login audit method to keep track of logged in user detail as history
      * @param string $login_type 
      * @param integer $iduser 
    */
    public function do_login_audit($login_type="General",$iduser = ""){
        if($iduser == ""){ $iduser = $_SESSION['do_User']->iduser ; }
        $last_login = $this->getLastLogin($iduser);
        if($last_login){ 
            $this->getId($last_login);
            $this->last_login = date("Y-m-d h:i:s");
            $this->ip_address = $this->getUserIpAddress();
            $this->login_type = $login_type;
            $this->update();
        }else{
            $this->last_login = date("Y-m-d h:i:s");
            $this->ip_address = $this->getUserIpAddress();
            $this->login_type = $login_type;
            $this->iduser = $iduser;
            $this->add();
        }

    }

    
    /**
      * Get when the user logged in last time
      * @param integer $iduser 
      * @return idlogin_audit if logged in else return false when the user has never logged in to the system.
    */
    public function getLastLogin($iduser){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table." where iduser = ".$iduser);
        if($q->getNumRows()){
            $q->fetch();
            return $q->getData("idlogin_audit");
        }else{
            return false ;
        }
    }


    /**
      * Get the IP address of the user
      * @return IP address of the user
     */
    public function getUserIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
        {
          $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
        {
          $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
          $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
?>
