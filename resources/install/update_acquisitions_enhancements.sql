ALTER TABLE ResourcePayment ADD COLUMN `priceTaxExcluded` int(10) unsigned default NULL AFTER selectorLoginID;
ALTER TABLE ResourcePayment ADD COLUMN `taxRate` int(10) unsigned default NULL AFTER priceTaxExcluded;
ALTER TABLE ResourcePayment ADD COLUMN `priceTaxIncluded` int(10) unsigned default NULL AFTER taxRate;
