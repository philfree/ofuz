<?php 

  /**
   * XML Flat data Load class
   * Basic XML manipulation, will need rewrite
   *
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2004
   * @version 3.0
   * @package RadriaCore
   * @access public
   */

class XMLFlatDataLoad extends XMLBaseLoad {
  var $finaldata ;
  var $lowerfinaldata ;

  function startElement($parser, $name, $attrs=''){
    $this->current_tag = $name;
  }

  function endElement($parser, $name, $attrs=''){
    $this->current_tag = "" ;
  }

  function characterData($parser, $data){
      $curtag = strtolower($this->current_tag) ;
      $this->lowerfinaldata[$curtag] .= $data ;
      $this->finaldata[$this->current_tag] .= $data ;
  }

  function arrayToXML($arraydata, $file, $type) {
    $xmlFile  = "<?xml version=\"1.0\"?>" ;
    $xmlFile .= "\n  <".$type.">" ;
    while (list($field, $value) = each($arraydata)) {
      if (!empty($field)) {
          $xmlFile .="\n    <".$field."><![CDATA[".$value."]]></".$field.">" ;
      }
    }
    $xmlFile .= "\n  </".$type.">" ;
    $fp = fopen($file, "w") ;
    fwrite($fp, $xmlFile) ;
    fclose($fp) ;
    return $xmlFile ;
  }
}
?>
