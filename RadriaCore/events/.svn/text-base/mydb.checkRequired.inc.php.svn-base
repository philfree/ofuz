<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
 /**   Event Mydb.checkRequired
  *
  * Check that all the field set as required are field in.
  * If not it sets the doSave param at "no" to block the save and
  * call the message page.
  * <br>- param array fields that contains the content of the fields to check
  * <br>- param array required indexed on fields name and contains value "yes"
  * <br>Option:
  * <br>- param string errorpage page to display the error message
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.0
  */
  /**
  $strRequiredField = "Vous devez remplire to les champs obligatoire" ;
   */
 global $strRequiredField;
 if (!isset($strRequiredField)) {
   $strRequiredField = "You must fill in all the fields that are required." ;
 }
 if ($submitbutton != "Cancel") {
        if (is_array($fields)) {
	  while (list($key, $val) = each($fields)) {
		if (($required[$key]=="yes") && $val == "") {
			if (strlen($errorpage)>0) {
				$urlerror = $errorpage;
			} else {
				$urlerror = $this->getMessagePage() ;
			}
			$disp = new Display($urlerror);
			$disp->addParam("message", $strRequiredField) ;
			$this->setDisplayNext($disp) ;
			$this->updateParam("doSave", "no") ;
		}
	  }
        }
 }
?>