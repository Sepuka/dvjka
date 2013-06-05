-- MySQL dump 10.13  Distrib 5.5.28, for Linux (i686)
--
-- Host: localhost    Database: freelance
-- ------------------------------------------------------
-- Server version	5.5.28-log

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
-- Table structure for table `DVJK_payments`
--

DROP TABLE IF EXISTS `DVJK_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DVJK_payments` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Sender_id` bigint(20) unsigned NOT NULL,
  `Dest_id` bigint(20) unsigned NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `DateTimeCreate` datetime NOT NULL,
  `Complete` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Sender_id` (`Sender_id`),
  KEY `DateTimeCreate` (`DateTimeCreate`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DVJK_payments`
--

LOCK TABLES `DVJK_payments` WRITE;
/*!40000 ALTER TABLE `DVJK_payments` DISABLE KEYS */;
INSERT INTO `DVJK_payments` VALUES (1,6,7,10000.00,'2013-06-05 11:20:57',1),(2,7,6,100.00,'2013-06-05 17:36:30',1),(3,6,7,100.00,'2013-06-05 18:08:51',1),(4,6,7,100.00,'2013-06-05 18:12:10',1);
/*!40000 ALTER TABLE `DVJK_payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DVJK_users`
--

DROP TABLE IF EXISTS `DVJK_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DVJK_users` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Phone` char(10) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `DateTimeCreate` datetime NOT NULL,
  `Enabled` tinyint(4) NOT NULL DEFAULT '1',
  `Ref` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Phone` (`Phone`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DVJK_users`
--

LOCK TABLES `DVJK_users` WRITE;
/*!40000 ALTER TABLE `DVJK_users` DISABLE KEYS */;
INSERT INTO `DVJK_users` VALUES (6,'9091112233','58704563','2013-06-03 15:29:52',1,NULL),(7,'9312375828','88060179','2013-06-04 12:34:58',1,6);
/*!40000 ALTER TABLE `DVJK_users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-06-05 18:15:48
