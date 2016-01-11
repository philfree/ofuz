<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Contacts list';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/i_header.inc.php');

    if (!is_object($_SESSION['do_Contacts'])) {
        $do_Contacts = new Contact();
       // $do_Contacts->get_all_contacts();
        $do_Contacts->setRegistry("all_contacts");
        $do_Contacts->sessionPersistent("do_Contacts", "index.php", 36000);
    } else {
      //     $_SESSION['do_Contacts']->setLog("\n contacts page query:".$_SESSION['do_Contacts']->getSqlQuery());
      $_SESSION['do_Contacts']->query();
      //echo $_SESSION['do_Contacts']->getSqlQuery();
    }
    $show_companies = false;

    if ($show_companies) {
        $do_Companies = new Company();
        $do_Companies->get_all_companies();
        $do_Companies->setRegistry("company_full_info");
    }


?>
<?php $thistab = 'Contacts'; include_once('i_ofuz_navtabs.php'); ?>
<script type="text/javascript">
//<![CDATA[
function setDel() {
    if (confirm("Are you sure you want to delete the selected contacts?")) {
        $("#do_Contacts__eventDeleteMultiple").submit();
    }
}
function fnHighlight(area) {
	var ck=$("#ck"+area);
	var div=$("#cid"+area);
	var ctlbar=$("#contacts_ctlbar");
    ck.attr("checked",(ck.is(":checked")?"":"checked"));
    if (ck.is(":checked")) {
        div.css("background-color", "#ffffdd");
        if(ctlbar.is(":hidden"))ctlbar.slideDown("fast");
    } else {
        div.css("background-color", "#ffffff");
        var tally=0;
        $("input[type=checkbox][checked]").each(function(){tally++;});
        if(tally==0)ctlbar.slideUp("fast");
    }
}
function fnSelAll() {
    $("input[type=checkbox]").each(function(){this.checked=true;});
    $("div.contact").css("background-color", "#ffffdd");
}
function fnSelNone() {
    $("input[type=checkbox]").each(function(){this.checked=false;});
    $("div.contact").css("background-color", "#ffffff");
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
// Ends here
//]]>
</script>
<div  style="position:absolute; top:2px; right:25px; text-align:right;">
     <a href="i_contact_add.php" ><img src ="images/i_add.png"></a>
</div>
<div class="main mobile_main">
       <div class="mainheader">
                <?php                 
                $e_remove_tag_search = new Event("do_Contacts->eventSearchByTag");
                
                if (count($_SESSION['do_Contacts']->getSearchTags())>0) {  ?>               
                <div class="mobile_head_pad5"><!--class="pad20"-->
                    <h3>Showing contacts with tags: </h3>
                    <div><?php 
                        foreach ($_SESSION['do_Contacts']->getSearchTags() as $search_tag) {
                                $e_remove_tag_search->addParam("search_remove_tag_name", $search_tag); 
                                echo " ".$search_tag." <a href=\"".$e_remove_tag_search->getUrl()."\"><img src = \"images/delentry.gif\" width=\"14\" height=\"14\" border=\"0\" alt=\"Remove this tag\"></a>, ";
                        }
                        ?>
                    </div>
                    <?php   $SearchTags = $_SESSION['do_Contacts']->getSearchTags();
                        if (!empty($SearchTags)) {
                               
                              $do_subtag_list = new Tag();
                              $sub_tag_links = $do_subtag_list->getUserSubTagList("contact");
                            if (strlen(trim($sub_tag_links)) > 0) { ?>
                            <h3>Add tags to narrow your search</h3>
                                <?php 
                                   echo $sub_tag_links; 
                                ?>
                            <br/>
                       <?php } ?>
                    <?php
                         }
                    ?> 
                </div>
                <?php } else { ?>
                    <div class="mobile_head_pad5">                 
                        <h3>Search by name or company</h3>
                        <?php 
                           $e_search = new Event("do_Contacts->eventSetSearch");
                           $e_search->setLevel(500);
                           $e_search->addParam("goto", "i_contacts.php");
                        ?>
                        <form method="post" action="eventcontroler.php">
                        <?php  echo $e_search->getFormEvent(); ?>
                            <div><input type="text" name="contacts_search" id="contacts_search" class="mobile_find_someone" value="<?php echo $_SESSION['do_Contacts']->search_keyword; ?>" /></div>
                            <div class="suggestionsBox" id="suggestions" style="display: none;"></div>
                            <div class="suggestionList" id="autoSuggestionsList"></div>
                        </form>
                       <!-- <span class="text8">or <span class="bluelink"><a href="#" onclick="return false;">search by city, state, country, zip, phone, or email</a></span></span>-->
                       <?php // code to create the filter event 
                            $e_filter = new Event("do_Contacts->eventSetFilter");
                            $e_filter->setLevel(501);
                            $e_filter->addParam("goto", "i_contacts.php");
                        ?> <!-- <form id="setFilter" name="setFilter" method="post" action="eventcontroler.php">
                        <?php echo $e_filter->getFormEvent(); ?>
                          <h3>Filter contacts:
                            <select name="filter" onChange='$("#setFilter").submit();'>
                              <option value=""></option>
                              <option value="add"<?php echo $_SESSION['do_Contacts']->getFilter("add"); ?>>Recently added</option>
                              <option value="modify"<?php echo $_SESSION['do_Contacts']->getFilter("modify"); ?>>Recently modified</option>
                              <option value="view"<?php echo $_SESSION['do_Contacts']->getFilter("view"); ?>>Recently viewed</option>
                              <option value="active"<?php echo $_SESSION['do_Contacts']->getFilter("active"); ?>>With recent activity</option>
                            </select>
                          </h3>
                        </form>-->

                     <a id="show_tags_link" href="#" onclick="$('#tag_list').slideDown('slow');$('#show_tags_link').hide(); return false;">Show the Tags</a> <!--class="headline10"-->
                     <div id="tag_list" style="display:none">  <?php 
                              $do_tag_list = new Tag();
                              echo $do_tag_list->getUserTagList("contact");
                         ?>
                     </div>
                    </div>
                <?php } ?>                 
                </div>
                <?php
                  $e_del_or_tag = new Event("do_Contacts->eventDeleteMultiple");
                  $e_del_or_tag->addEventAction("do_Contacts->eventAddTagMultiple", 202);
                  $e_del_or_tag->addEventAction("mydb.gotoPage", 304);
                  $e_del_or_tag->addParam("goto", "i_contacts.php");
                  echo $e_del_or_tag->getFormHeader();
                  echo $e_del_or_tag->getFormEvent();
                ?>
                <div id="contacts_ctlbar" style="display: none;">
                    <span class="redlink"><a href="#" onclick="setDel(); return false;">Delete</a></span> selected or add tags to selected <input type="text" name="tags" />
                    <span class="sasnlinks">( <span class="bluelink"><a href="#" onclick="fnSelAll(); return false;">select all</a></span> | <span class="bluelink"><a href="#" onclick="fnSelNone(); return false;">select none</a></span> )</span>
                </div>
                <div class="mobile_head_pad5">
<?php
    if ($show_companies) {
        $do_Companies->view_list_companies();
    }  
    $_SESSION['do_Contacts']->view_i_list_contacts();
?>
                    <div class="bottompad40"></div>
                </div>
                </form>
<?php include_once('i_ofuz_logout.php'); ?>
</div>
</body>
</html>
