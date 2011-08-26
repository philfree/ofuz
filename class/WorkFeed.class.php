<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
// Copyrights 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com

   /**  
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package WorkFeed
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
    */

class WorkFeed extends DataObject {
    
    public $table = "workfeed";
    protected $primary_key = "idworkfeed";
    public $sql_view_limit = 50;
    public $sql_qry_start = 0;
    public $sql_qry_end = 50;
    public $feed_count = 0;

    /**
    * Serialize the feed object and store it 
    * in a database table (workfeed).
    * The addFeed will dispatch a feed element to all the users concerned about it.
    * @param WorkFeedItem based object to serialize and save.
    * @param users array of user for which that feed item is targeted
    */
    
    function addFeed($obj,$users=null){
        $obj->date_added = time();
        $serializedata = serialize($obj);
        if($users == null) { 
            $users = Array($_SESSION['do_User']->iduser); 
        }
        foreach ($users as $iduser) {
            $this->addNew();
            $this->feed_type = get_class($obj);
            /*if (get_magic_quotes_gpc()) {
                $data = addslashes($serializedata);
            }else{
                $data = $serializedata ;
            }*/
            $data = mysql_real_escape_string($serializedata);  
            $this->feed_data = $data;
            $this->iduser = $iduser;
            $this->date_added = date("Y-m-d H:i:s");
            $this->add();
        }
    }

    /**
      * Display the user feed.
    */
    function displayUserFeeds(){
        if($this->getUserFeeds()!== false){
            while($this->next()){
                /*if (!get_magic_quotes_gpc()) {
                    $feed_data = unserialize(stripslashes($this->feed_data));
                }else{
                    $feed_data = unserialize($this->feed_data);
                }*/
                //$feed_data = unserialize($this->feed_data);
                $feed_data = unserialize(stripslashes($this->feed_data));
                if (is_object($feed_data)) { 
                  echo $feed_data->display();
                } else {
                  $this->setError(" On Unserialize WorkFeed of type: ".$this->feed_type." id:".$this->getPrimaryKeyValue());
                }
            } 
        }
    }

    function getUserFeeds(){
		/**
        $do_user_relation = new UserRelations();
        $do_user_relation->getAllCoWorker();
        $do_projects = new Project();
        $do_projects->getProjectsAsCoWorkers();
        $project_co_worker = $do_projects->getOtherUserAsProjectCoworker();
        $co_workers = array();
        $all_permitted_user_array = array();
        if($do_user_relation->getNumRows()){
            while($do_user_relation->next()){
              $co_workers[] = $do_user_relation->idcoworker;
            }
        }

        if(is_array($co_workers) && is_array($project_co_worker)){
            $all_permitted_user_array = array_merge($project_co_worker, $co_workers);
            $all_permitted_user_array = array_unique($all_permitted_user_array);
        }elseif(is_array($project_co_worker)){
              $all_permitted_user_array = array_unique($all_permitted_user_array);
        }
        
        if(count($all_permitted_user_array) > 0 ){
               $this->query("select * from ".$this->table." 
                        where iduser IN (".implode(",",$all_permitted_user_array).") 
                        order by date_added desc limit 0,50
                      ");
        }else{ return false; }
		**/
		$this->query("select * from ".$this->table." 
                        where iduser = ".$_SESSION['do_User']->iduser." 
                        order by date_added desc limit {$this->sql_qry_start},{$this->sql_view_limit}");
		if ($this->getNumRows() == 0) { return false; } else { return true; };
    }

     /**
      * Display method for feed activity date
      * @param $date_timestamp -- the activity date timestamp
      * @return the formated date for the feed.
     */
     function showFeedActivityDate($date_timestamp){
	  $date = date("Y-m-d H:i:s",$date_timestamp);
	  OfuzUtilsi18n::formatDateLong($date,true);
     }

    /**
      * Get the count of workfeed
      * @return Number of workfeeds
    */
    function getWorkfeedCount() {
	    $this->query("select * from ".$this->table." 
		    where iduser = ".$_SESSION['do_User']->iduser." 
		    order by date_added desc");
	    $this->feed_count = $this->getNumRows();
    }

    /**
      * Autoload method to get the workfeed from the last one.
    */
    function autoLoadWorkfeedOnScrollDown() {
	    if($_SESSION['do_work_feed']->feed_count >= $_SESSION['do_work_feed']->sql_qry_start) {
		    $_SESSION['do_work_feed']->sql_qry_start = $_SESSION['do_work_feed']->sql_qry_start + $_SESSION['do_work_feed']->sql_view_limit;
		    $_SESSION['do_work_feed']->sql_qry_end = $_SESSION['do_work_feed']->sql_qry_end + $_SESSION['do_work_feed']->sql_view_limit;
		    $this->displayUserFeeds();
	    }
    }


}
?>
