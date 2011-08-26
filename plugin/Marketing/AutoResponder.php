<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $do_auto_responder = new AutoResponder();
    $do_auto_responder->sessionPersistent('do_auto_responder', 'contacts.php', OFUZ_TTL);
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() {
    	$("div[id^=autores]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
    });


function addAutoResponder(){
     $("#ptask_ctlbar_1").slideToggle("slow");
}
//]]>
</script>
    <?php
          if($_SESSION['in_page_message'] != ''){
			  $msg = new Message();
			  $msg->setContent(htmlentities($_SESSION['in_page_message']));
			  $msg->displayMessage();
          }
    ?> 
    
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text">
          <?php 
              echo _('Auto Responder'); 
              echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
              echo '<a href = "#" onclick ="addAutoResponder();return false;">'._('Create New').'</a>';
          ?>
        </div>
        
            <?php
				$msg = new Message(); 
				//echo $msg->getMessageFromContext('autoresponder instruction');
                                if ($msg->getMessageFromContext("autoresponder instruction")) {
				    echo $msg->displayMessage();
			         }
            ?>
           
        <div id="ptask_ctlbar_1" style = "display:none;">
         <?php
              
              $_SESSION['do_auto_responder']->newAddForm();
              $_SESSION['do_auto_responder']->form->addEventAction('do_auto_responder->eventCheckEmptyFields', 700);
              $_SESSION['do_auto_responder']->setRegistry('autoresponder_add');
              $_SESSION['do_auto_responder']->form->goto = $_PHP['SELF'];
              echo $_SESSION['do_auto_responder']->form->getFormHeader();
              echo $_SESSION['do_auto_responder']->form->getFormEvent();
              $_SESSION['do_auto_responder']->iduser = $_SESSION['do_User']->iduser;
              echo $_SESSION['do_auto_responder']->iduser;
              echo '<b>'._('Auto Responder Name').':</b>'. $_SESSION['do_auto_responder']->name;
              echo '<br/><br/><b>'._('Tag Name').':</b>'. $_SESSION['do_auto_responder']->tag_name;
              echo '<div align="right">'.$_SESSION['do_auto_responder']->form->getFormFooter(_('Create')).'</div>';
         ?>
        </div>
        <div class="solidline"></div>
        <?php
                $_SESSION['do_auto_responder']->setApplyRegistry(false,"Form");
                $_SESSION['do_auto_responder']->getUserSavedAutoResponders();
                if($_SESSION['do_auto_responder']->getNumRows()){
                      $del_auto_responder = new Event('do_auto_responder->eventDelAutoResponder');
                      $del_auto_responder->addParam('goto', $cfg_plugin_mkt_path.'/Autoresponder/');
                      $item_count = 0;
                      while($do_auto_responder->next()){
                             $item_count++;
                             echo '<div class="contacts"  id="autores'.$item_count.'">';
                             echo '<div class="contacts_desc">';
                             echo '<span class="contacts_name" >';
                             
                             echo '<a href="'.$cfg_plugin_mkt_path.'AutoResponderEmail/'.$_SESSION['do_auto_responder']->idautoresponder.'">'.$_SESSION['do_auto_responder']->name.'</a>';
							 echo " (".$_SESSION['do_auto_responder']->tag_name.")";
                             echo '</span>';
                             echo '</div>';
                             $del_auto_responder->addParam('id', $_SESSION['do_auto_responder']->idautoresponder);
                             $del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';

                             echo '<div id="trashcan'.$item_count.'" align="right" style="margin-left:400px;display:none;position:absolute;">'.'<a href="/settings_auto_responder_edit.php?id='.$_SESSION['do_auto_responder']->idautoresponder.'">edit &nbsp;&nbsp;|&nbsp;&nbsp;'.$del_auto_responder->getLink($del_img_url, ' title="'._('Delete this Auto Responder').'" onclick="if (!confirm(\''._('Do you really want to delete?').'\')) return false;"').'</div>';
                             
                             echo '</div>';
                             echo '<div class="spacerblock_2"></div>';
                             echo '<div class="solidline"></div>';
                      }   
                }else{
                    echo '<div style="margin-left:0px;">';
                    echo '<div class="messages_unauthorized">';
                    echo '<b>'._('You do not have any auto responder. To add click ').'<a href = "#" onclick ="addAutoResponder();return false;">'._('here').'</a></b>';
                    echo '</div></div>';
                }
	         
	 ?>