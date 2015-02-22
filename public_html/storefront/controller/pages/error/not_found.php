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
class ControllerPagesErrorNotFound extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 404 Not Found');
		
		$this->document->setTitle( $this->language->get('heading_title') );
		
		$this->document->resetBreadcrumbs();
 
      	$this->document->addBreadcrumb( array ( 
        	'href'      => $this->html->getURL('index/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	 ));		
		
		if (isset($this->request->get['rt'])) {
       		$this->document->addBreadcrumb( array ( 
        		'href'      => $this->html->getURL( $this->request->get['rt'] ),
        		'text'      => $this->language->get('text_error'),
        		'separator' => $this->language->get('text_separator')
      		 ));	   	
		}
		
		$this->view->assign('heading_title', $this->language->get('heading_title'));
		$this->view->assign('text_error', $this->language->get('text_error'));
		$continue = HtmlElementFactory::create( array ('type' => 'button',
		                                               'name' => 'continue_button',
			                                           'text'=> $this->language->get('button_continue'),
			                                           'style' => 'button'));
		$this->view->assign('button_continue', $continue);
		$this->view->assign('continue', $this->html->getURL('index/home'));
		$this->processTemplate('pages/error/not_found.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
  	}
}
?>