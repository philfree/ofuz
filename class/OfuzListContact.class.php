<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

class OfuzListContact extends OfuzList {

    public $uniq_key = "idcontact";

  /**
    * Display method for the list
  */

//  public function displayList(){
//      while($this->data->next()){
//          $this->displayRow($class_name) ;
//      }
//  }
  /**
  */
  public function displayRow($void=null){
  	$html = '';
       // $html .= '<div class="ofuz_list_contact" id="cid'.$this->data->idcontact.'" onclick="fnHighlight(\''.$this->data->idcontact.'\')"><table><tr><td><input type="checkbox" name="ck[]" id="ck'.$this->data->idcontact.'" value="'.$this->data->idcontact.'" class="ofuz_list_checkbox" onclick="fnHighlight(\''.$this->data->idcontact.'\')" /></td>';
        $html .= '<td class="ofuz_list_contact_col1">
                          <img src="'.$this->data->getContactPicture($this->data->idcontact).'" width="34" alt="" />
                      </td>';
        $html .= '<td class="ofuz_list_contact_col2">
                          <span class="contact_name"><a href="/Contact/'.$this->data->idcontact.'" onclick="allowHighlight=false;">'.$this->data->firstname.'&nbsp;'.$this->data->lastname.'</a></span>';
                          if ($this->data->idcompany != '') {
                              $e_detail_com = new Event('mydb.gotoPage');
                              $e_detail_com->addParam('goto', 'company.php');
                              $e_detail_com->addParam('idcompany',$this->data->idcompany);
                              $e_detail_com->addParam('tablename', 'company');
                              $e_detail_com->requestSave('eDetail_company', $_SERVER['PHP_SELF']);
                              $companyURL = $e_detail_com->getUrl();
                              //$html .= '<div class="contact_position"><i>'.$this->data->position.'</i> '._('at').' <a href="'.$companyURL.'" onclick="allowHighlight=false;">'.$this->data->company.'</a></div>';
                              $position = '';
                              if(strlen($this->data->position) > 0 ){
                                    $position =  '<i>'.$this->data->position.'</i> '._('at').' ';
                              }
                              $html .= '<div class="contact_position">'.$position.' <a href="'.$companyURL.'" onclick="allowHighlight=false;">'.$this->data->company.'</a></div>';
                          }
                          $html .= '</td>';
            $html .= '<td class="ofuz_list_contact_col3">
                          '.$this->data->phone_number.'<br />
                          <a href="mailto:'.$this->data->email_address.'">'.$this->data->email_address.'</a><br />
                          <i>'.str_replace(",", ", ", $this->data->tags).'</i>
                      </td>';
            //$html .= '</tr></table></div>';
            //$html .= '<div class="spacerblock_2"></div><div class="solidline"></div><div id="'.$this->data->idcontact.'" class="message_box"></div>';
    echo $html;
  }
    
}
?>
