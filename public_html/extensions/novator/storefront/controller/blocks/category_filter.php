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
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->data['data_sources'] = [
            'pages/product/search' => [
                'model_rt' => 'catalog/product',
                'model_method' => 'getFilteredProducts',
            ]
        ];
    }

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('novator/novator');
        $get = $this->request->get;

        $this->buildCategoryTree();

        $this->data['selected_brand'] = filterIntegerIdList((array)$get['manufacturer_id']);
        $this->data['selected_rating'] = filterIntegerIdList((array)$get['rating']);

        $this->data['text_apply'] = $this->language->get('fast_checkout_text_apply', 'checkout/fast_checkout');

        $httpQuery = $this->prepareProductSortingParameters();

        $httpQuery['model'] = $get['model'];
        $httpQuery['keyword'] = $get['keyword'];
        $httpQuery['description'] = $get['description'];

        $this->data['page_url'] = $this->html->getSEOURL($this->request->get['rt'], '&'.http_build_query($httpQuery));
        $this->view->batchAssign($this->data);
        if(!$this->data['category_tree'] && !$this->data['ratings'] && !$this->data['brands']){
            return;
        }

        $this->processTemplate();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function buildCategoryTree()
    {
        $extra = [];
        $get = $this->request->get;
        $dataSource = false;
        //can be int or array
        $categoryMdl = $this->model_catalog_category;
        $this->loadModel('catalog/review');
        $categoryId = $get['category_id'];
        $parentRoute = $this->parent_controller->parent_controller->children[0]['controller'];

        if (isset($get['path'])) {
            $parts = explode('_', $get['path']);
            if (count($parts) == 1) {
                //see if this is a category ID to sub category, need to build full path
                $parts = explode('_', $categoryMdl->buildPath($get['path']));
            }
            foreach(array_reverse($parts) as $i){
                if($i){
                    $categoryId[] = $i;
                }
            }
        }elseif(is_array($categoryId)){
            $categoryId = filterIntegerIdList($categoryId);
            $parts = explode('_',$categoryMdl->buildPath($categoryId[0]));
            $categoryId[] = (int)$parts[0];
            $categoryId = array_unique($categoryId);
        }

        if($parentRoute == 'pages/product/manufacturer'){
            /** @var ModelCatalogManufacturer $mdl */
            $mdl = $this->loadModel('catalog/manufacturer');
            if(!$categoryId) {
                $categoryId = $mdl->getCategories((array)$get['manufacturer_id']);
            }
            $this->data['brands'] = $mdl->getManufacturersData(
                [
                    'filter' => [
                        'manufacturer_id' => (array)$get['manufacturer_id'],
                        'rating'=>$get['rating']
                    ]
                ]
            );
            $this->data['ratings'] = $this->model_catalog_review->getBrandsAVGRatings(
                (array)$get['manufacturer_id'],
                ['filter'=>[
                    'rating'      => $get['rating'],
                    'category_id' => $categoryId,
                    ]
                ]
            );
        }elseif ($categoryId && $parentRoute == 'pages/product/category'){


            $filter = [
                'filter'=>[
                    'rating'=>$get['rating'],
                    'manufacturer_id' => (array)$get['manufacturer_id']
                ]
            ];
            $get['category_id'] = $categoryId;
            $this->data['brands'] = $categoryMdl->getCategoriesBrands( $categoryId, $filter );
            $this->data['ratings'] = $this->model_catalog_review->getCategoriesAVGRatings( $categoryId, $filter );
            $extra['lock_one_category'] = count((array)$get['category_id']) == 1;
        }elseif($this->data['data_sources'][$parentRoute]){
            $dataSource = true;
            $sourceInfo = $this->data['data_sources'][$parentRoute];
            $productList = [];
            try {
                $sourceMdl = $this->loadModel($sourceInfo['model_rt']);
                $productList = $sourceMdl->{$sourceInfo['model_method']}(['filter' => $get, 'limit' => 100000000000]);
            }catch(Exception $e){}
            if($productList) {
                if ( isset(current($productList)['manufacturer_id'])
                    && isset(current($productList)['manufacturer'])
                    && isset(current($productList)['rating'])
                ){
                    $counts = [];
                    foreach($productList as $item){
                        if($item['rating']) {
                            $this->data['ratings'][$item['rating']]++;
                        }
                        if(!$item['manufacturer']){
                            continue;
                        }
                        $counts[$item['manufacturer_id']]++;
                        $sortIdx[$item['manufacturer_id']]  = $item['manufacturer'];
                        $this->data['brands'][$item['manufacturer_id']] =
                            [
                                'manufacturer_id' => $item['manufacturer_id'],
                                'name' => $item['manufacturer']
                            ];
                    }
                    foreach($this->data['brands'] as $id => $item){
                        $this->data['brands'][$id]['product_count'] = $counts[$id];
                    }
                    array_multisort($this->data['brands'], SORT_STRING, $sortIdx);
                }

                $productIds = array_column($productList, 'product_id');
                $prodCatList = $categoryMdl->getCategoriesOfProducts($productIds);
                $names = array_column($prodCatList,'name','category_id');
                $counts = $catList = [];
                foreach($prodCatList as $item){
                    $counts[$item['category_id']]++;
                    $catList[(int)$item['category_id']] = [
                        'category_id' => $item['category_id'],
                        'name' => $item['name']
                    ];
                }
                $sortIdx = [];
                foreach($catList as &$item){
                    $item['name'] = $categoryMdl->getPath($item['category_id']);
                    $sortIdx[$item['category_id']]  = $item['name'];
                }
                array_multisort($catList, SORT_STRING, $sortIdx);
            }
        }
        if(!$catList) {
            $catList = $categoryMdl->getAllCategories(
                [
                    'filter' => [
                        'rating'          => $get['rating'],
                        'manufacturer_id' => (array)$get['manufacturer_id']
                    ]
                ]
            );
        }
        $categoryMdl->buildCategoryTree(
            $catList ,
            0 );

        if(!$dataSource) {
            $all = $categoryMdl->data['all_categories'];
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
            $categoryMdl->data['all_categories'] = $all;
        }else{
            $categoryMdl->data['all_categories'] = $catList;
        }

        $categoryTree = $categoryMdl->buildNestedCategoryList(0,['no_sum_children' => true]);
        $this->data['category_tree'] = renderFilterCategoryTreeNV($categoryTree, 0, (array)$get['category_id'], $extra);
    }
}