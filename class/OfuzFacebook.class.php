<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     */

class OfuzFacebook extends DataObject {
    
    private $appapikey = FACEBOOK_API_KEY;
    private $appsecret = FACEBOOK_APP_SECRET;
    public $fb_uid = 0;
    private $fbobj ;
    public $connected = false;

    function OfuzFacebook(&$fbobj){
       $this->fbobj = (object) $fbobj; 
    }
    
    function isLoggedInFacebook(){
      $fb_user_id = 0;
      $fb_user_id = $this->fbobj->get_loggedin_user();
      $this->fb_uid =  $fb_user_id;
    }

    function getFbFriends(){
       $fql = 'SELECT uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1='.$this->fb_uid.')';
       $friends = array();
        try{
          @$_friends = $this->fbobj->api_client->fql_query($fql);
        }catch(Exception $e){
	    return 'session_expired';
	}
        if(is_array($_friends) && count($_friends)) {
          foreach ($_friends as $friend) {
            $friends[] = $friend['uid'];
          }
          return $friends;
        }else{
          return false;
        }
    }

    function getFbUserName($uid = 0){
        if(!$uid){$uid = $this->fb_uid;}
        $user_details=$this->fbobj->api_client->users_getInfo($uid, @array('last_name','first_name'));
        if(is_array($user_details)){
            $data['first_name']=$user_details[0]['first_name'];
            $data['last_name']=$user_details[0]['last_name'];
        }else{
            $data['first_name']="";
            $data['last_name']="";
        }
        return $data;
    }

    function getFbUserAffiliations($uid = 0){
        if(!$uid){$uid = $this->fb_uid;}
        $data = $this->fbobj->api_client->users_getInfo($uid, array('affiliations'));
        return $data;
    }

    function getWorkHistory($uid = 0){
        if(!$uid){$uid = $this->fb_uid;}
        $data = $this->fbobj->api_client->users_getInfo($uid, array('work_history'));
        return $data;
    }

    function getProfileURL($uid = 0){
        if(!$uid){$uid = $this->fb_uid;}
        $data = $this->fbobj->api_client->users_getInfo($uid, array('profile_url'));
        return $data;
    }

    function getProfilePicWithLogo($uid = 0){
        if(!$uid){$uid = $this->fb_uid;}
        $data = $this->fbobj->api_client->users_getInfo($uid, array('pic_with_logo'));
       // $data = $this->fbobj->api_client->users_getInfo($uid, array('pic'));
        return $data;
    }
    
    function getUserExtendedPermissions($type){
        $permission = $this->fbobj->api_client->users_hasAppPermission($type,$this->fb_uid);
        return $permission;
    }

    function getFriendsList(){
        $data  = $this->fbobj->api_client->friends_getLists();
        return $data ; 
    }
   
    function getFriendsInList($idlist){
        $data  = $this->fbobj->api_client->friends_get($idlist);
        return $data ; 
    }

    function setUserNull(){
      $this->fbobj->api_client->set_user(null, null);
    }

     public function __wakeup() {
       return true ;
    }
}
?>