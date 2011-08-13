<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


 
$password = $fields["password"];
if($password != ''){
  $do_user_rel = new UserRelations();
  $enc_password = $do_user_rel->encrypt($password);
  $fields["password"] = $enc_password;
  $this->updateParam("fields", $fields) ;
}

?>