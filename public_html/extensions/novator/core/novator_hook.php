<?php

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

require_once(__DIR__.DIRECTORY_SEPARATOR.'helper.php');

class ExtensionNovator extends Extension {
    public function onHook_InitEnd(){
        if(Registry::getInstance()->get('config')->get('novator_status') && IS_ADMIN !== true) {
            Registry::getInstance()->get('language')->load('novator/novator');
        }
    }

    public function onControllerPagesCatalogCategory_UpdateData()
    {
        /** @var ControllerPagesCatalogCategory $that */
        $that = $this->baseObject;
        if($that->config->get('config_storefront_template') != 'novator'
            || $this->baseObject_method != 'update'){
            return;
        }
        $that->loadLanguage('novator/novator');
        $that->view->addHookVar(
            'category_form_hook_before_resources',
            $that->language->get('novator_category_form_info_alert')
        );
    }

    public function afterAHook_InitEnd()
    {
        if(!IS_ADMIN) {
            Registry::getInstance()->get('language')->load('novator/novator');
        }
    }

    public function onControllerCommonHeader_UpdateData()
    {
        $that = $this->baseObject;
        if($that->config->get('config_storefront_template') != 'novator'){
            return;
        }
        //add elements into mobile version of menu canvas
        $dd = new ADispatcher('blocks/content');
        $dd->dispatch();
        $scratchData = Registry::getInstance()->get('novator_scratch')['blocks/content'];
        $that->view->assign('mobile_menu_title', $scratchData['title']);
        $that->view->assign('mobile_menu_contents', $scratchData['contents']);

        $dd = new ADispatcher('blocks/currency');
        $that->view->assign( 'mobile_menu_currency', $dd->dispatchGetOutput());

        $dd = new ADispatcher('blocks/language');
        $that->view->assign( 'mobile_menu_language', $dd->dispatchGetOutput());

        /** @var ModelCatalogCategory $mdl */
        $mdl = $that->loadModel('catalog/category');
        $that->view->assign('mobile_menu_categories',  $mdl->getCategories(0) );
    }

    public function onControllerBlocksContent_UpdateData()
    {
        $that = $this->baseObject;
        if($that->config->get('config_storefront_template') != 'novator'){
            return;
        }
        Registry::getInstance()->set(
            'novator_scratch',
            [
            'blocks/content' => [
                'title' => $that->data['heading_title'],
                'contents' => $that->data['contents']
            ]
            ]
        );
    }

 }