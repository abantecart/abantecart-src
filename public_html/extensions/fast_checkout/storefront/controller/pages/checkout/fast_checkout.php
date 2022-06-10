<?php

/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2021 Belavier Commerce LLC

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

class ControllerPagesCheckoutFastCheckout extends AController
{

    public function __construct($registry, $instanceId, $controller, $parentController = '')
    {
        parent::__construct($registry, $instanceId, $controller, $parentController);

        $this->loadLanguage('fast_checkout/fast_checkout');
        if ($this->request->is_POST() && $this->request->get['single_checkout']) {
            $this->session->data['fc']['cart_key'] = randomWord(5);
        } elseif (!$this->session->data['fc']['cart_key']) {
            $this->session->data['fc']['cart_key'] = randomWord(5);
        }

        $this->extensions->hk_InitData($this, __FUNCTION__);

        //set sign for commonHead controller.
        // Needed to change url in the tpl. See addToCart method inside js
        $registry->set('fast_checkout', true);
        //short name
        $fcSession =& $this->session->data['fc'];

        $cartClassName = get_class($this->cart);
        //create new cart with single product (onclick buy-now button)
        if ($this->request->get['single_checkout'] && $this->request->is_POST()) {
            $post = $this->request->post;
            $fcSession['single_checkout'] = $this->data['single_checkout'] = true;
            $fcSession['cart'] = [];
            $this->registry->set(
                'cart',
                new $cartClassName($this->registry, $fcSession)
            );
            if (isset($this->request->post['product_id'])) {
                $this->loadModel('catalog/product', 'storefront');
                $productId = $post['product_id'];

                if (isset($post['option'])) {
                    $options = $post['option'];
                } else {
                    $options = [];
                }

                //for FILE-attributes
                if (has_value($this->request->files['option']['name'])) {
                    $fm = new AFile();
                    foreach ($this->request->files['option']['name'] as $id => $name) {
                        $attributeData = $this->model_catalog_product->getProductOption($productId, $id);
                        $attributeData['settings'] = unserialize($attributeData['settings']);
                        $filePathInfo = $fm->getUploadFilePath($attributeData['settings']['directory'], $name);

                        $options[$id] = $filePathInfo['name'];

                        if (!has_value($name)) {
                            continue;
                        }

                        if ($attributeData['required'] && !$this->request->files['option']['size'][$id]) {
                            $this->session->data['error'] = $this->language->get('error_required_options');
                            redirect($_SERVER['HTTP_REFERER']);
                        }

                        $fileData = [
                            'option_id' => $id,
                            'name'      => $filePathInfo['name'],
                            'path'      => $filePathInfo['path'],
                            'type'      => $this->request->files['option']['type'][$id],
                            'tmp_name'  => $this->request->files['option']['tmp_name'][$id],
                            'error'     => $this->request->files['option']['error'][$id],
                            'size'      => $this->request->files['option']['size'][$id],
                        ];

                        $fileErrors = $fm->validateFileOption($attributeData['settings'], $fileData);

                        if (has_value($fileErrors)) {
                            $this->session->data['error'] = implode('<br/>', $fileErrors);
                            redirect($_SERVER['HTTP_REFERER']);
                        } else {
                            $result = move_uploaded_file($fileData['tmp_name'], $filePathInfo['path']);

                            if (!$result || $this->request->files['package_file']['error']) {
                                $this->session->data['error'] .= '<br>Error: '.getTextUploadError(
                                        $this->request->files['option']['error'][$id]
                                    );
                                redirect($_SERVER['HTTP_REFERER']);
                            }
                        }

                        $dataset = new ADataset('file_uploads', 'admin');
                        $dataset->addRows(
                            [
                                'date_added' => date("Y-m-d H:i:s", time()),
                                'name'       => $filePathInfo['name'],
                                'type'       => $fileData['type'],
                                'section'    => 'product_option',
                                'section_id' => $attributeData['attribute_id'],
                                'path'       => $filePathInfo['path'],
                            ]
                        );
                    }
                }

                if ($textErrors = $this->model_catalog_product->validateProductOptions($productId, $options)) {
                    $this->session->data['error'] = $textErrors;
                    //send options values back via _GET
                    $url = '&'.http_build_query(['option' => $post['option']]);
                    redirect(
                        $this->html->getSecureURL(
                            'product/product',
                            '&product_id='.$post['product_id'].$url
                        )
                    );
                }

                $this->cart->add($post['product_id'], $post['quantity'], $options);
                $productCartKey = !$options ? $productId : $productId.':'.md5(serialize($options));
                if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
                    $this->session->data['error'] = $this->language->get('fast_checkout_text_not_enough_stock');
                    //send options values back via _GET
                    $url = '&'.http_build_query(['option' => $post['option']]);
                    redirect(
                        $this->html->getSecureURL(
                            'product/product',
                            '&product_id='.$post['product_id'].$url
                        )
                    );
                }
            }
            //if we added single product via POST request - do redirect to self
            $this->extensions->hk_ProcessData($this, 'post_single_checkout');
            redirect($this->html->getSecureURL('checkout/fast_checkout', '&product_key='.$productCartKey));
        } //do clone of default cart
        else {
            if (!$fcSession['single_checkout']) {
                $fcSession['single_checkout'] = false;
            }
            $fcSession['cart'] = $fcSession['cart'] ? : $this->session->data['cart'];
            $this->removeNoStockProducts();
            if (isset($this->session->data['coupon'])) {
                $fcSession['coupon'] = $this->session->data['coupon'];
            }
            $this->registry->set(
                'cart',
                new $cartClassName($this->registry, $fcSession)
            );
        }

        if ($this->request->get['single_checkout']) {
            $this->data['single_checkout'] = true;
        }
        //save cart_key into cookie to check on js-side
        // if another fc changed it
        setCookieOrParams(
            'fc_cart_key',
            $fcSession['cart_key'],
            [
                'path'     => dirname($this->request->server['PHP_SELF']),
                'domain'   => null,
                'secure'   => (defined('HTTPS') && HTTPS),
                'httponly' => false,
                'samesite' => ((defined('HTTPS') && HTTPS) ? 'None' : 'lax')
            ]
        );

        //check if two single-checkout tabs opened
        if (isset($this->request->get['product_key'])) {
            $cartProducts = $this->cart->getProducts();
            //warning about min/max qty exceeding
            if($fcSession['error']) {
                $this->data['error'] = $fcSession['error'];
                unset($this->session->data['fc']['error']);
            }

            $cartSingleProduct = $cartProducts[$this->request->get['product_key']];
            if (count($cartProducts) > 1 && $cartSingleProduct) {
                redirect(
                    $this->html->getSEOURL(
                        'product/product',
                        '&product_id='.$cartSingleProduct['product_id']
                    )
                );
            } elseif (!$cartSingleProduct) {
                //if product not found in the cart - just redirect to home
                redirect($this->html->getHomeURL());
            }
        }
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

    protected function removeNoStockProducts()
    {
        $cartProducts = $this->cart->getProducts();
        foreach ($cartProducts as $key => $cartProduct) {
            if (!$cartProduct['stock'] && !$this->config->get('config_stock_checkout')) {
                unset(
                    $this->session->data['fc']['cart'][$key]
                );
            }
        }
    }

    public function main()
    {
        $this->data['cart_rt'] = 'checkout/cart';
        if ($this->config->get('embed_mode') == true) {
            $this->data['cart_rt'] = 'r/checkout/cart/embed';
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        //validate if order min/max are met
        if (!$this->cart->hasMinRequirement() || !$this->cart->hasMaxRequirement()) {
            if ($this->session->data['fc']['single_checkout']) {
                #Check if order total max/min is set and met
                $cf_total_min = $this->config->get('total_order_minimum');
                $cf_total_max = $this->config->get('total_order_maximum');
                $error_msg = [];
                $this->loadLanguage('checkout/cart');
                if (!$this->cart->hasMinRequirement()) {
                    $error_msg[] = sprintf(
                        $this->language->get('error_order_minimum'),
                        $this->currency->format($cf_total_min)
                    );
                }
                if (!$this->cart->hasMaxRequirement()) {
                    $error_msg[] = sprintf(
                        $this->language->get('error_order_maximum'),
                        $this->currency->format($cf_total_max)
                    );
                }
                $this->session->data['error'] = implode(" ",$error_msg);
                redirect($this->html->getSecureURL('product/product','&product_id='.current($this->cart->getProducts())['product_id']));
            }
            redirect($this->html->getSecureURL($this->data['cart_rt']));
        }

        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            redirect($this->html->getSecureURL($this->data['cart_rt']));
        }

        if (HTTPS !== true) {
            $this->messages->saveError(
                'FastCheckout non-secure page!',
                'Page of Fast Checkout is non-secure. Checkout forbidden! Please set up ssl on server and set https store url!'
            );
            if (is_int(strpos($this->config->get('config_ssl_url'), 'https://'))) {
                redirect($this->config->get('config_ssl_url').'?'.http_build_query($_GET));
            } else {
                echo 'Non-secure connection! Checkout process forbidden.';
                exit;
            }
        }


        $this->document->setTitle($this->language->get('heading_title', 'fast_checkout/fast_checkout'));
        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('checkout/cart'),
                'text'      => $this->language->get('text_basket'),
                'separator' => $this->language->get('text_separator'),
            ]
        );

        $this->document->addBreadcrumb(
            [
                'href'      => $this->html->getSecureURL('checkout/fast_checkout'),
                'text'      => $this->language->get('fast_checkout_text_fast_checkout_title'),
                'separator' => $this->language->get('text_separator'),
            ]
        );
// TODO: clean up in the future
        $this->document->addStyle(
            [
                'href' => $this->view->templateResource('/css/bootstrap-xxs.css'),
                'rel'  => 'stylesheet',
            ]
        );
        $this->document->addStyle(
            [
                'href' => $this->view->templateResource('/css/pay.css'),
                'rel'  => 'stylesheet',
            ]
        );
        $this->document->addScript($this->view->templateResource('/js/credit_card_validation.js'));
        $this->document->addScript($this->view->templateResource('/javascript/common.js'));

        $this->data['cart_url'] = $this->html->getSecureURL('r/checkout/pay');
        $this->data['cart_key'] = $this->session->data['fc']['cart_key'];
        $this->data['single_checkout'] = $this->session->data['fc']['single_checkout'];
        if ($this->request->get['product_key']) {
            $this->data['product_key'] = $this->request->get['product_key'];
        }

        $this->view->batchAssign($this->data);

        $this->view->setTemplate('pages/checkout/fast_checkout.tpl');
        $this->processTemplate();
        //update data before render
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
