<?php

class ControllerBlocksCategorySlides extends AController
{
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $request = $this->request->get;
        /** @var ModelCatalogCategory $mdl */
        $mdl = $this->loadModel('catalog/category');
        $this->loadModel('tool/seo_url');

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
        } else {
            $categoryId = 0;
        }
        $resources = [];
        if($categoryId) {
            $AResource = new AResource('image');
            $resources = $AResource->getResourceAllObjects(
                'categories',
                $categoryId,
                [
                    'main' => [
                        'height' => 350,
                        'width' => 320
                    ]
                ]
            );
        }
        //remove first resource
        unset($resources[0]);

        $resources = array_values($resources);
        $this->view->assign('resources', $resources);
        $this->processTemplate();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}