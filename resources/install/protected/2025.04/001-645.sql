# Change from INT(10) UNSIGNED to just INT(10) so that negative values can be used.
ALTER TABLE ResourcePayment CHANGE `paymentAmount` `paymentAmount` INT(10) NULL DEFAULT NULL;
ALTER TABLE ResourcePayment CHANGE `priceTaxExcluded` `priceTaxExcluded` INT(10) NULL DEFAULT NULL;
ALTER TABLE ResourcePayment CHANGE `priceTaxIncluded` `priceTaxIncluded` INT(10) NULL DEFAULT NULL;