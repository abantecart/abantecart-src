<?php
/*------------------------------------------------------------------------------
  $Id$

  AbanteCart, Ideal OpenSource Ecommerce Solution
  http://www.AbanteCart.com

  Copyright © 2011-2014 Belavier Commerce LLC

  This source file is subject to Open Software License (OSL 3.0)
  License details is bundled with this package in the file LICENSE.txt.
  It is also available at this URL:
  <http://www.opensource.org/licenses/OSL-3.0>

 UPGRADE NOTE:
   Do not edit or add to this file if you wish to upgrade AbanteCart to newer
   versions in the future. If you wish to customize AbanteCart for your
   needs please refer to http://www.AbanteCart.com for more information.
------------------------------------------------------------------------------*/
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesExtensionExtensionsStorePrev extends AController {
	public $data;
	public function main(){
		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->initBreadcrumb(array(
		                                     'href' => $this->html->getSecureURL('index/home'),
		                                     'text' => $this->language->get('text_home'),
		                                     'separator' => FALSE
		                                ));
		$this->document->addBreadcrumb(array(
		                                    'href' => $this->html->getSecureURL('extension/extensions_store'),
		                                    'text' => $this->language->get('heading_title'),
		                                    'separator' => ' :: '
		                               ));

		if (isset($this->session->data[ 'success' ])) {
			$this->data[ 'success' ] = $this->session->data[ 'success' ];
			unset($this->session->data[ 'success' ]);
		} else {
			$this->data[ 'success' ] = '';
		}

		if (isset($this->session->data[ 'error' ])) {
			$this->data[ 'error_warning' ] .= $this->session->data[ 'error' ];
			unset($this->session->data[ 'error' ]);
		}else{
			$this->data[ 'error_warning' ] = '';
		}

		$this->view->assign( 'src', $this->html->getSecureURL('tool/extensions_store_prev'));
		$this->processTemplate('pages/extension/extensions_store_prev.tpl');
	}
}