<?php
if ( !defined ( 'DIR_CORE' ) ) {
	header ( 'Location: static_pages/' );
}

//load library provided by strype. If required, it can be upgraded in /lib directory
require_once(DIR_EXT . 'default_stripe/core/lib/Stripe.php');
/**
 * @param AConfig $config
 */
function grantStripeAccess( $config ) {
	if($config->get('default_stripe_access_token')) {
		 Stripe::setApiKey($config->get('default_stripe_access_token'));
	} else {
		if( $config->get('default_stripe_test_mode') ) {
		  Stripe::setApiKey($config->get('default_stripe_sk_test'));
		} else {
		    Stripe::setApiKey($config->get('default_stripe_sk_live'));
		}			
	}		
}