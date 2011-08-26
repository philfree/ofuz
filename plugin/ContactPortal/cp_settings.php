<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * Page to set the contact portal.
      * @author SQLFusion's Dream Team <info@sqlfusion.com>
      * @package OfuzContactPortal
      * @license ##License##
      * @version 0.6.2
      * @date 2010-11-13
      * @since 0.6.2
      */
   

    if(!is_object($_SESSION['do_contact_portal'])){
      $do_contact_portal = new ContactPoral(); 
      $do_contact_portal->getId($_SESSION['do_cont']->idcontact);
      $do_contact_portal->sessionPersistent("do_contact_portal", "index.php", OFUZ_TTL);
    }
   // $this_page_url = "/PlugIn/ContactPortal/settings";

?>
<script language="javascript">
function showMsgBox() {
	$("#portal_msg").slideToggle("slow");
}
    //]]>
</script>

                    <?php
                    $msg = new Message();
                    if ($_SESSION['in_page_message']) {
                        $msg->getMessage($_SESSION['in_page_message']);
                        echo $msg->displayMessage();
                        unset($_SESSION['in_page_message']);
                    }
                    ?>
                    <div class="dottedline"></div>
                          <?php
                          //checks if already Notes Shared (Url link is generated)
                          $portal_code = $_SESSION['do_contact_portal']->checkIfNotesShared($idcontact);
                          //generating an Event to create an unique URL link to Share Notes & Files
                          $e_generate_url = new Event("do_contact_portal->eventGenerateSharedUrl");
                          //$e_generate_url->addParam("idcontact",$idcontact);
                          $e_generate_url->addParam("goto", $plugin->getCurrentPageUrl());
                          //generating an Event to create an new unique URL link to Share Notes & Files
                          $e_generate_new_url = new Event("do_contact_portal->eventGenerateNewSharedUrl");
                          //$e_generate_new_url->addParam("idcontact", $idcontact);
                          $e_generate_new_url->addParam("goto",$plugin->getCurrentPageUrl());
                          //generating an Event to stop sharing the Notes & Files
                          $e_stop_sharing_notes = new Event("do_contact_portal->eventStopSharingNotes");
                          //$e_stop_sharing_notes->addParam("idcontact", $idcontact);
                          $e_stop_sharing_notes->addParam("goto", $plugin->getCurrentPageUrl());

                          if($portal_code){
                            $msg->getMessage("share file and notes settings");
                            echo $msg->displayMessage();
            
                          ?>
                    <div class="section20">
                       <div><?php echo _('The link bellow is a place where you and ')?><?php echo "<b>".$do_contact->firstname." ".$do_contact->lastname."</b>"; ?><?php echo _(' can share files, documents and notes with you.'); ?></div>
                       <div class="dottedline"></div>
                       <div id="share_file_notes_url" class="instruction_copy_past">   <b><?php //echo _('contact portal'); ?></b>  
                          <?php echo "<a href='".$GLOBALS['cfg_ofuz_site_http_base']."cp/".$portal_code."'>".$GLOBALS['cfg_ofuz_site_http_base']."cp/".$portal_code."</a>"; ?>
                       </div>
					   <div class="dottedline"></div>
                      <div id="share_file_notes_generate">
					    <ul>
                          <li><?php echo $e_generate_url->getLink("Send the address link by email"); ?></li> 
						  <li><?php echo $e_generate_new_url->getLink(_('Generate a new address link')); ?> </li>
						  <li><?php echo $e_stop_sharing_notes->getLink(_('Stop sharing')); ?></li>
						  <li>
							<?php
							$do_contact_msg = new ContactMessage();
							$pers_msg = $do_contact_msg->getPersonalizedMessage($_SESSION['do_contact_portal']->idcontact,$_SESSION['do_User']->iduser);
							?>
							<a href="#" onclick="showMsgBox(); return false;"><?php echo _('Set a personalized message'); ?></a>
							<div id="portal_msg" style="display:none;">
								<?php
									$e_portal_msg = new Event("ContactMessage->eventSetPersonalizedMessage");
									$e_portal_msg->addParam("idcontact", $_SESSION['do_contact_portal']->idcontact);
									echo $e_portal_msg->getFormHeader();
									echo $e_portal_msg->getFormEvent();
								?>
								<textarea name="per_msg" rows="3" cols="50"><?php if($pers_msg){echo $pers_msg;}?></textarea>
								<?php
									echo $e_portal_msg->getFormFooter("Submit");
								?>
							</div>
						  </li>
						</ul>
                      </div>
                    </div>
                          <?php
                          } else{
                          ?>
                    <div class="section20">
                        <?php
							$msg->getMessage("share file and notes initialisation");
							echo $msg->displayMessage();
                        ?><br/>
						<div class="dottedline"></div>
                       <div id="sharenotes" class="section20">
                         <div class="instruction_copy_past">
                              <?php echo $e_generate_url->getLink(_('Click Here to Turn On <br>File Sharing with '))."<b>".$do_contact->firstname." ".$do_contact->lastname."</b>"; ?>
                         </div>
                        </div>
                    </div>
                            <?php } ?>
                    <div id="share_file_notes_msg"><?php if($_GET['msg']){ echo htmlentities($_GET['msg']);} ?></div>
        </div>
 
