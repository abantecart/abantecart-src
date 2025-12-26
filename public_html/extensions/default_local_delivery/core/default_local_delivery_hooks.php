<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/

/**
 * Class ExtensionDefaultPpPro
 */
class ExtensionDefaultLocalDelivery extends Extension
{

    public function onControllerResponsesCheckoutPay_InitData(){
        /** @var ControllerResponsesCheckoutPay $that */
        $that = $this->baseObject;
        if( !$that->config->get('fast_checkout_status')){
            return;
        }
        $shipping_method = $that->session->data['fc']['shipping_method'];
        if($shipping_method['id'] == 'default_local_delivery.default_local_delivery'){
            //show comment field for local delivery anyway
            $that->config->get('fast_checkout_show_order_comment_field');
        }
    }

    public function onControllerResponsesCheckoutPay_UpdateData(){
        /** @var ControllerResponsesCheckoutPay $that */
        $that = $this->baseObject;
        if( !$that->config->get('fast_checkout_status')){
            return;
        }
        $shipping_method = $that->session->data['fc']['shipping_method'];
        if($shipping_method['id'] != 'default_local_delivery.default_local_delivery'){
            return;
        }

        $that->view->assign('require_telephone', true);
    }
}
