<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

   /** 
    *  Emailer Class
    * Used to Send Emails
    * Support fusion and mass mail with standard and HTML format.
    * Features :
    *  Mass mailing
    *  HTML and TEXT Email message
    *  Attach file to an email message
    * Check if an email as a valid domain name.
    *
    * Note : you can't attach a file to an HTML email.
    *
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @version 2.0.3
    * @package MyDB
    * @access public
    */

class Emailer {
    var $sql_query ;
    var $sql_order ;
    var $cfgEmailField = "email";
    var $cfgTemplateTable = "emailtemplate";
 //   var $headini ;
    var $header ;
    var $mBody;
    var $mBodyHtml ;
    var $mSubject ;
    var $mSenderEmail ;
    var $mSenderName ;
    var $file ;
    var $filename ;
    var $has_html = false;

 /**
   * Constructor, create a new instance of an Emailer.
   * @param string $subject subject of the email  message
   * @param string $body body of the email message
   * @param string $bodyhtml html version of the body of the email message
   * @access public
   */
    function Emailer($subject="", $body="", $bodyhtml="") {
        $this->mSubject = $subject ;
        $this->mBody = $body ;
        if (!empty($bodyhtml)) { 
        $this->mBodyHtml = $bodyhtml ;
        $this->hasHtml(true);
        }
    }

   /**
    * LoadEmailer load an instance of an email message to be sent.
    * @param mix sqlConnect $conx connexion to the database thrue an sqlConnect object or an EmailTemplate object.
    * @param string $templatename name of the template to load
    */
    function loadEmailer($conx, $templatename) {
        if (get_class($conx) == "EmailTemplate") {
            $EmailTemplate = $conx;
            $this->setSubject($EmailTemplate->subject);
            $this->setBody($EmailTemplate->body);
            $this->setBodyHtml($EmailTemplate->bodyhtml);
            $this->setSender($EmailTemplate->sendername, $EmailTemplate->senderemail);
        } else {
          $qGetTplt = new sqlQuery($conx) ;
          $qGetTplt->query("select * from $this->cfgTemplateTable where name='$templatename'") ;
          if ($qGetTplt->getNumRows() == 1) {
            $data = $qGetTplt->fetch() ;
            $this->setSubject($data->subject) ;
            $this->setBody($data->bodytext) ;
            $this->setBodyHtml($data->bodyhtml) ;
            $this->setSender($data->sendername,$data->senderemail) ;
          }
        }
    }

   /**
    * Set a file to be attach to the email message.
    * @param sqlConnect $conx connexion to the database thrue an sqlConnect object
    * @param string $templatename name of the template to load
    */
    function attachFile($file, $filename) {
        $this->file = $file ;
        $this->filename = $filename ;
    }

    /**
     * Set the sender of the email message
     * This method must be used before using setHeader()
     * @param string $name name of the sender
     * @param string $email email of the sender
     */
    function setSender($name, $email) {
        $this->mSenderName = $name ;
        $this->mSenderEmail = $email ;
        $this->setHeader() ;
    }

    /** 
     *  Set the Header
     *  it create the header of the message if the sender is set.
     *  @return bool true if the sender was set and header set correctly
     */
    function setHeader() {
        if (strlen($this->mSenderEmail) > 0 && strlen($this->mSenderName)>0) {
            $this->header = "X-Mailer: SQLFusion abuse@sqlfusion.com\nReturn-Path: ".$this->mSenderEmail." \nFrom: $this->mSenderName <".$this->mSenderEmail.">" ;
            return true ;
        } else {
          return false ;
        }
    }

    /**
     * Merge and send an email message with the content of
     * an executed sqlQuery .
     *
     *  @param sqlConnect $query executed sqlQuery
     *  @see getField()
     */
    function fusion($query) {
        $numsent = 0;
        set_time_limit(3600);
        while ($row = $query->fetchArray()) {
            $numsent++;
            $fields = $this->getField($this->mBody) ;
            $fields = $this->getField($this->mBodyHtml) ;
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
        return $numsent;
    }
    /**
     *  mergeQuery()
     *  Alias to fusion but using the new naming convention for consistancy
     *  with mergetArray();
     * @param $query an sqlQuery object
     * @see mergeArray()
     */
    function mergeQuery($query) {
        $this->fusion($query);
    }
    
    /**
     * mergeArray()
     * Merge an Array with a currently loaded email template
     * @param $fields Array of fields in format $fields['fieldname']=value;
     */
    function mergeArray($fields) {
        $this->setBody($this->stringFusion($this->getBody(), $fields)) ;
        if (strlen($this->getBodyHtml()) > 5) {
            $this->setBodyHtml($this->stringFusion($this->getBodyHtml(), $fields));
        }
        $this->setSubject($this->stringFusion($this->getSubject(), $fields));
    }
    
    
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

    function stringFusion($thestring, $values) {
        $fields = $this->getField($thestring) ;
        if (is_array($fields)) {
          while(list($key, $field) = each($fields)) {
            $thestring = str_replace("[$field]", $values[$field], $thestring) ;
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
        if ($subject == "") {
            $subject = $this->mSubject  ;
        }
        if ($body == "") {
          $body = $this->mBody ;
        }
        if ($headini == "") {
            $headini = $this->header ;
        }
        if ($file == "") {
          $file = $this->file ;
        }
        if ($filename == "") {
            $filename = $this->filename ;
        }
        $header = $headini ;
        $header .="\nMIME-Version: 1.0" ;
        $header .= "\nContent-Type: multipart/mixed;" ;
        $header .=" boundary=\"---------_nWlrBbmQBhCDarzOwKkYHIDdqSCD\"" ;
        $filestring = "";
        $fp = fopen ($file, "r") ;
        $filestring = fread($fp, filesize( $file) ) ;
        fclose ($fp) ;
        $filebase64 = chunk_split(base64_encode($filestring)) ;
        $startbody = "\nThis is a multi-part message in MIME format." ;
        $startbody .= "\n-----------_nWlrBbmQBhCDarzOwKkYHIDdqSCD" ;
        $startbody .= "\nContent-Type: text/plain;";
        $startbody .= "\nContent-Transfer-Encoding: 8bit\n\n" ;
        $body = $startbody.$body ;
        $body .= "\n-----------_nWlrBbmQBhCDarzOwKkYHIDdqSCD" ;
        $body .= "\nContent-Type: application/unknown; name=\"".$filename."\"" ;
        $body .= "\nContent-Transfer-Encoding: base64" ;
        $body .="\nContent-Disposition: attachment; filename=\"".$filename."\"\n" ;
        $body .="\n".$filebase64 ;
        $body .="\n-----------_nWlrBbmQBhCDarzOwKkYHIDdqSCD--" ;
        mail ($email, $subject, $body, $header) ;
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
        if ($subject == "") {
            $subject = $this->mSubject  ;
        }
        if ($body == "") {
          $body = $this->mBody ;
        }
        if ($header == "") {
            $header = $this->header ;
        }
        mail ($email, $subject, $body, $header) ;
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
        if ($subject == "") {
            $subject = $this->mSubject  ;
        }
        if ($body == "") {
          $body = $this->mBody ;
        }
        if ($headini == "") {
            $headini = $this->header ;
        }
        if ($bodyhtml == "") {
          $bodyhtml = $this->mBodyHtml ;
        }
        $header = $headini ;
        $header .="\nMIME-Version: 1.0" ;
        $header .= "\nContent-Type: multipart/alternative;" ;
        $header .=" boundary=\"---------_nWlrBbmQBhCDarzOwKkYHIDdqSCD\"" ;
        $startbody = "\n\n\n-----------_nWlrBbmQBhCDarzOwKkYHIDdqSCD" ;
        $startbody .= "\nContent-Type: text/plain; charset=\"iso-8859-1\";";
        $startbody .= "\nContent-Transfer-Encoding: 8bit\n\n" ;
        $body = $startbody.$body ;
        $body .= "\n\n\n-----------_nWlrBbmQBhCDarzOwKkYHIDdqSCD" ;
        $body .= "\nContent-Type: text/html; charset=\"iso-8859-1\";" ;
        $body .= "\nContent-Transfer-Encoding: 8bit" ;
        $body .="\n";
        $body .="\n".$bodyhtml ;
        $body .="\n\n-----------_nWlrBbmQBhCDarzOwKkYHIDdqSCD--" ;
        mail ($email, $subject, $body, $header) ;
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
    function setBody($bodytxt) {
        $this->mBody = $bodytxt;
    }
    function getBody() {
        return $this->mBody;
    }
    function setBodyHtml($bodyhtml) {
        $this->mBodyHtml = $bodyhtml;
    }
    function getBodyHtml() {
        return $this->mBodyHtml;
    }
    function setSubject($subject) {
        $this->mSubject = $subject;
    }
    function getSubject() {
        return $this->mSubject;
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
