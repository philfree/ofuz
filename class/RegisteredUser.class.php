<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  
  /**
   * Class RegisteredUser
   * This is the base Class to create users that can register and sign in.
   * 
   * The openid suppose is optional and uses the ZendFramework.
   * The class uses a table and have some required fields:
   *   - a username field
   *   - a password field
   *   - an email field
   *   - an openid field (openid varchar())
   */
  
  class RegisteredUser extends DataObject {
    public $table = "user";
    protected $primary_key = "iduser";

    private $field_username = "username";
    private $field_password = "password";
    private $field_email = "email";

    private $emailtemplate = Array (
                    "forgotpassword" => "forgotpassword",
                    "registration" => "registrationthank",
                    "registration_admin" => "admin_registration_alert");

    private $openid_identifier;
    private $openid_goto;
    private $openid_regPage;
    private $openid_errPage;

    public $savedforms = Array (
                    "UserRegistration" => "registrationForm",
                    "UserWithOpenIdRegistration" => "openid_regForm",
                    "LoginWithOpenId" => "openid_loginForm");
                    
    public $eventactions = Array (
                    "registration.sendEmail",
                    "mydb.addParamToDisplayNext");

    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       if (isset($GLOBALS['cgf_reg_user_table'])) { 
          $this->setTable($GLOBALS['cgf_reg_user_table']);
       }
       if (isset($GLOBALS['cgf_reg_user_primarykey'])) {
            $this->setPrimaryKey($GLOBALS['cgf_reg_user_primarykey']);
       }
       $this->setFields();
       $this->setLogRun(false);
       if (RADRIA_LOG_RUN_REGISTRATION) {
           $this->setLogRun(RADRIA_LOG_RUN_REGISTRATION);
       }
    }

    public function setUsernameField($field_name) {
       $this->field_username = $field_name;
    }
    public function getUsernameField() {
       return $this->field_username;
    }
    public function setPasswordField($field_name) {
       $this->field_password = $field_name;
    }
    public function getPasswordField() {
       return $this->field_password;
    }
    public function setEmailField($field_name) {
       $this->field_email = $field_name;
    }
    public function getEmailField() {
       return $this->field_email;
    }

    /**
     * formLogin
     * This method just call the login form.
     * Print out a login form for user identification
     * Having fun her as its nore very usefull to have a registry for such simple form
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
        
        $field_password = new FieldTypePassword($this->getPasswordField());         
        $field_password->label = _("Password");
        $field_password->size = 20;  	
        $field_password->maxlenght = 40;	 
        $field_password->loginform = true;
    
        $form_fields->addField($field_password);
                
        $this->setFields($form_fields) ;
        $login_form = $this->prepareForm();	 
        $login_form->setFormEvent($this->getObjectName()."->eventSignIn");
        //$login_form->addEventAction($this->getObjectName()."->eventAuditLogin", 207);
        $login_form->addParam("goto",$nextPage);         
        $login_form->addParam("errPage", $errPage);
        $login_form->setForm();
        $login_form->execute();
                
    }

    /**
     *  formRegister
     * Display a form for users to register.
     * IT uses the form template: form/registrationForm
     * 
     * @param array of param with thankyou page and send_email flag.
     */

    public function formRegister($thankyoupage, $emailtemplate, $admin_emailttemplate, $adminemail) {
        if (empty($this->savedforms['UserRegistration'])) {
            $f_regForm = $this->prepareForm();
        } else {
            $f_regForm = $this->prepareSavedForm($this->savedforms['UserRegistration']);
        }

        if(strlen($emailtemplate)  > 0) {
            $f_regForm->addEventAction($this->getObjectName()."->eventSendWelcomeEmail", 1012);
            $f_regForm->addParam("emailtemplate_registration", $emailtemplate);
            $f_regForm->addParam("emailtemplate_registration_admin", $admin_emailttemplate);
            $f_regForm->addParam("email_admin", $adminemail);
        }
        $f_regForm->addEventAction($this->getObjectName()."->eventAutoSignIn", 5050) ;
        $f_regForm->addEventAction($this->getObjectName()."->eventAddUserToPhilContact", 5060) ;
        $f_regForm->addEventAction($this->getObjectName()."->eventAddUserAsContact", 5065) ;
        $f_regForm->addEventAction($this->getObjectName()."->eventRegistrationValidation", 101) ;
        $f_regForm->setUrlNext($thankyoupage);
        $f_regForm->setForm();
        $f_regForm->execute();        
    }

     /** 
     * formForgotPassword
     * Display a form for users to get password.
     * IT uses the form template: form/registrationForm
     * 
     */
    public function formGetPassword( $button_text="", $sent_message="", $prompt_message="") {

        if (empty($button_text)) { $button_text = _("Submit"); }
        if (empty($sent_message)) { $sent_message = _("Your Password has been sent"); }
        if (empty($prompt_message)) { $prompt_message = _("Enter your email address"); }
        
        
        $field_email = new FieldTypeChar($this->getEmailField());
        $field_email->label = $prompt_message;
        $field_email->size = 20;
        $field_email->maxlenght = 40;
        $field_email->css_form_class = "formfield";		
        
        $form_fields = new FieldsForm();
        $form_fields->addField($field_email);
        //$this->setFields($form_fields) ;

        $forgotpass_form = $this->newForm($this->getObjectName()."->eventGetForgotPassword");	 
        //$forgotpass_form->addParam("message_goto",$_SERVER['PHP_SELF']);
        //$forgotpass_form->addParam("sent_message", $sent_message);
        $forgotpass_form->addParam("message_goto","user_login.php");
        $forgotpass_form->addParam("message", $sent_message);
        $forgotpass_form->addParam("user_table", "user");
        $htmlform = $forgotpass_form->getFormHeader();
        $htmlform .= $forgotpass_form->getFormEvent();
        $htmlform .= $form_fields->{$this->getEmailField()};
        $htmlform .= $forgotpass_form->getFormFooter($button_text);
        echo $htmlform;
    }

    /**
     *  formRegisterOpenId
     * Display a form for users to register with Open Id.
     * IT uses the form template: form/registrationForm
     * 
     * @param array of param with thankyou page and send_email flag.
     */

    public function formRegisterOpenId($thankyoupage, $emailtemplate, $admin_emailttemplate, $adminemail) {
        $f_regFormOpenId = $this->prepareSavedForm($this->savedforms['UserWithOpenIdRegistration']) ;
        $f_regFormOpenId->setRegistry($this->getTable());
        
        if(strlen($emailtemplate)  > 0) {
            $f_regFormOpenId->addEventAction($this->getObjectName()."->eventSendWelcomeEmail", 1012);
            $f_regFormOpenId->addParam("emailtemplate_registration", $emailtemplate);
            $f_regFormOpenId->addParam("emailtemplate_registration_admin", $admin_emailttemplate);
            $f_regFormOpenId->addParam("email_admin", $adminemail);
        }
        $f_regFormOpenId->addEventAction($this->getObjectName()."->eventAutoSignIn", 5050) ;
        $f_regFormOpenId->setUrlNext($thankyoupage);
        $f_regFormOpenId->setForm();
        $f_regFormOpenId->execute();
    }

    /**
     * formLoginOpenId
     * Display a form for users to login with Open Id.
     * IT uses the form template: form/registrationForm
     */
    public function formLoginOpenId($registration_page, $nextPage, $strWrongLoginPassword) {
        $f_regFormOpenId = $this->prepareSavedForm($this->savedforms['LoginWithOpenId']);
        $f_regFormOpenId->setFormEvent($this->getObjectName()."->eventOpenIdSignon",2300);
        //$f_regFormOpenId->addParam("goto", $nextPage);
        $f_regFormOpenId->addParam("errPage", $_SERVER['PHP_SELF']);
        //$f_regFormOpenId->addParam("regPage", $registration_page);
        $this->openid_regPage = $registration_page;
        $this->openid_errPage = $_SERVER['PHP_SELF'];
        $This->openid_goto = $nextPage;
        $f_regFormOpenId->addParam("strWrongLoginPassword", $strWrongLoginPassword);
        $f_regFormOpenId->setForm();
        $f_regFormOpenId->execute();
    }


 /**
   * Event registration.openid_signon
   * Check the OpenID using Zend Franework.
   * FIXME
   * @param EventControler object
   * @package registration
   * @author Jay Link
   * @version 1.3
   */

    function eventOpenIdSignon(EventControler $evtcl) {
        if (file_exists("Zend/OpenId/Consumer.php")) {
            include_once("Zend/OpenId/Consumer.php");
            $strWrongLoginPassword = $evtcl->strWrongLoginPassword;
            if (empty($strWrongLoginPassword)) {
                $strWrongLoginPassword = _('Wrong_login_or_password');
            }

            $this->setLog("\n(User) OpenID Sign on ".date("Y/m/d H:i:s"));

            $openid_action = $evtcl->openid_action;
            $openid_identifier = $evtcl->openid_identifier;
            $_SESSION['openid_identifier'] = $openid_identifier;
            $_SESSION['openid_userclass'] = $this->getObjectName();
            $this->openid_identifier = $openid_identifier;
            //$this->openid_goto = $evtcl->goto;
            //$this->openid_regPage = $evtcl->regPage;
            //$this->openid_errPage = $evtcl->errPage;

            if (isset($openid_action) && $openid_action == 'Login' && !empty($openid_identifier)) {
                $consumer = new Zend_OpenId_Consumer();
                if (!$consumer->login($openid_identifier, 'openid_verify.sys.php')) {
                    $dispError = new Display($this->errPage);
                    $dispError->addParam("openidmessage", $strWrongLoginPassword);
                    $evtcl->setDisplayNext($dispError);
                }
            } else if (empty($openid_identifier)) {
                $dispError = new Display($evtcl->errPage);
                $dispError->addParam("openidmessage", $strWrongLoginPassword);
                $evtcl->setDisplayNext($dispError);
            }
        }
    }


    function eventCheckOpenIdCallBack(EventControler $eventControler) {
        if (!file_exists("Zend/OpenId/Consumer.php")) {
            return false;
        }
        $message_status = "";
        if ($_GET['openid_mode'] == 'id_res') {
            $consumer = new Zend_OpenId_Consumer();
            if ($consumer->verify($_GET, $this->openid_identifier)) {
                //$conx = $eventControler->getDbCon();
                $qCheck = new sqlQuery($this->getDbCon());
                $eventControler->setLog("\n OpenId check,".$this->openid_identifier." table:".$this->getTable());
                $this->query("select * from `".$this->getTable()."` where `openid`='".$this->openid_identifier."'");
                $eventControler->setLog("\n Query executed for sign on:".$this->getSqlQuery());
                $eventControler->setLog("\n RegPage:".$this->openid_regPage." goto:".$this->openid_goto." errPage:".$this->openid_errPage);
                if ($qCheck->getNumrows() == 1) {
                    $userdata = Array();
                    $userdata['id'] = $this->getPrimaryKeyValue();
                    //$userdata['id'] = $this->iduser;
                    $userdata['firstname'] = $this->firstname;
                    $userdata['lastname'] = $this->lastname;
                    $userdata['email'] = $this->{$this->getEmailField()};
                    $userdata['username'] = $this->{$this->getUsernameField()};
                    if ($this->isadmin) {
                        $userdata['isadmin'] = 1 ;
                    }
                    $userdata['user_table'] = $user_table;
                    $_SESSION['userdata'] = $userdata;
                    if (!$this->isPersistent()) {
                        $this->sessionPersistent("do_".$this->getObjectName(), "signout.php", 36000);
                    }
                    $eventControler->goto = $this->openid_goto;
                    $eventControler->setUrlNext($this->openid_goto);
                    return true;
                } else {
                    $eventControler->setLog("\n this user need to register:".$this->openid_regPage);
                    $eventControler->goto = $this->openid_regPage;  
                    $eventControler->setUrlNext($this->openid_regPage);
                    $eventControler->setLog("\n Redirected set to:".$eventControler->getUrlNext());
                    return true;
                }
            } else {
                $message_status = 'The OpenID is invalid.';
            }
        } elseif ($_GET['openid_mode'] == 'cancel') {
            $message_status = 'The OpenID login was cancelled.';
        } else {
            $message_status = 'The OpenID is invalid.';
        }
        if (!empty($message_status)) {
            $err_disp = new Display($this->openid_errPage);
            $err_disp->addParam("openidmessage", $message_status);
            $eventControler->setDisplayNext($err_disp);
            return false;
        }
    }


  /**
   * Event registration log out
   * Logout the user and clear off all the session variable currently set.
   * This event can be triggered with an link event like:
   * <?php
   *   $e = new Event("RegisteredUser->eventLogout");
   *   $e->goto = "home.php";
   *   $e->full_sign_off = true;
   *   echo $e->getLink("Logout");
   * ?>
   * The full_sign_off param to true will kill the entire session  
   * It redirect to the goto page with a param in the url: 
   *  $_GET['loginmessage']; can be use to display the message 
   * 
   * @package registration
   * @author Philippe Lewicki
   * @version 1.0
   */

  function eventLogout(EventControler $eventControler) {

        $goto = $eventControler->getParam("goto");
        $full_sign_off = $eventControler->getParam("full_sign_off");

        if (empty($goto)) { 
            $goto = "index.php";
        }  
        if (empty($full_sign_off)) {
            $full_sign_off = false;
        }
        if ($full_sign_off) {          
           session_unregister('userdata'); 
           unset($_SESSION['userdata']);
           $this->setFree();
           $this->free();
        } else {
            // Unset all of the session variables.
            $_SESSION = array();
            
            // If it's desired to kill the session, also delete the session cookie.
            // Note: This will destroy the session, and not just the session data!
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }
            
            // Finally, destroy the session.
            session_destroy();
        }

        $dispError = new Display($goto) ;
        $dispError->addParam("message", _('Log out successful')) ;
        $eventControler->setDisplayNext($dispError) ;
        
      /*  if (class_exists("AuditLog")) {
            $log = new AuditLog();
            $log->init();
            $log->action = "logout";
            $log->user = $this->{$this->getUsernameField()};
            $log->object = $this->getObjectName();
            $log->add();
        } */


  }
  /**
   *  Event AuditLogin
   *  Record in a login table the users login.
   *  This requires an AuditLog object and auditlog table
   * @param EventControler eventcontroler instance
   */

  function eventAuditLogin(EventControler $evtcl) {
    if (is_object($_SESSION['AuditLog'])) {
        $_SESSION['AuditLog']->user = $this->{$this->getUsernameField()};
        $_SESSION['AuditLog']->action = "login";
        $_SESSION['AuditLog']->add();
    } else {
        $AuditLog = new AuditLog();
        $AuditLog->init();
        $AuditLog->user = $this->{$this->getUsernameField()};
        $AuditLog->action = "login";
        $AuditLog->object = get_class($this);
        $AuditLog->add();
    }
      
  }

  /**
   * Event registration.signon
   * Check the login and password in the users table.
   * First part get the parameters from the eventcontroler 
   * then check the username and password.
   * If the object is not yet persistant, setting up in the session
   * as $_SESSION['do_<class name>']
   *
   * @package registration
   * @author Philippe Lewicki
   * @version 2.0
   */
  function eventSignIn(EventControler $eventControler) {

        if($eventControler->goto == 'i_contacts.php'){ // Added for i-phone login page
            $dispError = new Display("i_login.php") ;
        }else{
          $dispError = new Display($errPage) ;
        }

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
                $userdata = Array();
               // $userdata['id'] = $this->getPrimaryKeyValue();
                $userdata['id'] = $this->iduser;
                $userdata['firstname'] = $this->firstname;
                $userdata['lastname'] = $this->lastname;
                $userdata['email'] = $this->email;
                $userdata['username'] = $this->{$this->getUsernameField()};
                if ($this->isadmin) {
                    $userdata['isadmin'] = 1 ;
                }
                $userdata['user_table'] = $user_table;
                $_SESSION['userdata'] = $userdata;
                if (!$this->isPersistent()) {
                    $this->sessionPersistent("do_".$this->getObjectName(), "signout.php", 36000);
                }
                $eventControler->setUrlNext($goto) ;

            } else {
                $dispError->addParam("loginmessage", $strWrongLoginPassword) ;
                $eventControler->setDisplayNext($dispError) ;
            }
        } else { 
                $dispError->addParam("loginmessage", $strWrongLoginPassword) ;
                $eventControler->setDisplayNext($dispError) ;
        }
     }

 function eventAutoSignIn(EventControler $eventControler) {
        if ($eventControler->doSave == "yes") {

            $this->setLog("\n (User) Registration Sign on ".date("Y/m/d H:i:s"));

            $fields = $eventControler->fields;
            $auth_username = $fields[$this->getUsernameField()];
            $auth_password = $fields[$this->getPasswordField()];
            $goto = $eventControler->goto;

            if (strlen($auth_username) > 0 && strlen($auth_password) > 0) {

                $this->setLog("\n(".$this->getObjectName().") table:".$this->getTable());
                $this->query("select * from `".$this->getTable()."` 
                            where `".$this->getUsernameField()."`='".$this->quote($auth_username)."' 
                            and `".$this->getPasswordField()."`='".$this->quote($auth_password)."'") ;
                $this->setLog("\n(User) Query executed for sign on:".$this->sql_query);
                
                if ($this->getNumrows() == 1) {
                    $userdata = Array();
                    $userdata['id'] = $this->iduser;
                    $userdata['firstname'] = $this->firstname;
                    $userdata['lastname'] = $this->lastname;
                    $userdata['email'] = $this->email;
                    $userdata['username'] = $this->{$this->getUsernameField()};
                    if ($this->isadmin) {
                        $userdata['isadmin'] = 1 ;
                    }
                    $userdata['user_table'] = $user_table;
                    $_SESSION['userdata'] = $userdata;
                    if (!$this->isPersistent()) {
                        $this->sessionPersistent("do_".$this->getObjectName(), "signout.php", 36000);
                    }
                } 
                $do_login_audit = new LoginAudit();
                $do_login_audit->do_login_audit();  
                $eventControler->setUrlNext($eventControler->goto) ;
            } 
        }
     }


    /**
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
        if ($qGetPass->getNumRows() > 0) {
            while($dPass = $qGetPass->fetchArray()) {
                $email = new Emailer() ;
                $email->loadEmailer($this->getDbCon(), $this->emailtemplate['forgotpassword']) ;
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
     * eventSendWelcomeEmail
     * Send a welcome email to the newly registered user and also send
     * an alert to the administrator.
     * Emails are created from templates stored in the emailtemplate table.
     */


    function eventSendWelcomeEmail(EventControler $eventControler) {
        $emailFieldName = $this->getEmailField();
        $fields = $eventControler->fields;
        $emailto = $fields[$emailFieldName];
        $email_admin = $eventControler->email_admin;
        
        if (strlen($eventControler->emailtemplate_registration_admin) >0) {
            $emailtemplate_registration_admin = $eventControler->emailtemplate_registration_admin;
        } elseif(!empty($emailtemplate['registration_admin'])) {
            $emailtemplate_registration_admin = $emailtemplate['registration_admin'];
        }

        if (strlen($eventControler->emailtemplate_registration) > 0) {
            $emailtemplate_registration = $eventControler->emailtemplate_registration;
        } else {
            $emailtemplate_registration = $emailtemplate['registration'];
        }

        if ($eventControler->doSave == "yes") {
            $email = new Emailer() ;
            $email->loadEmailer($this->getDbCon(), $emailtemplate_registration) ;
            $email->mergeArray($fields);
            if ($email->hasHtml) {
                $email->sendMailHtml($emailto) ;
            } else {
                $email->sendMailStandard($emailto);
            }

            // send an alert email to the administrator
            if (strlen($emailtemplate_registration_admin)>0 && !empty($email_admin)) {
                $emailadmin = new Emailer();
                $emailadmin->loadEmailer($this->getDbCon(), $emailtemplate_registration_admin) ;
                $emailadmin->mergeArray($fields);                
                if ($emailadmin->hasHtml()) {
                    $emailadmin->sendMailHtml($email_admin) ;
                } else {
                    $emailadmin->sendMailStandard($email_admin);
                }
            }
        }
    }

    function eventRegistrationValidation(EventControler $evtcl) {
      $fields = $evtcl->fields;
      $do_user_rel = new UserRelations();
      if($evtcl->errPage != ''){
          $errorpage = $evtcl->errPage ;
      }else{
          $errorpage = "user_register.php";
      }
      if(trim($fields["firstname"]) == "" || trim($fields["lastname"]) == "" || trim($fields["email"]) == "" || trim($fields["username"]) == "" || trim($fields["password"]) == "" || trim($evtcl->fieldrepeatpass["password"])== "" ) {
          $evtcl->validation_fail = 'Yes';
          $msg = "You must fill the required fields";
          $errorpage = $evtcl->errorpage;
          $dispError = new Display($errorpage) ;
          $dispError->addParam("id", $evtcl->id) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;


      }elseif(trim($fields["password"]) != $do_user_rel->encrypt($evtcl->fieldrepeatpass["password"])){
          $evtcl->validation_fail = 'Yes';
          $msg = "Both the Password are not matching";
          $errorpage = $evtcl->errorpage;
          $dispError = new Display($errorpage) ;
          $dispError->addParam("id", $evtcl->id) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;
        
      }else{ $evtcl->validation_fail = 'No'; }
      
    }


    
    /**
      * Event method to validate the user info at the time of updatating data
      * @param object $evtcl
    */

    function eventValidationOnUpdate(EventControler $evtcl) {
      $fields = $evtcl->fields;
      $do_user_rel = new UserRelations();
      $errorpage = $evtcl->errPage;
      if(trim($fields["firstname"]) == "" || trim($fields["lastname"]) == "" || trim($fields["email"]) == "" || trim($fields["username"]) == "" || trim($fields["password"]) == "" || trim($evtcl->fieldrepeatpass["password"])== "" ) {
          $evtcl->doSave = 'No';
          $msg = "You must fill the required fields";
          $errorpage = $evtcl->errorpage;
          $dispError = new Display($errorpage) ;
          $dispError->addParam("id", $evtcl->id) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;
          

      }elseif(trim($fields["password"]) != trim($evtcl->fieldrepeatpass["password"]) ){ 
          $evtcl->doSave = 'No';
          $msg = "Both the Password are not matching";
          $errorpage = $evtcl->errorpage;
          $dispError = new Display($errorpage) ;
          $dispError->addParam("id", $evtcl->id) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;
          
      }else{ 
          $q = new sqlQuery($this->getDbCon());
          $q->query("select * from user where email = '".trim($fields["email"])."' AND iduser <> ". $_SESSION['do_User']->iduser);

          $q1 = new sqlQuery($this->getDbCon());
          $q1->query("select * from user where username = '".$fields["username"]."'AND iduser <> ". $_SESSION['do_User']->iduser);

           if ($q->getNumRows() > 0 && $_SESSION['do_User']->email != trim($fields["email"]) ) {
                  $evtcl->doSave = 'No';
                  $msg = "reg_duplicate_email";
                  $dispError = new Display($errorpage) ;
                  $dispError->addParam("message", $msg) ;
                  $evtcl->setDisplayNext($dispError) ;
                  
            }elseif($q1->getNumRows() > 0 && $_SESSION['do_User']->username != trim($fields["username"])){
                  $evtcl->doSave = 'No';
                  $msg = "Username is already in use";
                  $dispError = new Display($errorpage) ;
                  $dispError->addParam("message", $msg) ;
                  $evtcl->setDisplayNext($dispError) ;
                 
          }else{  $evtcl->doSave = 'yes';  }
      }
      
    }

    /**
     * Custom method to update the user info from the setting page.
     * @param object $evtcl
    */
    function eventUpdateUserInfo(EventControler $evtcl) {
          $fields = $evtcl->fields;
          if( $evtcl->doSave == "yes"){
              $qry = "update ".$this->table." set 
                      firstname = '".$fields["firstname"]."',
                      lastname = '".$fields["lastname"]."',
                      email = '".$fields["email"]."',
                      username = '".$fields["username"]."',
                      password = '".$fields["password"]."',
                      company = '".$fields["company"]."',
                      position = '".$fields["position"]."',
                      address1 = '".$fields["address1"]."',
                      address2 = '".$fields["address2"]."',
                      city = '".$fields["city"]."',
                      zip = '".$fields["zip"]."',
                      state = '".$fields["state"]."',
                      country = '".$fields["country"]."'
                      where iduser = ".$_SESSION['do_User']->iduser ;
              
              $this->query($qry);
              $msg = "Data has been updated.";
              $dispError = new Display($evtcl->errPage) ;
              $dispError->addParam("message", $msg) ;
              $evtcl->setDisplayNext($dispError) ;
              
          }
    }
    


 }


?>
