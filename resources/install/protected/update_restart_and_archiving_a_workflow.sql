ALTER TABLE  `ResourceStep` ADD  `archivingDate` DATETIME NULL AFTER  `stepEndDate` ;
INSERT INTO `ResourceType` (`resourceTypeID`, `shortName`, `includeStats`) VALUES (NULL, 'Any', NULL);
INSERT INTO `ResourceFormat` (`resourceFormatID`, `shortName`) VALUES (NULL, 'Any');
INSERT INTO `AcquisitionType` (`acquisitionTypeID`, `shortName`) VALUES (NULL, 'Any');

