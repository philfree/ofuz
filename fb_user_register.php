<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Login';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/header.inc.php');
    include_once 'facebook_client/facebook.php';
    include_once 'class/OfuzFacebook.class.php';
    $facebook = new Facebook(FACEBOOK_API_KEY, FACEBOOK_APP_SECRET);
    $do_ofuz_fb =  new OfuzFacebook($facebook);
    $do_ofuz_fb->sessionPersistent("do_ofuz_fb", "logout.php", OFUZ_TTL);
    $_SESSION['do_ofuz_fb']->isLoggedInFacebook();
    $next_page = "index.php";
?>
<script type="text/javascript">
//<![CDATA[
var counter = <?php echo ($_SESSION['do_ofuz_fb']->fb_uid) ? 1 : 0; ?>;
//]]>
</script>
<script src="fbconnect.js" type="text/javascript"></script>
<script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" type="text/javascript"></script>
<script src="fbconnect.js" type="text/javascript"></script>
<script type="text/javascript">
    FB_RequireFeatures(["XFBML"], function()
    {
      FB.Facebook.init(FACEBOOK_API_KEY, "http://www.ofuz.net/xd_receiver.htm");
       //FB.Facebook.init(FACEBOOK_API_KEY, "http://dev.ofuz.net/xd_receiver.htm");
      FB.Facebook.get_sessionState().waitUntilReady(function(session) {
        var is_loggedin = session ? true : false;
 	var fbu = FB.Facebook.apiClient.get_session() ?
             FB.Facebook.apiClient.get_session().uid :
             0;
            if (is_loggedin && !counter) {
               window.location.reload();
 	    }
     }); 
    });
  </script>
<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
    <div class="loginbg2">
    <?php
        //echo $_SESSION['do_ofuz_fb']->fb_uid;exit;
        $loginForm = new User();
        $loginForm->sessionPersistent("do_User", "", 36000);
        if($_SESSION['do_ofuz_fb']->fb_uid){
            $evctl = new Event("do_User->eventLoginFb");
            $evctl->addParam("fbuid",$_SESSION['do_ofuz_fb']->fb_uid);
            $evctl->addParam("errPage","fb_ofuz_login_verification.php");
            $evctl->addParam("nextPage","index.php");
            $evctl->setSecure(true);
?>
<script type="text/javascript">
//<![CDATA[
window.location = "<?php echo $evctl->getUrl();?>";
//]]>
</script>
<?php } ?>
<?php
        if ($_GET['message']) {
    ?>
        <div class="error_message">
        <?php echo htmlentities(stripslashes($_GET['message'])); ?>
        </div>
    <?php } ?>
        <div class="text">
            Registration with Facebook Connect
            <br />
            <p>
              Login With Facebook :<br /> 
              <a href="#" onclick="FB.Connect.requireSession(); return false;" > <img id="fb_login_image" src="http://static.ak.fbcdn.net/images/fbconnect/login-buttons/connect_light_medium_long.gif" alt="Connect"/> </a>
            </p>
        </div>
    </div>
<!--    <div class="loginbg3">
        <a href="user_login_openid.php">You can also sign in using OpenID</a>
    </div>-->
    <div class="loginbg3">
        <a href="i_login.php">You can also sign into our mobile version</a>
    </div>
</div>
</body>
</html>