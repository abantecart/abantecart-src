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
#forms
create table `ac_field_groups`
(
    `group_id`     int,
    `group_txt_id` varchar(40) null
)
    collate = utf8mb4_unicode_ci;
alter table `ac_field_groups`
    modify `group_id` int auto_increment;

alter table `ac_field_groups`
    add constraint `ac_field_groups_pk`
        primary key (`group_id`);

create table ac_field_group_descriptions
(
    group_id    int          default 0  not null,
    name        varchar(255)            not null comment 'translatable',
    description varchar(255) default '' not null comment 'translatable',
    language_id int                     not null,
    primary key (group_id, language_id)
)
    collate = utf8mb4_unicode_ci;

create table `ac_field_group_to_form`
(
    `group_id`   int    null,
    `form_id`    int    null,
    `sort_order` int(3) null,
    constraint `ac_field_group_to_form_fk`
        foreign key (`form_id`) references `ac_forms` (`form_id`)
            on update cascade on delete cascade,
    constraint `ac_field_group_to_group_fk`
        foreign key (`group_id`) references `ac_field_groups` (`group_id`)
            on update cascade on delete cascade
);

alter table `ac_fields`
    add `group_id` int null after `form_id`,
    add `resource_id` int null,
    add `locked` int(1) default 0 not null,
    add `date_added` timestamp default current_timestamp() null,
    add `date_modified` timestamp default current_timestamp() not null on update current_timestamp(),
    add constraint `ac_field_group_fk`
        foreign key (`group_id`) references `ac_field_groups` (`group_id`)
            on delete set null;

delete from `ac_field_descriptions` where `field_id` not in (select `field_id` from `ac_fields`);
alter table `ac_field_descriptions`
    add constraint `ac_fields_fk`
        foreign key (`field_id`) references `ac_fields` (`field_id`)
            on update cascade on delete cascade;

drop table `ac_fields_group_descriptions`;
drop table `ac_fields_groups`;
drop table `ac_form_groups`;

alter table `ac_forms`
    add `locked` int(1) default 0 not null,
    add `date_added` timestamp default current_timestamp() null,
    add `date_modified` timestamp default current_timestamp() not null on update current_timestamp();

delete from `ac_form_descriptions` where `form_id` not in (select `form_id` from `ac_forms`);
alter table `ac_form_descriptions`
    add constraint `ac_form_descriptions_fk`
        foreign key (`form_id`) references `ac_forms` (`form_id`)
            on update cascade on delete cascade;
