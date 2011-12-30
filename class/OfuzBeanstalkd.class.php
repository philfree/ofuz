<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com


  /**
   * Class OfuzBeanstalkd
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @package OfuzCore
   * @license GNU Affero General Public License
   * @version 0.6
   * @date 2010-09-04
   * @since 0.6
   */


class OfuzBeanstalkd extends DataObject{
    public $table = "";
    public $primary_key = "";
    public $tube_name = 'ofuzqueue';
    
    
    
    /**
      * Function to add the data in the queue
      * @param object $obj
      * @param string $method
      * @param integer $iduser
    */
    public function addToQueue($obj,$method,$iduser){
        include_once("pheanstalk/pheanstalk_init.php");
        $pheanstalk = new Pheanstalk(JOB_QUEUE_IP.':'.JOB_QUEUE_PORT);
        //print_r($pheanstalk);exit;
        $job = new stdClass();
        $job->obj_data = $obj;
        $job->iduser = $iduser;
        $job->method_name = $method ;
        $job_data = json_encode($job);
        $pheanstalk->useTube($this->tube_name)->put($job_data, 1000, 5, 43200);
    }


    /**
      * Method to import the facebook friends using the queue
      * @param object $job
      * @see Contact::importFacebookFriends();
    */
    public function jobqueue_fb_friend_import($job){
        include_once 'facebook_client/facebook.php';
        include_once 'class/OfuzFacebook.class.php';
        //$qry = new sqlQuery($this->getDbCon());
        //$qry_d = new sqlQuery($this->getDbCon());
        $data = $job->obj_data ;
        $iduser = $job->iduser;
        $obj = unserialize($data);
        if(is_object($obj)){
            try{
                @$friends = $obj->getFbFriends();// will contain the fbid of friends
            }catch(Exception $e){
                echo 'Friends Not Found !!';
            }
            $list  = $obj->getFriendsList();
            $count = count(@$friends);
            $i = 1;
            $j=0;
            foreach(@$friends as $friend){
                $frnd_list = array();
                $frnd_list = $list;
                $list_name_array = array();
                if(is_array($frnd_list)){
                  foreach($frnd_list as $frnd_list){
                      $list_id = $frnd_list['flid'];
                      $frnds_in_list = $obj->getFriendsInList($list_id);
                      if(@in_array($friend,$frnds_in_list)){
                        $list_name_array[] = $frnd_list['name'];
                      }
                  }
                }
                $j++;
                //echo '11';
                $do_contact = new Contact(); 
                $name = $obj->getFbUserName($friend); // will contain the firstand last name in facebook
                $affiliations =  $obj->getFbUserAffiliations($friend);
                $work_history = $obj->getWorkHistory($friend);
                $profile_url  = $obj->getProfileURL($friend);
                $profile_pic_with_logo = $obj->getProfilePicWithLogo($friend);
                $friends_data = array("fb_uid"=>$friend,"name"=>$name, "affiliations"=>$affiliations,"work"=>$work_history,"profile_url"=>$profile_url,"pic_with_logo"=>$profile_pic_with_logo,"listname"=>$list_name_array);
                $do_contact->importFacebookFriends($friends_data,$iduser);
                $do_contact->free();
           
            }
            echo '<h2>Friends have been imported for Userid '.$iduser.'</h2> ';
        }
        
    }
}
