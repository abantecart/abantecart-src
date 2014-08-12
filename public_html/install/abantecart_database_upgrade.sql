# fix of previous upgrade bug
alter table `ac_downloads` modify column `expire_days` int(11) null default null;


# add new menu item for resource library
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (10,'resource_library',17);
-- ITEM_TEXT
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (11,'text_resource_library',17);
-- ITEM_URL
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (12,'catalog/resource_library',17);
-- PARENT_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (13,'catalog',17);
-- SORT_ORDER
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_integer`,`row_id`)
VALUES (14,7,17);
-- ITEM_TYPE
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (15,'core',17);
-- ITEM_RL_ID
INSERT INTO `ac_dataset_values` (`dataset_column_id`, `value_varchar`,`row_id`)
VALUES (40,'<i class="fa fa-camera"></i>&nbsp;',17);