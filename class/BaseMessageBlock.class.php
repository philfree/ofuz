<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
  * BaseMessageBlock class
  * 
  * @author
  */

class BaseMessageBlock extends BaseObject{

    public $block_content = '';
    public $url_path = '';
    public $url_name = '';
    protected $is_active = true;
    protected $button_onclick_display_block = false;
    protected $hidden_div_id = '';
    protected $button_url = '#' ;
    protected $call_back_method = '';
    protected $button_name = '';
    protected $button_div_id = '';
    protected $button_icon = '';
    protected $show_content = true;
    public $short_description = '';
    public $long_description = '' ;

    /**
      * Method to set the block content
      * @param string $content
    */
    public function setContent($content){
      $this->block_content = $content ;
    }

    /**
      * Method displaying the block
    */
    public function displayBlock(){
      $html = '';
      $button_ui = '';
      if($this->button_onclick_display_block === true){
        $button_ui .= $this->setButton();
      }
      if($this->show_content === true ){
	$html .= '<div class="left_menu">';
	//$html .= '<div id="db_message" class="messageshadow">';
	$html .= '     <div class="messages" style="position:relative">';
	$html .= "      ".$this->getContent();
	$html .= '     </div>';
	//$html .= '</div>';
	$html .= '</div>';
	$html .= '<div class="spacerblock_20"></div>';
      }


      $output = $html;

      if($this->button_onclick_display_block === true){
	  $hidden_block = '';
	  $hidden_block .= '<div id="'.$this->hidden_div_id.'" style="display:none;"><br />';
	  $output = $button_ui.$hidden_block . $output . '</div>';
      }

      if ($this->isActive()) {
        echo $output;
      }
    }


    /**
      * Method to get the content
    */
    public function getContent(){
      return $this->block_content;
    }

    /**
      * Method to process the block
      * Child class will override the method as per need
    */
    public function processBlock() {}

    /**
      * Method checking if the block is active to be displayed
    */
    public function isActive(){
      return $this->is_active;
    }
    
    /**
      * Set if the block is active and should be display or not
      * @param boolean true or false value.
      */
      
    public function setIsActive($bool_active) {
      $this->is_active = $bool_active; 
    }
    public function setActive($bool_active) {
      $this->setIsActive($bool_active);
    }
    
    /**
      * Function to set a button on the block 
    */
    function setButton(){
      $button = new DynamicButton();
      $html = $button->CreateButton($this->button_url, $this->button_name, $this->button_div_id, $this->call_back_method, $this->button_icon, 'margin: 0 0 10px 25px;');
      return $html;
    }

    /**
      * Function to set on click display if button is attached 
      * with an onlick show/hide operation
      * @param string $button_name
      * @param string $divid , the div that is hidden by default
      * @param string $button_url
      * @param string $call_back_method name with "()"
      * @param string $button_div_id
      * @param string $button_icon
    */
    function setButtonOnClickDisplayBlock($button_name,$divid='',$button_url='',$call_back_method='',$button_div_id='',$button_icon=''){
        $this->button_onclick_display_block = true;
        if(!empty($button_name)){ $this->button_name = $button_name; }
        if(!empty($divid)){ $this->hidden_div_id = $divid; }
        if(!empty($button_url)) { $this->button_url = $button_url; }
        if(!empty($call_back_method)) { $this->call_back_method = $call_back_method; }
        if(!empty($button_div_id)) { $this->button_div_id = $button_div_id; }
        if(!empty($button_icon)) { $this->button_icon = $button_icon; }
    }

    /**
      * Hide the content if not required to be shown, useful in case of just displaying the button.
    */
    function hideContent(){
        $this->show_content = false ;
    }

    /**
      * To get the short desctiption of the plugin
      * @return short_description
    */
    function getShortDescription(){
        return $this->short_description ;
    }

    /**
      * To get the long desctiption of the plugin
      * @return long_description
    */
    function getLongDescription(){
        return $this->long_description ;
    }

}

?>
