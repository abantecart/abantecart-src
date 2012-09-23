# move settings around for better location

UPDATE `ac_settings` SET `group` = 'details'
WHERE `key` like 'config_description_%';

UPDATE `ac_settings` SET `group` = 'details'
WHERE `key` IN ('store_name',
                'config_url', 
                'config_title', 
                'config_meta_description', 
                'config_owner', 
                'config_address', 
                'store_main_email', 
                'config_telephone', 
                'config_fax',
                'config_country_id',
                'config_zone_id', 
                'config_storefront_language', 
                'admin_language', 
                'config_currency', 
                'config_currency_auto', 
                'config_length_class', 
                'config_weight_class');
UPDATE `ac_settings` SET `group` = 'details', `key` = 'store_name'
WHERE `key` = 'config_name' AND `group` = 'custom_store';

UPDATE `ac_settings` SET `group` = 'general'
WHERE `key` IN (
                'config_catalog_limit',
                'config_admin_limit', 
                'config_bestseller_limit', 
                'config_featured_limit', 
                'config_latest_limit', 
                'config_special_limit', 
                'config_stock_display', 
                'config_stock_status_id', 
                'enable_reviews', 
                'config_download', 
                'config_download_status', 
                'config_help_links');

UPDATE `ac_settings` SET `group` = 'checkout'
WHERE `key` IN (
                'config_tax',
                'config_tax_store', 
                'config_tax_customer', 
                'starting_invoice_id', 
                'invoice_prefix', 
                'config_customer_group_id', 
                'config_customer_price', 
                'config_customer_approval', 
                'config_guest_checkout', 
                'config_account_id', 
                'config_checkout_id', 
                'config_stock_checkout', 
                'config_order_status_id', 
                'config_cart_weight', 
                'config_shipping_session', 
                'cart_ajax');

UPDATE `ac_settings` SET `group` = 'appearance'
WHERE `key` IN (
                'config_storefront_template',
                'storefront_width', 
                'admin_width', 
                'config_logo', 
                'config_icon', 
                'config_image_thumb_width', 
                'config_image_thumb_height', 
                'config_image_category_width', 
                'config_image_category_height', 
                'config_image_product_width', 
                'config_image_product_height', 
                'config_image_additional_width', 
                'config_image_additional_height', 
                'config_image_related_width', 
                'config_image_related_height', 
                'config_image_cart_width', 
                'config_image_cart_height', 
                'config_image_grid_width', 
                'config_image_grid_height', 
                'config_image_popup_width', 
                'config_image_popup_height');

UPDATE `ac_settings` SET `group` = 'api'
WHERE `key` IN ('config_storefront_api_status',
                'config_storefront_api_key', 
                'config_storefront_api_stock_check');

UPDATE `ac_settings` SET `group` = 'system'
WHERE `key` IN ('config_ssl',
                'config_session_ttl', 
                'config_maintenance', 
                'encryption_key', 
                'enable_seo_url', 
                'config_compression', 
                'config_cache_enable', 
                'config_upload_max_size', 
                'config_error_display', 
                'config_error_log', 
                'config_debug', 
                'config_debug_level', 
                'storefront_template_debug', 
                'config_error_filename');

INSERT INTO `ac_settings` VALUES ('', 0, 'api','config_storefront_api_stock_check','0');

INSERT INTO `ac_settings` VALUES ('', 0, 'checkout','config_cart_ajax','1');

INSERT INTO `ac_settings` VALUES ('', 0, 'general','config_nostock_autodisable','0');

INSERT INTO `ac_settings` VALUES ('', 0, 'general','config_show_tree_data','1');

ALTER TABLE `ac_stores` ADD COLUMN `alias` varchar(15) COLLATE utf8_bin NOT NULL;

ALTER TABLE `ac_stores` ADD COLUMN `status` int(1) NOT NULL;

INSERT INTO `ac_stores` (`store_id`,`name`,`alias`,`status`) VALUES ('0','default','default',1);
UPDATE `ac_stores` set `store_id`='0' WHERE `name` = 'default';

ALTER TABLE `ac_stores` DROP COLUMN	`url`;

ALTER TABLE `ac_stores` DROP COLUMN	`ssl`;

# populate aliases
UPDATE `ac_stores` SET alias = LOWER(SUBSTRING(name,0,15));

ALTER TABLE `ac_block_descriptions` ADD COLUMN `block_framed` tinyint(1) DEFAULT '1' AFTER `block_wrapper`;

ALTER TABLE `ac_product_option_values` ADD COLUMN `grouped_attribute_data` text DEFAULT NULL;

ALTER TABLE `ac_product_option_value_descriptions` ADD COLUMN `grouped_attribute_names` text COLLATE utf8_bin DEFAULT NULL;
ALTER TABLE `ac_order_totals` ADD COLUMN `type` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '';

INSERT INTO `ac_settings` VALUES ('', 0, 'coupon', 'coupon_total_type','discount');
INSERT INTO `ac_settings` VALUES ('', 0, 'total','total_total_type','total');
INSERT INTO `ac_settings` VALUES ('', 0, 'sub_total','sub_total_total_type','subtotal');
INSERT INTO `ac_settings` VALUES ('', 0, 'tax','tax_total_type','tax');
INSERT INTO `ac_settings` VALUES ('', 0, 'shipping','shipping_total_type','shipping');
INSERT INTO `ac_settings` VALUES ('', 0, 'handling','handling_total_type','fee');
INSERT INTO `ac_settings` VALUES ('', 0, 'low_order_fee','low_order_fee_total_type','fee');


DELETE FROM `ac_language_definitions`
WHERE `section`='1'
AND `block` IN ('english',
                'spanish',
                'catalog_attribute',
                'catalog_product',
                'common_header',
                'common_resource_library',
                'design_blocks',
                'extension_extensions',
                'localisation_tax_class',
                'sale_coupon',
                'setting_setting',
                'setting_store',
                'tool_error_log',
                'tool_global_search' );

DELETE FROM `ac_language_definitions`
WHERE `section`='0'
AND `block` IN ('english',
                'spanish',
                'default_pp_standart_default_pp_standart' );