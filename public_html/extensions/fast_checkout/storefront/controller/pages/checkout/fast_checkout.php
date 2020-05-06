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

class ControllerPagesCheckoutFastCheckout extends AController
{
    public $data = array();

    public function main()
    {
        if(HTTPS !== true){
            $this->messages->saveError(
                'FastCheckout non-secure page!',
                'Page of Fast Checkout is non-secure. Checkout forbidden! Please set up ssl on server and set https store url!'
            );
            if( is_int(strpos($this->config->get('config_ssl_url'), 'https://')) ){
                redirect($this->config->get('config_ssl_url').'?'.http_build_query($_GET));
            }else{
                echo 'Non-secure connection! Checkout process forbidden.';
                exit;
            }
        }
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->data['cart_url'] = $this->html->getSecureURL(
                                                    'r/checkout/pay',
                                                    '&order_id='.$this->session->data['fast_checkout']['cart_key']
        );

        $this->view->batchAssign($this->data);

        $this->view->setTemplate('pages/checkout/fast_checkout.tpl');
        $this->processTemplate();
        //update data before render
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}
