<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Import your address book';
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
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Import your address book');?></span>
        </div>
    </div>
    <div class="contentfull">        
      <div class="messageshadow">
	<div class="messages" style="font-size:1.8em;">Ofuz Getting started wizard</div>
      </div>
      <div align="center" style="font-size:1.4em;">
      <p>Select the service where you have most of your working contacts</p>


	<div id="import_address_book">
	  <div style="vertical-align:middle;"><a id="fb_import" href="ww_s1_fbconnect.php"><img src="/images/Facebook_Logo.jpg" alt="" /></a></div>
<!--
	  <table width="60%">

	    <tr>
	      <td width="20%" height="100px" style="vertical-align: middle;"><a id="g_import" href="ww_s1_gsync.php"><img src="/images/Google_Logo.gif" alt="" /></a></td>
	      <td width="20%" height="100px" style="vertical-align: middle;"><a id="fb_import" href="ww_s1_fbconnect.php"><img src="/images/Facebook_Logo.jpg" alt="" /></a></td>
	      <td width="20%" height="100px" style="vertical-align: middle;"><a id="tw_import" href="ww_s1_tw.php"><img src="/images/twitter_small.png" alt="" /></a></td>
	    </tr>
	    <tr>
	      <td width="20%" style="vertical-align: middle;"><a id="yahoo_import" href="ww_s1_yahoo.php"><img src="/images/yahoo.gif" alt="Import Contacts from Yahoo!" /></a></td>
	      <td width="20%" style="vertical-align: middle;"><a id="hotmail_import" href="ww_s1_hotmail.php"><img src="/images/hotmail.jpeg" alt="Import Contacts from Yahoo!" /></a></td>
	      <td width="20%" style="vertical-align: middle;">&nbsp;</td>
	    </tr>

	  </table>
-->
	</div>
      <div class="spacerblock_40"></div>
      <div>
	<a href="ww_s2.php"><input type="image" src="/images/next.jpg" border="0" /></a> <br />
	<a href="index.php" title="">Skip >></a>
      </div>
      <div class="spacerblock_80"></div>

      <div class="layout_footer"></div>

     </div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>

<script type='text/javascript' src='js/jquery.simplemodal.js'></script>
<script type="text/javascript">
  $(document).ready(function() {
	var ofuzWelcomeWizard = {
		message: null,
		init: function () {
			$('#import_address_book a#g_import, #import_address_book a#fb_import, #import_address_book a#tw_import, #import_address_book a#yahoo_import, #import_address_book a#hotmail_import').click(function (e) {
				e.preventDefault();
				var src = $(this).attr("href");

				// create a modal dialog with the iframe
				$.modal('<iframe src="' + src + '" height="400" width="800" style="border:0">', {
					closeHTML: "<div style='text-align:right;'><a href='#' title='Close' class='modal-close'>X</a></div>",
					containerCss:{
						backgroundColor:"#fff",
						borderColor:"#fff",
						height:450,
						padding:0,
						width:800
					},
					overlayId: 'simplemodal-overlay',
					containerId: 'simplemodal-container',
					onOpen: ofuzWelcomeWizard.open,
					onClose: ofuzWelcomeWizard.close
				});
			});
		},
		open: function (dialog) {

		    dialog.overlay.fadeIn('slow', function () {
			    dialog.data.hide();
			    dialog.container.fadeIn('slow', function () {
				    dialog.data.slideDown('slow');
			    });
		    });
		},
		close: function (dialog) {
		  dialog.data.fadeOut('slow', function () {
			  dialog.container.hide('slow', function () {
				  dialog.overlay.slideUp('slow', function () {
					  $.modal.close();
				  });
			  });
		  });
		},
		error: function (xhr) {
			alert(xhr.statusText);
		}
	};

	ofuzWelcomeWizard.init();

  });
</script>
</body>
</html>