ALTER TABLE `ac_tax_rates` ADD COLUMN `rate_prefix` char(1) COLLATE utf8_bin NOT NULL; -- % or $ 
ALTER TABLE `ac_tax_rates` ADD COLUMN `threshold_condition` char(2) COLLATE utf8_bin NOT NULL; -- '<=', '>=', '==' or '<'
ALTER TABLE `ac_tax_rates` ADD COLUMN `threshold` decimal(15,4) NOT NULL DEFAULT '0.0000';


INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES
('sub_total', 'sub_total_calculation_order', '1'),
('shipping', 'shipping_calculation_order', '3'),
('coupon', 'coupon_calculation_order', '4'),
('tax', 'tax_calculation_order', '5'),
('total', 'total_calculation_order', '6');
