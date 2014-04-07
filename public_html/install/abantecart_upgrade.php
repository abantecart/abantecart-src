<?php
/**
* @var $this APackageManager
*/

//add item to admin menu
$m = new AMenu('admin');
$m->insertMenuItem(
		array(
			'item_id' => 'languages',
			'item_text' => 'text_language',
			"item_url" => 'extension/extensions/language',
			"parent_id" => 'extension',
			"sort_order" => 5,
			"item_type" => 'core'));

//update extensions store menu items
$item = $m->deleteMenuItem('extensions_store');

$m->insertMenuItem(
		array(
			'item_id' => 'extensions_stores',
			'item_text' => 'text_extensions_store',
			"item_url" => 'extension/extensions_store',
			"parent_id" => 'extension',
			"sort_order" => 8,
			"item_type" => 'core'));

$m->insertMenuItem(
		array(
			'item_id' => 'extensions_store',
			'item_text' => 'text_extensions_store_new',
			"item_url" => 'extension/extensions_store',
			"parent_id" => 'extensions_stores',
			"sort_order" => 1,
			"item_type" => 'core'));

$m->insertMenuItem(
		array(
			'item_id' => 'extensions_store_prev',
			'item_text' => 'text_extensions_store_prev',
			"item_url" => 'extension/extensions_store_prev',
			"parent_id" => 'extensions_stores',
			"sort_order" => 2,
			"item_type" => 'core'));



//insert download attribute types
$this->db->query("INSERT INTO `".DB_PREFIX."global_attributes_types` (`type_key`, `controller`, `sort_order`, `status`) VALUES
							('download_attribute', 'responses/catalog/attribute/getDownloadAttributeSubform', 2, 1);");
$attr_id = $this->db->getLastId();

$this->db->query("INSERT INTO `".DB_PREFIX."global_attributes_type_descriptions` (`attribute_type_id`,`language_id`, `type_name`, `create_date`)
				VALUES ('".$attr_id."', 1, 'Download Attribute', NOW()),
       					('".$attr_id."', 9, 'Descargar Atributo', NOW());");