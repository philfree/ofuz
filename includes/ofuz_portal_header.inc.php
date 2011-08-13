<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
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
