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
     * Get a valid Access Token
	 */
    $token = $consumer->getRequestToken();
    $_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
    $_SESSION['TWITTER_ENTRY'] = 'user_login.php';

    /**
	 * Now redirect user to Twitter site so they can log in and
     * approve our access
	 */
	$consumer->redirect();
    exit;
?>
<html>
<head>
<title>Something went wrong</title>
</head>
<body>
Something went wrong.  Sorry.
</body>
</html>