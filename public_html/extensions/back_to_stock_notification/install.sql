DROP TABLE IF EXISTS `ac_back_to_stock`;
CREATE TABLE `ac_back_to_stock` (
	`task_id` int(11) NOT NULL AUTO_INCREMENT,
	`user_id` int(255) NOT NULL,
	`product_id` int(255) NOT NULL,
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`task_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;