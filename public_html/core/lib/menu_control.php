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

class AMenu {
	/**
	 * registry to provide access to cart objects
	 *
	 * @var object Registry
	 */
	protected $registry;
	/**
	 * @var object
	 */
	protected $dataset;
	/**
	 * @var integer
	 */
	protected $dataset_id = 0;
	/**
	 * @var array
	 */
	protected $menu_items = array();
	/**
	 * array form quick search menu item without dig in menu levels
	 *
	 * @var array
	 */
	protected $dataset_rows = array();
	/**
	 * array for checking for unique menu item id
	 * @var array
	 */
	protected $item_ids = array();

	public function __construct($menu_name = '') {
		// check for admin
		if (!IS_ADMIN) {
			throw new AException (AC_ERR_LOAD, 'Error: Could not initialize AMenu class! Permission denied.');
		}

		$this->registry = Registry::getInstance();
		$this->db = $this->registry->get('db');
		//check for correct menu name
		if (!in_array($menu_name, array( 'admin', 'storefront' ))) {
			throw new AException (AC_ERR_LOAD, 'Error: Could not initialize AMenu class! Unknown menu name: "' . $menu_name . '"');
		}

		$this->dataset = new ADataset ('menu', $menu_name);
		$this->menu_items = $this->_build_menu($this->dataset->getRows());

	}

	protected function _build_menu($values) {

		// need to resort by sort_order property
		$offset = 0; // it needs for process repeating sort numbers
		$tmp = $this->item_ids = array();
		if (is_array($values)) {
			$rm = new AResourceManager();
			$rm->setType('image');
			$language_id = $this->registry->get('language')->getContentLanguageID();

			foreach ($values as &$item) {
				if($item['item_icon_rl_id']) {
					$r = $rm->getResource($item['item_icon_rl_id'], $language_id);
					$item['item_icon_code'] = $r['resource_code'];
				}
				if (isset ($tmp [ $item [ 'parent_id' ] ] [ $item [ 'sort_order' ] ])) {
					$offset++;
				}
				$tmp [ $item [ 'parent_id' ] ] [ $item [ 'sort_order' ] + $offset ] = $item;
				$this->item_ids [ ] = $item [ 'item_id' ];
			}
		} unset($item);

		$this->dataset_rows = $values;

		$menu = array();
		foreach ($tmp as $key => $item) {
			ksort($item);
			$menu [ $key ] = $item;
		}
		return $menu;
	}

	/**
	 * method return menu array with levels
	 * @return array
	 */
	public function getMenuItems() {
		return $this->menu_items;
	}

	/**
	 * Method return menu item properties as array
	 *
	 * @param string $item_id
	 * @return boolean|array
	 */
	public function getMenuItem($item_id) {
		foreach ($this->dataset_rows as $item) {
			if ($item_id == $item [ 'item_id' ]) {
				return $item;
			}
		}
		return false;
	}

	/**
	 * Method return menu item properties by RT or URL
	 *
	 * @param $rt string
	 * @return boolean|array
	 */
	public function getMenuByRT($rt) {
		foreach ($this->dataset_rows as $item) {
			if ($rt == $item ['item_url']) {
				return $item;
			}
		}
		return false;
	}

	/**
	 * return dataset object
	 *
	 * @return ADataset|object
	 */
	public function getDataset() {
		return $this->dataset;
	}

	/**
	 * Method return menu item id list
	 *
	 * @return array
	 */
	public function getItemIds() {
		return $this->item_ids;
	}

	/**
	 * method return all children of submeni by parent id
	 *
	 * @param string $parent_id
	 * @return boolean | array
	 */
	public function getMenuChildren($parent_id) {
		if (!$this->dataset_rows) {
			return false;
		}
		$result = array();
		foreach ($this->dataset_rows as $item) {
			if ($item [ 'parent_id' ] == $parent_id) {
				$result [ ] = $item;
			}
		}
		return $result;
	}

	/**
	 * Method return item id list of item which have children items
	 *
	 * @return array
	 */
	public function getMenuParentIds() {
		if (!$this->dataset_rows) {
			return false;
		}
		$result = array();
		foreach ($this->dataset_rows as $item) {
			if (!in_array($item [ 'parent_id' ], $result)) {
				$result [ ] = $item [ 'parent_id' ];
			}
		}
		return $result;
	}

	/**
	 * method inserts new item to the end of menu level
	 *
	 * @param array $item("item_id"=>"","parent_id"=>"","item_text"=>,"rt"=>"","sort_order"=>"", "item_type" => "")
	 * @return boolean
	 */
	public function insertMenuItem($item = array()) {

		$check_array = array( "item_id", "item_text", "item_url", "parent_id", "sort_order", "item_type", "item_icon_rl_id" );

		//clean text id 
		$item [ "item_id" ] = preformatTextID($item [ "item_id" ]);

		if (!$item [ 'item_type' ]) {
			$item [ 'item_type' ] = 'extension';
		}

		if (!$item [ "item_id" ] || !$item [ "item_text" ] || sizeof(array_intersect($check_array, array_keys($item))) < 6) {
			return 'Error: Cannot to add menu item because item array is wrong.';
		}

		if ($item [ 'parent_id' ] && !isset ($this->menu_items [ $item [ 'parent_id' ] ])) {
			return 'Error: Cannot to add menu item because parent "' . $item [ 'parent_id' ] . '" is not exists';
		}

		// then insert
		//when autosorting
		if (!$item [ "sort_order" ]) {
			// we need to know last order number of children and set new for new item... yet
			$brothers = $this->getMenuChildren($item [ "parent_id" ]);
			$new_sort_order = 0;
			if ($brothers) {
				foreach ($brothers as $brother) {
					$new_sort_order = $brother [ 'sort_order' ] > $new_sort_order ? $brother [ 'sort_order' ] : $new_sort_order;
				}
			}
			$new_sort_order++;
			$item [ "sort_order" ] = $new_sort_order;

		}

		// checks for unique item_id				
		if (in_array($item [ "item_id" ], $this->item_ids)) {
			return 'Error: Cannot to add menu item because item with item_id "' . $item [ "item_id" ] . '" is already exists.';
		}
		$result = $this->dataset->addRows(array( $item ));
		// rebuild menu var after changing
		$this->_build_menu($this->dataset->getRows());
		$this->registry->get('cache')->delete('admin_menu');
		return $result;
	}

	/*
	 * method delete menu item by id (name)
	 * 
	 * @param string $item_name
	 * @return boolean
	 */
	public function deleteMenuItem($item_id) {
		//
		$this->dataset->deleteRows(array( "column_name" => "item_id", "operator" => "=", "value" => $item_id ));
		$this->_build_menu($this->dataset->getRows());
		$this->registry->get('cache')->delete('admin_menu');
		return true;
	}

	/*
	 * Мethod update menu item by condition (see ADataset->updateRow) 
	 *
	 * @param string $item_name
	 * @return boolean
	 */
	public function updateMenuItem($item_id, $new_values) {

		if (empty ($new_values) || !$item_id) {
			return false;
		}

		$this->dataset->updateRows(array( "column_name" => "item_id", "operator" => "=", "value" => $item_id ), $new_values);
		$this->_build_menu($this->dataset->getRows());
		$this->registry->get('cache')->delete('admin_menu');
		return true;
	}
}