DROP TABLE IF EXISTS `ExportConfig`;
CREATE TABLE  `ExportConfig` (
  `ID` int(11) NOT NULL auto_increment,
  `shortName` varchar(45) NOT NULL,
  `enabled` boolean NOT NULL default False,
  PRIMARY KEY  (`ID`)
) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `ExportConfig` (shortName)
VALUES ('Notes'),
       ('Parent Relationships'),
       ('Child Relationships');
