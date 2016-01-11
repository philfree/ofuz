<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHP sample code for the Google Calendar data API.  Utilizes the 
 * Zend Framework Gdata components to communicate with the Google API.
 * 
 * Requires the Zend Framework Gdata components and PHP >= 5.1.4
 *
 * You can run this sample both from the command line (CLI) and also
 * from a web browser.  When running through a web browser, only
 * AuthSub and outputting a list of calendars is demonstrated.  When
 * running via CLI, all functionality except AuthSub is available and dependent
 * upon the command line options passed.  Run this script without any
 * command line options to see usage, eg:
 *     /usr/local/bin/php -f Calendar.php
 *
 * More information on the Command Line Interface is available at:
 *     http://www.php.net/features.commandline
 *
 * NOTE: You must ensure that the Zend Framework is in your PHP include
 * path.  You can do this via php.ini settings, or by modifying the 
 * argument to set_include_path in the code below.
 *
 * NOTE: As this is sample code, not all of the functions do full error
 * handling.  Please see getEvent for an example of how errors could
 * be handled and the online code samples for additional information.
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata
 */
Zend_Loader::loadClass('Zend_Gdata');

/**
 * @see Zend_Gdata_AuthSub
 */
Zend_Loader::loadClass('Zend_Gdata_AuthSub');

/**
 * @see Zend_Gdata_ClientLogin
 */
//Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

/**
 * @see Zend_Gdata_Calendar
 */
//Zend_Loader::loadClass('Zend_Gdata_Calendar');

/**
 * @see Zend_Gdata_Query
 */
Zend_Loader::loadClass('Zend_Gdata_Query');

class Zend_ContactsAuth
{

    /**
    * Returns the full URL of the current page, based upon env variables
    * 
    * Env variables used:
    * $_SERVER['HTTPS'] = (on|off|)
    * $_SERVER['HTTP_HOST'] = value of the Host: header
    * $_SERVER['SERVER_PORT'] = port number (only used if not http/80,https/443)
    * $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
    *
    * @return string Current URL
    */
    function getCurrentUrl() 
    {
      global $_SERVER;
    
      /**
      * Filter php_self to avoid a security vulnerability.
      */
      $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);
    
      if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
        $protocol = 'https://';
      } else {
        $protocol = 'http://';
      }
      $host = $_SERVER['HTTP_HOST'];
      if ($_SERVER['SERVER_PORT'] != '' &&
        (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
        ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
        $port = ':' . $_SERVER['SERVER_PORT'];
      } else {
        $port = '';
      }
      return $protocol . $host . $port . $php_request_uri;
    }
    
    /**
    * Returns the AuthSub URL which the user must visit to authenticate requests 
    * from this application.
    *
    * Uses getCurrentUrl() to get the next URL which the user will be redirected
    * to after successfully authenticating with the Google service.
    *
    * @return string AuthSub URL
    */
    function getAuthSubUrl() 
    {
      $next = self::getCurrentUrl();
      $scope = 'http://www.google.com/m8/feeds/';
      $secure = false;
      $session = true;
      //$session = false;
      return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure, 
          $session);
    }

    /**
    * Outputs a request to the user to login to their Google account, including
    * a link to the AuthSub URL.
    * 
    * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
    *
    * @return void
    */
    public static function requestUserLogin($linkText) 
    {
      $authSubUrl = self::getAuthSubUrl();
      echo "<a href=\"{$authSubUrl}\">{$linkText}</a>"; 
    }
    
    /**
    * Returns a HTTP client object with the appropriate headers for communicating
    * with Google using AuthSub authentication.
    *
    * Uses the $_SESSION['sessionToken'] to store the AuthSub session token after
    * it is obtained.  The single use token supplied in the URL when redirected 
    * after the user succesfully authenticated to Google is retrieved from the 
    * $_GET['token'] variable.
    *
    * @return Zend_Http_Client
    */
    public static function getAuthSubHttpClient() 
    {
      global $_SESSION, $_GET;
      if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
        $_SESSION['sessionToken'] = 
            Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
      }
      $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);

      return $client;
    }
/*
//is used when SESSION TOKEN is stored in table
    public static function retrieveAuthSubHttpClient($session_token){
      //global $_SESSION, $_GET;
      $client = Zend_Gdata_AuthSub::getHttpClient($session_token);
      return $client;
    }
*/
}//end of class

