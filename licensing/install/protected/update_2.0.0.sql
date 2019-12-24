ALTER TABLE `Expression` MODIFY `lastUpdateDate` timestamp NOT NULL default CURRENT_TIMESTAMP;

ALTER TABLE `ExpressionNote` MODIFY `lastUpdateDate` timestamp NOT NULL default CURRENT_TIMESTAMP;
