<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 *
 * 
 */

//include_once("class/GoogleTranslateAPI.class.php");

class GoogleTranslatorEmailtemplate extends Google_Translate_API {

  public $table = "emailtemplate";
  protected $primary_key = "idemailtemplate";

  public $src_lng = "";
  public $dest_lng = "";

  function __construct(sqlConnect $conx=NULL, $table_name="") {
    parent::__construct($conx, $table_name);
    $this->src_lng = $_SESSION["src_lang"];
    $this->dest_lng = $_SESSION["dest_lang"];
  }

  function getTemplatesToBeTranslated() {
    if(!isset($_SESSION["skip_et"])) {
      $skip = "ORDER BY idemailtemplate ASC LIMIT 1";
    } else {
      $skip = $_SESSION["skip_et"];
    }

    $sql = "select idemailtemplate,subject,bodytext,bodyhtml,name,sendername,senderemail,language, count(*) as done from {$this->table} where language='{$this->src_lng}' or language = '{$this->dest_lng}' group by name having done = 1 {$skip}";

    $this->query($sql);
    
  }

  function eventSetNextTemplate(EventControler $evtcl) {
    unset($_SESSION["et_sub_src"]);
    unset($_SESSION["et_body_text_src"]);
    unset($_SESSION["et_body_html_src"]);

    $_SESSION["skip_et"] = "AND idemailtemplate > {$evtcl->current_idtemplate} ORDER BY idemailtemplate ASC LIMIT 1";
    $evtcl->setDisplayNext(new Display($evtcl->goto));
  }

  function eventSetPrevTemplate(EventControler $evtcl) {
    unset($_SESSION["et_sub_src"]);
    unset($_SESSION["et_body_text_src"]);
    unset($_SESSION["et_body_html_src"]);

    $_SESSION["skip_et"] = "AND idemailtemplate < {$evtcl->current_idtemplate} ORDER BY idemailtemplate DESC LIMIT 1";
    $evtcl->setDisplayNext(new Display($evtcl->goto));
  }

  function isPrev($current_idtemplate) {
    $q = new sqlQuery($this->getDbCon());
    $sql = "select idemailtemplate,subject,bodytext,bodyhtml,name, language, count(*) as done from {$this->table} where (language='{$this->src_lng}' or language = '{$this->dest_lng}') AND idemailtemplate < {$current_idtemplate}  group by name having done = 1 ORDER BY idemailtemplate DESC LIMIT 1";

    $q->query($sql);

    if($q->getNumRows()) {
      return true;
    } else {
      return false;
    }
  }

  function isNext($current_idtemplate) {
    $q = new sqlQuery($this->getDbCon());
    $sql = "select name, language, count(*) as done from {$this->table} where (language='{$this->src_lng}' or language = '{$this->dest_lng}') AND idemailtemplate > {$current_idtemplate}  group by name having done = 1 ORDER BY idemailtemplate ASC LIMIT 1";
    $q->query($sql);

    if($q->getNumRows()) {
      return true;
    } else {
      return false;
    }
  }

  function eventTranslateLanguage(EventControler $evtcl) {

    //$src_lang = explode("_",$evtcl->src_lang);
    //$dest_lng = explode("_",$evtcl->dest_lang);

    $src_lang = explode("_",$this->src_lng);
    $dest_lng = explode("_",$this->dest_lng);

    $fields_subject = MergeString :: getField($evtcl->et_sub_src);
    $arr_fields_subject = array();
    foreach($fields_subject as $fields_sub) {
      $arr_fields_subject[$fields_sub] = "AB".rand(1,9999)."YZ";
    }
    $subject = htmlspecialchars($evtcl->et_sub_src, ENT_QUOTES);
    $subject = MergeString::withArray($subject, $arr_fields_subject);

    $fields_bodytext = MergeString :: getField($evtcl->et_body_text_src);
    $arr_bodytext = array();
    foreach($fields_bodytext as $fields_body_text) {
      $arr_bodytext[$fields_body_text] = "AB".rand(1,9999)."YZ";
    }
    $bodytext = htmlspecialchars($evtcl->et_body_text_src, ENT_QUOTES);
    $bodytext = MergeString::withArray($bodytext, $arr_bodytext);

    $fields_bodyhtml = MergeString :: getField($evtcl->et_body_html_src);
    $arr_bodyhtml = array();
    foreach($fields_bodyhtml as $fields_body_html) {
      $arr_bodyhtml[$fields_body_html] = "AB".rand(1,9999)."YZ";
    }

    $bodyhtml = htmlspecialchars($evtcl->et_body_html_src, ENT_QUOTES);
    $bodyhtml = MergeString::withArray($bodyhtml, $arr_bodyhtml);

    $trans_subject = parent::translate($subject, $src_lang[0], $dest_lng[0]);
    $trans_bodytext = parent::translate($bodytext, $src_lang[0], $dest_lng[0]);
    $trans_bodyhtml = parent::translate($bodyhtml, $src_lang[0], $dest_lng[0]);

    $trans_subject = htmlspecialchars_decode($trans_subject);
    $trans_bodytext = htmlspecialchars_decode($trans_bodytext);
    $trans_bodyhtml = htmlspecialchars_decode($trans_bodyhtml);

    $subject = $this->withField($trans_subject, $arr_fields_subject) ;
    $bodytext = $this->withField($trans_bodytext, $arr_bodytext) ;
    $bodyhtml = $this->withField($trans_bodyhtml, $arr_bodyhtml) ;

    $_SESSION["et_sub_src"] = $subject;
    $_SESSION["et_body_text_src"] = $bodytext;
    $_SESSION["et_body_html_src"] = $bodyhtml;

  }

  function withField($thestring, $values) {
    if (is_array($values)) {
	foreach ($values as $key=>$val) {
	  $thestring = str_replace($val, '['.$key.']', $thestring) ;
	}
    }
    return $thestring;
  }

  function eventAddTranslateLanguage(EventControler $evtcl) {
    unset($_SESSION["et_sub_src"]);
    unset($_SESSION["et_body_text_src"]);
    unset($_SESSION["et_body_html_src"]);

    $this->name = $evtcl->name;
    $this->subject = $evtcl->et_sub_dst;
    $this->bodytext = $evtcl->et_body_text_dst;
    $this->bodyhtml = $evtcl->et_body_html_dst;
    $this->sendername = $evtcl->sendername;
    $this->senderemail = $evtcl->senderemail;
    $this->language = $this->dest_lng;
    $this->add();
    $evtcl->setDisplayNext(new Display($evtcl->goto));
  }

  function tempr() {
    $this->query("select bodyhtml from emailtemplate");
  }
} //end of class

?>
