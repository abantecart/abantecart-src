<?php

class ControllerBlocksCategoryFilter extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $request = $this->request->get;
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $this->loadModel('tool/seo_url');

        $categoryId = 0;

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
            $categoryId = array_pop($parts);
        }

        if(!$categoryId){
            //if category id is unknown
            return;
        }

        $path = explode("_", $mdl->buildPath($categoryId));

        $mdl->buildCategoryTree(
            $mdl->getAllCategories(),
            0
        );

        $categoryTree = $mdl->buildNestedCategoryList(0);

        foreach($categoryTree as &$c){
            if(!$c['product_count']){
                unset($c);
            }elseif( !in_array($c['category_id'], $path) ){
                unset($c['children']);
            }
        }


        $this->view->assign('category_tree', $categoryTree);







        $this->processTemplate();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}