<?php

/**
 * Class ControllerResponsesExtensionCardConnect
 *
 * @property ModelExtensionCardConnect $model_extension_cardconnect
 */
class ControllerResponsesExtensionCardConnect extends AController
{

    public $data = array();
    public $error = array();

    public function main()
    {
        $this->loadLanguage('cardconnect/cardconnect');
        $this->loadModel('extension/cardconnect');
        //need an order details
        $this->loadModel('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $this->data['cc_owner'] = HtmlElementFactory::create(
            array(
                'type'        => 'input',
                'name'        => 'cc_owner',
                'placeholder' => $this->language->get('entry_cc_owner'),
                'value'       => $order_info['payment_firstname'].' '.$order_info['payment_lastname'],
            ));

        $this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
        $this->data['cc_number'] = HtmlElementFactory::create(
            array(
                'type'        => 'input',
                'name'        => 'cc_number',
                'attr'        => 'autocomplete="off"',
                'placeholder' => $this->language->get('entry_cc_number'),
                'value'       => '',
            ));
        $this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
        $this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
        $this->data['entry_cc_cvv2_short'] = $this->language->get('entry_cc_cvv2_short');

        $this->data['months'] = array();
        for ($i = 1; $i <= 12; $i++) {
            $this->data['months'][sprintf('%02d', $i)] = sprintf('%02d - ', $i)
                .strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
        }
        $this->data['cc_expire_date_month'] = HtmlElementFactory::create(
            array(
                'type'    => 'selectbox',
                'name'    => 'cc_expire_date_month',
                'value'   => sprintf('%02d', date('m')),
                'options' => $this->data['months'],
                'style'   => 'input-medium short',
            )
        );

        $today = getdate();
        $this->data['years'] = array();
        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $this->data['years'][strftime('%Y', mktime(0, 0, 0, 1, 1, $i))] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
        }
        $this->data['cc_expire_date_year'] = HtmlElementFactory::create(
            array(
                'type'    => 'selectbox',
                'name'    => 'cc_expire_date_year',
                'value'   => sprintf('%02d', date('Y') + 1),
                'options' => $this->data['years'],
                'style'   => 'short',
            )
        );

        if ($this->customer->isLogged() && $this->config->get('cardconnect_save_cards_limit') > 0) {
            $this->data['store_cards'] = true;
            $this->data['cards'] = $this->model_extension_cardconnect->getCards($this->customer->getId());
        } else {
            $this->data['store_cards'] = false;
            $this->data['cards'] = array();
        }

        if ($this->data['store_cards']) {
            //if customer see if we have cardconnect customer object created for credit card saving
            if ($this->customer->getId()) {
                //load credit cards list
                $cc_list = array();
                if (is_array($this->data['cards'])) {
                    foreach ($this->data['cards'] as $c_card) {
                        //use card token (hash from cardconnect-server)
                        $cc_list[$c_card['token']] = 'XXX'.$c_card['account'].' Exp:'.$c_card['expiry'];
                    }
                    if (count($cc_list)) {
                        $this->data['saved_cc_list'] = HtmlElementFactory::create(array(
                            'type'    => 'selectbox',
                            'name'    => 'use_saved_cc',
                            'value'   => '',
                            'options' => $cc_list,
                        ));
                    }
                }
                //build credit card selector
                //option to save creditcard if limit is not reached
                if (count($cc_list) < $this->config->get('cardconnect_save_cards_limit')) {
                    $this->data['save_cc'] = HtmlElementFactory::create(
                        array(
                            'type'    => 'checkbox',
                            'name'    => 'save_cc',
                            'value'   => '1',
                            'checked' => false,
                        ));
                }
            }
        }

        $this->data['echeck'] = $this->config->get('cardconnect_echeck');
        $this->data['action'] = $this->html->getSecureURL('extension/cardconnect/send');
        $this->data['delete_card_url'] = $this->html->getSecureURL('extension/cardconnect/delete_card');

        $back = $this->request->get['rt'] != 'checkout/guest_step_3'
            ? $this->html->getSecureURL('checkout/payment')
            : $this->html->getSecureURL('checkout/guest_step_2');
        $this->data['back'] = HtmlElementFactory::create(
            array(
                'type'  => 'button',
                'name'  => 'back',
                'text'  => $this->language->get('button_back'),
                'style' => 'button',
                'href'  => $back,
                'icon'  => 'icon-arrow-left',
            )
        );

        $this->data['submit'] = HtmlElementFactory::create(
            array(
                'type'  => 'button',
                'name'  => 'cardconnect_button',
                'text'  => $this->language->get('button_confirm'),
                'style' => 'button btn-orange pull-right',
                'icon'  => 'icon-ok icon-white',
            ));

        $this->data['cardconnect_rt'] = 'r/extension/cardconnect';
        $this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
        $this->data['entry_cc_cvv2_short'] = $this->language->get('entry_cc_cvv2_short');
        $this->data['cc_cvv2_help_url'] = $this->html->getURL('r/extension/cardconnect/cvv2_help');
        $this->data['payment_address'] = $order_info['payment_address_1']." ".$order_info['payment_address_2'];
        $this->data['edit_address'] = $this->html->getSecureURL('checkout/address/payment');
        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_wait'] = $this->language->get('text_wait');

        $this->data['api_domain'] = $this->config->get('cardconnect_test_mode') ? 'fts-uat.cardconnect.com' : 'fts.cardconnect.com';

        //load creditcard input validation
        $this->document->addScriptBottom($this->view->templateResource('/javascript/credit_card_validation.js'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/extension/cardconnect_buttons.tpl');
    }

    public function cvv2_help()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('cardconnect/cardconnect');

        $image = '<img src="'.$this->view->templateResource('/image/securitycode.jpg').'" '
            .'alt="'.$this->language->get('entry_what_cvv2').'" />';

        $this->view->assign('title', '');
        $this->view->assign('description', $image);

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->processTemplate('responses/content/content.tpl');
    }

    public function send()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('cardconnect/cardconnect');
        $json = array();
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

            if (empty($post['cc_expire_date_month']) || empty($post['cc_expire_date_year'])) {
                $json['error'] = $this->language->get('error_incorrect_expiration');
            }

            if (strlen($post['cc_cvv2']) != 3 && strlen($post['cc_cvv2']) != 4) {
                $json['error'] = $this->language->get('error_incorrect_cvv');
            }
        }

        if (isset($json['error'])) {
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($json));
            return null;
        }

        $this->loadModel('checkout/order');
        $this->loadModel('extension/cardconnect');

        $order_id = $this->session->data['order_id'];
        // currency code
        $currency = $this->currency->getCode();
        $cardnumber = preg_replace('/[^0-9]/', '', $post['cc_token']);
        $cvv2 = preg_replace('/[^0-9]/', '', $post['cc_cvv2']);
        // Card owner name
        $cardname = html_entity_decode($post['cc_owner'], ENT_QUOTES, 'UTF-8');
        // order amount without decimal delimiter
        $amount = round(
                $this->currency->convert(
                    $this->cart->getFinalTotal(),
                    $this->config->get('config_currency'),
                    $currency
                ), 2) * 100;
        $pd = array(
            'amount'          => $amount,
            'currency'        => $currency,
            'order_id'        => $order_id,
            'cc_number'       => $cardnumber,
            'cc_expire_month' => $post['cc_expire_date_month'],
            'cc_expire_year'  => substr($post['cc_expire_date_year'], -2),
            'cc_owner'        => $cardname,
            'cc_cvv2'         => $cvv2,
            'save_cc'         => $post['save_cc'],
            'use_saved_cc'    => $post['use_saved_cc'],
            'method'          => 'card',
        );

        ADebug::checkpoint('cardconnect payment: Start processing order ID '.$order_id);

        $p_result = $this->model_extension_cardconnect->processPayment($pd);

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

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));

    }

    public function delete_card()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('cardconnect/cardconnect');
        $json = array();
        //validate input
        $post = $this->request->post;
        if (empty($post['use_saved_cc'])) {
            $json['error'] = $this->language->get('error_system');
        }
        if (!$this->customer->getId()) {
            $json['error'] = $this->language->get('error_system');
        }
        if (isset($json['error'])) {
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($json));
            return null;
        }

        $deleted = false;
        $customer_id = $this->customer->getId();
        if ($customer_id && $this->config->get('cardconnect_status') && $this->customer->isLogged()) {
            $this->loadModel('extension/cardconnect');
            $this->model_extension_cardconnect->deleteCard($post['use_saved_cc'], $customer_id);
            $deleted = true;
        }
        if (!$deleted) {
            // transaction failed
            $json['error'] = $this->language->get('error_system');
        } else {
            //basically reload the page
            $json['success'] = $this->html->getSecureURL('checkout/confirm');
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }
}
