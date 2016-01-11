<?php 
/**
Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html
**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com


  $pageTitle = 'Ofuz :: Register Now';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  setcookie("ofuz", "1", time()+25920000);
  include_once('config.php');
  include_once('includes/header.inc.php');

  if($_GET['id']){
      $msg = new Message();
      $msg->getMessage("share file and notes initialisation");
  }
?>
<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
    <div class="loginbg2">
<?php if($_GET['message']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div>
<?php  }?>
<?php 
      
      $do_user = new User();
      $do_user->sessionPersistent("do_User", "signout.php", 36000);
      if($_GET['id']){
            $do_user_rel = new UserRelations();//echo $do_user_rel->decrypt($_GET['id']);
	    $do_user_rel->getId($do_user_rel->decrypt($_GET['id'])) ;
            if( $do_user_rel->checkRegURL($do_user_rel->enc_email,$_GET['id'])){
                $_SESSION['do_User']->formRegisterInvitation('index.php',
                                                        'regthank',
                                                        'admin_registration_alert',
                                                        'support@sqlfusion.com',
                                                          $do_user_rel->enc_email,
                                                          $_GET['id'],
                                                          $_SERVER['PHP_SELF']
                                                        ); 
             }else{ echo _('Please check the URL');}

      }else{
       /* $_SESSION['do_User']->formRegister('import_contacts.php',
                                                     'regthank',
                                                     'admin_registration_alert',
                                                     'support@sqlfusion.com'
                                                     ); */

        $_SESSION['do_User']->formRegister('ww_s1.php',
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
