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