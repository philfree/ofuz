<?php
namespace RadriaCore\Radria;
 /**
   * XML Base Load data base class
   * Basic XML manipulation, will need rewrite
   *
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007
   * @version 3.0
   * @package RadriaCore
   * @access public
   */

class XMLBaseLoad {
  var $open_tags ;
  var $close_tags ;
  var $current_tag ;
  var $current_field ;
  var $current_type ;
  var $xml_file = '';
  var $type = 'UTF-8';
  var $fp   ;
  var $xml_parser ;

  function init($filename) {
    if (strlen($filename) > 0) {
      $this->xml_file = $filename ;
    }
    $this->xml_parser = xml_parser_create();
    xml_set_object($this->xml_parser, $this);
    xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, true);
    xml_set_element_handler($this->xml_parser, "startelement","endElement");
    xml_set_character_data_handler($this->xml_parser, "characterData");
    if (!($this->fp = fopen($this->xml_file, 'r'))) {
      die("Could not open $this->xml_file for parsing!\n");
    }
  }

  function startelement($parser, $name, $attrs='') {
  }

  function endElement($parser, $name, $attrs=''){
  }

  function characterData($parser, $data){
  }

  function parse() {
    while ($data = fread($this->fp, 4096)) {
      $data = utf8_encode($data);
      if (!xml_parse($this->xml_parser, $data, feof($this->fp))) {
        die(sprintf( "XML error: %s at line %d\n\n",
        xml_error_string(xml_get_error_code($this->xml_parser)),
        xml_get_current_line_number($this->xml_parser)));
      }

    }
  }
  function free() {
    xml_parser_free($this->xml_parser);
    fclose($this->fp) ;
  }
}
?>