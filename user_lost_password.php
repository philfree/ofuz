<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Lost Password';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
  include_once("config.php");
  include_once("includes/header.inc.php"); ?>
<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
    <div class="loginbg2">
     <div><?php if($_GET['message']) { ?>
     <br /><div class="error_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div>
     <?php }?></div>
     <br /><br /><br />
     <div>Enter your email address:</div><br />
<?php
$do_user = new User();
$do_user->formGetPassword('Submit','Your password has been sent','Enter your email address'); 
?>
   </div>
</div>


  </body>
</html>