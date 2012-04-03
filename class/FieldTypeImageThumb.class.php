<?php
// Copyright 2002 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see pagebuilder_license.txt

/**
 * Class FieldTypeImageThumb RegistryField class
 *
 * In the Form context Display a input type File and trigger the EventAction: mydb.formatPictureField that will process the uploaded file.
 * In the Disp context if the file is an image display it in an image tag, otherwize in a link to download it.
 * @package PASClass
 */
Class FieldTypeImageThumb extends RegistryFieldBase {

    function default_Disp($field_value="") {
        $file_path = trim($this->rdata['picture']);
        if (!ereg("/$", $file_path)) {
            $file_path .= "/";
        }
        if (!$this->getRdata('execute')) {
            $field_value = $this->no_PhpCode($field_value);
        }
        if ($this->getRData('showpicture')=="1" && !empty($field_value)) {
            $fval="<img border=\"0\" src=\"".$file_path.$field_value."\">";
         } else {
            //$fval = $file_path.$field_value;
            //$fval = "<a href=\"".$fval."\">".$fval."</a>" ;
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

            $thumb_max_width = $this->getRData("thumb_max_width");
            $thumb_max_height = $this->getRData("thumb_max_height") ;

            $filedir =  $this->rdata['picture'];
            $filethumbdir = $this->rdata['thumb_folder'];
            $fval .= "<input type=\"hidden\" name=\"mydb_events[5]\" value=\"pagebuilder.FieldImageUpload\"/>" ;
            if ($overwrite == "no") {
                $fval .= "<input type=\"hidden\" name=\"fileoverwrite[]\" value=\"no\"/>" ;
            }
            if ($max_width > 0 || $max_height > 0) {
                $fval .= "<input type=\"hidden\" name=\"mydb_events[23]\" value=\"pagebuilder.ImageFieldResizeWithThumb\"/>" ;
                $fval .= "<input type=\"hidden\" name=\"maxwidth[".$this->getFieldName()."]\" value=\"".$max_width."\"/>" ;
                $fval .= "<input type=\"hidden\" name=\"maxheight[".$this->getFieldName()."]\" value=\"".$max_height."\"/>" ;
                $fval .= "<input type=\"hidden\" name=\"thumb_maxwidth[".$this->getFieldName()."]\" value=\"".$thumb_max_width."\"/>" ;
                $fval .= "<input type=\"hidden\" name=\"thumb_maxheight[".$this->getFieldName()."]\" value=\"".$thumb_max_height."\"/>" ;
            }
            $fval .= "<input type=\"hidden\" name=\"filedirectoryuploaded[]\" value=\"$filedir\"/>" ;
            $fval .= "<input type=\"hidden\" name=\"filedirectorythumbuploaded[]\" value=\"$filethumbdir\"/>" ;
            $fval .= "<input type=\"hidden\" name=\"filefield[]\" value=\"".$this->getFieldName()."\"/>";
            $fval .= "<input type=\"hidden\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\"/>";
            $fval .= "<input class=\"adformfield\" name=\"userfile[]\" type=\"file\"/>";
            //if($field_value!="") $fval .= "(".$field_value.")";            
            $this->default_Disp($field_value);
            $this->processed .= $fval;
        }
    }
}

?>