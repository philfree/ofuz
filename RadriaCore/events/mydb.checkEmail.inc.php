<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt

/**   Event Mydb.checkEmail
  *
  * This Event Check if the domain of the email exists.
  * If not it sets the doSave param at "no" to block the save and
  * Call the message page.
  * checkdnsrr not supported on windows need to check OS before executing.
  * <br>- param array fields containing email value
  * <br>- param array emailfield containing the name of the forms email fields.
  * <br>Option :
  * <br>- param string errorpage page to display the errors
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.0
  */

/*  French version
  $strWrongEmail = "L'email que vous avez saisie n'exist pas" ;
 */
 global $strWrongEmail;
 if (!isset($strWrongEmail)) {
   $strWrongEmail = "The email you entered doesn't exist" ;
 }
 
  global $HTTP_SERVER_VARS;
    if ($submitbutton != "Cancel") {
        if (isset($emailfield)) {
            $nbremail = count($emailfield) ;
            for ($i=0; $i<$nbremail; $i++) {
                $tmp_emailname = $emailfield[$i] ;
                if (strlen($fields[$tmp_emailname]) > 0) {
                    list ( $user, $domain )  = explode ( "@", $fields[$tmp_emailname] );
                    if (!eregi("Win32", $HTTP_SERVER_VARS["SERVER_SOFTWARE"])) {
                        if (strlen($domain) > 0 && strlen($user)>0) {
                            if (!(checkdnsrr ( $domain, "ANY" ))) {
                                    if (strlen($errorpage)>0) {
                                            $urlerror = $errorpage;
                                    } else {
                                            $urlerror = $this->getMessagePage() ;
                                    }
                                    $disp = new Display($urlerror);
                                    $disp->addParam("message", $strWrongEmail) ;
                                    $this->setDisplayNext($disp) ;
                                    $this->updateParam("doSave", "no") ;
                            }
                        }
                    }
                }
            }
            reset($emailfield);
        }
    }

?>