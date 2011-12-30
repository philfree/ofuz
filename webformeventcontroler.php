<?php 
/**
 * Webform eventcontoler scipt will get post content form webform html section and process to the database. 
 * @param POST data
 * Created on Dec 30 2011
 * */
include_once("config.php") ;
  
$fields = array();
$efid = $_POST['fid'];
$euid = $_POST['uid'];
$tags = $_POST['tags'];


$do_user_rel = new UserRelations();
$fid=$do_user_rel->decrypt($efid);
$uid=$do_user_rel->decrypt($euid);


//$do_userform = new WebFormUser();
//$do_userform->posteventAddContact($fid,$_POST['fields'],$nxturl,$uid,$tags);

$evctl = new EventControler;
$evctl->addParam('fields', $_POST['fields']);
$evctl->addParam('uid', $uid);
$evctl->addParam('fid', $fid);
$do_userform = new WebFormUser();
$do_userform->eventAddContact($evctl);

?>
