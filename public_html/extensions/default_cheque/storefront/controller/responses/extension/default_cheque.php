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
if (!defined('DIR_CORE')) {
    header('Location: static_pages/');
}

class ControllerResponsesExtensionDefaultCheque extends AController
{
    public function main()
    {
        $this->loadLanguage('default_cheque/default_cheque');

        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->view->batchAssign($this->language->getASet());
        $this->view->assign('payable', $this->config->get('default_cheque_payable'));
        if ($this->config->get('default_cheque_address')) {
            $this->view->assign('address', $this->config->get('default_cheque_address'));
        } else {
            $this->view->assign('address', $this->config->get('config_address'));
        }

        $this->view->assign('continue', $this->html->getSecureURL('checkout/finalize'));

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
                'style' => 'button',
                'text'  => $this->language->get('button_confirm'),
            ]);
        $this->view->assign('button_confirm', $item);
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
        $this->processTemplate('responses/default_cheque.tpl');
    }

    public function confirm()
    {
        $this->extensions->hk_InitData($this,__FUNCTION__);
        $this->loadLanguage('default_cheque/default_cheque');
        $this->load->model('checkout/order');

        $comment = "\n\n".$this->language->get('text_payable')."\n";
        $comment .= $this->config->get('default_cheque_payable')."\n\n";
        $comment .= $this->language->get('text_address')."\n";
        $comment .= ($this->config->get('default_cheque_address') ?: $this->config->get('config_address')).PHP_EOL.PHP_EOL;
        $comment .= $this->language->get('text_payment')."\n";
        $this->data['comment'] = html_entity_decode($comment,ENT_QUOTES,'UTF-8');

        $this->extensions->hk_ProcessData($this, __FUNCTION__);

        $this->model_checkout_order->confirm(
            (int)$this->session->data['order_id'],
            (int)$this->config->get('default_cheque_order_status_id'),
            $this->data['comment']
        );
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
    }
}
