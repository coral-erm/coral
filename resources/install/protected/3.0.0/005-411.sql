CREATE TABLE `PricingFormula` (
  `pricingFormulaID` int(11) NOT NULL,
  `shortName` varchar(200) default NULL,
  `field1Name` varchar(200) default NULL,
  `field2Name` varchar(200) default NULL,
  `field3Name` varchar(200) default NULL,
  `field4Name` varchar(200) default NULL,
  `field5Name` varchar(200) default NULL,
  `field6Name` varchar(200) default NULL,
  `field7Name` varchar(200) default NULL,
  `field8Name` varchar(200) default NULL,
  `field9Name` varchar(200) default NULL,
  `field10Name` varchar(200) default NULL,
  `formula` TEXT default NULL
);

ALTER TABLE `PricingFormula` CHANGE pricingFormulaID pricingFormulaID INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY;

