-- MySQL dump 10.13  Distrib 5.1.54, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: ofuzdev
-- ------------------------------------------------------
-- Server version	5.1.54-1ubuntu4

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
-- Table structure for table `team`
--

DROP TABLE IF EXISTS `team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `idteam` int(11) NOT NULL AUTO_INCREMENT,
  `iduser` int(11) DEFAULT NULL,
  `team_name` varchar(200) DEFAULT NULL,
  `auto_share` varchar(3) DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  PRIMARY KEY (`idteam`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `team_users`
--

DROP TABLE IF EXISTS `team_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_users` (
  `idteam_users` int(11) NOT NULL AUTO_INCREMENT,
  `idteam` int(11) NOT NULL,
  `idco_worker` int(11) NOT NULL,
  PRIMARY KEY (`idteam_users`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;


--
-- Table structure for table `contact_team`
--

DROP TABLE IF EXISTS `contact_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_team` (
  `idcontact_team` int(11) NOT NULL AUTO_INCREMENT,
  `idcontact` int(11) NOT NULL,
  `idteam` int(11) NOT NULL,
  `idcoworker` int(11) DEFAULT NULL,
  PRIMARY KEY (`idcontact_team`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

ALTER TABLE task ADD COLUMN priority int NOT NULL;

CREATE TABLE `user_profile`  (iduser_profile INT(10) not null AUTO_INCREMENT, PRIMARY KEY (iduser_profile), INDEX (iduser_profile), UNIQUE (iduser_profile));
ALTER TABLE `user_profile` ADD `logo` VARCHAR(200) not null;
ALTER TABLE `user_profile` ADD `job_type` VARCHAR(100) not null;
ALTER TABLE `user_profile` ADD `job_description` TEXT not null;
alter table `user_profile` add `iduser` int(10) NOT NULL;


CREATE TABLE leankit_credentials (
idleankit_credentials INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
iduser INT NOT NULL,
username VARCHAR(100),
password VARCHAR(20),
created_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE `stripe_details` (
`idstripe_details` INT( 100 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`iduser` INT( 50 ) NOT NULL ,
`idcontact` INT( 50 ) NOT NULL ,
`stripe_token` VARCHAR( 200 ) NOT NULL ,
`createdate` TIMESTAMP NOT NULL
) ENGINE = MYISAM ;




