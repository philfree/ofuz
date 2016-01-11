<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

class CompanyWebsite extends MultiRecord {
    
    public $table = "company_website";
    protected $primary_key = "idcompany_website";
    protected $prefix = "CompanyWebsite";  // Should be the same as the class name 
    protected $dropdown_options = Array( "Company", "Blog", "Twitter", "LinkedIn", "Facebook", "Youtube", "Other");

   protected function getNewFormFields($new_website_count) {
      $form  ='
        <input type="text" name="mfields['.$this->getTable().'_new]['.$new_website_count.'][website]" value="">
        <select name="mfields['.$this->getTable().'_new]['.$new_website_count.'][website_type]">';
            foreach ($this->dropdown_options as $comp_website) {
                $form .= "<option>".$comp_website."</option>";
            }
       $form .='</select>';
       return $form;

   }

   protected function getUpdateFormFields() {
        $form .= '<input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][website]" value="'.$this->website.'">';
        $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][website_type]">';
        foreach ($this->dropdown_options as $website_type) {
            if ($website_type == $this->website_type) { $selected = " SELECTED"; } else { $selected = ""; }
            $form .= "<option ".$selected.">".$website_type."</option>";
        }
        $form .= '</select>';
        return $form;
   }     

   public function getDisplayLink() {
        $link = '<a href="'.$this->website.'" title="'.$this->website_type.'">'.$this->website.'</a> ';
        return $link;
   }

   /**
    * This method is used when editing or adding a web site
    * to try its best to rebuilt a working url of the website.
    */
   function rebuiltUrl($website_url, $type="") {
      if (!ereg("^http", $website_url)) {
        if (!empty($type)) {
            if (!ereg("twitter\.com", $website_url) && $type=="Twitter") {
                $website_url = "twitter.com/".$website_url;
            }
        }
        $website_url = "http://".$website_url;
      }
      return $website_url;
   }

   function eventSaveWebsites(EventControler $evctl)  {
      $mfields = $evctl->mfields;
      $this->setLog("\n ".$this->getPrefix().": Saving multiple Websites");
      $this->idcompany = $_SESSION['CompanyEditSave']->idcompany;
      if (is_array($mfields['company_website'])) { 
	      foreach($mfields['company_website'] as $primary_key_value=>$fields) {
	        $this->getId($primary_key_value);
	      	$this->website = $this->rebuiltUrl($fields['website'], $fields['website_type']);
	      	$this->website_type = $fields['website_type'];   	
	      	//$this->setPrimaryKeyValue($primary_key_value);
	      	$this->setLog("\n ".$this->getPrefix() .": Updating Website:".$this->website);
	      	$this->update();
	      }
      }
      if (is_array($mfields['company_website_new'])) {
              $this->idcompany_website = '';
	      foreach($mfields['company_website_new'] as $fields) {
	        $this->addNew();
	        $this->idcompany = $_SESSION['CompanyEditSave']->idcompany;
	      	$this->website = $this->rebuiltUrl($fields['website'], $fields['website_type']);
	      	$this->website_type = $fields['website_type'];
	      	$this->setLog("\n ContactWebsite: Adding Website:".$this->website." ".$fields['website']." Type:".$this->website_type.", for Company:".$this->idcompany);
	      	if (strlen($this->website) > 0) {
	      	    $this->add();
	      	}
	      }
      }
  
   } 
}
?>