<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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

<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
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
        $friends = $ofuz_twitter->userFriends(array('id'=>$tw_user_id));

        if (count($friends->user) > 0) {
            foreach ($friends->user as $user) {
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
                $do_twitter->importTwitterFriend($friend_data);
            }

            //rebuilding the userXX_contact table
            $contact_view = new ContactView();
            $contact_view->setUser($_SESSION['do_User']->iduser);
            $contact_view->rebuildContactUserTable();

            echo _('Your Twitter contacts have been successfully imported.');
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
</body>
</html>
<?php
    } else {
    	header('Location: ww_s1_settings_twitter.php');
    }
?>