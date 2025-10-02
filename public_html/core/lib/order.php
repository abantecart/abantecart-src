<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */

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
            $this->order_id = (int)$this->session->data['order_id'];
        } else {
            $this->order_id = (int)$order_id;
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
        return (array)$this->data + (array)$this->order_data;
    }

    /**
     * @param array $inData : Session data array
     *
     * @return array
     * NOTE: method to create an order based on provided data array.
     * @throws AException
     */
    public function buildOrderData(array $inData = [])
    {
        $order_info = [];
        if (!$inData) {
            return [];
        }

        $hasShipping = $this->cart->hasShipping();

        $taxes = $this->cart->getTaxes();

        $this->load->model('checkout/extension');

        $results = $this->model_checkout_extension->getExtensions('total');
        $calculation_order = [];
        foreach ($results as $key => $value) {
            $calculation_order[$key] = $this->config->get($value['key'] . '_calculation_order');
        }

        array_multisort($calculation_order, SORT_ASC, $results);

        $total_data = [];
        $totalAmount = 0;
        foreach ($results as $result) {
            /** @var ModelTotalTotal|ModelTotalSubTotal|ModelTotalShipping $mdl */
            $mdl = $this->load->model('total/' . $result['key']);
            $mdl->getTotal($total_data, $totalAmount, $taxes, $inData);

            //allow changing total data on-the-fly for extensions, for example rounding of amount etc
            $this->data = [
                'total_key'  => $result['key'],
                'total_data' => $total_data,
                'total'      => $totalAmount,
                'taxes'      => $taxes,
            ];

            $this->extensions->hk_ProcessData($this, __FUNCTION__);

            $total_data = $this->data['total_data'];
            $totalAmount = $this->data['total'];
            $taxes = $this->data['taxes'];
            unset(
                $this->data['total_key'],
                $this->data['total_data'],
                $this->data['total'],
                $this->data['taxes']
            );
        }

        $sort_order = [];
        $orderTotalAmount = $totalAmount;
        foreach ($total_data as $key => $value) {
            if ($value['id'] == 'total') {
                $orderTotalAmount = $value['value'];
            }
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $total_data);

        $order_info['store_id'] = $this->config->get('current_store_id') ?? $this->config->get('config_store_id');
        $order_info['store_name'] = $this->config->get('store_name');
        $order_info['store_url'] = $this->config->get('config_url') . $this->config->get('seo_prefix');
        //prepare data with customer details.
        if ($this->customer->getId()) {
            $order_info['customer_id'] = $this->customer->getId();
            $order_info['customer_group_id'] = $this->customer->getCustomerGroupId();
            $order_info['firstname'] = $this->customer->getFirstName();
            $order_info['lastname'] = $this->customer->getLastName();
            $order_info['email'] = $this->customer->getEmail();
            $order_info['telephone'] = $inData['telephone'] ?: $this->customer->getTelephone();
            $order_info['fax'] = $this->customer->getFax();
            /** @var ModelAccountAddress $addrMdl */
            $addrMdl = $this->load->model('account/address');

            if ($hasShipping) {
                $shippingAddressId = (int)$inData['shipping_address_id'];
                $shippingAddress = $addrMdl->getAddress($shippingAddressId);
                foreach ($shippingAddress as $key => $value) {
                    if ($key == 'ext_fields') {
                        foreach ($value as $k => $v) {
                            $order_info['shipping_' . $k] = $v;
                        }
                    } else {
                        $order_info['shipping_' . $key] = $value;
                    }
                }
            } else {
                foreach ($order_info as $key => &$value) {
                    if (str_starts_with($key, 'shipping_')) {
                        $value = '';
                    }
                }
            }

            $paymentAddressId = (int)$inData['payment_address_id'];
            $paymentAddress = $addrMdl->getAddress($paymentAddressId);
            foreach ($paymentAddress as $key => $value) {
                if ($key == 'ext_fields') {
                    foreach ($value as $k => $v) {
                        $order_info['payment_' . $k] = $v;
                    }
                } else {
                    $order_info['payment_' . $key] = $value;
                }
            }
        } else {
            if (isset($inData['guest'])) {
                //this is a guest order
                $order_info['customer_id'] = 0;
                $order_info['customer_group_id'] = $this->config->get('config_customer_group_id');
                $order_info = array_merge($order_info, $inData['guest']);

                //IM addresses
                $protocols = $this->im->getProtocols();
                foreach ($protocols as $protocol) {
                    if (has_value($inData['guest'][$protocol]) && !has_value($order_info[$protocol])) {
                        $order_info[$protocol] = $inData['guest'][$protocol];
                    }
                }

                $shippingDataSet = (array)($inData['guest']['shipping'] ?? $inData['guest']);
                foreach ($shippingDataSet as $key => $value) {
                    $order_info['shipping_' . $key] = $hasShipping ? $value : '';
                }

                $paymentDataSet = (array)$inData['guest'];
                unset($paymentDataSet['shipping']);
                foreach ($paymentDataSet as $key => $value) {
                    if (!is_array($value)) {
                        $order_info['payment_' . $key] = $value;
                    }
                }
            } else {
                return [];
            }
        }

        if (isset($inData['shipping_method']['title'])) {
            $order_info['shipping_method'] = $inData['shipping_method']['title'];
            // note - id by mask method_txt_id.method_option_id. for ex. default_weight.default_weight_1
            $order_info['shipping_method_key'] = $inData['shipping_method']['id'];
        } else {
            $order_info['shipping_method'] = '';
            $order_info['shipping_method_key'] = '';
        }

        if (isset($inData['payment_method']['title'])) {
            $order_info['payment_method'] = $inData['payment_method']['title'];
            preg_match('/^([^.]+)/', $inData['payment_method']['id'], $matches);
            $order_info['payment_method_key'] = $matches[1];
        } else {
            $order_info['payment_method'] = '';
        }

        $product_data = [];

        foreach ($this->cart->getProducts() + $this->cart->getVirtualProducts() as $key => $product) {
            $product_data[] = array_merge(
                $product,
                [
                    'weight'          => (float)$product['weight'],
                    'weight_iso_code' => $product['weight_class'],
                    'width'           => (float)$product['width'],
                    'height'          => (float)$product['height'],
                    'length'          => (float)$product['length'],
                    'length_iso_code' => $product['length_class'],
                    //ternary for virtual products
                    'price'           => $product['amount'] ?: $product['price'],
                    'total'           => $product['amount']
                        ? ($product['amount'] * $product['quantity'])
                        : $product['total'],
                    'tax'             => $this->tax->calcTotalTaxAmount($product['total'], $product['tax_class_id']),
                ]
            );
        }
        $order_info['products'] = $product_data;
        $order_info['totals'] = $total_data;
        $order_info['comment'] = $inData['comment'];
        //this amount in order currency taken from order total model result
        $order_info['total'] = $orderTotalAmount;
        $order_info['language_id'] = $this->config->get('storefront_language_id');
        $order_info['currency_id'] = $this->currency->getId();
        $order_info['currency'] = $this->currency->getCode();
        $order_info['value'] = $this->currency->getValue($this->currency->getCode());

        if (isset($inData['coupon'])) {
            $promotion = new APromotion();
            $coupon = $promotion->getCouponData($inData['coupon']);
            $order_info['coupon_id'] = (int)$coupon['coupon_id'];
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

    public static function getGoogleAnalyticsOrderData(array $orderData)
    {
        if (!$orderData) {
            return [];
        }

        $registry = Registry::getInstance();
        $currency = $registry->get('currency');

        //Google Analytics data for js-script.
        //This will be shown in the footer of the page
        $order_tax = $order_total = $order_shipping = 0.0;

        foreach ($orderData['totals'] as $total) {
            $total['value'] = (float)$total['value'];
            $total['type'] = $total['type'] ?: $total['total_type'];
            if ($total['type'] == 'total') {
                $order_total += $total['value'];
            } elseif ($total['type'] == 'tax') {
                $order_tax += $total['value'];
            } elseif ($total['type'] == 'shipping') {
                $order_shipping += $total['value'];
            }
        }

        if (!$orderData['shipping_city']) {
            $addr = [
                'city'    => $orderData['payment_city'],
                'state'   => $orderData['payment_zone'],
                'country' => $orderData['payment_country'],
            ];
        } else {
            $addr = [
                'city'    => $orderData['shipping_city'],
                'state'   => $orderData['shipping_zone'],
                'country' => $orderData['shipping_country'],
            ];
        }

        $gaOrderData = array_merge(
            [
                'transaction_id' => (int)$orderData['order_id'],
                'store_name'     => $registry->get('config')->get('store_name'),
                'currency_code'  => $orderData['currency'] ?: $registry->get('currency')->getCode(),
                'total'          => (float)$currency->format_number($order_total),
                'tax'            => (float)$currency->format_number($order_tax),
                'shipping'       => (float)$currency->format_number($order_shipping),
                'coupon'         => $registry->get('session')->data['coupon']
            ],
            $addr
        );

        if ($orderData['order_products']) {
            /** @var ModelAccountOrder $mdl */
            $mdl = $registry->get('load')->model('account/order');
            $gaOrderData['items'] = [];
            foreach ($orderData['order_products'] as $product) {
                //try to get option sku for product. If not presents - take main sku from product details
                $options = $mdl->getOrderOptions((int)$orderData['order_id'], $product['order_product_id']);
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

                $gaOrderData['items'][] = [
                    'item_id'   => (int)$product['product_id'],
                    'item_name' => $product['name'],
                    'sku'       => $sku,
                    'price'     => (float)$product['price'],
                    'quantity'  => (int)$product['quantity'],
                ];
            }
        }
        return $gaOrderData;
    }
}