<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

require_once(__DIR__.DIRECTORY_SEPARATOR.'helper.php');

class ExtensionBootstrap5 extends Extension {
    public function onHook_InitEnd(){
        if(Registry::getInstance()->get('config')->get('bootstrap5_status') && IS_ADMIN !== true) {
            Registry::getInstance()->get('language')->load('bootstrap5/bootstrap5');
        }
    }
 }

