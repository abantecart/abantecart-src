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

class ControllerBlocksMegaMenu extends AController
{
    protected $category_id = 0;
    protected $path = [];
    protected $selected_root_id = [];
    protected $thumbnails = [], $featured = [];

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
    }

    public function main()
    {
        $request = $this->request->get;

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('blocks/menu');
        $this->loadLanguage('blocks/category');
        $this->loadLanguage('common/header');

        $this->data['categories'] = $this->buildCategories($request);

        //Show frames for generic block.
        $this->data['block_framed'] = true;
        $this->data['home_href'] =  $this->html->getHomeURL();

        $this->data['storefront_menu'] = $this->buildMenu();
        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function buildCategories($request){
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');

        //detect selected category
        if (isset($request['path'])) {
            $this->path = $this->data['path'] = explode('_', $request['path']);
            $this->category_id = $this->data['selected_category_id'] = end($this->path);
        }

        //load main level categories and filter categories without products
        $all_categories = array_filter(
            $mdl->getAllCategories(),
            function ($cat) { return (bool)$cat['product_count']; }
        );

        //build thumbnails list and featured products
        $category_ids = array_column($all_categories, 'category_id');
        $resource = new AResource('image');
        $this->thumbnails = $category_ids
            ? $resource->getMainThumbList(
                'categories',
                $category_ids,
                $this->config->get('config_image_category_width'),
                $this->config->get('config_image_category_height')
            )
            : [];
        $this->featured = $mdl->getFeaturedCategoryProducts($category_ids);
        $featuredProductsIds = [];
        foreach ($this->featured as $items) {
            $featuredProductsIds = array_merge($featuredProductsIds, array_column($items, 'product_id'));
        }

        $featuredProductsIds = array_unique($featuredProductsIds);
        $productThumbnails = $featuredProductsIds
            ? $resource->getMainThumbList(
                'products',
                $featuredProductsIds,
                $this->config->get('config_image_thumb_width'),
                $this->config->get('config_image_thumb_width')
            )
            : [];
        foreach ($this->featured as &$items) {
            foreach ($items as &$item)
                if ($productThumbnails[$item['product_id']]) {
                    $item['thumbnail'] = $productThumbnails[$item['product_id']];
                }
        }

        //Build category tree
        $categoryTree = $this->buildCategoryTree($all_categories);
        return $this->buildNestedCategoryList($categoryTree);
    }

    /** Function builds one dimensional category tree based on given array
     *
     * @param array $all_categories
     * @param int $parent_id
     * @param string $path
     *
     * @return array
     * @throws AException
     */
    protected function buildCategoryTree($all_categories = [], $parent_id = 0, $path = '')
    {
        $output = [];
        foreach ($all_categories as $category) {
            if ($parent_id != $category['parent_id']) {
                continue;
            }
            $category['path'] = $path ? $path . '_' . $category['category_id'] : $category['category_id'];
            $category['parents'] = explode("_", $category['path']);
            //dig into level
            $category['level'] = sizeof($category['parents']) - 1;
            if ($category['category_id'] == $this->category_id) {
                //mark root
                $this->selected_root_id = $category['parents'][0];
            }
            $output[] = $category;
            $output = array_merge(
                $output, $this->buildCategoryTree($all_categories, $category['category_id'], $category['path'])
            );
        }

        return $output;
    }

    /** Function builds one multi-dimensional (nested) category tree for menu
     *
     * @param int $parent_id
     *
     * @return array
     * @throws AException
     */
    protected function buildNestedCategoryList($categoryTree, $parent_id = 0)
    {
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->load->model('catalog/category');
        $output = [];
        foreach ($categoryTree as $category) {
            $category['current'] = false;
            if ($category['parent_id'] != $parent_id) {
                continue;
            }
            $category['children'] = $this->buildNestedCategoryList($categoryTree, $category['category_id']) ?? [];
            $category['featured_products'] = $this->featured[$category['category_id']] ?? [];
            $thumbnail = $this->thumbnails[$category['category_id']];
            $category['thumb'] = $thumbnail['thumb_url'];
            $category['icon'] = $thumbnail['resource_id'];
            $category['product_count'] = $mdl->getProductCount($category['category_id']);
            $category['href'] = $this->html->getSEOURL('product/category', '&path=' . $category['path'], '&encode');
            //mark current category
            if ($category['category_id'] == $this->category_id) {
                $category['current'] = true;
            }
            $output[] = $category;
        }
        return $output;
    }

    protected function buildMenu()
    {
        $cache_key = 'storefront_menu'.
            '.store_'.(int) $this->config->get('config_store_id')
            .'_lang_'.$this->config->get('storefront_language_id');
        $menu_items = $this->cache->pull($cache_key);
        if ($menu_items === false) {
            $menu = new AMenu_Storefront();
            $menu_items = $menu->getMenuItems();
            $this->cache->push($cache_key, $menu_items);
        }
        //build menu structure after caching. related to http/https urls
        return $this->session->data['storefront_menu'] = $this->prepareMenu($menu_items, '');
    }

    protected function prepareMenu($menu_items, $level = '')
    {
        $logged = $this->customer->isLogged();
        $menu = [];
        if ($level && empty($menu_items[$level])) {
            return $menu;
        }
        $lang_id = (int) $this->config->get('storefront_language_id');
        foreach ($menu_items[$level] as $item) {

            // is status not set - set it as an active
            if(!($item['settings']['status'] ?? 1)){
                continue;
            }

            if (($logged && $item['item_id'] == 'login')
                || (!$logged && $item['item_id'] == 'logout')
            ) {
                continue;
            }

            if (preg_match("/^http/i", $item ['item_url'])) {
                //process full URLs
                $href = $item ['item_url'];
            }
            elseif (preg_match("/^\.\.\//i", $item ['item_url'])) {
                //process relative url such as ../blog/index.php
                $href = str_replace('../', '', $item ['item_url']);
            } else {
                $href = $item ['item_url']=='#' ? '#' : $this->html->getSecureSEOURL($item ['item_url']);
            }
            $item['id'] = $item['item_id'];
            $item['icon'] = $item['item_icon'] ?? '';
            $item['icon_rl_id'] = $item['item_icon_rl_id'] ?? '';
            $item['href'] = $href;
            $item['text'] = $item['item_text'][$lang_id] ?? '';
            $item['children'] = $this->prepareMenu($menu_items, $item['item_id']);

            //if at least one child is current - mark parent as current too
            if($item['children']) {
                $item['current'] = in_array(true, array_column($item['children'], 'current'));
            }

            parse_str($item['item_url'], $urlParams);
            if(!$level && $item['category']){
                $item['current'] = in_array($urlParams['path'], $this->path);
            }elseif(isset($item['path'])) {
                $item['current'] = str_starts_with($this->request->get['path'], $item['path']);
            }elseif( !isset($item['current']) ){
                $item['current'] = array_key_first($urlParams) == $this->request->get['rt'];
            }
            $menu[] = $item;
        }
        return $menu;
    }
}