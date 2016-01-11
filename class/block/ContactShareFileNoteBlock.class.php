<?php
// Copyright SQLFusion LLC, all rights reserved
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Display just a button to link on the contact_share_settings.php page.
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license GNU Affero General Public License
     * @version 0.6
     * @date 2010-09-06
     * @since 0.6
     */

class ContactShareFileNoteBlock extends BaseBlock{
      public $short_description = 'Share files note with Contact block';
      public $long_description = 'Share the files and note with the contact using the contact portal';
    
      /**
	    * processBlock() , This method must be added  
	    * Required to set the Block Title and The Block Content Followed by displayBlock()
	    * Must extend BaseBlock
        */
      function processBlock(){
	      $this->setButtonOnClickDisplayBlock(_('share files and notes'),'','/contact_share_settings.php','','','dyn_button_share_this');
	      $this->hideContent();
	      $this->displayBlock();
      }     
     
}

?>
