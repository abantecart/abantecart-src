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
class ControllerResponsesListingGridReportCustomer extends AController {
	private $error = array();

    public function online() {

	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('report/customer');

		//Prepare filter config
		$grid_filter_params = array( 'customer' => 'c.lastname', 'ip' => 'co.ip', 'url' => 'co.url', 'time' => 'co.date_added' );
		$filter_grid = new AFilter(array( 'method' => 'post', 'grid_filter_params' => $grid_filter_params ));

		$total = $this->model_report_customer->getTotalOnlineCustomers($filter_grid->getFilterData());
		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages($total);
		$response->records = $total;
		$response->userdata = new stdClass();

	    $results = $this->model_report_customer->getOnlineCustomers($filter_grid->getFilterData());
	    $i = 0;
		foreach ($results as $result) {
            $response->rows[$i]['id'] = $result['customer_id'];
            //mark inactive customers. 
            if ($result['status'] != 1) {
            	$response->userdata->classes[$result['customer_id']] = 'attention';
            }
			$response->rows[$i]['cell'] = array(
				$result['customer'],
				$result['ip'],
				dateISO2Display($result['date_added'], $this->language->get('date_format_short').' '.$this->language->get('time_format')),
				$result['url'],
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

    public function orders() {
	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('report/customer');

		//Prepare filter config
		$filter_params =  array('date_start', 'date_end', 'order_status');
		$filter_form = new AFilter(array( 'method' => 'get', 'filter_params' => $filter_params ));
		$filter_grid = new AFilter(array( 'method' => 'post' ));
		$data = array_merge($filter_form->getFilterData(), $filter_grid->getFilterData());

		//add filters for custom processing
		$allowedFields = array( 'customer_id', 'customer' );
		if ( isset($this->request->post[ '_search' ]) && $this->request->post[ '_search' ] == 'true') {
			$searchData = AJson::decode(htmlspecialchars_decode($this->request->post[ 'filters' ]), true);
			foreach ($searchData['rules'] as $rule) {
				if (!in_array($rule['field'], $allowedFields)) continue;
				$data['filter'][$rule['field']] = $rule['data'];
			}
		}

		$total = $this->model_report_customer->getTotalCustomerOrders($data);
		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages($total);
		$response->records = $total;

	    $results = $this->model_report_customer->getCustomerOrders($data);
	    $i = 0;
		foreach ($results as $result) {
			if ($result['customer_id'] > 0) {
	            $response->rows[$i]['id'] = $result['customer_id'];
			} else {
				//this is guest order
				$response->rows[$i]['id'] = 'null';
				$result['customer_id'] = 'Guest';
			}
            //mark inactive or missing customers. 
            if ($result['status'] != 1) {
				$response->userdata->classes[$result['customer_id']] = 'attention';
			}
			$response->rows[$i]['cell'] = array(
				$result['customer_id'],
				$result['customer'],
				$result['customer_group'],
				$result['order_count'],
				$this->currency->format($result['total'], $this->config->get('config_currency')),
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

    public function transactions() {
	    //init controller data
        $this->extensions->hk_InitData($this,__FUNCTION__);

		$this->loadModel('report/customer');

		//Prepare filter config
		$filter_params =  array('date_start', 'date_end');
		$filter_form = new AFilter(array( 'method' => 'get', 'filter_params' => $filter_params ));
		$filter_grid = new AFilter(array( 'method' => 'post' ));
		$data = array_merge($filter_form->getFilterData(), $filter_grid->getFilterData());

		//add filters for custom processing
		$allowedFields = array( 'customer' );
		if ( isset($this->request->post[ '_search' ]) && $this->request->post[ '_search' ] == 'true') {
			$searchData = AJson::decode(htmlspecialchars_decode($this->request->post[ 'filters' ]), true);
			foreach ($searchData['rules'] as $rule) {
				if (!in_array($rule['field'], $allowedFields)) continue;
				$data['filter'][$rule['field']] = $rule['data'];
			}
		}

		$total = $this->model_report_customer->getTotalCustomerTransactions($data);
		$response = new stdClass();
		$response->page = $filter_grid->getParam('page');
		$response->total = $filter_grid->calcTotalPages($total);
		$response->records = $total;

	    $results = $this->model_report_customer->getCustomerTransactions($data);
	    $i = 0;
		foreach ($results as $result) {
            $response->rows[$i]['id'] = $result['customer_id'];
            //mark inactive customers. 
            if ($result['status'] != 1) {
				$response->userdata->classes[$result['customer_id']] = 'attention';
			}
			$response->rows[$i]['cell'] = array(
				$result['date_added'],
				$result['customer'],
				$this->currency->format($result['debit'], $this->config->get('config_currency')),
				$this->currency->format($result['credit'], $this->config->get('config_currency')),
				$result['transaction_type'],
				$result['created_by'],
			);
			$i++;
		}

		//update controller data
        $this->extensions->hk_UpdateData($this,__FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

}