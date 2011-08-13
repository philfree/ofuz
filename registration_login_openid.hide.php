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
<DIV id="DRAG_addon_LoginFormOpenIdAddOn" style="top:98px;left:144px;height:112px;width:377px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;classname:LoginFormOpenIdAddOn;filename:class/;" --><?php if($_GET['openidmessage']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['openidmessage'])); ?></div>
<?php }?>
<?php $do_user = new User();
 $do_user->sessionPersistent("do_User", "signout.php", 36000);
      $_SESSION['do_User']->formLoginOpenId('registration_register_openid.hide.php','registration_user_page.hide.php','Wrong Login'); 
?>
</DIV>


<DIV id="DRAG_txt_Unnamed" style="top:64px;left:99px;height:33px;width:256px;position:absolute;visibility:visible;z-index:5;" class="text">
<!--META  metainfo="execorder:30;" -->Sign in with Open ID<br />
</DIV>



  </body>
</html>