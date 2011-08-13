<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzOnline
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.3
     */

    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');

    $pageTitle = 'Ofuz :: '._('Upgrade Thank you');
    include_once('includes/header.inc.php');

    $do_sync = new Sync($GLOBALS['conx']);
     if (isset($_GET['ref']) && $_GET['ref'] == 'reg') {
      $ref = $_GET['ref'];
      $_SESSION["page_from"] = $ref;
  }
?>
 <link rel="stylesheet" type="text/css" href="/includes/ofuzcom_theme.css" /> 
<script type="text/javascript">
    //<![CDATA[
    function fnEnterEmail(ref, act) {
        $.ajax({
            type: "GET",
<?php
$e_emailForm = new Event("Sync->eventAjaxEnterEmailForm");
$e_emailForm->setEventControler("ajax_evctl.php");
$e_emailForm->setSecure(false);
?>
            url: "<?php echo $e_emailForm->getUrl(); ?>",
            data: "referrer="+ref+"&act="+act,
            success: function(html){
                $("#instruction"+ref+act).slideToggle("slow");

                $("#"+ref+act)[0].innerHTML = html;
                $("#"+ref+act).toggle(0);
            }
        });
    }
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Sync'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="contentfull">
	
<?php if($_GET['message']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div><?php } ?>
<?php 
      $e_reg = new Event("UserPlan->eventUpgrade");
      $e_reg->setLevel(20);
	  include_once("class/OfuzApiClientBase.class.php");
	  include_once("class/OfuzApiClient.class.php");
	  $api_call = new OfuzApiClient(OFUZ_API_KEY, "json");
	  $api_call->setObject(true);
	  $api_call->firstname = $_SESSION['do_User']->firstname;
	  $api_call->lastname = $_SESSION['do_User']->lastname;
	  $api_call->email = $_SESSION['do_User']->email;
	  //$idcontact = json_decode($api_call->get_contact_id());

	  if ($api_call->get_contact_id()) {		 
		  $response = $api_call->getResponse();
		  //print_r($response);
		  $idcontact = $response[1][0]->idcontact;	 
	  }  else {
	      $api_call->getResponse()->msg;
	  }
	  //$api_call->clearRequest();
	  //$api_call->idcontact = $idcontact; 
	  
	  $api_call_sub = new OfuzApiClient(OFUZ_API_KEY, "json");
	  $api_call_sub->setObject(true);
	  $api_call_sub->idcontact = $response[1][0]->idcontact;
	  if($api_call_sub->get_contact_subscription()) {
		  $current_plan = $api_call_sub->getResponse()->line_item[0]->item;	
		  $idinvoice = $api_call_sub->getResponse()->idinvoice; 		  
	  } else {
		  $current_plan = "free";
		  $idinvoice = 0;
		  //echo $api_call_sub->getResponse()->msg;
	  }
	  if ($_SESSION['autologin_paid']) {
		  if ($current_plan == "Ofuz99") { 
				$_SESSION['do_User']->plan = '99';
				$_SESSION['do_User']->update();
				
		  } elseif ($current_plan == "Ofuz24") { 
				$_SESSION['do_User']->plan = '24';
				$_SESSION['do_User']->update();		  
		  } else { 
				$_SESSION['do_User']->plan = 'free';
				$_SESSION['do_User']->update();						  
		  }
	  }
?>
   <!-- <div class="center_text text_blue"><h1>Upgrade to the Plan that's best for you</h1></div>-->
    <div class="text_20 text_height_28 center_text">Thank you, your new plan is taking effect right now.</div>
    <div class="spacerblock_30"></div>
	
    <?php if($_GET['message']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div><div class="spacerblock_30"></div><?php } ?>

   
    <div class="spacerblock_80"></div>	
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
