<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Block object to search Co-Worker
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
 

class CoworkerListInvitations extends BaseBlock{
      public $short_description = 'Coworkers invited block';
      public $long_description = 'List the co-workers invited and not yet accepted the invitation.';
    
      /**
	    * processBlock() , This method must be added  
	    * Required to set the Block Title and The Block Content Followed by displayBlock()
	    * Must extend BaseBlock
        */

      function processBlock(){

          $this->setTitle(_('Invitations Pending'));
          $this->setContent($this->generateCoworkerInvitationList());
          $this->displayBlock();

      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/UserRelations.class.php
      */

      function generateCoworkerInvitationList(){

          $output = '';
          $_SESSION['do_coworker']->getAllRequestsSent();
          if($_SESSION['do_coworker']->getNumrows()){
              $count = 0;
              while ($_SESSION['do_coworker']->next()) {
                  $e_remove_invitation =  new Event("do_coworker->eventRemoveInvitation");
                  $e_remove_invitation->addParam('id',$_SESSION['do_coworker']->iduser_relations);
                  $e_remove_invitation->addParam("goto",$_SERVER['PHP_SELF']);
              
                  $output .= '<div class="co_worker_item"><div class="co_worker_desc">' ;
                  $output .= '<div id="invite'.$count.'" class="co_worker_item co_worker_desc">'; 
                  $output .= '<div style="position: relative;">';
                  if( $_SESSION['do_coworker']->idcoworker ){
                      $output .= $_SESSION['do_User']->getFullName($_SESSION['do_coworker']->idcoworker);
                  } else {
                      $output .= $_SESSION['do_coworker']->decrypt($_SESSION['do_coworker']->enc_email);
                  }
                  $img_del = '<img src="/images/delete.gif" width="14px" height="14px" alt="" />';
                  $output .= '<div width="15px" id="trashcan'.$count.'" class="deletenote" style="right:0;">'.$e_remove_invitation->getLink($img_del).'</div>';
                  $output .= '</div></div>';
                  $output .= '</div></div>';
              }
              return $output;
          }else{
               $this->setIsActive(false);
          }
          

      }

}

?>
