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


class BlockMarketing extends BaseBlock{
      public $short_description = 'Marketing Block';
      public $long_description = 'Marketing Block';
    
       /**
        * processBlock() , This method must be added  
        * Required to set the Block Title and The Block Content Followed by displayBlock()
        * Must extent BaseBlock
        */
      function processBlock(){
        $this->setTitle("Marketing");
        
        $content = '<a href="/Tab/Marketing/WebForm">WebForms</a><br/><a href="/Tab/Marketing/AutoResponder">Auto Responders</a><br/><a href="/Tab/Marketing/MEmailTemplate">Email Template</a>';
        
        $this->setContent($content);
        $this->displayBlock();
      }
}

?>
