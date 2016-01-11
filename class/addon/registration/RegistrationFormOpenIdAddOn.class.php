<?php
    /**
     * SampleAddOn
     * Is the sample AddOn to describe and show 
     * how an add works.
     */

class RegistrationFormOpenIdAddOn extends RegistrationAddOnBase {
   
    public $name = "Registration Open Id Form";
    public $name_toolbar = "Reg Open Id Form";
    protected $description = "Registration Open Id form for new users and user editing";
    protected $group = "Samples"; // name of the group this add on is part of
    protected $default_layer_name = "RegistrationFormOpenId";
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
        $formfields = new FieldsForm("RegistrationFormAddOn");
        $formfields->setValues($values);
        $form = "Form to register on your site when using OpenID.";
        $form .="<br>Next Page:".$formfields->thankyou_page;
        $form .="<hr></hr>";
        $form .="<u></u>Optional paramters:</u>";
        $form .="<br/><b>Welcome email:</b><br/>".$formfields->emailtemplate;
        $form .="<br/><b>Admin alert email:</b><br/>".$formfields->admin_emailtemplate;
        $form .="<br/><b>Admin email address:</b><br/>".$formfields->admin_email;
        $form .= "<br/><b>User's class name:</b><br/>".$formfields->User_Class;        
        
        
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
        if (empty($params['User_Class'])) {
            $params['User_Class'] = "User";
        }

        $content = "<?php if(\$_GET['openidmessage']) { ?>";
        $content .= '<div class="error_message"><?php echo htmlentities(stripslashes($_GET[\'openidmessage\'])); ?></div>'; 
        $content .= "\n<?php  }?>";  
        $content .= "\n<?php \$do_user = new ".$params['User_Class']."();";
        $content .= "\n      \$do_user->sessionPersistent(\"do_".$params['User_Class']."\", \"signout.php\", 36000);";
        $content .= "\n      \$_SESSION['do_".$params['User_Class']."']->formRegisterOpenId('".$params['thankyou_page']."',
                                                     '".$params['emailtemplate']."',
                                                     '".$params['admin_emailtemplate']."',
                                                     '".$params['admin_email']."'
                                                     ); ";
        $content .= "\n?>\n\n";       
        $this->setLog("\n---------AddOn insert AddOn content in page (".$this->getObjectName().")-----------\n".htmlentities($content)."\n----------\n");
        return $content;
    }
    
    /**
     * Set the default value when the addon is
     * inserted directly in the page.
     * @return array of fieldname / values.
     */
    public function defaultValues() {
        $fields = Array("thankyou_page" => "registered_user_page.hide.php",
                        "emailtemplate" => "",
                        "admin_emailtemplate" => "",
                        "admin_email" => "",
                        "User_Class" => "");
        return $fields;
    }

}


?>
