<?php


if (! defined ( 'DIR_CORE' )) {
 header ( 'Location: static_pages/' );
}

class ControllerResponsesExtensionCardknox extends AController {
    protected $iFieldsVersion = '2.6.2006.0102';

    public function main()
    {
        $this->loadLanguage('cardknox/cardknox');
        $this->loadModel('checkout/order');
        $data['iFieldsVersion'] = $this->iFieldsVersion;

        $data['action'] = $this->html->getSecureURL('r/extension/cardknox/send');

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        if($this->session->data['error']){
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }
        //build submit form
        $form = new AForm();
        $form->setForm(['form_name' => 'cardknox']);
        $data['form_open'] = $form->getFieldHtml(
            [
                'type' => 'form',
                'name' => 'cardknox',
                'attr' => 'class = "form-horizontal validate-creditcard"',
                'csrf' => true,
            ]
        );

        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[sprintf('%02d', $i)] = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));
        }
        $data['cc_expire_date_month'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'cc_month',
                'value'   => sprintf('%02d', date('m')),
                'options' => $months,
                'style'   => 'short input-small',
            ]
        );

        $today = getdate();
        $years = [];
        for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
            $years[strftime('%Y', mktime(0, 0, 0, 1, 1, $i))] = strftime('%Y', mktime(0, 0, 0, 1, 1, $i));
        }
        $data['cc_expire_date_year'] = $form->getFieldHtml(
            [
                'type'    => 'selectbox',
                'name'    => 'cc_year',
                'value'   => sprintf('%02d', date('Y') + 1),
                'options' => $years,
                'style'   => 'short input-small',
            ]
        );

        $data['amount'] = $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'amount',
                'value' => $this->currency->format(
                                $order_info['total'],
                                $order_info['currency_code'],
                                1.00000,
                                false
                            )
            ]
        );
        //payment method of cardknox
        $data['method'] = $form->getFieldHtml(
            [
                'type'  => 'hidden',
                'name'  => 'method',
                'value' => 'cc'
            ]
        );
        $data['customer_name'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'xName',
                'value' => $order_info['payment_firstname'] .' '.$order_info['payment_lastname'],
                'placeholder' => $this->language->get('cardknox_text_your_name')
            ]
        );
        $data['routing_number'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'xRouting',
                'value' => '',
                'placeholder' => $this->language->get('cardknox_text_routing_number')
            ]
        );
        $data['ebt_type'] = $form->getFieldHtml(
            [
                'type'  => 'radio',
                'name'  => 'ebt_type',
                'options' => [
                    'ebtcb' => 'EBT Cash Benefits',
                    'ebtfs' => 'EBT Food Stamp'
                ],
                'value' => 'ebtcb'
            ]
        );
        $data['ebt_number'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'EBTCardNum',
                'value' => '',
                'placeholder' => $this->language->get('cardknox_text_ebt_number')
            ]
        );

        if ($this->request->get['rt'] == 'checkout/guest_step_3') {
            $back_url = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);
        } else {
            $back_url = $this->html->getSecureURL('checkout/payment', '&mode=edit', true);
        }

        $data['ebt_init_url'] = $this->html->getSecureURL('r/extension/cardknox/ebt_init','',true);
        $data['cardknox_text_ebt'] = $this->language->get('cardknox_text_ebt','cardknox/cardknox');
        $data['back'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'back',
                'text'  => $this->language->get('button_back'),
                'style' => 'button',
                'href'  => $back_url,
            ]
        );

        $data['submit'] = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'cardknox_button',
                'text'  => $this->language->get('button_confirm'),
                'style' => 'button btn-orange',
            ]
        );

        $this->view->batchAssign($data);
        $this->processTemplate('responses/cardknox.tpl');
    }
    protected function ed($str)
    {
        return html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    }

    public function send($redirect = false)
    {
        $output = [];

        if (!$this->csrftoken->isTokenValid()) {
            $output['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($output));
            return;
        }

        $endpointUrl = 'https://x1.cardknox.com/gateway';

        $this->loadLanguage('cardknox/cardknox');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $data = [];

        $data['xKey'] = $this->config->get('cardknox_transaction_key');
        $data['xVersion'] = '4.5.8';
        $data['xSoftwareName'] = 'AbanteCart';
        $data['xSoftwareVersion'] = VERSION;

        $data['xBillFirstname'] = $this->ed($order_info['payment_firstname']);
        $data['xBillLastname'] = $this->ed($order_info['payment_lastname']);
        $data['xBillCompany'] = $this->ed($order_info['payment_company']);
        $data['xBillStreet'] = $this->ed($order_info['payment_address_1']);
        $data['xBillCity'] = $this->ed($order_info['payment_city']);
        $data['xBillState'] = $this->ed($order_info['payment_zone']);
        $data['xBillZip'] = $this->ed($order_info['payment_postcode']);
        $data['xBillCountry'] = $this->ed($order_info['payment_country']);
        $data['xBillPhone'] = $order_info['telephone'];
        $data['xIP'] = $this->request->getRemoteIP();
        $data['xEmail'] = $order_info['email'];
        $data['xDescription'] = $this->ed($this->config->get('config_name'));
        $data['xAmount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['value'], false);
        $data['xCurrency'] = $this->currency->getCode();
        //Credit/Debit Card
        if($this->request->post['method'] == 'cc') {
            $data['xCommand'] = ($this->config->get('cardknox_transaction_method') == 'capture') ? 'cc:sale' : 'cc:authonly';
            $data['xCardNum'] = $this->request->post['xCardNum'];
            $data['xExp'] = $this->request->post['cc_month'].substr($this->request->post['cc_year'], 2, 2);
            $data['xCVV'] = $this->request->post['xCVV'];
        }
        //check ACH
        elseif($this->request->post['method'] == 'ach'){
            $data['xCommand'] = 'check:sale';
            $data['xAccount'] = $this->request->post['xACH'];
            $data['xRouting'] = $this->request->post['xRouting'];
        }
        //EBT
        elseif($this->request->post['method'] == 'ebt'){
            if($this->request->post['ebt_type'] == 'ebtcb') {
                $data['xCommand'] = 'ebtonline:cbsale';
            }else{
                $data['xCommand'] = 'ebtonline:fssale';
            }
            $data['xRefNum'] = $this->session->data['cardknox']['xRefNum'];
        }

        if(!$output['error']) {
            $data['xInvoice'] = $this->session->data['order_id'];

            /* Customer Shipping Address Fields */
            if ($order_info['shipping_method']) {
                $data['xShipFirstname'] = $this->ed($order_info['shipping_firstname']);
                $data['xShipLastname'] = $this->ed($order_info['shipping_lastname']);
                $data['xShipCompany'] = $this->ed($order_info['shipping_company']);
                $data['xShipStreet'] = $this->ed($order_info['shipping_address_1'])
                    .' '
                    .$this->ed($order_info['shipping_address_2']);
                $data['xShipCity'] = $this->ed($order_info['shipping_city']);
                $data['xShipState'] = $this->ed($order_info['shipping_zone']);
                $data['xShipZip'] = $this->ed($order_info['shipping_postcode']);
                $data['xShipCountry'] = $this->ed($order_info['shipping_country']);
            } else {
                $data['xShipFirstname'] = $this->ed($order_info['payment_firstname']);
                $data['xShipLastname'] = $this->ed($order_info['payment_lastname']);
                $data['xShipCompany'] = $this->ed($order_info['payment_company']);
                $data['xShipStreet'] = $this->ed($order_info['payment_address_1'])
                    .' '
                    .$this->ed($order_info['payment_address_2']);
                $data['xShipCity'] = $this->ed($order_info['payment_city']);
                $data['xShipState'] = $this->ed($order_info['payment_zone']);
                $data['xShipZip'] = $this->ed($order_info['payment_postcode']);
                $data['xShipCountry'] = $this->ed($order_info['payment_country']);
            }

            $curl = curl_init($endpointUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
            $apiResponse = curl_exec($curl);

            if (curl_error($curl)) {
                $output['error'] = 'CURL ERROR: '.curl_errno($curl).'::'.curl_error($curl);
                $this->log->write('CARDKNOX CURL ERROR: '.curl_errno($curl).'::'.curl_error($curl));
            } elseif ($apiResponse) {
                $response_info = [];
                parse_str($apiResponse, $response_info);

                if (($response_info['xResult'] == 'A')) {
                    $this->model_checkout_order->confirm(
                        $this->session->data['order_id'],
                        $this->config->get('cardknox_success_order_status')
                    );
                    $this->model_checkout_order->updatePaymentMethodData(
                        $this->session->data['order_id'],
                        serialize($response_info)
                    );
                    $this->model_checkout_order->update(
                        $this->session->data['order_id'],
                        $this->config->get('cardknox_success_order_status'),
                        var_export($response_info, true),
                        false
                    );
                    $redirect = $this->request->get_or_post('fast_checkout')
                        ? 'checkout/fast_checkout_success'
                        : 'checkout/success';
                    $this->session->data['processed_order_id'] = $this->session->data['order_id'];
                    $output['success'] = $this->html->getSecureURL($redirect, '&order_id='.$this->session->data['order_id']);
                    unset($this->session->data['order_id']);

                } else {
                    $output['error'] = $response_info['xError'];
                }
            } else {
                $output['error'] = 'Empty Gateway Response';
                $this->log->write('Cardknox CURL ERROR: Empty Gateway Response');
            }
            curl_close($curl);
        }

        if (isset($output['error'])) {
            if ($output['error']) {
                $csrftoken = $this->registry->get('csrftoken');
                $output['csrfinstance'] = $csrftoken->setInstance();
                $output['csrftoken'] = $csrftoken->setToken();
            }
        }
        if($redirect){
            if($output['success']) {
                redirect($output['success']);
                return;
            }else{
                $this->loadLanguage('cardknox/cardknox');
                $this->session->data['error'] = $this->language->get('cardknox_ebt_declined').'('.$response_info['xError'].': '.$response_info['xErrorCode'].')';
                $redirect = $this->request->get_or_post('fast_checkout') ? 'checkout/fast_checkout' : 'checkout/confirm';
                redirect($this->html->getSecureURL($redirect, '&method='.$this->request->post['method']));
            }
        }
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($output));
    }

    public function ebt_init()
    {
        if (!$this->csrftoken->isTokenValid()) {
            $json['error'] = $this->language->get('error_unknown');
            $this->load->library('json');
            $this->response->setOutput(AJson::encode($json));
            return;
        }

        $endpointUrl = 'https://x1.cardknox.com/gateway';

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $data = [];

        $data['xKey'] = $this->config->get('cardknox_transaction_key');
        $data['xVersion'] = '4.5.8';
        $data['xSoftwareName'] = 'AbanteCart';
        $data['xSoftwareVersion'] = VERSION;
        $data['xIP'] = $this->request->getRemoteIP();

        $data['xCommand'] = 'ebtonline:initiate';
        $data['xCardNum'] = $this->request->post['xCardNum'];

        $data['xBillFirstname'] = $this->ed($order_info['payment_firstname']);
        $data['xBillLastname'] = $this->ed($order_info['payment_lastname']);
        $data['xBillCompany'] = $this->ed($order_info['payment_company']);
        $data['xBillStreet'] = $this->ed($order_info['payment_address_1']);
        $data['xBillCity'] = $this->ed($order_info['payment_city']);
        $data['xBillState'] = $this->ed($order_info['payment_zone']);
        $data['xBillZip'] = $this->ed($order_info['payment_postcode']);
        $data['xBillCountry'] = $this->ed($order_info['payment_country']);
        $data['xBillPhone'] = $order_info['telephone'];
        /* Customer Shipping Address Fields */
        if ($order_info['shipping_method']) {
            $data['xShipMethod'] =  'Other';
            $data['xShipFirstname'] = $this->ed($order_info['shipping_firstname']);
            $data['xShipLastname'] = $this->ed($order_info['shipping_lastname']);
            $data['xShipCompany'] = $this->ed($order_info['shipping_company']);
            $data['xShipStreet'] = $this->ed($order_info['shipping_address_1'])
                                    . ' '
                                    . $this->ed($order_info['shipping_address_2']);
            $data['xShipCity'] = $this->ed($order_info['shipping_city']);
            $data['xShipState'] = $this->ed($order_info['shipping_zone']);
            $data['xShipZip'] = $this->ed($order_info['shipping_postcode']);
            $data['xShipCountry'] = $this->ed($order_info['shipping_country']);
        } else {
            $data['xShipMethod'] =  'Other';
            $data['xShipFirstname'] = $this->ed($order_info['payment_firstname']);
            $data['xShipLastname'] = $this->ed($order_info['payment_lastname']);
            $data['xShipCompany'] = $this->ed($order_info['payment_company']);
            $data['xShipStreet'] = $this->ed($order_info['payment_address_1'])
                    . ' '
                    . $this->ed($order_info['payment_address_2']);
            $data['xShipCity'] = $this->ed($order_info['payment_city']);
            $data['xShipState'] = $this->ed($order_info['payment_zone']);
            $data['xShipZip'] = $this->ed($order_info['payment_postcode']);
            $data['xShipCountry'] = $this->ed($order_info['payment_country']);
        }

        $curl = curl_init($endpointUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

        $apiResponse = curl_exec($curl);
        $json = [];

        if (curl_error($curl)) {
            $json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
            $this->log->write('CARDKNOX CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));
        } elseif ($apiResponse) {
            $response_info = [];
            parse_str($apiResponse, $response_info);
            if (( $response_info['xResult'] == 'A') ){
                $this->session->data['cardknox'] = $response_info;
                $json['success'] = true;
                $json['PinPadURL'] = $response_info['xPinPadURL'];
                $json['AccuID'] = $response_info['xAccuID'];
                $json['AccuReturnURL'] = $this->html->getSecureURL(
                    'r/extension/cardknox/ebt_finalize',
                    ($this->request->post['fast_checkout'] ? '&fast_checkout=1' : '')
                );
            } else {
                $json['error'] = $response_info['xError'];
            }
        } else {
            $json['error'] = 'Empty Gateway Response';
            $this->log->write('Cardknox CURL ERROR: Empty Gateway Response');
        }
        curl_close($curl);
        //change csrf-token every time
        $csrftoken = $this->registry->get('csrftoken');
        $json['csrfinstance'] = $csrftoken->setInstance();
        $json['csrftoken'] = $csrftoken->setToken();

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

    public function ebt_finalize()
    {
        if(!$this->request->is_POST()
            ||
            $this->request->post['AccuId'] != $this->session->data['cardknox']['xAccuID']
        ){
            http_response_code(404);
            exit;
        }

        if($this->request->post['AccuResponseCode'] != 'ACCU000'){
            $this->loadLanguage('cardknox/cardknox');
            $this->session->data['error'] = sprintf(
                $this->language->get('cardknox_pinpad_error'),
                $this->language->get('cardknox_pinpad_error_'.strtolower($this->request->post['AccuResponseCode']))
            );

            $redirect = $this->request->get['fast_checkout'] ? 'checkout/fast_checkout' : 'checkout/confirm';
            redirect($this->html->getSecureURL($redirect, '&method='.$this->request->post['method']));

        }

        $this->send(true);
    }
 }
