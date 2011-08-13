/*
Workerpool JS to load remote data to local db after Setup
*/
(function(){
var db;
var wp = google.gears.workerPool;
var sender; // Parent Worker ID (ggears_contacts.php)
// handle incoming messages:
wp.onmessage = function(a, b, message) {
	try{
		var msg = message.body; // what was the message
		sender = message.sender; // Where did the message come from
		var resp; // response to be returned to the Parent Worker
		switch (msg)
		{
			case 'loadAllContacts':
			loadAllContacts(); /* load all contacts onto local DB...*/
			break;
			default:
			resp = 'Oops...no message sent!!';
			wp.sendMessage(resp, sender);
			break;
		}
	} // try
	catch(ex)
	{ // send back an error message if needed
		wp.sendMessage('Error in worker: '+ err.message, sender);
	}
} // onmessage



// handle any errors that come up...
wp.onerror = function( err ){
wp.sendMessage('Error in worker: '+ err.message, sender);
return true;
}

function createLocalDB() {

	try {
		db = google.gears.factory.create('beta.database', '1.0');
	} catch (ex) {
		resp = 'Could not create database: ' + ex.message;
		wp.sendMessage(resp, sender);
	}
	
	if (db) {
				
		db.open('database-record');

		db.execute('create table if not exists contact' +
					' (idcontact int, firstname varchar(150), lastname varchar(150), company varchar(150), position varchar(150), email varchar(200), phone varchar(20), tag varchar(200), company_website varchar(200), datecreated int,contact_photo varchar(200),idcompany int, PRIMARY KEY(idcontact))');

		//first clears the local DB
		try {
			db.execute('delete from contact');
		} catch (ex) {
			resp = 'An error occurred while clearing Local DB' + ex.message;
			wp.sendMessage(resp, sender);
		}
		//This will be added in ofuz v0.5
		//addForm();
	}
	else
	{
		resp = 'Error, unable to open db!';
		wp.sendMessage(resp, sender);
	}

}

function loadAllContacts(){

	createLocalDB();

	var request = google.gears.factory.create('beta.httprequest');
	// get Contacts from SERVER
	request.open('GET', 'ggears_contacts_intermediate.php');
	request.onreadystatechange = function() {
	if (request.readyState == 4) {
	try{

	jsonResponseText = request.responseText;

	//var jsonResponseText = json_parse(response); // using http://www.JSON.org/json_parse.js
	if(jsonResponseText) {
		jsonObj = eval('(' + jsonResponseText + ')');
		for(arrContact in jsonObj[1]) {

			for(keyContact in jsonObj[1][arrContact]) {

				var idcontact = jsonObj[1][arrContact]['idcontact'];
				var arrCon = new Array();

				arrCon['idcontact'] = jsonObj[1][arrContact]['idcontact'];
				arrCon['firstname'] = jsonObj[1][arrContact]['firstname'];
				arrCon['lastname'] = jsonObj[1][arrContact]['lastname'];
				arrCon['company'] = jsonObj[1][arrContact]['company'];
				arrCon['position'] = jsonObj[1][arrContact]['position'];
				arrCon['email_address'] = jsonObj[1][arrContact]['email_address'];
				arrCon['phone_number'] = jsonObj[1][arrContact]['phone_number'];
				arrCon['tag_name'] = jsonObj[1][arrContact]['tag_name'];
				arrCon['company_website'] = jsonObj[1][arrContact]['company_website'];
				arrCon['contact_photo'] = jsonObj[1][arrContact]['contact_photo'];
				arrCon['idcompany'] = jsonObj[1][arrContact]['idcompany'];
				//document.write(arrCon['firstname']+' '+arrCon['phone']+' '+arrCon['tag']+'<BR>');

				insertContactInLocalDB(arrCon);

				/*var retVal = isContactPresentInLocalDB(idcontact);
				if(retVal) {

					updateContactInLocalDB(arrCon);

				} else {

					insertContactInLocalDB(arrCon);

				}*/

			}

		}

		resp = 'synchronized';
		wp.sendMessage(resp, sender);

	} else {

		resp = 'No Contacts';
		wp.sendMessage(resp, sender);
	}
	}// try
	catch(ex){
	wp.sendMessage("Worker Error: line - "+ ex.lineNumber + " Ex: "+ ex.message, sender);
	} // catch
	} // readystate
	}; // onstatechange
	request.send();

} // loadAllContacts()

function isContactPresentInLocalDB(idcontact) {

	if (!google.gears.factory || !db) {
	return;
	}

	try {

		var rs = db.execute('select idcontact,firstname from contact WHERE idcontact = ? ',[idcontact]);

		while (rs.isValidRow()) {
		    return true;
		}
		
	} catch (e) {
		//throw new Error(e.message);
		wp.sendMessage(e.message, sender);
	}
}

function updateContactInLocalDB(arrCon) {
	if (!google.gears.factory || !db) {
	return;
	}
	
	var currTime = new Date().getTime();
	
	// update the contact.
	// The Gears database automatically escapes/unescapes updated values.
	try{
	db.execute('update contact set firstname = ?, lastname = ?, company = ?, position = ?, email = ?, phone = ?, tag = ?, company_website = ?, datecreated = ?, contact_photo = ?, idcompany = ? where idcontact = ?', [arrCon['firstname'],arrCon['lastname'],arrCon['company'],arrCon['position'],arrCon['email_address'],arrCon['phone_number'],arrCon['tag_name'],arrCon['company_website'],currTime,arrCon['contact_photo'],arrCon['idcompany'],arrCon['idcontact']]);
	}
	catch(ex)
	{
		//setError('Error updating record in Local DB: ' + ex.message);return;
		//$('#ggears_sync_msg')[0].innerHTML = 'Error updating record in Local DB: ' + ex.message;return;
		resp = 'Error updating record in Local DB: ' + ex.message;
		wp.sendMessage(resp, sender);
	}

	/*resp = 'Contacts synchronizing...';
	wp.sendMessage(resp, sender);*/
	
}

function insertContactInLocalDB(arrCon) {

	if (!google.gears.factory || !db) {
	return;
	}
	
	var currTime = new Date().getTime();
	
	// Insert the new item.
	// The Gears database automatically escapes/unescapes inserted values.
	try{
	db.execute('insert into contact values (?,?, ?,?,?,?,?,?,?,?,?,?)', [arrCon['idcontact'],arrCon['firstname'],arrCon['lastname'],arrCon['company'],arrCon['position'],arrCon['email_address'],arrCon['phone_number'],arrCon['tag_name'],arrCon['company_website'],currTime, arrCon['contact_photo'], arrCon['idcompany']]);
	}
	catch(ex)
	{

		//$('#ggears_sync_msg')[0].innerHTML = 'Error saving record in Local DB: ' + ex.message;return;
		resp = 'Error saving record in Local DB: ' + ex.message;return;
		wp.sendMessage(resp, sender);
	}

	/*resp = 'Contacts synchronizing...';
	wp.sendMessage(resp, sender);*/

}

})();