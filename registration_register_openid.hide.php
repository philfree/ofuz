<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * Default Template web pages 
   */
  $pageTitle = "Page Title";
  $Author = "PAS Pagebuilder";
  $Keywords = "PAS Pagebuilder SQLFusion Web authoring tool";
  $Description = "The best way to built rich web sites";
  $background_color = "white";
  $background_image = "none";
  include_once("config.php");
  include_once("includes/header.inc.php"); include_once("pb_globaldivs.sys.php");?>
<DIV id="DRAG_txt_Unnamed" style="top:30px;left:49px;height:50px;width:350px;position:absolute;visibility:visible;z-index:5;" class="text">
<!--META  metainfo="execorder:30;" -->Sample registration page.
</DIV>


<DIV id="DRAG_addon_RegistrationFormOpenId" style="top:76px;left:75px;height:505px;width:457px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;classname:RegistrationFormOpenIdAddOn;filename:class/;" --><?php if($_GET['openidmessage']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['openidmessage'])); ?></div>
<?php  }?>
<?php $do_user = new User();
      $do_user->sessionPersistent("do_User", "signout.php", 36000);
      $_SESSION['do_User']->formRegisterOpenId('registration_user_page.hide.php',
                                                     'regthank',
                                                     '',
                                                     ''
                                                     ); 
?>
</DIV>



  </body>
</html>