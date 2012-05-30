<?php 
// Copyright 2001 - 2007 SQLFusion LLC           info@sqlfusion.com

  /**
   * Extra config, post configuration.
   * load config settings from packages.
   * all the *postconf.inc.php files are loaded after
   * the session is started database connection is set and 
   * global vars are loaded (session events and global vars). 
   **/

    $d = dir($cfg_project_directory."includes/");
    while($entry = $d->read()) {
        if (preg_match("/\.postconf\.inc\.php$/i", $entry) && !preg_match("/^\./", $entry)) {
            include_once($entry);
        }
    }
    $d->close();

?>