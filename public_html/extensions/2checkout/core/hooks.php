<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2020 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Licence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/


class Extension2Checkout extends Extension
{
    //payment confirmation pending page
    public function onControllerPagesCheckoutSuccess_InitData()
    {
        $that = $this->baseObject;
        $order_id = (int)$that->session->data['order_id'];
        if (!$order_id || $that->session->data['2checkout_pending_ipn_skip']) {
            return null;
        }
        $that->loadModel('checkout/order');
        $order_info = $that->model_checkout_order->getOrder($order_id);
        //do nothing if order confirmed or it's not created with paypal standart
        if ((int)$order_info['order_status_id'] != 0 || $order_info['payment_method_key'] != '2checkout') {
            return null;
        }
        //set sign to prevent double redirect (see above)
        $that->session->data['2checkout_pending_ipn_skip'] = true;
        redirect($that->html->getSecureURL('extension/2checkout/pending_payment'));
    }

    //delete sign after success
    public function onControllerPagesCheckoutSuccess_UpdateData()
    {
        unset($this->baseObject->session->data['2checkout_pending_ipn_skip']);
    }

}
