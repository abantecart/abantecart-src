insert into `ac_languages` (`language_id`, `name`, `code`, `locale`, `image`, `directory`, `filename`, `sort_order`, `status`) VALUES
(null, 'Русский', 'ru', 'es_RU.UTF-8,ru_RU,russian', '', 'russian', 'russian', 3, 1);

CREATE TABLE `ac_country_descriptions` (
  `country_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL COMMENT 'translatable',
  PRIMARY KEY (`country_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `ac_country_descriptions` (`country_id`, `language_id`, `name`)
SELECT `country_id`, 1, `name` from `ac_countries`; 
ALTER TABLE `ac_countries` drop column `name`;

CREATE TABLE `ac_zone_descriptions` (
  `zone_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `name` varchar(128) COLLATE utf8_bin NOT NULL COMMENT 'translatable',
  PRIMARY KEY (`zone_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `ac_zone_descriptions` (`zone_id`, `language_id`, `name`)
SELECT `zone_id`, 1, `name` from `ac_zones`; 
ALTER TABLE `ac_zones` drop column `name`;

CREATE TABLE `ac_tax_class_descriptions` (
  `tax_class_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `title` varchar(128) COLLATE utf8_bin NOT NULL COMMENT 'translatable',
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'translatable',
  PRIMARY KEY (`tax_class_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `ac_tax_class_descriptions` (`tax_class_id`, `language_id`, `title`, `description`)
SELECT `tax_class_id`, 1, `title`, `description` from `ac_tax_classes`; 
ALTER TABLE `ac_tax_classes` drop column `title`;
ALTER TABLE `ac_tax_classes` drop column `description`;

CREATE TABLE `ac_tax_rate_descriptions` (
  `tax_rate_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'translatable',
  PRIMARY KEY (`tax_rate_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `ac_tax_rate_descriptions` (`tax_rate_id`, `language_id`, `description`)
SELECT `tax_rate_id`, 1, `description` from `ac_tax_rates`; 
ALTER TABLE `ac_tax_rates` drop column `description`;

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

ALTER TABLE `ac_products` ADD COLUMN `call_to_order` smallint NOT NULL default '0' AFTER `cost`;
