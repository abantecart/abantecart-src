<?php
/**
* @var $this APackageManager
*/

//insert download attribute types
$this->db->query("INSERT INTO `".DB_PREFIX."ac_global_attributes_types` (`type_key`, `controller`, `sort_order`, `status`) VALUES
							('download_attribute', 'Download Attribute', 'responses/catalog/attribute/getDownloadAttributeSubform', 2, 1);");
$attr_id = $this->db->getLastId();

$this->db->query("INSERT INTO `".DB_PREFIX."global_attributes_type_descriptions` (`attribute_id`,`language_id`, `type_name`, `create_date`)
				VALUES ('".$attr_id."', 1, 'Download Attribute', NOW()),
       					('".$attr_id."', 9, 'Descargar Atributo', NOW());");