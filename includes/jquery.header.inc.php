<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

?>
<?php if ($jquery_google_cdn) { 
  if ($GLOBALS['google_cdn_loaded'] !== true) { 
   $GLOBALS['google_cdn_loaded'] = true; ?>
  <script src="http://www.google.com/jsapi"></script>
  <?php } ?>
  <script>
     google.load("jquery", "<?php echo $jquery_version; ?>");
  </script>

<?php } else { ?>
  <script type="text/javascript" src="/jquery/jquery-1.4.2.min.js"></script>
<?php } ?>
  <script type="text/javascript" src="/jquery/jquery.expandable.js"></script>
  <script type="text/javascript" src="/jquery/jquery.field.js"></script>
  <script type="text/javascript" src="/js/shortcuts.js"></script>