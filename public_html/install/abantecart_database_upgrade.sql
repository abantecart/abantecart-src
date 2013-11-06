insert into ac_settings (store_id, `group`,`key`,`value`) values (0,'system','config_voicecontrol', 1);

alter table `ac_fields` add column `regexp_pattern` varchar(255) NOT NULL DEFAULT '' AFTER `status`;
alter table `ac_field_descriptions` add column `error_text` varchar(255) not null default '' AFTER `language_id`, comment = 'translatable';

DROP TABLE IF EXISTS `ac_product_files`;
CREATE TABLE `ac_product_files` (
  `file_id` int(11) NOT NULL AUTO_INCREMENT, -- download_id
  `product_id` int(11) NOT NULL,		-- merge from  ac_products_to_downloads
  `status` int(1) NOT NULL DEFAULT '0', -- in migration set to 1
  `filename` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mask` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '', -- ??? need to see what this is for
  `max_downloads` int(11) DEFAULT NULL, -- remaining, NULL -> No limit 
  `expire_days` int(11) DEFAULT NULL,  -- defalut to NULL -> No expiration
  `sort_order` int(11) NOT NULL,  
  `activate_order_status_id` int(11) NOT NULL DEFAULT '0', 
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

--- Copy name from RL to here and allow to edit
--- Need to discuss
DROP TABLE IF EXISTS `ac_product_files_descriptions`;
CREATE TABLE `ac_product_files_descriptions` (
  `file_id` int(11) NOT NULL,  -- download_id
  `language_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'translatable',
  PRIMARY KEY (`file_id`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- All data is taken from global 
DROP TABLE IF EXISTS `ac_product_file_attribute_values`;
CREATE TABLE `ac_product_file_attributes` (
  `product_file_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `attribute_value_id` int(11),
  PRIMARY KEY (`product_option_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `ac_order_product_files`;
CREATE TABLE `ac_order_product_files` (
  `order_product_file_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_product_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL, 
  `status` int(1) NOT NULL DEFAULT '0',
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '',
  `filename` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `mask` varchar(128) COLLATE utf8_bin NOT NULL DEFAULT '',
  `remaining_count` int(3) DEFAULT NULL,
  `expire_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `sort_order` int(11) NOT NULL, 
  `activate_order_status_id` int(11) NOT NULL DEFAULT '0', 
  `attributes_data` text COLLATE utf8_bin  DEFAULT NULL,  -- serialized values 
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  
  PRIMARY KEY (`order_product_file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

INSERT INTO `ac_product_files` (`file_id`, `product_id`, `status`, `filename`, `mask`, `max_downloads`, `expire_days`, `sort_order`, `activate_order_status_id`, `date_added`, `date_modified`)
SELECT d.`download_id`, pd.`product_id`, 1, d.`filename`,  d.`mask`, d.`remaining`, NULL, 0, 5, d.`date_added`, now() 
FROM `ac_downloads` d INNER JOIN `ac_products_to_downloads` pd ON d.`download_id` = pd.`download_id`;

INSERT INTO `ac_product_files_descriptions` (`file_id`, `language_id`, `name`)
SELECT `download_id`, `language_id`, `name` from `ac_download_descriptions`;  

INSERT INTO `ac_order_product_files` (`order_product_file_id`, `order_id`, `order_product_id`, `file_id`, `status`, `name`, `filename`, `mask`, `remaining_count`, `expire_date`, `sort_order`, `activate_order_status_id`, `attributes_data`, `date_added`, `date_modified`)
SELECT `order_download_id`, `order_id`, `order_product_id`, 0, 1, `name`, `filename`, `mask`, `remaining`, '', 0, '', null, '', now() FROM `ac_order_downloads`;

-- Table to keep other order details
DROP TABLE IF EXISTS `ac_order_data`;
CREATE TABLE `ac_order_data` (
  `order_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `data` text COLLATE utf8_bin DEFAULT NULL,  -- serialized values
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`order_id`, `type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `ac_order_data_types`;
CREATE TABLE `ac_order_data_types` (
  `type_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_id` int(11) NOT NULL,
  `name` varchar(64) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'translatable',
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;
