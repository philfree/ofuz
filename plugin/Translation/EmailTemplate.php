<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

?>

<table class="mainheader pad20" width="100%">
        <tr>
           <td><span class="headline14">
      <?php
          echo _('Language Translator for Emailtemplate');
      ?></span>
      </td>
  </tr>
	</table>
<?php if($_SESSION["dest_lang"] != "") { 
  $do_gt = new GoogleTranslatorEmailtemplate();
  $do_gt->getTemplatesToBeTranslated();
?>
	<table class="pad20" width="35%">
	  <tr>
	    <td width="25%" align="left">
	      <?php
	      if($do_gt->isPrev($do_gt->idemailtemplate)) {
		$e_prev = new Event("GoogleTranslatorEmailtemplate->eventSetPrevTemplate");
		$e_prev->addParam("current_idtemplate", $do_gt->idemailtemplate);
		$e_prev->addParam("goto", $_SERVER["REDIRECT_URL"] );
		echo $e_prev->getLink(_("Previous"));
	      }
	      ?>
	    </td>
	    <td width="25%" align="right">
	      <?php
	      if($do_gt->isNext($do_gt->idemailtemplate)) {
		echo "&nbsp;&nbsp&nbsp&nbsp";

		$e_next = new Event("GoogleTranslatorEmailtemplate->eventSetNextTemplate");
		$e_next->addParam("current_idtemplate", $do_gt->idemailtemplate);
		$e_next->addParam("goto", $_SERVER["REDIRECT_URL"] );
		echo $e_next->getLink(_("Next"));
	      }
	      ?>
	    </td>
	  </tr>
	  <tr>
	    <td colspan="2">
	      <?php
	      while($do_gt->next()) {
		$e_gt = new Event("GoogleTranslatorEmailtemplate->eventTranslateLanguage");
		$e_gt->addParam("goto",  $_SERVER["REDIRECT_URL"] );
// 		$e_gt->addParam("src_lng",$src_lng);
// 		$e_gt->addParam("dest_lng",$dest_lng);
		echo $e_gt->getFormHeader();
		echo $e_gt->getFormEvent();
	      ?>

		<div class="spacerblock_20"></div>
		<b>Subject</b><br />
		<input type="text" name="et_sub_src" id="et_sub_src" value="<?php echo $do_gt->subject; ?>" />
		<div class="spacerblock_20"></div>
		<b>bodytext</b><br />
		<textarea name="et_body_text_src" id="et_body_text_src" cols="50" rows="5" wrap=hard><?php echo $do_gt->bodytext; ?></textarea> <br />
		<div class="spacerblock_20"></div>
		<b>bodyhtml</b><br />
		<textarea name="et_body_html_src" id="et_body_html_src" cols="50" rows="5" wrap=hard><?php echo $do_gt->bodyhtml; ?></textarea> <br /><br />

		<?php echo $e_gt->getFormFooter(_("Suggest a translation")); ?>
		<div class="spacerblock_20"></div>

		<div class="spacerblock_20"></div>
	      <?php
		$e_gt_translated = new Event("GoogleTranslatorEmailtemplate->eventAddTranslateLanguage");
		$e_gt_translated->addParam("goto",  $_SERVER["REDIRECT_URL"] );
		//$e_gt_translated->addParam("dest_lng",$dest_lng);
		$e_gt_translated->addParam("name",$do_gt->name);
		$e_gt_translated->addParam("sendername",$do_gt->sendername);
		$e_gt_translated->addParam("senderemail",$do_gt->senderemail);

		echo $e_gt_translated->getFormHeader();
		echo $e_gt_translated->getFormEvent();
	      ?>
		<b>Subject</b><br />
		<input type="text" name="et_sub_dst" id="et_sub_dst" value="<?php if(isset($_SESSION["et_sub_src"])) {echo $_SESSION["et_sub_src"];}?>" />
		<div class="spacerblock_20"></div>
		<b>bodytext</b><br />
		<textarea name="et_body_text_dst" id="et_body_text_dst" cols="50" rows="5" wrap=hard><?php if(isset($_SESSION["et_body_text_src"])) {echo $_SESSION["et_body_text_src"];}?></textarea> <br />
		<div class="spacerblock_20"></div>
		<b>bodyhtml</b><br />
		<textarea name="et_body_html_dst" id="et_body_html_dst" cols="50" rows="5" wrap=hard><?php if(isset($_SESSION["et_body_html_src"])) {echo $_SESSION["et_body_html_src"];}?></textarea> <br />
	      <?php echo $e_gt_translated->getFormFooter("Save"); ?>
	      <?php } ?>
	    </td>
	  </tr>
	</table>
<?php } else { ?>
        <div class="spacerblock_20"></div>
        <div>Please select the destination language to see the Email templates.</div>
<?php } ?>
        <div class="spacerblock_20"></div>
