<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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
<DIV id="DRAG_txt_Unnamed" style="top:171px;left:347px;height:19px;width:386px;position:absolute;visibility:visible;z-index:5;" class="text">
<!--META  metainfo="execorder:30;" -->Thank for registering into our site.
</DIV>


<DIV id="DRAG_script_TextLink" style="top:220px;left:394px;height:28px;width:170px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;filename:includes/text_link.script.inc.php;" -->    <?php 
        $textoflink = "Click here to sign-on" ;
        $pagetolinkto = "registration_login.hide.php";
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