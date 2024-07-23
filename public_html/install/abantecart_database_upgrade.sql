alter table `ac_global_attributes_values`
    add price_modifier float default 0.0 null after attribute_id;

alter table `ac_global_attributes_values`
    add txt_id varchar(255) null;

alter table `ac_global_attributes_values`
    add constraint ga_value_txt_id_idx
        unique (txt_id);
alter table `ac_product_option_values`
    add txt_id varchar(255) null after group_id;

alter table `ac_global_attributes_values`
    add price_prefix char(1) null after price_modifier;

alter table `ac_page_descriptions`
    alter column `date_added` set default (CURRENT_TIMESTAMP);

alter table `ac_order_data`
    alter column `date_added` set default (CURRENT_TIMESTAMP);

update `ac_settings`
SET `group` = 'appearance'
WHERE `group` = 'general'
    AND `key` IN (
                   'config_catalog_limit',
                   'config_bestseller_limit',
                   'config_featured_limit',
                   'config_latest_limit',
                   'config_special_limit'
                  );
INSERT INTO `ac_settings` (`group`, `key`, `value` )
VALUES ('appearance', 'viewed_products_limit', 3);

alter table `ac_block_layouts`
    modify date_added timestamp default CURRENT_TIMESTAMP null;

alter table `ac_block_layouts`
    modify date_modified timestamp default CURRENT_TIMESTAMP null on update CURRENT_TIMESTAMP;
alter table cba_block_descriptions
    modify date_added timestamp default CURRENT_TIMESTAMP null;

alter table `ac_block_descriptions`
    modify date_modified timestamp default CURRENT_TIMESTAMP null on update CURRENT_TIMESTAMP;

#replace group with "checkout"
UPDATE `ac_settings` SET `group` = 'checkout' WHERE `group`='fast_checkout';
DELETE FROM `ac_settings` WHERE `key` IN ('fast_checkout_store_id', 'fast_checkout_status', 'fast_checkout_layout', 'fast_checkout_priority','fast_checkout_sort_order');
DELETE FROM `ac_extensions` WHERE `key` = 'fast_checkout';

ALTER TABLE `ac_block_descriptions`
    modify `block_framed` tinyint(1) DEFAULT '0';