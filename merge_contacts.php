<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

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
function fnHighlightCoworkers(area) {
	var cwid=$("#cwid"+area);
	var div=$("#cw"+area);
	var ctlbar=$("#coworker_ctlbar");
    cwid.attr("checked",(cwid.is(":checked")?"":"checked"));
    if (cwid.is(":checked")) {
        div.css("background-color", "#ffffdd");
        if(ctlbar.is(":hidden"))ctlbar.slideDown("fast");
    } else {
        div.css("background-color", "#ffffff");
        var tally=0;
        $("input[type=checkbox][checked]").each(function(){tally++;});
        if(tally==0)ctlbar.slideUp("fast");
    }
}

function setContactForCoworker(){
  $("#do_contact_sharing__eventShareContactsMultiple").submit();
}
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
              echo _('You should have atleat 2 Contacts to be merged');
              echo '<br /><a href="contacts.php">'._('Click To Back').'</a>';
          }else{
              if (!is_object($_SESSION['do_contact'])) {
                  $do_contact = new Contact();
                  $do_contact->sessionPersistent("do_contact", "index.php", 36000);
              }
              $e_marge = new Event("do_contact->eventMergeContacts");
              $e_marge->addEventAction("mydb.gotoPage", 304);
              $e_marge->addParam("goto", "contacts.php");
              $e_marge->addParam("contact_ids", $contact_ids);
              echo $e_marge->getFormHeader();
              echo $e_marge->getFormEvent();
              foreach($contact_ids as $contact){
                 echo '<div class="contact">';
                 $_SESSION['do_contact']->getContactDetails($contact);
                 echo '<span style="color:orange;">Keep&nbsp;&nbsp;<input type="radio" name = "cont_id[]" value ="'.$contact.'"></span>';
                 echo '<b>'.$_SESSION['do_contact']->firstname.' '.$_SESSION['do_contact']->lastname.'</b><br />'; 
                 echo '<b>Contact Information:</b><br />';

                 $ContactPhone = $_SESSION['do_contact']->getChildContactPhone();
                 if($ContactPhone->getNumRows()){
                      echo "<b>Phone</b><br />";
                      echo '<div style="width:auto;margin-left:30px;">';
                      while($ContactPhone->next()){
                          echo '<span class="co_worker_pending">Keep&nbsp;&nbsp;<input type="checkbox" name="cont_phone[]" value = "'.$ContactPhone->idcontact_phone.'">&nbsp;</span>';
                          echo '<div class="merge_content">';
                          echo $ContactPhone->phone_type;
                          echo ': '.$ContactPhone->phone_number;
                          echo '</div>';
                      }
                      echo '</div>';
                  }

                  $ContactEmail = $_SESSION['do_contact']->getChildContactEmail();
                  if($ContactEmail->getNumRows()){
                      echo "<b>"._('Email')."</b><br />";
                      echo '<div style="width:auto;margin-left:30px;">';
                      while($ContactEmail->next()){
                         echo '<span class="co_worker_pending">Keep&nbsp;&nbsp;<input type="checkbox" name="cont_email[]" value = "'.$ContactEmail->idcontact_email.'">&nbsp;</span>';
                         echo '<div class="merge_content">';
                         echo $ContactEmail->email_type;
                         echo ':'.$ContactEmail->email_address;
                         echo '</div>';
                      }
                      echo '</div>';
                  }

                  $ContactInstantMessage = $_SESSION['do_contact']->getChildContactInstantMessage();
                  if($ContactInstantMessage->getNumRows()){
                      echo "<b>"._('IM')."</b><br />";
                      echo '<div style="width:auto;margin-left:30px;">';
                      while($ContactInstantMessage->next()){
                          echo '<span class="co_worker_pending">Keep&nbsp;&nbsp;<input type="checkbox" name="cont_im[]" value = "'.$ContactInstantMessage->idcontact_instant_message.'">&nbsp;</span>';
                          echo '<div class="merge_content">';
                          echo $ContactInstantMessage->im_options;
                          echo ': '.$ContactInstantMessage->im_type;
                          echo ': '.$ContactInstantMessage->im_username;
                          echo '</div>';
                      }
                      echo '</div>';
                  }
                  
                  $ContactWebsite = $_SESSION['do_contact']->getChildContactWebsite();
                  if($ContactWebsite->getNumRows()){
                      echo "<b>"._('Website')."</b><br />";
                      echo '<div style="width:auto;margin-left:30px;">';
                      while($ContactWebsite->next()){
                          echo '<span class="co_worker_pending">Keep&nbsp;&nbsp;<input type="checkbox" name="cont_website[]" value = "'.$ContactWebsite->idcontact_website.'">&nbsp;</span>';
                          echo '<div class="merge_content">';
                          echo  $ContactWebsite->website_type;
                          echo ':'.$ContactWebsite->website;
                          echo '</div>';
                      }
                      echo '</div>';
                   }

                   $ContactAddr = $_SESSION['do_contact']->getChildContactAddress();
                   if($ContactAddr->getNumRows()){
                      echo "<b>"._("Address")."</b><br />";
                      echo '<div style="width:auto;margin-left:30px;">';
                      while($ContactAddr->next()){
                          echo '<span class="co_worker_pending">Keep&nbsp;&nbsp;<input type="checkbox" name="cont_addr[]" value = "'.$ContactAddr->idcontact_address.'">&nbsp;</span>';
                          echo '<div class="merge_content">';
                          echo  _('Address Type :').$ContactAddr->address_type;
                          echo  '<br />'._('Address :').$ContactAddr->address;
                          echo  '<br />'._('Street :').$ContactAddr->street;
                          echo  '<br />'._('Zipcode :').$ContactAddr->zipcode;
                          echo  '<br />'._('City :').$ContactAddr->city;
                          echo  '<br />'._('State :').$ContactAddr->state;
                          echo  '<br />'._('Country :').$ContactAddr->country;
                          //echo '<br />';
                          echo '</div>';
                      }
                      echo '</div>';
                   }

                   $ContactRSS = $_SESSION['do_contact']->getChildContactRssFeed();
                   if($ContactRSS->getNumRows()){
                      echo "<b>RSS Feed</b><br />";
                      echo '<div style="width:600px;margin-left:30px;">';
                      while($ContactRSS->next()){
                          echo '<span class="co_worker_pending">Keep&nbsp;&nbsp;<input type="checkbox" name="cont_rss[]" value = "'.$ContactRSS->idcontact_rss_feed.'">&nbsp;</span>';
                          echo '<div class="merge_content">';
                          echo  _('Feed Type :').$ContactRSS->feed_type;
                          echo  '<br />'._('RSS Feed URL :').$ContactRSS->rss_feed_url;
                          echo  '<br />'._('Username :').$ContactRSS->username;
                          echo '</div>';
                      }
                      echo '</div>';
                   }

                  echo '</div>';
                  echo '<div class="solidline"></div>';
              }
              echo $e_marge->getFormFooter(_('Continue'));
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