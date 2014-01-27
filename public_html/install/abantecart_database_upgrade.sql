ALTER TABLE `ac_tax_rates` CHANGE `rate` `rate` DECIMAL(15, 4) NOT NULL DEFAULT '0.0000';

CREATE UNIQUE INDEX `ac_languages_index`
ON `ac_languages` (`language_id`, `code`);

CREATE TABLE `ac_country_descriptions` (
  `country_id`  INT(11)          NOT NULL,
  `language_id` INT(11)          NOT NULL,
  `name`        VARCHAR(128)
                COLLATE utf8_bin NOT NULL
  COMMENT 'translatable',
  PRIMARY KEY (`country_id`, `language_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COLLATE =utf8_bin;

INSERT INTO `ac_country_descriptions` (`country_id`, `language_id`, `name`)
  SELECT
    `country_id`,
    1,
    `name`
  FROM `ac_countries`;
ALTER TABLE `ac_countries` DROP COLUMN `name`;

CREATE TABLE `ac_zone_descriptions` (
  `zone_id`     INT(11)          NOT NULL,
  `language_id` INT(11)          NOT NULL,
  `name`        VARCHAR(128)
                COLLATE utf8_bin NOT NULL
  COMMENT 'translatable',
  PRIMARY KEY (`zone_id`, `language_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COLLATE =utf8_bin;

INSERT INTO `ac_zone_descriptions` (`zone_id`, `language_id`, `name`)
  SELECT
    `zone_id`,
    1,
    `name`
  FROM `ac_zones`;

ALTER TABLE `ac_zones` DROP COLUMN `name`;
UPDATE `ac_zone_descriptions`
SET `name` = 'Kharkiv'
WHERE `zone_id` = 3487;
UPDATE `ac_zone_descriptions`
SET `name` = 'Kyiv'
WHERE `zone_id` = 3490;
UPDATE `ac_zone_descriptions`
SET `name` = 'Kherson'
WHERE `zone_id` = 3491;
UPDATE `ac_zones`
SET `code` = 'KS'
WHERE `zone_id` = 3491;

CREATE TABLE `ac_tax_class_descriptions` (
  `tax_class_id` INT(11)          NOT NULL,
  `language_id`  INT(11)          NOT NULL,
  `title`        VARCHAR(128)
                 COLLATE utf8_bin NOT NULL
  COMMENT 'translatable',
  `description`  VARCHAR(255)
                 COLLATE utf8_bin NOT NULL DEFAULT ''
  COMMENT 'translatable',
  PRIMARY KEY (`tax_class_id`, `language_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COLLATE =utf8_bin;

INSERT INTO `ac_tax_class_descriptions` (`tax_class_id`, `language_id`, `title`, `description`)
  SELECT
    `tax_class_id`,
    1,
    `title`,
    `description`
  FROM `ac_tax_classes`;
ALTER TABLE `ac_tax_classes` DROP COLUMN `title`;
ALTER TABLE `ac_tax_classes` DROP COLUMN `description`;

CREATE TABLE `ac_tax_rate_descriptions` (
  `tax_rate_id` INT(11)          NOT NULL,
  `language_id` INT(11)          NOT NULL,
  `description` VARCHAR(255)
                COLLATE utf8_bin NOT NULL DEFAULT ''
  COMMENT 'translatable',
  PRIMARY KEY (`tax_rate_id`, `language_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COLLATE =utf8_bin;

INSERT INTO `ac_tax_rate_descriptions` (`tax_rate_id`, `language_id`, `description`)
  SELECT
    `tax_rate_id`,
    1,
    `description`
  FROM `ac_tax_rates`;
ALTER TABLE `ac_tax_rates` DROP COLUMN `description`;

INSERT INTO `ac_settings` (store_id, `group`, `key`, `value`) VALUES (0, 'system', 'config_voicecontrol', 1);

ALTER TABLE `ac_fields` ADD COLUMN `regexp_pattern` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `status`;
ALTER TABLE `ac_field_descriptions` ADD COLUMN `error_text` VARCHAR(255) NOT NULL DEFAULT ''
AFTER `language_id`, COMMENT = 'translatable';

ALTER TABLE `ac_downloads` CHANGE `remaining` `max_downloads` INT(11) DEFAULT NULL;
ALTER TABLE `ac_downloads` ADD COLUMN
(
`status` INT(1) NOT NULL DEFAULT '0',
`shared` int(1) NOT NULL DEFAULT '0', -- if used by other products set to 1
`expire_days` DATETIME NULL,
`sort_order` INT(11) NOT NULL,
`activate` VARCHAR(64) NOT NULL,
`activate_order_status_id` INT(11) NOT NULL DEFAULT '0',
`date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

UPDATE `ac_downloads` SET `shared`=1, `status`=1;

DROP TABLE IF EXISTS `ac_download_attribute_values`;
CREATE TABLE `ac_download_attribute_values` (
  `download_attribute_id` INT(11) NOT NULL AUTO_INCREMENT,
  `attribute_id`          INT(11) NOT NULL,
  `download_id`           INT(11) NOT NULL,
  `attribute_value_ids`   TEXT
                          COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`download_attribute_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COLLATE =utf8_bin
  AUTO_INCREMENT =1;


ALTER TABLE `ac_order_downloads` CHANGE `remaining` `remaining_count` INT(11) DEFAULT NULL;
ALTER TABLE `ac_order_downloads` ADD COLUMN
(
`download_id` INT(11) NOT NULL DEFAULT '0',
`status` INT(1) NOT NULL DEFAULT '0',
`expire_date` DATETIME NULL,
`percentage`  int(11) NULL DEFAULT 0,
`sort_order` INT(11) NOT NULL,
`activate` VARCHAR(64) NOT NULL,
`activate_order_status_id` INT(11) NOT NULL DEFAULT '0',
`attributes_data` TEXT COLLATE utf8_bin DEFAULT NULL,
`date_added` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
`date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS `ac_order_downloads_history`;
CREATE TABLE `ac_order_downloads_history` (
  `order_download_history_id` INT(11)          NOT NULL AUTO_INCREMENT,
  `order_download_id`         INT(11)          NOT NULL,
  `order_id`                  INT(11)          NOT NULL,
  `order_product_id`          INT(11)          NOT NULL,
  `filename`                  VARCHAR(128)
                              COLLATE utf8_bin NOT NULL DEFAULT '',
  `mask`                      VARCHAR(128)
                              COLLATE utf8_bin NOT NULL DEFAULT '',
  `download_id`               INT(11)          NOT NULL,
  `download_percent`          INT(11) DEFAULT '0',
  `time`                      TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`order_download_history_id`, `order_download_id`, `order_id`, `order_product_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COLLATE =utf8_bin
  AUTO_INCREMENT =1;


DROP TABLE IF EXISTS `ac_order_data`;
CREATE TABLE `ac_order_data` (
  `order_id`      INT(11)   NOT NULL,
  `type_id`       INT(11)   NOT NULL,
  `data`          TEXT
                  COLLATE utf8_bin DEFAULT NULL, -- serialized values
  `date_added`    TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`, `type_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COLLATE =utf8_bin;

DROP TABLE IF EXISTS `ac_order_data_types`;
CREATE TABLE `ac_order_data_types` (
  `type_id`       INT(11)          NOT NULL AUTO_INCREMENT,
  `language_id`   INT(11)          NOT NULL,
  `name`          VARCHAR(64)
                  COLLATE utf8_bin NOT NULL DEFAULT ''
  COMMENT 'translatable',
  `date_added`    TIMESTAMP        NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`type_id`)
)
  ENGINE =MyISAM
  DEFAULT CHARSET =utf8
  COLLATE =utf8_bin
  AUTO_INCREMENT =1;

ALTER TABLE `ac_products` ADD COLUMN `call_to_order` SMALLINT NOT NULL DEFAULT '0'
AFTER `cost`;

INSERT INTO `ac_resource_types` (`type_name`, `default_icon`, `default_directory`, `file_types`, `access_type`) VALUES
('download', 'icon_resource_download.png', 'download/', '/.+$/i', 1);
