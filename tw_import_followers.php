<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Twitter Setup';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');

    $do_twitter = new OfuzTwitter();
    $serialized_token = $do_twitter->getAccessToken();

    if ($serialized_token) {
        include_once('includes/header.inc.php');
?>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Import Contacts From Twitter');?></span>
        </div>
    </div>
    <div class="contentfull">
        <div class="spacerblock_80"></div>
        <div class="spacerblock_80">
            <div id="waitimage" class="center_text">
                <img class="center_elem" src="/images/wait30.gif" width="30" height="30" alt="" />
            </div>
        </div>
<?php
        $token = unserialize($serialized_token);
        $tw_user_id = $token->getParam('user_id');
        $tw_screen_name = $token->getParam('screen_name');
        $tw_config = $do_twitter->getTwitterConfig();
        $ofuz_twitter = new Ofuz_Service_Twitter($tw_user_id, $tw_config, $token);
        $followers = $ofuz_twitter->userFollowers(false); // false = do not fetch Status

        if (count($followers->user) > 0) {
            foreach ($followers->user as $user) {
                $user_id = $user->id;
                $name = $user->name; // will contain the first and last name
                $screen_name = $user->screen_name;
                $description = $user->description;
                $profile_image_url = $user->profile_image_url;
                $url = $user->url;

                $friend_data = array(
                    'user_id'=>$user_id,
                    'name'=>$name,
                    'screen_name'=>$screen_name,
                    'description'=>$description,
                    'profile_image_url'=>$profile_image_url,
                    'url'=>$url
                );
                $do_twitter->importTwitterFriend($friend_data, 'Follower');
            }

            //rebuilding the userXX_contact table
            $contact_view = new ContactView();
            $contact_view->setUser($_SESSION['do_User']->iduser);
            $contact_view->rebuildContactUserTable();

            echo _('Your Twitter followers have been successfully imported.');
            echo '&nbsp;&nbsp;&nbsp;<a href="contacts.php">',_('Go to the Contacts Page'),'</a>';
        }
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    $("#waitimage").hide(0);
});
//]]>
</script>
    <div class="spacerblock_80"></div><div class="spacerblock_80"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<div class="layout_footer"></div>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
<?php
    } else {
    	header('Location: settings_twitter.php');
    }
?>