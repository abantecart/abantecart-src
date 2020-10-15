ALTER TABLE `ac_extensions`
ADD COLUMN `license_expires` TIMESTAMP NULL AFTER `date_installed`,
ADD COLUMN `mp_product_url` VARCHAR(255) NULL DEFAULT '' AFTER `license_expires`;
