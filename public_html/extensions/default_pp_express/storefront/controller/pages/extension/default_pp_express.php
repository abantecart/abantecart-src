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

class ControllerPagesExtensionDefaultPPExpress extends AController {

	public $data = array();

	public function error() {
		unset($this->session->data['pp_express_checkout']); // remove session data with paypal token
		$this->loadLanguage('default_pp_express/default_pp_express');

		$this->data['heading_title'] = $this->language->get('text_paypal_error');
		$this->data['continue'] = $this->html->getSecureURL('index/home');

		$this->data['message'] = has_value($this->session->data['pp_express_checkout_error']) ? $this->session->data['pp_express_checkout_error'] : $this->language->get('service_error');
		$this->data['button_continue'] = $this->html->buildElement(
			array(
				'type' => 'button',
				'name' => 'button_continue',
				'text' => $this->language->get('button_continue'),
				'href' => $this->data['continue']
			)
		);

		$this->view->batchAssign($this->data);
		$this->processTemplate('responses/default_pp_express_error.tpl');
	}
}