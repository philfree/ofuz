-- MySQL dump 10.13  Distrib 5.7.9, for osx10.9 (x86_64)
--
-- Host: 10.0.3.196    Database: gishwhes_hunt
-- ------------------------------------------------------
-- Server version	5.5.44-0+deb8u1
CREATE DATABASE IF NOT EXISTS gishwhes_blog;
CREATE DATABASE IF NOT EXISTS gishwhes_hunt;

USE gishwhes_hunt;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `FinalSubmission2013`
--

DROP TABLE IF EXISTS `FinalSubmission2013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FinalSubmission2013` (
  `idreviewer_final_submissions` int(11) NOT NULL AUTO_INCREMENT,
  `idreviewer` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `link` varchar(256) NOT NULL,
  PRIMARY KEY (`idreviewer_final_submissions`),
  KEY `itemid_idx` (`itemid`),
  KEY `idreviewerf_idx` (`idreviewer`)
) ENGINE=InnoDB AUTO_INCREMENT=8427 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FinalSubmission2014`
--

DROP TABLE IF EXISTS `FinalSubmission2014`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FinalSubmission2014` (
  `idreviewer_final_submissions` int(11) NOT NULL AUTO_INCREMENT,
  `idreviewer` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `link` varchar(256) NOT NULL,
  PRIMARY KEY (`idreviewer_final_submissions`),
  KEY `itemid_idx` (`itemid`),
  KEY `idreviewerf_idx` (`idreviewer`)
) ENGINE=InnoDB AUTO_INCREMENT=198750 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `admin_user`
--

DROP TABLE IF EXISTS `admin_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_user` (
  `idadmin_user` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  PRIMARY KEY (`idadmin_user`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ajax_chat_bans`
--

DROP TABLE IF EXISTS `ajax_chat_bans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ajax_chat_bans` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `userName` (`userName`),
  KEY `dateTime` (`dateTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ajax_chat_invitations`
--

DROP TABLE IF EXISTS `ajax_chat_invitations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ajax_chat_invitations` (
  `userID` int(11) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  PRIMARY KEY (`userID`,`channel`),
  KEY `dateTime` (`dateTime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ajax_chat_messages`
--

DROP TABLE IF EXISTS `ajax_chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ajax_chat_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `userRole` int(1) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  `text` text COLLATE utf8_bin,
  PRIMARY KEY (`id`),
  KEY `message_condition` (`id`,`channel`,`dateTime`),
  KEY `dateTime` (`dateTime`)
) ENGINE=MyISAM AUTO_INCREMENT=198 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ajax_chat_online`
--

DROP TABLE IF EXISTS `ajax_chat_online`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ajax_chat_online` (
  `userID` int(11) NOT NULL,
  `userName` varchar(64) COLLATE utf8_bin NOT NULL,
  `userRole` int(1) NOT NULL,
  `channel` int(11) NOT NULL,
  `dateTime` datetime NOT NULL,
  `ip` varbinary(16) NOT NULL,
  PRIMARY KEY (`userID`),
  KEY `userName` (`userName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `birthday`
--

DROP TABLE IF EXISTS `birthday`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `birthday` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subject` text,
  `message` text,
  `testmessage` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `country` (
  `idcountry` int(11) NOT NULL AUTO_INCREMENT,
  `country_code` varchar(3) DEFAULT NULL,
  `country_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idcountry`)
) ENGINE=MyISAM AUTO_INCREMENT=250 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_queue`
--

DROP TABLE IF EXISTS `email_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_queue` (
  `idemail_queue` int(50) NOT NULL AUTO_INCREMENT,
  `email_group` varchar(100) NOT NULL,
  `from_name` varchar(50) NOT NULL,
  `from_email` varchar(50) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `text` longtext NOT NULL,
  `html` longtext NOT NULL,
  `attachment` varchar(50) NOT NULL,
  `status` enum('start','processing','sent') NOT NULL,
  PRIMARY KEY (`idemail_queue`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `extra_url`
--

DROP TABLE IF EXISTS `extra_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `extra_url` (
  `idextra_url` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `url` varchar(200) NOT NULL,
  PRIMARY KEY (`idextra_url`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fegvep`
--

DROP TABLE IF EXISTS `fegvep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fegvep` (
  `idfegvep` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idfegvep`)
) ENGINE=InnoDB AUTO_INCREMENT=795 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `final_item_selections`
--

DROP TABLE IF EXISTS `final_item_selections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `final_item_selections` (
  `idfinal_item_selections` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `link` varchar(256) NOT NULL,
  `comment` text,
  `team_name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`idfinal_item_selections`)
) ENGINE=InnoDB AUTO_INCREMENT=1125 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `flags`
--

DROP TABLE IF EXISTS `flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `flags` (
  `idflag` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  `orderid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idflag`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ghof2015`
--

DROP TABLE IF EXISTS `ghof2015`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ghof2015` (
  `idfinal_item_selections` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `link` varchar(256) NOT NULL,
  `comment` text,
  `team_name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`idfinal_item_selections`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ghof_hopefuls`
--

DROP TABLE IF EXISTS `ghof_hopefuls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ghof_hopefuls` (
  `idghof_hopefuls` int(11) NOT NULL AUTO_INCREMENT,
  `itemid` int(11) NOT NULL,
  `link` varchar(256) NOT NULL,
  `username` varchar(256) DEFAULT NULL,
  `team_name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`idghof_hopefuls`)
) ENGINE=InnoDB AUTO_INCREMENT=332 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gish_roll`
--

DROP TABLE IF EXISTS `gish_roll`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gish_roll` (
  `idgish_roll` int(10) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(200) NOT NULL,
  PRIMARY KEY (`idgish_roll`)
) ENGINE=MyISAM AUTO_INCREMENT=837 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gisherouttakes`
--

DROP TABLE IF EXISTS `gisherouttakes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gisherouttakes` (
  `idgisherouttake` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idgisherouttake`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gisholar`
--

DROP TABLE IF EXISTS `gisholar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gisholar` (
  `idgisholar` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  PRIMARY KEY (`idgisholar`)
) ENGINE=MyISAM AUTO_INCREMENT=10753 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gisholarship_questions`
--

DROP TABLE IF EXISTS `gisholarship_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gisholarship_questions` (
  `idgisholarship_questions` int(11) NOT NULL AUTO_INCREMENT,
  `idusers` int(11) NOT NULL,
  `answer1` text NOT NULL,
  `answer2` text NOT NULL,
  `answer3` text NOT NULL,
  PRIMARY KEY (`idgisholarship_questions`)
) ENGINE=MyISAM AUTO_INCREMENT=10568 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gishwheshiest`
--

DROP TABLE IF EXISTS `gishwheshiest`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gishwheshiest` (
  `idgishwheshiest` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  `idteam` varchar(50) DEFAULT NULL,
  `link1` varchar(50) DEFAULT NULL,
  `link2` varchar(50) DEFAULT NULL,
  `IP` varchar(1000) DEFAULT NULL,
  `chosenlink` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idgishwheshiest`)
) ENGINE=MyISAM AUTO_INCREMENT=1185 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gishwheshiest2013`
--

DROP TABLE IF EXISTS `gishwheshiest2013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gishwheshiest2013` (
  `idgishwheshiest` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  `idteam` varchar(50) DEFAULT NULL,
  `link1` varchar(50) DEFAULT NULL,
  `link2` varchar(50) DEFAULT NULL,
  `IP` varchar(1000) DEFAULT NULL,
  `chosenlink` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idgishwheshiest`)
) ENGINE=MyISAM AUTO_INCREMENT=1368 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `gp_tier`
--

DROP TABLE IF EXISTS `gp_tier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `gp_tier` (
  `idgp_tier` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tier_name` varchar(50) DEFAULT NULL,
  `tier_min` int(11) DEFAULT NULL,
  `tier_max` int(11) DEFAULT NULL,
  PRIMARY KEY (`idgp_tier`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hugs`
--

DROP TABLE IF EXISTS `hugs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hugs` (
  `idhug` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(50) DEFAULT NULL,
  `awslink` varchar(50) DEFAULT NULL,
  `referrer_email` varchar(50) DEFAULT NULL,
  `add_to_mailing_list` varchar(50) DEFAULT NULL,
  `date_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `IP` varchar(1000) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `DQ` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`idhug`)
) ENGINE=MyISAM AUTO_INCREMENT=119051 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hugs_backup`
--

DROP TABLE IF EXISTS `hugs_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hugs_backup` (
  `idhug` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(50) DEFAULT NULL,
  `referrer_email` varchar(50) DEFAULT NULL,
  `add_to_mailing_list` varchar(50) DEFAULT NULL,
  `date_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `IP` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`idhug`)
) ENGINE=MyISAM AUTO_INCREMENT=119051 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hugs_backup_2`
--

DROP TABLE IF EXISTS `hugs_backup_2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hugs_backup_2` (
  `idhug` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(50) DEFAULT NULL,
  `referrer_email` varchar(50) DEFAULT NULL,
  `add_to_mailing_list` varchar(50) DEFAULT NULL,
  `date_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `IP` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`idhug`)
) ENGINE=MyISAM AUTO_INCREMENT=119051 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hugs_backup_3`
--

DROP TABLE IF EXISTS `hugs_backup_3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hugs_backup_3` (
  `idhug` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(50) DEFAULT NULL,
  `referrer_email` varchar(50) DEFAULT NULL,
  `add_to_mailing_list` varchar(50) DEFAULT NULL,
  `date_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `IP` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`idhug`)
) ENGINE=MyISAM AUTO_INCREMENT=119051 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ismishainnorthamerica`
--

DROP TABLE IF EXISTS `ismishainnorthamerica`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ismishainnorthamerica` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `image` varchar(20) DEFAULT '',
  `answer` varchar(20) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_list`
--

DROP TABLE IF EXISTS `item_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_list` (
  `iditem_list` int(11) NOT NULL AUTO_INCREMENT,
  `item` text,
  `points` int(11) DEFAULT NULL,
  PRIMARY KEY (`iditem_list`)
) ENGINE=MyISAM AUTO_INCREMENT=217 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_list_points`
--

DROP TABLE IF EXISTS `item_list_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_list_points` (
  `iditem_list_points` int(11) NOT NULL AUTO_INCREMENT,
  `idteam` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `link` text,
  `link_type` varchar(15) DEFAULT NULL,
  `submitted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iditem` int(11) NOT NULL DEFAULT '0',
  `coffee` int(11) DEFAULT '0',
  `awesome` int(11) DEFAULT '0',
  `comment_on_item` text CHARACTER SET utf8 COLLATE utf8_general_mysql500_ci,
  PRIMARY KEY (`iditem_list_points`),
  KEY `idteam_idx` (`idteam`),
  KEY `iditem_idx` (`iditem`)
) ENGINE=InnoDB AUTO_INCREMENT=165782 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_review`
--

DROP TABLE IF EXISTS `item_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_review` (
  `iditem_review` int(11) NOT NULL AUTO_INCREMENT,
  `hash_key` varchar(256) NOT NULL,
  `idreviewer` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `user_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'checks user is active or not',
  `item_range_start` int(11) NOT NULL,
  `item_range_end` int(11) NOT NULL,
  PRIMARY KEY (`iditem_review`),
  KEY `idreviewer_idx` (`idreviewer`),
  KEY `user_status_idx` (`user_status`),
  KEY `hash_key_idx` (`hash_key`),
  KEY `itemid_idx` (`itemid`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `item_review_detail`
--

DROP TABLE IF EXISTS `item_review_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_review_detail` (
  `iditem_review_detail` int(11) NOT NULL AUTO_INCREMENT,
  `iditem` int(11) NOT NULL,
  `iditem_list_points` int(11) NOT NULL,
  `flag` int(11) NOT NULL,
  `point` float NOT NULL,
  `round` int(11) NOT NULL DEFAULT '1',
  `idreviewer` int(11) NOT NULL,
  PRIMARY KEY (`iditem_review_detail`),
  KEY `reviewer` (`idreviewer`),
  KEY `item` (`iditem`),
  KEY `itempoints` (`iditem_list_points`),
  KEY `iditem_idx` (`iditem`),
  KEY `idreviewer_idx` (`idreviewer`),
  KEY `round_idx` (`round`),
  KEY `flag_idx` (`flag`),
  KEY `iditem_list_points_idx` (`iditem_list_points`)
) ENGINE=InnoDB AUTO_INCREMENT=2567191 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `itemsubmission_division`
--

DROP TABLE IF EXISTS `itemsubmission_division`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `itemsubmission_division` (
  `iditemsubmission_division` int(11) NOT NULL AUTO_INCREMENT,
  `idreviewer` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `range` varchar(256) NOT NULL,
  PRIMARY KEY (`iditemsubmission_division`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `markers_geocode`
--

DROP TABLE IF EXISTS `markers_geocode`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `markers_geocode` (
  `idmarkers_geocode` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(200) NOT NULL,
  `state` varchar(200) NOT NULL,
  `lat` varchar(100) DEFAULT NULL,
  `lng` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idmarkers_geocode`),
  KEY `city_idx` (`city`)
) ENGINE=MyISAM AUTO_INCREMENT=10074 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paid_gisholarship`
--

DROP TABLE IF EXISTS `paid_gisholarship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paid_gisholarship` (
  `idpaid_gisholarship` int(11) NOT NULL AUTO_INCREMENT,
  `paid_by_iduser` int(11) NOT NULL,
  `idtransaction` int(11) NOT NULL,
  `share_email` enum('Yes','No') NOT NULL DEFAULT 'No',
  `idgisholar` int(11) NOT NULL,
  PRIMARY KEY (`idpaid_gisholarship`)
) ENGINE=MyISAM AUTO_INCREMENT=8616 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paid_invitation`
--

DROP TABLE IF EXISTS `paid_invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paid_invitation` (
  `idpaid_invitation` int(12) NOT NULL AUTO_INCREMENT,
  `paid_by_iduser` int(12) NOT NULL COMMENT 'ID user',
  `guest_email` varchar(50) NOT NULL COMMENT 'Guest email',
  `guest_iduser` int(11) DEFAULT NULL,
  `idtransaction` int(12) NOT NULL COMMENT 'Transaction id',
  PRIMARY KEY (`idpaid_invitation`),
  KEY `paid_by_iduser_idx` (`paid_by_iduser`),
  KEY `guest_iduser_idx` (`guest_iduser`),
  KEY `idtransaction_idx` (`idtransaction`)
) ENGINE=InnoDB AUTO_INCREMENT=6039 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participants2011`
--

DROP TABLE IF EXISTS `participants2011`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participants2011` (
  `ID` int(4) DEFAULT NULL,
  `FirstName` varchar(28) DEFAULT NULL,
  `LastName` varchar(27) DEFAULT NULL,
  `Email` varchar(45) DEFAULT NULL,
  `TeamNum` int(3) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participants2012`
--

DROP TABLE IF EXISTS `participants2012`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participants2012` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `registered_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registration_level` int(5) DEFAULT '0',
  `idteam` int(11) DEFAULT '0',
  `team_type` varchar(50) NOT NULL,
  `fb_user_id` bigint(20) unsigned DEFAULT '0',
  `address1` text,
  `address2` text,
  `zipcode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=17024 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participants2013`
--

DROP TABLE IF EXISTS `participants2013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participants2013` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `registered_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registration_level` int(5) DEFAULT '0',
  `idteam` int(11) DEFAULT '0',
  `team_type` varchar(50) NOT NULL,
  `fb_user_id` bigint(20) unsigned DEFAULT '0',
  `address1` text,
  `address2` text,
  `zipcode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=27817 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `participants2014`
--

DROP TABLE IF EXISTS `participants2014`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `participants2014` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `email1` varchar(250) DEFAULT NULL,
  `email2` varchar(250) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `address1` text,
  `address2` text,
  `city` varchar(200) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `zipcode` varchar(50) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `dob` date DEFAULT '0000-00-00',
  `registered_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registration_level` float NOT NULL DEFAULT '0',
  `idteam` int(11) NOT NULL DEFAULT '0',
  `team_type` varchar(50) NOT NULL,
  `fb_user_id` bigint(20) unsigned DEFAULT '0',
  `gp_total` int(11) DEFAULT '0',
  `gp_manual_add` int(11) DEFAULT NULL,
  `gp_ghof` int(11) DEFAULT '0',
  `gp_fegvep2014` int(11) DEFAULT NULL,
  `gp_hunt2014` int(11) DEFAULT NULL,
  `gp_fegvep2013` int(11) DEFAULT NULL,
  `gp_hunt2013` int(11) DEFAULT NULL,
  `gp_hunt2012` int(11) DEFAULT NULL,
  `gp_hunt2011` int(11) DEFAULT NULL,
  `ecard` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`iduser`),
  KEY `username_idx` (`username`),
  KEY `idteam_idx` (`idteam`),
  KEY `team_type_idx` (`team_type`),
  KEY `email_idx` (`email`),
  KEY `last_name_idx` (`last_name`),
  KEY `city_idx` (`city`)
) ENGINE=MyISAM AUTO_INCREMENT=55467 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `password_recovery`
--

DROP TABLE IF EXISTS `password_recovery`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_recovery` (
  `idpassword_recovery` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `hashkey` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`idpassword_recovery`)
) ENGINE=InnoDB AUTO_INCREMENT=21164 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `paypal_transaction_log`
--

DROP TABLE IF EXISTS `paypal_transaction_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paypal_transaction_log` (
  `Transaction_Date` varchar(8) DEFAULT NULL,
  `Transaction_Time` varchar(8) DEFAULT NULL,
  `TimeZone` varchar(3) DEFAULT NULL,
  `Name` varchar(39) DEFAULT NULL,
  `Type` varchar(32) DEFAULT NULL,
  `Status` varchar(27) DEFAULT NULL,
  `Gross` varchar(9) DEFAULT NULL,
  `EmailAddress` varchar(45) DEFAULT NULL,
  `ToEmailAddress` varchar(34) DEFAULT NULL,
  `TransactionID` varchar(22) DEFAULT NULL,
  `ItemTitle` varchar(83) DEFAULT NULL,
  `CustomNumber` varchar(14) DEFAULT NULL,
  `Country` varchar(20) DEFAULT NULL,
  `NULL` varchar(13) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pledges`
--

DROP TABLE IF EXISTS `pledges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pledges` (
  `idpledge` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `referrer_email` varchar(50) DEFAULT NULL,
  `mailing_list` varchar(10) DEFAULT NULL,
  `act` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`idpledge`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `referral_registration`
--

DROP TABLE IF EXISTS `referral_registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referral_registration` (
  `idreferral_registration` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `referred_by` int(11) NOT NULL,
  PRIMARY KEY (`idreferral_registration`)
) ENGINE=MyISAM AUTO_INCREMENT=38706 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `refund_paid_invitation`
--

DROP TABLE IF EXISTS `refund_paid_invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refund_paid_invitation` (
  `idrefund_paid_invitation` int(10) NOT NULL AUTO_INCREMENT,
  `idtransaction` int(10) NOT NULL,
  `ref_tran_id` varchar(100) NOT NULL,
  `error_msg` varchar(200) NOT NULL,
  PRIMARY KEY (`idrefund_paid_invitation`)
) ENGINE=InnoDB AUTO_INCREMENT=359 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `refund_transaction`
--

DROP TABLE IF EXISTS `refund_transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `refund_transaction` (
  `idrefund_transaction` int(20) NOT NULL AUTO_INCREMENT,
  `idtransaction` int(20) NOT NULL,
  `iduser` int(20) NOT NULL,
  `date_added` datetime NOT NULL,
  `amount` float NOT NULL,
  `gateway` varchar(50) NOT NULL,
  `charge_id` varchar(100) NOT NULL,
  `refund_id` varchar(200) NOT NULL,
  PRIMARY KEY (`idrefund_transaction`)
) ENGINE=InnoDB AUTO_INCREMENT=265 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `registration_abandon`
--

DROP TABLE IF EXISTS `registration_abandon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `registration_abandon` (
  `idregistration_abandon` int(20) NOT NULL AUTO_INCREMENT,
  `iduser` int(30) NOT NULL,
  `email_status` enum('queue','ready','sent') NOT NULL DEFAULT 'queue',
  PRIMARY KEY (`idregistration_abandon`)
) ENGINE=MyISAM AUTO_INCREMENT=35418 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `review_completed_email`
--

DROP TABLE IF EXISTS `review_completed_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `review_completed_email` (
  `idreview_completed_email` int(50) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`idreview_completed_email`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=721 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reviewer`
--

DROP TABLE IF EXISTS `reviewer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviewer` (
  `idreviewer` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `emailid` varchar(256) NOT NULL,
  `status` enum('available','busy','inactive') NOT NULL DEFAULT 'available',
  `comments` text NOT NULL,
  `completed_count` int(10) NOT NULL DEFAULT '0',
  `completed_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idreviewer`),
  KEY `status_idx` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reviewer_2014`
--

DROP TABLE IF EXISTS `reviewer_2014`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviewer_2014` (
  `idreviewer` int(11) NOT NULL DEFAULT '0',
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `emailid` varchar(256) NOT NULL,
  `status` enum('available','busy','inactive') NOT NULL DEFAULT 'available',
  `comments` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reviewer_final_submissions`
--

DROP TABLE IF EXISTS `reviewer_final_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviewer_final_submissions` (
  `idreviewer_final_submissions` int(11) NOT NULL AUTO_INCREMENT,
  `idreviewer` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `link` varchar(256) NOT NULL,
  PRIMARY KEY (`idreviewer_final_submissions`),
  KEY `itemid_idx` (`itemid`),
  KEY `idreviewerf_idx` (`idreviewer`)
) ENGINE=InnoDB AUTO_INCREMENT=86549 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `second_level_item_review`
--

DROP TABLE IF EXISTS `second_level_item_review`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `second_level_item_review` (
  `idsecond_level_item_review` int(11) NOT NULL AUTO_INCREMENT,
  `hash_key` varchar(256) NOT NULL,
  `idreviewer` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `item_range_start` int(11) NOT NULL,
  `item_range_end` int(11) NOT NULL,
  PRIMARY KEY (`idsecond_level_item_review`),
  KEY `idreviewer_idx` (`idreviewer`),
  KEY `hash_key_idx` (`hash_key`),
  KEY `itemid_idx` (`itemid`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `second_level_item_review_details`
--

DROP TABLE IF EXISTS `second_level_item_review_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `second_level_item_review_details` (
  `idsecond_level_item_review_details` int(20) NOT NULL AUTO_INCREMENT,
  `idreviewer` int(20) NOT NULL,
  `itemid` int(20) NOT NULL,
  `idreviewer_final_submissions` int(20) NOT NULL,
  `flag` int(10) NOT NULL,
  `round` int(10) NOT NULL DEFAULT '0',
  `point` int(20) NOT NULL,
  PRIMARY KEY (`idsecond_level_item_review_details`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `second_level_reviewer_final_submissions`
--

DROP TABLE IF EXISTS `second_level_reviewer_final_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `second_level_reviewer_final_submissions` (
  `idsecond_level_reviewer_final_submissions` int(11) NOT NULL AUTO_INCREMENT,
  `idreviewer` int(11) NOT NULL,
  `itemid` int(11) NOT NULL,
  `link` varchar(256) NOT NULL,
  PRIMARY KEY (`idsecond_level_reviewer_final_submissions`),
  KEY `itemid_idx` (`itemid`),
  KEY `idreviewerf_idx` (`idreviewer`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `shopemails`
--

DROP TABLE IF EXISTS `shopemails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopemails` (
  `idshopemails` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idshopemails`)
) ENGINE=InnoDB AUTO_INCREMENT=4276 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `site_registration`
--

DROP TABLE IF EXISTS `site_registration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `site_registration` (
  `idsite_registration` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(250) DEFAULT NULL,
  `registered_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idsite_registration`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `idstates` int(11) NOT NULL AUTO_INCREMENT,
  `state_code` varchar(3) DEFAULT NULL,
  `state_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idstates`)
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submissions2011`
--

DROP TABLE IF EXISTS `submissions2011`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submissions2011` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `TeamNum` int(11) NOT NULL,
  `ItemNum` int(11) NOT NULL,
  `Link` varchar(300) NOT NULL,
  `Points` int(11) NOT NULL,
  `Tag` varchar(20) NOT NULL,
  `Votes` int(9) NOT NULL DEFAULT '0',
  `lulz` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=41077 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submissions2012`
--

DROP TABLE IF EXISTS `submissions2012`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submissions2012` (
  `iditem_list_points` int(11) NOT NULL AUTO_INCREMENT,
  `idteam` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `link` text,
  `link_type` varchar(15) DEFAULT NULL,
  `submitted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iditem` int(11) NOT NULL DEFAULT '0',
  `coffee` int(11) DEFAULT '0',
  `awesome` int(11) DEFAULT '0',
  PRIMARY KEY (`iditem_list_points`)
) ENGINE=InnoDB AUTO_INCREMENT=49486 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submissions2013`
--

DROP TABLE IF EXISTS `submissions2013`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submissions2013` (
  `iditem_list_points` int(11) NOT NULL AUTO_INCREMENT,
  `idteam` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `link` text,
  `link_type` varchar(15) DEFAULT NULL,
  `submitted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iditem` int(11) NOT NULL DEFAULT '0',
  `coffee` int(11) DEFAULT '0',
  `awesome` int(11) DEFAULT '0',
  `comment_on_item` text CHARACTER SET utf8 COLLATE utf8_general_mysql500_ci,
  PRIMARY KEY (`iditem_list_points`)
) ENGINE=InnoDB AUTO_INCREMENT=91628 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submissions2014`
--

DROP TABLE IF EXISTS `submissions2014`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submissions2014` (
  `iditem_list_points` int(11) NOT NULL AUTO_INCREMENT,
  `idteam` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `link` text,
  `link_type` varchar(15) DEFAULT NULL,
  `submitted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iditem` int(11) NOT NULL DEFAULT '0',
  `coffee` int(11) DEFAULT '0',
  `awesome` int(11) DEFAULT '0',
  `comment_on_item` text CHARACTER SET utf8 COLLATE utf8_general_mysql500_ci,
  PRIMARY KEY (`iditem_list_points`),
  KEY `idteam_idx` (`idteam`),
  KEY `iditem_idx` (`iditem`)
) ENGINE=InnoDB AUTO_INCREMENT=128208 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `submissions2015`
--

DROP TABLE IF EXISTS `submissions2015`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `submissions2015` (
  `iditem_list_points` int(11) NOT NULL AUTO_INCREMENT,
  `idteam` int(11) NOT NULL,
  `points` int(11) NOT NULL,
  `link` text,
  `link_type` varchar(15) DEFAULT NULL,
  `submitted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `iditem` int(11) NOT NULL DEFAULT '0',
  `coffee` int(11) DEFAULT '0',
  `awesome` int(11) DEFAULT '0',
  `comment_on_item` text CHARACTER SET utf8 COLLATE utf8_general_mysql500_ci,
  PRIMARY KEY (`iditem_list_points`),
  KEY `idteam_idx` (`idteam`),
  KEY `iditem_idx` (`iditem`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscribe_email`
--

DROP TABLE IF EXISTS `subscribe_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscribe_email` (
  `idsubscribe_email` int(50) NOT NULL AUTO_INCREMENT,
  `subscriber_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`idsubscribe_email`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=153516 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `idteam` int(11) NOT NULL AUTO_INCREMENT,
  `team_name` varchar(255) NOT NULL,
  `iduser` int(11) NOT NULL,
  `points` int(11) NOT NULL DEFAULT '0',
  `gishpoints` int(11) NOT NULL DEFAULT '0',
  `bonuspoints` int(11) NOT NULL DEFAULT '0',
  `gishpoints1` int(11) NOT NULL DEFAULT '0',
  `bonuspoints1` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idteam`),
  KEY `team_name_idx` (`team_name`),
  KEY `iduser_idx` (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=8271 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `test`
--

DROP TABLE IF EXISTS `test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `test` (
  `id` int(19) NOT NULL AUTO_INCREMENT,
  `desc` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonials` (
  `idtestimonials` int(10) NOT NULL AUTO_INCREMENT,
  `testimonial_data` text NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '',
  `quote` text,
  PRIMARY KEY (`idtestimonials`)
) ENGINE=MyISAM AUTO_INCREMENT=1307 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction` (
  `idtransaction` int(50) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `amount` float NOT NULL,
  `gateway` varchar(100) NOT NULL,
  `charge_id` varchar(100) NOT NULL,
  PRIMARY KEY (`idtransaction`),
  KEY `iduser_idx` (`iduser`),
  KEY `date_added_idx` (`date_added`)
) ENGINE=MyISAM AUTO_INCREMENT=38868 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `twitter_account`
--

DROP TABLE IF EXISTS `twitter_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `twitter_account` (
  `idtwitter_account` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(15) NOT NULL,
  `tw_user_id` varchar(20) DEFAULT NULL,
  `tw_screen_name` varchar(100) DEFAULT NULL,
  `tw_token` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`idtwitter_account`),
  UNIQUE KEY `idtwitter_account_2` (`idtwitter_account`),
  KEY `idtwitter_account` (`idtwitter_account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unpaid_invitation`
--

DROP TABLE IF EXISTS `unpaid_invitation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unpaid_invitation` (
  `idunpaid_invitation` int(12) NOT NULL AUTO_INCREMENT,
  `invite_by_iduser` int(12) NOT NULL COMMENT 'ID user',
  `invite_email` varchar(50) NOT NULL COMMENT 'Guest email',
  `invite_iduser` int(11) DEFAULT NULL,
  PRIMARY KEY (`idunpaid_invitation`)
) ENGINE=InnoDB AUTO_INCREMENT=40483 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `iduser` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL,
  `email1` varchar(250) DEFAULT NULL,
  `email2` varchar(250) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `address1` text,
  `address2` text,
  `city` varchar(200) DEFAULT NULL,
  `state` varchar(200) DEFAULT NULL,
  `zipcode` varchar(50) DEFAULT NULL,
  `country` varchar(2) DEFAULT NULL,
  `dob` date DEFAULT '0000-00-00',
  `registered_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `registration_level` float NOT NULL DEFAULT '0',
  `idteam` int(11) NOT NULL DEFAULT '0',
  `team_type` varchar(50) NOT NULL,
  `fb_user_id` bigint(20) unsigned DEFAULT '0',
  `gp_total` int(11) DEFAULT '0',
  `gp_manual_add` int(11) DEFAULT NULL,
  `gp_misc` int(11) DEFAULT '0',
  `gp_ghof` int(11) DEFAULT '0',
  `gp_fegvep2015` int(11) DEFAULT NULL,
  `gp_hunt2015` int(11) DEFAULT NULL,
  `gp_fegvep2014` int(11) DEFAULT NULL,
  `gp_hunt2014` int(11) DEFAULT NULL,
  `gp_fegvep2013` int(11) DEFAULT NULL,
  `gp_hunt2013` int(11) DEFAULT NULL,
  `gp_hunt2012` int(11) DEFAULT NULL,
  `gp_hunt2011` int(11) DEFAULT NULL,
  `ecard` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`iduser`),
  KEY `username_idx` (`username`),
  KEY `idteam_idx` (`idteam`),
  KEY `team_type_idx` (`team_type`),
  KEY `email_idx` (`email`),
  KEY `last_name_idx` (`last_name`),
  KEY `city_idx` (`city`),
  KEY `registration_level_idx` (`registration_level`)
) ENGINE=MyISAM AUTO_INCREMENT=99572 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_registraion_level_details`
--

DROP TABLE IF EXISTS `user_registraion_level_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_registraion_level_details` (
  `iduser_regisration_level_details` int(50) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) DEFAULT NULL,
  `selected_option` varchar(100) NOT NULL,
  `custom_message` text NOT NULL,
  `status` varchar(10) DEFAULT '',
  PRIMARY KEY (`iduser_regisration_level_details`),
  KEY `iduser_idx` (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=851 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-11-09 15:40:23
