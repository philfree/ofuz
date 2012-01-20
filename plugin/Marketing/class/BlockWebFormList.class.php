<?php
// Copyright 2010 SQLFusion LLC  info@sqlfusion.com
// All rights reserved
/**COPYRIGHTS**/
/**
  * An Marketing Block plugin class
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


class BlockWebFormList extends BaseBlock{
      public $short_description = 'WebForms List';
      public $long_description = 'WebForms List';
    
       /**
        * processBlock() , This method must be added  
        * Required to set the Block Title and The Block Content Followed by displayBlock()
        * Must extent BaseBlock
        */
      function processBlock(){
        $this->setTitle("My WebForms");
        
        $content = $this->getWebFormList();
       
        $this->setContent($content);
        $this->displayBlock();
      }
      
      function getWebFormList() {
		    $do_userform = new WebFormUser();
		    $do_userform->getUsersWebForms();
		    $output = '';
		    if (isset($GLOBALS['plugin_item_value'])) { $up = '../'; } else { $up = ''; }
            if($do_userform->getNumRows()){
                $count = 0;
				while($do_userform->next()){
					$e_remove_wf =  new Event("do_userform->eventDeleteWebForm");
					$e_remove_wf->addParam('id',$do_userform->idwebformuser);
					$e_remove_wf->addParam("goto",$_SERVER['PHP_SELF']);
					$count++;
					$output .= '<div id="webform'. $count . '" class="co_worker_item co_worker_desc">'; 
					$output .=  '<div style="position: relative;">';
					$output .=  '<a href="'.$up.'WebForm/'.$do_userform->idwebformuser.'">'.$do_userform->title.'</a>';
					$img_del = '<img src="/images/delete.gif" width="14px" height="14px" alt="" />';
					$output .=  '<div width="15px" id="trashcan'. $count. '" class="deletenote" style="right:0;">'.$e_remove_wf->getLink($img_del).'</div>';
					$output .=  '</div></div>';
                }
	         } else {
				$this->setIsActive(false); 
			 }
			 return $output;
	 }
}

?>
