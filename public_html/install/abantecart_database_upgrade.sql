ALTER TABLE `ac_orders`
CHANGE COLUMN `invoice_prefix` `invoice_prefix` VARCHAR(10) NOT NULL DEFAULT '',
CHANGE COLUMN `payment_method_data` `payment_method_data` text NOT NULL DEFAULT ''
;
