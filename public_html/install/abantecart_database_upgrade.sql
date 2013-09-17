INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES
(7,'all_settings',191),
(7,'settings_details',192),
(7,'settings_general',193),
(7,'settings_checkout',194),
(7,'settings_appearance',195),
(7,'settings_mail',196),
(7,'settings_api',197),
(7,'settings_system',198),
(7,'settings_newstore',199);

INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES
(8,'text_all_settings',191),
(8,'text_settings_details',192),
(8,'text_settings_general',193),
(8,'text_settings_checkout',194),
(8,'text_settings_appearance',195),
(8,'text_settings_mail',196),
(8,'text_settings_api',197),
(8,'text_settings_system',198),
(8,'text_settings_newstore',199);

INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES
(9,'setting/setting/all',191),
(9,'setting/setting/details',192),
(9,'setting/setting/general',193),
(9,'setting/setting/checkout',194),
(9,'setting/setting/appearance',195),
(9,'setting/setting/mail',196),
(9,'setting/setting/api',197),
(9,'setting/setting/system',198),
(9,'setting/store/insert',199);

INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES
(10,'setting',191),
(10,'setting',192),
(10,'setting',193),
(10,'setting',194),
(10,'setting',195),
(10,'setting',196),
(10,'setting',197),
(10,'setting',198),
(10,'setting',199);

INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_integer`,`row_id`)
VALUES
(11,1,191),
(11,2,192),
(11,3,193),
(11,4,194),
(11,5,195),
(11,6,196),
(11,7,197),
(11,8,198),
(11,9,199);

INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES
(12,'core',191),
(12,'core',192),
(12,'core',193),
(12,'core',194),
(12,'core',195),
(12,'core',196),
(12,'core',197),
(12,'core',198),
(12,'core',199);


ALTER TABLE `ac_global_attributes` ADD COLUMN `regexp_pattern` varchar(255);
ALTER TABLE `ac_product_options` ADD COLUMN `regexp_pattern` varchar(255);

ALTER TABLE `ac_global_attributes_descriptions` ADD COLUMN `error_text` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_product_option_descriptions` ADD COLUMN `error_text` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';

CREATE TABLE IF NOT EXISTS `ac_global_attributes_type_descriptions` (
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

INSERT INTO `ac_customer_groups` (`name`) VALUES ('Newsletter Subscribers');