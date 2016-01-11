<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

// Code to load FB JS API skip if the file name is in the array
$arry_dont_load_api = Array("fb_connect.php");
$arry_dont_load_api_len = count($arry_dont_load_api);
$currentFile = $_SERVER["PHP_SELF"];
  $parts = Explode('/', $currentFile);//print_r($parts);
  if(!in_array($parts[count($parts) - 1],$arry_dont_load_api)){ 
  	if($_SESSION['do_User']->global_fb_connected && $_SESSION['do_User']->fb_user_id){

?>

<div id="fb-root"></div>

<script type="text/javascript">
//<![CDATA[
  window.fbAsyncInit = function() {
    FB.init({appId: '<?php echo FACEBOOK_APP_ID ; ?>', status: true, cookie: true,
             xfbml: true});
  };
  (function() {
    var e = document.createElement('script'); e.async = true;
    e.src = document.location.protocol +
      '//connect.facebook.net/en_US/all.js';
    document.getElementById('fb-root').appendChild(e);
  }());
//]]>
</script>

<?php } }?>
