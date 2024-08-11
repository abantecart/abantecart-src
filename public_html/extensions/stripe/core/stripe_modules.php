<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

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