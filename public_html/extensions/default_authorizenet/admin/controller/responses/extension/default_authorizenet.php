<?php

/**
 * Class ControllerResponsesExtensionAuthorizeNet
 *
 * @property ModelExtensionDefaultAuthorizenet $model_extension_default_authorizenet
 */
class ControllerResponsesExtensionDefaultAuthorizeNet extends AController
{

    public function capture()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('default_authorizenet/default_authorizenet');
        $json = array();

        if (has_value($this->request->post['order_id']) && $this->request->post['amount'] > 0) {
            $order_id = $this->request->post['order_id'];
            $amount = preformatFloat($this->request->post['amount']);
            $this->loadModel('extension/default_authorizenet');
            $authorizenet_order = $this->model_extension_default_authorizenet->getAuthorizeNetOrder($order_id);
            try {
                //get current order
                $ch_data = $this->model_extension_default_authorizenet->getAuthorizeNetTransaction(
                    $authorizenet_order['charge_id']
                );
                $ch_data['authAmount'] = round($ch_data['authAmount'], 2);

                //validate if captured
                if ( ! $ch_data['captured'] && $ch_data['authAmount'] >= $amount) {
                    $capture = $this->model_extension_default_authorizenet->captureAuthorizeNet(
                        $authorizenet_order['charge_id'],
                        $amount
                    );
                    if ( ! $capture['error']) {
                        $json['msg'] = $this->language->get('text_captured_order');
                        $json['msg'] .= "\n".$capture['description'];

                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($order_id, array(
                            'order_status_id' => $this->config->get('default_authorizenet_status_success_settled'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $amount.' '.$this->language->get('text_captured_ok'),
                        ));
                    } else {
                        $json['error'] = true;
                        $json['msg'] = $capture['error'];
                    }
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_capture');
                }
            } catch (AException $e) {
                $json['error'] = true;
                $json['msg'] = $e->getMessage();
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

        $this->loadLanguage('default_authorizenet/default_authorizenet');
        $json = array();

        if (has_value($this->request->post['order_id']) && $this->request->post['amount'] > 0) {
            $order_id = (int)$this->request->post['order_id'];
            $amount = preformatFloat($this->request->post['amount']);
            $this->loadModel('extension/default_authorizenet');
            $authorizenet_order = $this->model_extension_default_authorizenet->getAuthorizeNetOrder($order_id);
            try {
                //get current order
                $ch_data = $this->model_extension_default_authorizenet->getAuthorizeNetTransaction(
                    $authorizenet_order['charge_id']
                );
                if (in_array($ch_data['transactionStatus'], array('settledSuccessfully'))) {
                    $ch_data['captured'] = true;
                }
                $ch_data['authAmount'] = round($ch_data['authAmount'], 2);
                $ch_data['amount_refunded'] = round($ch_data['amount_refunded'], 2);
                $remainder = $ch_data['authAmount'] - $ch_data['amount_refunded'];

                //validate if captured
                if ($ch_data['captured'] && $remainder >= $amount) {
                    $refund = $this->model_extension_default_authorizenet->refundAuthorizeNet($ch_data, $amount);

                    if ( ! $refund['error']) {
                        $json['msg'] = $this->language->get('text_refund_order');
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($order_id, array(
                            'order_status_id' => $this->config->get('default_authorizenet_status_refund'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $amount.' '.$this->language->get('text_refunded_ok'),
                        ));
                    } else {
                        $json['error'] = true;
                        $json['msg'] = $this->language->get('error_unable_to_refund');
                        $json['msg'] .= "\n".$refund['error'];
                    }
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_refund');
                }
            } catch (Exception $e) {
                $json['error'] = true;
                $json['msg'] = $e->getMessage();
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

    public function void()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $this->loadLanguage('default_authorizenet/default_authorizenet');
        $json = array();
        if (has_value($this->request->post['order_id'])) {
            $order_id = (int)$this->request->post['order_id'];
            $this->loadModel('extension/default_authorizenet');
            $authorizenet_order = $this->model_extension_default_authorizenet->getAuthorizeNetOrder($order_id);
            try {
                //get current order
                $ch_data = $this->model_extension_default_authorizenet->getAuthorizeNetTransaction(
                    $authorizenet_order['charge_id']
                );
                $can_void = false;
                if (in_array($ch_data['transactionStatus'],
                            array('authorizedPendingCapture','capturedPendingSettlement'))
                ) {
                    $can_void = true;
                }

                //validate if captured
                if ( $can_void ) {
                    //refund with full amount
                    $ch_data['amount'] = round($ch_data['amount'], 2);
                    $void = $this->model_extension_default_authorizenet->voidAuthorizeNet(
                        $authorizenet_order['charge_id']
                    );

                    if ( ! $void['error']) {
                        $json['msg'] = $this->language->get('text_voided');
                        $json['msg'] .= "\n".$void['description'];
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($order_id, array(
                            'order_status_id' => $this->config->get('default_authorizenet_status_void'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $this->language->get('text_voided'),
                        ));
                    }
                } else {
                    $json['error'] = true;
                    $json['msg'] = $this->language->get('error_unable_to_void');
                }
            } catch (Exception $e) {
                $json['error'] = true;
                $json['msg'] = $e->getMessage();
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