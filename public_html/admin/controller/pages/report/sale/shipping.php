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
class ControllerPagesReportSaleShipping extends AController{
	public $data = array();
    public function main()   {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);
		
        $grid_settings = array(
            //id of grid
            'table_id' => 'report_shipping_grid',
            // url to load data from
            'url' => $this->html->getSecureURL('listing_grid/report_sale/shipping'),
            // default sort column
            'sortname' => 'date_end',
            'columns_search' => false,
            'multiselect' => 'false',
        );

        $form = new AForm();
        $form->setForm(array(
            'form_name' => 'report_shipping_grid_search',
        ));

        $this->data['grid_search_form'] = array();
        $this->data['grid_search_form']['id'] = 'report_shipping_grid_search';
        $this->data['grid_search_form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'report_shipping_grid_search',
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

        $groups = array();
        $groups['year'] = $this->language->get('text_year');
        $groups['month'] = $this->language->get('text_month');
        $groups['week'] = $this->language->get('text_week');
        $groups['day'] = $this->language->get('text_day');

        $this->data['grid_search_form']['fields']['group'] = $form->getFieldHtml(array(
            'type' => 'selectbox',
            'name' => 'group',
            'options' => $groups,
            'value' => "week",
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

        $grid_settings['search_form'] = true;

        $grid_settings['colNames'] = array(
            $this->language->get('column_date_start'),
            $this->language->get('column_date_end'),
            $this->language->get('column_orders'),
            $this->language->get('column_total'),
        );

        $grid_settings['colModel'] = array(
            array(
                'name' => 'date_start',
                'index' => 'date_start',
                'width' => 100,
                'align' => 'center',
                'sorttype' => 'string',
            ),
            array(
                'name' => 'date_end',
                'index' => 'date_end',
                'width' => 100,
                'align' => 'center',
                'sorttype' => 'string',
            ),
            array(
                'name' => 'orders',
                'index' => 'orders',
                'width' => 100,
                'align' => 'center',
            ),
            array(
                'name' => 'total',
                'index' => 'total',
                'width' => 110,
                'align' => 'center',
            ),
        );

        $grid = $this->dispatch('common/listing_grid', array($grid_settings));
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());
        $this->view->assign('search_form', $this->data['grid_search_form']);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('report/sale/shipping'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: ',
			'current'	=> true
        ));

        $this->processTemplate('pages/report/sale/shipping.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}