<?php
namespace RadriaCore\Radria\FieldType;
use RadriaCore\Radria\Display;
use RadriaCore\Radria\Event;
use RadriaCore\Radria\EventControler;
use RadriaCore\Radria\mysql\SqlQuery;

/**
 * Class strFBFieldType RegistryField class
 *
 * In the Form context display 2 text line field in password mode and trigger the EventAction: mydb.checkUsernamePassword to check it the username and password dont already exists and if the 2 passwords are the same.
 * This RegistryField requires a strFBFieldTypeLogin field in the same form.
 * @package PASClass
 */

class FieldTypePassword  extends RegistryFieldStyle
{
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            if (!$this->getRdata('execute')) {
                $field_value = $this->no_PhpCode($field_value);
            }
            if ($this->getRdata("loginform")) {
                $fval .= "<input type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\"/>" ;
            } else {
                $e_password = new Event($this->getEventActionName("eventCheckUsernamePassword"));
                $e_password->addParam("accessfield[password]", $this->getFieldName())->setLevel($this->getEventLevel());
                $fval = $e_password->getEvent();
                $fval .= "<input ".$this->getStyleParam()." type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\"/>" ;
                $fval .=  "\n<br/><input id=\"confirm_password\" type=\"password\" name=\"fieldrepeatpass[".$this->getFieldName()."]\" value=\"".$field_value."\"/>"  ;
            }
            $this->processed .= $fval;
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            if (!$this->getRdata('execute')) {
                $field_value = $this->no_PhpCode($field_value);
            }
            $this->processed .= $field_value;
        }
    }

    function eventCheckUsernamePassword(EventControler $evctl) {
        /**   Event CheckUsernamePassword
         *
         * To test if passwords matches and there is not already a login and password
         * To work the uniq id of the table must be named as id<table name>.
         * If its a new record the uniqid must be an empty string else a integer..
         * If not it sets the doSave param at "no" to block the save and
         * Call the message page.
         * @package RadriaEvents
         * @author Philippe Lewicki <phil@sqlfusion.com>
         * @param array accessfield array with the name of the password and login fields
         * Option :
         * @param string errorpage page to display the errors
         * @copyright SQLFusion
         */
        /*
        $strMissingField  = "Vous devez avoir 1 login et 1 mot de passe" ;
        $strErrorPasswordNotMatch = "Les mots de passe saisie ne correspondent pas ";
        $strErrorLoginAlreadyUsed = "Loggin deja utilise, Vous devez choisir un autre login";
        */
        global $strMissingField, $strErrorPasswordNotMatch, $strErrorLoginAlreadyUsed;
        if (!isset($strMissingField)) {
            $strMissingField = "You need a login and password in the form" ;
        }
        if (!isset($strErrorPasswordNotMatch)) {
            $strErrorPasswordNotMatch = "The password entries do not match" ;
        }
        if (!isset($strErrorLoginAlreadyUsed)) {
            $strErrorLoginAlreadyUsed = "The username is already in use" ;
        }
        $accessfield = $evctl->accessfield;
        $fields = $evctl->fields;
        $fieldrepeatpass = $evctl->fieldrepeatpass;
        $errorpage = $evctl->errorpage;
        $this->setLog("\n Check login & password:".$evctl->errorpage);
        $this->setLogArray($fields);
        $this->setLog("\n Repeat pass:");
        $this->setLogArray($fieldrepeatpass);
        if ($evctl->submitbutton != "Cancel") {
            if (strlen($errorpage)>0) {
                $dispError = new Display($errorpage) ;
            } else {
                $dispError = new Display($evctl->getMessagePage()) ;
            }
            $dispError->addParam("message","") ;

            if (is_array($accessfield)) {
                if (!isset($table)) { $table = "users"; }
                $nbraccess = count($accessfield) ;
                if ($nbraccess != 2) {
                    $dispError->editParam("message",$strMissingField) ;
                }
                $passwordfield = $accessfield["password"] ;
                $loginfield = $accessfield["login"] ;
                $this->setLog("\n Verify pass:".$fieldrepeatpass[$passwordfield]);
                if ($fields[$passwordfield] != $fieldrepeatpass[$passwordfield]) {
                    $dispError->editParam("message", $strErrorPasswordNotMatch) ;
                }
                if (get_magic_quotes_gpc()) {
                    $primarykey = stripslashes($primarykey) ;
                }
                if (strlen($primarykey) > 0) {
                    $queryverif = "select * from ".$table." where ".$loginfield."='".$fields[$loginfield]."' AND NOT(".$primarykey.")" ;
                } else {
                    $queryverif = "select * from ".$table." where ".$loginfield."='".$fields[$loginfield]."'" ;
                }
                $qVerif = new SqlQuery($evctl->getDbCon()) ;
                $rverif = $qVerif->query($queryverif) ;
                if ($qVerif->getNumRows()) {
                    $dispError->editParam("message",$strErrorLoginAlreadyUsed ) ;
                }
            }
            $error  = $dispError->getParam("message") ;
            if (strlen($error) > 0) {
                $_SESSION["in_page_message"] = $error;
                $evctl->setDisplayNext($dispError) ;
                $evctl->updateParam("doSave", "no") ;
                // echo "supposed to be no from here " ;
            }
        }
    }
}