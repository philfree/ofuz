<?php 
// Copyright 2001 - 2010 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * Class User
   * This class manage most of the Action and date relate to users using Ofuz
   *
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @package OfuzCore
   * @license ##License##
   * @version 0.6
   * @date 2010-09-06
   * @since 0.1
   */

class User extends RegisteredUser {
    public $table = "user";
    protected $primary_key = "iduser";

    private $field_username = "username";
    private $field_password = "password";
    private $field_email = "email";
    public $is_mobile = false;
    public $global_fb_connected = false;
    public $set_user_in_session = false;
    public $user_search_txt = '';
    private $tag_list_html = '';
    public $rest_apikey_gen_secret_key = '';

    public $savedforms = Array (
                    "UserRegistration" => "OfuzRegistrationForm",
                    "UserWithOpenIdRegistration" => "OfuzOpenIdRegistrationForm",
                    "LoginWithOpenId" => "openid_loginForm");
                    
    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(RADRIA_LOG_RUN_OFUZ);
       $this->rest_apikey_gen_secret_key = OFUZ_RESTAPIKEY_GEN_SECRECT_KEY ;
    }

    /**
      Get the full name of the User.
      @param int $iduser 
      @return fullname of the user
    */

    function getFullName($iduser=null){
      if (is_null($iduser)) {
        $fullname = $this->firstname.' '.$this->lastname;
      } else {
        $q = new sqlQuery($this->getDbCon());
        $q->query("select firstname,lastname from user where iduser = ".$iduser) ;
        while($q->fetch()){
            $fullname = $q->getData("firstname").' '.$q->getData("lastname");
        }
      }
      return $fullname;
    }

    /**
      * Get the Email Id of the User.
      * @param iduser -- INT
      * @return email id of the user
    */
    
    function getEmailId($iduser){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select email from user where iduser = ".$iduser) ;
        $q->fetch();
        return $q->getData("email");
    }
      
    /**
      * Get user data by email
      * @param string $email
    */
    function getUserDataByEmail($email){
        $this->query("select * from ".$this->table." where email = '".$email."'" );
        $this->getValues();
    }


    /**
      * Event method for signin
      * @param $eventControler -- Object
    */
    function eventSignIn(EventControler $eventControler) {
       parent::eventSignIn($eventControler) ;
       if ($eventControler->goto == "i_contacts.php") {
            $this->is_mobile = true;
       }
    }

    /**
      * Method to get is using mobile device
      * @return member var is_mobile
    */
    function getIsMobile() {
        return $this->is_mobile;
    }

    /**
      * Registration form generation from the Ofuz Invitation URL
      * @param string $thankyoupage 
      * @param string $emailtemplate 
      * @param string $admin_emailttemplate
      * @param string $adminemail 
      * @param string $key
      * @param string $id
      * @param string $errorpage 

      @return the form to register
    */
    function formRegisterInvitation($thankyoupage, $emailtemplate, $admin_emailttemplate, $adminemail,$key,$id,$errorpage) {
        $f_regForm = $this->prepareSavedForm("OfuzInvitationRegistrationForm");
        //$errorpage = $errorpage."?key=".$key."&ref=".$id_rel; 
        if(strlen($emailtemplate)  > 0) {
            $f_regForm->addEventAction($this->getObjectName()."->eventSendWelcomeEmail", 1012);
            $f_regForm->addParam("emailtemplate_registration", $emailtemplate);
            $f_regForm->addParam("emailtemplate_registration_admin", $admin_emailttemplate);
            $f_regForm->addParam("email_admin", $adminemail);
        }
        $f_regForm->addParam("errorpage", $errorpage);
        $f_regForm->addParam("key", $key);
        $f_regForm->addParam("id", $id);
        $f_regForm->addParam("successpage", $thankyoupage);

        $f_regForm->addEventAction($this->getObjectName()."->eventRegistrationValidation", 101) ;
        $f_regForm->setFormEvent($this->getObjectName()."->eventNewInvitedUserAdd", 5050) ;
        //$f_regForm->setFormEvent($this->getObjectName()."->eventSetUserRelation", 5051) ;
        //$f_regForm->setUrlNext($thankyoupage);
        $f_regForm->setForm();
        $f_regForm->execute();
    }

    
    /**
      * Adding users from the Invitaion URL and setting the Co-Worker 
      * relation between the invitee and the invitor
      * @param $evtcl -- Object
    */
    function eventNewInvitedUserAdd(EventControler $evtcl){
      $fields = $evtcl->fields;
      if($fields["firstname"] == '' || $fields["lastname"] =='' || $fields["email"] == '' || $fields["username"]==''){
          $msg = "You must fill the require fields";
          $errorpage = $evtcl->errorpage;
          $dispError = new Display($errorpage) ;
          $dispError->addParam("id", $evtcl->id) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;
      }else{
          if($evtcl->validation_fail == 'No'){
          $q = new sqlQuery($this->getDbCon());
          $q->query("select * from user where email = '".$fields["email"]."'");
				
				
          $q1 = new sqlQuery($this->getDbCon());
          $q1->query("select * from user where username = '".$fields["username"]."'");
				
          if ($q1->getNumRows() > 0) {
              $msg = "Username is already in use";
              $errorpage = $evtcl->errorpage;
              $dispError = new Display($errorpage) ;
              $dispError->addParam("id", $evtcl->id) ;
              $dispError->addParam("message", $msg) ;
              $evtcl->setDisplayNext($dispError) ;
              }elseif($q->getNumRows() > 0){
              $msg = "reg_duplicate_email";
              $errorpage = $evtcl->errorpage;
              $dispError = new Display($errorpage) ;
              $dispError->addParam("id", $evtcl->id) ;
              $dispError->addParam("message", $msg) ;
              $evtcl->setDisplayNext($dispError) ;
          }else{
					//Add the user
              include_once("class/UserRelations.class.php");
              $do_user_rel = new UserRelations();
              $_SESSION['do_User']->firstname = $fields["firstname"];
              $_SESSION['do_User']->lastname = $fields["lastname"];
              $_SESSION['do_User']->email = $fields["email"];
              $_SESSION['do_User']->username = $fields["username"];
              $_SESSION['do_User']->company = $fields["company"];
              $_SESSION['do_User']->regdate = date("Y-m-d");
              //$_SESSION['do_User']->password = $do_user_rel->encrypt($fields["password"]);
              $_SESSION['do_User']->password = $fields["password"];
              $_SESSION['do_User']->plan = "free";
              $_SESSION['do_User']->status = "active";
              $_SESSION['do_User']->add();
            
              $last_id = $this->getInsertId($this->table, $this->primary_key);
		
              // Update the user_relations
              $do_user_rel = new UserRelations();
              $iduser_relations = $do_user_rel->decrypt($evtcl->id);
              $do_user_rel->getId($iduser_relations);
              $do_user_rel->idcoworker = $last_id;
              $do_user_rel->enc_email =  $do_user_rel->encrypt($fields["email"]);
              $do_user_rel->accepted = 'Yes';
              $id_sender = $do_user_rel->iduser;
              $do_user_rel->update();
              
              $do_user_rel_rev = new UserRelations();
              $do_user_rel_rev->addNew();
              $do_user_rel_rev->accepted = 'Yes';
              $do_user_rel_rev->iduser =  $last_id;
              $do_user_rel_rev->idcoworker =$id_sender;
              $do_user_rel_rev->add();
              //Set the session variable
              $userdata = Array();
              $userdata['id'] = $last_id;
              $userdata['firstname'] = $fields["firstname"];
              $userdata['lastname'] = $fields["lastname"];
              $userdata['email'] = $fields["email"];
              $userdata['username'] = $this->{$this->getUsernameField()};
              $userdata['user_table'] = $this->table;
              $_SESSION['userdata'] = $userdata;
              if (!$this->isPersistent()) {
                $this->sessionPersistent("do_".$this->getObjectName(), "signout.php", 36000);
              }

              // Send an email to the sender that the Co-Worker has registered
              $do_user_rel->sendEmailOnCoWorkerRegistration($id_sender, $_SESSION['do_User']);
              //Redirect the page
              $errorpage = $evtcl->successpage;
              $dispError = new Display($errorpage) ;
              $evtcl->setDisplayNext($dispError) ;
              }
            }
        }
    }


    /**
      * Method to check if the user name is a duplicate one during registration
      * @param  $username -- STRING
      * @return Boolean
    */
    public function checkDuplicateUserName($username){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from user where username = '".$username."'");
        if($q->getNumRows()){
            return true ;
        }else{
            return false ;
        }
    }
    

    /**
      * Event to generate the drop_box_code
      * @param $event_controler -- Object
    */
    function eventGenerateDropBoxId(EventControler $event_controler){
      $drop_box_code = $this->generateRandomDropBoxId();
      $q = new sqlQuery($this->getDbCon());
      $q->query("update ".$this->table." set drop_box_code = ".$drop_box_code." where ".$this->primary_key." = ".$_SESSION['do_User']->iduser);
      //echo "update user set drop_box_code = ".$drop_box_code." where iduser = ".$_SESSION['do_User']->iduser ;exit;
      $_SESSION['do_User']->drop_box_code = $drop_box_code;
    } 

    /** 
     * Generate the Random Drop box id
     * @return $drop_box_code
     */

    function generateRandomDropBoxId(){
      $drop_box_code = rand(0, pow(10, 5));
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from ".$this->table." where drop_box_code = ".$drop_box_code);
      if ($q->getNumRows()) { 
        $i = 1;
        $drop_box_code = $this->generateRandomDropBoxId();
      }elseif(strlen($drop_box_code < 5)){
          $drop_box_code = $this->generateRandomDropBoxId();
      }
      return $drop_box_code;
    }

    
    /**
      * Function to get Userid from drop box code
      * @param string $drop_box_code
      * @return iduser on found else boolean false
    */
     function getUserFromDropBox($drop_box_code){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select iduser from ".$this->table." where drop_box_code = ".$drop_box_code);
        if($q->getNumRows() > 0 ){
            $q->fetch();
            return $q->getData("iduser");
        }else{
            return false ;
        }
     }


    /**
     * Overriding the method formLogin() in Parent class
     * @param string $nextPage 
     * @param string $strWrongLoginPassword
     * @param string $login_form_style 
     * @param string $errPage

     * @return the login form
    */

    function formLogin( $nextPage, 
                        $strWrongLoginPassword, 
                        $login_form_style="",
                        $errPage="") {

        if (empty($login_form_style)) { $login_form_style = "formfield"; }
        if (empty($errPage)) { $errPage =  $_SERVER['PHP_SELF']; }
        if (empty($strWrongLoginPassword)) { $strWrongLoginPassword =_("Wrong Username or Password"); }
        $form_fields = new Fields();
        
        $field_username = new FieldTypeChar($this->getUsernameField());
        $field_username->label = _("User name");
        $field_username->size = 20;
        $field_username->maxlenght = 40;
        $field_username->css_form_class = "formfield";		
        
        $form_fields->addField($field_username);
        
        $field_password = new OfuzFieldTypePassword($this->getPasswordField());         
        $field_password->label = _("Password");
        $field_password->size = 20;  	
        $field_password->maxlenght = 40; 
        $field_password->loginform = true;
    
        $form_fields->addField($field_password);
                
        $this->setFields($form_fields) ;
        $login_form = $this->prepareForm();	 
        $login_form->setFormEvent($this->getObjectName()."->eventCheckIdentification");
        $login_form->addParam("goto",$nextPage);
        $login_form->addParam("errPage", $errPage);
        $login_form->setForm();
        $login_form->execute();
    }


    /**
     *************Overriding the method eventGetForgotPassword in Parent class to send decrypt password****************
     * Event registration.getForgotPassword
     * Returns the username and password for the
     * email id specified
     * @package registration
     * @author Philippe Lewicki, Abhik
     * @version 2.0
     */
    function eventGetForgotPassword(EventControler $eventControler) {

        $disp = new Display($eventControler->message_goto) ;

        $this->setLog("\n (".$this->getObjectName().") Get Forgot Password ".date("Y/m/d H:i:s"));
        $conx = $this->getDbCon();
        $fields = $eventControler->fields;
        $useremail = $fields[$this->getEmailField()];
        
        include_once("class/Emailer.class.php") ;
        
        $qGetPass = new sqlQuery($this->getDbCon()) ;
        $qGetPass->query("select * from `".$this->getTable()."` where `".$this->getEmailField()."`='".$useremail."'");
        $do_user_rel = new UserRelations();
        if ($qGetPass->getNumRows() > 0) {
            while($dPass = $qGetPass->fetchArray()) {
                $email = new Emailer() ;
                $email->loadEmailer($this->getDbCon(), 'forgotpassword') ;
                $dPass["password"] = $do_user_rel->decrypt($dPass["password"]);
                $email->mergeArray($dPass) ;
                if ($email->hasHtml()) {
                    $email->sendMailHtml($dPass[$this->getEmailField()]) ;
                } else {
                    $email->sendMailStandard($dPass[$this->getEmailField()]) ;
                }
                $disp->addParam("message", _("Your password has been sent to: ").$useremail) ;
            }
        } else {
            $disp->addParam("message", _("No user found with that email address")) ; 
        }
        $eventControler->setDisplayNext($disp) ;
    }

    /**
      * Login event method checks different identification
      * @param $eventControler -- Object
    */
    function eventCheckIdentification(EventControler $eventControler) {
        $login_error = false;
        setcookie("ofuz", "1", time()+25920000);
        $this->setLog("\n (User) Registration Sign on ".date("Y/m/d H:i:s"));
        $conx = $this->getDbCon();
        $strWrongLoginPassword = $eventControler->strWrongLoginPassword;
        if (strlen($eventControler->password_field)>0) {
            $password_field = $eventControler->password_field;
            $this->setPasswordField($eventControler->password_field);
        } else {
            $password_field = $this->getPasswordField();
        }
        if (strlen($eventControler->username_field)>0) {
            $username_field = $eventControler->username_field;
            $this->setUsernameField($eventControler->username_field);
        } else {
            $username_field = $this->getUsernameField();
        }
        $fields = $eventControler->fields;
        $auth_username = $fields[$username_field];
        $auth_password = $fields[$password_field];
        
        // Changes made to encrypt the password before looking in the DB
        $do_user_rel = new UserRelations();
        $auth_password  = $do_user_rel->encrypt($auth_password);

        $goto = $eventControler->goto;

        if (empty($strWrongLoginPassword)) {
            $strWrongLoginPassword = _('Wrong login or password');
        }

        if (strlen($auth_username) > 0 && strlen($auth_password) > 0) {

            $this->setLog("\n(User) database: ".$conx->db.", table:".$this->getTable());
            $this->query("select * from `".$this->getTable()."` 
                          where `".$this->getUsernameField()."`='".$this->quote($auth_username)."' 
                          and `".$this->getPasswordField()."`='".$this->quote($auth_password)."'") ;
            $this->setLog("\n(User) Query executed for sign on:".$this->sql_query);
            
            if ($this->getNumrows() == 1) {
                if(isset($_SESSION["google"]["openid_identity"])) {
                  $this->setGoogleOpenIdIdentity($this->iduser);
                }

                if ($this->status == 'active') {				
                        $do_login_audit = new LoginAudit();
                        if($this->fb_user_id){// IS a FB connected User
                          if($this->email == ''){ // Oups!!!! no email id then you must login with facebook
                                $login_error = true;
                                $msg = _('Seems like you have registered through facebook. Please login with facebook !');
                          }else{
                                $this->setSessionVariable();// Ok you are smart you set an email id also !!!
                                $do_login_audit->do_login_audit();
                          }
                        }else{ 
                                $this->setSessionVariable(); 
                                $do_login_audit->do_login_audit();  
                        } // There you are a general user you can try our FB connent !!!
                    /* Scope to check other login features 
                          Ex: $other_id = $this->otherMethod();
                          We can also change the $msg to class var to hold a message from a message array
                    */
                        if($login_error){ //echo $eventControler->$errPage;
                          $err_disp = new Display($eventControler->errPage);
                          $_SESSION['crdmsg'] = $msg;
                          $err_disp->addParam("message", $msg);
                          $eventControler->setDisplayNext($err_disp);
                        }else{ //echo '2';
                            // check if the user has contacts
                                // if not redirect to welcome_to_ofuz.php
                                $contacts = $this->getChildContact();
                                if ($contacts->getNumrows() < 2) {
                                        if($eventControler->goto == 'settings_myinfo.php'){
                                              $eventControler->setDisplayNext(new Display("/settings_myinfo.php")) ;
                                        }else{
                                            $eventControler->setDisplayNext(new Display("/welcome_to_ofuz.php")) ;
                                        }
                                } else {
                                        $eventControler->setUrlNext($eventControler->goto) ;
                                }
                        }
                } else {
                        $err_disp = new Display($eventControler->errPage);
                        $msg = _("Your account is not currently active, contact our tech support at ".$GLOBALS['cfg_ofuz_email_support']);
                        $_SESSION['crdmsg'] = $msg;
                        $err_disp->addParam("message", $msg);
                        $eventControler->setDisplayNext($err_disp) ;
                }
                
            } else { //echo '3';
                $err_disp = new Display($eventControler->errPage);
                $msg = _("Wrong Login !");
                $err_disp->addParam("message", $msg);
                $eventControler->setDisplayNext($err_disp) ;
            }
        }
    }

    /**
      * Method to set the User Object in the persistent Method
    */
    function setSessionVariable() {
      $this->getId($this->iduser);
      $this->sessionPersistent("do_User", "signout.php", OFUZ_TTL_LONG);
     
    }

    /**
      * Login verification with Facebook
      * @param $evtcl -- Object
    */
    function eventLoginFb(EventControler $evtcl) { 
        if($this->checkValidUserFb($evtcl->fbuid)){
          if ($this->status == 'active') {
              $this->setSessionVariable();
              $this->global_fb_connected = true;
              $do_login_audit = new LoginAudit();
              $do_login_audit->do_login_audit("Facebook");  
              $evtcl->setUrlNext($evtcl->nextPage) ;
          }else{
              $err_disp = new Display('error.php');
              $msg = _("Your account is not currently active, contact our tech support at ".$GLOBALS['cfg_ofuz_email_support']);
              $_SESSION['errorMessage'] = $msg;
              $eventControler->setDisplayNext($err_disp) ; 
          }
        }else{ 
          $err_disp = new Display($evtcl->errPage);
          $msg = "Please validate yourself if you are an existing Ofuz user or else continue registration.";
          $err_disp->addParam("message", $msg);
          $evtcl->setDisplayNext($err_disp);

        }
    }

    /**
      * Check if the fbid is a valid id from the user table
      * @param $fbuid -- INT
      * @return iduser if found else return false
    */
    function checkValidUserFb($fbuid){
        $this->query("select * from `".$this->table."` 
                          where `fb_user_id`=".$fbuid) ;
//         echo "select * from `".$this->table."` 
//                           where `fb_user_id`=".$fbuid ;
        if ($this->getNumRows() >0) {
            return  $this->iduser;
        }else{
            return false;
        }
    }

    /**
      * While general registered user logs in for the first time with Facebook ask for 
      * login validation so that the fb_user_id can be stored in the DB
      @param string $goto 
      @param string $errPage
      @Param integer $fbid

      @return the form for FB validation
    */
    function formFBLoginVerification($goto,$errPage,$fbid){
		
        $form_fields = new Fields("",$this->getDbCon());
        $field_username = new FieldTypeChar($this->getUsernameField());
        $field_username->label = _("User name");
        $field_username->size = 20;
        $field_username->maxlenght = 40;
        $field_username->css_form_class = "formfield";		
        
        $form_fields->addField($field_username);
        
        $field_password = new OfuzFieldTypePassword($this->getPasswordField());         
        $field_password->label = _("Password");
        $field_password->size = 20;  	
        $field_password->maxlenght = 40;	 
        $field_password->loginform = true;
    
        $form_fields->addField($field_password);
                
        $this->setFields($form_fields) ;
        $login_form = $this->prepareForm();	 
        $login_form->setFormEvent($this->getObjectName()."->eventCheckIdentificationOnFbLogin");
        $login_form->addParam("goto",$goto);
        $login_form->addParam("errPage", $errPage);
        $login_form->addParam("fbid", $fbid);
        $login_form->setForm();
        $login_form->execute();
    }

    /**
      * Event method to set the Identification after FB login and user details
      * verification
      * @param object $evtcl 
    */
    function eventCheckIdentificationOnFbLogin(EventControler $evtcl) { 
        setcookie("ofuz", "1", time()+25920000);
        if($evtcl->fbid && $evtcl->fbid !=''){
            if (strlen($evtcl->password_field)>0) {
                $password_field = $evtcl->password_field;
                $this->setPasswordField($evtcl->password_field);
            } else {
                $password_field = $this->getPasswordField();
            }
            if (strlen($evtcl->username_field)>0) {
                $username_field = $evtcl->username_field;
                $this->setUsernameField($evtcl->username_field);
            } else {
                $username_field = $this->getUsernameField();
            }
            $fields = $evtcl->fields;
            $auth_username = $fields[$username_field];
            $auth_password = $fields[$password_field];
            // Changes made to encrypt the password before looking in the DB
            $do_user_rel = new UserRelations();
            $auth_password  = $do_user_rel->encrypt($auth_password);

            $this->query("select * from `".$this->table."` 
                              where `username`='".$this->quote($auth_username)."' 
                              and `password`='".$this->quote($auth_password)."'") ;
            if ($this->getNumrows() == 1) { 
              if ($this->status == 'active') {
                  $this->query("update user set fb_user_id = ".$evtcl->fbid." where iduser=".$this->iduser);
                  $this->setSessionVariable();
                  $this->global_fb_connected = true;
                  $do_login_audit = new LoginAudit();
                  $do_login_audit->do_login_audit("Facebook");  
                  $evtcl->setUrlNext($evtcl->goto) ;
              }else{
                  $err_disp = new Display($evtcl->errPage);
                  $msg = _("Your account is not currently active, contact our tech support at ".$GLOBALS['cfg_ofuz_email_support']);
                  $_SESSION['crdmsg'] = $msg;
                  $err_disp->addParam("message", $msg);
                  $eventControler->setDisplayNext($err_disp) ; 
              }
            }else{ 
              $err_disp = new Display($evtcl->errPage);
              $msg = "Sorry! But there is no user found with the supplied details.";
              $err_disp->addParam("message", $msg);
              $evtcl->setDisplayNext($err_disp) ;
            }
        }else{
            $err_disp = new Display($evtcl->errPage);
            $msg = "Sorry! But seems like you are not connected to facebook. Please connect first.";
            $err_disp->addParam("message", $msg);
            $evtcl->setDisplayNext($err_disp) ;
        }
    }

    /**
      * New User Registration with Facebook
      * @param object $evtcl 
    */
    function eventRegNewFbUser(EventControler $evtcl) {
        //echo $evtcl->goto;
        if($evtcl->fbuid && $evtcl->fbuid != ''){
          if($evtcl->emailid == ''){
            $err_disp = new Display($evtcl->errPage);
            $msg = "Please enter a valid email id.";
            $err_disp->addParam("message", $msg);
            $evtcl->setDisplayNext($err_disp) ;
          }elseif($this->checkValidUserFb($evtcl->fbuid)){
              $err_disp = new Display($evtcl->errPage);
              $msg = "Sorry! But it seems like you already registered, so please verify youself instead.";
              $err_disp->addParam("message", $msg);
              $evtcl->setDisplayNext($err_disp) ;
          }else{
              $this->firstname = $evtcl->fname;
              $this->lastname = $evtcl->lname;
              $this->company = $evtcl->comp;
              $this->position = $evtcl->position;
              $this->fb_user_id = $evtcl->fbuid;
              $this->email = $evtcl->emailid;
              $this->plan = 'free';
              $this->status = 'active';
              $this->add();
              $this->iduser = $this->getPrimaryKeyValue();
              $this->setSessionVariable();
              $this->global_fb_connected = true;
              $text = '';
              $text .= 'Hi '.$this->firstname.'  </br >';
              $text .= 'Welcome to Ofuz. Thanks for logging in with facebook connect. You can login to ofuz with your facebook login, also you will not need any username and password for login to ofuz, all you need to login with facebook connect button. How ever you can set one username as well from the setting and at any point of time can use the general login instead of facebook connect.<br /><br />';
              
              $text .= 'Thank You !<br /> Ofuz Team';
              $do_template = new EmailTemplate();
              $do_template->senderemail = "support@sqlfusion.com";
              $do_template->sendername = "Ofuz";
              $do_template->subject = "Welcome To Ofuz";
              $do_template->bodytext = $text;
              $do_template->bodyhtml = $do_template->bodytext;
              $this->sendMessage($do_template);
              $evtcl->setUrlNext("welcome_to_ofuz.php") ;
          }
        }else{
            $err_disp = new Display($evtcl->errPage);
            $msg = _('Sorry! But seems like you are not connected to facebook. Please connect first.');
            $err_disp->addParam("message", $msg);
            $evtcl->setDisplayNext($err_disp) ;
        }
    }

    /**
      * While general registered user logs in for the first time with Twitter ask for 
      * login validation so that the fb_user_id can be stored in the DB
      * @param string $goto
      * @param string $errPage
      * @param string $tw_user_id
      * @param string $tw_screen_name
      * @param string $tw_token
      * 
      * @return the form for Twitter validation
      */
    function formTwLoginVerification($goto,$errPage,$tw_user_id,$tw_screen_name,$tw_token){
		
        $form_fields = new Fields('',$this->getDbCon());
        $field_username = new FieldTypeChar($this->getUsernameField());
        $field_username->label = _('User name');
        $field_username->size = 20;
        $field_username->maxlenght = 40;
        $field_username->css_form_class = 'formfield';		
        
        $form_fields->addField($field_username);
        
        $field_password = new OfuzFieldTypePassword($this->getPasswordField());         
        $field_password->label = _('Password');
        $field_password->size = 20;  	
        $field_password->maxlenght = 40;	 
        $field_password->loginform = true;
    
        $form_fields->addField($field_password);
                
        $this->setFields($form_fields) ;
        $login_form = $this->prepareForm();	 
        $login_form->setFormEvent($this->getObjectName().'->eventCheckIdentificationOnTwLogin');
        $login_form->addParam('goto',$goto);
        $login_form->addParam('errPage', $errPage);
        $login_form->addParam('tw_user_id', $tw_user_id);
        $login_form->addParam('tw_screen_name', $tw_screen_name);
        $login_form->addParam('tw_token', $tw_token);
        $login_form->setForm();
        $login_form->execute();
    }

    /**
      * Event method to set the Identification after Twitter login and user details
      * verification
      * @param $evtcl -- Object
    */
    function eventCheckIdentificationOnTwLogin(EventControler $evtcl) { 
        setcookie('ofuz', '1', time()+25920000);
        if ($evtcl->tw_user_id && $evtcl->tw_user_id !='' &&
            $evtcl->tw_screen_name && $evtcl->tw_screen_name !='' && 
            $evtcl->tw_token && $evtcl->tw_token !='') {
            if (strlen($evtcl->password_field)>0) {
                $password_field = $evtcl->password_field;
                $this->setPasswordField($evtcl->password_field);
            } else {
                $password_field = $this->getPasswordField();
            }
            if (strlen($evtcl->username_field)>0) {
                $username_field = $evtcl->username_field;
                $this->setUsernameField($evtcl->username_field);
            } else {
                $username_field = $this->getUsernameField();
            }
            $fields = $evtcl->fields;
            $auth_username = $fields[$username_field];
            $auth_password = $fields[$password_field];
            // Changes made to encrypt the password before looking in the DB
            $do_user_rel = new UserRelations();
            $auth_password  = $do_user_rel->encrypt($auth_password);

            $this->query("select * from `".$this->table."` 
                              where `username`='".$this->quote($auth_username)."' 
                              and `password`='".$this->quote($auth_password)."'") ;
            if ($this->getNumrows() == 1) { 
              if ($this->status == 'active') {
                  $this->query("INSERT INTO twitter_account (iduser, tw_user_id, tw_screen_name, tw_token) VALUES (".$this->iduser.",'".$evtcl->tw_user_id."','".$evtcl->tw_screen_name."','".$evtcl->tw_token."')");
                  $this->setSessionVariable();
                  $do_login_audit = new LoginAudit();
                  $do_login_audit->do_login_audit('Twitter');  
                  $evtcl->setUrlNext($evtcl->goto) ;
              }else{
                  $err_disp = new Display($evtcl->errPage);
                  $msg = _("Your account is not currently active, contact our tech support at ".$GLOBALS['cfg_ofuz_email_support']);
                  $_SESSION['crdmsg'] = $msg;
                  $err_disp->addParam("message", $msg);
                  $eventControler->setDisplayNext($err_disp) ; 
              }
            }else{ 
              $err_disp = new Display($evtcl->errPage);
              $msg = "Sorry! But there is no user found with the supplied details.";
              $err_disp->addParam("message", $msg);
              $evtcl->setDisplayNext($err_disp) ;
            }
        }else{
            $err_disp = new Display($evtcl->errPage);
            $msg = "Sorry! But seems like you are not connected to Twitter. Please connect first.";
            $err_disp->addParam("message", $msg);
            $evtcl->setDisplayNext($err_disp) ;
        }
    }

    /**
      * New User Registration with Twitter
      * @param object $evtcl
    */
    function eventRegNewTwUser(EventControler $evtcl) {
        if($evtcl->tw_user_id && $evtcl->tw_user_id != ''){
          $do_twitter = new OfuzTwitter();
          if($evtcl->emailid == ''){
            $err_disp = new Display($evtcl->errPage);
            $msg = "Please enter a valid email id.";
            $err_disp->addParam("message", $msg);
            $evtcl->setDisplayNext($err_disp) ;
          }elseif($do_twitter->getOfuzUserID($evtcl->tw_user_id)){
              $err_disp = new Display($evtcl->errPage);
              $msg = "Sorry! But it seems like you already registered, so please verify youself instead.";
              $err_disp->addParam("message", $msg);
              $evtcl->setDisplayNext($err_disp) ;
          }else{
              $this->firstname = $evtcl->firstname;
              $this->lastname = $evtcl->lastname;
              $this->email = $evtcl->emailid;
              $this->plan = 'free';
              $this->status = 'active';
              $this->add();
              $this->iduser = $this->getPrimaryKeyValue();
              $this->setSessionVariable();

              $do_twitter->setAccessToken($evtcl->tw_user_id, $evtcl->tw_screen_name, $evtcl->tw_token);

              $text = '';
              $text .= 'Hi '.$this->firstname.'  </br >';
              $text .= 'Welcome to Ofuz. Thanks for logging in with Twitter. You will not need to use a username and password to login to Ofuz; all you need is the "Sign in with Twitter" button. However, you can set one username as well from the Settings menu, and then you can use the general login instead of Twitter.<br /><br />';
              $text .= 'Thank You !<br /> Ofuz Team';
              $do_template = new EmailTemplate();
              $do_template->senderemail = "support@sqlfusion.com";
              $do_template->sendername = "Ofuz";
              $do_template->subject = "Welcome To Ofuz";
              $do_template->bodytext = $text;
              $do_template->bodyhtml = $do_template->bodytext;
              $this->sendMessage($do_template);
              $evtcl->setUrlNext("welcome_to_ofuz.php") ;
          }
        }else{
            $err_disp = new Display($evtcl->errPage);
            $msg = _('Sorry! But seems like you are not connected to Twitter. Please connect first.');
            $err_disp->addParam("message", $msg);
            $evtcl->setDisplayNext($err_disp) ;
        }
    }

    /**
      * Send email for the User Object
      * @param $template -- STRING
      * @param $values -- Array
      * @return Boolean
    */
    function sendMessage($template, $values=Array()) {
        if (!is_object($template)) { return false; }
        if (empty($values)) { $values = $this->getValues(); }
        $message_sent = false;
        if(strlen($this->email)>4){
          //Use for sending email here for general users
          $emailer = new Radria_Emailer('UTF-8');
          $emailer->setEmailTemplate($template);
          $emailer->mergeArray($values);//required even if there is nothig to merge
          $emailer->addTo($this->email);
          $emailer->send();
          $emailer->cleanup();
          $message_sent = true;
          
        }elseif($this->fb_user_id && $this->global_fb_connected){ //echo 'here';
            //echo $template->subject;
            include_once 'facebook_client/facebook.php';
            include_once 'class/OfuzFacebook.class.php';
            $facebook = new Facebook(FACEBOOK_API_KEY, FACEBOOK_APP_SECRET);
            //$do_ofuz_fb =  new OfuzFacebook($facebook);
            try{
              //$facebook->api_client->notifications_sendEmail($this->fb_user_id, $template->subject,'', $template->bodyhtml); 
              $message_sent = true;
            }catch(Exception $e){}
        }
        if ($message_sent) {
                return true;
        } else { 
                return false; 
        }	
    }

     /** 
      *   eventSendPortalAlert
      *   This event is triggered when adding a note in a contact 
	  *   It will send a copy of the note the contact.
      */

     function eventSendPortalAlert(EventControler $event_controler) {
        $this->setLog("eventSendPortalAlert starting (".date("Y/m/d H:i:s").")");
        $do_contact_email = $_SESSION['do_contact']->getChildContactEmail();
          $email_to = $do_contact_email->getDefaultEmail();
        $this->setLog("\nDocument in note: ".$_SESSION['ContactNoteEditSave']->document);
        if(strlen($_SESSION['ContactNoteEditSave']->document) > 0){ // If a file is attached
        $doc_link = $GLOBALS['cfg_ofuz_site_http_base'].'files/'.$_SESSION['ContactNoteEditSave']->document;
                  $doc_name = $_SESSION['ContactNoteEditSave']->document;
        $this->setLog("\n Document set to: ".$doc_name." url:".$doc_link);
              } else {$doc_name = ''; $doc_link = '';}
        $contact_link = $GLOBALS['cfg_ofuz_site_http_base'].'Contact/'.$_SESSION['do_contact']->idcontact;
        $contact_name = $_SESSION['do_contact']->firstname." ".$_SESSION['do_contact']->lastname;
        $this->setLog("\nWe send a message from:".$email_to);
        if (strlen($email_to) > 4) {
          $template = new EmailTemplate("ofuz portal alert");
          $template->setFrom($email_to,  $contact_name  );
                $content = Array ( 'note_html' => nl2br(htmlentities($_SESSION['ContactNoteEditSave']->note)),
                            'note_text' => $_SESSION['ContactNoteEditSave']->note,
              'doc_name' => $doc_name,
              'doc_link' => $doc_link,
              'contact_link' => $contact_link,
              'contact_name' => $contact_name);
          $this->sendMessage($template, $content);
        }
     } 

    
    /**
      * Method to generate a random password.
      * @param $length -- INT
      * @param $allow -- STRING
    */
    function randomPassword($length, $allow = "abcdefghijklmnopqrstuvwxyz0123456789") {
        $i = 1;
        while ($i <= $length) {
          $max = strlen($allow)-1;
          $num = rand(0, $max);
          $temp = substr($allow, $num, 1);
          $ret = $ret . $temp;
          $i++;
        }
       return $ret;
   }

   /**
     * Event method to search users by name.
     * @param $evtcl -- Object
   */
   function eventSetSearchByName(EventControler $evtcl) { 
      $search_key = $evtcl->search_txt;
      if($search_key == '' || empty($search_key) ){
          $msg = "Please enter some search text";
          $dispError = new Display($evtcl->goto) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;
      }else{
          $this->user_search_txt = $search_key;
          $this->setSqlQuery("select * from ".$this->table." where ( firstname like '%".$search_key."%' OR lastname like '%".$search_key."%') AND iduser <> ".$_SESSION['do_User']->iduser);
      
          $evtcl->setDisplayNext(new Display($evtcl->goto));
      }
   }

   /**
     * Event method to generate the API key
     * @param $evtcl -- Object
   */
   function eventGenerateAPIKey(EventControler $evtcl) {
      if($_SESSION['do_User']->iduser){
          $this->api_key = $this->generateAPIKey();
          $this->updateAPIKey();
      }
   }

   /**
     * Method to auto generate the API key
   */
   function autoGenerateAPIKey() {
      if($_SESSION['do_User']->iduser){
          $this->api_key = $this->generateAPIKey();
          $this->updateAPIKey();
      }
   }
    
   /**
     * method to generate a md5 string for the API Key
     * @return $api_key
   */
   private function generateAPIKey(){
      $secrect_key = $this->rest_apikey_gen_secret_key ;
      $api_key = md5($secrect_key.time());
      return $api_key;
   }

   /**
     * Method to update the API key
   */
   function updateAPIKey(){
        $q = new sqlQuery($this->getDbCon());
        $q->query("update ".$this->table." set api_key = '".$this->api_key. "' Where ".$this->primary_key." = ".$_SESSION['do_User']->iduser. " Limit 1");
   }

   

   /**
     * API usage method for API Key authentication
     * @param $key -- STRING
   */
    function validateAPIKey($key){
       if($key != ''){
            $q = new sqlQuery($this->getDbCon());
            $q->query("select * from ".$this->table ." where api_key = '".$key."'");
            if($q->getNumRows()){ 
               $q->fetch();
               return $q->getData("iduser");
            }else{ return false; }
        }else { return false; }
    }

    /**
      * adding user as a contact
      * @param $evtcl -- Object
    */
    function eventAddUserAsContact(EventControler $evtcl){
	    if($evtcl->validation_fail != 'Yes'){ // Variable set in the method 
		if($_SESSION["do_User"]->iduser) {
			$idcompany = "";
			if($evtcl->fields["company"]) {
				$do_company = new Company();
				$idcompany = $do_company->addNewCompany($evtcl->fields["company"],$_SESSION["do_User"]->iduser);
			}
			$do_contact = new Contact();
			$do_contact->firstname = $evtcl->fields["firstname"];
			$do_contact->lastname = $evtcl->fields["lastname"];
			$do_contact->iduser = $_SESSION["do_User"]->iduser;
			$do_contact->idcompany = $idcompany;
			$do_contact->company = $evtcl->fields["company"];
			$do_contact->add();
			$do_contact->addEmail($evtcl->fields["email"],'Home');
	
			$lastInsertedContId = $do_contact->getPrimaryKeyValue();
	
			$this->getId($_SESSION["do_User"]->iduser);
			$this->idcontact = $lastInsertedContId;
			$this->update();
			$contact_view = new ContactView();
			$contact_view->setUser($_SESSION["do_User"]->iduser);
		    $contact_view->rebuildContactUserTable();
		}
	  }

    }

    /**
      * Method adding user as Contact
      * @param $firstname -- STRING
      * @param $lastname -- STRING
      * @param $company -- STRING
      * @param $email -- STRING
      * @param $iduser -- INT
      * FIXME May be no need to rebuilt the contact view but to enter an entry which is faster
    */
    function addUserAsContact($firstname,$lastname,$company,$email,$iduser){
	  $idcompany = "";
	  if($company!= "" ) {
		  $do_company = new Company();
		  $idcompany = $do_company->addNewCompany($company,$iduser);
	  }
	  $do_contact = new Contact();
	  $do_contact->firstname = $firstname;
	  $do_contact->lastname = $lastname;
	  $do_contact->iduser = $iduser;
	  $do_contact->idcompany = $idcompany;
	  $do_contact->company = $company;
	  $do_contact->add();
	  $do_contact->addEmail($email,'Home');

	  $lastInsertedContId = $do_contact->getPrimaryKeyValue();

	  $this->getId($iduser);
	  $this->idcontact = $lastInsertedContId;
	  $this->update();
	  $contact_view = new ContactView();
	  $contact_view->setUser($iduser);
	  $contact_view->rebuildContactUserTable(); 
    }

    /**
      * Update the user contact 
      * @param $idcontact -- INT
    */
    function updateUserContact($idcontact){
	$this->query("update ".$this->table." set idcontact = ".$idcontact." where iduser = ".$_SESSION['do_User']->iduser);
    }

    /**
      * Get All isuser from the user table
      * Better would be to use getALL() 
    */
    function getAllUsersId() {
	    $sql = "SELECT iduser
			    FROM {$this->table}
			";
	    $this->query($sql);
    }
    
    /**
      * Get the count of total users
      * @return the total number of users
    */
    function getTotalUsers() {
	    $sql = "SELECT COUNT(iduser) AS total_users
			    FROM {$this->table}
			";
	    $this->query($sql);
	    if ($this->fetch()) {
		    return $this->getData("total_users");
	    }
    }

    /**
      * Get the count of total active users
      * @return the active user count -- INT
      */
    function getTotalActiveUsers() {
        $sql = "SELECT COUNT(user.iduser) AS active_users
                  FROM {$this->table}
            INNER JOIN login_audit
                    ON user.iduser = login_audit.iduser
                 WHERE DATEDIFF(CURDATE(), user.regdate) > 30
                   AND DATEDIFF(CURDATE(), login_audit.last_login) <= 7";
        $this->query($sql);
	    if ($this->fetch()) {
		    return $this->getData('active_users');
	    }
    }

    /**
      * Get the count of total new active users
      * @return the active user count -- INT
      */
    function getTotalNewActiveUsers() {
        $sql = "SELECT COUNT(user.iduser) AS active_users
                  FROM {$this->table}
            INNER JOIN login_audit
                    ON user.iduser = login_audit.iduser
                 WHERE DATEDIFF(CURDATE(), user.regdate) > 7
                   AND DATEDIFF(CURDATE(), login_audit.last_login) <= 7
                   AND DATEDIFF(CURDATE(), login_audit.last_login) > 1";
        $this->query($sql);
	    if ($this->fetch()) {
		    return $this->getData('active_users');
	    }
    }

    /**
      * Get the count of users that registered yesterday
      * @return the count of applicable users -- INT
      */
    function getUsersRegisteredYesterday() {
        $sql = "SELECT COUNT(iduser) AS registrations
                  FROM {$this->table}
                 WHERE DATEDIFF(CURDATE(), regdate) = 1";
        $this->query($sql);
	    if ($this->fetch()) {
		    return $this->getData('registrations');
	    }
    }

    /**
      * Get the count of users that registered in the past week
      * @return the count of applicable users -- INT
      */
    function getUsersRegisteredThisPastWeek() {
        $sql = "SELECT COUNT(iduser) AS registrations
                  FROM {$this->table}
                 WHERE DATEDIFF(CURDATE(), regdate) <= 8
                   AND DATEDIFF(CURDATE(), regdate) >= 1";
        $this->query($sql);
	    if ($this->fetch()) {
		    return $this->getData('registrations');
	    }
    }

    /**
      * Get the count of users that logged in yesterday
      * @return the count of applicable users -- INT
      */
    function getUsersLoggedInYesterday() {
        $sql = "SELECT COUNT(user.iduser) AS total_users
                  FROM {$this->table}
            INNER JOIN login_audit
                    ON user.iduser = login_audit.iduser
                 WHERE DATEDIFF(CURDATE(), login_audit.last_login) = 1";
        $this->query($sql);
	    if ($this->fetch()) {
		    return $this->getData('total_users');
	    }
    }

    /**
      * Get user details by name and emailid
      * @param string $firstname 
      * @param string $lastname 
      * @param Array $arr_contact_emails 
    */
    function getUserDetailsByNameEmail($firstname, $lastname, $arr_contact_emails) {
	    $str_emails = implode("','", $arr_contact_emails);
	    $sql = "SELECT *
			    FROM {$this->table}
			    WHERE
			    firstname = '{$firstname}' AND
			    lastname = '{$lastname}' AND 
			    email in ('{$str_emails}')
		    ";

	    $this->query($sql);
    }

    /**
      * Get the list of users who have logged in within last week
      * @param integer $period  
    */

    public function getUserLoggedInWithinPeriod($period = 7){
      $this->query("SELECT user. * FROM user
		    INNER JOIN login_audit ON user.iduser = login_audit.iduser
		    WHERE DATEDIFF( NOW( ) , login_audit.last_login )<= ".$period. " AND user.status = 'active'");
      $this->getValues();
    }


    /**
      * Suspend an user account 
      * @param integer $iduser
    */
    public function suspendUser($iduser){
      $this->query("update user set status = 'suspend' where iduser = ".$iduser." limit 1" );
    }


    /**
      * Function check the user who have not logged in for a specific period
      * @param integer $period
    */
     public function getUserNotLoggedInWithinPeriod($period = 60){
        $qry =  "SELECT user. *,login_audit.last_login as last_login_date FROM user
          INNER JOIN login_audit ON user.iduser = login_audit.iduser
          WHERE DATEDIFF( NOW( ) , login_audit.last_login )>= ".$period." AND user.status = 'active'" ;
      
        //echo $qry ;
        $this->query($qry);
        $this->getValues();
     }

  /**
   * Checks if Google Processed OpenId Identity exists for this User.
   * If User has logged in with Google account earlier, the Identity exists in DB.
   * @param int $google_openid_identity
   * @return bool
   */
  public function googleOpenIdIdentityExists($google_openid_identity) {
    $this->query("SELECT * FROM {$this->table} WHERE `google_openid_identity` = '{$google_openid_identity}'");
    $this->getValues();
  }

  /**
   * Sets Google Processed OpenId Identity for the User.
   * @param int $iduser
   * @return void
   */
  public function setGoogleOpenIdIdentity($iduser) {
    $sql = "UPDATE {$this->table}
            SET google_openid_identity = '".$_SESSION["google"]["openid_identity"]."'
            WHERE iduser = '{$iduser}'
           ";
    $this->query($sql);
    unset($_SESSION["google"]);
  }

  /**
   * This event suspends an User.Just changes the status to 'suspend'.
   * @param obj : EventControler
   * @return void
   */
  public function eventSuspendUser(EventControler $evtcl) {
    $do_oex = new OfuzExportXML();
    $do_oex->exportUserAccount($evtcl->iduser);

    $sql = "UPDATE `".$this->table."`
            SET `status` = 'suspend'
            WHERE `iduser` = ".$evtcl->iduser."
          ";
    $this->query($sql);
    $evtcl->setDisplayNext(new Display($evtcl->goto)) ;
  }

  /**
   * This event deletes entire User Account from Ofuz.
   * @param obj : EventControler
   * @return void
   */
  public function eventDeleteUser(EventControler $evtcl) {
    $do_oex = new OfuzExportXML();
    $do_oex->exportUserAccount($evtcl->iduser);

    $do_oca = new OfuzCancelAccount();
    $do_oca->deleteUserAccount($evtcl->iduser);
  }

    /**
     * New User Registration with Google Account
     * @param $evtcl -- Object
     * @return Void
     */
    function eventRegGoogleUser(EventControler $evtcl) {

        if($evtcl->google_openid_identity && $evtcl->google_openid_identity != ''){

          if($evtcl->firstname == '' || $evtcl->lastname == '' || $evtcl->email == ''){

            $err_disp = new Display($evtcl->err_page);
            $msg = "All the fields are required for your registration with Ofuz.";
            $err_disp->addParam("message", $msg);
            $evtcl->setDisplayNext($err_disp) ;

          } else {

              $this->firstname = $evtcl->firstname;
              $this->lastname = $evtcl->lastname;
              $this->google_openid_identity = $evtcl->google_openid_identity;
              $this->email = $evtcl->email;
              $this->plan = 'free';
              $this->status = 'active';
              $this->add();
              $this->iduser = $this->getPrimaryKeyValue();
              $this->setSessionVariable();

              $text = '';
              $text .= "Hi ".$this->firstname."\r\n";
              $text .= 'Welcome to Ofuz. Thanks for logging in with your Google Account. You can login to ofuz with your Google Account, also you will not need any username and password for login to ofuz, all you need to login with \'Sign in with Google\' button. How ever you can set one username as well from the setting and at any point of time can use the general login instead of facebook connect.<br /><br />';
              
              $text .= 'Thank You !<br /> Ofuz Team';
              $do_template = new EmailTemplate();
              $do_template->senderemail = "support@sqlfusion.com";
              $do_template->sendername = "Ofuz";
              $do_template->subject = "Welcome To Ofuz";
              $do_template->bodytext = $text;
              $do_template->bodyhtml = $do_template->bodytext;
              $this->sendMessage($do_template);
              $evtcl->setUrlNext("welcome_to_ofuz.php") ;

          }

        }else{

            $err_disp = new Display($evtcl->err_page);
            $msg = _('Sorry! It seems like you are not connected to Google. Please try again.');
            $err_disp->addParam("message", $msg);
            $evtcl->setDisplayNext($err_disp) ;

        }

    }

}

?>
