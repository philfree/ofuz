<?php
    /**
     * SampleAddOn
     * Is the sample AddOn to describe and show 
     * how an add works.
     */

class LogoutAddOn extends RegistrationAddOnBase {
   
    public $name = "Logout";
    public $name_toolbar = "Logout";
    protected $description = "Logout users ";
    protected $group = "Samples"; // name of the group this add on is part of
    protected $default_layer_name = "LogoutAddOn";
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
        $formfields = new FieldsForm("LogoutAddOn");
        $formfields->setValues($values);
        $form = "this is a sample form";
        $form .="<br>Full Sign Off: ".$formfields->full_sign_off;
        $form .="<br>Next Page:".$formfields->next_page;
        $form .="<br>Link Text:".$formfields->link_text;
        $form .= "<br/>User's class name:".$formfields->Users_Class;
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
        if (!isset($params['full_sign_off'])) {
            $params['full_sign_off'] = false;
        }
        $content = "<?php if(\$_GET['message']) { ?>";
        $content .= '<div class="error_message"><?php echo htmlentities(stripslashes($_GET[\'message\'])); ?></div>'; 
        $content .= "\n<?php }?>";  
        $content .= "\n<?php \$do_user = new ".$params['User_Class']."();";
        
        $content .= "\n \$do_logout = new Event(\"do_".$params['User_Class']."->eventLogout\"); ";
        $content .= "\n \$do_logout->addParam(\"goto\",\"".$params['next_page']."\"); ";
        $content .= "\n \$do_logout->addParam(\"full_sign_off\",\"".$params['full_sign_off']."\"); ";
        $content .= "\n echo \$do_logout->getLink(\"".$params['link_text']."\"); ";
        $content .= "\n?>";
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
