<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


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

