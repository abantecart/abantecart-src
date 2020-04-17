<?php
/*------------------------------------------------------------------------------
$Id$
  
This file and its content is copyright of AlgoZone Inc - © AlgoZone Inc 2003-2018. All rights reserved.

You may not, except with our express written permission, modify, distribute or commercially exploit the content. Nor may you transmit it or store it in any other website or other form of electronic retrieval system.
------------------------------------------------------------------------------*/

if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ExtensionFastCheckout extends Extension
{
    private $init_loaded = false;
    protected $registry;
    protected $sc_rt = 'r/checkout/pay';

    public function __construct()
    {
        //CORS solution for http 2 https
        header("Access-Control-Allow-Origin: ".'http://'.REAL_HOST.get_url_path($_SERVER['PHP_SELF']));
        $this->registry = Registry::getInstance();
        if (!isset($this->registry->get('session')->data['fast_checkout'])) {
            $this->registry->get('session')->data['fast_checkout'] = array();
        }
    }

    public function onControllerCommonMaintenance_InitData()
    {
        $that = $this->baseObject;
        if ($this->baseObject_method != 'response') {
            return null;
        }

        $that->loadModel('catalog/product');
        $that->loadModel('checkout/extension');
        $that->loadLanguage('fast_checkout/fast_checkout');
        //Check if we need to substidude regular cart with fast checkout cart for single product
        $cart_key = $that->request->post_or_get('cart_key');
        $product_id = $that->request->post_or_get('product_id');
        if (!$cart_key && $product_id) {
            //we have single product checkout
            $cart_key = randomWord(5);
            $that->request->get['cart_key'] = $cart_key;
            //new custom cart
            $ret = $this->buildCustomCart($that, $cart_key);
            if ($ret) {
                echo "Error: $ret"; //??????
            }
        } else {
            if ($cart_key && isset($that->session->data['fast_checkout'][$cart_key]['cart'])) {
                //custom cart already build
                $csession = &$that->session->data['fast_checkout'][$cart_key];
                $cart_class_name = get_class($that->cart);
                $this->registry->set('cart', new $cart_class_name($this->registry, $csession));
            } else {
                // we use main cart first time, generate cart key
                if (!$cart_key) {
                    $cart_key = randomWord(5);
                }
                $that->request->get['cart_key'] = $cart_key;
                if (!isset($that->session->data['fast_checkout'][$cart_key])) {
                    $that->session->data['fast_checkout'][$cart_key] = array();
                }
            }
        }
    }

    private function buildCustomCart($that, $cart_key)
    {
        $request = $that->request->is_POST() ? $that->request->post : $that->request->get;
        $request['quantity'] = !(int)$request['quantity'] ? 1 : (int)$request['quantity'];
        $product_id = $that->request->post_or_get('product_id');
        if ($product_id) {
            //Note: Do not clean prior sessions as multiple windows can be used.
            $that->session->data['fast_checkout'][$cart_key]['product_id'] = $product_id;
        } else {
            if ($that->session->data['fast_checkout'][$cart_key]) {
                //if we already have a quick cart instance
                $product_id = $that->session->data['fast_checkout'][$cart_key]['product_id'];
            }
        }

        $csession = &$that->session->data['fast_checkout'][$cart_key];
        $cart_class_name = get_class($that->cart);

        //create new custom cart instance.
        $csession['cart'] = array();
        if (isset($request['option'])) {
            $options = $request['option'];
        } else {
            $options = array();
        }

        //for FILE-attributes
        if (has_value($that->request->files['option']['name'])) {

            $fm = new AFile();
            $that->loadModel('catalog_product');
            $that->loadLanguage('checkout/cart');
            foreach ($that->request->files['option']['name'] as $id => $name) {

                $attribute_data = $that->model_catalog_product->getProductOption($product_id, $id);
                $attribute_data['settings'] = unserialize($attribute_data['settings']);
                $file_path_info = $fm->getUploadFilePath($attribute_data['settings']['directory'], $name);

                $options[$id] = $file_path_info['name'];

                if (!has_value($name)) {
                    continue;
                }

                if ($attribute_data['required'] && !$that->request->files['option']['size'][$id]) {
                    return
                        sprintf($that->language->get('fast_checkout_error_product_option'),
                            $that->language->get('error_required_options'));;
                }

                $file_data = array(
                    'option_id' => $id,
                    'name'      => $file_path_info['name'],
                    'path'      => $file_path_info['path'],
                    'type'      => $that->request->files['option']['type'][$id],
                    'tmp_name'  => $that->request->files['option']['tmp_name'][$id],
                    'error'     => $that->request->files['option']['error'][$id],
                    'size'      => $that->request->files['option']['size'][$id],
                );

                $file_errors = $fm->validateFileOption($attribute_data['settings'], $file_data);

                if (has_value($file_errors)) {
                    return
                        sprintf($that->language->get('fast_checkout_error_product_option'), implode('', $file_errors));
                } else {
                    $result = move_uploaded_file($file_data['tmp_name'], $file_path_info['path']);

                    if (!$result || $that->request->files['package_file']['error']) {
                        return
                            sprintf($this->language->get('fast_checkout_error_product_option'),
                                'Error: '.getTextUploadError($that->request->files['option']['error'][$id]));
                    }
                }

                $dataset = new ADataset('file_uploads', 'admin');
                $dataset->addRows(
                    array(
                        'date_added' => date("Y-m-d H:i:s", time()),
                        'name'       => $file_path_info['name'],
                        'type'       => $file_data['type'],
                        'section'    => 'product_option',
                        'section_id' => $attribute_data['attribute_id'],
                        'path'       => $file_path_info['path'],
                    )
                );

            }
        }

        $that->loadLanguage('checkout/cart');
        if ($errors = $that->model_catalog_product->validateProductOptions($product_id, $options)) {
            return
                sprintf($that->language->get('fast_checkout_error_product_option'), implode(' ', $errors));
        }
        //load product to custom cart instance
        $this->addToSessionCart($product_id, $request['quantity'], $options, $csession);

        $this->registry->set('cart', new $cart_class_name($this->registry, $csession));
        $that->cart->getProducts(true);

    }

    private function addToSessionCart($product_id, $qty = 1, $options = array(), &$c_data)
    {
        $product_id = (int)$product_id;
        if (!$options) {
            $key = $product_id;
        } else {
            $key = $product_id.':'.md5(serialize($options));
        }

        if ((int)$qty && ((int)$qty > 0)) {
            if (!isset($c_data['cart'][$key])) {
                $c_data['cart'][$key]['qty'] = (int)$qty;
            } else {
                $c_data['cart'][$key]['qty'] += (int)$qty;
            }
            $c_data['cart'][$key]['options'] = $options;
        }
    }

    public function onControllerPagesCheckoutShipping_InitData()
    {
        $that = $this->baseObject;
        redirect($that->html->getSecureURL($this->sc_rt, "&viewport=".$that->config->get('fast_checkout_view_mode')));
    }

    public function onControllerCommonFooter_UpdateData()
    {
        $that = $this->baseObject;

        $that->loadLanguage('fast_checkout/fast_checkout');
    }

    public function onControllerResponsesEmbedHead_InitData()
    {
        $that = $this->baseObject;
        if (!$that->config->get('embed_mode')) {
            return null;
        }
        $this->_init($that);
    }

    public function onControllerBlocksBestSeller_UpdateData()
    {
        $that = $this->baseObject;
        $this->_init($that);
        $this->_add_prod_listing_buttons($that);
    }

    public function onControllerBlocksFeatured_UpdateData()
    {
        $that = $this->baseObject;
        $this->_init($that);
        $this->_add_prod_listing_buttons($that);
    }

    public function onControllerBlocksLatest_UpdateData()
    {
        $that = $this->baseObject;
        $this->_init($that);
        $this->_add_prod_listing_buttons($that);
    }

    public function onControllerBlocksSpecial_UpdateData()
    {
        $that = $this->baseObject;
        $this->_init($that);
        $this->_add_prod_listing_buttons($that);
    }

    public function onControllerPagesProductCategory_UpdateData()
    {
        $that = $this->baseObject;
        $this->_init($that);
        $this->_add_prod_listing_buttons($that);
    }

    public function onControllerPagesProductManufacturer_UpdateData()
    {
        $that = $this->baseObject;
        $this->_init($that);
        $this->_add_prod_listing_buttons($that);
    }

    public function onControllerPagesProductSpecial_UpdateData()
    {
        $that = $this->baseObject;
        $this->_init($that);
        $this->_add_prod_listing_buttons($that);
    }

    public function onControllerPagesProductSearch_UpdateData()
    {
        $that = $this->baseObject;
        $this->_init($that);
        $this->_add_prod_listing_buttons($that);
    }

    private function _add_prod_listing_buttons($that)
    {
        $buy_now = $buy_now_text = $that->language->get('fast_checkout_buy_now');
        $add_to_cart = $add_to_cart_text = $that->language->get('button_add_to_cart');

        if (!$that->data['products'] || !is_array($that->data['products'])) {
            return;
        }
        foreach ($that->data['products'] as $prod) {
            if (!$prod['options']) {
                $pay_now_html = $pay_now_list_html = '';
                //if config to show both add to cart and buy now
                if (!$that->config->get('fast_checkout_hide_add_to_cart')) {
                    $buy_now = '';
                    $pay_now_html .= '<a title="'.$add_to_cart_text.'" data-id="'.$prod['product_id'].'" href="#" class="productcart sch_productcart">
                                      <i class="fa fa-cart-plus fa-fw"></i> </a>';
                    $pay_now_list_html .= '<a title="'.$add_to_cart_text.'" data-id="'.$prod['product_id'].'" href="#" class="productcart sch_productcart">
                                            <i class="fa fa-cart-plus fa-fw"></i> '.$add_to_cart.'</a>';
                }
                if ($that->config->get('fast_checkout_view_mode') == 'window') {
                    $pay_now_html .= '<a title="'.$buy_now_text.'" 
                                        href="'.$that->html->getSecureURL('r/checkout/pay',
                            '&viewport=window&product_id='.$prod['product_id']).'" class="btn btn-primary sch_buynow">
                                                        <i class="fa fa-credit-card fa-fw"></i> '.$buy_now.'</a>';
                } else {
                    $pay_now_html .= '<a title="'.$buy_now_text.'" href="#" data-id="'.$prod['product_id'].'" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#pay_modal" class="btn btn-primary sch_buynow">
                                    <i class="fa fa-credit-card fa-fw"></i> '.$buy_now.'</a>';
                }
                $that->view->addHookVar('product_add_to_cart_html_'.$prod['product_id'], $pay_now_html);

                $pay_now_list_html .= '<a title="'.$buy_now_text.'" href="#" data-id="'.$prod['product_id'].'" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#pay_modal" class="btn btn-primary sch_buynow">
                    <i class="fa fa-credit-card fa-fw"></i> </a>';
                $that->view->addHookVar('product_add_to_cart_list_html_'.$prod['product_id'], $pay_now_list_html);
            }
        }
    }

    public function onControllerPagesProductProduct_UpdateData()
    {
        $that = $this->baseObject;
        $url = $that->html->getSecureURL('r/checkout/pay');

        $this->_init($that);

        $buy_now = $that->language->get('fast_checkout_buy_now');
        $add_to_cart = $that->language->get('button_add_to_cart');

        //if config to show both add to cart and buy now
        $pay_now_html = '';
        if ($that->config->get('fast_checkout_view_mode') == 'window') {
            $pay_now_html .= '<a title="'.$buy_now.'" 
                                href="'.$that->html->getSecureURL('r/checkout/pay',
                    '&viewport=window&product_id='.$that->request->get['product_id']).'" class="btn btn-primary sch_buynow">
                                <i class="fa fa-credit-card fa-fw"></i> '.$buy_now.'</a>';
        }
        if (!$that->config->get('fast_checkout_hide_add_to_cart')) {
            $pay_now_html .= '<a href="#" onclick="document.getElementById(\'product\').submit(); return false;" class="btn btn-default btn-xl cart sch_productcart"><i class="fa fa-cart-plus fa-fw"></i> <span class="hidden-xs">'
                .$add_to_cart.'</span></a>&nbsp;&nbsp;';
        }
        $pay_now_html .= '<a data-href="'.$url.'" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#pay_modal" class="btn btn-primary btn-xl sch_buynow">
        <i class="fa fa-credit-card fa-fw"></i> <span class="hidden-xs">'.$buy_now.'</span></a>';
        $that->view->addHookVar('product_add_to_cart_html', $pay_now_html);

        //NOTE: pages/product/product.post.tpl template is included to handle modal

        if ($that->config->get('embed_mode')) {
            $that->view->setTemplate('embed/product/product_sc.tpl');
        }
    }

    public function onControllerResponsesEmbedJS_UpdateData()
    {
        if ($this->baseObject_method != 'product') {
            return null;
        }
        $that = $this->baseObject;

        $this->_init($that);
        $product_id = $that->request->get['product_id'];

        $buy_now = $that->language->get('fast_checkout_buy_now');

        $url = $that->html->getSecureURL('r/checkout/pay', '&product_id='.$product_id);
        $target = $this->baseObject->data['target'];

        $pay_now_html = '<button data-href="'.$url
            .'" data-backdrop="static" data-keyboard="false" data-target="#abc_embed_modal" data-toggle="abcmodal" class="abantecart_button">'
            .$buy_now.'</button>';

        $product_data = $that->view->getData('product');
        if (!$product_data['options'] && !is_array($product_data['options'])) {
            $that->load->model('catalog/product');
            $product_options = $that->model_catalog_product->getProductOptions($product_id);
        } else {
            $product_options = $product_data['options'];
        }
        if (!$product_options) {
            $js = "$('#".$target." .abantecart_addtocart').append('".$pay_now_html."')";
            if ($that->config->get('fast_checkout_hide_add_to_cart')) {

                unset($product_data['quantity'], $product_data['button_addtocart']);
                $that->view->assign('product', $product_data);
            }
            $that->view->addHookVar('embed_product_js', $js);
        }
        $that->view->setTemplate('embed/js_product_sc.tpl');
    }

    public function onControllerPagesAccountInvoice_UpdateData()
    {
        $that = $this->baseObject;

        if ($that->config->get('config_guest_checkout')) {
            //to do, add button to request email with download link for guests. Use emailDownloads()
            /*
            $download_html .= '<a href="#" class="btn btn-primary">
                    <i class="fa fa-cloud_download fa-fw"></i> '.$request_download.'</a>';
            $that->view->addHookVar('hk_additional_buttons', $download_html);
            */
        }
    }

    /**
     * @param AController $that
     *
     * @return null
     */
    private function _init(&$that)
    {
        if ($this->init_loaded === true) {
            return null;
        }

        $that->document->addStyle(
            array(
                'href'  => $that->view->templateResource('/css/fast_checkout.css'),
                'rel'   => 'stylesheet',
                'media' => 'screen',
            )
        );
        $that->document->addScript($that->view->templateResource('/js/credit_card_validation.js'));
        $that->loadLanguage('fast_checkout/fast_checkout');
        $this->init_loaded = true;
    }

    //if generic checkout process - remove sign of simple checkout
    public function onControllerPagesCheckoutConfirm_InitData()
    {
        $this->baseObject->session->data['fast-checkout'] = false;
    }

    //forward to fast_checkout success page if checkout was simple
    public function onControllerPagesCheckoutSuccess_ProcessData()
    {
        $that =& $this->baseObject;
        if ($that->session->data['fast-checkout']) {
            header('Location: '.$that->html->getSecureURL('r/checkout/pay/success',
                    '&viewport=window&order_id='.$that->session->data['processed_order_id']));
            exit;
        }
    }

}
