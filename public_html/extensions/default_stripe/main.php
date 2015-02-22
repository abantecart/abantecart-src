<?php

if(!class_exists('ExtensionStripe')){
	include('core/default_stripe.php');
}

$controllers = array(
    'storefront' => array('responses/extension/default_stripe'),
    'admin' => array(
		'responses/extension/default_stripe',
		'pages/extension/default_stripe_settings',
	),
);

$models = array(
    'storefront' => array( 'extension/default_stripe' ),
    'admin' => array( 'extension/default_stripe' ),
);

$languages = array(
    'storefront' => array(
	    'stripe/default_stripe'),
    'admin' => array(
        'stripe/default_stripe')
);

$templates = array(
    'storefront' => array(
		'responses/default_stripe.tpl' 
	),
    'admin' => array(
		'pages/extension/default_stripe_settings.tpl',    
    )
);