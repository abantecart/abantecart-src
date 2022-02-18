DELETE FROM `ac_settings` WHERE `group`= 'avatax_integration';
DROP TABLE IF EXISTS `ac_avatax_product_taxcode_values`;
DROP TABLE IF EXISTS `ac_avatax_customer_settings_values`;

ALTER TABLE `ac_order_products` DROP COLUMN `taxcode_value`;