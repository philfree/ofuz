<?php 
// Copyright 2001 - 2007 SQLFusion LLC           info@sqlfusion.com

   /**
    * Default index page.
	*
    * @package RadriaSampleSite
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2012
    * @version 1.0
	*/

  $pageTitle = "Home" ;
  $Author = "SQLFusion";
  $Keywords = "Keywords for search engine";
  $Description = "Description for search engine";
  $background_color = "white";
  $background_image = "";
  
  include_once("config.php") ;
  include("includes/header.inc.php") ;
?>
<h1>Radria Sample app </h1>

<h3>Setup the database</h3>
<pre>
create database RadriaSample;
grant all privileges on RadriaSample.* to radria@localhost identified by 'sample';
flush privileges;

CREATE TABLE `user` (
  `iduser` int(10) NOT NULL AUTO_INCREMENT,
  `firstname` char(40) NOT NULL DEFAULT '',
  `lastname` char(40) NOT NULL DEFAULT '',
  `email` char(80) NOT NULL DEFAULT '',
  `username` char(20) NOT NULL DEFAULT '',
  `password` char(20) NOT NULL DEFAULT '',
  `regdate` date DEFAULT NULL,
  `status` varchar(200) NOT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `invoice` (
  `idinvoice` int(10) NOT NULL AUTO_INCREMENT,
  `num` int(14) NOT NULL,
  `iduser` int(14) NOT NULL,
  `description` mediumtext NOT NULL,
  `amount` float(10,2) NOT NULL,
  `datepaid` date NOT NULL,
  `datecreated` date NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`idinvoice`),
  KEY `iduser` (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


</pre>
<h3>Give a try</h3>
<div>
<a href="users.php">Manage Users</a>
</div>
<?
  include("includes/footer.inc.php");
?>
