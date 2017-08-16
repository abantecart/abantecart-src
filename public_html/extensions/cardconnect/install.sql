
CREATE TABLE IF NOT EXISTS `ac_cardconnect_cards` (
  `cardconnect_card_id` INT(11) NOT NULL AUTO_INCREMENT,
  `customer_id` INT(11) NOT NULL DEFAULT '0',
  `profileid` VARCHAR(16) NOT NULL DEFAULT '',
  `token` VARCHAR(19) NOT NULL DEFAULT '',
  `type` VARCHAR(50) NOT NULL DEFAULT '',
  `account` VARCHAR(4) NOT NULL DEFAULT '',
  `expiry` VARCHAR(4) NOT NULL DEFAULT '',
  `date_added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`cardconnect_card_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `ac_cardconnect_orders` (
  `cardconnect_order_id` INT(11) NOT NULL AUTO_INCREMENT,
  `order_id` INT(11) NOT NULL DEFAULT '0',
  `cardconnect_test_mode` INT(1) NOT NULL DEFAULT '0',
  `customer_id` INT(11) NOT NULL DEFAULT '0',
  `payment_method` VARCHAR(255) NOT NULL DEFAULT '',
  `retref` VARCHAR(12) NOT NULL DEFAULT '',
  `authcode` VARCHAR(6) NOT NULL DEFAULT '',
  `currency_code` VARCHAR(3) NOT NULL DEFAULT '',
  `total` DECIMAL(10, 2) NOT NULL DEFAULT '0.00',
  `date_added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`cardconnect_order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `ac_cardconnect_order_transactions` (
  `cardconnect_order_transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
  `cardconnect_order_id` INT(11) NOT NULL DEFAULT '0',
  `type` VARCHAR(50) NOT NULL DEFAULT '',
  `retref` VARCHAR(12) NOT NULL DEFAULT '',
  `amount` DECIMAL(10, 2) NOT NULL DEFAULT '0.00',
  `status` VARCHAR(255) NOT NULL DEFAULT '',
  `date_modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_added` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`cardconnect_order_transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

CREATE TABLE IF NOT EXISTS `ac_cardconnect_customers` (
	`customer_id` int(11) NOT NULL,
	`profileid` varchar(50) NOT NULL,
	`test_mode` tinyint(1) DEFAULT 0,
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`customer_id`, `test_mode`),
	UNIQUE KEY `customer_id` (`customer_id`, `test_mode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;