alter table `ac_product_option_values`
add column IF NOT EXISTS require_shipping smallint default 0 not null comment 'depends on "shipping" column of table "products" ' after prefix;

alter table `ac_coupons`
    modify code varchar(255) not null;

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('checkout','config_unauth_customer',1);

alter table `ac_orders` add `ext_fields` json null;

alter table `ac_addresses`
    add `ext_fields` json null,
    add `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    add `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

alter table `ac_customers`
    add `ext_fields` json null after data;

alter table `ac_forms`
    add `locked` int(1) NOT NULL DEFAULT '0',
    add `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    add `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

alter table `ac_fields`
    add `resource_id` int(11) NULL,
    add `locked` int(1) NOT NULL DEFAULT '0',
    add `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    add `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

DROP TABLE IF EXISTS `ac_form_groups`;
rename table `ac_fields_groups` to `ac_field_groups`;

alter table `ac_field_groups`
    drop column field_id,
    drop column sort_order,
    modify group_id int auto_increment,
    add group_txt_id varchar(40) null after `group_id`;
alter table `ac_field_groups`
    auto_increment = 1;

alter table `ac_fields`
    add `group_id` int null after `form_id`;

alter table `ac_fields`
    add constraint `ac_field_group_fk`
        foreign key (`group_id`) references `ac_field_groups` (`group_id`)
            on delete set null;


rename table `ac_fields_group_descriptions` to `ac_field_group_descriptions`;

create table `ac_field_group_to_form`
(
    group_id   int    null,
    form_id    int    null,
    sort_order int(3) null,
    constraint `ac_field_group_to_form_fk`
        foreign key (form_id) references `ac_forms` (form_id)
            on update cascade on delete cascade,
    constraint `ac_field_group_to_group_fk`
        foreign key (group_id) references `ac_field_groups` (group_id)
            on update cascade on delete cascade
)engine = INNODB;


