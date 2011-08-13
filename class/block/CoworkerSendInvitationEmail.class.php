<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Block object to send email invitation to Co-Worker
     * calls the method: $_SESSION['do_coworker']->generateFromAddCoWorker()
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license ##License##
     * @version 0.6.1
     * @date 2010-09-06
     * @since 0.6.1
     * 
     */
 

class CoworkerSendInvitationEmail extends BaseBlock{
    
      public $short_description = 'Co-Workers send Email Invitation block';
      public $long_description = 'Send email invitation to a co-worker to join ofuz and work as worker';

      /**
	    * processBlock() , This method must be added  
	    * Required to set the Block Title and The Block Content Followed by displayBlock()
	    * Must extend BaseBlock
        */

      function processBlock(){

          $this->setTitle(_('Invite a Co-Worker'));
          $this->setContent($this->generateFormEmailInvitation());
          $this->displayBlock();

      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/ProjectTask.class.php
      */

      function generateFormEmailInvitation(){

          $output = '';

          $output .= $_SESSION['do_coworker']->generateFromAddCoWorker($_SERVER['PHP_SELF'],true);

          return $output;

      }

}

?>
