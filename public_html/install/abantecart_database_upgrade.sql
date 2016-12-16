ALTER TABLE `ac_global_attributes_descriptions`
ADD COLUMN `placeholder` varchar(255) COLLATE utf8_general_ci DEFAULT '' COMMENT 'translatable';

CREATE FULLTEXT INDEX `ac_customers_name_idx` ON `ac_customers` (`firstname`, `lastname`);
ALTER TABLE `ac_customers` ADD COLUMN `last_login` TIMESTAMP NULL;

UPDATE `ac_dataset_values`
SET `value_varchar` = 'extension'
WHERE `dataset_column_id` = 15 AND `row_id` = 222;

