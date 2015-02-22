<?php  
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2015 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' )) {
	header ( 'Location: static_pages/' );
}
class ControllerBlocksNewsLetterSignUp extends AController {
	public $data=array();
	public function main() {

		$this->loadLanguage('blocks/newsletter_signup');

		$form  =  new AForm();
		$this->data['form_open'] = $form->getFieldHtml( array( 'type' => 'form',
															   'name' => 'subscribeFrm',
															   'method' => 'get',
															   'action' => $this->html->getSecureURL('account/subscriber','',true)));
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_signup'] = $this->language->get('text_signup');
		$this->data['text_sign_in'] = $this->language->get('text_sign_in');
		$this->data['text_subscribe'] = $this->language->get('text_subscribe');

		$this->data['form_fields']['rt'] = 'account/subscriber';

		$this->view->batchAssign($this->data);
		$this->processTemplate();
	}
}
