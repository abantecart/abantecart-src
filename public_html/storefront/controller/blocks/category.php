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

class ControllerBlocksCategory extends AController
{
    public $data = array();
    protected $category_id = 0;
    protected $path = array();
    protected $selected_root_id = array();
    protected $thumbnails = array();

    public function main()
    {
        $request = $this->request->get;

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //HTML cache only for non-customer
        if (!$this->customer->isLogged() && !$this->customer->isUnauthCustomer()) {
            $allowed_cache_keys = array('path');
            $cache_val = array('path' => $request['path']);
            $this->buildHTMLCacheKey($allowed_cache_keys, $cache_val);
            //disable cache when login display price setting is off or enabled showing of prices with taxes
            if (($this->config->get('config_customer_price') && !$this->config->get('config_tax'))
                && $this->html_cache()
            ) {
                return null;
            }
        }

        $this->view->assign('heading_title', $this->language->get('heading_title', 'blocks/category'));

        $this->loadModel('catalog/category');

        if (isset($request['path'])) {
            $this->path = explode('_', $request['path']);
            $this->category_id = end($this->path);
        }
        $this->view->assign('selected_category_id', $this->category_id);
        $this->view->assign('path', $request['path']);

        //load main lavel categories
        $all_categories = $this->model_catalog_category->getAllCategories();
        //build thumbnails list
        $category_ids = array();
        foreach ($all_categories as $category) {
            $category_ids[] = $category['category_id'];
        }
        $resource = new AResource('image');

        $this->thumbnails = $resource->getMainThumbList(
            'categories',
            $category_ids,
            $this->config->get('config_image_category_width'),
            $this->config->get('config_image_category_height')
        );

        //Build category tree
        $this->_buildCategoryTree($all_categories);
        $categories = $this->_buildNestedCategoryList();
        $this->view->assign('categories', $categories);

        //Framed needs to show frames for generic block.
        //If tpl used by listing block framed was set by listing block settings
        $this->view->assign('block_framed', true);
        $this->view->assign('home_href', $this->html->getHomeURL());

        $this->processTemplate();

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /** Function builds one dimentional category tree based on given array
     *
     * @param array  $all_categories
     * @param int    $parent_id
     * @param string $path
     *
     * @return array
     */
    private function _buildCategoryTree($all_categories = array(), $parent_id = 0, $path = '')
    {
        $output = array();
        foreach ($all_categories as $category) {
            if ($parent_id != $category['parent_id']) {
                continue;
            }
            $category['path'] = $path ? $path.'_'.$category['category_id'] : $category['category_id'];
            $category['parents'] = explode("_", $category['path']);
            //dig into level
            $category['level'] = sizeof($category['parents']) - 1;
            if ($category['category_id'] == $this->category_id) {
                //mark root
                $this->selected_root_id = $category['parents'][0];
            }
            $output[] = $category;
            $output = array_merge($output, $this->_buildCategoryTree($all_categories, $category['category_id'], $category['path']));
        }
        if ($parent_id == 0) {
            //place result into memory for future usage (for menu. see below)
            $this->data['all_categories'] = $output;
            // cut list and expand only selected tree branch
            $cutted_tree = array();
            foreach ($output as $category) {
                if ($category['parent_id'] != 0 && !in_array($this->selected_root_id, $category['parents'])) {
                    continue;
                }
                $category['href'] = $this->html->getSEOURL('product/category', '&path='.$category['path'], '&encode');
                $cutted_tree[] = $category;
            }
            return $cutted_tree;
        } else {
            return $output;
        }
    }

    /** Function builds one multi-dimensional (nested) category tree for menu
     *
     * @param int $parent_id
     *
     * @return array
     */
    private function _buildNestedCategoryList($parent_id = 0)
    {

        $output = array();
        foreach ($this->data['all_categories'] as $category) {
            if ($category['parent_id'] != $parent_id) {
                continue;
            }
            $category['children'] = $this->_buildNestedCategoryList($category['category_id']);
            $thumbnail = $this->thumbnails[$category['category_id']];
            $category['thumb'] = $thumbnail['thumb_url'];
            //get product counts from children levels. 
            if (count($category['children'])) {
                foreach ($category['children'] as $child) {
                    $category['product_count'] += $child['product_count'];
                }
            }
            $category['href'] = $this->html->getSEOURL('product/category', '&path='.$category['path'], '&encode');
            //mark current category
            if (in_array($category['category_id'], $this->path)) {
                $category['current'] = true;
            }
            $output[] = $category;
        }
        return $output;
    }

}
