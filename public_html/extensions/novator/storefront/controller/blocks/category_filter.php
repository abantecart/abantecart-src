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

class ControllerBlocksCategoryFilter extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('novator/novator');
        $request = $this->request->get;
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $this->loadModel('tool/seo_url');
        //can be int or array
        $categoryId = $request['category_id'];
        if (isset($request['path'])) {
            $path = '';
            $parts = explode('_', $request['path']);
            if (count($parts) == 1) {
                //see if this is a category ID to sub category, need to build full path
                $parts = explode('_', $mdl->buildPath($request['path']));
            }
            foreach ($parts as $path_id) {
                $category_info = $mdl->getCategory($path_id);
                if ($category_info) {
                    if (!$path) {
                        $path = $path_id;
                    } else {
                        $path .= '_' . $path_id;
                    }
                }
            }
            $categoryId = array_unique([(int)array_pop($parts), (int)$parts[0]]);
        }elseif(is_array($categoryId)){
            $categoryId = filterIntegerIdList($categoryId);
            $parts = explode('_',$mdl->buildPath($categoryId[0]));
            $categoryId[] = (int)$parts[0];
            $categoryId = array_unique($categoryId);
        }

        $mdl->buildCategoryTree( $mdl->getAllCategories(),0 );

        $all = $mdl->data['all_categories'];

        foreach($all as $i=>$c){
            //exclude empty categories
            if(!(int)$c['product_count']){
                unset($all[$i]);
            }
            //filter by tree leaf
            elseif( !array_intersect(filterIntegerIdList(explode("_",$c['path'])),(array)$categoryId) ){
                unset($all[$i]);
            }
        }

        $mdl->data['all_categories'] = $all;
        $categoryTree = $mdl->buildNestedCategoryList(0);

        $this->view->assign('category_tree', renderFilterCategoryTreeNV($categoryTree, 0, $categoryId));


        /** @var ModelCatalogReview $mdlReview */
        $mdlReview = $this->loadModel('catalog/review');
        $this->data['ratings'] = $mdlReview->getCategoriesAVGRatings($categoryId);
        $this->data['brands'] = $mdl->getCategoriesBrands($categoryId);
        $this->data['selected_brand'] = filterIntegerIdList($this->request->get['manufacturer']);
        $this->data['selected_rating'] = filterIntegerIdList($this->request->get['rating']);

        $this->data['text_apply'] = $this->language->get('fast_checkout_text_apply', 'checkout/fast_checkout');

        $httpQuery = [];

        $page = $request['page'] ?? 1;
        $httpQuery['page'] = $page;
        $limit = (int)$this->request->get['limit'] ?: $this->config->get('config_catalog_limit');
        $httpQuery['limit'] = $limit;

        $sorting_href = $request['sort'];
        if (!$sorting_href || !isset($this->data['sorts'][$request['sort']])) {
            $sorting_href = $this->config->get('config_product_default_sort_order');
        }
        list($sort, $order) = explode("-", $sorting_href);
        if ($sort == 'name') {
            $sort = 'pd.' . $sort;
        } elseif (in_array($sort, ['sort_order', 'price'])) {
            $sort = 'p.' . $sort;
        }
        $httpQuery['sort'] = $sort;
        $httpQuery['order'] = $order;

        $this->data['page_url'] = $this->html->getSEOURL($this->request->get['rt'], '&'.http_build_query($httpQuery));

        $this->view->batchAssign($this->data);
        $this->processTemplate();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}