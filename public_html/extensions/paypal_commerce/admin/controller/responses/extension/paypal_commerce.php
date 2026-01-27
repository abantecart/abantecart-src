<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2026 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

use PaypalServerSdkLib\PaypalServerSdkClient;
use PaypalServerSdkLib\PaypalServerSdkClientBuilder;

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ControllerResponsesExtensionPaypalCommerce
 *
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
        $mode = $this->request->get['mode'] == 'test' ? 'test' : 'live';
        $storeId = (int) $this->request->get['store_id'];

        if (!$authCode || !$sharedId || !$pmid) {
            $this->log->write(
                'Paypal Onboarding error: not enough parameters! ' . var_export($this->request->get, true)
            );
            $this->session->data['error'] = 'Oops, something went wrong. Incident was reported';
            redirect(
                $this->html->getSecureURL(
                    'extension/extensions/edit',
                    '&extension=paypal_commerce&store_id=' . $storeId
                )
            );
            return;
        }

        $credentials = $this->getPPCredentials($authCode, $sharedId, $pmid, $mode);
        if ($credentials) {
            $settings = [
                'paypal_commerce_onboarding' => 1,
                'paypal_commerce_test_mode'  => $mode == 'test' ? 1 : 0,
            ];

            foreach ($credentials as $k => $v) {
                $settings['paypal_commerce_' . $k] = $v;
            }

            $this->model_setting_setting->editSetting('paypal_commerce', $settings, $storeId);
            $this->loadLanguage('paypal_commerce/paypal_commerce');
            $this->session->data['success'] = $this->language->get('text_connect_success');

            redirect(
                $this->html->getSecureURL(
                    'extension/extensions/edit',
                    '&extension=paypal_commerce&store_id=' . $storeId . '&onboarded=1'
                )
            );
        } else {
            $this->session->data['error'] = 'Oops, something went wrong. Incident was reported';
            redirect(
                $this->html->getSecureURL(
                    'extension/extensions/edit',
                    '&extension=paypal_commerce&store_id=' . $storeId
                )
            );
        }
    }

    /**
     * Retrieves PayPal Commerce credentials using the provided authorization details.
     *
     * @param string $authCode The authorization code provided by PayPal.
     * @param string $sharedId The shared ID used for API integration with PayPal.
     * @param string $pmid The PayPal merchant ID for the account.
     * @param string $mode The operation mode, either 'test' for sandbox or 'live' for production.
     *
     * @return array An array containing the retrieved credentials or an empty array on failure.
     * @throws AException
     */
    protected function getPPCredentials($authCode, $sharedId, $pmid, $mode)
    {
        $output = [];
        if (!$authCode || !$sharedId || !$pmid || !$mode) {
            redirect($this->html->getSecureURL('extension/paypal_commerce_settings'));
        }

        $curl = curl_init();
        $endpointUrl = $mode == 'test'
            ? 'https://api-m.sandbox.paypal.com/v1/oauth2/token'
            : 'https://api-m.paypal.com/v1/oauth2/token';

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL            => $endpointUrl
                    . '?'
                    . http_build_query(
                        [
                            'grant_type'    => 'authorization_code',
                            'code'          => $authCode,
                            'code_verifier' => getNonce(UNIQUE_ID),
                        ]
                    ),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Basic ' . base64_encode($sharedId . ':'),
                ],
            ]
        );

        $response = curl_exec($curl);
        $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($respCode == 200) {
            $response = json_decode($response, true);
            $accessToken = $response['access_token'];
            $output = $this->obtainCredentials($accessToken, $pmid, $mode);
        } else {
            $this->log->write(__FILE__ . ': CURL ISSUE: ' . curl_error($curl) . "\n" . var_export($response, true));
        }
        curl_close($curl);
        return $output;
    }

    /**
     * Obtain credentials for a PayPal merchant account using the provided access token.
     *
     * @param string $accessToken The access token obtained from PayPal.
     * @param string $pmid The PayPal merchant ID for the account.
     * @param string $mode The operation mode, either 'test' for sandbox or 'live' for production.
     *
     * @return array An array containing the retrieved credentials or an empty array on failure.
     */
    protected function obtainCredentials($accessToken, $pmid, $mode)
    {
        $output = [];

        $curl = curl_init();
        $endpointUrl = $mode == 'test'
            ? 'https://api-m.sandbox.paypal.com/v1/customer/partners/' . $pmid . '/merchant-integrations/credentials/'
            : 'https://api-m.paypal.com/v1/customer/partners/' . $pmid . '/merchant-integrations/credentials/';

        curl_setopt_array(
            $curl,
            [
                CURLOPT_URL            => $endpointUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING       => '',
                CURLOPT_MAXREDIRS      => 10,
                CURLOPT_TIMEOUT        => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST  => 'GET',
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $accessToken,
                ],
            ]
        );

        $response = curl_exec($curl);
        $respCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($respCode == 200) {
            $json = json_decode($response, true);
            $output = [
                'client_id'     => $json['client_id'],
                'client_secret' => $json['client_secret'],
                'payer_id'      => $json['payer_id'],
            ];
        } else {
            $this->log->write(
                __FILE__ . ': CURL ISSUE: ' . curl_error($curl) . "\n" . var_export(curl_getinfo($curl), true)
            );
        }
        curl_close($curl);
        return $output;
    }

    public function test()
    {
        $this->load->library('json');
        $this->loadLanguage('paypal_commerce/paypal_commerce');
        $this->loadModel('setting/setting');
        extract($this->extractCredentials());
        $error_message = '';
        try {
            $client = getPaypalClient((string) $clientId, (string) $clientSecret, (int) $testMode);
            $client->getOrdersController()->getOrder(['id' => '1234']);
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }

        $json = [];

        if (!$error_message) {
            $json['message'] = $this->language->get('paypal_commerce_connection_success');
            $json['error'] = false;
        } else {
            $response = AJson::decode($error_message);
            if (is_object($response) && $response->error_description) {
                $error_message = "API error: " . $response->error_description;
            }
            $json['message'] = $this->language->getAndReplace(
                          'paypal_commerce_error_cannot_connect',
                replaces: nl2br($error_message)
            );
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

        extract($this->extractCredentials());

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
        } catch (Exception|Error $e) {
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

        $json = $data = [];

        if (has_value($this->request->post['order_id'])) {
            $order_id = $this->request->post['order_id'];
            $amount = preformatFloat($this->request->post['amount']);
            $this->loadModel('sale/order');
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');
            $paypalOrder = $mdl->getPaypalOrder($order_id);
            try {
                //get current order
                $chargeData = $mdl->getPaypalCharge($paypalOrder['charge_id']);
                $amt = 0.0;
                $authId = $currencyCode = '';
                $authorizations = $chargeData?->getPurchaseUnits()[0]->getPayments()->getAuthorizations();
                if ($authorizations) {
                    foreach ($authorizations as $auth) {
                        $authId = $auth->getId();
                        $amt += (float) $auth->getAmount()->getValue();
                        $currencyCode = $auth->getAmount()->getCurrencyCode();
                    }
                    $data['amount'] = round($amt, 2);
                }
                //if amount not set - take the total amount
                if (!$amount) {
                    $amount = $data['amount'];
                }

                //validate if captured
                if ($chargeData->getIntent() == 'AUTHORIZE' && $data['amount'] >= $amount) {
                    try {
                        $mdl->capture($authId, number_format($amount, 2), $currencyCode);
                        $json['msg'] = $this->language->get('text_captured_order');
                        // update main order status
                        $this->model_sale_order->addOrderHistory($order_id, [
                            'order_status_id' => $this->config->get('paypal_commerce_status_success_settled'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $amount . ' ' . $this->language->get('text_captured_ok'),
                        ]);
                    } catch (Exception|Error $e) {
                        $json['error'] = true;
                        $json['msg'] = __METHOD_ . ' ' . $e->getMessage();
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
                if ($message['details']) {
                    foreach ($message['details'] as $det) {
                        $json['msg'] .= ' ' . $det['description'];
                    }
                }
            }
        } else {
            $json['error'] = true;
            if ($this->request->post['amount'] <= 0) {
                $json['msg'] = $this->language->get('error_missing_amount');
            } else {
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
        $orderId = null;
        if ($this->request->post['order_id']) {
            $orderId = (int) $this->request->post['order_id'];
            $amount = preformatFloat($this->request->post['amount']);
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');
            $paypalOrder = $mdl->getPaypalOrder($orderId);
            $amt = 0.0;
            try {
                //get current order
                $chargeData = $mdl->getPaypalCharge($paypalOrder['charge_id']);
                $allRefunds = $chargeData->getPurchaseUnits()[0]->getPayments()->getRefunds();
                if ($allRefunds) {
                    foreach ($allRefunds as $refund) {
                        $amt += (float) $refund->getAmount()->getValue();
                    }
                    $data['refunded'] = true;
                    $data['amount_refunded'] = round($amt, 2);
                }
                $amt = 0.0;
                $captureId = $currencyCode = '';
                $allCaptures = $chargeData->getPurchaseUnits()[0]->getPayments()->getCaptures();
                if ($allCaptures) {
                    foreach ($allCaptures as $capt) {
                        $captureId = $capt->getId();
                        $amt += (float) $capt->getAmount()->getValue();
                        $currencyCode = $capt->getAmount()->getCurrencyCode();
                    }
                }
                $data['amount'] = round($amt, 2);
                $remainder = $data['amount'] - $data['amount_refunded'];
                //in case when amount for refund not set - take reminder
                if (!$amount) {
                    $amount = $remainder;
                }

                //validate if captured
                if ($remainder >= $amount) {
                    $refund = $mdl->refund($captureId, number_format($amount, 2), $currencyCode);
                    if ($refund->getId()) {
                        $json['msg'] = $this->language->get('text_refund_order');
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($orderId, [
                            'order_status_id' => $this->config->get('paypal_commerce_status_refund'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $amount . ' ' . $this->language->get('text_refunded_ok'),
                        ]);
                    }
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_refund');
                }
            } catch (Exception $e) {
                $json['error'] = true;
                $message = $e->getMessage();
                $message = AJson::decode($message, true);
                $json['msg'] = $message['message'];
                if ($message['details']) {
                    foreach ($message['details'] as $det) {
                        $json['msg'] .= ' ' . $det['description'];
                    }
                }
            }
        } else {
            $json['error'] = true;
            if ($this->request->post['amount'] <= 0) {
                $json['msg'] = $this->language->get('error_missing_amount');
            } else {
                $json['msg'] = $this->language->get('error_system');
            }
        }
        if (!$json['error'] && $orderId) {
            /** @var ModelSaleOrder $mdl */
            $mdl = $this->loadModel('sale/order');
            $mdl->addOrderHistory(
                $orderId,
                [
                    'order_status_id' => $this->order_status->getStatusByTextId('refunded'),
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
        $orderId = null;
        if (has_value($this->request->post['order_id'])) {
            $orderId = (int) $this->request->post['order_id'];
            /** @var ModelExtensionPaypalCommerce $mdl */
            $mdl = $this->loadModel('extension/paypal_commerce');
            $paypalOrder = $mdl->getPaypalOrder($orderId);

            try {
                //get current order
                $chargeData = $mdl->getPaypalCharge($paypalOrder['charge_id']);
                //validate if captured
                if ($chargeData) {
                    $void =
                        $mdl->void($chargeData->getPurchaseUnits()[0]->getPayments()->getAuthorizations()[0]->getId());
                    if ($void->getId()) {
                        $json['msg'] = $this->language->get('text_voided');
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($orderId, [
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
                if ($message['details']) {
                    foreach ($message['details'] as $det) {
                        $json['msg'] .= ' ' . $det['description'];
                    }
                }
            }
        } else {
            $json['error'] = true;
            $json['msg'] = $this->language->get('error_system');
        }

        if (!$json['error'] && $orderId) {
            /** @var ModelSaleOrder $mdl */
            $mdl = $this->loadModel('sale/order');
            $mdl->addOrderHistory(
                $orderId,
                [
                    'order_status_id' => $this->order_status->getStatusByTextId('canceled'),
                ]
            );
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

    protected function extractCredentials()
    {
        return [
            'testMode'     => $this->request->get['paypal_commerce_test_mode']
                ?: $this->config->get('paypal_commerce_test_mode'),
            'clientId'     => $this->request->get['paypal_commerce_client_id']
                ?: $this->config->get('paypal_commerce_client_id'),
            'clientSecret' => $this->request->get['paypal_commerce_client_secret']
                ?: $this->config->get('paypal_commerce_client_secret')
        ];
    }
}