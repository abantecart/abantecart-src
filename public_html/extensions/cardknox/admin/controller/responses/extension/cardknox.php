<?php

if (!IS_ADMIN || !defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesExtensionCardKnox extends AController
{
    public function capture()
    {
        $json = array();
        if (has_value($this->request->get['order_id'])) {

            $this->loadModel('sale/order');
            $order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
            /** @var ModelExtensionCardKnox $mdl */
            $mdl = $this->loadModel('extension/cardknox');
            $this->loadLanguage('cardknox/cardknox');

            if (has_value($order_info['payment_method_data'])) {
                $endpointUrl = 'https://x1.cardknox.com/gateway';
                $payment_method_data = unserialize($order_info['payment_method_data']);
                $data = [];
                $data['xKey'] = $this->config->get('cardknox_transaction_key');
                $data['xVersion'] = '4.5.8';
                $data['xSoftwareName'] = 'AbanteCart';
                $data['xSoftwareVersion'] = VERSION;
                $data['xCommand'] = 'cc:capture';
                $data['xRefNum'] = $payment_method_data['xRefNum'];

                $curl = curl_init($endpointUrl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

                $result = curl_exec($curl);
                $json = array();

                $curl_error = curl_error($curl);
                curl_close($curl);
                parse_str($result, $response_info);

                if( $curl_error || $response_info['xResult'] != 'A' ) {
                    $this->session->data['error'] = $curl_error.' '.$response_info['xError'];
                } else {
                    $this->loadModel('extension/cardknox');
                    $payment_method_data['captured'] = true;
                    $payment_method_data['xBatch'] = $response_info['xBatch'];

                    $mdl->updatePaymentMethodData($this->request->get['order_id'], $payment_method_data);
                    $mdl->addOrderHistory(array(
                        'order_id'        => $this->request->get['order_id'],
                        'order_status_id' => $order_info['order_status_id'],
                        'notify'          => 0,
                        'comment'         => $this->currency->format(
                                $payment_method_data['xAuthAmount'],
                                $order_info['currency'],
                                $order_info['value']
                            )
                            .' captured.',
                    ));
                    $this->session->data['success'] = $this->language->get('text_capture_success');
                }
            } else {
                // no payment method data, funds can not be captured
                $this->session->data['error'] = $this->language->get('error_no_payment_method_data');
            }
        } else {
            // no order_id
            $this->session->data['error'] = $this->language->get('error_no_order_id');
        }

        $json['href'] = $this->html->getSecureURL('sale/order/payment_details', '&order_id='.(int)$this->request->get['order_id'].'&extension=cardknox');
        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));

    }

    public function refund()
    {

        /** @var ModelExtensionCardKnox $mdl */
        $mdl = $this->loadModel('extension/cardknox');
        $this->loadLanguage('cardknox/cardknox');
        $this->loadModel('sale/order');
        $order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
        $json = array();

        if ($order_info) {
            $amount = (float)$this->request->get['amount'];
            if ($amount <= $order_info['total']) {
                $payment_method_data = unserialize($order_info['payment_method_data']);
                if (has_value($order_info['payment_method_data']) && !$payment_method_data['refunded_amount']) {
                    $endpointUrl = 'https://x1.cardknox.com/gateway';
                    $data = [];
                    $data['xKey'] = $this->config->get('cardknox_transaction_key');
                    $data['xVersion'] = '4.5.8';
                    $data['xSoftwareName'] = 'AbanteCart';
                    $data['xSoftwareVersion'] = VERSION;
                    $data['xCommand'] = 'cc:refund';
                    $data['xRefNum'] = $payment_method_data['xRefNum'];
                    $data['xAmount'] = $amount;

                    $curl = curl_init($endpointUrl);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));

                    $response = curl_exec($curl);
                    $curl_error = curl_error($curl);
                    curl_close($curl);
                    parse_str($response, $result);

                    if ($result['xResult'] != 'A') {
                        $this->session->data['error'] = $result['xError'];
                    } else {
                            // update order_totals
                            $this->loadModel('extension/cardknox');
                            $mdl->processRefund(array(
                                'order_id' => $this->request->get['order_id'],
                                'amount'   => $amount,
                                'currency' => $order_info['currency'],
                            ));

                            $payment_method_data['refunded_amount'] = $amount;

                            $mdl->updatePaymentMethodData($this->request->get['order_id'], $payment_method_data);
                            $mdl->addOrderHistory(array(
                                'order_id'        => $this->request->get['order_id'],
                                'order_status_id' => $order_info['order_status_id'],
                                'notify'          => 0,
                                'comment'         => $this->currency->format($amount, $order_info['currency'], $order_info['value']).' refunded.',
                            ));
                            $this->session->data['success'] = $this->language->get('cardknox_text_refund_success');
                        }
                } else {
                    // no payment method data, funds can not be captured
                    $this->session->data['error'] = $this->language->get('cardknox_error_no_payment_method_data');
                }
            } else {
                // no or wrong amount
                $this->session->data['error'] = $this->language->get('cardknox_error_empty_amount');
            }
        } else {
            // no order_id
            $this->session->data['error'] = $this->language->get('cardknox_error_no_order_id');
        }

        $json['href'] = $this->html->getSecureURL('sale/order/payment_details', '&order_id='.(int)$this->request->get['order_id'].'&extension=cardknox');

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($json));
    }
}