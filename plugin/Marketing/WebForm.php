<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $do_userform = new WebFormUser();
    $wb_access = true;
    $do_userform->sessionPersistent('do_userform', 'contacts.php', OFUZ_TTL);
    if (isset($_GET['edit'])) {
      $idwebformuser = $_GET['edit'] ;
      if(!$_SESSION['do_userform']->isWebFormOwner($idwebformuser)){
          $wb_access = false;
      }
    }
    if (isset($plugin_item_value)) {
      $idwebformuser = $plugin_item_value ;
      if(!$_SESSION['do_userform']->isWebFormOwner($idwebformuser)){
          $wb_access = false;
      }
    }
    

?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    	$("div[id^=webform]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
    });
//]]>
</script>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$wb_access){
              $msg = new Message(); 
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo $msg->getMessage("unauthorized weform access");
              echo '</div></div><br /><br />';
              exit;
          }

          if($_SESSION['in_page_message'] != ''){
                  echo '<div style="margin-left:0px;">';
                  echo '<div class="messages_unauthorized">';
                  echo htmlentities($_SESSION['in_page_message']);
                  $_SESSION['in_page_message'] = '';
                  echo '</div></div><br /><br />';
          }
    ?> 
        <div class="messageshadow">
            <div class="messages">
            <?php
				$msg = new Message(); 
				echo $msg->getMessage('web form creator instruction');
            ?>
            </div>
        </div><br />
        <?php
	         $_SESSION['setting_mode'] = 'Yes';
                 if (isset($plugin_item_value) && $plugin_item_value !=''){
                    $_SESSION['do_userform']->getId($plugin_item_value);
                    $do_userform->iduser = $_SESSION['do_User']->iduser;
                    $do_userform->setRegistry("webformuser");
                    $do_userform->newUpdateForm('do_userform');
                    $do_userform->form->addEventAction('do_userform->eventDeleteWebFormFields', 2100);
                    $do_userform->form->addEventAction('do_userform->eventAddFormFields', 2103);
                    $do_userform->form->goto = $cfg_plugin_mkt_path.'WebFormUrl';
		    echo $do_userform->form->getFormHeader();
		    echo $do_userform->form->getFormEvent();
		 
		    echo '<b>'._('Form Title').':</b>'. $do_userform->title;
		 
		    echo '<br/><b>'._('Form Description').'</b><br/>'.$do_userform->description;
                    $do_webform = new WebFormField();
		    $do_webform->getAll('display_order');
                    echo $do_webform->displayFieldsOnWebFormEdit($plugin_item_value);
		    echo '<br/><b>'._('Tags for those contacts').':</b></b> '.$do_userform->tags;
		    echo '<br/><b>'._('Web address to take the user after submitting the form').':</b> '.$do_userform->urlnext;
		    echo '<br/><b>'._('Receive an email alert when someone submit the form').':</b> '.$do_userform->email_alert;
		    echo '<div align="right">'.$do_userform->form->getFormFooter(_('Create')).'</div>';
                    ?>
                    <div><?php 
		            $_SESSION['do_userform']->setApplyRegistry(false);
		            echo _('The url for the web form '). '<b>'.$_SESSION['do_userform']->title.'</b>'._(' is ').
		                $GLOBALS['cfg_ofuz_site_http_base'].'form/'.
						$_SESSION['do_userform']->getPrimaryKeyValue(); 
						?>
		</div>
		<?php echo _('Embed code to insert in your blog or web site'); ?>
		<div>
		<textarea rows="2" cols="100"><script type="text/javascript" src="<?php echo    $GLOBALS['cfg_ofuz_site_http_base'].'js_form.php?fid='.$_SESSION['do_userform']->getPrimaryKeyValue(); ?>"></script>
                </textarea>
		</div>
                    <?php

                  }else{
                      $do_userform->iduser = $_SESSION['do_User']->iduser;
                      $do_userform->newAddForm('do_userform');
                      $do_userform->form->addEventAction('do_userform->eventAddFormFields', 2103);
                      $do_userform->form->addEventAction('do_userform->eventCheckEmptyFields', 700);
                      $do_userform->form->goto = $cfg_plugin_mkt_path.'WebFormUrl';
                      echo $do_userform->form->getFormHeader();
                      echo $do_userform->form->getFormEvent();
                      
                      echo '<b>'._('Form Title').':</b>'. $do_userform->title;
                      
                      echo '<br/><b>'._('Form Description').'</b><br/>'.$do_userform->description;
                      $do_webform = new WebFormField();
                      $do_webform->getAll('display_order');
                      echo $do_webform->displayFields();
                      echo '<br/><b>'._('Tags for those contacts').':</b></b> '.$do_userform->tags;
                      echo '<br/><b>'._('Web address to take the user after submitting the form').':</b> '.$do_userform->urlnext;
                      echo '<br/><b>'._('Receive an email alert when someone submit the form').':</b> '.$do_userform->email_alert;
                      echo '<div align="right">'.$do_userform->form->getFormFooter(_('Create')).'</div>';
                  }
	 ?>
        </div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
