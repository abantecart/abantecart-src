<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
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

class ControllerPagesCheckoutSuccess extends AController
{
    public $data = array();
    public $errors = array();

    public function main()
    {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $order_id = (int)$this->session->data['order_id'];

        if ($order_id && $this->validate($order_id)) {
            //debit transaction
            $this->_debit_transaction($order_id);

            //clear session before redirect
            $this->_clear_order_session();

            //save order_id into session as processed order to allow one redirect
            $this->session->data['processed_order_id'] = $order_id;

            $this->extensions->hk_ProcessData($this);
            //Redirect back to load new page with cleared shopping cart content
            redirect($this->html->getSecureURL('checkout/success'));
        } //when validation failed
        elseif ($order_id) {
            $this->session->data['processed_order_id'] = $order_id;
        } else {
            $order_id = $this->session->data['processed_order_id'];
        }

        //check if payment was processed
        if (!(int)$this->session->data['processed_order_id']) {
            redirect($this->html->getURL('index/home'));
        } elseif (!$order_id && (int)$this->session->data['processed_order_id']) {
            $order_id = (int)$this->session->data['processed_order_id'];
        }
        unset($this->session->data['processed_order_id']);

        $heading_title = $this->language->get('heading_title');
        $this->document->setTitle($heading_title);
        $this->view->assign('heading_title', $heading_title);

        $this->document->resetBreadcrumbs();
        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getHomeURL(),
            'text'      => $this->language->get('text_home'),
            'separator' => false,
        ));

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getSecureURL('checkout/cart'),
            'text'      => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator'),
        ));

        if ($this->customer->isLogged()) {
            $this->document->addBreadcrumb(array(
                'href'      => $this->html->getSecureURL('checkout/shipping'),
                'text'      => $this->language->get('text_shipping'),
                'separator' => $this->language->get('text_separator'),
            ));

            $this->document->addBreadcrumb(array(
                'href'      => $this->html->getSecureURL('checkout/payment'),
                'text'      => $this->language->get('text_payment'),
                'separator' => $this->language->get('text_separator'),
            ));

            $this->document->addBreadcrumb(array(
                'href'      => $this->html->getSecureURL('checkout/confirm'),
                'text'      => $this->language->get('text_confirm'),
                'separator' => $this->language->get('text_separator'),
            ));
        } else {
            $this->document->addBreadcrumb(array(
                'href'      => $this->html->getSecureURL('checkout/guest'),
                'text'      => $this->language->get('text_guest'),
                'separator' => $this->language->get('text_separator'),
            ));

            $this->document->addBreadcrumb(array(
                'href'      => $this->html->getSecureURL('checkout/guest/confirm'),
                'text'      => $this->language->get('text_confirm'),
                'separator' => $this->language->get('text_separator'),
            ));
        }

        $this->document->addBreadcrumb(array(
            'href'      => $this->html->getURL('checkout/success'),
            'text'      => $this->language->get('text_success'),
            'separator' => $this->language->get('text_separator'),
        ));

        $this->loadModel('account/order');
        $order_info = $this->model_account_order->getOrder($order_id);
        if ($order_info) {
            $order_info['order_products'] = $this->model_account_order->getOrderProducts($order_id);
        }

        $order_totals = $this->model_account_order->getOrderTotals($order_id);
        $this->_google_analytics($order_info, $order_totals);

        if ($this->errors) {
            $this->view->assign('text_message', implode('<br>', $this->errors));
        } elseif ($this->session->data['account'] == 'guest') {
            //give link on order page for quest
            $enc = new AEncryption($this->config->get('encryption_key'));
            $order_token = $enc->encrypt($order_id.'::'.$order_info['email']);
            $order_url = $this->html->getSecureURL('account/invoice', '&ot='.$order_token);
            $this->view->assign('text_message',
                sprintf($this->language->get('text_message_guest'),
                    $order_url,
                    $this->html->getURL('content/contact')
                )
            );
        } else {
            $text_message = sprintf($this->language->get('text_message_account'),
                $order_id,
                $this->html->getSecureURL('account/invoice', '&order_id='.$order_id),
                $this->html->getURL('content/contact'));

            $this->view->assign('text_message', $text_message);
        }
        $this->view->assign('button_continue', $this->language->get('button_continue'));
        $this->view->assign('continue', $this->html->getHomeURL());
        $continue = $this->html->buildElement(
            array(
                'type'  => 'button',
                'name'  => 'continue_button',
                'text'  => $this->language->get('button_continue'),
                'style' => 'button',
            ));
        $this->view->assign('continue_button', $continue);
        //clear session anyway
        $this->_clear_order_session();

        if ($this->config->get('embed_mode') == true) {
            //load special headers
            $this->addChild('responses/embed/head', 'head');
            $this->addChild('responses/embed/footer', 'footer');
            $this->processTemplate('embed/common/success.tpl');
        } else {
            $this->processTemplate('common/success.tpl');
        }

        //init controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * Validating order data for different cases
     *
     * @param int $order_id
     *
     * @return bool
     */
    protected function validate($order_id)
    {

        //check is order incomplete
        $this->loadModel('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($order_id);
        //when order exists but still incomplete by some reasons - mark it as failed
        if ((int)$order_info['order_status_id'] == $this->order_status->getStatusByTextId('incomplete')) {
            $new_status_id = $this->order_status->getStatusByTextId('failed');
            $this->model_checkout_order->confirm($order_id, $new_status_id);
            $this->_debit_transaction($order_id);
            $this->messages->saveWarning(
                sprintf($this->language->get('text_title_failed_order_to_admin'), $order_id),
                $this->language->get('text_message_failed_order_to_admin').' '.'#admin#rt=sale/order/details&order_id='.$order_id
            );
            $text_message = $this->language->get('text_message_failed_order');
            $this->errors[] = $text_message;
        }

        //perform additional custom order validation in extensions
        $this->extensions->hk_ValidateData($this);

        if ($this->errors) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $order_id
     *
     * @return bool|null
     */
    protected function _debit_transaction($order_id)
    {
        // in default currency
        $amount = $this->session->data['used_balance'];
        if (!$amount) {
            return null;
        }
        $transaction_data = array(
            'order_id'         => $order_id,
            'amount'           => $amount,
            'transaction_type' => 'order',
            'created_by'       => $this->customer->getId(),
            'description'      => sprintf($this->language->get('text_applied_balance_to_order'),
                $this->currency->format($this->currency->convert($amount,
                    $this->config->get('config_currency'),
                    $this->session->data['currency']),
                    $this->session->data['currency'], 1),
                $order_id),
        );

        try {
            $this->customer->debitTransaction($transaction_data);
        } catch (AException $e) {
            $error = new AError('Error: Debit transaction cannot be applied.'.var_export($transaction_data,
                    true)."\n".$e->getMessage()."\n".$e->getFile());
            $error->toLog()->toMessages();
            return false;
        }

        return true;
    }

    /**
     * Method for purging session data related to order
     */
    protected function _clear_order_session()
    {
        $this->cart->clear();
        $this->customer->clearCustomerCart();
        unset($this->session->data['shipping_method'],
            $this->session->data['shipping_methods'],
            $this->session->data['payment_method'],
            $this->session->data['payment_methods'],
            $this->session->data['guest'],
            $this->session->data['comment'],
            $this->session->data['order_id'],
            $this->session->data['coupon'],
            $this->session->data['used_balance'],
            $this->session->data['used_balance_full']);
    }

    protected function _google_analytics($order_data, $order_totals)
    {

        //google analytics data for js-script.
        //This will be shown in the footer of the page
        $order_tax = $order_total = $order_shipping = 0.0;
        foreach ($order_totals as $i => $total) {
            if ($total['type'] == 'total') {
                $order_total += $total['value'];
            } elseif ($total['type'] == 'tax') {
                $order_tax += $total['value'];
            } elseif ($total['type'] == 'shipping') {
                $order_shipping += $total['value'];
            }
        }

        if (!$order_data['shipping_city']) {
            $addr = array(
                'city'    => $order_data['payment_city'],
                'state'   => $order_data['payment_zone'],
                'country' => $order_data['payment_country'],
            );
        } else {
            $addr = array(
                'city'    => $order_data['shipping_city'],
                'state'   => $order_data['shipping_zone'],
                'country' => $order_data['shipping_country'],
            );
        }

        $ga_data = array_merge(
            array(
                'transaction_id' => (int)$order_data['order_id'],
                'store_name'     => $this->config->get('store_name'),
                'currency_code'  => $order_data['currency'],
                'total'          => $this->currency->format_number($order_total),
                'tax'            => $this->currency->format_number($order_tax),
                'shipping'       => $this->currency->format_number($order_shipping),
            ), $addr);

        if ($order_data['order_products']) {
            $ga_data['items'] = array();
            foreach ($order_data['order_products'] as $product) {
                //try to get option sku for product. If not presents - take main sku from product details
                $options = $this->model_account_order->getOrderOptions((int)$order_data['order_id'], $product['order_product_id']);
                $sku = '';
                foreach ($options as $opt) {
                    if ($opt['sku']) {
                        $sku = $opt['sku'];
                        break;
                    }
                }
                if (!$sku) {
                    $sku = $product['sku'];
                }

                $ga_data['items'][] = array(
                    'id'       => (int)$order_data['order_id'],
                    'name'     => $product['name'],
                    'sku'      => $sku,
                    'price'    => $product['price'],
                    'quantity' => $product['quantity'],
                );
            }
        }

        $this->registry->set('google_analytics_data', $ga_data);
    }

}