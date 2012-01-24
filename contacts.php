<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Page to list all the invoices
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzPage
     * @license GNU Affero General Public License
     * @version 0.6
     * @date 2010-09-06
     * @since 0.1
     */

    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    $pageTitle = _('Contacts').' :: Ofuz ';
    include_once('includes/header.inc.php');

	//rebuilding contact view if it's empty
	$do_cv = new ContactView();
	if($do_cv->isContactViewEmpty()) {
		$do_con = new Contact();
		if($do_con->getTotalNumContactsForUser($_SESSION["do_User"]->iduser)) {
			$do_cv->rebuildContactUserTable();
		}
	} 

    if (!is_object($_SESSION['do_Contacts'])) {
        $do_Contacts = new Contact();
       // $do_Contacts->get_all_contacts();
        $do_Contacts->setRegistry("all_contacts");
        $do_Contacts->sessionPersistent("do_Contacts", "index.php", 36000);
		//$_SESSION['refresh_contacts'] = true;
		$do_Contacts->query("SELECT * FROM ".$do_Contacts->getSqlViewName()." ORDER BY ".$do_Contacts->sql_view_order." LIMIT ".$do_Contacts->sql_qry_start.",".$do_Contacts->sql_view_limit);
    } else {
      ///$_SESSION['do_Contacts']->setLog("\n ----------------- \n [Contact] :: contacts page query:".$_SESSION['do_Contacts']->getSqlQuery()."\n --------------\n");
      $_SESSION['do_Contacts']->query();
      //echo $_SESSION['do_Contacts']->getSqlQuery();
		if($_SESSION['do_Contacts']->contact_count == 0 )
			$_SESSION['do_Contacts']->contact_count = $_SESSION['do_Contacts']->getNumRows();
		//echo "Count: ".$_SESSION['do_Contacts']->contact_count;
    }
    $show_companies = false;

    if ($show_companies) {
        $do_Companies = new Company();
        $do_Companies->get_all_companies();
        $do_Companies->setRegistry("company_full_info");
    }

	/**
	 * resetting the sql query limit start whenever this page is loaded
	 */
	if(is_object($_SESSION['do_Contacts']) ) {
		//$_SESSION['do_Contacts']->sql_qry_start = 0;
	}
	

?>
<script type="text/javascript">
//<![CDATA[

<?php include_once('includes/ofuz_js.inc.php'); ?>

function eventActionMultiple(eventaction, confirm_message) {
		if (confirm_message.length > 2) {
			if (confirm(confirm_message)) {
				$("#do_Contacts__eventAddTagMultiple_mydb_events_100_").attr("value", eventaction);
				$("#do_Contacts__eventAddTagMultiple").submit();	
		    }
		 } else {
			$("#do_Contacts__eventAddTagMultiple_mydb_events_100_").attr("value", eventaction);
			$("#do_Contacts__eventAddTagMultiple").submit();	
		}	
}
function actionMultiple(actionpage, confirm_message) {
		if (confirm_message.length > 2) {
			if (confirm(confirm_message)) {
				$("#do_Contacts__eventAddTagMultiple").attr("action", actionpage);
				$("#do_Contacts__eventAddTagMultiple").submit();	
		    }
		 } else {
			$("#do_Contacts__eventAddTagMultiple").attr("action", actionpage);
			$("#do_Contacts__eventAddTagMultiple").submit();	
		}	
}


function deleteTagMul(){
    $("#do_Contacts__eventAddTagMultiple_mydb_events_100_").attr("value", "do_Contacts->eventDeleteMultipleContactsTag");
    $("#do_Contacts__eventAddTagMultiple").submit();
}

function addTags() {
	$("#do_Contacts__eventAddTagMultiple").submit();
}

var allowHighlight = true;
function fnHighlight(area) {
	if (allowHighlight == false) return;
	var ck=$("#ck"+area);
	var div=$("#cid"+area);
	var ctlbar=$("#contacts_ctlbar");
    ck.attr("checked",(ck.is(":checked")?"":"checked"));
    if (ck.is(":checked")) {
        div.css("background-color", "#ffffdd");
        if(ctlbar.is(":hidden"))ctlbar.slideDown("fast");
    } else {
        div.css("background-color", "#ffffff");
        if($("input:checked").length==0)ctlbar.slideUp("fast");
    }
}
function fnSelAll() {
	//expandContactsOnSelectAll();
    $("input:checkbox").each(function(){this.checked=true;});
    $("div.contacts").css("background-color", "#ffffdd");
}
function fnSelNone() {
    $("input:checkbox").each(function(){this.checked=false;});
    $("div.contacts").css("background-color", "#ffffff");
    $("#contacts_ctlbar").slideUp("fast");
}

// For the ajax suggestions
function lookup(contacts_search) {
    if(contacts_search.length == 0) {
        // Hide the suggestion box.
        $('#suggestions').hide();
    } else {
        $.post("_ajax_get_contact_suggestion.php", {queryString: ""+contacts_search+""}, function(data){
            if(data.length >0) {
                $('#suggestions').show();
                $('#autoSuggestionsList').html(data);
            }
        });
    }
} // lookup

function fill(thisValue) {
    $('#contacts_search').val(thisValue);
   $('#suggestions').hide();
}

function expandContactsOnSelectAll() {

		$('div#last_contact_loader').html('<img src="/images/loader1.gif" alt="" />');
		$.ajax({
			type: "GET",
		<?php
		$e_exp_contacts = new Event("do_Contacts->eventAutoLoadContactsOnSelectAll");
		$e_exp_contacts->setEventControler("ajax_evctl.php");
		$e_exp_contacts->setSecure(false);
		?>
			url: "<?php echo $e_exp_contacts->getUrl(); ?>",
			success: function(data){
					$(".message_box:last").after(data);
					$('div#last_contact_loader').empty();
			}
		});

}

function loadContacts() {
		var search_key = document.getElementById('contacts_search').value;
		var filter = document.getElementById('filter').value;
		//alert(search_key);
		$('div#last_contact_loader').html('<img src="/images/loader1.gif" alt="" />');
		$.ajax({
			type: "GET",
		<?php
		$e_contacts = new Event("do_Contacts->autoLoadContactsOnScrollDown");
		$e_contacts->setEventControler("ajax_evctl.php");
		$e_contacts->setSecure(false);
		?>
			url: "<?php echo $e_contacts->getUrl(); ?>",
			data: "searchkey="+search_key+"&filter="+filter,
			success: function(data){
					$(".message_box:last").after(data);
					$('div#last_contact_loader').empty();
			}
		});

}

$(document).ready(function() {
    $(window).scroll(function(){
        if ($(window).scrollTop() == $(document).height() - $(window).height()){
            loadContacts();
        }
    });
});

function sticky_relocate() {
  var window_top = $(window).scrollTop();
  var div_top = $('#sticky-anchor').offset().top;
  if (window_top > div_top)
    $('#contacts_ctlbar').addClass('stick')
  else
    $('#contacts_ctlbar').removeClass('stick');
  }
 /*
  * Sticky Div
  * On scroll down, the action menu sticks on top
    On scroll up, it comes back to original position
    On selecting the last contact, the action menu sticks on top and display.
  */
 $(function() {
  $(window).scroll(sticky_relocate);
  sticky_relocate();
  });


//]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Contacts'); include_once('includes/ofuz_navtabs.php'); ?>

<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        <div class="left_text_links">
<?php
$button_new_contact = new DynamicButton();
$do_contact_limit = new UserPlan();
if ($do_contact_limit->canUserAddContact()) {
    echo $button_new_contact->CreateButton('/contact_add.php', _('add new contact'), '', '', 'dyn_button_add_new_contact');
} else {
    echo $button_new_contact->CreateButton('/upgrade_your_account.php?msg='.$_SESSION['do_User']->plan.'_c', _('add new contact'), '', '', 'dyn_button_add_new_contact');
}
?>
            <!-- hide for now as its counter intuitive when we do not have listing of companies 
            <br /><br />
            <a href="company_add.php"><?php echo _('Add a new company'); ?></a> //-->
        </div><br /><br />
        <?php
            $GLOBALS['page_name'] = 'contacts';
            include_once('plugin_block.php');
        ?>

    </td><td class="layout_rcolumn">
		<?php
		
            $msg = new Message(); 
			if ($msg->getMessageFromContext("contact list")) {
				echo $msg->displayMessage();
			}
        ?>
        <?php if ($_SESSION['do_Contacts']->getContactCount() == 0) { ?>
        <div class="messageshadow">
            <div class="messages">
            <?php
                $msg = new Message();
                echo $msg->getMessage('no contacts yet');
            ?>
            </div>
        </div>
        <br /><br />
        <?php } ?>
        <div class="mainheader">
                <?php                 
                $e_remove_tag_search = new Event("do_Contacts->eventSearchByTag");
                
                if (count($_SESSION['do_Contacts']->getSearchTags())>0 && is_resource($_SESSION['do_Contacts']->getResultSet())) { 
                ?> <div class="pad20">
                    <h3><?php echo _('Showing contacts with tags:'); ?></h3>
                    <h2><?php 
                        foreach ($_SESSION['do_Contacts']->getSearchTags() as $search_tag) {
                                $e_remove_tag_search->addParam("search_remove_tag_name", $search_tag); 
                                echo " ".$search_tag." <a href=\"".$e_remove_tag_search->getUrl()."\"><img src=\"/images/delete.gif\" width=\"14\" height=\"14\" border=\"0\" alt=\"Remove this tag\" /></a>, ";
                        }
                        ?>
                    </h2>
        </div>
                <?php } else { ?>
                    <div class="tundra">
                    <div class="pad20">
                        <h3><?php echo _('Find someone by typing their name or company'); ?></h3>
                        <?php 
                           $e_search = new Event("do_Contacts->eventSetSearch");
                           $e_search->setLevel(500);
                           $e_search->addParam("goto", "contacts.php");
                        ?>
                        <?php echo $e_search->getFormHeader(); ?>
                        <?php  echo $e_search->getFormEvent(); ?>
                            <div><input type="text" name="contacts_search" id="contacts_search" class="find_someone" value="<?php echo $_SESSION['do_Contacts']->search_keyword; ?>" />
				<?php
				$e_clear_search = new Event("do_Contacts->eventClearSearch");
				$broom = '<img style="vertical-align:middle;" src="/images/broom.png" border="0" title="Clear Search" />';
				echo $e_clear_search->getLink($broom);
				?>

				  <input type="submit" value="<?php echo _('Search');?>"></div>
                            <div class="suggestionsBox" id="suggestions" style="display: none;"></div>
                            <div class="suggestionList" id="autoSuggestionsList"></div>
                        </form>
                       <!-- <span class="text8">or <span class="bluelink"><a href="#" onclick="return false;">search by city, state, country, zip, phone, or email</a></span></span>-->
                       <?php // code to create the filter event 
                            $e_filter = new Event("do_Contacts->eventSetFilter");
                            $e_filter->setLevel(501);
                            $e_filter->addParam("goto", "contacts.php");
                        ?><form id="setFilter" name="setFilter" method="post" action="eventcontroler.php">
                        <?php echo $e_filter->getFormEvent(); ?>
                          <h3><?php echo _('View contacts:');?>
                            <select id="filter" name="filter" onchange='$("#setFilter").submit();'>
                              <option value=""></option>
                              <option value="add"<?php echo $_SESSION['do_Contacts']->getFilter("add"); ?>><?php echo _('Recently added');?></option>
                              <option value="modify"<?php echo $_SESSION['do_Contacts']->getFilter("modify"); ?>><?php echo _('Recently modified');?></option>
                              <option value="view"<?php echo $_SESSION['do_Contacts']->getFilter("view"); ?>><?php echo _('Recently viewed');?></option>
                              <option value="active"<?php echo $_SESSION['do_Contacts']->getFilter("active"); ?>><?php echo _('With recent activity');?></option>
                              <option value="alpha"<?php echo $_SESSION['do_Contacts']->getFilter("alpha"); ?>><?php echo _('Alphabetically'); ?></option>
                            </select>
                          </h3>
                        </form>
                     </div>
                     </div>
                <?php } ?>                 
                </div>
                <?php
                  $e_del_or_tag = new Event("do_Contacts->eventAddTagMultiple");
                  $e_del_or_tag->addEventAction("mydb.gotoPage", 304);
                  $e_del_or_tag->addParam("goto", "contacts.php");
                  echo $e_del_or_tag->getFormHeader();
                  echo $e_del_or_tag->getFormEvent();
		  
                ?>
		<div id="sticky-anchor"></div>
                <div id="contacts_ctlbar" style="display: none;">
                    <b><?php echo _('With the selected contacts you can:');?></b><br />
                    <?php echo _('Add tags')._(':'); ?>
					<?php
						$do_suggest_tag = new Tag();
						$do_suggest_tag->setFields("auto_suggest_tag"); 
						$do_suggest_tag->setApplyRegistry(true, "Form");
						echo $do_suggest_tag->tag_name;
					?>
					<input type="button" name="btnsuggesttag" value="add tags" onclick="addTags();" />
					<div class="spacerblock_5"></div>
    <?php echo _('or ');?><?php echo $GLOBALS['do_tag_list']->generateUserTagsDropDown();?>
    <div class="spacerblock_5"></div>
    <?php echo _('or ');?><span class="redlink"><a href="#" onclick="eventActionMultiple('do_Contacts->eventDeleteMultiple','Are you sure you want to delete the selected contacts?'); return false;"><?php echo _('Delete them');?></a></span>
    <?php if($_SESSION['do_Contacts']->set_unshare){?>
    <br /><?php echo _('or ');?><span class="redlink"><a href="#" onclick="eventActionMultiple('do_Contacts->eventUnShareMultiple','Are you sure you want to Unshare the selected contacts?');return false;"><?php echo _('Un-Share Contact');?></a></span>
    <input type ="hidden" name="co_worker_id" value = "<?php echo $_SESSION['do_Contacts']->unshare_co_worker;?>">
    <?}else{?>
    <br /><?php echo _('or ');?><span class="redlink"><a href="#" onclick="actionMultiple('co_workers.php','');return false;"><?php echo _('Share With Co-Workers');?></a></span>
    <?php } ?>
   <?php 

     foreach ($cfg_plugin_eventmultiple_placement['contacts'] as $event_multiple_plugin) {
						  if (strlen($event_multiple_plugin['event'])>0) {

						     echo "\n<br>"._('or ').'<span class="redlink"><a href="#" onclick="eventActionMultiple(\''.$event_multiple_plugin['event'].'\',\''.$event_multiple_plugin['confirm'].'\');return false;">'._($event_multiple_plugin['name']).'</a></span> ';
						  } elseif (strlen($event_multiple_plugin['action'])>0) {
							 echo "\n<br>"._('or ').
							  '<span class="redlink">
							    <a href="#" onclick="actionMultiple(\''.
							      $event_multiple_plugin['action'].
							      '\',\''.
							      $event_multiple_plugin['confirm'].

							      '\');return false;">'._($event_multiple_plugin['name']).'</a></span> ';
						  }
					  }                    
                    ?>
                  
                    <div class="spacerblock_10"></div>
                    <span class="sasnlinks">( <span class="bluelink"><a href="#" onclick="fnSelAll(); return false;"><?php echo _('select all'); ?></a></span> | <span class="bluelink"><a href="#" onclick="fnSelNone(); return false;"><?php echo _('select none');?></a></span> )</span>
                </div>
                <div class="contentfull">
				<?php
                    if (!is_resource($_SESSION['do_Contacts']->getResultSet())) {
                        $_SESSION['do_Contacts']->query("SELECT * FROM ".$_SESSION['do_Contacts']->getSqlViewName()." ORDER BY ".$_SESSION['do_Contacts']->sql_view_order." LIMIT ".$_SESSION['do_Contacts']->sql_qry_start.",".$_SESSION['do_Contacts']->sql_view_limit);
                    }
                    if( strlen($_SESSION['do_Contacts']->search_keyword) > 0 && $_SESSION['do_Contacts']->getNumRows() == 0 ){
                         echo '<br /><br />';
                         $e_clear_search = new Event("do_Contacts->eventClearSearch");
                         $data = array("click_here"=>$e_clear_search->getLink(_('click here')));
                         $msg = new Message();
                         $msg->setData(array("click_here"=>$e_clear_search->getLink(_('click here'))));
                         $msg->getMessage("no_contact_found");
                         $msg->displayMessage();
                    }else{
                        $_SESSION['do_Contacts']->first();
                        $OfuzList = new OfuzListContact($_SESSION['do_Contacts']);
                        $OfuzList->setMultiSelect(true);
                        $OfuzList->displayList();
                    }
		   
		    
                ?>
                </div>
				<div id="last_contact_loader"></div>
                </form>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>

<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>


</body>
</html>
