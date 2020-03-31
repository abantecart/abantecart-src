ALTER TABLE `ac_orders`
CHANGE COLUMN `invoice_prefix` `invoice_prefix` VARCHAR(10) NOT NULL DEFAULT '',
CHANGE COLUMN `payment_method_data` `payment_method_data` text NOT NULL DEFAULT ''
;

ALTER TABLE `ac_customer_transactions`
  CHANGE COLUMN `credit` `credit` DECIMAL(15,4) NULL DEFAULT '0',
  CHANGE COLUMN `debit` `debit` DECIMAL(15,4) NULL DEFAULT '0';

DROP TABLE IF EXISTS `ac_product_stock_locations`;
CREATE TABLE `ac_product_stock_locations` (
  `product_id` int(11) NOT NULL,
  `product_option_value_id` int(11) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `ac_product_stock_locations_idx` (`product_id`,`product_option_value_id`,`location_id`),
  KEY `ac_product_stock_locations_idx2` (`product_option_value_id`)
);

DROP TABLE IF EXISTS `ac_order_product_stock_locations`;
CREATE TABLE `ac_order_product_stock_locations` (
  `order_product_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_option_value_id` int(11) DEFAULT NULL,
  `location_id` int(11) NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `sort_order` int(11) DEFAULT '0',
  KEY `ac_product_options_value_idx` (`product_option_value_id`),
  KEY `ac_product_options_value_idx2` (`order_product_id`,`product_id`,`product_option_value_id`,`location_id`)
);

ALTER TABLE `ac_custom_lists`
ADD COLUMN `store_id` INT NULL DEFAULT 0 AFTER `id`;

ALTER TABLE `ac_product_stock_locations` CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE `ac_order_product_stock_locations` CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES
('general','config_google_tag_manager_id','');


DROP TABLE IF EXISTS `ac_email_templates`;
CREATE TABLE `ac_email_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) NOT NULL,
  `text_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `language_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT 0,
  `headers` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `html_body` text COLLATE utf8_unicode_ci NOT NULL,
  `text_body` text COLLATE utf8_unicode_ci NOT NULL,
  `allowed_placeholders` text COLLATE utf8_unicode_ci NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_deleted` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_text_id_idx` (`text_id`,`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `ac_email_templates`
--
LOCK TABLES `abc_email_templates` WRITE;
/*!40000 ALTER TABLE `abc_email_templates` DISABLE KEYS */;
INSERT INTO `abc_email_templates` VALUES (1,1,'storefront_reset_password_link',1,'','{{store_name}} - Password reset','A password reset was requested from {{store_name}}&lt;br /&gt;\r\nTo reset your password click$
/*!40000 ALTER TABLE `abc_email_templates` ENABLE KEYS */;
UNLOCK TABLES;



--
-- SUBMENU DESIGN
-- ITEM_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (10,'email_templates',137);
-- ITEM_TEXT
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (11,'email_templates',137);
-- ITEM_URL
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (12,'design/email_templates',137);
-- PARENT_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (13,'design',137);
-- SORT_ORDER
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_integer`,`row_id`)
VALUES  (14,8,137);
-- ITEM_TYPE
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (15,'core',137);
-- ITEM_RL_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (40,'281',137);
