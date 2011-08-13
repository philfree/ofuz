<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /****
   * Event that send a thank you email to the registrar.
   * This is a sample to show how to use the email template package.
   *
   */

  include_once("class/Emailer.class.php") ; 
  if ($doSave == "yes") {
    $email = new Emailer() ;
    $email->loadEmailer($this->getDbCon(), "regthank") ;
    $email->mBody = $email->stringFusion($email->mBody, $fields) ;
    $email->mBodyHtml = $email->stringFusion($email->mBodyHtml, $fields) ;
    $email->sendMailHtml($fields[Email]) ;
  }
?>