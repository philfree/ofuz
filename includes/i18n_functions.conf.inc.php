<?php
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

   /**
    *  i18n.conf.inc.php
    *  Internationalization configuration file
    *
    *  Create if doesn't exists the functions needed for intenationalisation 
    */

 //   global $cfg_lang, $cfg_language_app, $cfg_locale_path;

    //$locales = Array();

    if (!function_exists("i18n_include")) {
        function i18n_include($include_filename, $language_app="") {
            global $cfg_locale_path, $cfg_lang, $cfg_language_app;
            if (empty($language_app)) { $language_app = $cfg_language_app; }
            if (file_exists($cfg_locale_path.$cfg_lang."/".$language_app."/".$include_filename)) {
                include($cfg_locale_path.$cfg_lang."/".$language_app."/".$include_filename);
                return true;
            } else { return false; }
        }
        function i18n_include_once($include_filename, $language_app="") {
            global $cfg_locale_path, $cfg_lang, $cfg_language_app;
            if (empty($language_app)) { $language_app = $cfg_language_app; }
            if (file_exists($cfg_locale_path.$cfg_lang."/".$language_app."/".$include_filename)) {
                include_once($cfg_locale_path.$cfg_lang."/".$language_app."/".$include_filename);
                return true;
            } else { return false; }
        }
    }
    if (!function_exists("_") && !function_exists("gettext")) {
        i18n_include_once($cfg_language_app.".php");
    
        function gettext($i18n_key_string) {
            if (!empty($GLOBALS['locales'][$i18n_key_string])) {
                return $GLOBALS['locales'][$i18n_key_string];
            } else {
                return $i18n_key_string;
            } 
        }
    
        function _($i18n_key_string) {
            return gettext($i18n_key_string);
        }

        // function for savedquery and registry:
        function i18n_var($params) {
            $i18n_key_string = $params[1];
            if (exist($param[2])) {
                $cfg_language_app = $param[2];
            } else { $cfg_language_app = ""; }
            return _($i18n_key_string, $cfg_language_app);
        }
        // function for reports and forms:
        function i18n($reportdata, $row, $dbc) {
            $i18n_key_string = $reportdata[1];
            if (isset($reportdata[2])) {
                $cfg_language_app = $reportdata[2];
            } else { $cfg_language_app = ""; }
            return _($i18n_key_string, $cfg_language_app);
        }

    } else {
        setlocale(LC_MESSAGES,$GLOBALS['cfg_lang'].".".$GLOBALS['cfg_charset']);
        putenv("LANG=".$GLOBALS['cfg_lang'].".".$GLOBALS['cfg_charset']); 
        putenv("LANGUAGE=".$GLOBALS['cfg_lang'].".".$GLOBALS['cfg_charset']);
        setlocale(LC_ALL, $GLOBALS['cfg_lang'].".".$GLOBALS['cfg_charset']);
        //Required only if locale is not on the web root.
      
        bindtextdomain($GLOBALS['cfg_language_file'], dirname($_SERVER["SCRIPT_FILENAME"])."/".$GLOBALS['cfg_locale_path']);
        textdomain($GLOBALS['cfg_language_file']);


    }
    
    if (!function_exists("__")) {
        //i18n_include_once("locales.php");
        function __($i18n_key_string, $cfg_language_app="") {
            if (empty($cfg_language_app)) { $cfg_language_app = $GLOBALS['cfg_language_app']; }
            textdomain($cfg_language_file);
            return gettext($i18n_key_string);
            //if (!empty($GLOBALS['locales_'.$cfg_language_app][$i18n_key_string])) {
            //    return $GLOBALS['locales_'.$cfg_language_app][$i18n_key_string];
            //}
        }
        // function for savedquery and registry:
        function _i18n_var($params) {
            $i18n_key_string = $params[1];
            if (exist($param[2])) {
                $cfg_language_app = $param[2];
            } else { $cfg_language_app = ""; }
            return __($i18n_key_string, $cfg_language_app);
        }
        // function for reports and forms:
        function _i18n($reportdata, $row, $dbc) {
            $i18n_key_string = $reportdata[1];
            if (isset($reportdata[2])) {
                $cfg_language_app = $reportdata[2];
            } else { $cfg_language_app = ""; }
            return __($i18n_key_string, $cfg_language_app);
        }
    }

