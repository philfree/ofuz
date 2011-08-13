<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php echo $pageTitle; ?></title>
    <meta name="author" content="<?php echo $Author; ?>" />
    <meta name="keywords" content="<?php echo $Keywords; ?>" />
    <meta name="description" content="<?php echo $Description; ?>" />
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="content-style-type" content="text/css" />
    <?php 
    $d = dir($cfg_project_directory."includes/");
    while($entry = $d->read()) {
        if (preg_match("/\.header\.inc\.php$/i", $entry) && !preg_match("/^\./", $entry)) {
            include_once($entry);
        }
    }
    $d->close();
    ?>
  </head>
  <body <?php echo $body_properties;?>>
