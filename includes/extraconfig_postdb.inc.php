<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


  /**
   * Extra config, post configuration.
   * load config settings from packages.
   * all the *postconf.inc.php files are loaded after
   * the session is started database connection is set and 
   * global vars are loaded (session events and global vars). 
   **/
   
 
    $d = dir('includes/');
    while($entry = $d->read()) {
        if (preg_match("/\.postconf\.inc\.php$/i", $entry) && !preg_match("/^\./", $entry)) {
            include_once($entry);
        }
    }
    $d->close();

