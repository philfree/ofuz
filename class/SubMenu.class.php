<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
  * SubMenu class
  * Simple Class to manage the Menu links, instead of using an Array.
  *
  * @author SQLFusion's Second Dream Team <info@sqlfusion.com>
  * @package OfuzCore
  * @license GNU Affero General Public License
  * @version 0.6
  * @date 2010-09-03
  * @since 0.6
  */
  
  
class SubMenu extends BaseObject {

   protected $menu_items = Array();

   public function addMenuItem($label, $url, $index=null) {
     if ($index != null) {
       $this->menu_items[$index] = Array('label' => $label, 'url' => $url, 'type'=>'link') ;
     } else {
       $this->menu_items[] = Array('label' => $label, 'url' => $url, 'type' => 'link') ;
     }
     return $this;
   }
   
   public function getMenuItems() {
      return $this->menu_items;
   }
   /**
    * addMenuItemPHPCallback
    * This will generate a link from a php function or method call.
    */
   public function addMenuItemPHPCallback($label, $function_name, $class_name='') {
	   $this->menu_items[] = Array('label' => $label, 'function_name' => $function_name, 'type' => 'php-callback', 'class_name' => $class_name);
	   return $this;
   }
   /** 
    * addMenuItemJSCallback 
    * Create a link to trigger a javascripts functions
    * @param string label lable of the link.
    * @param string function_name name of the javascript function with parameters.
    */
   public function addMenuItemJSCallback($label, $function_name) {
	   $this->menu_items[] = Array('label' => $label, 'function_name' => $function_name, 'type' => 'js-callback');
	   return $this;
   }  
   /**
    * Return the HTML to display the menu
      <?php echo $link_html;?><?php echo _('Upcoming');?><span class="headerlinksI">|</span><a href="tasks_completed.php"><?php echo _('Completed');?></a></span>
          
    */
    
   
   public function getMenu() {
      $html = '<span class="headerlinks">';
      foreach ($this->getMenuItems() as $menu_item) {
		switch ($menu_item['type']) {		
		   case 'link':
      	      $html .= '<a href="'.$menu_item['url'].'">'.$menu_item['label'].'</a><span class="headerlinksI">|</span>';
      	      break;
      	   case 'php-callback':
      	      break;
      	   case 'js-callback':
      	      $html .= '<a href="#" onClick="'.$menu_item['function_name'].'; return false;">'.$menu_item['label'].'</a><span class="headerlinksI">|</span>';
      	      break;
      	   default;     
       	      $html .= '<a href="'.$menu_item['url'].'">'.$menu_item['label'].'</a><span class="headerlinksI">|</span>';
      	      break;     	   
		} 
		
      }
      $html .= '</span>';
      return $html;
   }

}
?>
