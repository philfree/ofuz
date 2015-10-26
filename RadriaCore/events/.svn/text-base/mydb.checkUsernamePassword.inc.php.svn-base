<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
 /**   Event Mydb.checkUsernamePassword
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
    $strMissingField = "You need a login and password in the form " ;
  }
  if (!isset($strErrorPasswordNotMatch)) {
    $strErrorPasswordNotMatch = "The password entries do not match" ;
  }
  if (!isset($strErrorLoginAlreadyUsed)) {
    $strErrorLoginAlreadyUsed = "The username is already in use" ;
  }

if ($submitbutton != "Cancel") {
        if (strlen($errorpage)>0) {
                $dispError = new Display($errorpage) ;
        } else {
                $dispError = new Display($this->getMessagePage()) ;
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
                $qVerif = new sqlQuery($dbc) ;
                $rverif = $qVerif->query($queryverif) ;
                if ($qVerif->getNumRows()) {
                    $dispError->editParam("message",$strErrorLoginAlreadyUsed ) ;
                }
        }
        $error  = $dispError->getParam("message") ;
        if (strlen($error) > 0) {
                $this->setDisplayNext($dispError) ;
                $this->updateParam("doSave", "no") ;
        // echo "supposed to be no from here " ;
        }
}
?>