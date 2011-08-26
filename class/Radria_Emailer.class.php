<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

   /** 
    * Radria_Emailer Class
    * Used to Send Emails using email templates
    * with merge capabilities.
    * 
    * Its compatible with the older Emailer.class.php but its based 
    * on the Zend_Mail
    * http://framework.zend.com/
    *
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @version 2.0.3
    * @package Radria
    * @access public
    */

class Radria_Emailer extends Zend_Mail {
    var $sql_query ;
    var $sql_order ;
    var $cfgEmailField = "email";
    var $cfgTemplateTable = "emailtemplate";
    var $header ;
    var $mBody;
    var $mBodyHtml ;
    var $mSubject ;
    var $mSenderEmail ;
    var $mSenderName ;
    var $file ;
    var $filename ;
    var $has_html = false;
    private $num_sent=0;

   /**
    * setEmailTemplate load an instance of an email message to be sent or merged
    * Loading the template directly from the database should not be used anymore, its keeped for compatiblity 
    * reason.
    * @usage $emailier->setEmailTempalte(new EmailTemplate("email_template_name"));
    * @param mix sqlConnect $conx connexion to the database thrue an sqlConnect object or an EmailTemplate object.
    * @param string $templatename name of the template to load
    */
    function setEmailTemplate($templatename="", $conx=null) {
        if (is_object($templatename) && (get_class($templatename) == "EmailTemplate" || is_subclass_of($templatename, "EmailTemplate"))) { 
            $EmailTemplate = $templatename;
                //echo $EmailTemplate->bodytext;exit;
	        $this->setTemplateBodyText($EmailTemplate->bodytext);
	        $this->setTemplateBodyHtml($EmailTemplate->bodyhtml);

            $this->setTemplateSubject($EmailTemplate->subject);
            //$this->setBody($EmailTemplate->body);
            //$this->setBodyHtml($EmailTemplate->bodyhtml);
            $this->setFrom($EmailTemplate->senderemail, $EmailTemplate->sendername);
            //$this->setFrom($EmailTemplate->sendername, $EmailTemplate->senderemail);
            $this->setHeader();
            return true;
        } else {
          if (is_null($conx)) {  $conx = $GLOBALS['conx']; }
          $qGetTplt = new sqlQuery($conx) ;
          $qGetTplt->query('select * from '.$this->cfgTemplateTable.' where name=\''.$templatename.'\'') ;
          if ($qGetTplt->getNumRows() == 1) {
            $data = $qGetTplt->fetch() ;
            $this->setTemplateSubject($data->subject) ;
            $this->setTemplateBodyText($data->bodytext) ;
            $this->setTemplateBodyHtml($data->bodyhtml) ;
            $this->setFrom($data->senderemail, $data->sendername) ;
	    $this->setHeader();
            return true;
          } else { return false; }
        }
	//return $this;
    }

    function loadEmailer($conx, $templatename) {
        $this->setEmailTemplate($templatename, $conx);
    }

   /**
    * Set a file to be attach to the email message.
    * @param sqlConnect $conx connexion to the database thrue an sqlConnect object
    * @param string $templatename name of the template to load
    */
    function attachFile($file, $filename) {
        $file_content = file_get_contents($file); 
        $at = $this->createAttachment($file_content);
        // $at->type        = 'image/gif';
        // $at->disposition = Zend_Mime::DISPOSITION_INLINE;
        // $at->encoding    = Zend_Mime::ENCODING_8BIT;
        $at->filename    = $filename;
    }

    /**
     * Set the sender of the email message
     * This is deprecate use setFrom() instead. 
     * @param string $name name of the sender
     * @param string $email email of the sender
     * @deprecate
     * @see setFrom()
     */
    function setSender($name, $email) {
        $this->setFrom($email, $name); 
    }

    /** 
     *  Set the Header
     *  it create the header of the message if the sender is set.
     *  @return bool true if the sender was set and header set correctly
     */
    function setHeader() {
        $this->addHeader('X-MailGenerator', 'SQLFusion Radria abuse@sqlfusion.com');
        //if (strlen($this->mSenderEmail) > 0 && strlen($this->mSenderName)>0) {
        //    $this->header = "X-Mailer: SQLFusion abuse@sqlfusion.com\nReturn-Path: ".$this->mSenderEmail." \nFrom: $this->mSenderName <".$this->mSenderEmail.">" ;
        //    return true ;
        //} else {
        //  return false ;
        //}
    }

    /**
     * Merge and send an email message with the content of
     * an executed sqlQuery .
     * This one is deprecate, use the sendMergeQuery() instead.
     *
     *  @param sqlConnect $query executed sqlQuery
     *  @deprecated
     *  @see getField()
     */
    function fusion($query) {
        $this->numsent = 0;
        set_time_limit(3600);
        while ($row = $query->fetchArray()) {
            $this->numsent++;
            $fields = MergeString::getField($this->mBody) ;
            $fields = MergeString::getField($this->mBodyHtml) ;
            $SendBody = $this->mBody;
            $SendBodyHtml = $this->mBodyHtml ;
            $SendSubject = $this->mSubject;
            if (is_array($fields)) {
                foreach($fields as $field) {
                    $SendBody = eregi_replace("\[$field\]", $row[$field], $SendBody) ;
                    $SendBodyHtml = eregi_replace("\[$field\]", $row[$field],  $SendBodyHtml) ;
                    $SendSubject = eregi_replace("\[$field\]", $row[$field], $SendSubject) ;
                }
            }
            $SendBody = ereg_replace("\r","", $SendBody);
            $this->setHeader() ;
            if (strlen($SendBodyHtml)>5) {
                $this->sendMailHtml($row[$this->cfgEmailField], $SendSubject, $SendBody, $SendBodyHtml, $this->header) ;
            } elseif (strlen($this->file) > 0 && strlen($this->filename) > 0) {
                $this->sendMailJoin($row[$this->cfgEmailField], $SendSubject, $SendBody, $this->file, $this->filename, $this->header) ;
            } else {
                $this->sendMailStandard($row[$this->cfgEmailField], $SendSubject, $SendBody, $this->header) ;
            }
            //if (is_integer($numsent/10)) { echo ".";}
            //if (is_integer($numsent/100)) {echo $numsent." <br>\n"; }
        }
        return $this->numsent;
    }
    /**
     *  mergeQuery()
     *  Does the same as fusion() but using the new naming convention for consistancy
     *  with mergetArray(); 
     *  Its a more elegant got but with a small performance hit.
     *  fusion() is just keeped for high performance needs.
     * 
     * @param $query an sqlQuery object
     * @see mergeArray()
     */
    function sendMergeQuery(sqlQuery $query) {
        if (is_object($query) && is_resource($query->getResultSet()))  {
        $this->numsent = 0;
        while ($row = $query->fetchArray()) {
            set_time_limit(360);
            $this->numsent++;
            $this->mergeArray($row);
            $this->addTo($row[$this->cfgEmailField]);
            $this->send();
        }
        return $this->numsent ;
        } else {
            $this->setError("\n Query parameter is not an object or an executed sql query with a valide result set, merge mail can't be done");
        }
    }
    
    /**
     * mergeArray()
     * Merge an Array with a currently loaded email template
     * @param $fields_values Array of fields in format $fields['fieldname']=value;
     */
    function mergeArray($fields_values) {
        $this->setBodyText(MergeString::withArray($this->getTemplateBodyText(), $fields_values)) ;
        if (strlen($this->getTemplateBodyHtml()) > 5) {
            $this->setBodyHtml(MergeString::withArray($this->getTemplateBodyHtml(), $fields_values));
        }
        $this->setSubject(MergeString::withArray($this->getTemplateSubject(), $fields_values));
    }
   

    /**
     * sendMergeArray()
     * if the email parameter is not set
     * the array parameter requires an email key with the email to send 
     * the message to. 
     * @param array $field_value  
     */
    function sendMergeArray($fields_values, $email='') {
       if (empty($email)) { $email = $fields_values['email']; }
       if (empty($email)) { 
                    $this->setError("sendMergeArray: No email address found in both parameters");
                    return false; 
		   }
		$this->mergeArray($fields_values) ;
		$this->addTo($email);
		$this->numsent++;
		$this->send();
        $this->cleanup();
    }
   /**
    * cleanup
    * Cleanup a message so an other message can be sent.
    */

    function cleanup() {
        $this->clearSubject();
	$this->clearRecipients();
	//$this->clearBodyText();
	//$this->clearBodyHtml();
    }

   /**
    * eventSendTemplateEmail
    * eventaction that uses a template to send an email.
    * requires template name, email to send to and optional array of data to merge.
    * @param email string with email addrss
    * @param email_template name of the template to load
    * @param fields array for fields with value to merge with the email template.
    */

    function eventSendTemplateEmail(EventControler $eventcontroler) {
       $fields = $eventcontroler->fields;
       $email = '';
       if (strlen($eventcontroler->email)> 0) {
          $email = $eventcontroler->email;
       } elseif(strlen($fields['email'])>0) {
          $email = $fields['email'];
       }
       if (!empty($email) && strlen($eventcontroler->email_template)>0) {
	      $this->setEmailTemplate(new EmailTemplate($eventcontroler->email_template));
          if (is_array($fields)) {
		     $this->mergeArray($fields);
	      }
	      $this->addTo($email);
	      $this->send();
       }
    }

    function getNumberOfEmailSent() {
	return $this->numsent;
    }

    function setTemplateBodyText($body) {
        $this->mBody = $body;
    }
    function setTemplateBodyHtml($bodyhtml) {
        $this->mBodyHtml = stripslashes($bodyhtml);
    }
    function setTemplateSubject($subject) {
        $this->subject = $subject;
    }
    function getTemplateBodyText() {
        return $this->mBody;
    }
    function getTemplateBodyHtml() {
        return $this->mBodyHtml;
    }
    function getTemplateSubject() {
        return $this->subject;
    }


// From here to down the end are method from the oldclass that should be obsolete by now.
// do not use them as we will remove them in future version.
  /**
   * Load the field in the field attribute from the HTML template.
   * get Table Field could be used instead but it will not get the
   * extra fields and multiple tables fields
   * @param String $template HTML template (row, header, footer) where there is fields to be used
   * @access public
   * @return Array $fields indexed on the field name.
   */
    function getField($template) {
        while (ereg('\[([^\[]*)\]', $template, $fieldmatches)) {
          $fields[] = $fieldmatches[1];
          $template = str_replace($fieldmatches[0], "", $template) ;
        }
        return $fields ;
    }

    /**
     * The secret sauce.
     * Take a string, extract the fields in [] and replace the fields in [] with
     * their respective values from the $values array.
     * @param string with fields to merge in []
     * @param array $values array with format $values[field_name] = $value
     * @return string merged
     */
    function stringFusion($thestring, $values) {
        $fields = $this->getField($thestring) ;
        if (is_array($fields)) {
          foreach ($fields as $field) {
            $thestring = str_replace('['.$field.']', $values[$field], $thestring) ;
          }
        }
        return $thestring;
    }
    
    /**
     *  Check if the email as an existing domain name.
     * @param string $email email to check
     */
    function validateEmail ( $email )   {
        list ( $user, $domain )  = split ( "@", $email, 2 );
        if ( checkdnsrr ( $domain, "ANY" ) ) {
            $return = true ;
        } else {
            $return = false ;
        }
        return $return;
    }

// Bellow are keeped for compatibility reason
    /**
     *  Send an email with a file attach
     *  @param string $email
     *  @param string $subject
     *  @param string $body
     *  @param string $file
     *  @param string $filename
     *  @param string $headini
     */

    function sendMailJoin($email, $subject="", $body="", $file="", $filename="", $headini="") {
        if (!empty($subject)) {
            $this->setSubject($subject);
        }
        if (!empty($body)) {
            $mail->setBodyText($body);
        }
        if (!empty($headini)) {
            if (!is_array($headini)) {
              // todo, fixme. take the string and change it to array: email, name
            }
            $this->setFrom($headini[0], $headini[1]) ;
        }
        if (!empty($bodyhtml)) {
            $mail->setBodyHtml($bodyhtml);
        }

        //if (empty($file)) {
        //  $file = $this->file ;
       // }
        //if ($filename == "") {
        //    $filename = $this->filename ;
       // }

        $mail->addTo($email);
        $mail->send();
        return "vide" ;
    }

    /**
     *  Send a standard text email
     *  @param string $email
     *  @param string $subject
     *  @param string $body
     *  @param string $header
     */
    function sendMailStandard($email, $subject="", $body="", $header="") {
        if (!empty($subject)) {
            $this->setSubject($subject);
        }
        if (!empty($body)) {
            $mail->setBodyText($body);
        }
        if (!empty($header)) {
            if (!is_array($header)) {
              // todo, fixme. take the string and change it to array: email, name
            }
            $this->setFrom($header[0], $header[1]) ;
        }
        $mail->addTo($email);
        $mail->send();
        return "vide" ;
    }


    /**
     *  Send an email with a text and html version
     *  @param string $email
     *  @param string $subject
     *  @param string $body
     *  @param string $bodyhtml
     *  @param string $headini
     */
    function sendMailHtml($email, $subject="", $body="", $bodyhtml="", $headini="") {
        if (!empty($subject)) {
            $this->setSubject($subject);
        }
        if (!empty($body)) {
            $mail->setBodyText($body);
        }
        if (!empty($headini)) {
            if (!is_array($headini)) {
              // todo, fixme. take the string and change it to array: email, name
            }
            $this->setFrom($headini[0], $headini[1]) ;
        }
        if (!empty($bodyhtml)) {
            $mail->setBodyHtml($bodyhtml);
        }
        $mail->addTo($email);
        $mail->send();

        return "vide" ;
    }


    function prependBody($str) {
        $this->mBody = $str."\n".$this->mBody;
    }

    function prependBodyHtml($str) {
        $this->mBodyHtml = $str."\n".$this->mBodyHtml;
    }

    function appendBody($str) {
        $this->mBody .= $str;
    }

    function appendBodyHtml($str) {
        $this->mBodyHtml .= $str;
    }

    /**
     * setBody
     * Deprecate use setBodyText() instead.
     */
    function setBody($bodytxt) {
        $this->setBodyText($bodytxt);
    }
    function getBody() {
        return $this->getBodyText();
    }
    function hasHtml($bool=NULL) {
        if (is_null($bool)) {
            return $this->has_html;
        } else {
            $this->has_html = (bool)$bool;
        }
        
    }
}
?>
