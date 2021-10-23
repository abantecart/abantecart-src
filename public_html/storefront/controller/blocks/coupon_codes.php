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

class ControllerBlocksCouponCodes extends AController
{
    public function __construct($registry, $instance_id, $controller, $parent_controller = '')
    {
        parent::__construct($registry, $instance_id, $controller, $parent_controller);
        $this->data['empty_render_text'] =
            'To view content of block coupon status should be enabled in the store settings';
    }

    public function main($action = '')
    {
        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->loadLanguage('checkout/payment');

        if (!$this->config->get('coupon_status')) {
            return;
        }

        $this->data['coupon_status'] = $this->config->get('coupon_status');

        $enteredCoupon = $this->request->post['coupon'] ?? $this->session->data['coupon'];

        $form = new AForm();
        $form->setForm(['form_name' => 'coupon']);

        $this->data['coupon_code'] = $enteredCoupon;
        $this->data['form_open'] = $form->getFieldHtml(
            [
                'type'   => 'form',
                'name'   => 'coupon',
                'action' => $action,
                'csrf'   => true,
            ]
        );
        $this->data['coupon'] = $form->getFieldHtml(
            [
                'type'  => 'input',
                'name'  => 'coupon',
                'value' => $enteredCoupon,
            ]
        );
        $this->data['submit'] = $form->getFieldHtml(
            [
                'type' => 'submit',
                'name' => $this->language->get('button_coupon'),
            ]
        );

        $this->view->batchAssign($this->data);
        $this->processTemplate('blocks/coupon_form.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}