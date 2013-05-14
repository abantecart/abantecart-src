
ALTER TABLE `ac_global_attributes` ADD COLUMN `settings` text COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `required`;
ALTER TABLE `ac_fields` ADD COLUMN `settings` text COLLATE utf8_bin NOT NULL DEFAULT '' AFTER `required`;

ALTER TABLE `ac_url_aliases` ADD COLUMN `language_id` INT NOT NULL DEFAULT '1';
ALTER TABLE `ac_url_aliases` MODIFY COLUMN `keyword` varchar(255) NOT NULL COMMENT 'translatable';

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('general', 'config_product_default_sort_order', 'sort_order-ASC');

DROP TABLE IF EXISTS `ac_customer_transactions`;
CREATE TABLE `ac_customer_transactions` (
  `customer_transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL  COMMENT 'user_id for admin, customer_id for storefront section',
  `section` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - admin, 0 - customer',
  `credit` float DEFAULT '0',
  `debit` float DEFAULT '0',
  `transaction_type` varchar(255) NOT NULL DEFAULT '' COMMENT 'text type of transaction',
  `comment` text COMMENT 'comment for internal use',
  `description` text COMMENT 'text for customer',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `create_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`customer_transaction_id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1;

INSERT INTO `ac_extensions` (`type`, `key`, `category`, `status`, `priority`, `version`, `license_key`, `date_installed`, `update_date`, `create_date`) VALUES
('total', 'balance', '', 1, 1, '1.0', null, now(), now(), now() );
UPDATE `ac_extensions` SET `key` = 'handling' WHERE `key` = 'handling_fee';

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES
('balance', 'balance_status', '1'),
('balance', 'balance_sort_order', '5'),
('balance', 'balance_calculation_order', '5'),
('balance', 'balance_total_type', 'balance');

INSERT INTO `ac_countries` (`country_id`, `name`, `iso_code_2`, `iso_code_3`, `address_format`, `status`)
  VALUES	(240,'Northern Ireland','GB','NIR','',1);

INSERT INTO `ac_zones` (`zone_id`, `country_id`, `code`, `name`, `status`)
VALUES
(3949, 240, '', 'Antrim',1),
(3950, 240, '', 'Armagh',1),
(3951, 240, '', 'Down',1),
(3952, 240, '', 'Fermanagh',1),
(3953, 240, '', 'Derry',1),
(3954, 240, '', 'Tyrone',1)
;

--
--SUBMENU SYSTEM->DATA
--ITEM_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (7,'file_uploads',188);
--ITEM_TEXT
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (8,'text_file_uploads',188);
--ITEM_URL
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (9,'tool/files',188);
--PARENT_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (10,'data',188);
--SORT_ORDER
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_integer`,`row_id`)
VALUES  (11,5,188);
--ITEM_TYPE
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES  (12,'core',188);


--## ADD FILE UPLOADS DATASET
INSERT INTO `ac_datasets` (`dataset_name`,`dataset_key`) VALUES ('file_uploads','admin');
INSERT INTO `ac_dataset_properties` (`dataset_id`,`dataset_property_name`,`dataset_property_value`) VALUES ('5','controller','tool/files');

INSERT INTO `ac_dataset_definition` (`dataset_id`,`dataset_column_name`,`dataset_column_type`,`dataset_column_sort_order`)
VALUES  (5,'date_added','timestamp',1),
		(5,'name','varchar',2),
		(5,'type','varchar',3),
		(5,'section','varchar',4),
		(5,'section_id','integer',5),
		(5,'path','varchar',6);


-- Install default_html5 template


INSERT INTO `ac_pages` (`page_id`, `parent_page_id`, `controller`, `key_param`, `key_value`, `created`)
VALUES (10, 0, 'pages/index/maintenance', '', '', now() );

INSERT INTO `ac_page_descriptions` (`page_id`, `language_id`, `name`, `title`, `seo_url`, `keywords`, `description`, `content`, `created`) VALUES

(10, 1, 'Maintenance Page', '', '', '', '', '', now() ),
(10, 9, 'Mantenimiento de la página.', '', '', '', '', '', now() );



-- ??? Need solution for upgrade and layout ID conflicts
/*
INSERT INTO `ac_layouts` (`layout_id`, `template_id`, `layout_type`, `layout_name`, `created`) VALUES
(11,  'default_html5', 0, 'Default Page Layout', now() ),
(12,  'default_html5', 1, 'Home Page', now() ),
(13, 'default_html5', 1, 'Login Page', now() ),
(14, 'default_html5', 1, 'Default Product Page', now() ),
(15, 'default_html5', 1, 'Checkout Pages', now() ), 
(16, 'default_html5', 1, 'Product Listing Page', now() );*/

INSERT INTO `ac_pages_layouts` (`layout_id`, `page_id`) VALUES
(11, 1 ),
(12, 2 ),
(13, 4 ),
(14, 5 ),
(15, 3 );

INSERT INTO `ac_blocks` (`block_txt_id`, `controller`, `created`) VALUES
('newsletter_signup', 'blocks/newsletter_signup', now()), 
('search', 'blocks/search', now()),
('menu', 'blocks/menu', now(),
('breadcrumbs', 'blocks/breadcrumbs', now()
);

UPDATE `ac_block_templates` set template = 'blocks/html_block_footer.tpl' where `block_id` = 17 and `parent_block_id` = 8;
