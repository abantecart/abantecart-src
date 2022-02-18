DROP TABLE IF EXISTS `ac_avatax_product_taxcode_values`;
CREATE TABLE IF NOT EXISTS `ac_avatax_product_taxcode_values` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`product_id` int(11) NOT NULL,
	`taxcode_value` char(255),
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `ac_avatax_customer_settings_values`;
CREATE TABLE IF NOT EXISTS `ac_avatax_customer_settings_values` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`customer_id` int(11) NOT NULL,
		`status` int(1) NOT NULL,
		`exemption_number` char(255),
	`entity_use_code` char(255),
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

ALTER TABLE `ac_order_products` ADD COLUMN `taxcode_value` VARCHAR(255) DEFAULT '';

