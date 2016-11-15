<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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
  * @package PASEvents
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2004
  * @version 3.8	
  */
  global $strUnabletoSave;
  if (!isset($strUnabletoSave)) {
      $strUnabletoSave = "Was unable to save the file :";
  }
  $strUnabletoSave .= $userfile_name[$fidx] ; 

	/*********************************************/
	function checkfilename($file_name, $addlabel)
	{
		$temp_file = "";
		$new_name = "";
		$temp_file = $file_name;
		$last = substr($temp_file, strpos($temp_file,"."));
		$start = substr($temp_file, 0, (strlen($temp_file)-strlen($last)));

		$start = ereg_replace("[^A-Za-z0-9 .\-|_ßAÁÂAÄAAÇEÉEËIÍÎINOÓ
ÔOÖUÚUÜÝaáâaäaaçeéeëiíîind’
oóôoöuúuüýyAaĂăĄąĆćCcCcČč
ĎďĐđEeEeEeĘęĚěGgGgGgGgHh
HhIiIiIiIiIiJjKĹĺLlĽľŁłŃńNnŇňOo
OoŐőOoŔŕRrŘřŚśSsŞşŠšŢţŤťTt
UuUuUuŮŰůűUuWwYyYŹźŻżŽž
fOoUuAIiOoUuUUuUuUu×O×O\]", "", $start);
		$start = ereg_replace("<>\/'~ˇ^˘°˛`˙´:", "", $start);
                $start = str_replace("\\", "", $start);
		$start = ereg_replace(" ", "", $start);

		$addlabel = ereg_replace("<>\/'~ˇ^˘°˛`˙´:", "", $addlabel);
                $addlabel = str_replace("\\", "", $addlabel);
		$addlabel = ereg_replace(" ", "_", $addlabel);
                
		if (strlen(trim($start)) > 50)
			$start = substr($start, 0, 10)."_".$addlabel;
		$new_name = trim($start).$last;
		//echo $new_name;exit;
                $new_name = str_replace("\\'","",$new_name);
		$new_name = str_replace(" ","_",$new_name);
		return $new_name;
	}
	/*********************************************/

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

				// Making Process on file with Garbadge values.
				$userfile_name = checkfilename($userfile_name, $fields['label']);

				$ipath = $filedirectoryuploaded[$fidx] ;
				if($overwrite == "no") { 
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
					$this->setError("<b>File Upload</b>".$this->getErrorMessage()) ;
					if (strlen($errorpage)>0) {
					$urlerror = $errorpage;
					} else {
					$urlerror = $this->getMessagePage() ;
					}
					$disp = new Display($urlerror);
					$disp->addParam("message", $strUnabletoSave) ;
					$this->updateparam("doSave", "no") ;
					$this->setDisplayNext($disp) ;
				} else {
						move_uploaded_file($userfile, $destpath);
				}
				$fields[$filefield[$fidx]] = $val ;
				$this->updateparam("fields", $fields) ;
			}
		}

    }
?>
