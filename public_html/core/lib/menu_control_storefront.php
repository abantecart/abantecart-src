<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class AMenu_Storefront extends AMenu
{
    protected $cache;
    protected $dataset_description;
    protected $dataset_description_rows;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
        $this->db = $this->registry->get('db');
        $this->cache = $this->registry->get('cache');

        $this->dataset = new ADataset ('menu', 'storefront');
        $this->dataset_description = new ADataset ('menu', 'storefront_description');

        $this->_buildMenu();
    }

    protected function _buildMenu()
    {
        $languageId = $this->registry->get('language')->getContentLanguageID();
        $storeId = (int)$this->registry->get('session')->data['current_store_id'];
        $this->dataset_rows = (array) $this->dataset->getRows();
        $this->dataset_description_rows = (array) $this->dataset_description->getRows();

        /** @var ModelCatalogCategory $categoryMdl */
        $categoryMdl = $this->registry->get('load')->model('catalog/category');

        /** @var ModelCatalogContent $contentMdl */
        $contentMdl = $this->registry->get('load')->model('catalog/content', 'storefront');

        // need to resort by sort_order property
        $tmp = $this->item_ids = [];
        foreach ($this->dataset_rows as $item) {
            $leaf = [];
            foreach ($this->dataset_description_rows as $description_item) {
                if ($description_item['item_id'] == $item['item_id']) {
                    $item['item_text'][$description_item['language_id']] = $description_item['item_text'];
                }
            }

            //Increment sort for child
            while (isset ($tmp[$leaf['parent_id']][$item['sort_order']])) {
                $item['sort_order']++;
            }

            //mark current page
            $rt = $this->registry->get('request')->get_or_post('rt');
            if ($item['item_url'] == $rt) {
                $item['current'] = true;
            }

            $item['item_url'] = html_entity_decode($item['item_url']);
            $item['settings'] = unserialize($item['settings']) ?: [];
            $this->item_ids[] = $item['item_id'];

            $isCategory = str_starts_with($item['item_url'], 'product/category&path=');
            if ($isCategory) {
                parse_str($item['item_url'], $qry);
                $ctgTrail = explode("_", $qry['path']);
                if (!$categoryMdl->getCategory(end($ctgTrail))) {
                    //skip if category not from current store
                    continue;
                }
                $item['category'] = true;
                //tree of subcategories
                if ($item['settings']['include_children'] && !$item['category_tree']
                ) {
                    $requestID = $this->registry->get('request')->get_or_post('path');
                    if (count($ctgTrail) && in_array($requestID, $ctgTrail)) {
                        $item['current'] = true;
                    }
                    //process leafs
                    $leaf = $this->addNestedCategoryItems($item, $languageId, $requestID);
                }
            }
            //tree of content menu
            $isContent = str_starts_with($item['item_url'],'content/content&content_id=');
            if( $isContent) {
                parse_str($item['item_url'], $qry);
                $contentId = (int)$qry['content_id'];
                if(!$contentMdl->getContent($contentId, $storeId, $languageId)){
                    continue;
                }

                if ($item['settings']['include_children'] && !$item['content_tree'] ) {
                    $leaf = $this->addNestedContentItems($item, $languageId);
                    $item['content'] = true;
                }
            }

            $tmp[$item['parent_id']][$item['sort_order']] = $item;

            if($leaf){
                //merge children with parent item
                foreach($leaf as $parentId => $child){
                    foreach($child as $sortOrder => $itm) {
                        while(isset ($output[$parentId][$sortOrder])) {
                            $sortOrder++;
                        }
                        $tmp[$parentId][$sortOrder] = $itm;
                    }
                }
            }
        }
        $menu = [];
        foreach ($tmp as $key => $item) {
            ksort($item);
            $menu [$key] = $item;
        }

        $this->menu_items = $menu;
    }

    /**
     * @param array $parentItem
     * @param int $languageId
     * @param int $requestId
     * @return array
     * @throws AException
     */
    protected function addNestedCategoryItems($parentItem, $languageId, $requestId)
    {
        $output = [];
        $resource = new AResource('image');

        parse_str($parentItem['item_url'], $qry);
        $path = explode("_",$qry['path']);
        $parentId = (int)end($path);
        if(!$parentId) {
            return $output;
        }
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->registry->get('load')->model('catalog/category');
        $childrenIDs = $mdl->getChildrenIDs($parentId);
        if(!$childrenIDs){
            return $output;
        }

        $leaf = $mdl->getCategoriesData(['filter_ids' => $childrenIDs]);
        if(!$leaf){
            return $output;
        }

        foreach ( $leaf as $k => $cat) {
            if (!$cat['products_count']) {
                unset($leaf[$k]);
            } else {
                $leafItem = $cat;
                $leafItem['path'] = $mdl->buildPath($cat['category_id']);
                $leafItem['category_tree'] = true;
                if ($cat['parent_id'] == $parentId) {
                    $leafItem['parent_id'] = $parentItem['item_id'];
                } else {
                    $pth = explode('_', $leafItem['path']);
                    array_pop($pth);
                    $leafItem['parent_id'] = $parentItem['item_id'] . '.' . implode('_', $pth);
                }
                $leafItem['item_id'] = $parentItem['item_id']
                    . ($leafItem['path'] ? '.' . $leafItem['path']:'');

                $leafItem['item_url'] = 'product/category&path=' . $leafItem['path'];
                $leafItem['settings']['target'] = $leafItem['settings']['target'] ?? $parentItem['settings']['target'];

                $leafItem['item_text'] = [$languageId => $cat['name']];
                $leafItem['sort_order'] = $cat['sort_order'];
                if ($cat['path'] == $requestId ) {
                    $leafItem['current'] = true;
                }
                $this->item_ids[] = $leafItem['item_id'];
                while(isset ($output[$leafItem['parent_id']][$leafItem['sort_order']])) {
                    $leafItem['sort_order']++;
                }

                $resources = $resource->getResources( 'categories', $cat['category_id'], $languageId );
                $leafItem['item_icon_rl_id'] = $resources[0]['resource_id'];
                $leafItem['resources'] = $resources;
                $output[$leafItem['parent_id']][$leafItem['sort_order']] = $leafItem;
            }
        }
        return $output;
    }

    protected function addNestedContentItems($parentItem, $languageId)
    {
        $output = [];
        /** @var ModelCatalogContent $mdl */
        $mdl = $this->registry->get('load')->model('catalog/content');
        parse_str($parentItem['item_url'], $qry);
        $parentId = $qry['content_id'];
        if(!$parentId) {
            return $output;
        }
        //build dynamic content (pages) links

        $mdl->getContents();
        $childrenIDs = $mdl->getChildrenIDs($parentId);

        if(!$childrenIDs){
            return $output;
        }
        $leaf = $mdl->getContents(['filter_ids' => $childrenIDs]);
        if(!$leaf){ return $output; }

        foreach ( $leaf as $cntnt) {
            $leafItem = $cntnt;
            $leafItem['content_tree'] = true;
            if ($cntnt['parent_content_id'] == $parentId) {
                $leafItem['parent_id'] = $parentItem['item_id'];
                $leafItem['item_id'] = $parentItem['item_id'] . '.' . $leafItem['content_id'];
            } else {
                $leafItem['item_id'] = $parentItem['item_id'] . '.' . $leafItem['content_id'];
                $leafItem['parent_id'] = $parentItem['item_id'] . '.' . $leafItem['parent_content_id'];
            }
            $leafItem['item_url'] = 'content/content&content_id=' . $leafItem['content_id'];
            $leafItem['item_text'] = [$languageId => $cntnt['name']];
            $leafItem['sort_order'] = $cntnt['sort_order'];
            $this->item_ids[] = $leafItem['item_id'];
            while(isset ($output[$leafItem['parent_id']][$leafItem['sort_order']])) {
                $leafItem['sort_order']++;
            }
            $output[$leafItem['parent_id']][$leafItem['sort_order']] = $leafItem;
        }
        return $output;
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
        $menu_item = false;

        foreach ($this->dataset_rows as $item) {
            if ($item_id == $item ['item_id']) {
                $menu_item = $item;
                break;
            }
        }
        // add text data
        foreach ($this->dataset_description_rows as $item) {
            if ($item_id == $item ['item_id']) {
                $menu_item['item_text'][$item['language_id']] = $item['item_text'];
            }
        }
        if(isset($menu_item['settings'])){
            $menu_item['settings'] = unserialize($menu_item['settings']);
        }
        return $menu_item;
    }

    /**
     * Method return list of all leaf menu items
     *
     * @return array
     */
    public function getLeafMenus()
    {
        $return_arr = [];
        $all_parents = [];
        foreach ($this->dataset_rows as $item) {
            if ($item ['parent_id']) {
                $all_parents[] = $item['parent_id'];
            }
        }
        foreach ($this->dataset_rows as $item) {
            if (!in_array($item['item_id'], $all_parents)) {
                $return_arr[$item['item_id']] = $item['item_id'];
            }
        }
        return $return_arr;
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
        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException (AC_ERR_LOAD, 'Error: permission denied to change menu');
        }

        //clean text id
        $item["item_id"] = preformatTextID($item ["item_id"]);
        $item['settings'] = !is_serialized($item['settings']) ? serialize($item['settings']) : $item['settings'];

        $check_array = [
            "item_id",
            "item_icon",
            "item_text",
            "item_url",
            "parent_id",
            "sort_order",
            "item_type",
            "item_icon_rl_id",
            "settings"
        ];

        if (!$item ["item_id"] || !$item ["item_text"]
            || sizeof(array_intersect($check_array, array_keys($item))) < 7) {
            return 'Error: Cannot add menu item because item array is wrong.';
        }

        if ($item ['parent_id'] && !in_array($item ['parent_id'], $this->item_ids)) {
            return 'Error: Cannot add menu item because parent "'.$item ['parent_id'].'" is not exists';
        }

        if (!$item ["sort_order"]) {
            // we need to know last order number of children and set new for new item... yet
            $brothers = $this->getMenuChildren($item ["parent_id"]);
            $new_sort_order = 0;
            if ($brothers) {
                foreach ($brothers as $brother) {
                    $new_sort_order = max($brother ['sort_order'], $new_sort_order);
                }
            }
            $new_sort_order += 10;
            $item ["sort_order"] = $new_sort_order;
        }
        // concatenate parent_name with item name
        if (!$item ['item_type']) {
            $item ['item_type'] = 'extension';
        }
        // checks for unique item_id
        if (in_array($item ["item_id"], $this->item_ids)) {
            return 'Error: Cannot to add menu item because item with item_id "'
                .$item ["item_id"].'" is already exists.';
        }
        $row = $item;
        unset($row['item_text']);
        //insert row in storefront
        $result = $this->dataset->addRows([$row]);

        //insert language data in storefront_description
        $item_text = [];
        foreach ($item['item_text'] as $language_id => $text) {
            $item_text[] = [
                'item_id'     => $item['item_id'],
                'language_id' => $language_id,
                'item_text'   => $text,
            ];
        }
        $this->dataset_description->addRows($item_text);

        // rebuild menu var after changing
        $this->_buildMenu();
        $this->cache->remove('storefront_menu');
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
        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException (AC_ERR_LOAD, 'Error: permission denied to change menu');
        }
        $this->dataset->deleteRows(
            [
                "column_name" => "item_id",
                "operator" => "=",
                "value" => $item_id
            ]
        );
        $this->dataset_description->deleteRows(
            [
                "column_name" => "item_id",
                "operator" => "=",
                "value" => $item_id
            ]
        );
        $this->_buildMenu();
        $this->cache->remove('storefront_menu');
        return true;
    }

    /*
     * Method update menu item by condition (see ADataset->updateRow)
     *
     * @param string $item_name
     * @return boolean
     */
    public function updateMenuItem($item_id, $new_values)
    {
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
            $this->dataset->updateRows(["column_name" => "item_id", "operator" => "=", "value" => $item_id], $row);
        }

        if (!empty($new_values['item_text'])) {
            //insert language data in storefront_description
            // not possible to get data for certain item id and lang id
            // get all languages for item and update them
            $item_text = $this->dataset_description->searchRows(
                [
                    "column_name" => "item_id",
                    "operator" => "=",
                    "value" => $item_id
                ]
            );

            foreach ($new_values['item_text'] as $language_id => $text) {
                foreach ($item_text as $id => $item) {
                    if ($item['language_id'] == $language_id) {
                        $item_text[$id]['item_text'] = $text;
                        break;
                    }
                }
            }
            $this->dataset_description->deleteRows(
                [
                    "column_name" => "item_id",
                    "operator" => "=",
                    "value" => $item_id
                ]
            );
            $this->dataset_description->addRows($item_text);
        }

        $this->_buildMenu();
        $this->cache->remove('storefront_menu');
        //for case
        $this->cache->remove('category');
        $this->cache->remove('content');
        return true;
    }

    /**
     * update dataset_description - add new language menu names
     *
     * @param       $language_id
     * @param array $data
     *
     * @return void
     * @throws AException
     */
    public function addLanguage($language_id, $data = [])
    {
        $data = !is_array($data) ? [] : $data;

        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException(
                AC_ERR_LOAD,
                'Error: permission denied to change menu'
            );
        }

        $config = $this->registry->get('config');
        $item_rt = [];
        foreach ($this->dataset_rows as $item) {
            $item_rt[$item['item_id']] = $item['item_url'];
        }

        //insert language data in storefront_description
        $item_text = [];
        foreach ($this->dataset_description_rows as $row) {
            if ($row['language_id'] == $config->get('storefront_language_id')) {
                $text = $data[$item_rt[$row['item_id']]] ?? $row['item_text'];
                $item_text[] = [
                    'item_id'     => $row['item_id'],
                    'language_id' => $language_id,
                    'item_text'   => $text,
                ];
            }
        }

        $this->dataset_description->addRows($item_text);
        $this->cache->remove('storefront_menu');
    }

    /**
     * update dataset_description - delete language menu names
     *
     * @param $language_id
     *
     * @return void
     * @throws AException
     */
    public function deleteLanguage($language_id)
    {
        if (!IS_ADMIN) { // forbid for non admin calls
            throw new AException(
                AC_ERR_LOAD,
                'Error: permission denied to change menu'
            );
        }
        $this->dataset_description->deleteRows(
            [
                "column_name" => "language_id",
                "operator" => "=",
                "value" => $language_id
            ]
        );
        $this->cache->remove('storefront_menu');
    }


    /**
     * Method return list of all child menu items
     *
     * @return array
     */
    public function getChildren($parentId)
    {
        $children = [];
        foreach ($this->dataset_rows as $item) {
            if ($item ['parent_id'] == $parentId) {
                $children[$item['item_id']] = $item;
                $children = array_merge($children, $this->getChildren($item['item_id']));
            }
        }
        return $children;
    }
}