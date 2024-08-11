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

use Stripe\Charge;
use Stripe\Collection;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Refund;

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ModelExtensionStripe
 *
 * @property ModelCatalogProduct $model_catalog_product
 */
class ModelExtensionStripe extends Model
{
    public $error = [];

    /**
     * @param Registry $registry
     */
    public function __construct($registry)
    {
        parent::__construct($registry);
        grantStripeAccess($this->config);
    }

    /**
     * @param int $orderId
     * @return array
     * @throws AException
     */
    public function getStripeOrder($orderId)
    {
        $result = $this->db->query(
            "SELECT * 
            FROM `" . $this->db->table("stripe_orders") . "` 
            WHERE `order_id` = '" . (int)$orderId . "' 
            LIMIT 1"
        );
        return $result->row;
    }

    /**
     * @param string $chargeId
     * @return false|Charge
     */
    public function getStripeCharge($chargeId)
    {
        if (!$chargeId) {
            return false;
        }
        try {
            if (is_int(strpos($chargeId, "ch_"))) {
                return Stripe\Charge::retrieve($chargeId);
            } elseif (is_int(strpos($chargeId, "pi_"))) {
                $pi = Stripe\PaymentIntent::retrieve($chargeId);
                $lch = $pi->latest_charge;
                return Stripe\Charge::retrieve($lch);
            }
        } catch (Exception|Error $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return false;
    }

    /**
     * @param string $intentId
     * @return false|PaymentIntent
     */
    public function getPaymentIntent($intentId)
    {
        if (!$intentId) {
            return false;
        }
        try {
            return Stripe\PaymentIntent::retrieve($intentId);
        } catch (Exception|Error $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return false;
    }

    /**
     * @param string $intentId
     * @return false|PaymentIntent
     */
    public function cancelPaymentIntent($intentId)
    {
        if (!$intentId) {
            return false;
        }
        try {
            $ch = Stripe\PaymentIntent::retrieve($intentId);
            return $ch->cancel();
        } catch (Exception|Error $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return false;
    }

    /**
     * @param string $chargeId
     * @param float $amount
     * @return false|Refund
     */
    public function refund($chargeId, $amount)
    {
        if (!$chargeId) {
            return false;
        }
        try {
            return Stripe\Refund::create(
                [
                    'amount' => round($amount, 2) * 100,
                    'charge' => $chargeId,
                ]
            );
        } catch (Exception|Error $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return false;
    }

    /**
     * @param string $chargeId
     * @param float $amount
     * @return false|Charge
     */
    public function captureStripe($chargeId, $amount)
    {
        if (!$chargeId) {
            return false;
        }
        try {
            $ch = Stripe\Charge::retrieve($chargeId);
            $params = [];
            if ($amount) {
                $params['amount'] = round($amount, 2) * 100;
            }
            return $ch->capture($params);
        } catch (Exception|Error $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return false;
    }

    /**
     * @param $intentId
     * @param $amount
     * @return false|PaymentIntent
     */
    public function capturePaymentIntent($intentId, $amount)
    {
        if (!$intentId) {
            return false;
        }
        try {
            $intent = PaymentIntent::retrieve($intentId);
            $params = [];
            if ($amount) {
                $params['amount'] = round($amount, 2) * 100;
            }
            return $intent->capture($params);
        } catch (Exception|Error $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return false;
    }
}