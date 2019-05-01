ALTER TABLE `ac_orders`
CHANGE COLUMN `invoice_prefix` `invoice_prefix` VARCHAR(10) NOT NULL DEFAULT '',
CHANGE COLUMN `payment_method_data` `payment_method_data` text NOT NULL DEFAULT ''
;

ALTER TABLE `ac_product_stock_locations` CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `ac_order_product_stock_locations` CHARACTER SET utf8 COLLATE utf8_general_ci;