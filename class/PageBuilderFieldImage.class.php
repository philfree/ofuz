<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


/**
 * Class strFBFieldFile RegistryField class
 *
 * In the Form context Display a input type File and trigger the EventAction: mydb.formatPictureField that will process the uploaded file.
 * In the Disp context if the file is an image display it in an image tag, otherwize in a link to download it.
 * @package PASClass
 */
Class PageBuilderFieldImage extends RegistryFieldBase {
    function default_Disp($field_value="") {
        $file_path = trim($this->rdata['picture']);
        if (!substr($file_path, -1) == '/') {
            $file_path .= "/";
        }
        if (!$this->getRdata('execute')) {
            $field_value = $this->no_PhpCode($field_value);
        }
        if ($this->getRData('showpicture')=="1" && !empty($field_value)) {
			if (preg_match('/^http\:\/\//', $field_value)) {
				$fval="<img border=\"0\" src=\"".$field_value."\">-----------";
			} else {
				$fval="<img border=\"0\" src=\"".$file_path.$field_value."\">========"; 
			}
         } else {
            $fval = $file_path.$field_value;
            $fval = "<a href=\"".$fval."\">".$fval."</a>" ;
         }
         $this->processed .= $fval;
    }

    function default_Form($field_value="") {
        if (!$this->rdata['hidden'] && !$this->rdata['readonly']) {
            if (!$this->getRdata('execute')) {
                    $field_value = $this->no_PhpCode($field_value);
            }
            $overwrite = strtolower($this->getRData('overwrite'));
            $max_width = $this->getRData("max_width") ;
            $max_height = $this->getRData("max_height") ;
            $file_path =  $this->picture;
			if (!substr($file_path, -1) == '/') {
				$file_path .= "/";
			}
			$fval = '';
			
            $fval .= "<input type=\"hidden\" name=\"mydb_events[5]\" value=\"pagebuilder.FieldUploadFile\"/>" ;
            if ($overwrite == "no") {
                $fval .= "<input type=\"hidden\" name=\"fileoverwrite[]\" value=\"no\"/>" ;
            }
            if ($max_width > 0 || $max_height > 0) {
                $fval .= "<input type=\"hidden\" name=\"mydb_events[23]\" value=\"pagebuilder.ImageFieldResize\"/>" ;
                $fval .= "<input type=\"hidden\" name=\"maxwidth[".$this->getFieldName()."]\" value=\"".$max_width."\"/>" ;
                $fval .= "<input type=\"hidden\" name=\"maxheight[".$this->getFieldName()."]\" value=\"".$max_height."\"/>" ;
            }
            $fval .= "<input type=\"hidden\" name=\"filedirectoryuploaded[]\" value=\"".$file_path."\"/>" ;
            $fval .= "<input type=\"hidden\" name=\"filefield[]\" value=\"".$this->getFieldName()."\"/>";
			if($this->getRData('showpicture')=="1" && !empty($field_value)) {
				 if (preg_match('/^http\:\/\//', $field_value)) {
					$fval.="<img border=\"0\" src=\"".$field_value."\">";
				 } else {
					$fval.="<img border=\"0\" src=\"".$file_path.$field_value."\">"; 
				 } 
			 }
            $fval .= "<input type=\"hidden\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\"/>";
            $fval .= "<input class=\"adformfield\" name=\"userfile[]\" type=\"file\"/>";
			
            $this->processed .= $fval;
        }
    }
}

?>
