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
class ControllerPagesIndexMaintenance extends AController {
	public $data;
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

        $this->document->setTitle( $this->language->get('heading_title') );
        
        
        $this->data['language'] = $this->language->get('code');
        $this->data['direction'] = $this->language->get('direction');
        
        $this->data['title'] = $this->language->get('heading_title');
                
        $this->document->resetBreadcrumbs();

        $this->document->addBreadcrumb( array ( 
            'href'      => ($this->html->getURL('index/maintenance')),
            'text'      => $this->language->get('text_maintenance'),
            'separator' => FALSE
         )); 
        
        $this->data['breadcrumbs'] = $this->document->getBreadcrumbs();
        $this->data['message'] = $this->language->get('text_message');
        $this->view->batchAssign($this->data);

        $this->processTemplate('pages/index/maintenance.tpl');

        //init controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

    }
}
?>