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
<DIV id="DRAG_addon_ForgotPasswordForm" style="top:120px;left:222px;height:45px;width:529px;position:absolute;visibility:visible;z-index:5;border-style:none;">
<!--META  metainfo="execorder:30;classname:ForgotPasswordFormAddOn;filename:class/;notdragable:no;global:no;" --><?php if($_GET['message']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div>
<?php }?>
<?php $do_user = new User();
      $do_user->formGetPassword('Submit','Your password has been sent','Enter your email address'); 
?>
</DIV>


<DIV id="DRAG_txt_Unnamed" style="top:79px;left:159px;height:21px;width:392px;position:absolute;visibility:visible;z-index:5;" class="text">
<!--META  metainfo="execorder:30;" -->Sample page with a forgot password form
</DIV>



  </body>
</html>