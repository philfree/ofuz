<?php 
    /**
      *@author Abhik Chakraborty, Philippe Lewicki, Ravi Rokkam
      * The class will be compatable with the new Graph API and connect API
     */

class RadriaFacebookConnect extends BaseObject {
    
    public $fb_uid = 0;
    private $fbobj ;
    public $connected = false;

    //Deperecated v3.1.1 of FB SDK
    private $fb_session = '';
    
    private $fb_aps_id = '';
    private $fb_aps_secrect_key = '';
    private $permission = '';
    private $access_token = '';

    private $error_message = '';
    
    private $user_data = array();

    /**
      * Constructor function
      * @param object $fbobj, the Facebook Object
      * @param integer $fb_aps_id
      * @param string $fb_aps_secrect_key
    */
    function __construct($fbobj,$fb_aps_id,$fb_aps_secrect_key){
        $this->fbobj = (object) $fbobj; 
        $this->fb_aps_id = $fb_aps_id;
        $this->fb_aps_secrect_key = $fb_aps_secrect_key ;
        $this->permission = 'email,publish_stream,read_stream,offline_access,user_birthday,user_location,read_friendlists,sms';
    }



    /**
      * Get session from facebook
      * @return fb_session
      * NOTE : Deperecated v3.1.1 of FB SDK
    */
    function getFbSession(){
      return $this->fb_session ;
    }

    /**
      * Function set Access Token
    */
    function setAccessToken($tocken){
        $this->access_token = $tocken ;
    }


    /**
      * Function get Access Tocken
    */
    function getAccessToken(){
        return $this->access_token ;  
    }

    /**
      * Function set Fb user id in member var $fb_uid
      * @param integer $id
    */
    function setFbUserId($id){
        $this->fb_uid = $id ;
    }

    /**
      * Function to get the Fb user id
      * @return userid of the logged in Facebook User
    */
    function getFbUserId(){
        return $this->fb_uid ;
    }
    
    
    /**
      * Function to get the list of permissions
    */
    function getFbPermissionList(){
        return $this->permission ;
    }


    /**
      * Function to set the error message.
      * @param string $str
    */
    function setErrorMessage($str){
      $this->error_message = $str ;
    }


    /**
      * Functon to get the error message.
      * Returns the error string if there is anything
    */
    function getErrorMessage(){
        return $this->error_message ;
    }


    /**
      * Sets the user data after the API call /me
      * The method is called from isLoggedInFacebook method
      * @param array $data, the facebook connected user data
      * @see http://developers.facebook.com/docs/reference/api/
      * @see http://developers.facebook.com/docs/reference/api/user/
      * @see RadriaFacebookConnect::isLoggedInFacebook()
    */
    function setUserData($data){ 
        if(is_array($data) && count($data) > 0 ){
            $this->user_data = $data ;
        }
    }
    

    /**
      * Returns the userdata stored in array user_data
    */
    function getUserData(){
        return $this->user_data ;
    }

    /**
      * This is deprecated as per the new auth API dated 13,dec 2011
      * getSession() is no longer there on v3.1.1 of FB SDK
    */
    /*function isLoggedInFacebook(){
      $fb_user_id = 0;
      try{
          $this->fb_session = $this->fbobj->getSession();
          if($this->fb_session){
            $fb_user_id = $this->fbobj->getUser();
            $this->setFbUserId($fb_user_id);
            try{ 
                $me = $this->fbobj->api('/me');
                //print_r($me);
                if($me){
                    if($_REQUEST["fbs_".$this->fb_aps_id]==""){
                       return ;
                    }
                    $a = str_ireplace("\"","",$_REQUEST["fbs_".$this->fb_aps_id]);
                    if(!$a)
                    {
                        $er = "Permission Disallow!";
                        $this->setErrorMessage($er);
                    }else{
                        $this->setAccessToken($a);
                        $this->setUserData($me);
                    }
                }
            }catch(Exception $e){
                  $er = "Can not set the access token";
                  $this->setErrorMessage($er);
            }
            
          }else{  
            $er = "No Session available from Facebook !";
            $this->setErrorMessage($er);
          }
      }catch(Exception $e){
          $this->setErrorMessage($e);
      }
      
    }*/

    /**
      * Function to check if the user is logged in facebook
      * If logged in then set the facebook user id in fb_uid
      * Using the v3.1.1 of FB SDK and auth 
    */

    function isLoggedInFacebook(){
        $fb_user_id = 0;
        if($this->fbobj->getAccessToken() != ''){
            $this->setAccessToken($this->fbobj->getAccessToken);
            $fb_user_id = $this->fbobj->getUser();
            if($fb_user_id){
                  $this->setFbUserId($fb_user_id); 
                  if ($user) {
                      try {
                        // Proceed knowing you have a logged in user who's authenticated.
                        $user_profile = $facebook->api('/me');
                        $this->setUserData($user_profile) ;
                      } catch (FacebookApiException $e) {
                        $this->setErrorMessage($e);
                      }
                  }
            }
        }else{
            $er = "Can not set the access token";
            $this->setErrorMessage($er);
        }
    }


    /**
      * Function to get the user info from facebook
      * NOTE : The method is a conventional way of calling the API and then get the details
               This method can be avoided by using the method isLoggedInFacebook()
      * @see isLoggedInFacebook()
      * Will keep the method till next version.
    */
    function getUserInformation(){
        if($this->fb_session != ''){
           $permissionCheck = split(",",$this->permission);
            $a = $this->getAccessToken();
            if(!$a)
            {
                $er = "Permission Disallow!";
                $this->setErrorMessage($er);  
                
            }else{
                $user = json_decode(@file_get_contents('https://graph.facebook.com/me?'.$a));
                //echo 'https://graph.facebook.com/me?'.$a;
                $Result["UserID"]      = $user->id;
                $Result["Name"]        = $user->name;
                $Result["FirstName"]   = $user->first_name;
                $Result["LastName"]    = $user->last_name;
                $Result["ProfileLink"] = $user->link;
                $Result["ImageLink"] = "<img src='https://graph.facebook.com/".$user->id."/picture' />";
                $Result["About"]       = $user->about;
                $Result["Quotes"]      = $user->quotes;
                $Result["Gender"]      = $user->gender;
                $Result["TimeZone"]    = $user->timezone;
                if(in_array("email",$permissionCheck))
                {
                    $Result["Email"]       = $user->email;
                }
                if(in_array("user_birthday",$permissionCheck))
                {
                    $Result["Birthday"]    = $user->birthday;
                }
                if(in_array("user_location",$permissionCheck))
                {
                    $Result["PermanentAddress"]    = $user->location->name;
                    $Result["CurrentAddress"]    = $user->hometown->name;
                }
                return $Result;
            }
        }
    }
 
    /**
      * Function publishStream()
      * @param array $data
      * The data array in the form
      * $data = array("message"=>"Message goes here","picture"=>$vars,"link"=>"URL for the name parameter to point to, you can remove this parameter if you wish",
      *              "name"=>"The name param, optional","description"=>"The post description, once again optional")
      * @see http://developers.facebook.com/docs/reference/rest/stream.publish/
    */
    function publishStream($data){ 
          if(is_array($data) && count($data) > 0 ){
              $stream_array = $data;
          }
          if( $this->checkIfPermissionGiven('publish_stream') === true) {
              try{
                  $result =$this->fbobj->api(
                                          '/me/feed/',
                                          'post',
                                          $stream_array
                                        );
              }catch(Exception $e){
                    $er = 'Error from Facebook .'.$e ;
                    $this->setErrorMessage($er);
              }
          }else{
              $er = 'Could not be posted on Facebook ! You must give permission to post feed via this application' ;
              $this->setErrorMessage($er);
          }
    }


    /**
      * Function to check if the connected user is a FB page admin
      * @param integer $page_id
      * @return boolean
    */
    function isPageAdmin($page_id){
        $return = false ;
        if($this->getFbSession()!= ''){ 
            $pagelist = $this->fbobj->api("/me/accounts");
            if(is_array($pagelist['data']) && count($pagelist['data']) > 0 ){
                foreach($pagelist['data'] as $page){
                    if($page['id'] == $page_id){
                        $return = true;
                        break;
                    }
                }
            }
        }
        return $return ;
    }
    

    /**
      * Function to post stream on the fanpage 
      * @param integer $page_id
      * @param array $data
      * $data = array("message"=>"Message goes here","picture"=>$vars,"link"=>"URL for the name parameter to point to, you can remove this parameter if you wish",
      *              "name"=>"The name param, optional","description"=>"The post description, once again optional")
      * @see http://developers.facebook.com/docs/reference/rest/stream.publish/
    */
    function publishStreamOnFanPage($page_id,$data){
        $is_admin = $this->isPageAdmin($page_id) ;
        if($this->getFbSession()!= ''){ 
            if($is_admin === true){
                if(is_array($data) && count($data) > 0 ){
                    $stream_array = $data;
                }
                try{
                    $result = $this->fbobj->api('/'.$page_id.'/feed', 'post', $stream_array);
                }catch(Exception $e){
                    $er = 'Error from Facebook .'.$e ;
                    $this->setErrorMessage($er);
                }
            }else{
              $er = 'You are not admin of the Scamexposed Fan page to perform this operation';
              $this->setErrorMessage($er);
            }
        }
    }
   
    /**
      * Method to get the permission list for the user
      * @param string $permission_name
      * @return boolean
      * @see http://developers.facebook.com/docs/reference/api/
    */

    function checkIfPermissionGiven($permission_name){
        $permissions = $this->fbobj->api('/me/permissions');
        if( array_key_exists($permission_name, $permissions['data'][0]) ) {
            return true;
        }else{
            return false ;
        }
    }
    
}
?>