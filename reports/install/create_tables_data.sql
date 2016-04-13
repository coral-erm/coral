CREATE TABLE IF NOT EXISTS  `Report` (
  `reportID` int(11) NOT NULL auto_increment,
  `reportName` varchar(45) NOT NULL,
  `reportSQL` text NOT NULL,
  `reportGroupingColumnName` varchar(45) default NULL,
  `defaultRecPageNumber` int(11) default '100',
  `groupTotalInd` tinyint(1) default NULL,
  `specialPageURL` varchar(45) default NULL,
  `orderBySQL` text,
  `infoDisplayText` varchar(2000) default NULL,
  `excelOnlyInd` tinyint(1) default NULL,
  `reportDatabaseName` varchar(45) NOT NULL,
  PRIMARY KEY  (`reportID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `ReportGroupingColumn` (
  `reportID` int(11) NOT NULL,
  `reportGroupingColumnName` varchar(45) NOT NULL,
  `reportGroupingColumnID` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  USING BTREE (`reportGroupingColumnID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `ReportParameter` (
  `reportParameterID` int(11) NOT NULL auto_increment,
  `reportID` int(11) default NULL,
  `parameterDisplayPrompt` varchar(45) default NULL,
  `parameterAddWhereClause` varchar(500) default NULL,
  `parameterTypeCode` varchar(45) default NULL,
  `parameterFormatCode` varchar(45) default NULL,
  `requiredInd` tinyint(1) default NULL,
  `parameterAddWhereNumber` int(11) default NULL,
  `parameterSQLStatement` text,
  `parentReportParameterID` int(11) default NULL,
  `parameterSQLRestriction` text,
  PRIMARY KEY  (`reportParameterID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `ReportSum` (
  `reportID` int(11) NOT NULL,
  `reportSumID` int(10) unsigned NOT NULL auto_increment,
  `reportColumnName` varchar(45) default NULL,
  `reportGroupingColumnName` varchar(45) default NULL,
  `reportAction` varchar(45) default NULL,
  PRIMARY KEY  (`reportSumID`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=latin1;


DELETE FROM Report;
INSERT INTO Report (reportID, reportName, reportSQL, reportGroupingColumnName, defaultRecPageNumber, groupTotalInd, specialPageURL, orderBySQL, infoDisplayText, excelOnlyInd, reportDatabaseName)  
VALUES 
('1','Usage Statistics by Titles','SELECT t.Title TITLE, pp.reportDisplayName PUBLISHER, Platform.reportDisplayName PLATFORM, mus.year YEAR,
MAX(IF(ti.identifierType=\'DOI\', ti.identifier, null)) DOI,
MAX(IF(ti.identifierType=\'ISSN\', concat(substr(ti.identifier,1,4), \'-\', substr(ti.identifier,5,4)),null)) PRINT_ISSN,
MAX(IF(ti.identifierType=\'eISSN\', concat(substr(ti.identifier,1,4), \'-\', substr(ti.identifier,5,4)),null)) ONLINE_ISSN,
MAX(IF(ti.identifierType=\'ISBN\', ti.identifier, null)) ISBN,
MAX(IF(month=1,usageCount,null)) JAN,
MAX(IF(month=2,usageCount,null)) FEB,
MAX(IF(month=3,usageCount,null)) MAR,
MAX(IF(month=4,usageCount,null)) APR,
MAX(IF(month=5,usageCount,null)) MAY,
MAX(IF(month=6,usageCount,null)) JUN,
MAX(IF(month=7,usageCount,null)) JUL,
MAX(IF(month=8,usageCount,null)) AUG,
MAX(IF(month=9,usageCount,null)) SEP,
MAX(IF(month=10,usageCount,null)) OCT,
MAX(IF(month=\'11\',usageCount,null)) NOV,
MAX(IF(month=\'12\',usageCount,null)) `DEC`,
totalCount YTD_TOTAL,
ytdHTMLCount YTD_HTML,
ytdPDFCount YTD_PDF,
IF(MAX(outlierID)=0,\'N\',\'Y\') outlier_flag,
t.titleID titleID,
MAX(IF(month=1,overrideUsageCount,null)) JAN_OVERRIDE,
MAX(IF(month=2,overrideUsageCount,null)) FEB_OVERRIDE,
MAX(IF(month=3,overrideUsageCount,null)) MAR_OVERRIDE,
MAX(IF(month=4,overrideUsageCount,null)) APR_OVERRIDE,
MAX(IF(month=5,overrideUsageCount,null)) MAY_OVERRIDE,
MAX(IF(month=6,overrideUsageCount,null)) JUN_OVERRIDE,
MAX(IF(month=7,overrideUsageCount,null)) JUL_OVERRIDE,
MAX(IF(month=8,overrideUsageCount,null)) AUG_OVERRIDE,
MAX(IF(month=9,overrideUsageCount,null)) SEP_OVERRIDE,
MAX(IF(month=10, overrideUsageCount,null)) OCT_OVERRIDE,
MAX(IF(month=11,overrideUsageCount,null)) NOV_OVERRIDE,
MAX(IF(month=12,overrideUsageCount,null)) DEC_OVERRIDE,
overrideTotalCount YTD_OVERRIDE,
overrideHTMLCount HTML_OVERRIDE,
overridePDFCount PDF_OVERRIDE,
MAX(IF(month=1,outlierID,null)) JAN_OUTLIER,
MAX(IF(month=2,outlierID,null)) FEB_OUTLIER,
MAX(IF(month=3,outlierID,null)) MAR_OUTLIER,
MAX(IF(month=4,outlierID,null)) APR_OUTLIER,
MAX(IF(month=5,outlierID,null)) MAY_OUTLIER,
MAX(IF(month=6,outlierID,null)) JUN_OUTLIER,
MAX(IF(month=7,outlierID,null)) JUL_OUTLIER,
MAX(IF(month=8,outlierID,null)) AUG_OUTLIER,
MAX(IF(month=9,outlierID,null)) SEP_OUTLIER,
MAX(IF(month=10,outlierID,null)) OCT_OUTLIER,
MAX(IF(month=11,outlierID,null)) NOV_OUTLIER,
MAX(IF(month=12,outlierID,null)) DEC_OUTLIER,
yus.mergeInd mergeInd, pp.publisherPlatformID, pp.platformID,
replace(replace(replace(t.Title,"A ",""),"An ",""),"The ","") TITLE_SORT
FROM Platform, PublisherPlatform pp,
MonthlyUsageSummary mus LEFT JOIN YearlyUsageSummary yus ON yus.publisherPlatformID = mus.publisherPlatformID AND yus.year = mus.year AND yus.titleID = mus.titleID AND yus.archiveInd = mus.archiveInd, 
Title t LEFT JOIN TitleIdentifier ti ON t.titleID = ti.titleID
WHERE Platform.platformID = pp.platformID
AND t.titleID in (SELECT t2.titleID FROM Title t2 LEFT JOIN TitleIdentifier ti2 ON ti2.titleID = t2.titleID WHERE t2.titleID != \'\' ADD_WHERE2) 
ADD_WHERE
AND mus.publisherPlatformID = pp.publisherPlatformID
AND mus.titleID = t.titleID
GROUP BY t.titleID, t.Title, pp.reportDisplayName, Platform.reportDisplayName, mus.year, overrideTotalCount, totalCount, overrideHTMLCount, ytdHTMLCount, overridePDFCount, ytdPDFCount, yus.mergeInd, pp.publisherPlatformID, pp.platformID','','100','0','',' order by TITLE_SORT,2,3,4','<h3>Frequently Asked Questions</h3><b>Q. Why isn\'t the HTML number double the PDF number for interfaces that automatically download HTML?</b><br />A. Frequently these sites do NOT automatically download HTML from the Table of Contents browse interface, so even platforms such as ScienceDirect occasionally have higher PDF than HTML counts.<br /><br /><b>Q. I thought COUNTER standards prevented double-counting of article downloads.</b><br />A. COUNTER does require that duplicate clicks on HTML or PDF within a short period of time be counted once. But COUNTER specifically does not deny double count of different formats--HTML and PDF. Because some publishers automatically choose HTML for users, and because many users prefer to save and/or print the PDF version, this interface significantly inflates total article usage.<br /><br /><b>Q. Why do some Highwire Press publishers have high HTML ratios to PDFs, but some appear to have a very low ratio?</b><br />A. Some publishers have automatic HTML display on Highwire, and some do not. This is because the publisher is able to indicate a preferred linking page through the DOI registry. Because this platform includes multiple publishers, the interface impact is not consistent.','0', 'usageDatabase');



INSERT INTO Report (reportID, reportName, reportSQL, reportGroupingColumnName, defaultRecPageNumber, groupTotalInd, specialPageURL, orderBySQL, infoDisplayText, excelOnlyInd, reportDatabaseName)  
VALUES ('2','Usage Statistics by Provider / Publisher',
'SELECT t.Title TITLE, pp.reportDisplayName PUBLISHER, Platform.reportDisplayName PLATFORM, mus.year YEAR,
MAX(IF(ti.identifierType=\'DOI\', ti.identifier, null)) DOI,
MAX(IF(ti.identifierType=\'ISSN\', concat(substr(ti.identifier,1,4), \'-\', substr(ti.identifier,5,4)),null)) PRINT_ISSN,
MAX(IF(ti.identifierType=\'eISSN\', concat(substr(ti.identifier,1,4), \'-\', substr(ti.identifier,5,4)),null)) ONLINE_ISSN,
MAX(IF(ti.identifierType=\'ISBN\', ti.identifier, null)) ISBN,
MAX(IF(month=1,usageCount,null)) JAN,
MAX(IF(month=2,usageCount,null)) FEB,
MAX(IF(month=3,usageCount,null)) MAR,
MAX(IF(month=4,usageCount,null)) APR,
MAX(IF(month=5,usageCount,null)) MAY,
MAX(IF(month=6,usageCount,null)) JUN,
MAX(IF(month=7,usageCount,null)) JUL,
MAX(IF(month=8,usageCount,null)) AUG,
MAX(IF(month=9,usageCount,null)) SEP,
MAX(IF(month=10,usageCount,null)) OCT,
MAX(IF(month=11,usageCount,null)) NOV,
MAX(IF(month=12,usageCount,null)) `DEC`,
totalCount YTD_TOTAL,
ytdHTMLCount YTD_HTML,
ytdPDFCount YTD_PDF,
IF(MAX(outlierID)=0,\'N\',\'Y\') OUTLIER_FLAG,
t.titleID titleID,
MAX(IF(month=1,overrideUsageCount,null)) JAN_OVERRIDE,
MAX(IF(month=2,overrideUsageCount,null)) FEB_OVERRIDE,
MAX(IF(month=3,overrideUsageCount,null)) MAR_OVERRIDE,
MAX(IF(month=4,overrideUsageCount,null)) APR_OVERRIDE,
MAX(IF(month=5,overrideUsageCount,null)) MAY_OVERRIDE,
MAX(IF(month=6,overrideUsageCount,null)) JUN_OVERRIDE,
MAX(IF(month=7,overrideUsageCount,null)) JUL_OVERRIDE,
MAX(IF(month=8,overrideUsageCount,null)) AUG_OVERRIDE,
MAX(IF(month=9,overrideUsageCount,null)) SEP_OVERRIDE,
MAX(IF(month=10, overrideUsageCount,null)) OCT_OVERRIDE,
MAX(IF(month=11,overrideUsageCount,null)) NOV_OVERRIDE,
MAX(IF(month=12,overrideUsageCount,null)) DEC_OVERRIDE,
overrideTotalCount YTD_OVERRIDE,
overrideHTMLCount HTML_OVERRIDE,
overridePDFCount PDF_OVERRIDE,
MAX(IF(month=1,outlierID,null)) JAN_OUTLIER,
MAX(IF(month=2,outlierID,null)) FEB_OUTLIER,
MAX(IF(month=3,outlierID,null)) MAR_OUTLIER,
MAX(IF(month=4,outlierID,null)) APR_OUTLIER,
MAX(IF(month=5,outlierID,null)) MAY_OUTLIER,
MAX(IF(month=6,outlierID,null)) JUN_OUTLIER,
MAX(IF(month=7,outlierID,null)) JUL_OUTLIER,
MAX(IF(month=8,outlierID,null)) AUG_OUTLIER,
MAX(IF(month=9,outlierID,null)) SEP_OUTLIER,
MAX(IF(month=10,outlierID,null)) OCT_OUTLIER,
MAX(IF(month=11,outlierID,null)) NOV_OUTLIER,
MAX(IF(month=12,outlierID,null)) DEC_OUTLIER,
yus.mergeInd mergeInd, pp.publisherPlatformID, pp.platformID,
replace(replace(replace(t.Title,"A ",""),"An ",""),"The ","") TITLE_SORT
FROM Platform, PublisherPlatform pp,
MonthlyUsageSummary mus LEFT JOIN YearlyUsageSummary yus ON yus.publisherPlatformID = mus.publisherPlatformID AND yus.year = mus.year AND yus.titleID = mus.titleID AND yus.archiveInd = mus.archiveInd, 
Title t LEFT JOIN TitleIdentifier ti ON t.titleID = ti.titleID
WHERE Platform.platformID = pp.platformID
ADD_WHERE
AND mus.publisherPlatformID = pp.publisherPlatformID
AND mus.titleID = t.titleID
GROUP BY t.titleID, t.Title, pp.reportDisplayName, Platform.reportDisplayName, mus.year, overrideTotalCount, totalCount, overrideHTMLCount, ytdHTMLCount, overridePDFCount, ytdPDFCount, yus.mergeInd, pp.publisherPlatformID, pp.platformID','','100','1','',' order by TITLE_SORT,2,3,4','<h3>Frequently Asked Questions</h3><b>Q. Why isn\'t the HTML number double the PDF number for interfaces that automatically download HTML?</b><br />A. Frequently these sites do NOT automatically download HTML from the Table of Contents browse interface, so even platforms such as ScienceDirect occasionally have higher PDF than HTML counts.<br /><br /><b>Q. I thought COUNTER standards prevented double-counting of article downloads.</b><br />A. COUNTER does require that duplicate clicks on HTML or PDF within a short period of time be counted once. But COUNTER specifically does not deny double count of different formats--HTML and PDF. Because some publishers automatically choose HTML for users, and because many users prefer to save and/or print the PDF version, this interface significantly inflates total article usage.<br /><br /><b>Q. Why do some Highwire Press publishers have high HTML ratios to PDFs, but some appear to have a very low ratio?</b><br />A. Some publishers have automatic HTML display on Highwire, and some do not. This is because the publisher is able to indicate a preferred linking page through the DOI registry. Because this platform includes multiple publishers, the interface impact is not consistent.','0', 'usageDatabase');



INSERT INTO Report (reportID, reportName, reportSQL, reportGroupingColumnName, defaultRecPageNumber, groupTotalInd, specialPageURL, orderBySQL, infoDisplayText, excelOnlyInd, reportDatabaseName)  VALUES 
('3','Usage Statistics - Provider Rollup','SELECT Platform.reportDisplayName PLATFORM,
number_of_titles,
mus.year,
sum(IF(month=1,IFNULL(overrideUsageCount,usageCount),null)) JAN,
sum(IF(month=2,IFNULL(overrideUsageCount,usageCount),null)) FEB,
sum(IF(month=3,IFNULL(overrideUsageCount,usageCount),null)) MAR,
sum(IF(month=4,IFNULL(overrideUsageCount,usageCount),null)) APR,
sum(IF(month=5,IFNULL(overrideUsageCount,usageCount),null)) MAY,
sum(IF(month=6,IFNULL(overrideUsageCount,usageCount),null)) JUN,
sum(IF(month=7,IFNULL(overrideUsageCount,usageCount),null)) JUL,
sum(IF(month=8,IFNULL(overrideUsageCount,usageCount),null)) AUG,
sum(IF(month=9,IFNULL(overrideUsageCount,usageCount),null)) SEP,
sum(IF(month=10,IFNULL(overrideUsageCount,usageCount),null)) OCT,
sum(IF(month=11,IFNULL(overrideUsageCount,usageCount),null)) NOV,
sum(IF(month=12,IFNULL(overrideUsageCount,usageCount),null)) `DEC`,
total_count YTD_TOTAL,
html_count YTD_HTML,
pdf_count YTD_PDF,
Platform.platformID
FROM Platform, Publisher, PublisherPlatform pp, MonthlyUsageSummary mus,
        (SELECT pp.platformID platformID, count(distinct yus.titleID) number_of_titles, yus.year year,
        sum(totalCount) total_count, sum(ytdHTMLCount) html_count, sum(ytdPDFCount) pdf_count
        FROM Platform, Publisher, PublisherPlatform pp, YearlyUsageSummary yus
        WHERE pp.publisherPlatformID = yus.publisherPlatformID AND pp.platformID = Platform.platformID AND pp.publisherID = Publisher.publisherID
        GROUP BY pp.platformID, yus.year) ytd
WHERE ytd.year = mus.year
AND pp.platformID = ytd.platformID
AND pp.publisherPlatformID = mus.publisherPlatformID
AND pp.platformID = Platform.platformID
AND pp.publisherID = Publisher.publisherID
ADD_WHERE
GROUP BY Platform.reportDisplayName, mus.year, number_of_titles, total_count, html_count, pdf_count, Platform.platformID','','100','1','','order by 1, 3','<h3>Frequently Asked Questions</h3><b>Q. Why isn\'t the HTML number double the PDF number for interfaces that automatically download HTML?</b><br />A. Frequently these sites do NOT automatically download HTML from the Table of Contents browse interface, so even platforms such as ScienceDirect occasionally have higher PDF than HTML counts.<br /><br /><b>Q. I thought COUNTER standards prevented double-counting of article downloads.</b><br />A. COUNTER does require that duplicate clicks on HTML or PDF within a short period of time be counted once. But COUNTER specifically does not deny double count of different formats--HTML and PDF. Because some publishers automatically choose HTML for users, and because many users prefer to save and/or print the PDF version, this interface significantly inflates total article usage.<br /><br /><b>Q. Why do some Highwire Press publishers have high HTML ratios to PDFs, but some appear to have a very low ratio?</b><br />A. Some publishers have automatic HTML display on Highwire, and some do not. This is because the publisher is able to indicate a preferred linking page through the DOI registry. Because this platform includes multiple publishers, the interface impact is not consistent.','0', 'usageDatabase');


INSERT INTO Report (reportID, reportName, reportSQL, reportGroupingColumnName, defaultRecPageNumber, groupTotalInd, specialPageURL, orderBySQL, infoDisplayText, excelOnlyInd, reportDatabaseName)  VALUES ('4','Usage Statistics - Publisher Rollup','SELECT pp.reportDisplayName Publisher,
Platform.reportDisplayName Platform,
number_of_titles,
mus.year,
sum(IF(month=1,IFNULL(overrideUsageCount,usageCount),null)) JAN,
sum(IF(month=2,IFNULL(overrideUsageCount,usageCount),null)) FEB,
sum(IF(month=3,IFNULL(overrideUsageCount,usageCount),null)) MAR,
sum(IF(month=4,IFNULL(overrideUsageCount,usageCount),null)) APR,
sum(IF(month=5,IFNULL(overrideUsageCount,usageCount),null)) MAY,
sum(IF(month=6,IFNULL(overrideUsageCount,usageCount),null)) JUN,
sum(IF(month=7,IFNULL(overrideUsageCount,usageCount),null)) JUL,
sum(IF(month=8,IFNULL(overrideUsageCount,usageCount),null)) AUG,
sum(IF(month=9,IFNULL(overrideUsageCount,usageCount),null)) SEP,
sum(IF(month=10,IFNULL(overrideUsageCount,usageCount),null)) OCT,
sum(IF(month=11,IFNULL(overrideUsageCount,usageCount),null)) NOV,
sum(IF(month=12,IFNULL(overrideUsageCount,usageCount),null)) `DEC`,
total_count YTD_TOTAL,
html_count YTD_HTML,
pdf_count YTD_PDF,
Platform.platformID platformID,
pp.publisherPlatformID
FROM Platform, Publisher, PublisherPlatform pp, MonthlyUsageSummary mus,
        (SELECT pp.publisherPlatformID publisherPlatformID, count(distinct yus.titleID) number_of_titles, yus.year year,
        sum(totalCount) total_count, sum(ytdHTMLCount) html_count, sum(ytdPDFCount) pdf_count
        FROM Platform, Publisher, PublisherPlatform pp, YearlyUsageSummary yus
        WHERE pp.publisherPlatformID = yus.publisherPlatformID AND pp.platformID = Platform.platformID AND pp.publisherID = Publisher.publisherID
        GROUP BY pp.publisherPlatformID, yus.year) ytd
WHERE ytd.year = mus.year
AND pp.publisherPlatformID = ytd.publisherPlatformID
AND pp.publisherPlatformID = mus.publisherPlatformID
AND pp.platformID = Platform.platformID
AND pp.publisherID = Publisher.publisherID
ADD_WHERE
GROUP BY pp.reportDisplayName, Platform.reportDisplayName, mus.year, number_of_titles, total_count, html_count, pdf_count, pp.publisherPlatformID, Platform.platformID','','100','1','','order by 1','<h3>Frequently Asked Questions</h3><b>Q. Why isn\'t the HTML number double the PDF number for interfaces that automatically download HTML?</b><br />A. Frequently these sites do NOT automatically download HTML from the Table of Contents browse interface, so even platforms such as ScienceDirect occasionally have higher PDF than HTML counts.<br /><br /><b>Q. I thought COUNTER standards prevented double-counting of article downloads.</b><br />A. COUNTER does require that duplicate clicks on HTML or PDF within a short period of time be counted once. But COUNTER specifically does not deny double count of different formats--HTML and PDF. Because some publishers automatically choose HTML for users, and because many users prefer to save and/or print the PDF version, this interface significantly inflates total article usage.<br /><br /><b>Q. Why do some Highwire Press publishers have high HTML ratios to PDFs, but some appear to have a very low ratio?</b><br />A. Some publishers have automatic HTML display on Highwire, and some do not. This is because the publisher is able to indicate a preferred linking page through the DOI registry. Because this platform includes multiple publishers, the interface impact is not consistent.','0', 'usageDatabase');



INSERT INTO Report (reportID, reportName, reportSQL, reportGroupingColumnName, defaultRecPageNumber, groupTotalInd, specialPageURL, orderBySQL, infoDisplayText, excelOnlyInd, reportDatabaseName)  VALUES ('5','Usage Statistics - Top Journal Requests','SELECT t.Title TITLE,
max(pp.reportDisplayName) PUBLISHER,
GROUP_CONCAT(distinct Platform.reportDisplayName ORDER BY Platform.reportDisplayName DESC SEPARATOR \', \') PLATFORM,
yus.year YEAR,
MAX(IF(ti.identifierType=\'DOI\', ti.identifier, null)) DOI,
MAX(IF(ti.identifierType=\'ISSN\', concat(substr(ti.identifier,1,4), \'-\', substr(ti.identifier,5,4)),null)) PRINT_ISSN,
MAX(IF(ti.identifierType=\'eISSN\', concat(substr(ti.identifier,1,4), \'-\', substr(ti.identifier,5,4)),null)) ONLINE_ISSN,
MAX(IF(ti.identifierType=\'ISBN\', ti.identifier, null)) ISBN,
sum(distinct totalCount) YTD_TOTAL,
sum(distinct ytdHTMLCount) YTD_HTML,
sum(distinct ytdPDFCount) YTD_PDF,
t.titleID titleID,
overrideTotalCount YTD_OVERRIDE,
overrideHTMLCount HTML_OVERRIDE,
overridePDFCount PDF_OVERRIDE,
replace(replace(replace(t.Title,"A ",""),"An ",""),"The ","") TITLE_SORT
FROM Title t, TitleIdentifier ti, Platform, Publisher, PublisherPlatform pp, YearlyUsageSummary yus
WHERE t.titleID = yus.titleID
AND pp.publisherPlatformID = yus.publisherPlatformID
AND pp.platformID = Platform.platformID
AND pp.publisherID = Publisher.publisherID
AND ti.titleID=t.titleID
ADD_WHERE
GROUP BY t.Title, yus.year, t.titleID','','100','1','','order by sum(distinct totalCount) desc, TITLE_SORT','<h3>Frequently Asked Questions</h3><b>Q. Why isn\'t the HTML number double the PDF number for interfaces that automatically download HTML?</b><br />A. Frequently these sites do NOT automatically download HTML from the Table of Contents browse interface, so even platforms such as ScienceDirect occasionally have higher PDF than HTML counts.<br /><br /><b>Q. I thought COUNTER standards prevented double-counting of article downloads.</b><br />A. COUNTER does require that duplicate clicks on HTML or PDF within a short period of time be counted once. But COUNTER specifically does not deny double count of different formats--HTML and PDF. Because some publishers automatically choose HTML for users, and because many users prefer to save and/or print the PDF version, this interface significantly inflates total article usage.<br /><br /><b>Q. Why do some Highwire Press publishers have high HTML ratios to PDFs, but some appear to have a very low ratio?</b><br />A. Some publishers have automatic HTML display on Highwire, and some do not. This is because the publisher is able to indicate a preferred linking page through the DOI registry. Because this platform includes multiple publishers, the interface impact is not consistent.','0', 'usageDatabase');


INSERT INTO Report (reportID, reportName, reportSQL, reportGroupingColumnName, defaultRecPageNumber, groupTotalInd, specialPageURL, orderBySQL, infoDisplayText, excelOnlyInd, reportDatabaseName)  VALUES ('6','Usage Statistics - Yearly Usage Statistics','SELECT t.Title TITLE,
PRINT_ISSN,
ONLINE_ISSN,
pp.reportDisplayName PUBLISHER,
Platform.reportDisplayName PLATFORM,
max(IF(year=2013, totalCount, null)) "2013_ytd",
max(IF(year=2012, totalCount, null)) "2012_ytd",
max(IF(year=2011, totalCount, null)) "2011_ytd",
max(IF(year=2010, totalCount, null)) "2010_ytd",
max(IF(year=2009, totalCount, null)) "2009_ytd",
max(IF(year=2008, totalCount, null)) "2008_ytd",
sum(totalCount) "all_years",
t.titleID, pp.platformID,
replace(replace(replace(t.Title,"A ",""),"An ",""),"The ","") TITLE_SORT
FROM Title t, (SELECT titleID, 
  MAX(IF(identifierType=\'ISSN\', concat(substr(identifier,1,4), \'-\', substr(identifier,5,4)),null)) PRINT_ISSN, 
  MAX(IF(identifierType=\'online\', concat(substr(identifier,1,4), \'-\', substr(identifier,5,4)),null)) ONLINE_ISSN 
  FROM TitleIdentifier GROUP BY titleID) ti, 
Platform, Publisher, PublisherPlatform pp, YearlyUsageSummary yus
WHERE t.titleID = yus.titleID
AND pp.publisherPlatformID = yus.publisherPlatformID
AND pp.platformID = Platform.platformID
AND pp.publisherID = Publisher.publisherID
AND ti.titleID=t.titleID
ADD_WHERE
GROUP BY t.Title, print_issn, online_issn, pp.reportDisplayName, Platform.reportDisplayName, t.titleID, pp.platformID','','100','1','','order by TITLE_SORT','<h3>Frequently Asked Questions</h3><b>Q. Why isn\'t the HTML number double the PDF number for interfaces that automatically download HTML?</b><br />A. Frequently these sites do NOT automatically download HTML from the Table of Contents browse interface, so even platforms such as ScienceDirect occasionally have higher PDF than HTML counts.<br /><br /><b>Q. I thought COUNTER standards prevented double-counting of article downloads.</b><br />A. COUNTER does require that duplicate clicks on HTML or PDF within a short period of time be counted once. But COUNTER specifically does not deny double count of different formats--HTML and PDF. Because some publishers automatically choose HTML for users, and because many users prefer to save and/or print the PDF version, this interface significantly inflates total article usage.<br /><br /><b>Q. Why do some Highwire Press publishers have high HTML ratios to PDFs, but some appear to have a very low ratio?</b><br />A. Some publishers have automatic HTML display on Highwire, and some do not. This is because the publisher is able to indicate a preferred linking page through the DOI registry. Because this platform includes multiple publishers, the interface impact is not consistent.','0', 'usageDatabase');



DELETE FROM ReportSum;
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','APR','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','AUG','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','DEC','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','FEB','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','JAN','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','JUL','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','JUN','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','MAR','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','MAY','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','NOV','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','OCT','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','SEP','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','YTD_HTML','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','YTD_PDF','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('1','YTD_TOTAL','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','APR','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','AUG','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','DEC','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','FEB','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','JAN','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','JUL','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','JUN','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','MAR','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','MAY','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','NOV','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','OCT','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','SEP','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','YTD_HTML','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','YTD_PDF','', 'sum');
INSERT INTO ReportSum (reportID, reportColumnName, reportGroupingColumnName, reportAction)  VALUES ('2','YTD_TOTAL','', 'sum');



DELETE FROM ReportParameter;
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('1','Do not adjust numbers for use violations','Overriden','chk','','0','0','','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('1','ISSN/ISBN/DOI','(ti2.identifier = \'PARM\' OR ti2.identifier = REPLACE(\'PARM\',"-",""))','txt','','0','2','','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('1','Title Search','upper(t2.title) like upper(\'%PARM%\')','txt','','0','2','','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('1','Year','mus.year = \'PARM\'','dd','','0','0','SELECT distinct year, year from YearlyUsageSummary ORDER BY 1 desc','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('2','Do not adjust numbers for use violations','Overriden','chk','','0','0','','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('2','Provider / Publisher','(concat(\'PL_\', CAST(Platform.platformID AS CHAR)) = \'PARM\' OR concat(\'PB_\', CAST(pp.publisherPlatformID AS CHAR)) = \'PARM\')','dd','','0','0','SELECT concat(\'PL_\', CAST(Platform.platformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM Platform WHERE reportDropDownInd = 1 UNION SELECT concat(\'PB_\', CAST(publisherPlatformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM PublisherPlatform WHERE reportDropDownInd = 1 ORDER BY 3','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('2','Year','mus.year = \'PARM\'','dd','','0','0','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 desc','5','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('3','Provider','concat(\'PL_\', CAST(Platform.platformID AS CHAR)) in (\'PARM\')','ms','','0','0','SELECT concat(\'PL_\', CAST(platformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM Platform WHERE reportDropDownInd = 1 ORDER BY 3','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('3','Year','mus.year = \'PARM\'','dd','','0','0','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 desc','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('4','Publisher','concat(\'PB_\', CAST(pp.publisherPlatformID AS CHAR)) in (\'PARM\')','ms','','0','0','SELECT GROUP_CONCAT(DISTINCT concat(\'PB_\', CAST(publisherPlatformID AS CHAR)) ORDER BY publisherPlatformID DESC SEPARATOR \', \'), reportDisplayName, upper(reportDisplayName) FROM PublisherPlatform WHERE reportDropDownInd = 1 GROUP BY reportDisplayName ORDER BY 3','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('4','Year','mus.year = \'PARM\'','dd','','0','0','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 desc','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('5','Do not adjust numbers for use violations','Overriden','chk','','0','0','','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('5','Limit','limit','dd','','1','0','SELECT 25,25 union SELECT 50,50 union SELECT 100,100 order by 1','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('5','Provider / Publisher','(concat(\'PL_\', CAST(Platform.platformID AS CHAR)) = \'PARM\' OR concat(\'PB_\', CAST(pp.publisherPlatformID AS CHAR)) = \'PARM\')','dd','','0','0','SELECT concat(\'PL_\', CAST(Platform.platformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM Platform WHERE reportDropDownInd = 1 UNION SELECT concat(\'PB_\', CAST(publisherPlatformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM PublisherPlatform WHERE reportDropDownInd = 1 ORDER BY 3','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('5','Year','yus.year = \'PARM\'','dd','','0','0','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 desc','13','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('6','Do not adjust numbers for use violations','Overriden','chk','','0','0','','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('6','Provider / Publisher','(concat(\'PL_\', CAST(Platform.platformID AS CHAR)) = \'PARM\' OR concat(\'PB_\', CAST(pp.publisherPlatformID AS CHAR)) = \'PARM\')','dd','','0','0','SELECT concat(\'PL_\', CAST(Platform.platformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM Platform WHERE reportDropDownInd = 1 UNION SELECT concat(\'PB_\', CAST(publisherPlatformID AS CHAR)), reportDisplayName, upper(reportDisplayName) FROM PublisherPlatform WHERE reportDropDownInd = 1 ORDER BY 3','0','');
INSERT INTO ReportParameter (reportID, parameterDisplayPrompt, parameterAddWhereClause, parameterTypeCode, parameterFormatCode, requiredInd, parameterAddWhereNumber, parameterSQLStatement, parentReportParameterID, parameterSQLRestriction)  VALUES ('6','Year','yus.year = \'PARM\'','dd','','0','0','SELECT distinct year, year FROM YearlyUsageSummary yus, PublisherPlatform pp WHERE pp.publisherPlatformID=yus.publisherPlatformID ADD_WHERE ORDER BY 1 desc','17','and (concat(\'PB_\', CAST(yus.publisherPlatformID AS CHAR)) = \'PARM\' or concat(\'PL_\', CAST(pp.platformID AS CHAR)) = \'PARM\')');


DELETE FROM ReportGroupingColumn;
INSERT INTO ReportGroupingColumn (reportID, reportGroupingColumnID, reportGroupingColumnName) VALUES ('1', '1', 'TITLE');
INSERT INTO ReportGroupingColumn (reportID, reportGroupingColumnID, reportGroupingColumnName) VALUES ('2', '2', 'TITLE');
