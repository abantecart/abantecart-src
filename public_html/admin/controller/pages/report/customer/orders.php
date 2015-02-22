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
if (!defined('DIR_CORE') || !IS_ADMIN) {
    header('Location: static_pages/');
}
class ControllerPagesReportCustomerOrders extends AController{
	public $data = array();
    public function main()   {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $grid_settings = array(
            //id of grid
            'table_id' => 'customer_order_grid',
            // url to load data from
            'url' => $this->html->getSecureURL('listing_grid/report_customer/orders'),
            // default sort column
            'sortname' => 'customer',
            'columns_search' => true,
            'multiselect' => 'false',
			'actions' => array(
			    'view' => array(
			    	'text' => $this->language->get('text_view'),
			    	'href' => $this->html->getSecureURL ( 'sale/customer/update','&customer_id=%ID%')
			    )
			),
        );

        $grid_settings['search_form'] = true;

        $grid_settings['colNames'] = array(
            $this->language->get('column_customer_id'),
            $this->language->get('column_customer'),
            $this->language->get('column_group'),
            $this->language->get('column_order_count'),
            $this->language->get('column_total'),
        );

        $grid_settings['colModel'] = array(
            array(
                'name' => 'customer_id',
                'index' => 'customer_id',
                'width' => 80,
                'align' => 'center',
                'search' => true,
            ),
            array(
                'name' => 'customer',
                'index' => 'customer',
                'width' => 120,
                'align' => 'center',
                'search' => true,
            ),
            array(
                'name' => 'group',
                'index' => 'group',
                'width' => 100,
                'align' => 'center',
                'sorttype' => 'string',
                'search' => false,
            ),
            array(
                'name' => 'order_count',
                'index' => 'order_count',
                'width' => 80,
                'align' => 'center',
                'search' => false,
            ),
            array(
                'name' => 'total',
                'index' => 'total',
                'width' => 100,
                'align' => 'center',
                'search' => false,
            ),
        );

        $grid = $this->dispatch('common/listing_grid', array($grid_settings));
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());

		//prepare the filter form
		//Note: External search form needs to be named [grid_name]_search
		//		In this case it will be auto submited to filter grid
        $form = new AForm();
        $form->setForm(array(
            'form_name' => 'customer_order_grid_search',
        ));
        $this->data['grid_search_form'] = array();
        $this->data['grid_search_form']['id'] = 'customer_order_grid_search';
        $this->data['grid_search_form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'customer_order_grid_search',
            'action' => '',
        ));
        $this->data['grid_search_form']['submit'] = $form->getFieldHtml(array(
            'type' => 'button',
            'name' => 'submit',
            'text' => $this->language->get('button_go'),
            'style' => 'button1',
        ));
		$this->view->assign('js_date_format', format4Datepicker($this->language->get('date_format_short')));
        $this->data['grid_search_form']['fields']['date_start'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'date_start',
            'default' => dateInt2Display(strtotime('-7 day')),
        ));
        $this->data['grid_search_form']['fields']['date_end'] = $form->getFieldHtml(array(
            'type' => 'input',
            'name' => 'date_end',
            'default' => dateInt2Display(time()),
        ));

		$this->loadModel('localisation/order_status');
		$results = $this->model_localisation_order_status->getOrderStatuses();
		$statuses = array(
			'' => $this->language->get('text_all_orders')
		);
		foreach ($results as $item) {
			$statuses[$item['order_status_id']] = $item['name'];
		}

        $this->data['grid_search_form']['fields']['status'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'order_status',
            'options' => $statuses,
            'placeholder' => $this->language->get('text_select_status')
        ));
        $this->view->assign('search_form', $this->data['grid_search_form']);

		$this->view->assign('reset', $this->html->getSecureURL('report/customer/orders'));

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('report/customer/orders'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: ',
			'current'	=> true
        ));

        $this->processTemplate('pages/report/customer/orders.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}

