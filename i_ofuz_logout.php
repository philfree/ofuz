<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
?>
<div class="itopblue">
<?php 
       $e_logout = new Event("do_User->eventLogout");
       $e_logout->addParam("goto", "i_login.php");
          ?><div class = "navtab"><div class="navtab_text"><a href="<?php echo $e_logout->getUrl(); ?>">Logout</a></div></div>
        <div align="right" style="float:right;"><?php echo $mobile_local_bottom_nav_links; // to be aligned from the right ?></div>
</div>