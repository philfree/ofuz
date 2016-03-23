<?php
// Copyright 2010 SQLFusion LLC  info@sqlfusion.com
// All rights reserved
/**COPYRIGHTS**/
/**
  * A Timesheet Block plugin class
  * The class must extends the BaseBlock
  * setTitle() will set the Block Title
  * setContent() will set the content
  * displayBlock() call will display the block
  * isActive() is set to true by default so to inactivate the block uncomment the method isActive();
  * @package TimesheetPlugIn
  * @author varaprasad <varaprasad@htmlfusion.com>
  * @license ##License##
  * @version 0.1
  * @date 2016-03-20
  */


class BlockTimesheets extends BaseBlock{
      public $short_description = 'Sample Plugin block';
      public $long_description = 'Sample plugin block';
    
       /**
        * processBlock() , This method must be added  
        * Required to set the Block Title and The Block Content Followed by displayBlock()
        * Must extent BaseBlock
        */
      function processBlock(){
        //$this->setTitle("Weather In Los Angeles");
        $this->setContent($this->generateTimeEntryBlock());
        //$this->setContent("The Weather here is pretty cool now a days !!");
        $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content 
       * @return string : HTML form
       * @see class/TimesheetsPlugIn.class.php
      */
      function generateTimeEntryBlock(){
        
        $output = '';
        $output .= '<div class="percent95">';
        $do_entry_add = new TimesheetsPlugIn();
        $output .= $do_entry_add->getTimeEntryAddForm($next_page);
        $output .= '</div>';
        return $output;
      }
}

?>
