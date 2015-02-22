<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  Lincence details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if ( !defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}

class ControllerResponsesExtensionDefaultCheque extends AController {
	public function main() {
		$this->loadLanguage('default_cheque/default_cheque');
		
    	$this->view->batchAssign(  $this->language->getASet() );
		$this->view->assign('payable', $this->config->get('default_cheque_payable'));
		if ($this->config->get('default_cheque_address')) {
			$this->view->assign('address', $this->config->get('default_cheque_address'));
		} else {
			$this->view->assign('address', $this->config->get('config_address'));
		}
	
		$this->view->assign('continue', $this->html->getSecureURL('checkout/success'));

		$item = HtmlElementFactory::create( array( 'type' => 'button',
												  'name' => 'back',
												  'style' => 'button',
												  'text' => $this->language->get('button_back') ));
		$this->view->assign('button_back', $item);

		$item = HtmlElementFactory::create( array( 'type' => 'button',
												  'name' => 'checkout',
												  'style' => 'button',
												  'text' => $this->language->get('button_confirm') ));
		$this->view->assign('button_confirm', $item);

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$this->view->assign('back', $this->html->getSecureURL('checkout/payment'));
		} else {
			$this->view->assign('back', $this->html->getSecureURL('checkout/guest_step_2'));
		}

        $this->processTemplate('responses/default_cheque.tpl' );
	}
	
	public function confirm() {
		$this->loadLanguage('default_cheque/default_cheque');
		$this->load->model('checkout/order');
		
		$comment  = "\n\n".$this->language->get('text_payable') . "\n";
		$comment .= $this->config->get('default_cheque_payable') . "\n\n";
		$comment .= $this->language->get('text_address') . "\n";
		$comment .= ($this->config->get('default_cheque_address') ? $this->config->get('default_cheque_address') : $this->config->get('config_address')) . "\n\n";
		$comment .= $this->language->get('text_payment') . "\n";
		
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('default_cheque_order_status_id'), $comment);
	}
}
