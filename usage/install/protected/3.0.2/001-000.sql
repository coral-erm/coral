ALTER TABLE TitleIdentifier MODIFY identifier VARCHAR(255);
ALTER TABLE ImportLog MODIFY fileName VARCHAR(255);

INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("PR_R5", "Platform Master Report (PR) R5", "Platform");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("PR_P1_R5", "Platform Usage (PR_P1) R5", "Platform");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("DR_R5", "Database Master Report (DR) R5", "Database");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("DR_D1_R5", "Database Search and Item Usage (DR_D1) R5", "Database");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("DR_D2_R5", "Database Access Denied (DR_D2) R5", "Database");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("TR_R5", "Title Master Report (TR) R5", "Title");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("TR_B1_R5", "Book Requests (Excluding OA_Gold) (TR_B1) R5", "Book");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("TR_B2_R5", "Book Access Denied (TR_B2) R5", "Book");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("TR_B3_R5", "Book Usage by Access Type (TR_B3) R5", "Book");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("TR_J1_R5", "Journal Requests (Excluding OA_Gold) (TR_J1) R5", "Journal");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("TR_J2_R5", "Journal Access Denied (TR_J2) R5", "Journal");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("TR_J3_R5", "Journal Usage by Access Type (TR_J3) R5", "Journal");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("TR_J4_R5", "Journal Requests by YOP (Excluding OA_Gold) (TR_J4) R5", "Journal");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("IR_R5", "Item Master Report (IR) R5", "Item");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("IR_A1_R5", "Journal Article Requests (IR_A1) R5", "Item");
INSERT INTO Layout (layoutCode, name, resourceType) VALUES ("IR_M1_R5", "Multimedia Item Requests (IR_M1) R5", "Item");

DELETE FROM Layout WHERE layoutCode IN ('JR1_R3','JR1a_R3','BR1_R3','DB1_R3');

ALTER TABLE MonthlyUsageSummary ADD COLUMN accessType VARCHAR(255) NULL;
ALTER TABLE MonthlyUsageSummary ADD COLUMN accessMethod VARCHAR(255) NULL;
ALTER TABLE MonthlyUsageSummary ADD COLUMN yop INT(11) NULL;
ALTER TABLE MonthlyUsageSummary ADD COLUMN layoutID INT(11) NULL;

ALTER TABLE YearlyUsageSummary ADD COLUMN accessType VARCHAR(255) NULL;
ALTER TABLE YearlyUsageSummary ADD COLUMN accessMethod VARCHAR(255) NULL;
ALTER TABLE YearlyUsageSummary ADD COLUMN monthIncomplete INT(1) DEFAULT 0;
ALTER TABLE YearlyUsageSummary ADD COLUMN yop INT(11) NULL;
ALTER TABLE YearlyUsageSummary ADD COLUMN layoutID INT(11) NULL;

ALTER TABLE Title ADD COLUMN publicationDate DATETIME NULL;
ALTER TABLE Title ADD COLUMN articleVersion VARCHAR(255) NULL;
ALTER TABLE Title ADD COLUMN authors VARCHAR(255) NULL;
ALTER TABLE Title ADD COLUMN parentID INT(11) NULL;
ALTER TABLE Title ADD COLUMN componentID INT(11) NULL;

ALTER TABLE Publisher ADD COLUMN counterPublisherID VARCHAR(255) NULL;

ALTER TABLE SushiService ADD COLUMN apiKey VARCHAR(255) NULL;
