CREATE TABLE IF NOT EXISTS `ac_authorizenet_orders` (
	`authorizenet_order_id` INT(11) NOT NULL AUTO_INCREMENT,
	`authorizenet_test_mode` tinyint(1) DEFAULT 0,
	`order_id` INT(11) NOT NULL,
	`charge_id` CHAR(50) NOT NULL,
	`charge_id_previous` CHAR(50) NOT NULL DEFAULT '',
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`authorizenet_order_id`),
	INDEX `ac_authorizenet_order_idx` (`authorizenet_order_id`, `order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

