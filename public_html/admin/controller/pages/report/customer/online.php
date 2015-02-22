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
class ControllerPagesReportCustomerOnline extends AController{
	public $data = array();
    public function main()   {

        //init controller data
        $this->extensions->hk_InitData($this, __FUNCTION__);

        $grid_settings = array(
            //id of grid
            'table_id' => 'customer_online_grid',
            // url to load data from
            'url' => $this->html->getSecureURL('listing_grid/report_customer/online'),
            // default sort column
            'sortname' => 'time',
            'columns_search' => true,
            'multiselect' => 'false',
        );

        $grid_settings['search_form'] = true;

        $grid_settings['colNames'] = array(
            $this->language->get('column_customer'),
            $this->language->get('column_ip'),
            $this->language->get('column_time'),
            $this->language->get('column_url'),
        );

        $grid_settings['colModel'] = array(
            array(
                'name' => 'customer',
                'index' => 'customer',
                'width' => 100,
                'align' => 'center',
                'search' => true,
            ),
            array(
                'name' => 'ip',
                'index' => 'ip',
                'width' => 60,
                'align' => 'center',
                'sorttype' => 'string',
                'search' => true,
            ),
            array(
                'name' => 'time',
                'index' => 'time',
                'width' => 80,
                'align' => 'center',
                'sorttype' => 'string',
                'search' => false,
            ),
            array(
                'name' => 'url',
                'index' => 'url',
                'width' => 200,
                'align' => 'left',
                'sorttype' => 'string',
                'search' => true,
            ),
        );

        $grid = $this->dispatch('common/listing_grid', array($grid_settings));
        $this->view->assign('listing_grid', $grid->dispatchGetOutput());

		$this->view->assign('reset', $this->html->getSecureURL('report/customer/online'));

        $this->document->setTitle($this->language->get('heading_title'));
        $this->document->initBreadcrumb(array(
            'href' => $this->html->getSecureURL('index/home'),
            'text' => $this->language->get('text_home'),
            'separator' => FALSE
        ));
        $this->document->addBreadcrumb(array(
            'href' => $this->html->getSecureURL('report/customer/online'),
            'text' => $this->language->get('heading_title'),
            'separator' => ' :: ',
			'current'	=> true
        ));

        $this->processTemplate('pages/report/customer/online.tpl');

        //update controller data
        $this->extensions->hk_UpdateData($this, __FUNCTION__);
    }
}

