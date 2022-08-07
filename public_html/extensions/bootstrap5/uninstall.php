<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}


$extension_id = 'bootstrap5';
// delete template layouts
try{
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