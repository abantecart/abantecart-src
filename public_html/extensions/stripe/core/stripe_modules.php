<?php
//load library provided by stripe. If required, it can be upgraded in /lib directory
/**
 * @param AConfig $config
 */
function grantStripeAccess($config)
{
    $apiKey = $config->get('stripe_test_mode') ? $config->get('stripe_sk_test') : $config->get('stripe_sk_live');
    Stripe\Stripe::setApiKey($apiKey);
    \Stripe\Stripe::setApiVersion("2024-04-10");
    return new Stripe\StripeClient($apiKey);

}