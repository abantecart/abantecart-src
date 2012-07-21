
/*
updating language definitions table
*/

ALTER TABLE `ac_language_definitions` MODIFY `block` varchar(160) NOT NULL;
ALTER TABLE `ac_language_definitions` MODIFY `language_key` varchar(170) character set utf8 collate utf8_bin NOT NULL;
 /*need to add deleting duplicates from this table*/
DELETE FROM `ac_language_definitions`
WHERE `language_id`<'1' OR TRIM(`language_key`)='' OR TRIM(`language_value`)='' OR TRIM(`block`)='';

DROP TABLE IF EXISTS `ac_abantecart_temp_table`;
CREATE TABLE `abantecart_temp_table` (
  `language_definition_id` int(11) NOT NULL auto_increment,
  `language_id` int(11) NOT NULL,
  `section` tinyint(1) NOT NULL default '0' COMMENT '0-SF, 1-ADMIN',
  `block` varchar(160) NOT NULL,
  `language_key` varchar(170) character set utf8 collate utf8_bin NOT NULL,
  `language_value` text NOT NULL,
  `update_date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `create_date` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`language_definition_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO abantecart_temp_table (`section`,`block`,`language_id`,`language_key`,`language_value`, `update_date`, `create_date`)
SELECT DISTINCT `section`,`block`,`language_id`,`language_key`,`language_value`, `update_date`, `create_date`
FROM `ac_language_definitions`
GROUP BY `section`,`block`,`language_id`,`language_key`
ORDER BY update_date ASC;

TRUNCATE TABLE `ac_language_definitions`;

INSERT INTO `ac_language_definitions` (`section`,`block`,`language_id`,`language_key`,`language_value`, `update_date`, `create_date`)
SELECT `section`,`block`,`language_id`,`language_key`,`language_value`, `update_date`, `create_date`
FROM abantecart_temp_table;

DROP TABLE IF EXISTS `abantecart_temp_table`;


CREATE UNIQUE INDEX `lang_definition_index`
ON `ac_language_definitions` ( `section`,`block`,`language_id`,`language_key` );