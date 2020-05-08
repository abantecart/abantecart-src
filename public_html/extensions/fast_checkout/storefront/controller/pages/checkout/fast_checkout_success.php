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

class ControllerPagesCheckoutFastCheckoutSuccess extends AController
{
    private $error = array();
    public $data = array();

    public function main()
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('fast_checkout/fast_checkout');

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getHomeURL(),
                'text'      => $this->language->get('text_home'),
                'separator' => false,
            ));

        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL('checkout/cart'),
                'text'      => $this->language->get('text_basket'),
                'separator' => $this->language->get('text_separator'),
            ));

        $this->document->addBreadcrumb(
            array(
                'href'      => $this->html->getSecureURL('checkout/fast_checkout'),
                'text'      => $this->language->get('fast_checkout_text_fast_checkout_title'),
                'separator' => $this->language->get('text_separator'),
            ));


        $this->data['success_url'] = $this->html->getSecureURL('r/checkout/pay/success', '&viewport=window&order_id='.$this->request->get['order_id']);

        $this->view->batchAssign($this->data);

        $this->view->setTemplate('pages/checkout/fast_checkout_success.tpl');
        $this->processTemplate();
        //update data before render
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }

}
