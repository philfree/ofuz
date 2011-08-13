<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Contacts';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

	//If gears is not enabled, redirect to contacts.php
	$do_user_settings = new UserSettings();
	$setting_gears_arr = $do_user_settings->getSettingValue("google_gears");
	if($setting_gears_arr['setting_value'] == 'No') {
        $message = '<div class="contentfull">';
		$message .= '<div class="contentfull">';
		$message .= '<div class="messageshadow">';
		$message .= '<div class="messages">';
		$message .= 'You have not turned on the Gears. <br />To access the Gears enabled Contacts page, please follow : <B>Settings->Google Gears->Turn On</B>.<br />You are being redirected to Contacts page without Gears....';
		$message .= '</div></div></div></div>';
        echo $message;
		$jscript = '<script language="javascript">';
		$jscript .= 'setTimeout("redirectUser()",30000);';
		$jscript .= "function redirectUser(){window.location='/contacts.php';}";
		$jscript .= '</script>';
		echo $jscript;
		exit();
	}

    if (!is_object($_SESSION['do_Contacts'])) {
        $do_Contacts = new Contact();
       // $do_Contacts->get_all_contacts();
        $do_Contacts->setRegistry("all_contacts");
        $do_Contacts->sessionPersistent("do_Contacts", "index.php", 36000);
		$_SESSION['refresh_contacts'] = true;
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
<script type="text/javascript">
//<![CDATA[
function setDel() {
    if (confirm("Are you sure you want to delete the selected contacts?")) {
        $("#do_Contacts__eventAddTagMultiple_mydb_events_100_").attr("value", "do_Contacts->eventDeleteMultiple");
        $("#do_Contacts__eventAddTagMultiple").submit();
    }
}       

function setEMail() {
    $("#do_Contacts__eventAddTagMultiple_mydb_events_100_").attr("value", "do_Contacts->eventGetForMailMerge");
    $("#do_Contacts__eventAddTagMultiple").submit();
}

function setShare(){
  $("#do_Contacts__eventAddTagMultiple").attr("action","co_workers.php");
  $("#do_Contacts__eventAddTagMultiple").submit(); 

}

function setMerge(){
  if (confirm("Are you sure you want to merge the selected contacts?")) {
      $("#do_Contacts__eventAddTagMultiple").attr("action","merge_automated.php");
      $("#do_Contacts__eventAddTagMultiple").submit(); 
  }

}
function setUnShare() {
    if (confirm("Are you sure you want to Unshare the selected contacts?")) {
        $("#do_Contacts__eventAddTagMultiple_mydb_events_100_").attr("value", "do_Contacts->eventUnShareMultiple");
        $("#do_Contacts__eventAddTagMultiple").submit();
    }
}

function deleteTagMul(){
    $("#do_Contacts__eventAddTagMultiple_mydb_events_100_").attr("value", "do_Contacts->eventDeleteMultipleContactsTag");
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
	expandGearsContactsOnSelectAll();
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

// Ends here
//]]>
</script>

<script type="text/javascript"  src="gears_init.js"></script>
<script type="text/javascript">

var isIE=false;

var db;
var limitStart = 0;
var limitEnd = 100;
/*
function setError(m)
{
	var e= getElement('gg_contentfull');
	if ( e!=null ){
		e.style.color='red';
		e.innerHTML=m;
	}
}
*/

isGearsInstalled();

function ggSyncProcess() {

	$('#is-connected').hide();
	$('#syncing').show();
	$('#ggears_sync_msg')[0].innerHTML = 'Status: Updating for offline use' + ' \n ' + 'Please wait...';
	$('#ggears_sync_msg').show();

	// create a workerpool
	var workerPool = google.gears.factory.create('beta.workerpool');
	// handle messages from the worker
	workerPool.onmessage = function(a, b, message) {
		$('#ggears_sync_msg')[0].innerHTML = message.body;
		var msg_body = message.body;
		if(msg_body == 'synchronized') {
			$('#ggears_sync_msg')[0].innerHTML = 'Ready for offline use!' + ' \n ' + 'You can go offline at any time.';
			$('#ggears_sync_msg').show();
			$('#syncing').hide();
			$('#synced').show();

			//listRecords();
			displayContacts();
			//makeOfflineModeAvailable();
		}
		if(msg_body == 'No Contacts') {
			$('#ggears_sync_msg')[0].innerHTML = 'Oops...No Contacts available to sync!!';
			$('#ggears_sync_msg').show();
			$('#syncing').hide();
			$('#is-connected').show();

			//listRecords();
			displayContacts();
		}
	};
	// create a child worker from a URL
	var childWorkerId = workerPool.createWorkerFromUrl('gears_db_load.js');
	// send the child a message... (messageBody, Unique Worker ID)
	workerPool.sendMessage("loadAllContacts", childWorkerId);

}

function isGearsInstalled() {

	if (!window.google || !google.gears) {
		if (confirm("This Application requires Gears to be installed. Install now?")) {
			// Use an absolute URL to allow this to work when run from a local file.
			/*location.href = "http://gears.google.com/?action=install&message=welcome" 
			+ "&return=http://dev.ofuz.net/ggears_contact.html";*/
		
				var message = 'To enable fast client-side search of Ofuz '
							+ 'please install Gears';
				var url = 'http://gears.google.com/?action=install'
							+ '&message=' + encodeURIComponent(message)
							+ '&return=' + encodeURIComponent(window.location.href);
		
			location.href = url;
		
			return;
		}
	}

	activatingOfuzInLocalComputer();
}

function activatingOfuzInLocalComputer() {

	try {
		db = google.gears.factory.create('beta.database', '1.0');
		server = google.gears.factory.create('beta.localserver', '1.0');
		//rstore = this.server.createStore('resource_store');
		mstore = server.createManagedStore('managed_store');
		mstore.manifestUrl = 'ggears_resources_manifest.js';
		mstore.checkForUpdate();
	} catch (ex) {
		//setError('Could not create database: ' + ex.message);
		$('#ggears_sync_msg')[0].innerHTML = 'Could not create database: ' + ex.message;
	}
	
	if (db) {
		db.open('database-record');
		db.execute('create table if not exists contact' +
					' (idcontact int, firstname varchar(150), lastname varchar(150), company varchar(150), position varchar(150), email varchar(200), phone varchar(20), tag varchar(200), company_website varchar(200), datecreated int,contact_photo varchar(200),idcompany int, PRIMARY KEY(idcontact))');
		//This will be added in ofuz v0.5
		//addForm();
	}
	else
	{
		//setError('Error, unable to open db!');
		$('#ggears_sync_msg')[0].innerHTML = 'Error, unable to open db!';
	}

}

function getElement(id) {
	var select;
	if ( document.all )
	{
		select = document.all(id);
		isIE=true;
	}
	else
		select = document.getElementById(id);
	return select;
}

function listRecords()
{	

	if (!google.gears.factory || !db) {
	return;
	}

	// We re-throw Gears exceptions to make them play nice with certain tools.
	// This will be unnecessary in a future version of Gears.

	var ll = '';
	try
	{

		var rs = db.execute('select * from contact order by firstname limit '+limitStart+','+limitEnd);

		while (rs.isValidRow())
		{
			var company_link = '';
		
			if(rs.field(3)) {
				company_link = '<a href="Company/'+rs.field(11)+'" onclick="allowHighlight=false;">'+rs.field(3)+'</a>';
			}

			var phone = (rs.field(6)) ? rs.field(6) : '';
			var position = (rs.field(4)) ? '<i>'+rs.field(4)+'</i> at ' : '';
			var tagnames = (rs.field(7)) ? '<div>'+rs.field(7)+'</div>' : '';
	
			ll+='<div class="contacts" id="cid'+rs.field(0)+'" onclick="fnHighlight('+rs.field(0)+')">';
			ll+='<div class="contacts_main">';
			ll+='<input type="checkbox" name="ck\[\]" id="ck'+rs.field(0)+'" value="'+rs.field(0)+'" class="ofuz_list_checkbox" onclick="fnHighlight('+rs.field(0)+')" style="visibility:hidden;" />';
			if(rs.field(10)) {
				ll+='<img src="'+rs.field(10)+'" width="34" height="34" alt="" />';
			} else {
				ll+='<img src="/images/empty_avatar.gif" width="34" height="34" alt="" />';
			}

			ll+='</div>';
			ll+='<div class="contacts_desc">';
			ll+='<span class="contacts_name"><a href="Contact/'+rs.field(0)+'" onclick="allowHighlight=false;">'+rs.field(1)+' '+rs.field(2)+'</a></span><br />';
			ll+= position+company_link;
			ll+='</div>';
			if(phone) {
				ll+='<div class="contacts_email">'+phone+'<br/>';
			}
			if(rs.field(5)) {
				ll+='<a href="mailto:'+rs.field(5)+'">'+rs.field(5)+'</a><br />';
			}
			if(tagnames) {
				ll+= tagnames;
			}
			ll+='</div>';
			ll+='</div>';

			ll+='<div class="spacerblock_2"></div>';
			ll+='<div class="solidline"></div>';
			ll+='<div id="'+rs.field(0)+'" class="message_box"></div>';

	
			rs.next();

		}
		rs.close();
		
		//$('#gg_contentfull')[0].innerHTML = ll;
	
	}
	catch (e)
	{
		throw new Error(e.message);
	}

	var tag;
	tag = getContactTags();
	$('#gg_tags')[0].innerHTML = tag;

	return ll;
}


function getContactTags() {
	var str_tag = '';
	if (!google.gears.factory || !db) {
	return;
	}

	var rs = db.execute('select tag from contact order by tag desc');
	var tag = '';

	while (rs.isValidRow()) {
		
		if(rs.field(0)) {
			
			tag += (tag == '') ? rs.field(0) : ','+rs.field(0);

		}

		rs.next();
	}

	rs.close();

	if(tag) {
		
		temp=tag.split(",");
		
		newArr=[]
		
		for(var i=0;i<temp.length;i++){
		
		isIn=0
		
		for(var j=0;j<newArr.length;j++){
		
		if(temp[i]==newArr[j]){
		isIn=1
		}
		
		}
		
		if(isIn==0){
		newArr.push(temp[i])
		}
		
		}
		//sorting array in ascending order
		newArr.sort();

		for(i in newArr) {
			str_tag += (str_tag=='') ? '<span id="'+newArr[i]+'" style="cursor:pointer;" onClick="searchRecordsTags(this)">'+newArr[i]+'</span>' : ', '+'<span id="'+newArr[i]+'" style="cursor:pointer;" onClick="searchRecordsTags(this)">'+newArr[i]+'</span>';

		}

	}

	return str_tag;

}

function getContactSubTags(qq) {
	
	if (!google.gears.factory || !db) {
	return;
	}

	var rs = db.execute('select * from contact WHERE tag LIKE ? order by tag',['%'+qq+'%']);

	var tag = '';

	while (rs.isValidRow()) {
		
		if(rs.field(7)) {

			tag += (tag == '') ? rs.field(7) : ','+rs.field(7);

		}

		rs.next();
	}

	rs.close();

	if(tag) {
		
		temp=tag.split(",");
		
		newArr=[]
		
		for(var i=0;i<temp.length;i++){
		
		isIn=0
		
		for(var j=0;j<newArr.length;j++){
		
		if(temp[i]==newArr[j]){
		isIn=1
		}
		
		}
		
		if(isIn==0){
		newArr.push(temp[i])
		}
		
		}

		var str_tag = '';
		//newTagArr=[];
		if(newArr) {
			newArr.sort();
		}
		hd_gg_sub_tags = '';  //hidden value for the subtags string
		for(i in newArr) {			
			if(newArr[i] != qq) {
				hd_gg_sub_tags += (hd_gg_sub_tags == '') ? newArr[i] : ','+newArr[i];
				//newTagArr.push(newArr[i]);
				str_tag += (str_tag=='') ? '<span id="'+newArr[i]+'" style="cursor:pointer;" onClick="searchRecordsSubTags(this)">'+newArr[i]+'</span>' : ', '+'<span id="'+newArr[i]+'" style="cursor:pointer;" onClick="searchRecordsSubTags(this)">'+newArr[i]+'</span>';
			}

		}

		$('#hd_gg_sub_tags')[0].innerHTML = hd_gg_sub_tags;
	}

	return str_tag;

}

//creates desktop shortcut for ofuz
function createDesktopShortcut() {

  var desktop = google.gears.factory.create("beta.desktop");
  var description = "This shortcut launches the Ofuz shortcut to access the Application.";

  var icons = {
    "16x16": "images/icon16.png",
    "32x32": "images/icon32.png",
    "48x48": "images/icon48.png",
    "128x128": "images/icon128.png"
  };

  desktop.createShortcut("ofuz",  // name
                         "ggears_contacts.html",  // url
                         icons,  // icons (must specify at least one)
                         description);  // description (optional)

}

function ggChangeStatus() {
	$('#synced').hide();
	$('#syncing').hide();
	$('#is-connected').show();
	$('#ggears_sync_msg')[0].innerHTML = 'Click above icon to sync!';
	$('#ggears_sync_msg').show();
}

/*
function makeOfflineModeAvailable() {

	var request = google.gears.factory.create('beta.httprequest');
	request.open('GET', 'ggears_contacts.html');
	request.onreadystatechange = function() {
	if (request.readyState == 4) {
		//console.write(request.responseText);
	}
	};
	request.send();

}
*/

//commented since 'Search button' is removed.
/*
function searchRecords()
{

	if (!google.gears.factory || !db) {
	return;
	}

    var qq= getElement('qq').value;

	var e= getElement('gg_contentfull');
	if ( e!=null ){

		//swTab(3);
		var ll = '';

		try
		{

			var rs = db.execute('select * from contact WHERE firstname LIKE ? OR lastname LIKE ? OR company LIKE ? OR phone LIKE ? OR tag LIKE ? order by firstname',['%'+qq+'%','%'+qq+'%','%'+qq+'%','%'+qq+'%','%'+qq+'%']);

			while (rs.isValidRow())
			{
				var company_link = '';
			
				if(rs.field(3)) {
					company_link = '<a href="Company/'+rs.field(11)+'" onclick="allowHighlight=false;">'+rs.field(3)+'</a>';
				}
				var phone = (rs.field(6)) ? rs.field(6) : '';
				var position = (rs.field(4)) ? '<i>'+rs.field(4)+'</i> at ' : '';
				var tagnames = (rs.field(7)) ? '<div>'+rs.field(7)+'</div>' : '';
		
				ll+='<div class="contacts" id="cid'+rs.field(0)+'" onclick="fnHighlight('+rs.field(0)+')">';
				ll+='<div class="contacts_main">';
				ll+='<input type="checkbox" name="ck\[\]" id="ck'+rs.field(0)+'" value="'+rs.field(0)+'" class="ofuz_list_checkbox" onclick="fnHighlight('+rs.field(0)+')" style="visibility:hidden;" />';
				if(rs.field(10)) {
					ll+='<img src="'+rs.field(10)+'" width="34" height="34" alt="" />';
				} else {
					ll+='<img src="/images/empty_avatar.gif" width="34" height="34" alt="" />';
				}
				ll+='</div>';
				ll+='<div class="contacts_desc">';
				ll+='<span class="contacts_name"><a href="Contact/'+rs.field(0)+'" onclick="allowHighlight=false;">'+rs.field(1)+' '+rs.field(2)+'</a></span><br />';
				ll+= position+company_link;
				ll+='</div>';
				if(phone) {
					ll+='<div class="contacts_email">'+phone+'<br/>';
				}
				if(rs.field(5)) {
					ll+='<a href="mailto:'+rs.field(5)+'">'+rs.field(5)+'</a><br />';
				}
				if(tagnames) {
					ll+= tagnames;
				}
				ll+='</div>';
				ll+='</div>';
	
				ll+='<div class="spacerblock_2"></div>';
				ll+='<div class="solidline"></div>';
	
		
				rs.next();
	
			}
			rs.close();
			
			$('#gg_contentfull')[0].innerHTML = ll;

		}
		catch (e)
		{
		throw new Error(e.message);
		}
	}

}
*/
//search as you type
function instantSearch()
{
	if (!google.gears.factory || !db) {
	return;
	}

    var qq= getElement('qq').value;
	var searchkey_length = qq.length;

	if(searchkey_length == 2) {
		//listRecords();
		displayContacts();
	}

	if(searchkey_length > 2)
	{
		$('#search_indicator').show();

		//var qq= getElement('qq').value;
	
		var e= getElement('gg_contentfull');
		if ( e!=null ){
	
			//swTab(3);
			var ll = '';
	
			try
			{
	
				var rs = db.execute('select * from contact WHERE firstname LIKE ? OR lastname LIKE ? OR company LIKE ? OR phone LIKE ? OR tag LIKE ? order by firstname',[qq+'%',qq+'%',qq+'%',qq+'%',qq+'%']);
	
				while (rs.isValidRow())
				{
					var company_link = '';
				
					if(rs.field(3)) {
						company_link = '<a href="Company/'+rs.field(11)+'" onclick="allowHighlight=false;">'+rs.field(3)+'</a>';
					}
					var phone = (rs.field(6)) ? rs.field(6) : '';
					var position = (rs.field(4)) ? '<i>'+rs.field(4)+'</i> at ' : '';
					var tagnames = (rs.field(7)) ? '<div>'+rs.field(7)+'</div>' : '';
			
					ll+='<div class="contacts" id="cid'+rs.field(0)+'" onclick="fnHighlight('+rs.field(0)+')">';
					ll+='<div class="contacts_main">';
					ll+='<input type="checkbox" name="ck\[\]" id="ck'+rs.field(0)+'" value="'+rs.field(0)+'" class="ofuz_list_checkbox" onclick="fnHighlight('+rs.field(0)+')" style="visibility:hidden;" />';
					if(rs.field(10)) {
						ll+='<img src="'+rs.field(10)+'" width="34" height="34" alt="" />';
					} else {
						ll+='<img src="/images/empty_avatar.gif" width="34" height="34" alt="" />';
					}
					ll+='</div>';
					ll+='<div class="contacts_desc">';
					ll+='<span class="contacts_name"><a href="Contact/'+rs.field(0)+'" onclick="allowHighlight=false;">'+rs.field(1)+' '+rs.field(2)+'</a></span><br />';
					ll+= position+company_link;
					ll+='</div>';
					if(phone) {
						ll+='<div class="contacts_email">'+phone+'<br/>';
					}
					if(rs.field(5)) {
						ll+='<a href="mailto:'+rs.field(5)+'">'+rs.field(5)+'</a><br />';
					}
					if(tagnames) {
						ll+= tagnames;
					}
					ll+='</div>';
					ll+='</div>';
		
					ll+='<div class="spacerblock_2"></div>';
					ll+='<div class="solidline"></div>';
		
			
					rs.next();
		
				}
				rs.close();
				
				$('#gg_contentfull')[0].innerHTML = ll;
				$('#search_indicator').hide();
			}
			catch (e)
			{
			throw new Error(e.message);
			}
		}
	}
}

function searchRecordsTags(eleObj)
{

	if (!google.gears.factory || !db) {
	return;
	}

    var qq = eleObj.innerHTML;

	var e= getElement('gg_contentfull');
	if ( e!=null ){

		//swTab(3);
		var ll = '';

		try
		{

			var rs = db.execute('select * from contact WHERE tag LIKE ? order by firstname',['%'+qq+'%']);

			while (rs.isValidRow())
			{
				var company_link = '';
			
				if(rs.field(3)) {
					company_link = '<a href="Company/'+rs.field(11)+'" onclick="allowHighlight=false;">'+rs.field(3)+'</a>';
				}
				var phone = (rs.field(6)) ? rs.field(6) : '';
				var position = (rs.field(4)) ? '<i>'+rs.field(4)+'</i> at ' : '';
				var tagnames = (rs.field(7)) ? '<div>'+rs.field(7)+'</div>' : '';
		
				ll+='<div class="contacts" id="cid'+rs.field(0)+'" onclick="fnHighlight('+rs.field(0)+')">';
				ll+='<div class="contacts_main">';
				ll+='<input type="checkbox" name="ck\[\]" id="ck'+rs.field(0)+'" value="'+rs.field(0)+'" class="ofuz_list_checkbox" onclick="fnHighlight('+rs.field(0)+')" style="visibility:hidden;" />';
				if(rs.field(10)) {
					ll+='<img src="'+rs.field(10)+'" width="34" height="34" alt="" />';
				} else {
					ll+='<img src="/images/empty_avatar.gif" width="34" height="34" alt="" />';
				}
				ll+='</div>';
				ll+='<div class="contacts_desc">';
				ll+='<span class="contacts_name"><a href="Contact/'+rs.field(0)+'" onclick="allowHighlight=false;">'+rs.field(1)+' '+rs.field(2)+'</a></span><br />';
				ll+= position+company_link;
				ll+='</div>';
				if(phone) {
					ll+='<div class="contacts_email">'+phone+'<br/>';
				}
				if(rs.field(5)) {
					ll+='<a href="mailto:'+rs.field(5)+'">'+rs.field(5)+'</a><br />';
				}
				if(tagnames) {
					ll+= tagnames;
				}
				ll+='</div>';
				ll+='</div>';
	
				ll+='<div class="spacerblock_2"></div>';
				ll+='<div class="solidline"></div>';
	
		
				rs.next();
	
			}
			rs.close();
			
			$('#gg_contentfull')[0].innerHTML = ll;

			//Sub Tags
			if(qq) {
				
				$('#hd_gg_sub_tags_header')[0].innerHTML = qq;
				var subtag_header = qq+' <span id="'+qq+'" style="cursor:pointer;" onClick="deCumulateSubTags(\''+qq+'\')"><img src="images/delentry.gif" border="0"/></span>';

				$('#gg_sub_tags_header')[0].innerHTML = '<h3>Showing contacts with tags:</h3><h2>'+subtag_header+'</h2>';
				$('#gg_search').hide();
				$('#gg_sub_tags_header').show();

				qq = getContactSubTags(qq);

				$('#gg_sub_tags_main').show();
				$('#gg_sub_tags')[0].innerHTML = qq;
 
			}

		}
		catch (e)
		{
		throw new Error(e.message);
		}
	}

}

function searchRecordsSubTags(eleObj)
{

	if (!google.gears.factory || !db) {
	return;
	}

    var qq = eleObj.innerHTML;

	//Sub Tags
	if(qq) {

		cumulateSubTagSearch(qq);
		arr_subtag_search = $('#hd_gg_sub_tags_header')[0].innerHTML.split(",");
		var like_wildcard = '';
		var like_value = '';
		var query = "select * from contact WHERE";
		for(i in arr_subtag_search) {

			if(like_wildcard=='') {
				like_wildcard += " tag LIKE '%"+arr_subtag_search[i]+"%'";
			} else {
				like_wildcard += " AND tag LIKE '%"+arr_subtag_search[i]+"%'";
			}

		}
		query += like_wildcard+" order by firstname";
		query += '';

	}

	var e= getElement('gg_contentfull');
	if ( e!=null ){

		//swTab(3);
		var ll = '';

		try
		{
			//alert(query);
			//var rs = db.execute('select * from contact WHERE tag LIKE ? order by firstname',['%'+qq+'%']);
			var rs = db.execute(query);

			while (rs.isValidRow())
			{
				var company_link = '';
			
				if(rs.field(3)) {
					company_link = '<a href="Company/'+rs.field(11)+'" onclick="allowHighlight=false;">'+rs.field(3)+'</a>';
				}
				var phone = (rs.field(6)) ? rs.field(6) : '';
				var position = (rs.field(4)) ? '<i>'+rs.field(4)+'</i> at ' : '';
				var tagnames = (rs.field(7)) ? '<div>'+rs.field(7)+'</div>' : '';
		
				ll+='<div class="contacts" id="cid'+rs.field(0)+'" onclick="fnHighlight('+rs.field(0)+')">';
				ll+='<div class="contacts_main">';
				ll+='<input type="checkbox" name="ck\[\]" id="ck'+rs.field(0)+'" value="'+rs.field(0)+'" class="ofuz_list_checkbox" onclick="fnHighlight('+rs.field(0)+')" style="visibility:hidden;" />';
				if(rs.field(10)) {
					ll+='<img src="'+rs.field(10)+'" width="34" height="34" alt="" />';
				} else {
					ll+='<img src="/images/empty_avatar.gif" width="34" height="34" alt="" />';
				}
				ll+='</div>';
				ll+='<div class="contacts_desc">';
				ll+='<span class="contacts_name"><a href="Contact/'+rs.field(0)+'" onclick="allowHighlight=false;">'+rs.field(1)+' '+rs.field(2)+'</a></span><br />';
				ll+= position+company_link;
				ll+='</div>';
				if(phone) {
					ll+='<div class="contacts_email">'+phone+'<br/>';
				}
				if(rs.field(5)) {
					ll+='<a href="mailto:'+rs.field(5)+'">'+rs.field(5)+'</a><br />';
				}
				if(tagnames) {
					ll+= tagnames;
				}
				ll+='</div>';
				ll+='</div>';
	
				ll+='<div class="spacerblock_2"></div>';
				ll+='<div class="solidline"></div>';
	
		
				rs.next();
	
			}
			rs.close();
			
			$('#gg_contentfull')[0].innerHTML = ll;

			/*
			//Sub Tags
			if(qq) {
				cumulateSubTagSearch(qq);
			}
			*/

		}
		catch (e)
		{
		throw new Error(e.message);
		}
	}

}

function decumulateSearch(qq)
{

	if (!google.gears.factory || !db) {
	return;
	}

	//Sub Tags
	if(qq) {

		arr_subtag_search = $('#hd_gg_sub_tags_header')[0].innerHTML.split(",");

		var like_wildcard = '';
		var like_value = '';
		var query = "select * from contact WHERE";
		for(i in arr_subtag_search) {

			if(like_wildcard=='') {
				like_wildcard += " tag LIKE '%"+arr_subtag_search[i]+"%'";
			} else {
				like_wildcard += " AND tag LIKE '%"+arr_subtag_search[i]+"%'";
			}

		}
		query += like_wildcard+" order by firstname";
		query += '';

	}

	if(arr_subtag_search != '') {
		var e= getElement('gg_contentfull');
		if ( e!=null ){
	
			//swTab(3);
			var ll = '';
	
			try
			{
				//alert(query);
				//var rs = db.execute('select * from contact WHERE tag LIKE ? order by firstname',['%'+qq+'%']);
				var rs = db.execute(query);
	
				while (rs.isValidRow())
				{
					var company_link = '';
				
					if(rs.field(3)) {
						company_link = '<a href="Company/'+rs.field(11)+'" onclick="allowHighlight=false;">'+rs.field(3)+'</a>';
					}
					var phone = (rs.field(6)) ? rs.field(6) : '';
					var position = (rs.field(4)) ? '<i>'+rs.field(4)+'</i> at ' : '';
					var tagnames = (rs.field(7)) ? '<div>'+rs.field(7)+'</div>' : '';
			
					ll+='<div class="contacts" id="cid'+rs.field(0)+'" onclick="fnHighlight('+rs.field(0)+')">';
					ll+='<div class="contacts_main">';
					ll+='<input type="checkbox" name="ck\[\]" id="ck'+rs.field(0)+'" value="'+rs.field(0)+'" class="ofuz_list_checkbox" onclick="fnHighlight('+rs.field(0)+')" style="visibility:hidden;" />';
					if(rs.field(10)) {
						ll+='<img src="'+rs.field(10)+'" width="34" height="34" alt="" />';
					} else {
						ll+='<img src="/images/empty_avatar.gif" width="34" height="34" alt="" />';
					}
					ll+='</div>';
					ll+='<div class="contacts_desc">';
					ll+='<span class="contacts_name"><a href="Contact/'+rs.field(0)+'" onclick="allowHighlight=false;">'+rs.field(1)+' '+rs.field(2)+'</a></span><br />';
					ll+= position+company_link;
					ll+='</div>';
					if(phone) {
						ll+='<div class="contacts_email">'+phone+'<br/>';
					}
					if(rs.field(5)) {
						ll+='<a href="mailto:'+rs.field(5)+'">'+rs.field(5)+'</a><br />';
					}
					if(tagnames) {
						ll+= tagnames;
					}
					ll+='</div>';
					ll+='</div>';
		
					ll+='<div class="spacerblock_2"></div>';
					ll+='<div class="solidline"></div>';
		
			
					rs.next();
		
				}
				rs.close();
				
				$('#gg_contentfull')[0].innerHTML = ll;
	
				/*
				//Sub Tags
				if(qq) {
					cumulateSubTagSearch(qq);
				}
				*/
	
			}
			catch (e)
			{
			throw new Error(e.message);
			}
		}
	} else {
		limitStart = 0;
		displayContacts();
	}

}

function cumulateSubTagSearch(qq) {

	if(qq=='') {
		$('#gg_search').show();
		$('#gg_sub_tags_header').hide();
	} else {
		$('#gg_search').hide();
		$('#gg_sub_tags_header').show();
	} 

	arr_tags = $('#hd_gg_sub_tags')[0].innerHTML.split(",");

	var tag;
	var str_tag = '';
	var new_str_tags = '';
	tag = $('#hd_gg_sub_tags_header')[0].innerHTML;
	tag += (tag) ? ','+qq : qq;

	$('#hd_gg_sub_tags_header')[0].innerHTML = tag;

	if(tag) {
		temp = tag.split(",");
		for(i in temp) {
			for(j in arr_tags) {
				if(arr_tags[j] == temp[i]) {
					arr_tags = removeByElement(arr_tags,arr_tags[j])
					break;
				}
			}
		}


		var hd_gg_sub_tags = '';
		for(i in arr_tags) {

				hd_gg_sub_tags += (hd_gg_sub_tags == '') ? arr_tags[i] : ','+arr_tags[i];
				str_tag += (str_tag=='') ? '<span id="'+arr_tags[i]+'" style="cursor:pointer;" onClick="searchRecordsSubTags(this)">'+arr_tags[i]+'</span>' : ', '+'<span id="'+arr_tags[i]+'" style="cursor:pointer;" onClick="searchRecordsSubTags(this)">'+arr_tags[i]+'</span>';
		}

		
		$('#gg_sub_tags')[0].innerHTML = str_tag;

		if(str_tag=='') {
			$('#gg_sub_tags_main').hide();
		} else {
			$('#gg_sub_tags_main').show();
		}
		
		$('#hd_gg_sub_tags')[0].innerHTML = hd_gg_sub_tags;

		var subtag = '';
		var hd_gg_sub_tags_header = '';
		for(t in temp) {
			hd_gg_sub_tags_header += (hd_gg_sub_tags_header == '') ? temp[t] : ','+temp[t];
			subtag += (subtag == '') ? temp[t]+' <span id="'+temp[t]+'" style="cursor:pointer;" onClick="deCumulateSubTags(\''+temp[t]+'\')"><img src="images/delentry.gif" border="0"/></span>' : ', '+temp[t]+' <span id="'+temp[t]+'" style="cursor:pointer;" onClick="deCumulateSubTags(\''+temp[t]+'\')"><img src="images/delentry.gif" border="0"/></span>';
		}
		$('#gg_sub_tags_header')[0].innerHTML = '<h3>Showing contacts with tags:</h3><h2>'+subtag+'</h2>';
		$('#hd_gg_sub_tags_header')[0].innerHTML = hd_gg_sub_tags_header;
		
	}
}

function removeByElement(arrayName,arrayElement) {
    for(var i=0; i<arrayName.length;i++ )
     { 
        if(arrayName[i]==arrayElement)
            arrayName.splice(i,1); 
      }
	return arrayName;
}

function deCumulateSubTags(subtag) {
	arr_subtag = $('#hd_gg_sub_tags')[0].innerHTML.split(",");
	arr_subtag.push(subtag);
	if(arr_subtag) {
		arr_subtag.sort();
	}
	var subtags = '';
	var hd_gg_sub_tags = '';
	for(i in arr_subtag) {
		hd_gg_sub_tags += (hd_gg_sub_tags == '') ? arr_subtag[i] : ','+arr_subtag[i];

		subtags += (subtags=='') ? '<span id="'+arr_subtag[i]+'" style="cursor:pointer;" onClick="searchRecordsSubTags(this, arr_subtag)">'+arr_subtag[i]+'</span>' : ','+'<span id="'+arr_subtag[i]+'" style="cursor:pointer;" onClick="searchRecordsSubTags(this, arr_subtag)">'+arr_subtag[i]+'</span>';
	}
		//alert(hd_gg_sub_tags)
	$('#gg_sub_tags')[0].innerHTML = subtags;
	$('#hd_gg_sub_tags')[0].innerHTML = hd_gg_sub_tags;

	arr_subtag_header = $('#hd_gg_sub_tags_header')[0].innerHTML.split(",");

	arr_subtag_header = removeByElement(arr_subtag_header,subtag)
	var hd_gg_sub_tags_header = '';
	var subtag_header = '';
	for(i in arr_subtag_header) {
		//alert(arr_subtag_header[i]);
		hd_gg_sub_tags_header += (hd_gg_sub_tags_header == '') ? arr_subtag_header[i] : ','+arr_subtag_header[i];
		subtag_header += (subtag_header == '') ? arr_subtag_header[i]+' <span id="'+arr_subtag_header[i]+'" style="cursor:pointer;" onClick="deCumulateSubTags(\''+arr_subtag_header[i]+'\')"><img src="images/delentry.gif" border="0"/></span>' : ', '+arr_subtag_header[i]+' <span id="'+arr_subtag_header[i]+'" style="cursor:pointer;" onClick="deCumulateSubTags(\''+arr_subtag_header[i]+'\')"><img src="images/delentry.gif" border="0"/></span>';
	}
	//alert(hd_gg_sub_tags_header+':'+subtag_header)
	$('#gg_sub_tags_header')[0].innerHTML = '<h3>Showing contacts with tags:</h3><h2>'+subtag_header+'</h2>';
	$('#hd_gg_sub_tags_header')[0].innerHTML = hd_gg_sub_tags_header;

	if(subtag_header=='') {
		$('#gg_search').show();
		$('#gg_sub_tags_header').hide();
		$('#gg_sub_tags_main').hide();
	} else {
		$('#gg_search').hide();
		$('#gg_sub_tags_header').show();
		$('#gg_sub_tags_main').show();
	} 
	decumulateSearch(subtag);
}

function autoLoadContacts() {
	limitStart = limitStart + limitEnd;
	$('div#last_contact_loader').html('<img src="/images/loader1.gif">');

	contacts_html = listRecords();

	$(".message_box:last").after(contacts_html);
	$('div#last_contact_loader').empty();
}

$(document).ready(function()
{
$(window).scroll(function(){
if ($(window).scrollTop() == $(document).height() - $(window).height()){
autoLoadContacts();
}
});
});

function displayContacts() {
	var html = listRecords();
	$('#gg_contentfull')[0].innerHTML = html;
}

function expandGearsContactsOnSelectAll() {

	if (!google.gears.factory || !db) {
	return;
	}

	var rs = db.execute('select count(*) as total_contacts from contact');
	if (rs.isValidRow())
	{
		total_contacts = rs.field(0);
	}

	limitStart = 0; limitEnd = total_contacts;

	$('div#last_contact_loader').html('<img src="/images/loader1.gif">');

	contacts_html = listRecords();

	$(".message_box:last").after(contacts_html);
	$('div#last_contact_loader').empty();
}
</script>

<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>

<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        <div class="left_text_links">
<?php
//<input type="image" src="/images/add_new_contact.jpg" onclick="document.location.href='/contact_add.php'" title="Add new contacts"/>
$button_new_contact = new DynamicButton();
echo $button_new_contact->CreateButton('/contact_add.php', 'add new contact', '', '', 'dyn_button_add_new_contact');
?>
        </div>
		<br/>
		<div class="left_text_links">
<?php
//<a href="#" id="desktopshortcut" style="cursor:pointer;" onclick="createDesktopShortcut()" title="Create a short cut link on your desktop to Ofuz for Offline use">
//<img src="/images/desktop_shortcut.jpg" border="0" />
//</a>
$button_shortcut = new DynamicButton();
echo $button_new_contact->CreateButton('#', 'desktop shortcut', '', 'createDesktopShortcut()');
?>
		</div>
		<br /><br />
		<div id="gg_sub_tags_main" style="display:none;">
        <div class="left_menu_header">
            <div class="left_menu_header_content">Subtags</div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content">
                <div class="headline10">Cumulate tags to narrow your search</div>
				<div id="gg_sub_tags"></div>
            </div>
        </div>
        <div class="left_menu_footer"></div><br /><br />
		</div>


        <div class="left_menu_header">
            <div class="left_menu_header_content">Tags</div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content" id="gg_tags">
            </div>
        </div>

        <div class="left_menu_footer"></div><br /><br />

        <div class="left_menu_header">
            <div class="left_menu_header_content">
				<span id="is-connected" style="cursor:pointer;display:block;" onclick="ggSyncProcess()">
				<img src="images/off-connected-synced.gif" border="0" title="Currently online: click to sync" />
                </span>
				<span id="syncing" style="display:none">
				<img src="images/off-connected-syncing.gif" border="0" title="syncing" />
				</span>
				<span id="synced" style="cursor:pointer;display:none"  onclick="ggChangeStatus()">
				<img src="images/synced.gif" border="0" title="synced" />
				</span>
			</div>
        </div>
        <div class="left_menu">
            <div class="left_menu_content"   id="ggears_sync_msg">
				Click above icon to sync!
            </div>
        </div>
        <div class="left_menu_footer"></div>
	

    </td><td class="layout_rcolumn">
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
		<div class="pad20" id="gg_sub_tags_header" style="display:none;"></div>
		<div class="tundra">
		<div class="pad20" id="gg_search">
			<h3>Find someone by typing their Tags, First Name, Last Name, company and Phone.</h3>
			<input type="text" name="qq" id="qq" size="50" maxlength="150" value="" tabindex="1" onkeyup="instantSearch()"/>
			<!--<input type="submit" value="Search" onClick="searchRecords();" tabindex="2" />-->
			&nbsp;&nbsp;&nbsp;<span id="search_indicator" style="display:none"><img src="/images/wait16g.gif" border="0" /></span>
			</div>
			</div>
		</div>
		<?php
			$do_tag_list = new Tag();
			$e_del_or_tag = new Event("do_Contacts->eventAddTagMultiple");
			//$e_del_or_tag->addEventAction("do_Contacts->eventDeleteMultiple", 202);
			$e_del_or_tag->addEventAction("mydb.gotoPage", 304);
			$e_del_or_tag->addParam("goto", "ggears_contacts.php");
			echo $e_del_or_tag->getFormHeader();
			echo $e_del_or_tag->getFormEvent();
		?>
		<div id="contacts_ctlbar" style="display: none;">
			<b>With the selected contacts you can:</b>
			<span class="redlink"><a href="#" onclick="setDel(); return false;">Delete them</a></span> or add tags <input type="text" name="tags" />
			&nbsp;or&nbsp;<?php echo $do_tag_list->generateUserTagsDropDown();?>
			<br/>
			<?php if($_SESSION['do_Contacts']->set_unshare){?>
			or <span class="redlink"><a href="#" onclick="setUnShare();return false;">Un-Share Contact</a></span>
			<input type ="hidden" name="co_worker_id" value = "<?php echo $_SESSION['do_Contacts']->unshare_co_worker;?>">
			<?}else{?>
			or <span class="redlink"><a href="#" onclick="setShare();return false;">Share With Co-Workers</a></span>
			<?php } ?>
			or <span class="redlink"><a href="#" onclick="setMerge();return false;">Merge in one</a></span>
			or <span class="redlink"><a href="#" onclick="setEMail();return false;">Send a Message</a></span><br/>
			<span class="sasnlinks">( <span class="bluelink"><a href="#" onclick="fnSelAll(); return false;">select all</a></span> | <span class="bluelink"><a href="#" onclick="fnSelNone(); return false;">select none</a></span> )</span>
		</div>
		<div class="contentfull" id="gg_contentfull">
		</div>
		<div id="last_contact_loader"></div>
		</form>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
	<div id="hd_gg_sub_tags" style="display:none;"></div>
	<div id="hd_gg_sub_tags_header" style="display:none;"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
<script type="text/javascript">
//listRecords();
displayContacts();
</script>
