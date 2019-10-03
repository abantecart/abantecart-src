ALTER TABLE `ac_orders`
CHANGE COLUMN `invoice_prefix` `invoice_prefix` VARCHAR(10) NOT NULL DEFAULT '',
CHANGE COLUMN `payment_method_data` `payment_method_data` text NOT NULL DEFAULT ''
;

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
ADD COLUMN `store_id` INT NULL DEFAULT 0 AFTER `id`;

ALTER TABLE `ac_product_stock_locations` CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `ac_order_product_stock_locations` CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES
('general','config_google_tag_manager_id','');