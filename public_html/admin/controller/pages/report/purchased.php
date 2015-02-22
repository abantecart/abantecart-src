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
class ControllerPagesReportPurchased extends AController {
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$grid_settings = array(
			//id of grid
            'table_id' => 'report_viewed_grid',
            // url to load data from
			'url' => $this->html->getSecureURL('listing_grid/report_purchased'),
            // default sort column
			'sortname' => 'quantity',
			'columns_search' => false,
			'multiselect' => 'false',
		);

		$grid_settings['colNames'] = array(
			$this->language->get('column_name'),
			$this->language->get('column_model'),
			$this->language->get('column_quantity'),
			$this->language->get('column_total'),
		);

		$grid_settings['colModel'] = array(
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
				'name' => 'quantity',
				'index' => 'quantity',
				'width' => 50,
                'align' => 'center',
				'sortable' => false,
			),
            array(
				'name' => 'total',
				'index' => 'total',
				'width' => 90,
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
       		'href'      => $this->html->getSecureURL('report/purchased'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: ',
			'current'	=> true
   		 ));		
		
		$this->view->assign('reset', $this->html->getSecureURL('report/purchased'));
		$this->view->assign('reset_button', $this->html->buildButton(array(
		    'name' => 'reset_button',
		    'text' => $this->language->get('button_reset'),
		    'style' => 'button1',
	    )));

		$this->document->setTitle( $this->language->get('heading_title') );

		$this->processTemplate('pages/report/purchased.tpl' );

        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}	
}
