<?php
error_reporting(E_ALL); 
ini_set( 'display_errors','1');
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    setcookie("ofuz", "1", time()+25920000);
    include_once('config.php');
    $pageTitle = 'Ofuz :: '._('Login');
    include_once('includes/header.inc.php');
    include_once 'facebook_client/facebook.php';
    //include_once 'class/OfuzFacebook.class.php';
				include_once('class/RadriaFacebookConnect.class.php');

    /*$facebook = new Facebook(FACEBOOK_API_KEY, FACEBOOK_APP_SECRET);
    $do_ofuz_fb =  new OfuzFacebook($facebook);
    $do_ofuz_fb->sessionPersistent("do_ofuz_fb", "logout.php", OFUZ_TTL);
    $_SESSION['do_ofuz_fb']->isLoggedInFacebook();*/

		$facebook = new Facebook(array(
				'appId'  => FACEBOOK_APP_ID,
				'secret' => FACEBOOK_APP_SECRET,
				'cookie' => true,
				'domain'=> FACEBOOK_CONNECT_DOMAIN
		));

		$do_ofuz_fb = new RadriaFacebookConnect($facebook,FACEBOOK_APP_ID,FACEBOOK_APP_SECRET);
		$do_ofuz_fb->sessionPersistent("do_ofuz_fb", "logout.php", OFUZ_TTL);
		$_SESSION['do_ofuz_fb']->isLoggedInFacebook();


    $next_page = "index.php";
    if(isset($_GET['reg']) && $_GET['reg'] == '1'){
      $next_page = "welcome_to_ofuz.php";
    }
    if(isset($_GET['reg']) && $_GET['reg'] == 'pp'){
      $next_page = "settings_myinfo.php";
    }
    $from = $_GET['entry'];
    if (ereg("Task", $from) 
    ||  ereg("Project", $from)
    ||  ereg("cp", $from)
    ||  ereg("Contact", $from)
    ||  ereg("fb_ofuz_login_verification",$from)
    ||  ereg("Company", $from)) {
        $next_page = $from;
    }
    if (isset($_SESSION['entry']))  { 
  $next_page = $_SESSION['entry'];
  unset($_SESSION['entry']);
 }
 

?>
<script type="text/javascript">
//<![CDATA[
var counter = <?php echo ($_SESSION['do_ofuz_fb']->fb_uid) ? 1 : 0; ?>;
window.onload = function(){document.do_User__eventCheckIdentification["fields[username]"].focus();}
//]]>
</script>

<?php
if($application_layer_protocol == "https") {
?>
<!--<script type="text/javascript" src="https://ssl.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"></script>-->
<?php
} else {
?>
<!--<script type="text/javascript" src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" ></script>-->
<?php
}
?>

<script type="text/javascript">
//     FB_RequireFeatures(["XFBML"], function()
//     {
//     // xd_receiver to pull from a secure location
// <?php
// if($application_layer_protocol == "https") {
// ?>
//   FB.Facebook.init("<?php echo FACEBOOK_API_KEY ;?>", "<?php echo FACEBOOK_XD_RECEIVER_HTTPS ;?>");
// <?php
// } else {
// ?>
//   FB.Facebook.init("<?php echo FACEBOOK_API_KEY ;?>", "<?php echo FACEBOOK_XD_RECEIVER_HTTP ;?>");
// <?php
// }
// ?>
//       FB.Facebook.get_sessionState().waitUntilReady(function(session) {
//         var is_loggedin = session ? true : false;
//   var fbu = FB.Facebook.apiClient.get_session() ?
//              FB.Facebook.apiClient.get_session().uid :
//              0;
//             if (is_loggedin && !counter) {
//                window.location.reload();
//       }
//      }); 
//     });
  </script>
<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
    <div class="loginbg2">
    <?php
        //echo $_SESSION['do_ofuz_fb']->fb_uid;exit;
        $loginForm = new User();
        $loginForm->sessionPersistent("do_User", "", 36000);
        if($_SESSION['do_ofuz_fb']->fb_uid != 0 && $_SESSION['do_ofuz_fb']->fb_uid != "" && is_object($_SESSION['do_ofuz_fb'])){
            $evctl = new Event("do_User->eventLoginFb");
            $evctl->addParam("fbuid",$_SESSION['do_ofuz_fb']->fb_uid);
            $evctl->addParam("errPage","fb_ofuz_login_verification.php");
            $evctl->addParam("nextPage","index.php");
            $evctl->setSecure(true);
?>
<script type="text/javascript">
//<![CDATA[
window.location.href = "<?php echo $evctl->getUrl();?>";
//]]>
</script>
<?php } ?>
<?php
        if ($_GET['message']) {
            $message = new Message();
            $message->setContent($_GET['message']);
            $message->displayMessage();
   
        } ?>
        <div class="text" style="position:relative;">
            <?php $_SESSION['do_User']->formLogin($next_page, _("Incorrect username or password"), "text",$_SERVER['PHP_SELF']); ?>
           
            <!--Try registration with facebook connect <a href="fb_user_register.php">here</a> .<br />-->
            <?php echo _('If you forgot your password, you can retrieve it ');?><a href="user_lost_password.php"><?php echo ' '._('here');?></a>
            <br />
            <p>
            <?php if(FACEBOOK_API_KEY != ''){ ?>

                <?php echo _('Login With Facebook:') ; ?><br />
																<?php
																				if($application_layer_protocol == "https") {
																								$login_url = SITE_URL_HTTPS.'/user_login.php';
																				}else{ $login_url = SITE_URL.'/user_login.php'; }
																?>
																<fb:login-button scope="<?php echo  $_SESSION['do_ofuz_fb']->getFbPermissionList() ; ?>" onlogin='window.location="<?php echo $login_url ; ?>";'>Connect with Facebook</fb:login-button> 
            </p>
      <?php } ?>
            <p>
                <?php echo _('Login With Twitter:') ; ?><br />
                <a href="/tw_login.php"><img src="/images/sign-in-with-twitter-d.png" alt="" /></a>
            </p>
            <p>
                <!--<a href="google_federated_login.php">Login with your Google Account</a>-->
              <?php echo _('Sign in with Google:'); ?><br />
              <a href="google_federated_login.php" id="LoginWithGoogleLink">
                <img style="margin-right: 3px;" src="images/gfavicon.gif" height="16" width="16" align="absmiddle" border="0">
                  <span class="google"><span>G</span><span>o</span><span>o</span><span>g</span><span>l</span><span>e</span> Account</span>
              </a>
            </p>
        </div>

    </div>
<!--    <div class="loginbg3">
        <a href="user_login_openid.php">You can also sign in using OpenID</a>
    </div>-->
    <div class="loginbg3">
        <a href="i_login.php"><?php echo _('You can also sign into our mobile version');?></a>
    </div>
</div>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    $("#registration_emailpass_signon :input[name='fields[email]']").focus();
});
//]]>
</script>


<div id="fb-root"></div>
<script type="text/javascript">
//<![CDATA[
    window.fbAsyncInit = function()
            {
                FB.init
                ({
                    appId   : '<?php echo FACEBOOK_APP_ID ; ?>',
                    status  : true, // check login status
                    cookie  : true, // enable cookies to allow the server to access the session
                    xfbml   : true, // parse XFBML
                    oauth   : true
                });
                FB.Event.subscribe('auth.login', function()
                {
                    window.location.reload();
                });
            };
          
          (function()
          {
            var e = document.createElement('script');
            e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
            e.async = true;
            document.getElementById('fb-root').appendChild(e);
            }());
            
            
            
//]]>
</script>
</body>
</html>