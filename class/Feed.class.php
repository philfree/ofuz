<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
/**
  * Feed class
  * with all the usefull functions.
  * @author Ravi Rokkam <ravi@sqlfusion.com>
  */
/**
  * @see Zend_Loader
  */
require_once 'Zend/Loader.php';
/**
  * @see Zend_Feed
  */
include_once 'Zend/Feed.php';
include_once 'Zend/Feed/Rss.php';

class Feed extends DataObject {

    public $num_feeds = "";

    function eventAjaxNumOfFeedsInWebPage(EventControler $evctl){
		$do_contact_website = new ContactWebsite();
		$do_contact_website->getId($evctl->idcontact_website);
		$website_type = $do_contact_website->website_type;
		$uri = $do_contact_website->website;
		if($website_type == "RSS Feed") {
			//Feed from xml
			$feed = Zend_Feed::import($uri);
			$feed_count = $feed->count();
			$this->updateFeedStatus($feed_count, $evctl->idcontact_website);
			$evctl->addOutputValue($feed_count);
		} else {
			//Feed from webpage
			$uri = (substr(ltrim($uri), 0, 7) != 'http://' ? 'http://' : '') . $uri;
			$arr_feeds = Zend_Feed::findFeeds($uri);
			$this->num_feeds = count($arr_feeds);
			$this->updateFeedStatus($this->num_feeds, $evctl->idcontact_website);
			$evctl->addOutputValue(count($arr_feeds));
		}
    }
/*
    function eventAjaxNumOfFeedsInWebPage(EventControler $evctl){
		$idcontact_website = $evctl->idcontact_website;
		$uri = $this->getWebsiteName($idcontact_website);
		//$file_extension = $this->getFileExtension($uri);
		$is_xml_feed = @simplexml_load_file($uri);

		if($is_xml_feed===FALSE) {
			$uri = (substr(ltrim($uri), 0, 7) != 'http://' ? 'http://' : '') . $uri;
			$arr_feeds = Zend_Feed::findFeeds($uri);
			$this->num_feeds = count($arr_feeds);
			$this->updateFeedStatus($this->num_feeds, $idcontact_website);
			$evctl->addOutputValue(count($arr_feeds));
		}
    }
*/
    function retrieveFirstItem($uri, $idcontact_website=null){

		$do_contact_website = new ContactWebsite();
		$do_contact_website->getId($idcontact_website);
		$website_type = $do_contact_website->website_type;
		if($website_type == "RSS Feed") {
			try {
				$feed = new Zend_Feed_Rss($uri);
				$entry = $feed->current();
				if(trim($entry->title()) == trim($entry->description())){
					$str_item .= $entry->description() . "<br />";
				} else{
					$str_item = "<B>".$entry->title() . "</B><br />";
					$str_item .= $entry->description() . "<br />";
				}
				$str_item .= " ".date("D, j M Y H:i:s", strtotime($entry->pubDate())) . "<br />";
				$arr_item[0] = $str_item;
				$arr_item[1] = strtotime($entry->pubDate());
				return $arr_item;
			} catch (Exception $ex) {
				$this->turnFeedOff($idcontact_website);
			}
		} else {
			try {
				$obj_arr_feeds = Zend_Feed::findFeeds($uri);
				foreach ($obj_arr_feeds as $channel) {
					if($channel->item){
						//fetches the first (current) updated item
						$item = $channel->current();
						if(trim($item->title()) == trim($item->description())){
							$str_item .= $item->description() . "<br />";
						} else{
							$str_item = "<B>".$item->title() . "</B><br />";
							$str_item .= $item->description() . "<br />";
						}
						$str_item .= " ".date("D, j M Y H:i:s", strtotime($item->pubDate())) . "<br />";
						$arr_item[0] = $str_item;
						$arr_item[1] = strtotime($item->pubDate());
						
						break;
					}
				}
				return $arr_item;
			} catch (Exception $ex) {
				$this->turnFeedOff($idcontact_website);
			}
		}
    }

    function retrieveSinceLastFetch($uri, $idcontact_website=null){
        $do_website =  new ContactWebsite();
		$do_website->getid($idcontact_website);
		$last_fetch_time = $do_website->feed_last_fetch;
		$website_type = $do_website->website_type;

		if($website_type == "RSS Feed") {
			try {
				$feed = new Zend_Feed_Rss($uri);
				$arr_item = Array();
				$i=0;
				foreach($feed as $entry) {
					if(trim($entry->title()) == trim($entry->description())){
						$str_item .= $entry->description() . "<br />";
					} else{
						$str_item = "<B>".$entry->title() . "</B><br />";
						$str_item .= $entry->description() . "<br />";
					}
					$str_item .= " ".date("D, j M Y H:i:s", strtotime($entry->pubDate())) . "<br />";
					$arr_item[$i][0] = $str_item;
					$arr_item[$i][1] = strtotime($entry->pubDate());
					$i++;
					if ($last_fetch_time > strtotime($entry->pubDate())) { 
						break;
					}
				}
			
				return $arr_item;
			} catch (Exception $ex) {
				$this->turnFeedOff($idcontact_website);
			}
		} else {
			try {
				$obj_arr_feeds = Zend_Feed::findFeeds($uri);
				$arr_item = Array();
				$i=0;
				foreach ($obj_arr_feeds as $channel) {
					if($channel->item){
						//fetches the first (current) updated item
						$item = $channel->current();
						if(trim($item->title()) == trim($item->description())){
							$str_item .= $item->description() . "<br />";
						} else{
							$str_item = "<B>".$item->title() . "</B><br />";
							$str_item .= $item->description() . "<br />";
						}
						$str_item .= " ".date("D, j M Y H:i:s", strtotime($item->pubDate())) . "<br />";
						$arr_item[$i][0] = $str_item;
						$arr_item[$i][1] = strtotime($item->pubDate());
						$i++;
						if ($last_fetch_time > strtotime($item->pubDate())) { 
							break;
						}
					}
				}
				return $arr_item;
			} catch (Exception $ex) {
				$this->turnFeedOff($idcontact_website);
			}
		}
    }	

    function getWebsiteName($idcontact_website){
            $do_contact_website = new ContactWebsite();
            $do_contact_website->getId($idcontact_website);
            return $do_contact_website->website;
    }
 
    //updates auto fetch 
    function updateFeedStatus($num_feeds, $idcontact_website){
        $do_contact_website = new ContactWebsite();
        $do_contact_website->getId($idcontact_website);
        if($num_feeds == 0){
            $do_contact_website->feed_auto_fetch = 'None';
        } else{
            if($do_contact_website->feed_auto_fetch == 'None'){
                $do_contact_website->feed_auto_fetch = 'No';
            }
        }
        $do_contact_website->update();
    }

	function turnFeedOff($idcontact_website) {
        $do_contact_website = new ContactWebsite();
        $do_contact_website->getId($idcontact_website);
        $do_contact_website->feed_auto_fetch = 'No';
        $do_contact_website->update();
	}

	function getFileExtension($filename) {
		$path_info = pathinfo($filename);
		if($path_info) {
			return $path_info['extension'];
		}
	}
}

?>
