CREATE TABLE IF NOT EXISTS `ac_realex_orders` (
	`realex_order_id` INT(11) NOT NULL AUTO_INCREMENT,
	`order_id` INT(11) NOT NULL,
	`order_ref` CHAR(50) NOT NULL,
	`order_ref_previous` CHAR(50) NOT NULL,
	`pasref` VARCHAR(50) NOT NULL,
	`pasref_previous` VARCHAR(50) NOT NULL,
	`capture_status` INT(1) DEFAULT NULL,
	`void_status` INT(1) DEFAULT NULL,
	`settle_type` VARCHAR(8) DEFAULT NULL,
	`rebate_status` INT(1) DEFAULT NULL,
	`currency_code` CHAR(3) NOT NULL,
	`authcode` VARCHAR(30) NOT NULL,
	`account` VARCHAR(30) NOT NULL,
	`total` DECIMAL( 10, 2 ) NOT NULL,
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`realex_order_id`),
	INDEX `ac_realex_orders_idx` (`realex_order_id`, `order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE IF NOT EXISTS `ac_realex_order_transactions` (
	`realex_order_transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
	`realex_order_id` INT(11) NOT NULL,
	`type` ENUM('auth', 'payment', 'rebate', 'void') DEFAULT NULL,
	`amount` DECIMAL( 10, 2 ) NOT NULL,
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`realex_order_transaction_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;