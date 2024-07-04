<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2024 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details is bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs please refer to http://www.AbanteCart.com for more information.
 */

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * Class ControllerResponsesCheckoutPay
 *
 */
class ControllerResponsesCheckoutPay extends AController
{
    public $error = [];
    protected $action = '';
    protected $allow_guest = false;
    /** @var array short reference to fast checkout session data */
    protected $fc_session;

    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->load->library('json');
        $this->allow_guest = $this->config->get('config_guest_checkout');
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $cartClassName = get_class($this->cart);
        $this->registry->set(
            'cart',
            new $cartClassName($this->registry, $this->session->data['fc'])
        );

        $this->loadModel('catalog/product');
        $this->loadModel('checkout/extension');
        $this->loadModel('checkout/order');
        $this->loadLanguage('checkout/fast_checkout');

        $this->data['require_telephone'] = $this->config->get('fast_checkout_require_phone_number');

        //set tax zone for tax class based on session data
        $tax_zone_id = $tax_country_id = null;
        $guestSessionData = $this->session->data['fc']['guest'] ? : $this->session->data['guest'];
        if ($this->allow_guest && $guestSessionData) {
            //when payment address was set as address for taxes
            if ($this->config->get('config_tax_customer')) {
                $tax_country_id = $guestSessionData['country_id'];
                $tax_zone_id = $guestSessionData['zone_id'];
            } else {
                $tax_country_id = $guestSessionData['shipping']['country_id'] ?? $guestSessionData['country_id'];
                $tax_zone_id = $guestSessionData['shipping']['zone_id'] ?? $guestSessionData['zone_id'];
            }
        }

        if ($tax_country_id && !$this->request->post['country_id']) {
            $this->tax->setZone($tax_country_id, $tax_zone_id);
        }
        //short reference to fast checkout session
        $this->fc_session =& $this->session->data['fc'];
        //set cart key into cookie every request to compare it on js-side
        // (case when two single-checkout tabs opened)
        //see js function checkCartKey(); in the tpls
        setCookieOrParams(
            'fc_cart_key',
            $this->fc_session['cart_key'],
            [
                'path'     => dirname($this->request->server['PHP_SELF']),
                'domain'   => null,
                'secure'   => (defined('HTTPS') && HTTPS),
                'httponly' => false,
                'samesite' => ((defined('HTTPS') && HTTPS) ? 'None' : 'lax')
            ]
        );
    }

    //DO NOT REMOVE!
    public function __destruct()
    {
        //this needed to save session for next controller call
        $this->session->data['fc'] = $this->fc_session;
    }

    public function main()
    {
        $this->data['error'] = '';
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $request = array_merge($this->request->get, $this->request->post);
        //handle coupon
        $this->_handleCoupon($request);
        $getParams = '';
        $this->data['onChangeCheckboxBtnUrl'] = $this->html->getSecureURL('r/checkout/pay/changeCheckBox');
        if ($this->config->get('config_checkout_id')) {
            $this->loadLanguage('checkout/fast_checkout');
            /** @var ModelCatalogContent $mdl */
            $mdl = $this->loadModel('catalog/content');
            $content_info = $mdl->getContent($this->config->get('config_checkout_id'));
            if ($content_info) {
                $this->data['text_accept_agree'] = $this->language->get('text_accept_agree');
                $this->data['text_accept_agree_href'] = $this->html->getURL(
                    'r/content/content/loadInfo',
                    '&content_id='.$this->config->get('config_checkout_id'),
                    true
                );
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
            $this->data['comment'] = $this->fc_session['comment'];
        }

        $this->data['address_edit_base_url'] = $this->html->getSecureURL(
            'account/address/update',
            '&address_id='
        );

        if (!$this->customer->isLogged() && $this->allow_guest) {
            $this->data['allow_account_creation'] = $this->config->get('fast_checkout_create_account');
            //set default value if allow to create account for guests
            if($this->data['allow_account_creation']
                && !isset($this->fc_session['additional']['create_account'])
            ) {
                $this->fc_session['additional']['create_account'] = true;
            }
        }

        $this->data['payment_equal_shipping_address'] = $this->config->get('fast_checkout_payment_address_equal_shipping');
        $this->data['all_addresses'] = [];
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
                if ($this->fc_session['payment_address_id']) {
                    if(
                        in_array(
                            $this->fc_session['payment_address_id'],
                            array_column($this->data['all_addresses'],'address_id')
                        )
                    ) {
                        $address_id = $this->fc_session['payment_address_id'];
                    }else{
                        $this->fc_session['payment_address_id'] = $address_id;
                    }
                }
            }
            foreach ($this->data['all_addresses'] as $adr) {
                if ($adr['address_id'] == $address_id) {
                    $this->fc_session['payment_address_id'] = $adr['address_id'];
                    if ($this->config->get('config_tax_customer')
                        || (!$this->config->get('config_tax_customer') && !$this->cart->hasShipping())
                    ) {
                        $tax_zone_id = $adr['zone_id'];
                        $tax_country_id = $adr['country_id'];
                    }
                    break;
                }
            }
            if (!$address_id) {
                $adr = current($this->data['all_addresses']);
                $address_id = $this->fc_session['payment_address_id'] = $adr['address_id'];
            }
            $this->data['payment_address'] = $this->model_account_address->getAddress($address_id);
            if(!$this->request->get['shipping_address_id']) {
                //validate payment address. See hook calls inside. Some extensions can affect on it
                $this->error = $this->model_account_address->validateAddressData($this->data['payment_address']);
                if ($this->error) {
                    $this->data['error'] = $this->language->get('fast_checkout_text_payment_address') . ": " . implode("\n", $this->error);
                }
            }
        } elseif ($this->allow_guest) {
            //set default value if allow to create account for guests
            if(
                !isset($this->fc_session['additional']['same_as_shipping'])
                || $this->data['payment_equal_shipping_address']
            ) {
                $this->fc_session['additional']['same_as_shipping'] = true;
            }
            //note: guest details in stored into main session to share details with main site
            if (!$this->fc_session['guest']['address_1']) {
                //shipping required, show address form.
                $this->action = 'enter';
                $this->_addressForm('payment', (array)$this->data['default_address']);
                return;
            } else {
                if ($this->fc_session['guest']) {
                    $tax_country_id = $this->config->get('config_tax_customer')
                        ? $this->fc_session['guest']['country_id']
                        : '';
                    $tax_zone_id = $this->config->get('config_tax_customer')
                        ? $this->fc_session['guest']['zone_id']
                        : '';
                }
            }
        }
        if ($tax_country_id) {
            $this->tax->setZone($tax_country_id, $tax_zone_id);
        }

        //check if shipping required.
        if (!$this->cart->hasShipping()) {
            unset(
                $this->fc_session['shipping_address_id'],
                $this->fc_session['shipping_method'],
                $this->fc_session['shipping_methods'],
                $this->session->data['guest']['shipping'],
                $this->fc_session['guest']['shipping'],
                $this->session->data['fc']['guest']['shipping'],
            );
        } else {
            if ($this->customer->isLogged()) {
                $this->data['loggedin'] = true;
                $this->loadModel('account/address');
                if (!$this->data['all_addresses']) {
                    $this->data['all_addresses'] = $this->model_account_address->getAddresses();
                }
                if (!count($this->data['all_addresses'])) {
                    //Something wrong. Account is missing address, direct to regular customer address page.
                    $this->error['message'] = $this->language->get('fast_checkout_error_no_address');
                    $this->data['error'] = $this->error['message'];
                    $this->view->batchAssign($this->data);
                    $this->response->setOutput($this->view->fetch('responses/checkout/main.tpl'));
                    return;
                }
                //was address changed?
                if ($this->request->get['shipping_address_id']) {
                    $address_id = $this->request->get['shipping_address_id'];
                } else {
                    if ($this->fc_session['shipping_address_id']) {
                        $address_id = $this->fc_session['shipping_address_id'];
                    } else {
                        $address_id = $this->customer->getAddressId();
                    }
                }

                foreach ($this->data['all_addresses'] as $adr) {
                    if ($adr['address_id'] == $address_id) {
                        $this->fc_session['shipping_address_id'] = $adr['address_id'];
                        if (!$this->config->get('config_tax_customer')) {
                            $tax_zone_id = $adr['zone_id'];
                            $tax_country_id = $adr['country_id'];
                        }
                        break;
                    }
                }
                if (!$address_id) {
                    $adr = current($this->data['all_addresses']);
                    $address_id = $this->fc_session['shipping_address_id'] = $adr['address_id'];
                    if (!$this->config->get('config_tax_customer')) {
                        $tax_zone_id = $adr['zone_id'];
                        $tax_country_id = $adr['country_id'];
                    }
                }elseif($this->config->get('fast_checkout_payment_address_equal_shipping')){
                    $this->fc_session['payment_address_id'] = $address_id;
                }

                $addressData = $this->model_account_address->getAddress($address_id);
                if($addressData === false){
                    $address_id = $this->customer->getAddressId();
                    $addressData = $this->model_account_address->getAddress($address_id);
                }
                //validate payment address. See hook calls inside. Some extensions can affect on it
                $this->error = $this->model_account_address->validateAddressData($addressData);
                if($this->error) {
                    $this->data['error'] = $this->language->get('fast_checkout_text_shipping_address').": ". implode("\n", $this->error);
                }
            } else {
                if ($this->allow_guest && !$this->fc_session['guest']['shipping']) {
                    //shipping required, show address form for guest
                    $this->action = 'enter';
                    $this->_addressForm('shipping', (array)$this->data['default_address']);
                    return;
                } else {
                    if ($this->allow_guest && $this->fc_session['guest']) {
                        $tax_country_id = !$this->config->get('config_tax_customer')
                                            && isset($this->fc_session['guest']['shipping']['country_id'])
                                       ? $this->fc_session['guest']['shipping']['country_id']
                                       : $this->fc_session['guest']['country_id'];
                        $tax_zone_id = !$this->config->get('config_tax_customer')
                                            && isset($this->fc_session['guest']['shipping']['zone_id'])
                                        ? $this->fc_session['guest']['shipping']['zone_id']
                                        : $this->fc_session['guest']['zone_id'];
                        //if shipping address takes as tax  address
                        if(!$this->config->get('config_tax_customer')){
                           $this->fc_session['country_id'] = $tax_country_id;
                           $this->fc_session['zone_id'] = $tax_zone_id;
                        }
                    } else {
                        //no guess allowed, need to login
                        $this->action = 'login';
                    }
                }
            }
        }

        if ($tax_country_id) {
            $this->tax->setZone(
                $tax_country_id,
                $tax_zone_id
            );

            $this->fc_session['tax_country_id'] = $tax_country_id;
            $this->fc_session['tax_zone_id'] = $tax_zone_id;
            $this->fc_session['country_id'] = $tax_country_id;
            $this->fc_session['zone_id'] = $tax_zone_id;
            $this->session->data['fc'] = $this->fc_session;
        }

        //set shipping method
        $this->_select_shipping($this->request->get['shipping_method']??$this->fc_session['shipping_method']['id']);

        //handle balance. Re-apply balance on every request as total can change
        if ($this->fc_session['used_balance'] && !$request['balance']) {
            $request['balance'] = 'reapply';
        }
        $this->_handleBalance($request);

        //final step to build cart view and totals
        if (!$this->_build_cart_product_details()) {
            $this->_show_error('No items to be purchased are found');
            return;
        }

        //do we show payment details yet? Show only if shipping selected
        $this->data['show_payment'] = true;
        if ($this->cart->hasShipping()
            && (!$this->fc_session['shipping_method'] || !count($this->fc_session['shipping_methods']))
        ) {
                //no shipping selected yet, not ready for payment
                $this->data['show_payment'] = false;
        }

        $form = new AForm();
        $form->setForm(['form_name' => 'PayFrm']);
        $this->data['pay_form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'PayFrm',
                'action' => '',
            ]
        );
        if ($this->customer->isLogged()) {
            $this->data['loggedin'] = true;
            //get customer name for payment from selected payment address
            foreach ($this->data['all_addresses'] as $adr) {
                if ($adr['address_id'] == $this->fc_session['payment_address_id']) {
                    $this->data['customer_name'] = $adr['firstname'].' '.$adr['lastname'];
                    break;
                }
            }
            //no name found use names from account
            if (!$this->data['customer_name']) {
                $this->data['customer_name'] = $this->customer->getFirstName().' '.$this->customer->getLastName();
            }
            $this->data['logout_url'] = $this->html->getSecureURL('r/checkout/pay/logout', $getParams);

            //customer details
            $this->data['customer_email'] = $this->customer->getEmail();
            $phone = $this->data['customer_telephone'] = $this->customer->getTelephone() ?: $this->fc_session['telephone'];
            if ($phone && $this->config->get('fast_checkout_require_phone_number')) {
                $pattern = $this->config->get('config_phone_validation_pattern') ? : DEFAULT_PHONE_REGEX_PATTERN;
                if (mb_strlen($phone) < 3 || mb_strlen($phone) > 32 || !preg_match($pattern, $phone)) {
                    //hide payment form when phone number required and incorrect
                    $this->data['show_payment'] = false;
                }
            }
        }

        if ($this->data['show_payment']) {
            //Order must be created before payment form rendering!
            if (!$this->session->data['order_id'] || $this->request->get['payment_address_id']) {
                $this->updateOrCreateOrder($this->fc_session, $request);
            }
            $this->_build_payment_view($request, $getParams);
        }

        $this->addLoginForm($request, $getParams);

        $this->data['step'] = $this->data['step'] ?: 'payment';

        //last step with payment form
        $this->data['action'] = $this->action;

        if ($this->fc_session['guest']) {
            $this->data['edit_address_url'] = $this->html->getSecureURL(
                'r/checkout/pay/edit_address',
                $getParams
            );
        }
        $this->data['main_url'] = $this->html->getSecureURL('r/checkout/pay/main', $getParams);
        if (isset($this->error['message'])) {
            $this->data['error'] = $this->error['message'];
        }

        //pass cart session to template view
        $this->data['csession'] = $this->fc_session;

        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        if (!$this->fc_session['guest'] && !$this->customer->getId()) {
            $this->address();
            $this->session->data['fc'] = $this->fc_session;
            return;
        }

        $this->updateOrCreateOrder($this->fc_session, $request);

        $this->view->batchAssign($this->data);
        $this->response->setOutput($this->view->fetch('responses/checkout/main.tpl'));
    }

    public function updateOrderData()
    {
        //this needed to save session for next controller call
        $this->session->data['fc'] = $this->fc_session;
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $request = array_merge($this->request->get, $this->request->post);
        $this->updateOrCreateOrder($this->fc_session, $request);

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function updateOrCreateOrder($in_data, $request)
    {
        //do not allow to run if already ran and failed
        if($this->error['updateOrCreateOrder']){
            return;
        }

        //do nothing if customer is nobody
        if(!$this->customer->isLogged() && !$this->fc_session['guest']){
            return;
        }

        //if customer unknown - skip creation of order
        if (!$this->config->get('config_guest_checkout') && !$this->customer->isLogged()) {
            return;
        }
        //this needed to save session for next controller call
        $this->session->data['fc'] = $this->fc_session;

        if (!$this->session->data['order_id']) {
            //create order and save details
            $order = new AOrder($this->registry);
        } else {
            $order = new AOrder($this->registry, $this->session->data['order_id']);
        }
        $order->buildOrderData($in_data);
        $order_id = $order->saveOrder();

        /** @var ModelCheckoutFastCheckout $mdl */
        $mdl = $this->loadModel('checkout/fast_checkout');
        if ($order_id) {
            if ($request['telephone']) {
                $mdl->updateOrderDetails(
                    $order_id,
                    [
                        'telephone' => $request['telephone'],
                    ]
                );
                if (!$this->customer->isLogged()) {
                    $this->fc_session['guest']['telephone'] = $request['telephone'];
                }elseif(!$this->customer->getTelephone()){
                    $this->loadModel('account/customer');
                    $this->model_account_customer->editCustomer(
                        [
                            'firstname' => $this->customer->getFirstName(),
                            'lastname'  => $this->customer->getLastName(),
                            'email'     => $this->customer->getEmail(),
                            'telephone' => $request['telephone'],
                            'fax'       => $this->customer->getFax()
                        ]
                    );
                }
                $this->fc_session['telephone'] = $request['telephone'];
            }
            if ($request['comment']) {
                $mdl->updateOrderDetails(
                    $order_id,
                    [
                        'comment' => $request['comment'],
                    ]
                );
                $this->fc_session['comment']
                    = $this->session->data['fc']['comment']
                    = $request['comment'];
            }

            $this->session->data['order_id'] = $order_id;
        } else {
            $this->_to_log(
                sprintf(
                    $this->language->get('fast_checkout_error_unexpected_log'),
                    var_export($in_data, true)."\nOrderID: ".var_export($order_id, true)."\nOrderData: ".var_export($order->getOrderData(), true)
                )
            );
            $this->error['message'] = $this->language->get('fast_checkout_error_unexpected');
            $this->error['updateOrCreateOrder'] = true;
            unset($this->session->data['order_id']);
            $this->main();
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

            $this->data['login_form']['loginname'] = $form->getFieldHtml(
                [
                    'type'  => 'input',
                    'name'  => 'loginname',
                    'value' => $request['loginname'],
                ]
            );
            $this->data['login_form']['password'] = $form->getFieldHtml(
                [
                    'type' => 'password',
                    'name' => 'password',
                ]
            );

            $this->data['customer_email'] = $request['email'] ? : $this->fc_session['guest']['email'];
            $this->data['customer_telephone'] = $request['telephone'] ? : $this->fc_session['guest']['telephone'];
            $this->data['reset_url'] = $this->html->getSecureURL('account/login');
        }
    }

    protected function _build_payment_view($request, $get_params)
    {
        $this->data['payment_methods'] = $this->_get_payment_methods();
        $this->data['payment_method'] = $request['payment_method'] ?: $this->data['payment_method'];

        if(count($this->data['payment_methods']) == 1
            && current($this->data['payment_methods'])['id'] == 'no_payment_required'
        ){
            $this->data['payment_method'] = 'no_payment_required';
        }

        $selected_payment = $this->fc_session['payment_method'];
        //case when switches shipping method into method with accepted payments list
        if ($selected_payment && !isset($this->data['payment_methods'][$selected_payment['id']])) {
            unset($this->fc_session['payment_method']);
            $selected_payment = [];
        }

        if (!$this->data['payment_method'] && count($this->data['payment_methods']) == 1) {
            $this->data['payment_method'] = key($this->data['payment_methods']);
        } else {
            if (!$this->data['payment_method'] && !$selected_payment) {
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
        }

        //show selected payment form
        if ($this->data['payment_method']) {
            $this->fc_session['payment_method'] = [
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
            $this->view->assign('payment_form', $dd->dispatchGetOutput());
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

        //check if logged in
        if ($this->customer->isLogged()) {
            if ($this->session->data['order_id']) {
                $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
                if ($order_info['telephone']) {
                    $this->fc_session['telephone'] = $this->data['customer_telephone'] = $order_info['telephone'];
                }
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
            $this->data['balance_value'] = $this->currency->format(
                $balance,
                $this->session->data['currency'],
                1
            );
            $this->data['used_balance_full'] = $this->fc_session['used_balance_full'];
            $this->data['used_balance'] = $this->fc_session['used_balance'];

            if ($this->data['used_balance_full']) {
                unset(
                    $this->data['payment_methods'],
                    $this->data['payment_method']
                );
            }
            $this->data['balance_remains'] = $this->data['balance_value'];
            if ((float) $this->data['used_balance'] > 0) {
                $this->data['balance_remains'] = $this->currency->format(
                    $balance_def_currency - (float) $this->data['used_balance']
                );
            }
        } else {
            $this->data['customer_telephone'] = $this->fc_session['guest']['telephone'];
        }

        //check if any payment is available for address or show balance if available.
        $this->data['payment_select_action'] = $payment_select_action;
        $this->data['payment_available'] = $this->data['payment_methods'] || $this->fc_session['used_balance_full'];
        if (!$this->data['balance_enough'] && !$this->data['payment_available']) {
            $this->error['message'] = $this->data['payment_available'];
            $this->data['payment_available'] = false;
        } else {
            if ($this->data['balance_enough'] && !$this->data['payment_available']) {
                //we only have balance
                $this->data['payment_available'] = true;
            }
        }

        if ($this->data['payment_available'] === true) {
            //Check if fraud extension enabled and any payment is blocked based on the score
            $this->extensions->hk_ProcessData(
                $this,
                'fast_checkout_fraud_payment_checkpoint',
                $this->fc_session
            );
        }
    }

    /**
     * @return bool
     * @throws AException
     */
    protected function _build_cart_product_details()
    {
        $resource = new AResource('image');
        $products = [];
        foreach ($this->cart->getProducts()  as $cartProduct) {
            $option_data = [];
            foreach ($cartProduct['option'] as $option) {
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

            //get main image
            $thumbnail = $resource->getMainImage(
                'products',
                $cartProduct['product_id'],
                $this->config->get('config_image_grid_width'),
                $this->config->get('config_image_grid_height')
            );

            $products[] = [
                'key'       => $cartProduct['key'],
                'name'      => $cartProduct['name'],
                'thumbnail' => $thumbnail,
                'option'    => $option_data,
                'quantity'  => $cartProduct['quantity'],
                'stock'     => $cartProduct['stock'],
                'price'     => $this->currency->format(
                    $this->tax->calculate(
                        $cartProduct['price'],
                        $cartProduct['tax_class_id'],
                        $this->config->get('config_tax')
                    )
                ),
                'href'      => $this->html->getSEOURL('product/product', '&product_id='.$cartProduct['product_id'], true),
            ];
        }

        //check for virtual product such as gift certificate, account credit etc
        $virtual_products = $this->cart->getVirtualProducts();
        if ($virtual_products) {
            foreach ($virtual_products as $virtual) {
                $products[] = [
                    'name'     => ($virtual['name'] ? : 'Virtual Product'),
                    'model'    => $virtual['model'],
                    'price'    => $this->currency->format(
                                        $virtual['amount'],
                                        $this->currency->getCode()
                                  ),
                    'quantity' => ($virtual['quantity'] ? : 1),
                    'option'   => [],
                    'weight'   => (float)$virtual['weight'],
                    'thumbnail' => $virtual['thumbnail'],
                ];
                $this->data['items_total'] += ($virtual['quantity'] ? : 1)
                    * $this->currency->format(
                        $virtual['amount'],
                        $this->currency->getCode(),
                        '',
                        false
                    );
            }
        }

        $this->data['products'] = $products;

        $display_totals = $this->cart->buildTotalDisplay(true);
        $this->data['totals'] = $display_totals['total_data'];
        $this->data['total'] = $display_totals['total'];
        $this->data['total_string'] = $this->currency->format($display_totals['total']);
        return ($this->data['totals']);
    }

    public function confirm()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->action = 'confirm';
        //Validate quick cart instance
        if (!$this->fc_session) {
            $this->_show_error();
            return;
        }
        $this->tax->setZone(
            $this->fc_session['tax_country_id'],
            $this->fc_session['tax_zone_id']
        );
        //this needed to save session for next controller call
        $this->session->data['fc'] = $this->fc_session;

        //recalculate totals
        $this->cart->buildTotalDisplay(true);

        $request = $this->request->post;
        //validate order details
        if (!$this->_validate_order_details($request)) {
            $this->action = "payment";
            $this->main();
            return;
        }

        //validate payment details
        if ($this->customer->isLogged() && $this->fc_session['used_balance_full']) {
            //validate account balance
            $balance = $this->currency->convert(
                $this->customer->getBalance(),
                $this->config->get('config_currency'),
                $this->session->data['currency']
            );

            //balance has to cover full order amount
            if ($balance < $this->cart->getFinalTotal()) {
                $this->error['message'] = $this->language->get('fast_checkout_error_low_balance');
                $this->main();
                return;
            }
            $this->fc_session['payment_method'] = [
                'id'    => 'no_payment_required',
                'title' => $this->language->get('no_payment_required'),
            ];

            $this->updateOrCreateOrder($this->fc_session, $request);
        }

        if (!$this->customer->getId()) {
            $guest_info =& $this->fc_session['guest'];
            //add email into guest session data
            $guest_info['email'] = $request['email'];
            if ($request['telephone']) {
                $guest_info['telephone'] = $request['telephone'];
            }
        }

        $order_id = $this->session->data['order_id'];
        //Process payment
        if ($this->fc_session['used_balance_full']) {
            $this->model_checkout_order->confirm(
                $order_id,
                $this->config->get('config_order_status_id')
            );
            $this->load->library('json');
            $data['url'] = $this->html->getSecureURL('checkout/finalize', '&order_id='.$order_id);
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
    }

    public function success()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);
        $order_id = $this->request->get['order_id'];
        if (!$order_id || !is_numeric($order_id) || $this->session->data['processed_order_id'] != $order_id) {
            $this->error['message'] = $this->language->get('fast_checkout_error_unexpected');
            $this->main();
            return;
        }

        $this->loadModel('checkout/order');
        $order_details = $this->model_checkout_order->getOrder($order_id);
        if ($order_details && $order_details['order_status_id'] > 0) {
            $this->_process_success($order_id, $order_details);
        } else {
            $this->error['message'] = $this->language->get('fast_checkout_error_unexpected');
            $this->main();
        }
    }

    protected function _process_success($order_id, $order_data)
    {
        if (!$order_id || empty($order_data)) {
            $this->error['message'] = $this->language->get('fast_checkout_error_unexpected');
            $this->main();
            return;
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
            /** @var ModelCheckoutFastCheckout $mdl */
            $mdl = $this->loadModel('checkout/fast_checkout', 'storefront');
            //for guest build tokenized access to download and order details.
            $secToken = $mdl->getGuestToken($order_id);
            $order_token = generateOrderToken($order_id,$order_data['email'], $secToken);
            $this->data['button_order_details'] = $this->html->buildElement(
                [
                    'type' => 'button',
                    'text' => $this->language->get('button_history'),
                    'href' => $this->html->getSecureURL('account/order_details', '&ot='.$order_token),
                    'icon' => 'fa fa-briefcase fa-fw',
                ]
            );
            if ($this->config->get('fast_checkout_create_account')
                && $this->session->data['fc']['additional']['create_account']
            ) {
                $this->_save_customer_account($order_data);
            }

            //if download build download link for one download or downloads list
            if ($this->config->get('config_download')) {
                $download = $this->_get_download($order_id, $order_token);
                if ($download['count'] > 0) {
                    //if downloads exists but unavailable - give order details page
                    if (!$download['download_url']) {
                        $download['download_url'] = $this->html->getSecureURL(
                            'account/order_details',
                            ($order_token ? '&ot='.$order_token : '')
                        );
                    }
                    if ($download['count'] == 1) {
                        $this->data['download_url'] = $download['download_url'];
                    } else {
                        $this->data['order_details_url'] = $download['download_url'];
                    }
                    //email download link for guest.
                    $mdl->emailDownloads($order_data, $download);
                }
            }
        }

        /** @var ModelAccountOrder $mdlOrder */
        $mdlOrder = $this->loadModel('account/order');
        $order_details = $mdlOrder->getOrder($order_id);
        //build custom order message based on the status
        if ($this->order_status->getStatusByTextId('completed') == $order_details['order_status_id']) {
            $this->data['order_finished_message'] = $this->language->get('fast_checkout_order_success_message');
        } elseif($this->session->data['processing_order_errors']) {
            $this->data['order_finished_message'] = $this->session->data['processing_order_errors'];
        } else {
            $this->data['order_finished_message'] = $this->language->get('fast_checkout_order_processing_message');
        }

        $order_data['order_products'] = $mdlOrder->getOrderProducts($order_id);
        $order_data['totals'] = $mdlOrder->getOrderTotals($order_id);

        $this->data['gaOrderData'] = AOrder::getGoogleAnalyticsOrderData( $order_data );
        $this->data['gaOrderData']['transaction_id'] = $order_id;
        $this->_clear_data();
        $this->data['step'] = 'process';

        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        $this->data['order_id'] = $order_id;
        $this->view->batchAssign($this->data);
        /** @see responses/checkout/success.tpl */
        $this->response->setOutput($this->view->fetch('responses/checkout/success.tpl'));
    }

    protected function _save_customer_account($order_data)
    {
        /** @var ModelCheckoutFastCheckout $mdl */
        $mdl = $this->loadModel('checkout/fast_checkout');
        $customer_data = [
            'status'        => 1,
            'loginname'     => $this->config->get('prevent_email_as_login')
                            ? randomWord(7)
                            : $order_data['email'],
            'password'      => randomWord(7),
            'firstname'     => $order_data['firstname'],
            'lastname'      => $order_data['lastname'],
            'email'         => $order_data['email'],
            'telephone'     => $order_data['telephone'],
            'fax'           => $order_data['fax'],
            'company'       => ($order_data['shipping_company'] ? : $order_data['payment_company']),
            'address_1'     => ($order_data['shipping_address_1'] ? : $order_data['payment_address_1']),
            'address_2'     => ($order_data['shipping_address_2'] ? : $order_data['payment_address_2']),
            'city'          => ($order_data['shipping_city'] ? : $order_data['payment_city']),
            'postcode'      => ($order_data['shipping_postcode'] ? : $order_data['payment_postcode']),
            'country_id'    => ($order_data['shipping_country_id'] ? : $order_data['payment_country_id']),
            'zone_id'       => ($order_data['shipping_zone_id'] ? : $order_data['payment_zone_id']),
            'language_code' => $this->language->getLanguageCode(),
        ];

        $customer_id = 0;
        $new_customer = false;
        //check is customer already exists
        $this->loadModel('account/customer');
        $customer_info = $this->model_account_customer->getCustomerByEmail($order_data['email']);
        if (!$customer_info) {
            if(!$this->config->get('prevent_email_as_login')){
                $sql = "SELECT count(*) as count
                        FROM ".$this->db->table('customers')."
                        WHERE loginname='".$this->db->escape($customer_data['loginname'])."'";
                $result = $this->db->query($sql);
                if($result->row['count']>0){
                    $customer_data['loginname'] .= '_'.time();
                }
            }
            $this->data['account_data'] = $customer_data;
            // add ability to overwrite data via hooks
            $this->extensions->hk_ProcessData($this, __FUNCTION__);

            $customer_id = (int) $mdl->addCustomer($this->data['account_data']);
            if ($customer_id) {
                $new_customer = true;
                $this->data['text_account_created'] = $this->language->get('fast_checkout_text_account_created');
                //if customer gave two addresses - save payment address as additional
                if ($order_data['shipping_address_1']
                    && $order_data['payment_address_1']
                    && ($order_data['shipping_address_1'] != $order_data['payment_address_1'])
                ) {
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
                    $this->loadModel('account/address');
                    $this->model_account_address->addAddress($address);
                }
            }
        } else {
            if ($customer_info['customer_id']) {
                $customer_id = (int) $customer_info['customer_id'];
            }
        }
        //update order_details
        if ($customer_id) {
            $mdl->updateOrderCustomer(
                $order_data['order_id'],
                $customer_id
            );
            if ($new_customer) {
                $mdl->sendEmailActivation($this->data['account_data']);
            }
        }

        $this->data['customer_id'] = $customer_id;
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    /**
     * @param int $orderId
     * @param string $orderToken
     *
     * @return array
     * @throws AException
     */
    protected function _get_download($orderId, $orderToken = '')
    {
        $downloadUrl = '';
        $customerId = (int) $this->customer->getId();
        $orderDownloads = $this->download->getCustomerOrderDownloads($orderId, $customerId);
        if (!$orderDownloads) {
            return [];
        }

        $suffix = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        //build order token for guests
        foreach ($orderDownloads as &$downloadInfo) {
            $text_status = $this->download->getTextStatusForOrderDownload($downloadInfo);
            $size = 0;
            if (is_numeric($downloadInfo['filename'])) {
                $rl = new AResource('download');
                $resource = $rl->getResource($downloadInfo['filename']);
                if ($resource && $resource['resource_path']) {
                    $size = filesize(DIR_RESOURCE.$rl->getTypeDir().$resource['resource_path']);
                }
            }else{
                $size = filesize(DIR_RESOURCE.$downloadInfo['filename']);
            }

            $i = 0;
            while (($size / 1024) > 1) {
                $size = $size / 1024;
                $i++;
            }
            //if download available
            if (!$text_status) {
                $downloadInfo['href'] = $this->html->getSecureURL(
                    'account/order_details/startdownload',
                    '&order_download_id='.$downloadInfo['order_download_id']
                    .($orderToken ? '&ot='.$orderToken : '')
                );
                $downloadInfo['text_status'] = '';
                $downloadInfo['size'] = round((float)substr($size, 0, strpos($size, '.') + 4), 2).$suffix[$i];
            } else {
                $downloadInfo['text_status'] = $text_status;
                $downloadInfo['size'] = '';
                //for pending downloads
                $this->data['text_order_download_pending'] = $this->language->get(
                    'fast_checkout_text_order_download_pending'
                );
            }
        }

        $this->data['order_downloads'] = $orderDownloads;
        $downloadsCount = sizeof($orderDownloads);

        if ($downloadsCount == 1) {
            $orderDownloads = array_values($orderDownloads);
            $downloadInfo = $orderDownloads[0];
            //if download available
            if (!$downloadInfo['text_status']) {
                $downloadUrl = $downloadInfo['href'];
            } else {
                $downloadUrl = $this->html->getSecureURL('account/order_details', '&ot='.$orderToken);
            }
        } else {
            if ($downloadsCount > 1) {
                //if some of them is available - show download button
                foreach ($orderDownloads as $d) {
                    if (!$d['text_status']) {
                        if (!$customerId) {
                            //guest download
                            $downloadUrl = $this->html->getSecureURL('account/order_details', '&ot='.$orderToken);
                        } else {
                            $downloadUrl = $this->html->getSecureURL('account/download');
                        }
                        break;
                    }
                }
            }
        }

        return [
            'count' => $downloadsCount,
            'download_url' => $downloadUrl
        ];
    }

    protected function _clear_data()
    {
        unset(
            $this->session->data['used_balance'],
            $this->session->data['cart'],
            $this->session->data['cart_key'],
            $this->session->data['shipping_method'],
            $this->session->data['shipping_methods'],
            $this->session->data['payment_method'],
            $this->session->data['payment_methods'],
            $this->session->data['order_id'],
            $this->session->data['fc']
        );
        $this->fc_session = [];
        $this->cart->clear();
    }

    public function login()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //Validate login
        $loginname = $this->request->post['loginname'];
        $password = $this->request->post['password'];
        if (!isset($loginname) || !isset($password)) {
            $this->action = 'login';
            $this->loadLanguage('account/login');
            $this->error['message'] = $this->language->get('error_login');
            if ($this->request->get['type']) {
                $data = $this->request->get['type'] == 'shipping'
                    ? $this->fc_session['guest']['shipping']
                    : $this->fc_session['guest'];
                $this->_addressForm($this->request->get['type'], $data);
                return;
            }
        } elseif (!$this->_validate_login($loginname, $password)) {
            $this->action = 'login';
            $this->loadLanguage('account/login');
            $this->error['message'] = $this->error['login_message']
                .' '
                .$this->language->get('error_login');

            if ($this->request->get['type']) {
                $data = $this->request->get['type'] == 'shipping'
                    ? $this->fc_session['guest']['shipping']
                    : $this->fc_session['guest'];
                $this->_addressForm($this->request->get['type'], $data);
                return;
            }
        } else {
            $this->action = 'payment';
        }

        $this->main();
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
            $data = $this->fc_session['guest'];
            $this->data['customer_email'] = $this->fc_session['guest']['email'];
        } else {
            if( $this->cart->hasShipping()) {
                $this->request->get['type'] = 'shipping';
            }
            $data = $this->fc_session['guest']['shipping'];
        }
        $this->_addressForm($this->request->get['type'], $data);
    }

    public function address()
    {
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //Validate address entries
        $post = $this->request->post;
        if ($this->request->is_POST() && $this->action != 'login') {
            $this->loadModel('account/address');
            $this->error = $this->model_account_address->validateAddressData($post);
            $this->_validateEmailTelephone($post);
            if ($this->error && $this->error['warning']) {
                //we have errors
                $this->error['message'] = $this->error['warning'];
                $this->_addressForm($this->request->get['type'], $post);
                return;
            }
        } else {
            $this->_addressForm($this->request->get['type'], $post);
            return;
        }

        //all good save guest address
        $sessionGuest =& $this->session->data['fc']['guest'];
        $sessionGuest = $sessionGuest ? : [];
        //is this first or payment address?
        if ($this->request->get['type'] == 'payment'
            || !$this->fc_session['guest']
            || $this->config->get('fast_checkout_payment_address_equal_shipping')
        ) {
            //do not clear if we have guest data and edit
            $sessionGuest['firstname'] = $post['firstname'];
            $sessionGuest['lastname'] = $post['lastname'];
            $sessionGuest['email'] = $post['email'];
            $sessionGuest['company'] = '';
            $sessionGuest['telephone'] = $post['telephone'];
            $sessionGuest['address_1'] = $post['address_1'];
            $sessionGuest['address_2'] = $post['address_2'];
            $sessionGuest['zone_id'] = $post['zone_id'];
            $sessionGuest['postcode'] = $post['postcode'];
            $sessionGuest['city'] = $post['city'];
            $sessionGuest['country_id'] = $post['country_id'];

            if ($this->config->get('config_customer_tax')) {
                $this->tax->setZone($post['country_id'], $post['zone_id']);
            }

            $this->loadModel('localisation/country');
            $country_info = $this->model_localisation_country->getCountry($post['country_id']);
            if ($country_info) {
                $sessionGuest['country'] = $country_info['name'];
                $sessionGuest['iso_code_2'] = $country_info['iso_code_2'];
                $sessionGuest['iso_code_3'] = $country_info['iso_code_3'];
                $sessionGuest['address_format'] = $country_info['address_format'];
            } else {
                $sessionGuest['country'] = '';
                $sessionGuest['iso_code_2'] = '';
                $sessionGuest['iso_code_3'] = '';
                $sessionGuest['address_format'] = '';
            }

            $this->loadModel('localisation/zone');
            $zone_info = $this->model_localisation_zone->getZone($post['zone_id']);
            if ($zone_info) {
                $sessionGuest['zone'] = $zone_info['name'];
                $sessionGuest['zone_code'] = $zone_info['code'];
            } else {
                $sessionGuest['zone'] = '';
                $sessionGuest['zone_code'] = '';
            }
        }

        if (
            $this->request->get['type'] == 'shipping'
            || isset($post['same_as_shipping'])
            || $this->config->get('fast_checkout_payment_address_equal_shipping')
        ) {
            $sessionGuest['email'] = $post['email'];
            $sessionGuest['telephone'] = $post['telephone'];
            $sessionGuest['shipping']['company'] = '';
            $sessionGuest['shipping']['firstname'] = $post['firstname'];
            $sessionGuest['shipping']['lastname'] = $post['lastname'];
            $sessionGuest['shipping']['address_1'] = $post['address_1'];
            $sessionGuest['shipping']['address_2'] = $post['address_2'];
            $sessionGuest['shipping']['zone_id'] = $post['zone_id'];
            $sessionGuest['shipping']['postcode'] = $post['postcode'];
            $sessionGuest['shipping']['city'] = $post['city'];
            $sessionGuest['shipping']['country_id'] = $post['country_id'];

            $this->loadModel('localisation/country');
            $country_info = $this->model_localisation_country->getCountry($post['country_id']);
            if ($country_info) {
                $sessionGuest['shipping']['country'] = $country_info['name'];
                $sessionGuest['shipping']['iso_code_2'] = $country_info['iso_code_2'];
                $sessionGuest['shipping']['iso_code_3'] = $country_info['iso_code_3'];
                $sessionGuest['shipping']['address_format'] = $country_info['address_format'];
            } else {
                $sessionGuest['shipping']['country'] = '';
                $sessionGuest['shipping']['iso_code_2'] = '';
                $sessionGuest['shipping']['iso_code_3'] = '';
                $sessionGuest['shipping']['address_format'] = '';
            }

            $this->loadModel('localisation/zone');
            $zone_info = $this->model_localisation_zone->getZone($post['zone_id']);
            if ($zone_info) {
                $sessionGuest['shipping']['zone'] = $zone_info['name'];
                $sessionGuest['shipping']['zone_code'] = $zone_info['code'];
            } else {
                $sessionGuest['shipping']['zone'] = '';
                $sessionGuest['shipping']['zone_code'] = '';
            }

            if (!$this->config->get('config_customer_tax')) {
                $this->tax->setZone($post['country_id'], $post['zone_id']);
            }
        }
        $this->action = 'payment';
        $this->fc_session['guest'] = $sessionGuest;
        $this->main();
    }

    /**
     * @param string $type
     * @param array $data
     *
     * @throws AException
     */
    protected function _addressForm($type, $data)
    {
        $params = '&type='.$type;
        $this->loadLanguage('account/address');
        $form = new AForm();
        $form->setForm(
            [
                'form_name' => 'Address2Frm',
            ]
        );

        $this->data['form']['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'Address2Frm',
                'action' => $this->html->getSecureURL('r/checkout/pay/address', $params),
            ]
        );

        $this->data['form']['firstname'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'firstname',
                'value'    => $data['firstname'],
                'attr'     =>' minlength="1" maxlength="32" ',
                'required' => true,
            ]
        );
        $this->data['form']['lastname'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'lastname',
                'value'    => $data['lastname'],
                'attr'     =>' minlength="1" maxlength="32" ',
                'required' => true,
            ]
        );
        $this->data['form']['address_1'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'address_1',
                'value'    => $data['address_1'],
                'attr'     =>' minlength="3" maxlength="128" ',
                'required' => true,
            ]
        );
        $this->data['form']['address_2'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'address_2',
                'value'    => $data['address_2'],
                'attr'     =>' maxlength="128" ',
                'required' => false,
            ]
        );
        $this->data['form']['city'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'city',
                'value'    => $data['city'],
                'attr'     =>' minlength="3" maxlength="128" ',
                'required' => true,
            ]
        );
        $this->data['form']['zone'] = $form->getFieldHtml(
            [
                'type'     => 'selectbox',
                'name'     => 'zone_id',
                'required' => true,
            ]
        );
        $this->data['form']['postcode'] = $form->getFieldHtml(
            [
                'type'     => 'input',
                'name'     => 'postcode',
                'value'    => $data['postcode'],
                'attr'     =>' minlength="3" maxlength="10" ',
                'required' => true
            ]
        );

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
                'value'    => ($data['country_id'] ?? $this->config->get('config_country_id')),
                'required' => true,
            ]
        );
        $this->data['form']['continue'] = $form->getFieldHtml(
            [
                'type' => 'submit',
                'name' => $this->language->get('button_continue'),
            ]
        );

        $this->data['zone_id'] = $data['zone_id'] ?? 'false';

        $this->loadModel('localisation/country');
        $this->data['countries'] = $this->model_localisation_country->getCountries();

        $this->data['customer_email'] = $data['email'] ? : $this->fc_session['guest']['email'];
        $this->data['customer_telephone'] = $data['telephone'] ? : $this->fc_session['guest']['telephone'];

        //login form portion
        $this->addLoginForm($data, $params);

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
            return;
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);

        $this->view->batchAssign($this->data);
        $this->response->setOutput($this->view->fetch('responses/checkout/main.tpl'));
    }

    /**
     * @param string $loginname
     * @param string $password
     *
     * @return bool
     * @throws AException
     */
    protected function _validate_login($loginname, $password)
    {
        $this->loadLanguage('account/login');
        if (!$this->customer->login($loginname, $password)) {
            if ($this->config->get('config_customer_email_activation')) {
                //check if account is not confirmed in the email.
                $this->loadModel('account/customer');
                $customer_info = $this->model_account_customer->getCustomerByLogin($loginname);
                if ($customer_info
                    && !$customer_info['status']
                    && isset($customer_info['data']['email_activation'])
                    && $customer_info['data']['email_activation']
                ) {
                    //show link for resend activation code to email
                    $enc = new AEncryption($this->config->get('encryption_key'));
                    $rid = $enc->encrypt($customer_info['customer_id'].'::'.$customer_info['data']['email_activation']);
                    $this->error['login_message'] = sprintf(
                        $this->language->get('text_resend_activation_email'),
                        "\n"
                        .$this->html->getSecureURL('account/create/resend', '&rid='.$rid)
                    );
                }
            }
            return false;
        } else {
            unset(
                $this->session->data['guest'],
                $this->session->data['fc']['guest'],
            );
            $this->loadModel('account/address');
            $address = $this->model_account_address->getAddress($this->customer->getAddressId());
            $this->session->data['fc']['country_id'] = $this->session->data['country_id'] = $address['country_id'];
            $this->session->data['fc']['zone_id'] = $this->session->data['zone_id'] = $address['zone_id'];
            return true;
        }
    }

    /**
     * @return array
     * @throws AException
     */
    protected function _get_payment_methods()
    {
        $this->loadModel('checkout/extension');
        $this->loadModel('account/address');
        $method_data = [];
        $no_payment = false;

        // If total amount of order is zero - do redirect on confirmation page
        $total = $this->cart->buildTotalDisplay(true);

        $results = $this->model_checkout_extension->getExtensions('payment');

        $ac_payments = [];
        //#Check config of selected shipping method and see if we have accepted payments restriction
        $shipping_ext = explode('.', $this->fc_session['shipping_method']['id']);
        $ship_ext_config = $this->model_checkout_extension->getSettings($shipping_ext[0]);
        $accept_payment_ids = $ship_ext_config[$shipping_ext[0]."_accept_payments"];
        if ($this->fc_session['used_balance_full']) {
            $accept_payment_ids = [];
        }

        if (round($this->cart->getFinalTotal(),4) == 0) {
            $paymentHTML = $this->html->buildButton(
                [
                    'text'  => $this->language->get('order_confirm'),
                    'title' => $this->language->get('order_confirm'),
                    'id'    => 'no_payment_confirm',
                    'style' => 'btn btn-primary btn-lg btn-block',
                    'icon'  => 'fa fa-check',
                ]
            );
            $this->view->assign('payment_form', $paymentHTML);
            $no_payment = true;
        }
        elseif (is_array($accept_payment_ids) && count($accept_payment_ids)) {
            //#filter only allowed payment methods based on shipping
            foreach ($results as $result) {
                if (in_array($result['extension_id'], $accept_payment_ids)) {
                    $ac_payments[] = $result;
                }
            }
        }
        elseif ($this->fc_session['used_balance_full']) {
            $ac_payments = [];
            $paymentHTML = $this->html->buildButton(
                [
                    'text'  => $this->language->get('order_confirm'),
                    'title' => $this->language->get('order_confirm'),
                    'id'    => 'no_payment_confirm',
                    'style' => 'btn btn-primary btn-lg btn-block',
                    'icon'  => 'fa fa-check',
                ]
            );
            $this->view->assign('payment_form', $paymentHTML);
        }else {
            $ac_payments = $results;
        }

        $this->data['payment_address'] = $payment_address = $this->customer->isLogged()
            ? $this->model_account_address->getAddress(
                    $this->fc_session['payment_address_id']
                )
            : $this->fc_session['guest'];

        $psettings = [];
        foreach ($ac_payments as $result) {
            //#filter only allowed payment methods based on total min/max
            $pkey = $result['key'];
            $psettings[$pkey] = $this->model_checkout_extension->getSettings($pkey);
            $min = $psettings[$pkey][$pkey."_payment_minimum_total"] ?? null;
            $max = $psettings[$pkey][$pkey."_payment_maximum_total"] ?? null;
            $autoselect = $psettings[$pkey][$pkey."_autoselect"] ?? null;

            if ((has_value($min) && $total['total'] < $min)
                || (has_value($max) && $total['total'] > $max)
            ) {
                continue;
            }
            /** @var ModelExtensionDefaultCOD $mdl */
            $mdl = $this->loadModel('extension/'.$pkey);
            $method = $mdl->getMethod($payment_address);
            if ($method) {
                $method_data[$pkey] = $method;
                //# Add storefront icon if available
                $icon = $psettings[$pkey][$pkey."_payment_storefront_icon"];
                if (has_value($icon)) {
                    $icon_data = $this->model_checkout_extension->getSettingImage($icon);
                    $icon_data['image'] = is_numeric($icon)
                        ? $icon_data['type_dir'].'/'.$icon_data['resource_path']
                        : $icon;
                    $method_data[$pkey]['icon'] = $icon_data;
                }
                //check if this is a redirect type of the payment
                if ($psettings[$pkey][$pkey."_redirect_payment"]) {
                    $method_data[$pkey]['is_redirect_payment'] = true;
                }
                //if payment with autoselect presents in the list - set first as preselected
                if(!$this->data['payment_method'] && $autoselect){
                    $this->data['payment_method'] = $method['id'];
                }
            }
        }
        if($no_payment){
            $method_data = [
                'no_payment_required' => [
                    'id'         => 'no_payment_required',
                    'title'      => $this->language->get('no_payment_required'),
                    'sort_order' => 1,
                ]
            ];
        }
        // if no "autoselect" methods - set first as preselected
        if(!$this->data['payment_method']){
            reset($method_data);
            $this->data['payment_method'] = current($method_data)['id'];
        }

        $this->session->data['payment_methods'] = $method_data;
        $this->extensions->hk_ProcessData($this, 'fast_checkout_get_payments_list');

        return $this->session->data['payment_methods'];
    }

    protected function _select_shipping($selected = '')
    {
        //if shipping not required - skip
        if (!$this->cart->hasShipping()) {
            return;
        }
        $selected_shipping = [];
        if ($selected) {
            $selected_shipping = explode('.', $selected);
        }

        if (!isset($this->fc_session['shipping_methods'])
            || !$this->config->get('config_shipping_session')
        ) {
            $quote_data = [];
            $results = $this->model_checkout_extension->getExtensions('shipping');
            if ($this->fc_session['shipping_address_id']) {
                $this->loadModel('account/address');
                $shipping_address = $this->model_account_address->getAddress(
                    $this->fc_session['shipping_address_id']
                );
            } else {
                $shipping_address = $this->fc_session['guest']['shipping'] ?: $this->session->data['guest']['shipping'];
            }

            foreach ($results as $result) {
                $shpTxtId = $result['key'];
                /** @var ModelExtensionDefaultFlatRateShipping $mdl */
                $mdl = $this->loadModel('extension/'.$shpTxtId);
                $quote = $mdl->getQuote($shipping_address);

                if ($quote) {
                    $quote_data[$shpTxtId] = [
                        'title'      => $quote['title'],
                        'quote'      => $quote['quote'],
                        'sort_order' => $quote['sort_order'],
                        'error'      => $quote['error'],
                    ];
                    $shpSettings = $this->model_checkout_extension->getSettings($shpTxtId);
                    $icon = $shpSettings[$shpTxtId."_shipping_storefront_icon"];
                    if (has_value($icon)) {
                        $icon_data = $this->model_checkout_extension->getSettingImage($icon);
                        $icon_data['image'] = $icon;
                        $quote_data[$shpTxtId]['icon'] = $icon_data;
                    }
                }
            }

            $sort_order = [];
            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);
            $this->fc_session['shipping_methods'] = $quote_data;

            if ($this->fc_session['shipping_method']) {
                $shippingMethods = explode('.', $this->fc_session['shipping_method']['id']);
                $shippingMethod = $shippingMethods[0];
                if ($shippingMethod && $quote_data[$shippingMethod]) {
                    $this->fc_session['shipping_method'] = $quote_data[$shippingMethod]['quote'][$shippingMethod];
                } else {
                    unset($this->fc_session['shipping_method']);
                }
            }

            //if any error in shipping method, need to log
            $shipMethods = $this->fc_session['shipping_methods'] ?? [];
            if (count($shipMethods)) {
                $orderId = $this->request->get['order_id'] ?? $this->fc_session['order_id'];
                foreach ($shipMethods as $method) {
                    if ($method['error'] ?? '') {
                        $errText = "Error with shipping quote: ";
                        if($orderId){
                            $errText .= "OrderID: ".$this->request->get['order_id'].' ';
                        }
                        if($this->customer->isLogged()){
                            $errText .= "CustomerID: ".$this->customer->getId().' ';
                        }
                        $this->_to_log($errText.$method['error']);
                    }
                }
            } elseif (count($shipMethods) === 0 && $this->cart->hasShipping()) {
                $this->_to_log(
                    "No shipping option offered to the customer at checkout. "
                    ."Probably, need to check shipping setting!"
                );
            }
        }

        //# If only 1 shipping - set it as default
        if ($selected_shipping) {
            //user selected new shipping method
            $this->fc_session['shipping_method'] =
                $this->fc_session['shipping_methods'][$selected_shipping[0]]['quote'][$selected_shipping[1]];
        } else {
            //set only method
            $only_method = $this->fc_session['shipping_methods'];
            $autoSelects = [];
            foreach ($only_method as $shpKey => $value) {
                $as = $this->config->get($shpKey.'_autoselect');
                if($as) {
                    $autoSelects[$shpKey] = $as;
                }
            }
            if(count($autoSelects)){
                $currentKey = key($autoSelects);
                $this->fc_session['shipping_method'] = current($only_method[$currentKey]['quote']);
                if($this->fc_session['shipping_method']) {
                    $this->fc_session['shipping_method']['auto_selected'] = true;
                }
            }else{
                foreach ($only_method as $value) {
                    if (is_array($value['quote']) && sizeof($value['quote']) == 1) {
                        $this->fc_session['shipping_method'] = current($value['quote']);
                        if($this->fc_session['shipping_method']) {
                            $this->fc_session['shipping_method']['auto_selected'] = true;
                        }
                    }
                }
            }
            if(!$this->fc_session['shipping_method']){
                $currentKey = key($only_method);
                $this->fc_session['shipping_method'] = current((array)$only_method[$currentKey]['quote']);
                if($this->fc_session['shipping_method']) {
                    $this->fc_session['shipping_method']['auto_selected'] = true;
                }
            }
        }

        $this->data['shipping_methods'] = $this->fc_session['shipping_methods'] ?? [];
        $shipping = $this->fc_session['shipping_method']['id'];
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
                        $this->data['shipping_methods'][$k]['quote'][$key]['radio'] = $this->html->buildElement(
                            [
                                'type'    => 'radio',
                                'id'      => $val['id'],
                                'name'    => 'shipping_method',
                                'options' =>
                                    [
                                        $val['id'] => '<div class="checkbox_place"><i class="fa fa-check fa-2x"></i></div>',
                                    ],
                                'value'   => $selected,
                            ]
                        );
                    }
                }
            }
        } else {
            $this->data['shipping_methods'] = [];
        }
        $this->fc_session['shipping_methods'] = $this->data['shipping_methods'];
        if ($selected_shipping) {
            $this->updateOrCreateOrder($this->fc_session, ['shipping_method' => $selected]);
        }
    }

    /**
     * @param array $request
     *
     * @return bool
     * @throws AException
     */
    protected function _validate_order_details($request)
    {
        if (!$request['payment_address_id']
            && !$this->fc_session['guest']['address_1']
        ) {
            $this->error['message'] = $this->language->get('fast_checkout_error_payment_address');
            return false;
        }

        if ($this->cart->hasShipping()) {
            if (!$request['shipping_address_id']
                && !$this->fc_session['guest']['shipping']['address_1']
            ) {
                $this->error['message'] = $this->language->get('fast_checkout_error_shipping_address');
                return false;
            }
            if (!$request['shipping_method']) {
                $this->error['message'] = $this->language->get('fast_checkout_error_shipping_method');
                return false;
            }
        }

        if ($this->config->get('fast_checkout_require_phone_number') && !$request['telephone'])
        {
            $this->error['message'] = $this->language->get('fast_checkout_error_phone');
            return false;
        }

        if (!$request['email']) {
            $this->error['message'] = $this->language->get('fast_checkout_error_email');
            return false;
        }
        return true;
    }

    protected function _validateEmailTelephone($request)
    {
        $errors = [];
        if ($this->config->get('fast_checkout_require_phone_number') && !$request['telephone']) {
            $errors[] = $this->language->get('fast_checkout_error_phone');
        }

        if (!$request['email']) {
            $errors[] = $this->language->get('fast_checkout_error_email');
        }
        if ($errors) {
            $this->error['warning'] .= implode('<br>', $errors);
        }
    }

    protected function _handleCoupon($request)
    {
        //handle coupon
        if ($this->config->get('coupon_status') && $this->config->get('fast_checkout_allow_coupon')) {
            $this->data['enabled_coupon'] = true;
        }
        if ($this->data['enabled_coupon'] && $request['coupon_code']
            && $this->_validateCoupon($request['coupon_code'])
        ) {
            $this->fc_session['coupon']
                = $this->session->data['fc']['coupon'] = $request['coupon_code'];
        } else {
            if ($request['remove_coupon']) {
                unset(
                    $this->session->data['fc']['coupon'],
                    $this->fc_session['coupon']
                );
            }
        }
    }

    protected function _handleBalance($request)
    {
        if (!$this->customer->isLogged() || !$request['balance']) {
            return;
        }

        if ($request['balance'] == 'disapply' || $request['balance'] == 'reapply') {
            unset(
                $this->fc_session['used_balance'],
                $this->fc_session['balance'],
                $this->fc_session['used_balance_full'],
                $this->fc_session['payment_method'],
                $this->session->data['used_balance']
            );
        }
        if ($request['balance'] == 'apply' || $request['balance'] == 'reapply') {
            //get customer balance in general currency
            $balance = $this->customer->getBalance();

            $order_totals = $this->cart->buildTotalDisplay(true);
            $order_total = $order_totals['total'];
            if ($this->fc_session['used_balance']) {
                #check if we still have balance.
                if ($this->fc_session['used_balance'] <= $balance) {
                    $this->fc_session['used_balance_full'] = true;
                } else {
                    //if balance become less or 0 reapply partial
                    $this->fc_session['used_balance'] = $balance;
                    $this->fc_session['used_balance_full'] = false;
                }
            } else {
                if ($balance > 0) {
                    if ($balance >= $order_total) {
                        $this->fc_session['used_balance'] = $order_total;
                        $this->fc_session['used_balance_full'] = true;
                        $this->fc_session['payment_method'] = [
                            'id'    => 'no_payment_required',
                            'title' => $this->language->get('no_payment_required'),
                        ];
                    } else { //partial pay
                        $this->fc_session['used_balance'] = $balance;
                        $this->fc_session['used_balance_full'] = false;
                    }
                }
            }
            $this->session->data['used_balance'] = $this->fc_session['used_balance'];
            $this->updateOrCreateOrder($this->fc_session, $request);
            $this->cart->buildTotalDisplay(true);
        }
    }

    /**
     * @param string $coupon_code
     *
     * @return bool
     * @throws AException
     */
    protected function _validateCoupon($coupon_code)
    {
        $promotion = new APromotion();
        $coupon = $promotion->getCouponData($coupon_code);
        if (!$coupon) {
            $this->data['error'] = $this->error['message'] = $this->language->get('fast_checkout_error_coupon');
        }

        //validate post data
        $this->extensions->hk_ValidateData($this, [ 'coupon_code' => $coupon_code ]);

        return (!$this->error);
    }

    /**
     * @param string $message
     *
     * @return bool
     * @throws AException
     */
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

    /**
     * @param string $error_text
     *
     * @return string
     * @throws AException
     */
    protected function _build_error($error_text = '')
    {
        $this->data['error'] = $error_text;
        $this->view->batchAssign($this->data);
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
        return $this->view->fetch('responses/checkout/error.tpl');
    }

    public function changeCheckBox()
    {
        $fieldName = $this->request->post['fieldName'];
        $isOn = $this->request->post['isOn'];
        if (!$fieldName) {
            return;
        }
        $this->session->data['fc']['additional'][$fieldName] = ($isOn === 'true');
    }

}
