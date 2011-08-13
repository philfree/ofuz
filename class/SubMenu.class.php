<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/

/**
  * SubMenu class
  * Simple Class to manage the Menu links, instead of using an Array.
  *
  * @author SQLFusion's Second Dream Team <info@sqlfusion.com>
  * @package OfuzCore
  * @license ##License##
  * @version 0.6
  * @date 2010-09-03
  * @since 0.6
  */
  
  
class SubMenu extends BaseObject {

   protected $menu_items = Array();

   public function addMenuItem($label, $url, $index=null) {
     if ($index != null) {
       $this->menu_items[$index] = Array('label' => $label, 'url' => $url) ;
     } else {
       $this->menu_items[] = Array('label' => $label, 'url' => $url) ;
     }
     return $this;
   }
   
   public function getMenuItems() {
      return $this->menu_items;
   }
   
   /**
    * Return the HTML to display the menu
      <?php echo $link_html;?><?php echo _('Upcoming');?><span class="headerlinksI">|</span><a href="tasks_completed.php"><?php echo _('Completed');?></a></span>
          
    */
    
   
   public function getMenu() {
      $html = '<span class="headerlinks">';
      foreach ($this->getMenuItems() as $menu_item) {
      	$html .= '<a href="'.$menu_item['url'].'">'.$menu_item['label'].'</a><span class="headerlinksI">|</span>';
      }
      $html .= '</span>';
      return $html;
   }

}
?>