
/*
updating language definitions table
*/

ALTER TABLE `ac_language_definitions` MODIFY `block` varchar(160) NOT NULL;
ALTER TABLE `ac_language_definitions` MODIFY `language_key` varchar(170) character set utf8 collate utf8_bin NOT NULL;
 /*need to add deleting duplicates from this table*/
DELETE FROM `ac_language_definitions`
WHERE `language_id`<'1' OR TRIM(`language_key`)='' OR TRIM(`language_value`)='' OR TRIM(`block`)='';


CREATE UNIQUE INDEX `lang_definition_index`
ON `ac_language_definitions` ( `section`,`block`,`language_id`,`language_key` );