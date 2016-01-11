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


  include_once 'facebook_client/facebook.php';
  include_once 'class/OfuzFacebook.class.php';
  $facebook = new Facebook(FACEBOOK_API_KEY, FACEBOOK_APP_SECRET);
  if (!is_object($_SESSION['do_ofuz_fb'])) {
      $do_ofuz_fb =  new OfuzFacebook($facebook);
      $do_ofuz_fb->sessionPersistent("do_ofuz_fb", "index.php", 36000);
  }

  $_SESSION['do_ofuz_fb']->istLoggedInFacebook();
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
  <title>Ofuz</title>
  <link rel="stylesheet" href="http://static.ak.connect.facebook.com/css/fb_connect.css" type="text/css" />
</head>
<body>
  <h1>Facebook Connect</h1>
  <p>
    Connect To Facebook
  </p>
  <p>
  <fb:login-button>
  </fb:login-button>
  </p>
    <script src="http://static.ak.connect.facebook.com/js/api_lib/v0.4/FeatureLoader.js.php"
    type="text/javascript"></script>

  <script type="text/javascript">
    FB_RequireFeatures(["XFBML"], function()
    {
      FB.Facebook.init(FACEBOOK_API_KEY, "http://ofuz.net/xd_receiver.htm");
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
<!--<fb:serverfbml>  <script type="text/fbml"> <fb:prompt-permission perms="create_event">Allow creating event to the application</fb:prompt-permission>  </script> </fb:serverfbml>-->
<?php  
     if($_SESSION['do_ofuz_fb']->fb_uid){
       // echo '<fb:prompt-permission perms="email">Would you like to receive email from our application?</fb:prompt-permission>';
        $event_info = array();
        $event_info['name'] = 'Party';
        $event_info['category'] = 1;
        $event_info['subcategory'] = 1;
        $event_info['host'] = 'Ofuz';
        $event_info['location'] = 'Los Angeles';
        $event_info['city'] = 'Los Angeles';
        $event_info['start_time'] = gmmktime(2,0,0,27,3,2009); //Converts time to UTC
        $event_info['end_time'] = gmmktime(5,0,0,27,3,2009); //COnverts time to UTC

        try
        {
        $event_id = $facebook->api_client->events_create($event_info);
        echo 'Event Created! Event Id is: '.$event_id;
        }
        catch(Exception $e)
        {
        echo 'Error message: '.$e->getMessage().' Error code:'.$e->getCode();
        } 
     }
   /* foreach($friends as $friend){
      ?>
          <fb:profile-pic uid="<?php echo $friend;?>" facebook-logo="true" linked="true" size="square"></fb:profile-pic>
          <br />
          <fb:user-status uid="<?php echo $friend;?>" linked="true"></fb:user-status>
          <br />
      <?php

    }*/

?>
<!--</div>-->
<!--<div style="margin-top:50px;margin-left:620px;" >
  <fb:comments canpost="true" candelete="true" numposts="5"></fb:comments>
</div>-->

<!--</fb:serverfbml >-->
<!--<h2>XFBML rendered as an iframe from facebook.com</h2>-->
	<!-- Server FBML tags are necessary for Facebook elements which must be hosted on Facebook.  The request form is one of these, as demonstrated below.  See the documentation for a list of such elements.  -->	
<!--<fb:serverfbml style="width: 755px;">
        <script type="text/fbml">
        <fb:fbml>
        <fb:request-form 
          action="<url for post invite action, see wiki.developers.facebook.com for fb:request-form details>" 
          method="POST" 
          invite="true" 
          type="XFBML" 
          content="This is a test invitation from XFBML test app
          <fb:req-choice url='see wiki page for fb:req-choice for details' 
          label='Ignore the Connect test app!' />">
          <fb:multi-friend-selector 
            showborder="false" 
            actiontext="Invite your friends to use Connect.">
        </fb:request-form>
        </fb:fbml>
        </script>
</fb:serverfbml>-->
<?php //} ?>





</body>
</html>
