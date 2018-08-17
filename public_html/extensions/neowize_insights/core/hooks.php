<?php
/*
NeoWize Insights - analytics and actionable insights for eCommerce sites.
For more info: www.neowize.com
*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

/**
 * This class listen to some useful hooks and send data to NeoWize
 */
class ExtensionNeowizeInsights extends Extension
{

    protected $registry;

    public function __construct()
    {
        $this->registry = Registry::getInstance();
    }

    // HOOK FUNCTIONS

    // called on checkout complete and send order data to NeoWize.
    public function onControllerPagesCheckoutSuccess_InitData()
    {
        try {
            $this->addCheckoutDataToPage();
        } catch (Exception $e) {
            NeowizeUtils::reportException(__FUNCTION__, $e);
        }
    }

    public function onControllerCommonFooterTop_InitData()
    {
        $that = $this->baseObject;
        try {
            // make sure Neowize should run on this system
            if (!NeowizeUtils::shouldRun()) {
                return null;
            }

            $data = array();
            $data['neowize_api_key'] = NeowizeUtils::getApiKey();
            // get current product id (relevant for product pages)
            $data['neowize_product_id'] = $that->request->get['product_id'];
            // get current cart data
            $data['neowize_current_cart'] = json_encode($this->getCurrentCartData());

            $that->view->batchAssign($data);

        } catch (Exception $e) {
            NeowizeUtils::reportException(__FUNCTION__, $e);
        }

    }

    // get current cart data
    protected function getCurrentCartData()
    {
        // get current cart data
        $that = $this->baseobject;

        $products = $this->registry->get('cart')->getProducts();

        // convert cart to neowize format with needed data
        $curr_cart = array();
        foreach ($products as $key => $product) {
            // get product id
            $product_id = $product['product_id'];

            // get main image url
            $main_image = NeowizeUtils::getProductImage($product_id, $this->registry->get('config'));

            // get product options data
            $options = array();
            if (isset($product['option'])) {
                foreach ($product['option'] as $option) {
                    array_push($options, array(
                        'option_value_id'      => $option['product_option_value_id'],
                        'option_id'            => $option['product_option_id'],
                        'value'                => $option['value'],
                        'price_mod'            => $option['price'],
                        'price_mod_by_percent' => $option['prefix'] == '%',
                    ));
                }
            }

            // set product data
            $product_data = array(
                "image_url"  => $main_image,
                "unique_id"  => $product_id,
                "quantity"   => $product['quantity'],
                "unit_price" => $product['price'],
                "item_name"  => $product['name'],
                "options"    => $options,
            );

            // add product data to cart
            array_push($curr_cart, $product_data);
        }

        // add data to page
        return $curr_cart;
    }






    // INTERNAL FUNCTIONS

    // add checkout / order data to page (via cookie), so NeoWize can parse it and use it.
    protected function addCheckoutDataToPage()
    {
        // get session data
        $session_data = $this->baseObject->session->data;

        // this controller is called twice - once with data once without it. skip the run without data.
        if (!isset($session_data['order_id'])) {
            return;
        }

        // get registry
        $registry = Registry::getInstance();

        // get order data
        $order = new AOrder($registry);
        $order_data = $order->buildOrderData($session_data);

        // calc grand total and other price components
        $order_tax = $order_total = $order_shipping = 0.0;
        foreach ($order_data['totals'] as $total) {
            if ($total['total_type'] == 'total') {
                $order_total += $total['value'];
            } elseif ($total['total_type'] == 'tax') {
                $order_tax += $total['value'];
            } elseif ($total['total_type'] == 'shipping') {
                $order_shipping += $total['value'];
            }
        }

        // convert to a dictionary
        $order_data_dict = array(
            'order_id'        => (int)$session_data['order_id'],
            'currency_code'   => $order_data['currency'],
            'grand_total'     => $order_total,
            'subtotal'        => $order_total - $order_tax - $order_shipping,
            'tax_amount'      => $order_tax,
            'shipping_amount' => $order_shipping,
            'city'            => $order_data['shipping_city'],
            'state'           => $order_data['shipping_zone'],
            'country'         => $order_data['shipping_country'],
        );

        // set order data into cookie. this cookie will be read from the javascript side and parsed into events.
        setcookie('neowize_order_data', json_encode($order_data_dict), 0, '/');
    }
}