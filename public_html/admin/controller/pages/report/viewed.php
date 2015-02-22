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
class ControllerPagesReportViewed extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->document->setTitle( $this->language->get('heading_title') );
		$grid_settings = array(
			//id of grid
            'table_id' => 'report_viewed_grid',
            // url to load data from
			'url' => $this->html->getSecureURL('listing_grid/report_viewed'),
            // default sort column
			'sortname' => 'date_end',
			'columns_search' => false,
			'multiselect' => 'false',
		);

		$grid_settings['colNames'] = array(
			$this->language->get('column_product_id'),			
			$this->language->get('column_name'),
			$this->language->get('column_model'),
			$this->language->get('column_viewed'),
			$this->language->get('column_percent'),
		);

		$grid_settings['colModel'] = array(
			array(
				'name' => 'product_id',
				'index' => 'product_id',
				'width' => 50,
                'align' => 'center',
				'sortable' => false,
			),
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 300,
				'align' => 'left',
				'sortable' => false,
			),
			array(
				'name' => 'model',
				'index' => 'model',
				'width' => 80,
                'align' => 'center',
				'sortable' => false,
			),
			array(
				'name' => 'viewed',
				'index' => 'viewed',
				'width' => 50,
                'align' => 'center',
				'sortable' => false,
			),
            array(
				'name' => 'percent',
				'index' => 'percent',
				'width' => 50,
                'align' => 'center',
	            'sortable' => false,
			),
		);

		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('report/viewed'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		 ));


		$this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}
		$this->view->assign('reset', $this->html->getSecureURL('report/viewed/reset'));

		$this->processTemplate('pages/report/viewed.tpl' );
        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
	
	public function reset() {
		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->model_report_viewed->reset();
		$this->session->data['success'] = $this->language->get('text_success');
		
		$url = '';
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->redirect($this->html->getSecureURL('report/viewed', $url));
	}
}
