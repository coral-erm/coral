CREATE TABLE IF NOT EXISTS `Attachment` (
  `attachmentID` int(10) NOT NULL auto_increment,
  `licenseID` int(10) default NULL,
  `sentDate` date default NULL,
  `attachmentText` text,
  PRIMARY KEY  USING BTREE (`attachmentID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `AttachmentFile` (
  `attachmentFileID` int(10) unsigned NOT NULL auto_increment,
  `attachmentID` int(10) unsigned NOT NULL,
  `attachmentURL` varchar(200) NOT NULL,
  PRIMARY KEY  USING BTREE (`attachmentFileID`)
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
  PRIMARY KEY  (`documentID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


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
  PRIMARY KEY  (`expressionID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `ExpressionNote` (
  `expressionNoteID` int(10) NOT NULL auto_increment,
  `expressionID` int(10) default NULL,
  `note` varchar(2000) default NULL,
  `displayOrderSeqNumber` int(10) default NULL,
  `lastUpdateDate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`expressionNoteID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



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
  PRIMARY KEY  (`licenseID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `Privilege` (
  `privilegeID` int(10) unsigned NOT NULL auto_increment,
  `shortName` varchar(50) default NULL,
  PRIMARY KEY  USING BTREE (`privilegeID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Organization`;
CREATE TABLE IF NOT EXISTS  `Organization` (
  `organizationID` int(10) unsigned NOT NULL auto_increment,
  `shortName` tinytext NOT NULL,
  PRIMARY KEY  USING BTREE (`organizationID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Qualifier` (
  `qualifierID` int(10) unsigned NOT NULL auto_increment,
  `expressionTypeID` INTEGER UNSIGNED NOT NULL,
  `shortName` varchar(45) NOT NULL,
  PRIMARY KEY  (`qualifierID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE  IF NOT EXISTS `ExpressionQualifierProfile` (
  `expressionID` INTEGER UNSIGNED NOT NULL,
  `qualifierID` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`expressionID`, `qualifierID`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `SFXProvider` (
  `sfxProviderID` int(10) unsigned NOT NULL auto_increment,
  `documentID` int(10) unsigned NOT NULL,
  `shortName` varchar(245) NOT NULL,
  PRIMARY KEY  (`sfxProviderID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `Signature` (
  `signatureID` int(10) unsigned NOT NULL auto_increment,
  `documentID` int(10) unsigned NOT NULL,
  `signatureTypeID` int(10) unsigned NOT NULL,
  `signatureDate` date default NULL,
  `signerName` tinytext,
  PRIMARY KEY  (`signatureID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;



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


CREATE TABLE IF NOT EXISTS `User` (
  `loginID` varchar(50) NOT NULL,
  `lastName` varchar(45) default NULL,
  `firstName` varchar(45) default NULL,
  `privilegeID` int(10) unsigned default NULL,
  `emailAddressForTermsTool` varchar(150) default NULL,
  PRIMARY KEY  USING BTREE (`loginID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS CalendarSettings (
  `calendarSettingsID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `shortName` tinytext NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`calendarSettingsID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DELETE FROM DocumentType;
INSERT INTO DocumentType (shortName) values ('SERU');
INSERT INTO DocumentType (shortName) values ('Internal Acknowledgment');
INSERT INTO DocumentType (shortName) values ('Agreement');
INSERT INTO DocumentType (shortName) values ('Countersigned Agreement');
INSERT INTO DocumentType (shortName) values ('Amendment');
INSERT INTO DocumentType (shortName) values ('Consortium Authorization Statement');
INSERT INTO DocumentType (shortName) values ('Order Form');

DELETE FROM ExpressionType;
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (1, 'Authorized Users','Internal');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (2, 'Interlibrary Loan','Display');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (3, 'Coursepacks','Display');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (4, 'eReserves','Display');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (5, 'Post Cancellation Access','Internal');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (6, 'General Notes','Internal');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (7, 'Jurisdiction (Choice of Forum)','Internal');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (8, 'Third Party Archiving','Internal');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (9, 'Confidentiality Clause','Internal');
INSERT INTO ExpressionType (expressionTypeID, shortName, noteType) values (10, 'Multi-year Term','Internal');


DELETE FROM Qualifier;
INSERT INTO Qualifier (expressionTypeID, shortName) values (2, 'Not Clear');
INSERT INTO Qualifier (expressionTypeID, shortName) values (2, 'Not Reviewed');
INSERT INTO Qualifier (expressionTypeID, shortName) values (2, 'Prohibited');
INSERT INTO Qualifier (expressionTypeID, shortName) values (2, 'Permitted');
INSERT INTO Qualifier (expressionTypeID, shortName) values (3, 'Not Clear');
INSERT INTO Qualifier (expressionTypeID, shortName) values (3, 'Not Reviewed');
INSERT INTO Qualifier (expressionTypeID, shortName) values (3, 'Prohibited');
INSERT INTO Qualifier (expressionTypeID, shortName) values (3, 'Permitted');


DELETE FROM Privilege;
INSERT INTO Privilege (privilegeID, shortName) values (1, 'admin');
INSERT INTO Privilege (privilegeID, shortName) values (2, 'add/edit');
INSERT INTO Privilege (privilegeID, shortName) values (3, 'view only');
INSERT INTO Privilege (privilegeID, shortName) values (4, 'restricted');

DELETE FROM Organization;
INSERT INTO Organization (shortName) values ("Accessible Archives Inc");
INSERT INTO Organization (shortName) values ("ACCU Weather Sales and Services, Inc");
INSERT INTO Organization (shortName) values ("Adam Matthew Digital Ltd");
INSERT INTO Organization (shortName) values ("Agricultural History Society");
INSERT INTO Organization (shortName) values ("Agricultural Institute of Canada");
INSERT INTO Organization (shortName) values ("AICPA");
INSERT INTO Organization (shortName) values ("Akademiai Kiado");
INSERT INTO Organization (shortName) values ("Albert C. Muller");
INSERT INTO Organization (shortName) values ("Alexander Street Press, LLC");
INSERT INTO Organization (shortName) values ("Allen Press");
INSERT INTO Organization (shortName) values ("Alliance for Children and Families");
INSERT INTO Organization (shortName) values ("American Academy of Religion");
INSERT INTO Organization (shortName) values ("American Association for Cancer Research (AACR)");
INSERT INTO Organization (shortName) values ("American Association for the Advancement of Science (AAAS)");
INSERT INTO Organization (shortName) values ("American Association of Immunologists, Inc.");
INSERT INTO Organization (shortName) values ("American Concrete Institute (ACI)");
INSERT INTO Organization (shortName) values ("American Council of Learned Societies (ACLS)");
INSERT INTO Organization (shortName) values ("American Counseling Association");
INSERT INTO Organization (shortName) values ("American Economic Association (AEA)");
INSERT INTO Organization (shortName) values ("American Fisheries Society");
INSERT INTO Organization (shortName) values ("American Geophysical Union");
INSERT INTO Organization (shortName) values ("American Insitute of Physics (AIP)");
INSERT INTO Organization (shortName) values ("American Institute of Aeronautics and Astronautics (AIAA)");
INSERT INTO Organization (shortName) values ("American Library Association (ALA)");
INSERT INTO Organization (shortName) values ("American Mathematical Society (AMS)");
INSERT INTO Organization (shortName) values ("American Medical Association (AMA)");
INSERT INTO Organization (shortName) values ("American Meteorological Society (AMS)");
INSERT INTO Organization (shortName) values ("American Physical Society (APS)");
INSERT INTO Organization (shortName) values ("American Physiological Society");
INSERT INTO Organization (shortName) values ("American Phytopathological Society");
INSERT INTO Organization (shortName) values ("American Psychiatric Publishing");
INSERT INTO Organization (shortName) values ("American Psychological Association (APA)");
INSERT INTO Organization (shortName) values ("American Society for Cell Biology");
INSERT INTO Organization (shortName) values ("American Society for Clinical Investigation");
INSERT INTO Organization (shortName) values ("American Society for Horticultural Science");
INSERT INTO Organization (shortName) values ("American Society for Nutrition");
INSERT INTO Organization (shortName) values ("American Society for Testing and Materials (ASTM)");
INSERT INTO Organization (shortName) values ("American Society of Agronomy");
INSERT INTO Organization (shortName) values ("American Society of Civil Engineers (ASCE)");
INSERT INTO Organization (shortName) values ("American Society of Limnology and Oceanography (ASLO)");
INSERT INTO Organization (shortName) values ("American Society of Plant Biologists");
INSERT INTO Organization (shortName) values ("American Society of Tropical Medicine and Hygiene");
INSERT INTO Organization (shortName) values ("American Statistical Association");
INSERT INTO Organization (shortName) values ("Ammons Scientific Limited");
INSERT INTO Organization (shortName) values ("Annual Reviews");
INSERT INTO Organization (shortName) values ("Antiquity Publications Limited");
INSERT INTO Organization (shortName) values ("Applied Probability Trust");
INSERT INTO Organization (shortName) values ("Army Times Publishing Company");
INSERT INTO Organization (shortName) values ("ARTstor Inc");
INSERT INTO Organization (shortName) values ("Asempa Limited");
INSERT INTO Organization (shortName) values ("Association of Research Libraries (ARL)");
INSERT INTO Organization (shortName) values ("Atypon Systems Inc");
INSERT INTO Organization (shortName) values ("Augustine Institute");
INSERT INTO Organization (shortName) values ("Barkhuis Publishing");
INSERT INTO Organization (shortName) values ("Begell House, Inc");
INSERT INTO Organization (shortName) values ("Beilstein");
INSERT INTO Organization (shortName) values ("Belser Wissenschaftlicher Dienst Ltd");
INSERT INTO Organization (shortName) values ("Berg Publishers");
INSERT INTO Organization (shortName) values ("Berghahn Books");
INSERT INTO Organization (shortName) values ("Berkeley Electronic Press");
INSERT INTO Organization (shortName) values ("BIGresearch LLC");
INSERT INTO Organization (shortName) values ("BioMed Central");
INSERT INTO Organization (shortName) values ("BioOne");
INSERT INTO Organization (shortName) values ("Blackwell Publishing");
INSERT INTO Organization (shortName) values ("BMJ Publishing Group Limited");
INSERT INTO Organization (shortName) values ("Boopsie, INC.");
INSERT INTO Organization (shortName) values ("Botanical Society of America");
INSERT INTO Organization (shortName) values ("Boyd Printing");
INSERT INTO Organization (shortName) values ("Brepols Publishers");
INSERT INTO Organization (shortName) values ("Brill");
INSERT INTO Organization (shortName) values ("Bulletin of the Atomic Scientists");
INSERT INTO Organization (shortName) values ("Bureau of National Affairs, Inc");
INSERT INTO Organization (shortName) values ("Business Monitor International");
INSERT INTO Organization (shortName) values ("CABI Publishing");
INSERT INTO Organization (shortName) values ("Cambridge Crystallographic Data Centre");
INSERT INTO Organization (shortName) values ("Cambridge Scientific Abstracts");
INSERT INTO Organization (shortName) values ("Cambridge University Press");
INSERT INTO Organization (shortName) values ("Canadian Association of African Studies");
INSERT INTO Organization (shortName) values ("Canadian Mathematical Society");
INSERT INTO Organization (shortName) values ("Carbon Disclosure Project");
INSERT INTO Organization (shortName) values ("CareerShift LLC");
INSERT INTO Organization (shortName) values ("CCH Incorporated");
INSERT INTO Organization (shortName) values ("Centro de Investigaciones Sociologicas");
INSERT INTO Organization (shortName) values ("Chemical Abstracts Service (CAS)");
INSERT INTO Organization (shortName) values ("Chiniquy Collection");
INSERT INTO Organization (shortName) values ("Chorus America");
INSERT INTO Organization (shortName) values ("Chronicle of Higher Education");
INSERT INTO Organization (shortName) values ("Colegio de Mexico");
INSERT INTO Organization (shortName) values ("College Art Association");
INSERT INTO Organization (shortName) values ("Company of Biologists Ltd");
INSERT INTO Organization (shortName) values ("Competitive Media Reporting, LLC (TNS Media Intelligence TNSMI)");
INSERT INTO Organization (shortName) values ("Consejo Superior de Investigaciones Cientificas (CSIC)");
INSERT INTO Organization (shortName) values ("Consumer Electronics Association");
INSERT INTO Organization (shortName) values ("Cornell University Library");
INSERT INTO Organization (shortName) values ("Corporacion Latinobarometro");
INSERT INTO Organization (shortName) values ("Corporation for National Research Initiatives (CNRI)");
INSERT INTO Organization (shortName) values ("CQ Press");
INSERT INTO Organization (shortName) values ("CSIRO Publishing");
INSERT INTO Organization (shortName) values ("Current History, Inc");
INSERT INTO Organization (shortName) values ("Dialog");
INSERT INTO Organization (shortName) values ("Dialogue Foundation");
INSERT INTO Organization (shortName) values ("Digital Distributed Community Archive");
INSERT INTO Organization (shortName) values ("Digital Heritage Publishing Limited");
INSERT INTO Organization (shortName) values ("Duke University Press");
INSERT INTO Organization (shortName) values ("Dun and Bradstreet Inc");
INSERT INTO Organization (shortName) values ("Dunstans Publishing Ltd");
INSERT INTO Organization (shortName) values ("East View Information Services");
INSERT INTO Organization (shortName) values ("EBSCO");
INSERT INTO Organization (shortName) values ("Ecological Society of America");
INSERT INTO Organization (shortName) values ("Edinburgh University Press");
INSERT INTO Organization (shortName) values ("EDP Sciences");
INSERT INTO Organization (shortName) values ("Elsevier");
INSERT INTO Organization (shortName) values ("Encyclopaedia Britannica Online");
INSERT INTO Organization (shortName) values ("Endocrine Society");
INSERT INTO Organization (shortName) values ("Entomological Society of Canada");
INSERT INTO Organization (shortName) values ("Equinox Publishing Ltd");
INSERT INTO Organization (shortName) values ("European Mathematical Society Publishing House");
INSERT INTO Organization (shortName) values ("European Society of Endocrinology");
INSERT INTO Organization (shortName) values ("Evolutionary Ecology Ltd");
INSERT INTO Organization (shortName) values ("ExLibris");
INSERT INTO Organization (shortName) values ("Experian Marketing Solutions, Inc.");
INSERT INTO Organization (shortName) values ("FamilyLink.com, Inc.");
INSERT INTO Organization (shortName) values ("FamilyLink.com, Inc.");
INSERT INTO Organization (shortName) values ("Faulkner Information Services");
INSERT INTO Organization (shortName) values ("Federation of American Societies for Experimental Biology");
INSERT INTO Organization (shortName) values ("Forrester Research, Inc");
INSERT INTO Organization (shortName) values ("Franz Steiner Verlag");
INSERT INTO Organization (shortName) values ("Genetics Society of America");
INSERT INTO Organization (shortName) values ("Geographic Research, Inc");
INSERT INTO Organization (shortName) values ("GeoScienceWorld");
INSERT INTO Organization (shortName) values ("Global Science Press");
INSERT INTO Organization (shortName) values ("Grove Dictionaries, Inc");
INSERT INTO Organization (shortName) values ("GuideStar USA, Inc");
INSERT INTO Organization (shortName) values ("H.W. Wilson Company");
INSERT INTO Organization (shortName) values ("H1 Base, Inc");
INSERT INTO Organization (shortName) values ("Hans Zell Publishing");
INSERT INTO Organization (shortName) values ("Haworth Press");
INSERT INTO Organization (shortName) values ("Heldref Publications");
INSERT INTO Organization (shortName) values ("HighWire Press");
INSERT INTO Organization (shortName) values ("Histochemical Society");
INSERT INTO Organization (shortName) values ("Human Kinetics Inc.");
INSERT INTO Organization (shortName) values ("IBISWorld USA");
INSERT INTO Organization (shortName) values ("Idea Group Inc");
INSERT INTO Organization (shortName) values ("IEEE");
INSERT INTO Organization (shortName) values ("Incisive Media Ltd");
INSERT INTO Organization (shortName) values ("Indiana University Mathematics Journal");
INSERT INTO Organization (shortName) values ("Informa Healthcare USA, Inc");
INSERT INTO Organization (shortName) values ("Information Resources, Inc");
INSERT INTO Organization (shortName) values ("INFORMS");
INSERT INTO Organization (shortName) values ("Ingentaconnect");
INSERT INTO Organization (shortName) values ("Institute of Mathematics of the Polish Academy of Sciences");
INSERT INTO Organization (shortName) values ("Institute of Physics (IOP)");
INSERT INTO Organization (shortName) values ("Institution of Engineering and Technology (IET)");
INSERT INTO Organization (shortName) values ("Institutional Shareholder Services Inc");
INSERT INTO Organization (shortName) values ("InteLex");
INSERT INTO Organization (shortName) values ("Intellect");
INSERT INTO Organization (shortName) values ("Intelligence Research Limited");
INSERT INTO Organization (shortName) values ("International Press");
INSERT INTO Organization (shortName) values ("IOS Press");
INSERT INTO Organization (shortName) values ("IPA Source, LLC");
INSERT INTO Organization (shortName) values ("Irish Newspaper Archives Ltd");
INSERT INTO Organization (shortName) values ("ITHAKA");
INSERT INTO Organization (shortName) values ("IVES Group, Inc");
INSERT INTO Organization (shortName) values ("Japan Focus");
INSERT INTO Organization (shortName) values ("John Benjamins Publishing Company");
INSERT INTO Organization (shortName) values ("JSTOR");
INSERT INTO Organization (shortName) values ("Karger");
INSERT INTO Organization (shortName) values ("Keesings Worldwide, LLC");
INSERT INTO Organization (shortName) values ("KLD Research and Analytics Inc");
INSERT INTO Organization (shortName) values ("Landes Bioscience");
INSERT INTO Organization (shortName) values ("LexisNexis");
INSERT INTO Organization (shortName) values ("Librairie Droz");
INSERT INTO Organization (shortName) values ("Library of Congress, Cataloging Distribution Service");
INSERT INTO Organization (shortName) values ("Lipper Inc");
INSERT INTO Organization (shortName) values ("Liverpool University Press");
INSERT INTO Organization (shortName) values ("Lord Music Reference Inc");
INSERT INTO Organization (shortName) values ("M.E. Sharpe, Inc");
INSERT INTO Organization (shortName) values ("Manchester University Press");
INSERT INTO Organization (shortName) values ("Marine Biological Laboratory");
INSERT INTO Organization (shortName) values ("MarketResearch.com, Inc");
INSERT INTO Organization (shortName) values ("Marquis Who's Who LLC");
INSERT INTO Organization (shortName) values ("Mary Ann Liebert, Inc");
INSERT INTO Organization (shortName) values ("Massachusetts Medical Society");
INSERT INTO Organization (shortName) values ("Mathematical Sciences Publishers");
INSERT INTO Organization (shortName) values ("Mediamark Research and Intelligence, LLC");
INSERT INTO Organization (shortName) values ("Mergent, Inc");
INSERT INTO Organization (shortName) values ("Metropolitan Opera");
INSERT INTO Organization (shortName) values ("Mintel International Group Limited");
INSERT INTO Organization (shortName) values ("MIT Press");
INSERT INTO Organization (shortName) values ("MIT");
INSERT INTO Organization (shortName) values ("Morningstar Inc.");
INSERT INTO Organization (shortName) values ("National Academy of Sciences");
INSERT INTO Organization (shortName) values ("National Gallery Company Ltd");
INSERT INTO Organization (shortName) values ("National Research Council of Canada");
INSERT INTO Organization (shortName) values ("Nature Publishing Group");
INSERT INTO Organization (shortName) values ("Naxos Digital Services Limited");
INSERT INTO Organization (shortName) values ("Neilson Journals Publishing");
INSERT INTO Organization (shortName) values ("New York Review of Books");
INSERT INTO Organization (shortName) values ("NewsBank, Inc");
INSERT INTO Organization (shortName) values ("OCLC");
INSERT INTO Organization (shortName) values ("Otto Harrassowitz");
INSERT INTO Organization (shortName) values ("Ovid");
INSERT INTO Organization (shortName) values ("Oxford Centre of Hebrew and Jewish Studies");
INSERT INTO Organization (shortName) values ("Oxford University Press");
INSERT INTO Organization (shortName) values ("Paradigm Publishers");
INSERT INTO Organization (shortName) values ("Paratext");
INSERT INTO Organization (shortName) values ("Peeters Publishers");
INSERT INTO Organization (shortName) values ("Philosophy Documentation Center");
INSERT INTO Organization (shortName) values ("Portland Press Limited");
INSERT INTO Organization (shortName) values ("Preservation Technologies LP");
INSERT INTO Organization (shortName) values ("Project Muse");
INSERT INTO Organization (shortName) values ("ProQuest LLC");
INSERT INTO Organization (shortName) values ("Psychoanalytic Electronic Publishing Inc");
INSERT INTO Organization (shortName) values ("R.R. Bowker");
INSERT INTO Organization (shortName) values ("Religious and Theological Abstracts, Inc");
INSERT INTO Organization (shortName) values ("Reuters Loan Pricing Corporation");
INSERT INTO Organization (shortName) values ("Risk Management Association (RMA)");
INSERT INTO Organization (shortName) values ("Rivista di Studi italiani");
INSERT INTO Organization (shortName) values ("Robert Blakemore");
INSERT INTO Organization (shortName) values ("Rockefeller University Press");
INSERT INTO Organization (shortName) values ("Roper Center for Public Opinion Research");
INSERT INTO Organization (shortName) values ("Royal Society of Chemistry");
INSERT INTO Organization (shortName) values ("Royal Society of London");
INSERT INTO Organization (shortName) values ("SAGE Publications");
INSERT INTO Organization (shortName) values ("Scholarly Digital Editions");
INSERT INTO Organization (shortName) values ("Seminario Matematico of the University of Padua");
INSERT INTO Organization (shortName) values ("Simmons Market Research Bureau Inc");
INSERT INTO Organization (shortName) values ("SISMEL - Edizioni del Galluzzo");
INSERT INTO Organization (shortName) values ("Social Explorer");
INSERT INTO Organization (shortName) values ("Societe Mathematique de France");
INSERT INTO Organization (shortName) values ("Society for Endocrinology");
INSERT INTO Organization (shortName) values ("Society for Experimental Biology and Medicine");
INSERT INTO Organization (shortName) values ("Society for General Microbiology");
INSERT INTO Organization (shortName) values ("Society for Industrial and Applied Mathematics (SIAM)");
INSERT INTO Organization (shortName) values ("Society for Leukocyte Biology");
INSERT INTO Organization (shortName) values ("Society for Neuroscience");
INSERT INTO Organization (shortName) values ("Society for Reproduction and Fertility");
INSERT INTO Organization (shortName) values ("Society of Antiquaries of Scotland");
INSERT INTO Organization (shortName) values ("Society of Environmental Toxicology and Chemistry");
INSERT INTO Organization (shortName) values ("SPIE");
INSERT INTO Organization (shortName) values ("Springer");
INSERT INTO Organization (shortName) values ("Standard and Poor's");
INSERT INTO Organization (shortName) values ("Stanford University");
INSERT INTO Organization (shortName) values ("Swank Motion Pictures, Inc");
INSERT INTO Organization (shortName) values ("Swiss Chemical Society");
INSERT INTO Organization (shortName) values ("Tablet Publishing (London)");
INSERT INTO Organization (shortName) values ("Taylor and Francis");
INSERT INTO Organization (shortName) values ("Teachers College Record");
INSERT INTO Organization (shortName) values ("Terra Scientific Publishing Company");
INSERT INTO Organization (shortName) values ("Tetrad Computer Applications Inc");
INSERT INTO Organization (shortName) values ("The Academy of the Hebrew Language");
INSERT INTO Organization (shortName) values ("Thesaurus Linguae Graecae");
INSERT INTO Organization (shortName) values ("Thomas Telford Ltd");
INSERT INTO Organization (shortName) values ("Thomson Financial Inc");
INSERT INTO Organization (shortName) values ("Thomson Gale");
INSERT INTO Organization (shortName) values ("Thomson RIA");
INSERT INTO Organization (shortName) values ("Thomson Scientific Inc. (Institute for Scientific Information, Inc.)");
INSERT INTO Organization (shortName) values ("Trans Tech Publications");
INSERT INTO Organization (shortName) values ("Transportation Research Board");
INSERT INTO Organization (shortName) values ("U.S. Department of Commerce");
INSERT INTO Organization (shortName) values ("UCLA Chicano Studies Research Center Press");
INSERT INTO Organization (shortName) values ("University of Barcelona");
INSERT INTO Organization (shortName) values ("University of Buckingham Press");
INSERT INTO Organization (shortName) values ("University of California Press");
INSERT INTO Organization (shortName) values ("University of Chicago Press");
INSERT INTO Organization (shortName) values ("University of Houston Department of Mathematics");
INSERT INTO Organization (shortName) values ("University of Illinois Press");
INSERT INTO Organization (shortName) values ("University of Iowa");
INSERT INTO Organization (shortName) values ("University of Pittsburgh");
INSERT INTO Organization (shortName) values ("University of Toronto Press Inc");
INSERT INTO Organization (shortName) values ("University of Toronto");
INSERT INTO Organization (shortName) values ("University of Virginia Press");
INSERT INTO Organization (shortName) values ("University of Wisconsin Press");
INSERT INTO Organization (shortName) values ("Universum USA");
INSERT INTO Organization (shortName) values ("Uniworld Business Publications, Inc");
INSERT INTO Organization (shortName) values ("Value Line, Inc");
INSERT INTO Organization (shortName) values ("Vanderbilt University");
INSERT INTO Organization (shortName) values ("Vault, Inc");
INSERT INTO Organization (shortName) values ("Verlag C.H. Beck");
INSERT INTO Organization (shortName) values ("Verlag der Zeitschrift fur Naturforschung ");
INSERT INTO Organization (shortName) values ("W.S. Maney and Son Ltd");
INSERT INTO Organization (shortName) values ("Walter de Gruyter");
INSERT INTO Organization (shortName) values ("White Horse Press");
INSERT INTO Organization (shortName) values ("Wiley");
INSERT INTO Organization (shortName) values ("World Scientific");
INSERT INTO Organization (shortName) values ("World Trade Press");
INSERT INTO Organization (shortName) values ("Worldwatch Institute");
INSERT INTO Organization (shortName) values ("Yankelovich Inc");


DELETE FROM SignatureType;
INSERT INTO SignatureType (shortName) values ("Agent");
INSERT INTO SignatureType (shortName) values ("Consortium");
INSERT INTO SignatureType (shortName) values ("Internal");
INSERT INTO SignatureType (shortName) values ("Provider");

DELETE FROM Status;
INSERT INTO Status (shortName) values ("Awaiting Document");
INSERT INTO Status (shortName) values ("Complete");
INSERT INTO Status (shortName) values ("Document Only");
INSERT INTO Status (shortName) values ("Editing Expressions");
INSERT INTO Status (shortName) values ("NLR");

DELETE FROM Attachment;
DELETE FROM AttachmentFile;
DELETE FROM Consortium;
DELETE FROM Document;
DELETE FROM Expression;
DELETE FROM ExpressionNote;
DELETE FROM License;
DELETE FROM SFXProvider;
DELETE FROM Signature;

DELETE FROM CalendarSettings;
INSERT INTO CalendarSettings VALUES (1,'Days Before Subscription End','730');
INSERT INTO CalendarSettings VALUES (2,'Days After Subscription End','90');
INSERT INTO CalendarSettings VALUES (3,'Resource Type(s)','1');
INSERT INTO CalendarSettings VALUES (4,'Authorized Site(s)','1');


ALTER TABLE `Attachment` ADD INDEX `licenseID` ( `licenseID` );
ALTER TABLE `Document` ADD INDEX `licenseID` ( `licenseID` );
ALTER TABLE `Document` ADD INDEX `documentTypeID` ( `documentTypeID` );
ALTER TABLE `Document` ADD INDEX `parentDocumentID` ( `parentDocumentID` );
ALTER TABLE `Expression` ADD INDEX `documentID` ( `documentID` );
ALTER TABLE `Expression` ADD INDEX `expressionTypeID` ( `expressionTypeID` );
ALTER TABLE `ExpressionNote` ADD INDEX `expressionID` ( `expressionID` );
ALTER TABLE `License` ADD INDEX `organizationID` ( `organizationID` );
ALTER TABLE `License` ADD INDEX `consortiumID` ( `consortiumID` );
ALTER TABLE `License` ADD INDEX `statusID` ( `statusID` );
ALTER TABLE `SFXProvider` ADD INDEX `documentID` ( `documentID` );
ALTER TABLE `Signature` ADD INDEX `documentID` ( `documentID` );
ALTER TABLE `Signature` ADD INDEX `signatureTypeID` ( `signatureTypeID` );
ALTER TABLE `Qualifier` ADD INDEX `expressionTypeID` ( `expressionTypeID` );
