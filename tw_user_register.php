<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: '._('Login');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/header.inc.php');

    if (empty($_SESSION['TWITTER_REGISTER'])) {
        header('Location: user_login.php');
        exit;
    }
?>
<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
	<div class="messageshadow" style="width:440px; margin: 0 auto">
	    <div class="messages">
		<?php echo _('Thank you for connecting to Ofuz with Twitter.'),'<br />',
		           _('If you already have a username and password at Ofuz please sign in below to connect your Twitter account to Ofuz.'),'<br />',
		           _('Otherwise just provide us with your email address to create a new account.'); ?>
        <?php echo htmlentities(stripslashes($_GET['message'])); ?>
        </div>
      </div>
    <div class="loginbg2">
    <?php
        $loginForm_tw = new User();
        $loginForm_tw->sessionPersistent('do_User_login', 'logout.php', 36000);
	 ?>
        <div class="text">
		    <div class="section20">
            <?php echo _('If you are a new Ofuz user, please enter your email address below:'); ?><br /><br />
            <?php 
              $loginFormEmail_tw = new User();
              $loginFormEmail_tw->sessionPersistent('do_User_login_email', 'logout.php', 36000);
              $e_new_tw_reg = new Event('do_User_login_email->eventRegNewTwUser');
              $e_new_tw_reg->setLevel(20);
              $e_new_tw_reg->addParam('tw_user_id',$_SESSION['TWITTER_REGISTER']['tw_user_id']);
              $e_new_tw_reg->addParam('tw_screen_name',$_SESSION['TWITTER_REGISTER']['tw_screen_name']);
              $e_new_tw_reg->addParam('tw_token',$_SESSION['TWITTER_REGISTER']['tw_token']);
              $e_new_tw_reg->addParam('firstname',$_SESSION['TWITTER_REGISTER']['firstname']);
              $e_new_tw_reg->addParam('lastname',$_SESSION['TWITTER_REGISTER']['lastname']);
              $e_new_tw_reg->addParam('errPage','tw_user_register.php');
              
              echo $e_new_tw_reg->getFormHeader();
              echo $e_new_tw_reg->getFormEvent();
              echo _('Your email address: ').'<input type="Text" name="emailid" id="emailid" class="formfield" /><br />';
              echo '<div align="right"><input type="submit" value="'._('Continue').'" /></div>';
              echo '</form>';
              echo '</div>';
			  ?>
            </div>
            <div class="dottedline"></div>
            <div class="section20">	
            <?php
                echo _('Otherwise, sign in here to link your Twitter and Ofuz accounts:'),'<br /><br />';
                $_SESSION['do_User_login']->formTwLoginVerification('index.php',$_SERVER['PHP_SELF'],$_SESSION['TWITTER_REGISTER']['tw_user_id'],$_SESSION['TWITTER_REGISTER']['tw_screen_name'],$_SESSION['TWITTER_REGISTER']['tw_token']);
              ?>
           </div>
        </div>
    </div>
    <div class="loginbg3">
    </div>
</div>
</body>
</html>