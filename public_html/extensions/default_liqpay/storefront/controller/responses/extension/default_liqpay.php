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

class ControllerResponsesExtensionDefaultLiqPay extends AController
{
    public function main()
    {
        $this->load->model('checkout/order');

        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $order_id = $this->session->data['order_id'];
        $description = 'Order #'.$order_id;
        $order_id .= '#'.time();

        $private_key = $this->config->get('default_liqpay_private_key');
        $public_key = $this->config->get('default_liqpay_public_key');
        $currency = $order_info['currency'];
        if ($currency == 'RUR') {
            $currency = 'RUB';
        }

        $amount = $this->currency->format(
            $order_info['total'],
            $order_info['currency_code'],
            $order_info['currency_value'],
            false
        );

        $fields = [];
        $fields['action'] = 'pay';
        $fields['version'] = '3';
        $fields['amount'] = $amount;
        $fields['currency'] = $currency;
        $fields['description'] = $description;
        $fields['order_id'] = 'order_id_'.$order_id;
        $fields['sandbox'] = (int) $this->config->get('default_liqpay_test_mode');
        $fields['result_url'] = $this->html->getSecureURL(
            'r/extension/default_liqpay/confirm',
            is_int(strpos($this->request->server['QUERY_STRING'],'rt=r/checkout/pay')) ? '&fast_checkout=1' : ''
        );

        $fields['server_url'] = $this->html->getSecureURL('extension/default_liqpay/callback');

        $liqpay = new LiqPay($public_key, $private_key);
        $params = $liqpay->cnb_form_raw($fields);

        $form = new AForm();
        $form->setForm(['form_name' => 'checkout']);
        $data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'    => 'form',
                'name'    => 'checkout',
                'action'  => $params['url'],
                'enctype' => 'application/x-www-form-urlencoded',
            ]
        );
        unset($params['url']);

        foreach ($params as $k => $val) {
            $data['form']['fields'][$k] = $form->getFieldHtml(
                [
                    'type'  => 'hidden',
                    'name'  => $k,
                    'value' => $val,
                ]
            );
        }

        if ($this->request->get['rt'] == 'checkout/guest_step_3') {
            $back_url = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);
        } else {
            $back_url = $this->html->getSecureURL('checkout/payment', '&mode=edit', true);
        }

        $data['form']['back'] = $form->getFieldHtml(
            [
                'type'  => 'button',
                'name'  => 'back',
                'text'  => $this->language->get('button_back'),
                'style' => 'button',
                'href'  => $back_url,
            ]
        );
        $data['form']['submit'] = $form->getFieldHtml(
            [
                'type' => 'submit',
                'name' => $this->language->get('button_confirm'),
            ]
        );

        $this->view->batchAssign($data);
        $this->processTemplate('responses/default_liqpay.tpl');
    }

    public function confirm()
    {
        $order_id = $this->session->data['order_id'];
        if(!$order_id){
            return;
        }
        /** @var ModelCheckoutOrder $mdl */
        $mdl = $this->loadModel('checkout/order');
        $mdl->confirm(
            $order_id,
            $this->order_status->getStatusByTextId('pending')
        );

        $this->session->data['processed_order_id'] = $order_id;
        unset($this->session->data['order_id']);
        $redirect = $this->request->get['fast_checkout']
            ? 'checkout/fast_checkout_success'
            : 'checkout/success';
        redirect( $this->html->getSecureURL( $redirect, '&order_id='.$order_id ) );
    }

    private function getOrderStatus($liqpay_status)
    {
        if ($this->config->get('default_liqpay_order_status_id') != $this->order_status->getStatusByTextId('completed')) {
            return $this->config->get('default_liqpay_order_status_id');
        }
        //for "auto-complete" orders check status from api-response. If something wrong - set pending
        switch ($liqpay_status) {
            case 'sandbox':
            case 'success':
                $ac_status = $this->order_status->getStatusByTextId('completed');
                break;
            case 'failure':
                $ac_status = $this->order_status->getStatusByTextId('failed');
                break;
            case 'processing':
                $ac_status = $this->order_status->getStatusByTextId('processing');
                break;
            case 'reversed':
                $ac_status = $this->order_status->getStatusByTextId('reversed');
                break;
            default:
                $ac_status = $this->order_status->getStatusByTextId('pending');
                break;
        }
        return $ac_status;
    }

    public function callback()
    {
        $callback_data = json_decode(base64_decode($this->request->post['data']), true);

        $private_key = $this->config->get('default_liqpay_private_key');

        $data = base64_encode(json_encode($callback_data));
        $signature = base64_encode(sha1($private_key.$data.$private_key, 1));

        if ($signature == $this->request->post['signature']) {
            $order_status_id = $this->getOrderStatus($callback_data['status']);
            /** @var ModelCheckoutOrder $mdl */
            $mdl = $this->load->model('checkout/order');
            $mdl->update($callback_data['order_id'], (int) $order_status_id, 'LiqPay callback changed order status.');
            $mdl->updatePaymentMethodData($callback_data['order_id'], serialize($callback_data));
        }
    }
}