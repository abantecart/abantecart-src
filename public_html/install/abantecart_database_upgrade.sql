
ALTER TABLE `ac_product_option_values`
    ADD COLUMN `cost` DECIMAL(15,4) NOT NULL AFTER `price`;

ALTER TABLE `ac_order_products`
    ADD COLUMN `cost` DECIMAL(15,4) NOT NULL DEFAULT '0.0000' AFTER `price`;

ALTER TABLE `ac_order_options`
    ADD COLUMN `cost` DECIMAL(15,4) NOT NULL DEFAULT '0.0000' AFTER `price`;
