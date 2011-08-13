<?php
    /**
     * SampleAddOn
     * Is the sample AddOn to describe and show 
     * how an add works.
     */

class LoginFormAddOn extends RegistrationAddOnBase {
   
    public $name = "Login Form";
    public $name_toolbar = "Login";
    protected $description = "Login form to sign-in with links to registration and forgot password pages.";
    protected $group = "Samples"; // name of the group this add on is part of
    protected $default_layer_name = "LoginForm";
    //protected $help_url = "docs/registration/index.html";
    public $icon_small = "images/make.gif"; // 16px
    public $icon_large = "images/addon_on.gif"; // 32px
    protected $default_pos = Array ("top" => 200,
                                    "left" => 100,
                                    "width" => 300,
                                    "height" => 500);
    protected $default_border_style = ""; // solid, dash... css style
    protected $direct_insert = false; // Directly insert do not display add form.
   
    /**
     * formProperty
     * This form display on the left side bar property.
     * It display each time the user click the AddOn in the page.
     * The width is limited.
     * form displayed to the user to configure and customize the add-on to be display in 
     * the page.
     */
    
    public function formProperty() {
        $values = $this->getAddOnValues();
        $formfields = new FieldsForm("login.script.inc.php");
        $formfields->setValues($values);
        $form = "Login form:";
        $form .= "<br>Page after signing in: ".$formfields->Next_Page;
        $form .= "<br> Error message for wrong login and password:<br>";
        $form .= $formfields->err_string;
        $form .= "<br>Page for user registration:".$formfields->Registration_Page;
        $form .= "<br>Page to retrieve forgotten password:<br>";
        $form .= $formfields->Get_Password_Page;
        $form .= "<br>Optional paramters:";
        $form .= "<br/>User's class name:".$formfields->User_Class;
        $form .= "<br>Login Form style:".$formfields->login_form_style;
        return $form;
    }
    
    /**
     * formFullPage()
     * This form is not limited in width 
     * It is access when users add the AddOn for the first time in the 
     * page and then when they double click or select Edit from the 
     * context menu.
     * This one is optional by default its the same as the
     * formProperty().
     */ 

    public function formFullPage() {
        $form = $this->headerFullPageForm();
        $form .= $this->formProperty();

        return $form;
    }
    
    /**
     *  insertInPage()
     *  Gets the params from the form in an
     *  array structure and process the code
     *  that need to be inserted in the page.
     */
    public function insertInPage(Array $params) {   
        $content = "<?php ";
        if (empty($params['User_Class'])) {
            $params['User_Class'] = "User";
        }
        $params['login_form_style'] = substr($params['login_form_style'], 1);
   
        $content .= "\$loginForm = new ".$params['User_Class']."();";
        $content .= "\$loginForm->sessionPersistent(\"do_".$params['User_Class']."\", \"\", 36000);";
        $content .= "if(\$_GET['message']) { ?>";
        $content .= "<div class=\"error_message\"><?php echo htmlentities(stripslashes(\$_GET['message'])); ?></div>";
        $content .= "<?php } ?>";
        $content .= "<div class=\"".$params['login_form_style']."\">";
        $content .=  "<?php ";
        $content .= "\$_SESSION['do_".$params['User_Class']."']->formLogin(\"".$params['Next_Page']."\", \"".$params['err_string']."\", \"".$params['login_form_style']."\");";
        $content .= "?>";
        $content .= "<br />
        If you have not registered yet, please do so <a href=\"".$params['Registration_Page']."\">here</a> .<br />
        If you forgot your password, you can retrieve it <a href=\"".$params['Get_Password_Page']."\">here</a>
        ";
        $content .= "</div>";
        
        $this->setLog("\n---------AddOn insert AddOn content in page (".$this->getObjectName().")-----------\n".htmlentities($content)."\n----------\n");
        return $content;
    }
    
    /**
     * Set the default value when the addon is
     * inserted directly in the page.
     * @return array of fieldname / values.
     */
    public function defaultValues() {
        $fields = Array("name" => "Philippe Lewicki",
                        "city" => "Los Angeles");
        return $fields;
    }

}


?>
