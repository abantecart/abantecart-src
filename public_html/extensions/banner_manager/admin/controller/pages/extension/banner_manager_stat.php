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
class ControllerPagesExtensionBannerManagerStat extends AController {
	public $data = array();
	public function main() {

        //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadLanguage('banner_manager/banner_manager');

		$this->document->setTitle( $this->language->get('banner_manager_name_stat') );
		$grid_settings = array(
			//id of grid
            'table_id' => 'banner_stat_grid',
            // url to load data from
			'url' => $this->html->getSecureURL('listing_grid/banner_manager_stat'),
            // default sort column
			'sortname' => 'date_end',
			'columns_search' => false,
			'multiselect' => 'false',
			'actions' => array(
							'view' => array(
								'text' => $this->language->get('text_view'),
								'href' => $this->html->getSecureURL('extension/banner_manager_stat/details','&banner_id=%ID%')
							)
			)
		);

		$grid_settings['colNames'] = array(
			$this->language->get('column_banner_name'),
			$this->language->get('column_banner_group'),
			$this->language->get('column_clicked'),
			$this->language->get('column_viewed'),
			$this->language->get('column_percent')
		);

		$grid_settings['colModel'] = array(
			array(
				'name' => 'name',
				'index' => 'name',
				'width' => 250,
				'align' => 'left',
				'sortable' => false
			),
			array(
				'name' => 'group_name',
				'index' => 'banner_group_name',
				'width' => 160,
                'align' => 'left',
				'sortable' => false
			),
			array(
				'name' => 'clicked',
				'index' => 'clicked',
				'width' => 40,
                'align' => 'center',
				'sortable' => false
			),
			array(
				'name' => 'viewed',
				'index' => 'viewed',
				'width' => 120,
                'align' => 'center',
				'sortable' => false
			),
            array(
				'name' => 'percent',
				'index' => 'percent',
				'width' => 60,
                'align' => 'center',
	            'sortable' => false
			)
		);


		$grid = $this->dispatch('common/listing_grid', array( $grid_settings ) );
		$this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->document->initBreadcrumb( array (
       		'href'      => $this->html->getSecureURL('index/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		 ));
   		$this->document->addBreadcrumb( array ( 
       		'href'      => $this->html->getSecureURL('extension/banner_manager_stat'),
       		'text'      => $this->language->get('banner_manager_name_stat'),
      		'separator' => ' :: ',
			'current'	=> true
   		 ));

		$this->view->assign('success', $this->session->data['success']);
        if (isset($this->session->data['success'])) {
			unset($this->session->data['success']);
		}

		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/extension/banner_manager_stat.tpl' );
        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}

	public function details(){
		//init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);
		$this->loadLanguage('common/header');
		$this->loadLanguage('common/home');
		$this->loadLanguage('banner_manager/banner_manager');
		$this->document->setTitle( $this->language->get('banner_manager_name_stat') );


		$this->document->initBreadcrumb(
				array(
						'href' => $this->html->getSecureURL('index/home'),
						'text' => $this->language->get('text_home'),
						'separator' => FALSE));
		$this->document->addBreadcrumb(
				array(
						'href' => $this->html->getSecureURL('extension/banner_manager'),
						'text' => $this->language->get('banner_manager_name'),
						'separator' => ' :: '));


		$this->loadModel('extension/banner_manager');
		$info = $this->model_extension_banner_manager->getBanner((int)$this->request->get['banner_id']);

		$this->data['heading_title'] = $this->language->get('banner_manager_name_stat') .':  '.$info['name'];

		$this->document->addBreadcrumb(
						array(
								'href' => $this->html->getSecureURL('extension/banner_manager_stat','&banner_id='.$this->request->get['banner_id']),
								'text' => $this->data['heading_title'],
								'separator' => ' :: ',
								'current' => true
						));
		$this->data['chart_url'] =  $this->html->getSecureURL('extension/banner_manager_chart', '&banner_id='.$this->request->get['banner_id']) ;
		$options = array(
						'day' => $this->language->get('text_day'),
						'week' => $this->language->get('text_week'),
						'month' => $this->language->get('text_month'),
						'year' => $this->language->get('text_year')
		);

		$this->data['select_range'] = HtmlElementFactory::create(array( 'type' => 'selectbox',
		                                                                'name' => 'range',
		                                                                'options' => $options,
		                                                                'value' => 'day'));

		$this->data['text_count'] = $this->language->get('text_count');
		$this->view->batchAssign($this->data);
		$this->processTemplate('pages/extension/banner_manager_stat_details.tpl' );
        //update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);
	}
}
