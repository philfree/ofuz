<?php 

/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: WebForm';
    $Author = 'SQLFusion LLC';
    //$Keywords = 'Keywords for search engine';
    //$Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/header.inc.php');

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
	//$do_User = $do_webformuser->getParentUser();
   // $do_User->sessionPersistent("do_user", "index.php", OFUZ_TTL);
    //$do_user = $do_contact->getParentUser();

    include_once('includes/ofuz_portal_header.inc.php');
    
?>


<div class="content">
    <table class="main">
        <tr> 
            <td class="main_right">
                <div class="contacttop">
                    <table class="tplain">
                        <tr>
                            <td>
                                <div class="pad20">
                                    <span class="headline14">
                                        <?php echo $do_webformuser->title; ?>
                                        
                                    </span>
                                </div>
                            </td>
                        </tr>
                    </table>
		        </div>
                <div class="contentfull">
		          <div class="vpad10">
                      <?php 
					        //if (strlen($do_webformuser->description)>0) {
								echo $do_webformuser->description;
							/**} else { 
								$msg = new Message();
								$msg->setData(Array("user_firstname" => $do_user->firstname, 
										"user_lastname" => $do_user->lastname,
										"user_company" => $do_user->company));
								$msg->displayMessage("welcome client webform");
							}**/
                      ?>
		    </div>
				<!--
                    <div class="vpad10">
                        <span class="headline11">Post a message</span>		
                    </div>
					-->
                     <?php		
						 $do_webformuser->newForm("do_webformuser->eventAddContact");
						 if ($do_webformuser->email_alert == 'y') {
							$do_webformuser->form->addEventAction("do_webformuser->eventSendEmailAlert", 300);
						 }
						 echo $do_webformuser->form->getFormHeader();
						 echo $do_webformuser->form->getFormEvent();
						 
						 echo $do_webformuser->displayWebFormFields();
						 
						 echo '<div align="right">'.$do_webformuser->form->getFormFooter(_('Submit')).'</div>';						 
                     ?>										
                    <div class="dottedline"></div>
                    <div class="section">
                    </div>
                    <div class="solidline"></div>
                    <div class="bottompad40"></div>
                </div>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
