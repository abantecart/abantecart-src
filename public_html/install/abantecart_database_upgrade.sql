ALTER TABLE `ac_product_options`
  ADD `settings` text COLLATE utf8_general_ci;

ALTER TABLE `ac_order_options`
  ADD `settings` text COLLATE utf8_general_ci;
  
ALTER TABLE `ac_orders`
  ADD `shipping_method_key` varchar(128) NOT NULL DEFAULT '';

ALTER TABLE `ac_orders`
  ADD `payment_method_key` varchar(128) NOT NULL DEFAULT '';

ALTER TABLE `ac_order_totals`
  ADD `key` varchar(128) NOT NULL DEFAULT '';
