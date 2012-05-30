<html>
  <head>
    <title><?php echo $pageTitle;?></title>
    <link rel="stylesheet" type="text/css" href="includes/style.css">
    <meta name="author" content="<?php echo $Author; ?>">
    <meta name="keywords" content="<?php echo $Keywords; ?>">
    <meta name="description" content="<?php echo $Description; ?>">
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
