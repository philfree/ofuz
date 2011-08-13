<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

// Code to load FB JS API skip if the file name is in the array
$arry_dont_load_api = Array("fb_connect.php");
$arry_dont_load_api_len = count($arry_dont_load_api);
$currentFile = $_SERVER["PHP_SELF"];
  $parts = Explode('/', $currentFile);//print_r($parts);
  if(!in_array($parts[count($parts) - 1],$arry_dont_load_api)){ 
  	if($_SESSION['do_User']->global_fb_connected && $_SESSION['do_User']->fb_user_id){

?>

<!--<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>-->
<?php
if($application_layer_protocol == "https") {
?>
<script type="text/javascript" src="https://ssl.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"></script>
<script type="text/javascript">
//<![CDATA[
FB_RequireFeatures(["XFBML"], function() {
	// xd_receiver to pull from a secure location
	FB.Facebook.init("<?php echo FACEBOOK_API_KEY ;?>", "<?php echo FACEBOOK_XD_RECEIVER_HTTPS ;?>");
	FB.Facebook.get_sessionState().waitUntilReady(function(session) {
	var is_loggedin = session ? true : false;
  }); 
});
//]]>
</script>
<?php
} else {
?>
<script type="text/javascript" src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" ></script>
<script type="text/javascript">
//<![CDATA[
FB_RequireFeatures(["XFBML"], function() {
	// xd_receiver to pull from a secure location
	FB.Facebook.init("<?php echo FACEBOOK_API_KEY ;?>", "<?php echo FACEBOOK_XD_RECEIVER_HTTP ;?>");
	FB.Facebook.get_sessionState().waitUntilReady(function(session) {
	var is_loggedin = session ? true : false;
  }); 
});
//]]>
</script>
<?php } ?>

<?php } }?>
