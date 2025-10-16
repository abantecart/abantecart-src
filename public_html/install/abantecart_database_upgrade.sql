alter table `ac_ant_messages`
    add `placeholder` varchar(128) null after `priority`;

alter table `ac_email_templates`
    modify subject varchar(255) collate utf8mb3_unicode_ci not null comment 'translatable';

alter table `ac_email_templates`
    modify html_body text collate utf8mb3_unicode_ci not null comment 'translatable';

alter table `ac_email_templates`
    modify text_body text collate utf8mb3_unicode_ci not null comment 'translatable';

alter table `ac_product_option_values`
add column IF NOT EXISTS require_shipping smallint default 0 not null comment 'depends on "shipping" column of table "products" ' after prefix;

alter table `ac_coupons`
    modify code varchar(255) not null;

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('checkout','config_unauth_customer',1);

alter table `ac_orders`
    modify `shipping_company` varchar(255) NULL,
    modify `payment_company` varchar(255) NULL,
    add `ext_fields` json null after `ip`,
    modify `payment_method_data` text NOT NULL DEFAULT '' after `payment_method_key`;

alter table `ac_addresses`
    modify `company` varchar(255) NULL,
    add `ext_fields` json null,
    add `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    add `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

alter table `ac_customers`
    add `ext_fields` json null after data;
#forms
DROP TABLE IF EXISTS `ac_field_groups`;
CREATE TABLE `ac_field_groups`
(
    `group_id` int(11) NOT NULL AUTO_INCREMENT,
    `group_txt_id` varchar(40) DEFAULT NULL,
    PRIMARY KEY (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ac_field_group_descriptions`;
CREATE TABLE `ac_field_group_descriptions`
(
    `group_id` int(11) NOT NULL DEFAULT 0,
    `name` varchar(255) NOT NULL COMMENT 'translatable',
    `description` varchar(255) NOT NULL DEFAULT '' COMMENT 'translatable',
    `language_id` int(11) NOT NULL,
    PRIMARY KEY (`group_id`,`language_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `ac_field_group_to_form`;
CREATE TABLE `ac_field_group_to_form`
(
  `group_id` int(11) DEFAULT NULL,
  `form_id` int(11) DEFAULT NULL,
  `sort_order` int(3) DEFAULT NULL,
  KEY `ac_field_group_to_form_fk` (`form_id`),
  KEY `ac_field_group_to_group_fk` (`group_id`),
  CONSTRAINT `ac_field_group_to_form_fk` FOREIGN KEY (`form_id`) REFERENCES `ac_forms` (`form_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ac_field_group_to_group_fk` FOREIGN KEY (`group_id`) REFERENCES `ac_field_groups` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

alter table `ac_global_attributes`
    modify attribute_parent_id int null,
    modify attribute_group_id int null;

UPDATE `ac_global_attributes` SET attribute_parent_id=NULL WHERE attribute_parent_id='0';
UPDATE `ac_global_attributes` SET attribute_group_id=NULL WHERE attribute_group_id='0';

alter table `ac_order_data_types` modify name varchar(64) default '' not null;
