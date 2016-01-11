<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Login';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/i_header.inc.php');
?>
<div class="loginbg1">
    <div class="loginbg_mobile">
    <?php
        $loginForm = new User();
        $loginForm->sessionPersistent("do_User", "", 36000);
        if ($_GET['message']) {
    ?>
        <div class="error_message">
        <?php echo htmlentities(stripslashes($_GET['message'])); ?>
        </div>
    <?php } ?>
        <div class="text">
            <?php $_SESSION['do_User']->formLogin("i_contacts.php", "Incorrect username or password", "text",$_SERVER['PHP_SELF']); ?>
            If you have not registered yet, please do so <a href="user_register.php">here</a> .<br />
            If you forgot your password, you can retrieve it <a href="user_lost_password.php">here</a>
        </div>
    </div>
    <div>
        <a href="user_login_openid.php">You can also sign in using OpenID</a>
    </div>
</div>
</body>
</html>
