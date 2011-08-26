<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

    $pageTitle = 'Ofuz :: '._('Co-Workers');
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
    $do_project = new Project();
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

function showSharedDetail(divid){
  $("#"+divid).fadeIn("slow");
}

function hideSharedDetail(divid){
  $("#"+divid).fadeOut("slow");
}

//]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
	<?php
	  $GLOBALS['page_name'] = 'os_co_worker';
	  include_once('plugin_block.php');
	?>
    </td><td class="layout_rcolumn">
        <!--<div class="mainheader">
            <div class="pad20">
                <span class="headline14">Co-Workers</span>-->
              <div class="contentfull">
                          <?php
                            $_SESSION['do_coworker']->getAllRequest(); //Get all the requests
                            if($_SESSION['do_coworker']->getNumrows()){
                              echo '<br /><b>'._('The following User(s) are waiting for approval from you to become co-workers.').'</b><br />';
                              while($_SESSION['do_coworker']->next()){
                                $user_name = $_SESSION['do_User']->getFullName($_SESSION['do_coworker']->iduser);

                                $e_accept = new Event("do_coworker->eventAcceptInvitation");
                                $e_accept->addParam("id",$_SESSION['do_coworker']->iduser_relations);
                                $e_accept->addParam("goto",$_SERVER['PHP_SELF']);
                                $e_accept->addParam("coworker",$_SESSION['do_coworker']->idcoworker);
                                $e_accept->addParam("user",$_SESSION['do_coworker']->iduser);

                                $e_reject = new Event("do_coworker->eventRejectInvitation");
                                $e_reject->addParam("id",$_SESSION['do_coworker']->iduser_relations);
                                $e_reject->addParam("goto",$_SERVER['PHP_SELF']);
                                echo '<div style="width:200px;float:left;">'.$user_name.'</div><div style="position:relative;">'.$e_accept->getLink("Accept").'&nbsp;&nbsp;'.$e_reject->getLink("Reject").'</div>';
                              }
                            }else{
                              //echo '<br /><b>You have no Invitations to be a Co-Worker.</b><br />';
                            }
                            //echo '<div class="solidline"></div>';
                          ?>
           <!-- </div>
        </div>
        <div class="contentfull">-->
                     <?php
                          $set_share = false;
                          if(isset($_POST['ck'])){
                            $contact_ids = $_POST['ck'];
                            $set_share = true; // Request comes from Contact page
                          }
                          $_SESSION['do_coworker']->getAllCoWorker(); //Get all the co-workers
                          if (!is_object($_SESSION['do_contact_sharing'])) {
                            $do_contact_sharing = new ContactSharing();
                            $do_contact_sharing->sessionPersistent("do_contact_sharing", "index.php", 36000);
                           }
                           if($_SESSION['do_coworker']->getNumrows()){
                              if (!is_object($_SESSION['do_Contacts'])) {
                                  $do_Contacts = new Contact();
                                  $do_Contacts->setRegistry("all_contacts");
                                  $do_Contacts->sessionPersistent("do_Contacts", "index.php", 36000);
                              }
                               if(!$set_share){ // If not having POST vales
                                  echo '<b>'._('Your co-workers :').'</b><br /><br />';
                                  $user_coworker = new User();
                                  while($_SESSION['do_coworker']->next()){
                                      $user_coworker->getId($_SESSION['do_coworker']->idcoworker);
                                      // Get the contacts shared for this co-worker
                                      $shared_contacts = $_SESSION['do_contact_sharing']->getSharedContacts($_SESSION['do_coworker']->idcoworker);
                                      if($shared_contacts && is_array($shared_contacts)){ 
                                        $ids_shared_as_user = implode(",",$shared_contacts);
                                      }else{$ids_shared_as_user = '';}
                                      // Create filter event link
                                      $e_shared_contacts_filter = new Event("do_Contacts->eventFilterContactAsCoWorker");
                                      $e_shared_contacts_filter->addParam("ids",$ids_shared_as_user);
                                      $e_shared_contacts_filter->addParam("goto","contacts.php");
                                      $e_shared_contacts_filter->addParam("setShare","No");
                                      $e_shared_contacts_filter->addParam("coworker",$_SESSION['do_coworker']->idcoworker);
                                      // Get Contacts shared by this contact
                                      $shared_contacts_from_coworker = $_SESSION['do_contact_sharing']->getSharedContactsByCoWorker($_SESSION['do_coworker']->idcoworker);  
                                      if($shared_contacts_from_coworker && is_array($shared_contacts_from_coworker)){
                                        $ids_shared_from_coworker = implode(",",$shared_contacts_from_coworker);
                                      }else{$ids_shared_from_coworker = '';}
                                      $e_shared_contacts_from_coworker_filter = new Event("do_Contacts->eventFilterContactAsCoWorker");
                                      $e_shared_contacts_from_coworker_filter->addParam("ids",$ids_shared_from_coworker);
                                      $e_shared_contacts_from_coworker_filter->addParam("goto","contacts.php");
                                      $e_shared_contacts_from_coworker_filter->addParam("setShare","Yes");
                                      $e_shared_contacts_from_coworker_filter->addParam("coworker",$_SESSION['do_coworker']->idcoworker);
                                      $no_cont_shared = $_SESSION['do_contact_sharing']->countSharedContacts($_SESSION['do_coworker']->idcoworker);
                                      $no_cont_shared_by_co_worker =$_SESSION['do_contact_sharing']->countSharedContactsByCoWorker($_SESSION['do_coworker']->idcoworker);
                                      echo '<div style="width:auto;"><a onmouseover = "showSharedDetail(\''.$_SESSION['do_coworker']->idcoworker.'\');" onmouseout = "hideSharedDetail(\''.$_SESSION['do_coworker']->idcoworker.'\')">'
                                              .$user_coworker->getFullName().
                                            '</a></div>
                                             &nbsp;';

				      $num_project_shared = $do_project->getNumProjectsShared($_SESSION["do_User"]->iduser,$_SESSION['do_coworker']->idcoworker);
				      $no_proj_shared_by_co_worker = $do_project->getNumProjectsShared($_SESSION['do_coworker']->idcoworker,$_SESSION["do_User"]->iduser);
				      
				      echo '<div id ="'.$_SESSION['do_coworker']->idcoworker.'" style="display:none;">';
                                      if ($no_cont_shared > 0) {
					//$e_shared_contacts_filter->getLink( 
                                        echo  '<span>
                                               '.sprintf(_('You shared %d contacts'), $no_cont_shared).' 
                                            </span>
                                            &nbsp;'.sprintf(_('and %d projects'),$num_project_shared).'&nbsp;&nbsp;';
                                      }else{
					  echo '<span>'.sprintf(_('You shared %d contacts'), $no_cont_shared).'</span>&nbsp;'.sprintf(_('and %d projects'),$num_project_shared).'&nbsp;&nbsp;';
				      }

                                      if ($no_cont_shared_by_co_worker > 0) {
				      //$e_shared_contacts_from_coworker_filter->getLink(
                                      echo    '<span>' 
                                               .sprintf(_("%s shared %d contacts"), $user_coworker->firstname, $no_cont_shared_by_co_worker)
                                            .'</span>&nbsp;'.sprintf(_('and %d projects'),$no_proj_shared_by_co_worker);
                                      }else{
					  echo '<span>' 
                                               .sprintf(_("%s shared %d contacts"), $user_coworker->firstname, $no_cont_shared_by_co_worker)
                                            .'</span>&nbsp;'.sprintf(_('and %d projects'),$no_proj_shared_by_co_worker);
				      }
                                      //if ($no_cont_shared > 0 || $no_cont_shared_by_co_worker > 0) {
                                        //echo '<br />';
                                      //}
				      echo '</div>';
				      echo '<br />';
                                      
                                  }// class="co_worker_pending"
                              }else{ // Having some POST data from contacts.php 
                                   $e_share_cont = new Event("do_contact_sharing->eventShareContactsMultiple");
                                   $e_share_cont->addEventAction("mydb.gotoPage", 304);
                                   $e_share_cont->addParam("goto", "co_workers.php");
                                   $e_share_cont->addParam("idcontacts",$contact_ids);
                                   echo $e_share_cont->getFormHeader();
                                   echo $e_share_cont->getFormEvent();

                                   echo '<b>'._('Choose co-workers for sharing the contacts :').'</b><br />';
                                   echo '<div id="coworker_ctlbar" style="display: none;">';
                                   echo '<span class="redlink"><a href="#" onclick="setContactForCoworker(); return false;" style="font-size:20px;">'._('Share').'</a></span>';
                                   echo '</div>';
                                   $user_coworker = new User();
                                   while($_SESSION['do_coworker']->next()){
                                      $user_coworker->getId($_SESSION['do_coworker']->idcoworker);
                                      echo '<div class="contact" id="cw'.$_SESSION['do_coworker']->idcoworker.'" onclick="fnHighlightCoworkers(\''.$_SESSION['do_coworker']->idcoworker.'\')">';
                                      echo '<input type="checkbox" class="ofuz_list_checkbox" name="cwid[]" id="cwid'.$_SESSION['do_coworker']->idcoworker.'" value="'.$_SESSION['do_coworker']->idcoworker.'" onclick="fnHighlightCoworkers(\''.$_SESSION['do_coworker']->idcoworker.'\')">&nbsp;&nbsp;';
                                      $no_cont_shared = $_SESSION['do_contact_sharing']->countSharedContacts($_SESSION['do_coworker']->idcoworker);

                                       $no_cont_shared_by_co_worker =$_SESSION['do_contact_sharing']->countSharedContactsByCoWorker($_SESSION['do_coworker']->idcoworker);

                                      echo '<div style="width:auto;">'
                                              .$user_coworker->getFullName().
                                            '</div>
                                             &nbsp;';
                                    /**  echo   '<span>
                                               '._('You have shared').' '.$no_cont_shared.' '._('contacts').' 
                                            </span>
                                            &nbsp;&nbsp;';
                                      echo  '<span>' 
                                               .$no_cont_shared_by_co_worker.' '. _('contacts are shared by').' '.$_SESSION['do_User']->getFullName($_SESSION['do_coworker']->idcoworker).
                                            '</span>';
                                            **/
                                      echo '</div>';
                                      echo '<div class="solidline"></div>';
                                  } 
                                echo '</form>';
                              }
                           }else{
                               echo '<b>'. _('You have no co-workers').'</b>';
                           }
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
