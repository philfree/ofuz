<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"> 
  <head>
    <title>Ofuz :: Manual Twitter import</title> 
    <meta name="author" content="SQLFusion LLC" /> 
    <meta name="keywords" content="Keywords for search engine" /> 
    <meta name="description" content="Description for search engine" /> 
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate" /> 
    <meta http-equiv="Pragma" content="no-cache" /> 
    <meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
    <meta http-equiv="content-style-type" content="text/css" />
    <meta http-equiv="refresh" content="300" />
  </head>
  <body>
  Manual Twitter import.  This page refreshes every 5 minutes. <?php echo date('Y-m-d h:m:s'); ?>
  </body>
</html>
<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

//$GLOBALS['cfg_full_path'] = '/server/vhtdocs/ofuz.net/';

include_once('config.php');

$do_twitter = new OfuzTwitter();
$tw_config = $do_twitter->getTwitterConfig();

$do_twitter->cronGetAccessTokens();
$do_twitter->getValues();
if ($do_twitter->getNumRows() > 0) {
    while ($do_twitter->next()) {
        set_time_limit(300);
        $iduser = $do_twitter->iduser;
        $serialized_token = $do_twitter->tw_token;
        if ($serialized_token) {
            $token = unserialize($serialized_token);
            $tw_user_id = $token->getParam('user_id');
            $ofuz_twitter = new Ofuz_Service_Twitter($tw_user_id, $tw_config, $token);
            $imported = array();

            // Import Notes from friends
            $friends = $ofuz_twitter->userFriends(array('id'=>$tw_user_id));
            //echo '<pre>'; print_r($friends); echo '</pre>';
            if (is_object($friends) && count($friends->user) > 0) {
                foreach ($friends->user as $user) {
                	//echo '<pre>'; print_r($user->status); echo '</pre>';
                    $idcontact = $do_twitter->cronFeedStatus($iduser, $user->id, strtotime($user->status->created_at));
                    if ($idcontact !== false) {
                        $link = '<br /><a href="http://twitter.com/'.$user->screen_name.'" target="_blank">Back to the Source of the Article</a><br />';
                        $note_content = nl2br(strip_tags($user->status->text));
                        $do_contact_note = new ContactNotes();
                        $do_contact_note->idcontact = $idcontact;
                        $do_contact_note->note = $note_content.$link;
                        $do_contact_note->date_added = date('Y-m-d');
                        $do_contact_note->iduser = $iduser;
                        $do_contact_note->add();

                        $workfeed = new WorkFeedRssFeedImport();
                        $workfeed->addRssFeed($do_contact_note, 'http://twitter.com/'.$user->screen_name, $note_content);

                        $imported[] = $user->id;
                    }
                }
            }

            // Import Notes from followers
            $followers = $ofuz_twitter->userFollowers(true);
            if (is_object($followers) && count($followers->user) > 0) {
                foreach ($followers->user as $user) {
                    // Don't import twice; friends may be followers
                    if (in_array($user->id, $imported)) {
                        continue;
                    }
                    $idcontact = $do_twitter->cronFeedStatus($iduser, $user->id, strtotime($user->status->created_at));
                    if ($idcontact !== false) {
                        $link = '<br /><a href="http://twitter.com/'.$user->screen_name.'" target="_blank">Back to the Source of the Article</a><br />';
                        $note_content = nl2br(strip_tags($user->status->text));
                        $do_contact_note = new ContactNotes();
                        $do_contact_note->idcontact = $idcontact;
                        $do_contact_note->note = $note_content.$link;
                        $do_contact_note->date_added = date('Y-m-d');
                        $do_contact_note->iduser = $iduser;
                        $do_contact_note->add();

                        $workfeed = new WorkFeedRssFeedImport();
                        $workfeed->addRssFeed($do_contact_note, 'http://twitter.com/'.$user->screen_name, $note_content);
                    }
                }
            }
        }
    }
}
?>