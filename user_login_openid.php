<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Login';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
  include_once("config.php");
  include_once("includes/header.inc.php"); ?>
<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
    <div class="loginbg2">

<!--META  metainfo="execorder:30;classname:LoginFormOpenIdAddOn;filename:class/;" --><?php if($_GET['openidmessage']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['openidmessage'])); ?></div>
<?php }?>
<?php $do_user = new User();
 $do_user->sessionPersistent("do_User", "signout.php", 36000);
      $_SESSION['do_User']->formLoginOpenId('user_register_openid.php','index.php','Wrong Login'); 
?>
    </div>
</div>



  </body>
</html>