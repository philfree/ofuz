<?php 
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 

    /**
      * UserProfile class
      * Using the DataObject
      *
      * Most of the Methods and Events related to the UserProfile in Ofuz are defined here.
      *
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzCore
      * @license GNU Affero General Public License
      * @version 0.6
      * @date 2010-09-03
      * @since 0.1
      */
   
class UserProfile extends DataObject {
    
  public $table = "user_profile";
  protected $primary_key = "iduser_profile";



    
  function __construct(sqlConnect $conx=NULL, $table_name="") {
    parent::__construct($conx, $table_name);
    if (RADRIA_LOG_RUN_OFUZ) {
    $this->setLogRun(OFUZ_LOG_RUN_CONTACT);
    }
  }

  /**
    *function to get distinct job_type 
    *
  **/
  function getUserJobType(){
    $this->query( "SELECT distinct(job_type)
                   FROM {$this->table}
                   WHERE job_type!=''
                   ORDER BY `job_type` 
                   ASC");    
  }

  function getProfileInformation($iduser=''){
    if($iduser!=''){
      $this->query("SELECT * 
                  FROM {$this->table}
                  WHERE iduser ='{$iduser}'");   
      if($this->getNumRows()) {
	$profile_information = $this->getValues();
      } else {
	$profile_information = "";
      }
      return $profile_information;
    }

  }


 } //end of class
