<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: My Information';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
?>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
  $GLOBALS['thistabsetting'] = 'My Profile';
  include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">        
  
     <div class="contentfull">

 <!--   <div class="messageshadow">
            <div class="messages">
            <?php
    $msg = new Message(); 
    echo $msg->getMessage('my profile instruction');
    //echo '<br /><br />';
    //echo _('Your Profile Page is : '). $GLOBALS['cfg_ofuz_site_http_base'].'profile/'.$_SESSION['do_User']->username;
            ?>
            </div>-->
        </div><br />

        <?php 
      $do_contact = new Contact();
            if(!empty($_SESSION['do_User']->idcontact) && $_SESSION['do_User']->idcontact != 0){
               $idcontact=$_SESSION['do_User']->idcontact;
      $do_contact->getId($_SESSION['do_User']->idcontact);
    }else{
   // Add a new contact and update the idcontact to the user table for the user
   $do_contact->addNew();
   $do_contact->firstname = $_SESSION['do_User']->firstname ;
   $do_contact->lastname = $_SESSION['do_User']->lastname ;
   $do_contact->iduser = $_SESSION['do_User']->iduser ;
   $do_contact->add();
   $idcontact_inserted = $do_contact->getPrimaryKeyValue();
   $do_contact_view = new ContactView();
   $do_contact_view->setUser($_SESSION['do_User']->iduser);
   $do_contact_view->addFromContact($do_contact);
   $_SESSION['do_User']->idcontact = $idcontact_inserted;
   $_SESSION['do_User']->updateUserContact($idcontact_inserted);
   $do_contact->getId($idcontact_inserted);
    }
    $_SESSION['edit_from_page'] = 'settings_myinfo.php';
    $do_contact->sessionPersistent("ContactEditSave", "contact.php", OFUZ_TTL);
              ?>

        <div class="profile_subject_line">
        <div class="my_profile">My Profile&nbsp;
        <span class="edit_profile">
          <a href="/contact_edit.php">Edit Profile</a>
          </b> 
          </span> 
        </div>
        </div>

 <div class="my_profile_class">

            <div class="left_profile_box">          
            
            <div class="profile_photo">
            <?php if ($_SESSION['ContactEditSave']->picture != '') { ?>
                <img src="<?php echo $_SESSION['ContactEditSave']->getContactPicture(); ?>" height="100%"  width = 100% alt="" />
            <?php } ?>

            </div>            
            <div class="profile_edit_photo"><a href="/contact_edit.php">Edit Photo</a></div>
            </div>
        
            
            <div class="right_profile_box">
            <div><a class="profile_name"><?php echo $_SESSION['ContactEditSave']->firstname.' '.$_SESSION['ContactEditSave']->lastname;?></a>          
            <br/><a class="profile_title"><?php echo $_SESSION['ContactEditSave']->position;?> at </a>  <a class="profile_company"><?php echo $_SESSION['ContactEditSave']->company;?></a>
            </div>
    </div>

    </div>
    
      
<br>    
<div id="profile_subject_line">       
<span class="profile_text_subjectline">Bio</span>  


<span class="profile_text_link"><a href="/profile_edit.php">Edit Bio</a></span>

</div>

<div class="profile_line"></div>

<div class="profile_text_content">
<p>
  <?php    
      $do_userprofile=new UserProfile();
      $profile_information=$do_userprofile->getProfileInformation($_SESSION['do_User']->iduser);    
      echo $profile_information['job_description'];
  ?>
</p>
</div>


<div id="profile_subject_line">       
<span class="profile_text_subjectline">Contact Information</span>  
<!~-Needs a link to Edit Bio--->
<span class="profile_text_link"><a href="/contact_edit.php">Edit Contact Info</a></span>
</div>

<div class="profile_line"></div>

<div class="profile_ci_text">

            <div>
            <br>
           
        <?php
                
                $ContactEmail = $_SESSION['ContactEditSave']->getChildContactEmail();
                    if($ContactEmail->getNumRows()){                       
                      while($ContactEmail->next()){
                          echo "</br>";
                          echo "<img class=profile_icon src=/images/profile_icon_email.png alt=  height=11 width=16>";
                             echo '<a class=profile_ci_text  href="mailto:'.$ContactEmail->email_address.'" title="'.$ContactEmail->email_type.'">'.$ContactEmail->email_address.'</a>';                          
                        }
                    }else{
                    echo "<img class=profile_icon src=/images/profile_icon_email.png alt=  height=11 width=16>";
                      } ?>
           
   </div>

            <div>
            <br>
          <img src="/images/profile_icon_website.png " alt=" " height="21" width="16">
            <a class="profile_ci_text">
              <?php
                $ContactWebsite = $_SESSION['ContactEditSave']->getChildContactWebsite();
                    if($ContactWebsite->getNumRows()){                        
                        while($ContactWebsite->next()){
                            echo $ContactWebsite->getDisplayLink();
                           
                        }
                    }
                ?>
          </a>
   </div>


            <div>
            <br>
              <?php
        



             $ContactInstantMessage = $_SESSION['ContactEditSave']->getChildContactInstantMessage();
    if ( $ContactInstantMessage->getNumRows()) {
        while($ContactInstantMessage->next()){
            echo '<div class="layout_lineitem">';
            if ( $ContactInstantMessage->im_type == 'AIM') {
                echo '<img class="profile_icon" src="/images/profile_icon_aim.png" width="16" height="16" alt="" />';
                echo "<a class=profile_ci_text>".$ContactInstantMessage->im_username."</a>";
            } else if ( $ContactInstantMessage->im_type == 'Google Talk') {
                echo '<img class="profile_icon" src="/images/profile_icon_gtalk.png" width="16" height="16" alt="" />';
                echo "<a class=profile_ci_text>".$ContactInstantMessage->im_username."</a>";
            } else if ( $ContactInstantMessage->im_type == 'Jabber') {
                echo '<img class="profile_icon" src="/images/profile_icon_jabber.png" width="16" height="16" alt="" />';
                echo "<a class=profile_ci_text>".$ContactInstantMessage->im_username."</a>";
            } else if ( $ContactInstantMessage->im_type == 'MSN') {
                echo '<img class="profile_icon" src="/images/profile_icon_msn.png" width="16" height="16" alt="" />';
                echo "<a class=profile_ci_text>".$ContactInstantMessage->im_username."</a>";
            } else if ( $ContactInstantMessage->im_type == 'Skype') {
                echo '<img class="profile_icon" src="/images/profile_icon_skype.png" width="16" height="16" alt="" />';
                echo "<a class=profile_ci_text>".$ContactInstantMessage->im_username."</a>";
            } else if ( $ContactInstantMessage->im_type == 'Yahoo') {
                echo '<img class="profile_icon" src="/images/profile_icon_yahoo.png" width="16" height="16" alt="" />';
                echo "<a class=profile_ci_text>".$ContactInstantMessage->im_username."</a>";
            }

        }
    }else{
      echo '<img class="profile_icon" src="/images/profile_icon_skype.png" width="16" height="16" alt="" />';
    }
  ?>           
</div>

            <div>
            <br>
             <?php               
            

    $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
      if ($ContactPhone->getNumRows()) {
        while($ContactPhone->next()){
            echo '<div class="layout_lineitem">';
            if ($ContactPhone->phone_type == 'Work') {
                echo '<img class="profile_icon" src="/images/profile_icon_phonew.png" width="16" height="15" alt="" />';
            } else {
                echo '<img class="profile_icon" src="/images/profile_icon_phonem.png" width="16" height="18" alt="" />';
            }
            echo '<a class="profile_ci_text" href="tel:'.$ContactPhone->phone_number.'">'.$ContactPhone->phone_number.'</a>';
            echo '</div>',"\n";
        }
    }else{
      echo '<img class="profile_icon" src="/images/profile_icon_phonew.png" width="16" height="15" alt="" />';

    }
?>
     
   </div>


<br/>
<div style="margin-left:36px;">
<!--Need a link to the Address Line-->
<?php
  $ContactAddress = $_SESSION['ContactEditSave']->getChildContactAddress();
  if ($ContactAddress->getNumRows()) {   
    while($ContactAddress->next()) {

    echo nl2br($ContactAddress->address) . "<br>";

   }
      }
?>

</div>



</div>
<div class="contact_content">
</div>
</div>

<br> 
<div class="profile_text_subjectline">Quick Links</div> 
<div class="profile_line">
</div> 

<div class="profile_text">
<p class="public_profile_url">Public Profile URL</p>


<?php
       $url = $GLOBALS['cfg_ofuz_site_http_base'].'profile/'.$_SESSION['do_User']->username;
       $url = urlencode($url);
  ?>
 </div>
<div class="profile_quicklink_userprofile"><p><?php echo '<a href="'.$GLOBALS['cfg_ofuz_site_http_base'].'profile/'.$_SESSION['do_User']->username.'" target="_blank">'.$GLOBALS['cfg_ofuz_site_http_base'].'profile/'.$_SESSION['do_User']->username.'</a>';?></p></div> 
<p>This is your personal web address to share with others</p>
<br>

        <p class="public_profile_url">
        Public QR Code
        </p>
        
<div class="qr_info">        
        <div class="qr_image"><?php echo '<img src="http://chart.apis.google.com/chart?chs=300x300&amp;cht=qr&amp;chl='.$url.'" alt="QR code for this URL" width="90" height="90" />';?></div> 
        <div class="qr_download"><a href = "<?php echo "http://chart.apis.google.com/chart?chs=300x300&amp;cht=qr&amp;chl='.$url."; ?>">Download QR Code</a></div>
</div>        


       <div class="qr_use"><p>Use a QR Code to let people scan and import your profile contact information to their cell phones</p></div>
 
         
        </div>




        </div>

    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
