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

/**
 * @noinspection PhpUndefinedClassInspection
 * @property ModelExtensionDefaultPPExpress $model_extension_default_pp_express
 */
class ControllerResponsesExtensionDefaultPPExpress extends AController
{

    public $data = array();

    public function main()
    {
        $this->loadLanguage('default_pp_express/default_pp_express');

        if ($this->request->get['rt'] == 'checkout/guest_step_3') {
            $back_url = $this->html->getSecureURL('checkout/guest_step_2', '&mode=edit', true);
        } else {
            $back_url = $this->html->getSecureURL('checkout/payment', '&mode=edit', true);
        }

        $this->data['button_back'] = $this->html->buildElement(
            array(
                'type' => 'button',
                'name' => 'back',
                'text' => $this->language->get('button_back'),
                'href' => $back_url,
            ));

        $this->data['button_confirm'] = $this->html->buildElement(
            array(
                'type'  => 'submit',
                'name'  => $this->language->get('button_confirm'),
                'style' => 'button',
                'href'  => $this->html->getSecureURL('r/extension/default_pp_express/confirm',
                    '&csrfinstance='.$this->csrftoken->setInstance()
                    .'&csrftoken='.$this->csrftoken->setToken()),
            ));

        $this->view->batchAssign($this->data);
        $this->processTemplate('responses/default_pp_express.tpl');
    }

    public function confirm()
    {
        if (!$this->csrftoken->isTokenValid()) {
            exit('Forbidden: invalid csrf-token');
        }

        $this->loadLanguage('default_pp_express/default_pp_express');

        if (has_value($this->session->data['pp_express_checkout_error'])) {
            unset($this->session->data['pp_express_checkout_error']);
        }
        //in case usual checkout with pp_exp
        if (!has_value($this->session->data['pp_express_checkout']['token']) || !has_value($this->session->data['pp_express_checkout']['PayerID'])) {
            $this->set_pp();
        }
        if (!has_value($this->session->data['pp_express_checkout']['token']) || !has_value($this->session->data['pp_express_checkout']['PayerID'])) {
            $this->session->data['pp_express_checkout_error'] = $this->language->get('service_error');
            redirect($this->html->getSecureURL('extension/default_pp_express/error'));
        }

        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        if (!$this->config->get('default_pp_express_test')) {
            $api_endpoint = 'https://api-3t.paypal.com/nvp';
        } else {
            $api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
        }

        if (!$this->config->get('default_pp_express_transaction')) {
            $paymentaction = 'authorization';
        } else {
            $paymentaction = 'sale';
        }

        $language = $this->language->getCurrentLanguage();
        $locale = explode(',', $language['locale']);

        $payment_data = array(
            'METHOD'                         => 'DoExpressCheckoutPayment',
            'VERSION'                        => '98.0',
            'USER'                           => html_entity_decode($this->config->get('default_pp_express_username'), ENT_QUOTES, 'UTF-8'),
            'PWD'                            => html_entity_decode($this->config->get('default_pp_express_password'), ENT_QUOTES, 'UTF-8'),
            'SIGNATURE'                      => html_entity_decode($this->config->get('default_pp_express_signature'), ENT_QUOTES, 'UTF-8'),
            'PAYMENTREQUEST_0_PAYMENTACTION' => $paymentaction,
            'PAYMENTREQUEST_0_AMT'           => $this->currency->format($order_info['total'], $order_info['currency'], $order_info['value'], false),
            'PAYMENTREQUEST_0_CURRENCYCODE'  => $order_info['currency'],
            'BUTTONSOURCE'                   => 'Abante_Cart',
            'TOKEN'                          => $this->session->data['pp_express_checkout']['token'],
            'PAYERID'                        => $this->session->data['pp_express_checkout']['PayerID'],
            'LOCALECODE'                     => $locale[1],
        );
        ADebug::variable('Paypal Express Debug Log sent confirm:', var_export($payment_data, true));

        $curl = curl_init($api_endpoint);

        curl_setopt($curl, CURLOPT_PORT, 443);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

        $response = curl_exec($curl);

        curl_close($curl);

        $ec_details = $this->_parse_http_query($response);

        ADebug::variable('Paypal Express Debug Log Received_confirm:', var_export($ec_details, true));
        #$this->log->write( var_export($ec_details, true) );

        if ($ec_details['ACK'] != 'Success') {
            $warning = new AWarning('PayPal Express Checkout Error: '.$ec_details['L_LONGMESSAGE0'].'. Test mode = '.$this->config->get('default_pp_express_test').'.');
            $warning->toLog()->toDebug();
            $this->session->data['pp_express_checkout_error'] = $this->language->get('service_error');
            redirect($this->html->getSecureURL('extension/default_pp_express/error'));
        } else {
            if ($ec_details['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed') {
                $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('default_pp_express_order_status_id'));
            } else {
                //set order to Pending status
                $this->model_checkout_order->confirm($this->session->data['order_id'], $this->order_status->getStatusByTextId('pending'));
            }

            $this->model_checkout_order->updatePaymentMethodData($this->session->data['order_id'], $ec_details);

            unset($this->session->data['pp_express_checkout']);
            redirect($this->html->getSecureURL('checkout/success'));

        }

    }

    public function set_pp()
    {
        $this->loadLanguage('default_pp_express/default_pp_express');
        if ($this->cart->hasProducts() && $this->cart->hasStock() && ($amount = $this->cart->getFinalTotal())) {

            //do not allow redirecting to paypal side for non-logged users when guest-checkout is disabled
            if (!$this->config->get('config_guest_checkout') && !$this->customer->isLogged()) {
                $this->session->data['redirect'] = $this->html->getCatalogURL('r/extension/default_pp_express/set_pp');
                redirect($this->html->getSecureURL('account/login'));
                return null;
            }

            if (!$this->config->get('default_pp_express_test')) {
                $api_endpoint = 'https://api-3t.paypal.com/nvp';
            } else {
                $api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
            }

            if (!$this->config->get('default_pp_express_transaction')) {
                $paymentaction = 'authorization';
            } else {
                $paymentaction = 'sale';
            }

            $products_data = $this->_get_products_data(array(
                'currency' => $this->session->data['currency'],
                'value'    => '',
            ));

            $language = $this->language->getCurrentLanguage();
            $locale = explode(',', $language['locale']);
            $order_total = (float)$this->currency->format_number($amount, $this->session->data['currency']);
            $payment_data = array(
                'METHOD'                         => 'SetExpressCheckout',
                'VERSION'                        => '98.0',
                'USER'                           => html_entity_decode($this->config->get('default_pp_express_username'), ENT_QUOTES, 'UTF-8'),
                'PWD'                            => html_entity_decode($this->config->get('default_pp_express_password'), ENT_QUOTES, 'UTF-8'),
                'SIGNATURE'                      => html_entity_decode($this->config->get('default_pp_express_signature'), ENT_QUOTES, 'UTF-8'),
                'PAYMENTREQUEST_0_PAYMENTACTION' => $paymentaction,
                'PAYMENTREQUEST_0_AMT'           => $order_total,
                'PAYMENTREQUEST_0_CURRENCYCODE'  => $this->session->data['currency'],
                'RETURNURL'                      => $this->html->getSecureURL('r/extension/default_pp_express/callback', ($this->request->get['to_confirm'] ? '&to_confirm=1' : '')),
                'CANCELURL'                      => (has_value($this->request->get['redirect_to']) ? $this->request->get['redirect_to'] : $this->request->server['HTTP_REFERER']),
                'LOCALECODE'                     => $locale[1],
            );
            $skip_item_list = false;
            if (($this->data['items_total'] - $order_total) !== 0.0) {
                $payment_data['L_PAYMENTREQUEST_0_ITEMAMT'] = $order_total;
                $skip_item_list = true;
            }
            if (!$skip_item_list) {
                foreach ($products_data as $key => $product) {
                    $payment_data['L_PAYMENTREQUEST_0_NAME'.$key] = $product['name'];
                    $payment_data['L_PAYMENTREQUEST_0_AMT'.$key] = (float)$product['price'];
                    $payment_data['L_PAYMENTREQUEST_0_NUMBER'.$key] = $product['model'];
                    $payment_data['L_PAYMENTREQUEST_0_QTY'.$key] = $product['quantity'];
                    $payment_data['L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE'.$key] = $product['weight'];
                    $payment_data['L_PAYMENTREQUEST_0_ITEMWEGHTUNIT'.$key] = $product['weight_type'];
                }
            } else {
                $payment_data['L_PAYMENTREQUEST_0_NAME0'] = $this->language->get('text_order_total_amount');
                $payment_data['L_PAYMENTREQUEST_0_AMT0'] = $order_total;
                $payment_data['L_PAYMENTREQUEST_0_NUMBER0'] = '';
                $payment_data['L_PAYMENTREQUEST_0_QTY0'] = 1;
                $payment_data['L_PAYMENTREQUEST_0_ITEMWEIGHTVALUE0'] = '';
                $payment_data['L_PAYMENTREQUEST_0_ITEMWEGHTUNIT0'] = '';
            }

            if ($this->config->get('default_pp_express_credit_cards')) {
                $payment_data['SOLUTIONTYPE'] = 'Sole';
                $payment_data['LANDINGPAGE'] = 'Billing';
            }

            if ($this->config->get('default_pp_express_billmelater') && has_value($this->request->get['fundsource']) && strtolower($this->request->get['fundsource']) == 'bml') {
                $payment_data['SOLUTIONTYPE'] = 'Sole';
                $payment_data['LANDINGPAGE'] = 'Billing';
                $payment_data['USERSELECTEDFUNDINGSOURCE'] = 'BML';
            }

            if (has_value($this->config->get('default_pp_express_custom_logo'))) {
                $payment_data['LOGOIMG'] = HTTPS_SERVER.'resources/'.$this->config->get('default_pp_express_custom_logo');
            }

            if (has_value($this->config->get('default_pp_express_custom_bg_color'))) {
                $payment_data['CARTBORDERCOLOR'] = ltrim($this->config->get('default_pp_express_custom_bg_color'), '#');
            }

            ADebug::variable('Paypal Express Debug Log Sent setpp:', var_export($payment_data, true));
            $curl = curl_init($api_endpoint);

            curl_setopt($curl, CURLOPT_PORT, 443);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

            $response = curl_exec($curl);

            curl_close($curl);

            $ec_settings = $this->_parse_http_query($response);
            ADebug::variable('Paypal Express Debug Log Received setpp:', var_export($ec_settings, true));

            if (isset($ec_settings['TOKEN'])) {
                if (!$this->config->get('default_pp_express_test')) {
                    redirect('https://www.paypal.com/webscr?cmd=_express-checkout&token='.urlencode($ec_settings['TOKEN']));// . '&useraction=commit');
                } else {
                    redirect('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token='.urlencode($ec_settings['TOKEN']));// . '&useraction=commit');
                }
            } else {

                $warning = new AWarning('PayPal Express Checkout Error: '.$ec_settings['L_LONGMESSAGE0'].'. Test mode = '.$this->config->get('default_pp_express_test').'.');
                $warning->toLog()->toDebug();
                $this->session->data['pp_express_checkout_error'] = $this->language->get('service_error');
                redirect($this->html->getSecureURL('extension/default_pp_express/error'));
            }

        } else {
            redirect($this->html->getSecureURL('checkout/cart'));
        }

    }

    public function callback()
    {

        if (has_value($this->request->get['token']) && has_value($this->request->get['PayerID'])) {
            $session =& $this->session->data;
            $this->loadLanguage('default_pp_express/default_pp_express');
            $session['pp_express_checkout']['token'] = $this->request->get['token'];
            $session['pp_express_checkout']['PayerID'] = $this->request->get['PayerID'];
            $session['pp_express_checkout']['currency'] = $this->currency->getCode();

            $session['payment_method'] = array(
                'id'         => 'default_pp_express',
                'title'      => $this->language->get('text_title'),
                'sort_order' => $this->config->get('default_pp_express_sort_order'),
            );

            if (!$this->config->get('default_pp_express_test')) {
                $api_endpoint = 'https://api-3t.paypal.com/nvp';
            } else {
                $api_endpoint = 'https://api-3t.sandbox.paypal.com/nvp';
            }

            $payment_data = array(
                'METHOD'    => 'GetExpressCheckoutDetails',
                'VERSION'   => '98.0',
                'USER'      => html_entity_decode($this->config->get('default_pp_express_username'), ENT_QUOTES, 'UTF-8'),
                'PWD'       => html_entity_decode($this->config->get('default_pp_express_password'), ENT_QUOTES, 'UTF-8'),
                'SIGNATURE' => html_entity_decode($this->config->get('default_pp_express_signature'), ENT_QUOTES, 'UTF-8'),
                'TOKEN'     => $session['pp_express_checkout']['token'],
            );
            ADebug::variable('Paypal Express Debug Log sent callback:', var_export($payment_data, true));

            $curl = curl_init($api_endpoint);

            curl_setopt($curl, CURLOPT_PORT, 443);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($payment_data));

            $response = curl_exec($curl);

            curl_close($curl);

            $ec_details = $this->_parse_http_query($response);

            ADebug::variable('Paypal Express Debug Log Received callback:', var_export($ec_details, true));

            if ($ec_details['SHIPTONAME']) {
                list($shp_first_name, $shp_last_name) = explode(' ', $ec_details['SHIPTONAME']);
                $new_shipping_address = true;
            } else {
                $shp_first_name = $ec_details['FIRSTNAME'];
                $shp_last_name = $ec_details['LASTNAME'];
                $new_shipping_address = false;
            }
            $this->loadModel('extension/default_pp_express');

            if ($this->customer->isLogged()) {

                $country_id = $this->model_extension_default_pp_express->getCountryIdByCode2($ec_details['SHIPTOCOUNTRYCODE']);
                if (mb_strlen($ec_details['SHIPTOSTATE']) == 2) {
                    $zone_id = $this->model_extension_default_pp_express->getZoneId($country_id, $ec_details['SHIPTOSTATE']);
                } else {
                    $zone_id = $this->model_extension_default_pp_express->getZoneIdByName($country_id, $ec_details['SHIPTOSTATE']);
                }

                $this->tax->setZone($country_id, $zone_id);

                $pp_shipping_data = array(
                    'firstname'  => $shp_first_name,
                    'lastname'   => $shp_last_name,
                    'address_1'  => $ec_details['SHIPTOSTREET'],
                    'address_2'  => has_value($ec_details['SHIPTOSTREET2']) ? $ec_details['SHIPTOSTREET2'] : '',
                    'city'       => $ec_details['SHIPTOCITY'],
                    'zone_code'  => $ec_details['SHIPTOSTATE'],
                    'zone_id'    => $zone_id,
                    'iso_code_2' => $ec_details['SHIPTOCOUNTRYCODE'],
                    'country'    => $ec_details['SHIPTOCOUNTRYNAME'],
                    'country_id' => $country_id,
                    'postcode'   => $ec_details['SHIPTOZIP'],
                );

                $this->loadModel('account/address');
                $addresses = $this->model_account_address->getAddresses();

                if (has_value($addresses)) {

                    $pp_str = strtolower(str_replace(' ', '', implode('', $pp_shipping_data)));

                    foreach ($addresses as $addr) {
                        $check_arr = array(
                            'firstname'  => $addr['firstname'],
                            'lastname'   => $addr['lastname'],
                            'address_1'  => $addr['address_1'],
                            'address_2'  => $addr['address_2'],
                            'city'       => $addr['city'],
                            'zone_code'  => $addr['zone_code'],
                            'iso_code_2' => $addr['iso_code_2'],
                            'country'    => $addr['country'],
                            'postcode'   => $addr['postcode'],
                        );

                        $check_str = strtolower(str_replace(' ', '', implode('', $check_arr)));
                        if ($pp_str == $check_str) {
                            $session['shipping_address_id'] = $addr['address_id'];
                            break;
                        }

                    }
                }

                if ($new_shipping_address) {
                    $session['shipping_address_id'] = $this->model_extension_default_pp_express->addShippingAddress($pp_shipping_data);

                    $this->loadModel('checkout/extension');
                    if (!isset($session['shipping_methods']) || !$this->config->get('config_shipping_session')) {
                        $quote_data = array();
                        $results = $this->model_checkout_extension->getExtensions('shipping');
                        foreach ($results as $result) {
                            $this->loadModel('extension/'.$result['key']);
                            /** @noinspection PhpUndefinedMethodInspection */
                            $quote = $this->{'model_extension_'.$result['key']}->getQuote($pp_shipping_data);
                            if ($quote) {
                                $quote_data[$result['key']] = array(
                                    'title'      => $quote['title'],
                                    'quote'      => $quote['quote'],
                                    'sort_order' => $quote['sort_order'],
                                    'error'      => $quote['error'],
                                );
                            }
                        }

                        $sort_order = array();
                        foreach ($quote_data as $key => $value) {
                            $sort_order[$key] = $value['sort_order'];
                        }

                        array_multisort($sort_order, SORT_ASC, $quote_data);
                        $session['shipping_methods'] = $quote_data;
                    }

                    //# If only 1 shipping and it is set to be defaulted
                    if (count($session['shipping_methods']) == 1) {
                        //set only method
                        $only_method = $session['shipping_methods'];
                        foreach ($only_method as $key => $value) {
                            $method_name = $key;
                            #Check config if we allowed to set this shipping and skip the step
                            $ext_config = $this->model_checkout_extension->getSettings($method_name);
                            $autoselect = $ext_config[$method_name."_autoselect"];
                            if ($autoselect) {
                                if (sizeof($only_method[$method_name]['quote']) == 1) {
                                    $session['shipping_method'] = current($only_method[$method_name]['quote']);
                                    break;
                                }
                            }
                        }
                    }

                }

                if (!$session['payment_address_id']) {
                    $session['payment_address_id'] = $session['shipping_address_id'];
                }
                redirect($this->html->getSecureURL('checkout/confirm'));
            } else {

                $country_id = $this->model_extension_default_pp_express->getCountryIdByCode2($ec_details['SHIPTOCOUNTRYCODE']);
                $this->loadModel('localisation/country');
                $country = $this->model_localisation_country->getCountry($country_id);
                $country = $country['name'];

                if (mb_strlen($ec_details['SHIPTOSTATE']) == 2) {
                    $zone_id = $this->model_extension_default_pp_express->getZoneId($country_id, $ec_details['SHIPTOSTATE']);
                } else {
                    $zone_id = $this->model_extension_default_pp_express->getZoneIdByName($country_id, $ec_details['SHIPTOSTATE']);
                }

                //leave payment address
                $session_guest =& $session['guest'];
                $session_guest['firstname'] = $session_guest['firstname'] ? $session_guest['firstname'] : $ec_details['FIRSTNAME'];
                $session_guest['lastname'] = $session_guest['lastname'] ? $session_guest['lastname'] : $ec_details['LASTNAME'];
                $session_guest['email'] = $session_guest['email'] ? $session_guest['email'] : $ec_details['EMAIL'];
                $session_guest['address_1'] = $session_guest['address_1'] ? $session_guest['address_1'] : $ec_details['SHIPTOSTREET'];
                $session_guest['address_2'] = $session_guest['address_2']
                    ? $session_guest['address_2']
                    : (has_value($ec_details['SHIPTOSTREET2']) ? $ec_details['SHIPTOSTREET2'] : '');
                $session_guest['postcode'] = $session_guest['postcode'] ? $session_guest['postcode'] : $ec_details['SHIPTOZIP'];
                $session_guest['city'] = $session_guest['city'] ? $session_guest['city'] : $ec_details['SHIPTOCITY'];
                $session_guest['country'] = $session_guest['country'] ? $session_guest['country'] : $country;
                $session_guest['country_id'] = $session_guest['country_id'] ? $session_guest['country_id'] : $country_id;
                $session_guest['zone'] = $session_guest['zone'] ? $session_guest['zone'] : $ec_details['SHIPTOSTATE'];
                $session_guest['zone_id'] = $session_guest['zone_id'] ? $session_guest['zone_id'] : $zone_id;

                $session_guest['shipping'] = array(
                    'firstname'  => $shp_first_name,
                    'lastname'   => $shp_last_name,
                    'email'      => $ec_details['EMAIL'],
                    'address_1'  => $ec_details['SHIPTOSTREET'],
                    'address_2'  => has_value($ec_details['SHIPTOSTREET2']) ? $ec_details['SHIPTOSTREET2'] : '',
                    'postcode'   => $ec_details['SHIPTOZIP'],
                    'city'       => $ec_details['SHIPTOCITY'],
                    'country'    => $country,
                    'country_id' => $country_id,
                    'zone'       => $ec_details['SHIPTOSTATE'],
                    'zone_id'    => $zone_id,
                    'iso_code_2' => $ec_details['SHIPTOCOUNTRYCODE'],
                );

                $this->tax->setZone($country_id, $zone_id);

                if ($this->request->get['to_confirm'] == 1) {
                    redirect($this->html->getSecureURL('checkout/guest_step_3'));
                } else {
                    redirect($this->html->getSecureURL('checkout/guest_step_2'));
                }
            }

        }
    }

    private function _get_products_data($order_info)
    {

        $this->load->library('encryption');
        $encryption = new AEncryption($this->config->get('encryption_key'));

        $this->data['products'] = array();
        $this->data['items_total'] = 0.0;
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
                    'name'  => html_entity_decode($option['name'], ENT_QUOTES,'UTF-8'),
                    'value' => (mb_strlen($value) > 20 ? mb_substr($value, 0, 20).'..' : $value),
                );
            }
            $price = $this->currency->format($product['price'], $order_info['currency'], $order_info['value'], false);
            $this->data['products'][] = array(
                'name'        => html_entity_decode($product['name'], ENT_QUOTES,'UTF-8'),
                'model'       => html_entity_decode($product['model'], ENT_QUOTES,'UTF-8'),
                'price'       => $price,
                'quantity'    => $product['quantity'],
                'option'      => $option_data,
                'weight'      => $product['weight'],
                'weight_type' => $product['weight_type'],
            );
            $this->data['items_total'] += $price * $product['quantity'];
        }

        $this->data['discount_amount_cart'] = 0;
        $totals = $this->cart->buildTotalDisplay();

        foreach ($totals['total_data'] as $total) {
            if (in_array($total['id'], array('subtotal', 'total'))) {
                continue;
            }
            if (in_array($total['id'], array('promotion', 'coupon'))) {
                $total['value'] = $total['value'] < 0 ? $total['value'] * -1 : $total['value'];
                $this->data['discount_amount_cart'] += $total['value'];
            } else {
                $price = $this->currency->format($total['value'], $order_info['currency'], $order_info['value'], false);
                $this->data['products'][] = array(
                    'name'     => html_entity_decode($total['title'], ENT_QUOTES,'UTF-8'),
                    'model'    => '',
                    'price'    => $price,
                    'quantity' => 1,
                    'option'   => array(),
                    'weight'   => 0,
                );
                $this->data['items_total'] += $price;
            }
        }

        if ($this->data['discount_amount_cart'] > 0) {
            $price = -1 * $this->currency->format($this->data['discount_amount_cart'], $order_info['currency'], $order_info['value'], false);
            $this->data['products'][] = array(
                'name'     => $this->language->get('text_discount'),
                'model'    => '',
                'price'    => $price,
                'quantity' => 1,
                'option'   => array(),
                'weight'   => 0,
            );
            $this->data['items_total'] += $price;
        }

        return $this->data['products'];
    }

    private function _parse_http_query($query)
    {

        $parts = explode('&', $query);

        $results = array();
        foreach ($parts as $part) {
            $item = explode('=', $part);
            $results[$item[0]] = urldecode($item[1]);
        }

        return $results;
    }
}
