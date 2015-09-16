update `ac_dataset_values` set value_varchar = 'design/template' where dataset_column_id = 12 and row_id = 131;

alter table `ac_blocks` modify column `block_txt_id` varchar(255) NOT NULL;
alter table `ac_blocks` modify column `controller` varchar(255) NOT NULL;
alter table `ac_block_templates` modify column `template` varchar(255) NOT NULL;

UPDATE `ac_dataset_values`
SET value_varchar = 'window.open(\'http://docs.abantecart.com\');'
WHERE value_varchar = 'window.open(\'http://www.abantecart.com/ecommerce-documentation\');';