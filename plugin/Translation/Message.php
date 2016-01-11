<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

?>


 <table class="mainheader pad20" width="100%">
  <tr>
      <td><span class="headline14">
      <?php
          echo _('Language Translator for Message'); echo " ".$do_gt->key_name;
      ?></span>
      </td>
  </tr>
 </table>
<?php if($_SESSION["dest_lang"] != "") { 
  $do_gt = new GoogleTranslatorMessage();
  $do_gt->getTemplatesToBeTranslated();
?>
 <table class="pad20" width="35%">
   <tr>
     <td width="25%" align="left">
       <?php
       if($do_gt->isPrev($do_gt->idmessage)) {
  $e_prev = new Event("GoogleTranslatorMessage->eventSetPrevTemplate");
  $e_prev->addParam("current_idtemplate", $do_gt->idmessage);
  $e_prev->addParam("goto", $_SERVER["REDIRECT_URL"] );
  echo $e_prev->getLink("Previous");
       }
       ?>
     </td>
     <td width="25%" align="right">
       <?php
       if($do_gt->isNext($do_gt->idmessage)) {
  echo "&nbsp;&nbsp&nbsp&nbsp";

  $e_next = new Event("GoogleTranslatorMessage->eventSetNextTemplate");
  $e_next->addParam("current_idtemplate", $do_gt->idmessage);
  $e_next->addParam("goto", $_SERVER["REDIRECT_URL"] );
  echo $e_next->getLink("Next");
       }
       ?>
     </td>
   </tr>
   <tr>
     <td colspan="2">
       <?php
       while($do_gt->next()) {
  $e_gt = new Event("GoogleTranslatorMessage->eventTranslateLanguage");
  $e_gt->addParam("goto",  $_SERVER["REDIRECT_URL"] );
//   $e_gt->addParam("src_lng",$src_lng);
//   $e_gt->addParam("dest_lng",$dest_lng);
  echo $e_gt->getFormHeader();
  echo $e_gt->getFormEvent();
       ?>

  <div class="spacerblock_20"></div>
  <b>Translate content</b><br />
  <textarea name="et_content_src" id="et_content_src" cols="80" rows="8" wrap=hard><?php echo $do_gt->content; ?></textarea> <br /><br />

  <?php echo $e_gt->getFormFooter(_("Suggest a translation")); ?>

  <div class="spacerblock_20"></div>
       <?php
  $e_gt_translated = new Event("GoogleTranslatorMessage->eventAddTranslateLanguage");
  $e_gt_translated->addParam("goto",  $_SERVER["REDIRECT_URL"] );
 // $e_gt_translated->addParam("dest_lng",$dest_lng);
  $e_gt_translated->addParam("key_name",$do_gt->key_name);
  $e_gt_translated->addParam("context",$do_gt->context);
  $e_gt_translated->addParam("can_close",$do_gt->can_close);
  $e_gt_translated->addParam("close_duration",$do_gt->close_duration);
  $e_gt_translated->addParam("plan",$do_gt->plan);

  echo $e_gt_translated->getFormHeader();
  echo $e_gt_translated->getFormEvent();
       ?>
  <b>Translated content</b><br />
  <textarea name="et_content_dst" id="et_content_dst" cols="80" rows="8" wrap=hard><?php if(isset($_SESSION["et_content_src"])) {echo $_SESSION["et_content_src"];}?></textarea> <br />
  <div class="spacerblock_20"></div>
  
       <?php echo $e_gt_translated->getFormFooter("Save"); ?>
       <?php } ?>
     </td>
   </tr>
 </table>
<?php } else { ?>
        <div class="spacerblock_20"></div>
        <div>Please select the destination language to see the messages.</div>
<?php } ?>
        <div class="spacerblock_20"></div>

    <div class="dottedline"></div>
    </td>
 </tr>
 </table>
