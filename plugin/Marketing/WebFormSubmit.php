<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    include_once('config.php');

    if($_GET['fid']){
        $do_webformuser = new WebFormUser();
		$do_webformuser->getId((int)$_GET['fid']);
		$do_webformuser->sessionPersistent("do_webformuser", "index.php", OFUZ_TTL);
    } else {
		$do_webformuser = $_SESSION['do_webformuser'];
	}

    if (!is_object($_SESSION['do_webformuser'])) {
      exit;
    }

    $do_webformuser->newForm('do_webformuser->eventAddContact');
	if ($do_webformuser->email_alert == 'y') {
		$do_webformuser->form->addEventAction("do_webformuser->eventSendEmailAlert", 300);
	}
	$js = '';
	if (strlen($do_webformuser->title)>0) {
		$js .= $do_webformuser->title . '<br />' ;
	}
	if (strlen ($do_webformuser->description ) > 0 ) {
		$js .= $do_webformuser->description  . '<br />';
	}
    $js .='<form method="post" action="http://'.$_SERVER['SERVER_NAME'].'/eventcontroler.php">' .
          $do_webformuser->form->getFormEvent() .
          $do_webformuser->displayWebFormFields() .
          '<div align="right">'.$do_webformuser->form->getFormFooter('Submit').'</div>';
    $js = addslashes(str_replace("\n", '', $js));
    $js = "<table><tr><td>".$js."</td></tr></table>";
?>
//<![CDATA[
document.write("<?php echo $js; ?>");
//]]>