/*
* upgrade SQL script for abantecart v.1.0.2 up to 1.0.3
*/

ALTER TABLE `ac_products`
ADD COLUMN `ship_individually` int(1) NOT NULL DEFAULT '0';
ALTER TABLE `ac_products`
ADD COLUMN `shipping_price` decimal(15,4) NOT NULL DEFAULT '0.0000';
ALTER TABLE `ac_products`
ADD COLUMN `free_shipping` int(1) NOT NULL DEFAULT '0';