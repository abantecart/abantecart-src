ALTER TABLE `ac_task_details`
CHANGE COLUMN `settings` `settings` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_task_steps`
CHANGE COLUMN `settings` `settings` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_customers`
CHANGE COLUMN `cart` `cart` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_order_downloads`
CHANGE COLUMN `attributes_data` `attributes_data` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_order_options`
CHANGE COLUMN `settings` `settings` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_product_descriptions`
CHANGE COLUMN `description` `description` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_reviews`
CHANGE COLUMN `text` `text` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_store_descriptions`
CHANGE COLUMN `description` `description` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_user_groups`
CHANGE COLUMN `permission` `permission` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_content_descriptions`
CHANGE COLUMN `content` `content` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_block_descriptions`
CHANGE COLUMN `content` `content` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_ant_messages`
CHANGE COLUMN `html` `html` LONGTEXT NULL DEFAULT NULL ;

ALTER TABLE `ac_customers`
ADD COLUMN `data` text DEFAULT null;

ALTER TABLE `ac_customers` ADD COLUMN `salt` varchar(8) COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE `ac_users` ADD COLUMN `salt` varchar(8) COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `ac_users` MODIFY `password` varchar(40);

DELETE FROM `ac_language_definitions` WHERE `section` = 0 AND `block`='account_forgotten';
DELETE FROM `ac_language_definitions` WHERE `section` = 0 AND `block`='mail_account_forgotten';
DELETE FROM `ac_language_definitions` WHERE `section` = 0 AND `block`='mail_account_forgotten_login';
