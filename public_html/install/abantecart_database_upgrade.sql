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
