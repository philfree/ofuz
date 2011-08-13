<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
?>
<?php if ($dojo_google_cdn) { 
  if ($GLOBALS['google_cdn_loaded'] !== true) { 
   $GLOBALS['google_cdn_loaded'] = true; ?>
  <script src="http://www.google.com/jsapi"></script>
  <?php } ?>
  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/dojo/<?php echo $dojo_version;?>/dijit/themes/tundra/tundra.css" />
  <script>
    // Load Dojo
                djConfig = {
                        isDebug: false,
                        parseOnLoad: true
                };
     google.load("dojo", "<?php echo $dojo_version; ?>");
  </script>

<?php } else { ?>

<script type="text/javascript" src="/dojo/dojo/dojo.js"
			djConfig="isDebug: false, parseOnLoad: true">
</script>

<style type="text/css">
			@import "/dojo/dojo/resources/dojo.css";
			@import "/dojo/dijit/themes/tundra/tundra.css";
			@import "/dojo/dijit/themes/tundra/tundra_rtl.css";

		</style>


<?php } ?>

<script type="text/javascript">
			dojo.require("dojo.parser");	// scan page for widgets and instantiate them
		</script>

<?php
  // Needed to set the theme on the body
  $body_properties = " class=\"tundra\"";
?>
