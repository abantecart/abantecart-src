CREATE INDEX `ac_products_status_idx` ON `ac_products` (`product_id`, `status`, `date_available`);
CREATE INDEX `ac_product_descriptions_name_idx` ON `ac_product_descriptions` (`product_id`, `name`);

CREATE INDEX `ac_resource_library_idx` ON `ac_resource_library` ( `resource_id`, `type_id`);

CREATE INDEX `ac_resource_map_sorting_idx` ON `ac_resource_map` ( `resource_id`, `sort_order`);

CREATE INDEX `ac_resource_descriptions_name_idx` ON `ac_resource_descriptions` ( `resource_id`, `name`);
CREATE INDEX `ac_resource_descriptions_title_idx` ON `ac_resource_descriptions` ( `resource_id`, `title`);

ALTER TABLE `ac_customers` CHANGE COLUMN `ip` `ip` VARCHAR(50);
ALTER TABLE `ac_orders` CHANGE COLUMN `ip` `ip` VARCHAR(50);
ALTER TABLE `ac_users` CHANGE COLUMN `ip` `ip` VARCHAR(50);

REPLACE INTO `ac_page_descriptions` (`page_id`, `language_id`, `name`, `title`, `seo_url`, `keywords`, `description`, `content`, `date_added`)
VALUES (12, 1, 'Cart Page', '', '', '', '', '', now() );

INSERT INTO `ac_settings` (`group`, `key`, `value`)
VALUES ('details','config_duplicate_contact_us_to_message',1);

ALTER TABLE `ac_order_products` ADD COLUMN `sku` VARCHAR(64) NOT NULL AFTER `model`;
ALTER TABLE `ac_order_options` ADD COLUMN `sku` VARCHAR(64) NOT NULL AFTER `name`;

