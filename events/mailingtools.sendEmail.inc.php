<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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