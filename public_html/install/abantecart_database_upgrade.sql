ALTER TABLE `ac_store_descriptions`
CHANGE COLUMN `description` `description` LONGTEXT NULL DEFAULT '' COMMENT 'translatable' ,
CHANGE COLUMN `title` `title` LONGTEXT NULL DEFAULT '' COMMENT 'translatable' ,
CHANGE COLUMN `meta_description` `meta_description` LONGTEXT NULL DEFAULT '' COMMENT 'translatable' ,
CHANGE COLUMN `meta_keywords` `meta_keywords` LONGTEXT NULL DEFAULT '' COMMENT 'translatable' ;
