<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

include_once('config.php');

/*$do_all_user = new User();
$do_all_user->getAll();
$do_enc = new UserRelations();
$q = new sqlQuery($GLOBALS['conx']);
while($do_all_user->next()){
  $pass =$do_all_user->password;
  if($pass != ''){
      $enc_pass = $do_enc->encrypt($pass);  
      $q->query("update user set password = '".$enc_pass."' where iduser = ".$do_all_user->iduser);
  }
  
}
*/

phpinfo();


?>