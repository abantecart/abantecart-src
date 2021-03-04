<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}


$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(DIR_EXT.'cardknox/image/cardknox_logo.png', DIR_RESOURCE.'image/cardknox_logo.png');

$resource = array(
    'language_id'   => $this->config->get('storefront_language_id'),
    'name'          => array(),
    'title'         => array(),
    'description'   => array(),
    'resource_path' => 'cardknox_logo.png',
    'resource_code' => '',
);

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'cardknox_logo.png';
    $resource['title'][$lang['language_id']] = 'cardknox_payment_storefront_icon';
    $resource['description'][$lang['language_id']] = 'CardKnox Storefront Icon';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    // get hexpath of resource (RL moved given file from rl-image-directory in own dir tree)
    $resource_info = $rm->getResource($resource_id, $this->config->get('admin_language_id'));
    // write it path in settings (array from parent method "install" of extension manager)
    $settings['cardknox_payment_storefront_icon'] = 'image/'.$resource_info['resource_path'];
}

$settings['cardknox_custom_logo'] = $this->config->get('config_logo');



