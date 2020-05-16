<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Licence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesExtensionDefaultPPStandart extends AController
{
    public $data = array();

    public function main()
    {
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['button_back'] = $this->language->get('button_back');

        if (!$this->config->get('default_pp_standart_test')) {
            $this->data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
        } else {
            $this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }

        //solution for embed mode do submit to parent window
        if ($this->config->get('embed_mode')) {
            $this->data['target_parent'] = 'target="_parent"';
        }

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->data['business'] = trim($this->config->get('default_pp_standart_email'));
        $this->data['item_name'] = html_entity_decode($this->config->get('store_name'), ENT_QUOTES, 'UTF-8');
        $this->data['currency_code'] = $order_info['currency'];
        $this->data['amount'] =
            $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], false);
        $this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
        $this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
        $this->data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
        $this->data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
        $this->data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
        $this->data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
        $this->data['country'] = $order_info['payment_iso_code_2'];
        $this->data['notify_url'] = $this->html->getURL('extension/default_pp_standart/callback');
        $this->data['email'] = $order_info['email'];
        $this->data['invoice'] = $this->session->data['order_id']
            .' - '.html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8')
            .' '.html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
        $this->data['lc'] = $this->session->data['language'];

        if (has_value($this->config->get('default_pp_standart_custom_logo'))) {
            if (strpos($this->config->get('default_pp_standart_custom_logo'), 'http') === 0) {
                $this->data['logoimg'] = $this->config->get('default_pp_standart_custom_logo');
            } else {
                $this->data['logoimg'] =
                    HTTPS_SERVER.'resources/'.$this->config->get('default_pp_standart_custom_logo');
            }
        }

        if (has_value($this->config->get('default_pp_standart_cartbordercolor'))) {
            $this->data['cartbordercolor'] = $this->config->get('default_pp_standart_cartbordercolor');
        }

        $this->load->library('encryption');
        $encryption = new AEncryption($this->config->get('encryption_key'));

        $this->data['products'] = array();
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['value'];
                } else {
                    $filename = $encryption->decrypt($option['value']);
                    $value = mb_substr($filename, 0, mb_strrpos($filename, '.'));
                }

                $option_data[] = array(
                    'name'  => $option['name'],
                    'value' => (mb_strlen($value) > 20 ? mb_substr($value, 0, 20).'..' : $value),
                );
            }

            $this->data['products'][] = array(
                'name'     => $product['name'],
                'model'    => $product['model'],
                'price'    => $this->currency->format($product['price'], $order_info['currency'], $order_info['value'],
                    false),
                'quantity' => $product['quantity'],
                'option'   => $option_data,
                'weight'   => $product['weight'],
            );
        }

        $this->data['discount_amount_cart'] = 0;
        $totals = $this->cart->buildTotalDisplay();

        foreach ($totals['total_data'] as $total) {
            if (in_array($total['id'], array('subtotal', 'total'))) {
                continue;
            }
            if (in_array($total['id'], array('promotion', 'coupon', 'balance'))) {
                $total['value'] = $total['value'] < 0 ? $total['value'] * -1 : $total['value'];
                $this->data['discount_amount_cart'] += $this->currency->format($total['value'], $order_info['currency'],
                    $order_info['value'], false);
            } else {
                $this->data['products'][] = array(
                    'name'     => $total['title'],
                    'model'    => '',
                    'price'    => $this->currency->format($total['value'], $order_info['currency'],
                        $order_info['value'], false),
                    'quantity' => 1,
                    'option'   => array(),
                    'weight'   => 0,
                );
            }
        }

        //check for virtual product such as gift certificate, account credit etc
        $virtual_products = $this->cart->getVirtualProducts();

        if ($virtual_products) {
            foreach ($virtual_products as $k => $virtual) {
                $this->data['products'][] = array(
                    'name'     => ($virtual['name'] ? $virtual['name'] : 'Virtual Product'),
                    'model'    => '',
                    'price'    => $this->currency->format($virtual['amount'], $order_info['currency'],
                        $order_info['value'], false),
                    'quantity' => ($virtual['quantity'] ? $virtual['quantity'] : 1),
                    'option'   => array(),
                    'weight'   => 0,
                );
                $this->data['items_total'] += ($virtual['quantity'] ? $virtual['quantity'] : 1)
                    * $this->currency->format($virtual['amount'], $order_info['currency'], $order_info['value'], false);
            }
        }

        if (!$this->config->get('default_pp_standart_transaction')) {
            $this->data['paymentaction'] = 'authorization';
        } else {
            $this->data['paymentaction'] = 'sale';
        }

        $this->data['return'] = $this->html->getSecureURL('checkout/success');

        if ($this->request->get['rt'] == 'checkout/guest_step_3') {
            $back_url = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);
        } else {
            $back_url = $this->html->getSecureURL('checkout/payment', '&mode=edit', true);
        }

        $this->data['cancel_return'] = $back_url;
        $this->data['back'] = $back_url;
        $this->data['custom'] = $encryption->encrypt($this->session->data['order_id']);
        $this->data['back'] = $this->html->buildElement(
            array(
                'type'  => 'button',
                'name'  => 'back',
                'text'  => $this->language->get('button_back'),
                'style' => 'button',
                'href'  => $back_url,
            ));
        $this->data['button_confirm'] = $this->html->buildElement(
            array(
                'type'  => 'submit',
                'name'  => $this->language->get('button_confirm'),
                'style' => 'button',
            ));

        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/default_pp_standart.tpl');
    }

    public function callback()
    {
        $this->load->library('encryption');
        $encryption = new AEncryption($this->config->get('encryption_key'));

        if (isset($this->request->post['custom'])) {
            $order_id = $encryption->decrypt($this->request->post['custom']);
        } else {
            $order_id = 0;
        }

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        $suspect = false;
        $message = '';
        if (!$order_info) {
            return false;
        }
        $method = $order_info['order_status_id'] ? 'update' : 'confirm';
        if ($this->request->post['payment_status'] != 'Reversed'
            && $order_info['order_status_id'] != $this->order_status->getStatusByTextId('failed')
            && $method == 'update'
        ) {
            return null;
        }

        if (!extension_loaded('curl')) {
            $this->log->write('Paypal Standart: CURL php-extension needs to be installed!');
            return false;
        }
        $order_total = round($order_info['total'] * $order_info['value'], 2);
        $currency_code = strtoupper($order_info['currency']);
        // check seller email and order amount and save message if not equal
        if ($this->request->post['receiver_email'] != $this->config->get('default_pp_standart_email')
            || $order_total != round($this->request->post['mc_gross'], 2)
            || $currency_code != strtoupper($this->request->post['mc_currency'])
        ) {
            $this->load->language('default_pp_standart/default_pp_standart');
            $message .= $this->language->get('text_suspect');
            $params = array(
                'payment_status',
                'pending_reason',
                'address_zip',
                'address_country_code',
                'address_name',
                'address_country',
                'address_city',
                'quantity',
                'payer_email',
                'first_name',
                'last_name',
                'payment_gross',
                'shipping',
                'ipn_track_id',
                'receiver_email',
            );
            foreach ($params as $p) {
                if (isset($this->request->post[$p])) {
                    $message .= $p.": ".$this->request->post[$p]."<br>\n";
                }
            }
            $msg = new AMessage();
            $msg->saveNotice(sprintf($this->language->get('text_suspect_subj'), $order_id), $message);
            $suspect = true;
        }

        $request = 'cmd=_notify-validate';
        foreach ($this->request->post as $key => $value) {
            $request .= '&'.$key.'='.urlencode(stripslashes(html_entity_decode($value, ENT_QUOTES, 'UTF-8')));
        }

        if ($this->config->get('default_pp_standart_test')) {
            $gateway_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $gateway_url = 'https://www.paypal.com/cgi-bin/webscr';
        }
        $ch = curl_init($gateway_url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        curl_close($ch);

        $args = array('order_id' => $order_id);
        if ($suspect === true) {
            // set pending status for all suspected orders
            $args['order_status_id'] = 1;
            $args['message'] = $message;
        } elseif ($this->request->post['payment_status'] == 'Reversed') {
            $args['order_status_id'] = $this->order_status->getStatusByTextId('reversed');
            if ($method == 'update') {
                $args['message'] = 'Changed by Paypal IPN';
                $this->messages->saveNotice(
                    'Order #'.$order_id.' has been reversed by Paypal IPN request.',
                    'See #admin#rt=sale/order/history&order_id='.$order_id.' for details'
                );
            }
        } elseif ($this->request->post['payment_status'] == 'Denied') {
            $args['order_status_id'] = $this->order_status->getStatusByTextId('denied');
            if ($method == 'update') {
                $args['message'] = 'Changed by Paypal IPN';
                $this->messages->saveNotice(
                    'Order #'.$order_id.' has been denied by Paypal IPN request.',
                    'See #admin#rt=sale/order/history&order_id='.$order_id.' for details'
                );
            }
        } elseif (strcmp($response, 'VERIFIED') == 0 || $this->request->post['payment_status'] == 'Completed') {
            $args['order_status_id'] = $this->config->get('default_pp_standart_order_status_id');
            if ($method == 'update') {
                $args['message'] = 'Changed by Paypal IPN';
                $this->messages->saveNotice(
                    'Order #'.$order_id.' has been changed by Paypal IPN request.',
                    'See #admin#rt=sale/order/history&order_id='.$order_id.' for details'
                );
            }
        } else {
            $args['order_status_id'] = $this->config->get('config_order_status_id');
            if ($method == 'update') {
                $args['message'] = 'Changed by Paypal IPN';
                $this->messages->saveNotice(
                    'Order #'.$order_id.' has been changed by paypal IPN request.',
                    'See #admin#rt=sale/order/history&order_id='.$order_id.' for details'
                );
            }
        }
        //call confirm or update method of model
        call_user_func_array(array($this->model_checkout_order, $method), $args);
        $this->model_checkout_order->updatePaymentMethodData($this->session->data['order_id'], $response);
        return true;
    }

    public function pending_payment()
    {
        $this->addChild('common/head', 'head', 'common/head.tpl');
        $this->addChild('common/footer', 'footer', 'common/footer.tpl');
        $this->document->setTitle('waiting for payment');
        $this->view->assign('text_message', 'waiting for payment confirmation');
        $this->view->assign('text_redirecting', 'redirecting');
        $this->view->assign('test_url', $this->html->getSecureURL('r/extension/default_pp_standart/is_confirmed'));
        $this->view->assign('success_url', $this->html->getSecureURL('checkout/success'));
        $this->processTemplate('responses/pending_ipn.tpl');
    }

    public function is_confirmed()
    {
        $order_id = (int)$this->session->data['order_id'];
        if (!$order_id) {
            $result = true;
        } else {
            $this->loadModel('checkout/order');
            $order_info = $this->model_checkout_order->getOrder($order_id);
            //do nothing if order confirmed or it's not created with paypal standart
            if ((int)$order_info['order_status_id'] != 0
                || $order_info['payment_method_key'] != 'default_pp_standart'
            ) {
                $result = true;
            } else {
                $result = false;
            }
        }

        $this->load->library('json');
        $this->response->addJSONHeader();
        $this->response->setOutput(AJson::encode(array('result' => $result)));
    }
}
