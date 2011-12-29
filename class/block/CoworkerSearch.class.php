<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Block object to search Co-Worker
     * calls the method: $_SESSION['do_coworker']->generateFromAddCoWorker()
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license GNU Affero General Public License
     * @version 0.6.1
     * @date 2010-09-06
     * @since 0.6.1
     * 
     */
 
class CoworkerSearch extends BaseBlock{
      public $short_description = 'Co-Workers search block';
      public $long_description = 'Search co-workers by first name or last name and add them as co-worker';
    
      /**
	    * processBlock() , This method must be added  
	    * Required to set the Block Title and The Block Content Followed by displayBlock()
	    * Must extend BaseBlock
        */

      function processBlock(){

          $this->setTitle(_('Find Co-Workers'));
          $this->setContent($this->generateCoworkerSearchForm());
          $this->displayBlock();

      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/UserRelations.class.php
      */

      function generateCoworkerSearchForm(){

          $output = '<div>'._('Search for Co-Workers by first or last name:').'</div>';
          if(!is_object($_SESSION['do_User_search'])) {
                $do_User_search = new User();
                $do_User_search->sessionPersistent("do_User_search", "logout.php", OFUZ_TTL);
          }
          $e_search = new Event("do_User_search->eventSetSearchByName");
          $e_search->setLevel(500);
          $e_search->addParam("goto", "co_worker_search.php");
          $output .= $e_search->getFormHeader();
          $output .= $e_search->getFormEvent();
          $output .='<div class="marginform">
                      <input type="Text" name="search_txt" id="search_txt" value="">
                   </div>
                    <div class="dottedline"></div>
                    <div class="section20">
                      <input type="submit" value="Search" />
                    </div>
            </div></form>';
          return $output;

      }

}

?>
