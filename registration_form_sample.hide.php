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


<DIV id="DRAG_script_registrationForm" style="top:76px;left:159px;height:402px;width:753px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;filename:includes/register.script.inc.php;" --><?php 
$send_email =  "True";
$thankyoupage = "registration_thank_you_sample.hide.php";


if($_GET['message']) { echo "<div class=\"error_message\">".htmlentities(stripslashes($_GET['message']))."</div>"; }

$do_user = new User();
$do_user->formRegister($thankyoupage, $send_email);

?>
</DIV>



  </body>
</html>