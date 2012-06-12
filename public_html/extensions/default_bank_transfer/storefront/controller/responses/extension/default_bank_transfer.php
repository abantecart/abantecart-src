<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>
  
 UPGRADE NOTE: 
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.  
------------------------------------------------------------------------------*/

class ControllerResponsesExtensionDefaultBankTransfer extends AController {
	public $data = array();
	public function main() {

		$this->loadLanguage('default_bank_transfer/default_bank_transfer');

		$this->data['text_instruction'] = $this->language->get('text_instructions');
		$this->data['text_description'] = $this->language->get('text_description');
		$this->data['text_payment'] = $this->language->get('text_payment');


		$this->data['text'] = html_entity_decode(nl2br($this->config->get('default_bank_transfer_text_instruction_' . $this->config->get('storefront_language_id'))));
		if(!$this->data['text']){
			$this->data['text'] = html_entity_decode(nl2br($this->config->get('default_bank_transfer_text_instruction_1')));
		}


		$item = HtmlElementFactory::create( array( 'type' => 'button',
		                                          'name' => 'back',
			                                      'style' => 'button',
		                                          'text' => $this->language->get('button_back') ));
		$item = $item->getHtml();
		$this->view->assign('button_back', $item);

		$item = HtmlElementFactory::create( array( 'type' => 'button',
		                                          'name' => 'checkout',
			                                      'style' => 'button',
		                                          'text' => $this->language->get('button_confirm') ));
		$item = $item->getHtml();
		$this->view->assign('button_confirm', $item);

		$this->view->assign('continue', $this->html->getSecureURL('checkout/success'));

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$this->view->assign('back', $this->html->getSecureURL('checkout/payment'));
		} else {
			$this->view->assign('back', $this->html->getSecureURL('checkout/guest_step_2'));
		}
		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/default_bank_transfer.tpl' );
	}


	
	public function confirm() {
		$this->loadLanguage('default_bank_transfer/default_bank_transfer');

		$comment  = $this->language->get('text_instructions') . "<br>\n\n";
		$text = html_entity_decode(nl2br($this->config->get('default_bank_transfer_text_instruction_' . $this->config->get('storefront_language_id'))));
		if(!$text){
			$text = html_entity_decode(nl2br($this->config->get('default_bank_transfer_text_instruction_1')));
		}
		$comment .= $text . "<br>\n\n";
		$comment .= $this->language->get('text_payment');

		$this->load->model('checkout/order');
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('default_bank_transfer_order_status_id'), $comment);
	}
}
?>