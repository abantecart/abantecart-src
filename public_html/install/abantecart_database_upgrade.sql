ALTER TABLE `ac_global_attributes_descriptions`
ADD COLUMN `placeholder` varchar(255) COLLATE utf8_general_ci DEFAULT '' COMMENT 'translatable';

CREATE FULLTEXT INDEX `ac_customers_name_idx` ON `ac_customers` (`firstname`, `lastname`);