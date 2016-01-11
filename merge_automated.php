<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Merge Contacts';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
?>
<script type="text/javascript">
    //<![CDATA[
    //]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline14"><?php echo _('Merge Contacts');?></span>
            <div class="solidline"></div>
        </div>
    </div>
    <div class="contentfull">
     <?php
          if(isset($_POST['ck'])){
            $contact_ids = $_POST['ck'];
          }
          if(count($contact_ids)< 2 ){
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo 'You should have atleat 2 Contacts to be merged';
              echo '<br /><a href="/contacts.php">Click To Back</a>';
              echo '</div></div><br /><br />';
          }else{
              $do_merge = true;
              if (!is_object($_SESSION['do_contact'])) {
                  $do_contact = new Contact();
                  $do_contact->sessionPersistent("do_contact", "index.php", 36000);
              }
              $error_txt = _('You have choosen to merge some contacts which are shared by some of your Co-Workers and you do not have access to merge them. Here are the contacts that are not permitted to be merged:');
              $error_txt .= '<br />';
              foreach($contact_ids as $contact){
                  if(!$_SESSION['do_contact']->isContactOwner($contact)){
                     $error_txt .= '<i><b>'.$_SESSION['do_contact']->getContactFullName($contact).'</b></i><br />';
                     $do_merge = false; 
                  }
              }
              if(!$do_merge){
                $error_txt .= '<br /><a href="/contacts.php">Click To Back</a>';
                echo '<div class="messageshadow_unauthorized">';
                echo '<div class="messages_unauthorized">';
                echo $error_txt;
                echo '</div></div><br /><br />';
                exit;
              }
              $idcontact = $_SESSION['do_contact']->MergeContactsAutomated($contact_ids); 
              if($idcontact){
                  $do_contact_edit = new Contact();
                  $do_contact_edit->getContactDetails($idcontact);
                  $do_contact_edit->sessionPersistent("ContactEditSave", "contact.php", 3600);
                  echo _('Contacts are merged successfully.');
                  $_SESSION['edit_from_page'] = 'contacts.php';                  
		  echo '<br />';
                  echo _('Please wait page will be redirected in 5 seconds.');
              ?>
                  <script type="text/javascript">
                  //<![CDATA[
                      setTimeout("location.href='/contact_edit.php'", 5000);
                  //]]>
                  </script>
              <?php
              }else{
                  echo _('An Error ocured while merging. Please try again');
                  echo '<br /><a href="/contacts.php">'._('Click To Back').'</a>';
              }
          }
     ?>
    </div>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>