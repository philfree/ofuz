<?php
// Copyright 2010 SQLFusion LLC  info@sqlfusion.com
// All rights reserved

    /**
      * Creates buttons using supplied text
      *
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzCore
      * @license ##License##
      * @version 0.6
      * @date 2010-09-04
      * @since 0.6
      */

class DynamicButton {

    /**
     * Generate the HTML to display the button.
     * Optional icon class name parameters include:
     *   dyn_button_add_new_contact
     *   dyn_button_add_new_invoice
     *   dyn_button_add_new_task
     *   dyn_button_receive_payment
     *   dyn_button_share_this
     * 
     * @param string $href is the URL the button will link to.
     * @param string $text is the text that display in the button.
     * @param string $onclick in a javascript call to onclick event.
     * @param string $icon is the name of a CSS class to display an icon on the left.
     * @param string $style is a string with css styles to apply to the button.
     * @return string of HTML code that will display the button.
     *
     */

  function CreateButton($href, $text, $id = '', $onclick = '', $icon = '', $style = '') {
    $html = '<div class="dyn_button"';
    if ($id != '') {
        $html .= ' id="'.$id.'"';
    }
    if ($style != '') {
        $html .= ' style="'.$style.'"';
    }
    $html .= '><div class="dyn_button_c"';
    if ($onclick != '') {
        $html .= ' onclick="'.$onclick.'">';
    } else {
        $html .= ' onclick="document.location.href=\''.$href.'\'">';
    }
    if ($icon == '') {
        $html .= '<div class="dyn_button_l"></div><div class="dyn_button_text">';
    } else {
        $html .= '<div class="'.$icon.'"></div><div class="dyn_button_text_icon">';
    }
    $html .= '<a href="'.$href.'"';
    if ($onclick != '') {
        $html .= ' onclick="'.$onclick.'"';
    }
    $html .= '>'._($text).'</a>';
    $html .= '</div><div class="dyn_button_r"></div></div></div>';
    return $html;
  }

}
?>
