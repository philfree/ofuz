<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/*
 *      contact_sendemail_save_tempatle.php
 *      
 *      Copyright 2009 SQLFusion LLC, Philippe Lewicki <philippe@sqlfusion.com>
 *      
 */


    include_once('config.php');
    $pageTitle = _('Ofuz :: Contact Unsubscribe');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
		
   // include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    //$do_message = new EmailTemplateUser("blank");
 
    // if ($_GET['idtemplate']) {
    //      $do_message->getId( (int)$_GET['idtemplate'] ); 
    // }

?>


<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
    <div class="layout_header">
        <div class="layout_logo">
            <a href="/index.php"><img src="/images/ofuz_logo.jpg" width="188" height="90" alt="" /></a>
        </div>
    </div>

<?php 
//$thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); 

 //$do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); 
 ?>
 
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php _('Opt-out'); ?></span>
        </div>
    </div>
    <div class="contentfull">
	<!--	<div class="messageshadow">
            <div class="messages">  -->
            <?php
				$msg = new Message(); 
				$sender_name = $_SESSION['user_unsub']->getFullName();
				$msg->setData(Array("user_name" => $sender_name));
				$msg->getMessage("contact unsubscribe email");
				$msg->displayMessage();
            ?>
      <!--      </div>
        </div> -->

    </div>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
