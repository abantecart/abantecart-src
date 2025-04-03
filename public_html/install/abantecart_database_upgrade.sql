alter table `ac_addresses` engine =InnoDB;
alter table `ac_addresses` collate = utf8mb4_unicode_ci;
alter table `ac_ant_messages` engine =InnoDB;
alter table `ac_ant_messages` collate = utf8mb4_unicode_ci;
alter table `ac_banner_descriptions` engine =InnoDB;
alter table `ac_banner_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_banner_stat` engine =InnoDB;
alter table `ac_banner_stat` collate = utf8mb4_unicode_ci;
alter table `ac_banners` engine =InnoDB;
alter table `ac_banners` collate = utf8mb4_unicode_ci;
alter table `ac_block_descriptions` engine =InnoDB;
alter table `ac_block_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_block_layouts` engine =InnoDB;
alter table `ac_block_layouts` collate = utf8mb4_unicode_ci;
alter table `ac_block_templates` engine =InnoDB;
alter table `ac_block_templates` collate = utf8mb4_unicode_ci;
alter table `ac_blocks` engine =InnoDB;
alter table `ac_blocks` collate = utf8mb4_unicode_ci;
alter table `ac_categories` engine =InnoDB;
alter table `ac_categories` collate = utf8mb4_unicode_ci;
alter table `ac_categories` add supplier_code varchar(100) null after status;
alter table `ac_categories` add supplier_id varchar(100) null after supplier_code;
alter table `ac_categories` add constraint `ac_categories_supplier_idx` unique (supplier_code, supplier_id);
alter table `ac_categories_to_stores` engine =InnoDB;
alter table `ac_categories_to_stores` collate = utf8mb4_unicode_ci;
alter table `ac_category_descriptions` engine =InnoDB;
alter table `ac_category_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_collection_descriptions` engine =InnoDB;
alter table `ac_collection_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_collections` engine =InnoDB;
alter table `ac_collections` collate = utf8mb4_unicode_ci;
alter table `ac_content_descriptions` engine =InnoDB;
alter table `ac_content_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_content_tags` engine =InnoDB;
alter table `ac_content_tags` collate = utf8mb4_unicode_ci;
alter table `ac_contents` engine =InnoDB;
alter table `ac_contents` collate = utf8mb4_unicode_ci;
alter table `ac_contents_to_stores` engine =InnoDB;
alter table `ac_contents_to_stores` collate = utf8mb4_unicode_ci;
alter table `ac_countries` engine =InnoDB;
alter table `ac_countries` collate = utf8mb4_unicode_ci;
alter table `ac_country_descriptions` engine =InnoDB;
alter table `ac_country_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_coupon_descriptions` engine =InnoDB;
alter table `ac_coupon_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_coupons` engine =InnoDB;
alter table `ac_coupons` collate = utf8mb4_unicode_ci;
alter table `ac_coupons_categories` engine =InnoDB;
alter table `ac_coupons_products` engine =InnoDB;
alter table `ac_coupons_products` collate = utf8mb4_unicode_ci;
alter table `ac_currencies` engine =InnoDB;
alter table `ac_currencies` collate = utf8mb4_unicode_ci;
alter table `ac_custom_blocks` engine =InnoDB;
alter table `ac_custom_blocks` collate = utf8mb4_unicode_ci;
alter table `ac_custom_lists` engine =InnoDB;
alter table `ac_custom_lists` collate = utf8mb4_unicode_ci;
alter table `ac_customer_groups` engine =InnoDB;
alter table `ac_customer_groups` collate = utf8mb4_unicode_ci;
alter table `ac_customer_notifications` engine =InnoDB;
alter table `ac_customer_notifications` charset = utf8mb4;
alter table `ac_customer_sessions` engine =InnoDB;
alter table `ac_customer_sessions` collate = utf8mb4_unicode_ci;
alter table `ac_customer_transactions` engine =InnoDB;
alter table `ac_customer_transactions` collate = utf8mb4_unicode_ci;
alter table `ac_customers` engine =InnoDB;
alter table `ac_customers` collate = utf8mb4_unicode_ci;
alter table `ac_dataset_column_properties` engine =InnoDB;
alter table `ac_dataset_column_properties` collate = utf8mb4_unicode_ci;
alter table `ac_dataset_definition` engine =InnoDB;
alter table `ac_dataset_definition` collate = utf8mb4_unicode_ci;
alter table `ac_dataset_properties` engine =InnoDB;
alter table `ac_dataset_properties` collate = utf8mb4_unicode_ci;
alter table `ac_dataset_values` engine =InnoDB;
alter table `ac_dataset_values` collate = utf8mb4_unicode_ci;
alter table `ac_datasets` modify dataset_name varchar(255) charset utf8mb3 not null;
alter table `ac_datasets` modify dataset_key varchar(255) charset utf8mb3 default '' null;
alter table `ac_datasets` engine =InnoDB;
alter table `ac_datasets` collate = utf8mb4_unicode_ci;
alter table `ac_download_attribute_values` engine =InnoDB;
alter table `ac_download_attribute_values` collate = utf8mb4_unicode_ci;
alter table `ac_download_descriptions` engine =InnoDB;
alter table `ac_download_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_downloads` modify activate_order_status_id varchar(255) default '' not null;
alter table `ac_downloads` engine =InnoDB;
alter table `ac_downloads` collate = utf8mb4_unicode_ci;
alter table `ac_email_templates` engine =InnoDB;
alter table `ac_email_templates` collate = utf8mb4_unicode_ci;
alter table `ac_encryption_keys` engine =InnoDB;
alter table `ac_encryption_keys` collate = utf8mb4_unicode_ci;
alter table `ac_extension_dependencies` engine =InnoDB;
alter table `ac_extension_dependencies` collate = utf8mb4_unicode_ci;
alter table `ac_extensions` engine =InnoDB;
alter table `ac_extensions` collate = utf8mb4_unicode_ci;
alter table `ac_field_descriptions` engine =InnoDB;
alter table `ac_field_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_field_values` engine =InnoDB;
alter table `ac_field_values` collate = utf8mb4_unicode_ci;
alter table `ac_fields` engine =InnoDB;
alter table `ac_fields` collate = utf8mb4_unicode_ci;
alter table `ac_fields_group_descriptions` engine =InnoDB;
alter table `ac_fields_group_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_fields_groups` engine =InnoDB;
alter table `ac_fields_groups` collate = utf8mb4_unicode_ci;
alter table `ac_fields_history` engine =InnoDB;
alter table `ac_fields_history` collate = utf8mb4_unicode_ci;
alter table `ac_form_descriptions` engine =InnoDB;
alter table `ac_form_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_form_groups` engine =InnoDB;
alter table `ac_form_groups` collate = utf8mb4_unicode_ci;
alter table `ac_forms` engine =InnoDB;
alter table `ac_forms` collate = utf8mb4_unicode_ci;
alter table `ac_global_attributes` engine =InnoDB;
alter table `ac_global_attributes` collate = utf8mb4_unicode_ci;
alter table `ac_global_attributes_descriptions` engine =InnoDB;
alter table `ac_global_attributes_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_global_attributes_groups` engine =InnoDB;
alter table `ac_global_attributes_groups` collate = utf8mb4_unicode_ci;
alter table `ac_global_attributes_groups_descriptions` engine =InnoDB;
alter table `ac_global_attributes_groups_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_global_attributes_type_descriptions` comment 'utf8mb4_unicode_ci';
alter table `ac_global_attributes_type_descriptions` engine =InnoDB;
alter table `ac_global_attributes_type_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_global_attributes_types` engine =InnoDB;
alter table `ac_global_attributes_types` collate = utf8mb4_unicode_ci;
alter table `ac_global_attributes_value_descriptions` engine =InnoDB;
alter table `ac_global_attributes_value_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_global_attributes_values` engine =InnoDB;
alter table `ac_global_attributes_values` collate = utf8mb4_unicode_ci;
drop index `ac_lang_definition_idx` on `ac_language_definitions`;
alter table `ac_language_definitions` engine =InnoDB;
alter table `ac_language_definitions` collate = utf8mb4_unicode_ci;
create index `ac_lang_definition_idx` on `ac_language_definitions` (language_value(500));
alter table `ac_languages` engine =InnoDB;
alter table `ac_languages` collate = utf8mb4_unicode_ci;
alter table `ac_layouts` engine =InnoDB;
alter table `ac_layouts` collate = utf8mb4_unicode_ci;
alter table `ac_length_class_descriptions` engine =InnoDB;
alter table `ac_length_class_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_length_classes` engine =InnoDB;
alter table `ac_length_classes` collate = utf8mb4_unicode_ci;
alter table `ac_locations` engine =InnoDB;
alter table `ac_locations` collate = utf8mb4_unicode_ci;
alter table `ac_manufacturers` engine =InnoDB;
alter table `ac_manufacturers` collate = utf8mb4_unicode_ci;
alter table `ac_manufacturers_to_stores` engine =InnoDB;
alter table `ac_manufacturers_to_stores` collate = utf8mb4_unicode_ci;
alter table `ac_messages` engine =InnoDB;
alter table `ac_messages` collate = utf8mb4_unicode_ci;

create table `ac_object_types`(
    id         int auto_increment,
    name       varchar(100) not null,
    related_to varchar(100) not null,
    primary key (id, name, related_to)
)
comment 'list of types for mapping data';

alter table `ac_online_customers` engine =InnoDB;
alter table `ac_online_customers` collate = utf8mb4_unicode_ci;
alter table `ac_order_data` engine =InnoDB;
alter table `ac_order_data` collate = utf8mb4_unicode_ci;
alter table `ac_order_data_types` engine =InnoDB;
alter table `ac_order_data_types` collate = utf8mb4_unicode_ci;
alter table `ac_order_downloads` engine =InnoDB;
alter table `ac_order_downloads` collate = utf8mb4_unicode_ci;
alter table `ac_order_downloads_history` engine =InnoDB;
alter table `ac_order_downloads_history` collate = utf8mb4_unicode_ci;
alter table `ac_order_history` engine =InnoDB;
alter table `ac_order_history` collate = utf8mb4_unicode_ci;
alter table `ac_order_options` engine =InnoDB;
alter table `ac_order_options` collate = utf8mb4_unicode_ci;
alter table `ac_order_product_stock_locations` engine =InnoDB;
alter table `ac_order_product_stock_locations` collate = utf8mb4_unicode_ci;
alter table `ac_order_products` engine =InnoDB;
alter table `ac_order_products` collate = utf8mb4_unicode_ci;
alter table `ac_order_status_ids` engine =InnoDB;
alter table `ac_order_status_ids` collate = utf8mb4_unicode_ci;
alter table `ac_order_statuses` engine =InnoDB;
alter table `ac_order_statuses` collate = utf8mb4_unicode_ci;
alter table `ac_order_totals` engine =InnoDB;
alter table `ac_order_totals` collate = utf8mb4_unicode_ci;
alter table `ac_orders` engine =InnoDB;
alter table `ac_orders` collate = utf8mb4_unicode_ci;
alter table `ac_page_descriptions` engine =InnoDB;
alter table `ac_page_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_pages` engine =InnoDB;
alter table `ac_pages` collate = utf8mb4_unicode_ci;
alter table `ac_pages_forms` engine =InnoDB;
alter table `ac_pages_forms` collate = utf8mb4_unicode_ci;
alter table `ac_pages_layouts` engine =InnoDB;
alter table `ac_pages_layouts` collate = utf8mb4_unicode_ci;
alter table `ac_product_descriptions` engine =InnoDB;
alter table `ac_product_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_product_discounts` engine =InnoDB;
alter table `ac_product_discounts` collate = utf8mb4_unicode_ci;
alter table `ac_product_filter_descriptions` engine =InnoDB;
alter table `ac_product_filter_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_product_filter_ranges` engine =InnoDB;
alter table `ac_product_filter_ranges` collate = utf8mb4_unicode_ci;
alter table `ac_product_filter_ranges_descriptions` engine =InnoDB;
alter table `ac_product_filter_ranges_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_product_filters` engine =InnoDB;
alter table `ac_product_filters` collate = utf8mb4_unicode_ci;
alter table `ac_product_option_descriptions` engine =InnoDB;
alter table `ac_product_option_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_product_option_value_descriptions` engine =InnoDB;
alter table `ac_product_option_value_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_product_option_values` engine =InnoDB;
alter table `ac_product_option_values` collate = utf8mb4_unicode_ci;
alter table `ac_product_option_values`
    add supplier_code varchar(100) null;
alter table `ac_product_option_values`
    add supplier_id varchar(100) null;
alter table `ac_product_option_values`
    add date_added timestamp default current_timestamp() null;
alter table `ac_product_option_values`
    add date_modified timestamp default current_timestamp() not null on update current_timestamp();
alter table `ac_product_option_values`
    add constraint `ac_product_option_values_supplier_idx`
        unique (supplier_id, supplier_code);
alter table `ac_product_options` engine =InnoDB;
alter table `ac_product_options` collate = utf8mb4_unicode_ci;
alter table `ac_product_specials` engine =InnoDB;
alter table `ac_product_specials` collate = utf8mb4_unicode_ci;
alter table `ac_product_stock_locations` engine =InnoDB;
alter table `ac_product_stock_locations` collate = utf8mb4_unicode_ci;
alter table `ac_product_stock_locations` add supplier_code varchar(100) null;
alter table `ac_product_stock_locations` add supplier_id varchar(100) null;
alter table `ac_product_tags` engine =InnoDB;
alter table `ac_product_tags` collate = utf8mb4_unicode_ci;
alter table `ac_products` engine =InnoDB;
alter table `ac_products` collate = utf8mb4_unicode_ci;
alter table `ac_products` add supplier_code varchar(100) null after call_to_order;
alter table `ac_products` add supplier_id varchar(100) null after supplier_code;
alter table `ac_products` add constraint `ac_product_stock_locations_supplier_idx`
        unique (supplier_code, supplier_id);
alter table `ac_products`
    add constraint `ac_products_supplier_idx`
        unique (supplier_code, supplier_id);
alter table `ac_products_featured` engine =InnoDB;
alter table `ac_products_featured` collate = utf8mb4_unicode_ci;
alter table `ac_products_related` engine =InnoDB;
alter table `ac_products_related` collate = utf8mb4_unicode_ci;
alter table `ac_products_to_categories` engine =InnoDB;
alter table `ac_products_to_categories` collate = utf8mb4_unicode_ci;
alter table `ac_products_to_downloads` engine =InnoDB;
alter table `ac_products_to_downloads` collate = utf8mb4_unicode_ci;
alter table `ac_products_to_stores` engine =InnoDB;
alter table `ac_products_to_stores` collate = utf8mb4_unicode_ci;
alter table `ac_resource_descriptions` engine =InnoDB;
alter table `ac_resource_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_resource_library` engine =InnoDB;
alter table `ac_resource_library` collate = utf8mb4_unicode_ci;
alter table `ac_resource_map` engine =InnoDB;
alter table `ac_resource_map` collate = utf8mb4_unicode_ci;
alter table `ac_resource_types` engine =InnoDB;
alter table `ac_resource_types` collate = utf8mb4_unicode_ci;
alter table `ac_reviews` engine =InnoDB;
alter table `ac_reviews` collate = utf8mb4_unicode_ci;
drop index `ac_settings_idx` on `ac_settings`;
alter table `ac_settings` engine =InnoDB;
alter table `ac_settings` collate = utf8mb4_unicode_ci;
create index `ac_settings_idx` on `ac_settings` (value(500));
alter table `ac_stock_statuses` engine =InnoDB;
alter table `ac_stock_statuses` collate = utf8mb4_unicode_ci;
alter table `ac_store_descriptions` engine =InnoDB;
alter table `ac_store_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_stores` engine =InnoDB;
alter table `ac_stores` collate = utf8mb4_unicode_ci;
create table `ac_supplier_data`
(
    id             int auto_increment,
    supplier_code  varchar(100)                          not null,
    object_type_id int                                   not null,
    object_id      int                                   not null,
    uid            varchar(255)                          not null comment 'unique id of object from supplier API',
    data           longtext collate utf8mb4_bin          not null comment 'json encoded data'
        check (json_valid(`data`)),
    date_added     timestamp default current_timestamp() not null,
    date_modified  timestamp default current_timestamp() not null on update current_timestamp(),
    primary key (id, supplier_code, object_type_id, object_id, uid)
);
create table `ac_suppliers`
(
    id            int auto_increment,
    code          varchar(100)                          not null,
    name          varchar(100)                          not null,
    date_added    timestamp default current_timestamp() not null,
    date_modified timestamp default current_timestamp() not null on update current_timestamp(),
    primary key (id, code)
);
alter table `ac_task_details` engine =InnoDB;
alter table `ac_task_details` collate = utf8mb4_unicode_ci;
alter table `ac_task_steps` engine =InnoDB;
alter table `ac_task_steps` collate = utf8mb4_unicode_ci;
alter table `ac_tasks` engine =InnoDB;
alter table `ac_tasks` collate = utf8mb4_unicode_ci;
alter table `ac_tax_class_descriptions` engine =InnoDB;
alter table `ac_tax_class_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_tax_classes` engine =InnoDB;
alter table `ac_tax_classes` collate = utf8mb4_unicode_ci;
alter table `ac_tax_rate_descriptions` engine =InnoDB;
alter table `ac_tax_rate_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_tax_rates` engine =InnoDB;
alter table `ac_tax_rates` collate = utf8mb4_unicode_ci;
alter table `ac_url_aliases` engine =InnoDB;
alter table `ac_url_aliases` collate = utf8mb4_unicode_ci;
alter table `ac_user_groups` engine =InnoDB;
alter table `ac_user_groups` collate = utf8mb4_unicode_ci;
alter table `ac_user_notifications` engine =InnoDB;
alter table `ac_user_notifications`
    charset = utf8mb4;
alter table `ac_user_sessions` engine =InnoDB;
alter table `ac_user_sessions` collate = utf8mb4_unicode_ci;
alter table `ac_users` engine =InnoDB;
alter table `ac_users` collate = utf8mb4_unicode_ci;
alter table `ac_weight_class_descriptions` engine =InnoDB;
alter table `ac_weight_class_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_weight_classes` engine =InnoDB;
alter table `ac_weight_classes` collate = utf8mb4_unicode_ci;
alter table `ac_zone_descriptions` engine =InnoDB;
alter table `ac_zone_descriptions` collate = utf8mb4_unicode_ci;
alter table `ac_zones` engine =InnoDB;
alter table `ac_zones` collate = utf8mb4_unicode_ci;
alter table `ac_zones_to_locations` engine =InnoDB;
alter table `ac_zones_to_locations` collate = utf8mb4_unicode_ci;






### CHECK IS NEEDED
#suppliers
create table `ac_suppliers`
(
    `id`            int auto_increment,
    `code`          varchar(100)                        not null,
    `name`          varchar(100)                        not null,
    `date_added`    timestamp default CURRENT_TIMESTAMP not null,
    `date_modified` timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint `ac_suppliers_pk`
        primary key (`id`, `code`)
);
create table `ac_object_types`
(
    `id`         int auto_increment,
    `name`       varchar(100) not null,
    `related_to` varchar(100)  not null,
    constraint `ac_object_types_pk`
        primary key (`id`, `name`, `related_to`)
)
    comment 'list of types for mapping data';
create table `ac_supplier_data`
(
    `id`             int auto_increment,
    `supplier_code`  varchar(100)                        not null, # doba etc
    `object_type_id` int                                 not null, #type if from object_types table (mean product, category, brand etc)
    `object_id`      int                                 not null, # product_id, category_id, manufacturer_id etc
    `uid`            varchar(255)                        not null comment 'unique id of object from supplier API',
    `data`           json                                not null comment 'json encoded data',
    `date_added`     timestamp default CURRENT_TIMESTAMP not null,
    `date_modified`  timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint `ac_supplier_data_pk`
        primary key (`id`, `supplier_code`, `object_type_id`, `object_id`, `uid`)
);
alter table `ac_products`
    add `supplier_code` varchar(100) null after `settings`,
    add `supplier_id` varchar(100) null after `supplier_code`;
create UNIQUE index `ac_products_supplier_idx`
    on `ac_products` (`supplier_code`, `supplier_id`);
ALTER TABLE `ac_product_stock_locations`
    add `supplier_code` varchar(100) null,
    add `supplier_id` varchar(100) null;
CREATE UNIQUE INDEX `ac_product_stock_locations_supplier_idx`
    on `ac_product_stock_locations` (`supplier_code`, `supplier_id`);
alter table `ac_product_option_values`
    add `supplier_code` varchar(100) null after `default`,
    add `supplier_id` varchar(100) null after `supplier_code`;
create unique index `ac_products_supplier_idx`
    on `ac_product_option_values` (`supplier_code`, `supplier_id`);
alter table `ac_categories`
    add `supplier_code` varchar(100) null after `status`,
    add `supplier_id` varchar(100) null after `supplier_code`;
create unique index `ac_categories_supplier_idx`
    on `ac_categories` (`supplier_code`, `supplier_id`);
