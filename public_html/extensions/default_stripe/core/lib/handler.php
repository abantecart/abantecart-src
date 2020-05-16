<?php

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 *
 * Class to handle default_stripe payment transaction
 *
 * @property AConfig                     $config
 * @property ALoader                     $load
 * @property ALanguage                   $language
 * @property ACart                       $cart
 * @property ACurrency                   $currency
 * @property ModelExtensionDefaultStripe $model_extension_default_stripe
 */
class PaymentHandler extends BasePaymentHandler
{

    /**
     * @var string
     */
    protected $id = 'default_stripe';
    /**
     * @var bool
     */
    protected $recurring_billing = false;

    public function details():array
    {
        return array(
            'id'         => $this->id,
            'title'      => $this->language->get('text_title'),
            'sort_order' => $this->config->get($this->id.'_sort_order'),
        );
    }

    public function validate_payment_details($data = array())
    {
        $this->load->language($this->id.'/'.$this->id);

        //check if saved cc mode is used
        $errors = array();
        if (!$data['use_saved_cc'] && !$data['cc_token']) {
            if (empty($data['cc_number'])) {
                $errors[] = $this->language->get('error_incorrect_number');
            }

            if (empty($data['cc_owner'])) {
                $errors[] = $this->language->get('error_incorrect_name');
            }

            if (empty($data['cc_expire_date_month']) || empty($data['cc_expire_date_year'])) {
                $errors[] = $this->language->get('error_incorrect_expiration');
            }

            if (strlen($data['cc_cvv2']) != 3 && strlen($data['cc_cvv2']) != 4) {
                $errors[] = $this->language->get('error_incorrect_cvv');
            }
        }
        return $errors;
    }

    public function process_payment($order_id, $data = array())
    {
        if (empty($order_id) || empty($data)) {
            return null;
        }

        $return = array();

        $this->load->model('checkout/order');
        $this->load->model('extension/'.$this->id);
        $this->load->language($this->id.'/'.$this->id);

        // currency code
        $currency = $this->currency->getCode();
        // order amount without decimal delimiter
        $amount = round($this->currency->convert($this->cart->getFinalTotal(), $this->config->get('config_currency'), $currency), 2) * 100;

        ADebug::checkpoint('Stripe Payment: Order ID '.$order_id);

        $pd = array(
            'amount'          => $amount,
            'currency'        => $currency,
            'order_id'        => $order_id,
            'cc_token'        => $data['cc_token'],
        );

        $p_result = $this->model_extension_default_stripe->processPayment($pd);

        ADebug::variable('Processing payment result: ', $p_result);
        if ($p_result['error']) {
            // transaction failed
            $return['error'] = (string)$p_result['error'];
            if ($p_result['code']) {
                $return['error'] .= ' ('.$p_result['code'].')';
            }
        } else {
            if ($p_result['paid']) {
                $return['success'] = true;
            } else {
                //Unexpected result
                $return['error'] = $this->language->get('error_system');
            }
        }
        return $return;
    }

}