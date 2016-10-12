CREATE TABLE `coral_resources_prod_sgbm`.`ImportHistory` ( `importID` INT NOT NULL AUTO_INCREMENT , `importDate` DATETIME NOT NULL , `filename` VARCHAR NOT NULL , `importedResourcesID` INT NOT NULL , PRIMARY KEY (`importID`));
CREATE TABLE `coral_resources_prod_sgbm`.`ImportedResources` ( `importID` INT NOT NULL , `resourceID` INT NOT NULL )
