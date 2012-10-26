INSERT INTO `ac_settings` VALUES ('', 0, 'general','auto_translate_status', 1);
INSERT INTO `ac_settings` VALUES ('', 0, 'general','translate_src_lang_code','en');
INSERT INTO `ac_settings` VALUES ('', 0, 'general','translate_override_existing', 0);
INSERT INTO `ac_settings` VALUES ('', 0, 'general','warn_lang_text_missing', 0);

ALTER TABLE `ac_banner_descriptions` MODIFY COLUMN `name` varchar(255) NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_banner_descriptions` MODIFY COLUMN `description` text COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_banner_descriptions` MODIFY COLUMN `meta` text(1500) DEFAULT '' COMMENT 'translatable';

ALTER TABLE `ac_block_descriptions` MODIFY COLUMN `name` varchar(255) NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_block_descriptions` MODIFY COLUMN `title` varchar(255) NOT NULL COMMENT 'translatable'; 
ALTER TABLE `ac_block_descriptions` MODIFY COLUMN `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';
ALTER TABLE `ac_block_descriptions` MODIFY COLUMN `content` text NOT NULL DEFAULT '' COMMENT 'translatable';

ALTER TABLE `ac_category_descriptions` MODIFY COLUMN `name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'translatable';
ALTER TABLE `ac_category_descriptions` MODIFY COLUMN `meta_keywords` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_category_descriptions` MODIFY COLUMN `meta_description` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_category_descriptions` MODIFY COLUMN `description` text COLLATE utf8_bin NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_content_descriptions` MODIFY COLUMN `name` varchar(255) NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_content_descriptions` MODIFY COLUMN `title` varchar(255) NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_content_descriptions` MODIFY COLUMN `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';
ALTER TABLE `ac_content_descriptions` MODIFY COLUMN `content` text NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_coupon_descriptions` MODIFY COLUMN `name` varchar(128) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_coupon_descriptions` MODIFY COLUMN `description` text COLLATE utf8_bin NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_download_descriptions` MODIFY COLUMN `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'translatable';

ALTER TABLE `ac_field_descriptions` MODIFY COLUMN `name` varchar(255) NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_field_descriptions` MODIFY COLUMN `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';

ALTER TABLE `ac_fields_group_descriptions` MODIFY COLUMN `name` varchar(255) NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_fields_group_descriptions` MODIFY COLUMN `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';

ALTER TABLE `ac_form_descriptions` MODIFY COLUMN `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';

ALTER TABLE `ac_global_attributes_descriptions` MODIFY COLUMN `name` varchar(64) COLLATE utf8_bin NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_global_attributes_value_descriptions` MODIFY COLUMN `value` text COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'translatable';

ALTER TABLE `ac_global_attributes_groups_descriptions` MODIFY COLUMN `name` varchar(64) COLLATE utf8_bin NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_language_definitions` MODIFY COLUMN `language_value` text NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_length_class_descriptions` MODIFY COLUMN `title` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_length_class_descriptions` MODIFY COLUMN `unit` varchar(4) COLLATE utf8_bin NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_order_statuses` MODIFY COLUMN `name` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_page_descriptions` MODIFY COLUMN `name` varchar(255) NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_page_descriptions` MODIFY COLUMN `title` varchar(255) NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_page_descriptions` MODIFY COLUMN `keywords` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';
ALTER TABLE `ac_page_descriptions` MODIFY COLUMN `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';
ALTER TABLE `ac_page_descriptions` MODIFY COLUMN `content` text DEFAULT NULL COMMENT 'translatable';

ALTER TABLE `ac_product_descriptions` MODIFY COLUMN `name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_product_descriptions` MODIFY COLUMN `meta_keywords` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_product_descriptions` MODIFY COLUMN `meta_description` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_product_descriptions` MODIFY COLUMN `description` text COLLATE utf8_bin NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_product_filter_descriptions` MODIFY COLUMN `value` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';
ALTER TABLE `ac_product_filter_ranges_descriptions` MODIFY COLUMN `name` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable';

ALTER TABLE `ac_product_option_descriptions` MODIFY COLUMN `name` varchar(255) COLLATE utf8_bin NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_product_option_value_descriptions` MODIFY COLUMN `name` text COLLATE utf8_bin DEFAULT NULL COMMENT 'translatable';

ALTER TABLE `ac_product_tags` MODIFY COLUMN `tag` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'translatable';

ALTER TABLE `ac_resource_descriptions` MODIFY COLUMN `name` varchar(255) NOT NULL DEFAULT '' NULL COMMENT 'translatable';
ALTER TABLE `ac_resource_descriptions` MODIFY COLUMN `title` varchar(255) NOT NULL DEFAULT '' NULL COMMENT 'translatable';
ALTER TABLE `ac_resource_descriptions` MODIFY COLUMN `description` text DEFAULT NULL NULL COMMENT 'translatable';

ALTER TABLE `ac_stock_statuses` MODIFY COLUMN `name` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
  
ALTER TABLE `ac_store_descriptions` MODIFY COLUMN `description` text COLLATE utf8_bin NOT NULL COMMENT 'translatable';
    
ALTER TABLE `ac_weight_class_descriptions` MODIFY COLUMN `title` varchar(32) COLLATE utf8_bin NOT NULL COMMENT 'translatable';
ALTER TABLE `ac_weight_class_descriptions` MODIFY COLUMN `unit` varchar(4) COLLATE utf8_bin NOT NULL DEFAULT ''  COMMENT 'translatable';

DROP TABLE IF EXISTS `ac_field_values`;
CREATE TABLE `ac_field_values` (
  `value_id` int(11) NOT NULL auto_increment,
  `field_id` int(11) NOT NULL DEFAULT '0',
  `value` text COLLATE utf8_bin NOT NULL DEFAULT '',
  `language_id` int(11) NOT NULL,
  PRIMARY KEY (`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;