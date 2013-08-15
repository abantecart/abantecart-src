ALTER TABLE `ac_global_attributes` ADD COLUMN `regexp_pattern` varchar(255);
ALTER TABLE `ac_product_options` ADD COLUMN `regexp_pattern` varchar(255);

ALTER TABLE `ac_global_attributes_descriptions` ADD COLUMN `error_text` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_product_option_descriptions` ADD COLUMN `error_text` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';


DROP TABLE IF EXISTS `ac_global_attributes_type_descriptions`;
CREATE TABLE `ac_global_attributes_type_descriptions` (
          `attribute_type_id` int(11) NOT NULL,
          `language_id` int(11) NOT NULL,
          `type_name` varchar(64) COLLATE utf8_bin NOT NULL COMMENT 'translatable',
          `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          `create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
PRIMARY KEY (`attribute_type_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='utf8_bin';

INSERT INTO `ac_global_attributes_type_descriptions` (`attribute_type_id`, `language_id`, `type_name`, `update_date`, `create_date`)
SELECT `attribute_type_id`, 1 as language_id, `type_name`, NOW() as update_date, NOW() as create_date
FROM `ac_global_attributes_types`;

ALTER TABLE `ac_global_attributes_types` DROP COLUMN `type_name`;

UPDATE `ac_global_attributes_types` SET `controller` = 'responses/catalog/attribute/getProductOptionSubform' WHERE `type_key`='product_option';