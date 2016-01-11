<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * WebFormField class
     * Using the DataObject
     */
   
class WebFormField extends DataObject {
    
    public $table = "webformfields";
    protected $primary_key = "idwebformfields";
    
/*
    private $report = Array (
      "list_contacts","i_list_contacts"
      );
    private $savedquery = Array (
      "all_contacts"
    );
    
    public $search_keyword = "";
    public $filter = "";
    private $search_tags = Array();
    private $sql_view_name = "";

    public $sql_view_order = "lastname";
    public $sql_view_limit = "50";
*/

    function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_WEBFORM);
		}
    }    
	
	function displayFields() {
		$out = '<table>
					<tr style="field_form_line">
						<td>'._('Selected').'</td><td>'._('Label').'</td><td>'._('Size in characters').'</td><!--<td>Required</td>-->
					</tr>
               ';
		while ($this->next()) {
			$out .= '<tr style="field_form_line">';
			$out .= '<td><input type="checkbox" name="field_selected['.$this->name.']" value="Yes"></td>';
			$out .= '<td><input type="text" name="field_label['.$this->name.']" value="'.$this->label.'"></td>';
			$out .= '<td><input type="text" name="field_size['.$this->name.']" value="'.$this->size.'"></td>'; 
			//$out .= '<td><input type="checkbox" name="field_required['.$this->name.']" value="'.$this->required.'"></td>';
			$out .= '</tr>';
		}
		$out .= '</table>';
		return $out;
	}

        

  function displayFieldsOnWebFormEdit($id) {
    $do_wf_user_field = new WebFormUserField();
    $data_form_field = $do_wf_user_field->getFieldsByWebFormUser($id);
    $out = '<table>
            <tr style="field_form_line">
            <td>'._('Selected').'</td><td>'._('Label').'</td><td>'._('Size in characters').'</td><!--<td>Required</td>-->
            </tr>';
      while ($this->next()) {
        $data = $data_form_field;
        $checked = "";
        foreach($data as $data){
          if($this->name == $data["name"]){
              $checked = "Checked"; 
              $name = $data["name"];
              $label = $data["label"];
              $size = $data["size"];
          }
        }
        if($checked == ""){  
          $name = $this->name;
          $label = $this->label;
          $size = $this->size;
        }
  
        $out .= '<tr style="field_form_line">';
        $out .= '<td><input type="checkbox" name="field_selected['.$name.']" value="Yes" '.$checked.'></td>';
        $out .= '<td><input type="text" name="field_label['.$name.']" value="'.$label.'"></td>';
        $out .= '<td><input type="text" name="field_size['.$name.']" value="'.$size.'"></td>'; 
        //$out .= '<td><input type="checkbox" name="field_required['.$this->name.']" value="'.$this->required.'"></td>';
        $out .= '</tr>';
      }
      $out .= '</table>';
      return $out;
  }
}
?>
