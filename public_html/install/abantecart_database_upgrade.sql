insert into `ac_settings` (store_id, `group`,`key`,`value`) values (0,'system','config_voicecontrol', 1);

alter table `ac_fields` add column `regexp_pattern` varchar(255) NOT NULL DEFAULT '' AFTER `status`;
alter table `ac_field_descriptions` add column `error_text` varchar(255) not null default '' AFTER `language_id`, comment = 'translatable';

alter table `ac_downloads` change `remaining` `max_downloads` int(11) DEFAULT NULL;
alter table `ac_downloads` add column 
(
	`expire_days` int(11) DEFAULT NULL,
	`sort_order` int(11) NOT NULL,  
	`activate_order_status_id` int(11) NOT NULL DEFAULT '0', 
    `shared` int(1) NOT NULL DEFAULT '0',
	`status` int(1) NOT NULL DEFAULT '0', 
	`date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
);
 
DROP TABLE IF EXISTS `ac_download_attribute_values`;
CREATE TABLE `ac_download_attribute_values` (
  `download_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) NOT NULL,
  `download_id` int(11) NOT NULL,
  `attribute_value_ids` text COLLATE utf8_bin  DEFAULT NULL,
  PRIMARY KEY (`download_attribute_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;


alter table `ac_order_downloads` change `remaining` `remaining_count` int(11) DEFAULT NULL;
alter table `ac_order_downloads` add column 
(
  `download_id` int(11) NOT NULL DEFAULT '0', 
  `status` int(1) NOT NULL DEFAULT '0',
  `expire_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `sort_order` int(11) NOT NULL, 
  `activate_order_status_id` int(11) NOT NULL DEFAULT '0', 
  `attributes_data` text COLLATE utf8_bin  DEFAULT NULL, 
  `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `ac_order_data`;
CREATE TABLE `ac_order_data` (
  `order_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `data` text COLLATE utf8_bin DEFAULT NULL,  -- serialized values
  `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
  PRIMARY KEY (`order_id`, `type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `ac_order_data_types`;
CREATE TABLE `ac_order_data_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'translatable',
  `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP, 
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

ALTER TABLE `ac_products` ADD COLUMN `call_for_order` smallint NOT NULL default '0' AFTER `cost`;
