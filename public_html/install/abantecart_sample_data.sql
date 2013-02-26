-- MySQL dump 10.11
--
-- Host: localhost    Database: abantecart_new_demo_data
-- ------------------------------------------------------
-- Server version	5.0.84

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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
-- Dumping data for table `block_descriptions`
--



INSERT INTO `ac_block_descriptions`
VALUES
(1,1,1,'0','0','home page static banner','home page banner','','&lt;div style=&quot;text-align: center;&quot;&gt;&lt;a href=&quot;index.php?rt=product/special&quot;&gt; &lt;img alt=&quot;banner&quot; src=&quot;storefront/view/default/image/banner1.jpg&quot; /&gt; &lt;/a&gt;&lt;/div&gt;','2012-03-14 13:21:21','2012-03-14 13:21:21'),
(2,2,1,'0','0','Video block','Video','','a:3:{s:18:\"listing_datasource\";s:5:\"media\";s:13:\"resource_type\";s:5:\"video\";s:5:\"limit\";s:1:\"1\";}','2012-03-14 14:49:33','2012-03-14 14:54:39'),
(3,3,1,'0','1','Custom Listing block','Popular','','a:2:{s:18:\"listing_datasource\";s:34:\"catalog_product_getPopularProducts\";s:5:\"limit\";s:2:\"12\";}','2012-03-15 10:56:23','2012-03-15 12:59:02'),
(4,3,9,'0','1','Popular','Popular','','','2012-03-15 13:00:37','2012-03-15 13:00:38'),
(5,2,9,'0','0','Video block','Video','','a:3:{s:18:\"listing_datasource\";s:5:\"media\";s:13:\"resource_type\";s:5:\"video\";s:5:\"limit\";s:0:\"\";}','2012-03-15 13:02:16','2012-03-15 13:02:41');


--
-- Dumping data for table `block_layouts`
--



INSERT INTO `ac_block_layouts` VALUES
(97,8,1,0,0,10,1,'2012-03-14 14:52:05','2012-03-14 15:14:17'),
(123,8,15,0,97,30,1,'2012-03-14 15:14:17','2012-03-14 15:14:17'),
(122,8,14,0,97,20,1,'2012-03-14 15:14:17','2012-03-14 15:14:17'),
(121,8,13,0,97,10,1,'2012-03-14 15:14:17','2012-03-14 15:14:17'),
(101,8,2,0,0,20,1,'2012-03-14 14:52:05','2012-03-14 15:14:17'),
(102,8,3,0,0,30,0,'2012-03-14 14:52:05','2012-03-14 15:14:17'),
(124,8,20,2,102,10,1,'2012-03-14 15:14:17','2012-03-14 15:14:17'),
(104,8,4,0,0,40,1,'2012-03-14 14:52:05','2012-03-14 15:14:17'),
(105,8,5,0,0,50,1,'2012-03-14 14:52:05','2012-03-14 15:14:17'),
(106,8,6,0,0,60,0,'2012-03-14 14:52:05','2012-03-14 15:14:17'),
(107,8,7,0,0,70,1,'2012-03-14 14:52:05','2012-03-14 15:14:17'),
(108,8,8,0,0,80,1,'2012-03-14 14:52:05','2012-03-14 15:14:17'),
(126,8,21,0,108,10,1,'2012-03-14 15:14:17','2012-03-14 15:14:17'),
(174,8,24,0,108,10,1,'2012-03-14 15:14:17','2012-03-14 15:14:17'),
(127,9,1,0,0,10,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(125,8,20,2,105,10,1,'2012-03-14 15:14:17','2012-03-14 15:14:17'),
(128,9,13,0,127,10,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(129,9,14,0,127,20,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(130,9,15,0,127,30,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(131,9,2,0,0,20,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(132,9,3,0,0,30,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(133,9,20,3,132,10,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(134,9,4,0,0,40,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(135,9,5,0,0,50,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(136,9,6,0,0,60,0,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(137,9,7,0,0,70,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(138,9,8,0,0,80,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(139,9,21,0,138,10,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(175,9,24,0,138,10,1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(140,10,1,0,0,10,1,'2012-03-15 13:03:42','2012-03-15 13:06:40'),
(160,10,15,0,140,30,1,'2012-03-15 13:06:40','2012-03-15 13:06:40'),
(159,10,14,0,140,20,1,'2012-03-15 13:06:40','2012-03-15 13:06:40'),
(158,10,13,0,140,10,1,'2012-03-15 13:06:40','2012-03-15 13:06:40'),
(144,10,2,0,0,20,1,'2012-03-15 13:03:42','2012-03-15 13:06:40'),
(145,10,3,0,0,30,0,'2012-03-15 13:03:42','2012-03-15 13:06:40'),
(146,10,4,0,0,40,1,'2012-03-15 13:03:42','2012-03-15 13:06:40'),
(147,10,5,0,0,50,1,'2012-03-15 13:03:42','2012-03-15 13:06:40'),
(148,10,6,0,0,60,0,'2012-03-15 13:03:42','2012-03-15 13:06:40'),
(149,10,7,0,0,70,1,'2012-03-15 13:03:42','2012-03-15 13:06:40'),
(151,10,8,0,0,80,1,'2012-03-15 13:03:42','2012-03-15 13:06:40'),
(161,10,21,0,151,10,1,'2012-03-15 13:06:40','2012-03-15 13:06:40'),
(173,10,24,0,151,10,1,'2012-03-15 13:06:40','2012-03-15 13:06:40');



--
-- Dumping data for table `categories`
--



INSERT INTO `ac_categories` 
VALUES 
(46,43,0,'2011-08-29 10:42:57','2011-08-29 10:42:57',1),
(47,43,0,'2011-08-29 10:43:54','2011-08-29 10:43:54',1),
(38,36,0,'2011-08-29 10:29:37','2011-08-29 10:29:37',1),
(40,36,0,'2011-08-29 10:30:25','2011-08-29 10:30:25',1),
(41,36,0,'2011-08-29 10:31:51','2011-08-29 10:31:51',1),
(42,36,0,'2011-08-29 10:32:22','2011-08-29 10:32:22',1),
(43,0,2,'2011-08-29 10:37:00','2011-08-31 06:29:53',1),
(44,43,0,'2011-08-29 10:37:38','2011-08-29 10:37:38',1),
(45,43,0,'2011-08-29 10:38:12','2011-08-29 10:38:12',1),
(39,36,0,'2011-08-29 10:30:02','2011-08-29 10:30:02',1),
(36,0,1,'2011-08-29 10:24:45','2011-08-31 06:29:25',1),
(37,36,0,'2011-08-29 10:28:48','2011-08-29 10:28:48',1),
(48,43,0,'2011-08-29 10:49:40','2011-08-29 10:51:26',1),
(49,0,3,'2011-08-29 10:53:49','2011-08-31 06:29:41',1),
(50,49,0,'2011-08-29 10:54:33','2011-08-29 10:54:33',1),
(51,49,0,'2011-08-29 10:55:04','2011-08-29 10:55:04',1),
(52,0,98,'2011-08-29 10:59:36','2011-08-31 06:29:41',1),
(53,52,0,'2011-08-29 11:00:53','2011-08-29 11:00:53',1),
(54,52,0,'2011-08-29 11:01:09','2011-08-29 11:01:09',1),
(58,0,4,'2011-08-29 11:06:18','2011-08-31 06:30:34',1),
(59,58,0,'2011-08-29 11:06:56','2011-08-29 11:06:56',1),
(60,58,0,'2011-08-29 11:07:25','2011-08-29 11:07:25',1),
(61,58,0,'2011-08-29 11:07:45','2011-08-29 11:07:45',1),
(62,58,0,'2011-08-29 11:08:01','2011-08-31 10:05:20',0),
(63,58,0,'2011-08-29 11:08:25','2011-08-29 11:08:25',1),
(64,0,99,'2011-08-29 11:10:20','2011-09-08 10:33:55',0);



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
(41,9,'Lips','','',''),
(42,1,'Nails','','',''),
(42,9,'Nails','','',''),
(36,9,'Makeup','Makeup','','&lt;p&gt;\r\n	All your makeup needs, from foundation to eye shadow in hundreds of different assortments and colors.&lt;/p&gt;\r\n'),
(40,9,'Cheeks','','',''),
(38,1,'Face','','',''),
(38,9,'Face','','',''),
(39,1,'Eyes','','',''),
(39,9,'Eyes','','',''),
(36,1,'Makeup','Makeup','','&lt;p&gt;\r\n	All your makeup needs, from foundation to eye shadow in hundreds of different assortments and colors.&lt;/p&gt;\r\n'),
(40,1,'Cheeks','','',''),
(37,9,'Value Sets','value sets makeup','',''),
(37,1,'Value Sets','value sets makeup','',''),
(43,9,'Skincare','','','&lt;p&gt;\r\n	Products from award-winning skin care brands&lt;/p&gt;\r\n'),
(44,1,'Sun','','',''),
(44,9,'Sun','','',''),
(45,1,'Gift Ideas &amp; Sets','','',''),
(45,9,'Gift Ideas &amp; Sets','','',''),
(46,1,'Face','','','&lt;p&gt;\r\n	Find face skin care solutions&lt;/p&gt;\r\n'),
(46,9,'Face','','','&lt;p&gt;\r\n	Find face skin care solutions&lt;/p&gt;\r\n'),
(47,1,'Eyes','','',''),
(47,9,'Eyes','','',''),
(48,1,'Hands &amp; Nails','','','&lt;p&gt;\r\n	Keep your hands looking fresh&lt;/p&gt;\r\n'),
(48,9,'Hands &amp; Nails','','',''),
(49,1,'Fragrance','','','&lt;p&gt;\r\n	Looking for a new scent? Check out our fragrance&lt;/p&gt;\r\n'),
(49,9,'Fragrance','','','&lt;p&gt;\r\n	Looking for a new scent? Check out our fragrance&lt;/p&gt;\r\n'),
(50,1,'Women','','','&lt;p&gt;\r\n	Fragrance for Women&lt;/p&gt;\r\n'),
(50,9,'Women','','','&lt;p&gt;\r\n	Fragrance for Women&lt;/p&gt;\r\n'),
(51,1,'Men','','',''),
(51,9,'Men','','',''),
(52,1,'Hair Care','','','&lt;p&gt;\r\n	The widest range of premium hair products&lt;/p&gt;\r\n'),
(52,9,'Hair','','','&lt;p&gt;\r\n	The widest range of premium hair products&lt;/p&gt;\r\n'),
(53,1,'Shampoo','','',''),
(53,9,'Shampoo','','',''),
(54,1,'Conditioner','','',''),
(54,9,'Conditioner','','',''),
(58,1,'Men','','',''),
(58,9,'Men','','',''),
(59,1,'Fragrance Sets','','',''),
(59,9,'Fragrance Sets','','',''),
(60,1,'Skincare','','',''),
(60,9,'Skincare','','',''),
(61,1,'Pre-Shave &amp; Shaving','','',''),
(61,9,'Pre-Shave &amp; Shaving','','',''),
(62,1,'Post-Shave &amp; Moisturizers','','',''),
(62,9,'Post-Shave &amp; Moisturizers','','',''),
(63,1,'Body &amp; Shower','','',''),
(63,9,'Body &amp; Shower','','',''),
(64,1,'Bath &amp; Body','','',''),
(64,9,'Bath &amp; Body','','','');



--
-- Dumping data for table `coupon_descriptions`
--



INSERT INTO `ac_coupon_descriptions` 
VALUES 
(4,1,'Coupon (-10%)','10% Discount'),
(5,1,'Coupon (Free Shipping)','Free Shipping'),
(6,1,'Coupon (-10.00)','Fixed Amount Discount'),
(4,9,'Coupon (-10%)','10% Discount'),
(5,9,'Coupon (Free Shipping)','Free Shipping'),
(6,9,'Coupon (-10.00)','Fixed Amount Discount');



--
-- Dumping data for table `coupons`
--



INSERT INTO `ac_coupons` 
VALUES 
(4,'2222','P','10.0000',0,0,'0.0000','2009-01-27','2010-03-06',10,'10',1,'2009-01-27 13:55:03'),
(5,'3333','P','0.0000',0,1,'100.0000','2009-03-01','2009-08-31',10,'10',1,'2009-03-14 21:13:53'),
(6,'1111','F','10.0000',0,0,'10.0000','2007-01-01','2011-03-01',10,'10',1,'2009-03-14 21:15:18');



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
(1,17,'2012-03-14 13:21:21','2012-03-14 13:21:21'),
(2,20,'2012-03-14 14:49:33','2012-03-14 14:49:33'),
(3,20,'2012-03-15 10:56:23','2012-03-15 10:56:23');



--
-- Dumping data for table `customers`
--



INSERT INTO `ac_customers` 
VALUES 
(2,0,'Juliana','Davis', 'julidavis@abantecart.com', 'julidavis@abantecart.com','+44 1688 308321','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,1,1,1,8,'109.104.166.98','2011-08-31 10:25:37'),
(3,0,'Keely','Mccoy','keelymccoy@abantecart.com','keelymccoy@abantecart.com','+44 1324 483784 ','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,2,1,1,8,'109.104.166.98','2011-08-31 10:39:08'),
(4,0,'Zelda','Weiss','zeldaweiss@abantecart.com','zeldaweiss@abantecart.com','+44 28 9027 1066 ','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,3,1,1,8,'109.104.166.138','2011-08-31 10:42:58'),
(5,0,'Gloria','Macias','gloriamacias@abantecart.com','gloriamacias@abantecart.com','+1 418-461-2440','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,4,1,1,8,'109.104.166.98','2011-08-31 10:46:58'),
(6,0,'Bernard','Horne','bernardhorne@abantecart.com','bernardhorne@abantecart.com','+1 418-752-3369 ','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,5,1,1,8,'109.104.166.138','2011-08-31 10:50:27'),
(7,0,'James','Curtis','jamescurtis@abantecart.com','jamescurtis@abantecart.com','+1 303-497-1010','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,6,1,1,8,'109.104.166.138','2011-08-31 11:00:03'),
(8,0,'Bruce','Rosarini','brucerosarini@abantecart.com','brucerosarini@abantecart.com','+1 807-346-10763','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,7,1,1,8,'109.104.166.98','2011-08-31 11:08:23'),
(9,0,'Carlos','Compton','carloscmpton@abantecart.com','carloscmpton@abantecart.com','+1 867-874-22391','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,8,1,1,8,'109.104.166.98','2011-08-31 11:13:14'),
(10,0,'Garrison','Baxter','garrisonbaxter@abantecart.com','garrisonbaxter@abantecart.com','+1 907-543-43088','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,9,1,1,8,'109.104.166.138','2011-09-01 08:51:47'),
(11,0,'Anthony','Blair','anthonyblair@abantecart.com','anthonyblair@abantecart.com','+1 907-842-2240','','05ec6352a8b997363e5c6483aeffeb50','a:0:{}',0,10,1,1,8,'171.98.12.12','2011-09-01 08:54:26'),
(12,0,'Allen','Waters','allenwaters@abantecart.com','allenwaters@abantecart.com','+1 540-985-59700','','6b006ba67f3c172e146991a2ad46d865','a:0:{}',0,11,1,1,8,'109.104.166.98','2011-09-01 09:12:56'),
(13,0,'qqqqqq','qqqqqq','1@abantecart','1@abantecart','55 555 5555 5555','','f73469b693cecf7fa70c3e39b6fde1f4','a:1:{s:3:\"97.\";i:1;}',0,12,1,1,8,'109.104.166.98','2011-09-08 11:28:20');



--
-- Dumping data for table `download_descriptions`
--



INSERT INTO `ac_download_descriptions` 
VALUES 
(1,1,'visionare pdf file'),
(1,9,'visionare pdf file');



--
-- Dumping data for table `downloads`
--



INSERT INTO `ac_downloads` 
VALUES 
(1,'download/18/74/c.zip','Visionnaire.zip',150,'2012-03-15 06:43:16');



--
-- Dumping data for table `global_attributes`
--



INSERT INTO `ac_global_attributes` 
VALUES 
(1,0,0,1,'S',1,1,1),
(2,0,0,1,'C',0,0,1),
(5,0,0,1,'G',1,1,1);



--
-- Dumping data for table `global_attributes_descriptions`
--



INSERT INTO `ac_global_attributes_descriptions` 
VALUES 
(1,1,'Size'),
(1,9,'Tamaño'),
(2,1,'Gift Wrapping'),
(2,9,'Papel de Regalo'),
(5,1,'Fragrance Type'),
(5,9,'Fragancia Tipo');



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
(53,1,9,'1 oz'),
(52,1,1,'75ml'),
(52,1,9,'75ml'),
(51,1,1,'50ml'),
(51,1,9,'50ml'),
(50,1,1,'30ml'),
(50,1,9,'30ml'),
(49,1,1,'2.5 oz'),
(49,1,9,'2.5 oz'),
(48,1,1,'1.5 oz'),
(48,1,9,'1.5 oz'),
(47,1,1,'33.8 oz'),
(47,1,9,'33.8 oz'),
(46,1,1,'15.2 oz'),
(46,1,9,'15.2 oz'),
(45,1,1,'8.45 oz'),
(45,1,9,'8.45 oz'),
(32,2,1,''),
(42,1,9,'1.7 oz'),
(42,1,1,'1.7 oz'),
(43,1,9,'3.4 oz'),
(43,1,1,'3.4 oz'),
(44,1,9,'100ml'),
(44,1,1,'100ml'),
(76,5,1,'Eau de Toilette'),
(77,5,1,'Eau de Cologne'),
(75,5,1,'Eau de Parfum'),
(76,5,9,'Eau de Toilette'),
(77,5,9,'Eau de Cologne'),
(75,5,9,'Eau de Parfum');



--
-- Dumping data for table `layouts`
--



INSERT INTO `ac_layouts` 
VALUES 
(8,'default',0,'Product: Pro-V Color Hair Solutions Color Preserve Shine Conditioner (product_id=101)',1,'2012-03-14 14:52:05','2012-03-14 14:52:05'),
(9,'default',0,'Product: Flora By Gucci Eau Fraiche (product_id=110)',1,'2012-03-15 11:16:40','2012-03-15 11:16:40'),
(10,'default',0,'Product: Lancome Slimissime 360 Slimming Activating Concentrate Unisex Treatment (product_id=107)',1,'2012-03-15 13:03:42','2012-03-15 13:03:42');



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
-- Dumping data for table `order_downloads`
--






--
-- Dumping data for table `order_history`
--



INSERT INTO `ac_order_history` 
VALUES 
(1,1,1,1,'','0000-00-00 00:00:00'),
(2,2,1,1,'','2011-09-07 04:02:31'),
(3,3,1,1,'','2011-09-07 04:41:25'),
(4,4,1,1,'','2011-09-07 04:51:07'),
(5,5,1,1,'','2011-09-07 05:20:22'),
(6,6,1,1,'','2011-09-07 05:21:56'),
(7,7,1,1,'','2011-09-07 05:24:11'),
(8,8,1,1,'','2011-09-07 05:36:21'),
(9,9,1,1,'','2011-09-07 05:37:20'),
(10,10,1,1,'','2011-09-07 05:39:30'),
(11,11,1,1,'','2011-09-07 05:40:03'),
(12,12,1,1,'','2012-03-15 10:04:06'),
(13,13,1,1,'','2012-03-15 10:05:40');



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



--
-- Dumping data for table `order_products`
--



INSERT INTO `ac_order_products` 
VALUES 
(1,1,46,'Sony VAIO','Product 19','1000.0000','1000.0000','0.0000',1,0),
(2,1,36,'iPod Nano','Product 9','100.0000','100.0000','0.0000',1,0),
(3,1,42,'Apple Cinema 30&quot;','Product 15','100.0000','100.0000','0.0000',1,0),
(4,2,48,'iPod Classic','product 20','100.0000','100.0000','0.0000',1,0),
(5,2,41,'iMac','Product 14','500.0000','500.0000','0.0000',1,0),
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
(1,0,'',0,'Your Store','http://localhost/',1,8,'fdsfdsf','czx','(092) 222-2222','','demo@abantecart.com','fdsfdsf','czx','','addresss','','testing','55555','Maryland',3643,'United States',223,'{firstname} {lastname} {company} {address_1} {address_2} {city}, {zone} {postcode} {country}','Flat Shipping Rate','fdsfdsf','czx','','addresss','','testing','55555','Maryland',3643,'United States',223,'{firstname} {lastname} {company} {address_1} {address_2} {city}, {zone} {postcode} {country}','Cash On Delivery','','1585.4400',1,1,1,'GBP','1.00000000',0,'0000-00-00 00:00:00','0000-00-00 00:00:00','127.0.0.1'),
(2,0,'',0,'Web Store Name','http://abantecart/public_html/',11,8,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','','','','','','','','',0,'',0,'','','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','418.8100',1,1,1,'USD','1.00000000',0,'2011-09-07 04:02:28','2011-09-07 04:02:28','109.104.166.98'),
(3,0,'',0,'Web Store Name','http://abantecart/public_html/',5,8,'Gloria','Macias','+1 418-461-2440','','gloriamacias@abantecart.com','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Flat Shipping Rate','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Cash On Delivery','Please ASAP','246.0000',1,1,1,'USD','1.00000000',0,'2011-09-07 04:41:22','2011-09-07 04:41:22','109.104.166.98'),
(4,0,'',0,'Web Store Name','http://abantecart/public_html/',5,8,'Gloria','Macias','+1 418-461-2440','','gloriamacias@abantecart.com','','','','','','','','',0,'',0,'','','Gloria','Macias','','Camille Marcoux 15','','Blanc-Sablon','1569','Nunavut',609,'Canada',38,'','Cash On Delivery','','310.5270',1,1,1,'USD','1.00000000',0,'2011-09-07 04:51:04','2011-09-07 04:51:04','109.104.166.98'),
(5,0,'',0,'Web Store Name','http://abantecart/public_html/',3,8,'Keely','Mccoy','+44 1324 483784 ','','keelymccoy@abantecart.com','Keely','Mccoy','','4842 Amet','','Grangemouth','','Gloucestershire',3552,'United Kingdom',222,'','Flat Shipping Rate','Keely','Mccoy','','4842 Amet','','Grangemouth','','Gloucestershire',3552,'United Kingdom',222,'','Cash On Delivery','','686.0925',1,1,1,'USD','1.00000000',0,'2011-09-07 05:20:12','2011-09-07 05:20:12','109.104.166.98'),
(6,0,'',0,'Web Store Name','http://abantecart/public_html/',2,8,'Juliana','Davis','+44 1688 308321','','julidavis@abantecart.com','','','','','','','','',0,'',0,'','','Juliana','Davis','','Highlands and Islands PA75 6QE','','Isle of Mull','','Highlands',3559,'United Kingdom',222,'','Cash On Delivery','Bulgari','218.0850',1,1,1,'USD','1.00000000',0,'2011-09-07 05:21:54','2011-09-07 05:21:54','109.104.166.98'),
(7,0,'',0,'Web Store Name','http://abantecart/public_html/',9,8,'Carlos','Compton','+1 867-874-22391','','carloscmpton@abantecart.com','','','','','','','','',0,'',0,'','','Carlos','Compton','','31 Capital Drive','','Hay River','','Nova Scotia',608,'Canada',38,'','Cash On Delivery','','175.7700',1,1,1,'USD','1.00000000',0,'2011-09-07 05:24:09','2011-09-07 05:24:09','109.104.166.98'),
(8,0,'',0,'Web Store Name','http://abantecart/public_html/',8,8,'Bruce','Rosarini','+1 807-346-10763','','brucerosarini@abantecart.com','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','62.0000',1,1,1,'USD','1.00000000',0,'2011-09-07 05:36:19','2011-09-07 05:36:19','109.104.166.98'),
(9,0,'',0,'Web Store Name','http://abantecart/public_html/',8,8,'Bruce','Rosarini','+1 807-346-10763','','brucerosarini@abantecart.com','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Bruce','Rosarini','','61 Cumberland ST','','Thunder Bay','','Minnesota',3646,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','352.0000',1,1,1,'USD','1.00000000',0,'2011-09-07 05:37:18','2011-09-07 05:37:18','109.104.166.98'),
(10,0,'',0,'Web Store Name','http://abantecart/public_html/',12,8,'Allen','Waters','+1 540-985-59700','','allenwaters@abantecart.com','','','','','','','','',0,'',0,'','','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','155.1550',1,1,1,'USD','1.00000000',0,'2011-09-07 05:39:28','2011-09-07 05:39:28','109.104.166.98'),
(11,0,'',0,'Web Store Name','http://abantecart/public_html/',12,8,'Allen','Waters','+1 540-985-59700','','allenwaters@abantecart.com','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Allen','Waters','','110 Shenandoah Avenue','','Roanoke','','Virginia',3673,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','21.0000',1,1,1,'USD','1.00000000',0,'2011-09-07 05:40:01','2011-09-07 05:40:01','109.104.166.98'),
(12,0,'',0,'Web Store Name','http://abantecart/public_html/',11,8,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','487.3750',2,1,3,'GBP','0.63820000',0,'2012-03-15 10:06:41','2012-03-15 10:04:01','171.98.12.12'),
(13,0,'',0,'Web Store Name','http://abantecart/public_html/',11,8,'Anthony','Blair','+1 907-842-2240','','anthonyblair@abantecart.com','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Flat Shipping Rate','Anthony','Blair','','104 Main Street','','Dillingham','','North Dakota',3657,'United States',223,'{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}','Cash On Delivery','','626.9600',1,1,3,'GBP','0.63820000',0,'2012-03-15 10:05:15','2012-03-15 10:05:15','171.98.12.12');



--
-- Dumping data for table `page_descriptions`
--



INSERT INTO `ac_page_descriptions` 
VALUES 
(6,1,'Pro-V Color Hair Solutions Color Preserve Shine Conditioner','','','','','','2012-03-14 14:52:05','2012-03-14 14:52:05'),
(6,9,'Pro-V Color Hair Solutions Color Preserve Shine Conditioner with Pump','','','','','','2012-03-14 14:52:05','2012-03-14 14:52:05'),
(7,1,'Flora By Gucci Eau Fraiche','','','','','','2012-03-15 11:16:40','2012-03-15 11:16:40'),
(7,9,'Flora By Gucci Eau Fraiche','','','','','','2012-03-15 11:16:40','2012-03-15 11:16:40'),
(8,1,'Lancome Slimissime 360 Slimming Activating Concentrate Unisex Treatment','','','','','','2012-03-15 13:03:42','2012-03-15 13:03:42'),
(8,9,'Lancome Slimissime 360 Slimming Activating Concentrate Unisex Treatment','','','','','','2012-03-15 13:03:42','2012-03-15 13:03:42');



--
-- Dumping data for table `pages`
--



INSERT INTO `ac_pages` 
VALUES 
(6,0,'pages/product/product','product_id','101','2012-03-14 14:52:05','2012-03-14 14:52:05'),
(7,0,'pages/product/product','product_id','110','2012-03-15 11:16:40','2012-03-15 11:16:40'),
(8,0,'pages/product/product','product_id','107','2012-03-15 13:03:42','2012-03-15 13:03:42');



--
-- Dumping data for table `pages_layouts`
--



INSERT INTO `ac_pages_layouts` 
VALUES 
(8,6),
(9,7),
(10,8);



--
-- Dumping data for table `product_descriptions`
--



INSERT INTO `ac_product_descriptions` 
VALUES 
(72,9,'Brunette expressions Conditioner','','','&lt;p&gt;\r\n	PUNTO DE PARTIDA DE SU CABELLO&lt;br /&gt;\r\n	Teñidos o destacados&lt;/p&gt;\r\n&lt;p&gt;\r\n	¿QUÉ&lt;br /&gt;\r\n	Pantene Pro-V SOLUCIONES DE COLOR DEL PELO expresiones Morena ™acondicionador hidrata el pelo de colores vivos que se protege del estrés diarioy el daño. Esta fórmula no depositar color funciona para todos los tonos decabello castaño.&lt;/p&gt;\r\n&lt;p&gt;\r\n	¿POR QUÉ&lt;br /&gt;\r\n	Pantene descubrió que la superficie de coloración del cabello cambiosoxidativos de. Internamente, la química en el color del pelo ataques de fuerzadando las proteínas en el cabello y hace que la fibra más poroso, lo que lleva a un debilitamiento del cabello y que se desvanece rápidamente. La superficie delas fibras del cabello se convierte en irregular y se desintegra la capa protectora de la fibra capilar. Sin la capa protectora, la fibra del cabello es propenso amicro-cicatrices y daños, que cambia la forma en que interactúa con la luz y conduce a un aspecto mate.&lt;/p&gt;\r\n&lt;p&gt;\r\n	CÓMO&lt;br /&gt;\r\n	Avanzada de Pantene Pro-Vitamina fórmula realza el color morena de gran intensidad y un brillo radiante. No el color del depósito de ingredientes acondicionadores mejorar y proteger el cabello teñido, mientras que ayuda arestaurar el brillo a las hebras de colores. El pelo se humedece y se infunde conun brillo radiante.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USO&lt;br /&gt;\r\n	Para obtener un color rico y vibrante que es brillante y de aspecto saludable, el uso con Pantene Pro-V de Pantene SOLUCIONES Color de cabello morenoExpresiones ™ Champú y tratamiento nutritivo de color.&lt;/p&gt;\r\n'),
(73,1,'Highlighting Expressions','','','&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Pro-V COLOUR HAIR SOLUTIONS Highlighting Expressions™ Conditioner protects and enhances colour treated hair and infuses blonde highlights with shine. The advanced Pro-Vitamin formula restores shine to dull highlights and protects hair from daily damage. This non-colour depositing formula works for all blonde highlights.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Pantene uncovered that oxidative colouring changes hair’s structure. Internally, the chemistry in hair colour attacks strength-giving proteins in hair and makes the fibre more porous, leading to weaker hair that fades more quickly. The surface of the hair fibres becomes uneven and the protective layer of the hair fibre disintegrates. Without the protective layer, the hair fibre is prone to micro-scarring and damage, which changes the way it interacts with light and leads to a dull appearance.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s non-colour depositing conditioner is designed to reinforce the structure of blonde highlighted hair and give it what it needs to reveal vibrant, glossy colour. Conditioning ingredients help revitalize and replenish highlighted hair while delivering brilliant shine and protecting from future damage. The result is healthy-looking hair rejuvenated with shimmering blonde highlights.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For rich, vibrant colour that’s shiny and healthy-looking, use with Pantene Pro-V Pantene COLOUR HAIR SOLUTIONS Highlighting Expressions™ Shampoo and Colour Nourishing Treatment.&lt;/p&gt;\r\n'),
(73,9,'Brunette expressions Conditioner','','','&lt;p&gt;\r\n	PUNTO DE PARTIDA DE SU CABELLO&lt;br /&gt;\r\n	Teñidos o destacados&lt;/p&gt;\r\n&lt;p&gt;\r\n	¿QUÉ&lt;br /&gt;\r\n	Pantene Pro-V SOLUCIONES DE COLOR DEL PELO expresiones Morena ™ acondicionador hidrata el pelo de colores vivos que se protege del estrés diario y el daño. Esta fórmula no depositar color funciona para todos los tonos de cabello castaño.&lt;/p&gt;\r\n&lt;p&gt;\r\n	¿POR QUÉ&lt;br /&gt;\r\n	Pantene descubrió que la superficie de coloración del cabello cambios oxidativos de. Internamente, la química en el color del pelo ataques de fuerza dando las proteínas en el cabello y hace que la fibra más poroso, lo que lleva a un debilitamiento del cabello y que se desvanece rápidamente. La superficie de las fibras del cabello se convierte en irregular y se desintegra la capa protectora de la fibra capilar. Sin la capa protectora, la fibra del cabello es propenso a micro-cicatrices y daños, que cambia la forma en que interactúa con la luz y conduce a un aspecto mate.&lt;/p&gt;\r\n&lt;p&gt;\r\n	CÓMO&lt;br /&gt;\r\n	Avanzada de Pantene Pro-Vitamina fórmula realza el color morena de gran intensidad y un brillo radiante. No el color del depósito de ingredientes acondicionadores mejorar y proteger el cabello teñido, mientras que ayuda a restaurar el brillo a las hebras de colores. El pelo se humedece y se infunde con un brillo radiante.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USO&lt;br /&gt;\r\n	Para obtener un color rico y vibrante que es brillante y de aspecto saludable, el uso con Pantene Pro-V de Pantene SOLUCIONES Color de cabello moreno Expresiones ™ Champú y tratamiento nutritivo de color.&lt;/p&gt;\r\n'),
(74,1,'Curls to straight Shampoo','','','&lt;p&gt;\r\n	YOUR HAIR’S STARTING POINT&lt;br /&gt;\r\n	Curly&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Curly Hair Series Curls to Straight Shampoo gently removes build-up, adding softness and control to your curls. The cleansing formula helps align and smooth the hair fibers. The result is healthy-looking hair that’s protected from frizz and ready for straight styling.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Research shows that each curly hair fibre grows in a unique pattern, twisting and turning in all directions. This unpredictable pattern makes it difficult to create and control straight styles. The curved fibres of curly hair intersect with each other more often than any other hair type, causing friction which can result in breakage. The curvature of the hair fibre also provides a large amount of volume in curly hair, which can be hard to tame.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s straight shampoo contains micro-smoothers that aid you in loosening and unwinding curls from their natural pattern. Curly hair is left ready for frizz controlled straight styling, and protected from styling damage.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For healthy-looking, curly hair that’s styled straight, use with Curls to Straight Conditioner and Anti-Frizz Straightening Crème.&lt;/p&gt;\r\n'),
(74,9,'Curls to straight champú','','','&lt;p&gt;\r\n	PUNTO DE PARTIDA DE SU CABELLO&lt;br /&gt;\r\n	rizado&lt;/p&gt;\r\n&lt;p&gt;\r\n	¿QUÉ&lt;br /&gt;\r\n	Pantene rizado rizos del pelo de la serie de Shampoo recta elimina suavemente la acumulación, la adición de la suavidad y el control de tus rizos. La fórmula de limpieza ayuda a alinear y alisar la fibra capilar. El resultado es una apariencia saludable del cabello que está protegido desde el frizz y listo para su estilo directo.&lt;/p&gt;\r\n&lt;p&gt;\r\n	¿POR QUÉ&lt;br /&gt;\r\n	La investigación muestra que cada fibra de cabello rizado crece en un patrón único, dando vueltas en todas direcciones. Este patrón impredecible hace que sea difícil de crear y controlar los estilos rectos. Las fibras curvadas del cabello rizado se intersectan entre sí con mayor frecuencia que cualquier otro tipo de cabello, causando fricción que puede conducir a la rotura. La curvatura de la fibra del cabello también proporciona una gran cantidad de volumen en el pelo rizado, que puede ser difícil de domar.&lt;/p&gt;\r\n&lt;p&gt;\r\n	CÓMO&lt;br /&gt;\r\n	Shampoo Pantene recta contiene micro-alisadores que ayuda a aflojar y relajarse rizos de su patrón natural. El pelo rizado se deja listo para el frizz hacia un estilo controlado y protegido de daños estilo.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USO&lt;br /&gt;\r\n	De aspecto sano, el pelo rizado que es un estilo directo, su uso con rizos de acondicionador recta y Crema Anti-Frizz enderezar.&lt;/p&gt;\r\n'),
(75,1,'Dove Men +Care Body Wash','','','&lt;p&gt;\r\n	A body and face wash developed for men\'s skin with Micromoisture technology.&lt;br /&gt;\r\n	Micromoisture activates on skin when lathering up, clinically proven to fight skin dryness.&lt;br /&gt;\r\n	Deep cleansing gel that rinses off easily. With purifying grains.&lt;br /&gt;\r\n	Dermatologist recommended.&lt;/p&gt;\r\n'),
(75,9,'Dove Men +Care Gel de Baño','','','&lt;p&gt;\r\n	Un lavado de cara y cuerpo desarrollado para la piel del hombre con la tecnología Micromoisture.&lt;br /&gt;\r\n	Micromoisture activa en la piel cuando enjabonando, clínicamente probado para combatir la sequedad de la piel.&lt;br /&gt;\r\n	Gel de limpieza profunda que se enjuaga fácilmente. Con granos de purificación.&lt;br /&gt;\r\n	Dermatólogo recomienda.&lt;/p&gt;\r\n'),
(76,1,'Men+Care Clean Comfort Deodorant','','','&lt;p&gt;\r\n	The first scented deodorant from Dove® specifically designed with a non-irritating formula to give men the power of 48-hour protection against underarm odor with advanced ¼ moisturizer technology. The bottom line? It’s tough on odor, not on skin&lt;/p&gt;\r\n'),
(76,9,'Men+Care Clean Comfort Desodorante','','','&lt;p&gt;\r\n	El desodorante con aroma de primera Dove ® diseñado específicamente con una fórmula no irritante para dar a los hombres el poder de 48 horas de protección contra el olor de las axilas con avanzada tecnología de ¼ de crema hidratante. El resultado final? Es difícil en el olor, no sobre la piel&lt;/p&gt;\r\n'),
(77,1,'Men+Care Active Clean Shower Tool','tool, man','','&lt;p&gt;\r\n	Dove® Men+CareTM Active Clean Dual-Sided Shower Tool works with body wash for extra scrubbing power you can’t get from just using your hands. The mesh side delivers the perfect amount of thick cleansing lather, and the scrub side helps exfoliate for a deeper clean. Easy to grip and easy to hang. For best results, replace every 4-6 weeks.&lt;/p&gt;\r\n'),
(77,9,'Men+Care Active Clean Herramienta de ducha','','','&lt;p&gt;\r\n	Dove ® Men + CareTM activo limpia de dos caras, la herramienta funciona con ducha lavado del cuerpo por el poder de lavado extra que no se puede obtener a partir de sólo usar las manos. El lado de la malla proporciona la cantidad perfecta de espuma de limpieza de espesor, y el lado matorrales ayuda a exfoliar una profunda limpieza. Fácil de agarrar y fácil de colgar. Para obtener mejores resultados, reemplace cada 4-6 semanas.&lt;/p&gt;\r\n'),
(78,1,'ck IN2U Eau De Toilette Spray for Him','','','&lt;p&gt;\r\n	Fresh but warm; a tension that creates sexiness.Spontaneous - sexy - connectedCK IN2U him is a fresh woody oriental that penetrates with lime gin fizz and rushes into a combination of cool musks that radiate from top to bottom and leaves you wanting more.&lt;/p&gt;\r\n'),
(78,9,'ck IN2U Eau De Toilette Spray para él','','','&lt;p&gt;\r\n	Fresca pero cálida, una tensión que crea sexiness.Spontaneous - sexy - connectedCK le IN2U es un oriental amaderado fresco que penetra con gin fizz y la cal se precipita en una combinación de almizcles frescos que irradia desde arriba hacia abajo y te deja con ganas de más.&lt;/p&gt;\r\n'),
(79,1,'ck One Gift Set','','','&lt;p&gt;\r\n	2 PC Gift Set includes 3.4 oz EDT Spray + Magnets. Ck One Cologne by Calvin Klein, Two bodies, two minds, and two souls are merged into the heat and passion of one. This erotic cologne combines man and woman with one provocative scent. This clean, refreshing fragrance has notes of bergamot, cardamom, pineapple, papaya, amber, and green tea.&lt;/p&gt;\r\n'),
(79,9,'ck One Set de regalo','','','&lt;p&gt;\r\n	2 Gift Set incluye PC 3.4 oz EDT Spray + imanes. CK One de Calvin Klein Colonia, dos cuerpos, dos mentes y dos almas se funden en el calor y la pasión de uno. Esta colonia erótica combina el hombre y la mujer con un aroma provocativo. Esta fragancia limpia y refrescante con notas de bergamota, cardamomo, piña, papaya, naranja, y té verde.&lt;/p&gt;\r\n'),
(50,1,'Skinsheen Bronzer Stick','','','&lt;p&gt;\r\n	Bronzes, shapes and sculpts the face. Sheer-to-medium buildable coverage that looks naturally radiant and sunny. Stashable - and with its M·A·C Surf, Baby look – way cool. Limited edition.&lt;/p&gt;\r\n'),
(50,9,'Skinsheen Bronzer Stick','','','&lt;p&gt;\r\n	Bronces, las formas y esculpe el rostro. Pura y mediano plazo la cobertura de edificabilidad que se ve naturalmente radiante y soleado. Stashable - y con su M · A · C Surf, busque bebé - manera fresca. De edición limitada.&lt;/p&gt;\r\n'),
(51,1,'BeneFit Girl Meets Pearl','','','&lt;p&gt;\r\n	Luxurious liquid pearl…the perfect accessory! This soft golden pink liquid pearl glides on for a breathtakingly luminous complexion. Customise your pearlessence with the easy to use twist up package … a few clicks for a subtle sheen, more clicks for a whoa! glow. Pat the luminous liquid over make up or wear alone for dewy lit from within radiance. It\'s pure pearly pleasure. Raspberry and chamomile for soothing. Light reflecting pigments for exquisite radiance. Sweet almond seed for firming and smoothing. Sesame seed oil for moisturising.Fresh red raspberry scent.&lt;/p&gt;\r\n'),
(51,9,'BeneFit Girl Meets Pearl','','','&lt;p&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Este&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;oro suave&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de color rosa&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;perla&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;líquido&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;se desliza&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;por&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;una tez&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;increíblemente&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;luminosa&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Giro&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;hacia arriba y&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;una palmadita en&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;más de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;maquillaje o&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;llevar&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;solo por un&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;resplandor&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;rocío&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de lujo&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;iluminado&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;desde adentro&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Es un placer&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;perla&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;pura&lt;/span&gt;&lt;span&gt;!&lt;/span&gt;&lt;/p&gt;\r\n'),
(52,1,'Benefit Bella Bamba','','','&lt;p&gt;\r\n	Amplify cheekbones and create the illusion of sculpted features with this 3D watermelon blush. Laced with shimmering gold undertones, bellabamba is taking eye popping pretty to the third dimension…you’ll never use traditional blush again! Tip: For a poreless complexion that pops, sweep bellabamba on cheeks after applying porefessional&lt;/p&gt;\r\n'),
(52,9,'Benefit Bella Bamba','','','&lt;p&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Amplificar&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;los pómulos&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y crear la ilusión&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de características&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;esculpidas&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;con esta&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;vista&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;3D&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de sandía&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Mezclada con&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;tintes&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de oro brillante&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;bellabamba&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;está tomando&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;ojo haciendo estallar&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;bastante&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;a la tercera dimensión&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;…&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;que nunca uso&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;vista&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;tradicional&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;una vez más&lt;/span&gt;&lt;span&gt;!&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Sugerencia: Para obtener&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;un cutis&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;sin poros&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;que aparece&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;barrido&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;bellabamba&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;en las mejillas&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;después de aplicar&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;porefessional&lt;/span&gt;&lt;/p&gt;\r\n'),
(53,1,'Tropiques Minerale Loose Bronzer','','','&lt;p&gt;\r\n	Precious earths, exclusively selected for their luxurious silky texture and gentle quality, are layered with mineral pigments in this lightweight powder to mimic the true color of tanned skin. Unique technology with inalterable earths ensures exquisite wear all day. Mineral blend smoothes complexion, while Aloe Vera helps protect skin from dryness.&lt;/p&gt;\r\n'),
(53,9,'Tropiques Minerale Loose Bronzer','','','&lt;p&gt;\r\n	Tierras preciosas, exclusivamente seleccionados por su rica textura sedosa y suave calidad, se colocan en capas de pigmentos minerales en este polvo ligero para imitar el verdadero color de la piel bronceada. Tecnología única con tierras inalterable asegura exquisita llevar todo el día. Mezcla mineral suaviza el cutis, mientras que Aloe Vera ayuda a proteger la piel de la sequedad.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(54,1,'L\'EXTRÊME Instant Extensions Lengthening Mascara','','','&lt;p&gt;\r\n	Extend your lashes up to 60% instantly! This exclusive Fibrestretch formula takes even the smallest natural lashes to dramatic lengths. The patented Extreme Lash brush attaches supple fibers to every eyelash for an instant lash extension effect.&lt;/p&gt;\r\n'),
(54,9,'L\'EXTRÊME Instant Extensions Lengthening Mascara','','','&lt;p&gt;\r\n	Extender las pestañas hasta un 60% al instante! Esta fórmula exclusiva Fibrestretch lleva hasta el más mínimo natural de las pestañas a las longitudes dramático. El cepillo patentado Lash extrema une las fibras flexibles a cada pestaña para un efecto de latigazo extensión instantánea.&lt;/p&gt;\r\n'),
(55,1,'LE ROUGE ABSOLU Reshaping &amp; Replenishing LipColour SPF 15','','','&lt;p&gt;\r\n	Smoother. Fuller. Absolutely replenished lips. This advanced lip color provides 6-hour care with continuous moisture and protective Vitamin E. Features plumping polymer and non-feathering color to define and reshape lips. Choose from an array of absolutely luxurious shades with a lustrous pearl or satin cream finish.&lt;/p&gt;\r\n'),
(55,9,'LE ROUGE ABSOLU Reshaping &amp; Replenishing LipColour SPF 15','','','&lt;p&gt;\r\n	Más suave. Fuller. Absolutamente repone los labios. Este color de los labios avanzado proporciona seis horas de cuidado con la humedad continua y protección vitamina E. Características plumping polímero y no de plumas de colores para definir y remodelar los labios. Elija entre una amplia gama de tonos absolutamente de lujo con una perla brillante o acabado satinado crema.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(56,1,'Waterproof Protective Undereye Concealer','','','&lt;p&gt;\r\n	This natural coverage concealer lets you instantly eliminate tell-tale signs of stress and fatigue. Provides complete, natural-looking coverage, evens skin tone, covers dark circles and minimizes fine lines around the eyes. The Result: A soft, matte finish&lt;/p&gt;\r\n'),
(56,9,'Waterproof Protective Undereye Concealer','','','&lt;p&gt;\r\n	Este corrector cobertura natural permite eliminar instantáneamente señales indicadoras de estrés y fatiga. Proporciona un completo y de apariencia natural de cobertura, empareja el tono de la piel, cubre las ojeras y reduce las líneas finas alrededor de los ojos. El resultado: un acabado suave y mate&lt;/p&gt;\r\n'),
(57,1,'Delicate Oil-Free Powder Blush','','','&lt;p&gt;\r\n	A sparkling shimmer of colour for a radiant glow. Silky soft, micro-bubble formula glides on easily and evenly. Lasts for hours. Oil-free and oil-absorbing, yet moisture-balancing. Perfect for all skin types.&lt;/p&gt;\r\n'),
(57,9,'Delicada Oil-Free Powder Blush','','','&lt;p&gt;\r\n	Un brillo resplandeciente de color para un brillo radiante. Suave como la seda, de micro-burbujas se desliza sobre la fórmula fácil y uniforme. Dura por horas. Libre de aceite y el aceite que absorbe, sin embargo, la humedad de equilibrio. Perfecto para todo tipo de piel.&lt;/p&gt;\r\n'),
(58,1,'&quot;hello flawless!&quot; custom powder foundation with SPF 15','','','&lt;p&gt;\r\n	There are degrees of cover-up…some like less, some like more! Our blendable powder formula with SPF 15 goes on beautifully sheer &amp;amp; builds easily for customized coverage. Sweep on with the accompanying brush for a sheer, natural finish or apply with the sponge for full coverage or spot cover-up. Our 6 flattering shades (2 light, 2 medium, 2 deep) make it incredibly easy to find your perfect shade. Once gals apply &quot;hello flawless!&quot; they\'ll finally have met their match q!&lt;/p&gt;\r\n'),
(58,9,'&quot;hello flawless!&quot; polvo personalizado foundation with SPF 15','','','&lt;p&gt;\r\n	Hay grados de encubrimiento … a algunos les gusta menos, a algunos les gustamás! Nuestra fórmula en polvo con SPF 15 blendable pasa maravillosamentepura y construye fácilmente para una cobertura personalizada. Barrer con el pincelde acompañamiento para un acabado simple, natural o aplicar con la esponjapara una cobertura completa o el punto de encubrimiento. Nuestro hijo de 6 tonoshalagadores (2 luz, 2 medianas y 2 de profundidad) que sea increíblemente fácil de encontrar su tono perfecto. Una vez que se aplican chicas &quot;hola impecable!&quot;que finalmente tendrá encontrado a esa persona!&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(59,1,'Viva Glam Lipstick','','','&lt;p&gt;\r\n	Time to wham up the GLAM in VIVA GLAM! It\'s a gaga-glamorous look at our abiding passion: The M·A·C AIDS Fund and the VIVA GLAM program are the heart and soul of M·A·C Cosmetics. Ladies and gentlemen, we give you the sensational Cyndi Lauper and the electric Lady Gaga&lt;/p&gt;\r\n'),
(59,9,'Viva Glam Lipstick','','','&lt;p&gt;\r\n	Es hora de Wham el GLAM de VIVA GLAM! Es una mirada gaga-glamoroso en nuestra pasión perdurable: El M · A · C AIDS Fund y el Programa VIVA GLAM son el corazón y el alma de M · A · C Cosmetics. Señoras y señores, le damos la sensacional Cyndi Lauper y los Electric Lady Gaga&lt;/p&gt;\r\n'),
(60,1,'Nail Lacquer','','','&lt;p&gt;\r\n	Revolutionary new high gloss formula. Three long-wearing finishes - Cream, Sheer, and Frosted. Visibly different. Provides no-streak/no-chip finish. Contains conditioners and UV protection. Go hi-lacquer!&lt;/p&gt;\r\n'),
(60,9,'Nail Laca','','','&lt;p&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Nueva y revolucionaria&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;fórmula&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de alto brillo.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Tres&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de larga duración&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;acabados&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;- Crema&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;escarpado,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;helado&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Visiblemente&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;diferentes&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Proporciona&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;no-streak/no-chip&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;final.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Contiene&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;acondicionadores&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y protección&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;UV&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Ir&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps atn&quot;&gt;hi-&lt;/span&gt;&lt;span&gt;laca&lt;/span&gt;&lt;span&gt;!&lt;/span&gt;&lt;/p&gt;\r\n'),
(61,1,'Color Design Eye Brightening All in One 5 Shadow &amp; Liner Palette','','','&lt;p&gt;\r\n	Infinitely luminous. Sensationally smooth. All-in-one 5 shadow palette to brighten eyes. Lancome’s new versatile, all-in-one palette conveniently creates a full eye look for day or night. Experience the newest generation of luminosity as silky lustrous powders transparently wrap the skin, allowing a seamless layering of pure color for a silky sheen and radiant finish. Build with absolute precision and apply the shades in 5 simple steps (all over, lid, crease, highlighter and liner) to design your customized eye look. Contour, sculpt and lift in soft day colors or intensify with dramatic evening hues for smoldering smoky effects. Long wear, 8-hour formula. Color does not fade, continues to stay true&lt;/p&gt;\r\n'),
(61,9,'Color Design Lluminar los Ojos All in One 5 Shadow &amp; Liner Palette','','','&lt;p&gt;\r\n	&amp;nbsp;Infinitamente luminoso. Sensacionalmente suave. Todo en un 5 paleta de sombra para iluminar los ojos. Nuevas y versátiles de Lancome, todo en una paleta convenientemente crea un look completo de la vista de día o de noche. Experiencia de la nueva generación de luminosidad como polvos de seda brillante transparente envoltura de la piel, permitiendo una perfecta superposición de colores puros para un brillo sedoso y acabado radiante. Construir con una precisión absoluta y aplicar las sombras en 5 sencillos pasos (por todas partes, tapa, pliegue, rotulador y el forro) para diseñar su aspecto visual personalizado. Contorno, esculpir y levantar en colores suaves día o intensificar la noche con tonos dramáticos para los efectos de humo ardiente. Larga duración, de 8 horas de fórmula. Color no se desvanece, sigue siendo fiel&lt;/p&gt;\r\n'),
(62,1,'ck one shock for him Deodorant','','','&lt;p&gt;\r\n	Shock Off! cK one shock for him opens with pure freshness, the heart pulses with spice and finishes with a masculine tobacco musk. Experience ck one shock, the newest fragrance from Calvin Klein with this 2.6 oz Deodorant.&lt;/p&gt;\r\n'),
(62,9,'ck one shock for him Desodorante','','','&lt;p&gt;\r\n	Choque de descuento! cK un choque para él se abre con la frescura pura, los impulsos del corazón con especias y termina con un almizcle tabaco masculino. Ck una experiencia de choque, la nueva fragancia de Calvin Klein con este desodorante oz 2.6.&lt;/p&gt;\r\n'),
(63,1,'Pour Homme Eau de Toilette','','','&lt;p&gt;\r\n	An intriguing masculine fragrance that fuses the bracing freshness of Darjeeling tea with the intensity of spice and musk. For those seeking a discreet accent to their personality.&lt;/p&gt;\r\n'),
(63,9,'Pour Homme Eau de Toilette','','','&lt;p&gt;\r\n	Una fragancia masculina fascinante que mezcla la frescura refuerzo de té de Darjeeling con la intensidad de las especias y almizcle. Para aquellos que buscan un toque discreto a su personalidad.&lt;/p&gt;\r\n'),
(64,1,' Beauty Eau de Parfum','','','&lt;p&gt;\r\n	Beauty by Calvin Klein is a sophisticated and feminine fragrance presenting a new scructure to modern florals. Radiating rich and intense luminosity; Beauty leaves a complex and memorable impression. Experience the glamour and strength with the Beauty&amp;nbsp; Eau de Parfum&lt;/p&gt;\r\n'),
(64,9,' Beauty Eau de Parfum','','','&lt;p&gt;\r\n	La belleza de Calvin Klein es una fragancia sofisticada y femenina presenta un nuevo scructure florales modernas. Irradia luminosidad intensa y rica; belleza deja una impresión compleja y memorable. Experimente el glamour y la fuerza con la belleza Eau de Parfum&lt;/p&gt;\r\n'),
(65,1,'Absolue Eye Precious Cells','','','&lt;p&gt;\r\n	Smoothes – Tightens – Regenerates Radiance Exclusive innovation from Lancôme A powerful combination of unique ingredients – Reconstruction Complex and Pro-Xylane™ – has been shown to improve the condition around the stem cells, and stimulate cell regeneration to reconstruct skin to a denser quality*. Results Immediately, the eye contour appears smoother and more radiant. Day 7, signs of fatigue are minimized and the appearance of puffiness is reduced. Day 28, density is improved. Skin is soft and looks healthier. The youthful look of the eye contour is restored. Ophthalmologist – tested. Dermatologist – tested for safety.&lt;/p&gt;\r\n'),
(65,9,'Absolue Eye Precious Cells','','','&lt;p&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Suaviza&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;- Cierra&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;-&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Regenera&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la innovación&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;resplandor&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;exclusivo de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Lancôme&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Una potente combinación de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;ingredientes únicos&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;-&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Complejo&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Pro&lt;/span&gt;&lt;span class=&quot;atn&quot;&gt;-&lt;/span&gt;&lt;span&gt;Reconstrucción y&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Xylane&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;™&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;-&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Se ha demostrado que&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;mejorar la condición&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;alrededor de las células&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;madre&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y estimular la&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;regeneración de las células&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;para reconstruir&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la piel&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;una calidad&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;más denso&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;*.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Los resultados de inmediato&lt;/span&gt;&lt;span&gt;, el&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;contorno de los ojos&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;parece más suave&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y más radiante.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;El día 7,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;los signos de fatiga&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;se reducen al mínimo&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y la aparición&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de las bolsas&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;se reduce.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;El día 28&lt;/span&gt;&lt;span&gt;, la densidad es&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;mejor&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;La piel es&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;suave y un aspecto&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;más saludable.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;El aspecto juvenil&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;contorno de los ojos&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;se restaura&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Oftalmólogo&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;-&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;prueba.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Dermatólogo -&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;pruebas de seguridad.&lt;/span&gt;&lt;/p&gt;\r\n'),
(66,1,'Total Moisture Facial Cream','','','&lt;p&gt;\r\n	Say good-bye to dry skin and hello to “total moisture”. This facial cream provides concentrated immediate &amp;amp; long-term hydration for an ultra radiant complexion. Contains exclusive tri-radiance complex to help develop the skin’s reserves of water &amp;amp; reinforce skin’s moisture barrier for a radiantly refreshed complexion. For normal to dry skin.&lt;/p&gt;\r\n'),
(66,9,'Total Moisture Facial Creama','','','&lt;p&gt;\r\n	Diga adiós a la piel seca y hola a &quot;la humedad total&quot;. Esta crema facial proporciona una hidratación inmediata y concentrada a largo plazo para un cutis radiante ultra. Contiene el exclusivo complejo tri-radiación para ayudar a desarrollar las reservas de la piel del agua y reforzar la barrera de humedad de la piel para un cutis radiante y fresco. Para piel normal a seca.&lt;/p&gt;\r\n'),
(67,1,'Flash Bronzer Body Gel','','','&lt;p&gt;\r\n	Look irresistible! Discover the self-tanning results you dream of: Instant bronzed glowing body Enriched with natural caramel extract for an immediate, gorgeous, bronzed glow. Exquisitely beautiful tan The perfect balance of self-tanning ingredients helps to achieve an ideal color, providing an even, natural-looking, golden tan. Color development within 30 minutes, lasting up to 5 days. Transfer-resistant formula With an exclusive Color-Set™ complex that smoothes on without streaks, dries in 4 minutes and protects clothes against rub-off. Hydrating &amp;amp; smoothing action Leaves skin soft, smooth, and hydrated. Pure Vitamin E delivers antioxidant protection, helping to reduce signs of premature aging. Indulgent experience Delightfully scented with hints of jasmine and honey in a silky, non-greasy formula&lt;/p&gt;\r\n'),
(67,9,'Flash Bronzer Body Gel','','','&lt;p&gt;\r\n	Mira irresistible! Descubra los resultados de autobronceado que sueñas: Instantcuerpo bronceado brillante Enriquecido con extracto natural de caramelo parauna investigación inmediata, brillo hermoso, bronceado. Exquisitamente hermosobronceado El perfecto equilibrio de ingredientes autobronceadores ayuda a lograr un color ideal, proporcionando un uniforme, de aspecto natural, bronceado dorado. Color de desarrollo a los 30 minutos, con una duración de hasta 5 días.Transferencia resistente fórmula con un exclusivo color-Set ™ complejo que suaviza el color uniforme, se seca en 4 minutos y protege la ropa contra el rub-off.Hidratación y suavizar la acción Deja la piel suave, tersa, e hidratada. PuraVitamina E proporciona protección antioxidante, ayudando a reducir los signosdel envejecimiento prematuro. Una experiencia de recreo deliciosamenteperfumado con notas de jazmín y miel en una sedosa y no grasa fórmula&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(68,1,'Absolute Anti-Age Spot Replenishing Unifying TreatmentSPF 15','','','&lt;p&gt;\r\n	A luxurious and comprehensive hand treatment that addresses the special needs of mature hands. Diminishes and discourages the appearance of age spots, while replenishing and protecting the skin. RESULT: Immediately, skin on hands is hydrated, soft and luminous. With continued use, skin becomes more uniform, looks firmer and youthful.Massage into hands and cuticles as needed.&lt;/p&gt;\r\n'),
(68,9,'Absolute Anti-Age Lugar Tratamiento Reconstituyente Unificación SPF 15','','','&lt;p&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Una mano&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de lujo y&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de tratamiento integral&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;que responda a las&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;necesidades especiales de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;las manos&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;maduras.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Disminuye&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y desalienta&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la aparición de&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;manchas de la edad&lt;/span&gt;&lt;span&gt;, mientras que&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la reposición&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y la protección de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la piel.&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;RESULTADO:&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Inmediatamente&lt;/span&gt;&lt;span&gt;, la piel de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;las manos&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;es&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;hidratada, suave&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y luminosa.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Con el uso continuo&lt;/span&gt;&lt;span&gt;, la piel&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;se vuelve más&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;uniforme,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;se ve&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;más firme y&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;youthful.Massage&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;en las manos&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y cutículas&lt;/span&gt;&lt;span&gt;, según sea necesario&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;/p&gt;\r\n'),
(69,1,'Seaweed Conditioner','','','&lt;p&gt;\r\n	What it is:&lt;br /&gt;\r\n	A lightweight detangler made with marine seaweed and spirulina.&lt;/p&gt;\r\n&lt;p&gt;\r\n	What it does:&lt;br /&gt;\r\n	This conditioner gently detangles, nourishes, softens, and helps to manage freshly washed hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	What it is formulated WITHOUT:&lt;br /&gt;\r\n	- Parabens&lt;/p&gt;\r\n&lt;p&gt;\r\n	What else you need to know:&lt;br /&gt;\r\n	Made with marine greens for practically anyone (and ideal for frequent bathers), this conditioner is best paired with Seaweed Shampoo. It\'s also safe for use on color-treated hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	-Sea Silk Extract: Nourishes scalp; promotes healthy looking hair.&lt;br /&gt;\r\n	-Ascophyllum Nudosum (Seaweed) Extract: Moisturizes; adds elasticity, luster, softness, body; reduces flyaways.&lt;br /&gt;\r\n	-Macrocystis Pyrifera (Sea Kelp) Extract: Adds shine and manageability.&lt;br /&gt;\r\n	-Spirulina Maxima Extract: Hydrates.&lt;/p&gt;\r\n'),
(69,9,'Seaweed Conditioner','','','&lt;p&gt;\r\n	Lo que es:&lt;br /&gt;\r\n	A desenredante ligero, hecho con algas marinas y la espirulina.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Lo que hace:&lt;br /&gt;\r\n	Este acondicionador desenreda con suavidad, nutre, suaviza y ayuda a manejar recién cabello lavado.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Lo que está formulado sin:&lt;br /&gt;\r\n	- Los parabenos&lt;/p&gt;\r\n&lt;p&gt;\r\n	¿Qué más necesita saber:&lt;br /&gt;\r\n	Hecho con hojas de marinos para que prácticamente cualquier persona (y es ideal para los bañistas frecuente), este acondicionador es el mejor emparejado con el champú de algas. También es seguro para su uso en el cabello teñido.&lt;/p&gt;\r\n&lt;p&gt;\r\n	-Sea extracto de seda: el cuero cabelludo Nutre, un cabello saludable.&lt;br /&gt;\r\n	-Ascophyllum Nudosum (alga marina) Extracto: Hidrata, da elasticidad, brillo, suavidad, cuerpo, reduce pelos sueltos.&lt;br /&gt;\r\n	-Macrocystis pyrifera (alga marina) Extracto: Agrega brillo y manejabilidad.&lt;br /&gt;\r\n	-Extracto de Spirulina Maxima: Hidrata.&lt;/p&gt;\r\n'),
(70,1,'Eau Parfumee au The Vert Shampoo','','','&lt;p&gt;\r\n	Structured around the refreshing vitality and purtiy of green tea, Bvlgari Eau the Vert Shampoo is an expression of elegance and personal indulgence. Delicately perfumed Eau Parfumée au thé vert shampoo gentle cleansing action makes it perfect for daily use.&lt;/p&gt;\r\n'),
(70,9,'Eau Parfumee au The Vert champú','','','&lt;p&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Estructurado en torno a&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la vitalidad&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;refrescante y&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;purtiy&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de té verde&lt;/span&gt;&lt;span&gt;, Bvlgari&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Eau&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Vert&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;el&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;champú&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;es una expresión de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;elegancia&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y placer&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;personal.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Delicadamente perfumada&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Eau&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Parfumee&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;au&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;vert&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la acción de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;champú de limpieza&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;suave&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;lo hace perfecto&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;para el uso diario&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;/p&gt;\r\n'),
(71,1,'Pantene Pro-V Conditioner, Classic Care','','','&lt;p&gt;\r\n	Conditions hair for healthy shine. How Can You See Healthy Hair? Pantene Complete Therapy Conditioner has a unique pro-vitamin complex that deeply infuses every strand - So you see 6 signs of health hair: Shine; Softness; Strength; Body; Less Frizz; Silkiness. Pantene Complete Therapy Conditioner: The ultimate pro-vitamin therapy provides gentle daily nourishing moisture for enhanced shine; Helps hair detangle easily; Helps prevent frizz and flyaways. Simply use and in just 10 days - and very day after - see shiny hair that\'s soft with less frizz. Best of all, healthy Pantene hair that is strong and more resistant to damage. Made in USA.&lt;/p&gt;\r\n'),
(71,9,'Pantene Pro-V Conditioner, Cuidado clásico','','','&lt;p&gt;\r\n	Acondiciona el cabello para lograr un brillo saludable. ¿Cómo puede ver el pelo sano? Acondicionador Pantene terapia completa tiene un único pro-vitaminacomplejo que infunde profundamente todas las tendencias - Así que ya ves seissignos de la salud del cabello: brillo, suavidad, fuerza, cuerpo, menos frizz,sedoso. Acondicionador Pantene terapia completa: La mejor pro-vitamina terapia proporciona la humedad suave diaria de alimentación para mejorar brillo, ayuda adesenredar el cabello fácilmente, ayuda a prevenir el frizz y pelos sueltos. Sólo tiene que utilizar y en sólo 10 días - y después de muy al día - ver el pelo brillante, suave, con menos frizz. Lo mejor de todo, el cabello Pantene saludable que es fuerte y más resistente al daño. Fabricado en USA.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(72,1,'Brunette expressions Conditioner','','','&lt;p&gt;\r\n	YOUR HAIR’S STARTING POINT&lt;br /&gt;\r\n	Colour-Treated or Highlighted&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHAT&lt;br /&gt;\r\n	Pantene Pro-V COLOUR HAIR SOLUTIONS Brunette Expressions™ Conditioner hydrates hair for rich colour that is protected from daily stress and damage. This non-colour depositing formula works for all shades of brunette hair.&lt;/p&gt;\r\n&lt;p&gt;\r\n	WHY&lt;br /&gt;\r\n	Pantene uncovered that oxidative colouring changes hair’s surface. Internally, the chemistry in hair colour attacks strength-giving proteins in hair and makes the fibre more porous, leading to weaker hair and that fades more quickly. The surface of the hair fibres then becomes uneven and the protective layer of the hair fibre disintegrates. Without the protective layer, the hair fibre is prone to micro-scarring and damage, which changes the way it interacts with light and leads to a dull appearance.&lt;/p&gt;\r\n&lt;p&gt;\r\n	HOW&lt;br /&gt;\r\n	Pantene’s advanced Pro-Vitamin formula enhances brunette colour for great intensity and radiant shine. Non-colour depositing conditioning ingredients enhance and protect colour treated hair, while helping to restore shine to coloured strands. Hair is moisturized and infused with radiant shine.&lt;/p&gt;\r\n&lt;p&gt;\r\n	USE&lt;br /&gt;\r\n	For rich, vibrant colour that’s shiny and healthy-looking, use with Pantene Pro-V Pantene COLOUR HAIR SOLUTIONS Brunette Expressions™ Shampoo and Colour Nourishing Treatment.&lt;/p&gt;\r\n'),
(80,1,'Acqua Di Gio Pour Homme','','','&lt;p&gt;\r\n	A resolutely masculine fragrance born from the sea, the sun, the earth, and the breeze of a Mediterranean island. Transparent, aromatic, and woody in nature Aqua Di Gio Pour Homme is a contemporary expression of masculinity, in an aura of marine notes, fruits, herbs, and woods.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Notes:&lt;br /&gt;\r\n	Marine Notes, Mandarin, Bergamot, Neroli, Persimmon, Rosemary, Nasturtium, Jasmine, Amber, Patchouli, Cistus.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Transparent, modern, and masculine.&lt;/p&gt;\r\n'),
(80,9,'Acqua Di Gio Pour Homme','','','&lt;p&gt;\r\n	Una fragancia decidida masculino nace de la mar, el sol, la tierra, y la brisa de una isla del Mediterráneo. Transparente, aromático y amaderado en la naturaleza de Aqua Di Gio Pour Homme es una expresión contemporánea de la masculinidad, en un aura de notas marinas, frutas, hierbas y maderas.&lt;/p&gt;\r\n&lt;p&gt;\r\n	notas:&lt;br /&gt;\r\n	Notas marinas, mandarina, bergamota, neroli, caqui, romero, capuchina, jazmín, ámbar, pachulí, Cistus.&lt;br /&gt;\r\n	estilo:&lt;br /&gt;\r\n	Moderna transparente, y lo masculino.&lt;/p&gt;\r\n'),
(81,1,'Armani Eau de Toilette Spray ','','','&lt;p&gt;\r\n	This confidently masculine embodiment of the sophisticated ease and understated elegance of Giorgio Armani fashions - is a simply tailored, yet intensely sensual combination of sparkling fresh fruits, robust spices, and rich wood notes.&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Lime, Bergamot, Mandarin, Sweet Orange, Petitgrain, Cinnamon, Clove, Nutmeg, Jasmine, Neroli, Coriander, Lavender, Oakmoss, Sandalwood, Patchouli, Vetiver, Cedar.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Fresh, masculine, and discreet.&lt;/p&gt;\r\n'),
(81,9,'Armani Eau de Toilette Spray ','','','&lt;p&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Esta realización&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;confianza&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;masculina de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la facilidad&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;sofisticada y&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;sobria elegancia de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Giorgio&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Armani&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;moda&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;-&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;es una&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;simple&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;medida,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;sin embargo,&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;intensamente&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;sensual&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;combinación&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de brillantes&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;frutas frescas&lt;/span&gt;&lt;span&gt;, las especias&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;robusto,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y las notas&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;ricas&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de madera.&lt;/span&gt;&lt;br /&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;notas:&lt;/span&gt;&lt;br /&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Lima,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;bergamota, mandarina,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;naranja dulce&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Petitgrain&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;canela, clavo,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;nuez moscada&lt;/span&gt;&lt;span&gt;, jazmín, neroli&lt;/span&gt;&lt;span&gt;, cilantro,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;lavanda&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;musgo de roble,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;madera de sándalo&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;pachulí&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;vetiver, cedro&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;br /&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;estilo:&lt;/span&gt;&lt;br /&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Fresco&lt;/span&gt;&lt;span&gt;, masculino&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y discreto&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;/p&gt;\r\n'),
(82,1,'Armani Code after shave balm','','','&lt;p&gt;\r\n	Splash on this refreshing balm post-shave to soothe and calm the skin. Scents skin with a hint of seductive Code.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Ingredients&lt;br /&gt;\r\n	Alcohol, Aqua/Water/Eau, Parfum/Fragrance, PEG 8, PEG 60 Hydrogenated Castor Oil, BHT, Allantoin (Comfrey Root), Linalool, Geraniol, Alpha Isomethyl Ionone, Coumarin, Limonene, Hydroxyisohexl 3 Cyclohexene Carboxaldehyde, Hydroxycitronellal, Citronellol, Citral, Butylphenyl Methlyproprional, Hexylcinnamal&lt;/p&gt;\r\n'),
(82,9,'Armani Code after shave balm','','','&lt;p&gt;\r\n	Splash on this refreshing balm post-shave to soothe and calm the skin. Scents skin with a hint of seductive Code.&lt;span class=&quot;Apple-converted-space&quot;&gt; &lt;/span&gt;&lt;/p&gt;\r\n'),
(83,1,'Armani Code Sport','','','&lt;p&gt;\r\n	Sport. It\'s a rite of seduction. A vision of Giorgio Armani, translated into a fragrance.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	This scent opens with an explosive freshness that features spearmint, peppermint, and wild mint—surprising and unusual top notes with a stunning effect. The citrusy heart of the fragrance reveals Code Sport\'s seductive power. Notes of vetiver from Haiti reveal a woody and distinguished character, at once wet and dry. Like a crisp coating of ice, a note of hivernal prolongs the dialogue between the scent\'s cool crispness and sensual breath, giving the fragrance an almost unlimited life.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Spearmint, Peppermint, Wild Mint, Citrus, Hivernal, Hatian Vetiver, Nigerian Ginger.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Stunning. Cool. Seductive.&lt;/p&gt;\r\n'),
(83,9,'Armani Code Sport','','','&lt;p&gt;\r\n	Es un rito de la seducción. Una visión de Giorgio Armani, se tradujo en una fragancia.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Esta fragancia se abre con una frescura explosivo que cuenta con menta menta verde, y salvajes notas altas sorprendente e inusual de menta con un efecto impresionante. El corazón de la fragancia cítrica revela poder de seducciónCódigo Sport. Notas de vetiver de Haití revela un personaje de Woody y distinguido, a la vez húmeda y seca. Como una capa crujiente de hielo, una notade hivernal prolonga el diálogo entre el aroma fresco frescura y aire sensual, lo que la fragancia de una vida casi ilimitada.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	notas:&lt;br /&gt;\r\n	Hierbabuena, menta, menta silvestre, Citrus, Hivernal, haitiano vetiver, jengibrenigeriano.&lt;br /&gt;\r\n	estilo:&lt;br /&gt;\r\n	Impresionante. Cool. Seductora.&lt;/p&gt;\r\n'),
(84,1,'Armani Code Pour Femme','','','&lt;p&gt;\r\n	A seductive new fragrance for women, Armani Code Pour Femme is a fresh, sexy, feminine blend of zesty blood orange, ginger, and pear sorbet softened with hints of sambac jasmine, orange blossom, and lavender honey, warmed with precious woods and vanilla.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Blood Orange, Ginger, Pear Sorbet, Sambac Jasmine, Orange Blossom, Seringa Flower, Lavender Honey, Precious Woods Complex, Vanilla.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Fresh. Sexy. Feminine.&lt;/p&gt;\r\n'),
(84,9,'Armani Code Pour Femme','','','&lt;p&gt;\r\n	Una fragancia seductora para las mujeres, Armani Code Pour Femme es una fresca, mezcla de sexy y femenina de la sangre sabrosa naranja, jengibre, sorbete de pera y suavizado con toques de jazmín sambac, azahar, lavanda y miel, se calienta con maderas preciosas y la vainilla.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	notas:&lt;br /&gt;\r\n	La sangre de naranja, jengibre, sorbete de pera, jazmín Sambac, flor de naranjo, flor Seringa, miel de lavanda, maderas preciosas complejo, Vainilla.&lt;br /&gt;\r\n	estilo:&lt;br /&gt;\r\n	Fresco. Sexy. Femenino.&lt;/p&gt;\r\n'),
(85,1,'Forbidden euphoria Eau de Parfum Spray ','','','&lt;p&gt;\r\n	Possessing an innate confidence and sophistication, she is just starting to explore her sexuality. What she doesn\'t yet know is that she already is every man\'s fantasy.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A younger interpretation of Euphoria, this fruity floriental scent capitalizes on a modern, fresh sexiness with a mysterious twist. Its sparkling top notes seduce the senses with a blend of forbidden fruit such as mandarin, passion fruit, and iced raspberry. The heart blooms with a hypnotic bouquet of tiger orchid and jasmine. Underneath its exotic floralcy lies a layer of addictive patchouli and a sophisticated blend of musks and cashmere woods for an everlasting impression.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	Notes:&lt;br /&gt;\r\n	Sparkling Mandarin, Peach Blossom, Iced Raspberry, Pink Peony, Tiger Orchid, Jasmine, Cashmere Woods, Patchouli Absolute, Skin Musk.&lt;br /&gt;\r\n	Style:&lt;br /&gt;\r\n	Sophisticated. Confident. Forbidden.&lt;/p&gt;\r\n'),
(85,9,'Forbidden euphoria Eau de Parfum Spray ','','','&lt;p&gt;\r\n	Poseedora de una confianza innata y sofisticación, que apenas está comenzando a explorar su sexualidad. Lo que todavía no sabe es que ella ya es una fantasía de todo hombre.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Una interpretación más joven de Euphoria, este aroma afrutado floriental provecho de una sensualidad moderna, fresca con un toque misterioso. Sus notas de cabeza chispeantes seducir los sentidos con una mezcla de la fruta prohibida, tales como mandarina, maracuyá, frambuesa y helado. Las flores del corazón con un ramo de orquídeas hipnótico de tigre y el jazmín. Bajo su floralcy exóticos se encuentra una capa de adictivo pachulí y una sofisticada mezcla de almizcle y maderas de cachemira para una impresión inolvidable.&lt;/p&gt;\r\n&lt;p&gt;\r\n	notas:&lt;br /&gt;\r\n	Espumoso mandarín, flor de durazno, frambuesa helado, rosa peonía, Orquídea Tigre, jazmín, maderas de cachemira, Absolute pachulí, almizcle de piel.&lt;br /&gt;\r\n	estilo:&lt;br /&gt;\r\n	Sofisticados. Confianza. Prohibida.&lt;/p&gt;\r\n'),
(86,1,'Euphoria Men Intense Eau De Toilette Spray','','','&lt;p&gt;\r\n	Launched by the design house of Calvin Klein in 2008, EUPHORIA MEN INTENSE is a men\'s fragrance that possesses a blend of Rain Water, Pepper, Ginger, Sage, Frosted Sudachi, Cedar leaf, Patchouli, Myrrh, Labdanum, Amber Solid, Vetiver&lt;/p&gt;\r\n'),
(86,9,'Euphoria Men Intense Eau De Toilette Spray','','','&lt;p&gt;\r\n	Lanzado al mercado por la casa de diseño de Calvin Klein en 2008, Euphoria Men Intense es una fragancia masculina que posee una mezcla de agua de lluvia, pimienta, jengibre, salvia, Frosted Sudachi, hoja de cedro, pachulí, mirra, ládano, ámbar, vetiver&lt;/p&gt;\r\n'),
(87,1,'MAN Eau de Toilette Spray','','','&lt;p&gt;\r\n	Man by Calvin Klein was launched in October of 2007 and proposed as a new classic for the modern Calvin Klein man, aged from 25 to 40. The name itself is programmatic and unambiguous, like an English translation of L\'Homme by Yves Saint Laurent. Simple, brief, to the point. You are going to smell the essence of masculinity if you are to take your cue from the name of the fragrance. The packaging is sleek, modernist, with an architectural sense of proportions and looks good. The fragrance was created by perfumers Jacques Cavallier and Harry Fremont from Firmenich in collaboration with consultant Ann Gottlieb. All these people are old hands at marketing successful mainstream fragrances. Man offers therefore a mainstream palatability but without coming across as depersonalized. It plays the distinctiveness card, but in a well reined in manner. The fragrance bears a typical masculine fresh aromatic, woody and spicy signature around the linear heart of the scent which itself is dark, fruity, and sweet enough to feel feminine. This rich amber-fruity accord is made even more seductive thanks to just the right amount of citrus-y counterpoint, which never clarifies the scent but on the contrary helps to deepen the dark fruity sensation.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(87,9,'MAN Eau de Toilette Spray','','','&lt;p&gt;\r\n	El hombre de Calvin Klein se lanzó en octubre de 2007 y se propone como un nuevo clásico para el hombre moderno Calvin Klein, con edades de 25 a 40. El nombre en sí es programática y sin ambigüedades, como una traducción al Inglés de L\'Homme de Yves Saint Laurent. Sencillo y breve, al grano. Usted va a oler la esencia de la masculinidad si va a tomar su punto de partida el nombre de la fragancia. El envase es elegante, moderno, con un sentido arquitectónico de proporciones y se ve bien. La fragancia fue creada por los perfumistas Jacques Cavallier y Harry Fremont de Firmenich en colaboración con el consultor de Ann Gottlieb. Todas estas personas son veteranos en la comercialización de fragancias éxito general. El hombre ofrece por lo tanto un buen sabor de masas, pero sin aparentar ser despersonalizado. Se juega la carta de carácter distintivo, pero de una manera bien frenó. La fragancia tiene una firma normal masculino fresco aromático, amaderado y picante alrededor del corazón lineal de la esencia que en sí es oscuro, afrutado y dulce suficiente para sentirse femenina. Este rico color ámbar con sabor a fruta acuerdo se hace aún más atractivo gracias a la cantidad justa de contrapunto y de cítricos, que nunca se aclara el olor sino por el contrario ayuda a profundizar la sensación de sabor a fruta oscura.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(88,1,'ck one Summer 3.4 oz','','','&lt;p&gt;\r\n	It\'s a concert on a hot summer night. The stage is set and the show\'s about to start. Feel the breeze, catch the vibe, and move to the beat with the pulsating energy of this limited-edition fragrance. A unisex scent, it is fresh, clean, and easy to wear. The fragrance opens with a burst of crisp melon. In the heart notes, an invigorating blend of green citrus and the zesty herbaceous effect of verbena creates a cool, edgy freshness. A base of exotic incense and earthy oakmoss is wrapped in the light, sensuous warmth of cedarwood, musk, and peach skin. Notes:Tangerine, Water Fern, Melon, Lemon, Sea Breeze Accord, Blue Freesia, Verbena, Rhubarb, Cedarwood, Skin Musk, Incense, Peach Skin. Style:Invigorating. Crisp. Cool.&lt;/p&gt;\r\n'),
(88,9,'ck one Summer 3.4 oz','','','&lt;p&gt;\r\n	Es un concierto en una noche de verano. El escenario está listo y el espectáculo está a punto de comenzar. Sienta la brisa, la captura el ambiente, y se mueven al ritmo con la energía pulsante de esta fragancia de edición limitada. Un perfume unisex, es fresco, limpio, y fácil de llevar. La fragancia se abre con una explosión de melón fresco. En las notas de corazón, una mezcla estimulante de cítricos verdes y el efecto herbáceas zesty de verbena crea una frescura y atrevido. Una base de incienso y musgo de roble exótica tierra está envuelta en la luz, calor sensual de madera de cedro, almizcle y piel de melocotón. Notas: mandarina, melón de agua Fern, limón, Acuerdo de la brisa marina, azul Freesia, Verbena, ruibarbo, madera de cedro, almizcle de piel, incienso, piel de melocotón. Estilo: vigorizante. Crisp. Cool.&lt;/p&gt;\r\n'),
(89,1,'Secret Obsession Perfume','','','&lt;p&gt;\r\n	Calvin Klein Secret Obsession eau de parfum spray for women blends notes of forbidden fruits, exotic flowers and a sultry wood signature to create an intoxicating aroma that is provocative and addictive.Calvin Klein is one of World of Shops most popular brands, and this Calvin Klein Secret Obsession eau de parfum spray for women is a firm favourite amongst our customers for its deep, feminine aroma that is perfect for those special evenings out.&lt;/p&gt;\r\n'),
(89,9,'Secret Obsession Perfume','','','&lt;p&gt;\r\n	Calvin Klein secreta obsesión eau de parfum spray para las mujeres combina notas de frutas prohibidas, flores exóticas y una firma de madera sensual para crear un aroma embriagador que es provocativo y addictive.Calvin Klein es una de las Tiendas del Mundo de las marcas más populares, y esto Calvin Kleinsecreta obsesión eau de parfum spray para las mujeres es uno de los favoritosentre nuestros clientes por su aroma profundo, lo femenino que es perfecto para esas noches especiales hacia fuera.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(90,1,'Obsession Night Perfume','','','&lt;p&gt;\r\n	Launched by the design house of Calvin Klein in 2005, OBSESSION NIGHT is a women\'s fragrance that possesses a blend of gardenia, tonka bean, bergamot, vanilla, sandalwood, jasmine, rose, amber, muguet and mandarin. It is recommended for evening wear.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Ingredients&lt;br /&gt;\r\n	Notes: Bergamot, Bitter Orange, Mandarin, White Floral, Angelica Root, Gardenia, Rose, Muguet, Night-Blooming Jasmine, Vanilla, Tonka Bean, Amber, Labdanum, Sandalwood, Cashmere Wood&lt;/p&gt;\r\n'),
(90,9,'Obsession Night Perfume','','','&lt;p&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Lanzado al mercado por&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la casa de diseño&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Calvin&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Klein&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;en 2005&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Obsession Night&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;es&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;una fragancia para&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;la mujer&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;que&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;posee una mezcla de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;gardenia,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;haba tonka&lt;/span&gt;&lt;span&gt;, bergamota&lt;/span&gt;&lt;span&gt;, vainilla&lt;/span&gt;&lt;span&gt;, sándalo,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;jazmín, rosa,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;ámbar,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;muguet&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;y mandarina&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Se recomienda&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;para la noche&lt;/span&gt;&lt;span&gt;.&lt;/span&gt;&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;ingredientes&lt;/span&gt;&lt;br /&gt;\r\n	&lt;span class=&quot;hps&quot;&gt;Notas:&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Bergamota&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;naranja amarga&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;mandarina,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;flores&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;blancas&lt;/span&gt;&lt;span&gt;, raíz de angélica&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Gardenia&lt;/span&gt;&lt;span&gt;, Rosa,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;Muguet&lt;/span&gt;&lt;span&gt;, Noche de&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;jazmín&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;vainilla&lt;/span&gt;&lt;span&gt;, haba tonka&lt;/span&gt;&lt;span&gt;, ámbar,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;ládano&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;madera de sándalo&lt;/span&gt;&lt;span&gt;,&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;madera&lt;/span&gt;&lt;span class=&quot;Apple-converted-space&quot;&gt;&amp;nbsp;&lt;/span&gt;&lt;span class=&quot;hps&quot;&gt;de cachemira&lt;/span&gt;&lt;/p&gt;\r\n'),
(91,1,'Jasmin Noir Body Lotion 6.8 fl oz','','','&lt;p&gt;\r\n	A bath collection for the body, scented with the Jasmin Noir fragrance. A tribute to ultimate femininity. Seduction and personal indulgence.&lt;br /&gt;\r\n	Body Lotion Fragrance: The new emblematic creation within the Bvlgari Pour Femme Collection Jasmin Noir, perfectly embodies the luxury and prestige of Bvlgari fine jewelry.&lt;br /&gt;\r\n	Jasmin Noir is a flower of the imagination. Precious jasmine, white and immaculate, in its noire interpretation. A flower of pure mystery. A rich and delicate flower that at nightfall, reveals its intriguing sensuality. A precious floral woody fragrance with ambery accents centered around one of the true jewels of perfumery: the jasmin flower. A scent that conjures forth the bewildering seductiveness of feminity as elegant as it is profoundly sensual.&lt;br /&gt;\r\n	Jasmin Noir tells a voluptuous floral story that begins with the pure radiance of luminous light given by green and scintillating notes: Vegetal Sap and fresh Gardenia Petals. Then, tender and seductive, the Sambac Jasmine Absolute, delivers its generous and bewitching notes. Unexpectedly allied with a transparent silky almond accord, it reveals a heart that is light yet thoroughly exhilarating and marvelously addictive. The scent\'s sumptuously rich notes repose on a bed of Precious Wood and ambery undertones, bringing together the depth and mystery of Patchouli, the warmth of Tonka Bean and the comfort of silky Musks for an elegant and intimate sensuality.&lt;br /&gt;\r\n	An exquisite fragrance of incomparable prestige, Jasmin Noir captures the very essence of the jeweler.&lt;br /&gt;\r\n	Made in Italy&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(91,9,'Jasmin Noir Body Lotion 6.8 fl oz','','','&lt;p&gt;\r\n	Una colección de baño para el cuerpo, perfumado con la fragancia Noir Jasmin. Un homenaje a la feminidad definitiva. La seducción y placer personal.&lt;br /&gt;\r\n	Perfume Body Lotion: La nueva creación emblemática dentro de la colección Bvlgari Pour Femme Jasmin Noir, encarna a la perfección el lujo y el prestigio de la joyería Bvlgari bien.&lt;br /&gt;\r\n	Jasmin Noir es una flor de la imaginación. Jazmín preciosas, blanca e inmaculada, en su interpretación noire. Una flor de misterio puro. Una flor rico y delicado, que al caer la noche, revela su sensualidad fascinante. Una fragancia floral preciosa madera con detalles en ambarino en torno a una de las verdaderas joyas de la perfumería: la flor de jazmín. Un aroma que evoca sucesivamente la seducción desconcertantes de la feminidad tan elegante como es profundamente sensual.&lt;br /&gt;\r\n	Jasmin Noir cuenta una historia voluptuosa floral que comienza con el puro resplandor de la luz luminosa dada por notas verdes y brillante: Vegetal Sap y pétalos frescos Gardenia. Entonces, tierna y seductora, el absoluto de jazmín Sambac, entrega sus notas generoso y encantador. Inesperadamente, aliado con un acuerdo transparente y sedoso de almendra, que revela un corazón que es ligero pero excitante y adictivo maravillosamente bien. El olor de reposo suntuosamente rica notas sobre un lecho de maderas preciosas y matices ambarino, que reúne a la profundidad y el misterio del pachulí, el calor de la Haba Tonka y la comodidad de almizcle suave de una sensualidad elegante e íntimo.&lt;br /&gt;\r\n	Una exquisita fragancia de prestigio incomparable, Jasmin Noir captura la esencia misma de la joyería.&lt;br /&gt;\r\n	Made in Italy&lt;/p&gt;\r\n'),
(92,1,'Body Cream by Bulgari','','','&lt;p&gt;\r\n	BVLGARI (Bulgari) by Bvlgari Body Cream 6.7 oz for Women Launched by the design house of Bvlgari in 1994, BVLGARI is classified as a refined, floral fragrance. This feminine scent possesses a blend of violet, orange blossom, and jasmine. Common spellings: Bulgari, Bvlgary, Bulgary.&lt;/p&gt;\r\n'),
(92,9,'Body Cream by Bulgari','','','&lt;p&gt;\r\n	BVLGARI (Bulgari) por el Consejo de Bvlgari Crema 6.7 oz para la Mujer puso en marcha por la casa de diseño de Bvlgari en 1994, BVLGARI está clasificada como una fragancia refinada, floral. Este aroma femenino posee una mezcla devioleta, flor de azahar y jazmín. Ortografía comunes: Bulgari, Bvlgary, Bulgaria.&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(93,1,'Creme Precieuse Nuit 50ml','','','&lt;p&gt;\r\n	A luxurious, melting night cream to repair skin during sleep Features Polypeptides that boost production of collagen &amp;amp; elastin Improves skin elasticity &amp;amp; firmness Visibly reduces appearance of wrinkles, fine lines &amp;amp; brown spots Enriched with Bvlgari Gem Essence to restore radiance Skin appears smooth, energized &amp;amp; luminous in morning Perfect for all skin types&lt;/p&gt;\r\n'),
(93,9,'Creme Precieuse Nuit 50ml','','','&lt;p&gt;\r\n	Una crema lujosa, la noche de fusión para reparar la piel durante el sueñoCaracterísticas polipéptidos que estimulan la producción de colágeno y elastinaMejora la elasticidad de la piel y la firmeza Reduce visiblemente la apariencia de las arrugas, líneas finas y manchas de color marrón Enriquecido con BvlgariEsencia gema para restaurar la piel aparece radiante suave, lleno de energía yluminosa en la mañana Perfecto para todo tipo de piel&lt;br /&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(94,1,'Night Care Crema Nera Obsidian Mineral Complex','','','&lt;p&gt;\r\n	When it comes to body, skin or eye care, you want to look to our products and you will find the best there is. These are the most exceptional personal care products available. They meet the strictest standards for quality sourcing, environmental impact, results and safety. Our body care products truly allows you to be good to your whole body.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Giorgio Armani - Cream Nera - Night Care Crema Nera Obsidian Mineral Complex3 Restoring Cream SPF 15 50ml/1.69oz A lavish, fresh &amp;amp; weightless anti-aging creamProvides shielding &amp;amp; moisturizing benefitsDeveloped with Obsidian Mineral Complex technology Formulated with iron, silicon &amp;amp; perlite to create a potent dermal restructuring system Contains Pro-XylaneTM &amp;amp; Hyaluronique Acid Targets loss of substance, sagging of features &amp;amp; deepening of wrinkles Reveals firmer, sleeker &amp;amp; plumper skin in a youthful look. With a fabulous Skincare product like this one, you\'ll be sure to enjoy the ultimate in a Skincare experience with promising results.&lt;/p&gt;\r\n'),
(94,9,'Night Care Crema Nera Obsidian Mineral Complex','','','&lt;p&gt;\r\n	Cuando se trata de cuidado del cuerpo, la piel o los ojos, quieres mirar a nuestros productos y se encuentra mejor que hay. Estos son los productos más excepcionales de atención personal disponible. Cumplen los más estrictos estándares de calidad de abastecimiento, el impacto ambiental, los resultados y la seguridad. Nuestros productos para el cuidado del cuerpo realmente le permite ser bueno para todo el cuerpo.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Giorgio Armani - Crema Nera - Cuidado Noche Crema Nera Obsidiana mineral Complex3 Restauración Cream SPF 15 50ml/1.69oz A creamProvides lujoso, fresco y sin peso anti-envejecimiento de la protección y la hidratación benefitsDeveloped con la tecnología de Mineral de Obsidiana Complejo Formulado con hierro, silicio y perlita para crear un potente sistema de reestructuración dérmica Contiene Pro-XylaneTM y Acido Hyaluronique objetivos pérdida de sustancia, la flacidez de las características y la profundización de las arrugas revela una piel más firme, más liso y más gordo en un aspecto juvenil. Cuidado de la piel con un producto fabuloso como este, usted estará seguro de disfrutar de lo último en una experiencia de cuidado de la piel, con resultados prometedores.&lt;/p&gt;\r\n'),
(95,1,'Skin Minerals For Men Cleansing Cream','','','&lt;p&gt;\r\n	Ultra-purifying skincare enriched with essential moisturizing minerals, designed to instantly moisturize / purify the skin.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Sodium and Potassium System: to preserve cutaneous hydration.&lt;br /&gt;\r\n	- Salicylic Acid and Hamamelis Extract: to tighten the pores and tone skin.&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Self-assessment*:&lt;br /&gt;\r\n	- leaves the skin clean 100%&lt;br /&gt;\r\n	- leaves the skin comfortable 93%&lt;br /&gt;\r\n	- leaves the skin smooth 95%&lt;br /&gt;\r\n	- skin complexion is uniform 89%&lt;br /&gt;\r\n	- skin texture is refined 80%&lt;br /&gt;\r\n	* use test: 60 men 20 -65 years old 4 weeks of self-assessment&lt;/p&gt;\r\n'),
(95,9,'Skin Minerals For Men Cleansing Cream','','','&lt;p&gt;\r\n	Ultra-purificación de cuidado de la piel enriquecidos con minerales esenciales hidratantes, diseñado para hidratar la piel al instante / purificar la piel.&lt;br /&gt;\r\n	Ingredientes activos&lt;br /&gt;\r\n	- Sodio y potasio del sistema: para preservar la hidratación cutánea.&lt;br /&gt;\r\n	- Ácido salicílico y Hamamelis Extracto: para apretar los poros y tonificar la piel.&lt;br /&gt;\r\n	Prestaciones superiores: las cifras.&lt;br /&gt;\r\n	Autoevaluación *:&lt;br /&gt;\r\n	- Deja la piel limpia al 100%&lt;br /&gt;\r\n	- Deja la piel confortable 93%&lt;br /&gt;\r\n	- Deja la piel suave 95%&lt;br /&gt;\r\n	- Flexibilidad de la piel es uniforme en un 89%&lt;br /&gt;\r\n	- Textura de la piel se refina el 80%&lt;br /&gt;\r\n	* Test de uso: 60 hombres de 20 -65 años de edad de 4 semanas de auto-evaluación&lt;/p&gt;\r\n'),
(96,1,'Eye master','','','&lt;p&gt;\r\n	The volcanic force of minerals concentrated in multi action skincare specifically designed to target wrinkles, bags and dark circles of the delicate eye area. To combat signs of aging and fatigue and visibly improve skin quality.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Volcanic Complex: an innovative combination of energy charged minerals, inspired by volcanic rocks&lt;br /&gt;\r\n	- Caffeine extract: to fight puffiness&lt;br /&gt;\r\n	- Conker and butcher’s broom extracts to stimulate cutaneous blood micro-circulation&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Proven immediate anti-puffiness action*:&lt;br /&gt;\r\n	- 15 minutes after application –19%&lt;br /&gt;\r\n	*instrumental test, 40 men, 42-65 years old&lt;br /&gt;\r\n	Self-assessment&lt;br /&gt;\r\n	- instantly revitalizes skin 77%**&lt;br /&gt;\r\n	- wrinkles appear reduced 78%***&lt;br /&gt;\r\n	- diminishes the appearance of dark circles 68%***&lt;br /&gt;\r\n	** use test, 40 men 42-65 years old, single application, self-assessment&lt;br /&gt;\r\n	*** use test, 40 men 42-65 years old, 4 weeks, self-assessment&lt;/p&gt;\r\n'),
(96,9,'Eye master','','','&lt;p&gt;\r\n	La fuerza volcánica de los minerales concentrados en cuidado de la piel la acción de múltiples diseñadas específicamente para las arrugas objetivo, bolsas y ojeras del contorno de los ojos. Para combatir los signos del envejecimiento y la fatiga y mejorar visiblemente la calidad de la piel.&lt;br /&gt;\r\n	Ingredientes activos&lt;br /&gt;\r\n	- Complejo Volcánico: una innovadora combinación de minerales energía cargada, inspirada en las rocas volcánicas&lt;br /&gt;\r\n	- Extracto de la cafeína: para luchar contra las bolsas&lt;br /&gt;\r\n	- Conker y rusco extrae la sangre para estimular la microcirculación cutánea&lt;br /&gt;\r\n	Prestaciones superiores: las cifras.&lt;br /&gt;\r\n	Demostrado inmediata contra las bolsas de acción *:&lt;br /&gt;\r\n	- 15 minutos después de la aplicación -19%&lt;br /&gt;\r\n	* prueba instrumental, 40 hombres, 42 a 65 años de edad&lt;br /&gt;\r\n	Auto-evaluación&lt;br /&gt;\r\n	- Revitaliza la piel al instante del 77% **&lt;br /&gt;\r\n	- Reducción de las arrugas aparecen *** 78%&lt;br /&gt;\r\n	- Disminuye la apariencia de las ojeras 68% ***&lt;br /&gt;\r\n	** Prueba de uso, 40 hombres 42-65 años de edad, una sola aplicación, la auto-evaluación&lt;br /&gt;\r\n	*** Prueba de uso, 40 hombres 42-65 años, 4 semanas, la auto-evaluación&lt;/p&gt;\r\n'),
(97,1,'Eye Rejuvenating Serum','','','&lt;p&gt;\r\n	The first advanced rejuvenating ‘weapon’ thanks to a corrective and smoothing texture and a power amplifying applicator.&lt;br /&gt;\r\n	The alliance of the [3.R] technology combined with an intensive re-smoothing system.&lt;br /&gt;\r\n	The eye rejuvenation serum also comes in an easily portable tube that boasts a silver bevelled applicator to ensure a good delivery of the product to the eye area as well as offering a means to improve circulation and reduce puffiness and eye bags.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Rarely women have been so convinced of its efficiency on skin rejuvenation:&lt;/p&gt;\r\n&lt;p&gt;\r\n	EYE CONTOUR LOOKS SMOOTHER 85%*&lt;br /&gt;\r\n	EYES LOOK YOUNGER 91%*&lt;br /&gt;\r\n	EYE PUFFINESS LOOKS SOFTENED 83%*&lt;/p&gt;\r\n&lt;p&gt;\r\n	*% of women – self assessment on 60 women after 4 weeks&lt;/p&gt;\r\n'),
(97,9,'Eye Rejuvenating Serum','','','&lt;p&gt;\r\n	La primera avanzada de rejuvenecimiento \'arma\' gracias a una textura correctivas y alisado y un aplicador de potencia de amplificación.&lt;br /&gt;\r\n	La alianza de los [3.R] tecnología, combinada con una intensa re-alisado del sistema.&lt;br /&gt;\r\n	El suero de rejuvenecimiento de los ojos también viene en un tubo fácil de transportar que cuenta con una de plata aplicador biselado para asegurar una buena entrega del producto a la zona de los ojos, así como ofrecer un medio para mejorar la circulación y reducir la hinchazón y las bolsas de los ojos.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Rara vez las mujeres han estado tan convencido de su eficacia en el rejuvenecimiento de la piel:&lt;/p&gt;\r\n&lt;p&gt;\r\n	CONTORNO DE OJOS se ve más suave * 85%&lt;br /&gt;\r\n	OJOS lucir más jóvenes * 91%&lt;br /&gt;\r\n	Hinchazón de los ojos PARECE * ABLANDADA 83%&lt;/p&gt;\r\n&lt;p&gt;\r\n	*% De las mujeres - auto-evaluación sobre 60 mujeres después de 4 semanas&lt;/p&gt;\r\n'),
(98,1,'Shaving cream','','','&lt;p&gt;\r\n	Moisturizing, charged with minerals and enriched with ultra softening agents. Its specific formula ensures an optimal, extremely gentle shave. Even four hours after shaving, the skin remains hydrated, soft and supple.&lt;br /&gt;\r\n	Active Ingredients&lt;br /&gt;\r\n	- Sodium and Potassium System: to preserve cutaneous hydration&lt;br /&gt;\r\n	- Bisabolol: to soothe skin&lt;br /&gt;\r\n	High Performances: the figures.&lt;br /&gt;\r\n	Measurements 4 hours after shaving:&lt;br /&gt;\r\n	- skin hydration +29%*&lt;br /&gt;\r\n	- skin softness +61%**&lt;br /&gt;\r\n	- skin suppleness +18%**&lt;br /&gt;\r\n	- skin dryness -39%**&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;br /&gt;\r\n	* instrumental test, 20 men, 20-70 years old&lt;br /&gt;\r\n	** clinical scorage, 20 men, 20-70 years old&lt;/p&gt;\r\n'),
(98,9,'Shaving cream','','','&lt;p&gt;\r\n	Hidratantes, acusado de minerales y enriquecida con ultra agentes suavizantes. Su fórmula específica se asegura una óptima y extremadamente suave afeitada. Hasta cuatro horas después del afeitado, la piel queda hidratada, suave y flexible.&lt;br /&gt;\r\n	Ingredientes activos&lt;br /&gt;\r\n	- Sodio y potasio del sistema: para preservar la hidratación cutánea&lt;br /&gt;\r\n	- Bisabolol: para calmar la piel&lt;br /&gt;\r\n	Prestaciones superiores: las cifras.&lt;br /&gt;\r\n	Mediciones de 4 horas después del afeitado:&lt;br /&gt;\r\n	- La piel hidratación * 29%&lt;br /&gt;\r\n	- La piel suavidad 61% **&lt;br /&gt;\r\n	- La piel elasticidad 18% **&lt;br /&gt;\r\n	- Piel de la sequedad -39% **&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;br /&gt;\r\n	* Prueba instrumental, 20 hombres, 20 a 70 años de edad&lt;br /&gt;\r\n	** Clínica scorage, 20 hombres, 20 a 70 años de edad&lt;/p&gt;\r\n'),
(99,1,'Fluid shine nail polish','','','&lt;p&gt;\r\n	Luxurious color at your fingertips. Fluid shine coats nails with intense shine and long-lasting, sophisticated color. The essential accessory to any makeup wardrobe.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Discover the Bronze collection 2010&lt;br /&gt;\r\n	Finish this season’s high summer look with a cranberry n°43 or blackberry n°44 nail, to echo the wet lips with intense color.&lt;/p&gt;\r\n'),
(99,9,'Fluid shine nail polish','','','&lt;p&gt;\r\n	Color de lujo a su alcance. Líquido brillo capas uñas con un brillo intenso y de larga duración, color sofisticados. El accesorio esencial para cualquier armario de maquillaje.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Descubra la colección de Bronce 2010&lt;br /&gt;\r\n	Finalizar aspecto de esta temporada alta de verano con un arándano n ° 43 o n ° 44 blackberry uñas, hacerse eco de los labios húmedos con un color intenso.&lt;/p&gt;\r\n'),
(100,1,'Smooth silk lip pencils','','','&lt;p&gt;\r\n	An incredibly soft lip pencil for subtle, precise definition. The silky texture allows for easy application and flawless results. To extend the hold of your lip color, fill lips in completely with Smooth silk lip pencil before applying your lipstick. Choose from a wide range of shades to complement every color in your lipstick wardrobe.&lt;/p&gt;\r\n'),
(100,9,'Smooth silk lip pencils','','','&lt;p&gt;\r\n	Un labio increíblemente suave lápiz para la definición sutil y preciso. La textura sedosa permite una fácil aplicación y resultados impecables. Para extender el dominio de su color de labios, rellenar los labios por completo con un lápiz de labios de seda suave antes de aplicar el lápiz labial. Elija entre una amplia gama de tonos para complementar todos los colores en su guardarropa lápiz labial.&lt;/p&gt;\r\n'),
(101,1,'Pro-V Color Hair Solutions Color Preserve Shine Conditioner','pantene, shampoo','','&lt;p&gt;\r\n	PANTENE\'s color preserve shine shampoo and conditioner system with micro-polishers smoothes and refinishes the hair’s outer layer. So your hair reflects light and shines brilliantly. Help preserve your multi-dimensional color.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Product Features&lt;/strong&gt;&lt;br /&gt;\r\n	Micro-polishers smooth the outer layer of hair to help Protect color and leave hair shiny&lt;br /&gt;\r\n	Lightweight moisturizers provide protection against damage&lt;br /&gt;\r\n	Designed for color-treated hair; Gentle enough for permed hair&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Ingredients&lt;/strong&gt;&lt;br /&gt;\r\n	Water, Stearyl Alcohol, Behentrimonium Methosulfate, Cetyl Alcohol, Fragrance, Bis-Aminopropyl Dimethicone, Isopropyl Alcohol, Benzyl Alcohol, Disodium EDTA, Panthenol, Panthenyl Ethyl Ether, Methylchloroisothiazolinone, Methylisothiazolinone&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(101,9,'Pro-V Color Hair Solutions Color Preserve Shine Conditioner with Pump','pantene, shampoo','','&lt;p&gt;\r\n	PANTENE\'s color preserve shine shampoo and conditioner system with micro-polishers smoothes and refinishes the hair’s outer layer. So your hair reflects light and shines brilliantly. Help preserve your multi-dimensional color.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Product Features&lt;/strong&gt;&lt;br /&gt;\r\n	Micro-polishers smooth the outer layer of hair to help Protect color and leave hair shiny&lt;br /&gt;\r\n	Lightweight moisturizers provide protection against damage&lt;br /&gt;\r\n	Designed for color-treated hair; Gentle enough for permed hair&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Ingredients&lt;/strong&gt;&lt;br /&gt;\r\n	Water, Stearyl Alcohol, Behentrimonium Methosulfate, Cetyl Alcohol, Fragrance, Bis-Aminopropyl Dimethicone, Isopropyl Alcohol, Benzyl Alcohol, Disodium EDTA, Panthenol, Panthenyl Ethyl Ether, Methylchloroisothiazolinone, Methylisothiazolinone&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n'),
(102,1,'Gucci Guilty','gicci, spray','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		Notes Consist Of Mandarin, Pink Pepper, Peach, Lilac, Geranium, Amber And Patchouli&lt;/li&gt;\r\n	&lt;li&gt;\r\n		For Casual Use&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;Gucci Guilty&lt;/em&gt; is a warm yet striking oriental floral with hedonism at its heart.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The scent seizes the attention with a flamboyant opening born of the natural rush that is mandarin shimmering alongside an audacious fist of pink pepper.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The middle notes are an alluring concoction of heady lilac and geranium, laced with the succulent tactility of peach - all velvet femininity with a beguiling hint of provocation.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The patchouli that is the hallmark of Gucci fragrances here conveys a message of strength, while the voluptuousness of amber suggests deep femininity.&lt;/p&gt;\r\n'),
(102,9,'Gucci Guilty','gicci, spray','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		Notes Consist Of Mandarin, Pink Pepper, Peach, Lilac, Geranium, Amber And Patchouli&lt;/li&gt;\r\n	&lt;li&gt;\r\n		For Casual Use&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;Gucci Guilty&lt;/em&gt; is a warm yet striking oriental floral with hedonism at its heart.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The scent seizes the attention with a flamboyant opening born of the natural rush that is mandarin shimmering alongside an audacious fist of pink pepper.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The middle notes are an alluring concoction of heady lilac and geranium, laced with the succulent tactility of peach - all velvet femininity with a beguiling hint of provocation.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The patchouli that is the hallmark of Gucci fragrances here conveys a message of strength, while the voluptuousness of amber suggests deep femininity.&lt;/p&gt;\r\n'),
(103,1,'Jasmin Noir L\'Essence Eau de Parfum Spray 75ml','','','&lt;p&gt;\r\n	A carnal impression of the immaculate jasmine flower, Bvlgari Jasmin Noir L\'Essence dresses the purity of the bloom in jet black mystery.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The fragrance is a more concentrated Eau de Parfum than the original Jasmin Noir, a blend of rare and precious ingredients that are more seductive, and more addictive than ever before. The profoundly sensual elixir captivates the senses, and enchants its wearer with its generous and bewitching touches.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A luminous bottle that honours the heritage of Bvlgari.&lt;/p&gt;\r\n'),
(103,9,'Jasmin Noir L\'Essence Eau de Parfum Spray ','','','&lt;p&gt;\r\n	A carnal impression of the immaculate jasmine flower, Bvlgari Jasmin Noir L\'Essence dresses the purity of the bloom in jet black mystery.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The fragrance is a more concentrated Eau de Parfum than the original Jasmin Noir, a blend of rare and precious ingredients that are more seductive, and more addictive than ever before. The profoundly sensual elixir captivates the senses, and enchants its wearer with its generous and bewitching touches.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A luminous bottle that honours the heritage of Bvlgari.&lt;/p&gt;\r\n'),
(104,1,'Calvin Klein Obsession For Women EDP Spray','','','&lt;p&gt;\r\n	Citrus, vanilla and greens lowering to notes of sandalwood, spices and musk. Recommended Use daytime&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;\'Obsession\'&lt;/em&gt; perfume was launched by the design house of Calvin Klein in 1985&lt;/p&gt;\r\n&lt;p&gt;\r\n	When you think about Calvin Klein, initially you think of his clothing line – specifically his jeans and underwear lines (not to mention the famous ad with a young Brooke Shields). But Calvin Klein’s penchant for perfume was equally as cutting edge as his foray into fashion.&lt;/p&gt;\r\n'),
(104,9,'Calvin Klein Obsession For Women EDP Spray','','','&lt;p&gt;\r\n	Citrus, vanilla and greens lowering to notes of sandalwood, spices and musk. Recommended Use daytime&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;\'Obsession\'&lt;/em&gt; perfume was launched by the design house of Calvin Klein in 1985&lt;/p&gt;\r\n&lt;p&gt;\r\n	When you think about Calvin Klein, initially you think of his clothing line – specifically his jeans and underwear lines (not to mention the famous ad with a young Brooke Shields). But Calvin Klein’s penchant for perfume was equally as cutting edge as his foray into fashion.&lt;/p&gt;\r\n'),
(105,1,'Bvlgari Aqua','','','&lt;p&gt;\r\n	Bvlgari Aqua (Pour Homme) Eau De Toilette Spray is an enticing and fresh cologne that exudes masculinity from its unique blend of amber santolina, posidonia and mandarin.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Bvlgari Aqua (Pour Homme) Eau De Toilette Spray scent lasts throughout the day without having an overpowering smell. It is subtle enough for daytime use and masculine enough for night wear.&lt;/p&gt;\r\n'),
(105,9,'Bvlgari Aqua Eau De Toilette Spray','','','&lt;p&gt;\r\n	Bvlgari Aqua (Pour Homme) Eau De Toilette Spray is an enticing and fresh cologne that exudes masculinity from its unique blend of amber santolina, posidonia and mandarin.&lt;/p&gt;\r\n&lt;p&gt;\r\n	Bvlgari Aqua (Pour Homme) Eau De Toilette Spray scent lasts throughout the day without having an overpowering smell. It is subtle enough for daytime use and masculine enough for night wear.&lt;/p&gt;\r\n'),
(106,1,'Omnia Eau de Toilette 65ml','bvlgary, omnia, EDT','','&lt;p&gt;\r\n	Choose Your scent&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Coral:&lt;/strong&gt; Inspired by the shimmering hues of precious red coral, Omnia Coral is a radiant floral-fruity Eau de Toilette of tropical Hibiscus and juicy Pomegranate, reminiscent of Summer, the sun, resplendent nature and far-off oceans.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Amethyst:&lt;/strong&gt; Inspired by the shimmering hues of the amethyst gemstone, this floral Eau de Toilette captures the myriad scents of Iris and Rose gardens caressed with morning dew.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Crystalline:&lt;/strong&gt; Created from the glowing clarity and purity of crystal, Omnia Crystalline is a sparkling jewel of light, illuminating and reflecting the gentle sensuality and luminous femininity. Sparkling like a precious jewel, like the rarest of crystals, in an exquisite jewel flacon.&lt;/p&gt;\r\n'),
(106,9,'Omnia Eau de Toilette 65ml','','','&lt;p&gt;\r\n	Choose Your scent&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Coral:&lt;/strong&gt; Inspired by the shimmering hues of precious red coral, Omnia Coral is a radiant floral-fruity Eau de Toilette of tropical Hibiscus and juicy Pomegranate, reminiscent of Summer, the sun, resplendent nature and far-off oceans.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Amethyst:&lt;/strong&gt; Inspired by the shimmering hues of the amethyst gemstone, this floral Eau de Toilette captures the myriad scents of Iris and Rose gardens caressed with morning dew.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;strong&gt;Omnia Crystalline&lt;/strong&gt;: Created from the glowing clarity and purity of crystal, Omnia Crystalline is a sparkling jewel of light, illuminating and reflecting the gentle sensuality and luminous femininity. Sparkling like a precious jewel, like the rarest of crystals, in an exquisite jewel flacon.&lt;/p&gt;\r\n'),
(107,1,'Lancome Slimissime 360 Slimming Activating Concentrate Unisex Treatment','','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		An anti-cellulite body treatment&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Features a special gel-cream texture &amp;amp; a quick-dissolving formula&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Formulated with an exclusive 360 Complex&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	An anti-cellulite body treatment. Features a special gel-cream texture &amp;amp; a quick-dissolving formula. Formulated with an exclusive 360 Complex. Helps combat presence of cellulite &amp;amp; reduce existing cellulite. Provides immediate invigorating &amp;amp; firming results. Concentrated with micro-pearl particles to illuminate skin. Creates svelte &amp;amp; re-sculpted body contours....&lt;/p&gt;\r\n'),
(107,9,'Lancome Slimissime 360 Slimming Activating Concentrate Unisex Treatment','','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		An anti-cellulite body treatment&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Features a special gel-cream texture &amp;amp; a quick-dissolving formula&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Formulated with an exclusive 360 Complex&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	An anti-cellulite body treatment. Features a special gel-cream texture &amp;amp; a quick-dissolving formula. Formulated with an exclusive 360 Complex. Helps combat presence of cellulite &amp;amp; reduce existing cellulite. Provides immediate invigorating &amp;amp; firming results. Concentrated with micro-pearl particles to illuminate skin. Creates svelte &amp;amp; re-sculpted body contours....&lt;/p&gt;\r\n'),
(108,1,'Lancome Hypnose Doll Lashes Mascara 4-Piece Gift Set','','','&lt;p&gt;\r\n	&amp;nbsp;Lancome Hypnose Doll Lashes Mascara 4-Piece Gift Set! Limited Edition!&lt;/p&gt;\r\n&lt;ol&gt;\r\n	&lt;li&gt;\r\n		0.22 oz full-size Hypnôse Doll Lashes Mascara in Black&lt;/li&gt;\r\n	&lt;li&gt;\r\n		0.07 oz full-size Le Crayon Khol Eyeliner in Black Ebony&lt;/li&gt;\r\n	&lt;li&gt;\r\n		0.07 oz travel-size Cils Booster XL Super Enhancing Mascara Base&lt;/li&gt;\r\n	&lt;li&gt;\r\n		1.7 fl oz travel-size Bi-Facil Double-Action Eye Makeup Remover&lt;/li&gt;\r\n&lt;/ol&gt;\r\n'),
(108,9,'Lancome Hypnose Doll Lashes Mascara 4-Piece Gift Set','','','&lt;p&gt;\r\n	&amp;nbsp;Lancome Hypnose Doll Lashes Mascara 4-Piece Gift Set! Limited Edition!&lt;/p&gt;\r\n&lt;ol&gt;\r\n	&lt;li&gt;\r\n		0.22 oz full-size Hypnôse Doll Lashes Mascara in Black&lt;/li&gt;\r\n	&lt;li&gt;\r\n		0.07 oz full-size Le Crayon Khol Eyeliner in Black Ebony&lt;/li&gt;\r\n	&lt;li&gt;\r\n		0.07 oz travel-size Cils Booster XL Super Enhancing Mascara Base&lt;/li&gt;\r\n	&lt;li&gt;\r\n		1.7 fl oz travel-size Bi-Facil Double-Action Eye Makeup Remover&lt;/li&gt;\r\n&lt;/ol&gt;\r\n'),
(109,1,'Lancome Visionnaire Advanced Skin Corrector','','','&lt;p&gt;\r\n	Lancôme innovates with VISIONNAIRE [LR 2412 &amp;nbsp;4%], its ﬁrst&amp;nbsp;skincare product formulated to fundamentally recreate truly&amp;nbsp;beautiful skin.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A Lancôme technological breakthrough has identiﬁed&amp;nbsp;a miraculous new molecule.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The name of this molecule: LR 2412.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A molecule that is able to “self-propel” through the layers&amp;nbsp;of the epidermis, to set off a series of tissular micro-transformations.&amp;nbsp;The result is that skin is visibly transformed: the texture is ﬁner,&amp;nbsp;wrinkles are erased, pigmentary and vascular irregularities are&amp;nbsp;reduced and pores are tightened.&lt;/p&gt;\r\n&lt;p&gt;\r\n	&amp;nbsp;&lt;/p&gt;\r\n&lt;p&gt;\r\n	&lt;em&gt;Download Presentation file after order.&lt;/em&gt;&lt;/p&gt;\r\n'),
(109,9,'Lancome Visionnaire Advanced Skin Corrector','','','&lt;p&gt;\r\n	Lancôme innovates with VISIONNAIRE [LR 2412 &amp;nbsp;4%], its ﬁrst&amp;nbsp;skincare product formulated to fundamentally recreate truly&amp;nbsp;beautiful skin.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A Lancôme technological breakthrough has identiﬁed&amp;nbsp;a miraculous new molecule.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	The name of this molecule: LR 2412.&lt;br /&gt;\r\n	&lt;br /&gt;\r\n	A molecule that is able to “self-propel” through the layers&amp;nbsp;of the epidermis, to set off a series of tissular micro-transformations.&amp;nbsp;The result is that skin is visibly transformed: the texture is ﬁner,&amp;nbsp;wrinkles are erased, pigmentary and vascular irregularities are&amp;nbsp;reduced and pores are tightened.&lt;/p&gt;\r\n'),
(110,1,'Flora By Gucci Eau Fraiche','','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		Perfect for all occasions&lt;/li&gt;\r\n	&lt;li&gt;\r\n		This item is not a tester; New and sealed&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Contains natural ingredients&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	Gucci presents the new spring version of this perfume called flora by Gucci eau fraiche in 2011. Even younger, more airy, vivid, sparkling and fresher than the original, the new fragrance is enriched with additional aromas of citruses in the top notes and aquatic and green nuances in the heart, while the base remains unchanged. The composition begins with mandarin, bergamot, kumquat, lemon and peony. The heart is made of rose petals and Osman thus with green and aquatic additions, laid on the base of sandalwood, patchouli and pink pepper.&lt;/p&gt;\r\n'),
(110,9,'Flora By Gucci Eau Fraiche','','','&lt;ul&gt;\r\n	&lt;li&gt;\r\n		Perfect for all occasions&lt;/li&gt;\r\n	&lt;li&gt;\r\n		This item is not a tester; New and sealed&lt;/li&gt;\r\n	&lt;li&gt;\r\n		Contains natural ingredients&lt;/li&gt;\r\n&lt;/ul&gt;\r\n&lt;p&gt;\r\n	Gucci presents the new spring version of this perfume called flora by Gucci eau fraiche in 2011. Even younger, more airy, vivid, sparkling and fresher than the original, the new fragrance is enriched with additional aromas of citruses in the top notes and aquatic and green nuances in the heart, while the base remains unchanged. The composition begins with mandarin, bergamot, kumquat, lemon and peony. The heart is made of rose petals and Osman thus with green and aquatic additions, laid on the base of sandalwood, patchouli and pink pepper.&lt;/p&gt;\r\n');




--
-- Dumping data for table `product_option_descriptions`
--



INSERT INTO `ac_product_option_descriptions` 
VALUES 
(315,9,54,'Color'),
(318,9,53,'Color'),
(318,1,53,'Color'),
(315,1,54,'Color'),
(319,9,56,'Color'),
(319,1,56,'Color'),
(304,1,57,'Color'),
(304,9,57,'Color'),
(305,1,59,'Color'),
(305,9,59,'Color'),
(306,1,55,'Color'),
(306,9,55,'Color'),
(307,1,60,'Color'),
(307,9,60,'Color'),
(308,1,61,'Color'),
(308,9,61,'Color'),
(316,9,63,'Tamaño fragancia'),
(316,1,63,'Fragrance Size'),
(314,9,64,'Tamaño fragancia'),
(314,1,64,'Fragrance Size'),
(317,9,69,'Tamaño'),
(317,1,69,'Size'),
(320,9,78,'Tamaño fragancia'),
(320,1,78,'Fragrance Size'),
(321,1,80,'Fragrance Size'),
(321,9,80,'Tamaño fragancia'),
(322,1,84,'Size'),
(322,9,84,'Tamaño'),
(323,1,85,'Fragrance Size'),
(323,9,85,'Tamaño fragancia'),
(324,1,89,'Fragrance Size'),
(324,9,89,'Tamaño fragancia'),
(326,9,90,'Tamaño fragancia'),
(326,1,90,'Fragrance Size'),
(327,1,99,'Color'),
(327,9,99,'Color'),
(328,1,100,'Color'),
(328,9,100,'Color'),
(329,1,101,'Size'),
(329,9,101,'Tamaño'),
(330,1,102,'Size'),
(330,9,102,'Tamaño'),
(331,1,104,'Size'),
(331,9,104,'Tamaño'),
(332,1,104,'Gift Wrapping'),
(332,9,104,'Papel de Regalo'),
(335,1,105,'Fragrance Type'),
(335,9,105,'Fragancia Tipo'),
(336,1,105,'Gift Wrapping'),
(336,9,105,'Papel de Regalo'),
(337,1,105,'Size'),
(337,9,105,'Tamaño'),
(338,1,106,'Choose Scent'),
(338,9,0,'Elija Scent'),
(339,1,106,'Gift Wrapping'),
(339,9,106,'Papel de Regalo'),
(340,1,109,'Gift Wrapping'),
(340,9,109,'Papel de Regalo'),
(341,1,110,'Size'),
(341,9,110,'Tamaño');



--
-- Dumping data for table `product_option_value_descriptions`
--



INSERT INTO `ac_product_option_value_descriptions` (product_option_value_id, language_id, product_id, name) 
VALUES 
(652,9,53,'Natural de Oro'),
(653,1,53,'Natural Ambre'),
(652,1,53,'Natural Golden'),
(646,9,54,'Marrón'),
(646,1,54,'Brown'),
(653,9,53,'Natural de Ambre'),
(645,9,54,'Negro'),
(645,1,54,'Black'),
(658,9,56,'Suede'),
(658,1,56,'Suede'),
(657,9,56,'Bisque de luz'),
(657,1,56,'Light Bisque'),
(656,9,56,'Ivore'),
(656,1,56,'Ivore'),
(655,9,56,'Dore'),
(655,1,56,'Dore'),
(654,9,56,'Bronce'),
(654,1,56,'Bronze'),
(612,1,57,'Pink Pool'),
(612,9,57,'Pink Pool'),
(613,1,57,'Mandarin Sky'),
(613,9,57,'Mandarin Sky'),
(614,1,57,'Brilliant Berry'),
(614,9,57,'Brilliant Berry'),
(615,1,59,'Viva Glam IV'),
(615,9,59,'Viva Glam IV'),
(616,1,59,'Viva Glam II'),
(616,9,59,'Viva Glam II'),
(617,1,59,'Viva Glam VI'),
(617,9,59,'Viva Glam VI'),
(618,1,55,'La Base'),
(618,9,55,'La Base'),
(619,1,55,'Lacewood'),
(619,9,55,'Lacewood'),
(620,1,55,'Smoky Rouge'),
(620,9,55,'Smoky Rouge'),
(621,1,55,'Tulipwood'),
(621,9,55,'Tulipwood'),
(622,1,60,'Shirelle'),
(622,9,60,'Shirelle'),
(623,1,60,'Vintage Vamp'),
(623,9,60,'Vintage Vamp'),
(624,1,60,'Nocturnelle'),
(624,9,60,'Nocturnelle'),
(625,1,61,'Golden Frenzy'),
(625,9,61,'Golden Frenzy'),
(626,1,61,'Gris Fatale'),
(626,9,61,'Gris Fatale'),
(627,1,61,'Jade Fever'),
(627,9,61,'Jade Fever'),
(649,9,63,'1.7 oz'),
(649,1,63,'1.7 oz'),
(648,9,63,'2.5 oz'),
(648,1,63,'2.5 oz'),
(647,9,63,'3.4 oz'),
(647,1,63,'3.4 oz'),
(644,9,64,'3.4 oz'),
(644,1,64,'3.4 oz'),
(643,9,64,'1.7 oz'),
(643,1,64,'1.7 oz'),
(642,9,64,'1.0 oz'),
(642,1,64,'1.0 oz'),
(651,9,69,'33.8 oz'),
(651,1,69,'33.8 oz'),
(650,9,69,'8 oz'),
(650,1,69,'8 oz'),
(662,9,78,'50ml'),
(662,1,78,'50ml'),
(661,9,78,'150ml'),
(661,1,78,'150ml'),
(660,9,78,'100ml'),
(660,1,78,'100ml'),
(659,1,56,'Light Buff'),
(659,9,56,'Buff luz'),
(663,1,80,'1.7 oz'),
(663,9,80,'1.7 oz'),
(664,1,80,'3.4 oz'),
(664,9,80,'3.4 oz'),
(665,1,80,'6.7 oz'),
(665,9,80,'6.7 oz'),
(666,1,84,'30 ml'),
(666,9,84,'30 ml'),
(667,1,84,'50 ml'),
(667,9,84,'50 ml'),
(668,1,84,'75 ml'),
(668,9,84,'75 ml'),
(669,1,85,'1 oz'),
(669,9,85,'1 oz'),
(670,1,85,'1.7 oz'),
(670,9,85,'1.7 oz'),
(671,1,85,'3.4 oz'),
(671,9,85,'3.4 oz'),
(672,1,89,'0.04 oz'),
(672,9,89,'0.04 oz'),
(673,1,89,'6.7 oz'),
(673,9,89,'6.7 oz'),
(674,1,89,'1.7 oz'),
(674,9,89,'1.7 oz'),
(676,9,90,'1.7 oz EDP Spray'),
(676,1,90,'1.7 oz EDP Spray'),
(677,1,90,'3.4 oz EDP Spray'),
(677,9,90,'3.4 oz EDP Spray'),
(678,1,99,'rose beige'),
(678,9,99,'rose beige'),
(679,1,99,'cranberry'),
(679,9,99,'cranberry'),
(680,1,99,'cassis'),
(680,9,99,'cassis'),
(681,1,100,'beige'),
(681,9,100,'beige'),
(682,1,100,'red beige'),
(682,9,100,'red beige'),
(683,1,100,'brique'),
(683,9,100,'brique'),
(684,1,100,'brown'),
(684,9,100,'marrón'),
(685,1,100,'mauve'),
(685,9,100,'mauve'),
(686,1,100,'red'),
(686,9,100,'rojo'),
(687,1,101,'8.45 oz'),
(688,1,101,'15.2 oz'),
(689,1,101,'33.8 oz'),
(690,1,102,'30ml'),
(691,1,102,'50ml'),
(692,1,102,'75ml'),
(714,1,104,'1 oz'),
(713,1,104,'1.7 oz'),
(700,9,104,'sí'),
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

INSERT INTO `ac_product_option_values` (product_option_value_id, product_option_id, product_id, group_id, sku, quantity, subtract, price, prefix, weight, weight_type, attribute_value_id, sort_order) 
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
(251,32,8,1,'50.0000','2010-02-01','2010-02-28'),
(242,39,8,1,'50.0000','2010-02-01','2010-02-28'),
(243,40,8,1,'50.0000','2010-02-01','2010-02-28'),
(244,41,8,1,'50.0000','2010-02-01','2010-02-28'),
(246,42,8,1,'50.0000','2010-02-01','2010-02-28'),
(249,43,8,1,'50.0000','2010-02-01','2010-02-28'),
(252,51,8,0,'19.0000','0000-00-00','0000-00-00'),
(253,55,8,0,'27.0000','0000-00-00','0000-00-00'),
(254,67,8,0,'29.0000','0000-00-00','0000-00-00'),
(255,72,8,0,'24.0000','0000-00-00','0000-00-00'),
(256,88,8,0,'27.0000','0000-00-00','0000-00-00'),
(257,93,8,0,'220.0000','0000-00-00','0000-00-00'),
(258,103,8,0,'42.0000','2012-02-05','2016-05-14');



--
-- Dumping data for table `product_tags`
--


INSERT INTO `ac_product_tags` 
VALUES 
(50,'cheeks',1),
(50,'cheeks',9),
(50,'makeup',1),
(50,'makeup',9),
(51,'cheeks',1),
(51,'cheeks',9),
(51,'makeup',1),
(51,'makeup',9),
(54,'eye',1),
(54,'eye',9),
(54,'makeup',1),
(54,'makeup',9),
(77,'body',1),
(77,'body',9),
(77,'men',1),
(77,'men',9),
(77,'shower',1),
(77,'shower',9),
(78,'fragrance',1),
(78,'fragrance',9),
(78,'men',1),
(78,'men',9),
(79,'fragrance',1),
(79,'men',1),
(79,'unisex',1),
(79,'women',1),
(83,'fragrance',9),
(83,'men',9),
(85,'fragrance',1),
(85,'fragrance',9),
(85,'women',1),
(85,'women',9),
(87,'fragrance',1),
(87,'fragrance',9),
(89,'fragrance',1),
(89,'fragrance',9),
(89,'woman',1),
(89,'woman',9),
(95,'gift',1),
(95,'gift',9),
(95,'man',1),
(95,'man',9),
(96,'man',1),
(96,'skincare',1),
(96,'skincare',9),
(96,'woman',9),
(98,'man',1),
(98,'man',9),
(99,'nail',1),
(99,'nail',9),
(99,'women',1),
(99,'women',9),
(101,'conditioner',1),
(101,'conditioner',9),
(103,'spray',1),
(103,'spray',9),
(108,'gift',1),
(108,'pen',1),
(108,'set',1);


--
-- Dumping data for table `products`
--



INSERT INTO `ac_products` (`product_id`,`model`,`sku`,`location`,`quantity`,`stock_status_id`,`manufacturer_id`,`shipping`,`price`,`tax_class_id`,`date_available`,`weight`,`weight_class_id`,`length`,`width`,`height`,`length_class_id`,`status`,`date_added`,`date_modified`,`viewed`,`sort_order`,`subtract`,`minimum`,`cost`) 
VALUES 
(68,'108681','','',1000,1,15,0,'42.0000',1,'2011-08-30','0.11',1,'0.00','0.00','0.00',0,1,'2011-08-31 05:48:56','2011-09-07 04:11:08',0,1,1,1,'24.0000'),
(65,'427847','','',1000,1,15,0,'105.0000',1,'2011-08-30','70.00',2,'0.00','0.00','0.00',0,1,'2011-08-31 05:27:30','2011-09-07 04:10:59',21,1,0,1,'99.0000'),
(66,'556240','','',145,1,12,0,'38.0000',1,'2011-08-30','0.40',1,'0.00','0.00','0.00',0,1,'2011-08-31 05:35:58','2011-09-07 04:35:47',4,1,1,1,'0.0000'),
(67,'463686','','',1000,1,15,0,'34.5000',1,'2011-08-30','0.30',1,'0.00','0.00','0.00',0,1,'2011-08-31 05:39:25','2011-09-07 04:23:49',2,1,1,1,'22.0000'),
(50,'558003','','',99,1,11,0,'29.5000',1,'2011-08-29','75.00',2,'0.00','0.00','0.00',0,1,'2011-08-30 07:58:45','2011-09-07 04:35:04',8,1,0,1,'0.0000'),
(51,'483857','','',98,1,12,1,'30.0000',1,'2011-08-29','0.05',1,'0.00','0.00','0.00',0,1,'2011-08-30 08:05:21','2011-09-07 04:18:54',7,1,1,1,'0.0000'),
(52,'523755','','',99,1,12,0,'28.0000',0,'2011-08-29','0.80',1,'0.00','0.00','0.00',0,1,'2011-08-30 08:19:10','2011-09-07 04:19:06',2,1,1,2,'0.0000'),
(53,'380440','','',1000,3,15,0,'38.5000',1,'2011-08-29','100.00',2,'0.00','0.00','0.00',0,1,'2011-08-30 08:26:28','2011-09-07 04:36:15',5,1,1,1,'22.0000'),
(54,'74144','','',999,1,15,1,'25.0000',1,'2011-08-29','0.15',1,'0.00','0.00','0.00',0,1,'2011-08-30 08:42:22','2011-09-07 04:25:11',10,1,1,1,'0.0000'),
(55,'tw152236','','',1000,1,15,0,'29.0000',1,'2011-08-29','0.08',1,'0.00','0.00','0.00',0,1,'2011-08-30 08:46:36','2011-09-07 04:25:27',5,1,1,1,'22.0000'),
(56,'35190','','',1000,1,15,0,'29.5000',1,'2011-08-29','85.00',2,'0.00','0.00','0.00',0,1,'2011-08-30 09:22:57','2011-09-07 04:37:05',9,1,1,1,'0.0000'),
(57,'117148','','',1000,1,15,0,'29.5000',1,'2011-08-29','0.20',1,'0.00','0.00','0.00',0,1,'2011-08-30 09:51:50','2011-09-07 04:21:17',12,1,1,1,'0.0000'),
(58,'374002','','',0,2,12,1,'34.0000',1,'2011-08-29','25.00',2,'0.00','0.00','0.00',0,1,'2011-08-30 10:05:30','2011-09-07 04:10:44',2,1,1,1,'10.0000'),
(59,'14.50','','',1000,1,11,1,'5.0000',1,'2011-08-29','75.00',2,'0.00','0.00','0.00',0,1,'2011-08-30 11:22:54','2011-09-07 04:36:45',2,1,1,1,'0.0000'),
(60,'112423','','',1000,1,11,0,'15.0000',1,'2011-08-30','0.30',2,'0.00','0.00','0.00',0,1,'2011-08-31 04:27:48','2011-09-07 04:32:07',2,1,0,1,'0.0000'),
(61,'529071','','',1000,1,15,0,'48.0000',1,'2011-08-30','0.13',2,'0.00','0.00','0.00',0,1,'2011-08-31 04:36:37','2011-09-07 04:20:29',4,1,0,1,'29.0000'),
(62,'601232','','',1000,1,13,0,'14.0000',1,'2011-08-30','0.50',1,'0.00','0.00','0.00',0,1,'2011-08-31 04:43:18','2011-09-07 04:38:43',3,1,0,1,'8.0000'),
(63,'374622','','',1000,1,14,0,'88.0000',1,'2011-08-30','0.75',1,'0.00','0.00','0.00',0,1,'2011-08-31 04:50:26','2011-09-07 04:33:26',3,1,0,1,'55.0000'),
(64,'497303','','',1000,1,13,0,'50.0000',1,'2011-08-30','150.00',2,'0.00','0.00','0.00',0,1,'2011-08-31 05:00:48','2011-09-07 04:10:30',8,1,1,1,'33.0000'),
(69,'SCND001','','',1000,1,16,0,'19.0000',1,'2011-08-30','0.25',1,'0.00','0.00','0.00',0,1,'2011-08-31 06:00:09','2011-09-07 04:33:37',6,1,0,1,'0.0000'),
(70,'522823','','',1000,1,14,0,'31.0000',1,'2011-08-30','0.25',2,'0.00','0.00','0.00',0,1,'2011-08-31 06:18:25','2011-09-07 04:22:05',1,1,1,1,'0.0000'),
(71,'PCND001','','',1000,1,17,0,'11.4500',1,'2011-08-30','0.30',1,'0.00','0.00','0.00',0,1,'2011-08-31 08:33:23','2011-09-07 04:32:57',2,1,1,1,'5.0000'),
(72,'PCND002','','',1000,1,17,0,'27.0000',1,'2011-08-30','0.40',1,'0.00','0.00','0.00',0,1,'2011-08-31 08:39:20','2011-09-07 04:19:46',4,1,1,1,'0.0000'),
(73,'PCND003','','',1000,1,17,0,'33.0000',1,'2011-08-30','0.40',1,'0.00','0.00','0.00',0,1,'2011-08-31 08:41:47','2011-09-07 04:24:42',1,1,1,1,'21.0000'),
(74,'PCND004','','',10000,1,17,0,'4.0000',1,'2011-08-30','0.35',1,'0.00','0.00','0.00',0,1,'2011-08-31 08:54:34','2011-09-07 04:21:06',3,1,1,1,'0.0000'),
(75,'DMBW0012','','',1000,1,18,0,'6.7000',1,'2011-08-30','0.20',1,'0.00','0.00','0.00',0,1,'2011-08-31 09:13:59','2011-09-07 04:21:28',1,1,1,1,'0.0000'),
(76,'DMBW0013','1235B','',99,1,18,0,'7.2000',1,'2011-08-30','0.20',1,'0.00','0.00','0.00',0,1,'2011-08-31 09:24:02','2011-09-07 04:31:48',5,1,1,1,'4.0000'),
(77,'DMBW0014','1234B','',1000,1,18,1,'6.0000',1,'2011-08-30','0.30',1,'0.00','0.00','0.00',0,1,'2011-08-31 09:28:55','2011-09-07 04:30:11',9,1,1,1,'2.0000'),
(78,'Cl0001','','',1000,1,13,0,'29.0000',1,'2011-08-30','125.00',2,'0.00','0.00','0.00',0,1,'2011-08-31 09:41:24','2011-09-07 04:37:33',10,1,1,1,'0.0000'),
(79,'CKGS01','','',1000,1,13,0,'36.0000',1,'2011-08-30','250.00',2,'0.00','0.00','0.00',0,1,'2011-08-31 09:52:02','2011-09-07 04:37:49',2,1,1,1,'28.0000'),
(80,'GRM001','','',850,1,19,0,'59.0000',1,'2011-09-01','80.00',2,'0.00','0.00','0.00',0,1,'2011-09-02 10:18:40','2011-09-07 04:11:17',5,1,1,1,'33.0000'),
(81,'GRM002','','',1000,1,19,0,'61.0000',1,'2011-09-01','150.00',2,'0.00','0.00','0.00',0,1,'2011-09-02 10:31:46','2011-09-07 04:18:38',2,1,1,1,'0.0000'),
(82,'GRM003','','',1000,1,19,0,'42.0000',1,'2011-09-01','100.00',2,'0.00','0.00','0.00',0,1,'2011-09-02 10:39:36','2011-09-07 04:18:26',2,1,1,1,'0.0000'),
(83,'GRM004','','',1000,1,19,0,'37.5000',1,'2011-09-01','15.00',2,'0.00','0.00','0.00',0,1,'2011-09-02 11:07:22','2011-09-07 04:12:41',2,1,1,1,'0.0000'),
(84,'GRM005','','',1000,1,19,0,'30.0000',1,'2011-09-01','175.00',2,'0.00','0.00','0.00',0,1,'2011-09-02 11:17:57','2011-09-07 04:12:32',7,1,1,1,'0.0000'),
(85,'Ck0010','','',1000,1,13,1,'45.0000',1,'2011-09-01','0.08',5,'0.00','0.00','0.00',0,1,'2011-09-02 11:48:08','2011-09-07 04:24:25',3,1,1,1,'0.0000'),
(86,'CK0009','','',1,1,13,1,'44.1000',1,'2011-09-04','0.17',2,'0.00','0.00','0.00',0,1,'2011-09-05 04:19:16','2011-09-07 04:23:05',2,1,1,1,'0.0000'),
(87,'CK0010','','',10000,1,13,0,'37.5000',1,'2011-09-04','0.20',1,'0.00','0.00','0.00',0,1,'2011-09-05 04:28:10','2011-09-07 04:25:55',1,1,1,1,'0.0000'),
(88,'CK0011','','',1,1,13,0,'31.0000',1,'2011-09-04','340.00',2,'0.00','0.00','0.00',0,1,'2011-09-05 04:32:33','2011-09-07 04:38:19',1,1,1,1,'19.0000'),
(89,'CK0012','','',1000,3,13,0,'62.0000',1,'2011-09-04','0.12',1,'0.00','0.00','0.00',0,1,'2011-09-05 04:38:55','2011-09-07 04:33:55',5,1,1,1,'40.0000'),
(90,'CK0013','','',1000,1,13,0,'39.0000',1,'2011-09-04','0.33',2,'0.00','0.00','0.00',0,1,'2011-09-05 05:03:44','2011-09-07 04:32:42',2,1,1,1,'0.0000'),
(91,'BVLG001','','',1000,1,14,0,'29.0000',1,'2011-09-04','0.16',2,'0.00','0.00','0.00',0,1,'2011-09-05 05:43:49','2011-09-07 04:25:00',2,1,1,1,'20.0000'),
(92,'BVLG002','','',1000,1,14,0,'57.0000',1,'2011-09-04','0.40',5,'0.00','0.00','0.00',0,1,'2011-09-05 05:51:26','2011-09-07 04:19:25',7,1,1,1,'44.0000'),
(93,'BVLG003','','',1000,1,14,0,'280.0000',1,'2011-09-04','0.30',5,'0.00','0.00','0.00',0,1,'2011-09-05 05:58:34','2011-09-07 04:20:46',8,1,1,1,'100.0000'),
(94,'GRMBC001','','',589,1,19,1,'263.0000',1,'2011-09-04','0.15',1,'0.00','0.00','0.00',0,1,'2011-09-05 06:08:16','2011-09-07 04:33:14',3,1,1,1,'125.0000'),
(95,'GRMBC002','','',100,3,19,0,'104.0000',1,'2011-09-04','0.15',1,'0.00','0.00','0.00',0,1,'2011-09-05 06:21:13','2011-09-07 04:34:34',5,1,1,1,'0.0000'),
(96,'GRMBC003','','',100,1,19,1,'82.0000',1,'2011-09-04','80.00',2,'0.00','0.00','0.00',0,1,'2011-09-05 06:25:19','2011-09-07 04:23:32',8,1,0,2,'67.0000'),
(97,'GRMBC004','','',1,1,19,0,'126.0000',1,'2011-09-04','20.00',2,'0.00','0.00','0.00',0,1,'2011-09-05 06:31:08','2011-09-07 04:23:20',9,1,1,1,'0.0000'),
(98,'GRMBC005','','',1000,1,19,1,'98.0000',1,'2011-09-04','40.00',2,'0.00','0.00','0.00',0,1,'2011-09-05 06:48:59','2011-09-07 04:34:08',2,1,1,1,'87.0000'),
(99,'GRMBC006','','',1000,1,19,0,'137.0000',1,'2011-09-04','0.09',6,'0.00','0.00','0.00',0,1,'2011-09-05 06:52:31','2011-09-07 04:24:08',12,1,1,1,'0.0000'),
(100,'GRMBC007','','',1000,1,19,0,'10.0000',1,'2011-09-04','15.00',2,'0.00','0.00','0.00',0,1,'2011-09-05 07:05:17','2011-09-07 04:35:21',13,1,1,4,'8.0000'),
(101,'Pro-V','','',1000,1,17,1,'8.2300',1,'2012-03-13','8.45',6,'2.00','3.00','15.00',1,1,'2012-03-14 10:00:34','2012-03-14 10:43:24',35,1,0,1,'0.0000'),
(102,'PRF00269','','',1000,1,20,1,'105.0000',1,'2012-03-14','2.50',6,'0.00','0.00','0.00',3,1,'2012-03-15 02:44:51','2012-03-15 02:48:49',6,1,0,1,'0.0000'),
(103,'PRF00270','','',100,1,14,1,'78.0000',1,'2012-03-14','80.00',2,'0.00','0.00','0.00',3,1,'2012-03-15 03:09:22','2012-03-15 03:13:49',4,1,0,1,'0.0000'),
(104,'PRF00271','','',1000,1,13,1,'49.0000',1,'2012-03-14','0.00',5,'0.00','0.00','0.00',3,1,'2012-03-15 03:25:07','2012-03-15 03:32:45',19,1,1,1,'0.0000'),
(105,'PRF00273','','',100,2,14,1,'55.0000',0,'2012-03-14','0.00',5,'0.00','0.00','0.00',3,1,'2012-03-15 04:55:09','2012-03-15 05:16:20',18,1,1,1,'0.0000'),
(106,'PRF00274','','',185,1,14,1,'70.0000',1,'2012-03-14','80.00',5,'0.00','0.00','0.00',3,1,'2012-03-15 05:29:24','2012-03-15 05:44:49',8,1,1,1,'0.0000'),
(107,'PRF00274','','',0,2,15,1,'66.0000',1,'2012-03-14','7.00',6,'0.00','0.00','0.00',3,1,'2012-03-15 05:53:47','2012-03-15 05:59:26',5,1,1,1,'0.0000'),
(108,'PRF00268','','',420,1,15,1,'125.0000',1,'2012-03-14','2.00',6,'0.00','0.00','0.00',3,1,'2012-03-15 06:06:58','2012-03-15 06:21:19',6,1,1,2,'0.0000'),
(109,'PRF00279','','',1,1,15,1,'84.0000',1,'2012-03-14','50.00',6,'3.00','2.00','10.00',1,1,'2012-03-15 06:27:52','2012-03-15 06:48:07',6,1,1,1,'0.0000'),
(110,'PRF00278','','',1000,1,20,1,'90.0000',1,'2012-03-14','0.00',6,'0.00','0.00','0.00',3,1,'2012-03-15 07:02:10','0000-00-00 00:00:00',21,1,0,1,'0.0000');



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
-- Dumping data for table `products_to_downloads`
--



INSERT INTO `ac_products_to_downloads` 
VALUES 
(109,1);



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
(100010,1,'az_demo_product_15_1.jpg','','','18/6a/a.jpg','','2011-11-08 19:54:53','2011-11-08 19:54:53'),
(100012,1,'az_demo_product_07.jpg','','','18/6a/c.jpg','','2011-11-08 19:56:17','2011-11-08 19:56:17'),
(100011,1,'az_demo_product_15.jpg','','','18/6a/b.jpg','','2011-11-08 19:54:53','2011-11-08 19:54:53'),
(100007,1,'az_demo_product_14_2.jpg','','','18/6a/7.jpg','','2011-11-08 19:50:51','2011-11-08 19:50:51'),
(100008,1,'az_demo_product_14.jpg','','','18/6a/8.jpg','','2011-11-08 19:50:51','2011-11-08 19:50:51'),
(100009,1,'az_demo_product_14_1.jpg','','','18/6a/9.jpg','','2011-11-08 19:50:53','2011-11-08 19:50:53'),
(100013,1,'az_demo_product_18.jpg','','','18/6a/d.jpg','','2011-11-08 20:03:00','2011-11-08 20:03:00'),
(100014,1,'az_demo_product_30.jpg','','','18/6a/e.jpg','','2011-11-08 20:07:30','2011-11-08 20:07:30'),
(100015,1,'az_demo_product_30_2.jpg','','','18/6a/f.jpg','','2011-11-08 20:07:30','2011-11-08 20:07:30'),
(100016,1,'az_demo_product_30_1.jpg','','','18/6b/0.jpg','','2011-11-08 20:07:32','2011-11-08 20:07:32'),
(100017,1,'az_demo_product_30_3.jpg','','','18/6b/1.jpg','','2011-11-08 20:07:35','2011-11-08 20:07:35'),
(100018,1,'az_demo_product_34.jpg','','','18/6b/2.jpg','','2011-11-08 20:08:50','2011-11-08 20:08:50'),
(100019,1,'az_demo_product_34_2.jpg','','','18/6b/3.jpg','','2011-11-08 20:08:51','2011-11-08 20:08:51'),
(100020,1,'az_demo_product_34_1.jpg','','','18/6b/4.jpg','','2011-11-08 20:08:52','2011-11-08 20:08:52'),
(100021,1,'az_demo_product_32.jpg','','','18/6b/5.jpg','','2011-11-08 20:09:57','2011-11-08 20:09:57'),
(100022,1,'az_demo_product_32.png','','','18/6b/6.png','','2011-11-08 20:11:34','2011-11-08 20:11:34'),
(100023,1,'az_demo_product_33.jpg','','','18/6b/7.jpg','','2011-11-08 20:12:28','2011-11-08 20:12:28'),
(100024,1,'az_demo_product_32_1.jpg','','','18/6b/8.jpg','','2011-11-08 20:12:28','2011-11-08 20:12:28'),
(100025,1,'az_demo_product_31.jpg','','','18/6b/9.jpg','','2011-11-08 20:13:15','2011-11-08 20:13:15'),
(100026,1,'az_demo_product_02.jpg','','','18/6b/a.jpg','','2011-11-08 20:14:15','2011-11-08 20:14:15'),
(100027,1,'az_demo_product_02_2.jpg','','','18/6b/b.jpg','','2011-11-08 20:14:17','2011-11-08 20:14:17'),
(100028,1,'az_demo_product_02_1.jpg','','','18/6b/c.jpg','','2011-11-08 20:14:21','2011-11-08 20:14:21'),
(100029,1,'az_demo_product_02_3.jpg','','','18/6b/d.jpg','','2011-11-08 20:16:05','2011-11-08 20:16:05'),
(100030,1,'az_demo_product_42.jpg','','','18/6b/e.jpg','','2011-11-08 20:17:37','2011-11-08 20:17:37'),
(100031,1,'az_demo_product_22.jpg','','','18/6b/f.jpg','','2011-11-08 20:18:42','2011-11-08 20:18:42'),
(100032,1,'az_demo_product_11_1.jpg','','','18/6c/0.jpg','','2011-11-08 20:19:46','2011-11-08 20:19:46'),
(100033,1,'az_demo_product_11_2.jpg','','','18/6c/1.jpg','','2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100034,1,'az_demo_product_11.jpg','','','18/6c/2.jpg','','2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100035,1,'az_demo_product_43.jpg','','','18/6c/3.jpg','','2011-11-08 20:20:41','2011-11-08 20:20:41'),
(100036,1,'az_demo_product_24.jpg','','','18/6c/4.jpg','','2011-11-08 20:21:47','2011-11-08 20:21:47'),
(100037,1,'az_demo_product_06_6.jpg','','','18/6c/5.jpg','','2011-11-08 20:22:54','2011-11-08 20:22:54'),
(100038,1,'az_demo_product_06_2.jpg','','','18/6c/6.jpg','','2011-11-08 20:22:59','2011-11-08 20:22:59'),
(100039,1,'az_demo_product_06_1.jpg','','','18/6c/7.jpg','','2011-11-08 20:22:59','2011-11-08 20:22:59'),
(100040,1,'az_demo_product_06.jpg','','','18/6c/8.jpg','','2011-11-08 20:23:00','2011-11-08 20:23:00'),
(100041,1,'az_demo_product_06_4.jpg','','','18/6c/9.jpg','','2011-11-08 20:23:00','2011-11-08 20:23:00'),
(100042,1,'az_demo_product_06_3.jpg','','','18/6c/a.jpg','','2011-11-08 20:23:04','2011-11-08 20:23:04'),
(100043,1,'az_demo_product_06_5.jpg','','','18/6c/b.jpg','','2011-11-08 20:23:04','2011-11-08 20:23:04'),
(100044,1,'az_demo_product_25_1.jpg','','','18/6c/c.jpg','','2011-11-08 20:24:16','2011-11-08 20:24:16'),
(100045,1,'az_demo_product_25_2.jpg','','','18/6c/d.jpg','','2011-11-08 20:24:20','2011-11-08 20:24:20'),
(100046,1,'az_demo_product_25.jpg','','','18/6c/e.jpg','','2011-11-08 20:24:59','2011-11-08 20:24:59'),
(100047,1,'az_demo_product_20.jpg','','','18/6c/f.jpg','','2011-11-08 20:26:07','2011-11-08 20:26:07'),
(100048,1,'az_demo_product_36.jpg','','','18/6d/0.jpg','','2011-11-08 20:27:05','2011-11-08 20:27:05'),
(100049,1,'az_demo_product_47.png','','','18/6d/1.png','','2011-11-08 20:28:16','2011-11-08 20:28:16'),
(100050,1,'az_demo_product_46.jpg','','','18/6d/2.jpg','','2011-11-08 20:29:29','2011-11-08 20:29:29'),
(100051,1,'az_demo_product_46.png','','','18/6d/3.png','','2011-11-08 20:29:31','2011-11-08 20:29:31'),
(100052,1,'az_demo_product_17.jpg','','','18/6d/4.jpg','','2011-11-08 20:30:22','2011-11-08 20:30:22'),
(100053,1,'az_demo_product_49_1.png','','','18/6d/5.png','','2011-11-08 20:31:38','2011-11-08 20:31:38'),
(100054,1,'az_demo_product_35_1.jpg','','','18/6d/6.jpg','','2011-11-08 20:32:33','2011-11-08 20:32:33'),
(100055,1,'az_demo_product_35_2.jpg','','','18/6d/7.jpg','','2011-11-08 20:32:34','2011-11-08 20:32:34'),
(100056,1,'az_demo_product_35.jpg','','','18/6d/8.jpg','','2011-11-08 20:32:35','2011-11-08 20:32:35'),
(100057,1,'az_demo_product_23.jpg','','','18/6d/9.jpg','','2011-11-08 20:33:31','2011-11-08 20:33:31'),
(100058,1,'az_demo_product_41.jpg','','','18/6d/a.jpg','','2011-11-08 20:34:54','2011-11-08 20:34:54'),
(100059,1,'az_demo_product_09_4.jpg','','','18/6d/b.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100060,1,'az_demo_product_09_1.jpg','','','18/6d/c.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100061,1,'az_demo_product_09.jpg','','','18/6d/d.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100062,1,'az_demo_product_09_3.jpg','','','18/6d/e.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100063,1,'az_demo_product_09_2.jpg','','','18/6d/f.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100064,1,'az_demo_product_37.jpg','','','18/6e/0.jpg','','2011-11-08 20:43:53','2011-11-08 20:43:53'),
(100065,1,'az_demo_product_26_2.jpg','','','18/6e/1.jpg','','2011-11-08 20:45:33','2011-11-08 20:45:33'),
(100066,1,'az_demo_product_26_3.jpg','','','18/6e/2.jpg','','2011-11-08 20:45:35','2011-11-08 20:45:35'),
(100067,1,'az_demo_product_26.jpg','','','18/6e/3.jpg','','2011-11-08 20:45:37','2011-11-08 20:45:37'),
(100068,1,'az_demo_product_26_1.jpg','','','18/6e/4.jpg','','2011-11-08 20:45:38','2011-11-08 20:45:38'),
(100069,1,'az_demo_product_27_1.jpg','','','18/6e/5.jpg','','2011-11-08 20:48:44','2011-11-08 20:48:44'),
(100070,1,'az_demo_product_27.jpg','','','18/6e/6.jpg','','2011-11-08 20:48:57','2011-11-08 20:48:57'),
(100071,1,'az_demo_product_10.jpg','','','18/6e/7.jpg','','2011-11-08 20:50:08','2011-11-08 20:50:08'),
(100072,1,'az_demo_product_10_1.jpg','','','18/6e/8.jpg','','2011-11-08 20:50:09','2011-11-08 20:50:09'),
(100073,1,'az_demo_product_10_2.jpg','','','18/6e/9.jpg','','2011-11-08 20:50:09','2011-11-08 20:50:09'),
(100074,1,'az_demo_product_10_3.jpg','','','18/6e/a.jpg','','2011-11-08 20:50:11','2011-11-08 20:50:11'),
(100075,1,'az_demo_product_44.jpg','','','18/6e/b.jpg','','2011-11-08 20:51:24','2011-11-08 20:51:24'),
(100076,1,'az_demo_product_40_1.jpg','','','18/6e/c.jpg','','2011-11-08 20:52:17','2011-11-08 20:52:17'),
(100077,1,'az_demo_product_40.jpg','','','18/6e/d.jpg','','2011-11-08 20:52:18','2011-11-08 20:52:18'),
(100078,1,'az_demo_product_40_2.jpg','','','18/6e/e.jpg','','2011-11-08 20:52:18','2011-11-08 20:52:18'),
(100079,1,'az_demo_product_21.jpg','','','18/6e/f.jpg','','2011-11-08 20:53:20','2011-11-08 20:53:20'),
(100080,1,'az_demo_product_13_2.jpg','','','18/6f/0.jpg','','2011-11-08 20:56:09','2011-11-08 20:56:09'),
(100081,1,'az_demo_product_13_1.jpg','','','18/6f/1.jpg','','2011-11-08 20:56:10','2011-11-08 20:56:10'),
(100082,1,'az_demo_product_19.jpg','','','18/6f/2.jpg','','2011-11-08 20:57:14','2011-11-08 20:57:14'),
(100083,1,'az_demo_product_39.jpg','','','18/6f/3.jpg','','2011-11-08 21:00:11','2011-11-08 21:00:11'),
(100084,1,'az_demo_product_39_3.jpg','','','18/6f/4.jpg','','2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100085,1,'az_demo_product_39_2.jpg','','','18/6f/5.jpg','','2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100086,1,'az_demo_product_39_1.jpg','','','18/6f/6.jpg','','2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100087,1,'az_demo_product_45.png','','','18/6f/7.png','','2011-11-08 21:02:04','2011-11-08 21:02:04'),
(100088,1,'az_demo_product_48.png','','','18/6f/8.png','','2011-11-08 21:04:09','2011-11-08 21:04:09'),
(100089,1,'az_demo_product_01.jpg','','','18/6f/9.jpg','','2011-11-08 21:05:06','2011-11-08 21:05:06'),
(100090,1,'az_demo_product_50.png','','','18/6f/a.png','','2011-11-08 21:06:18','2011-11-08 21:06:18'),
(100091,1,'az_demo_product_16_1.jpg','','','18/6f/b.jpg','','2011-11-08 21:07:20','2011-11-08 21:07:20'),
(100092,1,'az_demo_product_16.jpg','','','18/6f/c.jpg','','2011-11-08 21:07:20','2011-11-08 21:07:20'),
(100093,1,'az_demo_product_16_2.jpg','','','18/6f/d.jpg','','2011-11-08 21:07:24','2011-11-08 21:07:24'),
(100094,1,'az_demo_product_03.jpg','','','18/6f/e.jpg','','2011-11-08 21:08:27','2011-11-08 21:08:27'),
(100095,1,'az_demo_product_03_1.jpg','','','18/6f/f.jpg','','2011-11-08 21:08:33','2011-11-08 21:08:33'),
(100096,1,'az_demo_product_03_2.jpg','','','18/70/0.jpg','','2011-11-08 21:08:36','2011-11-08 21:08:36'),
(100097,1,'az_demo_product_08.jpg','','','18/70/1.jpg','','2011-11-08 21:09:38','2011-11-08 21:09:38'),
(100098,1,'az_demo_product_08_2.jpg','','','18/70/2.jpg','','2011-11-08 21:09:38','2011-11-08 21:09:38'),
(100099,1,'az_demo_product_08_3.jpg','','','18/70/3.jpg','','2011-11-08 21:09:39','2011-11-08 21:09:39'),
(100100,1,'az_demo_product_08_1.jpg','','','18/70/4.jpg','','2011-11-08 21:09:39','2011-11-08 21:09:39'),
(100101,1,'az_demo_product_05.jpg','','','18/70/5.jpg','','2011-11-08 21:10:52','2011-11-08 21:10:52'),
(100102,1,'az_demo_product_29_2.jpg','','','18/70/6.jpg','','2011-11-08 21:12:38','2011-11-08 21:12:38'),
(100103,1,'az_demo_product_29.jpg','','','18/70/7.jpg','','2011-11-08 21:12:40','2011-11-08 21:12:40'),
(100104,1,'az_demo_product_29_1.jpg','','','18/70/8.jpg','','2011-11-08 21:12:41','2011-11-08 21:12:41'),
(100105,1,'az_demo_product_29.jpg','','','18/70/9.jpg','','2011-11-08 21:14:19','2011-11-08 21:14:19'),
(100106,1,'az_demo_product_29_2.jpg','','','18/70/a.jpg','','2011-11-08 21:14:23','2011-11-08 21:14:23'),
(100107,1,'az_demo_product_29_1.jpg','','','18/70/b.jpg','','2011-11-08 21:14:26','2011-11-08 21:14:26'),
(100108,1,'az_demo_product_28_1.jpg','','','18/70/c.jpg','','2011-11-08 21:15:51','2011-11-08 21:15:51'),
(100109,1,'az_demo_product_28.jpg','','','18/70/d.jpg','','2011-11-08 21:15:53','2011-11-08 21:15:53'),
(100110,1,'az_demo_product_28_2.jpg','','','18/70/e.jpg','','2011-11-08 21:15:53','2011-11-08 21:15:53'),
(100111,1,'az_demo_product_38.jpg','','','18/70/f.jpg','','2011-11-08 21:25:03','2011-11-08 21:25:03'),
(100112,1,'az_demo_product_12.jpg','','','18/71/0.jpg','','2011-11-08 21:28:05','2011-11-08 21:28:05'),
(100113,1,'az_demo_product_12.png','','','18/71/1.png','','2011-11-08 21:28:22','2011-11-08 21:28:22'),
(100114,1,'mf_sephora_ba_logo_black.jpg','','','18/71/2.jpg','','2011-11-08 21:41:24','2011-11-08 21:41:24'),
(100115,1,'mf_Bvlgari.jpg','','','18/71/3.jpg','','2011-11-08 21:42:16','2011-11-08 21:42:16'),
(100116,1,'mf_calvin_klein.jpg','','','18/71/4.jpg','','2011-11-08 21:43:09','2011-11-08 21:43:09'),
(100117,1,'mf_benefit_logo_black.jpg','','','18/71/5.jpg','','2011-11-08 21:43:50','2011-11-08 21:43:50'),
(100118,1,'mf_mac_logo.jpg','','','18/71/6.jpg','','2011-11-08 21:44:29','2011-11-08 21:44:29'),
(100119,1,'mf_lancome_logo.gif','','','18/71/7.gif','','2011-11-08 21:45:15','2011-11-08 21:45:15'),
(100120,1,'mf_pantene_logo.jpg','','','18/71/8.jpg','','2011-11-08 21:46:11','2011-11-08 21:46:11'),
(100121,1,'mf_dove_logo.jpg','','','18/71/9.jpg','','2011-11-08 21:47:02','2011-11-08 21:47:02'),
(100122,1,'mf_armani_logo.gif','','','18/71/a.gif','','2011-11-08 21:47:56','2011-11-08 21:47:56'),
(100123,1,'demo_product_23.jpg','','','18/71/b.jpg','','2011-11-08 21:49:35','2011-11-08 21:49:35'),
(100124,1,'demo_product_04.jpg','','','18/71/c.jpg','','2011-11-08 21:50:27','2011-11-08 21:50:27'),
(100125,1,'demo_product_15.jpg','','','18/71/d.jpg','','2011-11-08 21:51:24','2011-11-08 21:51:24'),
(100126,1,'demo_product_14_2.jpg','','','18/71/e.jpg','','2011-11-08 21:52:17','2011-11-08 21:52:17'),
(100127,1,'demo_product_31.jpg','','','18/71/f.jpg','','2011-11-08 21:53:41','2011-11-08 21:53:41'),
(100128,1,'demo_product_34.jpg','','','18/72/0.jpg','','2011-11-08 21:54:44','2011-11-08 21:54:44'),
(100129,1,'demo_product_30_2.jpg','','','18/72/1.jpg','','2011-11-08 21:55:39','2011-11-08 21:55:39'),
(100130,1,'demo_product_24.jpg','','','18/72/2.jpg','','2011-11-08 21:59:23','2011-11-08 21:59:23'),
(100131,1,'demo_product_23.jpg','','','18/72/3.jpg','','2011-11-08 22:00:28','2011-11-08 22:00:28'),
(100132,1,'demo_product_05.jpg','','','18/72/4.jpg','','2011-11-08 22:01:48','2011-11-08 22:01:48'),
(100133,1,'demo_product_07.jpg','','','18/72/5.jpg','','2011-11-08 22:03:02','2011-11-08 22:03:02'),
(100134,1,'demo_product_08_3.jpg','','','18/72/6.jpg','','2011-11-08 22:04:14','2011-11-08 22:04:14'),
(100135,1,'demo_product_10_2.jpg','','','18/72/7.jpg','','2011-11-08 22:05:34','2011-11-08 22:05:34'),
(100136,1,'demo_product_47.png','','','18/72/8.png','','2011-11-08 22:06:59','2011-11-08 22:06:59'),
(100137,1,'demo_product_11_2.jpg','','','18/72/9.jpg','','2011-11-08 22:08:11','2011-11-08 22:08:11'),
(100138,1,'demo_product_40_2.jpg','','','18/72/a.jpg','','2011-11-08 22:10:13','2011-11-08 22:10:13'),
(100139,1,'demo_product_44.jpg','','','18/72/b.jpg','','2011-11-08 22:11:49','2011-11-08 22:11:49'),
(100140,1,'demo_product_29.jpg','','','18/72/c.jpg','','2011-11-08 22:13:13','2011-11-08 22:13:13'),
(100141,1,'demo_product_27.jpg','','','18/72/d.jpg','','2011-11-08 22:14:33','2011-11-08 22:14:33'),
(100142,1,'demo_product_42.jpg','','','18/72/e.jpg','','2011-11-08 22:16:11','2011-11-08 22:16:11'),
(100143,1,'demo_product_46.jpg','','','18/72/f.jpg','','2011-11-08 22:17:18','2011-11-08 22:17:18'),
(100144,1,'demo_product_18.jpg','','','18/73/0.jpg','','2011-11-08 22:18:43','2011-11-08 22:18:43'),
(100145,1,'demo_product_37.jpg','','','18/73/1.jpg','','2011-11-08 22:20:10','2011-11-08 22:20:10'),
(100146,1,'demo_product_49_1.png','','','18/73/2.png','','2011-11-08 22:21:44','2011-11-08 22:21:44'),
(100147,1,'store_logo.gif','','','18/73/3.gif','','2011-11-08 22:20:10','2011-11-08 22:20:10'),
(100148,1,'favicon.ico','','','18/73/4.ico','','2011-11-08 22:21:44','2011-11-08 22:21:44'),
(100010,9,'az_demo_product_15_1.jpg','','','18/6a/a.jpg','','2011-11-08 19:54:53','2011-11-08 19:54:53'),
(100012,9,'az_demo_product_07.jpg','','','18/6a/c.jpg','','2011-11-08 19:56:17','2011-11-08 19:56:17'),
(100011,9,'az_demo_product_15.jpg','','','18/6a/b.jpg','','2011-11-08 19:54:53','2011-11-08 19:54:53'),
(100007,9,'az_demo_product_14_2.jpg','','','18/6a/7.jpg','','2011-11-08 19:50:51','2011-11-08 19:50:51'),
(100008,9,'az_demo_product_14.jpg','','','18/6a/8.jpg','','2011-11-08 19:50:51','2011-11-08 19:50:51'),
(100009,9,'az_demo_product_14_1.jpg','','','18/6a/9.jpg','','2011-11-08 19:50:53','2011-11-08 19:50:53'),
(100013,9,'az_demo_product_18.jpg','','','18/6a/d.jpg','','2011-11-08 20:03:00','2011-11-08 20:03:00'),
(100014,9,'az_demo_product_30.jpg','','','18/6a/e.jpg','','2011-11-08 20:07:30','2011-11-08 20:07:30'),
(100015,9,'az_demo_product_30_2.jpg','','','18/6a/f.jpg','','2011-11-08 20:07:30','2011-11-08 20:07:30'),
(100016,9,'az_demo_product_30_1.jpg','','','18/6b/0.jpg','','2011-11-08 20:07:32','2011-11-08 20:07:32'),
(100017,9,'az_demo_product_30_3.jpg','','','18/6b/1.jpg','','2011-11-08 20:07:35','2011-11-08 20:07:35'),
(100018,9,'az_demo_product_34.jpg','','','18/6b/2.jpg','','2011-11-08 20:08:50','2011-11-08 20:08:50'),
(100019,9,'az_demo_product_34_2.jpg','','','18/6b/3.jpg','','2011-11-08 20:08:51','2011-11-08 20:08:51'),
(100020,9,'az_demo_product_34_1.jpg','','','18/6b/4.jpg','','2011-11-08 20:08:52','2011-11-08 20:08:52'),
(100021,9,'az_demo_product_32.jpg','','','18/6b/5.jpg','','2011-11-08 20:09:57','2011-11-08 20:09:57'),
(100022,9,'az_demo_product_32.png','','','18/6b/6.png','','2011-11-08 20:11:34','2011-11-08 20:11:34'),
(100023,9,'az_demo_product_33.jpg','','','18/6b/7.jpg','','2011-11-08 20:12:28','2011-11-08 20:12:28'),
(100024,9,'az_demo_product_32_1.jpg','','','18/6b/8.jpg','','2011-11-08 20:12:28','2011-11-08 20:12:28'),
(100025,9,'az_demo_product_31.jpg','','','18/6b/9.jpg','','2011-11-08 20:13:15','2011-11-08 20:13:15'),
(100026,9,'az_demo_product_02.jpg','','','18/6b/a.jpg','','2011-11-08 20:14:15','2011-11-08 20:14:15'),
(100027,9,'az_demo_product_02_2.jpg','','','18/6b/b.jpg','','2011-11-08 20:14:17','2011-11-08 20:14:17'),
(100028,9,'az_demo_product_02_1.jpg','','','18/6b/c.jpg','','2011-11-08 20:14:21','2011-11-08 20:14:21'),
(100029,9,'az_demo_product_02_3.jpg','','','18/6b/d.jpg','','2011-11-08 20:16:05','2011-11-08 20:16:05'),
(100030,9,'az_demo_product_42.jpg','','','18/6b/e.jpg','','2011-11-08 20:17:37','2011-11-08 20:17:37'),
(100031,9,'az_demo_product_22.jpg','','','18/6b/f.jpg','','2011-11-08 20:18:42','2011-11-08 20:18:42'),
(100032,9,'az_demo_product_11_1.jpg','','','18/6c/0.jpg','','2011-11-08 20:19:46','2011-11-08 20:19:46'),
(100033,9,'az_demo_product_11_2.jpg','','','18/6c/1.jpg','','2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100034,9,'az_demo_product_11.jpg','','','18/6c/2.jpg','','2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100035,9,'az_demo_product_43.jpg','','','18/6c/3.jpg','','2011-11-08 20:20:41','2011-11-08 20:20:41'),
(100036,9,'az_demo_product_24.jpg','','','18/6c/4.jpg','','2011-11-08 20:21:47','2011-11-08 20:21:47'),
(100037,9,'az_demo_product_06_6.jpg','','','18/6c/5.jpg','','2011-11-08 20:22:54','2011-11-08 20:22:54'),
(100038,9,'az_demo_product_06_2.jpg','','','18/6c/6.jpg','','2011-11-08 20:22:59','2011-11-08 20:22:59'),
(100039,9,'az_demo_product_06_1.jpg','','','18/6c/7.jpg','','2011-11-08 20:22:59','2011-11-08 20:22:59'),
(100040,9,'az_demo_product_06.jpg','','','18/6c/8.jpg','','2011-11-08 20:23:00','2011-11-08 20:23:00'),
(100041,9,'az_demo_product_06_4.jpg','','','18/6c/9.jpg','','2011-11-08 20:23:00','2011-11-08 20:23:00'),
(100042,9,'az_demo_product_06_3.jpg','','','18/6c/a.jpg','','2011-11-08 20:23:04','2011-11-08 20:23:04'),
(100043,9,'az_demo_product_06_5.jpg','','','18/6c/b.jpg','','2011-11-08 20:23:04','2011-11-08 20:23:04'),
(100044,9,'az_demo_product_25_1.jpg','','','18/6c/c.jpg','','2011-11-08 20:24:16','2011-11-08 20:24:16'),
(100045,9,'az_demo_product_25_2.jpg','','','18/6c/d.jpg','','2011-11-08 20:24:20','2011-11-08 20:24:20'),
(100046,9,'az_demo_product_25.jpg','','','18/6c/e.jpg','','2011-11-08 20:24:59','2011-11-08 20:24:59'),
(100047,9,'az_demo_product_20.jpg','','','18/6c/f.jpg','','2011-11-08 20:26:07','2011-11-08 20:26:07'),
(100048,9,'az_demo_product_36.jpg','','','18/6d/0.jpg','','2011-11-08 20:27:05','2011-11-08 20:27:05'),
(100049,9,'az_demo_product_47.png','','','18/6d/1.png','','2011-11-08 20:28:16','2011-11-08 20:28:16'),
(100050,9,'az_demo_product_46.jpg','','','18/6d/2.jpg','','2011-11-08 20:29:29','2011-11-08 20:29:29'),
(100051,9,'az_demo_product_46.png','','','18/6d/3.png','','2011-11-08 20:29:31','2011-11-08 20:29:31'),
(100052,9,'az_demo_product_17.jpg','','','18/6d/4.jpg','','2011-11-08 20:30:22','2011-11-08 20:30:22'),
(100053,9,'az_demo_product_49_1.png','','','18/6d/5.png','','2011-11-08 20:31:38','2011-11-08 20:31:38'),
(100054,9,'az_demo_product_35_1.jpg','','','18/6d/6.jpg','','2011-11-08 20:32:33','2011-11-08 20:32:33'),
(100055,9,'az_demo_product_35_2.jpg','','','18/6d/7.jpg','','2011-11-08 20:32:34','2011-11-08 20:32:34'),
(100056,9,'az_demo_product_35.jpg','','','18/6d/8.jpg','','2011-11-08 20:32:35','2011-11-08 20:32:35'),
(100057,9,'az_demo_product_23.jpg','','','18/6d/9.jpg','','2011-11-08 20:33:31','2011-11-08 20:33:31'),
(100058,9,'az_demo_product_41.jpg','','','18/6d/a.jpg','','2011-11-08 20:34:54','2011-11-08 20:34:54'),
(100059,9,'az_demo_product_09_4.jpg','','','18/6d/b.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100060,9,'az_demo_product_09_1.jpg','','','18/6d/c.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100061,9,'az_demo_product_09.jpg','','','18/6d/d.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100062,9,'az_demo_product_09_3.jpg','','','18/6d/e.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100063,9,'az_demo_product_09_2.jpg','','','18/6d/f.jpg','','2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100064,9,'az_demo_product_37.jpg','','','18/6e/0.jpg','','2011-11-08 20:43:53','2011-11-08 20:43:53'),
(100065,9,'az_demo_product_26_2.jpg','','','18/6e/1.jpg','','2011-11-08 20:45:33','2011-11-08 20:45:33'),
(100066,9,'az_demo_product_26_3.jpg','','','18/6e/2.jpg','','2011-11-08 20:45:35','2011-11-08 20:45:35'),
(100067,9,'az_demo_product_26.jpg','','','18/6e/3.jpg','','2011-11-08 20:45:37','2011-11-08 20:45:37'),
(100068,9,'az_demo_product_26_1.jpg','','','18/6e/4.jpg','','2011-11-08 20:45:38','2011-11-08 20:45:38'),
(100069,9,'az_demo_product_27_1.jpg','','','18/6e/5.jpg','','2011-11-08 20:48:44','2011-11-08 20:48:44'),
(100070,9,'az_demo_product_27.jpg','','','18/6e/6.jpg','','2011-11-08 20:48:57','2011-11-08 20:48:57'),
(100071,9,'az_demo_product_10.jpg','','','18/6e/7.jpg','','2011-11-08 20:50:08','2011-11-08 20:50:08'),
(100072,9,'az_demo_product_10_1.jpg','','','18/6e/8.jpg','','2011-11-08 20:50:09','2011-11-08 20:50:09'),
(100073,9,'az_demo_product_10_2.jpg','','','18/6e/9.jpg','','2011-11-08 20:50:09','2011-11-08 20:50:09'),
(100074,9,'az_demo_product_10_3.jpg','','','18/6e/a.jpg','','2011-11-08 20:50:11','2011-11-08 20:50:11'),
(100075,9,'az_demo_product_44.jpg','','','18/6e/b.jpg','','2011-11-08 20:51:24','2011-11-08 20:51:24'),
(100076,9,'az_demo_product_40_1.jpg','','','18/6e/c.jpg','','2011-11-08 20:52:17','2011-11-08 20:52:17'),
(100077,9,'az_demo_product_40.jpg','','','18/6e/d.jpg','','2011-11-08 20:52:18','2011-11-08 20:52:18'),
(100078,9,'az_demo_product_40_2.jpg','','','18/6e/e.jpg','','2011-11-08 20:52:18','2011-11-08 20:52:18'),
(100079,9,'az_demo_product_21.jpg','','','18/6e/f.jpg','','2011-11-08 20:53:20','2011-11-08 20:53:20'),
(100080,9,'az_demo_product_13_2.jpg','','','18/6f/0.jpg','','2011-11-08 20:56:09','2011-11-08 20:56:09'),
(100081,9,'az_demo_product_13_1.jpg','','','18/6f/1.jpg','','2011-11-08 20:56:10','2011-11-08 20:56:10'),
(100082,9,'az_demo_product_19.jpg','','','18/6f/2.jpg','','2011-11-08 20:57:14','2011-11-08 20:57:14'),
(100083,9,'az_demo_product_39.jpg','','','18/6f/3.jpg','','2011-11-08 21:00:11','2011-11-08 21:00:11'),
(100084,9,'az_demo_product_39_3.jpg','','','18/6f/4.jpg','','2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100085,9,'az_demo_product_39_2.jpg','','','18/6f/5.jpg','','2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100086,9,'az_demo_product_39_1.jpg','','','18/6f/6.jpg','','2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100087,9,'az_demo_product_45.png','','','18/6f/7.png','','2011-11-08 21:02:04','2011-11-08 21:02:04'),
(100088,9,'az_demo_product_48.png','','','18/6f/8.png','','2011-11-08 21:04:09','2011-11-08 21:04:09'),
(100089,9,'az_demo_product_01.jpg','','','18/6f/9.jpg','','2011-11-08 21:05:06','2011-11-08 21:05:06'),
(100090,9,'az_demo_product_50.png','','','18/6f/a.png','','2011-11-08 21:06:18','2011-11-08 21:06:18'),
(100091,9,'az_demo_product_16_1.jpg','','','18/6f/b.jpg','','2011-11-08 21:07:20','2011-11-08 21:07:20'),
(100092,9,'az_demo_product_16.jpg','','','18/6f/c.jpg','','2011-11-08 21:07:20','2011-11-08 21:07:20'),
(100093,9,'az_demo_product_16_2.jpg','','','18/6f/d.jpg','','2011-11-08 21:07:24','2011-11-08 21:07:24'),
(100094,9,'az_demo_product_03.jpg','','','18/6f/e.jpg','','2011-11-08 21:08:27','2011-11-08 21:08:27'),
(100095,9,'az_demo_product_03_1.jpg','','','18/6f/f.jpg','','2011-11-08 21:08:33','2011-11-08 21:08:33'),
(100096,9,'az_demo_product_03_2.jpg','','','18/70/0.jpg','','2011-11-08 21:08:36','2011-11-08 21:08:36'),
(100097,9,'az_demo_product_08.jpg','','','18/70/1.jpg','','2011-11-08 21:09:38','2011-11-08 21:09:38'),
(100098,9,'az_demo_product_08_2.jpg','','','18/70/2.jpg','','2011-11-08 21:09:38','2011-11-08 21:09:38'),
(100099,9,'az_demo_product_08_3.jpg','','','18/70/3.jpg','','2011-11-08 21:09:39','2011-11-08 21:09:39'),
(100100,9,'az_demo_product_08_1.jpg','','','18/70/4.jpg','','2011-11-08 21:09:39','2011-11-08 21:09:39'),
(100101,9,'az_demo_product_05.jpg','','','18/70/5.jpg','','2011-11-08 21:10:52','2011-11-08 21:10:52'),
(100102,9,'az_demo_product_29_2.jpg','','','18/70/6.jpg','','2011-11-08 21:12:38','2011-11-08 21:12:38'),
(100103,9,'az_demo_product_29.jpg','','','18/70/7.jpg','','2011-11-08 21:12:40','2011-11-08 21:12:40'),
(100104,9,'az_demo_product_29_1.jpg','','','18/70/8.jpg','','2011-11-08 21:12:41','2011-11-08 21:12:41'),
(100105,9,'az_demo_product_29.jpg','','','18/70/9.jpg','','2011-11-08 21:14:19','2011-11-08 21:14:19'),
(100106,9,'az_demo_product_29_2.jpg','','','18/70/a.jpg','','2011-11-08 21:14:23','2011-11-08 21:14:23'),
(100107,9,'az_demo_product_29_1.jpg','','','18/70/b.jpg','','2011-11-08 21:14:26','2011-11-08 21:14:26'),
(100108,9,'az_demo_product_28_1.jpg','','','18/70/c.jpg','','2011-11-08 21:15:51','2011-11-08 21:15:51'),
(100109,9,'az_demo_product_28.jpg','','','18/70/d.jpg','','2011-11-08 21:15:53','2011-11-08 21:15:53'),
(100110,9,'az_demo_product_28_2.jpg','','','18/70/e.jpg','','2011-11-08 21:15:53','2011-11-08 21:15:53'),
(100111,9,'az_demo_product_38.jpg','','','18/70/f.jpg','','2011-11-08 21:25:03','2011-11-08 21:25:03'),
(100112,9,'az_demo_product_12.jpg','','','18/71/0.jpg','','2011-11-08 21:28:05','2011-11-08 21:28:05'),
(100113,9,'az_demo_product_12.png','','','18/71/1.png','','2011-11-08 21:28:22','2011-11-08 21:28:22'),
(100114,9,'mf_sephora_ba_logo_black.jpg','','','18/71/2.jpg','','2011-11-08 21:41:24','2011-11-08 21:41:24'),
(100115,9,'mf_Bvlgari.jpg','','','18/71/3.jpg','','2011-11-08 21:42:16','2011-11-08 21:42:16'),
(100116,9,'mf_calvin_klein.jpg','','','18/71/4.jpg','','2011-11-08 21:43:09','2011-11-08 21:43:09'),
(100117,9,'mf_benefit_logo_black.jpg','','','18/71/5.jpg','','2011-11-08 21:43:50','2011-11-08 21:43:50'),
(100118,9,'mf_mac_logo.jpg','','','18/71/6.jpg','','2011-11-08 21:44:29','2011-11-08 21:44:29'),
(100119,9,'mf_lancome_logo.gif','','','18/71/7.gif','','2011-11-08 21:45:15','2011-11-08 21:45:15'),
(100120,9,'mf_pantene_logo.jpg','','','18/71/8.jpg','','2011-11-08 21:46:11','2011-11-08 21:46:11'),
(100121,9,'mf_dove_logo.jpg','','','18/71/9.jpg','','2011-11-08 21:47:02','2011-11-08 21:47:02'),
(100122,9,'mf_armani_logo.gif','','','18/71/a.gif','','2011-11-08 21:47:56','2011-11-08 21:47:56'),
(100123,9,'demo_product_23.jpg','','','18/71/b.jpg','','2011-11-08 21:49:35','2011-11-08 21:49:35'),
(100124,9,'demo_product_04.jpg','','','18/71/c.jpg','','2011-11-08 21:50:27','2011-11-08 21:50:27'),
(100125,9,'demo_product_15.jpg','','','18/71/d.jpg','','2011-11-08 21:51:24','2011-11-08 21:51:24'),
(100126,9,'demo_product_14_2.jpg','','','18/71/e.jpg','','2011-11-08 21:52:17','2011-11-08 21:52:17'),
(100127,9,'demo_product_31.jpg','','','18/71/f.jpg','','2011-11-08 21:53:41','2011-11-08 21:53:41'),
(100128,9,'demo_product_34.jpg','','','18/72/0.jpg','','2011-11-08 21:54:44','2011-11-08 21:54:44'),
(100129,9,'demo_product_30_2.jpg','','','18/72/1.jpg','','2011-11-08 21:55:39','2011-11-08 21:55:39'),
(100130,9,'demo_product_24.jpg','','','18/72/2.jpg','','2011-11-08 21:59:23','2011-11-08 21:59:23'),
(100131,9,'demo_product_23.jpg','','','18/72/3.jpg','','2011-11-08 22:00:28','2011-11-08 22:00:28'),
(100132,9,'demo_product_05.jpg','','','18/72/4.jpg','','2011-11-08 22:01:48','2011-11-08 22:01:48'),
(100133,9,'demo_product_07.jpg','','','18/72/5.jpg','','2011-11-08 22:03:02','2011-11-08 22:03:02'),
(100134,9,'demo_product_08_3.jpg','','','18/72/6.jpg','','2011-11-08 22:04:14','2011-11-08 22:04:14'),
(100135,9,'demo_product_10_2.jpg','','','18/72/7.jpg','','2011-11-08 22:05:34','2011-11-08 22:05:34'),
(100136,9,'demo_product_47.png','','','18/72/8.png','','2011-11-08 22:06:59','2011-11-08 22:06:59'),
(100137,9,'demo_product_11_2.jpg','','','18/72/9.jpg','','2011-11-08 22:08:11','2011-11-08 22:08:11'),
(100138,9,'demo_product_40_2.jpg','','','18/72/a.jpg','','2011-11-08 22:10:13','2011-11-08 22:10:13'),
(100139,9,'demo_product_44.jpg','','','18/72/b.jpg','','2011-11-08 22:11:49','2011-11-08 22:11:49'),
(100140,9,'demo_product_29.jpg','','','18/72/c.jpg','','2011-11-08 22:13:13','2011-11-08 22:13:13'),
(100141,9,'demo_product_27.jpg','','','18/72/d.jpg','','2011-11-08 22:14:33','2011-11-08 22:14:33'),
(100142,9,'demo_product_42.jpg','','','18/72/e.jpg','','2011-11-08 22:16:11','2011-11-08 22:16:11'),
(100143,9,'demo_product_46.jpg','','','18/72/f.jpg','','2011-11-08 22:17:18','2011-11-08 22:17:18'),
(100144,9,'demo_product_18.jpg','','','18/73/0.jpg','','2011-11-08 22:18:43','2011-11-08 22:18:43'),
(100145,9,'demo_product_37.jpg','','','18/73/1.jpg','','2011-11-08 22:20:10','2011-11-08 22:20:10'),
(100146,9,'demo_product_49_1.png','','','18/73/2.png','','2011-11-08 22:21:44','2011-11-08 22:21:44'),
(100147,9,'store_logo.gif','','','18/73/3.gif','','2011-11-08 22:20:10','2011-11-08 22:20:10'),
(100148,9,'favicon.ico','','','18/73/4.ico','','2011-11-08 22:21:44','2011-11-08 22:21:44'),
(100150,9,'az_demo_product_51.png','','','18/73/6.png','','2012-03-14 14:27:02','2012-03-14 14:27:02'),
(100150,1,'az_demo_product_51.png','','','18/73/6.png','','2012-03-14 14:27:02','2012-03-14 14:27:02'),
(100178,9,'abantecart video','','','','<object width=\"640\" height=\"360\"><param name=\"movie\" value=\"http://www.youtube.com/v/IQ5SLJUWbdA\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/IQ5SLJUWbdA\" type=\"application/x-shockwave-flash\" width=\"640\" height=\"360\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed></object>','2012-03-16 07:07:34','2012-03-16 07:22:44'),
(100153,1,'demo_mf_gucci.jpg','','','18/73/9.jpg','','2012-03-15 06:48:08','2012-03-15 06:48:08'),
(100153,9,'demo_mf_gucci.jpg','','','18/73/9.jpg','','2012-03-15 06:48:08','2012-03-15 06:48:08'),
(100154,1,'az_demo_product_52_1.jpg','','','18/73/a.jpg','','2012-03-15 06:53:01','2012-03-15 06:53:01'),
(100154,9,'az_demo_product_52_1.jpg','','','18/73/a.jpg','','2012-03-15 06:53:01','2012-03-15 06:53:01'),
(100155,1,'az_demo_product_52_2.png','','','18/73/b.png','','2012-03-15 06:53:14','2012-03-15 06:53:14'),
(100155,9,'az_demo_product_52_2.png','','','18/73/b.png','','2012-03-15 06:53:14','2012-03-15 06:53:14'),
(100156,1,'az_demo_product_52_3.png','','','18/73/c.png','','2012-03-15 06:54:41','2012-03-15 06:54:41'),
(100156,9,'az_demo_product_52_3.png','','','18/73/c.png','','2012-03-15 06:54:41','2012-03-15 06:54:41'),
(100157,1,'az_demo_product_53_3.jpg','','','18/73/d.jpg','','2012-03-15 07:11:39','2012-03-15 07:11:39'),
(100157,9,'az_demo_product_53_3.jpg','','','18/73/d.jpg','','2012-03-15 07:11:39','2012-03-15 07:11:39'),
(100158,1,'az_demo_product_53_1.png','','','18/73/e.png','','2012-03-15 07:11:45','2012-03-15 07:11:45'),
(100158,9,'az_demo_product_53_1.png','','','18/73/e.png','','2012-03-15 07:11:45','2012-03-15 07:11:45'),
(100159,1,'az_demo_product_53_2.png','','','18/73/f.png','','2012-03-15 07:11:45','2012-03-15 07:11:45'),
(100159,9,'az_demo_product_53_2.png','','','18/73/f.png','','2012-03-15 07:11:45','2012-03-15 07:11:45'),
(100160,1,'az_demo_product_54_1.jpg','','','18/74/0.jpg','','2012-03-15 07:32:29','2012-03-15 07:32:29'),
(100160,9,'az_demo_product_54_1.jpg','','','18/74/0.jpg','','2012-03-15 07:32:29','2012-03-15 07:32:29'),
(100162,9,'az_demo_product_55_1.jpg','','','18/74/2.jpg','','2012-03-15 09:15:00','2012-03-15 09:15:00'),
(100162,1,'az_demo_product_55_1.jpg','','','18/74/2.jpg','','2012-03-15 09:15:00','2012-03-15 09:15:00'),
(100163,1,'az_demo_product_56_3.jpg','','','18/74/3.jpg','','2012-03-15 09:37:14','2012-03-15 09:37:14'),
(100163,9,'az_demo_product_56_3.jpg','','','18/74/3.jpg','','2012-03-15 09:37:14','2012-03-15 09:37:14'),
(100164,1,'az_demo_product_56_2.jpg','','','18/74/4.jpg','','2012-03-15 09:37:15','2012-03-15 09:37:15'),
(100164,9,'az_demo_product_56_2.jpg','','','18/74/4.jpg','','2012-03-15 09:37:15','2012-03-15 09:37:15'),
(100165,1,'az_demo_product_56_1.jpg','','','18/74/5.jpg','','2012-03-15 09:37:20','2012-03-15 09:37:20'),
(100165,9,'az_demo_product_56_1.jpg','','','18/74/5.jpg','','2012-03-15 09:37:20','2012-03-15 09:37:20'),
(100166,1,'az_demo_product_57_1.jpg','','','18/74/6.jpg','','2012-03-15 09:57:29','2012-03-15 09:57:29'),
(100166,9,'az_demo_product_57_1.jpg','','','18/74/6.jpg','','2012-03-15 09:57:29','2012-03-15 09:57:29'),
(100167,1,'az_demo_product_57_2.jpg','','','18/74/7.jpg','','2012-03-15 09:57:33','2012-03-15 09:57:33'),
(100167,9,'az_demo_product_57_2.jpg','','','18/74/7.jpg','','2012-03-15 09:57:33','2012-03-15 09:57:33'),
(100168,1,'az_demo_product_58_1.jpg','','','18/74/8.jpg','','2012-03-15 10:17:25','2012-03-15 10:17:25'),
(100168,9,'az_demo_product_58_1.jpg','','','18/74/8.jpg','','2012-03-15 10:17:25','2012-03-15 10:17:25'),
(100169,1,'az_demo_product_58_3.jpg','','','18/74/9.jpg','','2012-03-15 10:17:47','2012-03-15 10:17:47'),
(100169,9,'az_demo_product_58_3.jpg','','','18/74/9.jpg','','2012-03-15 10:17:47','2012-03-15 10:17:47'),
(100170,1,'az_demo_product_58_4.jpg','','','18/74/a.jpg','','2012-03-15 10:17:48','2012-03-15 10:17:48'),
(100170,9,'az_demo_product_58_4.jpg','','','18/74/a.jpg','','2012-03-15 10:17:48','2012-03-15 10:17:48'),
(100171,1,'az_demo_product_58_2.jpg','','','18/74/b.jpg','','2012-03-15 10:17:48','2012-03-15 10:17:48'),
(100171,9,'az_demo_product_58_2.jpg','','','18/74/b.jpg','','2012-03-15 10:17:48','2012-03-15 10:17:48'),
(100172,1,'Visionnaire.zip','','','18/74/c.zip','','2012-03-15 10:43:06','2012-03-15 10:43:06'),
(100172,9,'Visionnaire.zip','','','18/74/c.zip','','2012-03-15 10:43:06','2012-03-15 10:43:06'),
(100173,1,'az_demo_product_59_1.jpg','','','18/74/d.jpg','','2012-03-15 10:44:32','2012-03-15 10:44:32'),
(100173,9,'az_demo_product_59_1.jpg','','','18/74/d.jpg','','2012-03-15 10:44:32','2012-03-15 10:44:32'),
(100174,1,'az_demo_product_60_1.jpg','','','18/74/e.jpg','','2012-03-15 11:08:58','2012-03-15 11:08:58'),
(100174,9,'az_demo_product_60_1.jpg','','','18/74/e.jpg','','2012-03-15 11:08:58','2012-03-15 11:08:58'),
(100175,1,'az_demo_product_60_2.jpg','','','18/74/f.jpg','','2012-03-15 11:09:28','2012-03-15 11:09:28'),
(100175,9,'az_demo_product_60_2.jpg','','','18/74/f.jpg','','2012-03-15 11:09:28','2012-03-15 11:09:28'),
(100176,1,'az_demo_product_60_5.jpg','','','18/75/0.jpg','','2012-03-15 11:09:30','2012-03-15 11:09:30'),
(100176,9,'az_demo_product_60_5.jpg','','','18/75/0.jpg','','2012-03-15 11:09:30','2012-03-15 11:09:30'),
(100178,1,'abantecart video','','','','<object width=\"640\" height=\"360\"><param name=\"movie\" value=\"http://www.youtube.com/v/IQ5SLJUWbdA\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/IQ5SLJUWbdA\" type=\"application/x-shockwave-flash\" width=\"640\" height=\"360\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed></object>','2012-03-16 07:07:34','2012-03-16 07:22:44');



--
-- Dumping data for table `resource_library`
--



INSERT INTO `ac_resource_library` 
VALUES 
(100010,1,'2011-11-08 19:54:53','2011-11-08 19:54:53'),
(100012,1,'2011-11-08 19:56:17','2011-11-08 19:56:17'),
(100011,1,'2011-11-08 19:54:53','2011-11-08 19:54:53'),
(100007,1,'2011-11-08 19:50:51','2011-11-08 19:50:51'),
(100008,1,'2011-11-08 19:50:51','2011-11-08 19:50:51'),
(100009,1,'2011-11-08 19:50:53','2011-11-08 19:50:53'),
(100013,1,'2011-11-08 20:03:00','2011-11-08 20:03:00'),
(100014,1,'2011-11-08 20:07:30','2011-11-08 20:07:30'),
(100015,1,'2011-11-08 20:07:30','2011-11-08 20:07:30'),
(100016,1,'2011-11-08 20:07:32','2011-11-08 20:07:32'),
(100017,1,'2011-11-08 20:07:35','2011-11-08 20:07:35'),
(100018,1,'2011-11-08 20:08:50','2011-11-08 20:08:50'),
(100019,1,'2011-11-08 20:08:51','2011-11-08 20:08:51'),
(100020,1,'2011-11-08 20:08:52','2011-11-08 20:08:52'),
(100021,1,'2011-11-08 20:09:57','2011-11-08 20:09:57'),
(100022,1,'2011-11-08 20:11:34','2011-11-08 20:11:34'),
(100023,1,'2011-11-08 20:12:28','2011-11-08 20:12:28'),
(100024,1,'2011-11-08 20:12:28','2011-11-08 20:12:28'),
(100025,1,'2011-11-08 20:13:15','2011-11-08 20:13:15'),
(100026,1,'2011-11-08 20:14:15','2011-11-08 20:14:15'),
(100027,1,'2011-11-08 20:14:17','2011-11-08 20:14:17'),
(100028,1,'2011-11-08 20:14:21','2011-11-08 20:14:21'),
(100029,1,'2011-11-08 20:16:05','2011-11-08 20:16:05'),
(100030,1,'2011-11-08 20:17:37','2011-11-08 20:17:37'),
(100031,1,'2011-11-08 20:18:42','2011-11-08 20:18:42'),
(100032,1,'2011-11-08 20:19:46','2011-11-08 20:19:46'),
(100033,1,'2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100034,1,'2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100035,1,'2011-11-08 20:20:41','2011-11-08 20:20:41'),
(100036,1,'2011-11-08 20:21:47','2011-11-08 20:21:47'),
(100037,1,'2011-11-08 20:22:54','2011-11-08 20:22:54'),
(100038,1,'2011-11-08 20:22:59','2011-11-08 20:22:59'),
(100039,1,'2011-11-08 20:22:59','2011-11-08 20:22:59'),
(100040,1,'2011-11-08 20:23:00','2011-11-08 20:23:00'),
(100041,1,'2011-11-08 20:23:00','2011-11-08 20:23:00'),
(100042,1,'2011-11-08 20:23:04','2011-11-08 20:23:04'),
(100043,1,'2011-11-08 20:23:04','2011-11-08 20:23:04'),
(100044,1,'2011-11-08 20:24:16','2011-11-08 20:24:16'),
(100045,1,'2011-11-08 20:24:20','2011-11-08 20:24:20'),
(100046,1,'2011-11-08 20:24:59','2011-11-08 20:24:59'),
(100047,1,'2011-11-08 20:26:07','2011-11-08 20:26:07'),
(100048,1,'2011-11-08 20:27:05','2011-11-08 20:27:05'),
(100049,1,'2011-11-08 20:28:16','2011-11-08 20:28:16'),
(100050,1,'2011-11-08 20:29:29','2011-11-08 20:29:29'),
(100051,1,'2011-11-08 20:29:31','2011-11-08 20:29:31'),
(100052,1,'2011-11-08 20:30:22','2011-11-08 20:30:22'),
(100053,1,'2011-11-08 20:31:38','2011-11-08 20:31:38'),
(100054,1,'2011-11-08 20:32:33','2011-11-08 20:32:33'),
(100055,1,'2011-11-08 20:32:34','2011-11-08 20:32:34'),
(100056,1,'2011-11-08 20:32:35','2011-11-08 20:32:35'),
(100057,1,'2011-11-08 20:33:31','2011-11-08 20:33:31'),
(100058,1,'2011-11-08 20:34:54','2011-11-08 20:34:54'),
(100059,1,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100060,1,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100061,1,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100062,1,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100063,1,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100064,1,'2011-11-08 20:43:53','2011-11-08 20:43:53'),
(100065,1,'2011-11-08 20:45:33','2011-11-08 20:45:33'),
(100066,1,'2011-11-08 20:45:35','2011-11-08 20:45:35'),
(100067,1,'2011-11-08 20:45:37','2011-11-08 20:45:37'),
(100068,1,'2011-11-08 20:45:38','2011-11-08 20:45:38'),
(100069,1,'2011-11-08 20:48:44','2011-11-08 20:48:44'),
(100070,1,'2011-11-08 20:48:57','2011-11-08 20:48:57'),
(100071,1,'2011-11-08 20:50:08','2011-11-08 20:50:08'),
(100072,1,'2011-11-08 20:50:09','2011-11-08 20:50:09'),
(100073,1,'2011-11-08 20:50:09','2011-11-08 20:50:09'),
(100074,1,'2011-11-08 20:50:11','2011-11-08 20:50:11'),
(100075,1,'2011-11-08 20:51:24','2011-11-08 20:51:24'),
(100076,1,'2011-11-08 20:52:17','2011-11-08 20:52:17'),
(100077,1,'2011-11-08 20:52:18','2011-11-08 20:52:18'),
(100078,1,'2011-11-08 20:52:18','2011-11-08 20:52:18'),
(100079,1,'2011-11-08 20:53:20','2011-11-08 20:53:20'),
(100080,1,'2011-11-08 20:56:09','2011-11-08 20:56:09'),
(100081,1,'2011-11-08 20:56:10','2011-11-08 20:56:10'),
(100082,1,'2011-11-08 20:57:14','2011-11-08 20:57:14'),
(100083,1,'2011-11-08 21:00:11','2011-11-08 21:00:11'),
(100084,1,'2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100085,1,'2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100086,1,'2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100087,1,'2011-11-08 21:02:04','2011-11-08 21:02:04'),
(100088,1,'2011-11-08 21:04:09','2011-11-08 21:04:09'),
(100089,1,'2011-11-08 21:05:06','2011-11-08 21:05:06'),
(100090,1,'2011-11-08 21:06:18','2011-11-08 21:06:18'),
(100091,1,'2011-11-08 21:07:20','2011-11-08 21:07:20'),
(100092,1,'2011-11-08 21:07:20','2011-11-08 21:07:20'),
(100093,1,'2011-11-08 21:07:24','2011-11-08 21:07:24'),
(100094,1,'2011-11-08 21:08:27','2011-11-08 21:08:27'),
(100095,1,'2011-11-08 21:08:33','2011-11-08 21:08:33'),
(100096,1,'2011-11-08 21:08:36','2011-11-08 21:08:36'),
(100097,1,'2011-11-08 21:09:38','2011-11-08 21:09:38'),
(100098,1,'2011-11-08 21:09:38','2011-11-08 21:09:38'),
(100099,1,'2011-11-08 21:09:39','2011-11-08 21:09:39'),
(100100,1,'2011-11-08 21:09:39','2011-11-08 21:09:39'),
(100101,1,'2011-11-08 21:10:52','2011-11-08 21:10:52'),
(100102,1,'2011-11-08 21:12:38','2011-11-08 21:12:38'),
(100103,1,'2011-11-08 21:12:40','2011-11-08 21:12:40'),
(100104,1,'2011-11-08 21:12:41','2011-11-08 21:12:41'),
(100105,1,'2011-11-08 21:14:19','2011-11-08 21:14:19'),
(100106,1,'2011-11-08 21:14:23','2011-11-08 21:14:23'),
(100107,1,'2011-11-08 21:14:26','2011-11-08 21:14:26'),
(100108,1,'2011-11-08 21:15:51','2011-11-08 21:15:51'),
(100109,1,'2011-11-08 21:15:53','2011-11-08 21:15:53'),
(100110,1,'2011-11-08 21:15:53','2011-11-08 21:15:53'),
(100111,1,'2011-11-08 21:25:03','2011-11-08 21:25:03'),
(100112,1,'2011-11-08 21:28:05','2011-11-08 21:28:05'),
(100113,1,'2011-11-08 21:28:22','2011-11-08 21:28:22'),
(100114,1,'2011-11-08 21:41:24','2011-11-08 21:41:24'),
(100115,1,'2011-11-08 21:42:16','2011-11-08 21:42:16'),
(100116,1,'2011-11-08 21:43:09','2011-11-08 21:43:09'),
(100117,1,'2011-11-08 21:43:50','2011-11-08 21:43:50'),
(100118,1,'2011-11-08 21:44:29','2011-11-08 21:44:29'),
(100119,1,'2011-11-08 21:45:15','2011-11-08 21:45:15'),
(100120,1,'2011-11-08 21:46:11','2011-11-08 21:46:11'),
(100121,1,'2011-11-08 21:47:02','2011-11-08 21:47:02'),
(100122,1,'2011-11-08 21:47:56','2011-11-08 21:47:56'),
(100123,1,'2011-11-08 21:49:35','2011-11-08 21:49:35'),
(100124,1,'2011-11-08 21:50:27','2011-11-08 21:50:27'),
(100125,1,'2011-11-08 21:51:24','2011-11-08 21:51:24'),
(100126,1,'2011-11-08 21:52:17','2011-11-08 21:52:17'),
(100127,1,'2011-11-08 21:53:41','2011-11-08 21:53:41'),
(100128,1,'2011-11-08 21:54:44','2011-11-08 21:54:44'),
(100129,1,'2011-11-08 21:55:39','2011-11-08 21:55:39'),
(100130,1,'2011-11-08 21:59:23','2011-11-08 21:59:23'),
(100131,1,'2011-11-08 22:00:28','2011-11-08 22:00:28'),
(100132,1,'2011-11-08 22:01:48','2011-11-08 22:01:48'),
(100133,1,'2011-11-08 22:03:02','2011-11-08 22:03:02'),
(100134,1,'2011-11-08 22:04:14','2011-11-08 22:04:14'),
(100135,1,'2011-11-08 22:05:34','2011-11-08 22:05:34'),
(100136,1,'2011-11-08 22:06:59','2011-11-08 22:06:59'),
(100137,1,'2011-11-08 22:08:11','2011-11-08 22:08:11'),
(100138,1,'2011-11-08 22:10:13','2011-11-08 22:10:13'),
(100139,1,'2011-11-08 22:11:49','2011-11-08 22:11:49'),
(100140,1,'2011-11-08 22:13:13','2011-11-08 22:13:13'),
(100141,1,'2011-11-08 22:14:33','2011-11-08 22:14:33'),
(100142,1,'2011-11-08 22:16:11','2011-11-08 22:16:11'),
(100143,1,'2011-11-08 22:17:18','2011-11-08 22:17:18'),
(100144,1,'2011-11-08 22:18:43','2011-11-08 22:18:43'),
(100145,1,'2011-11-08 22:20:10','2011-11-08 22:20:10'),
(100146,1,'2011-11-08 22:21:44','2011-11-08 22:21:44'),
(100147,1,'2011-11-08 22:20:10','2011-11-08 22:20:10'),
(100148,1,'2011-11-08 22:21:44','2011-11-08 22:21:44'),
(100150,1,'2012-03-14 14:27:02','2012-03-14 14:27:02'),
(100178,3,'2012-03-16 07:07:34','2012-03-16 07:07:34'),
(100153,1,'2012-03-15 06:48:08','2012-03-15 06:48:08'),
(100154,1,'2012-03-15 06:53:01','2012-03-15 06:53:01'),
(100155,1,'2012-03-15 06:53:14','2012-03-15 06:53:14'),
(100156,1,'2012-03-15 06:54:41','2012-03-15 06:54:41'),
(100157,1,'2012-03-15 07:11:39','2012-03-15 07:11:39'),
(100158,1,'2012-03-15 07:11:45','2012-03-15 07:11:45'),
(100159,1,'2012-03-15 07:11:45','2012-03-15 07:11:45'),
(100160,1,'2012-03-15 07:32:29','2012-03-15 07:32:29'),
(100162,1,'2012-03-15 09:15:00','2012-03-15 09:15:00'),
(100163,1,'2012-03-15 09:37:14','2012-03-15 09:37:14'),
(100164,1,'2012-03-15 09:37:15','2012-03-15 09:37:15'),
(100165,1,'2012-03-15 09:37:20','2012-03-15 09:37:20'),
(100166,1,'2012-03-15 09:57:29','2012-03-15 09:57:29'),
(100167,1,'2012-03-15 09:57:33','2012-03-15 09:57:33'),
(100168,1,'2012-03-15 10:17:25','2012-03-15 10:17:25'),
(100169,1,'2012-03-15 10:17:47','2012-03-15 10:17:47'),
(100170,1,'2012-03-15 10:17:48','2012-03-15 10:17:48'),
(100171,1,'2012-03-15 10:17:48','2012-03-15 10:17:48'),
(100172,5,'2012-03-15 10:43:06','2012-03-15 10:43:06'),
(100173,1,'2012-03-15 10:44:32','2012-03-15 10:44:32'),
(100174,1,'2012-03-15 11:08:58','2012-03-15 11:08:58'),
(100175,1,'2012-03-15 11:09:28','2012-03-15 11:09:28'),
(100176,1,'2012-03-15 11:09:30','2012-03-15 11:09:30');



--
-- Dumping data for table `resource_map`
--



INSERT INTO `ac_resource_map` 
VALUES 
(100012,'products',58,0,0,'2011-11-08 19:56:17','2011-11-08 19:56:17'),
(100014,'products',80,0,0,'2011-11-08 20:07:30','2011-11-08 20:07:30'),
(100013,'products',68,0,0,'2011-11-08 20:03:00','2011-11-08 20:03:00'),
(100015,'products',80,0,0,'2011-11-08 20:07:30','2011-11-08 20:07:30'),
(100011,'products',65,0,0,'2011-11-08 19:54:53','2011-11-08 19:54:53'),
(100010,'products',65,0,0,'2011-11-08 19:54:53','2011-11-08 19:54:53'),
(100007,'products',64,0,0,'2011-11-08 19:50:51','2011-11-08 19:50:51'),
(100008,'products',64,0,0,'2011-11-08 19:50:51','2011-11-08 19:50:51'),
(100009,'products',64,0,0,'2011-11-08 19:50:54','2011-11-08 19:50:54'),
(100016,'products',80,0,0,'2011-11-08 20:07:32','2011-11-08 20:07:32'),
(100017,'products',80,0,0,'2011-11-08 20:07:36','2011-11-08 20:07:36'),
(100018,'products',84,0,0,'2011-11-08 20:08:50','2011-11-08 20:08:50'),
(100019,'products',84,0,0,'2011-11-08 20:08:51','2011-11-08 20:08:51'),
(100020,'products',84,0,0,'2011-11-08 20:08:53','2011-11-08 20:08:53'),
(100021,'products',83,0,0,'2011-11-08 20:09:57','2011-11-08 20:09:57'),
(100022,'products',82,0,0,'2011-11-08 20:11:34','2011-11-08 20:11:34'),
(100023,'products',83,0,0,'2011-11-08 20:12:28','2011-11-08 20:12:28'),
(100024,'products',83,0,0,'2011-11-08 20:12:28','2011-11-08 20:12:28'),
(100025,'products',81,0,0,'2011-11-08 20:13:16','2011-11-08 20:13:16'),
(100026,'products',51,0,0,'2011-11-08 20:14:15','2011-11-08 20:14:15'),
(100027,'products',51,0,0,'2011-11-08 20:14:17','2011-11-08 20:14:17'),
(100028,'products',51,0,0,'2011-11-08 20:14:21','2011-11-08 20:14:21'),
(100029,'products',52,0,0,'2011-11-08 20:16:06','2011-11-08 20:16:06'),
(100030,'products',92,0,0,'2011-11-08 20:17:38','2011-11-08 20:17:38'),
(100031,'products',72,0,0,'2011-11-08 20:18:42','2011-11-08 20:18:42'),
(100032,'products',61,0,0,'2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100033,'products',61,0,0,'2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100034,'products',61,0,0,'2011-11-08 20:19:47','2011-11-08 20:19:47'),
(100035,'products',93,0,0,'2011-11-08 20:20:42','2011-11-08 20:20:42'),
(100036,'products',74,0,0,'2011-11-08 20:21:47','2011-11-08 20:21:47'),
(100037,'products',57,0,0,'2011-11-08 20:22:55','2011-11-08 20:22:55'),
(100038,'products',57,0,0,'2011-11-08 20:22:59','2011-11-08 20:22:59'),
(100039,'products',57,0,0,'2011-11-08 20:22:59','2011-11-08 20:22:59'),
(100040,'products',57,0,0,'2011-11-08 20:23:00','2011-11-08 20:23:00'),
(100041,'products',57,0,0,'2011-11-08 20:23:00','2011-11-08 20:23:00'),
(100042,'products',57,0,0,'2011-11-08 20:23:04','2011-11-08 20:23:04'),
(100043,'products',57,0,0,'2011-11-08 20:23:05','2011-11-08 20:23:05'),
(100044,'products',75,0,0,'2011-11-08 20:24:17','2011-11-08 20:24:17'),
(100045,'products',75,0,0,'2011-11-08 20:24:21','2011-11-08 20:24:21'),
(100046,'products',75,0,0,'2011-11-08 20:24:59','2011-11-08 20:24:59'),
(100047,'products',70,0,0,'2011-11-08 20:26:07','2011-11-08 20:26:07'),
(100048,'products',86,0,0,'2011-11-08 20:27:05','2011-11-08 20:27:05'),
(100049,'products',97,0,0,'2011-11-08 20:28:16','2011-11-08 20:28:16'),
(100050,'products',96,0,0,'2011-11-08 20:29:30','2011-11-08 20:29:30'),
(100051,'products',96,0,0,'2011-11-08 20:29:31','2011-11-08 20:29:31'),
(100052,'products',67,0,0,'2011-11-08 20:30:22','2011-11-08 20:30:22'),
(100053,'products',99,0,0,'2011-11-08 20:31:38','2011-11-08 20:31:38'),
(100054,'products',85,0,0,'2011-11-08 20:32:33','2011-11-08 20:32:33'),
(100055,'products',85,0,0,'2011-11-08 20:32:34','2011-11-08 20:32:34'),
(100056,'products',85,0,0,'2011-11-08 20:32:35','2011-11-08 20:32:35'),
(100057,'products',73,0,0,'2011-11-08 20:33:31','2011-11-08 20:33:31'),
(100058,'products',91,0,0,'2011-11-08 20:34:54','2011-11-08 20:34:54'),
(100059,'products',55,0,0,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100060,'products',55,0,0,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100061,'products',55,0,0,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100062,'products',55,0,0,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100063,'products',55,0,0,'2011-11-08 20:42:45','2011-11-08 20:42:45'),
(100064,'products',87,0,0,'2011-11-08 20:43:55','2011-11-08 20:43:55'),
(100065,'products',77,0,0,'2011-11-08 20:45:34','2011-11-08 20:45:34'),
(100066,'products',77,0,0,'2011-11-08 20:45:36','2011-11-08 20:45:36'),
(100067,'products',77,0,0,'2011-11-08 20:45:37','2011-11-08 20:45:37'),
(100068,'products',77,0,0,'2011-11-08 20:45:38','2011-11-08 20:45:38'),
(100069,'products',76,0,0,'2011-11-08 20:48:44','2011-11-08 20:48:44'),
(100070,'products',76,0,0,'2011-11-08 20:48:57','2011-11-08 20:48:57'),
(100071,'products',60,0,0,'2011-11-08 20:50:08','2011-11-08 20:50:08'),
(100072,'products',60,0,0,'2011-11-08 20:50:09','2011-11-08 20:50:09'),
(100073,'products',60,0,0,'2011-11-08 20:50:09','2011-11-08 20:50:09'),
(100074,'products',60,0,0,'2011-11-08 20:50:11','2011-11-08 20:50:11'),
(100075,'products',94,0,0,'2011-11-08 20:51:25','2011-11-08 20:51:25'),
(100076,'products',90,0,0,'2011-11-08 20:52:18','2011-11-08 20:52:18'),
(100077,'products',90,0,0,'2011-11-08 20:52:18','2011-11-08 20:52:18'),
(100078,'products',90,0,0,'2011-11-08 20:52:19','2011-11-08 20:52:19'),
(100079,'products',71,0,0,'2011-11-08 20:53:20','2011-11-08 20:53:20'),
(100080,'products',63,0,0,'2011-11-08 20:56:09','2011-11-08 20:56:09'),
(100081,'products',63,0,0,'2011-11-08 20:56:10','2011-11-08 20:56:10'),
(100082,'products',69,0,0,'2011-11-08 20:57:14','2011-11-08 20:57:14'),
(100083,'products',89,0,0,'2011-11-08 21:00:11','2011-11-08 21:00:11'),
(100084,'products',89,0,0,'2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100085,'products',89,0,0,'2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100086,'products',89,0,0,'2011-11-08 21:00:12','2011-11-08 21:00:12'),
(100087,'products',98,0,0,'2011-11-08 21:02:04','2011-11-08 21:02:04'),
(100088,'products',95,0,0,'2011-11-08 21:04:10','2011-11-08 21:04:10'),
(100089,'products',50,0,0,'2011-11-08 21:05:07','2011-11-08 21:05:07'),
(100090,'products',100,0,0,'2011-11-08 21:06:18','2011-11-08 21:06:18'),
(100091,'products',66,0,0,'2011-11-08 21:07:20','2011-11-08 21:07:20'),
(100092,'products',66,0,0,'2011-11-08 21:07:20','2011-11-08 21:07:20'),
(100093,'products',66,0,0,'2011-11-08 21:07:25','2011-11-08 21:07:25'),
(100094,'products',53,0,0,'2011-11-08 21:08:27','2011-11-08 21:08:27'),
(100095,'products',53,0,0,'2011-11-08 21:08:33','2011-11-08 21:08:33'),
(100096,'products',53,0,0,'2011-11-08 21:08:36','2011-11-08 21:08:36'),
(100097,'products',59,0,0,'2011-11-08 21:09:38','2011-11-08 21:09:38'),
(100098,'products',59,0,0,'2011-11-08 21:09:39','2011-11-08 21:09:39'),
(100099,'products',59,0,0,'2011-11-08 21:09:39','2011-11-08 21:09:39'),
(100100,'products',59,0,0,'2011-11-08 21:09:40','2011-11-08 21:09:40'),
(100101,'products',56,0,0,'2011-11-08 21:10:52','2011-11-08 21:10:52'),
(100109,'products',78,0,0,'2011-11-08 21:15:53','2011-11-08 21:15:53'),
(100108,'products',78,0,0,'2011-11-08 21:15:52','2011-11-08 21:15:52'),
(100105,'products',79,0,0,'2011-11-08 21:14:19','2011-11-08 21:14:19'),
(100106,'products',79,0,0,'2011-11-08 21:14:24','2011-11-08 21:14:24'),
(100107,'products',79,0,0,'2011-11-08 21:14:27','2011-11-08 21:14:27'),
(100110,'products',78,0,0,'2011-11-08 21:15:53','2011-11-08 21:15:53'),
(100111,'products',88,0,0,'2011-11-08 21:25:03','2011-11-08 21:25:03'),
(100112,'products',62,0,0,'2011-11-08 21:28:05','2011-11-08 21:28:05'),
(100113,'products',62,0,0,'2011-11-08 21:28:23','2011-11-08 21:28:23'),
(100114,'manufacturers',16,0,0,'2011-11-08 21:41:24','2011-11-08 21:41:24'),
(100115,'manufacturers',14,0,0,'2011-11-08 21:42:16','2011-11-08 21:42:16'),
(100116,'manufacturers',13,0,0,'2011-11-08 21:43:09','2011-11-08 21:43:09'),
(100117,'manufacturers',12,0,0,'2011-11-08 21:43:50','2011-11-08 21:43:50'),
(100118,'manufacturers',11,0,0,'2011-11-08 21:44:29','2011-11-08 21:44:29'),
(100119,'manufacturers',15,0,0,'2011-11-08 21:45:15','2011-11-08 21:45:15'),
(100120,'manufacturers',17,0,0,'2011-11-08 21:46:11','2011-11-08 21:46:11'),
(100121,'manufacturers',18,0,0,'2011-11-08 21:47:02','2011-11-08 21:47:02'),
(100122,'manufacturers',19,0,0,'2011-11-08 21:47:56','2011-11-08 21:47:56'),
(100123,'categories',52,0,0,'2011-11-08 21:49:35','2011-11-08 21:49:35'),
(100124,'categories',36,0,0,'2011-11-08 21:50:27','2011-11-08 21:50:27'),
(100125,'categories',43,0,0,'2011-11-08 21:51:24','2011-11-08 21:51:24'),
(100126,'categories',49,0,0,'2011-11-08 21:52:17','2011-11-08 21:52:17'),
(100127,'categories',58,0,0,'2011-11-08 21:53:41','2011-11-08 21:53:41'),
(100128,'categories',50,0,0,'2011-11-08 21:54:44','2011-11-08 21:54:44'),
(100129,'categories',51,0,0,'2011-11-08 21:55:39','2011-11-08 21:55:39'),
(100130,'categories',53,0,0,'2011-11-08 21:59:24','2011-11-08 21:59:24'),
(100131,'categories',54,0,0,'2011-11-08 22:00:29','2011-11-08 22:00:29'),
(100132,'categories',38,0,0,'2011-11-08 22:01:49','2011-11-08 22:01:49'),
(100133,'categories',40,0,0,'2011-11-08 22:03:02','2011-11-08 22:03:02'),
(100134,'categories',41,0,0,'2011-11-08 22:04:15','2011-11-08 22:04:15'),
(100135,'categories',42,0,0,'2011-11-08 22:05:35','2011-11-08 22:05:35'),
(100136,'categories',39,0,0,'2011-11-08 22:07:00','2011-11-08 22:07:00'),
(100137,'categories',37,0,0,'2011-11-08 22:08:11','2011-11-08 22:08:11'),
(100138,'categories',59,0,0,'2011-11-08 22:10:13','2011-11-08 22:10:13'),
(100139,'categories',60,0,0,'2011-11-08 22:11:50','2011-11-08 22:11:50'),
(100140,'categories',61,0,0,'2011-11-08 22:13:14','2011-11-08 22:13:14'),
(100141,'categories',63,0,0,'2011-11-08 22:14:33','2011-11-08 22:14:33'),
(100142,'categories',46,0,0,'2011-11-08 22:16:13','2011-11-08 22:16:13'),
(100143,'categories',47,0,0,'2011-11-08 22:17:18','2011-11-08 22:17:18'),
(100144,'categories',44,0,0,'2011-11-08 22:18:44','2011-11-08 22:18:44'),
(100145,'categories',45,0,0,'2011-11-08 22:20:12','2011-11-08 22:20:12'),
(100146,'categories',48,0,0,'2011-11-08 22:21:44','2011-11-08 22:21:44'),
(100150,'products',101,0,0,'2012-03-14 14:27:02','2012-03-14 14:27:02'),
(100178,'products',101,0,0,'2012-03-16 07:07:34','2012-03-16 07:07:34'),
(100153,'manufacturers',20,0,0,'2012-03-15 06:48:08','2012-03-15 06:48:08'),
(100154,'products',102,0,0,'2012-03-15 06:53:01','2012-03-15 06:53:01'),
(100155,'products',102,0,0,'2012-03-15 06:53:14','2012-03-15 06:53:14'),
(100156,'products',102,0,0,'2012-03-15 06:54:41','2012-03-15 06:54:41'),
(100157,'products',103,0,2,'2012-03-15 07:11:40','2012-03-15 07:12:14'),
(100158,'products',103,0,1,'2012-03-15 07:11:45','2012-03-15 07:12:14'),
(100159,'products',103,0,3,'2012-03-15 07:11:46','2012-03-15 07:12:14'),
(100160,'products',104,0,0,'2012-03-15 07:32:30','2012-03-15 07:32:30'),
(100162,'products',105,0,0,'2012-03-15 09:15:00','2012-03-15 09:15:00'),
(100163,'products',106,0,2,'2012-03-15 09:37:14','2012-03-15 09:37:42'),
(100164,'products',106,0,2,'2012-03-15 09:37:15','2012-03-15 09:37:42'),
(100165,'products',106,0,1,'2012-03-15 09:37:20','2012-03-15 09:37:36'),
(100166,'products',107,0,0,'2012-03-15 09:57:29','2012-03-15 09:57:29'),
(100167,'products',107,0,0,'2012-03-15 09:57:34','2012-03-15 09:57:34'),
(100168,'products',108,0,0,'2012-03-15 10:17:25','2012-03-15 10:17:25'),
(100169,'products',108,0,0,'2012-03-15 10:17:47','2012-03-15 10:17:47'),
(100170,'products',108,0,0,'2012-03-15 10:17:48','2012-03-15 10:17:48'),
(100171,'products',108,0,0,'2012-03-15 10:17:48','2012-03-15 10:17:48'),
(100173,'products',109,0,0,'2012-03-15 10:44:32','2012-03-15 10:44:32'),
(100174,'products',110,0,0,'2012-03-15 11:08:58','2012-03-15 11:08:58'),
(100175,'products',110,0,0,'2012-03-15 11:09:28','2012-03-15 11:09:28'),
(100176,'products',110,0,0,'2012-03-15 11:09:31','2012-03-15 11:09:31');



--
-- Dumping data for table `reviews`
--



INSERT INTO `ac_reviews` 
VALUES 
(63,77,6,'Bernard Horne','I thought since it was made for men that it was the perfect thing to go with the body wash. Its too small and doesn\'t lather up very well.',3,1,'2011-08-31 10:52:11','2011-08-31 10:52:19'),
(62,54,2,'Juliana Davis','I\'ve been wearing all Lancome mascara\'s and I\'m just get really upset when I\'m out. I\'ve tried other Brands, but it\'s always right back to the Lancome productss. The extend L\'EXTREME is by far the best!!! Really Long and Great! ',5,1,'2011-08-31 10:34:34','2011-08-31 10:34:49'),
(61,56,0,'Cassandra','Fortunately, I got this as a gift. BUT, I am willing to purchase this when I run out. This may be expensive but it is sooooo worth it! I love this concealer and I wouldn\'t even dare to use other brands. One more thing, the little tube lasts for a long time. I\'ve been using it everyday for 8 months now and I still have about 1/4 left.',5,1,'2011-08-30 09:29:33','2011-08-30 09:30:03'),
(64,76,7,'James','Finally a deodorant for men that doesn\'t smell like cheap cologne. I\'ve been using this for a couple of weeks now and I can\'t say anything bad about it. To me it just smells fresh',4,1,'2011-08-31 11:02:40','2011-08-31 11:02:50'),
(65,100,0,'Juli','Smooth Silk is an accurate name for this creamy lip liner. It is by far the best lip pencil I have ever encountered.',5,1,'2011-09-07 05:42:25','2011-09-07 05:44:00'),
(66,100,0,'Marianne','Nice pencil! This is a smooth, long lasting pencil, wonderful shades!',4,1,'2011-09-07 05:43:50','2011-09-07 05:44:02'),
(67,97,0,'Ann','Really reduces shades and swellings)',4,1,'2011-09-07 05:47:24','2011-09-07 05:47:33'),
(68,99,0,'Alice','This is much darker than the picture',2,1,'2011-09-07 05:50:58','2011-09-07 05:51:11'),
(69,57,0,'Jane','When it arrived, the blush had cracked and was crumbling all over, so I\'m only able to use half of it.',2,1,'2011-09-07 05:53:02','2011-09-07 05:53:11'),
(70,55,0,'Kristin K.','These lipsticks are moisturizing and have good pigmentation; however, their lasting power is not as advertised! ',4,1,'2011-09-07 05:54:52','2011-09-07 05:55:01'),
(71,55,0,'lara','This is quite simply good stuff. \nThe color payout is rich, the texture creamy and moist, and best of all no scent. No taste.',5,1,'2011-09-07 05:56:11','2011-09-07 05:56:20'),
(72,93,0,'L. D.','I totally love it.it smells heavenly . It smells so natural and my skin just loves it. ',5,1,'2011-09-07 05:58:06','2011-09-07 05:58:16'),
(73,93,0,'Walton','This creme is a bit heavy for my skin; however, as the day goes on it does not create an oily build-up. A little goes a long way, and I could see improvements in my skin tone within a week. Good product, will be purchasing again.',4,1,'2011-09-07 05:59:34','2011-09-07 05:59:43'),
(74,74,0,'Stefania V','it works very well moisturing and cleaning and unlike many other healthy shampoos it doesn\'t open the hair platelets too far and therefore doesn\'t feel so dry and sticky so I can get away without using a conditioner. Great value.',4,1,'2011-09-07 06:02:30','2011-09-07 06:02:41'),
(75,102,0,'Mary','This is more of a evening fragrance. I love it',4,1,'2012-03-15 03:03:17','2012-03-15 03:03:32'),
(76,110,0,'Lara','Product was very reasonably priced. It will make a nice gift.',5,1,'2012-03-15 07:14:45','2012-03-15 07:15:04');



--
-- Dumping data for table `url_aliases`
--


INSERT INTO `ac_url_aliases` 
VALUES 
(494,'manufacturer_id=11',''),
(493,'category_id=36','makeup'),
(455,'content_id=4','about_us'),
(496,'category_id=64',''),
(492,'category_id=48',''),
(497,'product_id=101','pro-v_color_hair_solutions_color_preserve_shine_conditioner_with_pump'),
(498,'product_id=102','gucci_guilty'),
(500,'manufacturer_id=20','gucci'),
(501,'product_id=103','jasmin_noir_l\'essence_eau_de_parfum_spray'),
(503,'product_id=104','calvin_klein_obsession_for_women_edp_spray'),
(504,'product_id=105','bvlgari_aqua_eau_de_toilette_spray'),
(508,'product_id=106','omnia_eau_de_toilette'),
(509,'product_id=107','lancome_slimissime_360_slimming_activating_concentrate_unisex_treatment'),
(510,'product_id=108','lancome_hypnose_doll_lashes_mascara_4-piece_gift_set'),
(513,'product_id=109','lancome_visionnaire_advanced_skin_corrector'),
(514,'product_id=110','flora_by_gucci_eau_fraiche');



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-03-17 12:17:44
