ALTER TABLE `ac_store_descriptions`
CHANGE COLUMN `description` `description` LONGTEXT NULL DEFAULT '' COMMENT 'translatable' ,
CHANGE COLUMN `title` `title` LONGTEXT NULL DEFAULT '' COMMENT 'translatable' ,
CHANGE COLUMN `meta_description` `meta_description` LONGTEXT NULL DEFAULT '' COMMENT 'translatable' ,
CHANGE COLUMN `meta_keywords` `meta_keywords` LONGTEXT NULL DEFAULT '' COMMENT 'translatable' ;

ALTER TABLE `ac_customer_transactions`
  CHANGE COLUMN `credit` `credit` DECIMAL(15,4) NULL DEFAULT '0',
  CHANGE COLUMN `debit` `debit` DECIMAL(15,4) NULL DEFAULT '0';

DROP TABLE IF EXISTS `ac_product_stock_locations`;
CREATE TABLE `ac_product_stock_locations` (
  `product_id` int(11) NOT NULL,
  `product_option_value_id` int(11) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `ac_product_stock_locations_idx` (`product_id`,`product_option_value_id`,`location_id`),
  KEY `ac_product_stock_locations_idx2` (`product_option_value_id`)
);

DROP TABLE IF EXISTS `ac_order_product_stock_locations`;
CREATE TABLE `ac_order_product_stock_locations` (
  `order_product_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_option_value_id` int(11) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) DEFAULT '0',
  KEY `ac_product_options_value_idx` (`product_option_value_id`),
  KEY `ac_product_options_value_idx2` (`order_product_id`,`product_id`,`product_option_value_id`,`location_id`)
);

ALTER TABLE `ac_custom_lists`
CHANGE COLUMN `store_id` `store_id` INT NULL DEFAULT 0 AFTER `id`,
DROP INDEX `ac_custom_block_id_list_idx` ,
ADD INDEX `ac_custom_block_id_list_idx` (`custom_block_id` ASC, `id` ASC, `data_type` ASC, `store_id` ASC);
