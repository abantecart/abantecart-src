<?php

if(!class_exists('ExtensionDiagnose')){
	include('core/diagnose.php');
}

$controllers = array(
    'storefront' => array(),
    'admin' => array(
		'responses/extension/diagnose',
		'pages/extension/diagnose_settings',
	),
);

$models = array(
    'storefront' => array(),
    'admin' => array( 'extension/diagnose' ),
);

$languages = array(
    'storefront' => array(),
    'admin' => array(
        'stripe/diagnose'
     )
);

$templates = array(
    'storefront' => array(
	),
    'admin' => array(
		'pages/extension/diagnose_settings.tpl',    
    )
);