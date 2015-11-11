<?php
namespace RadriaCore\Radria;
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
  /**
   * XML Registry Load class
   * Basic XML manipulation, will need rewrite
   *   
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2004
   * @version 3.0
   * @package RadriaCore
   * @access public
   */

class XMLRegistryLoad extends XMLBaseLoad {
  var $open_tags = array(
    'RFIELD' => '<RFIELD>',
    'RDATA' => '<RDATA>');

  var $close_tags = array(
    'RFIELD' => '</RDATA>',
    'RDATA' => '</RDATA>');

  var $finaldata ;

  function startElement($parser, $name, $attrs=''){
    $this->current_tag = $name;
    if ($format = $this->open_tags[$name]){
        switch($name){
            case 'RFIELD':
                $this->current_field = $attrs[NAME] ;
            break;
            case 'RDATA':
                $this->current_type = $attrs[TYPE] ;
            break ;
            default:
            break;
        }
    }
}

  function endElement($parser, $name, $attrs=''){
    if ($format = $this->close_tags[$name]){
    switch($name){
        case 'RFIELD':
          $this->current_field ="" ;
        break;
        case 'RDATA':
          $this->finaldata[$this->current_field][$this->current_type] = trim($this->current_data) ;
          $this->current_type = "" ;
          $this->current_data = "" ;
        break ;
        default:
        break;
    }
    }
  }

  function characterData($parser, $data){
    switch($this->current_tag){
    case 'RDATA':
      $this->current_data .= $data ;
      break;
    default:
        break;
    }
  }
}
?>
