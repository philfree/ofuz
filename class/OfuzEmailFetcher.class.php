<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/ 

    /**
      * OfuzEmailFetcher class.
      * This class extends the base MimeMailParser to implement additional methods
      * The email fetch is entirely based on the 
      * PECL :: Package :: mailparse
      * The package has to be installed prior using the class.
      * @see http://pecl.php.net/package/mailparse
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzCore
      * @license ##License##
      * @version 0.6
      * @date 2010-09-03
      * @since 0.1
      */

class OfuzEmailFetcher extends MimeMailParser{
    
    /**
     * @var $email_to to emails as an array 
    */
    public $email_to = array();

    /**
     * @var $email_from from emails as an array 
    */
    public $email_from = array();

    /**
     * @var $email_cc cc emails as an array 
    */
    public $email_cc = array();

    /**
     * @var $email_bcc bcc emails as an array 
    */
    public $email_bcc = array();

    /**
     * @var $base_header Base Header as an array
    */
    private $base_header = array();

    /**
     * @var $received_story Received Story 
    */
    public $received_story  ;
    
    /**
      * @var $header_string stores the header info as string
    */
    public $header_string = '' ;
    
 
    public function __construct() {
        $this->attachment_streams = array();
    }


    /**
      * Fetch emaildata from a file.
      * @param string $path, the file path 
      * Sets the path and process the base header
    */
    public function fetchEmailText($path){
        $this->setPath($path);
        $this->processBaseHeader();
    }

    /**
      * Fetch emaildata from a rowemail as string
      * @param string $rowEmail
      * Sets the text and process the base header
    */
    public function fetchEmailRow($rowEmail){
        $this->setText($rowEmail);
        $this->processBaseHeader();
    }

    /**
      * Fetch emaildata from a stream(STDIN)
      * @param resource $stream
      * Sets the Stream and process the base header
    */
    public function fetchEmailStream($stream){
        $this->setStream($stream);
        $this->processBaseHeader();
    }
    

    /**
      * Function to process the base header of the email
      * Sets the base header and then parse through it
      * set the received story from the row header
    */
    public function processBaseHeader(){
        $this->setBaseHeader();
        foreach ( $this->base_header  as $info1name => $info1value) {
          if (is_array($info1value)) {
              $header .= "<br /><br />Header: ";
              foreach($info1value as $headername=>$headervalue) {
                    if (is_array($headervalue)) {
                      foreach ($headervalue as $receivename=>$receivevalue) {
                        $receive_story .= "<br /> Receive: ".$receivename.":<br />".$receivevalue;
                      }
                    } else {
                      $header .= "<br />".$headername.":".$headervalue;
                    }
              }
                } else {
              $header .= "<br />".$info1name." = ".$info1value;
            }
          }
          $this->received_story = $receive_story;
          $this->header_string = $header ;
    }

    /**
      * Function setting the base header
      * @see mailparse_msg_get_part_data
      * @see http://php.net/manual/en/function.mailparse-msg-get-part-data.php
    */
    public function setBaseHeader(){
        $this->base_header = mailparse_msg_get_part_data($this->resource);
       
    }
    
    /**
      * Get the base header
      * @return base header of the email
    */
    public function getBaseHeader(){
        return $this->base_header ; 
    }

    /**
      * Get Header as string
      * @return string header
    */
    public function getHeaderString(){
        return $this->header_string ;
    }
    
    /**
      * Function get the email "to"
      * @return array with email 
      * @see mailparse_rfc822_parse_addresses
      * @see http://php.net/manual/en/function.mailparse-rfc822-parse-addresses.php
    */
    public function getToEmail(){
        return mailparse_rfc822_parse_addresses($this->base_header['headers']['to']);
    }

    /**
      * Function get the email "from"
      * @return array with email 
      * @see mailparse_rfc822_parse_addresses
      * @see http://php.net/manual/en/function.mailparse-rfc822-parse-addresses.php
    */
    public function getFromEmail(){
        return mailparse_rfc822_parse_addresses($this->base_header['headers']['from']);
    }

    /**
      * Function get the email "cc"
      * @return array with email 
      * @see mailparse_rfc822_parse_addresses
      * @see http://php.net/manual/en/function.mailparse-rfc822-parse-addresses.php
    */
    public function getCCEmail(){
        return mailparse_rfc822_parse_addresses($this->base_header['headers']['cc']);
    }

    /**
      * Function get the email "bcc"
      * @return array with email 
      * Check the received story for the email id 
      * @see mailparse_rfc822_parse_addresses
      * @see http://php.net/manual/en/function.mailparse-rfc822-parse-addresses.php
    */
    public function getBCCEmail(){
        $regexp = "/for \<(.*?)\>;/i";
        if (preg_match($regexp, $this->received_story, $receive_matches)) { 
          $original_target = $receive_matches[1];
        } 
        if(isset($original_target)){
            return mailparse_rfc822_parse_addresses($original_target);
        }
    }

    /**
      * Function getting the email address from the array "Sqlfusion"<info@sqlfusion.com>
      * @return the email address
    */
    public function getEmailAddress($fullEmailArray){ 
       return $fullEmailArray['address'];
    }

    /**
      * Function getting the email display from the array "Sqlfusion"<info@sqlfusion.com>
      * @return email display
    */
    public function getEmailDisplay($fullEmailArray){
            return $fullEmailArray['display'];
    }


    /**
      * Function saving the email attachment files in the supplied path
      * return array with the file name
    */
    public function saveAttachments($path){
        if($path == '' )return false ;
        
        $return_array = array();
        if($path == '') return false;
        $attachments = $this->getAttachments(); 
        if(count($attachments) > 0 ){
            foreach($attachments as $attachment) {
                // get the attachment name
                $filename = $attachment->filename;
                // write the file to the directory you want to save it in
                if ($fp = fopen($path.$filename, 'w')) {
                  while($bytes = $attachment->read()) {
                    fwrite($fp, $bytes);
                  }
                  fclose($fp);
                }
                $attchment_file['filename'] = $filename;
                $attchment_file['filesize'] = filesize($path.$filename);
                $return_array[] = $attchment_file;
              }
            return $return_array;
        }else{ return false ; }
        
    }

    /**
      * TODO
      * Method to Zip the attachements and save them in the supplied path
      * @param string $path
      * @param array $attachments
      * @return the ZIP filename
     */
    public function zipAttachmentFiles($path,$attachments){ 
        
    }

    
    

}

