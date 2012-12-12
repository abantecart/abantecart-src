ALTER TABLE `ac_tax_rates` ADD COLUMN `rate_prefix` char(1) COLLATE utf8_bin NOT NULL; -- % or $ 
ALTER TABLE `ac_tax_rates` ADD COLUMN `threshold_condition` char(2) COLLATE utf8_bin NOT NULL; -- '<=', '>=', '==' or '<'
ALTER TABLE `ac_tax_rates` ADD COLUMN `threshold` decimal(15,4) NOT NULL DEFAULT '0.0000';
