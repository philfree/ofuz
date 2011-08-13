-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: ofuzos
-- ------------------------------------------------------
-- Server version	5.1.49-2-log

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
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity` (
  `idactivity` int(10) NOT NULL AUTO_INCREMENT,
  `idcontact` int(10) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`idactivity`),
  KEY `idcontact` (`idcontact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity`
--

LOCK TABLES `activity` WRITE;
/*!40000 ALTER TABLE `activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autoresponder`
--

DROP TABLE IF EXISTS `autoresponder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `autoresponder` (
  `idautoresponder` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` varchar(15) NOT NULL,
  `name` varchar(200) NOT NULL,
  `tag_name` varchar(200) NOT NULL,
  PRIMARY KEY (`idautoresponder`),
  UNIQUE KEY `idautoresponder_2` (`idautoresponder`),
  KEY `idautoresponder` (`idautoresponder`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autoresponder`
--

LOCK TABLES `autoresponder` WRITE;
/*!40000 ALTER TABLE `autoresponder` DISABLE KEYS */;
/*!40000 ALTER TABLE `autoresponder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `autoresponder_email`
--

DROP TABLE IF EXISTS `autoresponder_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `autoresponder_email` (
  `idautoresponder_email` int(10) NOT NULL AUTO_INCREMENT,
  `idautoresponder` int(15) NOT NULL,
  `bodyhtml` text NOT NULL,
  `language` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `bodytext` text NOT NULL,
  `senderemail` varchar(200) NOT NULL,
  `sendername` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `num_days_to_send` int(4) NOT NULL,
  PRIMARY KEY (`idautoresponder_email`),
  UNIQUE KEY `idautoresponder_email_2` (`idautoresponder_email`),
  KEY `idautoresponder_email` (`idautoresponder_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `autoresponder_email`
--

LOCK TABLES `autoresponder_email` WRITE;
/*!40000 ALTER TABLE `autoresponder_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `autoresponder_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `breadcrumb`
--

DROP TABLE IF EXISTS `breadcrumb`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `breadcrumb` (
  `idbreadcrumb` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `type` varchar(40) NOT NULL,
  `when` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id` int(10) NOT NULL,
  PRIMARY KEY (`idbreadcrumb`),
  KEY `iduser` (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `breadcrumb`
--

LOCK TABLES `breadcrumb` WRITE;
/*!40000 ALTER TABLE `breadcrumb` DISABLE KEYS */;
/*!40000 ALTER TABLE `breadcrumb` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company`
--

DROP TABLE IF EXISTS `company`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company` (
  `idcompany` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(70) NOT NULL,
  `iduser` int(15) NOT NULL,
  PRIMARY KEY (`idcompany`),
  UNIQUE KEY `idcompany_2` (`idcompany`),
  KEY `idcompany` (`idcompany`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company`
--

LOCK TABLES `company` WRITE;
/*!40000 ALTER TABLE `company` DISABLE KEYS */;
/*!40000 ALTER TABLE `company` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_address`
--

DROP TABLE IF EXISTS `company_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_address` (
  `idcompany_address` int(10) NOT NULL AUTO_INCREMENT,
  `street` text NOT NULL,
  `city` varchar(70) NOT NULL,
  `state` varchar(50) NOT NULL,
  `zipcode` varchar(20) NOT NULL,
  `country` varchar(60) NOT NULL,
  `idcompany` int(15) NOT NULL,
  `address` text NOT NULL,
  `address_type` varchar(10) CHARACTER SET ucs2 NOT NULL,
  PRIMARY KEY (`idcompany_address`),
  UNIQUE KEY `idcompany_address_2` (`idcompany_address`),
  KEY `idcompany_address` (`idcompany_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_address`
--

LOCK TABLES `company_address` WRITE;
/*!40000 ALTER TABLE `company_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_email`
--

DROP TABLE IF EXISTS `company_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_email` (
  `idcompany_email` int(10) NOT NULL AUTO_INCREMENT,
  `idcompany` varchar(20) NOT NULL,
  `email_address` varchar(150) NOT NULL,
  `email_type` varchar(20) NOT NULL,
  `email_isdefault` int(4) NOT NULL,
  PRIMARY KEY (`idcompany_email`),
  UNIQUE KEY `idcompany_email_2` (`idcompany_email`),
  KEY `idcompany_email` (`idcompany_email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_email`
--

LOCK TABLES `company_email` WRITE;
/*!40000 ALTER TABLE `company_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_phone`
--

DROP TABLE IF EXISTS `company_phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_phone` (
  `idcompany_phone` int(10) NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(30) NOT NULL,
  `phone_type` varchar(20) NOT NULL,
  `idcompany` varchar(20) NOT NULL,
  PRIMARY KEY (`idcompany_phone`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_phone`
--

LOCK TABLES `company_phone` WRITE;
/*!40000 ALTER TABLE `company_phone` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_phone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `company_website`
--

DROP TABLE IF EXISTS `company_website`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `company_website` (
  `idcompany_website` int(10) NOT NULL AUTO_INCREMENT,
  `idcompany` varchar(15) NOT NULL,
  `website` varchar(200) NOT NULL,
  `website_type` varchar(100) NOT NULL,
  PRIMARY KEY (`idcompany_website`),
  UNIQUE KEY `idcompany_website_2` (`idcompany_website`),
  KEY `idcompany_website` (`idcompany_website`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `company_website`
--

LOCK TABLES `company_website` WRITE;
/*!40000 ALTER TABLE `company_website` DISABLE KEYS */;
/*!40000 ALTER TABLE `company_website` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact`
--

DROP TABLE IF EXISTS `contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact` (
  `idcontact` int(10) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(60) NOT NULL,
  `position` varchar(60) NOT NULL,
  `company` varchar(70) NOT NULL,
  `idcompany` int(10) NOT NULL,
  `iduser` int(15) NOT NULL,
  `picture` varchar(200) NOT NULL,
  `summary` varchar(254) NOT NULL,
  `birthday` date DEFAULT NULL,
  `portal_code` varchar(50) NOT NULL,
  `fb_userid` int(14) NOT NULL DEFAULT '0',
  `tw_user_id` varchar(20) DEFAULT NULL,
  `email_optout` varchar(1) NOT NULL,
  PRIMARY KEY (`idcontact`),
  KEY `useridx` (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact`
--

LOCK TABLES `contact` WRITE;
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_address`
--

DROP TABLE IF EXISTS `contact_address`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_address` (
  `idcontact_address` int(10) NOT NULL AUTO_INCREMENT,
  `city` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `street` mediumtext NOT NULL,
  `zipcode` varchar(20) NOT NULL,
  `idcontact` int(15) NOT NULL,
  `address` mediumtext NOT NULL,
  `address_type` varchar(10) NOT NULL,
  PRIMARY KEY (`idcontact_address`),
  UNIQUE KEY `idcontact_address_2` (`idcontact_address`),
  KEY `idcontact_address` (`idcontact_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_address`
--

LOCK TABLES `contact_address` WRITE;
/*!40000 ALTER TABLE `contact_address` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_address` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_email`
--

DROP TABLE IF EXISTS `contact_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_email` (
  `idcontact_email` int(10) NOT NULL AUTO_INCREMENT,
  `idcontact` int(10) NOT NULL,
  `email_address` varchar(180) NOT NULL,
  `email_type` varchar(50) NOT NULL,
  `email_isdefault` char(1) NOT NULL DEFAULT 'n',
  PRIMARY KEY (`idcontact_email`),
  KEY `idcontact` (`idcontact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_email`
--

LOCK TABLES `contact_email` WRITE;
/*!40000 ALTER TABLE `contact_email` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_email` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_instant_message`
--

DROP TABLE IF EXISTS `contact_instant_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_instant_message` (
  `idcontact_instant_message` int(10) NOT NULL AUTO_INCREMENT,
  `idcontact` int(14) NOT NULL,
  `im_options` varchar(20) NOT NULL,
  `im_type` varchar(50) NOT NULL,
  `im_username` varchar(100) NOT NULL,
  PRIMARY KEY (`idcontact_instant_message`),
  UNIQUE KEY `idcontact_instant_message_2` (`idcontact_instant_message`),
  KEY `idcontact_instant_message` (`idcontact_instant_message`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_instant_message`
--

LOCK TABLES `contact_instant_message` WRITE;
/*!40000 ALTER TABLE `contact_instant_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_instant_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_note`
--

DROP TABLE IF EXISTS `contact_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_note` (
  `idcontact_note` int(10) NOT NULL AUTO_INCREMENT,
  `idcontact` int(15) NOT NULL DEFAULT '0',
  `note` mediumtext NOT NULL,
  `date_added` date NOT NULL,
  `document` varchar(254) NOT NULL,
  `idcompany` int(15) NOT NULL DEFAULT '0',
  `iduser` varchar(15) NOT NULL,
  `priority` int(1) NOT NULL DEFAULT '0',
  `send_email` varchar(1) NOT NULL,
  `hours_work` float(10,2) NOT NULL DEFAULT '0.00',
  `note_visibility` varchar(50) NOT NULL,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`idcontact_note`),
  UNIQUE KEY `idcontact_note_2` (`idcontact_note`),
  KEY `idcontact_note` (`idcontact_note`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_note`
--

LOCK TABLES `contact_note` WRITE;
/*!40000 ALTER TABLE `contact_note` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_phone`
--

DROP TABLE IF EXISTS `contact_phone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_phone` (
  `idcontact_phone` int(10) NOT NULL AUTO_INCREMENT,
  `phone_number` varchar(30) NOT NULL,
  `phone_type` varchar(20) NOT NULL,
  `idcontact` varchar(20) NOT NULL,
  PRIMARY KEY (`idcontact_phone`),
  KEY `idcontact` (`idcontact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_phone`
--

LOCK TABLES `contact_phone` WRITE;
/*!40000 ALTER TABLE `contact_phone` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_phone` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_portal_message`
--

DROP TABLE IF EXISTS `contact_portal_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_portal_message` (
  `idcontact_portal_message` int(10) NOT NULL AUTO_INCREMENT,
  `idcontact` int(15) NOT NULL,
  `iduser` int(15) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`idcontact_portal_message`),
  UNIQUE KEY `idcontact_portal_message_2` (`idcontact_portal_message`),
  KEY `idcontact_portal_message` (`idcontact_portal_message`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_portal_message`
--

LOCK TABLES `contact_portal_message` WRITE;
/*!40000 ALTER TABLE `contact_portal_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_portal_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_rss_feed`
--

DROP TABLE IF EXISTS `contact_rss_feed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_rss_feed` (
  `idcontact_rss_feed` int(10) NOT NULL AUTO_INCREMENT,
  `idcontact` varchar(15) NOT NULL,
  `rss_feed_url` varchar(200) NOT NULL,
  `username` varchar(100) NOT NULL,
  `feed_type` varchar(100) NOT NULL,
  `import_to_note` varchar(10) NOT NULL,
  PRIMARY KEY (`idcontact_rss_feed`),
  UNIQUE KEY `idcontact_rss_feed_2` (`idcontact_rss_feed`),
  KEY `idcontact_rss_feed` (`idcontact_rss_feed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_rss_feed`
--

LOCK TABLES `contact_rss_feed` WRITE;
/*!40000 ALTER TABLE `contact_rss_feed` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_rss_feed` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_sharing`
--

DROP TABLE IF EXISTS `contact_sharing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_sharing` (
  `idcontact_sharing` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(14) NOT NULL,
  `idcontact` int(14) NOT NULL,
  `idcoworker` int(14) NOT NULL,
  PRIMARY KEY (`idcontact_sharing`),
  UNIQUE KEY `idcontact_sharing_2` (`idcontact_sharing`),
  KEY `idcontact_sharing` (`idcontact_sharing`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_sharing`
--

LOCK TABLES `contact_sharing` WRITE;
/*!40000 ALTER TABLE `contact_sharing` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_sharing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_website`
--

DROP TABLE IF EXISTS `contact_website`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_website` (
  `idcontact_website` int(10) NOT NULL AUTO_INCREMENT,
  `website` varchar(100) NOT NULL,
  `website_type` varchar(50) NOT NULL,
  `idcontact` varchar(15) NOT NULL,
  `feed_last_fetch` bigint(20) unsigned NOT NULL,
  `feed_auto_fetch` varchar(5) CHARACTER SET ucs2 NOT NULL DEFAULT 'None',
  PRIMARY KEY (`idcontact_website`),
  UNIQUE KEY `idcontact_website_2` (`idcontact_website`),
  KEY `idcontact_website` (`idcontact_website`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_website`
--

LOCK TABLES `contact_website` WRITE;
/*!40000 ALTER TABLE `contact_website` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_website` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `countries` (
  `idcountries` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`idcountries`)
) ENGINE=MyISAM AUTO_INCREMENT=253 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES (1,'Afghanistan'),(2,'Albania'),(3,'Algeria'),(4,'American Samoa'),(5,'Andorra'),(6,'Angola'),(7,'Anguilla'),(8,'Antarctica'),(9,'Antigua and Barbuda'),(10,'Argentina'),(11,'Armenia'),(12,'Aruba'),(13,'Australia'),(14,'Austria'),(15,'Azerbaijan'),(16,'Bahamas'),(17,'Bahrain'),(18,'Bangladesh'),(19,'Barbados'),(20,'Belarus'),(21,'Belgium'),(22,'Belize'),(23,'Benin'),(24,'Bermuda'),(25,'Bhutan'),(26,'Bolivia'),(27,'Bosnia and Herzegovina'),(28,'Botswana'),(29,'Bouvet Island'),(30,'Brazil'),(31,'British Indian Ocean Territory'),(32,'Brunei Darussalam'),(33,'Bulgaria'),(34,'Burkina Faso'),(35,'Burma'),(36,'Burundi'),(37,'Cambodia'),(38,'Cameroon'),(39,'Canada'),(40,'Canton and Enderbury Islands'),(41,'Cape Verde'),(42,'Cayman Islands'),(43,'Central African Republic'),(44,'Chad'),(45,'Chile'),(46,'China'),(47,'Christmas Island'),(48,'Cocos (Keeling Islands)'),(49,'Colombia'),(50,'Comoros'),(51,'Congo'),(52,'Cook Islands'),(53,'Costa Rica'),(54,'Cote D`Ivoire'),(55,'Croatia (Hrvatska)'),(56,'Cuba'),(57,'Cyprus'),(58,'Czech Republic'),(59,'Democratic Yemen'),(60,'Denmark'),(61,'Djibouti'),(62,'Dominica'),(63,'Dominican Republic'),(64,'Dronning Maud Land'),(65,'East Timor'),(66,'Ecuador'),(67,'Egypt'),(68,'El Salvador'),(69,'Equatorial Guinea'),(70,'Eritrea'),(71,'Estonia'),(72,'Ethiopia'),(73,'Falkland Islands (Malvinas)'),(74,'Faroe Islands'),(75,'Fiji'),(76,'Finland'),(77,'France'),(78,'France, Metropolitan'),(79,'French Guiana'),(80,'French Polynesia'),(81,'French Southern Territories'),(82,'Gabon'),(83,'Gambia'),(84,'Georgia'),(85,'Germany'),(86,'Ghana'),(87,'Gibraltar'),(88,'Greece'),(89,'Greenland'),(90,'Grenada'),(91,'Guadeloupe'),(92,'Guam'),(93,'Guatemala'),(94,'Guinea'),(95,'Guinea-bisseu'),(96,'Guyana'),(97,'Haiti'),(98,'Heard and Mc Donald Islands'),(99,'Honduras'),(100,'Hong Kong'),(101,'Hungary'),(102,'Iceland'),(103,'India'),(104,'Indonesia'),(105,'Iran (Islamic Republic of)'),(106,'Iraq'),(107,'Ireland'),(108,'Israel'),(109,'Italy'),(110,'Ivory Coast'),(111,'Jamaica'),(112,'Japan'),(113,'Johnston Island'),(114,'Jordan'),(115,'Kazakstan'),(116,'Kenya'),(117,'Kiribati'),(118,'Korea, Democratic People`s Republic of'),(119,'Korea, Republic of'),(120,'Kuwait'),(121,'Kyrgyzstan');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `created_date_log`
--

DROP TABLE IF EXISTS `created_date_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `created_date_log` (
  `idcreated_date_log` int(10) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  `id` int(15) NOT NULL,
  `created_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idcreated_date_log`),
  KEY `tableid` (`table_name`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `created_date_log`
--

LOCK TABLES `created_date_log` WRITE;
/*!40000 ALTER TABLE `created_date_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `created_date_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delete_paymentlog`
--

DROP TABLE IF EXISTS `delete_paymentlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delete_paymentlog` (
  `iddelete_paymentlog` int(10) NOT NULL AUTO_INCREMENT,
  `timestamp` varchar(200) NOT NULL,
  `idinvoice` int(15) NOT NULL,
  `amount` float(14,2) NOT NULL,
  `payment_type` varchar(200) NOT NULL,
  `ref_num` varchar(200) NOT NULL,
  PRIMARY KEY (`iddelete_paymentlog`),
  UNIQUE KEY `iddelete_paymentlog_2` (`iddelete_paymentlog`),
  KEY `iddelete_paymentlog` (`iddelete_paymentlog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delete_paymentlog`
--

LOCK TABLES `delete_paymentlog` WRITE;
/*!40000 ALTER TABLE `delete_paymentlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `delete_paymentlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `discussion_email_setting`
--

DROP TABLE IF EXISTS `discussion_email_setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discussion_email_setting` (
  `iddiscussion_email_setting` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(14) NOT NULL,
  `id` int(14) NOT NULL,
  `discussion_email_alert` varchar(5) NOT NULL,
  `setting_level` varchar(100) NOT NULL,
  PRIMARY KEY (`iddiscussion_email_setting`),
  UNIQUE KEY `iddiscussion_email_setting_2` (`iddiscussion_email_setting`),
  KEY `iddiscussion_email_setting` (`iddiscussion_email_setting`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discussion_email_setting`
--

LOCK TABLES `discussion_email_setting` WRITE;
/*!40000 ALTER TABLE `discussion_email_setting` DISABLE KEYS */;
/*!40000 ALTER TABLE `discussion_email_setting` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emailtemplate`
--

DROP TABLE IF EXISTS `emailtemplate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailtemplate` (
  `idemailtemplate` int(10) NOT NULL AUTO_INCREMENT,
  `subject` varchar(150) NOT NULL DEFAULT '',
  `bodytext` text NOT NULL,
  `bodyhtml` text NOT NULL,
  `name` varchar(254) NOT NULL DEFAULT '',
  `sendername` varchar(254) NOT NULL DEFAULT '',
  `senderemail` varchar(254) NOT NULL DEFAULT '',
  `thumbnail` varchar(70) NOT NULL DEFAULT '',
  `internal` varchar(10) NOT NULL DEFAULT '',
  `language` varchar(30) NOT NULL,
  PRIMARY KEY (`idemailtemplate`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emailtemplate`
--

LOCK TABLES `emailtemplate` WRITE;
/*!40000 ALTER TABLE `emailtemplate` DISABLE KEYS */;
INSERT INTO `emailtemplate` VALUES (1,'Welcome to Ofuz','[firstname],\r\nI want to welcome you to Ofuz.\r\n\r\nYou account is now active and ready to use, you can sign in at:\r\nhttp://www.ofuz.net/user_login.php\r\n\r\nCheck our blog for news and update on new features tutorials and howtos for your business:\r\nhttp://www.ofuz.net/blog/\r\n\r\nOfuz is still in beta mode so we need your feed back on bugs, problems or any suggestions you may have.\r\n\r\nThank you for using Ofuz\r\n\r\nPhilippe\r\nProduct Manager\r\n310 765 4632 x81\r\n','[firstname],<br>\r\nI want to welcome you to Ofuz.\r\n<br><br>\r\nYou account is now active and ready to use, you can sign in at:<br>\r\n<a href=\"http://www.ofuz.net/user_login.php\">http://www.ofuz.net/user_login.php</a>\r\n<br><br>\r\nCheck our blog for news and update on new features tutorials and howtos for your business:<br>\r\n<a href=\"http://www.ofuz.net/blog/\">http://www.ofuz.net/blog/</a>\r\n<br><br>\r\nOfuz is still in beta mode so we need your feed back on bugs, problems or any suggestions you may have.\r\n<br><br>\r\nThank you for using Ofuz\r\n<br><br>\r\nPhilippe<br>\r\nProduct Manager<br>\r\n310 765 4632 x81\r\n','regthank','Philippe Lewicki','support@sqlfusion.com','','','en_US'),(2,'Your Password','Dear [firstname],\r\nHere is your password:\r\n[password]\r\n\r\nThe username is [username]\r\n\r\nOfuz Support\r\nhttp://ofuz.net','','forgotpassword','Server','support@sqlfusion.com','','','en_US'),(5,'You have a new member','Sir,\r\nA new member registered on your web site.\r\n\r\nName: [firstname] [lastname]\r\n\r\nRegistration server.','','admin_registration_alert','Server','support@sqlfusion.com','','',''),(6,'{Ofuz} New comment on [task_category]:[task_name] ','[firstname]\r\nOn project [project-name], [discussion-owner] added a message to the task [task_category]:[task_name] discussion:\r\n\r\n[message_txt]\r\n\r\nTo follow the discussion click on the link below:\r\n[project-task-link]\r\n\r\nOfuz.net\r\nWork your network\r\nhttp://ofuz.net/','[firstname]<br>\r\nOn project <a href=\"[project-link]\">[project-name]</a>, [discussion-owner] added a message to the task <i>[task_category]</i>:<b>[task_name]</b>discussion:<br/>\r\n<br/>\r\n[message_html]\r\n<br/><br/>\r\nTo follow the discussion <a href=\"[project-task-link]\">click here</a> or on the link below:<br/>\r\n[project-task-link]<br/>\r\n<br/>\r\nOfuz.net<br/>\r\nWork your network<br/>\r\n<a href=\"http://ofuz.net/?o=amail\">http://ofuz.net</a>','ofuz_project_discussion','Ofuz.net','support@sqlfusion.com','','','en_US'),(7,'Ofuz co-worker Invitation','Hi,\r\n[firstname] has sent you a co-worker invitation to share contacts or projects.\r\n\r\nPlease follow the following link to register and accept [firstname] co-worker invitation:\r\n\r\nhttp://www.ofuz.net/user_register.php?id=[referer]\r\n','Hi,<br/>\r\n<b>[firstname]</b> has sent you a co-worker invitation to share contacts or projects.<br/>\r\n<br/>\r\nPlease <a href=\"http://www.ofuz.net/user_register.php?key=[enc_email]&id=[referer]\">click here</a> or follow the following link to register and accept [firstname] co-worker invitation:\r\n<br/>\r\nhttp://www.ofuz.net/user_register.php?id=[referer]','invitation','','','','','en_US'),(8,'{Ofuz} New Task: [task-name]','[firstname],\r\n\r\nIn project [project-name] [task-owner] added a new task: \r\n[task-category]: [task-name]\r\n\r\nTo view or discuss the task click the link below:\r\n[project-task-link]\r\n\r\nTo view all the tasks on that project click the link below:\r\n[project-link]\r\n\r\nOfuz.net\r\nWork your network\r\nhttp://ofuz.net/','[firstname],\r\n<br/>\r\nIn project <a href=\"[project-link]\">[project-name]</a> <i>[task-owner]</i> added a new task:<br/> \r\n<i>[task-category]</i>:<b>[task-name]</b>\r\n<br/>\r\nTo view or discuss the task <a href=\"[project-task-link]\">click here</a> or the link below:<br/>\r\n[project-task-link]<br/>\r\n<br/>\r\nOfuz.net<br/>\r\nWork your network<br/>\r\nhttp://ofuz.net/<br/>','ofuz_new_project_task','Ofuz.net','support@sqlfusion.com','','','en_US'),(9,'New contact from webform: [webformname]','[firstname],\r\n\r\nYou have a new contact that filled in the form [webformname].\r\nClick the link below to view this contact:\r\n[contact_url]\r\n\r\nHe entered the following values:\r\n[fields_content_text]\r\n\r\nOfuz alert.\r\nTo stop receiving this alert, turn it of from your web form at:\r\nhttp://www.ofuz.net/','[firstname],<br/>\r\n<br/>\r\nYou have a new contact that filled in the form [webformname].\r\n<a href=\"[contact_url]\">Click here</a> to view this new contact.\r\n<br/>\r\n\r\nHe entered the following values:<br/>\r\n[fields_content_html]\r\n\r\n<br/>\r\n<br/>\r\n<br/>\r\nOfuz web form alert.<br/>\r\nTo stop receiving this alert, turn it of from your web form at:<br/>\r\n<a href=\"http://www.ofuz.net\">ofuz.net</a><br/>','web form email alert','Ofuz','support@sqlfusion.com','','','en_US'),(10,'{Ofuz} Note from your contact portal','[note_text]\r\n\r\nAttachment:\r\n[doc_name]\r\n[doc_link]\r\n\r\nMessage sent by [contact_name] using Ofuz share note & file features. \r\nClick the link below to see all notes & files:\r\n[contact_link]\r\n\r\n','[note_html]\r\n<br/>\r\nAttachment: <a href=\"[doc_link]\">[doc_name]</a><br/>\r\n<br/>\r\n<br/>\r\nMessage sent by <a href=\"[contact_link]\">[contact_name]</a> using Ofuz share note & file features. <br/>\r\n<a href=\"[contact_link]\">Click here</a> to see all notes & files.</a>\r\n\r\n\r\n','ofuz portal alert','Ofuz','support@sqlfusion.com','','','en_US'),(11,'Ofuz new co-worker invitation','Dear [coworker-name],\r\n[sender-name] would like to add you as a co-worker.\r\nTo accept the invitation please click on the link below:\r\n[coworker_url]\r\n\r\n\r\nOfuz.net\r\nWork your network\r\nhttp://ofuz.net/','Dear [coworker-name],<br />\r\n[sender-name] would like to add you as a co-worker.\r\nTo accept the invitation please click on the link below:<br />\r\n<a href=\"[coworker_url]\">[coworker_url]</a>\r\n<br /><br />\r\n\r\nOfuz.net<br />\r\nWork your network<br />\r\n<a href=\"http://ofuz.net/?o=amail\">http://ofuz.net/</a>','ofuz_coworker_add_notification','Ofuz.net','support@sqlfusion.com','','','en_US'),(12,'Ofuz co-worker accept notification','Dear [name],\r\n[coworker_name] has accepted your invitation to be a co-worker.\r\n\r\n\r\nOfuz.net\r\nWork your network\r\nhttp://ofuz.net/','Dear [name],<br />\r\n[coworker_name] has accepted your invitation to be a co-worker.\r\n<br /><br />\r\n\r\nOfuz.net<br />\r\nWork your network<br />\r\n<a href=\"http://ofuz.net/?o=amail\">http://ofuz.net/</a>','ofuz_coworker_accept_invitation','Ofuz.net','support@sqlfusion.com','','','en_US'),(13,'Ofuz co-worker invitation reject notification','Dear [name],\r\n[coworker_name] has rejected your invitation to be a co-worker.\r\n\r\n\r\nOfuz.net\r\nWork your network\r\nhttp://ofuz.net/','Dear [name],<br />\r\n[coworker_name] has rejected your invitation to be a co-worker.\r\n<br /><br />\r\n\r\nOfuz.net<br />\r\nWork your network<br />\r\n<a href=\"http://ofuz.net/?o=amail\">http://ofuz.net/</a>','ofuz_coworker_reject_invitation','Ofuz.net','support@sqlfusion.com','','','en_US'),(14,'{Ofuz} Task:  [task-name] - Ownership is changed','[firstname],\r\n\r\nIn project [project-name] you have been assigned the ownership of the task : [task-name]\r\n\r\nTo view or discuss the task click the link below:\r\n[project-task-link]\r\n\r\nTo view all the tasks on that project click the link below:\r\n[project-link]\r\n\r\nOfuz.net\r\nWork your network\r\nhttp://ofuz.net/','[firstname],\r\n<br/>\r\nIn project <a href=\"[project-link]\">[project-name]</a> you have been assigned the ownership of the task : <b>[task-name]</b>\r\n<br/>\r\nTo view or discuss the task <a href=\"[project-task-link]\">click here</a> or the link below:<br/>\r\n[project-task-link]<br/>\r\n<br/>\r\nOfuz.net<br/>\r\nWork your network<br/>\r\nhttp://ofuz.net/<br/>','ofuz_task_ownership_change','Ofuz.net','support@sqlfusion.com','','','en_US'),(15,'invoice [num] from [company]','Dear [name],\r\n\r\nHere is the invoice [num] from [company] for \r\n[description]\r\n\r\nFor a total of [amount].\r\n\r\nTo review and pay your invoice access your invoice click on the address:\r\n[invoice_url]\r\n\r\nThank you for your business,\r\n\r\n[company]\r\n','Dear [name],<br />\r\n\r\nHere is the invoice [num] from <b>[company]</b> for \r\n[description]<br />\r\nwith a total amount of <b>[amount]</b>.<br />\r\n<br />\r\nTo review and pay your invoice access your invoice <a href=\"[invoice_url]\">click here</a>\r\n or use link below:<br />\r\n[invoice_url]\r\n<br /><br />\r\nThank you for your business,\r\n<br />\r\n[company]','ofuz_send_invoice','Ofuz.net','support@sqlfusion.com','','','en_US'),(16,'Invoice Payment confirmation','Dear [name],\r\n\r\nYou have paid [amount] for the Invoice #[num] towards [company]. The reference number for the transaction is [refnum] and the payment mode used [paytype]\r\n\r\n[description]\r\n\r\n[signature]','Dear [name],<br />\r\n\r\nYou have paid [amount] for the Invoice #[num] towards [company]. The reference number for the transaction is [refnum] and the payment mode used [paytype]\r\n<br />\r\n[description]\r\n<br /><br />\r\n[signature]','ofuz_inv_payment_confirmation','Ofuz.net','support@sqlfusion.com','','','en_US'),(17,'{Ofuz} I want to share files and notes with you','[firstname],\r\n\r\nHere is a web address on Ofuz for us to share documents:\r\n[contact_portal_url]\r\n\r\n[user_fullname]','[firstname],\r\n<br/><br/>\r\nHere is a web address on Ofuz for document sharing:<br/>\r\n<b><a href=\"[contact_portal_url]\">[contact_portal_url]</a></b><br/>\r\n<br/>\r\n[user_fullname]<br/>','contact share notes url','','','','','en_US'),(18,'{Ofuz:Nudge} on [task_category]:[task_name]','I just sent you a *nudge* on a discussion in the task  [task_category]:[task_name] from project [project-name]: \r\n[message_txt]\r\n \r\nTo respond click on the link below: \r\n[project-task-link] \r\n\r\nOfuz.net Work your network \r\nhttp://ofuz.net/','I just sent you a <b>nudge</b> on a discussion in the task <i>[task_category]</i>:<b>[task_name]</b> from project <a href=\"[project-link]\">[project-name]</a>: \r\n<br/> <br/> \r\n[message_html] \r\n<br/><br/> \r\nTo respond <a href=\"[project-task-link]\">click here</a> or on the link below:\r\n<br/> [project-task-link]\r\n<br/> \r\n<br/> Ofuz.net\r\n<br/> Work your network<br/> <a href=\"http://ofuz.net/?o=amail\">http://ofuz.net</a>','ofuz_project_discussion_nudge','','','','','en_US'),(19,'Past due Invoice [num]  for [invoice_short_description]','Dear [name],\r\nYou have a due amount [amt_due] for invoice number [num] for [invoice_description]:\r\n[invoice_url]\r\nfrom [sender_name] ,[company]\r\n   \r\nThank you very much for a prompt payment.\r\n   \r\n[sender_name]\r\n\r\n','Dear [name],<br />\r\nYou have a due amount [amt_due] for invoice number [num] for [invoice_description]:<br />\r\n<a href=\"[invoice_url]\">[invoice_url]</a>\r\n<br />\r\nfrom [sender_name] ,[company]\r\n<br />\r\nThank you very much for a prompt payment.\r\n<br />\r\n[sender_name]','ofuz_past_due_invoice_notification','Ofuz.net','support@sqlfusion.com','','','en_US'),(20,'invoice [num] from [company]','Dear [name],\r\n[description]\r\n\r\nThe next recurrent date for this invoice is [next_due_date]\r\n\r\nThe recurrent type is every [recurrence] [recurrence_type]\r\n\r\nTo access your invoice from [sender] ,[company] for [amount], go to: \r\n\r\n[invoice_url]','Dear [name],<br />\r\n[description]<br />\r\nThe next recurrent date for this invoice is [next_due_date]<br />\r\nThis invoice repeats every [recurrence] [recurrence_type]<br />\r\nTo access your invoice from [sender] ,[company] for [amount], go to: <br /><br />\r\n\r\n<a href=\"[invoice_url]\">[invoice_url]</a>','ofuz_send_recurrent_invoice','Ofuz.net','support@sqlfusion.com','','','en_US'),(21,'Invoice # [num] has been accepted by [contact]','The invoice # [num] \r\n[description]\r\n\r\nhas been accepted.\r\n\r\n\r\n[signature]','The invoice # [num] <br />\r\n[description]<br/>\r\nhas been accepted.\r\n<br /><br />\r\n\r\n[signature]','ofuz_inv_accept_confirmation_delete','Ofuz.net','support@sqlfusion.com','','','en_US'),(22,'Quote # [num] has been accepted by [contact]','The Quote # [num] \r\n\r\n[description]\r\n\r\nhas been accepted.\r\n[invoice_url]\r\n\r\n[signature]','The Quote # [num] <br />\r\n[description]<br/>\r\n<br/>\r\nhas been accepted.\r\n<br />\r\n[invoice_url]\r\n<br /><br />\r\n\r\n[signature]','ofuz_inv_accept_confirmation','Ofuz.net','support@sqlfusion.com','','','en_US'),(23,'unsubscribed from auto responder','Dear [firstname] [lastname],\r\nYou have unsubscribed from the autoresponder series called [responder].\r\nWork your network with Ofuz.net','Dear [firstname] [lastname],<br />\r\nYou have unsubscribed from the autoresponder series called [responder] .<br .>\r\nWork your network with Ofuz.net','unsubscribe_auto_responder','Ofuz','support@sqlfusion.com','','','en_US'),(24,'unsubscribed from auto responder','','Dear [firstname] [lastname],\r\nYou have unsubscribed from the autoresponder series called [responder] .\r\nWork your network with Ofuz.net','unsubscribe_auto_responder_delete','Ofuz','support@sqlfusion.com','','','en_US'),(25,'[company] Payment Confirmation for Invoice: [invoice_num]','Dear [username],\r\n\r\nAn amount of [amount] has been paid for Invoice #[num] by [name]. The reference number for the transaction is [refnum] and the payment mode used [paytype]\r\n\r\n[description]\r\n\r\n','Dear [username],<br />\r\n\r\nAn amount of [amount] has been paid for Invoice #[num] by [name]. The reference number for the transaction is [refnum] and the payment mode used [paytype]<br />\r\n\r\n[description]\r\n','ofuz_inv_payment_confirmation_adm','Ofuz.net','support@sqlfusion.com','','','en_US'),(26,'Bienvenue Ã  Ofuz','[firstname], je tiens Ã  vous souhaiter la\r\nbienvenue Ã  Ofuz. Votre compte est maintenant prÃªt\r\nÃ  utiliser, vous pouvez vous connecter Ã \r\nl\'adresse: http://www.ofuz.net/user_login.php\r\nConsultez notre blog pour les nouvelles et mise Ã \r\njour sur les nouvelles fonctionnalitÃ©s tutoriaux\r\net howtos pour votre entreprise:\r\nhttp://www.ofuz.net/blog/ Ofuz est toujours en\r\nmode beta donc nous avons besoin de votre\r\nfeed-back sur les bogues, des problÃ¨mes ou des\r\nsuggestions que vous pourriez avoir. Merci\r\nd\'utiliser Ofuz Philippe Chef de produit 310 765\r\n4632 x81','[firstname], <br> Je tiens Ã  vous souhaiter la\r\nbienvenue Ã  Ofuz. <br><br> Votre compte est\r\nmaintenant prÃªt Ã  utiliser, vous pouvez vous\r\nconnecter Ã  l\'adresse: <br> <a\\r\\nhref=\\','regthank','Philippe Lewicki','support@sqlfusion.com','','','fr_FR'),(27,'Quote [num] from [company]','Dear [name],\r\n\r\nHere is the quote [num] from [company] for \r\n[description]\r\n\r\nFor a total of [amount].\r\n\r\nPlease review and approve the same. To access your quote click on the address:\r\n[invoice_url]\r\n','Dear [name],<br />\r\n\r\nHere is the quote [num] from <b>[company]</b> for \r\n[description]<br />\r\nwith a total amount of <b>[amount]</b>.<br />\r\n<br />\r\n\r\nPlease review and approve the same.To access your quote <a href=\"[invoice_url]\">click here</a>\r\nor use link below:<br />\r\n[invoice_url]\r\n<br /><br />\r\nThank you for your business,\r\n<br />\r\n[company]','ofuz_send_quote','Ofuz.net','support@sqlfusion.com','','','en_US'),(28,'[coworker_name] has accepted your invitation','Dear [name],\r\n[coworker_name] has accepted your invitation and registered as a co-worker.\r\n\r\n\r\nOfuz.net\r\nWork your network\r\nhttp://ofuz.net/','Dear [name],<br />\r\n[coworker_name] has accepted your invitation and registered as a co-worker.\r\n\r\n<br /><br />\r\n\r\nOfuz.net<br />\r\nWork your network<br />\r\n<a href=\"http://ofuz.net/?o=amail\">http://ofuz.net/</a>','ofuz_coworker_registered_notification','Ofuz.net','support@sqlfusion.com','','','en_US'),(33,'à¤¸à¥à¤µà¤¾à¤—à¤¤ à¤•à¤°à¤¨à¥‡ à¤•à¥‡ à¤²à¤¿à¤ Ofuz','[firstname], à¤®à¥ˆà¤‚ Ofuz à¤šà¤¾à¤¹à¤¤à¥‡ à¤¹à¥ˆà¤‚ à¤•à¤¿ à¤†à¤ªà¤•à¥‡ à¤¸à¥à¤µà¤¾à¤—à¤¤ à¤•à¥‡ à¤²à¤¿à¤\r\nà¤Ÿà¥à¤¯à¥‚à¤Ÿà¥‹à¤°à¤¿à¤¯à¤². à¤†à¤ª à¤–à¤¾à¤¤à¤¾ à¤…à¤¬ à¤¸à¤•à¥à¤°à¤¿à¤¯ à¤¹à¥ˆ à¤”à¤° à¤¤à¥ˆà¤¯à¤¾à¤° à¤‰à¤ªà¤¯à¥‹à¤— à¤•à¤°à¤¨à¥‡ à¤•à¥‡\r\nà¤²à¤¿à¤, à¤®à¥‡à¤‚ à¤ªà¥à¤°à¤µà¥‡à¤¶ à¤•à¤° à¤¸à¤•à¤¤à¥‡ à¤¹à¥ˆà¤‚ à¤¤à¥à¤® à¤ªà¤°: à¤•à¥‡ à¤²à¤¿à¤ à¤¹à¤®à¤¾à¤°à¥‡ à¤¬à¥à¤²à¥‰à¤— à¤•à¥€ à¤œà¤¾à¤à¤š\r\nà¤•à¤°à¥‡à¤‚ http://www.ofuz.net/user_login.php à¤–à¤¬à¤° à¤¸à¥à¤µà¤¿à¤§à¤¾à¤“à¤‚\r\nà¤¨à¤ à¤”à¤° à¤…à¤¦à¥à¤¯à¤¤à¤¨ à¤ªà¤° à¤”à¤° à¤µà¥à¤¯à¤¾à¤ªà¤¾à¤° à¤…à¤ªà¤¨à¥‡ howtos à¤•à¥‡ à¤²à¤¿à¤:\r\nhttp://www.ofuz.net/blog/ Ofuz à¤®à¥‹à¤¡ à¤¬à¥€à¤Ÿà¤¾ à¤®à¥‡à¤‚ à¤¹à¥ˆ à¤…à¤­à¥€ à¤¤à¥‹\r\nà¤¹à¤® à¤•à¥€ à¤†à¤µà¤¶à¥à¤¯à¤•à¤¤à¤¾ à¤¹à¥ˆ à¤…à¤ªà¤¨à¥‡ à¤«à¤¼à¥€à¤¡ à¤•à¤¿à¤¸à¥€ à¤­à¥€ à¤ªà¥€à¤  à¤ªà¤° à¤•à¥€à¤¡à¤¼à¥‡, à¤¸à¤®à¤¸à¥à¤¯à¤¾\r\nà¤¯à¤¾ à¤¸à¥à¤à¤¾à¤µ à¤¹à¥‹ à¤¸à¤•à¤¤à¤¾ à¤¹à¥ˆ à¤†à¤ª 310 à¤ªà¥à¤°à¤¬à¤‚à¤§à¤•. à¤‰à¤¤à¥à¤ªà¤¾à¤¦ à¤«à¤¿à¤²à¤¿à¤ª à¤§à¤¨à¥à¤¯à¤µà¤¾à¤¦\r\nà¤•à¥‡ à¤²à¤¿à¤ Ofuz à¤•à¤¾ à¤‰à¤ªà¤¯à¥‹à¤— 765 4632 x81',', <br> à¤®à¥ˆà¤‚ à¤šà¤¾à¤¹à¤¤à¤¾ à¤¹à¥‚à¤ à¤•à¤¿ à¤–à¤¾à¤¤à¥‡ à¤®à¥‡à¤‚ à¤†à¤ªà¤•à¤¾ à¤¸à¥à¤µà¤¾à¤—à¤¤ <br> à¤†à¤ª\r\n[firstname] à¤•à¥‹ Ofuz. <br> à¤…à¤¬ à¤¸à¤•à¥à¤°à¤¿à¤¯ à¤¹à¥ˆ à¤”à¤° à¤¤à¥ˆà¤¯à¤¾à¤° à¤‰à¤ªà¤¯à¥‹à¤—\r\nà¤•à¤°à¤¨à¥‡ à¤•à¥‡ à¤²à¤¿à¤, à¤†à¤ª à¤®à¥‡à¤‚ à¤ªà¥à¤°à¤µà¥‡à¤¶ à¤•à¤° à¤¸à¤•à¤¤à¥‡ à¤¹à¥ˆà¤‚: <br>\r\nhttp://www.ofuz <a href = \" .net user_login.php /\r\n\"> http://www.ofuz.net/user_login.php </ a> <br>\r\n<br> à¤¸à¤®à¤¾à¤šà¤¾à¤° à¤¬à¥à¤²à¥‰à¤— à¤•à¥‡ à¤²à¤¿à¤ à¤¹à¤®à¤¾à¤°à¥‡ à¤µà¥à¤¯à¤¾à¤ªà¤¾à¤° à¤•à¥€ à¤œà¤¾à¤à¤š à¤•à¤°à¥‡à¤‚ à¤”à¤°\r\nà¤†à¤ªà¤•à¥‡ à¤…à¤¦à¥à¤¯à¤¤à¤¨ à¤ªà¤° howtos à¤”à¤° à¤Ÿà¥à¤¯à¥‚à¤Ÿà¥‹à¤°à¤¿à¤¯à¤² à¤•à¥‡ à¤²à¤¿à¤ à¤¨à¤ˆ à¤¸à¥à¤µà¤¿à¤§à¤¾à¤“à¤‚:\r\n<br <a href=\"http://www.ofuz.net/blog/\">\r\nhttp://www.ofuz.net/blog/ </ a> <br> <br> Ofuz à¤¹à¥ˆ\r\nà¤¤à¥‹ à¤…à¤­à¥€ à¤­à¥€ à¤¬à¥€à¤Ÿà¤¾ à¤®à¥‹à¤¡ à¤®à¥‡à¤‚ à¤¹à¤®> à¤†à¤µà¤¶à¥à¤¯à¤•à¤¤à¤¾ à¤¹à¥ˆ à¤…à¤ªà¤¨à¥‡ à¤«à¤¼à¥€à¤¡ à¤•à¤¿à¤¸à¥€ à¤­à¥€\r\nà¤ªà¥€à¤  à¤ªà¤° à¤•à¥€à¤¡à¤¼à¥‡, à¤¸à¤®à¤¸à¥à¤¯à¤¾ à¤¯à¤¾ à¤¸à¥à¤à¤¾à¤µ à¤¹à¥‹ à¤¸à¤•à¤¤à¤¾ à¤¹à¥ˆ à¤†à¤ª <br>. <br>\r\nà¤ªà¥à¤°à¤¬à¤‚à¤§à¤• <br> à¤‰à¤¤à¥à¤ªà¤¾à¤¦ <br> à¤§à¤¨à¥à¤¯à¤µà¤¾à¤¦ à¤•à¥‡ à¤²à¤¿à¤ à¤‰à¤ªà¤¯à¥‹à¤— à¤•à¤° Ofuz\r\n<br> <br> à¤«à¤¿à¤²à¤¿à¤ª 310 765 4632 x81','regthank','Philippe Lewicki','support@sqlfusion.com','','','hi_IN'),(32,'Votre mot de passe','Chers [firstname], Voici votre mot de passe:\r\n[password] Le nom d\'utilisateur est Ofuz\r\nhttp://ofuz.net soutien [username]','','forgotpassword','Server','support@sqlfusion.com','','','fr_FR'),(34,'Ofuzì— ì˜¤ì‹  ê²ƒì„ í™˜ì˜í•©ë‹ˆë‹¤','[firstname], ë‚œ Ofuz ë‹¹ì‹ ì„ í™˜ì˜í•˜ê³  ì‹¶ì§€ :.ì— ë¡œ\r\nê·¸ì¸í•˜ì‹­ì‹œì˜¤ ë‹¹ì‹ ì€ ì§€ê¸ˆ ê³„ì • ìˆ˜ê°€ ìž‘ë™í•˜ê³ , ì‚¬ìš©\r\ní•  ì¤€ë¹„ì— http://www.ofuz.net/user_login.php ë‰´ìŠ¤\r\në¸”ë¡œê·¸ë¥¼ìœ„í•œ í™•ì¸ ë° ìžìŠµì„œ ê¸°ëŠ¥ ì—…ë°ì´ íŠ¸ì— ëŒ€í•œ\r\nìƒˆë¡œìš´ ë¹„ì¦ˆë‹ˆìŠ¤ ì—¬ëŸ¬ë¶„ howtosê°€ : ë¬¸ì œë‚˜ìžˆì„ ìˆ˜ ìžˆ\r\nìŠµë‹ˆë‹¤ ë‹¹ì‹ ì€ ì–´ë–¤ ì œì•ˆ http://www.ofuz.net/blog/\r\në²„ê·¸ ë‹¤ì‹œ í”¼ë“œë¥¼ ê·€í•˜ì˜ ë² íƒ€ ëª¨ë“œ ê·¸ëž˜ì„œ ìš°ë¦¬ê°€ í•„\r\nìš” Ofuz ì•„ì§ë„ 310 ê´€ë¦¬ìžë¥¼ ì•ˆë‚´ í•„ë¦½ Ofuz. ì£¼ì…”ì„œ\r\nê°ì‚¬í•©ë‹ˆë‹¤ ë‹¹ì‹ ì„ìœ„í•œ ì‚¬ìš© 765 4632 x81','Ofuzí•˜ì‹­ì‹œì˜¤. <br> ì œí’ˆ <Br>, ì‚¬ìš©í•  ì¤€ë¹„í•˜ê³  ìžˆìœ¼\r\në©° [firstname] ì§€ê¸ˆì€ ì ê·¹ì ì¸ ë‹¹ì‹ ì´ ê³„ì •\r\nhttp://www.ofuz ë‹¹ì‹ ì´ ë¡œê·¸ì¸í•  ìˆ˜ \"=ì‹œ : hrefê°€\r\n<ì´ ì œí’ˆ <Br> ìœ„í•´, ì œí’ˆ <Br> ë‚´ê°€ ì›í•˜ëŠ” ê±´ì— ì˜¤\r\nì‹ ê±¸ í™˜ì˜í•©ë‹ˆë‹¤ ë‹¹ì‹ ì„ .net / user_login.phpëŠ” \">\r\nhttp://www.ofuz.net/user_login.phpì˜ <ì´ /> ì œí’ˆ\r\n<Br> <br>ì— ë‰´ìŠ¤ ë¸”ë¡œê·¸ë¥¼ìœ„í•œ í™•ì¸ ë° ë¹„ì¦ˆë‹ˆìŠ¤í•˜ì—¬\r\nì—…ë°ì´ íŠ¸ì— ëŒ€í•œ ìƒˆë¡œìš´ ê¸°ëŠ¥ ìžìŠµì„œ ë° howtos :\r\n<ì¹´ì— ìžˆë‹¤ì˜¤ì„ >ì€ <a\r\nhref=\"http://www.ofuz.net/blog/\">\r\nhttp://www.ofuz.net/blog/ <ì´ /> ì œí’ˆ <Br> ì œí’ˆ\r\n<Br> Ofuz ë„ˆë¬´ ëª¨ë“œ ì•„ì§ ë² íƒ€ ë²„ì „ì— ìš°ë¦¬ëŠ” ì´ ë‹¹\r\nì‹ ì´ ìˆ˜ìžˆëŠ” ì œì•ˆì„ ë‹¤ì‹œ ë²„ê·¸, ë¬¸ì œ ë˜ëŠ” í•„ìš” í”¼ë“œ\r\në¥¼ ê·€í•˜í•˜ì‹­ì‹œì˜¤. <br> ì œí’ˆ <br>ì˜ <br>ì„ ê´€ë¦¬ìž ì œ\r\ní’ˆ ì œí’ˆ <Br> ê°ì‚¬í•©ë‹ˆë‹¤ ë‹¹ì‹ ì„ìœ„í•œ ì‚¬ìš© Ofuz ì œí’ˆ\r\n<Br> ì œí’ˆ <Br> í•„ë¦½ 310 765 4632 x81','regthank','Philippe Lewicki','support@sqlfusion.com','','','ko_KR');
/*!40000 ALTER TABLE `emailtemplate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `emailtemplate_user`
--

DROP TABLE IF EXISTS `emailtemplate_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `emailtemplate_user` (
  `idemailtemplate_user` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(10) NOT NULL DEFAULT '0',
  `subject` varchar(150) NOT NULL DEFAULT '',
  `bodytext` text NOT NULL,
  `bodyhtml` text NOT NULL,
  `name` varchar(254) NOT NULL DEFAULT '',
  `sendername` varchar(254) NOT NULL DEFAULT '',
  `senderemail` varchar(254) NOT NULL DEFAULT '',
  `language` varchar(30) NOT NULL,
  PRIMARY KEY (`idemailtemplate_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `emailtemplate_user`
--

LOCK TABLES `emailtemplate_user` WRITE;
/*!40000 ALTER TABLE `emailtemplate_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `emailtemplate_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_account`
--

DROP TABLE IF EXISTS `google_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_account` (
  `idgoogle_account` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(15) NOT NULL,
  `session_token` varchar(200) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  PRIMARY KEY (`idgoogle_account`),
  UNIQUE KEY `idgoogle_account_2` (`idgoogle_account`),
  KEY `idgoogle_account` (`idgoogle_account`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_account`
--

LOCK TABLES `google_account` WRITE;
/*!40000 ALTER TABLE `google_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_contact`
--

DROP TABLE IF EXISTS `google_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_contact` (
  `idgoogle_contact` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(15) DEFAULT NULL,
  `last_updated` datetime DEFAULT NULL,
  `mode` varchar(5) NOT NULL,
  PRIMARY KEY (`idgoogle_contact`),
  UNIQUE KEY `idgoogle_contact_2` (`idgoogle_contact`),
  KEY `idgoogle_contact` (`idgoogle_contact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_contact`
--

LOCK TABLES `google_contact` WRITE;
/*!40000 ALTER TABLE `google_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `google_contact_info`
--

DROP TABLE IF EXISTS `google_contact_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `google_contact_info` (
  `idgoogle_contact_info` int(10) NOT NULL AUTO_INCREMENT,
  `entry_id` varchar(200) NOT NULL,
  `entry_link_self` varchar(200) NOT NULL,
  `entry_link_edit` varchar(200) NOT NULL,
  `idcontact` int(15) NOT NULL,
  `iduser` int(15) DEFAULT NULL,
  PRIMARY KEY (`idgoogle_contact_info`),
  UNIQUE KEY `idgoogle_contact_info_2` (`idgoogle_contact_info`),
  KEY `idgoogle_contact_info` (`idgoogle_contact_info`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `google_contact_info`
--

LOCK TABLES `google_contact_info` WRITE;
/*!40000 ALTER TABLE `google_contact_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `google_contact_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice` (
  `idinvoice` int(10) NOT NULL AUTO_INCREMENT,
  `num` int(14) NOT NULL,
  `iduser` int(14) NOT NULL,
  `description` mediumtext NOT NULL,
  `amount` float(10,2) NOT NULL,
  `datepaid` date NOT NULL,
  `datecreated` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `discount` varchar(10) NOT NULL,
  `idcontact` int(14) NOT NULL,
  `due_date` date NOT NULL,
  `invoice_address` mediumtext NOT NULL,
  `invoice_term` mediumtext NOT NULL,
  `invoice_note` mediumtext NOT NULL,
  `sub_total` float(14,2) NOT NULL,
  `net_total` float(14,2) NOT NULL,
  `amt_due` float(14,2) NOT NULL,
  `idcompany` int(14) NOT NULL,
  `tax` varchar(20) NOT NULL,
  `set_delete` int(1) NOT NULL DEFAULT '0',
  `total_discounted_amt` float(15,2) NOT NULL,
  `total_taxed_amount` float(15,2) NOT NULL,
  PRIMARY KEY (`idinvoice`),
  UNIQUE KEY `idinvoice_2` (`idinvoice`),
  KEY `idinvoice` (`idinvoice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice`
--

LOCK TABLES `invoice` WRITE;
/*!40000 ALTER TABLE `invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_callback`
--

DROP TABLE IF EXISTS `invoice_callback`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoice_callback` (
  `idinvoice_callback` int(10) NOT NULL AUTO_INCREMENT,
  `idinvoice` int(15) NOT NULL,
  `callback_url` varchar(200) NOT NULL,
  `next_url` varchar(200) NOT NULL,
  PRIMARY KEY (`idinvoice_callback`),
  UNIQUE KEY `idinvoice_callback_2` (`idinvoice_callback`),
  KEY `idinvoice_callback` (`idinvoice_callback`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_callback`
--

LOCK TABLES `invoice_callback` WRITE;
/*!40000 ALTER TABLE `invoice_callback` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_callback` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoiceline`
--

DROP TABLE IF EXISTS `invoiceline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invoiceline` (
  `idinvoiceline` int(10) NOT NULL AUTO_INCREMENT,
  `idinvoice` int(14) NOT NULL,
  `description` mediumtext NOT NULL,
  `price` float(10,2) NOT NULL,
  `qty` float(10,2) NOT NULL,
  `total` float(10,2) NOT NULL,
  `item` varchar(200) NOT NULL,
  `line_tax` float(15,2) NOT NULL,
  `discounted_amount` float(15,2) NOT NULL,
  `taxed_amount` float(15,2) NOT NULL,
  PRIMARY KEY (`idinvoiceline`),
  UNIQUE KEY `idinvoiceline_2` (`idinvoiceline`),
  KEY `idinvoiceline` (`idinvoiceline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoiceline`
--

LOCK TABLES `invoiceline` WRITE;
/*!40000 ALTER TABLE `invoiceline` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoiceline` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_audit`
--

DROP TABLE IF EXISTS `login_audit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_audit` (
  `idlogin_audit` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(15) NOT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_address` varchar(200) NOT NULL,
  `login_type` varchar(15) NOT NULL,
  PRIMARY KEY (`idlogin_audit`),
  UNIQUE KEY `idlogin_audit_2` (`idlogin_audit`),
  KEY `idlogin_audit` (`idlogin_audit`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_audit`
--

LOCK TABLES `login_audit` WRITE;
/*!40000 ALTER TABLE `login_audit` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_audit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `idmessage` int(10) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `language` varchar(50) NOT NULL,
  `context` varchar(60) NOT NULL DEFAULT '',
  `can_close` varchar(3) NOT NULL DEFAULT 'yes',
  `close_duration` varchar(20) NOT NULL DEFAULT '1 month',
  `plan` varchar(10) DEFAULT 'all',
  PRIMARY KEY (`idmessage`),
  UNIQUE KEY `idmessage_2` (`idmessage`),
  KEY `idmessage` (`idmessage`)
) ENGINE=MyISAM AUTO_INCREMENT=80 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (1,'test','','','','yes','1 month','all'),(2,'welcome client portal','[user_firstname] [user_lastname] has opened you an access to [user_company] portal and would like to share files and messages with you.<br>\r\nBookmark this page so you can come back to it anytime you need to send a message or upload a file for [user_firstname]','en_US','','yes','1 month','all'),(3,'welcome','Welcome and thank you for using Ofuz. This is your <b>Dashboard</b> with your <b>workfeed</b> to follow your co-workers\' and contacts\' activity.\r\n<ul>\r\n<li>Get started by <a href=\"co-workers.php\">inviting your co-workers</a> so you can share your contacts and projects with them.</li>\r\n<li>If you want to upgrade your current plan, you can do that at anytime: Just <a href=\"/upgrade_plan.php\">click here</a> to view the available plans.</li>\r\n<li>If you have a gmail account you can also import all your contacts from Gmail, Facebook, or Twitter using our <a href=\"sync.php\">Sync utility</a>.</li>\r\n<li>Ofuz is still in beta so please send us your feed back at <a href=\"mailto:support@sqlfusion.com\">support@sqlfusion.com</a> or click on the <b>FeedBack</b> button on the left.</li>\r\n<li>We post tips for freelancers and small businesses on our blog along with updates on what\'s happening at Ofuz: <a \r\nhref=\"http://www.ofuz.com/blog/\">\r\nofuz.com/blog</a></li>\r\n<li>If you want to follow our development check our <a href=\"hhttp://www.ofuz.net/PublicProject/361\">tasks for Ofuz 0.7 </a></li>\r\n</ul>','en_US','dashboard','yes','1 month','all'),(34,'share file and notes initialisation','Click on the URL below to generate a unique web address that your contact can use  to view notes, download documents, and upload documents to their Ofuz contact notes.\r\n<br/>\r\n ','en_US','','no','1 month','all'),(4,'unauthorized contact access','You do not have access to view this contact. Check the contact number and verify whether your co-worker has shared this contact with you.\r\n<br />\r\n<a href=\"/co_workers.php\">Co-Workers</a>','en_US','','yes','1 month','all'),(6,'unauthorized project access','You do not have access to view this project. Check the project number and verify whether your co-worker has shared this project with you.\r\n\r\n<a href=\"/projects.php\">Projects</a>','en_US','','yes','1 month','all'),(5,'unauthorized project access','You do not have access to view this project. Check the project number and verify whether your co-worker has shared this project with you.\r\n<br />\r\n<a href=\"/projects.php\">Projects</a>','en_US','','yes','1 month','all'),(7,'unauthorized task access','You do not have access to view this task. Check the task number and verify whether your co-worker has shared the project that this task is associated with.<br /> \r\n<a href=\"/projects.php\">Projects</a>','en_US','','yes','1 month','all'),(8,'save as template','Your message has been sent successfully; now you can save this message as a template.\r\nYou can then reuse it the next time you send a message.\r\nEnter a name for the email template and press <b>Save</b><br>\r\nHere are some <a href=\"http://www.ofuz.com/blog/2010/04/keeping-in-touch-micro-mailing-for-the-solo-entrepreneur/\">tips</a> for using email templates.','en_US','','yes','1 month','all'),(9,'email footer text','--------------------------------------\r\nWork your network with Ofuz, http://www.ofuz.com\r\n--------------------------------------\r\nThis message was sent to [receiver_name] from [sender_name]. \r\nIf you do not want to receive any more messages from [sender_name] through Ofuz click the link below:\r\n[unsubscribe_url]\r\n','en_US','','yes','1 month','all'),(10,'email footer html','<hr>\r\nWork your network with <a href=\"http://www.ofuz.com/\">Ofuz</a>\r\n<hr>\r\nThis message was sent to <b>[receiver_name]</b> from <b>[sender_name]</b>.<br>\r\nIf you do not want to receive any more messages from [sender_name] <a href=\"[unsubscribe_url]\">click here</a> or the url below:<br>\r\n[unsubscribe_url]\r\n\r\n','en_US','','yes','1 month','all'),(11,'contact unsubscribe email','You preference as been updated and will not receive any additional communications by email from [user_name].\r\n<br/>\r\nIf you have any question feel free to contact us at <a href=\"mailto:abuse@sqlfusion.com\">abuse@sqlfusion.com</a>','en_US','','no','1 month','all'),(12,'web form creator instruction','You can add new contacts by creating a web form. Contacts fill it out to be added to your contact list.\r\n<br/>\r\nBelow is a list of all the available web form fields. Select the box to the left of each field you want to appear on your form. <br/> \r\nYou can change the field labels and their size to better fit your needs.<br/>\r\nSee these how-to blog posts (<a href=\"http://www.ofuz.com/blog/2010/04/creating-a-free-squeeze-page-pt-1/\">Part1</a>, <a href=\"http://www.ofuz.com/blog/2010/05/create-a-free-squeeze-page-pt-2/\">Part2</a>, <a href=\"http://www.ofuz.com/blog/2010/05/how-to-create-a-free-squeeze-page-using-ofuz-pt3/\">Part3</a>) on using a Web Form to create a squeeze page.\r\n','en_US','','yes','1 month','all'),(13,'no contacts yet','To get started <a href=\"contact_add.php\">add your first contact here.</a>\r\n<br/>\r\nIf you have a Gmail account you can also import all your contacts from Gmail or Google Apps using our <a href=\"sync.php\">Sync utility</a>.<br/>\r\nOfuz also syncs with Facebook and Twitter!<br/>\r\nRead tips on using Ofuz Contacts here: <a href=\"http://www.ofuz.com/blog/2010/04/reducing-opportunity-losses-for-the-freelancer-pt-1/\">Part 1</a>, <a href=\"http://www.ofuz.com/blog/2010/04/reducing-opportunity-losses-for-the-freelancer-pt-2/\">Part 2</a>.','en_US','','yes','1 month','all'),(14,'drop box note','Did you know that notes can be added to your Ofuz account remotely?  You can create Notes or add new Contacts via email by using your dropbox address.  \r\n<br /><br/>\r\nAdd the following email address to your CC or BCC when sending emails. The contents of the email will be added as a Note:\r\n<br/>','en_US','','yes','1 month','all'),(42,'free_c','As a free user, you are limited to 200 contacts.\r\n<a href=\"upgrade_plan.php\">Upgrade now.</a>\r\n','en_US','subscription','no','1 month','all'),(15,'drop box task','You can create tasks remotely by using the dropbox task feature. Simply send an email to your assigned dropbox address and a new task will be created. The subject of the email will become the task\'s title.\r\n<br /><br />\r\nYour contacts can create tasks for you as well.\r\n<br /><br />\r\nIf you ever forget your dropbox email address, just check back here and it will be displayed.','en_US','','yes','1 month','all'),(16,'fb_import_friends','Facebook friend sync will keep your Facebook friends in sync with your Ofuz contacts.\r\nIf you are not yet connected with Facebook connect you will be asked to login to Facebook on the next page.\r\n<br>\r\nThe import <b>will take some time</b>, so leave the window open until it takes you back to the contact list.\r\n<br/><br/>\r\nOnce completed you can view all your Facebook friends by clicking on the Facebook tag.\r\n<br/>','en_US','','yes','1 month','all'),(20,'global_discussion_email_on','Your task discussion email alert is globally on. You will get email alerts for discussions that you are related to. You can turn global email alerts off if you do not want to get the emails.\r\nTurning alerts off will turn off all the emails from the discussions that you are participating in. ','en_US','','yes','1 month','all'),(21,'global_discussion_email_off','You have turned off the email alerts for task discussions. Now you will not receive emails from the task discussions. To re-enable email alerts you will need to set global email alerts to On.','en_US','','yes','1 month','all'),(17,'unauthorized weform access','This Webform does not belong to you. Please check the URL.<br />\r\n<a href=\"/settings.php\">Go Back</a>\r\n','en_US','','yes','1 month','all'),(18,'unauthorized email template access','This email template does not belong to you. Please check the URL.','en_US','','yes','1 month','all'),(19,'reset rest api key','Your API key is shown below. You will need to use this key for API calls. However you can reset the API key at any point of time. After you have reset the API key make sure to replace the old API key with the new one in the API calls because the old API key will be over-witten with the new one. ','en_US','','yes','1 month','all'),(22,'google_gears','When Gears is turned on, you will have access to a Gears-enabled Contacts page.','en_Us','','yes','1 month','all'),(23,'contacts_intro','Your contacts are listed below.<br/><br/>\r\nYou can Search by Tag. This allows you to act on multiple contacts. For example, you can send an email to multiple contacts after using the Search by Tag feature.<br/><br/>\r\nTo act on specific contacts, click anywhere in the white space of one or more contacts, then select an action from the box that appears above the contacts list.<br/><br/>\r\nTo edit a contact, click that contact\'s name and then click <b>edit</b> in the <b>Contact Information</b> box at top left.','en_US','contact list','yes','30 minute','all'),(24,'invoice first time','Welcome to Ofuz Invoicing.<br/><br/>\r\nBefore you start creating invoices make sure you are properly set up.<br>\r\nGo in Setting your <a href=\"/settings_info.php\">account information</a> and set up your full address and company information.\r\n<br/><br/>\r\nNext, set up your logo, Paypal or credit card gateway, and currency in <a href=\"/settings_invoice.php\">Invoice settings</a>.','en_US','','yes','2 month','all'),(25,'invoice first time','Welcome to Ofuz Invoicing.<br/><br/>\r\nBefore you start creating invoices make sure you are properly set up.<br>\r\nGo in Setting your <a href=\"/settings_info.php\">account information</a> and set up your full address and company information.\r\n<br/><br/>\r\nNext, set up your logo, Paypal or credit card gateway, and currency in <a href=\"/settings_invoice.php\">Invoice settings</a>.','en_US','','yes','2 month','all'),(26,'contact intro','Here you can see all the details on a contact. \r\nYou can add notes to your contact and you can attach files to the contact.<br/><br/>\r\nIf you want all the email you send to this contact to be added as Notes here, configure your email software to automatically CC or BCC messages to your drop box email address. <a href=\"/drop_box_note.php\">Click here</a> to se tup your email drop box.<br/>\r\n<br/>\r\nYou can also share all the notes and attached files with your contact by generating a contact portal link. Just click on the <b>Share file and notes</b> button.<br/>\r\n<br/>','en_US','contact','yes','2 month','all'),(27,'contact feed agregation','You can follow the updates and changes from your contacts like Twitter, blogs and web sites updates directly in Ofuz.<br>\r\n<a href=\"/contact_edit.php\">Edit this contact</a> and in the <b>Web Site</b> section add his twitter username, blog address or web site.<br>\r\n<br/>Once you save and come back here look on the list of sites, if you see a small rss icon: <img src=\"/images/feed-icon-12x12-orange.gif\"> click on it.<br>\r\n Once it turn green <img src=\"/images/feed-icon-12x12-green.png\"> this means that all updates from that web site will be added to your contact notes.\r\n','en_US','contact','yes','1 month','all'),(28,'tasks intro','This is a centralized place for all your tasks.<br/>\r\nTask from projects you own or task attached to contact will display here.<br/>\r\n<br/>\r\nTo edit a task click on it and to set a task as completed click on the check box.<br/>','en_US','tasks','yes','2 month','all'),(29,'project intro','All the project you create or the projects shared to you are listed here.<br/>\r\nTo share projects with a co-worker add them to your co-worker list. <a href=\"/co_workers.php\">Click here</a> to add co-workers.<br/>\r\n','en_US','project list','yes','2 month','all'),(30,'project task list intro','Use the form on the left to add new tasks in this project.<br/>\r\nAll the tasks associated with this project listed here, click on its name to start the discussion.<br/><br/>\r\nTo share this project with other participant make sure they are added to your <a href=\"/co_workers.php\">co-worker list</a> then select them in the drop down and click add.<br/> \r\n<br/>\r\n','en_US','project tasks','yes','2 month','all'),(31,'task discussion intro','You can add notes and documents to this task and all participants will receive an email alert. This will engage the conversation.\r\n<br/>\r\nIf you want to ping or nudge a specific participant, add a @ to their first or last name, like @philippe or @john , and s/he will receive a special email alert.','en_US','task discussion','yes','2 month','all'),(32,'invoice list intro','You can filter your invoice based on there status.<br/>\r\n<b>New</b> are invoiced prepared but not yet sent to the customer.<br/>\r\n<b>Sent</b> are invoice sent and waiting payments.<br/>\r\n<b>Partial</b> are invoices that have received payment but are not yet fully paid.<br/>\r\n<b>Paid</b> are invoice paid in full.<br/>\r\n<br/>\r\nBy default it lists all the invoices of the current month. Use the drop down with the month to view invoices from previous months.<br/>\r\n<br/>\r\nTo view or edit an invoice, click on it.<br/>','en_US','invoice list','yes','2 month','all'),(33,'welcome did you know','Welcome back,\r\n<br>\r\nDid you know you can add notes to a contact from your email application. <br>\r\nYou can get more information on that feature at:<br>\r\n  <a href=\"drop_box_note.php\">Add note drop box</a><br>\r\n<br>\r\n','en_US','dashboard','yes','1 month','all'),(35,'share file and notes settings','Send the web address bellow to your contact. <br/>\r\nHe will then be able to view all the notes and files you uploaded as well as upload his own documents and notes.<br/>\r\n\r\nAny body you will share the url bellow will be able to view the notes and documents. <br/>\r\n\r\nIf you want to close the access click on <b>Stop Access</b>.\r\nYou can also regenerate the web address, this will make the previous address unusable and give you a new one.<br/>\r\nAll the document and files will be preserved under your contact notes.<br/>\r\n','en_US','','no','1 month','all'),(36,'url portal initiated','New web address for this contact as been generated and sent by email.','en_US','','no','1 month','all'),(37,'url portal stoped','The web address you shared as been canceled.  \r\nFiles and notes for this contact are not accessible by external users.\r\n','en_US','','no','1 month','all'),(38,'url portal regenerated','A new access has been generated and the Url link has been successfully mailed to the contact.','en_US','','no','1 month','all'),(39,'setting_sync','You can also expand your contacts while synchronizing with:','en_US','contact','yes','1 month','all'),(40,'unauthorized_note_edit','Opps !! sorry this note was not added by you, so the edit operation is not permitted on this note.','en_US','','no','1 month','all'),(41,'unauthorized_invoice_access','You do not have access to view this invoice. Check the invoice number.','en_US','','no','1 month','all'),(43,'24_c','Congratulation you have reach over a 1000 contacts. \r\n<br/>\r\nYou current plan is limited to 1000 contact. <br/\r\n\r\n<a href=\"upgrade_99.php\">Click here</a> to Upgrade your plan. ','en_US','subscription','no','1 month','all'),(44,'free_p','The free plan is limited to 5 projects. <br/>\r\n<a href=\"upgrade_plan.php\">Click here</a> to upgrade your account to and more projects.','en_US','subscription','no','1 month','all'),(45,'free_i','Your current plan is limited at 5 invoices per month.<br/>\r\nPlease <a href=\"upgrade_plan.php\">click here</a> to upgrade you plan.','en_US','subscription','no','1 month','all'),(46,'inv_p','You have a special Free Unlimited invoice plan. This plan is limited to 5 projects.\r\n\r\nPlease upgrade now to add more project. \r\nThank you for supporting Ofuz.','en_US','','no','1 month','all'),(47,'unauthorized_autoresponder_access','This auto-responder does not belong to you.Please check the URL.','en_US','','no','1 month','all'),(48,'unauthorized_autoresponder_emailtemplate_access','The auto-responder email template you are trying to access does not belong to you. Please check the URL.','en_US','','no','1 month','all'),(49,'autoresponder instruction','You can create the auto responder and the email templates for the auto responders.','en_US','autoresponder instruction','yes','1 month','all'),(50,'autoresponder email instruction','You can create the email templates for the auto responders and can set the number of days when the emails to be sent.','en_US','autoresponder email instruction','yes','1 month','all'),(51,'wrong_invoice_url','This invoice doesn\'t exist or as been canceled.','en_US','','no','1 month','all'),(52,'add_edit_autoresponder','You can create the auto responder and the email templates for the auto responders. ','en_US','add edit autoresponder','no','1 month','all'),(53,'edit_autoresponder_email_template','You can edit your autoresponder email template here.','en_US','edit autoresponder email template','no','1 month','all'),(54,'my profile instruction','This is your public profile. You can share it with others so they can easily add you as a contact.\r\n \r\nThe code bar is a QRCode, it can be scan by most recent cell phone and it will import your profile contact information.\r\n\r\nDownload the QRCode, add it to your business cards or add it as an image on your phone.\r\n','en_US','','no','1 month','all'),(55,'invoice_client_email_not_found','We could not send the invoice as the contact in this invoice is missing an email address. ','en_US','','no','1 month','all'),(56,'client_invoice_sent','Invoice is sent to [contact_email].','en_US','invoice','no','1 month','all'),(57,'welcome client portal','[user_firstname] [user_lastname] a ouvert un accÃ¨s\r\nplus au portail [user_company] et que vous\r\nsouhaitez partager des fichiers et des messages\r\navec vous. <br> Marquer cette page afin que vous\r\npuissiez y revenir Ã  tout moment vous avez besoin\r\npour envoyer un message ou tÃ©lÃ©charger un fichier\r\npour [user_firstname]','fr_FR','','yes','1 month','all'),(58,'ofuz_export_xml','You can take your Ofuz account backup here.Export it and import on your server.','en_US','','no','1 month','all'),(59,'ofuz_export_xml_success','You have successfully exported your account.','en_US','','no','1 month','all'),(60,'ofuz_export_xml_failure','Sorry! Your account could not be exported.','en_US','','no','1 month','all'),(61,'registration invitation','[sender_firstname] [sender_lastname] wants you to be one of his or her co-workers in Ofuz.<br/>\r\nOnce you have registered, [sender_firstname] will be able to share contacts and projects with you.\r\n<br/>\r\nFill in the form below to register and get started.','en_US','','no','1 month','all'),(62,'reg_duplicate_email','The email id is already in use.','en_US','','no','1 month','all'),(63,'already_unsub_from_list','You have already unsubscribe from the list.\r\n\r\n<br />','en_US','','no','1 month','all'),(64,'unsub_list_message','[firstname] [lastname] you have successfully unsubscribed from the autoresponder series called \r\n[responder]\r\n\r\n<br />','en_US','','no','1 month','all'),(65,'no_contact_found','No contact were found, try another keyword or [click_here] to view all your contacts.','en_US','','no','1 month','all'),(66,'cw_user-is-already-cw','User is already your Co-Worker.','en_US','co-worker','yes','1 month','all'),(67,'cw_already-have-pending-invitation','You already have a pending invitation for this user.','en_US','co-worker','yes','1 month','all'),(68,'cw_user-is-already-in-db-notification-sent','User is already in the Database so the notification is sent.','en_US','co-worker','yes','1 month','all'),(69,'cw_already-have-pending-invitation-to','You already have a pending invitation to [enc_email].','en_US','co-worker','yes','1 month','all'),(70,'cw_user-not-in-db-register','User is not in the Database. So the Email Notification is sent to register.','en_US','co-worker','yes','1 month','all'),(71,'cw_enter-emailid','Please Enter an email id.','en_US','co-worker','yes','1 month','all'),(72,'cw_notification_sent_to_user','Notification is sent to the user. Once User accepts the invitation, user will be your co-worker and vise-versa.','en_US','co-worker','yes','1 month','all'),(73,'db_side-info','Welcome [user_firstname], \r\n\r\nDon\'t be limited by our Free plan -- take full advantage of the Ofuz Premium plan.\r\n<br/><br/>\r\nFor only $24/month you get unlimited projects and invoices with up to 5,000 contacts.\r\n<div align=\"center\">\r\n<a href=\"upgrade_plan.php\"><strong>Upgrade now</strong></a>\r\n</div>','en_US','db_side-info','yes','1 month','all'),(76,'unauthorized contact access','Je hebt geen toegang tot dit contact te zien. Controleer het telefoonnummer en\r\ncontroleer of uw medewerker heeft u gedeeld dit contact met. <br /> <a\r\nhref=\"/co_workers.php\"> Co-werknemers </ a>','fr_FR','','yes','1 month','all'),(77,'welcome client portal','ÙØªØ­Øª [user_firstname] [user_lastname] Ù„Ùƒ Ø§Ù„ÙˆØµÙˆÙ„ Ø¥Ù„Ù‰ Ø¨ÙˆØ§Ø¨Ø© [user_company] ØŒ ÙˆÙ†ÙˆØ¯\r\nØ£Ù† ØªØ¨Ø§Ø¯Ù„ Ø§Ù„Ù…Ù„ÙØ§Øª ÙˆØ§Ù„Ø±Ø³Ø§Ø¦Ù„ Ù…Ø¹Ùƒ. <br>Ù…Ø±Ø¬Ø¹ÙŠØ© Ù„Ù‡Ø°Ù‡ Ø§Ù„ØµÙØ­Ø© Ø­ØªÙ‰ ÙŠÙ…ÙƒÙ†Ùƒ Ø§Ù„Ø¹ÙˆØ¯Ø© Ø¥Ù„ÙŠÙ‡Ø§ ÙÙŠ\r\nØ£ÙŠ ÙˆÙ‚Øª ÙƒÙ†Øª ÙÙŠ Ø­Ø§Ø¬Ø© Ø§Ù„Ù‰ Ø§Ø±Ø³Ø§Ù„ Ø±Ø³Ø§Ù„Ø© Ø£Ùˆ ØªØ­Ù…ÙŠÙ„ Ù…Ù„Ù Ù„[user_firstname]','ar_KW','','yes','1 month','all'),(78,'welcome client portal','[user_firstname] [user_lastname] ka hapur ju njÃ« qasje nÃ« [user_company] portal\r\ndhe do tÃ« doja tÃ« ndani fotografi dhe mesazhe me ju. <br> Bookmark this page\r\nkÃ«shtu qÃ« ju mund tÃ« kthehen nÃ« atÃ« nÃ« Ã§do kohÃ« ju duhet pÃ«r tÃ« dÃ«rguar njÃ«\r\nmesazh ose ngarkoni njÃ« fotografi pÃ«r [user_firstname]','sq_AL','','yes','1 month','all'),(79,'welcome client portal','[user_firstname] [user_lastname] Ð¾Ñ‚ÐºÑ€Ñ‹Ð» Ð²Ð°Ð¼ Ð´Ð¾ÑÑ‚ÑƒÐ¿ Ðº [user_company] Ð¿Ð¾Ñ€Ñ‚Ð°Ð» Ð¸\r\nÑ…Ð¾Ñ‚ÐµÐ»Ð¸ Ð±Ñ‹ Ð¾Ð±Ð¼ÐµÐ½Ð¸Ð²Ð°Ñ‚ÑŒÑÑ Ñ„Ð°Ð¹Ð»Ð°Ð¼Ð¸ Ð¸ ÑÐ¾Ð¾Ð±Ñ‰ÐµÐ½Ð¸ÑÐ¼Ð¸ Ñ Ð²Ð°Ð¼Ð¸. <br> Ð—Ð°ÐºÐ»Ð°Ð´ÐºÐ° Ð½Ð° ÑÑ‚Ð¾Ð¹\r\nÑÑ‚Ñ€Ð°Ð½Ð¸Ñ†Ðµ, Ñ‡Ñ‚Ð¾Ð±Ñ‹ Ð²Ñ‹ Ð¼Ð¾Ð³Ð»Ð¸ Ð²ÐµÑ€Ð½ÑƒÑ‚ÑŒÑÑ Ðº Ð½ÐµÐ¼Ñƒ Ð² Ð»ÑŽÐ±Ð¾Ðµ Ð²Ñ€ÐµÐ¼Ñ Ð²Ð°Ð¼ Ð½ÐµÐ¾Ð±Ñ…Ð¾Ð´Ð¸Ð¼Ð¾ Ð¾Ñ‚Ð¿Ñ€Ð°Ð²Ð¸Ñ‚ÑŒ\r\nÑÐ¾Ð¾Ð±Ñ‰ÐµÐ½Ð¸Ðµ Ð¸Ð»Ð¸ Ð·Ð°Ð³Ñ€ÑƒÐ·Ð¸Ñ‚ÑŒ Ñ„Ð°Ð¹Ð» Ð´Ð»Ñ [user_firstname]','ru_RU','','yes','1 month','all');
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_draft`
--

DROP TABLE IF EXISTS `message_draft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_draft` (
  `idmessage_draft` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(14) NOT NULL,
  `type` varchar(50) NOT NULL,
  `timestamp` varchar(30) NOT NULL,
  `message_content` mediumtext NOT NULL,
  `message_subject` varchar(200) NOT NULL,
  PRIMARY KEY (`idmessage_draft`),
  UNIQUE KEY `idmessage_draft_2` (`idmessage_draft`),
  KEY `idmessage_draft` (`idmessage_draft`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_draft`
--

LOCK TABLES `message_draft` WRITE;
/*!40000 ALTER TABLE `message_draft` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_draft` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_usage`
--

DROP TABLE IF EXISTS `message_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_usage` (
  `idmessage_usage` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) NOT NULL,
  `date_sent` date NOT NULL DEFAULT '0000-00-00',
  `num_msg_sent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idmessage_usage`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_usage`
--

LOCK TABLES `message_usage` WRITE;
/*!40000 ALTER TABLE `message_usage` DISABLE KEYS */;
INSERT INTO `message_usage` VALUES (1,17,'2010-12-05',2),(2,17,'2010-12-06',1),(3,15,'2010-12-05',3),(4,15,'2010-12-06',1),(5,1,'2010-12-23',3);
/*!40000 ALTER TABLE `message_usage` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message_user`
--

DROP TABLE IF EXISTS `message_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message_user` (
  `idmessage_user` int(10) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(150) NOT NULL,
  `iduser` int(10) NOT NULL,
  `closed_until` datetime DEFAULT NULL,
  PRIMARY KEY (`idmessage_user`),
  KEY `isClosed` (`key_name`,`iduser`,`closed_until`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message_user`
--

LOCK TABLES `message_user` WRITE;
/*!40000 ALTER TABLE `message_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `message_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `note_draft`
--

DROP TABLE IF EXISTS `note_draft`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `note_draft` (
  `idnote_draft` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(14) NOT NULL,
  `id` int(14) NOT NULL,
  `id_type` varchar(50) NOT NULL,
  `note_content` text NOT NULL,
  `timestamp` varchar(30) NOT NULL,
  PRIMARY KEY (`idnote_draft`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `note_draft`
--

LOCK TABLES `note_draft` WRITE;
/*!40000 ALTER TABLE `note_draft` DISABLE KEYS */;
/*!40000 ALTER TABLE `note_draft` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_invoice`
--

DROP TABLE IF EXISTS `payment_invoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_invoice` (
  `idpayment_invoice` int(10) NOT NULL AUTO_INCREMENT,
  `idpayment` int(14) NOT NULL,
  `idinvoice` int(14) NOT NULL,
  `amount` float(10,2) NOT NULL,
  PRIMARY KEY (`idpayment_invoice`),
  UNIQUE KEY `idpayment_invoice_2` (`idpayment_invoice`),
  KEY `idpayment_invoice` (`idpayment_invoice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_invoice`
--

LOCK TABLES `payment_invoice` WRITE;
/*!40000 ALTER TABLE `payment_invoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_invoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paymentlog`
--

DROP TABLE IF EXISTS `paymentlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentlog` (
  `idpaymentlog` int(10) NOT NULL AUTO_INCREMENT,
  `timestamp` varchar(30) NOT NULL,
  `idinvoice` int(14) NOT NULL,
  `amount` float(15,2) NOT NULL,
  `payment_type` varchar(50) NOT NULL,
  `ref_num` varchar(100) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`idpaymentlog`),
  UNIQUE KEY `idpaymentlog_2` (`idpaymentlog`),
  KEY `idpaymentlog` (`idpaymentlog`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paymentlog`
--

LOCK TABLES `paymentlog` WRITE;
/*!40000 ALTER TABLE `paymentlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `paymentlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `paymentlog_extra_amount`
--

DROP TABLE IF EXISTS `paymentlog_extra_amount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `paymentlog_extra_amount` (
  `idpaymentlog_extra_amount` int(10) NOT NULL AUTO_INCREMENT,
  `idpaymentlog` int(15) NOT NULL,
  `extra_amt` float(15,2) NOT NULL,
  `iduser` int(15) NOT NULL,
  PRIMARY KEY (`idpaymentlog_extra_amount`),
  UNIQUE KEY `idpaymentlog_extra_amount_2` (`idpaymentlog_extra_amount`),
  KEY `idpaymentlog_extra_amount` (`idpaymentlog_extra_amount`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paymentlog_extra_amount`
--

LOCK TABLES `paymentlog_extra_amount` WRITE;
/*!40000 ALTER TABLE `paymentlog_extra_amount` DISABLE KEYS */;
/*!40000 ALTER TABLE `paymentlog_extra_amount` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `idpayments` int(10) NOT NULL AUTO_INCREMENT,
  `idcompany` int(14) NOT NULL,
  `amount` float(10,2) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `datereceived` date NOT NULL,
  PRIMARY KEY (`idpayments`),
  UNIQUE KEY `idpayments_2` (`idpayments`),
  KEY `idpayments` (`idpayments`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plugin_enable`
--

DROP TABLE IF EXISTS `plugin_enable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugin_enable` (
  `idplugin_enable` int(10) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(200) NOT NULL,
  `enabled` int(1) NOT NULL,
  `iduser` varchar(19) NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idplugin_enable`),
  UNIQUE KEY `idplugin_enable_2` (`idplugin_enable`),
  KEY `idplugin_enable` (`idplugin_enable`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plugin_enable`
--

LOCK TABLES `plugin_enable` WRITE;
/*!40000 ALTER TABLE `plugin_enable` DISABLE KEYS */;
INSERT INTO `plugin_enable` VALUES (1,'PaymentLogBlock',1,'15','2010-11-04 11:35:27'),(2,'RecurrentInvoiceBlock',1,'15','2010-11-04 11:14:43'),(3,'InvoicesMonthlyGraphBlock',1,'15','2010-11-04 11:14:46'),(4,'InvoicesYTDBlock',1,'15','2010-11-04 11:14:48'),(5,'ContactDetailBlock',1,'15','2010-11-04 11:37:03'),(6,'ContactInvoiceBlock',1,'15','2010-11-04 11:14:52'),(7,'ContactAddTaskBlock',1,'15','2010-11-04 11:14:56'),(8,'ContactShareCoworkerBlock',1,'15','2010-11-04 11:14:59'),(9,'ContactTasksBlock',1,'15','2010-11-04 11:15:03'),(10,'ContactFileUploadUrl',0,'15','2010-11-04 13:29:20'),(11,'ContactSubTagSearchBlock',1,'15','2010-11-04 11:15:10'),(12,'ContactTagSearchBlock',1,'15','2010-11-04 11:15:14'),(13,'DashboardTodaysTask',1,'15','2010-11-04 11:15:18'),(14,'DashboardMessageBlock',1,'15','2010-11-04 11:15:22'),(15,'TasksAddTaskBlock',1,'15','2010-11-04 11:15:26'),(16,'TaskProgressBlock',1,'15','2010-11-04 11:15:32'),(17,'BlockSample',1,'15','2010-11-04 11:16:03'),(18,'CoworkerAdd',1,'15','2010-11-04 11:16:07'),(19,'CoworkerListInvitations',1,'15','2010-11-04 11:16:11'),(20,'CoworkerSearch',1,'15','2010-11-04 11:16:15'),(21,'CoworkerSendInvitationEmail',1,'15','2010-11-04 11:16:25'),(22,'ProjectAddCoworkerBlock',1,'15','2010-11-04 11:16:41'),(23,'TaskOwnerBlock',1,'15','2010-11-04 11:16:53'),(24,'TaskDropBoxBlock',1,'15','2010-11-04 11:16:58'),(25,'ProjectsAddProjectBlock',1,'15','2010-11-04 11:17:03'),(26,'ProjectAddTaskDropboxBlock',1,'15','2010-11-04 11:17:08'),(27,'ProjectDiscussionEmailAlertBlock',1,'15','2010-11-04 11:17:12'),(28,'ProjectAddProjectTaskBlock',1,'15','2010-11-04 11:17:19'),(29,'ContactFileUploadUrl',1,'17','2010-11-04 11:40:11'),(30,'PaymentLogBlock',1,'20','2010-11-04 13:01:02'),(31,'ContactTagSearchBlock',1,'20','2010-11-04 13:01:21'),(32,'ContactSubTagSearchBlock',1,'20','2010-11-04 13:01:39');
/*!40000 ALTER TABLE `plugin_enable` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project` (
  `idproject` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(15) NOT NULL,
  `name` varchar(100) NOT NULL,
  `end_date_dateformat` date NOT NULL,
  `idcompany` varchar(254) NOT NULL,
  `status` varchar(15) NOT NULL DEFAULT 'open',
  `effort_estimated_hrs` float(10,2) NOT NULL,
  `is_public` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idproject`),
  UNIQUE KEY `idproject_2` (`idproject`),
  KEY `idproject` (`idproject`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_discuss`
--

DROP TABLE IF EXISTS `project_discuss`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_discuss` (
  `idproject_discuss` int(10) NOT NULL AUTO_INCREMENT,
  `idproject_task` int(10) NOT NULL,
  `idtask` int(10) NOT NULL,
  `idproject` int(10) NOT NULL,
  `discuss` text,
  `date_added` date NOT NULL,
  `document` varchar(254) NOT NULL,
  `iduser` int(10) NOT NULL DEFAULT '0',
  `drop_box_sender` varchar(100) NOT NULL,
  `priority` int(1) NOT NULL DEFAULT '0',
  `hours_work` float(10,2) NOT NULL DEFAULT '0.00',
  `discuss_edit_access` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`idproject_discuss`),
  UNIQUE KEY `idproject_discuss_2` (`idproject_discuss`),
  KEY `idproject_discuss` (`idproject_discuss`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_discuss`
--

LOCK TABLES `project_discuss` WRITE;
/*!40000 ALTER TABLE `project_discuss` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_discuss` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_sharing`
--

DROP TABLE IF EXISTS `project_sharing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_sharing` (
  `idproject_sharing` int(10) NOT NULL AUTO_INCREMENT,
  `idproject` int(15) NOT NULL,
  `iduser` int(15) NOT NULL,
  `idcoworker` int(15) NOT NULL,
  PRIMARY KEY (`idproject_sharing`),
  UNIQUE KEY `idproject_sharing_2` (`idproject_sharing`),
  KEY `idproject_sharing` (`idproject_sharing`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_sharing`
--

LOCK TABLES `project_sharing` WRITE;
/*!40000 ALTER TABLE `project_sharing` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_sharing` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_task`
--

DROP TABLE IF EXISTS `project_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_task` (
  `idproject_task` int(10) NOT NULL AUTO_INCREMENT,
  `idtask` int(15) NOT NULL,
  `idproject` int(15) NOT NULL,
  `progress` varchar(254) NOT NULL,
  `drop_box_code` int(15) NOT NULL DEFAULT '0',
  `priority` int(15) NOT NULL,
  `hrs_work_expected` float(10,2) NOT NULL,
  PRIMARY KEY (`idproject_task`),
  UNIQUE KEY `idproject_task_2` (`idproject_task`),
  KEY `idproject_task` (`idproject_task`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_task`
--

LOCK TABLES `project_task` WRITE;
/*!40000 ALTER TABLE `project_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `project_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurrent_invoice_cc`
--

DROP TABLE IF EXISTS `recurrent_invoice_cc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurrent_invoice_cc` (
  `idrecurrentinvoice` int(14) NOT NULL,
  `cc_num` tinyblob NOT NULL,
  `cc_exp_mon` char(2) NOT NULL,
  `cc_exp_year` varchar(4) NOT NULL,
  `cc_type` varchar(50) NOT NULL,
  `idrecurrent_invoice_cc` int(14) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`idrecurrent_invoice_cc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurrent_invoice_cc`
--

LOCK TABLES `recurrent_invoice_cc` WRITE;
/*!40000 ALTER TABLE `recurrent_invoice_cc` DISABLE KEYS */;
/*!40000 ALTER TABLE `recurrent_invoice_cc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recurrentinvoice`
--

DROP TABLE IF EXISTS `recurrentinvoice`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recurrentinvoice` (
  `idrecurrentinvoice` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(15) NOT NULL,
  `idinvoice` int(15) NOT NULL,
  `nextdate` date NOT NULL,
  `recurrence` int(10) NOT NULL,
  `recurrencetype` varchar(200) NOT NULL,
  PRIMARY KEY (`idrecurrentinvoice`),
  UNIQUE KEY `idrecurrentinvoice_2` (`idrecurrentinvoice`),
  KEY `idrecurrentinvoice` (`idrecurrentinvoice`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recurrentinvoice`
--

LOCK TABLES `recurrentinvoice` WRITE;
/*!40000 ALTER TABLE `recurrentinvoice` DISABLE KEYS */;
/*!40000 ALTER TABLE `recurrentinvoice` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referrer`
--

DROP TABLE IF EXISTS `referrer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referrer` (
  `idreferrer` int(10) NOT NULL AUTO_INCREMENT,
  `url` varchar(150) NOT NULL DEFAULT '',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `iduser` int(10) NOT NULL DEFAULT '0',
  `recorded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `visitor` mediumtext,
  PRIMARY KEY (`idreferrer`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referrer`
--

LOCK TABLES `referrer` WRITE;
/*!40000 ALTER TABLE `referrer` DISABLE KEYS */;
/*!40000 ALTER TABLE `referrer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reg_invoice_log`
--

DROP TABLE IF EXISTS `reg_invoice_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reg_invoice_log` (
  `idreg_invoice_log` int(10) NOT NULL AUTO_INCREMENT,
  `idinvoice` int(15) NOT NULL,
  `reg_iduser` int(15) NOT NULL,
  `iduser` int(15) NOT NULL,
  PRIMARY KEY (`idreg_invoice_log`),
  UNIQUE KEY `idreg_invoice_log_2` (`idreg_invoice_log`),
  KEY `idreg_invoice_log` (`idreg_invoice_log`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reg_invoice_log`
--

LOCK TABLES `reg_invoice_log` WRITE;
/*!40000 ALTER TABLE `reg_invoice_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `reg_invoice_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `states`
--

DROP TABLE IF EXISTS `states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `states` (
  `idstates` int(11) NOT NULL AUTO_INCREMENT,
  `name` text CHARACTER SET latin1 NOT NULL,
  `name_short` text NOT NULL,
  PRIMARY KEY (`idstates`)
) ENGINE=MyISAM AUTO_INCREMENT=1011522957 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `states`
--

LOCK TABLES `states` WRITE;
/*!40000 ALTER TABLE `states` DISABLE KEYS */;
INSERT INTO `states` VALUES (994318285,'Alaska','AK'),(994318504,'Alabama','AL'),(994318568,'Arkansas','AR'),(994318598,'American Samoa','AS'),(994318622,'Arizona','AZ'),(994318640,'California','CA'),(994318750,'Colorado','CO'),(994318777,'Connecticut','CT'),(994318908,'District of Columbia','DC'),(994318946,'Delaware','DE'),(994318997,'Florida','FL'),(994319018,'Georgia','GA'),(994319038,'Guam','GU'),(994319059,'Hawaii','HI'),(994319079,'Iowa','IA'),(994319098,'Idaho','ID'),(994319128,'Illinois','IL'),(994319149,'Indiana','IN'),(994319168,'Kansas','KS'),(994319186,'Kentucky','KY'),(994319205,'Louisiana','LA'),(994319223,'Massachusetts','MA'),(994319240,'Maryland','MD'),(994319258,'Maine','ME'),(994319278,'Michigan','MI'),(994319295,'Minnesota','MN'),(994319315,'Missouri','MO'),(994319334,'Northern Mariana Islands','MP'),(994319351,'Mississippi','MS'),(994319371,'Montana','MT'),(994319389,'North Carolina','NC'),(994319409,'North Dakota','ND'),(994319425,'Nebraska','NE'),(994319449,'New Hampshire','NH'),(994319466,'New Jersey','NJ'),(994319488,'New Mexico','NM'),(994319511,'Nevada','NV'),(994319518,'New York','NY'),(994319567,'Ohio','OH'),(994319621,'Oklahoma','OK'),(994319721,'Oregon','OR'),(994319737,'Pennsylvania','PA'),(994319753,'Puerto Rico','PR'),(994319773,'Palau','PW'),(994319791,'Rhode Island','RI'),(994319809,'South Carolina','SC'),(994319835,'Tennessee','TN'),(994319858,'Texas','TX'),(994319877,'Utah','UT'),(994319898,'Virginia','VA'),(994319918,'Virgin Island','VI'),(994319937,'Vermont','VT'),(994319964,'Washington','WA'),(994320006,'Wisconsin','WI'),(994320028,'West Virginia','WV'),(994320048,'Wyoming','WY'),(994320068,'Other - Not Listed','other'),(1011522956,'American Embassy','AE');
/*!40000 ALTER TABLE `states` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `idtag` int(10) NOT NULL AUTO_INCREMENT,
  `tag_name` varchar(200) NOT NULL,
  `iduser` int(10) NOT NULL,
  `reference_type` varchar(50) NOT NULL,
  `idreference` int(15) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`idtag`),
  KEY `ref` (`reference_type`,`idreference`),
  KEY `name` (`tag_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag`
--

LOCK TABLES `tag` WRITE;
/*!40000 ALTER TABLE `tag` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag_association`
--

DROP TABLE IF EXISTS `tag_association`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_association` (
  `idtag_association` int(10) NOT NULL AUTO_INCREMENT,
  `idtag` varchar(16) NOT NULL,
  `iduser` varchar(16) CHARACTER SET utf8 COLLATE utf8_latvian_ci NOT NULL,
  `reference_type` varchar(10) NOT NULL,
  `idreference` int(14) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`idtag_association`),
  UNIQUE KEY `idtag_association_2` (`idtag_association`),
  KEY `idtag_association` (`idtag_association`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag_association`
--

LOCK TABLES `tag_association` WRITE;
/*!40000 ALTER TABLE `tag_association` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag_association` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag_click`
--

DROP TABLE IF EXISTS `tag_click`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_click` (
  `idtag_click` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(10) NOT NULL DEFAULT '0',
  `clicked` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tag_name` varchar(200) NOT NULL,
  PRIMARY KEY (`idtag_click`),
  KEY `iduser` (`iduser`),
  KEY `tagname` (`tag_name`(30))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag_click`
--

LOCK TABLES `tag_click` WRITE;
/*!40000 ALTER TABLE `tag_click` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag_click` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tag_size`
--

DROP TABLE IF EXISTS `tag_size`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_size` (
  `idtag_click` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(10) NOT NULL DEFAULT '0',
  `tag_name` varchar(200) NOT NULL,
  `clicks` int(10) NOT NULL,
  PRIMARY KEY (`idtag_click`),
  KEY `iduser` (`iduser`),
  KEY `tagname` (`tag_name`(30))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tag_size`
--

LOCK TABLES `tag_size` WRITE;
/*!40000 ALTER TABLE `tag_size` DISABLE KEYS */;
/*!40000 ALTER TABLE `tag_size` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task` (
  `idtask` int(10) NOT NULL AUTO_INCREMENT,
  `task_description` varchar(200) NOT NULL DEFAULT '',
  `due_date` varchar(50) NOT NULL,
  `category` varchar(14) NOT NULL,
  `iduser` int(14) NOT NULL,
  `due_date_dateformat` date NOT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'open',
  `date_completed` date NOT NULL,
  `idcontact` int(14) NOT NULL DEFAULT '0',
  `from_note` int(1) NOT NULL DEFAULT '0',
  `is_sp_date_set` varchar(4) NOT NULL,
  `task_category` varchar(100) NOT NULL,
  PRIMARY KEY (`idtask`),
  UNIQUE KEY `idtask_2` (`idtask`),
  KEY `idtask` (`idtask`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task_category`
--

DROP TABLE IF EXISTS `task_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `task_category` (
  `idtask_category` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `iduser` int(14) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idtask_category`),
  UNIQUE KEY `idtask_category_2` (`idtask_category`),
  KEY `idtask_category` (`idtask_category`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task_category`
--

LOCK TABLES `task_category` WRITE;
/*!40000 ALTER TABLE `task_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temp_gmail_emails`
--

DROP TABLE IF EXISTS `temp_gmail_emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_gmail_emails` (
  `idtemp_gmail_emails` int(10) NOT NULL AUTO_INCREMENT,
  `email_address` varchar(180) NOT NULL,
  PRIMARY KEY (`idtemp_gmail_emails`),
  UNIQUE KEY `idtemp_gmail_emails_2` (`idtemp_gmail_emails`),
  KEY `idtemp_gmail_emails` (`idtemp_gmail_emails`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temp_gmail_emails`
--

LOCK TABLES `temp_gmail_emails` WRITE;
/*!40000 ALTER TABLE `temp_gmail_emails` DISABLE KEYS */;
/*!40000 ALTER TABLE `temp_gmail_emails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temp_import`
--

DROP TABLE IF EXISTS `temp_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_import` (
  `idtemp_import` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `company` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idtemp_import`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temp_import`
--

LOCK TABLES `temp_import` WRITE;
/*!40000 ALTER TABLE `temp_import` DISABLE KEYS */;
/*!40000 ALTER TABLE `temp_import` ENABLE KEYS */;
UNLOCK TABLES;

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
-- Dumping data for table `twitter_account`
--

LOCK TABLES `twitter_account` WRITE;
/*!40000 ALTER TABLE `twitter_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `twitter_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `updated_date_log`
--

DROP TABLE IF EXISTS `updated_date_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `updated_date_log` (
  `idupdated_date_log` int(10) NOT NULL AUTO_INCREMENT,
  `updatedate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `tablename` varchar(40) NOT NULL,
  `primarykeyvalue` int(10) NOT NULL,
  PRIMARY KEY (`idupdated_date_log`),
  KEY `recordid` (`tablename`,`primarykeyvalue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='record all updated records';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `updated_date_log`
--

LOCK TABLES `updated_date_log` WRITE;
/*!40000 ALTER TABLE `updated_date_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `updated_date_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `iduser` int(10) NOT NULL AUTO_INCREMENT,
  `firstname` char(40) NOT NULL DEFAULT '',
  `middlename` char(20) NOT NULL DEFAULT '',
  `lastname` char(40) NOT NULL DEFAULT '',
  `email` char(80) NOT NULL DEFAULT '',
  `phone` char(30) NOT NULL DEFAULT '',
  `company` char(40) NOT NULL DEFAULT '',
  `position` char(40) NOT NULL DEFAULT '',
  `address1` char(80) NOT NULL DEFAULT '',
  `address2` char(80) NOT NULL DEFAULT '',
  `city` char(40) NOT NULL DEFAULT '',
  `zip` char(20) NOT NULL DEFAULT '',
  `state` char(30) NOT NULL DEFAULT '',
  `country` char(40) NOT NULL DEFAULT '',
  `username` char(20) NOT NULL DEFAULT '',
  `password` char(20) NOT NULL DEFAULT '',
  `isadmin` int(5) NOT NULL DEFAULT '0',
  `regdate` date DEFAULT NULL,
  `openid` varchar(90) NOT NULL,
  `last_login` date NOT NULL,
  `drop_box_code` int(15) NOT NULL,
  `idcontact` int(10) DEFAULT '0',
  `fb_user_id` int(14) DEFAULT '0',
  `api_key` varchar(100) NOT NULL,
  `plan` varchar(200) NOT NULL,
  `status` varchar(200) NOT NULL,
  `google_openid_identity` varchar(250) NOT NULL,
  PRIMARY KEY (`iduser`),
  UNIQUE KEY `idusers` (`iduser`),
  KEY `idusers_2` (`iduser`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'default','','user','','','','','','','','','','','admin','5sDcy-Pm2OTg',0,NULL,'','0000-00-00',0,0,0,'','','active','');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_relations`
--

DROP TABLE IF EXISTS `user_relations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_relations` (
  `iduser_relations` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(14) NOT NULL,
  `idcoworker` varchar(14) NOT NULL,
  `accepted` varchar(20) NOT NULL,
  `enc_email` varchar(200) NOT NULL,
  PRIMARY KEY (`iduser_relations`),
  UNIQUE KEY `iduser_relations_2` (`iduser_relations`),
  KEY `iduser_relations` (`iduser_relations`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_relations`
--

LOCK TABLES `user_relations` WRITE;
/*!40000 ALTER TABLE `user_relations` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_relations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_settings`
--

DROP TABLE IF EXISTS `user_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_settings` (
  `iduser_settings` int(10) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(100) NOT NULL,
  `setting_value` varchar(100) NOT NULL,
  `iduser` int(14) NOT NULL,
  PRIMARY KEY (`iduser_settings`),
  UNIQUE KEY `iduser_settings_2` (`iduser_settings`),
  KEY `iduser_settings` (`iduser_settings`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_settings`
--

LOCK TABLES `user_settings` WRITE;
/*!40000 ALTER TABLE `user_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webformfields`
--

DROP TABLE IF EXISTS `webformfields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webformfields` (
  `idwebformfields` int(10) NOT NULL AUTO_INCREMENT,
  `label` varchar(60) NOT NULL,
  `name` varchar(30) NOT NULL,
  `required` varchar(1) NOT NULL,
  `class` varchar(40) NOT NULL,
  `variable` varchar(40) NOT NULL,
  `size` int(5) NOT NULL,
  `display_order` int(6) NOT NULL,
  `variable_type` varchar(20) NOT NULL,
  `field_type` varchar(50) NOT NULL,
  PRIMARY KEY (`idwebformfields`),
  UNIQUE KEY `idwebformfields_2` (`idwebformfields`),
  KEY `idwebformfields` (`idwebformfields`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webformfields`
--

LOCK TABLES `webformfields` WRITE;
/*!40000 ALTER TABLE `webformfields` DISABLE KEYS */;
INSERT INTO `webformfields` VALUES (1,'First Name','firstname','n','Contact','firstname',40,10,'','FieldTypeChar'),(2,'Last Name','lastname','','Contact','lastname',40,20,'','FieldTypeChar'),(3,'Company','company','','Contact','company',40,30,'','FieldTypeChar'),(4,'Position','position','','Contact','position',40,40,'','FieldTypeChar'),(5,'Work Email','emailwork','n','ContactEmail','email_address',40,50,'Work','FieldTypeChar'),(6,'Home Email','emailhome','n','ContactEmail','email_address',40,60,'Home','FieldTypeChar'),(7,'Other Email','emailother','','ContactEmail','email_address',40,70,'Other','FieldTypeChar'),(8,'Work Phone','phonework','','ContactPhone','phone_number',20,80,'Work','FieldTypeChar'),(9,'Home Phone','phonehome','','ContactPhone','phone_number',20,90,'Home','FieldTypeChar'),(10,'Mobile Phone','phonemobile','','ContactPhone','phone_number',20,100,'Mobile','FieldTypeChar'),(11,'Fax Number','phonefax','','ContactPhone','phone_number',20,110,'Fax','FieldTypeChar'),(12,'Other Phone','phoneother','','ContactPhone','phone_number',20,120,'Other','FieldTypeChar'),(13,'Company website','websitecompany','','ContactWebsite','website',40,130,'Company','FieldTypeChar'),(14,'Personal Webiste','websitepersonal','n','ContactWebsite','website',40,150,'Personal','FieldTypeChar'),(15,'Blog','websiteblog','n','ContactWebsite','website',40,140,'Blog','FieldTypeChar'),(16,'Twitter','websitetwitter','n','ContactWebsite','website',40,160,'Twitter','FieldTypeChar'),(17,'Linkedin Profile','websitelinkedin','n','ContactWebsite','website',40,170,'Linkedin','FieldTypeChar'),(18,'Street address','addressstreet','n','ContactAddress','street',40,190,'Work','FieldTypeChar'),(19,'City','addresscity','n','ContactAddress','city',40,200,'Work','FieldTypeChar'),(20,'Zip','addresszip','n','ContactAddress','zipcode',40,210,'Work','FieldTypeChar'),(21,'State','addressstate','n','ContactAddress','state',40,220,'Work','FieldTypeChar'),(22,'Country','addresscountry','n','ContactAddress','country',40,240,'Work','FieldTypeChar'),(23,'Notes','contactnotes','n','ContactNotes','note',60,260,'','FieldTypeText');
/*!40000 ALTER TABLE `webformfields` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webformuser`
--

DROP TABLE IF EXISTS `webformuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webformuser` (
  `idwebformuser` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL,
  `description` varchar(254) NOT NULL,
  `tags` varchar(200) NOT NULL,
  `iduser` int(15) NOT NULL,
  `urlnext` varchar(200) NOT NULL,
  `email_alert` varchar(1) NOT NULL,
  PRIMARY KEY (`idwebformuser`),
  UNIQUE KEY `idwebformuser_2` (`idwebformuser`),
  KEY `idwebformuser` (`idwebformuser`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webformuser`
--

LOCK TABLES `webformuser` WRITE;
/*!40000 ALTER TABLE `webformuser` DISABLE KEYS */;
/*!40000 ALTER TABLE `webformuser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `webformuserfield`
--

DROP TABLE IF EXISTS `webformuserfield`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webformuserfield` (
  `idwebformuserfield` int(10) NOT NULL AUTO_INCREMENT,
  `idwebformuser` int(10) NOT NULL,
  `name` varchar(60) NOT NULL,
  `idwebformfields` int(10) NOT NULL,
  `required` varchar(1) NOT NULL,
  `size` varchar(10) DEFAULT NULL,
  `label` varchar(200) NOT NULL,
  PRIMARY KEY (`idwebformuserfield`),
  UNIQUE KEY `idwebformuserfield_2` (`idwebformuserfield`),
  KEY `idwebformuserfield` (`idwebformuserfield`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `webformuserfield`
--

LOCK TABLES `webformuserfield` WRITE;
/*!40000 ALTER TABLE `webformuserfield` DISABLE KEYS */;
/*!40000 ALTER TABLE `webformuserfield` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `workfeed`
--

DROP TABLE IF EXISTS `workfeed`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `workfeed` (
  `idworkfeed` int(10) NOT NULL AUTO_INCREMENT,
  `iduser` int(14) NOT NULL,
  `date_added` datetime DEFAULT NULL,
  `feed_data` text NOT NULL,
  `feed_type` varchar(50) NOT NULL,
  PRIMARY KEY (`idworkfeed`),
  UNIQUE KEY `idworkfeed_2` (`idworkfeed`),
  KEY `idworkfeed` (`idworkfeed`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `workfeed`
--

LOCK TABLES `workfeed` WRITE;
/*!40000 ALTER TABLE `workfeed` DISABLE KEYS */;
/*!40000 ALTER TABLE `workfeed` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-01-15 17:41:36
