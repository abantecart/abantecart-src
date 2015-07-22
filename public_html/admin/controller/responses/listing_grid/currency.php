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
class ControllerResponsesListingGridCurrency extends AController {

	public function main() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('localisation/currency');
		$this->loadModel('localisation/currency');

		$page = $this->request->post[ 'page' ]; // get the requested page
		$limit = $this->request->post[ 'rows' ]; // get how many rows we want to have into the grid
		$sidx = $this->request->post[ 'sidx' ]; // get index row - i.e. user click to sort
		$sord = $this->request->post[ 'sord' ]; // get the direction

		$data = array(
			'sort' => $sidx,
			'order' => strtoupper($sord),
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		);


		$total = $this->model_localisation_currency->getTotalCurrencies();
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

		$results = $this->model_localisation_currency->getCurrencies($data);
		$i = 0;
		foreach ($results as $result) {

			$response->rows[ $i ][ 'id' ] = $result[ 'currency_id' ];
			$response->rows[ $i ][ 'cell' ] = array(
				$this->html->buildInput(array(
					'name' => 'title[' . $result[ 'currency_id' ] . ']',
					'value' => $result[ 'title' ],
				)),
				$this->html->buildInput(array(
					'name' => 'code[' . $result[ 'currency_id' ] . ']',
					'value' => $result[ 'code' ],
				)),
				$this->html->buildInput(array(
					'name' => 'value[' . $result[ 'currency_id' ] . ']',
					'value' => $result[ 'value' ],
				)),
				dateISO2Display($result[ 'date_modified' ], $this->language->get('date_format_short')),
				$this->html->buildCheckbox(array(
					'name' => 'status[' . $result[ 'currency_id' ] . ']',
					'value' => $result[ 'status' ],
					'style' => 'btn_switch',
				)),
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

		$this->loadModel('localisation/currency');
		$this->loadLanguage('localisation/currency');
		if (!$this->user->canModify('listing_grid/currency')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/currency'),
					'reset_value' => true
				));
		}

		switch ($this->request->post[ 'oper' ]) {
			case 'del':

				$this->loadModel('setting/store');
				$this->loadModel('sale/order');

				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						$err = '';
						$currency_info = $this->model_localisation_currency->getCurrency($id);
						if ($currency_info) {
							if ($this->config->get('config_currency') == $currency_info[ 'code' ]) {
								$err = $this->language->get('error_default');
							}

							$store_total = $this->model_setting_store->getTotalStoresByCurrency($currency_info[ 'code' ]);
							if ($store_total) {
								$err = sprintf($this->language->get('error_store'), $store_total);
							}
						}
						$order_total = $this->model_sale_order->getTotalOrdersByCurrencyId($id);
						if ($order_total) {
							$err = sprintf($this->language->get('error_order'), $order_total);
						}

						if (!empty($err)) {
							$error = new AError('');
							return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
						}

						$this->model_localisation_currency->deleteCurrency($id);
					}
				break;
			case 'save':
				$fields = array( 'title', 'code', 'value', 'status' );
				$ids = explode(',', $this->request->post[ 'id' ]);
				if (!empty($ids))
					foreach ($ids as $id) {
						foreach ($fields as $f) {

							if ($f == 'status' && !isset($this->request->post[ 'status' ][ $id ]))
								$this->request->post[ 'status' ][ $id ] = 0;

							if (isset($this->request->post[ $f ][ $id ])) {
								$err = $this->_validateField($f, $this->request->post[ $f ][ $id ]);
								if (!empty($err)) {
									$error = new AError('');
									return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
								}
								$this->model_localisation_currency->editCurrency($id, array( $f => $this->request->post[ $f ][ $id ] ));
							}
						}
					}

				break;

			default:
				//print_r($this->request->post);

		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	/**
	 * update only one field
	 *
	 * @return void
	 */
	public function update_field() {

		//init controller data
		$this->extensions->hk_InitData($this, __FUNCTION__);

		$this->loadLanguage('localisation/currency');
		if (!$this->user->canModify('listing_grid/currency')) {
			$error = new AError('');
			return $error->toJSONResponse('NO_PERMISSIONS_402',
				array( 'error_text' => sprintf($this->language->get('error_permission_modify'), 'listing_grid/currency'),
					'reset_value' => true
				));
		}

		$this->loadModel('localisation/currency');
		if (isset($this->request->get[ 'id' ])) {
			//request sent from edit form. ID in url
			foreach ($this->request->post as $key => $value) {
				$err = $this->_validateField($key, $value);
				if (!empty($err)) {
					$error = new AError('');
					return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
				}
				$data = array( $key => $value );
				$this->model_localisation_currency->editCurrency($this->request->get[ 'id' ], $data);
			}
			return null;
		}

		//request sent from jGrid. ID is key of array
		$fields = array( 'title', 'code', 'value', 'status' );
		foreach ($fields as $f) {
			if (isset($this->request->post[ $f ]))
				foreach ($this->request->post[ $f ] as $k => $v) {
					$err = $this->_validateField($f, $v);
					if (!empty($err)) {
						$error = new AError('');
						return $error->toJSONResponse('VALIDATION_ERROR_406', array( 'error_text' => $err ));
					}
					$result = $this->model_localisation_currency->editCurrency($k, array( $f => $v ));
					if (!$result) {
						if ($f == 'status') {
							$this->messages->saveNotice('Currency warning', 'Warning: You tried to disable the only enabled currency of cart!');
						}
						$this->response->setOutput('error!');
						return null;
					}
				}
		}

		//update controller data
		$this->extensions->hk_UpdateData($this, __FUNCTION__);
	}

	private function _validateField($field, $value) {
		$err = '';
		switch ($field) {
			case 'title':
				if ( mb_strlen($value) < 2  || mb_strlen($value) > 32 ) {
					$err = $this->language->get('error_title');
				}
				break;
			case 'code':
				if ( mb_strlen($value) != 3 ) {
					$err = $this->language->get('error_code');
				}
				break;
			default:
		}
		return $err;
	}

}
