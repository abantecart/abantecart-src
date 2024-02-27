alter table `ac_global_attributes_values`
    add price_modifier float default 0.0 null after attribute_id;

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