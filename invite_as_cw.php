<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Share Notes and Files';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

?>
<script type="text/javascript">
    //<![CDATA[
    var openform;
    function fnEditTask(task) {
        if ($("#e"+openform).length > 0) fnCancelEdit(openform);
        openform = task;
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("Task->eventAjaxEditTaskForm");
$e_editForm->setEventControler("ajax_evctl.php");
$e_editForm->setSecure(false);
?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "id="+task,
            success: function(html){
            	$("#t"+task).hide(0);
            	$("#e"+task)[0].innerHTML = html;
                $("#e"+task).show(0);
            }
        });
    };
    function fnCancelEdit(task) {
        $("#e"+task).hide(0);
        $("#e"+task)[0].innerHTML = "";
        $("#t"+task).show(0);
    };
    function fnTaskComplete(task) {
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("Task->eventAjaxTaskComplete");
$e_editForm->setEventControler("ajax_evctl.php");
$e_editForm->setSecure(false);
?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "id="+task,
            success: function(){
                $("#t"+task).css("text-decoration", "line-through");
                $("#t"+task).fadeOut("slow", function() {
                    $("#e"+task).remove();
                    $("#b"+task).remove();
                });
            }
        });
    };
    $(document).ready(function() {
    	$("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
    });

function showMsgBox() {
	$("#portal_msg").slideToggle("slow");
}
    //]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>

    <table class="layout_columns"><tr><td class="layout_lcolumn">
        &nbsp;
    </td><td class="layout_rcolumn">
    <?php
        $msg = new Message(); 
        

        if($_SESSION['in_page_message'] != ''){
            $msg->setData(Array("enc_email" => $_SESSION['in_page_message_data']['enc_email']));
            $msg->getMessage($_SESSION['in_page_message']);
            $msg->displayMessage();
            $_SESSION['in_page_message'] = '';
        }else{
            $msg->getMessage("invite_contact_cw");
            echo $msg->displayMessage();
        }
    ?>
        <div class="mainheader">
            <div class="pad20">
                <span class="headline14"><?php echo _('Invite as Co-Worker'); ?></span>
            </div>
        </div>
           <?php
               if ($_GET['message']) {
                echo '<div class="messageshadow"><div class="messages">'.htmlentities(stripslashes($_GET['message'])).'</div></div><br />';
               }
           ?>
        <div class="contentfull">
                     <?php
						 /* $idcontact = $_SESSION["eDetail_invite_cw"]->getparam("idcontact");
						  $firstname = $_SESSION["eDetail_invite_cw"]->getparam("firstname");
						  $lastname = $_SESSION["eDetail_invite_cw"]->getparam("lastname");
						  $email = $_SESSION["eDetail_invite_cw"]->getparam("email");
						  $name = $firstname." ".$lastname;
       */
        $idcontact = $do_contact->idcontact;
        $firstname = $do_contact->firstname;
        $lastname = $do_contact->lastname;
        $email = $do_contact->email;
        $name = $firstname." ".$lastname;
    

						  if(!is_object($_SESSION['do_cont'])){
            $do_cont = new Contact();
            $do_cont->sessionPersistent("do_cont", "index.php", OFUZ_TTL);
            $_SESSION['do_cont']->idcontact = $idcontact;
						  }

						  $do_cont_emails = new ContactEmail();
						  $arr_contact_emails = $do_cont_emails->getContactEmails($idcontact);

						  $do_co_worker = new UserRelations();
        $do_user = new User();
        $do_user->getUserDetailsByNameEmail($firstname, $lastname, $arr_contact_emails);

          if($do_user->getNumRows()){

            echo '<div class="solidline"></div>';
            while($do_user->next()){
                echo '<div class="contacts" >';
                echo '<div class="contacts_desc">';

                $full_name = $do_user->firstname.' '.$do_user->lastname;
                $invite = "Invite ".$full_name;
                $e_add_coworker = new Event("UserRelations->eventAddAsCoWorker");
                $e_add_coworker->addParam("iduser",$_SESSION['do_User']->iduser);
                $e_add_coworker->addParam("idcoworker",$do_user->iduser);
                $e_add_coworker->addParam("goto",$_SERVER['PHP_SELF']);
                $relation = $do_co_worker->getCoWorkerRelationData($do_user->iduser);

                //echo $e_add_coworker->getLink($invite);

                if($relation && is_array($relation)){
                  if($relation['accepted'] == 'Yes'){
                    echo '<i>'._($full_name.' is already a Co-Worker').'</i>';
                    $str_invite = _('I would like to invite someone else.');
                  }elseif($relation['accepted'] == 'No'){
                    echo '<i>'._('Invitation sent, waiting for confirmation').'</i>';
                    $str_invite = _('I would like to invite someone else.');
                  }else{
                    echo $e_add_coworker->getLink($invite);
                    $str_invite = _('No, this is not the contact I know.Let me send the invitation to:');
                  }
                }else{
                    echo $e_add_coworker->getLink($invite);
                    $str_invite = _('No, this is not the contact I know.Let me send the invitation to:');
                }

                echo '</div>';
                echo '</div>';
                echo '<div class="spacerblock_2"></div>';
                //echo '<div class="solidline"></div>';
              }
            }else{
								
              echo '<i>'._($name).'</i>'; 
              $str_invite = _('To invite ').$name.', '._('please choose or type Email address:');
            }
            echo '<br />';
            echo '<br />';
            echo '<span style="color:green;">';
            echo _($str_invite);
            echo '</span>';
            echo '<br />';
            /*if (!is_object($_SESSION['do_coworker_add'])) {
              $co_worker_form = new UserRelations();
              $co_worker_form->sessionPersistent("do_coworker_add", "index.php", 36000);
            }*/
            $do_co_worker->generateFromAddContactAsCoWorker($_SERVER['PHP_SELF']);
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
