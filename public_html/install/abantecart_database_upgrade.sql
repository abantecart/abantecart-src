UPDATE `ac_extensions` SET `key`='handling' where `key`='handling_fee';

ALTER TABLE `ac_global_attributes` ADD COLUMN `settings` text COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `required`;
ALTER TABLE `ac_fields` ADD COLUMN `settings` text COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `required`;


ALTER TABLE `ac_url_aliases` ADD COLUMN `language_id` INT NOT NULL DEFAULT '1';
ALTER TABLE `ac_url_aliases` MODIFY COLUMN `keyword` varchar(255) NOT NULL COMMENT 'translatable';

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('general', 'config_product_default_sort_order', 'sort_order-ASC');
