ALTER TABLE `ac_customers`
  ADD `wishlist` text COLLATE utf8_general_ci;

ALTER TABLE IF EXISTS `ac_customers_enc`
  ADD `wishlist` text COLLATE utf8_general_ci;
  
