<?php

if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

$language_list = $this->model_localisation_language->getLanguages();

$settings['default_pp_pro_payment_storefront_icon'] = 'image/18/75/f.jpg';

$settings['default_pp_pro_custom_logo'] = 'resources/' . $this->config->get('config_logo');