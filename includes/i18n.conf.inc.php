<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 


   /**
    *  i18n.conf.inc.php
    *  Internationalization configuration file
    *
    *  Create if doesn't exists the functions needed for intenationalisation 
    * @package OfuzCore
    */

    global $cfg_lang, $cfg_language_app, $cfg_locale_path;

    // once we have a couple languages working we will use: _SERVER["HTTP_ACCEPT_LANGUAGE"] to detect the language automaticaly.

    $GLOBALS['cfg_lang'] = "en_US";  // fr_FR
    $GLOBALS['cfg_charset'] = "utf8";
    $GLOBALS['cfg_language_app'] = "ofuz";
    $GLOBALS['cfg_locale_path'] = "locale/";
    $GLOBALS['cfg_language_file'] = "messages" ;
    

    /**
      * Date formating. The example is set for French language display pattern of date and time
      * Can add our own formating for the current language
    */
    $GLOBALS['cfg_time_formats']['fr_FR']['date'] = "%A %e %B %Y";
    $GLOBALS['cfg_time_formats']['fr_FR']['time'] = "%A %e %B %Y à %H:%M %P";


?>
