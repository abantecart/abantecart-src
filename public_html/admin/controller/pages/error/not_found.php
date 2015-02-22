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
if (! defined ( 'DIR_CORE' ) || !IS_ADMIN) {
	header ( 'Location: static_pages/' );
}
class ControllerPagesErrorNotFound extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

    	$this->document->setTitle( $this->language->get('heading_title') );
  		$this->document->resetBreadcrumbs();
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('error/not_found'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		 ));
		
		$this->view->assign('heading_title', $this->language->get('heading_title'));
		$this->view->assign('text_not_found', $this->language->get('text_not_found'));
		$this->view->assign('button_continue', $this->language->get('button_continue'));
		$this->view->assign('continue', $this->html->getSecureURL('index/home'));

		$this->processTemplate('pages/error/not_found.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
?>