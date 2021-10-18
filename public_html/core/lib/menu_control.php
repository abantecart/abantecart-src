<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

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

class AMenu
{
    /**
     * registry to provide access to cart objects
     *
     * @var object Registry
     */
    protected $registry;
    /** @var ADB */
    protected $db;
    /** @var object */
    protected $dataset;
    /** @var integer */
    protected $dataset_id = 0;
    /** @var array */
    protected $menu_items = [];
    /**
     * array form quick search menu item without dig in menu levels
     *
     * @var array
     */
    protected $dataset_rows = [];
    /**
     * array for checking for unique menu item id
     *
     * @var array
     */
    protected $item_ids = [];

    /**
     * AMenu constructor.
     *
     * @param string $menu_name
     *
     * @throws AException
     */
    public function __construct($menu_name = '')
    {
        // check for admin
        if (!IS_ADMIN) {
            throw new AException (AC_ERR_LOAD, 'Error: Could not initialize AMenu class! Permission denied.');
        }

        $this->registry = Registry::getInstance();
        $this->db = $this->registry->get('db');
        //check for correct menu name
        if (!in_array($menu_name, ['admin', 'storefront'])) {
            throw new AException (
                AC_ERR_LOAD,
                'Error: Could not initialize AMenu class! Unknown menu name: "'.$menu_name.'"'
            );
        }

        $this->dataset = new ADataset ('menu', $menu_name);
        $rows = $this->dataset->getRows();
        $this->menu_items = $this->_build_menu($rows);
    }

    /**
     * @param array $values
     *
     * @return array
     * @throws AException
     */
    protected function _build_menu($values)
    {
        $output = $this->item_ids = [];
        if (is_array($values)) {
            // need to resort by sort_order property and exclude disabled extension items
            $enabled_extension = $this->registry->get('extensions')->getEnabledExtensions();
            $rm = new AResourceManager();
            $rm->setType('image');
            $language_id = $this->registry->get('language')->getContentLanguageID();
            $indexes = [];
            foreach ($values as &$item) {
                //checks for disabled extension
                if ($item ['item_type'] == 'extension') {
                    // looks for this name in enabled extensions list. if is not there - skip it
                    if (!$this->_find_itemId_in_extensions($item ['item_id'], $enabled_extension)) {
                        continue;
                    } else { // if all fine - loads language of extension for menu item text show
                        if (strpos($item ['item_url'], 'http') === false) {
                            $this->registry->get('load')->language($item ['item_id'].'/'.$item ['item_id'], 'silent');
                            $item['language'] = $item ['item_id'].'/'.$item ['item_id'];
                        }
                    }
                }

                if ($item['item_icon_rl_id']) {
                    $r = $rm->getResource($item['item_icon_rl_id'], $language_id);
                    $item['item_icon_code'] = $r['resource_code'];
                }
                $output [$item ['parent_id']] [] = $item;
                $indexes[$item ['parent_id']][] = $item ['sort_order'];
                $this->item_ids [] = $item ['item_id'];
            }

            foreach ($output as $parentId => &$rows) {
                array_multisort($indexes[$parentId], $rows, SORT_NUMERIC, SORT_ASC);
            }
        }

        $this->dataset_rows = $values;
        return $output;
    }

    /**
     * @param $item_id
     * @param $extension_list
     *
     * @return bool
     */
    private function _find_itemId_in_extensions($item_id, $extension_list)
    {
        if (in_array($item_id, $extension_list)) {
            return true;
        }
        foreach ($extension_list as $ext_id) {
            $pos = strpos($item_id, $ext_id);
            if ($pos === 0 && substr($item_id, strlen($ext_id), 1) == '_') {
                return true;
            }
        }
        return false;
    }

    /**
     * method return menu array with levels
     *
     * @return array
     */
    public function getMenuItems()
    {
        return $this->menu_items;
    }

    /**
     * Method return menu item properties as array
     *
     * @param string $item_id
     *
     * @return boolean|array
     */
    public function getMenuItem($item_id)
    {
        foreach ($this->dataset_rows as $item) {
            if ($item_id == $item ['item_id']) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Method return menu item properties by RT or URL
     *
     * @param $rt string
     *
     * @return boolean|array
     */
    public function getMenuByRT($rt)
    {
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
    public function getDataset()
    {
        return $this->dataset;
    }

    /**
     * Method return menu item id list
     *
     * @return array
     */
    public function getItemIds()
    {
        return $this->item_ids;
    }

    /**
     * method return all children of submeni by parent id
     *
     * @param string $parent_id
     *
     * @return boolean | array
     */
    public function getMenuChildren($parent_id)
    {
        if (!$this->dataset_rows) {
            return false;
        }
        $result = [];
        foreach ($this->dataset_rows as $item) {
            if ($item ['parent_id'] == $parent_id) {
                $result [] = $item;
            }
        }
        return $result;
    }

    /**
     * Method return item id list of item which have children items
     *
     * @return array|false
     */
    public function getMenuParentIds()
    {
        if (!$this->dataset_rows) {
            return false;
        }
        $result = [];
        foreach ($this->dataset_rows as $item) {
            if (!in_array($item ['parent_id'], $result)) {
                $result [] = $item ['parent_id'];
            }
        }
        return $result;
    }

    /**
     * method inserts new item to the end of menu level
     *
     * @param array $item ("item_id"=>"","parent_id"=>"","item_text"=>,"rt"=>"","sort_order"=>"", "item_type" => "")
     *
     * @return boolean
     * @throws AException
     */
    public function insertMenuItem($item = [])
    {
        $check_array = ["item_id", "item_text", "item_url", "parent_id", "sort_order", "item_type", "item_icon_rl_id"];

        //clean text id 
        $item ["item_id"] = preformatTextID($item ["item_id"]);

        if (!$item ['item_type']) {
            $item ['item_type'] = 'extension';
        }

        if (!$item ["item_id"] || !$item ["item_text"]
            || sizeof(array_intersect($check_array, array_keys($item))) < 6) {
            return 'Error: Cannot to add menu item because item array is wrong.';
        }

        if ($item ['parent_id'] && !in_array($item ['parent_id'], $this->item_ids)) {
            return 'Error: Cannot to add menu item because parent "'.$item ['parent_id'].'" is not exists';
        }

        // then insert
        //when auto-sorting
        if (!$item ["sort_order"]) {
            // we need to know last order number of children and set new for new item... yet
            $brothers = $this->getMenuChildren($item ["parent_id"]);
            $new_sort_order = 0;
            if ($brothers) {
                foreach ($brothers as $brother) {
                    $new_sort_order =
                        $brother ['sort_order'] > $new_sort_order ? $brother ['sort_order'] : $new_sort_order;
                }
            }
            $new_sort_order++;
            $item ["sort_order"] = $new_sort_order;
        }

        // checks for unique item_id
        if (in_array($item ["item_id"], $this->item_ids)) {
            return 'Error: Cannot to add menu item because item with item_id "'
                .$item ["item_id"].'" is already exists.';
        }
        $result = $this->dataset->addRows([$item]);
        // rebuild menu var after changing
        $this->_build_menu($this->dataset->getRows());
        $this->registry->get('cache')->remove('admin_menu');
        return $result;
    }

    /*
     * method delete menu item by id (name)
     * 
     * @param string $item_name
     * @return boolean
     */
    public function deleteMenuItem($item_id)
    {
        //
        $this->dataset->deleteRows(["column_name" => "item_id", "operator" => "=", "value" => $item_id]);
        $this->_build_menu($this->dataset->getRows());
        $this->registry->get('cache')->remove('admin_menu');
        return true;
    }

    /*
     * Мethod update menu item by condition (see ADataset->updateRow) 
     *
     * @param string $item_name
     * @return boolean
     */
    public function updateMenuItem($item_id, $new_values)
    {
        if (empty ($new_values) || !$item_id) {
            return false;
        }

        $this->dataset->updateRows(["column_name" => "item_id", "operator" => "=", "value" => $item_id], $new_values);
        $this->_build_menu($this->dataset->getRows());
        $this->registry->get('cache')->remove('admin_menu');
        return true;
    }
}