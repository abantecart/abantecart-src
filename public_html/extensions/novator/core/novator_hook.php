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
 }

