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
    include_once 'facebook_client/facebook.php';
    //include_once 'class/OfuzFacebook.class.php';
				include_once('class/RadriaFacebookConnect.class.php');
    include_once('includes/header.inc.php');
    
    /*$facebook = new Facebook(FACEBOOK_API_KEY, FACEBOOK_APP_SECRET);
    $do_ofuz_fb =  new OfuzFacebook($facebook);
    $do_ofuz_fb->sessionPersistent("do_ofuz_fb", "logout.php", OFUZ_TTL);
    $_SESSION['do_ofuz_fb']->isLoggedInFacebook();
    if($_SESSION['do_ofuz_fb']->fb_uid){
        $name = $_SESSION['do_ofuz_fb']->getFbUserName($_SESSION['do_ofuz_fb']->fb_uid);
        $fname = $name["first_name"];
        $lname = $name["last_name"];
        $work = $_SESSION['do_ofuz_fb']->getWorkHistory($_SESSION['do_ofuz_fb']->fb_uid);
        $work_detail = $work[0];
        $work_history = @$work_detail["work_history"];
        if(is_array($work_history)){
            $position =  $work_history[0]["position"];
            $company =  $work_history[0]["company_name"];
        }else{$company = '';$position='';}
    }*/
//     error_reporting(E_ALL);
// ini_set('display_errors', '1');


				$facebook = new Facebook(array(
    'appId'  => FACEBOOK_APP_ID,
    'secret' => FACEBOOK_APP_SECRET,
    'cookie' => true,
    'domain'=> FACEBOOK_CONNECT_DOMAIN
  ));

		

		
		$do_ofuz_fb = new RadriaFacebookConnect($facebook,FACEBOOK_APP_ID,FACEBOOK_APP_SECRET); 
  $do_ofuz_fb->sessionPersistent("do_ofuz_fb", "logout.php", OFUZ_TTL); 
		$_SESSION['do_ofuz_fb']->isLoggedInFacebook(); // Must be called before any RadriaFacebookConnect :: method() call
		
		$user_data = $facebook->api('/me');  
		if(is_array($user_data) && count($user_data) > 0 && $_SESSION['do_ofuz_fb']->fb_uid != ''){
				$fname = $user_data["first_name"];
    $lname = $user_data["last_name"];
				$work_detail = $user_data["work"][0];
			 if(is_array($work_detail) && count($work_detail) > 0 ){
								$position = $work_detail["position"]["name"];
								$company = 	$work_detail["employer"]["name"];
				}else{$company = '';$position='';}
		}
		
		
	//	print_r($user_data);exit;

?>


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
//     <?php
// 	  if($application_layer_protocol == "https") {
// 	  ?>
// 			  FB.Facebook.init("<?php echo FACEBOOK_API_KEY ;?>", "<?php echo FACEBOOK_XD_RECEIVER_HTTPS ;?>");
// 	  <?php
// 	  } else {
// 	  ?>
// 			  FB.Facebook.init("<?php echo FACEBOOK_API_KEY ;?>", "<?php echo FACEBOOK_XD_RECEIVER_HTTP ;?>");
// 	  <?php
// 	  }
//     ?>
//       FB.Facebook.get_sessionState().waitUntilReady(function(session) {
//         var is_loggedin = session ? true : false;
//  	var fbu = FB.Facebook.apiClient.get_session() ?
//              FB.Facebook.apiClient.get_session().uid :
//              0;
//      }); 
//     });
  </script>
<script type="text/javascript">
//<![CDATA[
function showDiv(){
    document.getElementById('fb_val').style.display = 'block';
    //alert('done');
}
//]]>
</script>
<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
	<div class="messageshadow" style="width:440px; margin: 0 auto">
	    <div class="messages">
		<?php echo _('Thank you for connecting to Ofuz with facebook connect.'),'<br />',
		           _('If you already have a username and password at Ofuz please sign in below to connect your facebook account to Ofuz.'),'<br />',
		           _('Otherwise just provide us with your email address to create a new account.'); ?>
        <?php echo htmlentities(stripslashes($_GET['message'])); ?>
        </div>
      </div>
    <div class="loginbg2">
    <?php
        $loginForm_fb = new User();
        $loginForm_fb->sessionPersistent("do_User_login", "logout.php", 36000);
        
      //  if ($_GET['message']) {
    ?>
    <?php //}
	 ?>
        <div class="text">
		    <div class="section20">
		<?php echo _('If you are a new Ofuz user, please enter your email address below:'); ?><br />
            <?php 
              $loginFormEmail_fb = new User();
              $loginFormEmail_fb->sessionPersistent("do_User_login_email", "logout.php", 36000);
              $e_new_fb_reg = new Event("do_User_login_email->eventRegNewFbUser");
              $e_new_fb_reg->setLevel(20);
              $e_new_fb_reg->addParam("fbuid",$_SESSION['do_ofuz_fb']->fb_uid);
              $e_new_fb_reg->addParam("fname",$fname);
              $e_new_fb_reg->addParam("lname",$lname);
              $e_new_fb_reg->addParam("comp",$company);
              $e_new_fb_reg->addParam("position",$position);
              $e_new_fb_reg->addParam("errPage","fb_ofuz_login_verification.php");
              
              echo $e_new_fb_reg->getFormHeader();
              echo $e_new_fb_reg->getFormEvent();
              echo _('Your email address: ').'<input type="Text" name = "emailid" id = "emailid" class="formfield" ><br />';
              echo '<div align="right"><input type="submit" value="'._('Continue').'" /></div>';
              //echo '<div class="section20">';
              //echo '<div class="dottedline"></div>';
              echo '</form>';
              echo '</div>';
			  ?>
          </div>
            <div class="dottedline"></div>
            <div class="section20">
            		
         <?php 
                if(!$_SESSION['do_ofuz_fb']->getUserExtendedPermissions("email")){
                  //echo '<fb:prompt-permission perms="email" next_fbjs="showDiv()"> Allow Sending Email Notification</fb:prompt-permission>';
                   $style = 'style="display:block;"'; 
                }else{
                    $style = 'style="display:block;"';
                }
                echo '<div id="fb_val" '.$style.'>';
                echo _('Otherwise, sign in here to link your Facebook and Ofuz accounts:'),'<br /><br />';
                $_SESSION['do_User_login']->formFBLoginVerification("index.php",$_SERVER['PHP_SELF'],$_SESSION['do_ofuz_fb']->fb_uid);
            ?>
           </div>
        </div>
    </div>
    <div class="loginbg3">
    </div>
</div>
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
</body>
</html>
