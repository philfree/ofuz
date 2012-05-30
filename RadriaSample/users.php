<?php 
// Copyright 2012 SQLFusion LLC           info@sqlfusion.com

   /**
    * User Add
	*
    * @package RadriaSampleSite
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2012
    * @version 1.0
	*/


  
  include_once("config.php") ;
  
  $pageTitle = "Users" ;
  $Author = "SQLFusion";
  $Keywords = "Keywords for search engine";
  $Description = "Description for search engine";
  $background_color = "white";
  $background_image = "";
  
  include("includes/header.inc.php") ;
  
  $do_user = new User();
  $do_user->getAll();
?>
List of users:<br>
<?php
  while($do_user->next()) {
?>
<a href="user_detail.php?id=<?php echo $do_user->iduser; ?>"><?php echo $do_user->firstname; ?></a> | <?php echo $do_user->lastname; ?><br>
<?php } ?>



<?php
  include("includes/footer.inc.php");
?>
