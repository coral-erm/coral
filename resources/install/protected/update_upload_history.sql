CREATE TABLE `ImportHistory` (
  `importHistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `importDate` datetime NOT NULL,
  `filename` varchar(255) NOT NULL,
  `resourcesCount` int(11) NOT NULL,
  `importedResources` text NOT NULL,
  PRIMARY KEY (`importHistoryID`)
