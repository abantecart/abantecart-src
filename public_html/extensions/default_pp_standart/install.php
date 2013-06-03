<?php

if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

$language_list = $this->model_localisation_language->getLanguages();

$rm = new AResourceManager();
$rm->setType('image');

$result = copy(DIR_EXT.'default_pp_standart/image/pp_cc_mark_37x23.jpg', DIR_RESOURCE.'image/pp_cc_mark_37x23.jpg');

$resource = array(
	'language_id' => $this->config->get('storefront_language_id'),
	'name' => array(),
	'title' => 'default_pp_standart_default_storefront_icon',
	'description' => 'PayPal Pro UK Default Storefront Icon',
	'resource_path' => 'pp_cc_mark_37x23.jpg',
	'resource_code' => ''
);

foreach($language_list as $lang){
	$resource['name'][$lang['language_id']] = 'pp_cc_mark_37x23.jpg';
}
$resource_id = $rm->addResource($resource);

if ( $resource_id ) {
	// get hexpath of resource (RL moved given file from rl-image-directory in own dir tree)
	$resource_info = $rm->getResource($resource_id, $this->config->get('admin_language_id'));
	// write it path in settings (array from parent method "install" of extension manager)
	$settings['default_pp_standart_payment_storefront_icon'] =  'image/'.$resource_info['resource_path'];

}

$settings['default_pp_standart_custom_logo'] = 'resources/' . $this->config->get('config_logo');