DROP TABLE IF EXISTS `AccessMethod`;
CREATE TABLE  `AccessMethod` (
  `accessMethodID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`accessMethodID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AcquisitionType`;
CREATE TABLE  `AcquisitionType` (
  `acquisitionTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`acquisitionTypeID`),
  UNIQUE KEY `acquisitionTypeID` (`acquisitionTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AdministeringSite`;
CREATE TABLE  `AdministeringSite` (
  `administeringSiteID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`administeringSiteID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AlertDaysInAdvance`;
CREATE TABLE  `AlertDaysInAdvance` (
  `alertDaysInAdvanceID` int(11) NOT NULL auto_increment,
  `daysInAdvanceNumber` int(11) default NULL,
  PRIMARY KEY  (`alertDaysInAdvanceID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AlertEmailAddress`;
CREATE TABLE  `AlertEmailAddress` (
  `alertEmailAddressID` int(11) NOT NULL auto_increment,
  `emailAddress` varchar(200) default NULL,
  PRIMARY KEY  (`alertEmailAddressID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `Alias`;
CREATE TABLE  `Alias` (
  `aliasID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `aliasTypeID` int(11) default NULL,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  (`aliasID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AliasType`;
CREATE TABLE  `AliasType` (
  `aliasTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  (`aliasTypeID`),
  UNIQUE KEY `aliasTypeID` (`aliasTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Attachment`;
CREATE TABLE  `Attachment` (
  `attachmentID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `attachmentTypeID` int(11) default NULL,
  `shortName` varchar(200) default NULL,
  `descriptionText` text,
  `attachmentURL` varchar(200) default NULL,
  PRIMARY KEY  (`attachmentID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AttachmentType`;
CREATE TABLE  `AttachmentType` (
  `attachmentTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`attachmentTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `AuthenticationType`;
CREATE TABLE  `AuthenticationType` (
  `authenticationTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  USING BTREE (`authenticationTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AuthorizedSite`;
CREATE TABLE  `AuthorizedSite` (
  `authorizedSiteID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`authorizedSiteID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `CatalogingType`;
CREATE TABLE  `CatalogingType` (
  `catalogingTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`catalogingTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `CatalogingStatus`;
CREATE TABLE  `CatalogingStatus` (
  `catalogingStatusID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`catalogingStatusID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Contact`;
CREATE TABLE  `Contact` (
  `contactID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) NOT NULL,
  `lastUpdateDate` date default NULL,
  `name` varchar(150) default NULL,
  `title` varchar(150) default NULL,
  `addressText` varchar(300) default NULL,
  `phoneNumber` varchar(50) default NULL,
  `altPhoneNumber` varchar(50) default NULL,
  `faxNumber` varchar(50) default NULL,
  `emailAddress` varchar(100) default NULL,
  `archiveDate` date default NULL,
  `noteText` text,
  PRIMARY KEY  (`contactID`),
  UNIQUE KEY `contactID` (`contactID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ContactRole`;
CREATE TABLE  `ContactRole` (
  `contactRoleID` int(11) NOT NULL auto_increment,
  `shortName` varchar(50) default NULL,
  PRIMARY KEY  (`contactRoleID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ContactRoleProfile`;
CREATE TABLE  `ContactRoleProfile` (
  `contactID` int(10) unsigned NOT NULL,
  `contactRoleID` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`contactID`,`contactRoleID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Currency`;
CREATE TABLE  `Currency` (
  `currencyCode` varchar(3) NOT NULL,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  (`currencyCode`),
  UNIQUE KEY `currencyCode` (`currencyCode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ExternalLogin`;
CREATE TABLE  `ExternalLogin` (
  `externalLoginID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `externalLoginTypeID` int(11) default NULL,
  `updateDate` date default NULL,
  `loginURL` varchar(150) default NULL,
  `emailAddress` varchar(150) default NULL,
  `username` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `noteText` text,
  PRIMARY KEY  (`externalLoginID`),
  UNIQUE KEY `externalLoginID` (`externalLoginID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ExternalLoginType`;
CREATE TABLE  `ExternalLoginType` (
  `externalLoginTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`externalLoginTypeID`),
  UNIQUE KEY `externalLoginTypeID` (`externalLoginTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `IsbnOrIssn`;
CREATE TABLE  `IsbnOrIssn` (
  `isbnOrIssnID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `isbnOrIssn` varchar(45) NOT NULL,
  PRIMARY KEY  (`isbnOrIssnID`),
  KEY `resourceID` (`resourceID`),
  KEY `isbnOrIssn` (`isbnOrIssn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Fund`;
CREATE TABLE `Fund` (
  `fundID` int(11) NOT NULL auto_increment,
  `fundCode` varchar(20) default NULL,
  `shortName` varchar(200) default NULL,
  `archived` boolean default NULL,
  PRIMARY KEY (`fundID`),
  UNIQUE `fundCode` (`fundCode`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ImportConfig`;
CREATE TABLE `ImportConfig` (
  `importConfigID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  `configuration` varchar(1000) default NULL,
  PRIMARY KEY (`importConfigID`)
  ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

DROP TABLE IF EXISTS `OrgNameMapping`;
CREATE TABLE `OrgNameMapping` (
  `orgNameMappingID` int(11) NOT NULL auto_increment,
  `importConfigID` int(11) NOT NULL,
  `orgNameImported` varchar(200) default NULL,
  `orgNameMapped` varchar(200) default NULL,
  PRIMARY KEY (`orgNameMappingID`),
  KEY (`importConfigID`)
  ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=UTF8;

DROP TABLE IF EXISTS `LicenseStatus`;
CREATE TABLE  `LicenseStatus` (
  `licenseStatusID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`licenseStatusID`),
  UNIQUE KEY `licenseStatusID` (`licenseStatusID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `NoteType`;
CREATE TABLE  `NoteType` (
  `noteTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  (`noteTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `OrderType`;
CREATE TABLE  `OrderType` (
  `orderTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`orderTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `Organization`;
CREATE TABLE  `Organization` (
  `organizationID` int(10) unsigned NOT NULL auto_increment,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY  USING BTREE (`organizationID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `OrganizationRole`;
CREATE TABLE  `OrganizationRole` (
  `organizationRoleID` int(11) NOT NULL auto_increment,
  `shortName` varchar(50) default NULL,
  PRIMARY KEY  (`organizationRoleID`),
  UNIQUE KEY `organizationRoleID` (`organizationRoleID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `Privilege`;
CREATE TABLE  `Privilege` (
  `privilegeID` int(10) unsigned NOT NULL auto_increment,
  `shortName` varchar(50) default NULL,
  PRIMARY KEY  USING BTREE (`privilegeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `PurchaseSite`;
CREATE TABLE  `PurchaseSite` (
  `purchaseSiteID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`purchaseSiteID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `RelationshipType`;
CREATE TABLE  `RelationshipType` (
  `relationshipTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  (`relationshipTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Resource`;
CREATE TABLE  `Resource` (
  `resourceID` int(11) NOT NULL auto_increment,
  `createDate` date default NULL,
  `createLoginID` varchar(45) default NULL,
  `updateDate` date default NULL,
  `updateLoginID` varchar(45) default NULL,
  `archiveDate` date default NULL,
  `archiveLoginID` varchar(45) default NULL,
  `workflowRestartDate` date default NULL,
  `workflowRestartLoginID` varchar(45) default NULL,
  `titleText` varchar(200) default NULL,
  `descriptionText` text,
  `statusID` int(11) default NULL,
  `resourceTypeID` int(11) default NULL,
  `resourceFormatID` int(11) default NULL,
  `orderNumber` varchar(45) default NULL,
  `systemNumber` varchar(45) default NULL,
  `currentStartDate` date default NULL,
  `currentEndDate` date default NULL,
  `subscriptionAlertEnabledInd` int(10) unsigned default NULL,
  `userLimitID` int(11) default NULL,
  `resourceURL` varchar(2000) default NULL,
  `authenticationUserName` varchar(200) default NULL,
  `authenticationPassword` varchar(200) default NULL,
  `storageLocationID` int(11) default NULL,
  `registeredIPAddresses` varchar(200) default NULL,
  `acquisitionTypeID` int(10) unsigned default NULL,
  `authenticationTypeID` int(10) unsigned default NULL,
  `accessMethodID` int(10) unsigned default NULL,
  `providerText` varchar(200) default NULL,
  `recordSetIdentifier` VARCHAR( 45 ) DEFAULT NULL ,
  `hasOclcHoldings` varchar( 10 ) DEFAULT NULL ,
  `numberRecordsAvailable` VARCHAR( 45 ) DEFAULT NULL ,
  `numberRecordsLoaded` VARCHAR( 45 ) DEFAULT NULL ,
  `bibSourceURL` VARCHAR( 2000 ) DEFAULT NULL ,
  `catalogingTypeID` int(11) DEFAULT NULL,
  `catalogingStatusID` int(11) DEFAULT NULL,
  `coverageText` VARCHAR(1000) NULL DEFAULT NULL,
  `resourceAltURL` VARCHAR(2000) NULL DEFAULT NULL,
  PRIMARY KEY  (`resourceID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourceAdministeringSiteLink`;
CREATE TABLE  `ResourceAdministeringSiteLink` (
  `resourceAdministeringSiteLinkID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `administeringSiteID` int(11) default NULL,
  PRIMARY KEY  (`resourceAdministeringSiteLinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `ResourceAlert`;
CREATE TABLE  `ResourceAlert` (
  `resourceAlertID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `loginID` varchar(45) default NULL,
  `sendDate` date default NULL,
  `alertText` text,
  PRIMARY KEY  (`resourceAlertID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourceAuthorizedSiteLink`;
CREATE TABLE  `ResourceAuthorizedSiteLink` (
  `resourceAuthorizedSiteLinkID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `authorizedSiteID` int(11) default NULL,
  PRIMARY KEY  (`resourceAuthorizedSiteLinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `ResourceFormat`;
CREATE TABLE  `ResourceFormat` (
  `resourceFormatID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  USING BTREE (`resourceFormatID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourceLicenseLink`;
CREATE TABLE  `ResourceLicenseLink` (
  `resourceLicenseLinkID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `licenseID` int(11) default NULL,
  PRIMARY KEY  (`resourceLicenseLinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourceLicenseStatus`;
CREATE TABLE  `ResourceLicenseStatus` (
  `resourceLicenseStatusID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `licenseStatusID` int(11) default NULL,
  `licenseStatusChangeDate` datetime default NULL,
  `licenseStatusChangeLoginID` varchar(45) default NULL,
  PRIMARY KEY  (`resourceLicenseStatusID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourceNote`;
CREATE TABLE  `ResourceNote` (
  `resourceNoteID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `noteTypeID` int(11) default NULL,
  `tabName` varchar(45) default NULL,
  `updateDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `updateLoginID` varchar(45) default NULL,
  `noteText` text,
  PRIMARY KEY  (`resourceNoteID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ResourceOrganizationLink`;
CREATE TABLE  `ResourceOrganizationLink` (
  `resourceOrganizationLinkID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `organizationID` int(11) default NULL,
  `organizationRoleID` int(11) default NULL,
  PRIMARY KEY  (`resourceOrganizationLinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourcePayment`;
CREATE TABLE  `ResourcePayment` (
  `resourcePaymentID` int(11) NOT NULL auto_increment,
  `resourceID` int(10) unsigned NOT NULL,
  `fundID` int(10) default NULL,
  `selectorLoginID` varchar(45) default NULL,
  `priceTaxExcluded` int(10) unsigned default NULL,
  `taxRate` int(10) unsigned default NULL,
  `priceTaxIncluded` int(10) unsigned default NULL,
  `paymentAmount` int(10) unsigned default NULL,
  `orderTypeID` int(10) unsigned default NULL,
  `currencyCode` varchar(3) NOT NULL,
  `year` varchar(20) default NULL,
  `subscriptionStartDate` date default NULL,
  `subscriptionEndDate` date default NULL,
  `costDetailsID` int(11) default NULL,
  `costNote` text,
  `invoiceNum` varchar(20),
  PRIMARY KEY  (`resourcePaymentID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourcePurchaseSiteLink`;
CREATE TABLE  `ResourcePurchaseSiteLink` (
  `resourcePurchaseSiteLinkID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `purchaseSiteID` int(11) default NULL,
  PRIMARY KEY  (`resourcePurchaseSiteLinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourceRelationship`;
CREATE TABLE  `ResourceRelationship` (
  `resourceRelationshipID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `relatedResourceID` int(11) default NULL,
  `relationshipTypeID` int(11) default NULL,
  PRIMARY KEY  (`resourceRelationshipID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ResourceStep`;
CREATE TABLE  `ResourceStep` (
  `resourceStepID` int(11) NOT NULL auto_increment,
  `resourceID` int(11) default NULL,
  `stepID` int(11) default NULL,
  `stepStartDate` date default NULL,
  `stepEndDate` date default NULL,
  `endLoginID` varchar(200) default NULL,
  `priorStepID` int(11) default NULL,
  `stepName` varchar(200) default NULL,
  `userGroupID` int(11) default NULL,
  `displayOrderSequence` int(10) unsigned default NULL,
  PRIMARY KEY  (`resourceStepID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ResourceType`;
CREATE TABLE  `ResourceType` (
  `resourceTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  `includeStats` boolean default NULL,
  PRIMARY KEY  (`resourceTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `Status`;
CREATE TABLE  `Status` (
  `statusID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  (`statusID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `Step`;
CREATE TABLE  `Step` (
  `stepID` int(11) NOT NULL auto_increment,
  `priorStepID` int(11) default NULL,
  `stepName` varchar(200) default NULL,
  `userGroupID` int(11) default NULL,
  `workflowID` int(11) default NULL,
  `displayOrderSequence` int(10) unsigned default NULL,
  PRIMARY KEY  (`stepID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `StorageLocation`;
CREATE TABLE  `StorageLocation` (
  `storageLocationID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`storageLocationID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `User`;
CREATE TABLE  `User` (
  `loginID` varchar(50) NOT NULL default '',
  `lastName` varchar(45) default NULL,
  `firstName` varchar(45) default NULL,
  `privilegeID` int(10) unsigned default NULL,
  `accountTabIndicator` int(1) unsigned default '0',
  `emailAddress` varchar(200) default NULL,
  PRIMARY KEY  (`loginID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `UserGroup`;
CREATE TABLE  `UserGroup` (
  `userGroupID` int(11) NOT NULL auto_increment,
  `groupName` varchar(200) default NULL,
  `emailAddress` varchar(200) default NULL,
  `emailText` varchar(2000) default NULL,
  PRIMARY KEY  (`userGroupID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `UserGroupLink`;
CREATE TABLE  `UserGroupLink` (
  `userGroupLinkID` int(11) NOT NULL auto_increment,
  `loginID` varchar(200) default NULL,
  `userGroupID` int(11) default NULL,
  PRIMARY KEY  (`userGroupLinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;




DROP TABLE IF EXISTS `UserLimit`;
CREATE TABLE  `UserLimit` (
  `userLimitID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) default NULL,
  PRIMARY KEY  (`userLimitID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Workflow`;
CREATE TABLE  `Workflow` (
  `workflowID` int(11) NOT NULL auto_increment,
  `workflowName` varchar(200) default NULL,
  `resourceFormatIDValue` varchar(45) default NULL,
  `resourceTypeIDValue` varchar(45) default NULL,
  `acquisitionTypeIDValue` varchar(45) default NULL,
  PRIMARY KEY  (`workflowID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `GeneralSubject`;
CREATE TABLE `GeneralSubject` (
  `generalSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`generalSubjectID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `DetailedSubject`;
CREATE TABLE `DetailedSubject` (
  `detailedSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`detailedSubjectID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `GeneralDetailSubjectLink`;
CREATE TABLE `GeneralDetailSubjectLink` (
  `generalDetailSubjectLinkID` int(11) NOT NULL AUTO_INCREMENT,
  `generalSubjectID` int(11) DEFAULT NULL,
  `detailedSubjectID` int(11) DEFAULT NULL,
  PRIMARY KEY (`generalDetailSubjectLinkID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ResourceSubject`;
CREATE TABLE `ResourceSubject` (
  `resourceSubjectID` int(11) NOT NULL AUTO_INCREMENT,
  `resourceID` int(11) DEFAULT NULL,
  `generalDetailSubjectLinkID` int(11) DEFAULT NULL,
  PRIMARY KEY (`resourceSubjectID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `CostDetails`;
CREATE TABLE `CostDetails` (
  `costDetailsID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(200) NOT NULL,
  PRIMARY KEY (`costDetailsID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Issue`;
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
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `IssueRelationship`;
CREATE TABLE `IssueRelationship` (
  `issueRelationshipID` int(11) NOT NULL AUTO_INCREMENT,
  `issueID` int(11) NOT NULL,
  `entityID` int(11) NOT NULL,
  `entityTypeID` int(11) NOT NULL,
  PRIMARY KEY (`issueRelationshipID`),
  KEY `issueID` (`issueID`,`entityID`,`entityTypeID`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `IssueEntityType`;
CREATE TABLE `IssueEntityType` (
  `entityTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `entityName` varchar(80) NOT NULL,
  PRIMARY KEY (`entityTypeID`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `IssueContact`;
CREATE TABLE `IssueContact` (
  `issueContactID` int(11) NOT NULL AUTO_INCREMENT,
  `issueID` int(11) NOT NULL,
  `contactID` int(11) NOT NULL,
  PRIMARY KEY (`issueContactID`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `IssueEmail`;
CREATE TABLE `IssueEmail` (
  `issueEmailID` int(11) NOT NULL AUTO_INCREMENT,
  `issueID` int(11) NOT NULL,
  `email` varchar(120) NOT NULL,
  PRIMARY KEY (`IssueEmailID`),
  KEY `IssueID` (`IssueID`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `Downtime`;
CREATE TABLE IF NOT EXISTS `Downtime` (
  `downtimeID` int(11) NOT NULL AUTO_INCREMENT,
  `issueID` int(11) DEFAULT NULL,
  `entityID` int(11) NOT NULL,
  `entityTypeID` int(11) NOT NULL DEFAULT '2',
  `creatorID` varchar(80) NOT NULL,
  `dateCreated` datetime NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime DEFAULT NULL,
  `downtimeTypeID` int(11) NOT NULL,
  `note` TEXT DEFAULT NULL,
   PRIMARY KEY (`downtimeID`),
   KEY `IssueID` (`IssueID`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `DowntimeType`;
CREATE TABLE IF NOT EXISTS `DowntimeType` (
  `downtimeTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `shortName` varchar(80) NOT NULL,
  PRIMARY KEY (`downtimeTypeID`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=1;




ALTER TABLE `Alias` ADD INDEX `Index_resourceID`(`resourceID`),
 ADD INDEX `Index_aliasTypeID`(`aliasTypeID`),
 ADD INDEX `shortName` ( `shortName` ),
 ADD INDEX `Index_All`(`resourceID`, `aliasTypeID`);
 
 
ALTER TABLE `Resource` ADD INDEX `Index_createDate`(`createDate`),
 ADD INDEX `Index_createLoginID`(`createLoginID`),
 ADD INDEX `Index_titleText`(`titleText`),
 ADD INDEX `Index_statusID`(`statusID`),
 ADD INDEX `Index_resourceTypeID`(`resourceTypeID`),
 ADD INDEX `Index_resourceFormatID`(`resourceFormatID`),
 ADD INDEX `Index_acquisitionTypeID`(`authenticationTypeID`),
 ADD INDEX `catalogingTypeID` ( `catalogingTypeID` ),
 ADD INDEX `catalogingStatusID` ( `catalogingStatusID` ),
 ADD INDEX `Index_All`(`createDate`, `createLoginID`, `titleText`, `statusID`, `resourceTypeID`, `resourceFormatID`, `acquisitionTypeID`);
 
ALTER TABLE `ResourceFormat` ADD INDEX `shortName` ( `shortName` );

ALTER TABLE `ResourcePayment` ADD INDEX `Index_resourceID`(`resourceID`),
 ADD INDEX `Index_fundID`(`fundID`),
 ADD INDEX `Index_year`(`year`),
 ADD INDEX `Index_costDetailsID`(`costDetailsID`),
 ADD INDEX `Index_invoiceNum`(`invoiceNum`),
 ADD INDEX `Index_All`(`resourceID`, `fundID`, `year`, `costDetailsID`, `invoiceNum`);


ALTER TABLE `ResourceNote` ADD INDEX `Index_resourceID`(`resourceID`),
 ADD INDEX `Index_noteTypeID`(`noteTypeID`),
 ADD INDEX `Index_All`(`resourceID`, `noteTypeID`);
 
ALTER TABLE `ResourceStep` ADD INDEX `resourceID` ( `resourceID` );

ALTER TABLE `ResourceType` ADD INDEX `shortName` ( `shortName` );

ALTER TABLE `ResourceOrganizationLink` ADD INDEX `Index_resourceID`(`resourceID`),
 ADD INDEX `Index_organizationID`(`organizationID`),
 ADD INDEX `Index_All`(`resourceID`, `organizationID`);



ALTER TABLE `ResourcePurchaseSiteLink` ADD INDEX `Index_resourceID`(`resourceID`),
 ADD INDEX `Index_purchaseSiteID`(`purchaseSiteID`),
 ADD INDEX `Index_All`(`resourceID`, `purchaseSiteID`);

ALTER TABLE `ResourceAdministeringSiteLink` ADD INDEX `Index_resourceID`(`resourceID`),
 ADD INDEX `Index_administeringSiteID`(`administeringSiteID`),
 ADD INDEX `Index_All`(`resourceID`, `administeringSiteID`);
 

ALTER TABLE `ResourceAuthorizedSiteLink` ADD INDEX `Index_resourceID`(`resourceID`),
 ADD INDEX `Index_authorizedSiteID`(`authorizedSiteID`),
 ADD INDEX `Index_All`(`resourceID`, `authorizedSiteID`); 
 
ALTER TABLE `ResourceLicenseLink` ADD INDEX `resourceID` ( `resourceID` );

ALTER TABLE `ResourceLicenseStatus` ADD INDEX `resourceID` ( `resourceID` );
 
ALTER TABLE `ResourceRelationship` ADD INDEX `Index_resourceID`(`resourceID`),
 ADD INDEX `Index_relatedResourceID`(`relatedResourceID`),
 ADD INDEX `Index_All`(`resourceID`, `relatedResourceID`);
 
ALTER TABLE `Status` ADD INDEX `shortName` ( `shortName` );

ALTER TABLE `GeneralSubject` ADD INDEX `generalSubjectID` ( `generalSubjectID` );

ALTER TABLE `DetailedSubject` ADD INDEX `detailedSubjectID` ( `detailedSubjectID` );

ALTER TABLE `GeneralDetailSubjectLink` ADD INDEX `generalDetailSubjectLinkID` ( `generalDetailSubjectLinkID` ),
 ADD INDEX `Index_All` (`generalSubjectID` ASC, `detailedSubjectID` ASC), 
 ADD INDEX `Index_generalSubject` (`generalSubjectID` ASC), 
 ADD INDEX `Index_detailedSubject` (`detailedSubjectID` ASC) ;

ALTER TABLE `ResourceSubject` ADD INDEX `resourceSubjectID` ( `resourceSubjectID` ), 
 ADD INDEX `Index_All` (`resourceID` ASC, `generalDetailSubjectLinkID` ASC), 
 ADD INDEX `Index_ResourceID` (`resourceID` ASC), 
 ADD INDEX `Index_GeneralDetailLink` (`generalDetailSubjectLinkID` ASC) ;
 
ALTER TABLE `CostDetails` ADD INDEX `costDetailsID` ( `costDetailsID` ), 
 ADD INDEX `Index_shortName` (`shortName`),
 ADD INDEX `Index_All`(`costDetailsID`, `shortName`);
 
INSERT INTO `AccessMethod` (shortName) values ('Standalone CD');
INSERT INTO `AccessMethod` (shortName) values ('External Host');
INSERT INTO `AccessMethod` (shortName) values ('Local Host');


INSERT INTO `AcquisitionType` (acquisitionTypeID, shortName) values (1, 'Paid');
INSERT INTO `AcquisitionType` (acquisitionTypeID, shortName) values (2, 'Free');
INSERT INTO `AcquisitionType` (acquisitionTypeID, shortName) values (3, 'Trial');
INSERT INTO `AcquisitionType` (acquisitionTypeID, shortName) values (4, 'Any');


INSERT INTO `AdministeringSite` (shortName) values ('Main Library');


INSERT INTO `AlertDaysInAdvance` (daysInAdvanceNumber) values ('30');
INSERT INTO `AlertDaysInAdvance` (daysInAdvanceNumber) values ('60');
INSERT INTO `AlertDaysInAdvance` (daysInAdvanceNumber) values ('90');



INSERT INTO `AliasType` (shortName) values ('Abbreviation');
INSERT INTO `AliasType` (shortName) values ('Alternate Name');
INSERT INTO `AliasType` (shortName) values ('Name Change');



INSERT INTO `AttachmentType` (shortName) values ('Email');
INSERT INTO `AttachmentType` (shortName) values ('User Guide');
INSERT INTO `AttachmentType` (shortName) values ('Title List');
INSERT INTO `AttachmentType` (shortName) values ('General');



INSERT INTO `AuthenticationType` (shortName) values ('IP Address');
INSERT INTO `AuthenticationType` (shortName) values ('Username');
INSERT INTO `AuthenticationType` (shortName) values ('Referring URL');



INSERT INTO `AuthorizedSite` (shortName) values ('Main Campus');

INSERT INTO `CatalogingType` (catalogingTypeID, shortName) values (1, 'Batch');
INSERT INTO `CatalogingType` (catalogingTypeID, shortName) values (2, 'Manual');
INSERT INTO `CatalogingType` (catalogingTypeID, shortName) values (3, 'MARCit');

INSERT INTO `CatalogingStatus` (catalogingStatusID, shortName) values (1, 'Completed');
INSERT INTO `CatalogingStatus` (catalogingStatusID, shortName) values (2, 'Ongoing');
INSERT INTO `CatalogingStatus` (catalogingStatusID, shortName) values (3, 'Rejected');

INSERT INTO `ContactRole` (shortName) values ('Support');
INSERT INTO `ContactRole` (shortName) values ('Accounting');
INSERT INTO `ContactRole` (shortName) values ('General');
INSERT INTO `ContactRole` (shortName) values ('Licensing');
INSERT INTO `ContactRole` (shortName) values ('Sales');
INSERT INTO `ContactRole` (shortName) values ('Training');



INSERT INTO `Currency` (currencyCode, shortName) values ('USD', 'United States Dollar');
INSERT INTO `Currency` (currencyCode, shortName) values ('EUR', 'Euro');
INSERT INTO `Currency` (currencyCode, shortName) values ('GBP', 'Great Britain (UK) Pound');
INSERT INTO `Currency` (currencyCode, shortName) values ('CAD', 'Canadian Dollar');
INSERT INTO `Currency` (currencyCode, shortName) values ('ARS', 'Argentine Peso');
INSERT INTO `Currency` (currencyCode, shortName) values ('AUD', 'Australian Dollar');
INSERT INTO `Currency` (currencyCode, shortName) values ('SEK', 'Swedish Krona');




INSERT INTO `ExternalLoginType` (shortName) values ('Admin');
INSERT INTO `ExternalLoginType` (shortName) values ('FTP');
INSERT INTO `ExternalLoginType` (shortName) values ('Statistics');
INSERT INTO `ExternalLoginType` (shortName) values ('Support');



INSERT INTO `LicenseStatus` (shortName) values ('Pending');
INSERT INTO `LicenseStatus` (shortName) values ('Completed');
INSERT INTO `LicenseStatus` (shortName) values ('No License Required');



INSERT INTO `NoteType` (shortName) values ('Product Details');
INSERT INTO `NoteType` (shortName) values ('Acquisition Details');
INSERT INTO `NoteType` (shortName) values ('Access Details');
INSERT INTO `NoteType` (shortName) values ('General');
INSERT INTO `NoteType` (shortName) values ('Licensing Details');
INSERT INTO `NoteType` (shortName) values ('Initial Note');



INSERT INTO `OrderType` (shortName) values ('Ongoing');
INSERT INTO `OrderType` (shortName) values ('One Time');



INSERT INTO Organization (organizationID, shortName) values (2, 'Institute of Physics');
INSERT INTO Organization (organizationID, shortName) values (3, 'American Institute of Aeronautics and Astronautics');
INSERT INTO Organization (organizationID, shortName) values (4, 'American Physical Society');
INSERT INTO Organization (organizationID, shortName) values (5, 'American Society of Civil Engineers');
INSERT INTO Organization (organizationID, shortName) values (6, 'American Insitute of Physics');
INSERT INTO Organization (organizationID, shortName) values (7, 'Society for Industrial and Applied Mathematics');
INSERT INTO Organization (organizationID, shortName) values (8, 'TNS Media Intelligence');
INSERT INTO Organization (organizationID, shortName) values (9, 'Chemical Abstracts Service');
INSERT INTO Organization (organizationID, shortName) values (10, 'Risk Management Association');
INSERT INTO Organization (organizationID, shortName) values (11, 'American Concrete Institute');
INSERT INTO Organization (organizationID, shortName) values (12, 'American Association for Cancer Research');
INSERT INTO Organization (organizationID, shortName) values (13, 'Institution of Engineering and Technology');
INSERT INTO Organization (organizationID, shortName) values (14, 'American Economic Association');
INSERT INTO Organization (organizationID, shortName) values (15, 'American Mathematical Society');
INSERT INTO Organization (organizationID, shortName) values (16, 'American Medical Association');
INSERT INTO Organization (organizationID, shortName) values (18, 'Consejo Superior de Investigaciones Cientificas');
INSERT INTO Organization (organizationID, shortName) values (19, 'American Meteorological Society');
INSERT INTO Organization (organizationID, shortName) values (20, 'American Library Association');
INSERT INTO Organization (organizationID, shortName) values (21, 'American Society for Testing and Materials');
INSERT INTO Organization (organizationID, shortName) values (22, 'Association of Research Libraries');
INSERT INTO Organization (organizationID, shortName) values (23, 'American Society of Limnology and Oceanography');
INSERT INTO Organization (organizationID, shortName) values (24, 'Tablet Publishing');
INSERT INTO Organization (organizationID, shortName) values (25, 'American Psychological Association');
INSERT INTO Organization (organizationID, shortName) values (26, 'American Council of Learned Societies');
INSERT INTO Organization (organizationID, shortName) values (27, 'American Association for the Advancement of Science');
INSERT INTO Organization (organizationID, shortName) values (28, 'Thomson Healthcare and Science');
INSERT INTO Organization (organizationID, shortName) values (29, 'Elsevier');
INSERT INTO Organization (organizationID, shortName) values (30, 'JSTOR');
INSERT INTO Organization (organizationID, shortName) values (31, 'SAGE Publications');
INSERT INTO Organization (organizationID, shortName) values (32, 'American Geophysical Union');
INSERT INTO Organization (organizationID, shortName) values (33, 'Annual Reviews');
INSERT INTO Organization (organizationID, shortName) values (34, 'BioOne');
INSERT INTO Organization (organizationID, shortName) values (35, 'Gale');
INSERT INTO Organization (organizationID, shortName) values (36, 'Wiley');
INSERT INTO Organization (organizationID, shortName) values (37, 'Oxford University Press');
INSERT INTO Organization (organizationID, shortName) values (38, 'Springer');
INSERT INTO Organization (organizationID, shortName) values (39, 'Taylor and Francis');
INSERT INTO Organization (organizationID, shortName) values (40, 'Stanford University');
INSERT INTO Organization (organizationID, shortName) values (41, 'University of California Press');
INSERT INTO Organization (organizationID, shortName) values (42, 'EBSCO Publishing');
INSERT INTO Organization (organizationID, shortName) values (43, 'Blackwell Publishing');
INSERT INTO Organization (organizationID, shortName) values (44, 'Ovid');
INSERT INTO Organization (organizationID, shortName) values (45, 'Project Muse');
INSERT INTO Organization (organizationID, shortName) values (46, 'American Fisheries Society');
INSERT INTO Organization (organizationID, shortName) values (47, 'Neilson Journals Publishing');
INSERT INTO Organization (organizationID, shortName) values (48, 'GuideStar USA, Inc');
INSERT INTO Organization (organizationID, shortName) values (49, 'Alexander Street Press, LLC');
INSERT INTO Organization (organizationID, shortName) values (50, 'Informa Healthcare USA, Inc');
INSERT INTO Organization (organizationID, shortName) values (51, 'ProQuest LLC');
INSERT INTO Organization (organizationID, shortName) values (52, 'Accessible Archives Inc');
INSERT INTO Organization (organizationID, shortName) values (53, 'ACCU Weather Sales and Services, Inc');
INSERT INTO Organization (organizationID, shortName) values (54, 'Adam Matthew Digital Ltd');
INSERT INTO Organization (organizationID, shortName) values (55, 'Akademiai Kiado');
INSERT INTO Organization (organizationID, shortName) values (56, 'World Trade Press');
INSERT INTO Organization (organizationID, shortName) values (57, 'World Scientific');
INSERT INTO Organization (organizationID, shortName) values (58, 'Walter de Gruyter');
INSERT INTO Organization (organizationID, shortName) values (59, 'Cambridge University Press');
INSERT INTO Organization (organizationID, shortName) values (60, 'GeoScienceWorld');
INSERT INTO Organization (organizationID, shortName) values (61, 'IEEE');
INSERT INTO Organization (organizationID, shortName) values (62, 'Yankelovich Inc');
INSERT INTO Organization (organizationID, shortName) values (63, 'Nature Publishing Group');
INSERT INTO Organization (organizationID, shortName) values (64, 'Verlag der Zeitschrift fur Naturforschung ');
INSERT INTO Organization (organizationID, shortName) values (65, 'White Horse Press');
INSERT INTO Organization (organizationID, shortName) values (66, 'Verlag C.H. Beck');
INSERT INTO Organization (organizationID, shortName) values (67, 'Vault, Inc');
INSERT INTO Organization (organizationID, shortName) values (68, 'Value Line, Inc');
INSERT INTO Organization (organizationID, shortName) values (69, 'Vanderbilt University');
INSERT INTO Organization (organizationID, shortName) values (70, 'Uniworld Business Publications, Inc');
INSERT INTO Organization (organizationID, shortName) values (71, 'Universum USA');
INSERT INTO Organization (organizationID, shortName) values (72, 'University of Wisconsin Press');
INSERT INTO Organization (organizationID, shortName) values (73, 'University of Virginia Press');
INSERT INTO Organization (organizationID, shortName) values (74, 'University of Toronto Press Inc');
INSERT INTO Organization (organizationID, shortName) values (75, 'University of Toronto');
INSERT INTO Organization (organizationID, shortName) values (76, 'University of Pittsburgh');
INSERT INTO Organization (organizationID, shortName) values (77, 'University of Illinois Press');
INSERT INTO Organization (organizationID, shortName) values (78, 'University of Chicago Press');
INSERT INTO Organization (organizationID, shortName) values (79, 'University of Barcelona');
INSERT INTO Organization (organizationID, shortName) values (80, 'UCLA Chicano Studies Research Center Press');
INSERT INTO Organization (organizationID, shortName) values (81, 'Transportation Research Board');
INSERT INTO Organization (organizationID, shortName) values (82, 'Trans Tech Publications');
INSERT INTO Organization (organizationID, shortName) values (83, 'Thomas Telford Ltd');
INSERT INTO Organization (organizationID, shortName) values (84, 'Thesaurus Linguae Graecae');
INSERT INTO Organization (organizationID, shortName) values (85, 'Tetrad Computer Applications Inc');
INSERT INTO Organization (organizationID, shortName) values (86, 'Swank Motion Pictures, Inc');
INSERT INTO Organization (organizationID, shortName) values (87, 'Standard and Poors');
INSERT INTO Organization (organizationID, shortName) values (88, 'SPIE');
INSERT INTO Organization (organizationID, shortName) values (89, 'European Society of Endocrinology');
INSERT INTO Organization (organizationID, shortName) values (90, 'Society of Environmental Toxicology and Chemistry');
INSERT INTO Organization (organizationID, shortName) values (91, 'Society of Antiquaries of Scotland');
INSERT INTO Organization (organizationID, shortName) values (92, 'Society for Reproduction and Fertility');
INSERT INTO Organization (organizationID, shortName) values (93, 'Society for Neuroscience');
INSERT INTO Organization (organizationID, shortName) values (94, 'Society for Leukocyte Biology');
INSERT INTO Organization (organizationID, shortName) values (95, 'Society for General Microbiology');
INSERT INTO Organization (organizationID, shortName) values (96, 'Society for Experimental Biology and Medicine');
INSERT INTO Organization (organizationID, shortName) values (97, 'Society for Endocrinology');
INSERT INTO Organization (organizationID, shortName) values (98, 'Societe Mathematique de France');
INSERT INTO Organization (organizationID, shortName) values (99, 'Social Explorer');
INSERT INTO Organization (organizationID, shortName) values (404, 'SETAC');
INSERT INTO Organization (organizationID, shortName) values (101, 'Swiss Chemical Society');
INSERT INTO Organization (organizationID, shortName) values (102, 'Scholarly Digital Editions');
INSERT INTO Organization (organizationID, shortName) values (103, 'Royal Society of London');
INSERT INTO Organization (organizationID, shortName) values (104, 'Royal Society of Chemistry');
INSERT INTO Organization (organizationID, shortName) values (105, 'Roper Center for Public Opinion Research');
INSERT INTO Organization (organizationID, shortName) values (106, 'Rockefeller University Press');
INSERT INTO Organization (organizationID, shortName) values (107, 'Rivista di Studi italiani');
INSERT INTO Organization (organizationID, shortName) values (108, 'Reuters Loan Pricing Corporation');
INSERT INTO Organization (organizationID, shortName) values (109, 'Religious and Theological Abstracts, Inc');
INSERT INTO Organization (organizationID, shortName) values (110, 'Psychoanalytic Electronic Publishing Inc');
INSERT INTO Organization (organizationID, shortName) values (111, 'Cornell University Library');
INSERT INTO Organization (organizationID, shortName) values (112, 'Preservation Technologies LP');
INSERT INTO Organization (organizationID, shortName) values (113, 'Portland Press Limited');
INSERT INTO Organization (organizationID, shortName) values (114, 'ITHAKA');
INSERT INTO Organization (organizationID, shortName) values (115, 'Philosophy Documentation Center');
INSERT INTO Organization (organizationID, shortName) values (116, 'Peeters Publishers');
INSERT INTO Organization (organizationID, shortName) values (117, 'Paratext');
INSERT INTO Organization (organizationID, shortName) values (118, 'Mathematical Sciences Publishers');
INSERT INTO Organization (organizationID, shortName) values (119, 'Oxford Centre of Hebrew and Jewish Studies');
INSERT INTO Organization (organizationID, shortName) values (120, 'NewsBank, Inc');
INSERT INTO Organization (organizationID, shortName) values (121, 'Massachusetts Medical Society');
INSERT INTO Organization (organizationID, shortName) values (122, 'Naxos of America, Inc.');
INSERT INTO Organization (organizationID, shortName) values (123, 'National Research Council of Canada');
INSERT INTO Organization (organizationID, shortName) values (124, 'National Gallery Company Ltd');
INSERT INTO Organization (organizationID, shortName) values (125, 'National Academy of Sciences');
INSERT INTO Organization (organizationID, shortName) values (126, 'Mintel International Group Limited');
INSERT INTO Organization (organizationID, shortName) values (127, 'Metropolitan Opera');
INSERT INTO Organization (organizationID, shortName) values (128, 'M.E. Sharpe, Inc');
INSERT INTO Organization (organizationID, shortName) values (129, 'Mergent, Inc');
INSERT INTO Organization (organizationID, shortName) values (130, 'Mediamark Research and Intelligence, LLC');
INSERT INTO Organization (organizationID, shortName) values (131, 'Mary Ann Liebert, Inc');
INSERT INTO Organization (organizationID, shortName) values (132, 'MIT Press');
INSERT INTO Organization (organizationID, shortName) values (133, 'MarketResearch.com, Inc');
INSERT INTO Organization (organizationID, shortName) values (134, 'Marine Biological Laboratory');
INSERT INTO Organization (organizationID, shortName) values (135, 'W.S. Maney and Son Ltd');
INSERT INTO Organization (organizationID, shortName) values (136, 'Manchester University Press');
INSERT INTO Organization (organizationID, shortName) values (137, 'Lord Music Reference Inc');
INSERT INTO Organization (organizationID, shortName) values (138, 'Liverpool University Press');
INSERT INTO Organization (organizationID, shortName) values (139, 'Seminario Matematico of the University of Padua');
INSERT INTO Organization (organizationID, shortName) values (140, 'Library of Congress, Cataloging Distribution Service');
INSERT INTO Organization (organizationID, shortName) values (141, 'LexisNexis');
INSERT INTO Organization (organizationID, shortName) values (142, 'Corporacion Latinobarometro');
INSERT INTO Organization (organizationID, shortName) values (143, 'Landes Bioscience');
INSERT INTO Organization (organizationID, shortName) values (144, 'Keesings Worldwide, LLC');
INSERT INTO Organization (organizationID, shortName) values (145, 'Karger');
INSERT INTO Organization (organizationID, shortName) values (146, 'John Benjamins Publishing Company');
INSERT INTO Organization (organizationID, shortName) values (147, 'Irish Newspaper Archives Ltd');
INSERT INTO Organization (organizationID, shortName) values (148, 'IPA Source, LLC');
INSERT INTO Organization (organizationID, shortName) values (149, 'International Press');
INSERT INTO Organization (organizationID, shortName) values (150, 'Intelligence Research Limited');
INSERT INTO Organization (organizationID, shortName) values (151, 'Intellect');
INSERT INTO Organization (organizationID, shortName) values (152, 'InteLex');
INSERT INTO Organization (organizationID, shortName) values (153, 'Institute of Mathematics of the Polish Academy of Sciences');
INSERT INTO Organization (organizationID, shortName) values (154, 'Ingentaconnect');
INSERT INTO Organization (organizationID, shortName) values (155, 'INFORMS');
INSERT INTO Organization (organizationID, shortName) values (156, 'Information Resources, Inc');
INSERT INTO Organization (organizationID, shortName) values (157, 'Indiana University Mathematics Journal');
INSERT INTO Organization (organizationID, shortName) values (158, 'Incisive Media Ltd');
INSERT INTO Organization (organizationID, shortName) values (159, 'IGI Global ');
INSERT INTO Organization (organizationID, shortName) values (160, 'IBISWorld USA');
INSERT INTO Organization (organizationID, shortName) values (161, 'H.W. Wilson Company');
INSERT INTO Organization (organizationID, shortName) values (162, 'University of Houston Department of Mathematics');
INSERT INTO Organization (organizationID, shortName) values (163, 'Histochemical Society');
INSERT INTO Organization (organizationID, shortName) values (164, 'Morningstar Inc.');
INSERT INTO Organization (organizationID, shortName) values (165, 'Paradigm Publishers');
INSERT INTO Organization (organizationID, shortName) values (166, 'HighWire Press');
INSERT INTO Organization (organizationID, shortName) values (167, 'Heldref Publications');
INSERT INTO Organization (organizationID, shortName) values (168, 'Haworth Press');
INSERT INTO Organization (organizationID, shortName) values (417, 'Thomson Legal');
INSERT INTO Organization (organizationID, shortName) values (170, 'IOS Press');
INSERT INTO Organization (organizationID, shortName) values (171, 'Agricultural Institute of Canada');
INSERT INTO Organization (organizationID, shortName) values (172, 'Allen Press');
INSERT INTO Organization (organizationID, shortName) values (173, 'H1 Base, Inc');
INSERT INTO Organization (organizationID, shortName) values (175, 'Global Science Press');
INSERT INTO Organization (organizationID, shortName) values (176, 'Geographic Research, Inc');
INSERT INTO Organization (organizationID, shortName) values (177, 'Genetics Society of America');
INSERT INTO Organization (organizationID, shortName) values (178, 'Franz Steiner Verlag');
INSERT INTO Organization (organizationID, shortName) values (179, 'Forrester Research, Inc');
INSERT INTO Organization (organizationID, shortName) values (180, 'Federation of American Societies for Experimental Biology');
INSERT INTO Organization (organizationID, shortName) values (181, 'Faulkner Information Services');
INSERT INTO Organization (organizationID, shortName) values (182, 'ExLibris');
INSERT INTO Organization (organizationID, shortName) values (183, 'Brill');
INSERT INTO Organization (organizationID, shortName) values (184, 'Evolutionary Ecology Ltd');
INSERT INTO Organization (organizationID, shortName) values (185, 'European Mathematical Society Publishing House');
INSERT INTO Organization (organizationID, shortName) values (186, 'New York Review of Books');
INSERT INTO Organization (organizationID, shortName) values (187, 'Dunstans Publishing Ltd');
INSERT INTO Organization (organizationID, shortName) values (188, 'Equinox Publishing Ltd');
INSERT INTO Organization (organizationID, shortName) values (189, 'Entomological Society of Canada');
INSERT INTO Organization (organizationID, shortName) values (190, 'American Association of Immunologists, Inc.');
INSERT INTO Organization (organizationID, shortName) values (191, 'Endocrine Society');
INSERT INTO Organization (organizationID, shortName) values (192, 'EDP Sciences');
INSERT INTO Organization (organizationID, shortName) values (193, 'Edinburgh University Press');
INSERT INTO Organization (organizationID, shortName) values (194, 'Ecological Society of America');
INSERT INTO Organization (organizationID, shortName) values (195, 'East View Information Services');
INSERT INTO Organization (organizationID, shortName) values (196, 'Dun and Bradstreet Inc');
INSERT INTO Organization (organizationID, shortName) values (197, 'Duke University Press');
INSERT INTO Organization (organizationID, shortName) values (198, 'Digital Distributed Community Archive');
INSERT INTO Organization (organizationID, shortName) values (199, 'Albert C. Muller');
INSERT INTO Organization (organizationID, shortName) values (200, 'Dialogue Foundation');
INSERT INTO Organization (organizationID, shortName) values (201, 'Dialog');
INSERT INTO Organization (organizationID, shortName) values (202, 'Current History, Inc');
INSERT INTO Organization (organizationID, shortName) values (203, 'CSIRO Publishing');
INSERT INTO Organization (organizationID, shortName) values (204, 'CQ Press');
INSERT INTO Organization (organizationID, shortName) values (205, 'Japan Focus');
INSERT INTO Organization (organizationID, shortName) values (206, 'Carbon Disclosure Project');
INSERT INTO Organization (organizationID, shortName) values (207, 'University of Buckingham Press');
INSERT INTO Organization (organizationID, shortName) values (208, 'Boopsie, INC.');
INSERT INTO Organization (organizationID, shortName) values (209, 'Company of Biologists Ltd');
INSERT INTO Organization (organizationID, shortName) values (210, 'Chronicle of Higher Education');
INSERT INTO Organization (organizationID, shortName) values (211, 'CCH Incorporated');
INSERT INTO Organization (organizationID, shortName) values (212, 'CareerShift LLC');
INSERT INTO Organization (organizationID, shortName) values (213, 'Canadian Mathematical Society');
INSERT INTO Organization (organizationID, shortName) values (214, 'Cambridge Crystallographic Data Centre');
INSERT INTO Organization (organizationID, shortName) values (215, 'CABI Publishing');
INSERT INTO Organization (organizationID, shortName) values (216, 'Business Monitor International');
INSERT INTO Organization (organizationID, shortName) values (217, 'Bureau of National Affairs, Inc');
INSERT INTO Organization (organizationID, shortName) values (218, 'Bulletin of the Atomic Scientists');
INSERT INTO Organization (organizationID, shortName) values (219, 'Brepols Publishers');
INSERT INTO Organization (organizationID, shortName) values (221, 'Botanical Society of America');
INSERT INTO Organization (organizationID, shortName) values (222, 'BMJ Publishing Group Limited');
INSERT INTO Organization (organizationID, shortName) values (223, 'BioMed Central');
INSERT INTO Organization (organizationID, shortName) values (224, 'Berkeley Electronic Press');
INSERT INTO Organization (organizationID, shortName) values (225, 'Berghahn Books');
INSERT INTO Organization (organizationID, shortName) values (226, 'Berg Publishers');
INSERT INTO Organization (organizationID, shortName) values (227, 'Belser Wissenschaftlicher Dienst Ltd');
INSERT INTO Organization (organizationID, shortName) values (228, 'Beilstein Information Systems, Inc');
INSERT INTO Organization (organizationID, shortName) values (229, 'Barkhuis Publishing');
INSERT INTO Organization (organizationID, shortName) values (230, 'Augustine Institute');
INSERT INTO Organization (organizationID, shortName) values (231, 'Asempa Limited');
INSERT INTO Organization (organizationID, shortName) values (232, 'ARTstor Inc');
INSERT INTO Organization (organizationID, shortName) values (233, 'Applied Probability Trust');
INSERT INTO Organization (organizationID, shortName) values (234, 'Antiquity Publications Limited');
INSERT INTO Organization (organizationID, shortName) values (235, 'Ammons Scientific Limited');
INSERT INTO Organization (organizationID, shortName) values (236, 'American Statistical Association');
INSERT INTO Organization (organizationID, shortName) values (237, 'American Society of Tropical Medicine and Hygiene');
INSERT INTO Organization (organizationID, shortName) values (238, 'American Society of Plant Biologists');
INSERT INTO Organization (organizationID, shortName) values (239, 'Teachers College Record');
INSERT INTO Organization (organizationID, shortName) values (240, 'American Society of Agronomy');
INSERT INTO Organization (organizationID, shortName) values (241, 'American Society for Nutrition');
INSERT INTO Organization (organizationID, shortName) values (242, 'American Society for Horticultural Science');
INSERT INTO Organization (organizationID, shortName) values (243, 'American Society for Clinical Investigation');
INSERT INTO Organization (organizationID, shortName) values (244, 'American Society for Cell Biology');
INSERT INTO Organization (organizationID, shortName) values (245, 'American Psychiatric Publishing');
INSERT INTO Organization (organizationID, shortName) values (246, 'American Phytopathological Society');
INSERT INTO Organization (organizationID, shortName) values (247, 'American Physiological Society');
INSERT INTO Organization (organizationID, shortName) values (248, 'Encyclopaedia Britannica Online');
INSERT INTO Organization (organizationID, shortName) values (249, 'Agricultural History Society');
INSERT INTO Organization (organizationID, shortName) values (250, 'Begell House, Inc');
INSERT INTO Organization (organizationID, shortName) values (251, 'Hans Zell Publishing');
INSERT INTO Organization (organizationID, shortName) values (252, 'Alliance for Children and Families');
INSERT INTO Organization (organizationID, shortName) values (253, 'Robert Blakemore');
INSERT INTO Organization (organizationID, shortName) values (254, 'IVES Group, Inc');
INSERT INTO Organization (organizationID, shortName) values (255, 'Massachusetts Institute of Technology');
INSERT INTO Organization (organizationID, shortName) values (256, 'Marquis Who\'s Who LLC');
INSERT INTO Organization (organizationID, shortName) values (257, 'Atypon Systems Inc');
INSERT INTO Organization (organizationID, shortName) values (258, 'Worldwatch Institute');
INSERT INTO Organization (organizationID, shortName) values (259, 'Thomson Financial');
INSERT INTO Organization (organizationID, shortName) values (260, 'Digital Heritage Publishing Limited');
INSERT INTO Organization (organizationID, shortName) values (261, 'U.S. Department of Commerce');
INSERT INTO Organization (organizationID, shortName) values (262, 'Lipper Inc');
INSERT INTO Organization (organizationID, shortName) values (263, 'Chiniquy Collection');
INSERT INTO Organization (organizationID, shortName) values (264, 'OCLC');
INSERT INTO Organization (organizationID, shortName) values (265, 'Consumer Electronics Association');
INSERT INTO Organization (organizationID, shortName) values (267, 'Institutional Shareholder Services Inc');
INSERT INTO Organization (organizationID, shortName) values (268, 'KLD Research and Analytics Inc');
INSERT INTO Organization (organizationID, shortName) values (269, 'BIGresearch LLC');
INSERT INTO Organization (organizationID, shortName) values (270, 'Cambridge Scientific Abstracts');
INSERT INTO Organization (organizationID, shortName) values (271, 'American Institute of Certified Public Accountants');
INSERT INTO Organization (organizationID, shortName) values (272, 'Terra Scientific Publishing Company');
INSERT INTO Organization (organizationID, shortName) values (273, 'American Counseling Association');
INSERT INTO Organization (organizationID, shortName) values (274, 'Army Times Publishing Company');
INSERT INTO Organization (organizationID, shortName) values (275, 'Librairie Droz');
INSERT INTO Organization (organizationID, shortName) values (276, 'American Academy of Religion');
INSERT INTO Organization (organizationID, shortName) values (277, 'Boyd Printing');
INSERT INTO Organization (organizationID, shortName) values (278, 'Canadian Association of African Studies');
INSERT INTO Organization (organizationID, shortName) values (279, 'Experian Marketing Solutions, Inc.');
INSERT INTO Organization (organizationID, shortName) values (280, 'Centro de Investigaciones Sociologicas');
INSERT INTO Organization (organizationID, shortName) values (281, 'Chorus America');
INSERT INTO Organization (organizationID, shortName) values (282, 'College Art Association');
INSERT INTO Organization (organizationID, shortName) values (283, 'Human Kinetics Inc.');
INSERT INTO Organization (organizationID, shortName) values (288, 'NERL');
INSERT INTO Organization (organizationID, shortName) values (293, 'Colegio de Mexico');
INSERT INTO Organization (organizationID, shortName) values (294, 'University of Iowa');
INSERT INTO Organization (organizationID, shortName) values (295, 'Academy of the Hebrew Language');
INSERT INTO Organization (organizationID, shortName) values (296, 'FamilyLink.com, Inc.');
INSERT INTO Organization (organizationID, shortName) values (297, 'SISMEL - Edizioni del Galluzzo');
INSERT INTO Organization (organizationID, shortName) values (301, 'Informaworld');
INSERT INTO Organization (organizationID, shortName) values (302, 'ScienceDirect');
INSERT INTO Organization (organizationID, shortName) values (304, 'China Data Center');
INSERT INTO Organization (organizationID, shortName) values (305, 'Association for Computing Machinery');
INSERT INTO Organization (organizationID, shortName) values (306, 'American Chemical Society');
INSERT INTO Organization (organizationID, shortName) values (307, 'Design Research Publications');
INSERT INTO Organization (organizationID, shortName) values (308, 'ABC-CLIO');
INSERT INTO Organization (organizationID, shortName) values (311, 'American Association on Intellectual and Developmental Disabilities ');
INSERT INTO Organization (organizationID, shortName) values (310, 'American Antiquarian Society');
INSERT INTO Organization (organizationID, shortName) values (312, 'American Society for Microbiology');
INSERT INTO Organization (organizationID, shortName) values (314, 'American Society of Mechanical Engineers');
INSERT INTO Organization (organizationID, shortName) values (315, 'Now Publishers, Inc.');
INSERT INTO Organization (organizationID, shortName) values (316, 'Cabell Publishing Company, Inc.');
INSERT INTO Organization (organizationID, shortName) values (317, 'Center for Research Libraries');
INSERT INTO Organization (organizationID, shortName) values (444, 'Cold North Wind Inc');
INSERT INTO Organization (organizationID, shortName) values (321, 'Erudit ');
INSERT INTO Organization (organizationID, shortName) values (322, 'American Institute of Mathematical Sciences');
INSERT INTO Organization (organizationID, shortName) values (324, 'American Sociological Association');
INSERT INTO Organization (organizationID, shortName) values (325, 'Archaeological Institute of America');
INSERT INTO Organization (organizationID, shortName) values (326, 'Bertrand Russell Research Centre');
INSERT INTO Organization (organizationID, shortName) values (328, 'Cork University Press');
INSERT INTO Organization (organizationID, shortName) values (329, 'College Publishing');
INSERT INTO Organization (organizationID, shortName) values (330, 'Council for Learning Disabilities');
INSERT INTO Organization (organizationID, shortName) values (331, 'International Society on Hypertension in Blacks (ISHIB)');
INSERT INTO Organization (organizationID, shortName) values (332, 'Firenze University Press');
INSERT INTO Organization (organizationID, shortName) values (333, 'History of Earth Sciences Society');
INSERT INTO Organization (organizationID, shortName) values (334, 'History Today Ltd.');
INSERT INTO Organization (organizationID, shortName) values (335, 'Journal of Music');
INSERT INTO Organization (organizationID, shortName) values (336, 'University of Nebraska at Omaha');
INSERT INTO Organization (organizationID, shortName) values (337, 'Journal of Indo-European Studies');
INSERT INTO Organization (organizationID, shortName) values (338, 'Library Binding Institute');
INSERT INTO Organization (organizationID, shortName) values (339, 'McFarland & Co. Inc.');
INSERT INTO Organization (organizationID, shortName) values (340, 'Lyrasis');
INSERT INTO Organization (organizationID, shortName) values (341, 'Amigos Library Services');
INSERT INTO Organization (organizationID, shortName) values (343, 'Fabrizio Serra Editore');
INSERT INTO Organization (organizationID, shortName) values (344, 'Aux Amateurs');
INSERT INTO Organization (organizationID, shortName) values (346, 'National Affairs, Inc');
INSERT INTO Organization (organizationID, shortName) values (357, 'Society of Chemical Industry');
INSERT INTO Organization (organizationID, shortName) values (347, 'New Criterion');
INSERT INTO Organization (organizationID, shortName) values (348, 'Casa Editrice Leo S. Olschki s.r.l.');
INSERT INTO Organization (organizationID, shortName) values (349, 'Rhodes University, Department of Philosophy');
INSERT INTO Organization (organizationID, shortName) values (350, 'Rocky Mountain Mathematics Consortium');
INSERT INTO Organization (organizationID, shortName) values (352, 'Royal Irish Academy');
INSERT INTO Organization (organizationID, shortName) values (353, 'Chadwyck-Healey');
INSERT INTO Organization (organizationID, shortName) values (354, 'CSA illumina');
INSERT INTO Organization (organizationID, shortName) values (355, 'New School for Social Research');
INSERT INTO Organization (organizationID, shortName) values (356, 'Society of Biblical Literature');
INSERT INTO Organization (organizationID, shortName) values (358, 'Stazione Zoologica Anton Dohrn');
INSERT INTO Organization (organizationID, shortName) values (359, 'BioScientifica Ltd.');
INSERT INTO Organization (organizationID, shortName) values (360, 'CASALINI LIBRI');
INSERT INTO Organization (organizationID, shortName) values (361, 'Institute of Organic Chemistry');
INSERT INTO Organization (organizationID, shortName) values (362, 'Columbia International Affairs Online ');
INSERT INTO Organization (organizationID, shortName) values (363, 'Corporation for National Research Initiatives ');
INSERT INTO Organization (organizationID, shortName) values (364, 'Tilgher-Genova');
INSERT INTO Organization (organizationID, shortName) values (365, 'Emerald Group Publishing Limited');
INSERT INTO Organization (organizationID, shortName) values (366, 'Geological Society of America');
INSERT INTO Organization (organizationID, shortName) values (367, 'Institute of Mathematical Statistics');
INSERT INTO Organization (organizationID, shortName) values (368, 'Institute of Pure and Applied Physics');
INSERT INTO Organization (organizationID, shortName) values (369, 'JSTAGE');
INSERT INTO Organization (organizationID, shortName) values (370, 'Metapress');
INSERT INTO Organization (organizationID, shortName) values (371, 'Modern Language Association');
INSERT INTO Organization (organizationID, shortName) values (372, 'Optical Society of America');
INSERT INTO Organization (organizationID, shortName) values (373, 'University of British Columbia');
INSERT INTO Organization (organizationID, shortName) values (374, 'University of New Mexico');
INSERT INTO Organization (organizationID, shortName) values (375, 'Vandenhoeck & Ruprecht');
INSERT INTO Organization (organizationID, shortName) values (376, 'Verlag Mohr Siebeck GmbH & Co. KG');
INSERT INTO Organization (organizationID, shortName) values (377, 'Palgrave Macmillan');
INSERT INTO Organization (organizationID, shortName) values (378, 'Vittorio Klostermann');
INSERT INTO Organization (organizationID, shortName) values (379, 'Project Euclid');
INSERT INTO Organization (organizationID, shortName) values (380, 'Psychonomic Society ');
INSERT INTO Organization (organizationID, shortName) values (411, 'Cengage Learning');
INSERT INTO Organization (organizationID, shortName) values (382, 'Infotrieve');
INSERT INTO Organization (organizationID, shortName) values (383, 'Society of Automotive Engineers');
INSERT INTO Organization (organizationID, shortName) values (384, 'Turpion Publications');
INSERT INTO Organization (organizationID, shortName) values (426, 'Midwest Collaborative for Library Services');
INSERT INTO Organization (organizationID, shortName) values (386, 'Lawrence Erlbaum Associates');
INSERT INTO Organization (organizationID, shortName) values (387, 'Alphagraphics');
INSERT INTO Organization (organizationID, shortName) values (388, 'Bellerophon Publications, Inc.');
INSERT INTO Organization (organizationID, shortName) values (389, 'Boydell & Brewer Inc.');
INSERT INTO Organization (organizationID, shortName) values (390, 'Carcanet Press');
INSERT INTO Organization (organizationID, shortName) values (391, 'Feminist Studies');
INSERT INTO Organization (organizationID, shortName) values (393, 'Dustbooks');
INSERT INTO Organization (organizationID, shortName) values (394, 'Society for Applied Anthropology ');
INSERT INTO Organization (organizationID, shortName) values (395, 'United Nations Publications');
INSERT INTO Organization (organizationID, shortName) values (396, 'Wharton Research Data Services');
INSERT INTO Organization (organizationID, shortName) values (398, 'Human Development');
INSERT INTO Organization (organizationID, shortName) values (399, 'infoUSA Marketing, Inc.');
INSERT INTO Organization (organizationID, shortName) values (400, 'Bowker');
INSERT INTO Organization (organizationID, shortName) values (402, 'Brown University');
INSERT INTO Organization (organizationID, shortName) values (401, 'Women Writers Project');
INSERT INTO Organization (organizationID, shortName) values (445, 'Coutts');
INSERT INTO Organization (organizationID, shortName) values (446, 'Numara Software, Inc.');
INSERT INTO Organization (organizationID, shortName) values (447, 'Trinity College Library Dublin');
INSERT INTO Organization (organizationID, shortName) values (405, 'Oldenbourg Wissenschaftsverlag ');
INSERT INTO Organization (organizationID, shortName) values (406, 'Dow Jones');
INSERT INTO Organization (organizationID, shortName) values (412, 'Financial Information Inc. (FII)');
INSERT INTO Organization (organizationID, shortName) values (408, 'Jackson Publishing and Distribution');
INSERT INTO Organization (organizationID, shortName) values (409, 'Elsevier Engineering Information, Inc. ');
INSERT INTO Organization (organizationID, shortName) values (410, 'Eneclann Ltd.');
INSERT INTO Organization (organizationID, shortName) values (413, 'UCLA Latin American Institute');
INSERT INTO Organization (organizationID, shortName) values (414, 'Harmonie Park Press ');
INSERT INTO Organization (organizationID, shortName) values (415, 'Harrassowitz');
INSERT INTO Organization (organizationID, shortName) values (416, 'Thomson Reuters');
INSERT INTO Organization (organizationID, shortName) values (418, 'Human Relations Area Files, Inc. ');
INSERT INTO Organization (organizationID, shortName) values (432, 'Capital IQ');
INSERT INTO Organization (organizationID, shortName) values (419, 'Society for Ethnomusicology');
INSERT INTO Organization (organizationID, shortName) values (420, 'MSCI RiskMetrics');
INSERT INTO Organization (organizationID, shortName) values (421, 'Rapid Multimedia');
INSERT INTO Organization (organizationID, shortName) values (422, 'MSCI Inc');
INSERT INTO Organization (organizationID, shortName) values (423, 'New England Journal of Medicine');
INSERT INTO Organization (organizationID, shortName) values (424, 'NetLibrary');
INSERT INTO Organization (organizationID, shortName) values (425, 'CARMA');
INSERT INTO Organization (organizationID, shortName) values (427, 'Public Library of Science');
INSERT INTO Organization (organizationID, shortName) values (428, 'Social Science Electronic Publishing');
INSERT INTO Organization (organizationID, shortName) values (429, 'United Nations Industrial Develoipment Organization');
INSERT INTO Organization (organizationID, shortName) values (430, 'University of Michigan Press');
INSERT INTO Organization (organizationID, shortName) values (431, 'ORS Publishing, Inc.');
INSERT INTO Organization (organizationID, shortName) values (433, 'McGraw-Hill');
INSERT INTO Organization (organizationID, shortName) values (434, 'Biblical Archaeology Society');
INSERT INTO Organization (organizationID, shortName) values (435, 'GeoLytics, Inc.');
INSERT INTO Organization (organizationID, shortName) values (436, 'JoVE ');
INSERT INTO Organization (organizationID, shortName) values (437, 'ICEsoft Technologies, Inc.');
INSERT INTO Organization (organizationID, shortName) values (438, 'Films Media Group');
INSERT INTO Organization (organizationID, shortName) values (439, 'Films on Demand');
INSERT INTO Organization (organizationID, shortName) values (440, 'Connect Journals');
INSERT INTO Organization (organizationID, shortName) values (441, 'Scuola Normale Superiore');
INSERT INTO Organization (organizationID, shortName) values (442, 'Wolters Kluwer');
INSERT INTO Organization (organizationID, shortName) values (448, 'Pier Professional');
INSERT INTO Organization (organizationID, shortName) values (449, 'ABC News');
INSERT INTO Organization (organizationID, shortName) values (450, 'University of Aberdeen ');
INSERT INTO Organization (organizationID, shortName) values (451, 'BullFrog Films, Inc.');
INSERT INTO Organization (organizationID, shortName) values (453, 'FirstSearch');
INSERT INTO Organization (organizationID, shortName) values (455, 'History Cooperative ');
INSERT INTO Organization (organizationID, shortName) values (456, 'Omohundro Institute of Early American History and Culture');
INSERT INTO Organization (organizationID, shortName) values (457, 'Arms Control Association');
INSERT INTO Organization (organizationID, shortName) values (458, 'Heritage Archives');
INSERT INTO Organization (organizationID, shortName) values (459, 'International Historic Films, Inc.');
INSERT INTO Organization (organizationID, shortName) values (460, 'Euromonitor International ');
INSERT INTO Organization (organizationID, shortName) values (461, 'Safari Books Online');
INSERT INTO Organization (organizationID, shortName) values (462, 'Mirabile');
INSERT INTO Organization (organizationID, shortName) values (466, 'Publishing Technology');
INSERT INTO Organization (organizationID, shortName) values (463, 'SageWorks, Inc');
INSERT INTO Organization (organizationID, shortName) values (464, 'Johns Hopkins Universtiy Press');
INSERT INTO Organization (organizationID, shortName) values (465, 'Knovel ');
INSERT INTO Organization (organizationID, shortName) values (467, 'American Society of Nephrology');
INSERT INTO Organization (organizationID, shortName) values (468, 'Water Envrionment Federation ');
INSERT INTO Organization (organizationID, shortName) values (469, 'Refworks');
INSERT INTO Organization (organizationID, shortName) values (470, 'Cinemagician Productions');
INSERT INTO Organization (organizationID, shortName) values (471, 'Algorithmics');
INSERT INTO Organization (organizationID, shortName) values (472, 'YBP Library Services ');
INSERT INTO Organization (organizationID, shortName) values (474, 'Maydream Inc.');
INSERT INTO Organization (organizationID, shortName) values (475, 'Organization for Economic Cooperation and Development');
INSERT INTO Organization (organizationID, shortName) values (476, 'The Chronicle for Higher Education');
INSERT INTO Organization (organizationID, shortName) values (477, 'Association for Research in Vision and Ophthalmologie (ARVO)');
INSERT INTO Organization (organizationID, shortName) values (478, 'SRDS Media Solutions');
INSERT INTO Organization (organizationID, shortName) values (479, 'Kantar Media');
INSERT INTO Organization (organizationID, shortName) values (480, 'Peace & Justice Studies Association');
INSERT INTO Organization (organizationID, shortName) values (481, 'Addison Publications Ltd.');
INSERT INTO Organization (organizationID, shortName) values (482, 'Mutii-Science Publishing');
INSERT INTO Organization (organizationID, shortName) values (483, 'ASM International');
INSERT INTO Organization (organizationID, shortName) values (484, 'Verlag der Osterreichischen Akademie der Wissenschaften');
INSERT INTO Organization (organizationID, shortName) values (485, 'Anthology of Recorded Music');
INSERT INTO Organization (organizationID, shortName) values (486, 'Left Coast Press, Inc');
INSERT INTO Organization (organizationID, shortName) values (487, 'Video Data Bank');
INSERT INTO Organization (organizationID, shortName) values (488, 'Atlassian');
INSERT INTO Organization (organizationID, shortName) values (489, 'medici.tv');
INSERT INTO Organization (organizationID, shortName) values (490, 'Bar Ilan Research & Development Company Ltd');
INSERT INTO Organization (organizationID, shortName) values (491, 'Primary Source Media');
INSERT INTO Organization (organizationID, shortName) values (492, 'Ebrary');
INSERT INTO Organization (organizationID, shortName) values (493, 'University of Michigan, Department of Mathematics');
INSERT INTO Organization (organizationID, shortName) values (494, 'StataCorp LP ');
INSERT INTO Organization (organizationID, shortName) values (495, 'L\' Enseignement Mathematique  ');
INSERT INTO Organization (organizationID, shortName) values (496, 'Audio Engineering Society, Inc');
INSERT INTO Organization (organizationID, shortName) values (497, 'LOCKSS');
INSERT INTO Organization (organizationID, shortName) values (498, 'MUSEEC ');
INSERT INTO Organization (organizationID, shortName) values (499, 'Mortgage Bankers Association');
INSERT INTO Organization (organizationID, shortName) values (500, 'BibleWorks');
INSERT INTO Organization (organizationID, shortName) values (501, 'National Library of Ireland');
INSERT INTO Organization (organizationID, shortName) values (502, 'Scholars Press');
INSERT INTO Organization (organizationID, shortName) values (503, 'Index to Jewish periodicals');
INSERT INTO Organization (organizationID, shortName) values (504, 'Cold Spring Harbor Laboratory Press');



INSERT INTO OrganizationRole (shortName) values ("Consortium");
INSERT INTO OrganizationRole (shortName) values ("Library");
INSERT INTO OrganizationRole (shortName) values ("Platform");
INSERT INTO OrganizationRole (shortName) values ("Provider");
INSERT INTO OrganizationRole (shortName) values ("Publisher");
INSERT INTO OrganizationRole (shortName) values ("Vendor");



INSERT INTO `Privilege` (shortName) values ('admin');
INSERT INTO `Privilege` (shortName) values ('add/edit');
INSERT INTO `Privilege` (shortName) values ('view only');


INSERT INTO `PurchaseSite` (shortName) values ('Main Library');


INSERT INTO `RelationshipType` (shortName) values ('Parent');
INSERT INTO `RelationshipType` (shortName) values ('General');



INSERT INTO `ResourceFormat` (resourceFormatID, shortName) values (1, 'Print + Electronic');
INSERT INTO `ResourceFormat` (resourceFormatID, shortName) values (2, 'Electronic');
INSERT INTO `ResourceFormat` (resourceFormatID, shortName) values (3, 'Any');

INSERT INTO `ResourceType` (resourceTypeID, shortName) values (1, 'Any');


INSERT INTO `Status` (shortName) values ('In Progress');
INSERT INTO `Status` (shortName) values ('Completed');
INSERT INTO `Status` (shortName) values ('Saved');
INSERT INTO `Status` (shortName) values ('Archived');


INSERT INTO `StorageLocation` (shortName) values ('Reserve Book Room');

INSERT INTO `UserLimit` (shortName) values ('1');
INSERT INTO `UserLimit` (shortName) values ('2');
INSERT INTO `UserLimit` (shortName) values ('3');
INSERT INTO `UserLimit` (shortName) values ('4');
INSERT INTO `UserLimit` (shortName) values ('5');
INSERT INTO `UserLimit` (shortName) values ('6');
INSERT INTO `UserLimit` (shortName) values ('7');
INSERT INTO `UserLimit` (shortName) values ('8');
INSERT INTO `UserLimit` (shortName) values ('9');
INSERT INTO `UserLimit` (shortName) values ('10+');


INSERT INTO `Workflow` (workflowID, resourceFormatIDValue, resourceTypeIDValue, acquisitionTypeIDValue)
VALUES (1, '2', '','1');

INSERT INTO `Workflow` (workflowID, resourceFormatIDValue, resourceTypeIDValue, acquisitionTypeIDValue)
VALUES (2, '2', '','2');



INSERT INTO `UserGroup` (userGroupID, groupName) VALUES (1, 'Access');
INSERT INTO `UserGroup` (userGroupID, groupName) VALUES (2, 'Licensing');
INSERT INTO `UserGroup` (userGroupID, groupName) VALUES (3, 'Funding Approval');
INSERT INTO `UserGroup` (userGroupID, groupName) VALUES (4, 'Acquisitions');
INSERT INTO `UserGroup` (userGroupID, groupName) VALUES (5, 'Receipt');


INSERT INTO `Step` (stepID, priorStepID, stepName, userGroupID, workflowID, displayOrderSequence)
VALUES (1, NULL, 'Funding Approval', 3, 1, 1);
INSERT INTO `Step` (stepID, priorStepID, stepName, userGroupID, workflowID, displayOrderSequence)
VALUES (2, NULL, 'Licensing', 2, 1, 2);
INSERT INTO `Step` (stepID, priorStepID, stepName, userGroupID, workflowID, displayOrderSequence)
VALUES (3, 2, 'Order Processing', 4, 1, 3);
INSERT INTO `Step` (stepID, priorStepID, stepName, userGroupID, workflowID, displayOrderSequence)
VALUES (4, 3, 'Activation', 1, 1, 4);

INSERT INTO `Step` (stepID, priorStepID, stepName, userGroupID, workflowID, displayOrderSequence)
VALUES (5, NULL, 'Licensing', 2, 2, 1);
INSERT INTO `Step` (stepID, priorStepID, stepName, userGroupID, workflowID, displayOrderSequence)
VALUES (6, NULL, 'Activation', 1, 2, 2);
