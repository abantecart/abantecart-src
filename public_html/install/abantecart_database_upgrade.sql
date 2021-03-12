ALTER TABLE `ac_extensions`
ADD COLUMN `support_expiration` DATETIME NULL AFTER `date_installed`,
ADD COLUMN `mp_product_url` VARCHAR(255) NULL DEFAULT '' AFTER `support_expiration`;

ALTER TABLE `ac_resource_types`
CHANGE COLUMN `file_types` `file_types` VARCHAR(255) NOT NULL DEFAULT '' ;

ALTER TABLE `ac_coupons`
ADD COLUMN `condition_rule` ENUM('OR', 'AND') NOT NULL DEFAULT 'OR' AFTER `status`;

INSERT INTO `ac_settings` (`group`, `key`, `value`)
VALUES ('checkout','config_phone_validation_pattern','/^[0-9]{3,32}$/');


--
-- DDL for table `coupon_categories`
--
DROP TABLE IF EXISTS `ac_coupons_categories`;
CREATE TABLE `ac_coupons_products` (
  `coupon_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`coupon_category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1;

CREATE INDEX `ac_coupons_categories_idx` ON `ac_coupons_categories` ( `coupon_id`, `category_id`  );
