CREATE INDEX `ac_products_status_idx` ON `abc_products` (`product_id`, `status`, `date_available`);
CREATE INDEX `ac_product_descriptions_name_idx` ON `ac_product_descriptions` (`product_id`, `name`);

CREATE INDEX `ac_resource_library_idx` ON `ac_resource_library` ( `resource_id`, `type_id`);

CREATE INDEX `ac_resource_map_sorting_idx` ON `ac_resource_map` ( `resource_id`, `sort_order`);

CREATE INDEX `ac_resource_descriptions_name_idx` ON `ac_resource_descriptions` ( `resource_id`, `name`);
CREATE INDEX `ac_resource_descriptions_title_idx` ON `ac_resource_descriptions` ( `resource_id`, `title`);
