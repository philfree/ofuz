<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Twitter Setup';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');

    require_once 'Zend/Oauth/Consumer.php';

    $do_twitter = new OfuzTwitter();
    $configuration = $do_twitter->getTwitterConfig();

    /**
     * Set up an instance of the Consumer for use
     */
    $consumer = new Zend_Oauth_Consumer($configuration);

    /**
     * Someone's knocking at the door using the Callback URL - if they have
     * some GET data, it might mean that someone's just approved OAuth access
     * to their account, so we better exchange our current Request Token
     * for a newly authorised Access Token. There is an outstanding Request Token
     * to exchange, right?
     */
    if (!empty($_GET) && isset($_SESSION['TWITTER_REQUEST_TOKEN'])) {
        $token = $consumer->getAccessToken($_GET, unserialize($_SESSION['TWITTER_REQUEST_TOKEN']));
        $tw_user_id = $token->getParam('user_id');
        $tw_screen_name = $token->getParam('screen_name');
        $tw_token = serialize($token);

        /**
         * Now that we have an Access Token, we can discard the Request Token
         */
        $_SESSION['TWITTER_REQUEST_TOKEN'] = null;

        /**
         * Record the Access Token if necessary and return to the appropriate page
         */
        if (isset($_SESSION['TWITTER_ENTRY'])) {
            if ($_SESSION['TWITTER_ENTRY'] == 'user_login.php') {
                $iduser = $do_twitter->getOfuzUserID($tw_user_id);
                if ($iduser) {
                    $do_user = new User();
                    $do_user->iduser = $iduser;
                    $do_user->setSessionVariable();
                    $do_login_audit = new LoginAudit();
                    $do_login_audit->do_login_audit('Twitter');
                    header('Location: index.php');
                    exit;
                } else {
                    $config = $do_twitter->getTwitterConfig();
                    $ofuz_twitter = new Ofuz_Service_Twitter($tw_user_id, $config, $token);
                    $userdetail = $ofuz_twitter->userShow($tw_user_id);
                    list($firstname, $lastname) = split(' ', $userdetail->name);
                    $_SESSION['TWITTER_REGISTER'] = array(
                        'tw_user_id'=>$tw_user_id,
                        'tw_screen_name'=>$tw_screen_name,
                        'tw_token'=>$tw_token,
                        'firstname'=>$firstname,
                        'lastname'=>$lastname
                    );
                    header('Location: tw_user_register.php');
                    exit;
                }
                header('Location: user_login.php');
            } else if ($_SESSION['TWITTER_ENTRY'] == 'settings_twitter.php') {
                $do_twitter->setAccessToken($tw_user_id, $tw_screen_name, $tw_token);
                header('Location: settings_twitter.php');
                exit;
            }
            header('Location: user_login.php');
        } else {
            header('Location: user_login.php');
        }
    } else {
	    /**
	     * Mistaken request? Some malfeasant trying something?
	     */
        //exit('Invalid callback request. Oops. Sorry.');
        // Maybe they double-clicked, so redirect anyway
        if (isset($_SESSION['TWITTER_ENTRY'])) {
            header('Location: '.$_SESSION['TWITTER_ENTRY']);
        } else {
            header('Location: user_login.php');
        }
    }
?>