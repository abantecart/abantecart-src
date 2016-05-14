SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

INSERT INTO `ac_extensions` (`type`, `key`, `category`, `status`, `priority`, `version`, `license_key`, `date_installed`, `date_modified`, `date_added`)
VALUES ('payment', 'default_cod', 'payment', 1, 1, '1.0.1', null, now(), now(), now() );
INSERT INTO `ac_extensions` (`type`, `key`, `category`, `status`, `priority`, `version`, `license_key`, `date_installed`, `date_modified`, `date_added`)
VALUES ('shipping', 'default_flat_rate_shipping', 'shipping', 1, 1, '1.0.1', null, now(), now(), now() );

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_cod', 'default_cod_sort_order', '1');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_cod', 'default_cod_order_status_id', '1');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_cod', 'default_cod_status', '1');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_cod', 'default_cod_location_id', '0');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_cod', 'default_cod_autoselect', '1');

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_flat_rate_shipping', 'default_flat_rate_shipping_cost', '2');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_flat_rate_shipping', 'default_flat_rate_shipping_tax_class_id', '9');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_flat_rate_shipping', 'default_flat_rate_shipping_location_id', '0');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_flat_rate_shipping', 'default_flat_rate_shipping_status', '1');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_flat_rate_shipping', 'default_flat_rate_shipping_sort_order', '1');
INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES ('default_flat_rate_shipping', 'default_flat_rate_shipping_autoselect', '1');

--
-- Dumping data for table `addresses`
--

INSERT INTO `ac_addresses` VALUES (1,2,'','Juliana','Davis','9778 Golden Crescent','Apt 10','85804-7365','Humansville',223,3616);
INSERT INTO `ac_addresses` VALUES (2,3,'','Keely','Mccoy','5071 Misty Pond Farm','Suite #101','63406-9081','Bumble Bee',223,3648);
INSERT INTO `ac_addresses` VALUES (3,4,'BelfastCo','Zelda','Weiss','6944 Sleepy Fawn Abbey','Suite #31','86014-8121','Lawyers',223,3616);
INSERT INTO `ac_addresses` VALUES (4,5,'','Gloria','Macias','7590 Easy Robin Hollow','','73477-3842','Sandymush',223,3660);
INSERT INTO `ac_addresses` VALUES (5,6,'','Bernard','Horne','5607 Umber Branch Via','','86301-9785','Spook City',223,3616);
INSERT INTO `ac_addresses` VALUES (6,7,'','James','Curtis','6500 Arapahoe Road','','80303','Boulder',223,3634);
INSERT INTO `ac_addresses` VALUES (7,8,'','Bruce','Rosarini','61 Cumberland ST','','68624-2273','Skokie',223,3650);
INSERT INTO `ac_addresses` VALUES (8,9,'','Carlos','Compton','31 Capital Drive','','63142-0892','Fort Misery',223,3648);
INSERT INTO `ac_addresses` VALUES (9,10,'','Garrison','Baxter','Eddie Hoffman Highway','','64034-2948','Shell Pile',223,3648);
INSERT INTO `ac_addresses` VALUES (10,11,'','Anthony','Blair','104 Main Street','','29181-8284','Gassaway',223,3666);
INSERT INTO `ac_addresses` VALUES (11,12,'','Allen','Waters','110 Shenandoah Avenue','','86565-1710','Honohina',223,3616);
INSERT INTO `ac_addresses` VALUES (12,13,'','Tom','Kipling','100 Main Str','','64034-2948','Shell Pile',223,3648);

-- Dumping data for table `categories`
--

INSERT INTO `ac_categories` VALUES (46,43,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (47,43,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (38,36,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (40,36,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (41,36,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (42,36,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (43,0,2,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (44,43,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (45,43,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (39,36,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (36,0,1,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (37,36,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (48,43,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (49,0,3,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (50,49,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (51,49,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (52,0,98,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (53,52,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (54,52,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (58,0,4,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (59,58,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (60,58,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (61,58,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (62,58,0,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (63,58,0,1,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (64,0,99,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_categories` VALUES (65,0,100,1,'2015-06-12 10:03:16','2015-06-12 11:30:27');
INSERT INTO `ac_categories` VALUES (66,65,0,1,'2015-06-12 10:04:09','2015-06-12 11:28:05');
INSERT INTO `ac_categories` VALUES (67,65,0,1,'2015-06-12 10:06:44','2015-06-12 10:06:44');
INSERT INTO `ac_categories` VALUES (68,0,0,1,'2015-06-16 11:12:40','2015-06-16 11:12:40');
INSERT INTO `ac_categories` VALUES (69,68,0,1,'2015-06-16 11:13:35','2015-06-16 11:13:35');
INSERT INTO `ac_categories` VALUES (70,68,0,1,'2015-06-22 10:14:10','2015-06-22 10:14:10');
--
-- Dumping data for table `categories_to_stores`
--

INSERT INTO `ac_categories_to_stores` VALUES (36,0);
INSERT INTO `ac_categories_to_stores` VALUES (37,0);
INSERT INTO `ac_categories_to_stores` VALUES (38,0);
INSERT INTO `ac_categories_to_stores` VALUES (39,0);
INSERT INTO `ac_categories_to_stores` VALUES (40,0);
INSERT INTO `ac_categories_to_stores` VALUES (41,0);
INSERT INTO `ac_categories_to_stores` VALUES (42,0);
INSERT INTO `ac_categories_to_stores` VALUES (43,0);
INSERT INTO `ac_categories_to_stores` VALUES (44,0);
INSERT INTO `ac_categories_to_stores` VALUES (45,0);
INSERT INTO `ac_categories_to_stores` VALUES (46,0);
INSERT INTO `ac_categories_to_stores` VALUES (47,0);
INSERT INTO `ac_categories_to_stores` VALUES (48,0);
INSERT INTO `ac_categories_to_stores` VALUES (49,0);
INSERT INTO `ac_categories_to_stores` VALUES (50,0);
INSERT INTO `ac_categories_to_stores` VALUES (51,0);
INSERT INTO `ac_categories_to_stores` VALUES (52,0);
INSERT INTO `ac_categories_to_stores` VALUES (53,0);
INSERT INTO `ac_categories_to_stores` VALUES (54,0);
INSERT INTO `ac_categories_to_stores` VALUES (58,0);
INSERT INTO `ac_categories_to_stores` VALUES (59,0);
INSERT INTO `ac_categories_to_stores` VALUES (60,0);
INSERT INTO `ac_categories_to_stores` VALUES (61,0);
INSERT INTO `ac_categories_to_stores` VALUES (62,0);
INSERT INTO `ac_categories_to_stores` VALUES (63,0);
INSERT INTO `ac_categories_to_stores` VALUES (64,0);
INSERT INTO `ac_categories_to_stores` VALUES (65,0);
INSERT INTO `ac_categories_to_stores` VALUES (66,0);
INSERT INTO `ac_categories_to_stores` VALUES (67,0);
INSERT INTO `ac_categories_to_stores` VALUES (68,0);
INSERT INTO `ac_categories_to_stores` VALUES (69,0);
INSERT INTO `ac_categories_to_stores` VALUES (70,0);

--
-- Dumping data for table `category_descriptions`
--

INSERT INTO `ac_category_descriptions` VALUES (43,1,'Skincare','','','&lt;p&gt;\r\n	Products from award-winning skin care brands&lt;/p&gt;\r\n');
INSERT INTO `ac_category_descriptions` VALUES (41,1,'Lips','','','');
INSERT INTO `ac_category_descriptions` VALUES (42,1,'Nails','','','');
INSERT INTO `ac_category_descriptions` VALUES (38,1,'Face','','','');
INSERT INTO `ac_category_descriptions` VALUES (39,1,'Eyes','','','');
INSERT INTO `ac_category_descriptions` VALUES (36,1,'Makeup','Makeup','','&lt;p&gt;\r\n	All your makeup needs, from foundation to eye shadow in hundreds of different assortments and colors.&lt;/p&gt;\r\n');
INSERT INTO `ac_category_descriptions` VALUES (40,1,'Cheeks','','','');
INSERT INTO `ac_category_descriptions` VALUES (37,1,'Value Sets','value sets makeup','','');
INSERT INTO `ac_category_descriptions` VALUES (44,1,'Sun','','','');
INSERT INTO `ac_category_descriptions` VALUES (45,1,'Gift Ideas &amp; Sets','','','');
INSERT INTO `ac_category_descriptions` VALUES (46,1,'Face','','','&lt;p&gt;\r\n	Find face skin care solutions&lt;/p&gt;\r\n');
INSERT INTO `ac_category_descriptions` VALUES (47,1,'Eyes','','','');
INSERT INTO `ac_category_descriptions` VALUES (48,1,'Hands &amp; Nails','','','&lt;p&gt;\r\n	Keep your hands looking fresh&lt;/p&gt;\r\n');
INSERT INTO `ac_category_descriptions` VALUES (49,1,'Fragrance','','','&lt;p&gt;\r\n	Looking for a new scent? Check out our fragrance&lt;/p&gt;\r\n');
INSERT INTO `ac_category_descriptions` VALUES (50,1,'Women','','','&lt;p&gt;\r\n	Fragrance for Women&lt;/p&gt;\r\n');
INSERT INTO `ac_category_descriptions` VALUES (51,1,'Men','','','');
INSERT INTO `ac_category_descriptions` VALUES (52,1,'Hair Care','','','&lt;p&gt;\r\n	The widest range of premium hair products&lt;/p&gt;\r\n');
INSERT INTO `ac_category_descriptions` VALUES (53,1,'Shampoo','','','');
INSERT INTO `ac_category_descriptions` VALUES (54,1,'Conditioner','','','');
INSERT INTO `ac_category_descriptions` VALUES (58,1,'Men','','','');
INSERT INTO `ac_category_descriptions` VALUES (59,1,'Fragrance Sets','','','');
INSERT INTO `ac_category_descriptions` VALUES (60,1,'Skincare','','','');
INSERT INTO `ac_category_descriptions` VALUES (61,1,'Pre-Shave &amp; Shaving','','','');
INSERT INTO `ac_category_descriptions` VALUES (62,1,'Post-Shave &amp; Moisturizers','','','');
INSERT INTO `ac_category_descriptions` VALUES (63,1,'Body &amp; Shower','','','');
INSERT INTO `ac_category_descriptions` VALUES (64,1,'Bath &amp; Body','','','');
INSERT INTO `ac_category_descriptions` VALUES (65,1,'Books','','','Find the Items You&amp;#39;d Like to read');
INSERT INTO `ac_category_descriptions` VALUES (66,1,'Audio CD','','','');
INSERT INTO `ac_category_descriptions` VALUES (67,1,'Paperback','','','');
INSERT INTO `ac_category_descriptions` VALUES (68,1,'Apparel &amp; accessories','','','');
INSERT INTO `ac_category_descriptions` VALUES (69,1,'Shoes','','','');
INSERT INTO `ac_category_descriptions` VALUES (70,1,'T-shirts','','','Shop men&amp;#39;s T-shirts');


--
-- Dumping data for table `coupon_descriptions`
--

INSERT INTO `ac_coupon_descriptions` VALUES (4,1,'Coupon (-10%)','10% Discount');
INSERT INTO `ac_coupon_descriptions` VALUES (5,1,'Coupon (Free Shipping)','Free Shipping');
INSERT INTO `ac_coupon_descriptions` VALUES (6,1,'Coupon (-10.00)','Fixed Amount Discount');

--
-- Dumping data for table `coupons`
--

INSERT INTO `ac_coupons`
  (`coupon_id`,`code`,`type`,`discount`,`logged`,`shipping`,`total`,`date_start`,`date_end`,`uses_total`,`uses_customer`,`status`,`date_added`)
 VALUES
  (  4,  '2222',  'P',  10.0000,  0,  0,  0.0000,  '2015-01-01',  '2016-01-01',  10,  '10',  1,  NOW());

INSERT INTO `ac_coupons`
(`coupon_id`,`code`,`type`,`discount`,`logged`,`shipping`,`total`,`date_start`,`date_end`,`uses_total`,`uses_customer`,`status`,`date_added`)
VALUES
 (5,  '3333',  'P',  0.0000,  0,  1,  100.0000,  '2015-01-01',  '2016-01-01',  10,  '10',  1,  NOW());

INSERT INTO `ac_coupons`
(`coupon_id`,`code`,`type`,`discount`,`logged`,`shipping`,`total`,`date_start`,`date_end`,`uses_total`,`uses_customer`,`status`,`date_added`)
VALUES
 ( 6, '1111', 'F', 10.0000, 0, 0, 10.0000, '2015-01-01', '2016-01-01', 10, '10', 1, NOW());


--
-- Dumping data for table `coupons_products`
--

INSERT INTO `ac_coupons_products` VALUES (8,6,68);

--
-- Dumping data for table `block_layouts`
--
INSERT INTO `ac_block_layouts`
(`instance_id`,`layout_id`,`block_id`,`custom_block_id`,`parent_instance_id`,`position`,`status`,`date_added`,`date_modified`)
VALUES
(34,    2,  17, 1,  19,     10, 1,  NOW(),  NOW()),
(1836,	11,	17,	13,	337,	10,	1,	NOW(),	NOW()),
(1841,	11,	17,	15,	337,	60,	1,	NOW(),	NOW()),
(1838,	11,	17,	16,	337,	30,	1,	NOW(),	NOW()),
(1837,	11,	17,	14,	337,	20,	1,	NOW(),	NOW()),
(1834,	11,	17,	15,	330,	80,	1,	NOW(),	NOW()),
(1780,	12,	17,	15,	356,	60,	1,	NOW(),	NOW()),
(366,	12,	17,	1,	19,		10,	1,	NOW(),	NOW()),
(1774,	12,	20,	12,	353,	60,	1,	NOW(),	NOW()),
(1775,	12,	17,	13,	356,	10,	1,	NOW(),	NOW()),
(1765,	12,	17,	15,	349,	80,	1,	NOW(),	NOW()),
(1776,	12,	17,	14,	356,	20,	1,	NOW(),	NOW()),
(1767,	12,	23,	9,	350,	20,	1,	NOW(),	NOW()),
(1768,	12,	17,	10,	352,	10,	1,	NOW(),	NOW()),
(1773,	12,	23,	11,	353,	50,	1,	NOW(),	NOW()),
(1777,	12,	17,	16,	356,	30,	1,	NOW(),	NOW()),
(1808,	13,	17,	16,	379,	30,	1,	NOW(),	NOW()),
(1811,	13,	17,	15,	379,	60,	1,	NOW(),	NOW()),
(1807,	13,	17,	14,	379,	20,	1,	NOW(),	NOW()),
(1806,	13,	17,	13,	379,	10,	1,	NOW(),	NOW()),
(1804,	13,	17,	15,	378,	80,	1,	NOW(),	NOW()),
(1793,	14,	17,	16,	392,	30,	1,	NOW(),	NOW()),
(1792,	14,	17,	14,	392,	20,	1,	NOW(),	NOW()),
(1796,	14,	17,	15,	392,	60,	1,	NOW(),	NOW()),
(1791,	14,	17,	13,	392,	10,	1,	NOW(),	NOW()),
(1788,	14,	17,	15,	391,	80,	1,	NOW(),	NOW()),
(1819,	15,	17,	15,	395,	80,	1,	NOW(),	NOW()),
(1822,	15,	17,	14,	403,	20,	1,	NOW(),	NOW()),
(1821,	15,	17,	13,	403,	10,	1,	NOW(),	NOW()),
(1823,	15,	17,	16,	403,	30,	1,	NOW(),	NOW()),
(1826,	15,	17,	15,	403,	60,	1,	NOW(),	NOW()),
(1928,	18,	17,	15,	1907,	80,	1,	NOW(),	NOW()),
(1929,	18,	17,	14,	1908,	20,	1,	NOW(),	NOW()),
(1931,	18,	17,	13,	1908,	10,	1,	NOW(),	NOW()),
(1933,	18,	17,	16,	1908,	30,	1,	NOW(),	NOW()),
(1934,	18,	17,	15,	1908,	60,	1,	NOW(),	NOW()),
(2030,	19,	17,	15,	2017,	80,	1,	NOW(),	NOW()),
(2031,	19,	17,	14,	2019,	20,	1,	NOW(),	NOW()),
(2032,	19,	17,	13,	2019,	10,	1,	NOW(),	NOW()),
(2033,	19,	17,	16,	2019,	30,	1,	NOW(),	NOW()),
(2034,	19,	17,	15,	2019,	60,	1,	NOW(),	NOW());
--
-- Dumping data for table `custom_blocks`
--

INSERT INTO `ac_custom_blocks` VALUES (1,17,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (2,20,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (3,20,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (12,20,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (13,17,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (11,23,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (9,23,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (10,17,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (14,17,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (15,17,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_blocks` VALUES (16,17,'2015-06-12 09:56:24','2015-06-12 09:56:24');

--
-- Dumping data for table `block_descriptions`
--

INSERT INTO `ac_block_descriptions` VALUES (1,1,1,'0',0,'home page static banner','home page banner','','&lt;div style=&quot;text-align: center;&quot;&gt;&lt;a href=&quot;index.php?rt=product/special&quot;&gt; &lt;img alt=&quot;banner&quot; src=&quot;storefront/view/default/image/banner1.jpg&quot; /&gt; &lt;/a&gt;&lt;/div&gt;','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (2,2,1,'0',0,'Video block','Video','','a:3:{s:18:\"listing_datasource\";s:5:\"media\";s:13:\"resource_type\";s:5:\"video\";s:5:\"limit\";s:1:\"1\";}','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (3,3,1,'0',1,'Custom Listing block','Popular','','a:2:{s:18:\"listing_datasource\";s:34:\"catalog_product_getPopularProducts\";s:5:\"limit\";s:2:\"12\";}','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (30,16,1,'0',1,'Testimonials','Testimonials','Flexislider testimonials','&lt;div style=&quot;font-family: \'Open Sans\', sans-serif;&quot; class=&quot;flexslider&quot; id=&quot;testimonialsidebar&quot;&gt;\r\n	&lt;ul class=&quot;slides&quot;&gt;\r\n		&lt;li&gt;\r\n			&quot; I was working with many shopping carts, free and hosted for my clients. There is always something missing. In abantecart I find this gap to be much less. Interface is very easy to use and support is very responsive. This is considering its is free. Go abantecart go!&quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : TopShop on reviewcentre.com&lt;/span&gt;&lt;/li&gt;\r\n		&lt;li&gt;\r\n			&quot; Without a doubt the best cart I have used. The title says it all - abantecart is undoubtedly the best I have used. I\'m not an expert in site setup, so something this great looking and easy to use is absolutely perfect ... &quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : johnstenson80 on venturebeat.com&lt;/span&gt;&lt;/li&gt;\r\n		&lt;li&gt;\r\n			&quot; Will not regret using this cart. All good is already mentioned, I want to add my experience with support. My problems with some configuration were resolved quick. Faster than paid shopping cart we had before.&quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : shopper23 at bestshoppingcartreviews.com&lt;/span&gt;&lt;/li&gt;\r\n		&lt;li&gt;\r\n			&quot; Wow! Abante Cart is really a catch! What a nice experience it was for me. I mean, to have all these features so direct, so quick and easy was really essential for my website. I was able to add some features and a cart to my website in no time ...&quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : EcommerceSport at hotscripts.com&lt;/span&gt;&lt;/li&gt;\r\n		&lt;li&gt;\r\n			&quot; Love the cart. I installed it a while back and use it since when. Some features a hidden, but fun to discover them.&quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : Liz Wattkins at shopping-cart-reviews.com&lt;/span&gt;&lt;/li&gt;\r\n\r\n	&lt;/ul&gt;\r\n&lt;/div&gt;\r\n','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (28,15,1,'0',0,'Social Icons','Social Icons','This is a an HTML block to show social icons and link.\r\nNOTE: Need to edit HTML in block content to add  personal link to social media sites','      &lt;div class=&quot;social_icons&quot;&gt;\r\n        &lt;a href=&quot;http://www.facebook.com/AbanteCart&quot; target=&quot;_blank&quot; title=&quot;Facebook&quot; class=&quot;facebook&quot;&gt;Facebook&lt;/a&gt;\r\n        &lt;a href=&quot;https://twitter.com/abantecart&quot; target=&quot;_blank&quot; title=&quot;Twitter&quot; class=&quot;twitter&quot;&gt;Twitter&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; title=&quot;Linkedin&quot; class=&quot;linkedin&quot;&gt;Linkedin&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; title=&quot;rss&quot; class=&quot;rss&quot;&gt;rss&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; target=&quot;_blank&quot; title=&quot;Googleplus&quot; class=&quot;googleplus&quot;&gt;Googleplus&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; target=&quot;_blank&quot; title=&quot;Skype&quot; class=&quot;skype&quot;&gt;Skype&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; target=&quot;_blank&quot; title=&quot;Flickr&quot; class=&quot;flickr&quot;&gt;Flickr&lt;/a&gt;\r\n      &lt;/div&gt;\r\n','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (26,14,1,'0',1,'Contact us','Contact Us','','&lt;ul class=&quot;contact&quot;&gt;	&lt;li&gt;&lt;span class=&quot;phone&quot;&gt;&nbsp;&lt;/span&gt;+123 456 7890, +123 456 7890&lt;/li&gt;	&lt;li&gt;&lt;span class=&quot;mobile&quot;&gt;&nbsp;&lt;/span&gt;+123 456 7890, +123 456 78900&lt;/li&gt;	&lt;li&gt;&lt;span class=&quot;email&quot;&gt;&nbsp;&lt;/span&gt;help at abantecart.com&lt;/li&gt;	&lt;li&gt;&lt;span class=&quot;email&quot;&gt;&nbsp;&lt;/span&gt;help at abantecart.com&lt;/li&gt;&lt;/ul&gt;','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (24,13,1,'0',1,'About Us','About Us','','&lt;p&gt;\r\n	AbanteCart is a free eCommerce solution for merchants to provide ability creating online business and sell products or services online. AbanteCart application is built and supported by experienced enthusiasts that are passionate about their work and contribution to rapidly evolving eCommerce industry. AbanteCart is more than just a shopping cart, it is rapidly growing eCommerce platform with many benefits.&lt;/p&gt;\r\n','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (22,12,1,'blocks/listing_block/popular_brands_content_bottom.tpl',1,'Brands Scrolling List','Brands Scrolling List','','a:1:{s:18:\"listing_datasource\";s:20:\"custom_manufacturers\";}','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (20,11,1,'0',0,'Main Page Banner Bottom','Bottom Banners','','a:1:{s:17:\"banner_group_name\";s:19:\"Main bottom banners\";}','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (18,10,1,'0',0,'Main Page Promo','Promo','','	&lt;section class=&quot;row promo_section&quot;&gt;\r\n	&lt;div class=&quot;col-md-3 col-xs-6 promo_block&quot;&gt;\r\n		&lt;div class=&quot;promo_icon&quot;&gt;&lt;i class=&quot;fa fa-truck fa-fw&quot;&gt;&lt;/i&gt;&lt;/div&gt;\r\n		&lt;div class=&quot;promo_text&quot;&gt;\r\n			&lt;h2&gt;\r\n				Free shipping&lt;/h2&gt;\r\n			All over in world over $200\r\n		&lt;/div&gt;\r\n	&lt;/div&gt;\r\n	&lt;div class=&quot;col-md-3 col-xs-6 promo_block&quot;&gt;\r\n		&lt;div class=&quot;promo_icon&quot;&gt;&lt;i class=&quot;fa fa-money fa-fw&quot;&gt;&lt;/i&gt;&lt;/div&gt;\r\n		&lt;div class=&quot;promo_text&quot;&gt;\r\n			&lt;h2&gt;\r\n				Easy Payment&lt;/h2&gt;\r\n			Payment Gateway support&lt;/div&gt;\r\n	&lt;/div&gt;\r\n	&lt;div class=&quot;col-md-3 col-xs-6 promo_block&quot;&gt;\r\n		&lt;div class=&quot;promo_icon&quot;&gt;&lt;i class=&quot;fa fa-clock-o fa-fw&quot;&gt;&lt;/i&gt;&lt;/div&gt;\r\n		&lt;div class=&quot;promo_text&quot;&gt;\r\n			&lt;h2&gt;\r\n				24hrs Shipping&lt;/h2&gt;\r\n			For All US States&lt;/div&gt;\r\n	&lt;/div&gt;\r\n	&lt;div class=&quot;col-md-3 col-xs-6 promo_block&quot;&gt;\r\n		&lt;div class=&quot;promo_icon&quot;&gt;&lt;i class=&quot;fa fa-tags fa-fw&quot;&gt;&lt;/i&gt;&lt;/div&gt;\r\n		&lt;div class=&quot;promo_text&quot;&gt;\r\n			&lt;h2&gt;\r\n				Large Variety&lt;/h2&gt;\r\n			50,000+ Products&lt;/div&gt;\r\n	&lt;/div&gt;\r\n	&lt;/section&gt;','2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_block_descriptions` VALUES (16,9,1,'blocks/banner_block/one_by_one_slider_banner_block.tpl',0,'Main Page Banner Slider','Main Page Banner Slider','','a:1:{s:17:\"banner_group_name\";s:17:\"Main Page Banners\";}','2015-06-12 09:56:24','2015-06-12 09:56:24');

INSERT INTO `ac_custom_lists` VALUES (1,12,'manufacturer_id',12,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (2,12,'manufacturer_id',14,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (3,12,'manufacturer_id',13,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (4,12,'manufacturer_id',18,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (5,12,'manufacturer_id',19,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (6,12,'manufacturer_id',20,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (7,12,'manufacturer_id',15,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (8,12,'manufacturer_id',11,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (9,12,'manufacturer_id',17,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');
INSERT INTO `ac_custom_lists` VALUES (10,12,'manufacturer_id',16,0,'2015-06-12 09:56:24','2015-06-12 09:56:24');

--
-- Dumping data for table `customers`
--
INSERT INTO `ac_customers` VALUES
(2,0,'Juliana','Davis','julidavis@abantecart.com','julidavis@abantecart.com','(602) 141-7191','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,1,1,1,1,'109.104.166.98','2013-08-31 14:25:37','2015-06-12 09:56:24'),
(3,0,'Keely','Mccoy','keelymccoy@abantecart.com','keelymccoy@abantecart.com','(602) 916-1822','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,2,1,1,1,'109.104.166.98','2013-08-31 14:39:08','2015-06-12 09:56:24'),
(4,0,'Zelda','Weiss','zeldaweiss@abantecart.com','zeldaweiss@abantecart.com','(539) 838-9210','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,3,1,1,1,'109.104.166.138','2013-08-31 14:42:58','2015-06-12 09:56:24'),
(5,0,'Gloria','Macias','gloriamacias@abantecart.com','gloriamacias@abantecart.com','(573) 500-2105','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,4,1,1,1,'109.104.166.98','2013-08-31 14:46:58','2015-06-12 09:56:24'),
(6,0,'Bernard','Horne','bernardhorne@abantecart.com','bernardhorne@abantecart.com','(573) 500-2105','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,5,1,1,1,'109.104.166.138','2013-08-31 14:50:27','2015-06-12 09:56:24'),
(7,0,'James','Curtis','jamescurtis@abantecart.com','jamescurtis@abantecart.com','(602) 916-1822','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,6,1,1,1,'109.104.166.138','2013-08-31 15:00:03','2015-06-12 09:56:24'),
(8,0,'Bruce','Rosarini','brucerosarini@abantecart.com','brucerosarini@abantecart.com','(539) 838-9210','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,7,1,1,1,'109.104.166.98','2013-08-31 15:08:23','2015-06-12 09:56:24'),
(9,0,'Carlos','Compton','carloscmpton@abantecart.com','carloscmpton@abantecart.com','(928) 205-0511','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,8,1,1,1,'109.104.166.98','2013-08-31 15:13:14','2015-06-12 09:56:24'),
(10,0,'Garrison','Baxter','garrisonbaxter@abantecart.com','garrisonbaxter@abantecart.com','(803) 189-5001','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,9,1,1,1,'109.104.166.138','2013-09-01 12:51:47','2015-06-12 09:56:24'),
(11,0,'Anthony','Blair','anthonyblair@abantecart.com','anthonyblair@abantecart.com','(402) 456-6398','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,10,1,1,1,'171.98.12.12','2013-09-01 12:54:26','2015-06-12 09:56:24'),
(12,0,'Allen','Waters','allenwaters@abantecart.com','allenwaters@abantecart.com','(417) 280-7406','','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',NULL,0,11,1,1,1,'109.104.166.98','2013-09-01 13:12:56','2015-06-12 09:56:24'),
(13,0,'qqqqqq','qqqqqq','1@abantecart','1@abantecart','55 555 5555 5555','','','6b006ba67f3c172e146991a2ad46d865','a:1:{s:3:\"97.\";i:1;}',NULL,0,12,1,1,1,'109.104.166.98','2013-09-08 15:28:20','2015-06-12 09:56:24');

--
-- Dumping data for table `download_descriptions`
--

INSERT INTO `ac_download_descriptions` VALUES (1,1,'Audio CD1');
INSERT INTO `ac_download_descriptions` VALUES (2,1,'audio sample mp3');

--
-- Dumping data for table `downloads`
--

INSERT INTO `ac_downloads` VALUES (1,'download/18/76/4.mp3','audio-cd.mp3',50,NULL,0,'order_status',5,0,1,'2015-06-12 10:24:20','2015-06-12 10:24:20');
INSERT INTO `ac_downloads` VALUES (2,'download/18/76/5.mp3','sample.mp3',NULL,NULL,0,'before_order',0,0,1,'2015-06-12 10:32:17','2015-06-12 10:32:17');

--
-- Dumping data for table `global_attributes`
--

INSERT INTO `ac_global_attributes` VALUES (1,0,0,1,'S',1,1,'',1,NULL);
INSERT INTO `ac_global_attributes` VALUES (2,0,0,1,'C',0,0,'',1,NULL);
INSERT INTO `ac_global_attributes` VALUES (5,0,0,1,'G',1,1,'',1,NULL);
INSERT INTO `ac_global_attributes` VALUES (6,0,0,1,'S',0,0,'a:4:{s:10:\"extensions\";s:0:\"\";s:8:\"min_size\";s:0:\"\";s:8:\"max_size\";s:0:\"\";s:9:\"directory\";s:0:\"\";}',1,'');
INSERT INTO `ac_global_attributes` VALUES (7,6,0,1,'S',0,0,'a:4:{s:10:\"extensions\";s:0:\"\";s:8:\"min_size\";s:0:\"\";s:8:\"max_size\";s:0:\"\";s:9:\"directory\";s:0:\"\";}',1,'');
INSERT INTO `ac_global_attributes` VALUES (8,6,0,1,'S',0,0,'a:4:{s:10:\"extensions\";s:0:\"\";s:8:\"min_size\";s:0:\"\";s:8:\"max_size\";s:0:\"\";s:9:\"directory\";s:0:\"\";}',1,'');


--
-- Dumping data for table `global_attributes_descriptions`
--

INSERT INTO `ac_global_attributes_descriptions` VALUES (1,1,'Size','');
INSERT INTO `ac_global_attributes_descriptions` VALUES (2,1,'Gift Wrapping','');
INSERT INTO `ac_global_attributes_descriptions` VALUES (5,1,'Fragrance Type','');
INSERT INTO `ac_global_attributes_descriptions` VALUES (6,1,'Color&amp;Size','');
INSERT INTO `ac_global_attributes_descriptions` VALUES (7,1,'UK Size','');
INSERT INTO `ac_global_attributes_descriptions` VALUES (8,1,'Color','');

--
-- Dumping data for table `global_attributes_values`
--

INSERT INTO `ac_global_attributes_values` VALUES (53,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (52,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (51,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (50,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (49,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (48,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (47,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (46,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (45,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (32,2,0);
INSERT INTO `ac_global_attributes_values` VALUES (43,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (44,1,0);
INSERT INTO `ac_global_attributes_values` VALUES (76,5,0);
INSERT INTO `ac_global_attributes_values` VALUES (77,5,0);
INSERT INTO `ac_global_attributes_values` VALUES (75,5,0);
INSERT INTO `ac_global_attributes_values` VALUES (78,6,0);
INSERT INTO `ac_global_attributes_values` VALUES (79,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (80,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (81,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (82,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (83,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (84,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (85,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (86,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (87,7,0);
INSERT INTO `ac_global_attributes_values` VALUES (88,8,0);
INSERT INTO `ac_global_attributes_values` VALUES (89,8,0);
INSERT INTO `ac_global_attributes_values` VALUES (90,8,0);
INSERT INTO `ac_global_attributes_values` VALUES (91,8,0);
INSERT INTO `ac_global_attributes_values` VALUES (92,8,0);


INSERT INTO `ac_global_attributes_value_descriptions` VALUES (53,1,1,'1 oz');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (52,1,1,'75ml');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (51,1,1,'50ml');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (50,1,1,'30ml');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (49,1,1,'2.5 oz');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (48,1,1,'1.5 oz');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (47,1,1,'33.8 oz');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (46,1,1,'15.2 oz');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (45,1,1,'8.45 oz');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (32,2,1,'');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (42,1,1,'1.7 oz');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (43,1,1,'3.4 oz');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (44,1,1,'100ml');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (76,5,1,'Eau de Toilette');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (77,5,1,'Eau de Cologne');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (75,5,1,'Eau de Parfum');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (78,6,1,'');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (79,7,1,'UK 3');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (80,7,1,'UK 3.5');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (81,7,1,'UK 4');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (82,7,1,'UK 4.5');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (83,7,1,'UK 5');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (84,7,1,'UK 5.5');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (85,7,1,'UK 6');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (86,7,1,'UK 7');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (87,7,1,'UK 8');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (88,8,1,'Red');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (89,8,1,'White');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (90,8,1,'Black');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (91,8,1,'Blue');
INSERT INTO `ac_global_attributes_value_descriptions` VALUES (92,8,1,'Green');

--
-- Dumping data for table `manufacturers`
--

INSERT INTO `ac_manufacturers` VALUES (14,'Bvlgari',0);
INSERT INTO `ac_manufacturers` VALUES (13,'Calvin Klein',0);
INSERT INTO `ac_manufacturers` VALUES (12,'Benefit',0);
INSERT INTO `ac_manufacturers` VALUES (11,'M·A·C',0);
INSERT INTO `ac_manufacturers` VALUES (15,'Lancôme',0);
INSERT INTO `ac_manufacturers` VALUES (16,'Sephora',0);
INSERT INTO `ac_manufacturers` VALUES (17,'Pantene',0);
INSERT INTO `ac_manufacturers` VALUES (18,'Dove',0);
INSERT INTO `ac_manufacturers` VALUES (19,'Giorgio Armani',0);
INSERT INTO `ac_manufacturers` VALUES (20,'Gucci',0);


--
-- Dumping data for table `manufacturers_to_stores`
--

INSERT INTO `ac_manufacturers_to_stores` VALUES (11,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (12,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (13,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (14,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (15,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (16,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (17,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (18,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (19,0);
INSERT INTO `ac_manufacturers_to_stores` VALUES (20,0);

--
-- Dumping data for table `order_history`
--

INSERT INTO `ac_order_history` VALUES (1,1,1,1,'','0000-00-00 00:00:00','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (2,2,1,1,'','2013-09-07 08:02:31','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (3,3,1,1,'','2013-09-07 08:41:25','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (4,4,1,1,'','2013-09-07 08:51:07','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (5,5,1,1,'','2013-09-07 09:20:22','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (6,6,1,1,'','2013-09-07 09:21:56','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (7,7,1,1,'','2013-09-07 09:24:11','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (8,8,1,1,'','2013-09-07 09:36:21','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (9,9,1,1,'','2013-09-07 09:37:20','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (10,10,1,1,'','2013-09-07 09:39:30','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (11,11,1,1,'','2013-09-07 09:40:03','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (12,12,1,1,'','2012-03-15 14:04:06','2015-06-12 09:56:25');
INSERT INTO `ac_order_history` VALUES (13,13,1,1,'','2012-03-15 14:05:40','2015-06-12 09:56:25');

--
-- Dumping data for table `order_options`
--

INSERT INTO `ac_order_options`
		(`order_option_id`, `order_id`, `order_product_id`, `product_option_value_id`, `name`, `value`, `price`, `prefix`, `settings`)
VALUES
 (1,1,2,588,'Memory','8GB',99.0000,'+',NULL),
 (2,2,7,684,'Color','brown',10.0000,'+',NULL),
 (3,3,9,651,'Size','33.8 oz',49.0000,'+',NULL),
 (4,3,10,650,'Size','8 oz',19.0000,'+',NULL),
 (5,3,15,646,'Color','Brown',20.0000,'-',NULL),
 (6,4,16,613,'Color','Mandarin Sky',29.5000,'+',NULL),
 (7,4,18,664,'Fragrance Size','3.4 oz',84.0000,'+',NULL),
 (8,4,19,673,'Fragrance Size','6.7 oz',92.0000,'+',NULL),
 (9,4,21,661,'Fragrance Size','150ml',45.0000,'+',NULL),
 (10,5,23,627,'Color','Jade Fever',48.0000,'+',NULL),
 (11,5,24,626,'Color','Gris Fatale',48.0000,'+',NULL),
 (12,5,25,622,'Color','Shirelle',15.0000,'+',NULL),
 (13,5,26,619,'Color','Lacewood',27.0000,'+',NULL),
 (14,5,27,657,'Color','Light Bisque',30.5000,'+',NULL),
 (15,5,30,651,'Size','33.8 oz',49.0000,'+',NULL),
 (16,6,31,666,'Size','30 ml',30.0000,'+',NULL),
 (17,7,33,649,'Fragrance Size','1.7 oz',88.0000,'+',NULL),
 (18,7,34,660,'Fragrance Size','100ml',37.0000,'+',NULL),
 (19,8,35,646,'Color','Brown',20.0000,'-',NULL),
 (20,8,36,681,'Color','beige',10.0000,'+',NULL),
 (21,12,45,721,'Size','Eau de Toilette',78.5000,'$',NULL),
 (22,12,45,1,'Gift Wrapping','1',78.5000,'$',NULL),
 (23,12,47,738,'Size','30ml',90.0000,'$',NULL),
 (24,13,49,713,'Size','1.7 oz',72.0000,'$',NULL),
 (25,13,49,1,'Gift Wrapping','1',72.0000,'$',NULL);

INSERT INTO `ac_order_products` VALUES (6,2,97,'Eye Rejuvenating Serum','GRMBC004',126.0000,126.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (7,2,100,'Smooth silk lip pencils','GRMBC007',10.0000,40.0000,8.5000,4,0);
INSERT INTO `ac_order_products` VALUES (8,2,93,'Creme Precieuse Nuit 50ml','BVLG003',220.0000,220.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (9,3,69,'Seaweed Conditioner','SCND001',49.0000,49.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (10,3,69,'Seaweed Conditioner','SCND001',19.0000,19.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (11,3,77,'Men+Care Active Clean Shower Tool','DMBW0014',6.0000,6.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (12,3,98,'Shaving cream','GRMBC005',98.0000,98.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (13,3,62,'ck one shock for him Deodorant','601232',14.0000,14.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (14,3,66,'Total Moisture Facial Cream','556240',38.0000,38.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (15,3,54,'L\'EXTRÊME Instant Extensions Lengthening Mascara','74144',20.0000,20.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (16,4,57,'Delicate Oil-Free Powder Blush','117148',29.5000,29.5000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (17,4,67,'Flash Bronzer Body Gel','463686',29.0000,29.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (18,4,80,'Acqua Di Gio Pour Homme','GRM001',84.0000,84.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (19,4,89,'Secret Obsession Perfume','CK0012',92.0000,92.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (20,4,75,'Dove Men +Care Body Wash','DMBW0012',6.7000,6.7000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (21,4,78,'ck IN2U Eau De Toilette Spray for Him','Cl0001',45.0000,45.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (22,5,97,'Eye Rejuvenating Serum','GRMBC004',126.0000,126.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (23,5,61,'Color Design Eye Brightening All in One 5 Shadow &amp; Liner Palette','529071',48.0000,48.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (24,5,61,'Color Design Eye Brightening All in One 5 Shadow &amp; Liner Palette','529071',48.0000,96.0000,8.5000,2,0);
INSERT INTO `ac_order_products` VALUES (25,5,60,'Nail Lacquer','112423',15.0000,15.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (26,5,55,'LE ROUGE ABSOLU Reshaping &amp; Replenishing LipColour SPF 15','tw152236',27.0000,27.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (27,5,56,'Waterproof Protective Undereye Concealer','35190',30.5000,30.5000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (28,5,51,'BeneFit Girl Meets Pearl','483857',19.0000,19.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (29,5,93,'Creme Precieuse Nuit 50ml','BVLG003',220.0000,220.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (30,5,69,'Seaweed Conditioner','SCND001',49.0000,49.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (31,6,84,'Armani Code Pour Femme','GRM005',30.0000,30.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (32,6,92,'Body Cream by Bulgari','BVLG002',57.0000,171.0000,8.5000,3,0);
INSERT INTO `ac_order_products` VALUES (33,7,63,'Pour Homme Eau de Toilette','374622',88.0000,88.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (34,7,78,'ck IN2U Eau De Toilette Spray for Him','Cl0001',37.0000,74.0000,8.5000,2,0);
INSERT INTO `ac_order_products` VALUES (35,8,54,'L\'EXTRÊME Instant Extensions Lengthening Mascara','74144',20.0000,20.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (36,8,100,'Smooth silk lip pencils','GRMBC007',10.0000,40.0000,0.0000,4,0);
INSERT INTO `ac_order_products` VALUES (37,9,94,'Night Care Crema Nera Obsidian Mineral Complex','GRMBC001',263.0000,263.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (38,9,67,'Flash Bronzer Body Gel','463686',29.0000,29.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (39,9,91,'Jasmin Noir Body Lotion 6.8 fl oz','BVLG001',29.0000,58.0000,0.0000,2,0);
INSERT INTO `ac_order_products` VALUES (40,10,72,'Brunette expressions Conditioner','PCND002',24.0000,24.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (41,10,81,'Armani Eau de Toilette Spray ','GRM002',61.0000,61.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (42,10,88,'ck one Summer 3.4 oz','CK0011',27.0000,27.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (43,10,70,'Eau Parfumee au The Vert Shampoo','522823',31.0000,31.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (44,11,51,'BeneFit Girl Meets Pearl','483857',19.0000,19.0000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (45,12,105,'Bvlgari Aqua','PRF00273',78.5000,78.5000,0.0000,1,0);
INSERT INTO `ac_order_products` VALUES (46,12,65,'Absolue Eye Precious Cells','427847',105.0000,105.0000,8.5000,1,0);
INSERT INTO `ac_order_products` VALUES (47,12,110,'Flora By Gucci Eau Fraiche','PRF00278',90.0000,270.0000,8.5000,3,0);
INSERT INTO `ac_order_products` VALUES (48,12,95,'Skin Minerals For Men Cleansing Cream','GRMBC002',104.0000,0.0000,8.5000,0,0);
INSERT INTO `ac_order_products` VALUES (49,13,104,'Calvin Klein Obsession For Women EDP Spray','PRF00271',72.0000,576.0000,8.5000,8,0);

--
-- Dumping data for table `order_totals`
--

INSERT INTO `ac_order_totals` VALUES (1,1,'Sub-Total:','£1,583.44',1583.4400,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (2,1,'Flat Shipping Rate:','£2.00',2.0000,3,'shipping','shipping');
INSERT INTO `ac_order_totals` VALUES (3,1,'Total:','£1,585.44',1585.4400,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (4,2,'Sub-Total:','$386.00',386.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (5,2,'Retail 8.5%:','$32.81',32.8100,5,'tax','tax');
INSERT INTO `ac_order_totals` VALUES (6,2,'Total:','$418.81',418.8100,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (7,3,'Sub-Total:','$244.00',244.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (8,3,'Flat Shipping Rate:','$2.00',2.0000,3,'shipping','shipping');
INSERT INTO `ac_order_totals` VALUES (9,3,'Total:','$246.00',246.0000,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (10,4,'Sub-Total:','$286.20',286.2000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (11,4,'Retail 8.5%:','$24.33',24.3270,5,'tax','tax');
INSERT INTO `ac_order_totals` VALUES (12,4,'Total:','$310.53',310.5270,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (13,5,'Sub-Total:','$630.50',630.5000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (14,5,'Flat Shipping Rate:','$2.00',2.0000,3,'shipping','shipping');
INSERT INTO `ac_order_totals` VALUES (15,5,'Retail 8.5%:','$53.59',53.5925,5,'tax','tax');
INSERT INTO `ac_order_totals` VALUES (16,5,'Total:','$686.09',686.0925,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (17,6,'Sub-Total:','$201.00',201.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (18,6,'Retail 8.5%:','$17.09',17.0850,5,'tax','tax');
INSERT INTO `ac_order_totals` VALUES (19,6,'Total:','$218.09',218.0850,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (20,7,'Sub-Total:','$162.00',162.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (21,7,'Retail 8.5%:','$13.77',13.7700,5,'tax','tax');
INSERT INTO `ac_order_totals` VALUES (22,7,'Total:','$175.77',175.7700,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (23,8,'Sub-Total:','$60.00',60.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (24,8,'Flat Shipping Rate:','$2.00',2.0000,3,'shipping','shipping');
INSERT INTO `ac_order_totals` VALUES (25,8,'Total:','$62.00',62.0000,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (26,9,'Sub-Total:','$350.00',350.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (27,9,'Flat Shipping Rate:','$2.00',2.0000,3,'shipping','shipping');
INSERT INTO `ac_order_totals` VALUES (28,9,'Total:','$352.00',352.0000,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (29,10,'Sub-Total:','$143.00',143.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (30,10,'Retail 8.5%:','$12.16',12.1550,5,'tax','tax');
INSERT INTO `ac_order_totals` VALUES (31,10,'Total:','$155.16',155.1550,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (32,11,'Sub-Total:','$19.00',19.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (33,11,'Flat Shipping Rate:','$2.00',2.0000,3,'shipping','shipping');
INSERT INTO `ac_order_totals` VALUES (34,11,'Total:','$21.00',21.0000,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (35,12,'Sub-Total:','£289.42',453.5000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (36,12,'Flat Shipping Rate:','£1.28',2.0000,3,'shipping','shipping');
INSERT INTO `ac_order_totals` VALUES (37,12,'Retail 8.5%:','£20.34',31.8750,5,'tax','tax');
INSERT INTO `ac_order_totals` VALUES (38,12,'Total:','£311.04',487.3750,6,'total','total');
INSERT INTO `ac_order_totals` VALUES (39,13,'Sub-Total:','£367.60',576.0000,1,'subtotal','sub_total');
INSERT INTO `ac_order_totals` VALUES (40,13,'Flat Shipping Rate:','£1.28',2.0000,3,'shipping','shipping');
INSERT INTO `ac_order_totals` VALUES (41,13,'Retail 8.5%:','£31.25',48.9600,5,'tax','tax');
INSERT INTO `ac_order_totals` VALUES (42,13,'Total:','£400.13',626.9600,6,'total','total');

--
-- Dumping data for table `orders`
--


INSERT INTO `ac_orders` (
`order_id`,
`invoice_id`,
`invoice_prefix`,
`store_id`,
`store_name`,
`store_url`,
`customer_id`,
`customer_group_id`,
`firstname`,
`lastname`,
`telephone`,
`fax`,
`email`,
`shipping_firstname`,
`shipping_lastname`,
`shipping_company`,
`shipping_address_1`,
`shipping_address_2`,
`shipping_city`,
`shipping_postcode`,
`shipping_zone`,
`shipping_zone_id`,
`shipping_country`,
`shipping_country_id`,
`shipping_address_format`,
`shipping_method`,
`payment_firstname`,
`payment_lastname`,
`payment_company`,
`payment_address_1`,
`payment_address_2`,
`payment_city`,
`payment_postcode`,
`payment_zone`,
`payment_zone_id`,
`payment_country`,
`payment_country_id`,
`payment_address_format`,
`payment_method`,
`comment`,
`total`,
`order_status_id`,
`language_id`,
`currency_id`,
`currency`,
`value`,
`coupon_id`,
`date_added`,
`date_modified`,
`ip`,
`payment_method_data`
)
VALUES
(1,0,'',0,'Your Store','http://localhost/',1,1,'fdsfdsf','czx','(092) 222-2222','','demo@abantecart.com','fdsfdsf','czx','','addresss','','testing','55555','Maryland',3643,'United States',223,'{firstname} {lastname} {company} {address_1} {address_2} {city}, {zone} {postcode} {country}','Flat Shipping Rate','fdsfdsf','czx','','addresss','','testing','55555','Maryland',3643,'United States',223,'{firstname} {lastname} {company} {address_1} {address_2} {city}, {zone} {postcode} {country}','Cash On Delivery','','1585.4400',1,1,1,'GBP','1.00000000',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','127.0.0.1',''),
(2,0,'',0,'Web Store Name','demo',11,1,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','','','','','','','','',0,'',0,'','','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','418.8100',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(3,0,'',0,'Web Store Name','demo',5,1,'Gloria','Macias','+1 418-461-2440','','gloriamacias@abantecart.com','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Flat Shipping Rate','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Cash On Delivery','Please ASAP','246.0000',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(4,0,'',0,'Web Store Name','demo',5,1,'Gloria','Macias','+1 418-461-2440','','gloriamacias@abantecart.com','','','','','','','','',0,'',0,'','','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Cash On Delivery','','310.5270',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(5,0,'',0,'Web Store Name','demo',3,1,'Keely','Mccoy','+44 1324 483784 ','','keelymccoy@abantecart.com','Keely','Mccoy','','4842 Amet','','Grangemouth','','Gloucestershire',3552,'United Kingdom',222,'','Flat Shipping Rate','Keely','Mccoy','','4842 Amet','','Grangemouth','','Gloucestershire',3552,'United Kingdom',222,'','Cash On Delivery','','686.0925',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(6,0,'',0,'Web Store Name','demo',2,1,'Juliana','Davis','+44 1688 308321','','julidavis@abantecart.com','','','','','','','','',0,'',0,'','','Juliana','Davis','','Highlands and Islands PA75 6QE','','Isle of Mull','','Highlands',3559,'United Kingdom',222,'','Cash On Delivery','Bulgari','218.0850',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(7,0,'',0,'Web Store Name','demo',9,1,'Carlos','Compton','+1 867-874-22391','','carloscmpton@abantecart.com','','','','','','','','',0,'',0,'','','Carlos','Compton','','31 Capital Drive','','Hay River','','Nova Scotia',608,'Canada',38,'','Cash On Delivery','','175.7700',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(8,0,'',0,'Web Store Name','demo',8,1,'Bruce','Rosarini','+1 807-346-10763','','brucerosarini@abantecart.com','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','62.0000',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(9,0,'',0,'Web Store Name','demo',8,1,'Bruce','Rosarini','+1 807-346-10763','','brucerosarini@abantecart.com','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','352.0000',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(10,0,'',0,'Web Store Name','demo',12,1,'Allen','Waters','+1 540-985-59700','','allenwaters@abantecart.com','','','','','','','','',0,'',0,'','','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','155.1550',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(11,0,'',0,'Web Store Name','demo',12,1,'Allen','Waters','+1 540-985-59700','','allenwaters@abantecart.com','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','21.0000',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(12,0,'',0,'Web Store Name','demo',11,1,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','487.3750',2,1,3,'GBP','0.63820000',0,now(), now(),'171.98.12.12',''),
(13,0,'',0,'Web Store Name','demo',11,1,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','626.9600',1,1,3,'GBP','0.63820000',0,now(), now(),'171.98.12.12','');

--
-- Dumping data for table `order_downloads_history`
--

INSERT INTO `ac_order_downloads_history` VALUES (1,0,0,0,'download/18/76/5.mp3','sample.mp3',2,100,'2015-07-08 10:36:06');

--
-- Dumping data for table `product_descriptions`
--

INSERT INTO `ac_product_descriptions` VALUES (73,1,'Highlighting Expressions','','','&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Pro-V COLOUR HAIR SOLUTIONS Highlighting Expressions™ Conditioner protects and enhances colour treated hair and infuses blonde highlights with shine. The advanced Pro-Vitamin formula restores shine to dull highlights and protects hair from daily damage. This non-colour depositing formula works for all blonde highlights.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Pantene uncovered that oxidative colouring changes hair’s structure. Internally, the chemistry in hair colour attacks strength-giving proteins in hair and makes the fibre more porous, leading to weaker hair that fades more quickly. The surface of the hair fibres becomes uneven and the protective layer of the hair fibre disintegrates. Without the protective layer, the hair fibre is prone to micro-scarring and damage, which changes the way it interacts with light and leads to a dull appearance.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s non-colour depositing conditioner is designed to reinforce the structure of blonde highlighted hair and give it what it needs to reveal vibrant, glossy colour. Conditioning ingredients help revitalize and replenish highlighted hair while delivering brilliant shine and protecting from future damage. The result is healthy-looking hair rejuvenated with shimmering blonde highlights.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For rich, vibrant colour that’s shiny and healthy-looking, use with Pantene Pro-V Pantene COLOUR HAIR SOLUTIONS Highlighting Expressions™ Shampoo and Colour Nourishing Treatment.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (74,1,'Curls to straight Shampoo','','','&lt;p&gt;\r\n	YOUR HAIR’S STARTING POINT&lt;br /&gt;\r\n	Curly&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Curly Hair Series Curls to Straight Shampoo gently removes build-up, adding softness and control to your curls. The cleansing formula helps align and smooth the hair fibers. The result is healthy-looking hair that’s protected from frizz and ready for straight styling.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Research shows that each curly hair fibre grows in a unique pattern, twisting and turning in all directions. This unpredictable pattern makes it difficult to create and control straight styles. The curved fibres of curly hair intersect with each other more often than any other hair type, causing friction which can result in breakage. The curvature of the hair fibre also provides a large amount of volume in curly hair, which can be hard to tame.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s straight shampoo contains micro-smoothers that aid you in loosening and unwinding curls from their natural pattern. Curly hair is left ready for frizz controlled straight styling, and protected from styling damage.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For healthy-looking, curly hair that’s styled straight, use with Curls to Straight Conditioner and Anti-Frizz Straightening Crème.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (75,1,'Dove Men +Care Body Wash','','','&lt;p&gt;\r\n	A body and face wash developed for men\'s skin with Micromoisture technology.&lt;br /&gt;\r\n	Micromoisture activates on skin when lathering up, clinically proven to fight skin dryness.&lt;br /&gt;\r\n	Deep cleansing gel that rinses off easily. With purifying grains.&lt;br /&gt;\r\n	Dermatologist recommended.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (76,1,'Men+Care Clean Comfort Deodorant','','','&lt;p&gt;\r\n        The first scented deodorant from Dove® specifically designed with a non-irritating formula to give men the power of 48-hour protection against underarm odor with advanced ¼ moisturizer technology. The bottom line? It’s tough on odor, not on skin&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (77,1,'Men+Care Active Clean Shower Tool','tool, man','','&lt;p&gt;\r\n	Dove® Men+CareTM Active Clean Dual-Sided Shower Tool works with body wash for extra scrubbing power you can’t get from just using your hands. The mesh side delivers the perfect amount of thick cleansing lather, and the scrub side helps exfoliate for a deeper clean. Easy to grip and easy to hang. For best results, replace every 4-6 weeks.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (78,1,'ck IN2U Eau De Toilette Spray for Him','','','&lt;p&gt;\r\n	Fresh but warm; a tension that creates sexiness.Spontaneous - sexy - connectedCK IN2U him is a fresh woody oriental that penetrates with lime gin fizz and rushes into a combination of cool musks that radiate from top to bottom and leaves you wanting more.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (79,1,'ck One Gift Set','','','&lt;p&gt;\r\n	2 PC Gift Set includes 3.4 oz EDT Spray + Magnets. Ck One Cologne by Calvin Klein, Two bodies, two minds, and two souls are merged into the heat and passion of one. This erotic cologne combines man and woman with one provocative scent. This clean, refreshing fragrance has notes of bergamot, cardamom, pineapple, papaya, amber, and green tea.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (50,1,'Skinsheen Bronzer Stick','','','&lt;p&gt;\r\n	Bronzes, shapes and sculpts the face. Sheer-to-medium buildable coverage that looks naturally radiant and sunny. Stashable - and with its M·A·C Surf, Baby look – way cool. Limited edition.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (51,1,'BeneFit Girl Meets Pearl','','','&lt;p&gt;\r\n	Luxurious liquid pearl…the perfect accessory! This soft golden pink liquid pearl glides on for a breathtakingly luminous complexion. Customise your pearlessence with the easy to use twist up package … a few clicks for a subtle sheen, more clicks for a whoa! glow. Pat the luminous liquid over make up or wear alone for dewy lit from within radiance. It\'s pure pearly pleasure. Raspberry and chamomile for soothing. Light reflecting pigments for exquisite radiance. Sweet almond seed for firming and smoothing. Sesame seed oil for moisturising.Fresh red raspberry scent.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (52,1,'Benefit Bella Bamba','','','&lt;p&gt;\r\n	Amplify cheekbones and create the illusion of sculpted features with this 3D watermelon blush. Laced with shimmering gold undertones, bellabamba is taking eye popping pretty to the third dimension…you’ll never use traditional blush again! Tip: For a poreless complexion that pops, sweep bellabamba on cheeks after applying porefessional&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (53,1,'Tropiques Minerale Loose Bronzer','','','&lt;p&gt;\r\n	Precious earths, exclusively selected for their luxurious silky texture and gentle quality, are layered with mineral pigments in this lightweight powder to mimic the true color of tanned skin. Unique technology with inalterable earths ensures exquisite wear all day. Mineral blend smoothes complexion, while Aloe Vera helps protect skin from dryness.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (54,1,'L\'EXTRÊME Instant Extensions Lengthening Mascara','','','&lt;p&gt;\r\n	Extend your lashes up to 60% instantly! This exclusive Fibrestretch formula takes even the smallest natural lashes to dramatic lengths. The patented Extreme Lash brush attaches supple fibers to every eyelash for an instant lash extension effect.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (55,1,'LE ROUGE ABSOLU Reshaping &amp; Replenishing LipColour SPF 15','','','&lt;p&gt;\r\n	Smoother. Fuller. Absolutely replenished lips. This advanced lip color provides 6-hour care with continuous moisture and protective Vitamin E. Features plumping polymer and non-feathering color to define and reshape lips. Choose from an array of absolutely luxurious shades with a lustrous pearl or satin cream finish.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (56,1,'Waterproof Protective Undereye Concealer','','','&lt;p&gt;\r\n	This natural coverage concealer lets you instantly eliminate tell-tale signs of stress and fatigue. Provides complete, natural-looking coverage, evens skin tone, covers dark circles and minimizes fine lines around the eyes. The Result: A soft, matte finish&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (57,1,'Delicate Oil-Free Powder Blush','','','&lt;p&gt;\r\n	A sparkling shimmer of colour for a radiant glow. Silky soft, micro-bubble formula glides on easily and evenly. Lasts for hours. Oil-free and oil-absorbing, yet moisture-balancing. Perfect for all skin types.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (58,1,'&quot;hello flawless!&quot; custom powder foundation with SPF 15','','','&lt;p&gt;\r\n	There are degrees of cover-up…some like less, some like more! Our blendable powder formula with SPF 15 goes on beautifully sheer &amp;amp; builds easily for customized coverage. Sweep on with the accompanying brush for a sheer, natural finish or apply with the sponge for full coverage or spot cover-up. Our 6 flattering shades (2 light, 2 medium, 2 deep) make it incredibly easy to find your perfect shade. Once gals apply &quot;hello flawless!&quot; they\'ll finally have met their match q!&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (59,1,'Viva Glam Lipstick','','','&lt;p&gt;\r\n	Time to wham up the GLAM in VIVA GLAM! It\'s a gaga-glamorous look at our abiding passion: The M·A·C AIDS Fund and the VIVA GLAM program are the heart and soul of M·A·C Cosmetics. Ladies and gentlemen, we give you the sensational Cyndi Lauper and the electric Lady Gaga&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (60,1,'Nail Lacquer','','','&lt;p&gt;\r\n	Revolutionary new high gloss formula. Three long-wearing finishes - Cream, Sheer, and Frosted. Visibly different. Provides no-streak/no-chip finish. Contains conditioners and UV protection. Go hi-lacquer!&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (61,1,'Color Design Eye Brightening All in One 5 Shadow &amp; Liner Palette','','','&lt;p&gt;\r\n	Infinitely luminous. Sensationally smooth. All-in-one 5 shadow palette to brighten eyes. Lancome’s new versatile, all-in-one palette conveniently creates a full eye look for day or night. Experience the newest generation of luminosity as silky lustrous powders transparently wrap the skin, allowing a seamless layering of pure color for a silky sheen and radiant finish. Build with absolute precision and apply the shades in 5 simple steps (all over, lid, crease, highlighter and liner) to design your customized eye look. Contour, sculpt and lift in soft day colors or intensify with dramatic evening hues for smoldering smoky effects. Long wear, 8-hour formula. Color does not fade, continues to stay true&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (62,1,'ck one shock for him Deodorant','','','&lt;p&gt;\r\n	Shock Off! cK one shock for him opens with pure freshness, the heart pulses with spice and finishes with a masculine tobacco musk. Experience ck one shock, the newest fragrance from Calvin Klein with this 2.6 oz Deodorant.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (63,1,'Pour Homme Eau de Toilette','','','&lt;p&gt;\r\n	An intriguing masculine fragrance that fuses the bracing freshness of Darjeeling tea with the intensity of spice and musk. For those seeking a discreet accent to their personality.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (64,1,'Beauty Eau de Parfum','','','&lt;p&gt;\r\n	Beauty by Calvin Klein is a sophisticated and feminine fragrance presenting a new scructure to modern florals. Radiating rich and intense luminosity; Beauty leaves a complex and memorable impression. Experience the glamour and strength with the Beauty Eau de Parfum&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (65,1,'Absolue Eye Precious Cells','','','&lt;p&gt;\r\n	Smoothes – Tightens – Regenerates Radiance Exclusive innovation from Lancôme A powerful combination of unique ingredients – Reconstruction Complex and Pro-Xylane™ – has been shown to improve the condition around the stem cells, and stimulate cell regeneration to reconstruct skin to a denser quality*. Results Immediately, the eye contour appears smoother and more radiant. Day 7, signs of fatigue are minimized and the appearance of puffiness is reduced. Day 28, density is improved. Skin is soft and looks healthier. The youthful look of the eye contour is restored. Ophthalmologist – tested. Dermatologist – tested for safety.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (66,1,'Total Moisture Facial Cream','','','&lt;p&gt;\r\n	Say good-bye to dry skin and hello to “total moisture”. This facial cream provides concentrated immediate &amp;amp; long-term hydration for an ultra radiant complexion. Contains exclusive tri-radiance complex to help develop the skin’s reserves of water &amp;amp; reinforce skin’s moisture barrier for a radiantly refreshed complexion. For normal to dry skin.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (67,1,'Flash Bronzer Body Gel','','','&lt;p&gt;\r\n	Look irresistible! Discover the self-tanning results you dream of: Instant bronzed glowing body Enriched with natural caramel extract for an immediate, gorgeous, bronzed glow. Exquisitely beautiful tan The perfect balance of self-tanning ingredients helps to achieve an ideal color, providing an even, natural-looking, golden tan. Color development within 30 minutes, lasting up to 5 days. Transfer-resistant formula With an exclusive Color-Set™ complex that smoothes on without streaks, dries in 4 minutes and protects clothes against rub-off. Hydrating &amp;amp; smoothing action Leaves skin soft, smooth, and hydrated. Pure Vitamin E delivers antioxidant protection, helping to reduce signs of premature aging. Indulgent experience Delightfully scented with hints of jasmine and honey in a silky, non-greasy formula&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (68,1,'Absolute Anti-Age Spot Replenishing Unifying TreatmentSPF 15','','','&lt;p&gt;\r\n	A luxurious and comprehensive hand treatment that addresses the special needs of mature hands. Diminishes and discourages the appearance of age spots, while replenishing and protecting the skin. RESULT: Immediately, skin on hands is hydrated, soft and luminous. With continued use, skin becomes more uniform, looks firmer and youthful.Massage into hands and cuticles as needed.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (69,1,'Seaweed Conditioner','','','&lt;p&gt;\r\n	What it is:&lt;br /&gt;\r\n	A lightweight detangler made with marine seaweed and spirulina.&lt;/p&gt;\r\n&lt;p&gt;\r\n	What it does:&lt;br /&gt;\r\n	This conditioner gently detangles, nourishes, softens, and helps to manage freshly washed hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	What it is formulated WITHOUT:&lt;br /&gt;\r\n	- Parabens&lt;/p&gt;\r\n&lt;p&gt;\r\n	What else you need to know:&lt;br /&gt;\r\n	Made with marine greens for practically anyone (and ideal for frequent bathers), this conditioner is best paired with Seaweed Shampoo. It\'s also safe for use on color-treated hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	-Sea Silk Extract: Nourishes scalp; promotes healthy looking hair.&lt;br /&gt;\r\n	-Ascophyllum Nudosum (Seaweed) Extract: Moisturizes; adds elasticity, luster, softness, body; reduces flyaways.&lt;br /&gt;\r\n	-Macrocystis Pyrifera (Sea Kelp) Extract: Adds shine and manageability.&lt;br /&gt;\r\n	-Spirulina Maxima Extract: Hydrates.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (70,1,'Eau Parfumee au The Vert Shampoo','','','&lt;p&gt;\r\n	Structured around the refreshing vitality and purtiy of green tea, Bvlgari Eau the Vert Shampoo is an expression of elegance and personal indulgence. Delicately perfumed Eau Parfumée au thé vert shampoo gentle cleansing action makes it perfect for daily use.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (71,1,'Pantene Pro-V Conditioner, Classic Care','','','&lt;p&gt;\r\n	Conditions hair for healthy shine. How Can You See Healthy Hair? Pantene Complete Therapy Conditioner has a unique pro-vitamin complex that deeply infuses every strand - So you see 6 signs of health hair: Shine; Softness; Strength; Body; Less Frizz; Silkiness. Pantene Complete Therapy Conditioner: The ultimate pro-vitamin therapy provides gentle daily nourishing moisture for enhanced shine; Helps hair detangle easily; Helps prevent frizz and flyaways. Simply use and in just 10 days - and very day after - see shiny hair that\'s soft with less frizz. Best of all, healthy Pantene hair that is strong and more resistant to damage. Made in USA.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (72,1,'Brunette expressions Conditioner','','','&lt;p&gt;\r\n	YOUR HAIR’S STARTING POINT&lt;br /&gt;\r\n	Colour-Treated or Highlighted&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Pro-V COLOUR HAIR SOLUTIONS Brunette Expressions™ Conditioner hydrates hair for rich colour that is protected from daily stress and damage. This non-colour depositing formula works for all shades of brunette hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Pantene uncovered that oxidative colouring changes hair’s surface. Internally, the chemistry in hair colour attacks strength-giving proteins in hair and makes the fibre more porous, leading to weaker hair and that fades more quickly. The surface of the hair fibres then becomes uneven and the protective layer of the hair fibre disintegrates. Without the protective layer, the hair fibre is prone to micro-scarring and damage, which changes the way it interacts with light and leads to a dull appearance.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s advanced Pro-Vitamin formula enhances brunette colour for great intensity and radiant shine. Non-colour depositing conditioning ingredients enhance and protect colour treated hair, while helping to restore shine to coloured strands. Hair is moisturized and infused with radiant shine.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For rich, vibrant colour that’s shiny and healthy-looking, use with Pantene Pro-V Pantene COLOUR HAIR SOLUTIONS Brunette Expressions™ Shampoo and Colour Nourishing Treatment.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (80,1,'Acqua Di Gio Pour Homme','','','&lt;p&gt;\r\n	A resolutely masculine fragrance born from the sea, the sun, the earth, and the breeze of a Mediterranean island. Transparent, aromatic, and woody in nature Aqua Di Gio Pour Homme is a contemporary expression of masculinity, in an aura of marine notes, fruits, herbs, and woods.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Notes:&lt;br /&gt;\r\n	Marine Notes, Mandarin, Bergamot, Neroli, Persimmon, Rosemary, Nasturtium, Jasmine, Amber, Patchouli, Cistus.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Transparent, modern, and masculine.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (81,1,'Armani Eau de Toilette Spray ','','','&lt;p&gt;\r\n	This confidently masculine embodiment of the sophisticated ease and understated elegance of Giorgio Armani fashions - is a simply tailored, yet intensely sensual combination of sparkling fresh fruits, robust spices, and rich wood notes.&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Lime, Bergamot, Mandarin, Sweet Orange, Petitgrain, Cinnamon, Clove, Nutmeg, Jasmine, Neroli, Coriander, Lavender, Oakmoss, Sandalwood, Patchouli, Vetiver, Cedar.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Fresh, masculine, and discreet.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (82,1,'Armani Code after shave balm','','','&lt;p&gt;\r\n	Splash on this refreshing balm post-shave to soothe and calm the skin. Scents skin with a hint of seductive Code.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Ingredients&lt;br /&gt;\r\n	Alcohol, Aqua/Water/Eau, Parfum/Fragrance, PEG 8, PEG 60 Hydrogenated Castor Oil, BHT, Allantoin (Comfrey Root), Linalool, Geraniol, Alpha Isomethyl Ionone, Coumarin, Limonene, Hydroxyisohexl 3 Cyclohexene Carboxaldehyde, Hydroxycitronellal, Citronellol, Citral, Butylphenyl Methlyproprional, Hexylcinnamal&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (83,1,'Armani Code Sport','','','&lt;p&gt;\r\n	Sport. It\'s a rite of seduction. A vision of Giorgio Armani, translated into a fragrance.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	This scent opens with an explosive freshness that features spearmint, peppermint, and wild mint—surprising and unusual top notes with a stunning effect. The citrusy heart of the fragrance reveals Code Sport\'s seductive power. Notes of vetiver from Haiti reveal a woody and distinguished character, at once wet and dry. Like a crisp coating of ice, a note of hivernal prolongs the dialogue between the scent\'s cool crispness and sensual breath, giving the fragrance an almost unlimited life.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Spearmint, Peppermint, Wild Mint, Citrus, Hivernal, Hatian Vetiver, Nigerian Ginger.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Stunning. Cool. Seductive.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (84,1,'Armani Code Pour Femme','','','&lt;p&gt;\r\n	A seductive new fragrance for women, Armani Code Pour Femme is a fresh, sexy, feminine blend of zesty blood orange, ginger, and pear sorbet softened with hints of sambac jasmine, orange blossom, and lavender honey, warmed with precious woods and vanilla.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Blood Orange, Ginger, Pear Sorbet, Sambac Jasmine, Orange Blossom, Seringa Flower, Lavender Honey, Precious Woods Complex, Vanilla.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Fresh. Sexy. Feminine.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (85,1,'Forbidden euphoria Eau de Parfum Spray ','','','&lt;p&gt;\r\n	Possessing an innate confidence and sophistication, she is just starting to explore her sexuality. What she doesn\'t yet know is that she already is every man\'s fantasy.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A younger interpretation of Euphoria, this fruity floriental scent capitalizes on a modern, fresh sexiness with a mysterious twist. Its sparkling top notes seduce the senses with a blend of forbidden fruit such as mandarin, passion fruit, and iced raspberry. The heart blooms with a hypnotic bouquet of tiger orchid and jasmine. Underneath its exotic floralcy lies a layer of addictive patchouli and a sophisticated blend of musks and cashmere woods for an everlasting impression.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Sparkling Mandarin, Peach Blossom, Iced Raspberry, Pink Peony, Tiger Orchid, Jasmine, Cashmere Woods, Patchouli Absolute, Skin Musk.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Sophisticated. Confident. Forbidden.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (86,1,'Euphoria Men Intense Eau De Toilette Spray','','','&lt;p&gt;\r\n	Launched by the design house of Calvin Klein in 2008, EUPHORIA MEN INTENSE is a men\'s fragrance that possesses a blend of Rain Water, Pepper, Ginger, Sage, Frosted Sudachi, Cedar leaf, Patchouli, Myrrh, Labdanum, Amber Solid, Vetiver&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (87,1,'MAN Eau de Toilette Spray','','','&lt;p&gt;\r\n	Man by Calvin Klein was launched in October of 2007 and proposed as a new classic for the modern Calvin Klein man, aged from 25 to 40. The name itself is programmatic and unambiguous, like an English translation of L\'Homme by Yves Saint Laurent. Simple, brief, to the point. You are going to smell the essence of masculinity if you are to take your cue from the name of the fragrance. The packaging is sleek, modernist, with an architectural sense of proportions and looks good. The fragrance was created by perfumers Jacques Cavallier and Harry Fremont from Firmenich in collaboration with consultant Ann Gottlieb. All these people are old hands at marketing successful mainstream fragrances. Man offers therefore a mainstream palatability but without coming across as depersonalized. It plays the distinctiveness card, but in a well reined in manner. The fragrance bears a typical masculine fresh aromatic, woody and spicy signature around the linear heart of the scent which itself is dark, fruity, and sweet enough to feel feminine. This rich amber-fruity accord is made even more seductive thanks to just the right amount of citrus-y counterpoint, which never clarifies the scent but on the contrary helps to deepen the dark fruity sensation.&lt;br /&gt;\r\n	&nbsp;&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (88,1,'ck one Summer 3.4 oz','','','&lt;p&gt;\r\n	It\'s a concert on a hot summer night. The stage is set and the show\'s about to start. Feel the breeze, catch the vibe, and move to the beat with the pulsating energy of this limited-edition fragrance. A unisex scent, it is fresh, clean, and easy to wear. The fragrance opens with a burst of crisp melon. In the heart notes, an invigorating blend of green citrus and the zesty herbaceous effect of verbena creates a cool, edgy freshness. A base of exotic incense and earthy oakmoss is wrapped in the light, sensuous warmth of cedarwood, musk, and peach skin. Notes:Tangerine, Water Fern, Melon, Lemon, Sea Breeze Accord, Blue Freesia, Verbena, Rhubarb, Cedarwood, Skin Musk, Incense, Peach Skin. Style:Invigorating. Crisp. Cool.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (89,1,'Secret Obsession Perfume','','','&lt;p&gt;\r\n	Calvin Klein Secret Obsession eau de parfum spray for women blends notes of forbidden fruits, exotic flowers and a sultry wood signature to create an intoxicating aroma that is provocative and addictive.Calvin Klein is one of World of Shops most popular brands, and this Calvin Klein Secret Obsession eau de parfum spray for women is a firm favourite amongst our customers for its deep, feminine aroma that is perfect for those special evenings out.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (90,1,'Obsession Night Perfume','','','&lt;p&gt;\r\n	Launched by the design house of Calvin Klein in 2005, OBSESSION NIGHT is a women\'s fragrance that possesses a blend of gardenia, tonka bean, bergamot, vanilla, sandalwood, jasmine, rose, amber, muguet and mandarin. It is recommended for evening wear.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Ingredients&lt;br /&gt;\r\n	Notes: Bergamot, Bitter Orange, Mandarin, White Floral, Angelica Root, Gardenia, Rose, Muguet, Night-Blooming Jasmine, Vanilla, Tonka Bean, Amber, Labdanum, Sandalwood, Cashmere Wood&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (91,1,'Jasmin Noir Body Lotion 6.8 fl oz','','','&lt;p&gt;\r\n	A bath collection for the body, scented with the Jasmin Noir fragrance. A tribute to ultimate femininity. Seduction and personal indulgence.&lt;br /&gt;\r\n	Body Lotion Fragrance: The new emblematic creation within the Bvlgari Pour Femme Collection Jasmin Noir, perfectly embodies the luxury and prestige of Bvlgari fine jewelry.&lt;br /&gt;\r\n	Jasmin Noir is a flower of the imagination. Precious jasmine, white and immaculate, in its noire interpretation. A flower of pure mystery. A rich and delicate flower that at nightfall, reveals its intriguing sensuality. A precious floral woody fragrance with ambery accents centered around one of the true jewels of perfumery: the jasmin flower. A scent that conjures forth the bewildering seductiveness of feminity as elegant as it is profoundly sensual.&lt;br /&gt;\r\n	Jasmin Noir tells a voluptuous floral story that begins with the pure radiance of luminous light given by green and scintillating notes: Vegetal Sap and fresh Gardenia Petals. Then, tender and seductive, the Sambac Jasmine Absolute, delivers its generous and bewitching notes. Unexpectedly allied with a transparent silky almond accord, it reveals a heart that is light yet thoroughly exhilarating and marvelously addictive. The scent\'s sumptuously rich notes repose on a bed of Precious Wood and ambery undertones, bringing together the depth and mystery of Patchouli, the warmth of Tonka Bean and the comfort of silky Musks for an elegant and intimate sensuality.&lt;br /&gt;\r\n	An exquisite fragrance of incomparable prestige, Jasmin Noir captures the very essence of the jeweler.&lt;br /&gt;\r\n	Made in Italy&lt;br /&gt;\r\n	&nbsp;&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (92,1,'Body Cream by Bulgari','','','&lt;p&gt;\r\n	BVLGARI (Bulgari) by Bvlgari Body Cream 6.7 oz for Women Launched by the design house of Bvlgari in 1994, BVLGARI is classified as a refined, floral fragrance. This feminine scent possesses a blend of violet, orange blossom, and jasmine. Common spellings: Bulgari, Bvlgary, Bulgary.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (93,1,'Creme Precieuse Nuit 50ml','','','&lt;p&gt;\r\n	A luxurious, melting night cream to repair skin during sleep Features Polypeptides that boost production of collagen &amp;amp; elastin Improves skin elasticity &amp;amp; firmness Visibly reduces appearance of wrinkles, fine lines &amp;amp; brown spots Enriched with Bvlgari Gem Essence to restore radiance Skin appears smooth, energized &amp;amp; luminous in morning Perfect for all skin types&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (94,1,'Night Care Crema Nera Obsidian Mineral Complex','','','&lt;p&gt;\r\n	When it comes to body, skin or eye care, you want to look to our products and you will find the best there is. These are the most exceptional personal care products available. They meet the strictest standards for quality sourcing, environmental impact, results and safety. Our body care products truly allows you to be good to your whole body.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Giorgio Armani - Cream Nera - Night Care Crema Nera Obsidian Mineral Complex3 Restoring Cream SPF 15 50ml/1.69oz A lavish, fresh &amp;amp; weightless anti-aging creamProvides shielding &amp;amp; moisturizing benefitsDeveloped with Obsidian Mineral Complex technology Formulated with iron, silicon &amp;amp; perlite to create a potent dermal restructuring system Contains Pro-XylaneTM &amp;amp; Hyaluronique Acid Targets loss of substance, sagging of features &amp;amp; deepening of wrinkles Reveals firmer, sleeker &amp;amp; plumper skin in a youthful look. With a fabulous Skincare product like this one, you\'ll be sure to enjoy the ultimate in a Skincare experience with promising results.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (95,1,'Skin Minerals For Men Cleansing Cream','','','&lt;p&gt;\r\n	Ultra-purifying skincare enriched with essential moisturizing minerals, designed to instantly moisturize / purify the skin.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Sodium and Potassium System: to preserve cutaneous hydration.&lt;br /&gt;\r\n	- Salicylic Acid and Hamamelis Extract: to tighten the pores and tone skin.&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Self-assessment*:&lt;br /&gt;\r\n	- leaves the skin clean 100%&lt;br /&gt;\r\n	- leaves the skin comfortable 93%&lt;br /&gt;\r\n	- leaves the skin smooth 95%&lt;br /&gt;\r\n	- skin complexion is uniform 89%&lt;br /&gt;\r\n	- skin texture is refined 80%&lt;br /&gt;\r\n	* use test: 60 men 20 -65 years old 4 weeks of self-assessment&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (96,1,'Eye master','','','&lt;p&gt;\r\n	The volcanic force of minerals concentrated in multi action skincare specifically designed to target wrinkles, bags and dark circles of the delicate eye area. To combat signs of aging and fatigue and visibly improve skin quality.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Volcanic Complex: an innovative combination of energy charged minerals, inspired by volcanic rocks&lt;br /&gt;\r\n	- Caffeine extract: to fight puffiness&lt;br /&gt;\r\n	- Conker and butcher’s broom extracts to stimulate cutaneous blood micro-circulation&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Proven immediate anti-puffiness action*:&lt;br /&gt;\r\n	- 15 minutes after application –19%&lt;br /&gt;\r\n	*instrumental test, 40 men, 42-65 years old&lt;br /&gt;\r\n	Self-assessment&lt;br /&gt;\r\n	- instantly revitalizes skin 77%**&lt;br /&gt;\r\n	- wrinkles appear reduced 78%***&lt;br /&gt;\r\n	- diminishes the appearance of dark circles 68%***&lt;br /&gt;\r\n	** use test, 40 men 42-65 years old, single application, self-assessment&lt;br /&gt;\r\n	*** use test, 40 men 42-65 years old, 4 weeks, self-assessment&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (97,1,'Eye Rejuvenating Serum','','','&lt;p&gt;\r\n	The first advanced rejuvenating ‘weapon’ thanks to a corrective and smoothing texture and a power amplifying applicator.&lt;br /&gt;\r\n	The alliance of the [3.R] technology combined with an intensive re-smoothing system.&lt;br /&gt;\r\n	The eye rejuvenation serum also comes in an easily portable tube that boasts a silver bevelled applicator to ensure a good delivery of the product to the eye area as well as offering a means to improve circulation and reduce puffiness and eye bags.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Rarely women have been so convinced of its efficiency on skin rejuvenation:&lt;/p&gt;\r\n&lt;p&gt;\r\n	EYE CONTOUR LOOKS SMOOTHER 85%*&lt;br /&gt;\r\n	EYES LOOK YOUNGER 91%*&lt;br /&gt;\r\n	EYE PUFFINESS LOOKS SOFTENED 83%*&lt;/p&gt;\r\n&lt;p&gt;\r\n	*% of women – self assessment on 60 women after 4 weeks&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (98,1,'Shaving cream','','','&lt;p&gt;\r\n	Moisturizing, charged with minerals and enriched with ultra softening agents. Its specific formula ensures an optimal, extremely gentle shave. Even four hours after shaving, the skin remains hydrated, soft and supple.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Sodium and Potassium System: to preserve cutaneous hydration&lt;br /&gt;\r\n	- Bisabolol: to soothe skin&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Measurements 4 hours after shaving:&lt;br /&gt;\r\n	- skin hydration +29%*&lt;br /&gt;\r\n	- skin softness +61%**&lt;br /&gt;\r\n	- skin suppleness +18%**&lt;br /&gt;\r\n	- skin dryness -39%**&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;br /&gt;\r\n	* instrumental test, 20 men, 20-70 years old&lt;br /&gt;\r\n	** clinical scorage, 20 men, 20-70 years old&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (99,1,'Fluid shine nail polish','','','&lt;p&gt;\r\n	Luxurious color at your fingertips. Fluid shine coats nails with intense shine and long-lasting, sophisticated color. The essential accessory to any makeup wardrobe.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Discover the Bronze collection 2010&lt;br /&gt;\r\n	Finish this season’s high summer look with a cranberry n°43 or blackberry n°44 nail, to echo the wet lips with intense color.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (100,1,'Smooth silk lip pencils','','','&lt;p&gt;\r\n	An incredibly soft lip pencil for subtle, precise definition. The silky texture allows for easy application and flawless results. To extend the hold of your lip color, fill lips in completely with Smooth silk lip pencil before applying your lipstick. Choose from a wide range of shades to complement every color in your lipstick wardrobe.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (101,1,'Pro-V Color Hair Solutions Color Preserve Shine Conditioner','pantene, shampoo','','&lt;p&gt;\r\n	PANTENE\'s color preserve shine shampoo and conditioner system with micro-polishers smoothes and refinishes the hair’s outer layer. So your hair reflects light and shines brilliantly. Help preserve your multi-dimensional color.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Product Features&lt;/strong&gt;&lt;br /&gt;\r\n	Micro-polishers smooth the outer layer of hair to help Protect color and leave hair shiny&lt;br /&gt;\r\n	Lightweight moisturizers provide protection against damage&lt;br /&gt;\r\n	Designed for color-treated hair; Gentle enough for permed hair&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Ingredients&lt;/strong&gt;&lt;br /&gt;\r\n	Water, Stearyl Alcohol, Behentrimonium Methosulfate, Cetyl Alcohol, Fragrance, Bis-Aminopropyl Dimethicone, Isopropyl Alcohol, Benzyl Alcohol, Disodium EDTA, Panthenol, Panthenyl Ethyl Ether, Methylchloroisothiazolinone, Methylisothiazolinone&lt;/p&gt;\r\n&lt;p&gt;\r\n	&nbsp;&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (102,1,'Gucci Guilty','gicci, spray','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		Notes Consist Of Mandarin, Pink Pepper, Peach, Lilac, Geranium, Amber And Patchouli&lt;/li&gt;\r\n	&lt;li&gt;\r\n		For Casual Use&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;Gucci Guilty&lt;/em&gt; is a warm yet striking oriental floral with hedonism at its heart.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The scent seizes the attention with a flamboyant opening born of the natural rush that is mandarin shimmering alongside an audacious fist of pink pepper.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The middle notes are an alluring concoction of heady lilac and geranium, laced with the succulent tactility of peach - all velvet femininity with a beguiling hint of provocation.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The patchouli that is the hallmark of Gucci fragrances here conveys a message of strength, while the voluptuousness of amber suggests deep femininity.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (103,1,'Jasmin Noir L\'Essence Eau de Parfum Spray 75ml','','','&lt;p&gt;\r\n	A carnal impression of the immaculate jasmine flower, Bvlgari Jasmin Noir L\'Essence dresses the purity of the bloom in jet black mystery.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The fragrance is a more concentrated Eau de Parfum than the original Jasmin Noir, a blend of rare and precious ingredients that are more seductive, and more addictive than ever before. The profoundly sensual elixir captivates the senses, and enchants its wearer with its generous and bewitching touches.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A luminous bottle that honours the heritage of Bvlgari.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (104,1,'Calvin Klein Obsession For Women EDP Spray','','','&lt;p&gt;\r\n	Citrus, vanilla and greens lowering to notes of sandalwood, spices and musk. Recommended Use daytime&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;\'Obsession\'&lt;/em&gt; perfume was launched by the design house of Calvin Klein in 1985&lt;/p&gt;\r\n&lt;p&gt;\r\n	When you think about Calvin Klein, initially you think of his clothing line – specifically his jeans and underwear lines (not to mention the famous ad with a young Brooke Shields). But Calvin Klein’s penchant for perfume was equally as cutting edge as his foray into fashion.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (105,1,'Bvlgari Aqua','','','&lt;p&gt;\r\n	Bvlgari Aqua (Pour Homme) Eau De Toilette Spray is an enticing and fresh cologne that exudes masculinity from its unique blend of amber santolina, posidonia and mandarin.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Bvlgari Aqua (Pour Homme) Eau De Toilette Spray scent lasts throughout the day without having an overpowering smell. It is subtle enough for daytime use and masculine enough for night wear.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (106,1,'Omnia Eau de Toilette 65ml','bvlgary, omnia, EDT','','&lt;p&gt;\r\n	Choose Your scent&lt;/p&gt;\r\n&lt;p&gt;\r\n	&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Coral:&lt;/strong&gt; Inspired by the shimmering hues of precious red coral, Omnia Coral is a radiant floral-fruity Eau de Toilette of tropical Hibiscus and juicy Pomegranate, reminiscent of Summer, the sun, resplendent nature and far-off oceans.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Amethyst:&lt;/strong&gt; Inspired by the shimmering hues of the amethyst gemstone, this floral Eau de Toilette captures the myriad scents of Iris and Rose gardens caressed with morning dew.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Crystalline:&lt;/strong&gt; Created from the glowing clarity and purity of crystal, Omnia Crystalline is a sparkling jewel of light, illuminating and reflecting the gentle sensuality and luminous femininity. Sparkling like a precious jewel, like the rarest of crystals, in an exquisite jewel flacon.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (107,1,'Lancome Slimissime 360 Slimming Activating Concentrate Unisex Treatment','','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		An anti-cellulite body treatment&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Features a special gel-cream texture &amp;amp; a quick-dissolving formula&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Formulated with an exclusive 360 Complex&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	An anti-cellulite body treatment. Features a special gel-cream texture &amp;amp; a quick-dissolving formula. Formulated with an exclusive 360 Complex. Helps combat presence of cellulite &amp;amp; reduce existing cellulite. Provides immediate invigorating &amp;amp; firming results. Concentrated with micro-pearl particles to illuminate skin. Creates svelte &amp;amp; re-sculpted body contours....&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (108,1,'Lancome Hypnose Doll Lashes Mascara 4-Piece Gift Set','','','&lt;p&gt;\r\n	&nbsp;Lancome Hypnose Doll Lashes Mascara 4-Piece Gift Set! Limited Edition!&lt;/p&gt;\r\n&lt;ol&gt;\r\n	&lt;li&gt;\r\n		0.22 oz full-size Hypnôse Doll Lashes Mascara in Black&lt;/li&gt;\r\n	&lt;li&gt;\r\n		0.07 oz full-size Le Crayon Khol Eyeliner in Black Ebony&lt;/li&gt;\r\n	&lt;li&gt;\r\n		0.07 oz travel-size Cils Booster XL Super Enhancing Mascara Base&lt;/li&gt;\r\n	&lt;li&gt;\r\n		1.7 fl oz travel-size Bi-Facil Double-Action Eye Makeup Remover&lt;/li&gt;\r\n&lt;/ol&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (109,1,'Lancome Visionnaire Advanced Skin Corrector','','','&lt;p&gt;\r\n	Lancôme innovates with VISIONNAIRE [LR 2412 &nbsp;4%], its ?rst&nbsp;skincare product formulated to fundamentally recreate truly&nbsp;beautiful skin.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A Lancôme technological breakthrough has identi?ed&nbsp;a miraculous new molecule.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The name of this molecule: LR 2412.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A molecule that is able to “self-propel” through the layers&nbsp;of the epidermis, to set off a series of tissular micro-transformations.&nbsp;The result is that skin is visibly transformed: the texture is ?ner,&nbsp;wrinkles are erased, pigmentary and vascular irregularities are&nbsp;reduced and pores are tightened.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;Download Presentation file after order.&lt;/em&gt;&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (110,1,'Flora By Gucci Eau Fraiche','','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		Perfect for all occasions&lt;/li&gt;\r\n	&lt;li&gt;\r\n		This item is not a tester; New and sealed&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Contains natural ingredients&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	Gucci presents the new spring version of this perfume called flora by Gucci eau fraiche in 2011. Even younger, more airy, vivid, sparkling and fresher than the original, the new fragrance is enriched with additional aromas of citruses in the top notes and aquatic and green nuances in the heart, while the base remains unchanged. The composition begins with mandarin, bergamot, kumquat, lemon and peony. The heart is made of rose petals and Osman thus with green and aquatic additions, laid on the base of sandalwood, patchouli and pink pepper.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (111,1,'New French With Ease (1 book + 1 mp3 CD)','','','This title is available in book and CD. All Assimil courses are based on intuitive assimilations[registered], an original principle that is simple yet highly effective. Assimil has taken this natural process, through which you learned to speak your own language, and adapted it to their book and audio courses. Working progressively, with natural, lively dialogues, simple text notes and exercises, you will progress steadily to a level where you are able to converse in everyday situations. The first part of the course is the passive phase: you immerse yourself in the language by reading and repeating each lesson. During the second, the active phase, you use the structures and reflexes you have already absorbed while continuing to advance and learn. In just a few months, you will be able to speak French easily, fluently and naturally.&lt;br /&gt;\r\n&amp;nbsp;\r\n&lt;ul style=&quot;color: rgb(51, 51, 51); padding: 0px; list-style-type: none; font-size: 13px; font-family: verdana, arial, helvetica, sans-serif; line-height: 19px; orphans: auto; text-align: start; text-indent: 0px;&quot;&gt;\r\n	&lt;li&gt;Audio CD: 610 pages&lt;/li&gt;\r\n	&lt;li&gt;Publisher: ASSiMiL (3 April 2008)&lt;/li&gt;\r\n	&lt;li&gt;Language: English&lt;/li&gt;\r\n	&lt;li&gt;ISBN-10: 2700570057&lt;/li&gt;\r\n	&lt;li&gt;ISBN-13: 978-2700570052&lt;/li&gt;\r\n	&lt;li&gt;Product Dimensions: 23.6 x 17.8 x 5.1 cm&lt;/li&gt;\r\n&lt;/ul&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (112,1,'The Miracle Morning: The Not-So-Obvious Secret Guaranteed to Transform Your Life','','','What if you could miraculously wake up tomorrow and any&amp;mdash;or every area of your life was transformed? What would be different? Would you be happier? Healthier? More successful? In better shape? Would you have more energy? Less Stress? More Money? Better relationships? Which of your problems would be solved?&lt;br /&gt;\r\n&amp;nbsp;\r\n&lt;ul style=&quot;color: rgb(51, 51, 51); font-family: Arial, sans-serif; font-size: 13px; font-style: normal; font-variant: normal; font-weight: normal;&quot;&gt;\r\n	&lt;li&gt;&lt;b style=&quot;font-weight: 700; font-family: verdana, arial, helvetica, sans-serif;&quot;&gt;Print Length:&lt;/b&gt; 172 pages&lt;/li&gt;\r\n	&lt;li&gt;&lt;b style=&quot;font-weight: 700; font-family: verdana, arial, helvetica, sans-serif;&quot;&gt;Page Numbers Source ISBN:&lt;/b&gt; 0979019710&lt;/li&gt;\r\n	&lt;li&gt;&lt;b style=&quot;font-weight: 700; font-family: verdana, arial, helvetica, sans-serif;&quot;&gt;Publication Date:&lt;/b&gt; December 7, 2012&lt;/li&gt;\r\n	&lt;li&gt;&lt;b style=&quot;font-weight: 700; font-family: verdana, arial, helvetica, sans-serif;&quot;&gt;Language:&lt;/b&gt; English&lt;/li&gt;\r\n	&lt;li&gt;&lt;b style=&quot;font-weight: 700; font-family: verdana, arial, helvetica, sans-serif;&quot;&gt;ASIN:&lt;/b&gt; B00AKKS278&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;hr /&gt;\r\nAbout the Author\r\n&lt;p&gt;At age 20, Hal Elrod was hit head on by a drunk driver at 70 mph, was dead for 6 minutes, and doctors told his parents that if Hal ever came out of his coma, he had permanent brain damage and may never walk again. After 6 days of fighting for his life, Hal proved that we all have the ability to overcome any obstacle and create the life of our dreams. Not only did he walk, he became an ultra-marathon runner, hall of fame business achiever, international Keynote Speaker, Success Coach, husband, father, hip-hop recording artist, and multiple time #1 bestselling author of &amp;quot;The Miracle Morning: The Not-So-Obvious Secret Guaranteed To Transform Your Life... (Before 8AM)&amp;quot; and &amp;quot;Taking Life Head On: How To Love the Life You Have While You Create the Life of Your Dreams&amp;quot;&amp;mdash;two of the highest rated and most acclaimed books on Amazon.com. (Just read a few of the 200+ five-star reviews, and you&amp;rsquo;ll see why.) Hal has appeared on dozens of radio and TV shows, and he&amp;rsquo;s been featured in numerous books, including The Education of Millionaires, Cutting Edge Sales, Living College Life in the Front Row, The Author&amp;rsquo;s Guide To Building An Online Platform, The 800-Pound Gorilla of Sales and the bestselling Chicken Soup for the Soul series. To contact Hal about media appearances, speaking at your event, or if you just want to receive free training videos and resources, visit www.YoPalHal.com. To connect with Hal on Twitter, follow @HalElrod, and on Facebook at www.Facebook.com/YoPalHal.&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (113,1,'Paper Towns by John Green','','','&lt;strong&gt;Winner of the Edgar Award&lt;br /&gt;\r\nThe #1 New York Times Bestseller&lt;br /&gt;\r\nPublishers Weekly and USA Today Bestseller&lt;/strong&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\n&lt;em&gt;Millions of Copies Sold&lt;/em&gt;&lt;br /&gt;\r\n&lt;br /&gt;\r\nQuentin Jacobsen has spent a lifetime loving the magnificent Margo Roth Spiegelman from afar. So when she cracks open a window and climbs back into his life&amp;mdash;summoning him for an ingenious campaign of revenge&amp;mdash;he follows. When their all-nighter ends and a new day breaks, Margo has disappeared. But Q soon learns that there are clues&amp;mdash;and they&amp;rsquo;re for him. Embarking on an exhilarating adventure to find her, the closer Q gets, the less he sees the girl he thought he knew.&lt;br /&gt;\r\n&lt;br /&gt;\r\nOctober 2008&lt;br /&gt;\r\n352 pages&lt;br /&gt;\r\nISBN 9781101010938&lt;br /&gt;\r\nTitle: Paper Towns&lt;br /&gt;\r\nAuthor: John Green','');
INSERT INTO `ac_product_descriptions` VALUES (114,1,'Allegiant by Veronica Roth','','','Now includes an excerpt from the upcoming Four: A Divergent Collection.&lt;br /&gt;\r\n&lt;br /&gt;\r\nWhat if your whole world was a lie? What if a single revelation&amp;mdash;like a single choice&amp;mdash;changed everything? What if love and loyalty made you do things you never expected?&lt;br /&gt;\r\nThe explosive conclusion to Veronica Roth&amp;#39;s #1 New York Times bestselling Divergent trilogy reveals the secrets of the dystopian world that has captivated millions of readers in &lt;em&gt;Divergent&lt;/em&gt; and &lt;em&gt;Insurgent&lt;/em&gt;.&lt;br /&gt;\r\n&lt;br /&gt;\r\nOctober 2013&lt;br /&gt;\r\n544 pages&lt;br /&gt;\r\nISBN 9780062209276&lt;br /&gt;\r\nTitle: Allegiant&lt;br /&gt;\r\nAuthor: Veronica Roth','');
INSERT INTO `ac_product_descriptions` VALUES (115,1,'Fiorella Purple Peep Toes','','','Add more charm to your casual footwear collection with these purple peep toes from the house of Fiorella. Featuring a non-leather upper and lining, these slip-ons ate high on durability and style. While the wedge heels add extra inches to your silhouette, the resin sole ensures optimal traction for your feet. Team these peep toes with your casual outfits to complete your look for the day.&lt;br /&gt;\r\n&lt;br /&gt;\r\nSole Material&amp;nbsp; &lt;strong&gt;Resin Sheet&lt;/strong&gt;&lt;br /&gt;\r\nInner Lining&amp;nbsp;&amp;nbsp; &amp;nbsp;&lt;strong&gt;SYNTHETIC&lt;/strong&gt;&lt;br /&gt;\r\nClosing&amp;nbsp;&amp;nbsp; &amp;nbsp;&lt;strong&gt;&amp;nbsp;&amp;nbsp;&amp;nbsp; &amp;nbsp; &amp;nbsp; Slip On&lt;/strong&gt;&lt;br /&gt;\r\nHeel shape&amp;nbsp;&amp;nbsp;&amp;nbsp;&amp;nbsp; &lt;strong&gt;Wedge&lt;/strong&gt;&lt;br /&gt;\r\nHeel height&amp;nbsp;&amp;nbsp; &amp;nbsp;&lt;strong&gt; Medium: 2.5-3.5 Inch&lt;/strong&gt;\r\n&lt;hr /&gt;&amp;nbsp;\r\n&lt;table style=&quot;margin: 0px; padding: 0px; width: 385px; color: rgb(34, 34, 34); font-family: Arial; font-size: 13px; font-style: normal; font-variant: normal; font-weight: normal; letter-spacing: normal; line-height: 18px; orphans: auto; text-align: start; text-indent: 0px; text-transform: none; white-space: normal; widows: 1; word-spacing: 0px; -webkit-text-stroke-width: 0px; background-color: rgb(255, 255, 255);&quot;&gt;\r\n	&lt;tbody style=&quot;margin: 0px; padding: 0px;&quot;&gt;\r\n		&lt;tr style=&quot;margin: 0px; padding: 0px;&quot;&gt;\r\n			&lt;th style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;Euro Size&lt;/th&gt;\r\n			&lt;th style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;UK Size&lt;/th&gt;\r\n			&lt;th style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;Foot Size (In Cm)&lt;/th&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr class=&quot;even&quot; style=&quot;margin: 0px; padding: 0px;&quot;&gt;\r\n			&lt;td class=&quot;f-bold&quot; style=&quot;margin: 0px; padding: 7px; font-weight: bold; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;36&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;3&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;22.8-23.2&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr style=&quot;margin: 0px; padding: 0px;&quot;&gt;\r\n			&lt;td class=&quot;f-bold&quot; style=&quot;margin: 0px; padding: 7px; font-weight: bold; text-align: center; white-space: nowrap;&quot;&gt;37&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;4&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;23.4-23.7&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr class=&quot;even&quot; style=&quot;margin: 0px; padding: 0px;&quot;&gt;\r\n			&lt;td class=&quot;f-bold&quot; style=&quot;margin: 0px; padding: 7px; font-weight: bold; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;38&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;5&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;24.0-24.6&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr style=&quot;margin: 0px; padding: 0px;&quot;&gt;\r\n			&lt;td class=&quot;f-bold&quot; style=&quot;margin: 0px; padding: 7px; font-weight: bold; text-align: center; white-space: nowrap;&quot;&gt;39&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;6&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;24.8-25.2&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr class=&quot;even&quot; style=&quot;margin: 0px; padding: 0px;&quot;&gt;\r\n			&lt;td class=&quot;f-bold&quot; style=&quot;margin: 0px; padding: 7px; font-weight: bold; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;40&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;6.5&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap; background: rgb(238, 238, 238);&quot;&gt;25.4-25.7&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n		&lt;tr style=&quot;margin: 0px; padding: 0px;&quot;&gt;\r\n			&lt;td class=&quot;f-bold&quot; style=&quot;margin: 0px; padding: 7px; font-weight: bold; text-align: center; white-space: nowrap;&quot;&gt;41&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;7&lt;/td&gt;\r\n			&lt;td style=&quot;margin: 0px; padding: 7px; text-align: center; white-space: nowrap;&quot;&gt;26.0-26.4&lt;/td&gt;\r\n		&lt;/tr&gt;\r\n	&lt;/tbody&gt;\r\n&lt;/table&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (116,1,'New Ladies High Wedge Heel Toe Thong Diamante Flip Flop Sandals','','','&lt;p&gt;The annual Summer trend for wedges is back with these classic flip flop wedge heels. The sandals have a toe thong design, diamante detailing to the front and look fab worn with a maxi dress for a classic Summer look. Available in a choice of five different colours.&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li&gt;Available in 5 different colours&lt;/li&gt;\r\n	&lt;li&gt;Diamante detailing to the front&lt;/li&gt;\r\n	&lt;li&gt;Outer Material: &lt;em&gt;Synthetic&lt;/em&gt;&lt;/li&gt;\r\n	&lt;li&gt;Inner Material: &lt;em&gt;Manmade&lt;/em&gt;&lt;/li&gt;\r\n	&lt;li&gt;Heel Height: &lt;em&gt;9.5 centimetres&lt;/em&gt;&lt;/li&gt;\r\n	&lt;li&gt;Heel Type: Wedge&lt;/li&gt;\r\n	&lt;li&gt;Sandals have a toe thong design&lt;/li&gt;\r\n	&lt;li&gt;Classic flip flop wedge heels&lt;/li&gt;\r\n	&lt;li&gt;&lt;strong&gt;Perfect for the Summer!&lt;/strong&gt;&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n&lt;p&gt;&amp;nbsp;&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (117,1,'Ruby Shoo Womens Jada T-Bar','','','&lt;p&gt;Ruby Shoo is made up of a small team based in north London, England, with a passion for creating unique shoes and accessories that generate smiles whenever they are worn. We simply love it when we are asked the question: &amp;quot;Where did you get your shoes?&amp;quot;&lt;/p&gt;\r\n\r\n&lt;ul&gt;\r\n	&lt;li style=&quot;list-style-type: square; margin-left: 20px;&quot;&gt;Occasion footwear&lt;/li&gt;\r\n	&lt;li style=&quot;list-style-type: square; margin-left: 20px;&quot;&gt;Flower detail&lt;/li&gt;\r\n	&lt;li style=&quot;list-style-type: square; margin-left: 20px;&quot;&gt;Outer Material: Synthetic&lt;/li&gt;\r\n	&lt;li style=&quot;list-style-type: square; margin-left: 20px;&quot;&gt;Inner Material: Manmade&lt;/li&gt;\r\n	&lt;li style=&quot;list-style-type: square; margin-left: 20px;&quot;&gt;Sole: Manmade&lt;/li&gt;\r\n	&lt;li style=&quot;list-style-type: square; margin-left: 20px;&quot;&gt;Closure: Buckle&lt;/li&gt;\r\n	&lt;li style=&quot;list-style-type: square; margin-left: 20px;&quot;&gt;Heel Height: 9 centimetres&lt;/li&gt;\r\n	&lt;li style=&quot;list-style-type: square; margin-left: 20px;&quot;&gt;Heel Type: Stiletto&lt;/li&gt;\r\n&lt;/ul&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (118,1,'Womens high heel point toe stiletto sandals ankle strap court shoes','','','&lt;p&gt;Fashion Thirsty&amp;reg; branded products are only available exclusively through Fashion Thirsty&lt;/p&gt;\r\n&lt;ul&gt;\r\n	&lt;li&gt;Brand New In Box&lt;/li&gt;\r\n	&lt;li&gt;Available In A Range Of Colours And Fabrics&lt;/li&gt;\r\n	&lt;li&gt;Outer Material: Synthetic&lt;/li&gt;\r\n	&lt;li&gt;Inner Material: Manmade&lt;/li&gt;\r\n	&lt;li&gt;Sole: manmade&lt;/li&gt;\r\n	&lt;li&gt;Closure: Buckle&lt;/li&gt;\r\n	&lt;li&gt;Heel Height: 4.3&lt;/li&gt;\r\n	&lt;li&gt;Heel Type: Stiletto&lt;/li&gt;\r\n	&lt;li&gt;Approx Heel Height: 4.3 Inches / 10.8 cm&lt;/li&gt;\r\n	&lt;li&gt;Approx Platform Height: 0.1 inches / 0.3 cm&lt;/li&gt;\r\n	&lt;li&gt;Ankle Strap With Buckle Fastening&lt;/li&gt;\r\n&lt;/ul&gt;\r\n\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (119,1,'Fruit of the Loom T-Shirts 5 Pack - Super Premium','','','5x Fruit of the Loom T-Shirt Super Premium T&lt;br /&gt;\r\n10 Colors - Sizes: S, M, L, XL, XXL oder XXXL\r\n&lt;hr /&gt;The purchase of this 5-packs, you have the following choice:&lt;br /&gt;\r\n&amp;bull; 5 of the same color t-shirts (available in 18 colors)&lt;br /&gt;\r\n&amp;bull; 5 different colored T-shirts (given in 8 color sets available)&lt;br /&gt;\r\nYou have the freedom to choose from the adult sizes S, M, L, XL, XXL or XXXL.','');
INSERT INTO `ac_product_descriptions` VALUES (120,1,'Jersey Cotton Striped Polo Shirt','','t-shirs, men','&lt;p&gt;Classically designed Charles Wilson polo shirts now available.&lt;br /&gt;\r\nThis premium polo collection is a result of many years experience in designing and manufacturing polo shirts.&lt;br /&gt;\r\nPart of our Jersey Stripe polo range this colourful jersey cotton polo shirt is a must have piece for any casual wardrobe.&lt;br /&gt;\r\nDesigned in the UK&lt;br /&gt;\r\n100% Cotton&lt;br /&gt;\r\nJersey Stripe Polo&lt;br /&gt;\r\nMulti-Stripe Design&lt;br /&gt;\r\nThree Button Design&lt;br /&gt;\r\nMachine Washable at 40 Degrees&lt;/p&gt;\r\n','');
INSERT INTO `ac_product_descriptions` VALUES (121,1,'Designer Men Casual Formal Double Cuffs Grandad Band Collar Shirt Elegant Tie','','','Superb style double cuffs shirt. Made using highest quality cotton. Grandad collar. Slim Fit. Highest quality - made in Turkey. Great as casual or formal shirt.','');
INSERT INTO `ac_product_descriptions` VALUES (122,1,'Mens Fine Cotton Giraffe Polo Shirts','','','Designed by slim fit style FOR men and women in highest qualities and workmanship to bring buyers A different outlook on life of fashion Casual Basic Slim Fit Polo Shirts\r\n&lt;ul&gt;\r\n	&lt;li&gt;100% COTTON&lt;/li&gt;\r\n	&lt;li&gt;If you buy these Polo shits, You&amp;#39;ll never regret about purchase. Because it is so nice designed shirts for your daily look.&lt;/li&gt;\r\n	&lt;li&gt;Soft Elastic Decent slim fit &amp;amp; Button placket &amp;amp; sleeve ribbing with contrast trim.&lt;/li&gt;\r\n	&lt;li&gt;Machine Wash / Hand Wash Recommended&lt;/li&gt;\r\n&lt;/ul&gt;\r\n','');

--
-- Dumping data for table `product_discounts`
--

INSERT INTO `ac_product_discounts` VALUES (1,81,1,2,0,59.0000,'0000-00-00','0000-00-00','0000-00-00 00:00:00','2015-06-22 12:40:54');
INSERT INTO `ac_product_discounts` VALUES (2,81,1,3,0,56.0000,'0000-00-00','0000-00-00','0000-00-00 00:00:00','2015-06-22 12:41:09');
INSERT INTO `ac_product_discounts` VALUES (3,81,1,4,0,50.0000,'0000-00-00','0000-00-00','0000-00-00 00:00:00','2015-06-22 12:41:25');

--
-- Dumping data for table `product_option_descriptions`
--

INSERT INTO `ac_product_option_descriptions` VALUES (318,1,53,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (315,1,54,'Shade','','');
INSERT INTO `ac_product_option_descriptions` VALUES (319,1,56,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (304,1,57,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (305,1,59,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (306,1,55,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (307,1,60,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (308,1,61,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (316,1,63,'Fragrance Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (314,1,64,'Fragrance Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (317,1,69,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (320,1,78,'Fragrance Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (321,1,80,'Fragrance Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (322,1,84,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (323,1,85,'Fragrance Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (324,1,89,'Fragrance Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (326,1,90,'Fragrance Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (327,1,99,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (328,1,100,'Color','','');
INSERT INTO `ac_product_option_descriptions` VALUES (329,1,101,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (330,1,102,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (331,1,104,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (332,1,104,'Gift Wrapping','','');
INSERT INTO `ac_product_option_descriptions` VALUES (335,1,105,'Fragrance Type','','');
INSERT INTO `ac_product_option_descriptions` VALUES (336,1,105,'Gift Wrapping','','');
INSERT INTO `ac_product_option_descriptions` VALUES (337,1,105,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (338,1,106,'Choose Scent','','');
INSERT INTO `ac_product_option_descriptions` VALUES (339,1,106,'Gift Wrapping','','');
INSERT INTO `ac_product_option_descriptions` VALUES (340,1,109,'Gift Wrapping','','');
INSERT INTO `ac_product_option_descriptions` VALUES (341,1,110,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (342,1,115,'Select Size (EURO)','','');
INSERT INTO `ac_product_option_descriptions` VALUES (344,1,116,'UK size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (345,1,116,'Colour','','');
INSERT INTO `ac_product_option_descriptions` VALUES (346,1,117,'Color&amp;Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (347,1,118,'Colour','','');
INSERT INTO `ac_product_option_descriptions` VALUES (348,1,119,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (349,1,120,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (350,1,121,'Colour','','');
INSERT INTO `ac_product_option_descriptions` VALUES (351,1,121,'Size','','');
INSERT INTO `ac_product_option_descriptions` VALUES (352,1,122,'Size EU','','');

--
-- Dumping data for table `product_option_value_descriptions`
--


INSERT INTO `ac_product_option_value_descriptions` VALUES (653,1,53,'Natural Ambre',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (652,1,53,'Natural Golden',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (646,1,54,'Brown','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (645,1,54,'Black','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (658,1,56,'Suede',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (657,1,56,'Light Bisque',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (656,1,56,'Ivore',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (655,1,56,'Dore',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (654,1,56,'Bronze',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (612,1,57,'Pink Pool',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (613,1,57,'Mandarin Sky',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (614,1,57,'Brilliant Berry',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (615,1,59,'Viva Glam IV',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (616,1,59,'Viva Glam II',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (617,1,59,'Viva Glam VI',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (618,1,55,'La Base',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (619,1,55,'Lacewood',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (620,1,55,'Smoky Rouge',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (621,1,55,'Tulipwood',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (622,1,60,'Shirelle',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (623,1,60,'Vintage Vamp',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (624,1,60,'Nocturnelle',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (625,1,61,'Golden Frenzy',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (626,1,61,'Gris Fatale',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (627,1,61,'Jade Fever',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (649,1,63,'1.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (648,1,63,'2.5 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (647,1,63,'3.4 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (644,1,64,'3.4 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (643,1,64,'1.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (642,1,64,'1.0 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (651,1,69,'33.8 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (650,1,69,'8 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (662,1,78,'50ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (661,1,78,'150ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (660,1,78,'100ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (659,1,56,'Light Buff',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (663,1,80,'1.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (664,1,80,'3.4 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (665,1,80,'6.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (666,1,84,'30 ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (667,1,84,'50 ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (668,1,84,'75 ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (669,1,85,'1 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (670,1,85,'1.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (671,1,85,'3.4 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (672,1,89,'0.04 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (673,1,89,'6.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (674,1,89,'1.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (676,1,90,'1.7 oz EDP Spray',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (677,1,90,'3.4 oz EDP Spray',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (678,1,99,'rose beige',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (679,1,99,'cranberry',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (680,1,99,'cassis',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (681,1,100,'beige',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (682,1,100,'red beige',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (683,1,100,'brique',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (684,1,100,'brown',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (685,1,100,'mauve',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (686,1,100,'red',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (687,1,101,'8.45 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (688,1,101,'15.2 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (689,1,101,'33.8 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (690,1,102,'30ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (691,1,102,'50ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (692,1,102,'75ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (714,1,104,'1 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (713,1,104,'1.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (722,1,105,'Eau de Cologne',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (721,1,105,'Eau de Toilette',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (720,1,105,'Eau de Parfum',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (719,1,105,'yes',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (723,1,105,'1 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (724,1,105,'1.7 oz',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (733,1,106,'Crystalline',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (732,1,106,'Amethyst',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (731,1,106,'Coral',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (735,1,106,'yes',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (737,1,109,'yes',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (738,1,110,'30ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (739,1,110,'50ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (740,1,110,'75ml',NULL);
INSERT INTO `ac_product_option_value_descriptions` VALUES (741,1,115,'36','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (742,1,115,'37','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (743,1,115,'38','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (744,1,115,'39','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (745,1,115,'40','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (746,1,115,'41 -','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (747,1,116,'3 UK ','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (748,1,116,'4 UK ','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (749,1,116,'5 UK ','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (750,1,116,'6 UK ','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (751,1,116,'7 UK ','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (752,1,116,'white','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (753,1,116,'red','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (754,1,116,'black','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (755,1,117,'UK 3 / White','a:3:{i:0;a:2:{s:9:\"attr_v_id\";s:2:\"79\";s:4:\"name\";s:4:\"UK 3\";}i:1;a:2:{s:9:\"attr_v_id\";s:2:\"89\";s:4:\"name\";s:5:\"White\";}i:2;a:2:{i:0;s:4:\"UK 3\";i:1;s:5:\"White\";}}');
INSERT INTO `ac_product_option_value_descriptions` VALUES (756,1,117,'UK 3.5 / White','a:3:{i:0;a:2:{s:9:\"attr_v_id\";s:2:\"80\";s:4:\"name\";s:6:\"UK 3.5\";}i:1;a:2:{s:9:\"attr_v_id\";s:2:\"89\";s:4:\"name\";s:5:\"White\";}i:2;a:2:{i:0;s:6:\"UK 3.5\";i:1;s:5:\"White\";}}');
INSERT INTO `ac_product_option_value_descriptions` VALUES (757,1,117,'UK 4 / White','a:3:{i:0;a:2:{s:9:\"attr_v_id\";s:2:\"81\";s:4:\"name\";s:4:\"UK 4\";}i:1;a:2:{s:9:\"attr_v_id\";s:2:\"89\";s:4:\"name\";s:5:\"White\";}i:2;a:2:{i:0;s:4:\"UK 4\";i:1;s:5:\"White\";}}');
INSERT INTO `ac_product_option_value_descriptions` VALUES (758,1,117,'UK 6 / White','a:3:{i:0;a:2:{s:9:\"attr_v_id\";s:2:\"85\";s:4:\"name\";s:4:\"UK 6\";}i:1;a:2:{s:9:\"attr_v_id\";s:2:\"89\";s:4:\"name\";s:5:\"White\";}i:2;a:2:{i:0;s:4:\"UK 6\";i:1;s:5:\"White\";}}');
INSERT INTO `ac_product_option_value_descriptions` VALUES (759,1,117,'UK 3 / Red','a:3:{i:0;a:2:{s:9:\"attr_v_id\";s:2:\"79\";s:4:\"name\";s:4:\"UK 3\";}i:1;a:2:{s:9:\"attr_v_id\";s:2:\"88\";s:4:\"name\";s:3:\"Red\";}i:2;a:2:{i:0;s:4:\"UK 3\";i:1;s:3:\"Red\";}}');
INSERT INTO `ac_product_option_value_descriptions` VALUES (760,1,117,'UK 5 / Red','a:3:{i:0;a:2:{s:9:\"attr_v_id\";s:2:\"83\";s:4:\"name\";s:4:\"UK 5\";}i:1;a:2:{s:9:\"attr_v_id\";s:2:\"88\";s:4:\"name\";s:3:\"Red\";}i:2;a:2:{i:0;s:4:\"UK 5\";i:1;s:3:\"Red\";}}');
INSERT INTO `ac_product_option_value_descriptions` VALUES (761,1,117,'UK 7 / Red','a:3:{i:0;a:2:{s:9:\"attr_v_id\";s:2:\"86\";s:4:\"name\";s:4:\"UK 7\";}i:1;a:2:{s:9:\"attr_v_id\";s:2:\"88\";s:4:\"name\";s:3:\"Red\";}i:2;a:2:{i:0;s:4:\"UK 7\";i:1;s:3:\"Red\";}}');
INSERT INTO `ac_product_option_value_descriptions` VALUES (762,1,117,'UK 5.5 / Blue','a:3:{i:0;a:2:{s:9:\"attr_v_id\";s:2:\"84\";s:4:\"name\";s:6:\"UK 5.5\";}i:1;a:2:{s:9:\"attr_v_id\";s:2:\"91\";s:4:\"name\";s:4:\"Blue\";}i:2;a:2:{i:0;s:6:\"UK 5.5\";i:1;s:4:\"Blue\";}}');
INSERT INTO `ac_product_option_value_descriptions` VALUES (763,1,118,'black','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (764,1,118,'red','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (765,1,118,'green','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (766,1,118,'blue','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (767,1,118,'white','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (768,1,119,'Small','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (769,1,119,'Medium','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (770,1,119,'Large','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (771,1,119,'X-Large','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (772,1,120,'Small','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (773,1,120,'Large','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (774,1,121,'Light Blue','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (775,1,121,'White','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (776,1,121,'Small','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (777,1,121,'Medium','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (778,1,122,'EU XS (Asia M)','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (779,1,122,'EU S (Asia L)','');
INSERT INTO `ac_product_option_value_descriptions` VALUES (780,1,122,'EU 2XL (Asia 5XL)','');

--
-- Dumping data for table `product_option_values`
--

INSERT INTO `ac_product_option_values` VALUES (646,315,54,0,'',983,1,5.0000,'$',0.00000000,'lb',0,'',0,0);
INSERT INTO `ac_product_option_values` VALUES (653,318,53,0,'',2000,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (652,318,53,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (645,315,54,0,'',1000,1,0.0000,'$',0.00000000,'lb',0,'',0,1);
INSERT INTO `ac_product_option_values` VALUES (659,319,56,0,'',999,1,0.0000,'$',0.00000000,'lb',0,NULL,2,0);
INSERT INTO `ac_product_option_values` VALUES (658,319,56,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,1,0);
INSERT INTO `ac_product_option_values` VALUES (657,319,56,0,'',998,1,1.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (656,319,56,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (655,319,56,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (654,319,56,0,'',555,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (612,304,57,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (613,304,57,0,'',999,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (614,304,57,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (615,305,59,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (616,305,59,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (617,305,59,0,'',1000,1,2.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (618,306,55,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (619,306,55,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (620,306,55,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (621,306,55,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (622,307,60,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (623,307,60,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (624,307,60,0,'',0,0,1.4200,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (625,308,61,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (626,308,61,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (627,308,61,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (649,316,63,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (648,316,63,0,'',0,0,20.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (647,316,63,0,'',0,0,25.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (644,314,64,0,'',66,1,22.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (643,314,64,0,'',1000,1,10.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (642,314,64,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (651,317,69,0,'',553,1,30.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (650,317,69,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (662,320,78,0,'',59,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (661,320,78,0,'',887,1,16.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (660,320,78,0,'',998,1,8.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (663,321,80,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (664,321,80,0,'',0,0,25.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (665,321,80,0,'',0,0,45.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (666,322,84,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (667,322,84,0,'',1000,1,20.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (668,322,84,0,'',0,0,32.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (669,323,85,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (670,323,85,0,'',100,0,18.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (671,323,85,0,'',0,0,23.5000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (672,324,89,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (673,324,89,0,'',0,0,30.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (674,324,89,0,'',1000,1,10.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (676,326,90,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (677,326,90,0,'',556,1,15.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (678,327,99,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (679,327,99,0,'',50,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (680,327,99,0,'',48,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (681,328,100,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (682,328,100,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (683,328,100,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (684,328,100,0,'',46,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (685,328,100,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (686,328,100,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (687,329,101,0,'',256,0,-2.0000,'$',0.80000000,'lb',4,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (688,329,101,0,'',155,0,4.0000,'$',0.15000000,'lb',5,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (689,329,101,0,'',100,1,10.0000,'$',0.33000000,'lb',6,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (690,330,102,0,'',55,0,0.0000,'$',0.00000000,'lb',17,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (691,330,102,0,'',55,0,20.0000,'$',0.00000000,'lb',18,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (692,330,102,0,'',55,0,30.0000,'$',0.00000000,'lb',19,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (714,331,104,0,'',50,0,0.0000,'$',0.00000000,'lb',53,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (713,331,104,0,'',50,0,20.0000,'$',0.00000000,'lb',54,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (700,332,104,0,'',0,0,3.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (722,335,105,0,'',100,1,24.0000,'$',0.00000000,'lb',77,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (721,335,105,0,'',44,1,21.0000,'$',0.00000000,'lb',76,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (720,335,105,0,'',25,1,60.0000,'$',0.00000000,'lb',75,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (719,336,105,0,'',0,0,2.5000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (723,337,105,0,'',0,0,0.0000,'$',0.00000000,'lb',53,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (724,337,105,0,'',0,0,25.0000,'%',0.00000000,'lb',54,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (733,338,106,0,'',80,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (732,338,106,0,'',59,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (731,338,106,0,'',120,1,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (735,339,106,0,'',0,0,1.5000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (737,340,109,0,'',0,0,0.0000,'$',0.00000000,'lb',0,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (738,341,110,0,'',97,1,0.0000,'$',0.00000000,'lb',50,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (739,341,110,0,'',120,1,15.0000,'$',0.00000000,'lb',51,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (740,341,110,0,'',56,1,30.0000,'$',0.00000000,'lb',52,NULL,0,0);
INSERT INTO `ac_product_option_values` VALUES (741,342,115,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',0,0);
INSERT INTO `ac_product_option_values` VALUES (742,342,115,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',1,0);
INSERT INTO `ac_product_option_values` VALUES (743,342,115,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',2,1);
INSERT INTO `ac_product_option_values` VALUES (744,342,115,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',3,0);
INSERT INTO `ac_product_option_values` VALUES (745,342,115,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',4,0);
INSERT INTO `ac_product_option_values` VALUES (746,342,115,0,'',9,1,0.0000,'$',0.00000000,'lb',0,'',5,0);
INSERT INTO `ac_product_option_values` VALUES (747,344,116,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',0,0);
INSERT INTO `ac_product_option_values` VALUES (748,344,116,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',1,0);
INSERT INTO `ac_product_option_values` VALUES (749,344,116,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',2,0);
INSERT INTO `ac_product_option_values` VALUES (750,344,116,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',3,0);
INSERT INTO `ac_product_option_values` VALUES (751,344,116,0,'',0,1,0.0000,'$',0.00000000,'lb',0,'',4,0);
INSERT INTO `ac_product_option_values` VALUES (752,345,116,0,'',3,1,0.0000,'$',0.00000000,'lb',0,'',0,0);
INSERT INTO `ac_product_option_values` VALUES (753,345,116,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'',1,1);
INSERT INTO `ac_product_option_values` VALUES (754,345,116,0,'',4,1,0.0000,'$',0.10000000,'lb',0,'',2,0);
INSERT INTO `ac_product_option_values` VALUES (755,346,117,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'a:2:{i:0;a:2:{s:7:\"attr_id\";i:7;s:9:\"attr_v_id\";s:2:\"79\";}i:1;a:2:{s:7:\"attr_id\";i:8;s:9:\"attr_v_id\";s:2:\"89\";}}',0,0);
INSERT INTO `ac_product_option_values` VALUES (756,346,117,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'a:2:{i:0;a:2:{s:7:\"attr_id\";i:7;s:9:\"attr_v_id\";s:2:\"80\";}i:1;a:2:{s:7:\"attr_id\";i:8;s:9:\"attr_v_id\";s:2:\"89\";}}',1,1);
INSERT INTO `ac_product_option_values` VALUES (757,346,117,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'a:2:{i:0;a:2:{s:7:\"attr_id\";i:7;s:9:\"attr_v_id\";s:2:\"81\";}i:1;a:2:{s:7:\"attr_id\";i:8;s:9:\"attr_v_id\";s:2:\"89\";}}',2,0);
INSERT INTO `ac_product_option_values` VALUES (758,346,117,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'a:2:{i:0;a:2:{s:7:\"attr_id\";i:7;s:9:\"attr_v_id\";s:2:\"85\";}i:1;a:2:{s:7:\"attr_id\";i:8;s:9:\"attr_v_id\";s:2:\"89\";}}',3,0);
INSERT INTO `ac_product_option_values` VALUES (759,346,117,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'a:2:{i:0;a:2:{s:7:\"attr_id\";i:7;s:9:\"attr_v_id\";s:2:\"79\";}i:1;a:2:{s:7:\"attr_id\";i:8;s:9:\"attr_v_id\";s:2:\"88\";}}',4,0);
INSERT INTO `ac_product_option_values` VALUES (760,346,117,0,'',0,0,0.0000,'$',0.00000000,'lb',0,'a:2:{i:0;a:2:{s:7:\"attr_id\";i:7;s:9:\"attr_v_id\";s:2:\"83\";}i:1;a:2:{s:7:\"attr_id\";i:8;s:9:\"attr_v_id\";s:2:\"88\";}}',5,0);
INSERT INTO `ac_product_option_values` VALUES (761,346,117,0,'',0,0,2.5000,'$',0.00000000,'lb',0,'a:2:{i:0;a:2:{s:7:\"attr_id\";i:7;s:9:\"attr_v_id\";s:2:\"86\";}i:1;a:2:{s:7:\"attr_id\";i:8;s:9:\"attr_v_id\";s:2:\"88\";}}',6,0);
INSERT INTO `ac_product_option_values` VALUES (762,346,117,0,'',14,1,0.0000,'$',0.00000000,'lb',0,'a:2:{i:0;a:2:{s:7:\"attr_id\";i:7;s:9:\"attr_v_id\";s:2:\"84\";}i:1;a:2:{s:7:\"attr_id\";i:8;s:9:\"attr_v_id\";s:2:\"91\";}}',7,0);
INSERT INTO `ac_product_option_values` VALUES (763,347,118,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',0,1);
INSERT INTO `ac_product_option_values` VALUES (764,347,118,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',1,0);
INSERT INTO `ac_product_option_values` VALUES (765,347,118,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',2,0);
INSERT INTO `ac_product_option_values` VALUES (766,347,118,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',3,0);
INSERT INTO `ac_product_option_values` VALUES (767,347,118,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',4,0);
INSERT INTO `ac_product_option_values` VALUES (768,348,119,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',0,0);
INSERT INTO `ac_product_option_values` VALUES (769,348,119,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',1,1);
INSERT INTO `ac_product_option_values` VALUES (770,348,119,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',2,0);
INSERT INTO `ac_product_option_values` VALUES (771,348,119,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',3,0);
INSERT INTO `ac_product_option_values` VALUES (772,349,120,0,'',6,1,0.0000,'$',0.00000000,'g',0,'',0,0);
INSERT INTO `ac_product_option_values` VALUES (773,349,120,0,'',8,1,1.0000,'$',0.00000000,'g',0,'',1,1);
INSERT INTO `ac_product_option_values` VALUES (774,350,121,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',0,0);
INSERT INTO `ac_product_option_values` VALUES (775,350,121,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',1,0);
INSERT INTO `ac_product_option_values` VALUES (776,351,121,0,'',3,1,0.0000,'$',0.00000000,'g',0,'',0,0);
INSERT INTO `ac_product_option_values` VALUES (777,351,121,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',1,1);
INSERT INTO `ac_product_option_values` VALUES (778,352,122,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',0,1);
INSERT INTO `ac_product_option_values` VALUES (779,352,122,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',1,0);
INSERT INTO `ac_product_option_values` VALUES (780,352,122,0,'',0,0,0.0000,'$',0.00000000,'g',0,'',2,0);

--
-- Dumping data for table `product_options`
--

INSERT INTO `ac_product_options` VALUES (315,0,54,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (318,0,53,0,2,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (319,0,56,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (304,0,57,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (305,0,59,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (306,0,55,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (307,0,60,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (308,0,61,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (316,0,63,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (314,0,64,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (317,0,69,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (320,0,78,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (321,0,80,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (322,0,84,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (323,0,85,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (324,0,89,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (326,0,90,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (327,0,99,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (328,0,100,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (329,1,101,0,0,1,'S',1,'',NULL);
INSERT INTO `ac_product_options` VALUES (330,1,102,0,0,1,'S',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (331,1,104,0,0,1,'S',1,'',NULL);
INSERT INTO `ac_product_options` VALUES (332,2,104,0,0,1,'C',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (335,5,105,0,0,1,'G',1,'',NULL);
INSERT INTO `ac_product_options` VALUES (336,2,105,0,5,1,'C',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (337,1,105,0,2,1,'S',1,'',NULL);
INSERT INTO `ac_product_options` VALUES (338,0,106,0,1,1,'S',1,'',NULL);
INSERT INTO `ac_product_options` VALUES (339,2,106,0,2,1,'C',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (340,2,109,0,0,1,'C',0,'',NULL);
INSERT INTO `ac_product_options` VALUES (341,1,110,0,0,1,'S',1,'',NULL);
INSERT INTO `ac_product_options` VALUES (342,0,115,0,0,1,'S',1,'','');
INSERT INTO `ac_product_options` VALUES (344,0,116,0,0,1,'R',1,'','');
INSERT INTO `ac_product_options` VALUES (345,0,116,0,0,1,'S',1,'','');
INSERT INTO `ac_product_options` VALUES (346,6,117,0,0,1,'S',1,'','a:4:{s:10:\"extensions\";s:0:\"\";s:8:\"min_size\";s:0:\"\";s:8:\"max_size\";s:0:\"\";s:9:\"directory\";s:0:\"\";}');
INSERT INTO `ac_product_options` VALUES (347,0,118,0,0,1,'R',1,'','');
INSERT INTO `ac_product_options` VALUES (348,0,119,0,0,1,'S',1,'','');
INSERT INTO `ac_product_options` VALUES (349,0,120,0,0,1,'S',1,'','');
INSERT INTO `ac_product_options` VALUES (350,0,121,0,0,1,'S',1,'','');
INSERT INTO `ac_product_options` VALUES (351,0,121,0,0,1,'S',1,'','');
INSERT INTO `ac_product_options` VALUES (352,0,122,0,0,1,'S',1,'','');

--
-- Dumping data for table `product_specials`
--

INSERT INTO `ac_product_specials` VALUES (252,51,1,0,19.0000,'0000-00-00','0000-00-00','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_product_specials` VALUES (253,55,1,0,27.0000,'0000-00-00','0000-00-00','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_product_specials` VALUES (254,67,1,0,29.0000,'0000-00-00','0000-00-00','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_product_specials` VALUES (255,72,1,0,24.0000,'0000-00-00','0000-00-00','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_product_specials` VALUES (256,88,1,0,27.0000,'0000-00-00','0000-00-00','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_product_specials` VALUES (257,93,1,0,220.0000,'0000-00-00','0000-00-00','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_product_specials` VALUES (258,65,1,1,89.0000,'0000-00-00','0000-00-00','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_product_specials` VALUES (260,80,1,1,45.0000,'0000-00-00','0000-00-00','2015-06-12 09:56:25','2015-06-12 09:56:25');

--
-- Dumping data for table `product_tags`
--

INSERT INTO `ac_product_tags` VALUES (50,'cheeks',1);
INSERT INTO `ac_product_tags` VALUES (50,'makeup',1);
INSERT INTO `ac_product_tags` VALUES (51,'cheeks',1);
INSERT INTO `ac_product_tags` VALUES (51,'makeup',1);
INSERT INTO `ac_product_tags` VALUES (54,'eye',1);
INSERT INTO `ac_product_tags` VALUES (54,'makeup',1);
INSERT INTO `ac_product_tags` VALUES (77,'body',1);
INSERT INTO `ac_product_tags` VALUES (77,'men',1);
INSERT INTO `ac_product_tags` VALUES (77,'shower',1);
INSERT INTO `ac_product_tags` VALUES (78,'fragrance',1);
INSERT INTO `ac_product_tags` VALUES (78,'men',1);
INSERT INTO `ac_product_tags` VALUES (79,'fragrance',1);
INSERT INTO `ac_product_tags` VALUES (79,'men',1);
INSERT INTO `ac_product_tags` VALUES (79,'unisex',1);
INSERT INTO `ac_product_tags` VALUES (79,'women',1);
INSERT INTO `ac_product_tags` VALUES (81,'Eau de Toilette',1);
INSERT INTO `ac_product_tags` VALUES (85,'fragrance',1);
INSERT INTO `ac_product_tags` VALUES (85,'women',1);
INSERT INTO `ac_product_tags` VALUES (87,'fragrance',1);
INSERT INTO `ac_product_tags` VALUES (89,'fragrance',1);
INSERT INTO `ac_product_tags` VALUES (89,'woman',1);
INSERT INTO `ac_product_tags` VALUES (95,'gift',1);
INSERT INTO `ac_product_tags` VALUES (95,'man',1);
INSERT INTO `ac_product_tags` VALUES (96,'man',1);
INSERT INTO `ac_product_tags` VALUES (96,'skincare',1);
INSERT INTO `ac_product_tags` VALUES (98,'man',1);
INSERT INTO `ac_product_tags` VALUES (99,'nail',1);
INSERT INTO `ac_product_tags` VALUES (99,'women',1);
INSERT INTO `ac_product_tags` VALUES (101,'conditioner',1);
INSERT INTO `ac_product_tags` VALUES (103,'spray',1);
INSERT INTO `ac_product_tags` VALUES (108,'gift',1);
INSERT INTO `ac_product_tags` VALUES (108,'pen',1);
INSERT INTO `ac_product_tags` VALUES (108,'set',1);
INSERT INTO `ac_product_tags` VALUES (115,'fashion',1);
INSERT INTO `ac_product_tags` VALUES (115,'purple',1);
INSERT INTO `ac_product_tags` VALUES (115,'shoe',1);
INSERT INTO `ac_product_tags` VALUES (116,'fashion',1);
INSERT INTO `ac_product_tags` VALUES (116,'shoe',1);
INSERT INTO `ac_product_tags` VALUES (117,'fashion',1);
INSERT INTO `ac_product_tags` VALUES (117,'shoe',1);
INSERT INTO `ac_product_tags` VALUES (119,'fashion',1);
INSERT INTO `ac_product_tags` VALUES (119,'t-shirt',1);
INSERT INTO `ac_product_tags` VALUES (119,'yellow',1);
INSERT INTO `ac_product_tags` VALUES (120,'fashion',1);
INSERT INTO `ac_product_tags` VALUES (120,'t-shirt',1);
INSERT INTO `ac_product_tags` VALUES (121,'fashion',1);
INSERT INTO `ac_product_tags` VALUES (122,'fashion',1);
INSERT INTO `ac_product_tags` VALUES (122,'grey',1);

--
-- Dumping data for table `products`
--

INSERT INTO `ac_products` VALUES (68,'108681','','',1000,1,15,1,0,0,0.0000,42.0000,1,'2013-08-30',0.11,1,0.00,0.00,0.00,0,1,0,1,1,1,0,24.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (65,'427847','','',1000,1,15,1,0,0,0.0000,105.0000,1,'2013-08-30',70.00,2,0.00,0.00,0.00,0,1,21,1,0,1,0,99.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (66,'556240','','',145,1,12,1,0,0,0.0000,38.0000,1,'2013-08-30',0.40,1,0.00,0.00,0.00,0,1,4,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (67,'463686','','',0,2,15,1,0,0,0.0000,34.5000,1,'2013-08-30',0.30,1,0.00,0.00,0.00,2,1,6,1,1,1,0,22.0000,0,'2015-06-12 09:56:25','2015-06-22 13:00:31');
INSERT INTO `ac_products` VALUES (50,'558003','','',99,1,11,1,0,0,0.0000,29.5000,1,'2013-08-29',75.00,2,0.00,0.00,0.00,0,1,8,1,0,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (51,'483857','','',98,1,12,1,0,0,0.0000,30.0000,1,'2013-08-29',0.05,1,0.00,0.00,0.00,0,1,7,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (52,'523755','','',99,1,12,1,0,0,0.0000,28.0000,0,'2013-08-29',0.80,1,0.00,0.00,0.00,0,1,3,1,1,2,0,0.0000,0,'2015-06-12 09:56:25','2015-06-22 13:06:09');
INSERT INTO `ac_products` VALUES (53,'380440','','',1000,3,15,1,0,0,0.0000,38.5000,1,'2013-08-29',100.00,2,0.00,0.00,0.00,0,1,5,1,1,1,0,22.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (54,'74144','','',999,1,15,1,0,0,0.0000,25.0000,1,'2013-08-29',0.15,1,0.00,0.00,0.00,0,1,13,1,1,1,0,0.0000,1,'2015-06-12 09:56:25','2015-06-22 12:51:08');
INSERT INTO `ac_products` VALUES (55,'tw152236','','',1000,1,15,1,0,0,0.0000,29.0000,1,'2013-08-29',0.08,1,0.00,0.00,0.00,0,1,6,1,1,1,0,22.0000,0,'2015-06-12 09:56:25','2015-07-08 10:47:41');
INSERT INTO `ac_products` VALUES (56,'35190','','',1000,1,15,1,0,0,0.0000,29.5000,1,'2013-08-29',85.00,2,0.00,0.00,0.00,0,1,9,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (57,'117148','','',1000,1,15,1,0,0,0.0000,29.5000,1,'2013-08-29',0.20,1,0.00,0.00,0.00,0,1,12,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (58,'374002','','',0,2,12,1,0,0,0.0000,34.0000,1,'2013-08-29',25.00,2,0.00,0.00,0.00,0,1,3,1,1,1,0,10.0000,0,'2015-06-12 09:56:25','2015-06-22 12:58:27');
INSERT INTO `ac_products` VALUES (59,'14.50','','',1000,1,11,1,0,0,0.0000,5.0000,1,'2013-08-29',75.00,2,0.00,0.00,0.00,0,1,2,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (60,'112423','','',1000,1,11,1,0,0,0.0000,15.0000,1,'2013-08-30',0.30,2,0.00,0.00,0.00,0,1,2,1,0,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (61,'529071','','',1000,1,15,1,0,0,0.0000,48.0000,1,'2013-08-30',0.13,2,0.00,0.00,0.00,0,1,4,1,0,1,0,29.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (62,'601232','','',1000,1,13,1,0,0,0.0000,14.0000,1,'2013-08-30',0.50,1,0.00,0.00,0.00,0,1,3,1,0,1,0,8.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (63,'374622','','',1000,1,14,1,0,0,0.0000,88.0000,1,'2013-08-30',0.75,1,0.00,0.00,0.00,0,1,3,1,0,1,0,55.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (64,'497303','','',1000,1,13,1,0,0,0.0000,50.0000,1,'2013-08-30',150.00,2,0.00,0.00,0.00,0,1,8,1,1,1,0,33.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (69,'SCND001','','',1000,1,16,1,0,0,0.0000,19.0000,1,'2013-08-30',0.25,1,0.00,0.00,0.00,0,1,6,1,0,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (70,'522823','','',1000,1,14,1,0,0,0.0000,31.0000,1,'2013-08-30',0.25,2,0.00,0.00,0.00,0,1,1,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (71,'PCND001','','',1000,1,17,1,0,0,0.0000,11.4500,1,'2013-08-30',0.30,1,0.00,0.00,0.00,0,1,2,1,1,1,0,5.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (72,'PCND002','','',1000,1,17,1,0,0,0.0000,27.0000,1,'2013-08-30',0.40,1,0.00,0.00,0.00,0,1,4,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (73,'PCND003','','',1000,1,17,1,0,0,0.0000,33.0000,1,'2013-08-30',0.40,1,0.00,0.00,0.00,0,1,1,1,1,1,0,21.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (74,'PCND004','','',10000,1,17,1,0,0,0.0000,4.0000,1,'2013-08-30',0.35,1,0.00,0.00,0.00,0,1,3,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (75,'DMBW0012','','',1000,1,18,1,0,0,0.0000,6.7000,1,'2013-08-30',0.20,1,0.00,0.00,0.00,0,1,1,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (76,'DMBW0013','1235B','',99,1,18,1,0,0,0.0000,7.2000,1,'2013-08-30',0.20,1,0.00,0.00,0.00,0,1,5,1,1,1,0,4.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (77,'DMBW0014','1234B','',1000,1,18,1,0,0,0.0000,6.0000,1,'2013-08-30',0.30,1,0.00,0.00,0.00,0,1,9,1,1,1,0,2.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (78,'Cl0001','','',1000,1,13,1,0,0,0.0000,29.0000,1,'2013-08-30',125.00,2,0.00,0.00,0.00,0,1,10,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (79,'CKGS01','','',1000,1,13,1,0,0,0.0000,36.0000,1,'2013-08-30',250.00,2,0.00,0.00,0.00,0,1,2,1,1,1,0,28.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (80,'GRM001','','',850,1,19,1,0,0,0.0000,59.0000,1,'2013-09-01',80.00,2,0.00,0.00,0.00,0,1,5,1,1,1,0,33.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (81,'GRM002','','',1000,1,19,1,0,0,0.0000,61.0000,1,'2013-09-01',150.00,2,0.00,0.00,0.00,0,1,5,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-22 12:41:31');
INSERT INTO `ac_products` VALUES (82,'GRM003','','',1000,1,19,1,0,0,0.0000,42.0000,1,'2013-09-01',100.00,2,0.00,0.00,0.00,0,1,2,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (83,'GRM004','','',1000,1,19,1,0,0,0.0000,37.5000,1,'2013-09-01',15.00,2,0.00,0.00,0.00,0,1,2,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (84,'GRM005','','',1000,1,19,1,0,0,0.0000,30.0000,1,'2013-09-01',175.00,2,0.00,0.00,0.00,0,1,7,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (85,'Ck0010','','',1000,1,13,1,0,0,0.0000,45.0000,1,'2013-09-01',0.08,5,0.00,0.00,0.00,0,1,3,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (86,'CK0009','','',1,1,13,1,0,0,0.0000,44.1000,1,'2013-09-04',0.17,2,0.00,0.00,0.00,0,1,2,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (87,'CK0010','','',10000,1,13,1,0,0,0.0000,37.5000,1,'2013-09-04',0.20,1,0.00,0.00,0.00,0,1,1,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (88,'CK0011','','',1,1,13,1,0,0,0.0000,31.0000,1,'2013-09-04',340.00,2,0.00,0.00,0.00,0,1,1,1,1,1,0,19.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (89,'CK0012','','',1000,3,13,1,0,0,0.0000,62.0000,1,'2013-09-04',0.12,1,0.00,0.00,0.00,0,1,5,1,1,1,0,40.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (90,'CK0013','','',1000,1,13,1,0,0,0.0000,39.0000,1,'2013-09-04',0.33,2,0.00,0.00,0.00,0,1,2,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (91,'BVLG001','','',1000,1,14,1,0,0,0.0000,29.0000,1,'2013-09-04',0.16,2,0.00,0.00,0.00,0,1,2,1,1,1,0,20.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (92,'BVLG002','','',1000,1,14,1,0,0,0.0000,57.0000,1,'2013-09-04',0.40,5,0.00,0.00,0.00,0,1,7,1,1,1,0,44.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (93,'BVLG003','','',1000,1,14,1,0,0,0.0000,280.0000,1,'2013-09-04',0.30,5,0.00,0.00,0.00,0,1,8,1,1,1,0,100.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (94,'GRMBC001','','',589,1,19,1,0,0,0.0000,263.0000,1,'2013-09-04',0.15,1,0.00,0.00,0.00,0,1,3,1,1,1,0,125.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (95,'GRMBC002','','',100,3,19,1,0,0,0.0000,104.0000,1,'2013-09-04',0.15,1,0.00,0.00,0.00,0,1,5,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (96,'GRMBC003','','',100,1,19,1,0,0,0.0000,82.0000,1,'2013-09-04',80.00,2,0.00,0.00,0.00,0,1,8,1,0,2,0,67.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (97,'GRMBC004','','',1,1,19,1,0,0,0.0000,126.0000,1,'2013-09-04',20.00,2,0.00,0.00,0.00,0,1,9,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (98,'GRMBC005','','',1000,1,19,1,0,0,0.0000,98.0000,1,'2013-09-04',40.00,2,0.00,0.00,0.00,0,1,2,1,1,1,0,87.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (99,'GRMBC006','','',1000,1,19,1,0,0,0.0000,137.0000,1,'2013-09-04',0.09,6,0.00,0.00,0.00,0,1,12,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (100,'GRMBC007','','',1000,1,19,1,0,0,0.0000,10.0000,1,'2013-09-04',15.00,2,0.00,0.00,0.00,0,0,13,1,1,4,0,8.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (101,'Pro-V','','',1000,1,17,1,0,0,0.0000,8.2300,1,'2012-03-13',8.45,6,2.00,3.00,15.00,1,1,35,1,0,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (102,'PRF00269','','',1000,1,20,1,0,0,0.0000,105.0000,1,'2012-03-14',2.50,6,0.00,0.00,0.00,3,1,6,1,0,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (103,'PRF00270','','',100,1,14,1,0,0,0.0000,78.0000,1,'2012-03-14',80.00,2,0.00,0.00,0.00,3,1,4,1,0,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (104,'PRF00271','','',1000,1,13,1,0,0,0.0000,49.0000,1,'2012-03-14',0.00,5,0.00,0.00,0.00,3,1,19,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (105,'PRF00273','','',100,2,14,1,0,0,0.0000,55.0000,0,'2012-03-14',0.00,5,0.00,0.00,0.00,3,1,18,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (106,'PRF00274','','',185,1,14,1,0,0,0.0000,70.0000,1,'2012-03-14',80.00,5,0.00,0.00,0.00,3,1,8,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (107,'PRF00274','','',0,2,15,1,0,0,0.0000,66.0000,1,'2012-03-14',7.00,6,0.00,0.00,0.00,3,1,5,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (108,'PRF00268','','',420,1,15,1,0,0,0.0000,125.0000,1,'2012-03-14',2.00,6,0.00,0.00,0.00,3,1,6,1,1,2,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (109,'PRF00279','','',1,1,15,1,0,0,0.0000,84.0000,1,'2012-03-14',50.00,6,3.00,2.00,10.00,1,1,6,1,1,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (110,'PRF00278','','',1000,1,20,1,0,0,0.0000,90.0000,1,'2012-03-14',0.00,6,0.00,0.00,0.00,3,1,21,1,0,1,0,0.0000,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_products` VALUES (111,'','','',5,1,0,1,0,0,0.0000,85.0000,1,'2015-06-11',0.20,1,23.60,17.80,5.10,1,1,13,1,1,1,0,0.0000,0,'2015-06-12 10:12:48','2015-07-08 12:41:46');
INSERT INTO `ac_products` VALUES (112,'','','',10,1,0,1,0,1,0.0000,16.2000,1,'2015-06-11',0.12,1,0.00,0.00,0.00,3,1,5,1,1,1,0,0.0000,0,'2015-06-12 11:01:46','2015-06-16 11:08:57');
INSERT INTO `ac_products` VALUES (113,'','','',1,1,0,1,0,0,0.0000,9.9900,1,'2015-06-11',0.10,1,12.00,10.00,3.00,1,1,5,1,1,1,0,0.0000,0,'2015-06-12 11:45:22','2015-07-08 12:46:54');
INSERT INTO `ac_products` VALUES (114,'','','',1,1,0,1,0,0,0.0000,7.9900,0,'2015-06-11',0.20,1,15.00,12.00,1.00,1,1,2,1,1,1,0,0.0000,0,'2015-06-12 12:04:56','2015-07-08 10:30:23');
INSERT INTO `ac_products` VALUES (115,'','','',1,1,0,1,0,0,0.0000,110.0000,0,'2015-06-15',300.00,2,26.00,10.00,8.00,1,1,17,1,1,1,0,0.0000,0,'2015-06-16 11:01:53','2015-07-08 10:35:12');
INSERT INTO `ac_products` VALUES (116,'','','',100,1,0,1,0,0,0.0000,26.0000,1,'2015-06-15',1.00,5,8.00,5.00,7.00,3,1,24,1,0,1,0,0.0000,0,'2015-06-16 12:07:12','2015-07-08 10:33:34');
INSERT INTO `ac_products` VALUES (117,'B00OGL2XKO','','',100,1,0,1,0,1,0.0000,78.0000,1,'2015-06-17',0.50,5,7.00,4.00,3.00,3,1,24,1,1,1,0,50.0000,0,'2015-06-18 12:07:38','2015-07-08 12:41:15');
INSERT INTO `ac_products` VALUES (118,'','','',10,1,0,1,0,0,0.0000,26.0000,1,'2015-06-21',222.00,2,5.00,5.00,4.00,3,1,8,1,0,1,0,20.0000,0,'2015-06-22 09:24:11','2015-06-22 09:53:38');
INSERT INTO `ac_products` VALUES (119,'','','',456,1,0,1,0,0,0.0000,9.9900,1,'2015-06-21',400.00,2,0.00,0.00,0.00,3,1,5,1,1,1,0,0.0000,0,'2015-06-22 10:16:06','2015-07-08 10:31:45');
INSERT INTO `ac_products` VALUES (120,'B00UG4NFNY','','',1,1,0,1,0,0,0.0000,6.7500,0,'2015-06-21',100.00,2,0.00,0.00,0.00,3,1,5,1,1,1,0,0.0000,0,'2015-06-22 10:37:31','2015-07-08 10:32:12');
INSERT INTO `ac_products` VALUES (121,'','','',17,1,19,1,0,0,0.0000,32.0000,1,'2015-06-21',120.00,2,0.00,0.00,0.00,3,1,11,1,0,1,10,0.0000,0,'2015-06-22 12:00:53','2015-07-08 12:41:29');
INSERT INTO `ac_products` VALUES (122,'JDSK36','','',155,1,0,1,0,0,0.0000,21.0000,1,'2015-06-21',140.00,2,23.00,20.00,2.00,1,1,5,1,0,1,0,0.0000,0,'2015-06-22 12:28:25','2015-07-08 10:33:00');

--
-- Dumping data for table `products_featured`
--

INSERT INTO `ac_products_featured` VALUES (50);
INSERT INTO `ac_products_featured` VALUES (51);
INSERT INTO `ac_products_featured` VALUES (52);
INSERT INTO `ac_products_featured` VALUES (53);
INSERT INTO `ac_products_featured` VALUES (54);
INSERT INTO `ac_products_featured` VALUES (55);
INSERT INTO `ac_products_featured` VALUES (56);
INSERT INTO `ac_products_featured` VALUES (57);

--
-- Dumping data for table `products_related`
--

INSERT INTO `ac_products_related` VALUES (71,101);
INSERT INTO `ac_products_related` VALUES (100,108);
INSERT INTO `ac_products_related` VALUES (101,71);
INSERT INTO `ac_products_related` VALUES (108,100);
INSERT INTO `ac_products_related` VALUES (115,116);
INSERT INTO `ac_products_related` VALUES (115,118);
INSERT INTO `ac_products_related` VALUES (116,115);
INSERT INTO `ac_products_related` VALUES (116,118);
INSERT INTO `ac_products_related` VALUES (118,115);
INSERT INTO `ac_products_related` VALUES (118,116);

--
-- Dumping data for table `products_to_categories`
--

INSERT INTO `ac_products_to_categories` VALUES (50,40);
INSERT INTO `ac_products_to_categories` VALUES (51,40);
INSERT INTO `ac_products_to_categories` VALUES (52,40);
INSERT INTO `ac_products_to_categories` VALUES (53,36);
INSERT INTO `ac_products_to_categories` VALUES (53,40);
INSERT INTO `ac_products_to_categories` VALUES (54,36);
INSERT INTO `ac_products_to_categories` VALUES (54,39);
INSERT INTO `ac_products_to_categories` VALUES (55,41);
INSERT INTO `ac_products_to_categories` VALUES (56,36);
INSERT INTO `ac_products_to_categories` VALUES (56,39);
INSERT INTO `ac_products_to_categories` VALUES (57,36);
INSERT INTO `ac_products_to_categories` VALUES (57,38);
INSERT INTO `ac_products_to_categories` VALUES (58,36);
INSERT INTO `ac_products_to_categories` VALUES (58,38);
INSERT INTO `ac_products_to_categories` VALUES (59,36);
INSERT INTO `ac_products_to_categories` VALUES (59,41);
INSERT INTO `ac_products_to_categories` VALUES (60,42);
INSERT INTO `ac_products_to_categories` VALUES (61,37);
INSERT INTO `ac_products_to_categories` VALUES (62,49);
INSERT INTO `ac_products_to_categories` VALUES (62,51);
INSERT INTO `ac_products_to_categories` VALUES (63,51);
INSERT INTO `ac_products_to_categories` VALUES (64,49);
INSERT INTO `ac_products_to_categories` VALUES (64,50);
INSERT INTO `ac_products_to_categories` VALUES (65,43);
INSERT INTO `ac_products_to_categories` VALUES (65,47);
INSERT INTO `ac_products_to_categories` VALUES (66,43);
INSERT INTO `ac_products_to_categories` VALUES (66,46);
INSERT INTO `ac_products_to_categories` VALUES (67,43);
INSERT INTO `ac_products_to_categories` VALUES (67,44);
INSERT INTO `ac_products_to_categories` VALUES (68,43);
INSERT INTO `ac_products_to_categories` VALUES (68,48);
INSERT INTO `ac_products_to_categories` VALUES (69,52);
INSERT INTO `ac_products_to_categories` VALUES (69,54);
INSERT INTO `ac_products_to_categories` VALUES (69,64);
INSERT INTO `ac_products_to_categories` VALUES (70,52);
INSERT INTO `ac_products_to_categories` VALUES (70,53);
INSERT INTO `ac_products_to_categories` VALUES (71,52);
INSERT INTO `ac_products_to_categories` VALUES (71,54);
INSERT INTO `ac_products_to_categories` VALUES (72,54);
INSERT INTO `ac_products_to_categories` VALUES (73,54);
INSERT INTO `ac_products_to_categories` VALUES (74,52);
INSERT INTO `ac_products_to_categories` VALUES (74,53);
INSERT INTO `ac_products_to_categories` VALUES (75,58);
INSERT INTO `ac_products_to_categories` VALUES (75,63);
INSERT INTO `ac_products_to_categories` VALUES (76,58);
INSERT INTO `ac_products_to_categories` VALUES (76,60);
INSERT INTO `ac_products_to_categories` VALUES (77,58);
INSERT INTO `ac_products_to_categories` VALUES (77,60);
INSERT INTO `ac_products_to_categories` VALUES (77,63);
INSERT INTO `ac_products_to_categories` VALUES (78,58);
INSERT INTO `ac_products_to_categories` VALUES (78,59);
INSERT INTO `ac_products_to_categories` VALUES (78,62);
INSERT INTO `ac_products_to_categories` VALUES (79,50);
INSERT INTO `ac_products_to_categories` VALUES (79,62);
INSERT INTO `ac_products_to_categories` VALUES (80,49);
INSERT INTO `ac_products_to_categories` VALUES (80,51);
INSERT INTO `ac_products_to_categories` VALUES (81,51);
INSERT INTO `ac_products_to_categories` VALUES (82,51);
INSERT INTO `ac_products_to_categories` VALUES (82,59);
INSERT INTO `ac_products_to_categories` VALUES (83,51);
INSERT INTO `ac_products_to_categories` VALUES (84,49);
INSERT INTO `ac_products_to_categories` VALUES (84,50);
INSERT INTO `ac_products_to_categories` VALUES (85,49);
INSERT INTO `ac_products_to_categories` VALUES (85,50);
INSERT INTO `ac_products_to_categories` VALUES (86,51);
INSERT INTO `ac_products_to_categories` VALUES (86,59);
INSERT INTO `ac_products_to_categories` VALUES (87,51);
INSERT INTO `ac_products_to_categories` VALUES (87,59);
INSERT INTO `ac_products_to_categories` VALUES (88,50);
INSERT INTO `ac_products_to_categories` VALUES (89,49);
INSERT INTO `ac_products_to_categories` VALUES (89,50);
INSERT INTO `ac_products_to_categories` VALUES (90,50);
INSERT INTO `ac_products_to_categories` VALUES (90,59);
INSERT INTO `ac_products_to_categories` VALUES (91,46);
INSERT INTO `ac_products_to_categories` VALUES (92,46);
INSERT INTO `ac_products_to_categories` VALUES (93,43);
INSERT INTO `ac_products_to_categories` VALUES (93,46);
INSERT INTO `ac_products_to_categories` VALUES (94,45);
INSERT INTO `ac_products_to_categories` VALUES (95,45);
INSERT INTO `ac_products_to_categories` VALUES (95,60);
INSERT INTO `ac_products_to_categories` VALUES (96,47);
INSERT INTO `ac_products_to_categories` VALUES (96,60);
INSERT INTO `ac_products_to_categories` VALUES (97,47);
INSERT INTO `ac_products_to_categories` VALUES (98,61);
INSERT INTO `ac_products_to_categories` VALUES (99,42);
INSERT INTO `ac_products_to_categories` VALUES (100,36);
INSERT INTO `ac_products_to_categories` VALUES (100,41);
INSERT INTO `ac_products_to_categories` VALUES (101,54);
INSERT INTO `ac_products_to_categories` VALUES (102,49);
INSERT INTO `ac_products_to_categories` VALUES (102,50);
INSERT INTO `ac_products_to_categories` VALUES (104,49);
INSERT INTO `ac_products_to_categories` VALUES (104,50);
INSERT INTO `ac_products_to_categories` VALUES (105,50);
INSERT INTO `ac_products_to_categories` VALUES (106,49);
INSERT INTO `ac_products_to_categories` VALUES (106,50);
INSERT INTO `ac_products_to_categories` VALUES (107,45);
INSERT INTO `ac_products_to_categories` VALUES (107,63);
INSERT INTO `ac_products_to_categories` VALUES (108,37);
INSERT INTO `ac_products_to_categories` VALUES (108,39);
INSERT INTO `ac_products_to_categories` VALUES (108,41);
INSERT INTO `ac_products_to_categories` VALUES (108,45);
INSERT INTO `ac_products_to_categories` VALUES (109,46);
INSERT INTO `ac_products_to_categories` VALUES (110,50);
INSERT INTO `ac_products_to_categories` VALUES (111,65);
INSERT INTO `ac_products_to_categories` VALUES (111,66);
INSERT INTO `ac_products_to_categories` VALUES (112,65);
INSERT INTO `ac_products_to_categories` VALUES (112,67);
INSERT INTO `ac_products_to_categories` VALUES (113,65);
INSERT INTO `ac_products_to_categories` VALUES (113,67);
INSERT INTO `ac_products_to_categories` VALUES (114,65);
INSERT INTO `ac_products_to_categories` VALUES (114,67);
INSERT INTO `ac_products_to_categories` VALUES (115,68);
INSERT INTO `ac_products_to_categories` VALUES (115,69);
INSERT INTO `ac_products_to_categories` VALUES (116,68);
INSERT INTO `ac_products_to_categories` VALUES (116,69);
INSERT INTO `ac_products_to_categories` VALUES (117,68);
INSERT INTO `ac_products_to_categories` VALUES (117,69);
INSERT INTO `ac_products_to_categories` VALUES (118,68);
INSERT INTO `ac_products_to_categories` VALUES (118,69);
INSERT INTO `ac_products_to_categories` VALUES (119,68);
INSERT INTO `ac_products_to_categories` VALUES (119,70);
INSERT INTO `ac_products_to_categories` VALUES (120,68);
INSERT INTO `ac_products_to_categories` VALUES (120,70);
INSERT INTO `ac_products_to_categories` VALUES (121,68);
INSERT INTO `ac_products_to_categories` VALUES (121,70);
INSERT INTO `ac_products_to_categories` VALUES (122,68);
INSERT INTO `ac_products_to_categories` VALUES (122,70);

--
-- Dumping data for table `products_to_downloads`
--

INSERT INTO `ac_products_to_downloads` VALUES (111,1);
INSERT INTO `ac_products_to_downloads` VALUES (111,2);

--
-- Dumping data for table `products_to_stores`
--

INSERT INTO `ac_products_to_stores` VALUES (50,0);
INSERT INTO `ac_products_to_stores` VALUES (51,0);
INSERT INTO `ac_products_to_stores` VALUES (52,0);
INSERT INTO `ac_products_to_stores` VALUES (53,0);
INSERT INTO `ac_products_to_stores` VALUES (54,0);
INSERT INTO `ac_products_to_stores` VALUES (55,0);
INSERT INTO `ac_products_to_stores` VALUES (56,0);
INSERT INTO `ac_products_to_stores` VALUES (57,0);
INSERT INTO `ac_products_to_stores` VALUES (58,0);
INSERT INTO `ac_products_to_stores` VALUES (59,0);
INSERT INTO `ac_products_to_stores` VALUES (60,0);
INSERT INTO `ac_products_to_stores` VALUES (61,0);
INSERT INTO `ac_products_to_stores` VALUES (62,0);
INSERT INTO `ac_products_to_stores` VALUES (63,0);
INSERT INTO `ac_products_to_stores` VALUES (64,0);
INSERT INTO `ac_products_to_stores` VALUES (65,0);
INSERT INTO `ac_products_to_stores` VALUES (66,0);
INSERT INTO `ac_products_to_stores` VALUES (67,0);
INSERT INTO `ac_products_to_stores` VALUES (68,0);
INSERT INTO `ac_products_to_stores` VALUES (69,0);
INSERT INTO `ac_products_to_stores` VALUES (70,0);
INSERT INTO `ac_products_to_stores` VALUES (71,0);
INSERT INTO `ac_products_to_stores` VALUES (72,0);
INSERT INTO `ac_products_to_stores` VALUES (73,0);
INSERT INTO `ac_products_to_stores` VALUES (74,0);
INSERT INTO `ac_products_to_stores` VALUES (75,0);
INSERT INTO `ac_products_to_stores` VALUES (76,0);
INSERT INTO `ac_products_to_stores` VALUES (77,0);
INSERT INTO `ac_products_to_stores` VALUES (78,0);
INSERT INTO `ac_products_to_stores` VALUES (79,0);
INSERT INTO `ac_products_to_stores` VALUES (80,0);
INSERT INTO `ac_products_to_stores` VALUES (81,0);
INSERT INTO `ac_products_to_stores` VALUES (82,0);
INSERT INTO `ac_products_to_stores` VALUES (83,0);
INSERT INTO `ac_products_to_stores` VALUES (84,0);
INSERT INTO `ac_products_to_stores` VALUES (85,0);
INSERT INTO `ac_products_to_stores` VALUES (86,0);
INSERT INTO `ac_products_to_stores` VALUES (87,0);
INSERT INTO `ac_products_to_stores` VALUES (88,0);
INSERT INTO `ac_products_to_stores` VALUES (89,0);
INSERT INTO `ac_products_to_stores` VALUES (90,0);
INSERT INTO `ac_products_to_stores` VALUES (91,0);
INSERT INTO `ac_products_to_stores` VALUES (92,0);
INSERT INTO `ac_products_to_stores` VALUES (93,0);
INSERT INTO `ac_products_to_stores` VALUES (94,0);
INSERT INTO `ac_products_to_stores` VALUES (95,0);
INSERT INTO `ac_products_to_stores` VALUES (96,0);
INSERT INTO `ac_products_to_stores` VALUES (97,0);
INSERT INTO `ac_products_to_stores` VALUES (98,0);
INSERT INTO `ac_products_to_stores` VALUES (99,0);
INSERT INTO `ac_products_to_stores` VALUES (100,0);
INSERT INTO `ac_products_to_stores` VALUES (101,0);
INSERT INTO `ac_products_to_stores` VALUES (102,0);
INSERT INTO `ac_products_to_stores` VALUES (103,0);
INSERT INTO `ac_products_to_stores` VALUES (104,0);
INSERT INTO `ac_products_to_stores` VALUES (105,0);
INSERT INTO `ac_products_to_stores` VALUES (106,0);
INSERT INTO `ac_products_to_stores` VALUES (107,0);
INSERT INTO `ac_products_to_stores` VALUES (108,0);
INSERT INTO `ac_products_to_stores` VALUES (109,0);
INSERT INTO `ac_products_to_stores` VALUES (110,0);
INSERT INTO `ac_products_to_stores` VALUES (111,0);
INSERT INTO `ac_products_to_stores` VALUES (112,0);
INSERT INTO `ac_products_to_stores` VALUES (113,0);
INSERT INTO `ac_products_to_stores` VALUES (114,0);
INSERT INTO `ac_products_to_stores` VALUES (115,0);
INSERT INTO `ac_products_to_stores` VALUES (116,0);
INSERT INTO `ac_products_to_stores` VALUES (117,0);
INSERT INTO `ac_products_to_stores` VALUES (118,0);
INSERT INTO `ac_products_to_stores` VALUES (119,0);
INSERT INTO `ac_products_to_stores` VALUES (120,0);
INSERT INTO `ac_products_to_stores` VALUES (121,0);
INSERT INTO `ac_products_to_stores` VALUES (122,0);

--
-- Dumping data for table `resource_descriptions`
--

INSERT INTO `ac_resource_descriptions` VALUES (100010,1,'demo_product15_1.jpg','','','18/6a/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100012,1,'demo_product07.jpg','','','18/6a/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100011,1,'demo_product15.jpg','','','18/6a/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100007,1,'demo_product14_2.jpg','','','18/6a/7.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100008,1,'demo_product14.jpg','','','18/6a/8.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100009,1,'demo_product14_1.jpg','','','18/6a/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100013,1,'demo_product18.jpg','','','18/6a/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100014,1,'demo_product30.jpg','','','18/6a/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100015,1,'demo_product30_2.jpg','','','18/6a/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100016,1,'demo_product30_1.jpg','','','18/6b/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100017,1,'demo_product30_3.jpg','','','18/6b/1.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100018,1,'demo_product34.jpg','','','18/6b/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100019,1,'demo_product34_2.jpg','','','18/6b/3.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100020,1,'demo_product34_1.jpg','','','18/6b/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100021,1,'demo_product32.jpg','','','18/6b/5.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100022,1,'demo_product32.png','','','18/6b/6.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100023,1,'demo_product33.jpg','','','18/6b/7.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100024,1,'demo_product32_1.jpg','','','18/6b/8.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100249,1,'demo_product31.png','Armani Eau de Toilette','','18/79/9.png','','2015-06-22 12:38:44','2015-06-22 12:39:08');
INSERT INTO `ac_resource_descriptions` VALUES (100026,1,'demo_product02.jpg','','','18/6b/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100027,1,'demo_product02_2.jpg','','','18/6b/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100028,1,'demo_product02_1.jpg','','','18/6b/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100029,1,'demo_product02_3.jpg','','','18/6b/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100030,1,'demo_product42.jpg','','','18/6b/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100031,1,'demo_product22.jpg','','','18/6b/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100032,1,'demo_product11_1.jpg','','','18/6c/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100033,1,'demo_product11_2.jpg','','','18/6c/1.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100034,1,'demo_product11.jpg','','','18/6c/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100035,1,'demo_product43.jpg','','','18/6c/3.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100036,1,'demo_product24.jpg','','','18/6c/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100037,1,'demo_product06_6.jpg','','','18/6c/5.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100038,1,'demo_product06_2.jpg','','','18/6c/6.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100039,1,'demo_product06_1.jpg','','','18/6c/7.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100040,1,'demo_product06.jpg','','','18/6c/8.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100041,1,'demo_product06_4.jpg','','','18/6c/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100042,1,'demo_product06_3.jpg','','','18/6c/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100043,1,'demo_product06_5.jpg','','','18/6c/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100044,1,'demo_product25_1.jpg','','','18/6c/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100045,1,'demo_product25_2.jpg','','','18/6c/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100046,1,'demo_product25.jpg','','','18/6c/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100047,1,'demo_product20.jpg','','','18/6c/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100048,1,'demo_product36.jpg','','','18/6d/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100049,1,'demo_product47.png','','','18/6d/1.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100050,1,'demo_product46.jpg','','','18/6d/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100051,1,'demo_product46.png','','','18/6d/3.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100052,1,'demo_product17.jpg','','','18/6d/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100053,1,'demo_product49_1.png','','','18/6d/5.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100054,1,'demo_product35_1.jpg','','','18/6d/6.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100055,1,'demo_product35_2.jpg','','','18/6d/7.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100056,1,'demo_product35.jpg','','','18/6d/8.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100057,1,'demo_product23.jpg','','','18/6d/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100058,1,'demo_product41.jpg','','','18/6d/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100059,1,'demo_product09_4.jpg','','','18/6d/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100060,1,'demo_product09_1.jpg','','','18/6d/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100061,1,'demo_product09.jpg','','','18/6d/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100062,1,'demo_product09_3.jpg','','','18/6d/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100063,1,'demo_product09_2.jpg','','','18/6d/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100064,1,'demo_product37.jpg','','','18/6e/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100065,1,'demo_product26_2.jpg','','','18/6e/1.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100066,1,'demo_product26_3.jpg','','','18/6e/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100067,1,'demo_product26.jpg','','','18/6e/3.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100068,1,'demo_product26_1.jpg','','','18/6e/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100069,1,'demo_product27_1.jpg','','','18/6e/5.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100070,1,'demo_product27.jpg','','','18/6e/6.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100071,1,'demo_product10.jpg','','','18/6e/7.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100072,1,'demo_product10_1.jpg','','','18/6e/8.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100073,1,'demo_product10_2.jpg','','','18/6e/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100074,1,'demo_product10_3.jpg','','','18/6e/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100075,1,'demo_product44.jpg','','','18/6e/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100076,1,'demo_product40_1.jpg','','','18/6e/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100077,1,'demo_product40.jpg','','','18/6e/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100078,1,'demo_product40_2.jpg','','','18/6e/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100079,1,'demo_product21.jpg','','','18/6e/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100080,1,'demo_product13_2.jpg','','','18/6f/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100081,1,'demo_product13_1.jpg','','','18/6f/1.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100082,1,'demo_product19.jpg','','','18/6f/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100083,1,'demo_product39.jpg','','','18/6f/3.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100084,1,'demo_product39_3.jpg','','','18/6f/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100085,1,'demo_product39_2.jpg','','','18/6f/5.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100086,1,'demo_product39_1.jpg','','','18/6f/6.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100087,1,'demo_product45.png','','','18/6f/7.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100088,1,'demo_product48.png','','','18/6f/8.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100089,1,'demo_product01.jpg','','','18/6f/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100090,1,'demo_product50.jpg','','','18/6f/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100091,1,'demo_product16_1.jpg','','','18/6f/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100092,1,'demo_product16.jpg','','','18/6f/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100093,1,'demo_product16_2.jpg','','','18/6f/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100094,1,'demo_product03.jpg','','','18/6f/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100095,1,'demo_product03_1.jpg','','','18/6f/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100096,1,'demo_product03_2.jpg','','','18/70/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100097,1,'demo_product08.jpg','','','18/70/1.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100098,1,'demo_product08_2.jpg','','','18/70/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100099,1,'demo_product08_3.jpg','','','18/70/3.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100100,1,'demo_product08_1.jpg','','','18/70/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100101,1,'demo_product05.jpg','','','18/70/5.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100102,1,'demo_product29_2.jpg','','','18/70/6.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100103,1,'demo_product29.jpg','','','18/70/7.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100104,1,'demo_product29_1.jpg','','','18/70/8.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100105,1,'demo_product29.jpg','','','18/70/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100106,1,'demo_product29_2.jpg','','','18/70/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100107,1,'demo_product29_1.jpg','','','18/70/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100108,1,'demo_product28_1.jpg','','','18/70/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100109,1,'demo_product28.jpg','','','18/70/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100110,1,'demo_product28_2.jpg','','','18/70/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100111,1,'demo_product38.jpg','','','18/70/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100112,1,'demo_product12.jpg','','','18/71/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100113,1,'demo_product12.png','','','18/71/1.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100114,1,'mf_sephora_ba_logo_black.jpg','','','18/71/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100115,1,'mf_Bvlgari.jpg','','','18/71/3.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100116,1,'mf_calvin_klein.jpg','','','18/71/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100117,1,'mf_benefit_logo_black.jpg','','','18/71/5.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100118,1,'mf_mac_logo.jpg','','','18/71/6.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100119,1,'mf_lancome_logo.gif','','','18/71/7.gif','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100120,1,'mf_pantene_logo.jpg','','','18/71/8.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100121,1,'mf_dove_logo.jpg','','','18/71/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100122,1,'mf_armani_logo.gif','','','18/71/a.gif','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100123,1,'demo_product_23.jpg','','','18/71/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100124,1,'demo_product_04.jpg','','','18/71/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100125,1,'demo_product_15.jpg','','','18/71/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100126,1,'demo_product_14_2.jpg','','','18/71/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100127,1,'demo_product_31.jpg','','','18/71/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100128,1,'demo_product_34.jpg','','','18/72/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100129,1,'demo_product_30_2.jpg','','','18/72/1.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100130,1,'demo_product_24.jpg','','','18/72/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100131,1,'demo_product_23.jpg','','','18/72/3.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100132,1,'demo_product_05.jpg','','','18/72/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100133,1,'demo_product_07.jpg','','','18/72/5.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100134,1,'demo_product_08_3.jpg','','','18/72/6.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100135,1,'demo_product_10_2.jpg','','','18/72/7.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100136,1,'demo_product_47.png','','','18/72/8.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100137,1,'demo_product_11_2.jpg','','','18/72/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100138,1,'demo_product_40_2.jpg','','','18/72/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100139,1,'demo_product_44.jpg','','','18/72/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100140,1,'demo_product_29.jpg','','','18/72/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100141,1,'demo_product_27.jpg','','','18/72/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100142,1,'demo_product_42.jpg','','','18/72/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100143,1,'demo_product_46.jpg','','','18/72/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100144,1,'demo_product_18.jpg','','','18/73/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100145,1,'demo_product_37.jpg','','','18/73/1.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100146,1,'demo_product_49_1.png','','','18/73/2.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100147,1,'store_logo.png','','','18/73/3.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100148,1,'favicon.png','','','18/73/4.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100150,1,'demo_product51.png','','','18/73/6.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100153,1,'demo_mf_gucci.jpg','','','18/73/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100154,1,'demo_product52_1.jpg','','','18/73/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100155,1,'demo_product52_2.png','','','18/73/b.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100156,1,'demo_product52_3.png','','','18/73/c.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100157,1,'demo_product53_3.jpg','','','18/73/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100159,1,'demo_product53_2.png','','','18/73/f.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100160,1,'demo_product54_1.jpg','','','18/74/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100162,1,'demo_product55_1.jpg','','','18/74/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100163,1,'demo_product56_3.jpg','','','18/74/3.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100164,1,'demo_product56_2.jpg','','','18/74/4.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100165,1,'demo_product56_1.jpg','','','18/74/5.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100166,1,'demo_product57_1.jpg','','','18/74/6.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100167,1,'demo_product57_2.jpg','','','18/74/7.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100168,1,'demo_product58_1.jpg','','','18/74/8.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100169,1,'demo_product58_3.jpg','','','18/74/9.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100170,1,'demo_product58_4.jpg','','','18/74/a.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100171,1,'demo_product58_2.jpg','','','18/74/b.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100172,1,'Visionnaire.zip','','','18/74/c.zip','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100173,1,'demo_product59_1.jpg','','','18/74/d.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100174,1,'demo_product60_1.jpg','','','18/74/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100175,1,'demo_product60_2.jpg','','','18/74/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100176,1,'demo_product60_5.jpg','','','18/75/0.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100178,1,'abantecart video','','','','<object width=\"640\" height=\"360\"><param name=\"movie\" value=\"http://www.youtube.com/v/IQ5SLJUWbdA\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/IQ5SLJUWbdA\" type=\"application/x-shockwave-flash\" width=\"640\" height=\"360\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed></object>','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100188,1,'smbanner.jpg','','','18/75/c.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100190,1,'AM_mc_vs_dc_ae_319x110.jpg','PayPal Credit Cards','PayPal logo with supported Credit Cards','18/75/e.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100191,1,'AM_SbyPP_mc_vs_dc_ae_319x110.jpg','PayPal Secure Payments','Secure Payments by PayPal logo','18/75/f.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100192,1,'bdg_payments_by_pp_2line_165x56.png','Payments by PayPal','Payments by PayPal Logo','18/76/0.png','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100193,1,'pp_cc_mark_76x48.jpg','PayPal Icon','PayPal Small Icon','18/76/1.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100194,1,'banner_fallback.jpg','Fall back banner for small screen resolutions','Fall back banner for small screen resolutions','18/76/2.jpg','','2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_descriptions` VALUES (100195,1,'audiocd1.jpg','','','18/76/3.jpg','','2015-06-12 10:14:20','2015-06-12 10:14:20');
INSERT INTO `ac_resource_descriptions` VALUES (100196,1,'Marketplace-and_Extension-Installation-in-AbanteCart.mp3','Audio CD 1','','18/76/4.mp3','','2015-06-12 10:23:22','2015-06-12 10:23:44');
INSERT INTO `ac_resource_descriptions` VALUES (100197,1,'sample.mp3','','','18/76/5.mp3','','2015-06-12 10:31:41','2015-06-12 10:31:41');
INSERT INTO `ac_resource_descriptions` VALUES (100198,1,'paperback1.jpg','','','18/76/6.jpg','','2015-06-12 11:03:44','2015-06-12 11:03:44');
INSERT INTO `ac_resource_descriptions` VALUES (100199,1,'cdaudio.png','category image','','18/76/7.png','','2015-06-12 11:27:50','2015-06-12 11:28:00');
INSERT INTO `ac_resource_descriptions` VALUES (100200,1,'book2.png','','','18/76/8.png','','2015-06-12 11:30:19','2015-06-12 11:30:19');
INSERT INTO `ac_resource_descriptions` VALUES (100201,1,'papertowns2.jpg','','','18/76/9.jpg','','2015-06-12 11:49:01','2015-06-12 11:49:01');
INSERT INTO `ac_resource_descriptions` VALUES (100202,1,'paper2.jpg','','','18/76/a.jpg','','2015-06-12 11:49:25','2015-06-12 11:49:25');
INSERT INTO `ac_resource_descriptions` VALUES (100203,1,'paper-cover_allegiant.jpg','','','18/76/b.jpg','','2015-06-12 12:06:33','2015-06-12 12:06:33');
INSERT INTO `ac_resource_descriptions` VALUES (100204,1,'Fiorella-Purple-Peep-Toes-1.jpg','','','18/76/c.jpg','','2015-06-16 11:31:41','2015-06-16 11:31:41');
INSERT INTO `ac_resource_descriptions` VALUES (100206,1,'Fiorella-Purple-Peep-Toes-2.jpg','','','18/76/e.jpg','','2015-06-16 11:58:34','2015-06-16 11:58:34');
INSERT INTO `ac_resource_descriptions` VALUES (100207,1,'new-ladies-black1.jpg','','','18/76/f.jpg','','2015-06-16 12:28:57','2015-06-16 12:28:57');
INSERT INTO `ac_resource_descriptions` VALUES (100208,1,'new-ladies-black2.jpg','','','18/77/0.jpg','','2015-06-16 12:28:59','2015-06-16 12:28:59');
INSERT INTO `ac_resource_descriptions` VALUES (100209,1,'new-ladies-black3.jpg','','','18/77/1.jpg','','2015-06-16 12:29:03','2015-06-16 12:29:03');
INSERT INTO `ac_resource_descriptions` VALUES (100210,1,'new-ladies-black4.jpg','','','18/77/2.jpg','','2015-06-16 12:29:05','2015-06-16 12:29:05');
INSERT INTO `ac_resource_descriptions` VALUES (100211,1,'new-ladies-red6.jpg','','','18/77/3.jpg','','2015-06-17 12:16:43','2015-06-17 12:16:43');
INSERT INTO `ac_resource_descriptions` VALUES (100212,1,'new-ladies-red5.jpg','','','18/77/4.jpg','','2015-06-17 12:16:48','2015-06-17 12:16:48');
INSERT INTO `ac_resource_descriptions` VALUES (100213,1,'new-ladies-red4.jpg','','','18/77/5.jpg','','2015-06-17 12:16:51','2015-06-17 12:16:51');
INSERT INTO `ac_resource_descriptions` VALUES (100214,1,'new-ladies-red3.jpg','','','18/77/6.jpg','','2015-06-17 12:16:53','2015-06-17 12:16:53');
INSERT INTO `ac_resource_descriptions` VALUES (100215,1,'new-ladies-red2.jpg','','','18/77/7.jpg','','2015-06-17 12:16:56','2015-06-17 12:16:56');
INSERT INTO `ac_resource_descriptions` VALUES (100216,1,'new-ladies-red1.jpg','','','18/77/8.jpg','','2015-06-17 12:16:58','2015-06-17 12:16:58');
INSERT INTO `ac_resource_descriptions` VALUES (100217,1,'new-ladies-white5.jpg','','','18/77/9.jpg','','2015-06-17 12:17:32','2015-06-17 12:17:32');
INSERT INTO `ac_resource_descriptions` VALUES (100218,1,'new-ladies-white4.jpg','','','18/77/a.jpg','','2015-06-17 12:17:35','2015-06-17 12:17:35');
INSERT INTO `ac_resource_descriptions` VALUES (100219,1,'new-ladies-white3.jpg','','','18/77/b.jpg','','2015-06-17 12:17:37','2015-06-17 12:17:37');
INSERT INTO `ac_resource_descriptions` VALUES (100220,1,'new-ladies-white2.jpg','','','18/77/c.jpg','','2015-06-17 12:17:39','2015-06-17 12:17:39');
INSERT INTO `ac_resource_descriptions` VALUES (100221,1,'new-ladies-white1.jpg','','','18/77/d.jpg','','2015-06-17 12:17:41','2015-06-17 12:17:41');
INSERT INTO `ac_resource_descriptions` VALUES (100222,1,'new-ladies-red6.jpg','','','18/77/e.jpg','','2015-06-17 12:19:41','2015-06-17 12:19:41');
INSERT INTO `ac_resource_descriptions` VALUES (100223,1,'new-ladies-red5.jpg','','','18/77/f.jpg','','2015-06-17 12:19:44','2015-06-17 12:19:44');
INSERT INTO `ac_resource_descriptions` VALUES (100224,1,'new-ladies-red4.jpg','','','18/78/0.jpg','','2015-06-17 12:19:47','2015-06-17 12:19:47');
INSERT INTO `ac_resource_descriptions` VALUES (100225,1,'new-ladies-red3.jpg','','','18/78/1.jpg','','2015-06-17 12:19:50','2015-06-17 12:19:50');
INSERT INTO `ac_resource_descriptions` VALUES (100226,1,'new-ladies-red2.jpg','','','18/78/2.jpg','','2015-06-17 12:19:52','2015-06-17 12:19:52');
INSERT INTO `ac_resource_descriptions` VALUES (100227,1,'new-ladies-red1.jpg','','','18/78/3.jpg','','2015-06-17 12:19:55','2015-06-17 12:19:55');
INSERT INTO `ac_resource_descriptions` VALUES (100228,1,'shoe_1.jpg','','','18/78/4.jpg','','2015-06-18 12:31:08','2015-06-18 12:31:08');
INSERT INTO `ac_resource_descriptions` VALUES (100229,1,'shoe_1a.jpg','','','18/78/5.jpg','','2015-06-18 12:31:11','2015-06-18 12:31:11');
INSERT INTO `ac_resource_descriptions` VALUES (100230,1,'shoe_1b.jpg','','','18/78/6.jpg','','2015-06-18 12:31:17','2015-06-18 12:31:17');
INSERT INTO `ac_resource_descriptions` VALUES (100231,1,'shoe_1c.jpg','','','18/78/7.jpg','','2015-06-18 12:31:21','2015-06-18 12:31:21');
INSERT INTO `ac_resource_descriptions` VALUES (100232,1,'shoe_1d.jpg','','','18/78/8.jpg','','2015-06-18 12:31:27','2015-06-18 12:31:27');
INSERT INTO `ac_resource_descriptions` VALUES (100233,1,'shoe_1e.jpg','','','18/78/9.jpg','','2015-06-18 12:31:37','2015-06-18 12:31:37');
INSERT INTO `ac_resource_descriptions` VALUES (100235,1,'shoeblack_1.jpg','','','18/78/b.jpg','','2015-06-22 09:33:39','2015-06-22 09:33:39');
INSERT INTO `ac_resource_descriptions` VALUES (100236,1,'shoeblack_2.jpg','','','18/78/c.jpg','','2015-06-22 09:34:26','2015-06-22 09:34:26');
INSERT INTO `ac_resource_descriptions` VALUES (100237,1,'shoeblack_3.jpg','','','18/78/d.jpg','','2015-06-22 09:34:28','2015-06-22 09:34:28');
INSERT INTO `ac_resource_descriptions` VALUES (100238,1,'shoeblack_5.jpg','','','18/78/e.jpg','','2015-06-22 09:34:30','2015-06-22 09:34:30');
INSERT INTO `ac_resource_descriptions` VALUES (100239,1,'shoeblack_6.jpg','','','18/78/f.jpg','','2015-06-22 09:35:57','2015-06-22 09:35:57');
INSERT INTO `ac_resource_descriptions` VALUES (100240,1,'shoeblack_7.jpg','','','18/79/0.jpg','','2015-06-22 09:35:59','2015-06-22 09:35:59');
INSERT INTO `ac_resource_descriptions` VALUES (100241,1,'t-shirt.jpg','','','18/79/1.jpg','','2015-06-22 10:17:15','2015-06-22 10:17:15');
INSERT INTO `ac_resource_descriptions` VALUES (100242,1,'t-shirt-2.jpg','','','18/79/2.jpg','','2015-06-22 11:28:33','2015-06-22 11:28:33');
INSERT INTO `ac_resource_descriptions` VALUES (100243,1,'t-shirt-3.jpg','','','18/79/3.jpg','','2015-06-22 12:03:28','2015-06-22 12:03:28');
INSERT INTO `ac_resource_descriptions` VALUES (100244,1,'t-shirt-3a.jpg','','','18/79/4.jpg','','2015-06-22 12:04:17','2015-06-22 12:04:17');
INSERT INTO `ac_resource_descriptions` VALUES (100245,1,'t-shirt-4c.jpg','','','18/79/5.jpg','','2015-06-22 12:32:10','2015-06-22 12:32:10');
INSERT INTO `ac_resource_descriptions` VALUES (100246,1,'t-shirt-4b.jpg','','','18/79/6.jpg','','2015-06-22 12:32:14','2015-06-22 12:32:14');
INSERT INTO `ac_resource_descriptions` VALUES (100247,1,'t-shirt-4a.jpg','','','18/79/7.jpg','','2015-06-22 12:32:17','2015-06-22 12:32:17');
INSERT INTO `ac_resource_descriptions` VALUES (100248,1,'t-shirt-4.jpg','','','18/79/8.jpg','','2015-06-22 12:32:21','2015-06-22 12:32:21');
INSERT INTO `ac_resource_descriptions` VALUES (100250,1,'lancome-mascara.jpg','','','18/79/a.jpg','','2015-06-22 12:48:03','2015-06-22 12:48:03');

--
-- Dumping data for table `resource_library`
--

INSERT INTO `ac_resource_library` VALUES (100010,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100012,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100011,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100007,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100008,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100009,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100013,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100014,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100015,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100016,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100017,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100018,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100019,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100020,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100021,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100022,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100023,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100024,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100249,1,'2015-06-22 12:38:44','2015-06-22 12:38:44');
INSERT INTO `ac_resource_library` VALUES (100026,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100027,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100028,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100029,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100030,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100031,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100032,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100033,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100034,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100035,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100036,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100037,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100038,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100039,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100040,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100041,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100042,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100043,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100044,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100045,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100046,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100047,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100048,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100049,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100050,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100051,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100052,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100053,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100054,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100055,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100056,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100057,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100058,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100059,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100060,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100061,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100062,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100063,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100064,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100065,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100066,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100067,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100068,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100069,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100070,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100071,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100072,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100073,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100074,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100075,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100076,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100077,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100078,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100079,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100080,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100081,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100082,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100083,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100084,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100085,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100086,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100087,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100088,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100089,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100090,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100091,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100092,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100093,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100094,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100095,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100096,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100097,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100098,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100099,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100100,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100101,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100102,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100103,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100104,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100105,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100106,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100107,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100108,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100109,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100110,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100111,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100112,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100113,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100114,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100115,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100116,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100117,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100118,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100119,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100120,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100121,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100122,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100123,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100124,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100125,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100126,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100127,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100128,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100129,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100130,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100131,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100132,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100133,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100134,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100135,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100136,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100137,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100138,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100139,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100140,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100141,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100142,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100143,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100144,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100145,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100146,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100147,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100148,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100150,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100178,3,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100153,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100154,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100155,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100156,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100157,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100159,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100160,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100162,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100163,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100164,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100165,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100166,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100167,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100168,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100169,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100170,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100171,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100172,5,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100173,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100174,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100175,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100176,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100188,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100190,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100191,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100192,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100193,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100194,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_library` VALUES (100195,1,'2015-06-12 10:14:20','2015-06-12 10:14:20');
INSERT INTO `ac_resource_library` VALUES (100196,6,'2015-06-12 10:23:22','2015-06-12 10:23:22');
INSERT INTO `ac_resource_library` VALUES (100197,6,'2015-06-12 10:31:41','2015-06-12 10:31:41');
INSERT INTO `ac_resource_library` VALUES (100198,1,'2015-06-12 11:03:44','2015-06-12 11:03:44');
INSERT INTO `ac_resource_library` VALUES (100199,1,'2015-06-12 11:27:50','2015-06-12 11:27:50');
INSERT INTO `ac_resource_library` VALUES (100200,1,'2015-06-12 11:30:19','2015-06-12 11:30:19');
INSERT INTO `ac_resource_library` VALUES (100201,1,'2015-06-12 11:49:01','2015-06-12 11:49:01');
INSERT INTO `ac_resource_library` VALUES (100202,1,'2015-06-12 11:49:25','2015-06-12 11:49:25');
INSERT INTO `ac_resource_library` VALUES (100203,1,'2015-06-12 12:06:33','2015-06-12 12:06:33');
INSERT INTO `ac_resource_library` VALUES (100204,1,'2015-06-16 11:31:41','2015-06-16 11:31:41');
INSERT INTO `ac_resource_library` VALUES (100206,1,'2015-06-16 11:58:34','2015-06-16 11:58:34');
INSERT INTO `ac_resource_library` VALUES (100207,1,'2015-06-16 12:28:57','2015-06-16 12:28:57');
INSERT INTO `ac_resource_library` VALUES (100208,1,'2015-06-16 12:28:59','2015-06-16 12:28:59');
INSERT INTO `ac_resource_library` VALUES (100209,1,'2015-06-16 12:29:03','2015-06-16 12:29:03');
INSERT INTO `ac_resource_library` VALUES (100210,1,'2015-06-16 12:29:05','2015-06-16 12:29:05');
INSERT INTO `ac_resource_library` VALUES (100211,1,'2015-06-17 12:16:43','2015-06-17 12:16:43');
INSERT INTO `ac_resource_library` VALUES (100212,1,'2015-06-17 12:16:48','2015-06-17 12:16:48');
INSERT INTO `ac_resource_library` VALUES (100213,1,'2015-06-17 12:16:51','2015-06-17 12:16:51');
INSERT INTO `ac_resource_library` VALUES (100214,1,'2015-06-17 12:16:53','2015-06-17 12:16:53');
INSERT INTO `ac_resource_library` VALUES (100215,1,'2015-06-17 12:16:56','2015-06-17 12:16:56');
INSERT INTO `ac_resource_library` VALUES (100216,1,'2015-06-17 12:16:58','2015-06-17 12:16:58');
INSERT INTO `ac_resource_library` VALUES (100217,1,'2015-06-17 12:17:32','2015-06-17 12:17:32');
INSERT INTO `ac_resource_library` VALUES (100218,1,'2015-06-17 12:17:35','2015-06-17 12:17:35');
INSERT INTO `ac_resource_library` VALUES (100219,1,'2015-06-17 12:17:37','2015-06-17 12:17:37');
INSERT INTO `ac_resource_library` VALUES (100220,1,'2015-06-17 12:17:39','2015-06-17 12:17:39');
INSERT INTO `ac_resource_library` VALUES (100221,1,'2015-06-17 12:17:41','2015-06-17 12:17:41');
INSERT INTO `ac_resource_library` VALUES (100222,1,'2015-06-17 12:19:41','2015-06-17 12:19:41');
INSERT INTO `ac_resource_library` VALUES (100223,1,'2015-06-17 12:19:44','2015-06-17 12:19:44');
INSERT INTO `ac_resource_library` VALUES (100224,1,'2015-06-17 12:19:47','2015-06-17 12:19:47');
INSERT INTO `ac_resource_library` VALUES (100225,1,'2015-06-17 12:19:50','2015-06-17 12:19:50');
INSERT INTO `ac_resource_library` VALUES (100226,1,'2015-06-17 12:19:52','2015-06-17 12:19:52');
INSERT INTO `ac_resource_library` VALUES (100227,1,'2015-06-17 12:19:55','2015-06-17 12:19:55');
INSERT INTO `ac_resource_library` VALUES (100228,1,'2015-06-18 12:31:08','2015-06-18 12:31:08');
INSERT INTO `ac_resource_library` VALUES (100229,1,'2015-06-18 12:31:11','2015-06-18 12:31:11');
INSERT INTO `ac_resource_library` VALUES (100230,1,'2015-06-18 12:31:17','2015-06-18 12:31:17');
INSERT INTO `ac_resource_library` VALUES (100231,1,'2015-06-18 12:31:21','2015-06-18 12:31:21');
INSERT INTO `ac_resource_library` VALUES (100232,1,'2015-06-18 12:31:27','2015-06-18 12:31:27');
INSERT INTO `ac_resource_library` VALUES (100233,1,'2015-06-18 12:31:37','2015-06-18 12:31:37');
INSERT INTO `ac_resource_library` VALUES (100235,1,'2015-06-22 09:33:39','2015-06-22 09:33:39');
INSERT INTO `ac_resource_library` VALUES (100236,1,'2015-06-22 09:34:26','2015-06-22 09:34:26');
INSERT INTO `ac_resource_library` VALUES (100237,1,'2015-06-22 09:34:28','2015-06-22 09:34:28');
INSERT INTO `ac_resource_library` VALUES (100238,1,'2015-06-22 09:34:30','2015-06-22 09:34:30');
INSERT INTO `ac_resource_library` VALUES (100239,1,'2015-06-22 09:35:57','2015-06-22 09:35:57');
INSERT INTO `ac_resource_library` VALUES (100240,1,'2015-06-22 09:35:59','2015-06-22 09:35:59');
INSERT INTO `ac_resource_library` VALUES (100241,1,'2015-06-22 10:17:15','2015-06-22 10:17:15');
INSERT INTO `ac_resource_library` VALUES (100242,1,'2015-06-22 11:28:33','2015-06-22 11:28:33');
INSERT INTO `ac_resource_library` VALUES (100243,1,'2015-06-22 12:03:28','2015-06-22 12:03:28');
INSERT INTO `ac_resource_library` VALUES (100244,1,'2015-06-22 12:04:17','2015-06-22 12:04:17');
INSERT INTO `ac_resource_library` VALUES (100245,1,'2015-06-22 12:32:10','2015-06-22 12:32:10');
INSERT INTO `ac_resource_library` VALUES (100246,1,'2015-06-22 12:32:14','2015-06-22 12:32:14');
INSERT INTO `ac_resource_library` VALUES (100247,1,'2015-06-22 12:32:17','2015-06-22 12:32:17');
INSERT INTO `ac_resource_library` VALUES (100248,1,'2015-06-22 12:32:21','2015-06-22 12:32:21');
INSERT INTO `ac_resource_library` VALUES (100250,1,'2015-06-22 12:48:03','2015-06-22 12:48:03');

--
-- Dumping data for table `resource_map`
--

INSERT INTO `ac_resource_map` VALUES (100012,'products',58,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100014,'products',80,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100013,'products',68,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100015,'products',80,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100011,'products',65,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100010,'products',65,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100007,'products',64,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100008,'products',64,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100009,'products',64,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100016,'products',80,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100017,'products',80,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100018,'products',84,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100019,'products',84,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100020,'products',84,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100021,'products',83,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100022,'products',82,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100023,'products',83,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100024,'products',83,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100249,'products',81,0,1,'2015-06-22 12:38:44','2015-06-22 12:38:44');
INSERT INTO `ac_resource_map` VALUES (100026,'products',51,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100027,'products',51,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100028,'products',51,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100029,'products',52,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100030,'products',92,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100031,'products',72,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100032,'products',61,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100033,'products',61,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100034,'products',61,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100035,'products',93,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100036,'products',74,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100037,'products',57,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100038,'products',57,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100039,'products',57,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100040,'products',57,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100041,'products',57,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100042,'products',57,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100043,'products',57,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100044,'products',75,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100045,'products',75,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100046,'products',75,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100047,'products',70,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100048,'products',86,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100049,'products',97,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100050,'products',96,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100051,'products',96,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100052,'products',67,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100053,'products',99,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100054,'products',85,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100055,'products',85,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100056,'products',85,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100057,'products',73,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100058,'products',91,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100059,'products',55,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100060,'products',55,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100061,'products',55,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100062,'products',55,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100063,'products',55,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100064,'products',87,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100065,'products',77,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100066,'products',77,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100067,'products',77,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100068,'products',77,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100069,'products',76,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100070,'products',76,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100071,'products',60,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100072,'products',60,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100073,'products',60,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100074,'products',60,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100075,'products',94,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100076,'products',90,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100077,'products',90,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100078,'products',90,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100079,'products',71,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100080,'products',63,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100081,'products',63,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100082,'products',69,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100083,'products',89,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100084,'products',89,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100085,'products',89,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100086,'products',89,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100087,'products',98,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100088,'products',95,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100089,'products',50,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100090,'products',100,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100091,'products',66,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100092,'products',66,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100093,'products',66,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100094,'products',53,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100095,'products',53,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100096,'products',53,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100097,'products',59,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100098,'products',59,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100099,'products',59,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100100,'products',59,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100101,'products',56,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100109,'products',78,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100108,'products',78,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100105,'products',79,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100106,'products',79,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100107,'products',79,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100110,'products',78,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100111,'products',88,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100112,'products',62,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100113,'products',62,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100114,'manufacturers',16,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100115,'manufacturers',14,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100116,'manufacturers',13,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100117,'manufacturers',12,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100118,'manufacturers',11,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100119,'manufacturers',15,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100120,'manufacturers',17,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100121,'manufacturers',18,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100122,'manufacturers',19,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100123,'categories',52,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100124,'categories',36,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100125,'categories',43,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100126,'categories',49,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100249,'categories',58,0,1,'2015-06-23 10:30:10','2015-06-23 10:30:10');
INSERT INTO `ac_resource_map` VALUES (100128,'categories',50,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100129,'categories',51,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100130,'categories',53,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100131,'categories',54,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100132,'categories',38,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100133,'categories',40,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100134,'categories',41,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100135,'categories',42,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100136,'categories',39,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100137,'categories',37,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100138,'categories',59,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100139,'categories',60,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100140,'categories',61,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100141,'categories',63,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100142,'categories',46,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100143,'categories',47,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100144,'categories',44,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100145,'categories',45,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100146,'categories',48,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100150,'products',101,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100178,'products',101,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100153,'manufacturers',20,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100154,'products',102,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100155,'products',102,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100156,'products',102,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100157,'products',103,0,2,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100159,'products',103,0,3,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100160,'products',104,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100162,'products',105,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100163,'products',106,0,2,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100164,'products',106,0,2,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100165,'products',106,0,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100166,'products',107,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100167,'products',107,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100168,'products',108,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100169,'products',108,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100170,'products',108,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100171,'products',108,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100173,'products',109,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100174,'products',110,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100175,'products',110,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100176,'products',110,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100188,'banners',13,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100188,'banners',14,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100188,'banners',15,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100188,'banners',16,0,0,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100194,'banners',18,0,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_resource_map` VALUES (100195,'products',111,0,1,'2015-06-12 10:14:20','2015-06-12 10:14:20');
INSERT INTO `ac_resource_map` VALUES (100198,'products',112,0,1,'2015-06-12 11:03:44','2015-06-12 11:03:44');
INSERT INTO `ac_resource_map` VALUES (100199,'categories',66,0,1,'2015-06-12 11:27:50','2015-06-12 11:27:50');
INSERT INTO `ac_resource_map` VALUES (100200,'categories',65,0,1,'2015-06-12 11:30:19','2015-06-12 11:30:19');
INSERT INTO `ac_resource_map` VALUES (100201,'products',113,0,1,'2015-06-12 11:49:01','2015-06-12 11:49:01');
INSERT INTO `ac_resource_map` VALUES (100202,'products',113,0,2,'2015-06-12 11:49:26','2015-06-12 11:49:26');
INSERT INTO `ac_resource_map` VALUES (100202,'categories',67,0,1,'2015-06-12 12:00:49','2015-06-12 12:00:49');
INSERT INTO `ac_resource_map` VALUES (100203,'products',114,0,1,'2015-06-12 12:06:33','2015-06-12 12:06:33');
INSERT INTO `ac_resource_map` VALUES (100204,'products',115,0,2,'2015-06-16 11:31:41','2015-06-16 11:57:19');
INSERT INTO `ac_resource_map` VALUES (100206,'products',115,0,1,'2015-06-16 11:58:34','2015-06-16 11:58:45');
INSERT INTO `ac_resource_map` VALUES (100207,'product_option_value',754,0,1,'2015-06-16 12:28:57','2015-06-16 12:28:57');
INSERT INTO `ac_resource_map` VALUES (100208,'product_option_value',754,0,2,'2015-06-16 12:28:59','2015-06-16 12:28:59');
INSERT INTO `ac_resource_map` VALUES (100209,'product_option_value',754,0,3,'2015-06-16 12:29:03','2015-06-16 12:29:03');
INSERT INTO `ac_resource_map` VALUES (100210,'product_option_value',754,0,4,'2015-06-16 12:29:05','2015-06-16 12:29:05');
INSERT INTO `ac_resource_map` VALUES (100211,'product_option_value',753,0,6,'2015-06-17 12:16:43','2015-06-17 12:38:18');
INSERT INTO `ac_resource_map` VALUES (100212,'product_option_value',753,0,2,'2015-06-17 12:16:48','2015-06-17 12:16:48');
INSERT INTO `ac_resource_map` VALUES (100213,'product_option_value',753,0,3,'2015-06-17 12:16:51','2015-06-17 12:16:51');
INSERT INTO `ac_resource_map` VALUES (100214,'product_option_value',753,0,4,'2015-06-17 12:16:53','2015-06-17 12:16:53');
INSERT INTO `ac_resource_map` VALUES (100215,'product_option_value',753,0,5,'2015-06-17 12:16:56','2015-06-17 12:16:56');
INSERT INTO `ac_resource_map` VALUES (100216,'product_option_value',753,0,1,'2015-06-17 12:16:59','2015-06-17 12:38:18');
INSERT INTO `ac_resource_map` VALUES (100217,'product_option_value',752,0,4,'2015-06-17 12:17:32','2015-06-17 12:36:01');
INSERT INTO `ac_resource_map` VALUES (100218,'product_option_value',752,0,2,'2015-06-17 12:17:35','2015-06-17 12:17:35');
INSERT INTO `ac_resource_map` VALUES (100219,'product_option_value',752,0,3,'2015-06-17 12:17:37','2015-06-17 12:17:37');
INSERT INTO `ac_resource_map` VALUES (100220,'product_option_value',752,0,1,'2015-06-17 12:17:39','2015-06-17 12:36:01');
INSERT INTO `ac_resource_map` VALUES (100221,'product_option_value',752,0,5,'2015-06-17 12:17:42','2015-06-17 12:17:42');
INSERT INTO `ac_resource_map` VALUES (100222,'products',116,0,5,'2015-06-17 12:19:41','2015-06-17 12:22:21');
INSERT INTO `ac_resource_map` VALUES (100223,'products',116,0,4,'2015-06-17 12:19:44','2015-06-17 12:22:21');
INSERT INTO `ac_resource_map` VALUES (100224,'products',116,0,3,'2015-06-17 12:19:47','2015-06-17 12:19:47');
INSERT INTO `ac_resource_map` VALUES (100225,'products',116,0,1,'2015-06-17 12:19:50','2015-06-17 12:22:21');
INSERT INTO `ac_resource_map` VALUES (100226,'products',116,0,2,'2015-06-17 12:19:52','2015-06-17 12:22:21');
INSERT INTO `ac_resource_map` VALUES (100227,'products',116,0,6,'2015-06-17 12:19:55','2015-06-17 12:19:55');
INSERT INTO `ac_resource_map` VALUES (100228,'products',117,0,1,'2015-06-18 12:31:09','2015-06-18 12:31:09');
INSERT INTO `ac_resource_map` VALUES (100229,'products',117,0,2,'2015-06-18 12:31:11','2015-06-18 12:31:11');
INSERT INTO `ac_resource_map` VALUES (100230,'products',117,0,3,'2015-06-18 12:31:17','2015-06-18 12:31:17');
INSERT INTO `ac_resource_map` VALUES (100231,'products',117,0,4,'2015-06-18 12:31:21','2015-06-18 12:31:21');
INSERT INTO `ac_resource_map` VALUES (100232,'products',117,0,5,'2015-06-18 12:31:27','2015-06-18 12:31:27');
INSERT INTO `ac_resource_map` VALUES (100233,'products',117,0,6,'2015-06-18 12:31:37','2015-06-18 12:31:37');
INSERT INTO `ac_resource_map` VALUES (100235,'products',118,0,1,'2015-06-22 09:33:39','2015-06-22 09:33:39');
INSERT INTO `ac_resource_map` VALUES (100236,'products',118,0,2,'2015-06-22 09:34:26','2015-06-22 09:34:26');
INSERT INTO `ac_resource_map` VALUES (100237,'products',118,0,3,'2015-06-22 09:34:28','2015-06-22 09:34:28');
INSERT INTO `ac_resource_map` VALUES (100238,'products',118,0,4,'2015-06-22 09:34:30','2015-06-22 09:34:30');
INSERT INTO `ac_resource_map` VALUES (100239,'products',118,0,5,'2015-06-22 09:35:57','2015-06-22 09:35:57');
INSERT INTO `ac_resource_map` VALUES (100240,'products',118,0,6,'2015-06-22 09:35:59','2015-06-22 09:35:59');
INSERT INTO `ac_resource_map` VALUES (100241,'products',119,0,1,'2015-06-22 10:17:15','2015-06-22 10:17:15');
INSERT INTO `ac_resource_map` VALUES (100242,'products',120,0,1,'2015-06-22 11:28:33','2015-06-22 11:28:33');
INSERT INTO `ac_resource_map` VALUES (100243,'products',121,0,1,'2015-06-22 12:03:28','2015-06-22 12:03:28');
INSERT INTO `ac_resource_map` VALUES (100244,'products',121,0,2,'2015-06-22 12:04:17','2015-06-22 12:04:17');
INSERT INTO `ac_resource_map` VALUES (100245,'products',122,0,5,'2015-06-22 12:32:10','2015-06-22 12:32:36');
INSERT INTO `ac_resource_map` VALUES (100246,'products',122,0,2,'2015-06-22 12:32:14','2015-06-22 12:32:14');
INSERT INTO `ac_resource_map` VALUES (100247,'products',122,0,3,'2015-06-22 12:32:17','2015-06-22 12:32:17');
INSERT INTO `ac_resource_map` VALUES (100248,'products',122,0,1,'2015-06-22 12:32:21','2015-06-22 12:32:36');
INSERT INTO `ac_resource_map` VALUES (100250,'products',54,0,1,'2015-06-22 12:48:03','2015-06-22 12:48:03');
INSERT INTO `ac_resource_map` VALUES (100244,'categories',68,0,1,'2015-06-22 13:10:13','2015-06-22 13:10:13');
INSERT INTO `ac_resource_map` VALUES (100216,'categories',69,0,1,'2015-06-22 13:11:10','2015-06-22 13:11:10');
INSERT INTO `ac_resource_map` VALUES (100243,'categories',70,0,1,'2015-06-22 13:11:41','2015-06-22 13:11:41');

--
-- Dumping data for table `reviews`
--

INSERT INTO `ac_reviews` VALUES (63,77,6,'Bernard Horne','I thought since it was made for men that it was the perfect thing to go with the body wash. Its too small and doesn\'t lather up very well.',3,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (62,54,2,'Juliana Davis','I\'ve been wearing all Lancome mascara\'s and I\'m just get really upset when I\'m out. I\'ve tried other Brands, but it\'s always right back to the Lancome productss. The extend L\'EXTREME is by far the best!!! Really Long and Great! ',5,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (61,56,0,'Cassandra','Fortunately, I got this as a gift. BUT, I am willing to purchase this when I run out. This may be expensive but it is sooooo worth it! I love this concealer and I wouldn\'t even dare to use other brands. One more thing, the little tube lasts for a long time. I\'ve been using it everyday for 8 months now and I still have about 1/4 left.',5,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (64,76,7,'James','Finally a deodorant for men that doesn\'t smell like cheap cologne. I\'ve been using this for a couple of weeks now and I can\'t say anything bad about it. To me it just smells fresh',4,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (65,100,0,'Juli','Smooth Silk is an accurate name for this creamy lip liner. It is by far the best lip pencil I have ever encountered.',5,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (66,100,0,'Marianne','Nice pencil! This is a smooth, long lasting pencil, wonderful shades!',4,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (67,97,0,'Ann','Really reduces shades and swellings)',4,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (68,99,0,'Alice','This is much darker than the picture',2,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (69,57,0,'Jane','When it arrived, the blush had cracked and was crumbling all over, so I\'m only able to use half of it.',2,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (70,55,0,'Kristin K.','These lipsticks are moisturizing and have good pigmentation; however, their lasting power is not as advertised! ',4,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (71,55,0,'lara','This is quite simply good stuff. \nThe color payout is rich, the texture creamy and moist, and best of all no scent. No taste.',5,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (72,93,0,'L. D.','I totally love it.it smells heavenly . It smells so natural and my skin just loves it. ',5,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (73,93,0,'Walton','This creme is a bit heavy for my skin; however, as the day goes on it does not create an oily build-up. A little goes a long way, and I could see improvements in my skin tone within a week. Good product, will be purchasing again.',4,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (74,74,0,'Stefania V','it works very well moisturing and cleaning and unlike many other healthy shampoos it doesn\'t open the hair platelets too far and therefore doesn\'t feel so dry and sticky so I can get away without using a conditioner. Great value.',4,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (75,102,0,'Mary','This is more of a evening fragrance. I love it',4,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (76,110,0,'Lara','Product was very reasonably priced. It will make a nice gift.',5,1,'2015-06-12 09:56:25','2015-06-12 09:56:25');
INSERT INTO `ac_reviews` VALUES (77,111,0,'Mr. G. Thomas','I would totally recommend product for anyone keen to learn a foreign language quickly. \nHowever, you must be fully committed and be ready to dedicate your time for study every day!',5,1,'2015-06-12 10:45:59','2015-06-12 10:52:03');
INSERT INTO `ac_reviews` VALUES (78,119,0,'F Buckley ','Good quality. Also good enough to wear out.\n\nWould order more in the future. ',5,1,'2015-06-22 10:27:59','2015-06-22 10:28:13');


--
-- Dumping data for table `url_aliases`
--

INSERT INTO `ac_url_aliases` VALUES (1,'category_id=36','makeup',1);
INSERT INTO `ac_url_aliases` VALUES (2,'content_id=1','about_us',1);
INSERT INTO `ac_url_aliases` VALUES (3,'product_id=101','pro-v_color_hair_solutions_color_preserve_shine_conditioner_with_pump',1);
INSERT INTO `ac_url_aliases` VALUES (4,'product_id=102','gucci_guilty',1);
INSERT INTO `ac_url_aliases` VALUES (5,'manufacturer_id=20','gucci',1);
INSERT INTO `ac_url_aliases` VALUES (6,'product_id=103','jasmin_noir_lessence_eau_de_parfum_spray',1);
INSERT INTO `ac_url_aliases` VALUES (7,'product_id=104','calvin_klein_obsession_for_women_edp_spray',1);
INSERT INTO `ac_url_aliases` VALUES (8,'product_id=105','bvlgari_aqua_eau_de_toilette_spray',1);
INSERT INTO `ac_url_aliases` VALUES (9,'product_id=106','omnia_eau_de_toilette',1);
INSERT INTO `ac_url_aliases` VALUES (10,'product_id=107','lancome_slimissime_360_slimming_activating_concentrate_unisex_treatment',1);
INSERT INTO `ac_url_aliases` VALUES (11,'product_id=108','lancome_hypnose_doll_lashes_mascara_4-piece_gift_set',1);
INSERT INTO `ac_url_aliases` VALUES (12,'product_id=109','lancome_visionnaire_advanced_skin_corrector',1);
INSERT INTO `ac_url_aliases` VALUES (13,'product_id=110','flora_by_gucci_eau_fraiche',1);
INSERT INTO `ac_url_aliases` VALUES (14,'category_id=65','books',1);
INSERT INTO `ac_url_aliases` VALUES (15,'category_id=66','audio-cd',1);
INSERT INTO `ac_url_aliases` VALUES (16,'category_id=67','paperback',1);
INSERT INTO `ac_url_aliases` VALUES (17,'product_id=111','new-french-with-ease-1-book--1-mp3-cd',1);
INSERT INTO `ac_url_aliases` VALUES (18,'product_id=112','the-miracle-morning-the-not-so-obvious-secret-guaranteed-to-transform-your-life',1);
INSERT INTO `ac_url_aliases` VALUES (19,'product_id=113','paper-towns-by-john-green',1);
INSERT INTO `ac_url_aliases` VALUES (20,'product_id=114','allegiant-by-veronica-roth',1);
INSERT INTO `ac_url_aliases` VALUES (21,'product_id=115','fiorella-purple-peep-toes',1);
INSERT INTO `ac_url_aliases` VALUES (22,'category_id=68','apparel--accessories',1);
INSERT INTO `ac_url_aliases` VALUES (23,'category_id=69','shoes',1);
INSERT INTO `ac_url_aliases` VALUES (24,'product_id=116','new-ladies-high-wedge-heel-toe-thong-diamante-flip-flop-sandals',1);
INSERT INTO `ac_url_aliases` VALUES (25,'product_id=117','ruby-shoo-women',1);
INSERT INTO `ac_url_aliases` VALUES (26,'product_id=118','women-high-heel-point-toe-stiletto-sandals-ankle-strap-court-shoes',1);
INSERT INTO `ac_url_aliases` VALUES (27,'category_id=70','t-shirts',1);
INSERT INTO `ac_url_aliases` VALUES (28,'product_id=119','fruit-of-the-loom-t-shirts-5-pack---super-premium',1);
INSERT INTO `ac_url_aliases` VALUES (29,'product_id=120','jersey-cotton-striped-polo-shirt',1);
INSERT INTO `ac_url_aliases` VALUES (30,'product_id=121','designer-men-casual-formal-double-cuffs-grandad-band-collar-shirt-elegant-tie',1);
INSERT INTO `ac_url_aliases` VALUES (31,'product_id=122','mens-fine-cotton-giraffe-polo-shirts',1);

--
-- Dumping data for table `banners`
--

INSERT INTO `ac_banners` VALUES
(18,1,1,'Main Page Banners','2013-03-10 00:00:00',NULL,0,'',6,now(), now()),
(17,1,2,'Main Page Banners','2013-03-10 00:00:00',NULL,0,'',5,now(), now()),
(16,1,1,'Main bottom banners','2013-03-10 00:00:00',NULL,0,'http://www.abantecart.com',4,now(), now()),
(15,1,1,'Main bottom banners','2013-03-10 00:00:00',NULL,0,'http://www.abantecart.com',3,now(), now()),
(14,1,1,'Main bottom banners','2013-03-10 00:00:00',NULL,0,'http://www.abantecart.com',2,now(), now()),
(13,1,1,'Main bottom banners','2013-03-10 00:00:00',NULL,0,'http://www.abantecart.com',1,now(), now()),
(11,1,2,'Main Page Banners','2013-03-10 00:00:00',NULL,0,'',4,now(), now()),
(10,1,2,'Main Page Banners','2013-03-10 00:00:00',NULL,0,'',3,now(), now()),
(9,1,2,'Main Page Banners','2013-03-10 00:00:00',NULL,0,'',2,now(), now()),
(8,1,2,'Main Page Banners','2013-03-10 00:00:00',NULL,0,'',1,now(), now())
;


INSERT INTO `ac_banner_descriptions` VALUES
	(18,1,'fallback','','',now(), now()),
	(17,1,'Main Banner 5','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 slide2_bot&quot; src=&quot;storefront/view/default/image/banner_image_5.png&quot; width=&quot;600&quot; height=&quot;300&quot; /&gt; &lt;span class=&quot;txt1&quot;&gt;Application and data security&lt;/span&gt; &lt;span class=&quot;txt2&quot;&gt;Secure Solution&lt;/span&gt; &lt;span class=&quot;txt3&quot;&gt;Very secure solution with up to date industry security practices and inline with PCI compliance. Customer information protection with data encryption&lt;/span&gt; &lt;span class=&quot;txt4&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;#&quot;&gt;Install Now!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now()),
	(16,1,'banner4','','',now(), now()),
	(15,1,'banner3','','',now(), now()),
	(14,1,'banner2','','',now(), now()),
	(13,1,'banner1','','',now(), now()),
	(11,1,'Main Banner 4','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 slide3_bot&quot; src=&quot;storefront/view/default/image/banner_image_4.png&quot; width=&quot;600&quot; height=&quot;300&quot; /&gt; &lt;span class=&quot;txt1 blue&quot;&gt;Stay in control&lt;/span&gt; &lt;span class=&quot;txt2 blue&quot;&gt;Easy updates&lt;/span&gt; &lt;span class=&quot;txt3 short&quot;&gt;Upgrade right from admin. Backward supportability in upgrades and automatic backups. Easy extension download with one step installation.&lt;/span&gt; &lt;span class=&quot;txt4 txt4up&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;#&quot;&gt;Get Yours!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now()),
	(10,1,'Main Banner 3','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 slide2_bot&quot; src=&quot;storefront/view/default/image/banner_image_3.png&quot; width=&quot;600&quot; height=&quot;300&quot; /&gt; &lt;span class=&quot;txt1&quot;&gt;Feature rich with smart UI&lt;/span&gt; &lt;span class=&quot;txt2&quot;&gt;Easy &amp;amp; fun to manage&lt;/span&gt; &lt;span class=&quot;txt3&quot;&gt;Feature reach shopping cart application right out of the box. Standard features allow to set up complete eCommerce site with all the tools needed to sell products online.&lt;/span&gt; &lt;span class=&quot;txt4&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;#&quot;&gt;Install Now!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now()),
	(9,1,'Main Banner 2','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 wp1_left slide2_bot&quot; src=&quot;storefront/view/default/image/banner_image_2.png&quot; width=&quot;600&quot; height=&quot;300&quot; /&gt; &lt;span class=&quot;txt1 blue txt_right2&quot;&gt;Highly flexible layout on any page&lt;/span&gt; &lt;span class=&quot;txt2 blue txt_right2&quot;&gt;SEO Friendly&lt;/span&gt; &lt;span class=&quot;txt2 blue txt_right2&quot;&gt;Fast Loading&lt;/span&gt; &lt;span class=&quot;txt4 txt_right2 txt4up&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;#&quot;&gt;Try Now!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now()),
	(8,1,'Main Banner 1','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 slide1_bot&quot; src=&quot;storefront/view/default/image/banner_image_1.png&quot; width=&quot;600&quot; height=&quot;300&quot; /&gt; &lt;span class=&quot;txt1&quot;&gt;HTML5 Responsive Storefront to look great on&lt;/span&gt; &lt;span class=&quot;txt2&quot;&gt;ALL Screen Sizes&lt;/span&gt; &lt;span class=&quot;txt3 short&quot;&gt;Natively responsive template implemented with bootstrap library and HTML5. Will look good on most mobile devices and tablets.&lt;/span&gt; &lt;span class=&quot;txt4 txt4up&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;&quot;&gt;Try on your device!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now());
