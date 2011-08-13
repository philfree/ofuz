<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


   /**
    * Default index page.
	*
    * @package PASSiteTemplate
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2001-2004
    * @version 3.0
	*/

  include_once("config.php") ;
  $pageTitle = "Name of the Page" ;
  include("includes/header.inc.php") ;
?>
<DIV id="DRAG_addon_LoginForm" style="top:74px;left:116px;height:184px;width:501px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;classname:LoginFormAddOn;filename:class/;" --><?php $loginForm = new User();$loginForm->sessionPersistent("do_User", "", 36000);if($_GET['message']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div><?php } ?><div class="text"><?php $_SESSION['do_User']->formLogin("registration_user_page.hide.php", "Incorrect username or password", "text");?><br />
        If you have not registered yet, please do so <a href="registration_register.hide.php">here</a> .<br />
        If you forgot your password, you can retrieve it <a href="registration_forgot_password.hide.php">here</a>
        </div>
</DIV>


<DIV id="DRAG_txt_Unnamed" style="top:22px;left:63px;height:25px;width:243px;position:absolute;visibility:visible;z-index:5;border-style:none;" class="text">
<!--META  metainfo="execorder:30;notdragable:no;global:no;" -->This is a sample login page.<br />
</DIV>


<DIV id="DRAG_script_TextLink" style="top:27px;left:363px;height:25px;width:275px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;filename:includes/text_link.script.inc.php;" -->    <?php 
        $textoflink = "Sign in with OpenId Instead" ;
        $pagetolinkto = "registration_login_openid.hide.php";
        $ext_link = "";
        $textlink_styleon = ".linkon";
        $textlink_styleover = ".linkover";
        
    if (strlen($textoflink) > 0){
        $styleon_nodot = str_replace(".", "", $textlink_styleon);
        $styleover_nodot =  str_replace(".", "", $textlink_styleover);
?>
<a href="<?php  if (strlen($ext_link)>0) { echo $ext_link; } else {echo $pagetolinkto; }?>"  class="<?php echo $styleon_nodot?>"  onmouseOver="this.className='<?php echo $styleover_nodot?>'" onmouseout="this.className='<?php echo $styleon_nodot?>'"><?php echo stripslashes($textoflink);?></a>
<?php   } ?>
    
</DIV>



  </body>
</html>