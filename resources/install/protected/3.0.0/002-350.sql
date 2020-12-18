-- Update schema
CREATE TABLE `ResourceAcquisition` (
  `resourceAcquisitionID` int(11) NOT NULL,
  `resourceID` int(11) NOT NULL,
  `parentResourceID` int(11) DEFAULT NULL,
  `orderNumber` varchar(45) DEFAULT NULL,
  `systemNumber` varchar(45) DEFAULT NULL,
  `acquisitionTypeID` int(11) DEFAULT NULL,
  `subscriptionStartDate` date NOT NULL,
  `subscriptionEndDate` date NOT NULL,
  `subscriptionAlertEnabledInd` int(11) DEFAULT NULL,
  `organizationID` int(11) DEFAULT NULL,
  `licenseID` int(11) DEFAULT NULL,
  `authenticationTypeID` int(10) DEFAULT NULL,
  `authenticationUserName` varchar(200) DEFAULT NULL,
  `authenticationPassword` varchar(200) DEFAULT NULL,
  `accessMethodID` int(10) DEFAULT NULL,
  `storageLocationID` int(11) DEFAULT NULL,
  `userLimitID` int(11) DEFAULT NULL,
  `coverageText` varchar(1000) DEFAULT NULL,
  `bibSourceURL` varchar(2000) DEFAULT NULL,
  `catalogingTypeID` int(11) DEFAULT NULL,
  `catalogingStatusID` int(11) DEFAULT NULL,
  `numberRecordsAvailable` varchar(45) DEFAULT NULL,
  `numberRecordsLoaded` varchar(45) DEFAULT NULL,
  `recordSetIdentifier` varchar(45) DEFAULT NULL,
  `hasOclcHoldings` varchar(10) DEFAULT NULL,
  `workflowRestartDate` date DEFAULT NULL,
  `workflowRestartLoginID` varchar(45) DEFAULT NULL
);
ALTER TABLE `ResourceAcquisition`
CHANGE resourceAcquisitionID resourceAcquisitionID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;
ALTER TABLE `ResourceAcquisition` ADD INDEX(`resourceID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`organizationID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`authenticationTypeID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`licenseID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`acquisitionTypeID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`parentResourceID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`accessMethodID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`storageLocationID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`userLimitID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`catalogingTypeID`);
ALTER TABLE `ResourceAcquisition` ADD INDEX(`catalogingStatusID`);


ALTER TABLE `ResourcePurchaseSiteLink` CHANGE `resourceID` `resourceAcquisitionID` INT(11) NULL DEFAULT NULL;
ALTER TABLE `ResourcePayment` CHANGE `resourceID` `resourceAcquisitionID` INT(11) UNSIGNED NOT NULL;
ALTER TABLE `ResourceAdministeringSiteLink` CHANGE `resourceID` `resourceAcquisitionID` INT(11) NULL DEFAULT NULL;
ALTER TABLE `ResourceAuthorizedSiteLink` CHANGE `resourceID` `resourceAcquisitionID` INT(11) NULL DEFAULT NULL;
ALTER TABLE `Attachment` CHANGE `resourceID` `resourceAcquisitionID` INT(11) NULL DEFAULT NULL;
ALTER TABLE `Contact` CHANGE `resourceID` `resourceAcquisitionID` INT(11) NOT NULL;
ALTER TABLE `ResourceLicenseLink` CHANGE `resourceID` `resourceAcquisitionID` INT(11) NULL DEFAULT NULL;
ALTER TABLE `ResourceLicenseStatus` CHANGE `resourceID` `resourceAcquisitionID` INT(11) NULL DEFAULT NULL;
ALTER TABLE `IssueRelationship` ADD `resourceAcquisitionID` INT(11) NULL DEFAULT NULL AFTER `entityTypeID`;
ALTER TABLE `Downtime` ADD `resourceAcquisitionID` INT(11) NULL DEFAULT NULL AFTER `note`;
ALTER TABLE `ResourceStep` CHANGE `resourceID` `resourceAcquisitionID` INT(11) NULL DEFAULT NULL;
ALTER TABLE `ResourceNote` CHANGE `resourceID` `entityID` INT(11) NULL DEFAULT NULL;

-- Migrate existing data
INSERT INTO ResourceAcquisition (resourceID,
orderNumber,
systemNumber,
acquisitionTypeID,
subscriptionAlertEnabledInd,
authenticationTypeID,
authenticationUserName,
authenticationPassword,
accessMethodID,
storageLocationID,
userLimitID,
coverageText,
bibSourceURL,
catalogingTypeID,
catalogingStatusID,
numberRecordsAvailable,
numberRecordsLoaded,
recordSetIdentifier,
hasOclcHoldings,
workflowRestartDate,
workflowRestartLoginID,
subscriptionStartDate,
subscriptionEndDate) SELECT Resource.resourceID,
Resource.orderNumber,
Resource.systemNumber,
Resource.acquisitionTypeID,
Resource.subscriptionAlertEnabledInd,
Resource.authenticationTypeID,
Resource.authenticationUserName,
Resource.authenticationPassword,
Resource.accessMethodID,
Resource.storageLocationID,
Resource.userLimitID,
Resource.coverageText,
Resource.bibSourceURL,
Resource.catalogingTypeID,
Resource.catalogingStatusID,
Resource.numberRecordsAvailable,
Resource.numberRecordsLoaded,
Resource.recordSetIdentifier,
Resource.hasOclcHoldings,
Resource.workflowRestartDate,
Resource.workflowRestartLoginID,
Resource.currentStartDate,
Resource.currentEndDate FROM Resource;

UPDATE ResourcePurchaseSiteLink LEFT JOIN ResourceAcquisition ON ResourcePurchaseSiteLink.resourceAcquisitionID = ResourceAcquisition.resourceID SET ResourcePurchaseSiteLink.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE ResourcePayment LEFT JOIN ResourceAcquisition ON ResourcePayment.resourceAcquisitionID = ResourceAcquisition.resourceID SET ResourcePayment.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE ResourceAdministeringSiteLink LEFT JOIN ResourceAcquisition ON ResourceAdministeringSiteLink.resourceAcquisitionID = ResourceAcquisition.resourceID SET ResourceAdministeringSiteLink.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE ResourceAuthorizedSiteLink LEFT JOIN ResourceAcquisition ON ResourceAuthorizedSiteLink.resourceAcquisitionID = ResourceAcquisition.resourceID SET ResourceAuthorizedSiteLink.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE Attachment LEFT JOIN ResourceAcquisition ON Attachment.resourceAcquisitionID = ResourceAcquisition.resourceID SET Attachment.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE Contact LEFT JOIN ResourceAcquisition ON Contact.resourceAcquisitionID = ResourceAcquisition.resourceID SET Contact.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE ResourceLicenseLink LEFT JOIN ResourceAcquisition ON ResourceLicenseLink.resourceAcquisitionID = ResourceAcquisition.resourceID SET ResourceLicenseLink.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE ResourceLicenseStatus LEFT JOIN ResourceAcquisition ON ResourceLicenseStatus.resourceAcquisitionID = ResourceAcquisition.resourceID SET ResourceLicenseStatus.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE ResourceStep LEFT JOIN ResourceAcquisition ON ResourceStep.resourceAcquisitionID = ResourceAcquisition.resourceID SET ResourceStep.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE Downtime LEFT JOIN ResourceAcquisition ON Downtime.entityID = ResourceAcquisition.resourceID SET Downtime.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE IssueRelationship LEFT JOIN ResourceAcquisition ON IssueRelationship.entityID = ResourceAcquisition.resourceID SET IssueRelationship.resourceAcquisitionID = ResourceAcquisition.resourceAcquisitionID;

UPDATE ResourceNote LEFT JOIN ResourceAcquisition ON ResourceNote.entityID = ResourceAcquisition.resourceID SET ResourceNote.entityID = ResourceAcquisition.resourceAcquisitionID WHERE UPPER(tabName) = 'CATALOGING';

UPDATE ResourceNote LEFT JOIN ResourceAcquisition ON ResourceNote.entityID = ResourceAcquisition.resourceID SET ResourceNote.entityID = ResourceAcquisition.resourceAcquisitionID WHERE UPPER(tabName) = 'ACCESS';
UPDATE ResourceNote LEFT JOIN ResourceAcquisition ON ResourceNote.entityID = ResourceAcquisition.resourceID SET ResourceNote.entityID = ResourceAcquisition.resourceAcquisitionID WHERE UPPER(tabName) = 'ACQUISITIONS';

-- Drop old fields
ALTER TABLE `Resource`
  DROP `workflowRestartDate`,
  DROP `workflowRestartLoginID`,
  DROP `orderNumber`,
  DROP `systemNumber`,
  DROP `subscriptionAlertEnabledInd`,
  DROP `userLimitID`,
  DROP `authenticationUserName`,
  DROP `authenticationPassword`,
  DROP `storageLocationID`,
  DROP `acquisitionTypeID`,
  DROP `authenticationTypeID`,
  DROP `accessMethodID`,
  DROP `recordSetIdentifier`,
  DROP `hasOclcHoldings`,
  DROP `numberRecordsAvailable`,
  DROP `numberRecordsLoaded`,
  DROP `bibSourceURL`,
  DROP `catalogingTypeID`,
  DROP `catalogingStatusID`,
  DROP `coverageText`;

