CREATE TABLE IF NOT EXISTS `ac_stripe_orders` (
	`stripe_order_id` INT(11) NOT NULL AUTO_INCREMENT,
	`stripe_test_mode` tinyint(1) DEFAULT 0,
	`order_id` INT(11) NOT NULL,
	`charge_id` CHAR(50) NOT NULL,
	`charge_id_previous` CHAR(50) NOT NULL DEFAULT '',
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`stripe_order_id`),
	INDEX `ac_stripe_order_idx` (`stripe_order_id`, `order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `ac_stripe_customers` (
	`customer_id` int(11) NOT NULL,
	`customer_stripe_id` varchar(50) NOT NULL,
	`stripe_test_mode` tinyint(1) DEFAULT 0,
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`customer_id`, `stripe_test_mode`),
	UNIQUE KEY `customer_id` (`customer_id`, `stripe_test_mode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;