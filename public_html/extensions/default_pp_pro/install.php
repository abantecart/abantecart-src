<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(
    DIR_EXT.'default_pp_pro/image/secure_paypal_icon.jpg',
    DIR_RESOURCE.'image/secure_paypal_icon.jpg'
);

$resource = [
    'language_id'   => $this->config->get('storefront_language_id'),
    'name'          => [],
    'title'         => [],
    'description'   => [],
    'resource_path' => 'secure_paypal_icon.jpg',
    'resource_code' => '',
];

foreach ($language_list as $lang) {
    $resource['name'][$lang['language_id']] = 'secure_paypal_icon.jpg';
    $resource['title'][$lang['language_id']] = 'default_pp_pro_payment_storefront_icon';
    $resource['description'][$lang['language_id']] = 'Default PayPal Pro Default Storefront Icon';
}
$resource_id = $rm->addResource($resource);

if ($resource_id) {
    $settings['default_pp_pro_payment_storefront_icon'] = $resource_id;
}
