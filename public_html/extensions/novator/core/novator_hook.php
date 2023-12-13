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

 }