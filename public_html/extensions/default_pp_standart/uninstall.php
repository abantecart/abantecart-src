<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$rm = new AResourceManager();
$rm->setType('image');

$resources = $rm->getResources('extensions', 'default_pp_standart');
if (is_array($resources)) {
    foreach ($resources as $resource) {
        $rm->deleteResource($resource['resource_id']);
    }
}