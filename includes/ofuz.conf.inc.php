<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * Ofuz Base Configuration file
   */

  $oz_tp = "ofuz_";
  
  include_once("class/Company.class.php");
  include_once("class/Contact.class.php");
  include_once("class/ContactView.class.php");
  include_once("class/MultiRecord.class.php");
  include_once("class/ContactPhone.class.php");
  include_once("class/ContactEmail.class.php");
  include_once("class/ContactAddress.class.php");
  include_once("class/ContactInstantMessage.class.php");
  include_once("class/ContactWebsite.class.php");
  include_once("class/ContactRssFeed.class.php");
  include_once("class/CompanyEmail.class.php");
  include_once("class/CompanyPhone.class.php");
  include_once("class/CompanyAddress.class.php");
  include_once("class/CompanyWebsite.class.php");
  include_once("class/UserRelations.class.php");
  include_once("class/ContactSharing.class.php");
 
  include_once("class/Note.class.php");//base for Contact Note and Project Discuss  
  include_once("class/Task.class.php");
  include_once("class/TaskCategory.class.php");
  include_once("class/ContactNotes.class.php");
  include_once("class/NoteDraft.class.php");
  include_once("class/MessageDraft.class.php");
  include_once("class/DiscussionEmailSetting.class.php");
  include_once("class/UserSettings.class.php");

  include_once("class/Sync.class.php");
//  include_once("class/Note.class.php");
  include_once("class/Tag.class.php");  
//  include_once("class/Document.class.php");
  
  include_once("class/Activity.class.php");
  include_once("class/Breadcrumb.class.php");
  include_once("class/Message.class.php");
  include_once("class/MergeString.class.php");
  include_once("class/Feedback.class.php");
//  include_once("class/ContactMessage.class.php");
  include_once("class/EmailTemplateUser.class.php");
  include_once("class/Ofuz_Emailer.class.php");
  include_once("class/Project.class.php");
  include_once("class/ProjectTask.class.php");
  include_once("class/ProjectDiscuss.class.php");
  include_once("class/OfuzHorizontalSlider.class.php");
  include_once("class/ProjectSharing.class.php");

  include_once("class/Invoice.class.php");
  include_once("class/InvoiceLine.class.php");
  include_once("class/PaymentLog.class.php");
  include_once("class/PaymentInvoice.class.php");
  include_once("class/RecurrentInvoice.class.php");
  include_once("class/InvoiceCallback.class.php");
  include_once("class/RecurrentInvoiceCC.class.php");
  include_once("class/RegistrationInvoiceLog.class.php");
  include_once("class/DeletePaymentLog.class.php");
  include_once("class/OfuzCancelAccount.class.php");
  include_once("class/OfuzExportXML.class.php");
  include_once("class/UserInternalMarketing.class.php");
  include_once("class/TagInternalMarketing.class.php");
  include_once("class/Teams.class.php");
  include_once("class/ContactTeam.class.php");
  
  include_once("class/EventKey.class.php");
//  include_once("class/Task.class.php");
//  include_once("class/Project.class.php");

//  include_once("class/TimeWork.class.php");
  include_once("class/Feed.class.php");
  include_once("class/ContactImport.class.php");

  include_once("class/PageBuilderFieldImage.class.php");
  include_once("class/OfuzFieldTypePassword.class.php");
  include_once("class/OfuzFieldTypePassword2.class.php");
  include_once("class/OfuzFieldTypeEncryptedPassword.class.php");

//  include_once("class/WebFormField.class.php");
// include_once("class/WebFormUser.class.php");
//  include_once("class/WebFormUserField.class.php");
//  include_once("class/AutoResponder.class.php");
//  include_once("class/AutoResponderEmail.class.php");


  include_once("class/OfuzUserInterface.class.php");

  include_once("class/OfuzList.class.php");
  include_once("class/OfuzListContact.class.php");

  //Work feed 
  include_once("class/WorkFeed.class.php");
  include_once("class/WorkFeedItem.class.php");
  include_once("class/workfeed/WorkFeedContactNote.class.php");
  include_once("class/workfeed/WorkFeedProjectDiscuss.class.php");
  include_once("class/workfeed/WorkFeedProjectTask.class.php");
  include_once("class/workfeed/WorkFeedRssFeedImport.class.php");
  include_once("class/workfeed/WorkFeedContactNotePortal.class.php");
  include_once("class/workfeed/WorkFeedContactUnsubscibeEmails.class.php");
  include_once("class/workfeed/WorkFeedTwitterImport.class.php");
  // Ofuz API classes
  include_once("class/OfuzApiBase.class.php");
  include_once("class/OfuzApiMethods.class.php");
  

  //vCard Import
  include_once("class/VBook.class.php");

  //Report User Usage
  include_once("class/ReportUserUsage.class.php");

  //Login Auditing Related
  include_once("class/LoginAudit.class.php");
  
  //Twitter
  include_once("class/OfuzTwitter.class.php");
  
  include_once("class/OfuzFileUpload.class.php");
  include_once("class/OfuzFileDownload.class.php");
  //include_once("class/OfuzExpandingTextArea.class.php");

  //HTML clean up
  include_once("class/htmLawed.php");

  include_once("class/UserPlan.class.php");
  
  // Plugin Class
  include_once("class/PlugIn.class.php");
  include_once("class/BaseBlock.class.php");
  include_once("class/Tab.class.php");
  include_once("class/TabSetting.class.php");
  include_once("class/SubMenu.class.php");
  include_once("class/BaseMessageBlock.class.php");
  //include_once("class/PluginEnable.class.php");
  

  // Dynamic Buttons
  include_once("class/DynamicButton.class.php");

  //i18n
  include_once("class/OfuzUtilsi18n.class.php");

  //Email Parser
  include_once("class/MimeMailParserAttachment.class.php");
  include_once("class/MimeMailParser.class.php");
  include_once("class/OfuzEmailFetcher.class.php");

  define('RADRIA_LOG_RUN_OFUZ', false);
  define('OFUZ_LOG_RUN_TAG', false);
  define('OFUZ_LOG_RUN_CONTACT', false); 
  define('OFUZ_LOG_RUN_WEBFORM', false);
  define('OFUZ_LOG_RUN_MESSAGE', false);
   
  define('OFUZ_TTL_SHORT', 7200); 
  define('OFUZ_TTL', 86400);
  define('OFUZ_TTL_LONG', 864000);
  define('OFUZ_API_KEY', '');  
  
  define('OFUZ_COM', 'http://www.ofuz.com');
  define('OFUZ_NET', 'http://ofuz.localhost');
  define('EMAIL_DOMAIN', 'ofuz.net');

  //path
  define('XML_EXPORT', 'xml_export/');

  define('FACEBOOK_API_KEY','');
  define('FACEBOOK_APP_SECRET','');
  define('FACEBOOK_XD_RECEIVER_HTTP','http://www.ofuz.net/xd_receiver.htm');
  define('FACEBOOK_XD_RECEIVER_HTTPS','https://www.ofuz.net/xd_receiver.htm');

  define('TWITTER_CONSUMER_KEY','');
  define('TWITTER_CONSUMER_SECRET','');

  
  define('ENC_SECRECT_KEY','Opensource Rules'); // This is just for SQLFusion Do not disclose
  define('OFUZ_RESTAPIKEY_GEN_SECRECT_KEY','OFUZ_API_AUTHENTICATION_MD5_SECRET_KEY');
  
  // Detect the path to be used for url in emails or forwarded link or message
  $GLOBALS['cfg_ofuz_site_http_base'] = $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/";
  // Ugly hack, FIXME
  $GLOBALS['cfg_ofuz_site_https_base'] = "https://".str_replace("//", "/", $GLOBALS['cfg_ofuz_site_http_base']);
  $GLOBALS['cfg_ofuz_site_http_base'] = "http://".str_replace("//", "/", $GLOBALS['cfg_ofuz_site_http_base']);
  $GLOBALS['cfg_ofuz_email_support'] = "support@sqlfusion.com";
  $GLOBALS['email_domain'] = 'ofuz.net';

?>
