<?php  
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
   /**
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzOnline
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.5
     */

    $pageTitle = 'Ofuz :: '._('Upgrade');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
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
		  $idcontact = $response[1][0]->idcontact;	 
	  }  else {
              $api_call->email_work = $_SESSION['do_User']->email;
              $api_call->add_contact();
              $response = $api_call->getResponse();
              $idcontact = $response->idcontact;  
	  } 
	  
	  $api_call_sub = new OfuzApiClient(OFUZ_API_KEY, "json");
	  $api_call_sub->setObject(true);
      $api_call_sub->idcontact = $idcontact;
	  if($api_call_sub->get_contact_subscription()) {
		  $current_plan = $api_call_sub->getResponse()->line_item[0]->item;	
		  $idinvoice = $api_call_sub->getResponse()->idinvoice; 		  
	  } else {
		  $current_plan = "free";
		  $idinvoice = 0;
		  //echo $api_call_sub->getResponse()->msg;
	  }
	  $e_reg->addParam('idcontact', $idcontact);
	  $e_reg->addParam('idinvoice', $idinvoice);
	  $e_reg->addParam('user_id', $_SESSION['do_User']->iduser);
	  $e_reg->addParam('current_plan', $current_plan);
	  $e_reg->setSecure(false);
	  //print_r($e_reg);

?>
   <!-- <div class="center_text text_blue"><h1>Upgrade to the Plan that's best for you</h1></div>-->
    <div class="text_20 text_height_28 center_text"><?php echo _('Upgrade to the Plan that\'s best for you'); ?></div>
    <div class="spacerblock_30"></div>
    <?php if($_GET['message']) { ?><div class="error_message"><?php echo htmlentities(stripslashes($_GET['message'])); ?></div><div class="spacerblock_30"></div><?php } ?>
    <div class="choose_plan_area">
        <div class="choose_plan_area_top"></div>
        <div class="spacerblock_20"></div>
        <div class="choose_plan_cols center_elem">
        <div class="choose_plan_col">
            <?php $e_reg->addParam("plan","99"); ?>
            <div class="choose_plan_line1">$99 / month</div>
            <div class="text_fuscia text_bold">BEST VALUE</div>
            <div class="spacerblock_5"></div>
            <div class="solidline1"></div>
            <div class="spacerblock_10"></div>
            <div class="choose_plan_line2"><span class="text_blue">50,000</span> contacts</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">25,000</span> emails / week</div>
            <div class="choose_plan_line3">(outbound)</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">unlimited</span> users</div>
            <div class="choose_plan_line3">(Co-Workers)</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">unlimited</span> projects</div>
            <div class="choose_plan_line3">&nbsp;</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">unlimited</span> invoices</div>
            <div class="spacerblock_20"></div>
            <?php
			    if ($current_plan == "Ofuz99") { 
					echo _('Your current plan');
				} else {
					$img = '<img src="/images/Upgrade.png" width="86" height="38" alt="" />';
					echo $e_reg->getLink($img);
				}
            ?>
        </div>
        <div class="choose_plan_col choose_plan_col_center">
            <?php echo $e_reg->addParam("plan","24"); ?>
            <div class="choose_plan_line1">$24 / month</div>
            <div class="text_fuscia text_bold">MOST POPULAR</div>
            <div class="spacerblock_5"></div>
            <div class="solidline1"></div>
            <div class="spacerblock_10"></div>
            <div class="choose_plan_line2"><span class="text_blue">1,000</span> contacts</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">5,000</span> emails / week</div>
            <div class="choose_plan_line3">(outbound)</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">unlimited</span> users</div>
            <div class="choose_plan_line3">(Co-Workers)</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">unlimited</span> projects</div>
            <div class="choose_plan_line3">&nbsp;</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">unlimited</span> invoices</div>
            <div class="spacerblock_20"></div>
             <?php
			    if ($current_plan == "Ofuz24") {
					echo _('Your current plan');
				} else {
					$img = '<img src="/images/Upgrade.png" width="86" height="38" alt="" />';
					echo $e_reg->getLink($img);
				}
            ?>
        </div>
		
        <div class="choose_plan_col">
            <?php $e_reg->addParam("plan","free"); ?>
            <div class="choose_plan_line1">FREE</div>
            <div class="text_fuscia text_bold">LEARN ABOUT OFUZ</div>
            <div class="spacerblock_5"></div>
            <div class="solidline1"></div>
            <div class="spacerblock_10"></div>
            <div class="choose_plan_line2"><span class="text_blue">200</span> contacts</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">50</span> emails / day</div>
            <div class="choose_plan_line3">(outbound)</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">unlimited</span> users</div>
            <div class="choose_plan_line3">(Co-Workers)</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">5</span> projects</div>
            <div class="choose_plan_line3">(as owner)</div>
            <div class="spacerblock_5"></div>
            <div class="choose_plan_line2"><span class="text_blue">5</span> invoices / month</div>
            <div class="spacerblock_20"></div>
			
              <?php
			     if ($current_plan == "free") { 
					echo _('Your current plan');
				} else {
					//$img = '<img src="/images/SignUp.png" width="86" height="38" alt="" />';
					//echo $e_reg->getLink($img);
			    }
            ?>
        </div>
		
        </div>
        <div class="spacerblock_20"></div>
        <div class="choose_plan_area_bot"></div>
    </div>
    </form>
    <div class="spacerblock_20"></div>
    <div class="center_text">
        All accounts include daily backups of your data, and access from anywhere with a web browser.<br />
        You can upgrade from one account to another at any time.
    </div>
    <div class="spacerblock_80"></div>	
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
