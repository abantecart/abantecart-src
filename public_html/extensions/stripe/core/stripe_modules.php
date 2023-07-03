<?php
//load library provided by stripe. If required, it can be upgraded in /lib directory
/**
 * @param AConfig $config
 */
function grantStripeAccess($config)
{
    if ($config->get('stripe_test_mode')) {
        Stripe\Stripe::setApiKey($config->get('stripe_sk_test'));
    } else {
        Stripe\Stripe::setApiKey($config->get('stripe_sk_live'));
    }
    \Stripe\Stripe::setApiVersion("2019-02-19");
}