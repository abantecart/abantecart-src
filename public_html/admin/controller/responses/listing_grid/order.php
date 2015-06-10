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
class ControllerResponsesListingGridOrder extends AController {
	private $error = array();

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('sale/order');
		$this->loadModel('sale/order');

		$page = $this->request->post[ 'page' ]; // get the requested page
		$limit = $this->request->post[ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post[ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post[ 'sord' ]; // get the direction


		// process jGrid search parameter
		$allowedFields = array( 'name', 'order_id', 'date_added', 'total' );
		$allowedSortFields = array('customer_id', 'order_id', 'name', 'status', 'date_added', 'total', );

		$allowedDirection = array( 'asc', 'desc' );

		if (!in_array($sidx, $allowedSortFields)) $sidx = $allowedSortFields[ 0 ];
		if (!in_array($sord, $allowedDirection)) $sord = $allowedDirection[ 0 ];

		if (in_array($sidx,array('customer_id','order_id','date_added','total'))) {
			$sidx = 'o.' . $sidx;
		}

		$data = array(
			'sort' => $sidx,
			'order' => $sord,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		);
		if (isset($this->request->get[ 'status' ]) && $this->request->get[ 'status' ] != ''){
			$data[ 'filter_order_status_id' ] = $this->request->get[ 'status' ];
		}
		if (has_value($this->request->get[ 'customer_id' ])){
			$data[ 'filter_customer_id' ] = $this->request->get[ 'customer_id' ];
		}
		if (has_value($this->request->get[ 'product_id' ])){
			$data[ 'filter_product_id' ] = $this->request->get[ 'product_id' ];
		}

		if (isset($this->request->post[ '_search' ]) && $this->request->post[ '_search' ] == 'true') {
			$searchData = json_decode(htmlspecialchars_decode($this->request->post[ 'filters' ]), true);

			foreach ($searchData[ 'rules' ] as $rule) {
				if (!in_array($rule[ 'field' ], $allowedFields)) continue;
				$data[ 'filter_' . $rule[ 'field' ] ] = $rule[ 'data' ];
				if ($rule[ 'field' ] == 'date_added') {
					$data[ 'filter_' . $rule[ 'field' ] ] = dateDisplay2ISO($rule[ 'data' ]);
				}
			}
		}

		$this->loadModel('localisation/order_status');
		$results = $this->model_localisation_order_status->getOrderStatuses();
		$statuses = array( '' => $this->language->get('text_select_status'), );
		foreach ($results as $item) {
			$statuses[ $item[ 'order_status_id' ] ] = $item[ 'name' ];
		}


		$total = $this->model_sale_order->getTotalOrders($data);
		if ($total > 0) {
			$total_pages = ceil($total / $limit);
		} else {
			$total_pages = 0;
		}


		if($page > $total_pages){
			$page = $total_pages;
			$data['start'] = ($page - 1) * $limit;
		}

		$response = new stdClass();
		$response->page = $page;
		$response->total = $total_pages;
		$response->records = $total;

		$results = $this->model_sale_order->getOrders($data);

		$i = 0;
		foreach ($results as $result) {

			$response->rows[ $i ][ 'id' ] = $result[ 'order_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$result[ 'order_id' ],
				$result[ 'name' ],
				$this->html->buildSelectbox(array(
					'name' => 'order_status_id[' . $result[ 'order_id' ] . ']',
					'value' => array_search($result[ 'status' ], $statuses),
					'options' => $statuses,
				)),
				dateISO2Display($result[ 'date_added' ], $this->language->get('date_format_short')),
				$this->currency->format($result[ 'total' ], $result[ 'currency' ], $result[ 'value' ]),
			);
			$i++;
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

	public function update() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadModel('sale/order');
		$this->loadLanguage('sale/order');
		if (!$this->user->canModify('listing_grid/order')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/order'),
					'reset_value' => true
				));
		}

		switch ($this->request->post[ 'oper' ]) {
			case 'del':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						$this->model_sale_order->deleteOrder($id);
					}
				break;
			case 'save':
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						$this->model_sale_order->editOrder($id, array( 'order_status_id' => $this->request->post[ 'order_status_id' ][ $id ] ));
					}
				break;

			default:
				//print_r($this->request->post);

		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
		return null;
	}

	/**
	 * update only one field
	 *
	 * @return void
	 */
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('sale/order');
		$this->loadModel('sale/order');

		if (!$this->user->canModify('listing_grid/order')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/order'),
					'reset_value' => true
				));
		}

		if(has_value($this->request->post['downloads'])){
			$data = $this->request->post['downloads'];
			$this->loadModel('catalog/download');
			foreach($data as $order_download_id=>$item){
				if (isset($item['expire_date'])) {
					$item['expire_date'] = $item['expire_date'] ? dateDisplay2ISO($item['expire_date'], $this->language->get('date_format_short')) : '';
				}
				$this->model_catalog_download->editOrderDownload($order_download_id, $item);
			}
			return null;
		}

		if (isset($this->request->get[ 'id' ])) {
			$this->model_sale_order->editOrder($this->request->get[ 'id' ], $this->request->post);
			return null;
		}



		//request sent from jGrid. ID is key of array
		foreach ($this->request->post as $field => $value) {
			foreach ($value as $k => $v) {
				$this->model_sale_order->editOrder($k, array( $field => $v ));
			}
		}


		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	public function summary() {

		//update controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('sale/order');
		$this->loadModel('sale/order');

		$response = new stdClass();

		if (isset($this->request->get[ 'order_id' ])) {
			$order_id = $this->request->get[ 'order_id' ];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_sale_order->getOrder($order_id);

		if (empty($order_info)) {
			$response->error = $this->language->get('error_order_load');
		} else {
			$response->order = array(
				'order_id' => '#' . $order_info[ 'order_id' ],
				'name' => $order_info[ 'firstname' ] . '' . $order_info[ 'lastname' ],
				'email' => $order_info[ 'email' ],
				'telephone' => $order_info[ 'telephone' ],
				'date_added' => dateISO2Display($order_info[ 'date_added' ], $this->language->get('date_format_short')),
				'total' => $this->currency->format($order_info[ 'total' ], $order_info[ 'currency' ], $order_info[ 'value' ]),
				'order_status' => $order_info[ 'order_status_id' ],
				'shipping_method' => $order_info[ 'shipping_method' ],
				'payment_method' => $order_info[ 'payment_method' ],
			);

			if ($order_info[ 'customer_id' ]) {
				$response->order[ 'name' ] = '<a href="' . $this->html->getSecureURL('sale/customer/update', '&customer_id=' . $order_info[ 'customer_id' ]) . '">' . $response->order[ 'name' ] . '</a>';
			}

			$this->loadModel('localisation/order_status');
			$status = $this->model_localisation_order_status->getOrderStatus($order_info[ 'order_status_id' ]);
			if ($status)
				$response->order[ 'order_status' ] = $status[ 'name' ];

		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);

		$this->load->library('json');
		$this->response->setOutput(AJson::encode($response));
	}

}