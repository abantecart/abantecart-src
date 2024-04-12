<?php

use Stripe\Collection;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;

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

    public function getStripeOrder($order_id)
    {
        $qry = $this->db->query("SELECT * 
								FROM `".$this->db->table("stripe_orders")."` 
								WHERE `order_id` = '".(int)$order_id."' 
								LIMIT 1");
        if ($qry->num_rows) {
            return $qry->row;
        } else {
            return false;
        }
    }

    public function getStripeCharge($ch_id)
    {
        if (!has_value($ch_id)) {
            return [];
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);
            if(is_int(strpos($ch_id, "ch_"))) {
                return Stripe\Charge::retrieve($ch_id);
            }elseif(is_int(strpos($ch_id, "pi_"))){
                $pi =  Stripe\PaymentIntent::retrieve($ch_id);
                $lch = $pi->latest_charge;
                return Stripe\Charge::retrieve($lch);
            }
        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return false;
    }

    public function getPaymentIntent($pi_id)
    {
        if (!has_value($pi_id)) {
            return [];
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            return Stripe\PaymentIntent::retrieve($pi_id);

        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
            return null;
        }
    }


    public function cancelPaymentIntent($pi_id)
    {
        if (!has_value($pi_id)) {
            return [];
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);
            $ch = Stripe\PaymentIntent::retrieve($pi_id);
            return $ch->cancel();
        }catch(Exception $e){
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
            return null;
        }
    }

    public function refund($stripeChargeId, $amount)
    {
        if (!has_value($stripeChargeId)) {
            return [];
        }

            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            return Stripe\Refund::create([
                'amount' => round($amount,2) * 100,
                'charge' => $stripeChargeId,
            ]);
    }

    public function captureStripe($ch_id, $amount)
    {
        if (!has_value($ch_id)) {
            return [];
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            $ch = Stripe\Charge::retrieve($ch_id);
            $params = [];
            if ($amount) {
                $params['amount'] = round($amount,2) * 100;
            }
            return $ch->capture($params);
        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
            return null;
        }
    }

    public function capturePaymentIntent($pi_id, $amount)
    {
        if (!has_value($pi_id)) {
            return [];
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            $intent = PaymentIntent::retrieve($pi_id);
            $params = [];
            if ($amount) {
                $params['amount'] = round($amount,2) * 100;
            }
            return $intent->capture($params);
        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
            return null;
        }
    }

    /**
     * @param int $product_id
     *
     * @return bool
     * @throws AException
     */

    public function getProductSubscription($product_id)
    {
        $product_id = (int)$product_id;
        if (!$product_id) {
            return false;
        }

        $result = $this->db->query(
            "SELECT subscription_plan_id
				FROM `".$this->db->table("products`")."
				WHERE product_id = '".(int)$product_id."'");
        return $result->row['subscription_plan_id'];
    }

    /**
     * @param int $product_id
     * @param string $stripePlanId
     *
     * @return array|bool
     * @throws AException
     * @throws ApiErrorException
     */
    public function setProductAsSubscription($product_id, $stripePlanId)
    {
        $product_id = (int)$product_id;
        if (!$product_id && !$stripePlanId) {
            return ['error' => "Missing required parameters"];
        }

        //load plan details.
        $plan_det = $this->getStripePlan($stripePlanId);
        if ($plan_det->id != $stripePlanId && !$plan_det->amount) {
            return [
                'error' => "Subscription plan ".$stripePlanId
                    ." cannot be located in Stripe. Try again or check Stripe for more details",
            ];
        }
        //assume same currency as store
        //$currency = $plan_det->currency;
        $price = $plan_det->amount / 100;
        //update product price with plan price
        $this->db->query(
            "UPDATE `".$this->db->table("products`")."
				SET 
					subscription_plan_id = '".$this->db->escape($stripePlanId)."',
					price = ".(float)$price."
				WHERE product_id = '".(int)$product_id."'");

        $this->cache->remove('product');

        $this->load->model('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if (!$product_info) {
            return ['error' => "Product can not be located"];
        }

        //update stripe metadata for subscription
        $product_url = $this->html->getCatalogURL('product/product', '&product_id='.$product_id);
        $plan_det::update( $stripePlanId,
            [
                'metadata' => [
                    'product_id' => $product_id,
                    'product_url' => $product_url
                ]
            ]
        );
        return true;
    }

    public function removeProductAsSubscription($product_id)
    {
        $product_id = (int)$product_id;
        if (!$product_id) {
            return ['error' => "Missing required parameters"];
        }

        $stripePlanId = $this->getProductSubscription($product_id);

        $this->db->query(
            "UPDATE `".$this->db->table("products`")."
            SET 
                subscription_plan_id = '',
                price = 0.0
            WHERE product_id = '".(int)$product_id."'");

        $this->cache->remove('product');

        //load plan details.
        $plan_det = $this->getStripePlan($stripePlanId);
        if ($plan_det->id != $stripePlanId) {
            return true;
        }
        //clear stripe metadata for subscription
        $plan_det::update(
            $stripePlanId,
            [
                'metadata' => []
            ]
        );
        return true;
    }

    /**
     * @return null|Collection
     */
    public function getStripePlans()
    {
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);
            return Stripe\Plan::all();
        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
            return null;
        }
    }

    /**
     * @param $stripePlanId
     *
     * @return null|Stripe\Plan
     */
    public function getStripePlan($stripePlanId)
    {
        if (!$stripePlanId) {
            return null;
        }

        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);
            return Stripe\Plan::retrieve($stripePlanId);
        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
        }
        return null;

    }

}