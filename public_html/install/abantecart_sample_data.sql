SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Dumping data for table `addresses`
--


INSERT INTO `ac_addresses` 
VALUES 
(1,2,'','Juliana','Davis','Highlands and Islands PA75 6QE','','','Isle of Mull',222,3559),
(2,3,'','Keely','Mccoy','4842 Amet','','','Grangemouth',222,3552),
(3,4,'BelfastCo','Zelda','Weiss','Great Victoria Street','','','Belfast',222,3518),
(4,5,'','Gloria','Macias','Camille Marcoux 15','','1569','Blanc-Sablon',38,609),
(5,6,'','Bernard','Horne','3E rue','','','Paspébiac',38,613),
(6,7,'','James','Curtis','6500 Arapahoe Road','','80303','Boulder',223,3634),
(7,8,'','Bruce','Rosarini','61 Cumberland ST','','','Thunder Bay',223,3646),
(8,9,'','Carlos','Compton','31 Capital Drive','','','Hay River',38,608),
(9,10,'','Garrison','Baxter','Eddie Hoffman Highway','','','Bethel',223,3614),
(10,11,'','Anthony','Blair','104 Main Street','','','Dillingham',223,3657),
(11,12,'','Allen','Waters','110 Shenandoah Avenue','','','Roanoke',223,3673),
(12,13,'','qqqqqq','qqqqqq','qqqqqq','','','qqqqqq',222,3529);

--
-- Dumping data for table `categories`
--

INSERT INTO `ac_categories` 
VALUES 
(46,43,0,1,now(), now()),
(47,43,0,1,now(), now()),
(38,36,0,1,now(), now()),
(40,36,0,1,now(), now()),
(41,36,0,1,now(), now()),
(42,36,0,1,now(), now()),
(43,0,2,1,now(), now()),
(44,43,0,1,now(), now()),
(45,43,0,1,now(), now()),
(39,36,0,1,now(), now()),
(36,0,1,1,now(), now()),
(37,36,0,1,now(), now()),
(48,43,0,1,now(), now()),
(49,0,3,1,now(), now()),
(50,49,0,1,now(), now()),
(51,49,0,1,now(), now()),
(52,0,98,1,now(), now()),
(53,52,0,1,now(), now()),
(54,52,0,1,now(), now()),
(58,0,4,1,now(), now()),
(59,58,0,1,now(), now()),
(60,58,0,1,now(), now()),
(61,58,0,1,now(), now()),
(62,58,0,0,now(), now()),
(63,58,0,1,now(), now()),
(64,0,99,0,now(), now());

--
-- Dumping data for table `categories_to_stores`
--

INSERT INTO `ac_categories_to_stores` 
VALUES 
(36,0),
(37,0),
(38,0),
(39,0),
(40,0),
(41,0),
(42,0),
(43,0),
(44,0),
(45,0),
(46,0),
(47,0),
(48,0),
(49,0),
(50,0),
(51,0),
(52,0),
(53,0),
(54,0),
(58,0),
(59,0),
(60,0),
(61,0),
(62,0),
(63,0),
(64,0);

--
-- Dumping data for table `category_descriptions`
--

INSERT INTO `ac_category_descriptions` 
VALUES 
(43,1,'Skincare','','','&lt;p&gt;\r\n	Products from award-winning skin care brands&lt;/p&gt;\r\n'),
(41,1,'Lips','','',''),
(42,1,'Nails','','',''),
(38,1,'Face','','',''),
(39,1,'Eyes','','',''),
(36,1,'Makeup','Makeup','','&lt;p&gt;\r\n	All your makeup needs, from foundation to eye shadow in hundreds of different assortments and colors.&lt;/p&gt;\r\n'),
(40,1,'Cheeks','','',''),
(37,1,'Value Sets','value sets makeup','',''),
(44,1,'Sun','','',''),
(45,1,'Gift Ideas &amp; Sets','','',''),
(46,1,'Face','','','&lt;p&gt;\r\n	Find face skin care solutions&lt;/p&gt;\r\n'),
(47,1,'Eyes','','',''),
(48,1,'Hands &amp; Nails','','','&lt;p&gt;\r\n	Keep your hands looking fresh&lt;/p&gt;\r\n'),
(49,1,'Fragrance','','','&lt;p&gt;\r\n	Looking for a new scent? Check out our fragrance&lt;/p&gt;\r\n'),
(50,1,'Women','','','&lt;p&gt;\r\n	Fragrance for Women&lt;/p&gt;\r\n'),
(51,1,'Men','','',''),
(52,1,'Hair Care','','','&lt;p&gt;\r\n	The widest range of premium hair products&lt;/p&gt;\r\n'),
(53,1,'Shampoo','','',''),
(54,1,'Conditioner','','',''),
(58,1,'Men','','',''),
(59,1,'Fragrance Sets','','',''),
(60,1,'Skincare','','',''),
(61,1,'Pre-Shave &amp; Shaving','','',''),
(62,1,'Post-Shave &amp; Moisturizers','','',''),
(63,1,'Body &amp; Shower','','',''),
(64,1,'Bath &amp; Body','','','');



--
-- Dumping data for table `coupon_descriptions`
--



INSERT INTO `ac_coupon_descriptions` 
VALUES 
(4,1,'Coupon (-10%)','10% Discount'),
(5,1,'Coupon (Free Shipping)','Free Shipping'),
(6,1,'Coupon (-10.00)','Fixed Amount Discount');


--
-- Dumping data for table `coupons`
--

DROP TABLE IF EXISTS `ac_coupons`;
CREATE TABLE `ac_coupons` (
  `coupon_id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8_general_ci NOT NULL,
  `type` char(1) COLLATE utf8_general_ci NOT NULL,
  `discount` decimal(15,4) NOT NULL,
  `logged` int(1) NOT NULL,
  `shipping` int(1) NOT NULL,
  `total` decimal(15,4) NOT NULL,
  `date_start` date NOT NULL DEFAULT '0000-00-00',
  `date_end` date NOT NULL DEFAULT '0000-00-00',
  `uses_total` int(11) NOT NULL,
  `uses_customer` varchar(11) COLLATE utf8_general_ci NOT NULL,
  `status` int(1) NOT NULL,
  `date_added` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`coupon_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;



INSERT INTO `ac_coupons` 
VALUES 
(4,'2222','P','10.0000',0,0,'0.0000','2012-01-27','2010-03-06',10,'10',1,now(), now()),
(5,'3333','P','0.0000',0,1,'100.0000','2012-03-01','2015-08-31',10,'10',1,now(), now()),
(6,'1111','F','10.0000',0,0,'10.0000','2012-01-01','2015-03-01',10,'10',1,now(), now());


--
-- Dumping data for table `coupons_products`
--


INSERT INTO `ac_coupons_products` 
VALUES (8,6,68);


--
-- Dumping data for table `custom_blocks`
--

INSERT INTO `ac_custom_blocks` 
VALUES 
(1,17,now(), now()),
(2,20,now(), now()),
(3,20,now(), now()),
(12, 20, now(), now()),
(13, 17, now(), now()),
(11, 23, now(), now()),
(9, 23, now(), now()),
(10, 17, now(), now()),
(14, 17, now(), now()),
(15, 17, now(), now()),
(16, 17, now(), now())
;

--
-- Dumping data for table `block_descriptions`
--

INSERT INTO `ac_block_descriptions`
VALUES
(1,1,1,'0','0','home page static banner','home page banner','','&lt;div style=&quot;text-align: center;&quot;&gt;&lt;a href=&quot;index.php?rt=product/special&quot;&gt; &lt;img alt=&quot;banner&quot; src=&quot;storefront/view/default/image/banner1.jpg&quot; /&gt; &lt;/a&gt;&lt;/div&gt;',now(), now()),
(2,2,1,'0','0','Video block','Video','','a:3:{s:18:\"listing_datasource\";s:5:\"media\";s:13:\"resource_type\";s:5:\"video\";s:5:\"limit\";s:1:\"1\";}',now(), now()),
(3,3,1,'0','1','Custom Listing block','Popular','','a:2:{s:18:\"listing_datasource\";s:34:\"catalog_product_getPopularProducts\";s:5:\"limit\";s:2:\"12\";}',now(), now()),
(30,16,1,0,1,'Testimonials','Testimonials','Flexislider testimonials','&lt;div style=&quot;font-family: ''Open Sans'', sans-serif;&quot; class=&quot;flexslider&quot; id=&quot;testimonialsidebar&quot;&gt;\r\n	&lt;ul class=&quot;slides&quot;&gt;\r\n		&lt;li&gt;\r\n			&quot; I was working with many shopping carts, free and hosted for my clients. There is always something missing. In abantecart I find this gap to be much less. Interface is very easy to use and support is very responsive. This is considering its is free. Go abantecart go!&quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : TopShop on reviewcentre.com&lt;/span&gt;&lt;/li&gt;\r\n		&lt;li&gt;\r\n			&quot; Without a doubt the best cart I have used. The title says it all - abantecart is undoubtedly the best I have used. I\'m not an expert in site setup, so something this great looking and easy to use is absolutely perfect ... &quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : johnstenson80 on venturebeat.com&lt;/span&gt;&lt;/li&gt;\r\n		&lt;li&gt;\r\n			&quot; Will not regret using this cart. All good is already mentioned, I want to add my experience with support. My problems with some configuration were resolved quick. Faster than paid shopping cart we had before.&quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : shopper23 at bestshoppingcartreviews.com&lt;/span&gt;&lt;/li&gt;\r\n		&lt;li&gt;\r\n			&quot; Wow! Abante Cart is really a catch! What a nice experience it was for me. I mean, to have all these features so direct, so quick and easy was really essential for my website. I was able to add some features and a cart to my website in no time ...&quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : EcommerceSport at hotscripts.com&lt;/span&gt;&lt;/li&gt;\r\n		&lt;li&gt;\r\n			&quot; Love the cart. I installed it a while back and use it since when. Some features a hidden, but fun to discover them.&quot;&lt;br /&gt;\r\n			&lt;span class=&quot;pull-left orange&quot;&gt;By : Liz Wattkins at shopping-cart-reviews.com&lt;/span&gt;&lt;/li&gt;\r\n\r\n	&lt;/ul&gt;\r\n&lt;/div&gt;\r\n',now(),now()),
(28,15,1,0,0,'Social Icons','Social Icons','This is a an HTML block to show social icons and link.\r\nNOTE: Need to edit HTML in block content to add  personal link to social media sites','      &lt;div class=&quot;social_icons&quot;&gt;\r\n        &lt;a href=&quot;http://www.facebook.com/AbanteCart&quot; target=&quot;_blank&quot; title=&quot;Facebook&quot; class=&quot;facebook&quot;&gt;Facebook&lt;/a&gt;\r\n        &lt;a href=&quot;https://twitter.com/abantecart&quot; target=&quot;_blank&quot; title=&quot;Twitter&quot; class=&quot;twitter&quot;&gt;Twitter&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; title=&quot;Linkedin&quot; class=&quot;linkedin&quot;&gt;Linkedin&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; title=&quot;rss&quot; class=&quot;rss&quot;&gt;rss&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; target=&quot;_blank&quot; title=&quot;Googleplus&quot; class=&quot;googleplus&quot;&gt;Googleplus&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; target=&quot;_blank&quot; title=&quot;Skype&quot; class=&quot;skype&quot;&gt;Skype&lt;/a&gt;\r\n        &lt;a href=&quot;#&quot; target=&quot;_blank&quot; title=&quot;Flickr&quot; class=&quot;flickr&quot;&gt;Flickr&lt;/a&gt;\r\n      &lt;/div&gt;\r\n',now(),now()),
(26,14,1,0,1,'Contact us','Contact Us','','&lt;ul class=&quot;contact&quot;&gt;	&lt;li&gt;&lt;span class=&quot;phone&quot;&gt;&nbsp;&lt;/span&gt;+123 456 7890, +123 456 7890&lt;/li&gt;	&lt;li&gt;&lt;span class=&quot;mobile&quot;&gt;&nbsp;&lt;/span&gt;+123 456 7890, +123 456 78900&lt;/li&gt;	&lt;li&gt;&lt;span class=&quot;email&quot;&gt;&nbsp;&lt;/span&gt;help at abantecart.com&lt;/li&gt;	&lt;li&gt;&lt;span class=&quot;email&quot;&gt;&nbsp;&lt;/span&gt;help at abantecart.com&lt;/li&gt;&lt;/ul&gt;',now(),now()),
(24,13,1,0,1,'About Us','About Us','','&lt;p&gt;\r\n	AbanteCart is a free eCommerce solution for merchants to provide ability creating online business and sell products or services online. AbanteCart application is built and supported by experienced enthusiasts that are passionate about their work and contribution to rapidly evolving eCommerce industry. AbanteCart is more than just a shopping cart, it is rapidly growing eCommerce platform with many benefits.&lt;/p&gt;\r\n',now(),now()),
(22,12,1,'blocks/listing_block/popular_brands_content_bottom.tpl',1,'Brands Scrolling List','Brands Scrolling List','','a:1:{s:18:\"listing_datasource\";s:20:\"custom_manufacturers\";}',now(),now()),
(20,11,1,0,0,'Main Page Banner Bottom','Bottom Banners','','a:1:{s:17:\"banner_group_name\";s:19:\"Main bottom banners\";}',now(),now()),
(18,10,1,0,0,'Main Page Promo','Promo','','&lt;!-- Section Start--&gt;\r\n	&lt;section class=&quot;container otherddetails&quot;&gt;\r\n	&lt;div class=&quot;otherddetailspart&quot;&gt;\r\n		&lt;div class=&quot;innerclass free&quot;&gt;\r\n			&lt;h2&gt;\r\n				Free shipping&lt;/h2&gt;\r\n			All over in world over $200&lt;/div&gt;\r\n	&lt;/div&gt;\r\n	&lt;div class=&quot;otherddetailspart&quot;&gt;\r\n		&lt;div class=&quot;innerclass payment&quot;&gt;\r\n			&lt;h2&gt;\r\n				Easy Payment&lt;/h2&gt;\r\n			Payment Gatway support&lt;/div&gt;\r\n	&lt;/div&gt;\r\n	&lt;div class=&quot;otherddetailspart&quot;&gt;\r\n		&lt;div class=&quot;innerclass shipping&quot;&gt;\r\n			&lt;h2&gt;\r\n				24hrs Shipping&lt;/h2&gt;\r\n			For All US States&lt;/div&gt;\r\n	&lt;/div&gt;\r\n	&lt;div class=&quot;otherddetailspart&quot;&gt;\r\n		&lt;div class=&quot;innerclass choice&quot;&gt;\r\n			&lt;h2&gt;\r\n				Large Variety&lt;/h2&gt;\r\n			50,000+ Products&lt;/div&gt;\r\n	&lt;/div&gt;\r\n	&lt;/section&gt; &lt;!-- Section End--&gt;',now(),now()),
(16,9,1,'blocks/banner_block/one_by_one_slider_banner_block.tpl',0,'Main Page Banner Slider','Main Page Banner Slider','','a:1:{s:17:\"banner_group_name\";s:17:\"Main Page Banners\";}',now(),now())
;

INSERT INTO `ac_custom_lists` (`custom_block_id`, `data_type`, `id`, `sort_order`, `date_added`, `date_modified`) VALUES
(12, 'manufacturer_id', 12, 0, now(), now()),
(12, 'manufacturer_id', 14, 0, now(), now()),
(12, 'manufacturer_id', 13, 0, now(), now()),
(12, 'manufacturer_id', 18, 0, now(), now()),
(12, 'manufacturer_id', 19, 0, now(), now()),
(12, 'manufacturer_id', 20, 0, now(), now()),
(12, 'manufacturer_id', 15, 0, now(), now()),
(12, 'manufacturer_id', 11, 0, now(), now()),
(12, 'manufacturer_id', 17, 0, now(), now()),
(12, 'manufacturer_id', 16, 0, now(), now());

--
-- Dumping data for table `customers`
--
INSERT INTO `ac_customers`
(`customer_id`, `store_id`, `firstname`, `lastname`, `loginname`, `email`, `telephone`, `fax`, `password`, `cart`, `newsletter`, `address_id`,
 `status`, `approved`, `customer_group_id`, `ip`,  `date_added`)
VALUES 
(2,0,'Juliana','Davis', 'julidavis@abantecart.com', 'julidavis@abantecart.com','+44 1688 308321','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,1,1,1,1,'109.104.166.98','2013-08-31 10:25:37'),
(3,0,'Keely','Mccoy','keelymccoy@abantecart.com','keelymccoy@abantecart.com','+44 1324 483784 ','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,2,1,1,1,'109.104.166.98','2013-08-31 10:39:08'),
(4,0,'Zelda','Weiss','zeldaweiss@abantecart.com','zeldaweiss@abantecart.com','+44 28 9027 1066 ','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,3,1,1,1,'109.104.166.138','2013-08-31 10:42:58'),
(5,0,'Gloria','Macias','gloriamacias@abantecart.com','gloriamacias@abantecart.com','+1 418-461-2440','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,4,1,1,1,'109.104.166.98','2013-08-31 10:46:58'),
(6,0,'Bernard','Horne','bernardhorne@abantecart.com','bernardhorne@abantecart.com','+1 418-752-3369 ','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,5,1,1,1,'109.104.166.138','2013-08-31 10:50:27'),
(7,0,'James','Curtis','jamescurtis@abantecart.com','jamescurtis@abantecart.com','+1 303-497-1010','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,6,1,1,1,'109.104.166.138','2013-08-31 11:00:03'),
(8,0,'Bruce','Rosarini','brucerosarini@abantecart.com','brucerosarini@abantecart.com','+1 807-346-10763','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,7,1,1,1,'109.104.166.98','2013-08-31 11:08:23'),
(9,0,'Carlos','Compton','carloscmpton@abantecart.com','carloscmpton@abantecart.com','+1 867-874-22391','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,8,1,1,1,'109.104.166.98','2013-08-31 11:13:14'),
(10,0,'Garrison','Baxter','garrisonbaxter@abantecart.com','garrisonbaxter@abantecart.com','+1 907-543-43088','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,9,1,1,1,'109.104.166.138','2013-09-01 08:51:47'),
(11,0,'Anthony','Blair','anthonyblair@abantecart.com','anthonyblair@abantecart.com','+1 907-842-2240','','05ec6352a8b997363e5c6483aeffeb50','a:0:{}',0,10,1,1,1,'171.98.12.12','2013-09-01 08:54:26'),
(12,0,'Allen','Waters','allenwaters@abantecart.com','allenwaters@abantecart.com','+1 540-985-59700','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,11,1,1,1,'109.104.166.98','2013-09-01 09:12:56'),
(13,0,'qqqqqq','qqqqqq','1@abantecart','1@abantecart','55 555 5555 5555','','f73469b693cecf7fa70c3e39b6fde1f4','a:1:{s:3:\"97.\";i:1;}',0,12,1,1,1,'109.104.166.98','2013-09-08 11:28:20');

--
-- Dumping data for table `global_attributes`
--



INSERT INTO `ac_global_attributes`
( `attribute_id`,
  `attribute_parent_id`,
  `attribute_group_id`,
  `attribute_type_id`,
  `element_type`,
  `sort_order`,
  `required`,
  `settings`,
  `status`)
VALUES 
(1,0,0,1,'S',1,1,'',1),
(2,0,0,1,'C',0,0,'',1),
(5,0,0,1,'G',1,1,'',1);



--
-- Dumping data for table `global_attributes_descriptions`
--



INSERT INTO `ac_global_attributes_descriptions`
 (`attribute_id`,  `language_id`,  `name`)
VALUES
(1,1,'Size'),
(2,1,'Gift Wrapping'),
(5,1,'Fragrance Type');


--
-- Dumping data for table `global_attributes_values`
--
INSERT INTO `ac_global_attributes_values` VALUES
(53,1,0),
(52,1,0),
(51,1,0),
(50,1,0),
(49,1,0),
(48,1,0),
(47,1,0),
(46,1,0),
(45,1,0),
(32,2,0),
(43,1,0),
(44,1,0),
(76,5,0),
(77,5,0),
(75,5,0);


INSERT INTO `ac_global_attributes_value_descriptions` VALUES
(53,1,1,'1 oz'),
(52,1,1,'75ml'),
(51,1,1,'50ml'),
(50,1,1,'30ml'),
(49,1,1,'2.5 oz'),
(48,1,1,'1.5 oz'),
(47,1,1,'33.8 oz'),
(46,1,1,'15.2 oz'),
(45,1,1,'8.45 oz'),
(32,2,1,''),
(42,1,1,'1.7 oz'),
(43,1,1,'3.4 oz'),
(44,1,1,'100ml'),
(76,5,1,'Eau de Toilette'),
(77,5,1,'Eau de Cologne'),
(75,5,1,'Eau de Parfum');



--
-- Dumping data for table `manufacturers`
--



INSERT INTO `ac_manufacturers` 
VALUES 
(14,'Bvlgari',0),
(13,'Calvin Klein',0),
(12,'Benefit',0),
(11,'M·A·C',0),
(15,'Lancôme',0),
(16,'Sephora',0),
(17,'Pantene',0),
(18,'Dove',0),
(19,'Giorgio Armani',0),
(20,'Gucci',0);



--
-- Dumping data for table `manufacturers_to_stores`
--



INSERT INTO `ac_manufacturers_to_stores` 
VALUES 
(11,0),
(12,0),
(13,0),
(14,0),
(15,0),
(16,0),
(17,0),
(18,0),
(19,0),
(20,0);



--
-- Dumping data for table `order_history`
--



INSERT INTO `ac_order_history` 
VALUES 
(1,1,1,1,'','0000-00-00 00:00:00', now()),
(2,2,1,1,'','2013-09-07 04:02:31', now()),
(3,3,1,1,'','2013-09-07 04:41:25', now()),
(4,4,1,1,'','2013-09-07 04:51:07', now()),
(5,5,1,1,'','2013-09-07 05:20:22', now()),
(6,6,1,1,'','2013-09-07 05:21:56', now()),
(7,7,1,1,'','2013-09-07 05:24:11', now()),
(8,8,1,1,'','2013-09-07 05:36:21', now()),
(9,9,1,1,'','2013-09-07 05:37:20', now()),
(10,10,1,1,'','2013-09-07 05:39:30', now()),
(11,11,1,1,'','2013-09-07 05:40:03', now()),
(12,12,1,1,'','2012-03-15 10:04:06', now()),
(13,13,1,1,'','2012-03-15 10:05:40', now());



--
-- Dumping data for table `order_options`
--



INSERT INTO `ac_order_options` 
VALUES 
(1,1,2,588,'Memory','8GB','99.0000','+'),
(2,2,7,684,'Color','brown','10.0000','+'),
(3,3,9,651,'Size','33.8 oz','49.0000','+'),
(4,3,10,650,'Size','8 oz','19.0000','+'),
(5,3,15,646,'Color','Brown','20.0000','-'),
(6,4,16,613,'Color','Mandarin Sky','29.5000','+'),
(7,4,18,664,'Fragrance Size','3.4 oz','84.0000','+'),
(8,4,19,673,'Fragrance Size','6.7 oz','92.0000','+'),
(9,4,21,661,'Fragrance Size','150ml','45.0000','+'),
(10,5,23,627,'Color','Jade Fever','48.0000','+'),
(11,5,24,626,'Color','Gris Fatale','48.0000','+'),
(12,5,25,622,'Color','Shirelle','15.0000','+'),
(13,5,26,619,'Color','Lacewood','27.0000','+'),
(14,5,27,657,'Color','Light Bisque','30.5000','+'),
(15,5,30,651,'Size','33.8 oz','49.0000','+'),
(16,6,31,666,'Size','30 ml','30.0000','+'),
(17,7,33,649,'Fragrance Size','1.7 oz','88.0000','+'),
(18,7,34,660,'Fragrance Size','100ml','37.0000','+'),
(19,8,35,646,'Color','Brown','20.0000','-'),
(20,8,36,681,'Color','beige','10.0000','+'),
(21,12,45,721,'Size','Eau de Toilette','78.5000','$'),
(22,12,45,1,'Gift Wrapping','1','78.5000','$'),
(23,12,47,738,'Size','30ml','90.0000','$'),
(24,13,49,713,'Size','1.7 oz','72.0000','$'),
(25,13,49,1,'Gift Wrapping','1','72.0000','$');




INSERT INTO `ac_order_products` (`order_product_id`, `order_id`, `product_id`,`name`,`model`,`price`,`total`,`tax`,`quantity`,`subtract`)
VALUES
(6,2,97,'Eye Rejuvenating Serum','GRMBC004','126.0000','126.0000','8.5000',1,0),
(7,2,100,'Smooth silk lip pencils','GRMBC007','10.0000','40.0000','8.5000',4,0),
(8,2,93,'Creme Precieuse Nuit 50ml','BVLG003','220.0000','220.0000','8.5000',1,0),
(9,3,69,'Seaweed Conditioner','SCND001','49.0000','49.0000','0.0000',1,0),
(10,3,69,'Seaweed Conditioner','SCND001','19.0000','19.0000','0.0000',1,0),
(11,3,77,'Men+Care Active Clean Shower Tool','DMBW0014','6.0000','6.0000','0.0000',1,0),
(12,3,98,'Shaving cream','GRMBC005','98.0000','98.0000','0.0000',1,0),
(13,3,62,'ck one shock for him Deodorant','601232','14.0000','14.0000','0.0000',1,0),
(14,3,66,'Total Moisture Facial Cream','556240','38.0000','38.0000','0.0000',1,0),
(15,3,54,'L\'EXTRÊME Instant Extensions Lengthening Mascara','74144','20.0000','20.0000','0.0000',1,0),
(16,4,57,'Delicate Oil-Free Powder Blush','117148','29.5000','29.5000','8.5000',1,0),
(17,4,67,'Flash Bronzer Body Gel','463686','29.0000','29.0000','8.5000',1,0),
(18,4,80,'Acqua Di Gio Pour Homme','GRM001','84.0000','84.0000','8.5000',1,0),
(19,4,89,'Secret Obsession Perfume','CK0012','92.0000','92.0000','8.5000',1,0),
(20,4,75,'Dove Men +Care Body Wash','DMBW0012','6.7000','6.7000','8.5000',1,0),
(21,4,78,'ck IN2U Eau De Toilette Spray for Him','Cl0001','45.0000','45.0000','8.5000',1,0),
(22,5,97,'Eye Rejuvenating Serum','GRMBC004','126.0000','126.0000','8.5000',1,0),
(23,5,61,'Color Design Eye Brightening All in One 5 Shadow &amp; Liner Palette','529071','48.0000','48.0000','8.5000',1,0),
(24,5,61,'Color Design Eye Brightening All in One 5 Shadow &amp; Liner Palette','529071','48.0000','96.0000','8.5000',2,0),
(25,5,60,'Nail Lacquer','112423','15.0000','15.0000','8.5000',1,0),
(26,5,55,'LE ROUGE ABSOLU Reshaping &amp; Replenishing LipColour SPF 15','tw152236','27.0000','27.0000','8.5000',1,0),
(27,5,56,'Waterproof Protective Undereye Concealer','35190','30.5000','30.5000','8.5000',1,0),
(28,5,51,'BeneFit Girl Meets Pearl','483857','19.0000','19.0000','8.5000',1,0),
(29,5,93,'Creme Precieuse Nuit 50ml','BVLG003','220.0000','220.0000','8.5000',1,0),
(30,5,69,'Seaweed Conditioner','SCND001','49.0000','49.0000','8.5000',1,0),
(31,6,84,'Armani Code Pour Femme','GRM005','30.0000','30.0000','8.5000',1,0),
(32,6,92,'Body Cream by Bulgari','BVLG002','57.0000','171.0000','8.5000',3,0),
(33,7,63,'Pour Homme Eau de Toilette','374622','88.0000','88.0000','8.5000',1,0),
(34,7,78,'ck IN2U Eau De Toilette Spray for Him','Cl0001','37.0000','74.0000','8.5000',2,0),
(35,8,54,'L\'EXTRÊME Instant Extensions Lengthening Mascara','74144','20.0000','20.0000','0.0000',1,0),
(36,8,100,'Smooth silk lip pencils','GRMBC007','10.0000','40.0000','0.0000',4,0),
(37,9,94,'Night Care Crema Nera Obsidian Mineral Complex','GRMBC001','263.0000','263.0000','0.0000',1,0),
(38,9,67,'Flash Bronzer Body Gel','463686','29.0000','29.0000','0.0000',1,0),
(39,9,91,'Jasmin Noir Body Lotion 6.8 fl oz','BVLG001','29.0000','58.0000','0.0000',2,0),
(40,10,72,'Brunette expressions Conditioner','PCND002','24.0000','24.0000','8.5000',1,0),
(41,10,81,'Armani Eau de Toilette Spray ','GRM002','61.0000','61.0000','8.5000',1,0),
(42,10,88,'ck one Summer 3.4 oz','CK0011','27.0000','27.0000','8.5000',1,0),
(43,10,70,'Eau Parfumee au The Vert Shampoo','522823','31.0000','31.0000','8.5000',1,0),
(44,11,51,'BeneFit Girl Meets Pearl','483857','19.0000','19.0000','0.0000',1,0),
(45,12,105,'Bvlgari Aqua','PRF00273','78.5000','78.5000','0.0000',1,0),
(46,12,65,'Absolue Eye Precious Cells','427847','105.0000','105.0000','8.5000',1,0),
(47,12,110,'Flora By Gucci Eau Fraiche','PRF00278','90.0000','270.0000','8.5000',3,0),
(48,12,95,'Skin Minerals For Men Cleansing Cream','GRMBC002','104.0000','0.0000','8.5000',0,0),
(49,13,104,'Calvin Klein Obsession For Women EDP Spray','PRF00271','72.0000','576.0000','8.5000',8,0);



--
-- Dumping data for table `order_totals`
--



INSERT INTO `ac_order_totals` 
VALUES 
(1,1,'Sub-Total:','?1,583.44','1583.4400',1,'subtotal'),
(2,1,'Flat Shipping Rate:','?2.00','2.0000',3,'shipping'),
(3,1,'Total:','?1,585.44','1585.4400',6,'total'),
(4,2,'Sub-Total:','$386.00','386.0000',1,'subtotal'),
(5,2,'Retail 8.5%:','$32.81','32.8100',5,'tax'),
(6,2,'Total:','$418.81','418.8100',6,'total'),
(7,3,'Sub-Total:','$244.00','244.0000',1,'subtotal'),
(8,3,'Flat Shipping Rate:','$2.00','2.0000',3,'shipping'),
(9,3,'Total:','$246.00','246.0000',6,'total'),
(10,4,'Sub-Total:','$286.20','286.2000',1,'subtotal'),
(11,4,'Retail 8.5%:','$24.33','24.3270',5,'tax'),
(12,4,'Total:','$310.53','310.5270',6,'total'),
(13,5,'Sub-Total:','$630.50','630.5000',1,'subtotal'),
(14,5,'Flat Shipping Rate:','$2.00','2.0000',3,'shipping'),
(15,5,'Retail 8.5%:','$53.59','53.5925',5,'tax'),
(16,5,'Total:','$686.09','686.0925',6,'total'),
(17,6,'Sub-Total:','$201.00','201.0000',1,'subtotal'),
(18,6,'Retail 8.5%:','$17.09','17.0850',5,'tax'),
(19,6,'Total:','$218.09','218.0850',6,'total'),
(20,7,'Sub-Total:','$162.00','162.0000',1,'subtotal'),
(21,7,'Retail 8.5%:','$13.77','13.7700',5,'tax'),
(22,7,'Total:','$175.77','175.7700',6,'total'),
(23,8,'Sub-Total:','$60.00','60.0000',1,'subtotal'),
(24,8,'Flat Shipping Rate:','$2.00','2.0000',3,'shipping'),
(25,8,'Total:','$62.00','62.0000',6,'total'),
(26,9,'Sub-Total:','$350.00','350.0000',1,'subtotal'),
(27,9,'Flat Shipping Rate:','$2.00','2.0000',3,'shipping'),
(28,9,'Total:','$352.00','352.0000',6,'total'),
(29,10,'Sub-Total:','$143.00','143.0000',1,'subtotal'),
(30,10,'Retail 8.5%:','$12.16','12.1550',5,'tax'),
(31,10,'Total:','$155.16','155.1550',6,'total'),
(32,11,'Sub-Total:','$19.00','19.0000',1,'subtotal'),
(33,11,'Flat Shipping Rate:','$2.00','2.0000',3,'shipping'),
(34,11,'Total:','$21.00','21.0000',6,'total'),
(35,12,'Sub-Total:','£289.42','453.5000',1,'subtotal'),
(36,12,'Flat Shipping Rate:','£1.28','2.0000',3,'shipping'),
(37,12,'Retail 8.5%:','£20.34','31.8750',5,'tax'),
(38,12,'Total:','£311.04','487.3750',6,'total'),
(39,13,'Sub-Total:','£367.60','576.0000',1,'subtotal'),
(40,13,'Flat Shipping Rate:','£1.28','2.0000',3,'shipping'),
(41,13,'Retail 8.5%:','£31.25','48.9600',5,'tax'),
(42,13,'Total:','£400.13','626.9600',6,'total');



--
-- Dumping data for table `orders`
--



INSERT INTO `ac_orders` 
VALUES 
(1,0,'',0,'Your Store','http://localhost/',1,8,'fdsfdsf','czx','(092) 222-2222','','demo@abantecart.com','fdsfdsf','czx','','addresss','','testing','55555','Maryland',3643,'United States',223,'{firstname} {lastname} {company} {address_1} {address_2} {city}, {zone} {postcode} {country}','Flat Shipping Rate','fdsfdsf','czx','','addresss','','testing','55555','Maryland',3643,'United States',223,'{firstname} {lastname} {company} {address_1} {address_2} {city}, {zone} {postcode} {country}','Cash On Delivery','','1585.4400',1,1,1,'GBP','1.00000000',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','127.0.0.1',''),
(2,0,'',0,'Web Store Name','http://abantecart/public_html/',11,8,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','','','','','','','','',0,'',0,'','','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','418.8100',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(3,0,'',0,'Web Store Name','http://abantecart/public_html/',5,8,'Gloria','Macias','+1 418-461-2440','','gloriamacias@abantecart.com','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Flat Shipping Rate','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Cash On Delivery','Please ASAP','246.0000',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(4,0,'',0,'Web Store Name','http://abantecart/public_html/',5,8,'Gloria','Macias','+1 418-461-2440','','gloriamacias@abantecart.com','','','','','','','','',0,'',0,'','','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Cash On Delivery','','310.5270',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(5,0,'',0,'Web Store Name','http://abantecart/public_html/',3,8,'Keely','Mccoy','+44 1324 483784 ','','keelymccoy@abantecart.com','Keely','Mccoy','','4842 Amet','','Grangemouth','','Gloucestershire',3552,'United Kingdom',222,'','Flat Shipping Rate','Keely','Mccoy','','4842 Amet','','Grangemouth','','Gloucestershire',3552,'United Kingdom',222,'','Cash On Delivery','','686.0925',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(6,0,'',0,'Web Store Name','http://abantecart/public_html/',2,8,'Juliana','Davis','+44 1688 308321','','julidavis@abantecart.com','','','','','','','','',0,'',0,'','','Juliana','Davis','','Highlands and Islands PA75 6QE','','Isle of Mull','','Highlands',3559,'United Kingdom',222,'','Cash On Delivery','Bulgari','218.0850',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(7,0,'',0,'Web Store Name','http://abantecart/public_html/',9,8,'Carlos','Compton','+1 867-874-22391','','carloscmpton@abantecart.com','','','','','','','','',0,'',0,'','','Carlos','Compton','','31 Capital Drive','','Hay River','','Nova Scotia',608,'Canada',38,'','Cash On Delivery','','175.7700',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(8,0,'',0,'Web Store Name','http://abantecart/public_html/',8,8,'Bruce','Rosarini','+1 807-346-10763','','brucerosarini@abantecart.com','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','62.0000',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(9,0,'',0,'Web Store Name','http://abantecart/public_html/',8,8,'Bruce','Rosarini','+1 807-346-10763','','brucerosarini@abantecart.com','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','352.0000',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(10,0,'',0,'Web Store Name','http://abantecart/public_html/',12,8,'Allen','Waters','+1 540-985-59700','','allenwaters@abantecart.com','','','','','','','','',0,'',0,'','','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','155.1550',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(11,0,'',0,'Web Store Name','http://abantecart/public_html/',12,8,'Allen','Waters','+1 540-985-59700','','allenwaters@abantecart.com','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','21.0000',1,1,1,'USD','1.00000000',0,now(), now(),'109.104.166.98',''),
(12,0,'',0,'Web Store Name','http://abantecart/public_html/',11,8,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','487.3750',2,1,3,'GBP','0.63820000',0,now(), now(),'171.98.12.12',''),
(13,0,'',0,'Web Store Name','http://abantecart/public_html/',11,8,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','626.9600',1,1,3,'GBP','0.63820000',0,now(), now(),'171.98.12.12','');



--
-- Dumping data for table `product_descriptions`
--



INSERT INTO `ac_product_descriptions` 
VALUES 
(73,1,'Highlighting Expressions','','','&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Pro-V COLOUR HAIR SOLUTIONS Highlighting Expressions™ Conditioner protects and enhances colour treated hair and infuses blonde highlights with shine. The advanced Pro-Vitamin formula restores shine to dull highlights and protects hair from daily damage. This non-colour depositing formula works for all blonde highlights.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Pantene uncovered that oxidative colouring changes hair’s structure. Internally, the chemistry in hair colour attacks strength-giving proteins in hair and makes the fibre more porous, leading to weaker hair that fades more quickly. The surface of the hair fibres becomes uneven and the protective layer of the hair fibre disintegrates. Without the protective layer, the hair fibre is prone to micro-scarring and damage, which changes the way it interacts with light and leads to a dull appearance.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s non-colour depositing conditioner is designed to reinforce the structure of blonde highlighted hair and give it what it needs to reveal vibrant, glossy colour. Conditioning ingredients help revitalize and replenish highlighted hair while delivering brilliant shine and protecting from future damage. The result is healthy-looking hair rejuvenated with shimmering blonde highlights.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For rich, vibrant colour that’s shiny and healthy-looking, use with Pantene Pro-V Pantene COLOUR HAIR SOLUTIONS Highlighting Expressions™ Shampoo and Colour Nourishing Treatment.&lt;/p&gt;\r\n'),
(74,1,'Curls to straight Shampoo','','','&lt;p&gt;\r\n	YOUR HAIR’S STARTING POINT&lt;br /&gt;\r\n	Curly&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Curly Hair Series Curls to Straight Shampoo gently removes build-up, adding softness and control to your curls. The cleansing formula helps align and smooth the hair fibers. The result is healthy-looking hair that’s protected from frizz and ready for straight styling.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Research shows that each curly hair fibre grows in a unique pattern, twisting and turning in all directions. This unpredictable pattern makes it difficult to create and control straight styles. The curved fibres of curly hair intersect with each other more often than any other hair type, causing friction which can result in breakage. The curvature of the hair fibre also provides a large amount of volume in curly hair, which can be hard to tame.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s straight shampoo contains micro-smoothers that aid you in loosening and unwinding curls from their natural pattern. Curly hair is left ready for frizz controlled straight styling, and protected from styling damage.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For healthy-looking, curly hair that’s styled straight, use with Curls to Straight Conditioner and Anti-Frizz Straightening Crème.&lt;/p&gt;\r\n'),
(75,1,'Dove Men +Care Body Wash','','','&lt;p&gt;\r\n	A body and face wash developed for men\'s skin with Micromoisture technology.&lt;br /&gt;\r\n	Micromoisture activates on skin when lathering up, clinically proven to fight skin dryness.&lt;br /&gt;\r\n	Deep cleansing gel that rinses off easily. With purifying grains.&lt;br /&gt;\r\n	Dermatologist recommended.&lt;/p&gt;\r\n'),
(76,1,'Men+Care Clean Comfort Deodorant','','','&lt;p&gt;\r\n        The first scented deodorant from Dove® specifically designed with a non-irritating formula to give men the power of 48-hour protection against underarm odor with advanced ¼ moisturizer technology. The bottom line? It’s tough on odor, not on skin&lt;/p&gt;\r\n'),
(77,1,'Men+Care Active Clean Shower Tool','tool, man','','&lt;p&gt;\r\n	Dove® Men+CareTM Active Clean Dual-Sided Shower Tool works with body wash for extra scrubbing power you can’t get from just using your hands. The mesh side delivers the perfect amount of thick cleansing lather, and the scrub side helps exfoliate for a deeper clean. Easy to grip and easy to hang. For best results, replace every 4-6 weeks.&lt;/p&gt;\r\n'),
(78,1,'ck IN2U Eau De Toilette Spray for Him','','','&lt;p&gt;\r\n	Fresh but warm; a tension that creates sexiness.Spontaneous - sexy - connectedCK IN2U him is a fresh woody oriental that penetrates with lime gin fizz and rushes into a combination of cool musks that radiate from top to bottom and leaves you wanting more.&lt;/p&gt;\r\n'),
(79,1,'ck One Gift Set','','','&lt;p&gt;\r\n	2 PC Gift Set includes 3.4 oz EDT Spray + Magnets. Ck One Cologne by Calvin Klein, Two bodies, two minds, and two souls are merged into the heat and passion of one. This erotic cologne combines man and woman with one provocative scent. This clean, refreshing fragrance has notes of bergamot, cardamom, pineapple, papaya, amber, and green tea.&lt;/p&gt;\r\n'),
(50,1,'Skinsheen Bronzer Stick','','','&lt;p&gt;\r\n	Bronzes, shapes and sculpts the face. Sheer-to-medium buildable coverage that looks naturally radiant and sunny. Stashable - and with its M·A·C Surf, Baby look – way cool. Limited edition.&lt;/p&gt;\r\n'),
(51,1,'BeneFit Girl Meets Pearl','','','&lt;p&gt;\r\n	Luxurious liquid pearl…the perfect accessory! This soft golden pink liquid pearl glides on for a breathtakingly luminous complexion. Customise your pearlessence with the easy to use twist up package … a few clicks for a subtle sheen, more clicks for a whoa! glow. Pat the luminous liquid over make up or wear alone for dewy lit from within radiance. It\'s pure pearly pleasure. Raspberry and chamomile for soothing. Light reflecting pigments for exquisite radiance. Sweet almond seed for firming and smoothing. Sesame seed oil for moisturising.Fresh red raspberry scent.&lt;/p&gt;\r\n'),
(52,1,'Benefit Bella Bamba','','','&lt;p&gt;\r\n	Amplify cheekbones and create the illusion of sculpted features with this 3D watermelon blush. Laced with shimmering gold undertones, bellabamba is taking eye popping pretty to the third dimension…you’ll never use traditional blush again! Tip: For a poreless complexion that pops, sweep bellabamba on cheeks after applying porefessional&lt;/p&gt;\r\n'),
(53,1,'Tropiques Minerale Loose Bronzer','','','&lt;p&gt;\r\n	Precious earths, exclusively selected for their luxurious silky texture and gentle quality, are layered with mineral pigments in this lightweight powder to mimic the true color of tanned skin. Unique technology with inalterable earths ensures exquisite wear all day. Mineral blend smoothes complexion, while Aloe Vera helps protect skin from dryness.&lt;/p&gt;\r\n'),
(54,1,'L\'EXTRÊME Instant Extensions Lengthening Mascara','','','&lt;p&gt;\r\n	Extend your lashes up to 60% instantly! This exclusive Fibrestretch formula takes even the smallest natural lashes to dramatic lengths. The patented Extreme Lash brush attaches supple fibers to every eyelash for an instant lash extension effect.&lt;/p&gt;\r\n'),
(55,1,'LE ROUGE ABSOLU Reshaping &amp; Replenishing LipColour SPF 15','','','&lt;p&gt;\r\n	Smoother. Fuller. Absolutely replenished lips. This advanced lip color provides 6-hour care with continuous moisture and protective Vitamin E. Features plumping polymer and non-feathering color to define and reshape lips. Choose from an array of absolutely luxurious shades with a lustrous pearl or satin cream finish.&lt;/p&gt;\r\n'),
(56,1,'Waterproof Protective Undereye Concealer','','','&lt;p&gt;\r\n	This natural coverage concealer lets you instantly eliminate tell-tale signs of stress and fatigue. Provides complete, natural-looking coverage, evens skin tone, covers dark circles and minimizes fine lines around the eyes. The Result: A soft, matte finish&lt;/p&gt;\r\n'),
(57,1,'Delicate Oil-Free Powder Blush','','','&lt;p&gt;\r\n	A sparkling shimmer of colour for a radiant glow. Silky soft, micro-bubble formula glides on easily and evenly. Lasts for hours. Oil-free and oil-absorbing, yet moisture-balancing. Perfect for all skin types.&lt;/p&gt;\r\n'),
(58,1,'&quot;hello flawless!&quot; custom powder foundation with SPF 15','','','&lt;p&gt;\r\n	There are degrees of cover-up…some like less, some like more! Our blendable powder formula with SPF 15 goes on beautifully sheer &amp;amp; builds easily for customized coverage. Sweep on with the accompanying brush for a sheer, natural finish or apply with the sponge for full coverage or spot cover-up. Our 6 flattering shades (2 light, 2 medium, 2 deep) make it incredibly easy to find your perfect shade. Once gals apply &quot;hello flawless!&quot; they\'ll finally have met their match q!&lt;/p&gt;\r\n'),
(59,1,'Viva Glam Lipstick','','','&lt;p&gt;\r\n	Time to wham up the GLAM in VIVA GLAM! It\'s a gaga-glamorous look at our abiding passion: The M·A·C AIDS Fund and the VIVA GLAM program are the heart and soul of M·A·C Cosmetics. Ladies and gentlemen, we give you the sensational Cyndi Lauper and the electric Lady Gaga&lt;/p&gt;\r\n'),
(60,1,'Nail Lacquer','','','&lt;p&gt;\r\n	Revolutionary new high gloss formula. Three long-wearing finishes - Cream, Sheer, and Frosted. Visibly different. Provides no-streak/no-chip finish. Contains conditioners and UV protection. Go hi-lacquer!&lt;/p&gt;\r\n'),
(61,1,'Color Design Eye Brightening All in One 5 Shadow &amp; Liner Palette','','','&lt;p&gt;\r\n	Infinitely luminous. Sensationally smooth. All-in-one 5 shadow palette to brighten eyes. Lancome’s new versatile, all-in-one palette conveniently creates a full eye look for day or night. Experience the newest generation of luminosity as silky lustrous powders transparently wrap the skin, allowing a seamless layering of pure color for a silky sheen and radiant finish. Build with absolute precision and apply the shades in 5 simple steps (all over, lid, crease, highlighter and liner) to design your customized eye look. Contour, sculpt and lift in soft day colors or intensify with dramatic evening hues for smoldering smoky effects. Long wear, 8-hour formula. Color does not fade, continues to stay true&lt;/p&gt;\r\n'),
(62,1,'ck one shock for him Deodorant','','','&lt;p&gt;\r\n	Shock Off! cK one shock for him opens with pure freshness, the heart pulses with spice and finishes with a masculine tobacco musk. Experience ck one shock, the newest fragrance from Calvin Klein with this 2.6 oz Deodorant.&lt;/p&gt;\r\n'),
(63,1,'Pour Homme Eau de Toilette','','','&lt;p&gt;\r\n	An intriguing masculine fragrance that fuses the bracing freshness of Darjeeling tea with the intensity of spice and musk. For those seeking a discreet accent to their personality.&lt;/p&gt;\r\n'),
(64,1,'Beauty Eau de Parfum','','','&lt;p&gt;\r\n	Beauty by Calvin Klein is a sophisticated and feminine fragrance presenting a new scructure to modern florals. Radiating rich and intense luminosity; Beauty leaves a complex and memorable impression. Experience the glamour and strength with the Beauty Eau de Parfum&lt;/p&gt;\r\n'),
(65,1,'Absolue Eye Precious Cells','','','&lt;p&gt;\r\n	Smoothes – Tightens – Regenerates Radiance Exclusive innovation from Lancôme A powerful combination of unique ingredients – Reconstruction Complex and Pro-Xylane™ – has been shown to improve the condition around the stem cells, and stimulate cell regeneration to reconstruct skin to a denser quality*. Results Immediately, the eye contour appears smoother and more radiant. Day 7, signs of fatigue are minimized and the appearance of puffiness is reduced. Day 28, density is improved. Skin is soft and looks healthier. The youthful look of the eye contour is restored. Ophthalmologist – tested. Dermatologist – tested for safety.&lt;/p&gt;\r\n'),
(66,1,'Total Moisture Facial Cream','','','&lt;p&gt;\r\n	Say good-bye to dry skin and hello to “total moisture”. This facial cream provides concentrated immediate &amp;amp; long-term hydration for an ultra radiant complexion. Contains exclusive tri-radiance complex to help develop the skin’s reserves of water &amp;amp; reinforce skin’s moisture barrier for a radiantly refreshed complexion. For normal to dry skin.&lt;/p&gt;\r\n'),
(67,1,'Flash Bronzer Body Gel','','','&lt;p&gt;\r\n	Look irresistible! Discover the self-tanning results you dream of: Instant bronzed glowing body Enriched with natural caramel extract for an immediate, gorgeous, bronzed glow. Exquisitely beautiful tan The perfect balance of self-tanning ingredients helps to achieve an ideal color, providing an even, natural-looking, golden tan. Color development within 30 minutes, lasting up to 5 days. Transfer-resistant formula With an exclusive Color-Set™ complex that smoothes on without streaks, dries in 4 minutes and protects clothes against rub-off. Hydrating &amp;amp; smoothing action Leaves skin soft, smooth, and hydrated. Pure Vitamin E delivers antioxidant protection, helping to reduce signs of premature aging. Indulgent experience Delightfully scented with hints of jasmine and honey in a silky, non-greasy formula&lt;/p&gt;\r\n'),
(68,1,'Absolute Anti-Age Spot Replenishing Unifying TreatmentSPF 15','','','&lt;p&gt;\r\n	A luxurious and comprehensive hand treatment that addresses the special needs of mature hands. Diminishes and discourages the appearance of age spots, while replenishing and protecting the skin. RESULT: Immediately, skin on hands is hydrated, soft and luminous. With continued use, skin becomes more uniform, looks firmer and youthful.Massage into hands and cuticles as needed.&lt;/p&gt;\r\n'),
(69,1,'Seaweed Conditioner','','','&lt;p&gt;\r\n	What it is:&lt;br /&gt;\r\n	A lightweight detangler made with marine seaweed and spirulina.&lt;/p&gt;\r\n&lt;p&gt;\r\n	What it does:&lt;br /&gt;\r\n	This conditioner gently detangles, nourishes, softens, and helps to manage freshly washed hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	What it is formulated WITHOUT:&lt;br /&gt;\r\n	- Parabens&lt;/p&gt;\r\n&lt;p&gt;\r\n	What else you need to know:&lt;br /&gt;\r\n	Made with marine greens for practically anyone (and ideal for frequent bathers), this conditioner is best paired with Seaweed Shampoo. It\'s also safe for use on color-treated hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	-Sea Silk Extract: Nourishes scalp; promotes healthy looking hair.&lt;br /&gt;\r\n	-Ascophyllum Nudosum (Seaweed) Extract: Moisturizes; adds elasticity, luster, softness, body; reduces flyaways.&lt;br /&gt;\r\n	-Macrocystis Pyrifera (Sea Kelp) Extract: Adds shine and manageability.&lt;br /&gt;\r\n	-Spirulina Maxima Extract: Hydrates.&lt;/p&gt;\r\n'),
(70,1,'Eau Parfumee au The Vert Shampoo','','','&lt;p&gt;\r\n	Structured around the refreshing vitality and purtiy of green tea, Bvlgari Eau the Vert Shampoo is an expression of elegance and personal indulgence. Delicately perfumed Eau Parfumée au thé vert shampoo gentle cleansing action makes it perfect for daily use.&lt;/p&gt;\r\n'),
(71,1,'Pantene Pro-V Conditioner, Classic Care','','','&lt;p&gt;\r\n	Conditions hair for healthy shine. How Can You See Healthy Hair? Pantene Complete Therapy Conditioner has a unique pro-vitamin complex that deeply infuses every strand - So you see 6 signs of health hair: Shine; Softness; Strength; Body; Less Frizz; Silkiness. Pantene Complete Therapy Conditioner: The ultimate pro-vitamin therapy provides gentle daily nourishing moisture for enhanced shine; Helps hair detangle easily; Helps prevent frizz and flyaways. Simply use and in just 10 days - and very day after - see shiny hair that\'s soft with less frizz. Best of all, healthy Pantene hair that is strong and more resistant to damage. Made in USA.&lt;/p&gt;\r\n'),
(72,1,'Brunette expressions Conditioner','','','&lt;p&gt;\r\n	YOUR HAIR’S STARTING POINT&lt;br /&gt;\r\n	Colour-Treated or Highlighted&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Pro-V COLOUR HAIR SOLUTIONS Brunette Expressions™ Conditioner hydrates hair for rich colour that is protected from daily stress and damage. This non-colour depositing formula works for all shades of brunette hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Pantene uncovered that oxidative colouring changes hair’s surface. Internally, the chemistry in hair colour attacks strength-giving proteins in hair and makes the fibre more porous, leading to weaker hair and that fades more quickly. The surface of the hair fibres then becomes uneven and the protective layer of the hair fibre disintegrates. Without the protective layer, the hair fibre is prone to micro-scarring and damage, which changes the way it interacts with light and leads to a dull appearance.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s advanced Pro-Vitamin formula enhances brunette colour for great intensity and radiant shine. Non-colour depositing conditioning ingredients enhance and protect colour treated hair, while helping to restore shine to coloured strands. Hair is moisturized and infused with radiant shine.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For rich, vibrant colour that’s shiny and healthy-looking, use with Pantene Pro-V Pantene COLOUR HAIR SOLUTIONS Brunette Expressions™ Shampoo and Colour Nourishing Treatment.&lt;/p&gt;\r\n'),
(80,1,'Acqua Di Gio Pour Homme','','','&lt;p&gt;\r\n	A resolutely masculine fragrance born from the sea, the sun, the earth, and the breeze of a Mediterranean island. Transparent, aromatic, and woody in nature Aqua Di Gio Pour Homme is a contemporary expression of masculinity, in an aura of marine notes, fruits, herbs, and woods.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Notes:&lt;br /&gt;\r\n	Marine Notes, Mandarin, Bergamot, Neroli, Persimmon, Rosemary, Nasturtium, Jasmine, Amber, Patchouli, Cistus.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Transparent, modern, and masculine.&lt;/p&gt;\r\n'),
(81,1,'Armani Eau de Toilette Spray ','','','&lt;p&gt;\r\n	This confidently masculine embodiment of the sophisticated ease and understated elegance of Giorgio Armani fashions - is a simply tailored, yet intensely sensual combination of sparkling fresh fruits, robust spices, and rich wood notes.&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Lime, Bergamot, Mandarin, Sweet Orange, Petitgrain, Cinnamon, Clove, Nutmeg, Jasmine, Neroli, Coriander, Lavender, Oakmoss, Sandalwood, Patchouli, Vetiver, Cedar.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Fresh, masculine, and discreet.&lt;/p&gt;\r\n'),
(82,1,'Armani Code after shave balm','','','&lt;p&gt;\r\n	Splash on this refreshing balm post-shave to soothe and calm the skin. Scents skin with a hint of seductive Code.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Ingredients&lt;br /&gt;\r\n	Alcohol, Aqua/Water/Eau, Parfum/Fragrance, PEG 8, PEG 60 Hydrogenated Castor Oil, BHT, Allantoin (Comfrey Root), Linalool, Geraniol, Alpha Isomethyl Ionone, Coumarin, Limonene, Hydroxyisohexl 3 Cyclohexene Carboxaldehyde, Hydroxycitronellal, Citronellol, Citral, Butylphenyl Methlyproprional, Hexylcinnamal&lt;/p&gt;\r\n'),
(83,1,'Armani Code Sport','','','&lt;p&gt;\r\n	Sport. It\'s a rite of seduction. A vision of Giorgio Armani, translated into a fragrance.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	This scent opens with an explosive freshness that features spearmint, peppermint, and wild mint—surprising and unusual top notes with a stunning effect. The citrusy heart of the fragrance reveals Code Sport\'s seductive power. Notes of vetiver from Haiti reveal a woody and distinguished character, at once wet and dry. Like a crisp coating of ice, a note of hivernal prolongs the dialogue between the scent\'s cool crispness and sensual breath, giving the fragrance an almost unlimited life.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Spearmint, Peppermint, Wild Mint, Citrus, Hivernal, Hatian Vetiver, Nigerian Ginger.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Stunning. Cool. Seductive.&lt;/p&gt;\r\n'),
(84,1,'Armani Code Pour Femme','','','&lt;p&gt;\r\n	A seductive new fragrance for women, Armani Code Pour Femme is a fresh, sexy, feminine blend of zesty blood orange, ginger, and pear sorbet softened with hints of sambac jasmine, orange blossom, and lavender honey, warmed with precious woods and vanilla.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Blood Orange, Ginger, Pear Sorbet, Sambac Jasmine, Orange Blossom, Seringa Flower, Lavender Honey, Precious Woods Complex, Vanilla.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Fresh. Sexy. Feminine.&lt;/p&gt;\r\n'),
(85,1,'Forbidden euphoria Eau de Parfum Spray ','','','&lt;p&gt;\r\n	Possessing an innate confidence and sophistication, she is just starting to explore her sexuality. What she doesn\'t yet know is that she already is every man\'s fantasy.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A younger interpretation of Euphoria, this fruity floriental scent capitalizes on a modern, fresh sexiness with a mysterious twist. Its sparkling top notes seduce the senses with a blend of forbidden fruit such as mandarin, passion fruit, and iced raspberry. The heart blooms with a hypnotic bouquet of tiger orchid and jasmine. Underneath its exotic floralcy lies a layer of addictive patchouli and a sophisticated blend of musks and cashmere woods for an everlasting impression.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Sparkling Mandarin, Peach Blossom, Iced Raspberry, Pink Peony, Tiger Orchid, Jasmine, Cashmere Woods, Patchouli Absolute, Skin Musk.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Sophisticated. Confident. Forbidden.&lt;/p&gt;\r\n'),
(86,1,'Euphoria Men Intense Eau De Toilette Spray','','','&lt;p&gt;\r\n	Launched by the design house of Calvin Klein in 2008, EUPHORIA MEN INTENSE is a men\'s fragrance that possesses a blend of Rain Water, Pepper, Ginger, Sage, Frosted Sudachi, Cedar leaf, Patchouli, Myrrh, Labdanum, Amber Solid, Vetiver&lt;/p&gt;\r\n'),
(87,1,'MAN Eau de Toilette Spray','','','&lt;p&gt;\r\n	Man by Calvin Klein was launched in October of 2007 and proposed as a new classic for the modern Calvin Klein man, aged from 25 to 40. The name itself is programmatic and unambiguous, like an English translation of L\'Homme by Yves Saint Laurent. Simple, brief, to the point. You are going to smell the essence of masculinity if you are to take your cue from the name of the fragrance. The packaging is sleek, modernist, with an architectural sense of proportions and looks good. The fragrance was created by perfumers Jacques Cavallier and Harry Fremont from Firmenich in collaboration with consultant Ann Gottlieb. All these people are old hands at marketing successful mainstream fragrances. Man offers therefore a mainstream palatability but without coming across as depersonalized. It plays the distinctiveness card, but in a well reined in manner. The fragrance bears a typical masculine fresh aromatic, woody and spicy signature around the linear heart of the scent which itself is dark, fruity, and sweet enough to feel feminine. This rich amber-fruity accord is made even more seductive thanks to just the right amount of citrus-y counterpoint, which never clarifies the scent but on the contrary helps to deepen the dark fruity sensation.&lt;br /&gt;\r\n	&nbsp;&lt;/p&gt;\r\n'),
(88,1,'ck one Summer 3.4 oz','','','&lt;p&gt;\r\n	It\'s a concert on a hot summer night. The stage is set and the show\'s about to start. Feel the breeze, catch the vibe, and move to the beat with the pulsating energy of this limited-edition fragrance. A unisex scent, it is fresh, clean, and easy to wear. The fragrance opens with a burst of crisp melon. In the heart notes, an invigorating blend of green citrus and the zesty herbaceous effect of verbena creates a cool, edgy freshness. A base of exotic incense and earthy oakmoss is wrapped in the light, sensuous warmth of cedarwood, musk, and peach skin. Notes:Tangerine, Water Fern, Melon, Lemon, Sea Breeze Accord, Blue Freesia, Verbena, Rhubarb, Cedarwood, Skin Musk, Incense, Peach Skin. Style:Invigorating. Crisp. Cool.&lt;/p&gt;\r\n'),
(89,1,'Secret Obsession Perfume','','','&lt;p&gt;\r\n	Calvin Klein Secret Obsession eau de parfum spray for women blends notes of forbidden fruits, exotic flowers and a sultry wood signature to create an intoxicating aroma that is provocative and addictive.Calvin Klein is one of World of Shops most popular brands, and this Calvin Klein Secret Obsession eau de parfum spray for women is a firm favourite amongst our customers for its deep, feminine aroma that is perfect for those special evenings out.&lt;/p&gt;\r\n'),
(90,1,'Obsession Night Perfume','','','&lt;p&gt;\r\n	Launched by the design house of Calvin Klein in 2005, OBSESSION NIGHT is a women\'s fragrance that possesses a blend of gardenia, tonka bean, bergamot, vanilla, sandalwood, jasmine, rose, amber, muguet and mandarin. It is recommended for evening wear.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Ingredients&lt;br /&gt;\r\n	Notes: Bergamot, Bitter Orange, Mandarin, White Floral, Angelica Root, Gardenia, Rose, Muguet, Night-Blooming Jasmine, Vanilla, Tonka Bean, Amber, Labdanum, Sandalwood, Cashmere Wood&lt;/p&gt;\r\n'),
(91,1,'Jasmin Noir Body Lotion 6.8 fl oz','','','&lt;p&gt;\r\n	A bath collection for the body, scented with the Jasmin Noir fragrance. A tribute to ultimate femininity. Seduction and personal indulgence.&lt;br /&gt;\r\n	Body Lotion Fragrance: The new emblematic creation within the Bvlgari Pour Femme Collection Jasmin Noir, perfectly embodies the luxury and prestige of Bvlgari fine jewelry.&lt;br /&gt;\r\n	Jasmin Noir is a flower of the imagination. Precious jasmine, white and immaculate, in its noire interpretation. A flower of pure mystery. A rich and delicate flower that at nightfall, reveals its intriguing sensuality. A precious floral woody fragrance with ambery accents centered around one of the true jewels of perfumery: the jasmin flower. A scent that conjures forth the bewildering seductiveness of feminity as elegant as it is profoundly sensual.&lt;br /&gt;\r\n	Jasmin Noir tells a voluptuous floral story that begins with the pure radiance of luminous light given by green and scintillating notes: Vegetal Sap and fresh Gardenia Petals. Then, tender and seductive, the Sambac Jasmine Absolute, delivers its generous and bewitching notes. Unexpectedly allied with a transparent silky almond accord, it reveals a heart that is light yet thoroughly exhilarating and marvelously addictive. The scent\'s sumptuously rich notes repose on a bed of Precious Wood and ambery undertones, bringing together the depth and mystery of Patchouli, the warmth of Tonka Bean and the comfort of silky Musks for an elegant and intimate sensuality.&lt;br /&gt;\r\n	An exquisite fragrance of incomparable prestige, Jasmin Noir captures the very essence of the jeweler.&lt;br /&gt;\r\n	Made in Italy&lt;br /&gt;\r\n	&nbsp;&lt;/p&gt;\r\n'),
(92,1,'Body Cream by Bulgari','','','&lt;p&gt;\r\n	BVLGARI (Bulgari) by Bvlgari Body Cream 6.7 oz for Women Launched by the design house of Bvlgari in 1994, BVLGARI is classified as a refined, floral fragrance. This feminine scent possesses a blend of violet, orange blossom, and jasmine. Common spellings: Bulgari, Bvlgary, Bulgary.&lt;/p&gt;\r\n'),
(93,1,'Creme Precieuse Nuit 50ml','','','&lt;p&gt;\r\n	A luxurious, melting night cream to repair skin during sleep Features Polypeptides that boost production of collagen &amp;amp; elastin Improves skin elasticity &amp;amp; firmness Visibly reduces appearance of wrinkles, fine lines &amp;amp; brown spots Enriched with Bvlgari Gem Essence to restore radiance Skin appears smooth, energized &amp;amp; luminous in morning Perfect for all skin types&lt;/p&gt;\r\n'),
(94,1,'Night Care Crema Nera Obsidian Mineral Complex','','','&lt;p&gt;\r\n	When it comes to body, skin or eye care, you want to look to our products and you will find the best there is. These are the most exceptional personal care products available. They meet the strictest standards for quality sourcing, environmental impact, results and safety. Our body care products truly allows you to be good to your whole body.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Giorgio Armani - Cream Nera - Night Care Crema Nera Obsidian Mineral Complex3 Restoring Cream SPF 15 50ml/1.69oz A lavish, fresh &amp;amp; weightless anti-aging creamProvides shielding &amp;amp; moisturizing benefitsDeveloped with Obsidian Mineral Complex technology Formulated with iron, silicon &amp;amp; perlite to create a potent dermal restructuring system Contains Pro-XylaneTM &amp;amp; Hyaluronique Acid Targets loss of substance, sagging of features &amp;amp; deepening of wrinkles Reveals firmer, sleeker &amp;amp; plumper skin in a youthful look. With a fabulous Skincare product like this one, you\'ll be sure to enjoy the ultimate in a Skincare experience with promising results.&lt;/p&gt;\r\n'),
(95,1,'Skin Minerals For Men Cleansing Cream','','','&lt;p&gt;\r\n	Ultra-purifying skincare enriched with essential moisturizing minerals, designed to instantly moisturize / purify the skin.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Sodium and Potassium System: to preserve cutaneous hydration.&lt;br /&gt;\r\n	- Salicylic Acid and Hamamelis Extract: to tighten the pores and tone skin.&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Self-assessment*:&lt;br /&gt;\r\n	- leaves the skin clean 100%&lt;br /&gt;\r\n	- leaves the skin comfortable 93%&lt;br /&gt;\r\n	- leaves the skin smooth 95%&lt;br /&gt;\r\n	- skin complexion is uniform 89%&lt;br /&gt;\r\n	- skin texture is refined 80%&lt;br /&gt;\r\n	* use test: 60 men 20 -65 years old 4 weeks of self-assessment&lt;/p&gt;\r\n'),
(96,1,'Eye master','','','&lt;p&gt;\r\n	The volcanic force of minerals concentrated in multi action skincare specifically designed to target wrinkles, bags and dark circles of the delicate eye area. To combat signs of aging and fatigue and visibly improve skin quality.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Volcanic Complex: an innovative combination of energy charged minerals, inspired by volcanic rocks&lt;br /&gt;\r\n	- Caffeine extract: to fight puffiness&lt;br /&gt;\r\n	- Conker and butcher’s broom extracts to stimulate cutaneous blood micro-circulation&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Proven immediate anti-puffiness action*:&lt;br /&gt;\r\n	- 15 minutes after application –19%&lt;br /&gt;\r\n	*instrumental test, 40 men, 42-65 years old&lt;br /&gt;\r\n	Self-assessment&lt;br /&gt;\r\n	- instantly revitalizes skin 77%**&lt;br /&gt;\r\n	- wrinkles appear reduced 78%***&lt;br /&gt;\r\n	- diminishes the appearance of dark circles 68%***&lt;br /&gt;\r\n	** use test, 40 men 42-65 years old, single application, self-assessment&lt;br /&gt;\r\n	*** use test, 40 men 42-65 years old, 4 weeks, self-assessment&lt;/p&gt;\r\n'),
(97,1,'Eye Rejuvenating Serum','','','&lt;p&gt;\r\n	The first advanced rejuvenating ‘weapon’ thanks to a corrective and smoothing texture and a power amplifying applicator.&lt;br /&gt;\r\n	The alliance of the [3.R] technology combined with an intensive re-smoothing system.&lt;br /&gt;\r\n	The eye rejuvenation serum also comes in an easily portable tube that boasts a silver bevelled applicator to ensure a good delivery of the product to the eye area as well as offering a means to improve circulation and reduce puffiness and eye bags.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Rarely women have been so convinced of its efficiency on skin rejuvenation:&lt;/p&gt;\r\n&lt;p&gt;\r\n	EYE CONTOUR LOOKS SMOOTHER 85%*&lt;br /&gt;\r\n	EYES LOOK YOUNGER 91%*&lt;br /&gt;\r\n	EYE PUFFINESS LOOKS SOFTENED 83%*&lt;/p&gt;\r\n&lt;p&gt;\r\n	*% of women – self assessment on 60 women after 4 weeks&lt;/p&gt;\r\n'),
(98,1,'Shaving cream','','','&lt;p&gt;\r\n	Moisturizing, charged with minerals and enriched with ultra softening agents. Its specific formula ensures an optimal, extremely gentle shave. Even four hours after shaving, the skin remains hydrated, soft and supple.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Sodium and Potassium System: to preserve cutaneous hydration&lt;br /&gt;\r\n	- Bisabolol: to soothe skin&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Measurements 4 hours after shaving:&lt;br /&gt;\r\n	- skin hydration +29%*&lt;br /&gt;\r\n	- skin softness +61%**&lt;br /&gt;\r\n	- skin suppleness +18%**&lt;br /&gt;\r\n	- skin dryness -39%**&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;br /&gt;\r\n	* instrumental test, 20 men, 20-70 years old&lt;br /&gt;\r\n	** clinical scorage, 20 men, 20-70 years old&lt;/p&gt;\r\n'),
(99,1,'Fluid shine nail polish','','','&lt;p&gt;\r\n	Luxurious color at your fingertips. Fluid shine coats nails with intense shine and long-lasting, sophisticated color. The essential accessory to any makeup wardrobe.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Discover the Bronze collection 2010&lt;br /&gt;\r\n	Finish this season’s high summer look with a cranberry n°43 or blackberry n°44 nail, to echo the wet lips with intense color.&lt;/p&gt;\r\n'),
(100,1,'Smooth silk lip pencils','','','&lt;p&gt;\r\n	An incredibly soft lip pencil for subtle, precise definition. The silky texture allows for easy application and flawless results. To extend the hold of your lip color, fill lips in completely with Smooth silk lip pencil before applying your lipstick. Choose from a wide range of shades to complement every color in your lipstick wardrobe.&lt;/p&gt;\r\n'),
(101,1,'Pro-V Color Hair Solutions Color Preserve Shine Conditioner','pantene, shampoo','','&lt;p&gt;\r\n	PANTENE\'s color preserve shine shampoo and conditioner system with micro-polishers smoothes and refinishes the hair’s outer layer. So your hair reflects light and shines brilliantly. Help preserve your multi-dimensional color.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Product Features&lt;/strong&gt;&lt;br /&gt;\r\n	Micro-polishers smooth the outer layer of hair to help Protect color and leave hair shiny&lt;br /&gt;\r\n	Lightweight moisturizers provide protection against damage&lt;br /&gt;\r\n	Designed for color-treated hair; Gentle enough for permed hair&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Ingredients&lt;/strong&gt;&lt;br /&gt;\r\n	Water, Stearyl Alcohol, Behentrimonium Methosulfate, Cetyl Alcohol, Fragrance, Bis-Aminopropyl Dimethicone, Isopropyl Alcohol, Benzyl Alcohol, Disodium EDTA, Panthenol, Panthenyl Ethyl Ether, Methylchloroisothiazolinone, Methylisothiazolinone&lt;/p&gt;\r\n&lt;p&gt;\r\n	&nbsp;&lt;/p&gt;\r\n'),
(102,1,'Gucci Guilty','gicci, spray','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		Notes Consist Of Mandarin, Pink Pepper, Peach, Lilac, Geranium, Amber And Patchouli&lt;/li&gt;\r\n	&lt;li&gt;\r\n		For Casual Use&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;Gucci Guilty&lt;/em&gt; is a warm yet striking oriental floral with hedonism at its heart.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The scent seizes the attention with a flamboyant opening born of the natural rush that is mandarin shimmering alongside an audacious fist of pink pepper.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The middle notes are an alluring concoction of heady lilac and geranium, laced with the succulent tactility of peach - all velvet femininity with a beguiling hint of provocation.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The patchouli that is the hallmark of Gucci fragrances here conveys a message of strength, while the voluptuousness of amber suggests deep femininity.&lt;/p&gt;\r\n'),
(103,1,'Jasmin Noir L\'Essence Eau de Parfum Spray 75ml','','','&lt;p&gt;\r\n	A carnal impression of the immaculate jasmine flower, Bvlgari Jasmin Noir L\'Essence dresses the purity of the bloom in jet black mystery.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The fragrance is a more concentrated Eau de Parfum than the original Jasmin Noir, a blend of rare and precious ingredients that are more seductive, and more addictive than ever before. The profoundly sensual elixir captivates the senses, and enchants its wearer with its generous and bewitching touches.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A luminous bottle that honours the heritage of Bvlgari.&lt;/p&gt;\r\n'),
(104,1,'Calvin Klein Obsession For Women EDP Spray','','','&lt;p&gt;\r\n	Citrus, vanilla and greens lowering to notes of sandalwood, spices and musk. Recommended Use daytime&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;\'Obsession\'&lt;/em&gt; perfume was launched by the design house of Calvin Klein in 1985&lt;/p&gt;\r\n&lt;p&gt;\r\n	When you think about Calvin Klein, initially you think of his clothing line – specifically his jeans and underwear lines (not to mention the famous ad with a young Brooke Shields). But Calvin Klein’s penchant for perfume was equally as cutting edge as his foray into fashion.&lt;/p&gt;\r\n'),
(105,1,'Bvlgari Aqua','','','&lt;p&gt;\r\n	Bvlgari Aqua (Pour Homme) Eau De Toilette Spray is an enticing and fresh cologne that exudes masculinity from its unique blend of amber santolina, posidonia and mandarin.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Bvlgari Aqua (Pour Homme) Eau De Toilette Spray scent lasts throughout the day without having an overpowering smell. It is subtle enough for daytime use and masculine enough for night wear.&lt;/p&gt;\r\n'),
(106,1,'Omnia Eau de Toilette 65ml','bvlgary, omnia, EDT','','&lt;p&gt;\r\n	Choose Your scent&lt;/p&gt;\r\n&lt;p&gt;\r\n	&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Coral:&lt;/strong&gt; Inspired by the shimmering hues of precious red coral, Omnia Coral is a radiant floral-fruity Eau de Toilette of tropical Hibiscus and juicy Pomegranate, reminiscent of Summer, the sun, resplendent nature and far-off oceans.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Amethyst:&lt;/strong&gt; Inspired by the shimmering hues of the amethyst gemstone, this floral Eau de Toilette captures the myriad scents of Iris and Rose gardens caressed with morning dew.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Crystalline:&lt;/strong&gt; Created from the glowing clarity and purity of crystal, Omnia Crystalline is a sparkling jewel of light, illuminating and reflecting the gentle sensuality and luminous femininity. Sparkling like a precious jewel, like the rarest of crystals, in an exquisite jewel flacon.&lt;/p&gt;\r\n'),
(107,1,'Lancome Slimissime 360 Slimming Activating Concentrate Unisex Treatment','','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		An anti-cellulite body treatment&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Features a special gel-cream texture &amp;amp; a quick-dissolving formula&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Formulated with an exclusive 360 Complex&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	An anti-cellulite body treatment. Features a special gel-cream texture &amp;amp; a quick-dissolving formula. Formulated with an exclusive 360 Complex. Helps combat presence of cellulite &amp;amp; reduce existing cellulite. Provides immediate invigorating &amp;amp; firming results. Concentrated with micro-pearl particles to illuminate skin. Creates svelte &amp;amp; re-sculpted body contours....&lt;/p&gt;\r\n'),
(108,1,'Lancome Hypnose Doll Lashes Mascara 4-Piece Gift Set','','','&lt;p&gt;\r\n	&nbsp;Lancome Hypnose Doll Lashes Mascara 4-Piece Gift Set! Limited Edition!&lt;/p&gt;\r\n&lt;ol&gt;\r\n	&lt;li&gt;\r\n		0.22 oz full-size Hypnôse Doll Lashes Mascara in Black&lt;/li&gt;\r\n	&lt;li&gt;\r\n		0.07 oz full-size Le Crayon Khol Eyeliner in Black Ebony&lt;/li&gt;\r\n	&lt;li&gt;\r\n		0.07 oz travel-size Cils Booster XL Super Enhancing Mascara Base&lt;/li&gt;\r\n	&lt;li&gt;\r\n		1.7 fl oz travel-size Bi-Facil Double-Action Eye Makeup Remover&lt;/li&gt;\r\n&lt;/ol&gt;\r\n'),
(109,1,'Lancome Visionnaire Advanced Skin Corrector','','','&lt;p&gt;\r\n	Lancôme innovates with VISIONNAIRE [LR 2412 &nbsp;4%], its ﬁrst&nbsp;skincare product formulated to fundamentally recreate truly&nbsp;beautiful skin.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A Lancôme technological breakthrough has identiﬁed&nbsp;a miraculous new molecule.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The name of this molecule: LR 2412.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A molecule that is able to “self-propel” through the layers&nbsp;of the epidermis, to set off a series of tissular micro-transformations.&nbsp;The result is that skin is visibly transformed: the texture is ﬁner,&nbsp;wrinkles are erased, pigmentary and vascular irregularities are&nbsp;reduced and pores are tightened.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;Download Presentation file after order.&lt;/em&gt;&lt;/p&gt;\r\n'),
(110,1,'Flora By Gucci Eau Fraiche','','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		Perfect for all occasions&lt;/li&gt;\r\n	&lt;li&gt;\r\n		This item is not a tester; New and sealed&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Contains natural ingredients&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	Gucci presents the new spring version of this perfume called flora by Gucci eau fraiche in 2011. Even younger, more airy, vivid, sparkling and fresher than the original, the new fragrance is enriched with additional aromas of citruses in the top notes and aquatic and green nuances in the heart, while the base remains unchanged. The composition begins with mandarin, bergamot, kumquat, lemon and peony. The heart is made of rose petals and Osman thus with green and aquatic additions, laid on the base of sandalwood, patchouli and pink pepper.&lt;/p&gt;\r\n');


--
-- Dumping data for table `product_option_descriptions`
--


INSERT INTO `ac_product_option_descriptions` (`product_option_id`,`language_id`,`product_id`,`name`)
VALUES 
(318,1,53,'Color'),
(315,1,54,'Color'),
(319,1,56,'Color'),
(304,1,57,'Color'),
(305,1,59,'Color'),
(306,1,55,'Color'),
(307,1,60,'Color'),
(308,1,61,'Color'),
(316,1,63,'Fragrance Size'),
(314,1,64,'Fragrance Size'),
(317,1,69,'Size'),
(320,1,78,'Fragrance Size'),
(321,1,80,'Fragrance Size'),
(322,1,84,'Size'),
(323,1,85,'Fragrance Size'),
(324,1,89,'Fragrance Size'),
(326,1,90,'Fragrance Size'),
(327,1,99,'Color'),
(328,1,100,'Color'),
(329,1,101,'Size'),
(330,1,102,'Size'),
(331,1,104,'Size'),
(332,1,104,'Gift Wrapping'),
(335,1,105,'Fragrance Type'),
(336,1,105,'Gift Wrapping'),
(337,1,105,'Size'),
(338,1,106,'Choose Scent'),
(339,1,106,'Gift Wrapping'),
(340,1,109,'Gift Wrapping'),
(341,1,110,'Size');


--
-- Dumping data for table `product_option_value_descriptions`
--



INSERT INTO `ac_product_option_value_descriptions` (product_option_value_id, language_id, product_id, name) 
VALUES 
(653,1,53,'Natural Ambre'),
(652,1,53,'Natural Golden'),
(646,1,54,'Brown'),
(645,1,54,'Black'),
(658,1,56,'Suede'),
(657,1,56,'Light Bisque'),
(656,1,56,'Ivore'),
(655,1,56,'Dore'),
(654,1,56,'Bronze'),
(612,1,57,'Pink Pool'),
(613,1,57,'Mandarin Sky'),
(614,1,57,'Brilliant Berry'),
(615,1,59,'Viva Glam IV'),
(616,1,59,'Viva Glam II'),
(617,1,59,'Viva Glam VI'),
(618,1,55,'La Base'),
(619,1,55,'Lacewood'),
(620,1,55,'Smoky Rouge'),
(621,1,55,'Tulipwood'),
(622,1,60,'Shirelle'),
(623,1,60,'Vintage Vamp'),
(624,1,60,'Nocturnelle'),
(625,1,61,'Golden Frenzy'),
(626,1,61,'Gris Fatale'),
(627,1,61,'Jade Fever'),
(649,1,63,'1.7 oz'),
(648,1,63,'2.5 oz'),
(647,1,63,'3.4 oz'),
(644,1,64,'3.4 oz'),
(643,1,64,'1.7 oz'),
(642,1,64,'1.0 oz'),
(651,1,69,'33.8 oz'),
(650,1,69,'8 oz'),
(662,1,78,'50ml'),
(661,1,78,'150ml'),
(660,1,78,'100ml'),
(659,1,56,'Light Buff'),
(663,1,80,'1.7 oz'),
(664,1,80,'3.4 oz'),
(665,1,80,'6.7 oz'),
(666,1,84,'30 ml'),
(667,1,84,'50 ml'),
(668,1,84,'75 ml'),
(669,1,85,'1 oz'),
(670,1,85,'1.7 oz'),
(671,1,85,'3.4 oz'),
(672,1,89,'0.04 oz'),
(673,1,89,'6.7 oz'),
(674,1,89,'1.7 oz'),
(676,1,90,'1.7 oz EDP Spray'),
(677,1,90,'3.4 oz EDP Spray'),
(678,1,99,'rose beige'),
(679,1,99,'cranberry'),
(680,1,99,'cassis'),
(681,1,100,'beige'),
(682,1,100,'red beige'),
(683,1,100,'brique'),
(684,1,100,'brown'),
(685,1,100,'mauve'),
(686,1,100,'red'),
(687,1,101,'8.45 oz'),
(688,1,101,'15.2 oz'),
(689,1,101,'33.8 oz'),
(690,1,102,'30ml'),
(691,1,102,'50ml'),
(692,1,102,'75ml'),
(714,1,104,'1 oz'),
(713,1,104,'1.7 oz'),
(722,1,105,'Eau de Cologne'),
(721,1,105,'Eau de Toilette'),
(720,1,105,'Eau de Parfum'),
(719,1,105,'yes'),
(723,1,105,'1 oz'),
(724,1,105,'1.7 oz'),
(733,1,106,'Crystalline'),
(732,1,106,'Amethyst'),
(731,1,106,'Coral'),
(735,1,106,'yes'),
(737,1,109,'yes'),
(738,1,110,'30ml'),
(739,1,110,'50ml'),
(740,1,110,'75ml');


--
-- Dumping data for table `product_option_values`
--

INSERT INTO `ac_product_option_values`
(
product_option_value_id,
product_option_id,
product_id,
group_id,
sku,
quantity,
subtract,
price,
prefix,
weight,
weight_type,
attribute_value_id,
sort_order)
VALUES 
(646,315,54,0,'',983,1,'5.0000','$','0.00000000','lb',0,0),
(653,318,53,0,'',2000,1,'0.0000','$','0.00000000','lb',0,0),
(652,318,53,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(645,315,54,0,'',1000,1,'0.0000','$','0.00000000','lb',0,0),
(659,319,56,0,'',999,1,'0.0000','$','0.00000000','lb',0,2),
(658,319,56,0,'',0,0,'0.0000','$','0.00000000','lb',0,1),
(657,319,56,0,'',998,1,'1.0000','$','0.00000000','lb',0,0),
(656,319,56,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(655,319,56,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(654,319,56,0,'',555,0,'0.0000','$','0.00000000','lb',0,0),
(612,304,57,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(613,304,57,0,'',999,1,'0.0000','$','0.00000000','lb',0,0),
(614,304,57,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(615,305,59,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(616,305,59,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(617,305,59,0,'',1000,1,'2.0000','$','0.00000000','lb',0,0),
(618,306,55,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(619,306,55,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(620,306,55,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(621,306,55,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(622,307,60,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(623,307,60,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(624,307,60,0,'',0,0,'1.4200','$','0.00000000','lb',0,0),
(625,308,61,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(626,308,61,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(627,308,61,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(649,316,63,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(648,316,63,0,'',0,0,'20.0000','$','0.00000000','lb',0,0),
(647,316,63,0,'',0,0,'25.0000','$','0.00000000','lb',0,0),
(644,314,64,0,'',66,1,'22.0000','$','0.00000000','lb',0,0),
(643,314,64,0,'',1000,1,'10.0000','$','0.00000000','lb',0,0),
(642,314,64,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(651,317,69,0,'',553,1,'30.0000','$','0.00000000','lb',0,0),
(650,317,69,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(662,320,78,0,'',59,1,'0.0000','$','0.00000000','lb',0,0),
(661,320,78,0,'',887,1,'16.0000','$','0.00000000','lb',0,0),
(660,320,78,0,'',998,1,'8.0000','$','0.00000000','lb',0,0),
(663,321,80,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(664,321,80,0,'',0,0,'25.0000','$','0.00000000','lb',0,0),
(665,321,80,0,'',0,0,'45.0000','$','0.00000000','lb',0,0),
(666,322,84,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(667,322,84,0,'',1000,1,'20.0000','$','0.00000000','lb',0,0),
(668,322,84,0,'',0,0,'32.0000','$','0.00000000','lb',0,0),
(669,323,85,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(670,323,85,0,'',100,0,'18.0000','$','0.00000000','lb',0,0),
(671,323,85,0,'',0,0,'23.5000','$','0.00000000','lb',0,0),
(672,324,89,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(673,324,89,0,'',0,0,'30.0000','$','0.00000000','lb',0,0),
(674,324,89,0,'',1000,1,'10.0000','$','0.00000000','lb',0,0),
(676,326,90,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(677,326,90,0,'',556,1,'15.0000','$','0.00000000','lb',0,0),
(678,327,99,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(679,327,99,0,'',50,1,'0.0000','$','0.00000000','lb',0,0),
(680,327,99,0,'',48,1,'0.0000','$','0.00000000','lb',0,0),
(681,328,100,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(682,328,100,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(683,328,100,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(684,328,100,0,'',46,1,'0.0000','$','0.00000000','lb',0,0),
(685,328,100,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(686,328,100,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(687,329,101,0,'',256,0,'-2.0000','$','0.80000000','lb',4,0),
(688,329,101,0,'',155,0,'4.0000','$','0.15000000','lb',5,0),
(689,329,101,0,'',100,1,'10.0000','$','0.33000000','lb',6,0),
(690,330,102,0,'',55,0,'0.0000','$','0.00000000','lb',17,0),
(691,330,102,0,'',55,0,'20.0000','$','0.00000000','lb',18,0),
(692,330,102,0,'',55,0,'30.0000','$','0.00000000','lb',19,0),
(714,331,104,0,'',50,0,'0.0000','$','0.00000000','lb',53,0),
(713,331,104,0,'',50,0,'20.0000','$','0.00000000','lb',54,0),
(700,332,104,0,'',0,0,'3.0000','$','0.00000000','lb',0,0),
(722,335,105,0,'',100,1,'24.0000','$','0.00000000','lb',77,0),
(721,335,105,0,'',44,1,'21.0000','$','0.00000000','lb',76,0),
(720,335,105,0,'',25,1,'60.0000','$','0.00000000','lb',75,0),
(719,336,105,0,'',0,0,'2.5000','$','0.00000000','lb',0,0),
(723,337,105,0,'',0,0,'0.0000','$','0.00000000','lb',53,0),
(724,337,105,0,'',0,0,'25.0000','%','0.00000000','lb',54,0),
(733,338,106,0,'',80,1,'0.0000','$','0.00000000','lb',0,0),
(732,338,106,0,'',59,1,'0.0000','$','0.00000000','lb',0,0),
(731,338,106,0,'',120,1,'0.0000','$','0.00000000','lb',0,0),
(735,339,106,0,'',0,0,'1.5000','$','0.00000000','lb',0,0),
(737,340,109,0,'',0,0,'0.0000','$','0.00000000','lb',0,0),
(738,341,110,0,'',97,1,'0.0000','$','0.00000000','lb',50,0),
(739,341,110,0,'',120,1,'15.0000','$','0.00000000','lb',51,0),
(740,341,110,0,'',56,1,'30.0000','$','0.00000000','lb',52,0);



--
-- Dumping data for table `product_options`
--
INSERT INTO `ac_product_options`
     (`product_option_id`,
      `attribute_id`,
      `product_id`,
      `group_id`,
      `sort_order`,
      `status`,
      `element_type`,
      `required` )
VALUES 
(315,0,54,0,0,1,'S',0),
(318,0,53,0,2,1,'S',0),
(319,0,56,0,0,1,'S',0),
(304,0,57,0,0,1,'S',0),
(305,0,59,0,0,1,'S',0),
(306,0,55,0,0,1,'S',0),
(307,0,60,0,0,1,'S',0),
(308,0,61,0,0,1,'S',0),
(316,0,63,0,0,1,'S',0),
(314,0,64,0,0,1,'S',0),
(317,0,69,0,0,1,'S',0),
(320,0,78,0,0,1,'S',0),
(321,0,80,0,0,1,'S',0),
(322,0,84,0,0,1,'S',0),
(323,0,85,0,0,1,'S',0),
(324,0,89,0,0,1,'S',0),
(326,0,90,0,0,1,'S',0),
(327,0,99,0,0,1,'S',0),
(328,0,100,0,0,1,'S',0),
(329,1,101,0,0,1,'S',1),
(330,1,102,0,0,1,'S',0),
(331,1,104,0,0,1,'S',1),
(332,2,104,0,0,1,'C',0),
(335,5,105,0,0,1,'G',1),
(336,2,105,0,5,1,'C',0),
(337,1,105,0,2,1,'S',1),
(338,0,106,0,1,1,'S',1),
(339,2,106,0,2,1,'C',0),
(340,2,109,0,0,1,'C',0),
(341,1,110,0,0,1,'S',1);

--
-- Dumping data for table `product_specials`
--

INSERT INTO `ac_product_specials` 
VALUES 
(252,51,1,0,'19.0000','0000-00-00','0000-00-00',now(),now()),
(253,55,1,0,'27.0000','0000-00-00','0000-00-00',now(),now()),
(254,67,1,0,'29.0000','0000-00-00','0000-00-00',now(),now()),
(255,72,1,0,'24.0000','0000-00-00','0000-00-00',now(),now()),
(256,88,1,0,'27.0000','0000-00-00','0000-00-00',now(),now()),
(257,93,1,0,'220.0000','0000-00-00','0000-00-00',now(),now()),
(258,65,1,1,'89.0000','0000-00-00','0000-00-00',now(),now()),
(259,68,1,1,'35.0000','0000-00-00','0000-00-00',now(),now()),
(260,80,1,1,'45.0000','0000-00-00','0000-00-00',now(),now()),
(261,81,1,1,'49.0000','0000-00-00','0000-00-00',now(),now());

--
-- Dumping data for table `product_tags`
--

INSERT INTO `ac_product_tags` 
VALUES 
(50,'cheeks',1),
(50,'makeup',1),
(51,'cheeks',1),
(51,'makeup',1),
(54,'eye',1),
(54,'makeup',1),
(77,'body',1),
(77,'men',1),
(77,'shower',1),
(78,'fragrance',1),
(78,'men',1),
(79,'fragrance',1),
(79,'men',1),
(79,'unisex',1),
(79,'women',1),
(85,'fragrance',1),
(85,'women',1),
(87,'fragrance',1),
(89,'fragrance',1),
(89,'woman',1),
(95,'gift',1),
(95,'man',1),
(96,'man',1),
(96,'skincare',1),
(98,'man',1),
(99,'nail',1),
(99,'women',1),
(101,'conditioner',1),
(103,'spray',1),
(108,'gift',1),
(108,'pen',1),
(108,'set',1);


--
-- Dumping data for table `products`
--



INSERT INTO `ac_products` (`product_id`,`model`,`sku`,`location`,`quantity`,`stock_status_id`,`manufacturer_id`,`shipping`,`price`,`tax_class_id`,`date_available`,`weight`,`weight_class_id`,`length`,`width`,`height`,`length_class_id`,`status`,`date_added`,`date_modified`,`viewed`,`sort_order`,`subtract`,`minimum`,`cost`) 
VALUES 
(68,'108681','','',1000,1,15,1,'42.0000',1,'2013-08-30','0.11',1,'0.00','0.00','0.00',0,1,now(), now(),0,1,1,1,'24.0000'),
(65,'427847','','',1000,1,15,1,'105.0000',1,'2013-08-30','70.00',2,'0.00','0.00','0.00',0,1,now(), now(),21,1,0,1,'99.0000'),
(66,'556240','','',145,1,12,1,'38.0000',1,'2013-08-30','0.40',1,'0.00','0.00','0.00',0,1,now(), now(),4,1,1,1,'0.0000'),
(67,'463686','','',1000,1,15,1,'34.5000',1,'2013-08-30','0.30',1,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'22.0000'),
(50,'558003','','',99,1,11,1,'29.5000',1,'2013-08-29','75.00',2,'0.00','0.00','0.00',0,1,now(), now(),8,1,0,1,'0.0000'),
(51,'483857','','',98,1,12,1,'30.0000',1,'2013-08-29','0.05',1,'0.00','0.00','0.00',0,1,now(), now(),7,1,1,1,'0.0000'),
(52,'523755','','',99,1,12,1,'28.0000',0,'2013-08-29','0.80',1,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,2,'0.0000'),
(53,'380440','','',1000,3,15,1,'38.5000',1,'2013-08-29','100.00',2,'0.00','0.00','0.00',0,1,now(), now(),5,1,1,1,'22.0000'),
(54,'74144','','',999,1,15,1,'25.0000',1,'2013-08-29','0.15',1,'0.00','0.00','0.00',0,1,now(), now(),10,1,1,1,'0.0000'),
(55,'tw152236','','',1000,1,15,1,'29.0000',1,'2013-08-29','0.08',1,'0.00','0.00','0.00',0,1,now(), now(),5,1,1,1,'22.0000'),
(56,'35190','','',1000,1,15,1,'29.5000',1,'2013-08-29','85.00',2,'0.00','0.00','0.00',0,1,now(), now(),9,1,1,1,'0.0000'),
(57,'117148','','',1000,1,15,1,'29.5000',1,'2013-08-29','0.20',1,'0.00','0.00','0.00',0,1,now(), now(),12,1,1,1,'0.0000'),
(58,'374002','','',0,2,12,1,'34.0000',1,'2013-08-29','25.00',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'10.0000'),
(59,'14.50','','',1000,1,11,1,'5.0000',1,'2013-08-29','75.00',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'0.0000'),
(60,'112423','','',1000,1,11,1,'15.0000',1,'2013-08-30','0.30',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,0,1,'0.0000'),
(61,'529071','','',1000,1,15,1,'48.0000',1,'2013-08-30','0.13',2,'0.00','0.00','0.00',0,1,now(), now(),4,1,0,1,'29.0000'),
(62,'601232','','',1000,1,13,1,'14.0000',1,'2013-08-30','0.50',1,'0.00','0.00','0.00',0,1,now(), now(),3,1,0,1,'8.0000'),
(63,'374622','','',1000,1,14,1,'88.0000',1,'2013-08-30','0.75',1,'0.00','0.00','0.00',0,1,now(), now(),3,1,0,1,'55.0000'),
(64,'497303','','',1000,1,13,1,'50.0000',1,'2013-08-30','150.00',2,'0.00','0.00','0.00',0,1,now(), now(),8,1,1,1,'33.0000'),
(69,'SCND001','','',1000,1,16,1,'19.0000',1,'2013-08-30','0.25',1,'0.00','0.00','0.00',0,1,now(), now(),6,1,0,1,'0.0000'),
(70,'522823','','',1000,1,14,1,'31.0000',1,'2013-08-30','0.25',2,'0.00','0.00','0.00',0,1,now(), now(),1,1,1,1,'0.0000'),
(71,'PCND001','','',1000,1,17,1,'11.4500',1,'2013-08-30','0.30',1,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'5.0000'),
(72,'PCND002','','',1000,1,17,1,'27.0000',1,'2013-08-30','0.40',1,'0.00','0.00','0.00',0,1,now(), now(),4,1,1,1,'0.0000'),
(73,'PCND003','','',1000,1,17,1,'33.0000',1,'2013-08-30','0.40',1,'0.00','0.00','0.00',0,1,now(), now(),1,1,1,1,'21.0000'),
(74,'PCND004','','',10000,1,17,1,'4.0000',1,'2013-08-30','0.35',1,'0.00','0.00','0.00',0,1,now(), now(),3,1,1,1,'0.0000'),
(75,'DMBW0012','','',1000,1,18,1,'6.7000',1,'2013-08-30','0.20',1,'0.00','0.00','0.00',0,1,now(), now(),1,1,1,1,'0.0000'),
(76,'DMBW0013','1235B','',99,1,18,1,'7.2000',1,'2013-08-30','0.20',1,'0.00','0.00','0.00',0,1,now(), now(),5,1,1,1,'4.0000'),
(77,'DMBW0014','1234B','',1000,1,18,1,'6.0000',1,'2013-08-30','0.30',1,'0.00','0.00','0.00',0,1,now(), now(),9,1,1,1,'2.0000'),
(78,'Cl0001','','',1000,1,13,1,'29.0000',1,'2013-08-30','125.00',2,'0.00','0.00','0.00',0,1,now(), now(),10,1,1,1,'0.0000'),
(79,'CKGS01','','',1000,1,13,1,'36.0000',1,'2013-08-30','250.00',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'28.0000'),
(80,'GRM001','','',850,1,19,1,'59.0000',1,'2013-09-01','80.00',2,'0.00','0.00','0.00',0,1,now(), now(),5,1,1,1,'33.0000'),
(81,'GRM002','','',1000,1,19,1,'61.0000',1,'2013-09-01','150.00',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'0.0000'),
(82,'GRM003','','',1000,1,19,1,'42.0000',1,'2013-09-01','100.00',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'0.0000'),
(83,'GRM004','','',1000,1,19,1,'37.5000',1,'2013-09-01','15.00',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'0.0000'),
(84,'GRM005','','',1000,1,19,1,'30.0000',1,'2013-09-01','175.00',2,'0.00','0.00','0.00',0,1,now(), now(),7,1,1,1,'0.0000'),
(85,'Ck0010','','',1000,1,13,1,'45.0000',1,'2013-09-01','0.08',5,'0.00','0.00','0.00',0,1,now(), now(),3,1,1,1,'0.0000'),
(86,'CK0009','','',1,1,13,1,'44.1000',1,'2013-09-04','0.17',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'0.0000'),
(87,'CK0010','','',10000,1,13,1,'37.5000',1,'2013-09-04','0.20',1,'0.00','0.00','0.00',0,1,now(), now(),1,1,1,1,'0.0000'),
(88,'CK0011','','',1,1,13,1,'31.0000',1,'2013-09-04','340.00',2,'0.00','0.00','0.00',0,1,now(), now(),1,1,1,1,'19.0000'),
(89,'CK0012','','',1000,3,13,1,'62.0000',1,'2013-09-04','0.12',1,'0.00','0.00','0.00',0,1,now(), now(),5,1,1,1,'40.0000'),
(90,'CK0013','','',1000,1,13,1,'39.0000',1,'2013-09-04','0.33',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'0.0000'),
(91,'BVLG001','','',1000,1,14,1,'29.0000',1,'2013-09-04','0.16',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'20.0000'),
(92,'BVLG002','','',1000,1,14,1,'57.0000',1,'2013-09-04','0.40',5,'0.00','0.00','0.00',0,1,now(), now(),7,1,1,1,'44.0000'),
(93,'BVLG003','','',1000,1,14,1,'280.0000',1,'2013-09-04','0.30',5,'0.00','0.00','0.00',0,1,now(), now(),8,1,1,1,'100.0000'),
(94,'GRMBC001','','',589,1,19,1,'263.0000',1,'2013-09-04','0.15',1,'0.00','0.00','0.00',0,1,now(), now(),3,1,1,1,'125.0000'),
(95,'GRMBC002','','',100,3,19,1,'104.0000',1,'2013-09-04','0.15',1,'0.00','0.00','0.00',0,1,now(), now(),5,1,1,1,'0.0000'),
(96,'GRMBC003','','',100,1,19,1,'82.0000',1,'2013-09-04','80.00',2,'0.00','0.00','0.00',0,1,now(), now(),8,1,0,2,'67.0000'),
(97,'GRMBC004','','',1,1,19,1,'126.0000',1,'2013-09-04','20.00',2,'0.00','0.00','0.00',0,1,now(), now(),9,1,1,1,'0.0000'),
(98,'GRMBC005','','',1000,1,19,1,'98.0000',1,'2013-09-04','40.00',2,'0.00','0.00','0.00',0,1,now(), now(),2,1,1,1,'87.0000'),
(99,'GRMBC006','','',1000,1,19,1,'137.0000',1,'2013-09-04','0.09',6,'0.00','0.00','0.00',0,1,now(), now(),12,1,1,1,'0.0000'),
(100,'GRMBC007','','',1000,1,19,1,'10.0000',1,'2013-09-04','15.00',2,'0.00','0.00','0.00',0,0,now(), now(),13,1,1,4,'8.0000'),
(101,'Pro-V','','',1000,1,17,1,'8.2300',1,'2012-03-13','8.45',6,'2.00','3.00','15.00',1,1,now(), now(),35,1,0,1,'0.0000'),
(102,'PRF00269','','',1000,1,20,1,'105.0000',1,'2012-03-14','2.50',6,'0.00','0.00','0.00',3,1,now(), now(),6,1,0,1,'0.0000'),
(103,'PRF00270','','',100,1,14,1,'78.0000',1,'2012-03-14','80.00',2,'0.00','0.00','0.00',3,1,now(), now(),4,1,0,1,'0.0000'),
(104,'PRF00271','','',1000,1,13,1,'49.0000',1,'2012-03-14','0.00',5,'0.00','0.00','0.00',3,1,now(), now(),19,1,1,1,'0.0000'),
(105,'PRF00273','','',100,2,14,1,'55.0000',0,'2012-03-14','0.00',5,'0.00','0.00','0.00',3,1,now(), now(),18,1,1,1,'0.0000'),
(106,'PRF00274','','',185,1,14,1,'70.0000',1,'2012-03-14','80.00',5,'0.00','0.00','0.00',3,1,now(), now(),8,1,1,1,'0.0000'),
(107,'PRF00274','','',0,2,15,1,'66.0000',1,'2012-03-14','7.00',6,'0.00','0.00','0.00',3,1,now(), now(),5,1,1,1,'0.0000'),
(108,'PRF00268','','',420,1,15,1,'125.0000',1,'2012-03-14','2.00',6,'0.00','0.00','0.00',3,1,now(), now(),6,1,1,2,'0.0000'),
(109,'PRF00279','','',1,1,15,1,'84.0000',1,'2012-03-14','50.00',6,'3.00','2.00','10.00',1,1,now(), now(),6,1,1,1,'0.0000'),
(110,'PRF00278','','',1000,1,20,1,'90.0000',1,'2012-03-14','0.00',6,'0.00','0.00','0.00',3,1,now(), now(),21,1,0,1,'0.0000');

--
-- Dumping data for table `products_featured`
--

INSERT INTO `ac_products_featured` 
VALUES 
(50),
(51),
(52),
(53),
(54),
(55),
(56),
(57);

--
-- Dumping data for table `products_related`
--

INSERT INTO `ac_products_related` 
VALUES 
(71,101),
(100,108),
(101,71),
(108,100);

--
-- Dumping data for table `products_to_categories`
--

INSERT INTO `ac_products_to_categories` 
VALUES 
(50,40),
(51,40),
(52,40),
(53,36),
(53,40),
(54,36),
(54,39),
(55,41),
(56,36),
(56,39),
(57,36),
(57,38),
(58,36),
(58,38),
(59,36),
(59,41),
(60,42),
(61,37),
(62,49),
(62,51),
(63,51),
(64,49),
(64,50),
(65,43),
(65,47),
(66,43),
(66,46),
(67,43),
(67,44),
(68,43),
(68,48),
(69,52),
(69,54),
(69,64),
(70,52),
(70,53),
(71,52),
(71,54),
(72,54),
(73,54),
(74,52),
(74,53),
(75,58),
(75,63),
(76,58),
(76,60),
(77,58),
(77,60),
(77,63),
(78,58),
(78,59),
(78,62),
(79,50),
(79,62),
(80,49),
(80,51),
(81,51),
(82,51),
(82,59),
(83,51),
(84,49),
(84,50),
(85,49),
(85,50),
(86,51),
(86,59),
(87,51),
(87,59),
(88,50),
(89,49),
(89,50),
(90,50),
(90,59),
(91,46),
(92,46),
(93,43),
(93,46),
(94,45),
(95,45),
(95,60),
(96,47),
(96,60),
(97,47),
(98,61),
(99,42),
(100,36),
(100,41),
(101,54),
(102,49),
(102,50),
(104,49),
(104,50),
(105,50),
(106,49),
(106,50),
(107,45),
(107,63),
(108,37),
(108,39),
(108,41),
(108,45),
(109,46),
(110,50);

--
-- Dumping data for table `products_to_stores`
--

INSERT INTO `ac_products_to_stores` 
VALUES 
(50,0),
(51,0),
(52,0),
(53,0),
(54,0),
(55,0),
(56,0),
(57,0),
(58,0),
(59,0),
(60,0),
(61,0),
(62,0),
(63,0),
(64,0),
(65,0),
(66,0),
(67,0),
(68,0),
(69,0),
(70,0),
(71,0),
(72,0),
(73,0),
(74,0),
(75,0),
(76,0),
(77,0),
(78,0),
(79,0),
(80,0),
(81,0),
(82,0),
(83,0),
(84,0),
(85,0),
(86,0),
(87,0),
(88,0),
(89,0),
(90,0),
(91,0),
(92,0),
(93,0),
(94,0),
(95,0),
(96,0),
(97,0),
(98,0),
(99,0),
(100,0),
(101,0),
(102,0),
(103,0),
(104,0),
(105,0),
(106,0),
(107,0),
(108,0),
(109,0),
(110,0);

--
-- Dumping data for table `resource_descriptions`
--

INSERT INTO `ac_resource_descriptions` 
VALUES 
(100010,1,'az_demo_product_15_1.jpg','','','18/6a/a.jpg','',now(), now()),
(100012,1,'az_demo_product_07.jpg','','','18/6a/c.jpg','',now(), now()),
(100011,1,'az_demo_product_15.jpg','','','18/6a/b.jpg','',now(), now()),
(100007,1,'az_demo_product_14_2.jpg','','','18/6a/7.jpg','',now(), now()),
(100008,1,'az_demo_product_14.jpg','','','18/6a/8.jpg','',now(), now()),
(100009,1,'az_demo_product_14_1.jpg','','','18/6a/9.jpg','',now(), now()),
(100013,1,'az_demo_product_18.jpg','','','18/6a/d.jpg','',now(), now()),
(100014,1,'az_demo_product_30.jpg','','','18/6a/e.jpg','',now(), now()),
(100015,1,'az_demo_product_30_2.jpg','','','18/6a/f.jpg','',now(), now()),
(100016,1,'az_demo_product_30_1.jpg','','','18/6b/0.jpg','',now(), now()),
(100017,1,'az_demo_product_30_3.jpg','','','18/6b/1.jpg','',now(), now()),
(100018,1,'az_demo_product_34.jpg','','','18/6b/2.jpg','',now(), now()),
(100019,1,'az_demo_product_34_2.jpg','','','18/6b/3.jpg','',now(), now()),
(100020,1,'az_demo_product_34_1.jpg','','','18/6b/4.jpg','',now(), now()),
(100021,1,'az_demo_product_32.jpg','','','18/6b/5.jpg','',now(), now()),
(100022,1,'az_demo_product_32.png','','','18/6b/6.png','',now(), now()),
(100023,1,'az_demo_product_33.jpg','','','18/6b/7.jpg','',now(), now()),
(100024,1,'az_demo_product_32_1.jpg','','','18/6b/8.jpg','',now(), now()),
(100025,1,'az_demo_product_31.jpg','','','18/6b/9.jpg','',now(), now()),
(100026,1,'az_demo_product_02.jpg','','','18/6b/a.jpg','',now(), now()),
(100027,1,'az_demo_product_02_2.jpg','','','18/6b/b.jpg','',now(), now()),
(100028,1,'az_demo_product_02_1.jpg','','','18/6b/c.jpg','',now(), now()),
(100029,1,'az_demo_product_02_3.jpg','','','18/6b/d.jpg','',now(), now()),
(100030,1,'az_demo_product_42.jpg','','','18/6b/e.jpg','',now(), now()),
(100031,1,'az_demo_product_22.jpg','','','18/6b/f.jpg','',now(), now()),
(100032,1,'az_demo_product_11_1.jpg','','','18/6c/0.jpg','',now(), now()),
(100033,1,'az_demo_product_11_2.jpg','','','18/6c/1.jpg','',now(), now()),
(100034,1,'az_demo_product_11.jpg','','','18/6c/2.jpg','',now(), now()),
(100035,1,'az_demo_product_43.jpg','','','18/6c/3.jpg','',now(), now()),
(100036,1,'az_demo_product_24.jpg','','','18/6c/4.jpg','',now(), now()),
(100037,1,'az_demo_product_06_6.jpg','','','18/6c/5.jpg','',now(), now()),
(100038,1,'az_demo_product_06_2.jpg','','','18/6c/6.jpg','',now(), now()),
(100039,1,'az_demo_product_06_1.jpg','','','18/6c/7.jpg','',now(), now()),
(100040,1,'az_demo_product_06.jpg','','','18/6c/8.jpg','',now(), now()),
(100041,1,'az_demo_product_06_4.jpg','','','18/6c/9.jpg','',now(), now()),
(100042,1,'az_demo_product_06_3.jpg','','','18/6c/a.jpg','',now(), now()),
(100043,1,'az_demo_product_06_5.jpg','','','18/6c/b.jpg','',now(), now()),
(100044,1,'az_demo_product_25_1.jpg','','','18/6c/c.jpg','',now(), now()),
(100045,1,'az_demo_product_25_2.jpg','','','18/6c/d.jpg','',now(), now()),
(100046,1,'az_demo_product_25.jpg','','','18/6c/e.jpg','',now(), now()),
(100047,1,'az_demo_product_20.jpg','','','18/6c/f.jpg','',now(), now()),
(100048,1,'az_demo_product_36.jpg','','','18/6d/0.jpg','',now(), now()),
(100049,1,'az_demo_product_47.png','','','18/6d/1.png','',now(), now()),
(100050,1,'az_demo_product_46.jpg','','','18/6d/2.jpg','',now(), now()),
(100051,1,'az_demo_product_46.png','','','18/6d/3.png','',now(), now()),
(100052,1,'az_demo_product_17.jpg','','','18/6d/4.jpg','',now(), now()),
(100053,1,'az_demo_product_49_1.png','','','18/6d/5.png','',now(), now()),
(100054,1,'az_demo_product_35_1.jpg','','','18/6d/6.jpg','',now(), now()),
(100055,1,'az_demo_product_35_2.jpg','','','18/6d/7.jpg','',now(), now()),
(100056,1,'az_demo_product_35.jpg','','','18/6d/8.jpg','',now(), now()),
(100057,1,'az_demo_product_23.jpg','','','18/6d/9.jpg','',now(), now()),
(100058,1,'az_demo_product_41.jpg','','','18/6d/a.jpg','',now(), now()),
(100059,1,'az_demo_product_09_4.jpg','','','18/6d/b.jpg','',now(), now()),
(100060,1,'az_demo_product_09_1.jpg','','','18/6d/c.jpg','',now(), now()),
(100061,1,'az_demo_product_09.jpg','','','18/6d/d.jpg','',now(), now()),
(100062,1,'az_demo_product_09_3.jpg','','','18/6d/e.jpg','',now(), now()),
(100063,1,'az_demo_product_09_2.jpg','','','18/6d/f.jpg','',now(), now()),
(100064,1,'az_demo_product_37.jpg','','','18/6e/0.jpg','',now(), now()),
(100065,1,'az_demo_product_26_2.jpg','','','18/6e/1.jpg','',now(), now()),
(100066,1,'az_demo_product_26_3.jpg','','','18/6e/2.jpg','',now(), now()),
(100067,1,'az_demo_product_26.jpg','','','18/6e/3.jpg','',now(), now()),
(100068,1,'az_demo_product_26_1.jpg','','','18/6e/4.jpg','',now(), now()),
(100069,1,'az_demo_product_27_1.jpg','','','18/6e/5.jpg','',now(), now()),
(100070,1,'az_demo_product_27.jpg','','','18/6e/6.jpg','',now(), now()),
(100071,1,'az_demo_product_10.jpg','','','18/6e/7.jpg','',now(), now()),
(100072,1,'az_demo_product_10_1.jpg','','','18/6e/8.jpg','',now(), now()),
(100073,1,'az_demo_product_10_2.jpg','','','18/6e/9.jpg','',now(), now()),
(100074,1,'az_demo_product_10_3.jpg','','','18/6e/a.jpg','',now(), now()),
(100075,1,'az_demo_product_44.jpg','','','18/6e/b.jpg','',now(), now()),
(100076,1,'az_demo_product_40_1.jpg','','','18/6e/c.jpg','',now(), now()),
(100077,1,'az_demo_product_40.jpg','','','18/6e/d.jpg','',now(), now()),
(100078,1,'az_demo_product_40_2.jpg','','','18/6e/e.jpg','',now(), now()),
(100079,1,'az_demo_product_21.jpg','','','18/6e/f.jpg','',now(), now()),
(100080,1,'az_demo_product_13_2.jpg','','','18/6f/0.jpg','',now(), now()),
(100081,1,'az_demo_product_13_1.jpg','','','18/6f/1.jpg','',now(), now()),
(100082,1,'az_demo_product_19.jpg','','','18/6f/2.jpg','',now(), now()),
(100083,1,'az_demo_product_39.jpg','','','18/6f/3.jpg','',now(), now()),
(100084,1,'az_demo_product_39_3.jpg','','','18/6f/4.jpg','',now(), now()),
(100085,1,'az_demo_product_39_2.jpg','','','18/6f/5.jpg','',now(), now()),
(100086,1,'az_demo_product_39_1.jpg','','','18/6f/6.jpg','',now(), now()),
(100087,1,'az_demo_product_45.png','','','18/6f/7.png','',now(), now()),
(100088,1,'az_demo_product_48.png','','','18/6f/8.png','',now(), now()),
(100089,1,'az_demo_product_01.jpg','','','18/6f/9.jpg','',now(), now()),
(100090,1,'az_demo_product_50.jpg','','','18/6f/a.jpg','',now(), now()),
(100091,1,'az_demo_product_16_1.jpg','','','18/6f/b.jpg','',now(), now()),
(100092,1,'az_demo_product_16.jpg','','','18/6f/c.jpg','',now(), now()),
(100093,1,'az_demo_product_16_2.jpg','','','18/6f/d.jpg','',now(), now()),
(100094,1,'az_demo_product_03.jpg','','','18/6f/e.jpg','',now(), now()),
(100095,1,'az_demo_product_03_1.jpg','','','18/6f/f.jpg','',now(), now()),
(100096,1,'az_demo_product_03_2.jpg','','','18/70/0.jpg','',now(), now()),
(100097,1,'az_demo_product_08.jpg','','','18/70/1.jpg','',now(), now()),
(100098,1,'az_demo_product_08_2.jpg','','','18/70/2.jpg','',now(), now()),
(100099,1,'az_demo_product_08_3.jpg','','','18/70/3.jpg','',now(), now()),
(100100,1,'az_demo_product_08_1.jpg','','','18/70/4.jpg','',now(), now()),
(100101,1,'az_demo_product_05.jpg','','','18/70/5.jpg','',now(), now()),
(100102,1,'az_demo_product_29_2.jpg','','','18/70/6.jpg','',now(), now()),
(100103,1,'az_demo_product_29.jpg','','','18/70/7.jpg','',now(), now()),
(100104,1,'az_demo_product_29_1.jpg','','','18/70/8.jpg','',now(), now()),
(100105,1,'az_demo_product_29.jpg','','','18/70/9.jpg','',now(), now()),
(100106,1,'az_demo_product_29_2.jpg','','','18/70/a.jpg','',now(), now()),
(100107,1,'az_demo_product_29_1.jpg','','','18/70/b.jpg','',now(), now()),
(100108,1,'az_demo_product_28_1.jpg','','','18/70/c.jpg','',now(), now()),
(100109,1,'az_demo_product_28.jpg','','','18/70/d.jpg','',now(), now()),
(100110,1,'az_demo_product_28_2.jpg','','','18/70/e.jpg','',now(), now()),
(100111,1,'az_demo_product_38.jpg','','','18/70/f.jpg','',now(), now()),
(100112,1,'az_demo_product_12.jpg','','','18/71/0.jpg','',now(), now()),
(100113,1,'az_demo_product_12.png','','','18/71/1.png','',now(), now()),
(100114,1,'mf_sephora_ba_logo_black.jpg','','','18/71/2.jpg','',now(), now()),
(100115,1,'mf_Bvlgari.jpg','','','18/71/3.jpg','',now(), now()),
(100116,1,'mf_calvin_klein.jpg','','','18/71/4.jpg','',now(), now()),
(100117,1,'mf_benefit_logo_black.jpg','','','18/71/5.jpg','',now(), now()),
(100118,1,'mf_mac_logo.jpg','','','18/71/6.jpg','',now(), now()),
(100119,1,'mf_lancome_logo.gif','','','18/71/7.gif','',now(), now()),
(100120,1,'mf_pantene_logo.jpg','','','18/71/8.jpg','',now(), now()),
(100121,1,'mf_dove_logo.jpg','','','18/71/9.jpg','',now(), now()),
(100122,1,'mf_armani_logo.gif','','','18/71/a.gif','',now(), now()),
(100123,1,'demo_product_23.jpg','','','18/71/b.jpg','',now(), now()),
(100124,1,'demo_product_04.jpg','','','18/71/c.jpg','',now(), now()),
(100125,1,'demo_product_15.jpg','','','18/71/d.jpg','',now(), now()),
(100126,1,'demo_product_14_2.jpg','','','18/71/e.jpg','',now(), now()),
(100127,1,'demo_product_31.jpg','','','18/71/f.jpg','',now(), now()),
(100128,1,'demo_product_34.jpg','','','18/72/0.jpg','',now(), now()),
(100129,1,'demo_product_30_2.jpg','','','18/72/1.jpg','',now(), now()),
(100130,1,'demo_product_24.jpg','','','18/72/2.jpg','',now(), now()),
(100131,1,'demo_product_23.jpg','','','18/72/3.jpg','',now(), now()),
(100132,1,'demo_product_05.jpg','','','18/72/4.jpg','',now(), now()),
(100133,1,'demo_product_07.jpg','','','18/72/5.jpg','',now(), now()),
(100134,1,'demo_product_08_3.jpg','','','18/72/6.jpg','',now(), now()),
(100135,1,'demo_product_10_2.jpg','','','18/72/7.jpg','',now(), now()),
(100136,1,'demo_product_47.png','','','18/72/8.png','',now(), now()),
(100137,1,'demo_product_11_2.jpg','','','18/72/9.jpg','',now(), now()),
(100138,1,'demo_product_40_2.jpg','','','18/72/a.jpg','',now(), now()),
(100139,1,'demo_product_44.jpg','','','18/72/b.jpg','',now(), now()),
(100140,1,'demo_product_29.jpg','','','18/72/c.jpg','',now(), now()),
(100141,1,'demo_product_27.jpg','','','18/72/d.jpg','',now(), now()),
(100142,1,'demo_product_42.jpg','','','18/72/e.jpg','',now(), now()),
(100143,1,'demo_product_46.jpg','','','18/72/f.jpg','',now(), now()),
(100144,1,'demo_product_18.jpg','','','18/73/0.jpg','',now(), now()),
(100145,1,'demo_product_37.jpg','','','18/73/1.jpg','',now(), now()),
(100146,1,'demo_product_49_1.png','','','18/73/2.png','',now(), now()),
(100147,1,'store_logo.png','','','18/73/3.png','',now(), now()),
(100148,1,'favicon.ico','','','18/73/4.ico','',now(), now()),
(100150,1,'az_demo_product_51.png','','','18/73/6.png','',now(), now()),
(100153,1,'demo_mf_gucci.jpg','','','18/73/9.jpg','',now(), now()),
(100154,1,'az_demo_product_52_1.jpg','','','18/73/a.jpg','',now(), now()),
(100155,1,'az_demo_product_52_2.png','','','18/73/b.png','',now(), now()),
(100156,1,'az_demo_product_52_3.png','','','18/73/c.png','',now(), now()),
(100157,1,'az_demo_product_53_3.jpg','','','18/73/d.jpg','',now(), now()),
(100159,1,'az_demo_product_53_2.png','','','18/73/f.png','',now(), now()),
(100160,1,'az_demo_product_54_1.jpg','','','18/74/0.jpg','',now(), now()),
(100162,1,'az_demo_product_55_1.jpg','','','18/74/2.jpg','',now(), now()),
(100163,1,'az_demo_product_56_3.jpg','','','18/74/3.jpg','',now(), now()),
(100164,1,'az_demo_product_56_2.jpg','','','18/74/4.jpg','',now(), now()),
(100165,1,'az_demo_product_56_1.jpg','','','18/74/5.jpg','',now(), now()),
(100166,1,'az_demo_product_57_1.jpg','','','18/74/6.jpg','',now(), now()),
(100167,1,'az_demo_product_57_2.jpg','','','18/74/7.jpg','',now(), now()),
(100168,1,'az_demo_product_58_1.jpg','','','18/74/8.jpg','',now(), now()),
(100169,1,'az_demo_product_58_3.jpg','','','18/74/9.jpg','',now(), now()),
(100170,1,'az_demo_product_58_4.jpg','','','18/74/a.jpg','',now(), now()),
(100171,1,'az_demo_product_58_2.jpg','','','18/74/b.jpg','',now(), now()),
(100172,1,'Visionnaire.zip','','','18/74/c.zip','',now(), now()),
(100173,1,'az_demo_product_59_1.jpg','','','18/74/d.jpg','',now(), now()),
(100174,1,'az_demo_product_60_1.jpg','','','18/74/e.jpg','',now(), now()),
(100175,1,'az_demo_product_60_2.jpg','','','18/74/f.jpg','',now(), now()),
(100176,1,'az_demo_product_60_5.jpg','','','18/75/0.jpg','',now(), now()),
(100178,1,'abantecart video','','','','<object width=\"640\" height=\"360\"><param name=\"movie\" value=\"http://www.youtube.com/v/IQ5SLJUWbdA\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/IQ5SLJUWbdA\" type=\"application/x-shockwave-flash\" width=\"640\" height=\"360\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed></object>',now(), now()),
(100188,1,'smbanner.jpg','','','18/75/c.jpg','',now(), now()),
(100190,1,'AM_mc_vs_dc_ae_319x110.jpg','PayPal Credit Cards','PayPal logo with supported Credit Cards','18/75/e.jpg','',now(), now()),
(100191,1,'AM_SbyPP_mc_vs_dc_ae_319x110.jpg','PayPal Secure Payments','Secure Payments by PayPal logo','18/75/f.jpg','',now(), now()),
(100192,1,'bdg_payments_by_pp_2line_165x56.png','Payments by PayPal','Payments by PayPal Logo','18/76/0.png','',now(), now()),
(100193,1,'pp_cc_mark_76x48.jpg','PayPal Icon','PayPal Small Icon','18/76/1.jpg','',now(), now()),
(100194,1,'banner_fallback.jpg','Fall back banner for small screen resolutions','Fall back banner for small screen resolutions','18/76/2.jpg','',now(), now());

--
-- Dumping data for table `resource_library`
--



INSERT INTO `ac_resource_library` 
VALUES 
(100010,1,now(), now()),
(100012,1,now(), now()),
(100011,1,now(), now()),
(100007,1,now(), now()),
(100008,1,now(), now()),
(100009,1,now(), now()),
(100013,1,now(), now()),
(100014,1,now(), now()),
(100015,1,now(), now()),
(100016,1,now(), now()),
(100017,1,now(), now()),
(100018,1,now(), now()),
(100019,1,now(), now()),
(100020,1,now(), now()),
(100021,1,now(), now()),
(100022,1,now(), now()),
(100023,1,now(), now()),
(100024,1,now(), now()),
(100025,1,now(), now()),
(100026,1,now(), now()),
(100027,1,now(), now()),
(100028,1,now(), now()),
(100029,1,now(), now()),
(100030,1,now(), now()),
(100031,1,now(), now()),
(100032,1,now(), now()),
(100033,1,now(), now()),
(100034,1,now(), now()),
(100035,1,now(), now()),
(100036,1,now(), now()),
(100037,1,now(), now()),
(100038,1,now(), now()),
(100039,1,now(), now()),
(100040,1,now(), now()),
(100041,1,now(), now()),
(100042,1,now(), now()),
(100043,1,now(), now()),
(100044,1,now(), now()),
(100045,1,now(), now()),
(100046,1,now(), now()),
(100047,1,now(), now()),
(100048,1,now(), now()),
(100049,1,now(), now()),
(100050,1,now(), now()),
(100051,1,now(), now()),
(100052,1,now(), now()),
(100053,1,now(), now()),
(100054,1,now(), now()),
(100055,1,now(), now()),
(100056,1,now(), now()),
(100057,1,now(), now()),
(100058,1,now(), now()),
(100059,1,now(), now()),
(100060,1,now(), now()),
(100061,1,now(), now()),
(100062,1,now(), now()),
(100063,1,now(), now()),
(100064,1,now(), now()),
(100065,1,now(), now()),
(100066,1,now(), now()),
(100067,1,now(), now()),
(100068,1,now(), now()),
(100069,1,now(), now()),
(100070,1,now(), now()),
(100071,1,now(), now()),
(100072,1,now(), now()),
(100073,1,now(), now()),
(100074,1,now(), now()),
(100075,1,now(), now()),
(100076,1,now(), now()),
(100077,1,now(), now()),
(100078,1,now(), now()),
(100079,1,now(), now()),
(100080,1,now(), now()),
(100081,1,now(), now()),
(100082,1,now(), now()),
(100083,1,now(), now()),
(100084,1,now(), now()),
(100085,1,now(), now()),
(100086,1,now(), now()),
(100087,1,now(), now()),
(100088,1,now(), now()),
(100089,1,now(), now()),
(100090,1,now(), now()),
(100091,1,now(), now()),
(100092,1,now(), now()),
(100093,1,now(), now()),
(100094,1,now(), now()),
(100095,1,now(), now()),
(100096,1,now(), now()),
(100097,1,now(), now()),
(100098,1,now(), now()),
(100099,1,now(), now()),
(100100,1,now(), now()),
(100101,1,now(), now()),
(100102,1,now(), now()),
(100103,1,now(), now()),
(100104,1,now(), now()),
(100105,1,now(), now()),
(100106,1,now(), now()),
(100107,1,now(), now()),
(100108,1,now(), now()),
(100109,1,now(), now()),
(100110,1,now(), now()),
(100111,1,now(), now()),
(100112,1,now(), now()),
(100113,1,now(), now()),
(100114,1,now(), now()),
(100115,1,now(), now()),
(100116,1,now(), now()),
(100117,1,now(), now()),
(100118,1,now(), now()),
(100119,1,now(), now()),
(100120,1,now(), now()),
(100121,1,now(), now()),
(100122,1,now(), now()),
(100123,1,now(), now()),
(100124,1,now(), now()),
(100125,1,now(), now()),
(100126,1,now(), now()),
(100127,1,now(), now()),
(100128,1,now(), now()),
(100129,1,now(), now()),
(100130,1,now(), now()),
(100131,1,now(), now()),
(100132,1,now(), now()),
(100133,1,now(), now()),
(100134,1,now(), now()),
(100135,1,now(), now()),
(100136,1,now(), now()),
(100137,1,now(), now()),
(100138,1,now(), now()),
(100139,1,now(), now()),
(100140,1,now(), now()),
(100141,1,now(), now()),
(100142,1,now(), now()),
(100143,1,now(), now()),
(100144,1,now(), now()),
(100145,1,now(), now()),
(100146,1,now(), now()),
(100147,1,now(), now()),
(100148,1,now(), now()),
(100150,1,now(), now()),
(100178,3,now(), now()),
(100153,1,now(), now()),
(100154,1,now(), now()),
(100155,1,now(), now()),
(100156,1,now(), now()),
(100157,1,now(), now()),
(100159,1,now(), now()),
(100160,1,now(), now()),
(100162,1,now(), now()),
(100163,1,now(), now()),
(100164,1,now(), now()),
(100165,1,now(), now()),
(100166,1,now(), now()),
(100167,1,now(), now()),
(100168,1,now(), now()),
(100169,1,now(), now()),
(100170,1,now(), now()),
(100171,1,now(), now()),
(100172,5,now(), now()),
(100173,1,now(), now()),
(100174,1,now(), now()),
(100175,1,now(), now()),
(100176,1,now(), now()),
(100188,1,now(), now()),
(100190,1,now(), now()),
(100191,1,now(), now()),
(100192,1,now(), now()),
(100193,1,now(), now()),
(100194,1,now(), now())
;

--
-- Dumping data for table `resource_map`
--

INSERT INTO `ac_resource_map` 
VALUES 
(100012,'products',58,0,0,now(), now()),
(100014,'products',80,0,0,now(), now()),
(100013,'products',68,0,0,now(), now()),
(100015,'products',80,0,0,now(), now()),
(100011,'products',65,0,0,now(), now()),
(100010,'products',65,0,0,now(), now()),
(100007,'products',64,0,0,now(), now()),
(100008,'products',64,0,0,now(), now()),
(100009,'products',64,0,0,now(), now()),
(100016,'products',80,0,0,now(), now()),
(100017,'products',80,0,0,now(), now()),
(100018,'products',84,0,0,now(), now()),
(100019,'products',84,0,0,now(), now()),
(100020,'products',84,0,0,now(), now()),
(100021,'products',83,0,0,now(), now()),
(100022,'products',82,0,0,now(), now()),
(100023,'products',83,0,0,now(), now()),
(100024,'products',83,0,0,now(), now()),
(100025,'products',81,0,0,now(), now()),
(100026,'products',51,0,0,now(), now()),
(100027,'products',51,0,0,now(), now()),
(100028,'products',51,0,0,now(), now()),
(100029,'products',52,0,0,now(), now()),
(100030,'products',92,0,0,now(), now()),
(100031,'products',72,0,0,now(), now()),
(100032,'products',61,0,0,now(), now()),
(100033,'products',61,0,0,now(), now()),
(100034,'products',61,0,0,now(), now()),
(100035,'products',93,0,0,now(), now()),
(100036,'products',74,0,0,now(), now()),
(100037,'products',57,0,0,now(), now()),
(100038,'products',57,0,0,now(), now()),
(100039,'products',57,0,0,now(), now()),
(100040,'products',57,0,0,now(), now()),
(100041,'products',57,0,0,now(), now()),
(100042,'products',57,0,0,now(), now()),
(100043,'products',57,0,0,now(), now()),
(100044,'products',75,0,0,now(), now()),
(100045,'products',75,0,0,now(), now()),
(100046,'products',75,0,0,now(), now()),
(100047,'products',70,0,0,now(), now()),
(100048,'products',86,0,0,now(), now()),
(100049,'products',97,0,0,now(), now()),
(100050,'products',96,0,0,now(), now()),
(100051,'products',96,0,0,now(), now()),
(100052,'products',67,0,0,now(), now()),
(100053,'products',99,0,0,now(), now()),
(100054,'products',85,0,0,now(), now()),
(100055,'products',85,0,0,now(), now()),
(100056,'products',85,0,0,now(), now()),
(100057,'products',73,0,0,now(), now()),
(100058,'products',91,0,0,now(), now()),
(100059,'products',55,0,0,now(), now()),
(100060,'products',55,0,0,now(), now()),
(100061,'products',55,0,0,now(), now()),
(100062,'products',55,0,0,now(), now()),
(100063,'products',55,0,0,now(), now()),
(100064,'products',87,0,0,now(), now()),
(100065,'products',77,0,0,now(), now()),
(100066,'products',77,0,0,now(), now()),
(100067,'products',77,0,0,now(), now()),
(100068,'products',77,0,0,now(), now()),
(100069,'products',76,0,0,now(), now()),
(100070,'products',76,0,0,now(), now()),
(100071,'products',60,0,0,now(), now()),
(100072,'products',60,0,0,now(), now()),
(100073,'products',60,0,0,now(), now()),
(100074,'products',60,0,0,now(), now()),
(100075,'products',94,0,0,now(), now()),
(100076,'products',90,0,0,now(), now()),
(100077,'products',90,0,0,now(), now()),
(100078,'products',90,0,0,now(), now()),
(100079,'products',71,0,0,now(), now()),
(100080,'products',63,0,0,now(), now()),
(100081,'products',63,0,0,now(), now()),
(100082,'products',69,0,0,now(), now()),
(100083,'products',89,0,0,now(), now()),
(100084,'products',89,0,0,now(), now()),
(100085,'products',89,0,0,now(), now()),
(100086,'products',89,0,0,now(), now()),
(100087,'products',98,0,0,now(), now()),
(100088,'products',95,0,0,now(), now()),
(100089,'products',50,0,0,now(), now()),
(100090,'products',100,0,0,now(), now()),
(100091,'products',66,0,0,now(), now()),
(100092,'products',66,0,0,now(), now()),
(100093,'products',66,0,0,now(), now()),
(100094,'products',53,0,0,now(), now()),
(100095,'products',53,0,0,now(), now()),
(100096,'products',53,0,0,now(), now()),
(100097,'products',59,0,0,now(), now()),
(100098,'products',59,0,0,now(), now()),
(100099,'products',59,0,0,now(), now()),
(100100,'products',59,0,0,now(), now()),
(100101,'products',56,0,0,now(), now()),
(100109,'products',78,0,0,now(), now()),
(100108,'products',78,0,0,now(), now()),
(100105,'products',79,0,0,now(), now()),
(100106,'products',79,0,0,now(), now()),
(100107,'products',79,0,0,now(), now()),
(100110,'products',78,0,0,now(), now()),
(100111,'products',88,0,0,now(), now()),
(100112,'products',62,0,0,now(), now()),
(100113,'products',62,0,0,now(), now()),
(100114,'manufacturers',16,0,0,now(), now()),
(100115,'manufacturers',14,0,0,now(), now()),
(100116,'manufacturers',13,0,0,now(), now()),
(100117,'manufacturers',12,0,0,now(), now()),
(100118,'manufacturers',11,0,0,now(), now()),
(100119,'manufacturers',15,0,0,now(), now()),
(100120,'manufacturers',17,0,0,now(), now()),
(100121,'manufacturers',18,0,0,now(), now()),
(100122,'manufacturers',19,0,0,now(), now()),
(100123,'categories',52,0,0,now(), now()),
(100124,'categories',36,0,0,now(), now()),
(100125,'categories',43,0,0,now(), now()),
(100126,'categories',49,0,0,now(), now()),
(100127,'categories',58,0,0,now(), now()),
(100128,'categories',50,0,0,now(), now()),
(100129,'categories',51,0,0,now(), now()),
(100130,'categories',53,0,0,now(), now()),
(100131,'categories',54,0,0,now(), now()),
(100132,'categories',38,0,0,now(), now()),
(100133,'categories',40,0,0,now(), now()),
(100134,'categories',41,0,0,now(), now()),
(100135,'categories',42,0,0,now(), now()),
(100136,'categories',39,0,0,now(), now()),
(100137,'categories',37,0,0,now(), now()),
(100138,'categories',59,0,0,now(), now()),
(100139,'categories',60,0,0,now(), now()),
(100140,'categories',61,0,0,now(), now()),
(100141,'categories',63,0,0,now(), now()),
(100142,'categories',46,0,0,now(), now()),
(100143,'categories',47,0,0,now(), now()),
(100144,'categories',44,0,0,now(), now()),
(100145,'categories',45,0,0,now(), now()),
(100146,'categories',48,0,0,now(), now()),
(100150,'products',101,0,0,now(), now()),
(100178,'products',101,0,0,now(), now()),
(100153,'manufacturers',20,0,0,now(), now()),
(100154,'products',102,0,0,now(), now()),
(100155,'products',102,0,0,now(), now()),
(100156,'products',102,0,0,now(), now()),
(100157,'products',103,0,2,now(), now()),
(100159,'products',103,0,3,now(), now()),
(100160,'products',104,0,0,now(), now()),
(100162,'products',105,0,0,now(), now()),
(100163,'products',106,0,2,now(), now()),
(100164,'products',106,0,2,now(), now()),
(100165,'products',106,0,1,now(), now()),
(100166,'products',107,0,0,now(), now()),
(100167,'products',107,0,0,now(), now()),
(100168,'products',108,0,0,now(), now()),
(100169,'products',108,0,0,now(), now()),
(100170,'products',108,0,0,now(), now()),
(100171,'products',108,0,0,now(), now()),
(100173,'products',109,0,0,now(), now()),
(100174,'products',110,0,0,now(), now()),
(100175,'products',110,0,0,now(), now()),
(100176,'products',110,0,0,now(), now()),
(100188,'banners',13,0,0,now(), now()),
(100188,'banners',14,0,0,now(), now()),
(100188,'banners',15,0,0,now(), now()),
(100188,'banners',16,0,0,now(), now()),
(100194,'banners',18,0,1,now(), now())
;

--
-- Dumping data for table `reviews`
--

INSERT INTO `ac_reviews` 
VALUES 
(63,77,6,'Bernard Horne','I thought since it was made for men that it was the perfect thing to go with the body wash. Its too small and doesn\'t lather up very well.',3,1,now(), now()),
(62,54,2,'Juliana Davis','I\'ve been wearing all Lancome mascara\'s and I\'m just get really upset when I\'m out. I\'ve tried other Brands, but it\'s always right back to the Lancome productss. The extend L\'EXTREME is by far the best!!! Really Long and Great! ',5,1,now(), now()),
(61,56,0,'Cassandra','Fortunately, I got this as a gift. BUT, I am willing to purchase this when I run out. This may be expensive but it is sooooo worth it! I love this concealer and I wouldn\'t even dare to use other brands. One more thing, the little tube lasts for a long time. I\'ve been using it everyday for 8 months now and I still have about 1/4 left.',5,1,now(), now()),
(64,76,7,'James','Finally a deodorant for men that doesn\'t smell like cheap cologne. I\'ve been using this for a couple of weeks now and I can\'t say anything bad about it. To me it just smells fresh',4,1,now(), now()),
(65,100,0,'Juli','Smooth Silk is an accurate name for this creamy lip liner. It is by far the best lip pencil I have ever encountered.',5,1,now(), now()),
(66,100,0,'Marianne','Nice pencil! This is a smooth, long lasting pencil, wonderful shades!',4,1,now(), now()),
(67,97,0,'Ann','Really reduces shades and swellings)',4,1,now(), now()),
(68,99,0,'Alice','This is much darker than the picture',2,1,now(), now()),
(69,57,0,'Jane','When it arrived, the blush had cracked and was crumbling all over, so I\'m only able to use half of it.',2,1,now(), now()),
(70,55,0,'Kristin K.','These lipsticks are moisturizing and have good pigmentation; however, their lasting power is not as advertised! ',4,1,now(), now()),
(71,55,0,'lara','This is quite simply good stuff. \nThe color payout is rich, the texture creamy and moist, and best of all no scent. No taste.',5,1,now(), now()),
(72,93,0,'L. D.','I totally love it.it smells heavenly . It smells so natural and my skin just loves it. ',5,1,now(), now()),
(73,93,0,'Walton','This creme is a bit heavy for my skin; however, as the day goes on it does not create an oily build-up. A little goes a long way, and I could see improvements in my skin tone within a week. Good product, will be purchasing again.',4,1,now(), now()),
(74,74,0,'Stefania V','it works very well moisturing and cleaning and unlike many other healthy shampoos it doesn\'t open the hair platelets too far and therefore doesn\'t feel so dry and sticky so I can get away without using a conditioner. Great value.',4,1,now(), now()),
(75,102,0,'Mary','This is more of a evening fragrance. I love it',4,1,now(), now()),
(76,110,0,'Lara','Product was very reasonably priced. It will make a nice gift.',5,1,now(), now());



--
-- Dumping data for table `url_aliases`
--


INSERT INTO `ac_url_aliases` (`query`,`keyword`,`language_id`)
VALUES
('category_id=36','makeup',1),
('content_id=1','about_us',1),
('product_id=101','pro-v_color_hair_solutions_color_preserve_shine_conditioner_with_pump',1),
('product_id=102','gucci_guilty',1),
('manufacturer_id=20','gucci',1),
('product_id=103','jasmin_noir_l\'essence_eau_de_parfum_spray',1),
('product_id=104','calvin_klein_obsession_for_women_edp_spray',1),
('product_id=105','bvlgari_aqua_eau_de_toilette_spray',1),
('product_id=106','omnia_eau_de_toilette',1),
('product_id=107','lancome_slimissime_360_slimming_activating_concentrate_unisex_treatment',1),
('product_id=108','lancome_hypnose_doll_lashes_mascara_4-piece_gift_set',1),
('product_id=109','lancome_visionnaire_advanced_skin_corrector',1),
('product_id=110','flora_by_gucci_eau_fraiche',1);


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
	(17,1,'Main Banner 5','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 slide2_bot&quot; src=&quot;storefront/view/default/image/banner_image_5.png&quot; /&gt; &lt;span class=&quot;txt1&quot;&gt;Application and data security&lt;/span&gt; &lt;span class=&quot;txt2&quot;&gt;Secure Solution&lt;/span&gt; &lt;span class=&quot;txt3&quot;&gt;Very secure solution with up to date industry security practices and inline with PCI compliance. Customer information protection with data encryption&lt;/span&gt; &lt;span class=&quot;txt4&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;#&quot;&gt;Install Now!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now()),
	(16,1,'banner4','','',now(), now()),
	(15,1,'banner3','','',now(), now()),
	(14,1,'banner2','','',now(), now()),
	(13,1,'banner1','','',now(), now()),
	(11,1,'Main Banner 4','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 slide3_bot&quot; src=&quot;storefront/view/default/image/banner_image_4.png&quot; /&gt; &lt;span class=&quot;txt1 blue&quot;&gt;Stay in control&lt;/span&gt; &lt;span class=&quot;txt2 blue&quot;&gt;Easy updates&lt;/span&gt; &lt;span class=&quot;txt3 short&quot;&gt;Upgrade right from admin. Backward supportability in upgrades and automatic backups. Easy extension download with one step installation.&lt;/span&gt; &lt;span class=&quot;txt4 txt4up&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;#&quot;&gt;Get Yours!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now()),
	(10,1,'Main Banner 3','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 slide2_bot&quot; src=&quot;storefront/view/default/image/banner_image_3.png&quot; /&gt; &lt;span class=&quot;txt1&quot;&gt;Feature rich with smart UI&lt;/span&gt; &lt;span class=&quot;txt2&quot;&gt;Easy &amp;amp; fun to manage&lt;/span&gt; &lt;span class=&quot;txt3&quot;&gt;Feature reach shopping cart application right out of the box. Standard features allow to set up complete eCommerce site with all the tools needed to sell products online.&lt;/span&gt; &lt;span class=&quot;txt4&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;#&quot;&gt;Install Now!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now()),
	(9,1,'Main Banner 2','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 wp1_left slide2_bot&quot; src=&quot;storefront/view/default/image/banner_image_2.png&quot; /&gt; &lt;span class=&quot;txt1 blue txt_right2&quot;&gt;Highly flexible layout on any page&lt;/span&gt; &lt;span class=&quot;txt2 blue txt_right2&quot;&gt;SEO Friendly&lt;/span&gt; &lt;span class=&quot;txt2 blue txt_right2&quot;&gt;Fast Loading&lt;/span&gt; &lt;span class=&quot;txt4 txt_right2 txt4up&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;#&quot;&gt;Try Now!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now()),
	(8,1,'Main Banner 1','&lt;p&gt;\r\n	&lt;img alt=&quot;&quot; class=&quot;wp1_3 slide1_bot&quot; src=&quot;storefront/view/default/image/banner_image_1.png&quot; /&gt; &lt;span class=&quot;txt1&quot;&gt;HTML5 Responsive Storefront to look great on&lt;/span&gt; &lt;span class=&quot;txt2&quot;&gt;ALL Screen Sizes&lt;/span&gt; &lt;span class=&quot;txt3 short&quot;&gt;Natively responsive template implemented with bootstrap library and HTML5. Will look good on most mobile devices and tablets.&lt;/span&gt; &lt;span class=&quot;txt4 txt4up&quot;&gt;&lt;a class=&quot;btn btn-wht&quot; href=&quot;&quot;&gt;Try on your device!&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;\r\n','',now(), now());