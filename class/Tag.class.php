<?php 

// Copyrights 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


    /**
     * Tag class
     * Using the DataObject
     * @author Abhik Chakraborty, SQLFusion LLC info@sqlfusion.com
+----------------+--------------+------+-----+---------+----------------+
| Field          | Type         | Null | Key | Default | Extra          |
+----------------+--------------+------+-----+---------+----------------+
| idtag          | int(10)      | NO   | PRI | NULL    | auto_increment | 
| tag_name       | varchar(200) | NO   | MUL |         |                | 
| iduser         | int(10)      | NO   |     |         |                | 
| reference_type | varchar(50)  | NO   | MUL |         |                | 
| idreference    | int(15)      | NO   |     |         |                | 
| date_added     | date         | NO   |     |         |                | 
+----------------+--------------+------+-----+---------+----------------+
   * 
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @package OfuzCore
   * @license ##License##
   * @version 0.6
   * @date 2011-05-13
   * @since 0.1
    */

class Tag extends DataObject {
    
    public $table = "tag";
    protected $primary_key = "idtag";
    public $default_reference = "contact";

    function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_TAG);
		}
    } 

    /**
    * While adding a tag first it will check if the same
    * name is there in the tag table or not. IF there return
    * false else enter and return the inserted id
    */

    function addNewTag($tag_name){
      $tag_id = $this->isTagExists($tag_name); 
      if(!$tag_id){
        $this->tag_name = $tag_name;
        $this->add();
        $last_inserted_id = $this->getPrimaryKeyValue();
        return $last_inserted_id;
      }else{
        return $tag_id;
      }
    }

    /**
    * Get the Tag name and check if that is already in the tag table
    * If that tag is there in the tag table then return the tag id else
    * return false.
    */

    function isTagExists($tag_name){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from tag where tag_name = '".$tag_name."'");
      if($q->getNumRows() > 0){
        $q->fetch();
        return $q->getData("idtag");
      }else{
        return false;
      }
    } 

    
    /**
      * Function to check if the idtag is valid and exists 
      * @param string $idtag
      * @return boolean
    */
    function isTagValidTagId($idtag){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from tag where idtag = ".$idtag);
      if($q->getNumRows() > 0){
        return true;
      }else{
        return false;
      }
    }

    /**
    * Function get all tag names from a referrer 
    * @param idreference
    * @param iduser
    * @returns Array of tag names if there are tags else return false
    */

    function getAllTagNamesForReferer($idreference = 0,$iduser = 0 ){
      $q = new sqlQuery($this->getDbCon());
      if($iduser == 0 ){
        $q->query("select * from tag where idreference = ".$idreference);
            }else{
        $q->query("select * from tag where idreference = ".$idreference." AND iduser = ".$iduser );
      }
      $data = array();
      if($q->getNumRows()){
        while($q->fetch()){
            $data[] = $q->getData("tag_name");
        }
        return $data;
      }else{
        return false;
      }
    }

    function isTagExistsForReferer($tag_name,$idreference,$iduser = "",$reference_type=""){
      if($iduser == ""){ $iduser = $_SESSION['do_User']->iduser; }
      if (empty($reference_type)) { $reference_type = $this->default_reference; }
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from tag where tag_name = '".$tag_name."' 
                    AND idreference = ".$idreference." 
                    AND iduser =".$iduser."
                    AND reference_type = '".$reference_type."'");
//       echo "select * from tag where tag_name = '".$tag_name."' 
//                     AND idreference = ".$idreference." 
//                     AND iduser =".$iduser."
//                     AND reference_type = '".$reference_type."'";exit;
      if($q->getNumRows() > 0){
        $q->fetch();
        return $q->getData("idtag");
      }else{
        return false;
      }
    }

    function isTagOwner($idtag){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from tag where idtag = ".$idtag." AND iduser=".$_SESSION['do_User']->iduser); 
      if($q->getNumRows() > 0){
          return true;
      }else{
          return false;
      }
    }

    function getTagByName($tag_name) {
      $sql = "SELECT * FROM tag WHERE tag_name = '".$this->quote($tag_name)."'";
      if (is_object($_SESSION['do_User'])) {
              $sql .= " AND iduser=".$_SESSION['do_User']->iduser;
      }
      $this->query($sql);
      //return $this->idtag;
    }

   

    /**
     * While adding a tag association just check if it is 
     * already there. IF not then add that.
     */

    function addTagAssociation($idreference,$tag_name="",$reference_type="",$iduser=0){
        if (empty($iduser)) { $iduser = $_SESSION['do_User']->iduser; }
        if (empty($reference_type)) { $reference_type = $this->default_reference; }
        $tag_name = trim($tag_name);
        if(!empty($tag_name)){
            if(!$this->isTagAssociationExists($tag_name,$idreference, $reference_type, $iduser)){
                if ($reference_type=="contact") { 
                  $activity = new Activity();
                  $activity->idcontact= $idreference;
                  $activity->update();
                }
                $this->addNew();
                $this->iduser = $iduser;
                $this->tag_name = $tag_name;
                $this->reference_type = $reference_type;
                $this->idreference = $idreference;
                $this->date_added = date("Y-m-d");
                $this->add();
                $this->calculateTagSize();
            }
        }
    }

    /**
     * eventAjaxAddTagAssociation
     * ajax eventaction to add a tag to a contact using an ajax request 
     * with tag name and idcontact as parameters.
     * it returns the idtag of the newly inserted tag.
     */

    function eventAjaxAddTagAssociation(EventControler $event_controler) {
        $idreference = $event_controler->idcontact;
        $tag_name = trim($event_controler->tag_name);
        
        if(!empty($tag_name)){
            $reference_type = "contact";
            $iduser = $_SESSION['iduser']->iduser;
            $this->addTagAssociationShared($idreference,$tag_name,$reference_type,$iduser);
            if(!$this->isTagAssociationExists($tag_name,$idreference, $reference_type, $iduser)){
                $this->addTagAssociation($idreference, $tag_name, $reference_type, $iduser);
                $event_controler->addOutputValue( $this->getInsertId($this->getTable(), $this->getPrimaryKey()) );
            } else { $event_controler->addOutputValue(0); }
        }
        $_SESSION['last_tag_refresh'] = true;
    }




    /**
      * Function to add tags for the shared contacts
      * Usage : Adding tag to contact which is shared between many co-workers
      * @param integer $idreference
      * @param string $tag_name
      * @param string $reference_type
      * @param integer iduser
    */
    function addTagAssociationShared($idreference,$tag_name="",$reference_type="",$iduser=0){
          if($reference_type == "") { $reference_type = "contact" ; }
          if($iduser == 0){$iduser == $_SESSION['iduser']->iduser;}
          if(!empty($tag_name)){
              $idusers = array();
              $do_contact_view = new ContactView();
              $do_contact_sharing = new ContactSharing() ;
              $shared_co_workers = $do_contact_sharing->selectAllUsersFromContactSharing($idreference);
              if( $shared_co_workers!== false ){
                  foreach($shared_co_workers as $iduser){
                      if($_SESSION['iduser']->iduser != $iduser)
                          $idusers[] = $iduser ;
                  }
                  foreach($idusers as $iduser){
                      $reference_type = "contact";
                      if(!$this->isTagAssociationExists($tag_name,$idreference, $reference_type, $iduser)){
                          $this->addTagAssociation($idreference, $tag_name, $reference_type, $iduser);
                          $do_contact_view->setUser($iduser);
                          $do_contact_view->addTag($tag_name,$idreference);
                      } 
                  }    
              }
          }
    }


    /**
     * check if the tag association is already there or not. If yes
     * return true else return false.
     */

    function isTagAssociationExists($tag_name, $idreference, $reference_type="", $iduser=0){
        if (empty($reference_type)) { $reference_type = $this->default_reference; }
        if (empty($iduser)) { $iduser = $_SESSION['do_User']->iduser; }
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT * from tag WHERE tag_name='".trim($tag_name)."' 
                                       AND iduser = ".$iduser." 
                                       AND reference_type ='".$reference_type."' 
                                       AND idreference =".$idreference);

        if($q->getNumRows() > 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Before deleting a tag it will check in the tag_association
     * table is there are some tags associated with the idtag.
     * if yes tag should not be deleted from tag table else delete
     * OBSOLETE / DEPRECATE
     */

    function deleteTag($tag_name=""){
        if (empty($tag_name)) {
           $tag_name = $this->tag_name;
        }
        $q->query("select * from tag where tag_name = ".$this->quote($tag_name)." and iduser=".$_SESSION['do_User']);
        if ($q->getNumRows() == 1) {
            $this->delete();
        } 
        $q->free();
    }
 
    function delTagById($idtag){
      $this->getId($idtag) ;
      $this->delete();
      $this->deleteTagShared($this);
    }

    function eventAjaxDeleteTagById(EventControler $event_controler) {
        $this->getId($event_controler->idtag_delete);
        $this->delete();
        $this->deleteTagShared($this);
        $_SESSION['tag_refresh_now'] = true;
    }
    


    /**
      * Function to delete tags for a contact which is shared
      * @param object $Obj , holding the tag data
    */
    function deleteTagShared($Obj){
        $do_contact_sharing = new ContactSharing();
        $do_contact_view = new ContactView();
        $shared_co_workers = $do_contact_sharing->selectAllUsersFromContactSharing($Obj->idreference); //print_r($shared_co_workers);exit;
        if($shared_co_workers !== false ){
              $q = new sqlQuery($this->getDbCon());
              foreach($shared_co_workers as $user){
                  if($Obj->iduser != $user){
                     $idtag =  $this->isTagExistsForReferer($Obj->tag_name,$Obj->idreference, $user, $Obj->reference_type); //echo $Obj->tag_name.'----'.$Obj->idcontact.'----'.$Obj->reference_type.'---'.$user;exit;
                     if( $idtag !== false ){
                        $q->query("delete from ".$this->table." where idtag = ".$idtag ." limit 1 ");
                        $do_contact_view->setUser($user);
                        $do_contact_view->deleteTag($Obj->tag_name,$Obj->idreference);
                     }
                  }
              }
        }
    }
  

    /** 
    * Get the tag name depending on the idtag
    * PhL(10/25): Not sure this method is needed ???
    */

    function getTagName($idtag){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select tag_name from tag where idtag = ".$idtag);
      $q->fetch();
      return $q->getData("tag_name");
    }


    /** 
    * Get the tag details for the text suggestion 
    */

    function getSuggestionTags($str){
      $this->query("select * from tag where tag_name like '%".$str."'");
    }
    
    /**
     * Getuser sub tag list, display additional tags of a user 
     * when one or more tags are already selected to narrow the contacts search.
     * Currently this method is only for the contacts as it uses the do_Contacts object
     */
    function getUserSubTagList($reference_type="") {
        if (empty($reference_type)) { $reference_type = $this->default_reference; }
        $contact_in = "(";
        while ($_SESSION['do_Contacts']->next()) {
            $contact_in .= $_SESSION['do_Contacts']->idcontact.",";
        }
        $contact_in .= "0)";
        $_SESSION['do_Contacts']->first();
        $do_tags = new Tag($this->getDbCon());
        $do_tags->query("SELECT DISTINCT tag_name FROM tag WHERE reference_type='".$reference_type."' AND iduser=".$_SESSION['do_User']->iduser." AND idreference in ".$contact_in." ORDER BY tag_name");
        $html = "";

        $current_tags = $_SESSION['do_Contacts']->getSearchTags();

        if (!empty($current_tags)) {
            $e = new Event("do_Contacts->eventSearchByTag");
            if ($_SESSION['do_User']->is_mobile) {
                $e->addParam("goto", "i_contacts.php");
            } else {
                $e->addParam("goto", "contacts.php");
            }
            while ($do_tags->next()) {
                if (!in_array($do_tags->tag_name, $current_tags)) {
                    $e->addParam("search_add_tag_name", $do_tags->tag_name);
                    $html .= "\n<a href=\"".$e->getUrl()."\" class=\"tag_link\">".$do_tags->tag_name."</a>, ";
                }
            }
        }
        return $html;

    }
    /**
     * Getuser tag list, display all the tags of a user 
     * with a link to generate a search and if search exists cumulate tags
     * to narrow further a search.
     * Currently this method is only for the contacts as it uses the do_Contacts object
     */
    
    function getUserTagList($reference_type="") {
		if (isset($_SESSION['last_tag_refresh'])) {
			if ($_SESSION['last_tag_refresh'] < (time() - 60)) {
				$_SESSION['last_tag_refresh'] = time();
				$this->calculateTagSize();
				
			}
		} else {
			$_SESSION['last_tag_refresh'] = time();
			$this->calculateTagSize();
		}
		if ($_SESSION['tag_refresh_now']) {
			$this->calculateTagSize();
			$_SESSION['tag_refresh_now'] = false;
		} else { 
			$_SESSION['tag_refresh_now'] = false;
		}
	
		if ($_SESSION['do_User']->getIsMobile()) { $mobile = 'Yes'; }
		$this->setLog("\n Tag list generation:\n-----------------------");
	        $this->setLog("\n last tag refresh:".$_SESSION['last_tag_refresh']." Current time: ".time()." is time refresh now ?:".(string)$_SESSION['tag_refresh_now']."\n");
	    
		$do_tags = new Tag($this->getDbCon());

		$e = new Event("do_Contacts->eventSearchByTag");
		$e->setSecure(false);
		if($mobile == 'Yes'){
		  $e->addParam("goto", "i_contacts.php");
		}else{
		  $e->addParam("goto", "contacts.php");
		}
		$do_tags->query("SELECT tag_name, clicks FROM tag_size WHERE iduser=".$_SESSION['do_User']->iduser." ORDER BY tag_name");

		while ($do_tags->next()) {
			$e->addParam("search_tag_name", $do_tags->tag_name);
			if (!empty($html)) $html .= ', ';
			$html .= "\n".'<a href="'.$e->getUrl().'" class="tag_link" style="font-size:'.$do_tags->clicks.'px;">'.$do_tags->tag_name.'</a>';
		}
        return $html;
    }
    
    /**
     * addTagClick 
     * Record a tag click for computing the tags size
     * @note: we are using an external table to avoid update on the tag table 
     *        simplify the time sliding
     */

    function addTagClick() {
        $q = new sqlQuery($this->getDbCon());
        $q->query("INSERT INTO tag_click (`tag_name`, iduser, `clicked` ) VALUES ('".$this->tag_name."', ".$_SESSION['do_User']->iduser.", now())");
        $q->free();
    }

    /**
     * calculateTagSize
     * modified the script so only one table is now used to calculate the tag size.
	 * Add a time parameter so more recently clicked tag have more weigth than older clicks.
	 * 
     */
    function calculateTagSize($do_User=NULL) {
        $max_font = 40;
        $min_font = 12;
        $tag_weight = 20;
        $reference_type = $this->default_reference;
        $this->setLog("\n Calculating tag sizes at ".date("Y/m/d H:i:s")." last_calculate:".$_SESSION['last_tag_refresh']);
        if ($do_User == NULL) { $do_User = $_SESSION['do_User']; }
          $q_clicks = new DataObject();
          $q_clicks->setTable("tag_click");
          $q_clicks->query("SELECT tag_name, clicked FROM  tag_click WHERE tag_click.iduser=".$do_User->iduser." ORDER BY clicked DESC");
		 
          $this->setLog("\n Tag click list:".$q_clicks->getSqlQuery());
          $num_tag_clicked = 0;
          $max_weight = 0;
          while ($q_clicks->next()) {
            $ar_tags_weight[$q_clicks->tag_name]+=$tag_weight;
            if ($ar_tags_weight[$q_clicks->tag_name] > $max_weight) { $max_weight = $ar_tags_weight[$q_clicks->tag_name]; }
              $this->setLog("\n ".$q_clicks->tag_name." at ".$q_clicks->clicked." weight:".$tag_weight." tag weight:".$ar_tags_weight[$q_clicks->tag_name]);
            }
            if ($q_clicks->getNumRows() == 0) { 
              $q_size = new sqlQuery($this->getDbCon());
              $q_size->query("DELETE FROM tag_size WHERE iduser=".$do_User->iduser);
              $this->query("SELECT tag_name FROM tag WHERE reference_type='".$reference_type."' AND iduser=".$do_User->iduser." GROUP BY tag_name");
              while ($this->next()) {
                  $q_size->query("INSERT INTO tag_size (tag_name, clicks, iduser) VALUES ('".$this->tag_name."', 16, ".$do_User->iduser.")");

              }
              $q_size->free();
            }
            if($max_weight > 0) {
              $q_size = new sqlQuery($this->getDbCon());
              $q_size->query("DELETE FROM tag_size WHERE iduser=".$do_User->iduser);		 
                 foreach ($ar_tags_weight as $tag_name=>$final_weight) {
                    if ($final_weight > 1) {
                      $ar_tag_size[$tag_name] = $final_weight*($max_font/$max_weight);
                    } else {
                      $ar_tag_size[$tag_name] = $min_font;
                    }	
                    if ($ar_tag_size[$tag_name] < $min_font) { $ar_tag_size[$tag_name] = $min_font; }		
                      $this->setLog("\n Tag ".$tag_name." size: ".$ar_tag_size[$tag_name]." = ".$final_weight."*(".$max_font."/".$max_weight.") = ".$max_font/($max_weight+1)." -");  		 			 			 
                  } 
                  $q_tags = new sqlQuery($this->getDbCon());
                  $this->query("SELECT tag_name FROM tag WHERE reference_type='".$reference_type."' AND iduser=".$do_User->iduser." GROUP BY tag_name");
                  while ($this->next()) {
                      if (isset($ar_tag_size[$this->tag_name])) { 
                      $tag_size = $ar_tag_size[$this->tag_name]; 
                      } else {
                      $tag_size = $min_font; 
                      }
				
                      $q_size->query("INSERT INTO tag_size (tag_name, clicks, iduser) VALUES ('".$this->tag_name."', ".$tag_size.", ".$do_User->iduser.")");
                  }
                  $q_size->free();
              }
         $q_clicks->query("DELETE FROM tag_click WHERE iduser=".$do_User->iduser." AND clicked < date_sub(now(), interval 1 month)");
         $q_clicks->free();

    }

    /**
	 * @note: where is this used ? (PhL)
	 */

    function generateUserTagsDropDown($reference_type=""){
        $iduser =$_SESSION['do_User']->iduser;
        if (empty($reference_type)) { $reference_type = $this->default_reference; } 
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT distinct tag_name from ".$this->table."
                                       where 
                                       iduser = ".$iduser." 
                                       AND reference_type ='".$reference_type."'
                                       AND tag_name <> ''
                                       Order By tag_name   
                                        ");
       $html = '';
       if($q->getNumRows()){
          $html .='<select name = "delTagMul" id="delTagMul" onChange="deleteTagMul(); return false;" >';
          $html .= '<option value = "">'._('Select Tag to remove').'</option>';
          while($q->fetch()){
              $html .= '<option value = "'.$q->getData("tag_name").'">'.$q->getData("tag_name").'</option>'; 
          }
          $html .='</select>';
       }
       return $html; 
    }

    function getAllTagNamesForUser(){
      $this->query("select distinct(tag_name) from tag where iduser = ".$_SESSION['do_User']->iduser." AND iduser <> 0 AND tag_name <> '' order by tag_name asc");
    }

    function eventChangeTagNameVar(EventControler $event_controler) {
	    $event_controler->tags = $event_controler->fields['tag_name'];
    }

     /**
        Method to re-create the tags associated with a contact with the shared Co-Workers
        @param integer idcontact
        @param integer idcoworker
    */
    function addTagOnContactSharing($idcontact,$idcoworker,$iduser = 0){
        if($iduser == 0){
            $iduser = $_SESSION['do_User']->iduser ;
        }
        $tags = $this->getAllTagNamesForReferer($idcontact,$iduser);
        $comma_separated_tags = '';
        if(is_array($tags) && count($tags) > 0){
            $do_contact_view = new ContactView();
            foreach($tags as $tag){
              $this->addTagAssociation($idcontact,$tag,"",$idcoworker);
            }
            $do_contact_view->setUser($idcoworker);
            $do_contact_view->addTag(implode(",",$tags),$idcontact);
        }
	
    }


  function getUserContactTags($iduser,$idcontact) {
    $sql = "SELECT *
            FROM ".$this->table."
            WHERE iduser = ".$iduser." AND idreference = ".$idcontact;
    $this->query($sql);
  }

}
