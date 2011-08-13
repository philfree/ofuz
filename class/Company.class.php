<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/
    /**
     * Company class
     * Using the DataObject
     */

class Company extends DataObject {
    
    public $table = "company";
    protected $primary_key = "idcompany";
    
    private $report = Array (
      "list_companies"
      );
    private $savedquery = Array (
      "all_companies"
    );

    /**
     * Event Function used while adding a company
     * Checks if the Company is already in the database
     * If there then do not add and set doSave = mo
     * Else continue with other events from the called page
     * 
    */
    function eventCheckDuplicateCompanyInAdd(EventControler $evtcl){
      $fields = $evtcl->getParam('fields');
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from company where name='".trim($fields['name'])."'"); // need to have the iduser as well 
      if($q->getNumRows() > 0){
         $dispError = new Display($evtcl->errPage);
         $dispError->addParam("message", "This company is already in the database");
         $evtcl->addParam("doSave","no");
         $evtcl->setDisplayNext($dispError);
      }
    }

    /**
     * Event Function used while updating a company
     * Checks if the updated Company is already in the database
     * If there then do not update and set doSave = mo
     * Else continue with other events from the called page
     * 
    */
    function eventCheckDuplicateCompanyInUpdate(EventControler $evtcl){
      $fields = $evtcl->getParam('fields');
      $q_company = new sqlQuery($this->getDbCon());
      $q_company->query("select name from company where idcompany = ".$this->getPrimaryKeyValue());
      $q_company->fetch();
      $comp_name = $q_company->getData("name"); 
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from company where name='".trim($fields['name'])."' 
      AND name <> '".trim($comp_name)."'"); // need to have the iduser as well 
      if($q->getNumRows() > 0){
        $dispError = new Display($evtcl->goto);
        $dispError->addParam("message", "This company is already in the database");
        $evtcl->addParam("doSave","no");
        $evtcl->setDisplayNext($dispError);
      }
    }

    function eventAddCompany($params) {
    	$fields = $params['fields'];
    	$this->name = $fields['company'];
    	$this->add();
    }

    function addPhone($number, $type){
    }

    function addAddress($address, $type){
    }

    function addEmail($email, $type){
    }

    function addNewCompany($name,$idusers){
      $idcompany = $this->checkCompanyExists($name,$idusers);
      if(!$idcompany){
        $this->iduser = $idusers;
        $this->name = $name;
        $this->add();
        $last_inserted_id = $this->getPrimaryKeyValue();
        return $last_inserted_id;
      }else{
        return $idcompany;
      }
      
    }

	function checkCompanyExists($name,$iduser) {
        $q_company = new sqlQuery($this->getDbCon());
        $sql_sel = "SELECT *
                    FROM company
                    WHERE iduser = ".$iduser." AND 
                    name = '".$name."'
                   ";

        $q_company->query($sql_sel);

        if($q_company->getNumRows()){
            $q_company->fetch();
            return $q_company->getData("idcompany");
        } else{
                  return false;
        }
	}

    function getCompanyDetails($idcompany) {
        $this->query("select * from company where idcompany = " . $idcompany);
    }

    function getCompanyName($idcompany){
       $q = new sqlQuery($this->getDbCon());
       $q->query("select name from company where idcompany = ".$idcompany) ;
       while($q->fetch()){
          $name = $q->getData("name");
          $lname = $q->getData("lastname");
       }
       return $name;
    }

    function getUserCompany(){
        $this->query("select * from company where iduser=".$_SESSION['do_User']->iduser. " AND iduser <> 0 AND name <> '' order by name asc");
    }

    function formatTextDisplayWithStyle($text){
      /**
        $text = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $text); //phone number replace
        $text = preg_replace("/([0-9]{5})([0-9]{4})?/", "$1-$2", $text); //zip code replace
     */
      $ret = ereg_replace("[a-zA-Z]+://([.]?[a-zA-Z0-9_/-])*", "<a style =\"color:orange;\" href=\"\\0\" target = \"_blank\">\\0</a>", $text);
      $ret = ereg_replace("(^| )(www([.]?[a-zA-Z0-9_/-])*)", "\\1<a style =\"color:orange;\" href=\"http://\\2\" target = \"_blank\">\\2</a>", $ret);
      $ret = preg_replace("/([\w\.]+)(@)([\S\.]+)\b/i","<a style =\"color:orange;\" href=\"mailto:$1@$3\">$1@$3</a>",$ret);
      return ($ret) ;
    }

    /**
     * Add a breadcrumb for current company
     */

    function setBreadcrumb() {
        $do_breadcrumb = new Breadcrumb();
        $do_breadcrumb->type = "Company";
	if (is_object($_SESSION['do_User'])) {
	   $do_breadcrumb->iduser = $_SESSION['do_User']->iduser;
	}
        $do_breadcrumb->id = $this->idcompany;
        $do_breadcrumb->add();
    }

    /**
      API usage method for checking duplicate company
      @param iduser,company
    */
     function isDuplicateCompany($iduser,$company){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table. " where name = ".trim($company)." AND iduser = ".$iduser);
        if($q->getNumRows()){
            return $this->getData("idcompany");
        }else{ return false; }
     }


    /**
     *Get all Companies details for specific user
     */
      function getAllCompanies($iduser){	  
	  $sql= "SELECT *
		  FROM  {$this->table} 
		  WHERE  iduser = {$iduser} 
		  ORDER BY {$this->table}.idcompany" ;  
	  $this->query($sql);
	
    

      }
      

}
?>
