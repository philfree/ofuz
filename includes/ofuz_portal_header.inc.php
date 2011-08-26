<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
?>
<div class="topblue" style="height:50px">
    <div style="width: 100%; height: 30px;">
        <span class="rightlinks">
             <span class="whitelink">
                 <!-- &nbsp;<a href="contact_edit.php">Edit your contact information</a> -->
             </span>
        </span>
        <div class="myname">
   <?php echo $_SESSION['do_contact']->firstname, ' ', $_SESSION['do_contact']->lastname; ?> </div>
    </div>
</div>
