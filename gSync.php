<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Sync';
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
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11">Sync with Gmail</span>
        </div>
    </div>
<!--
    <div class="messages">
        <span class="headline11">Sync</span>
    </div>
-->
    <div class="contentfull">
        <div class="sync_link">
            <div class="headline10"></div>
            <span class="sync_item"><a href="#" onclick="fnEnterEmail('gmail','2'); return false;"><?php echo _("Import Contacts"); ?></a>
			</span>
            <div id="instructiongmail2" style="display: none;"><div class="messageshadow"><div class="message"><?php echo _("It imports only the active contacts from Gmail.So it doesn't import contact with no name."); ?></div></div></div>
            <div id="gmail2" style="display: none;"></div>
            <br />

            <span class="sync_item"><a href="#" onclick="fnEnterEmail('gmail','1'); return false;"><?php echo _("Export Contacts"); ?></a></span>
            <div id="instructiongmail1" style="display: none;"><div class="messageshadow"><div class="message"><?php echo _("Contacts will be exported from Ofuz which have atleast one emailid."); ?></div></div></div>
            <div id="gmail1" style="display: none;"></div>

        </div>
        <div style="color:red"><?php if($_GET['msg']){echo htmlentities($_GET['msg']);} ?></div>
    </div>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>