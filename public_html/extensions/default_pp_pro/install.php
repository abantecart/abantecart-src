<?php

if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(DIR_EXT.'default_pp_pro/image/secure_paypal_icon.jpg', DIR_RESOURCE.'image/secure_paypal_icon.jpg');

$resource = array(
	'language_id' => $this->config->get('storefront_language_id'),
	'name' => array(),
	'title' => array(),
	'description' => array(),
	'resource_path' => 'secure_paypal_icon.jpg',
	'resource_code' => ''
);

foreach($language_list as $lang){
	$resource['name'][$lang['language_id']] = 'secure_paypal_icon.jpg';
	$resource['title'][$lang['language_id']] = 'default_pp_pro_payment_storefront_icon';
	$resource['description'][$lang['language_id']] = 'Default PayPal Pro Default Storefront Icon';
}
$resource_id = $rm->addResource($resource);

if ( $resource_id ) {
	// get hexpath of resource (RL moved given file from rl-image-directory in own dir tree)
	$resource_info = $rm->getResource($resource_id, $this->config->get('admin_language_id'));
	// write it path in settings (array from parent method "install" of extension manager)
	$settings['default_pp_pro_payment_storefront_icon'] =  'image/'.$resource_info['resource_path'];

}
