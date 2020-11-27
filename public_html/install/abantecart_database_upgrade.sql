ALTER TABLE `ac_extensions`
ADD COLUMN `support_expiration` DATETIME NULL AFTER `date_installed`,
ADD COLUMN `mp_product_url` VARCHAR(255) NULL DEFAULT '' AFTER `support_expiration`;
