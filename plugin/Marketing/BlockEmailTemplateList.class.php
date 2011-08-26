<?php
// Copyright 2010 SQLFusion LLC  info@sqlfusion.com
// All rights reserved
/**COPYRIGHTS**/
/**
  * An BlockEmailTemplate List
  * Display the users email template in a block for editing.
  * The class must extends the BaseBlock
  * setTitle() will set the Block Title
  * setContent() will set the content
  * displayBlock() call will display the block
  * isActive() is set to true by default so to inactivate the block uncomment the method isActive();
  * @package Marketing
  * @author Philippe Lewicki <phil@sqlfusion.com>
  * @license ##License##
  * @version 0.1
  * @date 2010-11-08
  */


class BlockEmailTemplateList extends BaseBlock{
      public $short_description = 'Email Template List';
      public $long_description = 'Email Tempalte List';
    
       /**
        * processBlock() , This method must be added  
        * Required to set the Block Title and The Block Content Followed by displayBlock()
        * Must extent BaseBlock
        */
      function processBlock(){
        $this->setTitle("My Email Templates");
        
        $content = $this->getEmailTemplateList();
        
        $this->setContent($content);
        $this->displayBlock();
      }
      function getEmailTemplateList() {
		   $user_email_templates = new EmailTemplateUser("blank");
           $user_email_templates->getUserSavedEmailTemplates();
           $output = '';
            if($user_email_templates->getNumRows()){				
                $count = 0;
                    while($user_email_templates->next()){
                        $e_remove_etml =  new Event("do_user_email_teml->eventDeleteUserEmailTmpl");
                        $e_remove_etml->addParam('id',$user_email_templates->idemailtemplate_user);
                        $e_remove_etml->addParam("goto",$_SERVER['PHP_SELF']);
                        $count++;
                        $output .= '<div id="templt'. $count. '" class="co_worker_item co_worker_desc">'; 
                        $output .=  '<div style="position: relative;">';
                        $output .=  '<a href="'.$GLOBALS['cfg_plugin_mkt_path'].'MEmailTemplate/'.$user_email_templates->idemailtemplate_user.'">'.$user_email_templates->name.'</a>';
                        $img_del = '<img src="/images/delete.gif" width="14px" height="14px" alt="Delete" />';
                        $output .=  '<div width="15px" id="trashcan'. $count. '" class="deletenote" style="right:0;">'.$e_remove_etml->getLink($img_del, ' title="'._('Delete').'"').'</div>';
                        $output .=  '</div></div>';
                    }
             } else {
				$this->setIsActive(false);  
			 } 
			 return $output;    
		}

}

