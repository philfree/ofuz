<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /****
   * Message display page
   * Display the content of $message
   * @param string $message contains the message to display
   *
   * @package OfuzOnline
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007
   * @version 4.0
   */

   $pageTitle = 'Ofuz :: Upgrade Needed';
   $Author = 'SQLFusion LLC';
   $background_color = 'white'; 
	
   include_once("config.php") ;
   $pageTitle = "Message" ;
 
   include_once('includes/ofuz_check_access.script.inc.php');
   include_once('includes/header.inc.php');

?>
<script type="text/javascript">
    //<![CDATA[
	
	<?php include_once('includes/ofuz_js.inc.php'); ?>
	
    //]]>
</script>	
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
	 <div class="contentfull">
	 <div class="mainheader pad20" width="80%">
	 <span class="headline14"><?php echo $_SESSION['do_User']->firstname; ?> its time to upgrade your account.</span>
	 </div>
	 <div class="spacerblock_20"></div>
<?

   $msg = new Message(); 
  $msg_key = $_GET['msg'];
  if (strlen($msg_key) < 8) {
	  $msg->getMessage($msg_key);
      $msg->displayMessage();
  }
  
  //echo $arr_msg[$msg_key];
?>
    </div>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
