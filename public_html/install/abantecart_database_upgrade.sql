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
