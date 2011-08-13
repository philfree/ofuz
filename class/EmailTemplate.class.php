<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

   /** 
    * EmailTemplate
    * 
    * Object that store an email template
    * for reuse and mail merge.
    *  Here is an exemple:
	*  $do_template = new EmailTemplate();
	*  $do_template->senderemail = "philippe@sqlfusion.com";
	*  $do_template->sendername = "Philippe Lewicki";
	*  $do_template->subject = "This is an example";
	*  $do_template->bodytext = "This is the content of the sample message";
	*  $do_template->bodyhtml = nl2br($do_template->bodytext);
	* 
	*  An other example more OO / stylish
	*  $do_template = new EmailTemplate();
	*  $do_template->setFrom("phil@sqlfusion.com", "Philippe Lewicki")
	*              ->setSubject("This is an example")
	*              ->setMessage("This is the content of the sample message");
	* 
    */
    
class EmailTemplate extends DataObject {
    public $table = "emailtemplate";
    protected $primary_key = "idemailtemplate";
    private $lang="en_US";
    private $fallback_language="en_US";

    public function __construct($email_template_name="") {
        parent::__construct();
	if ($GLOBALS['cfg_lang']) {
	   $this->setLanguage($GLOBALS['cfg_lang']);
	}
        if (!empty($email_template_name)) {
            $this->query("select * from ".$this->getTable()." where name='".$this->quote($email_template_name)."' and language='".$this->getLanguage()."'") ;
			if ($this->getNumRows() == 0) {
				   $this->query("select * from ".$this->getTable()." where name='".$this->quote($email_template_name)."' and language='".$this->fallback_language."'") ;
			}
			if ($this->getNumRows() == 0) {
				   $this->setError("Template ".$email_template_name." not found");
			}
        }       
    }
    /**
     * Function to set to overwrite the default sender email
     * Usefull when the From is dynamic.
     * @param $email of the sender
     * @see setSenderName()
     */
    public function setSenderEmail($email) {
        $this->senderemail = $email;
        return $this;
    }
    /**
     * function to set the name of the sender
     * @param $name of the sender
     * @see setSenderEmail();
     */
    public function setSenderName($name) {
        $this->sendername = $name;
        return $this;
    }
	
	/**
	 *  setFrom
	 *  method to mimic the Zend_Mail 
	 *  for usability and will set on the emailtemplate
	 *  the email and name of the sender.
	 *  @param string email of the sender
	 *  @param string name of the sender
	 *  @return EmailTemplate object
	 */
	public function setFrom($email, $name) {
		$this->senderemail = $email;
		$this->sendername = $name;
		return $this;
	}

    /**
	 *  setBodyHtml
	 *  method to mimic the Zend_Mail
	 *  for usability and will set on the emailtemplate the 
	 *  bodyhtml of the message.
	 *  @param string body of the message in HTML
	 *  @return EmailTemplate object
	 */
    public function setBodyHtml($bodyhtml) {
		$this->bodyhtml = $bodyhtml;
		return $this;
	}
	
	/**
	 *  setBodyText
	 *  method to mimic the Zend_Mail
	 *  for usability and will set on the emailtemplate the 
	 *  bodytext of the message.
	 *  @param string body of the message in text
	 *  @return EmailTemplate object
	 */
	public function setBodyText($bodytext) {
		$this->bodytext = $bodytext;
		return $this;
	}

	/**
	 *  setSubject
	 *  method to mimic the Zend_Mail
	 *  for usability and will set on the emailtemplate the 
	 *  subject of the message.
	 *  @param string subject of the message
	 *  @return EmailTemplate object
	 */
	 public function setSubject($subject) {
		 $this->subject = $subject;
		 return $this;
	 }
		 	

    /**
	 *  setMessage 
	 *  set a text message on the template object.
	 *  @param $body is the core message of the email in text
	 *  @return EmailTemplate object
	 */ 
    public function setMessage($body) {
		$this->bodytext = $body;
		$this->bodyhtml = nl2br(htmlentities($body));
		return $this;
	}

    public function setLanguage($lang) {
	$this->lang = $lang;
    }
    public function getLanguage() {
        return $this->lang;
    }
    public function setTemplateName($name) {
        $this->name = $name;
    }
    public function getTemplateName() {
        return $this->name;
    }
}

?>
