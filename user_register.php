<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

  $pageTitle = 'Ofuz :: Register Now';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  setcookie("ofuz", "1", time()+25920000);
  include_once('config.php');

  if(!isset($_GET['id']) && empty($_GET['id'])){
     /*
      * pp = public profile
      * When user comes from Public Profile page
      */
      if($_SESSION["from_page"] == 'public_profile') {
        //header("Location: http://www.ofuz.com/?frm=pp");
      } else {
        //header("Location: http://www.ofuz.com/");
      }
  }

  include_once('includes/header.inc.php');
  $invitation = false ;
  $show_instruction_message = false ;
  $error_invitation = '';

  $do_user = new User();
  $do_user->sessionPersistent("do_User", "signout.php", 36000);

  if($_GET['id']){
      $do_user_rel = new UserRelations();
      if($do_user_rel->ifRelationExist($do_user_rel->decrypt($_GET['id'])) === false ){
	  $error_invitation =  _('The URL is not valid or using this URL the registration is already done.');
      }
      if($error_invitation == ''){
	  $do_user_rel->getId($do_user_rel->decrypt($_GET['id'])) ;
	  if( $do_user_rel->checkRegURL($do_user_rel->enc_email,$_GET['id'])){
	      $invitation = true ;
	      $show_instruction_message = true ;
	  }else{
	      $error_invitation =  _('The URL is not valid or using this URL the registration is already done.');
	  }
      }
  }

  if($show_instruction_message === true ){
      $do_sender_info = new User();
      $do_sender_info->getId($do_user_rel->iduser);
      echo '<div style = "width:500px;margin: 0 auto; padding: 10px; text-align:left;">';
      $msg = new Message();
      $msg->setData(Array("sender_firstname"=>$do_sender_info->firstname,
				    "sender_lastname"=>$do_sender_info->lastname,
				    "sender_email"=>$do_sender_info->email
			     ));
      $msg->getMessage("registration invitation");
      echo $msg->displayMessage();
      echo '</div>';
  }
?>



<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
    <div class="loginbg2">
        <?php
	if ($_GET['message']) {
	    $message = new Message();
	    if($_GET['message'] == 'reg_duplicate_email'){
          $message->getMessage("reg_duplicate_email");
	    }else{
          $message->setContent($_GET['message']);
	    }
	    $message->displayMessage();
	  
	}elseif($error_invitation != ''){
	    $message = new Message();
	    $message->setContent($error_invitation);
	    $message->displayMessage();
	    exit;
	}
	?>
<?php 

      if($invitation === true ){
	     $_SESSION['do_User']->formRegisterInvitation('index.php',
                                                        'regthank',
                                                        'admin_registration_alert',
                                                        'support@sqlfusion.com',
                                                          $do_user_rel->enc_email,
                                                          $_GET['id'],
                                                          $_SERVER['PHP_SELF']
                                                        ); 
      }else{
	     $_SESSION['do_User']->formRegister('welcome_to_ofuz.php',
                                                     'regthank',
                                                     'admin_registration_alert',
                                                     'support@sqlfusion.com'
                                                     ); 
      }
      

      
?>
    </div>
</div>


<?php include_once('includes/ofuz_analytics.inc.php'); ?>
  </body>
</html>
