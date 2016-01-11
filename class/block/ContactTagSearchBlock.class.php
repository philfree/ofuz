<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

/**
  * A ContactTagSearchBlock
  * contact.php has persistent session object as $_SESSION['ContactEditSave']
  * So for contact related data can be retrieve from this object
  * This is set a block on the left side of contact.php with contact details
  * Little complex than what we have on the other test Example Weather Object 
  * It also has 2 extra params in setContent() i.e. url_path and url_name
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ContactTagSearchBlock extends BaseBlock{
    public $short_description = 'Contact Tag search block';
    public $long_description = 'Search contacts by tags';
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */
      function processBlock(){
	  $this->setTitle(_('Search by tags'));
	  $this->setContent($this->generateTagList());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generateTagList(){
	    $output = '';
	    
        $SearchTags = $_SESSION['do_Contacts']->getSearchTags();
        if (!empty($SearchTags)) {
            echo '<br />';
        }

        $GLOBALS['do_tag_list'] = new Tag();
        $output = $GLOBALS['do_tag_list']->getUserTagList('contact');
	    return $output;
      }
   
}

?>