<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
	header('Location: static_pages/');
}

class AMenu_Storefront extends AMenu {

	protected $dataset_decription;
	protected $dataset_decription_rows;

	public function __construct() {

		$this->registry = Registry::getInstance();
		$this->db = $this->registry->get('db');

		$this->dataset = new ADataset ('menu', 'storefront');
		$this->dataset_decription = new ADataset ('menu', 'storefront_description');

		$this->_buildMenu();
	}

	protected function _buildMenu() {

		$this->dataset_rows = (array)$this->dataset->getRows();
		$this->dataset_decription_rows = (array)$this->dataset_decription->getRows();

		// need to resort by sort_order property
		$offset = 0; // it needs for process repeating sort numbers
		$tmp = $this->item_ids = array();
		foreach ($this->dataset_rows as $item) {

			foreach ($this->dataset_decription_rows as $description_item) {
				if ($description_item['item_id'] == $item['item_id']) {
					$item['item_text'][ $description_item['language_id'] ] = $description_item['item_text'];
				}
			}

			if (isset ($tmp [ $item ['parent_id'] ] [ $item['sort_order'] ])) {
				$offset++;
			}
			//mark current page
			$rt = $this->registry->get('request')->get_or_post('rt');
			if ($item['item_url'] == $rt ) {
				$item['current'] = true;
			}

			$tmp [ $item['parent_id'] ] [ $item['sort_order'] + $offset ] = $item;			
			$this->item_ids [ ] = $item['item_id'];

		}
		$menu = array();
		foreach ($tmp as $key => $item) {
			ksort($item);
			$menu [ $key ] = $item;
		}

		$this->menu_items = $menu;
	}

	/**
	 * Method return menu item properties as array
	 *
	 * @param string $item_id
	 * @return boolean|array
	 */
	public function getMenuItem($item_id) {

		$menu_item = false;

		foreach ($this->dataset_rows as $item) {
			if ($item_id == $item ['item_id']) {
				$menu_item = $item;
				break;
			}
		}
		// add text data
		foreach ($this->dataset_decription_rows as $item) {
			if ($item_id == $item ['item_id']) {
				$menu_item['item_text'][ $item['language_id'] ] = $item['item_text'];
			}
		}
		return $menu_item;
	}

	/**
	 * Method return list of all leaf menu items
	 *
	 * @return array
	 */
	public function getLeafMenus() {
		$return_arr = array();
		$all_parents = array();
		foreach ($this->dataset_rows as $item) {
			if ($item ['parent_id']) {
				$all_parents[ ] = $item['parent_id'];
			}
		}
		foreach ($this->dataset_rows as $item) {
			if (!in_array($item['item_id'], $all_parents)) {
				$return_arr[ $item['item_id'] ] = $item['item_id'];
			}
		}
		return $return_arr;
	}

	/**
	 * method inserts new item to the end of menu level
	 *
	 * @param array $item("item_id"=>"","parent_id"=>"","item_text"=>,"rt"=>"","sort_order"=>"", "item_type" => "")
	 * @throws AException
	 * @return boolean
	 */
	public function insertMenuItem($item = array()) {
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to change menu');
		}

		//clean text id 
		$item [ "item_id" ] = preformatTextID($item [ "item_id" ]);

		$check_array = array( "item_id", "item_icon", "item_text", "item_url", "parent_id", "sort_order", "item_type", "item_icon_rl_id" );

		if (!$item [ "item_id" ] || !$item [ "item_text" ] || sizeof(array_intersect($check_array, array_keys($item))) < 7) {
			return 'Error: Cannot add menu item because item array is wrong.';
		}

		if ($item ['parent_id'] && !in_array($item ['parent_id'], $this->item_ids)) {
			return 'Error: Cannot add menu item because parent "' . $item ['parent_id'] . '" is not exists';
		}

		if (!$item [ "sort_order" ]) {
			// we need to know last order number of children and set new for new item... yet
			$brothers = $this->getMenuChildren($item [ "parent_id" ]);
			$new_sort_order = 0;
			if ($brothers) {
				foreach ($brothers as $brother) {
					$new_sort_order = $brother ['sort_order'] > $new_sort_order ? $brother ['sort_order'] : $new_sort_order;
				}
			}
			$new_sort_order += 10;
			$item [ "sort_order" ] = $new_sort_order;

		}
		// concatenate parent_name with item name 
		if (!$item ['item_type']) {
			$item ['item_type'] = 'extension';
		}
		// checks for unique item_id				
		if (in_array($item [ "item_id" ], $this->item_ids)) {
			return 'Error: Cannot to add menu item because item with item_id "' . $item [ "item_id" ] . '" is already exists.';
		}
		$row = $item;
		unset($row['item_text']);
		//insert row in storefront
		$result = $this->dataset->addRows(array( $row ));

		//insert language data in storefront_description
		$item_text = array();
		foreach ($item['item_text'] as $language_id => $text) {
			$item_text[ ] = array(
				'item_id' => $item['item_id'],
				'language_id' => $language_id,
				'item_text' => $text,
			);
		}
		$this->dataset_decription->addRows($item_text);

		// rebuild menu var after changing
		$this->_buildMenu();
		$this->registry->get('cache')->delete('storefront_menu');
		return $result;
	}

	/*
	 * method delete menu item by id (name)
	 * 
	 * @param string $item_name
	 * @return boolean
	 */
	public function deleteMenuItem($item_id) {
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to change menu');
		}
		//
		$this->dataset->deleteRows(array( "column_name" => "item_id", "operator" => "=", "value" => $item_id ));
		$this->dataset_decription->deleteRows(array( "column_name" => "item_id", "operator" => "=", "value" => $item_id ));
		$this->_buildMenu();
		$this->registry->get('cache')->delete('storefront_menu');
		return true;
	}

	/*
	 * Мethod update menu item by condition (see ADataset->updateRow) 
	 *
	 * @param string $item_name
	 * @return boolean
	 */
	public function updateMenuItem($item_id, $new_values) {

		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to change menu');
		}

		if (empty ($new_values) || !$item_id) {
			return false;
		}

		//update row in storefront
		$row = $new_values;
		unset($row['item_text']);
		if (!empty($row)) {
			$this->dataset->updateRows(array( "column_name" => "item_id", "operator" => "=", "value" => $item_id ), $row);
		}

		if (!empty($new_values['item_text'])) {
			//insert language data in storefront_description
			// not possible to get data for certain item id and lang id
			// get all languages for item and update them
			$item_text = $this->dataset_decription->searchRows(array( "column_name" => "item_id", "operator" => "=", "value" => $item_id ));

			foreach ($new_values['item_text'] as $language_id => $text) {
				foreach ($item_text as $id => $item) {
					if ($item['language_id'] == $language_id) {
						$item_text[ $id ]['item_text'] = $text;
						break;
					}
				}
			}
			$this->dataset_decription->deleteRows(array( "column_name" => "item_id", "operator" => "=", "value" => $item_id ));
			$this->dataset_decription->addRows($item_text);

		}

		$this->_buildMenu();
		$this->registry->get('cache')->delete('storefront_menu');
		return true;
	}

	/**
	 * update dataset_description - add new language menu names
	 *
	 * @param $language_id
	 * @param array $data
	 * @throws AException
	 * @return void
	 */
	public function addLanguage($language_id, $data = array()) {

		$data = !is_array($data) ? array() : $data;

		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to change menu');
		}

		$config = $this->registry->get('config');
		foreach ($this->dataset_rows as $item) {
			$item_rt[ $item['item_id'] ] = $item['item_url'];
		}

		//insert language data in storefront_description
		$item_text = array();
		foreach ($this->dataset_decription_rows as $row) {
			if ($row['language_id'] == $config->get('storefront_language_id')) {
				if (isset($data[ $item_rt[ $row['item_id'] ] ])) {
					$text = $data[ $item_rt[ $row['item_id'] ] ];
				} else {
					$text = $row['item_text'];
				}
				$item_text[ ] = array(
					'item_id' => $row['item_id'],
					'language_id' => $language_id,
					'item_text' => $text,
				);
			}
		}

		$this->dataset_decription->addRows($item_text);
		$this->registry->get('cache')->delete('storefront_menu');
	}

	/**
	 * update dataset_description - delete language menu names
	 *
	 * @param $language_id
	 * @throws AException
	 * @return void
	 */
	public function deleteLanguage($language_id) {
		if (!IS_ADMIN) { // forbid for non admin calls
			throw new AException (AC_ERR_LOAD, 'Error: permission denied to change menu');
		}
		$this->dataset_decription->deleteRows(array( "column_name" => "language_id", "operator" => "=", "value" => $language_id ));
		$this->registry->get('cache')->delete('storefront_menu');
	}

}