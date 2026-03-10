INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('system','core_version', "1.4.4");

alter table `ac_ant_messages`
    modify `start_date` timestamp null;

alter table `ac_ant_messages`
    modify `viewed_date` timestamp null;

alter table `ac_banner_descriptions`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_banners`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_block_descriptions`
    modify `date_added` timestamp default current_timestamp() not null;

alter table `ac_block_descriptions`
    modify `date_modified` timestamp default current_timestamp() not null on update current_timestamp();

alter table `ac_block_templates`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_blocks`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_categories`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_collections`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_content_descriptions`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_contents`
    drop primary key,
    MODIFY COLUMN `content_id` INT AUTO_INCREMENT PRIMARY KEY;

alter table `ac_coupons`
    modify `date_start` date null;

alter table `ac_coupons`
    modify `date_end` date null;

alter table `ac_coupons`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_coupons_categories`
    charset = utf8mb4;

alter table `ac_custom_blocks`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_custom_lists`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_customer_notifications`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_customer_transactions`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_customers`
    modify `telephone` varchar(32) default '' not null,
    modify `date_added` timestamp default current_timestamp() null,
    modify `last_login` timestamp null;
update `ac_customers` SET `date_added` = `date_modified` WHERE `date_added` = '0000-00-00 00:00:00';

alter table `ac_downloads`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_email_templates`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_extensions`
    modify `date_installed` timestamp null;

alter table `ac_extensions`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_global_attributes_type_descriptions`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_language_definitions`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_layouts`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_length_classes`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_locations`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_messages`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_online_customers`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_order_data`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_order_data_types`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_order_downloads`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_order_history`
    modify `date_added` timestamp default current_timestamp() null;

ALTER TABLE `ac_orders` MODIFY date_added timestamp default current_timestamp();
update `ac_orders` SET date_added = date_modified WHERE date_added = '0000-00-00 00:00:00';

alter table `ac_page_descriptions`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_pages`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_product_discounts`
    modify `date_start` date null;

alter table `ac_product_discounts`
    modify `date_end` date null;

alter table `ac_product_discounts`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_product_specials`
    modify `date_start` date null;

alter table `ac_product_specials`
    modify `date_end` date null;

alter table `ac_product_specials`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_products`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_resource_descriptions`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_resource_library`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_resource_map`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_reviews`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_settings`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_task_details`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_task_steps`
    modify `last_time_run` timestamp null;

alter table `ac_task_steps`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_task_steps`
    engine = InnoDB;

alter table `ac_tasks`
    modify `last_time_run` timestamp null;

alter table `ac_tasks`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_tax_classes`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_tax_rates`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_user_groups`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_user_notifications`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_users`
    modify `last_login` datetime null;

alter table `ac_users`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_weight_classes`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_zones_to_locations`
    modify `date_added` timestamp default current_timestamp() null;

alter table `ac_stock_statuses`
    modify `name` varchar(128) NOT NULL COMMENT 'translatable';

create index `ac_orders_date_idx`
    on `ac_orders` (`date_added` desc, `date_modified` desc);

--
-- Table structure for table `ac_shopping_sessions`
--
DROP TABLE IF EXISTS `ac_shopping_sessions`;
create table `ac_shopping_sessions`
(
    `customer_id`   int                                 null,
    `order_id`      int                                 null,
    `type`          varchar(255)                        not null,
    `key`           varchar(255)                        not null,
    `data`          json                                not null,
    `date_added`    timestamp default CURRENT_TIMESTAMP not null,
    `date_modified` timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint `ac_shopping_sessions_customer_fk`
        foreign key (`customer_id`) references `ac_customers` (`customer_id`)
            on update cascade on delete cascade
);
create index `ac_shopping_sessions_int_idx`
    on `ac_shopping_sessions` (`customer_id` desc, `order_id` desc);
create unique index `ac_shopping_sessions_text_idx`
    on `ac_shopping_sessions` (`type`, `key`);

UPDATE `ac_product_discounts` SET date_start = NULL WHERE date_start = '0000-00-00';
UPDATE `ac_product_discounts` SET date_end = NULL WHERE date_end = '0000-00-00';
UPDATE `ac_product_specials` SET date_start = NULL WHERE date_start = '0000-00-00';
UPDATE `ac_product_specials` SET date_end = NULL WHERE date_end = '0000-00-00';

alter table `ac_category_descriptions`
    modify `description` longtext NULL COMMENT 'translatable';

alter table `ac_contents`
    add show_title int default 1 null after content_bar;