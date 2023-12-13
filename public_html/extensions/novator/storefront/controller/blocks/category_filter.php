<?php

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
        $categoryId = (int)$request['category_id'];
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

        $this->view->assign('category_tree', $this->renderTree($categoryTree, 0, $categoryId));

        $ids = $mdl->getChildrenIDs($categoryId);
        $ids[] = $categoryId;

        /** @var ModelCatalogReview $mdlReview */
        $mdlReview = $this->loadModel('catalog/review');
        $this->view->assign('ratings', $mdlReview->getCategoriesAVGRatings($ids));
        $this->view->assign('brands', $mdl->getCategoriesBrands($ids));

        $this->processTemplate();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function renderTree($tree, $level = 0, $currentId = 0)
    {
        if(!$tree || !is_array($tree)){
            return false;
        }
        $output = '<div>';
        foreach($tree as $cat){
            $cat['name'] = ($level ? ' - ' : '') .$cat['name'];
                $output .=
                    '<div class="my-2 ms-'.$level.'">
                        <a href="'.$this->html->getSEOURL('product/category','&category_id='.$cat['category_id']).'" 
                            class="link '.($currentId == $cat['category_id'] ? 'fw-bolder link-primary' : 'link-secondary').' d-block ms-'.$level.'" >'. str_repeat('&nbsp;', $level ).$cat['name'].'
                            <span class="float-end">('. $cat['product_count'].')</span>
                        </a>
                    </div>';

                if(!$cat['children']){ continue; }
                $output .= $this->renderTree($cat['children'], $level+1, $currentId);
        }
        $output .= '</div>';
        return $output;
    }
}