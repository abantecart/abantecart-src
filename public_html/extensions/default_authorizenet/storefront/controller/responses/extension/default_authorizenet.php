<?php

/**
 * Class ControllerResponsesExtensionAuthorizeNet
 *
 * @property  ModelExtensionDefaultAuthorizeNet $model_extension_default_authorizenet
 */
class ControllerResponsesExtensionDefaultAuthorizeNet extends AController
{
    public $data = array();
    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('default_authorizenet/default_authorizenet');

        $this->buildCCForm();

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        //load creditcard input validation
        $this->document->addScriptBottom($this->view->templateResource('/javascript/credit_card_validation.js'));
        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/default_authorizenet.tpl');
    }

    public function form_verification()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('default_authorizenet/default_authorizenet');
        //load creditcard input validation
        $this->document->addScriptBottom($this->view->templateResource('/javascript/credit_card_validation.js'));

        $this->buildCCForm();
        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/default_authorizenet_verification.tpl');
    }

    public function buildCCForm()
    {

        //need an order details
        $this->loadModel('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $this->data['payment_address'] = $order_info['payment_address_1']." ".$order_info['payment_address_2'];
        if($this->customer->isLogged()){
            $this->data['edit_address'] = $this->html->getSecureURL('checkout/address/payment');
        }

        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_wait'] = $this->language->get('text_wait');

        $form = new AForm();
        $form->setForm(
            array(
                'form_name' => 'authorizenet',
            )
        );

        $this->data['form_open'] = $form->getFieldHtml(
            array(
                'type' => 'form',
                'name' => 'authorizenet',
                'attr' => 'class = "validate-creditcard"',
                'csrf' => true,
            )
        );

        $this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
        $this->data['cc_owner_firstname'] = HtmlElementFactory::create(array(
            'type'        => 'input',
            'name'        => 'cc_owner_firstname',
            'placeholder' => 'First name',
            'value'       => $order_info['payment_firstname'],
        ));

        $this->data['cc_owner_lastname'] = HtmlElementFactory::create(array(
            'type'        => 'input',
            'name'        => 'cc_owner_lastname',
            'placeholder' => 'Last name',
            'value'       => $order_info['payment_lastname'],
        ));

        $this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
        $this->data['cc_number'] = HtmlElementFactory::create(array(
            'type'        => 'input',
            'name'        => 'cc_number',
            'attr'        => 'autocomplete="off"',
            'placeholder' => $this->language->get('entry_cc_number'),
            'value'       => '',
        ));

        $this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');

        $this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
        $this->data['entry_cc_cvv2_short'] = $this->language->get('entry_cc_cvv2_short');
        $this->data['cc_cvv2_help_url'] = $this->html->getURL('r/extension/default_authorizenet/cvv2_help');

        $this->data['cc_cvv2'] = HtmlElementFactory::create(array(
            'type'  => 'input',
            'name'  => 'cc_cvv2',
            'value' => '',
            'style' => 'short',
            'attr'  => ' autocomplete="off" ',
        ));

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');

        $months = array();

        for ($i = 1; $i <= 12; $i++) {
            $months[sprintf('%02d', $i)] = sprintf('%02d - ', $i).strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
        }
        $this->data['cc_expire_date_month'] = HtmlElementFactory::create(
            array(
                'type'    => 'selectbox',
                'name'    => 'cc_expire_date_month',
                'value'   => sprintf('%02d', date('m')),
                'options' => $months,
                'style'   => 'input-medium short',
            ));

        $today = getdate();
        $years = array();
        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $years[strftime('%Y', mktime(0, 0, 0, 1, 1, $i))] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
        }
        $this->data['cc_expire_date_year'] = HtmlElementFactory::create(array(
            'type'    => 'selectbox',
            'name'    => 'cc_expire_date_year',
            'value'   => sprintf('%02d', date('Y') + 1),
            'options' => $years,
            'style'   => 'short',
        ));

        $back = $this->request->get['rt'] != 'checkout/guest_step_3'
                ? $this->html->getSecureURL('checkout/payment')
                : $this->html->getSecureURL('checkout/guest_step_2');
        $this->data['back'] = HtmlElementFactory::create(array(
            'type'  => 'button',
            'name'  => 'back',
            'text'  => $this->language->get('button_back'),
            'style' => 'button',
            'href'  => $back,
            'icon'  => 'icon-arrow-left',
        ));

        $this->data['submit'] = HtmlElementFactory::create(array(
            'type'  => 'button',
            'name'  => 'authorizenet_button',
            'text'  => $this->language->get('button_confirm'),
            'style' => 'button btn-orange pull-right',
            'icon'  => 'icon-ok icon-white',
        ));

    }

    public function cvv2_help()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('default_authorizenet/default_authorizenet');

        $image = '<img src="'.$this->view->templateResource('/image/securitycode.jpg')
                .'" alt="'.$this->language->get('entry_what_cvv2').'" />';

        $this->view->assign('title', '');
        $this->view->assign('description', $image);

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->processTemplate('responses/content/content.tpl');
    }

    public function send()
    {
        if ( ! $this->csrftoken->isTokenValid()) {
            $json['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($json));

            return;
        }

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('default_authorizenet/default_authorizenet');
        //validate input
        $post = $this->request->post;
        //check if saved cc mode is used
        if ( ! $post['use_saved_cc']) {
            if (empty($post['cc_owner_firstname']) && empty($post['cc_owner_lastname'])) {
                $json['error'] = $this->language->get('error_incorrect_name');
            }
            if (empty($post['dataValue']) || empty($post['dataDescriptor'])) {
                $json['error'] = $this->language->get('error_system');
            }
        }

        if (isset($json['error'])) {
            $csrftoken = $this->registry->get('csrftoken');
            $json['csrfinstance'] = $csrftoken->setInstance();
            $json['csrftoken'] = $csrftoken->setToken();
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($json));

            return null;
        }

        $this->loadModel('checkout/order');
        $this->loadModel('extension/default_authorizenet');
        $this->loadLanguage('default_authorizenet/default_authorizenet');
        $order_id = $this->session->data['order_id'];

        $order_info = $this->model_checkout_order->getOrder($order_id);
        // currency code
        $currency = $this->currency->getCode();
        // order amount without decimal delimiter
        $amount = round($order_info['total'], 2);

        // Card owner name
        $card_firstname = html_entity_decode($post['cc_owner_firstname'], ENT_QUOTES, 'UTF-8');
        $card_lastname = html_entity_decode($post['cc_owner_lastname'], ENT_QUOTES, 'UTF-8');

        ADebug::checkpoint('AuthorizeNet Payment: Order ID '.$order_id);

        $pd = array(
            'amount'             => $amount,
            'currency'           => $currency,
            'order_id'           => $order_id,
            'cc_owner_firstname' => $card_firstname,
            'cc_owner_lastname'  => $card_lastname,
            'dataDescriptor'     => $post['dataDescriptor'],
            'dataValue'          => $post['dataValue'],
        );

        $p_result = $this->model_extension_default_authorizenet->processPayment($pd);

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
                $json['error'] = $this->language->get('error_system').'(abc)';
            }
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        if (isset($json['error']) && $json['error']) {
            $csrftoken = $this->registry->get('csrftoken');
            $json['csrfinstance'] = $csrftoken->setInstance();
            $json['csrftoken'] = $csrftoken->setToken();
        }
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

}

