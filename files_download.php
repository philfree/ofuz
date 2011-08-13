<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Merge Contacts';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    if($_SESSION['portal_idcontact'] == ''){// if not from contact portal 
       include_once('includes/ofuz_check_access.script.inc.php'); 
    }
    
    $access = false;
    $file = $_GET['filename'];
    
    $do_file_download = new OfuzFileDownload();
    if($do_file_download->checkFileAccessSecurity($file)){
        $access = true;
    }

if($access){
	/* For now we will redirect to the file after checking credentials
      $doc_path = '/server/vhtdocs/dev2.sqlfusion.com/ofuz3/files/'.$file; // Dev
      $filesize = filesize($doc_path);//echo $doc_path;exit;

      header('CacheControl: no-cache, no-store, must-revalidate');
      header('Pragma: no-cache');
      header('Expires: -1');
      header('Content-Disposition: attachment; filename="'.$file.'"');
      header("Content-Transfer-Encoding: binary");
      header("Content-Length: ".$filesize);
      header('Content-Type: file/001');
      if ($handle = fopen($doc_path, 'rb')) {
          while (!feof($handle)) {
              echo fread($handle, 8192);
              @ob_flush();
              @flush();
          }
          fclose($handle);
      }
	  */
	  header('Location: files/'.urlencode($file));
     // ob_clean();
     // flush();
     // readfile($doc_path);
}else{
include_once('includes/header.inc.php');
?>
<script type="text/javascript">
    //<![CDATA[
    //]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline14">Error</span>
            <div class="solidline"></div>
        </div>
    </div>
    <div class="contentfull">
     <?php
          
                echo '<div class="messageshadow_unauthorized">';
                echo '<div class="messages_unauthorized">';
                echo 'You can not access this file !!';
                $_SESSION['errorMessage'] = '';
                echo '</div></div><br /><br />';
                exit;
        
     ?>
    </div>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
<?php
}
?>
