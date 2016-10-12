CREATE TABLE `ImportHistory` (
  `importHistoryID` int(11) NOT NULL,
  `importDate` datetime NOT NULL,
  `filename` varchar(255) NOT NULL,
  `resourcesCount` int(11) NOT NULL,
  `importedResources` text NOT NULL
)
