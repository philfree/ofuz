<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 *
 * 
 */

#include_once("class/GoogleTranslateAPI.class.php");

class GoogleTranslatorMessage extends Google_Translate_API {

  public $table = "message";
  protected $primary_key = "idmessage";

  public $src_lng = "";
  public $dest_lng = "";
  
  function __construct(sqlConnect $conx=NULL, $table_name="") {
    parent::__construct($conx, $table_name);
    $this->src_lng = $_SESSION["src_lang"];
    $this->dest_lng = $_SESSION["dest_lang"];
  }

  function getTemplatesToBeTranslated() {
    if(!isset($_SESSION["skip"])) {
      $skip = "ORDER BY idmessage ASC LIMIT 1";
    } else {
      $skip = $_SESSION["skip"];
    }

    $sql = "select idmessage,key_name,content,context,can_close,close_duration,plan,language, count(*) as done from {$this->table} where language='{$this->src_lng}' or language = '{$this->dest_lng}' group by key_name having done = 1 {$skip}";

    $this->query($sql);
    
  }

  function eventSetNextTemplate(EventControler $evtcl) {
    unset($_SESSION["et_content_src"]);

    $_SESSION["skip"] = "AND idmessage > {$evtcl->current_idtemplate} ORDER BY idmessage ASC LIMIT 1";
    $evtcl->setDisplayNext(new Display($evtcl->goto));
  }

  function eventSetPrevTemplate(EventControler $evtcl) {
    unset($_SESSION["et_content_src"]);

    $_SESSION["skip"] = "AND idmessage < {$evtcl->current_idtemplate} ORDER BY idmessage DESC LIMIT 1";
    $evtcl->setDisplayNext(new Display($evtcl->goto));
  }

  function isPrev($current_idtemplate) {
    $q = new sqlQuery($this->getDbCon());
    $sql = "select idmessage,key_name,content,context,can_close,close_duration,plan,language, count(*) as done from {$this->table} where (language='{$this->src_lng}' or language = '{$this->dest_lng}') AND idmessage < {$current_idtemplate}  group by key_name having done = 1 ORDER BY idmessage DESC LIMIT 1";

    $q->query($sql);

    if($q->getNumRows()) {
      return true;
    } else {
      return false;
    }
  }

  function isNext($current_idtemplate) {
    $q = new sqlQuery($this->getDbCon());
    $sql = "select idmessage,key_name,content,context,can_close,close_duration,plan,language, count(*) as done from {$this->table} where (language='{$this->src_lng}' or language = '{$this->dest_lng}') AND idmessage > {$current_idtemplate}  group by key_name having done = 1 ORDER BY idmessage ASC LIMIT 1";
    $q->query($sql);

    if($q->getNumRows()) {
      return true;
    } else {
      return false;
    }
  }

  function eventTranslateLanguage(EventControler $evtcl) {
    $src_lang = explode("_",$this->src_lng);
    $dest_lng = explode("_",$this->dest_lng);

    $fields_content = MergeString :: getField($evtcl->et_content_src);
    $arr_fields_content = array();
    foreach($fields_content as $fields_cont) {
      $arr_fields_content[$fields_cont] = "AB".rand(1,9999)."YZ";
    }
    $content = htmlspecialchars($evtcl->et_content_src, ENT_QUOTES);
    $content = MergeString::withArray($content, $arr_fields_content);

    $trans_content = parent::translate($content, $src_lang[0], $dest_lng[0]);

    $trans_content = htmlspecialchars_decode($trans_content);

    $content = $this->withField($trans_content, $arr_fields_content) ;

    $_SESSION["et_content_src"] = $content;
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
    unset($_SESSION["et_content_src"]);

    $this->key_name = $evtcl->key_name;
    $this->content = $evtcl->et_content_dst;
    $this->language = $this->dest_lng;
    $this->context = $evtcl->context;
    $this->can_close = $evtcl->can_close;
    $this->close_duration = $evtcl->close_duration;
    $this->plan = $evtcl->plan;

    $this->add();
    $evtcl->setDisplayNext(new Display($evtcl->goto));
  }

} //end of class

?>
