INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('system','core_version', "1.4.4");

alter table `ac_orders` alter column date_added set default (CURRENT_TIMESTAMP);
update `ac_orders` SET date_added = date_modified WHERE date_added = '0000-00-00 00:00:00';
