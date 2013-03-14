UPDATE `ac_extensions` SET `key`='handling' where `key`='handling_fee';

ALTER TABLE `ac_url_aliases` ADD COLUMN `language_id` INT NOT NULL DEFAULT '1';
ALTER TABLE `ac_url_aliases` MODIFY COLUMN `keyword` varchar(255) NOT NULL COMMENT 'translatable';