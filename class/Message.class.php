<?php

// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC,  info@sqlfusion.com
 /**COPYRIGHTS**/ 


  /**
   * Class Message
   * To manage messages that will display in the site. 
   * it Work with the i18n functions to multilanguage messages.
   * 
    table: message 
    <pre>
	+----------------+--------------+------+-----+---------+----------------+
	| Field          | Type         | Null | Key | Default | Extra          |
	+----------------+--------------+------+-----+---------+----------------+
	| idmessage      | int(10)      | NO   | PRI | NULL    | auto_increment | 
	| key_name       | varchar(150) | NO   |     |         |                | 
	| content        | mediumtext   | NO   |     |         |                | 
	| language       | varchar(50)  | NO   |     |         |                | 
	| context        | varchar(60)  | NO   |     |         |                | 
	| can_close      | varchar(3)   | NO   |     | yes     |                | 
	| close_duration | varchar(20)  | NO   |     | 1 month |                | 
	| plan           | varchar(10)  | YES  |     | all     |                | 
	+----------------+--------------+------+-----+---------+----------------+
    </pre>

    table: message_user
    <pre>
	+----------------+--------------+------+-----+---------+----------------+
	| Field          | Type         | Null | Key | Default | Extra          |
	+----------------+--------------+------+-----+---------+----------------+
	| idmessage_user | int(10)      | NO   | PRI | NULL    | auto_increment | 
	| key_name       | varchar(150) | NO   | MUL |         |                | 
	| iduser         | int(10)      | NO   |     |         |                | 
	| closed_until   | datetime     | YES  |     | NULL    |                | 
	+----------------+--------------+------+-----+---------+----------------+
    </pre>
   *
   * The messages are stored in the database and the Message object will display them.
   * It will fully format the HTML arround it and can also be merge with an array of data, like 
   * with the email template.
   * 
   * The closing of the Messages is recorded in the message_user. They have an expiration date
   * in the closed_until variable. Passed that date the message will display again.
   * The close duration from from the message and its the "interval" format from the date_add in mysql.
   * This mean the message.close_duration can be: 1 month, 2 week, 5 day, 20 year.....
   * (yes, its singulare don't ask).
   * 
   * Here is an example:
   <pre>
     idmessage: 3
      key_name: welcome client portal
       content: [user_firstname] [user_lastname] has opened you an access to [user_company] portal and would like to share files and messages with you.<br>
Bookmark this page so you can come back to it anytime you need to send a message or upload a file for [user_firstname]
      language: en_US
       context: 
     can_close: no
close_duration: 1 month
   </pre>
   *  
   *  To display this message:
   <code>
		$msg = new Message();
		$msg->setData(Array("user_firstname" => $do_user->firstname, 
							"user_lastname" => $do_user->lastname,
							"user_company" => $do_user->company));
		$msg->getMessage("welcome client portal");
		echo $msg->displayMessage() ;
    </code>
   * 
   *  When the variable message->can_close = 'yes' the message will display a little close link & icon. 
   *  if set to 'no' then the message cannot be closed.
   * 
   *  A usefull feature is the context feature.
   *  You can group messages on context and display them in multiple places.
   *  All messages with the same context will display randomly. 
   *  When a message as been close if there is an other message for that context
   *  it will only display the next time the user logs in.
   *  Example of message by context:
    <code>
		$msg = new Message(); 
		if ($msg->getMessageFromContext("project tasks")) {
			echo $msg->displayMessage();
		}
    </code>
   * 
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @package OfuzCore
   * @license ##License##
   * @version 0.6
   * @date 2010-09-03
   * @since 0.2
   */


class Message extends DataObject {
    public $table = "message";
    public $primary_key = "idmessage";
    private $lang = "en_US";
    private $fallback_language="en_US";
    private $message_data = Array();

    function __construct(sqlConnect $conx=NULL, $table_name="") {
      if (isset($GLOBALS['cfg_lang'])) { 
          $this->setLanguage($GLOBALS['cfg_lang']);
      }
      parent::__construct($conx, $table_name);
      if (RADRIA_LOG_RUN_OFUZ) {
            $this->setLogRun(OFUZ_LOG_RUN_MESSAGE);
      } 
    }

    /**
     * setData
     * Add an array of data to merge with the message
     */

    function setData(Array $datas) {
       $this->message_data = $datas;
    }
  
    /**
     * setContent
     * set the content of the message to display.
     * This method can be used when displaying a message not 
     * already stored in the database.
     * For security reason this method will convert all htmlentities and remove slashes.
     * 
     * @param string $content of the message.
     * @see getMessage()
     */ 
    function setContent($content) {
        $this->values['content'] = htmlentities(stripslashes($content));
        $this->values['idmessage'] = 1;
        $this->values['can_close'] = 'no';
        $this->values['key_name'] = '';
    }

    /** 
      *  Get Random message
      *  Pass an array of message id and one is pick at random
      * @param array messages of messages
      * @return a message
      */
    function getRandomMessage(Array $messages_key, $lang='') {
        $message_id = rand(count($messages_key));
        return $this->getMessage($messages_key[$message_id], $lang);
    }

    /**
      * getMessageFromContext
      * Select a message from a context
      * @param string context name of the context
      * @return content of the message
      */
    function getMessageFromContext($context) {
        if ($_SESSION['message_closed'][$context] === true) { return false; }
        $this->query("SELECT * 
          FROM ".$this->getTable()." LEFT JOIN message_user 
              on (".$this->getTable().".key_name=message_user.key_name AND message_user.iduser=".$_SESSION['do_User']->iduser.") 
          WHERE context='".$context."' 
          AND message.language='".$this->lang."'
          AND (message.plan='".$_SESSION['do_User']->plan."' 
            OR message.plan='all')
          AND (closed_until < now() OR closed_until is null)");
          $this->setLog("\nQuery from context:".$this->getSqlQuery());
        if ($this->getNumRows() > 1) {
            $message_to_display = rand(0,$this->getNumRows()-1);
            $this->setCursor($message_to_display);
        } 
        $content = "";
        if ($this->getNumRows() > 0) {
            if (!empty($this->message_data)) {
                $content = MergeString::withArray($this->content, $this->message_data);
                return $content;
            } else {
                return $this->content;
            }
        } else { 
            $_SESSION['message_closed'][$context] = true;
            return false;
        }				
    }


    /**
     * getMessage 
     * get message using message  key string
     */
    function getMessage($message_key, $lang='') {
      if (empty($lang)) { $lang = $this->getLanguage(); } 
      $this->query("SELECT * FROM ".$this->getTable()." where key_name='".$this->quote($message_key)."' AND language='".$lang."'");
      $content = "";
      if ($this->next()) {
          if (!empty($this->message_data)) {
              $this->content = MergeString::withArray($this->content, $this->message_data);
              return $this->content;
          } else {
              return $this->content;
          }
      } else { 
          $this->query("SELECT * FROM ".$this->getTable()." where key_name='".$this->quote($message_key)."' AND language='".$this->fallback_language."'");
          if ($this->next()) {
              if (!empty($this->message_data)) {
                  $this->content = MergeString::withArray($this->content, $this->message_data);
                  return $this->content;
              } else {
                  return $this->content;
              }
          } else {
              return false;
          }
       }
    }
    /**
    * isClosed
    * Method to check is the user as closed the message box in
    * the last 2 month.
    * It run the query only if the session var was not set.
    * This is used by displaymessage to not display the message again
    */
    function isClosed($message_key, $do_user) {
        if ($_SESSION['message_closed'][$message_key] === true) {
            return true;
        } else {
            $this->query("SELECT closed_until FROM message_user 
                          WHERE key_name='".$message_key."' 
                              AND iduser=".$do_user->iduser." 
                              AND closed_until > now()");
            $this->setLog("\n Query if is closed:".$this->getSqlQuery());
            if($this->getNumRows() > 0) { 
                $_SESSION['message_closed'][$message_key] = true;
                return true; 
            } else { 
                return false; 
            } 		
        }			  
    }

    function displayMessage($message_key='', $lang='') {
        echo $this->getFormatedMessage($message_key, $lang);
    }

    /**
     * displayMessage
     * echo the message with the default styles
     */
    function getFormatedMessage($message_key='', $lang='') {
        $html_message ='';
        if (!$this->hasData() && !empty($message_key)) {
            $this->getMessage($message_key, $lang);
        }
        if ($this->hasData()) {
            if ($this->idmessage > 0) {
                                $display = false;
                                if (!isset($_SESSION['do_User'])) { $display = true; }
                                if ($this->can_close != "yes") { 
                                        $display = true; 
                                } else {
                        if (!$this->isClosed($this->key_name, $_SESSION['do_User'])) {
                                              $display = true;
                                        }
                                }
                                if ($display) {
                    $html_message .= '<div id="message_'.$this->idmessage.'" class="message">';

                    $html_message .= "    ".$this->content;
                    if ($this->can_close == "yes") {
                        $html_message .= '    <div class="delete_icon" style="position:absolute;top:3px;right:10px;">';
                        $html_message .= '        <a onClick="close_message('.$this->idmessage.');" href="#">'._('close').' <img class="delete_icon_tag" border="0" alt="Close Message" src="/images/delete.gif"></a>';
                        $html_message .= ' 	  </div>';
                    }
                    $html_message .= '</div>';
                }
            }
        }
        return $html_message;
    }
	

    public function eventAjaxCloseMessage(EventControler $event_controler) {
		$this->getId($event_controler->idmessage);
		$q_check = new sqlQuery($this->getDbCon());
		$q_check->query("select idmessage_user from message_user where key_name='".$this->key_name."' and iduser=".$_SESSION['do_User']->iduser);
		$q_set = new sqlQuery($this->getDbCon());
		//$this->setLogRun(true);
		if ($q_check->getNumRows() > 0) {
			$q_check->fetch();
			$q_set->query("update message_user set closed_until=date_add(now(), interval ".$this->close_duration.") where idmessage_user=".$q_check->getData("idmessage_user"));
		} else {
			$q_set->query("insert into message_user values ('', '".$this->key_name."', ".$_SESSION['do_User']->iduser.", date_add(now(), interval ".$this->close_duration."))");
		}
		//$this->setLog($q_set->getSqlQuery()); 
		$_SESSION['message_closed'][$this->key_name] = true;
		$q_check->free();
		$q_set->free();
		 
	}

    public function setLanguage($lang) {
            $this->lang = $lang;
    }
    public function getLanguage() {
            return $this->lang;
    }
    public function setCanClose($yes='yes') {
		$this->can_close = $yes;
	}

}