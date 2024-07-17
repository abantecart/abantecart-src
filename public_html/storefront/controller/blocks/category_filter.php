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
    /** @var array  list of category IDs */
    protected $selectedCategories = [];
    /** @var bool sign when show only categories with parent Id 0  to prevent display of all categories.*/
    protected $root_level = false;

    /** @var array filtered category list based on other parameters (brands and rates) */
    protected $categoryList = [];
    /** @var array list of manufacturer IDs */
    protected $selectedBrands = [];
    /** @var array filtered brand list based on other parameters (categories and rates) */
    protected $brandList = [];

    /** @var array list of rates */
    protected $selectedRatings = [];
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->data['data_sources'] = [
            'pages/product/search' => [
                'model_rt' => 'catalog/product',
                'model_method' => 'getFilteredProducts',
            ]
        ];

        // start to define selected items
        $this->selectedBrands = filterIntegerIdList((array)$this->request->get_or_post('manufacturer_id'));
        $this->selectedRatings = filterIntegerIdList((array)$this->request->get_or_post('rating'));
        $cIDs = (array)$this->request->get_or_post('category_id');
        $categoryPath = $this->request->get_or_post('path');
        if($cIDs) {
            $this->selectedCategories = filterIntegerIdList($cIDs);
        }elseif($categoryPath){
            $categoryPath = explode('_', $categoryPath);
            $this->selectedCategories = [end($categoryPath)];
            /** @var ModelCatalogCategory $categoryMdl */
            $categoryMdl = $this->loadModel('catalog/category');
            $children = $categoryMdl->getChildrenIDs($this->selectedCategories);
            $this->selectedCategories = array_merge( $this->selectedCategories,$children);
        }

        $parentRoute = $this->parent_controller->parent_controller->children[0]['controller'];
        if(str_starts_with($parentRoute, 'pages/product/manufacturer')){
            /** @var ModelCatalogManufacturer $mdl */
            $mdl = $this->loadModel('catalog/manufacturer');
            if(!$this->selectedCategories) {
                $this->selectedCategories = $mdl->getCategories(
                    [
                        'filter' => [
                            'manufacturer_id' => $this->selectedBrands,
                            'rating' => $this->selectedRatings
                        ]
                    ]
                );
            }
        }

        if(str_starts_with($parentRoute, 'pages/product/category')) {
            $this->root_level = !(bool)$this->selectedCategories;
        }
    }

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('novator/novator');
        $get = $this->request->get;

        $this->buildTrees();

        $this->data['selected_brand'] = $this->selectedBrands;
        $this->data['selected_rating'] = $this->selectedRatings;

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

    protected function buildTrees()
    {
        $extra = ['root_level' => $this->root_level ];
        $dataSource = false;
        //can be int or array
        $categoryMdl = $this->model_catalog_category;
        $this->loadModel('catalog/review');
        $parentRoute = $this->parent_controller->parent_controller->children[0]['controller'];

        if(str_starts_with($parentRoute, 'pages/product/manufacturer')){
            $this->buildTreesForBrandPage();
        }
        elseif ($this->selectedCategories && str_starts_with($parentRoute, 'pages/product/category')){
            $this->buildTreesForCategoryPage();
        }elseif($this->data['data_sources'][$parentRoute]){
            $this->buildTreesForProductListingPage($parentRoute);
        }
        //if list not taken from main controller result like a search page
        $this->getCategoryList();

        //call to build inner list in the model state ($data['all_categories'])
        $categoryTree = $categoryMdl->buildCategoryTree(
            $this->categoryList ,
            [
                'store_id' => (int)$this->config->get('config_store_id'),
                'filter' => [
                    'rating' => $this->selectedRatings,
                    'manufacturer_id' => $this->selectedBrands
                ],
                'root_level' => !(bool)$this->selectedCategories
            ]
        );

        if($dataSource) {
            //list from parent controller, like a search result
            $categoryMdl->data['all_categories'] = $this->categoryList;
        }

        $this->data['category_details'] = [
            'tree' => $categoryTree,
            'selected' => $this->selectedCategories,
            'extra' => $extra
        ];
    }

    protected function buildTreesForBrandPage()
    {
        /** @var ModelCatalogManufacturer $mdl */
        $mdl = $this->loadModel('catalog/manufacturer');
        $this->data['brands'] = $mdl->getManufacturersData(
            [
                'filter' => [
                    'manufacturer_id' => $this->selectedBrands,
                    'rating' => $this->selectedRatings
                ]
            ]
        );
        $this->data['ratings'] = $this->model_catalog_review->getBrandsAVGRatings(
            $this->selectedBrands,
            [
                'filter'=>[
                    'category_id' => $this->selectedCategories
                ]
            ]
        );
    }
    protected function buildTreesForCategoryPage()
    {
        /** @var ModelCatalogCategory $categoryMdl */
        $categoryMdl = $this->loadModel('catalog/category');
        $filter = [
            'filter'=>[
                'rating' => $this->selectedRatings,
                'manufacturer_id' => $this->selectedBrands
            ]
        ];

        $this->data['brands'] = $categoryMdl->getCategoriesBrands( $this->selectedCategories, $filter );
        $this->data['ratings'] = $this->model_catalog_review->getCategoriesAVGRatings( $this->selectedCategories, $filter );
        //$extra['lock_one_category'] = count($this->selectedCategories) == 1;
    }
    protected function buildTreesForProductListingPage($parentRoute)
    {
        /** @var ModelCatalogCategory $categoryMdl */
        $categoryMdl = $this->loadModel('catalog/category');
        $sourceInfo = $this->data['data_sources'][$parentRoute];
        $productList = [];
        try {
            $sourceMdl = $this->loadModel($sourceInfo['model_rt']);
            $productList = $sourceMdl->{$sourceInfo['model_method']}(
                [
                    'filter' => $this->request->get,
                    'limit' => 100000000000
                ]
            );
        }catch(Exception|Error $e){
            $this->log->write(__CLASS__.": ".$e->getMessage());
        }
        if($productList) {
            if ( isset(current($productList)['manufacturer_id'])
                && isset(current($productList)['manufacturer'])
                && isset(current($productList)['rating'])
            ){
                $counts = [];
                $this->data['ratings'] = ['5' => 0, '4'=>0, '3'=>0, '2'=>0, '1'=>0];
                foreach($productList as $item){
                    if($item['rating']) {
                        $this->data['ratings'][(string)round($item['rating'])]++;
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
            $this->categoryList = $catList;

            if(!$this->selectedRatings){
                $this->selectedRatings = array_keys($this->data['ratings']);
            }

            if(!$this->selectedBrands){
                $this->selectedBrands = array_column($this->data['brands'],'manufacturer_id');
            }

            if(!$this->selectedCategories){
                $this->selectedCategories = array_column($prodCatList,'category_id');
            }
        }
    }
    // get category list with all children regardless of selected
    protected function getCategoryList()
    {
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $fullCatList = $this->selectedCategories;
        if($this->selectedCategories) {
            foreach ($this->selectedCategories as $cId) {
                $fullCatList = array_merge($fullCatList, $mdl->getChildrenIDs($cId));
            }
        }else{
            $fullCatList = $mdl->getChildrenIDs(0, 'active_only', false);
        }

        $output = $mdl->getAllCategories(
            [
                'filter' => [
                    'category_id'     => array_unique($fullCatList),
                    'rating'          => $this->selectedRatings,
                    'manufacturer_id' => $this->selectedBrands
                ],
                'root_level' => $this->root_level
            ]
        );
        $this->categoryList = $output;
    }
}