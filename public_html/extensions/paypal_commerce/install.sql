
CREATE TABLE IF NOT EXISTS `ac_paypal_customers` (
	`customer_id` int(11) NOT NULL,
	`customer_paypal_id` varchar(50) NOT NULL,
	`paypal_test_mode` tinyint(1) DEFAULT 0,
	`date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`customer_id`, `paypal_test_mode`),
	UNIQUE KEY `customer_id` (`customer_id`, `paypal_test_mode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;