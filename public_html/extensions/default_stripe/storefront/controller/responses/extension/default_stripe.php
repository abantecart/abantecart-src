<?php
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ControllerResponsesExtensionDefaultStripe
 *
 * @property ModelExtensionDefaultStripe $model_extension_default_stripe
 */
class ControllerResponsesExtensionDefaultStripe extends AController
{
    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('default_stripe/default_stripe');
        $this->data['action'] = $this->html->getSecureURL('extension/default_stripe/send');
        //build submit form
        $form = new AForm();
        $form->setForm(
            array(
                'form_name' => 'stripe',
            )
        );

        $this->data['form_open'] = $form->getFieldHtml(
            array(
                'type' => 'form',
                'name' => 'stripe',
                'attr' => 'class = "validate-creditcard"',
                'csrf' => true,
            )
        );

        //need an order details
        $this->loadModel('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->data['payment_address'] = $order_info['payment_address_1']." ".$order_info['payment_address_2'];

        $this->data['edit_address'] = $this->html->getSecureURL('checkout/address/payment');
        //data for token
        $this->data['address_1'] = $order_info['payment_address_1'];
        $this->data['address_2'] = $order_info['payment_address_2'];
        $this->data['address_city'] = $order_info['payment_city'];
        $this->data['address_postcode'] = $order_info['payment_postcode'];
        $this->data['address_country_code'] = $order_info['payment_iso_code_2'];
        $this->data['address_zone_code'] = $order_info['payment_zone_code'];

        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_wait'] = $this->language->get('text_wait');

        $this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $this->data['cc_owner'] = $form->getFieldHtml(
            array(
                'type'        => 'input',
                'name'        => 'cc_owner',
                'placeholder' => $this->language->get('entry_cc_owner'),
                'value'       => $order_info['payment_firstname'].' '.$order_info['payment_lastname'],
            )
        );

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');

        if ($this->request->get['rt'] == 'checkout/guest_step_3') {
            $back_url = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);
        } else {
            $back_url = $this->html->getSecureURL('checkout/payment', '&mode=edit', true);
        }
        $this->data['back'] = $this->html->buildElement(
            array(
                'type'  => 'button',
                'name'  => 'back',
                'text'  => $this->language->get('button_back'),
                'style' => 'button',
                'href'  => $back_url,
                'icon'  => 'icon-arrow-left',
            )
        );

        $this->data['submit'] = $this->html->buildElement(
            array(
                'type'  => 'button',
                'name'  => 'stripe_button',
                'text'  => $this->language->get('button_confirm'),
                'style' => 'button btn-orange pull-right',
                'icon'  => 'icon-ok icon-white',
            )
        );
        $this->data['default_stripe_ssl_off_error'] = $this->language->get('default_stripe_ssl_off_error');
        $this->view->batchAssign($this->data);

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        //load creditcard input validation
        $this->document->addScriptBottom($this->view->templateResource('/javascript/credit_card_validation.js'));

        $this->processTemplate('responses/default_stripe.tpl');
    }

    public function send()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $json = array();

        if (!$this->csrftoken->isTokenValid()) {
            $json['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($json));
            return;
        }

        $this->loadLanguage('default_stripe/default_stripe');

        //validate input
        $post = $this->request->post;

        //check if saved cc mode is used
        if (!$post['use_saved_cc']) {
            if (empty($post['cc_token'])) {
                $json['error'] = $this->language->get('error_incorrect_number');
            }

            if (empty($post['cc_owner'])) {
                $json['error'] = $this->language->get('error_incorrect_name');
            }

        }

        if (isset($json['error'])) {
            if ($json['error']) {
                $csrftoken = $this->registry->get('csrftoken');
                $json['csrfinstance'] = $csrftoken->setInstance();
                $json['csrftoken'] = $csrftoken->setToken();
            }
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($json));
            return null;
        }

        $this->loadModel('checkout/order');
        $this->loadModel('extension/default_stripe');
        $this->loadLanguage('default_stripe/default_stripe');
        $order_id = $this->session->data['order_id'];

        // currency code
        $currency = $this->currency->getCode();
        // order amount without decimal delimiter
        $amount = round($this->currency->convert($this->cart->getFinalTotal(), $this->config->get('config_currency'), $currency), 2) * 100;

        ADebug::checkpoint('Stripe Payment: Start processing order ID '.$order_id);

        $pd = array(
            'order_id' => $order_id,
            'amount'   => $amount,
            'currency' => $currency,
            'cc_token' => $post['cc_token'],
        );

        $p_result = $this->model_extension_default_stripe->processPayment($pd);

        ADebug::variable('Processing payment result: ', $p_result);
        if ($p_result['error']) {
            // transaction failed
            $json['error'] = (string)$p_result['error'];
            if ($p_result['code']) {
                $json['error'] .= ' ('.$p_result['code'].')';
            }
        } else {
            if ($p_result['paid']) {
                $json['success'] = $this->html->getSecureURL('checkout/success');
            } else {
                //Unexpected result
                $json['error'] = $this->language->get('error_system');
            }
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        if (isset($json['error'])) {
            if ($json['error']) {
                $csrftoken = $this->registry->get('csrftoken');
                $json['csrfinstance'] = $csrftoken->setInstance();
                $json['csrftoken'] = $csrftoken->setToken();
            }
        }
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

    public function api()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['text_note'] = $this->language->get('text_note');
        $this->data['order_id'] = $this->session->data['order_id'];
        //list of required fields for payment 
        $this->data['required_fields'] = array(
            'name'                         => 'cc_owner',
            'credit_card_number'           => 'cc_number',
            'credit_card_cvv2'             => 'cc_cvv2',
            'credit_card_expiration_month' => 'cc_expire_date_month',
            'credit_card_expiration_year'  => 'cc_expire_date_year',
        );

        $this->data['process_rt'] = 'default_stripe/api_confirm';

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($this->data));
    }

    public function api_confirm()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $payment_controller = $this->dispatch('responses/extension/default_stripe/send');

        $result = $payment_controller->dispatchGetOutput();

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->response->setOutput($result);
    }
}
