ALTER TABLE `ac_product_discounts`
ADD COLUMN `price_prefix` CHAR(1) NOT NULL DEFAULT '' AFTER `priority`;

ALTER TABLE `ac_product_specials`
ADD COLUMN `price_prefix` CHAR(1) NOT NULL DEFAULT '' AFTER `priority`;
