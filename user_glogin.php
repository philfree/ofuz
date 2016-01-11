<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  setcookie("ofuz", "1", time()+25920000);
  include_once('config.php');
  $pageTitle = 'Ofuz :: '._('Login');
  include_once('includes/header.inc.php');

  $next_page = "index.php";
  if(isset($_GET['reg']) && $_GET['reg'] == '1'){
    $next_page = "settings_myinfo.php";
  }
  $from = $_GET['entry'];
  if (ereg("Task", $from) 
  ||  ereg("Project", $from)
  ||  ereg("cp", $from)
  ||  ereg("Contact", $from)
  ||  ereg("Company", $from)) {
    $next_page = $from;
  }
  if (isset($_SESSION['entry']))  { 
    $next_page = $_SESSION['entry'];
    unset($_SESSION['entry']);
  }
 
?>
<script type="text/javascript">
//<![CDATA[
window.onload = function(){document.do_User__eventCheckIdentification["fields[username]"].focus();}
//]]>
</script>

<div class="loginbg1">
    <div class="loginheader"><a href="/index.php"><img src="/images/ofuz_logo2.jpg" width="170" height="90" alt="" /></a></div>
 <div class="messageshadow" style="width:440px; margin: 0 auto">
     <div class="messages">
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
            <?php echo _('If you are a new Ofuz user, please continue with your Google account information to create a new account:'); ?><br /><br />
        <?php
        $e_google_user_reg = new Event("User->eventRegGoogleUser");
        $e_google_user_reg->setLevel(20);
        $e_google_user_reg->addParam("google_openid_identity",$_SESSION["google"]["openid_identity"]);
        $e_google_user_reg->addParam("err_page",$_SERVER["PHP_SELF"]);
        echo $e_google_user_reg->getFormHeader();
        echo $e_google_user_reg->getFormEvent();
        ?>
        <table>
        <tr>
            <td class="tdformlabel"><?php echo _('First Name: '); ?></td>
            <td class="tabletdformfield"><input type="Text" name="firstname" id="firstname" class="formfield" value="<?php echo $_SESSION["google"]["firstname"];?>" /></td>
        </tr>
        <tr>
            <td class="tdformlabel"><?php echo _('Last Name: '); ?></td>
            <td class="tabletdformfield"><input type="Text" name="lastname" id="lastname" class="formfield" value="<?php echo $_SESSION["google"]["lastname"];?>" /></td>
        </tr>
        <tr>
            <td class="tdformlabel"><?php echo _('Email address: '); ?></td>
            <td class="tabletdformfield"><input type="Text" name="email" id="email" class="formfield" value="<?php echo $_SESSION["google"]["email"];?>" /></td>
        </tr>
        </table>
        <div align="right"><input type="submit" value="<?php echo _('Continue');?>" /></div>
        </form>
            </div>
            <div class="dottedline"></div>
            <div class="section20"> 
            <?php
                echo _('Otherwise, sign in here to link your Google and Ofuz accounts:'),'<br /><br />';
                $_SESSION['do_User']->formLogin($next_page, _("Incorrect username or password"), "text",$_SERVER['PHP_SELF']);
              ?>
           </div>
        </div>
    </div>
    <div class="loginbg3">
    </div>
</div>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>