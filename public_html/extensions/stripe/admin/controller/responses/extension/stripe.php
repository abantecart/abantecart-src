<?php
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}

/**
 * Class ControllerResponsesExtensionStripe
 *
 */
class ControllerResponsesExtensionStripe extends AController
{
    public function capture()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('stripe/stripe');
        $json = [];

        if (has_value($this->request->post['order_id']) && $this->request->post['amount'] > 0) {
            $order_id = $this->request->post['order_id'];
            $amount = preformatFloat($this->request->post['amount']);
            /** @var ModelExtensionStripe $mdl */
            $mdl = $this->loadModel('extension/stripe');
            $stripe_order = $mdl->getStripeOrder($order_id);
            try {
                //get current order
                $ch_data = $mdl->getStripeCharge($stripe_order['charge_id']);
                $ch_data['amount'] = round($ch_data['amount'] / 100, 2);
                //validate if captured

                if (!$ch_data['captured'] && $ch_data['amount'] >= $amount) {
                    //get current order
                    if(is_int(strpos($stripe_order['charge_id'],'pi_'))){
                        $method = 'capturePaymentIntent';
                    }else{
                        $method = 'captureStripe';
                    }

                    $capture = $mdl->{$method}($stripe_order['charge_id'], $amount);
                    if ($capture['amount']) {
                        $json['msg'] = $this->language->get('text_captured_order');
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($order_id, [
                            'order_status_id' => $this->config->get('stripe_status_success_settled'),
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
                $json['msg'] = $e->getMessage();
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

        $this->loadLanguage('stripe/stripe');
        $json = [];

        if ($this->request->post['order_id'] && $this->request->post['amount'] > 0) {
            $order_id = (int)$this->request->post['order_id'];
            $amount = preformatFloat($this->request->post['amount']);
            /** @var ModelExtensionStripe $mdl */
            $mdl = $this->loadModel('extension/stripe');
            $stripe_order = $mdl->getStripeOrder($order_id);
            try {
                //get current order
                $ch_data = $mdl->getStripeCharge($stripe_order['charge_id']);
                $ch_data['amount'] = round($ch_data['amount'] / 100, 2);
                $ch_data['amount_refunded'] = round($ch_data['amount_refunded'] / 100, 2);
                $remainder = $ch_data['amount'] - $ch_data['amount_refunded'];
                //validate if captured
                if ($ch_data['captured'] && $remainder >= $amount) {
                    $refund = $mdl->refund($ch_data->id, $amount);
                    if ($refund['amount']) {
                        $json['msg'] = $this->language->get('text_refund_order');
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($order_id, [
                            'order_status_id' => $this->config->get('stripe_status_refund'),
                            'notify'          => 0,
                            'append'          => 1,
                            'comment'         => $amount.' '.$this->language->get('text_refunded_ok'),
                        ]);
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

    public function void()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $json = [];
        if (has_value($this->request->post['order_id'])) {
            $order_id = $this->request->post['order_id'];
            /** @var ModelExtensionStripe $mdl */
            $mdl = $this->loadModel('extension/stripe');
            $stripe_order = $mdl->getStripeOrder($order_id);
            try {
                //get current order
                if(is_int(strpos($stripe_order['charge_id'],'pi_'))){
                    $paymentIntent = true;
                    $method = 'getPaymentIntent';
                }else{
                    $paymentIntent = false;
                    $method = 'getStripeCharge';
                }
                $ch_data = $mdl->{$method}($stripe_order['charge_id']);

                //validate if captured
                if (!$ch_data['captured']) {
                    //refund with full amount
                    $ch_data['amount'] = round($ch_data['amount'] / 100, 2);
                    if($paymentIntent){
                        $refund = $mdl->cancelPaymentIntent( $stripe_order['charge_id'] );
                    }else {
                        $refund = $mdl->refund(
                            $ch_data['charge_id'],
                            $ch_data['amount']
                        );
                    }

                    if ($refund['amount']) {
                        $json['msg'] = $this->language->get('text_voided');
                        // update main order status
                        $this->loadModel('sale/order');
                        $this->model_sale_order->addOrderHistory($order_id, [
                            'order_status_id' => $this->config->get('stripe_status_void'),
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