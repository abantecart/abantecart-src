ALTER TABLE `ac_global_attributes` ADD COLUMN `regexp_pattern` varchar(255);
ALTER TABLE `ac_product_options` ADD COLUMN `regexp_pattern` varchar(255);

ALTER TABLE `ac_global_attributes_descriptions` ADD COLUMN `error_text` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_product_option_descriptions` ADD COLUMN `error_text` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';