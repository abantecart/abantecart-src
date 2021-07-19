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
 * Class AOrder
 *
 * @property ACart $cart
 * @property AConfig $config
 * @property ATax $tax
 * @property ACurrency $currency
 * @property ARequest $request
 * @property ALoader $load
 * @property ASession $session
 * @property ExtensionsAPI $extensions
 * @property ModelAccountOrder $model_account_order
 * @property ModelAccountAddress $model_account_address
 * @property ModelCheckoutExtension $model_checkout_extension
 * @property ModelCheckoutOrder $model_checkout_order
 * @property AIM $im
 *
 */
class AOrder
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var int
     */
    protected $customer_id;
    /**
     * @var int
     */
    protected $order_id;
    protected $customer;
    protected $order_data;
    /**
     * @var array public property. needs to use inside hooks
     */
    public $data = [];

    /**
     * AOrder constructor.
     *
     * @param Registry $registry
     * @param string $order_id
     *
     * @throws AException
     */
    public function __construct($registry, $order_id = '')
    {
        $this->registry = $registry;

        $this->load->model('checkout/order', 'storefront');
        $this->load->model('account/order', 'storefront');

        //if nothing is passed use session array. Customer session, can function on storefront only
        if (!has_value($order_id)) {
            $this->order_id = (int) $this->session->data['order_id'];
        } else {
            $this->order_id = (int) $order_id;
        }

        if (is_object($this->registry->get('customer'))) {
            $this->customer = $this->registry->get('customer');
            $this->customer_id = $this->customer->getId();
        } else {
            $this->customer = new ACustomer($registry);
        }
    }

    public function __get($key)
    {
        return $this->registry->get($key);
    }

    public function __set($key, $value)
    {
        $this->registry->set($key, $value);
    }

    /**
     * @param int $order_id
     * @param string $order_status_id
     *
     * @return array
     * @throws AException
     */
    public function loadOrderData($order_id, $order_status_id = '')
    {
        if ($order_id) {
            $this->order_id = $order_id;
        }
        //get order details for specific status. NOTE: Customer ID need to be set in customer class
        $this->order_data = $this->model_account_order->getOrder($this->order_id, $order_status_id);
        $this->extensions->hk_ProcessData($this, 'load_order_data');
        return (array) $this->data + (array) $this->order_data;
    }

    /**
     * @param array $indata : Session data array
     *
     * @return array
     * NOTE: method to create an order based on provided data array.
     * @throws AException
     */
    public function buildOrderData($indata)
    {
        $order_info = [];
        if (empty($indata)) {
            return [];
        }

        $total_data = [];
        $total = 0;
        $taxes = $this->cart->getTaxes();

        $this->load->model('checkout/extension');

        $results = $this->model_checkout_extension->getExtensions('total');
        $calculation_order = [];
        foreach ($results as $key => $value) {
            $calculation_order[$key] = $this->config->get($value['key'].'_calculation_order');
        }

        array_multisort($calculation_order, SORT_ASC, $results);

        foreach ($results as $result) {
            $this->load->model('total/'.$result['key']);
            $this->{'model_total_'.$result['key']}->getTotal($total_data, $total, $taxes, $indata);

            //allow to change total data on-the-fly for extensions, for example rounding of amount etc
            $this->data = [
                'total_key'  => $result['key'],
                'total_data' => $total_data,
                'total'      => $total,
                'taxes'      => $taxes,
            ];

            $this->extensions->hk_ProcessData($this, __FUNCTION__);

            $total_data = $this->data['total_data'];
            $total = $this->data['total'];
            $taxes = $this->data['taxes'];
            unset(
                $this->data['total_key'],
                $this->data['total_data'],
                $this->data['total'],
                $this->data['taxes']
            );
        }

        $sort_order = [];

        foreach ($total_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $total_data);

        $order_info['store_id'] = $this->config->get('config_store_id');
        $order_info['store_name'] = $this->config->get('store_name');
        $order_info['store_url'] = $this->config->get('config_url').$this->config->get('seo_prefix');
        //prepare data with customer details.
        if ($this->customer->getId()) {
            $order_info['customer_id'] = $this->customer->getId();
            $order_info['customer_group_id'] = $this->customer->getCustomerGroupId();
            $order_info['firstname'] = $this->customer->getFirstName();
            $order_info['lastname'] = $this->customer->getLastName();
            $order_info['email'] = $this->customer->getEmail();
            $order_info['telephone'] = $this->customer->getTelephone();
            $order_info['fax'] = $this->customer->getFax();

            $this->load->model('account/address');

            if ($this->cart->hasShipping()) {
                $ship_address_id = $indata['shipping_address_id'];

                $ship_address = $this->model_account_address->getAddress($ship_address_id);

                $order_info['shipping_firstname'] = $ship_address['firstname'];
                $order_info['shipping_lastname'] = $ship_address['lastname'];
                $order_info['shipping_company'] = $ship_address['company'];
                $order_info['shipping_address_1'] = $ship_address['address_1'];
                $order_info['shipping_address_2'] = $ship_address['address_2'];
                $order_info['shipping_city'] = $ship_address['city'];
                $order_info['shipping_postcode'] = $ship_address['postcode'];
                $order_info['shipping_zone'] = $ship_address['zone'];
                $order_info['shipping_zone_id'] = $ship_address['zone_id'];
                $order_info['shipping_country'] = $ship_address['country'];
                $order_info['shipping_country_id'] = $ship_address['country_id'];
                $order_info['shipping_address_format'] = $ship_address['address_format'];
            } else {
                $order_info['shipping_firstname'] = '';
                $order_info['shipping_lastname'] = '';
                $order_info['shipping_company'] = '';
                $order_info['shipping_address_1'] = '';
                $order_info['shipping_address_2'] = '';
                $order_info['shipping_city'] = '';
                $order_info['shipping_postcode'] = '';
                $order_info['shipping_zone'] = '';
                $order_info['shipping_zone_id'] = '';
                $order_info['shipping_country'] = '';
                $order_info['shipping_country_id'] = '';
                $order_info['shipping_address_format'] = '';
                $order_info['shipping_method'] = '';
            }

            $pay_address_id = $indata['payment_address_id'];

            $pay_address = $this->model_account_address->getAddress($pay_address_id);

            $order_info['payment_firstname'] = $pay_address['firstname'];
            $order_info['payment_lastname'] = $pay_address['lastname'];
            $order_info['payment_company'] = $pay_address['company'];
            $order_info['payment_address_1'] = $pay_address['address_1'];
            $order_info['payment_address_2'] = $pay_address['address_2'];
            $order_info['payment_city'] = $pay_address['city'];
            $order_info['payment_postcode'] = $pay_address['postcode'];
            $order_info['payment_zone'] = $pay_address['zone'];
            $order_info['payment_zone_id'] = $pay_address['zone_id'];
            $order_info['payment_country'] = $pay_address['country'];
            $order_info['payment_country_id'] = $pay_address['country_id'];
            $order_info['payment_address_format'] = $pay_address['address_format'];
        } else {
            if (isset($indata['guest'])) {
                //this is a guest order
                $order_info['customer_id'] = 0;
                $order_info['customer_group_id'] = $this->config->get('config_customer_group_id');
                $order_info['firstname'] = $indata['guest']['firstname'];
                $order_info['lastname'] = $indata['guest']['lastname'];
                $order_info['email'] = $indata['guest']['email'];
                $order_info['telephone'] = $indata['guest']['telephone'];
                $order_info['fax'] = $indata['guest']['fax'];

                //IM addresses
                $protocols = $this->im->getProtocols();
                foreach ($protocols as $protocol) {
                    if (has_value($indata['guest'][$protocol]) && !has_value($order_info[$protocol])) {
                        $order_info[$protocol] = $indata['guest'][$protocol];
                    }
                }

                if ($this->cart->hasShipping()) {
                    if (isset($indata['guest']['shipping'])) {
                        $order_info['shipping_firstname'] = $indata['guest']['shipping']['firstname'];
                        $order_info['shipping_lastname'] = $indata['guest']['shipping']['lastname'];
                        $order_info['shipping_company'] = $indata['guest']['shipping']['company'];
                        $order_info['shipping_address_1'] = $indata['guest']['shipping']['address_1'];
                        $order_info['shipping_address_2'] = $indata['guest']['shipping']['address_2'];
                        $order_info['shipping_city'] = $indata['guest']['shipping']['city'];
                        $order_info['shipping_postcode'] = $indata['guest']['shipping']['postcode'];
                        $order_info['shipping_zone'] = $indata['guest']['shipping']['zone'];
                        $order_info['shipping_zone_id'] = $indata['guest']['shipping']['zone_id'];
                        $order_info['shipping_country'] = $indata['guest']['shipping']['country'];
                        $order_info['shipping_country_id'] = $indata['guest']['shipping']['country_id'];
                        $order_info['shipping_address_format'] = $indata['guest']['shipping']['address_format'];
                    } else {
                        $order_info['shipping_firstname'] = $indata['guest']['firstname'];
                        $order_info['shipping_lastname'] = $indata['guest']['lastname'];
                        $order_info['shipping_company'] = $indata['guest']['company'];
                        $order_info['shipping_address_1'] = $indata['guest']['address_1'];
                        $order_info['shipping_address_2'] = $indata['guest']['address_2'];
                        $order_info['shipping_city'] = $indata['guest']['city'];
                        $order_info['shipping_postcode'] = $indata['guest']['postcode'];
                        $order_info['shipping_zone'] = $indata['guest']['zone'];
                        $order_info['shipping_zone_id'] = $indata['guest']['zone_id'];
                        $order_info['shipping_country'] = $indata['guest']['country'];
                        $order_info['shipping_country_id'] = $indata['guest']['country_id'];
                        $order_info['shipping_address_format'] = $indata['guest']['address_format'];
                    }
                } else {
                    $order_info['shipping_firstname'] = '';
                    $order_info['shipping_lastname'] = '';
                    $order_info['shipping_company'] = '';
                    $order_info['shipping_address_1'] = '';
                    $order_info['shipping_address_2'] = '';
                    $order_info['shipping_city'] = '';
                    $order_info['shipping_postcode'] = '';
                    $order_info['shipping_zone'] = '';
                    $order_info['shipping_zone_id'] = '';
                    $order_info['shipping_country'] = '';
                    $order_info['shipping_country_id'] = '';
                    $order_info['shipping_address_format'] = '';
                    $order_info['shipping_method'] = '';
                }

                $order_info['payment_firstname'] = $indata['guest']['firstname'];
                $order_info['payment_lastname'] = $indata['guest']['lastname'];
                $order_info['payment_company'] = $indata['guest']['company'];
                $order_info['payment_address_1'] = $indata['guest']['address_1'];
                $order_info['payment_address_2'] = $indata['guest']['address_2'];
                $order_info['payment_city'] = $indata['guest']['city'];
                $order_info['payment_postcode'] = $indata['guest']['postcode'];
                $order_info['payment_zone'] = $indata['guest']['zone'];
                $order_info['payment_zone_id'] = $indata['guest']['zone_id'];
                $order_info['payment_country'] = $indata['guest']['country'];
                $order_info['payment_country_id'] = $indata['guest']['country_id'];
                $order_info['payment_address_format'] = $indata['guest']['address_format'];
            } else {
                return [];
            }
        }

        if (isset($indata['shipping_method']['title'])) {
            $order_info['shipping_method'] = $indata['shipping_method']['title'];
            // note - id by mask method_txt_id.method_option_id. for ex. default_weight.default_weight_1
            $order_info['shipping_method_key'] = $indata['shipping_method']['id'];
        } else {
            $order_info['shipping_method'] = '';
            $order_info['shipping_method_key'] = '';
        }

        if (isset($indata['payment_method']['title'])) {
            $order_info['payment_method'] = $indata['payment_method']['title'];
            preg_match('/^([^.]+)/', $indata['payment_method']['id'], $matches);
            $order_info['payment_method_key'] = $matches[1];
        } else {
            $order_info['payment_method'] = '';
        }

        $product_data = [];

        foreach ($this->cart->getProducts() + $this->cart->getVirtualProducts() as $key => $product) {
            $product_data[] = [
                'key'        => $key,
                'product_id' => $product['product_id'],
                'name'       => $product['name'],
                'model'      => $product['model'],
                'sku'        => $product['sku'],
                'option'     => $product['option'],
                'download'   => $product['download'],
                'quantity'   => $product['quantity'],
                //ternary for virtual products
                'price'      => $product['amount'] ?: $product['price'],
                'cost'       => $product['cost'],
                'total'      => $product['amount'] ? ($product['amount']*$product['quantity']) : $product['total'],
                'tax'        => $this->tax->calcTotalTaxAmount($product['total'], $product['tax_class_id']),
                'stock'      => $product['stock'],
            ];
        }
        $order_info['products'] = $product_data;
        $order_info['totals'] = $total_data;
        $order_info['comment'] = $indata['comment'];
        $order_info['total'] = $total;
        $order_info['language_id'] = $this->config->get('storefront_language_id');
        $order_info['currency_id'] = $this->currency->getId();
        $order_info['currency'] = $this->currency->getCode();
        $order_info['value'] = $this->currency->getValue($this->currency->getCode());

        if (isset($indata['coupon'])) {
            $promotion = new APromotion();
            $coupon = $promotion->getCouponData($indata['coupon']);
            if ($coupon) {
                $order_info['coupon_id'] = $coupon['coupon_id'];
            } else {
                $order_info['coupon_id'] = 0;
            }
        } else {
            $order_info['coupon_id'] = 0;
        }

        $order_info['ip'] = $this->request->getRemoteIP();

        $this->order_data = $order_info;

        $this->extensions->hk_ProcessData($this, 'build_order_data', $order_info);
        // merge two arrays. $this-> data can be changed by hooks.
        return $this->data + $this->order_data;
    }

    public function getOrderData()
    {
        $this->extensions->hk_ProcessData($this, 'get_order_data');
        return $this->data + $this->order_data;
    }

    public function saveOrder()
    {
        if (empty($this->order_data)) {
            return null;
        }
        $this->extensions->hk_ProcessData($this, 'save_order');
        $output = $this->data + $this->order_data;
        $this->order_id = $this->model_checkout_order->create($output, $this->order_id);
        return $this->order_id;
    }

    public function getOrderId()
    {
        return $this->order_id;
    }

    public function getCustomerId()
    {
        return $this->customer_id;
    }

}
