DROP TABLE IF EXISTS `Report`;
CREATE TABLE IF NOT EXISTS  `Report` (
  `reportID` int(11) NOT NULL auto_increment,
  `reportName` varchar(45) NOT NULL,
  `defaultRecPageNumber` int(11) default '100',
  `excelOnlyInd` tinyint(1) default NULL,
  `reportDatabaseName` varchar(45) NOT NULL,
  PRIMARY KEY  (`reportID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ReportGroupingColumn`;
CREATE TABLE IF NOT EXISTS `ReportGroupingColumn` (
  `reportID` int(11) NOT NULL,
  `reportGroupingColumnName` varchar(45) NOT NULL,
  `reportGroupingColumnID` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  USING BTREE (`reportGroupingColumnID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ReportParameter`;
CREATE TABLE IF NOT EXISTS `ReportParameter` (
  `reportParameterID` int(11) NOT NULL auto_increment,
  `parameterTypeCode` varchar(45) default NULL,
  `parameterDisplayPrompt` varchar(45) default NULL,
  `parameterAddWhereClause` varchar(500) default NULL,
  `parameterAddWhereNumber` int(11) default NULL,
  `requiredInd` tinyint(1) default NULL,
  `parameterSQLStatement` text,
  `parameterSQLRestriction` text,
  PRIMARY KEY  (`reportParameterID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ReportParameterMap`;
CREATE TABLE IF NOT EXISTS `ReportParameterMap` (
  `reportID` int(11) default NULL,
  `reportParameterID` int(11) NOT NULL auto_increment,
  `parentReportParameterID` int(11) default NULL,
  PRIMARY KEY  (`reportID`,`reportParameterID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `ReportSum`;
CREATE TABLE IF NOT EXISTS `ReportSum` (
  `reportID` int(11) NOT NULL,
  `reportColumnName` varchar(45) default NULL,
  `reportAction` varchar(45) default NULL,
  PRIMARY KEY  (`reportID`,`reportColumnName`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


DELETE FROM Report;
INSERT INTO Report (reportID, reportName, defaultRecPageNumber, excelOnlyInd, reportDatabaseName)
VALUES ('1','Usage Statistics by Titles','100','0', 'usageDatabase');


INSERT INTO Report (reportID, reportName, defaultRecPageNumber, excelOnlyInd, reportDatabaseName)
VALUES ('2','Usage Statistics by Provider / Publisher','100','0', 'usageDatabase');


INSERT INTO Report (reportID, reportName, defaultRecPageNumber, excelOnlyInd, reportDatabaseName)
VALUES ('3','Usage Statistics - Provider Rollup','100','0', 'usageDatabase');


INSERT INTO Report (reportID, reportName, defaultRecPageNumber, excelOnlyInd, reportDatabaseName)
VALUES ('4','Usage Statistics - Publisher Rollup','100','0', 'usageDatabase');


INSERT INTO Report (reportID, reportName, defaultRecPageNumber, excelOnlyInd, reportDatabaseName)
VALUES ('5','Usage Statistics - Top Resource Requests','100', '0', 'usageDatabase');


INSERT INTO Report (reportID, reportName, defaultRecPageNumber, excelOnlyInd, reportDatabaseName)
VALUES ('6','Usage Statistics - Yearly Usage Statistics','100','0', 'usageDatabase');

DELETE FROM ReportSum;
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','JAN','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','FEB','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','MAR','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','APR','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','MAY','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','JUN','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','JUL','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','AUG','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','SEP','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','OCT','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','NOV','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','DEC','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','QUERY_TOTAL','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','YTD_HTML','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','YTD_PDF','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('1','YTD_TOTAL','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','JAN','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','FEB','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','MAR','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','APR','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','MAY','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','JUN','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','JUL','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','AUG','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','SEP','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','OCT','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','NOV','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','DEC','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','QUERY_TOTAL','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','YTD_HTML','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','YTD_PDF','sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportAction)  VALUES ('2','YTD_TOTAL','sum');

DELETE FROM ReportParameter;
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('1','chk','Do not adjust numbers for use violations','Overriden','0','0','','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('2','txt','ISSN/ISBN/DOI','(ti2.identifier = \'PARM\' OR ti2.identifier = REPLACE(\'PARM\',"-",""))','1','0','','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('3','txt','Title Search','upper(t2.title) like upper(\'%PARM%\')','1','0','','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('4','dd','Provider / Publisher','(concat(\'PL_\', CAST(Platform.platformID AS CHAR)) = \'PARM\' OR concat(\'PB_\', CAST(pp.publisherPlatformID AS CHAR)) = \'PARM\')','0','0','SELECT concat(\'PL_\', CAST(Platform.platformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM Platform WHERE reportDropDownInd = 1 UNION SELECT concat(\'PB_\', CAST(publisherPlatformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM PublisherPlatform WHERE reportDropDownInd = 1 ORDER BY 3','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('5','ms','Provider','concat(\'PL_\', CAST(Platform.platformID AS CHAR)) in (\'PARM\')','0','0','SELECT concat(\'PL_\', CAST(platformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM Platform WHERE reportDropDownInd = 1 ORDER BY 3','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('6','ms','Publisher','concat(\'PB_\', CAST(pp.publisherPlatformID AS CHAR)) in (\'PARM\')','0','0','SELECT GROUP_CONCAT(DISTINCT concat(\'PB_\', CAST(publisherPlatformID AS CHAR)) ORDER BY publisherPlatformID DESC SEPARATOR \', \'), reportDisplayName, upper(reportDisplayName) FROM PublisherPlatform WHERE reportDropDownInd = 1 GROUP BY reportDisplayName ORDER BY 3','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('7','dd','Limit','limit','0','1','SELECT 25,25 union SELECT 50,50 union SELECT 100,100 order by 1','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('8','dd','Year','mus.year = \'PARM\'','0','0','SELECT distinct year, year FROM YearlyUsageSummary ORDER BY 1 asc','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('9','dd','Year','mus.year = \'PARM\'','0','0','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('10','dd','Year','mus.year = \'PARM\'','0','0','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('11','dd','Year','yus.year = \'PARM\'','0','0','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('12','dd','Date Range','','0','1','SELECT distinct year, year FROM YearlyUsageSummary ORDER BY 1 asc','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('13','dd','Date Range','','0','1','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('14','dd','Date Range','','0','1','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 asc','');
INSERT INTO ReportParameter (reportParameterID, parameterTypeCode, parameterDisplayPrompt, parameterAddWhereClause, parameterAddWhereNumber, requiredInd, parameterSQLStatement, parameterSQLRestriction)  VALUES ('15','dd','Resource Type','t.resourceType= \'PARM\'','0','0','SELECT distinct resourceType, resourceType FROM Title ORDER BY 1 asc','');

DELETE FROM ReportParameterMap;
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('1','1','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('1','2','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('1','3','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('1','12','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('1','15','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('2','1','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('2','4','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('2','13','4');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('2','15','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('3','5','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('3','14','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('4','6','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('4','14','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('5','1','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('5','7','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('5','4','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('5','11','4');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('5','15','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('6','1','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('6','4','0');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('6','11','4');
INSERT INTO ReportParameterMap (reportID, reportParameterID, parentReportParameterID)  VALUES ('6','15','4');
