<?php
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ControllerResponsesExtensionPaypalCommerce
 *
 * @property ModelExtensionPaypalCommerce $model_extension_paypal_commerce
 */
class ControllerResponsesExtensionPaypalCommerce extends AController
{
    public function onboard()
    {
        $this->load->library('json');
        $this->loadLanguage('paypal_commerce/paypal_commerce');
        $this->loadModel('setting/setting');

        $authCode = $this->request->get['authCode'];
        $sharedId = $this->request->get['sharedId'];
        $pmid = $this->request->get['pmid'];
        $mode = $this->request->get['mode']=='test' ? 'test' : 'live';
        $storeId = (int)$this->request->get['store_id'];

        if(!$authCode || !$sharedId || !$pmid){
            $this->log->write('Paypal Onboarding error: not enough parameters! '. var_export($this->request->get, true));
            $this->session->data['error'] = 'Oops, something went wrong. Incident was reported';
            redirect(
                $this->html->getSecureURL(
                'extension/extensions/edit',
                '&extension=paypal_commerce&store_id='.$storeId
                )
            );
            return;
        }

        $credentials = $this->getPPCredentials( $authCode, $sharedId, $pmid, $mode );
        if($credentials) {
            $settings = [
                'paypal_commerce_onboarding' => 1,
                'paypal_commerce_test_mode'  => $mode == 'test' ? 1 : 0
            ];

            foreach($credentials as $k=>$v){
                $settings['paypal_commerce_'.$k] = $v;
            }

            $this->model_setting_setting->editSetting('paypal_commerce', $settings, $storeId);
            $this->loadLanguage('paypal_commerce/paypal_commerce');
            $this->session->data['success'] = $this->language->get('text_connect_success');

            redirect(
                $this->html->getSecureURL(
                    'extension/extensions/edit',
                    '&extension=paypal_commerce&store_id='.$storeId.'&onboarded=1'
                )
            );
        }else{
            $this->session->data['error'] = 'Oops, something went wrong. Incident was reported';
            redirect(
                $this->html->getSecureURL(
                    'extension/extensions/edit',
                    '&extension=paypal_commerce&store_id='.$storeId
                )
            );
        }

    }

    protected function getPPCredentials($authCode, $sharedId, $pmid, $mode)
    {
        $output = [];
        if(!$authCode || !$sharedId || !$pmid || !$mode){
            redirect($this->html->getSecureURL('extension/paypal_commerce_settings'));
        }

        $curl = curl_init();
        $endpointUrl = $mode == 'test'
            ? 'https://api-m.sandbox.paypal.com/v1/oauth2/token'
            : 'https://api-m.paypal.com/v1/oauth2/token';

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL => $endpointUrl .'?grant_type=authorization_code&code='.$authCode.'&code_verifier='.base64_encode(UNIQUE_ID),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic '.base64_encode($sharedId.':')
                ],
            ]
        );

        $response = curl_exec($curl);
        $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($respCode == 200) {
            $response = json_decode($response, true);
            $accessToken = $response['access_token'];
            $output = $this->obtainCredentials($accessToken, $pmid, $mode);
        }else{
            $this->log->write(__FILE__.': CURL ISSUE: '.curl_error($curl)."\n".var_export($response, true));
        }
        curl_close($curl);
        return $output;
    }

    protected function obtainCredentials($accessToken, $pmid, $mode)
    {
        $output = [];

        $curl = curl_init();
        $endpointUrl = $mode == 'test'
            ? 'https://api-m.sandbox.paypal.com/v1/customer/partners/'.$pmid.'/merchant-integrations/credentials/'
            : 'https://api-m.paypal.com/v1/customer/partners/'.$pmid.'/merchant-integrations/credentials/';

        curl_setopt_array($curl,
            [
                CURLOPT_URL => $endpointUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer '.$accessToken
                ],
            ]
        );

        $response = curl_exec($curl);
        $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if($respCode == 200) {
            $json = json_decode($response, true);
            $output = [
              'client_id'     => $json['client_id'],
              'client_secret' => $json['client_secret'],
            ];
        }else{
            $this->log->write(__FILE__.': CURL ISSUE: '.curl_error($curl)."\n".var_export(curl_getinfo($curl), true));
        }
        curl_close($curl);
        return $output;
    }

    public function test()
    {
        $this->load->library('json');
        $this->loadLanguage('paypal_commerce/paypal_commerce');
        $this->loadModel('setting/setting');
        $testMode = $this->request->get['paypal_commerce_test_mode']  ?: $this->config->get('paypal_commerce_test_mode');
        $clientId = $this->request->get['paypal_commerce_client_id']  ?: $this->config->get('paypal_commerce_client_id');
        $clientSecret = $this->request->get['paypal_commerce_client_secret']  ?: $this->config->get('paypal_commerce_client_secret');
        $error_message = '';
        try {
            $client = getPaypalClient($clientId, $clientSecret, $testMode);
            $request = new PayPalCheckoutSdk\Products\ProductsGetList();
            $client->execute($request);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        $json = [];

        if (!$error_message) {
            $json['message'] = $this->language->get('paypal_commerce_connection_success');
            $json['error'] = false;
        } else {
            $response = AJson::decode($error_message);
            if(is_object($response) && $response->error_description){
                $error_message = "API error : ".$response->error_description;
            }
            $json['message'] = "Connection to Paypal server can not be established.<br>"
                            .$error_message."
                            .<br>Check your server configuration or contact your hosting provider.";
            $json['error'] = true;
        }


        $this->response->setOutput(AJson::encode($json));
    }

    public function update_webhooks()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->load->library('json');
        $this->loadLanguage('paypal_commerce/paypal_commerce');
        $this->loadModel('setting/setting');

        $testMode = $this->request->get['paypal_commerce_test_mode'] ?: $this->config->get('paypal_commerce_test_mode');
        $clientId = $this->request->get['paypal_commerce_client_id'] ?: $this->config->get('paypal_commerce_client_id');
        $clientSecret = $this->request->get['paypal_commerce_client_secret'] ?: $this->config->get('paypal_commerce_client_secret');

        try {
            $this->config->set('paypal_commerce_test_mode', $testMode);
            $this->config->set('paypal_commerce_client_id', $clientId);
            $this->config->set('paypal_commerce_client_secret', $clientSecret);
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');
            //re-init ApiClient
            $mdl->__construct($this->registry);
            $mdl->updateWebHooks();

            $json['message'] = $this->language->get('paypal_commerce_text_register_webhooks_success');
            $json['error'] = false;
            $this->extensions->hk_UpdateData($this, __FUNCTION__);
            $this->response->setOutput(AJson::encode($json));
        } catch (Exception $e) {
            $error = new AError('');
            $error->toJSONResponse('VALIDATION_ERROR_406', ['error_text' => $e->getMessage()]);
        }
    }

    public function capture()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('paypal_commerce/paypal_commerce');
        $this->load->library('json');

        $json = [];

        if (has_value($this->request->post['order_id'])) {
            $order_id = $this->request->post['order_id'];
            $amount = (float)preg_replace('/[^0-9\.]/', '.', $this->request->post['amount']);
            $this->loadModel('extension/paypal_commerce');
            $this->loadModel('sale/order');
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');
            $paypalOrder = $mdl->getPaypalOrder($order_id);
            try {
                //get current order
                $chargeData = $mdl->getPaypalCharge($paypalOrder['charge_id']);
                $amt = 0;
                $authId = $currencyCode = '';
                if($chargeData->purchase_units[0]->payments->authorizations) {
                    foreach ($chargeData->purchase_units[0]->payments->authorizations as $auth) {
                        $authId = $auth->id;
                        $amt += $auth->amount->value;
                        $currencyCode = $auth->amount->currency_code;
                    }
                    $data['amount'] = round($amt, 2);
                }
                //if amount not set - take total amount
                if(!$amount){
                    $amount = $data['amount'];
                }

                //validate if captured
                if ($chargeData->intent == 'AUTHORIZE' && $data['amount'] >= $amount) {
                    $capture = $mdl->capture($authId, $amount, $currencyCode);
                    if ($capture->id) {
                        $json['msg'] = $this->language->get('text_captured_order');
                        // update main order status

                        $this->model_sale_order->addOrderHistory($order_id, [
                            'order_status_id' => $this->config->get('paypal_commerce_status_success_settled'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $amount.' '.$this->language->get('text_captured_ok'),
                        ]);
                    }
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_capture');
                }
            } catch (Exception $e) {
                $json['error'] = true;
                $message = $e->getMessage();
                $message = AJson::decode($message, true);
                $json['msg'] = $message['message'];
                if($message['details']){
                    foreach($message['details'] as $det){
                        $json['msg'] .= ' '.$det['description'];
                    }
                }
            }

        } else {
            if ($this->request->post['amount'] <= 0) {
                $json['error'] = true;
                $json['msg'] = $this->language->get('error_missing_amount');
            } else {
                $json['error'] = true;
                $json['msg'] = $this->language->get('error_system');
            }
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

    public function refund()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('paypal_commerce/paypal_commerce');
        $this->load->library('json');

        $json = [];

        if (has_value($this->request->post['order_id'])) {

            $order_id = $this->request->post['order_id'];
            $amount = (float)preg_replace('/[^0-9\.]/', '.', $this->request->post['amount']);

            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');

            $paypalOrder = $mdl->getPaypalOrder($order_id);
            $amt = 0;
            try {
                //get current order
                $chargeData = $mdl->getPaypalCharge($paypalOrder['charge_id']);
                if($chargeData->purchase_units[0]->payments->refunds){
                    foreach($chargeData->purchase_units[0]->payments->refunds as $refund) {
                        $amt += $refund->amount->value;
                    }
                    $data['refunded'] = true;
                    $data['amount_refunded'] = round($amt, 2);
                }
                $amt = 0;
                $captureId = $currencyCode = '';
                foreach($chargeData->purchase_units[0]->payments->captures as $capt) {
                    $captureId = $capt->id;
                    $amt += $capt->amount->value;
                    $currencyCode = $capt->amount->currency_code;
                }
                $data['amount'] = round($amt, 2);
                $remainder = $data['amount'] - $data['amount_refunded'];
                //in case when amount for refund not set - take reminder
                if(!$amount){
                   $amount = $remainder;
                }

                //validate if captured
                if ($remainder >= $amount) {

                    $refund = $mdl->refund($captureId, $amount, $currencyCode);

                    if ($refund->id) {
                        $json['msg'] = $this->language->get('text_refund_order');
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($order_id, [
                            'order_status_id' => $this->config->get('paypal_commerce_status_refund'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $amount.' '.$this->language->get('text_refunded_ok'),
                        ]);
                    }
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_refund');
                }
            } catch (\Exception $e) {
                $json['error'] = true;
                $message = $e->getMessage();
                $message = AJson::decode($message, true);
                $json['msg'] = $message['message'];
                if($message['details']){
                    foreach($message['details'] as $det){
                        $json['msg'] .= ' '.$det['description'];
                    }
                }
            }
        } else {
            if ($this->request->post['amount'] <= 0) {
                $json['error'] = true;
                $json['msg'] = $this->language->get('error_missing_amount');
            } else {
                $json['error'] = true;
                $json['msg'] = $this->language->get('error_system');
            }
        }
        if(!$json['error']){
            /** @var ModelSaleOrder $mdl */
            $mdl = $this->loadModel('sale/order');
            $mdl->addOrderHistory(
                $order_id,
                [
                    'order_status_id' => $this->order_status->getStatusByTextId('refunded')
                ]
            );
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->response->setOutput(AJson::encode($json));
    }

    public function void()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->load->library('json');
        $json = [];
        if (has_value($this->request->post['order_id'])) {
            $order_id = $this->request->post['order_id'];
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');

            $paypalOrder = $mdl->getPaypalOrder($order_id);
            $amt = 0;
            try {
                //get current order
                $chargeData = $mdl->getPaypalCharge($paypalOrder['charge_id']);
                //validate if captured
                if ($chargeData) {
                    $void = $mdl->void( $chargeData->purchase_units[0]->payments->authorizations[0]->id );
                    if ($void->id) {
                        $json['msg'] = $this->language->get('text_voided');
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($order_id, [
                            'order_status_id' => $this->config->get('paypal_commerce_status_void'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $this->language->get('text_voided'),
                        ]);
                    }

                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_void');
                }
            } catch (Exception $e) {
                $json['error'] = true;
                $message = $e->getMessage();
                $message = AJson::decode($message, true);
                $json['msg'] = $message['message'];
                if($message['details']){
                    foreach($message['details'] as $det){
                        $json['msg'] .= ' '.$det['description'];
                    }
                }
            }

        } else {
            $json['error'] = true;
            $json['msg'] = $this->language->get('error_system');
        }

        if(!$json['error']){
            /** @var ModelSaleOrder $mdl */
            $mdl = $this->loadModel('sale/order');
            $mdl->addOrderHistory(
                $order_id,
                [
                    'order_status_id' => $this->order_status->getStatusByTextId('canceled')
                ]
            );
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }


}