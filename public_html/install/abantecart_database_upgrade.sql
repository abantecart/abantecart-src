alter table `ac_contents`
    add `author` varchar(128) COLLATE utf8_general_ci NOT NULL DEFAULT '',
    add `icon_rl_id` int(11),
    add `publish_date` timestamp NULL,
    add `expire_date` timestamp NULL,
    add `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    add `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

update `ac_contents` c set `publish_date` = (
    select `date_added` from `ac_content_descriptions` cd where c.content_id = cd.content_id limit 1
);

#???? Remove duplicate ac_contents entries. content_id is now unique

CREATE TABLE `ac_content_tags` (
   `content_id` int(11) NOT NULL,
   `tag` varchar(32) COLLATE utf8_general_ci NOT NULL COMMENT 'translatable',
   `language_id` int(11) NOT NULL,
   PRIMARY KEY  (`content_id`,`tag`,`language_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `ac_blocks` (`block_txt_id`, `controller`, `date_added`) VALUES
    ('new_content','blocks/new_content',NOW());

INSERT INTO `ac_block_templates` (`block_id`, `parent_block_id`, `template`, `date_added`) VALUES
    (LAST_INSERT_ID(), 3, 'blocks/new_content.tpl',NOW()),
    (LAST_INSERT_ID(), 6, 'blocks/new_content.tpl',NOW());

INSERT INTO `ac_blocks` (`block_txt_id`, `controller`, `date_added`) VALUES
    ('content_search', 'blocks/content_search', now());

INSERT INTO `ac_block_templates` (`block_id`, `parent_block_id`, `template`, `date_added`) VALUES
    (LAST_INSERT_ID(), 1, 'blocks/content_search.tpl', now()),
    (LAST_INSERT_ID(), 2, 'blocks/content_search.tpl', now()),
    (LAST_INSERT_ID(), 3, 'blocks/content_search.tpl', now()),
    (LAST_INSERT_ID(), 6, 'blocks/content_search.tpl', now());
#suppliers
create table `ac_suppliers`
(
    id            int auto_increment,
    code          varchar(100)                        not null,
    name          varchar(100)                        not null,
    date_added    timestamp default CURRENT_TIMESTAMP not null,
    date_modified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint `ac_suppliers_pk`
        primary key (id, code)
);
create table `ac_object_types`
(
    id         int auto_increment,
    name       varchar(100) not null,
    related_to varchar(100)  not null,
    constraint `ac_object_types_pk`
        primary key (id, name, related_to)
)
    comment 'list of types for mapping data';

create table `ac_supplier_data`
(
    id             int auto_increment,
    supplier_code  varchar(100)                        not null, # doba etc
    object_type_id int                                 not null, #type if from object_types table (mean product, category, brand etc)
    object_id      int                                 not null, # product_id, category_id, manufacturer_id etc
    uid            varchar(255)                        not null comment 'unique id of object from supplier API',
    data           json                                not null comment 'json encoded data',
    date_added     timestamp default CURRENT_TIMESTAMP not null,
    date_modified  timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    constraint `ac_supplier_data_pk`
        primary key (id, supplier_code, object_type_id, object_id, uid)
);

alter table `ac_products`
    add supplier_code varchar(100) null after settings,
    add supplier_id varchar(100) null after supplier_code;
create index `ac_products_supplier_idx`
    on `ac_products` (supplier_code, supplier_id);

alter table `ac_product_option_values`
    add supplier_code varchar(100) null after `default`,
    add supplier_id varchar(100) null after supplier_code;
create index `ac_products_supplier_idx`
    on `ac_product_option_values` (supplier_code, supplier_id);


alter table `ac_categories`
    add supplier_code varchar(100) null after `status`,
    add supplier_id varchar(100) null after supplier_code;
create index `ac_categories_supplier_idx`
    on `ac_categories` (supplier_code, supplier_id);