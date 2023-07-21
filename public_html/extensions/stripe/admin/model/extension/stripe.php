<?php
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
    public $error = array();

    public function getStripeOrder($order_id)
    {
        $qry = $this->db->query("SELECT * 
								FROM `".$this->db->table("stripe_orders")."` 
								WHERE `order_id` = '".(int)$order_id."' 
								LIMIT 1");
        if ($qry->num_rows) {
            $order = $qry->row;
            return $order;
        } else {
            return false;
        }
    }

    public function getStripeCharge($ch_id)
    {
        if (!has_value($ch_id)) {
            return array();
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);
            if(is_int(strpos($ch_id, "ch_"))) {
                return Stripe\Charge::retrieve($ch_id);
            }elseif(is_int(strpos($ch_id, "pi_"))){
                $pi =  Stripe\PaymentIntent::retrieve($ch_id);
                return $pi->charges->data[0];
            }
        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
            return null;
        }
    }

    public function getPaymentIntent($pi_id)
    {
        if (!has_value($pi_id)) {
            return array();
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            $pi =  Stripe\PaymentIntent::retrieve($pi_id);
            return $pi;

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
            return array();
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);
            $ch = Stripe\PaymentIntent::retrieve($pi_id);
            $re = $ch->cancel();
            return $re;
        }catch(Exception $e){
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
            return null;
        }
    }

    public function refund($ch_id, $amount)
    {
        if (!has_value($ch_id)) {
            return array();
        }

            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);
            $ch = Stripe\Charge::retrieve($ch_id);
            $re = $ch->refunds->create(array(
                'amount' => round($amount,2) * 100,
            ));
            return $re;
    }

    public function captureStripe($ch_id, $amount)
    {
        if (!has_value($ch_id)) {
            return array();
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            $ch = Stripe\Charge::retrieve($ch_id);
            $params = array();
            if ($amount) {
                $params['amount'] = round($amount,2) * 100;
            }
            $re = $ch->capture($params);
            return $re;
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
            return array();
        }
        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);

            $intent = \Stripe\PaymentIntent::retrieve($pi_id);
            $params = array();
            if ($amount) {
                $params['amount'] = round($amount,2) * 100;
            }
            $re = $intent->capture($params);
            return $re;
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
     * @param string $stripe_plan
     *
     * @return array|bool
     */
    public function setProductAsSubscription($product_id, $stripe_plan)
    {
        $product_id = (int)$product_id;
        if (!$product_id && !$stripe_plan) {
            return array('error' => "Missing required parameters");
        }

        //load plan details.
        $plan_det = $this->getStripePlan($stripe_plan);
        if ($plan_det->id != $stripe_plan && !$plan_det->amount) {
            return array(
                'error' => "Subscription plan ".$stripe_plan
                    ." cannot be located in Stripe. Try again or check Stripe for more details",
            );
        }
        //assume same currency as store
        //$currency = $plan_det->currency;
        $price = $plan_det->amount / 100;
        //update product price with plan price
        $this->db->query(
            "UPDATE `".$this->db->table("products`")."
				SET 
					subscription_plan_id = '".$this->db->escape($stripe_plan)."',
					price = ".(float)$price."
				WHERE product_id = '".(int)$product_id."'");

        $this->cache->remove('product');

        $this->load->model('catalog/product');
        $product_info = $this->model_catalog_product->getProduct($product_id);
        if (!$product_info) {
            return array('error' => "Product can not be located");
        }

        //update stripe metadata for subscription
        $product_url = $this->html->getCatalogURL('product/product', '&product_id='.$product_id);
        $plan_det->metadata = array('product_id' => $product_id, 'product_url' => $product_url);
        $plan_det->save();
        return true;
    }

    public function removeProductAsSubscription($product_id)
    {
        $product_id = (int)$product_id;
        if (!$product_id) {
            return array('error' => "Missing required parameters");
        }

        $stripe_plan = $this->getProductSubscription($product_id);

        $this->db->query(
            "UPDATE `".$this->db->table("products`")."
				SET 
					subscription_plan_id = '',
					price = 0.0
				WHERE product_id = '".(int)$product_id."'");

        $this->cache->remove('product');

        //load plan details.
        $plan_det = $this->getStripePlan($stripe_plan);
        if ($plan_det->id != $stripe_plan) {
            return true;
        }
        //clear stripe metadata for subscription
        $plan_det->metadata = array();
        $plan_det->save();
        return true;
    }

    /**
     * @return null|\Stripe\Collection
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
     * @param $stripe_plan
     *
     * @return null|Stripe\Plan
     */
    public function getStripePlan($stripe_plan)
    {
        if (!$stripe_plan) {
            return null;
        }

        try {
            require_once(DIR_EXT.'stripe/core/stripe_modules.php');
            grantStripeAccess($this->config);
            return Stripe\Plan::retrieve($stripe_plan);
        } catch (Exception $e) {
            //log in AException
            $ae = new AException($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
            ac_exception_handler($ae);
            return null;
        }
    }

}