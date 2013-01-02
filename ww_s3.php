<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Setup your Team';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');
     
?>
<style type="text/css">
#simplemodal-overlay {background-color:#000;}
#simplemodal-container {background-color:#333; height:auto; border:8px solid #444; padding:12px;}
</style>

<script type="text/javascript">

$(document).ready(function () {
  $('#invite').click(function (){
   
    var email1 = $.trim($('#email1').val());
    var email2 = $.trim($('#email2').val());
    var email3 = $.trim($('#email3').val());
    var email4 = $.trim($('#email4').val());
    if(email1 == "" && email2 == "" && email3 == "" && email4 == "") {
      $('#msg').html(" You need to enter at least one email address.");
      return false;
    } else {
       $('#UserRelations__eventInviteMultipleCWs').submit();
    }
  });
});
</script>

<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Setup your Team');?></span>
        </div>
    </div>
    <div class="contentfull">        
      <div class="messageshadow">
	<div class="messages" style="font-size:1.8em;">Ofuz Getting started wizard</div>
      </div>
<?php
$e_cw_invite = new Event("UserRelations->eventInviteMultipleCWs");
$e_cw_invite->addParam("goto", "ww_s4.php");
echo $e_cw_invite->getFormHeader();
echo $e_cw_invite->getFormEvent();
?>
      <div align="center" style="font-size:1.4em;">
      <p id="pYourFirstProject">Enter the email address of the Co-Workers you want to work with.</p>
      <p id="msg"></p>
	<div id="invite_cw">
	  <div class="spacerblock_20"></div>
	  <div>Email addresses:</div>
	  <div><input class="txtboxStyle1" type="text" id="email1" name="email[]" value="" /></div>
	  <div class="spacerblock_10"></div>
	  <div><input class="txtboxStyle1" type="text" id="email2" name="email[]" value="" /></div>
	  <div class="spacerblock_10"></div>
	  <div><input class="txtboxStyle1" type="text" id="email3" name="email[]" value="" /></div>
	  <div class="spacerblock_10"></div>
	  <div><input class="txtboxStyle1" type="text" id="email4" name="email[]" value="" /></div>
	</div>
      <div class="spacerblock_40"></div>
      <div>
	<a id="invite" href="javascript:;"><img src="/images/invite.jpg" border="0" /></a> <br />
	<a href="index.php" title="">Skip >></a>
      </div>
</form>
      <div class="spacerblock_80"></div>

      <div class="layout_footer"></div>

     </div>
</td><td class="layout_rmargin"></td></tr></table>
</body>
</html>