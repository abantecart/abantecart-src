ALTER TABLE `ac_orders` ADD COLUMN `payment_method_data` text COLLATE utf8_bin NOT NULL;
ALTER TABLE `ac_product_option_values` ADD COLUMN `default` SMALLINT DEFAULT 0;
ALTER TABLE `ac_product_option_descriptions` ADD COLUMN `option_placeholder` varchar(255) COLLATE utf8_bin DEFAULT '' COMMENT 'translatable' ;

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES
('checkout','config_shipping_tax_estimate',1),
('checkout','config_coupon_on_cart_page',1);