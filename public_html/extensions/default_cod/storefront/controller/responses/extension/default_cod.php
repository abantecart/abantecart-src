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

class ControllerResponsesExtensionDefaultCod extends AController
{
    public function main()
    {

        $item = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'back',
                'style' => 'button',
                'text'  => $this->language->get('button_back'),
            ]
        );
        $this->view->assign('button_back', $item);

        $item = $this->html->buildElement(
            [
                'type'  => 'button',
                'name'  => 'checkout',
                'style' => 'button btn-primary',
                'text'  => $this->language->get('button_confirm'),
            ]
        );
        $this->view->assign('button_confirm', $item);

        $this->view->assign('continue', $this->html->getSecureURL('checkout/finalize'));
        $this->processTemplate('responses/default_cod.tpl');
    }

    public function api()
    {
        $data = [];

        $data['text_note'] = $this->language->get('text_note');
        $data['process_rt'] = 'default_cod/api_confirm';

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($data));
    }

    public function api_confirm()
    {
        $data = [];

        $this->confirm();
        $data['success'] = 'completed';

        $this->load->library('json');
        $this->response->setOutput(AJson::encode($data));
    }

    public function confirm()
    {
        $this->load->model('checkout/order');
        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('default_cod_order_status_id'));
        $this->response->addJSONHeader();
        $this->response->setOutput(json_encode(['result' => true]));
    }
}
