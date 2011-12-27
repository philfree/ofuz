<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Contacts';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');
  set_time_limit(3600);
     if (isset($_GET['ref']) && $_GET['ref'] == 'reg') {
      $ref = $_GET['ref'];
      $_SESSION["page_from"] = $ref;
  }
  include_once 'facebook_client/facebook.php';
  include_once 'class/OfuzFacebook.class.php';
  $facebook = new Facebook(FACEBOOK_API_KEY, FACEBOOK_APP_SECRET);
  if (!is_object($_SESSION['do_ofuz_fb'])) {
      $do_ofuz_fb =  new OfuzFacebook($facebook);
      $do_ofuz_fb->sessionPersistent("do_ofuz_fb", "index.php", OFUZ_TTL);
  }

  $_SESSION['do_ofuz_fb']->isLoggedInFacebook();
  //if($fb_uid){
   if($_SESSION['do_ofuz_fb']->fb_uid){
  ?>
    <script>
      var counter = 1;
    </script>
  <?php
  }else{
    ?>
    <script>
      var counter = 0;
    </script>
  <?php
  }
?>

  <title>Ofuz</title>
<!--
  <link rel="stylesheet" href="http://static.ak.connect.facebook.com/css/fb_connect.css" type="text/css" />
    <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"
    type="text/javascript"></script>
-->
 <?php
if($application_layer_protocol == "https") {
?>
<script type="text/javascript" src="https://ssl.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"></script>
<?php
} else {
?>
<script type="text/javascript" src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php" ></script>
<?php
}
?>

  <script type="text/javascript">
    FB_RequireFeatures(["XFBML"], function()
    {
      
	   // xd_receiver to pull from a secure location
      <?php
if($application_layer_protocol == "https") {
?>
		FB.Facebook.init("<?php echo FACEBOOK_API_KEY; ?>", "<?php echo FACEBOOK_XD_RECEIVER_HTTPS ; ?>");
<?php
} else {
?>
		FB.Facebook.init("<?php echo FACEBOOK_API_KEY ; ?>", "<?php echo FACEBOOK_XD_RECEIVER_HTTP ;?>");
<?php
}
?>
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
<!--<div id="bar" style="overflow: auto; height:300px; width:500px; margin-top:50px; float:left;" >-->
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Import Contacts From Facebook');?></span>
        </div>
    </div>
    <div class="contentfull">
        
<?php  
     if($_SESSION['do_ofuz_fb']->fb_uid){
        $_SESSION['do_ofuz_fb']->connected = true;
        $do_contact = new Contact();
        
        // Adding to the JOB QUEUE
        if($GLOBALS['ENABLE_JOB_QUEUE'] === true){
            $data_fb = serialize($_SESSION['do_ofuz_fb']);
            $qry = new sqlQuery($conx);
            $qry->query("insert into fb_test (data,iduser) values ('".$data_fb."',".$_SESSION['do_User']->iduser.")");
            echo '<H2>Request has been added in the Job Queue, you can enjoy browsing the other part and Ofuz will do the import for you without having to wait for a long time on this page.</H2>';
            exit;
        }
	try{
	    @$friends = $_SESSION['do_ofuz_fb']->getFbFriends();// will contain the fbid of friends
	}catch(Exception $e){
		$_SESSION['do_ofuz_fb']->setUserNull(null, null);
		echo _('Oops ! something wrong, seems like the facebook session is no longer valid, please clear the bowser cookies and retry. We appriciate your patience');
		//echo '<br /><a href="/fb_import_friends.php">'._('Click Here').'</a>';
		exit();
	}
        $list  = $_SESSION['do_ofuz_fb']->getFriendsList();
        $count = count(@$friends);
        $i = 1;
        $j=0;
        foreach(@$friends as $friend){
            $frnd_list = array();
            $frnd_list = $list;
            $list_name_array = array();
            if(is_array($frnd_list)){
              foreach($frnd_list as $frnd_list){
                  $list_id = $frnd_list['flid'];
                  $frnds_in_list = $_SESSION['do_ofuz_fb']->getFriendsInList($list_id);
                  if(@in_array($friend,$frnds_in_list)){
                    $list_name_array[] = $frnd_list['name'];
                  }
              }
            }
           
            $j++;
            if($i==1){
                $i++;
                echo '<div id="waitimage" style="display:block;">';
                echo '<img src="images/wait30.gif" width="30" height="30" alt="" />';
                echo '</div>';
            }
            if($count == $j){
            ?>
            <script>
                  var thisDiv = document.getElementById("waitimage");
                  thisDiv.style.display = "none";
            </script>
            <?php
            }
            $do_contact = new Contact();
           
            $name = $_SESSION['do_ofuz_fb']->getFbUserName($friend); // will contain the firstand last name in facebook
            $affiliations =  $_SESSION['do_ofuz_fb']->getFbUserAffiliations($friend);
            $work_history = $_SESSION['do_ofuz_fb']->getWorkHistory($friend);
            $profile_url  = $_SESSION['do_ofuz_fb']->getProfileURL($friend);
            $profile_pic_with_logo = $_SESSION['do_ofuz_fb']->getProfilePicWithLogo($friend);
            $friends_data = array("fb_uid"=>$friend,"name"=>$name, "affiliations"=>$affiliations,"work"=>$work_history,"profile_url"=>$profile_url,"pic_with_logo"=>$profile_pic_with_logo,"listname"=>$list_name_array);
           //print_r($friends_data);
            //echo '<br />';
            $do_contact->importFacebookFriends($friends_data);
            $do_contact->free();
           
        }
        //rebuilding the userXX_contact table
        /*$contact_view = new ContactView();
        $contact_view->setUser($_SESSION['do_User']->iduser);
        $contact_view->rebuildContactUserTable();*/

        if($_SESSION["page_from"] == 'reg'){
            $message= 'Facebook contacts have been imported successfully';
        ?>
          <script type="text/javascript">
              window.location = "/welcome_to_ofuz.php?message=<?php echo $message; ?>";
          </script>
        <?php
          }else{
              echo _('Facebook contacts has been imported successfully');
              echo '&nbsp;&nbsp;&nbsp;<a href="contacts.php">Go to Contact Page</a>';
        }
        
     }else{
        echo _('Please connect to facebook first');
        echo '<br />';
        echo _('If you are already connected to facebook please wait for few seconds, page will be auromatically redirected');
        ?>
        <br />
         <!--<a href="#" onclick="FB.Connect.requireSession(); return false;" > <img id="fb_login_image" src="http://static.ak.fbcdn.net/images/fbconnect/login-buttons/connect_light_medium_long.gif" alt="Connect"/> </a>-->
              <a href="#" onclick="FB.Connect.requireSession(); return false;" > <img id="fb_login_image" src="https://s-static.ak.fbcdn.net/images/fbconnect/login-buttons/connect_light_medium_long.gif" alt="Connect"/> </a>
        <?php
     }
   
?>
<div class="spacerblock_80"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>