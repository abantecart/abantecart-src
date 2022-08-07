ALTER TABLE `ac_product_discounts`
ADD COLUMN `price_prefix` CHAR(1) NOT NULL DEFAULT '' AFTER `priority`;

ALTER TABLE `ac_product_specials`
ADD COLUMN `price_prefix` CHAR(1) NOT NULL DEFAULT '' AFTER `priority`;

UPDATE `ac_email_templates`
    SET `text_id` = 'admin_reset_password_link'
WHERE  `text_id` = 'storefront_reset_password_link';
