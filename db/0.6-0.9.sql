
CREATE TABLE `contact_team` (
  `idcontact_team` int(11) NOT NULL AUTO_INCREMENT,
  `idcontact` int(11) NOT NULL,
  `idteam` int(11) NOT NULL,
  `idcoworker` int(11) DEFAULT NULL,
  PRIMARY KEY (`idcontact_team`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

CREATE TABLE `plugin_enable` (
  `idplugin_enable` int(10) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(200) NOT NULL,
  `enabled` int(1) NOT NULL,
  `iduser` varchar(19) NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idplugin_enable`),
  KEY `iduser` (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

CREATE TABLE `referrer` (
  `idreferrer` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(150) NOT NULL DEFAULT '',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `iduser` int(10) NOT NULL DEFAULT '0',
  `recorded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `visitor` mediumtext,
  PRIMARY KEY (`idreferrer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `team` (
  `idteam` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) DEFAULT NULL,
  `team_name` varchar(200) DEFAULT NULL,
  `auto_share` varchar(3) DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  PRIMARY KEY (`idteam`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

CREATE TABLE `team_users` (
  `idteam_users` int(11) NOT NULL AUTO_INCREMENT,
  `idteam` int(11) NOT NULL,
  `idco_worker` int(11) NOT NULL,
  PRIMARY KEY (`idteam_users`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

CREATE TABLE `temp_import` (
  `idtemp_import` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `company` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idtemp_import`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `user_profile` (
  `iduser_profile` int(10) NOT NULL AUTO_INCREMENT,
  `logo` varchar(200) NOT NULL,
  `job_type` varchar(100) NOT NULL,
  `job_description` text NOT NULL,
  PRIMARY KEY (`iduser_profile`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

CREATE TABLE `userid1_contact` (
  `idcontact` int(10) NOT NULL DEFAULT '0',
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(60) NOT NULL,
  `company` varchar(70) NOT NULL,
  `idcompany` int(10) NOT NULL,
  `position` varchar(60) NOT NULL,
  `picture` varchar(200) NOT NULL,
  `email_address` varchar(180) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `tags` varchar(250) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `company` (`company`),
  KEY `tags` (`tags`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `userid2_contact` (
  `idcontact` int(10) NOT NULL DEFAULT '0',
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(60) NOT NULL,
  `company` varchar(70) NOT NULL,
  `idcompany` int(10) NOT NULL,
  `position` varchar(60) NOT NULL,
  `picture` varchar(200) NOT NULL,
  `email_address` varchar(180) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `tags` varchar(250) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `company` (`company`),
  KEY `tags` (`tags`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `userid3_contact` (
  `idcontact` int(10) NOT NULL DEFAULT '0',
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(60) NOT NULL,
  `company` varchar(70) NOT NULL,
  `idcompany` int(10) NOT NULL,
  `position` varchar(60) NOT NULL,
  `picture` varchar(200) NOT NULL,
  `email_address` varchar(180) DEFAULT NULL,
  `phone_number` varchar(30) DEFAULT NULL,
  `tags` varchar(250) NOT NULL,
  `last_activity` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `first_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  KEY `firstname` (`firstname`),
  KEY `lastname` (`lastname`),
  KEY `company` (`company`),
  KEY `tags` (`tags`),
  KEY `last_activity` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `activity` COLLATE=utf8_general_ci;

ALTER TABLE `breadcrumb` COLLATE=utf8_general_ci;

ALTER TABLE `company` COLLATE=utf8_general_ci;

ALTER TABLE `company_address` COLLATE=utf8_general_ci;

ALTER TABLE `company_email` COLLATE=utf8_general_ci;

ALTER TABLE `company_phone` COLLATE=utf8_general_ci;

ALTER TABLE `company_website` COLLATE=utf8_general_ci;

ALTER TABLE `contact_email` COLLATE=utf8_general_ci;

ALTER TABLE `contact_instant_message` COLLATE=utf8_general_ci;

ALTER TABLE `contact_phone` COLLATE=utf8_general_ci;

ALTER TABLE `contact_rss_feed` COLLATE=utf8_general_ci;

ALTER TABLE `contact_sharing` COLLATE=utf8_general_ci;

ALTER TABLE `contact_website` COLLATE=utf8_general_ci;

ALTER TABLE `countries` COLLATE=utf8_general_ci;

ALTER TABLE `created_date_log` COLLATE=utf8_general_ci;

ALTER TABLE `emailtemplate` COLLATE=utf8_general_ci;

ALTER TABLE `emailtemplate_user` COLLATE=utf8_general_ci;

ALTER TABLE `google_account` COLLATE=utf8_general_ci;

ALTER TABLE `google_contact` COLLATE=utf8_general_ci;

ALTER TABLE `google_contact_info` COLLATE=utf8_general_ci;

ALTER TABLE `message` COLLATE=utf8_general_ci;

ALTER TABLE `note_draft` COLLATE=utf8_general_ci;

ALTER TABLE `payment_invoice` COLLATE=utf8_general_ci;

ALTER TABLE `payments` COLLATE=utf8_general_ci;

ALTER TABLE `project` COLLATE=utf8_general_ci;

ALTER TABLE `project_sharing` COLLATE=utf8_general_ci;

ALTER TABLE `project_task` COLLATE=utf8_general_ci;

ALTER TABLE `states` COLLATE=utf8_general_ci;

ALTER TABLE `tag_association` COLLATE=utf8_general_ci;

ALTER TABLE `tag_click` COLLATE=utf8_general_ci;

ALTER TABLE `tag_size` COLLATE=utf8_general_ci;

ALTER TABLE `task` COLLATE=utf8_general_ci;

ALTER TABLE `task_category` COLLATE=utf8_general_ci;

ALTER TABLE `temp_gmail_emails` COLLATE=utf8_general_ci;

ALTER TABLE `updated_date_log` COLLATE=utf8_general_ci;

ALTER TABLE `user_relations` COLLATE=utf8_general_ci;

ALTER TABLE `user_settings` COLLATE=utf8_general_ci;

ALTER TABLE `webformfields` COLLATE=utf8_general_ci;

ALTER TABLE `webformuser` COLLATE=utf8_general_ci;

ALTER TABLE `webformuserfield` COLLATE=utf8_general_ci;

ALTER TABLE `workfeed` COLLATE=utf8_general_ci;

ALTER TABLE `autoresponder_email` MODIFY `bodyhtml` text NOT NULL;
DROP TABLE `breadcrumb`;
CREATE TABLE `breadcrumb` (
  `idbreadcrumb` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `type` varchar(40) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int(10) NOT NULL,
  PRIMARY KEY (`idbreadcrumb`),
  KEY `iduser` (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=152 DEFAULT CHARSET=utf8;

ALTER TABLE `company` MODIFY `name` varchar(70) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company` MODIFY `iduser` int(15) NOT NULL DEFAULT '0';
ALTER TABLE `company_address` MODIFY `street` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `company_address` MODIFY `city` varchar(70) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_address` MODIFY `state` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_address` MODIFY `zipcode` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_address` MODIFY `country` varchar(60) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_address` MODIFY `idcompany` int(15) NOT NULL DEFAULT '0';
ALTER TABLE `company_address` MODIFY `address` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `company_address` MODIFY `address_type` varchar(10) COLLATE ucs2_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_email` MODIFY `idcompany` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_email` MODIFY `email_address` varchar(150) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_email` MODIFY `email_type` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_phone` MODIFY `phone_number` varchar(30) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_phone` MODIFY `phone_type` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_phone` MODIFY `idcompany` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_website` MODIFY `idcompany` varchar(15) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_website` MODIFY `website` varchar(200) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `company_website` MODIFY `website_type` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact` MODIFY `idcompany` int(10) NOT NULL DEFAULT '0';
ALTER TABLE `contact` MODIFY `iduser` int(15) NOT NULL DEFAULT '0';
ALTER TABLE `contact_address` MODIFY `idcontact` int(15) NOT NULL DEFAULT '0';
ALTER TABLE `contact_email` MODIFY `idcontact` int(10) NOT NULL DEFAULT '0';
ALTER TABLE `contact_email` MODIFY `email_address` varchar(180) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_email` MODIFY `email_type` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_email` MODIFY `email_isdefault` char(1) COLLATE utf8_general_ci NOT NULL DEFAULT 'n';
ALTER TABLE `contact_instant_message` MODIFY `idcontact` int(14) NOT NULL DEFAULT '0';
ALTER TABLE `contact_instant_message` MODIFY `im_options` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_instant_message` MODIFY `im_type` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_instant_message` MODIFY `im_username` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_note` MODIFY `date_added` date NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `contact_note` MODIFY `hours_work` float(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `contact_note` MODIFY `note_visibility` varchar(50) NOT NULL DEFAULT 'user coworker contact' AFTER `hours_work`;
ALTER TABLE `contact_note` ADD COLUMN `type` varchar(100) COLLATE 'utf8_general_ci' NOT NULL AFTER `note_visibility`;
ALTER TABLE `contact_phone` MODIFY `phone_number` varchar(30) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_phone` MODIFY `phone_type` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_phone` MODIFY `idcontact` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_rss_feed` MODIFY `idcontact` varchar(15) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_rss_feed` MODIFY `rss_feed_url` varchar(200) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_rss_feed` MODIFY `username` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_rss_feed` MODIFY `feed_type` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_rss_feed` MODIFY `import_to_note` varchar(10) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_website` MODIFY `website` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_website` MODIFY `website_type` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `contact_website` MODIFY `idcontact` varchar(15) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `contact_website` MODIFY `feed_auto_fetch` varchar(5) COLLATE ucs2_general_ci NOT NULL DEFAULT 'None';
ALTER TABLE `countries` MODIFY `name` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `created_date_log` MODIFY `table_name` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `created_date_log` MODIFY `id` int(15) NOT NULL DEFAULT '0';
ALTER TABLE `discussion_email_setting` MODIFY `iduser` int(14) NOT NULL;
ALTER TABLE `discussion_email_setting` MODIFY `discussion_email_alert` varchar(5) NOT NULL;
ALTER TABLE `emailtemplate` MODIFY `subject` varchar(150) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate` MODIFY `bodytext` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailtemplate` MODIFY `bodyhtml` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailtemplate` MODIFY `name` varchar(254) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate` MODIFY `sendername` varchar(254) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate` MODIFY `senderemail` varchar(254) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate` MODIFY `thumbnail` varchar(70) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate` MODIFY `internal` varchar(10) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate` MODIFY `language` varchar(30) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailtemplate_user` MODIFY `idemailtemplate_user` int(10) NOT NULL auto_increment;
ALTER TABLE `emailtemplate_user` MODIFY `subject` varchar(150) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate_user` MODIFY `bodytext` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailtemplate_user` MODIFY `bodyhtml` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `emailtemplate_user` MODIFY `name` varchar(254) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate_user` MODIFY `sendername` varchar(254) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate_user` MODIFY `senderemail` varchar(254) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `emailtemplate_user` MODIFY `language` varchar(30) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `google_account` MODIFY `iduser` int(15) NOT NULL DEFAULT '0';
ALTER TABLE `google_account` MODIFY `session_token` varchar(200) COLLATE utf8_general_ci NULL;
ALTER TABLE `google_account` MODIFY `user_name` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `google_contact` MODIFY `mode` varchar(5) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `google_contact_info` MODIFY `entry_id` varchar(200) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `google_contact_info` MODIFY `entry_link_self` varchar(200) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `google_contact_info` MODIFY `entry_link_edit` varchar(200) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `google_contact_info` MODIFY `idcontact` int(15) NOT NULL DEFAULT '0';
ALTER TABLE `invoice` MODIFY `tax` varchar(20) NOT NULL AFTER `idcompany`;
ALTER TABLE `invoice` ADD COLUMN `set_delete` int(1) NOT NULL DEFAULT '0' AFTER `tax`;
ALTER TABLE `invoice` MODIFY `total_discounted_amt` float(15,2) NOT NULL AFTER `set_delete`;
ALTER TABLE `invoice` MODIFY `total_taxed_amount` float(15,2) NOT NULL AFTER `total_discounted_amt`;
ALTER TABLE `message` MODIFY `key_name` varchar(150) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `message` MODIFY `content` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `message` MODIFY `language` varchar(50) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `message` MODIFY `context` varchar(60) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `message` MODIFY `can_close` varchar(3) COLLATE utf8_general_ci NOT NULL DEFAULT 'yes';
ALTER TABLE `message` MODIFY `close_duration` varchar(20) COLLATE utf8_general_ci NOT NULL DEFAULT '1 month';
ALTER TABLE `message` MODIFY `plan` varchar(10) COLLATE utf8_general_ci NULL DEFAULT 'all';
ALTER TABLE `note_draft` MODIFY `id_type` varchar(50) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `note_draft` MODIFY `note_content` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `note_draft` MODIFY `timestamp` varchar(30) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `payments` MODIFY `reference` varchar(50) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `project` MODIFY `name` varchar(100) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `project` MODIFY `idcompany` varchar(254) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `project` MODIFY `status` varchar(15) COLLATE utf8_general_ci NOT NULL DEFAULT 'open' AFTER `idcompany`;
ALTER TABLE `project` MODIFY `effort_estimated_hrs` float(10,2) NOT NULL AFTER `status`;
ALTER TABLE `project_discuss` MODIFY `discuss` text NULL;
ALTER TABLE `project_discuss` MODIFY `hours_work` float(10,2) NOT NULL DEFAULT '0.00';
ALTER TABLE `project_task` MODIFY `progress` varchar(254) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `states` MODIFY `name_short` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `tag` MODIFY `iduser` int(10) NOT NULL DEFAULT '0';
ALTER TABLE `tag` MODIFY `idreference` int(15) NOT NULL DEFAULT '0';
ALTER TABLE `tag` MODIFY `date_added` date NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `tag_association` MODIFY `idtag` varchar(16) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `tag_association` MODIFY `iduser` varchar(16) COLLATE utf8_latvian_ci NOT NULL DEFAULT '';
ALTER TABLE `tag_association` MODIFY `reference_type` varchar(10) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `tag_association` MODIFY `idreference` int(14) NOT NULL DEFAULT '0';
ALTER TABLE `tag_association` MODIFY `date_added` date NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `tag_click` MODIFY `tag_name` varchar(200) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `tag_size` MODIFY `tag_name` varchar(200) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `task` MODIFY `task_description` varchar(200) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `task` MODIFY `due_date` varchar(50) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `task` MODIFY `category` varchar(14) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `task` MODIFY `iduser` int(14) NOT NULL DEFAULT '0';
ALTER TABLE `task` MODIFY `due_date_dateformat` date NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `task` MODIFY `status` varchar(10) COLLATE utf8_general_ci NOT NULL DEFAULT 'open';
ALTER TABLE `task` MODIFY `date_completed` date NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `task` MODIFY `is_sp_date_set` varchar(4) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `task` MODIFY `task_category` varchar(100) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `task` ADD COLUMN `priority` int(11) NOT NULL AFTER `task_category`;
ALTER TABLE `task_category` MODIFY `name` varchar(100) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `temp_gmail_emails` MODIFY `email_address` varchar(180) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `updated_date_log` MODIFY `tablename` varchar(40) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `updated_date_log` MODIFY `primarykeyvalue` int(10) NOT NULL DEFAULT '0';
ALTER TABLE `user` MODIFY `firstname` char(40) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `middlename` char(20) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `lastname` char(40) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `email` char(80) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `phone` char(30) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `company` char(40) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `position` char(40) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `address1` char(80) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `address2` char(80) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `city` char(40) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `zip` char(20) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `state` char(30) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `country` char(40) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `username` char(20) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `password` char(20) NOT NULL DEFAULT '';
ALTER TABLE `user` MODIFY `last_login` date NOT NULL DEFAULT '0000-00-00';
ALTER TABLE `user` MODIFY `drop_box_code` int(15) NOT NULL;
ALTER TABLE `user` MODIFY `fb_user_id` int(14) NULL DEFAULT '0';
ALTER TABLE `user` MODIFY `plan` varchar(200) NOT NULL DEFAULT 'free';
ALTER TABLE `user` MODIFY `status` varchar(200) NOT NULL DEFAULT 'active';
ALTER TABLE `user_relations` MODIFY `idcoworker` varchar(14) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `user_relations` MODIFY `accepted` varchar(20) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `user_relations` MODIFY `enc_email` varchar(200) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `user_settings` MODIFY `setting_name` varchar(100) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `user_settings` MODIFY `setting_value` varchar(100) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformfields` MODIFY `label` varchar(60) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformfields` MODIFY `name` varchar(30) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformfields` MODIFY `required` varchar(1) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformfields` MODIFY `class` varchar(40) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformfields` MODIFY `variable` varchar(40) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformfields` MODIFY `variable_type` varchar(20) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformfields` MODIFY `field_type` varchar(50) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformuser` MODIFY `title` varchar(80) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformuser` MODIFY `description` varchar(254) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformuser` MODIFY `tags` varchar(200) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformuser` MODIFY `urlnext` varchar(200) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformuser` MODIFY `email_alert` varchar(1) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformuserfield` MODIFY `name` varchar(60) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformuserfield` MODIFY `required` varchar(1) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `webformuserfield` MODIFY `size` varchar(10) COLLATE utf8_general_ci NULL;
ALTER TABLE `webformuserfield` MODIFY `label` varchar(200) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `workfeed` MODIFY `feed_data` text COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `workfeed` MODIFY `feed_type` varchar(50) COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `activity` DROP INDEX idactivity_2;
ALTER TABLE `activity` DROP INDEX idactivity;
ALTER TABLE `autoresponder` DROP INDEX idautoresponder_2;
ALTER TABLE `autoresponder` DROP INDEX idautoresponder;
ALTER TABLE `autoresponder_email` DROP INDEX idautoresponder_email_2;
ALTER TABLE `autoresponder_email` DROP INDEX idautoresponder_email;
ALTER TABLE `company` DROP INDEX iduser;
ALTER TABLE `company_address` DROP INDEX idcompany_address_2;
ALTER TABLE `company_address` DROP INDEX idcompany_address;
ALTER TABLE `company_email` DROP INDEX idcompany_email_2;
ALTER TABLE `company_email` DROP INDEX idcompany_email;
ALTER TABLE `company_website` DROP INDEX idcompany_website_2;
ALTER TABLE `company_website` DROP INDEX idcompany_website;
ALTER TABLE `contact_address` DROP INDEX idcontact_address_2;
ALTER TABLE `contact_address` DROP INDEX idcontact_address;
ALTER TABLE `contact_instant_message` DROP INDEX idcontact_instant_message_2;
ALTER TABLE `contact_instant_message` DROP INDEX idcontact_instant_message;
ALTER TABLE `contact_note` DROP INDEX iduser;
ALTER TABLE `contact_portal_message` DROP INDEX idcontact_portal_message_2;
ALTER TABLE `contact_portal_message` DROP INDEX idcontact_portal_message;
ALTER TABLE `contact_rss_feed` DROP INDEX idcontact_rss_feed_2;
ALTER TABLE `contact_rss_feed` DROP INDEX idcontact_rss_feed;
CREATE INDEX idcontact ON `contact_rss_feed` (idcontact);
ALTER TABLE `contact_sharing` DROP INDEX idcontact_sharing_2;
ALTER TABLE `contact_sharing` DROP INDEX idcontact_sharing;
ALTER TABLE `contact_website` DROP INDEX idcontact;
ALTER TABLE `delete_paymentlog` DROP INDEX iddelete_paymentlog_2;
ALTER TABLE `delete_paymentlog` DROP INDEX iddelete_paymentlog;
ALTER TABLE `google_account` DROP INDEX idgoogle_account_2;
ALTER TABLE `google_account` DROP INDEX idgoogle_account;
ALTER TABLE `google_contact` DROP INDEX idgoogle_contact_2;
ALTER TABLE `google_contact` DROP INDEX idgoogle_contact;
ALTER TABLE `google_contact_info` DROP INDEX idgoogle_contact_info_2;
ALTER TABLE `google_contact_info` DROP INDEX idgoogle_contact_info;
ALTER TABLE `invoice` DROP INDEX idinvoice_2;
ALTER TABLE `invoice` DROP INDEX idinvoice;
CREATE INDEX iduser ON `invoice` (iduser);
ALTER TABLE `invoice_callback` DROP INDEX idinvoice_callback_2;
ALTER TABLE `invoice_callback` DROP INDEX idinvoice_callback;
ALTER TABLE `invoiceline` DROP INDEX idinvoiceline_2;
ALTER TABLE `invoiceline` DROP INDEX idinvoiceline;
CREATE INDEX idinvoice ON `invoiceline` (idinvoice);
ALTER TABLE `login_audit` DROP INDEX idlogin_audit_2;
ALTER TABLE `login_audit` DROP INDEX idlogin_audit;
CREATE INDEX iduser ON `login_audit` (iduser);
ALTER TABLE `message_draft` DROP INDEX idmessage_draft_2;
ALTER TABLE `message_draft` DROP INDEX idmessage_draft;
ALTER TABLE `note_draft` DROP INDEX idnote_draft_2;
ALTER TABLE `note_draft` DROP INDEX idnote_draft;
ALTER TABLE `payment_invoice` DROP INDEX idpayment_invoice_2;
ALTER TABLE `payment_invoice` DROP INDEX idpayment_invoice;
CREATE INDEX idinvoice ON `payment_invoice` (idinvoice);
ALTER TABLE `paymentlog` DROP INDEX idpaymentlog_2;
ALTER TABLE `paymentlog` DROP INDEX idpaymentlog;
CREATE INDEX idinvoice ON `paymentlog` (idinvoice);
ALTER TABLE `paymentlog_extra_amount` DROP INDEX idpaymentlog_extra_amount_2;
ALTER TABLE `paymentlog_extra_amount` DROP INDEX idpaymentlog_extra_amount;
ALTER TABLE `payments` DROP INDEX idpayments_2;
ALTER TABLE `payments` DROP INDEX idpayments;
ALTER TABLE `project` DROP INDEX idproject_2;
ALTER TABLE `project` DROP INDEX idproject;
CREATE INDEX iduser ON `project` (iduser);
CREATE INDEX idtask ON `project_discuss` (idtask);
CREATE INDEX idproject ON `project_discuss` (idproject);
CREATE INDEX date_added ON `project_discuss` (date_added);
ALTER TABLE `project_sharing` DROP INDEX idproject;
ALTER TABLE `project_sharing` DROP INDEX iduser;
ALTER TABLE `project_sharing` DROP INDEX idcoworker;
ALTER TABLE `project_task` DROP INDEX idtask;
ALTER TABLE `project_task` DROP INDEX idproject;
CREATE INDEX idtask_idx ON `project_task` (idtask);
CREATE INDEX idproject_idx ON `project_task` (idproject);
CREATE INDEX priority_idx ON `project_task` (priority);
ALTER TABLE `recurrentinvoice` DROP INDEX idrecurrentinvoice_2;
ALTER TABLE `recurrentinvoice` DROP INDEX idrecurrentinvoice;
CREATE INDEX idinvoice_idx ON `recurrentinvoice` (idinvoice);
ALTER TABLE `reg_invoice_log` DROP INDEX idreg_invoice_log_2;
ALTER TABLE `reg_invoice_log` DROP INDEX idreg_invoice_log;
ALTER TABLE `tag` DROP INDEX idtag_2;
ALTER TABLE `tag` DROP INDEX idtag;
CREATE INDEX name ON `tag` (tag_name);
ALTER TABLE `tag_association` DROP INDEX idtag_association_2;
ALTER TABLE `tag_association` DROP INDEX idtag_association;
ALTER TABLE `task` DROP INDEX iduser;
ALTER TABLE `task` DROP INDEX idcontact;
CREATE INDEX iduser_idx ON `task` (iduser);
CREATE INDEX idcontact_idx ON `task` (idcontact);
ALTER TABLE `task_category` DROP INDEX iduser;
CREATE INDEX iduser_idx ON `task_category` (iduser);
ALTER TABLE `temp_gmail_emails` DROP INDEX idtemp_gmail_emails_2;
ALTER TABLE `temp_gmail_emails` DROP INDEX idtemp_gmail_emails;
ALTER TABLE `twitter_account` DROP INDEX idtwitter_account;
ALTER TABLE `user` DROP INDEX idusers;
ALTER TABLE `user` DROP INDEX idusers_2;
ALTER TABLE `user_relations` DROP INDEX idcoworker;
ALTER TABLE `user_relations` DROP INDEX idcoworker_2;
ALTER TABLE `user_relations` DROP INDEX iduser;
ALTER TABLE `user_settings` DROP INDEX iduser_settings_2;
ALTER TABLE `user_settings` DROP INDEX iduser_settings;
CREATE INDEX iduser_idx ON `user_settings` (iduser);
ALTER TABLE `webformfields` DROP INDEX idwebformfields_2;
ALTER TABLE `webformfields` DROP INDEX idwebformfields;
ALTER TABLE `webformuser` DROP INDEX idwebformuser_2;
ALTER TABLE `webformuser` DROP INDEX idwebformuser;
CREATE INDEX iduser_idx ON `webformuser` (iduser);
ALTER TABLE `webformuserfield` DROP INDEX idwebformuserfield_2;
ALTER TABLE `webformuserfield` DROP INDEX idwebformuserfield;
CREATE INDEX idwebformuser_idx ON `webformuserfield` (idwebformuser);
ALTER TABLE `workfeed` DROP INDEX idworkfeed_2;
ALTER TABLE `workfeed` DROP INDEX idworkfeed;
CREATE INDEX iduser_idx ON `workfeed` (iduser);
