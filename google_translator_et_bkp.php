<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Language Translator';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');

  $src_lng = "en_US";
  $dest_lng = "fr_FR";

  $do_gt = new GoogleTranslatorEmailtemplate();
  $do_gt->src_lng = $src_lng;
  $do_gt->dest_lng = $dest_lng;
  $do_gt->getTemplatesToBeTranslated();
  
?>

 
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Dashboard';  
 $_SESSION['dashboard_link'] = "/daily_notes.php";
include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns">
		<tr><td class="layout_lcolumn">
<?php
$GLOBALS['page_name'] = 'google_translator_et';
include_once('plugin_block.php');
?>
    </td>
	<td class="layout_rcolumn">
	<?php
	      /*$msg = new Message(); 
	      $msg->getMessageFromContext("daily notes");
	      echo $msg->displayMessage();*/
	?>
	<table class="mainheader pad20" width="100%">
  <tr>
      <td><span class="headline14">
      <?php
          echo _('Language Translator for Emailtemplate');
      ?></span>
      </td>
      <td align="right">
        <?php include_once("includes/google_translator_menu.inc.php"); ?>
      </td>
  </tr>
	</table>

	<table class="pad20" width="35%">
	  <tr>
	    <td width="25%" align="left">
	      <?php
	      if($do_gt->isPrev($do_gt->idemailtemplate)) {
		$e_prev = new Event("GoogleTranslatorEmailtemplate->eventSetPrevTemplate");
		$e_prev->addParam("current_idtemplate", $do_gt->idemailtemplate);
		$e_prev->addParam("goto",$_SERVER['PHP_SELF']);
		echo $e_prev->getLink("Previous");
	      }
	      ?>
	    </td>
	    <td width="25%" align="right">
	      <?php
	      if($do_gt->isNext($do_gt->idemailtemplate)) {
		echo "&nbsp;&nbsp&nbsp&nbsp";

		$e_next = new Event("GoogleTranslatorEmailtemplate->eventSetNextTemplate");
		$e_next->addParam("current_idtemplate", $do_gt->idemailtemplate);
		$e_next->addParam("goto",$_SERVER['PHP_SELF']);
		echo $e_next->getLink("Next");
	      }
	      ?>
	    </td>
	  </tr>
	  <tr>
	    <td colspan="2">
	      <?php
	      while($do_gt->next()) {
		$e_gt = new Event("GoogleTranslatorEmailtemplate->eventTranslateLanguage");
		$e_gt->addParam("goto", $_SERVER['PHP_SELF']);
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
  <select name="src_lang">
    <option value="en_US">English</option>
  </select>
  <b> >> </b>
  <select name="dest_lang">
    <option value="sq_AL">Albanian (Albania)</option>
    <option value="ar_DZ">Arabic (Algeria)</option>
    <option value="ar_BH">Arabic (Bahrain)</option>
    <option value="ar_EG">Arabic (Egypt)</option>
    <option value="ar_IQ">Arabic (Iraq)</option>
    <option value="ar_JO">Arabic (Jordan)</option>
    <option value="ar_KW">Arabic (Kuwait)</option>
    <option value="ar_LB">Arabic (Lebanon)</option>
    <option value="ar_LY">Arabic (Libya)</option>
    <option value="ar_MA">Arabic (Morocco)</option>
    <option value="ar_OM">Arabic (Oman)</option>
    <option value="ar_QA">Arabic (Qatar)</option>
    <option value="ar_SA">Arabic (Saudi Arabia)</option>
    <option value="ar_SD">Arabic (Sudan)</option>
    <option value="ar_SY">Arabic (Syria)</option>
    <option value="ar_TN">Arabic (Tunisia)</option>
    <option value="ar_AE">Arabic (United Arab Emirates)</option>
    <option value="ar_YE">Arabic (Yemen)</option>
    <option value="be_BY">Belarusian (Belarus)</option>
    <option value="bg_BG">Bulgarian (Bulgaria)</option>
    <option value="ca_ES">Catalan (Spain)</option>
    <option value="zh_CN">Chinese (China)</option>
    <option value="zh_HK">Chinese (Hong Kong)</option>
    <option value="zh_SG">Chinese (Singapore)</option>
    <option value="zh_TW">Chinese (Taiwan)</option>
    <option value="hr_HR">Croatian (Croatia)</option>
    <option value="cs_CZ">Czech (Czech Republic)</option>
    <option value="da_DK">Danish (Denmark)</option>
    <option value="nl_BE">Dutch (Belgium)</option>
    <option value="nl_NL">Dutch (Netherlands)</option>
    <option value="en_AU">English (Australia)</option>
    <option value="en_CA">English (Canada)</option>
    <option value="en_IN">English (India)</option>
    <option value="en_IE">English (Ireland)</option>
    <option value="en_MT">English (Malta)</option>
    <option value="en_NZ">English (New Zealand)</option>
    <option value="en_PH">English (Philippines)</option>
    <option value="en_SG">English (Singapore)</option>
    <option value="en_ZA">English (South Africa)</option>
    <option value="en_GB">English (United Kingdom)</option>
    <option value="en_US">English (United States)</option>
    <option value="et_EE">Estonian (Estonia)</option>
    <option value="fi_FI">Finnish (Finland)</option>
    <option value="fr_BE">French (Belgium)</option>
    <option value="fr_CA">French (Canada)</option>
    <option value="fr_FR">French (France)</option>
    <option value="fr_LU">French (Luxembourg)</option>
    <option value="fr_CH">French (Switzerland)</option>
    <option value="de_AT">German (Austria)</option>
    <option value="de_DE" selected>German (Germany)</option>
    <option value="de_LU">German (Luxembourg)</option>
    <option value="de_CH">German (Switzerland)</option>
    <option value="el_CY">Greek (Cyprus)</option>
    <option value="el_GR">Greek (Greece)</option>
    <option value="iw_IL">Hebrew (Israel)</option>
    <option value="hi_IN">Hindi (India)</option>
    <option value="hu_HU">Hungarian (Hungary)</option>
    <option value="is_IS">Icelandic (Iceland)</option>
    <option value="in_ID">Indonesian (Indonesia)</option>
    <option value="ga_IE">Irish (Ireland)</option>
    <option value="it_IT">Italian (Italy)</option>
    <option value="it_CH">Italian (Switzerland)</option>
    <option value="ja_JP">Japanese (Japan)</option>
    <option value="ja_JP_JP">Japanese (Japan,JP)</option>
    <option value="ko_KR">Korean (South Korea)</option>
    <option value="lv_LV">Latvian (Latvia)</option>
    <option value="lt_LT">Lithuanian (Lithuania)</option>
    <option value="mk_MK">Macedonian (Macedonia)</option>
    <option value="ms_MY">Malay (Malaysia)</option>
    <option value="mt_MT">Maltese (Malta)</option>
    <option value="no_NO">Norwegian (Norway)</option>
    <option value="no_NO_NY">Norwegian (Norway,Nynorsk)</option>
    <option value="pl_PL">Polish (Poland)</option>
    <option value="pt_BR">Portuguese (Brazil)</option>
    <option value="pt_PT">Portuguese (Portugal)</option>
    <option value="ro_RO">Romanian (Romania)</option>
    <option value="ru_RU">Russian (Russia)</option>
    <option value="sr_BA">Serbian (Bosnia and Herzegovina)</option>
    <option value="sr_ME">Serbian (Montenegro)</option>
    <option value="sr_CS">Serbian (Serbia and Montenegro)</option>
    <option value="sr_RS">Serbian (Serbia)</option>
    <option value="sk_SK">Slovak (Slovakia)</option>
    <option value="sl_SI">Slovenian (Slovenia)</option>
    <option value="es_AR">Spanish (Argentina)</option>
    <option value="es_BO">Spanish (Bolivia)</option>
    <option value="es_CL">Spanish (Chile)</option>
    <option value="es_CO">Spanish (Colombia)</option>
    <option value="es_CR">Spanish (Costa Rica)</option>
    <option value="es_DO">Spanish (Dominican Republic)</option>
    <option value="es_EC">Spanish (Ecuador)</option>
    <option value="es_SV">Spanish (El Salvador)</option>
    <option value="es_GT">Spanish (Guatemala)</option>
    <option value="es_HN">Spanish (Honduras)</option>
    <option value="es_MX">Spanish (Mexico)</option>
    <option value="es_NI">Spanish (Nicaragua)</option>
    <option value="es_PA">Spanish (Panama)</option>
    <option value="es_PY">Spanish (Paraguay)</option>
    <option value="es_PE">Spanish (Peru)</option>
    <option value="es_PR">Spanish (Puerto Rico)</option>
    <option value="es_ES">Spanish (Spain)</option>
    <option value="es_US">Spanish (United States)</option>
    <option value="es_UY">Spanish (Uruguay)</option>
    <option value="es_VE">Spanish (Venezuela)</option>
    <option value="sv_SE">Swedish (Sweden)</option>
    <option value="th_TH">Thai (Thailand)</option>
    <option value="th_TH_TH">Thai (Thailand,TH)</option>
    <option value="tr_TR">Turkish (Turkey)</option>
    <option value="uk_UA">Ukrainian (Ukraine)</option>
    <option value="vi_VN">Vietnamese (Vietnam)</option>
  </select>
		<?php echo $e_gt->getFormFooter("Translate"); ?>
		<div class="spacerblock_20"></div>

		<div class="spacerblock_20"></div>
	      <?php
		$e_gt_translated = new Event("GoogleTranslatorEmailtemplate->eventAddTranslateLanguage");
		$e_gt_translated->addParam("goto", $_SERVER['PHP_SELF']);
		$e_gt_translated->addParam("dest_lng",$dest_lng);
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
        <div class="spacerblock_20"></div>

    <div class="dottedline"></div>
    </td>
	</tr>
	</table>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>