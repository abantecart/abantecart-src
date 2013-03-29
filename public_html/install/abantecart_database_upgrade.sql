UPDATE `ac_extensions` SET `key`='handling' where `key`='handling_fee';

ALTER TABLE `ac_global_attributes` ADD COLUMN `settings` text COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `required`;
ALTER TABLE `ac_fields` ADD COLUMN `settings` text COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `required`;

ALTER TABLE `ac_url_aliases` ADD COLUMN `language_id` INT NOT NULL DEFAULT '1';
ALTER TABLE `ac_url_aliases` MODIFY COLUMN `keyword` varchar(255) NOT NULL COMMENT 'translatable';

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('general', 'config_product_default_sort_order', 'sort_order-ASC');

-- Install default_html5 template
-- ??? Need solution for upgrade and layout ID conflicts

INSERT INTO `ac_layouts` (`layout_id`, `template_id`, `layout_type`, `layout_name`, `created`) VALUES
(11,  'default_html5', 0, 'Default Page Layout', now() ),
(12,  'default_html5', 1, 'Home Page', now() ),
(13, 'default_html5', 1, 'Login Page', now() ),
(14, 'default_html5', 1, 'Default Product Page', now() ),
(15, 'default_html5', 1, 'Checkout Pages', now() ), 
(16, 'default_html5', 1, 'Product Listing Page', now() );

INSERT INTO `ac_pages_layouts` (`layout_id`, `page_id`) VALUES
(11, 1 ),
(12, 2 ),
(13, 4 ),
(14, 5 ),
(15, 3 );

INSERT INTO `ac_block_layouts` (`layout_id`, `block_id`, `custom_block_id`, `parent_instance_id`, `position`, `status`, `created`) VALUES
(11, 1, 0, 0, 10, 1, now() ),
(11, 2, 0, 0, 20, 1, now() ),
(11, 3, 0, 0, 30, 1, now() ),
(11, 4, 0, 0, 40, 1, now() ),
(11, 5, 0, 0, 50, 1, now() ),
(11, 6, 0, 0, 60, 1, now() ),
(11, 7, 0, 0, 70, 1, now() ),
(11, 8, 0, 0, 80, 1, now() ),
(11, 9, 0, 3, 10, 1, now() ),
(11, 10, 0, 3, 20, 1, now() ),
(11, 11, 0, 3, 30, 1, now() ),
(11, 9, 0, 6, 30, 1, now() ),
(11, 10, 0, 6, 10, 1, now() ),
(11, 11, 0, 6, 20, 1, now() ),
(11, 13, 0, 1, 10, 1, now() ),
(11, 14, 0, 1, 20, 1, now() ),
(11, 15, 0, 1, 30, 1, now() ),
(11, 21, 0, 8, 10, 1, now() ),
(11, 24, 0, 8, 20, 1, now() );

-- Home page
INSERT INTO `ac_block_layouts` (`layout_id`, `block_id`, `custom_block_id`, `parent_instance_id`, `position`, `status`, `created`) VALUES
(12, 1, 0, 0, 10, 1, now() ),
(12, 2, 0, 0, 20, 1, now() ),
(12, 3, 0, 0, 30, 1, now() ),
(12, 4, 0, 0, 40, 1, now() ),
(12, 5, 0, 0, 50, 1, now() ),
(12, 6, 0, 0, 60, 1, now() ),
(12, 7, 0, 0, 70, 1, now() ),
(12, 8, 0, 0, 80, 1, now() ),
(12, 9, 0, 18, 10, 1, now() ),
(12, 10, 0, 18, 20, 1, now() ),
(12, 11, 0, 18, 30, 1, now() ),
(12, 12, 0, 20, 10, 1, now() ),
(12, 18, 0, 21, 10, 1, now() ),
(12, 19, 0, 21, 20, 1, now() ),
(12, 15, 0, 16, 30, 1, now() ),
(12, 13, 0, 16, 10, 1, now() ),
(12, 14, 0, 16, 20, 1, now() ),
(12, 17, 1, 19, 10, 1, now() ),
(12, 21, 0, 23, 10, 1, now() ),
(12, 24, 0, 23, 20, 1, now() );

-- Login page
INSERT INTO `ac_block_layouts` (`layout_id`, `block_id`, `custom_block_id`, `parent_instance_id`, `position`, `status`, `created`) VALUES
(13, 13, 0, 55, 10, 1, now() ),
(13, 7, 0, 0, 70, 1, now() ),
(13, 6, 0, 0, 60, 0, now() ),
(13, 5, 0, 0, 50, 1, now() ),
(13, 4, 0, 0, 40, 1, now() ),
(13, 3, 0, 0, 30, 0, now() ),
(13, 2, 0, 0, 20, 1, now() ),
(13, 15, 0, 55, 30, 1, now() ),
(13, 14, 0, 55, 20, 1, now() ),
(13, 1, 0, 0, 10, 1, now() ),
(13, 8, 0, 0, 80, 1, now() ),
(13, 21, 0, 65, 10, 1, now() ),
(13, 24, 0, 65, 20, 1, now() );
-- Default Product page
INSERT INTO `ac_block_layouts` (`layout_id`, `block_id`, `custom_block_id`, `parent_instance_id`, `position`, `status`, `created`) VALUES
(14, 13, 0, 75, 10, 1, now() ),
(14, 7, 0, 0, 70, 1, now() ),
(14, 6, 0, 0, 60, 0, now() ),
(14, 5, 0, 0, 50, 1, now() ),
(14, 4, 0, 0, 40, 1, now() ),
(14, 3, 0, 0, 30, 0, now() ),
(14, 2, 0, 0, 20, 1, now() ),
(14, 15, 0, 75, 30, 1, now() ),
(14, 14, 0, 75, 20, 1, now() ),
(14, 1, 0, 0, 10, 1, now() ),
(14, 8, 0, 0, 80, 1, now() ),
(14, 21, 0, 76, 10, 1, now() ),
(14, 24, 0, 76, 20, 1, now() );
-- Checkout pages
INSERT INTO `ac_block_layouts` (`layout_id`, `block_id`, `custom_block_id`, `parent_instance_id`, `position`, `status`, `created`) VALUES
(15, 1, 0, 0, 10, 1, now() ),
(15, 7, 0, 0, 70, 1, now() ),
(15, 6, 0, 0, 60, 1, now() ),
(15, 5, 0, 0, 50, 1, now() ),
(15, 2, 0, 0, 20, 1, now() ),
(15, 3, 0, 0, 30, 0, now() ),
(15, 4, 0, 0, 40, 1, now() ),
(15, 5, 0, 0, 50, 1, now() ),
(15, 8, 0, 0, 80, 1, now() ),
(15, 13, 0, 77, 10, 1, now() ),
(15, 14, 0, 77, 20, 1, now() ),
(15, 15, 0, 77, 30, 1, now() ),
(15, 16, 0, 79, 10, 1, now() ),
(15, 21, 0, 87, 10, 1, now() ),
(15, 24, 0, 87, 20, 1, now() );

INSERT INTO `ac_block_templates` (`block_id`, `parent_block_id`, `template`, `created`) VALUES
(11, 1, 'blocks/content_header.tpl', now() ),
(11, 8, 'blocks/content_footer.tpl', now() ),
(9, 1, 'blocks/category_top.tpl', now() ),
(9, 2, 'blocks/category_top.tpl', now() );

UPDATE `ac_block_templates` set template = 'blocks/html_block_footer.tpl' where `block_id` = 17 and `parent_block_id` = 8;

