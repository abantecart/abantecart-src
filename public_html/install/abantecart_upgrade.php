/*
*TODO: need to create script for debit/credit in customer transactions table
*/

/* Need to move this to upgrade.php */

INSERT INTO `ac_blocks` (`block_id`, `block_txt_id`, `controller`, `created`) VALUES
(29, 'account', 'blocks/account', now());

INSERT INTO `ac_block_templates` (`block_id`, `parent_block_id`, `template`, `created`) VALUES
(29, 3, 'blocks/account.tpl', now() ),
(29, 6, 'blocks/account.tpl', now() );

INSERT INTO `ac_pages` (`page_id`, `parent_page_id`, `controller`, `key_param`, `key_value`, `created`) VALUES
(NULL, 0, 'pages/account', '', '', now() );

SET @page_id = LAST_INSERT_ID();

INSERT INTO `ac_page_descriptions` (`page_id`, `language_id`, `name`, `title`, `seo_url`, `keywords`, `description`, `content`, `created`) VALUES
(@page_id, 1, 'Customer Account Pages', '', '', '', '', '', now() ),
(@page_id, 9, 'Cuenta Cliente Páginas', '', '', '', '', '', now() );

INSERT INTO `ac_layouts` (`layout_id`, `template_id`, `layout_type`, `layout_name`, `created`) VALUES
(NULL, 'default_html5', 1, 'Customer Account Pages', now());

SET @layout_id = LAST_INSERT_ID();


INSERT INTO `ac_pages_layouts` (`layout_id`, `page_id`) VALUES
(@layout_id, @page_id);

INSERT INTO `ac_block_layouts` (`instance_id`, `layout_id`, `block_id`, `custom_block_id`, `parent_instance_id`, `position`, `status`, `created`,`updated`) VALUES
(1900,18,5,0,0,50,1,NOW(),NOW()),
(1901,18,4,0,0,40,1,NOW(),NOW()),
(1902,18,3,0,0,30,0,NOW(),NOW()),
(1903,18,2,0,0,20,1,NOW(),NOW()),
(1904,18,5,0,0,50,1,NOW(),NOW()),
(1905,18,6,0,0,60,1,NOW(),NOW()),
(1906,18,7,0,0,70,1,NOW(),NOW()),
(1907,18,1,0,0,10,1,NOW(),NOW()),
(1908,18,8,0,0,80,1,NOW(),NOW()),
(1909,18,13,0,77,10,1,NOW(),NOW()),
(1910,18,14,0,77,20,1,NOW(),NOW()),
(1911,18,15,0,77,30,1,NOW(),NOW()),
(1912,18,24,0,87,20,1,NOW(),NOW()),
(1913,18,21,0,87,10,1,NOW(),NOW()),
(1914,18,16,0,79,10,1,NOW(),NOW()),
(1920,18,24,0,1908,70,1,NOW(),NOW()),
(1921,18,15,0,1907,70,1,NOW(),NOW()),
(1922,18,14,0,1907,60,1,NOW(),NOW()),
(1923,18,13,0,1907,50,1,NOW(),NOW()),
(1924,18,21,0,1908,80,1,NOW(),NOW()),
(1925,18,26,0,1907,40,1,NOW(),NOW()),
(1926,18,27,0,1907,30,1,NOW(),NOW()),
(1927,18,9,0,1903,10,1,NOW(),NOW()),
(1928,18,17,15,1907,80,1,NOW(),NOW()),
(1929,18,17,14,1908,20,1,NOW(),NOW()),
(1930,18,11,0,1908,50,1,NOW(),NOW()),
(1931,18,17,13,1908,10,1,NOW(),NOW()),
(1932,18,25,0,1908,40,1,NOW(),NOW()),
(1933,18,17,16,1908,30,1,NOW(),NOW()),
(1934,18,17,15,1908,60,1,NOW(),NOW()),
(1935,18,29,0,1905,10,1,NOW(),NOW());

