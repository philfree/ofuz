<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: User Profile';
  $Author = 'SQLFusion LLC';
  include_once('config.php');

  if (is_object($_SESSION['do_User'])) {
    try {
      if (!$_SESSION['do_User']->iduser) {
        unset($_SESSION['do_User']);
        header("Location: ".$GLOBALS['cfg_ofuz_site_http_base'].$_SERVER['REQUEST_URI']);
      }
    } catch (Exception $e) { 
      unset($_SESSION['do_User']);
      header("Location: ".$GLOBALS['cfg_ofuz_site_http_base'].$_SERVER['REQUEST_URI']);
    }
  }

  $_SESSION["from_page"] = "public_profile";

  if ($_GET['u']) {
    $do_contact = new Contact();
    $idcontact = $do_contact->getContactByUsername($_GET['u']);
    if ($idcontact === false) { echo "No public profiles have been found."; exit;  }
    $idcontact = $do_contact->idcontact;
    $do_contact->sessionPersistent("do_contact", "index.php", OFUZ_TTL);
  } elseif (isset($_SESSION['do_contact'])) {
    $idcontact = $_SESSION['do_contact'];
  } else {
    exit;
  }
  $_SESSION['public_profile_name'] = $_GET['u'];

    //if(isset($_POST['add_cont'])){
     if($_POST['hd_add_cont']){
	    $_SESSION['hcard_idcontact'] = $idcontact;
	    if (!is_object($_SESSION['do_User'])) {
	      $disp = new Display("user_login.php");
	      $disp->addParam("message", "Your session has expired, please sign-in again");
	      //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		      $_SESSION['entry'] = $_SERVER['REQUEST_URI'];
	      header("Location: /".$disp->getUrl());
	      exit;
	  } 
	  if (is_object($_SESSION['do_User'])) {
	  try {
	    if (!$_SESSION['do_User']->iduser) {
	      $disp = new Display("user_login.php");
	      $disp->addParam("message", "Error with your user record, please sign-in again");
	      //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		      $_SESSION['entry'] = $_SERVER['REQUEST_URI'];
	      header("Location: /".$disp->getUrl());
	      
	    }
	  } catch (Exception $e) { 
	      $disp = new Display("user_login.php");
	      $disp->addParam("message", "Error with your user record, please sign-in again");
	      //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		      $_SESSION['entry'] = $_SERVER['REQUEST_URI'];
	      header("Location: /".$disp->getUrl());
	  }
	}      
		      
 }

if($_SESSION['hcard_idcontact'] != '' ){
      $do_add_cont = new Contact();
      $do_add_cont->addNew();
      $do_add_cont->firstname = $_SESSION['do_contact']->firstname;
      $do_add_cont->lastname = $_SESSION['do_contact']->lastname;
      $do_add_cont->iduser = $_SESSION['do_User']->iduser;
      $do_add_cont->add();
      $idcontact_inserted = $do_add_cont->getPrimaryKeyValue();

      $ContactPhone = $_SESSION['do_contact']->getChildContactPhone();
      $ContactPhoneAdd = new ContactPhone();
      if($ContactPhone->getNumRows()){
          while($ContactPhone->next()){
              $ContactPhoneAdd->addNew();
              $ContactPhoneAdd->idcontact = $idcontact_inserted;
              $ContactPhoneAdd->phone_type = $ContactPhone->phone_type;
              $ContactPhoneAdd->phone_number = $ContactPhone->phone_number;
              $ContactPhoneAdd->add();
          }
      }

      $ContactEmail = $_SESSION['do_contact']->getChildContactEmail();
      $ContactEmailAdd = new ContactEmail();
      if($ContactEmail->getNumRows()){
        while($ContactEmail->next()){
            $ContactEmailAdd->addNew();
            $ContactEmailAdd->idcontact = $idcontact_inserted;
            $ContactEmailAdd->email_type = $ContactEmail->email_type;
            $ContactEmailAdd->email_address = $ContactEmail->email_address;
            $ContactEmailAdd->add();
        }
      }


      $ContactInstantMessage = $_SESSION['do_contact']->getChildContactInstantMessage();
      $ContactInstantMessageAdd = new ContactInstantMessage();
      if($ContactInstantMessage->getNumRows()){
        while($ContactInstantMessage->next()){
            $ContactInstantMessageAdd->addNew();
            $ContactInstantMessageAdd->idcontact = $idcontact_inserted;
            $ContactInstantMessageAdd->im_type = $ContactInstantMessage->im_type;
            $ContactInstantMessageAdd->im_username = $ContactInstantMessage->im_username;
            $ContactInstantMessageAdd->add();
        }
      }

      $ContactWebsite = $_SESSION['do_contact']->getChildContactWebsite();
      $ContactWebsiteAdd = new ContactWebsite();
      if($ContactWebsite->getNumRows()){
        while($ContactWebsite->next()){
            $ContactWebsiteAdd->addNew();
            $ContactWebsiteAdd->idcontact = $idcontact_inserted;
            $ContactWebsiteAdd->website = $ContactWebsite->website;
            $ContactWebsiteAdd->website_type = $ContactWebsite->website_type;
            $ContactWebsiteAdd->add();
        }
      }

      $ContactAddress = $_SESSION['do_contact']->getChildContactAddress();
      $ContactAddressAdd = new ContactAddress();
      if($ContactAddress->getNumRows()){
        while($ContactAddress->next()){
            $ContactAddressAdd->addNew();
            $ContactAddressAdd->idcontact = $idcontact_inserted;
            $ContactAddressAdd->address_type = $ContactAddress->address_type;
            $ContactAddressAdd->address = $ContactAddress->address;
            $ContactAddressAdd->add();
        }
      }

      $do_cont_ref = new Contact();
      $do_cont_ref->getId($idcontact_inserted);
      $do_contact_view = new ContactView();
      $do_contact_view->setUser($_SESSION['do_User']->iduser);
      $do_contact_view->addFromContact($do_cont_ref);
      $do_contact_view->updateFromContact($do_cont_ref);
      $do_cont_ref->free();
      $_SESSION['hcard_idcontact'] = '';
      header("Location: /Contact/".$idcontact_inserted);
      exit;
}
	



    $Keywords = 'Ofuz Profile for '.$_SESSION['do_contact']->firstname.' '.$_SESSION['do_contact']->lastname;
    //$Description = 'Description for search engine';
    $background_color = 'white';
    include_once('includes/header_profile.inc.php');
?>
<script type="text/javascript" src="/jquery/jquery-1.4.2.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
   // if screen width < 600 then Mobile/Tablet version else Web version
    smallScreen = (screen.width < 900) ? true : false;

    $.ajax({
      type: "POST",
      url: "/ajx_public_profile_loader.php",
      data: {smallscreen:smallScreen},
      dataType: "html",
      success: function(data) {   
	$('#publicProfile').html(data);
      }
    });

  });

</script>

<div class="layout_center">
    <div class="layout_top">
  <?php
      $id_user=$do_contact->getIdUser($idcontact);      
      $do_userprofile=new UserProfile();
      $profile_information=$do_userprofile->getProfileInformation($id_user);
        $ContactWebsite = $_SESSION['do_contact']->getChildContactWebsite();
        if($ContactWebsite->getNumRows()){                       
          $ContactWebsite->next();
          $website_url  = $ContactWebsite->website;        
        }else{
          $website_url  = '#';
        }

      if(empty($profile_information)){ ?>
        <a href="<?php echo $website_url; ?>"><img src="/images/ofuz_logo_profile.png" width="157" height="100" alt="" /></a>
      <?php 
        }else{       
      ?>
        <a href="<?php echo $website_url; ?>"><img src="/dbimage/<?php echo $profile_information['logo']; ?>" width="157" height="100" alt="" /></a>
      
      <?php
        }
      ?>
        
    </div>
</div>

<div id="publicProfile"></div>

<div class="layout_center">
    <div class="layout_bottom"></div>
</div>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>