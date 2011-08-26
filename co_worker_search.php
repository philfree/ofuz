<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Co-Workers';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_notes = new ContactNotes($GLOBALS['conx']);
    $do_contact = new Contact($GLOBALS['conx']);
    $do_company = new Company($GLOBALS['conx']);
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory($GLOBALS['conx']);
    $do_contact_task = new Contact();
?>
<script type="text/javascript">
//<![CDATA[
function fnHighlightCoworkers(area) {
	var cwid=$("#cwid"+area);
	var div=$("#cw"+area);
	var ctlbar=$("#coworker_ctlbar");
    cwid.attr("checked",(cwid.is(":checked")?"":"checked"));
    if (cwid.is(":checked")) {
        div.css("background-color", "#ffffdd");
        if(ctlbar.is(":hidden"))ctlbar.slideDown("fast");
    } else {
        div.css("background-color", "#ffffff");
        var tally=0;
        $("input[type=checkbox][checked]").each(function(){tally++;});
        if(tally==0)ctlbar.slideUp("fast");
    }
}

function setContactForCoworker(){
  $("#do_contact_sharing__eventShareContactsMultiple").submit();
}

$(document).ready(function() {
    	$("div[id^=invite]").hover(function(){$("div[id^=trashcan]",this).show("slow");},function(){$("div[id^=trashcan]",this).hide("slow");});
    });
//]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $GLOBALS['thistab'] = _('Co-Workers'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Find Co-Workers'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
                <div><?php echo _('Search for Co-Workers by first or last name:');?></div>
                <?php 
                    if(!is_object($_SESSION['do_User_search'])) {
                          $do_User_search = new User();
                          $do_User_search->sessionPersistent("do_User_search", "logout.php", 36000);
                    }
                    $e_search = new Event("do_User_search->eventSetSearchByName");
                    $e_search->setLevel(20);
                    $e_search->addParam("goto", "co_worker_search.php");
                    echo $e_search->getFormHeader();
                    echo $e_search->getFormEvent();
                 ?>
                   <div class="marginform">
                      <input type="Text" name = "search_txt" id = "search_txt" value = "<?php echo $_SESSION['do_User_search']->user_search_txt;?>">
                   </div>
                    <div class="dottedline"></div>
                    <div class="section20">
                      <input type="submit" value="Search" />
                    </div>
                  </form><!-- Need the </form> -->
            </div>
        </div>
        <div class="left_menu_footer"></div><br />

        <div class="left_menu_header">
            <div class="left_menu_header_content"><?php echo _('Invitations'); ?></div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
                <?php
                    $do_check_rejected = false ;
                    if (!is_object($_SESSION['do_coworker'])) {
                        $co_worker_form = new UserRelations();
                        $co_worker_form->sessionPersistent("do_coworker", "index.php", 36000);
                    }
                    $_SESSION['do_coworker']->getAllRequestsRejected(); //Get all the requests
                    if($do_check_rejected === true ){
                      
                 ?>
                <div class="co_worker_rejected">
                    <div class="headline10" style="color: #ff0000;"><?php echo _('Invitations Rejected'); ?></div>
                        <?php
                            while ($do_check_rejected === true ) {
                         ?>
                        <span class="co_worker_item">
                            <span class="co_worker_desc">
                                <?php 
                                    echo $_SESSION['do_User']->getFullName($_SESSION['do_coworker']->idcoworker);
                                 ?>
                            </span>
                        </span>
                        <?php } ?>
                    </div>
                <?php  
                    }
                    $_SESSION['do_coworker']->getAllRequestsSent();
                    if($_SESSION['do_coworker']->getNumrows()){
                 ?>
                    <div class="co_worker_pending">
                        <div class="headline10"><?php echo _('Invitations Pending');?></div>
                        <?php
                            while ($_SESSION['do_coworker']->next()) {
                                $e_remove_invitation =  new Event("do_coworker->eventRemoveInvitation");
                                $e_remove_invitation->addParam('id',$_SESSION['do_coworker']->iduser_relations);
                                $e_remove_invitation->addParam("goto",$_SERVER['PHP_SELF']);
                        ?>
                        <span class="co_worker_item">
                            <span class="co_worker_desc">
                            <?php  
                                echo '<div id="invite', $count, '" class="co_worker_item co_worker_desc">'; 
                                echo '<div style="position: relative;">';
                                if( $_SESSION['do_coworker']->idcoworker ){
                                    echo $_SESSION['do_User']->getFullName($_SESSION['do_coworker']->idcoworker);
                                } else {
                                    echo $_SESSION['do_coworker']->decrypt($_SESSION['do_coworker']->enc_email);
                                }
                               $img_del = '<img src="/images/delete.gif" width="14px" height="14px" alt="" />';
                                echo '<div width="15px" id="trashcan', $count, '" class="deletenote" style="right:0;">'.$e_remove_invitation->getLink($img_del).'</div>';
                                echo '</div></div>';
                             ?>
                            </span>
                        </span><br />
                    <?php } ?>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
        <div class="left_menu_footer"></div><br />
    </td><td class="layout_rcolumn">
       <!-- <div class="mainheader">-->
           <?php
               if($_SESSION['in_page_message'] != ''){
                $msg = new Message();
                if($_SESSION['in_page_message'] == "cw_already-have-pending-invitation-to") {
                  $msg->setData(Array("enc_email" => $_SESSION['in_page_message_data']['enc_email']));
                  $msg->getMessage($_SESSION['in_page_message']);
                  $msg->displayMessage();
                } else{
                  $msg->getMessage($_SESSION['in_page_message']);
                  $msg->displayMessage();
                }
               }
           ?>
        <!--</div>-->
        <div class="contentfull">
                     <?php
                          
                           $_SESSION['do_User_search']->query($_SESSION['do_User_search']->getSqlQuery());
                           if($_SESSION['do_User_search']->getNumRows()){
                              echo '<b>'._('Search Result').'</b><br />';
                              echo '<div class="solidline"></div>';
                              while($_SESSION['do_User_search']->next()){
                                  echo '<div class="contacts" >';
                                  echo '<div class="contacts_desc">';
                                  echo '<span class="contacts_name">';
                                  echo $_SESSION['do_User_search']->firstname.' '.$_SESSION['do_User_search']->lastname;
                                  echo '</span><br />';
                                  $e_add_coworker = new Event("do_coworker->eventAddAsCoWorker");
                                  $e_add_coworker->addParam("iduser",$_SESSION['do_User']->iduser);
                                  $e_add_coworker->addParam("idcoworker",$_SESSION['do_User_search']->iduser);
                                  $e_add_coworker->addParam("goto",$_SERVER['PHP_SELF']);
                                  $relation = $_SESSION['do_coworker']->getCoWorkerRelationData($_SESSION['do_User_search']->iduser);
                                  if($relation && is_array($relation)){
                                    if($relation['accepted'] == 'Yes'){
                                      echo '<i>'._('Already a Co-Worker').'</i>';
                                    }elseif($relation['accepted'] == 'No'){
                                      echo '<i>'._('Invitation sent, waiting for confirmation').'</i>';
                                    }else{
                                      echo $e_add_coworker->getLink(_('Add as Co-Worker'));
                                    }
                                  }else{
                                      echo $e_add_coworker->getLink(_('Add as Co-Worker'));
                                  }
                                  echo '</div>';
                                  echo '</div>';
                                  echo '<div class="spacerblock_2"></div>';
                                  echo '<div class="solidline"></div>';
                              }
                           }else{         
                              printf(_('%s was not found in Ofuz'), $_SESSION['do_User_search']->user_search_txt);
                           }
                           echo '<br />';
                           echo '<br />';
                           echo '<b>';
                           echo _('If your Co-Worker is not yet registered in Ofuz you can send an invititation by email:');
                           echo '</b>';
                           echo '<br />';
                           /*if (!is_object($_SESSION['do_coworker_add'])) {
                              $co_worker_form = new UserRelations();
                              $co_worker_form->sessionPersistent("do_coworker_add", "index.php", 36000);
                           }*/
                           $_SESSION['do_coworker']->generateFromAddCoWorker($_SERVER['PHP_SELF']);
                     ?>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>