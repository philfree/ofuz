<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

require_once 'Zend/Oauth/Consumer.php';
require_once 'Zend/Service/Twitter.php';

/**
 * Twitter class
 * Using the DataObject
 */

class OfuzTwitter extends DataObject {

    public $table = 'twitter_account';
    protected $primary_key = 'idtwitter_account';

    /**
     * Checks if user has integrated their Ofuz account with Twitter.
     * The same process could be done by calling getAccessToken() below and unserializing
     * but I thought this method would be easier for newbies to understand.
     */
    function checkTwitterIntegration() {
        if ($_SESSION['do_User']->iduser) {
            $q = new sqlQuery($this->getDbCon());
            $q->query("SELECT tw_screen_name FROM twitter_account WHERE iduser=".$_SESSION['do_User']->iduser);
            if ($q->getNumRows() > 0) {
      	        $q->fetch();
		        return $q->getData('tw_screen_name');
            }
        }
        return false;
    }

    /**
     * Get user's Twitter Access Token
     */
    function getAccessToken() {
        if ($_SESSION['do_User']->iduser) {
            $q = new sqlQuery($this->getDbCon());
            $q->query("SELECT tw_token FROM twitter_account WHERE iduser=".$_SESSION['do_User']->iduser);
            if ($q->getNumRows() > 0) {
      	        $q->fetch();
      	        $tw_token = $q->getData('tw_token');
      	        return $tw_token;
            }
        }
        return false;
    }

    /**
     * Get an Ofuz User ID from the provided tw_user_id
     * @param string tw_user_id
     */
    function getOfuzUserID($tw_user_id) {
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT iduser FROM twitter_account WHERE tw_user_id = ".$tw_user_id);
        if ($q->getNumRows() > 0) {
      	    $q->fetch();
      	    $iduser = $q->getData('iduser');
      	    return $iduser;
        }
        return false;
    }

    /**
     * Insert user's Twitter User ID, Screen Name, and Access Token
     */
    function setAccessToken($tw_user_id, $tw_screen_name, $tw_token) {
        if ($_SESSION['do_User']->iduser) {
            $q = new sqlQuery($this->getDbCon());
            $q->query("INSERT INTO twitter_account (iduser, tw_user_id, tw_screen_name, tw_token) VALUES (".$_SESSION['do_User']->iduser.",'".$tw_user_id."','".$tw_screen_name."','".$tw_token."')");
        }
    }

    /**
     * returns an array of configuration options
     */
    function getTwitterConfig() {
        $configuration = array(
            'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
            'signatureMethod' => 'HMAC-SHA1',
            'callbackUrl' => 'http://'.$_SERVER['SERVER_NAME'].'/tw_callback.php',
            'requestTokenUrl' => 'http://twitter.com/oauth/request_token',
            'authorizeUrl' => 'http://twitter.com/oauth/authorize',
            'accessTokenUrl' => 'http://twitter.com/oauth/access_token',
            'consumerKey' => TWITTER_CONSUMER_KEY,
            'consumerSecret' => TWITTER_CONSUMER_SECRET
        );
        return $configuration;
    }

    /**
     * Sends a Direct Message to yourself or to a follower
     * @param string tw_user_id
     * @param string message
     */
    function sendDirectMessage($tw_user_id, $message) {
        $token = unserialize($this->getAccessToken());
        $config = $this->getTwitterConfig();
        $twitter = new Ofuz_Service_Twitter($tw_user_id, $config, $token);
        $response = $twitter->directMessageNew($tw_user_id, $message);
    }

    /**
     * Sends a Direct Message to yourself or to a follower as a RADRIA Event
     * @param object EventControler
     * 
     * Example:
     * 
     * $do_twitter = new OfuzTwitter();
     * $do_twitter->sessionPersistent('do_twitter', 'signout.php', 36000);
     * $e_tw_message = new Event('do_twitter->eventSendDirectMessage');
     * $e_tw_message->addParam('goto',$_SERVER['PHP_SELF']);
     * $e_tw_message->addParam('message','Hello from Ofuz!');
     * echo _('Click '),$e_tw_message->getLink('HERE'),' to send a test tweet.';
     */
    function eventSendDirectMessage(EventControler $evtcl) {
        $token = unserialize($this->getAccessToken());
        $tw_user_id = $token->getParam('user_id');
        $config = $this->getTwitterConfig();
        $twitter = new Ofuz_Service_Twitter($tw_user_id, $config, $token);
        $response = $twitter->directMessageNew($tw_user_id, $evtcl->message);
    }

    /**
     * Import a Twitter friend
     */
    function importTwitterFriend($friend_data, $extra_tag = ''){
        $iduser = $_SESSION['do_User']->iduser;
        $tw_user_id = $friend_data['user_id'];
        $idcontact = $this->isTwFriendInContacts($iduser, $tw_user_id);
        list($fname, $lname) = explode(' ', $friend_data['name'], 2);
        $screen_name = $friend_data['screen_name'];
        $description = $friend_data['description'];
        $profile_image_url = $friend_data['profile_image_url'];
        $url = $friend_data['url'];
        $do_tag = new Tag();

        if ($idcontact) {
            //update the data

            $c = new Contact();
            $c->getId($idcontact);
            $c->firstname = $fname;
            $c->lastname = $lname;
			if($c->picture == "") {
            	$c->picture = $profile_image_url;
			}
            $c->tw_user_id = $tw_user_id;
            $c->update();

            $do_tag->addTagAssociation($idcontact,'Twitter','contact',$iduser);
            if ($extra_tag != '') {
                $do_tag->addTagAssociation($idcontact,$extra_tag,'contact',$iduser);
            }
        } else {
            // new entry

            $c = new Contact();
            $c->firstname = $fname;
            $c->lastname = $lname;
            $c->iduser = $iduser;
            $c->picture = $profile_image_url;
            $c->tw_user_id = $tw_user_id;
            $c->add();

            $idcontact = $c->idcontact;

            $w = new ContactWebsite();
            $w->idcontact = $idcontact;
            $w->website = 'http://twitter.com/'.$screen_name;
            $w->website_type = 'Twitter';
            $w->feed_auto_fetch = 'Yes';
            $w->add();

            if ($url != '') {
            	$w = new ContactWebsite();
            	$w->idcontact = $idcontact;
            	$w->website = $url;
            	$w->website_type = 'Personal';
            	$w->add();
            }

            $link = '<br /><a href="http://twitter.com/'.$screen_name.'" target="_blank">Back to the Source of the Article</a><br />';
            $do_contact_note = new ContactNotes();
            $do_contact_note->idcontact = $idcontact;
            $do_contact_note->note = $description.$link;
            $do_contact_note->date_added = date('Y-m-d');
            $do_contact_note->iduser = $iduser;
            $do_contact_note->add();

            $do_tag->addTagAssociation($idcontact,'Twitter','contact',$iduser);
            if ($extra_tag != '') {
                $do_tag->addTagAssociation($idcontact,$extra_tag,'contact',$iduser);
            }
        }
    }

    function isTwFriendInContacts($iduser, $tw_friend_id){
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT idcontact FROM contact WHERE tw_user_id = ".$tw_friend_id." AND iduser = ".$iduser);
        if($q->getNumRows()){
            $q->fetch();
            $idcontact = $q->getData("idcontact");
            return $idcontact;
        }
        return false;
    }

    /**
     * Get all Twitter Access Tokens
     */
    function cronGetAccessTokens() {
        $this->query("SELECT iduser, tw_token FROM twitter_account");
    }

    /**
     * Determine if tweets should be imported
     */
    function cronFeedStatus($iduser, $tw_friend_id, $tweettime){
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT c.idcontact, cw.idcontact_website FROM contact c INNER JOIN contact_website cw ON c.idcontact = cw.idcontact WHERE c.tw_user_id = ".$tw_friend_id." AND c.iduser = ".$iduser." AND cw.website_type = 'Twitter' AND cw.feed_auto_fetch = 'Yes' AND cw.feed_last_fetch <> '".$tweettime."'");
        if($q->getNumRows()){
            $q->fetch();
            $idcontact = $q->getData("idcontact");
            $idcontact_website = $q->getData("idcontact_website");
            $q->query("UPDATE contact_website SET feed_last_fetch = '".$tweettime."' WHERE idcontact_website = ".$idcontact_website);
            return $idcontact;
        }
        return false;
    }
}


/**
 * Ofuz_Service_Twitter
 *   allows Zend_Service_Twitter using Oauth
 */

class Ofuz_Service_Twitter extends Zend_Service_Twitter {

    protected $_client;

    public function __construct($username, $config, Zend_Oauth_Token_Access $token) {
        $this->_authInitialized = true;
        $this->_client = $token->getHttpClient($config);
        self::setHttpClient($this->_client);
        parent::__construct($username, null);
    }

    public function _init() {
        $client = $this->_client;
    }
}
?>
