ALTER TABLE `ac_customers` ADD COLUMN `loginname` varchar(96) COLLATE utf8_bin NOT NULL DEFAULT '';

UPDATE `ac_customers` SET `loginname` = `email`;

ALTER TABLE `ac_customers` ADD UNIQUE KEY `customers_loginname` (`loginname`);

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES
('checkout', 'prevent_email_as_login', '0'),
('api', 'config_admin_api_status', '0'),
('api', 'config_admin_api_key', ''),
('api', 'config_admin_access_ip_list', '');

DROP TABLE IF EXISTS `ac_encryption_keys`;
CREATE TABLE `ac_encryption_keys` (
  `key_id` int(3) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` int(1) NOT NULL,  
  `comment` text COLLATE utf8_bin NOT NULL,  
  PRIMARY KEY (`key_id`),
  UNIQUE KEY `encryption_keys_key_name` (`key_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;