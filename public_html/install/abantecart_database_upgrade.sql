ALTER TABLE `ac_product_descriptions`
ADD COLUMN `blurb` TEXT COLLATE utf8_general_ci  NOT NULL COMMENT 'translatable';

INSERT INTO `ac_settings` (`group`, `key`, `value`) VALUES
('general','config_embed_status',1),
('general','config_embed_click_action', 'modal');

