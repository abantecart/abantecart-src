ac_categories
ac_coupons
ac_customers
ac_customer_transactions
ac_online_customers
ac_downloads
ac_locations
ac_orders
ac_order_downloads
ac_order_data
ac_order_data_types
ac_order_history
ac_products
-ac_product_discounts
-ac_product_specials
ac_reviews
ac_tax_classes
ac_tax_rates
-ac_users
ac_zones_to_locations
ac_ant_messages

DELIMITER ;;
CREATE TRIGGER `ac_customer_transactions_trg` BEFORE INSERT ON `ac_customer_transactions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_extensions_trg` BEFORE INSERT ON `ac_extensions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_banners_trg` BEFORE INSERT ON `ac_banners` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_banner_descriptions_trg` BEFORE INSERT ON `ac_banner_descriptions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_language_definitions_trg` BEFORE INSERT ON `ac_language_definitions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_user_groups_trg` BEFORE INSERT ON `ac_user_groups` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_pages_trg` BEFORE INSERT ON `ac_pages` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_page_descriptions_trg` BEFORE INSERT ON `ac_page_descriptions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_content_descriptions_trg` BEFORE INSERT ON `ac_content_descriptions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_blocks_trg` BEFORE INSERT ON `ac_blocks` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_custom_blocks_trg` BEFORE INSERT ON `ac_custom_blocks` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_custom_lists_trg` BEFORE INSERT ON `ac_custom_lists` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_block_descriptions_trg` BEFORE INSERT ON `ac_block_descriptions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_block_templatess_trg` BEFORE INSERT ON `ac_block_templates` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_layouts_trg` BEFORE INSERT ON `ac_layouts` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_block_layouts_trg` BEFORE INSERT ON `ac_block_layouts` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_messages_trg` BEFORE INSERT ON `ac_messages` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_resource_library_trg` BEFORE INSERT ON `ac_resource_library` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_resource_descriptions_trg` BEFORE INSERT ON `ac_resource_descriptions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_resource_map_trg` BEFORE INSERT ON `ac_resource_map` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_global_attributes_type_descriptions_trg` BEFORE INSERT ON `ac_global_attributes_type_descriptions` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_tasks_trg` BEFORE INSERT ON `ac_tasks` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_task_details_trg` BEFORE INSERT ON `ac_task_details` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_task_steps_trg` BEFORE INSERT ON `ac_task_steps` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;

DELIMITER ;;
CREATE TRIGGER `ac_settings_trg` BEFORE INSERT ON `ac_settings` FOR EACH ROW
BEGIN
    SET NEW.date_added = NOW();
END;;
DELIMITER ;
