DROP PROCEDURE IF EXISTS add_missing_taxfield;

CREATE DEFINER=CURRENT_USER PROCEDURE add_missing_taxfield(missing_taxfield TEXT charset utf8)
BEGIN
DECLARE colName TEXT;
SELECT column_name INTO colName
FROM information_schema.columns
WHERE table_name='ResourcePayment'
  AND column_name=missing_taxfield;

IF colName is null THEN
  SET @stmt=CONCAT('ALTER TABLE `ResourcePayment` ADD `',missing_taxfield,'` int(10) unsigned default NULL');
  PREPARE stmt FROM @stmt;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
END IF;
END;

CALL add_missing_taxfield('priceTaxExcluded');
CALL add_missing_taxfield('taxRate');
CALL add_missing_taxfield('priceTaxIncluded');
DROP PROCEDURE add_missing_taxfield;
