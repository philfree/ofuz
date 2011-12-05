Introduction
------------
Ofuz is a platform for freelancer to manage Contacts, Tasks, Project and Invoices.
Clean un simplified user interface, it uses a concept of Co-Workers to work with other freelancers and customers on 
projects and client relations.

EmailImporter Plugin
--------------------
This plugin gives you access to import your email contacts from Live/Hotmail and Yahoo!
If you wish, you can add more Email services.


To work with Email Contacts import plugin, you need to download OpenInviter package.

A: Register/Sign up in http://openinviter.com/index.php

B: Download:
   http://openinviter.com/download.php
   Download the "General usage pack."

C: Openinviter installation Guide:
   Once you have downloaded the openinviter package, please follow install.txt to test the system requirements.
   
D: Move files to EmailImporter plugin
   Move content(all files & floders) of downloaded OpenInviter folder to ofuz/plugin/EmailImporter/  folder.

E: Uncomment this line require_once("plugin/EmailImporter/openinviter.php");
   in plugin/EmailImporter.conf.inc.php file

E: goto ofuz settings page, you will get to see 'Email Contacts Import' plugin.
   Start importing.
   
