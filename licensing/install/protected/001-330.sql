
DROP TABLE IF EXISTS `NoteType`;
CREATE TABLE  `NoteType` (
  `noteTypeID` int(11) NOT NULL auto_increment,
  `shortName` varchar(200) default NULL,
  PRIMARY KEY  (`noteTypeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;



DROP TABLE IF EXISTS `DocumentNote`;
CREATE TABLE  `DocumentNote` (
  `documentNoteID` int(11) NOT NULL auto_increment,
  `documentID` int(11) default NULL,
  `noteTypeID` int(11) default NULL,
  `updateDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `updateLoginID` varchar(45) default NULL,
  `noteText` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY  (`documentNoteID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;


ALTER TABLE `DocumentNote` ADD INDEX `Index_documentID`(`documentID`), ADD INDEX `Index_noteTypeID`(`noteTypeID`), ADD INDEX `Index_All`(`documentID`, `noteTypeID`);

