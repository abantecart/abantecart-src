<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
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

/** @noinspection PhpUndefinedClassInspection */

if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ControllerResponsesExtensionCardConnect
 *
 * @property ModelExtensionCardconnect $model_extension_cardconnect
 */
class ControllerResponsesExtensionCardConnect extends AController
{
    public function test()
    {
        $this->loadLanguage('cardconnect/cardconnect');
        $test_mode = $this->config->get('cardconnect_test_mode') ? 'ON' : 'OFF';
        $api_endpoint = getCardConnectEndPoint();
        $merchid = $this->config->get('cardconnect_merchant_id');
        require_once DIR_EXT . 'cardconnect' . DS . 'core' . DS . 'lib' . DS . 'pest' . DS . 'PestJSON.php';
        $pest = new PestJSON($api_endpoint);
        $pest->throw_exceptions = false;
        try {
            $pest->setupAuth($this->config->get('cardconnect_username'), $this->config->get('cardconnect_password'));
            $response = $pest->put($api_endpoint . "auth", ['merchid' => $merchid]);
        } catch (Exception $e) {
            $response = null;
        }

        if (empty($response)) {
            $json['message'] = "Connection to CardConnect server can not be established. "
                . "Check your server configuration or contact your hosting provider.";
            $json['error'] = true;
        } elseif (is_array($response)) {
            $json['message'] = $this->language->get('cardconnect_connection_success');
            $json['success'] = false;
        } else {
            $json['message'] = 'CardConnect Error: Please check your API Credentials and try again.' . "\n"
                . 'Also please note that Test mode is ' . $test_mode . '!';
            $json['error'] = true;
        }

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }

    public function capture()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('cardconnect/cardconnect');
        $json = [];
        $amount = $this->_preformat_amount($this->request->post['amount']);
        if (has_value($this->request->post['order_id']) && $amount) {
            $order_id = $this->request->post['order_id'];
            /** @var ModelExtensionCardConnect $mdl */
            $mdl = $this->loadModel('extension/cardconnect');
            $cardconnect_order = $mdl->getCardconnectOrder($order_id);
            //get current order
            $ch_data = $mdl->getCardConnectCharge($cardconnect_order['retref']);
            //validate if captured
            if ($ch_data['amount'] >= $amount) {
                $capture = $mdl->captureCardconnect($cardconnect_order['retref'], $amount);
                if ($capture['amount']) {
                    // update main order status
                    $this->loadModel('sale/order');
                    $this->model_sale_order->addOrderHistory(
                        $order_id,
                        [
                            'order_status_id' => $this->config->get('cardconnect_status_success_settled'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $capture['amount'] . ' ' . $this->language->get(
                                    'text_captured_ok'
                                ),
                        ]
                    );
                    $mdl->addTransaction(
                        $cardconnect_order['cardconnect_order_id'],
                        'capture',
                        $capture['retref'],
                        $capture['amount'],
                        $capture['setlstat']
                    );
                    $json['msg'] = $this->language->get('text_captured_order');
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_capture');
                }
            } else {
                $json['error'] = true;
                $json['msg'] = $this->language->get('error_unable_to_capture');
            }
        } else {
            if ($this->request->post['amount'] <= 0 || $amount) {
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

        $this->loadLanguage('cardconnect/cardconnect');
        $json = [];
        $amount = $this->_preformat_amount($this->request->post['amount']);
        if (has_value($this->request->post['order_id']) && $amount) {
            $order_id = $this->request->post['order_id'];
            /** @var ModelExtensionCardConnect $mdl */
            $mdl = $this->loadModel('extension/cardconnect');
            $cardconnect_order = $mdl->getCardconnectOrder($order_id);
            //get current order
            $ch_data = $mdl->getcardconnectCharge($cardconnect_order['retref']);
            $remainder = $ch_data['amount'] - $ch_data['refunded'];

            //validate if captured
            if ($ch_data['captured'] && $remainder >= $amount) {
                $refund = $mdl->refundcardconnect($cardconnect_order['retref'], $amount);
                if ($refund['amount']) {
                    $json['msg'] = $this->language->get('text_refund_order');
                    // update main order status
                    $this->loadModel('sale/order');
                    $this->model_sale_order->addOrderHistory(
                        $order_id,
                        [
                            'order_status_id' => $this->config->get('cardconnect_status_refund'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $amount . ' ' . $this->language->get('text_refunded_ok'),
                        ]
                    );
                    $mdl->addTransaction(
                        $cardconnect_order['cardconnect_order_id'],
                        'refund',
                        $cardconnect_order['retref'],
                        $amount * -1,
                        $refund['resptext']
                    );
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_capture');
                }
            } else {
                $json['error'] = true;
                $json['msg'] = $this->language->get('error_unable_to_refund');
            }
        } else {
            $json['error'] = true;
            if ($this->request->post['amount'] <= 0 || !$amount) {
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

    protected function _preformat_amount($amount)
    {
        if (!$amount) {
            return false;
        }
        $amount = str_replace(' ', '', $amount);
        return preg_match('/^\d+\.?\d*$/', $amount) ? $amount : false;
    }

    public function void()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('cardconnect/cardconnect');
        if (has_value($this->request->post['order_id'])) {
            $order_id = $this->request->post['order_id'];
            /** @var ModelExtensionCardConnect $mdl */
            $mdl = $this->loadModel('extension/cardconnect');
            $cardconnect_order = $mdl->getCardconnectOrder($order_id);
            //get current order
            $ch_data = $mdl->getCardconnectCharge($cardconnect_order['retref']);
            //validate if captured
            if (!$ch_data['captured']) {
                //refund with full amount
                $void = $mdl->voidCardConnect($cardconnect_order['retref']);
                if ($void['authcode'] == 'REVERS') {
                    $json['msg'] = $this->language->get('text_voided');
                    // update main order status
                    $this->loadModel('sale/order');
                    $this->model_sale_order->addOrderHistory(
                        $order_id,
                        [
                            'order_status_id' => $this->config->get('cardconnect_status_void'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $this->language->get('text_voided'),
                        ]
                    );
                    $mdl->addTransaction(
                        $cardconnect_order['cardconnect_order_id'],
                        'void',
                        $cardconnect_order['retref'],
                        $ch_data['amount'] * -1,
                        $void['resptext']
                    );
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_void');
                }
            } else {
                $json['error'] = true;
                $json['msg'] = $this->language->get('error_unable_to_void');
            }
        } else {
            $json['error'] = true;
            $json['msg'] = $this->language->get('error_system');
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }
}