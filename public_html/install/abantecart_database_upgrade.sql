UPDATE `ac_extensions` SET `key`='handling' where `key`='handling_fee';
<<<<<<< HEAD
ALTER TABLE `ac_global_attributes` ADD COLUMN `settings` text COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `required`;
=======

ALTER TABLE `ac_url_aliases` ADD COLUMN `language_id` INT NOT NULL DEFAULT '1';
ALTER TABLE `ac_url_aliases` MODIFY COLUMN `keyword` varchar(255) NOT NULL COMMENT 'translatable';
>>>>>>> d356d72d94883febe5a38549c0b4cfc4e0b28d4c
