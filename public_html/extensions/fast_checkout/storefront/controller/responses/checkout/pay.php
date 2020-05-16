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

/**
 * Class ControllerResponsesCheckoutPay
 *
 * @property ModelExtensionFastCheckout $model_extension_fast_checkout
 */
class ControllerResponsesCheckoutPay extends AController
{
    public $error = [];
    public $data = [];
    protected $action = '';
    protected $allow_guest = false;
    protected $form_rt = '';
    protected $cart_key = '';
    protected $cart_ses = [];

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        //set sign that checkout is fast. See usage in hooks
        $this->session->data['fast-checkout'] = true;

        $this->allow_guest = $this->config->get('config_guest_checkout');

        $this->loadModel('catalog/product');
        $this->loadModel('checkout/extension');
        $this->loadModel('checkout/order');
        $this->loadLanguage('fast_checkout/fast_checkout');
        $this->cart_key = $this->session->data['cart_key'];

        $this->data['cart_key'] = $this->cart_key;

        if (
            !isset($this->session->data['fast_checkout'][$this->cart_key])
            || $this->session->data['fast_checkout'][$this->cart_key]['cart'] !== $this->session->data['cart']
        ) {
            $this->session->data['fast_checkout'][$this->cart_key]['cart'] = $this->session->data['cart'];
            $this->removeNoStockProducts();
            if ( $this->session->data['coupon'] ){
                $this->session->data['fast_checkout'][$this->cart_key]['coupon'] = $this->session->data['coupon'];
            }
        }

        $cart_class_name = get_class($this->cart);
        $this->registry->set('cart', new $cart_class_name($this->registry, $this->session->data['fast_checkout'][$this->cart_key]));
        $this->data['require_telephone'] = $this->config->get('fast_checkout_require_phone_number');
    }

    public function getCartKey()
    {
        return $this->cart_key;
    }

    public function main()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $request = array_merge($this->request->get, $this->request->post);
        //handle coupon
        $this->_handleCoupon($request);
        $get_params = '&cart_key='.$this->cart_key;
        $this->data['onChangeCheckboxBtnUrl'] = $this->html->getSecureURL('r/checkout/pay/changeCheckBox');
        if ($this->config->get('config_checkout_id')) {
            $this->loadLanguage('checkout/confirm');
            $this->loadModel('catalog/content');
            $content_info = $this->model_catalog_content->getContent($this->config->get('config_checkout_id'));
            if ($content_info) {
                $this->data['text_accept_agree'] = $this->language->get('text_accept_agree');
                $this->data['text_accept_agree_href'] = $this->html->getURL('r/content/content/loadInfo', '&content_id='.$this->config->get('config_checkout_id'), true);
                $this->data['text_accept_agree_href_link'] = $content_info['title'];
            } else {
                $this->data['text_accept_agree'] = '';
            }
        } else {
            $this->data['text_accept_agree'] = '';
        }

        //check the case when we have order_id in session.
        //this indicates incomplete earlier order process
        if ($this->session->data['order_id']) {
            $order_id = $this->session->data['order_id'];
            //incomplete order status is OK and we can continue with it, other statuses are considered to be processed.
            $order_details = $this->model_checkout_order->getOrder($order_id);
            if ($order_details['order_status_id'] > 0) {
                //show message about already created order
                $this->data['info'] = sprintf(
                    $this->language->get('fast_checkout_text_info_duplicate_order'),
                    $this->html->getSecureURL('r/checkout/pay/success', '&order_id='.$order_id)
                );
                //release this order from session
                unset(
                    $this->session->data['order_id'],
                    $order_id
                );
                $this->extensions->hk_UpdateData($this, __FUNCTION__);
                $this->view->batchAssign($this->data);
                $this->response->setOutput($this->view->fetch('responses/checkout/main.tpl'));
            }
            $this->data['comment'] = $this->session->data['fast_checkout'][$this->cart_key]['comment'];
        }

        $this->data['address_edit_base_url'] = $this->html->getSecureURL('account/address/update', '&address_id=');
        if (!$this->customer->isLogged() && $this->allow_guest) {
            $this->data['allow_account_creation'] = $this->config->get('fast_checkout_create_account');
        }

        //Do we require payment address based on extension setting
        $this->data['need_payment_address'] = $this->config->get('fast_checkout_require_payment_address');
        $this->data['all_addresses'] = [];
        //Check for settings if need payment address
        if ($this->data['need_payment_address']) {
            $tax_country_id = $tax_zone_id = '';
            if ($this->customer->isLogged()) {
                $this->loadModel('account/address');
                $this->data['all_addresses'] = $this->model_account_address->getAddresses();
                if (!count($this->data['all_addresses'])) {
                    //Something wrong. Account is missing address, direct to regular customer address page.
                    $this->error['message'] = $this->language->get('fast_checkout_error_no_address');
                }
                //was address changed?
                $address_id = $this->customer->getAddressId();
                if ($this->request->get['payment_address_id']) {
                    $address_id = $this->request->get['payment_address_id'];
                } else {
                    if ($this->session->data['fast_checkout'][$this->cart_key]['payment_address_id']) {
                        $address_id = $this->session->data['fast_checkout'][$this->cart_key]['payment_address_id'];
                    }
                }
                foreach ($this->data['all_addresses'] as $adr) {
                    if ($adr['address_id'] == $address_id) {
                        $this->session->data['fast_checkout'][$this->cart_key]['payment_address_id'] = $adr['address_id'];
                        $tax_zone_id = $adr['zone_id'];
                        $tax_country_id = $adr['country_id'];
                        break;
                    }
                }
                if (!$address_id) {
                    $adr = current($this->data['all_addresses']);
                    $this->session->data['fast_checkout'][$this->cart_key]['payment_address_id'] = $adr['address_id'];
                }
            } else {
                if ($this->allow_guest) {
                    //note: guest details in stored into main session to share details with main site
                    if (!$this->session->data['guest']['address_1']) {
                        //shipping required, show address form.
                        $this->action = 'enter';
                        return $this->_address('payment', []);
                    } else {
                        if ($this->session->data['guest']) {
                            $tax_zone_id = $this->session->data['guest']['zone_id'];
                            $tax_country_id = $this->session->data['guest']['country_id'];
                        }
                    }
                }
            }
            //do we need to apply taxed on payment address?
            if (!$this->cart->hasShipping() || $this->config->get('config_tax_customer')) {
                $this->tax->setZone($tax_country_id, $tax_zone_id);
            }
        }

        //check if shipping required.
        if (!$this->cart->hasShipping()) {
            unset($this->session->data['fast_checkout'][$this->cart_key]['shipping_address_id']);
            unset($this->session->data['fast_checkout'][$this->cart_key]['shipping_method']);
            unset($this->session->data['fast_checkout'][$this->cart_key]['shipping_methods']);
            unset($this->session->data['guest']['shipping']);
        } else {
            if ($this->customer->isLogged()) {
                $this->loadModel('account/address');
                if (!$this->data['all_addresses']) {
                    $this->data['all_addresses'] = $this->model_account_address->getAddresses();
                }
                if (!count($this->data['all_addresses'])) {
                    //Something wrong. Account is missing address, direct to regular customer address page.
                    $this->error['message'] = $this->language->get('fast_checkout_error_no_address');
                }
                //was address changed?
                $address_id = $this->customer->getAddressId();
                if ($this->request->get['shipping_address_id']) {
                    $address_id = $this->request->get['shipping_address_id'];
                } else {
                    if ($this->session->data['fast_checkout'][$this->cart_key]['shipping_address_id']) {
                        $address_id = $this->session->data['fast_checkout'][$this->cart_key]['shipping_address_id'];
                    }
                }
                foreach ($this->data['all_addresses'] as $adr) {
                    if ($adr['address_id'] == $address_id) {
                        $this->session->data['fast_checkout'][$this->cart_key]['shipping_address_id'] = $adr['address_id'];
                        $this->session->data['fast_checkout'][$this->cart_key]['tax_zone_id'] = $adr['zone_id'];
                        $this->session->data['fast_checkout'][$this->cart_key]['tax_country_id'] = $adr['country_id'];
                        break;
                    }
                }
                if (!$address_id) {
                    $adr = current($this->data['all_addresses']);
                    $this->session->data['fast_checkout'][$this->cart_key]['shipping_address_id'] = $adr['address_id'];
                    $this->session->data['fast_checkout'][$this->cart_key]['tax_zone_id'] = $adr['zone_id'];
                    $this->session->data['fast_checkout'][$this->cart_key]['tax_country_id'] = $adr['country_id'];
                }
            } else {
                if ($this->allow_guest && !$this->session->data['guest']['shipping']) {
                    //shipping required, show address form for guest
                    $this->action = 'enter';
                    return $this->_address('shipping', []);
                } else {
                    if ($this->allow_guest && $this->session->data['guest']) {
                        $this->session->data['fast_checkout'][$this->cart_key]['tax_zone_id'] = $this->session->data['guest']['zone_id'];
                        $this->session->data['fast_checkout'][$this->cart_key]['tax_country_id'] = $this->session->data['guest']['country_id'];
                    } else {
                        //no guess allowed, need to login
                        $this->action = 'login';
                    }
                }
            }
        }

        //do we need to apply taxed on payment address?
        if ($this->cart->hasShipping() && !$this->config->get('config_tax_customer')) {
            $this->tax->setZone(
                $this->session->data['fast_checkout'][$this->cart_key]['tax_country_id'],
                $this->session->data['fast_checkout'][$this->cart_key]['tax_zone_id']
            );
        }

        //set shipping method
        $this->_select_shipping($this->request->get['shipping_method']);

        //handle balance. Re-apply balance on every request as total can change
        if ($this->session->data['fast_checkout'][$this->cart_key]['used_balance'] && !$request['balance']) {
            $request['balance'] = 'reapply';
        }
        $this->_handleBalance($request);

        //final step to build cart view and totals
        if (!$this->_build_cart_product_details()) {
            $this->_show_error('No items to be purchased are found');
            return null;
        }

        //do we show payment details yet? Show only if shipping selected
        $this->data['show_payment'] = true;
        if ($this->cart->hasShipping() && count($this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'])) {
            if (!$this->session->data['fast_checkout'][$this->cart_key]['shipping_method']) {
                //no shipping selected yet, not ready for payment
                $this->data['show_payment'] = false;
            }
        }

        if ($this->customer->isLogged()) {
            $this->data['loggedin'] = true;
            //get customer name for payment from selected payment address
            foreach ($this->data['all_addresses'] as $adr) {
                if ($adr['address_id'] == $this->session->data['fast_checkout'][$this->cart_key]['payment_address_id']) {
                    $this->data['customer_name'] = $adr['firstname'].' '.$adr['lastname'];
                    break;
                }
            }
            //no name found use names from account
            if (!$this->data['customer_name']) {
                $this->data['customer_name'] = $this->customer->getFirstName().' '.$this->customer->getLastName();
            }

            $this->data['logout_url'] = $this->html->getSecureURL('r/checkout/pay/logout', $get_params);
        }

        if ($this->data['show_payment'] == true) {
            //Order must be created before payment form rendering!
            if( !$this->session->data['order_id'] ) {
                $in_data = array_merge((array)$this->session->data, $this->session->data['fast_checkout'][$this->cart_key]);
                $this->updateOrCreateOrder($in_data, $request);
            }
            $this->_build_payment_view($request, $get_params);
        }

        $this->addLoginForm($request, $get_params);

        $this->data['step'] = !$this->data['step'] ? 'payment' : $this->data['step'];

        //last step with payment form
        $this->data['action'] = $this->action;
        $this->data['error'] = '';
        if ($this->session->data['guest']) {
            $this->data['edit_address_url'] = $this->html->getSecureURL('r/checkout/pay/edit_address', $get_params);
        }
        $this->data['main_url'] = $this->html->getSecureURL('r/checkout/pay/main', $get_params);
        if (isset($this->error['message'])) {
            $this->data['error'] = $this->error['message'];
        }

        //pass cart session to template view
        $this->data['csession'] = $this->session->data['fast_checkout'][$this->cart_key];

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $in_data = array_merge((array)$this->session->data, $this->session->data['fast_checkout'][$this->cart_key]);
        if (!$in_data['guest'] && !$this->customer->getId()) {
            $this->address();
            return;
        }

        $this->updateOrCreateOrder($in_data, $request);

        $this->view->batchAssign($this->data);
        $this->response->setOutput($this->view->fetch('responses/checkout/main.tpl'));
        return null;
    }

    public function updateOrderData(){
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $in_data = array_merge(
            (array)$this->session->data,
            $this->session->data['fast_checkout'][$this->cart_key]
        );
        $request = array_merge($this->request->get, $this->request->post);
        $this->updateOrCreateOrder($in_data, $request);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        return null;
    }

    protected function updateOrCreateOrder($in_data, $request)
    {
        if (!$this->session->data['order_id']) {
            //create order and save details
            $order = new AOrder($this->registry);
        } else {
            $order = new AOrder($this->registry, $this->session->data['order_id']);
        }

        $order->buildOrderData($in_data);
        $order_id = $order->saveOrder();
        $this->loadModel('extension/fast_checkout');
        if ($order_id) {
            if ($request['cc_telephone'] || $request['telephone']) {
                $telephone = $request['cc_telephone'] ? $request['cc_telephone'] : $request['telephone'];
                $this->model_extension_fast_checkout->updateOrderDetails(
                    $order_id,
                    array(
                        'telephone' => $telephone,
                    )
                );
                if( !$this->customer->isLogged() ){
                    $this->session->data['guest']['telephone'] = $telephone;
                }
            }
            if ($request['comment']) {
                $this->model_extension_fast_checkout->updateOrderDetails(
                    $order_id,
                    array(
                        'comment' => $request['comment'],
                    )
                );
                $this->session->data['fast_checkout'][$this->cart_key]['comment'] = $request['comment'];
            }

            $this->session->data['order_id'] = $order_id;
        } else {
            $this->_to_log(sprintf($this->language->get('fast_checkout_error_unexpected_log'), var_export($in_data, true)));
            $this->error['message'] = $this->language->get('fast_checkout_error_unexpected');
            unset($this->session->data['order_id']);
            return $this->main();
        }
    }

    protected function addLoginForm($request, $get_params)
    {
        if (!$this->customer->isLogged()) {
            $form = new AForm();
            $form->setForm(['form_name' => 'LoginFrm']);
            $this->data['login_form']['form_open'] = $form->getFieldHtml(
                [
                    'type'   => 'form',
                    'name'   => 'LoginFrm',
                    'method' => 'post',
                    'action' => $this->html->getSecureURL('r/checkout/pay/login', $get_params),
                    'csrf'   => true,
                ]
            );
            $this->data['customer_name'] = $request['cc_owner'];
            if ($this->session->data['guest']) {
                $this->data['customer_name'] =
                    $this->session->data['guest']['firstname'].' '.$this->session->data['guest']['lastname'];
            }
            if ($request['cc_email']) {
                $this->data['customer_email'] = $request['cc_email'];
            } else {
                $this->data['customer_email'] = $this->session->data['guest']['email'];
            }

            if ($request['telephone']) {
                $this->data['customer_telephone'] = $request['telephone'];
            } else {
                $this->data['customer_telephone'] = $this->session->data['guest']['telephone'];
            }

            $this->data['reset_url'] = $this->html->getSecureURL('account/login');

        }
    }

    protected function _build_payment_view($request, $get_params)
    {
        $this->data['payment_methods'] = $this->_get_payment_methods();
        //ignore unsupported paypal express payment
        if (isset($this->data['payment_methods']['default_pp_express'])) {
            unset($this->data['payment_methods']['default_pp_express']);
        }
        $this->data['payment_method'] = $request['payment_method'];
        $selected_payment = $this->session->data['fast_checkout'][$this->cart_key]['payment_method'];
        //case when switches shipping method into method with accepted payments list
        if($selected_payment && !isset($this->data['payment_methods'][$selected_payment['id']])){
            unset($this->session->data['fast_checkout'][$this->cart_key]['payment_method']);
            $selected_payment = array();
        }

        if (!$this->data['payment_method'] && count($this->data['payment_methods'])==1 ) {
            $this->data['payment_method'] = key($this->data['payment_methods']);
        }else if (!$this->data['payment_method'] && !$selected_payment) {
            //check autoselect payment
            foreach ($this->data['payment_methods'] as $id => $payment) {
                $psettings = $this->model_checkout_extension->getSettings($id);
                if ($psettings[$id.'_autoselect']) {
                    $this->data['payment_method'] = $id;
                }
            }
        } elseif (!$this->data['payment_method'] && $selected_payment) {
            $this->data['payment_method'] = $selected_payment['id'];
        }

        //show selected payment form
        if ($this->data['payment_method']) {
            $this->session->data['fast_checkout'][$this->cart_key]['payment_method'] = [
                'id'    => $this->data['payment_method'],
                'title' => $this->data['payment_method'] == 'no_payment_required'
                            ? $this->language->get('no_payment_required')
                            : $this->data['payment_methods'][$this->data['payment_method']]['title'],
            ];

            if ($this->data['payment_method'] != 'no_payment_required') {
                $rt = 'responses/extension/'.$this->data['payment_method'];
            } else {
                $rt = 'responses/checkout/no_payment';
            }
            $dd = new ADispatcher($rt);
            //style buttons
            $paymentHTML = preg_replace(
                "/<a id=\"back\".*?<\/a>/si",
                '',
                $dd->dispatchGetOutput()
            );
            $paymentHTML = preg_replace(
                '/btn-orange/',
                'btn-primary btn-lg btn-block',
                $paymentHTML
            );

            $paymentHTML = preg_replace(
                '/<a class="btn btn-default pull-left".*?<\/a>/si',
                '',
                $paymentHTML
            );

            $paymentHTML = preg_replace(
                '/<a .*? class="btn btn-default".*?<\/a>/si',
                '',
                $paymentHTML
            );
            $this->view->assign('payment_form', $paymentHTML);
        }

        //build a form
        $payment_select_action = $this->html->getSecureURL('r/checkout/pay/main', $get_params);
        $form_action = $this->html->getSecureURL('r/checkout/pay/confirm', $get_params);
        $form = new AForm();
        $form->setForm(['form_name' => 'PayFrm']);
        $this->data['pay_form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'PayFrm',
                'action' => $form_action,
            ]
        );

        //check if any payment is available for address or show balance if available.
        $this->data['payment_select_action'] = $payment_select_action;
        $this->data['payment_available'] = ($this->data['payment_methods'] || $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full']) ? true : false;
        if ($this->data['balance_enough'] !== true && $this->data['payment_available'] !== true) {
            $this->error['message'] = $this->data['payment_available'];
            $this->data['payment_available'] = false;
        } else {
            if ($this->data['balance_enough'] === true && $this->data['payment_available'] !== true) {
                //we only have balance
                $this->data['payment_available'] = false;
            }
        }

        //check if logged in
        if ($this->customer->isLogged()) {
            //customer details
            $this->data['customer_email'] = $this->customer->getEmail();
            if( $this->session->data['order_id'] ){
                $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
                $this->session->data['fast_checkout'][$this->cart_key]['telephone'] =
                                $this->data['customer_telephone'] =
                                $order_info['telephone'];
            }else{
                $this->data['customer_telephone'] = $this->customer->getTelephone();
            }

            //balance handling
            $balance_def_currency = $this->customer->getBalance();
            $balance = $this->currency->convert(
                $balance_def_currency,
                $this->config->get('config_currency'),
                $this->session->data['currency']
            );
            //is balance enough to cover all order amount
            $this->data['balance_enough'] = false;
            if ($balance > 0 && $balance >= $this->data['total']) {
                $this->data['balance_enough'] = true;
            }
            $this->data['balance'] = $balance;
            $this->data['balance_value'] = $this->currency->format($balance, $this->session->data['currency'], 1);
            $this->data['used_balance_full'] = $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full'];
            $this->data['used_balance'] = $this->session->data['fast_checkout'][$this->cart_key]['used_balance'];

            if($this->data['used_balance_full'] ){
                unset($this->data['payment_methods'],$this->data['payment_method'] );
            }
            $this->data['balance_remains'] = $this->data['balance_value'];
            if ((float)$this->data['used_balance'] > 0) {
                $this->data['balance_remains'] = $this->currency->format($balance_def_currency - (float)$this->data['used_balance']);
            }
        }else{
            $this->data['customer_telephone'] = $this->session->data['guest']['telephone'];
        }

        if ($this->data['payment_available'] === true) {
            //Check if fraud extension enabled and any payment is blocked based on the score
            $this->extensions->hk_ProcessData($this, 'fast_checkout_fraud_payment_checkpoint', $this->session->data['fast_checkout'][$this->cart_key]);
        }
    }

    protected function _build_cart_product_details()
    {
        $qty = 0;
        $resource = new AResource('image');
        $products = array();
        foreach ($this->cart->getProducts() as $result) {
            $option_data = [];

            foreach ($result['option'] as $option) {
                $value = $option['value'];
                // hide binary value for checkbox
                if ($option['element_type'] == 'C' && in_array($value, [0, 1], true)) {
                    $value = '';
                }
                $title = '';
                // strip long textarea value
                if ($option['element_type'] == 'T') {
                    $title = strip_tags($value);
                    $title = str_replace('\r\n', "\n", $title);

                    $value = str_replace('\r\n', "\n", $value);
                    if (mb_strlen($value) > 64) {
                        $value = mb_substr($value, 0, 64).'...';
                    }
                }

                $option_data[] = [
                    'name'  => $option['name'],
                    'value' => $value,
                    'title' => $title,
                ];
            }

            $qty += $result['quantity'];

            //get main image
            $thumbnail = $resource->getMainImage(
                'products',
                $result['product_id'],
                $this->config->get('config_image_grid_width'),
                $this->config->get('config_image_grid_height')
            );

            $products[] = [
                'key'       => $result['key'],
                'name'      => $result['name'],
                'thumbnail' => $thumbnail,
                'option'    => $option_data,
                'quantity'  => $result['quantity'],
                'stock'     => $result['stock'],
                'price'     => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'],
                    $this->config->get('config_tax'))),
                'href'      => $this->html->getSEOURL('product/product', '&product_id='.$result['product_id'], true),
            ];
        }
        $this->data['products'] = $products;

        $display_totals = $this->cart->buildTotalDisplay(true);

        $this->data['totals'] = $display_totals['total_data'];
        $this->data['total'] = $display_totals['total'];
        $this->data['total_string'] = $this->currency->format($display_totals['total']);
        if ($this->data['totals']) {
            return true;
        } else {
            return false;
        }

    }

    public function confirm()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->action = 'confirm';
        //Validate quick cart instance
        if (!$this->cart_key || !$this->session->data['fast_checkout'][$this->cart_key]) {
            return $this->_show_error();
        }

        //recalculate totals
        $this->cart->buildTotalDisplay(true);

        $request = $this->request->post;
        //validate order details
        if (!$this->_validate_order_details($request)) {
            $this->action = "payment";
            return $this->main();
        }

        //validate payment details
        if ($this->customer->isLogged() && $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full']) {
            //validate account balance
            $balance = $this->currency->convert(
                $this->customer->getBalance(),
                $this->config->get('config_currency'),
                $this->session->data['currency']
            );

            //balance has to cover full order amount
            if ($balance < $this->cart->getFinalTotal()) {
                $this->error['message'] = $this->language->get('fast_checkout_error_low_balance');
                return $this->main();
            }
            $this->session->data['fast_checkout'][$this->cart_key]['payment_method'] = [
                'id'    => 'no_payment_required',
                'title' => $this->language->get('no_payment_required'),
            ];
            $in_data = array_merge((array)$this->session->data, $this->session->data['fast_checkout'][$this->cart_key]);

            $this->updateOrCreateOrder($in_data, $request);

        }

        if (!$this->customer->getId()) {
            $guest_info =& $this->session->data['guest'];
            //add email into guest session data
            $guest_info['email'] = $request['cc_email'];
            if ($request['cc_telephone']) {
                $guest_info['telephone'] = $request['cc_telephone'];
            }
            //check case when order without any addresses
            if (!$guest_info['firstname'] && !$guest_info['lastname'] && $request['cc_owner']) {
                list($guest_info['firstname'], $guest_info['lastname']) = explode(' ', trim($request['cc_owner']));
            }
        }

        $order_id = $this->session->data['order_id'];
        //Process payment
        if ($this->session->data['fast_checkout'][$this->cart_key]['used_balance_full']) {
            $this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
            $this->load->library('json');
            $data['url'] = $this->html->getSecureURL('checkout/success', '&order_id='.$order_id);
            $this->response->setOutput(AJson::encode($data));
            return;
        }

        $this->action = 'confirm';
        $this->data['step'] = 'confirm';

        //payment method
        $payment_method = $this->request->get_or_post('payment_method');
        if ($payment_method) {
            //get payment ext key from setting ID
            $results = $this->model_checkout_extension->getExtensions('payment');
            foreach ($results as $result) {
                if ($result['key'] == $payment_method) {
                    $this->data['payment_method_id'] = $payment_method;
                    break;
                }
            }
        }
        $this->main();
        return null;
    }

    public function success()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $order_id = $this->request->get['order_id'];
        if (!$order_id || !is_numeric($order_id)) {
            $this->error['message'] = $this->language->get('fast_checkout_error_unexpected');
            return $this->main();
        }

        $this->loadModel('checkout/order');
        $order_details = $this->model_checkout_order->getOrder($order_id);
        if ($order_details && $order_details['order_status_id'] > 0) {
            $this->_process_success($order_id, $order_details, $this->cart_key);
        } else {
            $this->error['message'] = $this->language->get('fast_checkout_error_unexpected');
            return $this->main();
        }
    }

    protected function _process_success($order_id, $order_data, $cart_key, $request = [])
    {

        if (!$order_id || empty($order_data)) {
            $this->error['message'] = $this->language->get('fast_checkout_error_unexpected');
            return $this->main();
        }

        //NOTE: We get here only if payment is a success, build details for success page
        if ($this->customer->isLogged()) {
            //for customer show button to order history.
            $this->data['button_order_details'] = $this->html->buildElement(
                [
                    'type' => 'button',
                    'text' => $this->language->get('button_history'),
                    'href' => $this->html->getSecureURL('account/order_details', '&order_id='.$order_id),
                    'icon' => 'fa fa-briefcase fa-fw',
                ]
            );

            //if download build download link for one download or link to order details page
            if ($this->config->get('config_download')) {
                $download = $this->_get_download($order_id);
                if ($download['count'] > 0) {
                    if ($download['count'] == 1) {
                        $this->data['download_url'] = $download['download_url'];
                    } else {
                        $this->data['order_details_url'] = $download['download_url'];
                    }
                }
            }

        } else {
            //for guest build tokenized access to download and order details.
            $this->loadModel('extension/fast_checkout');
            $sec_token = genToken(32);
            $this->model_extension_fast_checkout->saveGuestToken($order_id, $sec_token);
            $enc = new AEncryption($this->config->get('encryption_key'));
            $order_token = $enc->encrypt($order_id.'::'.$order_data['email'].'::'.$sec_token);

            $this->data['button_order_details'] = $this->html->buildElement(
                [
                    'type' => 'button',
                    'text' => $this->language->get('button_history'),
                    'href' => $this->html->getSecureURL('account/order_details', '&ot='.$order_token),
                    'icon' => 'fa fa-briefcase fa-fw',
                ]
            );
            if ($this->config->get('fast_checkout_create_account') && $this->session->data['fast_checkout'][$this->cart_key]['additional']['create_account'] == 'true') {
                $this->_save_customer_account($order_data);
            }

            //if download build download link for one download or downloads list
            if ($this->config->get('config_download')) {
                $download = $this->_get_download($order_id, $order_token);
                if ($download['count'] > 0) {
                    //if downloads exists but unavailable - give order details page
                    if (!$download['download_url']) {
                        $download['download_url'] = $this->html->getSecureURL('account/order_details',
                            ($order_token ? '&ot='.$order_token : ''));
                    }
                    if ($download['count'] == 1) {
                        $this->data['download_url'] = $download['download_url'];
                    } else {
                        $this->data['order_details_url'] = $download['download_url'];
                    }
                    //email download link for guest.
                    $this->model_extension_fast_checkout->emailDownloads($order_data, $download);
                }
            }
        }

        $this->loadModel('account/order');
        $order_details = $this->model_account_order->getOrder($order_id);

        //build custom order message based on the status
        if ($this->order_status->getStatusByTextId('completed') == $order_details['order_status_id']) {
            $this->data['order_finished_message'] = $this->language->get('fast_checkout_order_success_message');
        } else {
            $this->data['order_finished_message'] = $this->language->get('fast_checkout_order_processing_message');
        }

        $this->_save_google_analytics($order_data);

        $this->_clear_data();
        $this->data['step'] = 'process';

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->data['order_id'] = $order_id;

        $this->view->batchAssign($this->data);
        $this->response->setOutput($this->view->fetch('responses/checkout/success.tpl'));
    }

    protected function _process_account_balance($amount, $order_id)
    {
        if ($amount) {
            $transaction_data = [
                'order_id'         => (int)$order_id,
                'amount'           => $amount,
                'transaction_type' => 'order',
                'created_by'       => $this->customer->getId(),
                'description'      => sprintf(
                    $this->language->get('text_applied_balance_to_order'),
                    $this->currency->format_number($amount, $this->session->data['currency'], 1),
                    (int)$order_id),
            ];
            try {
                $this->customer->debitTransaction($transaction_data);
                //validate no error in debitTransaction
                return true;
            } catch (AException $e) {
                //if something goes wrong mark order as failed
                $this->_to_log($this->language->get('fast_checkout_error_balance_apply').' #'.$order_id);
                $this->model_checkout_order->update($order_id, $this->order_status->getStatusByTextId('failed'),
                    $this->language->get('fast_checkout_error_balance_apply'));
            }
        }
        return false;
    }

    protected function _save_customer_account($order_data)
    {
        $customer_data = [
            'status'        => 1,
            'loginname'     => $order_data['email'],
            'password'      => randomWord(7),
            'firstname'     => $order_data['firstname'],
            'lastname'      => $order_data['lastname'],
            'email'         => $order_data['email'],
            'telephone'     => $order_data['telephone'],
            'fax'           => $order_data['fax'],
            'company'       => ($order_data['shipping_company'] ? $order_data['shipping_company'] : $order_data['payment_company']),
            'address_1'     => ($order_data['shipping_address_1'] ? $order_data['shipping_address_1'] : $order_data['payment_address_1']),
            'address_2'     => ($order_data['shipping_address_2'] ? $order_data['shipping_address_2'] : $order_data['payment_address_2']),
            'city'          => ($order_data['shipping_city'] ? $order_data['shipping_city'] : $order_data['payment_city']),
            'postcode'      => ($order_data['shipping_postcode'] ? $order_data['shipping_postcode'] : $order_data['payment_postcode']),
            'country_id'    => ($order_data['shipping_country_id'] ? $order_data['shipping_country_id'] : $order_data['payment_country_id']),
            'zone_id'       => ($order_data['shipping_zone_id'] ? $order_data['shipping_zone_id'] : $order_data['payment_zone_id']),
            'language_code' => $this->language->getLanguageCode(),
        ];

        $customer_id = 0;
        $new_customer = false;
        //check is customer already exists
        $this->loadModel('account/customer');
        $customer_info = $this->model_account_customer->getCustomerByEmail($order_data['email']);
        if (!$customer_info) {
            $customer_id = (int)$this->model_extension_fast_checkout->addCustomer($customer_data);
            if ($customer_id) {
                $new_customer = true;
                $this->data['text_account_created'] = $this->language->get('fast_checkout_text_account_created');
                //if customer gave two addresses - save payment address as additional
                if ($order_data['shipping_address_1'] && $order_data['payment_address_1']
                    && ($order_data['shipping_address_1'] != $order_data['payment_address_1'])) {

                    $address = [
                        'customer_id' => $customer_id,
                        'firstname'   => $order_data['firstname'],
                        'lastname'    => $order_data['lastname'],
                        'company'     => $order_data['payment_company'],
                        'address_1'   => $order_data['payment_address_1'],
                        'address_2'   => $order_data['payment_address_2'],
                        'city'        => $order_data['payment_city'],
                        'postcode'    => $order_data['payment_postcode'],
                        'country_id'  => $order_data['payment_country_id'],
                        'zone_id'     => $order_data['payment_zone_id'],
                    ];

                    $this->model_extension_fast_checkout->addAddress($address);
                }
            }
        } else {
            if ($customer_info['customer_id']) {
                $customer_id = (int)$customer_info['customer_id'];
            }
        }
        //update order_details
        if ($customer_id) {
            $this->loadModel('extension/fast_checkout');
            $this->model_extension_fast_checkout->updateOrderCustomer($order_data['order_id'], $customer_id);
            if ($new_customer == true) {
                $this->model_extension_fast_checkout->sendEmailActivation($customer_data);
            }
        }
    }

    protected function _get_download($order_id, $order_token = '')
    {
        $download_url = '';
        $customer_id = (int)$this->customer->getId();

        $this->loadModel('extension/fast_checkout');
        $order_downloads = $this->model_extension_fast_checkout->getCustomerOrderDownloads($order_id, $customer_id);

        if (!$order_downloads) {
            return [];
        }

        $suffix = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        //build order token for guests
        foreach ($order_downloads as &$download_info) {
            $text_status = $this->download->getTextStatusForOrderDownload($download_info);
            $size = filesize(DIR_RESOURCE.$download_info['filename']);
            $i = 0;
            while (($size / 1024) > 1) {
                $size = $size / 1024;
                $i++;
            }
            //if download available
            if (!$text_status) {
                $download_info['href'] = $this->html->getSecureURL(
                    'account/order_details/startdownload',
                    '&order_download_id='.$download_info['order_download_id']
                    .($order_token ? '&ot='.$order_token : '')
                );
                $download_info['text_status'] = '';
                $download_info['size'] = round(substr($size, 0, strpos($size, '.') + 4), 2).$suffix[$i];
            } else {
                $download_info['text_status'] = $text_status;
                $download_info['size'] = '';
                //for pending downloads
                $this->data['text_order_download_pending'] =
                    $this->language->get('fast_checkout_text_order_download_pending');
            }

        }

        $this->data['order_downloads'] = $order_downloads;
        $downloads_count = sizeof($order_downloads);

        if ($downloads_count == 1) {
            $order_downloads = array_values($order_downloads);
            $download_info = $order_downloads[0];
            //if download available
            if (!$download_info['text_status']) {
                $download_url = $download_info['href'];
            } else {
                $download_url = $this->html->getSecureURL('account/order_details', '&ot='.$order_token);
            }
        } else {
            if ($downloads_count > 1) {
                //if some of them is available - show download button
                foreach ($order_downloads as $d) {
                    if (!$d['text_status']) {
                        if (!$customer_id) {
                            //guest download
                            $download_url = $this->html->getSecureURL('account/order_details', '&ot='.$order_token);
                        } else {
                            $download_url = $this->html->getSecureURL('account/download');
                        }
                        break;
                    }
                }
            }
        }

        return ['count' => $downloads_count, 'download_url' => $download_url];
    }

    protected function _save_google_analytics($order_data)
    {
        // google analytics data for js-script in success.tpl
        $order_tax = $order_total = $order_shipping = 0.0;
        foreach ((array)$order_data['totals'] as $total) {
            if ($total['total_type'] == 'total') {
                $order_total += $total['value'];
            } elseif ($total['total_type'] == 'tax') {
                $order_tax += $total['value'];
            } elseif ($total['total_type'] == 'shipping') {
                $order_shipping += $total['value'];
            }
        }

        $addr = [];
        if (!$order_data['shipping_city']) {
            $addr = [
                'city'    => $order_data['payment_city'],
                'state'   => $order_data['payment_zone'],
                'country' => $order_data['payment_country'],
            ];
        } else {
            $addr = [
                'city'    => $order_data['shipping_city'],
                'state'   => $order_data['shipping_zone'],
                'country' => $order_data['shipping_country'],
            ];
        }

        $this->registry->set('google_analytics_data',
            array_merge(
                [
                    'transaction_id' => (int)$order_data['order_id'],
                    'store_name'     => $this->config->get('store_name'),
                    'currency_code'  => $order_data['currency'],
                    'total'          => $this->currency->format_number($order_total),
                    'tax'            => $this->currency->format_number($order_tax),
                    'shipping'       => $this->currency->format_number($order_shipping),
                ], $addr)
        );
    }

    protected function _clear_data()
    {
        unset($this->session->data['fast_checkout'][$this->cart_key]);
        unset($this->session->data['used_balance']);
        unset($this->session->data['cart']);
        unset($this->session->data['cart_key']);
        unset(
            $this->session->data['shipping_method'],
            $this->session->data['shipping_methods'],
            $this->session->data['payment_method'],
            $this->session->data['payment_methods'],
            $this->session->data['order_id']
        );
        $this->cart->clear();
    }

    public function login()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //Validate login
        $loginname = $this->request->post['loginname'];
        $password = $this->request->post['password'];
        if (!isset($loginname) || !isset($password) || !$this->_validate_login($loginname, $password)) {
            $this->action = 'login';
            $this->loadLanguage('account/login');
            $this->error['message'] = $this->language->get('error_login');
            if ($this->request->get['type']) {
                return $this->_address($this->request->get['type'], $this->request->post);
            }
        } else {
            $this->action = 'payment';
        }

        return $this->main();
    }

    public function logout()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if ($this->customer->isLogged() || $this->customer->isUnauthCustomer()) {
            $this->customer->logout();
            $this->cart->clear();

            unset(
                $this->session->data['shipping_address_id'],
                $this->session->data['shipping_method'],
                $this->session->data['shipping_methods'],
                $this->session->data['payment_address_id'],
                $this->session->data['payment_method'],
                $this->session->data['payment_methods'],
                $this->session->data['comment'],
                $this->session->data['order_id'],
                $this->session->data['coupon'],
                $this->session->data['merchant']
            );

            if ($this->config->get('config_tax_store')) {
                $country_id = $this->config->get('config_country_id');
                $zone_id = $this->config->get('config_zone_id');
            } else {
                $country_id = $zone_id = 0;
            }
            $this->tax->setZone($country_id, $zone_id);
        }

        $this->action = 'payment';
        $this->main();

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    public function edit_address()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        if ($this->request->get['type'] == 'payment') {
            $data = $this->session->data['guest'];
            $this->data['customer_email'] = $this->session->data['guest']['email'];
        } else {
            $data = $this->session->data['guest']['shipping'];
        }
        return $this->_address($this->request->get['type'], $data);
    }

    public function address()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //Validate address entries
        $post = $this->request->post;
        if ( $this->request->is_POST() && $this->action != 'login' ) {
            $this->loadModel('account/address');
            $this->error = $this->model_account_address->validateAddressData($post);
            $this->_validateEmailTelephone($post);
            if ($this->error && $this->error['warning']) {
                //we have errors
                $this->error['message'] = $this->error['warning'];
                return $this->_address($this->request->get['type'], $post);
            }
        } else {
            return $this->_address($this->request->get['type'], $post);
        }

        //all good save guest address
        //is this first or payment address?
        if ($this->request->get['type'] == 'payment' || !$this->session->data['guest']) {
            //do not clear if we have guest data and edit
            if (!$this->session->data['guest']) {
                $this->session->data['guest'] = [];
            }

            $this->session->data['guest']['firstname'] = $post['firstname'];
            $this->session->data['guest']['lastname'] = $post['lastname'];
            $this->session->data['guest']['email'] = $post['cc_email'];
            $this->session->data['guest']['company'] = '';
            $this->session->data['guest']['telephone'] = $post['telephone'];
            $this->session->data['guest']['address_1'] = $post['address_1'];
            $this->session->data['guest']['address_2'] = $post['address_2'];
            $this->session->data['guest']['zone_id'] = $post['zone_id'];
            $this->session->data['guest']['postcode'] = $post['postcode'];
            $this->session->data['guest']['city'] = $post['city'];
            $this->session->data['guest']['country_id'] = $post['country_id'];

            $this->tax->setZone($post['country_id'], $post['zone_id']);

            $this->loadModel('localisation/country');
            $country_info = $this->model_localisation_country->getCountry($post['country_id']);
            if ($country_info) {
                $this->session->data['guest']['country'] = $country_info['name'];
                $this->session->data['guest']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['guest']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['guest']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['guest']['country'] = '';
                $this->session->data['guest']['iso_code_2'] = '';
                $this->session->data['guest']['iso_code_3'] = '';
                $this->session->data['guest']['address_format'] = '';
            }

            $this->loadModel('localisation/zone');
            $zone_info = $this->model_localisation_zone->getZone($post['zone_id']);
            if ($zone_info) {
                $this->session->data['guest']['zone'] = $zone_info['name'];
                $this->session->data['guest']['zone_code'] = $zone_info['code'];
            } else {
                $this->session->data['guest']['zone'] = '';
                $this->session->data['guest']['zone_code'] = '';
            }

        }

        if ($this->request->get['type'] == 'shipping' || isset($post['same_as_shipping'])) {
            $this->session->data['guest']['email'] = $post['cc_email'];
            $this->session->data['guest']['telephone'] = $post['telephone'];
            $this->session->data['guest']['shipping']['company'] = '';
            $this->session->data['guest']['shipping']['firstname'] = $post['firstname'];
            $this->session->data['guest']['shipping']['lastname'] = $post['lastname'];
            $this->session->data['guest']['shipping']['address_1'] = $post['address_1'];
            $this->session->data['guest']['shipping']['address_2'] = $post['address_2'];
            $this->session->data['guest']['shipping']['zone_id'] = $post['zone_id'];
            $this->session->data['guest']['shipping']['postcode'] = $post['postcode'];
            $this->session->data['guest']['shipping']['city'] = $post['city'];
            $this->session->data['guest']['shipping']['country_id'] = $post['country_id'];

            $this->loadModel('localisation/country');
            $country_info = $this->model_localisation_country->getCountry($post['country_id']);
            if ($country_info) {
                $this->session->data['guest']['shipping']['country'] = $country_info['name'];
                $this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
                $this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
                $this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
            } else {
                $this->session->data['guest']['shipping']['country'] = '';
                $this->session->data['guest']['shipping']['iso_code_2'] = '';
                $this->session->data['guest']['shipping']['iso_code_3'] = '';
                $this->session->data['guest']['shipping']['address_format'] = '';
            }

            $this->loadModel('localisation/zone');
            $zone_info = $this->model_localisation_zone->getZone($post['zone_id']);
            if ($zone_info) {
                $this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
                $this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
            } else {
                $this->session->data['guest']['shipping']['zone'] = '';
                $this->session->data['guest']['shipping']['zone_code'] = '';
            }
        }
        $this->action = 'payment';
        return $this->main();
    }

    protected function _address($type, $data)
    {
        $params = "&cart_key=".$this->cart_key.'&type='.$type;
        $this->loadLanguage('account/address');
        $form = new AForm();
        $form->setForm(['form_name' => 'Address2Frm']);
        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'Address2Frm',
                'action' => $this->html->getSecureURL('r/checkout/pay/address', $params),
            ]);

        $this->data['form']['firstname'] = $form->getFieldHtml([
            'type'     => 'input',
            'name'     => 'firstname',
            'value'    => $data['firstname'],
            'required' => true,
        ]);
        $this->data['form']['lastname'] = $form->getFieldHtml([
            'type'     => 'input',
            'name'     => 'lastname',
            'value'    => $data['lastname'],
            'required' => true,
        ]);
        $this->data['form']['address_1'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'address_1',
                'value'    => $data['address_1'],
                'required' => true,
            ]);
        $this->data['form']['address_2'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'address_2',
                'value'    => $data['address_2'],
                'required' => false,
            ]);
        $this->data['form']['city'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'city',
                'value'    => $data['city'],
                'required' => true,
            ]);

        $this->data['form']['zone'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'zone_id',
                'required' => true,
            ]);

        $this->data['form']['postcode'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'postcode',
                'value'    => $data['postcode'],
                'required' => true,
            ]);
        $this->loadModel('localisation/country');
        $countries = $this->model_localisation_country->getCountries();
        $options = ['false' => $this->language->get('text_select')];
        foreach ($countries as $item) {
            $options[$item['country_id']] = $item['name'];
        }
        $this->data['form']['country_id'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'country_id',
                'options'  => $options,
                'value'    => (isset($data['country_id']) ? $data['country_id'] : $this->config->get('config_country_id')),
                'required' => true,
            ]);
        $this->data['form']['continue'] = $form->getFieldHtml(
            [
                'type' => 'submit',
                'name' => $this->language->get('button_continue'),
            ]);

        $this->data['zone_id'] = isset($data['zone_id']) ? $data['zone_id'] : 'false';

        $this->loadModel('localisation/country');
        $this->data['countries'] = $this->model_localisation_country->getCountries();

        if ($data['cc_email']) {
            $this->data['customer_email'] = $data['cc_email'];
        } else {
            $this->data['customer_email'] = $this->session->data['guest']['email'];
        }

        if ($data['telephone']) {
            $this->data['customer_telephone'] = $data['telephone'];
        } else {
            $this->data['customer_telephone'] = $this->session->data['guest']['telephone'];
        }

        //login form portion
        $this->data['reset_url'] = $this->html->getSecureURL('account/login');
        $form->setForm(['form_name' => 'LoginFrm']);
        $this->data['login_form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'LoginFrm',
                'action' => $this->html->getSecureURL('r/checkout/pay/login', $params),
            ]
        );

        $this->data['action'] = $this->action;
        $this->data['step'] = 'address';
        $this->data['type'] = $type;
        $this->data['error'] = '';

        if (isset($this->error['message'])) {
            $this->data['error'] = $this->error['message'];
            $this->data['errors'] = $this->error;
        }

        //build cart view and totals
        if (!$this->_build_cart_product_details()) {
            $this->_show_error('No items to be purchased are found');
            return null;
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->view->batchAssign($this->data);
        $this->response->setOutput($this->view->fetch('responses/checkout/main.tpl'));
        return null;
    }

    protected function _validate_login($loginname, $password)
    {
        if (!$this->customer->login($loginname, $password)) {
            return false;
        } else {
            unset($this->session->data['guest']);
            $this->loadModel('account/address');
            $address = $this->model_account_address->getAddress($this->customer->getAddressId());
            $this->session->data['country_id'] = $address['country_id'];
            $this->session->data['zone_id'] = $address['zone_id'];
            return true;
        }
    }

    protected function _get_payment_methods()
    {
        $this->loadModel('checkout/extension');
        $this->loadModel('account/address');
        $method_data = [];

        // If total amount of order is zero - do redirect on confirmation page
        $total = $this->cart->buildTotalDisplay(true);

        $results = $this->model_checkout_extension->getExtensions('payment');

        $ac_payments = [];
        //#Check config of selected shipping method and see if we have accepted payments restriction
        $shipping_ext = explode('.', $this->session->data['fast_checkout'][$this->cart_key]['shipping_method']['id']);
        $ship_ext_config = $this->model_checkout_extension->getSettings($shipping_ext[0]);
        $accept_payment_ids = $ship_ext_config[$shipping_ext[0]."_accept_payments"];
        if ($this->session->data['fast_checkout'][$this->cart_key]['used_balance_full']) {
            $accept_payment_ids = [];
        }

        if (is_array($accept_payment_ids) && count($accept_payment_ids)) {
            //#filter only allowed payment methods based on shipping
            foreach ($results as $result) {
                if (in_array($result['extension_id'], $accept_payment_ids)) {
                    $ac_payments[] = $result;
                }
            }
        } elseif ($this->session->data['fast_checkout'][$this->cart_key]['used_balance_full']) {
            $ac_payments = [];
            $paymentHTML = $this->html->buildButton([
                'text'  => $this->language->get('order_confirm'),
                'title' => $this->language->get('order_confirm'),
                'id'    => 'no_payment_confirm',
                'style' => 'btn btn-primary btn-lg btn-block',
                'icon'  => 'fa fa-check',
            ]);
            $this->view->assign('payment_form', $paymentHTML);
        } else {
            $ac_payments = $results;
        }

        $payment_address = $this->model_account_address->getAddress($this->session->data['fast_checkout'][$this->cart_key]['payment_address_id']);
        $psettings = [];
        foreach ($ac_payments as $result) {
            //#filter only allowed payment methods based on total min/max
            $pkey = $result['key'];
            $psettings[$pkey] = $this->model_checkout_extension->getSettings($pkey);
            $min = $psettings[$pkey][$pkey."_payment_minimum_total"];
            $max = $psettings[$pkey][$pkey."_payment_maximum_total"];
            if ((has_value($min) && $total['total'] < $min)
                || (has_value($max) && $total['total'] > $max)
            ) {
                continue;
            }

            $this->loadModel('extension/'.$pkey);
            $method = $this->{'model_extension_'.$pkey}->getMethod($payment_address);
            if ($method) {
                $method_data[$pkey] = $method;
                //# Add storefront icon if available
                $icon = $psettings[$pkey][$pkey."_payment_storefront_icon"];
                if (has_value($icon)) {
                    $icon_data = $this->model_checkout_extension->getSettingImage($icon);
                    $icon_data['image'] = $icon;
                    $method_data[$pkey]['icon'] = $icon_data;
                }
                //check if this is a redirect type of the payment
                if ($psettings[$pkey][$pkey."_redirect_payment"]) {
                    $method_data[$pkey]['is_redirect_payment'] = true;
                }
            }
        }
        $this->session->data['payment_methods'] = $method_data;

        return $method_data;
    }

    protected function _select_shipping($selected = '')
    {
        $selected_shipping = [];
        if ($selected) {
            $selected_shipping = explode('.', $selected);
        }

        if (!isset($this->session->data['fast_checkout'][$this->cart_key]['shipping_methods']) || !$this->config->get('config_shipping_session')) {
            $quote_data = [];

            $results = $this->model_checkout_extension->getExtensions('shipping');
            if ($this->session->data['fast_checkout'][$this->cart_key]['shipping_address_id']) {
                $this->loadModel('account/address');
                $shipping_address = $this->model_account_address->getAddress($this->session->data['fast_checkout'][$this->cart_key]['shipping_address_id']);
            } else {
                $shipping_address = $this->session->data['guest']['shipping'];
            }

            foreach ($results as $result) {
                $this->loadModel('extension/'.$result['key']);

                /** @noinspection PhpUndefinedMethodInspection */
                $quote = $this->{'model_extension_'.$result['key']}->getQuote($shipping_address);

                if ($quote) {
                    $quote_data[$result['key']] = [
                        'title'      => $quote['title'],
                        'quote'      => $quote['quote'],
                        'sort_order' => $quote['sort_order'],
                        'error'      => $quote['error'],
                    ];
                    $ext_setgs = $this->model_checkout_extension->getSettings($result['key']);
                    $icon = $ext_setgs[$result['key']."_shipping_storefront_icon"];
                    if (has_value($icon)) {
                        $icon_data = $this->model_checkout_extension->getSettingImage($icon);
                        $icon_data['image'] = $icon;
                        $quote_data[$result['key']]['icon'] = $icon_data;
                    }
                }
            }

            $sort_order = [];
            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);
            $this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'] = $quote_data;

            if ($this->session->data['fast_checkout'][$this->cart_key]['shipping_method']) {
                $shippingMethods = explode('.', $this->session->data['fast_checkout'][$this->cart_key]['shipping_method']['id']);
                $shippingMethod = $shippingMethods[0];
                if ($shippingMethod && $quote_data[$shippingMethod]) {
                    $this->session->data['fast_checkout'][$this->cart_key]['shipping_method'] = $quote_data[$shippingMethod]['quote'][$shippingMethod];
                } else {
                    unset($this->session->data['fast_checkout'][$this->cart_key]['shipping_method']);
                }
            }

            //if any error in shipping method, need to log
            if (count($this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'])) {
                foreach ($this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'] as $shp_key => $shmd) {
                    if ($shmd['error']) {
                        $this->_to_log("Error with shipping quote: ".$shmd['error']);
                    }
                }
            } elseif (count($this->session->data['fast_checkout'][$this->cart_key]['shipping_methods']) === 0 && $this->cart->hasShipping()) {
                $this->_to_log("No shipping option offered to the customer at checkout. Probably, need to check shipping setting!");
            }
        }

        //# If only 1 shipping and it is set to be defaulted
        if ($selected_shipping) {
            //user selected new shipping method
            $this->session->data['fast_checkout'][$this->cart_key]['shipping_method'] =
                $this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'][$selected_shipping[0]]['quote'][$selected_shipping[1]];
        } else {
            if (count($this->session->data['fast_checkout'][$this->cart_key]['shipping_methods']) == 1) {
                //set only method
                $only_method = $this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'];
                foreach ($only_method as $key => $value) {
                    $method_name = $key;
                    #Check config if we allowed to set this shipping and skip the step
                    $ext_config = $this->model_checkout_extension->getSettings($method_name);
                    $autoselect = $ext_config[$method_name."_autoselect"];
                    if ((is_array($only_method[$key]['quote']) && sizeof($only_method[$key]['quote']) == 1) || $autoselect) {
                        $this->session->data['fast_checkout'][$this->cart_key]['shipping_method'] = current($only_method[$method_name]['quote']);
                    }
                }
            } elseif (!$this->session->data['fast_checkout'][$this->cart_key]['shipping_method']) {
                //no shipping ever selected. User will be asked
            }
        }

        $this->data['shipping_methods'] = $this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'] ? $this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'] : [];
        $shipping = $this->session->data['fast_checkout'][$this->cart_key]['shipping_method']['id'];
        if ($this->data['shipping_methods']) {
            foreach ($this->data['shipping_methods'] as $k => $v) {
                if ($v['quote'] && is_array($v['quote'])) {
                    foreach ($v['quote'] as $key => $val) {
                        //check if we have only one method and select by default if was selected before
                        $selected = false;
                        if (count($this->data['shipping_methods']) == 1 && count($v['quote']) == 1) {
                            $selected = true;
                        } else {
                            if ($shipping == $val['id']) {
                                $selected = true;
                            }
                        }
                        $this->data['shipping_methods'][$k]['quote'][$key]['radio'] = $this->html->buildRadio(
                            [
                                'type'    => 'radio',
                                'id'      => $val['id'],
                                'name'    => 'shipping_method',
                                'options' => [$val['id'] => '<div class="checkbox_place"><i class="fa fa-check fa-2x"></i></div>'],
                                'value'   => $selected,
                            ]);
                    }
                }
            }
        } else {
            $this->data['shipping_methods'] = array();
        }
        $this->session->data['fast_checkout'][$this->cart_key]['shipping_methods'] = $this->data['shipping_methods'];
    }

    protected function _validate_order_details($request)
    {

        if ($this->config->get('fast_checkout_require_payment_address')) {
            if (!$request['payment_address_id']
                && !$this->session->data['guest']['address_1']
            ) {
                $this->error['message'] = $this->language->get('fast_checkout_error_payment_address');
                return false;
            }
        }

        if ($this->cart->hasShipping()) {
            if (!$request['shipping_address_id']
                && !$this->session->data['guest']['shipping']['address_1']
            ) {
                $this->error['message'] = $this->language->get('fast_checkout_error_shipping_address');
                return false;
            }
            if (!$request['shipping_method']) {
                $this->error['message'] = $this->language->get('fast_checkout_error_shipping_method');
                return false;
            }
        }

        if ($this->config->get('fast_checkout_require_phone_number') && !$request['telephone'] && !$request['cc_telephone'] ) {
            $this->error['message'] = $this->language->get('fast_checkout_error_phone');
            return false;
        }

        if (!$this->customer->isLogged() && !$request['cc_owner']) {
            $this->error['message'] = $this->language->get('fast_checkout_error_owner');
            return false;
        }

        if (!$request['cc_email']) {
            $this->error['message'] = $this->language->get('fast_checkout_error_email');
            return false;
        }
        return true;
    }

    protected function _validateEmailTelephone($request)
    {
        $errors = array();
        if ($this->config->get('fast_checkout_require_phone_number') && !$request['telephone']) {
            $errors[] .= $this->language->get('fast_checkout_error_phone');
        }

        if (!$request['cc_email']) {
            $errors[] = $this->language->get('fast_checkout_error_email');
        }
        $this->error['warning'] .= implode('<br>', $errors);
    }

    protected function _handleCoupon($request)
    {
        //handle coupon
        if ($this->config->get('coupon_status') && $this->config->get('fast_checkout_allow_coupon')) {
            $this->data['enabled_coupon'] = true;
        }
        if ($this->data['enabled_coupon'] && $request['coupon_code']
            && $this->_validateCoupon($request['coupon_code'])) {
            $this->session->data['fast_checkout'][$this->cart_key]['coupon'] = $request['coupon_code'];
        } else {
            if ($request['remove_coupon']) {
                unset($this->session->data['fast_checkout'][$this->cart_key]['coupon']);
            }
        }
    }

    protected function _handleBalance($request)
    {
        if (!$this->customer->isLogged() || !$request['balance']) {
            return null;
        }

        if ($request['balance'] == 'disapply' || $request['balance'] == 'reapply') {
            unset($this->session->data['fast_checkout'][$this->cart_key]['used_balance'],
                $this->session->data['fast_checkout'][$this->cart_key]['balance'],
                $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full'],
                $this->session->data['fast_checkout'][$this->cart_key]['payment_method'],
                $this->session->data['used_balance']
            );
        }
        if ($request['balance'] == 'apply' || $request['balance'] == 'reapply') {
            //get customer balance in general currency
            $balance = $this->customer->getBalance();

            $order_totals = $this->cart->buildTotalDisplay(true);
            $order_total = $order_totals['total'];
            if ($this->session->data['fast_checkout'][$this->cart_key]['used_balance']) {
                #check if we still have balance.
                if ($this->session->data['fast_checkout'][$this->cart_key]['used_balance'] <= $balance) {
                    $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full'] = true;
                } else {
                    //if balance become less or 0 reapply partial
                    $this->session->data['fast_checkout'][$this->cart_key]['used_balance'] = $balance;
                    $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full'] = false;
                }
            } else {
                if ($balance > 0) {
                    if ($balance >= $order_total) {
                        $this->session->data['fast_checkout'][$this->cart_key]['used_balance'] = $order_total;
                        $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full'] = true;
                        $this->session->data['fast_checkout'][$this->cart_key]['payment_method'] = array(
                                            'id'    => 'no_payment_required',
                                            'title' => $this->language->get('no_payment_required'),
                                        );
                    } else { //partial pay
                        $this->session->data['fast_checkout'][$this->cart_key]['used_balance'] = $balance;
                        $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full'] = false;
                    }
                }
            }
            $this->session->data['used_balance'] = $this->session->data['fast_checkout'][$this->cart_key]['used_balance'];
            $in_data = array_merge((array)$this->session->data, $this->session->data['fast_checkout'][$this->cart_key]);
            $this->updateOrCreateOrder($in_data, $request);

            $order_totals = $this->cart->buildTotalDisplay(true);
            $order_total = $order_totals['total'];
            //if balance enough to cover order amount
            if ($order_total == 0 && $this->session->data['fast_checkout'][$this->cart_key]['used_balance_full']) {
                return null;
            }
        }
    }

    protected function _validateCoupon($coupon_code)
    {
        $promotion = new APromotion();
        $coupon = $promotion->getCouponData($coupon_code);
        if (!$coupon) {
            $this->error['message'] = $this->language->get('fast_checkout_error_coupon');
        }

        //validate post data
        $this->extensions->hk_ValidateData($this);

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    protected function _to_log($message)
    {
        if (!$message) {
            return false;
        }
        $message = 'FastCheckout Error: '.$message;
        $error = new AError($message);
        $error->toLog()->toDebug();
        unset($error);
        return true;
    }

    protected function _show_error($error_text = '')
    {
        if (!$error_text) {
            $error_text = $this->language->get('fast_checkout_error_incorrect_request');
        }
        $this->response->setOutput($this->_build_error($error_text));
    }

    protected function _build_error($error_text = '')
    {
        $this->data['error'] = $error_text;
        $this->view->batchAssign($this->data);
        return $this->view->fetch('responses/checkout/error.tpl');
    }

    public function changeCheckBox()
    {
        $fieldName = $this->request->post['fieldName'];
        $isOn = $this->request->post['isOn'];
        if (!$fieldName) {
            return;
        }
        $this->session->data['fast_checkout'][$this->cart_key]['additional'][$fieldName] = $isOn;
    }

    protected function removeNoStockProducts()
    {
        $cartProducts = $this->cart->getProducts();
        foreach ($cartProducts as $key => $cartProduct) {
            if (!$cartProduct['stock'] && !$this->config->get('config_stock_checkout')) {
                unset($this->session->data['fast_checkout'][$this->cart_key]['cart'][$key]);
            }
        }
    }
}
