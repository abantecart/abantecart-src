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
class ControllerPagesReportCustomerTransactions extends AController{
	public $data = array();
    public function main()   {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('report/customer/transactions'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: ',
			'current'	=> true
        ));

        $grid_settings = array(
            //id of grid
            'table_id' => 'customer_transactions_grid',
            // url to load data from
            'url' => $this->html->getSecureURL('listing_grid/report_customer/transactions'),
            // default sort column
            'sortname' => 'date_added',
            'columns_search' => true,
            'multiselect' => 'false',
			'actions' => array(
			    'view' => array(
			    	'text' => $this->language->get('text_view'),
			    	'href' => $this->html->getSecureURL ( 'sale/customer_transaction','&customer_id=%ID%')
			    )
			),
        );

        $grid_settings['search_form'] = true;

        $grid_settings['colNames'] = array(
            $this->language->get('column_transaction_date'),
            $this->language->get('column_customer'),
            $this->language->get('column_debit'),
            $this->language->get('column_credit'),
            $this->language->get('column_transaction_type'),
            $this->language->get('column_created_by'),
        );

        $grid_settings['colModel'] = array(
            array(
                'name' => 'date_added',
                'index' => 'date_added',
                'width' => 110,
                'align' => 'center',
                'search' => false,
            ),
            array(
                'name' => 'customer',
                'index' => 'customer',
                'width' => 120,
                'align' => 'center',
                'sorttype' => 'string',
                'search' => true,
            ),
            array(
                'name' => 'debit',
                'index' => 'debit',
                'width' => 100,
                'align' => 'center',
                'search' => false,
            ),
            array(
                'name' => 'credit',
                'index' => 'credit',
                'width' => 100,
                'align' => 'center',
                'search' => false,
            ),
            array(
                'name' => 'transaction_type',
                'index' => 'transaction_type',
                'width' => 120,
                'align' => 'center',
                'search' => false,
            ),
            array(
                'name' => 'created_by',
                'index' => 'created_by',
                'width' => 120,
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
            'form_name' => 'customer_transactions_grid_search',
        ));
        $this->data['grid_search_form'] = array();
        $this->data['grid_search_form']['id'] = 'customer_transactions_grid_search';
        $this->data['grid_search_form']['form_open'] = $form->getFieldHtml(array(
            'type' => 'form',
            'name' => 'customer_transactions_grid_search',
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

        $this->view->assign('search_form', $this->data['grid_search_form']);

		$this->view->assign('reset', $this->html->getSecureURL('report/customer/transactions'));


        $this->processTemplate('pages/report/customer/transactions.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}