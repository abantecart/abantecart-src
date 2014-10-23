DROP TABLE IF EXISTS `ac_online_customers`;
CREATE TABLE `ac_online_customers` (
  `customer_id` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `url` text NOT NULL,
  `referer` text NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

#Standard date_added date_modified
ALTER TABLE `ac_customer_transactions`
  CHANGE `update_date` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CHANGE `create_date` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `ac_extensions`
  CHANGE `update_date` `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  CHANGE `create_date` `date_added` timestamp NOT NULL default '0000-00-00 00:00:00';

ALTER TABLE `ac_banners`
  CHANGE `create_date` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `update_date` `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;

ALTER TABLE `ac_banner_descriptions`
  CHANGE `update_date` `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;

ALTER TABLE `ac_language_definitions`
  CHANGE `update_date` `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  CHANGE `create_date` `date_added` timestamp NOT NULL default '0000-00-00 00:00:00';

ALTER TABLE `ac_user_groups`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_pages`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_page_descriptions`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_content_descriptions`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_blocks`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_custom_blocks`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_custom_lists`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_block_descriptions`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_block_templates`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_layouts`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_block_layouts`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_messages`
  CHANGE `create_date` `date_added` timestamp NOT NULL default '0000-00-00 00:00:00',
  CHANGE `update_date` `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;

ALTER TABLE `ac_ant_messages`
  CHANGE `update_date` `date_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP;

ALTER TABLE `ac_resource_library`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_resource_descriptions`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_resource_map`
  CHANGE `created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `updated` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_global_attributes_type_descriptions`
  CHANGE `create_date` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  CHANGE `update_date` `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE `ac_tasks`
  CHANGE `date_created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `ac_task_details`
  CHANGE `date_created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `ac_task_steps`
  CHANGE `date_created` `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `ac_settings`
  ADD `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `ac_product_discounts`
  ADD `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `ac_product_specials`
  ADD `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `ac_users`
  ADD `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00';

# fix of previous upgrade bug
alter table `ac_downloads` modify column `expire_days` int(11) null default null;


# add new menu item for resource library
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (10,'resource_library',17);
-- ITEM_TEXT
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (11,'text_resource_library',17);
-- ITEM_URL
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (12,'catalog/resource_library',17);
-- PARENT_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (13,'catalog',17);
-- SORT_ORDER
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_integer`,`row_id`)
VALUES (14,7,17);
-- ITEM_TYPE
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (15,'core',17);
-- ITEM_RL_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (40,'<i class="fa fa-camera"></i>&nbsp;',17);


#remove all language definitions for admin side
DELETE FROM `ac_language_definitions` WHERE section=1;

#indexes
CREATE INDEX `addresses_idx` ON `ac_addresses` ( `customer_id`, `country_id`, `zone_id`  );
CREATE INDEX `categories_idx` ON `ac_categories` ( `category_id`, `parent_id`, `status`  );
CREATE INDEX `countries_idx` ON `ac_countries` ( `iso_code_2`, `iso_code_3`, `status`  );
CREATE INDEX `coupons_products_idx` ON `ac_coupons_products` ( `coupon_id`, `product_id`  );
CREATE INDEX `customers_idx` ON `ac_customers` ( `store_id`, `address_id`, `customer_group_id` );
CREATE INDEX `customer_transactions_idx` ON `ac_customer_transactions` ( `customer_id`, `order_id` );
CREATE INDEX `downloads_idx` ON `ac_downloads` ( `activate_order_status_id`, `shared` );
CREATE INDEX `download_attribute_values_idx` ON `ac_download_attribute_values` ( `attribute_id`, `download_id` );

CREATE INDEX `orders_idx`
ON `ac_orders` (`invoice_id`,
								`store_id`,
								`customer_id`,
								`customer_group_id`,
								`shipping_zone_id`,
								`shipping_country_id`,
								`payment_zone_id`,
								`payment_country_id`,
								`order_status_id`,
								`language_id`,
								`currency_id`,
								`coupon_id`);

CREATE INDEX `order_downloads_idx` ON `ac_order_downloads` (`order_id`, `order_product_id`, `download_id`, `status`, `activate_order_status_id`);
CREATE INDEX `order_downloads_history_idx` ON `ac_order_downloads_history` (`download_id`);
CREATE INDEX `order_history_idx` ON `ac_order_history` (`order_id`, `order_status_id`, `notify`);
CREATE INDEX `order_options_idx` ON `ac_order_options` (`order_id`, `order_product_id`, `product_option_value_id`);
CREATE INDEX `order_products_idx` ON `ac_order_products` (`order_id`,  `product_id`);
CREATE INDEX `products_idx` ON `ac_products` (`stock_status_id`,  `manufacturer_id`, `weight_class_id`, `length_class_id`);
CREATE INDEX `product_discounts_idx` ON `ac_product_discounts` (`product_id`, `customer_group_id`);
CREATE INDEX `product_options_idx` ON `ac_product_options` (`attribute_id`, `product_id`, `group_id` );
CREATE INDEX `product_option_values_idx` ON `ac_product_option_values` ( `product_option_id`, `product_id`, `group_id`, `attribute_value_id` );
CREATE INDEX `product_option_value_descriptions_idx` ON `ac_product_option_value_descriptions` ( `product_id` );
CREATE INDEX `reviews_idx` ON `ac_reviews` ( `product_id`, `customer_id` );
CREATE INDEX `tax_rates_idx` ON `ac_tax_rates` ( `location_id`, `zone_id`, `tax_class_id` );
CREATE INDEX `global_attributes_idx` ON `ac_global_attributes` ( `attribute_parent_id`, `attribute_group_id`, `attribute_type_id` );


