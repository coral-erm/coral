/*
READ THIS! ##############################################
If you need to regenerate this because
- The Coral setup changed.
Or
- You need data that will be used in *most* of the tests
  Keep the test DB a small as possible, as it will be loaded before *every* test

Here are the instructions:
1. Do a clean Coral install
   With an admin user "coral_test" with a passphrase "coral_test"
2. Login
3. Create a resource type
4. Create a resource
5. Dump the DB with
   mysqldump -u MY_USER -p --databases coral_auth_prod coral_licensing_prod coral_management_prod coral_organizations_prod coral_reports_prod coral_resources_prod coral_usage_prod  > dump.sql
   You should need to use your root DB user as your coral DB user won't be authorized to LOCK TABLE
6. Remove the CREATE DATABASE from the dump
7. Update the CORALSessionID in tests/_support/Helper/Acceptance.php with the
   value from the dump in the Session table. Or set it back to the one in
   Acceptance.php
8. Replace the DB name with the test ones. See TEST.md
9. Include this comment! (and make sure it's up to date)
*/



-- MySQL dump 10.16  Distrib 10.1.17-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: coral_auth_test
-- ------------------------------------------------------
-- Server version	10.1.17-MariaDB

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
-- Current Database: `coral_auth_test`
--

USE `coral_auth_test`;

--
-- Table structure for table `Session`
--

DROP TABLE IF EXISTS `Session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Session` (
  `sessionID` varchar(100) NOT NULL DEFAULT '',
  `loginID` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sessionID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Session`
--

LOCK TABLES `Session` WRITE;
/*!40000 ALTER TABLE `Session` DISABLE KEYS */;
INSERT INTO `Session` VALUES ('bNWUrFmjzDtoyxXSyxlwLMROC5W5LwvnAH7sMkRBnBqcyDum1VZCiqRlmngyaRbbYZJl9anncTFQX03PMSSu9jWlN2ZoJ1FiQPJQ','coral_test','2016-09-09 15:53:20');
/*!40000 ALTER TABLE `Session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `loginID` varchar(50) NOT NULL,
  `password` varchar(250) DEFAULT NULL,
  `passwordPrefix` varchar(50) DEFAULT NULL,
  `adminInd` varchar(1) DEFAULT 'N',
  PRIMARY KEY (`loginID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES ('coral_test','302a5ecfa31bf5d84bd3cb698b4582eb3f037c11c4d98c2bc2e230a7dbf0acb5577564aecf53608547477466a418e74802643393852e9d7c67fcc9598b7c0d9c','t3tC9fC8GZyrDpzHuGyaxQ0RWvEG72vrvftvAvuGLixPN','Y');
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `coral_licensing_test`
--

USE `coral_licensing_test`;

--
-- Table structure for table `Attachment`
--

DROP TABLE IF EXISTS `Attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Attachment` (
  `attachmentID` int(10) NOT NULL AUTO_INCREMENT,
  `licenseID` int(10) DEFAULT NULL,
  `sentDate` date DEFAULT NULL,
  `attachmentText` text,
  PRIMARY KEY (`attachmentID`) USING BTREE,
  KEY `licenseID` (`licenseID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Attachment`
--

LOCK TABLES `Attachment` WRITE;
/*!40000 ALTER TABLE `Attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `Attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AttachmentFile`
--

DROP TABLE IF EXISTS `AttachmentFile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AttachmentFile` (
  `attachmentFileID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attachmentID` int(10) unsigned NOT NULL,
  `attachmentURL` varchar(200) NOT NULL,
  PRIMARY KEY (`attachmentFileID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AttachmentFile`
--

LOCK TABLES `AttachmentFile` WRITE;
/*!40000 ALTER TABLE `AttachmentFile` DISABLE KEYS */;
/*!40000 ALTER TABLE `AttachmentFile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CalendarSettings`
--

DROP TABLE IF EXISTS `CalendarSettings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CalendarSettings` (
  `calendarSettingsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`calendarSettingsID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CalendarSettings`
--

LOCK TABLES `CalendarSettings` WRITE;
/*!40000 ALTER TABLE `CalendarSettings` DISABLE KEYS */;
INSERT INTO `CalendarSettings` VALUES (1,'Days Before Subscription End','730'),(2,'Days After Subscription End','90'),(3,'Resource Type(s)','1'),(4,'Authorized Site(s)','1');
/*!40000 ALTER TABLE `CalendarSettings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Consortium`
--

DROP TABLE IF EXISTS `Consortium`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Consortium` (
  `consortiumID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`consortiumID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Consortium`
--

LOCK TABLES `Consortium` WRITE;
/*!40000 ALTER TABLE `Consortium` DISABLE KEYS */;
/*!40000 ALTER TABLE `Consortium` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Document`
--

DROP TABLE IF EXISTS `Document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Document` (
  `documentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  `documentTypeID` int(10) unsigned NOT NULL,
  `licenseID` int(10) unsigned NOT NULL,
  `effectiveDate` date DEFAULT NULL,
  `expirationDate` date DEFAULT NULL,
  `documentURL` varchar(200) DEFAULT NULL,
  `parentDocumentID` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`documentID`),
  KEY `licenseID` (`licenseID`),
  KEY `documentTypeID` (`documentTypeID`),
  KEY `parentDocumentID` (`parentDocumentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Document`
--

LOCK TABLES `Document` WRITE;
/*!40000 ALTER TABLE `Document` DISABLE KEYS */;
/*!40000 ALTER TABLE `Document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DocumentType`
--

DROP TABLE IF EXISTS `DocumentType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DocumentType` (
  `documentTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`documentTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DocumentType`
--

LOCK TABLES `DocumentType` WRITE;
/*!40000 ALTER TABLE `DocumentType` DISABLE KEYS */;
INSERT INTO `DocumentType` VALUES (1,'SERU'),(2,'Internal Acknowledgment'),(3,'Agreement'),(4,'Countersigned Agreement'),(5,'Amendment'),(6,'Consortium Authorization Statement'),(7,'Order Form');
/*!40000 ALTER TABLE `DocumentType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Expression`
--

DROP TABLE IF EXISTS `Expression`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Expression` (
  `expressionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documentID` int(10) unsigned NOT NULL,
  `expressionTypeID` int(10) unsigned NOT NULL,
  `documentText` text,
  `simplifiedText` text,
  `lastUpdateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `productionUseInd` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`expressionID`),
  KEY `documentID` (`documentID`),
  KEY `expressionTypeID` (`expressionTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Expression`
--

LOCK TABLES `Expression` WRITE;
/*!40000 ALTER TABLE `Expression` DISABLE KEYS */;
/*!40000 ALTER TABLE `Expression` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExpressionNote`
--

DROP TABLE IF EXISTS `ExpressionNote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExpressionNote` (
  `expressionNoteID` int(10) NOT NULL AUTO_INCREMENT,
  `expressionID` int(10) DEFAULT NULL,
  `note` varchar(2000) DEFAULT NULL,
  `displayOrderSeqNumber` int(10) DEFAULT NULL,
  `lastUpdateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expressionNoteID`),
  KEY `expressionID` (`expressionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExpressionNote`
--

LOCK TABLES `ExpressionNote` WRITE;
/*!40000 ALTER TABLE `ExpressionNote` DISABLE KEYS */;
/*!40000 ALTER TABLE `ExpressionNote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExpressionQualifierProfile`
--

DROP TABLE IF EXISTS `ExpressionQualifierProfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExpressionQualifierProfile` (
  `expressionID` int(10) unsigned NOT NULL,
  `qualifierID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`expressionID`,`qualifierID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExpressionQualifierProfile`
--

LOCK TABLES `ExpressionQualifierProfile` WRITE;
/*!40000 ALTER TABLE `ExpressionQualifierProfile` DISABLE KEYS */;
/*!40000 ALTER TABLE `ExpressionQualifierProfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExpressionType`
--

DROP TABLE IF EXISTS `ExpressionType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExpressionType` (
  `expressionTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  `noteType` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`expressionTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExpressionType`
--

LOCK TABLES `ExpressionType` WRITE;
/*!40000 ALTER TABLE `ExpressionType` DISABLE KEYS */;
INSERT INTO `ExpressionType` VALUES (1,'Authorized Users','Internal'),(2,'Interlibrary Loan','Display'),(3,'Coursepacks','Display'),(4,'eReserves','Display'),(5,'Post Cancellation Access','Internal'),(6,'General Notes','Internal'),(7,'Jurisdiction (Choice of Forum)','Internal'),(8,'Third Party Archiving','Internal'),(9,'Confidentiality Clause','Internal'),(10,'Multi-year Term','Internal');
/*!40000 ALTER TABLE `ExpressionType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `License`
--

DROP TABLE IF EXISTS `License`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `License` (
  `licenseID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `consortiumID` int(10) unsigned DEFAULT NULL,
  `organizationID` int(10) unsigned DEFAULT NULL,
  `shortName` tinytext NOT NULL,
  `statusID` int(10) unsigned DEFAULT NULL,
  `statusDate` datetime DEFAULT NULL,
  `createDate` datetime DEFAULT NULL,
  PRIMARY KEY (`licenseID`),
  KEY `organizationID` (`organizationID`),
  KEY `consortiumID` (`consortiumID`),
  KEY `statusID` (`statusID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `License`
--

LOCK TABLES `License` WRITE;
/*!40000 ALTER TABLE `License` DISABLE KEYS */;
INSERT INTO `License` VALUES (5, NULL, 505, 'My License', NULL, NULL, '2016-09-30 23:23:59');
/*!40000 ALTER TABLE `License` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Organization`
--

DROP TABLE IF EXISTS `Organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Organization` (
  `organizationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`organizationID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=289 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Organization`
--

LOCK TABLES `Organization` WRITE;
/*!40000 ALTER TABLE `Organization` DISABLE KEYS */;
INSERT INTO `Organization` VALUES (1,'Accessible Archives Inc'),(2,'ACCU Weather Sales and Services, Inc'),(3,'Adam Matthew Digital Ltd'),(4,'Agricultural History Society'),(5,'Agricultural Institute of Canada'),(6,'AICPA'),(7,'Akademiai Kiado'),(8,'Albert C. Muller'),(9,'Alexander Street Press, LLC'),(10,'Allen Press'),(11,'Alliance for Children and Families'),(12,'American Academy of Religion'),(13,'American Association for Cancer Research (AACR)'),(14,'American Association for the Advancement of Science (AAAS)'),(15,'American Association of Immunologists, Inc.'),(16,'American Concrete Institute (ACI)'),(17,'American Council of Learned Societies (ACLS)'),(18,'American Counseling Association'),(19,'American Economic Association (AEA)'),(20,'American Fisheries Society'),(21,'American Geophysical Union'),(22,'American Insitute of Physics (AIP)'),(23,'American Institute of Aeronautics and Astronautics (AIAA)'),(24,'American Library Association (ALA)'),(25,'American Mathematical Society (AMS)'),(26,'American Medical Association (AMA)'),(27,'American Meteorological Society (AMS)'),(28,'American Physical Society (APS)'),(29,'American Physiological Society'),(30,'American Phytopathological Society'),(31,'American Psychiatric Publishing'),(32,'American Psychological Association (APA)'),(33,'American Society for Cell Biology'),(34,'American Society for Clinical Investigation'),(35,'American Society for Horticultural Science'),(36,'American Society for Nutrition'),(37,'American Society for Testing and Materials (ASTM)'),(38,'American Society of Agronomy'),(39,'American Society of Civil Engineers (ASCE)'),(40,'American Society of Limnology and Oceanography (ASLO)'),(41,'American Society of Plant Biologists'),(42,'American Society of Tropical Medicine and Hygiene'),(43,'American Statistical Association'),(44,'Ammons Scientific Limited'),(45,'Annual Reviews'),(46,'Antiquity Publications Limited'),(47,'Applied Probability Trust'),(48,'Army Times Publishing Company'),(49,'ARTstor Inc'),(50,'Asempa Limited'),(51,'Association of Research Libraries (ARL)'),(52,'Atypon Systems Inc'),(53,'Augustine Institute'),(54,'Barkhuis Publishing'),(55,'Begell House, Inc'),(56,'Beilstein'),(57,'Belser Wissenschaftlicher Dienst Ltd'),(58,'Berg Publishers'),(59,'Berghahn Books'),(60,'Berkeley Electronic Press'),(61,'BIGresearch LLC'),(62,'BioMed Central'),(63,'BioOne'),(64,'Blackwell Publishing'),(65,'BMJ Publishing Group Limited'),(66,'Boopsie, INC.'),(67,'Botanical Society of America'),(68,'Boyd Printing'),(69,'Brepols Publishers'),(70,'Brill'),(71,'Bulletin of the Atomic Scientists'),(72,'Bureau of National Affairs, Inc'),(73,'Business Monitor International'),(74,'CABI Publishing'),(75,'Cambridge Crystallographic Data Centre'),(76,'Cambridge Scientific Abstracts'),(77,'Cambridge University Press'),(78,'Canadian Association of African Studies'),(79,'Canadian Mathematical Society'),(80,'Carbon Disclosure Project'),(81,'CareerShift LLC'),(82,'CCH Incorporated'),(83,'Centro de Investigaciones Sociologicas'),(84,'Chemical Abstracts Service (CAS)'),(85,'Chiniquy Collection'),(86,'Chorus America'),(87,'Chronicle of Higher Education'),(88,'Colegio de Mexico'),(89,'College Art Association'),(90,'Company of Biologists Ltd'),(91,'Competitive Media Reporting, LLC (TNS Media Intelligence TNSMI)'),(92,'Consejo Superior de Investigaciones Cientificas (CSIC)'),(93,'Consumer Electronics Association'),(94,'Cornell University Library'),(95,'Corporacion Latinobarometro'),(96,'Corporation for National Research Initiatives (CNRI)'),(97,'CQ Press'),(98,'CSIRO Publishing'),(99,'Current History, Inc'),(100,'Dialog'),(101,'Dialogue Foundation'),(102,'Digital Distributed Community Archive'),(103,'Digital Heritage Publishing Limited'),(104,'Duke University Press'),(105,'Dun and Bradstreet Inc'),(106,'Dunstans Publishing Ltd'),(107,'East View Information Services'),(108,'EBSCO'),(109,'Ecological Society of America'),(110,'Edinburgh University Press'),(111,'EDP Sciences'),(112,'Elsevier'),(113,'Encyclopaedia Britannica Online'),(114,'Endocrine Society'),(115,'Entomological Society of Canada'),(116,'Equinox Publishing Ltd'),(117,'European Mathematical Society Publishing House'),(118,'European Society of Endocrinology'),(119,'Evolutionary Ecology Ltd'),(120,'ExLibris'),(121,'Experian Marketing Solutions, Inc.'),(122,'FamilyLink.com, Inc.'),(123,'FamilyLink.com, Inc.'),(124,'Faulkner Information Services'),(125,'Federation of American Societies for Experimental Biology'),(126,'Forrester Research, Inc'),(127,'Franz Steiner Verlag'),(128,'Genetics Society of America'),(129,'Geographic Research, Inc'),(130,'GeoScienceWorld'),(131,'Global Science Press'),(132,'Grove Dictionaries, Inc'),(133,'GuideStar USA, Inc'),(134,'H.W. Wilson Company'),(135,'H1 Base, Inc'),(136,'Hans Zell Publishing'),(137,'Haworth Press'),(138,'Heldref Publications'),(139,'HighWire Press'),(140,'Histochemical Society'),(141,'Human Kinetics Inc.'),(142,'IBISWorld USA'),(143,'Idea Group Inc'),(144,'IEEE'),(145,'Incisive Media Ltd'),(146,'Indiana University Mathematics Journal'),(147,'Informa Healthcare USA, Inc'),(148,'Information Resources, Inc'),(149,'INFORMS'),(150,'Ingentaconnect'),(151,'Institute of Mathematics of the Polish Academy of Sciences'),(152,'Institute of Physics (IOP)'),(153,'Institution of Engineering and Technology (IET)'),(154,'Institutional Shareholder Services Inc'),(155,'InteLex'),(156,'Intellect'),(157,'Intelligence Research Limited'),(158,'International Press'),(159,'IOS Press'),(160,'IPA Source, LLC'),(161,'Irish Newspaper Archives Ltd'),(162,'ITHAKA'),(163,'IVES Group, Inc'),(164,'Japan Focus'),(165,'John Benjamins Publishing Company'),(166,'JSTOR'),(167,'Karger'),(168,'Keesings Worldwide, LLC'),(169,'KLD Research and Analytics Inc'),(170,'Landes Bioscience'),(171,'LexisNexis'),(172,'Librairie Droz'),(173,'Library of Congress, Cataloging Distribution Service'),(174,'Lipper Inc'),(175,'Liverpool University Press'),(176,'Lord Music Reference Inc'),(177,'M.E. Sharpe, Inc'),(178,'Manchester University Press'),(179,'Marine Biological Laboratory'),(180,'MarketResearch.com, Inc'),(181,'Marquis Who\'s Who LLC'),(182,'Mary Ann Liebert, Inc'),(183,'Massachusetts Medical Society'),(184,'Mathematical Sciences Publishers'),(185,'Mediamark Research and Intelligence, LLC'),(186,'Mergent, Inc'),(187,'Metropolitan Opera'),(188,'Mintel International Group Limited'),(189,'MIT Press'),(190,'MIT'),(191,'Morningstar Inc.'),(192,'National Academy of Sciences'),(193,'National Gallery Company Ltd'),(194,'National Research Council of Canada'),(195,'Nature Publishing Group'),(196,'Naxos Digital Services Limited'),(197,'Neilson Journals Publishing'),(198,'New York Review of Books'),(199,'NewsBank, Inc'),(200,'OCLC'),(201,'Otto Harrassowitz'),(202,'Ovid'),(203,'Oxford Centre of Hebrew and Jewish Studies'),(204,'Oxford University Press'),(205,'Paradigm Publishers'),(206,'Paratext'),(207,'Peeters Publishers'),(208,'Philosophy Documentation Center'),(209,'Portland Press Limited'),(210,'Preservation Technologies LP'),(211,'Project Muse'),(212,'ProQuest LLC'),(213,'Psychoanalytic Electronic Publishing Inc'),(214,'R.R. Bowker'),(215,'Religious and Theological Abstracts, Inc'),(216,'Reuters Loan Pricing Corporation'),(217,'Risk Management Association (RMA)'),(218,'Rivista di Studi italiani'),(219,'Robert Blakemore'),(220,'Rockefeller University Press'),(221,'Roper Center for Public Opinion Research'),(222,'Royal Society of Chemistry'),(223,'Royal Society of London'),(224,'SAGE Publications'),(225,'Scholarly Digital Editions'),(226,'Seminario Matematico of the University of Padua'),(227,'Simmons Market Research Bureau Inc'),(228,'SISMEL - Edizioni del Galluzzo'),(229,'Social Explorer'),(230,'Societe Mathematique de France'),(231,'Society for Endocrinology'),(232,'Society for Experimental Biology and Medicine'),(233,'Society for General Microbiology'),(234,'Society for Industrial and Applied Mathematics (SIAM)'),(235,'Society for Leukocyte Biology'),(236,'Society for Neuroscience'),(237,'Society for Reproduction and Fertility'),(238,'Society of Antiquaries of Scotland'),(239,'Society of Environmental Toxicology and Chemistry'),(240,'SPIE'),(241,'Springer'),(242,'Standard and Poor\'s'),(243,'Stanford University'),(244,'Swank Motion Pictures, Inc'),(245,'Swiss Chemical Society'),(246,'Tablet Publishing (London)'),(247,'Taylor and Francis'),(248,'Teachers College Record'),(249,'Terra Scientific Publishing Company'),(250,'Tetrad Computer Applications Inc'),(251,'The Academy of the Hebrew Language'),(252,'Thesaurus Linguae Graecae'),(253,'Thomas Telford Ltd'),(254,'Thomson Financial Inc'),(255,'Thomson Gale'),(256,'Thomson RIA'),(257,'Thomson Scientific Inc. (Institute for Scientific Information, Inc.)'),(258,'Trans Tech Publications'),(259,'Transportation Research Board'),(260,'U.S. Department of Commerce'),(261,'UCLA Chicano Studies Research Center Press'),(262,'University of Barcelona'),(263,'University of Buckingham Press'),(264,'University of California Press'),(265,'University of Chicago Press'),(266,'University of Houston Department of Mathematics'),(267,'University of Illinois Press'),(268,'University of Iowa'),(269,'University of Pittsburgh'),(270,'University of Toronto Press Inc'),(271,'University of Toronto'),(272,'University of Virginia Press'),(273,'University of Wisconsin Press'),(274,'Universum USA'),(275,'Uniworld Business Publications, Inc'),(276,'Value Line, Inc'),(277,'Vanderbilt University'),(278,'Vault, Inc'),(279,'Verlag C.H. Beck'),(280,'Verlag der Zeitschrift fur Naturforschung '),(281,'W.S. Maney and Son Ltd'),(282,'Walter de Gruyter'),(283,'White Horse Press'),(284,'Wiley'),(285,'World Scientific'),(286,'World Trade Press'),(287,'Worldwatch Institute'),(288,'Yankelovich Inc');
/*!40000 ALTER TABLE `Organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Privilege`
--

DROP TABLE IF EXISTS `Privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Privilege` (
  `privilegeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`privilegeID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Privilege`
--

LOCK TABLES `Privilege` WRITE;
/*!40000 ALTER TABLE `Privilege` DISABLE KEYS */;
INSERT INTO `Privilege` VALUES (1,'admin'),(2,'add/edit'),(3,'view only'),(4,'restricted');
/*!40000 ALTER TABLE `Privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Qualifier`
--

DROP TABLE IF EXISTS `Qualifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Qualifier` (
  `qualifierID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `expressionTypeID` int(10) unsigned NOT NULL,
  `shortName` varchar(45) NOT NULL,
  PRIMARY KEY (`qualifierID`),
  KEY `expressionTypeID` (`expressionTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Qualifier`
--

LOCK TABLES `Qualifier` WRITE;
/*!40000 ALTER TABLE `Qualifier` DISABLE KEYS */;
INSERT INTO `Qualifier` VALUES (1,2,'Not Clear'),(2,2,'Not Reviewed'),(3,2,'Prohibited'),(4,2,'Permitted'),(5,3,'Not Clear'),(6,3,'Not Reviewed'),(7,3,'Prohibited'),(8,3,'Permitted');
/*!40000 ALTER TABLE `Qualifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SFXProvider`
--

DROP TABLE IF EXISTS `SFXProvider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SFXProvider` (
  `sfxProviderID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documentID` int(10) unsigned NOT NULL,
  `shortName` varchar(245) NOT NULL,
  PRIMARY KEY (`sfxProviderID`),
  KEY `documentID` (`documentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SFXProvider`
--

LOCK TABLES `SFXProvider` WRITE;
/*!40000 ALTER TABLE `SFXProvider` DISABLE KEYS */;
/*!40000 ALTER TABLE `SFXProvider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Signature`
--

DROP TABLE IF EXISTS `Signature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Signature` (
  `signatureID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documentID` int(10) unsigned NOT NULL,
  `signatureTypeID` int(10) unsigned NOT NULL,
  `signatureDate` date DEFAULT NULL,
  `signerName` tinytext,
  PRIMARY KEY (`signatureID`),
  KEY `documentID` (`documentID`),
  KEY `signatureTypeID` (`signatureTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Signature`
--

LOCK TABLES `Signature` WRITE;
/*!40000 ALTER TABLE `Signature` DISABLE KEYS */;
/*!40000 ALTER TABLE `Signature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SignatureType`
--

DROP TABLE IF EXISTS `SignatureType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SignatureType` (
  `signatureTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`signatureTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SignatureType`
--

LOCK TABLES `SignatureType` WRITE;
/*!40000 ALTER TABLE `SignatureType` DISABLE KEYS */;
INSERT INTO `SignatureType` VALUES (1,'Agent'),(2,'Consortium'),(3,'Internal'),(4,'Provider');
/*!40000 ALTER TABLE `SignatureType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Status`
--

DROP TABLE IF EXISTS `Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Status` (
  `statusID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) NOT NULL,
  PRIMARY KEY (`statusID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Status`
--

LOCK TABLES `Status` WRITE;
/*!40000 ALTER TABLE `Status` DISABLE KEYS */;
INSERT INTO `Status` VALUES (1,'Awaiting Document'),(2,'Complete'),(3,'Document Only'),(4,'Editing Expressions'),(5,'NLR');
/*!40000 ALTER TABLE `Status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `loginID` varchar(50) NOT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `privilegeID` int(10) unsigned DEFAULT NULL,
  `emailAddressForTermsTool` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`loginID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES ('coral_test',NULL,NULL,1,NULL);
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `coral_management_test`
--

USE `coral_management_test`;

--
-- Table structure for table `Attachment`
--

DROP TABLE IF EXISTS `Attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Attachment` (
  `attachmentID` int(10) NOT NULL AUTO_INCREMENT,
  `licenseID` int(10) DEFAULT NULL,
  `sentDate` date DEFAULT NULL,
  `attachmentText` text,
  PRIMARY KEY (`attachmentID`),
  KEY `licenseID` (`licenseID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Attachment`
--

LOCK TABLES `Attachment` WRITE;
/*!40000 ALTER TABLE `Attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `Attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AttachmentFile`
--

DROP TABLE IF EXISTS `AttachmentFile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AttachmentFile` (
  `attachmentFileID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `attachmentID` int(10) unsigned NOT NULL,
  `attachmentURL` varchar(200) NOT NULL,
  PRIMARY KEY (`attachmentFileID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AttachmentFile`
--

LOCK TABLES `AttachmentFile` WRITE;
/*!40000 ALTER TABLE `AttachmentFile` DISABLE KEYS */;
/*!40000 ALTER TABLE `AttachmentFile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Consortium`
--

DROP TABLE IF EXISTS `Consortium`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Consortium` (
  `consortiumID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`consortiumID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Consortium`
--

LOCK TABLES `Consortium` WRITE;
/*!40000 ALTER TABLE `Consortium` DISABLE KEYS */;
INSERT INTO `Consortium` VALUES (1,'CORAL Documentation');
/*!40000 ALTER TABLE `Consortium` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Document`
--

DROP TABLE IF EXISTS `Document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Document` (
  `documentID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  `documentTypeID` int(10) unsigned NOT NULL,
  `licenseID` int(10) unsigned NOT NULL,
  `effectiveDate` date DEFAULT NULL,
  `expirationDate` date DEFAULT NULL,
  `documentURL` varchar(200) DEFAULT NULL,
  `parentDocumentID` int(10) unsigned DEFAULT NULL,
  `description` tinytext,
  `revisionDate` date DEFAULT NULL,
  PRIMARY KEY (`documentID`),
  KEY `licenseID` (`licenseID`),
  KEY `documentTypeID` (`documentTypeID`),
  KEY `parentDocumentID` (`parentDocumentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Document`
--

LOCK TABLES `Document` WRITE;
/*!40000 ALTER TABLE `Document` DISABLE KEYS */;
INSERT INTO `document` VALUES (1, 'Filler Doc', 1, 1, '2016-10-03', NULL, 'filler_doc.txt', NULL, NULL, '2016-10-03');
/*!40000 ALTER TABLE `Document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DocumentNote`
--

DROP TABLE IF EXISTS `DocumentNote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DocumentNote` (
  `documentNoteID` int(11) NOT NULL AUTO_INCREMENT,
  `licenseID` int(11) NOT NULL,
  `documentID` int(11) DEFAULT '0',
  `documentNoteTypeID` int(11) NOT NULL,
  `body` text NOT NULL,
  `createDate` datetime NOT NULL,
  PRIMARY KEY (`documentNoteID`),
  KEY `licenseID` (`licenseID`,`documentNoteTypeID`),
  KEY `documentID` (`documentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DocumentNote`
--

LOCK TABLES `DocumentNote` WRITE;
/*!40000 ALTER TABLE `DocumentNote` DISABLE KEYS */;
/*!40000 ALTER TABLE `DocumentNote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DocumentNoteType`
--

DROP TABLE IF EXISTS `DocumentNoteType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DocumentNoteType` (
  `documentNoteTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(60) NOT NULL,
  PRIMARY KEY (`documentNoteTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DocumentNoteType`
--

LOCK TABLES `DocumentNoteType` WRITE;
/*!40000 ALTER TABLE `DocumentNoteType` DISABLE KEYS */;
INSERT INTO `DocumentNoteType` VALUES (9,'General');
/*!40000 ALTER TABLE `DocumentNoteType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DocumentType`
--

DROP TABLE IF EXISTS `DocumentType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DocumentType` (
  `documentTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`documentTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DocumentType`
--

LOCK TABLES `DocumentType` WRITE;
/*!40000 ALTER TABLE `DocumentType` DISABLE KEYS */;
INSERT INTO `DocumentType` VALUES (1,'Checklist');
/*!40000 ALTER TABLE `DocumentType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Expression`
--

DROP TABLE IF EXISTS `Expression`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Expression` (
  `expressionID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documentID` int(10) unsigned NOT NULL,
  `expressionTypeID` int(10) unsigned NOT NULL,
  `documentText` text,
  `simplifiedText` text,
  `lastUpdateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `productionUseInd` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`expressionID`),
  KEY `documentID` (`documentID`),
  KEY `expressionTypeID` (`expressionTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Expression`
--

LOCK TABLES `Expression` WRITE;
/*!40000 ALTER TABLE `Expression` DISABLE KEYS */;
/*!40000 ALTER TABLE `Expression` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExpressionNote`
--

DROP TABLE IF EXISTS `ExpressionNote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExpressionNote` (
  `expressionNoteID` int(10) NOT NULL AUTO_INCREMENT,
  `expressionID` int(10) DEFAULT NULL,
  `note` varchar(2000) DEFAULT NULL,
  `displayOrderSeqNumber` int(10) DEFAULT NULL,
  `lastUpdateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expressionNoteID`),
  KEY `expressionID` (`expressionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExpressionNote`
--

LOCK TABLES `ExpressionNote` WRITE;
/*!40000 ALTER TABLE `ExpressionNote` DISABLE KEYS */;
/*!40000 ALTER TABLE `ExpressionNote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExpressionQualifierProfile`
--

DROP TABLE IF EXISTS `ExpressionQualifierProfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExpressionQualifierProfile` (
  `expressionID` int(10) unsigned NOT NULL,
  `qualifierID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`expressionID`,`qualifierID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExpressionQualifierProfile`
--

LOCK TABLES `ExpressionQualifierProfile` WRITE;
/*!40000 ALTER TABLE `ExpressionQualifierProfile` DISABLE KEYS */;
/*!40000 ALTER TABLE `ExpressionQualifierProfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExpressionType`
--

DROP TABLE IF EXISTS `ExpressionType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExpressionType` (
  `expressionTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  `noteType` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`expressionTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExpressionType`
--

LOCK TABLES `ExpressionType` WRITE;
/*!40000 ALTER TABLE `ExpressionType` DISABLE KEYS */;
/*!40000 ALTER TABLE `ExpressionType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `License`
--

DROP TABLE IF EXISTS `License`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `License` (
  `licenseID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `consortiumID` int(10) unsigned DEFAULT NULL,
  `organizationID` int(10) unsigned DEFAULT NULL,
  `shortName` tinytext NOT NULL,
  `statusID` int(10) unsigned DEFAULT NULL,
  `statusDate` datetime DEFAULT NULL,
  `createDate` datetime DEFAULT NULL,
  `typeID` int(11) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `statusLoginID` varchar(50) DEFAULT NULL,
  `createLoginID` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`licenseID`),
  KEY `organizationID` (`organizationID`),
  KEY `consortiumID` (`consortiumID`),
  KEY `statusID` (`statusID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `License`
--

LOCK TABLES `License` WRITE;
/*!40000 ALTER TABLE `License` DISABLE KEYS */;
INSERT INTO `license` VALUES (1, NULL, 1, 'Filler Doc', NULL, '2016-10-03 22:14:57', '2016-10-03 22:14:57', 1, NULL, 'coral_test', 'coral_test');
/*!40000 ALTER TABLE `License` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Organization`
--

DROP TABLE IF EXISTS `Organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Organization` (
  `organizationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`organizationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Organization`
--

LOCK TABLES `Organization` WRITE;
/*!40000 ALTER TABLE `Organization` DISABLE KEYS */;
INSERT INTO `Organization` VALUES (1, 'Default Internal');
/*!40000 ALTER TABLE `Organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Privilege`
--

DROP TABLE IF EXISTS `Privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Privilege` (
  `privilegeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`privilegeID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Privilege`
--

LOCK TABLES `Privilege` WRITE;
/*!40000 ALTER TABLE `Privilege` DISABLE KEYS */;
INSERT INTO `Privilege` VALUES (1,'admin'),(2,'add/edit'),(3,'view only');
/*!40000 ALTER TABLE `Privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Qualifier`
--

DROP TABLE IF EXISTS `Qualifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Qualifier` (
  `qualifierID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `expressionTypeID` int(10) unsigned NOT NULL,
  `shortName` varchar(45) NOT NULL,
  PRIMARY KEY (`qualifierID`),
  KEY `expressionTypeID` (`expressionTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Qualifier`
--

LOCK TABLES `Qualifier` WRITE;
/*!40000 ALTER TABLE `Qualifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `Qualifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SFXProvider`
--

DROP TABLE IF EXISTS `SFXProvider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SFXProvider` (
  `sfxProviderID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documentID` int(10) unsigned NOT NULL,
  `shortName` varchar(245) NOT NULL,
  PRIMARY KEY (`sfxProviderID`),
  KEY `documentID` (`documentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SFXProvider`
--

LOCK TABLES `SFXProvider` WRITE;
/*!40000 ALTER TABLE `SFXProvider` DISABLE KEYS */;
/*!40000 ALTER TABLE `SFXProvider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Signature`
--

DROP TABLE IF EXISTS `Signature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Signature` (
  `signatureID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `documentID` int(10) unsigned NOT NULL,
  `signatureTypeID` int(10) unsigned NOT NULL,
  `signatureDate` date DEFAULT NULL,
  `signerName` tinytext,
  PRIMARY KEY (`signatureID`),
  KEY `documentID` (`documentID`),
  KEY `signatureTypeID` (`signatureTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Signature`
--

LOCK TABLES `Signature` WRITE;
/*!40000 ALTER TABLE `Signature` DISABLE KEYS */;
/*!40000 ALTER TABLE `Signature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SignatureType`
--

DROP TABLE IF EXISTS `SignatureType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SignatureType` (
  `signatureTypeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`signatureTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SignatureType`
--

LOCK TABLES `SignatureType` WRITE;
/*!40000 ALTER TABLE `SignatureType` DISABLE KEYS */;
/*!40000 ALTER TABLE `SignatureType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Status`
--

DROP TABLE IF EXISTS `Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Status` (
  `statusID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) NOT NULL,
  PRIMARY KEY (`statusID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Status`
--

LOCK TABLES `Status` WRITE;
/*!40000 ALTER TABLE `Status` DISABLE KEYS */;
/*!40000 ALTER TABLE `Status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Type`
--

DROP TABLE IF EXISTS `Type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Type` (
  `typeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`typeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Type`
--

LOCK TABLES `Type` WRITE;
/*!40000 ALTER TABLE `Type` DISABLE KEYS */;
/*!40000 ALTER TABLE `Type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `loginID` varchar(50) NOT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `privilegeID` int(10) unsigned DEFAULT NULL,
  `emailAddressForTermsTool` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`loginID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES ('coral_test',NULL,NULL,1,NULL);
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `license_consortium`
--

DROP TABLE IF EXISTS `license_consortium`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `license_consortium` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `licenseID` int(11) DEFAULT NULL,
  `consortiumID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='							';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `license_consortium`
--

LOCK TABLES `license_consortium` WRITE;
/*!40000 ALTER TABLE `license_consortium` DISABLE KEYS */;
/*!40000 ALTER TABLE `license_consortium` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `coral_organizations_test`
--

USE `coral_organizations_test`;

--
-- Table structure for table `Alias`
--

DROP TABLE IF EXISTS `Alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Alias` (
  `aliasID` int(11) NOT NULL AUTO_INCREMENT,
  `organizationID` int(11) DEFAULT NULL,
  `aliasTypeID` int(11) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`aliasID`),
  UNIQUE KEY `aliasID` (`aliasID`),
  KEY `organizationID` (`organizationID`),
  KEY `aliasTypeID` (`aliasTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=123 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Alias`
--

LOCK TABLES `Alias` WRITE;
/*!40000 ALTER TABLE `Alias` DISABLE KEYS */;
INSERT INTO `Alias` VALUES (1,2,3,'IOP'),(2,3,3,'AIAA'),(3,4,3,'APS'),(4,5,3,'ASCE'),(5,6,3,'AIP'),(6,7,3,'SIAM'),(7,9,3,'CAS'),(8,10,3,'RMA'),(9,11,3,'ACI'),(10,12,3,'AACR'),(11,13,3,'IET'),(12,14,3,'AEA'),(13,15,3,'AMS'),(14,16,3,'AMA'),(15,35,2,'Thomson Gale'),(16,18,3,'CSIC'),(17,19,3,'AMS'),(18,20,3,'ALA'),(19,21,3,'ASTM'),(20,22,3,'ARL'),(21,23,3,'ASLO'),(22,25,3,'APA'),(23,26,3,'ACLS'),(24,27,3,'AAAS'),(25,265,3,'CEA'),(26,141,3,'L/N'),(27,270,3,'CSA'),(28,305,3,'ACM'),(29,306,3,'ACS'),(30,8,2,'Competitive Media Reporting, LLC'),(31,28,1,'Thomson ISI'),(32,53,3,'AccuNet'),(33,271,3,'AICPA'),(34,311,2,'American Association on Mental Retardation'),(35,311,3,'AAMR'),(36,311,3,'AAIDD'),(37,46,3,'AFS'),(38,32,3,'AGU'),(39,245,3,'APPI'),(40,244,3,'ASCB'),(41,242,3,'ASHS'),(42,312,3,'ASM'),(43,314,3,'ASME'),(44,401,3,'WWP'),(45,236,3,'ASA'),(46,224,3,'BE Press'),(47,180,3,'FASEB'),(48,177,3,'GSA'),(49,163,3,'HCS'),(50,134,3,'MBL'),(51,104,3,'RSC'),(52,96,3,'SEBM'),(53,95,3,'SGM'),(54,94,3,'SLB'),(55,93,3,'SfN'),(56,82,3,'TTP'),(57,61,1,'Institute of Electrical and Electronics Engineers'),(58,171,3,'AIC'),(59,276,3,'AAR'),(60,273,3,'ACA'),(61,322,3,'AIMS'),(62,214,3,'CCDC'),(63,362,3,'CIAO'),(64,363,3,'CNRI'),(65,366,3,'GSA'),(66,368,3,'IPAP'),(67,371,3,'MLA'),(68,372,3,'OSA'),(69,380,3,'PS'),(70,257,1,'Atypon Link'),(71,383,3,'SAE'),(72,41,1,'Caliber'),(73,59,3,'CUP'),(74,193,3,'EUP'),(75,365,1,'Insight'),(76,386,3,'LEA'),(77,372,1,'OpticsInfoBase'),(78,116,1,'POJ'),(79,113,1,'Sirius'),(80,255,3,'MIT'),(81,206,3,'CDP'),(82,317,3,'CRL'),(83,88,1,'Society of Photo-optical Instrumentation Engineers'),(84,279,1,'Simmons Market Research Bureau'),(85,404,1,'Society of Environmental Toxicology and Chemistry '),(86,405,1,'oldenbourg-link'),(87,415,1,'Otto Harrassowitz'),(88,259,2,'Thomson RIA'),(89,28,2,'Thomson Scientific'),(90,418,3,'HRAF'),(91,399,1,'ReferenceUSA'),(92,254,1,'Audit Analytics'),(93,159,2,'Idea Group Inc'),(94,87,3,'S&P'),(95,422,1,'Morgan Stanley Capital International'),(96,420,2,'RiskMetrics Group'),(97,267,3,'ISS'),(98,120,3,'Readex'),(99,425,1,'Center for the Advancement of the Research Methods and Analysis'),(100,305,1,'ACM Digital Library'),(101,426,3,'MCLS'),(102,426,2,'MLC'),(103,426,2,'Michigan Library Consortia'),(104,426,2,'INCOLSA'),(105,427,3,'PLoS'),(106,400,1,'R.R. Bowker'),(107,429,3,'UNIDO'),(108,434,3,'BAS'),(109,35,1,'APG: Academic and Professional Group'),(110,44,2,'Silverplatter'),(111,44,1,'OvidSP'),(112,122,1,'Naxos Digital Services Ltd'),(113,37,3,'OUP'),(114,475,3,'OECD'),(115,194,3,'ESA'),(116,173,1,'H1Base'),(117,28,1,'ISI'),(118,484,1,'Austrian Academy of Sciences Press'),(119,485,3,'ARM'),(120,485,1,'New World Records'),(121,490,1,'CDI Systems Ltd.'),(122,399,2,'infogroup');
/*!40000 ALTER TABLE `Alias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AliasType`
--

DROP TABLE IF EXISTS `AliasType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AliasType` (
  `aliasTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`aliasTypeID`),
  UNIQUE KEY `aliasTypeID` (`aliasTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AliasType`
--

LOCK TABLES `AliasType` WRITE;
/*!40000 ALTER TABLE `AliasType` DISABLE KEYS */;
INSERT INTO `AliasType` VALUES (1,'Alternate Name'),(2,'Name Change'),(3,'Acronym');
/*!40000 ALTER TABLE `AliasType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Contact`
--

DROP TABLE IF EXISTS `Contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Contact` (
  `contactID` int(11) NOT NULL AUTO_INCREMENT,
  `organizationID` int(11) NOT NULL,
  `lastUpdateDate` date DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `addressText` varchar(300) DEFAULT NULL,
  `phoneNumber` varchar(50) DEFAULT NULL,
  `altPhoneNumber` varchar(50) DEFAULT NULL,
  `faxNumber` varchar(50) DEFAULT NULL,
  `emailAddress` varchar(100) DEFAULT NULL,
  `archiveDate` date DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`contactID`),
  UNIQUE KEY `contactID` (`contactID`),
  KEY `organizationID` (`organizationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Contact`
--

LOCK TABLES `Contact` WRITE;
/*!40000 ALTER TABLE `Contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `Contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ContactRole`
--

DROP TABLE IF EXISTS `ContactRole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ContactRole` (
  `contactRoleID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`contactRoleID`),
  UNIQUE KEY `contactRoleID` (`contactRoleID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ContactRole`
--

LOCK TABLES `ContactRole` WRITE;
/*!40000 ALTER TABLE `ContactRole` DISABLE KEYS */;
INSERT INTO `ContactRole` VALUES (1,'Accounting'),(2,'Licensing'),(3,'Sales'),(4,'Support'),(5,'Training');
/*!40000 ALTER TABLE `ContactRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ContactRoleProfile`
--

DROP TABLE IF EXISTS `ContactRoleProfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ContactRoleProfile` (
  `contactID` int(10) unsigned NOT NULL,
  `contactRoleID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contactID`,`contactRoleID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ContactRoleProfile`
--

LOCK TABLES `ContactRoleProfile` WRITE;
/*!40000 ALTER TABLE `ContactRoleProfile` DISABLE KEYS */;
/*!40000 ALTER TABLE `ContactRoleProfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExternalLogin`
--

DROP TABLE IF EXISTS `ExternalLogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExternalLogin` (
  `externalLoginID` int(11) NOT NULL AUTO_INCREMENT,
  `organizationID` int(11) DEFAULT NULL,
  `externalLoginTypeID` int(11) DEFAULT NULL,
  `updateDate` date DEFAULT NULL,
  `loginURL` varchar(150) DEFAULT NULL,
  `emailAddress` varchar(150) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`externalLoginID`),
  UNIQUE KEY `externalLoginID` (`externalLoginID`),
  KEY `organizationID` (`organizationID`),
  KEY `externalLoginTypeID` (`externalLoginTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExternalLogin`
--

LOCK TABLES `ExternalLogin` WRITE;
/*!40000 ALTER TABLE `ExternalLogin` DISABLE KEYS */;
/*!40000 ALTER TABLE `ExternalLogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExternalLoginType`
--

DROP TABLE IF EXISTS `ExternalLoginType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExternalLoginType` (
  `externalLoginTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`externalLoginTypeID`),
  UNIQUE KEY `externalLoginTypeID` (`externalLoginTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExternalLoginType`
--

LOCK TABLES `ExternalLoginType` WRITE;
/*!40000 ALTER TABLE `ExternalLoginType` DISABLE KEYS */;
INSERT INTO `ExternalLoginType` VALUES (1,'Admin'),(2,'FTP'),(3,'Other'),(4,'Statistics'),(5,'Support');
/*!40000 ALTER TABLE `ExternalLoginType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IssueLog`
--

DROP TABLE IF EXISTS `IssueLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IssueLog` (
  `issueLogID` int(11) NOT NULL AUTO_INCREMENT,
  `organizationID` int(11) DEFAULT NULL,
  `issueLogTypeID` int(11) DEFAULT NULL,
  `updateDate` date DEFAULT NULL,
  `updateLoginID` varchar(50) DEFAULT NULL,
  `issueStartDate` date DEFAULT NULL,
  `issueEndDate` date DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`issueLogID`),
  UNIQUE KEY `issueLogID` (`issueLogID`),
  KEY `organizationID` (`organizationID`),
  KEY `issueLogTypeID` (`issueLogTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IssueLog`
--

LOCK TABLES `IssueLog` WRITE;
/*!40000 ALTER TABLE `IssueLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `IssueLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IssueLogType`
--

DROP TABLE IF EXISTS `IssueLogType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IssueLogType` (
  `issueLogTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`issueLogTypeID`),
  UNIQUE KEY `issueLogTypeID` (`issueLogTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IssueLogType`
--

LOCK TABLES `IssueLogType` WRITE;
/*!40000 ALTER TABLE `IssueLogType` DISABLE KEYS */;
/*!40000 ALTER TABLE `IssueLogType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Organization`
--

DROP TABLE IF EXISTS `Organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Organization` (
  `organizationID` int(11) NOT NULL AUTO_INCREMENT,
  `createDate` date DEFAULT NULL,
  `createLoginID` varchar(50) DEFAULT NULL,
  `updateDate` date DEFAULT NULL,
  `updateLoginID` varchar(50) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `companyURL` varchar(150) DEFAULT NULL,
  `noteText` text,
  `accountDetailText` text,
  PRIMARY KEY (`organizationID`),
  UNIQUE KEY `organizationID` (`organizationID`)
) ENGINE=MyISAM AUTO_INCREMENT=505 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Organization`
--

LOCK TABLES `Organization` WRITE;
/*!40000 ALTER TABLE `Organization` DISABLE KEYS */;
INSERT INTO `Organization` VALUES (2,'2016-09-09','system',NULL,NULL,'Institute of Physics',NULL,NULL,NULL),(3,'2016-09-09','system',NULL,NULL,'American Institute of Aeronautics and Astronautics',NULL,NULL,NULL),(4,'2016-09-09','system',NULL,NULL,'American Physical Society',NULL,NULL,NULL),(5,'2016-09-09','system',NULL,NULL,'American Society of Civil Engineers',NULL,NULL,NULL),(6,'2016-09-09','system',NULL,NULL,'American Insitute of Physics',NULL,NULL,NULL),(7,'2016-09-09','system',NULL,NULL,'Society for Industrial and Applied Mathematics',NULL,NULL,NULL),(8,'2016-09-09','system',NULL,NULL,'TNS Media Intelligence',NULL,NULL,NULL),(9,'2016-09-09','system',NULL,NULL,'Chemical Abstracts Service',NULL,NULL,NULL),(10,'2016-09-09','system',NULL,NULL,'Risk Management Association',NULL,NULL,NULL),(11,'2016-09-09','system',NULL,NULL,'American Concrete Institute',NULL,NULL,NULL),(12,'2016-09-09','system',NULL,NULL,'American Association for Cancer Research',NULL,NULL,NULL),(13,'2016-09-09','system',NULL,NULL,'Institution of Engineering and Technology',NULL,NULL,NULL),(14,'2016-09-09','system',NULL,NULL,'American Economic Association',NULL,NULL,NULL),(15,'2016-09-09','system',NULL,NULL,'American Mathematical Society',NULL,NULL,NULL),(16,'2016-09-09','system',NULL,NULL,'American Medical Association',NULL,NULL,NULL),(18,'2016-09-09','system',NULL,NULL,'Consejo Superior de Investigaciones Cientificas',NULL,NULL,NULL),(19,'2016-09-09','system',NULL,NULL,'American Meteorological Society',NULL,NULL,NULL),(20,'2016-09-09','system',NULL,NULL,'American Library Association',NULL,NULL,NULL),(21,'2016-09-09','system',NULL,NULL,'American Society for Testing and Materials',NULL,NULL,NULL),(22,'2016-09-09','system',NULL,NULL,'Association of Research Libraries',NULL,NULL,NULL),(23,'2016-09-09','system',NULL,NULL,'American Society of Limnology and Oceanography',NULL,NULL,NULL),(24,'2016-09-09','system',NULL,NULL,'Tablet Publishing',NULL,NULL,NULL),(25,'2016-09-09','system',NULL,NULL,'American Psychological Association',NULL,NULL,NULL),(26,'2016-09-09','system',NULL,NULL,'American Council of Learned Societies',NULL,NULL,NULL),(27,'2016-09-09','system',NULL,NULL,'American Association for the Advancement of Science',NULL,NULL,NULL),(28,'2016-09-09','system',NULL,NULL,'Thomson Healthcare and Science',NULL,NULL,NULL),(29,'2016-09-09','system',NULL,NULL,'Elsevier',NULL,NULL,NULL),(30,'2016-09-09','system',NULL,NULL,'JSTOR',NULL,NULL,NULL),(31,'2016-09-09','system',NULL,NULL,'SAGE Publications',NULL,NULL,NULL),(32,'2016-09-09','system',NULL,NULL,'American Geophysical Union',NULL,NULL,NULL),(33,'2016-09-09','system',NULL,NULL,'Annual Reviews',NULL,NULL,NULL),(34,'2016-09-09','system',NULL,NULL,'BioOne',NULL,NULL,NULL),(35,'2016-09-09','system',NULL,NULL,'Gale',NULL,NULL,NULL),(36,'2016-09-09','system',NULL,NULL,'Wiley',NULL,NULL,NULL),(37,'2016-09-09','system',NULL,NULL,'Oxford University Press',NULL,NULL,NULL),(38,'2016-09-09','system',NULL,NULL,'Springer',NULL,NULL,NULL),(39,'2016-09-09','system',NULL,NULL,'Taylor and Francis',NULL,NULL,NULL),(40,'2016-09-09','system',NULL,NULL,'Stanford University',NULL,NULL,NULL),(41,'2016-09-09','system',NULL,NULL,'University of California Press',NULL,NULL,NULL),(42,'2016-09-09','system',NULL,NULL,'EBSCO Publishing',NULL,NULL,NULL),(43,'2016-09-09','system',NULL,NULL,'Blackwell Publishing',NULL,NULL,NULL),(44,'2016-09-09','system',NULL,NULL,'Ovid',NULL,NULL,NULL),(45,'2016-09-09','system',NULL,NULL,'Project Muse',NULL,NULL,NULL),(46,'2016-09-09','system',NULL,NULL,'American Fisheries Society',NULL,NULL,NULL),(47,'2016-09-09','system',NULL,NULL,'Neilson Journals Publishing',NULL,NULL,NULL),(48,'2016-09-09','system',NULL,NULL,'GuideStar USA, Inc',NULL,NULL,NULL),(49,'2016-09-09','system',NULL,NULL,'Alexander Street Press, LLC',NULL,NULL,NULL),(50,'2016-09-09','system',NULL,NULL,'Informa Healthcare USA, Inc',NULL,NULL,NULL),(51,'2016-09-09','system',NULL,NULL,'ProQuest LLC',NULL,NULL,NULL),(52,'2016-09-09','system',NULL,NULL,'Accessible Archives Inc',NULL,NULL,NULL),(53,'2016-09-09','system',NULL,NULL,'ACCU Weather Sales and Services, Inc',NULL,NULL,NULL),(54,'2016-09-09','system',NULL,NULL,'Adam Matthew Digital Ltd',NULL,NULL,NULL),(55,'2016-09-09','system',NULL,NULL,'Akademiai Kiado',NULL,NULL,NULL),(56,'2016-09-09','system',NULL,NULL,'World Trade Press',NULL,NULL,NULL),(57,'2016-09-09','system',NULL,NULL,'World Scientific',NULL,NULL,NULL),(58,'2016-09-09','system',NULL,NULL,'Walter de Gruyter',NULL,NULL,NULL),(59,'2016-09-09','system',NULL,NULL,'Cambridge University Press',NULL,NULL,NULL),(60,'2016-09-09','system',NULL,NULL,'GeoScienceWorld',NULL,NULL,NULL),(61,'2016-09-09','system',NULL,NULL,'IEEE',NULL,NULL,NULL),(62,'2016-09-09','system',NULL,NULL,'Yankelovich Inc',NULL,NULL,NULL),(63,'2016-09-09','system',NULL,NULL,'Nature Publishing Group',NULL,NULL,NULL),(64,'2016-09-09','system',NULL,NULL,'Verlag der Zeitschrift fur Naturforschung ',NULL,NULL,NULL),(65,'2016-09-09','system',NULL,NULL,'White Horse Press',NULL,NULL,NULL),(66,'2016-09-09','system',NULL,NULL,'Verlag C.H. Beck',NULL,NULL,NULL),(67,'2016-09-09','system',NULL,NULL,'Vault, Inc',NULL,NULL,NULL),(68,'2016-09-09','system',NULL,NULL,'Value Line, Inc',NULL,NULL,NULL),(69,'2016-09-09','system',NULL,NULL,'Vanderbilt University',NULL,NULL,NULL),(70,'2016-09-09','system',NULL,NULL,'Uniworld Business Publications, Inc',NULL,NULL,NULL),(71,'2016-09-09','system',NULL,NULL,'Universum USA',NULL,NULL,NULL),(72,'2016-09-09','system',NULL,NULL,'University of Wisconsin Press',NULL,NULL,NULL),(73,'2016-09-09','system',NULL,NULL,'University of Virginia Press',NULL,NULL,NULL),(74,'2016-09-09','system',NULL,NULL,'University of Toronto Press Inc',NULL,NULL,NULL),(75,'2016-09-09','system',NULL,NULL,'University of Toronto',NULL,NULL,NULL),(76,'2016-09-09','system',NULL,NULL,'University of Pittsburgh',NULL,NULL,NULL),(77,'2016-09-09','system',NULL,NULL,'University of Illinois Press',NULL,NULL,NULL),(78,'2016-09-09','system',NULL,NULL,'University of Chicago Press',NULL,NULL,NULL),(79,'2016-09-09','system',NULL,NULL,'University of Barcelona',NULL,NULL,NULL),(80,'2016-09-09','system',NULL,NULL,'UCLA Chicano Studies Research Center Press',NULL,NULL,NULL),(81,'2016-09-09','system',NULL,NULL,'Transportation Research Board',NULL,NULL,NULL),(82,'2016-09-09','system',NULL,NULL,'Trans Tech Publications',NULL,NULL,NULL),(83,'2016-09-09','system',NULL,NULL,'Thomas Telford Ltd',NULL,NULL,NULL),(84,'2016-09-09','system',NULL,NULL,'Thesaurus Linguae Graecae',NULL,NULL,NULL),(85,'2016-09-09','system',NULL,NULL,'Tetrad Computer Applications Inc',NULL,NULL,NULL),(86,'2016-09-09','system',NULL,NULL,'Swank Motion Pictures, Inc',NULL,NULL,NULL),(87,'2016-09-09','system',NULL,NULL,'Standard and Poors',NULL,NULL,NULL),(88,'2016-09-09','system',NULL,NULL,'SPIE',NULL,NULL,NULL),(89,'2016-09-09','system',NULL,NULL,'European Society of Endocrinology',NULL,NULL,NULL),(90,'2016-09-09','system',NULL,NULL,'Society of Environmental Toxicology and Chemistry',NULL,NULL,NULL),(91,'2016-09-09','system',NULL,NULL,'Society of Antiquaries of Scotland',NULL,NULL,NULL),(92,'2016-09-09','system',NULL,NULL,'Society for Reproduction and Fertility',NULL,NULL,NULL),(93,'2016-09-09','system',NULL,NULL,'Society for Neuroscience',NULL,NULL,NULL),(94,'2016-09-09','system',NULL,NULL,'Society for Leukocyte Biology',NULL,NULL,NULL),(95,'2016-09-09','system',NULL,NULL,'Society for General Microbiology',NULL,NULL,NULL),(96,'2016-09-09','system',NULL,NULL,'Society for Experimental Biology and Medicine',NULL,NULL,NULL),(97,'2016-09-09','system',NULL,NULL,'Society for Endocrinology',NULL,NULL,NULL),(98,'2016-09-09','system',NULL,NULL,'Societe Mathematique de France',NULL,NULL,NULL),(99,'2016-09-09','system',NULL,NULL,'Social Explorer',NULL,NULL,NULL),(404,'2016-09-09','system',NULL,NULL,'SETAC',NULL,NULL,NULL),(101,'2016-09-09','system',NULL,NULL,'Swiss Chemical Society',NULL,NULL,NULL),(102,'2016-09-09','system',NULL,NULL,'Scholarly Digital Editions',NULL,NULL,NULL),(103,'2016-09-09','system',NULL,NULL,'Royal Society of London',NULL,NULL,NULL),(104,'2016-09-09','system',NULL,NULL,'Royal Society of Chemistry',NULL,NULL,NULL),(105,'2016-09-09','system',NULL,NULL,'Roper Center for Public Opinion Research',NULL,NULL,NULL),(106,'2016-09-09','system',NULL,NULL,'Rockefeller University Press',NULL,NULL,NULL),(107,'2016-09-09','system',NULL,NULL,'Rivista di Studi italiani',NULL,NULL,NULL),(108,'2016-09-09','system',NULL,NULL,'Reuters Loan Pricing Corporation',NULL,NULL,NULL),(109,'2016-09-09','system',NULL,NULL,'Religious and Theological Abstracts, Inc',NULL,NULL,NULL),(110,'2016-09-09','system',NULL,NULL,'Psychoanalytic Electronic Publishing Inc',NULL,NULL,NULL),(111,'2016-09-09','system',NULL,NULL,'Cornell University Library',NULL,NULL,NULL),(112,'2016-09-09','system',NULL,NULL,'Preservation Technologies LP',NULL,NULL,NULL),(113,'2016-09-09','system',NULL,NULL,'Portland Press Limited',NULL,NULL,NULL),(114,'2016-09-09','system',NULL,NULL,'ITHAKA',NULL,NULL,NULL),(115,'2016-09-09','system',NULL,NULL,'Philosophy Documentation Center',NULL,NULL,NULL),(116,'2016-09-09','system',NULL,NULL,'Peeters Publishers',NULL,NULL,NULL),(117,'2016-09-09','system',NULL,NULL,'Paratext',NULL,NULL,NULL),(118,'2016-09-09','system',NULL,NULL,'Mathematical Sciences Publishers',NULL,NULL,NULL),(119,'2016-09-09','system',NULL,NULL,'Oxford Centre of Hebrew and Jewish Studies',NULL,NULL,NULL),(120,'2016-09-09','system',NULL,NULL,'NewsBank, Inc',NULL,NULL,NULL),(121,'2016-09-09','system',NULL,NULL,'Massachusetts Medical Society',NULL,NULL,NULL),(122,'2016-09-09','system',NULL,NULL,'Naxos of America, Inc.',NULL,NULL,NULL),(123,'2016-09-09','system',NULL,NULL,'National Research Council of Canada',NULL,NULL,NULL),(124,'2016-09-09','system',NULL,NULL,'National Gallery Company Ltd',NULL,NULL,NULL),(125,'2016-09-09','system',NULL,NULL,'National Academy of Sciences',NULL,NULL,NULL),(126,'2016-09-09','system',NULL,NULL,'Mintel International Group Limited',NULL,NULL,NULL),(127,'2016-09-09','system',NULL,NULL,'Metropolitan Opera',NULL,NULL,NULL),(128,'2016-09-09','system',NULL,NULL,'M.E. Sharpe, Inc',NULL,NULL,NULL),(129,'2016-09-09','system',NULL,NULL,'Mergent, Inc',NULL,NULL,NULL),(130,'2016-09-09','system',NULL,NULL,'Mediamark Research and Intelligence, LLC',NULL,NULL,NULL),(131,'2016-09-09','system',NULL,NULL,'Mary Ann Liebert, Inc',NULL,NULL,NULL),(132,'2016-09-09','system',NULL,NULL,'MIT Press',NULL,NULL,NULL),(133,'2016-09-09','system',NULL,NULL,'MarketResearch.com, Inc',NULL,NULL,NULL),(134,'2016-09-09','system',NULL,NULL,'Marine Biological Laboratory',NULL,NULL,NULL),(135,'2016-09-09','system',NULL,NULL,'W.S. Maney and Son Ltd',NULL,NULL,NULL),(136,'2016-09-09','system',NULL,NULL,'Manchester University Press',NULL,NULL,NULL),(137,'2016-09-09','system',NULL,NULL,'Lord Music Reference Inc',NULL,NULL,NULL),(138,'2016-09-09','system',NULL,NULL,'Liverpool University Press',NULL,NULL,NULL),(139,'2016-09-09','system',NULL,NULL,'Seminario Matematico of the University of Padua',NULL,NULL,NULL),(140,'2016-09-09','system',NULL,NULL,'Library of Congress, Cataloging Distribution Service',NULL,NULL,NULL),(141,'2016-09-09','system',NULL,NULL,'LexisNexis',NULL,NULL,NULL),(142,'2016-09-09','system',NULL,NULL,'Corporacion Latinobarometro',NULL,NULL,NULL),(143,'2016-09-09','system',NULL,NULL,'Landes Bioscience',NULL,NULL,NULL),(144,'2016-09-09','system',NULL,NULL,'Keesings Worldwide, LLC',NULL,NULL,NULL),(145,'2016-09-09','system',NULL,NULL,'Karger',NULL,NULL,NULL),(146,'2016-09-09','system',NULL,NULL,'John Benjamins Publishing Company',NULL,NULL,NULL),(147,'2016-09-09','system',NULL,NULL,'Irish Newspaper Archives Ltd',NULL,NULL,NULL),(148,'2016-09-09','system',NULL,NULL,'IPA Source, LLC',NULL,NULL,NULL),(149,'2016-09-09','system',NULL,NULL,'International Press',NULL,NULL,NULL),(150,'2016-09-09','system',NULL,NULL,'Intelligence Research Limited',NULL,NULL,NULL),(151,'2016-09-09','system',NULL,NULL,'Intellect',NULL,NULL,NULL),(152,'2016-09-09','system',NULL,NULL,'InteLex',NULL,NULL,NULL),(153,'2016-09-09','system',NULL,NULL,'Institute of Mathematics of the Polish Academy of Sciences',NULL,NULL,NULL),(154,'2016-09-09','system',NULL,NULL,'Ingentaconnect',NULL,NULL,NULL),(155,'2016-09-09','system',NULL,NULL,'INFORMS',NULL,NULL,NULL),(156,'2016-09-09','system',NULL,NULL,'Information Resources, Inc',NULL,NULL,NULL),(157,'2016-09-09','system',NULL,NULL,'Indiana University Mathematics Journal',NULL,NULL,NULL),(158,'2016-09-09','system',NULL,NULL,'Incisive Media Ltd',NULL,NULL,NULL),(159,'2016-09-09','system',NULL,NULL,'IGI Global ',NULL,NULL,NULL),(160,'2016-09-09','system',NULL,NULL,'IBISWorld USA',NULL,NULL,NULL),(161,'2016-09-09','system',NULL,NULL,'H.W. Wilson Company',NULL,NULL,NULL),(162,'2016-09-09','system',NULL,NULL,'University of Houston Department of Mathematics',NULL,NULL,NULL),(163,'2016-09-09','system',NULL,NULL,'Histochemical Society',NULL,NULL,NULL),(164,'2016-09-09','system',NULL,NULL,'Morningstar Inc.',NULL,NULL,NULL),(165,'2016-09-09','system',NULL,NULL,'Paradigm Publishers',NULL,NULL,NULL),(166,'2016-09-09','system',NULL,NULL,'HighWire Press',NULL,NULL,NULL),(167,'2016-09-09','system',NULL,NULL,'Heldref Publications',NULL,NULL,NULL),(168,'2016-09-09','system',NULL,NULL,'Haworth Press',NULL,NULL,NULL),(417,'2016-09-09','system',NULL,NULL,'Thomson Legal',NULL,NULL,NULL),(170,'2016-09-09','system',NULL,NULL,'IOS Press',NULL,NULL,NULL),(171,'2016-09-09','system',NULL,NULL,'Agricultural Institute of Canada',NULL,NULL,NULL),(172,'2016-09-09','system',NULL,NULL,'Allen Press',NULL,NULL,NULL),(173,'2016-09-09','system',NULL,NULL,'H1 Base, Inc',NULL,NULL,NULL),(175,'2016-09-09','system',NULL,NULL,'Global Science Press',NULL,NULL,NULL),(176,'2016-09-09','system',NULL,NULL,'Geographic Research, Inc',NULL,NULL,NULL),(177,'2016-09-09','system',NULL,NULL,'Genetics Society of America',NULL,NULL,NULL),(178,'2016-09-09','system',NULL,NULL,'Franz Steiner Verlag',NULL,NULL,NULL),(179,'2016-09-09','system',NULL,NULL,'Forrester Research, Inc',NULL,NULL,NULL),(180,'2016-09-09','system',NULL,NULL,'Federation of American Societies for Experimental Biology',NULL,NULL,NULL),(181,'2016-09-09','system',NULL,NULL,'Faulkner Information Services',NULL,NULL,NULL),(182,'2016-09-09','system',NULL,NULL,'ExLibris',NULL,NULL,NULL),(183,'2016-09-09','system',NULL,NULL,'Brill',NULL,NULL,NULL),(184,'2016-09-09','system',NULL,NULL,'Evolutionary Ecology Ltd',NULL,NULL,NULL),(185,'2016-09-09','system',NULL,NULL,'European Mathematical Society Publishing House',NULL,NULL,NULL),(186,'2016-09-09','system',NULL,NULL,'New York Review of Books',NULL,NULL,NULL),(187,'2016-09-09','system',NULL,NULL,'Dunstans Publishing Ltd',NULL,NULL,NULL),(188,'2016-09-09','system',NULL,NULL,'Equinox Publishing Ltd',NULL,NULL,NULL),(189,'2016-09-09','system',NULL,NULL,'Entomological Society of Canada',NULL,NULL,NULL),(190,'2016-09-09','system',NULL,NULL,'American Association of Immunologists, Inc.',NULL,NULL,NULL),(191,'2016-09-09','system',NULL,NULL,'Endocrine Society',NULL,NULL,NULL),(192,'2016-09-09','system',NULL,NULL,'EDP Sciences',NULL,NULL,NULL),(193,'2016-09-09','system',NULL,NULL,'Edinburgh University Press',NULL,NULL,NULL),(194,'2016-09-09','system',NULL,NULL,'Ecological Society of America',NULL,NULL,NULL),(195,'2016-09-09','system',NULL,NULL,'East View Information Services',NULL,NULL,NULL),(196,'2016-09-09','system',NULL,NULL,'Dun and Bradstreet Inc',NULL,NULL,NULL),(197,'2016-09-09','system',NULL,NULL,'Duke University Press',NULL,NULL,NULL),(198,'2016-09-09','system',NULL,NULL,'Digital Distributed Community Archive',NULL,NULL,NULL),(199,'2016-09-09','system',NULL,NULL,'Albert C. Muller',NULL,NULL,NULL),(200,'2016-09-09','system',NULL,NULL,'Dialogue Foundation',NULL,NULL,NULL),(201,'2016-09-09','system',NULL,NULL,'Dialog',NULL,NULL,NULL),(202,'2016-09-09','system',NULL,NULL,'Current History, Inc',NULL,NULL,NULL),(203,'2016-09-09','system',NULL,NULL,'CSIRO Publishing',NULL,NULL,NULL),(204,'2016-09-09','system',NULL,NULL,'CQ Press',NULL,NULL,NULL),(205,'2016-09-09','system',NULL,NULL,'Japan Focus',NULL,NULL,NULL),(206,'2016-09-09','system',NULL,NULL,'Carbon Disclosure Project',NULL,NULL,NULL),(207,'2016-09-09','system',NULL,NULL,'University of Buckingham Press',NULL,NULL,NULL),(208,'2016-09-09','system',NULL,NULL,'Boopsie, INC.',NULL,NULL,NULL),(209,'2016-09-09','system',NULL,NULL,'Company of Biologists Ltd',NULL,NULL,NULL),(210,'2016-09-09','system',NULL,NULL,'Chronicle of Higher Education',NULL,NULL,NULL),(211,'2016-09-09','system',NULL,NULL,'CCH Incorporated',NULL,NULL,NULL),(212,'2016-09-09','system',NULL,NULL,'CareerShift LLC',NULL,NULL,NULL),(213,'2016-09-09','system',NULL,NULL,'Canadian Mathematical Society',NULL,NULL,NULL),(214,'2016-09-09','system',NULL,NULL,'Cambridge Crystallographic Data Centre',NULL,NULL,NULL),(215,'2016-09-09','system',NULL,NULL,'CABI Publishing',NULL,NULL,NULL),(216,'2016-09-09','system',NULL,NULL,'Business Monitor International',NULL,NULL,NULL),(217,'2016-09-09','system',NULL,NULL,'Bureau of National Affairs, Inc',NULL,NULL,NULL),(218,'2016-09-09','system',NULL,NULL,'Bulletin of the Atomic Scientists',NULL,NULL,NULL),(219,'2016-09-09','system',NULL,NULL,'Brepols Publishers',NULL,NULL,NULL),(221,'2016-09-09','system',NULL,NULL,'Botanical Society of America',NULL,NULL,NULL),(222,'2016-09-09','system',NULL,NULL,'BMJ Publishing Group Limited',NULL,NULL,NULL),(223,'2016-09-09','system',NULL,NULL,'BioMed Central',NULL,NULL,NULL),(224,'2016-09-09','system',NULL,NULL,'Berkeley Electronic Press',NULL,NULL,NULL),(225,'2016-09-09','system',NULL,NULL,'Berghahn Books',NULL,NULL,NULL),(226,'2016-09-09','system',NULL,NULL,'Berg Publishers',NULL,NULL,NULL),(227,'2016-09-09','system',NULL,NULL,'Belser Wissenschaftlicher Dienst Ltd',NULL,NULL,NULL),(228,'2016-09-09','system',NULL,NULL,'Beilstein Information Systems, Inc',NULL,NULL,NULL),(229,'2016-09-09','system',NULL,NULL,'Barkhuis Publishing',NULL,NULL,NULL),(230,'2016-09-09','system',NULL,NULL,'Augustine Institute',NULL,NULL,NULL),(231,'2016-09-09','system',NULL,NULL,'Asempa Limited',NULL,NULL,NULL),(232,'2016-09-09','system',NULL,NULL,'ARTstor Inc',NULL,NULL,NULL),(233,'2016-09-09','system',NULL,NULL,'Applied Probability Trust',NULL,NULL,NULL),(234,'2016-09-09','system',NULL,NULL,'Antiquity Publications Limited',NULL,NULL,NULL),(235,'2016-09-09','system',NULL,NULL,'Ammons Scientific Limited',NULL,NULL,NULL),(236,'2016-09-09','system',NULL,NULL,'American Statistical Association',NULL,NULL,NULL),(237,'2016-09-09','system',NULL,NULL,'American Society of Tropical Medicine and Hygiene',NULL,NULL,NULL),(238,'2016-09-09','system',NULL,NULL,'American Society of Plant Biologists',NULL,NULL,NULL),(239,'2016-09-09','system',NULL,NULL,'Teachers College Record',NULL,NULL,NULL),(240,'2016-09-09','system',NULL,NULL,'American Society of Agronomy',NULL,NULL,NULL),(241,'2016-09-09','system',NULL,NULL,'American Society for Nutrition',NULL,NULL,NULL),(242,'2016-09-09','system',NULL,NULL,'American Society for Horticultural Science',NULL,NULL,NULL),(243,'2016-09-09','system',NULL,NULL,'American Society for Clinical Investigation',NULL,NULL,NULL),(244,'2016-09-09','system',NULL,NULL,'American Society for Cell Biology',NULL,NULL,NULL),(245,'2016-09-09','system',NULL,NULL,'American Psychiatric Publishing',NULL,NULL,NULL),(246,'2016-09-09','system',NULL,NULL,'American Phytopathological Society',NULL,NULL,NULL),(247,'2016-09-09','system',NULL,NULL,'American Physiological Society',NULL,NULL,NULL),(248,'2016-09-09','system',NULL,NULL,'Encyclopaedia Britannica Online',NULL,NULL,NULL),(249,'2016-09-09','system',NULL,NULL,'Agricultural History Society',NULL,NULL,NULL),(250,'2016-09-09','system',NULL,NULL,'Begell House, Inc',NULL,NULL,NULL),(251,'2016-09-09','system',NULL,NULL,'Hans Zell Publishing',NULL,NULL,NULL),(252,'2016-09-09','system',NULL,NULL,'Alliance for Children and Families',NULL,NULL,NULL),(253,'2016-09-09','system',NULL,NULL,'Robert Blakemore',NULL,NULL,NULL),(254,'2016-09-09','system',NULL,NULL,'IVES Group, Inc',NULL,NULL,NULL),(255,'2016-09-09','system',NULL,NULL,'Massachusetts Institute of Technology',NULL,NULL,NULL),(256,'2016-09-09','system',NULL,NULL,'Marquis Who\'s Who LLC',NULL,NULL,NULL),(257,'2016-09-09','system',NULL,NULL,'Atypon Systems Inc',NULL,NULL,NULL),(258,'2016-09-09','system',NULL,NULL,'Worldwatch Institute',NULL,NULL,NULL),(259,'2016-09-09','system',NULL,NULL,'Thomson Financial',NULL,NULL,NULL),(260,'2016-09-09','system',NULL,NULL,'Digital Heritage Publishing Limited',NULL,NULL,NULL),(261,'2016-09-09','system',NULL,NULL,'U.S. Department of Commerce',NULL,NULL,NULL),(262,'2016-09-09','system',NULL,NULL,'Lipper Inc',NULL,NULL,NULL),(263,'2016-09-09','system',NULL,NULL,'Chiniquy Collection',NULL,NULL,NULL),(264,'2016-09-09','system',NULL,NULL,'OCLC',NULL,NULL,NULL),(265,'2016-09-09','system',NULL,NULL,'Consumer Electronics Association',NULL,NULL,NULL),(267,'2016-09-09','system',NULL,NULL,'Institutional Shareholder Services Inc',NULL,NULL,NULL),(268,'2016-09-09','system',NULL,NULL,'KLD Research and Analytics Inc',NULL,NULL,NULL),(269,'2016-09-09','system',NULL,NULL,'BIGresearch LLC',NULL,NULL,NULL),(270,'2016-09-09','system',NULL,NULL,'Cambridge Scientific Abstracts',NULL,NULL,NULL),(271,'2016-09-09','system',NULL,NULL,'American Institute of Certified Public Accountants',NULL,NULL,NULL),(272,'2016-09-09','system',NULL,NULL,'Terra Scientific Publishing Company',NULL,NULL,NULL),(273,'2016-09-09','system',NULL,NULL,'American Counseling Association',NULL,NULL,NULL),(274,'2016-09-09','system',NULL,NULL,'Army Times Publishing Company',NULL,NULL,NULL),(275,'2016-09-09','system',NULL,NULL,'Librairie Droz',NULL,NULL,NULL),(276,'2016-09-09','system',NULL,NULL,'American Academy of Religion',NULL,NULL,NULL),(277,'2016-09-09','system',NULL,NULL,'Boyd Printing',NULL,NULL,NULL),(278,'2016-09-09','system',NULL,NULL,'Canadian Association of African Studies',NULL,NULL,NULL),(279,'2016-09-09','system',NULL,NULL,'Experian Marketing Solutions, Inc.',NULL,NULL,NULL),(280,'2016-09-09','system',NULL,NULL,'Centro de Investigaciones Sociologicas',NULL,NULL,NULL),(281,'2016-09-09','system',NULL,NULL,'Chorus America',NULL,NULL,NULL),(282,'2016-09-09','system',NULL,NULL,'College Art Association',NULL,NULL,NULL),(283,'2016-09-09','system',NULL,NULL,'Human Kinetics Inc.',NULL,NULL,NULL),(288,'2016-09-09','system',NULL,NULL,'NERL',NULL,NULL,NULL),(293,'2016-09-09','system',NULL,NULL,'Colegio de Mexico',NULL,NULL,NULL),(294,'2016-09-09','system',NULL,NULL,'University of Iowa',NULL,NULL,NULL),(295,'2016-09-09','system',NULL,NULL,'Academy of the Hebrew Language',NULL,NULL,NULL),(296,'2016-09-09','system',NULL,NULL,'FamilyLink.com, Inc.',NULL,NULL,NULL),(297,'2016-09-09','system',NULL,NULL,'SISMEL - Edizioni del Galluzzo',NULL,NULL,NULL),(301,'2016-09-09','system',NULL,NULL,'Informaworld',NULL,NULL,NULL),(302,'2016-09-09','system',NULL,NULL,'ScienceDirect',NULL,NULL,NULL),(304,'2016-09-09','system',NULL,NULL,'China Data Center',NULL,NULL,NULL),(305,'2016-09-09','system',NULL,NULL,'Association for Computing Machinery',NULL,NULL,NULL),(306,'2016-09-09','system',NULL,NULL,'American Chemical Society',NULL,NULL,NULL),(307,'2016-09-09','system',NULL,NULL,'Design Research Publications',NULL,NULL,NULL),(308,'2016-09-09','system',NULL,NULL,'ABC-CLIO',NULL,NULL,NULL),(311,'2016-09-09','system',NULL,NULL,'American Association on Intellectual and Developmental Disabilities ',NULL,NULL,NULL),(310,'2016-09-09','system',NULL,NULL,'American Antiquarian Society',NULL,NULL,NULL),(312,'2016-09-09','system',NULL,NULL,'American Society for Microbiology',NULL,NULL,NULL),(314,'2016-09-09','system',NULL,NULL,'American Society of Mechanical Engineers',NULL,NULL,NULL),(315,'2016-09-09','system',NULL,NULL,'Now Publishers, Inc.',NULL,NULL,NULL),(316,'2016-09-09','system',NULL,NULL,'Cabell Publishing Company, Inc.',NULL,NULL,NULL),(317,'2016-09-09','system',NULL,NULL,'Center for Research Libraries',NULL,NULL,NULL),(444,'2016-09-09','system',NULL,NULL,'Cold North Wind Inc',NULL,NULL,NULL),(321,'2016-09-09','system',NULL,NULL,'Erudit ',NULL,NULL,NULL),(322,'2016-09-09','system',NULL,NULL,'American Institute of Mathematical Sciences',NULL,NULL,NULL),(324,'2016-09-09','system',NULL,NULL,'American Sociological Association',NULL,NULL,NULL),(325,'2016-09-09','system',NULL,NULL,'Archaeological Institute of America',NULL,NULL,NULL),(326,'2016-09-09','system',NULL,NULL,'Bertrand Russell Research Centre',NULL,NULL,NULL),(328,'2016-09-09','system',NULL,NULL,'Cork University Press',NULL,NULL,NULL),(329,'2016-09-09','system',NULL,NULL,'College Publishing',NULL,NULL,NULL),(330,'2016-09-09','system',NULL,NULL,'Council for Learning Disabilities',NULL,NULL,NULL),(331,'2016-09-09','system',NULL,NULL,'International Society on Hypertension in Blacks (ISHIB)',NULL,NULL,NULL),(332,'2016-09-09','system',NULL,NULL,'Firenze University Press',NULL,NULL,NULL),(333,'2016-09-09','system',NULL,NULL,'History of Earth Sciences Society',NULL,NULL,NULL),(334,'2016-09-09','system',NULL,NULL,'History Today Ltd.',NULL,NULL,NULL),(335,'2016-09-09','system',NULL,NULL,'Journal of Music',NULL,NULL,NULL),(336,'2016-09-09','system',NULL,NULL,'University of Nebraska at Omaha',NULL,NULL,NULL),(337,'2016-09-09','system',NULL,NULL,'Journal of Indo-European Studies',NULL,NULL,NULL),(338,'2016-09-09','system',NULL,NULL,'Library Binding Institute',NULL,NULL,NULL),(339,'2016-09-09','system',NULL,NULL,'McFarland & Co. Inc.',NULL,NULL,NULL),(340,'2016-09-09','system',NULL,NULL,'Lyrasis',NULL,NULL,NULL),(341,'2016-09-09','system',NULL,NULL,'Amigos Library Services',NULL,NULL,NULL),(343,'2016-09-09','system',NULL,NULL,'Fabrizio Serra Editore',NULL,NULL,NULL),(344,'2016-09-09','system',NULL,NULL,'Aux Amateurs',NULL,NULL,NULL),(346,'2016-09-09','system',NULL,NULL,'National Affairs, Inc',NULL,NULL,NULL),(357,'2016-09-09','system',NULL,NULL,'Society of Chemical Industry',NULL,NULL,NULL),(347,'2016-09-09','system',NULL,NULL,'New Criterion',NULL,NULL,NULL),(348,'2016-09-09','system',NULL,NULL,'Casa Editrice Leo S. Olschki s.r.l.',NULL,NULL,NULL),(349,'2016-09-09','system',NULL,NULL,'Rhodes University, Department of Philosophy',NULL,NULL,NULL),(350,'2016-09-09','system',NULL,NULL,'Rocky Mountain Mathematics Consortium',NULL,NULL,NULL),(352,'2016-09-09','system',NULL,NULL,'Royal Irish Academy',NULL,NULL,NULL),(353,'2016-09-09','system',NULL,NULL,'Chadwyck-Healey',NULL,NULL,NULL),(354,'2016-09-09','system',NULL,NULL,'CSA illumina',NULL,NULL,NULL),(355,'2016-09-09','system',NULL,NULL,'New School for Social Research',NULL,NULL,NULL),(356,'2016-09-09','system',NULL,NULL,'Society of Biblical Literature',NULL,NULL,NULL),(358,'2016-09-09','system',NULL,NULL,'Stazione Zoologica Anton Dohrn',NULL,NULL,NULL),(359,'2016-09-09','system',NULL,NULL,'BioScientifica Ltd.',NULL,NULL,NULL),(360,'2016-09-09','system',NULL,NULL,'CASALINI LIBRI',NULL,NULL,NULL),(361,'2016-09-09','system',NULL,NULL,'Institute of Organic Chemistry',NULL,NULL,NULL),(362,'2016-09-09','system',NULL,NULL,'Columbia International Affairs Online ',NULL,NULL,NULL),(363,'2016-09-09','system',NULL,NULL,'Corporation for National Research Initiatives ',NULL,NULL,NULL),(364,'2016-09-09','system',NULL,NULL,'Tilgher-Genova',NULL,NULL,NULL),(365,'2016-09-09','system',NULL,NULL,'Emerald Group Publishing Limited',NULL,NULL,NULL),(366,'2016-09-09','system',NULL,NULL,'Geological Society of America',NULL,NULL,NULL),(367,'2016-09-09','system',NULL,NULL,'Institute of Mathematical Statistics',NULL,NULL,NULL),(368,'2016-09-09','system',NULL,NULL,'Institute of Pure and Applied Physics',NULL,NULL,NULL),(369,'2016-09-09','system',NULL,NULL,'JSTAGE',NULL,NULL,NULL),(370,'2016-09-09','system',NULL,NULL,'Metapress',NULL,NULL,NULL),(371,'2016-09-09','system',NULL,NULL,'Modern Language Association',NULL,NULL,NULL),(372,'2016-09-09','system',NULL,NULL,'Optical Society of America',NULL,NULL,NULL),(373,'2016-09-09','system',NULL,NULL,'University of British Columbia',NULL,NULL,NULL),(374,'2016-09-09','system',NULL,NULL,'University of New Mexico',NULL,NULL,NULL),(375,'2016-09-09','system',NULL,NULL,'Vandenhoeck & Ruprecht',NULL,NULL,NULL),(376,'2016-09-09','system',NULL,NULL,'Verlag Mohr Siebeck GmbH & Co. KG',NULL,NULL,NULL),(377,'2016-09-09','system',NULL,NULL,'Palgrave Macmillan',NULL,NULL,NULL),(378,'2016-09-09','system',NULL,NULL,'Vittorio Klostermann',NULL,NULL,NULL),(379,'2016-09-09','system',NULL,NULL,'Project Euclid',NULL,NULL,NULL),(380,'2016-09-09','system',NULL,NULL,'Psychonomic Society ',NULL,NULL,NULL),(411,'2016-09-09','system',NULL,NULL,'Cengage Learning',NULL,NULL,NULL),(382,'2016-09-09','system',NULL,NULL,'Infotrieve',NULL,NULL,NULL),(383,'2016-09-09','system',NULL,NULL,'Society of Automotive Engineers',NULL,NULL,NULL),(384,'2016-09-09','system',NULL,NULL,'Turpion Publications',NULL,NULL,NULL),(426,'2016-09-09','system',NULL,NULL,'Midwest Collaborative for Library Services',NULL,NULL,NULL),(386,'2016-09-09','system',NULL,NULL,'Lawrence Erlbaum Associates',NULL,NULL,NULL),(387,'2016-09-09','system',NULL,NULL,'Alphagraphics',NULL,NULL,NULL),(388,'2016-09-09','system',NULL,NULL,'Bellerophon Publications, Inc.',NULL,NULL,NULL),(389,'2016-09-09','system',NULL,NULL,'Boydell & Brewer Inc.',NULL,NULL,NULL),(390,'2016-09-09','system',NULL,NULL,'Carcanet Press',NULL,NULL,NULL),(391,'2016-09-09','system',NULL,NULL,'Feminist Studies',NULL,NULL,NULL),(393,'2016-09-09','system',NULL,NULL,'Dustbooks',NULL,NULL,NULL),(394,'2016-09-09','system',NULL,NULL,'Society for Applied Anthropology ',NULL,NULL,NULL),(395,'2016-09-09','system',NULL,NULL,'United Nations Publications',NULL,NULL,NULL),(396,'2016-09-09','system',NULL,NULL,'Wharton Research Data Services',NULL,NULL,NULL),(398,'2016-09-09','system',NULL,NULL,'Human Development',NULL,NULL,NULL),(399,'2016-09-09','system',NULL,NULL,'infoUSA Marketing, Inc.',NULL,NULL,NULL),(400,'2016-09-09','system',NULL,NULL,'Bowker',NULL,NULL,NULL),(402,'2016-09-09','system',NULL,NULL,'Brown University',NULL,NULL,NULL),(401,'2016-09-09','system',NULL,NULL,'Women Writers Project',NULL,NULL,NULL),(445,'2016-09-09','system',NULL,NULL,'Coutts',NULL,NULL,NULL),(446,'2016-09-09','system',NULL,NULL,'Numara Software, Inc.',NULL,NULL,NULL),(447,'2016-09-09','system',NULL,NULL,'Trinity College Library Dublin',NULL,NULL,NULL),(405,'2016-09-09','system',NULL,NULL,'Oldenbourg Wissenschaftsverlag ',NULL,NULL,NULL),(406,'2016-09-09','system',NULL,NULL,'Dow Jones',NULL,NULL,NULL),(412,'2016-09-09','system',NULL,NULL,'Financial Information Inc. (FII)',NULL,NULL,NULL),(408,'2016-09-09','system',NULL,NULL,'Jackson Publishing and Distribution',NULL,NULL,NULL),(409,'2016-09-09','system',NULL,NULL,'Elsevier Engineering Information, Inc. ',NULL,NULL,NULL),(410,'2016-09-09','system',NULL,NULL,'Eneclann Ltd.',NULL,NULL,NULL),(413,'2016-09-09','system',NULL,NULL,'UCLA Latin American Institute',NULL,NULL,NULL),(414,'2016-09-09','system',NULL,NULL,'Harmonie Park Press ',NULL,NULL,NULL),(415,'2016-09-09','system',NULL,NULL,'Harrassowitz',NULL,NULL,NULL),(416,'2016-09-09','system',NULL,NULL,'Thomson Reuters',NULL,NULL,NULL),(418,'2016-09-09','system',NULL,NULL,'Human Relations Area Files, Inc. ',NULL,NULL,NULL),(432,'2016-09-09','system',NULL,NULL,'Capital IQ',NULL,NULL,NULL),(419,'2016-09-09','system',NULL,NULL,'Society for Ethnomusicology',NULL,NULL,NULL),(420,'2016-09-09','system',NULL,NULL,'MSCI RiskMetrics',NULL,NULL,NULL),(421,'2016-09-09','system',NULL,NULL,'Rapid Multimedia',NULL,NULL,NULL),(422,'2016-09-09','system',NULL,NULL,'MSCI Inc',NULL,NULL,NULL),(423,'2016-09-09','system',NULL,NULL,'New England Journal of Medicine',NULL,NULL,NULL),(424,'2016-09-09','system',NULL,NULL,'NetLibrary',NULL,NULL,NULL),(425,'2016-09-09','system',NULL,NULL,'CARMA',NULL,NULL,NULL),(427,'2016-09-09','system',NULL,NULL,'Public Library of Science',NULL,NULL,NULL),(428,'2016-09-09','system',NULL,NULL,'Social Science Electronic Publishing',NULL,NULL,NULL),(429,'2016-09-09','system',NULL,NULL,'United Nations Industrial Develoipment Organization',NULL,NULL,NULL),(430,'2016-09-09','system',NULL,NULL,'University of Michigan Press',NULL,NULL,NULL),(431,'2016-09-09','system',NULL,NULL,'ORS Publishing, Inc.',NULL,NULL,NULL),(433,'2016-09-09','system',NULL,NULL,'McGraw-Hill',NULL,NULL,NULL),(434,'2016-09-09','system',NULL,NULL,'Biblical Archaeology Society',NULL,NULL,NULL),(435,'2016-09-09','system',NULL,NULL,'GeoLytics, Inc.',NULL,NULL,NULL),(436,'2016-09-09','system',NULL,NULL,'JoVE ',NULL,NULL,NULL),(437,'2016-09-09','system',NULL,NULL,'ICEsoft Technologies, Inc.',NULL,NULL,NULL),(438,'2016-09-09','system',NULL,NULL,'Films Media Group',NULL,NULL,NULL),(439,'2016-09-09','system',NULL,NULL,'Films on Demand',NULL,NULL,NULL),(440,'2016-09-09','system',NULL,NULL,'Connect Journals',NULL,NULL,NULL),(441,'2016-09-09','system',NULL,NULL,'Scuola Normale Superiore',NULL,NULL,NULL),(442,'2016-09-09','system',NULL,NULL,'Wolters Kluwer',NULL,NULL,NULL),(448,'2016-09-09','system',NULL,NULL,'Pier Professional',NULL,NULL,NULL),(449,'2016-09-09','system',NULL,NULL,'ABC News',NULL,NULL,NULL),(450,'2016-09-09','system',NULL,NULL,'University of Aberdeen ',NULL,NULL,NULL),(451,'2016-09-09','system',NULL,NULL,'BullFrog Films, Inc.',NULL,NULL,NULL),(453,'2016-09-09','system',NULL,NULL,'FirstSearch',NULL,NULL,NULL),(455,'2016-09-09','system',NULL,NULL,'History Cooperative ',NULL,NULL,NULL),(456,'2016-09-09','system',NULL,NULL,'Omohundro Institute of Early American History and Culture',NULL,NULL,NULL),(457,'2016-09-09','system',NULL,NULL,'Arms Control Association',NULL,NULL,NULL),(458,'2016-09-09','system',NULL,NULL,'Heritage Archives',NULL,NULL,NULL),(459,'2016-09-09','system',NULL,NULL,'International Historic Films, Inc.',NULL,NULL,NULL),(460,'2016-09-09','system',NULL,NULL,'Euromonitor International ',NULL,NULL,NULL),(461,'2016-09-09','system',NULL,NULL,'Safari Books Online',NULL,NULL,NULL),(462,'2016-09-09','system',NULL,NULL,'Mirabile',NULL,NULL,NULL),(466,'2016-09-09','system',NULL,NULL,'Publishing Technology',NULL,NULL,NULL),(463,'2016-09-09','system',NULL,NULL,'SageWorks, Inc',NULL,NULL,NULL),(464,'2016-09-09','system',NULL,NULL,'Johns Hopkins Universtiy Press',NULL,NULL,NULL),(465,'2016-09-09','system',NULL,NULL,'Knovel ',NULL,NULL,NULL),(467,'2016-09-09','system',NULL,NULL,'American Society of Nephrology',NULL,NULL,NULL),(468,'2016-09-09','system',NULL,NULL,'Water Envrionment Federation ',NULL,NULL,NULL),(469,'2016-09-09','system',NULL,NULL,'Refworks',NULL,NULL,NULL),(470,'2016-09-09','system',NULL,NULL,'Cinemagician Productions',NULL,NULL,NULL),(471,'2016-09-09','system',NULL,NULL,'Algorithmics',NULL,NULL,NULL),(472,'2016-09-09','system',NULL,NULL,'YBP Library Services ',NULL,NULL,NULL),(474,'2016-09-09','system',NULL,NULL,'Maydream Inc.',NULL,NULL,NULL),(475,'2016-09-09','system',NULL,NULL,'Organization for Economic Cooperation and Development',NULL,NULL,NULL),(476,'2016-09-09','system',NULL,NULL,'The Chronicle for Higher Education',NULL,NULL,NULL),(477,'2016-09-09','system',NULL,NULL,'Association for Research in Vision and Ophthalmologie (ARVO)',NULL,NULL,NULL),(478,'2016-09-09','system',NULL,NULL,'SRDS Media Solutions',NULL,NULL,NULL),(479,'2016-09-09','system',NULL,NULL,'Kantar Media',NULL,NULL,NULL),(480,'2016-09-09','system',NULL,NULL,'Peace & Justice Studies Association',NULL,NULL,NULL),(481,'2016-09-09','system',NULL,NULL,'Addison Publications Ltd.',NULL,NULL,NULL),(482,'2016-09-09','system',NULL,NULL,'Mutii-Science Publishing',NULL,NULL,NULL),(483,'2016-09-09','system',NULL,NULL,'ASM International',NULL,NULL,NULL),(484,'2016-09-09','system',NULL,NULL,'Verlag der Osterreichischen Akademie der Wissenschaften',NULL,NULL,NULL),(485,'2016-09-09','system',NULL,NULL,'Anthology of Recorded Music',NULL,NULL,NULL),(486,'2016-09-09','system',NULL,NULL,'Left Coast Press, Inc',NULL,NULL,NULL),(487,'2016-09-09','system',NULL,NULL,'Video Data Bank',NULL,NULL,NULL),(488,'2016-09-09','system',NULL,NULL,'Atlassian',NULL,NULL,NULL),(489,'2016-09-09','system',NULL,NULL,'medici.tv',NULL,NULL,NULL),(490,'2016-09-09','system',NULL,NULL,'Bar Ilan Research & Development Company Ltd',NULL,NULL,NULL),(491,'2016-09-09','system',NULL,NULL,'Primary Source Media',NULL,NULL,NULL),(492,'2016-09-09','system',NULL,NULL,'Ebrary',NULL,NULL,NULL),(493,'2016-09-09','system',NULL,NULL,'University of Michigan, Department of Mathematics',NULL,NULL,NULL),(494,'2016-09-09','system',NULL,NULL,'StataCorp LP ',NULL,NULL,NULL),(495,'2016-09-09','system',NULL,NULL,'L\' Enseignement Mathematique  ',NULL,NULL,NULL),(496,'2016-09-09','system',NULL,NULL,'Audio Engineering Society, Inc',NULL,NULL,NULL),(497,'2016-09-09','system',NULL,NULL,'LOCKSS',NULL,NULL,NULL),(498,'2016-09-09','system',NULL,NULL,'MUSEEC ',NULL,NULL,NULL),(499,'2016-09-09','system',NULL,NULL,'Mortgage Bankers Association',NULL,NULL,NULL),(500,'2016-09-09','system',NULL,NULL,'BibleWorks',NULL,NULL,NULL),(501,'2016-09-09','system',NULL,NULL,'National Library of Ireland',NULL,NULL,NULL),(502,'2016-09-09','system',NULL,NULL,'Scholars Press',NULL,NULL,NULL),(503,'2016-09-09','system',NULL,NULL,'Index to Jewish periodicals',NULL,NULL,NULL),(504,'2016-09-09','system',NULL,NULL,'Cold Spring Harbor Laboratory Press',NULL,NULL,NULL),(505, '2016-09-30', 'coral_test', NULL, NULL, 'Test Publisher', NULL, NULL, NULL);
/*!40000 ALTER TABLE `Organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrganizationHierarchy`
--

DROP TABLE IF EXISTS `OrganizationHierarchy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrganizationHierarchy` (
  `organizationID` int(11) NOT NULL,
  `parentOrganizationID` int(11) NOT NULL,
  PRIMARY KEY (`organizationID`,`parentOrganizationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrganizationHierarchy`
--

LOCK TABLES `OrganizationHierarchy` WRITE;
/*!40000 ALTER TABLE `OrganizationHierarchy` DISABLE KEYS */;
INSERT INTO `OrganizationHierarchy` VALUES (28,416),(35,411),(43,36),(44,442),(87,433),(154,466),(168,39),(228,29),(259,416),(267,422),(270,51),(301,39),(302,29),(353,51),(354,270),(370,42),(401,402),(406,51),(409,29),(417,416),(420,422),(424,42),(432,87),(439,438),(453,264),(462,297),(469,270),(478,479);
/*!40000 ALTER TABLE `OrganizationHierarchy` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrganizationRole`
--

DROP TABLE IF EXISTS `OrganizationRole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrganizationRole` (
  `organizationRoleID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`organizationRoleID`),
  UNIQUE KEY `organizationRoleID` (`organizationRoleID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrganizationRole`
--

LOCK TABLES `OrganizationRole` WRITE;
/*!40000 ALTER TABLE `OrganizationRole` DISABLE KEYS */;
INSERT INTO `OrganizationRole` VALUES (1,'Consortium'),(2,'Library'),(3,'Platform'),(4,'Provider'),(5,'Publisher'),(6,'Vendor');
/*!40000 ALTER TABLE `OrganizationRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrganizationRoleProfile`
--

DROP TABLE IF EXISTS `OrganizationRoleProfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrganizationRoleProfile` (
  `organizationID` int(11) NOT NULL,
  `organizationRoleID` int(11) NOT NULL,
  PRIMARY KEY (`organizationID`,`organizationRoleID`),
  KEY `organizationRoleID` (`organizationRoleID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrganizationRoleProfile`
--

LOCK TABLES `OrganizationRoleProfile` WRITE;
/*!40000 ALTER TABLE `OrganizationRoleProfile` DISABLE KEYS */;
/*!40000 ALTER TABLE `OrganizationRoleProfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Privilege`
--

DROP TABLE IF EXISTS `Privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Privilege` (
  `privilegeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`privilegeID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Privilege`
--

LOCK TABLES `Privilege` WRITE;
/*!40000 ALTER TABLE `Privilege` DISABLE KEYS */;
INSERT INTO `Privilege` VALUES (1,'admin'),(2,'add/edit'),(3,'view only');
/*!40000 ALTER TABLE `Privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `loginID` varchar(50) NOT NULL,
  `privilegeID` int(11) DEFAULT NULL,
  `firstName` varchar(50) DEFAULT NULL,
  `lastName` varchar(50) DEFAULT NULL,
  `accountTabIndicator` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`loginID`) USING BTREE,
  KEY `roleID` (`privilegeID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES ('coral_test',1,NULL,NULL,0);
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `coral_reports_test`
--

USE `coral_reports_test`;

--
-- Table structure for table `Report`
--

DROP TABLE IF EXISTS `Report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Report` (
  `reportID` int(11) NOT NULL AUTO_INCREMENT,
  `reportName` varchar(45) NOT NULL,
  `defaultRecPageNumber` int(11) DEFAULT '100',
  `excelOnlyInd` tinyint(1) DEFAULT NULL,
  `reportDatabaseName` varchar(45) NOT NULL,
  PRIMARY KEY (`reportID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Report`
--

LOCK TABLES `Report` WRITE;
/*!40000 ALTER TABLE `Report` DISABLE KEYS */;
INSERT INTO `Report` VALUES (1,'Usage Statistics by Titles',100,0,'usageDatabase'),(2,'Usage Statistics by Provider / Publisher',100,0,'usageDatabase'),(3,'Usage Statistics - Provider Rollup',100,0,'usageDatabase'),(4,'Usage Statistics - Publisher Rollup',100,0,'usageDatabase'),(5,'Usage Statistics - Top Resource Requests',100,0,'usageDatabase'),(6,'Usage Statistics - Yearly Usage Statistics',100,0,'usageDatabase');
/*!40000 ALTER TABLE `Report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ReportGroupingColumn`
--

DROP TABLE IF EXISTS `ReportGroupingColumn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ReportGroupingColumn` (
  `reportID` int(11) NOT NULL,
  `reportGroupingColumnName` varchar(45) NOT NULL,
  `reportGroupingColumnID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`reportGroupingColumnID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ReportGroupingColumn`
--

LOCK TABLES `ReportGroupingColumn` WRITE;
/*!40000 ALTER TABLE `ReportGroupingColumn` DISABLE KEYS */;
/*!40000 ALTER TABLE `ReportGroupingColumn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ReportParameter`
--

DROP TABLE IF EXISTS `ReportParameter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ReportParameter` (
  `reportParameterID` int(11) NOT NULL AUTO_INCREMENT,
  `parameterTypeCode` varchar(45) DEFAULT NULL,
  `parameterDisplayPrompt` varchar(45) DEFAULT NULL,
  `parameterAddWhereClause` varchar(500) DEFAULT NULL,
  `parameterAddWhereNumber` int(11) DEFAULT NULL,
  `requiredInd` tinyint(1) DEFAULT NULL,
  `parameterSQLStatement` text,
  `parameterSQLRestriction` text,
  PRIMARY KEY (`reportParameterID`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ReportParameter`
--

LOCK TABLES `ReportParameter` WRITE;
/*!40000 ALTER TABLE `ReportParameter` DISABLE KEYS */;
INSERT INTO `ReportParameter` VALUES (1,'chk','Do not adjust numbers for use violations','Overriden',0,0,'',''),(2,'txt','ISSN/ISBN/DOI','(ti2.identifier = \'PARM\' OR ti2.identifier = REPLACE(\'PARM\',\"-\",\"\"))',1,0,'',''),(3,'txt','Title Search','upper(t2.title) like upper(\'%PARM%\')',1,0,'',''),(4,'dd','Provider / Publisher','(concat(\'PL_\', CAST(Platform.platformID AS CHAR)) = \'PARM\' OR concat(\'PB_\', CAST(pp.publisherPlatformID AS CHAR)) = \'PARM\')',0,0,'SELECT concat(\'PL_\', CAST(Platform.platformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM Platform WHERE reportDropDownInd = 1 UNION SELECT concat(\'PB_\', CAST(publisherPlatformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM PublisherPlatform WHERE reportDropDownInd = 1 ORDER BY 3',''),(5,'ms','Provider','concat(\'PL_\', CAST(Platform.platformID AS CHAR)) in (\'PARM\')',0,0,'SELECT concat(\'PL_\', CAST(platformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM Platform WHERE reportDropDownInd = 1 ORDER BY 3',''),(6,'ms','Publisher','concat(\'PB_\', CAST(pp.publisherPlatformID AS CHAR)) in (\'PARM\')',0,0,'SELECT GROUP_CONCAT(DISTINCT concat(\'PB_\', CAST(publisherPlatformID AS CHAR)) ORDER BY publisherPlatformID DESC SEPARATOR \', \'), reportDisplayName, upper(reportDisplayName) FROM PublisherPlatform WHERE reportDropDownInd = 1 GROUP BY reportDisplayName ORDER BY 3',''),(7,'dd','Limit','limit',0,1,'SELECT 25,25 union SELECT 50,50 union SELECT 100,100 order by 1',''),(8,'dd','Year','mus.year = \'PARM\'',0,0,'SELECT distinct year, year FROM YearlyUsageSummary ORDER BY 1 asc',''),(9,'dd','Year','mus.year = \'PARM\'',0,0,'SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')'),(10,'dd','Year','mus.year = \'PARM\'',0,0,'SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc',''),(11,'dd','Year','yus.year = \'PARM\'',0,0,'SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')'),(12,'dd','Date Range','',0,1,'SELECT distinct year, year FROM YearlyUsageSummary ORDER BY 1 asc',''),(13,'dd','Date Range','',0,1,'SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')'),(14,'dd','Date Range','',0,1,'SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc',''),(15,'dd','Resource Type','t.resourceType= \'PARM\'',0,0,'SELECT distinct resourceType, resourceType FROM Title ORDER BY 1 asc','');
/*!40000 ALTER TABLE `ReportParameter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ReportParameterMap`
--

DROP TABLE IF EXISTS `ReportParameterMap`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ReportParameterMap` (
  `reportID` int(11) NOT NULL,
  `reportParameterID` int(11) NOT NULL AUTO_INCREMENT,
  `parentReportParameterID` int(11) DEFAULT NULL,
  PRIMARY KEY (`reportID`,`reportParameterID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ReportParameterMap`
--

LOCK TABLES `ReportParameterMap` WRITE;
/*!40000 ALTER TABLE `ReportParameterMap` DISABLE KEYS */;
INSERT INTO `ReportParameterMap` VALUES (1,1,0),(1,2,0),(1,3,0),(1,12,0),(1,15,0),(2,1,0),(2,4,0),(2,13,4),(2,15,0),(3,5,0),(3,14,0),(4,6,0),(4,14,0),(5,1,0),(5,7,0),(5,4,0),(5,11,4),(5,15,0),(6,1,0),(6,4,0),(6,11,4),(6,15,4);
/*!40000 ALTER TABLE `ReportParameterMap` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ReportSum`
--

DROP TABLE IF EXISTS `ReportSum`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ReportSum` (
  `reportID` int(11) NOT NULL,
  `reportColumnName` varchar(45) NOT NULL,
  `reportAction` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`reportID`,`reportColumnName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ReportSum`
--

LOCK TABLES `ReportSum` WRITE;
/*!40000 ALTER TABLE `ReportSum` DISABLE KEYS */;
INSERT INTO `ReportSum` VALUES (1,'JAN','sum'),(1,'FEB','sum'),(1,'MAR','sum'),(1,'APR','sum'),(1,'MAY','sum'),(1,'JUN','sum'),(1,'JUL','sum'),(1,'AUG','sum'),(1,'SEP','sum'),(1,'OCT','sum'),(1,'NOV','sum'),(1,'DEC','sum'),(1,'QUERY_TOTAL','sum'),(1,'YTD_HTML','sum'),(1,'YTD_PDF','sum'),(1,'YTD_TOTAL','sum'),(2,'JAN','sum'),(2,'FEB','sum'),(2,'MAR','sum'),(2,'APR','sum'),(2,'MAY','sum'),(2,'JUN','sum'),(2,'JUL','sum'),(2,'AUG','sum'),(2,'SEP','sum'),(2,'OCT','sum'),(2,'NOV','sum'),(2,'DEC','sum'),(2,'QUERY_TOTAL','sum'),(2,'YTD_HTML','sum'),(2,'YTD_PDF','sum'),(2,'YTD_TOTAL','sum');
/*!40000 ALTER TABLE `ReportSum` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `coral_resources_test`
--

USE `coral_resources_test`;

--
-- Table structure for table `AccessMethod`
--

DROP TABLE IF EXISTS `AccessMethod`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AccessMethod` (
  `accessMethodID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`accessMethodID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AccessMethod`
--

LOCK TABLES `AccessMethod` WRITE;
/*!40000 ALTER TABLE `AccessMethod` DISABLE KEYS */;
INSERT INTO `AccessMethod` VALUES (1,'Standalone CD'),(2,'External Host'),(3,'Local Host');
/*!40000 ALTER TABLE `AccessMethod` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AcquisitionType`
--

DROP TABLE IF EXISTS `AcquisitionType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AcquisitionType` (
  `acquisitionTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`acquisitionTypeID`),
  UNIQUE KEY `acquisitionTypeID` (`acquisitionTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AcquisitionType`
--

LOCK TABLES `AcquisitionType` WRITE;
/*!40000 ALTER TABLE `AcquisitionType` DISABLE KEYS */;
INSERT INTO `AcquisitionType` VALUES (1,'Paid'),(2,'Free'),(3,'Trial');
/*!40000 ALTER TABLE `AcquisitionType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AdministeringSite`
--

DROP TABLE IF EXISTS `AdministeringSite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AdministeringSite` (
  `administeringSiteID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`administeringSiteID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AdministeringSite`
--

LOCK TABLES `AdministeringSite` WRITE;
/*!40000 ALTER TABLE `AdministeringSite` DISABLE KEYS */;
INSERT INTO `AdministeringSite` VALUES (1,'Main Library');
/*!40000 ALTER TABLE `AdministeringSite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AlertDaysInAdvance`
--

DROP TABLE IF EXISTS `AlertDaysInAdvance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AlertDaysInAdvance` (
  `alertDaysInAdvanceID` int(11) NOT NULL AUTO_INCREMENT,
  `daysInAdvanceNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`alertDaysInAdvanceID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AlertDaysInAdvance`
--

LOCK TABLES `AlertDaysInAdvance` WRITE;
/*!40000 ALTER TABLE `AlertDaysInAdvance` DISABLE KEYS */;
INSERT INTO `AlertDaysInAdvance` VALUES (1,30),(2,60),(3,90);
/*!40000 ALTER TABLE `AlertDaysInAdvance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AlertEmailAddress`
--

DROP TABLE IF EXISTS `AlertEmailAddress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AlertEmailAddress` (
  `alertEmailAddressID` int(11) NOT NULL AUTO_INCREMENT,
  `emailAddress` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`alertEmailAddressID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AlertEmailAddress`
--

LOCK TABLES `AlertEmailAddress` WRITE;
/*!40000 ALTER TABLE `AlertEmailAddress` DISABLE KEYS */;
/*!40000 ALTER TABLE `AlertEmailAddress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Alias`
--

DROP TABLE IF EXISTS `Alias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Alias` (
  `aliasID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `aliasTypeID` int(11) DEFAULT NULL,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`aliasID`),
  KEY `Index_resourceID` (`resourceID`),
  KEY `Index_aliasTypeID` (`aliasTypeID`),
  KEY `shortName` (`shortName`),
  KEY `Index_All` (`resourceID`,`aliasTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Alias`
--

LOCK TABLES `Alias` WRITE;
/*!40000 ALTER TABLE `Alias` DISABLE KEYS */;
/*!40000 ALTER TABLE `Alias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AliasType`
--

DROP TABLE IF EXISTS `AliasType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AliasType` (
  `aliasTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`aliasTypeID`),
  UNIQUE KEY `aliasTypeID` (`aliasTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AliasType`
--

LOCK TABLES `AliasType` WRITE;
/*!40000 ALTER TABLE `AliasType` DISABLE KEYS */;
INSERT INTO `AliasType` VALUES (1,'Abbreviation'),(2,'Alternate Name'),(3,'Name Change');
/*!40000 ALTER TABLE `AliasType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Attachment`
--

DROP TABLE IF EXISTS `Attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Attachment` (
  `attachmentID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `attachmentTypeID` int(11) DEFAULT NULL,
  `shortName` varchar(200) DEFAULT NULL,
  `descriptionText` text,
  `attachmentURL` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`attachmentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Attachment`
--

LOCK TABLES `Attachment` WRITE;
/*!40000 ALTER TABLE `Attachment` DISABLE KEYS */;
/*!40000 ALTER TABLE `Attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AttachmentType`
--

DROP TABLE IF EXISTS `AttachmentType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AttachmentType` (
  `attachmentTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`attachmentTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AttachmentType`
--

LOCK TABLES `AttachmentType` WRITE;
/*!40000 ALTER TABLE `AttachmentType` DISABLE KEYS */;
INSERT INTO `AttachmentType` VALUES (1,'Email'),(2,'User Guide'),(3,'Title List'),(4,'General');
/*!40000 ALTER TABLE `AttachmentType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthenticationType`
--

DROP TABLE IF EXISTS `AuthenticationType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthenticationType` (
  `authenticationTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`authenticationTypeID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthenticationType`
--

LOCK TABLES `AuthenticationType` WRITE;
/*!40000 ALTER TABLE `AuthenticationType` DISABLE KEYS */;
INSERT INTO `AuthenticationType` VALUES (1,'IP Address'),(2,'Username'),(3,'Referring URL');
/*!40000 ALTER TABLE `AuthenticationType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `AuthorizedSite`
--

DROP TABLE IF EXISTS `AuthorizedSite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AuthorizedSite` (
  `authorizedSiteID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`authorizedSiteID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AuthorizedSite`
--

LOCK TABLES `AuthorizedSite` WRITE;
/*!40000 ALTER TABLE `AuthorizedSite` DISABLE KEYS */;
INSERT INTO `AuthorizedSite` VALUES (1,'Main Campus');
/*!40000 ALTER TABLE `AuthorizedSite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CatalogingStatus`
--

DROP TABLE IF EXISTS `CatalogingStatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CatalogingStatus` (
  `catalogingStatusID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`catalogingStatusID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CatalogingStatus`
--

LOCK TABLES `CatalogingStatus` WRITE;
/*!40000 ALTER TABLE `CatalogingStatus` DISABLE KEYS */;
INSERT INTO `CatalogingStatus` VALUES (1,'Completed'),(2,'Ongoing'),(3,'Rejected');
/*!40000 ALTER TABLE `CatalogingStatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CatalogingType`
--

DROP TABLE IF EXISTS `CatalogingType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CatalogingType` (
  `catalogingTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`catalogingTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CatalogingType`
--

LOCK TABLES `CatalogingType` WRITE;
/*!40000 ALTER TABLE `CatalogingType` DISABLE KEYS */;
INSERT INTO `CatalogingType` VALUES (1,'Batch'),(2,'Manual'),(3,'MARCit');
/*!40000 ALTER TABLE `CatalogingType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Contact`
--

DROP TABLE IF EXISTS `Contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Contact` (
  `contactID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) NOT NULL,
  `lastUpdateDate` date DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `title` varchar(150) DEFAULT NULL,
  `addressText` varchar(300) DEFAULT NULL,
  `phoneNumber` varchar(50) DEFAULT NULL,
  `altPhoneNumber` varchar(50) DEFAULT NULL,
  `faxNumber` varchar(50) DEFAULT NULL,
  `emailAddress` varchar(100) DEFAULT NULL,
  `archiveDate` date DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`contactID`),
  UNIQUE KEY `contactID` (`contactID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Contact`
--

LOCK TABLES `Contact` WRITE;
/*!40000 ALTER TABLE `Contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `Contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ContactRole`
--

DROP TABLE IF EXISTS `ContactRole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ContactRole` (
  `contactRoleID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`contactRoleID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ContactRole`
--

LOCK TABLES `ContactRole` WRITE;
/*!40000 ALTER TABLE `ContactRole` DISABLE KEYS */;
INSERT INTO `ContactRole` VALUES (1,'Support'),(2,'Accounting'),(3,'General'),(4,'Licensing'),(5,'Sales'),(6,'Training');
/*!40000 ALTER TABLE `ContactRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ContactRoleProfile`
--

DROP TABLE IF EXISTS `ContactRoleProfile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ContactRoleProfile` (
  `contactID` int(10) unsigned NOT NULL,
  `contactRoleID` int(10) unsigned NOT NULL,
  PRIMARY KEY (`contactID`,`contactRoleID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ContactRoleProfile`
--

LOCK TABLES `ContactRoleProfile` WRITE;
/*!40000 ALTER TABLE `ContactRoleProfile` DISABLE KEYS */;
/*!40000 ALTER TABLE `ContactRoleProfile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `CostDetails`
--

DROP TABLE IF EXISTS `CostDetails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CostDetails` (
  `costDetailsID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) NOT NULL,
  PRIMARY KEY (`costDetailsID`),
  KEY `costDetailsID` (`costDetailsID`),
  KEY `Index_shortName` (`shortName`),
  KEY `Index_All` (`costDetailsID`,`shortName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CostDetails`
--

LOCK TABLES `CostDetails` WRITE;
/*!40000 ALTER TABLE `CostDetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `CostDetails` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Currency`
--

DROP TABLE IF EXISTS `Currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Currency` (
  `currencyCode` varchar(3) NOT NULL,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`currencyCode`),
  UNIQUE KEY `currencyCode` (`currencyCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Currency`
--

LOCK TABLES `Currency` WRITE;
/*!40000 ALTER TABLE `Currency` DISABLE KEYS */;
INSERT INTO `Currency` VALUES ('USD','United States Dollar'),('EUR','Euro'),('GBP','Great Britain (UK) Pound'),('CAD','Canadian Dollar'),('ARS','Argentine Peso'),('AUD','Australian Dollar'),('SEK','Swedish Krona');
/*!40000 ALTER TABLE `Currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DetailedSubject`
--

DROP TABLE IF EXISTS `DetailedSubject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DetailedSubject` (
  `detailedSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`detailedSubjectID`),
  KEY `detailedSubjectID` (`detailedSubjectID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DetailedSubject`
--

LOCK TABLES `DetailedSubject` WRITE;
/*!40000 ALTER TABLE `DetailedSubject` DISABLE KEYS */;
/*!40000 ALTER TABLE `DetailedSubject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Downtime`
--

DROP TABLE IF EXISTS `Downtime`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Downtime` (
  `downtimeID` int(11) NOT NULL AUTO_INCREMENT,
  `issueID` int(11) DEFAULT NULL,
  `entityID` int(11) NOT NULL,
  `entityTypeID` int(11) NOT NULL DEFAULT '2',
  `creatorID` varchar(80) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime DEFAULT NULL,
  `downtimeTypeID` int(11) NOT NULL,
  `note` text,
  PRIMARY KEY (`downtimeID`),
  KEY `IssueID` (`issueID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Downtime`
--

LOCK TABLES `Downtime` WRITE;
/*!40000 ALTER TABLE `Downtime` DISABLE KEYS */;
/*!40000 ALTER TABLE `Downtime` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `DowntimeType`
--

DROP TABLE IF EXISTS `DowntimeType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DowntimeType` (
  `downtimeTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(80) NOT NULL,
  PRIMARY KEY (`downtimeTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `DowntimeType`
--

LOCK TABLES `DowntimeType` WRITE;
/*!40000 ALTER TABLE `DowntimeType` DISABLE KEYS */;
/*!40000 ALTER TABLE `DowntimeType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExternalLogin`
--

DROP TABLE IF EXISTS `ExternalLogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExternalLogin` (
  `externalLoginID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `externalLoginTypeID` int(11) DEFAULT NULL,
  `updateDate` date DEFAULT NULL,
  `loginURL` varchar(150) DEFAULT NULL,
  `emailAddress` varchar(150) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`externalLoginID`),
  UNIQUE KEY `externalLoginID` (`externalLoginID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExternalLogin`
--

LOCK TABLES `ExternalLogin` WRITE;
/*!40000 ALTER TABLE `ExternalLogin` DISABLE KEYS */;
/*!40000 ALTER TABLE `ExternalLogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ExternalLoginType`
--

DROP TABLE IF EXISTS `ExternalLoginType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExternalLoginType` (
  `externalLoginTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`externalLoginTypeID`),
  UNIQUE KEY `externalLoginTypeID` (`externalLoginTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExternalLoginType`
--

LOCK TABLES `ExternalLoginType` WRITE;
/*!40000 ALTER TABLE `ExternalLoginType` DISABLE KEYS */;
INSERT INTO `ExternalLoginType` VALUES (1,'Admin'),(2,'FTP'),(3,'Statistics'),(4,'Support');
/*!40000 ALTER TABLE `ExternalLoginType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Fund`
--

DROP TABLE IF EXISTS `Fund`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Fund` (
  `fundID` int(11) NOT NULL AUTO_INCREMENT,
  `fundCode` varchar(20) DEFAULT NULL,
  `shortName` varchar(200) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`fundID`),
  UNIQUE KEY `fundCode` (`fundCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Fund`
--

LOCK TABLES `Fund` WRITE;
/*!40000 ALTER TABLE `Fund` DISABLE KEYS */;
/*!40000 ALTER TABLE `Fund` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GeneralDetailSubjectLink`
--

DROP TABLE IF EXISTS `GeneralDetailSubjectLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GeneralDetailSubjectLink` (
  `generalDetailSubjectLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `generalSubjectID` int(11) DEFAULT NULL,
  `detailedSubjectID` int(11) DEFAULT NULL,
  PRIMARY KEY (`generalDetailSubjectLinkID`),
  KEY `generalDetailSubjectLinkID` (`generalDetailSubjectLinkID`),
  KEY `Index_All` (`generalSubjectID`,`detailedSubjectID`),
  KEY `Index_generalSubject` (`generalSubjectID`),
  KEY `Index_detailedSubject` (`detailedSubjectID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GeneralDetailSubjectLink`
--

LOCK TABLES `GeneralDetailSubjectLink` WRITE;
/*!40000 ALTER TABLE `GeneralDetailSubjectLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `GeneralDetailSubjectLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `GeneralSubject`
--

DROP TABLE IF EXISTS `GeneralSubject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GeneralSubject` (
  `generalSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`generalSubjectID`),
  KEY `generalSubjectID` (`generalSubjectID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `GeneralSubject`
--

LOCK TABLES `GeneralSubject` WRITE;
/*!40000 ALTER TABLE `GeneralSubject` DISABLE KEYS */;
/*!40000 ALTER TABLE `GeneralSubject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ImportConfig`
--

DROP TABLE IF EXISTS `ImportConfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ImportConfig` (
  `importConfigID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  `configuration` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`importConfigID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ImportConfig`
--

LOCK TABLES `ImportConfig` WRITE;
/*!40000 ALTER TABLE `ImportConfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `ImportConfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IsbnOrIssn`
--

DROP TABLE IF EXISTS `IsbnOrIssn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IsbnOrIssn` (
  `isbnOrIssnID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `isbnOrIssn` varchar(45) NOT NULL,
  PRIMARY KEY (`isbnOrIssnID`),
  KEY `resourceID` (`resourceID`),
  KEY `isbnOrIssn` (`isbnOrIssn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IsbnOrIssn`
--

LOCK TABLES `IsbnOrIssn` WRITE;
/*!40000 ALTER TABLE `IsbnOrIssn` DISABLE KEYS */;
/*!40000 ALTER TABLE `IsbnOrIssn` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Issue`
--

DROP TABLE IF EXISTS `Issue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Issue` (
  `issueID` int(11) NOT NULL AUTO_INCREMENT,
  `creatorID` varchar(20) NOT NULL,
  `subjectText` varchar(80) NOT NULL,
  `bodyText` text NOT NULL,
  `reminderInterval` int(11) DEFAULT NULL,
  `dateCreated` datetime NOT NULL,
  `dateClosed` datetime DEFAULT NULL,
  `resolutionText` text,
  PRIMARY KEY (`issueID`),
  KEY `creatorID` (`creatorID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Issue`
--

LOCK TABLES `Issue` WRITE;
/*!40000 ALTER TABLE `Issue` DISABLE KEYS */;
/*!40000 ALTER TABLE `Issue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IssueContact`
--

DROP TABLE IF EXISTS `IssueContact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IssueContact` (
  `issueContactID` int(11) NOT NULL AUTO_INCREMENT,
  `issueID` int(11) NOT NULL,
  `contactID` int(11) NOT NULL,
  PRIMARY KEY (`issueContactID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IssueContact`
--

LOCK TABLES `IssueContact` WRITE;
/*!40000 ALTER TABLE `IssueContact` DISABLE KEYS */;
/*!40000 ALTER TABLE `IssueContact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IssueEmail`
--

DROP TABLE IF EXISTS `IssueEmail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IssueEmail` (
  `issueEmailID` int(11) NOT NULL AUTO_INCREMENT,
  `issueID` int(11) NOT NULL,
  `email` varchar(120) NOT NULL,
  PRIMARY KEY (`issueEmailID`),
  KEY `IssueID` (`issueID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IssueEmail`
--

LOCK TABLES `IssueEmail` WRITE;
/*!40000 ALTER TABLE `IssueEmail` DISABLE KEYS */;
/*!40000 ALTER TABLE `IssueEmail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IssueEntityType`
--

DROP TABLE IF EXISTS `IssueEntityType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IssueEntityType` (
  `entityTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `entityName` varchar(80) NOT NULL,
  PRIMARY KEY (`entityTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IssueEntityType`
--

LOCK TABLES `IssueEntityType` WRITE;
/*!40000 ALTER TABLE `IssueEntityType` DISABLE KEYS */;
/*!40000 ALTER TABLE `IssueEntityType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `IssueRelationship`
--

DROP TABLE IF EXISTS `IssueRelationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `IssueRelationship` (
  `issueRelationshipID` int(11) NOT NULL AUTO_INCREMENT,
  `issueID` int(11) NOT NULL,
  `entityID` int(11) NOT NULL,
  `entityTypeID` int(11) NOT NULL,
  PRIMARY KEY (`issueRelationshipID`),
  KEY `issueID` (`issueID`,`entityID`,`entityTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `IssueRelationship`
--

LOCK TABLES `IssueRelationship` WRITE;
/*!40000 ALTER TABLE `IssueRelationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `IssueRelationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LicenseStatus`
--

DROP TABLE IF EXISTS `LicenseStatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LicenseStatus` (
  `licenseStatusID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`licenseStatusID`),
  UNIQUE KEY `licenseStatusID` (`licenseStatusID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LicenseStatus`
--

LOCK TABLES `LicenseStatus` WRITE;
/*!40000 ALTER TABLE `LicenseStatus` DISABLE KEYS */;
INSERT INTO `LicenseStatus` VALUES (1,'Pending'),(2,'Completed'),(3,'No License Required');
/*!40000 ALTER TABLE `LicenseStatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `NoteType`
--

DROP TABLE IF EXISTS `NoteType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NoteType` (
  `noteTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`noteTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `NoteType`
--

LOCK TABLES `NoteType` WRITE;
/*!40000 ALTER TABLE `NoteType` DISABLE KEYS */;
INSERT INTO `NoteType` VALUES (1,'Product Details'),(2,'Acquisition Details'),(3,'Access Details'),(4,'General'),(5,'Licensing Details'),(6,'Initial Note');
/*!40000 ALTER TABLE `NoteType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrderType`
--

DROP TABLE IF EXISTS `OrderType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrderType` (
  `orderTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`orderTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrderType`
--

LOCK TABLES `OrderType` WRITE;
/*!40000 ALTER TABLE `OrderType` DISABLE KEYS */;
INSERT INTO `OrderType` VALUES (1,'Ongoing'),(2,'One Time');
/*!40000 ALTER TABLE `OrderType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrgNameMapping`
--

DROP TABLE IF EXISTS `OrgNameMapping`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrgNameMapping` (
  `orgNameMappingID` int(11) NOT NULL AUTO_INCREMENT,
  `importConfigID` int(11) NOT NULL,
  `orgNameImported` varchar(200) DEFAULT NULL,
  `orgNameMapped` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`orgNameMappingID`),
  KEY `importConfigID` (`importConfigID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrgNameMapping`
--

LOCK TABLES `OrgNameMapping` WRITE;
/*!40000 ALTER TABLE `OrgNameMapping` DISABLE KEYS */;
/*!40000 ALTER TABLE `OrgNameMapping` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Organization`
--

DROP TABLE IF EXISTS `Organization`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Organization` (
  `organizationID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY (`organizationID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=505 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Organization`
--

LOCK TABLES `Organization` WRITE;
/*!40000 ALTER TABLE `Organization` DISABLE KEYS */;
INSERT INTO `Organization` VALUES (2,'Institute of Physics'),(3,'American Institute of Aeronautics and Astronautics'),(4,'American Physical Society'),(5,'American Society of Civil Engineers'),(6,'American Insitute of Physics'),(7,'Society for Industrial and Applied Mathematics'),(8,'TNS Media Intelligence'),(9,'Chemical Abstracts Service'),(10,'Risk Management Association'),(11,'American Concrete Institute'),(12,'American Association for Cancer Research'),(13,'Institution of Engineering and Technology'),(14,'American Economic Association'),(15,'American Mathematical Society'),(16,'American Medical Association'),(18,'Consejo Superior de Investigaciones Cientificas'),(19,'American Meteorological Society'),(20,'American Library Association'),(21,'American Society for Testing and Materials'),(22,'Association of Research Libraries'),(23,'American Society of Limnology and Oceanography'),(24,'Tablet Publishing'),(25,'American Psychological Association'),(26,'American Council of Learned Societies'),(27,'American Association for the Advancement of Science'),(28,'Thomson Healthcare and Science'),(29,'Elsevier'),(30,'JSTOR'),(31,'SAGE Publications'),(32,'American Geophysical Union'),(33,'Annual Reviews'),(34,'BioOne'),(35,'Gale'),(36,'Wiley'),(37,'Oxford University Press'),(38,'Springer'),(39,'Taylor and Francis'),(40,'Stanford University'),(41,'University of California Press'),(42,'EBSCO Publishing'),(43,'Blackwell Publishing'),(44,'Ovid'),(45,'Project Muse'),(46,'American Fisheries Society'),(47,'Neilson Journals Publishing'),(48,'GuideStar USA, Inc'),(49,'Alexander Street Press, LLC'),(50,'Informa Healthcare USA, Inc'),(51,'ProQuest LLC'),(52,'Accessible Archives Inc'),(53,'ACCU Weather Sales and Services, Inc'),(54,'Adam Matthew Digital Ltd'),(55,'Akademiai Kiado'),(56,'World Trade Press'),(57,'World Scientific'),(58,'Walter de Gruyter'),(59,'Cambridge University Press'),(60,'GeoScienceWorld'),(61,'IEEE'),(62,'Yankelovich Inc'),(63,'Nature Publishing Group'),(64,'Verlag der Zeitschrift fur Naturforschung '),(65,'White Horse Press'),(66,'Verlag C.H. Beck'),(67,'Vault, Inc'),(68,'Value Line, Inc'),(69,'Vanderbilt University'),(70,'Uniworld Business Publications, Inc'),(71,'Universum USA'),(72,'University of Wisconsin Press'),(73,'University of Virginia Press'),(74,'University of Toronto Press Inc'),(75,'University of Toronto'),(76,'University of Pittsburgh'),(77,'University of Illinois Press'),(78,'University of Chicago Press'),(79,'University of Barcelona'),(80,'UCLA Chicano Studies Research Center Press'),(81,'Transportation Research Board'),(82,'Trans Tech Publications'),(83,'Thomas Telford Ltd'),(84,'Thesaurus Linguae Graecae'),(85,'Tetrad Computer Applications Inc'),(86,'Swank Motion Pictures, Inc'),(87,'Standard and Poors'),(88,'SPIE'),(89,'European Society of Endocrinology'),(90,'Society of Environmental Toxicology and Chemistry'),(91,'Society of Antiquaries of Scotland'),(92,'Society for Reproduction and Fertility'),(93,'Society for Neuroscience'),(94,'Society for Leukocyte Biology'),(95,'Society for General Microbiology'),(96,'Society for Experimental Biology and Medicine'),(97,'Society for Endocrinology'),(98,'Societe Mathematique de France'),(99,'Social Explorer'),(404,'SETAC'),(101,'Swiss Chemical Society'),(102,'Scholarly Digital Editions'),(103,'Royal Society of London'),(104,'Royal Society of Chemistry'),(105,'Roper Center for Public Opinion Research'),(106,'Rockefeller University Press'),(107,'Rivista di Studi italiani'),(108,'Reuters Loan Pricing Corporation'),(109,'Religious and Theological Abstracts, Inc'),(110,'Psychoanalytic Electronic Publishing Inc'),(111,'Cornell University Library'),(112,'Preservation Technologies LP'),(113,'Portland Press Limited'),(114,'ITHAKA'),(115,'Philosophy Documentation Center'),(116,'Peeters Publishers'),(117,'Paratext'),(118,'Mathematical Sciences Publishers'),(119,'Oxford Centre of Hebrew and Jewish Studies'),(120,'NewsBank, Inc'),(121,'Massachusetts Medical Society'),(122,'Naxos of America, Inc.'),(123,'National Research Council of Canada'),(124,'National Gallery Company Ltd'),(125,'National Academy of Sciences'),(126,'Mintel International Group Limited'),(127,'Metropolitan Opera'),(128,'M.E. Sharpe, Inc'),(129,'Mergent, Inc'),(130,'Mediamark Research and Intelligence, LLC'),(131,'Mary Ann Liebert, Inc'),(132,'MIT Press'),(133,'MarketResearch.com, Inc'),(134,'Marine Biological Laboratory'),(135,'W.S. Maney and Son Ltd'),(136,'Manchester University Press'),(137,'Lord Music Reference Inc'),(138,'Liverpool University Press'),(139,'Seminario Matematico of the University of Padua'),(140,'Library of Congress, Cataloging Distribution Service'),(141,'LexisNexis'),(142,'Corporacion Latinobarometro'),(143,'Landes Bioscience'),(144,'Keesings Worldwide, LLC'),(145,'Karger'),(146,'John Benjamins Publishing Company'),(147,'Irish Newspaper Archives Ltd'),(148,'IPA Source, LLC'),(149,'International Press'),(150,'Intelligence Research Limited'),(151,'Intellect'),(152,'InteLex'),(153,'Institute of Mathematics of the Polish Academy of Sciences'),(154,'Ingentaconnect'),(155,'INFORMS'),(156,'Information Resources, Inc'),(157,'Indiana University Mathematics Journal'),(158,'Incisive Media Ltd'),(159,'IGI Global '),(160,'IBISWorld USA'),(161,'H.W. Wilson Company'),(162,'University of Houston Department of Mathematics'),(163,'Histochemical Society'),(164,'Morningstar Inc.'),(165,'Paradigm Publishers'),(166,'HighWire Press'),(167,'Heldref Publications'),(168,'Haworth Press'),(417,'Thomson Legal'),(170,'IOS Press'),(171,'Agricultural Institute of Canada'),(172,'Allen Press'),(173,'H1 Base, Inc'),(175,'Global Science Press'),(176,'Geographic Research, Inc'),(177,'Genetics Society of America'),(178,'Franz Steiner Verlag'),(179,'Forrester Research, Inc'),(180,'Federation of American Societies for Experimental Biology'),(181,'Faulkner Information Services'),(182,'ExLibris'),(183,'Brill'),(184,'Evolutionary Ecology Ltd'),(185,'European Mathematical Society Publishing House'),(186,'New York Review of Books'),(187,'Dunstans Publishing Ltd'),(188,'Equinox Publishing Ltd'),(189,'Entomological Society of Canada'),(190,'American Association of Immunologists, Inc.'),(191,'Endocrine Society'),(192,'EDP Sciences'),(193,'Edinburgh University Press'),(194,'Ecological Society of America'),(195,'East View Information Services'),(196,'Dun and Bradstreet Inc'),(197,'Duke University Press'),(198,'Digital Distributed Community Archive'),(199,'Albert C. Muller'),(200,'Dialogue Foundation'),(201,'Dialog'),(202,'Current History, Inc'),(203,'CSIRO Publishing'),(204,'CQ Press'),(205,'Japan Focus'),(206,'Carbon Disclosure Project'),(207,'University of Buckingham Press'),(208,'Boopsie, INC.'),(209,'Company of Biologists Ltd'),(210,'Chronicle of Higher Education'),(211,'CCH Incorporated'),(212,'CareerShift LLC'),(213,'Canadian Mathematical Society'),(214,'Cambridge Crystallographic Data Centre'),(215,'CABI Publishing'),(216,'Business Monitor International'),(217,'Bureau of National Affairs, Inc'),(218,'Bulletin of the Atomic Scientists'),(219,'Brepols Publishers'),(221,'Botanical Society of America'),(222,'BMJ Publishing Group Limited'),(223,'BioMed Central'),(224,'Berkeley Electronic Press'),(225,'Berghahn Books'),(226,'Berg Publishers'),(227,'Belser Wissenschaftlicher Dienst Ltd'),(228,'Beilstein Information Systems, Inc'),(229,'Barkhuis Publishing'),(230,'Augustine Institute'),(231,'Asempa Limited'),(232,'ARTstor Inc'),(233,'Applied Probability Trust'),(234,'Antiquity Publications Limited'),(235,'Ammons Scientific Limited'),(236,'American Statistical Association'),(237,'American Society of Tropical Medicine and Hygiene'),(238,'American Society of Plant Biologists'),(239,'Teachers College Record'),(240,'American Society of Agronomy'),(241,'American Society for Nutrition'),(242,'American Society for Horticultural Science'),(243,'American Society for Clinical Investigation'),(244,'American Society for Cell Biology'),(245,'American Psychiatric Publishing'),(246,'American Phytopathological Society'),(247,'American Physiological Society'),(248,'Encyclopaedia Britannica Online'),(249,'Agricultural History Society'),(250,'Begell House, Inc'),(251,'Hans Zell Publishing'),(252,'Alliance for Children and Families'),(253,'Robert Blakemore'),(254,'IVES Group, Inc'),(255,'Massachusetts Institute of Technology'),(256,'Marquis Who\'s Who LLC'),(257,'Atypon Systems Inc'),(258,'Worldwatch Institute'),(259,'Thomson Financial'),(260,'Digital Heritage Publishing Limited'),(261,'U.S. Department of Commerce'),(262,'Lipper Inc'),(263,'Chiniquy Collection'),(264,'OCLC'),(265,'Consumer Electronics Association'),(267,'Institutional Shareholder Services Inc'),(268,'KLD Research and Analytics Inc'),(269,'BIGresearch LLC'),(270,'Cambridge Scientific Abstracts'),(271,'American Institute of Certified Public Accountants'),(272,'Terra Scientific Publishing Company'),(273,'American Counseling Association'),(274,'Army Times Publishing Company'),(275,'Librairie Droz'),(276,'American Academy of Religion'),(277,'Boyd Printing'),(278,'Canadian Association of African Studies'),(279,'Experian Marketing Solutions, Inc.'),(280,'Centro de Investigaciones Sociologicas'),(281,'Chorus America'),(282,'College Art Association'),(283,'Human Kinetics Inc.'),(288,'NERL'),(293,'Colegio de Mexico'),(294,'University of Iowa'),(295,'Academy of the Hebrew Language'),(296,'FamilyLink.com, Inc.'),(297,'SISMEL - Edizioni del Galluzzo'),(301,'Informaworld'),(302,'ScienceDirect'),(304,'China Data Center'),(305,'Association for Computing Machinery'),(306,'American Chemical Society'),(307,'Design Research Publications'),(308,'ABC-CLIO'),(311,'American Association on Intellectual and Developmental Disabilities '),(310,'American Antiquarian Society'),(312,'American Society for Microbiology'),(314,'American Society of Mechanical Engineers'),(315,'Now Publishers, Inc.'),(316,'Cabell Publishing Company, Inc.'),(317,'Center for Research Libraries'),(444,'Cold North Wind Inc'),(321,'Erudit '),(322,'American Institute of Mathematical Sciences'),(324,'American Sociological Association'),(325,'Archaeological Institute of America'),(326,'Bertrand Russell Research Centre'),(328,'Cork University Press'),(329,'College Publishing'),(330,'Council for Learning Disabilities'),(331,'International Society on Hypertension in Blacks (ISHIB)'),(332,'Firenze University Press'),(333,'History of Earth Sciences Society'),(334,'History Today Ltd.'),(335,'Journal of Music'),(336,'University of Nebraska at Omaha'),(337,'Journal of Indo-European Studies'),(338,'Library Binding Institute'),(339,'McFarland & Co. Inc.'),(340,'Lyrasis'),(341,'Amigos Library Services'),(343,'Fabrizio Serra Editore'),(344,'Aux Amateurs'),(346,'National Affairs, Inc'),(357,'Society of Chemical Industry'),(347,'New Criterion'),(348,'Casa Editrice Leo S. Olschki s.r.l.'),(349,'Rhodes University, Department of Philosophy'),(350,'Rocky Mountain Mathematics Consortium'),(352,'Royal Irish Academy'),(353,'Chadwyck-Healey'),(354,'CSA illumina'),(355,'New School for Social Research'),(356,'Society of Biblical Literature'),(358,'Stazione Zoologica Anton Dohrn'),(359,'BioScientifica Ltd.'),(360,'CASALINI LIBRI'),(361,'Institute of Organic Chemistry'),(362,'Columbia International Affairs Online '),(363,'Corporation for National Research Initiatives '),(364,'Tilgher-Genova'),(365,'Emerald Group Publishing Limited'),(366,'Geological Society of America'),(367,'Institute of Mathematical Statistics'),(368,'Institute of Pure and Applied Physics'),(369,'JSTAGE'),(370,'Metapress'),(371,'Modern Language Association'),(372,'Optical Society of America'),(373,'University of British Columbia'),(374,'University of New Mexico'),(375,'Vandenhoeck & Ruprecht'),(376,'Verlag Mohr Siebeck GmbH & Co. KG'),(377,'Palgrave Macmillan'),(378,'Vittorio Klostermann'),(379,'Project Euclid'),(380,'Psychonomic Society '),(411,'Cengage Learning'),(382,'Infotrieve'),(383,'Society of Automotive Engineers'),(384,'Turpion Publications'),(426,'Midwest Collaborative for Library Services'),(386,'Lawrence Erlbaum Associates'),(387,'Alphagraphics'),(388,'Bellerophon Publications, Inc.'),(389,'Boydell & Brewer Inc.'),(390,'Carcanet Press'),(391,'Feminist Studies'),(393,'Dustbooks'),(394,'Society for Applied Anthropology '),(395,'United Nations Publications'),(396,'Wharton Research Data Services'),(398,'Human Development'),(399,'infoUSA Marketing, Inc.'),(400,'Bowker'),(402,'Brown University'),(401,'Women Writers Project'),(445,'Coutts'),(446,'Numara Software, Inc.'),(447,'Trinity College Library Dublin'),(405,'Oldenbourg Wissenschaftsverlag '),(406,'Dow Jones'),(412,'Financial Information Inc. (FII)'),(408,'Jackson Publishing and Distribution'),(409,'Elsevier Engineering Information, Inc. '),(410,'Eneclann Ltd.'),(413,'UCLA Latin American Institute'),(414,'Harmonie Park Press '),(415,'Harrassowitz'),(416,'Thomson Reuters'),(418,'Human Relations Area Files, Inc. '),(432,'Capital IQ'),(419,'Society for Ethnomusicology'),(420,'MSCI RiskMetrics'),(421,'Rapid Multimedia'),(422,'MSCI Inc'),(423,'New England Journal of Medicine'),(424,'NetLibrary'),(425,'CARMA'),(427,'Public Library of Science'),(428,'Social Science Electronic Publishing'),(429,'United Nations Industrial Develoipment Organization'),(430,'University of Michigan Press'),(431,'ORS Publishing, Inc.'),(433,'McGraw-Hill'),(434,'Biblical Archaeology Society'),(435,'GeoLytics, Inc.'),(436,'JoVE '),(437,'ICEsoft Technologies, Inc.'),(438,'Films Media Group'),(439,'Films on Demand'),(440,'Connect Journals'),(441,'Scuola Normale Superiore'),(442,'Wolters Kluwer'),(448,'Pier Professional'),(449,'ABC News'),(450,'University of Aberdeen '),(451,'BullFrog Films, Inc.'),(453,'FirstSearch'),(455,'History Cooperative '),(456,'Omohundro Institute of Early American History and Culture'),(457,'Arms Control Association'),(458,'Heritage Archives'),(459,'International Historic Films, Inc.'),(460,'Euromonitor International '),(461,'Safari Books Online'),(462,'Mirabile'),(466,'Publishing Technology'),(463,'SageWorks, Inc'),(464,'Johns Hopkins Universtiy Press'),(465,'Knovel '),(467,'American Society of Nephrology'),(468,'Water Envrionment Federation '),(469,'Refworks'),(470,'Cinemagician Productions'),(471,'Algorithmics'),(472,'YBP Library Services '),(474,'Maydream Inc.'),(475,'Organization for Economic Cooperation and Development'),(476,'The Chronicle for Higher Education'),(477,'Association for Research in Vision and Ophthalmologie (ARVO)'),(478,'SRDS Media Solutions'),(479,'Kantar Media'),(480,'Peace & Justice Studies Association'),(481,'Addison Publications Ltd.'),(482,'Mutii-Science Publishing'),(483,'ASM International'),(484,'Verlag der Osterreichischen Akademie der Wissenschaften'),(485,'Anthology of Recorded Music'),(486,'Left Coast Press, Inc'),(487,'Video Data Bank'),(488,'Atlassian'),(489,'medici.tv'),(490,'Bar Ilan Research & Development Company Ltd'),(491,'Primary Source Media'),(492,'Ebrary'),(493,'University of Michigan, Department of Mathematics'),(494,'StataCorp LP '),(495,'L\' Enseignement Mathematique  '),(496,'Audio Engineering Society, Inc'),(497,'LOCKSS'),(498,'MUSEEC '),(499,'Mortgage Bankers Association'),(500,'BibleWorks'),(501,'National Library of Ireland'),(502,'Scholars Press'),(503,'Index to Jewish periodicals'),(504,'Cold Spring Harbor Laboratory Press');
/*!40000 ALTER TABLE `Organization` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OrganizationRole`
--

DROP TABLE IF EXISTS `OrganizationRole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrganizationRole` (
  `organizationRoleID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`organizationRoleID`),
  UNIQUE KEY `organizationRoleID` (`organizationRoleID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OrganizationRole`
--

LOCK TABLES `OrganizationRole` WRITE;
/*!40000 ALTER TABLE `OrganizationRole` DISABLE KEYS */;
INSERT INTO `OrganizationRole` VALUES (1,'Consortium'),(2,'Library'),(3,'Platform'),(4,'Provider'),(5,'Publisher'),(6,'Vendor');
/*!40000 ALTER TABLE `OrganizationRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Privilege`
--

DROP TABLE IF EXISTS `Privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Privilege` (
  `privilegeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`privilegeID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Privilege`
--

LOCK TABLES `Privilege` WRITE;
/*!40000 ALTER TABLE `Privilege` DISABLE KEYS */;
INSERT INTO `Privilege` VALUES (1,'admin'),(2,'add/edit'),(3,'view only');
/*!40000 ALTER TABLE `Privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PurchaseSite`
--

DROP TABLE IF EXISTS `PurchaseSite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PurchaseSite` (
  `purchaseSiteID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`purchaseSiteID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PurchaseSite`
--

LOCK TABLES `PurchaseSite` WRITE;
/*!40000 ALTER TABLE `PurchaseSite` DISABLE KEYS */;
INSERT INTO `PurchaseSite` VALUES (1,'Main Library');
/*!40000 ALTER TABLE `PurchaseSite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RelationshipType`
--

DROP TABLE IF EXISTS `RelationshipType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RelationshipType` (
  `relationshipTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`relationshipTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `RelationshipType`
--

LOCK TABLES `RelationshipType` WRITE;
/*!40000 ALTER TABLE `RelationshipType` DISABLE KEYS */;
INSERT INTO `RelationshipType` VALUES (1,'Parent'),(2,'General');
/*!40000 ALTER TABLE `RelationshipType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Resource`
--

DROP TABLE IF EXISTS `Resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Resource` (
  `resourceID` int(11) NOT NULL AUTO_INCREMENT,
  `createDate` date DEFAULT NULL,
  `createLoginID` varchar(45) DEFAULT NULL,
  `updateDate` date DEFAULT NULL,
  `updateLoginID` varchar(45) DEFAULT NULL,
  `archiveDate` date DEFAULT NULL,
  `archiveLoginID` varchar(45) DEFAULT NULL,
  `workflowRestartDate` date DEFAULT NULL,
  `workflowRestartLoginID` varchar(45) DEFAULT NULL,
  `titleText` varchar(200) DEFAULT NULL,
  `descriptionText` text,
  `statusID` int(11) DEFAULT NULL,
  `resourceTypeID` int(11) DEFAULT NULL,
  `resourceFormatID` int(11) DEFAULT NULL,
  `orderNumber` varchar(45) DEFAULT NULL,
  `systemNumber` varchar(45) DEFAULT NULL,
  `currentStartDate` date DEFAULT NULL,
  `currentEndDate` date DEFAULT NULL,
  `subscriptionAlertEnabledInd` int(10) unsigned DEFAULT NULL,
  `userLimitID` int(11) DEFAULT NULL,
  `resourceURL` varchar(2000) DEFAULT NULL,
  `authenticationUserName` varchar(200) DEFAULT NULL,
  `authenticationPassword` varchar(200) DEFAULT NULL,
  `storageLocationID` int(11) DEFAULT NULL,
  `registeredIPAddresses` varchar(200) DEFAULT NULL,
  `acquisitionTypeID` int(10) unsigned DEFAULT NULL,
  `authenticationTypeID` int(10) unsigned DEFAULT NULL,
  `accessMethodID` int(10) unsigned DEFAULT NULL,
  `providerText` varchar(200) DEFAULT NULL,
  `recordSetIdentifier` varchar(45) DEFAULT NULL,
  `hasOclcHoldings` varchar(10) DEFAULT NULL,
  `numberRecordsAvailable` varchar(45) DEFAULT NULL,
  `numberRecordsLoaded` varchar(45) DEFAULT NULL,
  `bibSourceURL` varchar(2000) DEFAULT NULL,
  `catalogingTypeID` int(11) DEFAULT NULL,
  `catalogingStatusID` int(11) DEFAULT NULL,
  `coverageText` varchar(1000) DEFAULT NULL,
  `resourceAltURL` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`resourceID`),
  KEY `Index_createDate` (`createDate`),
  KEY `Index_createLoginID` (`createLoginID`),
  KEY `Index_titleText` (`titleText`),
  KEY `Index_statusID` (`statusID`),
  KEY `Index_resourceTypeID` (`resourceTypeID`),
  KEY `Index_resourceFormatID` (`resourceFormatID`),
  KEY `Index_acquisitionTypeID` (`authenticationTypeID`),
  KEY `catalogingTypeID` (`catalogingTypeID`),
  KEY `catalogingStatusID` (`catalogingStatusID`),
  KEY `Index_All` (`createDate`,`createLoginID`,`titleText`,`statusID`,`resourceTypeID`,`resourceFormatID`,`acquisitionTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Resource`
--

LOCK TABLES `Resource` WRITE;
/*!40000 ALTER TABLE `Resource` DISABLE KEYS */;
INSERT INTO `Resource` VALUES (1,'2016-09-09','coral_test',NULL,NULL,NULL,NULL,NULL,NULL,'My Resource',NULL,1,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `Resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceAdministeringSiteLink`
--

DROP TABLE IF EXISTS `ResourceAdministeringSiteLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceAdministeringSiteLink` (
  `resourceAdministeringSiteLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `administeringSiteID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourceAdministeringSiteLinkID`),
  KEY `Index_resourceID` (`resourceID`),
  KEY `Index_administeringSiteID` (`administeringSiteID`),
  KEY `Index_All` (`resourceID`,`administeringSiteID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceAdministeringSiteLink`
--

LOCK TABLES `ResourceAdministeringSiteLink` WRITE;
/*!40000 ALTER TABLE `ResourceAdministeringSiteLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceAdministeringSiteLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceAlert`
--

DROP TABLE IF EXISTS `ResourceAlert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceAlert` (
  `resourceAlertID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `loginID` varchar(45) DEFAULT NULL,
  `sendDate` date DEFAULT NULL,
  `alertText` text,
  PRIMARY KEY (`resourceAlertID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceAlert`
--

LOCK TABLES `ResourceAlert` WRITE;
/*!40000 ALTER TABLE `ResourceAlert` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceAlert` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceAuthorizedSiteLink`
--

DROP TABLE IF EXISTS `ResourceAuthorizedSiteLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceAuthorizedSiteLink` (
  `resourceAuthorizedSiteLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `authorizedSiteID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourceAuthorizedSiteLinkID`),
  KEY `Index_resourceID` (`resourceID`),
  KEY `Index_authorizedSiteID` (`authorizedSiteID`),
  KEY `Index_All` (`resourceID`,`authorizedSiteID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceAuthorizedSiteLink`
--

LOCK TABLES `ResourceAuthorizedSiteLink` WRITE;
/*!40000 ALTER TABLE `ResourceAuthorizedSiteLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceAuthorizedSiteLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceFormat`
--

DROP TABLE IF EXISTS `ResourceFormat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceFormat` (
  `resourceFormatID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`resourceFormatID`) USING BTREE,
  KEY `shortName` (`shortName`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceFormat`
--

LOCK TABLES `ResourceFormat` WRITE;
/*!40000 ALTER TABLE `ResourceFormat` DISABLE KEYS */;
INSERT INTO `ResourceFormat` VALUES (1,'Print + Electronic'),(2,'Electronic');
/*!40000 ALTER TABLE `ResourceFormat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceLicenseLink`
--

DROP TABLE IF EXISTS `ResourceLicenseLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceLicenseLink` (
  `resourceLicenseLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `licenseID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourceLicenseLinkID`),
  KEY `resourceID` (`resourceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceLicenseLink`
--

LOCK TABLES `ResourceLicenseLink` WRITE;
/*!40000 ALTER TABLE `ResourceLicenseLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceLicenseLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceLicenseStatus`
--

DROP TABLE IF EXISTS `ResourceLicenseStatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceLicenseStatus` (
  `resourceLicenseStatusID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `licenseStatusID` int(11) DEFAULT NULL,
  `licenseStatusChangeDate` datetime DEFAULT NULL,
  `licenseStatusChangeLoginID` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`resourceLicenseStatusID`),
  KEY `resourceID` (`resourceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceLicenseStatus`
--

LOCK TABLES `ResourceLicenseStatus` WRITE;
/*!40000 ALTER TABLE `ResourceLicenseStatus` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceLicenseStatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceNote`
--

DROP TABLE IF EXISTS `ResourceNote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceNote` (
  `resourceNoteID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `noteTypeID` int(11) DEFAULT NULL,
  `tabName` varchar(45) DEFAULT NULL,
  `updateDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateLoginID` varchar(45) DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`resourceNoteID`),
  KEY `Index_resourceID` (`resourceID`),
  KEY `Index_noteTypeID` (`noteTypeID`),
  KEY `Index_All` (`resourceID`,`noteTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceNote`
--

LOCK TABLES `ResourceNote` WRITE;
/*!40000 ALTER TABLE `ResourceNote` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceNote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceOrganizationLink`
--

DROP TABLE IF EXISTS `ResourceOrganizationLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceOrganizationLink` (
  `resourceOrganizationLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `organizationID` int(11) DEFAULT NULL,
  `organizationRoleID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourceOrganizationLinkID`),
  KEY `Index_resourceID` (`resourceID`),
  KEY `Index_organizationID` (`organizationID`),
  KEY `Index_All` (`resourceID`,`organizationID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceOrganizationLink`
--

LOCK TABLES `ResourceOrganizationLink` WRITE;
/*!40000 ALTER TABLE `ResourceOrganizationLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceOrganizationLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourcePayment`
--

DROP TABLE IF EXISTS `ResourcePayment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourcePayment` (
  `resourcePaymentID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(10) unsigned NOT NULL,
  `fundID` int(10) DEFAULT NULL,
  `selectorLoginID` varchar(45) DEFAULT NULL,
  `priceTaxExcluded` int(10) unsigned DEFAULT NULL,
  `taxRate` int(10) unsigned DEFAULT NULL,
  `priceTaxIncluded` int(10) unsigned DEFAULT NULL,
  `paymentAmount` int(10) unsigned DEFAULT NULL,
  `orderTypeID` int(10) unsigned DEFAULT NULL,
  `currencyCode` varchar(3) NOT NULL,
  `year` varchar(20) DEFAULT NULL,
  `subscriptionStartDate` date DEFAULT NULL,
  `subscriptionEndDate` date DEFAULT NULL,
  `costDetailsID` int(11) DEFAULT NULL,
  `costNote` text,
  `invoiceNum` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`resourcePaymentID`),
  KEY `Index_resourceID` (`resourceID`),
  KEY `Index_fundID` (`fundID`),
  KEY `Index_year` (`year`),
  KEY `Index_costDetailsID` (`costDetailsID`),
  KEY `Index_invoiceNum` (`invoiceNum`),
  KEY `Index_All` (`resourceID`,`fundID`,`year`,`costDetailsID`,`invoiceNum`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourcePayment`
--

LOCK TABLES `ResourcePayment` WRITE;
/*!40000 ALTER TABLE `ResourcePayment` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourcePayment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourcePurchaseSiteLink`
--

DROP TABLE IF EXISTS `ResourcePurchaseSiteLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourcePurchaseSiteLink` (
  `resourcePurchaseSiteLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `purchaseSiteID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourcePurchaseSiteLinkID`),
  KEY `Index_resourceID` (`resourceID`),
  KEY `Index_purchaseSiteID` (`purchaseSiteID`),
  KEY `Index_All` (`resourceID`,`purchaseSiteID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourcePurchaseSiteLink`
--

LOCK TABLES `ResourcePurchaseSiteLink` WRITE;
/*!40000 ALTER TABLE `ResourcePurchaseSiteLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourcePurchaseSiteLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceRelationship`
--

DROP TABLE IF EXISTS `ResourceRelationship`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceRelationship` (
  `resourceRelationshipID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `relatedResourceID` int(11) DEFAULT NULL,
  `relationshipTypeID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourceRelationshipID`),
  KEY `Index_resourceID` (`resourceID`),
  KEY `Index_relatedResourceID` (`relatedResourceID`),
  KEY `Index_All` (`resourceID`,`relatedResourceID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceRelationship`
--

LOCK TABLES `ResourceRelationship` WRITE;
/*!40000 ALTER TABLE `ResourceRelationship` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceRelationship` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceStep`
--

DROP TABLE IF EXISTS `ResourceStep`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceStep` (
  `resourceStepID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `stepID` int(11) DEFAULT NULL,
  `stepStartDate` date DEFAULT NULL,
  `stepEndDate` date DEFAULT NULL,
  `endLoginID` varchar(200) DEFAULT NULL,
  `priorStepID` int(11) DEFAULT NULL,
  `stepName` varchar(200) DEFAULT NULL,
  `userGroupID` int(11) DEFAULT NULL,
  `displayOrderSequence` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`resourceStepID`),
  KEY `resourceID` (`resourceID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceStep`
--

LOCK TABLES `ResourceStep` WRITE;
/*!40000 ALTER TABLE `ResourceStep` DISABLE KEYS */;
INSERT INTO `ResourceStep` VALUES (1,1,1,'2016-09-09',NULL,NULL,NULL,'Funding Approval',3,1),(2,1,2,'2016-09-09',NULL,NULL,NULL,'Licensing',2,2),(3,1,3,NULL,NULL,NULL,2,'Order Processing',4,3),(4,1,4,NULL,NULL,NULL,3,'Activation',1,4);
/*!40000 ALTER TABLE `ResourceStep` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceSubject`
--

DROP TABLE IF EXISTS `ResourceSubject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceSubject` (
  `resourceSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `generalDetailSubjectLinkID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourceSubjectID`),
  KEY `resourceSubjectID` (`resourceSubjectID`),
  KEY `Index_All` (`resourceID`,`generalDetailSubjectLinkID`),
  KEY `Index_ResourceID` (`resourceID`),
  KEY `Index_GeneralDetailLink` (`generalDetailSubjectLinkID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceSubject`
--

LOCK TABLES `ResourceSubject` WRITE;
/*!40000 ALTER TABLE `ResourceSubject` DISABLE KEYS */;
/*!40000 ALTER TABLE `ResourceSubject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ResourceType`
--

DROP TABLE IF EXISTS `ResourceType`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ResourceType` (
  `resourceTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  `includeStats` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`resourceTypeID`),
  KEY `shortName` (`shortName`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ResourceType`
--

LOCK TABLES `ResourceType` WRITE;
/*!40000 ALTER TABLE `ResourceType` DISABLE KEYS */;
INSERT INTO `ResourceType` VALUES (1,'My Resource Type',NULL);
/*!40000 ALTER TABLE `ResourceType` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Status`
--

DROP TABLE IF EXISTS `Status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Status` (
  `statusID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`statusID`),
  KEY `shortName` (`shortName`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Status`
--

LOCK TABLES `Status` WRITE;
/*!40000 ALTER TABLE `Status` DISABLE KEYS */;
INSERT INTO `Status` VALUES (1,'In Progress'),(2,'Completed'),(3,'Saved'),(4,'Archived');
/*!40000 ALTER TABLE `Status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Step`
--

DROP TABLE IF EXISTS `Step`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Step` (
  `stepID` int(11) NOT NULL AUTO_INCREMENT,
  `priorStepID` int(11) DEFAULT NULL,
  `stepName` varchar(200) DEFAULT NULL,
  `userGroupID` int(11) DEFAULT NULL,
  `workflowID` int(11) DEFAULT NULL,
  `displayOrderSequence` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`stepID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Step`
--

LOCK TABLES `Step` WRITE;
/*!40000 ALTER TABLE `Step` DISABLE KEYS */;
INSERT INTO `Step` VALUES (1,NULL,'Funding Approval',3,1,1),(2,NULL,'Licensing',2,1,2),(3,2,'Order Processing',4,1,3),(4,3,'Activation',1,1,4),(5,NULL,'Licensing',2,2,1),(6,NULL,'Activation',1,2,2);
/*!40000 ALTER TABLE `Step` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `StorageLocation`
--

DROP TABLE IF EXISTS `StorageLocation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `StorageLocation` (
  `storageLocationID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`storageLocationID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `StorageLocation`
--

LOCK TABLES `StorageLocation` WRITE;
/*!40000 ALTER TABLE `StorageLocation` DISABLE KEYS */;
INSERT INTO `StorageLocation` VALUES (1,'Reserve Book Room');
/*!40000 ALTER TABLE `StorageLocation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `loginID` varchar(50) NOT NULL DEFAULT '',
  `lastName` varchar(45) DEFAULT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `privilegeID` int(10) unsigned DEFAULT NULL,
  `accountTabIndicator` int(1) unsigned DEFAULT '0',
  `emailAddress` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`loginID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES ('coral_test',NULL,NULL,1,0,NULL);
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UserGroup`
--

DROP TABLE IF EXISTS `UserGroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserGroup` (
  `userGroupID` int(11) NOT NULL AUTO_INCREMENT,
  `groupName` varchar(200) DEFAULT NULL,
  `emailAddress` varchar(200) DEFAULT NULL,
  `emailText` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`userGroupID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UserGroup`
--

LOCK TABLES `UserGroup` WRITE;
/*!40000 ALTER TABLE `UserGroup` DISABLE KEYS */;
INSERT INTO `UserGroup` VALUES (1,'Access',NULL,NULL),(2,'Licensing',NULL,NULL),(3,'Funding Approval',NULL,NULL),(4,'Acquisitions',NULL,NULL),(5,'Receipt',NULL,NULL);
/*!40000 ALTER TABLE `UserGroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UserGroupLink`
--

DROP TABLE IF EXISTS `UserGroupLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserGroupLink` (
  `userGroupLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `loginID` varchar(200) DEFAULT NULL,
  `userGroupID` int(11) DEFAULT NULL,
  PRIMARY KEY (`userGroupLinkID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UserGroupLink`
--

LOCK TABLES `UserGroupLink` WRITE;
/*!40000 ALTER TABLE `UserGroupLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `UserGroupLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `UserLimit`
--

DROP TABLE IF EXISTS `UserLimit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserLimit` (
  `userLimitID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`userLimitID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UserLimit`
--

LOCK TABLES `UserLimit` WRITE;
/*!40000 ALTER TABLE `UserLimit` DISABLE KEYS */;
INSERT INTO `UserLimit` VALUES (1,'1'),(2,'2'),(3,'3'),(4,'4'),(5,'5'),(6,'6'),(7,'7'),(8,'8'),(9,'9'),(10,'10+');
/*!40000 ALTER TABLE `UserLimit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Workflow`
--

DROP TABLE IF EXISTS `Workflow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Workflow` (
  `workflowID` int(11) NOT NULL AUTO_INCREMENT,
  `workflowName` varchar(200) DEFAULT NULL,
  `resourceFormatIDValue` varchar(45) DEFAULT NULL,
  `resourceTypeIDValue` varchar(45) DEFAULT NULL,
  `acquisitionTypeIDValue` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`workflowID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Workflow`
--

LOCK TABLES `Workflow` WRITE;
/*!40000 ALTER TABLE `Workflow` DISABLE KEYS */;
INSERT INTO `Workflow` VALUES (1,NULL,'2','','1'),(2,NULL,'2','','2');
/*!40000 ALTER TABLE `Workflow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `coral_usage_test`
--

USE `coral_usage_test`;

--
-- Table structure for table `ExternalLogin`
--

DROP TABLE IF EXISTS `ExternalLogin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ExternalLogin` (
  `externalLoginID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `publisherPlatformID` int(10) unsigned DEFAULT NULL,
  `platformID` int(10) unsigned DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `loginURL` varchar(245) DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`externalLoginID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ExternalLogin`
--

LOCK TABLES `ExternalLogin` WRITE;
/*!40000 ALTER TABLE `ExternalLogin` DISABLE KEYS */;
/*!40000 ALTER TABLE `ExternalLogin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ImportLog`
--

DROP TABLE IF EXISTS `ImportLog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ImportLog` (
  `importLogID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loginID` varchar(45) NOT NULL,
  `importDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `layoutCode` varchar(45) DEFAULT NULL,
  `fileName` varchar(45) DEFAULT NULL,
  `archiveFileURL` varchar(145) DEFAULT NULL,
  `logFileURL` varchar(145) DEFAULT NULL,
  `details` varchar(245) DEFAULT NULL,
  PRIMARY KEY (`importLogID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ImportLog`
--

LOCK TABLES `ImportLog` WRITE;
/*!40000 ALTER TABLE `ImportLog` DISABLE KEYS */;
/*!40000 ALTER TABLE `ImportLog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ImportLogPlatformLink`
--

DROP TABLE IF EXISTS `ImportLogPlatformLink`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ImportLogPlatformLink` (
  `importLogPlatformLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `platformID` int(11) DEFAULT NULL,
  `importLogID` int(11) DEFAULT NULL,
  PRIMARY KEY (`importLogPlatformLinkID`),
  KEY `Index_platformID` (`platformID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ImportLogPlatformLink`
--

LOCK TABLES `ImportLogPlatformLink` WRITE;
/*!40000 ALTER TABLE `ImportLogPlatformLink` DISABLE KEYS */;
/*!40000 ALTER TABLE `ImportLogPlatformLink` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Layout`
--

DROP TABLE IF EXISTS `Layout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Layout` (
  `layoutID` int(11) NOT NULL AUTO_INCREMENT,
  `layoutCode` varchar(45) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `resourceType` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`layoutID`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Layout`
--

LOCK TABLES `Layout` WRITE;
/*!40000 ALTER TABLE `Layout` DISABLE KEYS */;
INSERT INTO `Layout` VALUES (1,'JR1_R3','Journals (JR1) R3','Journal'),(2,'JR1a_R3','Journals (JR1) R3 archive','Journal'),(3,'JR1_R4','Journals (JR1) R4','Journal'),(4,'JR1a_R4','Journals (JR1) R4 archive','Journal'),(5,'BR1_R3','Books (BR1) R3','Book'),(6,'BR1_R4','Books (BR1) R4','Book'),(7,'BR2_R3','Book Sections (BR2) R3','Book'),(8,'BR2_R4','Book Sections (BR2) R4','Book'),(9,'DB1_R3','Database (DB1) R3','Database'),(10,'DB1_R4','Database (DB1) R4','Database');
/*!40000 ALTER TABLE `Layout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LogEmailAddress`
--

DROP TABLE IF EXISTS `LogEmailAddress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LogEmailAddress` (
  `logEmailAddressID` int(11) NOT NULL AUTO_INCREMENT,
  `emailAddress` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`logEmailAddressID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LogEmailAddress`
--

LOCK TABLES `LogEmailAddress` WRITE;
/*!40000 ALTER TABLE `LogEmailAddress` DISABLE KEYS */;
/*!40000 ALTER TABLE `LogEmailAddress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `MonthlyUsageSummary`
--

DROP TABLE IF EXISTS `MonthlyUsageSummary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MonthlyUsageSummary` (
  `monthlyUsageSummaryID` int(11) NOT NULL AUTO_INCREMENT,
  `titleID` int(11) NOT NULL,
  `publisherPlatformID` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `month` int(2) NOT NULL,
  `archiveInd` tinyint(1) DEFAULT NULL,
  `usageCount` int(11) DEFAULT NULL,
  `overrideUsageCount` int(11) DEFAULT NULL,
  `outlierID` int(10) unsigned DEFAULT NULL,
  `ignoreOutlierInd` tinyint(3) unsigned DEFAULT '0',
  `mergeInd` tinyint(1) unsigned DEFAULT '0',
  `activityType` varchar(45) DEFAULT NULL,
  `sectionType` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`monthlyUsageSummaryID`) USING BTREE,
  KEY `Index_titleID` (`titleID`),
  KEY `Index_publisherPlatformID` (`publisherPlatformID`),
  KEY `Index_year` (`year`),
  KEY `Index_TPPYMA` (`titleID`,`publisherPlatformID`,`year`,`month`,`archiveInd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `MonthlyUsageSummary`
--

LOCK TABLES `MonthlyUsageSummary` WRITE;
/*!40000 ALTER TABLE `MonthlyUsageSummary` DISABLE KEYS */;
/*!40000 ALTER TABLE `MonthlyUsageSummary` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Outlier`
--

DROP TABLE IF EXISTS `Outlier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Outlier` (
  `outlierID` int(11) NOT NULL AUTO_INCREMENT,
  `outlierLevel` int(11) DEFAULT NULL,
  `overageCount` int(11) DEFAULT NULL,
  `overagePercent` int(3) DEFAULT NULL,
  `color` varchar(45) NOT NULL,
  PRIMARY KEY (`outlierID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Outlier`
--

LOCK TABLES `Outlier` WRITE;
/*!40000 ALTER TABLE `Outlier` DISABLE KEYS */;
INSERT INTO `Outlier` VALUES (1,1,50,200,'yellow'),(2,2,100,300,'orange'),(3,3,200,400,'red');
/*!40000 ALTER TABLE `Outlier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Platform`
--

DROP TABLE IF EXISTS `Platform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Platform` (
  `platformID` int(11) NOT NULL AUTO_INCREMENT,
  `organizationID` int(10) unsigned DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `reportDisplayName` varchar(150) DEFAULT NULL,
  `reportDropDownInd` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`platformID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Platform`
--

LOCK TABLES `Platform` WRITE;
/*!40000 ALTER TABLE `Platform` DISABLE KEYS */;
/*!40000 ALTER TABLE `Platform` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PlatformNote`
--

DROP TABLE IF EXISTS `PlatformNote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PlatformNote` (
  `platformNoteID` int(11) NOT NULL AUTO_INCREMENT,
  `platformID` int(11) DEFAULT NULL,
  `startYear` int(4) DEFAULT NULL,
  `endYear` int(4) DEFAULT NULL,
  `counterCompliantInd` tinyint(1) unsigned DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`platformNoteID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PlatformNote`
--

LOCK TABLES `PlatformNote` WRITE;
/*!40000 ALTER TABLE `PlatformNote` DISABLE KEYS */;
/*!40000 ALTER TABLE `PlatformNote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Privilege`
--

DROP TABLE IF EXISTS `Privilege`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Privilege` (
  `privilegeID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`privilegeID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Privilege`
--

LOCK TABLES `Privilege` WRITE;
/*!40000 ALTER TABLE `Privilege` DISABLE KEYS */;
INSERT INTO `Privilege` VALUES (1,'admin'),(2,'add/edit');
/*!40000 ALTER TABLE `Privilege` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Publisher`
--

DROP TABLE IF EXISTS `Publisher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Publisher` (
  `publisherID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`publisherID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Publisher`
--

LOCK TABLES `Publisher` WRITE;
/*!40000 ALTER TABLE `Publisher` DISABLE KEYS */;
/*!40000 ALTER TABLE `Publisher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PublisherPlatform`
--

DROP TABLE IF EXISTS `PublisherPlatform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PublisherPlatform` (
  `publisherPlatformID` int(11) NOT NULL AUTO_INCREMENT,
  `publisherID` int(11) DEFAULT NULL,
  `platformID` int(11) DEFAULT NULL,
  `organizationID` int(10) unsigned DEFAULT NULL,
  `reportDisplayName` varchar(150) NOT NULL,
  `reportDropDownInd` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`publisherPlatformID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PublisherPlatform`
--

LOCK TABLES `PublisherPlatform` WRITE;
/*!40000 ALTER TABLE `PublisherPlatform` DISABLE KEYS */;
/*!40000 ALTER TABLE `PublisherPlatform` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PublisherPlatformNote`
--

DROP TABLE IF EXISTS `PublisherPlatformNote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PublisherPlatformNote` (
  `publisherPlatformNoteID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `publisherPlatformID` int(10) unsigned NOT NULL,
  `startYear` int(4) unsigned DEFAULT NULL,
  `endYear` int(4) unsigned DEFAULT NULL,
  `noteText` text,
  PRIMARY KEY (`publisherPlatformNoteID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PublisherPlatformNote`
--

LOCK TABLES `PublisherPlatformNote` WRITE;
/*!40000 ALTER TABLE `PublisherPlatformNote` DISABLE KEYS */;
/*!40000 ALTER TABLE `PublisherPlatformNote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `SushiService`
--

DROP TABLE IF EXISTS `SushiService`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SushiService` (
  `sushiServiceID` int(11) NOT NULL AUTO_INCREMENT,
  `platformID` int(11) DEFAULT NULL,
  `publisherPlatformID` int(11) DEFAULT NULL,
  `serviceURL` varchar(300) DEFAULT NULL,
  `wsdlURL` varchar(300) DEFAULT NULL,
  `requestorID` varchar(300) DEFAULT NULL,
  `customerID` varchar(300) DEFAULT NULL,
  `login` varchar(300) DEFAULT NULL,
  `password` varchar(300) DEFAULT NULL,
  `security` varchar(300) DEFAULT NULL,
  `serviceDayOfMonth` varchar(300) DEFAULT NULL,
  `noteText` varchar(300) DEFAULT NULL,
  `releaseNumber` varchar(45) DEFAULT NULL,
  `reportLayouts` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`sushiServiceID`),
  KEY `Index_publisherPlatformID` (`publisherPlatformID`),
  KEY `Index_platformID` (`platformID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `SushiService`
--

LOCK TABLES `SushiService` WRITE;
/*!40000 ALTER TABLE `SushiService` DISABLE KEYS */;
/*!40000 ALTER TABLE `SushiService` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Title`
--

DROP TABLE IF EXISTS `Title`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Title` (
  `titleID` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `resourceType` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`titleID`),
  KEY `Index_title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Title`
--

LOCK TABLES `Title` WRITE;
/*!40000 ALTER TABLE `Title` DISABLE KEYS */;
/*!40000 ALTER TABLE `Title` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TitleIdentifier`
--

DROP TABLE IF EXISTS `TitleIdentifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TitleIdentifier` (
  `titleIdentifierID` int(11) NOT NULL AUTO_INCREMENT,
  `titleID` int(11) DEFAULT NULL,
  `identifier` varchar(25) DEFAULT NULL,
  `identifierType` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`titleIdentifierID`),
  KEY `Index_titleID` (`titleID`),
  KEY `Index_issn` (`identifier`) USING BTREE,
  KEY `Index_ISSNType` (`identifierType`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TitleIdentifier`
--

LOCK TABLES `TitleIdentifier` WRITE;
/*!40000 ALTER TABLE `TitleIdentifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `TitleIdentifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `loginID` varchar(50) NOT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `privilegeID` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES ('coral_test',NULL,NULL,1);
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Version`
--

DROP TABLE IF EXISTS `Version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Version` (
  `version` varchar(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Version`
--

LOCK TABLES `Version` WRITE;
/*!40000 ALTER TABLE `Version` DISABLE KEYS */;
INSERT INTO `Version` VALUES ('1.2');
/*!40000 ALTER TABLE `Version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `YearlyUsageSummary`
--

DROP TABLE IF EXISTS `YearlyUsageSummary`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `YearlyUsageSummary` (
  `yearlyUsageSummaryID` int(11) NOT NULL AUTO_INCREMENT,
  `titleID` int(11) NOT NULL,
  `publisherPlatformID` int(11) NOT NULL,
  `year` int(4) DEFAULT NULL,
  `archiveInd` tinyint(1) DEFAULT NULL,
  `totalCount` int(11) DEFAULT NULL,
  `ytdHTMLCount` int(11) DEFAULT NULL,
  `ytdPDFCount` int(11) DEFAULT NULL,
  `overrideTotalCount` int(10) unsigned DEFAULT NULL,
  `overrideHTMLCount` int(10) unsigned DEFAULT NULL,
  `overridePDFCount` int(10) unsigned DEFAULT NULL,
  `mergeInd` tinyint(1) unsigned DEFAULT '0',
  `activityType` varchar(45) DEFAULT NULL,
  `sectionType` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`yearlyUsageSummaryID`) USING BTREE,
  KEY `Index_titleID` (`titleID`),
  KEY `Index_publisherPlatformID` (`publisherPlatformID`),
  KEY `Index_year` (`year`),
  KEY `Index_TPPYA` (`titleID`,`publisherPlatformID`,`year`,`archiveInd`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `YearlyUsageSummary`
--

LOCK TABLES `YearlyUsageSummary` WRITE;
/*!40000 ALTER TABLE `YearlyUsageSummary` DISABLE KEYS */;
/*!40000 ALTER TABLE `YearlyUsageSummary` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-09-09 17:54:41
