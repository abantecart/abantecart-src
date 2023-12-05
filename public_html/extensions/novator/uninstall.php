<?php
/** @var $this AExtensionManager */

if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}


$extension_id = 'novator';
// delete template layouts
try{
    if($this->config->get('config_storefront_template') == $extension_id){
        /** @var ModelSettingSetting $mdl */
        $mdl = $this->load->model('setting/setting');
        $mdl->editSetting('appearance',['config_storefront_template' => 'default']);
    }

    $layout = new ALayoutManager($extension_id);
    $layout->deleteTemplateLayouts();
}catch(AException $e){}

$rm = new AResourceManager();
$rm->setType('image');

$resources = $rm->getResources('extensions', $extension_id);
if (is_array($resources)) {
    foreach ($resources as $resource) {
        $rm->deleteResource($resource['resource_id']);
    }
}