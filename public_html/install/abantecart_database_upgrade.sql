alter table `ac_product_option_values`
add column IF NOT EXISTS require_shipping smallint default 0 not null comment 'depends on "shipping" column of table "products" ' after prefix;

alter table `ac_coupons`
    modify code varchar(255) not null;

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('checkout','config_unauth_customer',1);

alter table `ac_addresses`
    add `ext_fields` json null,
    add `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    add `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
