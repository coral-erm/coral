
CREATE TABLE IF NOT EXISTS `Attachment` (
  `attachmentID` int(10) NOT NULL auto_increment,
  `licenseID` int(10) default NULL,
  `sentDate` date default NULL,
  `attachmentText` text,
  PRIMARY KEY  (`attachmentID`),
  KEY `licenseID` (`licenseID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `AttachmentFile` (
  `attachmentFileID` int(10) unsigned NOT NULL auto_increment,
  `attachmentID` int(10) unsigned NOT NULL,
  `attachmentURL` varchar(200) NOT NULL,
  PRIMARY KEY  (`attachmentFileID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Consortium` (
  `consortiumID` int(10) unsigned NOT NULL auto_increment,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY  (`consortiumID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Document` (
  `documentID` int(10) unsigned NOT NULL auto_increment,
  `shortName` tinytext NOT NULL,
  `documentTypeID` int(10) unsigned NOT NULL,
  `licenseID` int(10) unsigned NOT NULL,
  `effectiveDate` date default NULL,
  `expirationDate` date default NULL,
  `documentURL` varchar(200) default NULL,
  `parentDocumentID` int(10) unsigned default NULL,
  `description` tinytext,
  `revisionDate` date default NULL,
  PRIMARY KEY  (`documentID`),
  KEY `licenseID` (`licenseID`),
  KEY `documentTypeID` (`documentTypeID`),
  KEY `parentDocumentID` (`parentDocumentID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `DocumentNote` (
  `documentNoteID` int(11) NOT NULL auto_increment,
  `licenseID` int(11) NOT NULL,
  `documentID` int(11) default '0',
  `documentNoteTypeID` int(11) NOT NULL,
  `body` text NOT NULL,
  `createDate` datetime NOT NULL,
  PRIMARY KEY  (`documentNoteID`),
  KEY `licenseID` (`licenseID`,`documentNoteTypeID`),
  KEY `documentID` (`documentID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `DocumentNoteType` (
  `documentNoteTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(60) NOT NULL,
  PRIMARY KEY  (`documentNoteTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `DocumentType` (
  `documentTypeID` int(10) unsigned NOT NULL auto_increment,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY  (`documentTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Expression` (
  `expressionID` int(10) unsigned NOT NULL auto_increment,
  `documentID` int(10) unsigned NOT NULL,
  `expressionTypeID` int(10) unsigned NOT NULL,
  `documentText` text,
  `simplifiedText` text,
  `lastUpdateDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `productionUseInd` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`expressionID`),
  KEY `documentID` (`documentID`),
  KEY `expressionTypeID` (`expressionTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ExpressionNote` (
  `expressionNoteID` int(10) NOT NULL auto_increment,
  `expressionID` int(10) default NULL,
  `note` varchar(2000) default NULL,
  `displayOrderSeqNumber` int(10) default NULL,
  `lastUpdateDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`expressionNoteID`),
  KEY `expressionID` (`expressionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ExpressionQualifierProfile` (
  `expressionID` int(10) unsigned NOT NULL,
  `qualifierID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`expressionID`,`qualifierID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `ExpressionType` (
  `expressionTypeID` int(10) unsigned NOT NULL auto_increment,
  `shortName` tinytext NOT NULL,
  `noteType` varchar(45) default NULL,
  PRIMARY KEY  (`expressionTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `License` (
  `licenseID` int(10) unsigned NOT NULL auto_increment,
  `consortiumID` int(10) unsigned default NULL,
  `organizationID` int(10) unsigned default NULL,
  `shortName` tinytext NOT NULL,
  `statusID` int(10) unsigned default NULL,
  `statusDate` datetime default NULL,
  `createDate` datetime default NULL,
  `typeID` int(11) default NULL,
  `description` varchar(200) default NULL,
  `statusLoginID` varchar(50) default NULL,
  `createLoginID` varchar(50) default NULL,
  PRIMARY KEY  (`licenseID`),
  KEY `organizationID` (`organizationID`),
  KEY `consortiumID` (`consortiumID`),
  KEY `statusID` (`statusID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Organization` (
  `organizationID` int(10) unsigned NOT NULL auto_increment,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY  (`organizationID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Privilege` (
  `privilegeID` int(10) unsigned NOT NULL auto_increment,
  `shortName` varchar(50) default NULL,
  PRIMARY KEY  (`privilegeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Qualifier` (
  `qualifierID` int(10) unsigned NOT NULL auto_increment,
  `expressionTypeID` int(10) unsigned NOT NULL,
  `shortName` varchar(45) NOT NULL,
  PRIMARY KEY  (`qualifierID`),
  KEY `expressionTypeID` (`expressionTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `SFXProvider` (
  `sfxProviderID` int(10) unsigned NOT NULL auto_increment,
  `documentID` int(10) unsigned NOT NULL,
  `shortName` varchar(245) NOT NULL,
  PRIMARY KEY  (`sfxProviderID`),
  KEY `documentID` (`documentID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Signature` (
  `signatureID` int(10) unsigned NOT NULL auto_increment,
  `documentID` int(10) unsigned NOT NULL,
  `signatureTypeID` int(10) unsigned NOT NULL,
  `signatureDate` date default NULL,
  `signerName` tinytext,
  PRIMARY KEY  (`signatureID`),
  KEY `documentID` (`documentID`),
  KEY `signatureTypeID` (`signatureTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `SignatureType` (
  `signatureTypeID` int(10) unsigned NOT NULL auto_increment,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY  (`signatureTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Status` (
  `statusID` int(10) unsigned NOT NULL auto_increment,
  `shortName` varchar(45) NOT NULL,
  PRIMARY KEY  (`statusID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Type` (
  `typeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  (`typeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `User` (
  `loginID` varchar(50) NOT NULL,
  `lastName` varchar(45) default NULL,
  `firstName` varchar(45) default NULL,
  `privilegeID` int(10) unsigned default NULL,
  `emailAddressForTermsTool` varchar(150) default NULL,
  PRIMARY KEY  (`loginID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `license_consortium` (
  `id` int(11) NOT NULL auto_increment,
  `licenseID` int(11) default NULL,
  `consortiumID` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='							';


LOCK TABLES `DocumentType` WRITE;
/*!40000 ALTER TABLE `DocumentType` DISABLE KEYS */;
INSERT INTO `DocumentType` VALUES (1,'Checklist');
/*!40000 ALTER TABLE `DocumentType` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `Privilege` WRITE;
/*!40000 ALTER TABLE `Privilege` DISABLE KEYS */;
INSERT INTO `Privilege` VALUES (1,'admin'),(2,'add/edit'),(3,'view only');
/*!40000 ALTER TABLE `Privilege` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `DocumentNoteType` WRITE;
/*!40000 ALTER TABLE `DocumentNoteType` DISABLE KEYS */;
INSERT INTO `DocumentNoteType` VALUES (9,'General');
/*!40000 ALTER TABLE `DocumentNoteType` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `Consortium` WRITE;
/*!40000 ALTER TABLE `Consortium` DISABLE KEYS */;
INSERT INTO `Consortium` VALUES (1,'CORAL Documentation');
/*!40000 ALTER TABLE `Consortium` ENABLE KEYS */;
UNLOCK TABLES;
