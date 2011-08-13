<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/


    /**
     *  Resize images after uploaded, set max size
     *  Width or Height so the image 
     **/


    $this->setLogRun(false);
    if (defined("PAS_LOG_RUN_PAGEBUILDER")) {
        $this->setLogRun(PAS_LOG_RUN_PAGEBUILDER);
    }
    $this->setLog("\n From image upload events: image resize ".date("Y/m/d H:i:s"));

    $uploaded_file = $this->getParam("uploaded_files");
    $image_width = $this->getParam("maxwidth");
    $image_height = $this->getParam("maxheight");
   // print_r($uploaded_file);exit;
    $this->setLog("\n Got Param:".count($uploaded_file));

    foreach($uploaded_file as $field_name => $imagefilename) {
        if (function_exists("imagecreatetruecolor")) {
            $this->setLog("\n processing field:".$field_name." image:".$imagefilename);
            if ((!empty($image_width[$field_name]) || !empty($image_height[$field_name])) && !empty($imagefilename)) {
                $max_width = $image_width[$field_name];
                $max_height = $image_height[$field_name];
                list($width, $height) = getimagesize($imagefilename);
                $height_coef = $height / $width;
                $width_coef = $width / $height;
                if ($width > $max_width && $new_width > 0) { 
                    //$width = $image_width; 
                    $new_height = $max_width*$height_coef;
                    $new_width = $max_width;
                } else {
                    $new_width = $width;
                    $new_height = $height;
                }
                if ($new_height > $max_height && $new_height > 0) {
                    
                    //$height = $image_height;
                    $new_width = $max_height*$width_coef;
                    $new_height = $max_height;
                } 
                if (eregi("jpg$", $imagefilename) || eregi("jpeg$", $imagefilename)) { 
                    $this->setLog("\n Resize jpg width:".$width."x".$height." to ".$new_width."x".$new_height);  
                    $thumb = imagecreatetruecolor($new_width, $new_height);
                    $source = imagecreatefromjpeg($imagefilename);                 
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagejpeg($thumb, $imagefilename); 
                } elseif (eregi("gif$", $imagefilename)) { 
                    $this->setLog("\n Resize gif width:".$width."x".$height." to ".$new_width."x".$new_height);  
                    $thumb = imagecreate($new_width, $new_height);
                    $source = imagecreatefromgif($imagefilename);                 
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagegif($thumb, $imagefilename); 
                } elseif (eregi("png$", $imagefilename)) {
                    $this->setLog("\n Resize png width:".$width."x".$height." to ".$new_width."x".$new_height);   
                    $thumb = imagecreatetruecolor($new_width, $new_height);
                    $source = imagecreatefrompng($imagefilename);                 
                    imagecopyresized($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                    imagepng($thumb, $imagefilename); 
                } else {
                    $errormessage = _("File format not supported. gif, png and jpg format are currently supported") ;
                }                            
            }
        } else {
                $this->setLog("\n GD 2 not supported, image will not be resized");
        }
    }
    $this->setLogRun(false);

?>