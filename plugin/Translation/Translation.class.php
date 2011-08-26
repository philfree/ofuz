<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/ 

  /**
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package Translation
    * @license ##License##
    * @version 0.6.2
    * @date 2010-12-06
    * @since 0.6.2
    */

class Translation extends BaseBlock{

  public $short_description = 'Ofuz Translation block';
  public $long_description = 'Translates the messages and Email templates.';

    
  /**
   * processBlock() , This method must be added  
   * Required to set the Block Title and The Block Content Followed by displayBlock()
   * Must extend BaseBlock
   */

  function processBlock(){
  
    $this->setTitle(_('Translation'));
    $this->setContent($this->generateBlockContent());    
    $this->displayBlock();
  }

  /**
   * A custom method within the Plugin to generate the content
   * @return string : html
   */

  function generateBlockContent(){
    $output = '';

    $e_translate = new Event("Translation->eventSetDestinationLanguage");
    $e_translate->addParam("goto", $_SERVER["REDIRECT_URL"]  );
    $output .= $e_translate->getFormHeader();
    $output .= $e_translate->getFormEvent();

    $source_lang = '<select name="src_lang">';
    $source_lang .= '<option value="en_US">English</option>';
    $source_lang .= '</select>';

    $destination_lang = '
    <select name="dest_lang">
      <option value="">Select</option>
      <option value="sq_AL" '.($selected = ($_SESSION["dest_lang"]=="sq_AL")?"selected":"").'>Albanian (Albania)</option>
      <option value="ar_DZ" '.($selected = ($_SESSION["dest_lang"]=="ar_DZ")?"selected":"").'>Arabic (Algeria)</option>
      <option value="ar_BH" '.($selected = ($_SESSION["dest_lang"]=="ar_BH")?"selected":"").'>Arabic (Bahrain)</option>
      <option value="ar_EG" '.($selected = ($_SESSION["dest_lang"]=="ar_EG")?"selected":"").'>Arabic (Egypt)</option>
      <option value="ar_IQ" '.($selected = ($_SESSION["dest_lang"]=="ar_IQ")?"selected":"").'>Arabic (Iraq)</option>
      <option value="ar_JO" '.($selected = ($_SESSION["dest_lang"]=="ar_JO")?"selected":"").'>Arabic (Jordan)</option>
      <option value="ar_KW" '.($selected = ($_SESSION["dest_lang"]=="ar_KW")?"selected":"").'>Arabic (Kuwait)</option>
      <option value="ar_LB" '.($selected = ($_SESSION["dest_lang"]=="ar_LB")?"selected":"").'>Arabic (Lebanon)</option>
      <option value="ar_LY" '.($selected = ($_SESSION["dest_lang"]=="ar_LY")?"selected":"").'>Arabic (Libya)</option>
      <option value="ar_MA" '.($selected = ($_SESSION["dest_lang"]=="ar_MA")?"selected":"").'>Arabic (Morocco)</option>
      <option value="ar_OM" '.($selected = ($_SESSION["dest_lang"]=="ar_OM")?"selected":"").'>Arabic (Oman)</option>
      <option value="ar_QA" '.($selected = ($_SESSION["dest_lang"]=="ar_QA")?"selected":"").'>Arabic (Qatar)</option>
      <option value="ar_SA" '.($selected = ($_SESSION["dest_lang"]=="ar_SA")?"selected":"").'>Arabic (Saudi Arabia)</option>
      <option value="ar_SD" '.($selected = ($_SESSION["dest_lang"]=="ar_SD")?"selected":"").'>Arabic (Sudan)</option>
      <option value="ar_SY" '.($selected = ($_SESSION["dest_lang"]=="ar_SY")?"selected":"").'>Arabic (Syria)</option>
      <option value="ar_TN" '.($selected = ($_SESSION["dest_lang"]=="ar_TN")?"selected":"").'>Arabic (Tunisia)</option>
      <option value="ar_AE" '.($selected = ($_SESSION["dest_lang"]=="ar_AE")?"selected":"").'>Arabic (United Arab Emirates)</option>
      <option value="ar_YE" '.($selected = ($_SESSION["dest_lang"]=="ar_YE")?"selected":"").'>Arabic (Yemen)</option>
      <option value="be_BY" '.($selected = ($_SESSION["dest_lang"]=="be_BY")?"selected":"").'>Belarusian (Belarus)</option>
      <option value="bg_BG" '.($selected = ($_SESSION["dest_lang"]=="bg_BG")?"selected":"").'>Bulgarian (Bulgaria)</option>
      <option value="ca_ES" '.($selected = ($_SESSION["dest_lang"]=="ca_ES")?"selected":"").'>Catalan (Spain)</option>
      <option value="zh_CN" '.($selected = ($_SESSION["dest_lang"]=="zh_CN")?"selected":"").'>Chinese (China)</option>
      <option value="zh_HK" '.($selected = ($_SESSION["dest_lang"]=="zh_HK")?"selected":"").'>Chinese (Hong Kong)</option>
      <option value="zh_SG" '.($selected = ($_SESSION["dest_lang"]=="zh_SG")?"selected":"").'>Chinese (Singapore)</option>
      <option value="zh_TW" '.($selected = ($_SESSION["dest_lang"]=="zh_TW")?"selected":"").'>Chinese (Taiwan)</option>
      <option value="hr_HR" '.($selected = ($_SESSION["dest_lang"]=="hr_HR")?"selected":"").'>Croatian (Croatia)</option>
      <option value="cs_CZ" '.($selected = ($_SESSION["dest_lang"]=="cs_CZ")?"selected":"").'>Czech (Czech Republic)</option>
      <option value="da_DK" '.($selected = ($_SESSION["dest_lang"]=="da_DK")?"selected":"").'>Danish (Denmark)</option>
      <option value="nl_BE" '.($selected = ($_SESSION["dest_lang"]=="nl_BE")?"selected":"").'>Dutch (Belgium)</option>
      <option value="nl_NL" '.($selected = ($_SESSION["dest_lang"]=="nl_NL")?"selected":"").'>Dutch (Netherlands)</option>
      <option value="en_AU" '.($selected = ($_SESSION["dest_lang"]=="en_AU")?"selected":"").'>English (Australia)</option>
      <option value="en_CA" '.($selected = ($_SESSION["dest_lang"]=="en_CA")?"selected":"").'>English (Canada)</option>
      <option value="en_IN" '.($selected = ($_SESSION["dest_lang"]=="en_IN")?"selected":"").'>English (India)</option>
      <option value="en_IE" '.($selected = ($_SESSION["dest_lang"]=="en_IE")?"selected":"").'>English (Ireland)</option>
      <option value="en_MT" '.($selected = ($_SESSION["dest_lang"]=="en_MT")?"selected":"").'>English (Malta)</option>
      <option value="en_NZ" '.($selected = ($_SESSION["dest_lang"]=="en_NZ")?"selected":"").'>English (New Zealand)</option>
      <option value="en_PH" '.($selected = ($_SESSION["dest_lang"]=="en_PH")?"selected":"").'>English (Philippines)</option>
      <option value="en_SG" '.($selected = ($_SESSION["dest_lang"]=="en_SG")?"selected":"").'>English (Singapore)</option>
      <option value="en_ZA" '.($selected = ($_SESSION["dest_lang"]=="en_ZA")?"selected":"").'>English (South Africa)</option>
      <option value="en_GB" '.($selected = ($_SESSION["dest_lang"]=="en_GB")?"selected":"").'>English (United Kingdom)</option>
      <option value="en_US" '.($selected = ($_SESSION["dest_lang"]=="en_US")?"selected":"").'>English (United States)</option>
      <option value="et_EE" '.($selected = ($_SESSION["dest_lang"]=="et_EE")?"selected":"").'>Estonian (Estonia)</option>
      <option value="fi_FI" '.($selected = ($_SESSION["dest_lang"]=="fi_FI")?"selected":"").'>Finnish (Finland)</option>
      <option value="fr_BE" '.($selected = ($_SESSION["dest_lang"]=="fr_BE")?"selected":"").'>French (Belgium)</option>
      <option value="fr_CA" '.($selected = ($_SESSION["dest_lang"]=="fr_CA")?"selected":"").'>French (Canada)</option>
      <option value="fr_FR" '.($selected = ($_SESSION["dest_lang"]=="fr_FR")?"selected":"").'>French (France)</option>
      <option value="fr_LU" '.($selected = ($_SESSION["dest_lang"]=="fr_LU")?"selected":"").'>French (Luxembourg)</option>
      <option value="fr_CH" '.($selected = ($_SESSION["dest_lang"]=="fr_CH")?"selected":"").'>French (Switzerland)</option>
      <option value="de_AT" '.($selected = ($_SESSION["dest_lang"]=="de_AT")?"selected":"").'>German (Austria)</option>
      <option value="de_DE" '.($selected = ($_SESSION["dest_lang"]=="de_DE")?"selected":"").'>German (Germany)</option>
      <option value="de_LU" '.($selected = ($_SESSION["dest_lang"]=="de_LU")?"selected":"").'>German (Luxembourg)</option>
      <option value="de_CH" '.($selected = ($_SESSION["dest_lang"]=="de_CH")?"selected":"").'>German (Switzerland)</option>
      <option value="el_CY" '.($selected = ($_SESSION["dest_lang"]=="el_CY")?"selected":"").'>Greek (Cyprus)</option>
      <option value="el_GR" '.($selected = ($_SESSION["dest_lang"]=="el_GR")?"selected":"").'>Greek (Greece)</option>
      <option value="iw_IL" '.($selected = ($_SESSION["dest_lang"]=="iw_IL")?"selected":"").'>Hebrew (Israel)</option>
      <option value="hi_IN" '.($selected = ($_SESSION["dest_lang"]=="hi_IN")?"selected":"").'>Hindi (India)</option>
      <option value="hu_HU" '.($selected = ($_SESSION["dest_lang"]=="hu_HU")?"selected":"").'>Hungarian (Hungary)</option>
      <option value="is_IS" '.($selected = ($_SESSION["dest_lang"]=="is_IS")?"selected":"").'>Icelandic (Iceland)</option>
      <option value="in_ID" '.($selected = ($_SESSION["dest_lang"]=="in_ID")?"selected":"").'>Indonesian (Indonesia)</option>
      <option value="ga_IE" '.($selected = ($_SESSION["dest_lang"]=="ga_IE")?"selected":"").'>Irish (Ireland)</option>
      <option value="it_IT" '.($selected = ($_SESSION["dest_lang"]=="it_IT")?"selected":"").'>Italian (Italy)</option>
      <option value="it_CH" '.($selected = ($_SESSION["dest_lang"]=="it_CH")?"selected":"").'>Italian (Switzerland)</option>
      <option value="ja_JP" '.($selected = ($_SESSION["dest_lang"]=="ja_JP")?"selected":"").'>Japanese (Japan)</option>
      <option value="ja_JP_JP" '.($selected = ($_SESSION["dest_lang"]=="ja_JP_JP")?"selected":"").'>Japanese (Japan,JP)</option>
      <option value="ko_KR" '.($selected = ($_SESSION["dest_lang"]=="ko_KR")?"selected":"").'>Korean (South Korea)</option>
      <option value="lv_LV" '.($selected = ($_SESSION["dest_lang"]=="lv_LV")?"selected":"").'>Latvian (Latvia)</option>
      <option value="lt_LT" '.($selected = ($_SESSION["dest_lang"]=="lt_LT")?"selected":"").'>Lithuanian (Lithuania)</option>
      <option value="mk_MK" '.($selected = ($_SESSION["dest_lang"]=="mk_MK")?"selected":"").'>Macedonian (Macedonia)</option>
      <option value="ms_MY" '.($selected = ($_SESSION["dest_lang"]=="ms_MY")?"selected":"").'>Malay (Malaysia)</option>
      <option value="mt_MT" '.($selected = ($_SESSION["dest_lang"]=="mt_MT")?"selected":"").'>Maltese (Malta)</option>
      <option value="no_NO" '.($selected = ($_SESSION["dest_lang"]=="no_NO")?"selected":"").'>Norwegian (Norway)</option>
      <option value="no_NO_NY" '.($selected = ($_SESSION["dest_lang"]=="no_NO_NY")?"selected":"").'>Norwegian (Norway,Nynorsk)</option>
      <option value="pl_PL" '.($selected = ($_SESSION["dest_lang"]=="pl_PL")?"selected":"").'>Polish (Poland)</option>
      <option value="pt_BR" '.($selected = ($_SESSION["dest_lang"]=="pt_BR")?"selected":"").'>Portuguese (Brazil)</option>
      <option value="pt_PT" '.($selected = ($_SESSION["dest_lang"]=="pt_PT")?"selected":"").'>Portuguese (Portugal)</option>
      <option value="ro_RO" '.($selected = ($_SESSION["dest_lang"]=="ro_RO")?"selected":"").'>Romanian (Romania)</option>
      <option value="ru_RU" '.($selected = ($_SESSION["dest_lang"]=="ru_RU")?"selected":"").'>Russian (Russia)</option>
      <option value="sr_BA" '.($selected = ($_SESSION["dest_lang"]=="sr_BA")?"selected":"").'>Serbian (Bosnia and Herzegovina)</option>
      <option value="sr_ME" '.($selected = ($_SESSION["dest_lang"]=="sr_ME")?"selected":"").'>Serbian (Montenegro)</option>
      <option value="sr_CS" '.($selected = ($_SESSION["dest_lang"]=="sr_CS")?"selected":"").'>Serbian (Serbia and Montenegro)</option>
      <option value="sr_RS" '.($selected = ($_SESSION["dest_lang"]=="sr_RS")?"selected":"").'>Serbian (Serbia)</option>
      <option value="sk_SK" '.($selected = ($_SESSION["dest_lang"]=="sk_SK")?"selected":"").'>Slovak (Slovakia)</option>
      <option value="sl_SI" '.($selected = ($_SESSION["dest_lang"]=="sl_SI")?"selected":"").'>Slovenian (Slovenia)</option>
      <option value="es_AR" '.($selected = ($_SESSION["dest_lang"]=="es_AR")?"selected":"").'>Spanish (Argentina)</option>
      <option value="es_BO" '.($selected = ($_SESSION["dest_lang"]=="es_BO")?"selected":"").'>Spanish (Bolivia)</option>
      <option value="es_CL" '.($selected = ($_SESSION["dest_lang"]=="es_CL")?"selected":"").'>Spanish (Chile)</option>
      <option value="es_CO" '.($selected = ($_SESSION["dest_lang"]=="es_CO")?"selected":"").'>Spanish (Colombia)</option>
      <option value="es_CR" '.($selected = ($_SESSION["dest_lang"]=="es_CR")?"selected":"").'>Spanish (Costa Rica)</option>
      <option value="es_DO" '.($selected = ($_SESSION["dest_lang"]=="es_DO")?"selected":"").'>Spanish (Dominican Republic)</option>
      <option value="es_EC" '.($selected = ($_SESSION["dest_lang"]=="es_EC")?"selected":"").'>Spanish (Ecuador)</option>
      <option value="es_SV" '.($selected = ($_SESSION["dest_lang"]=="es_SV")?"selected":"").'>Spanish (El Salvador)</option>
      <option value="es_GT" '.($selected = ($_SESSION["dest_lang"]=="es_GT")?"selected":"").'>Spanish (Guatemala)</option>
      <option value="es_HN" '.($selected = ($_SESSION["dest_lang"]=="es_HN")?"selected":"").'>Spanish (Honduras)</option>
      <option value="es_MX" '.($selected = ($_SESSION["dest_lang"]=="es_MX")?"selected":"").'>Spanish (Mexico)</option>
      <option value="es_NI" '.($selected = ($_SESSION["dest_lang"]=="es_NI")?"selected":"").'>Spanish (Nicaragua)</option>
      <option value="es_PA" '.($selected = ($_SESSION["dest_lang"]=="es_PA")?"selected":"").'>Spanish (Panama)</option>
      <option value="es_PY" '.($selected = ($_SESSION["dest_lang"]=="es_PY")?"selected":"").'>Spanish (Paraguay)</option>
      <option value="es_PE" '.($selected = ($_SESSION["dest_lang"]=="es_PE")?"selected":"").'>Spanish (Peru)</option>
      <option value="es_PR" '.($selected = ($_SESSION["dest_lang"]=="es_PR")?"selected":"").'>Spanish (Puerto Rico)</option>
      <option value="es_ES" '.($selected = ($_SESSION["dest_lang"]=="es_ES")?"selected":"").'>Spanish (Spain)</option>
      <option value="es_US" '.($selected = ($_SESSION["dest_lang"]=="es_US")?"selected":"").'>Spanish (United States)</option>
      <option value="es_UY" '.($selected = ($_SESSION["dest_lang"]=="es_UY")?"selected":"").'>Spanish (Uruguay)</option>
      <option value="es_VE" '.($selected = ($_SESSION["dest_lang"]=="es_VE")?"selected":"").'>Spanish (Venezuela)</option>
      <option value="sv_SE" '.($selected = ($_SESSION["dest_lang"]=="sv_SE")?"selected":"").'>Swedish (Sweden)</option>
      <option value="th_TH" '.($selected = ($_SESSION["dest_lang"]=="th_TH")?"selected":"").'>Thai (Thailand)</option>
      <option value="th_TH_TH" '.($selected = ($_SESSION["dest_lang"]=="th_TH_TH")?"selected":"").'>Thai (Thailand,TH)</option>
      <option value="tr_TR" '.($selected = ($_SESSION["dest_lang"]=="tr_TR")?"selected":"").'>Turkish (Turkey)</option>
      <option value="uk_UA" '.($selected = ($_SESSION["dest_lang"]=="uk_UA")?"selected":"").'>Ukrainian (Ukraine)</option>
      <option value="vi_VN" '.($selected = ($_SESSION["dest_lang"]=="vi_VN")?"selected":"").'>Vietnamese (Vietnam)</option>
    </select>';
    
    $output .= $source_lang."<br /> To <br />".$destination_lang;
    $output .= $e_translate->getFormFooter(_("Submit"));

    $menu = "<br /><a href=\"/Tab/Translation/Message\">Untranslated messages</a><br />";
    $menu .= "<a href=\"/Tab/Translation/EmailTemplate\">Untranslated email template</a><br />";
    $menu .= "<a href=\"/entrans/list.php?category=untrans&page=1\">Untranslated text strings</a><br />";
   // $menu .= "<a href=\"javascript:;\">Edit email template</a><br />";
   // $menu .= "<a href=\"javascript:;\">Edit messages</a>";

    return $output.$menu;
  }  

  function eventSetDestinationLanguage(EventControler $evtcl) {
    $_SESSION["src_lang"] = $evtcl->src_lang;
    $_SESSION["dest_lang"] = $evtcl->dest_lang;
    $evtcl->setDisplayNext(new Display($evtcl->goto));
  }


      
}

?>
