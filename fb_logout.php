<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Welcome';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php //$thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php //$do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Logging out ....');?><br /><br /><br /></span>
        </div>
    </div>
    <div class="contentfull">
        
<?php  
     $expire = time() - 3600;
  @setcookie('cookyogaglouserid', '', $expire, '/');
  @setcookie('cookyogaglouserfirstname', '', $expire, '/');
  @setcookie('cookyogaglouserlastname', '', $expire, '/');
  @setcookie('cookyogaglouseremail', '', $expire, '/');

    // Unset all of the session variables.
    $_SESSION = array();

  // If it's desired to kill the session, also delete the session cookie.
  // Note: This will destroy the session, and not just the session data!
  if (isset($_COOKIE[session_name()])) {
      @setcookie(session_name(), '', time()-42000, '/');
  }

  // Finally, destroy the session.
  session_destroy();

?> 
<script type="text/javascript">
//<![CDATA[
window.location = "/user_login.php";
//]]>
</script>

<div class="spacerblock_80"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>