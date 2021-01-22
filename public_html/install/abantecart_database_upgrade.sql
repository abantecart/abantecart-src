ALTER TABLE `ac_extensions`
ADD COLUMN `support_expiration` DATETIME NULL AFTER `date_installed`,
ADD COLUMN `mp_product_url` VARCHAR(255) NULL DEFAULT '' AFTER `support_expiration`;

ALTER TABLE `ac_resource_types`
CHANGE COLUMN `file_types` `file_types` VARCHAR(255) NOT NULL DEFAULT '' ;

