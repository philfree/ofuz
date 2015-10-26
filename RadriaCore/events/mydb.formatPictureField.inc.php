<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
 /**
  *   Event Mydb.formatPictureField.
  *
  * Format a picture field and upload the file
  * <br>- @param array filefield name of the fields that contain file name.
  * <br>- @param array filedirectoryuploded array directory of where to upload the file.
  * <br>- @param array filenameuploded array with the optional name on the server.
  * <br>- @param file userfile name of the field file.
  * <br>Options :
  * <br>-@param string errorpage page to display the errors
  * <br> @param bool fileoverwrite value yes or no (1 or 0)
  * @package RadriaEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.8	
  */
  global $strUnabletoSave;
  if (!isset($strUnabletoSave)) {
      $strUnabletoSave = "Was unable to save the file :";
  }
  $strUnabletoSave .= $userfile_name[$fidx] ; 

    function setnewfilename($path, $filename, $id=0) {
        if (!ereg("/$", $path)) { $path .= "/";}
        if (file_exists($path.$filename)) {
            $id++;
            $filename = "n".$id."-".$filename;
            setnewfilename($path, $filename, $id);
        } 
        return $filename;
    }

    $this->setLogRun(false);
    if (defined("RADRIA_LOG_RUN_MYDB_EVENTS")) {
        $this->setLogRun(RADRIA_LOG_RUN_MYDB_EVENTS);
    }
    
    for($fidx=0;$fidx<count($filefield);$fidx++) {

        $userfile = $_FILES['userfile']['tmp_name'][$fidx];
        $userfile_name = $_FILES['userfile']['name'][$fidx];
        $this->setLog("\nUserfile:".$userfile_name);

    if($userfile != "none") {
        if($userfile_name=="") {
            $val="";
        } else {

            $overwrite = $fileoverwrite[$fidx];
    
            $filepatharray = explode("/", $userfile_name) ;
            $numsubdir = count($filepatharray) ;
            if ($numsubdir >1) {
                $userfile_name = $filepatharray[$numsubdir-1] ;
            }
    
            $ipath = $filedirectoryuploaded[$fidx] ;
            if($overwrite == "no" || $overwrite == "0") { 
                $chk_file_name = $userfile_name;
                $file_num = 0;
                while (file_exists($ipath."/".$chk_file_name)) {
                    if (preg_match("/^n[0-9]*_/", $chk_file_name, $match)) {
                      $chk_file_name = str_replace($match[0], "", $chk_file_name);
                    }
                    $chk_file_name = "n".++$file_num."_".$chk_file_name;
                }
                $val = $chk_file_name;
    
            } else { 
                $val=$userfile_name;;
            }

            $destpath= $ipath."/".$val;
    
            if(!is_uploaded_file($userfile)) {

                switch ($_FILES['userfile']["error"][$fidx]) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $error_message = "The uploaded file exceeds the upload_max_filesize directive (".ini_get("upload_max_filesize").") in php.ini.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_message = "The uploaded file was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message = "No file was uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_message = "Missing a temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_message = "Failed to write file to disk";
                    break;
                default:
                    $error_message = "Unknown File Error";
                }

                $this->setError("<b>File Upload</b> ".$error_message." - ".$this->getErrorMessage()) ;
                if (strlen($errorpage)>0) {
                $urlerror = $errorpage;
                } else {
                $urlerror = $this->getMessagePage() ;
                }
                $disp = new Display($urlerror);
                $disp->addParam("message", $strUnabletoSave."  ".$error_message) ;
                $this->updateparam("doSave", "no") ;
                $this->setDisplayNext($disp) ;
            } else {
                    move_uploaded_file($userfile, $destpath);
            }
            $fields[$filefield[$fidx]] = $val ;
            $this->updateparam("fields", $fields) ;
            $this->addParam("uploaded_file", $destpath);
        }
    }

    }
  
  
?>