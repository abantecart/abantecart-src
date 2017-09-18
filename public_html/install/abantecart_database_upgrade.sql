ALTER TABLE `ac_products` ADD COLUMN `stock_checkout` CHAR(1) DEFAULT '' AFTER `quantity`;

ALTER TABLE `ac_weight_classes` ADD COLUMN `iso_code` VARCHAR(5) NOT NULL AFTER `value`;
UPDATE `ac_weight_classes` SET iso_code = 'KILO' WHERE weight_class_id = 1;
UPDATE `ac_weight_classes` SET iso_code = 'GRAM' WHERE weight_class_id = 2;
UPDATE `ac_weight_classes` SET iso_code = 'PUND' WHERE weight_class_id = 5;
UPDATE `ac_weight_classes` SET iso_code = 'USOU' WHERE weight_class_id = 6;

ALTER TABLE `ac_length_classes` ADD COLUMN `iso_code` VARCHAR(5) NOT NULL AFTER `value`;
UPDATE `ac_length_classes` SET iso_code = 'CMET' WHERE length_class_id = 1;
UPDATE `ac_length_classes` SET iso_code = 'MMET' WHERE length_class_id = 2;
UPDATE `ac_length_classes` SET iso_code = 'INCH' WHERE length_class_id = 3;
