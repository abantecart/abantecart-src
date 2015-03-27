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

class ControllerResponsesExtensionDefaultBanktransfer extends AController {
	public function main() {
		$this->loadLanguage('default_banktransfer/default_banktransfer');

		$this->view->assign('text_instructions', $this->language->get('text_instructions'));
		$this->view->assign('text_payment', $this->language->get('text_payment'));
		
    	$this->view->batchAssign(  $this->language->getASet() );
		$lang_id = $this->language->getLanguageID();
		$instructions = $this->config->get('default_banktransfer_instructions_'.$lang_id);

		if(!$instructions){
			$this->messages->saveError('default_banktransfer error', 'Please, set instructions for all languages!');
			$lang_id = $this->language->getDefaultLanguageID();
			$instructions = $this->config->get('default_banktransfer_instructions_'.$lang_id);
		}

		if(!$instructions){
			$this->messages->saveError('default_banktransfer error', 'Please, set instructions for all languages!');
		}

		$this->view->assign('instructions', nl2br($instructions) );
		$this->view->assign('continue', $this->html->getSecureURL('checkout/success'));		

		if ($this->request->get['rt'] != 'checkout/guest_step_3') {
			$this->view->assign('back', $this->html->getSecureURL('checkout/payment','',true));
		} else {
			$this->view->assign('back', $this->html->getSecureURL('checkout/guest_step_2','',true));
		}
		
		//check total for to meat min requirement 
		if ( has_value($this->config->get('default_banktransfer_order_min')) ) {
			if ($this->cart->getTotal() < $this->config->get('default_banktransfer_order_min') ) {
				$this->view->assign('minimum_notmet', $this->language->get('text_minimum_notmet'));	
			}
		}
		
        $this->processTemplate('responses/default_banktransfer.tpl' );
	}
	
	public function confirm() {
		$this->loadLanguage('default_banktransfer/default_banktransfer');
		$this->load->model('checkout/order');
		
		$comment  = $this->language->get('text_instructions') . "\n";
		$comment .= $this->config->get('default_banktransfer_instructions_'.$this->language->getLanguageID()) . "\n\n";
		$comment .= $this->language->get('text_payment') . "\n";
		
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('default_banktransfer_order_status_id'), $comment);
	}
}

