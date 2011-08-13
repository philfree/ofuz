<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com

  /**
	* WorkFeedItem base class
	* 
	* It as a general addFeed method that call the WorkFeed
	* object of serialization of it self.
	* the date_added variable is set automaticaly with a timestamp
	* and can be used in the display() method.
	* The addFeed should not be overide but called from a method that
	* prepared the content of the variable of the WorkFeed.
	*
	* @author SQLFusion's Dream Team <info@sqlfusion.com>
	* @package WorkFeed
	* @license ##License##
	* @version 0.6
	* @date 2010-09-04
	* @since 0.6
    */

class WorkFeedItem {

	private $date_added;

    function addFeed($users=null){
        $do_workfeed = new WorkFeed();
        if ($users == null) {
            $do_workfeed->addFeed($this);
        } else {
            $do_workfeed->addFeed($this,$users);
        }
    }
	
	function display() {}
	function eventAddFeed(EventControler $evtcl) {}
	function prepareFeedContent() {}
    
}
