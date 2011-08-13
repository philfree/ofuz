<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

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
            if (is_object($friends) && count($friends->user) > 0) {
                foreach ($friends->user as $user) {
                    $idcontact = $do_twitter->cronFeedStatus($iduser, $user->id, strtotime($user->status->created_at));
                    if ($idcontact !== false) {
                        $link = '<br /><a href="http://twitter.com/'.$user->screen_name.'" target="_blank">Back to the Source of the Article</a><br />';
                        $note_content = nl2br(strip_tags($user->status->text));
                        $do_contact_note = new ContactNotes();
                        $do_contact_note->idcontact = $idcontact;
                        $do_contact_note->note = $note_content.$link;
                        $do_contact_note->date_added = date('Y-m-d');
                        $do_contact_note->iduser = $iduser;
                        $do_contact_note->type = 'Twitter';
                        $do_contact_note->add();

                        $workfeed = new WorkFeedTwitterImport();
                        $workfeed->addTweet($do_contact_note, 'http://twitter.com/'.$user->screen_name, $note_content);

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
                        $do_contact_note->type = 'Twitter';
                        $do_contact_note->iduser = $iduser;
                        $do_contact_note->add();

                        $workfeed = new WorkFeedTwitterImport();
                        $workfeed->addTweet($do_contact_note, 'http://twitter.com/'.$user->screen_name, $note_content);
                    }
                }
            }
        }
    }
}
?>