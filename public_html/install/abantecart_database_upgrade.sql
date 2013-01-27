ALTER TABLE `ac_customers` ADD COLUMN `loginname` varchar(96) COLLATE utf8_bin NOT NULL DEFAULT '';

UPDATE `ac_customers` SET `loginname` = `email`;

ALTER TABLE `ac_customers` ADD UNIQUE KEY `customers_loginname` (`loginname`);

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES
('checkout', 'prevent_email_as_login', '0');