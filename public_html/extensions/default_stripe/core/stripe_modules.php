<?php

/**
 * @param AConfig $config
 */
function grantStripeAccess($config)
{
    if ($config->get('default_stripe_test_mode')) {
        \Stripe\Stripe::setApiKey($config->get('default_stripe_sk_test'));
    } else {
        \Stripe\Stripe::setApiKey($config->get('default_stripe_sk_live'));
    }
    if($config->get('default_stripe_account_id')) {
        \Stripe\Stripe::setAccountId($config->get('default_stripe_account_id'));
    }
    \Stripe\Stripe::setApiVersion("2019-02-19");
}