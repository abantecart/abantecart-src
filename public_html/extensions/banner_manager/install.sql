DROP TABLE IF EXISTS `ac_banners`;
CREATE TABLE `ac_banners` (
	`banner_id` int(11) NOT NULL AUTO_INCREMENT,
	`status` int(1) NOT NULL DEFAULT '0',
	`banner_type` int(11) NOT NULL DEFAULT '1',
	`banner_group_name` varchar(255) NOT NULL DEFAULT '',
	`start_date` timestamp NULL DEFAULT NULL,
	`end_date` timestamp NULL DEFAULT NULL,
	`blank` tinyint(1) NOT NULL DEFAULT '0',
	`target_url` text DEFAULT '',
	`sort_order` int(11) NOT NULL,
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`banner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `ac_banner_descriptions`;
CREATE TABLE `ac_banner_descriptions` (
  `banner_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `meta` text(1500) DEFAULT '',
	`date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`banner_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `ac_banner_stat`;
CREATE TABLE `ac_banner_stat` (
  `banner_id` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `store_id` int(11) NOT NULL,
  `user_info` text(1500) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
CREATE INDEX `banner_stat_idx` ON `ac_banner_stat` (`banner_id`, `type`, `time`, `store_id`);